<?php
/*function php_curl_ajax1($method,$url,$header,$postdata){//2021/4/13 一開始使用該function，後續詢問後發現，對方單純使用JSON通訊，改由下方funtcion
	if($method=='get'){
		if(sizeof($postdata)>0){
			$url .= '?';
			foreach($postdata as $name=>$value){
				$url .= $name.'='.$value.'&';
			}
		}
		else{
		}
	}
	else{
	}
	$ch = curl_init();
	//echo '<br>url='.$url.'<br>';
	curl_setopt($ch, CURLOPT_URL, $url);//
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	if($method=='post'){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
	}
	else if($method=='put'){
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
	}
	else{
	}
	// Edit: prior variable $postFields should be $postfields;
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$tempdata = curl_exec($ch);
	$result=json_decode($tempdata,true);
	curl_close($ch);
	$res[]=$tempdata;
	$res[]=$result;
	return $res;
}*/
function php_curl_ajax($method,$url,$header,$postdata){
	/*if($method=='get'){//2021/4/13 該API無需使用該流程
		if(sizeof($postdata)>0){
			$url .= '?';
			foreach($postdata as $name=>$value){
				$url .= $name.'='.$value.'&';
			}
		}
		else{
		}
	}
	else{
	}*/
	$ch = curl_init();
	//echo '<br>url='.$url.'<br>';
	curl_setopt($ch, CURLOPT_URL, $url);//
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	if($method=='post'){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
	}
	else if($method=='put'){
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
	}
	else{
	}
	// Edit: prior variable $postFields should be $postfields;
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$tempdata = curl_exec($ch);
	$result=json_decode($tempdata,true);
	curl_close($ch);

	/*$f=fopen('./a1erp.log','a');
	fwrite($f,date('Y/m/d H:i:s').' -- get response = '.$tempdata.PHP_EOL);
	fclose($f);*/

	$res[]=$tempdata;
	$res[]=$result;
	return $res;
}
function check_url($url,$find){
	// 建一個cURL
	$ch = curl_init();

	// 設置cURL參數
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// 執行cURL
	curl_exec($ch);

	$output = curl_exec($ch);
	// if($output === FALSE ){
		// echo "CURL Error:".curl_error($ch); 
	// }

	// 取得資訊
	$info = curl_getinfo($ch);

	// 取得狀態碼
	$http_code = $info["http_code"];

	// 關閉cURL
	curl_close($ch);

	// 判斷網站是否正常
	// 正常回傳true，錯誤回傳false
	if ($http_code == $find){
		return true;
	} else {
		return $output;
	}
}
function Login($url,$method,$UserName,$Password){
	//$header = array('Content-Type: application/x-www-form-urlencoded');
	$header = array('Content-Type: application/json','Accept: application/json');
	$PostData = array(
		'UserName' => $UserName,
		'Password' => $Password,
	);
	return php_curl_ajax($method,$url.'Login',$header,$PostData);
}
function Customers($url,$method,$key,$ID='',$ShortName='',$CellPhone=''){//2021/4/13 GET:取得客戶列表 POST:上傳客戶資料 PUT:修改客戶資料
	$header = array('Content-Type: application/json','Accept: application/json','Authorization:'.$key);
	if($method=='get'){
		$PostData = array();
	}
	else{
		if($method=='put'){
			$PostData = array(
				'ShortName' => $ShortName,
				'CellPhone' => $CellPhone,
			);
		}
		else{
			$PostData = array(
				'ID' => $ID,
				'ShortName' => $ShortName,
				'CellPhone' => $CellPhone,
			);
		}
	}
	if($method=='put'||($method=='get'&&$ID!='')){
		return php_curl_ajax($method,$url.'Customers/'.$ID,$header,$PostData);
	}
	else{
		return php_curl_ajax($method,$url.'Customers',$header,$PostData);
	}
}
function Categorys($url,$method,$key,$Name='',$Seq=''){//2021/4/12 GET:取得商品分類列表 POST:上傳商品分類 PUT:修改商品分類(對於我們POS來說幾乎沒用。因為API是使用分類名稱作為索引值，只能修改顯示順序。)
	//$header = array('Content-Type: application/x-www-form-urlencoded','Authorization:'.$key);
	$header = array('Content-Type: application/json','Accept: application/json','Authorization:'.$key);
	if($method=='get'){
		$PostData = array();
	}
	else{
		$PostData = array(
			'Name' => $Name,
			'Seq' => $Seq,
		);
	}
	return php_curl_ajax($method,$url.'Categorys',$header,$PostData);
}
function Items($url,$method,$key,$ID='',$Name='',$StockUnit='',$CategoryName='',$StdPurPrice=''){
	$header = array('Content-Type: application/json','Accept: application/json','Authorization:'.$key);
	if($method=='get'){
		$PostData = array();
	}
	else{
		if($method=='post'){
			$PostData = array(
				'ID' => $ID,
				'Name' => $Name,
				'StockUnit' => $StockUnit,
				'CategoryName' => $CategoryName,
				'IsStock' => 1,
				'StdPurPrice' => $StdPurPrice,
				'IsTaxStdPurPrice' => 1,
				'Type' => 1,
			);
		}
		else{
			$PostData = array(
				'Name' => $Name,
				'StockUnit' => $StockUnit,
				'CategoryName' => $CategoryName,
				'StdPurPrice' => $StdPurPrice,
				'IsTaxStdPurPrice' => 1,
				'Type' => 1,
			);
		}
	}
	if($method=='put'||($method=='get'&&$ID!='')){
		return php_curl_ajax($method,$url.'Items/'.$ID,$header,$PostData);
	}
	else{
		return php_curl_ajax($method,$url.'Items',$header,$PostData);
	}
}
function Sales($url,$method,$key,$ID,$TradeDate,$CustomerID,$Payment,$InvoiceType,$InvoiceDate,$Invoice,$TaxID,$TaxType,$TaxRate,$TotalTax,$TotalSaleAmount,$SaleDetails,$OtherAccount,$Memo){
	$check=check_url($url,'200');
	if($check===true||$check!=''){
		$header = array('Content-Type: application/json','Accept: application/json','Authorization:'.$key);
		if($Payment=='3'){
			$PostData = array(
				'ID' => preg_replace('/-/','',$TradeDate).$ID,
				'TradeDate' => $TradeDate,
				'CustomerID' => $CustomerID,
				'Payment' => $Payment,
				'InvoiceType' => $InvoiceType,
				'InvoiceDate' => $InvoiceDate,
				'Invoice' => $Invoice,
				'TaxID' => $TaxID,
				'TaxType' => $TaxType,
				'TaxRate' => $TaxRate,
				'TotalTax' => $TotalTax,
				'TotalSaleAmount' => $TotalSaleAmount,
				'Memo' => $Memo,
				'SaleDetails' => $SaleDetails,
				'ToAccount' => $OtherAccount,//2021/11/16 OtherAccount 換成 ToAccount
			);
		}
		else{
			$PostData = array(
				'ID' => preg_replace('/-/','',$TradeDate).$ID,
				'TradeDate' => $TradeDate,
				'CustomerID' => $CustomerID,
				'Payment' => $Payment,
				'InvoiceType' => $InvoiceType,
				'InvoiceDate' => $InvoiceDate,
				'Invoice' => $Invoice,
				'TaxID' => $TaxID,
				'TaxType' => $TaxType,
				'TaxRate' => $TaxRate,
				'TotalTax' => $TotalTax,
				'TotalSaleAmount' => $TotalSaleAmount,
				'Memo' => $Memo,
				'SaleDetails' => $SaleDetails,
			);
		}
		
		$res=php_curl_ajax($method,$url.'Sales',$header,$PostData);
		/*$f=fopen('./a1SALElog.log','a');
		fwrite($f,date('Y/m/d H:i:s').' --- request: '.print_r($PostData,true).PHP_EOL);
		fwrite($f,date('Y/m/d H:i:s').' --- response: '.print_r($res,true).PHP_EOL);
		fclose($f);*/
		return $res;
	}
	else{
		/*$f=fopen('./a1SALElog.log','a');
		fwrite($f,date('Y/m/d H:i:s').' --- can not connect url: '.$url.PHP_EOL);
		fclose($f);*/
		return 'offonline';
	}
}
function SaleReturns($url,$method,$key,$ID,$TradeDate,$CustomerID,$Payment,$InvoiceType,$InvoiceDate,$Invoice,$TaxID,$TaxType,$TaxRate,$TotalTax,$TotalReturnAmount,$SaleReturnDetails,$Memo,$SaleNo){
	$header = array('Content-Type: application/json','Accept: application/json','Authorization:'.$key);
	$PostData = array(
		'ID' => preg_replace('/-/','',$TradeDate).$ID,
		'TradeDate' => $TradeDate,
		'CustomerID' => $CustomerID,
		'Payment' => $Payment,
		'InvoiceType' => $InvoiceType,
		'InvoiceDate' => $InvoiceDate,
		'Invoice' => $Invoice,
		'TaxID' => $TaxID,
		'TaxType' => $TaxType,
		'TaxRate' => $TaxRate,
		'TotalTax' => $TotalTax,
		'TotalReturnAmount' => $TotalReturnAmount,
		'SaleNo' => $SaleNo,
		'Memo' => $Memo,
		'SaleReturnDetails' => $SaleReturnDetails,
	);
	/*$f=fopen('./a1erp.log','a');
	fwrite($f,date('Y/m/d H:i:s').' -- pass data = '.print_r($PostData,true).PHP_EOL);
	fclose($f);*/
	return php_curl_ajax($method,$url.'SaleReturns',$header,$PostData);
}
function Stock($url,$method,$key,$ID){
	$header = array('Content-Type: application/json','Accept: application/json','Authorization:'.$key);
	$PostData = array();
	return php_curl_ajax($method,$url.'Stock/'.$ID,$header,$PostData);
}
?>