<?php
$csv = fopen('../../../database/love.csv','r');
//echo '/,('.$_POST['code'].',)/';
while( !feof($csv) ){
	$str=fgets($csv);
	if(preg_match('/,('.$_POST['code'].',)/',$str)){
		echo $str;
		break;
	}
	else{
	}
}
?>