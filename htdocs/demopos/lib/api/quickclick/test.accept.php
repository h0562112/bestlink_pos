<?php
$setup=parse_ini_file('../../../../database/setup.ini',true);

$ts = strtotime(date('YmdHis'));
$secret = $setup['quickclick']['secret'];
$sig = hash_hmac('sha256', $ts, $secret, true);
$res = base64_encode($sig);
$accesskeyid = $setup['quickclick']['accesskeyid'];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $setup['quickclick']['url']."/orders/".$_GET['orderid']."/accept");
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

echo 'path= '.$setup['quickclick']['url']."/orders/".$_GET['orderid']."/accept<br>";
echo 'header= '."Content-Type: application/json<br>"."        Authorization: QC ".$accesskeyid.':'.$res."<br>        Seed: ".$ts.'<br>';
echo 'response= '.print_r($response,true);
?>