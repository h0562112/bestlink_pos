<?php
include_once '../../../tool/myerrorlog.php';
include '../../../tool/dbTool.inc.php';
include '../../../tool/inilib.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
$setup=parse_ini_file('../../../database/setup.ini',true);
if(isset($setup['a1erp']['warehouse'])){//2021/10/25 讀取倉庫名稱
	$warehouse=$setup['a1erp']['warehouse'];
}
else{
	$warehouse='公司倉';
}
if(isset($init['a1'])&&$init['a1']['usea1erp']=='1'){//2021/4/14 0>>關閉a1ERP串接1>>開啟a1ERP串接
	include_once '../api/a1erp/a1_api.inc.php';
}
else{
}
if(isset($init['keeper'])&&isset($init['keeper']['useerp'])&&$init['keeper']['useerp']=='1'){//2022/6/9 0>>關閉keeperERP串接1>>開啟keeperERP串接
	include_once '../api/keepererp/keeper_api.inc.php';
}
else{
}
//date_default_timezone_set('Asia/Taipei');

if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['terminalnumber']])){
		$invmachine=$dbmapping['map'][$_POST['terminalnumber']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}

if(file_exists('../../../database/otherpay.ini')){
	$otherpay=parse_ini_file('../../../database/otherpay.ini',true);
}
else{
}
if($_POST['terminalnumber']=='rightnow'){
	date_default_timezone_set($init['init']['settime']);
	$timeini['time']['bizdate']=date('Ymd');
	$timeini['time']['zcounter']='1';
}
else{
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
		$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
	}
	else{//帳務以主機為主體計算
		$timeini=parse_ini_file('../../../database/timem1.ini',true);
	}
}
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$conn=sqlconnect("../../../database/sale","SALES_".substr($timeini['time']['bizdate'],0,6).".DB","","","","sqlite");
$selectsql='PRAGMA table_info(tempCST011)';
$column=sqlquery($conn,$selectsql,'sqlite');
$columnname=array_column($column,'name');
if(in_array('intella',$columnname)){
}
else{
	$insertsql='ALTER TABLE tempCST011 ADD COLUMN intella TEXT';
	sqlnoresponse($conn,$insertsql,'sqlite');
}
if(in_array('nidin',$columnname)){
}
else{
	$insertsql='ALTER TABLE tempCST011 ADD COLUMN nidin TEXT';
	sqlnoresponse($conn,$insertsql,'sqlite');
}
$selectsql='PRAGMA table_info(CST011)';
$column=sqlquery($conn,$selectsql,'sqlite');
$columnname=array_column($column,'name');
if(in_array('intella',$columnname)){
}
else{
	$insertsql='ALTER TABLE CST011 ADD COLUMN intella TEXT';
	sqlnoresponse($conn,$insertsql,'sqlite');
}
if(in_array('nidin',$columnname)){
}
else{
	$insertsql='ALTER TABLE CST011 ADD COLUMN nidin TEXT';
	sqlnoresponse($conn,$insertsql,'sqlite');
}

if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$lastconn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".DB","","","","sqlite");
	$selectsql='PRAGMA table_info(tempCST011)';
	$column=sqlquery($lastconn,$selectsql,'sqlite');
	$columnname=array_column($column,'name');
	if(in_array('intella',$columnname)){
	}
	else{
		$insertsql='ALTER TABLE tempCST011 ADD COLUMN intella TEXT';
		sqlnoresponse($lastconn,$insertsql,'sqlite');
	}
	if(in_array('nidin',$columnname)){
	}
	else{
		$insertsql='ALTER TABLE tempCST011 ADD COLUMN nidin TEXT';
		sqlnoresponse($lastconn,$insertsql,'sqlite');
	}
	$selectsql='PRAGMA table_info(CST011)';
	$column=sqlquery($lastconn,$selectsql,'sqlite');
	$columnname=array_column($column,'name');
	if(in_array('intella',$columnname)){
	}
	else{
		$insertsql='ALTER TABLE CST011 ADD COLUMN intella TEXT';
		sqlnoresponse($lastconn,$insertsql,'sqlite');
	}
	if(in_array('nidin',$columnname)){
	}
	else{
		$insertsql='ALTER TABLE CST011 ADD COLUMN nidin TEXT';
		sqlnoresponse($lastconn,$insertsql,'sqlite');
	}
	sqlclose($lastconn,'sqlite');
	$conn->exec("ATTACH '".$init['db']['dbfile']."SALES_".substr($_POST['bizdate'],0,6).".db' AS trandb");
}
else{
}

