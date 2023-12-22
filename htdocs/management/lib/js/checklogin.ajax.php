<?php
session_start();
if(isset($_SESSION['ID'])){
	echo 'login';
}
else{
	echo 'logout';
}
?>