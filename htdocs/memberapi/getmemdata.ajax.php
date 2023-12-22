<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../tool/dbTool.inc.php';
/*if(isset($_GET['admin'])){
	$_POST=$_GET;
	//print_r($_POST);
}
else{
}*/
//if($_POST['type']=='online'){
	if(isset($_POST['ajax'])){
		/*
		if(file_exists('../management/menudata/'.$_POST['company'].'/person/member.db')){
			$conn=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
			$sql='SELECT * FROM person WHERE memno="'.$_POST['memno'].'" AND state=1';
			$data=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
			echo json_encode($data);
		}
		else{
			echo json_encode('查無資料庫。');
		}
		*/
		if($_POST['type']=='online'){//網路會員
			//echo print_r($_POST);
			$conn=sqlconnect('localhost','','tableplus','0424732003','utf8','mysql');
			$sql='SHOW DATABASES LIKE "'.$_POST['company'].'";';
			$dbexists=sqlquery($conn,$sql,'mysql');
			//print_r($dbexists);
			if(isset($dbexists[0])){//存在DB
				sqlclose($conn,'mysql');
				$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
				$sql='SELECT member.*,powergroup.name AS powername FROM member INNER JOIN powergroup ON powergroup.pno=member.power WHERE member.memno="'.$_POST['memno'].'" AND member.state=1';
				$data=sqlquery($conn,$sql,'mysql');
				echo json_encode($data);
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
				echo json_encode('查無資料庫。');
			}
			sqlclose($conn,'mysql');
		}
		else{//本地會員
			if(file_exists('../management/menudata/'.$_POST['company'].'/person/member.db')){
				$conn=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
				$sql='SELECT person.*,powergroup.name AS powername FROM person INNER JOIN powergroup ON powergroup.pno=person.power WHERE person.memno="'.$_POST['memno'].'" AND person.state=1';
				$data=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				echo json_encode($data);
			}
			else{
				echo json_encode('查無資料庫。');
			}
		}
	}
	else{
		if(isset($_POST['search'])){//使用會員電話查詢是否有相同的電話
			/*
			if(file_exists('../management/menudata/'.$_POST['company'].'/person/member.db')){
				$conn=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
				$sql="SELECT * FROM person WHERE (tel='".$_POST['tel']."' OR tel2='".$_POST['tel']."' OR setting='".$_POST['tel']."') AND state=1";
				$data=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				echo json_encode($data);
			}
			else{
				echo json_encode('empty');
			}
			*/
			if($_POST['type']=='online'){//網路會員
				//echo print_r($_POST);
				$conn=sqlconnect('localhost','','tableplus','0424732003','utf8','mysql');
				$sql='SHOW DATABASES LIKE "'.$_POST['company'].'";';
				$dbexists=sqlquery($conn,$sql,'mysql');
				//print_r($dbexists);
				if(isset($dbexists[0])){//存在DB
					sqlclose($conn,'mysql');
					$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
					if(!isset($_POST['membertype'])||$_POST['membertype']=='1'){
						$sql="SELECT member.*,powergroup.name AS powername FROM member INNER JOIN powergroup ON powergroup.pno=member.power WHERE (member.tel='".$_POST['tel']."' OR member.tel2='".$_POST['tel']."' OR member.setting='".$_POST['tel']."') AND member.state=1";
					}
					else{//門市會員不共用
						$sql="SELECT member.*,powergroup.name AS powername FROM member INNER JOIN powergroup ON powergroup.pno=member.power WHERE SUBSTR(member.memno,1,LENGTH('".$_POST['story']."'))=='".$_POST['story']."' AND (member.tel='".$_POST['tel']."' OR member.tel2='".$_POST['tel']."' OR member.setting='".$_POST['tel']."') AND member.state=1";
					}
					$data=sqlquery($conn,$sql,'mysql');
					echo json_encode($data);
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
					echo json_encode('empty');
				}
				sqlclose($conn,'mysql');
			}
			else{//本地會員
				if(file_exists('../management/menudata/'.$_POST['company'].'/person/member.db')){
					$conn=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
					if(!isset($_POST['membertype'])||$_POST['membertype']=='1'){
						$sql="SELECT person.*,powergroup.name AS powername FROM person INNER JOIN powergroup ON powergroup.pno=person.power WHERE (person.tel='".$_POST['tel']."' OR person.tel2='".$_POST['tel']."' OR person.setting='".$_POST['tel']."') AND person.state=1";
					}
					else{//門市會員不共用
						$sql="SELECT person.*,powergroup.name AS powername FROM person INNER JOIN powergroup ON powergroup.pno=person.power WHERE SUBSTR(person.memno,1,LENGTH('".$_POST['story']."'))=='".$_POST['story']."' AND (person.tel='".$_POST['tel']."' OR person.tel2='".$_POST['tel']."' OR person.setting='".$_POST['tel']."') AND person.state=1";
					}
					$data=sqlquery($conn,$sql,'sqlite');
					sqlclose($conn,'sqlite');
					echo json_encode($data);
				}
				else{
					echo json_encode('empty');
				}
			}
		}
		else{
			/*
			if(file_exists('../management/menudata/'.$_POST['company'].'/person/member.db')){
				$conn=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
				$sql="SELECT * FROM person WHERE (tel LIKE '%".$_POST['tel']."%' OR tel2 LIKE '%".$_POST['tel']."%' OR setting LIKE '%".$_POST['tel']."%') AND state=1";
				$data=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				echo json_encode($data);
			}
			else{
				echo json_encode('查無資料庫。');
			}
			*/

			if($_POST['type']=='online'){//網路會員
				//echo print_r($_POST);
				$conn=sqlconnect('localhost','','tableplus','0424732003','utf8','mysql');
				$sql='SHOW DATABASES LIKE "'.$_POST['company'].'";';
				$dbexists=sqlquery($conn,$sql,'mysql');
				//print_r($dbexists);
				if(isset($dbexists[0])){//存在DB
					sqlclose($conn,'mysql');
					$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
					if(isset($_POST['membertype'])&&$_POST['membertype']=='0'){//門市會員不共用
						$sql="SELECT member.*,powergroup.name AS powername FROM member INNER JOIN powergroup ON powergroup.pno=member.power WHERE SUBSTR(member.memno,1,LENGTH('".$_POST['story']."'))='".$_POST['story']."' AND (member.tel LIKE '%".$_POST['tel']."%' OR member.tel2 LIKE '%".$_POST['tel']."%' OR member.setting LIKE '%".$_POST['tel']."%') AND member.state=1";
					}
					else{
						$sql="SELECT member.*,powergroup.name AS powername FROM member INNER JOIN powergroup ON powergroup.pno=member.power WHERE (member.tel LIKE '%".$_POST['tel']."%' OR member.tel2 LIKE '%".$_POST['tel']."%' OR member.setting LIKE '%".$_POST['tel']."%') AND member.state=1";
					}
					$data=sqlquery($conn,$sql,'mysql');
					echo json_encode($data);
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
					echo json_encode('查無資料庫。');
				}
				sqlclose($conn,'mysql');
			}
			else{//本地會員
				if(file_exists('../management/menudata/'.$_POST['company'].'/person/member.db')){
					$conn=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
					if(isset($_POST['membertype'])&&$_POST['membertype']=='0'){//門市會員不共用
						$sql="SELECT person.*,powergroup.name AS powername FROM person INNER JOIN powergroup ON powergroup.pno=person.power WHERE SUBSTR(person.memno,1,LENGTH('".$_POST['story']."'))='".$_POST['story']."' AND (person.tel LIKE '%".$_POST['tel']."%' OR person.tel2 LIKE '%".$_POST['tel']."%' OR person.setting LIKE '%".$_POST['tel']."%') AND person.state=1";
					}
					else{
						$sql="SELECT person.*,powergroup.name AS powername FROM person INNER JOIN powergroup ON powergroup.pno=person.power WHERE (person.tel LIKE '%".$_POST['tel']."%' OR person.tel2 LIKE '%".$_POST['tel']."%' OR person.setting LIKE '%".$_POST['tel']."%') AND person.state=1";
					}
					$data=sqlquery($conn,$sql,'sqlite');
					sqlclose($conn,'sqlite');
					echo json_encode($data);
				}
				else{
					echo json_encode('查無資料庫。');
				}
			}
		}
	}
/*}
else{
	if(file_exists('../database/person/member.db')){
		$conn=sqlconnect('../database/person','member.db','','','','sqlite');
		$sql="SELECT * FROM person WHERE (tel LIKE '%".$_POST['tel']."%' OR tel2 LIKE '%".$_POST['tel']."%' OR setting LIKE '%".$_POST['tel']."%') AND state=1";
		$data=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
		echo json_encode($data);
	}
	else{
		echo json_encode('查無資料庫。');
	}
}*/
?>