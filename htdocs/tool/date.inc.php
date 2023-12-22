<?php
function getMon($startdate,$enddate){
	$startdate=preg_replace('/-/','',$startdate);
	$enddate=preg_replace('/-/','',$enddate);
	$startY=substr($startdate,0,4);
	$startM=substr($startdate,4,2);
	$endY=substr($enddate,0,4);
	$endM=substr($enddate,4,2);
	$totalMon=intval($endM)-intval($startM);
	if($endY==$startY){
	}
	else{
		$totalMon=12+intval($totalMon);
	}
	return $totalMon;
}
?>