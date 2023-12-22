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
//print_r($_POST);
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
if(isset($_POST['unitpricelink'])){//2020/8/18 修改品項
	if(!is_array($_POST['item'])){//2020/8/18 非套餐
		$mainitem=$_POST['item'];//主產品編號
		$mainunitpricelink=$_POST['unitpricelink'];//主產品價格名稱
		$mainunitprice=$_POST['unitprice'];//主產品單價
		$maintasteno=$_POST['tasteno'];//主產品點選備註編號
		$maintastemoney=$_POST['tastemoney'];//主產品點選備註小計
		$maintastename=$_POST['tastename'];//主產品點選備註清單
		$maintastenumber=$_POST['tastenumber'];//2021/6/10 主產品點選備註數量清單
	}
	else{
		$mainitem=$_POST['item'][0];//主產品編號
		$mainunitpricelink=$_POST['unitpricelink'][0];//主產品價格名稱
		$mainunitprice=$_POST['unitprice'][0];//主產品單價
		$maintasteno=$_POST['tasteno'][0];//主產品點選備註編號
		$maintastemoney=$_POST['tastemoney'][0];//主產品點選備註小計
		$maintastename=$_POST['tastename'][0];//主產品點選備註清單
		$maintastenumber=$_POST['tastenumber'][0];//2021/6/10 主產品點選備註數量清單
	}
}
else{
	$mainitem=$_POST['item'];//主產品編號
}
$sql='SELECT * FROM itemsdata WHERE inumber="'.$mainitem.'" ORDER BY frontsq ASC';
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
		if(!is_dir('../../../database/img/'.$item[0]['imgfile'])&&$item[0]['imgfile']!=''){//250*250px左右
			echo '<tr>
					<td colspan="2" style="width: 50vw;height: 25vw;background-image:url(\'../database/img/'.$item[0]['imgfile'].'\');background-size: contain;background-repeat: no-repeat;background-position: 50%;">
					</td>
				</tr>';
		}
		else{
		}
		echo '<tr>
				<td colspan="2" style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">
				<input type="hidden" name="typeno" value="'.$item[0]['fronttype'].'">
				<input type="hidden" name="type" value="">
				<input type="hidden" name="no" value="'.$mainitem.'">
				<input type="hidden" name="personcount" value="'.$itemname[$mainitem]['personcount'].'">';
		if(isset($itemname[$mainitem]['charge'])){//2020/1/30舊菜單缺少屬性值
			echo '<input type="hidden" name="needcharge" value="'.$itemname[$mainitem]['charge'].'">';
		}
		else{
			echo '<input type="hidden" name="needcharge" value="0">';
		}
		echo '<input type="hidden" name="name" value="'.$itemname[$mainitem]['name1'].'">
				<input type="hidden" name="name2" value="'.$itemname[$mainitem]['name2'].'">
				<input type="hidden" name="isgroup" value="'.$item[0]['isgroup'].'">
				<input type="hidden" name="childtype" value="'.$item[0]['childtype'].'">
				<input type="hidden" name="seq" value="'.$item[0]['frontsq'].'">';
			if(isset($itemname[$mainitem]['insaleinv'])){
				echo '<input type="hidden" name="insaleinv" value="'.$itemname[$mainitem]['insaleinv'].'">';
			}
			else{
				echo '<input type="hidden" name="insaleinv" value="1">';
			}
			echo '<input type="hidden" name="discount" value="0">
				<input type="hidden" name="discontent" value="">
				<input type="hidden" name="dis1" value="'.$itemname[$mainitem]['dis1'].'">
				<input type="hidden" name="dis2" value="'.$itemname[$mainitem]['dis2'].'">
				<input type="hidden" name="dis3" value="'.$itemname[$mainitem]['dis3'].'">
				<input type="hidden" name="dis4" value="'.$itemname[$mainitem]['dis4'].'">';
			
			$selecttype='';//2020/5/19 修改品項的狀況，紀錄贈點類別
			$selectpoint='';//2020/5/19 修改品項的狀況，紀錄贈與點數
			$moneypoint='';//2020/5/19 價格對應的贈與點數規則
			$selectmoney='';//2020/5/19 可選擇的價格名稱
			for($i=1;$i<=6;$i++){//2020/11/2 因為有價格並不是連續填入(money1、money2、money6) for($i=1;$i<=$itemname[$mainitem]['mnumber'];$i++){
				if($itemname[$mainitem]['money'.$i]==""){//2020/11/2 如果價格為空，則不顯示
					continue;
				}
				else{
					if($itemname[$mainitem]['mname'.$i.'1']==''){
						if(isset($mainunitpricelink)&&isset($mainunitprice)&&$mainunitprice==$itemname[$mainitem]['money'.$i]){
							$selectmoney .= '<option id="'.$i.'" value=";'.$itemname[$mainitem]['money'.$i].'" selected>'.$itemname[$mainitem]['money'.$i].'</option>';
							
							if(isset($itemname[$mainitem]['getpointtype'.$i])&&$itemname[$mainitem]['getpointtype'.$i]!=''){
								$selecttype=$itemname[$mainitem]['getpointtype'.$i];//2020/5/19 修改品項的狀況，紀錄贈點類別
							}
							else{
								$selecttype='1';//2020/5/19 修改品項的狀況，紀錄贈點類別
							}
							if(isset($itemname[$mainitem]['getpoint'.$i])&&$itemname[$mainitem]['getpoint'.$i]!=''){
								$selectpoint=$itemname[$mainitem]['getpoint'.$i];//2020/5/19 修改品項的狀況，紀錄贈與點數
							}
							else{
								$selectpoint='0';//2020/5/19 修改品項的狀況，紀錄贈與點數
							}
						}
						else{
							$selectmoney .= '<option id="'.$i.'" value=";'.$itemname[$mainitem]['money'.$i].'">'.$itemname[$mainitem]['money'.$i].'</option>';
						}
					}
					else{
						if(isset($mainunitpricelink)&&isset($mainunitprice)&&($mainunitpricelink.';'.$mainunitprice)==($itemname[$mainitem]['mname'.$i.'1'].';'.$itemname[$mainitem]['money'.$i])){
							$selectmoney .= '<option id="'.$i.'" value="'.$itemname[$mainitem]['mname'.$i.'1'].';'.$itemname[$mainitem]['money'.$i].'" selected>'.$itemname[$mainitem]['mname'.$i.'1'].'('.$itemname[$mainitem]['money'.$i].')</option>';

							if(isset($itemname[$mainitem]['getpointtype'.$i])&&$itemname[$mainitem]['getpointtype'.$i]!=''){
								$selecttype=$itemname[$mainitem]['getpointtype'.$i];//2020/5/19 修改品項的狀況，紀錄贈點類別
							}
							else{
								$selecttype='1';//2020/5/19 修改品項的狀況，紀錄贈點類別
							}
							if(isset($itemname[$mainitem]['getpoint'.$i])&&$itemname[$mainitem]['getpoint'.$i]!=''){
								$selectpoint=$itemname[$mainitem]['getpoint'.$i];//2020/5/19 修改品項的狀況，紀錄贈與點數
							}
							else{
								$selectpoint='0';//2020/5/19 修改品項的狀況，紀錄贈與點數
							}
						}
						else{
							$selectmoney .= '<option id="'.$i.'" value="'.$itemname[$mainitem]['mname'.$i.'1'].';'.$itemname[$mainitem]['money'.$i].'">'.$itemname[$mainitem]['mname'.$i.'1'].'('.$itemname[$mainitem]['money'.$i].')</option>';
						}
					}

					if(isset($itemname[$mainitem]['getpointtype'.$i])&&$itemname[$mainitem]['getpointtype'.$i]!=''){
						$moneypoint .= '<input type="hidden" name="getpointtype'.$i.'" value="'.$itemname[$mainitem]['getpointtype'.$i].'">';
					}
					else{
						$moneypoint .= '<input type="hidden" name="getpointtype'.$i.'" value="1">';
					}
					if(isset($itemname[$mainitem]['getpoint'.$i])&&$itemname[$mainitem]['getpoint'.$i]!=''){
						$moneypoint .= '<input type="hidden" name="getpoint'.$i.'" value="'.$itemname[$mainitem]['getpoint'.$i].'">';
					}
					else{
						$moneypoint .= '<input type="hidden" name="getpoint'.$i.'" value="0">';
					}
				}
			}

			if($selecttype==''){//2020/5/19
				if(isset($itemname[$mainitem]['getpointtype1'])&&$itemname[$mainitem]['getpointtype1']!=''){
					$selecttype=$itemname[$mainitem]['getpointtype1'];//2020/5/19 預設固定點數
				}
				else{
					$selecttype='1';//2020/5/19 預設固定點數
				}
			}
			else{
			}
			if($selectpoint==''){//2020/5/19
				if(isset($itemname[$mainitem]['getpoint1'])&&$itemname[$mainitem]['getpoint1']!=''){
					$selectpoint=$itemname[$mainitem]['getpoint1'];//2020/5/19 預設贈與點數
				}
				else{
					$selectpoint='0';//2020/5/19 預設贈與點數
				}
			}
			else{
			}

			echo '<input type="hidden" name="getpointtype" value="'.$selecttype.'">';//2020/5/19
			echo '<input type="hidden" name="initgetpoint" value="'.$selectpoint.'">';//2020/5/19
			if(isset($_POST['qty'])){
				if($_POST['qty']<=1){
					echo '<input type="hidden" name="getpoint" value="'.$selectpoint.'">';//2020/5/19
				}
				else{
					echo '<input type="hidden" name="getpoint" value="'.intval($selectpoint)*intval($_POST['qty']).'">';//2020/5/19echo $_POST['qty'];
				}
			}
			else{
				echo '<input type="hidden" name="getpoint" value="'.$selectpoint.'">';//2020/5/19
			}

			echo '<div style="width:100%;float:left;text-align:center;">'.$itemname[$mainitem]['name1'];
				if (strlen($itemname[$mainitem]['introduction1'])>0){
					echo '<br><span style="font-size:15px;color:'.$itemname[$mainitem]['introcolor1'].';">'.$itemname[$mainitem]['introduction1'].'</span>';
					if (strlen($itemname[$mainitem]['introduction2'])>0){
						echo '<br>';
					}
					echo '<span style="font-size:15px;color:'.$itemname[$mainitem]['introcolor2'].';">'.$itemname[$mainitem]['introduction2'].'</span>';
					if (strlen($itemname[$mainitem]['introduction3'])>0){
						echo '<br>';
					}
					echo '<span style="font-size:15px;color:'.$itemname[$mainitem]['introcolor3'].';">'.$itemname[$mainitem]['introduction3'].'</span>';
					if (strlen($itemname[$mainitem]['introduction4'])>0){
						echo '<br>';
					}
					echo '<span style="font-size:15px;color:'.$itemname[$mainitem]['introcolor4'].';">'.$itemname[$mainitem]['introduction4'].'</span>';
					if (strlen($itemname[$mainitem]['introduction5'])>0){
						echo '<br>';
					}
					echo '<span style="font-size:15px;color:'.$itemname[$mainitem]['introcolor5'].';">'.$itemname[$mainitem]['introduction5'].'</span>';
					if (strlen($itemname[$mainitem]['introduction6'])>0){
						echo '<br>';
					}
					echo '<span style="font-size:15px;color:'.$itemname[$mainitem]['introcolor6'].';">'.$itemname[$mainitem]['introduction6'].'</span></div></td>';
				}
				else {
					echo "<br>".$itemname[$mainitem]['name2'];
				}
			echo '</tr>';
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
		echo '<tr style="';if($itemname[$mainitem]['openmoney']=='1')echo 'display:none;';echo '">
				<td style="padding:5px;height:36px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">價格</td>
				<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">
					<select id="money" style="width:calc(100% - 12px);font-size:20px;border:1px solid #898989;border-radius:5px;padding:5px;">';

				/*$moneypoint='';//2020/5/19 價格對應的贈與點數規則//2020/5/19 因為在修改品項的狀況，需要計算點數，提前處理
				for($i=1;$i<=$itemname[$mainitem]['mnumber'];$i++){
					if($itemname[$mainitem]['mname'.$i.'1']==''){
						if(isset($mainunitpricelink)&&isset($mainunitprice)&&$mainunitprice==$itemname[$mainitem]['money'.$i]){
							echo '<option id="'.$i.'" value=";'.$itemname[$mainitem]['money'.$i].'" selected>'.$itemname[$mainitem]['money'.$i].'</option>';
						}
						else{
							echo '<option id="'.$i.'" value=";'.$itemname[$mainitem]['money'.$i].'">'.$itemname[$mainitem]['money'.$i].'</option>';
						}
					}
					else{
						if(isset($mainunitpricelink)&&isset($mainunitprice)&&($mainunitpricelink.';'.$mainunitprice)==($itemname[$mainitem]['mname'.$i.'1'].';'.$itemname[$mainitem]['money'.$i])){
							echo '<option id="'.$i.'" value="'.$itemname[$mainitem]['mname'.$i.'1'].';'.$itemname[$mainitem]['money'.$i].'" selected>'.$itemname[$mainitem]['mname'.$i.'1'].'('.$itemname[$mainitem]['money'.$i].')</option>';
						}
						else{
							echo '<option id="'.$i.'" value="'.$itemname[$mainitem]['mname'.$i.'1'].';'.$itemname[$mainitem]['money'.$i].'">'.$itemname[$mainitem]['mname'.$i.'1'].'('.$itemname[$mainitem]['money'.$i].')</option>';
						}
					}

					if(isset($itemname[$mainitem]['getpointtype'.$i])&&$itemname[$mainitem]['getpointtype'.$i]!=''){
						$moneypoint .= '<input type="hidden" name="getpointtype'.$i.'" value="'.$itemname[$mainitem]['getpointtype'.$i].'">';
					}
					else{
						$moneypoint .= '<input type="hidden" name="getpointtype'.$i.'" value="1">';
					}
					if(isset($itemname[$mainitem]['getpoint'.$i])&&$itemname[$mainitem]['getpoint'.$i]!=''){
						$moneypoint .= '<input type="hidden" name="getpoint'.$i.'" value="'.$itemname[$mainitem]['getpoint'.$i].'">';
					}
					else{
						$moneypoint .= '<input type="hidden" name="getpoint'.$i.'" value="0">';
					}
				}*/
				echo $selectmoney;//2020/5/19 select中的options
				echo '</select>';
				echo $moneypoint;
				echo '<input type="tel" id="openmoney" style="width:calc(100% - 120px);border:1px solid #171717;padding:2px 5px;border-radius:5px;text-align:right;font-size:20px;display:none;" value="'.$itemname[$mainitem]['money1'].'">
				</td>
			</tr>';
		echo '<tr style="display:none;">
				<td style="width:90px;height:36px;padding:5px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;">小計</td>
				<td style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;"><input type="number" style="width:calc(100% - 114px);height:31px;border:2px;padding:1px 0;float:left;font-size:20px;text-align:right;" name="amt" value="';
			if(isset($_POST['subtotal'])){
				echo $_POST['subtotal'];
			}
			else{
				echo $itemname[$mainitem]['money1'];
			}
			echo '" readonly></td>
			</tr>';
	$tastelist='';
	if(isset($maintasteno)&&$maintasteno!=''){
		$tastenolist=preg_split('/,/',$maintasteno);
		$tastename=preg_split('/,/',$maintastename);
		$tastenumberar=preg_split('/,/',$maintastenumber);
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
		//echo sizeof($onlytaste);
		//echo ';'.sizeof($publictaste);
	}
	else{
		echo 'display:none;';
	}
	echo '">
			<td colspan="2" style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;"><div style="width:100%;float:left;text-align:center;">客製選項:<span id="tastemoney">';
		if(isset($maintastemoney)){
			echo $maintastemoney;
		}
		else{
			echo '0';
		}
		echo '</span></div></td>
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
											<td colspan="2" style="padding:5px;height:36px;background-color: #f0f0f0;border-bottom: 1px; border-bottom-style: solid; border-bottom-color: #dcdcdc;font-size:20px;font-weight: bolder;position:relative;"><img src="./img/return.png?'.date('YmdHis').'" style="filter:invert(100%);width:16px;height:16px;padding: 3px;margin: 0;vertical-align: top;transform: rotate(90deg);"><span data-id="tastetypename">'.$tastegroupdata[$n]['name'].'</span>';
					if(isset($tastegroupdata[$n]['nidinlimitmin'])){//2021/6/22 與nidin最少數量參數共用
					//if(isset($tastegroupdata[$n]['mobilerequired'])){
						$pretastelist .= '<input type="hidden" name="mobilerequired[]" value="'.$tastegroupdata[$n]['nidinlimitmin'].'">';//2021/6/22 總數下限的標準(0:非必選；N:至少必選N項)
						if($tastegroupdata[$n]['nidinlimitmin']==0){//2021/6/10 非必選
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
						else{//2021/6/10 必選
							if($tastegroupdata[$n]['pos']=='1'){
								$pretastelist=$pretastelist.'<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>必選1項</p>';
								$pretastelist .= '<input type="hidden" name="tastelimit[]" value="'.$tastegroupdata[$n]['pos'].'">';
							}
							else if($tastegroupdata[$n]['pos']=='-1'){//2021/6/22 具有選擇範圍，就可以達到至少必選N項(有下限於總上限)//2021/6/10 必選一定要有上限
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
					else{//2021/6/10 預設非必選
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
					/*if(isset($tastegroupdata[$n]['mobiletastetype'])){//2021/6/11 更改為每個備註設定可選最大值//2021/6/10 品項複選或單選
						$pretastelist .= '<input type="hidden" name="mobiletastetype[]" value="'.$tastegroupdata[$n]['mobiletastetype'].'">';
					}
					else{
						$pretastelist .= '<input type="hidden" name="mobiletastetype[]" value="1">';
					}*/
					$pretastelist .= '<input type="hidden" name="tastesumnumber[]" value="'.$tastechecknumber.'">';//2021/6/10 該群組所選的數量
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
	echo $tastelist;

	if(!isset($item[0]['childtype'])||strlen($item[0]['childtype'])==0){//2020/7/22 無套餐品項、跟隨品項
	}
	else{
		$front=parse_ini_file('../../../database/'.$_POST['story'].'-front.ini',true);
		$spchildtype=preg_split('/,/',$item[0]['childtype']);
		$nochoseitem=array();//跟隨品項
		$choseitem=array();//套餐品項
		//print_r($spchildtype);
		foreach($spchildtype as $sptoken){
			if(preg_match('/;/',$sptoken)){
				$ttoken=preg_split('/;/',$sptoken);
				$spitem=preg_split('/-/',$ttoken[0]);
				$choseitem[$spitem[0]]['frontname']=$front[$spitem[0]]['name1'];
				$choseitem[$spitem[0]]['chosenumber']=$spitem[2];
				if(!isset($front[$spitem[0]]['required'])||$front[$spitem[0]]['required']=='1'){//2021/6/22 必選套餐選項
					$choseitem[$spitem[0]]['required']='1';
				}
				else{
					$choseitem[$spitem[0]]['required']='0';
				}
				for($i=0;$i<sizeof($ttoken);$i++){
					$t=preg_split('/-/',$ttoken[$i]);
					$choseitem[$t[0]]['items'][]=$t[1];
				}
			}
			else{
				array_push($nochoseitem,$sptoken);
			}
		}
		
		if(sizeof($nochoseitem)>0){
			$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
			$nochosediv = '<tr style="height:max-content;" class="nochoseitem">
					<td colspan="2" style="padding:5px;height:36px;background-color: #f0f0f0;border-bottom: 1px; border-bottom-style: solid; border-bottom-color: #dcdcdc;font-size:18px;position:relative;"><img src="./img/return.png?'.date('YmdHis').'" style="filter:invert(100%);width:16px;height:16px;padding: 3px;margin: 0;vertical-align: top;transform: rotate(-90deg);"><span data-id="subfixtypename">跟隨品項</span></td>
				</tr>';
			$subtimes=0;
			$itemtimes=1;
			$nochosemney=0;
			foreach($nochoseitem as $items){
				$spitems=preg_split('/-/',$items);
				$sql='SELECT * FROM itemsdata WHERE inumber="'.$spitems[1].'" ORDER BY frontsq ASC';
				$itemsdata=sqlquery($conn,$sql,'sqlite');
				$nochosediv .= '<tr id="nochoseitem" style="display:none;">
						<td style="padding:0 5px 0 15px;height:46px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;color:#9b9b9b;font-size:14px;">
							<div style="margin:5px 0;float:left;">'.$itemname[$spitems[1]]['name1'].'</div><div class="subfixitemmoney'.$itemtimes.'" style="margin:5px 0;float:left;';
							if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
								if((floatval($_POST['unitprice'][array_search($spitems[1],$_POST['item'])])+floatval($_POST['tastemoney'][array_search($spitems[1],$_POST['item'])]))>0){
									$nochosediv .= 'display:block;';
								}
								else{
									$nochosediv .= 'display:none;';
								}
								$nochosediv .= '">('; 
								$nochosediv .= (floatval($_POST['unitprice'][array_search($spitems[1],$_POST['item'])])+floatval($_POST['tastemoney'][array_search($spitems[1],$_POST['item'])]));
							}
							else{
								if($itemname[$spitems[1]]['money1']>0){
									$nochosediv .= 'display:block;';
								}
								else{
									$nochosediv .= 'display:none;';
								}
								$nochosediv .= '">('; 
								$nochosediv .= $itemname[$spitems[1]]['money1'];
								$nochosemney=floatval($nochosemney)+floatval($itemname[$spitems[1]]['money1']);
							}
						$nochosediv .= ')</div>';
					//if((isset($publictaste)&&sizeof($publictaste)>0)||strlen($itemsdata[0]['taste'])>0){//2020/8/19 避免備註順序錯亂，每個品項都開啟
						$nochosediv .= '<div class="nochoseitem'.$itemtimes.'" id="taste" style="width:max-content;margin:5px 0;border:1px solid #898989;float:left;';
								if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])&&strlen($_POST['tastenumber'][array_search($spitems[1],$_POST['item'])])>0){
									$nochosediv .= 'color:#ff0000;font-weight:bold;';
								}
								else{
									$nochosediv .= 'color:#898989;font-weight:normal;';
								}
								$nochosediv .= '">自訂備註<img src="./img/return.png?'.date('YmdHis').'" style="filter:invert(100%);width:13px;height:13px;padding: 3px;margin: 0;vertical-align: top;transform: rotate(180deg);"></div>';
					/*}
					else{
					}*/
					$itemtimes++;
					$nochosediv .= '</td>';
				$nochosediv .= '<td style="padding:0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;font-size:14px;">
						<div class="needsclick" id="" onclick="" style="display:inline-block;overflow:hidden;width:100%;height:46px;">
							<div style="width:max-content;margin:0 auto;padding:0;">
								<div id="n" style="float:left;height:46px;line-height:46px;color:#dcdcdc;font-weight:normal;margin-right:2px;">NO</div>
								<div class="switch" id="movebox">
									<input type="hidden" name="subfixtypeno[]" value="'.$itemsdata[0]['fronttype'].'">
									<input type="hidden" name="subfixtype[]" value="">
									<input type="hidden" name="subfixpersoncount[]" value="'.$itemname[$spitems[1]]['personcount'].'">
									<input type="hidden" name="subfixneedcharge[]" value="'.$itemname[$spitems[1]]['charge'].'">
									<input type="hidden" name="subfixname[]" value="'.$itemname[$spitems[1]]['name1'].'">
									<input type="hidden" name="subfixname2[]" value="'.$itemname[$spitems[1]]['name2'].'">
									<input type="hidden" name="subfixisgroup[]" value="'.$itemsdata[0]['isgroup'].'">
									<input type="hidden" name="subfixchildtype[]" value="'.$itemsdata[0]['childtype'].'">
									<input type="hidden" name="subfixinsaleinv[]" value="'.$itemname[$spitems[1]]['insaleinv'].'">
									<input type="hidden" name="subfixdiscount[]" value="">
									<input type="hidden" name="subfixdiscontent[]" value="">
									<input type="hidden" name="subfixdis1[]" value="">
									<input type="hidden" name="subfixdis2[]" value="">
									<input type="hidden" name="subfixdis3[]" value="">
									<input type="hidden" name="subfixdis4[]" value="">';

								$selecttype='';//2020/5/19 修改品項的狀況，紀錄贈點類別
								$selectpoint='';//2020/5/19 修改品項的狀況，紀錄贈與點數
								if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
									$subunitpricelink=$_POST['unitpricelink'][array_search($spitems[1],$_POST['item'])];
								}
								else{
									$subunitpricelink=$itemname[$spitems[1]]['mname11'];
								}
								if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
									$subunitprice=$_POST['unitprice'][array_search($spitems[1],$_POST['item'])];
								}
								else{
									$subunitprice=$itemname[$spitems[1]]['money1'];
								}
								for($i=1;$i<=$itemname[$spitems[1]]['mnumber'];$i++){
									if($itemname[$spitems[1]]['mname'.$i.'1']==''){
										if(isset($subunitpricelink)&&isset($subunitprice)&&$subunitprice==$itemname[$spitems[1]]['money'.$i]){
											if(isset($itemname[$spitems[1]]['getpointtype'.$i])&&$itemname[$spitems[1]]['getpointtype'.$i]!=''){
												$selecttype=$itemname[$spitems[1]]['getpointtype'.$i];//2020/5/19 修改品項的狀況，紀錄贈點類別
											}
											else{
												$selecttype='1';//2020/5/19 修改品項的狀況，紀錄贈點類別
											}
											if(isset($itemname[$spitems[1]]['getpoint'.$i])&&$itemname[$spitems[1]]['getpoint'.$i]!=''){
												$selectpoint=$itemname[$spitems[1]]['getpoint'.$i];//2020/5/19 修改品項的狀況，紀錄贈與點數
											}
											else{
												$selectpoint='0';//2020/5/19 修改品項的狀況，紀錄贈與點數
											}
										}
										else{
										}
									}
									else{
										if(isset($subunitpricelink)&&isset($subunitprice)&&($subunitpricelink.';'.$subunitprice)==($itemname[$spitems[1]]['mname'.$i.'1'].';'.$itemname[$spitems[1]]['money'.$i])){
											if(isset($itemname[$spitems[1]]['getpointtype'.$i])&&$itemname[$spitems[1]]['getpointtype'.$i]!=''){
												$selecttype=$itemname[$spitems[1]]['getpointtype'.$i];//2020/5/19 修改品項的狀況，紀錄贈點類別
											}
											else{
												$selecttype='1';//2020/5/19 修改品項的狀況，紀錄贈點類別
											}
											if(isset($itemname[$spitems[1]]['getpoint'.$i])&&$itemname[$spitems[1]]['getpoint'.$i]!=''){
												$selectpoint=$itemname[$spitems[1]]['getpoint'.$i];//2020/5/19 修改品項的狀況，紀錄贈與點數
											}
											else{
												$selectpoint='0';//2020/5/19 修改品項的狀況，紀錄贈與點數
											}
										}
										else{
										}
									}
								}

								if($selecttype==''){//2020/5/19
									if(isset($itemname[$spitems[1]]['getpointtype1'])&&$itemname[$spitems[1]]['getpointtype1']!=''){
										$selecttype=$itemname[$spitems[1]]['getpointtype1'];//2020/5/19 預設固定點數
									}
									else{
										$selecttype='1';//2020/5/19 預設固定點數
									}
								}
								else{
								}
								if($selectpoint==''){//2020/5/19
									if(isset($itemname[$spitems[1]]['getpoint1'])&&$itemname[$spitems[1]]['getpoint1']!=''){
										$selectpoint=$itemname[$spitems[1]]['getpoint1'];//2020/5/19 預設贈與點數
									}
									else{
										$selectpoint='0';//2020/5/19 預設贈與點數
									}
								}
								else{
								}
								$nochosediv .= '<input type="hidden" name="subfixgetpointtype[]" value="'.$selecttype.'">';
								$nochosediv .= '<input type="hidden" name="subfixinitgetpoint[]" value="'.$selectpoint.'">';
								$nochosediv .= '<input type="hidden" name="subfixgetpoint[]" value="'.$selectpoint.'">';

								$nochosediv .= '<input type="hidden" name="subfixmname1[]" value="';
									if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
										$nochosediv .= $_POST['unitpricelink'][array_search($spitems[1],$_POST['item'])];
									}
									else{
										$nochosediv .= $itemname[$spitems[1]]['mname11'];
									}
									$nochosediv .= '">
									<input type="hidden" name="subfixunitprice[]" value="';
									if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
										$nochosediv .= $_POST['unitprice'][array_search($spitems[1],$_POST['item'])];
									}
									else{
										$nochosediv .= $itemname[$spitems[1]]['money1'];
									}
									$nochosediv .= '">
									<input type="hidden" name="subfixtaste1[]" value="';
									if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
										$nochosediv .= $_POST['tasteno'][array_search($spitems[1],$_POST['item'])];
									}
									else{
									}
									$nochosediv .= '">
									<input type="hidden" name="subfixtaste1name[]" value="';
									if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
										$nochosediv .= $_POST['tastename'][array_search($spitems[1],$_POST['item'])];
									}
									else{
									}
									$nochosediv .= '">
									<input type="hidden" name="subfixtaste1price[]" value="';
									if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
										$nochosediv .= $_POST['tasteprice'][array_search($spitems[1],$_POST['item'])];
									}
									else{
									}
									$nochosediv .= '">
									<input type="hidden" name="subfixtaste1number[]" value="';
									if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
										$nochosediv .= $_POST['tastenumber'][array_search($spitems[1],$_POST['item'])];
									}
									else{
									}
									$nochosediv .= '">
									<input type="hidden" name="subfixtaste1money[]" value="';
									if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
										$nochosediv .= $_POST['tastemoney'][array_search($spitems[1],$_POST['item'])];
									}
									else{
										$nochosediv .= '0';
									}
									$nochosediv .= '">
									<input type="hidden" name="subfixmoney[]" value="';
									if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($spitems[1],$_POST['item'])){
										$nochosediv .= (floatval($_POST['unitprice'][array_search($spitems[1],$_POST['item'])])+floatval($_POST['tastemoney'][array_search($spitems[1],$_POST['item'])]));
									}
									else{
										$nochosediv .= $itemname[$spitems[1]]['money1'];
									}
									$nochosediv .= '">';
									//2021/6/17 檢查套餐品項中是否具有必選備註
									$nochosediv .= '<input type="hidden" name="subfixrequired[]" value="';
									if($itemsdata[0]['taste']!=''){
										$subfixgrouptaste=array();
										$subfixtasteset=preg_split('/-/',$itemsdata[0]['taste']);
										$subvarrequired=false;
										for($l=0;$l<sizeof($subfixtasteset);$l++){
											if(substr($subfixtasteset[$l],0,2)=='1;'){
												$subfixtastecont=preg_split('/;/',$subfixtasteset[0]);
												if(preg_match('/,/',$subfixtastecont[1])){
													$temp1=preg_split('/,/',$subfixtastecont[1]);
													for($t=0;$t<sizeof($temp1);$t++){
														if($taste[$temp1[$t]]['state']=='1'){
															if(isset($initsetting['init']['tastegroup'])&&$initsetting['init']['tastegroup']=='1'&&isset($tastegroupdata)&&isset($taste[$temp1[$t]]['group'])&&$taste[$temp1[$t]]['group']!=''){
																if(!isset($subfixgrouptaste[$taste[$temp1[$t]]['group']])){
																	$subfixgrouptaste[$taste[$temp1[$t]]['group']][]=$temp1[$t];
																}
																else{
																}
															}
															else{
																if(!isset($subfixgrouptaste['-1'])){
																	$subfixgrouptaste['-1'][]=$temp1[$t];
																}
																else{
																}
															}
														}
														else{
														}
													}
												}
												else{
													if($taste[$subfixtastecont[1]]['state']=='1'){
														if(isset($initsetting['init']['tastegroup'])&&$initsetting['init']['tastegroup']=='1'&&isset($tastegroupdata)&&isset($taste[$subfixtastecont[1]]['group'])&&$taste[$subfixtastecont[1]]['group']!=''){
															if(!isset($subfixgrouptaste[$taste[$subfixtastecont[1]]['group']])){
																$subfixgrouptaste[$taste[$subfixtastecont[1]]['group']][]=$subfixtastecont[1];
															}
															else{
															}
														}
														else{
															if(!isset($subfixgrouptaste['-1'])){
																$subfixgrouptaste['-1'][]=$subfixtastecont[1];
															}
															else{
															}
														}
													}
													else{
													}
												}
											}
										}
										if(sizeof($subfixgrouptaste)>0&&isset($tastegroupdata)){
											foreach($subfixgrouptaste as $g=>$x){
												if($g!='-1'&&isset($tastegroupdata[$g]['nidinlimitmin'])&&intval($tastegroupdata[$g]['nidinlimitmin'])>0){//2021/6/22 與nidin最少數量參數共用
												//if($g!='-1'&&isset($tastegroupdata[$g]['mobilerequired'])&&$tastegroupdata[$g]['mobilerequired']==1){//具有必選備註
													$subfixrequired=true;
													break;
												}
												else{
												}
											}
										}
										else{
										}
										if($subfixrequired){
											$nochosediv .= '1';
										}
										else{
											$nochosediv .= '0';
										}
									}
									else{
										$nochosediv .= '0';
									}
									$nochosediv .= '">';
									$nochosediv .= '<input name="subfixno[]" value="'.$spitems[1].'" type="checkbox" checked>
									<span class="slider"></span>
								</div>
								<div id="y" style="float:left;height:46px;line-height:46px;color:#4a4a4a;font-weight:bold;margin-left:2px;">YES</div>
							</div>
						</div>
					</td>
				</tr>';
			}
			sqlclose($conn,'sqlite');
		}
		else{
		}

		if(sizeof($nochoseitem)>0||sizeof($choseitem)>0){
			echo '<tr style="height:max-content;">
					<td colspan="2" style="padding:5px 0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;"><div style="width:100%;float:left;text-align:center;">套餐選項:<span id="totalsubmoney">';
				if(isset($_POST['item'])&&is_array($_POST['item'])){
					echo array_sum($_POST['unitprice'])+array_sum($_POST['tastemoney'])-floatval($_POST['unitprice'][0])-floatval($_POST['tastemoney'][0]);
				}
				else if(isset($nochosemney)&&$nochosemney>0){
					echo $nochosemney;
				}
				else{
					echo '0';
				}
				echo '</span></div></td>
				</tr>';
		}
		else{
		}
		if(isset($nochosediv)){
			echo $nochosediv;
		}
		else{
		}

		if(sizeof($choseitem)>0){
			$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
			foreach($choseitem as $subno=>$sub){
				$subcontent='';
				$itemtimes=1;
				foreach($sub['items'] as $item){
					$sql='SELECT * FROM itemsdata WHERE inumber="'.$item.'" ORDER BY frontsq ASC';
					$itemsdata=sqlquery($conn,$sql,'sqlite');
					for($loop=0;$loop<$sub['chosenumber'];$loop++){
						$subcontent .= '<tr id="choseitem'.$subno.'">
								<td style="padding:0 5px 0 15px;height:46px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;color:#9b9b9b;font-size:14px;">
									<div style="margin:5px 0;float:left;">'.$itemname[$item]['name1'].'</div><div class="subvaritemmoney'.$subno.$itemtimes.'" style="margin:5px 0;float:left;';
							/*if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
								$subcontent .= 'display:block;';
							}
							else{
								$subcontent .= 'display:none;';
							}
								$subcontent .= '">(';*/
							if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
								if((floatval($_POST['unitprice'][array_search($item,$_POST['item'])])+floatval($_POST['tastemoney'][array_search($item,$_POST['item'])]))>0){
									$subcontent .= 'display:block;';
								}
								else{
									$subcontent .= 'display:none;';
								}
								$subcontent .= '">(';
								$subcontent .= (floatval($_POST['unitprice'][array_search($item,$_POST['item'])])+floatval($_POST['tastemoney'][array_search($item,$_POST['item'])]));
							}
							else{
								if($itemname[$item]['money1']>0){
									$subcontent .= 'display:block;';
								}
								else{
									$subcontent .= 'display:none;';
								}
								$subcontent .= '">(';
								$subcontent .= $itemname[$item]['money1'];
							}
						$subcontent .= ')</div>';
							//if((isset($publictaste)&&sizeof($publictaste)>0)||strlen($itemsdata[0]['taste'])>0){//2020/8/19 避免備註順序錯亂，每個品項都開啟
								$subcontent .= '<div class="choseitem'.$subno.$itemtimes.'" id="taste" style="width:max-content;margin:5px 0;border:1px solid #898989;float:left;';
								if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])&&strlen($_POST['tastenumber'][array_search($item,$_POST['item'])])>0){
									$subcontent .= 'color:#ff0000;font-weight:bold;';
								}
								else{
									$subcontent .= 'color:#898989;font-weight:normal;';
								}
								if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
									$subcontent .= 'display:block;';
								}
								else{
									$subcontent .= 'display:none;';
								}
								$subcontent .= '">自訂備註<img src="./img/return.png?'.date('YmdHis').'" style="filter:invert(100%);width:13px;height:13px;padding: 3px;margin: 0;vertical-align: top;transform: rotate(180deg);"></div>';
							/*}
							else{
							}*/
							$itemtimes++;
							$subcontent .= '</td>';
						$subcontent .= '<td style="padding:0;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: #dcdcdc;font-size:14px;">
								<div class="needsclick" id="subitem" onclick="" style="display:inline-block;overflow:hidden;width:100%;height:46px;">
									<div style="width:max-content;margin:0 auto;padding:0;">
										<div id="n" style="float:left;height:46px;line-height:46px;';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= 'color:#dcdcdc;font-weight:normal;';
										}
										else{
											$subcontent .= 'color:#4a4a4a;font-weight:bold;';
										}
										$subcontent .= 'margin-right:2px;">NO</div>
										<div class="switch" id="movebox">
											<input type="hidden" name="subvartypeno[]" value="'.$itemsdata[0]['fronttype'].'">
											<input type="hidden" name="subvartype[]" value="">
											<input type="hidden" name="subvarpersoncount[]" value="'.$itemname[$item]['personcount'].'">
											<input type="hidden" name="subvarneedcharge[]" value="'.$itemname[$item]['charge'].'">
											<input type="hidden" name="subvarname[]" value="'.$itemname[$item]['name1'].'">
											<input type="hidden" name="subvarname2[]" value="'.$itemname[$item]['name2'].'">
											<input type="hidden" name="subvarisgroup[]" value="'.$itemsdata[0]['isgroup'].'">
											<input type="hidden" name="subvarchildtype[]" value="'.$itemsdata[0]['childtype'].'">
											<input type="hidden" name="subvarinsaleinv[]" value="'.$itemname[$item]['insaleinv'].'">
											<input type="hidden" name="subvardiscount[]" value="">
											<input type="hidden" name="subvardiscontent[]" value="">
											<input type="hidden" name="subvardis1[]" value="">
											<input type="hidden" name="subvardis2[]" value="">
											<input type="hidden" name="subvardis3[]" value="">
											<input type="hidden" name="subvardis4[]" value="">';
										
										$selecttype='';//2020/5/19 修改品項的狀況，紀錄贈點類別
										$selectpoint='';//2020/5/19 修改品項的狀況，紀錄贈與點數
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subunitpricelink=$_POST['unitpricelink'][array_search($item,$_POST['item'])];
										}
										else{
											$subunitpricelink=$itemname[$item]['mname11'];
										}
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subunitprice=$_POST['unitprice'][array_search($item,$_POST['item'])];
										}
										else{
											$subunitprice=$itemname[$item]['money1'];
										}
										for($i=1;$i<=$itemname[$item]['mnumber'];$i++){
											if($itemname[$item]['mname'.$i.'1']==''){
												if(isset($subunitpricelink)&&isset($subunitprice)&&$subunitprice==$itemname[$item]['money'.$i]){
													if(isset($itemname[$item]['getpointtype'.$i])&&$itemname[$item]['getpointtype'.$i]!=''){
														$selecttype=$itemname[$item]['getpointtype'.$i];//2020/5/19 修改品項的狀況，紀錄贈點類別
													}
													else{
														$selecttype='1';//2020/5/19 修改品項的狀況，紀錄贈點類別
													}
													if(isset($itemname[$item]['getpoint'.$i])&&$itemname[$item]['getpoint'.$i]!=''){
														$selectpoint=$itemname[$item]['getpoint'.$i];//2020/5/19 修改品項的狀況，紀錄贈與點數
													}
													else{
														$selectpoint='0';//2020/5/19 修改品項的狀況，紀錄贈與點數
													}
												}
												else{
												}
											}
											else{
												if(isset($subunitpricelink)&&isset($subunitprice)&&($subunitpricelink.';'.$subunitprice)==($itemname[$item]['mname'.$i.'1'].';'.$itemname[$item]['money'.$i])){
													if(isset($itemname[$item]['getpointtype'.$i])&&$itemname[$item]['getpointtype'.$i]!=''){
														$selecttype=$itemname[$item]['getpointtype'.$i];//2020/5/19 修改品項的狀況，紀錄贈點類別
													}
													else{
														$selecttype='1';//2020/5/19 修改品項的狀況，紀錄贈點類別
													}
													if(isset($itemname[$item]['getpoint'.$i])&&$itemname[$item]['getpoint'.$i]!=''){
														$selectpoint=$itemname[$item]['getpoint'.$i];//2020/5/19 修改品項的狀況，紀錄贈與點數
													}
													else{
														$selectpoint='0';//2020/5/19 修改品項的狀況，紀錄贈與點數
													}
												}
												else{
												}
											}
										}

										if($selecttype==''){//2020/5/19
											if(isset($itemname[$item]['getpointtype1'])&&$itemname[$item]['getpointtype1']!=''){
												$selecttype=$itemname[$item]['getpointtype1'];//2020/5/19 預設固定點數
											}
											else{
												$selecttype='1';//2020/5/19 預設固定點數
											}
										}
										else{
										}
										if($selectpoint==''){//2020/5/19
											if(isset($itemname[$item]['getpoint1'])&&$itemname[$item]['getpoint1']!=''){
												$selectpoint=$itemname[$item]['getpoint1'];//2020/5/19 預設贈與點數
											}
											else{
												$selectpoint='0';//2020/5/19 預設贈與點數
											}
										}
										else{
										}
										$subcontent .= '<input type="hidden" name="subvargetpointtype[]" value="'.$selecttype.'">';
										$subcontent .= '<input type="hidden" name="subvarinitgetpoint[]" value="'.$selectpoint.'">';
										$subcontent .= '<input type="hidden" name="subvargetpoint[]" value="'.$selectpoint.'">';
										
										$subcontent .= '<input type="hidden" name="subvarmname1[]" value="';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= $_POST['unitpricelink'][array_search($item,$_POST['item'])];
										}
										else{
											$subcontent .= $itemname[$item]['mname11'];
										}
										$subcontent .= '">
											<input type="hidden" name="subvarunitprice[]" value="';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= $_POST['unitprice'][array_search($item,$_POST['item'])];
										}
										else{
											$subcontent .= $itemname[$item]['money1'];
										}
										$subcontent .= '">
											<input type="hidden" name="subvartaste1[]" value="';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= $_POST['tasteno'][array_search($item,$_POST['item'])];
										}
										else{
										}
										$subcontent .= '">
											<input type="hidden" name="subvartaste1name[]" value="';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= $_POST['tastename'][array_search($item,$_POST['item'])];
										}
										else{
										}
										$subcontent .= '">
											<input type="hidden" name="subvartaste1price[]" value="';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= $_POST['tasteprice'][array_search($item,$_POST['item'])];
										}
										else{
										}
										$subcontent .= '">
											<input type="hidden" name="subvartaste1number[]" value="';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= $_POST['tastenumber'][array_search($item,$_POST['item'])];
										}
										else{
										}
										$subcontent .= '">
											<input type="hidden" name="subvartaste1money[]" value="';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= $_POST['tastemoney'][array_search($item,$_POST['item'])];
										}
										else{
											$subcontent .= '0';
										}
										$subcontent .= '">
											<input type="hidden" name="subvarmoney[]" value="';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= (floatval($_POST['unitprice'][array_search($item,$_POST['item'])])+floatval($_POST['tastemoney'][array_search($item,$_POST['item'])]));
										}
										else{
											$subcontent .= $itemname[$item]['money1'];
										}
										$subcontent .= '">';
										//2021/6/17 檢查套餐品項中是否具有必選備註
										$subcontent .= '<input type="hidden" name="subvarrequired[]" value="';
										if($itemsdata[0]['taste']!=''){
											$subvargrouptaste=array();
											$subvartasteset=preg_split('/-/',$itemsdata[0]['taste']);
											$subvarrequired=false;
											for($l=0;$l<sizeof($subvartasteset);$l++){
												if(substr($subvartasteset[$l],0,2)=='1;'){
													$subvartastecont=preg_split('/;/',$subvartasteset[0]);
													if(preg_match('/,/',$subvartastecont[1])){
														$temp1=preg_split('/,/',$subvartastecont[1]);
														for($t=0;$t<sizeof($temp1);$t++){
															if($taste[$temp1[$t]]['state']=='1'){
																if(isset($initsetting['init']['tastegroup'])&&$initsetting['init']['tastegroup']=='1'&&isset($tastegroupdata)&&isset($taste[$temp1[$t]]['group'])&&$taste[$temp1[$t]]['group']!=''){
																	if(!isset($subvargrouptaste[$taste[$temp1[$t]]['group']])){
																		$subvargrouptaste[$taste[$temp1[$t]]['group']][]=$temp1[$t];
																	}
																	else{
																	}
																}
																else{
																	if(!isset($subvargrouptaste['-1'])){
																		$subvargrouptaste['-1'][]=$temp1[$t];
																	}
																	else{
																	}
																}
															}
															else{
															}
														}
													}
													else{
														if($taste[$subvartastecont[1]]['state']=='1'){
															if(isset($initsetting['init']['tastegroup'])&&$initsetting['init']['tastegroup']=='1'&&isset($tastegroupdata)&&isset($taste[$subvartastecont[1]]['group'])&&$taste[$subvartastecont[1]]['group']!=''){
																if(!isset($subvargrouptaste[$taste[$subvartastecont[1]]['group']])){
																	$subvargrouptaste[$taste[$subvartastecont[1]]['group']][]=$subvartastecont[1];
																}
																else{
																}
															}
															else{
																if(!isset($subvargrouptaste['-1'])){
																	$subvargrouptaste['-1'][]=$subvartastecont[1];
																}
																else{
																}
															}
														}
														else{
														}
													}
												}
											}
											if(sizeof($subvargrouptaste)>0&&isset($tastegroupdata)){
												foreach($subvargrouptaste as $g=>$x){
													if($g!='-1'&&isset($tastegroupdata[$g]['nidinlimitmin'])&&intval($tastegroupdata[$g]['nidinlimitmin'])>0){//2021/6/22 與nidin最少數量參數共用
													//if($g!='-1'&&isset($tastegroupdata[$g]['mobilerequired'])&&$tastegroupdata[$g]['mobilerequired']==1){//具有必選備註
														$subvarrequired=true;
														break;
													}
													else{
													}
												}
											}
											else{
											}
											if($subvarrequired){
												$subcontent .= '1';
											}
											else{
												$subcontent .= '0';
											}
										}
										else{
											$subcontent .= '0';
										}
										$subcontent .= '">';
										$subcontent .= '<input name="subvarno[]" value="'.$item.'" type="checkbox" ';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= 'checked';
										}
										else{
										}
										$subcontent .= '>
											<span class="slider"></span>
										</div>
										<div id="y" style="float:left;height:46px;line-height:46px;';
										if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
											$subcontent .= 'color:#4a4a4a;font-weight:bold;';
										}
										else{
											$subcontent .= 'color:#dcdcdc;font-weight:normal;';
										}
										$subcontent .= 'margin-left:2px;">YES</div>
									</div>
								</div>
							</td>
						</tr>';
						
						if(isset($_POST['item'])&&is_array($_POST['item'])&&in_array($item,$_POST['item'])){
							$deleteindex=array_search($item,$_POST['item']);
							unset($_POST['unitpricelink'][$deleteindex]);
							unset($_POST['unitprice'][$deleteindex]);
							unset($_POST['tasteno'][$deleteindex]);
							unset($_POST['tastename'][$deleteindex]);
							unset($_POST['tasteprice'][$deleteindex]);
							unset($_POST['tastenumber'][$deleteindex]);
							unset($_POST['tastemoney'][$deleteindex]);
							unset($_POST['item'][$deleteindex]);
						}
						else{
						}
					}
				}
				if($subcontent!=''){
					echo '<tr style="height:max-content;" class="choseitem" id="'.$subno.'">
							<td colspan="2" style="padding:5px;height:36px;background-color: #f0f0f0;border-bottom: 1px; border-bottom-style: solid; border-bottom-color: #dcdcdc;font-size:18px;position:relative;"><img src="./img/return.png?'.date('YmdHis').'" style="filter:invert(100%);width:16px;height:16px;padding: 3px;margin: 0;vertical-align: top;transform: rotate(90deg);">';
					echo '<span data-id="subvartypename">'.$sub['frontname'].'</span><input type="hidden" name="chosenumber" value="'.$sub['chosenumber'].'"><input type="hidden" name="required" value="'.$sub['required'].'">';
					if($sub['required']){
						if($sub['chosenumber']=='1'){
							echo '<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>必選1項</p>';
						}
						else{
							echo '<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;"><span style="color:#ff0000;">＊</span>必選'.$sub['chosenumber'].'項</p>';
						}
					}
					else{
						if($sub['chosenumber']=='1'){
							echo '<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">可選1項</p>';
						}
						else{
							echo '<p style="font-size:16px;float:right;margin:0;position:absolute;bottom:5px;right:5px;color:#b5b5b5;">可選0-'.$sub['chosenumber'].'項</p>';
						}
					}
					echo '</td>
						</tr>';
					echo $subcontent;
				}
				else{
				}
			}
			sqlclose($conn,'sqlite');
		}
		else{
		}
	}

	echo '<tr style="height:max-content;"><td colspan="2" style="padding:5px;height:36px;background-color: #f0f0f0;font-size:18px;position:relative;">特殊需求</td></tr><tr><td colspan="2"><textarea placeholder="填寫其他需求(醬料多一點，不加洋蔥等)" name="othertaste" rows="3" style="width: calc(100% - 20px); margin: 0 5px; padding: 15px 0 10px 10px;resize:none;border:0px;border-bottom:1px solid #F3F3F3;font-size:14px;">';
	if(isset($tastenolist)&&in_array('99999',$tastenolist)){
		echo $tastename[array_search('99999',$tastenolist)];
	}
	else{
	}
	echo '</textarea></td></tr>';

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