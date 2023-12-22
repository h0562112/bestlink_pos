<?php
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
$setup=parse_ini_file('../../../database/setup.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinename']])){
		$invmachine=$dbmapping['map'][$_POST['machinename']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}

if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(file_exists('../../syspram/paper-'.$init['init']['firlan'].'.ini')){
	$paper=parse_ini_file('../../syspram/paper-'.$init['init']['firlan'].'.ini',true);
}
else{
	$paper='-1';
}
if(isset($print['item']['textfont'])){
}
else{
	$print['item']['textfont']="微軟正黑體";
}
$maxhour=0;
if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
	$filedb='../../../database/sale/SALES_'.substr($_POST['papbizdateS'],0,6).'.db';
}
else{
	$filedb='../../../database/sale/SALES_'.substr($timeini['time']['bizdate'],0,6).'.db';
}
if(file_exists($filedb)){
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['papbizdateS'],0,6).'.db','','','','sqlite');
	}
	else{
		$conn=sqlconnect('../../../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
	}
	if(!$conn){
		echo '資料庫尚未上傳資料。';
		sqlclose($conn,'sqlite');
	}
	else{
		$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
		$res=sqlquery($conn,$sql,'sqlite');
		if(isset($res[0]['name'])){
			$sql="SELECT BIZDATE,SUM(SALESTTLAMT) AS AMT,SUBSTR(CREATEDATETIME,9,2) AS SALETIME FROM (SELECT BIZDATE,SALESTTLAMT,CASE WHEN UPDATEDATETIME='0' THEN CREATEDATETIME ELSE UPDATEDATETIME END AS CREATEDATETIME FROM CST011 WHERE BIZDATE";
			if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
				$sql=$sql." BETWEEN '".$_POST['papbizdateS']."' AND '".$_POST['papbizdateE']."'";
			}
			else{
				$sql=$sql."='".$timeini['time']['bizdate']."'";
			}
			if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
				if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
					$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
				}
				else if(isset($_POST['zcounter'])){
					//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
				}
				else{
					$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
				}
			}
			else{
				$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			}
			$sql=$sql." AND NBCHKNUMBER IS NULL) GROUP BY SUBSTR(CREATEDATETIME,9,2) ORDER BY SALETIME";
			$menuarray=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
			//echo $sql;
			//print_r($menuarray);
			if(sizeof($menuarray)==0){
				echo "查無資料。";
			}
			else if($menuarray[0]=="SQL語法錯誤"||$menuarray[0]=="連線失敗"){
				if($dubug==1){
					echo $list[0]."(select)".$sql;
				}
				else{
					echo $list[0]."(select)";
				}
			}
			else{
				$maxhour=0;
				foreach($menuarray as $l){
					if(isset($data[intval($l['SALETIME'])])){
						$data[intval($l['SALETIME'])]=intval($data[intval($l['SALETIME'])])+intval($l['AMT']);
					}
					else{
						$data[intval($l['SALETIME'])]=$l['AMT'];
					}
					if(intval($maxhour)<intval($l['SALETIME'])){
						$maxhour=$l['SALETIME'];
					}
					else{
					}
				}
			}
		}
		else{
		}
	}
	$table='';
	if(!isset($_POST['type'])||$_POST['type']!='view'){
		$PHPWord = new PHPWord();
		
		if(isset($print['item']['hispapertype'])){
			if(file_exists('../../../template/historypaper'.$print['item']['hispapertype'].'.docx')){
				$document = $PHPWord->loadTemplate('../../../template/historypaper'.$print['item']['hispapertype'].'.docx');
			}
			else{
				$document = $PHPWord->loadTemplate('../../../template/historypaper.docx');
			}
		}
		else{
			$document = $PHPWord->loadTemplate('../../../template/historypaper.docx');
		}
		$document->setValue('story',$setup['basic']['storyname']);
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']==$_POST['papbizdateE']){
			if($_POST['zcounter']=='allday'){
				$document->setValue('bizdate',$_POST['papbizdateS']);
			}
			else{
				$document->setValue('bizdate',$_POST['papbizdateS'].'-'.$_POST['zcounter']);
			}
		}
		else if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']!=$_POST['papbizdateE']){
			$document->setValue('bizdate',$_POST['papbizdateS'].'~'.$_POST['papbizdateE']);
		}
		else{
			$document->setValue('bizdate',$timeini['time']['bizdate'].'-'.$timeini['time']['zcounter']);
		}
		if($paper!='-1'){
			$document->setValue('title',$paper['historypaper']['histitle3']);
		}
		else{
			$document->setValue('title','時段金額彙總');
		}
		$document->setValue('date',date('Y/m/d'));
		$document->setValue('time',date('H:i:s'));

		$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		if($paper!='-1'){
			$table .= $paper['historypaper']['his3title1'];
		}
		else{
			$table .= "時段";
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:b/><w:szCs w:val="16"/></w:rPr><w:t>';
		if($paper!='-1'){
			$table .= $paper['historypaper']['his3title2'];
		}
		else{
			$table .= "金額";
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		$check=0;
		for($t=0;$t<=$maxhour;$t++){
			if($check==1){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= str_pad($t%24, 2, '0', STR_PAD_LEFT).':00~'.str_pad((intval($t)+1)%24, 2, '0', STR_PAD_LEFT).':00';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if(isset($data[$t])){
					$table .= number_format($data[$t]);
				}
				else{
					$table .= '0';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
				if(isset($data[$t])){
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= str_pad($t%24, 2, '0', STR_PAD_LEFT).':00~'.str_pad((intval($t)+1)%24, 2, '0', STR_PAD_LEFT).':00';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= number_format($data[$t]);
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
					$check=1;
				}
				else{
				}
			}
		}
		$table .= '</w:tbl>';
		$document->setValue('data',$table);
		if(sizeof($data)>0){
			//$document->save("../../../print/noread/".date('YmdHis')."_paper.docx");
			$filename=date('YmdHis');
			$document->save("../../../print/read/".$filename."_paper.docx");
			$prt=fopen("../../../print/noread/".$filename."_paper.prt",'w');
			fclose($prt);
			/*$prt=fopen("../../../print/noread/log_paper.txt",'w');
			fwrite($prt,$table);
			fclose($prt);*/
		}
		else{
			$document->save("../../../print/read/delete_paper.docx");
		}
	}
	else{
		echo '<div style="width:100%;height:100%;overflow-y:scroll;">';
		echo '<table style="border-collapse:collapse;text-align:right;">';
			echo '<caption>';
				echo '<table style="border-collapse:collapse;">';
					echo '<tr>
							<td nowrap="nowrap" style="text-align:center;">'.$setup['basic']['storyname'].'<td>
						</tr>
						<tr>
							<td nowrap="nowrap" style="text-align:center;">';
							if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']==$_POST['papbizdateE']){
								if($_POST['zcounter']=='allday'){
									echo $_POST['papbizdateS'];
								}
								else{
									echo $_POST['papbizdateS'].'-'.$_POST['zcounter'];
								}
							}
							else if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']!=$_POST['papbizdateE']){
								echo $_POST['papbizdateS'].'~'.$_POST['papbizdateE'];
							}
							else{
								echo $timeini['time']['bizdate'].'-'.$timeini['time']['zcounter'];
							}
							if($paper!='-1'){
								echo $paper['historypaper']['histitle3'];
							}
							else{
								echo '時段金額彙總';
							}
						echo '</td>
						</tr>';
				echo '</table>';
			echo '</caption>';
		
		echo '<tr>
				<td style="height:20px;"></td>
			</tr>
			<tr>
				<td style="text-align:left;">';
				if($paper!='-1'){
					echo $paper['historypaper']['his3title1'];
				}
				else{
					echo "時段";
				}
			echo '</td>
				<td>';
				if($paper!='-1'){
					echo $paper['historypaper']['his3title2'];
				}
				else{
					echo "金額";
				}
			echo '</td>
			</tr>';
		$check=0;
		for($t=0;$t<=$maxhour;$t++){
			if($check==1){
				echo '<tr>
						<td style="text-align:left;">'.str_pad($t%24, 2, '0', STR_PAD_LEFT).':00~'.str_pad((intval($t)+1)%24, 2, '0', STR_PAD_LEFT).':00</td>
						<td>';
						if(isset($data[$t])){
							echo number_format($data[$t]);
						}
						else{
							echo '0';
						}
					echo '</td>
					</tr>';
			}
			else{
				if(isset($data[$t])){
					echo '<tr>
							<td style="text-align:left;">'.str_pad($t%24, 2, '0', STR_PAD_LEFT).':00~'.str_pad((intval($t)+1)%24, 2, '0', STR_PAD_LEFT).':00</td>
							<td>'.number_format($data[$t]).'</td>
						</tr>';
					$check=1;
				}
				else{
				}
			}
		}
		echo '</table>';
		echo '</div>';
	}
}
else{
}
?>