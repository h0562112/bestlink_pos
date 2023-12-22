<?php
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
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$bizdate=$timeini['time']['bizdate'];
$otherpay=parse_ini_file('../../../database/otherpay.ini',true);


$cover=array();
$paylist=array();
$paylist['salesttlamt']=0;
$paylist['tax1']=0;
$paylist['tax2']=0;
$paylist['tax3']=0;
$paylist['tax4']=0;
$paylist['tax9']=0;
$paylist['ta1']=0;
$paylist['ta2']=0;
$paylist['ta3']=0;
$paylist['ta4']=0;
$paylist['ta5']=0;
$paylist['ta6']=0;
$paylist['ta7']=0;
$paylist['ta8']=0;
$paylist['ta9']=0;
$paylist['ta10']=0;
$paylist['nontax']=0;
if(file_exists('../../../database/sale/Cover.db')){
	$conn=sqlconnect('../../../database/sale','Cover.db','','','','sqlite');
	$sql='SELECT bizdate,consecnumber,salesttlamt,tax1,tax9,tax2,tax3,tax4';
	for($i=1;$i<=10;$i++){
		$sql=$sql.',(CASE posTA'.$i.' WHEN 0 THEN ta'.$i.' ELSE SUBSTR(ta'.$i.',1,posTA'.$i.'-1) END) pointta'.$i.',(CASE posTA'.$i.' WHEN 0 THEN ta'.$i.' ELSE SUBSTR(ta'.$i.',posTA'.$i.'+1) END) ta'.$i;
	}
	$sql=$sql.',(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) nontax FROM (SELECT *,INSTR(ta1,"=") AS posTA1,INSTR(ta2,"=") AS posTA2,INSTR(ta3,"=") AS posTA3,INSTR(ta4,"=") AS posTA4,INSTR(ta5,"=") AS posTA5,INSTR(ta6,"=") AS posTA6,INSTR(ta7,"=") AS posTA7,INSTR(ta8,"=") AS posTA8,INSTR(ta9,"=") AS posTA9,INSTR(ta10,"=") AS posTA10,INSTR(nontax,"=") AS posNONTAX FROM list WHERE bizdate="'.$bizdate.'" AND state=1 GROUP BY bizdate,consecnumber) GROUP BY bizdate,consecnumber';
	$tempcover=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($tempcover[0]['coverbizdate'])){
		$consecnumberar=array_column($tempcover,'consecnumber');
		foreach($tempcover as $tc){
			$cover[$tc['consecnumber']]['salesttlamt']=$tc['salesttlamt'];
			$cover[$tc['consecnumber']]['tax1']=$tc['tax1'];
			$cover[$tc['consecnumber']]['tax2']=$tc['tax2'];
			$cover[$tc['consecnumber']]['tax3']=$tc['tax3'];
			$cover[$tc['consecnumber']]['tax4']=$tc['tax4'];
			$cover[$tc['consecnumber']]['tax9']=$tc['tax9'];
			$cover[$tc['consecnumber']]['ta1']=$tc['ta1'];
			$cover[$tc['consecnumber']]['ta2']=$tc['ta2'];
			$cover[$tc['consecnumber']]['ta3']=$tc['ta3'];
			$cover[$tc['consecnumber']]['ta4']=$tc['ta4'];
			$cover[$tc['consecnumber']]['ta5']=$tc['ta5'];
			$cover[$tc['consecnumber']]['ta6']=$tc['ta6'];
			$cover[$tc['consecnumber']]['ta7']=$tc['ta7'];
			$cover[$tc['consecnumber']]['ta8']=$tc['ta8'];
			$cover[$tc['consecnumber']]['ta9']=$tc['ta9'];
			$cover[$tc['consecnumber']]['ta10']=$tc['ta10'];
			$cover[$tc['consecnumber']]['nontax']=$tc['nontax'];
		}
	}
	else{
	}
}
else{
}
if(file_exists('../../../database/sale/SALES_'.substr($bizdate,0,6).'.db')){
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($bizdate,0,6).'.db','','','','sqlite');
	$sql='SELECT BIZDATE,CONSECNUMBER,SALESTTLAMT,TAX1,TAX9,TAX2,TAX3,TAX4';
	for($i=1;$i<=10;$i++){
		$sql=$sql.',(CASE posTA'.$i.' WHEN 0 THEN TA'.$i.' ELSE SUBSTR(TA'.$i.',1,posTA'.$i.'-1) END) pointta'.$i.',(CASE posTA'.$i.' WHEN 0 THEN TA'.$i.' ELSE SUBSTR(TA'.$i.',posTA'.$i.'+1) END) ta'.$i;
	}
	$sql=$sql.',(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,posNONTAX+1) END) NONTAX FROM (SELECT *,INSTR(TA1,"=") AS posTA1,INSTR(TA2,"=") AS posTA2,INSTR(TA3,"=") AS posTA3,INSTR(TA4,"=") AS posTA4,INSTR(ta5,"=") AS posTA5,INSTR(TA6,"=") AS posTA6,INSTR(TA7,"=") AS posTA7,INSTR(TA8,"=") AS posTA8,INSTR(TA9,"=") AS posTA9,INSTR(TA10,"=") AS posTA10,INSTR(NONTAX,"=") AS posNONTAX FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER IS NULL GROUP BY BIZDATE,CONSECNUMBER) GROUP BY BIZDATE,CONSECNUMBER';
	$tempcover=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($tempcover[0]['BIZDATE'])){
		$consecnumberar=array_column($tempcover,'consecnumber');
		foreach($tempcover as $tc){
			if(isset($cover[$tc['consecnumber']])){
				$paylist['salesttlamt']=floatval($paylist['salesttlamt'])+floatval($cover[$tc['consecnumber']]['salesttlamt']);
				$paylist['tax1']=floatval($paylist['tax1'])+floatval($cover[$tc['consecnumber']]['tax1']);
				$paylist['tax2']=floatval($paylist['tax2'])+floatval($cover[$tc['consecnumber']]['tax2']);
				$paylist['tax3']=floatval($paylist['tax3'])+floatval($cover[$tc['consecnumber']]['tax3']);
				$paylist['tax4']=floatval($paylist['tax4'])+floatval($cover[$tc['consecnumber']]['tax4']);
				$paylist['tax9']=floatval($paylist['tax9'])+floatval($cover[$tc['consecnumber']]['tax9']);
				$paylist['ta1']=floatval($paylist['ta1'])+floatval($cover[$tc['consecnumber']]['ta1']);
				$paylist['ta2']=floatval($paylist['ta2'])+floatval($cover[$tc['consecnumber']]['ta2']);
				$paylist['ta3']=floatval($paylist['ta3'])+floatval($cover[$tc['consecnumber']]['ta3']);
				$paylist['ta4']=floatval($paylist['ta4'])+floatval($cover[$tc['consecnumber']]['ta4']);
				$paylist['ta5']=floatval($paylist['ta5'])+floatval($cover[$tc['consecnumber']]['ta5']);
				$paylist['ta6']=floatval($paylist['ta6'])+floatval($cover[$tc['consecnumber']]['ta6']);
				$paylist['ta7']=floatval($paylist['ta7'])+floatval($cover[$tc['consecnumber']]['ta7']);
				$paylist['ta8']=floatval($paylist['ta8'])+floatval($cover[$tc['consecnumber']]['ta8']);
				$paylist['ta9']=floatval($paylist['ta9'])+floatval($cover[$tc['consecnumber']]['ta9']);
				$paylist['ta10']=floatval($paylist['ta10'])+floatval($cover[$tc['consecnumber']]['ta10']);
				$paylist['nontax']=floatval($paylist['nontax'])+floatval($cover[$tc['consecnumber']]['nontax']);
			}
			else{
				$paylist['salesttlamt']=floatval($paylist['salesttlamt'])+floatval($tc['SALESTTLAMT']);
				$paylist['tax1']=floatval($paylist['tax1'])+floatval($tc['TAX1']);
				$paylist['tax2']=floatval($paylist['tax2'])+floatval($tc['TAX2']);
				$paylist['tax3']=floatval($paylist['tax3'])+floatval($tc['TAX3']);
				$paylist['tax4']=floatval($paylist['tax4'])+floatval($tc['TAX4']);
				$paylist['tax9']=floatval($paylist['tax9'])+floatval($tc['TAX9']);
				$paylist['ta1']=floatval($paylist['ta1'])+floatval($tc['ta1']);
				$paylist['ta2']=floatval($paylist['ta2'])+floatval($tc['ta2']);
				$paylist['ta3']=floatval($paylist['ta3'])+floatval($tc['ta3']);
				$paylist['ta4']=floatval($paylist['ta4'])+floatval($tc['ta4']);
				$paylist['ta5']=floatval($paylist['ta5'])+floatval($tc['ta5']);
				$paylist['ta6']=floatval($paylist['ta6'])+floatval($tc['ta6']);
				$paylist['ta7']=floatval($paylist['ta7'])+floatval($tc['ta7']);
				$paylist['ta8']=floatval($paylist['ta8'])+floatval($tc['ta8']);
				$paylist['ta9']=floatval($paylist['ta9'])+floatval($tc['ta9']);
				$paylist['ta10']=floatval($paylist['ta10'])+floatval($tc['ta10']);
				$paylist['nontax']=floatval($paylist['nontax'])+floatval($tc['NONTAX']);
			}
		}
	}
	else{
	}
}
else{
}

//echo json_encode($paylist);
echo '<table>
		<tr>
			<td style="border-bottom: 3px solid;font-weight: bold;font-size: large;">銷售額</td>
			<td style="text-align:right;border-bottom: 3px solid;font-weight: bold;font-size: large;">'.number_format($paylist['salesttlamt']+$paylist['tax1']+$paylist['tax9']).'</td>
		</tr>
		<tr>
			<td>現金</td>
			<td style="text-align:right;">'.number_format($paylist['tax2']).'</td>
		</tr>';
		if($init['init']['cashbut']=='1'){
			'<tr>
				<td>信用卡</td>
				<td style="text-align:right;">'.number_format($paylist['tax3']).'</td>
			</tr>';
		}
		'<!-- <tr>
			<td>現金</td>
			<td style="text-align:right;">'.number_format($paylist['tax4']).'</td>
		</tr> -->';
if($otherpay['pay']['openpay']=='1'){
	for($i=1;$i<sizeof($otherpay);$i++){
		echo '<tr>
				<td>'.$otherpay['item'.$i]['name'].'</td>
				<td style="text-align:right;">'.number_format($paylist[strtolower($otherpay['item'.$i]['dbname'])]).'</td>
			</tr>';
	}
}
else{
}
echo '</table>';
?>