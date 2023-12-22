<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
if(file_exists('../../../database/otherpay.ini')){
	$temp=parse_ini_file('../../../database/otherpay.ini',true);
	$otherpaydata=array();
	foreach($temp as $i=>$v){
		if($i=='pay'||(!isset($v['location'])||$v['location']=='CST011')||(isset($v['fromdb'])&&$v['fromdb']=='member')){
		}
		else{
			array_push($otherpaydata,$v['location']);
		}
	}
}
else{
}
$conn=sqlconnect('../../../database/sale','Cover.db','','','','sqlite');
$sql='SELECT * FROM list WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'"';
$list=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT saleno FROM salemap WHERE consecnumber="'.$_POST['consecnumber'].'"';
$saleno=sqlquery($conn,$sql,'sqlite');
if(sizeof($list)>0){
	$conn=sqlconnect('../../../database/sale','Cover.db','','','','sqlite');
	$selectsql='PRAGMA table_info(list)';
	$column=sqlquery($conn,$selectsql,'sqlite');
	$columnname=array_column($column,'name');
	if(in_array('intella',$columnname)){
	}
	else{
		$insertsql='ALTER TABLE list ADD COLUMN intella TEXT';
		sqlnoresponse($conn,$insertsql,'sqlite');
	}
	if(in_array('nidin',$columnname)){
	}
	else{
		$insertsql='ALTER TABLE list ADD COLUMN nidin TEXT';
		sqlnoresponse($conn,$insertsql,'sqlite');
	}
	$sql='SELECT (salesttlamt+tax1) AS AMT,tax2 AS TAX2,tax3 AS TAX3,tax4 AS TAX4,tax9 AS TAX9,ta1 AS TA1,ta2 AS TA2,ta3 AS TA3,ta4 AS TA4,ta5 AS TA5,ta6 AS TA6,ta7 AS TA7,ta8 AS TA8,ta9 AS TA9,ta10 AS TA10,nontax AS NONTAX,intella,nidin';
	if(isset($otherpaydata[0])){
		for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
			$sql=$sql.','.strtolower($otherpaydata[$rowindex]).' AS '.strtolower($otherpaydata[$rowindex]);
		}
	}
	else{
	}
	$sql=$sql.' FROM list WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'"';
	$amt=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	echo $amt[0]['AMT'].'－'.$amt[0]['TAX2'].'－'.$amt[0]['TAX3'].'－'.$amt[0]['TAX4'].'－'.$amt[0]['TAX9'].'－TA1:'.$amt[0]['TA1'].'－TA2:'.$amt[0]['TA2'].'－TA3:'.$amt[0]['TA3'].'－TA4:'.$amt[0]['TA4'].'－TA5:'.$amt[0]['TA5'].'－TA6:'.$amt[0]['TA6'].'－TA7:'.$amt[0]['TA7'].'－TA8:'.$amt[0]['TA8'].'－TA9:'.$amt[0]['TA9'].'－TA10:'.$amt[0]['TA10'];
	if(isset($otherpaydata[0])){
		for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
			echo '－'.strtolower($otherpaydata[$rowindex]).':'.$amt[0][strtolower($otherpaydata[$rowindex])];
		}
	}
	else{
	}
	echo '－intella:'.preg_replace('/:/',',',$amt[0]['intella']).'－nidin:';
	$nidinpaytype=preg_split('/-/',$amt[0]['nidin']);
	if(sizeof($nidinpaytype)>3){
		$t=$nidinpaytype[0].','.$nidinpaytype[1];
		for($i=2;$i<sizeof($nidinpaytype)-1;$i++){
			$t.='-'.$nidinpaytype[$i];
		}
		$t.=','.$nidinpaytype[(sizeof($nidinpaytype)-1)];
		echo preg_replace('/:/',',',$t);
	}
	else{
		echo preg_replace('/:/',',',preg_replace('/-/',',',$amt[0]['nidin']));
	}
	echo '－'.$amt[0]['NONTAX'].'－1－';
}
else{
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
	$sql='SELECT (SALESTTLAMT+TAX1) AS AMT,TAX2,TAX3,TAX4,TAX9,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,NONTAX,intella,nidin';
	if(isset($otherpaydata[0])){
		for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
			$sql=$sql.','.strtolower($otherpaydata[$rowindex]);
		}
	}
	else{
	}
	$sql=$sql.' FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
	$amt=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	echo $amt[0]['AMT'].'－'.$amt[0]['TAX2'].'－'.$amt[0]['TAX3'].'－'.$amt[0]['TAX4'].'－'.$amt[0]['TAX9'].'－TA1:'.$amt[0]['TA1'].'－TA2:'.$amt[0]['TA2'].'－TA3:'.$amt[0]['TA3'].'－TA4:'.$amt[0]['TA4'].'－TA5:'.$amt[0]['TA5'].'－TA6:'.$amt[0]['TA6'].'－TA7:'.$amt[0]['TA7'].'－TA8:'.$amt[0]['TA8'].'－TA9:'.$amt[0]['TA9'].'－TA10:'.$amt[0]['TA10'];
	if(isset($otherpaydata[0])){
		for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
			echo '－'.strtolower($otherpaydata[$rowindex]).':'.$amt[0][strtolower($otherpaydata[$rowindex])];
		}
	}
	else{
	}
	echo '－intella:'.preg_replace('/:/',',',$amt[0]['intella']).'－nidin:';
	$nidinpaytype=preg_split('/-/',$amt[0]['nidin']);
	if(sizeof($nidinpaytype)>3){
		$t=$nidinpaytype[0].','.$nidinpaytype[1];
		for($i=2;$i<sizeof($nidinpaytype)-1;$i++){
			$t.='-'.$nidinpaytype[$i];
		}
		$t.=','.$nidinpaytype[(sizeof($nidinpaytype)-1)];
		echo preg_replace('/:/',',',$t);
	}
	else{
		echo preg_replace('/:/',',',preg_replace('/-/',',',$amt[0]['nidin']));
	}
	echo '－'.$amt[0]['NONTAX'].'－';
	if(floatval($amt[0]['NONTAX'])>0){
		echo '1－';
	}
	else{
		echo '0－';
	}
}
echo $_POST['bizdate'].'－'.$_POST['consecnumber'];
if(isset($saleno[0]['saleno'])){
	echo '－'.$saleno[0]['saleno'];
}
else{
	echo '－';
}
?>