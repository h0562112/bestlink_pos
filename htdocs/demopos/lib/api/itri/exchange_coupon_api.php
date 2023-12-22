<?php
$itrisetup=parse_ini_file('../../../../database/itrisetup.ini',true);
date_default_timezone_set('Asia/Taipei');

$PostData = json_encode(array(
	"StoreApiKey" => $itrisetup['itri']['storeapikey'],
	//"CouponApiKey" => $itrisetup['itri']['couponapikey'],
	"CouponSN" => $_POST['couponsn']
));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $itrisetup['itri']['url'].'Coupon_Exchange_CouponCert');//
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
}
curl_close($ch);
//print_r($Result);
//$Response = json_decode($Result);
//print_r($Response);
echo $Result;

?>