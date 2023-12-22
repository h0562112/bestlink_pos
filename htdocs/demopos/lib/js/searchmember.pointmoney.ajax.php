<?php
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machine']])){
		$invmachine=$dbmapping['map'][$_POST['machine']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
if(file_exists('../../../database/otherpay.ini')){
	$otherpay=parse_ini_file('../../../database/otherpay.ini',true);
	$membermethod=array();
	$notcomputepoint=array();
	foreach($otherpay as $index=>$value){
		if($index!='pay'&&isset($value['fromdb'])&&$value['fromdb']=='member'){
			$membermethod[$value['location']]=$value['dbname'];
			if(!isset($value['computepoint'])||$value['computepoint']=='1'){
			}
			else{
				$notcomputepoint[$value['location']]=$value['dbname'];
			}
		}
		else if($index!='pay'&&(isset($value['computepoint'])&&$value['computepoint']=='0')){
			$membermethod[$value['dbname']]=$value['dbname'];
			$notcomputepoint[$value['dbname']]=$value['dbname'];
		}
		else{
		}
	}
}
else{
}
$setup=parse_ini_file('../../../database/setup.ini',true);
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
if(file_exists('../../../database/member.ini')){
	$member=parse_ini_file('../../../database/member.ini',true);
}
else{
}
$sql='SELECT CUSTCODE AS memno,TAX2 AS paymoney,TAX3,SALESTTLAMT';
foreach($membermethod as $name=>$value){
	if($name!='memberpoint'&&$name!='membermoney'){
		$sql .= ','.$value;
	}
	else{
	}
}
if(isset($membermethod['memberpoint'])){
	$sql.=','.$membermethod['memberpoint'].' AS memberpoint';
}
else{
	$sql.=',"0=0" AS memberpoint';
}
if(isset($membermethod['membermoney'])){
	$sql.=','.$membermethod['membermoney'].' AS membermoney';
}
else{
	$sql.=',"0=0" AS membermoney';
}
$sql.=' FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
//echo $sql;
$res=sqlquery($conn,$sql,'sqlite');

if(isset($member)&&isset($member['init']['moneytype'])&&$member['init']['moneytype']=='2'){//2020/7/7 判斷金額1>>銷售金額2>>支付現金(產品點數設定失效)
}
else{
	$othermoney=0;
	if(sizeof($notcomputepoint)>0){
		foreach($notcomputepoint as $colname=>$colvalue){
			$subothermoney=preg_split('/=/',$res[0][$colname]);
			if(isset($subothermoney[1])){
				$othermoney=floatval($othermoney)+floatval($subothermoney[1]);
			}
			else{
				$othermoney=floatval($othermoney)+floatval($subothermoney[0]);
			}
		}
	}
	else{
	}
	if(isset($otherpay['pay']['pointofcash'])&&$otherpay['pay']['pointofcash']=='0'){//2020/9/1 現金不計算會員點數
		$othermoney=floatval($othermoney)+floatval($res[0]['cash']);
	}
	else{
	}
	if(isset($otherpay['pay']['pointofcard'])&&$otherpay['pay']['pointofcard']=='0'){//2020/9/1 信用卡不計算會員點數
		$othermoney=floatval($othermoney)+floatval($res[0]['TAX3']);
	}
	else{
	}

	$res[0]['paymoney']=min((floatval($res[0]['SALESTTLAMT'])-floatval($othermoney)),$res[0]['SALESTTLAMT']);
	if($res[0]['paymoney']<0){
		$res[0]['paymoney']=0;
	}
	else{
	}
}

if($res[0]['memno']!=''){
	$res[0]['state']='void';
	$res[0]['paymoney']=floatval(0)-floatval($res[0]['paymoney']);
	
	if($_POST['type']=='online'){
		$PostData=array(
				'company'=>$_POST['company'],
				'story'=>$_POST['story'],
				'settime'=>$init['init']['settime'],
				'bizdate'=>$_POST['bizdate'],
				'consecnumber'=>$_POST['consecnumber'],
				'memno'=>$res[0]['memno']
			);
		//print_r($PostData);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/searchmember.ajax.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$getResult = curl_exec($ch);
		//print_r($Result);
		//2020/5/25 $Result = json_decode($getResult,1);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
			$init=parse_ini_file('../../../database/initsetting.ini',true);
			//date_default_timezone_set('Asia/Taipei');
			date_default_timezone_set($init['init']['settime']);
			$header=fopen('../../../printlog.txt','a');
			fwrite($header,date('Y/m/d H:i:s').' -- '.print_r('cURL error when connecting to "http://api.tableplus.com.tw/outposandorder/memberapi/searchmember.ajax.php": ' . curl_error($ch),true).PHP_EOL);
			fclose($header);

			$cardno[0]['cardno']='';
			$giftpoint[0]['giftpoint']='0';
		}
		else{
			$Result = json_decode($getResult,1);
			$cardno[0]['cardno']=$Result['cardno'];
			$giftpoint[0]['giftpoint']=$Result['giftpoint'];
		}
		curl_close($ch);
		//print_r($Result);
		

		//2020/5/6 回收推薦人、level2推薦人回饋點數
		$PostData=array(
				'company'=>$_POST['company'],
				'story'=>$_POST['story'],
				'settime'=>$init['init']['settime'],
				'bizdate'=>$_POST['bizdate'],
				'consecnumber'=>$_POST['consecnumber']
			);
		//print_r($PostData);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/searchmemberrecommend.ajax.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$getResult = curl_exec($ch);
		//print_r($Result);
		//2020/5/25 $Result = json_decode($getResult,1);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
			$init=parse_ini_file('../../../database/initsetting.ini',true);
			//date_default_timezone_set('Asia/Taipei');
			date_default_timezone_set($init['init']['settime']);
			$header=fopen('../../../printlog.txt','a');
			fwrite($header,date('Y/m/d H:i:s').' -- '.print_r('cURL error when connecting to "http://api.tableplus.com.tw/outposandorder/memberapi/searchmemberrecommend.ajax.php": ' . curl_error($ch),true).PHP_EOL);
			fclose($header);

			$res[0]['re1']='';//2020/5/6 預設推薦人編號
			$res[0]['re1point']='0';//2020/5/6 預設回收推薦人點數
			$res[0]['re2']='';//2020/5/6 預設level2推薦人編號
			$res[0]['re2point']='0';//2020/5/6 預設回收level2推薦人點數
		}
		else{
			$Result = json_decode($getResult,1);
			$res[0]['re1']=$Result['memno1'];//2020/5/6 推薦人編號
			$res[0]['re1point']=floatval(0)-floatval($Result['giftpoint1']);//2020/5/6 回收推薦人點數
			$res[0]['re2']=$Result['memno2'];//2020/5/6 level2推薦人編號
			$res[0]['re2point']=floatval(0)-floatval($Result['giftpoint2']);//2020/5/6 回收level2推薦人點數
		}
		curl_close($ch);
	}
	else{
		$conn1=sqlconnect('../../../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
		$sql='SELECT cardno FROM person WHERE memno="'.$res[0]['memno'].'"';
		$cardno=sqlquery($conn1,$sql,'sqlite');
		sqlclose($conn1,'sqlite');
		$conn1=sqlconnect('../../../database/sale','memsalelist_'.date('Ym').'.db','','','','sqlite');
		$sql='SELECT giftpoint FROM salemap WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.intval($_POST['consecnumber']).'"';
		$giftpoint=sqlquery($conn1,$sql,'sqlite');
		sqlclose($conn1,'sqlite');

		$res[0]['re1']='';//2020/5/6 推薦人編號
		$res[0]['re1point']=0;//2020/5/6 回收推薦人點數
		$res[0]['re2']='';//2020/5/6 level2推薦人編號
		$res[0]['re2point']=0;//2020/5/6 回收level2推薦人點數
	}
	$res[0]['giftpoint']=floatval(0)-floatval($giftpoint[0]['giftpoint']);
	$temp=preg_split('/=/',$res[0]['memberpoint']);
	$res[0]['memberpoint']=$temp[0];
	$res[0]['memberpoint']=floatval(0)-floatval($res[0]['memberpoint']);
	$temp=preg_split('/=/',$res[0]['membermoney']);
	$res[0]['membermoney']=$temp[0];
	$res[0]['membermoney']=floatval(0)-floatval($res[0]['membermoney']);
	$res[0]['company']=$setup['basic']['company'];
	$res[0]['story']=$setup['basic']['story'];
	$res[0]['datetime']=date('YmdHis');
	$res[0]['bizdate']=$_POST['bizdate'];
	$res[0]['consecnumber']=$_POST['consecnumber'];
	$res[0]['cardno']=$cardno[0]['cardno'];
	//print_r($res);
	echo json_encode($res);
}
else{
}
sqlclose($conn,'sqlite');
?>