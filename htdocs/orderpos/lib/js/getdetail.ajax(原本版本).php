<?php
include_once '../../../tool/dbTool.inc.php';
function quicksort($origArray,$type) {//快速排序//for最低價、最高價
	if (sizeof($origArray) == 1) { 
		return $origArray;
	}
	else if(sizeof($origArray) == 0){
		return 'null';
	}
	else {
		$left = array();
		$right = array();
		$newArray = array();
		$pivot = array_pop($origArray);
		$length = sizeof($origArray);
		for ($i = 0; $i < $length; $i++) {
			if (floatval($origArray[$i][$type]) <= floatval($pivot[$type])) {
				array_push($left,$origArray[$i]);
			} else {
				array_push($right,$origArray[$i]);
			}
		}
		if(sizeof($left)==0){
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort($right,$type);
				$newArray=array_merge(array($pivot),$tempright);
			}
		}
		else{
			$templeft=quicksort($left,$type);
			$n=sizeof($templeft);
			$start=$n;
			$newArray=array_merge($templeft,array($pivot));
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort($right,$type);
				$newArray=array_merge($newArray,$tempright);
			}
		}
		return $newArray;
	}
}
$conn=sqlconnect('../../../database/','menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata WHERE inumber="'.$_POST['item'].'" ORDER BY frontsq ASC';
$item=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
$itemname=parse_ini_file('../../../database/'.$_POST['story'].'-menu.ini',true);
$taste=parse_ini_file('../../../database/'.$_POST['story'].'-taste.ini',true);
if(file_exists('../../../database/'.$_POST['story'].'-tastegroup.ini')){
	$tastegroupdata=parse_ini_file('../../../database/'.$_POST['story'].'-tastegroup.ini',true);
}
else{
}
$grouptaste=array();
if(isset($item)&&isset($item[0]['inumber'])){
	echo '<form class="itemdata" method="post" action=""><div class="tastenumberbox"><div class="difftaste"><img src="./img/diff.png?'.date('YmdHis').'" style="width:100%;height:100%;object-fit: contain;"></div><input type="text" class="numberinput" value="1" readonly><input type="hidden" class="tasteindex" value=""><div class="addtaste"><img src="./img/plus.png?'.date('YmdHis').'" style="width:100%;height:100%;object-fit: contain;"></div></div>';
		if(isset($_POST['itemseq'])){
			echo '<input type="hidden" name="itemseq" value="'.$_POST['itemseq'].'">';
		}
		else{
		}
		echo '<table id="detail" style="width:calc(100% - 10px);margin:5px;border-collapse: collapse;font-size:20px;">';
		if(!is_dir('../../../management/menudata/'.$_POST['story'].'/'.$_POST['dep'].'/itemimg/'.$item[0]['imgfile'])&&$item[0]['imgfile']!=''){//250*250px左右
			echo '<tr>
					<td colspan="2" style="width: 100vw;height: 53.33vw;background-image:url(\'../management/menudata/'.$_POST['story'].'/'.$_POST['dep'].'/itemimg/'.$item[0]['imgfile'].'\');background-size: contain;background-repeat: no-repeat;background-position: 50%;">
					</td>
				</tr>';
		}
		else{
		}
		echo '<tr>
				<td colspan="2" style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">
				<input type="hidden" name="typeno" value="'.$item[0]['fronttype'].'">
				<input type="hidden" name="type" value="">
				<input type="hidden" name="no" value="'.$_POST['item'].'">
				<input type="hidden" name="personcount" value="'.$itemname[$_POST['item']]['personcount'].'">
				<input type="hidden" name="needcharge" value="'.$itemname[$_POST['item']]['charge'].'">
				<input type="hidden" name="name" value="'.$itemname[$_POST['item']]['name1'].'">
				<input type="hidden" name="name2" value="">
				<input type="hidden" name="isgroup" value="'.$item[0]['isgroup'].'">
				<input type="hidden" name="childtype" value="'.$item[0]['childtype'].'">
				<input type="hidden" name="seq" value="'.$item[0]['frontsq'].'">';
			if(isset($itemname[$_POST['item']]['insaleinv'])){
				echo '<input type="hidden" name="insaleinv" value="'.$itemname[$_POST['item']]['insaleinv'].'">';
			}
			else{
				echo '<input type="hidden" name="insaleinv" value="1">';
			}
			echo '<input type="hidden" name="discount" value="0">
				<input type="hidden" name="discontent" value="">
				<input type="hidden" name="dis1" value="'.$itemname[$_POST['item']]['dis1'].'">
				<input type="hidden" name="dis2" value="'.$itemname[$_POST['item']]['dis2'].'">
				<input type="hidden" name="dis3" value="'.$itemname[$_POST['item']]['dis3'].'">
				<input type="hidden" name="dis4" value="'.$itemname[$_POST['item']]['dis4'].'">
				<div style="width:100%;float:left;text-align:center;">'.$itemname[$_POST['item']]['name1'].'<br><span style="font-size:15px;color:#898989;">'.$itemname[$_POST['item']]['introduction1'].'</span></div></td>
			</tr>';
		echo '<tr>
				<td style="width:90px;height:36px;padding:5px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">訂購數量</td>
				<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;"><input type="tel" style="width:calc(100% - 114px);height:31px;border:2px;padding:1px 0;float:left;font-size:20px;text-align:right;" name="qty" value="';
			if(isset($_POST['qty'])){
				if($_POST['qty']<=1){
					echo '1';
				}
				else{
					echo $_POST['qty'];
				}
			}
			else{
				echo '1';
			}
			echo '" readonly><div id="diff" style="float:left;width:31px;height:31px;margin:0 12px;cursor: pointer;border-radius:100%;background-color:rgb(26,26,26,0.5);"><img src="./img/diff.png?'.date('YmdHis').'" style="width:31px;height:31px;"></div><div id="plus" style="float:left;width:31px;height:31px;margin:0 12px;cursor: pointer;border-radius:100%;background-color:rgb(26,26,26,0.5);"><img src="./img/plus.png?'.date('YmdHis').'" style="width:31px;height:31px;"></div></td>
			</tr>';
		echo '<tr>
				<td style="padding:5px;height:36px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">價格</td>
				<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">
					<select id="money" style="width:calc(100% - 12px);font-size:20px;border:1px solid #898989;border-radius:5px;padding:5px;';if($itemname[$_POST['item']]['openmoney']=='1')echo 'display:none;';echo '">';
				for($i=1;$i<=$itemname[$_POST['item']]['mnumber'];$i++){
					if($itemname[$_POST['item']]['mname'.$i.'1']==''){
						if(isset($_POST['unitpricelink'])&&isset($_POST['unitprice'])&&$_POST['unitprice']==$itemname[$_POST['item']]['money'.$i]){
							echo '<option value=";'.$itemname[$_POST['item']]['money'.$i].'" selected>'.$itemname[$_POST['item']]['money'.$i].'</option>';
						}
						else{
							echo '<option value=";'.$itemname[$_POST['item']]['money'.$i].'">'.$itemname[$_POST['item']]['money'.$i].'</option>';
						}
					}
					else{
						if(isset($_POST['unitpricelink'])&&isset($_POST['unitprice'])&&($_POST['unitpricelink'].';'.$_POST['unitprice'])==($itemname[$_POST['item']]['mname'.$i.'1'].';'.$itemname[$_POST['item']]['money'.$i])){
							echo '<option value="'.$itemname[$_POST['item']]['mname'.$i.'1'].';'.$itemname[$_POST['item']]['money'.$i].'" selected>'.$itemname[$_POST['item']]['mname'.$i.'1'].'('.$itemname[$_POST['item']]['money'.$i].')</option>';
						}
						else{
							echo '<option value="'.$itemname[$_POST['item']]['mname'.$i.'1'].';'.$itemname[$_POST['item']]['money'.$i].'">'.$itemname[$_POST['item']]['mname'.$i.'1'].'('.$itemname[$_POST['item']]['money'.$i].')</option>';
						}
					}
				}
				echo '</select>
					<input type="tel" id="openmoney" style="width:calc(100% - 120px);border:1px solid #171717;padding:2px 5px;border-radius:5px;text-align:right;font-size:20px;';if($itemname[$_POST['item']]['openmoney']=='1')echo '';else echo 'display:none;';echo '" value="'.$itemname[$_POST['item']]['money1'].'">
				</td>
			</tr>';
		//2021/7/6 跟隨orderweb
		echo '<tr style="display:none;">
				<td style="width:90px;height:36px;padding:5px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">小計</td>
				<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;"><input type="number" style="width:calc(100% - 114px);height:31px;border:2px;padding:1px 0;float:left;font-size:20px;text-align:right;" name="amt" value="';
			if(isset($_POST['subtotal'])){
				echo $_POST['subtotal'];
			}
			else{
				echo $itemname[$_POST['item']]['money1'];
			}
			echo '" readonly></td>
			</tr>';
	$tastelist='';
	if(isset($_POST['tasteno'])&&$_POST['tasteno']!=''){
		$tastenolist=preg_split('/,/',$_POST['tasteno']);
		//$tastename=preg_split('/,/',$_POST['tastename']);//2021/7/6 用於顯示開放備註，先不處理
		$tastenumberar=preg_split('/,/',$_POST['tastenumber']);//2021/7/6
	}
	else{
	}
	if(strlen($item[0]['taste'])>0){
		$tasteset=preg_split('/-/',$item[0]['taste']);
		for($l=0;$l<sizeof($tasteset);$l++){
			if(substr($tasteset[$l],0,2)=='1;'){
				$tastecont=preg_split('/;/',$tasteset[0]);
				if(preg_match('/,/',$tastecont[1])){
					$temp1=preg_split('/,/',$tastecont[1]);
					$temp11=array();
					for($t=0;$t<sizeof($temp1);$t++){
						if(!isset($taste[$temp1[$t]]['seq'])||$taste[$temp1[$t]]['seq']==''){
							$taste[$temp1[$t]]['seq']=1;
						}
						else{
						}
						array_push($temp11,array('index'=>$t,'no'=>$temp1[$t],'seq'=>$taste[$temp1[$t]]['seq']));
					}
					//print_r( $temp11);
					$onlytaste=quicksort($temp11,'seq');

				}
				else{
					$onlytaste=array();
					array_push($onlytaste,array('index'=>'0','no'=>$tastecont[1],'seq'=>$taste[$tastecont[1]]['seq']));
				}
			}
		}
	}
	else{
	}
	$temp11=array();
	foreach($taste as $n=>$t){
		if($t['public']=='1'&&$t['state']=='1'){
			if(!isset($t['seq'])||$t['seq']==''){
				array_push($temp11,array('index'=>$n,'no'=>$n,'seq'=>'1'));
			}
			else{
				array_push($temp11,array('index'=>$n,'no'=>$n,'seq'=>$t['seq']));
			}
		}
		else{
		}
		
	}
	if(isset($temp11)&&sizeof($temp11)>0){
		$publictaste=quicksort($temp11,'seq');
	}
	else{
	}
	//2021/7/6 往後移到此處，確定好備註數量後，若沒有備註則不顯示該金額欄位
	echo '<tr style="height:max-content;';
	if((isset($onlytaste)&&sizeof($onlytaste)>0)||(isset($publictaste)&&sizeof($publictaste)>0)){
	}
	else{
		echo 'display:none;';
	}
	echo '">
			<td colspan="2" style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;"><div style="width:100%;float:left;text-align:center;">客製選項:<span id="tastemoney">';
		if(isset($_POST['tastemoney'])){
			echo $_POST['tastemoney'];
		}
		else{
			echo '0';
		}
		echo '</span></div></td>
		</tr>';
	if(isset($onlytaste)&&sizeof($onlytaste)>0){
		foreach($onlytaste as $n=>$t){
			if($taste[$t['no']]['state']=='1'){
				if(isset($initsetting['init']['tastegroup'])&&$initsetting['init']['tastegroup']=='1'&&isset($tastegroupdata)&&isset($taste[$t['no']]['group'])&&$taste[$t['no']]['group']!=''){
					$grouptaste[$taste[$t['no']]['group']][]=$t['no'];
				}
				else{
					/*$tastelist=$tastelist.'<tr>
											<td style="padding:5px;height:36px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: rgba(137, 137, 137, 0.5);">'.$t['name1'];
						if(floatval($t['money'])<=0){
						}
						else{
							$tastelist=$tastelist.'('.$t['money'].')';
						}
						$tastelist=$tastelist.'</td>
											<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: rgba(137, 137, 137, 0.5);"><div id="n" style="float:left;height:33px;line-height:33px;color:#4a4a4a;font-weight:bold;">NO</div><label class="switch"><input type="hidden" name="money[]" value="'.$t['money'].'"><input type="hidden" name="tastename[]" value="'.$t['name1'].'"><input name="tasteno[]" value="'.$n.'" type="checkbox"><span class="slider"></span></label><div id="y" style="float:left;height:33px;line-height:33px;color:#898989;font-weight:normal;">YES</div></td>
										</tr>';*/
					$grouptaste['-1'][]=$t['no'];
				}
			}
			else{
			}
		}
	}
	else{
	}
	if(isset($publictaste)&&sizeof($publictaste)>0){
		foreach($publictaste as $n=>$t){
			//if($t['state']=='1'&&$t['public']=='1'){
				if(isset($initsetting['init']['tastegroup'])&&$initsetting['init']['tastegroup']=='1'&&isset($tastegroupdata)&&isset($taste[$t['no']]['group'])&&$taste[$t['no']]['group']!=''){
					$grouptaste[$taste[$t['no']]['group']][]=$t['no'];
				}
				else{
					/*$tastelist=$tastelist.'<tr>
											<td style="padding:5px;height:36px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: rgba(137, 137, 137, 0.5);">'.$t['name1'];
						if(floatval($t['money'])<=0){
						}
						else{
							$tastelist=$tastelist.'('.$t['money'].')';
						}
						$tastelist=$tastelist.'</td>
											<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: rgba(137, 137, 137, 0.5);"><div id="n" style="float:left;height:33px;line-height:33px;color:#4a4a4a;font-weight:bold;">NO</div><label class="switch"><input type="hidden" name="money[]" value="'.$t['money'].'"><input type="hidden" name="tastename[]" value="'.$t['name1'].'"><input name="tasteno[]" value="'.$n.'" type="checkbox"><span class="slider"></span></label><div id="y" style="float:left;height:33px;line-height:33px;color:#898989;font-weight:normal;">YES</div></td>
										</tr>';*/
					$grouptaste['-1'][]=$t['no'];
				}
			/*}
			else{
			}*/
		}
	}
	else{
	}
	if(isset($tastegroupdata)){
		if(sizeof($grouptaste)>0){
			foreach($grouptaste as $n=>$v){
				$pretastelist='';
				$tastelistcontent='';
				$tastenumber=0;
				$tastechecknumber=0;
				foreach($v as $ii=>$vv){
					$tastenumber++;
					if($tastenumber%3==1){
						$tastelistcontent .= '<tr id="tastelabel'.$n.'"><td colspan="2" style="padding:0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #F3F3F3;font-size:14px;"><div style="display:flex;flex-wrap:nowrap;">';//<div style="margin:5px 0;">'.$taste[$vv]['name1'];
					}
					else{
					}

					$tastelistcontent=$tastelistcontent.'<div class="needsclick" id="label" onclick="" style="width:calc(100% / 3 - 4px);min-height:50px;margin:5px 2px;float:left;border-radius:5px;display:flex;';
					if(isset($tastenolist)&&in_array($vv,$tastenolist)){
						$tastelistcontent .= 'background-color:rgb(26,26,26,0.5);color:#ffffff;';
					}
					else{
						$tastelistcontent .= 'background-color:#ffffff;color:#000000;';
					}
					$tastelistcontent .= '"><div class="switch">'.$taste[$vv]['name1'];
					if(isset($tastenolist)&&in_array($vv,$tastenolist)&&$tastenumberar[array_search($vv,$tastenolist)]>1){
						$tastelistcontent .= '<span data-id="tastenumber" style="display:contents;">*'.$tastenumberar[array_search($vv,$tastenolist)].'</span>';
					}
					else{
						$tastelistcontent .= '<span data-id="tastenumber" style="display:none;"></span>';
					}
					if(floatval($taste[$vv]['money'])==0){
					}
					else{
						$tastelistcontent=$tastelistcontent.'('.$taste[$vv]['money'].')';
					}
					$tastelistcontent=$tastelistcontent.'<input type="hidden" name="money[]" value="'.$taste[$vv]['money'].'"><input type="hidden" name="tastename[]" value="'.$taste[$vv]['name1'].'">';
					if(isset($taste[$vv]['maxlimit'])){//2021/7/6 更改為每個備註設定可選最大值
						$tastelistcontent=$tastelistcontent.'<input type="hidden" name="maxlimit[]" value="'.$taste[$vv]['maxlimit'].'">';
					}
					else{
						$tastelistcontent=$tastelistcontent.'<input type="hidden" name="maxlimit[]" value="1">';
					}
					if(isset($tastenolist)&&in_array($vv,$tastenolist)){
						$tastelistcontent=$tastelistcontent.'<input type="checkbox" name="tastenumber[]" value="'.$tastenumberar[array_search($vv,$tastenolist)].'" checked><input name="tasteno[]" value="'.$vv.'" type="checkbox" checked>';
						$tastechecknumber=intval($tastechecknumber)+intval($tastenumberar[array_search($vv,$tastenolist)]);
					}
					else{
						$tastelistcontent=$tastelistcontent.'<input type="checkbox" name="tastenumber[]" value="1"><input name="tasteno[]" value="'.$vv.'" type="checkbox">';
					}
					$tastelistcontent .= '</div></div>';
					if($tastenumber%3==0){
						$tastelistcontent .= '</div></td></tr>';
					}
					else{
					}
				}
				if($tastenumber%3>0){
					$tastelistcontent .= '</div></td></tr>';
				}
				else{
				}

				if($n!='-1'){
					$pretastelist=$pretastelist.'<tr style="height:max-content;" class="tastelabel'.$n.'">
											<td colspan="2" style="padding:5px;height:36px;background-color: #f0f0f0;border-bottom: 1px; border-bottom-style: solid; border-bottom-color: #F3F3F3;font-size:20px;font-weight: bolder;position:relative;"><img src="./img/return.png?'.date('YmdHis').'" style="filter:invert(100%);width:16px;height:16px;padding: 3px;margin: 0;vertical-align: top;transform: rotate(90deg);"><span data-id="tastetypename">'.$tastegroupdata[$n]['name'].'</span>';
					if(isset($tastegroupdata[$n]['nidinlimitmin'])){//2021/7/6 與nidin最少數量參數共用
						$pretastelist .= '<input type="hidden" name="mobilerequired[]" value="'.$tastegroupdata[$n]['nidinlimitmin'].'">';//2021/7/6 總數下限的標準(0:非必選；N:至少必選N項)
						if($tastegroupdata[$n]['nidinlimitmin']==0){//2021/7/6 非必選
							if($tastegroupdata[$n]['pos']=='1'){
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">單選</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else if($tastegroupdata[$n]['pos']=='-1'){
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">不限</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else{
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">請選0-'.$tastegroupdata[$n]['pos'].'項</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
						}
						else{//2021/7/6 必選
							if($tastegroupdata[$n]['pos']=='1'){
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>必選1項</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else if($tastegroupdata[$n]['pos']=='-1'){//2021/7/6 具有選擇範圍，就可以達到至少必選N項(有下限於總上限)
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>至少選'.$tastegroupdata[$n]['nidinlimitmin'].'項</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else{
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>';
								if($tastegroupdata[$n]['nidinlimitmin']==$tastegroupdata[$n]['pos']){
									$pretastelist=$pretastelist.'請選'.$tastegroupdata[$n]['pos'].'項';
								}
								else{
									$pretastelist=$pretastelist.'請選'.$tastegroupdata[$n]['nidinlimitmin'].'-'.$tastegroupdata[$n]['pos'].'項';
								}
								$pretastelist=$pretastelist.'</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
						}
					}
					else{//2021/7/6 預設非必選
						$pretastelist .= '<input type="hidden" name="mobilerequired[]" value="0">';
						if($tastegroupdata[$n]['pos']=='1'){
							$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">單選</p>';
							$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
						}
						else if($tastegroupdata[$n]['pos']=='-1'){
							$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">不限</p>';
							$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
						}
						else{
							$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">請選0-'.$tastegroupdata[$n]['pos'].'項</p>';
							$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
						}
					}
					$pretastelist .= '<input type="hidden" name="tastesumnumber[]" value="'.$tastechecknumber.'">';//2021/7/6 該群組所選的數量
					$pretastelist=$pretastelist.'</td>
										</tr>';
					
					$tastelist=$tastelist.$pretastelist;
				}
				else{
				}
				$tastelist=$tastelist.$tastelistcontent;
			}
		}
		else{
		}
	}
	else{
		if(isset($grouptaste['-1'])){
			$tastenumber=0;
			foreach($grouptaste['-1'] as $ii=>$vv){
				$tastenumber++;
				if($tastenumber%3==1){
					$tastelist=$tastelist.'<tr><td colspan="2" style="padding:0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #F3F3F3;font-size:14px;"><div style="display:flex;flex-wrap:nowrap;">';
				}
				else{
				}

				$tastelist=$tastelist.'<div class="needsclick" id="label" onclick=""  style="overflow:hidden;width:calc(100% / 3 - 4px);min-height:50px;margin:5px 2px;float:left;border-radius:5px;display:flex;"><div class="switch">'.$taste[$vv]['name1'];
				if(isset($tastenolist)&&in_array($vv,$tastenolist)&&$tastenumberar[array_search($vv,$tastenolist)]>1){
					$tastelist .= '<span data-id="tastenumber" style="display:contents;">*'.$tastenumberar[array_search($vv,$tastenolist)].'</span>';
				}
				else{
					$tastelist .= '<span data-id="tastenumber" style="display:none;"></span>';
				}
				if(floatval($taste[$vv]['money'])==0){
				}
				else{
					$tastelist=$tastelist.'('.$taste[$vv]['money'].')';
				}
				if(isset($tastenolist)&&in_array($vv,$tastenolist)){
					$tastelist=$tastelist.'<input type="hidden" name="money[]" value="'.$taste[$vv]['money'].'"><input type="hidden" name="tastename[]" value="'.$taste[$vv]['name1'].'"><input type="checkbox" name="tastenumber[]" value="'.$tastenumberar[array_search($vv,$tastenolist)].'" checked><input name="tasteno[]" value="'.$vv.'" type="checkbox" checked><input type="hidden" name="maxlimit[]" value="1">';
				}
				else{
					$tastelist=$tastelist.'<input type="hidden" name="money[]" value="'.$taste[$vv]['money'].'"><input type="hidden" name="tastename[]" value="'.$taste[$vv]['name1'].'"><input type="checkbox" name="tastenumber[]" value="1"><input name="tasteno[]" value="'.$vv.'" type="checkbox"><input type="hidden" name="maxlimit[]" value="1">';
				}
				$tastelist=$tastelist.'</div></div>';
				if($tastenumber%3==0){
					$tastelist .= '</div></td></tr>';
				}
				else{
				}
			}
			if($tastenumber%3==0){
				$tastelist .= '</div></td></tr>';
			}
			else{
			}
		}
		else{
		}
	}
	echo $tastelist;
	echo '</table>
			<div style="width:160px;margin:10px auto 25px auto;overflow:hidden;text-align:center;">
				<div id="diff" style="float:left;width:31px;height:31px;margin:0 12px;cursor: pointer;border-radius:100%;background-color:rgb(26,26,26,0.5);"><img src="./img/diff.png?'.date('YmdHis').'" style="width:31px;height:31px;"></div><input type="number" style="width:calc(100% - 110px);height:31px;border:2px;padding:1px 0;float:left;font-size:20px;text-align:center;" name="qty" value="';
			if(isset($_POST['qty'])){
				if($_POST['qty']<=1){
					echo '1';
				}
				else{
					echo $_POST['qty'];
				}
			}
			else{
				echo '1';
			}
			echo '" readonly><div id="plus" style="float:left;width:31px;height:31px;margin:0 12px;cursor: pointer;border-radius:100%;background-color:rgb(26,26,26,0.5);"><img src="./img/plus.png?'.date('YmdHis').'" style="width:31px;height:31px;"></div>
			</div>
			<div class="pagetop" style="width:40px;height:40px;line-height:40px;cursor: pointer;text-align:center;display:none;position:fixed;bottom:80px;right:15px;border-radius:100%;background-color: rgb(26,26,26,0.5);color: #ffffff;"><u>Top</u></div>
		</form>';
}
else{
}
?>