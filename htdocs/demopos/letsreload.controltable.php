<?php
//2020/10/22 可能會轉由nodejs觸發
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