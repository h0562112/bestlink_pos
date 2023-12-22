<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$content=parse_ini_file('../../../database/setup.ini',true);
if(!isset($content['basic']['sendinvlocation'])||$content['basic']['sendinvlocation']=='1'){//亮點
	include_once '../../../demoinv/newinv.php';
}
else if($content['basic']['sendinvlocation']=='2'){//神通
}
else if($content['basic']['sendinvlocation']=='3'){//中鼎
}
//print_r($content);
//include_once '../../../tool/inilib.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinename']])){
		$invmachine=$dbmapping['map'][$_POST['machinename']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
if($_POST['consecnumber']==''){
	$consecnumber=$machinedata['basic']['consecnumber'];
}
else{
	$consecnumber=$_POST['consecnumber'];
}
$consecnumber=str_pad($consecnumber,6,'0',STR_PAD_LEFT);
if(file_exists('../../../database/sale/SALES_'.substr($_POST['bizdate'],0,6).'.db')){
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT CLKNAME,INVOICENUMBER,RELINVOICENUMBER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND consecnumber="'.$consecnumber.'"';//2022/5/19 增加CLKNAME,RELINVOICENUMBER兩個欄位，方便後續辨別foodpanda單子中的發票是否需要統一編號
	$invnumber=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($invnumber)&&sizeof($invnumber)>0&&isset($invnumber[0]['INVOICENUMBER'])&&strlen(trim($invnumber[0]['INVOICENUMBER']))==10){
		$tttt=1;
	}
	else{
		$tttt=0;
	}
}
else{
	$tttt=0;
}
if($tttt==0){
	//2022/5/19 檢查備註中有沒有統一編號(目前只有foodpanda)
	if($invnumber[0]['CLKNAME']=='FoodPanda'&&preg_match('/(統一編號: )/',$invnumber[0]['RELINVOICENUMBER'])&&$_POST['tempban']==''){
		$contain=preg_split('/(統一編號: )/',$invnumber[0]['RELINVOICENUMBER']);
		$_POST['tempban']=substr($contain[1],0,8);
	}
	else if($invnumber[0]['CLKNAME']=='FoodPanda'&&preg_match('/(統一編號:)/',$invnumber[0]['RELINVOICENUMBER'])&&$_POST['tempban']==''){//2022/5/19 快一點接下來的Foodpanda備註中，會將空白拿掉(不知道它們為什麼要變動格式)
		$contain=preg_split('/(統一編號:)/',$invnumber[0]['RELINVOICENUMBER']);
		$_POST['tempban']=substr($contain[1],0,8);
	}
	else{
	}

	$consecnumber=str_pad($consecnumber,6,'0',STR_PAD_LEFT);
	$Y=date('Y');
	$year=(intval($Y)-1911);
	$month=date('m');
	if(intval($month)%2==0){
		$m=$month;
	}
	else{
		$m=intval($month)+1;
	}
	if(strlen($m)<2){
		$m='0'.$m;
	}
	$day=date('d');
	$hour=date('H');
	$min=date('i');
	$sec=date('s');
	
	/*if(intval(substr($_POST['bizdate'],4,2))%2==1){
		if(intval(substr($_POST['bizdate'],4,2))<9){
			$invdate=substr($_POST['bizdate'],0,4).'0'.(intval(substr($_POST['bizdate'],4,2))+1);
		}
		else{
			$invdate=substr($_POST['bizdate'],0,4).(intval(substr($_POST['bizdate'],4,2))+1);
		}
	}
	else{
		$invdate=substr($_POST['bizdate'],0,6);
	}*/
	$invdate=$Y.$m;
	if(file_exists("../../../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db")){
	}
	else{
		if(file_exists("../../../database/sale/EMinvdata.DB")){
		}
		else{
			include_once './create.emptyDB.php';
			create('EMinvdata');
		}
		copy("../../../database/sale/EMinvdata.db","../../../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db");
	}
	$conn1=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
	$sql='SELECT banno FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND datetime="'.$year.$m.'" ORDER BY banno LIMIT 1';
	$table=sqlquery($conn1,$sql,'sqlite');
	//echo 'banno='.$talbe[0]['banno'];

	//2022/2/7 底下SQL的結果後續沒有應用，先移除
	//$sql='SELECT COUNT(*) AS num FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND datetime="'.$year.$m.'"';
	//$checkadd=sqlquery($conn1,$sql,'sqlite');

	if(sizeof($table)==0){
		sqlclose($conn1,'sqlite');
		echo 'error';
	}
	else if(strlen(trim($table[0]['banno']))==10){
		$sql='UPDATE number SET state=0 WHERE banno="'.$table[0]['banno'].'" AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND datetime="'.$year.$m.'"';
		sqlnoresponse($conn1,$sql,'sqlite');
		$sql='SELECT invnumber FROM invlist WHERE invnumber="'.$table[0]['banno'].'"';
		$invexists=sqlquery($conn1,$sql,'sqlite');
		while(isset($invexists[0]['invnumber'])){//2022/2/7 該發票號碼已存在開立紀錄
			$sql='SELECT banno FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND datetime="'.$year.$m.'" ORDER BY banno LIMIT 1';
			$table=sqlquery($conn1,$sql,'sqlite');
			if(sizeof($table)==0){
				sqlclose($conn1,'sqlite');
				$invexists='';
				echo 'error';
			}
			else if(strlen(trim($table[0]['banno']))==10){
				$sql='UPDATE number SET state=0 WHERE banno="'.$table[0]['banno'].'" AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND datetime="'.$year.$m.'"';
				sqlnoresponse($conn1,$sql,'sqlite');
				$sql='SELECT invnumber FROM invlist WHERE invnumber="'.$table[0]['banno'].'"';
				$invexists=sqlquery($conn1,$sql,'sqlite');
			}
			else{
				sqlclose($conn1,'sqlite');
				$invexists='';
				echo 'invoicenumber length is not enough';
			}
		}
		if(sizeof($table)==0){
			sqlclose($conn1,'sqlite');
			break;
		}
		else if(strlen(trim($table[0]['banno']))==10){
		}
		else{
			sqlclose($conn1,'sqlite');
			break;
		}
		$conn2=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
		$sql='UPDATE tempCST011 SET TERMINALNUMBER="'.$_POST['machinename'].'",INVOICENUMBER="'.$table[0]['banno'].'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"';
		sqlnoresponse($conn2,$sql,'sqlite');
		//if($_POST['invlist']=='2'){//總項
			$sql='SELECT *,TAX5 AS invmoney FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"';
			$list=sqlquery($conn2,$sql,'sqlite');
			//echo $sql;
		/*}
		else{
			$sql='';
			$list=sqlquery($conn,$sql,'sqlite');
		}*/
		//$list[0]['SALESTTLAMT']=floatval($list[0]['SALESTTLAMT'])+floatval($list[0]['TAX1']);
		//$list[0]['SALESTTLAMT']=$list[0]['invmoney'];
		sqlclose($conn1,'sqlite');
		sqlclose($conn2,'sqlite');
		if(!isset($content['basic']['sendinvlocation'])||$content['basic']['sendinvlocation']=='1'){//亮點
			$filename='C0401_'.$table[0]['banno'].'_'.$Y.$month.$day.$hour.$min.$sec.'_'.$invmachine.'.xml';
			$xml=new SimpleXMLElement($xmlstr);
			$xml->addChild('Main');
			$xmlMain=$xml->Main;
			$xmlMain->addChild('InvoiceNumber',$table[0]['banno']);
			$date=$Y.$month.$day;
			$xmlMain->addChild('InvoiceDate',$date);
			$time=$hour.':'.$min.':'.$sec;
			$xmlMain->addChild('InvoiceTime',$time);
			$xmlMain->addChild('Seller');
			$xmlMainSeller=$xmlMain->Seller;
			$xmlMainSeller->addChild('Identifier',$content['basic']['Identifier']);
			$xmlMainSeller->addChild('Name',$content['basic']['Name']);
			$xmlMain->addChild('Buyer');
			$xmlMainBuyer=$xmlMain->Buyer;
			if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
				$buyerid=$_POST['tempban'];
				$xmlMainBuyer->addChild('Identifier',$_POST['tempban']);
				$buyername='0000';
				$xmlMainBuyer->addChild('Name','0000');
			}
			else{
				$buyerid='0000000000';
				$xmlMainBuyer->addChild('Identifier','0000000000');
				$buyername='0000000000';
				$xmlMainBuyer->addChild('Name','0000000000');
			}
			//從 神通 聽說，發票上不能列印信用卡後四碼
			/*if(isset($list[0]['CREDITCARD'])&&$list[0]['CREDITCARD']!=''&&$list[0]['CREDITCARD']!=null){
				$xmlMain->addChild('MainRemark',"信用卡尾碼：".$list[0]['CREDITCARD']);
			}
			else{
			}*/
			$xmlMain->addChild('RelateNumber',$list[0]['CONSECNUMBER']);
			$xmlMain->addChild('InvoiceType','07');
			if(isset($_POST['tempcontainer'])&&strlen($_POST['tempcontainer'])>0){
				if(substr($_POST['tempcontainer'],0,1)=='/'){//手機條碼
					$xmlMain->addChild('DonateMark','0');
					$xmlMain->addChild('CarrierType','3J0002');
					$xmlMain->addChild('CarrierId1',strtoupper($_POST['tempcontainer']));
					$xmlMain->addChild('CarrierId2',strtoupper($_POST['tempcontainer']));
					if(strlen($_POST['tempban'])==8){
						$xmlMain->addChild('PrintMark','Y');
					}
					else{
						$xmlMain->addChild('PrintMark','N');
					}
				}
				else if(is_numeric(substr($_POST['tempcontainer'],0,1))){//愛心碼
					$xmlMain->addChild('DonateMark','1');
					$xmlMain->addChild('PrintMark','N');
					$xmlMain->addChild('NPOBAN',$_POST['tempcontainer']);
				}
				else if(strlen($_POST['tempcontainer'])==16){//自然人憑證
					$xmlMain->addChild('DonateMark','0');
					$xmlMain->addChild('CarrierType','CQ0001');
					$xmlMain->addChild('CarrierId1',strtoupper($_POST['tempcontainer']));
					$xmlMain->addChild('CarrierId2',strtoupper($_POST['tempcontainer']));
					if(strlen($_POST['tempban'])==8){
						$xmlMain->addChild('PrintMark','Y');
					}
					else{
						$xmlMain->addChild('PrintMark','N');
					}
				}
				else{
					$xmlMain->addChild('DonateMark','0');
					$xmlMain->addChild('PrintMark','Y');
				}
			}
			else{
				$xmlMain->addChild('DonateMark','0');
				$xmlMain->addChild('PrintMark','Y');
			}
			srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
			$rnumber=rand(0,9999);
			while(strlen($rnumber)<4){
				$rnumber='0'.$rnumber;
			}
			$xmlMain->addChild('RandomNumber',$rnumber);
			$xml->addChild('Details');
			$value='';
			$xmlDetails=$xml->Details;
			if($_POST['invlist']=='2'){//總項
				$xmlDetails->addChild('ProductItem');
				$value=$value.'"'.$content['basic']['itemname'].'",';
				$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
				$value=$value.'1,';
				$xmlDetails->ProductItem[0]->addChild('Quantity','1');
				$value=$value.$list[0]['invmoney'].',';
				$xmlDetails->ProductItem[0]->addChild('UnitPrice',$list[0]['invmoney']);
				$value=$value.$list[0]['invmoney'].',';
				$xmlDetails->ProductItem[0]->addChild('Amount',$list[0]['invmoney']);
				$value=$value.'1';
				$xmlDetails->ProductItem[0]->addChild('SequenceNumber','001');
				$xmlDetails->ProductItem[0]->addChild('Remark','Tx');
			}
			else{//明細
				$taste=parse_ini_file('../../../database/'.$content['basic']['company'].'-taste.ini',true);
				$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
				$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"';
				$itemlist=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				for($i=0,$j=0;$i<sizeof($itemlist);$i=$i+2){
					if(isset($itemlist[$i+1]['AMT'])){
						if((intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']))>0){
							$xmlDetails->addChild('ProductItem');
							$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
							$xmlDetails->ProductItem[$j]->addChild('Description',$itemlist[$i]['ITEMNAME']);
							$value=$value.$itemlist[$i]['QTY'].',';
							$xmlDetails->ProductItem[$j]->addChild('Quantity',$itemlist[$i]['QTY']);
							$unitprice=intval($itemlist[$i]['UNITPRICE']);
							for($t=1;$t<=10;$t++){
								if($itemlist[$i]['SELECTIVEITEM'.$t]==null){
									break;
								}
								else{
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									$temptaste=preg_split('/,/',$itemlist[$i]['SELECTIVEITEM'.$t]);
									for($k=0;$k<sizeof($temptaste);$k++){
										if(substr($temptaste[$k],0,5)!='99999'){
											$unitprice=intval($unitprice)+intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1));
										}
										else{
										}
									}
								}
								/*else{
									$unitprice=intval($unitprice)+intval($taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],5,1));
								}*/
							}
							$value=$value.$unitprice.',';
							$xmlDetails->ProductItem[$j]->addChild('UnitPrice',$unitprice);
							$value=$value.(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT'])).',';
							$xmlDetails->ProductItem[$j]->addChild('Amount',(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT'])));
							$value=$value.'1';
							$xmlDetails->ProductItem[$j]->addChild('SequenceNumber',str_pad(($j+1),3,'0',STR_PAD_LEFT));
							$xmlDetails->ProductItem[$j]->addChild('Remark','Tx');
							$j++;
						}
						else{
						}
					}
					else{
						if(intval($itemlist[$i]['AMT'])>0){
							$xmlDetails->addChild('ProductItem');
							$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
							$xmlDetails->ProductItem[$j]->addChild('Description',$itemlist[$i]['ITEMNAME']);
							$value=$value.$itemlist[$i]['QTY'].',';
							$xmlDetails->ProductItem[$j]->addChild('Quantity',$itemlist[$i]['QTY']);
							$unitprice=intval($itemlist[$i]['UNITPRICE']);
							for($t=1;$t<=10;$t++){
								if($itemlist[$i]['SELECTIVEITEM'.$t]==null){
									break;
								}
								else{
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									$temptaste=preg_split('/,/',$itemlist[$i]['SELECTIVEITEM'.$t]);
									for($k=0;$k<sizeof($temptaste);$k++){
										if(substr($temptaste[$k],0,5)!='99999'){
											$unitprice=intval($unitprice)+intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1));
										}
										else{
										}
									}
								}
								/*else{
									$unitprice=intval($unitprice)+intval($taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],5,1));
								}*/
							}
							$value=$value.$unitprice.',';
							$xmlDetails->ProductItem[$j]->addChild('UnitPrice',$unitprice);
							$value=$value.intval($itemlist[$i]['AMT']).',';
							$xmlDetails->ProductItem[$j]->addChild('Amount',intval($itemlist[$i]['AMT']));
							$value=$value.'1';
							$xmlDetails->ProductItem[$j]->addChild('SequenceNumber',str_pad(($j+1),3,'0',STR_PAD_LEFT));
							$xmlDetails->ProductItem[$j]->addChild('Remark','Tx');
							$j++;
						}
						else{
						}
					}
				}
			}
			$xml->addChild('Amount');
			$xmlAmount=$xml->Amount;
			/*if($init['init']['invmoneytype']=='0'){//2020/2/6由於之前沒有產品inv設定值，目前該邏輯相同，判斷於寫資料庫前即判斷完成
				$invmoney=floatval($list[0]['SALESTTLAMT'])+floatval($list[0]['TAX1']);
			}
			else{*/
				$invmoney=$list[0]['invmoney'];
			//}
			if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
				$xmlAmount->addChild('SalesAmount',round(intval($invmoney)/1.05));
				$xmlAmount->addChild('FreeTaxSalesAmount','0');
				$xmlAmount->addChild('ZeroTaxSalesAmount','0');
				$xmlAmount->addChild('TaxType','1');
				$xmlAmount->addChild('TaxRate','0.05');
				$xmlAmount->addChild('TaxAmount',intval($invmoney)-intval(round($invmoney/1.05)));
				$xmlAmount->addChild('TotalAmount',$invmoney);
			}
			else{
				$xmlAmount->addChild('SalesAmount',$invmoney);
				$xmlAmount->addChild('FreeTaxSalesAmount','0');
				$xmlAmount->addChild('ZeroTaxSalesAmount','0');
				$xmlAmount->addChild('TaxType','1');
				$xmlAmount->addChild('TaxRate','0.05');
				$xmlAmount->addChild('TaxAmount','0');
				$xmlAmount->addChild('TotalAmount',$invmoney);
			}
			if(intval(date('m'))%2==0){
				$month=date('m');
			}
			else{
				$month=intval(date('m'))+1;
			}
			if(strlen($month)<2){
				$month='0'.$month;
			}
			$xml->saveXML('../../../print/noread/'.$filename);
		}
		else if($content['basic']['sendinvlocation']=='2'){//神通
			$filename='mC0401_'.$table[0]['banno'].'_'.$Y.$month.$day.$hour.$min.$sec.'_'.$invmachine.'.json';
			$invjson=array();
			//Store
			$invjson['Store']=null;
			//Main
			$invjson['Main']['InvoiceNumber']=$table[0]['banno'];
			$date=$Y.$month.$day;
			$invjson['Main']['InvoiceDate']=$date;
			$time=$hour.':'.$min.':'.$sec;
			$invjson['Main']['InvoiceTime']=$time;
			//Seller
			$invjson['Seller']['Identifier']=$content['basic']['Identifier'];
			$invjson['Seller']['Name']=$content['basic']['Name'];
			$invjson['Seller']['Address']=$content['basic']['address'];
			$invjson['Seller']['PersonInCharge']=null;//負責人姓名
			$invjson['Seller']['TelephoneNumber']=$content['basic']['tel'];
			$invjson['Seller']['FacsimileNumber']=null;//傳真號碼
			$invjson['Seller']['EmailAddress']=null;//電子郵件地址
			$invjson['Seller']['CustomerNumber']=null;//客戶編號
			$invjson['Seller']['RoleRemark']=null;//營業人角色註記
			//Buyer
			if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
				$buyerid=$_POST['tempban'];
				$invjson['Buyer']['Identifier']=$_POST['tempban'];
				$buyername='0000';
				$invjson['Buyer']['Name']='0000';
			}
			else{
				$buyerid='0000000000';
				$invjson['Buyer']['Identifier']='0000000000';
				$buyername='0000000000';
				$invjson['Buyer']['Name']='0000000000';
			}
			$invjson['Buyer']['Address']=null;//地址
			$invjson['Buyer']['PersonInCharge']=null;//負責人姓名
			$invjson['Buyer']['TelephoneNumber']=null;//電話號碼
			$invjson['Buyer']['FacsimileNumber']=null;//傳真號碼
			$invjson['Buyer']['EmailAddress']=null;//電子郵件地址
			$invjson['Buyer']['CustomreNumber']=null;//客戶編號
			$invjson['Buyer']['RoleRemark']=null;//營業人角色註記

			$invjson['CheckNumber']=null;//發票檢查碼
			$invjson['BuyerRemark']=null;//買受人註記欄
			//從 神通 聽說，發票上不能列印信用卡後四碼
			/*if(isset($list[0]['CREDITCARD'])&&$list[0]['CREDITCARD']!=''&&$list[0]['CREDITCARD']!=null){
				$xmlMain->addChild('MainRemark',"信用卡尾碼：".$list[0]['CREDITCARD']);
			}
			else{
			}*/
			$invjson['MainRemark']=null;//總備註
			$invjson['CustomsClearanceMark']=null;//通關方式註記
			$invjson['Category']=null;//沖帳別
			$invjson['RelateNumber']=$list[0]['CONSECNUMBER'];
			$invjson['InvoiceType']='07';
			$invjson['GroupMark']=null;//彙開註記
			if(isset($_POST['tempcontainer'])&&strlen($_POST['tempcontainer'])>0){
				if(substr($_POST['tempcontainer'],0,1)=='/'){//手機條碼
					$invjson['DonateMark']='0';
					$invjson['CarriewType']='3J0002';
					$invjson['CarrierId1']=strtoupper($_POST['tempcontainer']);
					$invjson['CarrierId2']=strtoupper($_POST['tempcontainer']);
					if(strlen($_POST['tempban'])==8){
						$invjson['PrintMark']='Y';
					}
					else{
						$invjson['PrintMark']='N';
					}
					$invjson['NPOBAN']=null;//發票捐贈對象
				}
				else if(is_numeric(substr($_POST['tempcontainer'],0,1))){//愛心碼
					$invjson['DonateMark']='1';
					$invjson['CarriewType']=null;//載具類別號碼
					$invjson['CarrierId1']=null;//載具引碼id
					$invjson['CarrierId2']=null;//載具引碼id
					$invjson['PrintMark']='N';
					$invjson['NPOBAN']=$_POST['tempcontainer'];
				}
				else if(strlen($_POST['tempcontainer'])==16){//自然人憑證
					$invjson['DonateMark']='0';
					$invjson['CarriewType']='CQ0001';
					$invjson['CarrierId1']=strtoupper($_POST['tempcontainer']);
					$invjson['CarrierId2']=strtoupper($_POST['tempcontainer']);
					if(strlen($_POST['tempban'])==8){
						$invjson['PrintMark']='Y';
					}
					else{
						$invjson['PrintMark']='N';
					}
					
					$invjson['NPOBAN']=null;//發票捐贈對象
				}
				else{
					$invjson['DonateMark']='0';
					$invjson['CarriewType']=null;//載具類別號碼
					$invjson['CarrierId1']=null;//載具引碼id
					$invjson['CarrierId2']=null;//載具引碼id
					$invjson['PrintMark']='Y';
					$invjson['NPOBAN']=null;//發票捐贈對象
				}
			}
			else{
				$invjson['DonateMark']='0';
				$invjson['CarriewType']=null;//載具類別號碼
				$invjson['CarrierId1']=null;//載具引碼id
				$invjson['CarrierId2']=null;//載具引碼id
				$invjson['PrintMark']='Y';
				$invjson['NPOBAN']=null;//發票捐贈對象
			}
			srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
			$rnumber=rand(0,9999);
			while(strlen($rnumber)<4){
				$rnumber='0'.$rnumber;
			}
			$invjson['RandomNumber']=$rnumber;
			//Detail
			$value='';
			if($_POST['invlist']=='2'){//總項
				$items=array();
				$value=$value.'"'.$content['basic']['itemname'].'",';
				$items['Description']=$content['basic']['itemname'];
				$value=$value.'1,';
				$items['Quantity']='1';
				$items['Unit']='份';
				$value=$value.$list[0]['invmoney'].',';
				$items['UnitPrice']=$list[0]['invmoney'];
				$value=$value.$list[0]['invmoney'].',';
				$items['Amount']=$list[0]['invmoney'];
				$value=$value.'1';
				$items['SequenceNumber']='1';
				$items['Remark']=null;//單一欄位備註
				$items['RelateNumber']=null;//相關號碼
				$invjson['Detail']['Production'][]=$items;
			}
			else{//明細
				$taste=parse_ini_file('../../../database/'.$content['basic']['company'].'-taste.ini',true);
				$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
				$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"';
				$itemlist=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				for($i=0,$j=0;$i<sizeof($itemlist);$i=$i+2){
					$items=array();
					if(isset($itemlist[$i+1]['AMT'])){
						if((intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']))>0){
							$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
							$items['Description']=$itemlist[$i]['ITEMNAME'];
							$value=$value.$itemlist[$i]['QTY'].',';
							$items['Quantity']=$itemlist[$i]['QTY'];
							$items['Unit']='份';
							$unitprice=intval($itemlist[$i]['UNITPRICE']);
							for($t=1;$t<=10;$t++){
								if($itemlist[$i]['SELECTIVEITEM'.$t]==null){
									break;
								}
								else{
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									$temptaste=preg_split('/,/',$itemlist[$i]['SELECTIVEITEM'.$t]);
									for($k=0;$k<sizeof($temptaste);$k++){
										if(substr($temptaste[$k],0,5)!='99999'){
											$unitprice=intval($unitprice)+intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1));
										}
										else{
										}
									}
								}
								/*else{
									$unitprice=intval($unitprice)+intval($taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],5,1));
								}*/
							}
							$value=$value.$unitprice.',';
							$items['UnitPrice']=$unitprice;
							$value=$value.(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT'])).',';
							$items['Amount']=(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']));
							$value=$value.'1';
							$items['SequenceNumber']=str_pad(($j+1),3,'0',STR_PAD_LEFT);
							$items['Remark']=null;//單一欄位備註
							$items['RelateNumber']=null;//相關號碼
							$j++;
						}
						else{
						}
					}
					else{
						if(intval($itemlist[$i]['AMT'])>0){
							$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
							$items['Description']=$itemlist[$i]['ITEMNAME'];
							$value=$value.$itemlist[$i]['QTY'].',';
							$items['Quantity']=$itemlist[$i]['QTY'];
							$items['Unit']='份';
							$unitprice=intval($itemlist[$i]['UNITPRICE']);
							for($t=1;$t<=10;$t++){
								if($itemlist[$i]['SELECTIVEITEM'.$t]==null){
									break;
								}
								else{
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									$temptaste=preg_split('/,/',$itemlist[$i]['SELECTIVEITEM'.$t]);
									for($k=0;$k<sizeof($temptaste);$k++){
										if(substr($temptaste[$k],0,5)!='99999'){
											$unitprice=intval($unitprice)+intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1));
										}
										else{
										}
									}
								}
								/*else{
									$unitprice=intval($unitprice)+intval($taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],5,1));
								}*/
							}
							$value=$value.$unitprice.',';
							$items['UnitPrice']=$unitprice;
							$value=$value.intval($itemlist[$i]['AMT']).',';
							$items['Amount']=intval($itemlist[$i]['AMT']);
							$value=$value.'1';
							$items['SequenceNumber']=str_pad(($j+1),3,'0',STR_PAD_LEFT);
							$items['Remark']=null;//單一欄位備註
							$items['RelateNumber']=null;//相關號碼
							$j++;
						}
						else{
						}
					}
					$invjson['Detail']['Production'][]=$items;
				}
			}
			//Amount
			/*if($init['init']['invmoneytype']=='0'){//2020/2/6由於之前沒有產品inv設定值，目前該邏輯相同，判斷於寫資料庫前即判斷完成
				$invmoney=floatval($list[0]['SALESTTLAMT'])+floatval($list[0]['TAX1']);
			}
			else{*/
				$invmoney=$list[0]['invmoney'];
			//}
			if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
				$invjson['Amount']['SaleAmount']=round(intval($invmoney)/1.05);
				$invjson['Amount']['FreeTaxSalesAmount']=0;
				$invjson['Amount']['ZeroTaxSalesAmount']=0;
				$invjson['Amount']['TaxType']='1';
				$invjson['Amount']['TaxRate']=0.05;
				$invjson['Amount']['TaxAmount']=(intval($invmoney)-intval(round($invmoney/1.05)));
				$invjson['Amount']['TotalAmount']=$invmoney;
			}
			else{
				$invjson['Amount']['SaleAmount']=$invmoney;
				$invjson['Amount']['FreeTaxSalesAmount']=0;
				$invjson['Amount']['ZeroTaxSalesAmount']=0;
				$invjson['Amount']['TaxType']='1';
				$invjson['Amount']['TaxRate']=0.05;
				$invjson['Amount']['TaxAmount']=0;
				$invjson['Amount']['TotalAmount']=$invmoney;

			}
			$invjson['Amount']['DiscountCurrencyAmount']=null;//扣抵金額
			$invjson['Amount']['OriginalCurrencyAmount']=null;//原幣金額
			$invjson['Amount']['ExchangeRate']=null;//匯率
			$invjson['Amount']['Currency']=null;//幣別

			if(intval(date('m'))%2==0){
				$month=date('m');
			}
			else{
				$month=intval(date('m'))+1;
			}
			if(strlen($month)<2){
				$month='0'.$month;
			}
			$f=fopen('../../../print/noread/'.$filename,'w');
			fwrite($f,json_encode($invjson));
			fclose($f);
			//$xml->saveXML('../../../print/noread/'.$filename);
		}
		else if($content['basic']['sendinvlocation']=='3'){//中鼎
			if(!isset($content['zdninv']['useEIP'])||$content['zdninv']['useEIP']=='1'){
				$filename='C0401_'.$table[0]['banno'].'_'.$Y.$month.$day.$hour.$min.$sec.'_'.$invmachine.'.xml';
				for($i=1;$i<=19;$i++){
					$invarray[$i]='';
				}
				$invarray[9]=$table[0]['banno'];
				$date=$Y.$month.$day;
				$time=$hour.':'.$min.':'.$sec;
				$invarray[11]=$date.'-'.$hour.$min.$sec;
				$invarray[8]=$content['basic']['Identifier'];
				$invarray[2]=$content['basic']['Name'];
				if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
					$buyerid=$_POST['tempban'];
					$buyername='0000';
					$invarray[19]=$_POST['tempban'];
					$invarray[17]='';
				}
				else{
					$buyerid='0000000000';
					$buyername='0000000000';
					$invarray[19]='';
					$invarray[17]='';
				}
				$invarray[18]=$list[0]['CONSECNUMBER'];
				if(isset($_POST['tempcontainer'])&&strlen($_POST['tempcontainer'])>0){
					if(substr($_POST['tempcontainer'],0,1)=='/'){//手機條碼
						$invarray[1]='';
						$invarray[12]=strtoupper($_POST['tempcontainer']);
						$invarray[13]='';
					}
					else if(is_numeric(substr($_POST['tempcontainer'],0,1))){//愛心碼
						$invarray[1]='';
						$invarray[12]='';
						$invarray[13]=$_POST['tempcontainer'];
					}
					else if(strlen($_POST['tempcontainer'])==16){//自然人憑證
						$invarray[1]='';
						$invarray[12]=strtoupper($_POST['tempcontainer']);
						$invarray[13]='';
					}
					else{
						$invarray[1]='';
						$invarray[12]='';
						$invarray[13]='';
					}
				}
				else{
					if(isset($content['zdninv']['printtype'])){
						$invarray[1]=$content['zdninv']['printtype'];
					}
					else{
						$invarray[1]='';
					}
					$invarray[12]='';
					$invarray[13]='';
				}
				srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
				$rnumber=rand(0,9999);
				while(strlen($rnumber)<4){
					$rnumber='0'.$rnumber;
				}
				$invarray[10]=$rnumber;
				if($_POST['invlist']=='2'){//總項
					$invarray[20]=$content['basic']['itemname'];
					$invarray[21]='1';
					$invarray[22]=$list[0]['invmoney'];
					$invarray[23]=$list[0]['invmoney'];
					$invarray[24]='001';
				}
				else{//明細
					$invarray[1]='Y';//2019/12/24預設列印發票明細
					$menu=parse_ini_file('../../../database/'.$content['basic']['company'].'-menu.ini',true);
					$taste=parse_ini_file('../../../database/'.$content['basic']['company'].'-taste.ini',true);
					$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
					$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"';
					$itemlist=sqlquery($conn,$sql,'sqlite');
					sqlclose($conn,'sqlite');
					for($i=0,$j=0;$i<sizeof($itemlist);$i=$i+2){
						if(isset($menu[intval($itemlist[$i]['ITEMCODE'])]['insaleinv'])&&$menu[intval($itemlist[$i]['ITEMCODE'])]['insaleinv']=='0'){//2020/2/10免稅商品不列印於明細
						}
						else{
							if(isset($itemlist[$i+1]['AMT'])){
								if((intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']))>0){
									$unitprice=intval($itemlist[$i]['UNITPRICE']);
									for($t=1;$t<=10;$t++){
										if($itemlist[$i]['SELECTIVEITEM'.$t]==null){
											break;
										}
										else{
											//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
											$temptaste=preg_split('/,/',$itemlist[$i]['SELECTIVEITEM'.$t]);
											for($k=0;$k<sizeof($temptaste);$k++){
												if(substr($temptaste[$k],0,5)!='99999'){
													$unitprice=intval($unitprice)+intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1));
												}
												else{
												}
											}
										}
										/*else{
											if(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5)!='99999'){
												$unitprice=intval($unitprice)+intval($taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],5,1));
											}
											else{
											}
										}*/
									}

									$invarray[intval(20)+$j*5]=$itemlist[$i]['ITEMNAME'];
									$invarray[intval(21)+$j*5]=$itemlist[$i]['QTY'];
									$invarray[intval(22)+$j*5]=$unitprice;
									$invarray[intval(23)+$j*5]=(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']));
									$invarray[intval(24)+$j*5]=str_pad(($j+1),3,'0',STR_PAD_LEFT);
									$j++;
								}
								else{
								}
							}
							else{
								if(intval($itemlist[$i]['AMT'])!=0){
									$unitprice=intval($itemlist[$i]['UNITPRICE']);
									for($t=1;$t<=10;$t++){
										if($itemlist[$i]['SELECTIVEITEM'.$t]==null){
											break;
										}
										else{
											//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
											$temptaste=preg_split('/,/',$itemlist[$i]['SELECTIVEITEM'.$t]);
											for($k=0;$k<sizeof($temptaste);$k++){
												if(substr($temptaste[$k],0,5)!='99999'){
													$unitprice=intval($unitprice)+intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1));
												}
												else{
												}
											}
										}
										/*else{
											if(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5)!='99999'){
												$unitprice=intval($unitprice)+intval($taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],5,1));
											}
											else{
											}
										}*/
									}

									$invarray[intval(20)+$j*5]=$itemlist[$i]['ITEMNAME'];
									$invarray[intval(21)+$j*5]=$itemlist[$i]['QTY'];
									$invarray[intval(22)+$j*5]=$unitprice;
									$invarray[intval(23)+$j*5]=intval($itemlist[$i]['AMT']);
									$invarray[intval(24)+$j*5]=str_pad(($j+1),3,'0',STR_PAD_LEFT);
									$j++;
								}
								else{
								}
							}
						}
					}
				}
				$invmoney=$list[0]['invmoney'];

				if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){

					$invarray[14]=round(intval($invmoney)/1.05);
					$invarray[15]='0';
					$invarray[16]=intval($invmoney)-intval(round($invmoney/1.05));
					$invarray[7]=$invmoney;
				}
				else{
					
					$invarray[14]=$invmoney;
					$invarray[15]='0';
					$invarray[16]='0';
					$invarray[7]=$invmoney;
				}
				
				$inv=fopen('../../../print/noread/'.$filename,'w');
				foreach($invarray as $i){
					fwrite($inv,$i.PHP_EOL);
				}
				fclose($inv);

				if((!isset($invarray[9])||$invarray[9]=="")||(!isset($invarray[20])||$invarray[20]=="")){//2020/11/6 發票內容(data.txt)不完整
					$f=fopen('../../../printlog.txt','a');
					fwrite($f,date('Y/m/d H:i:s').' -- data.txt('.$filename.') is not complete. : ');
					if(!isset($invarray[9])||$invarray[9]==""){
						fwrite($f,'invnumber is empty.'.PHP_EOL);
					}
					else{
						fwrite($f,'salelist is empty.'.PHP_EOL);
					}
					fclose($f);
				}
				else{
				}
			}
			else{//2021/5/27 產生發票PDF，由我們自己印出發票。以及產生上傳發票資料的JSON檔案
				if(isset($_POST['tempcontainer'])&&strlen($_POST['tempcontainer'])>0){//2021/5/27 提前到這邊方便後續判斷是否要列印紙本發票證明聯
					if(substr($_POST['tempcontainer'],0,1)=='/'){//手機條碼
						$xml['Main']['DonateMark']='0';
						$xml['Main']['CarrierType']='3J0002';
						$xml['Main']['CarrierId1']=strtoupper($_POST['tempcontainer']);
						$xml['Main']['CarrierId2']=strtoupper($_POST['tempcontainer']);
						if(strlen($_POST['tempban'])==8){
							$xml['Main']['PrintMark']='Y';
						}
						else{
							$xml['Main']['PrintMark']='N';
						}
					}
					else if(is_numeric(substr($_POST['tempcontainer'],0,1))){//愛心碼
						$xml['Main']['DonateMark']='1';
						$xml['Main']['PrintMark']='N';
						$xml['Main']['NPOBAN']=$_POST['tempcontainer'];
					}
					else if(strlen($_POST['tempcontainer'])==16){//自然人憑證
						$xml['Main']['DonateMark']='0';
						$xml['Main']['CarrierType']='CQ0001';
						$xml['Main']['CarrierId1']=strtoupper($_POST['tempcontainer']);
						$xml['Main']['CarrierId2']=strtoupper($_POST['tempcontainer']);
						if(strlen($_POST['tempban'])==8){
							$xml['Main']['PrintMark']='Y';
						}
						else{
							$xml['Main']['PrintMark']='N';
						}
					}
					else{
						$xml['Main']['DonateMark']='0';
						$xml['Main']['PrintMark']='Y';
					}
				}
				else{
					$xml['Main']['DonateMark']='0';
					$xml['Main']['PrintMark']='Y';
				}
				if($xml['Main']['PrintMark']=='Y'){
					include_once '../../../tool/AES/lib.php';
					include_once '../../../tool/phpqrcode/qrlib.php';
					include_once '../../../tool/phpbarcode/src/BarcodeGenerator.php';
					include_once '../../../tool/phpbarcode/src/BarcodeGeneratorPNG.php';
					
					//產生電子發票開立資訊PDF
					require_once('../../../tool/TCPDF/examples/tcpdf_include.php');
					//$pdf = new TCPDF(P:直式、L:橫式, 單位(mm), 紙張大小(長短邊；不分長寬：array(,) ), true, 'UTF-8', false);
					$pdf = new TCPDF("P", "mm", array(72,297), true, "UTF-8", false);
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('Nicola Asuni');
					$pdf->SetTitle('invoice');
					//$pdf->SetSubject('TCPDF Tutorial');
					//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
					$pdf->setPrintHeader(false);
					$pdf->SetHeaderMargin(0);
					$pdf->setPrintFooter(false);
					$pdf->SetMargins(2, 0, 22);
					if (@file_exists(dirname(__FILE__).'/../../../tool/TCPDF/examples/lang/eng.php')) {
						require_once(dirname(__FILE__).'/../../../tool/TCPDF/examples/lang/eng.php');
						$pdf->setLanguageArray($l);
					}
					//$pdf->SetFont('DroidSansFallback', '', 10);
					$pdf->AddPage();
					//$pdf->MultiCell(寬, 高, 內容, 框線, 對齊：L靠左、C置中、R靠右, 是否填塞, 下一個元件的位置：「0（預設）右邊；1下行最左邊；2目前元件下方」, X軸, Y軸, 若true會重設最後一格的高度, 0不延伸；1字大於格寬才縮放文字；2一律縮放文字到格寬；3字大於格寬才縮放字距；4一律縮放字距到格寬、「$ignore_min_height」自動忽略最小高度, 0, 自動調整內距, 高度上限, 垂直對齊T、C、B, 自動縮放字大小到格內);
					$pdf->SetFont('DroidSansFallback', '', 12);
					if(substr($table[0]['banno'],0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
						$pdf->MultiCell('', '', "測試發票", 0, 'C', 0, 0, 0, 0, 1, 0, 0, 0, 10, 'T', 0);
					}
					else if(isset($content['zdninv']['imgtitle'])&&$content['zdninv']['imgtitle']==='1'&&file_exists('../../../database/invlogo/logo.png')){
						$pdf->writeHTMLCell('','','','','<img src="../../../database/invlogo/logo.png" />',0,1,false,false,'C',1);
					}
					else if(isset($content['zdninv']['titletext'])){
						$pdf->MultiCell('', '', $content['zdninv']['titletext'], 0, 'C', 0, 0, 0, 0, 1, 0, 0, 0, 10, 'T', 0);
					}
					else{
						$pdf->MultiCell('', '', "", 0, 'C', 0, 0, 0, 0, 1, 0, 0, 0, 10, 'T', 0);
					}
					$pdf->SetFont('DroidSansFallback', 'B', 17);
					$pdf->MultiCell('', '', "電子發票證明聯", 0, 'C', 0, 0, 0, 8.5, 1, 0, 0, 0, 10, 'T', 0);
					$pdf->SetFont('DroidSansFallback', 'B', 17);
					$pdf->MultiCell('', '', $year."年".str_pad((intval($m)-1),2,'0',STR_PAD_LEFT)."－".$m."月", 0, 'C', 0, 0, 0, 15, 1, 0, 0, 0, 10, 'T', 0);
					$pdf->MultiCell('', '', substr($table[0]['banno'],0,2).'－'.substr($table[0]['banno'],2), 0, 'C', 0, 0, 0, 21.5, 1, 0, 0, 0, 10, 'T', 0);
				}
				else{
				}

				$filename='C0401_'.$content['basic']['Identifier'].'_'.$table[0]['banno'].'_'.$month.$day.$hour.$min.$sec.'.json';
				$xml['Main']['InvoiceNumber']=$table[0]['banno'];
				srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
				$rnumber=rand(0,9999);
				while(strlen($rnumber)<4){
					$rnumber='0'.$rnumber;
				}
				$xml['Main']['RandomNumber']=$rnumber;
				$date=$Y.$month.$day;
				$xml['Main']['InvoiceDate']=$date;
				$time=$hour.':'.$min.':'.$sec;
				$xml['Main']['InvoiceTime']=$time;
				if($xml['Main']['PrintMark']=='Y'){
					$pdf->SetFont('DroidSansFallback', '', 9);
					$pdf->MultiCell('', '', "隨機碼:".$rnumber, 0, 'L', 0, 0, 3, 32.5, 1, 0, 0, 0, 10, 'T', 0);//2021/5/27 因為cell有指定(x,y)座標，所以不用依照順序填入
					$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
					file_put_contents('../../../print/barcode/'.$year.$m.$table[0]['banno'].$rnumber.'barcode.png', $generator->getBarcodeNoText(($year.$m.$table[0]['banno'].$rnumber), $generator::TYPE_CODE_39));
					$pdf->Image('../../../print/barcode/'.$year.$m.$table[0]['banno'].$rnumber.'barcode.png',3,35,45,20,'png','','M',1,300);
					$pdf->SetFont('DroidSansFallback', '', 9);
					$pdf->MultiCell('', '', $Y.'-'.$month.'-'.$day." ".$time, 0, 'L', 0, 0, 3, 29, 1, 0, 0, 0, 10, 'T', 0);
				}
				else{
				}
				$xml['Main']['Seller']['Identifier']=$content['basic']['Identifier'];
				$xml['Main']['Seller']['Name']=$content['basic']['Name'];
				$xml['Main']['Seller']['Address']=$content['basic']['address'];
				$invmoney=$list[0]['invmoney'];
				if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
					$buyerid=$_POST['tempban'];
					$xml['Main']['Buyer']['Identifier']=$_POST['tempban'];
					if($xml['Main']['PrintMark']=='Y'){
						$pdf->MultiCell('', '', "格式:25", 0, 'L', 0, 0, 35, 29, 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell('', '', "買方".$_POST['tempban'], 0, 'L', 0, 0, 26, 36, 1, 0, 0, 0, 10, 'T', 0);//2021/5/27 因為cell有指定(x,y)座標，所以不用依照順序填入
					}
					else{
					}
					$buyername='0000';
					$xml['Main']['Buyer']['Name']='0000';
					$buyerhexmoney=str_pad((intval($invmoney)-intval(round($invmoney/1.05))),8,'0',STR_PAD_LEFT);
					$xml['Amount']['SalesAmount']=intval(round($invmoney/1.05));
					$xml['Amount']['FreeTaxSalesAmount']='0';
					$xml['Amount']['ZeroTaxSalesAmount']='0';
					$xml['Amount']['TaxType']='1';
					$xml['Amount']['TaxRate']='0.05';
					$xml['Amount']['TaxAmount']=intval($invmoney)-intval(round($invmoney/1.05));
					$xml['Amount']['TotalAmount']=$invmoney;
				}
				else{
					$buyerid='0000000000';
					$xml['Main']['Buyer']['Identifier']='0000000000';
					$buyername='0000000000';
					$xml['Main']['Buyer']['Name']='0000000000';
					$buyerhexmoney='00000000';
					$xml['Amount']['SalesAmount']=$invmoney;
					$xml['Amount']['FreeTaxSalesAmount']='0';
					$xml['Amount']['ZeroTaxSalesAmount']='0';
					$xml['Amount']['TaxType']='1';
					$xml['Amount']['TaxRate']='0.05';
					$xml['Amount']['TaxAmount']='0';
					$xml['Amount']['TotalAmount']=$invmoney;
				}
				//2021/5/27 因為xml改為array，不用考慮順序，同時將底下運算合併至上方，方便運算buyerhexmoney(電子發票qrcode需要未稅金額)
				/*//if($init['init']['invmoneytype']=='0'){//2020/2/6由於之前沒有產品inv設定值，目前該邏輯相同，判斷於寫資料庫前即判斷完成
				//	$invmoney=floatval($list[0]['SALESTTLAMT'])+floatval($list[0]['TAX1']);
				//}
				//else{
					$invmoney=$list[0]['invmoney'];
				//}
				if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
					$xml['Amount']['SalesAmount']=round(intval($invmoney)/1.05);
					$xml['Amount']['FreeTaxSalesAmount']='0';
					$xml['Amount']['ZeroTaxSalesAmount']='0';
					$xml['Amount']['TaxType']='1';
					$xml['Amount']['TaxRate']='0.05';
					$xml['Amount']['TaxAmount']=intval($invmoney)-intval(round($invmoney/1.05));
					$xml['Amount']['TotalAmount']=$invmoney;
				}
				else{
					$xml['Amount']['SalesAmount']=$invmoney;
					$xml['Amount']['FreeTaxSalesAmount']='0';
					$xml['Amount']['ZeroTaxSalesAmount']='0';
					$xml['Amount']['TaxType']='1';
					$xml['Amount']['TaxRate']='0.05';
					$xml['Amount']['TaxAmount']='0';
					$xml['Amount']['TotalAmount']=$invmoney;
				}*/
				//2021/5/27 因為xml改為array，不用考慮順序，同時將底下運算合併至上方，方便運算buyerhexmoney(電子發票qrcode需要未稅金額)
				//從 神通 聽說，發票上不能列印信用卡後四碼
				/*if(isset($list[0]['CREDITCARD'])&&$list[0]['CREDITCARD']!=''&&$list[0]['CREDITCARD']!=null){
					$xmlMain['MainRemark',"信用卡尾碼：".$list[0]['CREDITCARD']);
				}
				else{
				}*/
				$xml['Main']['RelateNumber']=$list[0]['CONSECNUMBER'];
				$xml['Main']['InvoiceType']='07';
				/*if(isset($_POST['tempcontainer'])&&strlen($_POST['tempcontainer'])>0){//2021/5/27 提早到前面，方便判斷是否需要列印紙本發票證明聯
					if(substr($_POST['tempcontainer'],0,1)=='/'){//手機條碼
						$xml['Main']['DonateMark']='0';
						$xml['Main']['CarrierType']='3J0002';
						$xml['Main']['CarrierId1']=strtoupper($_POST['tempcontainer']);
						$xml['Main']['CarrierId2']=strtoupper($_POST['tempcontainer']);
						if(strlen($_POST['tempban'])==8){
							$xml['Main']['PrintMark']='Y';
						}
						else{
							$xml['Main']['PrintMark']='N';
						}
					}
					else if(is_numeric(substr($_POST['tempcontainer'],0,1))){//愛心碼
						$xml['Main']['DonateMark']='1';
						$xml['Main']['PrintMark']='N';
						$xml['Main']['NPOBAN']=$_POST['tempcontainer'];
					}
					else if(strlen($_POST['tempcontainer'])==16){//自然人憑證
						$xml['Main']['DonateMark']='0';
						$xml['Main']['CarrierType']='CQ0001';
						$xml['Main']['CarrierId1']=strtoupper($_POST['tempcontainer']);
						$xml['Main']['CarrierId2']=strtoupper($_POST['tempcontainer']);
						if(strlen($_POST['tempban'])==8){
							$xml['Main']['PrintMark']='Y';
						}
						else{
							$xml['Main']['PrintMark']='N';
						}
					}
					else{
						$xml['Main']['DonateMark']='0';
						$xml['Main']['PrintMark']='Y';
					}
				}
				else{
					$xml['Main']['DonateMark']='0';
					$xml['Main']['PrintMark']='Y';
				}*/
				/*srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());//2021/5/27 由於電子發票barcode需要隨機碼，因此往前提
				$rnumber=rand(0,9999);
				while(strlen($rnumber)<4){
					$rnumber='0'.$rnumber;
				}*/
				if($xml['Main']['PrintMark']=='Y'){
					$pdf->MultiCell('', '', "總計:".$list[0]['invmoney'], 0, 'L', 0, 0, 25, 32.5, 1, 0, 0, 0, 10, 'T', 0);
					$pdf->MultiCell('', '', "賣方".$content['basic']['Identifier'], 0, 'L', 0, 0, 3, 36, 1, 0, 0, 0, 10, 'T', 0);
					$qrcodeClass = new encryQrcode();
					$aesKey = "1905B9C0E27FB708712E42CED49178AB";// input your aeskey
					$invoiceNumAndRandomCode = $table[0]['banno'].$rnumber;// input your invoiceNumber And RandomCode
					$encry=$qrcodeClass->aes128_cbc_encrypt($aesKey, $invoiceNumAndRandomCode);
					QRcode::png($table[0]['banno'].$year.$month.$day.$rnumber.$buyerhexmoney.str_pad(dechex($list[0]['invmoney']),8,'0',STR_PAD_LEFT).$buyerid.$content['basic']['Identifier'].$encry.":**********:1:1:1:".$content['basic']['itemname'].":1:".$list[0]['invmoney'], "../../../print/qrcode/leftqrcode.png", "L", "4", 2);
					$pdf->Image("../../../print/qrcode/leftqrcode.png",5,51,20,20,'png','','M',1,300);
					QRcode::png("**                                                                                                                                   ", "../../../print/qrcode/rightqrcode.png", "L", "4", 2);
					$pdf->Image("../../../print/qrcode/rightqrcode.png",28,51,20,20,'png','','M',1,300);
					$pdf->SetFont('DroidSansFallback', '', 8.5);
					$pdf->MultiCell('', '', "機號:".$invmachine, 0, 'L', 0, 0, 3, 71, 1, 0, 0, 0, 10, 'T', 0);
					$pdf->MultiCell('', '', "單號:".$consecnumber, 0, 'R', 0, 1, 3, 71, 1, 0, 0, 0, 10, 'T', 0);

					//若發票檔頭為圖檔，則需要列印門市名
					if(substr($table[0]['banno'],0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
						//$pdf->MultiCell('', '', "測試發票", 0, 'C', 0, 0, 0, 0, 1, 0, 0, 0, 10, 'T', 0);
					}
					else if(isset($content['zdninv']['imgtitle'])&&$content['zdninv']['imgtitle']==='1'&&file_exists('../../../database/invlogo/logo.png')){
						if(isset($content['basic']['storyname'])){
							$pdf->MultiCell('', '', $content['basic']['storyname'], 0, 'L', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						else{
						}
					}
					else if(isset($content['zdninv']['titletext'])){
						//$pdf->MultiCell('', '', $content['zdninv']['titletext'], 0, 'C', 0, 0, 0, 0, 1, 0, 0, 0, 10, 'T', 0);
					}
					else{
						//$pdf->MultiCell('', '', "", 0, 'C', 0, 0, 0, 0, 1, 0, 0, 0, 10, 'T', 0);
					}
					
					//多列印發票抬頭，因為不是每家發票檔頭都是使用抬頭
					//$pdf->MultiCell('', '', $content['basic']['Name'], 0, 'L', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);

					$pdf->MultiCell('', '', "**退貨時請攜帶電子發票證明聯", 0, 'L', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
				}
				else{
				}
				$value='';
				if($_POST['invlist']=='2'){//總項
					$value=$value.'"'.$content['basic']['itemname'].'",';
					$xml['Details']['ProductItem'][0]['Description']=$content['basic']['itemname'];
					$value=$value.'1,';
					$xml['Details']['ProductItem'][0]['Quantity']='1';
					$value=$value.$list[0]['invmoney'].',';
					$xml['Details']['ProductItem'][0]['UnitPrice']=$list[0]['invmoney'];
					$value=$value.$list[0]['invmoney'].',';
					$xml['Details']['ProductItem'][0]['Amount']=$list[0]['invmoney'];
					$value=$value.'1';
					$xml['Details']['ProductItem'][0]['SequenceNumber']='001';
					$xml['Details']['ProductItem'][0]['Remark']='Tx';
					if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
						if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){//有買方統編時，必列印消費明細，且消費明細與證明聯不切分
							$pagey=80;
						}
						else{//if(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1')//列印消費明細，明細與證明聯切分兩張
							$pdf->lastPage();
							$pdf->AddPage();
							$pagey=0;
						}
						$pdf->SetFont('DroidSansFallback', '', 12);
						if(substr($table[0]['banno'],0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
							$pdf->MultiCell('', '', "測試發票", 0, 'C', 0, 1, 0, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						else if(isset($content['zdninv']['imgtitle'])&&$content['zdninv']['imgtitle']==='1'&&file_exists('../../../database/invlogo/logo.png')){
							$pdf->writeHTMLCell('','','','','<img src="../../../database/invlogo/logo.png" />',0,1,false,false,'C',1);
						}
						else if(isset($content['zdninv']['titletext'])){
							$pdf->MultiCell('', '', $content['zdninv']['titletext'], 0, 'C', 0, 1, 0, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						else{
							$pdf->MultiCell('', '', "", 0, 'C', 0, 1, 0, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						$pdf->MultiCell('', '', "交易明細", 0, 'C', 0, 1, 0, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->SetFont('DroidSansFallback', '', 8);
						$pdf->MultiCell('', '', $content['basic']['itemname'], 0, 'L', 0, 2, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell(15, '', $list[0]['invmoney']."x", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell(10, '', "1", 0, 'R', 0, 0, 18, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell(20, '', $list[0]['invmoney']."TX", 0, 'R', 0, 1, 28, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->SetFont('DroidSansFallback', '', 10);
						$pdf->MultiCell('', '', "---------------------------------------", 0, 'C', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->SetFont('DroidSansFallback', '', 8);
						$pdf->MultiCell('', '', "總計", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell('', '', "＄".$list[0]['invmoney'], 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
							$pdf->MultiCell('', '', "應稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄".intval(round($list[0]['invmoney']/1.05)), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "免稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄0", 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "稅額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄".(intval($list[0]['invmoney'])-intval(round($list[0]['invmoney']/1.05))), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						else{
						}
						$pdf->lastPage();
					}
					else{
					}
				}
				else{//明細
					$taste=parse_ini_file('../../../database/'.$content['basic']['company'].'-taste.ini',true);
					$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
					$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"';
					$itemlist=sqlquery($conn,$sql,'sqlite');
					sqlclose($conn,'sqlite');
					if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
						if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){//有買方統編時，必列印消費明細，且消費明細與證明聯不切分
							$pagey=80;
						}
						else{//if(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1')//列印消費明細，明細與證明聯切分兩張
							$pdf->lastPage();
							$pdf->AddPage();
							$pagey=0;
						}
						$pdf->SetFont('DroidSansFallback', '', 12);
						if(substr($table[0]['banno'],0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
							$pdf->MultiCell('', '', "測試發票", 0, 'C', 0, 1, 0, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						else if(isset($content['zdninv']['imgtitle'])&&$content['zdninv']['imgtitle']==='1'&&file_exists('../../../database/invlogo/logo.png')){
							$pdf->writeHTMLCell('','','','','<img src="../../../database/invlogo/logo.png" />',0,1,false,false,'C',1);
						}
						else if(isset($content['zdninv']['titletext'])){
							$pdf->MultiCell('', '', $content['zdninv']['titletext'], 0, 'C', 0, 1, 0, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						else{
							$pdf->MultiCell('', '', "", 0, 'C', 0, 1, 0, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						$pdf->MultiCell('', '', "交易明細", 0, 'C', 0, 1, 0, '', 1, 0, 0, 0, 10, 'T', 0);
					}
					else{
					}
					for($i=0,$j=0;$i<sizeof($itemlist);$i=$i+2){
						if(isset($itemlist[$i+1]['AMT'])){
							if((intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']))>0){
								$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
								$invname=$itemlist[$i]['ITEMNAME'];
								$xml['Details']['ProductItem'][$j]['Description']=$itemlist[$i]['ITEMNAME'];
								$value=$value.$itemlist[$i]['QTY'].',';
								$xml['Details']['ProductItem'][$j]['Quantity']=$itemlist[$i]['QTY'];
								$unitprice=intval($itemlist[$i]['UNITPRICE']);
								for($t=1;$t<=10;$t++){
									if($itemlist[$i]['SELECTIVEITEM'.$t]==null){
										if($t==1){
										}
										else{
											$invname.=')';
										}
										break;
									}
									else{
										//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
										$temptaste=preg_split('/,/',$itemlist[$i]['SELECTIVEITEM'.$t]);
										if($t==1){
											$invname.='(';
										}
										else{
										}
										for($k=0;$k<sizeof($temptaste);$k++){
											if($t!=1||$k!=0){
												$invname.=',';
											}
											else{
											}
											$invname.=$taste[intval(substr($temptaste[$k],0,5))]['name1'];
											if(substr($temptaste[$k],0,5)!='99999'){
												$unitprice=intval($unitprice)+intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1));
											}
											else{
											}
										}
									}
									/*else{
										if($t==1){
											$invname.='(';
										}
										else{
											$invname.=',';
										}
										$invname=$taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
										$unitprice=intval($unitprice)+intval($taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],5,1));
									}*/
								}
								$value=$value.$unitprice.',';
								$xml['Details']['ProductItem'][$j]['UnitPrice']=$unitprice;
								$value=$value.(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT'])).',';
								$xml['Details']['ProductItem'][$j]['Amount']=(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']));
								$value=$value.'1';
								$xml['Details']['ProductItem'][$j]['SequenceNumber']=str_pad(($j+1),3,'0',STR_PAD_LEFT);
								$xml['Details']['ProductItem'][$j]['Remark']='Tx';
								if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
									$pdf->SetFont('DroidSansFallback', '', 8);
									$pdf->MultiCell('', '', $invname, 0, 'L', 0, 2, 3, '', 1, 0, 0, 0, 10, 'T', 0);
									$pdf->MultiCell(15, '', $unitprice."x", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
									$pdf->MultiCell(10, '', $itemlist[$i]['QTY'], 0, 'R', 0, 0, 18, '', 1, 0, 0, 0, 10, 'T', 0);
									$pdf->MultiCell(20, '', (intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']))."TX", 0, 'R', 0, 1, 28, '', 1, 0, 0, 0, 10, 'T', 0);
								}
								else{
								}
								$j++;
							}
							else{
							}
						}
						else{
							if(intval($itemlist[$i]['AMT'])>0){
								$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
								$invname=$itemlist[$i]['ITEMNAME'];
								$xml['Details']['ProductItem'][$j]['Description']=$itemlist[$i]['ITEMNAME'];
								$value=$value.$itemlist[$i]['QTY'].',';
								$xml['Details']['ProductItem'][$j]['Quantity']=$itemlist[$i]['QTY'];
								$unitprice=intval($itemlist[$i]['UNITPRICE']);
								for($t=1;$t<=10;$t++){
									if($itemlist[$i]['SELECTIVEITEM'.$t]==null){
										if($t==1){
										}
										else{
											$invname.=')';
										}
										break;
									}
									else{
										//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
										$temptaste=preg_split('/,/',$itemlist[$i]['SELECTIVEITEM'.$t]);
										if($t==1){
											$invname.='(';
										}
										else{
										}
										for($k=0;$k<sizeof($temptaste);$k++){
											if($t!=1||$k!=0){
												$invname.=',';
											}
											else{
											}
											$invname.=$taste[intval(substr($temptaste[$k],0,5))]['name1'];
											if(substr($temptaste[$k],0,5)!='99999'){
												$unitprice=intval($unitprice)+intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1));
											}
											else{
											}
										}
									}
									/*else{
										if($t==1){
											$invname.='(';
										}
										else{
											$invname.=',';
										}
										$invname=$taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
										$unitprice=intval($unitprice)+intval($taste[intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($itemlist[$i]['SELECTIVEITEM'.$t],5,1));
									}*/
								}
								$value=$value.$unitprice.',';
								$xml['Details']['ProductItem'][$j]['UnitPrice']=$unitprice;
								$value=$value.intval($itemlist[$i]['AMT']).',';
								$xml['Details']['ProductItem'][$j]['Amount']=intval($itemlist[$i]['AMT']);
								$value=$value.'1';
								$xml['Details']['ProductItem'][$j]['SequenceNumber']=str_pad(($j+1),3,'0',STR_PAD_LEFT);
								$xml['Details']['ProductItem'][$j]['Remark']='Tx';
								if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
									$pdf->SetFont('DroidSansFallback', '', 8);
									$pdf->MultiCell('', '', $invname, 0, 'L', 0, 2, 3, '', 1, 0, 0, 0, 10, 'T', 0);
									$pdf->MultiCell(15, '', $unitprice."x", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
									$pdf->MultiCell(10, '', $itemlist[$i]['QTY'], 0, 'R', 0, 0, 18, '', 1, 0, 0, 0, 10, 'T', 0);
									$pdf->MultiCell(20, '', intval($itemlist[$i]['AMT'])."TX", 0, 'R', 0, 1, 28, '', 1, 0, 0, 0, 10, 'T', 0);
								}
								else{
								}
								$j++;
							}
							else{
							}
						}
					}
					if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
						$pdf->SetFont('DroidSansFallback', '', 10);
						$pdf->MultiCell('', '', "---------------------------------------", 0, 'C', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->SetFont('DroidSansFallback', '', 8);
						$pdf->MultiCell('', '', "總計", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell('', '', "＄".$list[0]['invmoney'], 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						if(!is_numeric(substr($_POST['tempcontainer'],0,1))&&strlen($_POST['tempban'])==8){
							$pdf->MultiCell('', '', "應稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄".intval(round($invmoney/1.05)), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "免稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄0", 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "稅額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄".(intval($invmoney)-intval(round($invmoney/1.05))), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
						}
						else{
						}
						$pdf->lastPage();
					}
					else{
					}
				}
				if(file_exists('../../../print/invuploadlog')){
				}
				else{
					mkdir('../../../print/invuploadlog');
				}
				if(file_exists('../../../print/invuploadlog/waitupload')){
				}
				else{
					mkdir('../../../print/invuploadlog/waitupload');
				}
				if(file_exists('../../../print/invuploadlog/uploaded')){
				}
				else{
					mkdir('../../../print/invuploadlog/uploaded');
				}
				if(file_exists('../../../print/invuploadlog/log')){
				}
				else{
					mkdir('../../../print/invuploadlog/log');
				}
				if(substr($table[0]['banno'],0,2)!='OO'){//2021/6/4 發票號碼開頭OO為測試發票，不產生json上傳檔案
					$f=fopen('../../../print/invuploadlog/waitupload/'.$filename,'w');
					fwrite($f,json_encode($xml));
					fclose($f);
				}
				else{
				}
				if($xml['Main']['PrintMark']=='Y'){
					$pdf->Output(dirname(__FILE__).'/../../../print/noread/'.$content['basic']['Identifier'].'_C0401'.$invmachine.'_'.$table[0]['banno'].'_'.$month.$day.$hour.$min.$sec.'.pdf', 'F');
				}
				else{//2021/7/15 不用列印紙本發票時，產生對應發票檔案，以利後續刪除 發票號碼 檔案
					$file=fopen('../../../print/noread/'.$invmachine.'_'.$table[0]['banno'].'.inv','w');
					fclose($file);
				}
			}

			if(intval(date('m'))%2==0){
				$month=date('m');
			}
			else{
				$month=intval(date('m'))+1;
			}
			if(strlen($month)<2){
				$month='0'.$month;
			}
		}
		$conn1=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
		if(isset($_POST['tempcontainer'])&&strlen($_POST['tempcontainer'])>0){
			if(substr($_POST['tempcontainer'],0,1)=='/'){//手機條碼
				$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$table[0]['banno'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$list[0]['CONSECNUMBER'].'","0","3J0002","'.strtoupper($_POST['tempcontainer']).'","'.strtoupper($_POST['tempcontainer']).'",';
				if(strlen($_POST['tempban'])==8){
					$sql=$sql.'"Y",';
				}
				else{
					$sql=$sql.'"N",';
				}
				$sql=$sql.'NULL,"'.$rnumber.'",'.$invmoney.')';
			}
			else if(is_numeric(substr($_POST['tempcontainer'],0,1))){//愛心碼
				$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$table[0]['banno'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$list[0]['CONSECNUMBER'].'","1",NULL,NULL,NULL,"N","'.$_POST['tempcontainer'].'","'.$rnumber.'",'.$invmoney.')';
			}
			else if(strlen($_POST['tempcontainer'])==16){//自然人憑證
				$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$table[0]['banno'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$list[0]['CONSECNUMBER'].'","0","CQ0001","'.strtoupper($_POST['tempcontainer']).'","'.strtoupper($_POST['tempcontainer']).'","N",NULL,"'.$rnumber.'",'.$invmoney.')';
			}
			else{
				$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$table[0]['banno'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$list[0]['CONSECNUMBER'].'","0",NULL,NULL,NULL,"Y",NULL,"'.$rnumber.'",'.$invmoney.')';
			}
		}
		else{
			$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$table[0]['banno'].'","'.$date.'","'.$time.'","'.$content['basic']['Identifier'].'","'.$content['basic']['Name'].'","'.$buyerid.'","'.$buyername.'","'.$list[0]['CONSECNUMBER'].'","0",NULL,NULL,NULL,"Y",NULL,"'.$rnumber.'",'.$invmoney.')';
		}
		sqlnoresponse($conn1,$sql,'sqlite');
		$sql='INSERT INTO salelist (listno,invnumber,createdate,createtime,name,qty,unitprice,money,lineno) VALUES ("'.$list[0]['CONSECNUMBER'].'","'.$table[0]['banno'].'","'.$date.'","'.$time.'","'.$content['basic']['itemname'].'",1,'.$invmoney.','.$invmoney.',1)';
		sqlnoresponse($conn1,$sql,'sqlite');
		sqlclose($conn1,'sqlite');
		echo $table[0]['banno'];
	}
	else{
		sqlclose($conn1,'sqlite');
		echo 'invoicenumber length is not enough';
	}
}
else{
	echo $invnumber[0]['INVOICENUMBER'];
}
?>