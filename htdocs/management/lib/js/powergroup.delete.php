<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$list='';
for($i=0;$i<sizeof($_POST['pg']);$i++){
	if(strlen($list)==0){
		$list=$_POST['pg'][$i];
	}
	else{
		$list=$list.','.$_POST['pg'][$i];
	}
}
$sql="UPDATE powergroup SET state=0,`delete`=1 WHERE pno IN (".$list.")";
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>