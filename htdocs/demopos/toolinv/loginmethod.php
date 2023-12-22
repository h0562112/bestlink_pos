<?php
session_start();
$_SESSION['rfidname']=$_GET['id'];
echo '<script>alert("loginmethod");</script>';
?>