<?php
$filename=$_GET['f'];
$content=parse_ini_file('../database/setup.ini',true);
header("Content-type: text/xml; charset=utf-8");
header("Content-Disposition: in-line; filename=".$filename);
readfile('./'.$content['basic']['company'].'/'.$content['basic']['story'].'/'.$filename);
?>