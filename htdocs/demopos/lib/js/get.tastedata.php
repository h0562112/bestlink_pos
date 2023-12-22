<?php
$taste=parse_ini_file('../../../database/'.$_POST['company'].'-taste.ini',true);

echo json_encode($taste[$_POST['tasteno']]);
?>