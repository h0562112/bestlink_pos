<?php
if(file_exists('../demopos/lib/api/mempaypw/data/'.$_POST['machine'].'.ini')){
	$pw=parse_ini_file('../demopos/lib/api/mempaypw/data/'.$_POST['machine'].'.ini',true);
	if(isset($pw['basic']['pw'])){
		echo $pw['basic']['pw'];
	}
	else{
		echo '';
	}
}
else{
	echo 'notexitst';
}
?>