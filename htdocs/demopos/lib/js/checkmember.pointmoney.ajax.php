<?php
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(file_exists('../../../database/member.ini')){
	$member=parse_ini_file('../../../database/member.ini',true);
}
else{
}
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}

//2022/3/11 若為預約單，在資料庫中的營業日為開單日，因此在查詢的時候，都使用POST的資訊，最後回傳時才用$timeini的值
/*if(isset($_POST['bizdate'])&&$_POST['bizdate']!=''&&$init['init']['bysaleday']=='0'){//2020/11/30 帳單營收歸屬開單日
	$timeini['time']['bizdate']=$_POST['bizdate'];
}
else{
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
		$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
	}
	else{//帳務以主機為主體計算
		$timeini=parse_ini_file('../../../database/timem1.ini',true);
	}
}*/
if(file_exists('../../../database/otherpay.ini')){
	$otherpay=parse_ini_file('../../../database/otherpay.ini',true);
	$paymethod=array();
	$notcomputepoint=array();
	foreach($otherpay as $index=>$value){
		if($index!='pay'&&isset($value['fromdb'])&&$value['fromdb']=='member'){
			$paymethod[$value['location']]=$value['dbname'];
			if(!isset($value['computepoint'])||$value['computepoint']=='1'){
			}
			else{
				$notcomputepoint[$value['location']]=$value['dbname'];
			}
		}
		else if($index!='pay'&&(isset($value['computepoint'])&&$value['computepoint']=='0')){
			$paymethod[$value['dbname']]=$value['dbname'];
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
$sql='SELECT CUSTCODE AS memno,TAX2 AS cash,TAX3 AS cashcard,SALESTTLAMT';
//print_r($paymethod);
foreach($paymethod as $name=>$value){
	if($name!='memberpoint'&&$name!='membermoney'){
		$sql .= ','.$value;
	}
	else{
		if($name=='memberpoint'){
			$sql .= ','.$value.' AS memberpoint';
		}
		else{//$name=='membermoney'
			$sql .= ','.$value.' AS membermoney';
		}
	}
}
if(!isset($paymethod['memberpoint'])){
	$sql.=',"0=0" AS memberpoint';
}
else{
}
if(!isset($paymethod['membermoney'])){
	$sql.=',"0=0" AS membermoney';
}
else{
}
$sql.=' FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
//echo $sql;
$res=sqlquery($conn,$sql,'sqlite');
/*$f=fopen('./debug.txt','a');
fwrite($f,'tempCST011=> '.$sql.PHP_EOL);
fwrite($f,'res=>'.print_r($res,true).PHP_EOL);
fclose($f);*/
if($res[0]['memno']!=''){
	if(preg_match('/;-;/',$res[0]['memno'])){
		$temp=preg_split('/\;\-\;/',$res[0]['memno']);
		unset($res[0]['memno']);
		$res[0]['memno']=$temp[0];
	}
	else{
		//$res[0]['memno']=$temp[0];
	}
	if($init['init']['onlinemember']=='1'){//網路會員
		$PostData=array(
			"company"=>$_POST['company'],
			"memno"=>$res[0]['memno']
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/checkmember.ajax.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);//2021/10/18//2020/5/5 count($PostData)
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);//2021/10/18//2020/5/5 http_build_query($PostData)
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));//2020/5/5
		$Result = curl_exec($ch);
		if(curl_errno($ch) !== 0) {
			print_r('cURL error when connecting to http://api.tableplus.com.tw/outposandorder/memberapi/checkmember.ajax.php : ' . curl_error($ch));
			$f=fopen('../../../printlog.txt','a');
			fwrite($f,date('Y/m/d H:i:s').' -- checkmember.pointmoney.ajax.php api.tableplus.com.tw/outposandorder/memberapi/checkmember.ajax.php(online error)'.PHP_EOL);
			fwrite($f,date('Y/m/d H:i:s').' -- send data : '.print_r($PostData,true).PHP_EOL);
			fwrite($f,date('Y/m/d H:i:s').' -- '.print_r(curl_error($ch),true).PHP_EOL);
			fclose($f);
		}
		else{
			$f=fopen('../../../printlog.txt','a');
			fwrite($f,date('Y/m/d H:i:s').' -- checkmember.pointmoney.ajax.php api.tableplus.com.tw/outposandorder/memberapi/checkmember.ajax.php(online success)'.PHP_EOL);
			fclose($f);
		}
		curl_close($ch);
		$cardno = json_decode($Result,true);
		//print_r($cardno);
		//$cardno = $Response->Response;
	}
	else{
		$conn1=sqlconnect('../../../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
		//$conn1=sqlconnect('../../../database/person','member.db','','','','sqlite');
		$sql='SELECT cardno FROM person WHERE memno="'.$res[0]['memno'].'"';
		$cardno=sqlquery($conn1,$sql,'sqlite');
		sqlclose($conn1,'sqlite');
		$f=fopen('../../../printlog.txt','a');
		fwrite($f,date('Y/m/d H:i:s').' -- checkmember.pointmoney.ajax.php(offline success)'.PHP_EOL);
		fclose($f);
	}
	
	$sql='SELECT ITEMCODE,LINENUMBER,UNITPRICELINK,QTY,AMT,TAXCODE4,TAXCODE5 FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER';//2020/4/14 增加TAXCODE4、TAXCODE5分別代表贈點類別、固定點數
	$saleitems=sqlquery($conn,$sql,'sqlite');
	/*$f=fopen('./debug.txt','a');
	fwrite($f,'tempCST012=>'.$sql.PHP_EOL);
	fwrite($f,print_r($saleitems,true).PHP_EOL);
	fclose($f);*/
	
	$res[0]['re1']='';//2020/5/5 推薦人編號
	$res[0]['re1point']=0;//2020/5/5 推薦人回饋點數
	$res[0]['re2']='';//2020/5/5 level2推薦人編號
	$res[0]['re2point']=0;//2020/5/5 level2推薦人回饋點數

	if(isset($init['init']['ourmempointmoney'])&&$init['init']['ourmempointmoney']=='1'){//2020/4/14 使用POS內建會員點數(儲值金)
		$fixpoint=0;//固定點數
		$moneypoint=0;//金額點數
		//$money=0;//金額(點數設定為金額點數的銷售金額)
		$money=$res[0]['SALESTTLAMT'];//金額(點數設定為金額點數的銷售金額)//2020/9/1
		$othermoney=0;//2020/9/1 不計算點數的付款金額
		
		//$computetag=0;
		for($i=0;$i<sizeof($saleitems);$i++){
			/*if($saleitems[$i]['TAXCODE4']=='2'){//金額點數
				$money=floatval($money)+floatval($saleitems[$i]['AMT']);
			}
			else{//固定點數
				$fixpoint=intval($fixpoint)+intval($saleitems[$i]['TAXCODE5']);
			}*/
			//2020/9/1
			if($saleitems[$i]['TAXCODE4']=='2'){//金額點數
				//$money=floatval($money)+floatval($saleitems[$i]['AMT']);
				$computetag=0;
			}
			else if($saleitems[$i]['TAXCODE4']=='1'){//固定點數
				/*if($computetag==0){
					$computetag=1;
				}
				else{*/
					$money=floatval($money)-floatval($saleitems[$i]['AMT']);
				//}
				$fixpoint=intval($fixpoint)+intval($saleitems[$i]['TAXCODE5']);
			}
			else{//2020/9/1 帳單折讓、自動優惠、會員折讓
				//$money=floatval($money)+floatval($saleitems[$i]['AMT']);
			}
		}

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
			$othermoney=floatval($othermoney)+floatval($res[0]['cashcard']);
		}
		else{
		}
		
		if(isset($member)&&isset($member['init']['moneytype'])&&$member['init']['moneytype']=='2'){//2020/7/7 判斷金額1>>銷售金額2>>支付現金(產品點數設定失效)
			$money=$res[0]['cash'];
			$fixpoint=0;
		}
		else{
			$money=min((floatval($money)-floatval($othermoney)),$res[0]['SALESTTLAMT']);
			if($money<0){
				$money=0;
			}
			else{
			}

			/*if(intval($money)<intval($res[0]['SALESTTLAMT'])){
			}
			else{//2020/7/27 當金額超出銷售金額，則以銷售金額為主
				$money=$res[0]['SALESTTLAMT'];
			}*/
		}

		if(isset($member)&&isset($member['init']['getpoint'])&&$member['init']['getpoint']=='2'){//贈送銷售金額的固定%點數(取整數)
			$PostData=array("company"=>$_POST['company'],"memno"=>$res[0]['memno']);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getrecommend.ajax.php');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_POST, count($PostData));//2020/5/5
			// Edit: prior variable $postFields should be $postfields;
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostData));//2020/5/5
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
			//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));//2020/5/5
			$Resultcom = curl_exec($ch);
			if(curl_errno($ch) !== 0) {
				print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
			}
			curl_close($ch);
			$recommend = json_decode($Resultcom,true);
			$res[0]['re1']=$recommend['re1'];
			$res[0]['re2']=$recommend['re2'];
			
			if(isset($member['basic']['firlevel'])&&$member['basic']['firlevel']!=''){
				$firlevel=$member['basic']['firlevel'];
			}
			else{
				$firlevel=5;
			}
			if(isset($member['basic']['seclevel'])&&$member['basic']['seclevel']!=''){
				$seclevel=$member['basic']['seclevel'];
			}
			else{
				$seclevel=3;
			}
			if(isset($member['basic']['thilevel'])&&$member['basic']['thilevel']!=''){
				$thilevel=$member['basic']['thilevel'];
			}
			else{
				$thilevel=2;
			}

			$moneypoint=intval(floatval($money)*floatval($firlevel)/floatval(100));
			$secmoneypoint=intval(floatval($money)*floatval($seclevel)/floatval(100));
			$thimoneypoint=intval(floatval($money)*floatval($thilevel)/floatval(100));

			$res[0]['re1point']=$secmoneypoint;
			$res[0]['re2point']=$thimoneypoint;
			$res[0]['usemoney']=$money;
			$res[0]['selfproportion']=$firlevel;
			$res[0]['re1proportion']=$seclevel;
			$res[0]['re2proportion']=$thilevel;
		}
		else{//每滿足固定金額贈送固定點數
			if(isset($member)&&isset($member['basic']['money'])&&isset($member['basic']['point'])&&is_numeric($member['basic']['money'])&&is_numeric($member['basic']['point'])){//新版參數－儲存在member.ini
				if($member['basic']['money']!='0'){
					$moneypoint=intval(floatval($money)/floatval($member['basic']['money']))*intval($member['basic']['point']);
				}
				else{
				}
			}
			else{//舊版參數－儲存在initsetting.ini
				if(isset($init['mempoint']['money'])&&isset($init['mempoint']['point'])&&is_numeric($init['mempoint']['money'])&&is_numeric($init['mempoint']['point'])){
					if($init['mempoint']['money']!='0'){
						$moneypoint=intval(floatval($money)/floatval($init['mempoint']['money']))*intval($init['mempoint']['point']);
					}
					else{
					}
				}
				else{
				}
			}
		}
		
		$res[0]['paymoney']=$money;
		$res[0]['giftpoint']=intval($fixpoint)+intval($moneypoint);
	}
	else{
		$res[0]['paymoney']=$res[0]['cash'];
		$res[0]['giftpoint']=0;
	}

	$temp=preg_split('/=/',$res[0]['memberpoint']);
	$res[0]['memberpoint']=$temp[0];
	$temp=preg_split('/=/',$res[0]['membermoney']);
	$res[0]['membermoney']=$temp[0];
	$res[0]['company']=$setup['basic']['company'];
	$res[0]['story']=$setup['basic']['story'];
	$res[0]['datetime']=date('YmdHis');

	if(isset($_POST['bizdate'])&&$_POST['bizdate']!=''&&$init['init']['bysaleday']=='0'){//2020/11/30 帳單營收歸屬開單日
		$timeini['time']['bizdate']=$_POST['bizdate'];
	}
	else{
		if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
			$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
		}
		else{//帳務以主機為主體計算
			$timeini=parse_ini_file('../../../database/timem1.ini',true);
		}
	}
	$res[0]['bizdate']=$timeini['time']['bizdate'];
	$res[0]['consecnumber']=$_POST['consecnumber'];
	$res[0]['cardno']=$cardno[0]['cardno'];
	//print_r($res);
	echo json_encode($res);
}
else{
}
sqlclose($conn,'sqlite');
?>