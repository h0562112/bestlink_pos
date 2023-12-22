<?php
function j2a($jsonstr){
	$temp=array();
	$namearray=array_column(json_decode($jsonstr,true), 'name');
	$valuearray=array_column(json_decode($jsonstr,true), 'value');
	for($i=0;$i<sizeof($namearray);$i++){
		//echo substr($namearray[$i],-2);
		if(substr($namearray[$i],-2)=='[]'){
			$temp[substr($namearray[$i],0,strlen($namearray[$i])-2)][]=$valuearray[$i];
		}
		else{
			$temp[$namearray[$i]]=$valuearray[$i];
		}
	}
	return $temp;
}
?>