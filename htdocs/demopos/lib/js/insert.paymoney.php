<?php
include_once '../../../tool/dbTool.inc.php';
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
if(isset($content['init']['accounting'])&&$content['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
if(isset($_POST['usercode'])){
	$usercode=$_POST['usercode'];
	$username=$_POST['username'];
}
else{
	$usercode=' ';
	$username=' ';
}
if(isset($_POST['moneytype'])){
	$moneytype=$_POST['moneytype'];
}
else{
	$moneytype='paymemmoney';
}
$init=parse_ini_file('../../../database/initsetting.ini',true);
date_default_timezone_set($init['init']['settime']);
$filename='SALES_'.substr($timeini['time']['bizdate'],0,6);
$bizdate=$timeini['time']['bizdate'];
$zcounter=$timeini['time']['zcounter'];
//fwrite($file,'exist'.PHP_EOL);

if(isset($_POST['memno'])&&strlen($_POST['memno'])!=0){
	if(isset($init['init']['onlinemember'])&&$init['init']['onlinemember']=='1'){
		$PostData = array(
			"type"=> "online",
			"memno" => $_POST['memno'],
			//"CouponApiKey" => $itrisetup['itri']['couponapikey'],
			"company" => $_POST['company'],
			"ajax" => ""
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php');//
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$memdata = curl_exec($ch);
		$memdata=json_decode($memdata,1);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
		}
		curl_close($ch);
	}
	else{
		$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
		$sql='SELECT * FROM person WHERE memno="'.$_POST['memno'].'" AND state=1';
		$memdata=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
	}
}
else{
}
if(isset($memdata[0]['tel'])){
}
else{
	$memdata[0]['tel']='';
	$memdata[0]['memno']=$_POST['memno'];
}

if(file_exists("../../../database/sale/".$filename.".DB")){
}
else{
	if(file_exists("../../../database/sale/empty.DB")){
	}
	else{
		include_once 'create.emptyDB.php';
		create('empty');
	}
	copy("../../../database/sale/empty.DB","../../../database/sale/".$filename.".DB");
}
$conn=sqlconnect('../../../database/sale',$filename.'.db','','','','sqlite');
$timepoint=date('YmdHis');
if(isset($_POST['deletetime'])){
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ("'.$_POST['machinetype'].'","'.$bizdate.'"," ","'.$moneytype.'","'.$usercode.'","'.$username.'","9","9","99","'.$memdata[0]['tel'].'","'.$memdata[0]['memno'].'",'.$_POST['paymoney'].','.$_POST['getmemmoney'].',"'.$zcounter.'",0,"'.$timepoint.'")';
	sqlnoresponse($conn,$sql,'sqlite');
	$sql='UPDATE CST012 SET REMARKS=0 WHERE CREATEDATETIME="'.$_POST['deletetime'].'"';
	sqlnoresponse($conn,$sql,'sqlite');
}
else{
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ("'.$_POST['machinetype'].'","'.$bizdate.'"," ","'.$moneytype.'","'.$usercode.'","'.$username.'","9","9","99","'.$memdata[0]['tel'].'","'.$memdata[0]['memno'].'",'.$_POST['paymoney'].','.$_POST['getmemmoney'].',"'.$zcounter.'",1,"'.$timepoint.'")';
	sqlnoresponse($conn,$sql,'sqlite');
}
sqlclose($conn,'sqlite');
echo json_encode(['bizdate'=>$bizdate,'zcounter'=>$zcounter,'timepoint'=>$timepoint,'settime'=>$init['init']['settime']]);
?>