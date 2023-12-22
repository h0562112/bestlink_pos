<?php
include_once "./nidin_api_inc.php";

$setup=parse_ini_file('../../../../database/setup.ini',true);

$getclass=GetClass($setup['nidin']['url'],"get",$_POST["Token"],$_POST["User"]);

echo json_encode($getclass);

$header=fopen('../../../../printlog.txt','a');
if($getclass!=null&&$getclass['status']==200){
	//fwrite($header,date('Y/m/d H:i:s').' -- NIDIN classStatus SUCCESS.(message='.$getclass['message'].';description='.$getclass['description'].')'.PHP_EOL);
	if(isset($getclass['token_need_replace'])&&$getclass['token_need_replace']==1){
		fwrite($header,date('Y/m/d H:i:s').' -- NIDIN TOKEN NEED REPLACE.(status='.$getclass['status'].') (token_need_replace='.$getclass['token_need_replace'].')'.PHP_EOL);
	}
	else{
	}
}
else if($getclass==null){
	fwrite($header,date('Y/m/d H:i:s').' -- CAN NOT CONNECT NIDIN SERVER.'.PHP_EOL);
}
else{
	if(isset($getclass['message'])){
	}
	else{
		$getclass['message']='';
	}
	if(isset($getclass['description'])){
	}
	else{
		$getclass['description']='';
	}
	if(isset($getclass['status'])){
	}
	else{
		$getclass['status']='';
	}
	fwrite($header,date('Y/m/d H:i:s').' -- NIDIN classStatus FAIL.(status='.$getclass['status'].') '.$getclass['message'].'('.$getclass['description'].')'.PHP_EOL);
}
fclose($header);
?>