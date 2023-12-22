<?php
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';
include_once '../tool/inilib.php';
$init=parse_ini_file('../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machine']])){
		$invmachine=$dbmapping['map'][$_POST['machine']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}

if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../database/machinedata.ini',true);
$conn=sqlconnect('../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT (SELECT CONSECNUMBER FROM CST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) AS one,(SELECT CONSECNUMBER FROM tempCST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) AS two';
$s=sqlquery($conn,$sql,'sqlite');
//2021/10/18 查詢網路訂單的編號
$sql='SELECT (SELECT SUBSTR(CONSECNUMBER,2) FROM CST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS one,(SELECT SUBSTR(CONSECNUMBER,2) FROM tempCST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS two';
$w=sqlquery($conn,$sql,'sqlite');
if($s[0]['one']==null){
	$s[0]['one']=$w[0]['one'];
}
else{
	if(floatval($s[0]['one'])<floatval($w[0]['one'])){
		$s[0]['one']=$w[0]['one'];
	}
	else{
	}
}
if($s[0]['two']==null){
	$s[0]['two']=$w[0]['two'];
}
else{
	if(floatval($s[0]['two'])<floatval($w[0]['two'])){
		$s[0]['two']=$w[0]['two'];
	}
	else{
	}
}

if($s[0]['one']==null&&$s[0]['two']==null){
	$machinedata['basic']['consecnumber']='1';
}
else if($s[0]['one']==null){
	$machinedata['basic']['consecnumber']=intval($s[0]['two'])+1;
}
else if($s[0]['two']==null){
	$machinedata['basic']['consecnumber']=intval($s[0]['one'])+1;
}
else{
	if(intval($s[0]['one'])>intval($s[0]['two'])){
		$machinedata['basic']['consecnumber']=intval($s[0]['one'])+1;
	}
	else{
		$machinedata['basic']['consecnumber']=intval($s[0]['two'])+1;
	}
}
write_ini_file($machinedata,'../database/machinedata.ini');
$_POST['consecnumbers']=preg_replace('/,/','","',$_POST['consecnumbers']);

$temptab=preg_split('/,/',$_POST['tabini']);
$maintab=parse_ini_file('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$temptab[0]).'.ini',true);
$maintab[$temptab[0]]['consecnumber']=str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT);
if($maintab[$temptab[0]]['tablestate']=="1"){
	$sectemptab=preg_split('/,/',$maintab[$temptab[0]]['table']);
	for($i=0;$i<sizeof($sectemptab);$i++){
		if($sectemptab[$i]==$temptab[0]){
		}
		else{
			//echo '1./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$sectemptab[$i].'.ini';
			unlink('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$sectemptab[$i]).'.ini');
		}
	}
}
else{
}
$maintab[$temptab[0]]['table']=$temptab[0];
$maintab[$temptab[0]]['tablestate']="0";
for($i=1;$i<sizeof($temptab);$i++){
	if(file_exists('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$temptab[$i]).'.ini')){
		//echo './table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$temptab[$i].'.ini';
		$sectab=parse_ini_file('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$temptab[$i]).'.ini',true);
		$maintab[$temptab[0]]['person']=floatval($maintab[$temptab[0]]['person'])+floatval($sectab[$temptab[$i]]['person']);
		$maintab[$temptab[0]]['saleamt']=floatval($maintab[$temptab[0]]['saleamt'])+floatval($sectab[$temptab[$i]]['saleamt']);
		if($sectab[$temptab[$i]]['tablestate']=="1"){
			$sectemptab=preg_split('/,/',$sectab[$temptab[$i]]['table']);
			/*for($j=1;$j<sizeof($sectemptab);$j++){
				echo '2./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$sectemptab[$j].'.ini';
				unlink('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$sectemptab[$j].'.ini');
			}*/
		}
		else{
		}
		//echo '3./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$temptab[$i].'.ini';
		unlink('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$temptab[$i]).'.ini');
	}
	else{
	}
}
$maintab[$temptab[0]]['state']="999";
$maintab[$temptab[0]]['machine']=$_POST['machine'];
write_ini_file($maintab,'./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$temptab[0]).'.ini');

$sql='INSERT INTO tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,SALESTTLQTY,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TAX10,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,TABLENUMBER,RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,CREDITCARD) SELECT TERMINALNUMBER,BIZDATE,"'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'",INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,"'.$_POST['usercode'].'","'.$_POST['username'].'",REGMODE,REGTYPE,REGFUNC,SUM(SALESTTLQTY),SUM(SALESTTLAMT),SUM(TAX1),SUM(TAX2),SUM(TAX3),SUM(TAX4),SUM(TAX5),SUM(TAX6),SUM(TAX7),SUM(TAX8),TAX9,TAX10,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,"'.$temptab[0].'",RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,"'.date('YmdHis').'",UPDATEDATETIME,NULL FROM tempCST011 WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'")';
sqlnoresponse($conn,$sql,'sqlite');

if(isset($init['init']['openchar'])&&$init['init']['openchar']=='1'&&isset($init['init']['charge'])&&$init['init']['charge']=='1'&&isset($init['init']['chargenumber'])&&isset($init['init']['chargeeq'])){
	if($init['init']['chargeeq']=='1'){
		$sql='SELECT SALESTTLAMT FROM tempCST011 WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'")';
		$amt=sqlquery($conn,$sql,'sqlite');
	}
	else{
		$sql='SELECT SUM(AMT) AS SALESTTLAMT FROM tempCST012 WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'") AND DTLMODE="1" AND DTLTYPE="1" AND DTLFUNC="01"';
		$amt=sqlquery($conn,$sql,'sqlite');
	}
	if($init['init']['accuracytype']=='1'){
		$tax1=round(floatval($amt[0]['SALESTTLAMT'])*intval($init['init']['chargenumber'])/100,$init['init']['accuracy']);
	}
	else if($init['init']['accuracytype']=='2'){
		$tax1=ceil(floatval($amt[0]['SALESTTLAMT'])*intval($init['init']['chargenumber'])/100,$init['init']['accuracy']);
	}
	else{
		$tax1=floor(floatval($amt[0]['SALESTTLAMT'])*intval($init['init']['chargenumber'])/100,$init['init']['accuracy']);
	}
	$sql='UPDATE tempCST011 SET TAX1='.$tax1.' WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER="'.$machinedata['basic']['consecnumber'].'"';
	sqlnoresponse($conn,$sql,'sqlite');
}
else{
}
if($init['init']['controltable']=='1'){
	$sql='UPDATE tempCST011 SET NBCHKDATE="'.date('YmdHis').'",NBCHKTIME="'.$_POST['usercode'].'",NBCHKNUMBER="合併至'.$temptab[0].'('.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).')" WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'");';
}
else{
	$sql='UPDATE tempCST011 SET NBCHKDATE="'.date('YmdHis').'",NBCHKTIME="'.$_POST['usercode'].'",NBCHKNUMBER="合併至'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'" WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'");';
}
//sqlnoresponse($conn,$sql,'sqlite');
$sql=$sql.'INSERT INTO CST011 SELECT * FROM tempCST011 WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'");';
//sqlnoresponse($conn,$sql,'sqlite');
$sql=$sql.'DELETE FROM tempCST011 WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'");';
//sqlnoresponse($conn,$sql,'sqlite');
//CST011處理完成

$sql=$sql.'INSERT INTO tempCST012 SELECT TERMINALNUMBER,BIZDATE,"'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'",(SELECT substr("000"||(COUNT(*)+1),-3,3) FROM tempCST012 WHERE CAST(rowid AS INT)<CAST(t.rowid AS INT) AND BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'")) AS LINENUMBER,"'.$_POST['usercode'].'","'.$_POST['username'].'",DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITQTY,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,TAXCODE1,TAXCODE2,TAXCODE3,TAXCODE4,TAXCODE5,ZCOUNTER,REMARKS,"'.date('YmdHis').'" FROM tempCST012 AS t WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'");INSERT INTO CST012 SELECT * FROM tempCST012 WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'");';
//echo $sql;
//sqlnoresponse($conn,$sql,'sqliteexec');
//$sql='';
//sqlnoresponse($conn,$sql,'sqlite');
$sql=$sql.'DELETE FROM tempCST012 WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER IN ("'.$_POST['consecnumbers'].'");';
sqlnoresponse($conn,$sql,'sqliteexec');
sqlclose($conn,'sqlite');
echo $timeini['time']['bizdate'].','.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT);
?>