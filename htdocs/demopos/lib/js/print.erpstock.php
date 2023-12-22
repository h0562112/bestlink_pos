<?php
$_POST['stockdata']=json_decode($_POST['stockdata'],true);
//print_r($stock);
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$setup=parse_ini_file('../../../database/setup.ini',true);
if(isset($setup['a1erp']['warehouse'])){
	$warehouse=$setup['a1erp']['warehouse'];
}
else{
	$warehouse='公司倉';
}

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

$date=date('Y/m/d');
$time=date('H:i:s');

$frontname=parse_ini_file('../../../database/'.$setup['basic']['company'].'-front.ini',true);
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT inumber,fronttype FROM itemsdata ORDER BY typeseq ASC';
$menudata=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

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
	$document->setValue('bizdate',$date+' '+$warehouse);
	if(isset($paper['historypaper']['histitle5'])){
		$document->setValue('title',$paper['historypaper']['histitle5']);
	}
	else{
		$document->setValue('title','庫存表');
	}
	$document->setValue('date',$date);
	$document->setValue('time',$time);
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3161"/><w:gridCol w:w="734"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	if($paper!='-1'){
		$table .= $paper['name']['saleitem'];
	}
	else{
		$table .= 'Items';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:b/><w:szCs w:val="16"/></w:rPr><w:t>';
	if(isset($paper['name']['stock'])){
		$table .= $paper['name']['stock'];
	}
	else{
		$table .= '庫存';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$itemdeptcode='';
	foreach($menudata as $item){
		if(isset($_POST['stockdata'][$item['inumber']])){
			if($itemdeptcode==''||$itemdeptcode!=$item['fronttype']){
				$itemdeptcode=$item['fronttype'];
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $_POST['stockdata'][$item['inumber']]['name'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $_POST['stockdata'][$item['inumber']]['qty'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $_POST['stockdata'][$item['inumber']]['name'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $_POST['stockdata'][$item['inumber']]['qty'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
		}
		else{
		}
	}
	$table .= '</w:tbl>';
	$document->setValue('data',$table);
	$filename=date('YmdHis');
	$document->save("../../../print/read/".$filename."_paper.docx");
	$prt=fopen("../../../print/noread/".$filename."_paper.prt",'w');
	fclose($prt);
}
else{
	echo '<div style="width:100%;height:100%;overflow-x:scroll;-moz-column-count: 3;-moz-column-gap: 10px;-webkit-column-count: 3;-webkit-column-gap: 10px;column-count: 3;column-gap: 10px;">';
	echo '<table style="border-collapse:collapse;text-align:right;">';
		echo '<caption>';
			echo '<table style="border-collapse:collapse;">';
				echo '<tr>
						<td nowrap="nowrap" style="text-align:center;">'.$setup['basic']['storyname'].'<td>
					</tr>
					<tr>
						<td nowrap="nowrap" style="text-align:center;">';
						echo $date+' '+$warehouse;
						if(isset($paper['historypaper']['histitle5'])){
							echo $paper['historypaper']['histitle5'];
						}
						else{
							echo '庫存表';
						}
					echo '</td>
					</tr>';
			echo '</table>';
		echo '</caption>';

	if(sizeof($_POST['stockdata'])>0){
		echo '<tr>
				<td style="height:20px;"></td>
			</tr>';
		echo '<tr>
				<td style="text-align:left;border-top:1px solid #000000;">';
				if($paper!='-1'){
					echo $paper['name']['saleitem'];
				}
				else{
					echo 'Items';
				}
			echo '</td>
				<td style="border-top:1px solid #000000;">';
				if($paper!='-1'&&isset($paper['name']['stock'])){
					echo $paper['name']['stock'];
				}
				else{
					echo '庫存';
				}
			echo '</td>
			</tr>';
		$itemdeptcode='';
		foreach($menudata as $item){
			if(isset($_POST['stockdata'][$item['inumber']])){
				if($itemdeptcode==''||$itemdeptcode!=$item['fronttype']){
					$itemdeptcode=$item['fronttype'];
					echo '<tr>
							<td style="border-top:1px dashed #000000;text-align:left;">'.$_POST['stockdata'][$item['inumber']]['name'].'</td>
							<td style="border-top:1px dashed #000000;">'.$_POST['stockdata'][$item['inumber']]['qty'].'</td>
						</tr>';
				}
				else{
					echo '<tr>
							<td style="text-align:left;">'.$_POST['stockdata'][$item['inumber']]['name'].'</td>
							<td>'.$_POST['stockdata'][$item['inumber']]['qty'].'</td>
						</tr>';
				}
			}
			else{
			}
		}
		echo '</table>';
	}
	else{
	}
}
?>