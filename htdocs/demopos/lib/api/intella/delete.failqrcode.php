<?php
if(file_exists('../../../../print/intellaqrcode/'.$_POST['machine'].'.png')){
	unlink('../../../../print/intellaqrcode/'.$_POST['machine'].'.png');
}
else{
}
?>