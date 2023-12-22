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
$init=parse_ini_file('../../../database/initsetting.ini',true);
$conn=sqlconnect("../../../database","menu.db","","","","sqlite");
if($init['init']['menutype']==1){
	$sql='SELECT * FROM itemsdata ORDER BY fronttype,frontsq,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(inumber))), "\'", ""), "0", "0")||inumber';
}
else{
	$sql='SELECT DISTINCT fronttype as front FROM itemsdata ORDER BY replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(fronttype))), "\'", ""), "0", "0")||fronttype';
	//$sql='SELECT DISTINCT fronttype as front FROM itemsdata ORDER BY frontsq';
}
$front=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if($init['init']['menutype']==1){
	$itemname=parse_ini_file('../../../database/'.$_POST['company'].'-menu.ini',true);
}
else{
}
if(isset($_POST['company'])&&strlen($_POST['company'])>0){
	$items=parse_ini_file('../../../database/'.$_POST['company'].'-front.ini',true);
}
else{
	$items=parse_ini_file('../../../database/front.ini',true);
}
$sortitems=quicksort($items,'seq');
$data=array();
$j=0;
if($init['init']['menutype']==1){
	for($i=0;$i<sizeof($front);$i++){
		if(substr($front[$i]['fronttype'],0,1)=='g'){
			continue;
		}
		else{
			if($items[$front[$i]['fronttype']]['state']=='0'&&(isset($items[$front[$i]['fronttype']]['posvisible'])&&$items[$front[$i]['fronttype']]['posvisible']=='0')){
			}
			else{
				$data[$j]['no']=$front[$i]['inumber'];
				$data[$j]['typeno']=$front[$i]['fronttype'];
				$data[$j]['name1']=$itemname[$front[$i]['inumber']]['name1'];
				$data[$j]['size1']=$itemname[$front[$i]['inumber']]['size1'];
				$data[$j]['color1']=$itemname[$front[$i]['inumber']]['color1'];
				$data[$j]['bold1']=$itemname[$front[$i]['inumber']]['bold1'];
				$data[$j]['name2']=$itemname[$front[$i]['inumber']]['name2'];
				$data[$j]['size2']=$itemname[$front[$i]['inumber']]['size2'];
				$data[$j]['color2']=$itemname[$front[$i]['inumber']]['color2'];
				$data[$j]['bold2']=$itemname[$front[$i]['inumber']]['bold2'];
				$data[$j]['bgcolor']=$itemname[$front[$i]['inumber']]['bgcolor'];
				$j++;
			}
		}
	}
}
else{
	foreach($sortitems as $sv){
		if($sv['state']=='1'&&(!isset($sv['posvisible'])||$sv['posvisible']=='1')){
			foreach($front as $fv){
				if($sv['typeno']==$fv['front']){
					$data[$j]['front']=$fv['front'];
					$data[$j]['name1']=$sv['name1'];
					$data[$j]['size1']=$sv['size1'];
					$data[$j]['color1']=$sv['color1'];
					$data[$j]['bold1']=$sv['bold1'];
					$data[$j]['name2']=$sv['name2'];
					$data[$j]['size2']=$sv['size2'];
					$data[$j]['color2']=$sv['color2'];
					$data[$j]['bold2']=$sv['bold2'];
					$data[$j]['bgcolor']=$sv['bgcolor'];
					$j++;
					break;
				}
				else{
				}
			}
		}
		else{
		}
	}
	/*for($i=0;$i<sizeof($front);$i++){
		if(substr($front[$i]['front'],0,1)=='g'){
			continue;
		}
		else{
			if($items[$front[$i]['front']]['state']=='0'){
			}
			else{
				$data[$j]['front']=$front[$i]['front'];
				$data[$j]['name1']=$items[$front[$i]['front']]['name1'];
				$data[$j]['size1']=$items[$front[$i]['front']]['size1'];
				$data[$j]['color1']=$items[$front[$i]['front']]['color1'];
				$data[$j]['bold1']=$items[$front[$i]['front']]['bold1'];
				$data[$j]['name2']=$items[$front[$i]['front']]['name2'];
				$data[$j]['size2']=$items[$front[$i]['front']]['size2'];
				$data[$j]['color2']=$items[$front[$i]['front']]['color2'];
				$data[$j]['bold2']=$items[$front[$i]['front']]['bold2'];
				$data[$j]['bgcolor']=$items[$front[$i]['front']]['bgcolor'];
				$j++;
			}
		}
	}*/
}
$frontname='';
echo json_encode($data);
?>