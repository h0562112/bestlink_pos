<?php
//2020/10/22 �i��|���nodejsĲ�o
if(file_exists('./table/tablereload/reload.txt')){
}
else{
	if(file_exists('./table/tablereload')){
	}
	else{
		mkdir('./table/tablereload');
	}
	$f=fopen('./table/tablereload/reload.txt','w');
	fclose($f);
}
echo 'ok';
?>