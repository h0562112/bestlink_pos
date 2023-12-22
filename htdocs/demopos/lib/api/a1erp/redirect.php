<?php
date_default_timezone_set('Asia/Taipei');
include_once '../../../../tool/inilib.php';
echo '體系代號：'.$_GET['story'];
echo '<BR>';
echo '門市代號：'.$_GET['dep'];
echo '<BR>';
echo 'API Key：'.$_GET['APIKey'];
echo '<BR>';
echo 'API Password：'.$_GET['Password'];
echo '<BR>';
if(isset($_GET['story'])&&$_GET['story']!=''&&(isset($_GET['dep'])&&$_GET['dep']!='')){
	$setup=parse_ini_file('../../../../menudata/'.$_GET['story'].'/'.$_GET['dep'].'/setup.ini',true);
	echo '<BR>';
	if(isset($_GET['APIKey'])&&$_GET['APIKey']!=''&&(isset($_GET['Password'])&&$_GET['Password']!='')){
		$setup['a1erp']['id']=$_GET['APIKey'];
		$setup['a1erp']['pw']=$_GET['Password'];
	}

	//print_r($setup);
	write_ini_file($setup,'../../../../menudata/'.$_GET['story'].'/'.$_GET['dep'].'/setup.ini');
}

echo '<h1 style="color:#ff0000;">請重整後台頁面。</h1>';
?>