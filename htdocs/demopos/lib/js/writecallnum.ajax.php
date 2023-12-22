<?php
$file=fopen('../../../print/callnumber/callnumber.txt','w');
fwrite($file, $_POST['num']);
fclose($file);
$file=fopen('../../../print/now.txt','w');
fwrite($file, $_POST['num']);
fclose($file);
$file=fopen('../../../print/noread/callnumber.txt','w');
fwrite($file, $_POST['num']);
fclose($file);
?>