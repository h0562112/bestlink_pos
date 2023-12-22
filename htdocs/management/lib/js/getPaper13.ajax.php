<?php
session_start();
date_default_timezone_set('Asia/Taipei');
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if($_SESSION['DB']==''){
	$initsetting=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/initsetting.ini',true);
	if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'&&file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini')){
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini');
		$machinemap=array_unique($temp);
	}
	else{
		$machinemap[]='m1';
	}
}
else{
	$initsetting=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
	if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'&&file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/mapping.ini')){
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
				$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="number"';
				$res=sqlquery($conn,$sql,'sqlite');
				if(isset($res[0]['name'])){
					$sql='SELECT createdate AS BIZDATE,createtime AS CRETIME,invnumber AS INVOICENUMBER,relatenumber AS CONSECNUMBER,totalamount AS SALESTTLAMT,state,carrierid1,buyerid FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" ORDER BY createdate ASC,invnumber ASC';
					$first=sqlquery($conn,$sql,'sqlite');
					if(sizeof($first)==0){
					}
					else{
						foreach($first as $item){
							if(isset($list[$item['BIZDATE']][$machine]['list'])){
								$list[$item['BIZDATE']][$machine]['endinv']=$item['INVOICENUMBER'];
								$list[$item['BIZDATE']][$machine]['totalsum']=intval($list[$item['BIZDATE']][$machine]['totalsum'])+intval($item['SALESTTLAMT']);
								if($item['state']==1){
									$list[$item['BIZDATE']][$machine]['suctotalsum']=intval($list[$item['BIZDATE']][$machine]['suctotalsum'])+intval($item['SALESTTLAMT']);
									//$list[$item['BIZDATE']]['faitotalsum']=intval($list[$item['BIZDATE']]['faitotalsum'])+intval($item['SALESTTLAMT']);
								}
								else{
									//$list[$item['BIZDATE']]['suctotalsum']=intval($list[$item['BIZDATE']]['suctotalsum'])+intval($item['SALESTTLAMT']);
									$list[$item['BIZDATE']][$machine]['faitotalsum']=intval($list[$item['BIZDATE']][$machine]['faitotalsum'])+intval($item['SALESTTLAMT']);
								}
								$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<tr';
								if($item['state']==99){
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].' style="color:#ff0000;"';
								}
								else{
								}
								$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'><td>'.$item['INVOICENUMBER'].'</td><td>'.$item['CONSECNUMBER'].'</td><td style="text-align:right;">'.number_format($item['SALESTTLAMT']).'</td><td>'.$item['BIZDATE'].preg_replace('/:/','',$item['CRETIME']).'</td>';
								if(strlen($item['carrierid1'])==0){
									if($item['buyerid']=="0000000000"){
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td></td>';
									}
									else{
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td>'.$item['buyerid'].'</td>';
									}
								}
								else{
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td>'.$item['carrierid1'].'</td>';
								}
								if($item['state']==1){
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td></td>';
								}
								else{
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td>作廢</td>';
								}
								if(isset($_POST['admin'])){
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td><div class="C0701" style="cursor: pointer; border: 1px solid #898989; padding: 0 6px; margin: 3px 5px; border-radius: 5px; box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;"><input type="hidden" class="inv" value="'.$item['INVOICENUMBER'].'"><input type="hidden" class="month" value="';
									if(intval(date('m',strtotime($tempdate)))%2==0){
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].date('Ym',strtotime($tempdate));
									}
									else{
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].date('Ym',strtotime($tempdate.' +1 month'));
									}
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'"><input type="hidden" class="machine" value="'.$machine.'">取消作廢</div></td>';

									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td><div class="C0501" style="cursor: pointer; border: 1px solid #898989; padding: 0 6px; margin: 3px 5px; border-radius: 5px; box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;"><input type="hidden" class="inv" value="'.$item['INVOICENUMBER'].'"><input type="hidden" class="month" value="';
									if(intval(date('m',strtotime($tempdate)))%2==0){
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].date('Ym',strtotime($tempdate));
									}
									else{
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].date('Ym',strtotime($tempdate.' +1 month'));
									}
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'"><input type="hidden" class="machine" value="'.$machine.'">作廢發票</div></td>';
								}
								else{
								}
								$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'</tr>';
							}
							else{
								$list[$item['BIZDATE']][$machine]['startinv']=$item['INVOICENUMBER'];
								$list[$item['BIZDATE']][$machine]['endinv']=$item['INVOICENUMBER'];
								$list[$item['BIZDATE']][$machine]['totalsum']=$item['SALESTTLAMT'];
								if($item['state']==1){
									$list[$item['BIZDATE']][$machine]['suctotalsum']=$item['SALESTTLAMT'];
									$list[$item['BIZDATE']][$machine]['faitotalsum']=0;
								}
								else{
									$list[$item['BIZDATE']][$machine]['suctotalsum']=0;
									$list[$item['BIZDATE']][$machine]['faitotalsum']=$item['SALESTTLAMT'];
								}
								$list[$item['BIZDATE']][$machine]['list']='<table class="table" style="border-bottom:1px solid #000000;margin-top:10px;"><tr><td colspan="11" style="padding:5px;"><h2>'.$item['BIZDATE'];
								if(isset($machinemap)&&sizeof($machinemap)==1){
								}
								else{
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'-'.$machine;
								}
								$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'</h2></td></tr><tr><td>電子發票號</td><td>帳單號</td><td style="text-align:right;">開立金額</td><td style="text-align:center;">開立時間</td><td>統編／載具</td><td>備註</td></tr><tr';
								if($item['state']==99){
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].' style="color:#ff0000;"';
								}
								else{
								}
								$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'><td>'.$item['INVOICENUMBER'].'</td><td>'.$item['CONSECNUMBER'].'</td><td style="text-align:right;">'.number_format($item['SALESTTLAMT']).'</td><td>'.$item['BIZDATE'].preg_replace('/:/','',$item['CRETIME']).'</td>';
								if(strlen($item['carrierid1'])==0){
									if($item['buyerid']=="0000000000"){
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td></td>';
									}
									else{
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td>'.$item['buyerid'].'</td>';
									}
								}
								else{
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td>'.$item['carrierid1'].'</td>';
								}
								if($item['state']==1){
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td></td>';
								}
								else{
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td>作廢</td>';
								}
								if(isset($_POST['admin'])){
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td><div class="C0701" style="cursor: pointer; border: 1px solid #898989; padding: 0 6px; margin: 3px 5px; border-radius: 5px; box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;"><input type="hidden" class="inv" value="'.$item['INVOICENUMBER'].'"><input type="hidden" class="month" value="';
									if(intval(date('m',strtotime($tempdate)))%2==0){
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].date('Ym',strtotime($tempdate));
									}
									else{
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].date('Ym',strtotime($tempdate.' +1 month'));
									}
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'"><input type="hidden" class="machine" value="'.$machine.'">取消作廢</div></td>';

									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'<td><div class="C0501" style="cursor: pointer; border: 1px solid #898989; padding: 0 6px; margin: 3px 5px; border-radius: 5px; box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;"><input type="hidden" class="inv" value="'.$item['INVOICENUMBER'].'"><input type="hidden" class="month" value="';
									if(intval(date('m',strtotime($tempdate)))%2==0){
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].date('Ym',strtotime($tempdate));
									}
									else{
										$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].date('Ym',strtotime($tempdate.' +1 month'));
									}
									$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'"><input type="hidden" class="machine" value="'.$machine.'">作廢發票</div></td>';
								}
								else{
								}
								$list[$item['BIZDATE']][$machine]['list']=$list[$item['BIZDATE']][$machine]['list'].'</tr>';
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
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="number"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql='SELECT createdate AS BIZDATE,createtime AS CRETIME,invnumber AS INVOICENUMBER,relatenumber AS CONSECNUMBER,totalamount AS SALESTTLAMT,state FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" ORDER BY createdate ASC,invnumber ASC';
				$first=sqlquery($conn,$sql,'sqlite');
				if(sizeof($first)==0){
				}
				else{
					foreach($first as $item){
						if(isset($list[$item['BIZDATE']]['list'])){
							$list[$item['BIZDATE']]['endinv']=$item['INVOICENUMBER'];
							$list[$item['BIZDATE']]['totalsum']=intval($list[$item['BIZDATE']]['totalsum'])+intval($item['SALESTTLAMT']);
							if($item['state']==1){
								$list[$item['BIZDATE']]['suctotalsum']=intval($list[$item['BIZDATE']]['suctotalsum'])+intval($item['SALESTTLAMT']);
								//$list[$item['BIZDATE']]['faitotalsum']=intval($list[$item['BIZDATE']]['faitotalsum'])+intval($item['SALESTTLAMT']);
							}
							else{
								//$list[$item['BIZDATE']]['suctotalsum']=intval($list[$item['BIZDATE']]['suctotalsum'])+intval($item['SALESTTLAMT']);
								$list[$item['BIZDATE']]['faitotalsum']=intval($list[$item['BIZDATE']]['faitotalsum'])+intval($item['SALESTTLAMT']);
							}
							$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'<tr';
							if($item['state']==99){
								$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].' style="color:#ff0000;"';
							}
							else{
							}
							$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'><td>'.$item['INVOICENUMBER'].'</td><td>'.$item['CONSECNUMBER'].'</td><td style="text-align:right;">'.number_format($item['SALESTTLAMT']).'</td><td>'.$item['BIZDATE'].preg_replace('/:/','',$item['CRETIME']).'</td>';
							if($item['state']==1){
								$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'<td></td>';
							}
							else{
								$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'<td>作廢</td>';
							}
							$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'</tr>';
						}
						else{
							$list[$item['BIZDATE']]['startinv']=$item['INVOICENUMBER'];
							$list[$item['BIZDATE']]['endinv']=$item['INVOICENUMBER'];
							$list[$item['BIZDATE']]['totalsum']=$item['SALESTTLAMT'];
							if($item['state']==1){
								$list[$item['BIZDATE']]['suctotalsum']=$item['SALESTTLAMT'];
								$list[$item['BIZDATE']]['faitotalsum']=0;
							}
							else{
								$list[$item['BIZDATE']]['suctotalsum']=0;
								$list[$item['BIZDATE']]['faitotalsum']=$item['SALESTTLAMT'];
							}
							$list[$item['BIZDATE']]['list']='<table class="table" style="border-bottom:1px solid #000000;margin-top:10px;"><tr><td colspan="11" style="padding:5px;"><h2>'.$item['BIZDATE'].'</h2></td></tr><tr><td>電子發票號</td><td>帳單號</td><td style="text-align:right;">開立金額</td><td style="text-align:center;">開立時間</td><td>備註</td></tr><tr';
							if($item['state']==99){
								$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].' style="color:#ff0000;"';
							}
							else{
							}
							$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'><td>'.$item['INVOICENUMBER'].'</td><td>'.$item['CONSECNUMBER'].'</td><td style="text-align:right;">'.number_format($item['SALESTTLAMT']).'</td><td>'.$item['BIZDATE'].preg_replace('/:/','',$item['CRETIME']).'</td>';
							if($item['state']==1){
								$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'<td></td>';
							}
							else{
								$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'<td>作廢</td>';
							}
							$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].'</tr>';
						}
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
			foreach($list as $l){
				foreach($machinemap as $machine){
					if(isset($l[$machine]['list'])){
						echo $l[$machine]['list'];
						echo '<tr style="font-size:22px">
								<td colspan="6">起號：'.$l[$machine]['startinv'].' 迄號：'.$l[$machine]['endinv'].'</td>
							</tr>
							<tr style="font-size:22px">
								<td colspan="6">合計金額：'.number_format($l[$machine]['totalsum']).'</td>
							</tr>
							<tr style="font-size:22px">
								<td colspan="6">開立金額：'.number_format($l[$machine]['suctotalsum']).'</td>
							</tr>
							<tr style="font-size:22px">
								<td colspan="6">作廢金額：'.number_format($l[$machine]['faitotalsum']).'</td>
							</tr>';
						echo '</table>';
					}
					else{
					}
				}
			}
		}
	}
}
else{
}
?>