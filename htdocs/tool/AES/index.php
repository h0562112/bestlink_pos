<?php
include_once 'lib.php';
include_once '../phpqrcode/qrlib.php';

$qrcodeClass = new encryQrcode();

$aesKey = "1905B9C0E27FB708712E42CED49178AB";// input your aeskey
$invoiceNumAndRandomCode = "BB123456785971";// input your invoiceNumber And RandomCode
$encry=$qrcodeClass->aes128_cbc_encrypt($aesKey, $invoiceNumAndRandomCode);

QRcode::png("BB12345678110012059710000000000004E200000000087216652".$qrcodeClass->aes128_cbc_encrypt($aesKey, $invoiceNumAndRandomCode).":**********:1:1:1::1:20000:", "./testinv.png", "L", "4", 2);
?>
