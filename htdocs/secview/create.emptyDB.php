<?php
function create($filename){
	if(file_exists('../demopos/lib/sql/'.$filename.'.ini')){
		$sql=parse_ini_file('../demopos/lib/sql/'.$filename.'.ini',true);
		$f=fopen('../database/sale/'.$filename.'.db','w');
		fclose($f);
		include_once '../tool/dbTool.inc.php';
		$conn=sqlconnect('../database/sale',$filename.'.db','','','','sqlite');
		sqlnoresponse($conn,$sql['sql'][1],'sqliteexec');
		sqlclose($conn,'sqlite');
	}
	else{
	}
}
?>