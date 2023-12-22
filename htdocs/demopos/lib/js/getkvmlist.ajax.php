<?php
include_once '../../../tool/dbTool.inc.php';
$dir='../../../print/kvm';
if(is_dir($dir)){
}
else{
	mkdir($dir);
}
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
if(file_exists('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini')){
	$buttonname=parse_ini_file('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
}
else if('../../syspram/buttons-TW.ini'){
	$buttonname=parse_ini_file('../../syspram/buttons-TW.ini',true);
}
else{
	$buttonname=parse_ini_file('../../syspram/buttons-1.ini',true);
}
$files=scandir($dir);
for($i=2;$i<sizeof($files);$i++){
	if(substr($files[$i],-4)=='.ini'){
		$item=parse_ini_file($dir.'/'.$files[$i],true);
		$spfile=preg_split('/;/',substr($files[$i],0,(strlen($files[$i])-4)));
		$conn=sqlconnect('../../../database/sale','SALES_'.substr($spfile[0],0,6).'.db','','','','sqlite');
		$sql='SELECT REMARKS,CLKNAME,CREATEDATETIME FROM CST011 WHERE BIZDATE="'.$spfile[0].'" AND CONSECNUMBER="'.$spfile[1].'"';
		$cst011=sqlquery($conn,$sql,'sqlite');
		if(isset($cst011[0]['REMARKS'])){
			$d=$cst011;
		}
		else{
			$sql='SELECT REMARKS,CLKNAME,CREATEDATETIME FROM tempCST011 WHERE BIZDATE="'.$spfile[0].'" AND CONSECNUMBER="'.$spfile[1].'"';
			$d=sqlquery($conn,$sql,'sqlite');
		}
		$sql='SELECT saleno FROM salemap WHERE BIZDATE="'.$spfile[0].'" AND CONSECNUMBER="'.$spfile[1].'"';
		$saleno=sqlquery($conn,$sql,'sqlite');
		if(isset($saleno[0]['saleno'])){
		}
		else{
			$saleno[0]['saleno']='';
		}
		sqlclose($conn,'sqlite');
		//print_r($d);
		echo "<div class='listitems' style='padding:10px 0;overflow:hidden;border-bottom:1px solid #898989;'><div style='width:25%;float:left;min-height:1px;'>".$spfile[0]."</div>";
		echo "<div style='width:25%;float:left;min-height:1px;'>";
		if(preg_match('/-/',$d[0]['REMARKS'])){
			$temp=preg_split('/-/',$d[0]['REMARKS']);
			if($temp[0]==1){//預約內用
				echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]].intval($saleno[0]['saleno']);
			}
			else if($temp[0]==2){//預約外帶
				echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]].intval($saleno[0]['saleno']);
			}
			else if($temp[0]==3){//預約外送
				echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]].intval($saleno[0]['saleno']);
			}
			else if($temp[0]==4){//預約自取
				echo '('.$interfacename['name']['reservation'].')'.$buttonname['name']['listtype'.$temp[0]].intval($saleno[0]['saleno']);
			}
			else{//暫廢
				echo $buttonname['name']['voidtemp'].intval($saleno[0]['saleno']);
			}
		}
		else{
			if($d[0]['REMARKS']==1){//內用
				echo $buttonname['name']['listtype'.$d[0]['REMARKS']].intval($saleno[0]['saleno']);
			}
			else if($d[0]['REMARKS']==2){//外帶
				echo $buttonname['name']['listtype'.$d[0]['REMARKS']].intval($saleno[0]['saleno']);
			}
			else if($d[0]['REMARKS']==3){//外送
				echo $buttonname['name']['listtype'.$d[0]['REMARKS']].intval($saleno[0]['saleno']);
			}
			else if($d[0]['REMARKS']==4){//自取
				echo $buttonname['name']['listtype'.$d[0]['REMARKS']].intval($saleno[0]['saleno']);
			}
			else{//暫廢
				echo $buttonname['name']['voidtemp'].intval($saleno[0]['saleno']);
			}
		}
		echo "<input type='hidden' name='consecnumber' value='".$spfile[1]."'></div>";
		echo "<div style='width:25%;float:left;min-height:1px;'>".$d[0]['CLKNAME']."</div><div style='width:25%;float:left;min-height:1px;text-align:center;'>";
		if(isset($temp[1])&&preg_match('/;/',$temp[1])){
			$templisttype=preg_split('/;/',$temp[1]);
			echo $interfacename['name']['reservationdate'].substr($templisttype[0],0,8).'<br>';//預約日
		}
		else{
		}
		echo $d[0]['CREATEDATETIME']."</div>";
		echo "</div>";
	}
	else{
	}
}
?>