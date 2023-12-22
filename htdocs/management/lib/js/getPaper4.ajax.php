<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';

function quick_sort($array){
	// find array size
	$length = count($array);
	
	// base case test, if array of length 0 then just return array to caller
	if($length <= 1){
		return $array;
	}
	else{
		
		$t='';

		// select an item to act as our pivot point, since list is unsorted first position is easiest
		//$pivot = $array[0];
		foreach($array as $k=>$a){
			$pivot[$k]['no'] = $k;
			$pivot[$k]['name'] = $a['name'];
			$pivot[$k]['amt'] = $a['amt'];
			$t=$k;
			break;
		}
		
		// declare our two arrays to act as partitions
		$left = $right = array();
		
		// loop and compare each item in the array to the pivot value, place item in appropriate partition
		/*for($i = 1; $i < count($array); $i++)
		{
			if($array[$i] < $pivot){
				$left[] = $array[$i];
			}
			else{
				$right[] = $array[$i];
			}
		}*/
		foreach($array as $k=>$a){
			if($k==$t){
				continue;
			}
			else{
				if($a['amt'] >= $pivot[$t]['amt']){
					$left[$k]['no'] = $k;
					$left[$k]['name'] = $a['name'];
					$left[$k]['amt'] = $a['amt'];
				}
				else{
					$right[$k]['no'] = $k;
					$right[$k]['name'] = $a['name'];
					$right[$k]['amt'] = $a['amt'];
				}
			}
		}
		
		// use recursion to now sort the left and right lists
		return array_merge(quick_sort($left), $pivot, quick_sort($right));
	}
}
$start=preg_replace('/-/','',$_POST['startdate1']);
$end=preg_replace('/-/','',$_POST['enddate1']);
$start2=preg_replace('/-/','',$_POST['startdate2']);
$end2=preg_replace('/-/','',$_POST['enddate2']);
if(isset($_POST['startdate1'])){
	$list=array();
	$temp=array();
	if($_SESSION['DB']==''){
		$itemname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-menu.ini',true);
	}
	else{
		$itemname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-menu.ini',true);
	}
	$totalMon=getMon($_POST['startdate1'],$_POST['enddate1']);
	$complete=0;
	for($i=0;$i<=$totalMon;$i++){
		if($_SESSION['DB']==''){
			if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/SALES_'.date("Ym",strtotime(substr(preg_replace('/-/','',$_POST['startdate1']),0,6).'01 +'.$i.' month')).'.db')){
				$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr(preg_replace('/-/','',$_POST['startdate1']),0,6).'01 +'.$i.' month')).'.db','','','','sqlite');
			}
			else{
				$conn='';
			}
		}
		else{
			if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_SESSION['DB'].'/SALES_'.date("Ym",strtotime(substr(preg_replace('/-/','',$_POST['startdate1']),0,6).'01 +'.$i.' month')).'.db')){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr(preg_replace('/-/','',$_POST['startdate1']),0,6).'01 +'.$i.' month')).'.db','','','','sqlite');
			}
			else{
				$conn='';
			}
		}
		if(!$conn||$conn==''){
		}
		else{
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql="SELECT SUM(CST012.AMT) AS TAMT,CST012.ITEMCODE,CST012.ITEMDEPTCODE FROM CST012 JOIN CST011 ON CST011.NBCHKNUMBER IS NULL AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.CREATEDATETIME=CST012.CREATEDATETIME WHERE CST012.ITEMCODE<>'0000000000000000' AND CST012.BIZDATE BETWEEN '".$start."' AND '".$end."' AND CST012.DTLMODE='1' AND CST012.DTLTYPE='1' AND CST012.DTLFUNC='01' GROUP BY CST012.ITEMCODE,CST012.ITEMDEPTCODE ORDER BY SUM(CST012.AMT) DESC,CST012.ITEMDEPTCODE,CST012.ITEMCODE";
				$table=sqlquery($conn,$sql,'sqlite');
				$index=1;
				foreach($table as $t){
					if(isset($list[$t['ITEMCODE']]['name'])){
						$list[intval($t['ITEMCODE'])]['amt']=floatval($list[$t['ITEMCODE']]['amt'])+floatval($t['TAMT']);
					}
					else{
						$list[intval($t['ITEMCODE'])]['name']=$itemname[intval($t['ITEMCODE'])]['name1'];
						$list[intval($t['ITEMCODE'])]['amt']=$t['TAMT'];
					}
				}
			}
			else{
				$complete++;
			}
			sqlclose($conn,'sqlite');
		}
	}
	if($complete>=($totalMon+1)){
		echo '主區間之資料庫未完整上傳。';
	}
	else{
		if($complete>0){
			echo '部分月份主區間之資料庫未完整上傳。';
		}
		else{
		}
		if(sizeof($list)==0){
			echo '查無資料。';
		}
		else{
			//$list=array("1"=>array("amt"=>"20"),"2"=>array("amt"=>"15"),"3"=>array("amt"=>"2"),"4"=>array("amt"=>"60"),"5"=>array("amt"=>"65"),"6"=>array("amt"=>"1"),"7"=>array("amt"=>"21"));
			$sort=quick_sort($list);
		}
	}
}
else{
}
if(isset($_POST['startdate2'])){
	$list=array();
	$temp=array();
	if($_SESSION['DB']==''){
		$itemname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-menu.ini',true);
	}
	else{
		$itemname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-menu.ini',true);
	}
	$totalMon=getMon($_POST['startdate2'],$_POST['enddate2']);
	$complete=0;
	for($i=0;$i<=$totalMon;$i++){
		$tempstart=date("Ym",strtotime($_POST['startdate2'].' +'.$i.' month'));
		if($_SESSION['DB']==''){
			if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/SALES_'.date("Ym",strtotime(substr(preg_replace('/-/','',$_POST['startdate2']),0,6).'01 +'.$i.' month')).'.db')){
				$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr(preg_replace('/-/','',$_POST['startdate2']),0,6).'01 +'.$i.' month')).'.db','','','','sqlite');
			}
			else{
				$conn='';
			}
		}
		else{
			if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_SESSION['DB'].'/SALES_'.date("Ym",strtotime(substr(preg_replace('/-/','',$_POST['startdate2']),0,6).'01 +'.$i.' month')).'.db')){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr(preg_replace('/-/','',$_POST['startdate2']),0,6).'01 +'.$i.' month')).'.db','','','','sqlite');
			}
			else{
				$conn='';
			}
		}
		if(!$conn||$conn==''){
		}
		else{
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql="SELECT SUM(CST012.AMT) AS TAMT,CST012.ITEMCODE,CST012.ITEMDEPTCODE FROM CST012 JOIN CST011 ON CST011.NBCHKNUMBER IS NULL AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.CREATEDATETIME=CST012.CREATEDATETIME WHERE CST012.ITEMCODE<>'0000000000000000' AND CST012.BIZDATE BETWEEN '".$start2."' AND '".$end2."' AND CST012.DTLMODE='1' AND CST012.DTLTYPE='1' AND CST012.DTLFUNC='01' GROUP BY CST012.ITEMCODE,CST012.ITEMDEPTCODE ORDER BY SUM(CST012.AMT) DESC,CST012.ITEMDEPTCODE,CST012.ITEMCODE";
				$table=sqlquery($conn,$sql,'sqlite');
				$index=1;
				foreach($table as $t){
					if(isset($list[$t['ITEMCODE']]['name'])){
						$list[intval($t['ITEMCODE'])]['amt']=floatval($list[$t['ITEMCODE']]['amt'])+floatval($t['TAMT']);
					}
					else{
						$list[intval($t['ITEMCODE'])]['name']=$itemname[intval($t['ITEMCODE'])]['name1'];
						$list[intval($t['ITEMCODE'])]['amt']=$t['TAMT'];
					}
				}
			}
			else{
				$complete++;
			}
			sqlclose($conn,'sqlite');
		}
	}
	if($complete>=($totalMon+1)){
		echo '副區間之資料庫未完整上傳。';
	}
	else{
		if($complete>0){
			echo '部分月份副區間之資料庫未完整上傳。';
		}
		else{
		}
		if(sizeof($list)==0){
			//echo '資料庫尚未上傳資料。';
			$sort2=array();
		}
		else{
			$sort2=quick_sort($list);
		}
	}
}
else{
}
$temp=array();
$temp2=array();
$befrank='';
$nowrank='';
for($i=0;$i<sizeof($sort);$i++){
	if($i>0&&$sort[$i]['amt']==$sort[$i-1]['amt']){
		$sort[$i]['rank']=$befrank;
	}
	else{
		$sort[$i]['rank']=$i+1;
		$befrank=$i+1;
	}
	array_push($temp,$sort[$i]['no']);
}
$befrank='';
$nowrank='';
for($i=0;$i<sizeof($sort2);$i++){
	if($i>0&&$sort2[$i]['amt']==$sort2[$i-1]['amt']){
		$sort2[$i]['rank']=$befrank;
	}
	else{
		$sort2[$i]['rank']=$i+1;
		$befrank=$i+1;
	}
	$nowrank=$befrank;
	array_push($temp2,$sort2[$i]['no']);
}
//print_r($sort);
//print_r($sort2);
//print_r($temp);
//print_r($temp2);
echo '<table id="fixTable" class="table">';
	echo '<thead>
			<tr>
				<th>產品</th>
				<th>目前排名</th>
				<th>前期排名</th>
				<th>(變動)</th>
				<th>主區間金額</th>
				<th>副區間金額</th>
			</tr>
		</thead>';
	echo '<tbody>';
