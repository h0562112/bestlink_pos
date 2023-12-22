<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$setup=parse_ini_file('../../../database/setup.ini',true);
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
if(file_exists('../../../database/sale/SALES_'.substr($_POST['bizdate'],0,6).'.db')){
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT INVOICENUMBER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND consecnumber="'.$consecnumber.'"';
	$invnumber=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($invnumber)&&sizeof($invnumber)>0&&isset($invnumber[0]['INVOICENUMBER'])&&strlen(trim($invnumber[0]['INVOICENUMBER']))>0){
		$tttt=1;
	}
	else{
		$tttt=0;
	}
}
else{
	$tttt=0;
}
//echo 'tttt='.$tttt;
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
if($tttt==0){
	if(isset($machinedata['basic']['startoinv'])&&isset($machinedata['basic']['endoinv'])&&substr($machinedata['basic']['startoinv'],0,2)==substr($machinedata['basic']['endoinv'],0,2)&&intval(substr($machinedata['basic']['startoinv'],2))<intval(substr($machinedata['basic']['endoinv'],2))){//可開立二聯式發票
		$oinvnumber=$machinedata['basic']['startoinv'];
		$machinedata['basic']['startoinv']=substr($machinedata['basic']['startoinv'],0,2).str_pad((intval(substr($machinedata['basic']['startoinv'],2))+1),8,'0',STR_PAD_LEFT);
		write_ini_file($machinedata,'../../../database/machinedata.ini');
		
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
		$date=$Y.$month.$day;
		$time=$hour.':'.$min.':'.$sec;
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
			if(file_exists('../../../database/sale/'.$invdate)){
			}
			else{
				mkdir('../../../database/sale/'.$invdate);
			}
			copy("../../../database/sale/EMinvdata.db","../../../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db");
		}
		
		$conn2=sqlconnect("../../../database/sale","SALES_".substr($_POST['bizdate'],0,6).".db","","","","sqlite");
		$sql='UPDATE tempCST011 SET INVOICENUMBER="'.$oinvnumber.'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"';
		sqlnoresponse($conn2,$sql,'sqlite');
		$sql='SELECT *,TAX5 AS invmoney FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'"';
		$list=sqlquery($conn2,$sql,'sqlite');

		$invmoney=$list[0]['invmoney'];

		$datetime=$Y.$month.$day.$hour.$min.$sec;
		$oinv=fopen('../../../print/noread/'.$datetime.'_tempoinv_'.intval($_POST['consecnumber']).'.csv','w');
		$oinvcontent='1,';
		if(strlen($_POST['tempban'])==8){
			$oinvcontent=$oinvcontent.$_POST['tempban'].',';
			$buyerid=$_POST['tempban'];
			$buyername='0000';
		}
		else{
			$oinvcontent=$oinvcontent.',';
			$buyerid='0000000000';
			$buyername='0000000000';
		}
		$oinvcontent=$oinvcontent.$setup['basic']['itemname'].','.$invmoney.','.$oinvnumber.','.$consecnumber;
		fwrite($oinv,$oinvcontent);
		fclose($oinv);
		rename('../../../print/noread/'.$datetime.'_tempoinv_'.intval($_POST['consecnumber']).'.csv','../../../print/noread/'.$datetime.'_oinv_'.intval($_POST['consecnumber']).'.csv');
		
		$conn1=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
		$sql='INSERT INTO invlist (invnumber,createdate,createtime,sellerid,sellername,buyerid,buyname,relatenumber,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount) VALUES ("'.$oinvnumber.'","'.$date.'","'.$time.'","","","'.$buyerid.'","'.$buyername.'","'.$list[0]['CONSECNUMBER'].'","0",NULL,NULL,NULL,"",NULL,"",'.$invmoney.')';
		sqlnoresponse($conn1,$sql,'sqlite');
		$sql='INSERT INTO salelist (listno,invnumber,createdate,createtime,name,qty,unitprice,money,lineno) VALUES ("'.$list[0]['CONSECNUMBER'].'","'.$oinvnumber.'","'.$date.'","'.$time.'","'.$setup['basic']['itemname'].'",1,'.$invmoney.','.$invmoney.',1)';
		sqlnoresponse($conn1,$sql,'sqlite');
		sqlclose($conn1,'sqlite');
		echo $oinvnumber;
	}
	else{
		echo 'oinv number is error.';
	}
}
else{
	echo $invnumber[0]['INVOICENUMBER'];
}
?>