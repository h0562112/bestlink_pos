<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
date_default_timezone_set('Asia/Taipei');
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['startdate'])){
	if($_SESSION['DB']==''){
		$conn1=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'menu.db','','','','sqlite');
	}
	else{
		$conn1=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_SESSION['DB'],'menu.db','','','','sqlite');
	}
	$menu=array();
	$sql='SELECT inumber,reartype FROM itemsdata';
	$tempmenu=sqlquery($conn1,$sql,'sqlite');
	foreach($tempmenu as $tm){
		$menu[$tm['inumber']]=$tm['reartype'];
	}
	sqlclose($conn1,'sqlite');
	$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
	$complete=0;
	for($m=0;$m<=$totalMon;$m++){
		if($_SESSION['DB']==''){
			$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
			if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-rear.ini')){
				$rearname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-rear.ini',true);
			}
			else{
				sqlclose($conn,'sqlite');
				$rearname='-1';
				break;
			}
		}
		else{
			$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
			if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_SESSION['DB'].'/'.$_POST['company'].'-rear.ini')){
				$rearname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_SESSION['DB'].'/'.$_POST['company'].'-rear.ini',true);
			}
			else{
				sqlclose($conn,'sqlite');
				$rearname='-1';
				break;
			}
		}
		if(!$conn){
			echo '資料庫尚未上傳資料。';
			sqlclose($conn,'sqlite');
		}
		else{
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql="SELECT CST012.BIZDATE,CST012.ITEMCODE,SUM(CST012.QTY) AS QTY,SUBSTR(CST012.CREATEDATETIME,0,11) AS SALETIME FROM CST012 JOIN CST011 ON CST011.BIZDATE=CST012.BIZDATE AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.NBCHKNUMBER IS NULL AND CST011.BIZDATE BETWEEN '".$start."' AND '".$end."' WHERE CST012.ITEMCODE<>'0000000000000000' GROUP BY SUBSTR(CST012.CREATEDATETIME,0,11),CST012.ITEMCODE";
				$menuarray=sqlquery($conn,$sql,'sqlite');
				
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
							if(isset($menu[intval($l['ITEMCODE'])])){
								if(isset($data[intval(substr($l['SALETIME'],8,2))])){
									if(isset($data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]])){
										$data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=intval($data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]])+intval($l['QTY']);
									}
									else{
										$data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=$l['QTY'];
									}
								}
								else{
									$data[intval(substr($l['SALETIME'],8,2))][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=$l['QTY'];
								}
								if(isset($data[$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]])){
									$data[$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=intval($data[$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]])+intval($l['QTY']);
								}
								else{
									$data[$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=intval($l['QTY']);
								}
							}
							else{
							}
							if(intval($maxhour)<intval(substr($l['SALETIME'],8,2))){
								$maxhour=substr($l['SALETIME'],8,2);
							}
							else{
							}
						}
						else{
							if(isset($menu[intval($l['ITEMCODE'])])){
								if(isset($data[intval(substr($l['SALETIME'],8,2))+24])){
									if(isset($data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]])){
										//$data[intval(substr($l['SALETIME'],11,2))+24][$l['BIZDATE']]=intval($data[intval(substr($l['SALETIME'],11,2))+24][$l['BIZDATE']])+intval($data[intval(substr($l['SALETIME'],11,2))+24][$l['BIZDATE']]);
										$data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=intval($data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]])+intval($l['QTY']);
									}
									else{
										$data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=$l['QTY'];
									}
								}
								else{
									$data[intval(substr($l['SALETIME'],8,2))+24][$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=$l['QTY'];
								}
								if(isset($data[$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]])){
									$data[$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=intval($data[$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]])+intval($l['QTY']);
								}
								else{
									$data[$l['BIZDATE']][$menu[intval($l['ITEMCODE'])]]=intval($l['QTY']);
								}
							}
							else{
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
		if($rearname!='-1'){
			echo '<table id="fixTable" class="table">';
			$check=0;
			for($t=-2;$t<=(intval($maxhour)+1);$t++){
				if($t==-1){
					echo '<thead>';
					echo '<tr>';
					echo '<td style="padding:5px;">營業日期</td>';
					for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
						echo "<td colspan='".sizeof($rearname)."' style='padding:5px;text-align:center;'>".substr(date("Ymd",$i),2,6)."<br>";
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
					
				}
				else if($t==-2){
					echo '<tr>';
					echo '<td style="padding:5px;">營業時段</td>';
					for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
						foreach($rearname as $r){
							echo "<td style='padding:5px;text-align:center;'>".$r['name']."</td>";
						}
					}
					echo '</tr>';
					echo '</thead>';
				}
				else if($t==(intval($maxhour)+1)){
					echo '<tr>';
					echo '<td style="padding:5px;">總計</td>';
					for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
						foreach($rearname as $d=>$r){
							echo "<td style='padding:5px;text-align:right;'>";
							if(isset($data[date("Ymd",$i)][$d])){
								echo number_format($data[date("Ymd",$i)][$d]);
							}
							else{
								echo '0';
							}
							echo '</td>';
						}
					}
					echo '</tr>';
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
								foreach($rearname as $d=>$r){
									echo "<td style='padding:5px;text-align:right;'>";
									if(isset($data[$t][date("Ymd",$i)][$d])){
										echo number_format($data[$t][date("Ymd",$i)][$d]);
									}
									else{
										echo '0';
									}
									echo "</td>";
								}
							}
							echo '</tr>';
						}
						else{
							echo "<tr>";
							echo "<td style='padding:5px;'>".str_pad($t%24, 2, "0", STR_PAD_LEFT).":00~".str_pad((intval($t)+1)%24, 2, "0", STR_PAD_LEFT).":00</td>";
							for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
								foreach($rearname as $d=>$r){
									echo "<td style='padding:5px;text-align:right;'>";
									echo '0';
									echo "</td>";
								}
							}
							echo '</tr>';
						}
					}
					else{
						if(isset($data[$t])){
							echo "<tr>";
							echo "<td style='padding:5px;'>".str_pad($t%24, 2, "0", STR_PAD_LEFT).":00~".str_pad((intval($t)+1)%24, 2, "0", STR_PAD_LEFT).":00</td>";
							for($i=strtotime(date($start));$i<=strtotime(date($end)),$i<=strtotime(date("Y-m-d"));$i=strtotime(date("Y-m-d",$i).' +1 days')){
								foreach($rearname as $d=>$r){
									echo "<td style='padding:5px;text-align:right;'>";
									if(isset($data[$t][date("Ymd",$i)][$d])){
										echo number_format($data[$t][date("Ymd",$i)][$d]);
									}
									else{
										echo '0';
									}
									echo "</td>";
								}
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
		else{
			echo '該功能尚未開啟。';
		}
	}
}
else{
}
?>