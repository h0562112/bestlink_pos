<?php
include_once '../../../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$Y=date('Y');
$year=(intval($Y)-1911);
$month=date('m');
$day=date('d');
$hour=date('H');
$min=date('i');
$sec=date('s');
print_r($_POST);
if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['month'].'/invdata_'.$_POST['month'].'.db')){
	$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['month'],'invdata_'.$_POST['month'].'.db',"","","","sqlite");
	$sql='select * from invlist where invnumber="'.$_POST['invnumber'].'"';
	//echo $sql;
	$data=sqlquery($conn,$sql,'sqlite');
	$sql='select * from salelist where invnumber="'.$_POST['invnumber'].'" order by lineno asc';
	$list=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(sizeof($data)&&isset($data[0]['invnumber'])){
		include_once 'C0501.php';
		$filename='C0501_'.$_POST['invnumber'].'_'.$Y.$month.$day.$hour.$min.$sec.'.xml';
		$xml=new SimpleXMLElement($o501xmlstr);
		$xml->addChild('CancelInvoiceNumber',$_POST['invnumber']);
		$xml->addChild('InvoiceDate',$data[0]['createdate']);
		$xml->addChild('BuyerId',$data[0]['buyerid']);
		$xml->addChild('SellerId',$data[0]['sellerid']);
		$xml->addChild('CancelDate',$Y.$month.$day);
		$xml->addChild('CancelTime',$hour.':'.$min.':'.$sec);
		$xml->addChild('CancelReason','作廢');
		
		if(file_exists('./file')){
		}
		else{
			mkdir('./file');
		}
		if(file_exists('./file/'.$_POST['company'])){
		}
		else{
			mkdir('./file/'.$_POST['company']);
		}
		if(file_exists('./file/'.$_POST['company'].'/'.$_POST['dep'])){
		}
		else{
			mkdir('./file/'.$_POST['company'].'/'.$_POST['dep']);
		}
		$xml->saveXML('./file/'.$_POST['company'].'/'.$_POST['dep'].'/'.$filename);
		$f=fopen('./file/'.$_POST['company'].'/'.$_POST['dep'].'/inv.txt','a');
		fwrite($f,'UPDATE invlist SET state=99 WHERE invnumber="'.$_POST['invnumber'].'";'.PHP_EOL);
		fclose($f);
		$f=fopen('./file/'.$_POST['company'].'/'.$_POST['dep'].'/sale.txt','a');
		fwrite($f,'UPDATE CST011 SET NBCHKDATE="'.$Y.$month.$day.$hour.$min.$sec.'",NBCHKTIME="admin",NBCHKNUMBER="Y" WHERE INVOICENUMBER="'.$_POST['invnumber'].'";'.PHP_EOL);
		fclose($f);
		echo "產生完成，請至遠端檢查檔案是否正確，並將檔案轉至客戶端上傳。\n\n請記得將客戶端的invdata發票資料改為作廢。";
	}
	else{
		echo '查無發票資訊。';
	}
}
else if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['month'].'/invdata_'.$_POST['month'].'_'.$_POST['machine'].'.db')){
	$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['month'],'invdata_'.$_POST['month'].'_'.$_POST['machine'].'.db',"","","","sqlite");
	$sql='select * from invlist where invnumber="'.$_POST['invnumber'].'"';
	//echo $sql;
	$data=sqlquery($conn,$sql,'sqlite');
	$sql='select * from salelist where invnumber="'.$_POST['invnumber'].'" order by lineno asc';
	$list=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(sizeof($data)&&isset($data[0]['invnumber'])){
		include_once 'C0501.php';
		$filename='C0501_'.$_POST['invnumber'].'_'.$Y.$month.$day.$hour.$min.$sec.'.xml';
		$xml=new SimpleXMLElement($o501xmlstr);
		$xml->addChild('CancelInvoiceNumber',$_POST['invnumber']);
		$xml->addChild('InvoiceDate',$data[0]['createdate']);
		$xml->addChild('BuyerId',$data[0]['buyerid']);
		$xml->addChild('SellerId',$data[0]['sellerid']);
		$xml->addChild('CancelDate',$Y.$month.$day);
		$xml->addChild('CancelTime',$hour.':'.$min.':'.$sec);
		$xml->addChild('CancelReason','作廢');
		
		if(file_exists('./file')){
		}
		else{
			mkdir('./file');
		}
		if(file_exists('./file/'.$_POST['company'])){
		}
		else{
			mkdir('./file/'.$_POST['company']);
		}
		if(file_exists('./file/'.$_POST['company'].'/'.$_POST['dep'])){
		}
		else{
			mkdir('./file/'.$_POST['company'].'/'.$_POST['dep']);
		}
		$xml->saveXML('./file/'.$_POST['company'].'/'.$_POST['dep'].'/'.$filename);
		$f=fopen('./file/'.$_POST['company'].'/'.$_POST['dep'].'/inv.txt','a');
		fwrite($f,'UPDATE invlist SET state=99 WHERE invnumber="'.$_POST['invnumber'].'";'.PHP_EOL);
		fclose($f);
		$f=fopen('./file/'.$_POST['company'].'/'.$_POST['dep'].'/sale.txt','a');
		fwrite($f,'UPDATE CST011 SET NBCHKDATE="'.$Y.$month.$day.$hour.$min.$sec.'",NBCHKTIME="admin",NBCHKNUMBER="Y" WHERE INVOICENUMBER="'.$_POST['invnumber'].'";'.PHP_EOL);
		fclose($f);
		echo "產生完成，請至遠端檢查檔案是否正確，並將檔案轉至客戶端上傳。\n\n請記得將客戶端的invdata發票資料改為作廢。";
	}
	else{
		echo '查無發票資訊。'.$_POST['machine'];
	}
}
else{
	echo '查無發票資訊。ALL';
}
?>