<?php
include_once '../../../../tool/dbTool.inc.php';
include_once '../../../../tool/inilib.php';

$company=$_GET['company'];
$dep=$_GET['dep'];
$orderid=str_pad($_GET['orderid'],10,'0',STR_PAD_LEFT);
$time=parse_ini_file('../../../../database/timem1.ini',true);
$bizdate=$time['time']['bizdate'];
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$menu=parse_ini_file('../../../database/'.$company.'-menu.ini',true);

$conn=sqlconnect('../../../../database','menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata';
$menudata=sqlquery($conn,$sql,'sqlite');
$menuno=array_column($menudata,'inumber');
sqlclose($conn,'sqlite');

$ts = strtotime(date('YmdHis'));
$secret = 'acc6fe509825b263eb9aa0bee09e96bb120f195d';
$sig = hash_hmac('sha256', $ts, $secret, true);
$res = base64_encode($sig);
//echo $ts.PHP_EOL;
$accesskeyid = 'S_20220104124376';

$ch = curl_init();

//curl_setopt($ch, CURLOPT_URL, "https://apis-staging.quickclick.cc/v1/orders/".$orderid);
curl_setopt($ch, CURLOPT_URL, "https://apis-staging.quickclick.cc/v1/shops/D_n46b2ABzz/menus");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: QC ".$accesskeyid.':'.$res,
  "Seed: ".$ts
));

$response = curl_exec($ch);
curl_close($ch);
$response=json_decode($response,true);
print_r($response);

//echo $response['user']['name'];

