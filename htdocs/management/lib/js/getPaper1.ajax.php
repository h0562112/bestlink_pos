<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
if(isset($_POST['lan'])&&$_POST['lan']!=''){
	if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
		$interface=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('../../lan/interface1.ini')){
		$interface=parse_ini_file('../../lan/interface1.ini',true);
	}
	else{
		$interface='-1';
	}
}
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);

if(isset($_POST['startdate'])){
	//echo 'where file'.'../../../11/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/menu.db<br>';
	//echo 'DB is exists? '.file_exists('../../../11/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/menu.db').'<br>';
	if($_SESSION['DB']==''){
		$connm=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'menu.db','','','','sqlite');
		//echo '../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/menu.db';
	}
	else{
		$connm=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'menu.db','','','','sqlite');
		//echo '../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/menu.db';
	}
	if(!$connm){
		//echo $_SESSION['company'].'<br>'.$_SESSION['DB'].'<br>'.'菜單資料遺失。';
		if($interface!='-1'&&isset($interface['name']['menudatadisa'])){
			echo $interface['name']['menudatadisa'];
		}
		else{
			echo '菜單資料遺失。';
		}
	}
	else{
		$sql='SELECT fronttype,inumber FROM itemsdata ORDER BY replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(fronttype))), "\'", ""), "0", "0")||fronttype,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(rearsq))), "\'", ""), "0", "0")||rearsq,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(inumber))), "\'", ""), "0", "0")||inumber';
		$menuarray=sqlquery($connm,$sql,'sqlite');
		if(sizeof($menuarray)==0){
			if($interface!='-1'&&isset($interface['name']['menudataempty'])){
				echo $interface['name']['menudataempty'];
			}
			else{
				echo '菜單資料尚未新增。';
			}
		}
		else{
			if($_SESSION['DB']==''){
				$itemname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-menu.ini',true);
				$rearname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-front.ini',true);
				$tastename=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-taste.ini',true);
				$init=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/initsetting.ini',true);
			}
			else{
				$itemname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-menu.ini',true);
				$rearname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-front.ini',true);
				$tastename=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-taste.ini',true);
				$init=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
			}
			if(file_exists('../../../ourpos/'.$_SESSION['company'].'/buttons-'.$init['init']['firlan'].'.ini')){
				$saletype=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/buttons-'.$init['init']['firlan'].'.ini',true);
			}
			else{
				$saletype=parse_ini_file('../../../ourpos/buttons-'.$init['init']['firlan'].'.ini',true);
			}
			
			$front=array();//產品編號反查目前類別編號
			$menu=array();//暫存menu產品陣列
			$taste=array();//暫存加料與備註陣列
			$list=array();//暫存帳單資料
			$charge=array();
			$totalcharge=0;
			foreach($menuarray as $i){
				if(isset($itemname[intval($i['inumber'])])){
					$front[intval($i['inumber'])]=intval($i['fronttype']);
					$menu[intval($i['fronttype'])]['name1']=$rearname[intval($i['fronttype'])]['name1'];
					$menu[intval($i['fronttype'])]['name2']=$rearname[intval($i['fronttype'])]['name2'];
					$menu[intval($i['fronttype'])][intval($i['inumber'])]['name1']=$itemname[intval($i['inumber'])]['name1'];
					$menu[intval($i['fronttype'])][intval($i['inumber'])]['name2']=$itemname[intval($i['inumber'])]['name2'];
					$menu[intval($i['fronttype'])][intval($i['inumber'])]['state']=$itemname[intval($i['inumber'])]['state'];
				}
				else{
				}
			}
			foreach($tastename as $index => $t){
				$menu['td'.$index]['name1']=$t['name1'];
				$menu['td'.$index]['name2']=$t['name2'];
			}
			$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
			$complete=0;
			for($m=0;$m<=$totalMon;$m++){
				if($_SESSION['DB']==''){
					$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
					//echo '../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db';
				}
				else{
					$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
					//echo '../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db';
				}

				if(!$conn){
					if($interface!='-1'&&isset($interface['name']['menudatanotup'])){
						echo $interface['name']['menudatanotup'];
					}
					else{
						echo '資料庫尚未上傳資料。';
					}
				}
				else{
					$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
					$res=sqlquery($conn,$sql,'sqlite');
					if(isset($res[0]['name'])){
						//echo '1';
						$sql='SELECT BIZDATE,SUM(TAX1) AS tax1,REMARKS FROM CST011 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND NBCHKNUMBER IS NULL GROUP BY BIZDATE,REMARKS';
						$tempcharge=sqlquery($conn,$sql,'sqlite');
						for($d=0;$d<sizeof($tempcharge);$d++){
							$charge[$tempcharge[$d]['BIZDATE']][$tempcharge[$d]['REMARKS']]['tax1']=$tempcharge[$d]['tax1'];
							$totalcharge=floatval($totalcharge)+floatval($tempcharge[$d]['tax1']);
						}
						$sql='SELECT BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMDEPTCODE,SUM(QTY) AS QTY,UNITPRICELINK,UNITPRICE,SUM(AMT) AS AMT,REMARKS FROM CST012 WHERE ((DTLMODE<>"9" AND DTLTYPE<>"9" AND DTLFUNC<>"99") OR (DTLMODE<>"4" AND DTLTYPE<>"1" AND DTLFUNC<>"01") OR (DTLMODE<>"3" AND DTLTYPE<>"1" AND DTLFUNC<>"01")) AND ITEMCODE<>"0000000000000000" AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'") GROUP BY  BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,UNITPRICE,UNITPRICELINK,ITEMCODE,ITEMDEPTCODE,REMARKS ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER ASC';
						$first=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT BIZDATE,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,QTY,UNITPRICE,AMT,REMARKS FROM CST012 WHERE ((DTLMODE<>"9" AND DTLTYPE<>"9" AND DTLFUNC<>"99") OR (DTLMODE<>"4" AND DTLTYPE<>"1" AND DTLFUNC<>"01") OR (DTLMODE<>"3" AND DTLTYPE<>"1" AND DTLFUNC<>"01")) AND ITEMCODE<>"0000000000000000" AND SELECTIVEITEM1 IS NOT NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'")';
						$second=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT SUM(persons) AS QTY,BIZDATE,SUM(SALESTTLAMT) AS AMT,SUM(TAX2) AS cashmoney,SUM(TAX3) AS cash,REMARKS FROM (SELECT CASE WHEN (TAX6+TAX7+TAX8)=0 THEN 1 ELSE (TAX6+TAX7+TAX8) END AS persons,BIZDATE,SALESTTLAMT,TAX2,TAX3,REMARKS FROM CST011 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND NBCHKNUMBER IS NULL) GROUP BY BIZDATE,REMARKS';
						$listarray=sqlquery($conn,$sql,'sqlite');
						if(sizeof($first)==0){
							if($interface!='-1'&&isset($interface['name']['searchdataempty'])){
								echo $interface['name']['searchdataempty'];
							}
							else{
								echo '搜尋時間區間並無資料。';
							}
						}
						else{
							foreach($first as $item){
								if($item['DTLMODE']=='1'&&$item['DTLTYPE']=='1'&&$item['DTLFUNC']=='01'){
									$menu[$front[intval($item['ITEMCODE'])]]['amt']=1;
									if(isset($menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']]['amt'])){
										$menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']]['amt']=floatval($menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']]['amt'])+(floatval($item['QTY'])*floatval($item['UNITPRICE']));
									}
									else{
										$menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']]['amt']=(floatval($item['QTY'])*floatval($item['UNITPRICE']));
									}
									if(isset($menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']][$item['BIZDATE']][$item['REMARKS']]['amt'])){
										$menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']][$item['BIZDATE']][$item['REMARKS']]['amt']=floatval($menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']][$item['BIZDATE']][$item['REMARKS']]['amt'])+(floatval($item['QTY'])*floatval($item['UNITPRICE']));
									}
									else{
										$menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']][$item['BIZDATE']][$item['REMARKS']]['amt']=(floatval($item['QTY'])*floatval($item['UNITPRICE']));
									}
									if(isset($menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']][$item['BIZDATE']][$item['REMARKS']]['qty'])){
										$menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']][$item['BIZDATE']][$item['REMARKS']]['qty']=intval($menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']][$item['BIZDATE']][$item['REMARKS']]['qty'])+intval($item['QTY']);
									}
									else{
										$menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']][$item['BIZDATE']][$item['REMARKS']]['qty']=$item['QTY'];
									}
									if(isset($menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']]['qty'])){
										$menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']]['qty']=intval($menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']]['qty'])+intval($item['QTY']);
									}
									else{
										$menu[$front[intval($item['ITEMCODE'])]][intval($item['ITEMCODE'])][$item['UNITPRICELINK']]['qty']=$item['QTY'];
									}
								}
								else{
									if(isset($menu[$item['ITEMCODE']]['amt'])){
										$menu[$item['ITEMCODE']]['amt']=floatval($menu[$item['ITEMCODE']]['amt'])+floatval($item['AMT']);
									}
									else{
										$menu[$item['ITEMCODE']]['amt']=$item['AMT'];
									}
									if(isset($menu[$item['ITEMCODE']][$item['BIZDATE']][$item['REMARKS']]['amt'])){
										$menu[$item['ITEMCODE']][$item['BIZDATE']][$item['REMARKS']]['amt']=floatval($menu[$item['ITEMCODE']][$item['BIZDATE']][$item['REMARKS']]['amt'])+floatval($item['AMT']);
									}
									else{
										$menu[$item['ITEMCODE']][$item['BIZDATE']][$item['REMARKS']]['amt']=$item['AMT'];
									}
								}
							}
							foreach($second as $taste){
								if(isset($menu['taste'][$taste['BIZDATE']][$item['REMARKS']]['amt'])){
									$menu['taste'][$taste['BIZDATE']][$item['REMARKS']]['amt']=floatval($menu['taste'][$taste['BIZDATE']][$item['REMARKS']]['amt'])+(floatval($taste['AMT'])-(floatval($taste['QTY'])*floatval($taste['UNITPRICE'])));
								}
								else{
									$menu['taste'][$taste['BIZDATE']][$item['REMARKS']]['amt']=floatval($taste['AMT'])-(floatval($taste['QTY'])*floatval($taste['UNITPRICE']));
								}
								for($i=1;$i<10;$i++){
									if($taste['SELECTIVEITEM'.$i]!=''){
										if(substr($taste['SELECTIVEITEM'.$i],0,5)!='99999'){
											if(isset($menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)][$taste['BIZDATE']][$taste['REMARKS']]['qty'])){
												$menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)][$taste['BIZDATE']][$taste['REMARKS']]['qty']=intval($menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)][$taste['BIZDATE']][$taste['REMARKS']]['qty'])+((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']);
											}
											else{
												$menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)][$taste['BIZDATE']][$taste['REMARKS']]['qty']=((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']);
											}
											if(isset($menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)][$taste['BIZDATE']][$taste['REMARKS']]['amt'])){
												$menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)][$taste['BIZDATE']][$taste['REMARKS']]['amt']=intval($menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)][$taste['BIZDATE']][$taste['REMARKS']]['amt'])+((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']*floatval($tastename[(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['money']));
											}
											else{
												$menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)][$taste['BIZDATE']][$taste['REMARKS']]['amt']=((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']*floatval($tastename[(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['money']));
											}
											if(isset($menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['qty'])){
												$menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['qty']=intval($menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['qty'])+((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']);
											}
											else{
												$menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['qty']=((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']);
											}
											if(isset($menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['amt'])){
												$menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['amt']=intval($menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['amt'])+((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']*floatval($tastename[(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['money']));
											}
											else{
												$menu['td'.(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['amt']=((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']*floatval($tastename[(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['money']));
											}
										}
										else{
										}
									}
									else{
										break;
									}
								}
							}
							foreach($listarray as $l){
								$list[$l['BIZDATE']][$l['REMARKS']]['money']=$l['AMT'];
								$list[$l['BIZDATE']][$l['REMARKS']]['qty']=$l['QTY'];
								$list[$l['BIZDATE']][$l['REMARKS']]['cashmoney']=$l['cashmoney'];
								$list[$l['BIZDATE']][$l['REMARKS']]['cash']=$l['cash'];
							}
						}
					}
					else{
						//echo '1';
						$complete++;
					}
				}
				sqlclose($conn,'sqlite');
			}
			if($complete>=($totalMon+1)){
				echo '資料庫未完整上傳。';
			}
			else{
				if($complete>0){
					echo '部分月份資料庫未完整上傳。';
				}
				else{
				}
				echo '<table id="fixTable" class="table"><thead><tr><th></th>';
				if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
					$ENDDATE=strtotime(date('Ymd'));
				}
				else{
					$ENDDATE=strtotime(date('Ymd',strtotime($end)));
				}
				$type=preg_split('/,/',$init['init']['orderlocation']);
				//print_r($type);
				echo '<th colspan="'.(2*sizeof($type)).'" id="bold" style="text-align:center;">';if($interface!='-1'&&isset($interface['name']['subtotal']))echo $interface['name']['subtotal'];else echo '小計';echo '</th>';
				/*for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
					echo "<th colspan='".(2*sizeof($type))."' style='padding:5px;text-align:center;'>".substr(date('Ymd',$d),2,6);
					switch (date("N",$d)) {
						case 1:
							if($interface!='-1'&&isset($interface['name']['mon'])){
								echo "(".$interface['name']['mon'].")";
							}
							else{
								echo "(一)";
							}
							break;
						case 2:
							if($interface!='-1'&&isset($interface['name']['tue'])){
								echo "(".$interface['name']['tue'].")";
							}
							else{
								echo "(二)";
							}
							break;
						case 3:
							if($interface!='-1'&&isset($interface['name']['wed'])){
								echo "(".$interface['name']['wed'].")";
							}
							else{
								echo "(三)";
							}
							break;
						case 4:
							if($interface!='-1'&&isset($interface['name']['thu'])){
								echo "(".$interface['name']['thu'].")";
							}
							else{
								echo "(四)";
							}
							break;
						case 5:
							if($interface!='-1'&&isset($interface['name']['fri'])){
								echo "(".$interface['name']['fri'].")";
							}
							else{
								echo "(五)";
							}
							break;
						case 6:
							if($interface!='-1'&&isset($interface['name']['sat'])){
								echo "<span style='font-weight:bold;color:#C13333;'>(".$interface['name']['sat'].")</span>";
							}
							else{
								echo "<span style='font-weight:bold;color:#C13333;'>(六)</span>";
							}
							break;
						case 7:
							if($interface!='-1'&&isset($interface['name']['sun'])){
								echo "<span style='font-weight:bold;color:#C13333;'>(".$interface['name']['sun'].")</span>";
							}
							else{
								echo "<span style='font-weight:bold;color:#C13333;'>(日)</span>";
							}
							break;
						default:
							break;
					}
					echo "</th>";
					//echo '<th style="text-align:right;">'.substr(date('Ymd',$d),2,6).'</th>';
				}*/
				echo '<th colspan="'.(2*sizeof($type)).'" id="bold" style="text-align:center;">';if($interface!='-1'&&isset($interface['name']['total']))echo $interface['name']['total'];else echo '合計';echo '</th>';
				echo '</tr>';
				echo '<tr><th></th>';
				foreach($type as $t){
					echo "<th colspan='2' id='bold' style='padding:0 5px;text-align:center;' nowrap='nowrap'>".$saletype['name']['listtype'.$t]."</th>";
				}
				echo "<th colspan='2' id='bold' style='padding:0 5px;text-align:center;' nowrap='nowrap'></th>";
				/*for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
					foreach($type as $t){
						echo "<th colspan='2' style='padding:5px;text-align:center;' nowrap='nowrap'>".$saletype['name']['listtype'.$t]."</th>";
					}
				}*/
				echo '</tr>';
				echo '<tr><th></th>';
				foreach($type as $t){
					echo "<th id='bold' style='padding:0 5px;text-align:center;' nowrap='nowrap'>";if($interface!='-1'&&isset($interface['name']['qty']))echo $interface['name']['qty'];else echo '數量';echo "</th>";
					echo "<th id='bold' style='padding:0 5px;text-align:center;' nowrap='nowrap'>";if($interface!='-1'&&isset($interface['name']['amt']))echo $interface['name']['amt'];else echo '金額';echo "</th>";
				}
				echo "<th id='bold' style='padding:0 5px;text-align:center;' nowrap='nowrap'>";if($interface!='-1'&&isset($interface['name']['qty']))echo $interface['name']['qty'];else echo '數量';echo "</th>";
				echo "<th id='bold' style='padding:0 5px;text-align:center;' nowrap='nowrap'>";if($interface!='-1'&&isset($interface['name']['amt']))echo $interface['name']['amt'];else echo '金額';echo "</th>";
				/*for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
					foreach($type as $t){
						echo "<th style='padding:5px;text-align:center;' nowrap='nowrap'>";if($interface!='-1'&&isset($interface['name']['qty']))echo $interface['name']['qty'];else echo '數量';echo "</th>";
						echo "<th style='padding:5px;text-align:center;' nowrap='nowrap'>";if($interface!='-1'&&isset($interface['name']['amt']))echo $interface['name']['amt'];else echo '金額';echo "</th>";
					}
				}*/
				echo '</tr>';
				echo '</thead><tbody>';
				$detaste=array();
				$discount=array();
				$dissum=0;
				$tastetitle='';//加料與備註合計欄位
				$tasteitems1='';//加料與備註品項
				$tasteitems2='';//加料與備註品項
				$cashmoney=array();
				$cash=array();
				$tasteitemsum=0;
				//$tasteindex=0;
				$sum1=0;
				$sum2=0;
				$sum3=0;
				$sum4=0;
				if(isset($menu)&&sizeof($menu)){
					foreach($menu as $k => $i){
						if((string)$k!='list'&&(string)$k!='item'&&(string)$k!='autodis'&&substr($k,0,1)!='t'&&isset($i['amt'])){
							echo "<tr><td class='title' style='width:200px;' ><input type='hidden' value='".$k."'>".$i['name1']."</td>";
							/*for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
								$presubtotal=0;
								for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
									echo '<td class="title money" style="text-align:right;"></td>';
									echo '<td class="title money" style="text-align:right;"></td>';
								}
							}*/
							for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
								echo '<td class="title money" style="text-align:right;"></td>';
								echo '<td class="title money" style="text-align:right;"></td>';
							}
							echo '<td class="title money" style="text-align:right;"></td>';
							echo '<td class="title money" style="text-align:right;"></td>';
							echo "</tr>";
							$thtml1='';
							$thtml2='';
							$tqty=0;
							$tamt=0;
							foreach($i as $di){
								if(is_array($di)){
									$computindex=0;
									foreach($di as $ddindex=>$ddi){
										$subqty=[0,0,0,0,0];
										$subamt=[0,0,0,0,0];
										$temphtml='';
										if(isset($ddi['qty'])&&intval($ddi['qty'])>0){
											echo '<tr><td ><div style="width:200px;margin-left:20px;">'.$di['name1'].'-'.$ddindex.'</div></td>';
											for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
												//$presubtotal=0;
												//print_r($di[date('Ymd',$d)]);
												for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
													if(isset($ddi[date('Ymd',$d)][intval($type[$saletypelen])]['qty'])){
														//$temphtml=$temphtml.'<td style="text-align:right;">'.number_format($ddi[date('Ymd',$d)][intval($type[$saletypelen])]['qty']).'</td>';
														$subqty[intval($type[$saletypelen])]=floatval($subqty[intval($type[$saletypelen])])+floatval($ddi[date('Ymd',$d)][intval($type[$saletypelen])]['qty']);
													}
													else{
														//$temphtml=$temphtml.'<td style="text-align:right;">0</td>';
													}
													if(isset($ddi[date('Ymd',$d)][intval($type[$saletypelen])]['amt'])){
														//$temphtml=$temphtml.'<td style="text-align:right;">'.number_format($ddi[date('Ymd',$d)][intval($type[$saletypelen])]['amt']).'</td>';
														$subamt[intval($type[$saletypelen])]=floatval($subamt[intval($type[$saletypelen])])+floatval($ddi[date('Ymd',$d)][intval($type[$saletypelen])]['amt']);
													}
													else{
														//$temphtml=$temphtml.'<td style="text-align:right;">0</td>';
													}
												}
											}
											$totalqty=0;
											$totalamt=0;
											for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
												echo '<td id="" style="text-align:right;" >'.number_format($subqty[intval($type[$saletypelen])]).'</td>';
												echo '<td id="" style="text-align:right;" >'.number_format($subamt[intval($type[$saletypelen])]).'</td>';
												$totalqty=floatval($totalqty)+floatval($subqty[intval($type[$saletypelen])]);
												$totalamt=floatval($totalamt)+floatval($subamt[intval($type[$saletypelen])]);
											}
											//echo $temphtml;
											echo '<td id="bold" style="text-align:right;padding:1px 0 1px 5px;" >'.number_format($totalqty).'</td>';
											echo '<td id="bold" style="text-align:right;padding:1px 0 1px 5px;" >'.number_format($totalamt).'</td>';
											echo '</tr>';
											$tqty=intval($tqty)+intval($ddi['qty']);
											$tamt=intval($tamt)+intval($ddi['amt']);
											$computindex++;
										}
										else{
										}
									}
								}
								else{
								}
							}
						}
						else if((string)$k!='list'&&(string)$k!='item'&&(string)$k!='autodis'&&substr($k,0,1)!='t'){
						}
						else if(substr($k,0,2)=='td'){
							$subtasqty=[0,0,0,0,0];
							$subtasamt=[0,0,0,0,0];
							$temptasteitem='';
							if(isset($i['qty'])&&intval($i['qty'])>0){
								$tasteitems1=$tasteitems1.'<tr><td >&nbsp;&nbsp;'.$i['name1'].'</td>';
								for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
									for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
										if(isset($i[date('Ymd',$d)][intval($type[$saletypelen])]['qty'])){
											$subtasqty[intval($type[$saletypelen])]=floatval($subtasqty[intval($type[$saletypelen])])+floatval($i[date('Ymd',$d)][intval($type[$saletypelen])]['qty']);
											$subtasamt[intval($type[$saletypelen])]=floatval($subtasamt[intval($type[$saletypelen])])+floatval($i[date('Ymd',$d)][intval($type[$saletypelen])]['amt']);
											//$temptasteitem=$temptasteitem.'<td style="text-align:right;">'.number_format($i[date('Ymd',$d)][intval($type[$saletypelen])]['qty']).'</td>';
											//$temptasteitem=$temptasteitem.'<td style="text-align:right;">'.number_format($i[date('Ymd',$d)][intval($type[$saletypelen])]['amt']).'</td>';
										}
										else{
											//$temptasteitem=$temptasteitem.'<td style="text-align:right;">0</td>';
											//$temptasteitem=$temptasteitem.'<td style="text-align:right;">0</td>';
										}
									}
								}
								$totalqty=0;
								$totalamt=0;
								for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
									$tasteitems1=$tasteitems1.'<td id="" style="text-align:right;">'.number_format($subtasqty[intval($type[$saletypelen])]).'</td>';
									$tasteitems1=$tasteitems1.'<td id="" style="text-align:right;">'.number_format($subtasamt[intval($type[$saletypelen])]).'</td>';
									$totalqty=floatval($totalqty)+floatval($subtasqty[intval($type[$saletypelen])]);
									$totalamt=floatval($totalamt)+floatval($subtasamt[intval($type[$saletypelen])]);
								}
								$tasteitems1=$tasteitems1.'<td id="bold" style="text-align:right;padding:1px 0 1px 5px;">'.number_format($totalqty).'</td>';
								$tasteitems1=$tasteitems1.'<td id="bold" style="text-align:right;padding:1px 0 1px 5px;">'.number_format($totalamt).'</td>';
								$tasteitems1=$tasteitems1.$temptasteitem.'</tr>';
							}
							else{
							}
						}
						else if((string)$k=='taste'){
							$sum=0;
							$tastetitle='<tr><td class="title" >';if($interface!='-1'&&isset($interface['name']['tastelabel']))$tastetitle=$tastetitle.$interface['name']['tastelabel'];else $tastetitle=$tastetitle.'加料與備註';$tastetitle=$tastetitle.'</td>';
							for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
								for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
									//$tastetitle=$tastetitle.'<td class="title" style="text-align:right;"></td>';
									//$tastetitle=$tastetitle.'<td class="title" style="text-align:right;"></td>';
									if(isset($i[date('Ymd',$d)][intval($type[$saletypelen])]['amt'])){
										$sum=floatval($sum)+floatval($i[date('Ymd',$d)][intval($type[$saletypelen])]['amt']);
									}
									else{
									}
								}
							}
							for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
								$tastetitle=$tastetitle.'<td class="title" style="text-align:right;"></td>';
								$tastetitle=$tastetitle.'<td class="title" style="text-align:right;"></td>';
							}
							$tastetitle=$tastetitle.'<td class="title" style="text-align:right;"></td>';
							$tastetitle=$tastetitle.'<td class="title" style="text-align:right;"></td>';
							$tastetitle=$tastetitle.'</tr>';
						}
						else if((string)$k=='list'||(string)$k=='item'||(string)$k=='autodis'){
							for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
								for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
									if(isset($i[date('Ymd',$d)][intval($type[$saletypelen])]['amt'])){
										if(isset($discount[date('Ymd',$d)][intval($type[$saletypelen])])){
											$discount[date('Ymd',$d)][intval($type[$saletypelen])]=floatval($discount[date('Ymd',$d)][intval($type[$saletypelen])])+floatval($i[date('Ymd',$d)][intval($type[$saletypelen])]['amt']);
											$dissum=floatval($dissum)+floatval($i[date('Ymd',$d)][intval($type[$saletypelen])]['amt']);
										}
										else{
											$discount[date('Ymd',$d)][intval($type[$saletypelen])]=$i[date('Ymd',$d)][intval($type[$saletypelen])]['amt'];
											$dissum=floatval($dissum)+floatval($i[date('Ymd',$d)][intval($type[$saletypelen])]['amt']);
										}
									}
									else{
									}
								}
							}
						}
						else{
						}
					}
				}
				else{
				}
				
				echo $tastetitle.$tasteitems1;
				$discounttype=[0,0,0,0,0];
				if(sizeof($discount)>0){
					$tempdiscount='';
					echo '<tr id="dis"><td class="title" >(-)';if($interface!='-1'&&isset($interface['name']['itemdis']))echo $interface['name']['itemdis'];else echo '折扣';echo '</td>';
					$index=1;
					for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
						for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
							if(isset($discount[date('Ymd',$d)][intval($type[$saletypelen])])){
								//$tempdiscount=$tempdiscount.'<td class="title money" style="text-align:right;';if($index%2==1)$tempdiscount=$tempdiscount.'background-color:#f0f0f0;';else $tempdiscount=$tempdiscount.'background-color:#ffffff;';$tempdiscount=$tempdiscount.'"></td>';
								$index++;
								$discounttype[intval($type[$saletypelen])]=floatval($discounttype[intval($type[$saletypelen])])+floatval($discount[date('Ymd',$d)][intval($type[$saletypelen])]);
								//$tempdiscount=$tempdiscount.'<td class="title money" style="text-align:right;';if($index%2==1)$tempdiscount=$tempdiscount.'background-color:#f0f0f0;';else $tempdiscount=$tempdiscount.'background-color:#ffffff;';$tempdiscount=$tempdiscount.'"><div>'.number_format($discount[date('Ymd',$d)][intval($type[$saletypelen])])."</div></td>";
								$dissum=floatval($dissum)+floatval($discount[date('Ymd',$d)][intval($type[$saletypelen])]['amt']);
							}
							else{
								//$tempdiscount=$tempdiscount.'<td class="title money" style="text-align:right;';if($index%2==1)$tempdiscount=$tempdiscount.'background-color:#f0f0f0;';else $tempdiscount=$tempdiscount.'background-color:#ffffff;';$tempdiscount=$tempdiscount.'"></td>';
								$index++;
								//$tempdiscount=$tempdiscount.'<td class="title money" style="text-align:right;';if($index%2==1)$tempdiscount=$tempdiscount.'background-color:#f0f0f0;';else $tempdiscount=$tempdiscount.'background-color:#ffffff;';$tempdiscount=$tempdiscount.'">0</td>';
							}
							$index++;
						}
					}
					for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
						echo '<td class="title" style="font-weight:normal;text-align:right;';if($index%2==1)echo 'background-color:#f0f0f0;';else echo 'background-color:#ffffff;';echo '"></td>';
						$index++;
						echo '<td class="title money" style="font-weight:normal;text-align:right;';if($index%2==1)echo 'background-color:#f0f0f0;';else echo 'background-color:#ffffff;';echo '"><div>'.number_format($discounttype[intval($type[$saletypelen])]).'</div></td>';
						$index++;
					}
					echo $tempdiscount.'<td class="title" id="bold" style="text-align:right;';if($index%2==1)echo 'background-color:#f0f0f0;';else echo 'background-color:#ffffff;';echo '"><div></div></td>';
					$index++;
					echo $tempdiscount.'<td class="title money" id="bold" style="text-align:right;';if($index%2==1)echo 'background-color:#f0f0f0;';else echo 'background-color:#ffffff;';echo '"><div>'.number_format($dissum).'</div></td></tr>';
					
				}
				else{
					$tempdiscount='';
					echo '<tr id="dis"><td class="title" >';if($interface!='-1'&&isset($interface['name']['itemdis']))echo $interface['name']['itemdis'];else echo '折扣';echo '</td>';
					$index=1;
					for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
						for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
							//$tempdiscount=$tempdiscount.'<td class="title" style="text-align:right;';if($index%2==1)$tempdiscount=$tempdiscount.'background-color:#f0f0f0;';else $tempdiscount=$tempdiscount.'background-color:#ffffff;';$tempdiscount=$tempdiscount.'"></td>';
							$index++;
							//$tempdiscount=$tempdiscount.'<td class="title" style="text-align:right;';if($index%2==1)$tempdiscount=$tempdiscount.'background-color:#f0f0f0;';else $tempdiscount=$tempdiscount.'background-color:#ffffff;';$tempdiscount=$tempdiscount.'">0</td>';
							$index++;
						}
					}
					for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
						$tempdiscount=$tempdiscount.'<td class="title" style="text-align:right;';if($index%2==1)echo 'background-color:#f0f0f0;';else echo 'background-color:#ffffff;';echo '"></td>';
						$index++;
						$tempdiscount=$tempdiscount.'<td class="title money" style="text-align:right;';if($index%2==1)echo 'background-color:#f0f0f0;';else echo 'background-color:#ffffff;';echo '"><div>'.number_format($discounttype[intval($type[$saletypelen])]).'</div></td>';
						$index++;
					}
					echo $tempdiscount.'<td class="title money" id="bold" style="text-align:right;';if($index%2==1)echo 'background-color:#f0f0f0;';else echo 'background-color:#ffffff;';echo '"><div></div></td>';
					$index++;
					echo $tempdiscount.'<td class="title money" id="bold" style="text-align:right;';if($index%2==1)echo 'background-color:#f0f0f0;';else echo 'background-color:#ffffff;';echo '"><div>'.number_format($dissum).'</div></td></tr>';
				}
				$predaymoney='';
				$predaynumber='';
				$predaypmoney='';
				$predaycashmoney='';
				$predaycharge='';
				$predaycash='';
				$temppredaymoney='';
				$temppredaynumber='';
				$temppredaypmoney='';
				$temppredaycashmoney='';
				$temppredaycharge='';
				$temppredaycash='';
				$chargetype=[0,0,0,0,0];
				$cashmoneytype=[0,0,0,0,0];
				$cashtype=[0,0,0,0,0];
				$daytype=[0,0,0,0,0];
				$numbertype=[0,0,0,0,0];
				if(sizeof($list)>0){
					$predaycharge='<tr id="preday"><td >';if($interface!='-1'&&isset($interface['name']['totalcharge']))$predaycharge=$predaycharge.$interface['name']['totalcharge'];else $predaycharge=$predaycharge.'總服務費';$predaycharge=$predaycharge.'</td>';
					$predaycashmoney='</tbody><tfoot><tr id="top"><th >';if($interface!='-1'&&isset($interface['name']['totalmoney']))$predaycashmoney=$predaycashmoney.$interface['name']['totalmoney'];else $predaycashmoney=$predaycashmoney.'現金收入';$predaycashmoney=$predaycashmoney.'</th>';
					$predaycash='<tr id="preday"><th >';if($interface!='-1'&&isset($interface['name']['totalcash']))$predaycash=$predaycash.$interface['name']['totalcash'];else $predaycash=$predaycash.'信用卡收入';$predaycash=$predaycash.'</th>';
					$predaymoney='<tr id="preday"><th >';if($interface!='-1'&&isset($interface['name']['perdaysale']))$predaymoney=$predaymoney.$interface['name']['perdaysale'];else $predaymoney=$predaymoney.'營收總計';$predaymoney=$predaymoney.'</th>';
					$predaynumber='<tr id="preday"><th >';if($interface!='-1'&&isset($interface['name']['totalpersonlabel']))$predaynumber=$predaynumber.$interface['name']['totalpersonlabel'];else $predaynumber=$predaynumber.'來客數';$predaynumber=$predaynumber.'</th>';
					$predaypmoney='<tr id="preday"><th >';if($interface!='-1'&&isset($interface['name']['totalavg']))$predaypmoney=$predaypmoney.$interface['name']['totalavg'];else $predaypmoney=$predaypmoney.'平均客單價';$predaypmoney=$predaypmoney.'</th>';
					$index=1;
					for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
						for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
							//$temppredaycharge=$temppredaycharge.'<td class="money" style="';if($index%2==1)$temppredaycharge=$temppredaycharge.'background-color:#f0f0f0;';else $temppredaycharge=$temppredaycharge.'background-color:#ffffff;';$temppredaycharge=$temppredaycharge.'"></td>';

							//$temppredaycashmoney=$temppredaycashmoney.'<th class="money" style="';if($index%2==1)$temppredaycashmoney=$temppredaycashmoney.'background-color:#f0f0f0;';else $temppredaycashmoney=$temppredaycashmoney.'background-color:#ffffff;';$temppredaycashmoney=$temppredaycashmoney.'"></th>';

							//$temppredaycash=$temppredaycash.'<th class="money" style="';if($index%2==1)$temppredaycash=$temppredaycash.'background-color:#f0f0f0;';else $temppredaycash=$temppredaycash.'background-color:#ffffff;';$temppredaycash=$temppredaycash.'"></th>';

							//$temppredaymoney=$temppredaymoney.'<th class="money" style="';if($index%2==1)$temppredaymoney=$temppredaymoney.'background-color:#f0f0f0;';else $temppredaymoney=$temppredaymoney.'background-color:#ffffff;';$temppredaymoney=$temppredaymoney.'"></th>';

							//$temppredaynumber=$temppredaynumber.'<th class="money" style="';if($index%2==1)$temppredaynumber=$temppredaynumber.'background-color:#f0f0f0;';else $temppredaynumber=$temppredaynumber.'background-color:#ffffff;';$temppredaynumber=$temppredaynumber.'"></th>';

							//$temppredaypmoney=$temppredaypmoney.'<th class="money" style="';if($index%2==1)$temppredaypmoney=$temppredaypmoney.'background-color:#f0f0f0;';else $temppredaypmoney=$temppredaypmoney.'background-color:#ffffff;';$temppredaypmoney=$temppredaypmoney.'"></th>';
							$index++;
							if(isset($list[date('Ymd',$d)][$type[$saletypelen]]['money'])){
								//$temppredaycharge=$temppredaycharge.'<td class="money" style="text-align:right;';if($index%2==1)$temppredaycharge=$temppredaycharge.'background-color:#f0f0f0;';else $temppredaycharge=$temppredaycharge.'background-color:#ffffff;';$temppredaycharge=$temppredaycharge.'"><div>';if(isset($charge[date('Ymd',$d)][$type[$saletypelen]]['tax1'])){$temppredaycharge=$temppredaycharge.number_format($charge[date('Ymd',$d)][$type[$saletypelen]]['tax1']);$chargetype[$type[$saletypelen]]=floatval($chargetype[$type[$saletypelen]])+floatval($charge[date('Ymd',$d)][$type[$saletypelen]]['tax1']);}else $temppredaycharge=$temppredaycharge.'0';$temppredaycharge=$temppredaycharge.'</div></td>';
								
								$cashmoneytype[$type[$saletypelen]]=floatval($cashmoneytype[$type[$saletypelen]])+floatval($list[date('Ymd',$d)][$type[$saletypelen]]['cashmoney']);
								//$temppredaycashmoney=$temppredaycashmoney.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaycashmoney=$temppredaycashmoney.'background-color:#f0f0f0;';else $temppredaycashmoney=$temppredaycashmoney.'background-color:#ffffff;';$temppredaycashmoney=$temppredaycashmoney.'"><div>'.number_format($list[date('Ymd',$d)][$type[$saletypelen]]['cashmoney']).'</div></th>';

								$cashtype[$type[$saletypelen]]=floatval($cashtype[$type[$saletypelen]])+floatval($list[date('Ymd',$d)][$type[$saletypelen]]['cash']);
								//$temppredaycash=$temppredaycash.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaycash=$temppredaycash.'background-color:#f0f0f0;';else $temppredaycash=$temppredaycash.'background-color:#ffffff;';$temppredaycash=$temppredaycash.'"><div>'.number_format($list[date('Ymd',$d)][$type[$saletypelen]]['cash']).'</div></th>';
								
								if(isset($charge[date('Ymd',$d)][$type[$saletypelen]]['tax1']))$submoney=(floatval($list[date('Ymd',$d)][$type[$saletypelen]]['money'])+floatval($charge[date('Ymd',$d)][$type[$saletypelen]]['tax1']));
								else $submoney=$list[date('Ymd',$d)][$type[$saletypelen]]['money'];
								$daytype[$type[$saletypelen]]=floatval($daytype[$type[$saletypelen]])+floatval($submoney);
								//$temppredaymoney=$temppredaymoney.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaymoney=$temppredaymoney.'background-color:#f0f0f0;';else $temppredaymoney=$temppredaymoney.'background-color:#ffffff;';$temppredaymoney=$temppredaymoney.'"><div>'.number_format($submoney).'</div></th>';

								$numbertype[$type[$saletypelen]]=floatval($numbertype[$type[$saletypelen]])+floatval($list[date('Ymd',$d)][$type[$saletypelen]]['qty']);
								//$temppredaynumber=$temppredaynumber.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaynumber=$temppredaynumber.'background-color:#f0f0f0;';else $temppredaynumber=$temppredaynumber.'background-color:#ffffff;';$temppredaynumber=$temppredaynumber.'">'.number_format($list[date('Ymd',$d)][$type[$saletypelen]]['qty']).'</th>';
								
								//$temppredaypmoney=$temppredaypmoney.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaypmoney=$temppredaypmoney.'background-color:#f0f0f0;';else $temppredaypmoney=$temppredaypmoney.'background-color:#ffffff;';$temppredaypmoney=$temppredaypmoney.'"><div>'.number_format(round($submoney/$list[date('Ymd',$d)][$type[$saletypelen]]['qty'],1)).'</div></th>';

								$sum1=floatval($sum1)+floatval($submoney);
								$sum2=floatval($sum2)+floatval($list[date('Ymd',$d)][$type[$saletypelen]]['qty']);
								$sum3=floatval($sum3)+floatval($list[date('Ymd',$d)][$type[$saletypelen]]['cashmoney']);
								$sum4=floatval($sum4)+floatval($list[date('Ymd',$d)][$type[$saletypelen]]['cash']);
							}
							else{
								//$temppredaycharge=$temppredaycharge.'<td class="money" style="text-align:right;';if($index%2==1)$temppredaycharge=$temppredaycharge.'background-color:#f0f0f0;';else $temppredaycharge=$temppredaycharge.'background-color:#ffffff;';$temppredaycharge=$temppredaycharge.'"><div>0</div></td>';
								
								//$temppredaycashmoney=$temppredaycashmoney.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaycashmoney=$temppredaycashmoney.'background-color:#f0f0f0;';else $temppredaycashmoney=$temppredaycashmoney.'background-color:#ffffff;';$temppredaycashmoney=$temppredaycashmoney.'"><div>0</div></th>';
								//$temppredaycash=$temppredaycash.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaycash=$temppredaycash.'background-color:#f0f0f0;';else $temppredaycash=$temppredaycash.'background-color:#ffffff;';$temppredaycash=$temppredaycash.'"><div>0</div></th>';

								//$temppredaymoney=$temppredaymoney.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaymoney=$temppredaymoney.'background-color:#f0f0f0;';else $temppredaymoney=$temppredaymoney.'background-color:#ffffff;';$temppredaymoney=$temppredaymoney.'">0</th>';
								//$temppredaynumber=$temppredaynumber.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaynumber=$temppredaynumber.'background-color:#f0f0f0;';else $temppredaynumber=$temppredaynumber.'background-color:#ffffff;';$temppredaynumber=$temppredaynumber.'">0</th>';
								//$temppredaypmoney=$temppredaypmoney.'<th class="money" style="text-align:right;';if($index%2==1)$temppredaypmoney=$temppredaypmoney.'background-color:#f0f0f0;';else $temppredaypmoney=$temppredaypmoney.'background-color:#ffffff;';$temppredaypmoney=$temppredaypmoney.'">0</th>';
							}
							$index++;
						}
					}
					for($saletypelen=0;$saletypelen<sizeof($type);$saletypelen++){
						$predaycharge=$predaycharge.'<td class="money" id="" style="';if($index%2==1)$predaycharge=$predaycharge.'background-color:#f0f0f0;';else $predaycharge=$predaycharge.'background-color:#ffffff;';$predaycharge=$predaycharge.'"></td>';

						$predaycashmoney=$predaycashmoney.'<th class="money" id="" style="';if($index%2==1)$predaycashmoney=$predaycashmoney.'background-color:#f0f0f0;';else $predaycashmoney=$predaycashmoney.'background-color:#ffffff;';$predaycashmoney=$predaycashmoney.'"></th>';

						$predaycash=$predaycash.'<th class="money" id="" style="';if($index%2==1)$predaycash=$predaycash.'background-color:#f0f0f0;';else $predaycash=$predaycash.'background-color:#ffffff;';$predaycash=$predaycash.'"></th>';

						$predaymoney=$predaymoney.'<th class="money" id="" style="';if($index%2==1)$predaymoney=$predaymoney.'background-color:#f0f0f0;';else $predaymoney=$predaymoney.'background-color:#ffffff;';$predaymoney=$predaymoney.'"></th>';

						$predaynumber=$predaynumber.'<th class="money" id="" style="';if($index%2==1)$predaynumber=$predaynumber.'background-color:#f0f0f0;';else $predaynumber=$predaynumber.'background-color:#ffffff;';$predaynumber=$predaynumber.'"></th>';

						$predaypmoney=$predaypmoney.'<th class="money" id="" style="';if($index%2==1)$predaypmoney=$predaypmoney.'background-color:#f0f0f0;';else $predaypmoney=$predaypmoney.'background-color:#ffffff;';$predaypmoney=$predaypmoney.'"></th>';
						$index++;

						$predaycharge=$predaycharge.'<td class="money" id="" style="text-align:right;';if($index%2==1)$predaycharge=$predaycharge.'background-color:#f0f0f0;';else $predaycharge=$predaycharge.'background-color:#ffffff;';$predaycharge=$predaycharge.'"><div>'.number_format($chargetype[$type[$saletypelen]]).'</div></td>';
						
						$predaycashmoney=$predaycashmoney.'<th class="money" id="" style="text-align:right;';if($index%2==1)$predaycashmoney=$predaycashmoney.'background-color:#f0f0f0;';else $predaycashmoney=$predaycashmoney.'background-color:#ffffff;';$predaycashmoney=$predaycashmoney.'"><div>'.number_format($cashmoneytype[$type[$saletypelen]]).'</div></th>';

						$predaycash=$predaycash.'<th class="money" id="" style="text-align:right;';if($index%2==1)$predaycash=$predaycash.'background-color:#f0f0f0;';else $predaycash=$predaycash.'background-color:#ffffff;';$predaycash=$predaycash.'"><div>'.number_format($cashtype[$type[$saletypelen]]).'</div></th>';
						
						$predaymoney=$predaymoney.'<th class="money" id="" style="text-align:right;';if($index%2==1)$predaymoney=$predaymoney.'background-color:#f0f0f0;';else $predaymoney=$predaymoney.'background-color:#ffffff;';$predaymoney=$predaymoney.'"><div>'.number_format($daytype[$type[$saletypelen]]).'</div></th>';

						$predaynumber=$predaynumber.'<th class="money" id="" style="text-align:right;';if($index%2==1)$predaynumber=$predaynumber.'background-color:#f0f0f0;';else $predaynumber=$predaynumber.'background-color:#ffffff;';$predaynumber=$predaynumber.'">'.number_format($numbertype[$type[$saletypelen]]).'</th>';
						
						if(floatval($numbertype[$type[$saletypelen]])>0){
							$predaypmoney=$predaypmoney.'<th class="money" id="" style="text-align:right;';if($index%2==1)$predaypmoney=$predaypmoney.'background-color:#f0f0f0;';else $predaypmoney=$predaypmoney.'background-color:#ffffff;';$predaypmoney=$predaypmoney.'"><div>'.number_format(round($daytype[$type[$saletypelen]]/$numbertype[$type[$saletypelen]],1)).'</div></th>';
						}
						else{
							$predaypmoney=$predaypmoney.'<th class="money" id="" style="text-align:right;';if($index%2==1)$predaypmoney=$predaypmoney.'background-color:#f0f0f0;';else $predaypmoney=$predaypmoney.'background-color:#ffffff;';$predaypmoney=$predaypmoney.'"><div>0</div></th>';
						}

						$index++;
					}
					$predaycharge=$predaycharge.$temppredaycharge.'<td class="" id="bold" style="text-align:right;';if($index%2==1)$predaycharge=$predaycharge.'background-color:#f0f0f0;';else $predaycharge=$predaycharge.'background-color:#ffffff;';$predaycharge=$predaycharge.'"><div></div></td>';

					$predaycashmoney=$predaycashmoney.$temppredaycashmoney.'<th class="" id="bold" style="text-align:right;';if($index%2==1)$predaycashmoney=$predaycashmoney.'background-color:#f0f0f0;';else $predaycashmoney=$predaycashmoney.'background-color:#ffffff;';$predaycashmoney=$predaycashmoney.'"><div></div></th>';
					$predaycash=$predaycash.$temppredaycash.'<th class="" id="bold" style="text-align:right;';if($index%2==1)$predaycash=$predaycash.'background-color:#f0f0f0;';else $predaycash=$predaycash.'background-color:#ffffff;';$predaycash=$predaycash.'"><div></div></th>';

					$predaymoney=$predaymoney.$temppredaymoney.'<th class="" id="bold" style="text-align:right;';if($index%2==1)$predaymoney=$predaymoney.'background-color:#f0f0f0;';else $predaymoney=$predaymoney.'background-color:#ffffff;';$predaymoney=$predaymoney.'"><div></div></th>';
					$predaynumber=$predaynumber.$temppredaynumber.'<th class="" id="bold" style="text-align:right;';if($index%2==1)$predaynumber=$predaynumber.'background-color:#f0f0f0;';else $predaynumber=$predaynumber.'background-color:#ffffff;';$predaynumber=$predaynumber.'"></th>';
					if(intval($sum2)==0){
						$predaypmoney=$predaypmoney.$temppredaypmoney.'<th class="" id="bold" style="text-align:right;';if($index%2==1)$predaypmoney=$predaypmoney.'background-color:#f0f0f0;';else $predaypmoney=$predaypmoney.'background-color:#ffffff;';$predaypmoney=$predaypmoney.'"><div></div></th>';
					}
					else{
						$predaypmoney=$predaypmoney.$temppredaypmoney.'<th class="" id="bold" style="text-align:right;';if($index%2==1)$predaypmoney=$predaypmoney.'background-color:#f0f0f0;';else $predaypmoney=$predaypmoney.'background-color:#ffffff;';$predaypmoney=$predaypmoney.'"><div></div></th>';
					}
					$index++;
					$predaycharge=$predaycharge.$temppredaycharge.'<td class="money" id="bold" style="text-align:right;';if($index%2==1)$predaycharge=$predaycharge.'background-color:#f0f0f0;';else $predaycharge=$predaycharge.'background-color:#ffffff;';$predaycharge=$predaycharge.'"><div>'.number_format($totalcharge).'</div></td></tr>';

					$predaycashmoney=$predaycashmoney.$temppredaycashmoney.'<th class="money" id="bold" style="text-align:right;';if($index%2==1)$predaycashmoney=$predaycashmoney.'background-color:#f0f0f0;';else $predaycashmoney=$predaycashmoney.'background-color:#ffffff;';$predaycashmoney=$predaycashmoney.'"><div>'.number_format($sum3).'</div></th></tr>';
					$predaycash=$predaycash.$temppredaycash.'<th class="money" id="bold" style="text-align:right;';if($index%2==1)$predaycash=$predaycash.'background-color:#f0f0f0;';else $predaycash=$predaycash.'background-color:#ffffff;';$predaycash=$predaycash.'"><div>'.number_format($sum4).'</div></th></tr>';

					$predaymoney=$predaymoney.$temppredaymoney.'<th class="money" id="bold" style="text-align:right;';if($index%2==1)$predaymoney=$predaymoney.'background-color:#f0f0f0;';else $predaymoney=$predaymoney.'background-color:#ffffff;';$predaymoney=$predaymoney.'"><div>'.number_format($sum1).'</div></th></tr>';
					$predaynumber=$predaynumber.$temppredaynumber.'<th class="money" id="bold" style="text-align:right;';if($index%2==1)$predaynumber=$predaynumber.'background-color:#f0f0f0;';else $predaynumber=$predaynumber.'background-color:#ffffff;';$predaynumber=$predaynumber.'">'.number_format($sum2).'</th></tr>';
					if(intval($sum2)==0){
						$predaypmoney=$predaypmoney.$temppredaypmoney.'<th class="money" id="bold" style="text-align:right;';if($index%2==1)$predaypmoney=$predaypmoney.'background-color:#f0f0f0;';else $predaypmoney=$predaypmoney.'background-color:#ffffff;';$predaypmoney=$predaypmoney.'"><div>0</div></th></tr>';
					}
					else{
						$predaypmoney=$predaypmoney.$temppredaypmoney.'<th class="money" id="bold" style="text-align:right;';if($index%2==1)$predaypmoney=$predaypmoney.'background-color:#f0f0f0;';else $predaypmoney=$predaypmoney.'background-color:#ffffff;';$predaypmoney=$predaypmoney.'"><div>'.number_format(round($sum1/$sum2,1)).'</div></th></tr>';
					}
				}
				else{
				}
				echo $predaycharge.$predaycashmoney.$predaycash.$predaymoney.$predaynumber.$predaypmoney;
				echo '</tfoot></table>';
			}
		}
	}
	sqlclose($connm,'sqlite');
}
else{
}
?>