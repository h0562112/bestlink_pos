<?php
include '../../tool/dbTool.inc.php';
include '../../tool/inilib.php';
echo "<script src='http://code.jquery.com/jquery-1.12.2.js'></script>";
date_default_timezone_set('Asia/Taipei');
$filename='../data/'.$_POST['company'].'-menu.ini';
$newtime=date('Y-m-d H:i:s');
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='INSERT INTO itemsdata (company,inumber,fronttype,reartype,taste,sale,createtime) SELECT "'.$_POST['company'].'",COUNT(inumber),"'.$_POST['fronttype'].'","'.$_POST['reartype'].'","'.$_POST['taste'].'","'.$_POST['sale'].'","'.$newtime.'" FROM itemsdata WHERE company="'.$_POST['company'].'"';
$sql2='SELECT inumber AS number FROM itemsdata WHERE company="'.$_POST['company'].'" AND createtime="'.$newtime.'"';
sqlnoresponse($conn,$sql,'mysql');
$item=sqlquery($conn,$sql2,'mysql');
if(isset($_FILES['imgfile'])&&$_FILES['imgfile']["name"]!=""){
	$target_dir = '../img/'.$_POST['company'].'/';
	$refilename=$target_dir.$item[0]['number'].'.'.pathinfo($_FILES['imgfile']["name"], PATHINFO_EXTENSION);//更換檔名但保留原副檔名
	if(file_exists($refilename)){
		unlink($refilename);
	}
	$target_file = $target_dir . basename($_FILES['imgfile']["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES['imgfile']["tmp_name"]);
		if($check !== false) {
			//echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
	}
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES['imgfile']["tmp_name"], $refilename)) {
			//echo "The file ". basename( $_FILES["Img"]["name"]). " has been uploaded.";
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
	//$imgfile=$target_dir.$_FILES['imgfile']["name"];
	//rename(iconv("utf-8","big5",$imgfile),$refilename);
	$sql3='UPDATE itemsdata SET imgfile="'.substr($refilename,3).'" WHERE company="'.$_POST['company'].'" AND createtime="'.$newtime.'"';
	sqlnoresponse($conn,$sql3,'mysql');
}
else{
}
sqlclose($conn,'mysql');

if(file_exists($filename)){
	$content=parse_ini_file($filename,true);
	$content[$item[0]['number']]['name']=$_POST['name'];
	$content[$item[0]['number']]['image']=substr($refilename,3);
	$content[$item[0]['number']]['qrnumber']='';
	for($i=1,$j=1;$i<=9;$i++){
		if(intval($_POST['money'.$i])>0){
			$content[$item[0]['number']]['mname'.$j]=$_POST['mname'.$i];
			$content[$item[0]['number']]['money'.$j]=$_POST['money'.$i];
			$j++;
		}
		else{
		}
	}
	for(;$j<10;$j++){
		$content[$item[0]['number']]['mname'.$j]='';
		$content[$item[0]['number']]['money'.$j]='';
	}
	$content[$item[0]['number']]['counter']='-100';
	$content[$item[0]['number']]['introduction']=$_POST['introduction'];
	write_ini_file($content,$filename);
}
else{
	$content=array();
	$content[$item[0]['number']]['name']=$_POST['name'];
	$content[$item[0]['number']]['image']=substr($refilename,3);
	$content[$item[0]['number']]['qrnumber']='';
	for($i=1,$j=1;$i<=9;$i++){
		if(intval($_POST['money'.$i])>0){
			$content[$item[0]['number']]['mname'.$j]=$_POST['mname'.$i];
			$content[$item[0]['number']]['money'.$j]=$_POST['money'.$i];
			$j++;
		}
		else{
		}
	}
	for(;$j<10;$j++){
		$content[$item[0]['number']]['mname'.$j]='';
		$content[$item[0]['number']]['money'.$j]='';
	}
	$content[$item[0]['number']]['counter']='-100';
	$content[$item[0]['number']]['introduction']=$_POST['introduction'];
	write_ini_file($content,$filename);
}
echo "<form method='post' action='../management.php'>
		<input type='hidden' name='conttype' value='menu'>
	</form>
	<script>
		$('form').submit();
	</script>";
?>