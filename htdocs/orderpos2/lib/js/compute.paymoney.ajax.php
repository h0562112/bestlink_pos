<?php
$otherpay=parse_ini_file('../../../database/otherpay.ini',true);

//2020/4/13 由於在表層只限制 找零 與 面額為1 的付款方式，底下部分流程暫時留空
if(isset($_POST['paysession'])&&isset($otherpay[$_POST['paysession']])){//2020/4/13 支付方式存在
	if(isset($otherpay[$_POST['paysession']]['price'])&&is_numeric($otherpay[$_POST['paysession']]['price'])&&$otherpay[$_POST['paysession']]['price']!='0'){//2020/4/13 面額
		$price=$otherpay[$_POST['paysession']]['price'];
	}
	else{
		$price=1;
	}

	if(!isset($otherpay[$_POST['paysession']]['type'])||$otherpay[$_POST['paysession']]['type']=='1'){//2020/4/13 其他付款找零
		$paytype='other';
		$otherfix='0';

		if((intval($price)*intval($_POST['money'])>=intval($_POST['notyet']))||(!isset($_POST['money'])||intval($_POST['money'])==0)){//2020/4/13 填入金額大於等於應付金額 或 未填入金額
			if(intval($_POST['notyet'])%intval($price)==0){//2020/4/13 在表層尚未更改限制前，都只會進入這個流程
				$viewmoney=(intval($_POST['notyet'])/intval($price));
			}
			else{
				$viewmoney=intval(intval($_POST['notyet'])/intval($price))+1;
			}
			$paymoney=intval($viewmoney)*intval($price);
		}
		else{
			if(intval($_POST['money'])%intval($price)==0){
				$viewmoney=(intval($_POST['money'])/intval($price));
			}
			else{
				$viewmoney=intval(intval($_POST['money'])/intval($price))+1;
			}
			$paymoney=intval($viewmoney)*intval($price);
		}
		
		$other=$paymoney;
		$otherstring=$otherpay[$_POST['paysession']]['location'].'-'.$otherpay[$_POST['paysession']]['dbname'].':'.$viewmoney.'='.$paymoney;

		echo json_encode(array('paytype'=>$paytype,'viewmoney'=>$viewmoney,'paymoney'=>$paymoney,'otherstring'=>$otherstring,'other'=>$other,'otherfix'=>$otherfix));
	}
	else{//2020/4/13 其他付款不找零
		//2020/4/13 暫時留空
	}
}
else{//2020/4/13 支付方式不存在，以現金付款方式
	if(isset($_POST['money'])&&isset($_POST['notyet'])&&intval($_POST['money'])>=intval($_POST['notyet'])){//2020/4/13 填入金額大於於應付金額
		echo json_encode(array('paytype'=>'cashmoney','viewmoney'=>$_POST['notyet'],'paymoney'=>$_POST['notyet'],'otherstring'=>'','other'=>'0','otherfix'=>'0'));
	}
	else{
		echo json_encode(array('paytype'=>'cashmoney','viewmoney'=>$_POST['money'],'paymoney'=>$_POST['money'],'otherstring'=>'','other'=>'0','otherfix'=>'0'));
	}
}
?>