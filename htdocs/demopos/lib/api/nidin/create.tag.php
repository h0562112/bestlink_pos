<?php
//2022/5/30 原先有寫在main.js中，但是被註解，忘記當初用途，先保留(最早有開一版面只為了處理nidin訂單，沒有跟POS串接，可能用在這邊)
require_once("../../tool/TCPDF/examples/tcpdf_include.php");

$list=$_POST['getlistarray'];
?>