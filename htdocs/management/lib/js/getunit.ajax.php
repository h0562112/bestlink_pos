<?php
$itemname=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini',true);
$unit=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/unit.ini',true);
echo $unit['unit'][$itemname[$_POST['itemno']]['unit']];
?>