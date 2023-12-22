<?php
include_once '../../../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql="SELECT * FROM personnel WHERE state=1 AND perno='".$_POST['perno']."' ORDER BY credatetime ASC";
$content=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'],'punch.db','','','','sqlite');
$sql='SELECT * FROM punchlist WHERE perno="'.$_POST['perno'].'" AND date BETWEEN "'.$_POST['startdate'].'" AND "'.$_POST['enddate'].'" AND state=1 ORDER BY date ASC,time ASC';
$punchlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

$filename=date('YmdHis').'.csv';
$fp=fopen('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/doc/'.$filename,'x');
fwrite($fp,"\xEF\xBB\xBF");
fclose($fp);
$listtable=array();
array_push($listtable,array('員工打卡紀錄'));
array_push($listtable,array('員工編號',iconv("UTF-8","Big5",$content[0]['percard']),'員工姓名',iconv("UTF-8","Big5",$content[0]['name'])));

if(sizeof($punchlist)==0||!isset($punchlist[0]['percard'])){
	array_push($listtable,array('總上班日','總班次','','總時數'));
	array_push($listtable,array(0,0,'',0));
}
else{
	$start=$_POST['startdate'];
	$end=$_POST['enddate'];
	if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
		$ENDDATE=strtotime(date('Ymd'));
	}
	else{
		$ENDDATE=strtotime(date('Ymd',strtotime($end)));
	}
	$personnel=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/personnel.ini',true);
	$list=array();
	$error=array();
	$date='';
	$time='';
	foreach($punchlist as $pu){
		if($pu['type']=='on'){
			$list[$pu['date']][$pu['time']]['on']=$pu['date'].' '.$pu['time'];
			$list[$pu['date']][$pu['time']]['off']='';
			$date=$pu['date'];
			$time=$pu['time'];
		}
		else if($pu['type']=='off'){
			if($date!=''&&$time!=''){
				$list[$date][$time]['off']=$pu['date'].' '.$pu['time'];
			}
			else{
				array_push($error,array('date'=>$date,'time'=>$time,'type'=>$pu['type'],'datetime'=>$pu['date'].' '.$pu['time']));
			}
			$date='';
			$time='';
		}
		else{
		}
	}
	array_push($listtable,array('上班日','上班時間','下班時間','時數'));

	if($personnel['basic']['punchtype']=='1'){//無打下班卡
	}
	else{//需打下班卡
		$date=0;
		$ontimes=0;
		$worktime=0;
		
		for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
			$record=array();//暫存單筆記錄(班含一次上、下班)
			$week=date("N",$d);
			if(isset($list[date('Y-m-d',$d)])){
				$date++;
				$index=0;
				foreach($list[date('Y-m-d',$d)] as $l){
					$ontimes++;
					if($index==0){
						$datetime=preg_replace('/-/','/',substr(date('Y-m-d',$d),2));
						switch ($week) {
							case 1:
								$datetime=$datetime."(一)";
								break;
							case 2:
								$datetime=$datetime."(二)";
								break;
							case 3:
								$datetime=$datetime."(三)";
								break;
							case 4:
								$datetime=$datetime."(四)";
								break;
							case 5:
								$datetime=$datetime."(五)";
								break;
							case 6:
								$datetime=$datetime."(六)";
								break;
							case 7:
								$datetime=$datetime."(日)";
								break;
							default:
								break;
						}
						array_push($record,$datetime);
					}
					else{
						array_push($record,'');
					}
					array_push($record,preg_replace('/-/','/',substr($l['on'],2,8)).' '.substr($l['on'],11,5),preg_replace('/-/','/',substr($l['off'],2,8)).' '.substr($l['off'],11,5));

						if($l['off']!=''){
							$diff=date_diff(date_create($l['off']),date_create($l['on']));
							$temp=preg_split('/:/',$diff->format('%h:%i'));
							//print_r($dt);
							if(intval(intval($temp[1])/30)){
								$dt=intval($temp[0])+(intval(intval(intval($temp[1])/30))/2);
							}
							else{
								$dt=intval($temp[0]);
							}
							array_push($record,$dt);
							$worktime=floatval($worktime)+floatval($dt);
						}
						else{
						}
					$index++;
				}
			}
			else{
				$datetime=preg_replace('/-/','/',substr(date('Y-m-d',$d),2));
				switch ($week) {
					case 1:
						$datetime=$datetime."(一)";
						break;
					case 2:
						$datetime=$datetime."(二)";
						break;
					case 3:
						$datetime=$datetime."(三)";
						break;
					case 4:
						$datetime=$datetime."(四)";
						break;
					case 5:
						$datetime=$datetime."(五)";
						break;
					case 6:
						$datetime=$datetime."(六)";
						break;
					case 7:
						$datetime=$datetime."(日)";
						break;
					default:
						break;
				}
				array_push($record,$datetime);
			}
			array_push($listtable,$record);
		}
	}
	array_push($listtable,array('總上班日','總班次','','總時數'));
	array_push($listtable,array($date,$ontimes,'',$worktime));
}

//echo $_POST['company'].'-'.$_POST['dep'].'-';
$fp=fopen('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/doc/'.$filename,'w');
foreach ($listtable as $fields) {
	fputcsv($fp, $fields);
}
fclose($fp);
echo $filename
?>