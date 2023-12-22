<?php
$kds=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini',true);
include_once '../../../tool/inilib.php';
if(isset($_POST['createnew'])){
	$kds['group'.$_POST['createpartition']]['name'][sizeof($kds['group'.$_POST['createpartition']]['name'])]=$_POST['createname'];//分群名稱
	$kds['group'.$_POST['createpartition']]['limit'][sizeof($kds['group'.$_POST['createpartition']]['limit'])]=$_POST['createlimit'];//群組數量上限;0為無上限
}
else{
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$temp=preg_split('/_/',$_POST['no'][$i]);
		$kds[$temp[0]]['name'][$temp[1]]=$_POST['newgroup'][$i];//分群名稱
		$kds[$temp[0]]['limit'][$temp[1]]=$_POST['newlimit'][$i];//群組數量上限;0為無上限
	}
}
write_ini_file($kds,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini');
?>