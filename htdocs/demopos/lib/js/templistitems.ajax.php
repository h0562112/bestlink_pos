<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$set=parse_ini_file('../../../database/setup.ini',true);
$menu=parse_ini_file('../../../database/'.$set['basic']['company'].'-menu.ini',true);
$otherpay=parse_ini_file('../../../database/otherpay.ini',true);
/*$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata ORDER BY fronttype ASC,inumber ASC';
$items=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');*/
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER ASC';
$listitem=sqlquery($conn,$sql,'sqlite');
$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$list=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$tt=array();
$tt['person1']=$list[0]['TAX6'];
$tt['person2']=$list[0]['TAX7'];
$tt['person3']=$list[0]['TAX8'];
$tt['amt']=$list[0]['SALESTTLAMT'];
$tt['charge']=$list[0]['TAX1'];
$tt['listtype']=$listitem[0]['REMARKS'];
$tt['listdis2']=0;
$tt['ininv']=0;
$tt['freeinv']=0;

$paycode='EX10';
if($list[0]['CLKNAME']=='FoodPanda'&&$list[0]['TAX4']!='0'){//2022/2/15 foodpanda平台串接的單子並已付款
	//2022/5/19 統一編號會存在帳單備註中，需要字串操作
	if(preg_match('/(統一編號: )/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 檢查是否需要統一編號
		$container=preg_split('/(統一編號: )/',$list[0]['RELINVOICENUMBER']);
		$tt['ban']=substr($container[1],0,8);
		$tt['container']='';//2022/5/19 Foodpanda沒有讓客人輸入載具
	}
	else if(preg_match('/(統一編號:)/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 檢查是否需要統一編號；快一點接下來的Foodpanda備註中，會將空白拿掉(不知道它們為什麼要變動格式)
		$container=preg_split('/(統一編號:)/',$list[0]['RELINVOICENUMBER']);
		$tt['ban']=substr($container[1],0,8);
		$tt['container']='';//2022/5/19 Foodpanda沒有讓客人輸入載具
	}
	else{
	}
	if($list[0]['EX10']!='0'){//2022/2/15 若單子吃下來的時候沒有設定foodpanda的付款方式，會存在EX10的欄位中
		$tt['payment']['foodpanda']['money']=$list[0]['EX10'];
	}
	else{
		for($i=1;$i<sizeof($otherpay);$i++){
			if(strtolower($otherpay['item'.$i]['name'])=='foodpanda'){
				$paycode=$otherpay['item'.$i]['dbname'];
				break;
			}
			else{
			}
		}
		$tt['payment']['foodpanda']['method'][]=$paycode;
		$tt['payment']['foodpanda']['money'][]=$list[0][$paycode];
	}
}
else if($list[0]['CLKNAME']=='FoodPanda'){//2022/5/19 未付款的單子仍要檢查統一編號
	//2022/5/19 統一編號會存在帳單備註中，需要字串操作
	if(preg_match('/(統一編號: )/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 檢查是否需要統一編號
		$container=preg_split('/(統一編號: )/',$list[0]['RELINVOICENUMBER']);
		$tt['ban']=substr($container[1],0,8);
		$tt['container']='';//2022/5/19 Foodpanda沒有讓客人輸入載具
	}
	else if(preg_match('/(統一編號:)/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 檢查是否需要統一編號；快一點接下來的Foodpanda備註中，會將空白拿掉(不知道它們為什麼要變動格式)
		$container=preg_split('/(統一編號:)/',$list[0]['RELINVOICENUMBER']);
		$tt['ban']=substr($container[1],0,8);
		$tt['container']='';//2022/5/19 Foodpanda沒有讓客人輸入載具
	}
	else{
	}
}
else{
}
if($list[0]['CLKNAME']=='UberEats'&&$list[0]['TAX4']!='0'){//2022/10/3 ubereats平台串接的單子並已付款
	if($list[0]['EX10']!='0'){//2022/2/15 若單子吃下來的時候沒有設定ubereats的付款方式，會存在EX10的欄位中
		$tt['payment']['ubereats']['money']=$list[0]['EX10'];
	}
	else{
		for($i=1;$i<sizeof($otherpay);$i++){
			if(strtolower($otherpay['item'.$i]['name'])=='ubereats'){
				$paycode=$otherpay['item'.$i]['dbname'];
				break;
			}
			else{
			}
		}
		$tt['payment']['ubereats']['method'][]=$paycode;
		$tt['payment']['ubereats']['money'][]=$list[0][$paycode];
	}
}
else{
}
if($list[0]['CLKNAME']=='QuickClick'&&$list[0]['TAX4']!='0'){//2022/2/15 quickclick平台串接的單子並已付款
	if($list[0]['EX10']!='0'){//2022/2/15 若單子吃下來的時候沒有設定quickclick的付款方式，會存在EX10的欄位中
		$tt['payment']['quickclick']['money']=$list[0]['EX10'];
	}
	else{
		if(substr($list[0]['CLKCODE'],0,2)=='FP'){//2022/10/3 quickclick中串的foodpanda訂單
			for($i=1;$i<sizeof($otherpay);$i++){
				if(strtolower($otherpay['item'.$i]['name'])=='foodpanda'){
					$paycode=$otherpay['item'.$i]['dbname'];
					break;
				}
				else{
				}
			}
			$tt['payment']['foodpanda']['method'][]=$paycode;
			$tt['payment']['foodpanda']['money'][]=$list[0][$paycode];
		}
		else if(substr($list[0]['CLKCODE'],0,2)=='UE'){//2022/10/3 quickclick中串的ubereats訂單
			for($i=1;$i<sizeof($otherpay);$i++){
				if(strtolower($otherpay['item'.$i]['name'])=='ubereats'){
					$paycode=$otherpay['item'.$i]['dbname'];
					break;
				}
				else{
				}
			}
			$tt['payment']['ubereats']['method'][]=$paycode;
			$tt['payment']['ubereats']['money'][]=$list[0][$paycode];
		}
		else{//2022/10/3 quickclick訂單
			for($i=1;$i<sizeof($otherpay);$i++){
				if(strtolower($otherpay['item'.$i]['name'])=='quickclick'){
					$paycode=$otherpay['item'.$i]['dbname'];
					break;
				}
				else{
				}
			}
			$tt['payment']['quickclick']['method'][]=$paycode;
			$tt['payment']['quickclick']['money'][]=$list[0][$paycode];
		}
	}
}
else{
}
if(isset($list[0]['nidin'])){//2021/3/10 nidin付款方式
	$nidinarray=preg_split('/:/',$list[0]['nidin']);
	$nidinpaytype=preg_split('/-/',$nidinarray[0]);
	//2021/11/8 因為載具字元包含了 - ，因此在切割時也會同時被切到，需要另外補回來
	if(sizeof($nidinpaytype)>3){
		for($i=2;$i<(sizeof($nidinpaytype)-1);$i++){
			$nidinpaytype[1].='-'.$nidinpaytype[$i];
		}
		$nidinpaytype[2]=$nidinpaytype[(sizeof($nidinpaytype)-1)];
	}
	else{
	}
	$tt['ban']=$nidinpaytype[0];
	$tt['container']=$nidinpaytype[1];
	if($nidinpaytype[sizeof($nidinpaytype)-1]=='50'){//2021/3/10 50:POS付款，紀錄只為了後台分析報表使用
	}
	else{//10:nidin線上付款
		for($i=1;$i<sizeof($nidinarray);$i=$i+2){
			$tt['payment']['nidin']['method'][]=$nidinarray[$i];
			$tt['payment']['nidin']['money'][]=$nidinarray[$i+1];
		}
	}
}
else{
}
if(isset($list[0]['intella'])){//2021/9/13 intella付款方式
	$intellaarray=preg_split('/:/',$list[0]['intella']);
	//$intellapaytype=preg_split('/-/',$intellaarray[0]);
	//$tt['ban']=$nidinpaytype[0];
	//$tt['container']=$nidinpaytype[1];
	/*if($nidinpaytype[sizeof($nidinpaytype)-1]=='50'){//2021/9/13 50:POS付款，紀錄只為了後台分析報表使用
	}
	else{//10:nidin線上付款*/
		for($i=1;$i<sizeof($intellaarray);$i=$i+2){
			$tt['payment']['intella']['method'][]=$intellaarray[$i];
			$tt['payment']['intella']['money'][]=$intellaarray[$i+1];
		}
	//}
}
else{
}
for($i=0;$i<sizeof($listitem);$i=$i+2){
	if($listitem[$i]['ITEMCODE']=='autodis'){
	}
	else if($listitem[$i]['ITEMCODE']=='list'){
		$tt['listdis2']=$listitem[$i]['AMT'];
	}
	else{
		if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['insaleinv'])&&$menu[intval($listitem[$i]['ITEMCODE'])]['insaleinv']=='0'){//免稅
			$tt['freeinv']=intval($tt['freeinv'])+intval(floatval($listitem[$i]['AMT'])+floatval($listitem[$i+1]['AMT']));
		}
		else{//應稅
			$tt['ininv']=intval($tt['ininv'])+intval(floatval($listitem[$i]['AMT'])+floatval($listitem[$i+1]['AMT']));
		}
		if(isset($tt['no'])){
			$tt['no'][sizeof($tt['no'])]=intval($listitem[$i]['ITEMCODE']);
			for($j=1;$j<=4;$j++){
				$tt['dis'.$j][sizeof($tt['dis'.$j])]=$menu[intval($listitem[$i]['ITEMCODE'])]['dis'.$j];
			}
			$tt['discount'][sizeof($tt['discount'])]=$listitem[$i+1]['AMT'];
			$tt['getpointtype'][sizeof($tt['getpointtype'])]=$listitem[$i]['TAXCODE4'];//2020/4/20 1>>固定點數2>>金額點數
			$tt['dispoint'][sizeof($tt['dispoint'])]=-(intval($listitem[$i+1]['TAXCODE5'])+intval($listitem[$i]['TAXCODE5']));//2020/4/20 單品促銷－點數兌換所使用的點數
			$tt['unitprice'][sizeof($tt['unitprice'])]=$listitem[$i]['UNITPRICE'];
			$tt['money'][sizeof($tt['money'])]=(floatval($listitem[$i]['AMT'])/floatval($listitem[$i]['QTY']));
			$tt['subtotal'][sizeof($tt['subtotal'])]=floatval($listitem[$i]['AMT']);//消費金額
			$tt['number'][sizeof($tt['number'])]=$listitem[$i]['QTY'];
			//$tt['itemdis'][sizeof($tt['itemdis'])]=$listitem[$i+1]['AMT'];
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['charge'])&&$menu[intval($listitem[$i]['ITEMCODE'])]['charge']!=''){
				$tt['needcharge'][sizeof($tt['needcharge'])]=$menu[intval($listitem[$i]['ITEMCODE'])]['charge'];
			}
			else{
				$tt['needcharge'][sizeof($tt['needcharge'])]='1';
			}
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['listdis'])){
				$tt['listdis'][sizeof($tt['listdis'])]=$menu[intval($listitem[$i]['ITEMCODE'])]['listdis'];
			}
			else{
				$tt['listdis'][sizeof($tt['listdis'])]="1";
			}
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['bothdis'])){
				$tt['bothdis'][sizeof($tt['bothdis'])]=$menu[intval($listitem[$i]['ITEMCODE'])]['bothdis'];
			}
			else{
				$tt['bothdis'][sizeof($tt['bothdis'])]="1";
			}
		}
		else{
			$tt['no'][0]=intval($listitem[$i]['ITEMCODE']);
			for($j=1;$j<=4;$j++){
				$tt['dis'.$j][0]=$menu[intval($listitem[$i]['ITEMCODE'])]['dis'.$j];
			}
			$tt['discount'][0]=$listitem[$i+1]['AMT'];
			$tt['getpointtype'][0]=$listitem[$i]['TAXCODE4'];//2020/4/20 1>>固定點數2>>金額點數
			$tt['dispoint'][0]=-(intval($listitem[$i+1]['TAXCODE5'])+intval($listitem[$i]['TAXCODE5']));//2020/4/20 單品促銷－點數兌換所使用的點數
			$tt['unitprice'][0]=$listitem[$i]['UNITPRICE'];
			$tt['money'][0]=(floatval($listitem[$i]['AMT'])/floatval($listitem[$i]['QTY']));
			$tt['subtotal'][0]=floatval($listitem[$i]['AMT']);//消費金額
			$tt['number'][0]=$listitem[$i]['QTY'];
			//$tt['itemdis'][0]=$listitem[$i+1]['AMT'];
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['charge'])&&$menu[intval($listitem[$i]['ITEMCODE'])]['charge']!=''){
				$tt['needcharge'][0]=$menu[intval($listitem[$i]['ITEMCODE'])]['charge'];
			}
			else{
				$tt['needcharge'][0]='1';
			}
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['listdis'])){
				$tt['listdis'][0]=$menu[intval($listitem[$i]['ITEMCODE'])]['listdis'];
			}
			else{
				$tt['listdis'][0]="1";
			}
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['bothdis'])){
				$tt['bothdis'][0]=$menu[intval($listitem[$i]['ITEMCODE'])]['bothdis'];
			}
			else{
				$tt['bothdis'][0]="1";
			}
		}
	}
}

echo json_encode($tt);
?>