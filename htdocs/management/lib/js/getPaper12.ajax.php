<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['startdate'])){
	$list=array();
	$cover=array();//暫存當月修改金額資料(修改月份可能跟紀錄月份不同)
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
				$sql='SELECT BIZDATE,DTLMODE,CLKCODE,ITEMNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,REMARKS,AMT,ZCOUNTER,CREATEDATETIME FROM CST012 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND (DTLMODE="4" OR DTLMODE="3") AND DTLTYPE="1" AND DTLFUNC="01" ORDER BY BIZDATE ASC,ZCOUNTER ASC,CREATEDATETIME ASC';
				$first=sqlquery($conn,$sql,'sqlite');
				if(sizeof($first)==0){
				}
				else{
					foreach($first as $item){
						if(isset($list[$item['BIZDATE']])){
							$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'<tr';
							$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'><td>'.$item['ZCOUNTER'].'</td><td>'.$item['CLKCODE'].'</td><td>'.$item['ITEMDEPTNAME'].'('.$item['ITEMNAME'].')</td>';
							if($item['DTLMODE']=='4'){//支出
								if(isset($list[$item['BIZDATE']]['outsubtotal'])){
									$list[$item['BIZDATE']]['outsubtotal']=floatval($list[$item['BIZDATE']]['outsubtotal'])+floatval($item['AMT']);
								}
								else{
									$list[$item['BIZDATE']]['outsubtotal']=floatval($item['AMT']);
								}
								$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'<td style="text-align:right;"></td><td style="text-align:right;">'.number_format($item['AMT']).'</td>';
							}
							else{//收入
								if(isset($list[$item['BIZDATE']]['insubtotal'])){
									$list[$item['BIZDATE']]['insubtotal']=floatval($list[$item['BIZDATE']]['insubtotal'])+floatval($item['AMT']);
								}
								else{
									$list[$item['BIZDATE']]['insubtotal']=floatval($item['AMT']);
								}
								$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'<td style="text-align:right;">'.number_format($item['AMT']).'</td><td style="text-align:right;"></td>';
							}
							$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'<td>';
							if($item['SELECTIVEITEM1']=='1'){
								$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'有';
							}
							else{
								$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'無';
							}
							$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'</td><td>'.substr($item['CREATEDATETIME'],0,4).'/'.substr($item['CREATEDATETIME'],4,2).'/'.substr($item['CREATEDATETIME'],6,2).' '.substr($item['CREATEDATETIME'],8,2).':'.substr($item['CREATEDATETIME'],10,2).'</td><td>'.$item['REMARKS'].'</td></tr>';
						}
						else{
							$list[$item['BIZDATE']]['content']='<table class="table" style="border-bottom:1px solid #000000;margin-top:10px;"><tr><td colspan="4" style="padding:5px;"><h2>'.$item['BIZDATE'].'</h2></td></tr><tr><td>班別</td><td>輸入人員代號</td><td style="text-align:right;">科目</td><td style="text-align:right;">收入金額</td><td style="text-align:right;">支出金額</td><td style="text-align:right;">憑證</td><td>輸入時間</td><td style="text-align:center;">備註</td></tr><tr';
							$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'><td>'.$item['ZCOUNTER'].'</td><td>'.$item['CLKCODE'].'</td><td>'.$item['ITEMDEPTNAME'].'('.$item['ITEMNAME'].')</td>';
							if($item['DTLMODE']=='4'){//支出
								$list[$item['BIZDATE']]['outsubtotal']=$item['AMT'];
								$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'<td style="text-align:right;"></td><td style="text-align:right;">'.number_format($item['AMT']).'</td>';
							}
							else{//收入
								$list[$item['BIZDATE']]['insubtotal']=$item['AMT'];
								$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'<td style="text-align:right;">'.number_format($item['AMT']).'</td><td style="text-align:right;"></td>';
							}
							$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'<td>';
							if($item['SELECTIVEITEM1']=='1'){
								$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'有';
							}
							else{
								$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'無';
							}
							$list[$item['BIZDATE']]['content']=$list[$item['BIZDATE']]['content'].'</td><td>'.substr($item['CREATEDATETIME'],0,4).'/'.substr($item['CREATEDATETIME'],4,2).'/'.substr($item['CREATEDATETIME'],6,2).' '.substr($item['CREATEDATETIME'],8,2).':'.substr($item['CREATEDATETIME'],10,2).'</td><td>'.$item['REMARKS'].'</td></tr>';
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
		echo '搜尋時間區間並無支出費用資料。';
		}
		else{
			foreach($list as $l){
				echo $l['content'];
				echo '<tr><td colspan="3">合計</td><td style="text-align:right;">';
				if(isset($l['insubtotal'])){
					echo number_format($l['insubtotal']);
				}
				else{
					echo '0';
				}
				echo '</td><td style="text-align:right;">';
				if(isset($l['outsubtotal'])){
					echo number_format($l['outsubtotal']);
				}
				else{
					echo '0';
				}
				echo '</td><td>=</td><td colspan="2">';
				if(isset($l['insubtotal'])&&isset($l['outsubtotal'])){
					echo number_format(floatval($l['insubtotal'])+floatval($l['outsubtotal']));
				}
				else if(isset($l['insubtotal'])){
					echo number_format($l['insubtotal']);
				}
				else if(isset($l['outsubtotal'])){
					echo number_format($l['outsubtotal']);
				}
				else{
					echo '0';
				}
				echo '</td></tr>';
				echo '</table>';
			}
		}
	}
}
else{
}
?>