<?php
include_once '../../../tool/myerrorlog.php';
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
$data=array();
if(file_exists('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini')){
	$buttons1=parse_ini_file('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
	array_push($data,$buttons1['name']['billfun15'.$_POST['type']]);
}
else{
	array_push($data,'');
}
if(file_exists('../../syspram/buttons-'.$initsetting['init']['seclan'].'.ini')){
	$buttons2=parse_ini_file('../../syspram/buttons-'.$initsetting['init']['seclan'].'.ini',true);
	array_push($data,$buttons2['name']['billfun15'.$_POST['type']]);
}
else{
	array_push($data,'');
}
if($_POST['type']=='a'){
	array_push($data,'暫結出單');
}
else{
	array_push($data,'回到桌控');
}
echo json_encode($data);
?>