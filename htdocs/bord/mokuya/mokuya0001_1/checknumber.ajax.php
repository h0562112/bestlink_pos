<?php
if(file_exists('./callnumber/callnumber.ini')){
	$f=parse_ini_file('./callnumber/callnumber.ini',true);
	if($_POST['datetag']!=$f['data']['time']){
		$t=$f['data']['num'];
		echo $f['data']['time'];
		$f=fopen('./now.txt','w');
		fwrite($f,$t);
		fclose($f);
	}
	else{
		if(file_exists('./callnumber/callnumber.txt')){
			include_once '../../tool/inilib.php';
			$f=fopen('./callnumber/callnumber.txt','r');
			$t=fgets($f);
			fclose($f);
			unlink('./callnumber/callnumber.txt');
			$f=fopen('./now.txt','w');
			fwrite($f,$t);
			fclose($f);
			$f=parse_ini_file('./callnumber/callnumber.ini',true);
			$f['data']['num']=$t;
			$f['data']['time']=date('YmdHis');
			write_ini_file($f,'./callnumber/callnumber.ini',true);
			echo $f['data']['time'];
		}
		else{
			echo 'error';
		}
	}
}
else if(file_exists('./callnumber/callnumber.txt')){
	$f=fopen('./callnumber/callnumber.txt','r');
	$t=fgets($f);
	fclose($f);
	unlink('./callnumber/callnumber.txt');
	$f=fopen('./now.txt','w');
	fwrite($f,$t);
	fclose($f);
}
else{
	echo 'error';
}
?>