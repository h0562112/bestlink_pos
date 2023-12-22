<?php
function write_ini_file($assoc_arr, $path){
	$content = arr2iniFmuti($assoc_arr);

	if (!$handle = fopen($path, 'w')) {
		return false;
	}
	$success = fwrite($handle, $content);
	fclose($handle);

	return $success;
}

function arr2iniFmuti(array $a,array $parent=array()){
	$out = '';
  	foreach ($a as $k => $v)
  	{
  		if (is_array($v))
  		{
  			$sec = array_merge((array) $parent, (array) $k);
			if(sizeof($sec)==1){
				$out .= "[" . $sec[0] . "]" . PHP_EOL;
			}
			else{
			}
			$out .= arr2iniFmuti($v, $sec);
  		}
  		else
  		{
			if(sizeof($parent)>1){
				$out .= $parent[1];
				for($i=2;$i<sizeof($parent);$i++){
					$out .= "[".$parent[$i]."]";
				}
				$out .= "[".$k."]=\"".$v."\"".PHP_EOL;
			}
			else{
				$out .= "$k=\"$v\"" . PHP_EOL;
			}
  		}
  	}
  	return $out;
}
?>