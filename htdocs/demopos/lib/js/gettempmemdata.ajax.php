<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../../../tool/dbTool.inc.php';
//print_r($_POST);
if(isset($_POST['ajax'])){
	$conn=sqlconnect('localhost','','tableplus','0424732003','utf8','mysql');
	$sql='SHOW DATABASES LIKE "'.$_POST['company'].'";';
	$res=sqlquery($conn,$sql,'mysql');
	if(isset($res[0])){//DB存在
		sqlclose($conn,'mysql');
		$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');

		$sql="SHOW COLUMNS FROM member LIKE 'recommend'";
		$colexists=sqlquery($conn,$sql,'mysql');
		if(isset($colexists[0]['Field'])&&$colexists[0]['Field']!=''){
		}
		else{
			sqlclose($conn,'mysql');
			$conn=sqlconnect('localhost',$_POST['company'],'orderpos','0424732003','utf8','mysql');
			$sql="ALTER TABLE member ADD recommend varchar(20) DEFAULT NULL COMMENT '推薦人memno'";
			sqlnoresponse($conn,$sql,'mysql');
			sqlclose($conn,'mysql');
			$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
		}

		//$sql="SELECT * FROM member WHERE memno='".$_POST['memno']."' AND state=1";
		if(preg_match('/;-;/',$_POST['memno'])){//2020/8/28 &&preg_match('/'.$_POST['company'].'/',$_POST['memno'])
			$memdata=preg_split('/;-;/',$_POST['memno']);
			$sql='SELECT member.*,powergroup.name AS powername FROM member INNER JOIN powergroup ON powergroup.pno=member.power WHERE member.memno="'.$memdata[0].'" AND member.state=1';
		}
		else{
			$sql='SELECT member.*,powergroup.name AS powername FROM member INNER JOIN powergroup ON powergroup.pno=member.power WHERE member.memno="'.$_POST['memno'].'" AND member.state=1';
		}
		$data=sqlquery($conn,$sql,'mysql');
	}
	else{
		echo json_encode('查無資料庫。');
	}
	sqlclose($conn,'mysql');
	/*$conn=sqlconnect('../../../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
	if(preg_match('/;-;/',$_POST['memno'])&&preg_match('/'.$_POST['company'].'/',$_POST['memno'])){
		$memdata=preg_split('/;-;/',$_POST['memno']);
		$sql='SELECT memno,cardno,name,tel,tel2,address FROM person WHERE memno="'.$memdata[0].'" AND state=1';
		$data=sqlquery($conn,$sql,'sqlite');
		if(isset($data[0]['memno'])){
		}
		else{
			$sql='SELECT memno,cardno,name,tel,tel2,address FROM person WHERE (tel="'.$memdata[1].'" OR tel2="'.$memdata[1].'") AND state=1';
			$data=sqlquery($conn,$sql,'sqlite');
			if(isset($data[0]['memno'])){
				$sql='UPDATE person SET memno="'.$memdata[0].'" WHERE (tel="'.$memdata[1].'" OR tel2="'.$memdata[1].'") AND state=1';
				sqlnoresponse($conn,$sql,'sqlite');
			}
			else{
			}
		}
		//echo json_encode($data);
		if(isset($data[0]['memno'])){
		}
		else{
			$sql='INSERT INTO person (memno,cardno,name,tel,power,firstdate,state,memno) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$memdata[2].'","'.$memdata[1].'","1",strftime("%Y-%m-%d"),1,"'.$memdata[0].'"';
			//echo $sql;
			sqlnoresponse($conn,$sql,'sqlite');
			$sql='SELECT memno,cardno,name,tel,tel2,address FROM person WHERE (tel="'.$memdata[1].'" OR tel2="'.$memdata[1].'") AND state=1';
			$data=sqlquery($conn,$sql,'sqlite');
		}
	}
	else{
		$sql="SELECT memno,cardno,name,tel,tel2,address,memno FROM person WHERE memno='".$_POST['memno']."' AND state=1";
		$data=sqlquery($conn,$sql,'sqlite');
	}
	sqlclose($conn,'sqlite');*/
	echo json_encode($data);
}
else if(file_exists('../../../database/person/member.db')){
	$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
	if(preg_match('/;-;/',$_POST['memno'])&&preg_match('/'.$_POST['company'].'/',$_POST['memno'])){
		$memdata=preg_split('/;-;/',$_POST['memno']);
		//$sql='SELECT memno,cardno,name,tel,tel2,address FROM person WHERE memno="'.$memdata[0].'" AND state=1';
		$sql='SELECT person.*,powergroup.name AS powername FROM person INNER JOIN powergroup ON powergroup.pno=person.power WHERE person.memno="'.$memdata[0].'" AND person.state=1';
		$data=sqlquery($conn,$sql,'sqlite');
		if(isset($data[0]['memno'])){
		}
		else{
			$sql='SELECT memno,cardno,name,tel,tel2,address FROM person WHERE (tel="'.$memdata[1].'" OR tel2="'.$memdata[1].'") AND state=1';
			$data=sqlquery($conn,$sql,'sqlite');
			if(isset($data[0]['memno'])){
				$sql='UPDATE person SET memno="'.$memdata[0].'" WHERE (tel="'.$memdata[1].'" OR tel2="'.$memdata[1].'") AND state=1';
				sqlnoresponse($conn,$sql,'sqlite');
			}
			else{
			}
		}
		//echo json_encode($data);
		if(isset($data[0]['memno'])){
		}
		else{
			$sql='INSERT INTO person (memno,cardno,name,tel,power,firstdate,state,memno) SELECT "'.$_POST['story'].'"||(SELECT COUNT(*) FROM person),(SELECT (strftime("%Y")-1911)||strftime("%m%d")||substr("00"||(COUNT(*)+1),-2,2) FROM person WHERE firstdate=strftime("%Y-%m-%d")),"'.$memdata[2].'","'.$memdata[1].'","1",strftime("%Y-%m-%d"),1,"'.$memdata[0].'"';
			//echo $sql;
			sqlnoresponse($conn,$sql,'sqlite');
			$sql='SELECT memno,cardno,name,tel,tel2,address FROM person WHERE (tel="'.$memdata[1].'" OR tel2="'.$memdata[1].'") AND state=1';
			$data=sqlquery($conn,$sql,'sqlite');
		}
	}
	else{
		$sql="SELECT memno,cardno,name,tel,tel2,address,memno FROM person WHERE memno='".$_POST['memno']."' AND state=1";
		$data=sqlquery($conn,$sql,'sqlite');
	}
	sqlclose($conn,'sqlite');
	echo json_encode($data);
}
else{
	echo json_encode('查無資料庫。');
}
?>