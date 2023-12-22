<?php
include_once '../../../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql="SELECT * FROM personnel WHERE state=1 ORDER BY CAST(perno AS INT) ASC,credatetime ASC";
$content=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'],'punch.db','','','','sqlite');
$sql='SELECT * FROM punchlist WHERE date BETWEEN "'.$_POST['startdate'].'" AND "'.$_POST['enddate'].'" AND state=1 ORDER BY CAST(perno AS INT) ASC,date ASC,time ASC';
$punchlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$personnel=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/personnel.ini',true);

$filename=date('YmdHis').'.csv';
$fp=fopen('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/doc/'.$filename,'x');
fclose($fp);
$listtable=array();
array_push($listtable,array('���u���d����'));

$gindex=0;
foreach($content as $c){
	array_push($listtable,array('���u�s��',iconv("UTF-8","Big5","'".$c['percard']),'���u�m�W',mb_convert_encoding($c['name'],"Big5","UTF-8")));
	$date=0;
	$ontimes=0;
	$worktime=0;
	while($gindex<sizeof($punchlist)){
		if(isset($punchlist[$gindex])&&$punchlist[$gindex]['perno']==$c['perno']){
			$start=$_POST['startdate'];
			$end=$_POST['enddate'];
			if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
				$ENDDATE=strtotime(date('Ymd'));
			}
			else{
				$ENDDATE=strtotime(date('Ymd',strtotime($end)));
			}
			
			$list=array();
			$error=array();
			$date='';
			$time='';
			for(;$gindex<sizeof($punchlist);$gindex++){
				if($punchlist[$gindex]['perno']==$c['perno']){
					if($punchlist[$gindex]['type']=='on'){
						$list[$punchlist[$gindex]['date']][$punchlist[$gindex]['time']]['on']=$punchlist[$gindex]['date'].' '.$punchlist[$gindex]['time'];
						$list[$punchlist[$gindex]['date']][$punchlist[$gindex]['time']]['realoff']='';
						$list[$punchlist[$gindex]['date']][$punchlist[$gindex]['time']]['off']='';
						$date=$punchlist[$gindex]['date'];
						$time=$punchlist[$gindex]['time'];
					}
					else if($punchlist[$gindex]['type']=='off'){
						$tempontime=preg_split('/:/',$punchlist[$gindex]['time']);
						if(intval($tempontime[1])<30){
							$tempontime[1]='00';
						}
						else{
							$tempontime[1]='30';
						}
						$temptime=implode(':',$tempontime);
						if($date!=''&&$time!=''){
							$list[$date][$time]['realoff']=$punchlist[$gindex]['date'].' '.$punchlist[$gindex]['time'];
							if(isset($personnel['basic']['computetime'])&&$personnel['basic']['computetime']=='2'){//�ɼƥH���I���D
								$list[$date][$time]['off']=$punchlist[$gindex]['date'].' '.$temptime;
							}
							else{//�ɼƥH�������D
								$list[$date][$time]['off']=$punchlist[$gindex]['date'].' '.$punchlist[$gindex]['time'];
							}
						}
						else{
							if(isset($personnel['basic']['computetime'])&&$personnel['basic']['computetime']=='2'){//�ɼƥH���I���D
								array_push($error,array('date'=>$date,'time'=>$time,'type'=>$punchlist[$gindex]['type'],'datetime'=>$punchlist[$gindex]['date'].' '.$temptime));
							}
							else{//�ɼƥH�������D
								array_push($error,array('date'=>$date,'time'=>$time,'type'=>$punchlist[$gindex]['type'],'datetime'=>$punchlist[$gindex]['date'].' '.$punchlist[$gindex]['time']));
							}
						}
						$date='';
						$time='';
					}
					else{
					}
				}
				else{
					break;
				}
			}
			array_push($listtable,array('�W�Z��','�W�Z�ɶ�','�U�Z�ɶ�','�ɼ�'));

			if($personnel['basic']['punchtype']=='1'){//�L���U�Z�d
			}
			else{//�ݥ��U�Z�d
				$date=0;
				$ontimes=0;
				$worktime=0;
				
				for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
					$week=date("N",$d);
					if(isset($list[date('Y-m-d',$d)])){
						$date++;
						$index=0;
						foreach($list[date('Y-m-d',$d)] as $l){
							$record=array();//�Ȧs�浧�O��(�Z�t�@���W�B�U�Z)
							$ontimes++;
							if($index==0){
								$datetime=preg_replace('/-/','/',substr(date('Y-m-d',$d),2));
								switch ($week) {
									case 1:
										$datetime=$datetime."(�@)";
										break;
									case 2:
										$datetime=$datetime."(�G)";
										break;
									case 3:
										$datetime=$datetime."(�T)";
										break;
									case 4:
										$datetime=$datetime."(�|)";
										break;
									case 5:
										$datetime=$datetime."(��)";
										break;
									case 6:
										$datetime=$datetime."(��)";
										break;
									case 7:
										$datetime=$datetime."(��)";
										break;
									default:
										break;
								}
								array_push($record,$datetime);
							}
							else{
								array_push($record,'');
							}
							array_push($record,preg_replace('/-/','/',substr($l['on'],2,8)).' '.substr($l['on'],11,5),preg_replace('/-/','/',substr($l['realoff'],2,8)).' '.substr($l['realoff'],11,5));

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
							array_push($listtable,$record);
						}
					}
					else{
						$record=array();//�Ȧs�浧�O��(�Z�t�@���W�B�U�Z)
						$datetime=preg_replace('/-/','/',substr(date('Y-m-d',$d),2));
						switch ($week) {
							case 1:
								$datetime=$datetime."(�@)";
								break;
							case 2:
								$datetime=$datetime."(�G)";
								break;
							case 3:
								$datetime=$datetime."(�T)";
								break;
							case 4:
								$datetime=$datetime."(�|)";
								break;
							case 5:
								$datetime=$datetime."(��)";
								break;
							case 6:
								$datetime=$datetime."(��)";
								break;
							case 7:
								$datetime=$datetime."(��)";
								break;
							default:
								break;
						}
						array_push($record,$datetime);
						array_push($listtable,$record);
					}
					//array_push($listtable,$record);
				}
			}
			array_push($listtable,array('�`�W�Z��','�`�Z��','','�`�ɼ�'));
			array_push($listtable,array($date,$ontimes,'',$worktime));
		}
		else{
			array_push($listtable,array('�`�W�Z��','�`�Z��','','�`�ɼ�'));
			array_push($listtable,array(0,0,'',0));
		}
		break;
	}
	array_push($listtable,array(''));
}

//echo $_POST['company'].'-'.$_POST['dep'].'-';
$fp=fopen('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/doc/'.$filename,'w');
foreach ($listtable as $fields) {
	fputcsv($fp, $fields);
}
fclose($fp);
echo $filename
?>