<?php
include_once "./nidin_api_inc.php";

$setup=parse_ini_file('../../../../database/setup.ini',true);

$res=Login($setup['nidin']['url'],"post",$setup['nidin']['id'],$setup['nidin']['pw'],$_POST['dep'].'_'.$_POST['machine'],'','');

echo json_encode($res);

$header=fopen('../../../../printlog.txt','a');
if($res!=null&&$res['status']==200){
	fwrite($header,date('Y/m/d H:i:s').' -- NIDIN LOGIN SUCCESS.(message='.$res['message'].';description='.$res['description'].')'.PHP_EOL);
}
else if($res==null){
	fwrite($header,date('Y/m/d H:i:s').' -- CAN NOT CONNECT NIDIN SERVER.(PW='.hash('sha256',$setup['nidin']['pw']).')'.PHP_EOL);
}
else{
	if(isset($res['message'])){
	}
	else{
		$res['message']='';
	}
	if(isset($res['description'])){
	}
	else{
		$res['description']='';
	}
	if(isset($res['status'])){
	}
	else{
		$res['status']='';
	}
	fwrite($header,date('Y/m/d H:i:s').' -- NIDIN LOGIN FAIL.(status='.$res['status'].') '.$res['message'].'('.$res['description'].')'.PHP_EOL);
}
fclose($header);
?>