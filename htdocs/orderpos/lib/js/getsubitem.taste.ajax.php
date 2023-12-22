<?php
//print_r($_POST);
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
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata WHERE inumber="'.$_POST['itemno'].'" ORDER BY frontsq ASC';
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
		$selecttype='';//2020/5/19 修改品項的狀況，紀錄贈點類別
		$selectpoint='';//2020/5/19 修改品項的狀況，紀錄贈與點數
		$moneypoint='';//2020/5/19 價格對應的贈與點數規則
		$selectmoney='';//2020/5/19 可選擇的價格名稱
		for($i=1;$i<=$itemname[$_POST['itemno']]['mnumber'];$i++){
			if($itemname[$_POST['itemno']]['mname'.$i.'1']==''){
				if(isset($_POST['mname'])&&isset($_POST['money'])&&$_POST['money']==$itemname[$_POST['itemno']]['money'.$i]){
					$selectmoney .= '<option id="'.$i.'" value=";'.$itemname[$_POST['itemno']]['money'.$i].'" selected>'.$itemname[$_POST['itemno']]['money'.$i].'</option>';
					
					if(isset($itemname[$_POST['itemno']]['getpointtype'.$i])&&$itemname[$_POST['itemno']]['getpointtype'.$i]!=''){
						$selecttype=$itemname[$_POST['itemno']]['getpointtype'.$i];//2020/5/19 修改品項的狀況，紀錄贈點類別
					}
					else{
						$selecttype='1';//2020/5/19 修改品項的狀況，紀錄贈點類別
					}
					if(isset($itemname[$_POST['itemno']]['getpoint'.$i])&&$itemname[$_POST['itemno']]['getpoint'.$i]!=''){
						$selectpoint=$itemname[$_POST['itemno']]['getpoint'.$i];//2020/5/19 修改品項的狀況，紀錄贈與點數
					}
					else{
						$selectpoint='0';//2020/5/19 修改品項的狀況，紀錄贈與點數
					}
				}
				else{
					$selectmoney .= '<option id="'.$i.'" value=";'.$itemname[$_POST['itemno']]['money'.$i].'">'.$itemname[$_POST['itemno']]['money'.$i].'</option>';
				}
			}
			else{
				if(isset($_POST['mname'])&&isset($_POST['money'])&&($_POST['mname'].';'.$_POST['money'])==($itemname[$_POST['itemno']]['mname'.$i.'1'].';'.$itemname[$_POST['itemno']]['money'.$i])){
					$selectmoney .= '<option id="'.$i.'" value="'.$itemname[$_POST['itemno']]['mname'.$i.'1'].';'.$itemname[$_POST['itemno']]['money'.$i].'" selected>'.$itemname[$_POST['itemno']]['mname'.$i.'1'].'('.$itemname[$_POST['itemno']]['money'.$i].')</option>';

					if(isset($itemname[$_POST['itemno']]['getpointtype'.$i])&&$itemname[$_POST['itemno']]['getpointtype'.$i]!=''){
						$selecttype=$itemname[$_POST['itemno']]['getpointtype'.$i];//2020/5/19 修改品項的狀況，紀錄贈點類別
					}
					else{
						$selecttype='1';//2020/5/19 修改品項的狀況，紀錄贈點類別
					}
					if(isset($itemname[$_POST['itemno']]['getpoint'.$i])&&$itemname[$_POST['itemno']]['getpoint'.$i]!=''){
						$selectpoint=$itemname[$_POST['itemno']]['getpoint'.$i];//2020/5/19 修改品項的狀況，紀錄贈與點數
					}
					else{
						$selectpoint='0';//2020/5/19 修改品項的狀況，紀錄贈與點數
					}
				}
				else{
					$selectmoney .= '<option id="'.$i.'" value="'.$itemname[$_POST['itemno']]['mname'.$i.'1'].';'.$itemname[$_POST['itemno']]['money'.$i].'">'.$itemname[$_POST['itemno']]['mname'.$i.'1'].'('.$itemname[$_POST['itemno']]['money'.$i].')</option>';
				}
			}

			if(isset($itemname[$_POST['itemno']]['getpointtype'.$i])&&$itemname[$_POST['itemno']]['getpointtype'.$i]!=''){
				$moneypoint .= '<input type="hidden" name="getpointtype'.$i.'" value="'.$itemname[$_POST['itemno']]['getpointtype'.$i].'">';
			}
			else{
				$moneypoint .= '<input type="hidden" name="getpointtype'.$i.'" value="1">';
			}
			if(isset($itemname[$_POST['itemno']]['getpoint'.$i])&&$itemname[$_POST['itemno']]['getpoint'.$i]!=''){
				$moneypoint .= '<input type="hidden" name="getpoint'.$i.'" value="'.$itemname[$_POST['itemno']]['getpoint'.$i].'">';
			}
			else{
				$moneypoint .= '<input type="hidden" name="getpoint'.$i.'" value="0">';
			}
		}

		if($selecttype==''){//2020/5/19
			if(isset($itemname[$_POST['itemno']]['getpointtype1'])&&$itemname[$_POST['itemno']]['getpointtype1']!=''){
				$selecttype=$itemname[$_POST['itemno']]['getpointtype1'];//2020/5/19 預設固定點數
			}
			else{
				$selecttype='1';//2020/5/19 預設固定點數
			}
		}
		else{
		}
		if($selectpoint==''){//2020/5/19
			if(isset($itemname[$_POST['itemno']]['getpoint1'])&&$itemname[$_POST['itemno']]['getpoint1']!=''){
				$selectpoint=$itemname[$_POST['itemno']]['getpoint1'];//2020/5/19 預設贈與點數
			}
			else{
				$selectpoint='0';//2020/5/19 預設贈與點數
			}
		}
		else{
		}

		echo '<table id="detail" style="width:calc(100% - 10px);margin:5px;border-collapse: collapse;font-size:20px;">';
		echo '<tr>
				<td style="width: 90px; height: 36px; padding: 5px; border-bottom: 1px; border-bottom-style: solid; border-bottom-color: #dcdcdc;">價格</td>
				<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">
					<select id="money" style="width:calc(100% - 12px);font-size:20px;border:1px solid #898989;border-radius:5px;padding:5px;';if($itemname[$_POST['itemno']]['openmoney']=='1')echo 'display:none;';echo '">';

				echo $selectmoney;//2020/5/19 select中的options
				echo '</select>';
				echo $moneypoint;
				echo '<input type="tel" id="openmoney" style="width:calc(100% - 120px);border:1px solid #171717;padding:2px 5px;border-radius:5px;text-align:right;font-size:20px;';if($itemname[$_POST['itemno']]['openmoney']=='1')echo '';else echo 'display:none;';echo '" value="'.$itemname[$_POST['itemno']]['money1'].'">
				</td>
			</tr>';
		echo '<tr style="display:none;">
				<td style="width:90px;height:36px;padding:5px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">小計</td>
				<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;"><input type="number" style="width:calc(100% - 114px);height:31px;border:2px;padding:1px 0;float:left;font-size:20px;text-align:right;" name="amt" value="';
			if(isset($_POST['subtotal'])){
				echo $_POST['subtotal'];
			}
			else{
				echo $itemname[$_POST['itemno']]['money1'];
			}
			echo '" readonly></td>
			</tr>';
	$tastelist='';
	if(isset($_POST['tasteno'])&&$_POST['tasteno']!=''){
		$tastenolist=preg_split('/,/',$_POST['tasteno']);
		$tastename=preg_split('/,/',$_POST['tastename']);
		$tastenumberar=preg_split('/,/',$_POST['tastenumber']);
	}
	else{
	}
	if(strlen($item[0]['taste'])>0){
		$onlytaste=array();
		$tasteset=preg_split('/-/',$item[0]['taste']);
		for($l=0;$l<sizeof($tasteset);$l++){
			if(substr($tasteset[$l],0,2)=='1;'){
				$tastecont=preg_split('/;/',$tasteset[0]);
				if(preg_match('/,/',$tastecont[1])){
					$temp1=preg_split('/,/',$tastecont[1]);
					$temp11=array();
					for($t=0;$t<sizeof($temp1);$t++){
						if($taste[$temp1[$t]]['state']=='1'){
							if(!isset($taste[$temp1[$t]]['seq'])||$taste[$temp1[$t]]['seq']==''){
								$taste[$temp1[$t]]['seq']=1;
							}
							else{
							}
							array_push($temp11,array('index'=>$t,'no'=>$temp1[$t],'seq'=>$taste[$temp1[$t]]['seq']));
						}
						else{
						}
					}
					//print_r( $temp11);
					if(sizeof($temp11)>0){
						$onlytaste=quicksort($temp11,'seq');
					}
					else{
					}

				}
				else{
					if($taste[$tastecont[1]]['state']=='1'){
						if(!isset($taste[$tastecont[1]]['seq'])||$taste[$tastecont[1]]['seq']==''){
							$taste[$tastecont[1]]['seq']=1;
						}
						else{
						}
						array_push($onlytaste,array('index'=>'0','no'=>$tastecont[1],'seq'=>$taste[$tastecont[1]]['seq']));
					}
					else{
					}
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
	//2021/6/11 往後移到此處，確定好備註數量後，若沒有備註則不顯示該金額欄位
	echo '<tr style="height:max-content;';
	if((isset($onlytaste)&&sizeof($onlytaste)>0)||(isset($publictaste)&&sizeof($publictaste)>0)){
	}
	else{
		echo 'display:none;';
	}
	echo '">
			<td colspan="2" style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;"><div style="width:100%;float:left;text-align:center;">客製選項:<span id="tastemoney">'.$_POST['tastemoney'].'</span></div></td>
		</tr>';
	if(isset($onlytaste)&&sizeof($onlytaste)>0){
		foreach($onlytaste as $n=>$t){
			//2021/6/11 檢查是否顯示於手機上的流程往前提
			/*if(isset($taste[$t['no']]['webvisible'])&&$taste[$t['no']]['webvisible']=='1'){//可否顯示於手機頁面設定值判斷
				$webvisible='1';
			}
			else{
				$webvisible='0';
			}
			if($taste[$t['no']]['state']=='1'&&$webvisible=='1'){//增加了可否顯示於手機頁面設定值判斷*/
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
	if(isset($publictaste)&&sizeof($publictaste)>0){
		foreach($publictaste as $n=>$t){
			//2021/6/11 檢查是否顯示於手機上的流程往前提
			/*if(isset($taste[$t['no']]['webvisible'])&&$taste[$t['no']]['webvisible']=='1'){//可否顯示於手機頁面設定值判斷
				$webvisible='1';
			}
			else{
				$webvisible='0';
			}
			if($taste[$t['no']]['state']=='1'&&$webvisible=='1'){//增加了可否顯示於手機頁面設定值判斷*/
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
						$tastelistcontent .= '<tr id="tastelabel'.$n.'"><td colspan="2" style="padding:0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;font-size:14px;"><div style="display:flex;flex-wrap:nowrap;">';//<div style="margin:5px 0;">'.$taste[$vv]['name1'];
					}
					else{
					}
					
					//$tastelistcontent=$tastelistcontent.'</div>';
					$tastelistcontent .= '<div class="needsclick" id="label" onclick="" style="width:calc(100% / 3 - 4px);min-height:50px;margin:5px 2px;float:left;border-radius:5px;display:flex;';
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
					if(isset($taste[$vv]['maxlimit'])){//2021/6/11 更改為每個備註設定可選最大值
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
											<td colspan="2" style="padding:5px;height:36px;background-color: #f0f0f0;border-bottom: 1px; border-bottom-style: solid; border-bottom-color: #dcdcdc;font-size:18px;position:relative;"><img src="./img/return.png?'.date('YmdHis').'" style="filter:invert(100%);width:16px;height:16px;padding: 3px;margin: 0;vertical-align: top;transform: rotate(90deg);"><span data-id="tastetypename">'.$tastegroupdata[$n]['name'].'</span>';
					if(isset($tastegroupdata[$n]['nidinlimitmin'])){//2021/6/22 與nidin最少數量參數共用
					//if(isset($tastegroupdata[$n]['mobilerequired'])){
						$pretastelist .= '<input type="hidden" name="mobilerequired[]" value="'.$tastegroupdata[$n]['nidinlimitmin'].'">';//2021/6/22 總數下限的標準(0:非必選；N:至少必選N項)
						if($tastegroupdata[$n]['nidinlimitmin']==0){//2021/6/11 非必選
							if($tastegroupdata[$n]['pos']=='1'){
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">單選</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else if($tastegroupdata[$n]['pos']=='-1'){
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">不限</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else{
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">請選0-'.$tastegroupdata[$n]['pos'].'項</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
						}
						else{//2021/6/11 必選
							if($tastegroupdata[$n]['pos']=='1'){
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>必選1項</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else if($tastegroupdata[$n]['pos']=='-1'){//2021/6/11 必選一定要有上限
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>至少選'.$tastegroupdata[$n]['nidinlimitmin'].'項</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else{
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>';
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
					else{//2021/6/11 預設非必選
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
							$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">請選0-'.$tastegroupdata[$n]['pos'].'項</p>';
							$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
						}
					}
					/*if(isset($tastegroupdata[$n]['mobiletastetype'])){//2021/6/11 更改為每個備註設定可選最大值//2021/6/11 品項複選或單選
						$pretastelist .= '<input type="hidden" name="mobiletastetype[]" value="'.$tastegroupdata[$n]['mobiletastetype'].'">';
					}
					else{
						$pretastelist .= '<input type="hidden" name="mobiletastetype[]" value="1">';
					}*/
					$pretastelist .= '<input type="hidden" name="tastesumnumber[]" value="'.$tastechecknumber.'">';//2021/6/11 該群組所選的數量
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
					$tastelist=$tastelist.'<tr><td colspan="2" style="padding:0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;font-size:14px;"><div style="display:flex;flex-wrap:nowrap;">';
				}
				else{
				}
				$tastelist .= '<div class="needsclick" id="label" onclick="" style="overflow:hidden;width:calc(100% / 3 - 4px);min-height:50px;margin:5px 2px;float:left;border-radius:5px;display:flex;"><div class="switch">'.$taste[$vv]['name1'];
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
				$tastelist .= '</div></div>';
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
	echo $tastelist.'<input type="hidden" name="test">';

	echo '<tr style="height:max-content;"><td colspan="2" style="padding:5px;height:36px;background-color: #f0f0f0;font-size:18px;position:relative;">特殊指示</td></tr><tr><td colspan="2"><textarea placeholder="新增註記(醬料多一點，不加洋蔥等)" name="othertaste" rows="3" style="width: calc(100% - 20px); margin: 0 5px; padding: 15px 0 10px 10px;resize:none;border:0px;border-bottom:1px solid #dcdcdc;font-size:14px;">';
	if(isset($tastenolist)&&in_array('99999',$tastenolist)){
		echo $tastename[array_search('99999',$tastenolist)];
	}
	else{
	}
	echo '</textarea></td></tr>';

	echo '</table></form>';
}
else{
}
?>