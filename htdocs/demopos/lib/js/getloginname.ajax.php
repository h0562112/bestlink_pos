<?php
$setting=parse_ini_file('../../../database/initsetting.ini',true);
$temp=array();
if(file_exists('../../syspram/login-'.$setting['init']['firlan'].'.ini')){
	$login=parse_ini_file('../../syspram/login-'.$setting['init']['firlan'].'.ini',true);
	$temp[0]=$login['name'][$_POST['name']];
}
else{
	$temp[0]='';
}
if(file_exists('../../syspram/login-'.$setting['init']['seclan'].'.ini')){
	$login=parse_ini_file('../../syspram/login-'.$setting['init']['seclan'].'.ini',true);
	$temp[1]=$login['name'][$_POST['name']];
}
else{
	$temp[1]='';
}
echo json_encode($temp);
?>