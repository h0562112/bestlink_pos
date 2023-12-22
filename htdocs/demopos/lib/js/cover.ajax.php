<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
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
//date_default_timezone_set('Asia/Taipei');


$conn1=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT SALESTTLAMT,TAX1 FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$initdata=sqlquery($conn1,$sql,'sqlite');
sqlclose($conn1,'sqlite');

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
$sql='SELECT * FROM list WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'"';
$list=sqlquery($conn,$sql,'sqlite');
if(sizeof($list)>0&&isset($list[0]['bizdate'])){
	$sql='UPDATE list SET tax2='.$_POST['cashmoney'].',tax3='.$_POST['cash'].',tax9='.$_POST['cashcomm'].',tax4=';
	if(isset($_POST['other'])&&$_POST['other']>0&&isset($_POST['otherfix'])&&$_POST['otherfix']>0){
		$sql=$sql.($_POST['other']+$_POST['otherfix']);
	}
	else if(isset($_POST['other'])&&$_POST['other']>0){
		$sql=$sql.$_POST['other'];
	}
	else if(isset($_POST['otherfix'])&&$_POST['otherfix']>0){
		$sql=$sql.$_POST['otherfix'];
	}
	else{
		$sql=$sql.'0';
	}
	$temp=preg_split('/,/',$_POST['otherstring']);
	$otharray=array();
	foreach($temp as $t){
		$tot1=preg_split('/:/',$t);
		if(preg_match('/CST011-TA/',$tot1[0])){
			$otharray[substr($tot1[0],7)]=$tot1[1];
		}
		else if(strstr($tot1[0],'memberpoint')){
			$otharray[substr($tot1[0],12)]=$tot1[1];
		}
		else if(strstr($tot1[0],'membermoney')){
			$otharray[substr($tot1[0],12)]=$tot1[1];
		}
		else if(strstr($tot1[0],'intellaother')){//英特拉付款無法透過修改付款功能變動
		}
		else if(strstr($tot1[0],'nidinother')){//2021/8/18 你訂付款無法透過修改付款功能變動
		}
		else{
			$tempother=preg_split('/-/',$tot1[0]);
			if(isset($tempother[1])){
				$otharray[$tempother[0]][$tempother[1]]=$tot1[1];
			}
			else{
			}
		}
	}
	for($i=1;$i<=10;$i++){
		if(isset($otharray['TA'.$i])){
			$sql=$sql.',ta'.$i.'="'.$otharray['TA'.$i].'"';
		}
		else{
			$sql=$sql.',ta'.$i.'=0';
		}
	}
	if(isset($otharray['NONTAX'])){
		$sql=$sql.',nontax='.$otharray['NONTAX'];
	}
	else{
		$sql=$sql.',nontax=0';
	}
	if(isset($otherpaydata[0])){
		for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
			if(isset($otharray[$otherpaydata[$rowindex]]['value'])){
				$sql=$sql.','.$otherpaydata[$rowindex].'="'.$otharray[$otherpaydata[$rowindex]]['value'].'"';
			}
			else{
				$sql=$sql.','.$otherpaydata[$rowindex].'=0';
			}
		}
	}
	else{
	}
	$sql=$sql.' WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'"';
	//echo $sql;
	sqlnoresponse($conn,$sql,'sqlite');
}
else{
	$conn1=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT intella FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
	//echo $sql;
	$intella=sqlquery($conn1,$sql,'sqlite');
	//print_r($intella);
	$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND TAX2='.$_POST['cashmoney'].' AND TAX3='.$_POST['cash'].' AND TAX9='.$_POST['cashcomm'].' AND TAX4='.$_POST['other'];
	$temp=preg_split('/,/',$_POST['otherstring']);
	$otharray=array();
	foreach($temp as $t){
		$tot1=preg_split('/:/',$t);
		if(preg_match('/CST011-TA/',$tot1[0])){
			$otharray[substr($tot1[0],7)]=$tot1[1];
		}
		else if(strstr($tot1[0],'memberpoint')){
			$otharray[substr($tot1[0],12)]=$tot1[1];
		}
		else if(strstr($tot1[0],'membermoney')){
			$otharray[substr($tot1[0],12)]=$tot1[1];
		}
		else if(strstr($tot1[0],'intellaother')){//英特拉付款無法透過修改付款功能變動
		}
		else if(strstr($tot1[0],'nidinother')){//2021/8/18 你訂付款無法透過修改付款功能變動
		}
		else{
			$tempother=preg_split('/-/',$tot1[0]);
			if(isset($tempother[1])){
				$otharray[$tempother[0]][$tempother[1]]=$tot1[1];
			}
			else{
			}
		}
	}
	for($i=1;$i<=10;$i++){
		if(isset($otharray['TA'.$i])){
			$sql=$sql.' AND TA'.$i.'="'.$otharray['TA'.$i].'"';
		}
		else{
			$sql=$sql.' AND TA'.$i.'=0';
		}
	}
	if(isset($otharray['NONTAX'])){
		$sql=$sql.' AND NONTAX='.$otharray['NONTAX'];
	}
	else{
		$sql=$sql.' AND NONTAX=0';
	}
	if(isset($otherpaydata[0])){
		for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
			if(isset($otharray[$otherpaydata[$rowindex]]['value'])){
				$sql=$sql.' AND '.$otherpaydata[$rowindex].'="'.$otharray[$otherpaydata[$rowindex]]['value'].'"';
			}
			else{
				$sql=$sql.' AND '.$otherpaydata[$rowindex].'=0';
			}
		}
	}
	else{
	}
	$initlist=sqlquery($conn1,$sql,'sqlite');
	sqlclose($conn1,'sqlite');
	//echo $sql;
	if(sizeof($initlist)>0&&isset($initlist['BIZDATE'])){
	}
	else{
		$sql='INSERT INTO list (coverbizdate,coverzcounter,usercode,username,bizdate,consecnumber,salesttlamt,tax1,tax2,tax3,tax4,tax9,ta1,ta2,ta3,ta4,ta5,ta6,ta7,ta8,ta9,ta10,intella,nontax';
		if(isset($otherpaydata[0])){
			for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
				$sql=$sql.','.$otherpaydata[$rowindex];
			}
		}
		else{
		}
		$sql=$sql.',createdatetime,state) VALUES ("'.$timeini['time']['bizdate'].'","'.$timeini['time']['zcounter'].'","'.$_POST['usercode'].'","'.$_POST['username'].'","'.$_POST['bizdate'].'","'.$_POST['consecnumber'].'",'.$initdata[0]['SALESTTLAMT'].','.$initdata[0]['TAX1'].','.$_POST['cashmoney'].','.$_POST['cash'].',';
		if(isset($_POST['other'])&&$_POST['other']>0&&isset($_POST['otherfix'])&&$_POST['otherfix']>0){
			$sql=$sql.($_POST['other']+$_POST['otherfix']);
		}
		else if(isset($_POST['other'])&&$_POST['other']>0){
			$sql=$sql.$_POST['other'];
		}
		else if(isset($_POST['otherfix'])&&$_POST['otherfix']>0){
			$sql=$sql.$_POST['otherfix'];
		}
		else{
			$sql=$sql.'0';
		}
		$sql=$sql.','.$_POST['cashcomm'];
		
		for($i=1;$i<=10;$i++){
			if(isset($otharray['TA'.$i])){
				$sql=$sql.',"'.$otharray['TA'.$i].'"';
			}
			else{
				$sql=$sql.',0';
			}
		}
		$sql=$sql.',"'.$intella[0]['intella'].'"';
		if(isset($otharray['NONTAX'])){
			$sql=$sql.','.$otharray['NONTAX'];
		}
		else{
			$sql=$sql.',0';
		}
		if(isset($otherpaydata[0])){
			for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
				if(isset($otharray[$otherpaydata[$rowindex]]['value'])){
					$sql=$sql.',"'.$otharray[$otherpaydata[$rowindex]]['value'].'"';
				}
				else{
					$sql=$sql.',0';
				}
			}
		}
		else{
		}
		date_default_timezone_set($init['init']['settime']);
		$sql=$sql.',"'.date('YmdHis').'",1)';
		//echo $sql;
		sqlnoresponse($conn,$sql,'sqlite');
	}
}
sqlclose($conn,'sqlite');
?>