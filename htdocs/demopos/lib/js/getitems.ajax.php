<?php
include '../../../tool/dbTool.inc.php';
$conn=sqlconnect("../../../database","menu.db","","","","sqlite");
if(isset($_POST['itemtype'])&&$_POST['itemtype']=='allitems'){
	$sql='SELECT fronttype as itemdep,inumber as number FROM itemsdata WHERE (state IS NULL OR state=1) ORDER BY CAST(typeseq AS INT),CAST(frontsq AS INT),replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(inumber))), "\'", ""), "0", "0")||inumber';
}
else if(isset($_POST['group'])){
	$item=preg_replace('/,/','","',$_POST['itemtype']);
	$sql='SELECT fronttype as itemdep,inumber as number FROM itemsdata WHERE inumber IN ("'.$item.'") AND (state IS NULL OR state="1") ORDER BY CAST(frontsq AS INT),inumber';
	//echo $sql;
}
else if(isset($_POST['itemtype'])){
	$sql='SELECT fronttype as itemdep,inumber as number FROM itemsdata WHERE fronttype="'.$_POST['itemtype'].'" AND (state IS NULL OR state="1") ORDER BY CAST(frontsq AS INT)';
}
else{//2020/5/27 若itemtype未傳入，則故意查詢不存在的類別代號，取得一個空陣列
	$sql='SELECT fronttype as itemdep,inumber as number FROM itemsdata WHERE fronttype="-1" AND (state IS NULL OR state="1") ORDER BY CAST(frontsq AS INT)';
}
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(!isset($_POST['company'])||strlen($_POST['company'])==0){
	$items=parse_ini_file('../../../database/items.ini',true);
}
else{
	$items=parse_ini_file('../../../database/'.$_POST['company'].'-menu.ini',true);
}
if(file_exists('../../../database/stock.ini')){
	$stock=parse_ini_file('../../../database/stock.ini',true);
}
else{
	$stock='-1';
}
//print_r($stock);
$itemlist=array();
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
else{
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$changestate='';
//echo $_POST['next'];
if(isset($_POST['next'])){//換頁按鈕
	if($_POST['next']!=''){//可能尚有產品未顯示
		if(isset($data[0]['number'])){
			$tempdata=array_column($data,'number');
			if(in_array($_POST['next'],$tempdata)){//取得目前顯示最後產品的位置
				if(isset($tempdata[array_search($_POST['next'],$tempdata)+1])){//檢查後面是否還有產品
					$i=array_search($_POST['next'],$tempdata)+1;
				}
				else{
					$i=0;
				}
			}
			else{
				$i=0;
			}
		}
		else{
			$i=-2;//2020/5/27 如果取得的陣列為空，則故意在換頁按鈕的起始號，顯示為-1，以便除錯
		}
	}
	else{//顯示迴圈到尾，從頭開始顯示
		$i=0;
	}
}
else{
	$i=0;
}
$page=($i+1).'/'.sizeof($data);
//print_r($data);
for(;$i<sizeof($data);$i++){
	if(isset($items[$data[$i]['number']]['state'])&&$items[$data[$i]['number']]['state']=='1'&&(!isset($items[$data[$i]['number']]['posvisible'])||$items[$data[$i]['number']]['posvisible']=='1')&&intval($items[$data[$i]['number']]['counter'])<=0){
		$temp=array();
		if(isset($_POST['group'])){
			$t=preg_split('/,/',$data[$i]['number']);
			foreach($t as $q){
				array_push($temp,$data[$i]['itemdep'],$q,$items[$q]['name1'],$items[$q]['name2']);
				if(isset($items[$q]['bgcolor'])){
					array_push($temp,$items[$q]['bgcolor']);
				}
				else{
					array_push($temp,'null');
				}
				if(isset($items[$q]['size1'])){
					array_push($temp,$items[$q]['size1'],$items[$q]['size2'],$items[$q]['color1'],$items[$q]['color2'],$items[$q]['bold1'],$items[$q]['bold2']);
				}
				else{
					array_push($temp,'14','14','#000000','#898989','0','0');
				}
				array_push($itemlist,$temp);
			}
		}
		else{
			array_push($temp,$data[$i]['itemdep'],$data[$i]['number'],$items[$data[$i]['number']]['name1'],$items[$data[$i]['number']]['name2']);
			if(isset($items[$data[$i]['number']]['bgcolor'])){
				array_push($temp,$items[$data[$i]['number']]['bgcolor']);
			}
			else{
				array_push($temp,'null');
			}
			if(isset($items[$data[$i]['number']]['size1'])){
				array_push($temp,$items[$data[$i]['number']]['size1'],$items[$data[$i]['number']]['size2'],$items[$data[$i]['number']]['color1'],$items[$data[$i]['number']]['color2'],$items[$data[$i]['number']]['bold1'],$items[$data[$i]['number']]['bold2']);
			}
			else{
				array_push($temp,'14','14','#000000','#898989','0','0');
			}
			array_push($itemlist,$temp);
		}
	}
	else if(isset($items[$data[$i]['number']]['state'])&&$items[$data[$i]['number']]['state']=='1'&&(!isset($items[$data[$i]['number']]['posvisible'])||$items[$data[$i]['number']]['posvisible']=='1')&&intval($items[$data[$i]['number']]['counter'])>0&&isset($stock[$data[$i]['number']]['stock'])&&intval($stock[$data[$i]['number']]['stock'])>0){
		if(intval($items[$data[$i]['number']]['counter'])==1||intval($items[$data[$i]['number']]['counter'])==2){//限量以商品數為主
			if(file_exists("../../../database/sale/SALES_".substr($timeini['time']['bizdate'],0,6).".db")){
				$conn=sqlconnect("../../../database/sale","SALES_".substr($timeini['time']['bizdate'],0,6).".db","","","","sqlite");
				$sql='SELECT SUM(CST012.QTY) AS QTY FROM CST012 JOIN CST011 ON NBCHKNUMBER IS NULL AND CST011.BIZDATE=CST012.BIZDATE AND CST011.CONSECNUMBER=CST012.CONSECNUMBER WHERE CAST(ITEMCODE AS INT)='.$data[$i]['number'].' AND CST012.BIZDATE="'.$timeini['time']['bizdate'].'"';
				//echo $sql;
				$num1=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT SUM(tempCST012.QTY) AS QTY FROM tempCST012 JOIN tempCST011 ON NBCHKNUMBER IS NULL AND tempCST011.BIZDATE=tempCST012.BIZDATE AND tempCST011.CONSECNUMBER=tempCST012.CONSECNUMBER WHERE CAST(ITEMCODE AS INT)='.$data[$i]['number'].' AND tempCST012.BIZDATE="'.$timeini['time']['bizdate'].'"';
				//echo $sql;
				$num2=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
			}
			else{
				$num1[0]['QTY']=0;
				$num2[0]['QTY']=0;
			}
			if((floatval($num1[0]['QTY'])+floatval($num2[0]['QTY']))<floatval($stock[$data[$i]['number']]['stock'])){
				$temp=array();
				if(isset($_POST['group'])){
					$t=preg_split('/,/',$data[$i]['number']);
					foreach($t as $q){
						array_push($temp,$data[$i]['itemdep'],$q,$items[$q]['name1'],$items[$q]['name2']);
						if(isset($items[$q]['bgcolor'])){
							array_push($temp,$items[$q]['bgcolor']);
						}
						else{
							array_push($temp,'null');
						}
						if(isset($items[$q]['size1'])){
							array_push($temp,$items[$q]['size1'],$items[$q]['size2'],$items[$q]['color1'],$items[$q]['color2'],$items[$q]['bold1'],$items[$q]['bold2']);
						}
						else{
							array_push($temp,'14','14','#000000','#898989','0','0');
						}
						array_push($itemlist,$temp);
					}
				}
				else{
					array_push($temp,$data[$i]['itemdep'],$data[$i]['number'],$items[$data[$i]['number']]['name1'],$items[$data[$i]['number']]['name2']);
					if(isset($items[$data[$i]['number']]['bgcolor'])){
						array_push($temp,$items[$data[$i]['number']]['bgcolor']);
					}
					else{
						array_push($temp,'null');
					}
					if(isset($items[$data[$i]['number']]['size1'])){
						array_push($temp,$items[$data[$i]['number']]['size1'],$items[$data[$i]['number']]['size2'],$items[$data[$i]['number']]['color1'],$items[$data[$i]['number']]['color2'],$items[$data[$i]['number']]['bold1'],$items[$data[$i]['number']]['bold2']);
					}
					else{
						array_push($temp,'14','14','#000000','#898989','0','0');
					}
					array_push($itemlist,$temp);
				}
			}
			else{
			}
		}
		else if(intval($items[$data[$i]['number']]['counter'])==3){//限量以帳單數為主
			$conn=sqlconnect("../../../database/sale","SALES_".substr($timeini['time']['bizdate'],0,6).".db","","","","sqlite");
			$sql='SELECT DISTINCT CST012.BIZDATE,CST012.CONSECNUMBER FROM CST012 WHERE CST012.BIZDATE="'.$timeini['time']['bizdate'].'" AND CAST(ITEMCODE AS INT)='.$data[$i]['number'];
			//echo $sql;
			$test=sqlquery($conn,$sql,'sqlite');
			if(isset($test)&&sizeof($test)>0&&isset($test[0]['BIZDATE'])){
				$sql='SELECT COUNT(*) AS QTY FROM CST011 JOIN (SELECT DISTINCT CST012.BIZDATE,CST012.CONSECNUMBER FROM CST012 WHERE CST012.BIZDATE="'.$timeini['time']['bizdate'].'" AND CAST(ITEMCODE AS INT)='.$data[$i]['number'].') AS CST012 ON CST011.BIZDATE=CST012.BIZDATE AND CST011.CONSECNUMBER=CST012.CONSECNUMBER WHERE NBCHKNUMBER IS NULL AND CST011.BIZDATE="'.$timeini['time']['bizdate'].'"';
				//echo $sql;
				$num1=sqlquery($conn,$sql,'sqlite');
			}
			else{
				$num1[0]['QTY']=0;
			}
			$sql='SELECT DISTINCT tempCST012.BIZDATE,tempCST012.CONSECNUMBER FROM tempCST012 WHERE tempCST012.BIZDATE="'.$timeini['time']['bizdate'].'" AND CAST(ITEMCODE AS INT)='.$data[$i]['number'];
			$test=sqlquery($conn,$sql,'sqlite');
			if(isset($test)&&sizeof($test)>0&&isset($test[0]['BIZDATE'])){
				$sql='SELECT COUNT(*) AS QTY FROM tempCST011 JOIN (SELECT DISTINCT tempCST012.BIZDATE,tempCST012.CONSECNUMBER FROM tempCST012 WHERE tempCST012.BIZDATE="'.$timeini['time']['bizdate'].'" AND CAST(ITEMCODE AS INT)='.$data[$i]['number'].') AS tempCST012 ON tempCST011.BIZDATE=tempCST012.BIZDATE AND tempCST011.CONSECNUMBER=tempCST012.CONSECNUMBER WHERE NBCHKNUMBER IS NULL AND tempCST011.BIZDATE="'.$timeini['time']['bizdate'].'"';
				//echo $sql;
				$num2=sqlquery($conn,$sql,'sqlite');
			}
			else{
				$num2[0]['QTY']=0;
			}
			sqlclose($conn,'sqlite');
			if((floatval($num1[0]['QTY'])+floatval($num2[0]['QTY']))<floatval($stock[$data[$i]['number']]['stock'])){
				$temp=array();
				if(isset($_POST['group'])){
					$t=preg_split('/,/',$data[$i]['number']);
					foreach($t as $q){
						array_push($temp,$data[$i]['itemdep'],$q,$items[$q]['name1'],$items[$q]['name2']);
						if(isset($items[$q]['bgcolor'])){
							array_push($temp,$items[$q]['bgcolor']);
						}
						else{
							array_push($temp,'null');
						}
						if(isset($items[$q]['size'.$init['init']['firlan']])){
							array_push($temp,$items[$q]['size'.$init['init']['firlan']],$items[$q]['size2'],$items[$q]['color'.$init['init']['firlan']],$items[$q]['color2'],$items[$q]['bold'.$init['init']['firlan']],$items[$q]['bold2']);
						}
						else{
							array_push($temp,'14','14','#000000','#898989','0','0');
						}
						array_push($itemlist,$temp);
					}
				}
				else{
					array_push($temp,$data[$i]['itemdep'],$data[$i]['number'],$items[$data[$i]['number']]['name'.$init['init']['firlan']],$items[$data[$i]['number']]['name2']);
					if(isset($items[$data[$i]['number']]['bgcolor'])){
						array_push($temp,$items[$data[$i]['number']]['bgcolor']);
					}
					else{
						array_push($temp,'null');
					}
					if(isset($items[$data[$i]['number']]['size'.$init['init']['firlan']])){
						array_push($temp,$items[$data[$i]['number']]['size'.$init['init']['firlan']],$items[$data[$i]['number']]['size2'],$items[$data[$i]['number']]['color'.$init['init']['firlan']],$items[$data[$i]['number']]['color2'],$items[$data[$i]['number']]['bold'.$init['init']['firlan']],$items[$data[$i]['number']]['bold2']);
					}
					else{
						array_push($temp,'14','14','#000000','#898989','0','0');
					}
					array_push($itemlist,$temp);
				}
			}
		}
		else{
		}
	}
	else if($items[$data[$i]['number']]['state']=='0'){
		if($changestate==''){
			$changestate=$data[$i]['number'];
		}
		else{
			$changestate.=','.$data[$i]['number'];
		}
	}
}
$itemlist['page']=$page;

if($changestate!=''){
	$conn=sqlconnect("../../../database","menu.db","","","","sqlite");
	$sql='UPDATE itemsdata SET state="0" WHERE inumber IN ('.$changestate.')';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,$sql,'sqlite');
}
else{
}

$items='';
echo json_encode($itemlist);
?>