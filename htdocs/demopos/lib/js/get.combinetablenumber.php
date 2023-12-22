<?php
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machine']])){
		$invmachine=$dbmapping['map'][$_POST['machine']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$res=array();//2020/3/20 承接桌號與桌號名稱
if(file_exists('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$_POST['tablenumber'].'.ini')){
	$ini=parse_ini_file('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$_POST['tablenumber'].'.ini',true);
	$tb=parse_ini_file('../../../database/floorspend.ini',true);
	//echo $ini[$_POST['tablenumber']]['table'];

	//2020/3/20
	$res[0]=$_POST['tablenumber'];
	$res[1]='';
	if(preg_match('/,/',$ini[$_POST['tablenumber']]['table'])){
		$tablelist=preg_split('/,/',$ini[$_POST['tablenumber']]['table']);
		for($i=0;$i<sizeof($tablelist);$i++){
			if(preg_match('/-/',$tablelist[$i])){
				$temp=preg_split('/-/',$tablelist[$i]);
				if(isset($tb['Tname'][$temp[0]])){
					if($res[1]!=''){
						$res[1] .= ',';
					}
					else{
					}
					$res[1] .= $tb['Tname'][$temp[0]].'-'.$temp[1];
				}
				else{
					if($res[1]!=''){
						$res[1] .= ',';
					}
					else{
					}
					$res[1] .= $temp[0].'-'.$temp[1];
				}
			}
			else{
				if(isset($tb['Tname'][$tablelist[$i]])){
					if($res[1]!=''){
						$res[1] .= ',';
					}
					else{
					}
					$res[1] .= $tb['Tname'][$tablelist[$i]];
				}
				else{
					if($res[1]!=''){
						$res[1] .= ',';
					}
					else{
					}
					$res[1] .= $tablelist[$i];
				}
			}
		}
	}
	else{
		if(preg_match('/-/',$ini[$_POST['tablenumber']]['table'])){
			$temp=preg_split('/-/',$ini[$_POST['tablenumber']]['table']);
			if(isset($tb['Tname'][$temp[0]])){
				if($res[1]!=''){
					$res[1] .= ',';
				}
				else{
				}
				$res[1] .= $tb['Tname'][$temp[0]].'-'.$temp[1];
			}
			else{
				if($res[1]!=''){
					$res[1] .= ',';
				}
				else{
				}
				$res[1] .= $temp[0].'-'.$temp[1];
			}
		}
		else{
			if(isset($tb['Tname'][$ini[$_POST['tablenumber']]['table']])){
				if($res[1]!=''){
					$res[1] .= ',';
				}
				else{
				}
				$res[1] .= $tb['Tname'][$ini[$_POST['tablenumber']]['table']];
			}
			else{
				if($res[1]!=''){
					$res[1] .= ',';
				}
				else{
				}
				$res[1] .= $ini[$_POST['tablenumber']]['table'];
			}
		}
	}
	echo json_encode($res);
}
else{
	$tb=parse_ini_file('../../../database/floorspend.ini',true);
	//echo $_POST['tablenumber'];

	//2020/3/20
	$res[0]=$_POST['tablenumber'];
	$res[1]='';
	if(isset($tb['Tname'][$_POST['tablenumber']])){
		$res[1] .= $tb['Tname'][$_POST['tablenumber']];
	}
	else{
		$res[1] .= $_POST['tablenumber'];
	}
	echo json_encode($res);
}
?>