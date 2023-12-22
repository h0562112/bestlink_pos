<?php
$handle = fopen("../../../print/now.txt", "r");
$parameter = fgets($handle);
fclose($handle);
$parameter=intval($parameter)+1;

$handle = fopen("../../../print/now.txt", "w");
fwrite($handle, $parameter);
fclose($handle);
echo $parameter;
?>