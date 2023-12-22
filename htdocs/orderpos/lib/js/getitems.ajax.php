<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT inumber FROM itemsdata WHERE fronttype="'.$_POST['type'].'" ORDER BY frontsq ASC';
$item=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$itemname=parse_ini_file('../../../database/'.$_POST['story'].'-menu.ini',true);
if(isset($item)&&isset($item[0]['inumber'])){
	for($i=0;$i<sizeof($item);$i++){
		if(isset($itemname[$item[$i]['inumber']])&&$itemname[$item[$i]['inumber']]['state']=='1'){
			echo '<div class="item"><input type="hidden" name="item" value="'.$item[$i]['inumber'].'">'.$itemname[$item[$i]['inumber']]['name1'].'</div>';
		}
		else{
		}
	}
}
else{
}
?>