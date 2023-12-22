<?php
if(file_exists("../../../print/now.txt")){
	$file=fopen("../../../print/now.txt","r") or die("Unable to open file!");
	$now=fread($file,filesize("../../../print/now.txt"));
	fclose($file);
	echo $now;
}
else{
	echo '0';
}
?>