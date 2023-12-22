<?php
include_once '../../../tool/inilib.php';
$prlitag=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/itemprinttype.ini',true);
for($i=0;$i<sizeof($_POST['prlicheckbox']);$i++){
	if(isset($prlitag[$_POST['prlicheckbox'][$i]]['name'])){
		if(strlen($_POST['name'][$i])>0){
			$prlitag[$_POST['prlicheckbox'][$i]]['name']=$_POST['name'][$i];
		}
		else{
			continue;
		}
	}
	else{
		continue;
	}
}
write_ini_file($prlitag,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/itemprinttype.ini');
?>