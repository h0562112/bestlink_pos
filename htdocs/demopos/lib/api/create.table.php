<?php
function create_table_to_DB($conn,$table){
	$sql="CREATE TABLE '".$table."' ('bizdate' TEXT NOT NULL, 'consecnumber' TEXT NOT NULL, 'value' INTEGER, PRIMARY KEY ('bizdate', 'zcounter', 'consecnumber'))";
	sqlnoresponse($conn,$sql,'sqlite');
	$functionconn=sqlquery('../../../database/sale','empty.db','','','','sqlite');
	$chksql='SELECT name FROM sqlite_master WHERE type="table" AND name="'.$otindex.'"';
	$t=sqlquery($functionconn,$chksql,'sqlite');
	if(isset($t[0]['name'])){
	}
	else{
		sqlnoresponse($functionconn,$sql,'sqlite');
	}
	sqlclose($functionconn,'sqlite');
	if(file_exists('../../../database/sale/Cover.db')){
		$functionconn=sqlquery('../../../database/sale','Cover.db','','','','sqlite');
		$chksql='SELECT name FROM sqlite_master WHERE type="table" AND name="'.$otindex.'"';
		$t=sqlquery($functionconn,$chksql,'sqlite');
		if(isset($t[0]['name'])){
		}
		else{
			sqlnoresponse($functionconn,$sql,'sqlite');
		}
		sqlclose($functionconn,'sqlite');
	}
	else{
	}
}
?>