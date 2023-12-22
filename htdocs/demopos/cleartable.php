<?php
include_once '../tool/myerrorlog.php';
$initsetting=parse_ini_file('../database/initsetting.ini',true);
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}
$dir='./table';
$filelist=scandir($dir);
for($i=2;$i<sizeof($filelist);$i++){
	if($filelist[$i]=='outside'){
	}
	else{
		$temp=preg_split('/;/',$filelist[$i]);
		if(!preg_match('/'.$temp[2].'/',iconv('utf-8','big5',$_POST['tabnum']).'.ini')){
		}
		else if(intval($temp[0])<intval($timeini['time']['bizdate'])){
			unlink('./table/'.$filelist[$i]);
		}
		else if(intval($temp[1])<intval($timeini['time']['zcounter'])){
			unlink('./table/'.$filelist[$i]);
		}
		else{
			break;
		}
	}
}
if(preg_match('/,/',$_POST['tabnum'])){
	$temp=preg_split('/,/',$_POST['tabnum']);
	for($i=0;$i<sizeof($temp);$i++){
		if(file_exists('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$temp[$i]).'.ini')){
			unlink('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$temp[$i]).'.ini');
		}
		else{
		}
	}
}
else{
	if(file_exists('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$_POST['tabnum']).'.ini')){
		unlink('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$_POST['tabnum']).'.ini');
	}
	else{
	}
}
?>