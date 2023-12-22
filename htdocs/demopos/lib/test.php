<?php
$fl=scandir('./js');
foreach($fl as $f){
	if(preg_match('/inv/',$f)){
		echo $f.'<br>';
	}
	else{
	}
}
?>