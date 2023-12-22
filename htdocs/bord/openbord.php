<title>遠端多媒體看板</title>
<?php
$company=$_GET['a'];
$story=$_GET['b'];
$content=parse_ini_file("./".$company.'/'.$story.'/data/content.ini',true);
echo "<script>";
preg_match('/\d+/',$content['bord']['type'],$temp);
echo "location.href='./".$company."/".$story."/index".strtolower(substr($content['bord']['type'],0,1)).$temp[0].".php';";
echo "</script>";
?>