<?php
$dir='../print/kvm';
$files=scandir($dir);
for($i=2;$i<sizeof($files);$i++){
	if(substr($files[$i],-4)=='.ini'){
		$f=parse_ini_file('../print/kvm/'.$files[$i],true);
		echo '<div>'.$f['list']['title'].'</div>';
		echo '<div style="border-bottom:1px #ffffff solid;">';
		for($index=0;$index<sizeof($f['item']['label']);$index++){
			echo '<span>'.$f['item']['label'][$index].'</span> ';
		}
		echo '</div>';
	}
	else{
	}
}
?>