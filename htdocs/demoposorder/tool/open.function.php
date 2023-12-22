<?php
$pw=parse_ini_file('./pw.ini',true);
if(isset($pw['pw'][3])&&$_POST['code']==$pw['pw'][3]){
	echo 'login';
}
else{
	echo 'error';
}
?>