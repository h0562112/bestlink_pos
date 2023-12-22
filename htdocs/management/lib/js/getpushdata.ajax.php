<?php
include_once '../../../tool/dbTool.inc.php';
$itemname=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini',true);
$unit=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/unit.ini',true);
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','manufact.db','','','','sqlite');
$sql='SELECT pushlist.*,pushlistdetail.itemno,pushlistdetail.qty,pushlistdetail.subtotal FROM pushlist JOIN pushlistdetail ON pushlist.listno=pushlistdetail.listno WHERE pushlist.no="'.$_POST['no'].'"';
$data=sqlquery($conn,$sql,'sqlite');
for($i=0;$i<sizeof($data);$i++){
	if(isset($itemname[$data[$i]['itemno']]['unit'])){
		if(isset($unit['unit'][$itemname[$data[$i]['itemno']]['unit']])){
			$data[$i]['unit']=$unit['unit'][$itemname[$data[$i]['itemno']]['unit']];
		}
		else{
			$data[$i]['unit']='份';
		}
	}
	else{
		$data[$i]['unit']='份';
	}
}
sqlclose($conn,'sqlite');
echo json_encode($data);
?>