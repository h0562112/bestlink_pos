<?php
if(isset($_GET['sendvar'])){
	$parameter=$_GET['sendvar'];
}
else if(isset($_POST['sendvar'])){
	$parameter=$_POST['sendvar'];
}
//echo $parameter;
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