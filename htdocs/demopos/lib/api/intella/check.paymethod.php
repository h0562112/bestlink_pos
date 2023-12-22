<?php
date_default_timezone_set('Asia/Taipei');
$methodmap=parse_ini_file('./data/methodmap.ini',true);
if(isset($methodmap['map'][$_POST['method']])){
	$res=['code'=>$_POST['method'],'payname'=>$methodmap['map'][$_POST['method']]];
}
else{
	$res=['code'=>$_POST['method'],'payname'=>'無對應方式'];
}
echo json_encode($res);
?>