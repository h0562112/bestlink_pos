<?php
include_once '../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
if(file_exists('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini')){
	$buttons=parse_ini_file('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
}
else if(file_exists('../../syspram/buttons-zh-TW.ini')){
	$buttons=parse_ini_file('../../syspram/buttons-zh-TW.ini',true);
}
else{
	$buttons=parse_ini_file('../../syspram/buttons-1.ini',true);
}
date_default_timezone_set($initsetting['init']['settime']);
$conn=sqlconnect('../../../database/sale','SALES_'.date('Ym').'.db','','','','sqlite');
$sql='SELECT *,salemap.saleno as saleno FROM CST011 JOIN salemap ON salemap.consecnumber=CST011.CONSECNUMBER WHERE INVOICENUMBER="'.$_POST['invno'].'" AND NBCHKNUMBER IS NULL UNION SELECT *,salemap.saleno as saleno FROM tempCST011 JOIN salemap ON salemap.consecnumber=tempCST011.CONSECNUMBER WHERE INVOICENUMBER="'.$_POST['invno'].'" AND NBCHKNUMBER IS NULL';
$list=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(sizeof($list)==0){
	if(date('m')%2==0){
		$conn=sqlconnect('../../../database/sale','SALES_'.date('Ym',strtotime(date('Y/m/d').' -1 month')).'.db','','','','sqlite');
		$sql='SELECT *,salemap.saleno as saleno FROM CST011 JOIN salemap ON salemap.consecnumber=CST011.CONSECNUMBER WHERE INVOICENUMBER="'.$_POST['invno'].'" AND NBCHKNUMBER IS NULL UNION SELECT *,salemap.saleno as saleno FROM tempCST011 JOIN salemap ON salemap.consecnumber=tempCST011.CONSECNUMBER WHERE INVOICENUMBER="'.$_POST['invno'].'" AND NBCHKNUMBER IS NULL';
		$list=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
	}
	else{
	}
}
else{
}
if(sizeof($list)==0){
	echo json_encode(['empty']);
}
else{
	if(date('m')%2==0){
		$conn=sqlconnect('../../../database/sale/'.date('Ym'),'invdata_'.date('Ym').'_'.$list[0]['TERMINALNUMBER'].'.db','','','','sqlite');
	}
	else{
		$conn=sqlconnect('../../../database/sale/'.date('Ym',strtotime(date('Y/m/d').' +1 month')),'invdata_'.date('Ym',strtotime(date('Y/m/d').' +1 month')).'_'.$list[0]['TERMINALNUMBER'].'.db','','','','sqlite');
	}
	$sql='SELECT * FROM invlist WHERE invnumber="'.$_POST['invno'].'"';
	$invdata=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$res=array();
	$res[0] = '<input type="hidden" id="bizdate" value="'.$list[0]['BIZDATE'].'">
				<input type="hidden" id="machinename" value="'.$list[0]['TERMINALNUMBER'].'">
				<input type="hidden" id="credate" value="'.substr($list[0]['CREATEDATETIME'],0,8).'">
				<input type="hidden" id="consecnumber" value="'.$list[0]['CONSECNUMBER'].'">
				<input type="hidden" id="invoicenumber" value="'.$list[0]['INVOICENUMBER'].'">
				<input type="hidden" id="memno" value="'.$list[0]['CUSTCODE'].'">
			<table>
				<tr>
					<td style="width:50%;text-align:center;padding:5px 0;">發票號碼</td>
					<td style="width:50%;">'.$list[0]['INVOICENUMBER'].'</td>
				</tr>
				<tr>
					<td style="width:50%;text-align:center;padding:5px 0;">開立時間</td>
					<td style="width:50%;">'.substr($invdata[0]['createdate'],0,4).'/'.substr($invdata[0]['createdate'],4,2).'/'.substr($invdata[0]['createdate'],6,2).' '.$invdata[0]['createtime'].'</td>
				</tr>
				<tr>
					<td style="width:50%;text-align:center;padding:5px 0;">開立金額</td>
					<td style="width:50%;">'.$invdata[0]['totalamount'].'</td>
				</tr>
				<tr>
					<td style="width:50%;text-align:center;padding:5px 0;">營業日期</td>
					<td style="width:50%;">'.$list[0]['BIZDATE'].'</td>
				</tr>
				<tr>
					<td style="width:50%;text-align:center;padding:5px 0;">消費類別</td>
					<td style="width:50%;">'.$buttons['name']['listtype'.substr($list[0]['REMARKS'],0,1)].$list[0]['saleno'].'</td>
				</tr>
				<tr>
					<td style="width:50%;text-align:center;padding:5px 0;">帳單號碼</td>
					<td style="width:50%;">'.$list[0]['CONSECNUMBER'].'</td>
				</tr>
				<tr>
					<td style="width:50%;text-align:center;padding:5px 0;">帳單金額</td>
					<td style="width:50%;">'.$list[0]['SALESTTLAMT'].'</td>
				</tr>
				<tr>
					<td style="width:50%;text-align:center;padding:5px 0;">會員名稱</td>
					<td style="width:50%;">'.$list[0]['CUSTNAME'].'</td>
				</tr>
			</table>';
	echo json_encode($res);
}
?>