<?php
include_once '../tool/dbTool.inc.php';
$conn=sqlconnect('../database/person','member.db','','','','sqlite');
//$conn=sqlconnect('../management/menudata/ttt/person','member.db','','','','sqlite');//茶葉DEMO使用
$sql='SELECT cardno,name FROM person WHERE (tel LIKE "'.$_POST['memno'].'%" OR tel2 LIKE "'.$_POST['memno'].'%")';
$list=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(sizeof($list)==0){
	echo '該電話不存在，請確認輸入為正確電話。';
}
else{
	if(sizeof($list)>1){
		echo '<div style="width:calc(100% - 100px);height:100%;font-size:30px;padding:0 50px;line-height:200px;">選擇會員：<select name="temp">';
		foreach($list as $l){
			echo '<option value="'.$l['cardno'].'-'.$l['name'].'">'.$l['cardno'].'-'.$l['name'].'</option>';
		}
		echo "</select><button id='submit' style='width:calc(100% / 2 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;cursor:pointer;'><div style='font-weight:bold;font-size:50px;color:#898989;'>確認</div><div style='font-weight:bold;font-size:35px;color:#CDCECE;'>Submit</div></button></div>";
	}
	else{
		echo $list[0]['cardno'].'-'.$list[0]['name'];
	}
}
?>