if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$sql='SELECT REMARKS,TABLENUMBER,ZCOUNTER FROM trandb.tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
}
else{
	$sql='SELECT REMARKS,TABLENUMBER,ZCOUNTER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
}
$remarks=sqlquery($conn,$sql,'sqlite');
if(sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])&&preg_match('/-/',$remarks[0]['REMARKS'])){
	if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
		$sql='UPDATE trandb.tempCST011 SET REMARKS="'.substr($remarks[0]['REMARKS'],0,1).'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
		$sql=$sql.'UPDATE trandb.tempCST012 SET REMARKS="'.substr($remarks[0]['REMARKS'],0,1).'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
	}
	else{
		$sql='UPDATE tempCST011 SET REMARKS="'.substr($remarks[0]['REMARKS'],0,1).'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
		$sql=$sql.'UPDATE tempCST012 SET REMARKS="'.substr($remarks[0]['REMARKS'],0,1).'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
	}
}
else{
	$sql='';
}
if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$sql=$sql.'INSERT INTO CST012 SELECT * FROM trandb.tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'" ORDER BY CREATEDATETIME;';
}
else{
	$sql=$sql.'INSERT INTO CST012 SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'" ORDER BY CREATEDATETIME;';
}
if($init['init']['bysaleday']==1){
	$sql=$sql.'UPDATE CST012 SET BIZDATE="'.$timeini['time']['bizdate'].'",ZCOUNTER="'.$timeini['time']['zcounter'].'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
}
else{
}
//sqlnoresponse($conn,$sql,'sqlite');
if(isset($otherpay)){
	foreach($otherpay as $i=>$v){
		if($i!='pay'&&isset($v['fromdb'])&&$v['fromdb']=='member'){
			$value[$v['location']]=$v['dbname'];
		}
		else{
		}
	}
}
else{
}
if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$select='SELECT CUSTCODE,CUSTNAME,SUM(TAX6+TAX7+TAX8) AS people,(TAX2+TAX3) AS AMT';
	if(isset($value)){
		foreach($value as $vi=>$vv){
			$select .= ','.$vv.' AS '.$vi;
		}
	}
	else{
	}
	$select .= ' FROM trandb.tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
}
else{
	$select='SELECT CUSTCODE,CUSTNAME,SUM(TAX6+TAX7+TAX8) AS people,(TAX2+TAX3) AS AMT';
	if(isset($value)){
		foreach($value as $vi=>$vv){
			$select .= ','.$vv.' AS '.$vi;
		}
	}
	else{
	}
	$select .= ' FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
}
$temp=sqlquery($conn,$select,'sqlite');

