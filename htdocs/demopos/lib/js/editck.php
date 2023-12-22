<?php
include_once '../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
$conn=sqlconnect('../../../google/Chrome/User Data/'.$initsetting['init']['posdvruser'],'Cookies','','','','sqlite');
//if(!$conn){
	$sql='update cookies set host_key="192.168.88.68",path="/" where host_key like "%localhost%" and name="auth"';
	$res=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	print_r($res);
/*}
else{
	echo 'fail';
}*/
?>