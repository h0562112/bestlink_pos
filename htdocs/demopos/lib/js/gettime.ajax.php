<?php
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
$interface=parse_ini_file('../../syspram/interface-'.$init['init']['firlan'].'.ini',true);
date_default_timezone_set($init['init']['settime']);
$res=array();
$res[0]=date('Y/m/d/');
switch(date('N')){
	case 1:
		if(isset($interface['name']['mon'])){
			$res[0] .= $interface['name']['mon'].' ';
		}
		else{
			$res[0] .= '週一 ';
		}
		break;
	case 2:
		if(isset($interface['name']['tue'])){
			$res[0] .= $interface['name']['tue'].' ';
		}
		else{
			$res[0] .= '週二 ';
		}
		break;
	case 3:
		if(isset($interface['name']['wed'])){
			$res[0] .= $interface['name']['wed'].' ';
		}
		else{
			$res[0] .= '週三 ';
		}
		break;
	case 4:
		if(isset($interface['name']['thu'])){
			$res[0] .= $interface['name']['thu'].' ';
		}
		else{
			$res[0] .= '週四 ';
		}
		break;
	case 5:
		if(isset($interface['name']['fri'])){
			$res[0] .= $interface['name']['fri'].' ';
		}
		else{
			$res[0] .= '週五 ';
		}
		break;
	case 6:
		if(isset($interface['name']['sat'])){
			$res[0] .= $interface['name']['sat'].' ';
		}
		else{
			$res[0] .= '週六 ';
		}
		break;
	case 7:
		if(isset($interface['name']['sun'])){
			$res[0] .= $interface['name']['sun'].' ';
		}
		else{
			$res[0] .= '週日 ';
		}
		break;
}
$res[0] .= date('H:i');
$res[1]=date('s');
echo json_encode($res);
?>