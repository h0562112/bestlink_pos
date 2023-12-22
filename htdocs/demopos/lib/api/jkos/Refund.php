<?php
include_once './Sign.php';
//$setup=parse_ini_file('../../../../database/setup.ini',true);

$data=array(
	'MerchantID' => $_POST['MerchantID'],
	'StoreID' => $_POST['StoreID'],
	'StoreName' => $_POST['StoreName'],
	'GatewayTradeNo' => '',
	'MerchantTradeNo' => $_POST['MerchantTradeNo'],
	'PosID' => 'm1',
	'PosTradeTime' => date('Y/m/d H:i:s'),
	'TradeNo' => $_POST['TradeNo'],
	'TradeAmount' => intval($_POST['TradeAmount']),
	'Remark' => '',
	'Extra1' => '',
	'Extra2' => '',
	'Extra3' => '',
	'SendTime' => date('YmdHis')
);

ksort($data);//2022/9/26 °w¹ïarray key±Æ§Ç

$data['Sign']=Sign($data,$_POST['MerchantKey']);

$header=array(
	'Content-Type: application/json; charset=UTF-8'
);
if(!file_exists('./log/'.date('Y').'/'.date('m').'/'.date('d'))){
	if(!file_exists('./log/'.date('Y').'/'.date('m'))){
		if(!file_exists('./log/'.date('Y'))){
			if(!file_exists('./log')){
				mkdir('./log');
			}
			else{
			}
			mkdir('./log/'.date('Y'));
		}
		else{
		}
		mkdir('./log/'.date('Y').'/'.date('m'));
	}
	else{
	}
	mkdir('./log/'.date('Y').'/'.date('m').'/'.date('d'));
}
else{
}
$f=fopen('./log/'.date('Y').'/'.date('m').'/'.date('d').'/jkos.log','a');
fwrite($f,date('Y/m/d H:i:s').' --- url: '.$_POST['url'].'/'.$_POST['SystemName'].'/Refund'.PHP_EOL);
fwrite($f,'                    --- header: '.print_r($header,true).PHP_EOL);
fwrite($f,'                    --- data: '.print_r($data,true).PHP_EOL);
fwrite($f,'                    --- use key: '.$_POST['MerchantKey'].PHP_EOL);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_POST['url'].'/'.$_POST['SystemName'].'/Refund');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POSTFIELDS, jkos_json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
curl_setopt($ch, CURLOPT_TIMEOUT, 15); //timeout in seconds
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	print_r('cURL error when connecting to ' . $_POST['url'].'/'.$_POST['SystemName'].'/Refund' . ': ' . curl_error($ch));
}
curl_close($ch);
$Response = json_decode($Result,1);

fwrite($f,'                    --- pass string: '.jkos_json_encode($data).PHP_EOL);
fwrite($f,'                    --- init result: '.$Result.PHP_EOL);
fwrite($f,'                    --- decode result: '.print_r($Response,true).PHP_EOL);
fclose($f);
echo json_encode($Response);
?>