/*$conn=sqlconnect('../../../../database/sale','SALES_'.substr($bizdate,0,6).'.db','','','','sqlite');
$sql='SELECT (SELECT CONSECNUMBER FROM CST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) AS one,(SELECT CONSECNUMBER FROM tempCST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) AS two';
$s=sqlquery($conn,$sql,'sqlite');

//2021/10/18 查詢網路訂單的編號
$sql='SELECT (SELECT SUBSTR(CONSECNUMBER,2) FROM CST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS one,(SELECT SUBSTR(CONSECNUMBER,2) FROM tempCST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS two';
$w=sqlquery($conn,$sql,'sqlite');
if($s[0]['one']==null){
	$s[0]['one']=$w[0]['one'];
}
else{
	if(floatval($s[0]['one'])<floatval($w[0]['one'])){
		$s[0]['one']=$w[0]['one'];
	}
	else{
	}
}
if($s[0]['two']==null){
	$s[0]['two']=$w[0]['two'];
}
else{
	if(floatval($s[0]['two'])<floatval($w[0]['two'])){
		$s[0]['two']=$w[0]['two'];
	}
	else{
	}
}

if($s[0]['one']==null&&$s[0]['two']==null){
	$machinedata['basic']['consecnumber']='1';
}
else if($s[0]['one']==null){
	if(floatval($machinedata['basic']['consecnumber'])>floatval($s[0]['two'])){
		$machinedata['basic']['consecnumber']=floatval($machinedata['basic']['consecnumber'])+1;
	}
	else{
		$machinedata['basic']['consecnumber']=floatval($s[0]['two'])+1;
	}
}
else if($s[0]['two']==null){
	if(floatval($machinedata['basic']['consecnumber'])>floatval($s[0]['one'])){
		$machinedata['basic']['consecnumber']=floatval($machinedata['basic']['consecnumber'])+1;
	}
	else{
		$machinedata['basic']['consecnumber']=floatval($s[0]['one'])+1;
	}
}
else{
	if(floatval($s[0]['one'])>floatval($s[0]['two'])){
		if(floatval($machinedata['basic']['consecnumber'])>floatval($s[0]['one'])){
			$machinedata['basic']['consecnumber']=floatval($machinedata['basic']['consecnumber'])+1;
		}
		else{
			$machinedata['basic']['consecnumber']=floatval($s[0]['one'])+1;
		}
	}
	else{
		if(floatval($machinedata['basic']['consecnumber'])>floatval($s[0]['two'])){
			$machinedata['basic']['consecnumber']=floatval($machinedata['basic']['consecnumber'])+1;
		}
		else{
			$machinedata['basic']['consecnumber']=floatval($s[0]['two'])+1;
		}
	}
}
//$machinedata['basic']['consecnumber']=intval($machinedata['basic']['consecnumber'])+1;
$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
$consecnumber=str_pad($machinedata['basic']['consecnumber'],6,"0",STR_PAD_LEFT);
$saleno=$machinedata['basic']['saleno'];
if(intval($machinedata['basic']['saleno'])>=intval($machinedata['basic']['maxsaleno'])){
	if(isset($machinedata['basic']['strsaleno'])){
		$machinedata['basic']['saleno']=$machinedata['basic']['strsaleno'];
	}
	else{
		$machinedata['basic']['saleno']=0;
	}
}
else{
}
if(sizeof($machinedata['basic'])>3){
	write_ini_file($machinedata,'../../../../database/machinedata.ini');
}
else{
	date_default_timezone_set($content['init']['settime']);
	$f=fopen('../../../../database/'.date('YmdHis').'machinedata.ini','w');
	//fwrite($f,print_r($machinedata,true).PHP_EOL);
	fclose($f);
}


$tqty=0;
$linenumber=1;
$subitem=0;
$itemno=[];//產品編號
$item=[];//產品名稱
$front=[];//產品類別
$taste=[];//備註
$price=[];//單價
$qty=[];//數量
$amt=[];//小計


for($i=0;$i<sizeof($response['carts']);$i++){
	$index=sizeof($item);
	if(in_array($response['carts'][$i]['externalId'],$menuno)){
		$item[$index]=$response['carts'][$i]['title'];
		$itemno[$index]=str_pad($response['carts'][$i]['externalId'],16,'0',STR_PAD_LEFT);
		$front[$index]=str_pad($menu[array_search($response['carts'][$i]['externalId'],$menudata)]['fronttype'],6,'0',STR_PAD_LEFT);
		$taste[$index]='';
		$price[$index]=$response['carts'][$i]['price'];
		$qty[$index]=$response['carts'][$i]['quantity'];
		$amt[$index]=$response['carts'][$i]['price']*$response['carts'][$i]['quantity'];
		//產品備註
		if(sizeof($response['carts'][$i]['selectedModifierGroups'])>0){//有加購選項
			for($t=0;$t<sizeof($response['carts'][$i]['selectedModifierGroups']);$t++){//第一層加購
				for($ft=0;$ft<sizeof($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions']);$ft++){//第一層中的加購選項
					if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId']=='m'){//特殊調整(名稱串接、價格加總、單價累加)
						$item[$index].=$response['carts'][$i]['selectedModifierGroups'][$t]['title'];
						$amt[$index]+=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']*$response['carts'][$i]['quantity'];
						$price[$index]+=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity'];
					}
					else if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId']!=''&&substr($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],0,1)=='t'){//POS中的備註編號
						if($taste[$index]!=''){
							$taste[$index].=',';
						}
						else{
						}
						$taste[$index].=str_pad((substr($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],1).$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']),6,'0',STR_PAD_LEFT);
						if(substr($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],1)=='99999'){//開放備註
							$taste[$index].=':'.$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['title'];
						}
						else{
						}
						$amt[$index]+=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price'];
					}
					else if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId']!=''&&is_numeric($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'])){//POS中的產品編號
						$sindex=sizeof($item);
						$item[$sindex]=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['title'];
						$itemno[$sindex]=str_pad($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],16,'0',STR_PAD_LEFT);
						$front[$sindex]=str_pad($menu[array_search($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],$menudata)]['fronttype'],6,'0',STR_PAD_LEFT);
						$taste[$sindex]='';
						$price[$sindex]=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price'];
						$qty[$sindex]=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity'];
						$amt[$sindex]=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity'];
						if(sizeof($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'])>0){//有第二層加購
							for($tt=0;$tt<sizeof($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups']);$tt++){//第二層加購
								for($st=0;$st<sizeof($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions']);$st++){//第二層的加購選項
									if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId']=='m'){//特殊調整(名稱串接、價格加總)
										$item[$sindex].=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['title'];
										$amt[$sindex]+=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['quantity']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity'];
										$price[$sindex]+=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['quantity'];
									}
									else if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId']!=''&&substr($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId'],0,1)=='t'){//POS中的備註編號
										if($taste[$sidnex]!=''){
											$taste[$sindex].=',';
										}
										else{
										}
										$taste[$sindex].=str_pad((substr($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId'],1).$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['quantity']),6,'0',STR_PAD_LEFT);
										if(substr($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId'],1)=='99999'){//開放備註
											$taste[$sindex].=':'.$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['title'];
										}
										else{
										}
										$amt[$sindex]+=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['quantity']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['price'];
									}
									else if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId']!=''&&is_numeric($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId'])){//POS中的產品編號
										$tindex=sizeof($item);
										$item[$tindex]=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['title'];
										$itemno[$tindex]=str_pad($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId'],16,'0',STR_PAD_LEFT);
										$front[$tindex]=str_pad($menu[array_search($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId'],$menudata)]['fronttype'],6,'0',STR_PAD_LEFT);
										$taste[$tindex]='';
										$price[$tindex]=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['price'];
										$qty[$tindex]=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['quantity'];
										$amt[$tindex]=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['quantity'];
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
				}
			}
		}
		else{
		}
	}
	else{
	}
}

print_r($item);
print_r($itemno);
print_r($front);
print_r($taste);
print_r($price);
print_r($qty);
print_r($amt);

$sql='';
$tqty=0;
$tamt=0;
for($i=0;$i<sizeof($item);$i++){
	$tqty+=$qty[$i];
	$tamt+=$amt[$i];
	$sql.='INSERT INTO tempCST012 VALUES ("quickclick","'.$bizdate.'","'.$consecnumber.'","'.str_pad(($i*2+1),3,'0',STR_PAD_LEFT).'","'.intval($response['orderId']).'","QuickClick","1","1","01","'.$itemno[$i].'","'.$item[$i].'","'.$front[$i].'","","'.$front[$i].'","","'.$taste[$i].'",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"0",NULL,"0","'.$qty[$i].'","'.$price[$i].'","'.$amt[$i].'",NULL,"0","0","1","0","'.$time['time']['zcounter'].'"';
	//帳單類別
	if($response['mealType']=='eat_in'){//內用
		$sql.=',"1"';
	}
	else if($response['mealType']=='to_go'){//外帶
		$sql.=',"2"';
	}
	else if($response['mealType']=='delivery'){//外送
		$sql.=',"3"';
	}
	else{//避免缺少欄位，未定應判斷為自取
		$sql.=',"4"';
	}
	$sql.=',"'.date('YmdHis',strtotime($response['createdTs'])).'");';
	$sql.='INSERT INTO tempCST012 VALUES ("quickclick","'.$bizdate.'","'.$consecnumber.'","'.str_pad((($i+1)*2),3,'0',STR_PAD_LEFT).'","'.intval($response['orderId']).'","QuickClick","1","3","02","item","單品優惠","","","","",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"0",NULL,"0","0","0","0",NULL,"0","0","1","0","'.$time['time']['zcounter'].'"';
	//帳單類別
	if($response['mealType']=='eat_in'){//內用
		$sql.=',"1"';
	}
	else if($response['mealType']=='to_go'){//外帶
		$sql.=',"2"';
	}
	else if($response['mealType']=='delivery'){//外送
		$sql.=',"3"';
	}
	else{//避免缺少欄位，未定應判斷為自取
		$sql.=',"4"';
	}
	$sql.=',"'.date('YmdHis',strtotime($response['createdTs'])).'");';
}
$sql.='INSERT INTO tempCST011 ("TERMINALNUMBER","BIZDATE","CONSECNUMBER","INVOICENUMBER","INVOICEDATE","INVOICETIME","OPENCLKCODE","CLKCODE","CLKNAME","REGMODE","REGTYPE","REGFUNC","SALESTTLQTY","SALESTTLAMT","TAX1","TAX2","TAX3","TAX4","TAX5","TAX6","TAX7","TAX8","TAX9","TAX10","TA1","TA2","TA3","TA4","TA5","TA6","TA7","TA8","TA9","TA10","EX1","EX2","EX3","EX4","EX5","EX6","EX7","EX8","EX9","EX10","NONTAX","PROFITAMT","COVER","CUSTGPCODE","CUSTGPNAME","CUSTCODE","CUSTNAME","POINTTARGET","POINTPREVIOUS","POINTGOT","POINTUSED","OPENCHKDATE","OPENCHKTIME","NBCHKDATE","NBCHKTIME","NBCHKNUMBER","TABLENUMBER","RELINVOICEDATE","RELINVOICETIME","RELINVOICENUMBER","ZCOUNTER","REMARKS","CREATEDATETIME","UPDATEDATETIME") VALUES ("quickclick","'.$bizdate.'","'.$consecnumber.'","","0","0","0","'.intval($response['orderId']).'","QuickClick","0","0","0","'.$tqty.'","'.$tamt.'","0"';
if($response['mealType']=='cash'){//現金
	$sql.=',"'.$tamt.'","0","0","'.$tamt.'"';
}
else if($response['mealType']=='qrpay_credit'){//信用卡
	$sql.=',"0","'.$tamt.'","0","'.$tamt.'"';
}
else{//其他付款；其餘都暫時歸屬其他付款
	$sql.=',"0","0","'.$tamt.'","'.$tamt.'"';
}
$sql.=',"1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0",NULL,NULL,NULL,NULL,"0","0","0","0",NULL,NULL,NULL,NULL,NULL,"'.$response['tableNumber'].'",NULL,NULL,"","'.$time['time']['zcounter'].'"';
//帳單類別
if($response['mealType']=='eat_in'){//內用
	$sql.=',"1"';
}
else if($response['mealType']=='to_go'){//外帶
	$sql.=',"2"';
}
else if($response['mealType']=='delivery'){//外送
	$sql.=',"3"';
}
else{//避免缺少欄位，未定應判斷為自取
	$sql.=',"4"';
}
$sql.=',"'.date('YmdHis',strtotime($response['createdTs'])).'","");';
$sql.='INSERT INTO salemap (bizdate,consecnumber,saleno) VALUES ("'.$bizdate.'","'.$consecnumber.'","'.$saleno.'");';*/


