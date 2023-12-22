<?php
$handle = fopen("now.txt", "r");
$parameter = fgets($handle);
fclose($handle);
$parameter=intval($parameter)+1;

$handle = fopen("now.txt", "w");
fwrite($handle, $parameter);
fclose($handle);
echo $parameter;
?>