<?php
function php_curl_ajax($method,$url,$header,$postdata){
	if($method=='get'){//2021/4/13 該API無需使用該流程
		if(sizeof($postdata)>0){
			$url .= '?';
			foreach($postdata as $name=>$value){
				$url .= $name.'='.$value.'&';
			}
		}
		else{
		}
	}
	else{
	}
	$ch = curl_init();
	//echo '<br>url='.$url.'<br>';
	curl_setopt($ch, CURLOPT_URL, $url);//
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	if($method=='post'){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);//2022/6/9 因為他們要接收xml字串(keeper erp 使用)
	}
	else if($method=='put'){
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
	}
	else{
	}
	// Edit: prior variable $postFields should be $postfields;
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$tempdata = curl_exec($ch);
	$result=json_decode($tempdata,true);
	curl_close($ch);
	//$res[]=$tempdata;
	//$res[]=$result;
	return $tempdata;
}

function Get_Token($url,$method,$id,$pw){
	date_default_timezone_set('Asia/Taipei');
	$header = array('Content-Type: application/json');

	$postdata = array(
		"client_id" => $id,
		"client_secret" => $pw,
		"grant_type" => "client_credentials"
	);
	
	$res=php_curl_ajax($method,$url.'/api/Auth/GetToken',$header,$postdata);
	
	$result=json_decode($res,true);
	if($result['valid']=='true'&&$result['value']['result']==true&&$result['value']['resultCode']=='000'){
	}
	else{
		$f=fopen('./log.log','a');
		fwrite($f,date('Y/m/d H:i:s').' --- Get_Token'.PHP_EOL);
		fwrite($f,'                    --- url= '.$url.'/api/Auth/GetToken'.PHP_EOL);
		fwrite($f,'                    --- method= '.$method.PHP_EOL);
		fwrite($f,'                    --- header= '.print_r($header,true).PHP_EOL);
		fwrite($f,'                    --- request= '.json_encode($postdata).PHP_EOL);
		fwrite($f,'                    --- response= '.$res.PHP_EOL);
		fclose($f);
	}

	return $res;
}

function CheckPhone($url,$method,$token,$account){
	date_default_timezone_set('Asia/Taipei');
	$header = array(
		'Authorization: Bearer '.$token,
		'Content-Type: application/json'
	);

	$postdata = array(
		"account" => $account
	);
	
	$res=php_curl_ajax($method,$url.'/api/Wallet/CheckWalletBalance',$header,$postdata);
	
	$result=json_decode($res,true);
	if($result['valid']=='true'&&$result['value']['result']==true&&$result['value']['resultCode']=='000'){
	}
	else{
		$f=fopen('./log.log','a');
		fwrite($f,date('Y/m/d H:i:s').' --- CheckWalletBalance'.PHP_EOL);
		fwrite($f,'                    --- url= '.$url.'/api/Wallet/CheckWalletBalance'.PHP_EOL);
		fwrite($f,'                    --- method= '.$method.PHP_EOL);
		fwrite($f,'                    --- header= '.print_r($header,true).PHP_EOL);
		fwrite($f,'                    --- request= '.json_encode($postdata).PHP_EOL);
		fwrite($f,'                    --- response= '.$res.PHP_EOL);
		fclose($f);
	}

	return $res;
}

function GiveCoins($url,$method,$token,$key,$iv,$senderaccount,$receiveraccount,$senderamount,$receiveramount){
	date_default_timezone_set('Asia/Taipei');

	$header = array(
		'Authorization: Bearer '.$token,
		'Content-Type: application/json'
	);

	$data = array(
		"From" => "Huwei",
		"SenderAccount" => $senderaccount,
		"ReceiverAccount" => $receiveraccount,
		"SenderAmount" => $senderamount,
		"ReceiverAmount" => $receiveramount,
		"Timestamp" => date('Y-m-d H:i:s')
	);

	//$iv_length = openssl_cipher_iv_length('aes-192-cbc');
	$desdata = openssl_encrypt(json_encode($data),'aes-192-cbc',$key,false,$iv);
	//return base64_encode($desdata);
	$postdata = array(
		"type" => "huwei",
		"data" => utf8_encode($desdata)
	);

	$res=php_curl_ajax($method,$url.'/api/Wallet/GiveNubi',$header,$postdata);
	
	$result=json_decode($res,true);
	if($result['valid']=='true'&&$result['value']['result']==true&&$result['value']['resultCode']=='000'){
	}
	else{
		$f=fopen('./log.log','a');
		fwrite($f,date('Y/m/d H:i:s').' --- GiveNubi'.PHP_EOL);
		fwrite($f,'                    --- url= '.$url.'/api/Wallet/GiveNubi'.PHP_EOL);
		fwrite($f,'                    --- method= '.$method.PHP_EOL);
		fwrite($f,'                    --- header= '.print_r($header,true).PHP_EOL);
		fwrite($f,'                    --- json string= '.json_encode($data).PHP_EOL);
		fwrite($f,'                    --- encrypt string= '.utf8_encode($desdata).PHP_EOL);
		fwrite($f,'                    --- request= '.json_encode($postdata).PHP_EOL);
		fwrite($f,'                    --- response= '.$res.PHP_EOL);
		fclose($f);
	}

	return $res;
}

function UseCoins($url,$method,$token,$key,$iv,$account,$consumeamount,$consumeitems,$commodityholder){
	date_default_timezone_set('Asia/Taipei');

	$header = array(
		'Authorization: Bearer '.$token,
		'Content-Type: application/json'
	);

	$data = array(
		"From" => "Huwei",
		"Account" => $account,
		"ConsumeAmount" => $consumeamount,
		"ConsumeItems" => $consumeitems,
		"CommodityHolder" => $commodityholder,
		"Description" => '虎尾兌點紀錄',
		"Timestamp" => date('Y-m-d H:i:s')
	);

	//$des = new DES($key, 'AES', DES::OUTPUT_BASE64, $iv);
	$desdata = openssl_encrypt(json_encode($data),'aes-192-cbc',$key,false,$iv);

	$postdata = array(
		"type" => "huwei",
		"data" => utf8_encode($desdata)
	);
	
	$res=php_curl_ajax($method,$url.'/api/Exchange/ConsumeNubi',$header,$postdata);
	
	$result=json_decode($res,true);
	if($result['valid']=='true'&&$result['value']['result']==true&&$result['value']['resultCode']=='000'){
	}
	else{
		$f=fopen('./log.log','a');
		fwrite($f,date('Y/m/d H:i:s').' --- ConsumeNubi'.PHP_EOL);
		fwrite($f,'                    --- url= '.$url.'/api/Exchange/ConsumeNubi'.PHP_EOL);
		fwrite($f,'                    --- method= '.$method.PHP_EOL);
		fwrite($f,'                    --- header= '.print_r($header,true).PHP_EOL);
		fwrite($f,'                    --- json string= '.json_encode($data).PHP_EOL);
		fwrite($f,'                    --- encrypt string= '.utf8_encode($desdata).PHP_EOL);
		fwrite($f,'                    --- request= '.json_encode($postdata).PHP_EOL);
		fwrite($f,'                    --- response= '.$res.PHP_EOL);
		fclose($f);
	}

	return $res;
}
?>