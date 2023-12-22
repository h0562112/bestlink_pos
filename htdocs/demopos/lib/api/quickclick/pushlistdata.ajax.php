<?php
include_once '../../../../tool/dbTool.inc.php';
include_once '../../../../tool/inilib.php';
if(file_exists('../../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../../database/mapping.ini',true);
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
$init=parse_ini_file('../../../../database/initsetting.ini',true);
date_default_timezone_set($init['init']['settime']);

$machinedata=parse_ini_file('../../../../database/machinedata.ini',true);
$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+sizeof($_POST['data'][0]);
$saleno=$machinedata['basic']['saleno'];
if(intval($machinedata['basic']['saleno'])>=intval($machinedata['basic']['maxsaleno'])){
	if(isset($machinedata['basic']['strsaleno'])){//2020/12/10
		$machinedata['basic']['saleno']=intval($machinedata['basic']['strsaleno'])+intval($saleno)-intval($machinedata['basic']['maxsaleno']);
	}
	else{
		$machinedata['basic']['saleno']=intval($saleno)-intval($machinedata['basic']['maxsaleno']);
	}
	$saleno=$machinedata['basic']['saleno'];
}
else{
}
write_ini_file($machinedata,'../../../../database/machinedata.ini');

/*if($_POST['machinetype']=='rightnow'){
	$timeini['time']['bizdate']=date('Ymd');
	$timeini['time']['zcounter']='1';
}
else{*/
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
		$timeini=parse_ini_file('../../../../database/time'.$invmachine.'.ini',true);
	}
	else{//帳務以主機為主體計算
		$timeini=parse_ini_file('../../../../database/timem1.ini',true);
	}
