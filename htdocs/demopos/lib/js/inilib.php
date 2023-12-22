<?php
function write_ini_file($assoc_arr, $path){
	$content = arr2ini($assoc_arr);

  	if (!$handle = fopen($path, 'w')) {
  		return false;
  	}
  	$success = fwrite($handle, $content);
  	fclose($handle);

  	return $success;
}

function arr2ini(array $a, array $parent = array()){
  	$out = '';
  	foreach ($a as $k => $v)
  	{
  		if (is_array($v))
  		{
  			$sec = array_merge((array) $parent, (array) $k);
  			$out .= '[' . join('.', $sec) . ']' . PHP_EOL;
  			$out .= arr2ini($v, $sec);
  		}
  		else
  		{
              //$v = str_replace('"', '\"', $v);
  			$out .= "$k=\"$v\"" . PHP_EOL;
  		}
  	}
  	return $out;
}
?>