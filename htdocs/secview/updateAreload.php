<?php
$title=parse_ini_file('./img/secview.ini',true);
$init=parse_ini_file('../database/initsetting.ini',true);
$setup=parse_ini_file('../database/setup.ini',true);
include_once '../tool/dbTool.inc.php';
if(file_exists("../database/sale/temp".$_GET['machine'].".db")){
}
else{
	if(file_exists("../database/sale/EMtemp.db")){
	}
	else{
		include_once './create.emptyDB.php';
		echo create('EMtemp');
		copy("../database/sale/EMtemp.db","../database/sale/temp".$_GET['machine'].".db");
	}
}
if(file_exists('../database/sale/temp'.$_GET['machine'].'.db')){
	$taste=parse_ini_file('../database/'.$setup['basic']['company'].'-taste.ini',true);

	$conn=sqlconnect('../database/sale','temp'.$_GET['machine'].'.db','','','','sqlite');
	$sql='SELECT *,(SELECT SUM(QTY) FROM list WHERE ITEMCODE!="item" AND TERMINALNUMBER="'.$_GET['machine'].'") AS TOTALQTY,(SELECT SUM(AMT) FROM list WHERE TERMINALNUMBER="'.$_GET['machine'].'") AS TOTALAMT FROM list WHERE TERMINALNUMBER="'.$_GET['machine'].'" AND LINENUMBER!="listdis" ORDER BY LINENUMBER DESC LIMIT '.(intval($title['rightlist']['maxitems'])*2);
	$list=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT * FROM list WHERE TERMINALNUMBER="'.$_GET['machine'].'" AND LINENUMBER="listdis"';
	$listdis=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT number FROM ban WHERE TERMINALNUMBER="'.$_GET['machine'].'"';
	$ban=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT tel,point FROM phone WHERE TERMINALNUMBER="'.$_GET['machine'].'"';
	$pointtree=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(sizeof($list)==0){
		echo '<div class="toplabel" style="height:102px;border-bottom: 3px solid #ffffff;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">'.$title['title']['text'].'</div>';
	}
	else{
	?>
		<div class="toplabel" style='width:100%;font-size:25px;height:102px;float:left;margin-bottom:4px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border-bottom: 3px solid #ffffff;'>
		<?php
		if(isset($init['init']['pointtree'])&&$init['init']['pointtree']=='1'){
			echo "<div style='width:100%;max-height:50%;height:25%;line-height:24px;float:left;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><table style='width:100%;'><tr><td style='width:auto;'>統編/載具</td><td style='width:auto;'>";
		}
		else{
			echo "<div style='width:100%;max-height:50%;height:50%;line-height:47px;float:left;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><table style='width:100%;'><tr><td style='width:auto;'>統編/載具</td><td style='width:auto;'>";
		}
					if(sizeof($ban)==0){
					}
					else{
						echo $ban[0]['number'];
					}
					?>
						</td>
					</tr>
				</table>
			</div>
		<?php
		if(isset($init['init']['pointtree'])&&$init['init']['pointtree']=='1'){
			echo "<div style='width:100%;max-height:50%;height:25%;line-height:24px;float:left;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					<table style='width:100%;'>
						<tr>
							<td style='width:auto;'>手機</td>
							<td style='width:auto;'>";
								if(sizeof($pointtree)==0){
								}
								else{
									echo $pointtree[0]['tel'];
								}
						echo "</td>
							<td style='width:auto;'>點數</td>
							<td style='width:auto;'>";
								if(sizeof($pointtree)==0){
									echo '0';
								}
								else{
									echo $pointtree[0]['point'];
								}
						echo "</td>
						</tr>
					</table>
				</div>";
		}
		else{
		}
		?>
			<div style='width:100%;max-height:50%;height:50%;line-height:47px;float:left;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<div style='width:50%;float:left;'>
					<span>總共</span><span style='font-size:37px;'><?php echo $list[0]['TOTALQTY']; ?></span><span>商品</span>
				</div>
				<div style='width:50%;float:left;'>
					<span>應收</span><span style='font-size:37px;'><?php echo $init['init']['frontunit'].$list[0]['TOTALAMT'].$init['init']['unit']; ?></span>
				</div>
			</div>
		</div>
		<div class='listtitlelabel' style='width:100%;float:left;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<div style='width:50%;font-size:15px;margin:2px 0;text-align:center;float:left;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border-right:1px solid #f0f0f0;'>Item</div>
			<div style='width:18%;font-size:15px;margin:2px 0;text-align:center;padding:0 3px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border-right:1px solid #f0f0f0;'>U/P</div>
			<div style='width:12%;font-size:15px;margin:2px 0;text-align:center;padding:0 3px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border-right:1px solid #f0f0f0;'>QTY</div>
			<div style='width:20%;font-size:15px;margin:2px 0;text-align:center;padding:0 3px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>Sub</div>
		</div>
		<?php
		for($i=1;$i<sizeof($list);$i=$i+2){
		?>
			<div class='listitemlabel' style='width:100%;overflow:hidden;margin-top:8px;;float:left;border-bottom:1px #898989 solid;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<div style='width:50%;float:left;overflow:hidden;'>
				<?php
				echo $list[$i]['ITEMNAME'];
				if($list[$i]['UNITPRICELINK']==null||strlen($list[$i]['UNITPRICELINK'])==0){
				}
				else{
					echo '('.$list[$i]['UNITPRICELINK'].')';
				}
				?>
				<?php
				if($list[$i]['SELECTIVEITEM1']!=null){
					$tastelist='';
					for($t=1;$t<=10;$t++){
						if(preg_match('/\,/',$list[$i]['SELECTIVEITEM'.$t])){//2022/2/25 現行的備註，都已 ',' 分隔存在selectiveitem1中
							$temoselectiveitem=preg_split('/\,/',$list[$i]['SELECTIVEITEM'.$t]);
							for($ts=0;$ts<sizeof($temoselectiveitem);$ts++){
								if(substr($temoselectiveitem[$ts],0,5)=='99999'){//2020/11/9 自訂備註
									if(strlen($tastelist)==0){
										$tastelist=substr($temoselectiveitem[$ts],7);
									}
									else{
										$tastelist=$tastelist.','.substr($temoselectiveitem[$ts],7);
									}
								}
								else if($temoselectiveitem[$ts]!=null){
									if(strlen($tastelist)==0){
										$tastelist=$taste[intval(substr($temoselectiveitem[$ts],0,5))]['name1'];
									}
									else{
										$tastelist=$tastelist.','.$taste[intval(substr($temoselectiveitem[$ts],0,5))]['name1'];
									}
									if(intval(substr($temoselectiveitem[$ts],5,1))==1){
									}
									else{
										$tastelist=$tastelist.'*'.intval(substr($temoselectiveitem[$ts],5,1));
									}
								}
							}
						}
						else if(substr($list[$i]['SELECTIVEITEM'.$t],0,5)=='99999'){//2020/11/9 自訂備註
							if(strlen($tastelist)==0){
								$tastelist=substr($list[$i]['SELECTIVEITEM'.$t],7);
							}
							else{
								$tastelist=$tastelist.','.substr($list[$i]['SELECTIVEITEM'.$t],7);
							}
						}
						else if($list[$i]['SELECTIVEITEM'.$t]!=null){
							if(strlen($tastelist)==0){
								$tastelist=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
							}
							else{
								$tastelist=$tastelist.','.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
							}
							if(intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1))==1){
							}
							else{
								$tastelist=$tastelist.'*'.intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
							}
						}
						else{
							break;
						}
					}
					echo '<br>&nbsp;'.$tastelist;
				}
				else{
				}
				?>
				</div>
				<div style='width:18%;padding:0 3px;float:left;text-align:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<?php
				echo $init['init']['frontunit'].$list[$i]['UNITPRICE'].$init['init']['unit'];
				?>
				</div>
				<div style='width:12%;padding:0 3px;float:left;text-align:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<?php
				echo $list[$i]['QTY'];
				?>
				</div>
				<div style='width:20%;padding:0 3px;float:left;text-align:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<?php
				echo $init['init']['frontunit'].(floatval($list[$i]['AMT'])+floatval($list[$i-1]['AMT'])).$init['init']['unit'];
				?>
				</div>
			</div>
		<?php
		}
		if(isset($listdis[0]['AMT'])){
		?>
			<div class='listitemlabel' style='width:100%;overflow:hidden;margin-top:8px;;float:left;border-bottom:1px #898989 solid;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<div style='width:50%;float:left;overflow:hidden;'>帳單優惠</div>
				<div style='width:18%;min-height: 1px;padding:0 3px;float:left;text-align:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'></div>
				<div style='width:12%;min-height: 1px;padding:0 3px;float:left;text-align:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'></div>
				<div style='width:20%;padding:0 3px;float:left;text-align:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<?php
				echo $init['init']['frontunit'].$listdis[0]['AMT'].$init['init']['unit'];
				?>
				</div>
			</div>
		<?php
		}
		else{
		}
	}
}
else{
	echo '<div style="height:102px;border-bottom: 3px solid #ffffff;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">'.$title['title']['text'].'</div>';
}
?>