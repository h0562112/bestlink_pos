<?php
include_once '../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($initsetting['init']['settime']);

if(file_exists('../../syspram/interface-'.$initsetting['init']['firlan'].'.ini')){
	$interface=parse_ini_file('../../syspram/interface-'.$initsetting['init']['firlan'].'.ini',true);
}
else if(file_exists('../../syspram/interface-TW.ini')){
	$interface=parse_ini_file('../../syspram/interface-TW.ini',true);
}
else{
	$interface=parse_ini_file('../../syspram/interface-1.ini',true);
}
if(file_exists('../../../database/personnel.ini')){
	$personnel=parse_ini_file('../../../database/personnel.ini',true);
}
else{
	//$personnel=parse_ini_file('../../../database/personnel.ini',true);
}
$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
$sql='SELECT perno,percard,name FROM personnel';
$personlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../database/person','punch.db','','','','sqlite');
$sql='SELECT * FROM punchlist WHERE date BETWEEN "'.$_POST['start'].'" AND "'.$_POST['end'].'" AND state=1';
if($_POST['perno']=='all'){
}
else{
	$sql.=' AND perno="'.$_POST['perno'].'"';
}
$sql.=' ORDER BY perno ASC,date ASC,time ASC,firstdatetime ASC,type DESC';
//echo $sql;
$punchlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$person=array();
for($i=0;$i<sizeof($personlist);$i++){
	$person[$personlist[$i]['perno']]['percard']=$personlist[$i]['percard'];
	$person[$personlist[$i]['perno']]['name']=$personlist[$i]['name'];
}
$punch=array();
$error=array();
$date='';
$time='';
$usercode='';
for($i=0;$i<sizeof($punchlist);$i++){
	if($usercode!=''){
	}
	else{
		$usercode=$punchlist[$i]['perno'];
	}
	if($punchlist[$i]['type']=='on'){
		$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['on'][]=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
		$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['realoff'][]='';
		$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['off'][]='';
		$date=$punchlist[$i]['date'];
		$time=$punchlist[$i]['time'];
	}
	else if($punchlist[$i]['type']=='off'){
		$tempontime=preg_split('/:/',$punchlist[$i]['time']);
		if(intval($tempontime[1])<30){
			$tempontime[1]='00';
		}
		else{
			$tempontime[1]='30';
		}
		$temptime=implode(':',$tempontime);
		if($date!=''&&$time!=''&&$usercode==$punchlist[$i]['perno']){
			$punch[$punchlist[$i]['perno']][$date][$time]['realoff'][sizeof($punch[$punchlist[$i]['perno']][$date][$time]['realoff'])-1]=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
			if(isset($personnel['basic']['computetime'])&&$personnel['basic']['computetime']=='2'){//時數以整點為主
				$punch[$punchlist[$i]['perno']][$date][$time]['off'][sizeof($punch[$punchlist[$i]['perno']][$date][$time]['off'])-1]=$punchlist[$i]['date'].' '.$temptime;
			}
			else{//時數以分鐘為主
				$punch[$punchlist[$i]['perno']][$date][$time]['off'][sizeof($punch[$punchlist[$i]['perno']][$date][$time]['off'])-1]=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
			}
		}
		else{
			$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['on'][]='';
			$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['realoff'][]=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
			if(isset($personnel['basic']['computetime'])&&$personnel['basic']['computetime']=='2'){//時數以整點為主
				$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['off'][]=$punchlist[$i]['date'].' '.$temptime;
			}
			else{//時數以分鐘為主
				$punch[$punchlist[$i]['perno']][$punchlist[$i]['date']][$punchlist[$i]['time']]['off'][]=$punchlist[$i]['date'].' '.$punchlist[$i]['time'];
			}
			//array_push($error,array('date'=>$date,'time'=>$time,'type'=>$punchlist[$i]['type'],'datetime'=>$punchlist[$i]['date'].' '.$punchlist[$i]['time']));
		}
		$date='';
		$time='';
	}
	else{
	}
	if($usercode!=''){
		$usercode=$punchlist[$i]['perno'];
	}
	else{
	}
}
echo '<div style="padding:5px;background-color:#ffffff;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;">';
echo '<div>';
if(isset($interface['name']['editpunchrestitle'])){
	echo $interface['name']['editpunchrestitle'];
}
else{
	echo '查詢日期';
}
echo '</div>';
echo '<div style="width:100%;padding-left:80px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;">'.preg_replace('/-/','/',$_POST['start']).'~'.preg_replace('/-/','/',$_POST['end']).'</div>';
if(strtotime($_POST['end'])>=strtotime(date('Y-m-d'))){
	$enddate=date('Y-m-d');
}
else{
	$enddate=$_POST['end'];
}
//print_r($punch);
foreach($punch as $perno=>$data){
	$date=0;
	$ontimes=0;
	$worktime=0;
	$perpundata='';
	$perpuntitle='<div style="border-top:1px solid #000000;padding-top:10px;">'.$person[$perno]['percard'].$person[$perno]['name'].'</div>';
	for($d=strtotime($_POST['start']);$d<=strtotime($enddate);$d=strtotime(date('Y-m-d',$d).' +1 day')){
		$perpundata.='<div ';
		if(date('N',$d)==6||date('N',$d)==7){
			$perpundata.='style="color:#ff0000;"';
		}
		else{
		}
		$perpundata.= '>'.substr(date('Y/m/d',$d),2);
		switch(date('N',$d)){
			case 1:
				if(isset($interface['name']['editpunchmon'])){
					$perpundata.= '('.$interface['name']['editpunchmon'].')';
				}
				else{
					$perpundata.= '(一)';
				}
				break;
			case 2:
				if(isset($interface['name']['editpunchtue'])){
					$perpundata.= '('.$interface['name']['editpunchtue'].')';
				}
				else{
					$perpundata.= '(二)';
				}
				break;
			case 3:
				if(isset($interface['name']['editpunchwed'])){
					$perpundata.= '('.$interface['name']['editpunchwed'].')';
				}
				else{
					$perpundata.= '(三)';
				}
				break;
			case 4:
				if(isset($interface['name']['editpunchthu'])){
					$perpundata.= '('.$interface['name']['editpunchthu'].')';
				}
				else{
					$perpundata.= '(四)';
				}
				break;
			case 5:
				if(isset($interface['name']['editpunchfri'])){
					$perpundata.= '('.$interface['name']['editpunchfri'].')';
				}
				else{
					$perpundata.= '(五)';
				}
				break;
			case 6:
				if(isset($interface['name']['editpunchsat'])){
					$perpundata.= '('.$interface['name']['editpunchsat'].')';
				}
				else{
					$perpundata.= '(六)';
				}
				break;
			case 7:
				if(isset($interface['name']['editpunchsun'])){
					$perpundata.= '('.$interface['name']['editpunchsun'].')';
				}
				else{
					$perpundata.= '(日)';
				}
				break;
			default:
				break;
		}
		$perpundata.= '</div>';
		if(isset($data[date('Y-m-d',$d)])){
			$date++;
			foreach($data[date('Y-m-d',$d)] as $time=>$l){
				for($index=0;$index<sizeof($l['on']);$index++){
					$ontimes++;
					$perpundata.='<div style="width:100%;padding-left:80px;margin:10px 0;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;overflow:hidden;">';
					$perpundata.='<div class="punchlabel" style="width:calc(100% / 3);min-height:1px;float:left;text-align:center;">'.preg_replace('/ /','<br>',preg_replace('/-/','/',substr($l['on'][$index],2,strlen($l['on'][$index])-5)));
					$perpundata.='<input type="hidden" id="perno" value="'.$perno.'"><input type="hidden" id="type" value="on">';
					if($l['on'][$index]!=''){
						$perpundata.='<input type="hidden" id="date" value="'.date('Y-m-d',$d).'">';
						$perpundata.='<input type="hidden" id="time" value="'.$time.'">';
					}
					else{
						$perpundata.='<input type="hidden" id="date" value="">';
						$perpundata.='<input type="hidden" id="time" value="">';
					}
					$perpundata.='</div>';
					$perpundata.='<div class="punchlabel" style="width:calc(100% / 3);min-height:1px;float:left;text-align:center;">'.preg_replace('/ /','<br>',preg_replace('/-/','/',substr($l['realoff'][$index],2,strlen($l['realoff'][$index])-5)));
					$perpundata.='<input type="hidden" id="perno" value="'.$perno.'"><input type="hidden" id="type" value="off">';
					if($l['realoff'][$index]!=''){
						$temp=preg_split('/ /',$l['realoff'][$index]);
						$perpundata.='<input type="hidden" id="date" value="'.$temp[0].'">';
						$perpundata.='<input type="hidden" id="time" value="'.$temp[1].'">';
					}
					else{
						$perpundata.='<input type="hidden" id="date" value="">';
						$perpundata.='<input type="hidden" id="time" value="">';
					}
					$perpundata.='</div>';
					$perpundata.='<div style="width:calc(100% / 3);min-height:1px;float:left;text-align:center;">';
					if($l['on'][$index]!=''&&$l['off'][$index]!=''){
						$diff=date_diff(date_create($l['off'][$index]),date_create($l['on'][$index]));
						$temp=preg_split('/:/',$diff->format('%d:%h:%i'));
						//print_r($dt);
						if(intval(intval($temp[2])/30)){
							$dt=intval($temp[0])*24+intval($temp[1])+(intval(intval(intval($temp[2])/30))/2);
						}
						else{
							$dt=intval($temp[0])*24+intval($temp[1]);
						}
						$perpundata.=$dt;
						$worktime=floatval($worktime)+floatval($dt);
					}
					else{
					}
					$perpundata.='</div>';
					$perpundata.='</div>';
				}
			}
		}
		else{
		}
	}
	echo $perpuntitle.'<div style="overflow:hidden;"><div style="width:calc(100% / 3);padding:5px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;float:left;">';
	if(isset($interface['name']['editpunchreslabel1'])){
		echo $interface['name']['editpunchreslabel1'];
	}
	else{
		echo '總上班日';
	}
	echo $date;
	echo '</div><div style="width:calc(100% / 3);padding:5px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;float:left;">';
	if(isset($interface['name']['editpunchreslabel2'])){
		echo $interface['name']['editpunchreslabel2'];
	}
	else{
		echo '總班次';
	}
	echo $ontimes;
	echo '</div><div style="width:calc(100% / 3);padding:5px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;float:left;">';
	if(isset($interface['name']['editpunchreslabel3'])){
		echo $interface['name']['editpunchreslabel3'];
	}
	else{
		echo '總時數';
	}
	echo $worktime;
	echo '</div></div>';
	echo '<div style="overflow:hidden;background-color: #EFEBDE;"><div style="width:80px;float:left;text-align:center;">';
	if(isset($interface['name']['editpunchreslabel4'])){
		echo $interface['name']['editpunchreslabel4'];
	}
	else{
		echo '上班日';
	}
	echo '</div><div style="width:calc((100% - 80px) / 3);float:left;text-align:center;">';
	if(isset($interface['name']['editpunchreslabel5'])){
		echo $interface['name']['editpunchreslabel5'];
	}
	else{
		echo '上班時間';
	}
	echo '</div><div style="width:calc((100% - 80px) / 3);float:left;text-align:center;">';
	if(isset($interface['name']['editpunchreslabel6'])){
		echo $interface['name']['editpunchreslabel6'];
	}
	else{
		echo '下班時間';
	}
	echo '</div><div style="width:calc((100% - 80px) / 3);float:left;text-align:center;">';
	if(isset($interface['name']['editpunchreslabel7'])){
		echo $interface['name']['editpunchreslabel7'];
	}
	else{
		echo '時數';
	}
	echo '</div></div>';
	echo $perpundata;
}
echo '</div>';
?>