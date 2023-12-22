<?php
function create($filename,$path1='../sql/',$path2='../../../database/sale/',$toolpath='../../../tool/'){
	if(file_exists($path1.$filename.'.ini')){
		$sql=parse_ini_file($path1.$filename.'.ini',true);
		$f=fopen($path2.$filename.'.db','w');
		fclose($f);
		include_once $toolpath.'dbTool.inc.php';
		$conn=sqlconnect($path2,$filename.'.db','','','','sqlite');
		sqlnoresponse($conn,$sql['sql'][1],'sqliteexec');
		sqlclose($conn,'sqlite');
	}
	else{
		return dirname(__FILE__);
	}
}
?>