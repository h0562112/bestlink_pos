<?php

$PostData =array(
	  "taxid"=>$_POST['id'],
	  "password"=>$_POST['psw']
  );

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_POST['url'].'getCurToken');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	print_r('cURL error when connecting to ' . $_POST['url'].'getCurToken: ' . curl_error($_POST['url'].'getCurToken'));
}
curl_close($ch);
echo json_encode($Result);

?>