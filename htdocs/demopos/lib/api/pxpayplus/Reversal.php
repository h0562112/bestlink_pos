<?php
include_once './Sign.php';

$postradetime=date('YmdHis');
$data=array(
	"store_id" => $_POST['storeid'],//storeid �ѥ���I����
	"store_name" => $_POST['storename'],
	"pos_id" => 'm1',
	"pos_trade_time" => $postradetime,
	"mer_trade_no" => $_POST['mertradeno'],
	"gate_trade_no" => '',
	"pay_token" => $_POST['PayToken'],
	"amount" => intval($_POST['Amount']),
	"req_time" => $postradetime
);

$hashstring=$_POST['storeid'].'m1'.$postradetime.$_POST['mertradeno'].$_POST['PayToken'].intval($_POST['Amount']).$postradetime;
$Sign=Sign($hashstring,$_POST['SecrectKey']);

$header=array(
	'Content-Type: application/json; charset=UTF-8',
	'PX-MerCode: '.$_POST['MerchantCode'],
	'PX-MerEnName: '.$_POST['MerEnName'],//MerEnName=>pos��dep code
	'PX-SignValue: '.$Sign
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
$f=fopen('./log/'.date('Y').'/'.date('m').'/'.date('d').'/pxpayplus.log','a');
fwrite($f,date('Y/m/d H:i:s').' --- url: '.$_POST['url'].'/Reversal'.PHP_EOL);
fwrite($f,'                    --- header: '.print_r($header,true).PHP_EOL);
fwrite($f,'                    --- data: '.print_r($data,true).PHP_EOL);
fwrite($f,'                    --- use key: '.$_POST['SecrectKey'].PHP_EOL);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_POST['url'].'/Reversal');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
curl_setopt($ch, CURLOPT_TIMEOUT, 15); //timeout in seconds
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	print_r('cURL error when connecting to ' . $_POST['url'].'/Reversal' . ' : ' . curl_error($ch));
}
curl_close($ch);
$Response = json_decode($Result,1);

fwrite($f,'                    --- pass string: '.json_encode($data).PHP_EOL);
fwrite($f,'                    --- init result: '.$Result.PHP_EOL);
fwrite($f,'                    --- decode result: '.print_r($Response,true).PHP_EOL);
fclose($f);
echo json_encode($Response);
?>