<?php
function point_money($company,$story,$memno,$paymoney,$giftpoint,$memberpoint,$membermoney,$usemoney=0,$giftmoney=0){
	include_once '../tool/dbTool.inc.php';
	$res=[];
	if(file_exists('../management/menudata/'.$company.'/'.$story.'/initsetting.ini')){
		$initsetting=parse_ini_file('../management/menudata/'.$company.'/'.$story.'/initsetting.ini',true);
		//if(isset($initsetting['init']['ourmempointmoney'])&&$initsetting['init']['ourmempointmoney']=='1'){//使用內建的會員點數或儲值金
			//if(file_exists('../management/menudata/'.$company)){
				$conn=sqlconnect('localhost','','tableplus','0424732003','utf8','mysql');
				$sql='SHOW DATABASES LIKE "'.$_POST['company'].'";';
				$res=sqlquery($conn,$sql,'mysql');
				sqlclose($conn,'mysql');
				if(isset($res[0])){//DB存在//if(file_exists('../management/menudata/'.$company.'/person/member.db')){
					if(isset($initsetting['mempoint'])){//file_exists('../management/menudata/'.$company.'/person/pointfx.ini')
						//$fx=parse_ini_file('../management/menudata/'.$company.'/person/pointfx.ini',true);
						if(isset($initsetting['mempoint']['money'])){
							$rate[0]=$initsetting['mempoint']['money'];
						}
						else{
							$rate[0]=1;
						}
						if(isset($initsetting['mempoint']['point'])){
							$rate[1]=$initsetting['mempoint']['point'];
						}
						else{
							$rate[1]=0;
						}
					}
					else{
						$rate[0]=1;
						$rate[1]=0;
					}
					$conn=sqlconnect('localhost',$company,'orderuser','0424732003','utf8','mysql',0);
					//$conn=sqlconnect('../management/menudata/'.$company.'/person','member.db','','','','sqlite',0);
					if(!$conn){//資料庫無法連結
						$res=[["state"=>"fail","message"=>"DB damaged"]];
					}
					else{
						$sql='SELECT * FROM member WHERE memno="'.$memno.'"';
						$memdata=sqlquery($conn,$sql,'mysql');
						//$memdata=sqlquery($conn,$sql,'sqlite');
						if(isset($memdata[0]['cardno'])){
							if(floatval($memdata[0]['point'])>=floatval($memberpoint)&&floatval($memdata[0]['money'])>=floatval($membermoney)){
								if(floatval($rate[0])>0&&floatval($rate[1])>=0){
									if(floatval($paymoney)<0){//作廢時支付金額的符號為-，因此得以"無條件進位"取近似值
										$giftpoint=ceil((floatval($paymoney)*floatval($rate[1])/floatval($rate[0]))+floatval($giftpoint));
									}
									else{//支付金額的符號為+，依正常流程以"無條件捨去"取近似值
										$giftpoint=floor((floatval($paymoney)*floatval($rate[1])/floatval($rate[0]))+floatval($giftpoint));
									}
									$remainingpoint=((floatval($memdata[0]['point'])-floatval($memberpoint)+floatval($giftpoint))>=0?(floatval($memdata[0]['point'])-floatval($memberpoint)+floatval($giftpoint)):0);
									$remainingmoney=((floatval($memdata[0]['money'])-floatval($membermoney))>=0?(floatval($memdata[0]['money'])-floatval($membermoney)):0);
									$sql='UPDATE member SET point='.$remainingpoint.',money='.$remainingmoney.' WHERE memno="'.$memno.'"';
									sqlnoresponse($conn,$sql,'mysql');
									//sqlnoresponse($conn,$sql,'sqlite');

									$res=[["state"=>"success","memno"=>$memdata[0]['memno'],"cardno"=>$memdata[0]['cardno'],"memname"=>$memdata[0]['name'],"tel"=>str_pad(substr($memdata[0]["tel"],0,4),strlen($memdata[0]['tel']),"*"),"initpoint"=>$memdata[0]['point'],"initmoney"=>$memdata[0]['money'],"giftpoint"=>$giftpoint,"paypoint"=>$memberpoint,"paymoney"=>$membermoney,"remainingpoint"=>$remainingpoint,"remainingmoney"=>$remainingmoney]];
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
					sqlclose($conn,'mysql');
					//sqlclose($conn,'sqlite');
				}
				else{//資料庫不存在
					$res=[["state"=>"fail","message"=>"DB is not exists"]];
				}
			/*}
			else{//體系代碼不存在或錯誤
				$res=[["state"=>"fail","message"=>"company code is error or not exists"]];
			}*/
		//}
		//else{//無使用內建的會員點數或儲值金
		//	$res=[["state"=>"notuse","message"=>"notuse"]];
		//}
	}
	else{//該門市設定檔不存在
		$res=[["state"=>"fail","message"=>"initsetting is not exists"]];
	}
	echo json_encode($res);
}
?>