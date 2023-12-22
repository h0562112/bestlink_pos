<?php
session_start();
date_default_timezone_set('Asia/Taipei');
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
	if(is_dir('../../../doc/')){
	}
	else{
		mkdir('../../../doc');
	}
	$filepath='../doc/'.date('Ymd');
	if(is_dir('../../'.$filepath.'/')){
	}
	else{
		mkdir('../../'.$filepath);
	}
	$file=$filepath.'/'.date('YmdHis').'.csv';
	$f=fopen('../../'.$file,'w');
	if(isset($machinemap)&&sizeof($machinemap)==1){
		fwrite($f,'���,�}�߱i��,�}�ߪ��B(���t�@�o),�_��,����,�@�o�i��,�@�o���B,�@�o�o��'.PHP_EOL);
	}
	else{
		fwrite($f,'���,����,�}�߱i��,�}�ߪ��B(���t�@�o),�_��,����,�@�o�i��,�@�o���B,�@�o�o��'.PHP_EOL);
	}
	
	$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
	$complete=0;
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
				//echo '��Ʈw�|���W�Ǹ�ơC';
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
					//$s3=sqlquery($conn,$sql,'sqlite');
					$sql='SELECT createdate,COUNT(*) AS voidnumber,SUM(totalamount) AS voidmoney FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=99 GROUP BY createdate';
					$s4=sqlquery($conn,$sql,'sqlite');
					$sql='SELECT createdate,invnumber FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=99';
					$s5=sqlquery($conn,$sql,'sqlite');
					$sql='SELECT createdate,SUM(totalamount) AS summoney FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=1 GROUP BY createdate';
					$s6=sqlquery($conn,$sql,'sqlite');
					if(!isset($s1[0]['createdate'])){//�`�ƶq
					}
					else{
						foreach($s1 as $item){
							$list[$item['createdate']][$machine]['totalnumber']=$item['number'];
						}
					}
					/*if(!isset($s3[0]['createdate'])){//�������X
					}
					else{
						foreach($s3 as $item){
							$list[$item['createdate']][$machine]['endinv']=$item['endinv'];
						}
					}*/
					if(!isset($s4[0]['createdate'])){//�@�o�i�ơB�@�o���B
					}
					else{
						foreach($s4 as $item){
							$list[$item['createdate']][$machine]['voidnumber']=$item['voidnumber'];
							$list[$item['createdate']][$machine]['voidmoney']=$item['voidmoney'];
						}
					}
					if(!isset($s5[0]['createdate'])){//�@�o���X
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
					if(!isset($s6[0]['createdate'])){//�}�ߪ��B
					}
					else{
						foreach($s6 as $item){
							$list[$item['createdate']][$machine]['summoney']=$item['summoney'];
						}
					}
					if(!isset($s2[0]['createdate'])){//�_�l���X
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
		/*if($_SESSION['DB']==''){
			$setup=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/setup.ini',true);
			if(intval(date('m',strtotime($tempdate)))%2==0){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.date('Ym',strtotime($tempdate)).'.db','','','','sqlite');
			}
			else{
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'.db','','','','sqlite');
			}
		}
		else{
			$setup=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/setup.ini',true);
			if(intval(date('m',strtotime($tempdate)))%2==0){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.date('Ym',strtotime($tempdate)).'.db','','','','sqlite');
			}
			else{
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.date('Ym',strtotime($tempdate.' +1 month')).'.db','','','','sqlite');
			}
		}
		if(!$conn){
			//echo '��Ʈw�|���W�Ǹ�ơC';
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
				if(!isset($s1[0]['createdate'])){//�`�ƶq
				}
				else{
					foreach($s1 as $item){
						$list[$item['createdate']]['totalnumber']=$item['number'];
					}
				}
				if(!isset($s2[0]['createdate'])){//�_�l���X
				}
				else{
					foreach($s2 as $item){
						$list[$item['createdate']]['startinv']=$item['startinv'];
					}
				}
				if(!isset($s3[0]['createdate'])){//�������X
				}
				else{
					foreach($s3 as $item){
						$list[$item['createdate']]['endinv']=$item['endinv'];
					}
				}
				if(!isset($s4[0]['createdate'])){//�@�o�i�ơB�@�o���B
				}
				else{
					foreach($s4 as $item){
						$list[$item['createdate']]['voidnumber']=$item['voidnumber'];
						$list[$item['createdate']]['voidmoney']=$item['voidmoney'];
					}
				}
				if(!isset($s5[0]['createdate'])){//�@�o���X
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
				if(!isset($s6[0]['createdate'])){//�}�ߪ��B
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
		echo '��Ʈw������W�ǡC';
	}
	else{
		if($complete>0){
			echo '���������Ʈw������W�ǡC';
		}
		else{
		}
		if(sizeof($list)==0){
			echo '�j�M�ɶ��϶��õL�q�l�o����ơC';
		}
		else{
			foreach($list as $date=>$l){
				$machineindex=1;
				foreach($machinemap as $machine){
					if(isset($l[$machine])){
						for($i=0;$i<sizeof($l[$machine]['startinv']);$i++){
							if($i!=0){
								if(isset($machinemap)&&sizeof($machinemap)==1){
									fwrite($f,',,,');
								}
								else{
									fwrite($f,',,');
								}
								
								if(isset($l[$machine]['startinv'][$i])){
									fwrite($f,','.$l[$machine]['startinv'][$i]);
								}
								else{
									fwrite($f,',');
								}
								if(isset($l[$machine]['endinv'][$i])){
									fwrite($f,','.$l[$machine]['endinv'][$i]);
								}
								else{
									fwrite($f,',');
								}
								fwrite($f,',,,'.PHP_EOL);
							}
							else{
								if($machineindex==1){
									switch(date('N',strtotime($date))){
										case 1:
											fwrite($f,substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'(�@)');
											break;
										case 2:
											fwrite($f,substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'(�G)');
											break;
										case 3:
											fwrite($f,substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'(�T)');
											break;
										case 4:
											fwrite($f,substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'(�|)');
											break;
										case 5:
											fwrite($f,substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'(��)');
											break;
										case 6:
											fwrite($f,substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'(��)');
											break;
										case 7:
											fwrite($f,substr($date,2,2).'/'.substr($date,4,2).'/'.substr($date,6,2).'(��)');
											break;
										default:
											break;
									}
									$machineindex++;
								}
								else{
								}
								if(isset($machinemap)&&sizeof($machinemap)==1){
								}
								else{
									fwrite($f,','.$machine);
								}
								if(isset($l[$machine]['totalnumber'])){
									fwrite($f,','.$l[$machine]['totalnumber']);
								}
								else{
									fwrite($f,',0');
								}
								if(isset($l[$machine]['summoney'])){
									fwrite($f,','.$l[$machine]['summoney']);
								}
								else{
									fwrite($f,',0');
								}
								if(isset($l[$machine]['startinv'][$i])){
									fwrite($f,','.$l[$machine]['startinv'][$i]);
								}
								else{
									fwrite($f,',');
								}
								if(isset($l[$machine]['endinv'][$i])){
									fwrite($f,','.$l[$machine]['endinv'][$i]);
								}
								else{
									fwrite($f,',');
								}
								if(isset($l[$machine]['voidnumber'])){
									fwrite($f,','.$l[$machine]['voidnumber']);
								}
								else{
									fwrite($f,',0');
								}
								if(isset($l[$machine]['voidmoney'])){
									fwrite($f,','.$l[$machine]['voidmoney']);
								}
								else{
									fwrite($f,',0');
								}
								if(isset($l[$machine]['voidinv'])){
									fwrite($f,','.$l[$machine]['voidinv']);
								}
								else{
									fwrite($f,',');
								}
								fwrite($f,PHP_EOL);
							}
						}
					}
					else{
					}
				}
			}
		}
	}
	fclose($f);
	echo $file;
}
else{
}
?>