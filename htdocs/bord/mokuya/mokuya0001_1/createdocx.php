<?php
	$myfile = fopen("max.txt", "r") or die("Unable to open file!");
	$nownumber=fread($myfile,filesize("max.txt"));
	fclose($myfile);
	$nownumber++;
	$handle = fopen("max.txt", "w");
	fwrite($handle, $nownumber);
	fclose($handle);
	require_once '../PHPWord.php';
	$PHPWord = new PHPWord();
	$document = $PHPWord->loadTemplate('../template.docx');
	include '../../../qrcode/phpqrcode/qrlib.php';
	$content=parse_ini_file('./data/content.ini',true);
	QRcode::png('http://www.quickcode.com.tw/bord/'.$content['initial']['company'].'/searchnumber.php?num='.$nownumber.'&a='.$content['initial']['company'].'&b='.$content['initial']['story'].'&c='.$content['fbid']['id'].'&ch='.$content['channel']['name'],'qrcode'.$nownumber.'.jpg');
	date_default_timezone_set('Asia/Taipei');
	$arrImagenes = array('qrcode'.$nownumber.'.jpg');
	$document->replaceStrToImg('qrcode', $arrImagenes);
	$document->setValue('time', date("Y/m/d H:i:s"));
	$document->setValue('s', '您的號碼');
	$document->setValue('num', $nownumber);
	$document->save("../../noread/".$_POST['a']."_".$_POST['b']."-".$nownumber.".docx");
	echo $nownumber;
	echo "<script>window.close();</script>";
?>