<?php
include_once '../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
$data=parse_ini_file('../../../database/machinedata.ini',true);
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT CUSTCODE FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$memno=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(isset($memno[0]['CUSTCODE'])&&$memno[0]['CUSTCODE']!=''){
	if($initsetting['init']['onlinemember']=='1'){//網路會員
		$PostData = array(
			"type"=>"online",
			"ajax" => "",
			"company" => $data['basic']['company'],
			"memno" => $memno[0]['CUSTCODE']
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
		$pointmoney=json_decode($memdata,1);
		if(!isset($pointmoney[0]['point'])){
			echo json_encode([["point"=>0,"money"=>0]]);
		}
		else{
			echo json_encode($pointmoney);
		}
		if(curl_errno($ch) !== 0) {
			print_r('cURL error when connecting to http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php : ' . curl_error($ch));
		}
		curl_close($ch);
	}
	else{//本地會員
		$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
		$sql='SELECT point,money FROM person WHERE memno="'.$memno[0]['CUSTCODE'].'"';
		$pointmoney=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
		if(!isset($pointmoney[0]['point'])){
			echo json_encode([["point"=>0,"money"=>0]]);
		}
		else{
			echo json_encode($pointmoney);
		}
	}
}
else{
	echo json_encode([["point"=>0,"money"=>0]]);
}
?>