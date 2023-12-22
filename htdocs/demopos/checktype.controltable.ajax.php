<?php
//2020/10/22 可能轉由nodejs觸發
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