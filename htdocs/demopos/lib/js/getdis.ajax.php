<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT AMT,ITEMGRPCODE,ITEMGRPNAME FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis"';
$autodis=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

if(isset($autodis[0]['AMT'])){
	$res['autodis']=$autodis[0]['AMT'];
	$res['autodiscontent']=$autodis[0]['ITEMGRPCODE'];
	$res['autodispremoney']=$autodis[0]['ITEMGRPNAME'];
}
else{
	$res['autodis']=0;
	$res['autodiscontent']='';
	$res['autodispremoney']=0;
}

echo json_encode($res);
?>