<?php
include_once '../../tool/dbTool.inc.php';
include_once '../lib/api/a1erp/a1_api.inc.php';

$conn=sqlconnect('../../database/sale/','SALES_'.substr(preg_replace('/-/','',$_POST['date']),0,6).'.db','','','','sqlite');
$a1sql='SELECT * FROM CST012 WHERE BIZDATE="'.preg_replace('/-/','',$_POST['date']).'" AND CONSECNUMBER="'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'" ORDER BY LINENUMBER ASC';
$a1012=sqlquery($conn,$a1sql,'sqlite');
$a1sql='SELECT * FROM CST011 WHERE BIZDATE="'.preg_replace('/-/','',$_POST['date']).'" AND CONSECNUMBER="'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'" AND NBCHKNUMBER="Y" AND REMARKS!="tempvoid"';
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
	
	$SaleReturnDetails=array();
	$linenumber=1;
	$index=0;
	for($i=0;$i<sizeof($a1012);$i++){
		if(is_numeric($a1012[$i]['ITEMCODE'])){
			$index=sizeof($SaleReturnDetails);
			$SaleReturnDetails[$index]['ID']=$linenumber;
			$SaleReturnDetails[$index]['ItemID']=intval($a1012[$i]['ITEMCODE']);
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
				$SaleReturnDetails[$index]['Memo']=$tastestring;
			}
			else{
				$SaleReturnDetails[$index]['Memo']='';
			}

			$linenumber++;
		}
		else{
			if($a1012[$i]['ITEMCODE']=='item'){//商品促銷
				$SaleReturnDetails[$index]['Amount']=floatval($SaleReturnDetails[$index]['Amount'])+floatval($a1012[$i]['AMT']);
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
			$CustomerID='P'.str_pad(substr($memberno[0],strlen($data['basic']['story'])),9,'0',STR_PAD_LEFT);
		}
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
			$InvoiceDate=substr($invdata[0]['invnumber'],0,4).'-'.substr($invdata[0]['invnumber'],4,2).'-'.substr($invdata[0]['invnumber'],6,2);
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
		$Memo=$a1011[0]['RELINVOICENUMBER'];
	}
	else{
		$Memo='';
	}
	
	$res=SaleReturns($url,'post',$key,$ID,$TradeDate,$CustomerID,$Payment,$InvoiceType,$InvoiceDate,$Invoice,$TaxID,$TaxType,$TaxRate,$TotalTax,$a1011[0]['SALESTTLAMT'],$SaleReturnDetails,$Memo);
	echo $_POST['date'].'-'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'<br>';
	print_r($res);
}
else{
	echo $ID.' 該帳單為暫結作廢單，因此不上傳銷退單。';
}
?>