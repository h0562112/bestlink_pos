<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$content=parse_ini_file('../../../database/setup.ini',true);

//$_POST=$_GET;
$inv=$_POST['invno'];
$Y=date('Y');
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
$invdate=$Y.$m;
//$invdate=$Y.'08';
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
	$invmachine='';
}
//if(file_exists("../../../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db")){
	$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
	$sql='SELECT * FROM invlist WHERE invnumber="'.$inv.'" AND state=1';
	$item=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$time=preg_split('/:/',$item[0]['createtime']);
	//if(!file_exists('../../../trnx/Log/C0401_'.strtoupper($inv).'_'.$item[0]['createdate'].$time[0].$time[1].$time[2].'_'.$invmachine.'.xml')){
	if($_POST['fileexists']=='notexists'){
		if(isset($item)&&sizeof($item)>0&&isset($item[0]['invnumber'])){
			if(!isset($content['basic']['sendinvlocation'])||$content['basic']['sendinvlocation']=='1'){//亮點
				include_once '../../../demoinv/newinv.php';
				$filename='C0401_'.strtoupper($inv).'_'.$item[0]['createdate'].$time[0].$time[1].$time[2].'_'.$invmachine.'.xml';
				$xml=new SimpleXMLElement($xmlstr);
				$xml->addChild('Main');
				$xmlMain=$xml->Main;
				$xmlMain->addChild('InvoiceNumber',strtoupper($inv));
				//$date=$Y.$month.$day;
				$xmlMain->addChild('InvoiceDate',$item[0]['createdate']);
				//$time=$hour.':'.$min.':'.$sec;
				$xmlMain->addChild('InvoiceTime',$item[0]['createtime']);
				$xmlMain->addChild('Seller');
				$xmlMainSeller=$xmlMain->Seller;
				$xmlMainSeller->addChild('Identifier',$item[0]['sellerid']);
				$xmlMainSeller->addChild('Name',$item[0]['sellername']);
				$xmlMain->addChild('Buyer');
				$xmlMainBuyer=$xmlMain->Buyer;
				if(strlen($item[0]['buyerid'])!='00000000'){
					$buyerid=$item[0]['buyerid'];
					$xmlMainBuyer->addChild('Identifier',$item[0]['buyerid']);
					$buyername='0000';
					$xmlMainBuyer->addChild('Name','0000');
				}
				else{
					$buyerid='00000000';
					$xmlMainBuyer->addChild('Identifier','00000000');
					$buyername='0000000000';
					$xmlMainBuyer->addChild('Name','0000000000');
				}
				$xmlMain->addChild('RelateNumber',$item[0]['relatenumber']);
				$xmlMain->addChild('InvoiceType','07');
				$xmlMain->addChild('DonateMark','0');
				$xmlMain->addChild('PrintMark','Y');
				srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
				$rnumber=rand(0,9999);
				while(strlen($rnumber)<4){
					$rnumber='0'.$rnumber;
				}
				$xmlMain->addChild('RandomNumber',$item[0]['randomnumber']);
				$xml->addChild('Details');
				$value='';
				$xmlDetails=$xml->Details;

				if($init['init']['invlist']=='2'){//總項
					$xmlDetails->addChild('ProductItem');
					$value=$value.'"'.$content['basic']['itemname'].'",';
					$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
					$value=$value.'1,';
					$xmlDetails->ProductItem[0]->addChild('Quantity','1');
					$value=$value.$item[0]['totalamount'].',';
					$xmlDetails->ProductItem[0]->addChild('UnitPrice',$item[0]['totalamount']);
					$value=$value.$item[0]['totalamount'].',';
					$xmlDetails->ProductItem[0]->addChild('Amount',$item[0]['totalamount']);
					$value=$value.'1';
					$xmlDetails->ProductItem[0]->addChild('SequenceNumber','001');
					$xmlDetails->ProductItem[0]->addChild('Remark','Tx');
				}
				else{//明細
					$taste=parse_ini_file('../../../database/'.$content['basic']['company'].'-taste.ini',true);
					$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
					$sql='SELECT * FROM CST012 JOIN CST011 ON CST011.BIZDATE="'.$_POST['bizdate'].'" AND CST011.INVOICENUMBER="'.$inv.'" AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.BIZDATE=CST012.BIZDATE';
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

				/*$xmlDetails->addChild('ProductItem');
				$value=$value.'"'.$content['basic']['itemname'].'",';
				$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
				$value=$value.'1,';
				$xmlDetails->ProductItem[0]->addChild('Quantity','1');
				$value=$value.$item[0]['totalamount'].',';
				$xmlDetails->ProductItem[0]->addChild('UnitPrice',$item[0]['totalamount']);
				$value=$value.$item[0]['totalamount'].',';
				$xmlDetails->ProductItem[0]->addChild('Amount',$item[0]['totalamount']);
				$value=$value.'1';
				$xmlDetails->ProductItem[0]->addChild('SequenceNumber','001');
				$xmlDetails->ProductItem[0]->addChild('Remark','Tx');*/
				$xml->addChild('Amount');
				$xmlAmount=$xml->Amount;
				if(strlen($item[0]['buyerid'])!=10){
					$xmlAmount->addChild('SalesAmount',round(intval($item[0]['totalamount'])/1.05));
					$xmlAmount->addChild('FreeTaxSalesAmount','0');
					$xmlAmount->addChild('ZeroTaxSalesAmount','0');
					$xmlAmount->addChild('TaxType','1');
					$xmlAmount->addChild('TaxRate','0.05');
					$xmlAmount->addChild('TaxAmount',intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05)));
					$xmlAmount->addChild('TotalAmount',$item[0]['totalamount']);
				}
				else{
					$xmlAmount->addChild('SalesAmount',$item[0]['totalamount']);
					$xmlAmount->addChild('FreeTaxSalesAmount','0');
					$xmlAmount->addChild('ZeroTaxSalesAmount','0');
					$xmlAmount->addChild('TaxType','1');
					$xmlAmount->addChild('TaxRate','0.05');
					$xmlAmount->addChild('TaxAmount','0');
					$xmlAmount->addChild('TotalAmount',$item[0]['totalamount']);

				}
				$xml->saveXML('../../../print/noread/'.$filename);
				echo 'success';
			}
			else if($content['basic']['sendinvlocation']=='2'){//神通
			}
			else if($content['basic']['sendinvlocation']=='3'){//中鼎
				if(!isset($content['zdninv']['useEIP'])||$content['zdninv']['useEIP']=='1'){
					//$filename='data.txt';
					$filename='C0401_'.strtoupper($inv).'_'.$item[0]['createdate'].$time[0].$time[1].$time[2].'_'.$invmachine.'.xml';
					for($i=1;$i<=19;$i++){
						$invarray[$i]='';
					}
					//$invfile=fopen('../../../print/noread/'.$filename,'w');
					//$xml=new SimpleXMLElement($xmlstr);
					//fwrite($invfile,''.PHP_EOL);
					//$xml->addChild('Main');
					//$xmlMain=$xml->Main;
					$invarray[9]=strtoupper($inv);
					//$xmlMain->addChild('InvoiceNumber',$table[0]['banno']);
					$date=$item[0]['createdate'];
					//$xmlMain->addChild('InvoiceDate',$date);
					$time=$item[0]['createtime'];
					//$xmlMain->addChild('InvoiceTime',$time);
					$invarray[11]=$date.'-'.preg_replace('/:/','',$time);
					//$xmlMain->addChild('Seller');
					//$xmlMainSeller=$xmlMain->Seller;
					//$xmlMainSeller->addChild('Identifier',$content['basic']['Identifier']);
					$invarray[8]=$item[0]['sellerid'];
					//$xmlMainSeller->addChild('Name',$content['basic']['Name']);
					$invarray[2]=$item[0]['sellername'];
					//$xmlMain->addChild('Buyer');
					//$xmlMainBuyer=$xmlMain->Buyer;
					if(intval($item[0]['buyerid'])>0&&$item[0]['buyerid']!='0000000000'){
						$buyerid=$item[0]['buyerid'];
						//$xmlMainBuyer->addChild('Identifier',$_POST['tempban']);
						$buyername='0000';
						//$xmlMainBuyer->addChild('Name','0000');
						$invarray[19]=$item[0]['buyerid'];
						$invarray[17]='';
					}
					else{
						$buyerid='0000000000';
						//$xmlMainBuyer->addChild('Identifier','0000000000');
						$buyername='0000000000';
						//$xmlMainBuyer->addChild('Name','0000000000');
						$invarray[19]='';
						$invarray[17]='';
					}
					//從 神通 聽說，發票上不能列印信用卡後四碼
					/*if(isset($list[0]['CREDITCARD'])&&$list[0]['CREDITCARD']!=''&&$list[0]['CREDITCARD']!=null){
						$xmlMain->addChild('MainRemark',"信用卡尾碼：".$list[0]['CREDITCARD']);
					}
					else{
					}*/
					//$xmlMain->addChild('RelateNumber',$list[0]['CONSECNUMBER']);
					$invarray[18]=$item[0]['relatenumber'];
					//$xmlMain->addChild('InvoiceType','07');
					if(strlen($item[0]['carriertype'])>0){
						if(substr($item[0]['carriertype'],0,1)=='/'){//手機條碼
							//$xmlMain->addChild('DonateMark','0');
							//$xmlMain->addChild('CarrierType','3J0002');
							//$xmlMain->addChild('CarrierId1',strtoupper($_POST['tempcontainer']));
							//$xmlMain->addChild('CarrierId2',strtoupper($_POST['tempcontainer']));
							/*if(strlen($_POST['tempban'])==8){
								//$xmlMain->addChild('PrintMark','Y');
							}
							else{
								//$xmlMain->addChild('PrintMark','N');
							}*/
							$invarray[1]='';
							$invarray[12]=strtoupper($item[0]['carrierid1']);
							$invarray[13]='';
						}
						else if(is_numeric(substr($item[0]['carriertype'],0,1))){//愛心碼
							//$xmlMain->addChild('DonateMark','1');
							//$xmlMain->addChild('PrintMark','N');
							//$xmlMain->addChild('NPOBAN',$_POST['tempcontainer']);
							$invarray[1]='';
							$invarray[12]='';
							$invarray[13]=$item[0]['npoban'];
						}
						else if(strlen($item[0]['carriertype'])==16){//自然人憑證
							//$xmlMain->addChild('DonateMark','0');
							//$xmlMain->addChild('CarrierType','CQ0001');
							//$xmlMain->addChild('CarrierId1',strtoupper($_POST['tempcontainer']));
							//$xmlMain->addChild('CarrierId2',strtoupper($_POST['tempcontainer']));
							/*if(strlen($_POST['tempban'])==8){
								//$xmlMain->addChild('PrintMark','Y');
							}
							else{
								//$xmlMain->addChild('PrintMark','N');
							}*/
							$invarray[1]='';
							$invarray[12]=strtoupper($item[0]['carrierid1']);
							$invarray[13]='';
						}
						else{
							//$xmlMain->addChild('DonateMark','0');
							//$xmlMain->addChild('PrintMark','Y');
							$invarray[1]='';
							$invarray[12]='';
							$invarray[13]='';
						}
					}
					else{
						//$xmlMain->addChild('DonateMark','0');
						//$xmlMain->addChild('PrintMark','Y');
						$invarray[1]='';
						$invarray[12]='';
						$invarray[13]='';
					}
					srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
					$rnumber=rand(0,9999);
					while(strlen($rnumber)<4){
						$rnumber='0'.$rnumber;
					}
					//$xmlMain->addChild('RandomNumber',$rnumber);
					$invarray[10]=$item[0]['randomnumber'];
					//$xml->addChild('Details');
					//$value='';
					//$xmlDetails=$xml->Details;
					if($init['init']['invlist']=='2'){//總項
						//$xmlDetails->addChild('ProductItem');
						//$value=$value.'"'.$content['basic']['itemname'].'",';
						//$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
						//$value=$value.'1,';
						//$xmlDetails->ProductItem[0]->addChild('Quantity','1');
						//$value=$value.$list[0]['invmoney'].',';
						//$xmlDetails->ProductItem[0]->addChild('UnitPrice',$list[0]['invmoney']);
						//$value=$value.$list[0]['invmoney'].',';
						//$xmlDetails->ProductItem[0]->addChild('Amount',$list[0]['invmoney']);
						//$value=$value.'1';
						//$xmlDetails->ProductItem[0]->addChild('SequenceNumber','001');
						//$xmlDetails->ProductItem[0]->addChild('Remark','Tx');
						$invarray[20]=$content['basic']['itemname'];
						$invarray[21]='1';
						$invarray[22]=$item[0]['totalamount'];
						$invarray[23]=$item[0]['totalamount'];
						$invarray[24]='001';
					}
					else{//明細
						$invarray[1]='Y';//2019/12/24預設列印發票明細
						$taste=parse_ini_file('../../../database/'.$content['basic']['company'].'-taste.ini',true);
						$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
						$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$item[0]['relatenumber'].'"';
						$itemtemp1list=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$item[0]['relatenumber'].'"';
						$itemtemp2list=sqlquery($conn,$sql,'sqlite');

						if(isset($itemtemp1list[0]['CONSECNUMBER'])){
							$itemlist=$itemtemp1list;
						}
						else if(isset($itemtemp2list[0]['CONSECNUMBER'])){
							$itemlist=$itemtemp2list;
						}
						else{
							$itemlist=array();
						}
						sqlclose($conn,'sqlite');
						for($i=0,$j=0;$i<sizeof($itemlist);$i=$i+2){
							if(isset($itemlist[$i+1]['AMT'])){
								if((intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']))>0){
									//$xmlDetails->addChild('ProductItem');
									//$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
									//$xmlDetails->ProductItem[$j]->addChild('Description',$itemlist[$i]['ITEMNAME']);
									//$value=$value.$itemlist[$i]['QTY'].',';
									//$xmlDetails->ProductItem[$j]->addChild('Quantity',$itemlist[$i]['QTY']);
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
									//$value=$value.$unitprice.',';
									//$xmlDetails->ProductItem[$j]->addChild('UnitPrice',$unitprice);
									//$value=$value.(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT'])).',';
									//$xmlDetails->ProductItem[$j]->addChild('Amount',(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT'])));
									//$value=$value.'1';
									//$xmlDetails->ProductItem[$j]->addChild('SequenceNumber',str_pad(($j+1),3,'0',STR_PAD_LEFT));
									//$xmlDetails->ProductItem[$j]->addChild('Remark','Tx');

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
								if(intval($itemlist[$i]['AMT'])>0){
									//$xmlDetails->addChild('ProductItem');
									//$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
									//$xmlDetails->ProductItem[$j]->addChild('Description',$itemlist[$i]['ITEMNAME']);
									//$value=$value.$itemlist[$i]['QTY'].',';
									//$xmlDetails->ProductItem[$j]->addChild('Quantity',$itemlist[$i]['QTY']);
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
									//$value=$value.$unitprice.',';
									//$xmlDetails->ProductItem[$j]->addChild('UnitPrice',$unitprice);
									//$value=$value.intval($itemlist[$i]['AMT']).',';
									//$xmlDetails->ProductItem[$j]->addChild('Amount',intval($itemlist[$i]['AMT']));
									//$value=$value.'1';
									//$xmlDetails->ProductItem[$j]->addChild('SequenceNumber',str_pad(($j+1),3,'0',STR_PAD_LEFT));
									//$xmlDetails->ProductItem[$j]->addChild('Remark','Tx');

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
					//$xml->addChild('Amount');
					//$xmlAmount=$xml->Amount;
					if(strlen($item[0]['buyerid'])!=10){
						//$xmlAmount->addChild('SalesAmount',round(intval($invmoney)/1.05));
						//$xmlAmount->addChild('FreeTaxSalesAmount','0');
						//$xmlAmount->addChild('ZeroTaxSalesAmount','0');
						//$xmlAmount->addChild('TaxType','1');
						//$xmlAmount->addChild('TaxRate','0.05');
						//$xmlAmount->addChild('TaxAmount',intval($invmoney)-intval(round($invmoney/1.05)));
						//$xmlAmount->addChild('TotalAmount',$invmoney);

						$invarray[14]=round(intval($item[0]['totalamount'])/1.05);
						$invarray[15]='0';
						$invarray[16]=intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05));
						$invarray[7]=$item[0]['totalamount'];
					}
					else{
						//$xmlAmount->addChild('SalesAmount',$invmoney);
						//$xmlAmount->addChild('FreeTaxSalesAmount','0');
						//$xmlAmount->addChild('ZeroTaxSalesAmount','0');
						//$xmlAmount->addChild('TaxType','1');
						//$xmlAmount->addChild('TaxRate','0.05');
						//$xmlAmount->addChild('TaxAmount','0');
						//$xmlAmount->addChild('TotalAmount',$invmoney);
						
						$invarray[14]=$item[0]['totalamount'];
						$invarray[15]='0';
						$invarray[16]='0';
						$invarray[7]=$item[0]['totalamount'];
					}
					//print_r($invarray);
					/*if(file_exists('../../../print/zdninv')){
					}
					else{
						mkdir('../../../print/zdninv');
					}*/
					$inv=fopen('../../../print/noread/'.$filename,'w');
					foreach($invarray as $i){
						fwrite($inv,$i.PHP_EOL);
					}
					fclose($inv);
					//$xml->saveXML('../../../print/noread/'.$filename);
					echo 'success';
				}
				else{
					if(strlen($item[0]['carriertype'])>0){
						if(substr($item[0]['carriertype'],0,1)=='/'){//手機條碼
							$xml['Main']['DonateMark']='0';
							$xml['Main']['CarrierType']='3J0002';
							$xml['Main']['CarrierId1']=strtoupper($item[0]['carrierid1']);
							$xml['Main']['CarrierId2']=strtoupper($item[0]['carrierid1']);
							if(strlen($item[0]['buyerid'])==8){
								$xml['Main']['PrintMark']='Y';
							}
							else{
								$xml['Main']['PrintMark']='N';
							}
						}
						else if(is_numeric(substr($item[0]['carriertype'],0,1))){//愛心碼
							$xml['Main']['DonateMark']='1';
							$xml['Main']['PrintMark']='N';
							$xml['Main']['NPOBAN']=$item[0]['npoban'];
						}
						else if(strlen($item[0]['carriertype'])==16){//自然人憑證
							$xml['Main']['DonateMark']='0';
							$xml['Main']['CarrierType']='CQ0001';
							$xml['Main']['CarrierId1']=strtoupper($item[0]['carrierid1']);
							$xml['Main']['CarrierId2']=strtoupper($item[0]['carrierid1']);
							if(strlen($item[0]['buyerid'])==8){
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
						if(intval(substr($item[0]['createdate'],4,2))%2==0){
							$invm=substr($item[0]['createdate'],4,2);
						}
						else{
							$invm=intval(substr($item[0]['createdate'],4,2))+1;
						}
						if(strlen($invm)<2){
							$invm='0'.$invm;
						}
						include_once '../../../tool/AES/lib.php';
						include_once '../../../tool/phpqrcode/qrlib.php';
						include_once '../../../tool/phpbarcode/src/BarcodeGenerator.php';
						include_once '../../../tool/phpbarcode/src/BarcodeGeneratorPNG.php';
						include_once '../../../tool/AES/lib.php';
						include_once '../../../tool/phpqrcode/qrlib.php';
						
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
						if(substr(strtoupper($inv),0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
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
						$pdf->MultiCell('', '', (intval(substr($item[0]['createdate'],0,4))-1911)."年".str_pad((intval($invm)-1),2,'0',STR_PAD_LEFT)."－".$invm."月", 0, 'C', 0, 0, 0, 15, 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell('', '', substr(strtoupper($inv),0,2).'－'.substr(strtoupper($inv),2), 0, 'C', 0, 0, 0, 21.5, 1, 0, 0, 0, 10, 'T', 0);
					}
					else{
					}
					$filename='C0401_'.$content['basic']['Identifier'].'_'.strtoupper($inv).'_'.substr($item[0]['createdate'],4).$time[0].$time[1].$time[2].'.json';
					$xml['Main']['InvoiceNumber']=strtoupper($inv);
					/*srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
					$rnumber=rand(0,9999);
					while(strlen($rnumber)<4){
						$rnumber='0'.$rnumber;
					}*/
					$xml['Main']['RandomNumber']=$item[0]['randomnumber'];
					//$date=$Y.$month.$day;
					$xml['Main']['InvoiceDate']=$item[0]['createdate'];
					//$time=$hour.':'.$min.':'.$sec;
					$xml['Main']['InvoiceTime']=$item[0]['createtime'];
					if($xml['Main']['PrintMark']=='Y'){
						$pdf->SetFont('DroidSansFallback', '', 9);
						$pdf->MultiCell('', '', "隨機碼:".$item[0]['randomnumber'], 0, 'L', 0, 0, 3, 32.5, 1, 0, 0, 0, 10, 'T', 0);//2021/5/27 因為cell有指定(x,y)座標，所以不用依照順序填入
						$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
						file_put_contents('../../../print/barcode/'.(intval(substr($item[0]['createdate'],0,4))-1911).$invm.strtoupper($inv).$item[0]['randomnumber'].'barcode.png', $generator->getBarcodeNoText(((intval(substr($item[0]['createdate'],0,4))-1911).$invm.strtoupper($inv).$item[0]['randomnumber']), $generator::TYPE_CODE_39));
						$pdf->Image('../../../print/barcode/'.(intval(substr($item[0]['createdate'],0,4))-1911).$invm.strtoupper($inv).$item[0]['randomnumber'].'barcode.png',3,35,45,20,'png','','M',1,300);
						$pdf->SetFont('DroidSansFallback', '', 9);
						$pdf->MultiCell('', '', substr($item[0]['createdate'],0,4).'-'.substr($item[0]['createdate'],4,2).'-'.substr($item[0]['createdate'],6,2)." ".$item[0]['createtime'], 0, 'L', 0, 0, 3, 29, 1, 0, 0, 0, 10, 'T', 0);
					}
					else{
					}
					$xml['Main']['Seller']['Identifier']=$item[0]['sellerid'];
					$xml['Main']['Seller']['Name']=$item[0]['sellername'];
					$xml['Main']['Seller']['Address']=$content['basic']['address'];
					if(strlen($item[0]['buyerid'])!=10){
						$buyerid=$item[0]['buyerid'];
						$xml['Main']['Buyer']['Identifier']=$item[0]['buyerid'];
						if($xml['Main']['PrintMark']=='Y'){
							$pdf->MultiCell('', '', "格式:25", 0, 'L', 0, 0, 35, 29, 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "買方".$item[0]['buyerid'], 0, 'L', 0, 0, 26, 36, 1, 0, 0, 0, 10, 'T', 0);//2021/5/27 因為cell有指定(x,y)座標，所以不用依照順序填入
						}
						else{
						}
						$buyername='0000';
						$xml['Main']['Buyer']['Name']='0000';
						$buyerhexmoney=str_pad((intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05))),8,'0',STR_PAD_LEFT);
						$xml['Amount']['SalesAmount']=round(intval($item[0]['totalamount'])/1.05);
						$xml['Amount']['FreeTaxSalesAmount']='0';
						$xml['Amount']['ZeroTaxSalesAmount']='0';
						$xml['Amount']['TaxType']='1';
						$xml['Amount']['TaxRate']='0.05';
						$xml['Amount']['TaxAmount']=intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05));
						$xml['Amount']['TotalAmount']=$item[0]['totalamount'];
					}
					else{
						$buyerid='00000000';
						$xml['Main']['Buyer']['Identifier']='00000000';
						$buyername='0000000000';
						$xml['Main']['Buyer']['Name']='0000000000';
						$buyerhexmoney='00000000';
						$xml['Amount']['SalesAmount']=$item[0]['totalamount'];
						$xml['Amount']['FreeTaxSalesAmount']='0';
						$xml['Amount']['ZeroTaxSalesAmount']='0';
						$xml['Amount']['TaxType']='1';
						$xml['Amount']['TaxRate']='0.05';
						$xml['Amount']['TaxAmount']='0';
						$xml['Amount']['TotalAmount']=$item[0]['totalamount'];
					}
					/*if(strlen($item[0]['buyerid'])!=10){
						$xml['Amount']['SalesAmount']=round(intval($item[0]['totalamount'])/1.05);
						$xml['Amount']['FreeTaxSalesAmount']='0';
						$xml['Amount']['ZeroTaxSalesAmount']='0';
						$xml['Amount']['TaxType']='1';
						$xml['Amount']['TaxRate']='0.05';
						$xml['Amount']['TaxAmount']=intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05));
						$xml['Amount']['TotalAmount']=$item[0]['totalamount'];
					}
					else{
						$xml['Amount']['SalesAmount']=$item[0]['totalamount'];
						$xml['Amount']['FreeTaxSalesAmount']='0';
						$xml['Amount']['ZeroTaxSalesAmount']='0';
						$xml['Amount']['TaxType']='1';
						$xml['Amount']['TaxRate']='0.05';
						$xml['Amount']['TaxAmount']='0';
						$xml['Amount']['TotalAmount']=$item[0]['totalamount'];

					}*/
					$xml['Main']['RelateNumber']=$item[0]['relatenumber'];
					$xml['Main']['InvoiceType']='07';
					if($xml['Main']['PrintMark']=='Y'){
						$pdf->MultiCell('', '', "總計:".$item[0]['totalamount'], 0, 'L', 0, 0, 25, 32.5, 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell('', '', "賣方".$content['basic']['Identifier'], 0, 'L', 0, 0, 3, 36, 1, 0, 0, 0, 10, 'T', 0);
						$qrcodeClass = new encryQrcode();
						$aesKey = "1905B9C0E27FB708712E42CED49178AB";// input your aeskey
						$invoiceNumAndRandomCode = strtoupper($inv).$item[0]['randomnumber'];// input your invoiceNumber And RandomCode
						$encry=$qrcodeClass->aes128_cbc_encrypt($aesKey, $invoiceNumAndRandomCode);
						QRcode::png(strtoupper($inv).$item[0]['createdate'].$item[0]['randomnumber'].$buyerhexmoney.str_pad(dechex($item[0]['totalamount']),8,'0',STR_PAD_LEFT).$buyerid.$content['basic']['Identifier'].$encry.":**********:1:1:1:".$content['basic']['itemname'].":1:".$item[0]['totalamount'], "../../../print/qrcode/leftqrcode.png", "L", "4", 2);
						$pdf->Image("../../../print/qrcode/leftqrcode.png",5,51,20,20,'png','','M',1,300);
						QRcode::png("**                                                                                                                                   ", "../../../print/qrcode/rightqrcode.png", "L", "4", 2);
						$pdf->Image("../../../print/qrcode/rightqrcode.png",28,51,20,20,'png','','M',1,300);
						$pdf->SetFont('DroidSansFallback', '', 8.5);
						$pdf->MultiCell('', '', "機號:".$invmachine, 0, 'L', 0, 0, 3, 71, 1, 0, 0, 0, 10, 'T', 0);

						$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
						$sql='SELECT CONSECNUMBER FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND INVOICENUMBER="'.$inv.'"';
						$consecnumber=sqlquery($conn,$sql,'sqlite');
						sqlclose($conn,'sqlite');
						if(isset($consecnumber[0]['CONSECNUMBER'])){
							$pdf->MultiCell('', '', "單號:".$consecnumber[0]['CONSECNUMBER'], 0, 'R', 0, 1, 3, 71, 1, 0, 0, 0, 10, 'T', 0);
						}
						else{
							$pdf->MultiCell('', '', "單號:", 0, 'R', 0, 1, 3, 71, 1, 0, 0, 0, 10, 'T', 0);
						}

						//若發票檔頭為圖檔，則需要列印門市名
						if(substr(strtoupper($inv),0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
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
					if($init['init']['invlist']=='2'){//總項
						$value=$value.'"'.$content['basic']['itemname'].'",';
						$xml['Details']['ProductItem'][0]['Description']=$content['basic']['itemname'];
						$value=$value.'1,';
						$xml['Details']['ProductItem'][0]['Quantity']='1';
						$value=$value.$item[0]['totalamount'].',';
						$xml['Details']['ProductItem'][0]['UnitPrice']=$item[0]['totalamount'];
						$value=$value.$item[0]['totalamount'].',';
						$xml['Details']['ProductItem'][0]['Amount']=$item[0]['totalamount'];
						$value=$value.'1';
						$xml['Details']['ProductItem'][0]['SequenceNumber']='001';
						$xml['Details']['ProductItem'][0]['Remark']='Tx';
						if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
							if(!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8){//有買方統編時，必列印消費明細，且消費明細與證明聯不切分
								$pagey=80;
							}
							else{//if(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1')//列印消費明細，明細與證明聯切分兩張
								$pdf->lastPage();
								$pdf->AddPage();
								$pagey=0;
							}
							$pdf->SetFont('DroidSansFallback', '', 12);
							if(substr(strtoupper($inv),0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
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
							$pdf->MultiCell(15, '', $item[0]['totalamount']."x", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell(10, '', "1", 0, 'R', 0, 0, 18, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell(20, '', $item[0]['totalamount']."TX", 0, 'R', 0, 2, 28, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->SetFont('DroidSansFallback', '', 10);
							$pdf->MultiCell('', '', "---------------------------------------", 0, 'C', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->SetFont('DroidSansFallback', '', 8);
							$pdf->MultiCell('', '', "總計", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄".$item[0]['totalamount'], 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							if(!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8){
								$pdf->MultiCell('', '', "應稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄".intval(round($item[0]['totalamount']/1.05)), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "免稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄0", 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "稅額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄".(intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05))), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
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
						$sql='SELECT * FROM CST012 JOIN CST011 ON CST011.BIZDATE="'.$_POST['bizdate'].'" AND CST011.INVOICENUMBER="'.$inv.'" AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.BIZDATE=CST012.BIZDATE';
						$itemlist=sqlquery($conn,$sql,'sqlite');
						sqlclose($conn,'sqlite');
						if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
							if(!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8){//有買方統編時，必列印消費明細，且消費明細與證明聯不切分
								$pagey=80;
							}
							else{//if(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1')//列印消費明細，明細與證明聯切分兩張
								$pdf->lastPage();
								$pdf->AddPage();
								$pagey=0;
							}
							$pdf->SetFont('DroidSansFallback', '', 12);
							if(substr(strtoupper($inv),0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
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
												if($t!=1&&$k!=0){
													$invname.=',';
												}
												else{
												}
												$invname=$taste[intval(substr($temptaste[$k],0,5))]['name1'];
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
									if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
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
												if($t!=1&&$k!=0){
													$invname.=',';
												}
												else{
												}
												$invname=$taste[intval(substr($temptaste[$k],0,5))]['name1'];
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
									if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
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
						if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
							$pdf->SetFont('DroidSansFallback', '', 10);
							$pdf->MultiCell('', '', "---------------------------------------", 0, 'C', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->SetFont('DroidSansFallback', '', 8);
							$pdf->MultiCell('', '', "總計", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄".$item[0]['totalamount'], 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							if(!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8){
								$pdf->MultiCell('', '', "應稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄".intval(round($item[0]['totalamount']/1.05)), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "免稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄0", 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "稅額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄".(intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05))), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
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
					if(substr(strtoupper($inv),0,2)!='OO'){//2021/6/4 發票號碼開頭OO為測試發票，不產生json上傳檔案
						$f=fopen('../../../print/invuploadlog/waitupload/'.$filename,'w');
						fwrite($f,json_encode($xml));
						fclose($f);
					}
					else{
					}
					if($xml['Main']['PrintMark']=='Y'){
						$pdf->Output(dirname(__FILE__).'/../../../print/noread/'.$content['basic']['Identifier'].'_C0401'.$invmachine.'_'.strtoupper($inv).'_'.substr($item[0]['createdate'],4).$time[0].$time[1].$time[2].'.pdf', 'F');
					}
					else{
					}
					echo 'success';
				}
			}
		}
		else{
			echo 'dataempty';
		}
	}
	else{//$_POST['fileexists']=='exists'
		if(isset($item)&&sizeof($item)>0&&isset($item[0]['invnumber'])){
			if(!isset($content['basic']['sendinvlocation'])||$content['basic']['sendinvlocation']=='1'){//亮點
				include_once '../../../demoinv/reinv.php';
				$filename='RC0401_'.strtoupper($inv).'_'.$item[0]['createdate'].$time[0].$time[1].$time[2].'_'.$invmachine.'.xml';
				$xml=new SimpleXMLElement($xmlstr);
				$xml->addChild('Main');
				$xmlMain=$xml->Main;
				$xmlMain->addChild('InvoiceNumber',strtoupper($inv));
				//$date=$Y.$month.$day;
				$xmlMain->addChild('InvoiceDate',$item[0]['createdate']);
				//$time=$hour.':'.$min.':'.$sec;
				$xmlMain->addChild('InvoiceTime',$item[0]['createtime']);
				$xmlMain->addChild('Seller');
				$xmlMainSeller=$xmlMain->Seller;
				$xmlMainSeller->addChild('Identifier',$item[0]['sellerid']);
				$xmlMainSeller->addChild('Name',$item[0]['sellername']);
				$xmlMain->addChild('Buyer');
				$xmlMainBuyer=$xmlMain->Buyer;
				if(strlen($item[0]['buyerid'])!='00000000'){
					$buyerid=$item[0]['buyerid'];
					$xmlMainBuyer->addChild('Identifier',$item[0]['buyerid']);
					$buyername='0000';
					$xmlMainBuyer->addChild('Name','0000');
				}
				else{
					$buyerid='00000000';
					$xmlMainBuyer->addChild('Identifier','00000000');
					$buyername='0000000000';
					$xmlMainBuyer->addChild('Name','0000000000');
				}
				$xmlMain->addChild('RelateNumber',$item[0]['relatenumber']);
				$xmlMain->addChild('InvoiceType','07');
				$xmlMain->addChild('DonateMark','0');
				$xmlMain->addChild('PrintMark','Y');
				srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
				$rnumber=rand(0,9999);
				while(strlen($rnumber)<4){
					$rnumber='0'.$rnumber;
				}
				$xmlMain->addChild('RandomNumber',$item[0]['randomnumber']);
				$xml->addChild('Details');
				$value='';
				$xmlDetails=$xml->Details;

				if($init['init']['invlist']=='2'){//總項
					$xmlDetails->addChild('ProductItem');
					$value=$value.'"'.$content['basic']['itemname'].'",';
					$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
					$value=$value.'1,';
					$xmlDetails->ProductItem[0]->addChild('Quantity','1');
					$value=$value.$item[0]['totalamount'].',';
					$xmlDetails->ProductItem[0]->addChild('UnitPrice',$item[0]['totalamount']);
					$value=$value.$item[0]['totalamount'].',';
					$xmlDetails->ProductItem[0]->addChild('Amount',$item[0]['totalamount']);
					$value=$value.'1';
					$xmlDetails->ProductItem[0]->addChild('SequenceNumber','001');
					$xmlDetails->ProductItem[0]->addChild('Remark','Tx');
				}
				else{//明細
					$taste=parse_ini_file('../../../database/'.$content['basic']['company'].'-taste.ini',true);
					$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
					$sql='SELECT * FROM CST012 JOIN CST011 ON CST011.BIZDATE="'.$_POST['bizdate'].'" AND CST011.INVOICENUMBER="'.$inv.'" AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.BIZDATE=CST012.BIZDATE';
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

				/*$xmlDetails->addChild('ProductItem');
				$value=$value.'"'.$content['basic']['itemname'].'",';
				$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
				$value=$value.'1,';
				$xmlDetails->ProductItem[0]->addChild('Quantity','1');
				$value=$value.$item[0]['totalamount'].',';
				$xmlDetails->ProductItem[0]->addChild('UnitPrice',$item[0]['totalamount']);
				$value=$value.$item[0]['totalamount'].',';
				$xmlDetails->ProductItem[0]->addChild('Amount',$item[0]['totalamount']);
				$value=$value.'1';
				$xmlDetails->ProductItem[0]->addChild('SequenceNumber','001');
				$xmlDetails->ProductItem[0]->addChild('Remark','Tx');*/
				$xml->addChild('Amount');
				$xmlAmount=$xml->Amount;
				if(strlen($item[0]['buyerid'])!=10){
					$xmlAmount->addChild('SalesAmount',round(intval($item[0]['totalamount'])/1.05));
					$xmlAmount->addChild('FreeTaxSalesAmount','0');
					$xmlAmount->addChild('ZeroTaxSalesAmount','0');
					$xmlAmount->addChild('TaxType','1');
					$xmlAmount->addChild('TaxRate','0.05');
					$xmlAmount->addChild('TaxAmount',intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05)));
					$xmlAmount->addChild('TotalAmount',$item[0]['totalamount']);
				}
				else{
					$xmlAmount->addChild('SalesAmount',$item[0]['totalamount']);
					$xmlAmount->addChild('FreeTaxSalesAmount','0');
					$xmlAmount->addChild('ZeroTaxSalesAmount','0');
					$xmlAmount->addChild('TaxType','1');
					$xmlAmount->addChild('TaxRate','0.05');
					$xmlAmount->addChild('TaxAmount','0');
					$xmlAmount->addChild('TotalAmount',$item[0]['totalamount']);

				}
				$xml->saveXML('../../../print/noread/'.$filename);
				echo 'success';
			}
			else if($content['basic']['sendinvlocation']=='2'){//神通
			}
			else if($content['basic']['sendinvlocation']=='3'){//中鼎
				if(!isset($content['zdninv']['useEIP'])||$content['zdninv']['useEIP']=='1'){
					//$filename='data.txt';
					$filename='RC0401_'.strtoupper($inv).'_'.$item[0]['createdate'].$time[0].$time[1].$time[2].'_'.$invmachine.'.xml';
					for($i=1;$i<=19;$i++){
						$invarray[$i]='';
					}
					//$invfile=fopen('../../../print/noread/'.$filename,'w');
					//$xml=new SimpleXMLElement($xmlstr);
					//fwrite($invfile,''.PHP_EOL);
					//$xml->addChild('Main');
					//$xmlMain=$xml->Main;
					$invarray[9]=strtoupper($inv);
					//$xmlMain->addChild('InvoiceNumber',$table[0]['banno']);
					$date=$item[0]['createdate'];
					//$xmlMain->addChild('InvoiceDate',$date);
					$time=$item[0]['createtime'];
					//$xmlMain->addChild('InvoiceTime',$time);
					$invarray[11]=$date.'-'.preg_replace('/:/','',$time);
					//$xmlMain->addChild('Seller');
					//$xmlMainSeller=$xmlMain->Seller;
					//$xmlMainSeller->addChild('Identifier',$content['basic']['Identifier']);
					$invarray[8]=$item[0]['sellerid'];
					//$xmlMainSeller->addChild('Name',$content['basic']['Name']);
					$invarray[2]=$item[0]['sellername'];
					//$xmlMain->addChild('Buyer');
					//$xmlMainBuyer=$xmlMain->Buyer;
					if(intval($item[0]['buyerid'])>0&&strlen($item[0]['buyerid'])!='0000000000'){
						$buyerid=$item[0]['buyerid'];
						//$xmlMainBuyer->addChild('Identifier',$_POST['tempban']);
						$buyername='0000';
						//$xmlMainBuyer->addChild('Name','0000');
						$invarray[19]=$item[0]['buyerid'];
						$invarray[17]='';
					}
					else{
						$buyerid='0000000000';
						//$xmlMainBuyer->addChild('Identifier','0000000000');
						$buyername='0000000000';
						//$xmlMainBuyer->addChild('Name','0000000000');
						$invarray[19]='';
						$invarray[17]='';
					}
					//從 神通 聽說，發票上不能列印信用卡後四碼
					/*if(isset($list[0]['CREDITCARD'])&&$list[0]['CREDITCARD']!=''&&$list[0]['CREDITCARD']!=null){
						$xmlMain->addChild('MainRemark',"信用卡尾碼：".$list[0]['CREDITCARD']);
					}
					else{
					}*/
					//$xmlMain->addChild('RelateNumber',$list[0]['CONSECNUMBER']);
					$invarray[18]=$item[0]['relatenumber'];
					//$xmlMain->addChild('InvoiceType','07');
					if(strlen($item[0]['carriertype'])>0){
						if(substr($item[0]['carriertype'],0,1)=='/'){//手機條碼
							//$xmlMain->addChild('DonateMark','0');
							//$xmlMain->addChild('CarrierType','3J0002');
							//$xmlMain->addChild('CarrierId1',strtoupper($_POST['tempcontainer']));
							//$xmlMain->addChild('CarrierId2',strtoupper($_POST['tempcontainer']));
							/*if(strlen($_POST['tempban'])==8){
								//$xmlMain->addChild('PrintMark','Y');
							}
							else{
								//$xmlMain->addChild('PrintMark','N');
							}*/
							$invarray[1]='R';
							$invarray[12]=strtoupper($item[0]['carrierid1']);
							$invarray[13]='';
						}
						else if(is_numeric(substr($item[0]['carriertype'],0,1))){//愛心碼
							//$xmlMain->addChild('DonateMark','1');
							//$xmlMain->addChild('PrintMark','N');
							//$xmlMain->addChild('NPOBAN',$_POST['tempcontainer']);
							$invarray[1]='R';
							$invarray[12]='';
							$invarray[13]=$item[0]['npoban'];
						}
						else if(strlen($item[0]['carriertype'])==16){//自然人憑證
							//$xmlMain->addChild('DonateMark','0');
							//$xmlMain->addChild('CarrierType','CQ0001');
							//$xmlMain->addChild('CarrierId1',strtoupper($_POST['tempcontainer']));
							//$xmlMain->addChild('CarrierId2',strtoupper($_POST['tempcontainer']));
							/*if(strlen($_POST['tempban'])==8){
								//$xmlMain->addChild('PrintMark','Y');
							}
							else{
								//$xmlMain->addChild('PrintMark','N');
							}*/
							$invarray[1]='R';
							$invarray[12]=strtoupper($item[0]['carrierid1']);
							$invarray[13]='';
						}
						else{
							//$xmlMain->addChild('DonateMark','0');
							//$xmlMain->addChild('PrintMark','Y');
							$invarray[1]='R';
							$invarray[12]='';
							$invarray[13]='';
						}
					}
					else{
						//$xmlMain->addChild('DonateMark','0');
						//$xmlMain->addChild('PrintMark','Y');
						$invarray[1]='R';
						$invarray[12]='';
						$invarray[13]='';
					}
					srand(mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'))/pi());
					$rnumber=rand(0,9999);
					while(strlen($rnumber)<4){
						$rnumber='0'.$rnumber;
					}
					//$xmlMain->addChild('RandomNumber',$rnumber);
					$invarray[10]=$item[0]['randomnumber'];
					//$xml->addChild('Details');
					//$value='';
					//$xmlDetails=$xml->Details;
					if($init['init']['invlist']=='2'){//總項
						//$xmlDetails->addChild('ProductItem');
						//$value=$value.'"'.$content['basic']['itemname'].'",';
						//$xmlDetails->ProductItem[0]->addChild('Description',$content['basic']['itemname']);
						//$value=$value.'1,';
						//$xmlDetails->ProductItem[0]->addChild('Quantity','1');
						//$value=$value.$list[0]['invmoney'].',';
						//$xmlDetails->ProductItem[0]->addChild('UnitPrice',$list[0]['invmoney']);
						//$value=$value.$list[0]['invmoney'].',';
						//$xmlDetails->ProductItem[0]->addChild('Amount',$list[0]['invmoney']);
						//$value=$value.'1';
						//$xmlDetails->ProductItem[0]->addChild('SequenceNumber','001');
						//$xmlDetails->ProductItem[0]->addChild('Remark','Tx');
						$invarray[20]=$content['basic']['itemname'];
						$invarray[21]='1';
						$invarray[22]=$item[0]['totalamount'];
						$invarray[23]=$item[0]['totalamount'];
						$invarray[24]='001';
					}
					else{//明細
						$invarray[1]='S';//2019/12/24預設列印發票明細
						$taste=parse_ini_file('../../../database/'.$content['basic']['company'].'-taste.ini',true);
						$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
						$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$item[0]['relatenumber'].'"';
						$itemlist1=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$item[0]['relatenumber'].'"';
						$itemlist2=sqlquery($conn,$sql,'sqlite');
						sqlclose($conn,'sqlite');
						if(isset($itemlist1[0]['AMT'])){
							$itemlist=$itemlist1;
						}
						else if(isset($itemlist2[0]['AMT'])){
							$itemlist=$itemlist2;
						}
						else{
							$itemlist=array();
						}
						for($i=0,$j=0;$i<sizeof($itemlist);$i=$i+2){
							if(isset($itemlist[$i+1]['AMT'])){
								if((intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT']))>0){
									//$xmlDetails->addChild('ProductItem');
									//$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
									//$xmlDetails->ProductItem[$j]->addChild('Description',$itemlist[$i]['ITEMNAME']);
									//$value=$value.$itemlist[$i]['QTY'].',';
									//$xmlDetails->ProductItem[$j]->addChild('Quantity',$itemlist[$i]['QTY']);
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
									//$value=$value.$unitprice.',';
									//$xmlDetails->ProductItem[$j]->addChild('UnitPrice',$unitprice);
									//$value=$value.(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT'])).',';
									//$xmlDetails->ProductItem[$j]->addChild('Amount',(intval($itemlist[$i]['AMT'])+intval($itemlist[$i+1]['AMT'])));
									//$value=$value.'1';
									//$xmlDetails->ProductItem[$j]->addChild('SequenceNumber',str_pad(($j+1),3,'0',STR_PAD_LEFT));
									//$xmlDetails->ProductItem[$j]->addChild('Remark','Tx');

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
								if(intval($itemlist[$i]['AMT'])>0){
									//$xmlDetails->addChild('ProductItem');
									//$value=$value.'"'.$itemlist[$i]['ITEMNAME'].'",';
									//$xmlDetails->ProductItem[$j]->addChild('Description',$itemlist[$i]['ITEMNAME']);
									//$value=$value.$itemlist[$i]['QTY'].',';
									//$xmlDetails->ProductItem[$j]->addChild('Quantity',$itemlist[$i]['QTY']);
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
									//$value=$value.$unitprice.',';
									//$xmlDetails->ProductItem[$j]->addChild('UnitPrice',$unitprice);
									//$value=$value.intval($itemlist[$i]['AMT']).',';
									//$xmlDetails->ProductItem[$j]->addChild('Amount',intval($itemlist[$i]['AMT']));
									//$value=$value.'1';
									//$xmlDetails->ProductItem[$j]->addChild('SequenceNumber',str_pad(($j+1),3,'0',STR_PAD_LEFT));
									//$xmlDetails->ProductItem[$j]->addChild('Remark','Tx');

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
					//$xml->addChild('Amount');
					//$xmlAmount=$xml->Amount;
					if(strlen($item[0]['buyerid'])!=10){
						//$xmlAmount->addChild('SalesAmount',round(intval($invmoney)/1.05));
						//$xmlAmount->addChild('FreeTaxSalesAmount','0');
						//$xmlAmount->addChild('ZeroTaxSalesAmount','0');
						//$xmlAmount->addChild('TaxType','1');
						//$xmlAmount->addChild('TaxRate','0.05');
						//$xmlAmount->addChild('TaxAmount',intval($invmoney)-intval(round($invmoney/1.05)));
						//$xmlAmount->addChild('TotalAmount',$invmoney);

						$invarray[14]=round(intval($item[0]['totalamount'])/1.05);
						$invarray[15]='0';
						$invarray[16]=intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05));
						$invarray[7]=$item[0]['totalamount'];
					}
					else{
						//$xmlAmount->addChild('SalesAmount',$invmoney);
						//$xmlAmount->addChild('FreeTaxSalesAmount','0');
						//$xmlAmount->addChild('ZeroTaxSalesAmount','0');
						//$xmlAmount->addChild('TaxType','1');
						//$xmlAmount->addChild('TaxRate','0.05');
						//$xmlAmount->addChild('TaxAmount','0');
						//$xmlAmount->addChild('TotalAmount',$invmoney);
						
						$invarray[14]=$item[0]['totalamount'];
						$invarray[15]='0';
						$invarray[16]='0';
						$invarray[7]=$item[0]['totalamount'];
					}
					//print_r($invarray);
					/*if(file_exists('../../../print/zdninv')){
					}
					else{
						mkdir('../../../print/zdninv');
					}*/
					$inv=fopen('../../../print/noread/'.$filename,'w');
					foreach($invarray as $i){
						fwrite($inv,$i.PHP_EOL);
					}
					fclose($inv);
					//$xml->saveXML('../../../print/noread/'.$filename);
					echo 'success';
				}
				else{
					if(strlen($item[0]['carriertype'])>0){
						if(substr($item[0]['carriertype'],0,1)=='/'){//手機條碼
							if(strlen($item[0]['buyerid'])==8){
								$xml['Main']['PrintMark']='Y';
							}
							else{
								$xml['Main']['PrintMark']='N';
							}
						}
						else if(is_numeric(substr($item[0]['carriertype'],0,1))){//愛心碼
							$xml['Main']['PrintMark']='N';
						}
						else if(strlen($item[0]['carriertype'])==16){//自然人憑證
							if(strlen($item[0]['buyerid'])==8){
								$xml['Main']['PrintMark']='Y';
							}
							else{
								$xml['Main']['PrintMark']='N';
							}
						}
						else{
							$xml['Main']['PrintMark']='Y';
						}
					}
					else{
						$xml['Main']['PrintMark']='Y';
					}
					if($xml['Main']['PrintMark']=='Y'){
						if(intval(substr($item[0]['createdate'],4,2))%2==0){
							$invm=substr($item[0]['createdate'],4,2);
						}
						else{
							$invm=intval(substr($item[0]['createdate'],4,2))+1;
						}
						if(strlen($invm)<2){
							$invm='0'.$invm;
						}
						include_once '../../../tool/AES/lib.php';
						include_once '../../../tool/phpqrcode/qrlib.php';
						include_once '../../../tool/phpbarcode/src/BarcodeGenerator.php';
						include_once '../../../tool/phpbarcode/src/BarcodeGeneratorPNG.php';
						include_once '../../../tool/AES/lib.php';
						include_once '../../../tool/phpqrcode/qrlib.php';
						
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
						if(substr(strtoupper($inv),0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
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
						$pdf->SetFont('DroidSansFallback', 'B', 14);
						$pdf->MultiCell('', '', "電子發票證明聯補印", 0, 'C', 0, 0, 0, 8.5, 1, 0, 0, 0, 10, 'T', 0);
						$pdf->SetFont('DroidSansFallback', 'B', 17);
						$pdf->MultiCell('', '', (intval(substr($item[0]['createdate'],0,4))-1911)."年".str_pad((intval($invm)-1),2,'0',STR_PAD_LEFT)."－".$invm."月", 0, 'C', 0, 0, 0, 15, 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell('', '', substr(strtoupper($inv),0,2).'－'.substr(strtoupper($inv),2), 0, 'C', 0, 0, 0, 21.5, 1, 0, 0, 0, 10, 'T', 0);
					}
					else{
					}
					if($xml['Main']['PrintMark']=='Y'){
						$pdf->SetFont('DroidSansFallback', '', 9);
						$pdf->MultiCell('', '', "隨機碼:".$item[0]['randomnumber'], 0, 'L', 0, 0, 3, 32.5, 1, 0, 0, 0, 10, 'T', 0);//2021/5/27 因為cell有指定(x,y)座標，所以不用依照順序填入
						$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
						file_put_contents('../../../print/barcode/'.(intval(substr($item[0]['createdate'],0,4))-1911).$invm.strtoupper($inv).$item[0]['randomnumber'].'barcode.png', $generator->getBarcodeNoText(((intval(substr($item[0]['createdate'],0,4))-1911).$invm.strtoupper($inv).$item[0]['randomnumber']), $generator::TYPE_CODE_39));
						$pdf->Image('../../../print/barcode/'.(intval(substr($item[0]['createdate'],0,4))-1911).$invm.strtoupper($inv).$item[0]['randomnumber'].'barcode.png',3,35,45,20,'png','','M',1,300);
						$pdf->SetFont('DroidSansFallback', '', 9);
						$pdf->MultiCell('', '', substr($item[0]['createdate'],0,4).'-'.substr($item[0]['createdate'],4,2).'-'.substr($item[0]['createdate'],6,2)." ".$item[0]['createtime'], 0, 'L', 0, 0, 3, 29, 1, 0, 0, 0, 10, 'T', 0);
					}
					else{
					}
					if(strlen($item[0]['buyerid'])!=10){
						$buyerid=$item[0]['buyerid'];
						if($xml['Main']['PrintMark']=='Y'){
							$pdf->MultiCell('', '', "格式:25", 0, 'L', 0, 0, 35, 29, 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "買方".$item[0]['buyerid'], 0, 'L', 0, 0, 26, 36, 1, 0, 0, 0, 10, 'T', 0);//2021/5/27 因為cell有指定(x,y)座標，所以不用依照順序填入
						}
						else{
						}
						$buyername='0000';
						$buyerhexmoney=str_pad((intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05))),8,'0',STR_PAD_LEFT);
					}
					else{
						$buyerid='00000000';
						$buyername='0000000000';
						$buyerhexmoney='00000000';
					}
					if($xml['Main']['PrintMark']=='Y'){
						$pdf->MultiCell('', '', "總計:".$item[0]['totalamount'], 0, 'L', 0, 0, 25, 32.5, 1, 0, 0, 0, 10, 'T', 0);
						$pdf->MultiCell('', '', "賣方".$content['basic']['Identifier'], 0, 'L', 0, 0, 3, 36, 1, 0, 0, 0, 10, 'T', 0);
						$qrcodeClass = new encryQrcode();
						$aesKey = "1905B9C0E27FB708712E42CED49178AB";// input your aeskey
						$invoiceNumAndRandomCode = strtoupper($inv).$item[0]['randomnumber'];// input your invoiceNumber And RandomCode
						$encry=$qrcodeClass->aes128_cbc_encrypt($aesKey, $invoiceNumAndRandomCode);
						QRcode::png(strtoupper($inv).$item[0]['createdate'].$item[0]['randomnumber'].$buyerhexmoney.str_pad(dechex($item[0]['totalamount']),8,'0',STR_PAD_LEFT).$buyerid.$content['basic']['Identifier'].$encry.":**********:1:1:1:".$content['basic']['itemname'].":1:".$item[0]['totalamount'], "../../../print/qrcode/leftqrcode.png", "L", "4", 2);
						$pdf->Image("../../../print/qrcode/leftqrcode.png",5,51,20,20,'png','','M',1,300);
						QRcode::png("**                                                                                                                                   ", "../../../print/qrcode/rightqrcode.png", "L", "4", 2);
						$pdf->Image("../../../print/qrcode/rightqrcode.png",28,51,20,20,'png','','M',1,300);
						$pdf->SetFont('DroidSansFallback', '', 8.5);
						$pdf->MultiCell('', '', "機號:".$invmachine, 0, 'L', 0, 0, 3, 71, 1, 0, 0, 0, 10, 'T', 0);

						$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
						$sql='SELECT CONSECNUMBER FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND INVOICENUMBER="'.$inv.'"';
						$consecnumber=sqlquery($conn,$sql,'sqlite');
						sqlclose($conn,'sqlite');
						if(isset($consecnumber[0]['CONSECNUMBER'])){
							$pdf->MultiCell('', '', "單號:".$consecnumber[0]['CONSECNUMBER'], 0, 'R', 0, 1, 3, 71, 1, 0, 0, 0, 10, 'T', 0);
						}
						else{
							$pdf->MultiCell('', '', "單號:", 0, 'R', 0, 1, 3, 71, 1, 0, 0, 0, 10, 'T', 0);
						}

						//若發票檔頭為圖檔，則需要列印門市名
						if(substr(strtoupper($inv),0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
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
					if($init['init']['invlist']=='2'){//總項
						if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
							if(!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8){//有買方統編時，必列印消費明細，且消費明細與證明聯不切分
								$pagey=80;
							}
							else{//if(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1')//列印消費明細，明細與證明聯切分兩張
								$pdf->lastPage();
								$pdf->AddPage();
								$pagey=0;
							}
							$pdf->SetFont('DroidSansFallback', '', 12);
							if(substr(strtoupper($inv),0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
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
							$pdf->MultiCell(15, '', $item[0]['totalamount']."x", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell(10, '', "1", 0, 'R', 0, 0, 18, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell(20, '', $item[0]['totalamount']."TX", 0, 'R', 0, 1, 28, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->SetFont('DroidSansFallback', '', 10);
							$pdf->MultiCell('', '', "---------------------------------------", 0, 'C', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->SetFont('DroidSansFallback', '', 8);
							$pdf->MultiCell('', '', "總計", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄".$item[0]['totalamount'], 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							if(strlen($item[0]['buyerid'])!=10){
								$pdf->MultiCell('', '', "應稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄".intval(round($item[0]['totalamount']/1.05)), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "免稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄0", 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "稅額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄".(intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05))), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
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
						$sql='SELECT * FROM CST012 JOIN CST011 ON CST011.BIZDATE="'.$_POST['bizdate'].'" AND CST011.INVOICENUMBER="'.$inv.'" AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.BIZDATE=CST012.BIZDATE';
						$itemlist=sqlquery($conn,$sql,'sqlite');
						sqlclose($conn,'sqlite');
						if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
							if(!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8){//有買方統編時，必列印消費明細，且消費明細與證明聯不切分
								$pagey=80;
							}
							else{//if(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1')//列印消費明細，明細與證明聯切分兩張
								$pdf->lastPage();
								$pdf->AddPage();
								$pagey=0;
							}
							$pdf->SetFont('DroidSansFallback', '', 12);
							if(substr(strtoupper($inv),0,2)=='OO'){//2021/6/4 發票號碼開頭OO為測試發票
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
									$invname=$itemlist[$i]['ITEMNAME'];
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
												if($t!=1&&$k!=0){
													$invname.=',';
												}
												else{
												}
												$invname=$taste[intval(substr($temptaste[$k],0,5))]['name1'];
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
									if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
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
									$invname=$itemlist[$i]['ITEMNAME'];
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
												if($t!=1&&$k!=0){
													$invname.=',';
												}
												else{
												}
												$invname=$taste[intval(substr($temptaste[$k],0,5))]['name1'];
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
									if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
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
						if($xml['Main']['PrintMark']=='Y'&&((!is_numeric(substr($item[0]['carrierid1'],0,1))&&strlen($item[0]['buyerid'])==8)||(isset($content['zdninv']['printlist'])&&$content['zdninv']['printlist']=='1'))){//2021/5/27 列印消費明細的兩種狀況
							$pdf->SetFont('DroidSansFallback', '', 10);
							$pdf->MultiCell('', '', "---------------------------------------", 0, 'C', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->SetFont('DroidSansFallback', '', 8);
							$pdf->MultiCell('', '', "總計", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							$pdf->MultiCell('', '', "＄".$item[0]['totalamount'], 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							if(strlen($item[0]['buyerid'])!=10){
								$pdf->MultiCell('', '', "應稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄".intval(round($item[0]['totalamount']/1.05)), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "免稅銷售額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄0", 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "稅額", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
								$pdf->MultiCell('', '', "＄".(intval($item[0]['totalamount'])-intval(round($item[0]['totalamount']/1.05))), 0, 'R', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);
							}
							else{
							}
							$pdf->lastPage();
						}
						else{
						}
					}
					if($xml['Main']['PrintMark']=='Y'){
						$pdf->Output(dirname(__FILE__).'/../../../print/noread/'.$content['basic']['Identifier'].'_RC0401'.$invmachine.'_'.strtoupper($inv).'_'.substr($item[0]['createdate'],4).$time[0].$time[1].$time[2].'.pdf', 'F');
					}
					else{
					}
					echo 'success';
				}
			}
		}
		else{
			echo 'dataempty';
		}
	}
/*}
else{
}*/
?>