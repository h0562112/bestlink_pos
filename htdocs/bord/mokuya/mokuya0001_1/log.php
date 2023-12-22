<?php
date_default_timezone_set('Asia/Taipei');
$content=fopen('./log.txt','a');
fwrite($content,date('Y/m/d H:i:s')."  type: ".$_POST['type'].";  content: ".$_POST['number']."\r\n");
fclose($content);
echo $_POST['number'];
?>