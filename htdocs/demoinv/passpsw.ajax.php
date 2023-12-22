<?php
$psw=parse_ini_file('./key.ini',true);

if(md5($_POST['psw'])==$psw['basic']['psw']){
	echo 'pass';
}
else{
	echo 'fail';
}
?>