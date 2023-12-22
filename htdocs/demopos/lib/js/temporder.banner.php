<?php
include_once '../../../tool/dbTool.inc.php';
//echo substr($_POST['bizdate'],0,6);
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);//2020/3/20 因為桌號改為對應方式，所以需要額外判斷是否開啟桌控
if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']=='1'){//2020/3/20 開啟桌控，在讀取桌號名稱
	$tb=parse_ini_file('../../../database/floorspend.ini',true);//2020/3/20 因為桌號改為對應方式，所以需要額外讀取桌號名稱
}
else{//2020/3/20 沒開啟桌控，顯示原本輸入桌號
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
