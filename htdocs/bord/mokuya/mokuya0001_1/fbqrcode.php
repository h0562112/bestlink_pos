<?php
include '../../../tool/phpqrcode/qrlib.php';
define('IMAGE_WIDTH',170);
define('IMAGE_HEIGHT',170);
// outputs image directly into browser, as PNG stream 
if(isset($_GET['id'])&&strlen($_GET['id'])>0){
	QRcode::png('fb://page/'.$_GET['id'],'fbcode.jpg');
	echo "<style>
			body{
				margin:0;
			}
		</style>";
	echo "<img src='fbcode.jpg' style='width:170px;height:170px;margin:0;float:left;'>";
}
else{
}
?>