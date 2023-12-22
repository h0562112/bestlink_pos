<?php
//header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Taipei');
$file="../../../ourpos/".$_GET['company']."/".$_GET['dep']."/doc/".$_GET['file']; // 實際檔案的路徑+檔名
$filename=date('YmdHi').".csv"; // 下載的檔名
header("Content-type: ".filetype($file));
header("Content-Disposition: attachment; filename=".$filename."");
//echo "\xEF\xBB\xBF";
readfile($file);
?>