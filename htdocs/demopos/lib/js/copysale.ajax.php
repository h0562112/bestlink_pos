<?php
include_once '../../../tool/myerrorlog.php';
srand(date('YmdHis'));
while(file_exists('../../../print/stop.ini')){
	usleep(100000*rand(0,5));//1 seconds = 1000000
}
$f=fopen('../../../print/stop.ini','w');
fclose($f);
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$data=parse_ini_file('../../../database/setup.ini',true);
//date_default_timezone_set('Asia/Taipei');


$machinedata['basic']['consecnumber']=intval($machinedata['basic']['consecnumber'])+1;
$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
$consecnumber=$machinedata['basic']['consecnumber'];
$saleno=$machinedata['basic']['saleno'];
if(intval($machinedata['basic']['saleno'])>=intval($machinedata['basic']['maxsaleno'])){
	if(isset($machinedata['basic']['strsaleno'])){
		$machinedata['basic']['saleno']=$machinedata['basic']['strsaleno'];
	}
	else{
		$machinedata['basic']['saleno']=0;
	}
}
else{
}
write_ini_file($machinedata,'../../../database/machinedata.ini');
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
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

if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){//2021/11/4 修改非當月的帳單，如沒有調整，於暫結單會撈不出來，因為會將紀錄寫在前一個月，但營業日卻是在當下營業日
	$lastconn=sqlconnect("../../../database/sale","SALES_".substr($timeini['time']['bizdate'],0,6).".DB","","","","sqlite");
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
	$conn->exec("ATTACH '".$init['db']['dbfile']."SALES_".substr($timeini['time']['bizdate'],0,6).".db' AS trandb");
}
else{
}
$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND NBCHKNUMBER="Y"';
$voiddata=sqlquery($conn,$sql,'sqlite');
//print_r($voiddata);
$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ((DTLMODE="1" AND DTLTYPE="1" AND DTLFUNC="01") OR (DTLMODE="1" AND DTLTYPE="3" AND DTLFUNC="02" AND ITEMCODE="item")) ORDER BY LINENUMBER ASC';
$voidlist=sqlquery($conn,$sql,'sqlite');
$tempamt=0;
$tempqty=0;
$itemindex=1;
date_default_timezone_set($init['init']['settime']);
$cretime=date('YmdHis');
for($i=0;$i<sizeof($voidlist);$i++){
	if($voidlist[$i]['REMARKS']=='1'){
		if($voiddata[0]['TABLENUMBER']==''){
			if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
				$sql='INSERT INTO trandb.tempCST012 SELECT TERMINALNUMBER,"'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'",LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITQTY,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,TAXCODE1,TAXCODE2,TAXCODE3,TAXCODE4,TAXCODE5,"'.$timeini['time']['zcounter'].'",SUBSTR(REMARKS,-1),"'.$cretime.'" FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$voidlist[$i]['LINENUMBER'].'"';
			}
			else{
				$sql='INSERT INTO tempCST012 SELECT TERMINALNUMBER,"'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'",LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITQTY,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,TAXCODE1,TAXCODE2,TAXCODE3,TAXCODE4,TAXCODE5,"'.$timeini['time']['zcounter'].'",SUBSTR(REMARKS,-1),"'.$cretime.'" FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$voidlist[$i]['LINENUMBER'].'"';
			}
			sqlnoresponse($conn,$sql,'sqlite');
			$tempamt=floatval($tempamt)+floatval($voidlist[$i]['AMT']);
			$tempqty=floatval($tempqty)+floatval($voidlist[$i]['QTY']);
		}
		else{
			if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
				$sql='INSERT INTO trandb.tempCST012 SELECT "'.$voiddata[0]['TABLENUMBER'].'","'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'",LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITQTY,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,TAXCODE1,TAXCODE2,TAXCODE3,TAXCODE4,TAXCODE5,"'.$timeini['time']['zcounter'].'",SUBSTR(REMARKS,-1),"'.$cretime.'" FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$voidlist[$i]['LINENUMBER'].'"';
			}
			else{
				$sql='INSERT INTO tempCST012 SELECT "'.$voiddata[0]['TABLENUMBER'].'","'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'",LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITQTY,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,TAXCODE1,TAXCODE2,TAXCODE3,TAXCODE4,TAXCODE5,"'.$timeini['time']['zcounter'].'",SUBSTR(REMARKS,-1),"'.$cretime.'" FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$voidlist[$i]['LINENUMBER'].'"';
			}
			sqlnoresponse($conn,$sql,'sqlite');
			$tempamt=floatval($tempamt)+floatval($voidlist[$i]['AMT']);
			$tempqty=floatval($tempqty)+floatval($voidlist[$i]['QTY']);
		}
	}
	else{
		if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
			$sql='INSERT INTO trandb.tempCST012 SELECT TERMINALNUMBER,"'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'",LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITQTY,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,TAXCODE1,TAXCODE2,TAXCODE3,TAXCODE4,TAXCODE5,"'.$timeini['time']['zcounter'].'",SUBSTR(REMARKS,-1),"'.$cretime.'" FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$voidlist[$i]['LINENUMBER'].'"';
		}
		else{
			$sql='INSERT INTO tempCST012 SELECT TERMINALNUMBER,"'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'",LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITQTY,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,TAXCODE1,TAXCODE2,TAXCODE3,TAXCODE4,TAXCODE5,"'.$timeini['time']['zcounter'].'",SUBSTR(REMARKS,-1),"'.$cretime.'" FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$voidlist[$i]['LINENUMBER'].'"';
		}
		sqlnoresponse($conn,$sql,'sqlite');
		$tempamt=floatval($tempamt)+floatval($voidlist[$i]['AMT']);
		$tempqty=floatval($tempqty)+floatval($voidlist[$i]['QTY']);
	}
}
if($init['init']['controltable']=='1'){
	if($voidlist[0]['REMARKS']=='1'){
		$ttlnum='';
		if(strstr($voiddata[0]['TABLENUMBER'],',')){
			$tablist=preg_split('/,/',$voiddata[0]['TABLENUMBER']);
			for($i=0;$i<sizeof($tablist);$i++){
				while(true){
					if(file_exists('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$tablist[$i]).'.ini')){
						if(strstr($tablist[$i],'-')){
							$num=preg_split('/-/',$tablist[$i]);
							$tablist[$i]=$num[0].'-'.(intval($num[1])+1);
						}
						else{
							$tablist[$i]=$tablist[$i].'-1';
						}
					}
					else{
						if($ttlnum==''){
							$ttlnum=$tablist[$i];
						}
						else{
							$ttlnum=$ttlnum.','.$tablist[$i];
						}
						break;
					}
				}
			}
			foreach($tablist as $tl){
				$fileini=fopen('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$tl).'.ini','a');
				fwrite($fileini,'['.$tl.']'.PHP_EOL);
				fwrite($fileini,'bizdate="'.$timeini['time']['bizdate'].'"'.PHP_EOL);
				fwrite($fileini,'zcounter="'.$timeini['time']['zcounter'].'"'.PHP_EOL);
				fwrite($fileini,'consecnumber="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'"'.PHP_EOL);
				fwrite($fileini,'saleamt="'.$voiddata[0]['SALESTTLAMT'].'"'.PHP_EOL);
				fwrite($fileini,'person="'.(intval($voiddata[0]['TAX6'])+intval($voiddata[0]['TAX7'])+intval($voiddata[0]['TAX8'])).'"'.PHP_EOL);
				fwrite($fileini,'createdatetime="'.$cretime.'"'.PHP_EOL);
				fwrite($fileini,'table="'.$ttlnum.'"'.PHP_EOL);
				fwrite($fileini,'tablestate="1"'.PHP_EOL);
				fclose($fileini);
			}
		}
		else{
			while(true){
				if(file_exists('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$voiddata[0]['TABLENUMBER']).'.ini')){
					if(strstr($voiddata[0]['TABLENUMBER'],'-')){
						$num=preg_split('/-/',$voiddata[0]['TABLENUMBER']);
						$voiddata[0]['TABLENUMBER']=$num[0].'-'.(intval($num[1])+1);
					}
					else{
						$voiddata[0]['TABLENUMBER']=$voiddata[0]['TABLENUMBER'].'-1';
					}
				}
				else{
					$fileini=fopen('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$voiddata[0]['TABLENUMBER']).'.ini','a');
					fwrite($fileini,'['.$voiddata[0]['TABLENUMBER'].']'.PHP_EOL);
					fwrite($fileini,'bizdate="'.$timeini['time']['bizdate'].'"'.PHP_EOL);
					fwrite($fileini,'zcounter="'.$timeini['time']['zcounter'].'"'.PHP_EOL);
					fwrite($fileini,'consecnumber="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'"'.PHP_EOL);
					fwrite($fileini,'saleamt="'.$voiddata[0]['SALESTTLAMT'].'"'.PHP_EOL);
					fwrite($fileini,'person="'.(intval($voiddata[0]['TAX6'])+intval($voiddata[0]['TAX7'])+intval($voiddata[0]['TAX8'])).'"'.PHP_EOL);
					fwrite($fileini,'createdatetime="'.$cretime.'"'.PHP_EOL);
					fwrite($fileini,'table="'.$voiddata[0]['TABLENUMBER'].'"'.PHP_EOL);
					fwrite($fileini,'tablestate="0"'.PHP_EOL);
					fclose($fileini);
					$ttlnum=$voiddata[0]['TABLENUMBER'];
					break;
				}
			}
		}
	}
	else if($voidlist[0]['REMARKS']=='2'){
		$fileini=fopen('../../table/outside/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',str_pad($consecnumber,6,'0',STR_PAD_LEFT)).'.ini','a');
		fclose($fileini);
		$ttlnum=$voiddata[0]['TABLENUMBER'];
	}
	else{
		$ttlnum=$voiddata[0]['TABLENUMBER'];
	}
}
else{
	$ttlnum=$voiddata[0]['TABLENUMBER'];
}
if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$sql='INSERT INTO trandb.tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,SALESTTLQTY,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TAX10,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,TABLENUMBER,RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,CREDITCARD) SELECT TERMINALNUMBER,"'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'"," ",INVOICEDATE,INVOICETIME,OPENCLKCODE,"'.$_POST['code'].'","'.$_POST['name'].'",REGMODE,REGTYPE,REGFUNC,'.$tempqty.','.$tempamt.',0,0,0,0,TAX5,TAX6,TAX7,TAX8,0,TAX10,0,0,0,0,0,0,0,0,0,0,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NULL,NULL,NULL,"'.$ttlnum.'",RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,"'.$timeini['time']['zcounter'].'",SUBSTR(REMARKS,-1),"'.$cretime.'",UPDATEDATETIME,NULL FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND NBCHKNUMBER="Y"';
}
else{
	$sql='INSERT INTO tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,SALESTTLQTY,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TAX10,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,TABLENUMBER,RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,CREDITCARD) SELECT TERMINALNUMBER,"'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'"," ",INVOICEDATE,INVOICETIME,OPENCLKCODE,"'.$_POST['code'].'","'.$_POST['name'].'",REGMODE,REGTYPE,REGFUNC,'.$tempqty.','.$tempamt.',0,0,0,0,TAX5,TAX6,TAX7,TAX8,0,TAX10,0,0,0,0,0,0,0,0,0,0,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NULL,NULL,NULL,"'.$ttlnum.'",RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,"'.$timeini['time']['zcounter'].'",SUBSTR(REMARKS,-1),"'.$cretime.'",UPDATEDATETIME,NULL FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND NBCHKNUMBER="Y"';
}
sqlnoresponse($conn,$sql,'sqlite');
if(substr($timeini['time']['bizdate'],0,6)!=substr($_POST['bizdate'],0,6)){
	$salenomap='INSERT INTO trandb.salemap (bizdate,consecnumber,saleno) VALUES ("'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'","'.$saleno.'")';
}
else{
	$salenomap='INSERT INTO salemap (bizdate,consecnumber,saleno) VALUES ("'.$timeini['time']['bizdate'].'","'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'","'.$saleno.'")';
}
sqlnoresponse($conn,$salenomap,'sqlite');
sqlclose($conn,'sqlite');

/*if(isset($_POST['memno'])&&strlen($_POST['memno'])!=0){
	$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
	$sql='SELECT * FROM person WHERE memno='.$_POST['memno'].' AND state=1';
	$memdata=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
else{
}*/
echo $voiddata[0]['REMARKS'].'-'.$timeini['time']['bizdate'].'-'.str_pad($consecnumber,6,'0',STR_PAD_LEFT);
if(file_exists('../../../print/stop.ini')){
	unlink('../../../print/stop.ini');
}
else{
}
?>