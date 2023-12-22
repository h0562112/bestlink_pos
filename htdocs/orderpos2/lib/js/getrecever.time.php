<?php
date_default_timezone_set('Asia/Taipei');
$res=['option'=>'','nowtime'=>'','time'=>''];
$temp='';
if(file_exists('../../../management/menudata/'.$_POST['story'].'/'.$_POST['dep'].'/setup.ini')){
	$setup=parse_ini_file('../../../management/menudata/'.$_POST['story'].'/'.$_POST['dep'].'/setup.ini',true);
	if(isset($setup['basic']['start'])||isset($setup['basic']['end'])){
		if(isset($setup['basic']['start'])){
			$s=sizeof($setup['basic']['start']);
		}
		else{
			$s=0;
		}
		if(isset($setup['basic']['end'])){
			$e=sizeof($setup['basic']['end']);
		}
		else{
			$e=0;
		}
		$n = $s>$e ? $s : $e;
		for($i=0;$i<$n;$i++){
			if(isset($setup['basic']['start'][$i])&&(!isset($temp['end'][$i-1])||(isset($temp['end'][$i-1])&&intval($temp['end'][$i-1]<=intval($setup['basic']['start'][$i]))))){
				$temp['start'][]=$setup['basic']['start'][$i];
			}
			else{
				if($n==1){
					$temp['start'][]=0;
				}
				else if($temp['end'][$i-1]){
					$temp['start'][]=intval($temp['end'][$i-1]);
				}
				else{
					$temp['start'][]=9;
				}
			}
			if(isset($setup['basic']['end'][$i])&&intval($setup['basic']['end'][$i])>=intval($temp['start'][$i])){
				$temp['end'][]=$setup['basic']['end'][$i];
			}
			else{
				$temp['end'][]=24;
			}
		}
	}
	else{
		$temp['start'][]=9;
		$temp['end'][]=23;
	}
}
else{
	$temp['start'][]=9;
	$temp['end'][]=23;
}
for($i=0;$i<sizeof($temp['start']);$i++){
	if(intval(date('H'))>=intval($temp['start'][$i])&&intval(date('H'))<intval($temp['end'][$i])){
		if(intval(date('H'))+1<24){
			$temp['nowstart'][]=intval(date('H'))+1;
		}
		else{
			$temp['nowstart'][]=24;
		}
		$temp['nowend'][]=$temp['end'][$i];
	}
	else if(intval(date('H'))<intval($temp['start'][$i])){
		$temp['nowstart'][]=$temp['start'][$i];
		$temp['nowend'][]=$temp['end'][$i];
	}
	else{
	}
}
for($i=0;$i<7;$i++){
	$res['option'] .= '<option value="'.date('Y-m-d',strtotime(date('Y-m-d').' +'.$i.' day')).'"';
	if($i==0){
		$res['option'] .= ' selected';
	}
	else{
	}
	$res['option'] .= '>'.date('n月j日',strtotime(date('Y-m-d').' +'.$i.' day')).'</option>';
}
for($i=0;$i<sizeof($temp['nowstart']);$i++){
	for($t=$temp['nowstart'][$i];$t<$temp['nowend'][$i];$t++){
		$res['nowtime'] .= '<option value="'.str_pad($t, 2, "0", STR_PAD_LEFT).':00:00">'.str_pad($t, 2, "0", STR_PAD_LEFT).':00-'.str_pad((intval($t)+1), 2, "0", STR_PAD_LEFT).':00</option>';
	}
}
for($i=0;$i<sizeof($temp['start']);$i++){
	for($t=$temp['start'][$i];$t<$temp['end'][$i];$t++){
		$res['time'] .= '<option value="'.str_pad($t, 2, "0", STR_PAD_LEFT).':00:00">'.str_pad($t, 2, "0", STR_PAD_LEFT).':00-'.str_pad((intval($t)+1), 2, "0", STR_PAD_LEFT).':00</option>';
	}
}
echo json_encode($res);
?>