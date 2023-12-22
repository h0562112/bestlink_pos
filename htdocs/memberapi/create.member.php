<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../tool/dbTool.inc.php';
$dbtype='sqlite';
if(file_exists('../menudata/'.$_POST['company'].'/'.$_POST['story'].'/initsetting.ini')){
	//echo print_r($_POST);
	$initsetting=parse_ini_file('../menudata/'.$_POST['company'].'/'.$_POST['story'].'/initsetting.ini',true);
	if($initsetting['init']['onlinemember']=='1'){//網路會員
		$conn=sqlconnect('localhost','','tableplus','0424732003','utf8','mysql');
		$sql='SHOW DATABASES LIKE "'.$_POST['company'].'";';
		$dbexists=sqlquery($conn,$sql,'mysql');
		//print_r($dbexists);
		if(isset($dbexists[0])){//存在DB
		}
		else{
			$sql='CREATE SCHEMA IF NOT EXISTS '.$_POST['company'].' DEFAULT CHARACTER SET utf8;';
			sqlnoresponse($conn,$sql,'mysql');
			$sql='GRANT SELECT, INSERT, UPDATE, DELETE ON `'.$_POST['company'].'`.* TO "orderuser"@"localhost";';
			sqlnoresponse($conn,$sql,'mysql');
			sqlclose($conn,'mysql');
			$conn=sqlconnect('localhost',$_POST['company'],'tableplus','0424732003','utf8','mysql');
			$tables=parse_ini_file('./mysql/tables.ini',true);
			for($sqlindex=1;$sqlindex<=sizeof($tables['sql']);$sqlindex++){
				sqlnoresponse($conn,$tables['sql'][$sqlindex],'mysql');
			}
		}
		sqlclose($conn,'mysql');

		$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
		$dbtype='mysql';
		//sqlclose($conn,'mysql');
	}
	else{//本地會員
		$conn=sqlconnect('../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
	}
}
else{//預設使用本地會員
	$conn=sqlconnect('../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
}
//$set=parse_ini_file('../ourpos/'.$_POST['company'].'/'.$_POST['story'].'/database/setup.ini',true);
//$initsetting=parse_ini_file('../database/initsetting.ini',true);
//if($_POST['type']=='online'){
	//$conn=sqlconnect('../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
/*}
else{
	$conn=sqlconnect('../database/person','member.db','','','','sqlite');
}*/
if(isset($_POST['initpower'])){
}
else{
	$_POST['initpower']='0';
}
if($conn){
	if($dbtype=='mysql'){
		if(isset($_POST['membertype'])&&$_POST['membertype']=='0'){//門市會員不共用
			$sql='SELECT *,COUNT(*) AS num FROM member WHERE SUBSTR(memno,1,LENGTH("'.$_POST['story'].'"))="'.$_POST['story'].'" AND (tel="'.$_POST['tel'].'" OR tel2="'.$_POST['tel'].'" OR setting="'.$_POST['tel'].'")';
		}
		else{
			$sql='SELECT *,COUNT(*) AS num FROM member WHERE tel="'.$_POST['tel'].'" OR tel2="'.$_POST['tel'].'" OR setting="'.$_POST['tel'].'"';
		}
	}
	else{
		if(isset($_POST['membertype'])&&$_POST['membertype']=='0'){//門市會員不共用
			$sql='SELECT COUNT(*) AS num,* FROM person WHERE SUBSTR(memno,1,LENGTH("'.$_POST['story'].'"))="'.$_POST['story'].'" AND (tel="'.$_POST['tel'].'" OR tel2="'.$_POST['tel'].'" OR setting="'.$_POST['tel'].'")';
		}
		else{
			$sql='SELECT COUNT(*) AS num,* FROM person WHERE tel="'.$_POST['tel'].'" OR tel2="'.$_POST['tel'].'" OR setting="'.$_POST['tel'].'"';
		}
	}
	$num=sqlquery($conn,$sql,$dbtype);
	if(isset($num[0]['num'])&&intval($num[0]['num'])>0){
		//echo $sql;
		echo 'exists';
		//print_r($num[0]['num']);
	}
	else{
		if(isset($_POST['address'])&&isset($_POST['remark'])){
			if(isset($_POST['setting'])){
				if($dbtype=='mysql'){
					$sql='INSERT INTO member (memno,cardno,setting,name,tel,address,power,firstdate,state,remark,id,pw) SELECT CONCAT("'.$_POST['story'].'",(SELECT COUNT(*) FROM member)),(SELECT CONCAT((YEAR(CURDATE())-1911),SUBSTR(CURDATE(),6,2),SUBSTR(CURDATE(),9,2),substr(CONCAT("00",(COUNT(*)+1)),-2,2)) FROM member WHERE firstdate=CURDATE()),"'.$_POST['setting'].'","'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",CURDATE(),1,"'.$_POST['remark'].'","'.$_POST['tel'].'","'.$_POST['tel'].'"';
				}
				else{
					$sql='INSERT INTO person (memno,cardno,setting,name,tel,address,power,firstdate,state,remark) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$_POST['setting'].'","'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",strftime("%Y-%m-%d"),1,"'.$_POST['remark'].'"';
				}
			}
			else{
				if($dbtype=='mysql'){
					$sql='INSERT INTO member (memno,cardno,name,tel,address,power,firstdate,state,remark,id,pw) SELECT CONCAT("'.$_POST['story'].'",(SELECT COUNT(*) FROM member)),(SELECT CONCAT((YEAR(CURDATE())-1911),SUBSTR(CURDATE(),6,2),SUBSTR(CURDATE(),9,2),substr(CONCAT("00",(COUNT(*)+1)),-2,2)) FROM member WHERE firstdate=CURDATE()),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",CURDATE(),1,"'.$_POST['remark'].'","'.$_POST['tel'].'","'.$_POST['tel'].'"';
				}
				else{
					$sql='INSERT INTO person (memno,cardno,name,tel,address,power,firstdate,state,remark) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",strftime("%Y-%m-%d"),1,"'.$_POST['remark'].'"';
				}
			}
		}
		else if(isset($_POST['address'])){
			if(isset($_POST['setting'])){
				if($dbtype=='mysql'){
					$sql='INSERT INTO member (memno,cardno,setting,name,tel,address,power,firstdate,state,id,pw) SELECT CONCAT("'.$_POST['story'].'",(SELECT COUNT(*) FROM member)),(SELECT CONCAT((YEAR(CURDATE())-1911),SUBSTR(CURDATE(),6,2),SUBSTR(CURDATE(),9,2),substr(CONCAT("00",(COUNT(*)+1)),-2,2)) FROM member WHERE firstdate=CURDATE()),"'.$_POST['setting'].'","'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",CURDATE(),1,"'.$_POST['tel'].'","'.$_POST['tel'].'"';
				}
				else{
					$sql='INSERT INTO person (memno,cardno,setting,name,tel,address,power,firstdate,state) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$_POST['setting'].'","'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",strftime("%Y-%m-%d"),1';
				}
			}
			else{
				if($dbtype=='mysql'){
					$sql='INSERT INTO member (memno,cardno,name,tel,address,power,firstdate,state,id,pw) SELECT CONCAT("'.$_POST['story'].'",(SELECT COUNT(*) FROM member)),(SELECT CONCAT((YEAR(CURDATE())-1911),SUBSTR(CURDATE(),6,2),SUBSTR(CURDATE(),9,2),substr(CONCAT("00",(COUNT(*)+1)),-2,2)) FROM member WHERE firstdate=CURDATE()),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",CURDATE(),1,"'.$_POST['tel'].'","'.$_POST['tel'].'"';
				}
				else{
					$sql='INSERT INTO person (memno,cardno,name,tel,address,power,firstdate,state) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['address'].'","'.$_POST['initpower'].'",strftime("%Y-%m-%d"),1';
				}
			}
		}
		else{
			if($dbtype=='mysql'){
				$sql='INSERT INTO member (memno,cardno,name,tel,power,firstdate,state,id,pw) SELECT CONCAT("'.$_POST['story'].'",(SELECT COUNT(*) FROM member)),(SELECT CONCAT((YEAR(CURDATE())-1911),SUBSTR(CURDATE(),6,2),SUBSTR(CURDATE(),9,2),substr(CONCAT("00",(COUNT(*)+1)),-2,2)) FROM member WHERE firstdate=CURDATE()),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['initpower'].'",CURDATE(),1,"'.$_POST['tel'].'","'.$_POST['tel'].'"';
			}
			else{
				$sql='INSERT INTO person (memno,cardno,name,tel,power,firstdate,state) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$_POST['name'].'","'.$_POST['tel'].'","'.$_POST['initpower'].'",strftime("%Y-%m-%d"),1';
			}
		}
		//$res=sqlquery($conn,$sql,'sqlite');
		//echo $res;
		//echo $sql;
		sqlnoresponse($conn,$sql,$dbtype);
		echo 'success';
		$sql='SELECT memno FROM member WHERE tel="'.$_POST['tel'].'" AND name="'.$_POST['name'].'"';
		$res=sqlquery($conn,$sql,$dbtype);
		if(isset($_POST['entertype'])&&$_POST['entertype']=='orderweb'){
			echo $res[0]['memno'];
		}
		else{
		}
	}
	sqlclose($conn,$dbtype);
}
else{
}
?>