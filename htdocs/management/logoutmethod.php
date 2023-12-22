<?php
session_start();
if(isset($_SESSION['ID'])&&$_SESSION['ID']=='admin'){
	unset($_SESSION['ID']);
	unset($_SESSION['DB']);
	unset($_SESSION['name']);
	unset($_SESSION['company']);
	echo "<script>location.href='./index.php';</script>";
}
else if(isset($_SESSION['ID'])){
	unset($_SESSION['ID']);
	unset($_SESSION['DB']);
	unset($_SESSION['name']);
	unset($_SESSION['usergroup']);
	unset($_SESSION['company']);
	if(isset($_GET['company'])){
		echo "<script>location.href='./index.php?company=".$_GET['company']."';</script>";
	}
	else{
		echo "<script>location.href='./index.php';</script>";
	}
}
else{
	if(isset($_GET['company'])){
		echo "<script>location.href='./index.php?company=".$_GET['company']."';</script>";
	}
	else{
		echo "<script>location.href='./index.php';</script>";
	}
}
?>