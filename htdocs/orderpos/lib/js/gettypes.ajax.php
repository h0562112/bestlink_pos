<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT DISTINCT fronttype FROM itemsdata';
$type=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$typename=parse_ini_file('../../../database/'.$_POST['story'].'-front.ini',true);
$targettype='';
if(isset($type)&&isset($type[0]['fronttype'])){
	echo '<div id="type" style="width:100%;height:50px;overflow-x:auto;overflow-y:hidden;margin:0;position:fixed;top:60px;left:0;background-color:#ffffff;padding:5px 0;"><div style="width:max-content;overflow:hidden;padding-right:5px;">';
	for($i=0;$i<sizeof($type);$i++){
		if(isset($typename[$type[$i]['fronttype']])&&$typename[$type[$i]['fronttype']]['state']=='1'&&(!isset($typename[$type[$i]['fronttype']]['subtype'])||$typename[$type[$i]['fronttype']]['subtype']=='0')){//2020/3/27 �W�[�L�o"�M�\�ﶵ"���O
			if($targettype!=''){
			}
			else{
				$targettype=$type[$i]['fronttype'];
			}
			echo '<div class="type" ';
			if(isset($_POST['itemtype'])&&$_POST['itemtype']!=''){
				if($_POST['itemtype']==$type[$i]['fronttype']){
					echo 'id="checked" ';
				}
				else{
				}
			}
			else if($targettype==$type[$i]['fronttype']){
				echo 'id="checked" ';
			}
			else{
			}
			echo '><input type="hidden" name="type" value="'.$type[$i]['fronttype'].'">'.$typename[$type[$i]['fronttype']]['name1'].'</div>';
		}
		else{
		}
	}
	echo '</div></div>';
	echo '<div id="items" style="width:100%;height:calc(100% - 180px);overflow:auto;position: fixed;top: 120px;">';
	$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
	if(isset($_POST['itemtype'])&&$_POST['itemtype']!=''){
		$sql='SELECT inumber FROM itemsdata WHERE fronttype="'.$_POST['itemtype'].'" ORDER BY frontsq ASC';
	}
	else{
		$sql='SELECT inumber FROM itemsdata WHERE fronttype="'.$targettype.'" ORDER BY frontsq ASC';
	}
	$item=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$itemname=parse_ini_file('../../../database/'.$_POST['story'].'-menu.ini',true);
	if(isset($item)&&isset($item[0]['inumber'])){
		for($i=0;$i<sizeof($item);$i++){
			if(isset($itemname[$item[$i]['inumber']])&&$itemname[$item[$i]['inumber']]['state']=='1'){
				echo '<div class="item"><input type="hidden" name="item" value="'.$item[$i]['inumber'].'">'.$itemname[$item[$i]['inumber']]['name1'].'</div>';
			}
			else{
			}
		}
	}
	else{
	}
	echo '</div>';
}
else{
}
?>