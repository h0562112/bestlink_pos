<?php
include '../../../tool/dbTool.inc.php';
function quicksort($origArray,$type) {//快速排序//for最低價、最高價
	if (sizeof($origArray) == 1) { 
		return $origArray;
	}
	else if(sizeof($origArray) == 0){
		return 'null';
	}
	else {
		$left = array();
		$right = array();
		$newArray = array();
		$pivot = array_pop($origArray);
		$length = sizeof($origArray);
		for ($i = 0; $i < $length; $i++) {
			if(isset($origArray[$i][$type])&&isset($pivot[$type])){
				if (floatval($origArray[$i][$type]) <= floatval($pivot[$type])) {
					array_push($left,$origArray[$i]);
				} else {
					array_push($right,$origArray[$i]);
				}
			}
			else if(isset($origArray[$i][$type])){
				array_push($right,$origArray[$i]);
			}
			else{
				array_push($left,$origArray[$i]);
			}
		}
		if(sizeof($left)==0){
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort($right,$type);
				$newArray=array_merge(array($pivot),$tempright);
			}
		}
		else{
			$templeft=quicksort($left,$type);
			$n=sizeof($templeft);
			$start=$n;
			$newArray=array_merge($templeft,array($pivot));
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort($right,$type);
				$newArray=array_merge($newArray,$tempright);
			}
		}
		return $newArray;
	}
}
if(isset($_POST['row'])){
	$row=$_POST['row'];
}
else{
}
if(isset($_POST['col'])){
	$col=$_POST['col'];
}
else{
}
$conn=sqlconnect("../../../database","menu.db","","","","sqlite");
$sql='SELECT DISTINCT fronttype as front FROM itemsdata WHERE state="1" OR state IS NULL ORDER BY replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(fronttype))), "\'", ""), "0", "0")||fronttype';
$front=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$frontname=parse_ini_file('../../../database/'.$_POST['company'].'-front.ini',true);
$sortfront=quicksort($frontname,'seq');

$temp=array_column($front,'front');
//print_r($temp);
foreach($sortfront as $sindex=>$sf){
	if($sf['state']=='1'&&in_array($sf['typeno'],$temp)&&(!isset($sf['subtype'])||$sf['subtype']=='0')&&(!isset($sf['posvisible'])||$sf['posvisible']=='1')){//2021/8/25 增加過濾"顯示於POS"類別//2020/3/27 增加過濾"套餐選項"類別
	}
	else{
		unset($sortfront[$sindex]);
	}
}
//print_r($sortfront);
$sortfront=array_values($sortfront);
//print_r($sortfront);
$temp=$sortfront;

if(isset($_POST['next'])){//換頁按鈕
	if($_POST['next']!=''){//可能尚有產品未顯示
		$tempdata=array_column($sortfront,'typeno');
		//echo PHP_EOL.'tempdata=';
		//print_r($tempdata);
		if(in_array($_POST['next'],$tempdata)){//取得目前顯示最後產品的位置
			if(isset($tempdata[array_search($_POST['next'],$tempdata)+1])){//檢查後面是否還有產品
				$i=array_search($_POST['next'],$tempdata)+1;
				//echo PHP_EOL.'next='.$_POST['next'];
			}
			else{
				$i=0;
			}
		}
		else{
			$i=0;
		}
	}
	else{//顯示迴圈到尾，從頭開始顯示
		$i=0;
	}
}
else{
	$i=0;
}
//echo PHP_EOL.'i='.$i;


/*if(isset($row)&&isset($col)){
	if(){
	}
	else if(){
	}
	else{
	}
	$page=($i+1).'/'.sizeof($sortfront);
}
else{*/
	$page=($i+1).'/'.sizeof($sortfront);
//}


//echo PHP_EOL.'sortfront=';
//print_r($sortfront);
//echo PHP_EOL.'size(sortfront)='.sizeof($sortfront);
for($j=0;$j<sizeof($temp);$j++){
	if(intval($j)<intval($i)){
		//echo PHP_EOL.'delete='.$j;
		unset($sortfront[$j]);
	}
	else{
		//echo PHP_EOL.$i.'=='.$j.PHP_EOL;
		break;
	}
}
//echo PHP_EOL.'sortfront=';
//print_r($sortfront);
$sortfront=array_values($sortfront);
$sortfront['page']=$page;
echo json_encode($sortfront);
?>