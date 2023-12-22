<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
//$sql='SELECT DISTINCT fronttype FROM itemsdata';
$sql='SELECT DISTINCT fronttype FROM itemsdata ORDER BY CAST(typeseq AS INT),CAST(fronttype AS INT) ASC';
$type=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$typename=parse_ini_file('../../../database/'.$_POST['story'].'-front.ini',true);
$targettype='';
$typelist='';
if(isset($type)&&isset($type[0]['fronttype'])){
	echo '<div id="type" style="width:100%;height:50px;overflow-x:auto;overflow-y:hidden;margin:0;position:fixed;top:60px;left:0;background-color:#ffffff;padding:5px 0;"><div style="width:max-content;overflow:hidden;padding-right:5px;">';
	for($i=0;$i<sizeof($type);$i++){
		if(isset($typename[$type[$i]['fronttype']])&&$typename[$type[$i]['fronttype']]['state']=='1'&&(!isset($typename[$type[$i]['fronttype']]['subtype'])||$typename[$type[$i]['fronttype']]['subtype']=='0')&&(!isset($typename[$type[$i]['fronttype']]['webvisible'])||$typename[$type[$i]['fronttype']]['webvisible']=='1')){//2020/3/27 增加過濾"套餐選項"類別//2020/8/31 增加顯示於手機點餐選項
			if($typelist!=''){
				$typelist.=',';
			}
			else{
			}
			$typelist.='"'.$type[$i]['fronttype'].'"';
			if($targettype!=''){
			}
			else if(isset($_POST['itemtype'])&&$_POST['itemtype']!=''){
				$targettype=$_POST['itemtype'];
			}
			else{
				$targettype=$type[$i]['fronttype'];
			}
			$typearray[$type[$i]['fronttype']]='<div class="type" name="'.$type[$i]['fronttype'].'" ';
			//echo '<div class="type" name="'.$type[$i]['fronttype'].'" ';
			if($targettype==$type[$i]['fronttype']){
				$typearray[$type[$i]['fronttype']] .= 'id="checked" ';
				//echo 'id="checked" ';
			}
			else{
			}
			$typearray[$type[$i]['fronttype']] .= '><input type="hidden" name="type" value="'.$type[$i]['fronttype'].'">'.$typename[$type[$i]['fronttype']]['name1'].'</div>';
			//echo '><input type="hidden" name="type" value="'.$type[$i]['fronttype'].'">'.$typename[$type[$i]['fronttype']]['name1'].'</div>';
		}
		else{
		}

		/*if(isset($typename[$type[$i]['fronttype']])&&$typename[$type[$i]['fronttype']]['state']=='1'&&(!isset($typename[$type[$i]['fronttype']]['subtype'])||$typename[$type[$i]['fronttype']]['subtype']=='0')){//2020/3/27 增加過濾"套餐選項"類別
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
		}*/
	}
	$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
	/*if(isset($_POST['itemtype'])&&$_POST['itemtype']!=''){
		$sql='SELECT inumber FROM itemsdata WHERE fronttype="'.$_POST['itemtype'].'" ORDER BY frontsq ASC';
	}
	else{
		$sql='SELECT inumber FROM itemsdata WHERE fronttype="'.$targettype.'" ORDER BY frontsq ASC';
	}*/
	$sql='SELECT inumber,fronttype,imgfile FROM itemsdata WHERE (state!=0 OR state IS NULL) AND fronttype IN ('.$typelist.') ORDER BY CAST(typeseq AS INT) ASC,CAST(fronttype AS INT) ASC,CAST(frontsq AS INT) ASC';//2021/6/2 撈出所有品項
	$item=sqlquery($conn,$sql,'sqlite');
	//print_r($item);
	sqlclose($conn,'sqlite');
	$itemname=parse_ini_file('../../../database/'.$_POST['story'].'-menu.ini',true);
	if(isset($item)&&isset($item[0]['inumber'])){
		$nowtypecode='';
		for($i=0;$i<sizeof($item);$i++){
			if(isset($nowtypecode[$item[$i]['fronttype']])){
			}
			else{
				$nowtypecode[$item[$i]['fronttype']]=0;
			}
			if(isset($itemname[$item[$i]['inumber']]['webvisible'])){
			}
			else{
				$itemname[$item[$i]['inumber']]['webvisible']='1';
			}
			if(isset($itemname[$item[$i]['inumber']])&&$itemname[$item[$i]['inumber']]['state']=='1'&&$itemname[$item[$i]['inumber']]['webvisible']=='1'){
				if(isset($itemarray[$item[$i]['fronttype']])){
				}
				else{
					$itemarray[$item[$i]['fronttype']]='<div class="typelabel" name="itemtype">'.$typename[$item[$i]['fronttype']]['name1'].'</div><div class="itemsbox">';
				}
				$itemarray[$item[$i]['fronttype']] .= '<div class="item" id=""><input type="hidden" name="item" value="'.$item[$i]['inumber'].'"><div id="itemcontain"><div id="itemimg">';
				if($item[$i]['imgfile']!=''&&file_exists('../../../database/img/'.$item[$i]['imgfile'])){
					$itemarray[$item[$i]['fronttype']] .= '<img class="lazy" data-src="../database/img/'.$item[$i]['imgfile'].'">';
				}
				else{
					if(file_exists('../../../database/img/empty.png')){
						$itemarray[$item[$i]['fronttype']] .= '<img class="lazy" data-src="../database/img/empty.png">';
					}
					else if(file_exists('../../img/empty.png')){
						$itemarray[$item[$i]['fronttype']] .= '<img class="lazy" data-src="./img/empty.png">';
					}
					else{
						$itemarray[$item[$i]['fronttype']] .= '';
					}
				}
				$itemarray[$item[$i]['fronttype']] .= '</div><div id="itemtext">'.$itemname[$item[$i]['inumber']]['name1'];
				if(strlen($itemname[$item[$i]['inumber']]['name2'])>0){
					$itemarray[$item[$i]['fronttype']] .= '<span id="name2">'.$itemname[$item[$i]['inumber']]['name2'].'</span>';
				}
				else{
				}
				if($itemname[$item[$i]['inumber']]['money1']!=0&&$itemname[$item[$i]['inumber']]['money1']!=''){
					$itemarray[$item[$i]['fronttype']] .= '<span id="money">＄'.number_format($itemname[$item[$i]['inumber']]['money1']).'</span>';
				}
				else{
				}
				$itemarray[$item[$i]['fronttype']] .= '</div></div></div>';
				if(isset($item[$i+1]['fronttype'])&&$item[$i]['fronttype']==$item[$i+1]['fronttype']){
				}
				else{
					$itemarray[$item[$i]['fronttype']] .= '</div>';
				}
			}
			else if(isset($item[$i+1]['fronttype'])&&$item[$i]['fronttype']!=$item[($i+1)]['fronttype']){
				if(isset($itemarray[$item[$i]['fronttype']])){
					$itemarray[$item[$i]['fronttype']] .= '</div>';
				}
				else{
					//$itemarray[$item[$i]['fronttype']]='<div class="typelabel" name="itemtype">'.$typename[$item[$i]['fronttype']]['name1'].'</div><div class="itemsbox">';
				}
			}
		}
		if(isset($itemname[$item[$i-1]['inumber']])&&$itemname[$item[$i-1]['inumber']]['state']=='1'&&$itemname[$item[$i-1]['inumber']]['webvisible']=='1'){
		}
		else{
			if(isset($itemarray[$item[$i]['fronttype']])){
				$itemarray[$item[$i]['fronttype']] .= '</div>';
			}
			else{
				//$itemarray[$item[$i]['fronttype']]='<div class="typelabel" name="itemtype">'.$typename[$item[$i]['fronttype']]['name1'].'</div><div class="itemsbox">';
			}
		}
	}
	else{
	}
	if(sizeof($typearray)>0){
		foreach($typearray as $type=>$htmlstring){
			if(isset($itemarray[$type])){
				echo $htmlstring;
			}
			else{
			}
		}
	}
	else{
	}
	echo '</div></div>';
	echo '<div id="items" style="width:100%;height:calc(100% - 180px);overflow:auto;position: fixed;top: 120px;">';
	if(sizeof($itemarray)>0){
		foreach($itemarray as $htmlstring){
			echo $htmlstring;
		}
	}
	else{
	}
	/*$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
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
	}*/
	echo '</div>';
}
else{
}
?>