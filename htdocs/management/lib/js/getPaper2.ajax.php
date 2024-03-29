<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['startdate'])){
	$list=array();
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
		}
		else{
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql='SELECT BIZDATE,INVOICENUMBER,CONSECNUMBER,SALESTTLQTY,SALESTTLAMT,TABLENUMBER,REMARKS,ZCOUNTER,CREATEDATETIME FROM CST011 WHERE NBCHKNUMBER IS NOT NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC';
				$first=sqlquery($conn,$sql,'sqlite');
				if(sizeof($first)==0){
				}
				else{
					foreach($first as $item){
						if(isset($list[$item['BIZDATE']]['list'])){
							if($item['REMARKS']=='tempvoid'||$item['REMARKS']=='temp'){
								if(intval($list[$item['BIZDATE']]['num'])%5==0&&intval($list[$item['BIZDATE']]['num'])>0){
									$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].',<br>'.$item['CONSECNUMBER'].'(暫)';
								}
								else{
									$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].','.$item['CONSECNUMBER'].'(暫)';
								}
							}
							else{
								if(intval($list[$item['BIZDATE']]['num'])%5==0&&intval($list[$item['BIZDATE']]['num'])>0){
									if(strlen($item['INVOICENUMBER'])!=10){
										$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].',<br>'.$item['CONSECNUMBER'];
									}
									else{
										$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].',<br>'.$item['CONSECNUMBER'].'('.$item['INVOICENUMBER'].')';
									}
								}
								else{
									if(strlen($item['INVOICENUMBER'])!=10){
										$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].','.$item['CONSECNUMBER'];
									}
									else{
										$list[$item['BIZDATE']]['list']=$list[$item['BIZDATE']]['list'].','.$item['CONSECNUMBER'].'('.$item['INVOICENUMBER'].')';
									}
								}
							}
							$list[$item['BIZDATE']]['num']=intval($list[$item['BIZDATE']]['num'])+1;
							$list[$item['BIZDATE']]['money']=floatval($list[$item['BIZDATE']]['money'])+floatval($item['SALESTTLAMT']);
						}
						else{
							$list[$item['BIZDATE']]['num']=1;
							$list[$item['BIZDATE']]['money']=floatval($item['SALESTTLAMT']);
							if(strlen($item['INVOICENUMBER'])!=10){
								$list[$item['BIZDATE']]['list']=$item['CONSECNUMBER'];
							}
							else{
								$list[$item['BIZDATE']]['list']=$item['CONSECNUMBER'].'('.$item['INVOICENUMBER'].')';
							}
						}
						if(isset($list['allqty'])){
							$list['allqty']=intval($list['allqty'])+1;
							$list['allamt']=floatval($list['allamt'])+floatval($item['SALESTTLAMT']);
						}
						else{
							$list['allqty']=1;
							$list['allamt']=floatval($item['SALESTTLAMT']);
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
		if(sizeof($list)==0){
			echo '搜尋時間區間並無作廢資料。';
		}
		else{
			echo '<table id="fixTable" class="table"><thead><tr><td style="padding:5px;">日期</td><td style="padding:5px;">作廢單號</td><td style="padding:5px;">合計張數</td><td style="padding:5px;">合計金額</td></tr></thead>';
			if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
				$ENDDATE=strtotime(date('Ymd'));
			}
			else{
				$ENDDATE=strtotime(date('Ymd',strtotime($end)));
			}
			echo '<tbody>';
			for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
				if(isset($list[date('Ymd',$d)]['list'])){
					echo '<tr>';
					echo '<td style="padding:5px;">'.date('Y/m/d',$d).'</td>';
					echo '<td style="padding:5px;">'.$list[date('Ymd',$d)]['list'].'</td>';
					echo '<td style="padding:5px;text-align:right;">'.number_format($list[date('Ymd',$d)]['num']).'</td>';
					echo '<td class="money" style="padding:5px;text-align:right;"><div>'.number_format($list[date('Ymd',$d)]['money']).'</div></td>';
					echo '</tr>';
				}
				else{
					/*echo '<tr>';
					echo '<td style="padding:5px;">'.date('Y/m/d',$d).'</td>';
					echo '<td style="padding:5px;"></td>';
					echo '<td style="padding:5px;text-align:right;">0</td>';
					echo '<td class="money" style="padding:5px;text-align:right;"><div>0</div></td>';
					echo '</tr>';*/
				}
			}
			echo '</tbody>';
			echo '<tfoot>';
			echo '<tr id="preday">';
			echo '<td style="padding:5px;">合計</td>';
			echo '<td style="padding:5px;"></td>';
			echo '<td style="padding:5px;text-align:right;">'.number_format($list['allqty']).'</td>';
			echo '<td class="money" style="padding:5px;text-align:right;"><div>'.number_format($list['allamt']).'</div></td>';
			echo '</tr>';
			echo '</tfoot></table>';
		}
	}
}
else{
}
?>