<?php
include_once '../../../../tool/dbTool.inc.php';
if(isset($_POST['phone'])){//�u���b����I�\(�U�Ⱥ�)�~�|�ǿ�i��
	$intellasetup=parse_ini_file('../../../../management/menudata/'.$_POST['machine'].'/'.$_POST['dep'].'/intellasetup.ini',true);
}
else{
	$intellasetup=parse_ini_file('../../../../database/intellasetup.ini',true);
}
date_default_timezone_set($intellasetup['intella']['settime']);

if(!isset($_POST['phone'])){
	srand(date('YmdHis'));
}
else{
	srand(date('YmdHis').$_POST['phone']);
}
$intellaconsecnumber=date('YmdHis').rand(100,999);//15�X����

$res=array("data"=>array("intellaconsecnumber"=>$intellaconsecnumber,'money'=>$_POST['money']));
echo json_encode($res);
?>