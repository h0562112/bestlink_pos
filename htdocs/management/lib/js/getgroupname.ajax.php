<?php
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini')){
	$temp=preg_split('/_/',$_POST['oldpt']);
	$kds=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini',true);
	if(isset($kds[$temp[0]]['name'][$temp[1]])){
		echo $kds[$temp[0]]['name'][$temp[1]];
	}
	else{
	}
}
else{
}
?>