<?php
if(isset($_GET['num'])){
	$parameter=$_GET['num'];
}
else if(isset($_POST['num'])){
	$parameter=$_POST['num'];
}
while(strlen($parameter)<3){
	$parameter='0'.$parameter;
}
if(strlen($parameter)>3){
	$parameter=substr($parameter,strlen($parameter)-3,3);
}
else{
}
$handle = fopen("now.txt", "w");
fwrite($handle, $parameter);
fclose($handle);
?>