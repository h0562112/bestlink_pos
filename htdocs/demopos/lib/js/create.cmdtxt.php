<?php
include_once '../../../tool/myerrorlog.php';
if(preg_match('/-/',$_POST['cmd'])){
	$temp=preg_split('/-/',$_POST['cmd']);
	if($temp[1]=='change'){
		$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
		if(substr($machinedata['basic']['bizdate'],0,6)==$temp[0]){
			//$file=fopen('../../../print/noread/report.txt','w');
			//fwrite($file, $temp[0]);
		}
		else{
			$file=fopen('../../../print/noread/'.$temp[1].'.txt','w');
			fwrite($file, $temp[0]);
			fclose($file);
		}
	}
	else{
		$file=fopen('../../../print/noread/'.$temp[1].'.txt','w');
		fwrite($file, $temp[0]);
		fclose($file);
	}
}
else{
	$file=fopen('../../../print/noread/'.$_POST['cmd'].'.txt','w');
	fclose($file);
}
?>