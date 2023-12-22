<title>invdownload</title>
<?php
/*由first呼叫下載發票*/
date_default_timezone_set('Asia/Taipei');

//2020/8/31 用來判斷該次是下載當期或下期的發票
$Y=date('Y')-1911;
$m=date('m');
if($m%2==1){
	$m=$m+1;
}
else{
}
$m=str_pad($m,2,'0',STR_PAD_LEFT);
$nowperiod=$Y.$m;

if(!isset($_GET['date'])||$_GET['date']==''){
	/*$Y=date('Y')-1911;//2020/8/31
	$m=date('m');
	if($m%2==1){
		$m=$m+1;
	}
	else{
	}
	$m=str_pad($m,2,'0',STR_PAD_LEFT);*/
	$period=$nowperiod;
}
else{
	$period=$_GET['date'];
}
?>
<script src="../../../../tool/jquery-1.12.4.js?<?php echo date('YmdHis'); ?>"></script>
<script src="./zdn_api.js?<?php echo date('YmdHis'); ?>"></script>
<?php
$setup=parse_ini_file('../../../../database/setup.ini',true);
if($setup['basic']['Identifier']!=$setup['zdninv']['id']||$setup['zdninv']['id']==''){
	echo '<title>inv get finish</title>';
}
else{
	$machine=$_GET['machine'];//機號
	if(isset($_GET['onceinv'])&&$_GET['onceinv']>0){
		$temp1=intval($_GET['onceinv']/50);
		$temp2=intval($_GET['onceinv']%50);
		if($temp2>0){
			$temp1=$temp1+1;
		}
		else{
		}
		$getinvonce=$temp1;//單次下載發票本數
		if(isset($setup['zdninv']['getinvbyonce'])&&$setup['zdninv']['getinvbyonce']!=''){
			if(intval($getinvonce)>intval($setup['zdninv']['getinvbyonce'])){
				$getinvonce=$setup['zdninv']['getinvbyonce'];
			}
			else{
			}
		}
		else{
		}
	}
	else if(!isset($_GET['onceinv'])){
		/*if(isset($setup['zdninv']['getinvbyonce'])&&$setup['zdninv']['getinvbyonce']!=''){
			$getinvonce=$setup['zdninv']['getinvbyonce'];//單次下載發票本數
		}
		else{*/
			$getinvonce='0';//單次下載發票本數
		//}
	}
	else{
		$getinvonce='0';//單次下載發票本數
	}

	if(isset($getinvonce)&&intval($getinvonce)>0){
		/*取得中鼎token getCurToken.php*/
		$PostData =array(
			"taxid" => $setup['zdninv']['id'],
			"password" => $setup['zdninv']['psw']
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $setup['zdninv']['url'].'getCurToken');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$Result = curl_exec($ch);
		if(curl_errno($ch) !== 0) {
			print_r('cURL error when connecting to ' . $setup['zdninv']['url'].'getCurToken: ' . curl_error($setup['zdninv']['url'].'getCurToken'));
		}
		curl_close($ch);
		$Result=json_decode($Result,true);
		//print_r($Result);
		if(isset($Result['token'])){//成功取得token
			$token=$Result['token'];//中鼎token
			/*取得中鼎token getCurToken.php*/


			/*檢視剩餘發票 showTrack.php*/
			$header=array(
				"Accept:application/json",
				"Authorization:Bearer ".$token
			);

			$PostData =array(
				"taxid"=>$setup['zdninv']['id'],
				"period"=>$period
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $setup['zdninv']['url'].'showTrack');
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			// Edit: prior variable $postFields should be $postfields;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
			$Result = curl_exec($ch);
			$Result=json_decode($Result,true);
			//print_r($Result);
			if(curl_errno($ch) !== 0) {
				print_r('cURL error when connecting to ' . $setup['zdninv']['url'].'showTrack: ' . curl_error($setup['zdninv']['url'].'showTrack'));
			}
			curl_close($ch);

			$remaining=0;//剩餘發票本數
			//if(isset($Result['data'])){//2020/11/19 交換判斷順序
			if(isset($Result['error'])&&$Result['error']=='Unauthenticated.'){//2020/9/2 可能性：token過期。嘗試一次：更新token後重送
				/*更新中鼎token renew.php*/
				$PostData =array(
					"taxid" => $setup['zdninv']['id'],
					"password" => $setup['zdninv']['psw']
				);

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $setup['zdninv']['url'].'renew');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_POST, 1);
				// Edit: prior variable $postFields should be $postfields;
				curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
				//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$Result = curl_exec($ch);
				if(curl_errno($ch) !== 0) {
					print_r('cURL error when connecting to ' . $setup['zdninv']['url'].'renew: ' . curl_error($setup['zdninv']['url'].'renew'));
				}
				curl_close($ch);
				$Result=json_decode($Result,true);

				if(isset($Result['token'])){//成功取得token
					$token=$Result['token'];//中鼎token
					/*取得中鼎token renew.php*/


					/*檢視剩餘發票 showTrack.php*/
					$header=array(
						"Accept:application/json",
						"Authorization:Bearer ".$token
					);

					$PostData =array(
						"taxid"=>$setup['zdninv']['id'],
						"period"=>$period
					);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $setup['zdninv']['url'].'showTrack');
					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_POST, 1);
					// Edit: prior variable $postFields should be $postfields;
					curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
					$Result = curl_exec($ch);
					$Result=json_decode($Result,true);
					//print_r($Result);
					if(curl_errno($ch) !== 0) {
						print_r('cURL error when connecting to ' . $setup['zdninv']['url'].'showTrack: ' . curl_error($setup['zdninv']['url'].'showTrack'));
					}
					curl_close($ch);

					$remaining=0;//剩餘發票本數
					if(isset($Result['data'])){
						for($i=0;$i<sizeof($Result['data']);$i++){
							if($Result['data'][$i]['type']=='07'&&intval($Result['data'][$i]['used_booklet'])<intval($Result['data'][$i]['total_booklet'])){
								$remaining=intval($remaining)+intval($Result['data'][$i]['total_booklet'])-intval($Result['data'][$i]['used_booklet']);
							}
							else{
							}
						}
					}
					else{
					}
				}
				else{
					$finv=fopen('../../../../zdninvlog.txt','a');
					fwrite($finv,date('YmdHis').' ---- resend '.print_r($PostData,true).PHP_EOL.print_r($Result,true).PHP_EOL);
					fclose($finv);
					echo 'resend：';
					echo 'pw: '.$setup['zdninv']['psw'].' 帳號密碼認證錯誤';
					echo '<title>inv get finish</title>';
				}
			}
			else{
				//if(isset($Result['error'])&&$Result['error']=='Unauthenticated'){//2020/11/19 交換判斷順序//2020/9/2 可能性：token過期。嘗試一次：更新token後重送
				if(isset($Result['data'])){
					for($i=0;$i<sizeof($Result['data']);$i++){
						if($Result['data'][$i]['type']=='07'&&intval($Result['data'][$i]['used_booklet'])<intval($Result['data'][$i]['total_booklet'])){
							$remaining=intval($remaining)+intval($Result['data'][$i]['total_booklet'])-intval($Result['data'][$i]['used_booklet']);
						}
						else{
						}
					}
				}
				else{
				}
			}
			/*檢視剩餘發票 showTrack.php*/


			/*下載電子發票 getInv.php*/
			if($remaining>0){//尚有剩餘發票
				if($remaining<$getinvonce){
					$booklet=$remaining;
				}
				else{
					$booklet=$getinvonce;
				}
				$header=array(
						"Accept:application/json",
						"Authorization:Bearer ".$token
					);

				$PostData =array(
					  "taxid"=>$setup['zdninv']['id'],
					  "booklet"=>$booklet
					);

				$ch = curl_init();
				if($nowperiod==$period){//2020/8/31 下載當期發票
					curl_setopt($ch, CURLOPT_URL, $setup['zdninv']['url'].'getInv');
				}
				else{//$nowperiod!=$period//2020/8/31 下載下期發票
					curl_setopt($ch, CURLOPT_URL, $setup['zdninv']['url'].'getNextInv');
				}
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_POST, 1);
				// Edit: prior variable $postFields should be $postfields;
				curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
				$invlist = curl_exec($ch);
				$invlist=json_decode($invlist,true);
				
				if(isset($Result['data'][0])){
					foreach($Result['data'] as $k=>$d){
						if(isset($Result['data'][$k]['total_booklet'])){
							$invlist['total_booklet'][$k]=$Result['data'][$k]['total_booklet'];
						}
						else{
							$invlist['total_booklet'][$k]=0;
						}
						if(isset($Result['data'][$k]['used_booklet'])){
							$invlist['used_booklet'][$k]=$Result['data'][$k]['used_booklet'];
						}
						else{
							$invlist['used_booklet'][$k]=0;
						}
					}
				}
				else{
				}

				//print_r($invlist);
				if(curl_errno($ch) !== 0) {
					print_r('cURL error when connecting to ' . $setup['zdninv']['url'].'getInv: ' . curl_error($setup['zdninv']['url'].'getInv'));
				}
				curl_close($ch);
			}
			else{
				echo 'response:';
				print_r($Result);
				echo 'date: '.$period.' remaining is 0';
			}
			/*下載電子發票 getInv.php*/


			/*產生發票號碼檔案 create.invfile.php*/
			if(isset($invlist['success'])&&$invlist['success']){//成功下載發票
				$path='../../../../trnx/Number/'.$machine.'/';//發票號檔案統一路徑
				if(file_exists($path)){
				}
				else{
					mkdir($path);
				}
				foreach($invlist['data'] as $data){
					if(isset($setup['zdninv']['throw'])&&$setup['zdninv']['throw']!=''){
						$throw=intval($setup['zdninv']['throw'])*50;
						if(isset($setup['zdninv']['pass'])&&$setup['zdninv']['pass']!=''){
							$pass=intval($setup['zdninv']['pass'])*50;
						}
						else{
							$pass=0;
						}
						
						$temppath=$path.$data['period'].'/'.$data['type'].'/';
						if(file_exists($path.$data['period'])){
						}
						else{
							mkdir($path.$data['period']);
						}
						if(file_exists($path.$data['period'].'/'.$data['type'])){
						}
						else{
							mkdir($path.$data['period'].'/'.$data['type']);
						}
						$i=intval($data['start']);
						for(;$i<(intval($data['start'])+intval($throw));$i++){
							//$f=fopen($temppath.$data['track'].str_pad($i,8,'0',STR_PAD_LEFT).'.inv','w');
							//fclose($f);
							;
						}
						for(;$i<=min((intval($data['start'])+intval($throw)+intval($pass)-1),intval($data['end']));$i++){
							$f=fopen($temppath.$data['track'].str_pad($i,8,'0',STR_PAD_LEFT).'.inv','w');
							fclose($f);
						}
					}
					else{
						$temppath=$path.$data['period'].'/'.$data['type'].'/';
						if(file_exists($path.$data['period'])){
						}
						else{
							mkdir($path.$data['period']);
						}
						if(file_exists($path.$data['period'].'/'.$data['type'])){
						}
						else{
							mkdir($path.$data['period'].'/'.$data['type']);
						}
						for($i=intval($data['start']);$i<=intval($data['end']);$i++){
							$f=fopen($temppath.$data['track'].str_pad($i,8,'0',STR_PAD_LEFT).'.inv','w');
							fclose($f);
						}
					}
				}
				/*$f=fopen('../../../../print/zdninv/add.txt','w');
				fclose($f);*/
				$finv=fopen('../../../../zdninvlog.txt','a');
				fwrite($finv,date('YmdHis').' ---- '.print_r($invlist,true).PHP_EOL);
				fclose($finv);
			}
			else{
				/*$f=fopen('../../../../print/zdninv/add.txt','w');
				fclose($f);*/
				$finv=fopen('../../../../zdninvlog.txt','a');
				if(!isset($invlist)){
					fwrite($finv,date('YmdHis').' ---- invoice number of could is zero.'.PHP_EOL);
				}
				else{
					fwrite($finv,date('YmdHis').' ---- '.print_r($invlist,true).PHP_EOL);
				}
				fclose($finv);
			}
			/*產生發票號碼檔案 create.invfile.php*/
			echo '<title>inv get finish</title>';
		}
		else{
			$finv=fopen('../../../../zdninvlog.txt','a');
			fwrite($finv,date('YmdHis').' ---- '.print_r($PostData,true).PHP_EOL.print_r($Result,true).PHP_EOL);
			fclose($finv);
			echo 'pw: '.$setup['zdninv']['psw'].' 帳號密碼認證錯誤';
			echo '<title>inv get finish</title>';
		}
	}
	else{
		echo '<title>inv get finish</title>';
	}
}
?>