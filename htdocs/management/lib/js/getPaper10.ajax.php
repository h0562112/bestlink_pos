<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';

$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['startdate'])){
	$list=array();
	if($_SESSION['DB']==''){
		$itemname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-menu.ini',true);
	}
	else{
		$itemname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-menu.ini',true);
	}
	$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
	$complete=0;
	for($i=0;$i<=$totalMon;$i++){
		if($_SESSION['DB']==''){
			if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$i.' month')).'.db')){
				$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$i.' month')).'.db','','','','sqlite');
			}
			else{
				$conn='';
			}
		}
		else{
			if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_SESSION['DB'].'/SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$i.' month')).'.db')){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$i.' month')).'.db','','','','sqlite');
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
				$sql="SELECT CST012.CONSECNUMBER,ITEMCODE,ITEMNAME,ITEMGRPCODE,UNITPRICELINK,QTY,UNITPRICE,AMT FROM CST012 JOIN CST011 ON CST011.NBCHKNUMBER IS NULL AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.CREATEDATETIME=CST012.CREATEDATETIME WHERE CST012.ITEMCODE!='list' AND CST012.BIZDATE BETWEEN '".$start."' AND '".$end."' AND ((CST012.DTLMODE='1' AND CST012.DTLTYPE='1' AND CST012.DTLFUNC='01') OR (CST012.DTLMODE='1' AND CST012.DTLTYPE='3' AND CST012.DTLFUNC='02'))";
				$table=sqlquery($conn,$sql,'sqlite');
				$index=1;
				for($j=0;$j<sizeof($table);$j=$j+2){
					if($table[$j]['ITEMCODE']=='item'){
						if($table[$j]['ITEMGRPCODE']=='free'){
							if(isset($list[$table[$j+1]['ITEMCODE']])){
								echo '<input type="hidden" value="'.$table[$j]['CONSECNUMBER'].'">';
								echo '<input type="hidden" value="'.$table[$j+1]['CONSECNUMBER'].'">';
								echo '<input type="hidden" value="1">';
								//$list[$table[$j+1]['ITEMCODE']][$table[$j+1]['UNITPRICELINK']]
								$list[$table[$j+1]['ITEMCODE']][$table[$j+1]['UNITPRICELINK']]=intval($list[$table[$j+1]['ITEMCODE']][$table[$j]['UNITPRICELINK']])+((0-floatval($table[$j]['AMT']))/floatval($table[$j+1]['UNITPRICE']));
							}
							else{
								echo '<input type="hidden" value="'.$table[$j]['CONSECNUMBER'].'">';
								echo '<input type="hidden" value="'.$table[$j+1]['CONSECNUMBER'].'">';
								echo '<input type="hidden" value="2">';
								//$list[$table[$j+1]['ITEMCODE']][$table[$j+1]['UNITPRICELINK']]
								$list[$table[$j+1]['ITEMCODE']][$table[$j+1]['UNITPRICELINK']]=((0-floatval($table[$j]['AMT']))/floatval($table[$j+1]['UNITPRICE']));
							}
						}
						else{
							continue;
						}
					}
					else{
						if(isset($table[$j+1]['ITEMGRPCODE'])&&$table[$j+1]['ITEMGRPCODE']=='free'){
							if(isset($list[$table[$j]['ITEMCODE']])){
								echo '<input type="hidden" value="'.$table[$j]['CONSECNUMBER'].'">';
								echo '<input type="hidden" value="'.$table[$j+1]['CONSECNUMBER'].'">';
								echo '<input type="hidden" value="3">';
								//$list[$table[$j]['ITEMCODE']][$table[$j]['UNITPRICELINK']]
								$list[$table[$j]['ITEMCODE']][$table[$j]['UNITPRICELINK']]=intval($list[$table[$j]['ITEMCODE']][$table[$j]['UNITPRICELINK']])+((0-floatval($table[$j+1]['AMT']))/floatval($table[$j]['UNITPRICE']));
							}
							else{
								echo '<input type="hidden" value="'.$table[$j]['CONSECNUMBER'].'">';
								echo '<input type="hidden" value="'.$table[$j+1]['CONSECNUMBER'].'">';
								echo '<input type="hidden" value="4">';
								//$list[$table[$j]['ITEMCODE']][$table[$j]['UNITPRICELINK']]
								$list[$table[$j]['ITEMCODE']][$table[$j]['UNITPRICELINK']]=((0-floatval($table[$j+1]['AMT']))/floatval($table[$j]['UNITPRICE']));
							}
						}
						else{
							continue;
						}
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
		echo '資料庫未完整上傳。';
	}
	else{
		if($complete>0){
			echo '部分月份資料庫未完整上傳。';
		}
		else{
		}
		if(sizeof($list)==0){
			echo '資料庫尚未上傳資料。';
		}
		else{
			echo '<table class="table" style="border-bottom:1px solid #000000;margin-top:10px;">
					<tr>
						<td>產品</td>
						<td>招待數量</td>
					</tr>';
			foreach($list as $k=>$l){
				foreach($l as $kk=>$ll){
					echo '<tr>
							<td>'.$itemname[intval($k)]['name1'];
						if(strlen($kk)==0){
						}
						else{
							echo '('.$kk.')';
						}
						echo '</td>
							<td>'.number_format($ll).'</td>
						</tr>';
				}
			}
			echo '</table>';
		}
	}
}
else{
}
?>