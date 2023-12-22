<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
$sql='SELECT * FROM tempcst011 WHERE SUBSTR(CUSTCODE,-(LENGTH("'.$_POST['phone'].'")+3))=";-;'.$_POST['phone'].'" ORDER BY ABS(ORDERTYPE) ASC,CREATEDATETIME DESC';
$cst011=sqlquery($conn,$sql,'mysql');
//print_r($cst011);
$sql='SELECT * FROM tempcst012 WHERE CONCAT(TERMINALNUMBER,BIZDATE,CONSECNUMBER) IN (';
for($i=0;$i<sizeof($cst011);$i++){
	if($i!=0){
		$sql .= ',"'.$cst011[$i]['TERMINALNUMBER'].$cst011[$i]['BIZDATE'].$cst011[$i]['CONSECNUMBER'].'"';
	}
	else{
		$sql .= '"'.$cst011[$i]['TERMINALNUMBER'].$cst011[$i]['BIZDATE'].$cst011[$i]['CONSECNUMBER'].'"';
	}
}
$sql .= ') ORDER BY ABS(ORDERTYPE) ASC,CREATEDATETIME DESC,CONSECNUMBER DESC,LINENUMBER ASC';
$tempcst012=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
if(sizeof($cst011)!=0){
	$depmap=array();
	//print_r($tempcst012);
	$list=array();
	for($i=0;$i<sizeof($cst011);$i++){
		if(sizeof($depmap)<=0||!isset($depmap[$cst011[$i]['TERMINALNUMBER']])){
			$conn=sqlconnect('localhost','papermanagement','paperadmin','1qaz2wsx','utf-8','mysql');
			$sql='SELECT companyname,deptname FROM userlogin WHERE company="'.$_POST['company'].'" AND dept="'.$cst011[$i]['TERMINALNUMBER'].'"';
			$dept=sqlquery($conn,$sql,'mysql');
			sqlclose($conn,'mysql');
			if(isset($dept[0]['companyname'])){
				$depmap[$cst011[$i]['TERMINALNUMBER']]=$dept[0]['companyname'].' '.$dept[0]['deptname'];
				$cst011[$i]['TERMINALNUMBER']=$depmap[$cst011[$i]['TERMINALNUMBER']];
			}
			else{
			}
		}
		else{
			$cst011[$i]['TERMINALNUMBER']=$depmap[$cst011[$i]['TERMINALNUMBER']];
		}
		$list[$cst011[$i]['CONSECNUMBER']]=$cst011[$i];
		
	}
	for($i=0;$i<sizeof($tempcst012);$i=$i+2){
		if($tempcst012[$i]['SELECTIVEITEM1']!=null){
			$taste=parse_ini_file('../../../management/menudata/'.$_POST['company'].'/'.$tempcst012[$i]['TERMINALNUMBER'].'/'.$_POST['company'].'-taste.ini',true);
			for($t=1;$t<=10;$t++){
				if($tempcst012[$i]['SELECTIVEITEM'.$t]!=null){
					$temparray[0]=substr($tempcst012[$i]['SELECTIVEITEM'.$t],0,strlen($tempcst012[$i]['SELECTIVEITEM'.$t])-1);
					$temparray[1]=substr($tempcst012[$i]['SELECTIVEITEM'.$t],-1);
					$tempcst012[$i]['SELECTIVEITEM'.$t]=$taste[intval($temparray[0])]['name1'];
					if($temparray[1]<=1){
					}
					else{
						$tempcst012[$i]['SELECTIVEITEM'.$t] .= ' X '.$temparray[1];
					}
				}
				else{
					break;
				}
			}
		}
		else{
		}
		if(!isset($list[$tempcst012[$i]['CONSECNUMBER']]['list'])){
			$list[$tempcst012[$i]['CONSECNUMBER']]['list'][0]=$tempcst012[$i];
			$list[$tempcst012[$i]['CONSECNUMBER']]['list'][0]['itemdiscount']=$tempcst012[$i+1];
		}
		else{
			$index=sizeof($list[$tempcst012[$i]['CONSECNUMBER']]['list']);
			$list[$tempcst012[$i]['CONSECNUMBER']]['list'][$index]=$tempcst012[$i];
			$list[$tempcst012[$i]['CONSECNUMBER']]['list'][$index]['itemdiscount']=$tempcst012[$i+1];
		}
	}
	echo json_encode($list);
}
else{
	echo json_encode('list is empty');
}
?>