<?php
include_once '../../../tool/dbTool.inc.php';
//echo substr($_POST['bizdate'],0,6);
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);//2020/3/20 �]���ู�אּ�����覡�A�ҥH�ݭn�B�~�P�_�O�_�}�Ү౱
if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']=='1'){//2020/3/20 �}�Ү౱�A�bŪ���ู�W��
	$tb=parse_ini_file('../../../database/floorspend.ini',true);//2020/3/20 �]���ู�אּ�����覡�A�ҥH�ݭn�B�~Ū���ู�W��
}
else{//2020/3/20 �S�}�Ү౱�A��ܭ쥻��J�ู
}
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT TABLENUMBER,SALESTTLQTY,SALESTTLAMT,REMARKS,RELINVOICENUMBER FROM tempCST011 WHERE CONSECNUMBER="'.$_POST['consecnumber'].'" AND BIZDATE="'.$_POST['bizdate'].'"';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

$data[0]['TABLENAME']='';
if(isset($data[0]['TABLENUMBER'])&&$data[0]['TABLENUMBER']!=''){
	$tablelist=preg_split('/,/',$data[0]['TABLENUMBER']);
	for($i=0;$i<sizeof($tablelist);$i++){
		if(preg_match('/-/',$tablelist[$i])){
			$temp=preg_split('/-/',$tablelist[$i]);
			if(isset($tb['Tname'][$temp[0]])){
				if($data[0]['TABLENAME']!=''){
					$data[0]['TABLENAME'] .= ',';
				}
				else{
				}
				$data[0]['TABLENAME'] .= $tb['Tname'][$temp[0]].'-'.$temp[1];
			}
			else{
				if($data[0]['TABLENAME']!=''){
					$data[0]['TABLENAME'] .= ',';
				}
				else{
				}
				$data[0]['TABLENAME'] .= $temp[0].'-'.$temp[1];
			}
		}
		else{
			if(isset($tb['Tname'][$tablelist[$i]])){
				if($data[0]['TABLENAME']!=''){
					$data[0]['TABLENAME'] .= ',';
				}
				else{
				}
				$data[0]['TABLENAME'] .= $tb['Tname'][$tablelist[$i]];
			}
			else{
				if($data[0]['TABLENAME']!=''){
					$data[0]['TABLENAME'] .= ',';
				}
				else{
				}
				$data[0]['TABLENAME'] .= $tablelist[$i];
			}
		}
	}
}
else{
}
echo json_encode($data);
?>