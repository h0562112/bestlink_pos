<?php
include '../../../tool/dbTool.inc.php';
$conn=sqlconnect("../../../database","menu.db","","","","sqlite");
$temp=preg_split('/,/',$_POST['childtype']);
$a='';
foreach($temp as $t){
	if(strlen($a)==0){
		$a='"'.$t.'"';
	}
	else{
		$a=$a.',"'.$t.'"';
	}
}
$sql='SELECT * FROM items WHERE itemdep IN ('.$a.') ORDER BY itemdep';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo json_encode($data);
?>