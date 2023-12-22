<?php
if(file_exists('../../../database/sale/'.$_GET['db'].'.db')){
}
else{
	if(file_exists('./'.$_GET['db'].'.ini')){
		$sql=parse_ini_file('./'.$_GET['db'].'.ini',true);
		$f=fopen('../../../database/sale/'.$_GET['db'].'.db','w');
		fclose($f);
		include_once '../../../tool/dbTool.inc.php';
		$conn=sqlconnect('../../../database/sale',$_GET['db'].'.db','','','','sqlite');
		sqlnoresponse($conn,$sql['sql'][1],'sqliteexec');
		sqlclose($conn,'sqlite');
	}
	else{
	}
}
?>