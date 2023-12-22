<?php
$tb=parse_ini_file('../../../database/floorspend.ini',true);
if(preg_match('/,/',$_POST['tablenumber'])){
	$splittable=preg_split('/,/',$_POST['tablenumber']);
	for($sti=0;$sti<sizeof($splittable);$sti++){
		if($sti!=0){
			echo ',';
		}
		else{
		}
		if(preg_match('/-/',$splittable[$sti])){
			$inittable=preg_split('/-/',$splittable[$sti]);
			if(isset($tb['Tname'][$inittable[0]])){
				echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
			}
			else{
				echo $splittable[$sti];
			}
		}
		else{
			if(isset($tb['Tname'][$splittable[$sti]])){
				echo $tb['Tname'][$splittable[$sti]];
			}
			else{
				echo $splittable[$sti];
			}
		}
	}
}
else{
	if(preg_match('/-/',$_POST['tablenumber'])){
		$inittable=preg_split('/-/',$_POST['tablenumber']);
		if(isset($tb['Tname'][$inittable[0]])){
			echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
		}
		else{
			echo $_POST['tablenumber'];
		}
	}
	else{
		if(isset($tb['Tname'][$_POST['tablenumber']])){
			echo $tb['Tname'][$_POST['tablenumber']];
		}
		else{
			echo $_POST['tablenumber'];
		}
	}
}
?>