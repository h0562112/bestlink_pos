<?php
if(file_exists('../print/intellaqrcode/'.$_POST['machine'].'.png')){
	echo 'exists';
}
else{
	echo 'notexitst';
}
?>