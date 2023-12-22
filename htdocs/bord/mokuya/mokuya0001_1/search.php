<?php
include '../../../tool/phpqrcode/qrlib.php';
define('IMAGE_WIDTH',150);
define('IMAGE_HEIGHT',150);
// outputs image directly into browser, as PNG stream 
if(isset($_GET['company'])&&strlen($_GET['company'])>0){
	QRcode::png('http://www.quickcode.com.tw/bord/'.$_GET['company'].'/'.$_GET['dep'].'/searchnumber.php','search.jpg');
	echo "<style>
			body{
				margin:0;
			}
		</style>";
	echo "<img src='search.jpg' style='width:112px;height:112px;margin:0;float:left;'>";
}
else{
}
?>