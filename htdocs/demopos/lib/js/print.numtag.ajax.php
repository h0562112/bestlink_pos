<?php
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/inilib.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
$setup=parse_ini_file('../../../database/setup.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$number=parse_ini_file('../../now.ini',true);
$number['now']['n']=intval($number['now']['n'])+1;
write_ini_file($number,'../../now.ini');
//$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$PHPWord = new PHPWord();
$document2 = $PHPWord->loadTemplate('../../../template/numbertag.docx');
$document2->setValue('story',$setup['basic']['storyname']);
$document2->setValue('datetime',date('Y/m/d H:m:i'));
$document2->setValue('type','排隊 '.$number['now']['n'].' 號');
$filename=date("YmdHis");
//$document2->save("../../../print/noread/number_".$filename.".docx");
$document2->save("../../../print/read/number_".$filename.".docx");
$prt=fopen("../../../print/noread/number_".$filename.".prt",'w');
fclose($prt);
?>