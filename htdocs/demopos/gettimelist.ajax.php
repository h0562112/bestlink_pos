<?php
include_once '../tool/dbTool.inc.php';
$init=parse_ini_file('../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}

if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}

$dir='./table';
$filelist=scandir($dir);
if(sizeof($filelist)>3){
	$date1=date_create(date('YmdHi'));
	$tabarray=array();
	echo '<table style="width:calc(100% - 20px);font-size:20px;">
			<tr>
				<td style="width:25%;text-align:center;">桌號</td>
				<td style="width:25%;text-align:center;">單號</td>
				<td style="width:25%;text-align:center;">開單時間</td>
				<td style="width:25%;text-align:center;">剩餘時間</td>
			</tr>';
	foreach($filelist as $fl){
		if(strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';')){
			$tabnum=preg_split('/;/',$fl);
			$filedata=parse_ini_file('./table/'.$fl,true);
			$date2=date_create(date('YmdHi',strtotime($filedata[substr($tabnum[2],0,-4)]['createdatetime'].' +'.$init['init']['maxtime'].' minute')));
			$diff=date_diff($date1,$date2);
			if(((floatval($diff->format("%R%d"))*1440)+(floatval($diff->format("%R%H"))*60)+floatval($diff->format("%R%i")))<=floatval($init['init']['hinttime'])&&!in_array($filedata[substr($tabnum[2],0,-4)]['table'],$tabarray)){
				echo '<tr ';
				if(((floatval($diff->format("%R%d"))*1440)+(floatval($diff->format("%R%H"))*60)+floatval($diff->format("%R%i")))<=floatval($init['init']['sechinttime'])){
					echo 'style="font-wright:bold;color:#ff0000;"';
				}
				else{
				}
				echo '>';
				array_push($tabarray,$filedata[substr($tabnum[2],0,-4)]['table']);
				echo '<td style="width:25%;text-align:right;">'.$filedata[substr($tabnum[2],0,-4)]['table'].'</td>';
				echo '<td style="width:25%;text-align:right;">'.intval($filedata[substr($tabnum[2],0,-4)]['consecnumber']).'</td>';
				echo '<td style="width:25%;text-align:right;">'.date('y/m/d',strtotime($filedata[substr($tabnum[2],0,-4)]['createdatetime'])).'<br>'.date('H:i:s',strtotime($filedata[substr($tabnum[2],0,-4)]['createdatetime'])).'<input type="hidden" value="'.date('YmdHis',strtotime($filedata[substr($tabnum[2],0,-4)]['createdatetime'].' +'.$init['init']['maxtime'].' minute')).'"</td>';
				echo '<td style="width:25%;text-align:right;">';
				if(((floatval($diff->format("%R%d"))*1440)+(floatval($diff->format("%R%H"))*60)+floatval($diff->format("%R%i")))<=0){
					echo '0';
				}
				else{
					echo ((floatval($diff->format("%R%d"))*1440)+(floatval($diff->format("%R%H"))*60)+floatval($diff->format("%R%i")));
				}
				echo '分</td>';
				echo '</tr>';
			}
			else{
			}
		}
		else{
		}
	}
}
else{
}
?>