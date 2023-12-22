<?php
if(file_exists('../../serviceitems/'.$_POST['tablenumber'].'.ini')){
	echo 'exists';
}
else{
	echo 'empty';
}
?>