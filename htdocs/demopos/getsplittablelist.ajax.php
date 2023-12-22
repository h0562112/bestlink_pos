<?php
//include_once '../tool/dbTool.inc.php';
include_once '../tool/quicksort.php';
$dir=('./table');
$filelist=scandir($dir,1);
$maps=0;
if(strstr($_POST['tabnum'],'-')){
	$temp=preg_split('/-/',$_POST['tabnum']);
	for($i=0;$i<sizeof($temp)-1;$i++){
		if($i==0){
			$_POST['tabnum']=$temp[$i];
		}
		else{
			$_POST['tabnum']=$_POST['tabnum'].'-'.$temp[$i];
		}
	}
}
else{
}
$floorspend=parse_ini_file('../database/floorspend.ini',true);
//print_r($floorspend);
foreach($filelist as $fl){
	if(!preg_match('/;'.$_POST['tabnum'].'.ini/',$fl)&&!preg_match('/;'.$_POST['tabnum'].'-\d.ini/',$fl)&&isset($result)){//2020/3/16 iconv('big5','utf-8',$fl) >> $fl ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
		break;
	}
	else if(preg_match('/;'.$_POST['tabnum'].'.ini/',$fl)||preg_match('/;'.$_POST['tabnum'].'-\d.ini/',$fl)){//2020/3/16  iconv('big5','utf-8',$fl) >> $fl ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
		$tabledata=parse_ini_file('./table/'.$fl,true);
		foreach($tabledata as $tdname=>$td){
			$result[$td['consecnumber']]['bizdate']=$_POST['bizdate'];
			$result[$td['consecnumber']]['state']=$td['state'];
			$result[$td['consecnumber']]['tablenumber']=$tdname;

			//2020/3/17 因為原本桌號改為對應方式，需要另外儲存桌號
			if(preg_match('/-/',$tdname)){
				$temp=preg_split('/-/',$tdname);
				$result[$td['consecnumber']]['tablename']=$floorspend['Tname'][$temp[0]].'-'.$temp[1];
			}
			else{
				$result[$td['consecnumber']]['tablename']=$floorspend['Tname'][$tdname];
			}
		}
	}
}
if(!isset($result)){
	$result['time']=date('H:i:s');
}
else{
	$result=quick_sort($result,'tablenumber');
	foreach($result as $t){
		$lasttabnum=$t['tablenumber'];
	}
	$result['lasttablenumber']=$lasttabnum;
}
echo json_encode($result);
?>