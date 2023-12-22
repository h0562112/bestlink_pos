<?php
include_once '../../../tool/myerrorlog.php';
require_once '../../../tool/PHPWord.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(isset($print['item']['ticket'])&&$print['item']['ticket']!='0'){
	include_once '../../../tool/dbTool.inc.php';
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT DISTINCT CREATEDATETIME FROM (SELECT CREATEDATETIME FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'" UNION ALL SELECT CREATEDATETIME FROM voiditem WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'")';
	$times=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($times)&&sizeof($times)>1){
	}
	else if(isset($_POST['templistitem'])&&sizeof($_POST['templistitem'])==sizeof($_POST['no'])){
	}
	else{
		if(isset($print['item']['tickettype'])&&$print['item']['tickettype']!=''&&file_exists('../../../template/ticket'.$print['item']['tickettype'].'.docx')){
			$PHPWord = new PHPWord();
			$document1 = $PHPWord->loadTemplate('../../../template/ticket'.$print['item']['tickettype'].'.docx');
			$document1->setValue('story',$print['item']['tickettitle']);
			$document1->setValue('time',date('Y/m/d H:i:s'));
			$document1->save("../../../print/read/".$_POST['consecnumber']."_ticket".$_POST['machinetype'].".docx");
			$prt=fopen("../../../print/noread/".$_POST['consecnumber']."_ticket".$_POST['machinetype'].".prt",'w');
			fclose($prt);
		}
		else if(file_exists('../../../template/ticket.docx')){
			$PHPWord = new PHPWord();
			$document1 = $PHPWord->loadTemplate('../../../template/ticket.docx');
			$document1->setValue('story',$print['item']['tickettitle']);
			$document1->setValue('time',date('Y/m/d H:i:s'));
			$document1->save("../../../print/read/".$_POST['consecnumber']."_ticket".$_POST['machinetype'].".docx");
			$prt=fopen("../../../print/noread/".$_POST['consecnumber']."_ticket".$_POST['machinetype'].".prt",'w');
			fclose($prt);
		}
		else{
		}
	}
}
else{
}
?>