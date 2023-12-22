<?php
include_once '../../../tool/myerrorlog.php';
function quicksort1($origArray) {//快速排序//for最低價、最高價//價格判斷包含加料金額
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
			if (floatval($origArray[$i]['money']) >= floatval($pivot['money'])) {
				array_push($left,$origArray[$i]);
			} else {
				array_push($right,$origArray[$i]);
			}
		}
		if(sizeof($left)==0){
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort1($right);
				$newArray=array_merge(array($pivot),$tempright);
			}
		}
		else{
			$templeft=quicksort1($left,0);
			$n=sizeof($templeft);
			$start=$n;
			$newArray=array_merge($templeft,array($pivot));
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort1($right);
				$newArray=array_merge($newArray,$tempright);
			}
		}
		return $newArray;
	}
}
function quicksort2($origArray) {//快速排序//for最低價、最高價//價格判斷不含加料金額
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
			if (floatval($origArray[$i]['unitprice']) >= floatval($pivot['unitprice'])) {
				array_push($left,$origArray[$i]);
			} else {
				array_push($right,$origArray[$i]);
			}
		}
		if(sizeof($left)==0){
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort2($right);
				$newArray=array_merge(array($pivot),$tempright);
			}
		}
		else{
			$templeft=quicksort2($left,0);
			$n=sizeof($templeft);
			$start=$n;
			$newArray=array_merge($templeft,array($pivot));
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort2($right);
				$newArray=array_merge($newArray,$tempright);
			}
		}
		return $newArray;
	}
}
if(preg_match('/-/',$_POST['listtype'])){
	$templisttype=preg_split('/-/',$_POST['listtype']);
	$_POST['listtype']=$templisttype[0];
}
else{
}
switch($_POST['listtype']){
	case 1:
		if(file_exists('../../../database/discount1.ini')){
			$dismethod=parse_ini_file('../../../database/discount1.ini',true);
		}
		else{
			$dismethod='';
		}
		break;
	case 2:
		if(file_exists('../../../database/discount2.ini')){
			$dismethod=parse_ini_file('../../../database/discount2.ini',true);
		}
		else{
			$dismethod='';
		}
		break;
	case 3:
		if(file_exists('../../../database/discount3.ini')){
			$dismethod=parse_ini_file('../../../database/discount3.ini',true);
		}
		else{
			$dismethod='';
		}
		break;
	case 4:
		if(file_exists('../../../database/discount4.ini')){
			$dismethod=parse_ini_file('../../../database/discount4.ini',true);
		}
		else{
			$dismethod='';
		}
		break;
	default:
		break;
}
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
date_default_timezone_set($initsetting['init']['settime']);
$today=date('w');
if($dismethod==''||sizeof($dismethod)==0){
}
else{
	$max=sizeof($_POST['no']);
	for($i=0;$i<$max;$i++){//將所有品項拆成數量1，便於將套用過優惠的產品除外
		if($_POST['number'][$i]>1){
			for(;$_POST['number'][$i]>1;$_POST['number'][$i]--){
				$_POST['no'][]=$_POST['no'][$i];
				$_POST['dis'.$_POST['listtype']][]=$_POST['dis'.$_POST['listtype']][$i];
				$_POST['discount'][]=$_POST['discount'][$i];
				$_POST['unitprice'][]=$_POST['unitprice'][$i];
				$_POST['money'][]=$_POST['money'][$i];
				$_POST['number'][]=1;
			}
		}
		else{
		}
	}
	//print_r($_POST);
	$dismoney=0;
	$discontent='';//紀錄使用哪些優惠方案
	$dispremoney='';//紀錄使用到的優惠方案分別優惠多少金額
	foreach($dismethod as $k=>$d){
		//echo $k;
		$disnumber=array();
		$itemlist=array();
		if($d['listtype']==0||(isset($d['days'])&&(!preg_match('/'.$today.'/',$d['days'])&&$d['days']!='all'))){//2022/12/1 當下非優惠日//該優惠方案已停用
		}
		else{//該優惠方案已啟用
			for($i=0;$i<sizeof($_POST['no']);$i++){
				if(isset($_POST['dis'.$_POST['listtype']][$i])&&$_POST['dis'.$_POST['listtype']][$i]!=''&&$_POST['dis'.$_POST['listtype']][$i]%intval($d['gnumber'])==0&&$_POST['number'][$i]>0){
					if($d['disitem']=='0'&&($_POST['discount'][$i]==''||intval($_POST['discount'][$i])==0)){//已折扣之商品不納入自動優惠
						array_push($itemlist,array('index'=>$i,'unitprice'=>$_POST['unitprice'][$i],'money'=>$_POST['money'][$i],'number'=>$_POST['number'][$i]));
						if(isset($disnumber[$k])){
							$disnumber[$k]['buy']=intval($disnumber[$k]['buy'])+intval($_POST['number'][$i]);
						}
						else{
							$disnumber[$k]['buy']=$_POST['number'][$i];
						}
					}
					else if($d['disitem']=='1'){//無論是否折扣接納入自動優惠
						array_push($itemlist,array('index'=>$i,'unitprice'=>$_POST['unitprice'][$i],'money'=>$_POST['money'][$i],'number'=>$_POST['number'][$i]));
						if(isset($disnumber[$k])){
							$disnumber[$k]['buy']=intval($disnumber[$k]['buy'])+intval($_POST['number'][$i]);
						}
						else{
							$disnumber[$k]['buy']=$_POST['number'][$i];
						}
					}
					else{
						continue;
					}
				}
				else{
					continue;
				}
			}
			if($d['itemtype']==1){//優惠價格為內扣
				$d['amt']=intval($d['buy'])+intval($d['free']);
			}
			else{//優惠以最相近之品項
			}
			if(isset($disnumber[$k]['buy'])&&$d['amt']<=$disnumber[$k]['buy']){//符合該優惠方案之商品數量滿足優惠方案之條件
				if($d['listtype']==-1){//該帳單不限制優惠方案執行次數
					$times=intval($disnumber[$k]['buy']/$d['amt'])*$d['free'];
				}
				else if((intval($disnumber[$k]['buy']/$d['amt'])*$d['free'])<=($d['listtype']*$d['free'])){
					$times=intval($disnumber[$k]['buy']/$d['amt'])*$d['free'];
				}
				else{
					$times=$d['listtype']*$d['free'];
				}
				if($d['taste']==1){//含加料之價格作優惠
					$sort=quicksort1($itemlist);
				}
				else{//以原價之價格作優惠
					$sort=quicksort2($itemlist);
				}
				//設定起始值與遞增條件(從後面往前掃，因此起始值與遞增會以最大值與負值)
				if($d['group']==1){//分群
					if($d['type']==1||$d['type']==2){//最低價、最高價
						if($d['gpstart']==1){//分群起始值：由高到低
							$t=0;
							$add=1;
						}
						else{//分群起始值：由低到高
							$t=sizeof($sort)-1;
							$add=-1;
						}
					}
					else{
					}
				}
				else{//不分群
					if($d['type']==1){//最低價
						$t=sizeof($sort)-1;
						$add=-1;
					}
					else if($d['type']==2){//最高價
						$t=0;
						$add=1;
					}
					else{
					}
				}
				$count=0;//計算執行優惠方案之次數
				if($d['distype']=='4'){//2020/11/27 均價
					if($d['type']==1||$d['type']==2){//最低價、最高價
						$itemnumber=$sort[$t]['number'];
						//for(;$t<sizeof($sort)&&$t>=0;$t=$t+intval($add)){
						if($times>0){
							if(strlen($discontent)==0){//紀錄使用哪些優惠方案
								$discontent=$k;
							}
							else{
								$discontent=$discontent.','.$k;
							}
						}
						else{
						}
						$premoney=0;//計算該優惠方案總優惠金額
						for($gi=$times;$gi>0;$gi--){
							$abstotal=0;//2020/11/27 計算符合優惠條件的品項總額
							if($d['taste']==1){//含加料之價格作優惠
								for($absi=($t+($gi-1)*$add*$d['amt']);($absi*$add)<(($t+$gi*$add*$d['amt'])*$add);$absi=$absi+$add){
									$abstotal=floatval($abstotal)+floatval($sort[$absi]['money']);

									//2021/9/8 扣除已餐與優惠的品項
									$_POST['dis'.$_POST['listtype']][$sort[$absi]['index']]='';
								}
								//$dismoney=floatval($dismoney)+floatval($abstotal/$d['amt']);
								$premoney=floatval($premoney)+floatval($abstotal/$d['amt']);
							}
							else{//以原價之價格作優惠
								for($absi=($t+($gi-1)*$add*$d['amt']);($absi*$add)<(($t+$gi*$add*$d['amt'])*$add);$absi=$absi+$add){
									$abstotal=floatval($abstotal)+floatval($sort[$absi]['unitprice']);

									//2021/9/8 扣除已餐與優惠的品項
									$_POST['dis'.$_POST['listtype']][$sort[$absi]['index']]='';
								}
								//$dismoney=floatval($dismoney)+floatval($abstotal/$d['amt']);
								$premoney=floatval($premoney)+floatval($abstotal/$d['amt']);
							}
							$count++;

							if($count>=$times){
								$dismoney=floatval($dismoney)+round($premoney,$initsetting['init']['accuracy']);
								if(strlen($dispremoney)==0){//記錄每個優惠方案分別優惠多少金額
									$dispremoney=round($premoney,$initsetting['init']['accuracy']);
								}
								else{
									$dispremoney=$dispremoney.','.round($premoney,$initsetting['init']['accuracy']);
								}
								break;
							}
							else{
								continue;
							}
						}
						if($count>=$times){
							continue;
						}
						else{
							$dismoney=floatval($dismoney)+round($premoney,$initsetting['init']['accuracy']);
							if(strlen($dispremoney)==0){//記錄每個優惠方案分別優惠多少金額
								$dispremoney=round($premoney,$initsetting['init']['accuracy']);
							}
							else{
								$dispremoney=$dispremoney.','.round($premoney,$initsetting['init']['accuracy']);
							}
						}
					}
					else{
					}
				}
				else if($d['group']==1){//分群
					if(($d['type']==1&&$d['gpstart']==1)||($d['type']==2&&$d['gpstart']==2)){//最低價＆分群起始值：由高到低、最高價＆分群起始值：由低到高
							
						$gpnumber=0;//分群
						$premoney=0;//計算該優惠方案總優惠金額

						$gpnumber=intval($gpnumber)+intval($sort[$t]['number']);
						for(;$t<sizeof($sort)&&$t>=0;$t=intval($t)+intval($add)){
							if($gpnumber<intval($d['amt'])){
							}
							else if($gpnumber==intval($d['amt'])){
								for($index=0;$index<$d['free'];$index++){
									if($d['taste']==1){//含加料之價格作優惠
										if($d['max']=='-1'){//折讓金額無上限
											if(!isset($d['distype'])||$d['distype']=='1'){//折讓
												$dismoney=floatval($dismoney)+$sort[$t-$index*$add]['money'];
												$premoney=floatval($premoney)+$sort[$t-$index*$add]['money'];
											}
											else if($d['distype']=='2'){//折扣
												$dismoney=floatval($dismoney)+floatval($sort[$t-$index*$add]['money']*(100-$d['dismoney'])/100);
												$premoney=floatval($premoney)+floatval($sort[$t-$index*$add]['money']*(100-$d['dismoney'])/100);
											}
											else if($d['distype']=='3'){//單一價
												$dismoney=floatval($dismoney)+$sort[$t-$index*$add]['money']-$d['dismoney'];
												$premoney=floatval($premoney)+$sort[$t-$index*$add]['money']-$d['dismoney'];
											}
										}
										else{
											if(floatval($d['max'])<floatval($sort[$t-$index*$add]['money'])){//超出折讓上限以上限金額優惠
												$dismoney=intval($dismoney)+floatval($d['max']);
												$premoney=intval($premoney)+floatval($d['max']);
											}
											else{
												$dismoney=intval($dismoney)+$sort[$t-$index*$add]['money'];
												$premoney=intval($premoney)+$sort[$t-$index*$add]['money'];
											}
										}
									}
									else{//以原價之價格作優惠
										if($d['max']=='-1'){//折讓金額無上限
											if(!isset($d['distype'])||$d['distype']=='1'){//折讓
												$dismoney=floatval($dismoney)+$sort[$t-$index*$add]['unitprice'];
												$premoney=floatval($premoney)+$sort[$t-$index*$add]['unitprice'];
											}
											else if($d['distype']=='2'){//折扣
												$dismoney=floatval($dismoney)+floatval($sort[$t-$index*$add]['unitprice']*(100-$d['dismoney'])/100);
												$premoney=floatval($premoney)+floatval($sort[$t-$index*$add]['unitprice']*(100-$d['dismoney'])/100);
											}
											else if($d['distype']=='3'){//單一價
												$dismoney=floatval($dismoney)+$sort[$t-$index*$add]['unitprice']-$d['dismoney'];
												$premoney=floatval($premoney)+$sort[$t-$index*$add]['unitprice']-$d['dismoney'];
											}
										}
										else{
											if(floatval($d['max'])<floatval($sort[$t-$index*$add]['unitprice'])){//超出折讓上限以上限金額優惠
												$dismoney=intval($dismoney)+floatval($d['max']);
												$premoney=intval($premoney)+floatval($d['max']);
											}
											else{
												$dismoney=intval($dismoney)+$sort[$t-$index*$add]['unitprice'];
												$premoney=intval($premoney)+$sort[$t-$index*$add]['unitprice'];
											}
										}
									}
									$count++;
								}
								for($recover=0;$recover<$gpnumber;$recover++){
									$_POST['number'][$sort[$t-$recover*$add]['index']]--;
								}
								$gpnumber=0;
								if($count>=$times){
									break;
								}
								else{
								}
							}
							else{
							}
							if(isset($sort[$t+intval($add)])){
								$gpnumber=intval($gpnumber)+intval($sort[$t+intval($add)]['number']);

								//2021/9/8 扣除已餐與優惠的品項
								$_POST['dis'.$_POST['listtype']][$sort[$t+intval($add)]['index']]='';
							}
							else{
							}
							if($count>=$times){
								break;
							}
							else{
							}
						}
						if(floatval($premoney)!=0){//折扣金額不為0表示有執行折扣
							if(strlen($discontent)==0){//紀錄使用哪些優惠方案
								$discontent=$k;
							}
							else{
								$discontent=$discontent.','.$k;
							}
							if(strlen($dispremoney)==0){//記錄每個優惠方案分別優惠多少金額
								$dispremoney=$premoney;
							}
							else{
								$dispremoney=$dispremoney.','.$premoney;
							}
						}
						else{
						}

					}
					else if(($d['type']==1&&$d['gpstart']==2)||($d['type']==2&&$d['gpstart']==1)){//最低價＆分群起始值：由低到高、最高價＆分群起始值：由高到低

						$gpnumber=0;//分群
						$premoney=0;//計算該優惠方案總優惠金額

						$gpnumber=intval($gpnumber)+intval($sort[$t]['number']);
						for(;$t<sizeof($sort)&&$t>=0;$t=intval($t)+intval($add)){
							if($gpnumber<intval($d['amt'])){
							}
							else if($gpnumber==intval($d['amt'])){
								for($index=0;$index<$d['free'];$index++){
									if($d['taste']==1){//含加料之價格作優惠
										if($d['max']=='-1'){//折讓金額無上限
											if(!isset($d['distype'])||$d['distype']=='1'){//折讓
												$dismoney=floatval($dismoney)+$sort[$t-($gpnumber-$index-1)*$add]['money'];
												$premoney=floatval($premoney)+$sort[$t-($gpnumber-$index-1)*$add]['money'];
											}
											else if($d['distype']=='2'){//折扣
												$dismoney=floatval($dismoney)+floatval($sort[$t-($gpnumber-$index-1)*$add]['money']*(100-$d['dismoney'])/100);
												$premoney=floatval($premoney)+floatval($sort[$t-($gpnumber-$index-1)*$add]['money']*(100-$d['dismoney'])/100);
											}
											else if($d['distype']=='3'){//單一價
												$dismoney=floatval($dismoney)+$sort[$t-($gpnumber-$index-1)*$add]['money']-$d['dismoney'];
												$premoney=floatval($premoney)+$sort[$t-($gpnumber-$index-1)*$add]['money']-$d['dismoney'];
											}
										}
										else{
											if(floatval($d['max'])<floatval($sort[$t-($gpnumber-$index-1)*$add]['money'])){//超出折讓上限以上限金額優惠
												$dismoney=floatval($dismoney)+floatval($d['max']);
												$premoney=floatval($premoney)+floatval($d['max']);
											}
											else{
												$dismoney=floatval($dismoney)+$sort[$t-($gpnumber-$index-1)*$add]['money'];
												$premoney=floatval($premoney)+$sort[$t-($gpnumber-$index-1)*$add]['money'];
											}
										}
									}
									else{//以原價之價格作優惠
										if($d['max']=='-1'){//折讓金額無上限
											if(!isset($d['distype'])||$d['distype']=='1'){//折讓
												$dismoney=floatval($dismoney)+$sort[$t-($gpnumber-$index-1)*$add]['unitprice'];
												$premoney=floatval($premoney)+$sort[$t-($gpnumber-$index-1)*$add]['unitprice'];
											}
											else if($d['distype']=='2'){//折扣
												$dismoney=floatval($dismoney)+floatval($sort[$t-($gpnumber-$index-1)*$add]['unitprice']*(100-$d['dismoney'])/100);
												$premoney=floatval($premoney)+floatval($sort[$t-($gpnumber-$index-1)*$add]['unitprice']*(100-$d['dismoney'])/100);
											}
											else if($d['distype']=='3'){//單一價
												$dismoney=floatval($dismoney)+$sort[$t-($gpnumber-$index-1)*$add]['unitprice']-$d['dismoney'];
												$premoney=floatval($premoney)+$sort[$t-($gpnumber-$index-1)*$add]['unitprice']-$d['dismoney'];
											}
										}
										else{
											if(floatval($d['max'])<floatval($sort[$t-($gpnumber-$index-1)*$add]['unitprice'])){//超出折讓上限以上限金額優惠
												$dismoney=floatval($dismoney)+floatval($d['max']);
												$premoney=floatval($premoney)+floatval($d['max']);
											}
											else{
												$dismoney=floatval($dismoney)+$sort[$t-($gpnumber-$index-1)*$add]['unitprice'];
												$premoney=floatval($premoney)+$sort[$t-($gpnumber-$index-1)*$add]['unitprice'];
											}
										}
									}
									$count++;
								}
								for($recover=0;$recover<$gpnumber;$recover++){
									$_POST['number'][$sort[$t-$recover*$add]['index']]--;
								}
								$gpnumber=0;
								if($count>=$times){
									break;
								}
								else{
								}
							}
							else{
							}
							if(isset($sort[$t+intval($add)])){
								$gpnumber=intval($gpnumber)+intval($sort[$t+intval($add)]['number']);

								//2021/9/8 扣除已餐與優惠的品項
								$_POST['dis'.$_POST['listtype']][$sort[$t+intval($add)]['index']]='';
							}
							else{
							}
							if($count>=$times){
								break;
							}
							else{
							}
						}
						if(floatval($premoney)!=0){//折扣金額不為0表示有執行折扣
							if(strlen($discontent)==0){//紀錄使用哪些優惠方案
								$discontent=$k;
							}
							else{
								$discontent=$discontent.','.$k;
							}
							if(strlen($dispremoney)==0){//記錄每個優惠方案分別優惠多少金額
								$dispremoney=$premoney;
							}
							else{
								$dispremoney=$dispremoney.','.$premoney;
							}
						}
						else{
						}
					}
					else{
					}
				}
				else{//不分群
					if($d['type']==1||$d['type']==2){//最低價、最高價
						$itemnumber=$sort[$t]['number'];
						//for(;$t<sizeof($sort)&&$t>=0;$t=$t+intval($add)){
						if($times>0){
							if(strlen($discontent)==0){//紀錄使用哪些優惠方案
								$discontent=$k;
							}
							else{
								$discontent=$discontent.','.$k;
							}
						}
						else{
						}
						$premoney=0;//計算該優惠方案總優惠金額
						for($gi=$times;$gi>0;$gi--){
							if($d['taste']==1){//含加料之價格作優惠
								if($d['max']=='-1'){//折讓金額無上限
									if(!isset($d['distype'])||$d['distype']=='1'){//折讓
										$dismoney=floatval($dismoney)+$sort[$t+($gi-1)*$add]['money'];
										$premoney=floatval($premoney)+$sort[$t+($gi-1)*$add]['money'];
									}
									else if($d['distype']=='2'){//折扣
										$dismoney=floatval($dismoney)+floatval($sort[$t+($gi-1)*$add]['money']*(100-$d['dismoney'])/100);
										$premoney=floatval($premoney)+floatval($sort[$t+($gi-1)*$add]['money']*(100-$d['dismoney'])/100);
									}
									else if($d['distype']=='3'){//單一價
										$dismoney=floatval($dismoney)+$sort[$t+($gi-1)*$add]['money']-$d['dismoney'];
										$premoney=floatval($premoney)+$sort[$t+($gi-1)*$add]['money']-$d['dismoney'];
									}
								}
								else{
									if(floatval($d['max'])<floatval($sort[$t+($gi-1)*$add]['money'])){//超出折讓上限以上限金額優惠
										$dismoney=floatval($dismoney)+floatval($d['max']);
										$premoney=floatval($premoney)+floatval($d['max']);
									}
									else{
										$dismoney=floatval($dismoney)+$sort[$t+($gi-1)*$add]['money'];
										$premoney=floatval($premoney)+$sort[$t+($gi-1)*$add]['money'];
									}
								}
							}
							else{//以原價之價格作優惠
								if($d['max']=='-1'){//折讓金額無上限
									if(!isset($d['distype'])||$d['distype']=='1'){//折讓
										$dismoney=floatval($dismoney)+$sort[$t+($gi-1)*$add]['unitprice'];
										$premoney=floatval($premoney)+$sort[$t+($gi-1)*$add]['unitprice'];
									}
									else if($d['distype']=='2'){//折扣
										$dismoney=floatval($dismoney)+floatval($sort[$t+($gi-1)*$add]['unitprice']*(100-$d['dismoney'])/100);
										$premoney=floatval($premoney)+floatval($sort[$t+($gi-1)*$add]['unitprice']*(100-$d['dismoney'])/100);
									}
									else if($d['distype']=='3'){//單一價
										$dismoney=floatval($dismoney)+$sort[$t+($gi-1)*$add]['unitprice']-$d['dismoney'];
										$premoney=floatval($premoney)+$sort[$t+($gi-1)*$add]['unitprice']-$d['dismoney'];
									}
								}
								else{
									if(floatval($d['max'])<floatval($sort[$t+($gi-1)*$add]['unitprice'])){//超出折讓上限以上限金額優惠
										$dismoney=floatval($dismoney)+floatval($d['max']);
										$premoney=floatval($premoney)+floatval($d['max']);
									}
									else{
										$dismoney=floatval($dismoney)+$sort[$t+($gi-1)*$add]['unitprice'];
										$premoney=floatval($premoney)+$sort[$t+($gi-1)*$add]['unitprice'];
									}
								}
							}
							$count++;

							if($count>=$times){
								if(strlen($dispremoney)==0){//記錄每個優惠方案分別優惠多少金額
									$dispremoney=$premoney;
								}
								else{
									$dispremoney=$dispremoney.','.$premoney;
								}
								break;
							}
							else{
								continue;
							}
						}

						for($gi=$d['amt']*$times;$gi>0;$gi--){//2021/9/8 扣除已餐與優惠的品項
							$_POST['dis'.$_POST['listtype']][$sort[$t+($gi-1)*$add]['index']]='';
						}

						if($count>=$times){
							continue;
						}
						else{
							if(strlen($dispremoney)==0){//記錄每個優惠方案分別優惠多少金額
								$dispremoney=$premoney;
							}
							else{
								$dispremoney=$dispremoney.','.$premoney;
							}
						}

					}
					else{
					}
				}
			}
			else{
				continue;
			}
		}
	}
	$precision=$initsetting['init']['accuracy'];
	if(!isset($initsetting['init']['accuracytype'])||$initsetting['init']['accuracytype']=='1'){//四捨五入
		//2021/4/15 扣掉一個單位來達到應負金額的五捨六入，
		$dismoney=floatval($dismoney)-floatval(pow(10,-(intval($precision)+1)));
		$dismoney=round($dismoney ,$precision);
	}
	else if($initsetting['init']['accuracytype']=='2'){//無條件進位
		//return ceilfun(temp ,precision);
		//2021/4/15 調整達到應付金額得無條件進位
		$dismoney=floor(floor(($dismoney*pow(10,($precision+1))))/pow(10,($precision+1)));
	}
	else{//無條件捨去
		//return floorfun(temp ,precision);
		//2021/4/15 調整達到應付金額得無條件進位
		$dismoney=ceil(floor(($dismoney*pow(10,($precision+1))))/pow(10,($precision+1)));
	}
	if(floatval($dismoney)==0){
		$dismoney=0;
	}
	else{
	}
	echo $dismoney.';'.$discontent.';'.$dispremoney;
}
?>