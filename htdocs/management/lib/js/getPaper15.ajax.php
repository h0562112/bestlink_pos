<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);

if(isset($_POST['startdate'])){
	//echo 'where file'.'../../../11/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/menu.db<br>';
	//echo 'DB is exists? '.file_exists('../../../11/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/menu.db').'<br>';
	if($_SESSION['DB']==''){
		$connm=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'menu.db','','','','sqlite');
	}
	else{
		$connm=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'menu.db','','','','sqlite');
	}
	if(!$connm){
		//echo $_SESSION['company'].'<br>'.$_SESSION['DB'].'<br>'.'菜單資料遺失。';
		echo '菜單資料遺失。';
	}
	else{
		$sql='SELECT reartype,inumber FROM itemsdata ORDER BY replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(reartype))), "\'", ""), "0", "0")||reartype,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(rearsq))), "\'", ""), "0", "0")||rearsq,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(inumber))), "\'", ""), "0", "0")||inumber';
		$menuarray=sqlquery($connm,$sql,'sqlite');
		if(sizeof($menuarray)==0){
			echo '菜單資料尚未新增。';
		}
		else{
			if($_SESSION['DB']==''){
				$itemname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-menu.ini',true);
				$rearname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-rear.ini',true);
				$tastename=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-taste.ini',true);
				$init=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/initsetting.ini',true);
			}
			else{
				$itemname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-menu.ini',true);
				$rearname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-rear.ini',true);
				$tastename=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-taste.ini',true);
				$init=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
			}
			
			$rear=array();//產品編號反查目前類別編號
			$menu=array();//暫存menu產品陣列
			$taste=array();//暫存加料與備註陣列
			$list=array();//暫存帳單資料
			foreach($menuarray as $i){
				if(isset($itemname[intval($i['inumber'])])){
					$rear[intval($i['inumber'])]=intval($i['reartype']);
					$menu[intval($i['reartype'])]['name']=$rearname[intval($i['reartype'])]['name'];
					$menu[intval($i['reartype'])]['zcounter']=0;
					$menu[intval($i['reartype'])]['qty']=0;
					$menu[intval($i['reartype'])]['amt']=0;
					$menu['charge']['name']='服務費';
					$menu['dis']['name']='折扣';
					$menu['saleamt']['name']='營收';
				}
				else{
				}
			}
			$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
			$complete=0;
			for($m=0;$m<=$totalMon;$m++){
				if($_SESSION['DB']==''){
					$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
				}
				else{
					$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
				}
				if(!$conn){
					echo '資料庫尚未上傳資料。';
				}
				else{
					$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
					$res=sqlquery($conn,$sql,'sqlite');
					if(isset($res[0]['name'])){
						if(isset($_POST['classgroup'])){
							$sql='SELECT BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMDEPTCODE,SUM(QTY) AS QTY,UNITPRICELINK,UNITPRICE,SUM(AMT) AS AMT,REMARKS,ZCOUNTER FROM CST012 WHERE ((DTLMODE<>"9" AND DTLTYPE<>"9" AND DTLFUNC<>"99") OR (DTLMODE<>"4" AND DTLTYPE<>"1" AND DTLFUNC<>"01") OR (DTLMODE<>"3" AND DTLTYPE<>"1" AND DTLFUNC<>"01")) AND ITEMCODE<>"0000000000000000" AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'") GROUP BY  BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,UNITPRICE,ITEMCODE,ITEMDEPTCODE,REMARKS,ZCOUNTER ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER ASC';
							$sql2='SELECT BIZDATE,ZCOUNTER,SUM(TAX1) AS tax,SUM(SALESTTLAMT+TAX1) AS amt FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY BIZDATE,ZCOUNTER ORDER BY BIZDATE ASC,ZCOUNTER ASC';
						}
						else{
							$sql='SELECT BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMDEPTCODE,SUM(QTY) AS QTY,UNITPRICELINK,UNITPRICE,SUM(AMT) AS AMT,REMARKS,1 AS ZCOUNTER FROM CST012 WHERE ((DTLMODE<>"9" AND DTLTYPE<>"9" AND DTLFUNC<>"99") OR (DTLMODE<>"4" AND DTLTYPE<>"1" AND DTLFUNC<>"01") OR (DTLMODE<>"3" AND DTLTYPE<>"1" AND DTLFUNC<>"01")) AND ITEMCODE<>"0000000000000000" AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'") GROUP BY  BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,UNITPRICE,ITEMCODE,ITEMDEPTCODE,REMARKS ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER ASC';
							$sql2='SELECT BIZDATE,1 AS ZCOUNTER,SUM(TAX1) AS tax,SUM(SALESTTLAMT+TAX1) AS amt FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY BIZDATE ORDER BY BIZDATE ASC';
						}
						//echo $sql;
						$first=sqlquery($conn,$sql,'sqlite');
						$second=sqlquery($conn,$sql2,'sqlite');
						if(sizeof($first)==0){
							echo '搜尋時間區間並無資料。';
						}
						else{
							foreach($first as $item){
								if($item['DTLMODE']=='1'&&$item['DTLTYPE']=='1'&&$item['DTLFUNC']=='01'){
									if(isset($menu[$rear[intval($item['ITEMCODE'])]][$item['BIZDATE']][$item['ZCOUNTER']]['qty'])){
										$menu[$rear[intval($item['ITEMCODE'])]][$item['BIZDATE']][$item['ZCOUNTER']]['qty']=floatval($menu[$rear[intval($item['ITEMCODE'])]][$item['BIZDATE']][$item['ZCOUNTER']]['qty'])+floatval($item['QTY']);
										$menu[$rear[intval($item['ITEMCODE'])]][$item['BIZDATE']][$item['ZCOUNTER']]['amt']=floatval($menu[$rear[intval($item['ITEMCODE'])]][$item['BIZDATE']][$item['ZCOUNTER']]['amt'])+(floatval($item['QTY'])*floatval($item['UNITPRICE']));
									}
									else{
										$menu[$rear[intval($item['ITEMCODE'])]][$item['BIZDATE']][$item['ZCOUNTER']]['qty']=floatval($item['QTY']);
										$menu[$rear[intval($item['ITEMCODE'])]][$item['BIZDATE']][$item['ZCOUNTER']]['amt']=floatval($item['QTY'])*floatval($item['UNITPRICE']);
									}
									if(isset($menu[$rear[intval($item['ITEMCODE'])]]['zcounter'])&&intval($menu[$rear[intval($item['ITEMCODE'])]]['zcounter'])>=$item['ZCOUNTER']){
									}
									else{
										$menu[$rear[intval($item['ITEMCODE'])]]['zcounter']=$item['ZCOUNTER'];
									}
									$menu[$rear[intval($item['ITEMCODE'])]]['qty']=floatval($menu[$rear[intval($item['ITEMCODE'])]]['qty'])+floatval($item['QTY']);
									$menu[$rear[intval($item['ITEMCODE'])]]['amt']=floatval($menu[$rear[intval($item['ITEMCODE'])]]['amt'])+(floatval($item['QTY'])*floatval($item['UNITPRICE']));
								}
								else{
									if(isset($menu['dis'][$item['BIZDATE']][$item['ZCOUNTER']]['amt'])){
										$menu['dis'][$item['BIZDATE']][$item['ZCOUNTER']]['amt']=floatval($menu['dis'][$item['BIZDATE']][$item['ZCOUNTER']]['amt'])+floatval($item['AMT']);
									}
									else{
										$menu['dis'][$item['BIZDATE']][$item['ZCOUNTER']]['amt']=floatval($item['AMT']);
									}
									if(isset($menu['dis']['amt'])){
										$menu['dis']['amt']=floatval($menu['dis']['amt'])+floatval($item['AMT']);
									}
									else{
										$menu['dis']['amt']=floatval($item['AMT']);
									}
								}
							}
							foreach($second as $item){
								$menu['charge'][$item['BIZDATE']][$item['ZCOUNTER']]['amt']=$item['tax'];
								$menu['saleamt'][$item['BIZDATE']][$item['ZCOUNTER']]['amt']=$item['amt'];
								if(isset($menu['charge']['amt'])){
									$menu['charge']['amt']=floatval($menu['charge']['amt'])+floatval($item['tax']);
									$menu['saleamt']['amt']=floatval($menu['saleamt']['amt'])+floatval($item['amt']);
								}
								else{
									$menu['charge']['amt']=floatval($item['tax']);
									$menu['saleamt']['amt']=floatval($item['amt']);
								}
							}
						}
					}
					else{
						$complete++;
					}
				}
				sqlclose($conn,'sqlite');
			}
			//print_r($menu);
			
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
				if(isset($_POST['classgroup'])){
					echo '<th rowspan="2">班別</th>';
				}
				else{
				}
				foreach($menu as $l=>$m){
					if(is_numeric($l)){
						echo '<th colspan="2" id="bold" style="text-align:center;">'.$m['name'].'</th>';
					}
					else{
					}
				}
				echo '<th id="bold" style="text-align:center;">'.$menu['dis']['name'].'</th><th id="bold" style="text-align:center;">'.$menu['charge']['name'].'</th><th id="bold" style="text-align:center;">'.$menu['saleamt']['name'].'</th>';
				echo '</tr>';
				echo '<tr><th></th>';
				foreach($menu as $l=>$m){
					if(is_numeric($l)){
						echo "<th id='bold' style='padding:5px;text-align:center;' nowrap='nowrap'>數量</th>";
						echo "<th id='bold' style='padding:5px;text-align:center;' nowrap='nowrap'>金額</th>";
					}
					else{
					}
				}
				echo "<th id='bold' style='padding:5px;text-align:center;' nowrap='nowrap'>金額</th><th id='bold' style='padding:5px;text-align:center;' nowrap='nowrap'>金額</th><th id='bold' style='padding:5px;text-align:center;' nowrap='nowrap'>金額</th>";
				echo '</tr>';
				if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
					$ENDDATE=strtotime(date('Ymd'));
				}
				else{
					$ENDDATE=strtotime(date('Ymd',strtotime($end)));
				}
				echo '</thead><tbody>';
				$max=0;
				foreach($menu as $v=>$i){
					if(!isset($i['zcounter'])||intval($max)>=intval($i['zcounter'])){
					}
					else{
						$max=$i['zcounter'];
					}
				}
				if(isset($menu)&&sizeof($menu)){
					for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
						$emptyz=0;
						for($z=1;$z<=$max;$z++){
							$ztime=0;
							$temphtml='';
							$temphtml2='';
							if($emptyz==0){
								$temphtml='<tr><td style="padding:5px 5px 5px 10px;">'.substr(date('Ymd',$d),2,6);
								switch (date("N",$d)) {
									case 1:
										$temphtml=$temphtml."(一)";
										break;
									case 2:
										$temphtml=$temphtml."(二)";
										break;
									case 3:
										$temphtml=$temphtml."(三)";
										break;
									case 4:
										$temphtml=$temphtml."(四)";
										break;
									case 5:
										$temphtml=$temphtml."(五)";
										break;
									case 6:
										$temphtml=$temphtml."<span style='font-weight:bold;color:#C13333;'>(六)</span>";
										break;
									case 7:
										$temphtml=$temphtml."<span style='font-weight:bold;color:#C13333;'>(日)</span>";
										break;
									default:
										break;
								}
								$temphtml=$temphtml.'</td>';
							}
							else{
								$temphtml='<tr><td></td>';
							}
							foreach($menu as $k => $i){
								if(is_numeric($k)){
									if(isset($i[date('Ymd',$d)][$z]['qty'])){
										$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i[date('Ymd',$d)][$z]['qty']).'</td><td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i[date('Ymd',$d)][$z]['amt']).'</td>';
										$ztime=intval($ztime)+intval($i[date('Ymd',$d)][$z]['qty']);
									}
									else{
										/*if($k=='charge'||$k=='dis'||$k=='saleamt'){
											$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">0</td><td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i[date('Ymd',$d)][$z]['amt']).'</td>';
										}
										else{*/
											$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">0</td><td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
										//}
									}
								}
								else{
								}
							}
							if(isset($menu['dis'][date('Ymd',$d)][$z]['amt'])){
								$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($menu['dis'][date('Ymd',$d)][$z]['amt']).'</td>';
							}
							else{
								$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
							}
							if(isset($menu['charge'][date('Ymd',$d)][$z]['amt'])){
								$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($menu['charge'][date('Ymd',$d)][$z]['amt']).'</td>';
							}
							else{
								$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
							}
							if(isset($menu['saleamt'][date('Ymd',$d)][$z]['amt'])){
								$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($menu['saleamt'][date('Ymd',$d)][$z]['amt']).'</td>';
							}
							else{
								$temphtml2=$temphtml2.'<td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
							}
							$temphtml2=$temphtml2.'</tr>';
							if(intval($ztime)>0){
								if(isset($_POST['classgroup'])){
									echo $temphtml.'<td>'.$z.'</td>'.$temphtml2;
								}
								else{
									echo $temphtml.$temphtml2;
								}
								$emptyz=1;
							}
							else{
							}
						}
					}
					echo '<tr><td style="padding:5px 5px 5px 10px;">總計</td>';
					//print_r($menu);
					if(isset($_POST['classgroup'])){
						echo '<td></td>';
					}
					else{
					}
					foreach($menu as $k => $i){
						if(is_numeric($k)){
							if(isset($i['qty'])){
								echo '<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i['qty']).'</td><td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i['amt']).'</td>';
							}
							else{
								echo '<td style="text-align:right;padding:5px 5px 5px 10px;">0</td><td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
							}
						}
						else{
						}
					}
					if(isset($menu['dis']['amt'])){
						echo '<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($menu['dis']['amt']).'</td>';
					}
					else{
						echo '<td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
					}
					if(isset($menu['charge']['amt'])){
						echo '<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($menu['charge']['amt']).'</td>';
					}
					else{
						echo '<td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
					}
					if(isset($menu['saleamt']['amt'])){
						echo '<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($menu['saleamt']['amt']).'</td>';
					}
					else{
						echo '<td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
					}
					echo '</tr>';
				}
				else{
				}
				echo '</tbody></table>';
			}
		}
	}
	sqlclose($connm,'sqlite');
}
else{
}
?>