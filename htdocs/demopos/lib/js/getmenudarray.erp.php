<?php
include_once '../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata WHERE state IS NULL OR state="1"';
$res=sqlquery($conn,$sql,'sqlite');
//print_r($res);
sqlclose($conn,'sqlite');

$setup=parse_ini_file('../../../database/setup.ini',true);
$menu=parse_ini_file('../../../database/'.$setup['basic']['company'].'-menu.ini',true);

$data=array();
if(sizeof($res)>0){
	foreach($res as $i){
		if(isset($menu[$i['inumber']])){//存在該產品編號
			if(!isset($menu[$i['inumber']]['posvisible'])||$menu[$i['inumber']]['posvisible']=='1'){//顯示在POS的產品
				if(!isset($menu[$i['inumber']]['erpstock'])||$menu[$i['inumber']]['erpstock']=='1'){//產品於ERP計算庫存
					$index=sizeof($data);
					$data[$index]['inumber']=$i['inumber'];
					$data[$index]['name']=$menu[$i['inumber']]['name1'];
					if(isset($i['erpcode'])&&$i['erpcode']!=null){
						$data[$index]['erpcode']=$i['erpcode'];
					}
					else{
						$data[$index]['erpcode']='';
					}
				}
				else{
					//echo 'not compute erp stock.';
				}
			}
			else{
				//echo 'not view pos.';
			}
		}
		else{
			//echo 'not exists.';
		}
	}
}
else{
	//echo 'not data.';
}
echo json_encode($data);
?>