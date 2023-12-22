<?php
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
$buttons=parse_ini_file('../../../demopos/syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
$listtype=preg_split('/,/',$initsetting['init']['orderlocation']);
//for($i=0;$i<sizeof($listtype);$i++){//<option value="3">外送</option><option value="4" selected>自取</option>
	//echo '<option value="'.$listtype[$i].'"';
	/*if($initsetting['init']['ordertype']!=$listtype[$i]){
	}
	else{
		echo ' selected';
	}*/
	//echo '>'.$buttons['name']['listtype'.$listtype[$i]].'</option>';
	echo $buttons['name']['listtype'.$_POST['listtype']];
//}
?>