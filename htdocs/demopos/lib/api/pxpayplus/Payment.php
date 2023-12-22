<?php
include_once './Sign.php';

$postradetime=date('YmdHis');
$data=array(
	"store_id" => $_POST['storeid'],//storeid 由全支付提供
	"store_name" => $_POST['storename'],
	"pos_id" => 'm1',
	"pos_trade_time" => $postradetime,
	"mer_trade_no" => $_POST['tradeno'],
	"gate_trade_no" => '',
	"pay_token" => $_POST['PayToken'],
	"trans_id" => '',
	"amount" => intval($_POST['Amount']),
	"none_discount_amount" => 0,
	"none_feedback_amount" => 0,
	"req_time" => $postradetime,
	"remark1" => '',
	"remark2" => '',
	"remark3" => '',
	"marketing" => array(),
	"products" => json_decode($_POST['item_json'],true)
);
/*
array(
						"name" => "餐費",
						"amount" => intval($_POST['amount']),
						"qty" => 1
					)
*/
$hashstring=$_POST['storeid'].'m1'.$postradetime.$_POST['tradeno'].$_POST['PayToken'].intval($_POST['Amount']).$postradetime;
$Sign=Sign($hashstring,$_POST['SecrectKey']);

$header=array(
	'Content-Type: application/json; charset=UTF-8',
	'PX-MerCode: '.$_POST['MerchantCode'],
	'PX-MerEnName: '.$_POST['MerEnName'],//MerEnName=>pos的dep code
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
fwrite($f,date('Y/m/d H:i:s').' --- url: '.$_POST['url'].'/Payment'.PHP_EOL);
fwrite($f,'                    --- header: '.print_r($header,true).PHP_EOL);
fwrite($f,'                    --- data: '.print_r($data,true).PHP_EOL);
fwrite($f,'                    --- use key: '.$_POST['SecrectKey'].PHP_EOL);
fwrite($f,'                    --- hashstring: '.$hashstring.PHP_EOL);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_POST['url'].'/Payment');
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
	print_r('cURL error when connecting to ' . $_POST['url'].'/Payment' . ' : ' . curl_error($ch));
}
curl_close($ch);
$Response = json_decode($Result,1);

fwrite($f,'                    --- pass string: '.json_encode($data).PHP_EOL);
fwrite($f,'                    --- init result: '.$Result.PHP_EOL);
fwrite($f,'                    --- decode result: '.print_r($Response,true).PHP_EOL);
fclose($f);
echo json_encode($Response);
?>