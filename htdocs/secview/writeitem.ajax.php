<?php
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';
$init=parse_ini_file('../database/initsetting.ini',true);
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
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
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}
if($_POST['itemtype']!='listdis'){
	$conn=sqlconnect("../database","menu.db","","","","sqlite");
	$sql='SELECT * FROM itemsdata WHERE inumber="'.$_POST['no'].'" AND fronttype="'.$_POST['itemtype'].'"';
	$data=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$itemname=parse_ini_file('../database/'.$_POST['company'].'-menu.ini',true);
}
else{
}

if(file_exists("../database/sale/temp".$_POST['machinename'].".db")){
}
else{
	if(file_exists("../database/sale/EMtemp.DB")){
	}
	else{
		include_once './create.emptyDB.php';
		create('EMtemp');
	}
	copy("../database/sale/EMtemp.db","../database/sale/temp".$_POST['machinename'].".db");
}
$conn=sqlconnect('../database/sale','temp'.$_POST['machinename'].'.db','','','','sqlite');
$target=intval($_POST['linenumber']);
$sql='SELECT COUNT(*) AS num FROM list WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$itemnum=sqlquery($conn,$sql,'sqlite');
if(intval($itemnum[0]['num'])==0){
	if($_POST['itemtype']!='listdis'){
		$sql='INSERT INTO list SELECT "'.$_POST['machinename'].'","'.$timeini['time']['bizdate'].'","'.$_POST['consecnumber'].'","001","'.$_POST['usercode'].'","'.$_POST['username'].'","1","1","01",substr(("0000000000000000"||"'.$data[0]['inumber'].'"),-16,16),"'.$itemname[$data[0]['inumber']]['name1'].'",substr(("000000"||"'.$data[0]['fronttype'].'"),-6,6),"",substr(("000000"||"'.$data[0]['fronttype'].'"),-6,6),"",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,"'.$_POST['mname'].'",0,1,'.$_POST['money1'].','.$_POST['money1'].',NULL,NULL,NULL,NULL,NULL,"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.date('YmdHis').'";INSERT INTO list SELECT "'.$_POST['machinename'].'","'.$timeini['time']['bizdate'].'","'.$_POST['consecnumber'].'","002","'.$_POST['usercode'].'","'.$_POST['username'].'","1","3","02","item","單品優惠","","","","",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,"",0,0,0,0,NULL,NULL,NULL,NULL,NULL,"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.date('YmdHis').'";';
	}
	else{
	}
}
else{
	if($_POST['itemtype']!='listdis'){
		$sql='SELECT * FROM list WHERE LINENUMBER=substr(("000"||"'.$target.'"),-3,3) AND TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
		$itemed=sqlquery($conn,$sql,'sqlite');
		if(sizeof($itemed)==0){//新點選之產品
			$sql='INSERT INTO list SELECT "'.$_POST['machinename'].'","'.$timeini['time']['bizdate'].'","'.$_POST['consecnumber'].'",substr("000"||(CASE(SELECT LINENUMBER FROM list WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER DESC LIMIT 1) WHEN NULL THEN "1" ELSE CAST((SELECT LINENUMBER FROM list WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER DESC LIMIT 1) AS INT)+1 END),-3,3),"'.$_POST['usercode'].'","'.$_POST['username'].'","1","1","01",substr(("0000000000000000"||"'.$data[0]['inumber'].'"),-16,16),"'.$itemname[$data[0]['inumber']]['name1'].'",substr(("000000"||"'.$data[0]['fronttype'].'"),-6,6),"",substr(("000000"||"'.$data[0]['fronttype'].'"),-6,6),"",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,"'.$_POST['mname'].'",0,1,'.$_POST['money1'].','.$_POST['money1'].',NULL,NULL,NULL,NULL,NULL,"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.date('YmdHis').'";INSERT INTO list SELECT "'.$_POST['machinename'].'","'.$timeini['time']['bizdate'].'","'.$_POST['consecnumber'].'",substr("000"||(CASE(SELECT LINENUMBER FROM list WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER DESC LIMIT 1) WHEN NULL THEN "1" ELSE CAST((SELECT LINENUMBER FROM list WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER DESC LIMIT 1) AS INT)+1 END),-3,3),"'.$_POST['usercode'].'","'.$_POST['username'].'","1","3","02","item","單品優惠","","","","",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,"",0,0,0,0,NULL,NULL,NULL,NULL,NULL,"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.date('YmdHis').'";';
		}
		else{//修改舊產品
			if(strlen($_POST['taste'])==0){
				$taste='SELECTIVEITEM1=NULL,';
				/*for($i=1;$i<=10;$i++){
					$taste=$taste.'SELECTIVEITEM'.$i.'=NULL,';
				}*/
				$tastemoney=0;
			}
			else{
				$temp=preg_split('/,/',$_POST['taste']);
				$tastenumber=preg_split('/,/',$_POST['tastenumber']);
				$taste='';
				for($i=0;$i<sizeof($temp);$i++){
					if($i!=0){
						$taste.='||","||';
					}
					else{
						$taste='SELECTIVEITEM1=';
					}
					$taste.='(substr(("00000"||"'.$temp[$i].'"),-5,5)||"'.$tastenumber[$i].'")';
				}
				$temp=preg_split('/,/',$_POST['tastemoney']);
				$tastemoney=0;
				for($i=0;$i<sizeof($temp);$i++){
					$tastemoney=floatval($tastemoney)+floatval($temp[$i])*intval($tastenumber[$i]);
				}
				$taste.=',';
			}
			$dis=floatval($_POST['subtotal'])-((floatval($_POST['money1'])+floatval($tastemoney))*floatval($_POST['number']));
			$sql='UPDATE list SET '.$taste.'UNITPRICELINK="'.$_POST['mname'].'",QTY='.$_POST['number'].',UNITPRICE='.$_POST['money1'].',AMT='.((floatval($_POST['money1'])+floatval($tastemoney))*floatval($_POST['number'])).' WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER=substr(("000"||"'.$target.'"),-3,3);UPDATE list SET AMT='.$dis.' WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER=substr(("000"||"'.(intval($target)+1).'"),-3,3);';
			//echo $sql;
		}
	}
	else{
		$sql='SELECT * FROM list WHERE LINENUMBER="'.$_POST['linenumber'].'" AND TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
		$itemed=sqlquery($conn,$sql,'sqlite');
		if(sizeof($itemed)==0&&$_POST['money1']!=0){//首次帳單優惠
			$sql='INSERT INTO list SELECT "'.$_POST['machinename'].'","'.$timeini['time']['bizdate'].'","'.$_POST['consecnumber'].'","'.$_POST['linenumber'].'","'.$_POST['usercode'].'","'.$_POST['username'].'","1","3","02","list","帳單優惠","","","","",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,0,0,'.$_POST['money1'].',NULL,NULL,NULL,NULL,NULL,"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.date('YmdHis').'";';
		}
		else{//修改帳單優惠
			if($_POST['money1']!=0){
				$sql='UPDATE list SET AMT='.$_POST['money1'].' WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$_POST['linenumber'].'";';
			}
			else{
				$sql='DELETE FROM list WHERE TERMINALNUMBER="'.$_POST['machinename'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$_POST['linenumber'].'";';
			}
			//echo $sql;
		}
	}
}
sqlnoresponse($conn,$sql,'sqliteexec');
sqlclose($conn,'sqlite');
?>