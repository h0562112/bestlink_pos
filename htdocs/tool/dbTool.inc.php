<?php
function sqlconnect($serverName,$dbName,$user,$password,$CharacterSet,$SQLtype,$debug = 1){
	date_default_timezone_set('Asia/Taipei');
	//$serverName = "192.168.1.240"; //serverName\instanceName
	//$dbName="wss";
	//$user="sa";
	//$password="ctdainfo";
	// Since UID and PWD are not specified in the $connectionInfo array,
	// The connection will be attempted using Windows Authentication.
	if($SQLtype=='sqlserver'){
		$connectionInfo = array( "Database"=>$dbName,"UID"=>$user,"PWD"=>$password,"CharacterSet"=>$CharacterSet);
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( $conn ) {
			 //echo "Connection established.<br />";
			 //sqlsrv_close( $conn );
		}else{
			 if($debug==1){
				 echo "Connection could not be established.<br />";
			 }
			 else{
			 }
			 die( print_r( sqlsrv_errors(), true));
			 return false;
		}
	}
	else if($SQLtype=='sqlite'){
		if(file_exists($serverName."/".$dbName)){
			$conn=new SQLite3($serverName."/".$dbName);
			if($conn){
			}
			else{
				if($debug==1){
					echo "Connection to database failed!<br>";
				}
				else{
				}
				return false;
			}
		}
		else{
			if($debug==1){
				echo "SQLite DataBase(".$serverName."/".$dbName.") is not exist.<br>";
			}
			else{
			}
			return false;
		}
	}
	else if($SQLtype=='mysql'){
		if($dbName==''){
			$conn=mysqli_connect($serverName,$user,$password);
		}
		else{
			$conn=mysqli_connect($serverName,$user,$password,$dbName);
		}
		if (mysqli_connect_errno()){
			if($debug==1){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			else{
			}
			return false;
		}
		else{
			mysqli_query($conn,"SET NAMES 'utf8'");
		}
	}

	return $conn;
}
function sqlquery($conn,$sql,$SQLtype) {
	date_default_timezone_set('Asia/Taipei');
	$Table=array();
	if($SQLtype=='sqlserver'){
		if($conn){
			$stmt=sqlsrv_query($conn,$sql);
			if($stmt){
				while($row=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC)){
					//echo "loop is running";
					//echo $row['Name'];
					array_push($Table,$row);
				}
			}
			else{
				array_push($Table,"SQL語法錯誤");
				//return $Table;
				//echo "SQL語法錯誤!<br>".$sql;
				//die( print_r( sqlsrv_errors(), true));
			}
			//echo sizeof($Table);
			//echo $Table[0]['ID'].'<br>';
			//echo $Table[0]['Name'].'<br>';
			//echo $Table[0]['PWD'].'<br>';
			//return $Table;
		}
		else{
			array_push($Table,"連線失敗");
			//return $Table;
		}
	}
	else if($SQLtype=='sqlite'){
		if($conn){
			$conn->busyTimeout(5000);
			$conn->exec('PRAGMA journal_mode = WAL;');
			$conn->exec('PRAGMA journal_size_limit = 1000;');
			$conn->exec('PRAGMA synchronous = OFF;');
			$conn->exec('BEGIN TRANSACTION');
			$stmt=$conn->query($sql);
			$conn->exec('COMMIT');
			//$stmt=$conn->query($sql);
			if($stmt){
				while($row=$stmt->fetchArray(SQLITE3_ASSOC)){
					array_push($Table,$row);
				}
			}
			else{
				$handle=fopen('./sql.error.txt','a');
				fwrite($handle,date('Y/m/d H:i:s')." -- ".$conn->lastErrorMsg().PHP_EOL);
				fclose($handle);
				array_push($Table,"SQL語法錯誤");
			}
		}
		else{
			array_push($Table,"連線失敗");
		}
	}
	else if($SQLtype=='sqliteexec'){
		if($conn){
			$conn->busyTimeout(5000);
			$conn->exec('PRAGMA journal_mode = wal;');
			$conn->exec('PRAGMA journal_size_limit = 1000;');
			$conn->exec('PRAGMA synchronous = off;');
			$conn->exec('BEGIN TRANSACTION');
			$stmt=$conn->query($sql);
			$conn->exec('COMMIT');
			//$stmt=$conn->exec($sql);
			if($stmt){
				while($row=$stmt->fetchArray(SQLITE3_ASSOC)){
					array_push($Table,$row);
				}
			}
			else{
				$handle=fopen('./sql.error.txt','a');
				fwrite($handle,date('Y/m/d H:i:s')." -- ".$conn->lastErrorMsg().PHP_EOL);
				fclose($handle);
				array_push($Table,"SQL語法錯誤");
			}
		}
		else{
			array_push($Table,"連線失敗");
		}
	}
	else if($SQLtype=='mysql'){
		if($conn){
			$stmt=mysqli_query($conn,$sql);
			if($stmt){
				while($row=mysqli_fetch_array($stmt,MYSQLI_ASSOC)){
					//echo "loop is running";
					//echo $row['Name'];
					array_push($Table,$row);
				}
			}
			else{
				array_push($Table,"SQL語法錯誤");
				//return $Table;
				//echo "SQL語法錯誤!<br>".$sql;
				//die( print_r( sqlsrv_errors(), true));
			}
			//echo sizeof($Table);
			//echo $Table[0]['ID'].'<br>';
			//echo $Table[0]['Name'].'<br>';
			//echo $Table[0]['PWD'].'<br>';
			//return $Table;
		}
		else{
			array_push($Table,"連線失敗");
			//return $Table;
		}
	}
	
	return $Table;
}
function sqlnoresponse($conn,$sql,$SQLtype) {
	date_default_timezone_set('Asia/Taipei');
	$Table=array();
	if($SQLtype=='sqlserver'){
		if($conn){
			$stmt=sqlsrv_query($conn,$sql);
			if($stmt){
			}
			else{
				array_push($Table,"SQL語法錯誤");
				//return $Table;
				//echo "SQL語法錯誤!<br>".$sql;
				//die( print_r( sqlsrv_errors(), true));
			}
			//echo sizeof($Table);
			//echo $Table[0]['ID'].'<br>';
			//echo $Table[0]['Name'].'<br>';
			//echo $Table[0]['PWD'].'<br>';
			//return $Table;
		}
		else{
			array_push($Table,"連線失敗");
			//return $Table;
		}
	}
	else if($SQLtype=='sqlite'){
		if($conn){
			$conn->busyTimeout(5000);
			$conn->exec('PRAGMA journal_mode = wal;');
			$conn->exec('PRAGMA journal_size_limit = 1000;');
			$conn->exec('PRAGMA synchronous = NORMAL;');
			$conn->exec('BEGIN TRANSACTION');
			$stmt=$conn->query($sql);
			$conn->exec('COMMIT');
			//$stmt=$conn->query($sql);
			if($stmt){
			}
			else{
				$handle=fopen('./sql.error.txt','a');
				fwrite($handle,date('Y/m/d H:i:s')." -- ".$conn->lastErrorMsg().PHP_EOL);
				fclose($handle);
				array_push($Table,"SQL語法錯誤");
			}
		}
		else{
			array_push($Table,"連線失敗");
		}
	}
	else if($SQLtype=='sqliteexec'){//使用在大量INSERT/UPDATE的情況下
		if($conn){
			$conn->busyTimeout(5000);
			$conn->exec('PRAGMA journal_mode = wal;');
			$conn->exec('PRAGMA journal_size_limit = 1000;');
			$conn->exec('PRAGMA synchronous = off;');
			$conn->exec('BEGIN TRANSACTION');
			$stmt=$conn->exec($sql);
			$conn->EXEC('COMMIT');
			if($stmt){
			}
			else{
				$handle=fopen('./sql.error.txt','a');
				fwrite($handle,date('Y/m/d H:i:s')." -- ".$conn->lastErrorMsg().PHP_EOL);
				fclose($handle);
				array_push($Table,"SQL語法錯誤");
			}
		}
		else{
			array_push($Table,"連線失敗");
		}
	}
	else if($SQLtype=='mysql'){
		if($conn){
			$stmt=mysqli_query($conn,$sql);
			if($stmt){
			}
			else{
				array_push($Table,mysqli_error_list($conn));
				//array_push($Table,"SQL語法錯誤");
				//return $Table;
				//echo "SQL語法錯誤!<br>".$sql;
				//die( print_r( sqlsrv_errors(), true));
			}
			//echo sizeof($Table);
			//echo $Table[0]['ID'].'<br>';
			//echo $Table[0]['Name'].'<br>';
			//echo $Table[0]['PWD'].'<br>';
			//return $Table;
		}
		else{
			array_push($Table,"連線失敗");
			//return $Table;
		}
	}
	else if($SQLtype=='mysqlexec'){
		if($conn){
			mysqli_begin_transaction($conn);
			$tempsql=preg_split('/;;;/',$sql);
			for($i=0;$i<(sizeof($tempsql)-1);$i++){
				if(!mysqli_query($conn,$tempsql[$i])){
					mysqli_query($conn,'ROLLBACK');
				}
			}
			mysqli_commit($conn);  
		}
		else{
			array_push($Table,"連線失敗");
		}
	}
	else if($SQLtype=='mysqladd'){
		if($conn){
			$stmt=$conn->query($sql);
			if($stmt){
			}
			else{
				array_push($Table,mysqli_error_list($conn));
				//array_push($Table,"SQL語法錯誤");
				//return $Table;
				//echo "SQL語法錯誤!<br>".$sql;
				//die( print_r( sqlsrv_errors(), true));
			}
		}
		else{
			array_push($Table,"連線失敗");
		}
	}
	
	return $Table;
}
function sqlclose($conn,$SQLtype){
	date_default_timezone_set('Asia/Taipei');
	if($conn){
		if($SQLtype=='sqlserver'){
			sqlsrv_close( $conn );
		}
		else if($SQLtype=='sqlite'){
			$conn->close();
			unset($conn);
		}
		else if($SQLtype=='mysql'){
			mysqli_close($conn);
		}
	}
	else{
		return;
	}
	return;
}
?>