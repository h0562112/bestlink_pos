<?php
include_once '../../../tool/myerrorlog.php';
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);

$setup=parse_ini_file('../../../database/setup.ini',true);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
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

if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(file_exists('../../syspram/paper-'.$init['init']['firlan'].'.ini')){
	$paper=parse_ini_file('../../syspram/paper-'.$init['init']['firlan'].'.ini',true);
}
else{
	$paper='-1';
}
if(file_exists('../../syspram/buttons-'.$init['init']['firlan'].'.ini')){
	$button=parse_ini_file('../../syspram/buttons-'.$init['init']['firlan'].'.ini',true);
}
else{
	$button='-1';
}
if(isset($print['item']['textfont'])){
}
else{
	$print['item']['textfont']="微軟正黑體";
}
if(file_exists('../../../database/type.ini')){
	$type=parse_ini_file('../../../database/type.ini',true);
}
else{
	$type='-1';
}
$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
if(file_exists('../../../database/otherpay.ini')){
	$otherpay=parse_ini_file('../../../database/otherpay.ini',true);
}
else{
	$otherpay='-1';
}
if(isset($_POST['papbizdateS'])&&$_POST['papbizdateS']!=''){
	$dbdate=$_POST['papbizdateS'];
}
else{
	$dbdate=$timeini['time']['bizdate'];
	//$dbdate=$machinedata['basic']['bizdate'];
}

if(isset($init['init']['posdvr'])&&$init['init']['posdvr']=='1'){//錢都錄
	$tempposdvr=date('YmdHis');
	$posdvr=fopen('../../../print/posdvr/'.$tempposdvr.';'.$_POST['machinename'].'.txt','w');
	$tempdvrcontent='';
	echo $tempposdvr.';'.$_POST['machinename'].'-';
}
else{
}

