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
if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//�b�ȥH�C�x�������ӧO�D��p��
	$content=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{//�b�ȥH�D�����D��p��
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
if(isset($data[0]['id'])){//2022/4/21 ���Ҧ��\�A�ˬd�K�X�Ѽ�
	//2022/4/21 ��Ʈw�Y���ª��h�Q�γ]�w�ɱK�X�@���w�]�ɻ��ѼơA�N���ұK�X���y�{�Τ@�A����@�o�y�{������Ʈw�γ]�w�ɱK�X
	if($initsetting['init']['voidpsw']==''&&$initsetting['init']['voidsale']=='0'){//2022/4/21 �����ұK�X�B�]�w�ɱK�X���šA���F�y�{�Τ@�A�N�]�w�ɱK�X�]��'1'�A�קK����۰ʱa�J�K�X����ܵL�v��
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