//}
if(file_exists("../../../../database/sale/SALES_".substr($timeini['time']['bizdate'],0,6).".DB")){
}
else{
	if(file_exists("../../../../database/sale/empty.DB")){
	}
	else{
		include_once 'create.emptyDB.php';
		create('empty');
	}
	copy("../../../../database/sale/empty.DB","../../../../database/sale/SALES_".substr($timeini['time']['bizdate'],0,6).".DB");
}
//print_r($_POST);
$conn=sqlconnect('../../../../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT (SELECT CONSECNUMBER FROM CST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) as con,(SELECT CONSECNUMBER FROM tempCST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) as tempcon';
$consecnumber=sqlquery($conn,$sql,'sqlite');

//2021/10/18 查詢網路訂單的編號
$sql='SELECT (SELECT SUBSTR(CONSECNUMBER,2) FROM CST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS one,(SELECT SUBSTR(CONSECNUMBER,2) FROM tempCST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS two';
$w=sqlquery($conn,$sql,'sqlite');
if($consecnumber[0]['con']==null){
	$consecnumber[0]['con']=$w[0]['one'];
}
else{
	if(floatval($consecnumber[0]['con'])<floatval($w[0]['one'])){
		$consecnumber[0]['con']=$w[0]['one'];
	}
	else{
	}
}
if($consecnumber[0]['tempcon']==null){
	$consecnumber[0]['tempcon']=$w[0]['two'];
}
else{
	if(floatval($consecnumber[0]['tempcon'])<floatval($w[0]['two'])){
		$consecnumber[0]['tempcon']=$w[0]['two'];
	}
	else{
	}
}
//print_r($_POST['data']);
$listdata=array();
//$intella=0;//2021/9/10 統一存進temp，方便後續開發票
if(isset($consecnumber[0])&&sizeof($consecnumber[0])==2){
	//$sql='';
	if(intval($consecnumber[0]['con'])<=intval($consecnumber[0]['tempcon'])){
		$consecnumber[0]['tempcon']=intval($consecnumber[0]['tempcon'])+sizeof($_POST['data'][0]);
		$j=sizeof($_POST['data'][1])-1;
		for($i=sizeof($_POST['data'][0])-1;$i>=0;$i--){
			$sql='SELECT * FROM tempCST011 WHERE CREATEDATETIME="'.$_POST['data'][0][$i]['CREATEDATETIME'].'"';
			$exi1=sqlquery($conn,$sql,'sqlite');

			$sql='SELECT * FROM CST011 WHERE CREATEDATETIME="'.$_POST['data'][0][$i]['CREATEDATETIME'].'"';
			$exi2=sqlquery($conn,$sql,'sqlite');
			if(isset($exi1[0]['TERMINALNUMBER'])||isset($exi2[0]['TERMINALNUMBER'])){//存在相同時間戳印訂單，先不儲存
			}
			else{
				$sql='INSERT INTO ';
				/*if(isset($_POST['data'][0][$i]['intella'])&&strlen($_POST['data'][0][$i]['intella'])>15){//利用線上電子支付結帳
					$sql=$sql.'CST011 ';
					$intella=1;
				}
				else{*/
					$sql=$sql.'tempCST011 ';
					//$intella=0;
				//}
				$sql=$sql.'(TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,SALESTTLQTY,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TAX10,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,TABLENUMBER,RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,CREDITCARD,intella) VALUES ';
				if(isset($_POST['data'][0][$i]['RELINVOICENUMBER'])&&($_POST['data'][0][$i]['RELINVOICENUMBER']!=';;'&&$_POST['data'][0][$i]['RELINVOICENUMBER']!=''&&$_POST['data'][0][$i]['RELINVOICENUMBER']!=null)){
					$userdata=$_POST['data'][0][$i]['RELINVOICENUMBER'];
				}
				else{
					$userdata='';
				}
				foreach($_POST['data'][0][$i] as $k=>$v){
					if($k=='TERMINALNUMBER'){
						if(strlen($v)<5){//2020/12/1 基本上機號不會太長，depcode通常至少5碼，該欄位會存depcode都是網路下單的帳單
							$sql=$sql.'("'.$v.'"';
						}
						else{
							$sql=$sql.'("m1"';
						}
					}
					else if($k=='BIZDATE'){
						$sql=$sql.',"'.$timeini['time']['bizdate'].'"';
					}
					else if($k=='ZCOUNTER'){
						$sql=$sql.',"'.$timeini['time']['zcounter'].'"';
					}
					else if($k=='CONSECNUMBER'){
						$sql=$sql.',"w'.str_pad($consecnumber[0]['tempcon'],5,'0',STR_PAD_LEFT).'"';
					}
					else if($k=='ORDERTYPE'){
					}
					else if($k=='listbizdate'){
					}
					else if($k=='UPDATEDATETIME'){
						$sql=$sql.',"'.date('YmdHis').'"';
					}
					else if($k=='INVOICENUMBER'){
						if(strlen($v)==0||$v==NULL){
							$sql=$sql.',""';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
					else if($k=='intella'){
						if($v==null||strlen($v)==0){
							$sql=$sql.',NULL';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
					else if($k=='RELINVOICENUMBER'){
						$sql=$sql.',"'.$v;
						if($userdata!=''){
							$sql=$sql.' '.$userdata;
						}
						else{
						}
						$sql=$sql.'"';
					}
					else{
						if($v==null){
							$sql=$sql.',NULL';
						}
						else if(strlen($v)==0){
							$sql=$sql.', ';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
				}
				if(isset($_POST['data'][0][$i]['intella'])){
				}
				else{
					$sql=$sql.',NULL';
				}
				$sql=$sql.');';
				$t012='';
				while($j>=0){
					if($_POST['data'][1][$j]['CONSECNUMBER']!=$_POST['data'][0][$i]['CONSECNUMBER']||$_POST['data'][1][$j]['BIZDATE']!=$_POST['data'][0][$i]['BIZDATE']){
						break;
					}
					else{
						/*if($intella){//利用線上電子支付結帳
							$sql=$sql.'INSERT INTO CST012 VALUES ';
						}
						else{*/
							//$sql=$sql.'INSERT INTO tempCST012 VALUES ';
						//}
						foreach($_POST['data'][1][$j] as $k=>$v){
							if($k=='TERMINALNUMBER'){
								if($t012!=''){
									$t012=$t012.',';
								}
								else{
									$t012=$t012.'INSERT INTO tempCST012 VALUES ';
								}
								$t012=$t012.'("'.$v.'"';
							}
							else if($k=='BIZDATE'){
								$t012=$t012.',"'.$timeini['time']['bizdate'].'"';
							}
							else if($k=='ZCOUNTER'){
								$t012=$t012.',"'.$timeini['time']['zcounter'].'"';
							}
							else if($k=='CONSECNUMBER'){
								$t012=$t012.',"w'.str_pad($consecnumber[0]['tempcon'],5,'0',STR_PAD_LEFT).'"';
							}
							else if($k=='ORDERTYPE'){
							}
							else{
								if($v==null){
									$t012=$t012.',NULL';
								}
								else{
									$t012=$t012.',"'.$v.'"';
								}
							}
						}
						$j--;
						$t012=$t012.')';
					}
				}
				$sql=$sql.$t012.';';

				/*$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
				$saleno=$machinedata['basic']['saleno'];
				if(intval($machinedata['basic']['saleno'])>intval($machinedata['basic']['maxsaleno'])){
					$machinedata['basic']['saleno']=1;
				}
				else{
				}*/
				
				$testsql='PRAGMA table_info(salemap);';
				$testarray=sqlquery($conn,$testsql,'sqlite');
				$test=array_column($testarray,'name');
				$testsql='';
				if(in_array('onlinebizdate',$test)){
				}
				else{
					$testsql=$testsql.'ALTER TABLE salemap ADD COLUMN onlinebizdate TEXT;';
					//sqlnoresponse($conn,$sql,'sqliteexec');
				}
				if(in_array('onlineconsecnumber',$test)){
				}
				else{
					$testsql=$testsql.'ALTER TABLE salemap ADD COLUMN onlineconsecnumber TEXT;';
					//sqlnoresponse($conn,$sql,'sqliteexec');
				}
				if($testsql!=''){
					sqlnoresponse($conn,$testsql,'sqliteexec');
				}
				else{
				}
				$sql=$sql.'INSERT INTO salemap (bizdate,consecnumber,saleno,onlinebizdate,onlineconsecnumber) VALUES ("'.$timeini['time']['bizdate'].'","w'.str_pad($consecnumber[0]['tempcon'],5,'0',STR_PAD_LEFT).'","'.$saleno.'","'.$_POST['data'][0][$i]['BIZDATE'].'","'.$_POST['data'][0][$i]['CONSECNUMBER'].'");';
				$listdata[]=array($timeini['time']['bizdate'],'w'.str_pad($consecnumber[0]['tempcon'],5,'0',STR_PAD_LEFT),$saleno,$_POST['data'][0][$i]['CONSECNUMBER'],$_POST['data'][0][$i]['TERMINALNUMBER'],$_POST['data'][0][$i]['BIZDATE']);

				$consecnumber[0]['tempcon']--;

				$saleno--;
				if(isset($machinedata['basic']['strsaleno'])){//2020/12/10
					if($saleno<$machinedata['basic']['strsaleno']){
						$saleno=$machinedata['basic']['maxsaleno'];
					}
					else{
					}
				}
				else{
					if($saleno<0){
						$saleno=$machinedata['basic']['maxsaleno'];
					}
					else{
					}
				}
				sqlnoresponse($conn,$sql,'sqliteexec');

				$f=fopen('./tempsql.txt','a');
				fwrite($f,date('Y/m/d H:i:s').' --- '.$sql.PHP_EOL);
				fclose($f);
			}
		}
	}
	else if(intval($consecnumber[0]['tempcon'])<intval($consecnumber[0]['con'])){
		$consecnumber[0]['con']=intval($consecnumber[0]['con'])+sizeof($_POST['data'][0]);
		$j=sizeof($_POST['data'][1])-1;
		for($i=sizeof($_POST['data'][0])-1;$i>=0;$i--){
			$sql='SELECT * FROM tempCST011 WHERE CREATEDATETIME="'.$_POST['data'][0][$i]['CREATEDATETIME'].'"';
			$exi1=sqlquery($conn,$sql,'sqlite');

			$sql='SELECT * FROM CST011 WHERE CREATEDATETIME="'.$_POST['data'][0][$i]['CREATEDATETIME'].'"';
			$exi2=sqlquery($conn,$sql,'sqlite');
			if(isset($exi1[0]['TERMINALNUMBER'])||isset($exi2[0]['TERMINALNUMBER'])){//存在相同時間戳印訂單，先不儲存
			}
			else{
				$sql='INSERT INTO ';
				/*if(isset($_POST['data'][0][$i]['intella'])&&strlen($_POST['data'][0][$i]['intella'])>15){//利用線上電子支付結帳
					$sql=$sql.'CST011 ';
					$intella=1;
				}
				else{*/
					$sql=$sql.'tempCST011 ';
					//$intella=0;
				//}
				$sql=$sql.'(TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,SALESTTLQTY,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TAX10,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,TABLENUMBER,RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,CREDITCARD,intella) VALUES ';
				foreach($_POST['data'][0][$i] as $k=>$v){
					if($k=='TERMINALNUMBER'){
						$sql=$sql.'("'.$v.'"';
					}
					else if($k=='BIZDATE'){
						$sql=$sql.',"'.$timeini['time']['bizdate'].'"';
					}
					else if($k=='ZCOUNTER'){
						$sql=$sql.',"'.$timeini['time']['zcounter'].'"';
					}
					else if($k=='CONSECNUMBER'){
						$sql=$sql.',"w'.str_pad($consecnumber[0]['con'],5,'0',STR_PAD_LEFT).'"';
					}
					else if($k=='ORDERTYPE'){
					}
					else if($k=='listbizdate'){
					}
					else if($k=='UPDATEDATETIME'){
						$sql=$sql.',"'.date('YmdHis').'"';
					}
					else if($k=='INVOICENUMBER'){
						if(strlen($v)==0||$v==NULL){
							$sql=$sql.',""';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
					else if($k=='intella'){
						if($v==null||strlen($v)==0){
							$sql=$sql.',NULL';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
					else{
						if($v==null){
							$sql=$sql.',NULL';
						}
						else if(strlen($v)==0){
							$sql=$sql.', ';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
				}
				if(isset($_POST['data'][0][$i]['intella'])){
				}
				else{
					$sql=$sql.',NULL';
				}
				$sql=$sql.');';
				while($j>=0){
					if($_POST['data'][1][$j]['CONSECNUMBER']!=$_POST['data'][0][$i]['CONSECNUMBER']||$_POST['data'][1][$j]['BIZDATE']!=$_POST['data'][0][$i]['BIZDATE']){
						break;
					}
					else{
						/*if($intella){//利用線上電子支付結帳
							$sql=$sql.'INSERT INTO CST012 VALUES ';
						}
						else{*/
							$sql=$sql.'INSERT INTO tempCST012 VALUES ';
						//}
						foreach($_POST['data'][1][$j] as $k=>$v){
							if($k=='TERMINALNUMBER'){
								$sql=$sql.'("'.$v.'"';
							}
							else if($k=='BIZDATE'){
								$sql=$sql.',"'.$timeini['time']['bizdate'].'"';
							}
							else if($k=='ZCOUNTER'){
								$sql=$sql.',"'.$timeini['time']['zcounter'].'"';
							}
							else if($k=='CONSECNUMBER'){
								$sql=$sql.',"w'.str_pad($consecnumber[0]['con'],5,'0',STR_PAD_LEFT).'"';
							}
							else if($k=='ORDERTYPE'){
							}
							else{
								if($v==null){
									$sql=$sql.',NULL';
								}
								else{
									$sql=$sql.',"'.$v.'"';
								}
							}
						}
						$j--;
						$sql=$sql.');';
					}
				}

				/*$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
				$saleno=$machinedata['basic']['saleno'];
				if(intval($machinedata['basic']['saleno'])>intval($machinedata['basic']['maxsaleno'])){
					$machinedata['basic']['saleno']=1;
				}
				else{
				}*/
				
				$testsql='PRAGMA table_info(salemap);';
				$testarray=sqlquery($conn,$testsql,'sqlite');
				$test=array_column($testarray,'name');
				$testsql='';
				if(in_array('onlinebizdate',$test)){
				}
				else{
					$testsql=$testsql.'ALTER TABLE salemap ADD COLUMN onlinebizdate TEXT;';
					//sqlnoresponse($conn,$sql,'sqliteexec');
				}
				if(in_array('onlineconsecnumber',$test)){
				}
				else{
					$testsql=$testsql.'ALTER TABLE salemap ADD COLUMN onlineconsecnumber TEXT;';
					//sqlnoresponse($conn,$sql,'sqliteexec');
				}
				if($testsql!=''){
					sqlnoresponse($conn,$testsql,'sqliteexec');
				}
				else{
				}
				$sql=$sql.'INSERT INTO salemap (bizdate,consecnumber,saleno,onlinebizdate,onlineconsecnumber) VALUES ("'.$timeini['time']['bizdate'].'","w'.str_pad($consecnumber[0]['con'],5,'0',STR_PAD_LEFT).'","'.$saleno.'","'.$_POST['data'][0][$i]['BIZDATE'].'","'.$_POST['data'][0][$i]['CONSECNUMBER'].'");';
				$listdata[]=array($timeini['time']['bizdate'],'w'.str_pad($consecnumber[0]['con'],5,'0',STR_PAD_LEFT),$saleno,$_POST['data'][0][$i]['CONSECNUMBER'],$_POST['data'][0][$i]['TERMINALNUMBER'],$_POST['data'][0][$i]['BIZDATE']);

				$consecnumber[0]['con']--;

				$saleno--;
				if(isset($machinedata['basic']['strsaleno'])){//2020/12/10
					if($saleno<$machinedata['basic']['strsaleno']){
						$saleno=$machinedata['basic']['maxsaleno'];
					}
					else{
					}
				}
				else{
					if($saleno<0){
						$saleno=$machinedata['basic']['maxsaleno'];
					}
					else{
					}
				}
				sqlnoresponse($conn,$sql,'sqliteexec');
				$f=fopen('./tempsql.txt','a');
				fwrite($f,date('Y/m/d H:i:s').' --- '.$sql.PHP_EOL);
				fclose($f);
			}
		}
	}
	else{
		$j=sizeof($_POST['data'][1])-1;
		for($i=sizeof($_POST['data'][0])-1;$i>=0;$i--){
			$sql='SELECT * FROM tempCST011 WHERE CREATEDATETIME="'.$_POST['data'][0][$i]['CREATEDATETIME'].'"';
			$exi1=sqlquery($conn,$sql,'sqlite');

			$sql='SELECT * FROM CST011 WHERE CREATEDATETIME="'.$_POST['data'][0][$i]['CREATEDATETIME'].'"';
			$exi2=sqlquery($conn,$sql,'sqlite');
			if(isset($exi1[0]['TERMINALNUMBER'])||isset($exi2[0]['TERMINALNUMBER'])){//存在相同時間戳印訂單，先不儲存
			}
			else{
				$sql='INSERT INTO ';
				/*if(isset($_POST['data'][0][$i]['intella'])&&strlen($_POST['data'][0][$i]['intella'])>15){//利用線上電子支付結帳
					$sql=$sql.'CST011 ';
					$intella=1;
				}
				else{*/
					$sql=$sql.'tempCST011 ';
					//$intella=0;
				//}
				$sql=$sql.'(TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,SALESTTLQTY,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TAX10,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,TABLENUMBER,RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,CREDITCARD,intella) VALUES ';
				foreach($_POST['data'][0][$i] as $k=>$v){
					if($k=='TERMINALNUMBER'){
						$sql=$sql.'("'.$v.'"';
					}
					else if($k=='BIZDATE'){
						$sql=$sql.',"'.$timeini['time']['bizdate'].'"';
					}
					else if($k=='ZCOUNTER'){
						$sql=$sql.',"'.$timeini['time']['zcounter'].'"';
					}
					else if($k=='CONSECNUMBER'){
						$sql=$sql.',"w'.str_pad((intval($i)+1),5,'0',STR_PAD_LEFT).'"';
					}
					else if($k=='ORDERTYPE'){
					}
					else if($k=='listbizdate'){
					}
					else if($k=='UPDATEDATETIME'){
						$sql=$sql.',"'.date('YmdHis').'"';
					}
					else if($k=='INVOICENUMBER'){
						if(strlen($v)==0||$v==NULL){
							$sql=$sql.',""';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
					else if($k=='intella'){
						if($v==null||strlen($v)==0){
							$sql=$sql.',NULL';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
					else{
						if($v==null){
							$sql=$sql.',NULL';
						}
						else if(strlen($v)==0){
							$sql=$sql.', ';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
				}
				if(isset($_POST['data'][0][$i]['intella'])){
				}
				else{
					$sql=$sql.',NULL';
				}
				$sql=$sql.');';
				while($j>=0){
					if($_POST['data'][1][$j]['CONSECNUMBER']!=$_POST['data'][0][$i]['CONSECNUMBER']||$_POST['data'][1][$j]['BIZDATE']!=$_POST['data'][0][$i]['BIZDATE']){
						break;
					}
					else{
						/*if($intella){//利用線上電子支付結帳
							$sql=$sql.'INSERT INTO CST012 VALUES ';
						}
						else{*/
							$sql=$sql.'INSERT INTO tempCST012 VALUES ';
						//}
						foreach($_POST['data'][1][$j] as $k=>$v){
							if($k=='TERMINALNUMBER'){
								$sql=$sql.'("'.$v.'"';
							}
							else if($k=='BIZDATE'){
								$sql=$sql.',"'.$timeini['time']['bizdate'].'"';
							}
							else if($k=='ZCOUNTER'){
								$sql=$sql.',"'.$timeini['time']['zcounter'].'"';
							}
							else if($k=='CONSECNUMBER'){
								$sql=$sql.',"w'.str_pad((intval($i)+1),5,'0',STR_PAD_LEFT).'"';
							}
							else if($k=='ORDERTYPE'){
							}
							else{
								if($v==null){
									$sql=$sql.',NULL';
								}
								else{
									$sql=$sql.',"'.$v.'"';
								}
							}
						}
						$j--;
						$sql=$sql.');';
					}
				}

				/*$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
				$saleno=$machinedata['basic']['saleno'];
				if(intval($machinedata['basic']['saleno'])>intval($machinedata['basic']['maxsaleno'])){
					$machinedata['basic']['saleno']=1;
				}
				else{
				}*/
				
				$testsql='PRAGMA table_info(salemap);';
				$testarray=sqlquery($conn,$testsql,'sqlite');
				$test=array_column($testarray,'name');
				$testsql='';
				if(in_array('onlinebizdate',$test)){
				}
				else{
					$testsql=$testsql.'ALTER TABLE salemap ADD COLUMN onlinebizdate TEXT;';
					//sqlnoresponse($conn,$sql,'sqliteexec');
				}
				if(in_array('onlineconsecnumber',$test)){
				}
				else{
					$testsql=$testsql.'ALTER TABLE salemap ADD COLUMN onlineconsecnumber TEXT;';
					//sqlnoresponse($conn,$sql,'sqliteexec');
				}
				if($testsql!=''){
					sqlnoresponse($conn,$testsql,'sqliteexec');
				}
				else{
				}
				$sql=$sql.'INSERT INTO salemap (bizdate,consecnumber,saleno,onlinebizdate,onlineconsecnumber) VALUES ("'.$timeini['time']['bizdate'].'","w'.str_pad((intval($i)+1),5,'0',STR_PAD_LEFT).'","'.$saleno.'","'.$_POST['data'][0][$i]['BIZDATE'].'","'.$_POST['data'][0][$i]['CONSECNUMBER'].'");';
				$listdata[]=array($timeini['time']['bizdate'],'w'.str_pad((intval($i)+1),5,'0',STR_PAD_LEFT),$saleno,$_POST['data'][0][$i]['CONSECNUMBER'],$_POST['data'][0][$i]['TERMINALNUMBER'],$_POST['data'][0][$i]['BIZDATE']);

				$saleno--;
				if(isset($machinedata['basic']['strsaleno'])){//2020/12/10
					if($saleno<$machinedata['basic']['strsaleno']){
						$saleno=$machinedata['basic']['maxsaleno'];
					}
					else{
					}
				}
				else{
					if($saleno<0){
						$saleno=$machinedata['basic']['maxsaleno'];
					}
					else{
					}
				}
				sqlnoresponse($conn,$sql,'sqliteexec');
				$f=fopen('./tempsql.txt','a');
				fwrite($f,date('Y/m/d H:i:s').' --- '.$sql.PHP_EOL);
				fclose($f);
			}
		}
	}
}
else{
	$j=sizeof($_POST['data'][1])-1;
	for($i=sizeof($_POST['data'][0])-1;$i>=0;$i--){
		$sql=$sql.'INSERT INTO ';
		/*if(isset($_POST['data'][0][$i]['intella'])&&strlen($_POST['data'][0][$i]['intella'])>15){//利用線上電子支付結帳
			$sql=$sql.'CST011 ';
			$intella=1;
		}
		else{*/
			$sql=$sql.'tempCST011 ';
			//$intella=0;
		//}
		$sql=$sql.'(TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,SALESTTLQTY,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TAX10,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,NONTAX,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,TABLENUMBER,RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,CREDITCARD,intella) VALUES ';
		foreach($_POST['data'][0][$i] as $k=>$v){
			if($k=='TERMINALNUMBER'){
				$sql=$sql.'("'.$v.'"';
			}
			else if($k=='BIZDATE'){
				$sql=$sql.',"'.$timeini['time']['bizdate'].'"';
			}
			else if($k=='ZCOUNTER'){
				$sql=$sql.',"'.$timeini['time']['zcounter'].'"';
			}
			else if($k=='CONSECNUMBER'){
				$sql=$sql.',"w'.str_pad((intval($i)+1),5,'0',STR_PAD_LEFT).'"';
			}
			else if($k=='ORDERTYPE'){
			}
			else if($k=='listbizdate'){
			}
			else if($k=='UPDATEDATETIME'){
				$sql=$sql.',"'.date('YmdHis').'"';
			}
			else if($k=='INVOICENUMBER'){
				if(strlen($v)==0||$v==NULL){
					$sql=$sql.',""';
				}
				else{
					$sql=$sql.',"'.$v.'"';
				}
			}
			else if($k=='intella'){
				if($v==null||strlen($v)==0){
					$sql=$sql.',NULL';
				}
				else{
					$sql=$sql.',"'.$v.'"';
				}
			}
			else{
				if($v==null){
					$sql=$sql.',NULL';
				}
				else if(strlen($v)==0){
					$sql=$sql.', ';
				}
				else{
					$sql=$sql.',"'.$v.'"';
				}
			}
		}
		if(isset($_POST['data'][0][$i]['intella'])){
		}
		else{
			$sql=$sql.',NULL';
		}
		$sql=$sql.');';
		while($j>=0){
			if($_POST['data'][1][$j]['CONSECNUMBER']!=$_POST['data'][0][$i]['CONSECNUMBER']||$_POST['data'][1][$j]['BIZDATE']!=$_POST['data'][0][$i]['BIZDATE']){
				break;
			}
			else{
				/*if($intella){//利用線上電子支付結帳
					$sql=$sql.'INSERT INTO CST012 VALUES ';
				}
				else{*/
					$sql=$sql.'INSERT INTO tempCST012 VALUES ';
				//}
				foreach($_POST['data'][1][$j] as $k=>$v){
					if($k=='TERMINALNUMBER'){
						$sql=$sql.'("'.$v.'"';
					}
					else if($k=='BIZDATE'){
						$sql=$sql.',"'.$timeini['time']['bizdate'].'"';
					}
					else if($k=='ZCOUNTER'){
						$sql=$sql.',"'.$timeini['time']['zcounter'].'"';
					}
					else if($k=='CONSECNUMBER'){
						$sql=$sql.',"w'.str_pad((intval($i)+1),5,'0',STR_PAD_LEFT).'"';
					}
					else if($k=='ORDERTYPE'){
					}
					else{
						if($v==null){
							$sql=$sql.',NULL';
						}
						else{
							$sql=$sql.',"'.$v.'"';
						}
					}
				}
				$j--;
				$sql=$sql.');';
			}
		}

		/*$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
		$saleno=$machinedata['basic']['saleno'];
		if(intval($machinedata['basic']['saleno'])>intval($machinedata['basic']['maxsaleno'])){
			$machinedata['basic']['saleno']=1;
		}
		else{
		}*/
		
		$testsql='PRAGMA table_info(salemap);';
		$testarray=sqlquery($conn,$testsql,'sqlite');
		$test=array_column($testarray,'name');
		$testsql='';
		if(in_array('onlinebizdate',$test)){
		}
		else{
			$testsql=$testsql.'ALTER TABLE salemap ADD COLUMN onlinebizdate TEXT;';
			//sqlnoresponse($conn,$sql,'sqliteexec');
		}
		if(in_array('onlineconsecnumber',$test)){
		}
		else{
			$testsql=$testsql.'ALTER TABLE salemap ADD COLUMN onlineconsecnumber TEXT;';
			//sqlnoresponse($conn,$sql,'sqliteexec');
		}
		if($testsql!=''){
			sqlnoresponse($conn,$testsql,'sqliteexec');
		}
		else{
		}
		$sql=$sql.'INSERT INTO salemap (bizdate,consecnumber,saleno,onlinebizdate,onlineconsecnumber) VALUES ("'.$timeini['time']['bizdate'].'","w'.str_pad((intval($i)+1),5,'0',STR_PAD_LEFT).'","'.$saleno.'","'.$_POST['data'][0][$i]['BIZDATE'].'","'.$_POST['data'][0][$i]['CONSECNUMBER'].'");';
		$listdata[]=array($timeini['time']['bizdate'],'w'.str_pad((intval($i)+1),5,'0',STR_PAD_LEFT),$saleno,$_POST['data'][0][$i]['CONSECNUMBER'],$_POST['data'][0][$i]['TERMINALNUMBER'],$_POST['data'][0][$i]['BIZDATE']);

		$saleno--;
		if(isset($machinedata['basic']['strsaleno'])){//2020/12/10
			if($saleno<$machinedata['basic']['strsaleno']){
				$saleno=$machinedata['basic']['maxsaleno'];
			}
			else{
			}
		}
		else{
			if($saleno<0){
				$saleno=$machinedata['basic']['maxsaleno'];
			}
			else{
			}
		}
	}
	sqlnoresponse($conn,$sql,'sqliteexec');
	$f=fopen('./tempsql.txt','a');
	fwrite($f,date('Y/m/d H:i:s').' --- '.$sql.PHP_EOL);
	fclose($f);
}
//echo $sql;
sqlclose($conn,'sqlite');
echo json_encode($listdata);
?>