<?php
include_once '../../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../../database','menu.db','','','','sqlite');
$frontdata=parse_ini_file('../../../../database/'.$_POST['company'].'-front.ini',true);
$tastedata=parse_ini_file('../../../../database/'.$_POST['company'].'-taste.ini',true);

//$paymentmethod=parse_ini_file("./paymentmethod.ini",true);
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

$list=$_POST['getlistarray'];//2022/6/1 $list[0]=>$cst011;$list[1]=>$cst012;(基本上$cst011中有所有未取單的列表，但cst012中只有最早未取單的內容)
$res='';

$res['bizdate']=$timeini['time']['bizdate'];
$res['consecnumber']='';
$res['tempbuytype']='1';//2021/2/26 該參數先行使用預設值，控制是否出單
$res['usercode']=$list[0][0]['CLKCODE'];
$res['username']='QuickClick';
$res['machinetype']=$_POST['machinetype'];
$res['listtype']=$list[0][0]['REMARKS'];
//2021/7/15 統一將QuickClick取餐時間填入預約時間
$res['listtype'] .= '-'.preg_replace('/[\/\: ]/','',$list[0][0]["receverdatetime"]).';1';

//2022/6/2 因為QuickClick訂單手動接單，是從我們伺服器的資料庫撈資料，因此寫入時就有對應付款方式(到店付款or線上支付：歸入QuickClick付款)
$res['otherstring']='';
for($p=1;$p<=10;$p++){
	if($list[0][0]['TA'.$p]!=''&&$list[0][0]['TA'.$p]!=null&&$list[0][0]['TA'.$p]!=0){
		if($res['otherstring']!=''){
			$res['otherstring'].=',';
		}
		else{
		}
		$res['otherstring'].='CST011-TA'.$p.':'.$list[0][0]['TA'.$p].'='.$list[0][0]['TA'.$p];
	}
	else{
	}
}
//$res['otherstring']='';//其他付款字串；需要將我們其他付款的code與nidin的code對應起來才能使用；先處理未結帳的帳單

//2022/6/1 QuickClick訂單部分，不將顧客資料轉入會員資料
/*$PostData = array(
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
}*/

$res['memno']='';
if(isset($list[0][0]['TABLENUMBER'])&&$list[0][0]['TABLENUMBER']!=''&&$list[0][0]['TABLENUMBER']!=null){
	$res['tablenumber']=$list[0][0]['TABLENUMBER'];
}
else{
	$res['tablenumber']='';
}
$res['person1']=0;
$res['person2']=0;
$res['person3']=0;