if(isset($init['a1'])&&$init['a1']['usea1erp']=='1'){//2021/4/14 0>>關閉a1ERP串接1>>開啟a1ERP串接
	if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
		$a1sql='SELECT * FROM trandb.tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'" ORDER BY LINENUMBER ASC';
		$a1012=sqlquery($conn,$a1sql,'sqlite');
		$a1sql='SELECT * FROM trandb.tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
		$a1011=sqlquery($conn,$a1sql,'sqlite');
		$hintsql='SELECT RELINVOICENUMBER FROM trandb.tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
		$listhint=sqlquery($conn,$hintsql,'sqlite');
	}
	else{
		$a1sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'" ORDER BY LINENUMBER ASC';
		$a1012=sqlquery($conn,$a1sql,'sqlite');
		$a1sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
		$a1011=sqlquery($conn,$a1sql,'sqlite');
		$hintsql='SELECT RELINVOICENUMBER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
		$listhint=sqlquery($conn,$hintsql,'sqlite');
	}
	$tastedata=parse_ini_file('../../../database/'.$setup['basic']['company'].'-taste.ini',true);
	//$setup=parse_ini_file('../../../database/setup.ini',true);
	if(isset($setup['a1erp'])){
		$url=$setup['a1erp']['url'];
		$id=$setup['a1erp']['id'];
		$pw=$setup['a1erp']['pw'];
		
		$SaleDetails=array();
		$linenumber=1;
		$index=0;
		$menuconn=sqlconnect('../../../database','menu.db','','','','sqlite');
		for($i=0;$i<sizeof($a1012);$i++){
			if(is_numeric($a1012[$i]['ITEMCODE'])){
				$index=sizeof($SaleDetails);
				$SaleDetails[$index]['ID']=$linenumber;

				$menusql='SELECT * FROM itemsdata WHERE inumber="'.intval($a1012[$i]['ITEMCODE']).'"';
				$itemres=sqlquery($menuconn,$menusql,'sqlite');
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
		//print_r($res);
		
		if(isset($res[1]['No'])){
			if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
				$updatesql='UPDATE trandb.tempCST011 SET RELINVOICENUMBER=RELINVOICENUMBER||"A1'.$res[1]['No'].'A1" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
				sqlnoresponse($conn,$updatesql,'sqlite');
			}
			else{
				$updatesql='UPDATE tempCST011 SET RELINVOICENUMBER=RELINVOICENUMBER||"A1'.$res[1]['No'].'A1" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
				sqlnoresponse($conn,$updatesql,'sqlite');
			}
		}
		else{
		}		
	}
	else{
	}
}
else{
}
$UPDATEDATETIME=date('YmdHis');//2022/6/9 後面資料庫結帳時間與keeperERP的銷貨單號使用
if(isset($init['keeper'])&&isset($init['keeper']['useerp'])&&$init['keeper']['useerp']=='1'){//2022/6/9 0>>關閉keeperERP串接1>>開啟keeperERP串接
	if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
		$a1sql='SELECT * FROM trandb.tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'" ORDER BY LINENUMBER ASC';
		$a1012=sqlquery($conn,$a1sql,'sqlite');
		$a1sql='SELECT * FROM trandb.tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
		$a1011=sqlquery($conn,$a1sql,'sqlite');
	}
	else{
		$a1sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'" ORDER BY LINENUMBER ASC';
		$a1012=sqlquery($conn,$a1sql,'sqlite');
		$a1sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
		$a1011=sqlquery($conn,$a1sql,'sqlite');
	}
	
	if(isset($setup['keepererp'])){
		//include_once '../api/keepererp/salelist.php';
		$xml="<?xml version='1.0' encoding='UTF-8'?><Outp xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>";

		$Tradeno='';
		for($i=0;$i<strlen($UPDATEDATETIME);$i=$i+2){
			$tag=substr($UPDATEDATETIME,$i,2);
			if(intval($tag)<=25){//0-25 26個 對應大寫英文
				$Tradeno .= chr(65+intval($tag));
			}
			else if(intval($tag)<=51){//26-51 25個 對應小寫英文
				$Tradeno .= chr(97+intval($tag)-26);
			}
			else{//52-59 8個 對應0-7
				$Tradeno .= (intval($tag)-52);
			}
		}

		$tastedata=parse_ini_file('../../../database/'.$setup['basic']['company'].'-taste.ini',true);
		$frontdata=parse_ini_file('../../../database/'.$setup['basic']['company'].'-front.ini',true);
		
		$salelist='';
		$menuconn=sqlconnect('../../../database','menu.db','','','','sqlite');
		for($i=0,$j=0;$i<sizeof($a1012);$i++){
			if($a1012[$i]['ITEMCODE']!='item'&&$a1012[$i]['ITEMCODE']!='autodis'&&$a1012[$i]['ITEMCODE']!='list'){
				$salelist .= '<OutpItem>';
				$salelist .= '<Line_no>'.($j+1).'.0</Line_no>';//項次

				$menusql='SELECT * FROM itemsdata WHERE inumber="'.intval($a1012[$i]['ITEMCODE']).'"';
				$itemres=sqlquery($menuconn,$menusql,'sqlite');
				if(isset($itemres[0]['erpcode'])&&($itemres[0]['erpcode']!=''&&$itemres[0]['erpcode']!=NULL)){
					$salelist .= '<Goodno>'.$itemres[0]['erpcode'].'</Goodno>';//貨品代號
				}
				else{
					$salelist .= '<Goodno>'.intval($a1012[$i]['ITEMCODE']).'</Goodno>';//貨品代號
				}
				
				$salelist .= '<Saleprice>'.intval(intval($a1012[$i]['AMT'])/intval($a1012[$i]['QTY'])).'.00000</Saleprice>';//賣價
				$salelist .= '<Tradeqty>'.$a1012[$i]['QTY'].'.000</Tradeqty>';//數量
				$salelist .= '<Cost></Cost>';//成本(option 欄位仍要存在)
				$salelist .= '<Saleprice2>'.$a1012[$i]['UNITPRICE'].'.00000</Saleprice2>';//標準售價
				$salelist .= '<Pkno>'.($j+1).'</Pkno>';//序
				$salelist .= '<Goodname2>'.$a1012[$i]['ITEMNAME'].'</Goodname2>';//菜名
				$salelist .= '<Oaddpri>'.(intval(intval($a1012[$i]['AMT'])/intval($a1012[$i]['QTY']))-intval($a1012[$i]['UNITPRICE'])).'.0000</Oaddpri>';//調整口味的總價錢
				if($a1012[$i]['SELECTIVEITEM1']!=''&&$a1012[$i]['SELECTIVEITEM1']!=null){
					$opentaste='';
					$tastename='';
					$tastes=preg_split('/\,/',$a1012[$i]['SELECTIVEITEM1']);
					for($t=0;$t<sizeof($tastes);$t++){
						if(substr($tastes[$t],0,5)=='99999'){//開放備註
							if(strlen($tastes[$t])<=47){
								$opentaste .= substr($tastes[$t],7);
							}
							else{
								$opentaste .= substr($tastes[$t],7,40);
							}
						}
						else{
							if(isset($tastedata[intval(substr($tastes[$t],0,5))])){
								if($tastename!=''){
									$tastename .= ',';
								}
								else{
								}
								$tastename .= $tastedata[intval(substr($tastes[$t],0,5))]['name1'];
							}
							else{
							}
						}
					}
					$salelist .= '<Rem>'.$opentaste.'</Rem>';//備註
					$salelist .= '<Omemo>'.$tastename.'</Omemo>';//調整口味
				}
				else{
					$salelist .= '<Rem></Rem>';//備註
					$salelist .= '<Omemo></Omemo>';//調整口味
				}
				if(isset($frontdata[intval($a1012[$i]['ITEMGRPCODE'])])){
					$salelist .= '<Mkdname>'.$frontdata[intval($a1012[$i]['ITEMGRPCODE'])]['name1'].'</Mkdname>';//菜單類別名稱
				}
				else{
					$salelist .= '<Mkdname>無對應類別名稱</Mkdname>';//菜單類別名稱
				}
				$salelist .= '</OutpItem>';
				$j++;
			}
			else{
			}
		}
		sqlclose($menuconn,'sqlite');
		
		$xml .= '<Outps>';
		$xml .= '<Tradeno>'.$Tradeno.'</Tradeno>';//銷貨單號
		$xml .= '<Ctmno></Ctmno>';//客戶代號(option 欄位仍要存在)
		$xml .= '<Tradedate>'.substr($UPDATEDATETIME,0,8).'</Tradedate>';//銷貨日期
		if($a1011[0]['INVOICENUMBER']==''||$a1011[0]['INVOICENUMBER']==null){
			$xml .= '<Recpno></Recpno>';//發票號碼
		}
		else{
			$xml .= '<Recpno>'.$a1011[0]['INVOICENUMBER'].'</Recpno>';//發票號碼
		}
		$xml .= '<Addresss></Addresss>';//送貨地址(option 欄位仍要存在)
		$xml .= '<Taxmark>2</Taxmark>';//稅別
		$xml .= '<Amount>'.$a1011[0]['SALESTTLAMT'].'</Amount>';//稅前金額
		$xml .= '<Tax>0</Tax>';//稅金
		$xml .= '<Salesno></Salesno>';//員工代號(option 欄位仍要存在)
		$xml .= '<Stno></Stno>';//庫別(option 欄位仍要存在)
		$xml .= '<Tamount>'.$a1011[0]['SALESTTLAMT'].'</Tamount>';//總價
		if($a1011[0]['RELINVOICENUMBER']==''||$a1011[0]['RELINVOICENUMBER']==null){
			$xml .= '<Remark></Remark>';//備註
		}
		else if(strlen($a1011[0]['RELINVOICENUMBER'])<=100){
			$xml .= '<Remark>'.$a1011[0]['RELINVOICENUMBER'].'</Remark>';//備註
		}
		else{
			$xml .= '<Remark>'.substr($a1011[0]['RELINVOICENUMBER'],0,100).'</Remark>';//備註
		}
		$xml .= '<Monthsec>'.substr($UPDATEDATETIME,0,6).'</Monthsec>';//帳款月份
		switch(substr($a1011[0]['REMARKS'],0,1)){
			case '1':
				$xml .= '<Sendtype>內用</Sendtype>';//用餐別
				break;
			case '2':
				$xml .= '<Sendtype>外帶</Sendtype>';//用餐別
				break;
			case '3':
				$xml .= '<Sendtype>外送</Sendtype>';//用餐別
				break;
			case '4':
				$xml .= '<Sendtype>自取</Sendtype>';//用餐別
				break;
			default:
				$xml .= '<Sendtype></Sendtype>';//用餐別
				break;
		}
		$xml .= '<Tradetime>'.substr($UPDATEDATETIME,8,2).':'.substr($UPDATEDATETIME,10,2).'</Tradetime>';//交易時間
		$xml .= '<Schno>'.$a1011[0]['ZCOUNTER'].'</Schno>';//班別
		$xml .= '<Machno></Machno>';//機別(option 欄位仍要存在)
		$xml .= '<Idno></Idno>';//統編(option 欄位仍要存在)
		$xml .= '<Cashpay>0</Cashpay>';//收現金額(option 欄位仍要存在)
		$xml .= '<Retcash>0</Retcash>';//找零金額(option 欄位仍要存在)
		$xml .= '<Crdpay>0</Crdpay>';//刷卡金額(option 欄位仍要存在)
		$xml .= '<Billpay>0</Billpay>';//禮券金額(option 欄位仍要存在)
		$xml .= '<Crdno></Crdno>';//卡號(option 欄位仍要存在)
		$xml .= '<Tkindno>POS</Tkindno>';//單據類別
		$xml .= '<Prepay>0</Prepay>';//沖預收(option 欄位仍要存在)
		$xml .= '<Moneypay>0</Moneypay>';//已沖金額(option 欄位仍要存在)
		$xml .= '<Deskno></Deskno>';//桌號(option 欄位仍要存在)
		$xml .= '<Useno></Useno>';//序號(option 欄位仍要存在)
		$xml .= '<Bmaxno></Bmaxno>';//(option 欄位仍要存在)
		$xml .= '<Imaxno>'.$j.'</Imaxno>';//明細最大序
		$xml .= '<Manqty>0</Manqty>';//人數(option 欄位仍要存在)
		$xml .= '<Outmark>0</Outmark>';//是否作廢
		$xml .= '<Taxrate>5</Taxrate>';//稅率
		$xml .= '<Othpay1>0</Othpay1>';//其他1金額(option 欄位仍要存在)
		$xml .= '<Othpay2>0</Othpay2>';//其他2金額(option 欄位仍要存在)
		$xml .= '<Othpay3>0</Othpay3>';//其他3金額(option 欄位仍要存在)
		$xml .= '<Othpay4>0</Othpay4>';//其他4金額(option 欄位仍要存在)
		$xml .= '<Othpay5>0</Othpay5>';//其他5金額(option 欄位仍要存在)
		$xml .= '<Othpay6>0</Othpay6>';//其他6金額(option 欄位仍要存在)
		$xml .= '<Atel></Atel>';//訂購人電話(option 欄位仍要存在)
		$xml .= '<Aname></Aname>';//訂購人名稱(option 欄位仍要存在)
		$xml .= '</Outps>';

		$xml .= $salelist;
		$xml .= "</Outp>";

		$keeperres=Keeper_Sales($setup['keepererp']['url'],'post',$xml,$setup['keepererp']['apikey'],$setup['keepererp']['authkey'],$setup['keepererp']['storeid'],$setup['keepererp']['depid']);
		if(isset($keeperres[0])){
			$keeperres[0]=preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $keeperres[0]);
			$res=simplexml_load_string($keeperres[0]);
			$json=json_encode($res);
			$res=json_decode($json,true);
			//print_r($res);
			if(isset($res['soapBody']['Upload_ts_outpResponse']['Upload_ts_outpResult'])){
				//echo $res['soapBody']['Upload_ts_outpResponse']['Upload_ts_outpResult'];
			}
			else{
				//echo '0';
				$f=fopen('../../../printlog.txt','a');
				fwrite($f,date('Y/m/d H:i:s').' -- temptodb.ajax.php TO keepererp'.print_r($res,true).PHP_EOL);
				fclose($f);
			}
		}
		else{
			//echo '1';
		}
	}
	else{
	}
}
else{
}

