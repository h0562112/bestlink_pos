<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.DB','','','','sqlite');
$sql='SELECT TABLENUMBER,ZCOUNTER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(strstr($data[0]['TABLENUMBER'],',')){
	$temp='unlock-';
	$templist=preg_split('/,/',$data[0]['TABLENUMBER']);
	foreach($templist as $tl){
		if(file_exists('../../table/'.$_POST['bizdate'].';'.$data[0]['ZCOUNTER'].';'.$tl.'.ini')){
			$tabini=parse_ini_file('../../table/'.$_POST['bizdate'].';'.$data[0]['ZCOUNTER'].';'.$tl.'.ini',true);
			if(isset($tabini[$tl]['state'])&&$tabini[$tl]['state']=='999'&&$tabini[$tl]['machine']!=$_POST['machine']){
				$temp='lock-'.$tabini[$tl]['machine'];
				break;
			}
			else{
				$temp='unlock-'.$tabini[$tl]['machine'];
			}
		}
		else{
			$temp='unlock-';
		}
	}
	echo $temp;
}
else{
	if(file_exists('../../table/'.$_POST['bizdate'].';'.$data[0]['ZCOUNTER'].';'.$data[0]['TABLENUMBER'].'.ini')){
		$tabini=parse_ini_file('../../table/'.$_POST['bizdate'].';'.$data[0]['ZCOUNTER'].';'.$data[0]['TABLENUMBER'].'.ini',true);
		if(isset($tabini[$data[0]['TABLENUMBER']]['state'])&&$tabini[$data[0]['TABLENUMBER']]['state']=='999'&&$tabini[$data[0]['TABLENUMBER']]['machine']!=$_POST['machine']){
			echo 'lock-'.$tabini[$data[0]['TABLENUMBER']]['machine'];
		}
		else{
			echo 'unlock-'.$tabini[$data[0]['TABLENUMBER']]['machine'];
		}
	}
	else{
		echo 'unlock-';
	}
}
?>