<?php
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(file_exists('../../dblockup/lock.txt')){
}
else{
	$lock=fopen('../../dblockup/lock.txt','w');
	fwrite($lock,date('Y/m/d H:i:s').PHP_EOL);
	fclose($lock);
}
?>