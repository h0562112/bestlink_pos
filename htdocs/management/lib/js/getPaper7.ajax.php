<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
date_default_timezone_set('Asia/Taipei');
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['startdate'])){
	$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
	$complete=0;
	for($m=0;$m<=$totalMon;$m++){
		if($_SESSION['DB']==''){
			$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
		}
		else{
			$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
		}
		if(!$conn){
			echo '資料庫尚未上傳資料。';
			sqlclose($conn,'sqlite');
		}
		else{
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				if(isset($_POST['type'])&&$_POST['type']!=''){
					$sql="SELECT BIZDATE,SUM(TAX6+TAX7+TAX8) AS AMT,SUBSTR(CREATEDATETIME,0,11) AS SALETIME FROM (SELECT BIZDATE,CASE WHEN (TAX6+TAX7+TAX8)=0 THEN 1 ELSE TAX6 END AS TAX6,TAX7,TAX8,".$_POST['type']." AS CREATEDATETIME FROM CST011 WHERE BIZDATE BETWEEN '".$start."' AND '".$end."' AND NBCHKNUMBER IS NULL) GROUP BY SUBSTR(CREATEDATETIME,0,11) ORDER BY SALETIME";
				}
				else{
					$sql="SELECT BIZDATE,SUM(TAX6+TAX7+TAX8) AS AMT,SUBSTR(CREATEDATETIME,0,11) AS SALETIME FROM (SELECT BIZDATE,CASE WHEN (TAX6+TAX7+TAX8)=0 THEN 1 ELSE TAX6 END AS TAX6,TAX7,TAX8,CASE WHEN UPDATEDATETIME='0' THEN CREATEDATETIME ELSE UPDATEDATETIME END AS CREATEDATETIME FROM CST011 WHERE BIZDATE BETWEEN '".$start."' AND '".$end."' AND NBCHKNUMBER IS NULL) GROUP BY SUBSTR(CREATEDATETIME,0,11) ORDER BY SALETIME";
				}
				$menuarray=sqlquery($conn,$sql,'sqlite');
				
				//print_r($menuarray);
				if(sizeof($menuarray)==0){
					echo "查無資料。";
				}
				else if($menuarray[0]=="SQL語法錯誤"||$menuarray[0]=="連線失敗"){
					if($dubug==1){
						echo $list[0]."(select)".$sql;
					}
					else{
						echo $list[0]."(select)";
					}
				}
				else{
					$maxhour=0;
					foreach($menuarray as $l){
						if($l['BIZDATE']==substr($l['SALETIME'],0,8)){
							if(isset($data[intval(substr($l['SALETIME'],8,2))])){
								if(isset($data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']])){
									$data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']]=intval($data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']])+intval($l['AMT']);
								}
								else{
									$data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']]=$l['AMT'];
								}
							}
							else{
								$data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']]=$l['AMT'];
							}
							if(intval($maxhour)<intval(substr($l['SALETIME'],8,2))){
								$maxhour=substr($l['SALETIME'],8,2);
							}
							else{
							}
						}
						else{
							if(isset($data[intval(substr($l['SALETIME'],8,2))+24])){
								if(isset($data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']])){
									//$data[intval(substr($l['SALETIME'],11,2))+24][$l['BIZDATE']]=intval($data[intval(substr($l['SALETIME'],11,2))+24][$l['BIZDATE']])+intval($data[intval(substr($l['SALETIME'],11,2))+24][$l['BIZDATE']]);
									$data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']]=intval($data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']])+intval($l['AMT']);
								}
								else{
									$data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']]=$l['AMT'];
								}
							}
							else{
								$data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']]=$l['AMT'];
							}
							if(intval($maxhour)<intval(intval(substr($l['SALETIME'],8,2))+24)){
								$maxhour=intval(substr($l['SALETIME'],8,2))+24;
							}
							else{
							}
						}
					}
				}
			}
			else{
				$complete++;
			}
		}
		sqlclose($conn,'sqlite');
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
		echo '<table id="fixTable" class="table">';
		$check=0;
		for($t=-1;$t<=$maxhour;$t++){
			if($t==-1){
				echo '<thead>';
				echo '<tr>';
				echo '<td style="padding:5px;">營業日期<br>營業時段</td>';
				for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
					echo "<td style='padding:5px;text-align:center;'>".substr(date("Ymd",$i),2,6)."<br>";
					switch (date("N",$i)) {
						case 1:
							echo "(一)";
							break;
						case 2:
							echo "(二)";
							break;
						case 3:
							echo "(三)";
							break;
						case 4:
							echo "(四)";
							break;
						case 5:
							echo "(五)";
							break;
						case 6:
							echo "<span style='font-weight:bold;color:#C13333;'>(六)</span>";
							break;
						case 7:
							echo "<span style='font-weight:bold;color:#C13333;'>(日)</span>";
							break;
						default:
							break;
					}
					echo "</td>";
				}
				echo '</tr>';
				echo '</thead>';
			}
			else{
				if($t==0){
					echo '<tbody>';
				}
				else{
				}
				if($check==1){
					if(isset($data[$t])){
						echo "<tr>";
						echo "<td style='padding:5px;'>".str_pad($t%24, 2, "0", STR_PAD_LEFT).":00~".str_pad((intval($t)+1)%24, 2, "0", STR_PAD_LEFT).":00</td>";
						for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
							echo "<td style='padding:5px;text-align:right;'>";
							if(isset($data[$t][date("Ymd",$i)])){
								echo number_format($data[$t][date("Ymd",$i)]);
							}
							else{
								echo '0';
							}
							echo "</td>";
						}
						echo '</tr>';
					}
					else{
						echo "<tr>";
						echo "<td style='padding:5px;'>".str_pad($t%24, 2, "0", STR_PAD_LEFT).":00~".str_pad((intval($t)+1)%24, 2, "0", STR_PAD_LEFT).":00</td>";
						for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
							echo "<td style='padding:5px;text-align:right;'>";
							echo '0';
							echo "</td>";
						}
						echo '</tr>';
					}
				}
				else{
					if(isset($data[$t])){
						echo "<tr>";
						echo "<td style='padding:5px;'>".str_pad($t%24, 2, "0", STR_PAD_LEFT).":00~".str_pad((intval($t)+1)%24, 2, "0", STR_PAD_LEFT).":00</td>";
						for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
							echo "<td style='padding:5px;text-align:right;'>";
							if(isset($data[$t][date("Ymd",$i)])){
								echo number_format($data[$t][date("Ymd",$i)]);
							}
							else{
								echo '0';
							}
							echo "</td>";
						}
						echo '</tr>';
						$check=1;
					}
					else{
					}
				}
			}
		}
		echo '</tbody></table>';
	}
}
else{
}
?>