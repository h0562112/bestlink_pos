<?php
include_once "./nidin_api_inc.php";

$setup=parse_ini_file('../../../../database/setup.ini',true);

$thelist=GetTheList($setup['nidin']['url'],"get",$_POST["Token"],$_POST["User"],$_POST['orderid']);

$header=fopen('../../../../printlog.txt','a');
if($thelist!=null&&$thelist['status']==200){
	fwrite($header,date('Y/m/d H:i:s').' -- NIDIN orderid/detail SUCCESS.(message='.$thelist['message'].';description='.$thelist['description'].')'.PHP_EOL);
	if(isset($thelist['token_need_replace'])&&$thelist['token_need_replace']==1){
		fwrite($header,date('Y/m/d H:i:s').' -- NIDIN TOKEN NEED REPLACE.(status='.$thelist['status'].') (token_need_replace='.$thelist['token_need_replace'].')'.PHP_EOL);
	}
	else{
	}
}
else if($thelist==null){
	fwrite($header,date('Y/m/d H:i:s').' -- CAN NOT CONNECT NIDIN SERVER.'.PHP_EOL);
}
else{
	if(isset($thelist['message'])){
	}
	else{
		$thelist['message']='';
	}
	if(isset($thelist['description'])){
	}
	else{
		$thelist['description']='';
	}
	if(isset($thelist['status'])){
	}
	else{
		$thelist['status']='';
	}
	fwrite($header,date('Y/m/d H:i:s').' -- NIDIN orderid/detail FAIL.(status='.$thelist['status'].') '.$thelist['message'].'('.$thelist['description'].')'.PHP_EOL);
}
fwrite($header,date('Y/m/d H:i:s').' -- getthelist data='.print_r($thelist,true).PHP_EOL);
fclose($header);

echo json_encode($thelist);
?>