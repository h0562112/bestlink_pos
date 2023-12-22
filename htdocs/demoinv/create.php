<?php
include_once 'newinv.php';
include_once '../tool/dbTool.inc.php';
include_once '../tool/inilib.php';
date_default_timezone_set('Asia/Taipei');
$machinedata=parse_ini_file('../database/machinedata.ini',true);
$machinedata['basic']['consecnumber']=intval($machinedata['basic']['consecnumber'])+1;
write_ini_file($machinedata,'../database/machinedata.ini');
$Y=date('Y');
$year=(intval($Y)-1911);
$month=date('m');
$day=date('d');
$hour=date('H');
$min=date('i');
$sec=date('s');
$content=parse_ini_file('../database/setup.ini',true);
$conn=sqlconnect("../database","menu.db","","","","sqlite");
//$sql='SELECT banno FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND dateTime="'.$year.$month.'" ORDER BY banno LIMIT 1';
//$table=sqlquery($conn,$sql,'mysql');
$filename='C0401_'.$_POST['tempinv'].'_'.$Y.$month.$day.$hour.$min.$sec.'.xml';
$xml=new SimpleXMLElement($xmlstr);
$xml->addChild('Main');
$xmlMain=$xml->Main;
$xmlMain->addChild('InvoiceNumber',$_POST['tempinv']);
$date=$Y.$month.$day;
$xmlMain->addChild('InvoiceDate',$date);
$time=$hour.':'.$min.':'.$sec;
$xmlMain->addChild('InvoiceTime',$time);
$xmlMain->addChild('Seller');
$xmlMainSeller=$xmlMain->Seller;
$xmlMainSeller->addChild('Identifier',$content['basic']['Identifier']);
$xmlMainSeller->addChild('Name',$content['basic']['Name']);
$xmlMain->addChild('Buyer');
$xmlMainBuyer=$xmlMain->Buyer;
if(strlen($_POST['tempban'])==8){
	$buyerid=$_POST['tempban'];
	$xmlMainBuyer->addChild('Identifier',$_POST['tempban']);
	$buyername='0000';
	$xmlMainBuyer->addChild('Name','0000');
}
else{
	$buyerid='0000000000';
	$xmlMainBuyer->addChild('Identifier','0000000000');
	$buyername='0000000000';
	$xmlMainBuyer->addChild('Name','0000000000');
}
$xmlMain->addChild('RelateNumber',$machinedata['basic']['consecnumber']);
$xmlMain->addChild('InvoiceType','07');
if(isset($_POST['tempcontainer'])&&strlen($_POST['tempcontainer'])>0){
	if(substr($_POST['tempcontainer'],0,1)=='/'){//共通載具
		$xmlMain->addChild('DonateMark','0');
		$xmlMain->addChild('CarrierType','3J0002');
		$xmlMain->addChild('CarrierId1',$_POST['tempcontainer']);
		$xmlMain->addChild('CarrierId2',$_POST['tempcontainer']);
		if(strlen($_POST['tempban'])==8){
			$xmlMain->addChild('PrintMark','Y');
		}
		else{
			$xmlMain->addChild('PrintMark','N');
		}
	}
	else if(is_numeric(substr($_POST['tempcontainer'],0,1))){//愛心碼
		$xmlMain->addChild('DonateMark','1');
		$xmlMain->addChild('PrintMark','N');
		$xmlMain->addChild('NPOBAN',$_POST['tempcontainer']);
	}
	else if(strlen($_POST['tempcontainer'])==16){//自然人憑證
		$xmlMain->addChild('DonateMark','0');
		$xmlMain->addChild('CarrierType','CQ0001');
		$xmlMain->addChild('CarrierId1',$_POST['tempcontainer']);
		$xmlMain->addChild('CarrierId2',$_POST['tempcontainer']);
		$xmlMain->addChild('PrintMark','N');
	}
	else{
		$xmlMain->addChild('DonateMark','0');
		$xmlMain->addChild('PrintMark','Y');
	}
}
else{
	$xmlMain->addChild('DonateMark','0');
	$xmlMain->addChild('PrintMark','Y');
}
srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
$rnumber=rand(0,9999);
while(strlen($rnumber)<4){
	$rnumber='0'.$rnumber;
}
$xmlMain->addChild('RandomNumber',$rnumber);
$xml->addChild('Details');
$value='';
$xmlDetails=$xml->Details;
$xmlDetails->addChild('ProductItem');
$value=$value.'"'.$content['basic']['itemname'].'",';
$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
$value=$value.'1,';
$xmlDetails->ProductItem[0]->addChild('Quantity','1');
$value=$value.$_POST['total'].',';
$xmlDetails->ProductItem[0]->addChild('UnitPrice',$_POST['total']);
$value=$value.$_POST['total'].',';
$xmlDetails->ProductItem[0]->addChild('Amount',$_POST['total']);
$value=$value.'1';
$xmlDetails->ProductItem[0]->addChild('SequenceNumber','001');
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
if(intval(date('m'))%2==0){
	$month=date('m');
}
else{
	$month=intval(date('m'))+1;
}
if(strlen($month)<2){
	$month='0'.$month;
}
$xml->saveXML('./'.$content['basic']['company'].'/'.$content['basic']['story'].'/'.$filename);
$sql='UPDATE number SET state=0 WHERE banno="'.$_POST['tempinv'].'" AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND dateTime="'.$year.$month.'"';
sqlnoresponse($conn,$sql,'sqlite');
if(isset($_POST['tempcontainer'])&&strlen($_POST['tempcontainer'])>0){
	if(substr($_POST['tempcontainer'],0,1)=='/'){
		$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$_POST['tempinv'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$machinedata['basic']['consecnumber'].'","0","3J0002","'.$_POST['tempcontainer'].'","'.$_POST['tempcontainer'].'",';
		if(strlen($_POST['tempban'])==8){
			$sql=$sql.'"Y",';
		}
		else{
			$sql=$sql.'"N",';
		}
		$sql=$sql.'NULL,"'.$rnumber.'",'.$_POST['total'].')';
	}
	else if(is_numeric(substr($_POST['tempcontainer'],0,1))){
		$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$_POST['tempinv'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$machinedata['basic']['consecnumber'].'","1",NULL,NULL,NULL,"N","'.$_POST['tempcontainer'].'","'.$rnumber.'",'.$_POST['total'].')';
	}
	else if(strlen($_POST['tempcontainer'])==16){
		$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$_POST['tempinv'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$machinedata['basic']['consecnumber'].'","0","CQ0001","'.$_POST['tempcontainer'].'","'.$_POST['tempcontainer'].'","N",NULL,"'.$rnumber.'",'.$_POST['total'].')';
	}
	else{
		$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$_POST['tempinv'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$machinedata['basic']['consecnumber'].'","0",NULL,NULL,NULL,"Y",NULL,"'.$rnumber.'",'.$_POST['total'].')';
	}
}
else{
	$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$_POST['tempinv'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$machinedata['basic']['consecnumber'].'","0",NULL,NULL,NULL,"Y",NULL,"'.$rnumber.'",'.$_POST['total'].')';
}
sqlnoresponse($conn,$sql,'sqlite');
$sql='INSERT INTO salelist (listno,invnumber,createdate,createtime,name,qty,unitprice,money,lineno) VALUES ("'.$machinedata['basic']['consecnumber'].'","'.$_POST['tempinv'].'","'.$date.'","'.$time.'","'.$content['basic']['itemname'].'",1,'.$_POST['total'].','.$_POST['total'].',1)';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
//echo "'ban://xml=".$filename.",company=".$content['basic']['story'].",dep=".$content['basic']['story']."'";
echo "<script>
		var mywin=window.open('ban://xml=".$filename.",company=".$content['basic']['company'].",dep=".$content['basic']['story'].",cmd=create,date=".$year.$month."','','width=1px,height=1px');
		/*mywin.document.write('<script>setTimeout(function(){window.close();},1000);\x3C\/script>');*/
		window.setTimeout(function(){location.href='./order.php';},5000);
	</script>";
echo "<center><img src='loading.gif'></center><br><center>列印電子發票中</center>";
?>