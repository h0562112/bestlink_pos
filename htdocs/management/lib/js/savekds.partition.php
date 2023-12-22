<?php
$kds=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini',true);
if(isset($_POST['lan'])&&$_POST['lan']!=''){
	if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
		$interface=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('../../lan/interface1.ini')){
		$interface=parse_ini_file('../../lan/interface1.ini',true);
	}
	else{
		$interface='-1';
	}
}
include_once '../../../tool/inilib.php';
if(isset($_POST['createnew'])){
	if(isset($kds['type']['name'])){
		if(isset($interface['name']['groupbysalf'])){
			$kds['group'.sizeof($kds['type']['name'])]['name'][-1]=$interface['name']['groupbysalf'];//分群名稱
		}
		else{
			$kds['group'.sizeof($kds['type']['name'])]['name'][-1]="依照產品本身做分群";//分群名稱
		}
		$kds['group'.sizeof($kds['type']['name'])]['limit'][-1]=0;//群組數量上限;0為無上限
		$kds['type']['name'][sizeof($kds['type']['name'])]=$_POST['createname'];
	}
	else{
		if(isset($interface['name']['groupbysalf'])){
			$kds['group0']['name'][-1]=$interface['name']['groupbysalf'];//分群名稱
		}
		else{
			$kds['group0']['name'][-1]="依照產品本身做分群";//分群名稱
		}
		$kds['group0']['limit'][-1]=0;//群組數量上限;0為無上限
		$kds['type']['name'][0]=$_POST['createname'];
	}
}
else{
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$kds['type']['name'][$_POST['no'][$i]]=$_POST['newpartition'][$i];
	}
}
write_ini_file($kds,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini');
?>