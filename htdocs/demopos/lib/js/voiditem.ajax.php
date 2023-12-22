<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';

$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
date_default_timezone_set($initsetting['init']['settime']);

$filename='SALES_'.substr($_POST['bizdate'],0,6);
$consecnumber=$_POST['consecnumber'];
if(file_exists("../../../database/sale/".$filename.".DB")){
}
else{
	copy("../../../database/sale/empty.DB","../../../database/sale/".$filename.".DB");
}
$conn=sqlconnect('../../../database/sale',$filename.'.DB','','','','sqlite');


$sql='SELECT QTY,AMT FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad(intval($_POST['linenumber']), 3, "0", STR_PAD_LEFT).'"';
$num=sqlquery($conn,$sql,'sqlite');
$sql='UPDATE tempCST011 SET SALESTTLQTY=(SELECT SALESTTLQTY-'.$num[0]['QTY'].' FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"),SALESTTLAMT=(SELECT SALESTTLAMT-'.$num[0]['AMT'].' FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'") WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'";';
//sqlnoresponse($conn,$sql,'sqlite');

$checkcolumn='PRAGMA table_info(voiditem)';
$allcolumn=sqlquery($conn,$checkcolumn,'sqlite');
if(!in_array('DELETEDATETIME',array_column($allcolumn,'name'))){
	$sqlalert='ALTER TABLE voiditem ADD COLUMN DELETEDATETIME VARCHAR (14) DEFAULT NULL;';
	sqlnoresponse($conn,$sqlalert,'sqliteexec');
}
else{
}

$sql=$sql.'INSERT INTO voiditem SELECT *,"'.$_POST['reason'].'",1,"'.date('YmdHis').'" FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad($_POST['linenumber'], 3, "0", STR_PAD_LEFT).'";';
$sql=$sql.'INSERT INTO voiditem SELECT *,"'.$_POST['reason'].'",1,"'.date('YmdHis').'" FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad(intval($_POST['linenumber'])+1, 3, "0", STR_PAD_LEFT).'";';
$sql=$sql.'UPDATE voiditem SET TERMINALNUMBER=(SELECT "'.$_POST['machine'].'"||"-"||TABLENUMBER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'") WHERE STATE=1;';
sqlnoresponse($conn,$sql,'sqliteexec');
//sqlnoresponse($conn,$sql,'sqlite');
//$sql='SELECT ITEMCODE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER="'.str_pad(intval($_POST['linenumber'])+1, 3, "0", STR_PAD_LEFT).'"';
//$y=sqlquery($conn,$sql,'sqlite');
$sql='DELETE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad($_POST['linenumber'], 3, "0", STR_PAD_LEFT).'";';
$sql=$sql.'DELETE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad(intval($_POST['linenumber'])+1, 3, "0", STR_PAD_LEFT).'";';
sqlnoresponse($conn,$sql,'sqliteexec');

/*if(sizeof($y)>0&&$y[0]['ITEMCODE']=='item'){
	$sql='INSERT INTO voiditem SELECT *,"'.$_POST['reason'].'",1 FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad(intval($_POST['linenumber'])+1, 3, "0", STR_PAD_LEFT).'"';
	sqlnoresponse($conn,$sql,'sqlite');
	$sql='DELETE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad(intval($_POST['linenumber'])+1, 3, "0", STR_PAD_LEFT).'";';
	sqlnoresponse($conn,$sql,'sqlite');
}
else{
}*/
sqlclose($conn,'sqlite');
?>