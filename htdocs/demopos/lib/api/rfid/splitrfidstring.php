<?php
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);

if(isset($initsetting['db']['dbfile'])&&$initsetting['db']['dbfile']!=''){
	$pythonpath=substr($initsetting['db']['dbfile'],0,2);
}
else{
	$pythonpath='C:';
}

if(isset($initsetting['rfid']['timeout'])){
	$timeout=$initsetting['rfid']['timeout'];
}
else{
	$timeout='0.5';
}
if(isset($initsetting['rfid']['checktime'])){
	$checktime=$initsetting['rfid']['checktime'];
}
else{
	$checktime='5';
}
if(isset($initsetting['rfid']['com'])){
	$com=$initsetting['rfid']['com'];
}
else{
	$com='5';
}

$combinearray=array();
$matcharray=array();
$index=1;
if(file_exists($pythonpath.'//pos/rfid.exe')){
	$string=exec($pythonpath.'//pos/rfid.exe --timeout='.$timeout.' --checktime='.$checktime.' --com='.$com);

	if($string!=''){
		preg_match_all('/\\\\n[A-Z0-9\,]+\\\\r\\\\n/',$string,$tokenarray);//切出所有字串

		if(isset($_GET['debug'])){
			echo '<div style="width:100%;word-break: break-all;border:1px solid #ff0000;margin:10px 0;">讀取出的原始字串(逾時時間=>'.$timeout.'秒；讀取次數=>'.$checktime.'次；連接埠=>COM'.$com.')：<br>'.$string.'</div>';
			echo '<div style="width:100%;word-break:breal-all;border:1px solid #ff0000;margin:10px 0;">切割後的字串陣列：<br>'.print_r($tokenarray[0],true).'</div>';
		}
		else{
		}

		if(isset($_GET['debug'])){
			echo '<div style="width:100%;border:1px solid #ff0000;margin:10px 0;">過濾重複字串，併截出規定字串：<br>';
		}
		else{
		}

		for($j=0;$j<sizeof($tokenarray[0]);$j++){
			$temp=preg_replace('/\\\\r/','',preg_replace('/\\\\n/','',$tokenarray[0][$j]));//過濾出真實字串
			if(strlen($temp)<=5){
				//continue;
			}
			else{						
				$matchtoken=substr($temp,strpos($temp,',')-8,8);//2020/4/13 intval(substr($temp,strpos($temp,',')-8,8))
				$token=substr($temp,strpos($temp,',')-8,4);//2020/4/13 intval(substr($temp,strpos($temp,',')-8,4))
				//if(!in_array($matchtoken,$matcharray)){//2020/5/11 因為卡號與預先設想不同，卡號完全一模一樣，因此暫時不判斷是否為同張卡片

					if(isset($_GET['debug'])){
						echo $index.'='.substr($temp,strpos($temp,',')-8,8).'<br>';
						$index++;
					}
					else{
					}

					array_push($matcharray,$matchtoken);
					array_push($combinearray,$token);
				/*}
				else{
				}*/
			}
		}

		if(isset($_GET['debug'])){
			echo '</div>';
		}
		else{
		}
	}
	else{
	}
}
else{
}

if(isset($_GET['debug'])){
	print_r($combinearray);
}
else{
	echo json_encode($combinearray);
}
?>