<?php

$dir='./table';
$filelist=scandir($dir,1);
$tag=0;

foreach($filelist as $fl){
	if($fl=='.'||$fl=='..'||$fl=='outside'){
	}
	else{
		if(preg_match('/-/',$_POST['tablenum'])){
			$temptable=preg_split('/-/',$_POST['tablenum']);
			$targettable='';
			for($i=0;$i<(sizeof($temptable)-1);$i++){
				if($targettable==''){
					$targettable=$temptable[$i];
				}
				else{
					$targettable .= '-'.$temptable[$i];
				}
			}
		}
		else{
			$targettable=$_POST['tablenum'];
		}
		if(preg_match('/;'.$targettable.'.ini/',$fl)&&$tag==0){//2020/3/19 因為桌號改為對應方式，所以比對檔案時不用轉碼 iconv('big5','utf-8',$fl) >> $fl
			$tabdata=parse_ini_file('./table/'.$fl,true);
			foreach($tabdata as $i=>$v){
				if($v['tablestate']=="1"){
					$list[0]['comnum']=1;
				}
				else{
					if(isset($list[0]['comnum'])){
					}
					else{
						$list[0]['comnum']=0;
					}
					if(isset($list[0]['num'])){
						$list[0]['num']++;
					}
					else{
						$list[0]['num']=1;
					}
					if(isset($list[0]['lastnum'])){
					}
					else{
						$tempfl=preg_split('/;/',$fl);
						$list[0]['lastnum']=substr($tempfl[sizeof($tempfl)-1],0,-4);
					}
				}
			}
		}
		else if(preg_match('/;'.$targettable.'-\d.ini/',$fl)){//2020/3/19 因為桌號改為對應方式，所以比對檔案時不用轉碼 iconv('big5','utf-8',$fl) >> $fl
			$tag=1;
			$tabdata=parse_ini_file('./table/'.$fl,true);
			foreach($tabdata as $i=>$v){
				if($v['tablestate']=="1"){
					$list[0]['comnum']=1;
				}
				else{
					if(isset($list[0]['comnum'])){
					}
					else{
						$list[0]['comnum']=0;
					}
					if(isset($list[0]['num'])){
						$list[0]['num']++;
					}
					else{
						$list[0]['num']=1;
					}
					if(isset($list[0]['lastnum'])&&preg_match('/-/',$list[0]['lastnum'])){
					}
					else{
						$tempfl=preg_split('/;/',$fl);
						$list[0]['lastnum']=substr($tempfl[sizeof($tempfl)-1],0,-4);
					}
				}
			}
		}
		else{
		}
	}
}

if(intval($list[0]['comnum'])>0){
	echo '目前點選之桌號已併桌，無法進行拆桌動作。';
}
else{
	$tb=parse_ini_file('../database/floorspend.ini',true);
	echo '目前點選的 ';
	if(isset($tb['Tname'][$targettable])){
		echo $tb['Tname'][$targettable];
	}
	else{
		echo $targettable;
	}
	echo ' 號桌，已擁有 '.$list[0]['num'].' 張帳單，是否確認拆桌？';
	if(isset($list[0]['lastnum'])&&($list[0]['lastnum']==null||$list[0]['lastnum']=='')){
		echo '<input type="hidden" name="lastnum" value="'.$_POST['tablenum'].'">';
	}
	else{
		echo '<input type="hidden" name="lastnum" value="'.$list[0]['lastnum'].'">';//2020/3/19 因為桌號改為對應方式，所以比對檔案時不用轉碼 iconv('big5','utf-8',$list[0]['lastnum']) >> $list[0]['lastnum']
	}
}
?>