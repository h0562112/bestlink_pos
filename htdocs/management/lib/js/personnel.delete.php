<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$list='';
for($i=0;$i<sizeof($_POST['no']);$i++){
	if(strlen($list)==0){
		$list='"'.$_POST['no'][$i].'"';
	}
	else{
		$list=$list.',"'.$_POST['no'][$i].'"';
	}
}
if(sizeof($_POST['no'])==1){
	$sql="UPDATE personnel SET state=0 WHERE perno=".$list;
}
else{
	$sql="UPDATE personnel SET state=0 WHERE perno IN (".$list.")";
}
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>