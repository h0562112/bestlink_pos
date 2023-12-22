<?php
/*
 state:fail>>體系代碼錯誤、資料庫連接錯誤 error>>查無對應資料、兌換比輸入錯誤(e.g. 0：1；分號前的值必須>0，分號後的值>=0；可接受比值為無窮小數，但兌換值以"無條件捨去"取近似值) success>>贈點與扣點(款)成功
 message:狀態描述
 cardno:會員編號
 name:會員名稱
 tel:會員電話
 initpoint:原先剩餘點數
 initmoney:原先剩餘儲值金
 giftpoint:贈與點數
 paypoint:支付點數
 paymoney:支付儲值金
 remainingpoint:剩餘點數
 remainingmoney:剩餘儲值金
*/
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once './point_money_function.php';
if(isset($_POST['usemoney'])&&isset($_POST['giftmoney'])){
	$res=point_money($_POST['company'],$_POST['story'],$_POST['memno'],$_POST['paymoney'],$_POST['giftpoint'],$_POST['memberpoint'],$_POST['membermoney'],$_POST['usemoney'],$_POST['giftmoney']);
}
else{//東方之冠過度使用
	$res=point_money($_POST['company'],$_POST['story'],$_POST['memno'],$_POST['paymoney'],$_POST['giftpoint'],$_POST['memberpoint'],$_POST['membermoney'],0,0);
}
if(strstr($res,'database is locked')){
	$res=[["state"=>"fail","message"=>"db lock"]];
	echo json_encode($res);
}
else{
	echo $res;
}
/*include_once '../tool/dbTool.inc.php';
$res=[];
if(file_exists('../management/menudata/'.$_POST['company'])){
	if(file_exists('../management/menudata/'.$_POST['company'].'/person/member.db')){
		$conn=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite',0);
		if(!$conn){//資料庫無法連結
			$res=[["state"=>"fail","message"=>"DB damaged"]];
		}
		else{
			$sql='SELECT * FROM person WHERE memno="'.$_POST['memno'].'"';
			$memdata=sqlquery($conn,$sql,'sqlite');
			if(isset($memdata[0]['cardno'])){
				if(floatval($memdata[0]['point'])>=floatval($_POST['memberpoint'])&&floatval($memdata[0]['money'])>=floatval($_POST['membermoney'])){
					$rate=preg_split('/：/',$_POST['fx']);
					//print_r($rate);
					if(floatval($rate[0])>0&&floatval($rate[1])>=0){
						if(floatval($_POST['paymoney'])<0){//作廢時支付金額的符號為-，因此得以"無條件進位"取近似值
							$giftpoint=ceil((floatval($_POST['paymoney'])*floatval($rate[1])/floatval($rate[0]))+floatval($_POST['giftpoint']));
						}
						else{//支付金額的符號為+，依正常流程以"無條件捨去"取近似值
							$giftpoint=floor((floatval($_POST['paymoney'])*floatval($rate[1])/floatval($rate[0]))+floatval($_POST['giftpoint']));
						}
						$remainingpoint=((floatval($memdata[0]['point'])-floatval($_POST['memberpoint'])+floatval($giftpoint))>=0?(floatval($memdata[0]['point'])-floatval($_POST['memberpoint'])+floatval($giftpoint)):0);
						$remainingmoney=((floatval($memdata[0]['money'])-floatval($_POST['membermoney']))>=0?(floatval($memdata[0]['money'])-floatval($_POST['membermoney'])):0);
						$sql='UPDATE person SET point='.$remainingpoint.',money='.$remainingmoney.' WHERE memno="'.$_POST['memno'].'"';
						sqlnoresponse($conn,$sql,'sqlite');

						$res=[["state"=>"success","cardno"=>$memdata[0]['cardno'],"name"=>$memdata[0]['name'],"tel"=>str_pad(substr($memdata[0]["tel"],0,4),strlen($memdata[0]['tel']),"*"),"initpoint"=>$memdata[0]['point'],"initmoney"=>$memdata[0]['money'],"giftpoint"=>$giftpoint,"paypoint"=>$_POST['memberpoint'],"paymoney"=>$_POST['membermoney'],"remainingpoint"=>$remainingpoint,"remainingmoney"=>$remainingmoney]];
					}
					else{//兌換比輸入錯誤
						$res=[["state"=>"error","message"=>"fx error"]];
					}
				}
				else{//該會員剩餘點數或儲值金不足
					$res=[["state"=>"error","message"=>"the remaining points and money are not enough"]];
				}
			}
			else{//查無該會員編號的資料
				$res=[["state"=>"error","message"=>"no match found"]];
			}
		}
		sqlclose($conn,'sqlite');
	}
	else{//資料庫不存在
		$res=[["state"=>"fail","message"=>"DB is not exists"]];
	}
}
else{//體系代碼不存在或錯誤
	$res=[["state"=>"fail","message"=>"company code is error or not exists"]];
}
echo json_encode($res);*/
?>