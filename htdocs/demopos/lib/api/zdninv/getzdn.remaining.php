<title>invdownload</title>
<?php
/*由first呼叫下載發票*/
date_default_timezone_set('Asia/Taipei');
if(!isset($_GET['date'])||$_GET['date']!=''){
	$Y=date('Y')-1911;
	$m=date('m');
	if($m%2==1){
		$m=$m+1;
	}
	else{
	}
	$m=str_pad($m,2,'0',STR_PAD_LEFT);
	$period=$Y.$m;
}
else{
	$period=$_GET['date'];
}
?>
<script src="../../../../tool/jquery-1.12.4.js?<?php echo date('YmdHis'); ?>"></script>
<script src="./zdn_api.js?<?php echo date('YmdHis'); ?>"></script>
<?php
//$setup=parse_ini_file('../../../../database/setup.ini',true);
/*if($setup['basic']['Identifier']!=$setup['zdninv']['id']||$setup['zdninv']['id']==''){
	echo '<title>inv get finish</title>';
}
else{*/
	$useurl='http://eiv2.zdn.tw:89/public/api/';
	echo 'useurl=>'.$useurl.'<br>';
	$machine=$_GET['machine'];//機號
	$getinvonce='1';//單次下載發票本數

	if(isset($getinvonce)&&intval($getinvonce)>0){
		/*取得中鼎token getCurToken.php*/
		$PostData =array(
			"taxid" => $_GET['id'],
			"password" => $_GET['psw']
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $useurl.'getCurToken');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$Result = curl_exec($ch);
		if(curl_errno($ch) !== 0) {
			print_r('cURL error when connecting to ' . $useurl.'getCurToken: ' . curl_error($useurl.'getCurToken'));
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
				"taxid"=>$_GET['id'],
				"period"=>$period
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $useurl.'showTrack');
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			// Edit: prior variable $postFields should be $postfields;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
			$Result = curl_exec($ch);
			$Result=json_decode($Result,true);
			echo 'showTrack =><br>';
			echo 'senddata: <br>';
			print_r($PostData);
			echo '<br>';
			print_r($Result);
			if(curl_errno($ch) !== 0) {
				print_r('cURL error when connecting to ' . $useurl.'showTrack: ' . curl_error($useurl.'showTrack'));
			}
			echo '<br>';
			curl_close($ch);
			/*檢視剩餘發票 showTrack.php*/
			//echo $Result['error'];
			if(isset($Result['error'])&&$Result['error']=='Unauthenticated.'){//2020/10/27 可能性：token過期。嘗試一次：更新token後重送
				/*更新中鼎token renew.php*/
				$PostData =array(
					"taxid" => $_GET['id'],
					"password" => $_GET['psw']
				);

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $useurl.'renew');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_POST, 1);
				// Edit: prior variable $postFields should be $postfields;
				curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
				//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$Result = curl_exec($ch);
				if(curl_errno($ch) !== 0) {
					print_r('cURL error when connecting to ' . $useurl.'renew: ' . curl_error($useurl.'renew'));
				}
				curl_close($ch);
				echo 'renew<br>';
				$Result=json_decode($Result,true);
				//print_r($Result);
				if(isset($Result['token'])){//成功取得token
					$token=$Result['token'];//中鼎token
					/*取得中鼎token renew.php*/


					/*檢視剩餘發票 showTrack.php*/
					$header=array(
						"Accept:application/json",
						"Authorization:Bearer ".$token
					);

					$PostData =array(
						"taxid"=>$_GET['id'],
						"period"=>$period
					);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $useurl.'showTrack');
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
					$Result=json_decode($Result,true);
					echo 'resend-showTrack =><br>';
					echo 'resend-senddata: <br>';
					print_r($PostData);
					echo '<br>';
					print_r($Result);
					if(curl_errno($ch) !== 0) {
						print_r('cURL error when connecting to ' . $useurl.'showTrack: ' . curl_error($useurl.'showTrack'));
					}
					curl_close($ch);
				}
				else{
				}
			}
			else{
			}
		}
		else{
			echo 'pw: '.$_GET['psw'].' 帳號密碼認證錯誤';
			echo '<title>inv get finish</title>';
		}
	}
	else{
		echo '<title>inv get finish</title>';
	}
//}
?>