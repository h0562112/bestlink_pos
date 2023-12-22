<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if($_SESSION['DB']==''){
	if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini')){
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini');
		$machinemap=array_unique($temp);
	}
	else{
		$machinemap[]='m1';
	}
}
else{
	if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/mapping.ini')){
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/mapping.ini');
		$machinemap=array_unique($temp);
	}
	else{
		$machinemap[]='m1';
	}
}
if(isset($_POST['startdate'])){
	$list=array();
	if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
		$ENDDATE=strtotime(date('Ymd'));
	}
	else{
		$ENDDATE=strtotime(date('Ymd',strtotime($end)));
	}
	$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
	$complete=0;
	$sumtotal=array();
	for($m=0;$m<=$totalMon;$m=$m+2){
		$tempdate=date("Ymd",strtotime(substr($start,0,6).'01 +'.$m.' month'));
		foreach($machinemap as $machine){
			if($_SESSION['DB']==''){
				if(intval(date('m',strtotime($tempdate)))%2==0){
					if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.date('Ym',strtotime($tempdate)).'/invdata_'.date('Ym',strtotime($tempdate)).'_'.$machine.'.db')){
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.date('Ym',strtotime($tempdate)),'invdata_'.date('Ym',strtotime($tempdate)).'_'.$machine.'.db','','','','sqlite');
					}
					else{
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.date('Ym',strtotime($tempdate)).'.db','','','','sqlite');
					}
				}
				else{
					if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.date('Ym',strtotime($tempdate.' +1 month')).'/invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'_'.$machine.'.db')){
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.date('Ym',strtotime($tempdate.' +1 month')),'invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'_'.$machine.'.db','','','','sqlite');
					}
					else{
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'.db','','','','sqlite');
					}
				}
			}
			else{
				if(intval(date('m',strtotime($tempdate)))%2==0){
					if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.date('Ym',strtotime($tempdate)).'/invdata_'.date('Ym',strtotime($tempdate)).'_'.$machine.'.db')){
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.date('Ym',strtotime($tempdate)),'invdata_'.date('Ym',strtotime($tempdate)).'_'.$machine.'.db','','','','sqlite');
					}
					else{
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.date('Ym',strtotime($tempdate)).'.db','','','','sqlite');
					}
				}
				else{
					if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.date('Ym',strtotime($tempdate.' +1 month')).'/invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'_'.$machine.'.db')){
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.date('Ym',strtotime($tempdate.' +1 month')),'invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'_'.$machine.'.db','','','','sqlite');
					}
					else{
						$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'.db','','','','sqlite');
					}
				}
			}
			if(!$conn){
				echo '資料庫尚未上傳資料。';
			}
			else{
				$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="invlist"';
				$res=sqlquery($conn,$sql,'sqlite');
				if(isset($res[0]['name'])){
					$sql='SELECT createdate,COUNT(*) AS number FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY createdate';
					$s1=sqlquery($conn,$sql,'sqlite');
					//$sql='SELECT createdate,MIN(invnumber) AS startinv FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY createdate';
					$sql='SELECT createdate,invnumber AS startinv FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" ORDER BY createdate ASC,startinv ASC';
					$s2=sqlquery($conn,$sql,'sqlite');
					//$sql='SELECT createdate,MAX(invnumber) AS endinv FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY createdate';
					//$sql='SELECT createdate,invnumber AS endinv FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" ORDER BY createdate ASC,endinv DESC';
					//$s3=sqlquery($conn,$sql,'sqlite');
					$sql='SELECT createdate,COUNT(*) AS voidnumber,SUM(totalamount) AS voidmoney FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=99 GROUP BY createdate';
					$s4=sqlquery($conn,$sql,'sqlite');
					$sql='SELECT createdate,invnumber FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=99';
					$s5=sqlquery($conn,$sql,'sqlite');
					$sql='SELECT createdate,SUM(totalamount) AS summoney FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=1 GROUP BY createdate';
					$s6=sqlquery($conn,$sql,'sqlite');
					if(!isset($s1[0]['createdate'])){//總數量
					}
					else{
						foreach($s1 as $item){
							$list[$item['createdate']][$machine]['totalnumber']=$item['number'];
						}
					}
					/*if(!isset($s3[0]['createdate'])){//結束號碼
					}
					else{
						//foreach($s3 as $item){
							//$list[$item['createdate']][$machine]['endinv']=$item['endinv'];
						//}
					}*/
					if(!isset($s4[0]['createdate'])){//作廢張數、作廢金額
					}
					else{
						foreach($s4 as $item){
							$list[$item['createdate']][$machine]['voidnumber']=$item['voidnumber'];
							$list[$item['createdate']][$machine]['voidmoney']=$item['voidmoney'];
						}
					}
					if(!isset($s5[0]['createdate'])){//作廢號碼
					}
					else{
						foreach($s5 as $item){
							if(isset($list[$item['createdate']][$machine]['voidinv'])){
								$list[$item['createdate']][$machine]['voidinv'].=','.$item['invnumber'];
							}
							else{
								$list[$item['createdate']][$machine]['voidinv']=$item['invnumber'];
							}
						}
					}
					if(!isset($s6[0]['createdate'])){//開立金額
					}
					else{
						foreach($s6 as $item){
							$list[$item['createdate']][$machine]['summoney']=$item['summoney'];
						}
					}
					if(!isset($s2[0]['createdate'])){//起始號碼
					}
					else{
						/*foreach($s2 as $item){
							$list[$item['createdate']][$machine]['startinv']=$item['startinv'];
						}*/
						$Mdate='';
						$Minv='';
						$mininv='';
						for($i=0;$i<sizeof($s2);$i++){
							if(isset($list[$s2[$i]['createdate']][$machine]['startinv'])&&(intval($mininv)+1)==intval(substr($s2[$i]['startinv'],2))){
								$Mdate=$s2[$i]['createdate'];
								$Minv=$s2[$i]['startinv'];
								$mininv=substr($s2[$i]['startinv'],2);
							}
							else if(isset($list[$s2[$i]['createdate']][$machine]['startinv'])&&(intval($mininv)+1)<intval(substr($s2[$i]['startinv'],2))){
								$list[$s2[$i]['createdate']][$machine]['startinv'][]=$s2[$i]['startinv'];
								$list[$Mdate][$machine]['endinv'][]=$Minv;
								$Mdate=$s2[$i]['createdate'];
								$Minv=$s2[$i]['startinv'];
								$mininv=substr($s2[$i]['startinv'],2);
							}
							else{
								$list[$s2[$i]['createdate']][$machine]['startinv'][]=$s2[$i]['startinv'];
								if($Mdate==''){
								}
								else{
									$list[$Mdate][$machine]['endinv'][]=$Minv;
								}
								$Mdate=$s2[$i]['createdate'];
								$Minv=$s2[$i]['startinv'];
								$mininv=substr($s2[$i]['startinv'],2);
							}
						}
						$list[$Mdate][$machine]['endinv'][]=$Minv;
					}
				}
				else{
					$complete++;
				}
			}
			sqlclose($conn,'sqlite');
		}
		//for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 month')){
		/*$tempdate=date("Ymd",strtotime(substr($start,0,6).'01 +'.$m.' month'));
		if($_SESSION['DB']==''){
			if(intval(date('m',strtotime($tempdate)))%2==0){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.date('Ym',strtotime($tempdate)).'.db','','','','sqlite');
			}
			else{
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'.db','','','','sqlite');
			}
		}
		else{
			if(intval(date('m',strtotime($tempdate)))%2==0){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.date('Ym',strtotime($tempdate)).'.db','','','','sqlite');
			}
			else{
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'.db','','','','sqlite');
			}
		}
		if(!$conn){
			echo '資料庫尚未上傳資料。';
		}
		else{
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="invlist"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql='SELECT createdate,COUNT(*) AS number FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY createdate';
				$s1=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT createdate,MIN(invnumber) AS startinv FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY createdate';
				$s2=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT createdate,MAX(invnumber) AS endinv FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY createdate';
				$s3=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT createdate,COUNT(*) AS voidnumber,SUM(totalamount) AS voidmoney FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=99 GROUP BY createdate';
				$s4=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT createdate,invnumber FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=99';
				$s5=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT createdate,SUM(totalamount) AS summoney FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=1 GROUP BY createdate';
				$s6=sqlquery($conn,$sql,'sqlite');
				if(!isset($s1[0]['createdate'])){//總數量
				}
				else{
					foreach($s1 as $item){
						$list[$item['createdate']]['totalnumber']=$item['number'];
					}
				}
				if(!isset($s2[0]['createdate'])){//起始號碼
				}
				else{
					foreach($s2 as $item){
						$list[$item['createdate']]['startinv']=$item['startinv'];
					}
				}
				if(!isset($s3[0]['createdate'])){//結束號碼
				}
				else{
					foreach($s3 as $item){
						$list[$item['createdate']]['endinv']=$item['endinv'];
					}
				}
				if(!isset($s4[0]['createdate'])){//作廢張數、作廢金額
				}
				else{
					foreach($s4 as $item){
						$list[$item['createdate']]['voidnumber']=$item['voidnumber'];
						$list[$item['createdate']]['voidmoney']=$item['voidmoney'];
					}
				}
				if(!isset($s5[0]['createdate'])){//作廢號碼
				}
				else{
					foreach($s5 as $item){
						if(isset($list[$item['createdate']]['voidinv'])){
							$list[$item['createdate']]['voidinv'].=','.$item['invnumber'];
						}
						else{
							$list[$item['createdate']]['voidinv']=$item['invnumber'];
						}
					}
				}
				if(!isset($s6[0]['createdate'])){//開立金額
				}
				else{
					foreach($s6 as $item){
						$list[$item['createdate']]['summoney']=$item['summoney'];
					}
				}
			}
			else{
				$complete++;
			}
		}
		sqlclose($conn,'sqlite');*/
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
			echo '搜尋時間區間並無電子發票資料。';
		}
		else{
			echo '<table class="table" style="border-bottom:1px solid #000000;margin-top:10px;"><tr><td>日期</td>';
			if(isset($machinemap)&&sizeof($machinemap)==1){
			}
			else{
				echo '<td>機號</td>';
			}
			echo '<td>開立張數</td><td style="text-align:center;">開立金額<br><span style="font-size:12px;">(不含作廢)</span></td><td>起號</td><td>迄號</td><td>作廢張數</td><td>作廢金額</td><td style="width:135px;">作廢發票</td></tr>';
			foreach($list as $date=>$l){
				$machineindex=1;
				foreach($machinemap as $machine){
					if(isset($l[$machine])){
						for($i=0;$i<sizeof($l[$machine]['startinv']);$i++){
							echo '<tr style="font-size:22px">';
							if($i!=0){
								echo '<td></td>';
								if(isset($machinemap)&&sizeof($machinemap)==1){
								}
								else{
									echo '<td></td>';
								}
								echo '<td></td><td></td>';
								if(isset($l[$machine]['startinv'][$i])){
									echo '<td>'.$l[$machine]['startinv'][$i].'</td>';
								}
								else{
									echo '<td></td>';
								}
								if(isset($l[$machine]['endinv'][$i])){
									echo '<td>'.$l[$machine]['endinv'][$i].'</td>';
								}
								else{
									echo '<td></td>';
								}
								echo '<td></td><td></td><td></td>';
							}
							else{
								echo '<td style="text-align:center;">';
								if($machineindex==1){
									switch(date('N',strtotime($date))){
										case 1:
											echo substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'<br>(一)';
											break;
										case 2:
											echo substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'<br>(二)';
											break;
										case 3:
											echo substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'<br>(三)';
											break;
										case 4:
											echo substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'<br>(四)';
											break;
										case 5:
											echo substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'<br>(五)';
											break;
										case 6:
											echo '<span style="color:#ff0000;">'.substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'<br>(六)</span>';
											break;
										case 7:
											echo '<span style="color:#ff0000;">'.substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'<br>(日)</span>';
											break;
										default:
											break;
									}
									$machineindex++;
								}
								else{
								}
								echo '</td>';
								if(isset($machinemap)&&sizeof($machinemap)==1){
								}
								else{
									echo '<td>'.$machine.'</td>';
								}
								if(isset($l[$machine]['totalnumber'])){
									echo '<td style="text-align:right;">'.number_format($l[$machine]['totalnumber']).'</td>';
									if(isset($sumtotal['totalnumber'])){
										$sumtotal['totalnumber']=intval($sumtotal['totalnumber'])+intval($l[$machine]['totalnumber']);
									}
									else{
										$sumtotal['totalnumber']=$l[$machine]['totalnumber'];
									}
								}
								else{
									echo '<td style="text-align:right;">0</td>';
									if(isset($sumtotal['totalnumber'])){
									}
									else{
										$sumtotal['totalnumber']=0;
									}
								}
								if(isset($l[$machine]['summoney'])){
									echo '<td style="text-align:right;">'.number_format($l[$machine]['summoney']).'</td>';
									if(isset($sumtotal['summoney'])){
										$sumtotal['summoney']=intval($sumtotal['summoney'])+intval($l[$machine]['summoney']);
									}
									else{
										$sumtotal['summoney']=$l[$machine]['summoney'];
									}
								}
								else{
									echo '<td style="text-align:right;">0</td>';
									if(isset($sumtotal['summoney'])){
									}
									else{
										$sumtotal['summoney']=0;
									}
								}
								if(isset($l[$machine]['startinv'][$i])){
									echo '<td>'.$l[$machine]['startinv'][$i].'</td>';
								}
								else{
									echo '<td></td>';
								}
								if(isset($l[$machine]['endinv'][$i])){
									echo '<td>'.$l[$machine]['endinv'][$i].'</td>';
								}
								else{
									echo '<td></td>';
								}
								if(isset($l[$machine]['voidnumber'])){
									echo '<td style="text-align:right;">'.number_format($l[$machine]['voidnumber']).'</td>';
									if(isset($sumtotal['voidnumber'])){
										$sumtotal['voidnumber']=intval($sumtotal['voidnumber'])+intval($l[$machine]['voidnumber']);
									}
									else{
										$sumtotal['voidnumber']=$l[$machine]['voidnumber'];
									}
								}
								else{
									echo '<td style="text-align:right;">0</td>';
									if(isset($sumtotal['voidnumber'])){
									}
									else{
										$sumtotal['voidnumber']=0;
									}
								}
								if(isset($l[$machine]['voidmoney'])){
									echo '<td style="text-align:right;">'.number_format($l[$machine]['voidmoney']).'</td>';
									if(isset($sumtotal['voidmoney'])){
										$sumtotal['voidmoney']=intval($sumtotal['voidmoney'])+intval($l[$machine]['voidmoney']);
									}
									else{
										$sumtotal['voidmoney']=$l[$machine]['voidmoney'];
									}
								}
								else{
									echo '<td style="text-align:right;">0</td>';
									if(isset($sumtotal['voidmoney'])){
									}
									else{
										$sumtotal['voidmoney']=0;
									}
								}
								if(isset($l[$machine]['voidinv'])){
									echo '<td style="width:135px;word-break: break-all;">'.$l[$machine]['voidinv'].'</td>';
								}
								else{
									echo '<td></td>';
								}
							}
							echo '</tr>';
						}
					}
					else{
					}
				}
			}
			echo '<tr style="font-size:22px"><td>合計</td>';
			if(isset($machinemap)&&sizeof($machinemap)==1){
			}
			else{
				echo '<td></td>';
			}
			echo '<td style="text-align:right;">'.number_format($sumtotal['totalnumber']).'</td><td style="text-align:right;">'.number_format($sumtotal['summoney']).'</td><td></td><td></td><td style="text-align:right;">'.number_format($sumtotal['voidnumber']).'</td><td style="text-align:right;">'.number_format($sumtotal['voidmoney']).'</td><td style="width:135px;"></td></tr>';
			echo '</table>';
		}
	}
}
else{
}
?>