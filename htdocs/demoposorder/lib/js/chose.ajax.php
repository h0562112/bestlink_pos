<?php
include '../../../tool/dbTool.inc.php';
$conn=sqlconnect("../../../database","menu.db","","","",'sqlite');
$sql='SELECT * FROM itemsdata WHERE inumber="'.$_POST['inumber'].'"';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(sizeof($data)==0){
}
else{
	if(file_exists('../../../database/'.$_POST['company'].'-menu.ini')){
		$itemname=parse_ini_file('../../../database/'.$_POST['company'].'-menu.ini',true);
		$data[0]['name']=(isset($itemname[$_POST['inumber']]['name1']))?($itemname[$_POST['inumber']]['name1']):('error');
		$data[0]['image']=(isset($itemname[$_POST['inumber']]['image']))?($itemname[$_POST['inumber']]['image']):('error');
		$data[0]['mcounter']=0;
		$j=0;
		for($i=1;$i<=9;$i++){
			if($itemname[$_POST['inumber']]['money'.$i]!=''){
				$data[0]['mcounter']=$data[0]['mcounter']+1;
				$j++;
				$data[0]['mname'.$j]=(isset($itemname[$_POST['inumber']]['mname'.$i.'1']))?($itemname[$_POST['inumber']]['mname'.$i.'1']):('error');
				$data[0]['money'.$j]=(isset($itemname[$_POST['inumber']]['money'.$i]))?($itemname[$_POST['inumber']]['money'.$i]):('error');
			}
			else{
			}
		}
		$data[0]['counter']=(isset($itemname[$_POST['inumber']]['counter']))?($itemname[$_POST['inumber']]['counter']):('error');
		for($i=1;$i<=6;$i++){
			if((isset($itemname[$_POST['inumber']]['introtitle'.$i])&&$itemname[$_POST['inumber']]['introtitle'.$i]!='')||(isset($itemname[$_POST['inumber']]['introduction'.$i])&&$itemname[$_POST['inumber']]['introduction'.$i]!='')){
				if(isset($itemname[$_POST['inumber']]['introtitle'.$i])&&$itemname[$_POST['inumber']]['introtitle'.$i]!=''){
					$data[0]['introduction'][$i]=$itemname[$_POST['inumber']]['introtitle'.$i].' : '.$itemname[$_POST['inumber']]['introduction'.$i];
				}
				else{
					$data[0]['introduction'][$i]=$itemname[$_POST['inumber']]['introduction'.$i];
				}
				$data[0]['introcolor'][$i]=(isset($itemname[$_POST['inumber']]['introcolor'.$i]))?($itemname[$_POST['inumber']]['introcolor'.$i]):('#000000');
			}
			else{
			}
		}
	}
	else{
		$itemname=0;
		$data[0]['message1']='DataMessageError';
	}
}
echo json_encode($data);
?>