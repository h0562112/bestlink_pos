<?php
$init=parse_ini_file('../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
echo substr(date('Y/m/d H:i'),2);
?>