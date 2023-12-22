<?php
$f=fopen('./log.log','a');//2022/9/19 log紀錄只在這邊儲存，posdemo的版本沒有加入，若要debug在移過去

$setup=parse_ini_file('../../../../database/setup.ini',true);

$ts = strtotime(date('YmdHis'));
$secret = $setup['quickclick']['secret'];
$sig = hash_hmac('sha256', $ts, $secret, true);
$res = base64_encode($sig);
$accesskeyid = $setup['quickclick']['accesskeyid'];

$ch = curl_init();

$POSTDATA=array(
	'company'=>$setup['basic']['company'],
	'dep'=>$setup['basic']['story'],
	'orderid'=>$_POST['orderid']
);

curl_setopt($ch, CURLOPT_URL, "http://api.tableplus.com.tw/outposandorder/quickclick/Accept.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTDATA);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!

$responseob1 = curl_exec($ch);
curl_close($ch);
$response1=json_decode($responseob1,true);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $setup['quickclick']['url']."/orders/".str_pad($_POST['orderid'],10,'0',STR_PAD_LEFT)."/accept");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{}");

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: QC ".$accesskeyid.':'.$res,
  "Seed: ".$ts
));

$responseob = curl_exec($ch);
curl_close($ch);
$response=json_decode($responseob,true);


fwrite($f,'path= '.$setup['quickclick']['url']."/orders/".str_pad($_POST['orderid'],10,'0',STR_PAD_LEFT)."/accept".PHP_EOL);
fwrite($f,'header= '."Content-Type: application/json".PHP_EOL."        Authorization: QC ".$accesskeyid.':'.$res.PHP_EOL."        Seed: ".$ts.PHP_EOL);
fwrite($f,'response= '.print_r($responseob,true).PHP_EOL);
fclose($f);
?>