<?php
header('Access-Control-Allow-Origin: *');//╗╖║▌йIеs┼vнн
include_once '../../../tool/dbTool.inc.php';
//$set=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['story'].'/database/setup.ini',true);
//$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
if($_POST['type']=='online'){
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','mysql');
	//$conn=sqlconnect('../../../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
	$sqltype='mysql';
}
else{
	$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
	$sqltype='sqlite';
}
if(isset($_POST['initpower'])){
}
else{
	$_POST['initpower']='0';
}
if($conn){
	if($_POST['type']=='online'){
		$sql='SELECT *,COUNT(*) AS num FROM member WHERE tel="'.$_POST['tel'].'" OR tel2="'.$_POST['tel'].'" OR setting="'.$_POST['tel'].'"';
	}
	else{
		$sql='SELECT COUNT(*) AS num,* FROM person WHERE tel="'.$_POST['tel'].'" OR tel2="'.$_POST['tel'].'" OR setting="'.$_POST['tel'].'"';
	}
	$num=sqlquery($conn,$sql,$sqltype);
	if(isset($num[0]['num'])&&intval($num[0]['num'])>0){
		//echo $sql;
		echo 'exists';
		//print_r($num[0]['num']);
	}
	else{
		if($_POST['type']=='online'){
			if(isset($_POST['address'])&&isset($_POST['remark'])){
				if(isset($_POST['setting'])){
					$sql='INSERT INTO member (memno,cardno,setting,name,tel,address,power,firstdate,state,remark) SELECT CONCAT("'.$_POST['story'].'",(SELECT COUNT(*) FROM member),(SELECT CONCAT((YEAR(CURDATE())-1911),SUBSTR(CURDATE(),6,2),SUBSTR(CURDATE(),9,2),substr(CONCAT("00",(COUNT(*)+1)),-2,2)) FROM member WHERE firstdate=CURDATE()),"'.$_POST['setting'].'","'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",CURDATE(),1,"'.$_POST['remark'].'"';
				}
				else{
					$sql='INSERT INTO member (memno,cardno,name,tel,address,power,firstdate,state,remark) SELECT CONCAT("'.$_POST['story'].'",(SELECT COUNT(*) FROM member),(SELECT CONCAT((YEAR(CURDATE())-1911),SUBSTR(CURDATE(),6,2),SUBSTR(CURDATE(),9,2),substr(CONCAT("00",(COUNT(*)+1)),-2,2)) FROM member WHERE firstdate=CURDATE()),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",CURDATE(),1,"'.$_POST['remark'].'"';
				}
			}
			else{
				$sql='INSERT INTO member (memno,cardno,name,tel,power,firstdate,state) SELECT CONCAT("'.$_POST['story'].'",(SELECT COUNT(*) FROM member),(SELECT CONCAT((YEAR(CURDATE())-1911),SUBSTR(CURDATE(),6,2),SUBSTR(CURDATE(),9,2),substr(CONCAT("00",(COUNT(*)+1)),-2,2)) FROM member WHERE firstdate=CURDATE()),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['initpower'].'",CURDATE(),1';
			}
		}
		else{
			if(isset($_POST['address'])&&isset($_POST['remark'])){
				if(isset($_POST['setting'])){
					$sql='INSERT INTO person (memno,cardno,setting,name,tel,address,power,firstdate,state,remark) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$_POST['setting'].'","'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",strftime("%Y-%m-%d"),1,"'.$_POST['remark'].'"';
				}
				else{
					$sql='INSERT INTO person (memno,cardno,name,tel,address,power,firstdate,state,remark) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",strftime("%Y-%m-%d"),1,"'.$_POST['remark'].'"';
				}
			}
			else{
				$sql='INSERT INTO person (memno,cardno,name,tel,power,firstdate,state) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['initpower'].'",strftime("%Y-%m-%d"),1';
			}
		}
		//$res=sqlquery($conn,$sql,'sqlite');
		//echo $res;
		sqlnoresponse($conn,$sql,$sqltype);
		echo 'success';
	}
	sqlclose($conn,$sqltype);
}
else{
}
?>