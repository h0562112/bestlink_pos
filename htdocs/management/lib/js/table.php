<?php
session_start();
?>
<script>
paper=$('.table').tabs();
</script>
<?php
$initsetting=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/temp/timem1.ini')){
	$timeini=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/temp/timem1.ini',true);
	$bizdate=$timeini['time']['bizdate'];
	$zcounter=$timeini['time']['zcounter'];
}
else{
	$machinedata=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/temp/machinedata.ini',true);
	$bizdate=$machinedata['basic']['bizdate'];
	$zcounter=$machinedata['basic']['zcounter'];
}
$tb=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/floorspend.ini',true);
date_default_timezone_set('Asia/Taipei');
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
?>
<div class='table' style="overflow:hidden;margin-bottom:3px;">
	<ul style='width:100%;float:left;-webkit-box-sizing: efborder-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		<li><a href='#table'><?php if($interface!='-1'&&isset($interface['name']['tablemenu']))echo $interface['name']['tablemenu'];else echo '即時桌控'; ?></a></li>
	</ul>
	<div id='table' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['tabletitle']))echo $interface['name']['tabletitle'];else echo '即時桌控(30秒自動更新)'; ?></center></h1>
		<div class='table' id="parent" style='position: relative;width:100%;border:1px solid #898989;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		<?php
		$nowtime=date_create(date('YmdHis'));
		include_once '../../../tool/dbTool.inc.php';
		if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/temp/SALES_'.substr($bizdate,0,6).'.db')){
			$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/temp','SALES_'.substr($bizdate,0,6).'.db','','','','sqlite');
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="tempCST011"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$bizdate.'" AND ZCOUNTER="'.$zcounter.'" ORDER BY TABLENUMBER';
				$list=sqlquery($conn,$sql,'sqlite');
				echo '<div style="display:none">';
				print_r($list);
				echo '</div>';
				$sql='SELECT SUM(amt) AS amt,SUM(qty) AS qty FROM (SELECT (SALESTTLAMT+TAX1) AS amt,(TAX6+TAX7+TAX8) AS qty FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND ZCOUNTER="'.$zcounter.'" AND NBCHKNUMBER IS NULL)';
				$result=sqlquery($conn,$sql,'sqlite');
				//echo $sql;
				$ttt=array();
				$tempperson=0;
				$tempamt=0;
				$tablelist=array();
				foreach($list as $l){
					if($l['REMARKS']=='1'){
						if(preg_match('/,/',$l['TABLENUMBER'])){
							$temp=preg_split('/,/',$l['TABLENUMBER']);
							foreach($temp as $t){
								$st=preg_split('/-/',$t);
								for($stl=1;$stl<sizeof($st)-1;$stl++){
									$st[0]=$st[0].'-'.$st[$stl];
								}
								if(in_array($st[0],$tablelist)){
								}
								else{
									array_push($tablelist,$st[0]);
								}
								if(isset($ttt[$st[0]])){
									$ttt[$st[0]]['split']=1;
								}
								else{
									$ttt[$st[0]]['split']=0;
								}
								$ttt[$st[0]]['inittablenum']=$t;
								$ttt[$st[0]]['consecnumber']=$l['CONSECNUMBER'];
								$ttt[$st[0]]['bizdate']=$l['BIZDATE'];
								$ttt[$st[0]]['amt']=$l['SALESTTLAMT'];
								$ttt[$st[0]]['persons']=intval($l['TAX6'])+intval($l['TAX7'])+intval($l['TAX8']);
								$ttt[$st[0]]['createdatetime']=$l['CREATEDATETIME'];
							}
						}
						else{
							$st=preg_split('/-/',$l['TABLENUMBER']);
							for($stl=1;$stl<sizeof($st)-1;$stl++){
								$st[0]=$st[0].'-'.$st[$stl];
							}
							if(in_array($st[0],$tablelist)){
							}
							else{
								array_push($tablelist,$st[0]);
							}
							if(isset($ttt[$st[0]])){
								$ttt[$st[0]]['split']=1;
							}
							else{
								$ttt[$st[0]]['split']=0;
							}
							$ttt[$st[0]]['inittablenum']=$l['TABLENUMBER'];
							$ttt[$st[0]]['consecnumber']=$l['CONSECNUMBER'];
							$ttt[$st[0]]['bizdate']=$l['BIZDATE'];
							$ttt[$st[0]]['amt']=$l['SALESTTLAMT'];
							$ttt[$st[0]]['persons']=intval($l['TAX6'])+intval($l['TAX7'])+intval($l['TAX8']);
							$ttt[$st[0]]['createdatetime']=$l['CREATEDATETIME'];
						}
						$tempperson=intval($tempperson)+intval($ttt[$st[0]]['persons']);
						$tempamt=intval($tempamt)+intval($ttt[$st[0]]['amt']);
					}
					else{
					}
				}
			}
			else{
				echo '資料上傳中。';
			}
			sqlclose($conn,'sqlite');
		}
		else{
		}
		?>
		<div style='position: relative;top:0;width:calc(100% - 2px);margin:1px;padding:0;float:left;'>
			<table>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['notsale']))echo $interface['name']['notsale'];else echo '未結金額'; ?></td>
					<td style='font-size:40px;'><?php if(isset($tempamt))echo number_format($tempamt);else echo '0'; ?></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['totalsale']))echo $interface['name']['totalsale'];else echo '營業額'; ?></td>
					<td style='font-size:40px;'><?php if(isset($result)&&sizeof($result)>0&&isset($result[0]['amt']))echo number_format(floatval($result[0]['amt']));else echo '0'; ?></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['totaltable']))echo $interface['name']['totaltable'];else echo '總桌數'; ?></td>
					<td style='font-size:40px;'><?php echo number_format($tb['TA']['number']); ?></td>
				</tr>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['nowperson']))echo $interface['name']['nowperson'];else echo '用餐人數'; ?></td>
					<td style='font-size:40px;'><?php if(isset($tempperson))echo number_format($tempperson);else echo '0'; ?></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['totalperson']))echo $interface['name']['totalperson'];else echo '累計人數'; ?></td>
					<td style='font-size:40px;'><?php if(isset($result)&&sizeof($result)>0&&isset($result[0]['qty']))echo number_format(floatval($result[0]['qty']));else echo '0'; ?></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['openedtable']))echo $interface['name']['openedtable'];else echo '已開桌'; ?></td>
					<td style='font-size:40px;'><?php if(isset($tablelist))echo number_format(sizeof($tablelist));else{echo '0';$tablelist=array();} ?></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['emptytable']))echo $interface['name']['emptytable'];else echo '空桌'; ?></td>
					<td style='font-size:40px;'><?php echo number_format(intval($tb['TA']['number'])-intval(sizeof($tablelist))); ?></td>
				</tr>
			</table>
		</div>
		<div style='width:100%;float:left;'>
			<table>
				<tr>
					<td style='width:40px;height:40px;background-color:#ff0066;'></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['openedtable']))echo $interface['name']['openedtable'];else echo '已開桌'; ?></td>
					<td style='width:40px;height:40px;background-color:#73d7ec;'></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['firstalert']))echo $interface['name']['firstalert'];else echo '第一次提示'; ?></td>
					<td style='width:40px;height:40px;background-color:#e7f12c;'></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['secondalert']))echo $interface['name']['secondalert'];else echo '第二次提示'; ?></td>
					<td style='width:40px;height:40px;background-color:#00d941;'></td>
					<td><?php if($interface!='-1'&&isset($interface['name']['timeup']))echo $interface['name']['timeup'];else echo '時間已到'; ?></td>
				</tr>
			</table>
		</div>
		<div style='width:100%;height:100%;position:relative;overflow:hidden;float:left;'>
		<?php
		$totaltable=0;
		$ordertable=0;
		//print_r($ttt);
		if(isset($tb['TA']['page'])&&intval($tb['TA']['page'])>=1){
			$startindex=1;
			$labelbox="<div id='butbox' style='width:100%;overflow:hidden;position: absolute;top:10px;'>";
			for($page=1;$page<=$tb['TA']['page'];$page++){
				$labelbox.='<button id="page'.$page.'button" style="width:75px;height:100%;float:left;" class="w3-bar-item w3-button ';if($page==1)$labelbox.='focus';$labelbox.='" onclick="openCity(\'inittable\',\'page'.$page.'\')">';
				if(isset($tb['TA']['controlfloor'.$page])){
					$labelbox.=$tb['TA']['controlfloor'.$page];
				}
				else{
					$labelbox.=$page.'樓';
				}
				$labelbox.='</button>';
				echo '<div id="page'.$page.'" class="w3-container inittableItem" style="height:calc(100% - 50px);overflow:hidden;position: relative;top:50px;';if($page==1)echo 'display:block;';else echo 'display:none;';echo '">';
				if(isset($tb['TA']['row'.$page])){
					$pagerow=$tb['TA']['row'.$page];
				}
				else{
					$pagerow=$tb['TA']['row'];
				}
				if(isset($tb['TA']['col'.$page])){
					$pagecol=$tb['TA']['col'.$page];
				}
				else{
					$pagecol=$tb['TA']['col'];
				}
				for($i=$startindex;$i<=(intval($pagerow)*intval($pagecol)+$startindex-1);$i++){
					echo '<div style="width:calc(100% / '.$pagecol.');height:calc(100% / '.$pagerow.');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
					if($tb['T'.$i]['tablename']==''){
					}
					else{
						if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
							if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
								//$mins=intval(substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],8,2))*60+intval(substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],10,2));
								$maxtime=date_create(date('YmdHis',strtotime($ttt[$tb['T'.$i]['tablename']]['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
								$diff=date_diff($nowtime,$maxtime);
								$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
								if(floatval($mins)>floatval($initsetting['init']['hinttime'])){//已開桌未到提示時間
									$t=1;
									$time=floatval($mins);
								}
								else if(floatval($mins)<=0){//用餐時間已到
									$t=-1;
									$time=0;
								}
								else if(floatval($mins)<=floatval($initsetting['init']['sechinttime'])){//已開桌且到第二提示時間
									$t=-2;
									$time=floatval($mins);
								}
								else{//已開桌且到第一提示時間但未到第二提示時間
									$t=2;
									$time=floatval($mins);
								}
							}
							else{//未開桌
								$t='';
								$time='';
							}
							?>
							<div class='table' <?php 
							if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
								echo 'id="comput notempty "';
								$ordertable++;
								$totaltable++;
							}
							else{
								echo 'id="comput"';
								$totaltable++;
							}
							if($t==1)echo 'style="border: 5px solid #898989;border-radius: 5px;background-color: #ff0066;color: #ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
							else if($t=='')echo 'style="background-color: buttonface;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
							else if($t==2)echo 'style="background-color:#73d7ec;color:#ffffff;border: 5px solid #898989;border-radius: 5px;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
							else if($t==-2)echo 'style="border: 5px solid #898989;border-radius: 5px;background-color: #e7f12c;color: #000000;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
							else echo 'style="background-color:#00d941;color:#ffffff;border: 5px solid #898989;border-radius: 5px;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
							if(isset($ttt[$tb['T'.$i]['tablename']]['split'])&&$ttt[$tb['T'.$i]['tablename']]['split']==1)echo 'name="split"';
							?>>
								<div id='tablenumber' style='width: 50%;height: 50%;line-height: 130%;float: left;text-align:center;'><?php echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value='<?php if(isset($ttt[$tb['T'.$i]['tablename']]['consecnumber']))echo $ttt[$tb['T'.$i]['tablename']]['consecnumber']; ?>'></div>
								<div id='amt' style='width: 50%;height: 50%;line-height: 130%;float: left;text-align:right;'><?php if(isset($ttt[$tb['T'.$i]['tablename']]['amt']))echo "<span>".$ttt[$tb['T'.$i]['tablename']]['amt']."</span>"; ?></div>
								<div id='persons' style='width: 50%;height: 50%;line-height: 130%;float: left;'><?php if(isset($ttt[$tb['T'.$i]['tablename']]['persons'])&&intval($ttt[$tb['T'.$i]['tablename']]['persons'])>0)echo $ttt[$tb['T'.$i]['tablename']]['persons'];if($interface!='-1'&&isset($interface['name']['personunit']))echo $interface['name']['personunit'];else echo '位'; ?></div>
								<div id='createdatetime' style='width: 50%;height: 50%;line-height: 130%;float: left;text-align:right;'><span id='val'><?php echo $time; ?></span><input type='hidden' name='createdatetime' value='<?php if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime']))echo substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],4,2).'/'.substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],6,2).' '.substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],8,2).':'.substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],10,2); ?>'><input type='hidden' name='bizdate' value='<?php if(isset($ttt[$tb['T'.$i]['tablename']]['bizdate']))echo $ttt[$tb['T'.$i]['tablename']]['bizdate']; ?>'></div>
							</div>
						<?php
						}
						else{
						}
					}
					echo '</div>';
				}
				$startindex=$i;
						
				echo '</div>';
			}
			$labelbox.="</div>";
			echo $labelbox;
		}
		else{
			for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
				echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
				if($tb['T'.$i]['tablename']==''){
				}
				else{
					if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
						if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
							//$mins=intval(substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],8,2))*60+intval(substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],10,2));
							$maxtime=date_create(date('YmdHis',strtotime($ttt[$tb['T'.$i]['tablename']]['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
							$diff=date_diff($nowtime,$maxtime);
							$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
							if(floatval($mins)>floatval($initsetting['init']['hinttime'])){//已開桌未到提示時間
								$t=1;
								$time=floatval($mins);
							}
							else if(floatval($mins)<=0){//用餐時間已到
								$t=-1;
								$time=0;
							}
							else if(floatval($mins)<=floatval($initsetting['init']['sechinttime'])){//已開桌且到第二提示時間
								$t=-2;
								$time=floatval($mins);
							}
							else{//已開桌且到第一提示時間但未到第二提示時間
								$t=2;
								$time=floatval($mins);
							}
						}
						else{//未開桌
							$t='';
							$time='';
						}
						?>
						<div class='table' <?php 
						if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
							echo 'id="comput notempty "';
							$ordertable++;
							$totaltable++;
						}
						else{
							echo 'id="comput"';
							$totaltable++;
						}
						if($t==1)echo 'style="border: 5px solid #898989;border-radius: 5px;background-color: #ff0066;color: #ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
						else if($t=='')echo 'style="background-color: buttonface;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
						else if($t==2)echo 'style="background-color:#73d7ec;color:#ffffff;border: 5px solid #898989;border-radius: 5px;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
						else if($t==-2)echo 'style="border: 5px solid #898989;border-radius: 5px;background-color: #e7f12c;color: #000000;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
						else echo 'style="background-color:#00d941;color:#ffffff;border: 5px solid #898989;border-radius: 5px;width:100%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:1.1vw;"';
						if(isset($ttt[$tb['T'.$i]['tablename']]['split'])&&$ttt[$tb['T'.$i]['tablename']]['split']==1)echo 'name="split"';
						?>>
							<div id='tablenumber' style='width: 50%;height: 50%;line-height: 130%;float: left;text-align:center;'><?php echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value='<?php if(isset($ttt[$tb['T'.$i]['tablename']]['consecnumber']))echo $ttt[$tb['T'.$i]['tablename']]['consecnumber']; ?>'></div>
							<div id='amt' style='width: 50%;height: 50%;line-height: 130%;float: left;text-align:right;'><?php if(isset($ttt[$tb['T'.$i]['tablename']]['amt']))echo "<span>".$ttt[$tb['T'.$i]['tablename']]['amt']."</span>"; ?></div>
							<div id='persons' style='width: 50%;height: 50%;line-height: 130%;float: left;'><?php if(isset($ttt[$tb['T'.$i]['tablename']]['persons'])&&intval($ttt[$tb['T'.$i]['tablename']]['persons'])>0)echo $ttt[$tb['T'.$i]['tablename']]['persons'];if($interface!='-1'&&isset($interface['name']['personunit']))echo $interface['name']['personunit'];else echo '位'; ?></div>
							<div id='createdatetime' style='width: 50%;height: 50%;line-height: 130%;float: left;text-align:right;'><span id='val'><?php echo $time; ?></span><input type='hidden' name='createdatetime' value='<?php if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime']))echo substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],4,2).'/'.substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],6,2).' '.substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],8,2).':'.substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],10,2); ?>'><input type='hidden' name='bizdate' value='<?php if(isset($ttt[$tb['T'.$i]['tablename']]['bizdate']))echo $ttt[$tb['T'.$i]['tablename']]['bizdate']; ?>'></div>
						</div>
					<?php
					}
					else{
					}
				}
				echo '</div>';
			}
		}
		?>
		</div>
	</div>
</div>