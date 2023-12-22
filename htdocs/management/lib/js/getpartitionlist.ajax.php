<?php
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini')){
	if(!isset($_POST['oldpt'])){
	}
	else{
		$temp=preg_split('/_/',$_POST['oldpt']);
		$ptno=substr($temp[0],5);
	}
	$kds=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini',true);
	if(isset($kds['type']['name'])){
		echo '<select name="createpartition">';
		for($i=0;$i<sizeof($kds['type']['name']);$i++){
			if(!isset($ptno)||$ptno!=$i){
				echo '<option value="'.$i.'">'.$kds['type']['name'][$i].'</option>';
			}
			else{
				echo '<option value="'.$i.'" selected>'.$kds['type']['name'][$i].'</option>';
			}
		}
		echo '</select>';
	}
	else{
	}
}
else{
}
?>