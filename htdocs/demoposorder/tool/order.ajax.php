<?php
include '../lib/dbTool.inc.php';
$conn=sqlconnect("../../database","menu.db","","","",'sqlite');
$sql='SELECT taste FROM itemsdata WHERE inumber="'.$_POST['inumber'].'"';
$taste=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$temp=preg_split('/,/',$taste[0]['taste']);
//if($_POST['dep']=='ttt001'){
	if(file_exists('../../database/'.$_POST['company'].'-taste.ini')){
		$tastemap=parse_ini_file('../../database/'.$_POST['company'].'-taste.ini',true);
	}
	else{
		$tastemap=0;
	}
	if(file_exists('../../database/'.$_POST['company'].'-menu.ini')){
		$menu=parse_ini_file('../../database/'.$_POST['company'].'-menu.ini',true);
	}
	else{
		$menu=0;
	}
//}
/*else{
	if(file_exists('../../database/'.$_POST['dep'].'-menu.ini')){
		$tastemap=parse_ini_file('../../database/'.$_POST['dep'].'-menu.ini',true);
	}
	else{
		$tastemap=0;
	}
	if(file_exists('../../database/'.$_POST['dep'].'-menu.ini')){
		$menu=parse_ini_file('../../database/'.$_POST['dep'].'-menu.ini',true);
	}
	else{
		$menu=0;
	}
}*/
if($menu==0){
}
else{
	$value[0]['img']=(isset($menu[$_POST['inumber']]['image']))?($menu[$_POST['inumber']]['image']):('error');
	$value[0]['itemname']=(isset($menu[$_POST['inumber']]['name1']))?($menu[$_POST['inumber']]['name1']):('error');
}
if($tastemap==0){
}
else{
	if($temp[0]==''){
	}
	else{
		for($i=0;$i<sizeof($temp);$i++){
			$value[$i]['taste']=$temp[$i];
			$value[$i]['type']=(isset($tastemap[$value[$i]['taste']]['type']))?($tastemap[$value[$i]['taste']]['type']):('error');
			$value[$i]['name']=(isset($tastemap[$value[$i]['taste']]['name1']))?($tastemap[$value[$i]['taste']]['name1']):('error');
			$value[$i]['money']=(isset($tastemap[$value[$i]['taste']]['money']))?($tastemap[$value[$i]['taste']]['money']):('error');
		}
	}
}
echo json_encode($value);
?>