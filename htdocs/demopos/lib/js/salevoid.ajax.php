<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
require_once '../../../tool/PHPWord.php';
$data=parse_ini_file('../../../database/setup.ini',true);
if(isset($data['a1erp']['warehouse'])){//2021/10/25 讀取倉庫名稱
	$warehouse=$data['a1erp']['warehouse'];
}
else{
	$warehouse='公司倉';
}
$content=parse_ini_file('../../../database/initsetting.ini',true);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
if(isset($content['a1'])&&$content['a1']['usea1erp']=='1'){//2021/4/14 0>>關閉a1ERP串接1>>開啟a1ERP串接
	include_once '../api/a1erp/a1_api.inc.php';
}
else{
}
//date_default_timezone_set('Asia/Taipei');

if(isset($print['clientlist']['invsize'])){
}
else{
	$print['clientlist']['invsize']="20";
}
date_default_timezone_set($content['init']['settime']);

if(isset($content['init']['posdvr'])&&$content['init']['posdvr']=='1'){
	$tempposdvr=date('YmdHis');
	$posdvr=fopen('../../../print/posdvr/'.$tempposdvr.';'.$_POST['terminalnumber'].'.txt','w');
	$tempdvrcontent='';
}
else{
}
if(file_exists('../../syspram/clientlist-'.$content['init']['firlan'].'.ini')){
	$list=parse_ini_file('../../syspram/clientlist-'.$content['init']['firlan'].'.ini',true);
}
else if(file_exists('../../syspram/clientlist-1.ini')){
	$list=parse_ini_file('../../syspram/clientlist-1.ini',true);
}
else if(file_exists('../../syspram/clientlist-TW.ini')){
	$list=parse_ini_file('../../syspram/clientlist-TW.ini',true);
}
else{
	$list='-1';
}
if(isset($content['init']['kvm'])&&$content['init']['kvm']=='1'){
	if(file_exists('../../../print/kvm/'.$_POST['bizdate'].';'.$_POST['consecnumber'].'.ini')){
		unlink('../../../print/kvm/'.$_POST['bizdate'].';'.$_POST['consecnumber'].'.ini');
	}
	else{
	}
}
else{
}

$date=$_POST['bizdate'];
$credate=$_POST['createdatetime'];

if(strlen($_POST['consecnumber'])<7){
	//$listno=intval($_POST['consecnumber']);
	//2022/1/20 因為foodpanda的訂單編號前面不是為數字，所以如繼續使用整數型別，會導致無法正常作廢
	$listno=$_POST['consecnumber'];
}
else{//2020/7/10 for 正航串接發票，因為帳單號超出int範圍
	$listno=$_POST['consecnumber'];
}

if($content['init']['onlinemember']=='1'&&$_POST['memno']!=''){//網路會員
	$PostData = array(
		"company" => $data['basic']['company'],
		"bizdate" => $date,
		"consecnumber" => $listno
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/voidsalelist.ajax.php');//
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	// Edit: prior variable $postFields should be $postfields;
	curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	$getmemdata = curl_exec($ch);
	//2020/5/25 $memdata=json_decode($getmemdata,1);
	if(curl_errno($ch) !== 0) {
		print_r('cURL error when connecting to http://api.tableplus.com.tw/outposandorder/memberapi/voidsalelist.ajax.php : ' . curl_error($ch));
	}
	else{
		$memdata=json_decode($getmemdata,1);
	}
	curl_close($ch);
}
else if($content['init']['onlinemember']=='0'&&$_POST['memno']!=''){//本地會員
	$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
	$sql='UPDATE salemap SET state=0 WHERE bizdate="'.$date.'" AND consecnumber="'.$listno.'"';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}

$conn=sqlconnect('../../../database/sale','SALES_'.substr($date,0,6).'.db','','','','sqlite');

//2022/2/15 quickclick串接 作廢
if(isset($content['init']['quickclick'])&&$content['init']['quickclick']=='1'){
	$sql="SELECT CLKCODE FROM CST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '".$credate."%'";
	//echo $sql;
	$clkcode=sqlquery($conn,$sql,'sqlite');
	if(isset($clkcode[0]['CLKCODE'])){
	}
	else{
		$sql="SELECT CLKCODE FROM tempCST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '".$credate."%'";
		$clkcode=sqlquery($conn,$sql,'sqlite');
	}

	$ts = strtotime(date('YmdHis'));
	//$secret = 'acc6fe509825b263eb9aa0bee09e96bb120f195d';
	$secret = $data['quickclick']['secret'];
	$sig = hash_hmac('sha256', $ts, $secret, true);
	$res = base64_encode($sig);
	//echo $ts.PHP_EOL;
	//$accesskeyid = 'S_20220104124376';
	$accesskeyid = $data['quickclick']['accesskeyid'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $data['quickclick']['url']."/orders/".$clkcode[0]['CLKCODE']."/cancel");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{
	  \"reason\": \"帳單作廢\"
	}");

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  "Content-Type: application/json",
	  "Authorization: QC ".$accesskeyid.':'.$res,
	  "Seed: ".$ts
	));

	$response = curl_exec($ch);
	curl_close($ch);
}
else{
}

$sql="SELECT COUNT(*) AS number,* FROM CST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '".$credate."%'";
//echo $sql;
$c=sqlquery($conn,$sql,'sqlite');
$sql="SELECT COUNT(*) AS number FROM tempCST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '".$credate."%'";
$tempc=sqlquery($conn,$sql,'sqlite');
$sql="SELECT * FROM salemap WHERE bizdate='".$date."' AND consecnumber='".str_pad($listno,6,'0',STR_PAD_LEFT)."'";
$saleno=sqlquery($conn,$sql,'sqlite');