if(file_exists('../../../database/sale/SALES_'.substr($dbdate,0,6).'.db')){
	if(file_exists('../../../database/sale/cover.db')){
		$conn=sqlconnect('../../../database/sale','cover.db','','','','sqlite');
		$sql='SELECT coverbizdate,coverzcounter,usercode,username,bizdate,consecnumber,salesttlamt,tax1,tax2,tax3,tax4,tax9,(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,1,posTA1-1) END) pointTA1,(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,posTA1+1) END) ta1,(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,1,posTA2-1) END) pointTA2,(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,posTA2+1) END) ta2,(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,1,posTA3-1) END) pointTA3,(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,posTA3+1) END) ta3,(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,1,posTA4-1) END) pointTA4,(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,posTA4+1) END) ta4,(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,1,posTA5-1) END) pointTA5,(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,posTA5+1) END) ta5,(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,1,posTA6-1) END) pointTA6,(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,posTA6+1) END) ta6,(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,1,posTA7-1) END) pointTA7,(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,posTA7+1) END) ta7,(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,1,posTA8-1) END) pointTA8,(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,posTA8+1) END) ta8,(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,1,posTA9-1) END) pointTA9,(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,posTA9+1) END) ta9,(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,1,posTA10-1) END) pointTA10,(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,posTA10+1) END) ta10,(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) nontax';
		$checkintella='PRAGMA table_info(list)';
		$allcolumn=sqlquery($conn,$checkintella,'sqlite');
		if(!in_array('intella',array_column($allcolumn,'name'))){
			//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
			$insertsql='ALTER TABLE list ADD COLUMN intella TEXT';
			sqlnoresponse($conn,$insertsql,'sqlite');
		}
		else{
			//$sql=$sql.',intella';
		}
		$sql=$sql.',intella';
		//2021/8/18
		//$checknidin='PRAGMA table_info(list)';
		//$allcolumn=sqlquery($conn,$checknidin,'sqlite');
		if(!in_array('nidin',array_column($allcolumn,'name'))){
			//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
			$insertsql='ALTER TABLE list ADD COLUMN nidin TEXT';
			sqlnoresponse($conn,$insertsql,'sqlite');
		}
		else{
			//$sql=$sql.',nidin';
		}
		$sql=$sql.',nidin';
		if(!isset($otherpay)){
		}
		else{
			foreach($otherpay as $io=>$iv){
				if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
				}
				else{
					$sql=$sql.',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',1,pos'.strtoupper($iv['location']).'-1) END) point'.$iv['location'].',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',pos'.strtoupper($iv['location']).'+1) END) '.$iv['location'];
				}
			}
		}
		$sql=$sql.',nbchkdate,nbchktime,nbchknumber,createdatetime,state FROM (SELECT *,INSTR(ta1,"=") AS posTA1,INSTR(ta2,"=") AS posTA2,INSTR(ta3,"=") AS posTA3,INSTR(ta4,"=") AS posTA4,INSTR(ta5,"=") AS posTA5,INSTR(ta6,"=") AS posTA6,INSTR(ta7,"=") AS posTA7,INSTR(ta8,"=") AS posTA8,INSTR(ta9,"=") AS posTA9,INSTR(ta10,"=") AS posTA10,INSTR(nontax,"=") AS posNONTAX';
		if(!isset($otherpay)){
		}
		else{
			foreach($otherpay as $io=>$iv){
				if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
				}
				else{
					$sql=$sql.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
				}
			}
		}
		$sql=$sql.' FROM list WHERE bizdate';
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
			$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
		}
		else{
			$sql=$sql.'="'.$dbdate.'"';
		}
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
			if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
				$sql=$sql.' AND coverzcounter="'.$_POST['zcounter'].'"';
			}
			else if(isset($_POST['zcounter'])){
				//$sql=$sql.'coverzcounter="'.$_POST['zcounter'].'"';
			}
			else{
				$sql=$sql.' AND coverzcounter="'.$timeini['time']['zcounter'].'"';
				//$sql=$sql.' AND coverzcounter="'.$machinedata['basic']['zcounter'].'"';
			}
		}
		else{
			$sql=$sql.' AND coverzcounter="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND coverzcounter="'.$machinedata['basic']['zcounter'].'"';
		}
		$sql=$sql.' AND nbchknumber IS NULL)';
		//echo $sql;
		$futurecover=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
	}
	else{
	}
	$conn=sqlconnect("../../../database/sale","SALES_".substr($dbdate,0,6).".db","","","","sqlite");
	$checkintella='PRAGMA table_info(CST011)';
	$allcolumn=sqlquery($conn,$checkintella,'sqlite');
	if(!in_array('intella',array_column($allcolumn,'name'))){
		//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
		$insertsql='ALTER TABLE CST011 ADD COLUMN intella TEXT';
		sqlnoresponse($conn,$insertsql,'sqlite');
	}
	else{
		//$sql=$sql.',intella';
	}
	$sql=$sql.',intella';
	//2021/8/18
	//$checknidin='PRAGMA table_info(list)';
	//$allcolumn=sqlquery($conn,$checknidin,'sqlite');
	if(!in_array('nidin',array_column($allcolumn,'name'))){
		//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
		$insertsql='ALTER TABLE CST011 ADD COLUMN nidin TEXT';
		sqlnoresponse($conn,$insertsql,'sqlite');
	}
	else{
		//$sql=$sql.',nidin';
	}
	$checkintella='PRAGMA table_info(tempCST011)';
	$allcolumn=sqlquery($conn,$checkintella,'sqlite');
	if(!in_array('intella',array_column($allcolumn,'name'))){
		//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
		$insertsql='ALTER TABLE tempCST011 ADD COLUMN intella TEXT';
		sqlnoresponse($conn,$insertsql,'sqlite');
	}
	else{
		//$sql=$sql.',intella';
	}
	$sql=$sql.',intella';
	//2021/8/18
	//$checknidin='PRAGMA table_info(list)';
	//$allcolumn=sqlquery($conn,$checknidin,'sqlite');
	if(!in_array('nidin',array_column($allcolumn,'name'))){
		//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
		$insertsql='ALTER TABLE tempCST011 ADD COLUMN nidin TEXT';
		sqlnoresponse($conn,$insertsql,'sqlite');
	}
	else{
		//$sql=$sql.',nidin';
	}
	$sql='SELECT TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,SUM(UNITPRICE) AS UNITPRICE,SUM(AMT) AS AMT,ZCOUNTER,REMARKS,CREATEDATETIME,SUM(UNITPRICE) AS totalmemmoney FROM CST012 WHERE BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	$sql=$sql.' AND LINENUMBER="paymemmoney" AND DTLMODE="9" AND DTLTYPE="9" AND DTLFUNC="99" ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.' AND TERMINALNUMBER="'.$_POST['machinename'].'" ';
	}
	else{//帳務以主機為主體計算
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'coverzcounter="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND coverzcounter="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND coverzcounter="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.' GROUP BY ITEMNAME ORDER BY CAST(SUBSTR(ITEMNAME,LENGTH("'.$setup['basic']['story'].'")) AS INT) ASC';
	//echo $sql;
	$memmoney=sqlquery($conn,$sql,'sqlite');
	if(sizeof($memmoney)>1){
		for($i=1;$i<sizeof($memmoney);$i++){
			$memmoney[0]['totalmemmoney']=floatval($memmoney[0]['totalmemmoney'])+floatval($memmoney[$i]['totalmemmoney']);
		}
	}
	else{
	}
	//echo $sql;
	$sql='SELECT (SELECT SUM(AMT) FROM CST012 WHERE BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	$sql=$sql.' AND DTLMODE="1" AND DTLTYPE="1" AND DTLFUNC="01" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.' AND TERMINALNUMBER="'.$_POST['machinename'].'" ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.' AND BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'CST012.ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND CST012.ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND CST012.ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.')) AS initmoney,(SELECT SUM(TAX1) FROM CST011 WHERE ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.'TERMINALNUMBER="'.$_POST['machinename'].'" AND ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.'BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.' AND NBCHKNUMBER IS NULL) AS charge,(SELECT SUM(SALESTTLAMT) FROM CST011 WHERE ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.'TERMINALNUMBER="'.$_POST['machinename'].'" AND ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.'BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.' AND NBCHKNUMBER IS NULL) AS afmoney,';
	$sql=$sql.'(SELECT SUM(TAX2) FROM CST011 WHERE ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.'TERMINALNUMBER="'.$_POST['machinename'].'" AND ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.'BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	if(isset($futurecover[0])){//當日帳單有修改付款
		$sql=$sql.' AND CONSECNUMBER NOT IN ("'.(implode('","',array_column($futurecover,'consecnumber'))).'")';
	}
	else{
	}
	$sql=$sql.' AND NBCHKNUMBER IS NULL) AS realymoney';
	//echo $sql;
	$salemoney=sqlquery($conn,$sql,'sqlite');
	
	if(isset($futurecover[0])){//當日帳單有修改付款(計算現金部分)
		$coverrealmoney=0;
		for($i=0;$i<sizeof($futurecover);$i++){
			$coverrealmoney=floatval($coverrealmoney)+floatval($futurecover[$i]['tax2']);
		}
		//echo $coverrealmoney;
	}
	else{
	}

	$sql='SELECT TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,SALESTTLQTY,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TAX10,(CASE posTA1 WHEN 0 THEN TA1 ELSE SUBSTR(TA1,1,posTA1-1) END) pointTA1,(CASE posTA1 WHEN 0 THEN TA1 ELSE SUBSTR(TA1,posTA1+1) END) TA1,(CASE posTA2 WHEN 0 THEN TA2 ELSE SUBSTR(TA2,1,posTA2-1) END) pointTA2,(CASE posTA2 WHEN 0 THEN TA2 ELSE SUBSTR(TA2,posTA2+1) END) TA2,(CASE posTA3 WHEN 0 THEN TA3 ELSE SUBSTR(TA3,1,posTA3-1) END) pointTA3,(CASE posTA3 WHEN 0 THEN TA3 ELSE SUBSTR(TA3,posTA3+1) END) TA3,(CASE posTA4 WHEN 0 THEN TA4 ELSE SUBSTR(TA4,1,posTA4-1) END) pointTA4,(CASE posTA4 WHEN 0 THEN TA4 ELSE SUBSTR(TA4,posTA4+1) END) TA4,(CASE posTA5 WHEN 0 THEN TA5 ELSE SUBSTR(TA5,1,posTA5-1) END) pointTA5,(CASE posTA5 WHEN 0 THEN TA5 ELSE SUBSTR(TA5,posTA5+1) END) TA5,(CASE posTA6 WHEN 0 THEN TA6 ELSE SUBSTR(TA6,1,posTA6-1) END) pointTA6,(CASE posTA6 WHEN 0 THEN TA6 ELSE SUBSTR(TA6,posTA6+1) END) TA6,(CASE posTA7 WHEN 0 THEN TA7 ELSE SUBSTR(TA7,1,posTA7-1) END) pointTA7,(CASE posTA7 WHEN 0 THEN TA7 ELSE SUBSTR(TA7,posTA7+1) END) TA7,(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(TA8,1,posTA8-1) END) pointTA8,(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(TA8,posTA8+1) END) TA8,(CASE posTA9 WHEN 0 THEN TA9 ELSE SUBSTR(TA9,1,posTA9-1) END) pointTA9,(CASE posTA9 WHEN 0 THEN TA9 ELSE SUBSTR(TA9,posTA9+1) END) TA9,(CASE posTA10 WHEN 0 THEN TA10 ELSE SUBSTR(TA10,1,posTA10-1) END) pointTA10,(CASE posTA10 WHEN 0 THEN TA10 ELSE SUBSTR(TA10,posTA10+1) END) TA10,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,posNONTAX+1) END) NONTAX';
	$checkintella='PRAGMA table_info(CST011)';
	$allcolumn=sqlquery($conn,$checkintella,'sqlite');
	if(!in_array('intella',array_column($allcolumn,'name'))){
		//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
		$insertsql='ALTER TABLE list ADD COLUMN intella TEXT';
		sqlnoresponse($conn,$insertsql,'sqlite');
	}
	else{
		//$sql=$sql.',intella';
	}
	$sql=$sql.',intella';
	//2021/8/18
	//$checknidin='PRAGMA table_info(CST011)';
	//$allcolumn=sqlquery($conn,$checknidin,'sqlite');
	if(!in_array('nidin',array_column($allcolumn,'name'))){
		//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
		$insertsql='ALTER TABLE list ADD COLUMN nidin TEXT';
		sqlnoresponse($conn,$insertsql,'sqlite');
	}
	else{
		//$sql=$sql.',nidin';
	}
	$sql=$sql.',nidin';
	if($otherpay=='-1'){
	}
	else{
		foreach($otherpay as $io=>$iv){
			if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
			}
			else{
				$sql=$sql.',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',1,pos'.strtoupper($iv['location']).'-1) END) point'.$iv['location'].',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',pos'.strtoupper($iv['location']).'+1) END) '.$iv['location'];
			}
		}
	}
	$sql=$sql.',EX1,EX2,EX3,EX4,EX5,EX6,EX7,EX8,EX9,EX10,PROFITAMT,COVER,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,POINTTARGET,POINTPREVIOUS,POINTGOT,POINTUSED,OPENCHKDATE,OPENCHKTIME,NBCHKDATE,NBCHKTIME,NBCHKNUMBER,TABLENUMBER,RELINVOICEDATE,RELINVOICETIME,RELINVOICENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,CREDITCARD FROM (SELECT *,INSTR(TA1,"=") AS posTA1,INSTR(TA2,"=") AS posTA2,INSTR(TA3,"=") AS posTA3,INSTR(TA4,"=") AS posTA4,INSTR(TA5,"=") AS posTA5,INSTR(TA6,"=") AS posTA6,INSTR(TA7,"=") AS posTA7,INSTR(TA8,"=") AS posTA8,INSTR(TA9,"=") AS posTA9,INSTR(TA10,"=") AS posTA10,INSTR(NONTAX,"=") AS posNONTAX';
	if($otherpay=='-1'){
	}
	else{
		foreach($otherpay as $io=>$iv){
			if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
			}
			else{
				$sql=$sql.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
			}
		}
	}
	$sql=$sql.' FROM CST011 WHERE ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.'TERMINALNUMBER="'.$_POST['machinename'].'" AND ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.'BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.' AND NBCHKNUMBER IS NULL)';
	//echo $sql;
	$listdata=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT (SELECT SUM(AMT) FROM CST012 WHERE BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	$sql=$sql.' AND ITEMCODE="item" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.' AND TERMINALNUMBER="'.$_POST['machinename'].'" ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.' AND BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'" ';
	}
	else{
		$sql=$sql.'="'.$dbdate.'" ';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
	}
	$sql=$sql.')) AS dis1,(SELECT SUM(AMT) FROM CST012 WHERE BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	$sql=$sql.' AND (DTLMODE!="1" OR DTLTYPE!="1" OR DTLFUNC!="01") AND (ITEMCODE="list" OR ITEMCODE="autodis") AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.' AND TERMINALNUMBER="'.$_POST['machinename'].'" ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.' AND BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'" ';
	}
	else{
		$sql=$sql.'="'.$dbdate.'" ';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND CST012.ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND CST012.ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.')) AS dis2,(SELECT SUM(AMT) FROM CST012 WHERE BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	$sql=$sql.' AND ITEMCODE="member" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.' AND TERMINALNUMBER="'.$_POST['machinename'].'" ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.' AND BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'" ';
	}
	else{
		$sql=$sql.'="'.$dbdate.'" ';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
	}
	$sql=$sql.')) AS dis3';
	//echo $sql;
	$dis=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT DISTINCT CST012.ITEMCODE AS ITEMCODE,CST012.ITEMDEPTCODE AS ITEMDEPTCODE,CST012.ITEMNAME AS ITEMNAME,SUM(CST012.QTY) AS QTY,CST012.UNITPRICELINK AS UNITPRICE,SUM(CST012.AMT+a.AMT) AS AMT FROM CST012 ';
	$sql=$sql.' JOIN (SELECT * FROM CST012 WHERE CST012.ITEMCODE="item" AND CST012.BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	$sql=$sql.') AS a ON CST012.CONSECNUMBER=a.CONSECNUMBER AND CAST(CST012.LINENUMBER AS INT)+1=CAST(a.LINENUMBER AS INT) AND CST012.ZCOUNTER=a.ZCOUNTER WHERE CST012.BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	$sql=$sql.' AND (CST012.ITEMCODE!="list" OR CST012.ITEMCODE!="list" OR CST012.ITEMCODE!="autodis") AND CST012.CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.' AND TERMINALNUMBER="'.$_POST['machinename'].'" ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.' AND BIZDATE ';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND CST011.ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND CST011.ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.') GROUP BY CST012.ITEMNAME,CST012.UNITPRICELINK ORDER BY CST012.ITEMDEPTCODE ASC,CST012.ITEMCODE ASC';
	//echo $sql;
	$listdetail=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT BIZDATE,CONSECNUMBER,(SALESTTLAMT+TAX1) AS SALESTTLAMT FROM CST011 WHERE ';
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		$sql=$sql.'TERMINALNUMBER="'.$_POST['machinename'].'" AND ';
	}
	else{//帳務以主機為主體計算
	}
	$sql=$sql.'BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.' AND NBCHKNUMBER="Y"';
	$voidlist=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT DTLMODE,SUM(AMT) AS AMT,ITEMNAME,ITEMDEPTCODE FROM CST012 WHERE BIZDATE ';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$dbdate.'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
	}
	$sql=$sql.' AND (DTLMODE="4" OR DTLMODE="3") AND DTLTYPE="1" AND DTLFUNC="01" GROUP BY DTLMODE,ITEMNAME,ITEMDEPTCODE';
	//echo $sql;
	$moeyout=sqlquery($conn,$sql,'sqlite');
	if($init['init']['useinv']==1){
		if(intval(substr($dbdate,4,2))%2==1){
			if(intval(substr($dbdate,4,2))<9){
				$invdate=substr($dbdate,0,4).'0'.(intval(substr($dbdate,4,2))+1);
			}
			else{
				$invdate=substr($dbdate,0,4).(intval(substr($dbdate,4,2))+1);
			}
		}
		else{
			$invdate=substr($dbdate,0,6);
		}
		$sql='SELECT INVOICENUMBER FROM CST011 WHERE ';
		if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
			$sql=$sql.'TERMINALNUMBER="'.$_POST['machinename'].'" AND ';
		}
		else{//帳務以主機為主體計算
		}
		$sql=$sql.'BIZDATE';
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
			$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
		}
		else{
			$sql=$sql.'="'.$dbdate.'"';
		}
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
			if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
				$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
			}
			else if(isset($_POST['zcounter'])){
				//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
			}
			else{
				$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
				//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
			}
		}
		else{
			$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
		}
		$sql=$sql.' AND NBCHKNUMBER IS NULL';
		$invconsecnumber=sqlquery($conn,$sql,'sqlite');
		$list='';
		foreach($invconsecnumber as $con){
			if(strlen($list)==0){
				$list='"'.$con['INVOICENUMBER'].'"';
			}
			else{
				$list=$list.',"'.$con['INVOICENUMBER'].'"';
			}
		}
		if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
			if(file_exists("../../../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db")){
				$conn1=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
			}
			else{
			}
		}
		else{//帳務依主機為主體計算
			if(file_exists("../../../database/sale/".$invdate."/invdata_".$invdate."_m1.db")){
				$conn1=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_m1.db","","","","sqlite");
			}
			else{
			}
		}
		if(isset($conn1)){
			$sql='SELECT * FROM invlist WHERE invnumber IN ('.$list.') ORDER BY invnumber ASC';
			$invlist=sqlquery($conn1,$sql,'sqlite');
			$sql='SELECT INVOICENUMBER FROM CST011 WHERE ';
			if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
				$sql=$sql.'TERMINALNUMBER="'.$_POST['machinename'].'" AND ';
			}
			else{//帳務以主機為主體計算
			}
			$sql=$sql.'BIZDATE';
			if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
				$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
			}
			else{
				$sql=$sql.'="'.$dbdate.'"';
			}
			if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
				if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
					$sql=$sql.' AND ZCOUNTER="'.$_POST['zcounter'].'"';
				}
				else if(isset($_POST['zcounter'])){
					//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
				}
				else{
					$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
					//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
				}
			}
			else{
				$sql=$sql.' AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
				//$sql=$sql.' AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'"';
			}
			$sql=$sql.' AND NBCHKNUMBER="Y"';
			$invconsecnumber=sqlquery($conn,$sql,'sqlite');
			$list='';
			foreach($invconsecnumber as $con){
				if(strlen($list)==0){
					$list='"'.$con['INVOICENUMBER'].'"';
				}
				else{
					$list=$list.',"'.$con['INVOICENUMBER'].'"';
				}
			}
			$sql='SELECT * FROM invlist WHERE invnumber IN ('.$list.')';
			$invvoidlist=sqlquery($conn1,$sql,'sqlite');
			//print_r($invvoidlist);
			sqlclose($conn1,'sqlite');
		}
		else{
		}
	}
	else{
	}
	sqlclose($conn,'sqlite');
	if(file_exists('../../../database/sale/Cover.db')){
		$conn=sqlconnect('../../../database/sale','Cover.db','','','','sqlite');
		$sql='SELECT SUM(tax2) AS tax2,SUM(tax3) AS tax3,SUM(tax4) AS tax4,SUM(tax9) AS tax9,SUM(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,1,posTA1-1) END) AS pointTA1,SUM(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,posTA1+1) END) AS TA1,SUM(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,1,posTA2-1) END) AS pointTA2,SUM(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,posTA2+1) END) AS TA2,SUM(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,1,posTA3-1) END) AS pointTA3,SUM(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,posTA3+1) END) AS TA3,SUM(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,1,posTA4-1) END) AS pointTA4,SUM(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,posTA4+1) END) AS TA4,SUM(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,1,posTA5-1) END) AS pointTA5,SUM(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,posTA5+1) END) AS TA5,SUM(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,1,posTA6-1) END) AS pointTA6,SUM(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,posTA6+1) END) AS TA6,SUM(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,1,posTA7-1) END) AS pointTA7,SUM(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,posTA7+1) END) AS TA7,SUM(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,1,posTA8-1) END) AS pointTA8,SUM(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,posTA8+1) END) AS TA8,SUM(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,1,posTA9-1) END) AS pointTA9,SUM(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,posTA9+1) END) AS TA9,SUM(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,1,posTA10-1) END) AS pointTA10,SUM(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,posTA10+1) END) AS TA10,SUM(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) AS pointNONTAX,SUM(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) AS NONTAX';
		$checkintella='PRAGMA table_info(list)';
		$allcolumn=sqlquery($conn,$checkintella,'sqlite');
		if(!in_array('intella',array_column($allcolumn,'name'))){
			//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
			$insertsql='ALTER TABLE list ADD COLUMN intella TEXT';
			sqlnoresponse($conn,$insertsql,'sqlite');
		}
		else{
			//$sql=$sql.',intella';
		}
		$sql=$sql.',intella';
		//2021/8/18
		//$checknidin='PRAGMA table_info(list)';
		//$allcolumn=sqlquery($conn,$checknidin,'sqlite');
		if(!in_array('nidin',array_column($allcolumn,'name'))){
			//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
			$insertsql='ALTER TABLE list ADD COLUMN nidin TEXT';
			sqlnoresponse($conn,$insertsql,'sqlite');
		}
		else{
			//$sql=$sql.',nidin';
		}
		$sql=$sql.',nidin';
		if($otherpay=='-1'){
		}
		else{
			foreach($otherpay as $io=>$iv){
				if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
				}
				else{
					$sql=$sql.',SUM(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',1,pos'.strtoupper($iv['location']).'-1) END) AS point'.$iv['location'].',SUM(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',pos'.strtoupper($iv['location']).'+1) END) AS '.$iv['location'];
				}
			}
		}
		$sql=$sql.' FROM (SELECT *,INSTR(ta1,"=") AS posTA1,INSTR(ta2,"=") AS posTA2,INSTR(ta3,"=") AS posTA3,INSTR(ta4,"=") AS posTA4,INSTR(ta5,"=") AS posTA5,INSTR(ta6,"=") AS posTA6,INSTR(ta7,"=") AS posTA7,INSTR(ta8,"=") AS posTA8,INSTR(ta9,"=") AS posTA9,INSTR(ta10,"=") AS posTA10,INSTR(nontax,"=") AS posNONTAX';
		if($otherpay=='-1'){
		}
		else{
			foreach($otherpay as $io=>$iv){
				if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
				}
				else{
					$sql=$sql.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
				}
			}
		}
		$sql=$sql.' FROM list WHERE bizdate';
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
			$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
		}
		else{
			$sql=$sql.'="'.$dbdate.'"';
		}
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
			if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
				$sql=$sql.' AND coverzcounter="'.$_POST['zcounter'].'"';
			}
			else if(isset($_POST['zcounter'])){
				//$sql=$sql.'coverzcounter="'.$_POST['zcounter'].'"';
			}
			else{
				$sql=$sql.' AND coverzcounter="'.$timeini['time']['zcounter'].'"';
				//$sql=$sql.' AND coverzcounter="'.$machinedata['basic']['zcounter'].'"';
			}
		}
		else{
			$sql=$sql.' AND coverzcounter="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND coverzcounter="'.$machinedata['basic']['zcounter'].'"';
		}
		$sql=$sql.' AND nbchknumber IS NULL)';
		//echo $sql;
		$cover=sqlquery($conn,$sql,'sqlite');
		//echo $cover[0]['tax2'].'-';
		$sql='SELECT coverbizdate,coverzcounter,usercode,username,bizdate,consecnumber,salesttlamt,tax1,tax2,tax3,tax4,tax9,(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,1,posTA1-1) END) pointTA1,(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,posTA1+1) END) ta1,(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,1,posTA2-1) END) pointTA2,(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,posTA2+1) END) ta2,(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,1,posTA3-1) END) pointTA3,(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,posTA3+1) END) ta3,(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,1,posTA4-1) END) pointTA4,(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,posTA4+1) END) ta4,(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,1,posTA5-1) END) pointTA5,(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,posTA5+1) END) ta5,(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,1,posTA6-1) END) pointTA6,(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,posTA6+1) END) ta6,(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,1,posTA7-1) END) pointTA7,(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,posTA7+1) END) ta7,(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,1,posTA8-1) END) pointTA8,(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,posTA8+1) END) ta8,(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,1,posTA9-1) END) pointTA9,(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,posTA9+1) END) ta9,(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,1,posTA10-1) END) pointTA10,(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,posTA10+1) END) ta10,(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) nontax';
		$checkintella='PRAGMA table_info(list)';
		$allcolumn=sqlquery($conn,$checkintella,'sqlite');
		if(!in_array('intella',array_column($allcolumn,'name'))){
			//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
			$insertsql='ALTER TABLE list ADD COLUMN intella TEXT';
			sqlnoresponse($conn,$insertsql,'sqlite');
		}
		else{
			//$sql=$sql.',intella';
		}
		$sql=$sql.',intella';
		//2021/8/18
		$checknidin='PRAGMA table_info(list)';
		$allcolumn=sqlquery($conn,$checknidin,'sqlite');
		if(!in_array('nidin',array_column($allcolumn,'name'))){
			//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
			$insertsql='ALTER TABLE list ADD COLUMN nidin TEXT';
			sqlnoresponse($conn,$insertsql,'sqlite');
		}
		else{
			//$sql=$sql.',nidin';
		}
		$sql=$sql.',nidin';
		if($otherpay=='-1'){
		}
		else{
			foreach($otherpay as $io=>$iv){
				if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
				}
				else{
					$sql=$sql.',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',1,pos'.strtoupper($iv['location']).'-1) END) point'.$iv['location'].',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',pos'.strtoupper($iv['location']).'+1) END) '.$iv['location'];
				}
			}
		}
		$sql=$sql.',nbchkdate,nbchktime,nbchknumber,createdatetime,state FROM (SELECT *,INSTR(ta1,"=") AS posTA1,INSTR(ta2,"=") AS posTA2,INSTR(ta3,"=") AS posTA3,INSTR(ta4,"=") AS posTA4,INSTR(ta5,"=") AS posTA5,INSTR(ta6,"=") AS posTA6,INSTR(ta7,"=") AS posTA7,INSTR(ta8,"=") AS posTA8,INSTR(ta9,"=") AS posTA9,INSTR(ta10,"=") AS posTA10,INSTR(nontax,"=") AS posNONTAX';
		if($otherpay=='-1'){
		}
		else{
			foreach($otherpay as $io=>$iv){
				if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
				}
				else{
					$sql=$sql.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
				}
			}
		}
		$sql=$sql.' FROM list WHERE bizdate';
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
			$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
		}
		else{
			$sql=$sql.'="'.$dbdate.'"';
		}
		if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
			if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
				$sql=$sql.' AND coverzcounter="'.$_POST['zcounter'].'"';
			}
			else if(isset($_POST['zcounter'])){
				//$sql=$sql.'coverzcounter="'.$_POST['zcounter'].'"';
			}
			else{
				$sql=$sql.' AND coverzcounter="'.$timeini['time']['zcounter'].'"';
				//$sql=$sql.' AND coverzcounter="'.$machinedata['basic']['zcounter'].'"';
			}
		}
		else{
			$sql=$sql.' AND coverzcounter="'.$timeini['time']['zcounter'].'"';
			//$sql=$sql.' AND coverzcounter="'.$machinedata['basic']['zcounter'].'"';
		}
		$sql=$sql.' AND nbchknumber IS NULL)';
		//echo $sql;
		$tempcoverlist=sqlquery($conn,$sql,'sqlite');
		$coverlist=array();
		if(sizeof($tempcoverlist)==0){
		}
		else{
			foreach($tempcoverlist as $tcl){
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['tax2']=$tcl['tax2'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['tax3']=$tcl['tax3'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['tax4']=$tcl['tax4'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['tax9']=$tcl['tax9'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta1']=$tcl['ta1'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta2']=$tcl['ta2'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta3']=$tcl['ta3'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta4']=$tcl['ta4'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta5']=$tcl['ta5'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta6']=$tcl['ta6'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta7']=$tcl['ta7'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta8']=$tcl['ta8'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta9']=$tcl['ta9'];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['ta10']=$tcl['ta10'];
				if($otherpay=='-1'){
				}
				else{
					foreach($otherpay as $oi=>$ov){
						if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
						}
						else{
							$coverlist[$tcl['bizdate']][$tcl['consecnumber']][$ov['location']]=$tcl[$ov['location']];
						}
					}
				}
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['nontax']=$tcl['nontax'];
				if(isset($tcl['intella'])){
					$tempintella=preg_split('/:/',$tcl['intella']);
				}
				else{
					$tempintella='';
				}
				if(isset($tempintella[2])){
				}
				else{
					$tempintella[2]=0;
				}
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['intella']=$tempintella[2];
				//2021/8/18
				if(isset($tcl['nidin'])){
					$tempnidin=preg_split('/:/',$tcl['nidin']);
				}
				else{
					$tempnidin='';
				}
				if(isset($tempnidin[2])){
				}
				else{
					$tempnidin[2]=0;
				}
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['nidin']=$tempnidin[2];
				$coverlist[$tcl['bizdate']][$tcl['consecnumber']]['state']='1';
			}
		}
		//print_r($coverlist);
		sqlclose($conn,'sqlite');
	}
	else{
	}
	if(sizeof($listdetail)==0){
		echo 'listdata is empty';
	}
	else{
		date_default_timezone_set($init['init']['settime']);
		$date=date('Y/m/d');
		$time=date('H:i:s');
		$rearlist=array();//分析類別
		//$salemoney=0;//銷售總額
		//$itemdis=0;//單品折扣
		$listdis=0;//帳單折扣
		$totallist1=0;//帳單總數
		$money1=0;//總金額
		$totallist2=0;
		$money2=0;
		$totallist3=0;
		$money3=0;
		$sumotherpay=0;
		$cash=0;
		$cashcomm=0;
		$other['TA1']=0;
		$other['TA2']=0;
		$other['TA3']=0;
		$other['TA4']=0;
		$other['TA5']=0;
		$other['TA6']=0;
		$other['TA7']=0;
		$other['TA8']=0;
		$other['TA9']=0;
		$other['TA10']=0;
		$other['NONTAX']=0;
		$other['intella']=0;
		$other['nidin']=0;//2021/8/18 你訂
		if($otherpay=='-1'){
		}
		else{
			foreach($otherpay as $oi=>$ov){
				if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
				}
				else{
					$other[$ov['location']]=0;
				}
			}
		}
		$list1[0]['qty']=0;
		$list1[0]['amt']=0;
		$list1[0]['TAX5']=0;
		$list1[0]['TAX6']=0;
		$list1[0]['TAX7']=0;
		$list2[0]['qty']=0;
		$list2[0]['amt']=0;
		$list2[0]['TAX5']=0;
		$list2[0]['TAX6']=0;
		$list2[0]['TAX7']=0;
		$list3[0]['qty']=0;
		$list3[0]['amt']=0;
		$list3[0]['TAX5']=0;
		$list3[0]['TAX6']=0;
		$list3[0]['TAX7']=0;
		$list4[0]['qty']=0;
		$list4[0]['amt']=0;
		$list4[0]['TAX5']=0;
		$list4[0]['TAX6']=0;
		$list4[0]['TAX7']=0;
		if($salemoney[0]['initmoney']==''){
			$initmoney=0;
		}
		else{
			$initmoney=$salemoney[0]['initmoney'];
		}
		if($salemoney[0]['charge']==''){
			$charge=0;
		}
		else{
			$charge=$salemoney[0]['charge'];
		}
		if($salemoney[0]['afmoney']==''){
			$afmoney=0;
		}
		else{
			$afmoney=$salemoney[0]['afmoney'];
		}
		if($salemoney[0]['realymoney']==''){
			$realysale=0;
		}
		else{
			$realysale=$salemoney[0]['realymoney'];
		}
		//print_r($salemoney);
		//echo $realysale;
		if(file_exists('../../../database/'.$setup['basic']['company'].'-rear.ini')){
			$rearname=parse_ini_file('../../../database/'.$setup['basic']['company'].'-rear.ini',true);
			$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
			$sql='SELECT inumber,reartype FROM itemsdata WHERE inumber IN (';
			for($i=0;$i<sizeof($listdetail);$i++){
				if($i==0){
					$sql=$sql.intval($listdetail[$i]['ITEMCODE']);
				}
				else{
					$sql=$sql.','.intval($listdetail[$i]['ITEMCODE']);
				}
			}
			$sql=$sql.')';
			//echo $sql;
			$temprear=sqlquery($conn,$sql,'sqlite');
			$rearmap=array();
			//print_r($temprear);
			//print_r($listdetail);
			sqlclose($conn,'sqlite');
			if(isset($temprear)&&sizeof($temprear)>0&&isset($temprear[0]['inumber'])){
				foreach($temprear as $tr){
					$rearmap[$tr['inumber']]=$tr['reartype'];
				}
				foreach($listdetail as $ld){
					if(strlen($ld['ITEMCODE'])<15||!isset($rearname[$rearmap[intval($ld['ITEMCODE'])]])){
					}
					else{
						if(isset($rearlist[$rearmap[intval($ld['ITEMCODE'])]]['QTY'])){
							$rearlist[$rearmap[intval($ld['ITEMCODE'])]]['QTY']=intval($rearlist[$rearmap[intval($ld['ITEMCODE'])]]['QTY'])+intval($ld['QTY']);
						}
						else{
							$rearlist[$rearmap[intval($ld['ITEMCODE'])]]['QTY']=intval($ld['QTY']);
							$rearlist[$rearmap[intval($ld['ITEMCODE'])]]['name']=$rearname[$rearmap[intval($ld['ITEMCODE'])]]['name'];
						}
					}
				}
				//print_r($rearlist);
			}
			else{
			}
			
		}
		else{
		}
		foreach($voidlist as $l){
			if(isset($coverlist)&&sizeof($coverlist)>0&&isset($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']])){
				$coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['state']='0';
				$cover[0]['tax2']=floatval($cover[0]['tax2'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax2']);
				$cover[0]['tax3']=floatval($cover[0]['tax3'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax3']);
				$cover[0]['tax4']=floatval($cover[0]['tax4'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax4']);
				$cover[0]['tax9']=floatval($cover[0]['tax9'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax9']);
				$cover[0]['TA1']=floatval($cover[0]['TA1'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta1']);
				$cover[0]['TA2']=floatval($cover[0]['TA2'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta2']);
				$cover[0]['TA3']=floatval($cover[0]['TA3'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta3']);
				$cover[0]['TA4']=floatval($cover[0]['TA4'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta4']);
				$cover[0]['TA5']=floatval($cover[0]['TA5'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta5']);
				$cover[0]['TA6']=floatval($cover[0]['TA6'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta6']);
				$cover[0]['TA7']=floatval($cover[0]['TA7'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta7']);
				$cover[0]['TA8']=floatval($cover[0]['TA8'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta8']);
				$cover[0]['TA9']=floatval($cover[0]['TA9'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta9']);
				$cover[0]['TA10']=floatval($cover[0]['TA10'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta10']);
				if($otherpay=='-1'){
				}
				else{
					foreach($otherpay as $oi=>$ov){
						if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
						}
						else{
							$cover[0][$ov['location']]=floatval($cover[0][$ov['location']])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']][$ov['location']]);
						}
					}
				}
				$cover[0]['NONTAX']=floatval($cover[0]['NONTAX'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nontax']);
				if(isset($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['intella'])){
					$tempintella=preg_split('/:/',$coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['intella']);
				}
				else{
					$tempintella='';
				}
				if(isset($tempintella[2])){
				}
				else{
					$tempintella[2]=0;
				}
				$cover[0]['intella']=floatval($cover[0]['intella'])-floatval($tempintella[2]);
				//2021/8/18
				if(isset($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nidin'])){
					$tempnidin=preg_split('/:/',$coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nidin']);
					$temppaycode=preg_split('/-/',$tempnidin[0]);
					//2021/11/8 因為載具字元包含了 - ，因此在切割時也會同時被切到，需要另外補回來
					if(sizeof($temppaycode)>3){
						for($n=2;$n<(sizeof($temppaycode)-1);$n++){
							$temppaycode[1].='-'.$temppaycode[$n];
						}
						$temppaycode[2]=$temppaycode[(sizeof($temppaycode)-1)];
					}
					else{
					}
					if(isset($temppaycode[2])&&$temppaycode[2]=='10'){//2021/8/25 已在你訂線上支付
					}
					else{
						$tempnidin='';
					}
					/*if(isset($tempnidin[2])&&$tempnidin[2]=='10'){//2021/8/25 已在你訂線上支付
					}
					else{
						$tempnidin='';
					}*/
				}
				else{
					$tempnidin='';
				}
				if(isset($tempnidin[2])){
				}
				else{
					$tempnidin[2]=0;
				}
				$cover[0]['nidin']=floatval($cover[0]['nidin'])-floatval($tempnidin[2]);
			}
			else{
			}
		}
		if(isset($futurecover)){
			$tempfuture=array_column($futurecover,'consecnumber');
		}
		else{
		}
		//echo $realysale;
		foreach($listdata as $l){
			if(isset($coverlist)&&sizeof($coverlist)>0&&isset($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']])){//計算"當日"修改"當日"帳單的修改付款差額
				$coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['state']='0';
				$realysale=floatval($realysale)+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax2']);//$realysale=floatval($realysale)-floatval($l['TAX2'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax2']);//因為已經過濾掉修改帳單的金額，因此公式修改
				$cover[0]['tax2']=floatval($cover[0]['tax2'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax2']);
				$cover[0]['tax3']=floatval($cover[0]['tax3'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax3']);
				$cover[0]['tax4']=floatval($cover[0]['tax4'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax4']);
				$cover[0]['tax9']=floatval($cover[0]['tax9'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax9']);
				$cover[0]['TA1']=floatval($cover[0]['TA1'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta1']);
				$cover[0]['TA2']=floatval($cover[0]['TA2'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta2']);
				$cover[0]['TA3']=floatval($cover[0]['TA3'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta3']);
				$cover[0]['TA4']=floatval($cover[0]['TA4'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta4']);
				$cover[0]['TA5']=floatval($cover[0]['TA5'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta5']);
				$cover[0]['TA6']=floatval($cover[0]['TA6'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta6']);
				$cover[0]['TA7']=floatval($cover[0]['TA7'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta7']);
				$cover[0]['TA8']=floatval($cover[0]['TA8'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta8']);
				$cover[0]['TA9']=floatval($cover[0]['TA9'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta9']);
				$cover[0]['TA10']=floatval($cover[0]['TA10'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta10']);
				if($otherpay=='-1'){
				}
				else{
					foreach($otherpay as $oi=>$ov){
						if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
						}
						else{
							$cover[0][$ov['location']]=floatval($cover[0][$ov['location']])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']][$ov['location']]);
						}
					}
				}
				$cover[0]['NONTAX']=floatval($cover[0]['NONTAX'])-floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nontax']);
				if(isset($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['intella'])){
					$tempintella=preg_split('/:/',$coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['intella']);
				}
				else{
					$tempintella='';
				}
				if(isset($tempintella[2])){
				}
				else{
					$tempintella[2]=0;
				}
				$cover[0]['intella']=floatval($cover[0]['intella'])-floatval($tempintella[2]);
				//2021/8/18
				if(isset($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nidin'])){
					$tempnidin=preg_split('/:/',$coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nidin']);
					$temppaycode=preg_split('/-/',$tempnidin[0]);
					//2021/11/8 因為載具字元包含了 - ，因此在切割時也會同時被切到，需要另外補回來
					if(sizeof($temppaycode)>3){
						for($n=2;$n<(sizeof($temppaycode)-1);$n++){
							$temppaycode[1].='-'.$temppaycode[$n];
						}
						$temppaycode[2]=$temppaycode[(sizeof($temppaycode)-1)];
					}
					else{
					}
					if(isset($temppaycode[2])&&$temppaycode[2]=='10'){//2021/8/25 已在你訂線上支付
					}
					else{
						$tempnidin='';
					}
					/*if(isset($tempnidin[2])&&$tempnidin[2]=='10'){//2021/8/25 已在你訂線上支付
					}
					else{
						$tempnidin='';
					}*/
				}
				else{
					$tempnidin='';
				}
				if(isset($tempnidin[2])){
				}
				else{
					$tempnidin[2]=0;
				}
				$cover[0]['nidin']=floatval($cover[0]['nidin'])-floatval($tempnidin[2]);
				$cash=floatval($cash)+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax3']);
				$cashcomm=floatval($cashcomm)+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['tax9']);
				$sumotherpay=floatval($sumotherpay)+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta1'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta2'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta3'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta4'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta5'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta6'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta7'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta8'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta9'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta10']);
				$other['TA1']=floatval($other['TA1'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta1']);
				$other['TA2']=floatval($other['TA2'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta2']);
				$other['TA3']=floatval($other['TA3'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta3']);
				$other['TA4']=floatval($other['TA4'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta4']);
				$other['TA5']=floatval($other['TA5'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta5']);
				$other['TA6']=floatval($other['TA6'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta6']);
				$other['TA7']=floatval($other['TA7'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta7']);
				$other['TA8']=floatval($other['TA8'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta8']);
				$other['TA9']=floatval($other['TA9'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta9']);
				$other['TA10']=floatval($other['TA10'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['ta10']);
				$other['NONTAX']=floatval($other['NONTAX'])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nontax']);
				if(isset($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['intella'])){
					$tempintella=preg_split('/:/',$coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['intella']);
				}
				else{
					$tempintella='';
				}
				if(isset($tempintella[2])){
				}
				else{
					$tempintella[2]=0;
				}
				$other['intella']=floatval($other['intella'])-floatval($tempintella[2]);
				//2021/8/18
				if(isset($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nidin'])){
					$tempnidin=preg_split('/:/',$coverlist[$l['BIZDATE']][$l['CONSECNUMBER']]['nidin']);
					$temppaycode=preg_split('/-/',$tempnidin[0]);
					//2021/11/8 因為載具字元包含了 - ，因此在切割時也會同時被切到，需要另外補回來
					if(sizeof($temppaycode)>3){
						for($n=2;$n<(sizeof($temppaycode)-1);$n++){
							$temppaycode[1].='-'.$temppaycode[$n];
						}
						$temppaycode[2]=$temppaycode[(sizeof($temppaycode)-1)];
					}
					else{
					}
					if(isset($temppaycode[2])&&$temppaycode[2]=='10'){//2021/8/25 已在你訂線上支付
					}
					else{
						$tempnidin='';
					}
					/*if(isset($tempnidin[2])&&$tempnidin[2]=='10'){//2021/8/25 已在你訂線上支付
					}
					else{
						$tempnidin='';
					}*/
				}
				else{
					$tempnidin='';
				}
				if(isset($tempnidin[2])){
				}
				else{
					$tempnidin[2]=0;
				}
				$other['nidin']=floatval($other['nidin'])-floatval($tempnidin[2]);
				if($otherpay=='-1'){
				}
				else{
					foreach($otherpay as $oi=>$ov){
						if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
						}
						else{
							$other[$ov['location']]=floatval($other[$ov['location']])+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']][$ov['location']]);
							$sumotherpay=floatval($sumotherpay)+floatval($coverlist[$l['BIZDATE']][$l['CONSECNUMBER']][$ov['location']]);
						}
					}
				}
			}
			else{
				/*if(isset($cover[0]['tax2'])){
					$cover[0]['tax2']=floatval($cover[0]['tax2'])-floatval($l['TAX2']);
					echo $cover[0]['tax2'].'-';
					$cover[0]['tax3']=floatval($cover[0]['tax3'])-floatval($l['TAX3']);
					$cover[0]['tax4']=floatval($cover[0]['tax4'])-floatval($l['TAX4']);
					$cover[0]['tax9']=floatval($cover[0]['tax9'])-floatval($l['TAX9']);
					$cover[0]['TA1']=floatval($cover[0]['TA1'])-floatval($l['TA1']);
					$cover[0]['TA2']=floatval($cover[0]['TA2'])-floatval($l['TA2']);
					$cover[0]['TA3']=floatval($cover[0]['TA3'])-floatval($l['TA3']);
					$cover[0]['TA4']=floatval($cover[0]['TA4'])-floatval($l['TA4']);
					$cover[0]['TA5']=floatval($cover[0]['TA5'])-floatval($l['TA5']);
					$cover[0]['TA6']=floatval($cover[0]['TA6'])-floatval($l['TA6']);
					$cover[0]['TA7']=floatval($cover[0]['TA7'])-floatval($l['TA7']);
					$cover[0]['TA8']=floatval($cover[0]['TA8'])-floatval($l['TA8']);
					$cover[0]['TA9']=floatval($cover[0]['TA9'])-floatval($l['TA9']);
					$cover[0]['TA10']=floatval($cover[0]['TA10'])-floatval($l['TA10']);
					$cover[0]['NONTAX']=floatval($cover[0]['NONTAX'])-floatval($l['NONTAX']);
				}
				else{
				}*/
				if(isset($tempfuture)&&in_array($l['CONSECNUMBER'],$tempfuture)){//計算未來時間修改"當日"帳單的修改付款差額(補印交班表)
					$cash=floatval($cash)+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['tax3']);
					$cashcomm=floatval($cashcomm)+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['tax9']);
					$sumotherpay=floatval($sumotherpay)+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta1'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta2'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta3'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta4'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta5'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta6'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta7'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta8'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta9'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta10']);
					$other['TA1']=floatval($other['TA1'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta1']);
					$other['TA2']=floatval($other['TA2'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta2']);
					$other['TA3']=floatval($other['TA3'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta3']);
					$other['TA4']=floatval($other['TA4'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta4']);
					$other['TA5']=floatval($other['TA5'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta5']);
					$other['TA6']=floatval($other['TA6'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta6']);
					$other['TA7']=floatval($other['TA7'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta7']);
					$other['TA8']=floatval($other['TA8'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta8']);
					$other['TA9']=floatval($other['TA9'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta9']);
					$other['TA10']=floatval($other['TA10'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['ta10']);
					$other['NONTAX']=floatval($other['NONTAX'])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['nontax']);
					if(isset($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['intella'])){
						$tempintella=preg_split('/:/',$futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['intella']);
					}
					else{
						$tempintella='';
					}
					if(isset($tempintella[2])){
					}
					else{
						$tempintella[2]=0;
					}
					$other['intella']=floatval($other['intella'])+floatval($tempintella[2]);
					//2021/8/18
					if(isset($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['nidin'])){
						$tempnidin=preg_split('/:/',$futurecover[array_search($l['CONSECNUMBER'],$tempfuture)]['nidin']);
						$temppaycode=preg_split('/-/',$tempnidin[0]);
						//2021/11/8 因為載具字元包含了 - ，因此在切割時也會同時被切到，需要另外補回來
						if(sizeof($temppaycode)>3){
							for($n=2;$n<(sizeof($temppaycode)-1);$n++){
								$temppaycode[1].='-'.$temppaycode[$n];
							}
							$temppaycode[2]=$temppaycode[(sizeof($temppaycode)-1)];
						}
						else{
						}
						if(isset($temppaycode[2])&&$temppaycode[2]=='10'){//2021/8/25 已在你訂線上支付
						}
						else{
							$tempnidin='';
						}
						/*if(isset($tempnidin[2])&&$tempnidin[2]=='10'){//2021/8/25 已在你訂線上支付
						}
						else{
							$tempnidin='';
						}*/
					}
					else{
						$tempnidin='';
					}
					if(isset($tempnidin[2])){
					}
					else{
						$tempnidin[2]=0;
					}
					$other['nidin']=floatval($other['nidin'])+floatval($tempnidin[2]);
					if($otherpay=='-1'){
					}
					else{
						foreach($otherpay as $oi=>$ov){
							if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
							}
							else{
								$other[$ov['location']]=floatval($other[$ov['location']])+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)][$ov['location']]);
								$sumotherpay=floatval($sumotherpay)+floatval($futurecover[array_search($l['CONSECNUMBER'],$tempfuture)][$ov['location']]);
							}
						}
					}
				}
				else{
					$cash=floatval($cash)+floatval($l['TAX3']);
					$cashcomm=floatval($cashcomm)+floatval($l['TAX9']);
					$sumotherpay=floatval($sumotherpay)+floatval($l['TA1'])+floatval($l['TA2'])+floatval($l['TA3'])+floatval($l['TA4'])+floatval($l['TA5'])+floatval($l['TA6'])+floatval($l['TA7'])+floatval($l['TA8'])+floatval($l['TA9'])+floatval($l['TA10']);
					$other['TA1']=floatval($other['TA1'])+floatval($l['TA1']);
					$other['TA2']=floatval($other['TA2'])+floatval($l['TA2']);
					$other['TA3']=floatval($other['TA3'])+floatval($l['TA3']);
					$other['TA4']=floatval($other['TA4'])+floatval($l['TA4']);
					$other['TA5']=floatval($other['TA5'])+floatval($l['TA5']);
					$other['TA6']=floatval($other['TA6'])+floatval($l['TA6']);
					$other['TA7']=floatval($other['TA7'])+floatval($l['TA7']);
					$other['TA8']=floatval($other['TA8'])+floatval($l['TA8']);
					$other['TA9']=floatval($other['TA9'])+floatval($l['TA9']);
					$other['TA10']=floatval($other['TA10'])+floatval($l['TA10']);
					$other['NONTAX']=floatval($other['NONTAX'])+floatval($l['NONTAX']);
					if(isset($l['intella'])){
						$tempintella=preg_split('/:/',$l['intella']);
					}
					else{
						$tempintella='';
					}
					if(isset($tempintella[2])){
					}
					else{
						$tempintella[2]=0;
					}
					$other['intella']=floatval($other['intella'])+floatval($tempintella[2]);
					//2021/8/18
					if(isset($l['nidin'])){
						$tempnidin=preg_split('/:/',$l['nidin']);
						$temppaycode=preg_split('/-/',$tempnidin[0]);
						//2021/11/8 因為載具字元包含了 - ，因此在切割時也會同時被切到，需要另外補回來
						if(sizeof($temppaycode)>3){
							for($n=2;$n<(sizeof($temppaycode)-1);$n++){
								$temppaycode[1].='-'.$temppaycode[$n];
							}
							$temppaycode[2]=$temppaycode[(sizeof($temppaycode)-1)];
						}
						else{
						}
						if(isset($temppaycode[2])&&$temppaycode[2]=='10'){//2021/8/25 已在你訂線上支付
						}
						else{
							$tempnidin='';
						}
						/*if(isset($tempnidin[2])&&$tempnidin[2]=='10'){//2021/8/25 已在你訂線上支付
						}
						else{
							$tempnidin='';
						}*/
					}
					else{
						$tempnidin='';
					}
					if(isset($tempnidin[2])){
					}
					else{
						$tempnidin[2]=0;
					}
					$other['nidin']=floatval($other['nidin'])+floatval($tempnidin[2]);
					if($otherpay=='-1'){
					}
					else{
						foreach($otherpay as $oi=>$ov){
							if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
							}
							else{
								$other[$ov['location']]=floatval($other[$ov['location']])+floatval($l[$ov['location']]);
								$sumotherpay=floatval($sumotherpay)+floatval($l[$ov['location']]);
							}
						}
					}
				}
			}
			if($l['REMARKS']=='1'){
				$list1[0]['qty']++;
				$list1[0]['amt']=floatval($list1[0]['amt'])+floatval($l['SALESTTLAMT'])+floatval($l['TAX1']);
				$list1[0]['TAX5']=floatval($list1[0]['TAX5'])+floatval($l['TAX5']);
				$list1[0]['TAX6']=floatval($list1[0]['TAX6'])+floatval($l['TAX6']);
				$list1[0]['TAX7']=floatval($list1[0]['TAX7'])+floatval($l['TAX7']);
			}
			else if($l['REMARKS']=='2'){
				$list2[0]['qty']++;
				$list2[0]['amt']=floatval($list2[0]['amt'])+floatval($l['SALESTTLAMT'])+floatval($l['TAX1']);
				$list2[0]['TAX5']=floatval($list2[0]['TAX5'])+floatval($l['TAX5']);
				$list2[0]['TAX6']=floatval($list2[0]['TAX6'])+floatval($l['TAX6']);
				$list2[0]['TAX7']=floatval($list2[0]['TAX7'])+floatval($l['TAX7']);
			}
			else if($l['REMARKS']=='3'){
				$list3[0]['qty']++;
				$list3[0]['amt']=floatval($list3[0]['amt'])+floatval($l['SALESTTLAMT'])+floatval($l['TAX1']);
				$list3[0]['TAX5']=floatval($list3[0]['TAX5'])+floatval($l['TAX5']);
				$list3[0]['TAX6']=floatval($list3[0]['TAX6'])+floatval($l['TAX6']);
				$list3[0]['TAX7']=floatval($list3[0]['TAX7'])+floatval($l['TAX7']);
			}
			else{
				$list4[0]['qty']++;
				$list4[0]['amt']=floatval($list4[0]['amt'])+floatval($l['SALESTTLAMT'])+floatval($l['TAX1']);
				$list4[0]['TAX5']=floatval($list4[0]['TAX5'])+floatval($l['TAX5']);
				$list4[0]['TAX6']=floatval($list4[0]['TAX6'])+floatval($l['TAX6']);
				$list4[0]['TAX7']=floatval($list4[0]['TAX7'])+floatval($l['TAX7']);
			}
		}
		//echo $realysale;
		if(isset($coverlist)){
			foreach($coverlist as $cover1index=>$cover1){
				foreach($cover1 as $cover2index=>$cover2){
					if($cover2['state']=='1'){
						//echo $cover1index;
						if(file_exists('../../../database/sale/SALES_'.substr($cover1index,0,6).'.db')){
							$conn=sqlconnect('../../../database/sale','SALES_'.substr($cover1index,0,6).'.db','','','','sqlite');
							$checkintella='PRAGMA table_info(CST011)';
							$allcolumn=sqlquery($conn,$checkintella,'sqlite');
							if(!in_array('intella',array_column($allcolumn,'name'))){
								//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
								$insertsql='ALTER TABLE CST011 ADD COLUMN intella TEXT';
								sqlnoresponse($conn,$insertsql,'sqlite');
							}
							else{
								//$sql=$sql.',intella';
							}
							$sql=$sql.',intella';
							//2021/8/18
							//$checknidin='PRAGMA table_info(list)';
							//$allcolumn=sqlquery($conn,$checknidin,'sqlite');
							if(!in_array('nidin',array_column($allcolumn,'name'))){
								//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
								$insertsql='ALTER TABLE CST011 ADD COLUMN nidin TEXT';
								sqlnoresponse($conn,$insertsql,'sqlite');
							}
							else{
								//$sql=$sql.',nidin';
							}
							$checkintella='PRAGMA table_info(tempCST011)';
							$allcolumn=sqlquery($conn,$checkintella,'sqlite');
							if(!in_array('intella',array_column($allcolumn,'name'))){
								//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
								$insertsql='ALTER TABLE tempCST011 ADD COLUMN intella TEXT';
								sqlnoresponse($conn,$insertsql,'sqlite');
							}
							else{
								//$sql=$sql.',intella';
							}
							$sql=$sql.',intella';
							//2021/8/18
							//$checknidin='PRAGMA table_info(list)';
							//$allcolumn=sqlquery($conn,$checknidin,'sqlite');
							if(!in_array('nidin',array_column($allcolumn,'name'))){
								//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
								$insertsql='ALTER TABLE tempCST011 ADD COLUMN nidin TEXT';
								sqlnoresponse($conn,$insertsql,'sqlite');
							}
							else{
								//$sql=$sql.',nidin';
							}
							$sql='SELECT * FROM CST011 WHERE ';
							if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
								$sql=$sql.'TERMINALNUMBER="'.$_POST['machinename'].'" AND ';
							}
							else{//帳務以主機為主體計算
							}
							$sql=$sql.'BIZDATE="'.$cover1index.'" AND CONSECNUMBER="'.$cover2index.'"';
							//echo $sql;
							$report=sqlquery($conn,$sql,'sqlite');
							sqlclose($conn,'sqlite');
							if(isset($report)&&sizeof($report)>0&&$report[0]['BIZDATE']){
								$cover[0]['tax2']=floatval($cover[0]['tax2'])-floatval($report[0]['TAX2']);
								$cover[0]['tax3']=floatval($cover[0]['tax3'])-floatval($report[0]['TAX3']);
								$cover[0]['tax4']=floatval($cover[0]['tax4'])-floatval($report[0]['TAX4']);
								$cover[0]['tax9']=floatval($cover[0]['tax9'])-floatval($report[0]['TAX9']);
								$cover[0]['TA1']=floatval($cover[0]['TA1'])-floatval($report[0]['TA1']);
								$cover[0]['TA2']=floatval($cover[0]['TA2'])-floatval($report[0]['TA2']);
								$cover[0]['TA3']=floatval($cover[0]['TA3'])-floatval($report[0]['TA3']);
								$cover[0]['TA4']=floatval($cover[0]['TA4'])-floatval($report[0]['TA4']);
								$cover[0]['TA5']=floatval($cover[0]['TA5'])-floatval($report[0]['TA5']);
								$cover[0]['TA6']=floatval($cover[0]['TA6'])-floatval($report[0]['TA6']);
								$cover[0]['TA7']=floatval($cover[0]['TA7'])-floatval($report[0]['TA7']);
								$cover[0]['TA8']=floatval($cover[0]['TA8'])-floatval($report[0]['TA8']);
								$cover[0]['TA9']=floatval($cover[0]['TA9'])-floatval($report[0]['TA9']);
								$cover[0]['TA10']=floatval($cover[0]['TA10'])-floatval($report[0]['TA10']);
								$cover[0]['NONTAX']=floatval($cover[0]['NONTAX'])-floatval($report[0]['NONTAX']);
								if(isset($report[0]['intella'])){
									$tempintella=preg_split('/:/',$report[0]['intella']);
								}
								else{
									$tempintella='';
								}
								if(isset($tempintella[2])){
								}
								else{
									$tempintella[2]=0;
								}
								$cover[0]['intella']=floatval($cover[0]['intella'])-floatval($tempintella[2]);
								//2021/8/18
								if(isset($report[0]['nidin'])){
									$tempnidin=preg_split('/:/',$report[0]['nidin']);
									$temppaycode=preg_split('/-/',$tempnidin[0]);
									//2021/11/8 因為載具字元包含了 - ，因此在切割時也會同時被切到，需要另外補回來
									if(sizeof($temppaycode)>3){
										for($n=2;$n<(sizeof($temppaycode)-1);$n++){
											$temppaycode[1].='-'.$temppaycode[$n];
										}
										$temppaycode[2]=$temppaycode[(sizeof($temppaycode)-1)];
									}
									else{
									}
									if(isset($temppaycode[2])&&$temppaycode[2]=='10'){//2021/8/25 已在你訂線上支付
									}
									else{
										$tempnidin='';
									}
									/*if(isset($tempnidin[2])&&$tempnidin[2]=='10'){//2021/8/25 已在你訂線上支付
									}
									else{
										$tempnidin='';
									}*/
								}
								else{
									$tempnidin='';
								}
								if(isset($tempnidin[2])){
								}
								else{
									$tempnidin[2]=0;
								}
								$cover[0]['nidin']=floatval($cover[0]['nidin'])-floatval($tempnidin[2]);
								if($otherpay=='-1'){
								}
								else{
									foreach($otherpay as $oi=>$ov){
										if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
										}
										else{
											$cover[0][$ov['location']]=floatval($cover[0][$ov['location']])-floatval($report[0][$ov['location']]);
										}
									}
								}
							}
							else{
							}
						}
						else{
						}
					}
					else{
					}
				}
			}
		}
		else{
		}

		/*if($salemoney[0]['cash']==''){
			$cash=0;
			$cashcomm=0;
			if(isset($cover)){
				$cash=$cover[0]['tax3'];
				$cashcomm=$cover[0]['tax9'];
			}
			else{
			}
		}
		else{
			$cash=$salemoney[0]['cash'];
			$cashcomm=$salemoney[0]['cashcomm'];
			if(isset($cover)){
				$cash=floatval($cash)+floatval($cover[0]['tax3']);
				$cashcomm=floatval($cashcomm)+floatval($cover[0]['tax9']);
			}
			else{
			}
		}*/
		//print_r($listdetail);

		if($dis[0]['dis1']==''){
			$dis1=0;
		}
		else{
			$dis1=$dis[0]['dis1'];
		}
		if($dis[0]['dis2']==''){
			$dis2=0;
		}
		else{
			$dis2=$dis[0]['dis2'];
		}
		if($dis[0]['dis3']==''){
			$dis3=0;
		}
		else{
			$dis3=$dis[0]['dis3'];
		}
		if(sizeof($moeyout)>0){
			$outmoney=0;
			$inmoney=0;
			for($i=0;$i<sizeof($moeyout);$i++){
				if($moeyout[$i]['DTLMODE']=='3'){
					$inmoney=floatval($inmoney)+floatval($moeyout[$i]['AMT']);
				}
				else{
					$outmoney=floatval($outmoney)+floatval($moeyout[$i]['AMT']);
				}
			}
		}
		else{
			$outmoney=0;
			$inmoney=0;
		}
		//$realysale=realymoneybcadd(bcadd(bcadd(bcadd(bcadd($afmoney,$charge,$init['init']['accuracy']),$outmoney,$init['init']['accuracy']),$inmoney,$init['init']['accuracy']),-$cash,$init['init']['accuracy']),$cashcomm,$init['init']['accuracy']);
		$realysale=floatval($realysale)+floatval($outmoney)+floatval($inmoney);
		//echo $realysale;
		if(!isset($_POST['type'])||$_POST['type']!='view'){
			$PHPWord = new PHPWord();
			if(file_exists('../../../template/change'.$print['item']['changetype'].'.docx')){
				$document = $PHPWord->loadTemplate('../../../template/change'.$print['item']['changetype'].'.docx');
			}
			else{
				$document = $PHPWord->loadTemplate('../../../template/change.docx');
			}
			//$document = $PHPWord->loadTemplate('../../../template/change.docx');
			$document->setValue('story',$setup['basic']['storyname']);
			$document->setValue('subtitle','TABLE');
			$document->setValue('date',$date);
			$document->setValue('time',$time);
			if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']==$_POST['papbizdateE']){
				if($_POST['zcounter']=='allday'){
					$document->setValue('bizdate',$_POST['papbizdateS']);
				}
				else{
					$document->setValue('bizdate',$_POST['papbizdateS'].'-'.$_POST['zcounter']);
				}
			}
			else if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']!=$_POST['papbizdateE']){
				$document->setValue('bizdate',$_POST['papbizdateS'].'~'.$_POST['papbizdateE']);
			}
			else{
				$document->setValue('bizdate',$dbdate.'-'.$timeini['time']['zcounter']);
				//$document->setValue('bizdate',$dbdate.'-'.$machinedata['basic']['zcounter']);
			}
			if($paper!='-1'){
				if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
					$document->setValue('titlename',$_POST['machinename'].' '.$paper['name']['titlename']);
				}
				else{//帳務以主機為主體計算
					$document->setValue('titlename',$paper['name']['titlename']);
				}
				$document->setValue('salemoneyname',$paper['name']['salemoneyname']);
				$document->setValue('memberdisname',$paper['name']['memberdisname']);
				$document->setValue('chargename',$paper['name']['chargename']);
				$document->setValue('floorname',$paper['name']['floorname']);
				$document->setValue('itemdisname',$paper['name']['itemdisname']);
				$document->setValue('listdisname',$paper['name']['listdisname']);
				$document->setValue('totalsalename',$paper['name']['totalsalename']);
				$document->setValue('cashcommname',$paper['name']['cashcommname']);
				$document->setValue('cashname',$paper['name']['cashname']);
				$document->setValue('otherpayname',$paper['name']['otherpayname']);
				if(isset($paper['name']['intellapayname'])){
					$document->setValue('intellapayname',$paper['name']['intellapayname']);
				}
				else{
					$document->setValue('intellapayname',"(-)英特拉支付:");
				}
				//2021/8/18
				if(isset($paper['name']['nidinpayname'])){
					$document->setValue('nidinpayname',$paper['name']['nidinpayname']);
				}
				else{
					$document->setValue('nidinpayname',"(-)你訂支付:");
				}
				$document->setValue('inmoneyname',$paper['name']['inmoneyname']);
				$document->setValue('outmoneyname',$paper['name']['outmoneyname']);
				$document->setValue('notmoneyname',$paper['name']['notmoneyname']);
				$document->setValue('notcommname',$paper['name']['notcommname']);
				$document->setValue('notcashname',$paper['name']['notcashname']);
				$document->setValue('notothername',$paper['name']['notothername']);
				if(isset($paper['name']['memmoneyname'])){
					$document->setValue('memmoneyname',$paper['name']['memmoneyname']);
				}
				else{
					$document->setValue('memmoneyname','(+)儲值現金收入');
				}
				$document->setValue('realysalename',$paper['name']['realysalename']);
				$document->setValue('changename',$paper['name']['changename']);
				$document->setValue('shouldname',$paper['name']['shouldname']);
				//$document->setValue('saletitlename',$paper['name']['saletitlename']);
			}
			else{
				if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
					$document->setValue('titlename',$_POST['machinename'].' 交班表');
				}
				else{//帳務以主機為主體計算
					$document->setValue('titlename','交班表');
				}
				$document->setValue('salemoneyname','銷售總額:');
				$document->setValue('memberdisname','(-)會員折扣:');
				$document->setValue('chargename',"(+)服務費:");
				$document->setValue('floorname',"(+)低銷溢收:");
				$document->setValue('itemdisname',"(-)單品折讓金額:");
				$document->setValue('listdisname',"(-)帳單折讓金額:");
				$document->setValue('totalsalename',"銷售小計:");
				$document->setValue('cashcommname',"(+)信用卡手續費:");
				$document->setValue('cashname',"(-)信用卡收入:");
				$document->setValue('otherpayname',"(-)其他付款:");
				$document->setValue('intellapayname',"(-)英特拉支付:");
				//2021/8/18
				$document->setValue('nidinpayname',"(-)你訂支付:");
				$document->setValue('inmoneyname',"(+)收入費用:");
				$document->setValue('outmoneyname',"(-)支出費用:");
				$document->setValue('notmoneyname',"(+)非本班現金:");
				$document->setValue('notcommname',"(-)非本班手續費:");
				$document->setValue('notcashname',"(+)非本班信用卡:");
				$document->setValue('notothername',"(+)非本班其他付款:");
				$document->setValue('memmoneyname','(+)儲值現金收入');
				$document->setValue('realysalename',"繳回金額(不含找零金):");
				$document->setValue('changename',"找零金:");
				$document->setValue('shouldname',"錢櫃金額(含找零金):");
				//$document->setValue('saletitlename',"營業資訊");
			}
			
			$document->setValue('salemoney',$init['init']['frontunit'].$initmoney.$init['init']['unit']);
			$document->setValue('memberdis',$init['init']['frontunit'].$dis3.$init['init']['unit']);
			$document->setValue('charge',$init['init']['frontunit'].$charge.$init['init']['unit']);
			$document->setValue('floor',$init['init']['frontunit'].($afmoney-$initmoney-$dis1-$dis2-$dis3).$init['init']['unit']);
			$document->setValue('cashcomm',$init['init']['frontunit'].$cashcomm.$init['init']['unit']);
			$document->setValue('cash',$init['init']['frontunit'].$cash.$init['init']['unit']);
			$document->setValue('otherpay',$init['init']['frontunit'].$sumotherpay.$init['init']['unit']);
			$document->setValue('intellapay',$init['init']['frontunit'].$other['intella'].$init['init']['unit']);
			//2021/8/18
			$document->setValue('nidinpay',$init['init']['frontunit'].$other['nidin'].$init['init']['unit']);
			if(isset($cover[0]['tax2'])){
				if(isset($memmoney[0])){
					$document->setValue('realysale',$init['init']['frontunit'].($realysale+$cover[0]['tax2']+$memmoney[0]['totalmemmoney']).$init['init']['unit']);
					$document->setValue('should',$init['init']['frontunit'].($realysale+$machinedata['basic']['change']+$cover[0]['tax2']+$memmoney[0]['totalmemmoney']).$init['init']['unit']);
				}
				else{
					$document->setValue('realysale',$init['init']['frontunit'].($realysale+$cover[0]['tax2']).$init['init']['unit']);
					$document->setValue('should',$init['init']['frontunit'].($realysale+$machinedata['basic']['change']+$cover[0]['tax2']).$init['init']['unit']);
				}
			}
			else{
				if(isset($memmoney[0])){
					$document->setValue('realysale',$init['init']['frontunit'].($realysale+$memmoney[0]['totalmemmoney']).$init['init']['unit']);
					$document->setValue('should',$init['init']['frontunit'].($realysale+$machinedata['basic']['change']+$memmoney[0]['totalmemmoney']).$init['init']['unit']);
				}
				else{
					$document->setValue('realysale',$init['init']['frontunit'].$realysale.$init['init']['unit']);
					$document->setValue('should',$init['init']['frontunit'].($realysale+$machinedata['basic']['change']).$init['init']['unit']);
				}
			}

			$document->setValue('itemdis',$init['init']['frontunit'].$dis1.$init['init']['unit']);
			$document->setValue('listdis',$init['init']['frontunit'].$dis2.$init['init']['unit']);
			//$document->setValue('realysale',$init['init']['frontunit'].$realysale.$init['init']['unit']);
			$document->setValue('inmoney',$init['init']['frontunit'].$inmoney.$init['init']['unit']);
			$document->setValue('outmoney',$init['init']['frontunit'].$outmoney.$init['init']['unit']);
			$document->setValue('change',$init['init']['frontunit'].$machinedata['basic']['change'].$init['init']['unit']);
			$document->setValue('totalsale',$init['init']['frontunit'].(floatval($charge)+floatval($afmoney)).$init['init']['unit']);
			if(isset($cover)&&sizeof($cover)>0){
				if(isset($cover[0]['tax2'])){
					$document->setValue('notmoney',$init['init']['frontunit'].$cover[0]['tax2'].$init['init']['unit']);
				}
				else{
					$document->setValue('notmoney',$init['init']['frontunit'].'0'.$init['init']['unit']);
				}
				if(isset($cover[0]['tax9'])){
					$document->setValue('notcomm',$init['init']['frontunit'].$cover[0]['tax9'].$init['init']['unit']);
				}
				else{
					$document->setValue('notcomm',$init['init']['frontunit'].'0'.$init['init']['unit']);
				}
				if(isset($cover[0]['tax3'])){
					$document->setValue('notcash',$init['init']['frontunit'].$cover[0]['tax3'].$init['init']['unit']);
				}
				else{
					$document->setValue('notcash',$init['init']['frontunit'].'0'.$init['init']['unit']);
				}
				if(isset($cover[0]['tax4'])){
					$document->setValue('notother',$init['init']['frontunit'].$cover[0]['tax4'].$init['init']['unit']);
				}
				else{
					$document->setValue('notother',$init['init']['frontunit'].'0'.$init['init']['unit']);
				}
			}
			else{
				$document->setValue('notmoney',$init['init']['frontunit'].'0'.$init['init']['unit']);
				$document->setValue('notcomm',$init['init']['frontunit'].'0'.$init['init']['unit']);
				$document->setValue('notcash',$init['init']['frontunit'].'0'.$init['init']['unit']);
				$document->setValue('notother',$init['init']['frontunit'].'0'.$init['init']['unit']);
			}
			if(isset($memmoney[0])){
				$document->setValue('memmoney',$init['init']['frontunit'].$memmoney[0]['totalmemmoney'].$init['init']['unit']);
			}
			else{
				$document->setValue('memmoney',$init['init']['frontunit'].'0'.$init['init']['unit']);
			}

			if(isset($posdvr)){
				$tempdvrcontent .= "change".PHP_EOL.$setup['basic']['storyname'].PHP_EOL;
				if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']==$_POST['papbizdateE']){
					if($_POST['zcounter']=='allday'){
						$tempdvrcontent .= $_POST['papbizdateS'];
					}
					else{
						$tempdvrcontent .= $_POST['papbizdateS']."!45".$_POST['zcounter'];
					}
				}
				else if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']!=$_POST['papbizdateE']){
					$tempdvrcontent .= $_POST['papbizdateS']."!45".$_POST['papbizdateE'];
				}
				else{
					$tempdvrcontent .= $dbdate."!45".$timeini['time']['zcounter'];
				}
				if($paper!='-1'){
					if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
						$tempdvrcontent .= $_POST['machinename']." ".$paper['name']['titlename'].PHP_EOL;
					}
					else{//帳務以主機為主體計算
						$tempdvrcontent .= $paper['name']['titlename'].PHP_EOL;
					}
				}
				else{
					if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
						$tempdvrcontent .= $_POST['machinename']." 交班表".PHP_EOL;
					}
					else{//帳務以主機為主體計算
						$tempdvrcontent .= "交班表".PHP_EOL;
					}
				}
				$tempdvrcontent .= "Date!58".preg_replace("/[\/]/",'!47',$date).PHP_EOL;
				$tempdvrcontent .= "Time!58".preg_replace("/[:]/",'!58',$time).PHP_EOL;
				if($paper!='-1'){
					$tempdvrcontent .= preg_replace(array("/[+]/","/[-]/","/[)]/","/[:]/"),array('!43','!45','!41','!58'),$paper['name']['salemoneyname']);
					$tempdvrcontent .= $initmoney.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['memberdisname']);
					$tempdvrcontent .= $dis3.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['chargename']);
					$tempdvrcontent .= $charge.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['floorname']);
					$tempdvrcontent .= ($afmoney-$initmoney-$dis1-$dis2-$dis3).PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['itemdisname']);
					$tempdvrcontent .= $dis1.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['listdisname']);
					$tempdvrcontent .= $dis2.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['totalsalename']);
					$tempdvrcontent .= (floatval($charge)+floatval($afmoney)).PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['cashcommname']);
					$tempdvrcontent .= $cashcomm.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['cashname']);
					$tempdvrcontent .= $cash.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['otherpayname']);
					$tempdvrcontent .= $sumotherpay.PHP_EOL;
					if(isset($init['init']['intellapay'])&&$init['init']['intellapay']=='1'){
						if(isset($paper['name']['intellapayname'])){
							$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['intellapayname']);
						}
						else{
							$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),"(-)英特拉支付:");
						}
					}
					else{
					}
					$tempdvrcontent .= $other['intella'].PHP_EOL;
					//2021/8/18
					if(isset($init['nidin']['usenidin'])&&$init['nidin']['usenidin']=='1'){
						if(isset($paper['name']['nidinpayname'])){
							$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['nidinpayname']);
						}
						else{
							$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),"(-)你訂支付:");
						}
					}
					else{
					}
					$tempdvrcontent .= $other['nidin'].PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['inmoneyname']);
					$tempdvrcontent .= $inmoney.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['outmoneyname']);
					$tempdvrcontent .= $outmoney.PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['notmoneyname']);
					if(isset($cover)&&sizeof($cover)>0){
						if(isset($cover[0]['tax2'])){
							$tempdvrcontent .= $cover[0]['tax2'].PHP_EOL;
						}
						else{
							$tempdvrcontent .= '0'.PHP_EOL;
						}
					}
					else{
						$tempdvrcontent .= '0'.PHP_EOL;
					}
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['realysalename']);
					if(isset($cover[0]['tax2'])){
						$tempdvrcontent .= ($realysale+$cover[0]['tax2']).PHP_EOL;
					}
					else{
						$tempdvrcontent .= $realysale.PHP_EOL;
					}
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['changename']);
					$tempdvrcontent .= $machinedata['basic']['change'].PHP_EOL;
					$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['shouldname']);
					if(isset($cover[0]['tax2'])){
						$tempdvrcontent .= ($realysale+$machinedata['basic']['change']+$cover[0]['tax2']).PHP_EOL;
					}
					else{
						$tempdvrcontent .= ($realysale+$machinedata['basic']['change']).PHP_EOL;
					}
					//$tempdvrcontent .= preg_replace(array('/[+]/','/[-]/','/[)]/','/[:]/'),array('!43','!45','!41','!58'),$paper['name']['saletitlename']);
				}
				else{
					$tempdvrcontent .= "銷售總額!58";
					$tempdvrcontent .= $initmoney.PHP_EOL;
					$tempdvrcontent .= "!40!45!41會員折扣!58";
					$tempdvrcontent .= $dis3.PHP_EOL;
					$tempdvrcontent .= "!40!43!41服務費!58";
					$tempdvrcontent .= $charge.PHP_EOL;
					$tempdvrcontent .= "!40!43!41低銷溢收!58";
					$tempdvrcontent .= ($afmoney-$initmoney-$dis1-$dis2-$dis3).PHP_EOL;
					$tempdvrcontent .= "!40!45!41單品折讓金額!58";
					$tempdvrcontent .= $dis1.PHP_EOL;
					$tempdvrcontent .= "!40!45!41帳單折讓金額!58";
					$tempdvrcontent .= $dis2.PHP_EOL;
					$tempdvrcontent .= "銷售小計!58";
					$tempdvrcontent .= (floatval($charge)+floatval($afmoney)).PHP_EOL;
					$tempdvrcontent .= "!40!43!41信用卡手續費!58";
					$tempdvrcontent .= $cashcomm.PHP_EOL;
					$tempdvrcontent .= "!40!45!41信用卡收入!58";
					$tempdvrcontent .= $cash.PHP_EOL;
					$tempdvrcontent .= "!40!45!41其他付款!58";
					$tempdvrcontent .= $sumotherpay.PHP_EOL;
					if(isset($init['init']['intellapay'])&&$init['init']['intellapay']=='1'){
						$tempdvrcontent .= "!40!45!41英特拉支付!58";
						$tempdvrcontent .= $other['intella'].PHP_EOL;
					}
					else{
					}
					//2021/8/18
					if(isset($init['nidin']['usenidin'])&&$init['nidin']['usenidin']=='1'){
						$tempdvrcontent .= "!40!45!41你訂支付!58";
						$tempdvrcontent .= $other['nidin'].PHP_EOL;
					}
					else{
					}
					$tempdvrcontent .= "!40!43!41收入費用!58";
					$tempdvrcontent .= $inmoney.PHP_EOL;
					$tempdvrcontent .= "!40!45!41支出費用!58";
					$tempdvrcontent .= $outmoney.PHP_EOL;
					$tempdvrcontent .= "!40!43!41非本班現金!58";
					if(isset($cover)&&sizeof($cover)>0){
						if(isset($cover[0]['tax2'])){
							$tempdvrcontent .= $cover[0]['tax2'].PHP_EOL;
						}
						else{
							$tempdvrcontent .= '0'.PHP_EOL;
						}
					}
					else{
						$tempdvrcontent .= '0'.PHP_EOL;
					}
					$tempdvrcontent .= "繳回金額!40不含找零金!41!58";
					if(isset($cover[0]['tax2'])){
						$tempdvrcontent .= ($realysale+$cover[0]['tax2']).PHP_EOL;
					}
					else{
						$tempdvrcontent .= $realysale.PHP_EOL;
					}
					$tempdvrcontent .= "找零金!58";
					$tempdvrcontent .= $machinedata['basic']['change'].PHP_EOL;
					$tempdvrcontent .= "錢櫃金額!40含找零金!41!58";
					if(isset($cover[0]['tax2'])){
						$tempdvrcontent .= ($realysale+$machinedata['basic']['change']+$cover[0]['tax2']).PHP_EOL;
					}
					else{
						$tempdvrcontent .= ($realysale+$machinedata['basic']['change']).PHP_EOL;
					}
					//$tempdvrcontent .= "營業資訊";
				}
			}
			else{
			}

			$saletype=preg_split('/,/',$init['init']['orderlocation']);
			$allqty=intval($list1[0]['qty'])+intval($list2[0]['qty'])+intval($list3[0]['qty'])+intval($list4[0]['qty']);
			$allamt=intval($list1[0]['amt'])+intval($list2[0]['amt'])+intval($list3[0]['amt'])+intval($list4[0]['amt']);
			
			$table='';
			//2021/10/7 因為作廢帳單號沒卡在營業資訊中，若沒有開啟營業資訊，則無法顯示作廢單號；因此將該迴圈拉到這裡
			$count=0;
			$money=0;
			$allvoidlist='';
			for($i=0;$i<sizeof($voidlist);$i++){
				if(strlen($allvoidlist)==0){
					$allvoidlist=intval($voidlist[$i]['CONSECNUMBER']);
				}
				else{
					$allvoidlist=$allvoidlist.','.intval($voidlist[$i]['CONSECNUMBER']);
				}
				$money=intval($money)+intval($voidlist[$i]['SALESTTLAMT']);
				$count++;
			}
			if($print!='-1'&&isset($print['block']['saledetail'])&&$print['block']['saledetail']=='1'){
				$table.='<w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p><w:p w:rsidR="008C4EB0" w:rsidRPr="001E039E" w:rsidRDefault="00FC09EC" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['saletitlename'];
				}
				else{
					$table.="營業資訊";
				}
				$table.='</w:t></w:r></w:p>';
				if(isset($posdvr)){
					if($paper!='-1'){
						$tempdvrcontent .= $paper['name']['saletitlename'].PHP_EOL;
					}
					else{
						$tempdvrcontent .= "營業資訊".PHP_EOL;
					}
				}
				else{
				}
				$table .= '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="'.$print['item']['chatablesize1'].'"/><w:gridCol w:w="'.$print['item']['chatablesize2'].'"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.((intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2']))).'" w:type="dxa"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['salettlname'];
				}
				else{
					$table.="彙總";
				}
				$table.='</w:t></w:r></w:p></w:tc></w:tr>';
				if(isset($posdvr)){
					if($paper!='-1'){
						$tempdvrcontent .= "   ".$paper['name']['salettlname'].PHP_EOL;
					}
					else{
						$tempdvrcontent .= "   彙總".PHP_EOL;
					}
				}
				else{
				}
				$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['listqtyttlname'];
				}
				else{
					$table.="帳單總數:";
				}
				$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$allqty.'</w:t></w:r></w:p></w:tc></w:tr>';
				if(isset($posdvr)){
					if($paper!='-1'){
						$tempdvrcontent .= preg_replace('/[:]/','!58',$paper['name']['listqtyttlname']);
					}
					else{
						$tempdvrcontent .= "帳單總數!58";
					}
					$tempdvrcontent .= $allqty.PHP_EOL;
				}
				else{
				}
				$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['amtttlname'];
				}
				else{
					$table.="總金額:";
				}
				$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$allamt.'</w:t></w:r></w:p></w:tc></w:tr>';
				if(isset($posdvr)){
					if($paper!='-1'){
						$tempdvrcontent .= preg_replace('/[:]/','!58',$paper['name']['amtttlname']);
					}
					else{
						$tempdvrcontent .= "總金額!58";
					}
					$tempdvrcontent .= $allamt.PHP_EOL;
				}
				else{
				}
				$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['avgttlname'];
				}
				else{
					$table.="平均金額:";
				}
				$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
				if($allqty==0){
					$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
				}
				else{
					$table .= $init['init']['frontunit'].bcdiv($allamt,$allqty,$init['init']['accuracy']).$init['init']['unit'];
				}
				$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
				if(isset($posdvr)){
					if($paper!='-1'){
						$tempdvrcontent .= preg_replace('/[:]/','!58',$paper['name']['avgttlname']);
					}
					else{
						$tempdvrcontent .= "平均金額!58";
					}
					if($allqty==0){
						$tempdvrcontent .= '0'.PHP_EOL;
					}
					else{
						$tempdvrcontent .= bcdiv($allamt,$allqty,$init['init']['accuracy']).PHP_EOL;
					}
				}
				else{
				}
				if(in_array(1,$saletype)){
					$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
					if($button!='-1'){
						$table.=$button['name']['listtype1'];
					}
					else{
						$table.="內用";
					}
					$table.='</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table.=$paper['name']['listqty1name'];
					}
					else{
						$table.="帳單總數:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$list1[0]['qty'].'</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table.=$paper['name']['amt1name'];
					}
					else{
						$table.="總金額:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($list1[0]['amt']==''){
						$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
					}
					else{
						$table .= $init['init']['frontunit'].$list1[0]['amt'].$init['init']['unit'];
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					if($init['init']['openpersoncount']=='1'){
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avg1name'];
						}
						else{
							$table.="平均金額:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					}
					else{
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avg1name'];
						}
						else{
							$table.="平均金額:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					}
					if($list1[0]['qty']==0){
						$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
					}
					else{
						$table .= $init['init']['frontunit'].bcdiv($list1[0]['amt'],$list1[0]['qty'],$init['init']['accuracy']).$init['init']['unit'];
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					$totalpersons=0;
					if($init['init']['openpersoncount']=='1'){
						for($i=1;$i<=3;$i++){
							if($floorspend['person'.$i]['name']==''){
							}
							else{
								$ttt=0;
								for($j=$i+1;$j<3;$j++){
									if(isset($floorspend['person'.$j])&&$floorspend['person'.$j]['name']==''){
									}
									else{
										$ttt=1;
										break;
									}
								}
								if($ttt==1){
									$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$floorspend['person'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
								}
								else{
									$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$floorspend['person'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
								}
								if($list1[0]['TAX'.(5+$i)]==0){
									$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									$totalpersons=intval($totalpersons)+intval($list1[0]['TAX'.(5+$i)]);
									$table .= $init['init']['frontunit'].$list1[0]['TAX'.(5+$i)].$init['init']['unit'];
								}
								$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
							}
						}
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avgpersonname'];
						}
						else{
							$table.="平均客單價:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($totalpersons==0){
							$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
						}
						else{
							$table .= $init['init']['frontunit'].bcdiv($list1[0]['amt'],$totalpersons,$init['init']['accuracy']).$init['init']['unit'];
						}
						$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					}
					else{
					}
				}
				else{
				}
				if(in_array(2,$saletype)){
					$table .= '<w:tr w:rsidR="009F34C5" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="009F34C5" w:rsidRPr="00014EA3" w:rsidRDefault="009F34C5" w:rsidP="009F34C5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
					if($button!='-1'){
						$table.=$button['name']['listtype2'];
					}
					else{
						$table.="外帶";
					}
					$table.='</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="009F34C5" w:rsidTr="000443E5"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="009F34C5" w:rsidRPr="00014EA3" w:rsidRDefault="009F34C5" w:rsidP="000443E5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table.=$paper['name']['listqty2name'];
					}
					else{
						$table.="帳單總數:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="009F34C5" w:rsidRPr="00014EA3" w:rsidRDefault="00C53649" w:rsidP="000443E5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>'.$list2[0]['qty'].'</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="009F34C5" w:rsidTr="000443E5"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="009F34C5" w:rsidRPr="00014EA3" w:rsidRDefault="009F34C5" w:rsidP="000443E5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table.=$paper['name']['amt2name'];
					}
					else{
						$table.="總金額:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="009F34C5" w:rsidRPr="00014EA3" w:rsidRDefault="00C53649" w:rsidP="000443E5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>';
					if($list2[0]['amt']==''){
						$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
					}
					else{
						$table .= $init['init']['frontunit'].$list2[0]['amt'].$init['init']['unit'];
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					if($init['init']['openpersoncount']=='1'){
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avg2name'];
						}
						else{
							$table.="平均金額:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					}
					else{
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avg2name'];
						}
						else{
							$table.="平均金額:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					}
					if($list2[0]['qty']==0){
						$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
					}
					else{
						$table .= $init['init']['frontunit'].bcdiv($list2[0]['amt'],$list2[0]['qty'],$init['init']['accuracy']).$init['init']['unit'];
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					$totalpersons=0;
					if($init['init']['openpersoncount']=='1'){
						for($i=1;$i<=3;$i++){
							if($floorspend['person'.$i]['name']==''){
							}
							else{
								$ttt=0;
								for($j=$i+1;$j<3;$j++){
									if(isset($floorspend['person'.$j])&&$floorspend['person'.$j]['name']==''){
									}
									else{
										$ttt=1;
										break;
									}
								}
								if($ttt==1){
									$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$floorspend['person'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
								}
								else{
									$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$floorspend['person'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
								}
								if($list2[0]['TAX'.(5+$i)]==0){
									$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									$totalpersons=intval($totalpersons)+intval($list2[0]['TAX'.(5+$i)]);
									$table .= $init['init']['frontunit'].$list2[0]['TAX'.(5+$i)].$init['init']['unit'];
								}
								$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
							}
						}
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avgpersonname'];
						}
						else{
							$table.="平均客單價:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($totalpersons==0){
							$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
						}
						else{
							$table .= $init['init']['frontunit'].bcdiv($list2[0]['amt'],$totalpersons,$init['init']['accuracy']).$init['init']['unit'];
						}
						$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					}
					else{
					}
				}
				else{
				}
				if(in_array(3,$saletype)){
					$table .= '<w:tr w:rsidR="009F34C5" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="009F34C5" w:rsidRPr="00014EA3" w:rsidRDefault="009F34C5" w:rsidP="009F34C5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
					if($button!='-1'){
						$table.=$button['name']['listtype3'];
					}
					else{
						$table.='外送';
					}
					$table.='</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00C53649" w:rsidTr="000443E5"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00C53649" w:rsidRPr="00BF18A6" w:rsidRDefault="00C53649" w:rsidP="000443E5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table.=$paper['name']['listqty3name'];
					}
					else{
						$table.="帳單總數:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00C53649" w:rsidRPr="00BF18A6" w:rsidRDefault="00C53649" w:rsidP="000443E5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$list3[0]['qty'].'</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00C53649" w:rsidTr="000443E5"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00C53649" w:rsidRPr="00BF18A6" w:rsidRDefault="00C53649" w:rsidP="000443E5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table.=$paper['name']['amt3name'];
					}
					else{
						$table.="總金額:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00C53649" w:rsidRPr="00BF18A6" w:rsidRDefault="00C53649" w:rsidP="000443E5"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($list3[0]['amt']==''){
						$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
					}
					else{
						$table .= $init['init']['frontunit'].$list3[0]['amt'].$init['init']['unit'];
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					if($init['init']['openpersoncount']=='1'){
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avg3name'];
						}
						else{
							$table.="平均金額:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					}
					else{
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avg3name'];
						}
						else{
							$table.="平均金額:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					}
					if($list3[0]['qty']==0){
						$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
					}
					else{
						$table .= $init['init']['frontunit'].bcdiv($list3[0]['amt'],$list3[0]['qty'],$init['init']['accuracy']).$init['init']['unit'];
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					$totalpersons=0;
					if($init['init']['openpersoncount']=='1'){
						for($i=1;$i<=3;$i++){
							if($floorspend['person'.$i]['name']==''){
							}
							else{
								$ttt=0;
								for($j=$i+1;$j<3;$j++){
									if(isset($floorspend['person'.$j])&&$floorspend['person'.$j]['name']==''){
									}
									else{
										$ttt=1;
										break;
									}
								}
								if($ttt==1){
									$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$floorspend['person'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
								}
								else{
									$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$floorspend['person'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
								}
								if($list3[0]['TAX'.(5+$i)]==0){
									$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									$totalpersons=intval($totalpersons)+intval($list3[0]['TAX'.(5+$i)]);
									$table .= $init['init']['frontunit'].$list3[0]['TAX'.(5+$i)].$init['init']['unit'];
								}
								$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
							}
						}
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avgpersonname'];
						}
						else{
							$table.="平均客單價:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($totalpersons==0){
							$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
						}
						else{
							$table .= $init['init']['frontunit'].bcdiv($list3[0]['amt'],$totalpersons,$init['init']['accuracy']).$init['init']['unit'];
						}
						$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					}
					else{
					}
				}
				else{
				}
				if(in_array(4,$saletype)){
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00014EA3" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
					if($button!='-1'){
						$table.=$button['name']['listtype4'];
					}
					else{
						$table.="自取";
					}
					$table.='</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table.=$paper['name']['listqty4name'];
					}
					else{
						$table.="帳單總數:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$list4[0]['qty'].'</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table.=$paper['name']['amt4name'];
					}
					else{
						$table.="總金額:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00EF6065"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($list4[0]['amt']==''){
						$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
					}
					else{
						$table .= $init['init']['frontunit'].$list4[0]['amt'].$init['init']['unit'];
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					if($init['init']['openpersoncount']=='1'){
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avg4name'];
						}
						else{
							$table.="平均金額:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					}
					else{
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avg4name'];
						}
						else{
							$table.="平均金額:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					}
					if($list4[0]['qty']==0){
						$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
					}
					else{
						$table .= $init['init']['frontunit'].bcdiv($list4[0]['amt'],$list4[0]['qty'],$init['init']['accuracy']).$init['init']['unit'];
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					$totalpersons=0;
					if($init['init']['openpersoncount']=='1'){
						for($i=1;$i<=3;$i++){
							if($floorspend['person'.$i]['name']==''){
							}
							else{
								$ttt=0;
								for($j=$i+1;$j<3;$j++){
									if(isset($floorspend['person'.$j])&&$floorspend['person'.$j]['name']==''){
									}
									else{
										$ttt=1;
										break;
									}
								}
								if($ttt==1){
									$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$floorspend['person'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
								}
								else{
									$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$floorspend['person'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
								}
								if($list4[0]['TAX'.(5+$i)]==0){
									$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									$totalpersons=intval($totalpersons)+intval($list4[0]['TAX'.(5+$i)]);
									$table .= $init['init']['frontunit'].$list4[0]['TAX'.(5+$i)].$init['init']['unit'];
								}
								$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
							}
						}
						$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['avgpersonname'];
						}
						else{
							$table.="平均客單價:";
						}
						$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00014EA3"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
						if($totalpersons==0){
							$table .= $init['init']['frontunit'].'0'.$init['init']['unit'];
						}
						else{
							$table .= $init['init']['frontunit'].bcdiv($list4[0]['amt'],$totalpersons,$init['init']['accuracy']).$init['init']['unit'];
						}
						$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					}
					else{
					}
				}
				else{
				}
				//2021/10/7 因為作廢帳單號沒卡在營業資訊中，若沒有開啟營業資訊，則無法顯示作廢單號；因此將該迴圈拉到前面
				/*$count=0;
				$money=0;
				$allvoidlist='';
				for($i=0;$i<sizeof($voidlist);$i++){
					if(strlen($allvoidlist)==0){
						$allvoidlist=intval($voidlist[$i]['CONSECNUMBER']);
					}
					else{
						$allvoidlist=$allvoidlist.','.intval($voidlist[$i]['CONSECNUMBER']);
					}
					$money=intval($money)+intval($voidlist[$i]['SALESTTLAMT']);
					$count++;
				}*/
				$table .= '<w:tr w:rsidR="00100319" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00100319" w:rsidRPr="00BF18A6" w:rsidRDefault="00100319" w:rsidP="00100319"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Times New Roman" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['titleVname'];
				}
				else{
					$table.="作廢帳單";
				}
				$table.='</w:t></w:r></w:p></w:tc></w:tr>';
				$table .= '<w:tr w:rsidR="00100319" w:rsidTr="00773B76"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00100319" w:rsidRPr="00BF18A6" w:rsidRDefault="00100319" w:rsidP="00AA6B48"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['listqtyVname'];
				}
				else{
					$table.="帳單總數:";
				}
				$table.='</w:t></w:r></w:p></w:tc>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/></w:tcPr><w:p w:rsidR="00100319" w:rsidRPr="00BF18A6" w:rsidRDefault="00100319" w:rsidP="00767D07"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$count.'</w:t></w:r></w:p></w:tc>';
				$table .= '</w:tr>';
				$table .= '<w:tr w:rsidR="00100319" w:rsidTr="00773B76"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00100319" w:rsidRPr="00BF18A6" w:rsidRDefault="00100319" w:rsidP="00AA6B48"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['amtVname'];
				}
				else{
					$table.="總金額:";
				}
				$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/></w:tcPr><w:p w:rsidR="00100319" w:rsidRPr="00BF18A6" w:rsidRDefault="00100319" w:rsidP="00767D07"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$money.'</w:t></w:r></w:p></w:tc></w:tr>';
				$table .= '</w:tbl>';
			}
			else{
			}
			//$document->setValue('saledetail',$table);
			if($print!='-1'&&isset($print['block']['allvoidlist'])&&$print['block']['allvoidlist']=='1'){
				$table.='<w:p w:rsidR="008C4EB0" w:rsidRPr="001E039E" w:rsidRDefault="00FC09EC" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table.=$paper['name']['voidlistname'];
					if(isset($allvoidlist)){
						$table.=$allvoidlist;
					}
				}
				else{
					$table.='作廢帳單號:';
					if(isset($allvoidlist)){
						$table.=$allvoidlist;
					}
				}
				$table.='</w:t></w:r></w:p><w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p>';
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['memmoney'])&&$print['block']['memmoney']=='1'&&isset($memmoney[0])){
				$table .= '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="'.$print['item']['chatablesize1'].'"/><w:gridCol w:w="'.$print['item']['chatablesize2'].'"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00014EA3" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
				if($paper!='-1'&&isset($paper['name']['memmoneylist'])){
					$table .= $paper['name']['memmoneylist'];
				}
				else{
					$table .= '會員儲值列表';
				}
				$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
				$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
				if($paper!='-1'&&isset($paper['name']['memno'])){
					$table .= $paper['name']['memno'];
				}
				else{
					$table .= '會員電話';
				}
				$table .= '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
				if($paper!='-1'&&isset($paper['name']['paymoney'])){
					$table .= $paper['name']['paymoney'];
				}
				else{
					$table .= '儲值金額';
				}
				$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
				
				for($i=0;$i<sizeof($memmoney);$i++){
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="pct"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($memmoney[$i]['ITEMCODE']!=''){
						$table .= $memmoney[$i]['ITEMCODE'];
					}
					else{
						$table .= '('.$memmoney[$i]['ITEMNAME'].')';
					}
					$table .= '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$init['init']['frontunit'];
					$table .= $memmoney[$i]['UNITPRICE'];
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
				}
				
				$table .= '</w:tbl>';
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['otherpay'])&&$print['block']['otherpay']=='1'){
				if($otherpay!='-1'&&$otherpay['pay']['openpay']==1){
					$table .= '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="'.$print['item']['chatablesize1'].'"/><w:gridCol w:w="'.$print['item']['chatablesize2'].'"/></w:tblGrid>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00014EA3" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['otherpaynamelist'];
					}
					else{
						$table .= '其他付款項目';
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					
					for($i=1;$i<sizeof($otherpay);$i++){
						$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$otherpay['item'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$init['init']['frontunit'];
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							$table .= $other[$otherpay['item'.$i]['dbname']];
						}
						else{
							$table .= $other[$otherpay['item'.$i]['location']];
						}
						$table .= $init['init']['unit'].'</w:t></w:r></w:p></w:tc></w:tr>';
					}
					
					$table .= '</w:tbl>';
					if(isset($cover)&&sizeof($cover)>0){
						$table .= '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="'.$print['item']['chatablesize1'].'"/><w:gridCol w:w="'.$print['item']['chatablesize2'].'"/></w:tblGrid><w:tr w:rsidR="00EF6065" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00014EA3" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
						if($paper!='-1'){
							$table.=$paper['name']['notothertitlename'];
						}
						else{
							$table.="非本班其他付款";
						}
						$table.='</w:t></w:r></w:p></w:tc></w:tr>';
						for($i=1;$i<sizeof($otherpay);$i++){
							$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$otherpay['item'.$i]['name'].':</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$init['init']['frontunit'];
							if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
								if(isset($cover[0][$otherpay['item'.$i]['dbname']])&&$cover[0][$otherpay['item'.$i]['dbname']]>0){
									$table .= $cover[0][$otherpay['item'.$i]['dbname']];
								}
								else{
									$table .= '0';
								}
							}
							else{
								if(isset($cover[0][$otherpay['item'.$i]['location']])&&$cover[0][$otherpay['item'.$i]['location']]>0){
									$table .= $cover[0][$otherpay['item'.$i]['location']];
								}
								else{
									$table .= '0';
								}
							}
							$table .= $init['init']['unit'].'</w:t></w:r></w:p></w:tc></w:tr>';
						}
						$table .= '</w:tbl><w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p>';
					}
					else{
					}
					//$document->setValue('otherpaydetail',$table);
				}
				else{
				}
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['inv'])&&$print['block']['inv']=='1'){
				if($init['init']['useinv']==1&&isset($invlist)){
					$qty=0;
					$amt=0;
					$voidqty=0;
					$voidamt=0;
					$voidlist='';
					for($i=0;$i<sizeof($invlist);$i++){
						$amt=intval($amt)+intval($invlist[$i]['totalamount']);
						$qty++;
					}
					for($i=0;$i<sizeof($invvoidlist);$i++){
						if(strlen($voidlist)==''){
							$voidlist=$invvoidlist[$i]['invnumber'];
						}
						else{
							$voidlist=$voidlist.','.$invvoidlist[$i]['invnumber'];
						}
						$voidamt=intval($voidamt)+intval($invvoidlist[$i]['totalamount']);
						$voidqty++;
					}
					$table .= '<w:tbl><w:tblPr><w:tblStyle w:val="a3"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="'.$print['item']['chatablesize1'].'"/><w:gridCol w:w="'.$print['item']['chatablesize2'].'"/></w:tblGrid>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00014EA3" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['invnotvoidname'];
					}
					else{
						$table .= "發票彙總(不含作廢)";
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['invalready'];
					}
					else{
						$table .= "已開發票數:";
					}
					$table .= '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$qty.'</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['invmoneyalready'];
					}
					else{
						$table .= "已開立金額:";
					}
					$table .= '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00EF6065"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$amt.'</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['startinv'];
					}
					else{
						$table .= "起始發票號:";
					}
					$table .= '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00EF6065"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if(isset($invlist[0])){
						$table .= $invlist[0]['invnumber'];
					}
					else{
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="pct"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['endinv'];
					}
					else{
						$table .= "結束發票號:";
					}
					$table .= '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="pct"/><w:tcBorders><w:bottom w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00EF6065"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if(isset($invlist[0])){
						$table .= $invlist[sizeof($invlist)-1]['invnumber'];
					}
					else{
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00881D27"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.(intval($print['item']['chatablesize1'])+intval($print['item']['chatablesize2'])).'" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00014EA3" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['chatabletitlefont'].'"/><w:szCs w:val="'.$print['item']['chatabletitlefont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['voidinv'];
					}
					else{
						$table .= "作廢發票";
					}
					$table .= '</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['voidqty'];
					}
					else{
						$table .= "作廢數量:";
					}
					$table .= '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$voidqty.'</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '<w:tr w:rsidR="00EF6065" w:rsidTr="00F12259"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize1'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00F12259"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['voidmoney'];
					}
					else{
						$table .= "作廢金額:";
					}
					$table.='</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="'.$print['item']['chatablesize2'].'" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00EF6065" w:rsidRPr="00BF18A6" w:rsidRDefault="00EF6065" w:rsidP="00EF6065"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr></w:pPr><w:r w:rsidRPr="00BF18A6"><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/><w:b/><w:sz w:val="'.$print['item']['chatablecontentfont'].'"/><w:szCs w:val="'.$print['item']['chatablecontentfont'].'"/></w:rPr><w:t>'.$voidamt.'</w:t></w:r></w:p></w:tc></w:tr>';
					$table .= '</w:tbl>';
					//$document->setValue('invtable',$table);
					$table.='<w:p w:rsidR="008C4EB0" w:rsidRPr="001E039E" w:rsidRDefault="00FC09EC" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>';
					if($paper!='-1'){
						$table .= $paper['name']['voidinvoicenumber'].$voidlist;
					}
					else{
						$table .= '作廢發票號:'.$voidlist;
					}
					$table.='</w:t></w:r></w:p><w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p>';
					
				}
				else{
					//$document->setValue('invtable','');
					//$document->setValue('invtablelist','');
				}
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['rearlist'])&&$print['block']['rearlist']=='1'){
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3161"/><w:gridCol w:w="734"/><w:gridCol w:w="1105"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['reartitle'];
				}
				else{
					$table .= "分析類別";
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:b/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['rearqty'];
				}
				else{
					$table .= "QTY";
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				foreach($rearlist as $item){
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= $item['name'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= $item['QTY'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				$table .= '</w:tbl><w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p>';
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['salelist'])&&$print['block']['salelist']=='1'){
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3161"/><w:gridCol w:w="734"/><w:gridCol w:w="1105"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['saleitem'];
				}
				else{
					$table .= 'Items';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:b/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['saleqty'];
				}
				else{
					$table .= 'QTY';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:b/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['saleamt'];
				}
				else{
					$table .= 'AMT';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$itemdeptcode='';
				foreach($listdetail as $item){
					if($itemdeptcode==''||$itemdeptcode!=$item['ITEMDEPTCODE']){
						$itemdeptcode=$item['ITEMDEPTCODE'];
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						if(strlen($item['UNITPRICE'])==0){
							$table .= $item['ITEMNAME'];
						}
						else{
							$table .= $item['ITEMNAME']."(".$item['UNITPRICE'].")";
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['QTY'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['AMT'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
					else{
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						if(strlen($item['UNITPRICE'])==0){
							$table .= $item['ITEMNAME'];
						}
						else{
							$table .= $item['ITEMNAME']."(".$item['UNITPRICE'].")";
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['QTY'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['AMT'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
				}
				$table .= '</w:tbl><w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p>';
			}
			else{
			}
			
			if($print!='-1'&&isset($print['block']['inoutmoney'])&&$print['block']['inoutmoney']=='1'&&sizeof($moeyout)>0){
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2895"/><w:gridCol w:w="2105"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['inmoneylistname'];
				}
				else{
					$table .= '收入費用明細';
				}
				$table .= '</w:t></w:r></w:p></w:tc>';
				$table .= '</w:tr>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2895" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['inmoneylistlabel'];
				}
				else{
					$table .= '科目';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['inmoneylistsub'];
				}
				else{
					$table .= 'Sub';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				foreach($moeyout as $item){
					if($item['DTLMODE']=='3'){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="2895" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						if($item['ITEMNAME']==''){
							$table .= $type['type'][$item['ITEMDEPTCODE']];
						}
						else{
							$table .= $type['type'][$item['ITEMDEPTCODE']].'('.$item['ITEMNAME'].')';
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="2105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $init['init']['frontunit'].$item['AMT'].$init['init']['unit'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
					else{
					}
				}
				$table .= '</w:tbl>';
				//$document->setValue('moneyin',$table);
				//$table='';
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblInd w:w="-107" w:type="dxa"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2895"/><w:gridCol w:w="2105"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="008C4EB0" w:rsidTr="00B56C68"><w:trPr><w:trHeight w:val="20"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="008C4EB0" w:rsidRPr="00014EA3" w:rsidRDefault="008C4EB0" w:rsidP="00B56C68"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['outmoneylistname'];
				}
				else{
					$table .= '支出費用明細';
				}
				$table .= '</w:t></w:r></w:p></w:tc>';
				$table .= '</w:tr>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2895" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['outmoneylistlabel'];
				}
				else{
					$table .= '科目';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['outmoneylistsub'];
				}
				else{
					$table .= "Sub";
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				foreach($moeyout as $item){
					if($item['DTLMODE']=='4'){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="2895" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						if($item['ITEMNAME']==''){
							$table .= $type['type'][$item['ITEMDEPTCODE']];
						}
						else{
							$table .= $type['type'][$item['ITEMDEPTCODE']].'('.$item['ITEMNAME'].')';
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="2105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $init['init']['frontunit'].$item['AMT'].$init['init']['unit'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
					else{
					}
				}
				$table .= '</w:tbl><w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p>';
			}
			else{
			}
			//$document->setValue('moneyout',$table);
			$document->setValue('saledetail',$table);
			date_default_timezone_set($init['init']['settime']);
			if($print['item']['printchange']!='0'){
				//$document->save("../../../print/noread/".date('YmdHis')."_paper.docx");
				$filename=date('YmdHis');
				$document->save("../../../print/read/".$filename."_paper.docx");
				if(isset($_POST['machinename'])&&$_POST['machinename']!=''&&isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
					$prt=fopen("../../../print/noread/".$filename."_paper.".$_POST['machinename'],'w');
				}
				else{
					$prt=fopen("../../../print/noread/".$filename."_paper.m1",'w');
				}
				fclose($prt);
				/*$prt=fopen("../../../print/noread/log_paper.txt",'w');
				fwrite($prt,$table);
				fclose($prt);*/
			}
			else{
				$document->save("../../../print/read/delete_paper.docx");
			}
			if(isset($posdvr)){
				fwrite($posdvr,$tempdvrcontent);
				fclose($posdvr);
			}
			else{
			}
		}
		else if(isset($_POST['type'])&&$_POST['type']=='view'){//只發生在瀏覽報表
			echo '<div style="width:100%;height:100%;overflow-x:scroll;-moz-column-count: 3;-moz-column-gap: 10px;-webkit-column-count: 3;-webkit-column-gap: 10px;column-count: 3;column-gap: 10px;">';
			echo '<table style="width:100%;border-collapse:collapse;text-align:right;">';
				//echo '<caption>';
					//echo '<table style="width:100%;border-collapse:collapse;">';
						echo '<tr>
								<td colspan="2" style="text-align:center;">'.$setup['basic']['storyname'].'</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align:center;">';
								if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']==$_POST['papbizdateE']){
									if($_POST['zcounter']=='allday'){
										echo $_POST['papbizdateS'];
									}
									else{
										echo $_POST['papbizdateS'].'-'.$_POST['zcounter'];
									}
								}
								else if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']!=$_POST['papbizdateE']){
									echo $_POST['papbizdateS'].'~'.$_POST['papbizdateE'];
								}
								else{
									echo $dbdate.'-'.$timeini['time']['zcounter'];
									//echo $dbdate.'-'.$machinedata['basic']['zcounter'];
								}
								if($paper!='-1'){
									echo $paper['name']['titlename'];
								}
								else{
									echo '交班表';
								}
							echo '</td>
							</tr>';
					//echo '</table>';
				//echo '</caption>
				echo '<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['salemoneyname'];
						}
						else{
							echo '銷售總額:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$initmoney.$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['memberdisname'];
						}
						else{
							echo '(-)會員折扣:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$dis3.$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['chargename'];
						}
						else{
							echo '(+)服務費:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$charge.$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['floorname'];
						}
						else{
							echo '(+)低銷溢收:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].($afmoney-$initmoney-$dis1-$dis2-$dis3).$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['itemdisname'];
						}
						else{
							echo '(-)單品折讓金額:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$dis1.$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['listdisname'];
						}
						else{
							echo '(-)帳單折讓金額:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$dis2.$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['totalsalename'];
						}
						else{
							echo '銷售小計:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].(floatval($charge)+floatval($afmoney)).$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['cashcommname'];
						}
						else{
							echo '(+)信用卡手續費:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$cashcomm.$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['cashname'];
						}
						else{
							echo '(-)信用卡收入:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$cash.$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['otherpayname'];
						}
						else{
							echo '(-)其他付款:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$sumotherpay.$init['init']['unit'].'</td>
					</tr>';
					if(isset($init['init']['intellapay'])&&$init['init']['intellapay']=='1'){
						echo '<tr>
								<td>';
								if($paper!='-1'&&isset($paper['name']['intellapayname'])){
									echo $paper['name']['intellapayname'];
								}
								else{
									echo '(-)英特拉支付:';
								}
							echo '</td>
								<td>'.$init['init']['frontunit'].$other['intella'].$init['init']['unit'].'</td>
							</tr>';
					}
					else{
					}
					//2021/8/18
					if(isset($init['nidin']['usenidin'])&&$init['nidin']['usenidin']=='1'){
						echo '<tr>
								<td>';
								if($paper!='-1'&&isset($paper['name']['nidinpayname'])){
									echo $paper['name']['nidinpayname'];
								}
								else{
									echo '(-)你訂支付:';
								}
							echo '</td>
								<td>'.$init['init']['frontunit'].$other['nidin'].$init['init']['unit'].'</td>
							</tr>';
					}
					else{
					}
				echo '<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['inmoneyname'];
						}
						else{
							echo '(+)收入費用:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$inmoney.$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['outmoneyname'];
						}
						else{
							echo '(-)支出費用:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$outmoney.$init['init']['unit'].'</td>
					</tr>';
				/*echo '<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['notmoneyname'];
						}
						else{
							echo '(+)非本班現金:';
						}
					echo '</td>
						<td>';
						if(isset($cover)&&sizeof($cover)>0){
							if(isset($cover[0]['tax2'])){
								echo $init['init']['frontunit'].$cover[0]['tax2'].$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
						}
						else{
							echo $init['init']['frontunit'].'0'.$init['init']['unit'];
						}
					echo '</td>
					</tr>';*/
				echo '<tr>
						<td>';
						if(isset($paper['name']['memmoneyname'])){
							echo $paper['name']['memmoneyname'];
						}
						else{
							echo '(+)儲值現金收入:';
						}
					echo '</td>
						<td>';
						if(isset($memmoney[0])){
							echo $init['init']['frontunit'].$memmoney[0]['totalmemmoney'].$init['init']['unit'];
						}
						else{
							echo $init['init']['frontunit'].'0'.$init['init']['unit'];
						}
					echo '</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['realysalename'];
						}
						else{
							echo '繳回金額(不含找零金):';
						}
					echo '</td>
						<td>';
						if(isset($cover[0]['tax2'])&&$cover[0]['tax2']!='0'){
							if(isset($memmoney[0])){
								echo $init['init']['frontunit'].($realysale+$cover[0]['tax2']+$memmoney[0]['totalmemmoney']).$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].($realysale+$cover[0]['tax2']).$init['init']['unit'];
							}
						}
						else{
							if(isset($memmoney[0])){
								echo $init['init']['frontunit'].($realysale+$memmoney[0]['totalmemmoney']).$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].$realysale.$init['init']['unit'];
							}
						}
					echo '</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['changename'];
						}
						else{
							echo '找零金:';
						}
					echo '</td>
						<td>'.$init['init']['frontunit'].$machinedata['basic']['change'].$init['init']['unit'].'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['shouldname'];
						}
						else{
							echo '錢櫃金額(含找零金):';
						}
					echo '</td>
						<td>';
						if(isset($cover[0]['tax2'])&&$cover[0]['tax2']!='0'){
							if(isset($memmoney[0])){
								echo $init['init']['frontunit'].($realysale+$machinedata['basic']['change']+$cover[0]['tax2']+$memmoney[0]['totalmemmoney']).$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].($realysale+$machinedata['basic']['change']+$cover[0]['tax2']).$init['init']['unit'];
							}
						}
						else{
							if(isset($memmoney[0])){
								echo $init['init']['frontunit'].($realysale+$machinedata['basic']['change']+$memmoney[0]['totalmemmoney']).$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].($realysale+$machinedata['basic']['change']).$init['init']['unit'];
							}
						}
					echo '</td>
					</tr>
				</table>';
			
			$saletype=preg_split('/,/',$init['init']['orderlocation']);
			$allqty=intval($list1[0]['qty'])+intval($list2[0]['qty'])+intval($list3[0]['qty'])+intval($list4[0]['qty']);
			$allamt=intval($list1[0]['amt'])+intval($list2[0]['amt'])+intval($list3[0]['amt'])+intval($list4[0]['amt']);
			
			$table='';
			//2021/10/7 因為作廢帳單號沒卡在營業資訊中，若沒有開啟營業資訊，則無法顯示作廢單號；因此將該迴圈拉到這裡
			$count=0;
			$money=0;
			$allvoidlist='';
			for($i=0;$i<sizeof($voidlist);$i++){
				if(strlen($allvoidlist)==0){
					$allvoidlist=intval($voidlist[$i]['CONSECNUMBER']);
				}
				else{
					$allvoidlist=$allvoidlist.','.intval($voidlist[$i]['CONSECNUMBER']);
				}
				$money=intval($money)+intval($voidlist[$i]['SALESTTLAMT']);
				$count++;
			}
			echo '<table style="width:100%;border-collapse:collapse;text-align:right;">';
			if($print!='-1'&&isset($print['block']['saledetail'])&&$print['block']['saledetail']=='1'){
				
				echo '<tr>
						<td colspan="2" style="height:20px;"></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;">';
						if($paper!='-1'){
							echo $paper['name']['saletitlename'];
						}
						else{
							echo "營業資訊";
						}
					echo '</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;">';
						if($paper!='-1'){
							echo $paper['name']['salettlname'];
						}
						else{
							echo "彙總";
						}
					echo '</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['listqtyttlname'];
						}
						else{
							echo "帳單總數:";
						}
					echo '</td>
						<td>'.$allqty.'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['amtttlname'];
						}
						else{
							echo "總金額:";
						}
					echo '</td>
						<td>'.$allamt.'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['avgttlname'];
						}
						else{
							echo "平均金額:";
						}
					echo '</td>
						<td>';
						if($allqty==0){
							echo $init['init']['frontunit'].'0'.$init['init']['unit'];
						}
						else{
							echo $init['init']['frontunit'].bcdiv($allamt,$allqty,$init['init']['accuracy']).$init['init']['unit'];
						}
					echo '</td>
					</tr>';
				if(in_array(1,$saletype)){
					echo '<tr>
							<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
							if($button!='-1'){
								echo $button['name']['listtype1'];
							}
							else{
								echo "內用";
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['listqty1name'];
							}
							else{
								echo "帳單總數:";
							}
						echo '</td>
							<td>'.$list1[0]['qty'].'</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['amt1name'];
							}
							else{
								echo "總金額:";
							}
						echo '</td>
							<td>';
							if($list1[0]['amt']==''){
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].number_format($list1[0]['amt']).$init['init']['unit'];
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['avg1name'];
							}
							else{
								echo "平均金額:";
							}
						echo '</td>
							<td>';
							if($list1[0]['qty']==0){
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].number_format(bcdiv($list1[0]['amt'],$list1[0]['qty'],$init['init']['accuracy'])).$init['init']['unit'];
							}
						echo '</td>
						</tr>';
					$totalpersons=0;
					if($init['init']['openpersoncount']=='1'){
						for($i=1;$i<=3;$i++){
							if($floorspend['person'.$i]['name']==''){
							}
							else{
								echo '<tr>
										<td>';
								$ttt=0;
								for($j=$i+1;$j<3;$j++){
									if(isset($floorspend['person'.$j])&&$floorspend['person'.$j]['name']==''){
									}
									else{
										$ttt=1;
										break;
									}
								}
								echo $floorspend['person'.$i]['name'].':</td>
										<td>';
								if($list1[0]['TAX'.(5+$i)]==0){
									echo $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									$totalpersons=intval($totalpersons)+intval($list1[0]['TAX'.(5+$i)]);
									echo $init['init']['frontunit'].$list1[0]['TAX'.(5+$i)].$init['init']['unit'];
								}
									echo '</td>
									</tr>';
							}
						}
						echo '<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['avgpersonname'];
								}
								else{
									echo "平均客單價:";
								}
							echo '</td>
								<td>';
								if($totalpersons==0){
									echo $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									echo $init['init']['frontunit'].bcdiv($list1[0]['amt'],$totalpersons,$init['init']['accuracy']).$init['init']['unit'];
								}
							echo '</td>
							</tr>';
					}
					else{
					}
				}
				else{
				}
				if(in_array(2,$saletype)){
					echo '<tr>
							<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
							if($button!='-1'){
								echo $button['name']['listtype2'];
							}
							else{
								echo "外帶";
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['listqty2name'];
							}
							else{
								echo "帳單總數:";
							}
						echo '</td>
							<td>'.$list2[0]['qty'].'</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['amt2name'];
							}
							else{
								echo "總金額:";
							}
						echo '</td>
							<td>';
							if($list2[0]['amt']==''){
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].number_format($list2[0]['amt']).$init['init']['unit'];
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['avg2name'];
							}
							else{
								echo "平均金額:";
							}
						echo '</td>
							<td>';
							if($list2[0]['qty']==0){
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].number_format(bcdiv($list2[0]['amt'],$list2[0]['qty'],$init['init']['accuracy'])).$init['init']['unit'];
							}
						echo '</td>
						</tr>';
					$totalpersons=0;
					if($init['init']['openpersoncount']=='1'){
						for($i=1;$i<=3;$i++){
							if($floorspend['person'.$i]['name']==''){
							}
							else{
								echo '<tr>
										<td>';
								$ttt=0;
								for($j=$i+1;$j<3;$j++){
									if(isset($floorspend['person'.$j])&&$floorspend['person'.$j]['name']==''){
									}
									else{
										$ttt=1;
										break;
									}
								}
								echo $floorspend['person'.$i]['name'].':</td>
										<td>';
										if($list2[0]['TAX'.(5+$i)]==0){
											echo $init['init']['frontunit'].'0'.$init['init']['unit'];
										}
										else{
											$totalpersons=intval($totalpersons)+intval($list2[0]['TAX'.(5+$i)]);
											echo $init['init']['frontunit'].$list2[0]['TAX'.(5+$i)].$init['init']['unit'];
										}
									echo '</td>
									</tr>';
							}
						}
						echo '<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['avgpersonname'];
								}
								else{
									echo "平均客單價:";
								}
							echo '</td>
								<td>';
								if($totalpersons==0){
									echo $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									echo $init['init']['frontunit'].bcdiv($list2[0]['amt'],$totalpersons,$init['init']['accuracy']).$init['init']['unit'];
								}
							echo '</td>
							</tr>';
					}
					else{
					}
				}
				else{
				}
				if(in_array(3,$saletype)){
					echo '<tr>
							<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
							if($button!='-1'){
								echo $button['name']['listtype3'];
							}
							else{
								echo '外送';
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['listqty3name'];
							}
							else{
								echo "帳單總數:";
							}
						echo '</td>
							<td>'.$list3[0]['qty'].'</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['amt3name'];
							}
							else{
								echo "總金額:";
							}
						echo '</td>
							<td>';
							if($list3[0]['amt']==''){
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].number_format($list3[0]['amt']).$init['init']['unit'];
							}
						echo '</td>
						</tr>';
					echo '<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['avg3name'];
							}
							else{
								echo "平均金額:";
							}
						echo '</td>
							<td>';
							if($list3[0]['qty']==0){
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].bcdiv($list3[0]['amt'],$list3[0]['qty'],$init['init']['accuracy']).$init['init']['unit'];
							}
						echo '</td>
						</tr>';
					$totalpersons=0;
					if($init['init']['openpersoncount']=='1'){
						for($i=1;$i<=3;$i++){
							if($floorspend['person'.$i]['name']==''){
							}
							else{
								$ttt=0;
								for($j=$i+1;$j<3;$j++){
									if(isset($floorspend['person'.$j])&&$floorspend['person'.$j]['name']==''){
									}
									else{
										$ttt=1;
										break;
									}
								}
								echo '<tr>
										<td>'.$floorspend['person'.$i]['name'].':</td>
										<td>';
										if($list3[0]['TAX'.(5+$i)]==0){
											echo $init['init']['frontunit'].'0'.$init['init']['unit'];
										}
										else{
											$totalpersons=intval($totalpersons)+intval($list3[0]['TAX'.(5+$i)]);
											echo $init['init']['frontunit'].$list3[0]['TAX'.(5+$i)].$init['init']['unit'];
										}
									echo '</td>
									</tr>';
							}
						}
						echo '<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['avgpersonname'];
								}
								else{
									echo "平均客單價:";
								}
							echo '</td>
								<td>';
								if($totalpersons==0){
									echo $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									echo $init['init']['frontunit'].bcdiv($list3[0]['amt'],$totalpersons,$init['init']['accuracy']).$init['init']['unit'];
								}
							echo '</td>
							</tr>';
					}
					else{
					}
				}
				else{
				}
				if(in_array(4,$saletype)){
					echo '<tr>
							<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
							if($button!='-1'){
								echo $button['name']['listtype4'];
							}
							else{
								echo "自取";
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['listqty4name'];
							}
							else{
								echo "帳單總數:";
							}
						echo '</td>
							<td>'.$list4[0]['qty'].'</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['amt4name'];
							}
							else{
								echo "總金額:";
							}
						echo '</td>
							<td>';
							if($list4[0]['amt']==''){
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].$list4[0]['amt'].$init['init']['unit'];
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['avg4name'];
							}
							else{
								echo "平均金額:";
							}
						echo '</td>
							<td>';
							if($list4[0]['qty']==0){
								echo $init['init']['frontunit'].'0'.$init['init']['unit'];
							}
							else{
								echo $init['init']['frontunit'].bcdiv($list4[0]['amt'],$list4[0]['qty'],$init['init']['accuracy']).$init['init']['unit'];
							}
						echo '</td>
						</tr>';
					$totalpersons=0;
					if($init['init']['openpersoncount']=='1'){
						for($i=1;$i<=3;$i++){
							if($floorspend['person'.$i]['name']==''){
							}
							else{
								$ttt=0;
								for($j=$i+1;$j<3;$j++){
									if(isset($floorspend['person'.$j])&&$floorspend['person'.$j]['name']==''){
									}
									else{
										$ttt=1;
										break;
									}
								}
								echo '<tr>
										<td>'.$floorspend['person'.$i]['name'].':</td>
										<td>';
										if($list4[0]['TAX'.(5+$i)]==0){
											echo $init['init']['frontunit'].'0'.$init['init']['unit'];
										}
										else{
											$totalpersons=intval($totalpersons)+intval($list4[0]['TAX'.(5+$i)]);
											echo $init['init']['frontunit'].$list4[0]['TAX'.(5+$i)].$init['init']['unit'];
										}
									echo '</td>
									</tr>';
							}
						}
						echo '<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['avgpersonname'];
								}
								else{
									echo "平均客單價:";
								}
							echo '</td>
								<td>';
								if($totalpersons==0){
									echo $init['init']['frontunit'].'0'.$init['init']['unit'];
								}
								else{
									echo $init['init']['frontunit'].bcdiv($list4[0]['amt'],$totalpersons,$init['init']['accuracy']).$init['init']['unit'];
								}
							echo '</td>
							</tr>';
					}
					else{
					}
				}
				else{
				}
				//2021/10/7 因為作廢帳單號沒卡在營業資訊中，若沒有開啟營業資訊，則無法顯示作廢單號；因此將該迴圈拉到前面
				/*$count=0;
				$money=0;
				$allvoidlist='';
				for($i=0;$i<sizeof($voidlist);$i++){
					if(strlen($allvoidlist)==0){
						$allvoidlist=intval($voidlist[$i]['CONSECNUMBER']);
					}
					else{
						$allvoidlist=$allvoidlist.','.intval($voidlist[$i]['CONSECNUMBER']);
					}
					$money=intval($money)+intval($voidlist[$i]['SALESTTLAMT']);
					$count++;
				}*/
				echo '<tr>
						<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
						if($paper!='-1'){
							echo $paper['name']['titleVname'];
						}
						else{
							echo "作廢帳單";
						}
					echo '</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['listqtyVname'];
						}
						else{
							echo "帳單總數:";
						}
					echo '</td>
						<td>'.number_format($count).'</td>
					</tr>
					<tr>
						<td>';
						if($paper!='-1'){
							echo $paper['name']['amtVname'];
						}
						else{
							echo "總金額:";
						}
					echo '</td>
						<td>'.number_format($money).'</td>
					</tr>';
			}
			else{
			}
			//$document->setValue('saledetail',$table);
			if($print!='-1'&&isset($print['block']['allvoidlist'])&&$print['block']['allvoidlist']=='1'){
				echo '<tr>
						<td colspan="2" style="text-align:left;word-break: break-all;">
							';
							if($paper!='-1'){
								echo $paper['name']['voidlistname'].'<br>';
								if(isset($allvoidlist)){
									echo $allvoidlist;
								}
							}
							else{
								echo '作廢帳單號:<br>';
								if(isset($allvoidlist)){
									echo $allvoidlist;
								}
							}
						echo '
						</td>
					</tr>';
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['memmoney'])&&$print['block']['memmoney']=='1'&&isset($memmoney[0])){
				echo '
						<tr>
							<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
				if($paper!='-1'&&isset($paper['name']['memmoneylist'])){
					echo $paper['name']['memmoneylist'];
				}
				else{
					echo '會員儲值列表';
				}
						echo '</td>
						</tr>';
				echo '<tr>
						<td style="text-align:center;">';
				if($paper!='-1'&&isset($paper['name']['memno'])){
					echo $paper['name']['memno'];
				}
				else{
					echo '會員電話';
				}
					echo '</td>
						<td style="text-align:center;">';
				if($paper!='-1'&&isset($paper['name']['paymoney'])){
					echo $paper['name']['paymoney'];
				}
				else{
					echo '儲值金額';
				}
					echo '</td>
					</tr>';
				
				for($i=0;$i<sizeof($memmoney);$i++){
					echo '<tr>
							<td style="text-align:left;">';
					if($memmoney[$i]['ITEMCODE']!=''){
						echo $memmoney[$i]['ITEMCODE'];
					}
					else{
						echo '('.$memmoney[$i]['ITEMNAME'].')';
					}
						echo '</td>
							<td>';
					echo $memmoney[$i]['UNITPRICE'];
						echo '</td>
						</tr>';
				}
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['otherpay'])&&$print['block']['otherpay']=='1'){
				if($otherpay!='-1'&&$otherpay['pay']['openpay']==1){
					echo '
							<tr>
								<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
								if($paper!='-1'){
									echo $paper['name']['otherpaynamelist'];
								}
								else{
									echo '其他付款項目';
								}
							echo '</td>
							</tr>';
					
					for($i=1;$i<sizeof($otherpay);$i++){
						echo '<tr>
								<td>'.$otherpay['item'.$i]['name'].':</td>';
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							echo '<td>'.$init['init']['frontunit'].number_format($other[$otherpay['item'.$i]['dbname']]).$init['init']['unit'].'</td>';
						}
						else{
							echo '<td>'.$init['init']['frontunit'].number_format($other[$otherpay['item'.$i]['location']]).$init['init']['unit'].'</td>';
						}
						echo '</tr>';
					}

					if(isset($cover)&&sizeof($cover)>0){
						echo '
								<tr>
									<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
									if($paper!='-1'){
										echo $paper['name']['notothertitlename'];
									}
									else{
										echo "非本班其他付款";
									}
								echo '</td>
								</tr>';

						for($i=1;$i<sizeof($otherpay);$i++){
							echo '<tr>
									<td>'.$otherpay['item'.$i]['name'].':</td>';
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							echo '<td>'.$init['init']['frontunit'].number_format($cover[0][$otherpay['item'.$i]['dbname']]).$init['init']['unit'].'</td>';
						}
						else{
							echo '<td>'.$init['init']['frontunit'].number_format($cover[0][$otherpay['item'.$i]['location']]).$init['init']['unit'].'</td>';
						}
						echo '</tr>';
						}
					}
					else{
					}
				}
				else{
				}
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['inv'])&&$print['block']['inv']=='1'){
				if($init['init']['useinv']==1&&isset($invlist)){
					$qty=0;
					$amt=0;
					$voidqty=0;
					$voidamt=0;
					$voidlist='';
					for($i=0;$i<sizeof($invlist);$i++){
						$amt=intval($amt)+intval($invlist[$i]['totalamount']);
						$qty++;
					}
					for($i=0;$i<sizeof($invvoidlist);$i++){
						if(strlen($voidlist)==''){
							$voidlist=$invvoidlist[$i]['invnumber'];
						}
						else{
							$voidlist=$voidlist.','.$invvoidlist[$i]['invnumber'];
						}
						$voidamt=intval($voidamt)+intval($invvoidlist[$i]['totalamount']);
						$voidqty++;
					}
					echo '
							<tr>
								<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
								if($paper!='-1'){
									echo $paper['name']['invnotvoidname'];
								}
								else{
									echo "發票彙總(不含作廢)";
								}
							echo '</td>
							</tr>
							<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['invalready'];
								}
								else{
									echo "已開發票數:";
								}
							echo '</td>
								<td>'.$qty.'</td>
							</tr>
							<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['invmoneyalready'];
								}
								else{
									echo "已開立金額:";
								}
							echo '</td>
								<td>'.number_format($amt).'</td>
							</tr>
							<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['startinv'];
								}
								else{
									echo "起始發票號:";
								}
							echo '</td>';
							if(isset($invlist[0])){
								echo '<td>'.$invlist[0]['invnumber'].'</td>';
							}
							else{
								echo '<td></td>';
							}
						echo '</tr>
							<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['endinv'];
								}
								else{
									echo "結束發票號:";
								}
							echo '</td>';
							if(isset($invlist[0])){
								echo '<td>'.$invlist[sizeof($invlist)-1]['invnumber'].'</td>';
							}
							else{
								echo '<td></td>';
							}
						echo '</tr>
							<tr>
								<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
								if($paper!='-1'){
									echo $paper['name']['voidinv'];
								}
								else{
									echo "作廢發票";
								}
							echo '</td>
							</tr>
							<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['voidqty'];
								}
								else{
									echo "作廢數量:";
								}
							echo '</td>
								<td>'.$voidqty.'</td>
							</tr>
							<tr>
								<td>';
								if($paper!='-1'){
									echo $paper['name']['voidmoney'];
								}
								else{
									echo "作廢金額:";
								}
							echo '</td>
								<td>'.number_format($voidamt).'</td>
							</tr>
						';
					//$document->setValue('invtable',$table);
					echo '<tr>
							<td colspan="2" style="text-align:left;word-break: break-all;">
								';
								if($paper!='-1'){
									echo $paper['name']['voidinvoicenumber'].'<br>'.$voidlist;
								}
								else{
									echo '作廢發票號:<br>'.$voidlist;
								}
							echo '
							</td>
						</tr>';
				}
				else{
				}
			}
			else{
			}
			echo '</table>';
			if($print!='-1'&&isset($print['block']['rearlist'])&&$print['block']['rearlist']=='1'){
				echo '<table style="width:100%;border-collapse:collapse;text-align:right;">
						<tr>
							<td colspan="2" style="height:20px;"></td>
						</tr>
						<tr>
							<td style="margin-top:20px;text-align:left;border-top:1px solid #000000;">';
							if($paper!='-1'){
								echo $paper['name']['reartitle'];
							}
							else{
								echo "分析類別";
							}
						echo '</td>
							<td style="margin-top:20px;border-top:1px solid #000000;">';
							if($paper!='-1'){
								echo $paper['name']['rearqty'];
							}
							else{
								echo "QTY";
							}
						echo '</td>
						</tr>';
				foreach($rearlist as $item){
					echo '<tr>
							<td style="text-align:left;">'.$item['name'].'</td>
							<td>'.$item['QTY'].'</td>
						</tr>';
				}
				echo '</table>';
			}
			else{
			}
			if($print!='-1'&&isset($print['block']['salelist'])&&$print['block']['salelist']=='1'){
				echo '<table style="width:100%;border-collapse:collapse;text-align:right;">
						<tr>
							<td colspan="2" style="height:20px;"></td>
						</tr>
						<tr>
							<td style="text-align:left;border-top:1px solid #000000;">';
							if($paper!='-1'){
								echo $paper['name']['saleitem'];
							}
							else{
								echo 'Items';
							}
						echo '</td>
							<td style="border-top:1px solid #000000;">';
							if($paper!='-1'){
								echo $paper['name']['saleqty'];
							}
							else{
								echo 'QTY';
							}
						echo '</td>
							<td style="border-top:1px solid #000000;">';
							if($paper!='-1'){
								echo $paper['name']['saleamt'];
							}
							else{
								echo 'AMT';
							}
						echo '</td>
						</tr>';
				$itemdeptcode='';
				foreach($listdetail as $item){
					if($itemdeptcode==''||$itemdeptcode!=$item['ITEMDEPTCODE']){
						$itemdeptcode=$item['ITEMDEPTCODE'];
						echo '<tr>
								<td style="border-top:1px dashed #000000;text-align:left;">'.$item['ITEMNAME'].'</td>
								<td style="border-top:1px dashed #000000;">'.number_format($item['QTY']).'</td>
								<td style="border-top:1px dashed #000000;">'.number_format($item['AMT']).'</td>
							</tr>';
					}
					else{
						echo '<tr>
								<td style="text-align:left;">'.$item['ITEMNAME'].'</td>
								<td>'.number_format($item['QTY']).'</td>
								<td>'.number_format($item['AMT']).'</td>
							</tr>';
					}
				}
				echo '</table>';
			}
			else{
			}
			
			if($print!='-1'&&isset($print['block']['inoutmoney'])&&$print['block']['inoutmoney']=='1'&&sizeof($moeyout)>0){
				echo '<table style="width:100%;border-collapse:collapse;text-align:right;">
						<tr>
							<td colspan="2" style="height:20px;"></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
							if($paper!='-1'){
								echo $paper['name']['inmoneylistname'];
							}
							else{
								echo '收入費用明細';
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['inmoneylistlabel'];
							}
							else{
								echo '科目';
							}
						echo '</td>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['inmoneylistsub'];
							}
							else{
								echo 'Sub';
							}
						echo '<td>
						</tr>';
				foreach($moeyout as $item){
					if($item['DTLMODE']=='3'){
						echo '<tr>
								<td>';
								if($item['ITEMNAME']==''){
									echo $type['type'][$item['ITEMDEPTCODE']];
								}
								else{
									echo $type['type'][$item['ITEMDEPTCODE']].'('.$item['ITEMNAME'].')';
								}
							echo '</td>
								<td>'.$init['init']['frontunit'].number_format($item['AMT']).$init['init']['unit'].'</td>
							</tr>';
					}
					else{
					}
				}
				//$document->setValue('moneyin',$table);
				//$table='';
				echo '
						<tr>
							<td colspan="2" style="height:20px;"></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center;border-top:1px dashed #000000;">';
							if($paper!='-1'){
								echo $paper['name']['outmoneylistname'];
							}
							else{
								echo '支出費用明細';
							}
						echo '</td>
						</tr>
						<tr>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['outmoneylistlabel'];
							}
							else{
								echo '科目';
							}
						echo '</td>
							<td>';
							if($paper!='-1'){
								echo $paper['name']['outmoneylistsub'];
							}
							else{
								echo 'Sub';
							}
						echo '<td>
						</tr>';
				foreach($moeyout as $item){
					if($item['DTLMODE']=='4'){
						echo '<tr>
								<td>';
								if($item['ITEMNAME']==''){
									echo $type['type'][$item['ITEMDEPTCODE']];
								}
								else{
									echo $type['type'][$item['ITEMDEPTCODE']].'('.$item['ITEMNAME'].')';
								}
							echo '</td>
								<td>'.$init['init']['frontunit'].number_format($item['AMT']).$init['init']['unit'].'</td>
							</tr>';
					}
					else{
					}
				}
				echo '</table>';
			}
			else{
			}
			echo '</div>';
		}
		else{
		}
	}
}
else{
}

?>