<?php
include_once '../tool/dbTool.inc.php';
$conn=sqlconnect('../database/person','member.db','','','','sqlite');
//$conn=sqlconnect('../management/menudata/ttt/person','member.db','','','','sqlite');//����DEMO�ϥ�
$sql='SELECT * FROM person WHERE cardno="'.$_POST['cardno'].'"';
$table1=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(file_exists('../database/sale/SALES_'.$table1[0]['lastsale'].'.db')){
	$conn=sqlconnect('../database/sale','SALES_'.$table1[0]['lastsale'].'.db','','','','sqlite');
	$sql='SELECT CST012.* FROM CST012 JOIN (SELECT CST011.CONSECNUMBER,CST011.CREATEDATETIME FROM CST011 WHERE CST011.CUSTCODE="'.$table1[0]['cardno'].'" AND CST011.NBCHKNUMBER IS NULL ORDER BY CST011.CREATEDATETIME LIMIT 1) AS a ON a.CONSECNUMBER=CST012.CONSECNUMBER AND a.CREATEDATETIME=CST012.CREATEDATETIME ORDER BY LINENUMBER ASC';
	$table2=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
else{
	$table2=array();
}
if(sizeof($table2)==0){
	echo json_encode($table1);
}
else{
	for($i=0;$i<sizeof($table2);$i++){
		$table2[$i]['cardno']=$table1[0]['cardno'];
		$table2[$i]['name']=$table1[0]['name'];
		$table2[$i]['birth']=$table1[0]['birth'];
		$table2[$i]['sex']=$table1[0]['sex'];
		$table2[$i]['tel']=$table1[0]['tel'];
		$table2[$i]['tel2']=$table1[0]['tel2'];
		$table2[$i]['address']=$table1[0]['address'];
	}
	echo json_encode($table2);
}
?>