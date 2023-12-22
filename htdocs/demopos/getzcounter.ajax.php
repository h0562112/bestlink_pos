<?php
include_once '../tool/dbTool.inc.php';
$init=parse_ini_file('../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(file_exists('./syspram/buttons-1.ini')){
	$buttons1=parse_ini_file('./syspram/buttons-1.ini',true);
}
else{
	$buttons1='-1';
}
$bizdate=preg_replace('/-/','',$_POST['bizdate']);
$filename='SALES_'.substr($bizdate,0,6);
if(file_exists("../database/sale/".$filename.".DB")){
}
else{
	copy("../database/sale/empty.DB","../database/sale/".$filename.".DB");
}
$conn=sqlconnect('../database/sale',$filename.'.db','','','','sqlite');
$sql='SELECT DISTINCT ZCOUNTER FROM CST012 WHERE BIZDATE="'.$bizdate.'"';
$options=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if($_POST['type']=='.historypaper'&&sizeof($options)>0&&isset($options[0]['ZCOUNTER'])){
	echo '<option value="allday" selected>';
	if(isset($buttons1['name']['allday'])){
		echo $buttons1['name']['allday'];
	}
	else{
		echo '整個營業日';
	}
	echo '</option>';
}
else{
}
for($i=0;$i<sizeof($options);$i++){
	if($options[$i]['ZCOUNTER']==null||$options[$i]['ZCOUNTER']==''){
	}
	else{
		if($i==(sizeof($options)-1)&&$_POST['type']=='#outmoney'){
			echo '<option value='.$options[$i]['ZCOUNTER'].' selected>'.$options[$i]['ZCOUNTER'].'</option>';
		}
		else{
			echo '<option value='.$options[$i]['ZCOUNTER'].'>'.$options[$i]['ZCOUNTER'].'</option>';
		}
	}
}
?>