$befrank='';
$nowrank='';
if($_SESSION['DB']==''){
	$unit=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/unit.ini',true);
}
else{
	$unit=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/unit.ini',true);
}
for($i=0;$i<sizeof($sort);$i++){
	echo '<tr>';
	echo '<td>'.$sort[$i]['name'].'</td>';
	echo '<td style="text-align:right;">'.$sort[$i]['rank'].'</td>';
	if(in_array($sort[$i]['no'],$temp2)){
		echo '<td style="text-align:right;">'.$sort2[array_search($sort[$i]['no'],$temp2)]['rank'].'</td>';
	}
	else{
		echo '<td style="text-align:right;">'.intval(sizeof($sort)).'</td>';
	}
	if(in_array($sort[$i]['no'],$temp2)){
		echo '<td style="text-align:right;">(';
		if((intval($sort2[array_search($sort[$i]['no'],$temp2)]['rank'])-intval($sort[$i]['rank']))>0){
			echo '<span style="color:#ff4c00;">▲</span>'.(intval($sort2[array_search($sort[$i]['no'],$temp2)]['rank'])-intval($sort[$i]['rank']));
		}
		else if((intval($sort2[array_search($sort[$i]['no'],$temp2)]['rank'])-intval($sort[$i]['rank']))<0){
			echo '<span style="color:#009900;">▼</span>'.(0-(intval($sort2[array_search($sort[$i]['no'],$temp2)]['rank'])-intval($sort[$i]['rank'])));
		}
		else{
			echo '⁃';
		}
		echo ')</td>';
	}
	else{
		echo '<td style="text-align:right;">(';
		if((intval(sizeof($sort))-intval($sort[$i]['rank']))>0){
			echo '<span style="color:#ff4c00;">▲</span>'.(intval(sizeof($sort))-intval($sort[$i]['rank']));
		}
		else if((intval(sizeof($sort))-intval($sort[$i]['rank']))<0){
			echo '<span style="color:#009900;">▼</span>'.(0-(intval(sizeof($sort))-intval($sort[$i]['rank'])));
		}
		else{
			echo '⁃';
		}
		echo ')</td>';
	}
	echo '<td style="text-align:right;">'.$unit['init']['frontunit'].number_format($sort[$i]['amt']).$unit['init']['unit'].'</td>';
	if(in_array($sort[$i]['no'],$temp2)){
		echo '<td style="text-align:right;">'.$unit['init']['frontunit'].number_format($sort2[array_search($sort[$i]['no'],$temp2)]['amt']).$unit['init']['unit'].'</td>';
	}
	else{
		echo '<td style="text-align:right;"><em>'.$unit['init']['frontunit'].'0'.$unit['init']['unit'].'</em></td>';
	}
	echo '</tr>';
}
	echo '</tbody>';
echo '</table>';
?>