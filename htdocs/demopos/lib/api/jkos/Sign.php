<?php
function Sign($data,$MerchantKey){
	$temp=jkos_json_encode($data);
	//$temp=json_encode($data);
	$temp .= $MerchantKey;
	//echo 'json string= '.$temp.'<br><br>';
	return hash('sha256',$temp);
}

function jkos_json_encode($data){
	$res='{';
	$sign='';
	foreach($data as $k=>$v){
		if($k=='Sign'){
			$sign=',"'.$k.'":"'.$v.'"';
		}
		else{
			if($res!='{'){
				$res .= ',';
			}
			else{
			}
			if($k!='TradeAmount'&&$k!='UnRedeem'){
				$res .= '"'.$k.'":"'.$v.'"';
			}
			else{
				$res .= '"'.$k.'":'.$v;
			}
		}
	}
	if($sign==''){
	}
	else{
		$res .= $sign;
	}
	$res .= '}';

	return $res;
}
?>