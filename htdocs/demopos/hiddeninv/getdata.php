<?php
include_once '../../tool/dbTool.inc.php';

//輸入暫結單
/*create.tempdb.php*/
$initsetting=parse_ini_file('../../database/initsetting.ini',true);
$time=parse_ini_file('../../database/timem1.ini',true);

$type=array();
$discount=array();
$discontent=array();

array_shift($_POST['no']);
array_shift($_POST['number']);
array_shift($_POST['type']);
array_shift($_POST['name']);
array_shift($_POST['taste']);
array_shift($_POST['tastename']);
array_shift($_POST['tastenumber']);
array_shift($_POST['mname']);
array_shift($_POST['money']);
for($i=0;$i<sizeof($_POST['type']);$i++){
	array_push($type,'');
	array_push($discount,'');
	array_push($discontent,'');
}
$PostData=array(
			'machinetype'=>'m1',
			'consecnumber'=>'',
			'memno'=>$_POST['memno'][0],
			'no'=>($_POST['no']),
			'number'=>($_POST['number']),
			'listtype'=>$initsetting['init']['ordertype'],
			'typeno'=>($_POST['type']),
			'type'=>($type),
			'name'=>($_POST['name']),
			'taste1'=>($_POST['taste']),
			'taste1name'=>($_POST['tastename']),
			'taste1number'=>($_POST['tastenumber']),
			'mname1'=>($_POST['mname']),
			'unitprice'=>($_POST['money']),
			'money'=>($_POST['money']),
			'usercode'=>'system',
			'username'=>'系統寫入',
			'discount'=>($discount),
			'discontent'=>($discontent),
			'listtotal'=>$_POST['subtotal'][0],
			'memberdis'=>'0',
			'listdis1'=>'0',
			'listdis2'=>'0',
			'autodis'=>'0',
			'should'=>$_POST['subtotal'][0],
			'charge'=>'0',
			'tablenumber'=>'',
			'invsalemoney'=>$_POST['subtotal'][0]
		);
//print_r($PostData);

//print_r($_SERVER);
$temp=preg_split('/htdocs/',dirname(__FILE__));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, preg_replace('/\\\\/','/','http://'.$_SERVER['HTTP_HOST'].$temp[1].'\../lib/js/create.tempdb.php'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostData));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	echo curl_error($ch);
}
curl_close($ch);
//echo $Result;
$tempResult=preg_split('/-/',$Result);
$consecnumber=$tempResult[1];

//開立發票
/*open.inv.php*/
if($initsetting['init']['useinv']=='1'){//開啟電子發票
	if(substr($time['time']['bizdate'],4,2)%2==1){//奇數月
		$invdate=substr($time['time']['bizdate'],0,4).str_pad(substr($time['time']['bizdate'],4,2)+1,2,'0',STR_PAD_LEFT);
	}
	else{//偶數月
		$invdate=substr($time['time']['bizdate'],0,6);
	}
	$conn=sqlconnect('../../database/sale/'.$invdate,'invdata_'.$invdate.'_m1.db','','','','sqlite');
	$sql='SELECT COUNT(*) AS num FROM number WHERE state=1';
	$num=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($num[0]['num'])&&$num[0]['num']>0){//具有剩餘發票張數
		
		$PostData=array(
					'machinename'=>'m1',
					'consecnumber'=>$tempResult[1],
					'bizdate'=>$time['time']['bizdate'],
					'tempban'=>$_POST['tempban'][0],
					'tempcontainer'=>'',
					'invlist'=>$initsetting['init']['invlist']
				);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, preg_replace('/\\\\/','/','http://'.$_SERVER['HTTP_HOST'].$temp[1].'\../lib/js/open.inv.php'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostData));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$Result = curl_exec($ch);
		if(curl_errno($ch) !== 0) {
			echo curl_error($ch);
		}
		curl_close($ch);
		//echo $Result;
	}
	else{
	}
}
else{
}

//轉入正式單
/*temptodb.ajax.php*/
$PostData=array(
			'terminalnumber'=>'m1',
			'bizdate'=>$time['time']['bizdate'],
			'numbertag'=>$consecnumber
		);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, preg_replace('/\\\\/','/','http://'.$_SERVER['HTTP_HOST'].$temp[1].'\../lib/js/temptodb.ajax.php'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostData));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	echo curl_error($ch);
}
curl_close($ch);
?>
