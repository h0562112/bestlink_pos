<?php
include_once '../../../tool/myerrorlog.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
$map=parse_ini_file('../../../database/mapping.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($_POST['machinetype'])&&isset($map['map'][$_POST['machinetype']])){//�b�ȥH�C�x�������ӧO�D��p��
	$content=parse_ini_file('../../../database/time'.$map['map'][$_POST['machinetype']].'.ini',true);
}
else{//�b�ȥH�D�����D��p��
	$content=parse_ini_file('../../../database/timem1.ini',true);
}
if($content['time']['isopen']=='0'){
	echo 'success';
}
else{
	echo 'error';
}
?>