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
if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['month'].'/invdata_'.$_POST['month'].'.db')){
	$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['month'],'invdata_'.$_POST['month'].'.db',"","","","sqlite");
	$sql='select * from invlist where invnumber="'.strtoupper($_POST['invnumber']).'"';
	$data=sqlquery($conn,$sql,'sqlite');
	$sql='select * from salelist where invnumber="'.strtoupper($_POST['invnumber']).'" order by lineno asc';
	$list=sqlquery($conn,$sql,'sqlite');
	if(sizeof($data)&&isset($data[0]['invnumber'])){
		include_once 'C0701.php';
		$filename='C0701_'.strtoupper($_POST['invnumber']).'_'.$Y.$month.$day.$hour.$min.$sec.'.xml';
		$xml=new SimpleXMLElement($o701xmlstr);
		$xml->addChild('VoidInvoiceNumber',strtoupper($_POST['invnumber']));
		$xml->addChild('InvoiceDate',$data[0]['createdate']);
		$xml->addChild('BuyerId',$data[0]['buyerid']);
		$xml->addChild('SellerId',$data[0]['sellerid']);
		$xml->addChild('VoidDate',$Y.$month.$day);
		$xml->addChild('VoidTime',$hour.':'.$min.':'.$sec);
		$xml->addChild('VoidReason','操作錯誤');
		
		$xml->saveXML('./file/'.$_POST['company'].'/'.$_POST['dep'].'/'.$filename);


		include_once 'C0401.php';
		$content=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/setup.ini',true);
		$filename='C0401_'.strtoupper($_POST['invnumber']).'_'.$data[0]['createdate'].preg_replace("/:/",'',$data[0]['createtime']).'.xml';
		$xml=new SimpleXMLElement($o401xmlstr);
		$xml->addChild('Main');
		$xmlMain=$xml->Main;
		$xmlMain->addChild('InvoiceNumber',$data[0]['invnumber']);
		$xmlMain->addChild('InvoiceDate',$data[0]['createdate']);
		$xmlMain->addChild('InvoiceTime',$data[0]['createtime']);
		$xmlMain->addChild('Seller');
		$xmlMainSeller=$xmlMain->Seller;
		$xmlMainSeller->addChild('Identifier',$content['basic']['Identifier']);
		$xmlMainSeller->addChild('Name',$content['basic']['Name']);
		$xmlMain->addChild('Buyer');
		$xmlMainBuyer=$xmlMain->Buyer;
		if(strlen($data[0]['buyerid'])==8){
			$xmlMainBuyer->addChild('Identifier',$data[0]['buyerid']);
			$xmlMainBuyer->addChild('Name',$data[0]['buyname']);
		}
		else{
			$xmlMainBuyer->addChild('Identifier','0000000000');
			$xmlMainBuyer->addChild('Name','0000000000');
		}
		$xmlMain->addChild('RelateNumber',$data[0]['relatenumber']);
		$xmlMain->addChild('InvoiceType','07');

		$xmlMain->addChild('DonateMark',$data[0]['donatemark']);
		if(isset($data[0]['carriertype'])&&$data[0]['carriertype']!=''&&$data[0]['carriertype']!=null){
			$xmlMain->addChild('CarrierType',$data[0]['carriertype']);
		}
		else{
		}
		if(isset($data[0]['carrierid1'])&&$data[0]['carrierid1']!=''&&$data[0]['carrierid1']!=null){
			$xmlMain->addChild('CarrierId1',$data[0]['carrierid1']);
		}
		else{
		}
		if(isset($data[0]['carrierid2'])&&$data[0]['carrierid2']!=''&&$data[0]['carrierid2']!=null){
			$xmlMain->addChild('CarrierId2',$data[0]['carrierid2']);
		}
		else{
		}
		$xmlMain->addChild('PrintMark',$data[0]['printmark']);
		if(isset($data[0]['npoban'])&&$data[0]['npoban']!=''&&$data[0]['npoban']!=null){
			$xmlMain->addChild('NPOBAN',$data[0]['npoban']);
		}
		else{
		}
		$xmlMain->addChild('RandomNumber',$data[0]['randomnumber']);

		$xml->addChild('Details');
		$xmlDetails=$xml->Details;
		foreach($list as $l){
			$xmlDetails->addChild('ProductItem');
			$xmlDetails->ProductItem[0]->addChild('Description',$l['name']);
			$xmlDetails->ProductItem[0]->addChild('Quantity',$l['qty']);
			$xmlDetails->ProductItem[0]->addChild('UnitPrice',$l['unitprice']);
			$xmlDetails->ProductItem[0]->addChild('Amount',$l['money']);
			$xmlDetails->ProductItem[0]->addChild('SequenceNumber',str_pad($l['lineno'], 3, "0", STR_PAD_LEFT));
			$xmlDetails->ProductItem[0]->addChild('Remark','Tx');
		}
		
		$xml->addChild('Amount');
		$xmlAmount=$xml->Amount;
		if(strlen($data[0]['buyerid'])==8){
			$xmlAmount->addChild('SalesAmount',round(intval($data[0]['totalamount'])/1.05));
			$xmlAmount->addChild('FreeTaxSalesAmount','0');
			$xmlAmount->addChild('ZeroTaxSalesAmount','0');
			$xmlAmount->addChild('TaxType','1');
			$xmlAmount->addChild('TaxRate','0.05');
			$xmlAmount->addChild('TaxAmount',intval($data[0]['totalamount'])-intval(round($data[0]['totalamount']/1.05)));
			$xmlAmount->addChild('TotalAmount',$data[0]['totalamount']);
		}
		else{
			$xmlAmount->addChild('SalesAmount',$data[0]['totalamount']);
			$xmlAmount->addChild('FreeTaxSalesAmount','0');
			$xmlAmount->addChild('ZeroTaxSalesAmount','0');
			$xmlAmount->addChild('TaxType','1');
			$xmlAmount->addChild('TaxRate','0.05');
			$xmlAmount->addChild('TaxAmount','0');
			$xmlAmount->addChild('TotalAmount',$data[0]['totalamount']);

		}

		$xml->saveXML('./file/'.$_POST['company'].'/'.$_POST['dep'].'/'.$filename);
		$f=fopen('./file/'.$_POST['company'].'/'.$_POST['dep'].'/inv.txt','a');
		fwrite($f,'UPDATE invlist SET state=1 WHERE invnumber="'.$_POST['invnumber'].'";'.PHP_EOL);
		fclose($f);
		$f=fopen('./file/'.$_POST['company'].'/'.$_POST['dep'].'/sale.txt','a');
		fwrite($f,'UPDATE CST011 SET NBCHKDATE=NULL,NBCHKTIME=NULL,NBCHKNUMBER=NULL WHERE INVOICENUMBER="'.$_POST['invnumber'].'";'.PHP_EOL);
		fclose($f);
		echo "產生完成，請至遠端檢查檔案是否正確，並將檔案轉至客戶端上傳。\n\n請記得將客戶端的invdata發票資料改為非作廢。\n\n其中C0701與C0401兩個檔案需間隔3個小時分別放入noread資料夾中。";
	}
	else{
		echo '查無發票資訊。';
	}
	sqlclose($conn,'sqlite');
}
else if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['month'].'/invdata_'.$_POST['month'].'_'.$_POST['machine'].'.db')){
	$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['month'],'invdata_'.$_POST['month'].'_'.$_POST['machine'].'.db',"","","","sqlite");
	$sql='select * from invlist where invnumber="'.strtoupper($_POST['invnumber']).'"';
	$data=sqlquery($conn,$sql,'sqlite');
	$sql='select * from salelist where invnumber="'.strtoupper($_POST['invnumber']).'" order by lineno asc';
	$list=sqlquery($conn,$sql,'sqlite');
	if(sizeof($data)&&isset($data[0]['invnumber'])){
		include_once 'C0701.php';
		$filename='C0701_'.strtoupper($_POST['invnumber']).'_'.$Y.$month.$day.$hour.$min.$sec.'.xml';
		$xml=new SimpleXMLElement($o701xmlstr);
		$xml->addChild('VoidInvoiceNumber',strtoupper($_POST['invnumber']));
		$xml->addChild('InvoiceDate',$data[0]['createdate']);
		$xml->addChild('BuyerId',$data[0]['buyerid']);
		$xml->addChild('SellerId',$data[0]['sellerid']);
		$xml->addChild('VoidDate',$Y.$month.$day);
		$xml->addChild('VoidTime',$hour.':'.$min.':'.$sec);
		$xml->addChild('VoidReason','操作錯誤');
		
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


		include_once 'C0401.php';
		$content=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/setup.ini',true);
		$filename='C0401_'.strtoupper($_POST['invnumber']).'_'.$data[0]['createdate'].preg_replace("/:/",'',$data[0]['createtime']).'.xml';
		$xml=new SimpleXMLElement($o401xmlstr);
		$xml->addChild('Main');
		$xmlMain=$xml->Main;
		$xmlMain->addChild('InvoiceNumber',$data[0]['invnumber']);
		$xmlMain->addChild('InvoiceDate',$data[0]['createdate']);
		$xmlMain->addChild('InvoiceTime',$data[0]['createtime']);
		$xmlMain->addChild('Seller');
		$xmlMainSeller=$xmlMain->Seller;
		$xmlMainSeller->addChild('Identifier',$content['basic']['Identifier']);
		$xmlMainSeller->addChild('Name',$content['basic']['Name']);
		$xmlMain->addChild('Buyer');
		$xmlMainBuyer=$xmlMain->Buyer;
		if(strlen($data[0]['buyerid'])==8){
			$xmlMainBuyer->addChild('Identifier',$data[0]['buyerid']);
			$xmlMainBuyer->addChild('Name',$data[0]['buyname']);
		}
		else{
			$xmlMainBuyer->addChild('Identifier','0000000000');
			$xmlMainBuyer->addChild('Name','0000000000');
		}
		$xmlMain->addChild('RelateNumber',$data[0]['relatenumber']);
		$xmlMain->addChild('InvoiceType','07');

		$xmlMain->addChild('DonateMark',$data[0]['donatemark']);
		if(isset($data[0]['carriertype'])&&$data[0]['carriertype']!=''&&$data[0]['carriertype']!=null){
			$xmlMain->addChild('CarrierType',$data[0]['carriertype']);
		}
		else{
		}
		if(isset($data[0]['carrierid1'])&&$data[0]['carrierid1']!=''&&$data[0]['carrierid1']!=null){
			$xmlMain->addChild('CarrierId1',$data[0]['carrierid1']);
		}
		else{
		}
		if(isset($data[0]['carrierid2'])&&$data[0]['carrierid2']!=''&&$data[0]['carrierid2']!=null){
			$xmlMain->addChild('CarrierId2',$data[0]['carrierid2']);
		}
		else{
		}
		$xmlMain->addChild('PrintMark',$data[0]['printmark']);
		if(isset($data[0]['npoban'])&&$data[0]['npoban']!=''&&$data[0]['npoban']!=null){
			$xmlMain->addChild('NPOBAN',$data[0]['npoban']);
		}
		else{
		}
		$xmlMain->addChild('RandomNumber',$data[0]['randomnumber']);

		$xml->addChild('Details');
		$xmlDetails=$xml->Details;
		foreach($list as $l){
			$xmlDetails->addChild('ProductItem');
			$xmlDetails->ProductItem[0]->addChild('Description',$l['name']);
			$xmlDetails->ProductItem[0]->addChild('Quantity',$l['qty']);
			$xmlDetails->ProductItem[0]->addChild('UnitPrice',$l['unitprice']);
			$xmlDetails->ProductItem[0]->addChild('Amount',$l['money']);
			$xmlDetails->ProductItem[0]->addChild('SequenceNumber',str_pad($l['lineno'], 3, "0", STR_PAD_LEFT));
			$xmlDetails->ProductItem[0]->addChild('Remark','Tx');
		}
		
		$xml->addChild('Amount');
		$xmlAmount=$xml->Amount;
		if(strlen($data[0]['buyerid'])==8){
			$xmlAmount->addChild('SalesAmount',round(intval($data[0]['totalamount'])/1.05));
			$xmlAmount->addChild('FreeTaxSalesAmount','0');
			$xmlAmount->addChild('ZeroTaxSalesAmount','0');
			$xmlAmount->addChild('TaxType','1');
			$xmlAmount->addChild('TaxRate','0.05');
			$xmlAmount->addChild('TaxAmount',intval($data[0]['totalamount'])-intval(round($data[0]['totalamount']/1.05)));
			$xmlAmount->addChild('TotalAmount',$data[0]['totalamount']);
		}
		else{
			$xmlAmount->addChild('SalesAmount',$data[0]['totalamount']);
			$xmlAmount->addChild('FreeTaxSalesAmount','0');
			$xmlAmount->addChild('ZeroTaxSalesAmount','0');
			$xmlAmount->addChild('TaxType','1');
			$xmlAmount->addChild('TaxRate','0.05');
			$xmlAmount->addChild('TaxAmount','0');
			$xmlAmount->addChild('TotalAmount',$data[0]['totalamount']);

		}
		
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
		fwrite($f,'UPDATE invlist SET state=1 WHERE invnumber="'.$_POST['invnumber'].'";'.PHP_EOL);
		fclose($f);
		$f=fopen('./file/'.$_POST['company'].'/'.$_POST['dep'].'/sale.txt','a');
		fwrite($f,'UPDATE CST011 SET NBCHKDATE=NULL,NBCHKTIME=NULL,NBCHKNUMBER=NULL WHERE INVOICENUMBER="'.$_POST['invnumber'].'";'.PHP_EOL);
		fclose($f);
		echo "產生完成，請至遠端檢查檔案是否正確，並將檔案轉至客戶端上傳。\n\n請記得將客戶端的invdata發票資料改為非作廢。\n\n其中C0701與C0401兩個檔案需間隔3個小時分別放入noread資料夾中。";
	}
	else{
		echo '查無發票資訊。'.$_POST['machine'];
	}
	sqlclose($conn,'sqlite');
}
else{
	echo '查無發票資訊。ALL';
}
?>