<?php
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(file_exists('../../../database/itemdis.ini')){
	$itemdis=parse_ini_file('../../../database/itemdis.ini',true);
}
else{
	$itemdis='';
}
$res=array();
if(isset($itemdis['listdis'])){
	for($i=1;$i<=6;$i++){
		$res['disbut'.$i]=$itemdis['listdis']['state'.$i];
		$res['disnum'.$i]=$itemdis['listdis']['number'.$i];
		//$res['distype'.$i]=$itemdis['listdis']['type'.$i];
	}
}
else{
	for($i=1;$i<=6;$i++){
		$res['disbut'.$i]=$init['init']['disbut'.$i];
		$res['disnum'.$i]=$init['init']['disnum'.$i];
	}
}
echo json_encode($res);
?>