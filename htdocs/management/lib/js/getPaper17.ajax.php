<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$invstart=$_POST['startdate'];
if($_SESSION['DB']==''){
	$machinedata=parse_ini_file('../../../menudata/'.$_SESSION['company'].'/'.$_POST['dbname'].'/setup.ini',true);
	if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini')){
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini');
		$machinemap=array_unique($temp);
	}
	else{
		$machinemap[]='m1';
	}
}
else{
	$machinedata=parse_ini_file('../../../menudata/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/setup.ini',true);
	if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/mapping.ini')){
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/mapping.ini');
		$machinemap=array_unique($temp);
	}
	else{
		$machinemap[]='m1';
	}
}
$dbarray=array();
if(isset($machinedata['inv']['total'])){//兩個月總發票組數
	if(isset($_POST['startdate'])){
		$complete=0;
		foreach($machinemap as $index=>$machine){
			if($_SESSION['DB']==''){
				if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.$invstart.'/invdata_'.$invstart.'_'.$machine.'.db')){
					if(!isset($conn)){
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.$invstart,'invdata_'.$invstart.'_'.$machine.'.db','','','','sqlite');
					}
					else{
						$r=$conn->exec("ATTACH 'd://xampp/htdocs/outposandorder/ourpos/".$_SESSION['company']."/".$_POST['dbname']."/".$invstart."/invdata_".$invstart."_".$machine.".db' AS ".$machine);
						$dbarray[$machine]='1';
					}
				}
				else if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/invdata_'.$invstart.'.db')){
					if(!isset($conn)){
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.$invstart.'.db','','','','sqlite');
					}
					else{
					}
				}
				else{
				}
			}
			else{
				if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$invstart.'/invdata_'.$invstart.'_'.$machine.'.db')){
					if(!isset($conn)){
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$invstart,'invdata_'.$invstart.'_'.$machine.'.db','','','','sqlite');
					}
					else{
						$r=$conn->exec("ATTACH 'd://xampp/htdocs/outposandorder/ourpos/".$_SESSION['company']."/".$_SESSION['DB']."/".$invstart."/invdata_".$invstart."_".$machine.".db' AS ".$machine);
						$dbarray[$machine]='1';
					}
				}
				else if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/invdata_'.$invstart.'.db')){
					if(!isset($conn)){
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.$invstart.'.db','','','','sqlite');
					}
					else{
					}
				}
				else{
				}
			}
		}
		//foreach($machinemap as $machine){
			if(isset($conn)&&$conn){
				if(isset($dbarray)&&sizeof($dbarray)>0){
					foreach($dbarray as $idnex=>$machine){
						$sql='SELECT name FROM '.$index.'.sqlite_master WHERE type="table" AND name="number"';
						$res=sqlquery($conn,$sql,'sqlite');
						if(isset($res[0]['name'])){
						}
						else{
							$dbarray[$index]='0';
						}
					}
				}
				else{
				}
				$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="number"';
				$res=sqlquery($conn,$sql,'sqlite');
				if(isset($res[0]['name'])){
					if(isset($dbarray)&&sizeof($dbarray)>0){
						$temp=array_unique($dbarray);
					}
					else{
					}
					if(isset($temp)&&((sizeof($temp)==1&&array_pop($temp)=='1')||sizeof($temp)==2)){
						$sql1='SELECT banno FROM number';
						$sql2='SELECT * FROM number WHERE state=1';
						$sql3='SELECT banno FROM number';
						$sql4='SELECT * FROM number WHERE state=0';
						foreach($dbarray as $index=>$value){
							if($value=='1'){
								$sql1 .= ' UNION SELECT banno FROM '.$index.'.number';
								$sql2 .= ' UNION SELECT * FROM '.$index.'.number WHERE state=1';
								$sql3 .= ' UNION SELECT banno FROM '.$index.'.number';
								$sql4 .= ' UNION SELECT * FROM '.$index.'.number WHERE state=0';
							}
							else{
							}
						}
						
						$sql1='SELECT banno FROM ('.$sql1.') ORDER BY banno ASC LIMIT 1';
						$startinv=sqlquery($conn,$sql1,'sqlite');
						$sql2='SELECT * FROM ('.$sql2.') ORDER BY banno ASC';
						$emptyinv=sqlquery($conn,$sql2,'sqlite');
						$sql3='SELECT banno FROM ('.$sql3.') ORDER BY banno DESC LIMIT 1';
						$endinv=sqlquery($conn,$sql3,'sqlite');
						$sql4='SELECT banno FROM ('.$sql4.') ORDER BY banno ASC';
						$alreadyinv=sqlquery($conn,$sql4,'sqlite');
					}
					else{
						$sql='SELECT banno FROM number ORDER BY banno ASC LIMIT 1';
						$startinv=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT * FROM number WHERE state=1 ORDER BY banno ASC';
						$emptyinv=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT banno FROM number ORDER BY banno DESC LIMIT 1';
						$endinv=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT banno FROM number WHERE state=0 ORDER BY banno ASC';
						$alreadyinv=sqlquery($conn,$sql,'sqlite');
					}
				}
				else{
					$complete++;
				}
				sqlclose($conn,'sqlite');
			}
			else{
			}
			if($complete>=1){
				echo '資料庫未完整上傳。';
			}
			else{
				if(!isset($emptyinv)||sizeof($emptyinv)==0){
					echo '搜尋時間區間並無空白發票資料。';
				}
				else{
					echo '<div>';
						if((intval(substr($startinv[0]['banno'],2))+intval($machinedata['inv']['total'])*50)>intval(substr($endinv[0]['banno'],2))){
							echo '<h2>共有 '.(sizeof($emptyinv)+((intval(substr($startinv[0]['banno'],2))+intval($machinedata['inv']['total'])*50-1)-intval(substr($endinv[0]['banno'],2)))).'張 空白發票</h2>';
						}
						else{
							echo '<h2>共有 '.sizeof($emptyinv).'張 空白發票</h2>';
						}
						//echo '<p>共有 '.sizeof($emptyinv).'張 空白發票</p>';
						echo '<h2>空白發票區段</h2>';
						$en=substr($startinv[0]['banno'],0,2);
						$start='';
						$end=substr($startinv[0]['banno'],2);
						$index=1;
						echo '<table>
								<tr>
									<td>序號</td>
									<td>空白發票起號</td>
									<td></td>
									<td>空白發票迄號</td>
								</tr>';
						foreach($emptyinv as $l){
							if($start==''&&intval(substr($l['banno'],2))>=intval($end)){
								$start=substr($l['banno'],2);
								$end=substr($l['banno'],2);
								echo '<tr>
										<td>'.$index.'</td>
										<td>'.$l['banno'].'</td>
										<td>～</td>';
								$index++;
							}
							else if(intval($start)<intval(substr($l['banno'],2))&&intval(substr($l['banno'],2))>(intval($end)+1)&&!in_array($en.(intval($end)+1),$alreadyinv)){
									echo '<td>'.$en.$end.'</td>
									</tr>';
								$start=substr($l['banno'],2);
								$end=substr($l['banno'],2);
								echo '<tr>
										<td>'.$index.'</td>
										<td>'.$l['banno'].'</td>
										<td>～</td>';
								$index++;
							}
							else if(intval($start)<intval(substr($l['banno'],2))&&intval(substr($l['banno'],2))>(intval($end)+1)&&in_array($en.(intval($end)+1),$alreadyinv)){
								$end=(intval($end)+1);
							}
							else if(intval($start)<intval(substr($l['banno'],2))&&intval(substr($l['banno'],2))==intval($end)+1){
								$end=substr($l['banno'],2);
							}
							else{
							}
						}
						if(intval($start)<intval(substr($emptyinv[sizeof($emptyinv)-1]['banno'],2))&&intval(substr($emptyinv[sizeof($emptyinv)-1]['banno'],2))<=intval((intval(substr($startinv[0]['banno'],2))+intval($machinedata['inv']['total'])*50-1))){
							echo '<td>'.$en.str_pad((intval(substr($startinv[0]['banno'],2))+intval($machinedata['inv']['total'])*50-1),8,"0",STR_PAD_LEFT).'</td>
								</tr>';
							echo '<input type="hidden" value="'.intval($machinedata['inv']['total']).'">';
							echo '<input type="hidden" value="'.intval(substr($emptyinv[sizeof($emptyinv)-1]['banno'],2)).'-'.intval((intval(substr($startinv[0]['banno'],2))+intval($machinedata['inv']['total'])*50-1)).'">';
							echo '<input type="hidden" value="'.intval($start).'-'.intval(substr($emptyinv[sizeof($emptyinv)-1]['banno'],2)).'">';
						}
						else{
							echo '<input type="hidden" value="'.intval($machinedata['inv']['total']).'">';
							echo '<input type="hidden" value="'.intval(substr($emptyinv[sizeof($emptyinv)-1]['banno'],2)).'-'.intval((intval(substr($startinv[0]['banno'],2))+intval($machinedata['inv']['total'])*50-1)).'">';
							echo '<input type="hidden" value="'.intval($start).'-'.intval(substr($emptyinv[sizeof($emptyinv)-1]['banno'],2)).'">';
						}
						echo '</table>';
					echo '</div>';
				}
			}
		//}
	}
	else{
	}
}
else{
	echo '<form class="setmachinedata" method="post" action="">
			<table>
				<caption>
					請填入申請的發票組數
				</caption>
				<tr>
					<td>發票組數</td>
					<td><input type="number" name="total"></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="button" id="send" value="確認"></td>
				</tr>
			</table>
		</form>';
}
?>