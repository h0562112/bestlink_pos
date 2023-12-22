<?php
function sqlconnect($serverName,$dbName,$user,$password,$CharacterSet,$SQLtype){
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
			 echo "Connection could not be established.<br />";
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
				echo "Connection to database failed!<br>";
				return false;
			}
		}
		else{
			echo "SQLite DataBase is not exist.<br>";
			return false;
		}
	}
	else if($SQLtype=='mysql'){
		$conn=mysqli_connect($serverName,$user,$password,$dbName);
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			return false;
		}
		else{
			mysqli_query($conn,"SET NAMES 'utf8'");
		}
	}

	return $conn;
}
function sqlquery($conn,$sql,$SQLtype) {
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
			$stmt=$conn->query($sql);
			if($stmt){
				while($row=$stmt->fetchArray(SQLITE3_ASSOC)){
					array_push($Table,$row);
				}		
			}
			else{
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
			$stmt=$conn->query($sql);
			if($stmt){
			}
			else{
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
function sqlclose($conn,$SQLtype){
	if($conn){
		if($SQLtype=='sqlserver'){
			sqlsrv_close( $conn );
		}
		else if($SQLtype=='sqlite'){
			$conn->close();
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