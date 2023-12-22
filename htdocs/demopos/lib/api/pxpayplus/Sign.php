<?php
function Sign($data,$SecrectKey){
	$decodeKey='';
	for($l=0;$l<strlen($SecrectKey);$l=$l+2){
		$decodeKey.=chr(hexdec(substr($SecrectKey,$l,2)));
	}

	return strtoupper(hash_hmac('sha256',$data,$decodeKey));
}
?>