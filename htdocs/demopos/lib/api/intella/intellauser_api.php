<?php
//echo $_POST['qrcode'];
require_once "../../../../tool/scan2pay/Rsa.php";
$intellasetup=parse_ini_file('../../../../database/intellasetup.ini',true);
$methodmap=parse_ini_file('./data/methodmap.ini',true);
date_default_timezone_set($intellasetup['intella']['settime']);

$ApiKey='';
$Request='';

if(isset($intellasetup['intella']['qrcodetimeout'])&&$intellasetup['intella']['qrcodetimeout']!=''){
}
else{
	$intellasetup['intella']['qrcodetimeout']='5';
}
$Data =json_encode( array(
	  "DeviceInfo"=>"skb0001",
	  "StoreOrderNo"=>$_POST['consecnumber'],
	  "Body"=>$intellasetup['intella']['itemname'],
	  "TotalFee"=>$_POST['total'],
	  "StoreInfo"=>$_POST['dep'],
	  "Cashier"=>$_POST['usercode'],
	  "AuthCode"=>$_POST['authcode']
  ));

$Request_Json = array(
	"Header" => array(
		"Method" => "00000",
		"ServiceType"=>"Micropay",
		"MchId"=>$intellasetup['intella']['mchid'],
		"TradeKey"=>hash('sha256',$intellasetup['intella']['tradekey']),
		"CreateTime"=>date('YmdHis')
	),
	"Data" =>$Data
);

$Request = json_encode($Request_Json);

require_once "../../../../tool/scan2pay/keyforintella/Crypt.php";
$key = 'Y3UJ147HKIYRT8Ovrsik0A==';
$iv = $intellasetup['intella']['iv'];
$cbc = new Crypt(base64_decode($key), MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC,$iv);//AES
$Request = $cbc->encrypt($Request);//$Request 加密结果

$pub_key = openssl_pkey_get_public(file_get_contents('../../../../tool/scan2pay/keyforintella/'.$intellasetup['intella']['pubkey']));
//$pub_key = openssl_pkey_get_public(file_get_contents('../../../../tool/scan2pay/keyforintella/stage-public.pem'));//測試public key
$keyData = openssl_pkey_get_details($pub_key);

$rsa = new Rsa();
$rsa->publicKey = $keyData['key'];
$ApiKey = $rsa->publicEncrypt($key, $rsa->publicKey);
$ApiKey = base64_encode($ApiKey);

$PostData = json_encode( array(
	"Request" =>$Request,
	"ApiKey"=>$ApiKey
	)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $intellasetup['intella']['url']);
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
$Response = json_decode($Result,true);
//print_r($Response);
$enc = $Response['Response'];
$decrypted = $cbc->decrypt($enc);//解密结果
$temp=json_decode($decrypted,true);
if(isset($temp['Header']['Method'])&&isset($methodmap['map'][$temp['Header']['Method']])){
	$temp['Header']['MethodName']=$methodmap['map'][$temp['Header']['Method']];
}
else if(isset($temp['Header']['Method'])){
	$temp['Header']['MethodName']='';
}
else{
	$temp['Header']['MethodName']='';
}

echo json_encode($temp);
?>