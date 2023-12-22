<?php
include_once '../tool/dbTool.inc.php';
$id=strtolower($_POST['id']);
$psw=strtolower($_POST['psw']);
$initsetting=parse_ini_file('../database/initsetting.ini',true);
date_default_timezone_set($initsetting['init']['settime']);
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($_POST['machine'])&&isset($dbmapping['map'][$_POST['machine']])){
		$invmachine=$dbmapping['map'][$_POST['machine']];
	}
	else if(!isset($_POST['machine'])){
		$_POST['machine']='m1';
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$content=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$content=parse_ini_file('../database/timem1.ini',true);
}
$y=date('Y');
$m=date('m');
if(strlen($m)<2){
	$m='0'.$m;
}

if(file_exists('../database/sale/SALES_'.$y.$m.'.db')){
}
else{
	if(file_exists("../database/sale/empty.DB")){
	}
	else{
		include_once './lib/js/create.emptyDB.php';
		create('empty','./lib/sql/','../database/sale/','../tool/');
	}
	copy("../database/sale/empty.DB","../database/sale/SALES_".$y.$m.".DB");
}

$conn=sqlconnect('../database/person','data.db','','','','sqlite');
if(strlen($psw)==0){
	$sql='SELECT * FROM person WHERE id="'.$id.'" AND pw IS NULL AND state=1';
}
else{
	$sql='SELECT * FROM person WHERE id="'.$id.'" AND pw="'.$psw.'" AND state=1';
}
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(isset($data[0]['id'])){//2022/4/21 驗證成功，檢查密碼參數
	//2022/4/21 資料庫若為舊版則利用設定檔密碼作為預設補齊參數，將驗證密碼的流程統一，後續作廢流程不分資料庫或設定檔密碼
	if($initsetting['init']['voidpsw']==''&&$initsetting['init']['voidsale']=='0'){//2022/4/21 不驗證密碼且設定檔密碼為空，為了流程統一，將設定檔密碼設為'1'，避免後續自動帶入密碼時顯示無權限
		$initsetting['init']['voidpsw']='1';
	}
	else{
	}
	if(isset($data[0]['voidpw'])){
	}
	else{
		$data[0]['voidpw']=$initsetting['init']['voidpsw'];
	}
	if(isset($data[0]['paperpw'])){
	}
	else{
		$data[0]['paperpw']=$initsetting['init']['voidpsw'];
	}
	if(isset($data[0]['punchpw'])){
	}
	else{
		$data[0]['punchpw']=$initsetting['init']['voidpsw'];
	}
	if(isset($data[0]['reprintpw'])){
	}
	else{
		$data[0]['reprintpw']=$initsetting['init']['voidpsw'];
	}

	$conn=sqlconnect('../database/sale','SALES_'.$y.$m.'.db','','','','sqlite');
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['machine'].'","'.$content['time']['bizdate'].'"," ","login","'.$data[0]['id'].'","'.$data[0]['name'].'","9","9","99","'.$content['time']['zcounter'].'","'.date('YmdHis').'")';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
else{
}
echo json_encode($data);
?>