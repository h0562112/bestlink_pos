<?php
include_once '../../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../../database','menu.db','','','','sqlite');
$frontdata=parse_ini_file('../../../../database/'.$_POST['company'].'-front.ini',true);

$paymentmethod=parse_ini_file("./paymentmethod.ini",true);
if(file_exists('../../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
$content=parse_ini_file('../../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);

if(isset($content['init']['accounting'])&&$content['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../../database/timem1.ini',true);
}

$list=$_POST['getlistarray'];
$res='';
//if(isset($list)&&sizeof($list['list'])>0){
	$res['bizdate']=$timeini['time']['bizdate'];
	$res['consecnumber']='';
	$res['tempbuytype']='1';//2021/2/26 該參數先行使用預設值，控制是否出單
	//$res['printclientlist']='1';//2021/2/26 該參數先行使用預設值，控制是否出單
	//$res['totalnumber']=0;//2021/2/26 該參數後面會新增
	//$res['total']=0;//2021/2/26 該參數後面會新增
	$res['usercode']=$list['order']['order_info']['order_id'];
	$res['username']='nidin';
	$res['machinetype']=$_POST['machinetype'];
	switch($list['order']["order_info"]["delivery_type"]){
		case 1:
			$res['listtype']=4;// "自取";
			break;
		case 2:
			$res['listtype']=1;// "內用";
			break;
		case 3:
			$res['listtype']=3;// "外送";
			break;
		case 4:
			$res['listtype']=2;// "外帶";
			break;
		/*case 5:
			$html .= "吧檯";
			break;*/
		default:
			$res['listtype']='';// "未知";"吧檯";
			break;
	}
	//2021/7/15 因為nidin目前的預約單只限制為不同日期，而無法精確到時間，所以將nidin訂單統一為預約單
	//if($list['order']['order_info']['order_method']==2){//預約
		$res['listtype'] .= '-'.preg_replace('/-/','',$list['order']["order_info"]["delivery_reserv_date"]).preg_replace('/:/','',substr($list['order']["order_info"]["delivery_reserv_time"],0,5)).';1';
	/*}
	else{
	}*/

	$res['otherstring']='';//其他付款字串；需要將我們其他付款的code與nidin的code對應起來才能使用；先處理未結帳的帳單

	$PostData = array(
		"membertype" => $_POST['membertype'],
		"type" => "online",
		"tel" => $list['order']["order_info"]["order_phone"],
		"company" => $_POST['company'],
		"story" => $_POST['story'],
		"search" => '1'
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php');//
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	// Edit: prior variable $postFields should be $postfields;
	curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	$memdata = curl_exec($ch);
	$memdata=json_decode($memdata,1);
	if(curl_errno($ch) !== 0) {
		//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
	}
	curl_close($ch);
	if(isset($memdata[0]['memno'])){
	}
	else{
		$PostData = array(
			"membertype" => $_POST['membertype'],
			"type" => "online",
			"initpower" => $_POST['initpower'],
			"name" => preg_replace('/&/','＆',$list['order']["order_info"]["order_name"]),
			"tel" => $list['order']["order_info"]["order_phone"],
			"company" => $_POST['company'],
			"story" => $_POST['story']
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/create.member.php');//
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$memdata = curl_exec($ch);
		$memdata=json_decode($memdata,1);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
		}
		curl_close($ch);
		$PostData = array(
			"membertype" => $_POST['membertype'],
			"type" => "online",
			"tel" => $list['order']["order_info"]["order_phone"],
			"company" => $_POST['company'],
			"story" => $_POST['story'],
			"search" => '1'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php');//
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$memdata = curl_exec($ch);
	$res['senddata2']=$PostData;
	$res['getdata2']=$memdata;
	$memdata=json_decode($memdata,1);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
		}
		curl_close($ch);
	}
	if($list['order']['order_info']['address']!=''){
		$PostData = array(
			"membertype" => $_POST['membertype'],
			"type" => "online",
			"tel" => $list['order']["order_info"]["order_phone"],
			"address" => preg_replace('/[\\\'\"\&\.\$]/','',$list['order']['order_info']['address']),
			"company" => $_POST['company'],
			"story" => $_POST['story'],
			"search" => '1'
		);
		$res['editadd']=$PostData;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemaddno.ajax.php');//
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$memaddno = curl_exec($ch);
		$res['addresponse']=$memaddno;
		$memaddno=json_decode($memaddno,1);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
		}
		curl_close($ch);
		
		$res['memaddno']=$memaddno['addno'];
	}
	else{
	}
	
	$res['memno']=$memdata[0]['memno'];
	$res['tablenumber']='';
	$res['person1']=0;
	$res['person2']=0;
	$res['person3']=0;

	$sizearraycode=array(10,12,14,16,18);
	for($t=0;$t<sizeof($list['order']["items"]);$t++){
		$res['no'][]=$list['order']['items'][$t]['vendor_custom_id'];
		if(isset($res['linenumber'])){
			$res['linenumber'][]=(sizeof($res['linenumber'])+1)*2-1;
		}
		else{
			$res['linenumber'][]=1;
		}
		$res['number'][]=$list['order']['items'][$t]['amount'];

		$sql='SELECT * FROM itemsdata WHERE inumber="'.$list['order']['items'][$t]['vendor_custom_id'].'"';
		$fronttype=sqlquery($conn,$sql,'sqlite');
		$res['typeno'][]=$fronttype[0]['fronttype'];
		$res['type'][]='';

		$res['name'][]=preg_replace('/&/','＆',$list['order']["items"][$t]["name"]);
		$res['name2'][]='';
		$res['unitprice'][]=$list['order']["items"][$t]["price"];
		$res['discontent'][]=0;
		$res['discount'][]=0;

		if($list['order']["items"][$t]["type"]=="1"){//產品名稱
			$itemmoney=$list['order']["items"][$t]["price"];
			$taste='';
			$tastename='';
			$tastenumber='';
			$tasteprice='';
			$tastemoney='0';

			if(isset($list['order']["items"][$t]["options"])&&sizeof($list['order']["items"][$t]["options"])>0){
				$res['mname1'][]='';
				$res['mname2'][]='';
				for($o=0;$o<sizeof($list['order']["items"][$t]["options"]);$o++){
					if($list['order']["items"][$t]["options"][$o]['type']=='2'&&!in_array($list['order']["items"][$t]["options"][$o]['sub_type'],$sizearraycode)){
						if($tastename!=''){
							$taste.=',';
							$tastename.=',';
							$tastenumber.=',';
							$tasteprice.=',';
							//$tastemoney.=',';
						}
						else{
						}
						$taste .= $list['order']["items"][$t]["options"][$o]["vendor_custom_id"];
						$tastename .= preg_replace('/&/','＆',$list['order']["items"][$t]["options"][$o]["name"]);
						$tasteprice .= $list['order']['items'][$t]['options'][$o]['price'];
						if($list['order']["items"][$t]["options"][$o]["amount"]>=$list['order']["items"][$t]["amount"]){
							if((intval($list['order']["items"][$t]["options"][$o]["amount"])/intval($list['order']["items"][$t]["amount"]))==1){
							}
							else{
								$tastename .= '*'.(intval($list['order']["items"][$t]["options"][$o]["amount"])/intval($list['order']["items"][$t]["amount"]));
							}
							$tastenumber .= (intval($list['order']["items"][$t]["options"][$o]["amount"])/intval($list['order']["items"][$t]["amount"]));
							$itemmoney=floatval($itemmoney)+(floatval($list['order']["items"][$t]["options"][$o]["price"])*floatval(intval($list['order']["items"][$t]["options"][$o]["amount"])/intval($list['order']["items"][$t]["amount"])));
							$tastemoney=floatval($tastemoney)+(floatval($list['order']["items"][$t]["options"][$o]["price"])*floatval(intval($list['order']["items"][$t]["options"][$o]["amount"])/intval($list['order']["items"][$t]["amount"])));
						}
						else{
							if($list['order']["items"][$t]["options"][$o]["amount"]==1){
							}
							else{
								$tastename .= '*'.$list['order']["items"][$t]["options"][$o]["amount"];
							}
							$tastenumber .= $list['order']["items"][$t]["options"][$o]["amount"];
							$itemmoney=floatval($itemmoney)+floatval($list['order']["items"][$t]["options"][$o]["money"]);
							$tastemoney=floatval($tastemoney)+floatval($list['order']["items"][$t]["options"][$o]["money"]);
						}
					}
					else{
						$itemmoney=floatval($itemmoney)+floatval($list['order']["items"][$t]["options"][$o]["price"]);
						$res['mname1'][sizeof($res['mname1'])-1]=preg_replace('/&/','＆',$list['order']["items"][$t]["options"][$o]["name"]);
						$res['mname2'][sizeof($res['mname2'])-1]='';
						$res['unitprice'][sizeof($res['unitprice'])-1]=floatval($res['unitprice'][sizeof($res['unitprice'])-1])+floatval($list['order']['items'][$t]['options'][$o]['price']);
					}
				}
			}
			else{
				$res['mname1'][]='';
				$res['mname2'][]='';
			}
			if($taste!=''){
				$taste.=',';
				$tastename.=',';
				$tastenumber.=',';
				$tasteprice.=',';
			}
			else{
			}
			$res['taste1'][]=$taste.'99999';
			$res['taste1name'][]=$tastename.preg_replace('/&/','＆',$list['order']['items'][$t]['memo']);
			$res['taste1number'][]=$tastenumber.'1';
			$res['taste1price'][]=$tasteprice.'0';
			$res['taste1money'][]=$tastemoney;
			
			$res['money'][]=$itemmoney;
			$res['subtotal'][]=floatval($itemmoney)*floatval($list['order']['items'][$t]['amount']);
			$res['isgroup'][]='0';
			$res['childtype'][]='';
			if(isset($res['order'][0])){
				$res['order'][]=sizeof($res['order'])+1;
			}
			else{
				$res['order'][]=1;
			}
		}
		else{//2021/4/28 已初步完成，等待客戶回饋//2021/2/25 套餐部分目前沒有範例可測試，先不做 //$list['order']["items"][$t]["type"]=="3"//套餐名稱
			$res['taste1'][]='99999';
			$res['taste1name'][]=preg_replace('/&/','＆',$list['order']['items'][$t]['memo']);
			$res['taste1number'][]='1';
			$res['taste1price'][]='0';
			$res['taste1money'][]='0';
			$res['mname1'][]=$list['order']["items"][$t]["price"];
			$res['mname2'][]='';
			$res['money'][]=$list['order']["items"][$t]["price"];
			$res['subtotal'][]=floatval($list['order']["items"][$t]["price"])*floatval($list['order']['items'][$t]['amount']);
			$res['isgroup'][]=$fronttype[0]['isgroup'];
			$res['childtype'][]='';
			if(isset($res['order'][0])){
				$res['order'][]=sizeof($res['order'])+1;
			}
			else{
				$res['order'][]=1;
			}
			
			if(isset($list['order']["items"][$t]["items"])&&sizeof($list['order']["items"][$t]["items"])>0){
				for($sub=0;$sub<sizeof($list['order']["items"][$t]["items"]);$sub++){
					$res['no'][]=$list['order']['items'][$t]['items'][$sub]['vendor_custom_id'];
					if(isset($res['linenumber'])){
						$res['linenumber'][]=(sizeof($res['linenumber'])+1)*2-1;
					}
					else{
						$res['linenumber'][]=1;
					}
					$res['number'][]=$list['order']['items'][$t]['items'][$sub]['amount'];

					$sql='SELECT * FROM itemsdata WHERE inumber="'.$list['order']['items'][$t]['items'][$sub]['vendor_custom_id'].'"';
					$subfronttype=sqlquery($conn,$sql,'sqlite');
					$res['typeno'][]=$subfronttype[0]['fronttype'];
					$res['type'][]='';

					$res['name'][]=preg_replace('/&/','＆',$list['order']["items"][$t]['items'][$sub]["name"]);
					$res['name2'][]='';
					$res['unitprice'][]=$list['order']["items"][$t]['items'][$sub]["price"];
					$res['discontent'][]=0;
					$res['discount'][]=0;

					$itemmoney=$list['order']["items"][$t]['items'][$sub]["price"];
					$taste='';
					$tastename='';
					$tastenumber='';
					$tasteprice='';
					$tastemoney='0';

					if(isset($list['order']["items"][$t]['items'][$sub]["options"])&&sizeof($list['order']["items"][$t]['items'][$sub]["options"])>0){
						$res['mname1'][]='';
						$res['mname2'][]='';
						for($o=0;$o<sizeof($list['order']["items"][$t]['items'][$sub]["options"]);$o++){
							if($list['order']["items"][$t]['items'][$sub]["options"][$o]['type']=='2'&&!in_array($list['order']["items"][$t]['items'][$sub]["options"][$o]['sub_type'],$sizearraycode)){
								if($tastename!=''){
									$taste.=',';
									$tastename.=',';
									$tastenumber.=',';
									$tasteprice.=',';
									//$tastemoney.=',';
								}
								else{
								}
								$taste .= $list['order']["items"][$t]['items'][$sub]["options"][$o]["vendor_custom_id"];
								$tastename .= preg_replace('/&/','＆',$list['order']["items"][$t]['items'][$sub]["options"][$o]["name"]);
								$tasteprice .= $list['order']['items'][$t]['items'][$sub]['options'][$o]['price'];
								if($list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"]>=$list['order']["items"][$t]['items'][$sub]["amount"]){
									if((intval($list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"])/intval($list['order']["items"][$t]['items'][$sub]["amount"]))==1){
									}
									else{
										$tastename .= '*'.(intval($list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"])/intval($list['order']["items"][$t]['items'][$sub]["amount"]));
									}
									$tastenumber .= (intval($list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"])/intval($list['order']["items"][$t]['items'][$sub]["amount"]));
									$itemmoney=floatval($itemmoney)+(floatval($list['order']["items"][$t]['items'][$sub]["options"][$o]["price"])*floatval(intval($list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"])/intval($list['order']["items"][$t]['items'][$sub]["amount"])));
									$tastemoney=floatval($tastemoney)+(floatval($list['order']["items"][$t]['items'][$sub]["options"][$o]["price"])*floatval(intval($list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"])/intval($list['order']["items"][$t]['items'][$sub]["amount"])));
								}
								else{
									if($list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"]==1){
									}
									else{
										$tastename .= '*'.$list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"];
									}
									$tastenumber .= $list['order']["items"][$t]['items'][$sub]["options"][$o]["amount"];
									$itemmoney=floatval($itemmoney)+floatval($list['order']["items"][$t]['items'][$sub]["options"][$o]["money"]);
									$tastemoney=floatval($tastemoney)+floatval($list['order']["items"][$t]['items'][$sub]["options"][$o]["money"]);
								}
							}
							else{
								$itemmoney=floatval($itemmoney)+floatval($list['order']["items"][$t]['items'][$sub]["options"][$o]["price"]);
								$res['mname1'][sizeof($res['mname1'])-1]=preg_replace('/&/','＆',$list['order']["items"][$t]['items'][$sub]["options"][$o]["name"]);
								$res['mname2'][sizeof($res['mname2'])-1]='';
								$res['unitprice'][sizeof($res['unitprice'])-1]=floatval($res['unitprice'][sizeof($res['unitprice'])-1])+floatval($list['order']['items'][$t]['items'][$sub]['options'][$o]['price']);
							}
						}
					}
					else{
						$res['mname1'][]='';
						$res['mname2'][]='';
					}
					//2021/6/10 套餐品項不列印訂購人姓名(開放備註)
					/*if($taste!=''){
						$taste.=',';
						$tastename.=',';
						$tastenumber.=',';
						$tasteprice.=',';
					}
					else{
					}
					$res['taste1'][]=$taste.'99999';
					$res['taste1name'][]=$tastename.$list['order']['items'][$t]['items'][$sub]['memo'];
					$res['taste1number'][]=$tastenumber.'1';
					$res['taste1price'][]=$tasteprice.'0';
					$res['taste1money'][]=$tastemoney;*/
					$res['taste1'][]=$taste;
					$res['taste1name'][]=$tastename;
					$res['taste1number'][]=$tastenumber;
					$res['taste1price'][]=$tasteprice;
					$res['taste1money'][]=$tastemoney;
					
					$res['money'][]=$itemmoney;
					$res['subtotal'][]=floatval($itemmoney)*floatval($list['order']['items'][$t]['items'][$sub]['amount']);
					$res['isgroup'][]='0';
					$res['childtype'][]='';
					if(isset($frontdata[$subfronttype[0]['fronttype']]['subtype'])&&$frontdata[$subfronttype[0]['fronttype']]['subtype']=='1'){
						$res['order'][]='－';

					}
					else{
						if(isset($res['order'][0])){
							$res['order'][]=sizeof($res['order'])+1;
						}
						else{
							$res['order'][]=1;
						}
					}
				}
			}
			else{
			}
		}
	}

	$res['salelisthint']=preg_replace('/&/','＆',$list['order']["order_info"]["shopper_remark"]);
	if($list['order']['order_info']['shopper_payment_status']=='11'){
	}
	else{
		$res['listtotal']=$list['order']['order_info']['money'];
	}
	$res['total']=$list['order']['order_info']['paid_money'];
	$res['totalnumber']=$list['order']['order_info']['amount'];
	$res['ininv']=$list['order']['order_info']['paid_money'];
	$res['charge']=0;
	$res['itemdis']=0;
	$res['autodis']=0;
	$res['floorspan']=0;
	$res['should']=$list['order']['order_info']['paid_money'];
	$res['listtotal']=$list['order']['order_info']['paid_money'];
	$res['listdis1']=0;
	$res['listdis2']=0;

	$res['payment']['payment_status']=$list['order']['order_info']['shopper_payment_status'];
	//if($list['order']['order_info']['shopper_payment_status']!=11){
		if(isset($list['order']['invoice_usage'])&&isset($list['order']['invoice_usage']['donate_code'])&&$list['order']['invoice_usage']['donate_code']==30){//2021/3/10 愛心碼
			$res['payment']['data'] = '-'.$list['order']['invoice_usage']['npoban'].'-';
		}
		else{
			if(isset($list['order']['order_info']['ein'])&&$list['order']['order_info']['ein']!=''&&$list['order']['order_info']['ein']!=null){//2021/11/2 統編
				$res['payment']['data'] = $list['order']['order_info']['ein'].'--';
			}
			else{//2021/11/2 載具
				$res['payment']['data'] = '-';
				if(isset($list['order']['invoice_usage'])&&isset($list['order']['invoice_usage']['carrier_id1'])&&$list['order']['invoice_usage']['carrier_id1']!=''&&$list['order']['invoice_usage']['carrier_id1']!=null&&$list['order']['invoice_usage']['donate_code']==20){
					$res['payment']['data'] .= $list['order']['invoice_usage']['carrier_id1'].'-';
				}
				else{
					$res['payment']['data'] .= '-';
				}
			}
		}
		$res['payment']['data'] .= $list['order']['order_info']['pay_place_type'].':';
		if(isset($list['order']["order_payments"])&&sizeof($list['order']["order_payments"])>0){
			$payment=array();
			for($p=0;$p<sizeof($list['order']["order_payments"]);$p++){
				if(isset($payment[$list['order']["order_payments"][$p]['method']])){
					$payment[$list['order']["order_payments"][$p]['method']]=floatval($payment[$list['order']["order_payments"][$p]['method']])+floatval($list['order']["order_payments"][$p]['money']);
				}
				else{
					$payment[$list['order']["order_payments"][$p]['method']]=$list['order']["order_payments"][$p]['money'];
				}
			}
			
			if(sizeof($payment)>0){
				foreach($payment as $method=>$money){
					$res['payment']['data'] .= $method.':'.$money;
				}
			}
			else{
			}
		}
		else{
		}
	/*}
	else{
	}*/

	if(isset($list['order']['discounts'])&&sizeof($list['order']['discounts'])>0){
		//$res['autodis']=0;
		for($i=0;$i<sizeof($list['order']['discounts']);$i++){
			$res['autodis']=floatval($res['autodis'])+floatval($list['order']['discounts'][$i]['money']);
			//2021/8/12 該值在POS中為銷售原價，因此要加上折扣金額
			$res['autodiscontent']='nidin';
			$res['autodispremoney']=$res['autodis'];
			$res['total']=floatval($res['total'])+floatval($list['order']['discounts'][$i]['money']);
		}
	}
	else{
	}

	echo json_encode($res);
/*}
else{
}*/

sqlclose($conn,'sqlite');
?>