if($c[0]['CLKCODE']=='web'&&$c[0]['CLKNAME']=='網路訂購'&&isset($saleno[0]['onlinebizdate'])&&isset($saleno[0]['onlineconsecnumber'])){
	$PostData = array(
		"type"=> 'sale',
		"company"=> $data['basic']['company'],
		"bizdate" => $saleno[0]['onlinebizdate'],
		"consecnumber" => $saleno[0]['onlineconsecnumber']
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/orderweb/lib/js/webchangelist.ajax.php');//
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	// Edit: prior variable $postFields should be $postfields;
	curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	$getmemdata = curl_exec($ch);
	//2020/5/25 $memdata=json_decode($getmemdata,1);
	if(curl_errno($ch) !== 0) {
		//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
	}
	else{
		$memdata=json_decode($getmemdata,1);
	}
	curl_close($ch);
}
else{
}

if($c[0]['number']>=1){
	date_default_timezone_set($content['init']['settime']);
	$sql='UPDATE CST011 SET NBCHKDATE="'.date("YmdHis").'",NBCHKTIME="'.$_POST['name'].'",NBCHKNUMBER="Y"';
	if(isset($_POST['remarks'])){
		$sql=$sql.',REMARKS="'.$_POST['remarks'].'-"||(SELECT REMARKS FROM CST011 WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'" AND CREATEDATETIME LIKE "'.$credate.'%") WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'" AND CREATEDATETIME LIKE "'.$credate.'%"';
	}
	else{
		$sql=$sql.' WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'" AND CREATEDATETIME LIKE "'.$credate.'%"';
	}
	//echo $sql;
	
	sqlnoresponse($conn,$sql,'sqlite');
	$sql="SELECT * FROM CST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '".$credate."%'";
	$saledata=sqlquery($conn,$sql,'sqlite');
	$sql="SELECT * FROM CST012 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '".$credate."%' ORDER BY LINENUMBER";
	$salelist=sqlquery($conn,$sql,'sqlite');
	//print_r($salelist);
	sqlclose($conn,'sqlite');

	if(isset($content['a1'])&&$content['a1']['usea1erp']=='1'){//2021/4/14 0>>關閉a1ERP串接1>>開啟a1ERP串接
		$a1011=$saledata;
		$a1012=$salelist;
		if(isset($data['a1erp'])){
			$url=$data['a1erp']['url'];
			$id=$data['a1erp']['id'];
			$pw=$data['a1erp']['pw'];
			
			$SaleReturnDetails=array();
			$linenumber=1;
			$index=0;

			$menucode=sqlconnect('../../../database','menu.db','','','','sqlite');
			for($i=0;$i<sizeof($a1012);$i++){
				if(is_numeric($a1012[$i]['ITEMCODE'])){
					$sql='SELECT * FROM itemsdata WHERE inumber="'.intval($a1012[$i]['ITEMCODE']).'"';
					$code=sqlquery($menucode,$sql,'sqlite');
					$index=sizeof($SaleReturnDetails);
					$SaleReturnDetails[$index]['ID']=$linenumber;
					if(isset($code[0]['erpcode'])&&$code[0]['erpcode']!=''&&$code[0]['erpcode']!=null){
						$SaleReturnDetails[$index]['ItemID']=$code[0]['erpcode'];
					}
					else{
						$SaleReturnDetails[$index]['ItemID']=intval($a1012[$i]['ITEMCODE']);
					}
					$SaleReturnDetails[$index]['Qty']=$a1012[$i]['QTY'];
					$SaleReturnDetails[$index]['Amount']=$a1012[$i]['AMT'];
					$SaleReturnDetails[$index]['Warehouse']=$warehouse;

					if(isset($a1012[$i]['SELECTIVEITEM1'])&&$a1012[$i]['SELECTIVEITEM1']!=''&&$a1012[$i]['SELECTIVEITEM1']!=null){
						$tastestring='';
						$tasteno=preg_split('/\,/',$a1012[$i]['SELECTIVEITEM1']);
						for($t=0;$t<sizeof($tasteno);$t++){
							if(substr($tasteno[$t],0,5)=='99999'){//開放備註
								if($tastestring!=''){
									$tastestring .= ',';
								}
								else{
								}
								$tastestring .= substr($tasteno[$t],7);
							}
							else{
								if(isset($taste[intval(substr($tasteno[$t],0,5))])){
									if($tastestring!=''){
										$tastestring .= ',';
									}
									else{
									}
									$tastestring .= $taste[intval(substr($tasteno[$t],0,5))]['name1'];
								}
								else{
								}
							}
						}
						$SaleReturnDetails[$index]['Memo']=$tastestring;
					}
					else{
						$SaleReturnDetails[$index]['Memo']='';
					}
					/*if(isset($a1011[0]['INVOICENUMBER'])&&$a1011[0]['INVOICENUMBER']!=''){
						$SaleReturnDetails[$index]['NonTaxAmount']=($a1012[$i]['AMT']/1.05);
						$SaleReturnDetails[$index]['Tax']=$a1012[$i]['AMT']-$SaleReturnDetails[$index]['NonTaxAmount'];
					}
					else{
						$SaleReturnDetails[$index]['NonTaxAmount']=$a1012[$i]['AMT'];
						$SaleReturnDetails[$index]['Tax']=0;
					}*/

					$linenumber++;
				}
				else{
					if($a1012[$i]['ITEMCODE']=='item'){//商品促銷
						$SaleReturnDetails[$index]['Amount']=floatval($SaleReturnDetails[$index]['Amount'])+floatval($a1012[$i]['AMT']);
						/*if(isset($a1011[0]['INVOICENUMBER'])&&$a1011[0]['INVOICENUMBER']!=''){
							$SaleReturnDetails[$index]['NonTaxAmount']=($SaleReturnDetails[$index]['Amount']/1.05);
							$SaleReturnDetails[$index]['Tax']=$SaleReturnDetails[$index]['Amount']-$SaleReturnDetails[$index]['NonTaxAmount'];
						}
						else{
							$SaleReturnDetails[$index]['NonTaxAmount']=floatval($SaleReturnDetails[$index]['NonTaxAmount'])+floatval($a1012[$i]['AMT']);
						}*/
					}
					else if($a1012[$i]['ITEMCODE']=='list'){//帳單優惠
						$index=sizeof($SaleReturnDetails);
						$SaleReturnDetails[$index]['ID']=$linenumber;
						$SaleReturnDetails[$index]['ItemID']='D999999999';
						$SaleReturnDetails[$index]['Qty']=1;
						$SaleReturnDetails[$index]['Amount']=$a1012[$i]['AMT'];
						$SaleReturnDetails[$index]['Warehouse']=$warehouse;
						$linenumber++;
					}
					else if($a1012[$i]['ITEMCODE']=='autodis'){//系統自動優惠
						$index=sizeof($SaleReturnDetails);
						$SaleReturnDetails[$index]['ID']=$linenumber;
						$SaleReturnDetails[$index]['ItemID']='D999999998';
						$SaleReturnDetails[$index]['Qty']=1;
						$SaleReturnDetails[$index]['Amount']=$a1012[$i]['AMT'];
						$SaleReturnDetails[$index]['Warehouse']=$warehouse;
						$linenumber++;
					}
					else{
					}
				}
			}

			$login=Login($url,"post",$id,$pw);
			$key=$login[1]['access_token'];
			$ID=$data['basic']['story'].$a1011[0]['CONSECNUMBER'];
			$TradeDate=substr($a1011[0]['BIZDATE'],0,4).'-'.substr($a1011[0]['BIZDATE'],4,2).'-'.substr($a1011[0]['BIZDATE'],6,2);
			if(isset($a1011[0]['CUSTCODE'])&&$a1011[0]['CUSTCODE']!=''){
				$memberno=preg_split('/;-;/',$a1011[0]['CUSTCODE']);

				$PostData = array(
					"ajax"=> '',
					"type"=> 'online',
					"company" => $data['basic']['company'],
					"memno" => $memberno[0]
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
				$memdata=json_decode($memdata,1);
				if(curl_errno($ch) !== 0) {
					//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
				}
				curl_close($ch);
				
				if(isset($memdata[0]['a1code'])&&$memdata[0]['a1code']!=''&&$memdata[0]['a1code']!=null){
					$CustomerID=$memdata[0]['a1code'];
				}
				else{
					$CustomerID='P'.str_pad(substr($memberno[0],strlen($data['basic']['story'])),9,'0',STR_PAD_LEFT);
				}
				//$CustomerID='P'.str_pad(substr($memberno[0],strlen($data['basic']['story'])),9,'0',STR_PAD_LEFT);
			}
			else{
				$CustomerID='P999999999';
			}
			$Payment='1';
			if(isset($a1011[0]['INVOICENUMBER'])&&$a1011[0]['INVOICENUMBER']!=''){
				if(intval(substr($a1011[0]['BIZDATE'],4,2))%2==1){
					$invmonth=date('Ym',strtotime(substr($a1011[0]['BIZDATE'],0,4).'-'.substr($a1011[0]['BIZDATE'],4,2).'-01 +1 month'));
				}
				else{
					$invmonth=substr($a1011[0]['BIZDATE'],0,6);
				}

				if(file_exists('../../../database/sale/'.$invmonth.'/invdata_'.$invmonth.'_m1.db')){
					$invconn=sqlconnect('../../../database/sale/'.$invmonth,'invdata_'.$invmonth.'_m1.db','','','','sqlite');
					$invsql='SELECT * FROM invlist WHERE invnumber="'.$a1011[0]['INVOICENUMBER'].'"';
					$invdata=sqlquery($invconn,$invsql,'sqlite');
					sqlclose($invconn,'sqlite');

					if(!isset($invdata[0]['invnumber'])){
						$invdata='';

						$invmonth=date('Ym',strtotime(substr($invmonth,0,4).'-'.substr($invmonth,4,2).'-01 -2 month'));
						if(file_exists('../../../database/sale/'.$invmonth.'/invdata_'.$invmonth.'_m1.db')){
							$invconn=sqlconnect('../../../database/sale/'.$invmonth,'invdata_'.$invmonth.'_m1.db','','','','sqlite');
							$invsql='SELECT * FROM invlist WHERE invnumber="'.$a1011[0]['INVOICENUMBER'].'"';
							$invdata=sqlquery($invconn,$invsql,'sqlite');
							sqlclose($invconn,'sqlite');

							if(!isset($invdata[0]['invnumber'])){
								$invdata='';
							}
							else{
							}
						}
						else{
							$invdata='';
						}
					}
					else{
					}
				}
				else{
					$invmonth=date('Ym',strtotime(substr($invmonth,0,4).'-'.substr($invmonth,4,2).'-01 -2 month'));
					if(file_exists('../../../database/sale/'.$invmonth.'/invdata_'.$invmonth.'_m1.db')){
						$invconn=sqlconnect('../../../database/sale/'.$invmonth,'invdata_'.$invmonth.'_m1.db','','','','sqlite');
						$invsql='SELECT * FROM invlist WHERE invnumber="'.$a1011[0]['INVOICENUMBER'].'"';
						$invdata=sqlquery($invconn,$invsql,'sqlite');
						sqlclose($invconn,'sqlite');

						if(!isset($invdata[0]['invnumber'])){
							$invdata='';
						}
						else{
						}
					}
					else{
						$invdata='';
					}
				}
				if(isset($invdata[0]['invnumber'])){
					$InvoiceDate=substr($invdata[0]['createdate'],0,4).'-'.substr($invdata[0]['createdate'],4,2).'-'.substr($invdata[0]['createdate'],6,2);
					if($invdata[0]['buyerid']!='0000000000'){
						$TaxID=$invdata[0]['buyerid'];
					}
					else{
						$TaxID='';
					}
					$inimoney=intval(intval($invdata[0]['totalamount'])/1.05);
					$TotalTax=intval($invdata[0]['totalamount'])-intval($inimoney);
				}
				else{
					$InvoiceDate='';
					$TaxID='';
					$TotalTax=0;
				}
				$InvoiceType='7';
				$Invoice=$a1011[0]['INVOICENUMBER'];
				$TaxType='4';
				$TaxRate=0.05;
			}
			else{
				$InvoiceType='0';
				$InvoiceDate='';
				$Invoice='';
				$TaxID='';
				$TaxType='0';
				$TaxRate=0;
				$TotalTax=0;
			}
			$TotalSaleAmount=$a1011[0]['SALESTTLAMT'];
			if(isset($a1011[0]['RELINVOICENUMBER'])&&$a1011[0]['RELINVOICENUMBER']!=''&&$a1011[0]['RELINVOICENUMBER']!=null){
				if(preg_match('/(A1\d*A1)/',$a1011[0]['RELINVOICENUMBER'])){
					preg_match('/(A1\d*A1)/',$a1011[0]['RELINVOICENUMBER'],$listhint);
					if(isset($listhint[0])&&$listhint[0]!=''){
						$SaleNo=substr($listhint[0],2,-2);
					}
					else{
						$SaleNo='';
					}
					$tempMemo=preg_split('/(A1\d*A1)/',$a1011[0]['RELINVOICENUMBER']);
					$Memo='';
					for($i=0;$i<sizeof($tempMemo);$i++){
						$Memo.=$tempMemo[$i];
					}
				}
				else{
					$Memo=$a1011[0]['RELINVOICENUMBER'];
				}
			}
			else{
				$SaleNo='';
				$Memo='';
			}
			
			$res=SaleReturns($url,'post',$key,$ID,$TradeDate,$CustomerID,$Payment,$InvoiceType,$InvoiceDate,$Invoice,$TaxID,$TaxType,$TaxRate,$TotalTax,$a1011[0]['SALESTTLAMT'],$SaleReturnDetails,$Memo,$SaleNo);
			//print_r($res);

			/*$f=fopen('./member.log','a');
			fwrite($f,date('Y/m/d H:i:s').' '.print_r($res,true).PHP_EOL);
			fclose($f);*/
		}
		else{
		}
	}
	else{
	}

	$PHPWord = new PHPWord();
	$document = $PHPWord->loadTemplate('../../../template/void.docx');
	$document->setValue('title',$buttons['name']['billfun29']);
	if(strstr($saledata[0]['REMARKS'],'-')){
		$tempsaledata=preg_split('/-/',$saledata[0]['REMARKS']);
		if(isset($saleno[0]['saleno'])){
			if($tempsaledata[1]=='1'){
				$document->setValue('type',$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
			}
			else if($tempsaledata[1]=='2'){
				$document->setValue('type',$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
			}
			else if($tempsaledata[1]=='3'){
				$document->setValue('type',$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
			}
			else{
				$document->setValue('type',$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
			}
		}
		else{
			if($tempsaledata[1]=='1'){
				$document->setValue('type',$buttons['name']['listtype1'].' '.$saledata[0]['TABLENUMBER']);
			}
			else if($tempsaledata[1]=='2'){
				$document->setValue('type',$buttons['name']['listtype2'].' '.$saledata[0]['TABLENUMBER']);
			}
			else if($tempsaledata[1]=='3'){
				$document->setValue('type',$buttons['name']['listtype3'].' '.$saledata[0]['TABLENUMBER']);
			}
			else{
				$document->setValue('type',$buttons['name']['listtype4'].' '.$saledata[0]['TABLENUMBER']);
			}
		}
	}
	else{
		if(isset($saleno[0]['saleno'])){
			if($saledata[0]['REMARKS']=='1'){
				$document->setValue('type',$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
			}
			else if($saledata[0]['REMARKS']=='2'){
				$document->setValue('type',$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
			}
			else if($saledata[0]['REMARKS']=='3'){
				$document->setValue('type',$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
			}
			else{
				$document->setValue('type',$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
			}
		}
		else{
			if($saledata[0]['REMARKS']=='1'){
				$document->setValue('type',$buttons['name']['listtype1'].' '.$saledata[0]['TABLENUMBER']);
			}
			else if($saledata[0]['REMARKS']=='2'){
				$document->setValue('type',$buttons['name']['listtype2'].' '.$saledata[0]['TABLENUMBER']);
			}
			else if($saledata[0]['REMARKS']=='3'){
				$document->setValue('type',$buttons['name']['listtype3'].' '.$saledata[0]['TABLENUMBER']);
			}
			else{
				$document->setValue('type',$buttons['name']['listtype4'].' '.$saledata[0]['TABLENUMBER']);
			}
		}
	}
	/*if($saledata[0]['REMARKS']=='1'){
		$document->setValue('type',$buttons['name']['listtype1'].' '.$saledata[0]['TABLENUMBER']);
	}
	else if($saledata[0]['REMARKS']=='2'){
		$document->setValue('type',$buttons['name']['listtype2'].' '.$saledata[0]['TABLENUMBER']);
	}
	else{
		$document->setValue('type',$buttons['name']['listtype3'].' '.$saledata[0]['TABLENUMBER']);
	}*/
	$document->setValue('consecnumber',intval($saledata[0]['CONSECNUMBER']));
	date_default_timezone_set($content['init']['settime']);
	$document->setValue('datetime',date('Y/m/d H:m:i'));
	if(isset($data['basic']['address'])&&$data['basic']['address']!=''){
		$document->setValue('address', $data['basic']['address']);
	}
	else{
	}
	if(isset($data['basic']['tel'])&&$data['basic']['tel']!=''){
		$document->setValue('phone', $data['basic']['tel']);
	}
	else{
	}
	$document->setValue('story',$data['basic']['storyname']);
	$tindex=0;

	$table='';

	//2020/9/18 發票資訊
	if(isset($saledata[0]['INVOICENUMBER'])&&$saledata[0]['INVOICENUMBER']!=''){
		//考慮是否放入載具、統編資訊
		$table .= '<w:p w:rsidR="008C4EB0" w:rsidRPr="001E039E" w:rsidRDefault="00FC09EC" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['clientlist']['invsize'].'"/><w:szCs w:val="'.$print['clientlist']['invsize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['clientlist']['invsize'].'"/><w:szCs w:val="'.$print['clientlist']['invsize'].'"/></w:rPr><w:t>';
		if($list!='-1'&&isset($list['name']['invoicenumber'])){
			$table.=$list['name']['invoicenumber'].":";
		}
		else{
			$table .= "發票號碼:";
		}
		$table .= $saledata[0]['INVOICENUMBER'].'</w:t></w:r></w:p>';
	}
	else{
	}

	$table .= '<w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>-----------------------------------------------</w:t></w:r></w:p><w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "Items";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "U/P";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "Sub";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	
	$invitem=0;
	$totalqty=0;
	$listdis=0;
	$charge=0;
	for($i=0;$i<sizeof($salelist);$i=$i+2){
		if($salelist[$i]['ITEMCODE']!='item'&&$salelist[$i]['ITEMCODE']!='list'&&$salelist[$i]['ITEMCODE']!='autodis'&&$salelist[$i]['ITEMCODE']!='member'){
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="10"/></w:rPr><w:t>';
			if(strlen($salelist[$i]['UNITPRICELINK'])==0){
				$table .= $salelist[$i]['ITEMNAME'].' x '.$salelist[$i]['QTY'];
			}
			else{
				$table .= $salelist[$i]['ITEMNAME'].' ( '.$salelist[$i]['UNITPRICELINK'].' ) x '.$salelist[$i]['QTY'];
			}
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			$table .= $content['init']['frontunit'].(int)($salelist[$i]['AMT']/$salelist[$i]['QTY']).$content['init']['unit'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			$table .= $content['init']['frontunit'].$salelist[$i]['AMT'].$content['init']['unit'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$totalqty=intval($totalqty)+intval($salelist[$i]['QTY']);

			if(isset($posdvr)){
				$tempdvrcontent .= $salelist[$i]['ITEMNAME']."X".$salelist[$i]['QTY']."  ";
				if(floatval(($salelist[$i]['AMT']/$salelist[$i]['QTY']))==0){
					$tempdvrcontent .= "0  0".PHP_EOL;;
				}
				else{
					$tempdvrcontent .= preg_replace('/{.}/','!46',($salelist[$i]['AMT']/$salelist[$i]['QTY']))."  ".preg_replace('/{.}/','!46',($salelist[$i]['AMT'])).PHP_EOL;
				}
			}
			else{
			}

			for($t=1;$t<10;$t++){
				/*if(strlen($salelist[$i]['SELECTIVEITEM'.$t])>0){
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$tt=(int)intval($salelist[$i]['SELECTIVEITEM'.$t])/10;
					$ttt=intval($salelist[$i]['SELECTIVEITEM'.$t])%10;
					if(preg_match('/99999/',$salelist[$i]['SELECTIVEITEM'.$t])){
						$table .= '　+'.substr($salelist[$i]['SELECTIVEITEM'.$t],7);
					}
					else{
						if(intval($ttt)==1){
							$table .= '　+'.$taste[$tt]['name1'];
						}
						else{
							$table .= '　+'.$taste[$tt]['name1'].'*'.$ttt;
						}
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '-';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '-';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					if(isset($posdvr)){
						if($ttt==1){
							$tempdvrcontent .= " !43".$taste[$tt]['name1']."  ".preg_replace('/{.}/','!46',$taste[$tt]['money'])."  ".preg_replace('/{.}/','!46',floatval($taste[$tt]['money'])).PHP_EOL;
						}
						else{
							$tempdvrcontent .= " !43".$taste[$tt]['name1']."X".$ttt."  ".preg_replace('/{.}/','!46',$taste[$tt]['money'])."  ".preg_replace('/{.}/','!46',($ttt*floatval($taste[$tt]['money']))).PHP_EOL;
						}
					}
					else{
					}
				}*/
				//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
				if(strlen($salelist[$i]['SELECTIVEITEM'.$t])>0){
					$temptaste=preg_split('/,/',$salelist[$i]['SELECTIVEITEM'.$t]);
					for($k=0;$k<sizeof($temptaste);$k++){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$tt=(int)intval($temptaste[$k])/10;
						$ttt=intval($temptaste[$k])%10;
						if(preg_match('/99999/',$temptaste[$k])){
							$table .= '　+'.substr($temptaste[$k],7);
						}
						else{
							if(intval($ttt)==1){
								$table .= '　+'.$taste[$tt]['name1'];
							}
							else{
								$table .= '　+'.$taste[$tt]['name1'].'*'.$ttt;
							}
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$table .= '-';
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$table .= '-';
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						if(isset($posdvr)){
							if($ttt==1){
								$tempdvrcontent .= " !43".$taste[$tt]['name1']."  ".preg_replace('/{.}/','!46',$taste[$tt]['money'])."  ".preg_replace('/{.}/','!46',floatval($taste[$tt]['money'])).PHP_EOL;
							}
							else{
								$tempdvrcontent .= " !43".$taste[$tt]['name1']."X".$ttt."  ".preg_replace('/{.}/','!46',$taste[$tt]['money'])."  ".preg_replace('/{.}/','!46',($ttt*floatval($taste[$tt]['money']))).PHP_EOL;
							}
						}
						else{
						}
					}
				}
				else{
					break;
				}
			}
			if(isset($salelist[(intval($i)+1)]['ITEMCODE'])&&$salelist[(intval($i)+1)]['ITEMCODE']=='item'&&floatval($salelist[(intval($i)+1)]['AMT'])!=0){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				//$table .= '-'.$_POST['taste1'][$t];
				if(isset($list['name']['itemdis'])){
					$table .= '　+'.$list['name']['itemdis'];
				}
				else{
					$table .= '　+優惠折扣';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
				$table .= '-';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$salelist[(intval($i)+1)]['AMT'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				
				if(isset($posdvr)){
					if(isset($list['name']['itemdis'])){
						$tempdvrcontent .= " !43".$list['name']['itemdis']."    ".preg_replace('/{.}/','!46',$salelist[(intval($i)+1)]['AMT']).PHP_EOL;
					}
					else{
						$tempdvrcontent .= " !43優惠折扣    ".preg_replace('/{.}/','!46',$salelist[(intval($i)+1)]['AMT']).PHP_EOL;
					}
				}
				else{
				}
			}
			else{
			}
			
			if(isset($salelist[(intval($i)+1)]['ITEMCODE'])&&$salelist[(intval($i)+1)]['ITEMCODE']=='member'){
				$listdis=intval($listdis)+intval($salelist[(intval($i)+1)]['AMT']);
			}
			else{
			}
			if(isset($salelist[(intval($i)+1)]['ITEMCODE'])&&$salelist[(intval($i)+1)]['ITEMCODE']=='list'){
				$listdis=intval($listdis)+intval($salelist[(intval($i)+1)]['AMT']);
			}
			else{
			}
			if(isset($salelist[(intval($i)+1)]['ITEMCODE'])&&$salelist[(intval($i)+1)]['ITEMCODE']=='charge'){
				$charge=$salelist[(intval($i)+1)]['AMT'];
			}
			else{
			}
		}
		else{
		}
	}
	if(isset($posdvr)){
		$tempdvrcontent .= "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL;
	}
	else{
	}
	/***/
	/*if($content['init']['useoinv']=='1'&&$content['init']['oinv']=='1'){
		if($_POST['invlist']=='1'){
			if(strlen($oinv)==0){
				$oinv=sizeof($_POST['no']).',,';
			}
			else{
			}
		}
		else{
		}
	}
	else{
	}*/
	/***/
	$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>-----------------------------------------------</w:t></w:r></w:p>';
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	//$table .= '-'.$_POST['taste1'][$t];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= 'QTY';
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	if($saledata[0]['SALESTTLQTY']==0){
		$table .= $totalqty;
	}
	else{
		$table .= $saledata[0]['SALESTTLQTY'];
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	if(floatval($listdis)!=0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		//$table .= '-'.$_POST['taste1'][$t];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= 'Discount';
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$listdis.$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	if(floatval($charge)>0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		//$table .= '-'.$_POST['taste1'][$t];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= '服務費';
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$charge.$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	//$table .= '-'.$_POST['taste1'][$t];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= 'AMT';
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= $content['init']['frontunit'].$saledata[0]['SALESTTLAMT'].$content['init']['unit'];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$table .= '</w:tbl>';
	$document->setValue('item',$table);
	date_default_timezone_set($content['init']['settime']);
	$filename=date("YmdHis");
	if(isset($print['item']['voidsale'])&&$print['item']['voidsale']==1){
		//$document->save("../../../print/noread/".$filename."_clientlist_".intval($saledata[0]['CONSECNUMBER']).".docx");
		if(!isset($_POST['terminalnumber'])||$_POST['terminalnumber']==''){
			$document->save("../../../print/read/".intval($saledata[0]['CONSECNUMBER'])."_clientlistm1_".$filename.".docx");
			$prt=fopen("../../../print/noread/".intval($saledata[0]['CONSECNUMBER'])."_clientlistm1_".$filename.".prt",'w');
			fclose($prt);
		}
		else{
			$document->save("../../../print/read/".intval($saledata[0]['CONSECNUMBER'])."_clientlist".$_POST['terminalnumber']."_".$filename.".docx");
			$prt=fopen("../../../print/noread/".intval($saledata[0]['CONSECNUMBER'])."_clientlist".$_POST['terminalnumber']."_".$filename.".prt",'w');
			fclose($prt);
		}
	}
	else{
		$document->save("../../../print/read/delete_clientlist.docx");
	}
	echo 'success-';
	if(isset($posdvr)){
		$tempdvrcontent = "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "名稱   單價   小計".PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL.$tempdvrcontent;
		if(isset($saledata[0]['CLKNAME'])){
			$tempdvrcontent = "服務員!58".$saledata[0]['CLKNAME'].PHP_EOL.$tempdvrcontent;
		}
		else{
			$tempdvrcontent = "服務員!58".PHP_EOL.$tempdvrcontent;
		}
		$tempdvrcontent = "時間!58".substr($saledata[0]['CREATEDATETIME'],0,4).'!47'.substr($saledata[0]['CREATEDATETIME'],4,2).'!47'.substr($saledata[0]['CREATEDATETIME'],6,2).' '.substr($saledata[0]['CREATEDATETIME'],8,2).'!58'.substr($saledata[0]['CREATEDATETIME'],10,2).'!58'.substr($saledata[0]['CREATEDATETIME'],12,2).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "機!58".$saledata[0]['TERMINALNUMBER'].PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "編號!58".str_pad($listno,6,'0',STR_PAD_LEFT).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "voidsalelist".PHP_EOL."桌號!58".trim($saledata[0]['TABLENUMBER']).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent .= "小 計  ".preg_replace('/{.}/','!46',$saledata[0]['SALESTTLAMT']).PHP_EOL;
		fwrite($posdvr,$tempdvrcontent);
		fclose($posdvr);
	}
	else{
	}
	if(isset($content['init']['posdvr'])&&$content['init']['posdvr']=='1'){
		echo $tempposdvr.';'.$_POST['terminalnumber'];
	}
	else{
	}
}
else if($tempc[0]['number']>=1){
	date_default_timezone_set($content['init']['settime']);
	$sql='UPDATE tempCST011 SET NBCHKDATE="'.date("YmdHis").'",NBCHKTIME="'.$_POST['name'].'",NBCHKNUMBER="Y" WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'" AND CREATEDATETIME LIKE "'.$credate.'%";INSERT INTO CST011 SELECT * FROM tempCST011 WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'" AND CREATEDATETIME LIKE "'.$credate.'%";DELETE FROM tempCST011 WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'" AND CREATEDATETIME LIKE "'.$credate.'%";INSERT INTO CST012 SELECT * FROM tempCST012 WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'" AND CREATEDATETIME LIKE "'.$credate.'%" ORDER BY LINENUMBER;DELETE FROM tempCST012 WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'" AND CREATEDATETIME LIKE "'.$credate.'%";';
	sqlnoresponse($conn,$sql,'sqliteexec');
	$sql="SELECT * FROM CST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '".$credate."%'";
	$saledata=sqlquery($conn,$sql,'sqlite');
	$sql="SELECT * FROM CST012 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '".$credate."%' ORDER BY LINENUMBER";
	$salelist=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$PHPWord = new PHPWord();
	$document = $PHPWord->loadTemplate('../../../template/void.docx');
	$document->setValue('title',$buttons['name']['billfun29']);
	if(strstr($saledata[0]['REMARKS'],'-')){
		$tempsaledata=preg_split($saledata[0]['REMARKS']);
		if($tempsaledata[1]=='1'){
			$document->setValue('type',$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
		}
		else if($tempsaledata[1]=='2'){
			$document->setValue('type',$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
		}
		else if($tempsaledata[1]=='3'){
			$document->setValue('type',$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
		}
		else{
			$document->setValue('type',$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
		}
	}
	else{
		if($saledata[0]['REMARKS']=='1'){
			$document->setValue('type',$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
		}
		else if($saledata[0]['REMARKS']=='2'){
			$document->setValue('type',$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
		}
		else if($saledata[0]['REMARKS']=='3'){
			$document->setValue('type',$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
		}
		else{
			$document->setValue('type',$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$saledata[0]['TABLENUMBER']);
		}
	}
	/*if($saledata[0]['REMARKS']=='1'){
		$document->setValue('type',$buttons['name']['listtype1'].' '.$saledata[0]['TABLENUMBER']);
	}
	else if($saledata[0]['REMARKS']=='2'){
		$document->setValue('type',$buttons['name']['listtype2'].' '.$saledata[0]['TABLENUMBER']);
	}
	else{
		$document->setValue('type',$buttons['name']['listtype3'].' '.$saledata[0]['TABLENUMBER']);
	}*/
	$document->setValue('consecnumber',intval($saledata[0]['CONSECNUMBER']));
	date_default_timezone_set($content['init']['settime']);
	$document->setValue('datetime',date('Y/m/d H:m:i'));
	if(isset($data['basic']['address'])&&$data['basic']['address']!=''){
		$document->setValue('address', $data['basic']['address']);
	}
	else{
	}
	if(isset($data['basic']['tel'])&&$data['basic']['tel']!=''){
		$document->setValue('phone', $data['basic']['tel']);
	}
	else{
	}
	$document->setValue('story',$data['basic']['storyname']);
	$tindex=0;

	$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "Items";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "U/P";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "Sub";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	
	$invitem=0;
	$totalqty=0;
	$listdis=0;
	$charge=0;
	for($i=0;$i<sizeof($salelist);$i=$i+2){
		if(isset($salelist[$i]['ITEMCODE'])&&($salelist[$i]['ITEMCODE']!='item'&&$salelist[$i]['ITEMCODE']!='list'&&$salelist[$i]['ITEMCODE']!='member'&&$salelist[$i]['ITEMCODE']!='autodis'&&$salelist[$i]['ITEMCODE']!='charge')){
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="10"/></w:rPr><w:t>';
			if(strlen($salelist[$i]['UNITPRICELINK'])==0){
				$table .= $salelist[$i]['ITEMNAME'].' x '.$salelist[$i]['QTY'];
			}
			else{
				$table .= $salelist[$i]['ITEMNAME'].' ( '.$salelist[$i]['UNITPRICELINK'].' ) x '.$salelist[$i]['QTY'];
			}
			//$table .= $_POST['name'][$i].'('.$_POST['mname1'][$i].')x'.$_POST['number'][$i];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			$table .= $content['init']['frontunit'].(int)($salelist[$i]['AMT']/$salelist[$i]['QTY']).$content['init']['unit'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			$table .= $content['init']['frontunit'].$salelist[$i]['AMT'].$content['init']['unit'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$totalqty=intval($totalqty)+intval($salelist[$i]['QTY']);

			if(isset($posdvr)){
				$tempdvrcontent .= $salelist[$i]['ITEMNAME']."X".$salelist[$i]['QTY']."  ";
				if(floatval(($salelist[$i]['AMT']/$salelist[$i]['QTY']))==0){
					$tempdvrcontent .= "0  0".PHP_EOL;;
				}
				else{
					$tempdvrcontent .= preg_replace('/{.}/','!46',($salelist[$i]['AMT']/$salelist[$i]['QTY']))."  ".preg_replace('/{.}/','!46',($salelist[$i]['AMT'])).PHP_EOL;
				}
			}
			else{
			}

			for($t=1;$t<10;$t++){
				/*if(strlen($salelist[$i]['SELECTIVEITEM'.$t])>0){
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$tt=(int)intval($salelist[$i]['SELECTIVEITEM'.$t])/10;
					$ttt=intval($salelist[$i]['SELECTIVEITEM'.$t])%10;
					if(preg_match('/99999/',$salelist[$i]['SELECTIVEITEM'.$t])){
						$table .= '　+'.substr($salelist[$i]['SELECTIVEITEM'.$t],7);
					}
					else{
						if(intval($ttt)==1){
							$table .= '　+'.$taste[$tt]['name1'];
						}
						else{
							$table .= '　+'.$taste[$tt]['name1'].'*'.$ttt;
						}
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '-';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '-';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					if(isset($posdvr)){
						if($ttt==1){
							$tempdvrcontent .= " !43".$taste[$tt]['name1']."  ".preg_replace('/{.}/','!46',$taste[$tt]['money'])."  ".preg_replace('/{.}/','!46',floatval($taste[$tt]['money'])).PHP_EOL;
						}
						else{
							$tempdvrcontent .= " !43".$taste[$tt]['name1']."X".$ttt."  ".preg_replace('/{.}/','!46',$taste[$tt]['money'])."  ".preg_replace('/{.}/','!46',($ttt*floatval($taste[$tt]['money']))).PHP_EOL;
						}
					}
					else{
					}
				}*/
				//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
				if(strlen($salelist[$i]['SELECTIVEITEM'.$t])>0){
					$temptaste=preg_split('/,/',$salelist[$i]['SELECTIVEITEM'.$t]);
					for($k=0;$k<sizeof($temptaste);$k++){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$tt=(int)intval($temptaste[$k])/10;
						$ttt=intval($temptaste[$k])%10;
						if(preg_match('/99999/',$temptaste[$k])){
							$table .= '　+'.substr($temptaste[$k],7);
						}
						else{
							if(intval($ttt)==1){
								$table .= '　+'.$taste[$tt]['name1'];
							}
							else{
								$table .= '　+'.$taste[$tt]['name1'].'*'.$ttt;
							}
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$table .= '-';
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$table .= '-';
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						if(isset($posdvr)){
							if($ttt==1){
								$tempdvrcontent .= " !43".$taste[$tt]['name1']."  ".preg_replace('/{.}/','!46',$taste[$tt]['money'])."  ".preg_replace('/{.}/','!46',floatval($taste[$tt]['money'])).PHP_EOL;
							}
							else{
								$tempdvrcontent .= " !43".$taste[$tt]['name1']."X".$ttt."  ".preg_replace('/{.}/','!46',$taste[$tt]['money'])."  ".preg_replace('/{.}/','!46',($ttt*floatval($taste[$tt]['money']))).PHP_EOL;
							}
						}
						else{
						}
					}
				}
				else{
					break;
				}
			}
			if(isset($salelist[(intval($i)+1)]['ITEMCODE'])&&$salelist[(intval($i)+1)]['ITEMCODE']=='item'&&floatval($salelist[(intval($i)+1)]['AMT'])!=0){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				//$table .= '-'.$_POST['taste1'][$t];
				if(isset($list['name']['itemdis'])){
					$table .= '　+'.$list['name']['itemdis'];
				}
				else{
					$table .= '　+優惠折扣';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
				$table .= '-';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$salelist[(intval($i)+1)]['AMT'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";

				if(isset($posdvr)){
					if(isset($list['name']['itemdis'])){
						$tempdvrcontent .= " !43".$list['name']['itemdis']."    ".preg_replace('/{.}/','!46',$salelist[(intval($i)+1)]['AMT']).PHP_EOL;
					}
					else{
						$tempdvrcontent .= " !43優惠折扣    ".preg_replace('/{.}/','!46',$salelist[(intval($i)+1)]['AMT']).PHP_EOL;
					}
				}
				else{
				}
			}
			else{
			}
			
			if(isset($salelist[(intval($i)+1)]['ITEMCODE'])&&$salelist[(intval($i)+1)]['ITEMCODE']=='member'){
				$listdis=intval($listdis)+intval($salelist[(intval($i)+1)]['AMT']);
			}
			else{
			}
			if(isset($salelist[(intval($i)+1)]['ITEMCODE'])&&$salelist[(intval($i)+1)]['ITEMCODE']=='list'){
				$listdis=intval($listdis)+intval($salelist[(intval($i)+1)]['AMT']);
			}
			else{
			}
			if(isset($salelist[(intval($i)+1)]['ITEMCODE'])&&$salelist[(intval($i)+1)]['ITEMCODE']=='charge'){
				$charge=$salelist[(intval($i)+1)]['AMT'];
			}
			else{
			}
		}
		else{
		}
	}
	if(isset($posdvr)){
		$tempdvrcontent .= "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL;
	}
	else{
	}
	/***/
	/*if($content['init']['useoinv']=='1'&&$content['init']['oinv']=='1'){
		if($_POST['invlist']=='1'){
			if(strlen($oinv)==0){
				$oinv=sizeof($_POST['no']).',,';
			}
			else{
			}
		}
		else{
		}
	}
	else{
	}*/
	/***/
	$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>-----------------------------------------------</w:t></w:r></w:p>';
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	//$table .= '-'.$_POST['taste1'][$t];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= 'QTY';
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	if($saledata[0]['SALESTTLQTY']==0){
		$table .= $totalqty;
	}
	else{
		$table .= $saledata[0]['SALESTTLQTY'];
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	if(floatval($listdis)!=0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		//$table .= '-'.$_POST['taste1'][$t];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= 'Discount';
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$listdis.$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	if(floatval($charge)>0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		//$table .= '-'.$_POST['taste1'][$t];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= '服務費';
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$charge.$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	//$table .= '-'.$_POST['taste1'][$t];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= 'AMT';
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= $content['init']['frontunit'].$saledata[0]['SALESTTLAMT'].$content['init']['unit'];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$table .= '</w:tbl>';
	$document->setValue('item',$table);
	date_default_timezone_set($content['init']['settime']);
	$filename=date("YmdHis");
	if(isset($print['item']['voidsale'])&&$print['item']['voidsale']==1){
		//$document->save("../../../print/noread/".$filename."_clientlist_".intval($saledata[0]['CONSECNUMBER']).".docx");
		if(!isset($_POST['terminalnumber'])||$_POST['terminalnumber']==''){
			$document->save("../../../print/read/".intval($saledata[0]['CONSECNUMBER'])."_clientlistm1_".$filename.".docx");
			$prt=fopen("../../../print/noread/".intval($saledata[0]['CONSECNUMBER'])."_clientlistm1_".$filename.".prt",'w');
			fclose($prt);
		}
		else{
			$document->save("../../../print/read/".intval($saledata[0]['CONSECNUMBER'])."_clientlist".$_POST['terminalnumber']."_".$filename.".docx");
			$prt=fopen("../../../print/noread/".intval($saledata[0]['CONSECNUMBER'])."_clientlist".$_POST['terminalnumber']."_".$filename.".prt",'w');
			fclose($prt);
		}
	}
	else{
		$document->save("../../../print/read/delete_clientlist.docx");
	}
	echo 'success-';
	if(isset($posdvr)){
		$tempdvrcontent = "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "名稱   單價   小計".PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL.$tempdvrcontent;
		if(isset($saledata[0]['CLKNAME'])){
			$tempdvrcontent = "服務員!58".$saledata[0]['CLKNAME'].PHP_EOL.$tempdvrcontent;
		}
		else{
			$tempdvrcontent = "服務員!58".PHP_EOL.$tempdvrcontent;
		}
		$tempdvrcontent = "時間!58".substr($saledata[0]['CREATEDATETIME'],0,4).'!47'.substr($saledata[0]['CREATEDATETIME'],4,2).'!47'.substr($saledata[0]['CREATEDATETIME'],6,2).' '.substr($saledata[0]['CREATEDATETIME'],8,2).'!58'.substr($saledata[0]['CREATEDATETIME'],10,2).'!58'.substr($saledata[0]['CREATEDATETIME'],12,2).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "機!58".$saledata[0]['TERMINALNUMBER'].PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "編號!58".str_pad($listno,6,'0',STR_PAD_LEFT).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "voidtemplist".PHP_EOL."桌號!58".trim($saledata[0]['TABLENUMBER']).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent .= "小 計  ".preg_replace('/{.}/','!46',$saledata[0]['SALESTTLAMT']).PHP_EOL;
		fwrite($posdvr,$tempdvrcontent);
		fclose($posdvr);
	}
	else{
	}
	if(isset($content['init']['posdvr'])&&$content['init']['posdvr']=='1'){
		echo $tempposdvr.';'.$_POST['terminalnumber'];
	}
	else{
	}
}
else{
	sqlclose($conn,'sqlite');
	echo 'fail';
}
?>