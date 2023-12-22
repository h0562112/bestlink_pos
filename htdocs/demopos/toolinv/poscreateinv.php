<?php
include 'tempXML.php';
include '../../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$year=(intval(date('Y'))-1911);
if(intval(date('m'))%2==0){
	$month=date('m');
}
else{
	$month=intval(date('m'))+1;
}
if(strlen($month)<2){
	$month='0'.$month;
}
$content=parse_ini_file('./setup.ini',true);
$conn=sqlconnect("localhost","ban","banuser","1qaz2wsx","utf-8","mysql");
//$sql='SELECT banno FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND dateTime="'.$year.$month.'" ORDER BY banno LIMIT 1';
//$table=sqlquery($conn,$sql,'mysql');
$filename='C0401_'.$_POST['tempinv'].'_'.date('YmdHis').'.xml';
$xml=new SimpleXMLElement($xmlstr);
$xml->addChild('Main');
$xmlMain=$xml->Main;
$xmlMain->addChild('InvoiceNumber',$_POST['tempinv']);
$xmlMain->addChild('InvoiceDate',date("Ymd"));
$xmlMain->addChild('InvoiceTime',date("H:i:s"));
$xmlMain->addChild('Seller');
$xmlMainSeller=$xmlMain->Seller;
$xmlMainSeller->addChild('Identifier',$content['basic']['Identifier']);
$xmlMainSeller->addChild('Name',$content['basic']['Name']);
$xmlMain->addChild('Buyer');
$xmlMainBuyer=$xmlMain->Buyer;
if(strlen($_POST['tempban'])==8){
	$xmlMainBuyer->addChild('Identifier',$_POST['tempban']);
	$xmlMainBuyer->addChild('Name','0000');
}
else{
	$xmlMainBuyer->addChild('Identifier','00000000');
	$xmlMainBuyer->addChild('Name','0000000000');
}
$xmlMain->addChild('RelateNumber','1');
$xmlMain->addChild('InvoiceType','07');
$xmlMain->addChild('DonateMark','0');
$xmlMain->addChild('PrintMark','Y');
srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
$rnumber=rand(0,9999);
while(strlen($rnumber)<4){
	$rnumber='0'.$rnumber;
}
$xmlMain->addChild('RandomNumber',$rnumber);
$xml->addChild('Details');
$xmlDetails=$xml->Details;
$xmlDetails->addChild('ProductItem');
$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
$xmlDetails->ProductItem[0]->addChild('Quantity','1');
$xmlDetails->ProductItem[0]->addChild('UnitPrice',$_POST['total']);
$xmlDetails->ProductItem[0]->addChild('Amount',$_POST['total']);
$xmlDetails->ProductItem[0]->addChild('SequenceNumber','1');
$xmlDetails->ProductItem[0]->addChild('Remark','Tx');
$xml->addChild('Amount');
$xmlAmount=$xml->Amount;
if(strlen($_POST['tempban'])==8){
	$xmlAmount->addChild('SalesAmount',round(intval($_POST['total'])/1.05));
	$xmlAmount->addChild('FreeTaxSalesAmount','0');
	$xmlAmount->addChild('ZeroTaxSalesAmount','0');
	$xmlAmount->addChild('TaxType','1');
	$xmlAmount->addChild('TaxRate','0.05');
	$xmlAmount->addChild('TaxAmount',intval($_POST['total'])-intval(round($_POST['total']/1.05)));
	$xmlAmount->addChild('TotalAmount',$_POST['total']);
}
else{
	$xmlAmount->addChild('SalesAmount',$_POST['total']);
	$xmlAmount->addChild('FreeTaxSalesAmount','0');
	$xmlAmount->addChild('ZeroTaxSalesAmount','0');
	$xmlAmount->addChild('TaxType','1');
	$xmlAmount->addChild('TaxRate','0.05');
	$xmlAmount->addChild('TaxAmount','0');
	$xmlAmount->addChild('TotalAmount',$_POST['total']);

}
$xml->saveXML('../'.$content['basic']['company'].'/'.$content['basic']['story'].'/'.$filename);
$sql='UPDATE number SET state=0 WHERE banno="'.$_POST['tempinv'].'" AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND dateTime="'.$year.$month.'"';
$table=sqlnoresponse($conn,$sql,'mysql');
sqlclose($conn,'mysql');
//echo "'ban://xml=".$filename.",company=".$content['basic']['story'].",dep=".$content['basic']['story']."'";
echo "<script>
		var mywin=window.open('ban://xml=".$filename.",company=".$content['basic']['company'].",dep=".$content['basic']['story'].",cmd=create,date=".$year.$month."','','width=1px,height=1px');
		/*mywin.document.write('<script>setTimeout(function(){window.close();},1000);\x3C\/script>');*/
		location.href='../order.php';
	</script>";
echo "列印電子發票中";
?>