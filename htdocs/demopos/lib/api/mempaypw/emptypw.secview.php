<?php
include_once '../../../../tool/inilib.php';

if(file_exists('./data/'.$_POST['machine'].'.ini')){
	$pwstring=parse_ini_file('./data/'.$_POST['machine'].'.ini',true);
	//print_r($pwstring);
	$pwstring['basic']['pw'] = '';
	write_ini_file($pwstring,'./data/'.$_POST['machine'].'.ini');
}
else{
	$f=fopen('./data/'.$_POST['machine'].'.ini','w');
	fwrite($f,'[basic]'.PHP_EOL);
	fwrite($f,'pw=""'.PHP_EOL);
	fclose($f);
}
?>