<?php
if(isset($_POST['company'])&&isset($_POST['dep'])){//至少需要有體系與門市代碼
	if(isset($_POST['tasteno'])){//勾選加料與備註後，需要判斷所屬群組條件
		if(file_exists('../../../database/initsetting.ini')){
			$init=parse_ini_file('../../../database/initsetting.ini',true);
		}
		else{
		}
		if(file_exists('../../../database/'.$_POST['company'].'-taste.ini')){
			$taste=parse_ini_file('../../../database/'.$_POST['company'].'-taste.ini',true);
		}
		else{
		}
		if(file_exists('../../../database/'.$_POST['company'].'-tastegroup.ini')){
			$tastegroup=parse_ini_file('../../../database/'.$_POST['company'].'-tastegroup.ini',true);
		}
		else{
		}
		if(isset($init)&&isset($init['init']['tastegroup'])&&$init['init']['tastegroup']=='1'){//使用加料與備註群組
			if(isset($taste)&&isset($tastegroup)){//同時具有加料備註與加料備註群組資料
				$artaste=array();
				for($i=0;$i<sizeof($_POST['tasteno']);$i++){
					if($_POST['tasteno'][$i]!=$_POST['target']){
						if(isset($taste[$_POST['tasteno'][$i]]['group'])){
							$artaste[$taste[$_POST['tasteno'][$i]]['group']][]=$_POST['tasteno'][$i];
						}
						else{
							$artaste['-1'][]=$_POST['tasteno'][$i];
						}
					}
					else{
					}
				}
				if(isset($taste[$_POST['target']]['group'])&&isset($tastegroup[$taste[$_POST['target']]['group']]['pos'])&&$tastegroup[$taste[$_POST['target']]['group']]['pos']!='-1'){//目標所屬群組可選數量有上限
					if(isset($artaste[$taste[$_POST['target']]['group']])&&sizeof($artaste[$taste[$_POST['target']]['group']])>=$tastegroup[$taste[$_POST['target']]['group']]['pos']){
						echo json_encode(['state'=>'fail','message'=>'group overflow']);
					}
					else{
						echo json_encode(['state'=>'pass','message'=>'group has space']);
					}
				}
				else{//目標所屬群組為無上限複選
					echo json_encode(['state'=>'pass','message'=>'infinite number']);
				}
			}
			else{//缺少資料，無法判斷
				echo json_encode(['state'=>'fail','message'=>'some data disappear']);
			}
		}
		else{//停用加料與備註群組
			echo json_encode(['state'=>'pass','message'=>'no group']);
		}
	}
	else{//無勾選加料與備註，因此無條件成功
		echo json_encode(['state'=>'pass','message'=>'taste is empty']);
	}
}
else{
}
//print_r($_POST);
?>