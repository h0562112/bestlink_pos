<?php
header('Access-Control-Allow-Origin: *');//╗╖║▌йIеs┼vнн

if(!isset($_POST['classstate'])){
	$orderweb['basic']['webclass']='1';
}
else{
	if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/orderweb.ini')){
		$orderweb=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/orderweb.ini',true);
		if(isset($orderweb['basic']['webclass'])){
			if($orderweb['basic']['webclass']!=$_POST['classstate']){
				include_once '../../../tool/inilib.php';
				$orderweb['basic']['webclass']=$_POST['classstate'];
				write_ini_file($orderweb,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/orderweb.ini');
			}
			else{
			}
		}
		else{
			$orderweb['basic']['webclass']='1';
		}
	}
	else{
		if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'])){
			$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/orderweb.ini','w');
			fwrite($f,'[basic]'.PHP_EOL);
			fwrite($f,'interval="15"'.PHP_EOL);
			fwrite($f,'start[0]="0900"'.PHP_EOL);
			fwrite($f,'end[0]="2300"'.PHP_EOL);
			fwrite($f,'webclass="1"'.PHP_EOL);
			fwrite($f,'[week1]'.PHP_EOL);
			fwrite($f,'open="1"'.PHP_EOL);
			fwrite($f,'interval="15"'.PHP_EOL);
			fwrite($f,'start[0]="0900"'.PHP_EOL);
			fwrite($f,'end[0]="2300"'.PHP_EOL);
			fwrite($f,'[week2]'.PHP_EOL);
			fwrite($f,'open="1"'.PHP_EOL);
			fwrite($f,'interval="15"'.PHP_EOL);
			fwrite($f,'start[0]="0900"'.PHP_EOL);
			fwrite($f,'end[0]="2300"'.PHP_EOL);
			fwrite($f,'[week3]'.PHP_EOL);
			fwrite($f,'open="1"'.PHP_EOL);
			fwrite($f,'interval="15"'.PHP_EOL);
			fwrite($f,'start[0]="0900"'.PHP_EOL);
			fwrite($f,'end[0]="2300"'.PHP_EOL);
			fwrite($f,'[week4]'.PHP_EOL);
			fwrite($f,'open="1"'.PHP_EOL);
			fwrite($f,'interval="15"'.PHP_EOL);
			fwrite($f,'start[0]="0900"'.PHP_EOL);
			fwrite($f,'end[0]="2300"'.PHP_EOL);
			fwrite($f,'[week5]'.PHP_EOL);
			fwrite($f,'open="1"'.PHP_EOL);
			fwrite($f,'interval="15"'.PHP_EOL);
			fwrite($f,'start[0]="0900"'.PHP_EOL);
			fwrite($f,'end[0]="2300"'.PHP_EOL);
			fwrite($f,'[week6]'.PHP_EOL);
			fwrite($f,'open="1"'.PHP_EOL);
			fwrite($f,'interval="15"'.PHP_EOL);
			fwrite($f,'start[0]="0900"'.PHP_EOL);
			fwrite($f,'end[0]="2300"'.PHP_EOL);
			fwrite($f,'[week7]'.PHP_EOL);
			fwrite($f,'open="1"'.PHP_EOL);
			fwrite($f,'interval="15"'.PHP_EOL);
			fwrite($f,'start[0]="0900"'.PHP_EOL);
			fwrite($f,'end[0]="2300"'.PHP_EOL);
			fclose($f);
			$orderweb['basic']['webclass']='1';
		}
		else{
			$orderweb['basic']['webclass']='1';
		}
	}
}
if($orderweb['basic']['webclass']=='0'){
	echo json_encode('empty');
}
else{
	include_once '../../../tool/dbTool.inc.php';
	$conn=sqlconnect('localhost','','orderuser','0424732003','utf-8','mysql');
	$sql='SHOW DATABASES LIKE "'.$_POST['company'].'"';
	$res=sqlquery($conn,$sql,'mysql');
	sqlclose($conn,'mysql');
	if(isset($res[0])){
		$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
		$sql='SELECT * FROM tempcst011 WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="1" ORDER BY CONSECNUMBER ASC';
		$tempres011=sqlquery($conn,$sql,'mysql');
		$sql='SELECT * FROM tempcst012 WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="1" ORDER BY CONSECNUMBER ASC,LINENUMBER DESC';
		$tempres012=sqlquery($conn,$sql,'mysql');

		$sql='UPDATE tempcst011 SET ORDERTYPE="-1" WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="1"';
		sqlnoresponse($conn,$sql,'mysql');
		$sql='UPDATE tempcst012 SET ORDERTYPE="-1" WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="1"';
		sqlnoresponse($conn,$sql,'mysql');

		sqlclose($conn,'mysql');
		if(sizeof($tempres011)>0){
			echo json_encode(array($tempres011,$tempres012));
		}
		else{
			echo json_encode('empty');
		}
	}
	else{
		echo json_encode('empty');
	}
}
?>