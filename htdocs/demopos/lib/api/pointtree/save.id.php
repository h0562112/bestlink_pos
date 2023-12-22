<?php
include_once '../../../../tool/dbTool.inc.php';
$timeini=parse_ini_file('../../../../database/timem1.ini',true);
$filename='point_'.substr($timeini['time']['bizdate'],0,6);
if(file_exists("../../../../database/sale/".$filename.".db")){
}
else{
	if(file_exists("../../../../database/sale/EMpoint.db")){
	}
	else{
		include_once '../../js/create.emptyDB.php';
		create('EMpoint');
	}
	copy("../../../../database/sale/EMpoint.db","../../../../database/sale/".$filename.".db");
}
$conn=sqlconnect('../../../../database/sale',$filename.'.db','','','','sqlite');
$sql='UPDATE pointtree SET pos_token_tx_id="'.$_POST['pos_token_tx_id'].'" WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'" AND token="'.$_POST['token'].'" AND mobilephone="'.$_POST['mobile_number'].'"';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>