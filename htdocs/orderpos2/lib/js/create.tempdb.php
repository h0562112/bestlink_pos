<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';

$conn=sqlconnect('localhost',$_POST['story'],'orderuser','0424732003','utf-8','mysql');
$sql='SELECT CONSECNUMBER FROM tempcst011 WHERE TERMINALNUMBER="'.$_POST['dep'].'" ORDER BY CONSECNUMBER DESC LIMIT 1';
$s=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
if(isset($s[0])&&$s[0]['CONSECNUMBER']!=null){
	$consecnumber=intval($s[0]['CONSECNUMBER'])+1;
}
else{
	$consecnumber='1';
}

$consecnumber=str_pad($consecnumber,6,'0',STR_PAD_LEFT);
echo $consecnumber;
$data=parse_ini_file('../../../database/setup.ini',true);
//$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(file_exists('../../../ourpos/'.$_POST['story'].'/'.$_POST['dep'].'/initsetting.ini')){
	$content=parse_ini_file('../../../ourpos/'.$_POST['story'].'/'.$_POST['dep'].'/initsetting.ini',true);
}
else{
	$content=parse_ini_file('../../../database/initsetting.ini',true);
}
$buttons=parse_ini_file('../../../demopos/syspram/buttons-'.$content['init']['firlan'].'.ini',true);
$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
//$pti=parse_ini_file('../../../database/itemprinttype.ini',true);

if(isset($_POST['memno'])&&strlen($_POST['memno'])!=0){
	$conn=sqlconnect('localhost',$_POST['story'],'orderuser','0424732003','utf-8','mysql');
	$sql='SELECT * FROM member WHERE memno="'.$_POST['memno'].'" AND state=1';
	//echo $sql;
	$memdata=sqlquery($conn,$sql,'mysql');
	//print_r($memdata);
	sqlclose($conn,'mysql');
}
else{
}

//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);
$datetime=date('YmdHis');

if(isset($_POST['no'])){
	$totalqty=0;
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$totalqty=intval($totalqty)+intval($_POST['number'][$i]);
	}
}
else{
}

$insertdate=$datetime;

$conn=sqlconnect('localhost',$_POST['story'],'orderuser','0424732003','urf-8','mysql');

$saleinvdata='';

/*提前至產生clientlist之前，以利產生退菜單*/
/*退餐單SQL步驟*/
/*新舊資料比對*/
/**/
$handle=fopen('../../'.$_POST['story'].'tempdb.log.txt','a');
fwrite($handle,date('Y/m/d H:i:s').' -- '.$_POST['dep'].' -- CST012 - '.$consecnumber.PHP_EOL);
$sql='SELECT LINENUMBER FROM tempcst012 WHERE CONSECNUMBER="'.$consecnumber.'" ORDER BY LINENUMBER DESC LIMIT 1';
$indarray=sqlquery($conn,$sql,'mysql');
if(sizeof($indarray)==0){
	$index=1;
}
else{
	$index=intval($indarray[0]['LINENUMBER'])+1;
}

$sql='';
$totalqty=0;
$totalamt=0;
if(isset($_POST['itemno'])){
	for($i=0;$i<sizeof($_POST['itemno']);$i++){
		$sql=$sql."INSERT INTO tempcst012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
		$values='("'.$_POST['dep'].'","'.date('Ymd').'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'","web","網路訂購","1","1","01","'.str_pad($_POST['itemno'][$i],16,'0',STR_PAD_LEFT).'","'.$_POST['itemname'][$i].'","'.str_pad($_POST['typeno'][$i],6,'0',STR_PAD_LEFT).'","'.str_pad($_POST['seq'][$i],2,'0',STR_PAD_LEFT).'","'.str_pad($_POST['typeno'][$i],6,'0',STR_PAD_LEFT).'",';
		$temptasteno=preg_split('/,/',$_POST['tasteno'][$i]);
		$temptastenumber=preg_split('/,/',$_POST['tastenumber'][$i]);
		for($j=0;$j<10;$j++){
			if(isset($temptasteno[$j])&&strlen($temptasteno[$j])>0){
				$values=$values.'"'.str_pad($temptasteno[$j].(intval($temptastenumber[$j])%10),6,'0',STR_PAD_LEFT).'",';
			}
			else{
				$values=$values.'NULL,';
			}
		}
		$values=$values.'"'.$_POST['unitpricelink'][$i].'",0,'.$_POST['qty'][$i].',';
		if($_POST['unitprice'][$i]==''||strlen(trim($_POST['unitprice'][$i]))==0){
			$values=$values.'0';
		}
		else{
			$values=$values.$_POST['unitprice'][$i];
		}
		$values=$values.','.$_POST['amt'][$i].',"web","';
		if(isset($_POST['listtype'])&&$_POST['listtype']!=''){
			$values=$values.$_POST['listtype'];
		}
		else{
			$values=$values.'2';
		}
		$values=$values.'","'.$insertdate.'")';
		$totalqty=floatval($totalqty)+floatval($_POST['qty'][$i]);
		$totalamt=floatval($totalamt)+floatval($_POST['amt'][$i]);

		$index++;

		$values=$values.',("'.$_POST['dep'].'","'.date('Ymd').'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'","web","網路訂購","1","3","02","item","單品優惠","0","","0",';

		for($j=0;$j<10;$j++){
			$values=$values.'NULL,';
		}

		$values=$values.'NULL,0,0,0,0,"web","';
		if(isset($_POST['listtype'])&&$_POST['listtype']!=''){
			$values=$values.$_POST['listtype'];
		}
		else{
			$values=$values.'2';
		}
		$values=$values.'","'.$insertdate.'")';
		$index++;
		fwrite($handle,date('Y/m/d H:i:s').' -- '.$values.PHP_EOL);
		$sql=$sql.$values.';;;';
	
	}
}
else{
}

//echo $sql;
//sqlnoresponse($conn,$sql,'sqliteexec');
fwrite($handle,date('Y/m/d H:i:s').' -- '.$_POST['dep'].' -- CST011 - '.$consecnumber.PHP_EOL);

$sqlselect='SELECT COUNT(*) AS num FROM tempcst011 WHERE CONSECNUMBER="'.$consecnumber.'"';
$test=sqlquery($conn,$sqlselect,'mysql');

$sql=$sql.'INSERT INTO tempcst011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLQTY,SALESTTLAMT,REMARKS,ZCOUNTER,CREATEDATETIME,TAX1,TAX2,TAX5,TAX6,TAX7,TAX8,TAX9,CUSTCODE,CUSTNAME) VALUES ("'.$_POST['dep'].'","'.date('Ymd').'","'.$consecnumber.'","'.$saleinvdata.'","web","網路訂購",'.$totalqty.','.$totalamt.',"';
if(isset($_POST['listtype'])&&$_POST['listtype']!=''){
	$sql=$sql.$_POST['listtype'];
}
else{
	$sql=$sql.'2';
}
$sql=$sql.'","web","'.$insertdate.'",0,'.$totalamt.',0,0,0,0,0,"'.$memdata[0]['memno'].';-;'.$memdata[0]['tel'].'","'.$memdata[0]['name'].'");;;';

fwrite($handle,date('Y/m/d H:i:s').' -- '.$sql.PHP_EOL);
fclose($handle);
sqlnoresponse($conn,$sql,'mysqlexec');
sqlclose($conn,'mysql');
$f=fopen('../../../print/noread/report.txt','w');
fclose($f);
?>