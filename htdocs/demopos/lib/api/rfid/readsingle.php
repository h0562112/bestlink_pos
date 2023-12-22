<?php
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);

if(isset($initsetting['db']['dbfile'])&&$initsetting['db']['dbfile']!=''){
	$pythonpath=substr($initsetting['db']['dbfile'],0,2);
}
else{
	$pythonpath='C:';
}

if(isset($initsetting['rfid']['timeout'])){
	$timeout=$initsetting['rfid']['timeout'];
}
else{
	$timeout='0.5';
}
if(isset($initsetting['rfid']['checktime'])){
	$checktime=$initsetting['rfid']['checktime'];
}
else{
	$checktime='5';
}
if(isset($initsetting['rfid']['com'])){
	$com=$initsetting['rfid']['com'];
}
else{
	$com='5';
}

$combinearray=array();
$matcharray=array();
$index=1;
$tokenarray=array();
if(file_exists($pythonpath.'//pos/rfidtype2.exe')){
	$string=exec($pythonpath.'//pos/rfidtype2.exe '.$com);

	if($string!=''){
		$temptokenarray=preg_split('/\,/',$string);

		array_push($tokenarray,$temptokenarray[1]);

		if(isset($_GET['debug'])){
			echo '<div style="width:100%;word-break: break-all;border:1px solid #ff0000;margin:10px 0;">讀取出的原始字串(連接埠=>COM'.$com.')：<br>'.$string.'</div>';
		}
		else{
		}

	}
	else{
	}
}
else{
}

if(isset($_GET['debug'])){
	print_r($tokenarray);
}
else{
	echo json_encode($tokenarray);
}
?>