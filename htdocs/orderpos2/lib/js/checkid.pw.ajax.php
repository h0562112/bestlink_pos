<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','webmember','orderuser','0424732003','utf-8','mysql');
if($conn){
	$sql='SELECT * FROM member WHERE id="'.$_POST['id'].'" AND pw="'.$_POST['pw'].'"';
	$cloudnum=sqlquery($conn,$sql,'mysql');
	if(isset($cloudnum[0]['memno'])){
		$pass=1;
	}
	else{
		$pass=0;
	}
	sqlclose($conn,'mysql');
}
else{
}
if(isset($pass)&&$pass==1&&isset($_POST['company'])&&$_POST['company']!=''){
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
	if($_POST['dep']!=''){
		$target=$_POST['dep'];
	}
	else{
		$target=$_POST['company'];
	}
	if($conn){
		$sql='SELECT memno FROM member WHERE id="'.$_POST['id'].'" AND pw="'.$_POST['pw'].'" AND SUBSTR(memno,1,LENGTH("W'.$target.'"))="W'.$target.'"';
		$num=sqlquery($conn,$sql,'mysql');
		if(isset($num[0]['memno'])){
			echo 'success-'.$num[0]['memno'];
		}
		else{
			if(isset($cloudnum[0]['setting'])){
				$sql='INSERT INTO member (memno,cardno,setting,name,tel,address,power,firstdate,id,pw) SELECT CONCAT("W'.$target.'",(SELECT COUNT(*) FROM member WHERE SUBSTR(memno,1,LENGTH("W'.$target.'"))="W'.$target.'")),(SELECT CONCAT((CAST(DATE_FORMAT(CURDATE(),"%Y") AS INT)-1911),DATE_FORMAT(CURDATE(),"%m%d"),substr(CONCAT("000",(COUNT(*)+1)),-3,3)),"'.$cloudnum[0]['setting'].'" FROM member WHERE firstdate=DATE_FORMAT(CURDATE(),"%Y-%m-%d")),"'.$cloudnum[0]['name'].'","'.$cloudnum[0]['tel'].'","'.$cloudnum[0]['address'].'","'.$cloudnum[0]['power'].'",DATE_FORMAT(CURDATE(),"%Y-%m-%d"),"'.$cloudnum[0]['tel'].'","'.$cloudnum[0]['tel'].'"';
			}
			else if(isset($cloudnum)){
				$sql='INSERT INTO member (memno,cardno,name,tel,address,power,firstdate,id,pw) SELECT CONCAT("W'.$target.'",(SELECT COUNT(*) FROM member WHERE SUBSTR(memno,1,LENGTH("W'.$target.'"))="W'.$target.'")),(SELECT CONCAT((CAST(DATE_FORMAT(CURDATE(),"%Y") AS INT)-1911),DATE_FORMAT(CURDATE(),"%m%d"),substr(CONCAT("000",(COUNT(*)+1)),-3,3)) FROM member WHERE firstdate=DATE_FORMAT(CURDATE(),"%Y-%m-%d")),"'.$cloudnum[0]['name'].'","'.$cloudnum[0]['tel'].'","'.$cloudnum[0]['address'].'","'.$cloudnum[0]['power'].'",DATE_FORMAT(CURDATE(),"%Y-%m-%d"),"'.$cloudnum[0]['tel'].'","'.$cloudnum[0]['tel'].'"';
			}
			else{
			}
			sqlnoresponse($conn,$sql,'mysql');
			$sql='SELECT memno FROM member WHERE tel="'.$cloudnum[0]['tel'].'" AND SUBSTR(memno,1,LENGTH("W'.$target.'"))="W'.$target.'"';
			$memno=sqlquery($conn,$sql,'mysql');
			echo 'success-'.$memno[0]['memno'];
		}
		sqlclose($conn,'mysql');
	}
	else{
		echo 'error1';
	}
}
else{
	echo 'error2';
}
?>