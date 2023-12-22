<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//print_r($_POST);
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT COUNT(*) AS num,tempCST011.TABLENUMBER,tempCST012.ZCOUNTER,tempCST012.AMT FROM tempCST012 JOIN tempCST011 ON tempCST011.BIZDATE=tempCST012.BIZDATE AND tempCST011.CONSECNUMBER=tempCST012.CONSECNUMBER WHERE tempCST012.BIZDATE="'.$_POST['bizdate'].'" AND tempCST012.CONSECNUMBER="'.$_POST['consecnumber'].'" AND tempCST012.ITEMCODE="autodis"';
//echo $sql;
$num=sqlquery($conn,$sql,'sqlite');
if(strlen($_POST['autodiscontent'])>0){
	if(sizeof($num)>0&&isset($num[0]['num'])&&floatval($num[0]['num'])>0){
		$sql='';
		$sql='UPDATE tempCST011 SET SALESTTLAMT=((SELECT SALESTTLAMT FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'")-(SELECT AMT FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis")-'.$_POST['autodis'].') WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'";';
		//sqlnoresponse($conn,$sql,'sqlite');
		//echo $sql;
		$sql=$sql.'DELETE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis";';
		$sql=$sql.'INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) SELECT TERMINALNUMBER,BIZDATE,CONSECNUMBER,substr("000"||(CAST(LINENUMBER AS integer)+1),-3),CLKCODE,CLKNAME,"1","3","02","autodis","自動優惠","'.$_POST['autodiscontent'].'","'.$_POST['autodispermoney'].'","","",-'.$_POST['autodis'].',ZCOUNTER,REMARKS,CREATEDATETIME FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER DESC LIMIT 1;';
		sqlnoresponse($conn,$sql,'sqliteexec');
		//echo $sql;
		if($init['init']['controltable']==1){
			include_once '../../../tool/inilib.php';
			$tablist=preg_split('/,/',$num[0]['TABLENUMBER']);
			foreach($tablist as $tl){
				if(file_exists('../../table/'.$_POST['bizdate'].';'.$num[0]['ZCOUNTER'].';'.$tl.'.ini')){
					$tabdata=parse_ini_file('../../table/'.$_POST['bizdate'].';'.$num[0]['ZCOUNTER'].';'.$tl.'.ini',true);
					$tabdata[$tl]['saleamt']=floatval($tabdata[$tl]['saleamt'])-floatval($num[0]['AMT'])-floatval($_POST['autodis']);
					write_ini_file($tabdata,'../../table/'.$_POST['bizdate'].';'.$num[0]['ZCOUNTER'].';'.$tl.'.ini');
				}
				else{
				}
			}
		}
		else{
		}
	}
	else{
		$sql='SELECT TABLENUMBER,ZCOUNTER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
		$num=sqlquery($conn,$sql,'sqlite');
		$sql='UPDATE tempCST011 SET SALESTTLAMT=((SELECT SALESTTLAMT FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'")-'.$_POST['autodis'].') WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'";';
		//sqlnoresponse($conn,$sql,'sqlite');
		//echo $sql;
		$sql=$sql.'INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) SELECT TERMINALNUMBER,BIZDATE,CONSECNUMBER,substr("000"||(CAST(LINENUMBER AS integer)+1),-3),CLKCODE,CLKNAME,"1","3","02","autodis","自動優惠","'.$_POST['autodiscontent'].'","'.$_POST['autodispermoney'].'","","",-'.$_POST['autodis'].',ZCOUNTER,REMARKS,CREATEDATETIME FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER DESC LIMIT 1;';
		sqlnoresponse($conn,$sql,'sqliteexec');
		//echo $sql;
		if($init['init']['controltable']==1){
			include_once '../../../tool/inilib.php';
			$tablist=preg_split('/,/',$num[0]['TABLENUMBER']);
			foreach($tablist as $tl){
				if(file_exists('../../table/'.$_POST['bizdate'].';'.$num[0]['ZCOUNTER'].';'.$tl.'.ini')){
					$tabdata=parse_ini_file('../../table/'.$_POST['bizdate'].';'.$num[0]['ZCOUNTER'].';'.$tl.'.ini',true);
					$tabdata[$tl]['saleamt']=floatval($tabdata[$tl]['saleamt'])-floatval($_POST['autodis']);
					write_ini_file($tabdata,'../../table/'.$_POST['bizdate'].';'.$num[0]['ZCOUNTER'].';'.$tl.'.ini');
				}
				else{
				}
			}
		}
		else{
		}
	}
}
else{
	$sql='DELETE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis";';
	sqlnoresponse($conn,$sql,'sqliteexec');
}
sqlclose($conn,'sqlite');
?>