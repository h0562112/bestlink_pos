<?php
include_once "./nidin_api_inc.php";

$setup=parse_ini_file('../../../../database/setup.ini',true);

$list=GetList($setup['nidin']['url'],"get",$_POST["Token"],$_POST["User"],1,1,"status","10");

if(isset($list['list'][0])){
	if($list['list'][0]["order_info"]["shopper_payment_status"]!=11){
		if(isset($list['list'][0]["order_payments"])&&sizeof($list['list'][0]["order_payments"])>0){
			$payment=array();
			for($p=0;$p<sizeof($list['list'][0]["order_payments"]);$p++){
				if(isset($payment[$list['list'][0]["order_payments"][$p]['method']])){
					$payment[$list['list'][0]["order_payments"][$p]['method']]['money']=floatval($payment[$list['list'][0]["order_payments"][$p]['method']]['money'])+floatval($list['list'][0]["order_payments"][$p]['money']);
				}
				else{
					$payment[$list['list'][0]["order_payments"][$p]['method']]['pay_place_type']=$list['list'][0]["order_payments"][$p]['pay_place_type'];
					$payment[$list['list'][0]["order_payments"][$p]['method']]['method']=$list['list'][0]["order_payments"][$p]['method'];
					$payment[$list['list'][0]["order_payments"][$p]['method']]['name']=$list['list'][0]["order_payments"][$p]['name'];
					$payment[$list['list'][0]["order_payments"][$p]['method']]['money']=$list['list'][0]["order_payments"][$p]['money'];
					$payment[$list['list'][0]["order_payments"][$p]['method']]['shopper_payment_status']=$list['list'][0]["order_payments"][$p]['shopper_payment_status'];
					$payment[$list['list'][0]["order_payments"][$p]['method']]['store_payment_status']=$list['list'][0]["order_payments"][$p]['store_payment_status'];
					$payment[$list['list'][0]["order_payments"][$p]['method']]['ref_no']=$list['list'][0]["order_payments"][$p]['ref_no'];
					$payment[$list['list'][0]["order_payments"][$p]['method']]['transaction_id']=$list['list'][0]["order_payments"][$p]['transaction_id'];
				}
			}
			if(sizeof($payment)>0){
				$list['list'][0]["order_payments"]=array();
				foreach($payment as $paymethod=>$paymoney){
					$list['list'][0]["order_payments"][]=$payment[$paymethod];
				}
			}
			else{
			}
		}
		else{
		}
	}

	if(sizeof($list['list'][0]["items"])>0){
		$sizearraycode=array(10,12,14,16,18);
		for($t=0;$t<sizeof($list['list'][0]["items"]);$t++){
			$list['list'][0]['myitemlist'][$t]['name']='';
			$list['list'][0]['myitemlist'][$t]['size']='';
			$list['list'][0]['myitemlist'][$t]['taste']='';
			$list['list'][0]['myitemlist'][$t]['price']='';
			$list['list'][0]['myitemlist'][$t]['number']='';
			$list['list'][0]['myitemlist'][$t]['money']='';
			$list['list'][0]['myitemlist'][$t]['remark']='';
			$list['list'][0]['myitemlist'][$t]['group']='';
			$list['list'][0]['myitemlist'][$t]['subitem']=array();

			$list['list'][0]['myitemlist'][$t]['name']=$list['list'][0]["items"][$t]["name"];
			$list['list'][0]['myitemlist'][$t]['number']=$list['list'][0]["items"][$t]["amount"];
			$list['list'][0]['myitemlist'][$t]['price']=$list['list'][0]["items"][$t]["price"];
			$list['list'][0]['myitemlist'][$t]['remark']=($list['list'][0]["items"][$t]["memo"]!=$list['list'][0]["order_info"]["order_name"])?($list['list'][0]["items"][$t]["memo"]):('');
			if($list['list'][0]["items"][$t]["type"]=="1"){//產品名稱
				$list['list'][0]['myitemlist'][$t]['group']='0';

				$list['list'][0]['myitemlist'][$t]['money']=$list['list'][0]["items"][$t]["money"];
				if(isset($list['list'][0]["items"][$t]["options"])&&sizeof($list['list'][0]["items"][$t]["options"])>0){
					for($o=0;$o<sizeof($list['list'][0]["items"][$t]["options"]);$o++){
						if($list['list'][0]["items"][$t]["options"][$o]['type']=='2'&&!in_array($list['list'][0]["items"][$t]["options"][$o]['sub_type'],$sizearraycode)){
							$list['list'][0]['myitemlist'][$t]['money']=floatval($list['list'][0]['myitemlist'][$t]['money'])+floatval($list['list'][0]["items"][$t]["options"][$o]["money"]);
							$list['list'][0]['myitemlist'][$t]['price']=floatval($list['list'][0]['myitemlist'][$t]['price'])+floatval(intval($list['list'][0]["items"][$t]["options"][$o]["price"])*intval($list['list'][0]["items"][$t]["options"][$o]["show_amount"]));
							if($list['list'][0]['myitemlist'][$t]['taste']!=''){
								$list['list'][0]['myitemlist'][$t]['taste'] .= ',';
							}
							else{
							}
							$list['list'][0]['myitemlist'][$t]['taste'] .= $list['list'][0]["items"][$t]["options"][$o]["name"];
							if($list['list'][0]["items"][$t]["options"][$o]["show_amount"]>1){
								$list['list'][0]['myitemlist'][$t]['taste'] .= '*'.$list['list'][0]["items"][$t]["options"][$o]["show_amount"];
							}
							else{
							}
						}
						else{
							$list['list'][0]['myitemlist'][$t]['size']=$list['list'][0]["items"][$t]["options"][$o]["name"];
							$list['list'][0]['myitemlist'][$t]['money']=floatval($list['list'][0]['myitemlist'][$t]['money'])+floatval($list['list'][0]["items"][$t]["options"][$o]["money"]);
							$list['list'][0]['myitemlist'][$t]['price']=floatval($list['list'][0]['myitemlist'][$t]['price'])+floatval($list['list'][0]["items"][$t]["options"][$o]["price"]);
						}
					}
				}
				else{
				}
			}
			else{//$list['list'][0]["items"][$t]["type"]=="3"//套餐名稱
				$list['list'][0]['myitemlist'][$t]['group']='1';

				$list['list'][0]['myitemlist'][$t]['money']=$list['list'][0]["items"][$t]["money"];
				if(isset($list['list'][0]["items"][$t]["items"])&&sizeof($list['list'][0]["items"][$t]["items"])>0){
					for($sub=0;$sub<sizeof($list['list'][0]["items"][$t]["items"]);$sub++){
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['name']='';
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['size']='';
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['taste']='';
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['price']='';
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['number']='';
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['money']='';
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['remark']='';

						$list['list'][0]['myitemlist'][$t]['money']=floatval($list['list'][0]['myitemlist'][$t]['money'])+floatval($list['list'][0]["items"][$t]["items"][$sub]["money"]);
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['money']=$list['list'][0]["items"][$t]["items"][$sub]["money"];

						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['name']=$list['list'][0]["items"][$t]["items"][$sub]["name"];
						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['number']=$list['list'][0]["items"][$t]["items"][$sub]["amount"];

						$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['price']=$list['list'][0]["items"][$t]["items"][$sub]["price"];

						if(isset($list['list'][0]["items"][$t]["items"][$sub]["options"])&&sizeof($list['list'][0]["items"][$t]["items"][$sub]["options"])>0){
							for($o=0;$o<sizeof($list['list'][0]["items"][$t]["items"][$sub]["options"]);$o++){
								if($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]['type']=='2'&&!in_array($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]['sub_type'],$sizearraycode)){
									$list['list'][0]['myitemlist'][$t]['money']=floatval($list['list'][0]['myitemlist'][$t]['money'])+floatval($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["money"]);
									$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['money']=floatval($list['list'][0]['myitemlist'][$t]['subitem'][$sub]['money'])+floatval($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["money"]);
									$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['price']=floatval($list['list'][0]['myitemlist'][$t]['subitem'][$sub]['price'])+floatval(intval($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["price"])*intval($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["show_amount"]));
									if($list['list'][0]['myitemlist'][$t]['subitem'][$sub]['taste']!=''){
										$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['taste'] .= ',';
									}
									else{
									}
									$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['taste'] .= $list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["name"];
									if($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["show_amount"]>1){
										$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['taste'] .= $list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["show_amount"];
									}
									else{
									}
								}
								else{
									$list['list'][0]['myitemlist'][$t]['money']=floatval($list['list'][0]['myitemlist'][$t]['money'])+floatval($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["money"]);
									$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['money']=floatval($list['list'][0]['myitemlist'][$t]['subitem'][$sub]['money'])+floatval($list['list'][0]["items"][$t]["items"][$sub]["options"][$o]["money"]);
									$list['list'][0]['myitemlist'][$t]['subitem'][$sub]['size']=$list['list'][0]["items"][$t]["items"][$sub]["options"][$o]['name'];
								}
							}
						}
						else{
						}
					}
				}
				else{
				}
			}
		}
	}
	else{
	}
}
else{
}

echo json_encode($list);

$header=fopen('../../../../printlog.txt','a');
if($list!=null&&$list['status']==200){
	fwrite($header,date('Y/m/d H:i:s').' -- NIDIN detail SUCCESS.(message='.$list['message'].';description='.$list['description'].')'.PHP_EOL);
	if(isset($list['token_need_replace'])&&$list['token_need_replace']==1){
		fwrite($header,date('Y/m/d H:i:s').' -- NIDIN TOKEN NEED REPLACE.(status='.$list['status'].') (token_need_replace='.$list['token_need_replace'].')'.PHP_EOL);
	}
	else{
	}
}
else if($list==null){
	fwrite($header,date('Y/m/d H:i:s').' -- CAN NOT CONNECT NIDIN SERVER.'.PHP_EOL);
}
else{
	if(isset($list['message'])){
	}
	else{
		$list['message']='';
	}
	if(isset($list['description'])){
	}
	else{
		$list['description']='';
	}
	if(isset($list['status'])){
	}
	else{
		$list['status']='';
	}
	fwrite($header,date('Y/m/d H:i:s').' -- NIDIN detail FAIL.(status='.$list['status'].') '.$list['message'].'('.$list['description'].')'.PHP_EOL);
}
fclose($header);
?>