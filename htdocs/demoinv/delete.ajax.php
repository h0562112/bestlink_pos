<?php
include '../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$Y=date('Y');
$year=(intval($Y)-1911);
$m=date('m');
if(intval(date('m'))%2==0){
	$month=date('m');
}
else{
	$month=intval(date('m'))+1;
}
if(strlen($month)<2){
	$month='0'.$month;
}
$day=date('d');
$hour=date('H');
$min=date('m');
$sec=date('i');
$conn=sqlconnect("../database/sale","SALES_201710.db","","","","sqlite");
$number=strtoupper($_GET['number']);
$sql='SELECT * FROM invlist WHERE invnumber="'.$number.'" AND state=1';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

if(sizeof($data)==0||$data[0]=='連線失敗'||$data[0]=='SQL語法錯誤'){
	echo 'invlist is empty '.$number;
}
else{
	if(substr($data[0]['createdate'],0,6)!=($Y.$month)&&substr($data[0]['createdate'],0,6)!=date('Ym',strtotime($Y.'-'.$month.'-'.'01 -1 month'))){
		echo 'is not now'.substr($data[0]['createdate'],0,6).($Y.$month);
	}
	else{
		include 'cancelinv.php';
		$content=parse_ini_file('../database/setup.ini',true);
		//$filename='C0501_'.$number.'_'.$Y.$month.$day.$hour.$min.$sec.'.xml';
		$filename='C0501_'.$number.'_'.$Y.$m.$day.$hour.$min.$sec.'.xml';
		$xml=new SimpleXMLElement($xmlstr);
		$xml->addChild('CancelInvoiceNumber',$data[0]['invnumber']);
		$xml->addChild('InvoiceDate',$data[0]['createdate']);
		$xml->addChild('BuyerId',$data[0]['buyerid']);
		$xml->addChild('SellerId',$content['basic']['Identifier']);
		$xml->addChild('CancelDate',date('Ymd'));
		$xml->addChild('CancelTime',date('H:i:s'));
		$xml->addChild('CancelReason','作廢發票');
		$xml->saveXML('./'.$content['basic']['company'].'/'.$content['basic']['story'].'/'.$filename);
		$conn=sqlconnect("../database/sale","SALES_201710.db","","","","sqlite");
		$sql='UPDATE invlist SET state=99 WHERE invnumber="'.$number.'"';
		sqlnoresponse($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
		echo $filename;
	}
}
?>