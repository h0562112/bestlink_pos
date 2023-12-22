<?php
/*
** 使用以下連結進入該網頁
** /facetest.php/123/456/789
** 將後方變數寫入log.txt檔案中
** $_SERVER['PATH_INFO']="/123/456/789"
** $args[0]=""
** $args[1]="123"
** $args[2]="456"
** $args[3]="789"
*/

$args = explode('/', $_SERVER['PATH_INFO']);

if(file_exists('../../../../print/faceid')){
}
else{
	mkdir('../../../../print/faceid');
}
$f=fopen('../../../../print/faceid/'.$args[1].'-faceid.ini','w');
fwrite($f,'[receverdata]'.PHP_EOL);
for($i=1;$i<sizeof($args);$i++){
	if($args[2]=='open_door'){//驗證faceID
		switch($i){
			case '1':
				fwrite($f,'machine="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '2':
				fwrite($f,'function="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '3':
				fwrite($f,'name="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '4':
				fwrite($f,'number="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '5':
				fwrite($f,'type="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '6':
				fwrite($f,'createdatetime="'.$args[$i].'"'.PHP_EOL);
				continue;
			default:
				fwrite($f,'="'.$args[$i].'"'.PHP_EOL);
				continue;
		}
	}
	else if($args[2]=='create_user'){//新增faceID
		switch($i){
			case '1':
				fwrite($f,'machine="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '2':
				fwrite($f,'function="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '3':
				fwrite($f,'name="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '4':
				fwrite($f,'admintype="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '5':
				fwrite($f,'number="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '6':
				fwrite($f,'type="'.$args[$i].'"'.PHP_EOL);
				continue;
			default:
				fwrite($f,'="'.$args[$i].'"'.PHP_EOL);
				continue;
		}
	}
	else{//刪除faceID
		switch($i){
			case '1':
				fwrite($f,'machine="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '2':
				fwrite($f,'function="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '3':
				fwrite($f,'name="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '4':
				fwrite($f,'admintype="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '5':
				fwrite($f,'number="'.$args[$i].'"'.PHP_EOL);
				continue;
			case '6':
				fwrite($f,'type="'.$args[$i].'"'.PHP_EOL);
				continue;
			default:
				fwrite($f,'="'.$args[$i].'"'.PHP_EOL);
				continue;
		}
	}
}
fclose($f);
if($args[2]=='create_user'){//新增faceID
	date_default_timezone_set('Asia/Taipei');
	$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
	$facedata=parse_ini_file('../../../../print/faceid/'.$args[1].'-faceid.ini',true);
	//print_r($facedata);
	if(isset($initsetting['init']['onlinemember'])&&$initsetting['init']['onlinemember']=='1'){//網路會員
		$setup=parse_ini_file('../../../../database/setup.ini',true);
		$PostData = array(
			"company"=> $setup['basic']['company'],
			"story" => $setup['basic']['story'],
			"membertype" => $initsetting['init']['membertype'],
			"tel" => $facedata['receverdata']['number'],
			"name" => $facedata['receverdata']['name']
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/create.member.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$memdata = curl_exec($ch);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
		}
		curl_close($ch);
		if($memdata=='success'){
			unlink('../../../../print/faceid/'.$args[1].'-faceid.ini');
		}
		else{
			$datetime=date('Y/m/d H:i:s');
			$f=fopen('../../../../print/faceid/log.txt','a');
			fwrite($f,$datetime.' --- CREATE ERROR'.PHP_EOL.print_r($PostData,true).PHP_EOL);
			fclose($f);

		}
	}
	else{//本地會員//由於網路會員為大宗，暫時不處理本地會員部分2019/11/22
		/*$setup=parse_ini_file('../../../../database/setup.ini',true);
		$PostData = array(
			"company"=> $setup['basic']['company'],
			"story" => $setup['basic']['story'],
			"membertype" => $initsetting['init']['membertype'],
			"tel" => $facedata['receverdata']['number'],
			"name" => $facedata['receverdata']['name']
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1/memberapi/create.member.php');
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
		if($memdata=='success'){
		}
		else{
			$f=fopen('../../../../print/faceid/log.txt','a');
			fwrite($f,date('Y/m/d H:i:s').' --- '.print_r($PostData,true).PHP_EOL);
			fclose($f);
		}*/
	}
}
else if($args[2]=='delete_user'){//刪除faceID
	//continue;
}
else{//if($args[2]=='open_door')//驗證faceID
	//continue;
}
?>