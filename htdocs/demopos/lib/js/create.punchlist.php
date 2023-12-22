<?php
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($initsetting['init']['settime']);

if(file_exists('../../syspram/interface-'.$initsetting['init']['firlan'].'.ini')){
	$interface=parse_ini_file('../../syspram/interface-'.$initsetting['init']['firlan'].'.ini',true);
}
else if(file_exists('../../syspram/interface-TW.ini')){
	$interface=parse_ini_file('../../syspram/interface-TW.ini',true);
}
else{
	$interface=parse_ini_file('../../syspram/interface-1.ini',true);
}
if(file_exists('../../../database/personnel.ini')){
	$personnel=parse_ini_file('../../../database/personnel.ini',true);
}
else{
	//$personnel=parse_ini_file('../../../database/personnel.ini',true);
}
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(isset($print['punchlist']['textfont'])){
}
else{
	$print['punchlist']['textfont']="微軟正黑體";
}
$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
$sql='SELECT perno,percard,name FROM personnel';
$personlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../database/person','punch.db','','','','sqlite');
$sql='SELECT * FROM punchlist WHERE date BETWEEN "'.$_POST['start'].'" AND "'.$_POST['end'].'" AND state=1';
if($_POST['perno']=='all'){
}
else{
	$sql.=' AND perno="'.$_POST['perno'].'"';
}
$sql.=' ORDER BY perno ASC,date ASC,time ASC';
$punchlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$person=array();
for($i=0;$i<sizeof($personlist);$i++){
	$person[$personlist[$i]['perno']]['percard']=$personlist[$i]['percard'];
	$person[$personlist[$i]['perno']]['name']=$personlist[$i]['name'];
}
$punch=array();
$error=array();
$date='';
$time='';
for($i=0;$i<sizeof($punchlist);$i++){
	if($punchlist[$i]['type']=='on'){
		$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['on']=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
		$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['realoff']='';
		$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['off']='';
		$date=$punchlist[$i]['date'];
		$time=$punchlist[$i]['time'];
	}
	else if($punchlist[$i]['type']=='off'){
		$tempontime=preg_split('/:/',$punchlist[$i]['time']);
		if(intval($tempontime[1])<30){
			$tempontime[1]='00';
		}
		else{
			$tempontime[1]='30';
		}
		$temptime=implode(':',$tempontime);
		if($date!=''&&$time!=''){
			$punch[$punchlist[$i]['perno']][$date][$time]['realoff']=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
			if(isset($personnel['basic']['computetime'])&&$personnel['basic']['computetime']=='2'){//時數以整點為主
				//$punch[$punchlist[$i]['perno']][$date][$time]['off'][sizeof($punch[$punchlist[$i]['perno']][$date][$time]['off'])-1]=$punchlist[$i]['date'].' '.$temptime;
				$punch[$punchlist[$i]['perno']][$date][$time]['off']=$punchlist[$i]['date'].' '.$temptime;
			}
			else{//時數以分鐘為主
				//$punch[$punchlist[$i]['perno']][$date][$time]['off'][sizeof($punch[$punchlist[$i]['perno']][$date][$time]['off'])-1]=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
				$punch[$punchlist[$i]['perno']][$date][$time]['off']=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
			}
		}
		else{
			$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['on']='';
			$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['realoff']=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
			if(isset($personnel['basic']['computetime'])&&$personnel['basic']['computetime']=='2'){//時數以整點為主
				//$punch[$punchlist[$i]['perno']][$date][$time]['off'][sizeof($punch[$punchlist[$i]['perno']][$date][$time]['off'])-1]=$punchlist[$i]['date'].' '.$temptime;
				$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['off']=$punchlist[$i]['date'].' '.$temptime;
			}
			else{//時數以分鐘為主
				//$punch[$punchlist[$i]['perno']][$date][$time]['off'][sizeof($punch[$punchlist[$i]['perno']][$date][$time]['off'])-1]=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
				$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['off']=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
			}
			//array_push($error,array('date'=>$date,'time'=>$time,'type'=>$punchlist[$i]['type'],'datetime'=>$punchlist[$i]['date'].' '.$punchlist[$i]['time']));
		}
		$date='';
		$time='';
	}
	else{
	}
}
$PHPWord = new PHPWord();
if(isset($print['punchlist']['size'])&&file_exists('../../../template/punchpaper'.$print['punchlist']['size'].'.docx')){
	$document = $PHPWord->loadTemplate('../../../template/punchpaper'.$print['punchlist']['size'].'.docx');
}
else{
	$document = $PHPWord->loadTemplate('../../../template/punchpaper.docx');
}
$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="833"/><w:gridCol w:w="833"/><w:gridCol w:w="833"/><w:gridCol w:w="833"/><w:gridCol w:w="834"/><w:gridCol w:w="834"/></w:tblGrid>';

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="6"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:t>';
$table .= preg_replace('/-/','/',$_POST['start']).'~'.preg_replace('/-/','/',$_POST['end']);
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

