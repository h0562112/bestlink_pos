<?php
if(file_exists('../../../print/zdninv')){
	if(file_exists('../../../print/zdninv/add1.txt')){
		unlink('../../../print/zdninv/add1.txt');
		echo 'success';
	}
	else{
		echo 'file not exists';
	}
}
else{
	echo 'dir not exists';
}
?>