if(intval($temp[0]['people'])>0){
	if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
		date_default_timezone_set($init['init']['settime']);
		$sql=$sql.'UPDATE trandb.tempCST011 SET UPDATEDATETIME="'.$UPDATEDATETIME.'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
	}
	else{
		date_default_timezone_set($init['init']['settime']);
		$sql=$sql.'UPDATE tempCST011 SET UPDATEDATETIME="'.$UPDATEDATETIME.'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
	}
}
else{
	if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
		date_default_timezone_set($init['init']['settime']);
		$sql=$sql.'UPDATE trandb.tempCST011 SET TAX6=1,UPDATEDATETIME="'.$UPDATEDATETIME.'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
	}
	else{
		date_default_timezone_set($init['init']['settime']);
		$sql=$sql.'UPDATE tempCST011 SET TAX6=1,UPDATEDATETIME="'.$UPDATEDATETIME.'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
	}
}
if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$sql=$sql.'INSERT INTO CST011 SELECT * FROM trandb.tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'" ORDER BY CREATEDATETIME;';
}
else{
	$sql=$sql.'INSERT INTO CST011 SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'" ORDER BY CREATEDATETIME;';
}
if($init['init']['bysaleday']==1){
	$sql=$sql.'UPDATE CST011 SET BIZDATE="'.$timeini['time']['bizdate'].'",ZCOUNTER="'.$timeini['time']['zcounter'].'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
}
else{
}
//sqlnoresponse($conn,$sql,'sqlite');
if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$sql=$sql.'DELETE FROM trandb.tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
	//sqlnoresponse($conn,$sql,'sqlite');
	$sql=$sql.'DELETE FROM trandb.tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
}
else{
	$sql=$sql.'DELETE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
	//sqlnoresponse($conn,$sql,'sqlite');
	$sql=$sql.'DELETE FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
}
sqlnoresponse($conn,$sql,'sqliteexec');
//echo $sql;
$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'"';
$c=sqlquery($conn,$sql,'sqlite');
//print_r($c);
if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$sql='SELECT * FROM trandb.salemap WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
}
else{
	$sql='SELECT * FROM salemap WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'";';
}
$saleno=sqlquery($conn,$sql,'sqlite');
//print_r($saleno);
sqlclose($conn,'sqlite');
if(isset($c[0]['CLKCODE'])&&$c[0]['CLKCODE']=='web'&&isset($c[0]['CLKNAME'])&&$c[0]['CLKNAME']=='網路訂購'&&isset($saleno[0]['onlinebizdate'])&&isset($saleno[0]['onlineconsecnumber'])){
	$data=parse_ini_file('../../../database/setup.ini',true);
	$PostData = array(
		"type"=> 'saleout',
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
	$memdata = curl_exec($ch);
	$memdata=json_decode($memdata,1);
	if(curl_errno($ch) !== 0) {
		//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
	}
	curl_close($ch);
	//print_r($memdata);
}
else{
}

if($init['init']['controltable']==1){
	if(preg_match('/-/',$remarks[0]['REMARKS'])){
		$tempremarks=preg_split('/-/',$remarks[0]['REMARKS']);
		$tremarks='';
		for($i=1;$i<sizeof($tempremarks);$i++){
			if($tremarks==''){
				$tremarks=$tempremarks[$i];
			}
			else{
				$tremarks=$tremarks.'-'.$tempremarks[$i];
			}
		}
	}
	else{
		$tremarks=$remarks[0]['REMARKS'];
	}
	if($tremarks=='1'){
		$tablist=preg_split('/,/',$remarks[0]['TABLENUMBER']);
		foreach($tablist as $tl){
			if(file_exists('../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.iconv('utf-8','big5',$tl).'.ini')){
				if(!isset($init['init']['controltabini'])||(isset($init['init']['controltabini'])&&$init['init']['controltabini']=='1')){//結帳後清桌控
					try {
						unlink('../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.iconv('utf-8','big5',$tl).'.ini');
					}
					catch(Exception $e){
						$header=fopen('../../../printlog.txt','a');
						date_default_timezone_set($init['init']['settime']);
						fwrite($header,date('Y/m/d H:i:s').' -- file is exists(../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.$tl.'.ini) utf-8 to big5, but error'.PHP_EOL.'  '.$e.PHP_EOL);
						fclose($header);
					}
				}
				else{
					$temptable=parse_ini_file('../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.iconv('utf-8','big5',$tl).'.ini',true);
					$temptable[$tl]['state']="0";
					write_ini_file($temptable,'../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.iconv('utf-8','big5',$tl).'.ini');
				}
			}
			else if(file_exists('../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.$tl.'.ini')){
				if(!isset($init['init']['controltabini'])||(isset($init['init']['controltabini'])&&$init['init']['controltabini']=='1')){//結帳後清桌控
					try {
						unlink('../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.$tl.'.ini');
					}
					catch(Exception $e){
						$header=fopen('../../../printlog.txt','a');
						date_default_timezone_set($init['init']['settime']);
						fwrite($header,date('Y/m/d H:i:s').' -- file is exists(../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.$tl.'.ini), but error'.PHP_EOL.'  '.$e.PHP_EOL);
						fclose($header);
					}
				}
				else{
					$temptable=parse_ini_file('../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.$tl.'.ini',true);
					$temptable[$tl]['state']="0";
					write_ini_file($temptable,'../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.$tl.'.ini');
				}
			}
			else{
				$header=fopen('../../../printlog.txt','a');
				date_default_timezone_set($init['init']['settime']);
				fwrite($header,date('Y/m/d H:i:s').' -- file is not exists ../../table/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.$tl.'.ini'.PHP_EOL);
				fclose($header);
			}
		}
	}
	else if($tremarks=='2'){
		if(file_exists('../../table/outside/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'.ini')){
			try {
				unlink('../../table/outside/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'.ini');
			}
			catch(Exception $e){
				$header=fopen('../../../printlog.txt','a');
				date_default_timezone_set($init['init']['settime']);
				fwrite($header,date('Y/m/d H:i:s').' -- file is exists(../../table/outside/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'.ini), but error'.PHP_EOL.'  '.$e.PHP_EOL);
				fclose($header);
			}
		}
		else{
			$header=fopen('../../../printlog.txt','a');
			date_default_timezone_set($init['init']['settime']);
			fwrite($header,date('Y/m/d H:i:s').' -- file is not exists ../../table/outside/'.$_POST['bizdate'].';'.$remarks[0]['ZCOUNTER'].';'.str_pad($_POST['numbertag'],6,'0',STR_PAD_LEFT).'.ini'.PHP_EOL);
			fclose($header);
		}
	}
	else{
	}
}
else{
}

/*if($temp[0]['CUSTCODE']!=''&&$temp[0]['CUSTCODE']!=null){
	$setup=parse_ini_file('../../../database/setup.ini',true);
	$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
	$sql='SELECT * FROM person WHERE memno="'.$temp[0]['CUSTCODE'].'" AND state=1';
	$memdata=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(file_exists('../../../database/sale/memsalelist_'.substr($timeini['time']['bizdate'],0,6).'.db')){
	}
	else{
		copy("../../../database/sale/memsalelist.DB",'../../../database/sale/memsalelist_'.substr($timeini['time']['bizdate'],0,6).'.DB');
	}
	$conn=sqlconnect('../../../database/sale','memsalelist_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
	$sql='INSERT INTO salemap (bizdate,company,dep,consecnumber,memno,cardno,money,state';
	if(isset($value)){
		foreach($value as $vi=>$vv){
			$sql .= ','.$vi;
		}
	}
	else{
	}
	$sql .= ') VALUES ("'.$timeini['time']['bizdate'].'","'.$setup['basic']['company'].'","'.$setup['basic']['story'].'","'.$machinedata['basic']['consecnumber'].'","'.$memdata[0]['memno'].'","'.$memdata[0]['cardno'].'",'.$temp[0]['AMT'].',1';
	if(isset($value)){
		foreach($value as $vi=>$vv){
			$t=preg_split('/=/',$temp[0][$vi]);
			$sql .= ','.$t[0];
		}
	}
	else{
	}
	$sql .= ')';
	//echo $sql;
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
else{
}*/
?>