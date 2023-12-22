<?php
//$_POST['response']=json_decode($_POST['response'],true);
require_once "../../../../tool/scan2pay/Rsa.php";
$intellasetup=parse_ini_file('../../../../database/intellasetup.ini',true);
date_default_timezone_set($intellasetup['intella']['settime']);

$ApiKey='';
$Request='';

if($_POST['response']['Header']['ServiceType']=='Payment'){//扣款
	$Data =json_encode( array(
		"DeviceId"=>$_POST['response']['Data']['request']['DeviceID'],
		"Retry"=>$_POST['response']['Data']['Retry'],
		"Amount"=>$_POST['total'],
		"StoreOrderNo"=>$_POST['intellaconsecnumber'],
		"TerminalTXNNumber"=>$_POST['response']['Data']['request']['TerminalTXNNumber'],
		"HostSerialNumber"=>$_POST['response']['Data']['request']['HostSerialNumber']
	));
}
else if($_POST['response']['Header']['ServiceType']=='Refund'){//退款
	$Data =json_encode( array(
		"DeviceId"=>$_POST['response']['Data']['request']['DeviceID'],
		"Retry"=>$_POST['response']['Data']['Retry'],
		"Amount"=>$_POST['response']['Data']['request']['Amount'],
		"StoreOrderNo"=>$_POST['response']['Data']['OrderId'],
		"TerminalTXNNumber"=>$_POST['response']['Data']['request']['TerminalTXNNumber'],
		"HostSerialNumber"=>$_POST['response']['Data']['request']['HostSerialNumber'],
		"RefundKey"=>hash('sha256',$intellasetup['intella']['refundkey'])
	));
}
else if($_POST['response']['Header']['ServiceType']=='SignOn'){//登入
	$Data =json_encode( array(
		"DeviceId"=>$intellasetup['intella'][$_POST['machine'].'deviceid'],
		"Retry"=>$_POST['response']['Data']['Retry']
	));
}
else if($_POST['response']['Header']['ServiceType']=='BalanceQuery'){//餘額
	$Data =json_encode( array(
		"DeviceId"=>$intellasetup['intella'][$_POST['machine'].'deviceid'],
		"Retry"=>$_POST['response']['Data']['Retry']
	));
}

$Request_Json = array(
	"Header" => array(
		"Method"=>"31800",
		"ServiceType"=>$_POST['response']['Header']['ServiceType'],
		"MchId"=>$_POST['response']['Header']['MchId'],
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
$Response = json_decode($Result);
//print_r($Response);
$enc = $Response->Response;
$decrypted = $cbc->decrypt($enc);//解密结果
echo json_encode($decrypted);

?>