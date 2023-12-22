<?php
/*流程調整，移到送出帳單時使用*/
if(file_exists('../../../ourpos/'.$_POST['story'].'/'.$_POST['dep'].'/initsetting.ini')){
	$init=parse_ini_file('../../../ourpos/'.$_POST['story'].'/'.$_POST['dep'].'/initsetting.ini',true);
	if(file_exists('../../../demopos/syspram/buttons-1.ini')){
		$buttons=parse_ini_file('../../../demopos/syspram/buttons-1.ini',true);
	}
	else{
		$buttons='-1';
	}
	if(isset($init['init']['orderlocation'])&&preg_match('/,/',$init['init']['orderlocation'])){
		$listtype=preg_split('/,/',$init['init']['orderlocation']);
		for($i=0;$i<sizeof($listtype);$i++){
			if($buttons=='-1'||!isset($buttons['name']['listtype'.$listtype[$i]])){
				if($listtype[$i]=='1'){
					//echo '<div class="listtype" style="width:calc(50% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="listtype" value="1">內用</div>';
				}
				else if($listtype[$i]=='2'){
					//echo '<div class="listtype" style="width:calc(50% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="listtype" value="2">外帶</div>';
				}
				else if($listtype[$i]=='3'){
					echo '<div class="listtype" style="width:calc(50% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="listtype" value="3">外送</div>';
				}
				else{
					echo '<div class="listtype" style="width:calc(50% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="listtype" value="4">自取</div>';
				}
			}
			else{
				if($listtype[$i]=='1'){
					//echo '<div class="listtype" style="width:calc(50% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="listtype" value="'.$listtype[$i].'">'.$buttons['name']['listtype'.$listtype[$i]].'</div>';
				}
				else if($listtype[$i]=='2'){
					//echo '<div class="listtype" style="width:calc(50% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="listtype" value="'.$listtype[$i].'">'.$buttons['name']['listtype'.$listtype[$i]].'</div>';
				}
				else if($listtype[$i]=='3'){
					echo '<div class="listtype" style="width:calc(50% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="listtype" value="'.$listtype[$i].'">'.$buttons['name']['listtype'.$listtype[$i]].'</div>';
				}
				else{
					echo '<div class="listtype" style="width:calc(50% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="listtype" value="'.$listtype[$i].'">'.$buttons['name']['listtype'.$listtype[$i]].'</div>';
				}
			}
		}
	}
	else{
		echo 'bysale';
	}
}
else{
	echo 'bysale';
}
?>