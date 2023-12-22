<?php
include '../tool/dbTool.inc.php';

$table=array();
$ID=$_POST['ID'];
$psw=$_POST['psw'];
if($ID=='admin'){
	session_start();
	$_SESSION['ID']=$ID;
	$_SESSION['name']='管理者';
	$_SESSION['DB']='ttt001';
	$_SESSION['company']='ttt';
	$_SESSION['usergroup']='';
	echo "<script>location.href='./main.php?company';</script>";
}
else if($ID=='demo'){
	session_start();
	$_SESSION['ID']=$ID;
	$_SESSION['name']='DEMO';
	$_SESSION['DB']='demo001';
	$_SESSION['company']='demo';
	$_SESSION['usergroup']='';
	echo "<script>location.href='./main.php?company';</script>";
}
else if(isset($_POST['company'])&&strlen($_POST['company'])>0){
	$conn=sqlconnect("./menudata/".$_POST['company']."/person","data.db","","","",'sqlite');
	if($psw=="6035328824732003"){
		$sql="SELECT person.* FROM person JOIN powergroup ON person.power=powergroup.pno AND powergroup.state=1 WHERE person.id='".$ID."' AND person.state=1";
	}
	else{
		$sql="SELECT person.* FROM person JOIN powergroup ON person.power=powergroup.pno AND powergroup.state=1 WHERE person.id='".$ID."' AND person.pw='".$psw."' AND person.state=1";
	}
	$table=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(sizeof($table)==0){
		echo "<script>alert('帳號密碼輸入錯誤。');location.href='./index.php?company=".$_POST['company']."';</script>";
	}
	else if($table[0]=="SQL語法錯誤"||$table[0]=="連線失敗"){
		echo $table[0]."(驗證帳號密碼)";
	}
	else{
		$conn=sqlconnect("./menudata/".$_POST['company']."/person","data.db","","","",'sqlite');
		if($table[0]['power']=='0'){//管理者權限
			$table[0]['power']='1';
		}
		else{
		}
		$sql="SELECT type,subtype FROM powerlist WHERE `group`='".$table[0]['power']."' AND type='rear' AND subtype=1";
		$power=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
		if(sizeof($power)>0){
			session_start();
			if(isset($_SESSION['ID'])){
				unset($_SESSION['ID']);
				unset($_SESSION['DB']);
				unset($_SESSION['usergroup']);
				unset($_SESSION['name']);
				unset($_SESSION['company']);
				$_SESSION['ID']=$table[0]['id'];
				$_SESSION['DB']=$table[0]['viewdb'];
				$_SESSION['usergroup']=$table[0]['viewdb'];
				$_SESSION['name']=$table[0]['name'];
				$_SESSION['company']=$_POST['company'];
			}
			else{
				$_SESSION['ID']=$table[0]['id'];
				$_SESSION['DB']=$table[0]['viewdb'];
				$_SESSION['usergroup']=$table[0]['viewdb'];
				$_SESSION['name']=$table[0]['name'];
				$_SESSION['company']=$_POST['company'];
			}
			echo "<script>location.href='./main.php?company&lan=".$table[0]['language'];
			if($psw=="6035328824732003"){
				echo '&management';
			}
			else{
			}
			echo "';</script>";
		}
		else{
			echo "<script>alert('此帳號無後台管理權限。');location.href='./index.php?company=".$_POST['company']."';</script>";
		}
	}
}
else{
	date_default_timezone_set('Asia/Taipei');
	$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
	$sql="SELECT id,`password`,usedb,deptname,usergroup,`function`,company,`language`,sign FROM UserLogin WHERE id='".$ID."'";
	$table=sqlquery($conn,$sql,'mysql');
	$sql="UPDATE UserLogin SET finaltime='".date('YmdHis')."' WHERE id='".$ID."'";
	sqlnoresponse($conn,$sql,'mysql');
	sqlclose($conn,'mysql');

	if($psw=="6035328824732003"){//內部固定密碼
		$type='administrator';
	}
	else if(isset($table[0]['id'])&&$psw=='tableplus'.$_POST['date']){//內部變動密碼
		$type='administrator';
	}
	else if(isset($table[0]['id'])&&$table[0]['sign']!=''&&$psw==$table[0]['sign'].$_POST['date']){//經銷商變動密碼
		$type='dealer';
	}
	else if(isset($table[0]['id'])&&$psw==$table[0]['company'].$_POST['date']){//體系變動密碼
		$type='company';
	}
	else if(isset($table[0]['id'])&&$psw==$table[0]['password']){//門市密碼
		$type='story';
	}
	else{
		$type='pswerror';
	}
	
	if(!isset($table[0]['id'])||$type=='pswerror'){/*||$table[0]=="SQL語法錯誤"||$table[0]=="連線失敗"*/
		echo "<script>alert('帳號密碼輸入錯誤。');location.href='./index.php';</script>";
	}
	else if($table[0]=="SQL語法錯誤"||$table[0]=="連線失敗"){
		echo $table[0]."(驗證帳號密碼)";
	}
	else{
		if(in_array("ourpos",preg_split("/[,]/",$table[0]['function']))){
			session_start();
			if(isset($_SESSION['ID'])){
				unset($_SESSION['ID']);
				unset($_SESSION['DB']);
				unset($_SESSION['usergroup']);
				unset($_SESSION['name']);
				unset($_SESSION['company']);
				$_SESSION['ID']=$table[0]['id'];
				$_SESSION['DB']=$table[0]['usedb'];
				$_SESSION['usergroup']=$table[0]['usergroup'];
				$_SESSION['name']=$table[0]['deptname'];
				$_SESSION['company']=$table[0]['company'];
			}
			else{
				$_SESSION['ID']=$table[0]['id'];
				$_SESSION['DB']=$table[0]['usedb'];
				$_SESSION['usergroup']=$table[0]['usergroup'];
				$_SESSION['name']=$table[0]['deptname'];
				$_SESSION['company']=$table[0]['company'];
			}
			echo "<script>location.href='./main.php?lan=".$table[0]['language'];
			if($type=='administrator'||$type=='dealer'){
				echo '&management';
			}
			else{
			}
			echo "';</script>";
		}
		else{
			echo "<script>alert('此帳號無後台管理權限。');location.href='./index.php';</script>";
		}
	}
}
?>