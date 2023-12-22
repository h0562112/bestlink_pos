<?php
if(file_exists('../print/intellaqrcode/'.$_POST['machine'].'.png')){
	echo json_encode([['state'=>'success','time'=>date('YmdHis')]]);
}
else{
	echo json_encode([['state'=>'fail']]);
}
?>