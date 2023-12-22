<?php
if(is_dir('../print')){
}
else{
	mkdir('../print');
}
$file=fopen('../print/now.txt','w');
fwrite($file, $_POST['num']);
fclose($file);

if(is_dir('../print/noread')){
}
else{
	mkdir('../print/noread');
}
$file=fopen('../print/noread/callnumber.txt','w');
fwrite($file, $_POST['num']);
fclose($file);

if(is_dir('../print/callnumber')){
}
else{
	mkdir('../print/callnumber');
}
$file=fopen('../print/callnumber/callnumber.txt','w');
fwrite($file, $_POST['num']);
fclose($file);
?>