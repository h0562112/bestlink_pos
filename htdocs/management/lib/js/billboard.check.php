<?php
if(file_exists('../../billboard/datalist.ini')){
	date_default_timezone_set('Asia/Taipei');
	$nowdate=date('Y-m-d');
	$datalist=parse_ini_file('../../billboard/datalist.ini',true);
	//print_r($datalist);
	$count=0;
	if(sizeof($datalist)>0){
		foreach($datalist as $index=>$data){
			if(strtotime($data['start'])<=strtotime($nowdate)&&strtotime($data['end'])>=strtotime($nowdate)){
				$count++;
				echo '['.$data['date'].']<br>';
				foreach($data as $i=>$v){
					if(is_numeric($i)){
						echo '&nbsp;&nbsp;'.$i.'. '.$v.'<br>';
					}
					else{
						//echo is_numeric($i);
					}
				}
				//echo '2';
			}
			else{
				//echo strtotime(date('Ymd',$data['start']))<=strtotime(date('Ymd'));
				//echo strtotime(date('Ymd',$data['end']))>=strtotime(date('Ymd'));
				//echo '1';
			}
			//echo '3';
		}
		if($count==0){
			echo 'empty';
		}
		else{
		}
	}
	else{
		echo 'empty';
	}
}
else{
	echo 'empty';
}
?>