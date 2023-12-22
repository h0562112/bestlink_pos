<?php
//echo $_POST['qrcode'];
require_once "../../../../tool/scan2pay/Rsa.php";
if($_POST['callbackurl']!=''){//只有在手機點餐(顧客端)才會傳輸進來
	$intellasetup=parse_ini_file('../../../../management/menudata/'.$_POST['dep'].'/'.$_POST['machine'].'/intellasetup.ini',true);
}
else{
	$intellasetup=parse_ini_file('../../../../database/intellasetup.ini',true);
}
date_default_timezone_set($intellasetup['intella']['settime']);

$ApiKey='';
$Request='';

if(isset($intellasetup['intella']['qrcodetimeout'])&&$intellasetup['intella']['qrcodetimeout']!=''){
}
else{
	$intellasetup['intella']['qrcodetimeout']='5';
}
if(isset($_POST['callbackurl'])&&$_POST['callbackurl']!=''){
	//$callbackurl=$_POST['callbackurl'];
	$Data =json_encode( array(
		  "TimeExpire"=>date('YmdHis',strtotime(date('YmdHis').' +'.$intellasetup['intella']['qrcodetimeout'].' minute')),
		  "DeviceInfo"=>"skb0001",
		  "StoreOrderNo"=>$_POST['consecnumber'],
		  "Body"=>$intellasetup['intella']['itemname'],
		  "TotalFee"=>$_POST['total'],
		  "StoreInfo"=>$_POST['dep'],
		  "Cashier"=>$_POST['usercode'],
		  "CallBackUrl"=>$callbackurl
	  ));
}
else{
	//$callbackurl='';
	$Data =json_encode( array(
		  "TimeExpire"=>date('YmdHis',strtotime(date('YmdHis').' +'.$intellasetup['intella']['qrcodetimeout'].' minute')),
		  "DeviceInfo"=>"skb0001",
		  "StoreOrderNo"=>$_POST['consecnumber'],
		  "Body"=>$intellasetup['intella']['itemname'],
		  "TotalFee"=>$_POST['total'],
		  "StoreInfo"=>$_POST['dep'],
		  "Cashier"=>$_POST['usercode']
	  ));
}
/*$Data =json_encode( array(
	  "TimeExpire"=>date('YmdHis',strtotime(date('YmdHis').' +'.$intellasetup['intella']['qrcodetimeout'].' minute')),
	  "DeviceInfo"=>"skb0001",
	  "StoreOrderNo"=>$_POST['consecnumber'],
	  "Body"=>$intellasetup['intella']['itemname'],
	  "TotalFee"=>$_POST['total'],
	  "StoreInfo"=>$_POST['dep'],
	  "Cashier"=>$_POST['usercode'],
	  "CallBackUrl"=>$callbackurl
  ));*/

$Request_Json = array(
	"Header" => array(
		"Method" => "00000",
		"ServiceType"=>"OLPay",
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
$decrypted = json_decode($decrypted,true);

//echo strlen($decrypted['Data']['urlToken']);
if(isset($decrypted['Data']['urlToken'])&&strlen($decrypted['Data']['urlToken'])!=0){
	if(isset($_POST['machine'])){
		$qrcodename=$_POST['machine'].'.png';
	}
	else{
		$qrcodename='m1.png';
	}
	include_once '../../../../tool/phpqrcode/qrlib.php'; 
	if(isset($_POST['type'])&&$_POST['type']=='2'){//QRcode列印於暫出明細單
		if(file_exists('../../../../print/clientintellaqrcode')){
		}
		else{
			mkdir('../../../../print/clientintellaqrcode',0777,true);
		}
		// outputs image directly into browser, as PNG stream 
		QRcode::png($decrypted['Data']['urlToken'],'../../../../print/clientintellaqrcode/'.$qrcodename,'H',6);
	}
	else if(isset($_POST['type'])&&$_POST['type']=='1'){//QRcode顯示於客顯
		if(file_exists('../../../../print/intellaqrcode')){
		}
		else{
			mkdir('../../../../print/intellaqrcode',0777,true);
		}
		// outputs image directly into browser, as PNG stream 
		QRcode::png($decrypted['Data']['urlToken'],'../../../../print/intellaqrcode/'.$qrcodename,'H',6);
	}
	else if(isset($_POST['type'])&&$_POST['type']=='web'){//顧客手機點餐，直接電子支付，無須QRcode，直接使用連結跳轉
		$qrcodename=$decrypted['Data']['urlToken'];
	}
	else{
	}
	//echo $_GET['id'];
	echo $qrcodename;
}
else{
}
?>