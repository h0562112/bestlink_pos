<!doctype html>
<?php
include_once '../tool/dbTool.inc.php';
include_once 'sidebar.funtion.php';
include_once 'content.function.php';
session_start();
if(isset($_POST['DB'])){
	$_SESSION['DB']=$_POST['DB'];
	if(isset($_POST['startdate'])){
		$_SESSION['startdate']=$_POST['startdate'];
	}
	else{
	}
	if(isset($_POST['enddate'])){
		$_SESSION['enddate']=$_POST['enddate'];
	}
	else{
	}
}
else{
	if(isset($_POST['startdate'])){
		$_SESSION['startdate']=$_POST['startdate'];
	}
	else{
	}
	if(isset($_POST['enddate'])){
		$_SESSION['enddate']=$_POST['enddate'];
	}
	else{
	}
}
if(!isset($_SESSION['ID'])||$_SESSION['ID']==""){
	echo "<script>location.href='../view/index.php';</script>";
}
else{
	if(isset($_POST['startdate'])){
		$_SESSION['startdate']=$_POST['startdate'];
	}
	else if(isset($_POST['enddate'])){
		$_SESSION['enddate']=$_POST['enddate'];
	}
	else{
		$_SESSION['startdate']=date("Y-m-01");
		$_SESSION['enddate']=date("Y-m-t");
	}
	$ID=$_SESSION['ID'];
	$usergroup=$_SESSION['usergroup'];
	$DB=$_SESSION['DB'];
	$company=$_SESSION['company'];
	if(isset($_SESSION['startdate'])||isset($_SESSION['enddate'])){
		$startdate=$_SESSION['startdate'];
		$enddate=$_SESSION['enddate'];
	}
	else{
		$startdate=date("Y-m-01");
		$enddate=date("Y-m-t");
	}
}
$defcolor='#ffffff';//非正在瀏覽之選項底色
$clicolor='#33ccff';//正在瀏覽之選項底色
$ovecolor='#ffcc00';//滑鼠在上之選項底色

$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>點餐機後台管理</title>
	<script src='http://code.jquery.com/jquery-1.12.2.js'></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<style>
	li input {
		padding:0;
		margin:0;
		width:100%;
		height:100%;
		text-align:left;
		border:0 #ffffff solid;
		background-color:#ffffff;
	}
	</style>
</head>
<body>
	<div id="" class="main">
		<div id="" class="header">
			<div id="logo" class="">
				
			</div>
			<div id="menu" class="">
				
			</div>
		</div>
		<div id="" class="body">
			<div id="sidebar" class="">
				<?php sidebar('boss'); ?>
			</div>
			<div id="content" class="">
				<?php content($ID,$company,$DB,$usergroup,$startdate,$enddate); ?>
			</div>
		</div>
		<div id="footer" class="">
			
		</div>
	</div>
</body>
</html>