/*$sql='';
for($i=0;$i<sizeof($response['carts']);$i++){
	$mainamt=$response['carts'][$i]['price']*$response['carts'][$i]['quantity'];//主產品小計
	$subamt=0;//副產品小計
	$value='';//副產品SQL
	$sql.='INSERT INTO tempCST012 VALUES ("quickclick","'.$bizdate.'","'.$consecnumber.'","'.$linenumber.'","'.intval($response['orderId']).'","QuickClick","1","1","01","'.str_pad($response['carts'][$i]['externalId'],16,'0',STR_PAD_LEFT).'","'.$response['carts'][$i]['title'].'"';
	//產品類別
	if(in_array($response['carts'][$i]['externalId'],$menuno)){
		$sql.=',"'.str_pad($menu[array_search($response['carts'][$i]['externalId'],$menudata)]['fronttype'],6,'0',STR_PAD_LEFT).'","","'.str_pad($menu[array_search($response['carts'][$i]['externalId'],$menudata)]['fronttype'],6,'0',STR_PAD_LEFT).'",""';
	}
	else{
		$sql.=',"","","",""';
	}
	//產品備註
	if(sizeof($response['carts'][$i]['selectedModifierGroups'])>0){//有加購選項
		$sql.=',"';
		for($t=0;$t<sizeof($response['carts'][$i]['selectedModifierGroups']);$t++){//第一層加購
			$ftaste=[];
			for($ft=0;$ft<sizeof($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions']);$ft++){//第一層中的加購選項
				if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId']=='M'){//特殊調整(名稱串接、價格加總)
					$mainamt+=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']*$response['carts'][$i]['quantity'];
					$response['carts'][$i]['title'].=$response['carts'][$i]['selectedModifierGroups'][$t]['title'];
				}
				else if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId']!=''&&substr($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],0,1)=='t'){//POS中的備註編號
					$mainamt+=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']*$response['carts'][$i]['quantity'];//備註小計
					$ftaste[]=str_pad((substr($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],1).$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']),6,'0',STR_PAD_LEFT);
				}
				else if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId']!=''&&is_numeric($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'])){//POS中的產品編號
					$subamt=$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price']*$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']*$response['carts'][$i]['quantity'];//副產品小計
					$subitem+=2;
					$value.='INSERT INTO tempCST012 VALUES ("quickclick","'.$bizdate.'","'.$consecnumber.'","'.($linenumber+$subitem).'","'.intval($response['orderId']).'","QuickClick","1","1","01","'.str_pad($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],16,'0',STR_PAD_LEFT).'","'.$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['title'].'"';
					//產品類別
					if(in_array($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],$menuno)){
						$value.=',"'.str_pad($menu[array_search($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],$menudata)]['fronttype'],6,'0',STR_PAD_LEFT).'","","'.str_pad($menu[array_search($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['externalId'],$menudata)]['fronttype'],6,'0',STR_PAD_LEFT).'",""';
					}
					else{
						$value.=',"","","",""';
					}
					if(sizeof($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'])>0){//有第二層加購
						$staste=[];
						$value.=',"';
						for($tt=0;$tt<sizeof($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups']);$tt++){//第二層加購
							for($st=0;$st<sizeof($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions']);$st++){//第二層的加購選項
								if($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['selectedModifierGroups'][$tt]['selectedModifierOptions'][$st]['externalId']=='M'){//特殊調整(名稱串接、價格加總)
								}
								else if(){//POS中的備註編號
								}
								else if(){//POS中的產品編號
								}
								else{
								}
							}
						}
						$value.=implode(',',$staste).'"';
						$value.=',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL';
					}
					else{
						$value.=',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL';
					}
					$value.=',"0",NULL,"0","'.($response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['quantity']*$response['carts'][$i]['quantity']).'","'.$response['carts'][$i]['selectedModifierGroups'][$t]['selectedModifierOptions'][$ft]['price'].'","'.$subamt.'",NULL,"0","0","1","0","'.$time['time']['zcounter'].'"';
					//帳單類別
					if($response['mealType']=='eat_in'){//內用
						$value.=',"1"';
					}
					else if($response['mealType']=='to_go'){//外帶
						$value.=',"2"';
					}
					else if($response['mealType']=='delivery'){//外送
						$value.=',"3"';
					}
					else{//避免缺少欄位，未定應判斷為自取
						$value.=',"4"';
					}
					$value.=',"'.date('YmdHis',strtotime($response['createdTs'])).'");';
				}
				else{
				}
			}
		}
		$sql.=implode(',',$ftaste).'"';
		$sql.=',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL';
	}
	else{
		$sql.=',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL';
	}
	$sql.=',"0",NULL,"0","'.$response['carts'][$i]['quantity'].'","'.$response['carts'][$i]['price'].'","'.$mainamt.'",NULL,"0","0","1","0","'.$time['time']['zcounter'].'"';
	//帳單類別
	if($response['mealType']=='eat_in'){//內用
		$sql.=',"1"';
	}
	else if($response['mealType']=='to_go'){//外帶
		$sql.=',"2"';
	}
	else if($response['mealType']=='delivery'){//外送
		$sql.=',"3"';
	}
	else{//避免缺少欄位，未定應判斷為自取
		$sql.=',"4"';
	}
	$sql.=',"'.date('YmdHis',strtotime($response['createdTs'])).'");';
}*/

/*echo $sql;

sqlnoresponse($conn,$sql,'sqliteexec');
sqlclsoe($conn,'sqlite');*/
?>