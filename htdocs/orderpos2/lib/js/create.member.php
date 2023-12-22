<?php
header('Access-Control-Allow-Origin: *');//╗╖║▌йIеs┼vнн
include_once '../../../tool/dbTool.inc.php';
//$set=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['story'].'/database/setup.ini',true);
//$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
if(isset($_POST['initpower'])){
}
else{
	$_POST['initpower']='0';
}
$conn=sqlconnect('localhost','webmember','orderuser','0424732003','utf-8','mysql');
if($conn){
	$sql='SELECT COUNT(*) AS num FROM member WHERE tel="'.$_POST['tel'].'"';
	$num=sqlquery($conn,$sql,'mysql');
	if(isset($num[0]['num'])&&intval($num[0]['num'])>0){
		echo 'exists';
	}
	else{
		if(isset($_POST['setting'])){
			$sql='INSERT INTO member (memno,cardno,setting,name,tel,address,power,firstdate,id,pw) SELECT (SELECT COUNT(*) FROM member),(SELECT CONCAT((CAST(DATE_FORMAT(CURDATE(),"%Y") AS INT)-1911),DATE_FORMAT(CURDATE(),"%m%d"),substr(CONCAT("000",(COUNT(*)+1)),-3,3)),"'.$_POST['setting'].'" FROM member WHERE firstdate=DATE_FORMAT(CURDATE(),"%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",DATE_FORMAT(CURDATE(),"%Y-%m-%d"),"'.$_POST['tel'].'","'.$_POST['tel'].'"';
		}
		else{
			$sql='INSERT INTO member (memno,cardno,name,tel,address,power,firstdate,id,pw) SELECT (SELECT COUNT(*) FROM member),(SELECT CONCAT((CAST(DATE_FORMAT(CURDATE(),"%Y") AS INT)-1911),DATE_FORMAT(CURDATE(),"%m%d"),substr(CONCAT("000",(COUNT(*)+1)),-3,3)) FROM member WHERE firstdate=DATE_FORMAT(CURDATE(),"%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",DATE_FORMAT(CURDATE(),"%Y-%m-%d"),"'.$_POST['tel'].'","'.$_POST['tel'].'"';
			//echo $sql;
		}
		sqlnoresponse($conn,$sql,'mysql');
		echo 'OK';
	}
	sqlclose($conn,'mysql');
}
else{
}
if(isset($_POST['company'])&&$_POST['company']!=''){
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
	if($conn){
		if($_POST['story']!=''){
			$target=$_POST['story'];
		}
		else{
			$target=$_POST['company'];
		}
		$sql='SELECT COUNT(*) AS num FROM member WHERE tel="'.$_POST['tel'].'" AND SUBSTR(memno,1,LENGTH("W'.$target.'"))="W'.$target.'"';
		$num=sqlquery($conn,$sql,'mysql');
		if(isset($num[0]['num'])&&intval($num[0]['num'])>0){
		}
		else{
			if(isset($_POST['setting'])){
				$sql='INSERT INTO member (memno,cardno,setting,name,tel,address,power,firstdate,id,pw) SELECT CONCAT("W'.$target.'",(SELECT COUNT(*) FROM member WHERE SUBSTR(memno,1,LENGTH("W'.$target.'"))="W'.$target.'")),(SELECT CONCAT((CAST(DATE_FORMAT(CURDATE(),"%Y") AS INT)-1911),DATE_FORMAT(CURDATE(),"%m%d"),substr(CONCAT("000",(COUNT(*)+1)),-3,3)),"'.$_POST['setting'].'" FROM member WHERE firstdate=DATE_FORMAT(CURDATE(),"%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",DATE_FORMAT(CURDATE(),"%Y-%m-%d"),"'.$_POST['tel'].'","'.$_POST['tel'].'"';
			}
			else{
				$sql='INSERT INTO member (memno,cardno,name,tel,address,power,firstdate,id,pw) SELECT CONCAT("W'.$target.'",(SELECT COUNT(*) FROM member WHERE SUBSTR(memno,1,LENGTH("W'.$target.'"))="W'.$target.'")),(SELECT CONCAT((CAST(DATE_FORMAT(CURDATE(),"%Y") AS INT)-1911),DATE_FORMAT(CURDATE(),"%m%d"),substr(CONCAT("000",(COUNT(*)+1)),-3,3)) FROM member WHERE firstdate=DATE_FORMAT(CURDATE(),"%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",DATE_FORMAT(CURDATE(),"%Y-%m-%d"),"'.$_POST['tel'].'","'.$_POST['tel'].'"';
				//echo $sql;
			}
			sqlnoresponse($conn,$sql,'mysql');
			$sql='SELECT memno FROM member WHERE tel="'.$_POST['tel'].'" AND SUBSTR(memno,1,LENGTH("W'.$target.'"))="W'.$target.'"';
			$memno=sqlquery($conn,$sql,'mysql');
			echo 'success-'.$memno[0]['memno'];
		}
		sqlclose($conn,'mysql');
	}
	else{
	}
}
else{
}
?>