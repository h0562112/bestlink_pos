<?php
include '../../../tool/phpqrcode/qrlib.php'; 
// outputs image directly into browser, as PNG stream 
if(file_exists('../../../print/qrcode')){
}
else{
	mkdir('../../../print/qrcode',0777,true);
}
QRcode::png('https://s.intella.co/p8th1bcgqq1',false,'L',7,6);
//echo $_GET['id'];
?>