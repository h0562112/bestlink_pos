<?php
if(file_exists('../../../../print/faceid/'.(substr($_POST['tag'],0,4)).'/'.(substr($_POST['tag'],4,2)).'/'.(substr($_POST['tag'],6,2)).'/faceid_log.ini')){
	$facedata=parse_ini_file('../../../../print/faceid/'.(substr($_POST['tag'],0,4)).'/'.(substr($_POST['tag'],4,2)).'/'.(substr($_POST['tag'],6,2)).'/faceid_log.ini',true);
	$res=['state'=>'success','tel'=>$facedata[$_POST['tag']]['tel']];
	echo json_encode($res);
}
else{
	echo json_encode(['state'=>'fail','filename'=>'../../../../print/faceid/'.(substr($_POST['tag'],0,4)).'/'.(substr($_POST['tag'],4,2)).'/'.(substr($_POST['tag'],6,2)).'/faceid_log.ini']);
}
?>