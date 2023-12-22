<?php
$myfile = fopen("./now.txt", "r") or die("Unable to open file!");
$now=fread($myfile,filesize("./now.txt"));
fclose($myfile);
echo $now;
?>