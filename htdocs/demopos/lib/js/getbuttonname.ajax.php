<?php
$setting=parse_ini_file('../../../database/initsetting.ini',true);
$temp=array();
if(file_exists('../../syspram/buttons-'.$setting['init']['firlan'].'.ini')){
	$button=parse_ini_file('../../syspram/buttons-'.$setting['init']['firlan'].'.ini',true);
	$temp[0]=$button['name'][$_POST['name']];
}
else{
	$temp[0]='';
}
if(file_exists('../../syspram/buttons-'.$setting['init']['seclan'].'.ini')){
	$button=parse_ini_file('../../syspram/buttons-'.$setting['init']['seclan'].'.ini',true);
	$temp[1]=$button['name'][$_POST['name']];
}
else{
	$temp[1]='';
}
echo json_encode($temp);
?>