if(strtotime($_POST['end'])>=strtotime(date('Y-m-d'))){
	$enddate=date('Y-m-d');
}
else{
	$enddate=$_POST['end'];
}
foreach($punch as $perno=>$data){
	$date=0;
	$ontimes=0;
	$worktime=0;

	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="6"/><w:tcBorders><w:top w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
	$table .= $person[$perno]['percard'].$person[$perno]['name'];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";

	$temp = '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$temp .= '上班日';
	$temp .= "</w:t></w:r></w:p></w:tc>";
	$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="single" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$temp .= '上班';
	$temp .= '</w:t></w:r></w:p><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$temp .= '時間';
	$temp .= '</w:t></w:r></w:p></w:tc>';
	$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$temp .= '下班';
	$temp .= '</w:t></w:r></w:p><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$temp .= '時間';
	$temp .= '</w:t></w:r></w:p></w:tc>';
	$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="single" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$temp .= '時數';
	$temp .= "</w:t></w:r></w:p></w:tc>";
	$temp .= "</w:tr>";

	for($d=strtotime($_POST['start']);$d<=strtotime($enddate);$d=strtotime(date('Y-m-d',$d).' +1 day')){
		if(isset($data[date('Y-m-d',$d)])){
			$date++;
			$preclass=0;
			foreach($data[date('Y-m-d',$d)] as $time=>$l){
				$ontimes++;
				$temp .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				if($preclass==0){
					$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$temp .= substr(date('Y/m/d',$d),2);
					$temp .= '</w:t></w:r></w:p><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					switch(date('N',$d)){
						case 1:
							if(isset($interface['name']['editpunchmon'])){
								$temp .= '('.$interface['name']['editpunchmon'].')';
							}
							else{
								$temp .= '(一)';
							}
							break;
						case 2:
							if(isset($interface['name']['editpunchtue'])){
								$temp .= '('.$interface['name']['editpunchtue'].')';
							}
							else{
								$temp .='(二)';
							}
							break;
						case 3:
							if(isset($interface['name']['editpunchwed'])){
								$temp .= '('.$interface['name']['editpunchwed'].')';
							}
							else{
								$temp .= '(三)';
							}
							break;
						case 4:
							if(isset($interface['name']['editpunchthu'])){
								$temp .= '('.$interface['name']['editpunchthu'].')';
							}
							else{
								$temp .= '(四)';
							}
							break;
						case 5:
							if(isset($interface['name']['editpunchfri'])){
								$temp .= '('.$interface['name']['editpunchfri'].')';
							}
							else{
								$temp .= '(五)';
							}
							break;
						case 6:
							if(isset($interface['name']['editpunchsat'])){
								$temp .= '('.$interface['name']['editpunchsat'].')';
							}
							else{
								$temp .= '(六)';
							}
							break;
						case 7:
							if(isset($interface['name']['editpunchsun'])){
								$temp .= '('.$interface['name']['editpunchsun'].')';
							}
							else{
								$temp .= '(日)';
							}
							break;
						default:
							break;
					}
					$temp .= '</w:t></w:r></w:p></w:tc>';
				}
				else{
					$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$temp .= "</w:t></w:r></w:p></w:tc>";
				}
				
				if($l['on']!=''){
					$splittemp=preg_split('/ /',preg_replace('/-/','/',substr($l['on'],2,strlen($l['on'])-5)));
					$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$temp .= $splittemp[0];
					$temp .= '</w:t></w:r></w:p><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$temp .= $splittemp[1];
					$temp .= '</w:t></w:r></w:p></w:tc>';
				}
				else{
					$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$temp .= '</w:t></w:r></w:p></w:tc>';
				}

				if($l['realoff']!=''){
					$splittemp=preg_split('/ /',preg_replace('/-/','/',substr($l['realoff'],2,strlen($l['realoff'])-5)));
					$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$temp .= $splittemp[0];
					$temp .= '</w:t></w:r></w:p><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$temp .= $splittemp[1];
					$temp .= '</w:t></w:r></w:p></w:tc>';
				}
				else{
					$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$temp .= '</w:t></w:r></w:p></w:tc>';
				}

				$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($l['on']!=''&&$l['off']!=''){

					$diff=date_diff(date_create($l['off']),date_create($l['on']));
					$temptime=preg_split('/:/',$diff->format('%d:%h:%i'));
					//print_r($dt);
					if(intval(intval($temptime[2])/30)){
						$dt=intval($temptime[0])*24+intval($temptime[1])+(intval(intval(intval($temptime[2])/30))/2);
					}
					else{
						$dt=intval($temptime[0])*24+intval($temptime[1]);
					}
					$temp .= $dt;
					$worktime=floatval($worktime)+floatval($dt);
				}
				else{
				}
				$temp .= "</w:t></w:r></w:p></w:tc>";
				$temp .= "</w:tr>";

				$preclass++;
			}
		}
		else{
			$temp .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			$temp .= substr(date('Y/m/d',$d),2);
			$temp .= '</w:t></w:r></w:p><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			switch(date('N',$d)){
				case 1:
					if(isset($interface['name']['editpunchmon'])){
						$temp .= '('.$interface['name']['editpunchmon'].')';
					}
					else{
						$temp .= '(一)';
					}
					break;
				case 2:
					if(isset($interface['name']['editpunchtue'])){
						$temp .= '('.$interface['name']['editpunchtue'].')';
					}
					else{
						$temp .='(二)';
					}
					break;
				case 3:
					if(isset($interface['name']['editpunchwed'])){
						$temp .= '('.$interface['name']['editpunchwed'].')';
					}
					else{
						$temp .= '(三)';
					}
					break;
				case 4:
					if(isset($interface['name']['editpunchthu'])){
						$temp .= '('.$interface['name']['editpunchthu'].')';
					}
					else{
						$temp .= '(四)';
					}
					break;
				case 5:
					if(isset($interface['name']['editpunchfri'])){
						$temp .= '('.$interface['name']['editpunchfri'].')';
					}
					else{
						$temp .= '(五)';
					}
					break;
				case 6:
					if(isset($interface['name']['editpunchsat'])){
						$temp .= '('.$interface['name']['editpunchsat'].')';
					}
					else{
						$temp .= '(六)';
					}
					break;
				case 7:
					if(isset($interface['name']['editpunchsun'])){
						$temp .= '('.$interface['name']['editpunchsun'].')';
					}
					else{
						$temp .= '(日)';
					}
					break;
				default:
					break;
			}
			$temp .= '</w:t></w:r></w:p></w:tc>';
			$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			$temp .= '</w:t></w:r></w:p></w:tc>';
			$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			$temp .= '</w:t></w:r></w:p></w:tc>';
			$temp .= '<w:tc><w:tcPr><w:tcW w:w="1250" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			$temp .= "</w:t></w:r></w:p></w:tc>";
			$temp .= "</w:tr>";
		}
	}

	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	if(isset($interface['name']['editpunchreslabel1'])){
		$table .=$interface['name']['editpunchreslabel1'];
	}
	else{
		$table .='總上班日';
	}
	$table .=$date;
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1666" w:type="pct"/><w:gridSpan w:val="2"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	if(isset($interface['name']['editpunchreslabel2'])){
		$table .= $interface['name']['editpunchreslabel2'];
	}
	else{
		$table .= '總班次';
	}
	$table .= $ontimes;
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['punchlist']['textfont'].'" w:eastAsia="'.$print['punchlist']['textfont'].'" w:hAnsi="'.$print['punchlist']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	if(isset($interface['name']['editpunchreslabel3'])){
		$table .= $interface['name']['editpunchreslabel3'];
	}
	else{
		$table .= '總時數';
	}
	$table .= $worktime;
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$table .= $temp;
}
$table .= '</w:tbl>';
$filename=date('YmdHis');
$document->setValue('table',$table);
$document->save("../../../print/read/".$filename."_punchlist.docx");
$prt=fopen("../../../print/noread/".$filename."_punchlist.prt",'w');
fclose($prt);
?>