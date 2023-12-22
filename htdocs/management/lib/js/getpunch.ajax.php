<?php
include_once '../../../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$start=$_POST['startdate'];
$end=$_POST['enddate'];
if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
	$ENDDATE=strtotime(date('Ymd'));
}
else{
	$ENDDATE=strtotime(date('Ymd',strtotime($end)));
}
$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'],'punch.db','','','','sqlite');
$sql='SELECT * FROM punchlist WHERE perno="'.$_POST['perno'].'" AND date BETWEEN "'.$_POST['startdate'].'" AND "'.$_POST['enddate'].'" AND state=1 ORDER BY date ASC,time ASC';
//echo $sql;
$punchlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(sizeof($punchlist)==0||!isset($punchlist[0]['percard'])){
	echo 'empty';
}
else{
	$personnel=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/personnel.ini',true);
	$list=array();
	$error=array();
	$date='';
	$time='';
	foreach($punchlist as $pu){
		if($pu['type']=='on'){
			$list[$pu['date']][$pu['time']]['on']=$pu['date'].' '.$pu['time'];
			$list[$pu['date']][$pu['time']]['realoff']='';
			$list[$pu['date']][$pu['time']]['off']='';
			$date=$pu['date'];
			$time=$pu['time'];
		}
		else if($pu['type']=='off'){
			$tempontime=preg_split('/:/',$pu['time']);
			if(intval($tempontime[1])<30){
				$tempontime[1]='00';
			}
			else{
				$tempontime[1]='30';
			}
			$temptime=implode(':',$tempontime);
			if($date!=''&&$time!=''){
				$list[$date][$time]['realoff']=$pu['date'].' '.$pu['time'];
				if(isset($personnel['basic']['computetime'])&&$personnel['basic']['computetime']=='2'){//時數以整點為主
					$list[$date][$time]['off']=$pu['date'].' '.$temptime;
				}
				else{//時數以分鐘為主
					$list[$date][$time]['off']=$pu['date'].' '.$pu['time'];
				}
			}
			else{
				$list[$pu['date']][$pu['time']]['on']='';
				$list[$pu['date']][$pu['time']]['realoff']=$pu['date'].' '.$pu['time'];
				if(isset($personnel['basic']['computetime'])&&$personnel['basic']['computetime']=='2'){//時數以整點為主
					$list[$pu['date']][$pu['time']]['off']=$pu['date'].' '.$temptime;
				}
				else{//時數以分鐘為主
					$list[$pu['date']][$pu['time']]['off']=$pu['date'].' '.$pu['time'];
				}
				//array_push($error,array('date'=>$date,'time'=>$time,'type'=>$punchlist[$i]['type'],'datetime'=>$punchlist[$i]['date'].' '.$punchlist[$i]['time']));
			}
			$date='';
			$time='';
		}
		else{
		}
	}
	$tabletitle='<tr>
					<th></th>
					<th>上班日</th>
					<th>上班時間</th>
					<th>下班時間</th>
					<th>時數</th>
					<th></th>
				</tr>';
	$table='';

	if($personnel['basic']['punchtype']=='1'){//無打下班卡
	}
	else{//需打下班卡
		$date=0;
		$ontimes=0;
		$worktime=0;
		for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
			$week=date("N",$d);
			if(isset($list[date('Y-m-d',$d)])){
				$date++;
				$index=0;
				foreach($list[date('Y-m-d',$d)] as $l){
					$ontimes++;
					$table=$table.'<tr>
							<td></td>
							<td style="text-align:center;">';
					if($index==0){
						if($week==6||$week==7){
							$table=$table."<span style='font-weight:bold;color:#C13333;'>";
						}
						else{
						}
						$table=$table. preg_replace('/-/','/',substr(date('Y-m-d',$d),2)).'<br>';
						switch ($week) {
							case 1:
								$table=$table."(一)";
								break;
							case 2:
								$table=$table. "(二)";
								break;
							case 3:
								$table=$table. "(三)";
								break;
							case 4:
								$table=$table. "(四)";
								break;
							case 5:
								$table=$table. "(五)";
								break;
							case 6:
								$table=$table. "(六)";
								break;
							case 7:
								$table=$table. "(日)";
								break;
							default:
								break;
						}
						if($week==6||$week==7){
							$table=$table. "</span>";
						}
						else{
						}
					}
					else{
					}
						$table=$table. '</td>
							<td style="padding:5px;text-align:center;">'.preg_replace('/-/','/',substr($l['on'],2,8)).' '.substr($l['on'],11,5).'</td>
							<td style="padding:5px;text-align:center;">'.preg_replace('/-/','/',substr($l['realoff'],2,8)).' '.substr($l['realoff'],11,5).'</td>
							<td style="text-align:center;">';
						if($l['on']!=''&&$l['off']!=''){
							$diff=date_diff(date_create($l['off']),date_create($l['on']));
							$temp=preg_split('/:/',$diff->format('%d:%h:%i'));
							//print_r($dt);
							if(intval(intval($temp[2])/30)){
								$dt=intval($temp[0])*24+intval($temp[1])+(intval(intval(intval($temp[2])/30))/2);
							}
							else{
								$dt=intval($temp[0])*24+intval($temp[1]);
							}
							$table=$table. $dt;
							$worktime=floatval($worktime)+floatval($dt);
						}
						else{
						}
						$table=$table. '</td>
							<td></td>
						</tr>';
					$index++;
				}
			}
			else{
				$table=$table. '<tr>
						<td></td>
						<td style="text-align:center;">';
					if($week==6||$week==7){
						$table=$table. "<span style='font-weight:bold;color:#C13333;'>";
					}
					else{
					}
					$table=$table. preg_replace('/-/','/',substr(date('Y-m-d',$d),2)).'<br>';
					switch ($week) {
						case 1:
							$table=$table. "(一)";
							break;
						case 2:
							$table=$table. "(二)";
							break;
						case 3:
							$table=$table. "(三)";
							break;
						case 4:
							$table=$table. "(四)";
							break;
						case 5:
							$table=$table. "(五)";
							break;
						case 6:
							$table=$table. "(六)";
							break;
						case 7:
							$table=$table. "(日)";
							break;
						default:
							break;
					}
					if($week==6||$week==7){
						$table=$table. "</span>";
					}
					else{
					}
					$table=$table. '</td>
						<td style="padding:5px;text-align:center;"></td>
						<td style="padding:5px;text-align:center;"></td>
						<td style="text-align:center;"></td>
						<td></td>
					</tr>';
			}
		}
	}
	$table=$table.'</table>';
	echo '<table class="table">
			<tr>
				<td></td>
				<td style="padding:5px;text-align:center;">總上班日</td>
				<td colspan="2" style="padding:5px;text-align:center;">總班次</td>
				<td style="padding:5px;text-align:center;">總時數</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td style="padding:5px;text-align:center;">'.$date.'</td>
				<td colspan="2" style="padding:5px;text-align:center;">'.$ontimes.'</td>
				<td style="padding:5px;text-align:center;">'.$worktime.'</td>
				<td></td>
			</tr>'.$tabletitle.$table;
}
?>