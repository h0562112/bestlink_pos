<?php
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini')){
	$kds=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini',true);
	if(isset($kds['group'.$_POST['partition']]['name'])){
		echo '<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">';
		foreach($kds['group'.$_POST['partition']]['name'] as $i=>$v){
			echo '<a id="'.$i.'">'.$v.'</a>';
		}
	}
	else{
	}
}
else{
}
?>