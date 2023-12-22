<!doctype html>
<html lang="en">
<head>
	<?php
	include_once './lib/dbTool.inc.php';
	$init=parse_ini_file('../database/initsetting.ini',true);
	$content=parse_ini_file('../database/setup.ini',true);
	$time=parse_ini_file('../database/time.ini',true);
	$orinit=parse_ini_file('../database/orderinitset.ini',true);
	?>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="./lib/css/jquery-ui.css">
	<link rel="stylesheet" href="./main.css">
	<script src='./lib/js/jquery-1.12.4.js'></script>
	<script src="./lib/js/jquery-ui.js"></script>
	<script src='./lib/js/jquery.cycle2.js'></script>
	<script src='./main.js'></script>
	<title>點餐畫面</title>
</head>
<body>
	<input type='hidden' name='basiccompany' value='<?php echo $content["basic"]["company"]; ?>'>
	<input type='hidden' name='basicdep' value='<?php echo $content["basic"]["story"]; ?>'>
	<input type='hidden' id='frontunit' value='<?php echo $init['init']['frontunit']; ?>'>
	<input type='hidden' id='unit' value='<?php echo $init['init']['unit']; ?>'>
	<span class='timeout' style='display:none;'></span>
	<div style='display:none;'>
		<a href="#" class="listprev">&lt;&lt;Prev</a>
		<a href="#" class="listnext">Next&gt;&gt;</a>
	</div>
	<div class='print'>
		<div style='text-align:center;margin:calc(700 / 1920 * 1080px) 0 0 0;'>
			<img src='../database/img/loading.gif'>
			<div style='margin:calc(15 / 1920 * 1080px) 0 0 0;'>
				出單中，請稍候<br>
				感謝您今日的蒞臨，期待下次再為您服務
			</div>
		</div>
	</div>
	<div class='chose' style='overflow:hidden;'>
		<img id='img'>
		<div id='name' style='font-weight:bold;text-align:center;float:right;'></div><input type='hidden' name='inumber'>
		<div style='width:50%;height:calc(100% - 46px);float:right;'>
			<div class='money' style='height:calc(100% - 75px)'>
			</div>
			<div style='width:100%;text-align:center;'>
				<input type='button' class='cancel' style='width:calc(205 / 1080 * 800px);height:calc(100 / 1920 * 1080px);background-image:url("../database/img/cancel.png");background-size:100% 100%;'>
			</div>
		</div>
		<div id='introduction' style='width:calc(50% - 15px);height:calc(100% - 50vw);padding:calc(10 / 1920 * 1080px) calc(5 / 1080 * 800px) 0 calc(10 / 1080 * 800px);color:#3e3a39;font-size:calc(25 / 1080 * 800px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow-y:auto;'>
			<table style='max-width:100%;'>
				<tr id='color1'>
					<td id='introtitle1'></td>
					<td id='introduction1'></td>
				</tr>
				<tr id='color2'>
					<td id='introtitle2'></td>
					<td id='introduction2'></td>
				</tr>
				<tr id='color3'>
					<td id='introtitle3'></td>
					<td id='introduction3'></td>
				</tr>
				<tr id='color4'>
					<td id='introtitle4'></td>
					<td id='introduction4'></td>
				</tr>
				<tr id='color5'>
					<td id='introtitle5'></td>
					<td id='introduction5'></td>
				</tr>
				<tr id='color6'>
					<td id='introtitle6'></td>
					<td id='introduction6'></td>
				</tr>
			</table>
		</div>
	</div>
	<div class='order' style='overflow:hidden;'>
		<img id='orimg'>
		<div id='orname' style='width:50vw;height:40px;overflow:hidden;margin:5px 0;font-weight:bold;text-align:center;float:left;'></div>
		<div id='ormname' style='width:50vw;height:40px;overflow:hidden;margin:5px 0;font-weight:bold;text-align:right;float:left;padding-right:2vw;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;'></div>
		<div id='con' style='width:calc(50% - 10px);height:calc(50vw - 100px);float:left;font-size:calc(25 / 1080 * 800px);overflow-y:auto;margin-left:calc(10 / 1080 * 800px);'></div>
		<div class='funbox' style='margin-top:calc(30 / 1080 * 100vh);height:calc(100vh - 50vw - (100 / 1920 * 1080px) - (60 / 1080 * 100vh) - 2vh);float:left;overflow:hidden;'>
			<button id='taste' style='width:20vw;height:10vh;float:left;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>加料</div></button>
			<button id='dis' style='width:20vw;height:10vh;float:left;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>促銷</div></button>
		</div>
		<div id='disbox' style='width:100%;margin-top:calc(30 / 1080 * 100vh);float:left;display:none;'>
			<div id='keybord' style='width:65vw;height:calc(100vh - 50vw - (100 / 1920 * 1080px) - (60 / 1080 * 100vh) - 2vh);float:left;'>
				<button id='free' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>全部<br>招待</div></button>
				<button id='dis1' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>折讓</div></button>
				<button id='dis2' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>折扣</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='7'><div>7</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='8'><div>8</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='9'><div>9</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='4'><div>4</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='5'><div>5</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='6'><div>6</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='1'><div>1</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='2'><div>2</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='3'><div>3</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='0'><div>0</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='.'><div>.</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='AC'><div>AC</div></button>
			</div>
			<div id='view' style='width:35vw;height:calc(100vh - 50vw - (100 / 1920 * 1080px) - (60 / 1080 * 100vh) - 2vh);float:left;'>
				<table style='width:100%;font-size:4vw;'>
					<tr>
						<td>輸入顯示</td>
					</tr>
					<tr>
						<td><input type='text' id='inputbox' style='width:98%;text-align:right;' readonly></td>
					</tr>
					<tr>
						<td>原價</td>
					</tr>
					<tr>
						<td><input type='text' id='initmoney' style='width:98%;text-align:right;' readonly></td>
					</tr>
					<tr>
						<td>扣抵金額</td>
					</tr>
					<tr>
						<td><input type='text' id='dismoney' style='width:98%;text-align:right;' name='dis' readonly></td>
					</tr>
					<tr>
						<td>小計</td>
					</tr>
					<tr>
						<td><input type='text' id='money' style='width:98%;text-align:right;' name='money' readonly></td>
					</tr>
					<tr>
						<td><button id='return' style='width:98%;float:left;padding:25px 0;margin:48px 1px 1px 1px;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>取消折扣</div></button></td>
					</tr>
				</table>
			</div>
		</div>
		<div id='ortastebox' style='width:100%;margin-top:calc(50 / 1920 * 1080px);float:left;display:none;'>
			<div id='ortaste'>
			</div>
		</div>
		<div id='buttons' style='width:calc(880 / 1080 * 800px);color:#3e3a39;font-size:calc(25 / 1080 *800px);float:left;margin-top:calc(30 / 1080 * 100vh);'>
			<!-- <button class='orfun' id='taste' style='width:205px;height:100px;margin:0;border:0;float:left;background-image:url("../database/img/buttonborder.png");background-size: 100% 100%;color:#898989;'><div style='width:100%;height:60%;font-size:52px;'>加料</div><div style='width:100%;height:40%;font-size:26px;'>Feeding</div></button> -->
			<div style='width:calc(100% - calc(410 / 1080 * 800px));height:calc(93 / 1920 * 1080px);text-align:center;float:left;'>
				<img src='../database/img/plus.png' id='orplus' style='width:calc(70 / 1080 * 800px);height:calc(70 / 1920 * 1080px);margin:calc(15 / 1920 * 1080px) calc(20 / 1080 * 800px);vertical-align:middle;float:left;'>
				<img src='../database/img/diff.png' id='ordiff' style='width:calc(70 / 1080 * 800px);height:calc(70 / 1920 * 1080px);margin:calc(15 / 1920 * 1080px) calc(20 / 1080 * 800px);vertical-align:middle;float:left;'>
				<div style='height:calc(100 / 1920 * 1080px);line-height:calc(100 / 1920 * 1080px);font-size:calc(50 / 1080 * 800px);color:#3e3a39;float:left;'><strong><span class='orviewnum' style='border-bottom:1px solid #000000;'>1</span></strong></div><input type='number' class='ornumber' style='display:none;' value='1'>
			</div>
			<button class='orfun' id='cancel' style='width:calc(205 / 1080 * 800px);height:calc(100 / 1920 * 1080px);margin:0;border:0;float:left;background-image:url("../database/img/buttonborder.png");background-size: 100% 100%;color:#898989;'><div style='width:100%;height:60%;font-size:calc(52 / 1080 * 800px);'>取消</div><div style='width:100%;height:40%;font-size:calc(26 / 1080 * 800px);'>Cancel</div></button>
			<button class='orfun' id='sub' style='width:calc(205 / 1080 * 800px);height:calc(100 / 1920 * 1080px);margin:0;border:0;float:left;background-image:url("../database/img/buttonborder.png");background-size: 100% 100%;color:#898989;'><div style='width:100%;height:60%;font-size:calc(52 / 1080 * 800px);'>送出</div><div style='width:100%;height:40%;font-size:calc(26 / 1080 * 800px);'>Submit</div></button>
		</div>
	</div>
	<div class='chosetype'>
		<div class='div' id='closetype' style='width:calc(980 / 1080 * 800px);height:calc(110 / 1920 * 1080px);padding-left:calc(100 / 1080 * 800px);border-bottom:1px solid #595757;'><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);float:left;' src='../database/img/type_icon.png'><div style='width:calc(100% - (110 / 1080 * 800px));height:60%;line-height:calc(95 / 1920 * 1080px);float:left;font-size:calc(58 / 1080 * 800px);font-weight:bold;color:#595757;'>類別</div><div style='width:calc(100% - (110 / 1080 * 800px));height:40%;line-height:calc(44 / 1920 * 1080px);float:left;font-size:calc(26 / 1080 * 800px);font-weight:bold;color:#898989;'>Category</div></div>
		<div class='div' style='width:calc(980 / 1080 * 800px);height:calc(110 / 1920 * 1080px);padding-left:calc(100 / 1080 * 800px);border-bottom:1px solid #595757;'><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);float:left;' src='../database/img/type_icon1.png'><div style='width:calc(100% - ((110 + 110 + 100)/ 1080 * 800px));height:60%;line-height:calc(95 / 1920 * 1080px);float:left;font-size:calc(58 / 1080 * 800px);font-weight:bold;color:#595757;'>便當</div><div style='width:calc(100% - ((110 + 110 + 100)/ 1080 * 800px));height:40%;line-height:calc(44 / 1920 * 1080px);float:left;font-size:calc(26 / 1080 * 800px);font-weight:bold;color:#898989;'>Lunch Box</div><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);' src='../database/img/type_icon_right.png'></div>
		<div class='div' style='width:calc(980 / 1080 * 800px);height:calc(110 / 1920 * 1080px);padding-left:calc(100 / 1080 * 800px);border-bottom:1px solid #595757;'><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);float:left;' src='../database/img/type_icon2.png'><div style='width:calc(100% - ((110 + 110 + 100)/ 1080 * 800px));height:60%;line-height:calc(95 / 1920 * 1080px);float:left;font-size:calc(58 / 1080 * 800px);font-weight:bold;color:#595757;'>單點</div><div style='width:calc(100% - ((110 + 110 + 100) / 1080 * 800px));height:40%;line-height:calc(44 / 1920 * 1080px);float:left;font-size:calc(26 / 1080 * 800px);font-weight:bold;color:#898989;'>A la carte</div><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);' src='../database/img/type_icon_right.png'></div>
		<div class='div' style='width:calc(980 / 1080 * 800px);height:calc(110 / 1920 * 1080px);padding-left:calc(100 / 1080 * 800px);border-bottom:1px solid #595757;'><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);float:left;' src='../database/img/type_icon3.png'><div style='width:calc(100% - ((110 + 110 + 100)/ 1080 * 800px));height:60%;line-height:calc(95 / 1920 * 1080px);float:left;font-size:calc(58 / 1080 * 800px);font-weight:bold;color:#595757;'>飲品</div><div style='width:calc(100% - ((110 + 110 + 100) / 1080 * 800px));height:40%;line-height:calc(44 / 1920 * 1080px);float:left;font-size:calc(26 / 1080 * 800px);font-weight:bold;color:#898989;'>Drinks</div><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);' src='../database/img/type_icon_right.png'></div>
		<div class='div' style='width:calc(980 / 1080 * 800px);height:calc(110 / 1920 * 1080px);padding-left:calc(100 / 1080 * 800px);border-bottom:1px solid #595757;'><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);float:left;' src='../database/img/type_icon4.png'><div style='width:calc(100% - ((110 + 110 + 100)/ 1080 * 800px));height:60%;line-height:calc(95 / 1920 * 1080px);float:left;font-size:calc(58 / 1080 * 800px);font-weight:bold;color:#595757;'>湯品</div><div style='width:calc(100% - ((110 + 110 + 100) / 1080 * 800px));height:40%;line-height:calc(44 / 1920 * 1080px);float:left;font-size:calc(26 / 1080 * 800px);font-weight:bold;color:#898989;'>Soup</div><img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);' src='../database/img/type_icon_right.png'></div>
	</div>
	<div class='editbox' style=''>
		<div style='width:100%;font-size:calc(80 / 1080 * 800px);'><center>修改明細</center></div>
		<input type='hidden' name='index'>
		<img id='orimg'>
		<div id='orname' style='width:50vw;height:40px;overflow:hidden;margin:5px 0;font-weight:bold;text-align:center;float:left;'></div>
		<div id='ormname' style='width:50vw;height:40px;overflow:hidden;margin:5px 0;font-weight:bold;text-align:right;float:left;padding-right:2vw;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;'></div>
		<div id='con' style='width:calc(50% - 10px);height:calc(50vw - 179px);float:left;font-size:calc(30 / 1080 * 800px);overflow-y:auto;margin-left:calc(10 / 1080 * 800px);'>
			<table style='max-width:100%;'>
				<tr id='color1'>
					<td id='introtitle1'></td>
					<td id='introduction1'></td>
				</tr>
				<tr id='color2'>
					<td id='introtitle2'></td>
					<td id='introduction2'></td>
				</tr>
				<tr id='color3'>
					<td id='introtitle3'></td>
					<td id='introduction3'></td>
				</tr>
				<tr id='color4'>
					<td id='introtitle4'></td>
					<td id='introduction4'></td>
				</tr>
				<tr id='color5'>
					<td id='introtitle5'></td>
					<td id='introduction5'></td>
				</tr>
				<tr id='color6'>
					<td id='introtitle6'></td>
					<td id='introduction6'></td>
				</tr>
			</table>
		</div>
		<div class='funbox' style='margin-top:calc(30 / 1080 * 100vh);height:calc(100vh - 50vw - (100 / 1920 * 1080px) - (60 / 1080 * 100vh) - 2vh);float:left;overflow:hidden;'>
			<button id='taste' style='width:20vw;height:10vh;float:left;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>加料</div></button>
			<button id='dis' style='width:20vw;height:10vh;float:left;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>促銷</div></button>
		</div>
		<div id='disbox' style='width:100%;margin-top:calc(30 / 1080 * 100vh);float:left;display:none;'>
			<div id='keybord' style='width:65vw;height:calc(100vh - 50vw - (100 / 1920 * 1080px) - (60 / 1080 * 100vh) - 2vh);float:left;'>
				<button id='free' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>全部<br>招待</div></button>
				<button id='dis1' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>折讓</div></button>
				<button id='dis2' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>折扣</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='7'><div>7</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='8'><div>8</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='9'><div>9</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='4'><div>4</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='5'><div>5</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='6'><div>6</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='1'><div>1</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='2'><div>2</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='3'><div>3</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='0'><div>0</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='.'><div>.</div></button>
				<button style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);float:left;margin:1px;border:3px solid #898989;border-radius: 10px;color:#898989;' value='AC'><div>AC</div></button>
			</div>
			<div id='view' style='width:35vw;height:calc(100vh - 50vw - (100 / 1920 * 1080px) - (60 / 1080 * 100vh) - 2vh);float:left;'>
				<table style='width:100%;font-size:4vw;'>
					<tr>
						<td>輸入顯示</td>
					</tr>
					<tr>
						<td><input type='text' id='inputbox' style='width:98%;text-align:right;' readonly></td>
					</tr>
					<tr>
						<td>原價</td>
					</tr>
					<tr>
						<td><input type='text' id='initmoney' style='width:98%;text-align:right;' readonly></td>
					</tr>
					<tr>
						<td>扣抵金額</td>
					</tr>
					<tr>
						<td><input type='text' id='dismoney' style='width:98%;text-align:right;' name='dis' readonly></td>
					</tr>
					<tr>
						<td>小計</td>
					</tr>
					<tr>
						<td><input type='text' id='money' style='width:98%;text-align:right;' name='money' readonly></td>
					</tr>
					<tr>
						<td><button id='return' style='width:98%;float:left;padding:25px 0;margin:48px 1px 1px 1px;border:3px solid #898989;border-radius: 10px;color:#898989;'><div>取消折扣</div></button></td>
					</tr>
				</table>
			</div>
		</div>
		<div id='ortastebox' style='width:100%;margin-top:calc(50 / 1920 * 1080px);float:left;display:none;'>
			<div id='ortaste'>
			</div>
		</div>
		<div id='buttons' style='width:calc(880 / 1080 * 800px);color:#3e3a39;font-size:calc(25 / 1080 *800px);float:left;margin-top:calc(30 / 1080 * 100vh);'>
			<!-- <button class='orfun' id='taste' style='width:205px;height:100px;margin:0;border:0;float:left;background-image:url("../database/img/buttonborder.png");background-size: 100% 100%;color:#898989;'><div style='width:100%;height:60%;font-size:52px;'>加料</div><div style='width:100%;height:40%;font-size:26px;'>Feeding</div></button> -->
			<div style='width:calc(100% - calc(410 / 1080 * 800px));height:calc(93 / 1920 * 1080px);text-align:center;float:left;'>
				<img src='../database/img/plus.png' id='orplus' style='width:calc(70 / 1080 * 800px);height:calc(70 / 1920 * 1080px);margin:calc(15 / 1920 * 1080px) calc(20 / 1080 * 800px);vertical-align:middle;float:left;'>
				<img src='../database/img/diff.png' id='ordiff' style='width:calc(70 / 1080 * 800px);height:calc(70 / 1920 * 1080px);margin:calc(15 / 1920 * 1080px) calc(20 / 1080 * 800px);vertical-align:middle;float:left;'>
				<div style='height:calc(100 / 1920 * 1080px);line-height:calc(100 / 1920 * 1080px);font-size:calc(50 / 1080 * 800px);color:#3e3a39;float:left;'><strong><span class='orviewnum' style='border-bottom:1px solid #000000;'>1</span></strong></div><input type='number' class='ornumber' style='display:none;' value='1'>
			</div>
			<button class='orfun' id='cancel' style='width:calc(205 / 1080 * 800px);height:calc(100 / 1920 * 1080px);margin:0;border:0;float:left;background-image:url("../database/img/buttonborder.png");background-size: 100% 100%;color:#898989;'><div style='width:100%;height:60%;font-size:calc(52 / 1080 * 800px);'>取消</div><div style='width:100%;height:40%;font-size:calc(26 / 1080 * 800px);'>Cancel</div></button>
			<button class='orfun' id='sub' style='width:calc(205 / 1080 * 800px);height:calc(100 / 1920 * 1080px);margin:0;border:0;float:left;background-image:url("../database/img/buttonborder.png");background-size: 100% 100%;color:#898989;'><div style='width:100%;height:60%;font-size:calc(52 / 1080 * 800px);'>送出</div><div style='width:100%;height:40%;font-size:calc(26 / 1080 * 800px);'>Submit</div></button>
		</div>
	</div>
	<div class='result' style='padding:calc(100 / 1920 * 1080px) calc(100 / 1080 * 800px);'>
		<form method='post' action='create.list.php' class='resultform' style='width:100%;height:calc(100% - (141 / 1920 * 1080px));margin:0;padding:0;'>
			<?php
			if(isset($_GET['userid'])){
				echo "<input type='hidden' name='userid' value='".$_GET['userid']."'>";
			}
			else{
				echo "<input type='hidden' name='userid' value=''>";
			}
			?>
			<div class='rescontent' style='width:calc(100% - (50 / 1080 * 800px));height:100%;'>
				<div class='listbox' style='width:100%;height:calc(100% - (203 / 1920 * 1080px));'>
					<div style='width:100%;height:calc(107 / 1920 * 1080px);'>
						<div style='width:40%;height:100%;float:left;background-color:#898989;'>
							<div style='font-size:calc(55 / 1080 * 100vw);font-weight:bold;text-align:center;color:#ffffff;'>產品</div>
							<div style='font-size:calc(30 / 1080 * 100vw);font-weight:bold;text-align:center;color:#ffffff;'>Product</div>
						</div>
						<div style='width:20%;height:100%;font-weight:bold;text-align:center;float:left;background-color:#898989;color:#ffffff;'>
							<div style='font-size:calc(55 / 1080 * 100vw);font-weight:bold;text-align:center;color:#ffffff;'>份數</div>
							<div style='font-size:calc(30 / 1080 * 100vw);font-weight:bold;text-align:center;color:#ffffff;'>Quantity</div>
						</div>
						<div style='width:20%;height:100%;font-weight:bold;text-align:center;float:left;background-color:#898989;color:#ffffff;'>
							<div style='font-size:calc(55 / 1080 * 100vw);font-weight:bold;text-align:center;color:#ffffff;'>單價</div>
							<div style='font-size:calc(30 / 1080 * 100vw);font-weight:bold;text-align:center;color:#ffffff;'>Price</div>
						</div>
						<div style='width:20%;height:100%;font-weight:bold;text-align:center;float:left;background-color:#898989;color:#ffffff;'>
							<div style='font-size:calc(55 / 1080 * 100vw);font-weight:bold;text-align:center;color:#ffffff;'>小計</div>
							<div style='font-size:calc(30 / 1080 * 100vw);font-weight:bold;text-align:center;color:#ffffff;'>Total</div>
						</div>
					</div>
					<div id='list' style='width:100%;height:calc(100% - calc(107 / 1920 * 1080px));overflow-x:hidden;overflow-y:auto;'>
						<div style='width:100%;float:left;'>
						<table  id='div' style='width:100%;border-collapse:collapse;'>
							<tr id='itemlist' style='width:100%; min-height:calc((1267 / 8 - 4) / 1920 * 1080px);'>
								<td id='name' style='height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='number' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='price' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='money' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
							</tr>
							<tr id='itemlist' style='width:100%; min-height:calc((1267 / 8 - 4) / 1920 * 1080px);'>
								<td id='name' style='height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='number' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='price' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='money' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
							</tr>
							<tr id='itemlist' style='width:100%; min-height:calc((1267 / 8 - 4) / 1920 * 1080px);'>
								<td id='name' style='height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='number' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='price' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='money' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
							</tr>
							<tr id='itemlist' style='width:100%; min-height:calc((1267 / 8 - 4) / 1920 * 1080px);'>
								<td id='name' style='height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='number' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='price' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='money' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
							</tr>
							<tr id='itemlist' style='width:100%; min-height:calc((1267 / 8 - 4) / 1920 * 1080px);'>
								<td id='name' style='height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='number' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='price' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='money' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
							</tr>
							<tr id='itemlist' style='width:100%; min-height:calc((1267 / 8 - 4) / 1920 * 1080px);'>
								<td id='name' style='height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='number' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='price' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='money' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
							</tr>
							<tr id='itemlist' style='width:100%; min-height:calc((1267 / 8 - 4) / 1920 * 1080px);'>
								<td id='name' style='height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='number' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='price' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='money' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
							</tr>
							<tr id='itemlist' style='width:100%; min-height:calc((1267 / 8 - 4) / 1920 * 1080px);'>
								<td id='name' style='height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='number' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='price' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
								<td id='money' style='width:20%;height:calc((1267 / 8 - 2) / 1920 * 1080px);'></td>
							</tr>
						</table>
						</div>
						<!-- <div id='emptydiv' style='width:100%;height:100%;min-height:50px;floar:left;'>
							<div style='width:40%;height:100%;float:left;'>
							</div>
							<div style='width:20%;height:100%;float:left;'>
							</div>
							<div style='width:20%;height:100%;float:left;'>
							</div>
							<div style='width:20%;height:100%;float:left;'>
							</div>
						</div> -->
					</div>
				</div>
				<div class='totalbox' style='width:100%;height:calc(200 / 1920 * 1080px);'>
					<div style='width:50%;height:100%;float:left;'>
					<?php 
					if(isset($_GET['type'])){
						if($_GET['type']=='out'){
							echo '<div style="width:calc(100 / 1080 * 800px);margin-top:calc(100 / 1920 * 1080px);text-align:center;font-size:calc(50 / 1080 * 800px);font-weight:bold;">外帶</div><div style="width:calc(100 / 1080 * 800px);text-align:center;font-size:calc(25 / 1080 * 800px);font-weight:bold;">To go</div>';
						}
						else {
							echo '<div style="width:calc(100 / 1080 * 800px);margin-top:calc(100 / 1920 * 1080px);text-align:center;font-size:calc(50 / 1080 * 800px);font-weight:bold;">內用</div><div style="width:calc(100 / 1080 * 800px);text-align:center;font-size:calc(25 / 1080 * 800px);font-weight:bold;">Dine in</div>';
						}
					}
					else if(isset($_GET['memno'])){
						echo '<div style="width:100%;padding-top:calc(13 / 1920 * 1080px);text-align:left;font-size:calc(50 / 1080 * 800px);font-weight:bold;color:#898989;"><input type="hidden" name="memno" value=""></div>';
					}
					else if(isset($_GET['number'])){
						echo '<div style="width:calc(300 / 1080 * 800px);margin-top:calc(100 / 1920 * 1080px);text-align:center;font-size:calc(50 / 1080 * 800px);font-weight:bold;">內用 '.$_GET['number'].'<input type="hidden" name="tablenumber" value="'.$_GET['number'].'"> 號桌</div>';
					}
					else{
						echo '<div class="membox" style="width:100%;padding-top:calc(13 / 1920 * 1080px);text-align:left;font-size:calc(50 / 1080 * 800px);font-weight:bold;color:#898989;"></div>';
					}
					?></div>
					<div id='timediv' style='width:50%;height:50%;line-height:calc(100 / 1920 * 1080px);font-size:calc(50 / 1080 * 800px);font-weight:bold;text-align:left;float:left;'>
						產品總數量<span id='time' style='padding:0 calc(10 / 1080 * 800px);border-bottom:1px solid #000000;'>0</span>
					</div>
					<div id='moneydiv' style='width:calc(50% - 10px);height:50%;line-height:calc(100 / 1920 * 1080px);padding-right:calc(10 / 1080 * 800px);font-size:calc(50 / 1080 * 800px);font-weight:bold;text-align:right;float:left;background-image:url("../database/img/totalmoney.png");background-repeat: no-repeat;background-size: 100% 100%;'>
						<?php echo $init['init']['frontunit']; ?><span id='money'>0</span><?php echo $init['init']['unit']; ?><input type='hidden' id='total' name='total' value=''>
					</div>
				</div>
			</div>
		</form>
		<div id='buttons' style='width:100%;height:calc(116 / 1920 * 1080px);padding:calc(25 / 1920 * 1080px) 0 0 0;color:#3e3a39;font-size:calc(25 / 1080 * 800px);float:left;'><button class='orfun' id='reset' style='width:calc(340 / 1080 * 800px);height:calc(105 / 1920 * 1080px);vertical-align:middle;background-image:url("../database/img/longbuttonborder.png");background-size:100% 100%;'><div style='font-size:calc(52 / 1080 * 800px);color:#909090;'>返回</div><div style='font-size:calc(25 / 1080 * 800px);color:#A6A6A6;'>Return</div></button><input type='button' class='orfun' id='sub' style='width:calc(210 / 1080 * 800px);height:calc(110 / 1920 * 1080px);margin-left:calc(20 / 1080 * 800px);float:right;background-image:url("../database/img/submit.png");background-size:100% 100%;'><input type='button' class='orfun' id='cancel' style='width:calc(210 / 1080 * 800px);height:calc(110 / 1920 * 1080px);float:right;background-image:url("../database/img/home.png");background-size:100% 100%;'>
		<!-- <input type='button' class='orfun' id='cancel' value='取消' style='margin:21.5px 0 0 0;vertical-align:middle;'>
		<input type='button' class='orfun' id='sub' value='送出' style='margin:21.5px 6px 0 100px;vertical-align:middle;'> -->
		</div>
	</div>
	<div class='sysmeg' style='background-image:url("../database/img/sysmeg.png")'>
		<div style='width:100%;height:50%;line-height:calc(160.5 / 1920 * 1080px);font-size:clac(55 / 1080 * 800px);font-weight:bold;text-align:center;'>
			是否取消點餐？
		</div>
		<div style='width:100%;height:50%;text-align:center;'>
			<input type='button' class='mbutton' id='yes' value='是' style='margin:0 calc(147 / 1080 * 800px) 0 0;'><input type='button' class='mbutton' id='no' value='否'>
		</div>
	</div>
	<div class='bill'>
		<div style='width:calc(685 / 1080 * 800px);margin:calc(650 / 1920 * 1080px) calc(196.5 / 1080 * 800px) 0 calc(196.5 / 1080 * 800px);'>
			<button class='mbutton' id='print' style='width:calc(665 / 1080 * 800px);height:calc(165 / 1920 * 1080px);background-image:url("../database/img/billfun1.png");background-size:100% 100%;margin:0 0 calc(15 / 1920 * 1080px) 0;'></button>
			<button class='mbutton' id='return' style='width:calc(665 / 1080 * 800px);height:calc(165 / 1920 * 1080px);background-image:url("../database/img/billfun2.png");background-size:100% 100%;'></button>
			<div id='note'>
				<ul>
					<li>請持點餐明細至櫃檯任選三樣菜(不選菜除外)</li>
					<li>鈔票、便當請當面點清，離櫃恕不負責</li>
					<li>主菜離櫃，恕不更換</li>
					<li>白飯若不加滷汁請先告知</li>
				</ul>
			</div>
		</div>
	</div>
	<div class='keybord'>
		<div style='width:calc(100% - 100px);height:calc(100% - 100px);margin:50px'>
			<input type='text' name='memno' style='width:100%;height:120px;float:left;text-align:right;font-size:100px;margin-bottom:2.5px;font-size:50px;' placeholder="請輸入電話" readonly>
			<input type='button' class='button' id='7' value='7' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='8' value='8' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='9' value='9' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='4' value='4' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='5' value='5' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='6' value='6' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='1' value='1' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='2' value='2' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='3' value='3' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<input type='button' class='button' id='0' value='0' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;font-size:80px;cursor:pointer;'>
			<button id='AC' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;cursor:pointer;'><div style='font-weight:bold;font-size:50px;color:#898989;'>重填</div><div style='font-weight:bold;font-size:45px;color:#CDCECE;'>AC</div></button>
			<button id='BKSP' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;cursor:pointer;'><div style='font-weight:bold;font-size:50px;color:#898989;'>倒退</div><div style='font-weight:bold;font-size:45px;color:#CDCECE;'>BKSP</div></button>
			<button id='cancel' style='width:calc(100% / 2 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;cursor:pointer;'><div style='font-weight:bold;font-size:50px;color:#898989;'>取消</div><div style='font-weight:bold;font-size:35px;color:#CDCECE;'>Cancel</div></button>
			<button id='submit' style='width:calc(100% / 2 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;cursor:pointer;'><div style='font-weight:bold;font-size:50px;color:#898989;'>確認</div><div style='font-weight:bold;font-size:35px;color:#CDCECE;'>Submit</div></button>
		</div>
	</div>
	<div class='memlist' style='padding:10px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;cursor:pointer;'>
		<div id='memdata' style='width:100%;height:240px;float:left;font-size:25px;font-family:Consolas,Microsoft JhengHei,sans-serif;'>
		</div>
		<div id='memsale' style='width:100%;height:240px;float:left;font-size:25px;font-family:Consolas,Microsoft JhengHei,sans-serif;'>
		</div>
		<div style='width:100%;height:100px;float:left;'>
			<div id='close' style='width:180px;height:calc(100% - 20px);margin:2.5px;float:left;border:3px solid #898989;border-radius: 7px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;cursor:pointer;cursor:pointer;font-size:50px;text-align:center;line-height:80px;color:#898989;'>
				關閉
			</div>
			<div id='change' style='width:180px;height:calc(100% - 20px);margin:2.5px;float:left;border:3px solid #898989;border-radius: 7px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;cursor:pointer;cursor:pointer;font-size:50px;text-align:center;line-height:80px;color:#898989;'>
				切換
			</div>
		</div>
	</div>
	<div class='selmem'>
	</div>
	<div class='checkbord'>
		<input type='hidden' id='tvid' name='<?php $team=parse_ini_file('../tw.ini',true);echo $team['basic']['id']; ?>'>
		<div class='funbox' style='width:calc(100% - 100px);height:calc(100% - 100px);margin:50px;overflow:hidden;display:none;'>
			<div id='menu' style='width:100%;height:20%;float:left;'>
				<button id='openclass' style='width:calc(100% / 4 - 5px);height:calc(100% / 2 - 5px);margin:2.5px;font-size:25px;float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;<?php if($time['time']['isopen']=='1'&&$orinit['init']['mainpos']==0)echo 'color:#898989;';else echo 'color:#f0f0f0;'; ?>' <?php if($time['time']['isopen']=='1'&&$orinit['init']['mainpos']==0)echo '';else echo 'disabled'; ?>>開班
				</button>
				<button id='closeclass' style='width:calc(100% / 4 - 5px);height:calc(100% / 2 - 5px);margin:2.5px;font-size:25px;float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;<?php if($time['time']['isclose']=='1'&&$orinit['init']['mainpos']==0)echo 'color:#898989;';else echo 'color:#f0f0f0;'; ?>' <?php if($time['time']['isclose']=='1'&&$orinit['init']['mainpos']==0)echo '';else echo 'disabled'; ?>>交班
				</button>
				<button style='width:calc(100% / 4 - 5px);height:calc(100% / 2 - 5px);margin:2.5px;font-size:25px;float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;color:#f0f0f0;' disabled>歷史交班
				</button>
				<button id='return' style='width:calc(100% / 4 - 5px);height:calc(100% / 2 - 5px);margin:2.5px;font-size:25px;float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>返回
				</button>
				<button id='voidsale' style='width:calc(100% / 4 - 5px);height:calc(100% / 2 - 5px);margin:2.5px;font-size:25px;float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>帳單作廢
				</button>
				<button id='viewsale' style='width:calc(100% / 4 - 5px);height:calc(100% / 2 - 5px);margin:2.5px;font-size:25px;float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>瀏覽帳單
				</button>
				<button id='help' style='width:calc(100% / 4 - 5px);height:calc(100% / 2 - 5px);margin:2.5px;font-size:25px;float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>遠端協助
				</button>
				<button id='close' style='width:calc(100% / 4 - 5px);height:calc(100% / 2 - 5px);margin:2.5px;font-size:25px;float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>關閉程式
				</button>
			</div>
			<div id='content' style='width:100%;height:80%;float:left;'>
			</div>
		</div>
		<div class='inputcode' style='width:calc(100% - 100px);height:calc(100% - 100px);margin:50px;overflow:hidden;'>
			<input type='password' name='pw' style='width:calc(100% - 5px);height:80px;float:left;text-align:right;font-size:60px;margin:0 0 2.5px 2.5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' placeholder="PASSWORD">
			<?php
			for($i=0;$i<26;$i++){
				echo "<input type='button' class='button' id='".chr($i+65)."' value='".chr($i+65)."'>";
			}
			?>
			<input type='button' class='button' id='' value='' disabled>
			<button id='AC' style='width:calc(100% / 4 - 5px);height:calc((100% - 82.5px) / 5.5 - 5px);float:right;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='width:100%;height:100%;-webkit-writing-mode: vertical-lr;writing-mode: vertical-lr;font-size:80px;color:#898989;font-weight:bold;text-align:center;line-height:134px;'>重填</div></button>
			<input type='button' class='button' id='7' value='7'>
			<input type='button' class='button' id='8' value='8'>
			<input type='button' class='button' id='9' value='9'>
			<input type='button' class='button' id='4' value='4'>
			<input type='button' class='button' id='5' value='5'>
			<input type='button' class='button' id='6' value='6'>
			<button id='BKSP' style='width:calc(100% / 4 - 5px);height:calc((100% - 82.5px) / 5.5 - 5px);float:right;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;vertical-align:middle;'><div style='width:100%;height:100%;-webkit-writing-mode: vertical-lr;writing-mode: vertical-lr;font-size:80px;color:#898989;font-weight:bold;text-align:center;line-height:134px;'>倒退</div></button>
			<input type='button' class='button' id='1' value='1'>
			<input type='button' class='button' id='2' value='2'>
			<input type='button' class='button' id='3' value='3'>
			<input type='button' class='button' id='0' value='0'>
			<button id='cancel' style='width:calc(100% / 4 - 5px);height:calc((100% - 82.5px) / 11 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='font-weight:bold;font-size:70px;color:#898989;'>返回</div></button>
			<button id='submit' style='width:calc(100% / 2 - 5px);height:calc((100% - 82.5px) / 11 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='font-weight:bold;font-size:70px;color:#898989;'>確認</div></button>
		</div>
	</div>
	<div class="logo" style='padding-top:20px;background-color:#F7F8F8;overflow:hidden;'>
		<div style='width:calc(980 / 1080 * 800px);height:(120 / 1920 * 1080px);padding-left:calc(100 / 1080 * 800px);float:left;'>
		<?php 
		if(isset($_GET['type'])){
			if($_GET['type']=='out'){
				echo '<div style="width:calc(100 / 1080 * 800px);padding-top:13px;text-align:center;font-size:50px;font-weight:bold;color:#898989;">外帶</div><div style="width:calc(100 / 1080 * 800px);text-align:center;font-size:23px;color:#898989;">To go</div>';
			}
			else {
				echo '<div style="width:calc(100 / 1080 * 800px);text-align:center;font-size:50px;font-weight:bold;color:#898989;">內用</div><div style="width:calc(100 / 1080 * 800px);text-align:center;font-size:23px;color:#898989;">Dine in</div>';
			}
		}
		else if(isset($_GET['memno'])){
			echo '<div class="memno" style="width:calc(400 / 1080 * 800px);padding-top:(13 / 1080 * 800px);font-size:calc(50 / 1080 * 800px);font-weight:bold;text-align:center;color:#898989;border:3px solid #898989;border-radius: 7px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;cursor:pointer;"></div>';
		}
		else if(isset($_GET['number'])){
			echo '<div style="width:calc(300 / 1080 * 800px);padding-top:(13 / 1080 * 800px);font-size:calc(50 / 1080 * 800px);font-weight:bold;color:#898989;">內用 '.$_GET['number'].' 號桌</div>';
		}
		else{
			echo '<div class="memno" style="width:calc(400 / 1080 * 800px);padding-top:(13 / 1080 * 800px);font-size:calc(50 / 1080 * 800px);font-weight:bold;text-align:center;color:#898989;border:3px solid #898989;border-radius: 7px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;cursor:pointer;">輸入會員電話</div>';
		}
		?>
		</div>
		<img style='width:calc(1080 / 1080 * 800px);height:calc(105 / 1920 * 1080px);float:left;' src='../database/img/<?php echo $content["basic"]["company"]; ?>/logo.png'>
		<!-- id='opentype' --><div  style='width:calc(980 / 1080 * 800px);height:calc(110 / 1920 * 1080px);padding-left:calc(100 / 1080 * 800px);background-color:#ffffff;float:left;'><!-- <img style='width:calc(110 / 1080 * 800px);height:calc(110 / 1920 * 1080px);float:left;' src='../database/img/type_icon.png'><div style='width:calc(100% - (110 / 1080 * 800px));height:60%;line-height:calc(110 / 1920 * 1080px * .6);float:left;font-size:calc(58 / 1080 * 800px);font-weight:bold;color:#595757;'>類別</div><div style='width:calc(100% - (110 / 1080 * 800px));height:40%;line-height:calc(110 / 1920 * 1080px * .4);float:left;font-size:calc(26 / 1080 * 800px);font-weight:bold;color:#898989;'>Category</div> --></div>
	</div>
	<div style='width:calc(100 / 1080 * 800px);height:calc(1100 / 1920 * 1080px);float:left;'>
		<img class='prev' style='width:calc(80 / 1080 * 800px);margin-top:calc(500 / 1920 * 1080px);margin-left:calc(10 / 1080 * 800px);' src='../database/img/left.png'>
	</div>
	<div id='main'>
		<div class='cycle-slideshow content' data-cycle-fx='scrollHorz' data-cycle-pager=".pagers" data-cycle-timeout='0' data-cycle-speed='800' data-cycle-slides="> div[class='foodbox']" data-cycle-prev=".prev" data-cycle-next=".next">
		<?php
		$conn=sqlconnect("../database","menu.db","","","",'sqlite');
		$sql='SELECT * FROM itemsdata ORDER BY createtime DESC';
		$itemname=parse_ini_file('../database/'.$content['basic']['company'].'-menu.ini',true);
		$frontname=parse_ini_file('../database/'.$content['basic']['company'].'-front.ini',true);
		$items=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
		if(sizeof($items)==0){
		}
		else{
			$rownumber=2;
			$colnumber=2;
			$pagenumber=$rownumber*$colnumber;
			$index=0;
			for($i=0;$i<sizeof($items);$i++){
				if(floatval($itemname[$items[$i]['inumber']]['counter'])<0||$itemname[$items[$i]['inumber']]['state']=='0'){
				}
				else{
					$mcounter=0;
					for($m=1;$m<=$pagenumber;$m++){
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
					if($index%$pagenumber==0){
						echo "<div class='foodbox'>";
					}
					else{
					}
					echo "<div class='food";
							if($index%$rownumber==($rownumber-1)){
							}
							else{
								echo " right";
							}
							if(floor($index/$rownumber)==($colnumber-1)){
							}
							else{
								echo " bottom";
							}
					echo "' id='number".$items[$i]['inumber']."'>
							<div class='fdimgbox'>
								<img class='foodimg' src='";
							if(isset($itemname[$items[$i]['inumber']]['image'])&&file_exists("../database/img/".$content['basic']['company'].'/'.$itemname[$items[$i]['inumber']]['image'])){
								echo "../database/img/".$content['basic']['company'].'/'.$itemname[$items[$i]['inumber']]['image']."?".date('YmdHis');
							}
							else{
								echo "../database/img/".$content['basic']['company']."/Null.png?".date('YmdHis');
							}
							echo "'>
								<div class='plus'>
									<img src='../database/img/wplus.png'>
								</div>
							</div>
							<div class='foodtitle'><strong style='float:left;font-size:".$itemname[$items[$i]['inumber']]['size1']."px;color:".$itemname[$items[$i]['inumber']]['color1'].";font-weight:";if($itemname[$items[$i]['inumber']]['bold1']=='1')echo 'bold';else echo 'normal';echo ";'>".$itemname[$items[$i]['inumber']]['name1']."</strong>";
						if($itemname[$items[$i]['inumber']]['mnumber']=='1'){
							echo "<strong style='float:right;font-size:".$itemname[$items[$i]['inumber']]['size1']."px;color:".$itemname[$items[$i]['inumber']]['color1'].";font-weight:";if($itemname[$items[$i]['inumber']]['bold1']=='1')echo 'bold';else echo 'normal';echo ";'>".$init['init']['frontunit'].$itemname[$items[$i]['inumber']]['money1'].$init['init']['unit']."</strong></div>";
						}
						else{
							echo "<strong style='float:right;'></strong></div>";
						}
						echo "<input type='hidden' class='typeno' value='".$items[$i]['fronttype']."'>
							<input type='hidden' class='typename' value='".$frontname[$items[$i]['fronttype']]['name1']."'>
							<input type='hidden' class='name' value='".$itemname[$items[$i]['inumber']]['name1']."'>
							<input type='hidden' class='inumber' value='".$items[$i]['inumber']."'>";
					echo "</div>";
					if($index%$pagenumber==($pagenumber-1)){
						echo "</div>";
					}
					else{
					}
					$index++;
				}
			}
			if($index%$pagenumber<=($pagenumber-1) && $index%$pagenumber>=1){
				echo "</div>";
			}
			else{
			}
		}
		?>
		</div>
		<div class="pagers"></div>
	</div>
	<div style='width:calc(100 / 1080 * 800px);height:calc(1100 / 1920 * 1080px);float:left;'>
		<img class='next' style='width:calc(80 / 1080 * 800px);margin-top:calc(500 / 1920 * 1080px);margin-left:calc(10 / 1080 * 800px);' src='../database/img/right.png'>
	</div>
	<div class='list'>
		<div class='listcontent'><table><tr class='box'></tr></table></div>
	</div>
	<div class='finalfun'>
		<div class='total' style='width:calc(332 / 1080 * 800px);height:calc(100 / 1920 * 1080px);line-height:calc(100 / 1920 * 1080px);text-align:right;float:left;font-size:calc(55 / 1080 * 800px);color:#3e3a39;padding-right:calc(10 / 1080 * 800px);margin:calc(37.5 / 1920 * 1080px) 0 0 calc(100 / 1080 * 800px);font-weight:bold;background-image:url("../database/img/totalmoney.png");background-size:100% 100%;'><?php echo $init['init']['frontunit']; ?><div style='display:inline;' class='tmoy'>0</div><?php echo $init['init']['unit']; ?></div>
		<input type='button' class='mbutton' id='submit' style='width:calc(210 / 1080 * 800px);height:calc(110 / 1920 * 1080px);margin:calc(37.5 / 1920 * 1080px) calc(100 / 1080 * 800px) 0 0;float:right;background-image:url("../database/img/submit.png");background-size:100% 100%;'>
		<input type='button' class='mbutton' id='home' style='width:calc(210 / 1080 * 800px);height:calc(110 / 1920 * 1080px);margin:calc(37.5 / 1920 * 1080px) calc(10 / 1080 * 800px) 0 0;float:right;background-image:url("../database/img/home.png");background-size:100% 100%;'>
	</div>
</body>
</html>
