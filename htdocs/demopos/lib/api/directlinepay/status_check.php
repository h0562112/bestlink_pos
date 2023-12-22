<?php
$setup=parse_ini_file('../../../../database/setup.ini',true);
$tradecode=parse_ini_file('./tradecode.ini',true);

$header=array(
	'Content-Type: application/json; charset=UTF-8',
	'X-LINE-ChannelId: '.$setup['directlinepay']['channelid'],
	'X-LINE-MerchantDeviceType: POS',
	'X-LINE-MerchantDeviceProfileId: POS',
	'X-LINE-ChannelSecret: '.$setup['directlinepay']['channelsecret']
);

//$PostData = json_encode( array(
//		"productName" =>"pos line pay",
//		"amount"=>$_POST['money'],
//		"currency"=>"TWD",
//		"orderId"=>$_POST['dep'].date('YmdHis'),
//		"oneTimeKey"=>$_POST['code']
//	)
//);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $setup['directlinepay']['url'].'/orders/'.urlencode($_POST['orderid']).'/check');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
//curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
}
curl_close($ch);
$Response = json_decode($Result,1);

if(isset($Response['returnCode'])){
	if(isset($tradecode['code'][$Response['returnCode']])){
		$Response['returnMessageC']=$tradecode['code'][$Response['returnCode']];
	}
	else{
		$Response['returnMessageC']='จาฅ~ชฌชp';
	}
	if(isset($Response['info']['failReturnCode'])&&isset($tradecode['code'][$Response['info']['failReturnCode']])){
		$Response['failReturnMessageC']=$tradecode['code'][$Response['info']['failReturnCode']];
	}
	else{
	}
}
else{
}

$y=date('Y');
$m=date('m');
$d=date('d');
if(!file_exists('./log/'.$y.'/'.$m.'/'.$d)){
	if(!file_exists('./log/'.$y.'/'.$m)){
		if(!file_exists('./log/'.$y)){
			if(!file_exists('./log')){
				mkdir('./log');
			}
			else{
			}
			mkdir('./log/'.$y);
		}
		else{
		}
		mkdir('./log/'.$y.'/'.$m);
	}
	else{
	}
	mkdir('./log/'.$y.'/'.$m.'/'.$d);
}
else{
}
$f=fopen('./log/'.$y.'/'.$m.'/'.$d.'/linepay.log','a');
fwrite($f,date('Y/m/d H:i:s').' -- channelid= '.$setup['directlinepay']['channelid'].PHP_EOL);
fwrite($f,'                    -- channelsecret= '.$setup['directlinepay']['channelsecret'].PHP_EOL);
fwrite($f,'                    -- orderid= '.$_POST['orderid'].PHP_EOL);
fwrite($f,'                    -- url path= '.$setup['directlinepay']['url'].'/orders/'.urlencode($_POST['orderid']).'/check'.PHP_EOL);
fwrite($f,'                    -- get result= '.print_r($Response,true).PHP_EOL);
fclose($f);

echo json_encode($Response);

?>