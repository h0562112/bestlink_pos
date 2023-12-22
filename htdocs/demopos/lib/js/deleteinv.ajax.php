<?php
include_once '../../../tool/myerrorlog.php';
include '../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($initsetting['init']['settime']);
$Y=date('Y');
$year=(intval($Y)-1911);
$m=date('m');
if(intval($m)%2==0){
	$month=$m;
}
else{
	$month=intval($m)+1;
}
if(strlen($month)<2){
	$month='0'.$month;
}
$day=date('d');
$hour=date('H');
$min=date('m');
$sec=date('i');
if(intval(substr($_POST['bizdate'],4,2))%2==1){
	if(intval(substr($_POST['bizdate'],4,2))<9){
		$invdate=substr($_POST['bizdate'],0,4).'0'.(intval(substr($_POST['bizdate'],4,2))+1);
	}
	else{
		$invdate=substr($_POST['bizdate'],0,4).(intval(substr($_POST['bizdate'],4,2))+1);
	}
}
else{
	$invdate=substr($_POST['bizdate'],0,6);
}
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

if(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1'){
	if(file_exists("../../../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db")){
		$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
		$number=$_POST['number'];
		$sql='SELECT * FROM invlist WHERE invnumber="'.$number.'" AND state=1';
		$data=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');

		if(sizeof($data)==0){
			echo 'invlist is empty '.$number;
		}
		else if($data[0]=='連線失敗'||$data[0]=='SQL語法錯誤'){
			echo 'database error';
		}
		else{
			if(substr($data[0]['createdate'],0,6)!=($Y.$month)&&substr($data[0]['createdate'],0,6)!=date('Ym',strtotime($Y.'-'.$month.'-'.'01 -1 month'))){
				echo 'is not now'.substr($data[0]['createdate'],0,6).($Y.$month);
			}
			else{
				$content=parse_ini_file('../../../database/setup.ini',true);
				if(!isset($content['basic']['sendinvlocation'])||$content['basic']['sendinvlocation']=='1'){//亮點
					include '../../../demoinv/cancelinv.php';
					$filename='C0501_'.$number.'_'.$Y.$m.$day.$hour.$min.$sec.'_'.$invmachine.'.xml';
					$xml=new SimpleXMLElement($xmlstr);
					$xml->addChild('CancelInvoiceNumber',$data[0]['invnumber']);
					$xml->addChild('InvoiceDate',$data[0]['createdate']);
					$xml->addChild('BuyerId',$data[0]['buyerid']);
					$xml->addChild('SellerId',$content['basic']['Identifier']);
					$xml->addChild('CancelDate',$Y.$m.$day);
					$xml->addChild('CancelTime',$hour.':'.$min.':'.$sec);
					$xml->addChild('CancelReason','作廢發票');
					$xml->saveXML('../../../print/noread/'.$filename);
					$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
					$sql='UPDATE invlist SET state=99 WHERE invnumber="'.$number.'"';
					sqlnoresponse($conn,$sql,'sqlite');
					sqlclose($conn,'sqlite');
					//echo $filename;
				}
				else if($content['basic']['sendinvlocation']=='2'){//神通
				}
				else if($content['basic']['sendinvlocation']=='3'){//中鼎
					$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
					$sql='SELECT CLKCODE FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND INVOICENUMBER="'.$number.'"';
					$clkcode=sqlquery($conn,$sql,'sqlite');
					sqlclose($conn,'sqlite');
					if(isset($clkcode[0]['CLKCODE'])&&$clkcode[0]['CLKCODE']=='chiB2B'){//2020/12/16 正航B2B發票作廢流程
						include '../../../invofchi/B2B/B2Bdelinv.php';
						$filename='A0501_'.$content['basic']['Identifier'].'_'.$number.'_'.$m.$day.$hour.$min.$sec.'.xml';
						$xml=new SimpleXMLElement($xmlstr);
						$xml->addChild('CancelInvoiceNumber',$data[0]['invnumber']);
						$xml->addChild('InvoiceDate',$data[0]['createdate']);
						$xml->addChild('BuyerId',$data[0]['buyerid']);
						$xml->addChild('SellerId',$content['basic']['Identifier']);
						$xml->addChild('CancelDate',$Y.$m.$day);
						$xml->addChild('CancelTime',$hour.':'.$min.':'.$sec);
						$xml->addChild('CancelReason','作廢發票');
						$xml->addChild('ReturnTaxDocumentNumber','');
						$xml->addChild('Remark','');
						$xml->saveXML('../../../print/noread/'.$filename);
						$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
						$sql='UPDATE invlist SET state=99 WHERE invnumber="'.$number.'"';
						sqlnoresponse($conn,$sql,'sqlite');
						sqlclose($conn,'sqlite');
						//echo $filename;
						
						$basic=parse_ini_file('../../../invofchi/basic.php',true);
						$ftpconn=ftp_connect($basic['B2Bset']['ftp'],$basic['B2Bset']['port']) or die ("Cannot initiate connection to host");
						ftp_login($ftpconn,$basic['B2Bset']['id'],$basic['B2Bset']['pw']) or die("Cannot login");
						$upload=ftp_put($ftpconn,'./'.$filename,'../print/noread/'.$filename,FTP_BINARY);
						if(!$upload){
						}
						else{
							rename('../print/noread/'.$filename,'../print/read/'.$filename);
						}
						ftp_close($ftpconn);
					}
					else{
						if(!isset($content['zdninv']['useEIP'])||$content['zdninv']['useEIP']=='1'){
							$filename='C0501_'.$number.'_'.$Y.$m.$day.$hour.$min.$sec.'_'.$invmachine.'.xml';
							/*if(file_exists('../../../print/zdninv')){
							}
							else{
								mkdir('../../../print/zdninv');
							}*/
							$inv=fopen('../../../print/noread/'.$filename,'w');
							fwrite($inv,'D'.PHP_EOL);
							fwrite($inv,$data[0]['createdate'].PHP_EOL);
							fwrite($inv,$data[0]['invnumber'].PHP_EOL);
							fclose($inv);
						}
						else{
							$filename='C0501_'.$content['basic']['Identifier'].'_'.$number.'_'.$m.$day.$hour.$min.$sec.'.json';
							$xml['CancelInvoiceNumber']=$data[0]['invnumber'];
							$xml['InvoiceDate']=$data[0]['createdate'];
							$xml['BuyerId']=$data[0]['buyerid'];
							$xml['SellerId']=$content['basic']['Identifier'];
							$xml['CancelDate']=$Y.$m.$day;
							$xml['CancelTime']=$hour.':'.$min.':'.$sec;
							$xml['CancelReason']='作廢發票';
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
							$f=fopen('../../../print/invuploadlog/waitupload/'.$filename,'w');
							fwrite($f,json_encode($xml));
							fclose($f);
						}
						$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
						$sql='UPDATE invlist SET state=99 WHERE invnumber="'.$number.'"';
						sqlnoresponse($conn,$sql,'sqlite');
						sqlclose($conn,'sqlite');
						//echo $filename;
					}
				}
			}
		}
	}
	else{
	}
}
else if(isset($initsetting['init']['useoinv'])&&$initsetting['init']['useoinv']=='1'){
	if(file_exists("../../../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db")){
		$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
		$number=$_POST['number'];
		$sql='SELECT * FROM invlist WHERE invnumber="'.$number.'" AND state=1';
		$data=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');

		if(sizeof($data)==0){
			echo 'invlist is empty '.$number;
		}
		else if($data[0]=='連線失敗'||$data[0]=='SQL語法錯誤'){
			echo 'database error';
		}
		else{
			if(substr($data[0]['createdate'],0,6)!=($Y.$month)&&substr($data[0]['createdate'],0,6)!=date('Ym',strtotime($Y.'-'.$month.'-'.'01 -1 month'))){
				echo 'is not now'.substr($data[0]['createdate'],0,6).($Y.$month);
			}
			else{
				$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
				$sql='UPDATE invlist SET state=99 WHERE invnumber="'.$number.'"';
				sqlnoresponse($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
			}
		}
	}
	else{
	}
}
else{
}
?>