$autodis=0;
for($t=0;$t<sizeof($list[1]);$t++){
	if($list[1][$t]['ITEMCODE']!='list'&&$list[1][$t]['ITEMCODE']!='autodis'&&$list[1][$t]['ITEMCODE']!='item'){
		$res['no'][]=intval($list[1][$t]['ITEMCODE']);
		$res['linenumber'][]=intval($list[1][$t]['LINENUMBER']);
		$res['number'][]=$list[1][$t]['QTY'];
		$res['typeno'][]=intval($list[1][$t]['ITEMGRPCODE']);
		$res['type'][]='';
		$res['name'][]=preg_replace('/&/','＆',$list[1][$t]['ITEMNAME']);
		$res['name2'][]='';
		$res['unitprice'][]=$list[1][$t]['UNITPRICE'];
		//2022/6/2 QuickClick訂單不會有產品折扣
		/*if(isset($list[1][$t+1]['AMT'])&&$list[1][$t+1]['AMT']!=0){
			$res['discontent'][]='dis1';
			$res['discount'][]=$list[1][$t+1]['AMT'];
		}
		else{*/
			$res['discontent'][]=0;
			$res['discount'][]=0;
		//}
		
		$taste1='';
		$taste1name='';
		$taste1number='';
		$taste1price='';
		$taste1money=0;
		if($list[1][$t]['SELECTIVEITEM1']!=''&&$list[1][$t]['SELECTIVEITEM1']!=null){
			//$res['taste1'][]=$list[1][$t]['SELECTIVEITEM1'];
			$taste=preg_split('/,/',$list[1][$t]['SELECTIVEITEM1']);
			for($tindex=0;$tindex<sizeof($taste);$tindex++){
				if($taste1name!=''){
					$taste1.=',';
					$taste1name.=',';
					$taste1number.=',';
					$taste1price.=',';
				}
				else{
				}
				$taste1.=intval(substr($taste[$tindex],0,5));
				if(substr($taste[$tindex],0,5)!='99999'){
					$taste1name.=$tastedata[intval(substr($taste[$tindex],0,5))]['name1'];
					if(intval(substr($taste[$tindex],5,1))!=1){
						$taste1name.='*'.substr($taste[$tindex],5,1);
					}
					else{
					}
					$taste1number.=substr($taste[$tindex],5,1);
					$taste1price.=$tastedata[intval(substr($taste[$tindex],0,5))]['money'];
					$taste1money=intval($taste1money)+intval(substr($taste[$tindex],5,1))*intval($tastedata[intval(substr($taste[$tindex],0,5))]['money']);
				}
				else{
					$taste1name.=substr($taste[$tindex],7);
					$taste1number.='1';
					$taste1price.='0';
				}
			}
			$res['taste1'][]=$taste1;
			$res['taste1name'][]=$taste1name;
			$res['taste1number'][]=$taste1number;
			$res['taste1price'][]=$taste1price;
			$res['taste1money'][]=$taste1money;
		}
		else{
			$res['taste1'][]='';
			$res['taste1name'][]='';
			$res['taste1number'][]='';
			$res['taste1price'][]='';
			$res['taste1money'][]='0';
		}
		$res['mname1'][]=$list[1][$t]['UNITPRICELINK'];
		$res['mname2'][]='';
		$res['money'][]=intval($list[1][$t]['UNITPRICE'])+intval($taste1money);
		$res['subtotal'][]=$list[1][$t]['AMT'];

		$sql='SELECT * FROM itemsdata WHERE inumber="'.intval($list[1][$t]['ITEMCODE']).'"';
		$fronttype=sqlquery($conn,$sql,'sqlite');

		$res['isgroup'][]=$fronttype[0]['isgroup'];
		$res['childtype'][]='';
		if(isset($frontdata[intval($list[1][$t]['ITEMGRPCODE'])]['subtype'])&&$frontdata[intval($list[1][$t]['ITEMGRPCODE'])]['subtype']=='1'){
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
	else if($list[1][$t]['ITEMCODE']=='autodis'){
		$autodis=intval($autodis)+intval($list[1][$t]['AMT']);
	}
	else{
	}
}

$res['salelisthint']=preg_replace('/&/','＆',$list[0][0]['RELINVOICENUMBER']);
if(isset($list[0][0]['RELINVOICETIME'])&&$list[0][0]['RELINVOICETIME']!=''&&$list[0][0]['RELINVOICETIME']!=null){//2022/10/14 多產生一個參數，方便儲存訂購人資訊(姓名與電話)
	$res['buyerdata']=preg_replace('/&/','＆',$list[0][0]['RELINVOICETIME']);
}
else{
	$res['buyerdata']='';
}
$res['total']=$list[0][0]['SALESTTLAMT'];
$res['totalnumber']=$list[0][0]['SALESTTLQTY'];
$res['ininv']=$list[0][0]['SALESTTLAMT'];
$res['charge']=0;
$res['itemdis']=0;
$res['autodiscontent']='quickclick';
$res['autodispremoney']=$autodis;
$res['autodis']=$autodis;
$res['floorspan']=0;
$res['should']=$list[0][0]['SALESTTLAMT'];
$res['listtotal']=$list[0][0]['SALESTTLAMT'];
$res['listdis1']=0;
$res['listdis2']=0;

//2022/6/2 這部分原先是用來判斷付款方式以及發票資訊，但這邊因為付款方式上面以判斷完畢，另外發票資訊，目前測試帳號沒有開啟，無法測試邏輯是否正常，因此先註解
/*$res['payment']['payment_status']=$list['order']['order_info']['shopper_payment_status'];
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
}*/
sqlclose($conn,'sqlite');
echo json_encode($res);
?>