<?php
$intellasetup=parse_ini_file('../../../../database/intellasetup.ini',true);
date_default_timezone_set($intellasetup['intella']['settime']);
$f=fopen('../../../../intella.txt','a');
fwrite($f,date('Y/m/d H:i:s ').$_POST['type'].' '.$intellasetup['intella'][$_POST['machine'].'deviceid'].' '.json_encode($_POST['response']).PHP_EOL);
fclose($f);
echo json_encode(array('log'));
?>