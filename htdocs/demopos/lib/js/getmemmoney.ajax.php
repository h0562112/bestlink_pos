<?php
if(file_exists('../../../database/memmoney.ini')){
	$rate=parse_ini_file('../../../database/memmoney.ini',true);
	if($_POST['money']==''){
		$_POST['money']=0;
	}
	else{
	}
	if(sizeof($rate)==0){
		$res=['paymoney'=>$_POST['money'],'memmoney'=>$_POST['money'],'precompute'=>floatval($_POST['remaining'])+floatval($_POST['money'])];
	}
	else{
		for($i=1;$i<=sizeof($rate);$i++){
			if(floatval($_POST['money'])>floatval($rate[$i]['floormoney'])){//高於門檻
				if(isset($rate[$i+1])){//存在下一個門檻
					if(floatval($_POST['money'])>floatval($rate[$i+1]['floormoney'])){//仍然高於下個門檻
						continue;
					}
					else if(floatval($_POST['money'])==floatval($rate[$i+1]['floormoney'])){//恰好等於下個門檻
						if(isset($rate[$i+1]['changetype'])&&$rate[$i+1]['changetype']=='2'){//兌換 rate% * floormoney 儲值金(利用門檻金額計算，並加上門檻金額與支付金額之差額)
							$memmoney=floatval($rate[$i+1]['floormoney'])*floatval($rate[$i+1]['rate'])/100+floatval($_POST['money'])-floatval($rate[$i+1]['floormoney']);
						}
						else if(isset($rate[$i+1]['changetype'])&&$rate[$i+1]['changetype']=='3'){//兌換 rate% * 支付金額 儲值金(利用支付金額計算)
							$memmoney=floatval($_POST['money'])*floatval($rate[$i+1]['rate'])/100;
						}
						else{//兌換X儲值金
							$memmoney=floatval($rate[$i+1]['membermoney'])+floatval($_POST['money'])-floatval($rate[$i+1]['floormoney']);
						}
						break;
					}
					else{
						if(isset($rate[$i]['changetype'])&&$rate[$i]['changetype']=='2'){//兌換 rate% * floormoney 儲值金(利用門檻金額計算，並加上門檻金額與支付金額之差額)
							$memmoney=floatval($rate[$i]['floormoney'])*floatval($rate[$i]['rate'])/100+floatval($_POST['money'])-floatval($rate[$i]['floormoney']);
						}
						else if(isset($rate[$i]['changetype'])&&$rate[$i]['changetype']=='3'){//兌換 rate% * 支付金額 儲值金(利用支付金額計算)
							$memmoney=floatval($_POST['money'])*floatval($rate[$i]['rate'])/100;
						}
						else{//兌換X儲值金
							$memmoney=floatval($rate[$i]['membermoney'])+floatval($_POST['money'])-floatval($rate[$i]['floormoney']);
						}
						break;
					}
				}
				else{
					if(isset($rate[$i]['changetype'])&&$rate[$i]['changetype']=='2'){//兌換 rate% * floormoney 儲值金(利用門檻金額計算，並加上門檻金額與支付金額之差額)
						$memmoney=floatval($rate[$i]['floormoney'])*floatval($rate[$i]['rate'])/100+floatval($_POST['money'])-floatval($rate[$i]['floormoney']);
					}
					else if(isset($rate[$i]['changetype'])&&$rate[$i]['changetype']=='3'){//兌換 rate% * 支付金額 儲值金(利用支付金額計算)
						$memmoney=floatval($_POST['money'])*floatval($rate[$i]['rate'])/100;
					}
					else{//兌換X儲值金
						$memmoney=floatval($rate[$i]['membermoney'])+floatval($_POST['money'])-floatval($rate[$i]['floormoney']);
					}
					break;
				}
			}
			else if(floatval($_POST['money'])==floatval($rate[$i]['floormoney'])){//恰好滿足
				if(isset($rate[$i]['changetype'])&&$rate[$i]['changetype']=='2'){//兌換 rate% * floormoney 儲值金(利用門檻金額計算，並加上門檻金額與支付金額之差額)
					$memmoney=floatval($rate[$i]['floormoney'])*floatval($rate[$i]['rate'])/100+floatval($_POST['money'])-floatval($rate[$i]['floormoney']);
				}
				else if(isset($rate[$i]['changetype'])&&$rate[$i]['changetype']=='3'){//兌換 rate% * 支付金額 儲值金(利用支付金額計算)
					$memmoney=floatval($_POST['money'])*floatval($rate[$i]['rate'])/100;
				}
				else{//兌換X儲值金
					$memmoney=floatval($rate[$i]['membermoney'])+floatval($_POST['money'])-floatval($rate[$i]['floormoney']);
				}
				break;
			}
			else{//低於門檻
				$memmoney=$_POST['money'];
				break;
			}
		}
		$res=['paymoney'=>$_POST['money'],'memmoney'=>$memmoney,'precompute'=>floatval($_POST['remaining'])+floatval($memmoney)];
	}
}
else{
	$res=['paymoney'=>$_POST['money'],'memmoney'=>$_POST['money'],'precompute'=>floatval($_POST['remaining'])+floatval($_POST['money'])];
}
echo json_encode($res);
?>