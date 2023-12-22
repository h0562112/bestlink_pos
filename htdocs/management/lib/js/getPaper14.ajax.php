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
		$sql='SELECT fronttype,inumber FROM itemsdata ORDER BY replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(fronttype))), "\'", ""), "0", "0")||fronttype,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(frontsq))), "\'", ""), "0", "0")||frontsq,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(inumber))), "\'", ""), "0", "0")||inumber';
		$menuarray=sqlquery($connm,$sql,'sqlite');
		if(sizeof($menuarray)==0){
			echo '菜單資料尚未新增。';
		}
		else{
			if($_SESSION['DB']==''){
				$itemname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-menu.ini',true);
				$frontname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-front.ini',true);
				$tastename=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/'.$_POST['company'].'-taste.ini',true);
				$init=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/initsetting.ini',true);
			}
			else{
				$itemname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-menu.ini',true);
				$frontname=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-front.ini',true);
				$tastename=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-taste.ini',true);
				$init=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
			}
			
			$front=array();//產品編號反查目前類別編號
			$menu=array();//暫存menu產品陣列
			$taste=array();//暫存加料與備註陣列
			$list=array();//暫存帳單資料
			foreach($menuarray as $i){
				if(isset($itemname[intval($i['inumber'])])){
					$front[intval($i['inumber'])]=intval($i['fronttype']);
					$menu[intval($i['fronttype'])]['name']=$frontname[intval($i['fronttype'])]['name1'];
					$menu[intval($i['fronttype'])]['qty']=0;
					$menu[intval($i['fronttype'])]['amt']=0;
				}
				else{
				}
			}
			$menu['td']['name']='備註';
			$menu['td']['qty']=0;
			$menu['td']['amt']=0;
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
						$sql='SELECT BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMDEPTCODE,SUM(QTY) AS QTY,UNITPRICELINK,UNITPRICE,SUM(AMT) AS AMT,REMARKS FROM CST012 WHERE ((DTLMODE<>"9" AND DTLTYPE<>"9" AND DTLFUNC<>"99") OR (DTLMODE<>"4" AND DTLTYPE<>"1" AND DTLFUNC<>"01") OR (DTLMODE<>"3" AND DTLTYPE<>"1" AND DTLFUNC<>"01")) AND ITEMCODE<>"0000000000000000" AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'") GROUP BY  BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,UNITPRICE,ITEMCODE,ITEMDEPTCODE,REMARKS ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER ASC';
						$first=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT BIZDATE,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,QTY,UNITPRICE,AMT,REMARKS FROM CST012 WHERE ((DTLMODE<>"9" AND DTLTYPE<>"9" AND DTLFUNC<>"99") OR (DTLMODE<>"4" AND DTLTYPE<>"1" AND DTLFUNC<>"01") OR (DTLMODE<>"3" AND DTLTYPE<>"1" AND DTLFUNC<>"01")) AND ITEMCODE<>"0000000000000000" AND SELECTIVEITEM1 IS NOT NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'")';
						$second=sqlquery($conn,$sql,'sqlite');
						$sql='SELECT SUM(persons) AS QTY,BIZDATE,SUM(SALESTTLAMT) AS AMT,SUM(TAX2) AS cashmoney,SUM(TAX3) AS cash,REMARKS FROM (SELECT CASE WHEN (TAX6+TAX7+TAX8)=0 THEN 1 ELSE (TAX6+TAX7+TAX8) END AS persons,BIZDATE,SALESTTLAMT,TAX2,TAX3,REMARKS FROM CST011 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND NBCHKNUMBER IS NULL) GROUP BY BIZDATE,REMARKS';
						$listarray=sqlquery($conn,$sql,'sqlite');
						if(sizeof($first)==0){
							echo '搜尋時間區間並無資料。';
						}
						else{
							foreach($first as $item){
								if($item['DTLMODE']=='1'&&$item['DTLTYPE']=='1'&&$item['DTLFUNC']=='01'){
									if(isset($menu[$front[intval($item['ITEMCODE'])]][$item['BIZDATE']]['qty'])){
										$menu[$front[intval($item['ITEMCODE'])]][$item['BIZDATE']]['qty']=floatval($menu[$front[intval($item['ITEMCODE'])]][$item['BIZDATE']]['qty'])+floatval($item['QTY']);
										$menu[$front[intval($item['ITEMCODE'])]][$item['BIZDATE']]['amt']=floatval($menu[$front[intval($item['ITEMCODE'])]][$item['BIZDATE']]['amt'])+(floatval($item['QTY'])*floatval($item['UNITPRICE']));
									}
									else{
										$menu[$front[intval($item['ITEMCODE'])]][$item['BIZDATE']]['qty']=floatval($item['QTY']);
										$menu[$front[intval($item['ITEMCODE'])]][$item['BIZDATE']]['amt']=floatval($item['QTY'])*floatval($item['UNITPRICE']);
									}
									$menu[$front[intval($item['ITEMCODE'])]]['qty']=floatval($menu[$front[intval($item['ITEMCODE'])]]['qty'])+floatval($item['QTY']);
									$menu[$front[intval($item['ITEMCODE'])]]['amt']=floatval($menu[$front[intval($item['ITEMCODE'])]]['amt'])+(floatval($item['QTY'])*floatval($item['UNITPRICE']));
								}
								else{
								}
							}
							foreach($second as $taste){
								for($i=1;$i<10;$i++){
									if($taste['SELECTIVEITEM'.$i]!=''){
										if(substr($taste['SELECTIVEITEM'.$i],0,5)!='99999'){
											if(isset($menu['td'][$taste['BIZDATE']]['qty'])){
												$menu['td'][$taste['BIZDATE']]['qty']=intval($menu['td'][$taste['BIZDATE']]['qty'])+((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']);
											}
											else{
												$menu['td'][$taste['BIZDATE']]['qty']=((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']);
											}
											if(isset($menu['td'][$taste['BIZDATE']]['amt'])){
												$menu['td'][$taste['BIZDATE']]['amt']=floatval($menu['td'][$taste['BIZDATE']]['amt'])+((floatval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']*floatval($tastename[(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['money']));
											}
											else{
												$menu['td'][$taste['BIZDATE']]['amt']=((floatval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']*floatval($tastename[(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['money']));
											}
											$menu['td']['qty']=intval($menu['td']['qty'])+((intval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']);
											$menu['td']['amt']=floatval($menu['td']['amt'])+((floatval($taste['SELECTIVEITEM'.$i])%10)*$taste['QTY']*floatval($tastename[(int)(intval($taste['SELECTIVEITEM'.$i])/10)]['money']));
										}
										else{
										}
									}
									else{
										break;
									}
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
				foreach($menu as $m){
					echo '<th colspan="2" id="bold" style="text-align:center;">'.$m['name'].'</th>';
				}
				echo '</tr>';
				echo '<tr><th></th>';
				foreach($menu as $m){
					echo "<th id='bold' style='padding:5px;text-align:center;' nowrap='nowrap'>數量</th>";
					echo "<th id='bold' style='padding:5px;text-align:center;' nowrap='nowrap'>金額</th>";
				}
				echo '</tr>';
				if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
					$ENDDATE=strtotime(date('Ymd'));
				}
				else{
					$ENDDATE=strtotime(date('Ymd',strtotime($end)));
				}
				echo '</thead><tbody>';
				if(isset($menu)&&sizeof($menu)){
					for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
						echo '<tr><td style="padding:5px 5px 5px 10px;">'.substr(date('Ymd',$d),2,6);
						switch (date("N",$d)) {
							case 1:
								echo "(一)";
								break;
							case 2:
								echo "(二)";
								break;
							case 3:
								echo "(三)";
								break;
							case 4:
								echo "(四)";
								break;
							case 5:
								echo "(五)";
								break;
							case 6:
								echo "<span style='font-weight:bold;color:#C13333;'>(六)</span>";
								break;
							case 7:
								echo "<span style='font-weight:bold;color:#C13333;'>(日)</span>";
								break;
							default:
								break;
						}
						echo '</td>';
						foreach($menu as $k => $i){
							if(isset($i[date('Ymd',$d)]['qty'])){
								echo '<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i[date('Ymd',$d)]['qty']).'</td><td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i[date('Ymd',$d)]['amt']).'</td>';
							}
							else{
								echo '<td style="text-align:right;padding:5px 5px 5px 10px;">0</td><td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
							}
						}
						echo '</tr>';
					}
					echo '<tr><td style="padding:5px 5px 5px 10px;">總計</td>';
					//print_r($menu);
					foreach($menu as $k => $i){
						if(isset($i['qty'])){
							echo '<td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i['qty']).'</td><td style="text-align:right;padding:5px 5px 5px 10px;">'.number_format($i['amt']).'</td>';
						}
						else{
							echo '<td style="text-align:right;padding:5px 5px 5px 10px;">0</td><td style="text-align:right;padding:5px 5px 5px 10px;">0</td>';
						}
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