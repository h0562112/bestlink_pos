<?php
//2020/10/22 �i�����nodejsĲ�o
if(file_exists('./table/tablereload/reload.txt')){
	echo 'reload';
}
else{
	if(file_exists('./table/tablereload')){
	}
	else{
		mkdir('./table/tablereload');
	}
}
?>