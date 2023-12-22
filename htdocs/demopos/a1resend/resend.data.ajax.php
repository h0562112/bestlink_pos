<?php
include_once '../../tool/dbTool.inc.php';
include_once '../lib/api/a1erp/a1_api.inc.php';

$conn=sqlconnect('../../database/sale','SALES_'.substr(preg_replace('/-/','',$_POST['date']),0,6).'.db','','','','sqlite');
$a1sql='SELECT * FROM CST012 WHERE BIZDATE="'.preg_replace('/-/','',$_POST['date']).'" AND CONSECNUMBER="'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'" ORDER BY LINENUMBER ASC';
$a1012=sqlquery($conn,$a1sql,'sqlite');
$a1sql='SELECT * FROM CST011 WHERE BIZDATE="'.preg_replace('/-/','',$_POST['date']).'" AND CONSECNUMBER="'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'"';
$a1011=sqlquery($conn,$a1sql,'sqlite');
sqlclose($conn,'sqlite');

$setup=parse_ini_file('../../database/setup.ini',true);
$tastedata=parse_ini_file('../../database/'.$setup['basic']['company'].'-taste.ini',true);
if(isset($setup['a1erp']['warehouse'])){//2021/10/25 讀取倉庫名稱
	$warehouse=$setup['a1erp']['warehouse'];
}
else{
	$warehouse='公司倉';
}
if(isset($setup['a1erp'])&&isset($a1011[0])){
	$url=$setup['a1erp']['url'];
	$id=$setup['a1erp']['id'];
	$pw=$setup['a1erp']['pw'];
	
	$SaleDetails=array();
	$linenumber=1;
	$index=0;
	$menuconn=sqlconnect('../../database','menu.db','','','','sqlite');
	for($i=0;$i<sizeof($a1012);$i++){
		if(is_numeric($a1012[$i]['ITEMCODE'])){
			$index=sizeof($SaleDetails);
			$SaleDetails[$index]['ID']=$linenumber;

			$sql='SELECT * FROM itemsdata WHERE inumber="'.intval($a1012[$i]['ITEMCODE']).'"';
			$itemres=sqlquery($menuconn,$sql,'sqlite');
			if(isset($itemres[0]['erpcode'])&&($itemres[0]['erpcode']!=''&&$itemres[0]['erpcode']!=NULL)){
				$SaleDetails[$index]['ItemID']=$itemres[0]['erpcode'];
			}
			else{
				$SaleDetails[$index]['ItemID']=intval($a1012[$i]['ITEMCODE']);
			}

			$SaleDetails[$index]['Qty']=$a1012[$i]['QTY'];
			$SaleDetails[$index]['Amount']=$a1012[$i]['AMT'];
			$SaleDetails[$index]['Warehouse']=$warehouse;

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
						if(isset($tastedata[intval(substr($tasteno[$t],0,5))])){
							if($tastestring!=''){
								$tastestring .= ',';
							}
							else{
							}
							$tastestring .= $tastedata[intval(substr($tasteno[$t],0,5))]['name1'];
						}
						else{
						}
					}
				}
				$SaleDetails[$index]['Memo']=$tastestring;
			}
			else{
				$SaleDetails[$index]['Memo']='';
			}

			$linenumber++;
		}
		else{
			if($a1012[$i]['ITEMCODE']=='item'){//商品促銷
				$SaleDetails[$index]['Amount']=floatval($SaleDetails[$index]['Amount'])+floatval($a1012[$i]['AMT']);
			}
			else if($a1012[$i]['ITEMCODE']=='list'){//帳單優惠
				$index=sizeof($SaleDetails);
				$SaleDetails[$index]['ID']=$linenumber;
				$SaleDetails[$index]['ItemID']='D999999999';
				$SaleDetails[$index]['Qty']=1;
				$SaleDetails[$index]['Amount']=intval($a1012[$i]['AMT']);
				$SaleDetails[$index]['Warehouse']=$warehouse;
				$linenumber++;
			}
			else if($a1012[$i]['ITEMCODE']=='autodis'){//系統自動優惠
				$index=sizeof($SaleDetails);
				$SaleDetails[$index]['ID']=$linenumber;
				$SaleDetails[$index]['ItemID']='D999999998';
				$SaleDetails[$index]['Qty']=1;
				$SaleDetails[$index]['Amount']=intval($a1012[$i]['AMT']);
				$SaleDetails[$index]['Warehouse']=$warehouse;
				$linenumber++;
			}
			else if($a1012[$i]['ITEMCODE']=='member'){//會員優惠
				$index=sizeof($SaleDetails);
				$SaleDetails[$index]['ID']=$linenumber;
				$SaleDetails[$index]['ItemID']='D999999997';
				$SaleDetails[$index]['Qty']=1;
				$SaleDetails[$index]['Amount']=intval($a1012[$i]['AMT']);
				$SaleDetails[$index]['Warehouse']=$warehouse;
				$linenumber++;
			}
			else{
			}
		}
	}
	if($a1011[0]['TAX1']>0){//服務費不為0
		$index=sizeof($SaleDetails);
		$SaleDetails[$index]['ID']=$linenumber;
		$SaleDetails[$index]['ItemID']='D999999996';
		$SaleDetails[$index]['Qty']=1;
		$SaleDetails[$index]['Amount']=intval($a1011[0]['TAX1']);
		$SaleDetails[$index]['Warehouse']=$warehouse;
		$linenumber++;
	}
	else{
	}
	sqlclose($menuconn,'sqlite');

	$login=Login($url,"post",$id,$pw);
	$key=$login[1]['access_token'];
	$ID=$setup['basic']['story'].$a1011[0]['CONSECNUMBER'];
	$TradeDate=substr($a1011[0]['BIZDATE'],0,4).'-'.substr($a1011[0]['BIZDATE'],4,2).'-'.substr($a1011[0]['BIZDATE'],6,2);
	if(isset($a1011[0]['CUSTCODE'])&&$a1011[0]['CUSTCODE']!=''){
		$memberno=preg_split('/;-;/',$a1011[0]['CUSTCODE']);

		$PostData = array(
			"ajax"=> '',
			"type"=> 'online',
			"company" => $setup['basic']['company'],
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
			$CustomerID='P'.str_pad(substr($memberno[0],strlen($setup['basic']['story'])),9,'0',STR_PAD_LEFT);
		}
	}
	else{
		$CustomerID='P999999999';
	}
	$Payment='1';
	if($a1011[0]['TAX2']>0){
		$Payment='1';
	}
	else if($a1011[0]['TAX3']>0){
		$Payment='2';
	}
	else if($a1011[0]['TAX4']>0){
		$Payment='3';
	}
	if(isset($a1011[0]['INVOICENUMBER'])&&$a1011[0]['INVOICENUMBER']!=''){
		if(intval(substr($a1011[0]['BIZDATE'],4,2))%2==1){
			$invmonth=date('Ym',strtotime(substr($a1011[0]['BIZDATE'],0,4).'-'.substr($a1011[0]['BIZDATE'],4,2).'-01 +1 month'));
		}
		else{
			$invmonth=substr($a1011[0]['BIZDATE'],0,6);
		}

		if(file_exists('../../database/sale/'.$invmonth.'/invdata_'.$invmonth.'_m1.db')){
			$invconn=sqlconnect('../../database/sale/'.$invmonth,'invdata_'.$invmonth.'_m1.db','','','','sqlite');
			$invsql='SELECT * FROM invlist WHERE invnumber="'.$a1011[0]['INVOICENUMBER'].'"';
			$invdata=sqlquery($invconn,$invsql,'sqlite');
			sqlclose($invconn,'sqlite');

			if(!isset($invdata[0]['invnumber'])){
				$invdata='';

				$invmonth=date('Ym',strtotime(substr($invmonth,0,4).'-'.substr($invmonth,4,2).'-01 -2 month'));
				if(file_exists('../../database/sale/'.$invmonth.'/invdata_'.$invmonth.'_m1.db')){
					$invconn=sqlconnect('../../database/sale/'.$invmonth,'invdata_'.$invmonth.'_m1.db','','','','sqlite');
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
			if(file_exists('../../database/sale/'.$invmonth.'/invdata_'.$invmonth.'_m1.db')){
				$invconn=sqlconnect('../../database/sale/'.$invmonth,'invdata_'.$invmonth.'_m1.db','','','','sqlite');
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
	$OtherAccount='';
	for($i=1;$i<=10;$i++){
		if($a1011[0]['TA'.$i]!=0){
			$OtherAccount='TA'.$i;
			break;
		}
		else{
		}
	}

	if(isset($a1011[0]['RELINVOICENUMBER'])&&$a1011[0]['RELINVOICENUMBER']!=''&&$a1011[0]['RELINVOICENUMBER']!=null){
		$Memo=$a1011[0]['RELINVOICENUMBER'];
	}
	else{
		$Memo='';
	}
	
	$res=Sales($url,'post',$key,$ID,$TradeDate,$CustomerID,$Payment,$InvoiceType,$InvoiceDate,$Invoice,$TaxID,$TaxType,$TaxRate,$TotalTax,($a1011[0]['SALESTTLAMT']+$a1011[0]['TAX1']),$SaleDetails,$OtherAccount,$Memo);
	echo $_POST['date'].'-'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'<br>';
	echo 'send data:';
	print_r(array($url,'post',$key,$ID,$TradeDate,$CustomerID,$Payment,$InvoiceType,$InvoiceDate,$Invoice,$TaxID,$TaxType,$TaxRate,$TotalTax,($a1011[0]['SALESTTLAMT']+$a1011[0]['TAX1']),$SaleDetails,$OtherAccount,$Memo));
	echo '<br>';
	print_r($res);
}
else{
}
?>