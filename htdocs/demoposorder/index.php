<!doctype html>
<html lang="en">
<head>
	<?php
	include_once '../tool/dbTool.inc.php';
	$content=parse_ini_file('../database/setup.ini',true);
	$machinedata=parse_ini_file('../database/machinedata.ini',true);
	if(file_exists('../database/posorder.ini')){
		$posorder=parse_ini_file('../database/posorder.ini',true);
	}
	else{
	}
	?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../tool/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="./lib/css/main.css">
	<script src="../tool/jquery-1.12.4.js"></script>
	<script src="../tool/ui/1.12.1/jquery-ui.js"></script>
	<script src="./lib/js/jquery.fly.min.js"></script>
	<script src="./lib/js/main.js"></script>
	<!-- <link rel="stylesheet" href="pager.css"> -->
	<title>EDM</title>
</head>
<body>
	<span class='timeout' style='display:none;'></span>
	<input type='hidden' class='company' value='<?php echo $content['basic']['company']; ?>'>
	<input type='hidden' class='dep' value='<?php echo $content['basic']['story']; ?>'>
	<input type='hidden' class='table' value='<?php if(isset($_GET['table']))echo $_GET['table']; ?>'>
	<!-- <div style='display:none;'>
		<a href="#" class="prev">&lt;&lt;Prev</a>
		<a href="#" class="next">Next&gt;&gt;</a>
	</div> -->
	<div class="logo">
		<?php if(file_exists('./img/logo.png'))echo '<img src="./img/logo.png" style="width:100%;height:100%;">';else echo $machinedata['basic']['story']; ?>
	</div>
	<div id='main'>
		<div class='content'>
		<?php
		//$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
		$conn=sqlconnect('../database','menu.db','','','','sqlite');
		$sql='SELECT * FROM itemsdata';
		$items=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
		$itemname=parse_ini_file('../database/'.$content['basic']['company'].'-menu.ini',true);
		if(sizeof($items)==0){
		}
		else{
			for($i=0;$i<sizeof($items);$i++){
				if($itemname[$items[$i]['inumber']]['state']=='1'){
					$mcounter=0;
					for($m=1;$m<=9;$m++){
						if($itemname[$items[$i]['inumber']]['money'.$m]>0){
							$mcounter++;
							$money=$itemname[$items[$i]['inumber']]['money'.$m];
							$mname=$itemname[$items[$i]['inumber']]['mname1'.$m];
						}
						else{
						}
						if($mcounter==2){
							break;
						}
						else{
						}
					}
					echo "<div class='food' id='number".$items[$i]['inumber']."'>
							<div class='fdimgbox'>";
							if(file_exists("./img/".$items[$i]['imgfile'].".png")){
								echo "<img class='foodimg' src='./img/".$items[$i]['imgfile'].".png?".date('YmdHis')."'>";
							}
							else{
								echo "<img class='foodimg' src='./img/emptyimg.png?".date('YmdHis')."'>";
							}
						echo "</div>
							<div class='foodtitle'>".$itemname[$items[$i]['inumber']]['name1']."</div>
							<input type='hidden' class='name' value='".$itemname[$items[$i]['inumber']]['name1']."'>
							<input type='hidden' class='inumber' value='".$items[$i]['inumber']."'>";
					echo "</div>";
				}
				else{
				}
			}
		}
		?>
		</div>
		<!-- <img class='leftimg' src='left.png'>
		<div class="pagers"></div>
		<img class='rightimg' src='right.png'> -->
		<div class='table'>
			<div id='label'>
				桌號
			</div>
			<div id='table'>
				<?php if(isset($_GET['tab']))echo $_GET['tab'];else $_GET['tab']=''; ?>
				<input type='hidden' id='tablenumber' value='<?php echo $_GET['tab']; ?>'>
			</div>
		</div>
		<div class="function">
			<div id="service" class="fun">
				<div class='funimgbox' style='background-color:<?php
				if(file_exists('./serviceitems/'.$_GET['tab'].'.ini')){
					echo 'rgb(230, 93, 93)';
				}
				else{
					echo 'transparent';
				}
				?>;'>
					<img id='img' src='./img/service.png'>
					<input type='hidden' class='type' value='<?php
				if(file_exists('./serviceitems/'.$_GET['tab'].'.ini')){
					echo 'wait';
				}
				else{
					echo 'idle';
				}
				?>'>
				</div>
				<div class='funtitle'><?php
				if(file_exists('./serviceitems/'.$_GET['tab'].'.ini')){
					echo '等待服務人員';
				}
				else{
					echo '服務鈴';
				}
				?></div>
			</div>
		<?php
		if(isset($posorder['init']['order'])&&$posorder['init']['order']=='1'){
		?>
			<div id="getlist" class="fun">
				<div class='funimgbox'>
					<div class='point'></div>
					<div class='temppoint'></div>
					<img class='funimg' src='getlist.png'>
				</div>
				<div class='funtitle'>紀錄/結帳</div>
			</div>
		<?php
		}
		else{
		}
		?>
		</div>
	</div>
	<div class='service' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;'>
		<div id='item'>
			<div id='title'>
				請問需要什麼服務項目？（可複選）
			</div>
			<div id='items'>
			<?php
			if(file_exists('../database/service.ini')){
				$service=parse_ini_file('../database/service.ini',true);
				if(isset($service['items'])){
					foreach($service['items'] as $i=>$v){
						echo '<button id="serviceitems">';
						echo '<div>'.$v.'</div>';
						echo '<input type="checkbox" name="sercheck" id="sercheck" value="'.$i.'">';
						echo '</button>';
					}
				}
				else{
				}
			}
			else{
				for($i=0;$i<10;$i++){
					echo '<div class="serviceitems"></div>';
				}
			}
			?>
			</div>
		</div>
		<div id='fun'>
			<button class='send' disabled><div>送出需求</div></button>
			<button class='return'><div>返回</div></button>
		</div>
	</div>
	<div class='checkcancelservice' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;'>
		<div style='width:100%;height:calc(200% / 3);text-align:center;font-size:40px;display:table;'>
			<div style='display:table-cell;vertical-align: middle;'>請問要取消提出的服務嗎?</div>
		</div>
		<div style='width:100%;height:calc(100% / 3);text-align:center;'>
			<button id='send'><div>確認</div></button>
			<button id='cancel'><div>取消</div></button>
		</div>
	</div>
	<div class='chose' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;'>
		<div id='imgbox'>
			<img id='img' src=''>
		</div>
		<div id='name'></div>
		<div id='introductionbox'>
		</div>
		<button class='return'><div>返回</div></button>
	<?php
	if(isset($posorder['init']['order'])&&$posorder['init']['order']=='1'){
	?>
		<button class='send'><div>送出</div></button>
		<button class='taste'><div>加料/備註</div></button>
		<div id='selectbox'>
			<div style='width:100%;height:calc(100% - 74px);float:left;text-align:center;font-size:45px;'>規格：</div>
			<select name='mname'>
			</select>
		</div>
		<div id='inputbox'>
			<div style='width:100%;height:calc(100% - 74px);float:left;text-align:center;font-size:45px;'>數量：</div>
			<img class='diffbun' src='./img/diff.png'>
			<input type='number' name='number' value='1' readonly>
			<img class='plusbun' src='./img/plus.png'>
		</div>
	<?php
	}
	else{
	}
	?>
		<!-- <table style='width:100%;height:100%;'>
			<tr>
				<td rowspan='2' style='width:50%;height:100%;'>
					<div style='width:100%;height:100%;'>
						<div class='cycle-slideshow' id='moncontent' style='width:100%;height:100%;' data-cycle-fx='scrollHorz' data-cycle-timeout='0' data-cycle-speed='800' data-cycle-slides="> div[class='money']" data-cycle-next=".more">
							<div class='money' style='width:100%;height:75%;'>
								<input type='button' id='1' class='mbutton' value='' style='width:100%;height:30%;margin-bottom:1%;' disabled>
								<input type='hidden' class='mname1' value=''>
								<input type='hidden' class='mvalue1' value=''>
								<input type='button' id='2' class='mbutton' value='' style='width:100%;height:30%;margin-bottom:1%;' disabled>
								<input type='hidden' class='mname2' value=''>
								<input type='hidden' class='mvalue2' value=''>
								<input type='button' id='3' class='mbutton' value='' style='width:100%;height:30%;margin-bottom:1%;' disabled>
								<input type='hidden' class='mname3' value=''>
								<input type='hidden' class='mvalue3' value=''>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td style='width:50%;height:50%;'><div id='introduction' style='width:100%;height:100%;'></div></td>
			</tr>
		</table> -->
	</div>
	<div class='order'>
		<table style='width:100%;height:100%;'>
			<tr>
				<td style='width:50%;height:66%;'><img id='orimg' style='width:100%;' src=''><div id='orname' style='font-size:30px;text-align:center;'></div></td>
				<td style='width:50%;height:66%;'><div id='ortaste' style='width:480px;height:300px;border:1px #000000 solid;'></div></td>
			</tr>
			<tr>
				<td colspan='2' style='width:100%;height:33%;'><div id='ormoney' style='width:calc(30% - 60px);height:64px;padding-top:15px;float:left;text-align:right;padding-right:60px;font-size:30px;'></div><div style='width:70%;float:left;'><img src='ordermls.png' id='ordiff' style='height:50%;vertical-align:middle;'><input type='text' class='ornumber' style='width:30%;height:50%;text-align:right;font-size:30px;margin:0 60px;vertical-align:middle;' value='0' readonly><img src='plus.png' id='orplus' style='height:50%;vertical-align:middle;'></div></td>
			</tr>
		</table>
	</div>
	<div class='ordernumber'>
		<table style='width:100%;height:100%;'>
			<tr>
				<td colspan='4'><input type='text' id='viewnumber' style='width:calc(100% - 4px);height:100%;text-align:right;font-size:40px;' value='0' readonly></td>
			</tr>
			<tr>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='7'></td>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='8'></td>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='9'></td>
				<td><input type='button' style='width:100%;height:100%;' value='' disabled></td>
			</tr>
			<tr>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='4'></td>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='5'></td>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='6'></td>
				<td><input type='button' id='cancel' style='width:100%;height:100%;' value='取消'></td>
			</tr>
			<tr>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='1'></td>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='2'></td>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='3'></td>
				<td><input type='button' id='backspace' style='width:100%;height:100%;' value='倒退'></td>
			</tr>
			<tr>
				<td><input type='button' style='width:100%;height:100%;' value='' disabled></td>
				<td><input type='button' class='numbutton' style='width:100%;height:100%;' value='0'></td>
				<td><input type='button' style='width:100%;height:100%;' value='' disabled></td>
				<td><input type='button' id='submit' style='width:100%;height:100%;' value='送出'></td>
			</tr>
		</table>
	</div>
	<?php
	if(isset($posorder['init']['order'])&&$posorder['init']['order']=='1'){
	?>
	<div class='list' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='點餐紀錄'>
		<div class='listcontent'></div>
		<div class='total'><span id='tolbox'>總共<span class='tmoy'>0</span>元</span></div>
	</div>
	<!-- <div class='finish' title='系統訊息'>
		您要內用？外帶？
	</div> -->
	<div class='keybord' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='數量'>
		<input type='hidden' name='type' value='bord'>
		<input type='text' name='num' max='999' style='width:100%;height:50px;font-size:25px;margin-bottom:1px;padding:0 5px;background-color:#ffffff;text-align:right;'>
		<button id='clear' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>清空</button>
		<button id='back' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>倒退</button>
		<button style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'></button>
		<button id='number' value='7' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>7</button>
		<button id='number' value='8' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>8</button>
		<button id='number' value='9' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>9</button>
		<button id='number' value='4' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>4</button>
		<button id='number' value='5' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>5</button>
		<button id='number' value='6' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>6</button>
		<button id='number' value='1' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>1</button>
		<button id='number' value='2' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>2</button>
		<button id='number' value='3' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>3</button>
		<button id='number' value='0' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>0</button>
		<button id='add' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>+</button>
		<button id='diff' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>-</button>
		<button id='send' style='width:calc(200% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>送出</button>
		<button id='cancel' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>取消</button>
	</div>
	<?php
	}
	else{
	}
	?>
</body>
</html>
