<?php
//print_r($_POST);
$init=parse_ini_file('../../../database/initsetting.ini',true);
$PostData=json_encode(array("settime"=>$init['init']['settime'],"data"=>$_POST['data'],"senddata"=>$_POST['senddata']));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/insertmemlist.ajax.php');
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
$cardno = $Result;
echo $cardno;
?>