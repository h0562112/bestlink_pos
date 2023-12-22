<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
if(file_exists('../../../database/personnel.ini')){
	$personnel=parse_ini_file('../../../database/personnel.ini',true);
}
else{
	//$personnel='-1';
}
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($initsetting['init']['settime']);
$pri=parse_ini_file('../../../database/printlisttag.ini',true);
$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
$sql='SELECT perno FROM personnel WHERE percard="'.$_POST['punchno'].'" AND state=1';
$perno=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,$sql,'sqlite');
if(isset($perno[0])){
	if(file_exists("../../../database/person/punch.db")){
	}
	else{
		include_once 'create.emptyDB.php';
		create('punch','../sql/','../../../database/person/');
	}
	$conn=sqlconnect('../../../database/person','punch.db','','','','sqlite');
	$sql='SELECT * FROM punchlist WHERE perno="'.$perno[0]['perno'].'" AND state=1 ORDER BY firstdatetime DESC LIMIT 1';
	$result=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$date=date('Y-m-d');
	$time=date('H:i:s');
	if(isset($personnel)&&$personnel['basic']['punchtype']=='1'){//免打下班卡(報班)
		echo 'success;'.$date.' '.$time;
		$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
		$sql='SELECT name FROM personnel WHERE percard="'.$_POST['punchno'].'" AND state=1 ';
		$pername=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');

		echo ';'.$_POST['punchno'].';'.$pername[0]['name'];

		if(isset($pri['punchlist']['print'])&&$pri['punchlist']['print']=='1'&&file_exists('../../../template/punchlist'.$pri['punchlist']['size'].'.docx')){
			require_once '../../../tool/PHPWord.php';
			
			if(file_exists('../../syspram/punchlist-'.$initsetting['init']['firlan'].'.ini')){
				$lan=parse_ini_file('../../syspram/punchlist-'.$initsetting['init']['firlan'].'.ini',true);
			}
			else if(file_exists('../../syspram/punchlist-TW.ini')){
				$lan=parse_ini_file('../../syspram/punchlist-TW.ini',true);
			}
			else{
				$lan=parse_ini_file('../../syspram/punchlist-1.ini',true);
			}
			$PHPWord = new PHPWord();
			$document = $PHPWord->loadTemplate('../../../template/punchlist'.$pri['punchlist']['size'].'.docx');
			$table='';
			$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['labelsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['labelsize'])*2).'"/></w:rPr><w:t>';
			$table .= $lan['name']['onlyontitle'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/></w:rPr><w:t>';
			$table .= $_POST['punchno'].' '.$pername[0]['name'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['labelsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['labelsize'])*2).'"/></w:rPr><w:t>';
			$table .= $lan['name']['onlyrow'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/></w:rPr><w:t>';
			$table .= $date.' '.substr($time,0,5);
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$table .= "</w:tbl>";
			$document->setValue('table',$table);
			$filetime=date('YmdHis');
			$document->save("../../../print/read/".$filetime."_punchlist".$_POST['machinetype'].".docx");
			$prt=fopen("../../../print/noread/".$filetime."_punchlist".$_POST['machinetype'].".prt",'w');
			fclose($prt);
		}
		else{
		}
	}
	else{//須打下班卡
		if(sizeof($result)==0||$result[0]['type']==$_POST['type']){
			echo 'success;'.$date.' '.$time;
			$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
			$sql='SELECT name FROM personnel WHERE percard="'.$_POST['punchno'].'" AND state=1 ';
			$pername=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');

			echo ';'.$_POST['punchno'].';'.$pername[0]['name'];

			if($_POST['type']=='on'&&isset($pri['punchlist']['print'])&&$pri['punchlist']['print']=='1'&&(file_exists('../../../template/punchlist'.$pri['punchlist']['size'].'.docx')||file_exists('../../../template/punchlist.docx'))){
				require_once '../../../tool/PHPWord.php';
				if(file_exists('../../syspram/punchlist-'.$initsetting['init']['firlan'].'.ini')){
					$lan=parse_ini_file('../../syspram/punchlist-'.$initsetting['init']['firlan'].'.ini',true);
				}
				else if(file_exists('../../syspram/punchlist-TW.ini')){
					$lan=parse_ini_file('../../syspram/punchlist-TW.ini',true);
				}
				else{
					$lan=parse_ini_file('../../syspram/punchlist-1.ini',true);
				}
				$PHPWord = new PHPWord();
				if(file_exists('../../../template/punchlist'.$pri['punchlist']['size'].'.docx')){
					$document = $PHPWord->loadTemplate('../../../template/punchlist'.$pri['punchlist']['size'].'.docx');
				}
				else{
					$document = $PHPWord->loadTemplate('../../../template/punchlist.docx');
				}
				$table='';
				$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['labelsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['labelsize'])*2).'"/></w:rPr><w:t>';
				$table .= $lan['name']['punchtitle'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/></w:rPr><w:t>';
				$table .= $_POST['punchno'].' '.$pername[0]['name'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['labelsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['labelsize'])*2).'"/></w:rPr><w:t>';
				$table .= $lan['name']['punchrow'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/></w:rPr><w:t>';
				$table .= preg_replace('/-/','-',$result[0]['date']).' '.substr($result[0]['time'],0,5);
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$pri['punchlist']['textfont'].'" w:eastAsia="'.$pri['punchlist']['textfont'].'" w:hAnsi="'.$pri['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/><w:szCs w:val="'.(floatval($pri['punchlist']['contentsize'])*2).'"/></w:rPr><w:t>';
				$table .= $date.' '.substr($time,0,5);
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$table .= "</w:tbl>";
				$document->setValue('table',$table);
				$filetime=date('YmdHis');
				$document->save("../../../print/read/".$filetime."_punchlist".$_POST['machinetype'].".docx");
				$prt=fopen("../../../print/noread/".$filetime."_punchlist".$_POST['machinetype'].".prt",'w');
				fclose($prt);
			}
			else{
			}
		}
		else{
			echo 'fail';
		}
	}
}
else{
	echo 'error';
}
?>