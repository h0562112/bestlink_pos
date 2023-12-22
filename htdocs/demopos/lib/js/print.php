<?php
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(isset($_POST['file'])&&$_POST['file']=='point-tree'){
	$header=fopen('../../../point-tree-log.txt','a');
}
else if(isset($_POST['file'])&&$_POST['file']=='posdvr'){
	$header=fopen('../../../posdvr-log.txt','a');
}
else if(isset($_POST['file'])&&$_POST['file']=='zdninvlog'){
	$header=fopen('../../../zdninvlog.txt','a');
}
else{
	$header=fopen('../../../printlog.txt','a');
}
if(isset($_POST['data'])){//¤¤¹©µo²¼LOG
	fwrite($header,date('Y/m/d H:i:s').' -- '.$_POST['html'].PHP_EOL);
	fwrite($header,print_r($_POST['data'],true).PHP_EOL);
}
else if(preg_match('/-PHP_EOL-/',$_POST['html'])){
	$temp=preg_split('/-PHP_EOL-/',$_POST['html']);
	for($i=0;$i<sizeof($temp);$i++){
		fwrite($header,date('Y/m/d H:i:s').' -- '.$temp[$i].PHP_EOL);
	}
}
else{
	fwrite($header,date('Y/m/d H:i:s').' -- '.$_POST['html'].PHP_EOL);
}
fclose($header);
?>