<?php
include_once '../../../tool/date.inc.php';
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
if(isset($init['init']['controltable'])&&$init['init']['controltable']=='1'){//2020/3/20 開啟桌控，在讀取桌號名稱
	$tb=parse_ini_file('../../../database/floorspend.ini',true);//2020/3/20 因為桌號改為對應方式，所以需要額外讀取桌號名稱
}
else{//2020/3/20 沒開啟桌控，顯示原本輸入桌號
}
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$buttonname=parse_ini_file('../../syspram/buttons-'.$init['init']['firlan'].'.ini',true);
$interfacename=parse_ini_file('../../syspram/interface-'.$init['init']['firlan'].'.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(isset($_POST['bizdate'])){
	if($_POST['type']=='-'){
		$bizdate=date('Ymd',strtotime($_POST['bizdate'].' -1 day'));
	}
	else{
		$bizdate=date('Ymd',strtotime($_POST['bizdate'].' +1 day'));
	}
}
else{
	$bizdate=$timeini['time']['bizdate'];
	
}
$sale=array();//saleno
$table=array();//桌號
$inv=array();//2021/7/21 發票資訊 array(發票號碼=>array(createtime,updatetime,carrier=>統編/載具)) 先不記錄統編，只顯示載具

//2021/7/21 底下這判斷式用途不明，且上下兩個操作一模一樣，暫時先移除
//if(intval(substr($bizdate,6,2))==intval(date('t',mktime(0,0,0,substr($bizdate,4,2),substr($bizdate,6,2),substr($bizdate,0,4))))){
	if(file_exists('../../../database/sale/SALES_'.date('Ym',strtotime(substr($bizdate,0,6).'01 -1 month')).'.db')){//撈出前一個月的未結帳單
		$conn=sqlconnect('../../../database/sale','SALES_'.date('Ym',strtotime(substr($bizdate,0,6).'01 -1 month')).'.db','','','','sqlite');
		if(isset($_POST['temp'])&&$_POST['temp']=='temp'){//不分營業日顯示
			$sql='SELECT (SELECT COUNT(*) FROM tempCST011 WHERE NBCHKNUMBER IS NULL) AS ttcount,(SELECT SUM(SALESTTLAMT + TAX1 + TAX9) FROM tempCST011 WHERE NBCHKNUMBER IS NULL) AS ttmoney,TERMINALNUMBER,CONSECNUMBER,BIZDATE,CLKCODE,CLKNAME,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,INVOICENUMBER,(SALESTTLAMT + TAX1 + TAX9) AS SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,TABLENUMBER FROM tempCST011 WHERE NBCHKNUMBER IS NULL ORDER BY substr(CREATEDATETIME||"000",1,17) DESC';
			$datas1=sqlquery($conn,$sql,'sqlite');
			//print_r($datas1);
			foreach($datas1 as $i){
				$table[$i['BIZDATE']][str_pad($i['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]=$i['TABLENUMBER'];
				//2021/7/21
				if(isset($init['init']['useinv'])&&$init['init']['useinv']=='1'&&strlen($i['INVOICENUMBER'])==10){
					$inv[$i['INVOICENUMBER']]['createdatetime']=$i['CREATEDATETIME'];
					$inv[$i['INVOICENUMBER']]['updatedatetime']='';
					$inv[$i['INVOICENUMBER']]['carrier']='';
				}
				else{
				}
			}
			//2021/7/21 未結帳單撈出已結帳單的桌號？
			/*$sql='SELECT CONSECNUMBER,BIZDATE,TABLENUMBER FROM CST011 ORDER BY CREATEDATETIME DESC';
			$temptable=sqlquery($conn,$sql,'sqlite');
			//print_r($temptable);
			foreach($temptable as $i){
				$table[$i['BIZDATE']][str_pad($i['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]=$i['TABLENUMBER'];
			}*/
		}
		else{
		}
		$sql='SELECT * FROM salemap JOIN tempCST011 ON tempCST011.CONSECNUMBER=salemap.consecnumber';
		$tempsale=sqlquery($conn,$sql,'sqlite');
		//$sale=array();
		foreach($tempsale as $i){
			$sale[$i['bizdate']][str_pad($i['consecnumber'],6,"0",STR_PAD_LEFT)]=$i['saleno'];
		}
		sqlclose($conn,'sqlite');
	}
	else{
	}
/*}
else{
	if(file_exists('../../../database/sale/SALES_'.date('Ym',strtotime(substr($bizdate,0,6).'01 -1 month')).'.db')){
		$conn=sqlconnect('../../../database/sale','SALES_'.date('Ym',strtotime(substr($bizdate,0,6).'01 -1 month')).'.db','','','','sqlite');
		if(isset($_POST['temp'])&&$_POST['temp']=='temp'){//不分營業日顯示
			$sql='SELECT (SELECT COUNT(*) FROM tempCST011 WHERE NBCHKNUMBER IS NULL) AS ttcount,(SELECT SUM(SALESTTLAMT + TAX1 + TAX9) FROM tempCST011 WHERE NBCHKNUMBER IS NULL) AS ttmoney,TERMINALNUMBER,CONSECNUMBER,BIZDATE,CLKCODE,CLKNAME,CUSTGPCODE,CUSTGPNAME,CUSTCODE,INVOICENUMBER,(SALESTTLAMT + TAX1 + TAX9) AS SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,TABLENUMBER FROM tempCST011 WHERE NBCHKNUMBER IS NULL ORDER BY CREATEDATETIME DESC';
			$datas1=sqlquery($conn,$sql,'sqlite');
			//print_r($datas1);
			foreach($datas1 as $i){
				$table[$i['BIZDATE']][str_pad($i['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]=$i['TABLENUMBER'];
			}
			$sql='SELECT CONSECNUMBER,BIZDATE,TABLENUMBER FROM CST011 ORDER BY CREATEDATETIME DESC';
			$temptable=sqlquery($conn,$sql,'sqlite');
			//print_r($temptable);
			foreach($temptable as $i){
				$table[$i['BIZDATE']][str_pad($i['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]=$i['TABLENUMBER'];
			}
		}
		else{
		}
		$sql='SELECT * FROM salemap WHERE bizdate="'.$bizdate.'"';
		$tempsale=sqlquery($conn,$sql,'sqlite');
		//$sale=array();
		foreach($tempsale as $i){
			$sale[$i['bizdate']][str_pad($i['consecnumber'],6,"0",STR_PAD_LEFT)]=$i['saleno'];
		}
		sqlclose($conn,'sqlite');
	}
	else{
	}
}*/
if(file_exists('../../../database/sale/SALES_'.substr($bizdate,0,6).'.db')){
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($bizdate,0,6).'.db','','','','sqlite');
	if(isset($_POST['sale'])&&$_POST['sale']=='sale'){
		$sql='SELECT * FROM salemap WHERE bizdate="'.$bizdate.'"';
		$tempsale=sqlquery($conn,$sql,'sqlite');
		//2021/7/21 與下方的sql功能重複
		/*$sql='SELECT BIZDATE,CONSECNUMBER,TABLENUMBER FROM CST011 WHERE BIZDATE="'.$bizdate.'" ORDER BY CREATEDATETIME DESC';
		$temptable=sqlquery($conn,$sql,'sqlite');
		//print_r($datas1);
		foreach($temptable as $i){
			$table[$i['BIZDATE']][str_pad($i['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]=$i['TABLENUMBER'];
		}*/
		$sql='SELECT (SELECT COUNT(*) FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER IS NULL) AS ttcount,(SELECT SUM(SALESTTLAMT + TAX1 + TAX9) FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER IS NULL) AS ttmoney,(SELECT COUNT(*) FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER="Y") AS voidcount,(SELECT SUM(SALESTTLAMT + TAX1 + TAX9) FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER="Y") AS voidmoney,TERMINALNUMBER,BIZDATE,CONSECNUMBER,CLKCODE,CLKNAME,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,INVOICENUMBER,(SALESTTLAMT + TAX1 + TAX9) AS SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,TABLENUMBER FROM CST011 WHERE BIZDATE="'.$bizdate.'" ORDER BY substr(CREATEDATETIME||"000",1,17) DESC';
	}
	else if(isset($_POST['temp'])&&$_POST['temp']=='temp'){//不分營業日顯示
		$sql='SELECT * FROM salemap JOIN tempCST011 ON tempCST011.CONSECNUMBER=salemap.consecnumber';
		$tempsale=sqlquery($conn,$sql,'sqlite');
		//2021/7/21 未結帳單撈出已結帳單的桌號？
		/*$sql='SELECT CONSECNUMBER,BIZDATE,TABLENUMBER FROM CST011 ORDER BY CREATEDATETIME DESC';
		$temptable=sqlquery($conn,$sql,'sqlite');
		//print_r($temptable);
		foreach($temptable as $i){
			$table[$i['BIZDATE']][str_pad($i['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]=$i['TABLENUMBER'];
		}*/
		if(isset($_POST['mancode'])&&$_POST['mancode']!=''){//2021/10/29 orderpos暫結查詢外送員的單
			$sql='SELECT (SELECT COUNT(*) FROM tempCST011 WHERE NBCHKNUMBER IS NULL AND CUSTGPCODE="'.$_POST['mancode'].'") AS ttcount,(SELECT SUM(SALESTTLAMT + TAX1 + TAX9) FROM tempCST011 WHERE NBCHKNUMBER IS NULL AND CUSTGPCODE="'.$_POST['mancode'].'") AS ttmoney,TERMINALNUMBER,CONSECNUMBER,BIZDATE,CLKCODE,CLKNAME,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,INVOICENUMBER,(SALESTTLAMT + TAX1 + TAX9) AS SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,TABLENUMBER FROM tempCST011 WHERE NBCHKNUMBER IS NULL AND CUSTGPCODE="'.$_POST['mancode'].'" ORDER BY substr(CREATEDATETIME||"000",1,17) DESC';
		}
		else{
			$sql='SELECT (SELECT COUNT(*) FROM tempCST011 WHERE NBCHKNUMBER IS NULL) AS ttcount,(SELECT SUM(SALESTTLAMT + TAX1 + TAX9) FROM tempCST011 WHERE NBCHKNUMBER IS NULL) AS ttmoney,TERMINALNUMBER,CONSECNUMBER,BIZDATE,CLKCODE,CLKNAME,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,INVOICENUMBER,(SALESTTLAMT + TAX1 + TAX9) AS SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,TABLENUMBER FROM tempCST011 WHERE NBCHKNUMBER IS NULL ORDER BY substr(CREATEDATETIME||"000",1,17) DESC';
		}
	}
	else{
		$sql='SELECT * FROM salemap WHERE bizdate="'.$bizdate.'"';
		$tempsale=sqlquery($conn,$sql,'sqlite');
		//2021/7/21 與下方的sql功能重複
		/*$sql='SELECT BIZDATE,CONSECNUMBER,TABLENUMBER FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER IS NULL ORDER BY CREATEDATETIME DESC';
		$temptable=sqlquery($conn,$sql,'sqlite');
		//print_r($datas1);
		foreach($temptable as $i){
			$table[$i['BIZDATE']][str_pad($i['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]=$i['TABLENUMBER'];
		}*/
		$sql='SELECT (SELECT COUNT(*) FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER="Y") AS voidcount,(SELECT SUM(SALESTTLAMT + TAX1 + TAX9) FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER="Y") AS voidmoney,TERMINALNUMBER,BIZDATE,CONSECNUMBER,CLKCODE,CLKNAME,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,INVOICENUMBER,(SALESTTLAMT + TAX1 + TAX9) AS SALESTTLAMT,NBCHKNUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME,TABLENUMBER FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND NBCHKNUMBER IS NULL ORDER BY substr(CREATEDATETIME||"000",1,17) DESC';
	}
	$datas=sqlquery($conn,$sql,'sqlite');
	//print_r($datas);
	//$sale=array();
	foreach($tempsale as $i){
		$sale[$i['bizdate']][str_pad($i['consecnumber'],6,"0",STR_PAD_LEFT)]=$i['saleno'];
	}
	foreach($datas as $i){
		$table[$i['BIZDATE']][str_pad($i['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]=$i['TABLENUMBER'];
		//2021/7/21
		if(isset($init['init']['useinv'])&&$init['init']['useinv']=='1'&&strlen($i['INVOICENUMBER'])==10){
			$inv[$i['INVOICENUMBER']]['createdatetime']=$i['CREATEDATETIME'];
			$inv[$i['INVOICENUMBER']]['updatedatetime']=$i['UPDATEDATETIME'];
			$inv[$i['INVOICENUMBER']]['carrier']='';
		}
		else{
		}
	}
	sqlclose($conn,'sqlite');
}
else{
}
//print_r($sale);
//print_r($table);
if(isset($_POST['sale'])&&$_POST['sale']=='sale'&&file_exists('../../../database/sale/Cover.db')){
	$conn=sqlconnect('../../../database/sale','Cover.db','','','','sqlite');
	$sql='SELECT * FROM list WHERE bizdate="'.$bizdate.'" AND state=1';
	$tempcover=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$cover=array();
	if(isset($tempcover[0]['coverbizdate'])){
		foreach($tempcover as $tc){
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['salesttlamt']=$tc['salesttlamt'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['tax1']=$tc['tax1'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['tax2']=$tc['tax2'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['tax3']=$tc['tax3'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['tax4']=$tc['tax4'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['tax9']=$tc['tax9'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta1']=$tc['ta1'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta2']=$tc['ta2'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta3']=$tc['ta3'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta4']=$tc['ta4'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta5']=$tc['ta5'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta6']=$tc['ta6'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta7']=$tc['ta7'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta8']=$tc['ta8'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta9']=$tc['ta9'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['ta10']=$tc['ta10'];
			$cover[$tc['bizdate']][str_pad($tc['consecnumber'],6,"0",STR_PAD_LEFT)]['nontax']=$tc['nontax'];
		}
	}
	else{
	}
}
else{
}

if((!isset($datas)||sizeof($datas)==0)&&(!isset($datas1)||sizeof($datas1)==0)){
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
	//2021/7/21
	if(sizeof($inv)>0&&isset($init['init']['useinv'])&&$init['init']['useinv']=='1'){
		//print_r($inv);
		$records=sizeof($inv);
		$strtime=substr(min(array_column($inv,'createdatetime')),0,6);
		if($strtime%2==0){
		}
		else{
			$strtime=date('Ym',strtotime(substr($strtime,0,4).'-'.substr($strtime,4,2).'-01 +1 month'));
		}
		if(strlen(max(array_column($inv,'updatedatetime')))>6){
			$endtime=substr(max(array_column($inv,'updatedatetime')),0,6);
		}
		else{
			$endtime=date('Ym');
		}
		if($endtime%2==0){
		}
		else{
			$endtime=date('Ym',strtotime(substr($endtime,0,4).'-'.substr($endtime,4,2).'-01 +1 month'));
		}
		//echo $strtime.';'.$endtime;
		$diffmon=getMon($strtime,$endtime);
		for($m=strtotime(substr($strtime,0,4).'-'.substr($strtime,4,2).'-01');$m<=strtotime(substr($endtime,0,4).'-'.substr($endtime,4,2).'-01');$m=strtotime(date('Y-m-d',$m).' +2 month')){
			if(file_exists('../../../database/sale/'.date('Ym',$m).'/invdata_'.date('Ym',$m).'_m1.db')){
				$conn=sqlconnect('../../../database/sale/'.date('Ym',$m),'invdata_'.date('Ym',$m).'_m1.db','','','','sqlite');
				$sql='SELECT * FROM invlist WHERE invnumber IN ("'.implode('","',array_keys($inv)).'")';
				$invdata=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				if(sizeof($invdata)>0){
					for($i=0;$i<sizeof($invdata);$i++){
						/*if($invdata[$i]['buyerid']!='0000000000'){//有輸入統編
							//echo '統編';
							$inv[$invdata[$i]['invnumber']]['carrier']=$invdata[$i]['buyerid'];
						}
						else */if(strlen($invdata[$i]['carrierid1'])>0){//有輸入載具
							//echo '載具';
							$inv[$invdata[$i]['invnumber']]['carrier']=$invdata[$i]['carrierid1'];
						}
						else{
							//echo '一般';
						}
					}
					$records=intval($records)-intval(sizeof($invdata));
				}
				else{
				}
			}
			else{
			}
			if($records==0){
				break;
			}
			else{
			}
		}
	}
	else{
	}
	echo "<div style='display:none;'>
			<input type='hidden' id='bizdate' value='".$bizdate."'>
		</div>";
	if(isset($datas1)&&isset($datas1[0]['BIZDATE'])){
		foreach($datas1 as $d){
			echo "<div class='listitems' style='width:max-content;padding:10px 0;overflow:hidden;border-bottom:1px solid #898989;";
			if(strlen($d['NBCHKNUMBER'])>0&&($d['NBCHKNUMBER']=='Y'||$d['NBCHKNUMBER']!=null)){//2020/10/6/週二
				echo 'color:#ff0000;';
			}
			else{
			}
			echo "'><div style='width:97px;float:left;min-height:1px;'>".$d['BIZDATE']."</div>";
			echo "<div style='width:90px;float:left;min-height:1px;'>";
			if(preg_match('/-/',$d['REMARKS'])){
				$temp=preg_split('/-/',$d['REMARKS']);
				if($temp[0]==1){//預約內用
					echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo '<span style="font-size:25px;font-weight:bold;">';
						//echo $d['TABLENUMBER'];//2020/3/23
						if(preg_match('/,/',$d['TABLENUMBER'])){//併桌
							$splittable=preg_split('/,/',$d['TABLENUMBER']);
							for($sti=0;$sti<sizeof($splittable);$sti++){
								if($sti!=0){
									echo ',';
								}
								else{
								}
								if(preg_match('/-/',$splittable[$sti])){//拆桌
									$inittable=preg_split('/-/',$splittable[$sti]);//2020/3/23 因為桌號改為對應方式，需要額外判斷
									if(isset($tb['Tname'][$inittable[0]])){
										echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
									}
									else{
										echo $splittable[$sti];
									}
								}
								else{
									if(isset($tb['Tname'][$splittable[$sti]])){//2020/3/23 因為桌號改為對應方式，需要額外判斷
										echo $tb['Tname'][$splittable[$sti]];
									}
									else{
										echo $splittable[$sti];
									}
								}
							}
						}
						else{
							if(preg_match('/-/',$d['TABLENUMBER'])){//拆桌
								$inittable=preg_split('/-/',$d['TABLENUMBER']);//2020/3/23 因為桌號改為對應方式，需要額外判斷
								if(isset($tb['Tname'][$inittable[0]])){
									echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
								}
								else{
									echo $d['TABLENUMBER'];
								}
							}
							else{
								if(isset($tb['Tname'][$d['TABLENUMBER']])){//2020/3/23 因為桌號改為對應方式，需要額外判斷
									echo $tb['Tname'][$d['TABLENUMBER']];
								}
								else{
									echo $d['TABLENUMBER'];
								}
							}
						}
						echo '</span>';
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
				else if($temp[0]==2){//預約外帶
					echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				else if($temp[0]==3){//預約外送
					echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				else if($temp[0]==4){//預約自取
					echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				else if($temp[0]=='editsale'){//修改帳單
					echo $buttonname['name']['editlist'];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo $d['TABLENUMBER'];
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
				else{//暫廢
					echo $buttonname['name']['voidtemp'];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo $d['TABLENUMBER'];
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
			}
			else{
				$temp='';
				if($d['REMARKS']==1){//內用
					echo $buttonname['name']['listtype'.$d['REMARKS']];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo '<span style="font-size:25px;font-weight:bold;">';
						//echo $d['TABLENUMBER'];//2020/3/23
						if(preg_match('/,/',$d['TABLENUMBER'])){//併桌
							$splittable=preg_split('/,/',$d['TABLENUMBER']);
							for($sti=0;$sti<sizeof($splittable);$sti++){
								if($sti!=0){
									echo ',';
								}
								else{
								}
								if(preg_match('/-/',$splittable[$sti])){//拆桌
									$inittable=preg_split('/-/',$splittable[$sti]);//2020/3/23 因為桌號改為對應方式，需要額外判斷
									if(isset($tb['Tname'][$inittable[0]])){
										echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
									}
									else{
										echo $splittable[$sti];
									}
								}
								else{
									if(isset($tb['Tname'][$splittable[$sti]])){//2020/3/23 因為桌號改為對應方式，需要額外判斷
										echo $tb['Tname'][$splittable[$sti]];
									}
									else{
										echo $splittable[$sti];
									}
								}
							}
						}
						else{
							if(preg_match('/-/',$d['TABLENUMBER'])){//拆桌
								$inittable=preg_split('/-/',$d['TABLENUMBER']);//2020/3/23 因為桌號改為對應方式，需要額外判斷
								if(isset($tb['Tname'][$inittable[0]])){
									echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
								}
								else{
									echo $d['TABLENUMBER'];
								}
							}
							else{
								if(isset($tb['Tname'][$d['TABLENUMBER']])){//2020/3/23 因為桌號改為對應方式，需要額外判斷
									echo $tb['Tname'][$d['TABLENUMBER']];
								}
								else{
									echo $d['TABLENUMBER'];
								}
							}
						}
						echo '</span>';
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
				else if($d['REMARKS']==2){//外帶
					echo $buttonname['name']['listtype'.$d['REMARKS']];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
					if(isset($_POST['temp'])&&$_POST['temp']=='temp'&&$d['TABLENUMBER']!=''){
						echo '<br>(<em>'.$buttonname['name']['listtype1'];
						if(strlen($d['TABLENUMBER'])==0){
							if(isset($sale[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)])){
								echo $sale[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)];
							}
							else{
							}
						}
						else{
							echo '<span style="font-size:25px;font-weight:bold;">'.$table[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)].'</span>';
							echo '<br>';
							if(isset($sale[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)])){
								echo $sale[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)];
							}
							else{
							}
						}
						echo '</em>)';
					}
					else{
					}
				}
				else if($d['REMARKS']==3){//外送
					echo $buttonname['name']['listtype'.$d['REMARKS']];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				else if($d['REMARKS']==4){//自取
					echo $buttonname['name']['listtype'.$d['REMARKS']];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				
				else if($d['REMARKS']=='editsale'){//修改帳單
					echo $buttonname['name']['editlist'];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo $d['TABLENUMBER'];
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
				else{//暫廢
					echo $buttonname['name']['voidtemp'];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo $d['TABLENUMBER'];
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
			}
			echo "<input type='hidden' name='consecnumber' value='".$d['CONSECNUMBER']."'><input type='hidden' name='machinename' value='".$d['TERMINALNUMBER']."'><input type='hidden' name='memno' value='".$d['CUSTCODE']."'><input type='hidden' name='listtype' value='".$d['REMARKS']."'></div>";
			echo "<div style='width:100px;float:left;'>".$d['CONSECNUMBER']."</div>";
			if($init['init']['useoinv']=='0'&&$init['init']['useinv']=='0'){
				echo "<div style='width:121px;float:left;min-height:1px;display:none;'>".$d['INVOICENUMBER']."</div>";
			}
			else{
				echo "<div style='width:121px;float:left;min-height:1px;'>".$d['INVOICENUMBER']."</div>";
			}
			if($init['init']['useinv']=='1'){
				echo "<div style='width:121px;float:left;min-height:1px;'>";
				echo ((isset($inv[$d['INVOICENUMBER']]['carrier']))?($inv[$d['INVOICENUMBER']]['carrier']):(''));
				echo "</div>";
			}
			else{
				echo "<div style='width:121px;float:left;min-height:1px;display:none'></div>";
			}
			echo "<div style='width:121px;float:left;min-height:1px;'>";
			if(isset($cover[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]['salesttlamt'])){
				echo floatval($cover[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]['salesttlamt'])+floatval($cover[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]['tax1'])+floatval($cover[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]['tax9']);
			}
			else{
				echo $d['SALESTTLAMT'];
			}
			echo "</div><div style='width:121px;float:left;min-height:1px;'>".$d['CUSTNAME']."</div><div style='width:121px;float:left;min-height:1px;'>".$d['CLKNAME']."</div><div style='width:121px;float:left;min-height:1px;'>".$d['CUSTGPNAME']."<input type='hidden' name='mancode' value='".$d['CUSTGPCODE']."'><input type='hidden' name='manname' value='".$d['CUSTGPNAME']."'></div><div style='width:152px;float:left;min-height:1px;text-align:center;'>";
			if(isset($temp[1])&&preg_match('/;/',$temp[1])){
				$templisttype=preg_split('/;/',$temp[1]);
				echo $interfacename['name']['reservationdate'].substr($templisttype[0],0,8).'<br>';//預約日
			}
			else{
			}
			echo $d['CREATEDATETIME']."</div>";
			if(strlen($d['NBCHKNUMBER'])>0&&($d['NBCHKNUMBER']=='Y'||$d['NBCHKNUMBER']!=null)){//2020/10/6/週二
				echo "<div style='width:38px;min-height:1px;float:left;text-align:center;'>".$d['NBCHKNUMBER']."</div>";
			}
			else if($d['NBCHKNUMBER']==null||$d['NBCHKNUMBER']==''){
				echo "<div style='width:38px;min-height:1px;float:left;text-align:center;'></div>";
			}
			else{
				echo "<div style='width:38px;min-height:1px;float:left;text-align:center;'>Y</div><div style='width:100%;float:left;text-align:right;'>".$d['NBCHKNUMBER']."</div>";
			}
			echo "<div><input type='hidden' name='memno' value='".$d['CUSTCODE']."'></div>";
			echo "</div>";
		}
	}
	else{
	}
	if(isset($datas)&&isset($datas[0]['BIZDATE'])){
		foreach($datas as $d){
			echo "<div class='listitems' style='width:max-content;padding:10px 0;overflow:hidden;border-bottom:1px solid #898989;";
			if(strlen($d['NBCHKNUMBER'])>0&&($d['NBCHKNUMBER']=='Y'||$d['NBCHKNUMBER']!=null)){//2020/10/6/週二
				echo 'color:#ff0000;';
			}
			else{
			}
			echo "'><div style='width:97px;float:left;min-height:1px;'>".$d['BIZDATE']."</div>";
			/*if($init['init']['useoinv']=='0'&&$init['init']['useinv']=='0'){
				echo "<div style='width:calc(80% / 6 - 6px);float:left;min-height:1px;'>";
			}
			else{*/
				echo "<div style='width:90px;float:left;min-height:1px;'>";
			//}
			if(preg_match('/-/',$d['REMARKS'])){
				$temp=preg_split('/-/',$d['REMARKS']);
				if($temp[0]==1){//預約內用
					echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo '<span style="font-size:25px;font-weight:bold;">';
						//echo $d['TABLENUMBER'];//2020/3/23
						if(preg_match('/,/',$d['TABLENUMBER'])){//併桌
							$splittable=preg_split('/,/',$d['TABLENUMBER']);
							for($sti=0;$sti<sizeof($splittable);$sti++){
								if($sti!=0){
									echo ',';
								}
								else{
								}
								if(preg_match('/-/',$splittable[$sti])){//拆桌
									$inittable=preg_split('/-/',$splittable[$sti]);//2020/3/23 因為桌號改為對應方式，需要額外判斷
									if(isset($tb['Tname'][$inittable[0]])){
										echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
									}
									else{
										echo $splittable[$sti];
									}
								}
								else{
									if(isset($tb['Tname'][$splittable[$sti]])){//2020/3/23 因為桌號改為對應方式，需要額外判斷
										echo $tb['Tname'][$splittable[$sti]];
									}
									else{
										echo $splittable[$sti];
									}
								}
							}
						}
						else{
							if(preg_match('/-/',$d['TABLENUMBER'])){//拆桌
								$inittable=preg_split('/-/',$d['TABLENUMBER']);//2020/3/23 因為桌號改為對應方式，需要額外判斷
								if(isset($tb['Tname'][$inittable[0]])){
									echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
								}
								else{
									echo $d['TABLENUMBER'];
								}
							}
							else{
								if(isset($tb['Tname'][$d['TABLENUMBER']])){//2020/3/23 因為桌號改為對應方式，需要額外判斷
									echo $tb['Tname'][$d['TABLENUMBER']];
								}
								else{
									echo $d['TABLENUMBER'];
								}
							}
						}
						echo '</span>';
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
				else if($temp[0]==2){//預約外帶
					echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				else if($temp[0]==3){//預約外送
					echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				else if($temp[0]==4){//預約自取
					echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				else if($temp[0]=='editsale'){//修改帳單
					echo $buttonname['name']['editlist'];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo $d['TABLENUMBER'];
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
				else{//暫廢
					echo $buttonname['name']['voidtemp'];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo $d['TABLENUMBER'];
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
			}
			else{
				$temp='';
				if($d['REMARKS']==1){//內用
					echo $buttonname['name']['listtype'.$d['REMARKS']];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						//if($init['init']['controltable']=='1'){
							echo '<span style="font-size:25px;font-weight:bold;">';
							//echo $d['TABLENUMBER'];//2020/3/23
							if(preg_match('/,/',$d['TABLENUMBER'])){//併桌
								$splittable=preg_split('/,/',$d['TABLENUMBER']);
								for($sti=0;$sti<sizeof($splittable);$sti++){
									if($sti!=0){
										echo ',';
									}
									else{
									}
									if(preg_match('/-/',$splittable[$sti])){//拆桌
										$inittable=preg_split('/-/',$splittable[$sti]);//2020/3/23 因為桌號改為對應方式，需要額外判斷
										if(isset($tb['Tname'][$inittable[0]])){
											echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
										}
										else{
											echo $splittable[$sti];
										}
									}
									else{
										if(isset($tb['Tname'][$splittable[$sti]])){//2020/3/23 因為桌號改為對應方式，需要額外判斷
											echo $tb['Tname'][$splittable[$sti]];
										}
										else{
											echo $splittable[$sti];
										}
									}
								}
							}
							else{
								if(preg_match('/-/',$d['TABLENUMBER'])){//拆桌
									$inittable=preg_split('/-/',$d['TABLENUMBER']);//2020/3/23 因為桌號改為對應方式，需要額外判斷
									if(isset($tb['Tname'][$inittable[0]])){
										echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
									}
									else{
										echo $d['TABLENUMBER'];
									}
								}
								else{
									if(isset($tb['Tname'][$d['TABLENUMBER']])){//2020/3/23 因為桌號改為對應方式，需要額外判斷
										echo $tb['Tname'][$d['TABLENUMBER']];
									}
									else{
										echo $d['TABLENUMBER'];
									}
								}
							}
							echo '</span>';
							if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
								echo '<br>';
								echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
							}
							else{
							}
						/*}
						else{
							echo intval(str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT));
						}*/
					}
				}
				else if($d['REMARKS']==2){//外帶
					echo $buttonname['name']['listtype'.$d['REMARKS']];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
					if(isset($_POST['temp'])&&$_POST['temp']=='temp'&&$d['TABLENUMBER']!=''){
						echo '<br>(<em>'.$buttonname['name']['listtype1'];
						if(strlen($d['TABLENUMBER'])==0){
							if(isset($sale[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)])){
								echo $sale[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)];
							}
							else{
							}
						}
						else{
							//if($init['init']['controltable']=='1'){
								echo '<span style="font-size:25px;font-weight:bold;">'.$table[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)].'</span>';
								if(isset($sale[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)])){
									echo '<br>';
									echo $sale[$d['BIZDATE']][str_pad($d['TABLENUMBER'],6,"0",STR_PAD_LEFT)];
								}
								else{
								}
							/*}
							else{
								echo intval(str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT));
							}*/
						}
						echo '</em>)';
					}
					else{
					}
				}
				else if($d['REMARKS']==3){//外送
					echo $buttonname['name']['listtype'.$d['REMARKS']];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				else if($d['REMARKS']==4){//自取
					echo $buttonname['name']['listtype'.$d['REMARKS']];
					if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
						echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
					}
					else{
					}
				}
				
				else if($d['REMARKS']=='editsale'){//修改帳單
					echo $buttonname['name']['editlist'];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo $d['TABLENUMBER'];
						echo '<br>';
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
				else{//暫廢
					echo $buttonname['name']['voidtemp'];
					if(strlen($d['TABLENUMBER'])==0){
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
					else{
						echo $d['TABLENUMBER'];
						if(isset($sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)])){
							echo $sale[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)];
						}
						else{
						}
					}
				}
			}
			echo "<input type='hidden' name='consecnumber' value='".$d['CONSECNUMBER']."'><input type='hidden' name='machinename' value='".$d['TERMINALNUMBER']."'><input type='hidden' name='memno' value='".$d['CUSTCODE']."'><input type='hidden' name='listtype' value='".$d['REMARKS']."'></div>";
			echo "<div style='width:100px;float:left;min-height:1px;'>".$d['CONSECNUMBER']."</div>";
			if($init['init']['useoinv']=='0'&&$init['init']['useinv']=='0'){
				echo "<div style='width:121px;float:left;min-height:1px;display:none;'>".$d['INVOICENUMBER']."</div>";
			}
			else{
				echo "<div style='width:121px;float:left;min-height:1px;'>".$d['INVOICENUMBER']."</div>";
			}
			if($init['init']['useinv']=='1'){
				echo "<div style='width:121px;float:left;min-height:1px;'>";
				echo ((isset($inv[$d['INVOICENUMBER']]['carrier']))?($inv[$d['INVOICENUMBER']]['carrier']):(''));
				echo "</div>";
			}
			else{
				echo "<div style='width:121px;float:left;min-height:1px;display:none;'></div>";
			}
			echo "<div style='width:121px;float:left;min-height:1px;'>";
			if(isset($cover[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]['salesttlamt'])){
				echo floatval($cover[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]['salesttlamt'])+floatval($cover[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]['tax1'])+floatval($cover[$d['BIZDATE']][str_pad($d['CONSECNUMBER'],6,"0",STR_PAD_LEFT)]['tax9']);
			}
			else{
				echo $d['SALESTTLAMT'];
			}
			echo "</div><div style='width:121px;float:left;min-height:1px;'>".$d['CUSTNAME']."</div><div style='width:121px;float:left;min-height:1px;'>".$d['CLKNAME']."</div><div style='width:121px;float:left;min-height:1px;'>".$d['CUSTGPNAME']."<input type='hidden' name='mancode' value='".$d['CUSTGPCODE']."'><input type='hidden' name='manname' value='".$d['CUSTGPNAME']."'></div><div style='width:152px;float:left;min-height:1px;text-align:center;'>";
			if(isset($temp[1])&&preg_match('/;/',$temp[1])){
				$templisttype=preg_split('/;/',$temp[1]);
				echo $interfacename['name']['reservationdate'].substr($templisttype[0],0,8).'<br>';//預約日
			}
			else{
			}
			echo $d['CREATEDATETIME']."</div>";
			if(strlen($d['NBCHKNUMBER'])>0&&($d['NBCHKNUMBER']=='Y'||$d['NBCHKNUMBER']!=null)){//2020/10/6/週二
				echo "<div style='width:38px;min-height:1px;float:left;text-align:center;'>".$d['NBCHKNUMBER']."</div>";
			}
			else if($d['NBCHKNUMBER']==null||$d['NBCHKNUMBER']==''){
				echo "<div style='width:38px;min-height:1px;float:left;text-align:center;'></div>";
			}
			else{
				echo "<div style='width:38px;min-height:1px;float:left;text-align:center;'>Y</div><div style='width:100%;float:left;text-align:right;'>".$d['NBCHKNUMBER']."</div>";
			}
			echo "<div><input type='hidden' name='memno' value='".$d['CUSTCODE']."'></div>";
			echo "</div>";
		}
	}
	else{
	}
	if(isset($datas[0]['ttcount'])){
		echo '<input type="hidden" name="ttcount" value="'.$datas[0]['ttcount'].'">
			<input type="hidden" name="ttmoney" value="';if($datas[0]['ttmoney']==null)echo '0';else echo $datas[0]['ttmoney'];echo '">';
	}
	else{
	}
	if(isset($datas[0]['voidcount'])){
		echo '<input type="hidden" name="voidcount" value="'.$datas[0]['voidcount'].'">
			<input type="hidden" name="voidmoney" value="';if($datas[0]['voidmoney']==null)echo '0';else echo $datas[0]['voidmoney'];echo '">';
	}
	else{
	}
}
?>