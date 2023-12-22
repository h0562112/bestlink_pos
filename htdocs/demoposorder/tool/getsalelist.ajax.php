<?php
include_once '../../tool/dbTool.inc.php';
$machinedata=parse_ini_file('../../database/machinedata.ini',true);
date_default_timezone_set('Asia/Taipei');
if(isset($_POST['bizdate'])){
	if($_POST['type']=='-'){
		$bizdate=date('Ymd',strtotime($_POST['bizdate'].' -1 day'));
	}
	else{
		$bizdate=date('Ymd',strtotime($_POST['bizdate'].' +1 day'));
	}
}
else{
	$bizdate=$machinedata['basic']['bizdate'];
	
}

if(file_exists('../../database/sale/SALES_'.substr($bizdate,0,6).'.db')){
	$conn=sqlconnect('../../database/sale','SALES_'.substr($bizdate,0,6).'.db','','','','sqlite');
	if(isset($_POST['sale'])&&$_POST['sale']=='sale'){
		$sql='SELECT (SELECT COUNT(*) FROM CST011 WHERE BIZDATE="'.$bizdate.'") AS ttcount,(SELECT SUM(SALESTTLAMT) FROM CST011 WHERE BIZDATE="'.$bizdate.'") AS ttmoney,(SELECT COUNT(*) FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER="Y") AS voidcount,(SELECT SUM(SALESTTLAMT) FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER="Y") AS voidmoney,BIZDATE,CONSECNUMBER,CLKCODE,CLKNAME,INVOICENUMBER,SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME FROM CST011 WHERE BIZDATE="'.$bizdate.'" ORDER BY CREATEDATETIME DESC';
		//$sql='SELECT BIZDATE,CONSECNUMBER,CLKCODE,CLKNAME,INVOICENUMBER,SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME FROM CST011 WHERE BIZDATE="'.$bizdate.'" ORDER BY CREATEDATETIME DESC';
	}
	else{
		$sql='SELECT BIZDATE,CONSECNUMBER,CLKCODE,CLKNAME,INVOICENUMBER,SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER IS NULL ORDER BY CREATEDATETIME DESC';
	}
	$datas=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
else{
}

if(!isset($datas)||sizeof($datas)==0){
	echo "<div style='display:none;'>
			<input type='hidden' id='bizdate' value='".$bizdate."'>
		</div>";
	echo '<div>查無資料。</div>';
	echo '<input type="hidden" name="ttcount" value="0">
		<input type="hidden" name="ttmoney" value="0">
		<input type="hidden" name="voidcount" value="0">
		<input type="hidden" name="voidmoney" value="0">';
}
else{
	echo "<div style='display:none;'>
			<input type='hidden' id='bizdate' value='".$bizdate."'>
		</div>";
	foreach($datas as $d){
		echo "<div class='listitems' style='padding:10px 0;overflow:hidden;border-bottom:1px solid #898989;'><div style='width:13%;float:left;min-height:1px;'>".$d['BIZDATE']."</div><div style='width:10%;float:left;min-height:1px;'>".$d['CONSECNUMBER']."</div><div style='width:calc(100% / 6 - 6px);float:left;min-height:1px;'>".$d['INVOICENUMBER']."</div><div style='width:calc(100% / 6 - 6px);float:left;min-height:1px;'>".$d['SALESTTLAMT']."</div><div style='width:calc(100% / 6 - 6px);float:left;min-height:1px;'>".$d['CLKNAME']."</div><div style='width:20%;float:left;min-height:1px;font-size:15px;'>".$d['CREATEDATETIME']."</div><div style='width:5%;min-height:1px;float:left;'>".$d['NBCHKNUMBER']."</div></div>";
	}
	echo '<input type="hidden" name="ttcount" value="'.$datas[0]['ttcount'].'">
		<input type="hidden" name="ttmoney" value="';if($datas[0]['ttmoney']==null)echo '0';else echo $datas[0]['ttmoney'];echo '">
		<input type="hidden" name="voidcount" value="'.$datas[0]['voidcount'].'">
		<input type="hidden" name="voidmoney" value="';if($datas[0]['voidmoney']==null)echo '0';else echo $datas[0]['voidmoney'];echo '">';
}

?>