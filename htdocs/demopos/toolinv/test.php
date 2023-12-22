<?php
$number='BB00001036';
$rand='1125';
$key='4CB5B9ECD85CFF45C71C51C6F0F3C53B';
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB); 
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
$temp=base64_encode(
    mcrypt_encrypt(
		MCRYPT_RIJNDAEL_128, 
        $key,
        $number.$rand,
        MCRYPT_MODE_ECB, 
        $iv
    )
);
echo strlen($temp)."<BR>";
echo '結果：'.$temp."<BR>";

$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB); 
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
$temp2=mcrypt_decrypt(
    MCRYPT_RIJNDAEL_128, 
    $key, 
    base64_decode('lgzPooMH+JaF/4uSfu8j6w=='), 
    MCRYPT_MODE_ECB, 
    $iv
);
echo strlen($temp2).'<BR>';
echo '結果：'.$temp2;

include 'encrypt.php';
use killworm737\qrcode\Qrcode;

$qrcodeClass = new Qrcode();

$aesKey = $key;// input your aeskey
$invoiceNumAndRandomCode = $number.$rand;// input your invoiceNumber And RandomCode
$qrcodeClass->aes128_cbc_encrypt($aesKey, $invoiceNumAndRandomCode);

echo '<BR>結果:'.$qrcodeClass->aes128_cbc_encrypt($aesKey, $invoiceNumAndRandomCode).'<BR>';
?>