<?php
$file=fopen('./error/tempdb.txt','w');
fwrite($file,$_POST['html']);
fclose($file);
?>