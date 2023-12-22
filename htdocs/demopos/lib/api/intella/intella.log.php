<?php
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
if(isset($initsetting['init']['intellalog'])&&$initsetting['init']['intellalog']=='1'){
	date_default_timezone_set('Asia/Taipei');
	$res=$_POST['res'];
	unset($_POST['res']);
	$function=$_POST['function'];
	unset($_POST['function']);
	$para=$_POST;
	$file='./data/check.intellapay.txt';
	$f=fopen($file,'a');
	fwrite($f,date('Y/m/d H:i:s').' Function=>'.print_r($function,true).PHP_EOL);
	fwrite($f,'                    Parameters=>'.print_r($para,true).PHP_EOL);
	fwrite($f,'                    Request=>'.print_r($res,true).PHP_EOL.PHP_EOL);
	fclose($f);
}
else{
}
?>