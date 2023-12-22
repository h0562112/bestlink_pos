<?php

$header=array(
		"Accept:application/json",
		"Authorization:Bearer ".$_POST['token']
	);

$PostData =array(
	  "taxid"=>$_POST['id'],
	  "period"=>$_POST['period']
	);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_POST['url'].'showTrack');
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	print_r('cURL error when connecting to ' . $_POST['url'].'showTrack: ' . curl_error($_POST['url'].'showTrack'));
}
curl_close($ch);
echo json_encode($Result);

?>