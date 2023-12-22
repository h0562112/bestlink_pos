<?php
header('Cache-Control:no-cache');//2020/8/3 關閉cache，避免客顯得產品列表無法正常刷新
$title=parse_ini_file('./img/secview.ini',true);
$init=parse_ini_file('../database/initsetting.ini',true);
$setup=parse_ini_file('../database/setup.ini',true);
//2020/10/30 nodejs設定值
if(isset($setup['nodejsaddress']['nodejsip'])){
}
else{
	$setup['nodejsaddress']['nodejsip']='127.0.0.1';
}
if(isset($setup['nodejsaddress']['nodejsport'])){
}
else{
	$setup['nodejsaddress']['nodejsport']='3700';
}
?>
<!doctype html>
<html lang="en">
 <head>
	<meta charset="UTF-8">
	<title>點餐明細</title>
	<?php
	if(isset($init['init']['usenodejs'])&&$init['init']['usenodejs']=='1'&&file_exists('../nodejs/node_modules/socket.io-client/dist/socket.io.js')){//2020/10/30 0>>遵循舊有流程1>>套用nodejs流程
	?>
	<script src='../tool/jquery-1.12.4.js'></script>
	<script src='../nodejs/node_modules/socket.io-client/dist/socket.io.js'></script>
	<script>
		$(document).ready(function(){
			var socket = io.connect('http://<?php echo $setup["nodejsaddress"]["nodejsip"]; ?>:<?php echo $setup["nodejsaddress"]["nodejsport"]; ?>');
			//console.log(mydata['name']);
			socket.emit('join','secview<?php echo $_GET["machine"]; ?>');
			socket.on('joinsuccess',function(msg){
				//mydata['id']=id;
				console.log(msg);
			});
			socket.on('disconnect',function(){//2020/11/11 server離線
				console.log('server disconnect');
				socket.emit('join','secview<?php echo $_GET["machine"]; ?>');//重新登入
			});
			socket.on('secviewupdate',function(msg){
				console.log('get message "'+msg+'"');
				switch(msg){
					case 'clear'://清除客顯內容
						$('.toplabel').html('<?php echo $title["title"]["text"]; ?>');
						$('.listtitlelabel').remove();
						$('.listitemlabel').remove();
						break;
					case 'listitemupdate'://更新點餐明細
						/*if($('.toplabel').find('div').length>0){//僅需要更新下方點餐明細
						}
						else{//另外產生上方帳單資訊區塊
						}*/
						$.ajax({//重整右邊帳單資訊
							url:'./updateAreload.php',
							method:'get',
							async:false,
							cache:false,
							data:{'machine':'<?php echo $_GET["machine"]; ?>'},
							dataType:'html',
							success:function(d){
								//console.log(d);
								$('body').html(d);
							},
							error:function(e){
								//console.log(e);
							}
						});
						break;
					default:
						break;
				}
			});
		});
	</script>
	<?php
	}
	else{
	}
	?>
	<style>
	body {
		margin:0;
		padding:2px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		font-family: Consolas,Microsoft JhengHei,sans-serif;
		font-size:<?php if(isset($title['rightlist']['fontsize']))echo $title['rightlist']['fontsize'];else echo '20'; ?>px;
		color:#ffffff;
	}
	div {
		font-size:<?php if(isset($title['rightlist']['fontsize']))echo $title['rightlist']['fontsize'];else echo '20'; ?>px;
	}
	</style>
</head>
<body>
	<?php
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
		//$setup=parse_ini_file('../database/setup.ini',true);//2020/10/30 上方已先載入，這邊不用再次載入
		$taste=parse_ini_file('../database/'.$setup['basic']['company'].'-taste.ini',true);
		//$init=parse_ini_file('../database/initsetting.ini',true);//2020/10/30 上方已先載入，這邊不用再次載入
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
				echo "<div style='width:100%;max-height:50%;height:25%;line-height:24px;float:left;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><table><tr><td style='width:auto;'>統編/載具</td><td style='width:auto;'>";
			}
			else{
				echo "<div style='width:100%;max-height:50%;height:50%;line-height:47px;float:left;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><table><tr><td style='width:auto;'>統編/載具</td><td style='width:auto;'>";
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
</body>
</html>
