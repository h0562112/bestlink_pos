<?php
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

curl_setopt($ch, CURLOPT_URL, "http://api.tableplus.com.tw/outposandorder/quickclick/Deny.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTDATA);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!

$responseob = curl_exec($ch);
curl_close($ch);
$response=json_decode($responseob,true);


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $setup['quickclick']['url']."/orders/".$_POST['orderid']."/deny");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

//底下兩行code為範例中加入，不知道用途
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
?>