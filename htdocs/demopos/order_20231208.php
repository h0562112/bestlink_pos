<!doctype html>
<html lang="en">
<?php
$machinedata=parse_ini_file('../database/machinedata.ini',true);
if(isset($machinedata['posdvr']['key'])&&$machinedata['posdvr']['key']!=''){
	setCookie('auth',$machinedata['posdvr']['key'],time()+86400,'/','quickcode.com.tw');
}
else{
}
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($dbmapping['map'][$machinedata['basic']['terminalnumber']])){
		$invmachine=$dbmapping['map'][$machinedata['basic']['terminalnumber']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
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
			if(isset($origArray[$i][$type])&&isset($pivot[$type])){
				if (floatval($origArray[$i][$type]) <= floatval($pivot[$type])) {
					array_push($left,$origArray[$i]);
				} else {
					array_push($right,$origArray[$i]);
				}
			}
			else if(isset($origArray[$i][$type])){
				array_push($right,$origArray[$i]);
			}
			else{
				array_push($left,$origArray[$i]);
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
$initsetting=parse_ini_file('../database/initsetting.ini',true);
if(file_exists('../database/member.ini')){
	$member=parse_ini_file('../database/member.ini',true);
}
else{
	$member='';
}
?>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<title>點餐畫面</title>
	<?php 
	$regex_match="/(iphone|iPad)/i";
	//if(preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']))){
	?>
	<script type="text/javascript" src="../tool/fastclick/lib/fastclick.js?<?php echo date('His'); ?>"></script>
	<?php
	/*}
	else{
	}*/
	?>
	<script type="text/javascript" src="../tool/jquery-1.12.4.js?<?php echo date('His'); ?>"></script>
	<script type="text/javascript" src="../tool/ui/1.12.1/jquery-ui.js?<?php echo date('His'); ?>"></script>
	<script type="text/javascript" src="../tool/ui/1.12.1/datepicker-zh-TW.js?<?php echo date('His'); ?>"></script>
	<script type="text/javascript" src="./lib/js/main.js?<?php echo date('His'); ?>"></script>
	<?php
	if(isset($initsetting['init']['pointtree'])&&$initsetting['init']['pointtree']=='1'){//集點樹API介接function
	?>
	<script type="text/javascript" src="./lib/api/pointtree_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	?>
	<?php
	if(isset($initsetting['init']['posdvr'])&&$initsetting['init']['posdvr']=='1'){//錢都錄API介接function
	?>
	<script type="text/javascript" src="./lib/api/posdvr/posdvr_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	?>
	<?php
	if(isset($initsetting['init']['bolai'])&&$initsetting['init']['bolai']=='1'){//鑫寶錸API介接function
	?>
	<script type="text/javascript" src="./lib/api/bolai/bolai_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	?>
	<?php
	if((isset($initsetting['init']['openmember'])&&$initsetting['init']['openmember']=='1')&&((isset($initsetting['init']['onlinemember'])&&$initsetting['init']['onlinemember']=='1')||(isset($initsetting['init']['ourmempointmoney'])&&$initsetting['init']['ourmempointmoney']=='1'))){//使用網路會員才需要include會員點數、儲值金的API。如是本地會員，則不用網路驗證。
	?>
	<script type="text/javascript" src="./lib/api/ourmember/member_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	?>
	<?php
	if(isset($initsetting['init']['intellapay'])&&$initsetting['init']['intellapay']=='1'){//0>>不使用1>>使用"英特拉"電子支付
	?>
	<script type="text/javascript" src="./lib/api/intella/intella_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	if(isset($initsetting['init']['directlinepay'])&&$initsetting['init']['directlinepay']=='1'){//0>>不使用1>>使用直接linepay付款串接
	?>
	<script type="text/javascript" src="./lib/api/directlinepay/directlinepay_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	if(isset($initsetting['init']['jkos'])&&$initsetting['init']['jkos']=='1'){//0>>不使用1>>使用街口付款串接
	?>
	<script type="text/javascript" src="./lib/api/jkos/jkos_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	if(isset($initsetting['init']['pxpayplus'])&&$initsetting['init']['pxpayplus']=='1'){//0>>不使用1>>使用全支付付款串接
	?>
	<script type="text/javascript" src="./lib/api/pxpayplus/pxpayplus_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	?>
	<script type="text/javascript" src="./lib/api/nccc/nccc_api.js?<?php echo date('His'); ?>"></script>
	<?php
	/*if(isset($initsetting['init']['itri'])&&$initsetting['init']['itri']=='1'){//0>>關閉"工研院商業獅"優惠卷1>>開啟"工研院商業獅"優惠卷//2020/2/11目前實際看來專案無法RUN，暫時移除
	?>
	<script src="./lib/api/itri/itri_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}*/
	?>
	<?php
	if(isset($initsetting['init']['usenodejs'])&&$initsetting['init']['usenodejs']=='1'&&file_exists('../nodejs/node_modules/socket.io-client/dist/socket.io.js')){//0>>遵循舊有流程1>>套用nodejs流程
	?>
	<script type="text/javascript" src='../nodejs/node_modules/socket.io-client/dist/socket.io.js'></script>
	<?php
	}
	else{
	}
	?>
	<link type="text/css" rel="stylesheet" href="../tool/ui/1.12.1/themes/base/jquery-ui.css?<?php echo date('His'); ?>">
	<?php
	if(isset($machinedata['basic']['csstype'])&&$machinedata['basic']['csstype']=='big'){
	?>
		<link type="text/css" rel="stylesheet" href="./lib/css/bigmain.css?<?php echo date('His'); ?>">
	<?php
	}
	else{
	?>
		<link type="text/css" rel="stylesheet" href="./lib/css/main.css?<?php echo date('His'); ?>">
	<?php
	}
	?>
	<?php
	include_once '../tool/dbTool.inc.php';
	//date_default_timezone_set('Asia/Taipei');
	date_default_timezone_set($initsetting['init']['settime']);
	$Y=date('Y');
	$year=(intval($Y)-1911);
	if(intval(date('m'))%2==0){
		$month=date('m');
	}
	else{
		$month=intval(date('m'))+1;
	}
	if(strlen($month)<2){
		$month='0'.$month;
	}
	$basic=parse_ini_file('../database/setup.ini',true);
	if(isset($basic['mobilekeyword']['word'])){
	}
	else{
		$basic['mobilekeyword']['word']='iOS|iPhone|iPad|Android|Macintosh';
	}
	if(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1'&&isset($basic['basic']['sendinvlocation'])&&$basic['basic']['sendinvlocation']=='3'){//中鼎發票
	?>
	<script type="text/javascript" src="./lib/api/zdninv/zdn_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	if(isset($initsetting['rfid']['open'])&&$initsetting['rfid']['open']!='0'){//2020/4/15 rfid讀取點餐，最先由三顧提出串接
	?>
	<script type="text/javascript" src="./lib/api/rfid/read_api.js?<?php echo date('His'); ?>"></script>
	<?php
	}
	else{
	}
	$print=parse_ini_file('../database/printlisttag.ini',true);
	if(file_exists('../database/mapping.ini')){
		$dbmapping=parse_ini_file('../database/mapping.ini',true);
		if(isset($_GET['submachine'])&&$_GET['submachine']!=''&&isset($dbmapping['map'][$_GET['submachine']])){
			$invmachine=$dbmapping['map'][$_GET['submachine']];
		}
		else if(isset($_GET['machine'])&&$_GET['machine']!=''&&isset($dbmapping['map'][$_GET['machine']])){
			$invmachine=$dbmapping['map'][$_GET['machine']];
		}
		else{
			$invmachine='m1';
		}
	}
	else{
		$invmachine='';
	}
	if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'){//帳務以每台分機為個別主體計算
		if(isset($invmachine)&&$invmachine!=''){
			$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
		}
		else{
			$timeini=parse_ini_file('../database/timem1.ini',true);
		}
	}
	else{//帳務以主機為主體計算
		$timeini=parse_ini_file('../database/timem1.ini',true);
	}
	if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']=='1'&&file_exists('../database/floorspend.ini')){
		$tb=parse_ini_file('../database/floorspend.ini',true);
	}
	else{
		$tb='-1';
	}
	if(file_exists('../database/otherpay.ini')){
		$otherpay=parse_ini_file('../database/otherpay.ini',true);
	}
	else{
		//$otherpay='-1';
	}
	//$conn=sqlconnect("localhost","ban","banuser","1qaz2wsx","utf-8","mysql");
	//$sql='SELECT banno FROM number WHERE state=1 AND company="'.$basic['basic']['company'].'" AND story="'.$basic['basic']['story'].'" AND dateTime="'.$year.$month.'" ORDER BY banno LIMIT 1';
	//$table=sqlquery($conn,$sql,'mysql');
	//sqlclose($conn,'mysql');
	if(file_exists('../database/itemdis.ini')){
		$itemdis=parse_ini_file('../database/itemdis.ini',true);
	}
	else{
		$itemdis='-1';
	}
	$unit=parse_ini_file('../database/unit.ini',true);
	if(file_exists('./syspram/buttons-'.$initsetting['init']['firlan'].'.ini')){
		$buttons1=parse_ini_file('./syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
	}
	else{
		$buttons1='-1';
	}
	if(file_exists('./syspram/buttons-'.$initsetting['init']['seclan'].'.ini')){
		$buttons2=parse_ini_file('./syspram/buttons-'.$initsetting['init']['seclan'].'.ini',true);
	}
	else{
		$buttons2='-1';
	}
	if(file_exists('./syspram/interface-'.$initsetting['init']['firlan'].'.ini')){
		$interface1=parse_ini_file('./syspram/interface-'.$initsetting['init']['firlan'].'.ini',true);
	}
	else{
		$interface1='-1';
	}
	if(file_exists('./syspram/interface-'.$initsetting['init']['seclan'].'.ini')){
		$interface2=parse_ini_file('./syspram/interface-'.$initsetting['init']['seclan'].'.ini',true);
	}
	else{
		$interface2='-1';
	}
	if(file_exists('../database/reason.ini')){
		$reason=parse_ini_file('../database/reason.ini',true);
	}
	else{
		$reason='';
	}
	if(file_exists('../database/personnel.ini')){
		$personnel=parse_ini_file('../database/personnel.ini',true);
	}
	else{
		$personnel='';
	}
	?>
	<?php
	if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']=='1'){
	?>
	<?php
	}
	else{
	}
	?>
	<style>
		#ui-datepicker-div.ui-datepicker {
			font-size:3vw;
		}
		.ui-dialog .ui-dialog-content {
			padding:0px 16px;
		}
		#ui-datepicker-div .ui-icon-circle-triangle-w {
			background-position: 0 0;
		}
		#ui-datepicker-div .ui-icon-circle-triangle-e {
			background-position: -50px 0;
		}
		#ui-datepicker-div .ui-icon {
			width:50px;
			height:50px;
			margin:calc((1.8em - 50px) / 2) calc((1.8em - 50px) / 2);
			top:0;
			left:0;
		}
		#ui-datepicker-div .ui-widget-header .ui-icon {
			background-image:url('../database/img/lr.png');
			background-size:100px 50px;
		}
		<?php
		if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']=='1'){
			?>
			.ui-dialog-titlebar button.hidden {
				display: none;
			}
			.inittable button,
			.tablesplit button,
			.tableclear button,
			.combine button,
			.tablecombine button,
			.changetable button {
				border-radius:0;
				padding: 1px 6px;
				white-space: normal;
				background-color:buttonface;
				border-width: 2px;
				border-style: outset;
				border-color: buttonface;
				border-image: initial;
			}
			.control#control button,
			.control#control div {
				font-family: Consolas,Microsoft JhengHei,sans-serif;
			}
			.tablemap button,
			.funcmap button {
				width:100%;
				height:100%;
				font-size:1.5vw;
				font-family: Consolas,Microsoft JhengHei,sans-serif;
			}
			.tablemap button #amt span:before {
				content:'<?php echo $initsetting["init"]["frontunit"];  ?>';
			}
			.tablemap button #amt span:after {
				content:'<?php echo $initsetting["init"]["unit"];  ?>';
			}
			.tablemap button[id="comput notempty "] {
				border:5px solid #898989;
				border-radius: 5px;
				background-color: #ff0066;
				color:#ffffff;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}
			.outsidelist button,
			.splittablelist button {
				width:100%;
				height:80px;
				margin-bottom:3px;
				float:left;
			}
			button div#tablenumber,
			button div#persons {
				width:50%;
				height:50%;
				line-height:130%;
				float:left;
			}
			button div#checkbox {
				width:50%;
				height:100%;
				font-size:300%;
				float:left;
			}
			button div#QTYlabel,
			button div#QTY {
				width:50%;
				height:100%;
				line-height:420%;
				float:left;
			}
			button div#amt,
			button div#createdatetime {
				width:50%;
				height:50%;
				line-height:130%;
				text-align:right;
				float:right;
			}
			.inittable td,
			.changetable td,
			.combine td,
			.tablecombine td {
				 width:calc(100% / <?php echo intval($tb['TA']['col'])+1; ?>);
				 height:calc(100% / <?php echo intval($tb['TA']['row']); ?>);
			}
			.changetable #notempty,
			.combine #notempty,
			.combine #check,
			.tablecombine #notempty,
			.tablecombine #check,
			.tablesplit #notempty,
			.tablesplit #check,
			.tableclear #notempty,
			.tableclear #check {
				border:5px solid #898989;
				border-radius: 5px;
				background-color: #ff0066;
				color:#ffffff;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}
			<?php
		}
		else{
		}
		?>
		<?php
		if($initsetting['init']['menutype']==1){//單層
			echo ".order#order #menubox #items {
					width: calc(100% - 2px);
					height: calc(75% - 2px);
					margin:	1px;
				}";
		}
		else if($initsetting['init']['menutype']==2){//雙層
			echo ".order#order #menubox #itemtype {
					width: calc(100% - 2px);
					height: calc(17% - 1px);
					margin:	1px 1px 0 1px;
				}

				.order#order #menubox #items {
					width: calc(100% - 2px);
					height: calc(58% - 1px);
					margin:	0 1px 1px 1px;
				}";
		}
		else if($initsetting['init']['menutype']==3){//雙頁雙層
			echo ".order#order #menubox #itemtype {
					width: calc(100% - 2px);
					height: calc(75% - 2px);
					margin:	1px;
					position: absolute;
					top: 0;
					left: 0;
					border-bottom:1px solid #898989;
				}

				.order#order #menubox #items {
					width: calc(100% - 2px);
					height: calc(75% - 2px);
					margin:	1px;
					position: absolute;
					top: 0;
					left: 0;
				}";
		}
		?>
	</style>
</head>
<body>
	<?php
	//2022/10/14 平板模組停用公告視窗
	if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"])&&isset($initsetting['mobilepa'])&&isset($initsetting['mobilepa']['startdate'])&&intval($initsetting['mobilepa']['startdate'])<=intval(date('Ymd'))){
		echo '<div class="mobilepa" style="width:100%;height:100%;padding:10px;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;background-color: #fbf4ec;z-index:10000;position:fixed;top:0;left:0;"><center style="font-size: 20px;">公告</center><div style="width: 100%; height: calc(100% - 129px - 80px);margin-bottom:2px;border-bottom:2px solid #000000; overflow-y: auto; overflow-x: hidden;text-align:center;padding:80px 0 0 0;font-size:30px;">因尚未收到您應繳納的帳款，導致您的平板使用已於 '.date('Y/m/d',strtotime($initsetting['mobilepa']['startdate'])).' 受到限制。<br><br>請聯絡客服<br><div><table style="width:100%;"><tr><td>Line@官方帳號</td><td><img src="./img/clientservice.png" style="width:100px;height:100px;"></td></tr><tr><td>客服連絡電話</td><td>04-2463-7555<br>04-3507-9949</td></tr></table></div></div><div style="width:100%;height:100px;"><span style="float:left;font-size:30px;margin:6px 0 0 0;">臨時啟用密碼：</span><input name="password" style="width: calc(100% - 200px - 210px - 2px - 40px); height: 50px; float: left; margin: 0px 10px; border: 1px solid #898989;font-size:30px;padding-left:5px;padding-right:5px;border-radius:5px;" type="password" placeholder="請輸入臨時啟用密碼"><button class="checkpa" style="width: 200px; height: 50px; float: left; font-size: 30px;margin:2px 0;">確認</button><div id="checkres" style="color:#ff0000;text-align:center;"></div></div></div>';
	}
	//2021/12/13 POS端公告視窗
	else if(isset($initsetting['pospa'])&&isset($initsetting['pospa']['startdate'])&&intval($initsetting['pospa']['startdate'])<=intval(date('Ymd'))){
		echo '<div class="pospa" style="width:100%;height:100%;padding:10px;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;background-color: #fbf4ec;z-index:10000;position:fixed;top:0;left:0;">
			<center style="font-size: 20px;">公告</center>
			<div style="width: 100%; height: calc(100% - 129px - 80px);margin-bottom:2px;border-bottom:2px solid #000000; overflow-y: auto; overflow-x: hidden;text-align:center;padding:80px 0 0 0;font-size:30px;">
				因尚未收到您應繳納的帳款，導致您的軟體使用已於 '.date('Y/m/d',strtotime($initsetting['pospa']['startdate'])).' 受到限制。<br>
				<br>
				請聯絡客服<br>
				<div>
					<table style="width:100%;">
						<tr>
							<td>Line@官方帳號</td>
							<td><img src="./img/clientservice.png" style="width:100px;height:100px;"></td>
						</tr>
						<tr>
							<td>客服連絡電話</td>
							<td>04-2463-7555<br>04-3507-9949</td>
						</tr>
					</table>
					<button class="exit" style="width: 200px; height: 50px; font-size: 30px;margin:2px 0;">離開系統</button>
				</div>
			</div>
			<div style="width:100%;height:100px;">
				<span style="float:left;font-size:30px;margin:6px 0 0 0;">臨時啟用密碼：</span>
				<input name="password" style="width: calc(100% - 200px - 210px - 2px - 40px); height: 50px; float: left; margin: 0px 10px; border: 1px solid #898989;font-size:30px;padding-left:5px;padding-right:5px;border-radius:5px;" type="password" placeholder="請輸入臨時啟用密碼">
				<button class="checkpa" style="width: 200px; height: 50px; float: left; font-size: 30px;margin:2px 0;">確認</button>
				<div id="checkres" style="color:#ff0000;text-align:center;"></div>
			</div>
		</div>';
	}
	else{
		//echo '<div>'.(intval($initsetting['pospa']['startdate'])<=intval(date('Ymd'))).'</div>';
	}
	?>
	<?php
	if(isset($initsetting['init']['usenodejs'])&&$initsetting['init']['usenodejs']=='1'&&file_exists('../nodejs/node_modules/socket.io-client/dist/socket.io.js')){//0>>遵循舊有流程1>>套用nodejs流程
		echo '<input type="hidden" name="usenodejs" value="1">';
	}
	else{
	}
	if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']=='1'){
	?>
	<div class='control' id='control'>
		<input type='hidden' id='usercode' value='<?php if(isset($_GET['usercode']))echo $_GET['usercode']; ?>'>
		<input type='hidden' id='username' value='<?php if(isset($_GET['username']))echo $_GET['username']; ?>'>
		<input type='hidden' id='bizdate' value='<?php echo $timeini['time']['bizdate']; ?>'>
		<input type='hidden' id='zcounter' value='<?php echo $timeini['time']['zcounter']; ?>'>
		<input type='hidden' id='mactype' value='<?php if(isset($_GET['submachine']))echo 'submachine';else echo 'machine'; ?>'>
		<input type='hidden' id='machinename' value='<?php if(isset($_GET['submachine']))echo 'empty';else if(isset($_GET['machine'])&&$_GET['machine']!='')echo $_GET['machine'];else echo 'm1'; ?>'>
		<input type='hidden' id='submachinename' value='<?php if(isset($_GET['submachine'])&&$_GET['submachine']!='')echo $_GET['submachine'];else echo 'empty'; ?>'>
		<?php
		if(file_exists('./syspram/buttons-'.$initsetting['init']['firlan'].'.ini')){
			$buttons1=parse_ini_file('./syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
		}
		else{
			$buttons1='-1';
		}
		if(file_exists('./syspram/buttons-'.$initsetting['init']['seclan'].'.ini')){
			$buttons2=parse_ini_file('./syspram/buttons-'.$initsetting['init']['seclan'].'.ini',true);
		}
		else{
			$buttons2='-1';
		}
		if(file_exists('./syspram/interface-'.$initsetting['init']['firlan'].'.ini')){
			$interface1=parse_ini_file('./syspram/interface-'.$initsetting['init']['firlan'].'.ini',true);
		}
		else{
			$interface1='-1';
		}
		if(file_exists('./syspram/interface-'.$initsetting['init']['seclan'].'.ini')){
			$interface2=parse_ini_file('./syspram/interface-'.$initsetting['init']['seclan'].'.ini',true);
		}
		else{
			$interface2='-1';
		}
		date_default_timezone_set('Asia/Taipei');
		include_once '../tool/dbTool.inc.php';
		?>
		<div class='outsidelist' title='<?php echo $buttons1['name']['listtype2']; ?>帳單'>
			<button id='newlist'>新增<?php echo $buttons1['name']['listtype2']; ?>單</button>
			<?php
			$dir='./table/outside';
			$filelist=scandir($dir,1);
			foreach($filelist as $fl){
				if(strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'])){
					if(isset($count)){
						$count++;
					}
					else{
						$count=1;
					}
				}
				else{
					if(isset($count)){
					}
					else{
						$count=0;
					}
				}
			}
			?>
		</div>
		<?php
		$nowtime=date_create(date('YmdHis'));
		?>
		<div class='inittable' style='overflow:hidden;'>
			<input type='hidden' name='window' value='inittable'>
			<!-- <table style='width:100%;height:100%;text-align:center;'> -->
			<div class='tablemap' style='width:calc(500% / 6 - 1em);height:calc(100% - 1em);float:left;margin:.5em;padding:0;position: relative;'>
				<?php
				$totaltable=0;
				$ordertable=0;
				if(isset($tb['TA']['page'])&&intval($tb['TA']['page'])>=1){
					$startindex=1;
					$labelbox="<div id='butbox' style='width:100%;overflow:hidden;position: absolute;top:0;'>";
					for($page=1;$page<=$tb['TA']['page'];$page++){
						$labelbox.='<button id="page'.$page.'button" style="width:75px;height:100%;float:left;" class="w3-bar-item w3-button ';if($page==1)$labelbox.='focus';$labelbox.='" onclick="openCity(\'inittable\',\'page'.$page.'\')">';
						if(isset($tb['TA']['controlfloor'.$page])){
							$labelbox.=$tb['TA']['controlfloor'.$page];
						}
						else{
							$labelbox.=$page.'樓';
						}
						$labelbox.='</button>';
						echo '<div id="page'.$page.'" class="w3-container inittableItem" style="height:calc(100% - 1em - 30px);overflow:hidden;position: relative;top:50px;';if($page==1)echo 'display:block;';else echo 'display:none;';echo '">';
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
							echo '<div class="T'.$i.'" style="width:calc(100% / '.$pagecol.');height:calc(100% / '.$pagerow.');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
							if($tb['T'.$i]['tablename']==''){
							}
							else{
								$splitnum=0;
								if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
								?>
									<?php 
									$tablenumname='';
									$t='';
									$time='';
									$bizdate='';
									$state='0';
									$consecnumber='';
									$saleamt='';
									$person='';
									$createdatetime='';
									$dir=('./table');
									$filelist=scandir($dir);
									$maps=0;
									foreach($filelist as $fl){
										//echo $fl;
										if(file_exists('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$tb['T'.$i]['tablename']).'.ini')&&$maps==0){
											$maps=-1;
											$tabledata=parse_ini_file('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$tb['T'.$i]['tablename']).'.ini',true);
											foreach($tabledata as $tdindex=>$td){
												if($td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&$td['consecnumber']!=""){
													$splitnum++;
													if(strlen($t)==0){
														$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
														$diff=date_diff($nowtime,$maxtime);
														$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
														if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
															$t=1;
															$time=floatval($mins);
														}
														else if(floatval($mins)<=0){
															$t=-1;
															$time=0;
														}
														else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
															$t=-2;
															$time=floatval($mins);
														}
														else{
															$t=2;
															$time=floatval($mins);
														}
														$tablenumname=$tdindex;
														$bizdate=$td['bizdate'];
														$state=$td['state'];
														$consecnumber=$td['consecnumber'];
														$saleamt=$td['saleamt'];
														$person=$td['person'];
														$createdatetime=$td['createdatetime'];
													}
													else{
													}
												}
												else{
												}
											}
										}
										else{
										}
										if(strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$tb['T'.$i]['tablename'].'-')&&$fl!=$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$tb['T'.$i]['tablename'].'.ini'&&$maps==0){
											$maps=1;
											$tabledata=parse_ini_file('./table/'.iconv('utf-8','big5',$fl),true);
											foreach($tabledata as $tdindex=>$td){
												if($td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&$td['consecnumber']!=""){
													$splitnum++;
													if(strlen($t)==0){
														$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
														$diff=date_diff($nowtime,$maxtime);
														$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
														if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
															$t=1;
															$time=floatval($mins);
														}
														else if(floatval($mins)<=0){
															$t=-1;
															$time=0;
														}
														else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
															$t=-2;
															$time=floatval($mins);
														}
														else{
															$t=2;
															$time=floatval($mins);
														}
														$tablenumname=$tdindex;
														$bizdate=$td['bizdate'];
														$state=$td['state'];
														$consecnumber=$td['consecnumber'];
														$saleamt=$td['saleamt'];
														$person=$td['person'];
														$createdatetime=$td['createdatetime'];
													}
													else{
													}
												}
												else{
												}
											}
										}
										else if(strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$tb['T'.$i]['tablename'])&&$fl!=$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$tb['T'.$i]['tablename'].'.ini'&&($maps==1||$maps==-1)){
											$maps=1;
											$splitnum++;
										}
										else{
											if(($maps==1)){
												break;
											}
											else{
											}
										}
									}
									?>
									<button class='table' <?php 
									if(strlen($t)!=0&&strlen($time)!=0){
									//if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
										echo 'id="comput notempty "';
										$ordertable++;
										$totaltable++;
									}
									else{
										echo 'id="comput"';
										$totaltable++;
									}
									if($t==1||$t=='');
									else if($t==2)echo 'style="background-color:#73d7ec;"';
									else if($t==-2)echo 'style="background-color:#e7f12c;"';
									else echo 'style="background-color:#00d941;"'; 
									?> <?php /*if(isset($ttt[$tb['T'.$i]['tablename']]['split'])&&$ttt[$tb['T'.$i]['tablename']]['split']==1)*/if(isset($splitnum)&&intval($splitnum)>1)echo 'name="split"'; ?>>
										<div id='tablenumber'><?php
											if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
											?><input type='hidden' name='tabnum' value='<?php if(isset($tablenumname)&&$tablenumname!='')echo $tablenumname;else echo $tb['T'.$i]['tablename']; ?>'>
											<input type='hidden' name='inittable' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
											<input type='hidden' name='consecnumber' value='<?php echo $consecnumber; ?>'>
											<input type='hidden' name='state' value='<?php echo $state; ?>'>
										</div>
										<div id='amt'><?php echo "<span>".$saleamt."</span>"; ?></div>
										<div id='persons'><?php if(intval($person)!='')echo $person.'位'; ?></div>
										<div id='createdatetime'><span id='val'><?php echo $time; ?></span><input type='hidden' name='createdatetime' value='<?php echo substr($createdatetime,4,2).'/'.substr($createdatetime,6,2).' '.substr($createdatetime,8,2).':'.substr($createdatetime,10,2); ?>'><input type='hidden' name='bizdate' value='<?php echo $bizdate; ?>'></div>
									</button>
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
					echo '<div style="width:100%;height:100%;">';
					for($i=1;$i<=(intval($tb['TA']['row'])*intval($tb['TA']['col']));$i++){
						echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
						if($tb['T'.$i]['tablename']==''){
						}
						else{
							$splitnum=0;
							if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
							?>
								<?php 
								$tablenumname='';
								$t='';
								$time='';
								$bizdate='';
								$state='0';
								$consecnumber='';
								$saleamt='';
								$person='';
								$createdatetime='';
								$dir=('./table');
								$filelist=scandir($dir);
								$maps=0;
								foreach($filelist as $fl){
									//echo $fl;
									if(file_exists('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$tb['T'.$i]['tablename']).'.ini')&&$maps==0){
										$maps=-1;
										$tabledata=parse_ini_file('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$tb['T'.$i]['tablename']).'.ini',true);
										foreach($tabledata as $tdindex=>$td){
											if($td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&$td['consecnumber']!=""){
												$splitnum++;
												if(strlen($t)==0){
													$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
													$diff=date_diff($nowtime,$maxtime);
													$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
													if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
														$t=1;
														$time=floatval($mins);
													}
													else if(floatval($mins)<=0){
														$t=-1;
														$time=0;
													}
													else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
														$t=-2;
														$time=floatval($mins);
													}
													else{
														$t=2;
														$time=floatval($mins);
													}
													$tablenumname=$tdindex;
													$bizdate=$td['bizdate'];
													$state=$td['state'];
													$consecnumber=$td['consecnumber'];
													$saleamt=$td['saleamt'];
													$person=$td['person'];
													$createdatetime=$td['createdatetime'];
												}
												else{
												}
											}
											else{
											}
										}
									}
									else{
									}
									if(strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$tb['T'.$i]['tablename'].'-')&&$fl!=$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$tb['T'.$i]['tablename'].'.ini'&&$maps==0){
										$maps=1;
										$tabledata=parse_ini_file('./table/'.iconv('utf-8','big5',$fl),true);
										foreach($tabledata as $tdindex=>$td){
											if($td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&$td['consecnumber']!=""){
												$splitnum++;
												if(strlen($t)==0){
													$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
													$diff=date_diff($nowtime,$maxtime);
													$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
													if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
														$t=1;
														$time=floatval($mins);
													}
													else if(floatval($mins)<=0){
														$t=-1;
														$time=0;
													}
													else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
														$t=-2;
														$time=floatval($mins);
													}
													else{
														$t=2;
														$time=floatval($mins);
													}
													$tablenumname=$tdindex;
													$bizdate=$td['bizdate'];
													$state=$td['state'];
													$consecnumber=$td['consecnumber'];
													$saleamt=$td['saleamt'];
													$person=$td['person'];
													$createdatetime=$td['createdatetime'];
												}
												else{
												}
											}
											else{
											}
										}
									}
									else if(strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$tb['T'.$i]['tablename'])&&$fl!=$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$tb['T'.$i]['tablename'].'.ini'&&($maps==1||$maps==-1)){
										$maps=1;
										$splitnum++;
										/*foreach($tabledata as $tdindex=>$td){
											if($td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&$td['consecnumber']!=""){
												$splitnum++;
												if(strlen($t)==0){
													$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
													$diff=date_diff($nowtime,$maxtime);
													$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
													if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
														$t=1;
														$time=floatval($mins);
													}
													else if(floatval($mins)<=0){
														$t=-1;
														$time=0;
													}
													else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
														$t=-2;
														$time=floatval($mins);
													}
													else{
														$t=2;
														$time=floatval($mins);
													}
													$tablenumname=$tdindex;
													$bizdate=$td['bizdate'];
													$consecnumber=$td['consecnumber'];
													$saleamt=$td['saleamt'];
													$person=$td['person'];
													$createdatetime=$td['createdatetime'];
												}
												else{
												}
											}
											else{
											}
										}*/
									}
									else{
										if(($maps==1)){
											break;
										}
										else{
										}
									}
								}
								/*if(file_exists('./table/'.$tb['T'.$i]['tablename'].'.ini')){
								//if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
									$tabledata=parse_ini_file('./table/'.$tb['T'.$i]['tablename'].'.ini',true);
									foreach($tabledata as $td){
										if($td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&$td['consecnumber']!=""){
											$splitnum++;
											if(strlen($t)==0){
												$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
												$diff=date_diff($nowtime,$maxtime);
												$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
												if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
													$t=1;
													$time=floatval($mins);
												}
												else if(floatval($mins)<=0){
													$t=-1;
													$time=0;
												}
												else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
													$t=-2;
													$time=floatval($mins);
												}
												else{
													$t=2;
													$time=floatval($mins);
												}
												$bizdate=$td['bizdate'];
												$consecnumber=$td['consecnumber'];
												$saleamt=$td['saleamt'];
												$person=$td['person'];
												$createdatetime=$td['createdatetime'];
											}
											else{
											}
										}
										else{
										}
									}
									//$mins=intval(substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],8,2))*60+intval(substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],10,2));
									/*$maxtime=date_create(date('YmdHis',strtotime($ttt[$tb['T'.$i]['tablename']]['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
									$diff=date_diff($nowtime,$maxtime);
									$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
									if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
										$t=1;
										$time=floatval($mins);
									}
									else if(floatval($mins)<=0){
										$t=-1;
										$time=0;
									}
									else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
										$t=-2;
										$time=floatval($mins);
									}
									else{
										$t=2;
										$time=floatval($mins);
									}*/
								/*}
								else{
									$t='';
									$time='';
								}*/
								?>
								<button class='table' <?php 
								if(strlen($t)!=0&&strlen($time)!=0){
								//if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
									echo 'id="comput notempty "';
									$ordertable++;
									$totaltable++;
								}
								else{
									echo 'id="comput"';
									$totaltable++;
								}
								if($t==1||$t=='');
								else if($t==2)echo 'style="background-color:#73d7ec;"';
								else if($t==-2)echo 'style="background-color:#e7f12c;"';
								else echo 'style="background-color:#00d941;"'; 
								?> <?php /*if(isset($ttt[$tb['T'.$i]['tablename']]['split'])&&$ttt[$tb['T'.$i]['tablename']]['split']==1)*/if(isset($splitnum)&&intval($splitnum)>1)echo 'name="split"'; ?>>
									<div id='tablenumber'><?php 
										if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
										?><input type='hidden' name='tabnum' value='<?php if(isset($tablenumname)&&$tablenumname!='')echo $tablenumname;else echo $tb['T'.$i]['tablename']; ?>'>
										<input type='hidden' name='inittable' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
										<input type='hidden' name='consecnumber' value='<?php echo $consecnumber; ?>'>
										<input type='hidden' name='state' value='<?php echo $state; ?>'>
									</div>
									<div id='amt'><?php echo "<span>".$saleamt."</span>"; ?></div>
									<div id='persons'><?php if(intval($person)!='')echo $person.'位'; ?></div>
									<div id='createdatetime'><span id='val'><?php echo $time; ?></span><input type='hidden' name='createdatetime' value='<?php echo substr($createdatetime,4,2).'/'.substr($createdatetime,6,2).' '.substr($createdatetime,8,2).':'.substr($createdatetime,10,2); ?>'><input type='hidden' name='bizdate' value='<?php echo $bizdate; ?>'></div>
								</button>
							<?php
							}
							else{
							}
						}
						echo '</div>';
					}
					echo '</div>';
				}
				?>
			</div>
			<div class='funcmap' style='width:calc((100% - 2em) / 6);height:calc(100% - 1em);float:left;margin:0;padding:0;position: absolute;right: 1em;bottom: .5em;'>
				<?php //2020/10/20
				//echo "<div style='width:100%;'><input type='text' style='width:100%;height:30px;font-size:20px;border:0;background-color: transparent;text-align:right;font-family: Consolas,Microsoft JhengHei,sans-serif;' data-id='time' value='";
				//echo '尚有'.(intval($totaltable)-intval($ordertable)).$tb['TA']['unit'].' '.date('H:i:s');
				//echo "' readonly></div>";
				?>
				<div style='width:100%;'><input type='text' style='width:100%;height:30px;font-size:20px;border:0;background-color: transparent;text-align:left;font-family: Consolas,Microsoft JhengHei,sans-serif;' data-id='remaining' value='<?php echo '尚有'.(intval($totaltable)-intval($ordertable)).$tb['TA']['unit']; ?>' readonly></div>
				<div style='width:100%;'><input type='text' style='width:100%;height:30px;font-size:20px;border:0;background-color: transparent;text-align:right;font-family: Consolas,Microsoft JhengHei,sans-serif;position:absolute;top:0;right:0;' data-id='time' value='<?php echo date('H:i:s'); ?>' readonly></div>
				<?php
				for($i=0;$i<9;$i++){
					if($i==0){
						?>
						<div style='width:100%;height:calc(100% / 6 - 10px);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button class='table' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?>>
								<div id='QTYlabel'><?php echo $buttons1['name']['listtype2']; ?><input type='hidden' name='tabnum' value='outside'></div>
								<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
							</button>
						</div>
						<?php
					}
					else if($i==1){
						?>
						<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 60%;'>
							<button id='tableclear' style='background-color:#898989;color:#ffffff;'><div>清理桌面</div></button>
						</div>
						<?php
					}
					else if($i==2){
						?>
						<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 50%;'>						<!-- <button id='AE' style='background-color:#898989;color:#ffffff;' <?php if(isset($initsetting['init']['moneycost'])&&$initsetting['init']['moneycost']==1&&$timeini['time']['isclose']!='0'&&!isset($_GET['submachine']));else echo 'disabled'; ?>><div>支出費用</div></button> -->
							<button id='cashdrawer' style='background-color:#898989;color:#ffffff;' <?php if(!isset($_GET['submachine']));else echo 'disabled'; ?>><div>開錢櫃</div></button>
						</div>
						<?php
					}
					else if($i==3){
						?>
						<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 40%;'>						<button id='tablesplit' style='background-color:#898989;color:#ffffff;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><div>拆桌</div></button>
						</div>
						<?php
					}
					else if($i==4){
						?>
						<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 30%;'>
							<button id='combine' style='background-color:#898989;color:#ffffff;' <?php if($timeini['time']['isclose']!='0'&&!isset($_GET['submachine']));else echo 'disabled'; ?>><div>合併結帳</div></button>
						</div>
						<?php
					}
					else if($i==5){
						?>
						<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 20%;'>
							<button id='tablecombine' style='background-color:#898989;color:#ffffff;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><div>併桌</div></button>
							<!-- <button id='cashdrawer'><div>錢櫃</div></button> -->
						</div>
						<?php
					}
					else if($i==6){
						?>
						<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 10%;'>
							<button id='changetable' style='background-color:#898989;color:#ffffff;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><div>換桌</div></button>
						</div>
						<?php
					}
					else if($i==7){
						?>
						<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 0;'>
							<button id="conexitsys" style='background-color:#898989;color:#ffffff;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'disabled'; ?>>
								<div>離開系統</div>
							</button>
						</div>
						<?php
					}
					else if($i==8){
						?>
						<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 70%;'>
							<button id='funbox' style='background-color:#898989;color:#ffffff;'><div>功能區</div></button>
						</div>
						<?php
					}
					else{
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div class='changetable' title='換桌' style='overflow:hidden;'>
			<input type='hidden' id='c1' value='empty'>
			<input type='hidden' id='c1consecnumber'>
			<input type='hidden' id='c2' value='empty'>
			<input type='hidden' id='c2consecnumber'>
			<div class='tablemap' style='width:calc(500% / 6 - 1em);height:calc(100% - 1em);float:left;margin:.5em;padding:0;position: relative;'>
				<?php
				//if(isset($tb['TA']['page'])&&intval($tb['TA']['page'])>1){
					$startindex=1;
					$labelbox="<div id='butbox' style='width:100%;overflow:hidden;position: absolute;top:0;'>";
					for($page=1;$page<=$tb['TA']['page'];$page++){
						$labelbox.='<button id="page'.$page.'button" style="width:90px;height:100%;float:left;" class="w3-bar-item w3-button ';if($page==1)$labelbox.='focus';$labelbox.='" onclick="openCity(\'changetable\',\'page'.$page.'\')">';
						if(isset($buttons1['name']['controlfloor'.$page])){
							$labelbox.=$buttons1['name']['controlfloor'.$page];
						}
						else{
							$labelbox.=$page.'樓';
						}
						$labelbox.='</button>';
						echo '<div id="page'.$page.'" class="w3-container changetableItem" style="height:calc(100% - 1em - 30px);overflow:hidden;position: relative;top:50px;';if($page==1)echo 'display:block;';else echo 'display:none;';echo '">';
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
								?>
									<button class='chtable'>
										<div id='tablenumber'><?php 
											if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
										?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
										<input type='hidden' name='consecnumber' value=''></div>
									</button>
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
				/*}
				else{
					echo '<div style="width:100%;height:100%;">';
					for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
						echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
						if($tb['T'.$i]['tablename']==''){
						}
						else{
							if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
							?>
								<button class='chtable'>
									<div id='tablenumber'><?php 
										if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
									?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
									<input type='hidden' name='consecnumber' value=''></div>
								</button>
							<?php
							}
							else{
							}
						}
						echo '</div>';
					}
					echo '</div>';
				}*/
				?>
			</div>
			<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
				<?php
				for($i=0;$i<6;$i++){
					if($i==0){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
								<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
								<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
							</button>
						</div>
						<?php
					}
					else if($i==4){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button id='change' style='text-align:center;background-color:#898989;color:#ffffff;' disabled>
								<span id='c1t' style='font-size:2vw;'></span>
								<input type='hidden' name='c1t'>
								<span style='margin:0 5px;'>換</span>
								<span id='c2t' style='font-size:2vw;'></span>
								<input type='hidden' name='c2t'>
							</button>
						</div>
						<?php
					}
					else if($i==5){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button id="return" style='background-color:#898989;color:#ffffff;'>
								<div>返回</div>
							</button>
						</div>
						<?php
					}
					else{
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div class='conexitsys' style='text-align:center;' title='系統訊息'>
			<div id="name1">離開系統?</div>
			<br>
			<button class="yes" value="確認" style='width:105px;height:70px;'><div id='name1'>確認</div></button>
			<button class="no" value="取消" style='width:105px;height:70px;'><div id='name1'>取消</div></button>
		</div>
		<div class='funbox' title='功能區'>
			<?php
			if(!isset($_GET['submachine'])){
			?>
			<button id='open' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isopen'])&&$timeini['time']['isopen']=='1')echo '';else echo 'disabled'; ?>><div id='name1' style='font-weight:bold;'>開班</div></button>
			<button id='close' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isclose'])&&$timeini['time']['isclose']=='1')echo '';else echo 'disabled'; ?>><div id='name1' style='font-weight:bold;'>交班</div></button>
			<!-- <button id='view' value="未結帳單" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['listfun3']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['listfun3']."</div>";else; ?></button> -->
			<!-- <button id='gotable' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isclose'])&&$timeini['time']['isclose']=='1')echo '';else echo 'disabled'; ?>><div style='font-weight:bold;'>帶位</div></button> -->
			<?php if(isset($timeini['time']['controlhinttime'])&&$timeini['time']['controlhinttime']=='1') { ?>
			<button id='tablehint' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div id='name1' style='font-weight:bold;'>時段提示</div></button>
			<?php } ?>
			<button id='salelist' value="瀏覽帳單" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun27']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['billfun27']."</div>";else; ?></button>
			<button id='voidsale' value="帳單作廢" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun29']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;>".$buttons2['name']['billfun29']."</div>";else; ?></button>
			<!-- <button id='notyet' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>未結帳單</div></button> -->
			<button id='kvm' value="廚房控單" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['kvm'])&&$initsetting['init']['kvm']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['kvm']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['kvm']."</div>";else; ?></button>
			<button id='AE' value="其他收/支" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isclose'])&&$timeini['time']['isclose']=='1'&&isset($initsetting['init']['moneycost'])&&$initsetting['init']['moneycost']=='1')echo '';else echo 'disabled'; ?>><div id='name1' style='font-weight:bold;'><?php if($buttons1!='-1'&&isset($buttons1['name']['billfun24']))echo $buttons1['name']['billfun24'];else echo '其他收/支'; ?></div></button>
			<button id='punch' value="員工打卡" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['openpunch'])&&$initsetting['init']['openpunch']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun31']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['billfun31']."</div>";else; ?></button>
			<button id='historypaper' value="列印報表" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['historypaper'])&&$initsetting['init']['historypaper']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun32']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['billfun32']."</div>";else; ?></button>
			<button id='logout' value="切換人員" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['openindex'])&&$initsetting['init']['openindex']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun23']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['billfun23']."</div>";else; ?></button>
			<button id='editpunch' value="打卡紀錄" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['openpunch'])&&$initsetting['init']['openpunch']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['editpunch']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['editpunch']."</div>";else; ?></button>
			<button id='printchange' value="其他功能" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['printchange']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['printchange']."</div>";else; ?></button>
			<button id='computemoney' value="統計錢櫃金額" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['opencomputemoney'])&&$initsetting['init']['opencomputemoney']=='0')echo 'disabled';else; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['computemoney']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['computemoney']."</div>";else; ?></button>
			<?php
			}
			else{
			?>
			<button id='view' value="未結帳單" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['listfun3']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['listfun3']."</div>";else; ?></button>
			<button id='voidsale' value="帳單作廢" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun29']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;>".$buttons2['name']['billfun29']."</div>";else; ?></button>
			<button id='kvm' value="廚房控單" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['kvm'])&&$initsetting['init']['kvm']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['kvm']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['kvm']."</div>";else; ?></button>
			<button id='punch' value="員工打卡" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['openpunch'])&&$initsetting['init']['openpunch']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun31']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['billfun31']."</div>";else; ?></button>
			<button id='historypaper' value="列印報表" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['historypaper'])&&$initsetting['init']['historypaper']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun32']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['billfun32']."</div>";else; ?></button>
			<button id='logout' value="切換人員" style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($initsetting['init']['openindex'])&&$initsetting['init']['openindex']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['billfun23']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2' style='font-weight:bold;'>".$buttons2['name']['billfun23']."</div>";else; ?></button>
			<?php
			}
			?>
			<button id='return' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div id='name1' style='font-weight:bold;'>返回</div></button>
		</div>
		<div class='combine' title='合併結帳' style='overflow:hidden;'>
			<div class='tablemap' style='width:calc(500% / 6 - 1em);height:calc(100% - 1em);float:left;margin:.5em;padding:0;position: relative;'>
				<?php
				//if(isset($tb['TA']['page'])&&intval($tb['TA']['page'])>1){
					$startindex=1;
					$labelbox="<div id='butbox' style='width:100%;overflow:hidden;position: absolute;top:0;'>";
					for($page=1;$page<=$tb['TA']['page'];$page++){
						$labelbox.='<button id="page'.$page.'button" style="width:90px;height:100%;float:left;" class="w3-bar-item w3-button ';if($page==1)$labelbox.='focus';$labelbox.='" onclick="openCity(\'combine\',\'page'.$page.'\')">';
						if(isset($buttons1['name']['controlfloor'.$page])){
							$labelbox.=$buttons1['name']['controlfloor'.$page];
						}
						else{
							$labelbox.=$page.'樓';
						}
						$labelbox.='</button>';
						echo '<div id="page'.$page.'" class="w3-container combineItem" style="height:calc(100% - 1em - 30px);overflow:hidden;position: relative;top:50px;';if($page==1)echo 'display:block;';else echo 'display:none;';echo '">';
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
							echo '<div id="t" style="width:calc(100% / '.$pagecol.');height:calc(100% / '.$pagerow.');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
							if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
							?>
								<button class='chtable'>
									<div id='tablenumber'><?php 
										if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
									?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
									<input type='hidden' name='consecnumber' value=''></div>
									<div id='checkbox'></div>
								</button>
							<?php
							}
							else{
							}
							echo '</div>';
						}
						$startindex=$i;
						
						echo '</div>';
					}
					$labelbox.="</div>";
					echo $labelbox;
				/*}
				else{
					echo '<div id="page" style="width:100%;height:100%;">';
					for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
						echo '<div id="t" style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
						if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
						?>
							<button class='chtable'>
								<div id='tablenumber'><?php 
									if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
								?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
								<input type='hidden' name='consecnumber' value=''></div>
								<div id='checkbox'></div>
							</button>
						<?php
						}
						else{
						}
						echo '</div>';
					}
					echo '</div>';
				}*/
				?>
			</div>
			<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
				<?php
				for($i=0;$i<6;$i++){
					if($i==0){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
								<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
								<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
							</button>
						</div>
						<?php
					}
					else if($i==4){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button id='sale' style='text-align:center;background-color:#898989;color:#ffffff;' disabled>
								<div style='margin:0 5px;'>合併結帳</div>
							</button>
						</div>
						<?php
					}
					else if($i==5){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button id="return" style='background-color:#898989;color:#ffffff;'>
								<div>返回</div>
							</button>
						</div>
						<?php
					}
					else{
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div class='tablecombine' title='併桌' style='overflow:hidden;'>
			<div class='tablemap' style='width:calc(500% / 6 - 1em);height:calc(100% - 1em);float:left;margin:.5em;padding:0;position: relative;'>
				<?php
				//if(isset($tb['TA']['page'])&&intval($tb['TA']['page'])>1){
					$startindex=1;
					$labelbox="<div id='butbox' style='width:100%;overflow:hidden;position: absolute;top:0;'>";
					for($page=1;$page<=$tb['TA']['page'];$page++){
						$labelbox.='<button id="page'.$page.'button" style="width:90px;height:100%;float:left;" class="w3-bar-item w3-button ';if($page==1)$labelbox.='focus';$labelbox.='" onclick="openCity(\'tablecombine\',\'page'.$page.'\')">';
						if(isset($buttons1['name']['controlfloor'.$page])){
							$labelbox.=$buttons1['name']['controlfloor'.$page];
						}
						else{
							$labelbox.=$page.'樓';
						}
						$labelbox.='</button>';
						echo '<div id="page'.$page.'" class="w3-container tablecombineItem" style="height:calc(100% - 1em - 30px);overflow:hidden;position: relative;top:50px;';if($page==1)echo 'display:block;';else echo 'display:none;';echo '">';
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
							if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
							?>
								<button class='chtable'>
									<div id='tablenumber'><?php 
										if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
									?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
									<input type='hidden' name='consecnumber' value=''></div>
									<div id='checkbox'></div>
								</button>
							<?php
							}
							else{
							}
							echo '</div>';
						}
						$startindex=$i;
						
						echo '</div>';
					}
					$labelbox.="</div>";
					echo $labelbox;
				/*}
				else{
					echo '<div style="width:100%;height:100%;">';
					for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
						echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
						if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
						?>
							<button class='chtable'>
								<div id='tablenumber'><?php 
									if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
								?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
								<input type='hidden' name='consecnumber' value=''></div>
								<div id='checkbox'></div>
							</button>
						<?php
						}
						else{
						}
						echo '</div>';
					}
					echo '</div>';
				}*/
				?>
			</div>
			<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
				<?php
				for($i=0;$i<6;$i++){
					if($i==0){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
								<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
								<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
							</button>
						</div>
						<?php
					}
					else if($i==4){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button id='sale' style='text-align:center;background-color:#898989;color:#ffffff;' disabled>
								<div style='margin:0 5px;'>併桌</div>
							</button>
						</div>
						<?php
					}
					else if($i==5){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button id="return" style='background-color:#898989;color:#ffffff;'>
								<div>返回</div>
							</button>
						</div>
						<?php
					}
					else{
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div class='tablesplit' title='拆桌' style='overflow:hidden;'>
			<div class='tablemap' style='width:calc(500% / 6 - 1em);height:calc(100% - 1em);float:left;margin:.5em;padding:0;position: relative;'>
				<?php
				//if(isset($tb['TA']['page'])&&intval($tb['TA']['page'])>1){
					$startindex=1;
					$labelbox="<div id='butbox' style='width:100%;overflow:hidden;position: absolute;top:0;'>";
					for($page=1;$page<=$tb['TA']['page'];$page++){
						$labelbox.='<button id="page'.$page.'button" style="width:90px;height:100%;float:left;" class="w3-bar-item w3-button ';if($page==1)$labelbox.='focus';$labelbox.='" onclick="openCity(\'tablesplit\',\'page'.$page.'\')">';
						if(isset($buttons1['name']['controlfloor'.$page])){
							$labelbox.=$buttons1['name']['controlfloor'.$page];
						}
						else{
							$labelbox.=$page.'樓';
						}
						$labelbox.='</button>';
						echo '<div id="page'.$page.'" class="w3-container tablesplitItem" style="height:calc(100% - 1em - 30px);overflow:hidden;position: relative;top:50px;';if($page==1)echo 'display:block;';else echo 'display:none;';echo '">';
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
							if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
							?>
								<button class='chtable'>
									<div id='tablenumber'><?php 
										if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
										?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
										<input type='hidden' name='consecnumber' value=''></div>
									<div id='checkbox'></div>
								</button>
							<?php
							}
							else{
							}
							echo '</div>';
						}
						$startindex=$i;
						
						echo '</div>';
					}
					$labelbox.="</div>";
					echo $labelbox;
				/*}
				else{
					echo '<div style="width:100%;height:100%;">';
					for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
						echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
						if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
						?>
							<button class='chtable'>
								<div id='tablenumber'><?php 
									if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; 
									?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'>
									<input type='hidden' name='consecnumber' value=''></div>
								<div id='checkbox'></div>
							</button>
						<?php
						}
						else{
						}
						echo '</div>';
					}
					echo '</div>';
				}*/
				?>
			</div>
			<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
				<?php
				for($i=0;$i<6;$i++){
					if($i==0){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
								<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
								<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
							</button>
						</div>
						<?php
					}
					else if($i==5){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button id="return" style='background-color:#898989;color:#ffffff;'>
								<div>返回</div>
							</button>
						</div>
						<?php
					}
					else{
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div class='checktablecombine' title='併桌確認視窗'>
			<div id='text' style='width:100%;height:calc(100% - 48px);'>
			</div>
			<button id='cancel' style='font-size:30px;margin:1px;float:right;'><div>取消</div></button>
			<button id='sale' style='font-size:30px;margin:1px;float:right;'><div>確認併桌</div></button>
		</div>
		<div class='checkcombine' title='合併結帳確認視窗'>
			<div id='text' style='width:100%;height:calc(100% - 48px);'>
			</div>
			<button id='cancel' style='font-size:30px;margin:1px;float:right;'><div>取消</div></button>
			<button id='sale' style='font-size:30px;margin:1px;float:right;'><div>確認合併</div></button>
		</div>
		<div class='checktablesplit' title='拆桌確認視窗'>
			<div id='text' style='width:100%;height:calc(100% - 48px);'>
			</div>
			<button id='cancel' style='font-size:30px;margin:1px;float:right;'><div>取消</div></button>
			<button id='sale' style='font-size:30px;margin:1px;float:right;'><div>確認拆桌</div></button>
		</div>
		<div class='checktableclear' title='清理桌面確認視窗'>
			<div id='text' style='width:100%;height:calc(100% - 48px);'>
			</div>
			<button id='cancel' style='font-size:30px;margin:1px;float:right;'><div>取消</div></button>
			<button id='sale' style='font-size:30px;margin:1px;float:right;'><div>確認清理桌面</div></button>
		</div>
		<div class='splittablelist' title='拆桌帳單'>
		</div>
		<div class='tableclear' title='清理桌面' style='overflow:hidden;'>
			<div class='tablemap' style='width:calc(500% / 6 - 1em);height:calc(100% - 1em);float:left;margin:.5em;padding:0;position: relative;'>
				<?php
				//if(isset($tb['TA']['page'])&&intval($tb['TA']['page'])>1){
					$startindex=1;
					$labelbox="<div id='butbox' style='width:100%;overflow:hidden;position: absolute;top:0;'>";
					for($page=1;$page<=$tb['TA']['page'];$page++){
						$labelbox.='<button id="page'.$page.'button" style="width:90px;height:100%;float:left;" class="w3-bar-item w3-button ';if($page==1)$labelbox.='focus';$labelbox.='" onclick="openCity(\'tableclear\',\'page'.$page.'\')">';
						if(isset($buttons1['name']['controlfloor'.$page])){
							$labelbox.=$buttons1['name']['controlfloor'.$page];
						}
						else{
							$labelbox.=$page.'樓';
						}
						$labelbox.='</button>';
						echo '<div id="page'.$page.'" class="w3-container tableclearItem" style="height:calc(100% - 1em - 30px);overflow:hidden;position: relative;top:50px;';if($page==1)echo 'display:block;';else echo 'display:none;';echo '">';
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
							if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
							?>
								<button class='chtable'>
									<div id='tablenumber'><?php if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value=''><input type='hidden' name='state' value=''><input type='hidden' name='split' value=''></div>
									<div id='checkbox'></div>
								</button>
							<?php
							}
							else{
							}
							echo '</div>';
						}
						$startindex=$i;
						
						echo '</div>';
					}
					$labelbox.="</div>";
					echo $labelbox;
				/*}
				else{
					echo '<div style="width:100%;height:100%;">';
					for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
						echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
						if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
						?>
							<button class='chtable'>
								<div id='tablenumber'><?php if(isset($tb['Tname'][$tb['T'.$i]['tablename']]))echo $tb['Tname'][$tb['T'.$i]['tablename']];else echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value=''><input type='hidden' name='state' value=''><input type='hidden' name='split' value=''></div>
								<div id='checkbox'></div>
							</button>
						<?php
						}
						else{
						}
						echo '</div>';
					}
					echo '</div>';
				}*/
				?>
			</div>
			<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
				<?php
				for($i=0;$i<6;$i++){
					if($i==0){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
								<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
								<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
							</button>
						</div>
						<?php
					}
					else if($i==5){
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<button id="return" style='background-color:#898989;color:#ffffff;'>
								<div>返回</div>
							</button>
						</div>
						<?php
					}
					else{
						?>
						<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
		if(isset($initsetting['init']['controlhinttime'])&&$initsetting['init']['controlhinttime']=='1'){
		?>
			<div class='tablehint' title='時段提示'>
				<div id='timelist' style='width:100%;height:calc(85% - 1px);max-height:calc(85% - 1px);float:left;margin:0 0 1px 0;padding:0;overflow-y:auto;'>
				</div>
				<div style='width:100%;height:calc(15% - 1px);float:left;margin:1px 0 0 0;padding:0;'>
					<button id='cancel' style='width:100px;height:100%;float:right;' value='取消'>取消</button>
				</div>
			</div>
		<?php
		}
		else{
		}
		?>
		<!-- <div class='consetchange' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;z-index:3;' title='<?php if($interface1!='-1')echo $interface1['name']['49'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['49'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['49'];else;?>'>
			<div class='numbox' style='width:18%;height:100%;float:left;'>
				<button value='清除' style='width:100%;height:calc(100% / 13);margin:0 0 20px 0;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['16']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['16']."</div>";else; ?></button>
				<input type='button' value='1' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='2' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='3' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='4' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='5' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='6' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='7' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='8' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='9' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='0' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='.' style='width:100%;height:calc(100% / 13);'>
			</div>
			<div style='width:calc(82% - 2px);height:100%;float:left;margin-left:2px;'>
				<div style='float:left;width:17%;'>
					<?php if($interface1!='-1')echo "<div id='name1' style='font-size:17px;'>".$interface1['name']['50']."</div>"; ?>
					<?php if($interface2!='-1')echo "<div id='name2' style='font-size:13px;'>".$interface2['name']['50']."</div>";else; ?>
				</div>
				<?php echo $initsetting['init']['frontunit']; ?><input type='text' name='view' style='width:calc(50% - 12px);margin:5px;text-align:right;float:left;' value='<?php echo $machinedata['basic']['change']; ?>' onfocus><div style='float:left;height:35px;line-height:35px;'><?php echo $initsetting['init']['unit']; ?></div>
				<button id='settingchange' style='width:105px;height:55px;margin:10px calc((100% - 105px) / 2);background-color:#D5DC75;color:#000000;'>
					<?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['48']."</div>"; ?>
					<?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['48']."</div>";else; ?>
				</button>
			</div>
		</div> -->
	</div>
	<?php
	}
	else{
	}
	?>
	<div class='order' id='order'>
		<?php
		if(isset($initsetting['init']['usenodejs'])&&$initsetting['init']['usenodejs']=='1'&&file_exists('../nodejs/node_modules/socket.io-client/dist/socket.io.js')){//0>>遵循舊有流程1>>套用nodejs流程
		?>
		<div class='nodejssetting'>
			<input type='hidden' id='nodejsip' value='<?php if(isset($basic)&&isset($basic['nodejsaddress'])&&isset($basic['nodejsaddress']['nodejsip']))echo $basic['nodejsaddress']['nodejsip'];else echo '127.0.0.1'; ?>'>
			<input type='hidden' id='nodejsport' value='<?php if(isset($basic)&&isset($basic['nodejsaddress'])&&isset($basic['nodejsaddress']['nodejsport']))echo $basic['nodejsaddress']['nodejsport'];else echo '3700'; ?>'>
			<input type='hidden' id='serveraddress' value='<?php if(isset($basic)&&isset($basic['nodejsaddress'])&&isset($basic['nodejsaddress']['serveraddress']))echo $basic['nodejsaddress']['serveraddress'];else echo 'api.tableplus.com.tw'; ?>'>
			<input type='hidden' id='serverport' value='<?php if(isset($basic)&&isset($basic['nodejsaddress'])&&isset($basic['nodejsaddress']['serverport']))echo $basic['nodejsaddress']['serverport'];else echo '3700'; ?>'>
		</div>
		<?php
		}
		else{
		}
		if(isset($initsetting['init']['directlinepay'])&&$initsetting['init']['directlinepay']=='1'){//0>>不使用1>>使用直接linepay付款串接
		?>
		<div class='directlinepay' style="display:none;">
			<input type='hidden' id='url' value="<?php echo $basic['directlinepay']['url']; ?>">
			<input type='hidden' id='channelid' value="<?php echo $basic['directlinepay']['channelid']; ?>">
			<input type='hidden' id='channelsecret' value="<?php echo $basic['directlinepay']['channelsecret']; ?>">
		</div>
		<?php
		}
		else{
		}
		if(isset($initsetting['init']['jkos'])&&$initsetting['init']['jkos']=='1'){//0>>不使用1>>使用街口付款串接
		?>
		<div class='jkos' style="display:none;">
			<input type='hidden' id='url' value="<?php echo $basic['jkos']['url']; ?>">
			<input type='hidden' id='systemname' value="<?php echo $basic['jkos']['systemname']; ?>">
			<input type='hidden' id='merchantkey' value="<?php echo $basic['jkos']['merchantkey']; ?>">
			<input type='hidden' id='merchantid' value="<?php echo $basic['jkos']['merchantid']; ?>">
		</div>
		<?php
		}
		else{
		}
		if(isset($initsetting['init']['pxpayplus'])&&$initsetting['init']['pxpayplus']=='1'){//0>>不使用1>>使用全支付付款串接
		?>
		<div class='pxpayplus' style="display:none;">
			<input type='hidden' id='url' value="<?php echo $basic['pxpayplus']['url']; ?>">
			<input type='hidden' id='secrectkey' value="<?php echo $basic['pxpayplus']['secrectkey']; ?>">
			<input type='hidden' id='merchantcode' value="<?php echo $basic['pxpayplus']['merchantcode']; ?>">
			<input type='hidden' id='pxstorecode' value="<?php echo $basic['pxpayplus']['pxstorecode']; ?>">
			<input type='hidden' id='item_json' value="">
		</div>
		<?php
		}
		else{
		}
		if(file_exists('../database/intellasetup.ini')){
			$intellasetup=parse_ini_file('../database/intellasetup.ini',true);
		}
		else{
		}
		if(isset($intellasetup['intella']['qrcodetimeout'])&&$intellasetup['intella']['qrcodetimeout']!=''){
		}
		else{
			$intellasetup['intella']['qrcodetimeout']='5';
		}
		?>
		<div class='intella' style='display:none;'>
			<input type='hidden' id='qrcodetimeout' value='<?php echo $intellasetup['intella']['qrcodetimeout']; ?>'>
		</div>
		<?php
		if(isset($basic['basic']['sendinvlocation'])&&$basic['basic']['sendinvlocation']=='3'&&isset($basic['zdninv']['url'])&&$basic['zdninv']['url']!=''){//中鼎發票
		?>
		<div class='zdn' style='display:none;'>
			<input type='hidden' id='url' value='<?php echo $basic['zdninv']['url']; ?>'>
			<input type='hidden' id='id' value='<?php echo $basic['zdninv']['id']; ?>'>
			<input type='hidden' id='psw' value='<?php if(isset($basic['zdninv']['psw']))echo $basic['zdninv']['psw']; ?>'>
			<input type='hidden' id='token' value=''>
		</div>
		<?php
		}
		else{
		}
		if(isset($initsetting['nidin']['usenidin'])&&$initsetting['nidin']['usenidin']==1){
		?>
		<div class='nidin' style='display:none;'>
			<input type='hidden' id='usenidin' value='<?php echo $initsetting['nidin']['usenidin']; ?>'>
			<input type='hidden' id='alwayscheck' value='<?php if(isset($initsetting['nidin']['alwayscheck']))echo $initsetting['nidin']['alwayscheck'];else echo "1"; ?>'>
			<input type='hidden' id='autoaccept' value='<?php if(isset($initsetting['nidin']['autoaccept']))echo $initsetting['nidin']['autoaccept'];else echo "0"; ?>'>
		</div>
		<?php
		}
		else{
		}
		?>
		<div class='companydata' style='display:none;'>
			<input type='hidden' id='company' value='<?php echo $basic['basic']['company']; ?>'><!-- 公司編號 -->
			<input type='hidden' id='story' value='<?php echo $basic['basic']['story']; ?>'><!-- 門市編號 -->
			<input type='hidden' id='companyname' value='<?php echo $basic['basic']['Name']; ?>'><!-- 公司名稱 -->
			<input type='hidden' id='storyname' value='<?php echo $basic['basic']['storyname']; ?>'><!-- 門市名稱 -->
			<input type='hidden' name='bannumber' value='<?php echo $basic['basic']['Identifier']; ?>'><!-- 統一編號 -->
			<input type='hidden' id='terminalnumber' value='<?php if(isset($_GET['submachine'])&&$_GET['submachine']!='')echo $_GET['submachine'];else if(isset($_GET['machine'])&&$_GET['machine']!='')echo $_GET['machine'];else echo 'm1'; ?>'><!-- 機號 -->
			<input type='hidden' id='isopen' value='<?php echo $timeini['time']['isopen']; ?>'>
			<input type='hidden' id='isclose' value='<?php echo $timeini['time']['isclose']; ?>'>
			<input type='hidden' id='ispad' value='<?php if(isset($_GET['submachine']))echo 'submachine';else echo 'mainmachine'; ?>'>
			<input type='hidden' id='ismobile' value='<?php
			if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"])){
				echo '1';
			}
			else{
				echo '0';
			}
			?>'>
			<input type='hidden' id='initpower' value='<?php if(!isset($initsetting['init']['initpower'])||$initsetting['init']['initpower']=='')echo '0';else echo $initsetting['init']['initpower']; ?>'>
			<?php if(isset($_GET["submachine"]))echo "<input type='hidden' id='submachine'>";else; ?>
			<input type='hidden' id='posdvrkey' value='<?php if(isset($machinedata['posdvr']['key']))echo $machinedata['posdvr']['key']; ?>'>
			<?php if(isset($_GET['v']))echo "<input type='hidden' id='voidpw' value='".$_GET['v']."'>";else; ?>
			<?php if(isset($_GET['p']))echo "<input type='hidden' id='paperpw' value='".$_GET['p']."'>";else; ?>
			<?php if(isset($_GET['u']))echo "<input type='hidden' id='punchpw' value='".$_GET['u']."'>";else; ?>
			<?php if(isset($_GET['r']))echo "<input type='hidden' id='reprintpw' value='".$_GET['r']."'>";else; ?>
			<input type='hidden' id='settime' value='<?php echo $initsetting['init']['settime']; ?>'>
		</div>
		<?php
		include_once './lib/js/initial.parameters.php';
		parameter($initsetting,$buttons1,$buttons2,$itemdis,$print,$member);
		if(isset($_GET['listtype'])){
			$listtype=$_GET['listtype'];
		}
		else{
			if(isset($_GET['submachine'])){
				$listtype=$initsetting['init']['subordertype'];
			}
			else{
				$listtype=$initsetting['init']['ordertype'];
			}
		}
		?>
		<div class='parameters' style='display:none;'>
			<input type='hidden' id='typeno' value=''>
			<input type='hidden' id='type' value=''>
			<input type='hidden' id='no' value=''>
			<input type='hidden' id='name' value=''>
			<input type='hidden' id='isgroup' value=''><!-- 是否為套餐(組合) -->
			<input type='hidden' id='childtype' value=''><!-- 可組合之類別集合 -->
			<input type='hidden' id='msize' value=''><!-- 最多預留6個 -->
			<input type='hidden' id='mname1[]' value=''><!-- 顯示名稱1 -->
			<input type='hidden' id='mname2[]' value=''><!-- 顯示名稱1 -->
			<input type='hidden' id='money[]' value=''>
			<input type='hidden' id='getpointtype[]' value=''><!-- 兌換點數類別 -->
			<input type='hidden' id='getpoint[]' value=''><!-- 固定點數 -->
			<input type='hidden' id='mname1[]' value=''><!-- 顯示名稱2 -->
			<input type='hidden' id='mname2[]' value=''><!-- 顯示名稱2 -->
			<input type='hidden' id='money[]' value=''>
			<input type='hidden' id='getpointtype[]' value=''><!-- 兌換點數類別 -->
			<input type='hidden' id='getpoint[]' value=''><!-- 固定點數 -->
			<input type='hidden' id='mname1[]' value=''><!-- 顯示名稱3 -->
			<input type='hidden' id='mname2[]' value=''><!-- 顯示名稱3 -->
			<input type='hidden' id='money[]' value=''>
			<input type='hidden' id='getpointtype[]' value=''><!-- 兌換點數類別 -->
			<input type='hidden' id='getpoint[]' value=''><!-- 固定點數 -->
			<input type='hidden' id='mname1[]' value=''><!-- 顯示名稱4 -->
			<input type='hidden' id='mname2[]' value=''><!-- 顯示名稱4 -->
			<input type='hidden' id='money[]' value=''>
			<input type='hidden' id='getpointtype[]' value=''><!-- 兌換點數類別 -->
			<input type='hidden' id='getpoint[]' value=''><!-- 固定點數 -->
			<input type='hidden' id='mname1[]' value=''><!-- 顯示名稱5 -->
			<input type='hidden' id='mname2[]' value=''><!-- 顯示名稱5 -->
			<input type='hidden' id='money[]' value=''>
			<input type='hidden' id='getpointtype[]' value=''><!-- 兌換點數類別 -->
			<input type='hidden' id='getpoint[]' value=''><!-- 固定點數 -->
			<input type='hidden' id='mname1[]' value=''><!-- 顯示名稱6 -->
			<input type='hidden' id='mname2[]' value=''><!-- 顯示名稱6 -->
			<input type='hidden' id='money[]' value=''>
			<input type='hidden' id='getpointtype[]' value=''><!-- 兌換點數類別 -->
			<input type='hidden' id='getpoint[]' value=''><!-- 固定點數 -->
			<input type='hidden' id='taste1' value=''><!-- 備註與加料編號 -->
			<input type='hidden' id='taste1name' value=''><!-- 備註與加料名稱 -->
			<input type='hidden' id='taste2name' value=''><!-- 備註與加料名稱 -->
			<input type='hidden' id='taste1money' value=''><!-- 備註與加料加價 -->
			<input type='hidden' id='tastegroup' value=''><!-- 備註與加料群組編號 -->
			<input type='hidden' id='background' value=''><!-- 備註與加料底色 -->
			<input type='hidden' id='sale' value=''>
			<input type='hidden' id='salename' value=''>
		</div>
		<?php
		if(isset($initsetting['yunlincoins']['open'])&&$initsetting['yunlincoins']['open']=='1'){//0>>關閉yunlincoins串接1>>開啟yunlincoins串接
			if(file_exists('../database/yunlincoins.ini')){
				$yunlincoins=parse_ini_file('../database/yunlincoins.ini',true);
			}
			else{//預設轉換比
				$yunlincoins['yunlincoins']['coins']=10;
				$yunlincoins['yunlincoins']['tran']=1;
			}
		?>
		<div class='yunlincoinssetting'><!-- 雲林幣與儲值金轉換比 -->
			<input type='hidden' id='coins' value='<?php echo $yunlincoins['yunlincoins']['coins']; ?>'><!-- 雲林幣基數 -->
			<input type='hidden' id='tran' value='<?php echo $yunlincoins['yunlincoins']['tran']; ?>'><!-- 轉換儲值金 -->
		</div>
		<?php
		}
		else{
		}
		?>
		<div id="banner"><!-- banner -->
			<div id="type" class="" style='width:calc(168% / 5 - 1px);margin-right:1px;<?php if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']==1)echo 'visibility:hidden;'; ?>'>
				<input type='hidden' name='reservedatetime' value=''>
				<div id='listdata' style='height:100%;float:left;'>
				</div>
				<?php
				$temp=preg_split('/,/',$initsetting['init']['orderlocation']);
				for($i=0;$i<sizeof($temp);$i++){
					if($temp[$i]=='0'){
						if(sizeof($temp)==4){
							echo '<div style="width:100%;height:calc(100% - 2px);margin:1px;float:left;"></div>';
						}
						else{
							echo '<div style="width:130px;height:calc(100% - 2px);margin:1px;float:left;"></div>';
						}
					}
					else{
						switch($temp[$i]){
							case '1':
								if(isset($initsetting['init']['tabnum'])&&$initsetting['init']['tabnum']==1){
									echo '<input type="hidden" name="initabnum" value="1">';
								}
								else{
									echo '<input type="hidden" name="initabnum" value="0">';
								}
				?>
				<button id='button' class='inside' value="內用" style='<?php if(sizeof($temp)==4)echo "width:calc(100% / 4 - 2px);"; ?>' <?php if($listtype==1){echo 'disabled';}else{} ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listtype1']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listtype1']."</div>";else; ?></button>
				<?php
								break;
							case '2':
				?>
				<button id='button' class='come' value="外帶" style='<?php if(sizeof($temp)==4)echo "width:calc(100% / 4 - 2px);"; ?>' <?php if($listtype==2){echo 'disabled';}else{} ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listtype2']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listtype2']."</div>";else; ?></button>
				<?php
								break;
							case '3':
				?>
				<button id='button' class='outside' value="外送" style='<?php if(sizeof($temp)==4)echo "width:calc(100% / 4 - 2px);"; ?>' <?php if($listtype==3){echo 'disabled';}else{} ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listtype3']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listtype3']."</div>";else; ?></button>
				<?php
								break;
							case '4':
				?>
				<button id='button' class='bysale' value="自取" style='<?php if(sizeof($temp)==4)echo "width:calc(100% / 4 - 2px);"; ?>' <?php if($listtype==4){echo 'disabled';}else{} ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listtype4']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listtype4']."</div>";else; ?></button>
				<?php
								break;
						}
					}
				}
				?>
			</div>
			<div id="tablenumber" style="float:left;<?php if(isset($initsetting['init']['tabnum'])&&$initsetting['init']['tabnum']==1&&$listtype==1){echo 'width:calc(42% / 5 - 2px);height:100%;margin:0;';} ?>">
				<?php
				if(isset($initsetting['init']['tabnum'])&&$initsetting['init']['tabnum']==1&&$listtype==1){
				?>
				<div style="width:100%;height:50%;text-align:center;">
					<?php if($interface1!='-1'){
								echo $interface1['name']['tabnumtext'];
						  }
						  else{
								echo '桌號';
						   }
					?>
				</div>
				<div class="tabnumbox" style="width:100%;height:50%;border:1px solid #898989;text-align:center;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
					<?php
					if(isset($_GET['tabnum'])){
						echo $_GET['tabnum'];
					}
					else{
					}
					?>
				</div>
				<?php
				}
				else{
				}
				?>
			</div>
			<div id="detail" class="" style="<?php if(isset($initsetting['init']['tabnum'])&&$initsetting['init']['tabnum']==1&&$listtype==1)echo 'margin:0px;';else if(isset($initsetting['init']['useoinv'])&&$initsetting['init']['useoinv']=='1')echo 'margin-left:calc(45% / 5 - 20px);margin-right:0;';else echo 'margin-right:0;margin-left:calc(45% / 5 - 20px);'; ?>">
				<table style='float:left;'>
					<tr>
						<td><?php if($interface1!='-1')echo "<span id='name1'>".$interface1['name']['1']."</span>"; ?><?php if($interface1!='-1'&&$interface2!='-1')echo "/<span id='name2'>".$interface2['name']['1']."</span>";else if($interface1=='-1'&&$interface2!='-1')echo "<span id='name2'>".$interface2['name']['1']."</span>";else; ?></td><td><input type='text' class='viewtotal' id='viewtotal' value='0' style='background-color: #ffffff;color:#ff0000;text-align:right;margin-left:6px;' readonly></td>
					</tr>
					<tr>
						<td><?php if($interface1!='-1')echo "<span id='name1'>".$interface1['name']['2']."</span>"; ?><?php if($interface1!='-1'&&$interface2!='-1')echo "/<span id='name2'>".$interface2['name']['2']."</span>";else if($interface1=='-1'&&$interface2!='-1')echo "<span id='name2'>".$interface2['name']['2']."</span>";else; ?></td><td><input type='text' id='viewnumber' value='0' style='background-color: #ffffff;color:#ff0000;text-align:right;margin-left:6px;' readonly></td>
					</tr>
				</table>
				<?php
				if(!isset($initsetting['init']['useoinv'])||$initsetting['init']['useoinv']=='0'){
					if(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1'){
						if(intval(substr($timeini['time']['bizdate'],0,6))%2==1){
							if(intval(substr($timeini['time']['bizdate'],4,2))<9){
								$invdate=substr($timeini['time']['bizdate'],0,4).'0'.(intval(substr($timeini['time']['bizdate'],4,2))+1);
							}
							else{
								$invdate=substr($timeini['time']['bizdate'],0,4).(intval(substr($timeini['time']['bizdate'],4,2))+1);
							}
						}
						else{
							$invdate=substr($timeini['time']['bizdate'],0,6);
						}
						if(file_exists('../database/sale/'.$invdate.'/invdata_'.$invdate.'_'.$invmachine.'.db')){
							$conn=sqlconnect('../database/sale/'.$invdate,'invdata_'.$invdate.'_'.$invmachine.'.db','','','','sqlite');
							$sql='SELECT COUNT(*) AS notnum FROM number WHERE datetime!="'.(intval($invdate)-191100).'"';
							$not=sqlquery($conn,$sql,'sqlite');
							if(isset($not[0]['notnum'])&&$not[0]['notnum']!=0){
								$sql='UPDATE number SET state="0" WHERE datetime!="'.(intval($invdate)-191100).'"';
								sqlnoresponse($conn,$sql,'sqlite');
							}
							else{
							}
							$sql='SELECT COUNT(*) AS num FROM number WHERE company="'.$basic['basic']['company'].'" AND story="'.$basic['basic']['story'].'" AND state=1 AND datetime="'.(intval($invdate)-191100).'"';
							$invnum=sqlquery($conn,$sql,'sqlite');
							sqlclose($conn,'sqlite');
							?>
							<table style='float:left;'>
								<tr>
									<td><span id='name1'>剩餘<br>發票</span></td><td><input type='text' id='invnum' value='<?php echo $invnum[0]['num']; ?>' style='width:60px;background-color:#eeeeee;color:#ff0000;text-align:right;margin-left:6px;' readonly></td>
								</tr>
							</table>
							<?php
						}
						else{
							if(file_exists("../database/sale/EMinvdata.DB")){
							}
							else{
								include_once './lib/js/create.emptyDB.php';
								create("EMinvdata",'./lib/sql/','../database/sale/','../tool/');
							}
							if(file_exists("../database/sale/".$invdate)){
							}
							else{
								mkdir("../database/sale/".$invdate);
							}
							copy("../database/sale/EMinvdata.DB","../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db");
							?>
							<table style='float:left;'>
								<tr>
									<td><span id='name1'>剩餘<br>發票</span></td><td><input type='text' id='invnum' value='0' style='width:60px;background-color:#eeeeee;color:#ff0000;text-align:right;margin-left:6px;' readonly></td>
								</tr>
							</table>
							<?php
						}
					}
					else{
					}
				}
				else{
				?>
				<table style='float:left;'>
					<tr>
						<td>
							<div id='name1' style='text-align:center;width:100%;'>目前發票號</div>
						</td>
					</tr>
					<tr>
						<td>
							<input type='text' id='oinv' value='<?php 
							if(!isset($initsetting['init']['oinv'])||$initsetting['init']['oinv']=='0'||strlen($machinedata['basic']['startoinv'])!=10||$machinedata['basic']['startoinv']>=$machinedata['basic']['endoinv'])echo 'ERROR';
							else echo $machinedata['basic']['startoinv']; 
							?>' style='width:120px;background-color:<?php if(!isset($initsetting['init']['oinv'])||$initsetting['init']['oinv']=='0')echo '#eeeeee';else echo '#ffffff'; ?>;color:#ff0000;text-align:right;margin:0 3px;' readonly <?php if(!isset($initsetting['init']['oinv'])||$initsetting['init']['oinv']=='0')echo 'disabled';else; ?>>
							<input type='text' id='hid' value='' style='display:none;background-color: #ffffff;color:#ff0000;text-align:right;margin-left:6px;visibility:hidden;' readonly>
						</td>
					</tr>
				</table>
				<?php
				}
				?>
				<table id='tw' style='float:left;position: relative;display:block;height:100%;'><?php /*the last two attributes for sec-language*/ ?>
					<tbody style='display:block;height:100%;'><?php /*this tag for sec-language*/ ?>
						<tr style='display:block;height:100%;'><?php /*the style for sec-language*/ ?>
							<td style='display:block;height:100%;'><?php /*the style for sec-language*/ ?>
								<button id='view' value='未結清單' style='float:right;background-color:#FFA801;margin-right:2px;overflow:hidden;'><?php /*the last one attributes for sec-language*/ ?><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listfun3']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listfun3']."</div>";else; ?></button>
								<div id='point' style='width:20px;height:20px;position:absolute;top:0;right:0;border-radius:100%;color:#ffffff;font-size:18px;text-align:center;line-height:20px;'></div>
							</td>
						</tr>
					</tbody>
				</table>
				<table>
					<tr>
						<td style='text-align:center;'><?php if($interface1!='-1'&&isset($interface1['name']['salelisthint']))echo "<span id='name1'>".$interface1['name']['salelisthint']."</span>";else echo "<span id='name1'>帳單備註</span>"; ?></td>
					</tr>
					<tr>
						<td><input type="text" id='salelisthint' style='width:130px;' value='' readonly></td>
					</tr>
				</table>
			</div>
			<!-- <div style='height:100%;float:right;overflow:hidden;'>
				<button id='teamviewer' style='background-color:#FF4500;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun25']."</div>";if($buttons2!='-1')echo "<div id='name2' style='color:#ffffff;'>".$buttons2['name']['billfun25']."</div>";else; ?></button>
			</div>-->
			<!-- <div style='height:100%;float:right;overflow:hidden;'>
				<button id='updatemenu' style='background-color:#4282B5;'>
					<div id='name1'>UpdateMenu</div>
					<div id='name2' style='color:#ffffff;'>更新菜單</div>
				</button>
			</div> -->
			<div style='width:110px;border:0;float:right;font-size:13px;padding:0;position: absolute; right: 0;'><?php /*the last two attributes for sec-language*/ ?>
				<div data-id='username' style='text-align: right;padding: 0 2px 1px 0;white-space: nowrap;'>
					<?php
					if(isset($_GET['username'])){
						echo $_GET['usercode'].' '.$_GET['username'];
					}
					else{
					}
					?>
				</div>
				<div data-id='time' style='padding: 1px 2px 0 0;text-align: right;'>
					<?php 
					echo date('Y/m/d/');
					switch(date('N')){
						case 1:
							if($interface1!='-1'&&isset($interface1['name']['mon'])){
								echo $interface1['name']['mon'].' ';
							}
							else{
								echo '週一 ';
							}
							break;
						case 2:
							if($interface1!='-1'&&isset($interface1['name']['tue'])){
								echo $interface1['name']['tue'].' ';
							}
							else{
								echo '週二 ';
							}
							break;
						case 3:
							if($interface1!='-1'&&isset($interface1['name']['wed'])){
								echo $interface1['name']['wed'].' ';
							}
							else{
								echo '週三 ';
							}
							break;
						case 4:
							if($interface1!='-1'&&isset($interface1['name']['thu'])){
								echo $interface1['name']['thu'].' ';
							}
							else{
								echo '週四 ';
							}
							break;
						case 5:
							if($interface1!='-1'&&isset($interface1['name']['fri'])){
								echo $interface1['name']['fri'].' ';
							}
							else{
								echo '週五 ';
							}
							break;
						case 6:
							if($interface1!='-1'&&isset($interface1['name']['sat'])){
								echo $interface1['name']['sat'].' ';
							}
							else{
								echo '週六 ';
							}
							break;
						case 7:
							if($interface1!='-1'&&isset($interface1['name']['sun'])){
								echo $interface1['name']['sun'].' ';
							}
							else{
								echo '週日 ';
							}
							break;
					}
					echo date('H:i'); 
					?>
				</div>
			</div>
		</div>
		<div id="content"><!-- connect -->
			<div id="MemberBill" class=""><!-- 會員、明細、帳單 -->
				<div id="member" class="">
					<div id="tabs1" class="">
						<div style='height:100%;'>
							<div id='tempbox'>
								<input type='hidden' name='target' value='0'>
								<input type='hidden' name='linenumber' value='0'>
								<input type='hidden' name='typeno' value=''>
								<input type='hidden' name='type' value=''>
								<input type='hidden' name='no' value=''>
								<input type='hidden' name='personcount' value='0'>
								<input type='hidden' name='needcharge' value='1'>
								<input type='hidden' name='dis1' value=''>
								<input type='hidden' name='dis2' value=''>
								<input type='hidden' name='dis3' value=''>
								<input type='hidden' name='dis4' value=''>
								<input type='hidden' name='name' value=''>
								<input type='hidden' name='name2' value=''>
								<input type='hidden' name='isgroup' value=''>
								<input type='hidden' name='childtype' value=''>
								<input type='hidden' name='mname1' value=''>
								<input type='hidden' name='mname2' value=''>
								<input type='hidden' name='insaleinv' value=''>
								<input type='hidden' name='unitprice' value='0'>
								<input type='hidden' name='money' value='0'>
								<input type='hidden' name='discount' value='0'>
								<input type='hidden' name='discontent' value=''>
								<input type='hidden' name='dispoint' value='0'>
								<input type='hidden' name='dispointtime' value='0'>
								<input type='hidden' name='number' value='0'>
								<input type='hidden' name='subtotal' value='0'>
								<input type='hidden' name='taste1' value=''>
								<input type='hidden' name='taste1name' value=''>
								<input type='hidden' name='taste1price' value=''>
								<input type='hidden' name='taste1number' value=''>
								<input type='hidden' name='taste1money' value='0'>
								<input type='hidden' name='taste2' value=''>
								<input type='hidden' name='background' value=''>
								<input type='hidden' name='itemdis' value=''>
								<input type='hidden' name='listdis' value=''>
								<input type='hidden' name='bothdis' value=''>
								<input type='hidden' name='usemempoint' value=''>
								<input type='hidden' name='getpointtype' value=''>
								<input type='hidden' name='initgetpoint' value=''>
								<input type='hidden' name='getpoint' value=''>
							</div>
							<?php
							if(isset($initsetting['init']['bolai'])&&$initsetting['init']['bolai']=='1'){
							?>
							<div style='width:120px;height:100%;margin-right:2px;float:left;'>
							<?php
							}
							else{
							?>
							<div style='width:calc(50% - 4px);height:100%;margin-right:2px;float:left;'>
							<?php
							}
							?>
								<button id='membutton' style='width:100%;height:100%;float:left;overflow:hidden;' <?php if(isset($initsetting['init']['openmember'])&&$initsetting['init']['openmember']=='1')echo '';else echo 'disabled'; ?>><div style='width:100%;font-size:20px;float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['member']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['member']."</div>";else; ?></div><div id='tel' style='width:100%;font-size:20px;float:left;'></div><div id='memremarks' style="text-overflow: ellipsis;overflow: hidden;float: left;width: 90%;margin: 0 5%;"></div><input type='hidden' id='memberpoint' value='0'><?php //剩餘會員點數 ?><input type='hidden' id='membermoney' value='0'><?php //剩餘會員儲值金 ?><input type='hidden' id='companynumber' value=''><input type='hidden' id='recommend' value=''><?php //會員統編 ?></button>
								<div class='yunlincoinsico' style="width:32px;height:32px;content:' ';background: url('./lib/api/yunlincoins/ico.png');display:none;"></div>
							</div>
							<?php
							if(isset($initsetting['init']['bolai'])&&$initsetting['init']['bolai']=='1'){
							?>
							<div style='width:calc(50% - 124px);height:100%;margin-right:2px;float:left;'>
								<button id='memdeposit' style='width:100%;height:100%;float:left;overflow:hidden;'><div style='width:100%;font-size:20px;float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['deposit']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['deposit']."</div>";else; ?></div><div style='width:100%;font-size:20px;float:left;'></div></button>
							</div>
							<?php
							}
							else{
							}
							?>
							<textarea style='width:calc(50% - 4px);height:calc(100% - 5px);margin-right:2px;background-color:#000000;color:#ffffff;resize:none;float:left;display:none;' value='' id='editview' readonly></textarea>
							<div style='width:calc(50% - 4px);height:calc(100% - 5px);float:left;<?php if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']=='1')echo '';else echo 'display:none;'; ?>'>
								<button style='width:50%;height:100%;float:left;border:0px;'><div id='name1' style='font-size:3vh;'>用餐<br>人數</div></button>
								<input id='persons' type='text' style='width:50%;height:100%;background-color:#ffffff;text-align:right;float:left;font-size:25px;padding:0 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' name='persons' value='0' readonly>
							</div>
						</div>
					</div>
				</div>
				<div id="list" class="listtab">
					<div id='butobx' style='overflow:hidden;'>
						<button class="w3-bar-item w3-button focus" id='tabs4button' onclick="openCity('listtab','tabs4')" ><?php if(isset($interface1['name']['tag2']))echo "<span id='name1'>".$interface1['name']['tag2']."</span>"; ?><?php if(isset($interface1['name']['tag2'])&&isset($interface2['name']['tag2']))echo "<span id='name2'> /".$interface2['name']['tag2']."</span>";else if(!isset($interface1['name']['tag2'])&&isset($interface2['name']['tag2']))echo "<span id='name2'>".$interface2['name']['tag2']."</span>";else; ?></button>
						<button class="w3-bar-item w3-button" id='tabs5button' onclick="openCity('listtab','tabs5')" disabled><?php if(isset($interface1['name']['tag3']))echo "<span id='name1'>".$interface1['name']['tag3']."</span>"; ?><?php if(isset($interface1['name']['tag3'])&&isset($interface2['name']['tag3']))echo "<span id='name2'> /".$interface2['name']['tag3']."</span>";else if(!isset($interface1['name']['tag3'])&&isset($interface2['name']['tag3']))echo "<span id='name2'> /".$interface2['name']['tag3']."</span>";else; ?></button>
					</div>
					<!-- <ul>
						<li><a href="#tabs4"><?php if($interface1!='-1')echo "<span id='name1'>".$interface1['name']['tag2']."</span>"; ?><?php if($interface1!='-1'&&$interface2!='-1')echo "<span id='name2'> /".$interface2['name']['tag2']."</span>";else if($interface1=='-1'&&$interface2!='-1')echo "<span id='name2'>".$interface2['name']['tag2']."</span>";else; ?></a></li>
						<li><a href="#tabs5"><?php if($interface1!='-1')echo "<span id='name1'>".$interface1['name']['tag3']."</span>"; ?><?php if($interface1!='-1'&&$interface2!='-1')echo "<span id='name2'> /".$interface2['name']['tag3']."</span>";else if($interface1=='-1'&&$interface2!='-1')echo "<span id='name2'> /".$interface2['name']['tag3']."</span>";else; ?></a></li>
					</ul> -->
					<div id='listtabbox'>
						<div id="tabs4" class="w3-container listtabItem" style='background-color:#848284;'>
							<div class='listtitle' style='background-color:#EFEBDE;width:calc(100% - 20px);padding-right:20px;'>
								<div style='width:3%;min-height:1px;'>
								</div>
								<div style='width:10%;'>
									No
									<!-- <div id='name1'><?php echo $interface1['name']['7']; ?></div>
									<div id='name2'><?php if(isset($interface2['name']['7']))echo '('.$interface2['name']['7'].')';else; ?></div> -->
								</div>
								<div style='width:57%;'>
									Item
									<!-- <div id='name1'><?php echo $interface1['name']['8']; ?></div>
									<div id='name2'><?php if(isset($interface2['name']['8']))echo '('.$interface2['name']['8'].')';else; ?></div> -->
								</div>
								<div style='width:10%;text-align:right;'>
									U/P
									<!-- <div id='name1'><?php echo $interface1['name']['9']; ?></div>
									<div id='name2'><?php if(isset($interface2['name']['9']))echo '('.$interface2['name']['9'].')';else; ?></div> -->
								</div>
								<div style='width:10%;text-align:center;'>
									QTY
									<!-- <div id='name1'><?php echo $interface1['name']['10']; ?></div>
									<div id='name2'><?php if(isset($interface2['name']['10']))echo '('.$interface2['name']['10'].')';else; ?></div> -->
								</div>
								<div style='width:10%;text-align:right;'>
									Subtotal
									<!-- <div id='name1'><?php echo $interface1['name']['11']; ?></div>
									<div id='name2'><?php if(isset($interface2['name']['11']))echo '('.$interface2['name']['11'].')';else; ?></div> -->
								</div>
							</div>
							<div class='listcontentbox' style='background-color:#ffffff;width:100%;max-height:calc(100% - 22px);overflow-x:hidden;overflow-y:auto;'>
								<!-- 該區HTML，在未結帳單的流程中，會重新寫HTML，因此如有新增修改，請留意未結帳單加點步驟 -->
								<form data-id='listform' method='post' action='' style='overflow:hidden;'>
									<input type='hidden' name='machinetype' value='<?php if(isset($_GET['submachine']))echo $_GET['submachine'];else if(isset($_GET['machine'])) echo $_GET['machine'];else echo 'm1'; ?>'>
									<input type='hidden' name='memno' value=''>
									<input type='hidden' name='memtel' value=''>
									<input type='hidden' name='memaddno' value=''>
									<input type='hidden' name='consecnumber' value='<?php if(isset($_GET['consecnumber']))echo $_GET['consecnumber']; ?>'>
									<input type='hidden' name='saleno' value=''>
									<input type='hidden' name='bizdate' value='<?php if(isset($_GET['bizdate']))echo $_GET['bizdate'];else echo $timeini['time']['bizdate']; ?>'>
									<input type='hidden' name='listtype' value='<?php echo $listtype; ?>'>
									<input type='hidden' name='typename' value='<?php if($buttons1!='-1')echo $buttons1['name']['listtype'.$listtype]; ?>'>
									<input type='hidden' name='invsalemoney' value='0'>
									<input type='hidden' name='charge' value=''>
									<input type='hidden' name='tempban' value=''>
									<input type='hidden' name='tempbuytype' value='<?php echo $print['item']['tempbuytype']; ?>'>
									<input type='hidden' name='printclientlist' value='<?php if(isset($print['item']['printclientlist']))echo $print['item']['printclientlist']; ?>'>
									<input type='hidden' name='total' value='0'>
									<input type='hidden' name='totalnumber' value='0'>
									<input type='hidden' name='tablenumber' value='<?php if(isset($_GET['tabnum']))echo $_GET['tabnum'];else; ?>'>
									<input type='hidden' name='usercode' value='<?php if(isset($_GET['usercode']))echo $_GET['usercode'];else; ?>'>
									<input type='hidden' name='username' value='<?php if(isset($_GET['username']))echo $_GET['username'];else; ?>'>
									<input type='hidden' name='invlist' value='<?php echo $initsetting['init']['invlist']; ?>'>
									<input type='hidden' name='person1' value='0'>
									<input type='hidden' name='person2' value='0'>
									<input type='hidden' name='person3' value='0'>
									<input type='hidden' name='mancode' value=''>
									<input type='hidden' name='manname' value=''>
									<input type='hidden' name='linklist' value=''>
									<div class='listcontent' style='padding-right:5px;width:calc(100% - 5px);'>
									</div>
								</form>
							</div>
						</div>
						<div id="tabs5" class="w3-container listtabItem" style='background-color:#848284;display:none;'>
							<div class='listtitle' style='background-color:#EFEBDE;width:calc(100% - 3px);padding-right:3px;'>
								<div style='width:3%;min-height:1px;'>
								</div>
								<div style='width:10%;'>
									<?php if(isset($interface1['name']['7']))echo "<div id='name1'>".$interface1['name']['7']."</div>"; ?>
									<?php if(isset($interface1['name']['7'])&&isset($interface2['name']['7']))echo "<div id='name2'>(".$interface2['name']['7'].')</div>';else if(!isset($interface1['name']['7'])&&isset($interface2['name']['7']))echo "<div id='name2'>".$interface2['name']['7'].'</div>';else; ?>
								</div>
								<div style='width:57%;'>
									<?php if(isset($interface1['name']['8']))echo "<div id='name1'>".$interface1['name']['8']."</div>"; ?>
									<?php if(isset($interface1['name']['8'])&&isset($interface2['name']['8']))echo "<div id='name2'>(".$interface2['name']['8'].')</div>';else if(!isset($interface1['name']['8'])&&isset($interface2['name']['8']))echo "<div id='name2'>".$interface2['name']['8'].'</div>';else; ?>
								</div>
								<div style='width:10%;text-align:right;'>
									<?php if(isset($interface1['name']['9']))echo "<div id='name1'>".$interface1['name']['9']."</div>"; ?>
									<?php if(isset($interface1['name']['9'])&&isset($interface2['name']['9']))echo "<div id='name2'>(".$interface2['name']['9'].')</div>';else if(!isset($interface1['name']['9'])&&isset($interface2['name']['9']))echo "<div id='name2'>".$interface2['name']['9'].'</div>';else; ?>
								</div>
								<div style='width:10%;text-align:right;'>
									<?php if(isset($interface1['name']['10']))echo "<div id='name1'>".$interface1['name']['10']."</div>"; ?>
									<?php if(isset($interface1['name']['10'])&&isset($interface2['name']['10']))echo "<div id='name2'>(".$interface2['name']['10'].')</div>';else if(!isset($interface1['name']['10'])&&isset($interface2['name']['10']))echo "<div id='name2'>".$interface2['name']['10'].'</div>';else; ?>
								</div>
								<div style='width:10%;text-align:right;'>
									<?php if(isset($interface1['name']['11']))echo "<div id='name1'>".$interface1['name']['11']."</div>"; ?>
									<?php if(isset($interface1['name']['11'])&&isset($interface2['name']['11']))echo "<div id='name2'>(".$interface2['name']['11'].')</div>';else if(!isset($interface1['name']['11'])&&isset($interface2['name']['11']))echo "<div id='name2'>".$interface2['name']['11'].'</div>';else; ?>
								</div>
							</div>
							<div class='listcontentbox' style='background-color:#ffffff;width:100%;max-height:calc(100% - 22px);overflow-x:hidden;overflow-y:auto;'>
								<form data-id='listform' method='post' action=''>
									<input type='hidden' name='tempban' value=''>
									<input type='hidden' name='total' value='0'>
									<input type='hidden' name='terminalnumber' value='<?php echo $machinedata['basic']['terminalnumber']; ?>'>
									<div class='listcontent' style='padding-right:5px;width:calc(100% - 5px);'>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div id='funbox' style='position:absolute;bottom:0;width:100%;'>
						<button id='all' value='全選' style='background-color:#FFA801;width:110px;float:left;margin-right:2px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listfun1']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listfun1']."</div>";else; ?></button>
						<button id='cancel' value='取消' style='background-color:#FFA801;width:70px;float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listfun2']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listfun2']."</div>";else; ?></button>
						<?php
						if(isset($initsetting['rfid']['open'])&&$initsetting['rfid']['open']!='0'){//2020/4/15 rfid讀取點餐，最先由三顧提出串接
						?>
						<button class='readrfid' style='background-color:#FFA801;width:110px;float:left;margin-left:2px;'><div id='name1'>RFID</div></button>
						<div class='rfidbeep' style='display:none;'>
							<audio class='beepcontrol'>
								<source src="../tool/beep.mp3" type="audio/mpeg">
							</audio>
						</div>
						<div style='margin-left:5px;float:left;display:none;'><?php if(isset($interface1['name']['12']))echo "<span id='name1'>".$interface1['name']['12']."</span>"; ?><?php if(isset($interface1['name']['12'])&&isset($interface2['name']['12']))echo "<span id='name2'> /".$interface2['name']['12']."</span>";else if(!isset($interface1['name']['12'])&&isset($interface2['name']['12']))echo "<span id='name2'>".$interface2['name']['12']."</span>"; ?>：<input type='text' class='quickorder' style='width:80px;background-color:#ffffff;'></div>
						<?php
						}
						else if(isset($initsetting['init']['quickorder'])&&$initsetting['init']['quickorder']==1){
						?>
						<div style='margin-left:5px;float:left;'><?php if(isset($interface1['name']['12']))echo "<span id='name1'>".$interface1['name']['12']."</span>"; ?><?php if(isset($interface1['name']['12'])&&isset($interface2['name']['12']))echo "<span id='name2'> /".$interface2['name']['12']."</span>";else if(!isset($interface1['name']['12'])&&isset($interface2['name']['12']))echo "<span id='name2'>".$interface2['name']['12']."</span>"; ?>：<input type='text' class='quickorder' style='width:80px;background-color:#ffffff;' autofocus></div>
						<?php
						}
						else{
						}
						?>

						<!-- <button id='view' value='未結清單' style='float:right;background-color:#FFA801;width:90px;margin-right:2px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listfun3']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listfun3']."</div>";else; ?></button> -->
						<?php
						if((isset($initsetting['nidin']['usenidin'])&&$initsetting['nidin']['usenidin']==1&&(!isset($initsetting['nidin']['autoaccept'])||$initsetting['nidin']['autoaccept']=='0'))||(isset($initsetting['init']['quickclick'])&&$initsetting['init']['quickclick']=='1'&&isset($initsetting['init']['quickclicklisttype'])&&$initsetting['init']['quickclicklisttype']=='1')){//2022/5/26 quickclick訂單手動接單合併進來//2022/3/16 nidin自動接單則不顯示網路訂單按鈕
						?>
						<div style='position: relative;float:right;width:90px;height:100%;margin-right:2px;'><div id='point' style='width:20px;height:20px;position:absolute;top:0;right:0;border-radius:100%;text-align: center;line-height: 20px;'></div>
						<?php
						if(!isset($initsetting['init']['webding'])||$initsetting['init']['webding']=='1'){
							if(!isset($initsetting['init']['loopding'])||$initsetting['init']['loopding']=='0'){//2021/11/3
							?>
							<audio id="player">
							<?php
							}
							else{
							?>
							<audio id="player" loop>
							<?php
							}
						?>
						<!-- <audio id="player" loop> -->
							<source src="../database/ding.mp3" type="audio/mpeg">
						</audio>
						<?php
						}
						else{
						}
						?>
						<button id='webbooking' value='網路訂單' style='background-color:#FFA801;width:100%;height:100%;'><?php if(isset($buttons1['name']['weborder']))echo "<div id='name1'>".$buttons1['name']['weborder']."</div>";else echo "<div id='name1'>網路訂單</div>"; ?><?php if(isset($buttons2['name']['weborder']))echo "<div id='name2'>".$buttons2['name']['weborder']."</div>";else; ?></button>
						</div>
						<?php
						}
						else{
						}
						//2022/1/7 就是有人要兩個都開
						if(isset($initsetting['init']['callkeybord'])&&$initsetting['init']['callkeybord']==1){
						?>
						<button id='numkeybord' value='叫號鍵盤' style='float:right;background-color:#FFA801;width:90px;margin-right:2px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['listfun4']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['listfun4']."</div>";else; ?></button>
						<?php
						}
						else{
						}
						?>
					</div>
					<!-- <div id='viewtemp' style=''>
						
					</div>
					<div id='callnumber' style='float:right;'>
						
					</div> -->
				</div>
				<div id="billfun" class="city">
					<div id='butobx' style='overflow:hidden;position: relative;'>
						<button id='billfun1button' class="w3-bar-item w3-button <?php if($timeini['time']['isopen']=='1')echo '';else echo 'focus'; ?>" onclick="openCity('city','billfun1')" <?php if($timeini['time']['isopen']=='1')echo 'disabled'; ?>><?php if(isset($interface1['name']['tag4']))echo "<span id='name1'>".$interface1['name']['tag4']."</span>"; ?><?php if(isset($interface1['name']['tag4'])&&isset($interface2['name']['tag4']))echo "<span id='name2'> /".$interface2['name']['tag4']."</span>";else if(!isset($interface1['name']['tag4'])&&isset($interface2['name']['tag4']))echo "<span id='name2'>".$interface2['name']['tag4']."</span>";else; ?></button>
						<button id='billfun2button' class="w3-bar-item w3-button <?php if($timeini['time']['isopen']=='1')echo 'focus'; ?>" onclick="openCity('city','billfun2')"><?php if(isset($interface1['name']['tag5']))echo "<span id='name1'>".$interface1['name']['tag5']."</span>"; ?><?php if(isset($interface1['name']['tag5'])&&isset($interface2['name']['tag5']))echo "<span id='name2'> /".$interface2['name']['tag5']."</span>";else if(!isset($interface1['name']['tag5'])&&isset($interface2['name']['tag5']))echo "<span id='name2'>".$interface2['name']['tag5']."</span>";else; ?></button>
						<button id='billfun3button' class="w3-bar-item w3-button" onclick="openCity('city','billfun3')"><?php if(isset($interface1['name']['tag6']))echo "<span id='name1'>".$interface1['name']['tag6']."</span>"; ?><?php if(isset($interface1['name']['tag6'])&&isset($interface2['name']['tag6']))echo "<span id='name2'> /".$interface2['name']['tag6']."</span>";else if(!isset($interface1['name']['tag6'])&&isset($interface2['name']['tag6']))echo "<span id='name2'>".$interface2['name']['tag6']."</span>";else; ?></button>
					</div>
					<!-- <ul>
						<li><a href="#billfun1"><?php if($interface1!='-1')echo "<span id='name1'>".$interface1['name']['tag4']."</span>"; ?><?php if($interface1!='-1'&&$interface2!='-1')echo "<span id='name2'> /".$interface2['name']['tag4']."</span>";else if($interface1=='-1'&&$interface2!='-1')echo "<span id='name2'>".$interface2['name']['tag4']."</span>";else; ?></a></li>
						<li><a href="#billfun2"><?php if($interface1!='-1')echo "<span id='name1'>".$interface1['name']['tag5']."</span>"; ?><?php if($interface1!='-1'&&$interface2!='-1')echo "<span id='name2'> /".$interface2['name']['tag5']."</span>";else if($interface1=='-1'&&$interface2!='-1')echo "<span id='name2'>".$interface2['name']['tag5']."</span>";else; ?></a></li>
					</ul> -->
					<div id='citybox'>
						<div id="billfun1" class='w3-container cityItem' style='<?php if($timeini['time']['isopen']=='1')echo 'display:none;'; ?>'>
							<!-- <table style='width:100%;height:100%;'>
								<tr>
									<td style='width:25%;height:50%;'> --><button value='促銷' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun11']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun11']."</div>";else; ?></button><!-- </td>
									<td style='width:25%;'> --><button value='刪除' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun12']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun12']."</div>";else; ?></button><!-- </td>
									<td style='width:25%;'> --><button value='開錢櫃' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($_GET['submachine']))echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun13']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun13']."</div>";else; ?></button><!-- </td>
									<td rowspan='2' style='width:25%;'> --><button value='結帳' style='background-color:#C4A5C4;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(100% - 2px);float:right;' <?php if(isset($_GET['submachine']))echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun14']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun14']."</div>";else; ?></button><!-- </td>
								</tr>
								<tr> -->
									<?php
									if(isset($initsetting['init']['opentemp'])&&$initsetting['init']['opentemp']==1){
										if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']==1){
										?>
										<!-- <td style='width:25%;height:50%;'> --><button value='回到桌控' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun15b']."</div>";else; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun15b']."</div>";else; ?></button><!-- </td> -->
										<?php
										}
										else{
										?>
										<!-- <td style='width:25%;height:50%;'> --><button value='回到桌控' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun15a']."</div>";else; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun15a']."</div>";else; ?></button><!-- </td> -->
										<?php
										}
									?>
									<?php
									}
									else{
									?>
									<!-- <td style='width:25%;height:50%;'> --><button value='回到桌控' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']==1)echo '';else echo 'disabled'; ?>><?php if($buttons1!='-1'&&isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']==1)echo "<div id='name1'>".$buttons1['name']['billfun15b']."</div>";else if($buttons1!='-1'&&isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']==0)echo "<div id='name1'>".$buttons1['name']['billfun15a']."</div>"; ?><?php if($buttons2!='-1'&&isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']==1)echo "<div id='name2'>".$buttons2['name']['billfun15b']."</div>";else if($buttons2!='-1'&&isset($initsetting['init']['controltable'])&&$initsetting['init']['controltable']==0)echo "<div id='name2'>".$buttons2['name']['billfun15a']."</div>";else; ?></button><!-- </td> -->
									<?php
									}
									?>
									<!-- <td style='position: relative'> --><button value='套餐確認' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['sendset']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['sendset']."</div>";else; ?></button><!-- </td>
									<td> --><button value='現金結帳' style='background-color:#C4A5C4;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($_GET['submachine'])||(isset($initsetting['init']['salecash'])&&$initsetting['init']['salecash']=='0'))echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun17']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun17']."</div>";else; ?></button><!-- </td>
								</tr>
							</table> -->
						</div>
						<div id='billfun2' class='w3-container cityItem' style='<?php if($timeini['time']['isopen']=='1')echo '';else echo 'display:none;'; ?>'>
							<!-- <table style='width:100%;height:100%;'>
								<tr>
									<td style='width:25%;height:50%;'> --><button class='open' value='開店' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;'  <?php if($timeini['time']['isopen']=='0'||isset($_GET['submachine']))echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun21']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun21']."</div>";else; ?></button><!-- </td>
									<td style='width:25%;height:50%;'> --><button class='close' value='交班' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if($timeini['time']['isclose']=='0'||isset($_GET['submachine']))echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun22']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun22']."</div>";else; ?></button><!-- </td>
									<td style='width:25%;height:50%;'> --><button id='kvm' value='廚房控餐' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($initsetting['init']['kvm'])&&$initsetting['init']['kvm']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['kvm']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['kvm']."</div>";else; ?></button><!-- </td>
									<td style='width:25%;height:50%;'> --><button class='outmoney' value='支出費用' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($initsetting['init']['moneycost'])&&$initsetting['init']['moneycost']==1&&$timeini['time']['isclose']!='0'&&!isset($_GET['submachine']));else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun24']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun24']."</div>";else; ?></button><!-- </td>
								</tr>
								<tr>
									<td style='width:25%;height:50%;'> --><button id='salevoid' value='帳單作廢' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun29']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun29']."</div>";else; ?></button><!-- </td>
									<td> --><button style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' disabled></button><!-- </td>
									<td> --><button id='salelist' value='瀏覽帳單' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun27']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun27']."</div>";else; ?></button><!-- </td>
									<td> --><button id='exit' value='離開' style='background-color:#D57B70;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun28']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun28']."</div>";else; ?></button><!-- </td>
								</tr>
							</table> -->
						</div>
						<div id='billfun3' class='w3-container cityItem' style='display:none;'>
							<!-- <table style='width:100%;height:100%;'>
								<tr>
									<td style='width:25%;height:50%;'> --><button id='punch' value='員工打卡' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($initsetting['init']['openpunch'])&&$initsetting['init']['openpunch']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun31']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun31']."</div>";else; ?></button><!-- </td>
									<td style='width:25%;height:50%;'> --><button id='historypaper' value='列印報表' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($initsetting['init']['historypaper'])&&$initsetting['init']['historypaper']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun32']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun32']."</div>";else; ?></button><!-- </td>
									<td style='width:25%;height:50%;'> --><button class='logout' value='切換人員' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($initsetting['init']['openindex'])&&$initsetting['init']['openindex']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['billfun23']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['billfun23']."</div>";else; ?></button><!-- </td>
									<td style='width:25%;height:50%;'> --><button id='printchange' value='其他功能' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['printchange']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['printchange']."</div>";else; ?></button><!-- </td>
								</tr>
								<tr>
									<td style='width:25%;height:50%;'> --><button id='editpunch' value='打卡紀錄' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($initsetting['init']['openpunch'])&&$initsetting['init']['openpunch']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['editpunch']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['editpunch']."</div>";else; ?></button><!-- </td>
									<td> --><button id='computemoney' value='統計錢櫃金額' style='background-color:#4282B5;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if(isset($initsetting['init']['opencomputemoney'])&&$initsetting['init']['opencomputemoney']=='0')echo 'disabled';else; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['computemoney']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['computemoney']."</div>";else; ?></button><!-- </td>
									<td> --><button id='' value='' style='background-color:#D5DC75;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' disabled></button><!-- </td>
									<td> --><button id='easycardbalancequery' value='悠遊卡餘額' style='background-color:#D57B70;color:#000000;margin:1px;width:calc(25% - 2px);height:calc(50% - 2px);float:left;' <?php if((isset($initsetting['init']['intellapay'])&&$initsetting['init']['intellapay']=='1'&&isset($initsetting['init']['easycard'])&&$initsetting['init']['easycard']=='1')||(isset($initsetting['init']['nccc'])&&$initsetting['init']['nccc']=='1'&&isset($initsetting['init']['nccceasycard'])&&$initsetting['init']['nccceasycard']=='1'));else echo 'disabled'; ?>><?php if($buttons1!='-1'&&isset($buttons1['name']['easycardbanlance']))echo "<div id='name1'>".$buttons1['name']['easycardbanlance']."</div>"; ?><?php if($buttons2!='-1'&&isset($buttons2['name']['easycardbanlance']))echo "<div id='name2'>".$buttons2['name']['easycardbanlance']."</div>";else; ?></button><!-- </td>
								</tr>
							</table> -->
						</div>
					</div>
				</div>
			</div>
			<div id="numbox"><!-- 數字鍵盤 -->
				<div id='d1'>
					<input type='button' value='number' style='display:none;'>
					<input type='button' id='number' value='1'>
					<input type='button' id='number' value='2'>
					<input type='button' id='number' value='3'>
					<input type='button' id='number' value='4'>
					<input type='button' id='number' value='5'>
					<input type='button' id='number' value='6'>
					<input type='button' id='number' value='7'>
					<input type='button' id='number' value='8'>
					<input type='button' id='number' value='9'>
					<input type='button' id='number' value='0'>
				</div>
				<div id='d2'>
					<button id='clear' value='清除'><?php if($buttons1!='-1')echo "<div id='name1' style='font-weight:bold;'>".$buttons1['name']['numfun1']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['numfun1']."</div>";else; ?></button>
					<input type='button' id='plus' value='+'>
					<input type='button' id='diff' value='-'>
					<button id='open' value='開放金額' disabled><div id='name'>$</div></button>
					<input type='hidden' name='type' value='new'>
				</div>
			</div>
			<div id="menubox" class=""><!-- menu -->
				<?php
				if($initsetting['init']['menutype']==1){
				}
				else{
					echo '<div id="itemtype" ';if($initsetting['init']['menutype']==3)echo 'class="type3"';echo '>';
					$maxrow=$initsetting['init']['menutyperow'];
					$maxcol=$initsetting['init']['menutypecol'];
					$itemnumber=0;
					$conn=sqlconnect("../database","menu.db","","","","sqlite");
					$sql='SELECT DISTINCT fronttype as front FROM itemsdata WHERE state="1" OR state IS NULL ORDER BY replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(fronttype))), "\'", ""), "0", "0")||fronttype';
					$front=sqlquery($conn,$sql,'sqlite');
					$temp=array_column($front,'front');
					//print_r($front);
					sqlclose($conn,'sqlite');
					$frontname=parse_ini_file('../database/'.$basic['basic']['company'].'-front.ini',true);
					$sortfront=quicksort($frontname,'seq');
					foreach($sortfront as $sindex=>$sf){
						if($sf['state']=='1'&&in_array($sf['typeno'],$temp)&&(!isset($sf['subtype'])||$sf['subtype']=='0')&&(!isset($sf['posvisible'])||$sf['posvisible']=='1')){//2021/8/25 增加過濾"顯示於POS"類別//2020/3/27 增加過濾"套餐選項"類別
						}
						else{
							unset($sortfront[$sindex]);
						}
					}
					/*foreach($temp as $sindex=>$sf){
						if($sf['state']=='1'){
						}
						else{
							unset($sortfront[$sindex]);
						}
					}*/
					$sortfront=array_values($sortfront);
					$temp=$sortfront;
					//print_r($sortfront);
					foreach($sortfront as $sindex=>$sf){
						//if($sf['state']=='1'){
							//foreach($front as $fv){
								//if($sf['typeno']==$fv['front']){
									if(($initsetting['init']['menutype']==2||$initsetting['init']['menutype']==3)&&($sindex+1)==($maxrow*$maxcol)&&isset($sortfront[$sindex+1])){
										echo "<div class='typenext' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);overflow:hidden;color:#000000;background-color: #FCD7A3;'>
												<button class='typebut' value='換頁'><div id='name1' style='float:left;width:100%;font-size:150%;font-weight:bold;'>";
												if(isset($buttons1['name']['changepage']))echo $buttons1['name']['changepage'];else echo '換頁';
												echo "</div><div id='name2' style='float:left;width:100%;'>1/".sizeof($sortfront)."</div></button>
											</div>";
									}
									else if($itemnumber<($maxrow*$maxcol)){
										echo "<div class='type' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);overflow:hidden;'>
												<button class='typebut' value='".$sf['name1'].$sf['name2']."' style='";if(isset($sf['bgcolor']))echo 'background-color:'.$sf['bgcolor'].';';echo "'><div id='name1' style='float:left;width:100%;";if(isset($sf['size1']))echo 'font-size:'.$sf['size1'].'px;';if(isset($sf['color1']))echo 'color:'.$sf['color1'].';';if(isset($sf['bold1'])&&$sf['bold1']==1)echo 'font-weight:bold;';else echo 'font-weight:normal;';echo "'>".$sf['name1']."</div><div id='name2' style='float:left;width:100%;";if(isset($sf['size2']))echo 'font-size:'.$sf['size2'].'px;';if(isset($sf['color2']))echo 'color:'.$sf['color2'].';';if(isset($sf['bold2'])&&$sf['bold2']==1)echo 'font-weight:bold;';else echo 'font-weight:normal;';echo "'>".$sf['name2']."</div></button>
												<input type='hidden' class='front' value='".$sf['typeno']."'>
											</div>";
									}
									else{
									}
									$itemnumber++;
									//break;
								/*}
								else{
								}*/
							//}
						/*}
						else{
						}*/
					}
					/*foreach($front as $f){
						if(isset($frontname[$f['front']]['state'])&&$frontname[$f['front']]['state']=='1'){
							if(substr($f['front'],0,1)=='g'){
							}
							else{
								if($itemnumber<($maxrow*$maxcol)){
									echo "<div class='type' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);overflow:hidden;'>
											<button class='typebut' value='".$frontname[$f['front']]['name'.$initsetting['init']['firlan']].$frontname[$f['front']]['name'.$initsetting['init']['seclan']]."' style='";if(isset($frontname[$f['front']]['bgcolor']))echo 'background-color:'.$frontname[$f['front']]['bgcolor'].';';echo "'><div id='name1' style='float:left;width:100%;";if(isset($frontname[$f['front']]['size1']))echo 'font-size:'.$frontname[$f['front']]['size1'].'px;';if(isset($frontname[$f['front']]['color1']))echo 'color:'.$frontname[$f['front']]['color1'].';';if(isset($frontname[$f['front']]['bold1'])&&$frontname[$f['front']]['bold1']==1)echo 'font-weight:bold;';else echo 'font-weight:normal;';echo "'>".$frontname[$f['front']]['name'.$initsetting['init']['firlan']]."</div><div id='name2' style='float:left;width:100%;";if(isset($frontname[$f['front']]['size2']))echo 'font-size:'.$frontname[$f['front']]['size2'].'px;';if(isset($frontname[$f['front']]['color2']))echo 'color:'.$frontname[$f['front']]['color2'].';';if(isset($frontname[$f['front']]['bold2'])&&$frontname[$f['front']]['bold2']==1)echo 'font-weight:bold;';else echo 'font-weight:normal;';echo "'>".$frontname[$f['front']]['name'.$initsetting['init']['seclan']]."</div></button>
											<input type='hidden' class='front' value='".$f['front']."'>
										</div>";
									$itemnumber++;
								}
								else{
									break;
								}
							}
						}
						else{
						}
					}*/
					if($itemnumber<($maxrow*$maxcol)){
						for($i=0;$i<(($maxrow*$maxcol)-$itemnumber);$i++){
							echo "<div class='type' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);'>
									<input type='button' value='' disabled>
									<input type='hidden' name='front' value=''>
								</div>";
						}
					}
					else{
					}
					echo '</div>';
				}
				?>
				<div id="items" <?php if($initsetting['init']['menutype']==3)echo 'class="type3" style="display:none;"'; ?>>
					<?php
					$maxrow=$initsetting['init']['menurow'];
					$maxcol=$initsetting['init']['menucol'];
					if($initsetting['init']['menutype']==1){
						$conn=sqlconnect("../database","menu.db","","","","sqlite");
						$sql='SELECT * FROM itemsdata WHERE (state IS NULL OR state=1) ORDER BY CAST(typeseq AS INT),CAST(frontsq AS INT),replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(inumber))), "\'", ""), "0", "0")||inumber';
						$items=sqlquery($conn,$sql,'sqlite');
						//print_r($items);
						sqlclose($conn,'sqlite');
						$frontname=parse_ini_file('../database/'.$basic['basic']['company'].'-front.ini',true);
						$itemname=parse_ini_file('../database/'.$basic['basic']['company'].'-menu.ini',true);
						if(file_exists('../database/stock.ini')){
							$stock=parse_ini_file('../database/stock.ini',true);
						}
						else{
							$stock='-1';
						}
						$j=0;
						for($i=0;$i<$maxcol*$maxrow&&$j<$maxcol*$maxrow;$i++){
							if(!isset($items[$i]['inumber'])){
								break;
							}
							else if(($i+1)==($maxcol*$maxrow)&&((sizeof($items)-1)>($maxcol*$maxrow))){
								//var temp=items[number][2].split(';');
								echo "<div class='itemnext' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);color:#000000;background-color: #FCD7A3;'><button value='換頁'";

								echo "><div id='name1' style='float:left;width:100%;font-size:150%;font-weight:bold;'>";
								if(isset($buttons1['name']['changepage']))echo $buttons1['name']['changepage'];else echo '換頁';
								echo "</div><div id='name2' style='float:left;width:100%;font-weight:bold;'>1/".sizeof($items)."</div></button><input type='hidden' name='typeno' value='allitems'><input type='hidden' name='row' value='".$maxrow."'><input type='hidden' name='col' value='".$maxcol."'></div>";
								//$('.order#order #menubox #items').append(tt);
							}
							else if(isset($items[$i]['inumber'])&&$itemname[$items[$i]['inumber']]['state']=='1'&&(!isset($itemname[$items[$i]['inumber']]['posvisible'])||$itemname[$items[$i]['inumber']]['posvisible']=='1')&&((intval($itemname[$items[$i]['inumber']]['counter'])>0&&isset($stock[$items[$i]['inumber']]['stock'])&&intval($stock[$items[$i]['inumber']]['stock'])>0)||intval($itemname[$items[$i]['inumber']]['counter'])<=0)&&(isset($frontname[$items[$i]['fronttype']]['state'])&&$frontname[$items[$i]['fronttype']]['state']=='1'&&(!isset($frontname[$items[$i]['fronttype']]['posvisible'])||$frontname[$items[$i]['fronttype']]['posvisible']=='1'))){//2021/8/25 增加過濾"顯示於POS"類別、過濾"顯示於POS"品項
								if($i<sizeof($items)){
									if((isset($itemname[$items[$i]['inumber']]['name1'])&&trim($itemname[$items[$i]['inumber']]['name1'])!='')||(isset($itemname[$items[$i]['inumber']]['name2'])&&trim($itemname[$items[$i]['inumber']]['name2'])!='')){
										echo "<div class='item' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);'>
												<button value='".$itemname[$items[$i]['inumber']]['name1'].$itemname[$items[$i]['inumber']]['name2']."' style='";if(isset($itemname[$items[$i]['inumber']]['bgcolor']))echo 'background-color:'.$itemname[$items[$i]['inumber']]['bgcolor'].';';echo "'><div id='name1' style='float:left;width:100%;";if(isset($itemname[$items[$i]['inumber']]['size1']))echo 'font-size:'.$itemname[$items[$i]['inumber']]['size1'].'px;';if(isset($itemname[$items[$i]['inumber']]['color1']))echo 'color:'.$itemname[$items[$i]['inumber']]['color1'].';';if(isset($itemname[$items[$i]['inumber']]['bold1'])&&$itemname[$items[$i]['inumber']]['bold1']==1)echo 'font-weight:bold;';else echo 'font-weight:normal;';echo "'>".$itemname[$items[$i]['inumber']]['name1']."</div><div id='name2' style='float:left;width:100%;";if(isset($itemname[$items[$i]['inumber']]['size2']))echo 'font-size:'.$itemname[$items[$i]['inumber']]['size2'].'px;';if(isset($itemname[$items[$i]['inumber']]['color2']))echo 'color:'.$itemname[$items[$i]['inumber']]['color2'].';';if(isset($itemname[$items[$i]['inumber']]['bold2'])&&$itemname[$items[$i]['inumber']]['bold2']==1)echo 'font-weight:bold;';else echo 'font-weight:normal;';echo "'>".$itemname[$items[$i]['inumber']]['name2']."</div></button>
												<input type='hidden' name='no' value='".$items[$i]['inumber']."'>
												<input type='hidden' name='typeno' value='".$items[$i]['fronttype']."'>
											</div>";
									}
									else{
										echo "<div class='item' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);'>
												<button value='' id='empty' disabled><div id='name1' style='float:left;width:100%;";if(isset($itemname[$items[$i]['inumber']]['size1']))echo 'font-size:'.$itemname[$items[$i]['inumber']]['size1'].'px;';if(isset($itemname[$items[$i]['inumber']]['color1']))echo 'color:'.$itemname[$items[$i]['inumber']]['color1'].';';if(isset($itemname[$items[$i]['inumber']]['bold1'])&&$itemname[$items[$i]['inumber']]['bold1']==1)echo 'font-weight:bold;';else echo 'font-weight:normal;';echo "'></div><div id='name2' style='float:left;width:100%;";if(isset($itemname[$items[$i]['inumber']]['size2']))echo 'font-size:'.$itemname[$items[$i]['inumber']]['size2'].'px;';if(isset($itemname[$items[$i]['inumber']]['color2']))echo 'color:'.$itemname[$items[$i]['inumber']]['color2'].';';if(isset($itemname[$items[$i]['inumber']]['bold2'])&&$itemname[$items[$i]['inumber']]['bold2']==1)echo 'font-weight:bold;';else echo 'font-weight:normal;';echo "'></div></button>
											</div>";
									}
								}
								else{
									echo "<div class='item' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);'>
												<button value='' id='empty' disabled><div id='name1'></div><div id='name2'></div></button>
											</div>";
								}
							}
							else{
								$i--;
							}
							$j++;
						}
						for($i=0;$i<(intval($maxcol*$maxrow)-intval($j));$i++){
							echo "<div class='item' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);'>
									<button value='' id='empty' disabled><div id='name1'></div><div id='name2'></div></button>
								</div>";
						}
					}
					else{
						for($i=0;$i<$maxrow;$i++){
							for($j=0;$j<$maxcol;$j++){
								echo "<div class='item' style='width:calc(100% / ".$maxcol." - 2px);height:calc(100% / ".$maxrow." - 2px);'>
										<button value='' id='empty' disabled><div id='name1'></div><div id='name2'></div></button>
									</div>";			
							}
						}
					}				
					?>
				</div>
				<div id="itemfun">
					<input class='itemfun' type='button' style='display:none;'>
					<table>
						<tr>
							<td>
								<button class='itemmname1' value="1" disabled><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='itemmoney1' value=''>
								<input type='hidden' class='getpointtype1' value=''>
								<input type='hidden' class='getpoint1' value=''>
							</td>
							<td>
								<button class='itemmname2' value="2" disabled><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='itemmoney2' value=''>
								<input type='hidden' class='getpointtype2' value=''>
								<input type='hidden' class='getpoint2' value=''>
							</td>
							<td>
								<button class='itemmname3' value="3" disabled><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='itemmoney3' value=''>
								<input type='hidden' class='getpointtype3' value=''>
								<input type='hidden' class='getpoint3' value=''>
							</td>
							<td>
								<button class='itemmname4' value="4" disabled><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='itemmoney4' value=''>
								<input type='hidden' class='getpointtype4' value=''>
								<input type='hidden' class='getpoint4' value=''>
							</td>
							<td>
								<button class='itemmname5' value="5" disabled><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='itemmoney5' value=''>
								<input type='hidden' class='getpointtype5' value=''>
								<input type='hidden' class='getpoint5' value=''>
							</td>
							<td>
								<button class='itemmname6' value="6" disabled><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='itemmoney6' value=''>
								<input type='hidden' class='getpointtype6' value=''>
								<input type='hidden' class='getpoint6' value=''>
							</td>
						</tr>
						<tr>
							<td>
								<button id='taste0' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno0' value=''>
								<input type='hidden' class='tasteprice0' value=''>
							</td>
							<td>
								<button id='taste1' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno1' value=''>
								<input type='hidden' class='tasteprice1' value=''>
							</td>
							<td>
								<button id='taste2' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno2' value=''>
								<input type='hidden' class='tasteprice2' value=''>
							</td>
							<td>
								<button id='taste3' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno3' value=''>
								<input type='hidden' class='tasteprice3' value=''>
							</td>
							<td>
								<button id='taste4' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno4' value=''>
								<input type='hidden' class='tasteprice4' value=''>
							</td>
							<td>
								<button id='changetaste' value="換頁" style='color:#000000;background-color: #FCD7A3;' disabled><?php if($buttons1!='-1'&&isset($buttons1['name']['changepage']))echo "<div id='name1'>".$buttons1['name']['changepage']."</div>";else echo "<div id='name1'>換頁</div>";if($buttons2!='-1'&&isset($buttons2['name']['changepage']))echo "<div id='name2'>".$buttons2['name']['changepage']."</div>";else; ?></button>
								<input type='hidden' class='startchangetaste' value='0'>
							</td>
						</tr>
						<tr>
							<td>
								<button id='taste5' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno5' value=''>
								<input type='hidden' class='tasteprice5' value=''>
							</td>
							<td>
								<button id='taste6' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno6' value=''>
								<input type='hidden' class='tasteprice6' value=''>
							</td>
							<td>
								<button id='taste7' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno7' value=''>
								<input type='hidden' class='tasteprice7' value=''>
							</td>
							<td>
								<button id='taste8' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno8' value=''>
								<input type='hidden' class='tasteprice8' value=''>
							</td>
							<td>
								<button id='taste9' value="" style='color:#000000;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' class='tasteno9' value=''>
								<input type='hidden' class='tasteprice9' value=''>
							</td>
							<td><button class='itemfun12' value=">>其他" style='color:#000000;background-color: #FCD7A3;'><?php if($buttons1!='-1')echo "<div id='name1'>>>".$buttons1['name']['taste']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>>>".$buttons2['name']['taste']."</div>";else; ?></button></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<!-- 跳出視窗 -->
		<div class='dblock' style='text-align:center;' title='<?php if(isset($interface1['name']['38']))echo $interface1['name']['38'];if(isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo ' /'.$interface2['name']['38'];else if(!isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo $interface2['name']['38'];else; ?>'>
			<?php 
			if(isset($interface1['name']['dblock'])){
				echo "<div id='name1' style='margin-bottom:30px;'>".$interface1['name']['dblock']."</div>";
			}
			?>
			<?php
			if(isset($interface2['name']['dblock'])){
				echo "<div id='name2' style='margin-bottom:30px;'>".$interface2['name']['dblock']."</div>";
			}
			?>
			<br>
			<button class="check" value="確認" style='width:105px;margin-right:10px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['42']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['42']."</div>"; ?></button>
		</div>
		<div class='sysmeg' style='text-align:center;' title='<?php if(isset($interface1['name']['38']))echo $interface1['name']['38'];if(isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo ' /'.$interface2['name']['38'];else if(!isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo $interface2['name']['38'];else; ?>'>
			<?php 
			if($interface1!='-1'){
				echo "<div id='name1' class='syshint1' style='display:none;'>".$interface1['name']['syshint1']."</div>"; 
				echo "<div id='name1' class='syshint2' style='display:none;'>".$interface1['name']['syshint2']."</div>"; 
				echo "<div id='name1' class='syshint3' style='display:none;'>".$interface1['name']['syshint3']."</div>"; 
				echo "<div id='name1' class='syshint4' style='display:none;'>".$interface1['name']['syshint4']."</div>"; 
				echo "<div id='name1' class='syshint5' style='display:none;'>".$interface1['name']['syshint5']."</div>"; 
				echo "<div id='name1' class='syshint6' style='display:none;'>".$interface1['name']['syshint6']."</div>"; 
				echo "<div id='name1' class='syshint7' style='display:none;'>".$interface1['name']['syshint7']."</div>"; 
				echo "<div id='name1' class='syshint8' style='display:none;'>".$interface1['name']['syshint8']."</div>";
				echo "<div id='name1' class='syshint9' style='display:none;'>";
				if(isset($interface1['name']['syshint9'])){
					echo $interface1['name']['syshint9'];
				}
				else{
					echo "該帳單客人尚有外帶單未結帳。"; 
				}
				echo "</div>"; 
				echo "<div id='name1' class='syshint10' style='display:none;'>";
				if(isset($interface1['name']['syshint10'])){
					echo $interface1['name']['syshint10'];
				}
				else{
					echo "請先將英特拉付款清除（退款）"; 
				}
				echo "</div>";
				echo "<div id='name1' class='syshint11' style='display:none;'>";
				if(isset($interface1['name']['syshint11'])){
					echo $interface1['name']['syshint11'];
				}
				else{
					echo "不正確的付款操作。"; 
				}
				echo "</div>";
				echo "<div id='name1' class='syshint12' style='display:none;'>";
				if(isset($interface1['name']['syshint12'])){
					echo $interface1['name']['syshint12'];
				}
				else{
					echo "請先將信用卡付款清除（NCCC退款）"; 
				}
				echo "</div>";
				echo "<div id='name1' class='syshint13' style='display:none;'>";
				if(isset($interface1['name']['syshint13'])){
					echo $interface1['name']['syshint13'];
				}
				else{
					echo "請先將LinePay付款清除（退款）"; 
				}
				echo "</div>";
				echo "<div id='name1' class='syshint14' style='display:none;'>";
				if(isset($interface1['name']['syshint14'])){
					echo $interface1['name']['syshint14'];
				}
				else{
					echo "Nidin伺服器連線失敗，關閉自動連線。<br>若要重新連線，請切換人員後重新登入POS。"; 
				}
				echo "</div>";
			}
			?>
			<?php
			if($interface2!='-1'){
				echo "<div id='name2' class='syshint1' style='display:none;'>".$interface2['name']['syshint1']."</div>";
				echo "<div id='name2' class='syshint2' style='display:none;'>".$interface2['name']['syshint2']."</div>";
				echo "<div id='name2' class='syshint3' style='display:none;'>".$interface2['name']['syshint3']."</div>";
				echo "<div id='name2' class='syshint4' style='display:none;'>".$interface2['name']['syshint4']."</div>";
				echo "<div id='name2' class='syshint5' style='display:none;'>".$interface2['name']['syshint5']."</div>";
				echo "<div id='name2' class='syshint6' style='display:none;'>".$interface2['name']['syshint6']."</div>";
				echo "<div id='name2' class='syshint7' style='display:none;'>".$interface2['name']['syshint7']."</div>";
				echo "<div id='name2' class='syshint8' style='display:none;'>".$interface2['name']['syshint8']."</div>"; 
				echo "<div id='name1' class='syshint9' style='display:none;'>";
				if(isset($interface2['name']['syshint9'])){
					echo $interface2['name']['syshint9']; 
				}
				else{
					echo "該帳單客人尚有外帶單未結帳。"; 
				}
				echo "</div>"; 
				echo "<div id='name2' class='syshint10' style='display:none;'>";
				if(isset($interface2['name']['syshint10'])){
					echo $interface2['name']['syshint10']; 
				}
				else{
					echo "請先將英特拉付款清除（退款）"; 
				}
				echo "</div>"; 
				echo "<div id='name2' class='syshint11' style='display:none;'>";
				if(isset($interface2['name']['syshint11'])){
					echo $interface2['name']['syshint11']; 
				}
				else{
					echo "不正確的付款操作。"; 
				}
				echo "</div>"; 
				echo "<div id='name2' class='syshint12' style='display:none;'>";
				if(isset($interface1['name']['syshint12'])){
					echo $interface1['name']['syshint12'];
				}
				else{
					echo "請先將信用卡付款清除（NCCC退款）"; 
				}
				echo "</div>";
				echo "<div id='name2' class='syshint13' style='display:none;'>";
				if(isset($interface1['name']['syshint13'])){
					echo $interface1['name']['syshint13'];
				}
				else{
					echo "請先將LinePay付款清除（退款）"; 
				}
				echo "</div>";
				echo "<div id='name2' class='syshint14' style='display:none;'>";
				if(isset($interface1['name']['syshint14'])){
					echo $interface1['name']['syshint14'];
				}
				else{
					echo "Nidin伺服器連線失敗，關閉自動連線。<br>若要重新連線，請切換人員後重新登入POS。"; 
				}
				echo "</div>";
			}
			?>
			<br>
			<span id='list' style='border-top:1px #898989 solid;'></span>
			<br>
			<button class="closesysmeg" value="確認" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['42']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['42']."</div>"; ?></button>
		</div>
		<div class='alertmessage' style='text-align:center;' title='<?php if(isset($interface1['name']['38']))echo $interface1['name']['38'];if(isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo ' /'.$interface2['name']['38'];else if(!isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo $interface2['name']['38'];else; ?>'>
			<div id='text'></div>
			<button class='check' style='width:150px;height:40px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['42']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['42']."</div>"; ?></button>
		</div>
		<div class='nidinalertmessage' style='text-align:center;'>
			<div id='text'></div>
		</div>
		<div class='exitsys' style='text-align:center;' title='<?php if(isset($interface1['name']['38']))echo $interface1['name']['38'];if(isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo ' /'.$interface2['name']['38'];else if(!isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo $interface2['name']['38'];else; ?>'>
			<div id="name1">Close system now?</div>
			<br>
			<button class="yes" value="確認" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['20']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['20']."</div>"; ?></button>
			<button class="no" value="取消" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['21']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['21']."</div>"; ?></button>
		</div>
		<div class='sysmeg4' style='text-align:center;' title='<?php if(isset($interface1['name']['38']))echo $interface1['name']['38'];if(isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo ' /'.$interface2['name']['38'];else if(!isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo $interface2['name']['38'];else; ?>'>
			<div id='name1'><?php if(isset($interface1['name']['checknotsale']))echo $interface1['name']['checknotsale'];else echo '目前尚有未結帳單，是否確認交班？'; ?></div>
			<br>
			<button class="check" value="確認" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['42']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['42']."</div>"; ?></button>
			<button class="closesysmeg" value="取消" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['41']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['41']."</div>"; ?></button>
		</div>
		<div class='sysmeg5' style='text-align:center;' title='<?php if(isset($interface1['name']['38']))echo $interface1['name']['38'];if(isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo ' /'.$interface2['name']['38'];else if(!isset($interface1['name']['38'])&&isset($interface2['name']['38']))echo $interface2['name']['38'];else; ?>'>
			<div id='name1'><?php if(isset($interface1['name']['checkclose']))echo $interface1['name']['checkclose'];else echo '是否確認交班？'; ?></div>
			<br>
			<button class="check" value="確認" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['42']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['42']."</div>"; ?></button>
			<button class="closesysmeg" value="取消" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['41']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['41']."</div>"; ?></button>
		</div>
		<div class='setperson' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if(isset($interface1['name']['setperson']))echo $interface1['name']['setperson'];else echo '人數設定'; ?>'>
			<div id='view' style='width:100%;height:86px;background-color:#ffffff;overflow:hidden;padding:10px 0;'>
				<?php
				if(file_exists('../database/floorspend.ini')){
					$floorspend=parse_ini_file('../database/floorspend.ini',true);
					$tag='';
					?>
				<table style='width:100%;height:100%;'>
					<tr>
						<td rowspan='2' style='width:25%;height:50%;text-align:center;'><?php if(isset($interface1['name']['setperson']))echo $interface1['name']['setperson'];else echo '人數設定'; ?></td>
						<td style='width:25%;height:50%;text-align:center;'><?php if(isset($floorspend['person1']['name'])&&$floorspend['person1']['name']!=''){echo $floorspend['person1']['name'].'(';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person1']['floor'];else echo '0';echo ')<input type="hidden" name="personfloor1" value="';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person1']['floor'];else echo '0';echo '">';}else{echo '<input type="hidden" name="personfloor1" value="';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person1']['floor'];else echo '0';echo '">';} ?></td>
						<td style='width:25%;height:50%;text-align:center;'><?php if(isset($floorspend['person2']['name'])&&$floorspend['person2']['name']!=''){echo $floorspend['person2']['name'].'(';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person2']['floor'];else echo '0';echo ')<input type="hidden" name="personfloor2" value="';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person2']['floor'];else echo '0';echo '">';}else{echo '<input type="hidden" name="personfloor2" value="';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person2']['floor'];else echo '0';echo '">';} ?></td>
						<td style='width:25%;height:50%;text-align:center;'><?php if(isset($floorspend['person3']['name'])&&$floorspend['person3']['name']!=''){echo $floorspend['person3']['name'].'(';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person3']['floor'];else echo '0';echo ')<input type="hidden" name="personfloor3" value="';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person3']['floor'];else echo '0';echo '">';}else{echo '<input type="hidden" name="personfloor3" value="';if(isset($initsetting['init']['openpersoncount'])&&$initsetting['init']['openpersoncount']==1&&isset($initsetting['init']['openfloor'])&&$initsetting['init']['openfloor']==1)echo $floorspend['person3']['floor'];else echo '0';echo '">';} ?></td>
					</tr>
					<tr>
						<td style='width:25%;height:50%;text-align:center;'><?php if(isset($floorspend['person1']['name'])&&$floorspend['person1']['name']!=''){if($tag=='')$tag='persons1';echo '<input type="text" style="width:70%;height:100%;text-align:center;';if($tag!='persons1')echo 'background-color:#fbf4ec;';echo '" name="persons1" data-id="person" value="0" readonly>';}else ; ?></td>
						<td style='width:25%;height:50%;text-align:center;'><?php if(isset($floorspend['person2']['name'])&&$floorspend['person2']['name']!=''){if($tag=='')$tag='persons2';echo '<input type="text" style="width:70%;height:100%;text-align:center;';if($tag!='persons2')echo 'background-color:#fbf4ec;';echo '" name="persons2" data-id="person" value="0" readonly>';}else ; ?></td>
						<td style='width:25%;height:50%;text-align:center;'><?php if(isset($floorspend['person3']['name'])&&$floorspend['person3']['name']!=''){if($tag=='')$tag='persons3';echo '<input type="text" style="width:70%;height:100%;text-align:center;';if($tag!='persons3')echo 'background-color:#fbf4ec;';echo '" name="persons3" data-id="person" value="0" readonly>';}else ; ?></td>
					</tr>
				</table>
				<?php
				}
				else{
				}
				?>
				
			</div>
			<div id='buttons' style='width:100%;height:calc(100% - 98px);margin-top:2px;'>
				<input type='hidden' name='tag' value='<?php echo $tag; ?>'>
				<input type='button' class='input' style='font-size: 2.5em;' value='7'>
				<input type='button' class='input' style='font-size: 2.5em;' value='8'>
				<input type='button' class='input' style='font-size: 2.5em;' value='9'>
				<input type='button' id='pre' class='pre' style='font-size: 2.5em;' value='' disabled>
				<input type='button' class='input' style='font-size: 2.5em;' value='4'>
				<input type='button' class='input' style='font-size: 2.5em;' value='5'>
				<input type='button' class='input' style='font-size: 2.5em;' value='6'>
				<input type='button' id='next' class='next' style='font-size: 2.5em;' value='' disabled>
				<input type='button' class='input' style='font-size: 2.5em;' value='1'>
				<input type='button' class='input' style='font-size: 2.5em;' value='2'>
				<input type='button' class='input' style='font-size: 2.5em;' value='3'>
				<input type='button' id='back' style='font-size: 2.5em;' value='刪除'>
				<input type='button' class='input' style='font-size: 2.5em;' value='0'>
				<input type='button' class='input' style='font-size: 2.5em;' value='' disabled>
				<input type='button' class='input' style='font-size: 2.5em;' value='' disabled>
				<input type='button' id='ac' style='font-size: 2.5em;' value= '<?php if(isset($buttons1['numberpad']['ac']))echo $buttons1['numberpad']['ac'];else echo '重填' ?>'>
				<button id='submit' style='font-size: 2.5em;' value='確定'><?php if(isset($buttons1['numberpad']['submit']))echo $buttons1['numberpad']['submit'];else echo '確定' ?></button>
				<button id='cancel' style='font-size: 2.5em;' value='取消'><?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?></button>
			</div>
		</div>
		<div class='emptyitemsvoidlist' style='text-align:center;' title='<?php if(isset($interface1['name']['emptyitemsvoidlist']))echo $interface1['name']['emptyitemsvoidlist'];else echo '作廢帳單確認視窗';if(isset($interface1['name']['emptyitemsvoidlist'])&&isset($interface2['name']['emptyitemsvoidlist']))echo ' /'.$interface2['name']['emptyitemsvoidlist'];else if(!isset($interface1['name']['emptyitemsvoidlist'])&&isset($interface2['name']['emptyitemsvoidlist']))echo $interface2['name']['emptyitemsvoidlist'];else; ?>'>
			<div id='name1'><?php if(isset($interface1['name']['emptyitemsvoidlistcontent']))echo $interface1['name']['emptyitemsvoidlistcontent'];else echo '現在返回桌控會將目前帳單作廢，<br>請問要作廢帳單嗎？'; ?></div>
			<br>
			<button class="void" value="確認" style='width:105px;height:70px;'><div id='name1'><?php if(isset($buttons1['name']['emptyitemsbutton1']))echo $buttons1['name']['emptyitemsbutton1'];else echo '作廢帳單'; ?></div><?php if(isset($buttons2['name']['emptyitemsbutton1']))echo "<div id='name2'>".$buttons2['name']['emptyitemsbutton1']."</div>"; ?></button>
			<button class="return" value="取消" style='width:105px;height:70px;'><div id='name1'><?php if(isset($buttons1['name']['emptyitemsbutton2']))echo $buttons1['name']['emptyitemsbutton2'];else echo '返回桌控'; ?></div><?php if(isset($buttons2['name']['emptyitemsbutton2']))echo "<div id='name2'>".$buttons2['name']['emptyitemsbutton2']."</div>"; ?></button>
		</div>
		<div class='oinv' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='設定發票'>
			<div id='view' style='width:100%;overflow:hidden;padding:10px 0;'>
				<div style='width:80px;margin:0 10px;float:left;line-height:45px;'>
					<span>起始發票</span>
				</div>
				<div style='width:calc((100% - 120px) / 6);float:left;overflow:hidden;'><input type='text' style='width:calc(100% - 32px);padding:10px 5px;margin:0 10px;float:left;text-align:right;<?php if(strlen($machinedata['basic']['startoinv'])==10)echo 'background-color:#eeeeee;';else; ?>' name='en' value='<?php if(strlen($machinedata['basic']['startoinv'])==10)echo substr($machinedata['basic']['startoinv'],0,2);else; ?>' <?php if(strlen($machinedata['basic']['startoinv'])==10);else echo 'autofocus'; ?> readonly></div>
				<div style='width:20px;float:left;line-height:45px;'>－</div>
				<div style='width:calc((100% - 120px) / 2);float:left;overflow:hidden;'><input type='text' style='width:calc(100% - 32px);padding:10px 5px;margin:0 10px;float:left;<?php if(strlen($machinedata['basic']['startoinv'])==10);else echo 'background-color:#eeeeee;'; ?>' name='num' value='<?php if(strlen($machinedata['basic']['startoinv'])==10)echo substr($machinedata['basic']['startoinv'],2,8);else; ?>' <?php if(strlen($machinedata['basic']['startoinv'])==10) echo 'autofocus';else; ?> readonly></div>
			</div>
			<div id='buttons' style='width:100%;height:calc(100% - 65px);margin-top:2px;'>
				<input type='hidden' name='tag' value='<?php if(strlen($machinedata['basic']['startoinv'])==10)echo 'num';else echo 'en'; ?>'>
				<input type='button' class='input' value='7'>
				<input type='button' class='input' value='8'>
				<input type='button' class='input' value='9'>
				<input type='button' id='pre' class='pre' style='background-color: #c6d3e3;' value='Pre' disabled>
				<input type='button' class='input' value='4'>
				<input type='button' class='input' value='5'>
				<input type='button' class='input' value='6'>
				<input type='button' id='next' class='next' style='background-color: #c6d3e3;' value='Next' disabled>
				<input type='button' class='input' value='1'>
				<input type='button' class='input' value='2'>
				<input type='button' class='input' value='3'>
				<input type='button' id='back' style='background-color: #c6d3e3;' value='刪除'>
				<input type='button' class='input' value='0'>
				<input type='button' class='input' value='' disabled>
				<input type='button' class='input' value='' disabled>
				<input type='button' id='ac' style='background-color: #c6d3e3;' value='<?php if(isset($buttons1['numberpad']['ac']))echo $buttons1['numberpad']['ac'];else echo '重填' ?>'>
				<button id='submit' style='background-color: #c6d3e3;' value='確定'><?php if(isset($buttons1['numberpad']['submit']))echo $buttons1['numberpad']['submit'];else echo '確定' ?></button>
				<button id='cancel' style='background-color: #c6d3e3;' value='取消'><?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?></button>
			</div>
		</div>
		<div class='cancelinv' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='作廢發票'>
			<div id='view' style='width:100%;background-color:#ffffff;overflow:hidden;padding:10px 0;'>
				<div style='width:80px;margin:0 10px;float:left;line-height:45px;'>
					<span>作廢發票</span>
				</div>
				<div style='width:calc((100% - 120px) / 6);float:left;overflow:hidden;'><input type='text' style='width:calc(100% - 32px);padding:10px 5px;margin:0 10px;float:left;text-align:right;<?php if(strlen($machinedata['basic']['startoinv'])==10)echo 'background-color:#eeeeee;';else; ?>' name='en' value='<?php if(strlen($machinedata['basic']['startoinv'])==10)echo substr($machinedata['basic']['startoinv'],0,2);else; ?>' <?php if(strlen($machinedata['basic']['startoinv'])==10);else echo 'autofocus'; ?> readonly></div>
				<div style='width:20px;float:left;line-height:45px;'>－</div>
				<div style='width:calc((100% - 120px) / 2);float:left;overflow:hidden;'><input type='text' style='width:calc(100% - 32px);padding:10px 5px;margin:0 10px;float:left;<?php if(strlen($machinedata['basic']['startoinv'])==10);else echo 'background-color:#eeeeee;'; ?>' name='num' value='<?php if(strlen($machinedata['basic']['startoinv'])==10)echo substr($machinedata['basic']['startoinv'],2,8);else; ?>' <?php if(strlen($machinedata['basic']['startoinv'])==10) echo 'autofocus';else; ?> readonly></div>
			</div>
			<div id='buttons' style='width:100%;height:calc(100% - 65px);margin-top:2px;'>
				<input type='hidden' name='tag' value='<?php if(strlen($machinedata['basic']['startoinv'])==10)echo 'num';else echo 'en'; ?>'>
				<input type='button' class='input' value='7'>
				<input type='button' class='input' value='8'>
				<input type='button' class='input' value='9'>
				<input type='button' id='pre' class='pre' value='Pre' disabled>
				<input type='button' class='input' value='4'>
				<input type='button' class='input' value='5'>
				<input type='button' class='input' value='6'>
				<input type='button' id='next' class='next' value='Next' disabled>
				<input type='button' class='input' value='1'>
				<input type='button' class='input' value='2'>
				<input type='button' class='input' value='3'>
				<input type='button' id='back' value='刪除'>
				<input type='button' class='input' value='0'>
				<input type='button' class='input' value='' disabled>
				<input type='button' class='input' value='' disabled>
				<input type='button' id='ac' value='<?php if(isset($buttons1['numberpad']['ac']))echo $buttons1['numberpad']['ac'];else echo '重填' ?>'>
				<button id='submit' value='確定'><?php if(isset($buttons1['numberpad']['submit']))echo $buttons1['numberpad']['submit'];else echo '確定' ?></button>
				<button id='cancel' value='取消'><?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?></button>
			</div>
		</div>
		<div class='result' style='padding:15px;overflow-x:hidden;overflow-y:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if(isset($interface1['name']['23']))echo $interface1['name']['23'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['23'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['23'];else; ?>'>
			<div style='width:calc(50% - 245px);height:100%;float:left;overflow:hidden;'>
				<input type='text' name='view' style='width:100%;height:50px;font-size:25px;margin-bottom:1px;padding:0 5px;background-color:#ffffff;float:left;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>>
				<div class='numbox' style='width:100%;height:calc(100% - 51px);overflow:hidden;float:left;'>
					<button class='ban' style='width:calc(100% / 4 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;<?php if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0')echo 'visibility: hidden;'; ?>' ><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['ban']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['ban']."</div>"; ?></button>
					<button class='carrier' style='width:calc(100% / 4 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;<?php if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0')echo 'visibility: hidden;'; ?>'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['carrier']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['carrier']."</div>"; ?></button>
					<button class='carrier2' style='width:calc(100% / 4 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;<?php if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0')echo 'visibility: hidden;'; ?>'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['carrier2']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['carrier2']."</div>"; ?></button>
					<button class='donate' style='width:calc(100% / 4 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;<?php if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0')echo 'visibility: hidden;'; ?>'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['donate']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['donate']."</div>"; ?></button>
					<input type='button' value='7' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='8' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='9' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='4' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='5' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='6' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='1' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='2' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='3' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='0' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'>
					<input type='button' value='.' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;' <?php if(isset($initsetting['init']['accuracy'])&&intval($initsetting['init']['accuracy'])>='1');else echo 'disabled'; ?>>
					<button class='clear' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['22']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['22']."</div>"; ?></button>
					<?php
					if(isset($otherpay['pay']['openpay'])&&$otherpay['pay']['openpay']=='1'&&sizeof($otherpay)<=3&&sizeof($otherpay)>1){
						for($otindex=1;$otindex<sizeof($otherpay);$otindex++){
							echo "<button class='otherfunction' id='".$otherpay['item'.$otindex]['dbname']."' value='".$otherpay['item'.$otindex]['name']."' style='width:calc(100% / ".(sizeof($otherpay)+1)." - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>";
							if(isset($otherpay['item'.$otindex]['name'])&&$otherpay['item'.$otindex]['name']!='')echo "<div id='name1'>".$otherpay['item'.$otindex]['name']."</div>";
							if(isset($otherpay['item'.$otindex]['name2'])&&$otherpay['item'.$otindex]['name2']!='')echo "<div id='name2'>".$otherpay['item'.$otindex]['name2']."</div>";
							if(isset($otherpay['item'.$otindex]['directlinepay'])&&$otherpay['item'.$otindex]['directlinepay']=='1'){
								echo "<input type='hidden' name='directlinepay' value='1'>";
							}
							else{
								echo "<input type='hidden' name='directlinepay' value='0'>";
							}
							if(isset($otherpay['item'.$otindex]['jkos'])&&$otherpay['item'.$otindex]['jkos']=='1'){
								echo "<input type='hidden' name='jkos' value='1'>";
							}
							else{
								echo "<input type='hidden' name='jkos' value='0'>";
							}
							if(isset($otherpay['item'.$otindex]['pxpayplus'])&&$otherpay['item'.$otindex]['pxpayplus']=='1'){
								echo "<input type='hidden' name='pxpayplus' value='1'>";
							}
							else{
								echo "<input type='hidden' name='pxpayplus' value='0'>";
							}
							if(isset($otherpay['item'.$otindex]['fromdb'])){
								echo "<input type='hidden' name='fromdb' value='".$otherpay['item'.$otindex]['fromdb']."'>";
							}
							else{
							}
							if(isset($otherpay['item'.$otindex]['should'])){
								echo "<input type='hidden' name='should' value='".$otherpay['item'.$otindex]['should']."'>";
							}
							else{
							}
							if(isset($otherpay['item'.$otindex]['pay'])){
								echo "<input type='hidden' name='pay' value='".$otherpay['item'.$otindex]['pay']."'>";
							}
							else{
							}
							if(!isset($otherpay['item'.$otindex]['location'])){
								echo "<input type='hidden' name='location' value='CST011'>";
							}
							else{
								echo "<input type='hidden' name='location' value='".$otherpay['item'.$otindex]['location']."'>";
							}
							echo "<input type='hidden' name='name' value='".$otherpay['item'.$otindex]['name']."'>";
							if(!isset($otherpay['item'.$otindex]['inv'])){
								echo "<input type='hidden' name='inv' value='1'>";
							}
							else{
								echo "<input type='hidden' name='inv' value='".$otherpay['item'.$otindex]['inv']."'>";
							}
							if(!isset($otherpay['item'.$otindex]['price'])||floatval($otherpay['item'.$otindex]['price'])<'0'){
								echo "<input type='hidden' name='price' value='1'>";
							}
							else{
								echo "<input type='hidden' name='price' value='".$otherpay['item'.$otindex]['price']."'>";
							}
							if(!isset($otherpay['item'.$otindex]['type'])){
								echo "<input type='hidden' name='type' value='1'>";
							}
							else{
								echo "<input type='hidden' name='type' value='".$otherpay['item'.$otindex]['type']."'>";
							}
							echo "</button>";
						}
					?>
						<button class='cashbut' value='信用卡' style='width:calc(100% / <?php echo (sizeof($otherpay)+1); ?> - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['49']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['49']."</div>"; ?></button>
						<button class='moneybut' value='現金' style='width:calc(100% / <?php echo (sizeof($otherpay)+1); ?> - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['37']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['37']."</div>"; ?></button>
					<?php
					}
					else if(isset($otherpay['pay']['openpay'])&&$otherpay['pay']['openpay']=='1'&&sizeof($otherpay)>3){
					?>
						<button class='otherpay' value='其他付款' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['otherpay']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['otherpay']."</div>"; ?></button>
						<button class='cashbut' value='信用卡' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['49']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['49']."</div>"; ?></button>
						<button class='moneybut' value='現金' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['37']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['37']."</div>"; ?></button>
					<?php
					}
					else{
					?>
						<button class='otherpay' value='其他付款' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['otherpay']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['otherpay']."</div>"; ?></button>
						<button class='cashbut' value='信用卡' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['49']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['49']."</div>"; ?></button>
						<button class='moneybut' value='現金' style='width:calc(100% / 3 - 2px);height:calc(100% / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['37']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['37']."</div>"; ?></button>
					<?php
					}
					?>
				</div>
			</div>
			<div style='width:calc(50% + 245px);height:100%;overflow:hidden;float:left;'>
				<input type='hidden' class='target' value='view'>
				<input type='hidden' class='caninilistdis' value='0'>
				<input type='hidden' class='canlistdis' value='0'>
				<input type='hidden' class='cannotlistdis' value='0'>
				<input type='hidden' class='canusemempoint' value='0'>
				<input type='hidden' class='usepoint' value='0'>
				<input type='hidden' class='frontunit' value='<?php echo $initsetting['init']['frontunit']; ?>'>
				<input type='hidden' class='unit' value='<?php echo $initsetting['init']['unit']; ?>'>
				<!-- <div class='numbox' style='width:15%;height:100%;float:left;'>
					<button value='清除' style='width:100%;height:calc(100% / 13);margin:0 0 20px 0;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['22']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['22']."</div>"; ?></button>
					<input type='button' value='1' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='2' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='3' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='4' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='5' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='6' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='7' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='8' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='9' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='0' style='width:100%;height:calc(100% / 13);'>
					<input type='button' value='.' style='width:100%;height:calc(100% / 13);'>
				</div> -->
				<div style='width:100%;height:100%;float:left;'>
					<div style='width:490px;height:100%;float:left;padding:0 2px;position: relative;'>
						<!-- <input type='text' name='view' style='width:calc(100% - 10px);margin:0 5px 5px 5px;text-align:right;background-color:#ffffff;' onfocus> -->
						<table id='funbox'>
							<tr>
								<td>
									<?php
									if($initsetting['init']['listprint']==1){
										echo "<button id='loopbut' value='出單'>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['23']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['23']."</div>";
										echo "</button>";
									}
									else if($initsetting['init']['listprint']==2){
										echo "<button id='loopbut' value='不出單'>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['24']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['24']."</div>";
										echo "</button>";
									}
									else if($initsetting['init']['listprint']==3){
										echo "<button id='loopbut' value='只出總單'>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['25']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['25']."</div>";
										echo "</button>";
									}
									else{
										echo "<button id='loopbut' value='只出標籤'>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['26']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['26']."</div>";
										echo "</button>";
									}
									?>
								</td>
								<td>
									<button id='listdisbutA' value='帳單折扣'<?php if($initsetting['init']['orderdis']==0)echo " disabled"; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['35']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['35']."</div>"; ?></button>
								</td>
								<td>
									<button id='listdisbutB' value='帳單折讓'<?php if($initsetting['init']['orderdisnum']==0)echo " disabled"; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['36']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['36']."</div>"; ?></button>
								</td>
							</tr>
							<tr>
								<td>
									<?php
									if(isset($itemdis['listdis'])&&isset($itemdis['listdis']['state1'])){//2022/3/17
										if($itemdis['listdis']['state1']==='1'){
											echo "<button class='listdisbut1' value='扣折'>";
											if($itemdis['listdis']['name11']!='')echo "<div id='name1'>".$itemdis['listdis']['name11']."</div>";
											if($itemdis['listdis']['name12']!='')echo "<div id='name2'>".$itemdis['listdis']['name12']."</div>";
											echo "</button>
												<input type='hidden' class='listdis1' value='".$itemdis['listdis']['number1']."'>
												<input type='hidden' class='listdistype1' value='".$itemdis['listdis']['type1']."'>";
										}
										else{
											echo "<button class='listdisbut1' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis1' value=''>";

										}
									}
									else if($initsetting['init']['disbut1']==0){
										echo "<button class='listdisbut1' value='無扣折' disabled>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
										echo "</button>
											<input type='hidden' class='listdis1' value=''>";
									}
									else{
										if(intval($initsetting['init']['disnum1'])>0){
											//if($initsetting['init']['disnum1']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
												echo "<button class='listdisbut1' value='";
												if(isset($initsetting['init']['disname11'])&&$initsetting['init']['disname11']!=""){
													echo $initsetting['init']['disname11'];
												}
												else if(isset($initsetting['init']['disname12'])&&$initsetting['init']['disname12']!=""){
													echo $initsetting['init']['disname12'];
												}
												else if($initsetting['init']['disnum1']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
													echo ($initsetting['init']['disnum1']/10)."折";
												}
												else{
													echo $initsetting['init']['disnum1']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname11'])&&$initsetting['init']['disname11']!=""){
														echo $initsetting['init']['disname11'];
													}
													else if($initsetting['init']['disnum1']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum1']/10).' '.$buttons1['name']['29'];
													}
													else {
														echo $initsetting['init']['disnum1'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname12'])&&$initsetting['init']['disname12']!=""){
														echo $initsetting['init']['disname12'];
													}
													else if($initsetting['init']['disnum1']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum1']/10).' '.$buttons2['name']['29'];
													}
													else {
														echo $initsetting['init']['disnum1'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis1' value='".$initsetting['init']['disnum1']."'>";
											/*}
											else{
												echo "<button class='listdisbut1' value='";
												if(isset($initsetting['init']['disname1'])){
													echo $initsetting['init']['disname1'];
												}
												else{
													echo $initsetting['init']['disnum1']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname1'])){
														echo $initsetting['init']['disname1'];
													}
													else{
														echo $initsetting['init']['disnum1'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname1'])){
														echo $initsetting['init']['disname1'];
													}
													else{
														echo $initsetting['init']['disnum1'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis1' value='".$initsetting['init']['disnum1']."'>";
											}*/
										}
										else if(intval($initsetting['init']['disnum1'])==0){
											echo "<button class='listdisbut1' value='免費招待'>";
											if(isset($initsetting['init']['disname11'])&&$initsetting['init']['disname11']!='')echo "<div id='name1'>".$initsetting['init']['disname11']."</div>";
											if(isset($initsetting['init']['disname12'])&&$initsetting['init']['disname12']!='')echo "<div id='name2'>".$initsetting['init']['disname12']."</div>";
											echo "</button>
												<input type='hidden' class='listdis1' value='".$initsetting['init']['disnum1']."'>";
										}
										else{
											echo "<button class='listdisbut1' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis1' value=''>";
										}
									}
									?>
								</td>
								<td>
									<?php
									if(isset($itemdis['listdis'])&&isset($itemdis['listdis']['state2'])){//2022/3/17
										if($itemdis['listdis']['state2']==='1'){
											echo "<button class='listdisbut2' value='扣折'>";
											if($itemdis['listdis']['name21']!='')echo "<div id='name1'>".$itemdis['listdis']['name21']."</div>";
											if($itemdis['listdis']['name22']!='')echo "<div id='name2'>".$itemdis['listdis']['name22']."</div>";
											echo "</button>
												<input type='hidden' class='listdis2' value='".$itemdis['listdis']['number2']."'>
												<input type='hidden' class='listdistype2' value='".$itemdis['listdis']['type2']."'>";
										}
										else{
											echo "<button class='listdisbut2' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis2' value=''>";

										}
									}
									else if($initsetting['init']['disbut2']==0){
										echo "<button class='listdisbut2' value='無扣折' disabled>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
										echo "</button>
											<input type='hidden' class='listdis2' value=''>";
									}
									else{
										if(intval($initsetting['init']['disnum2'])>0){
											//if($initsetting['init']['disnum2']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
												echo "<button class='listdisbut2' value='";
												if(isset($initsetting['init']['disname21'])&&$initsetting['init']['disname21']!=""){
													echo $initsetting['init']['disname21'];
												}
												else if(isset($initsetting['init']['disname22'])&&$initsetting['init']['disname22']!=""){
													echo $initsetting['init']['disname22'];
												}
												else if($initsetting['init']['disnum2']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
													echo ($initsetting['init']['disnum2']/10)."折";
												}
												else{
													echo $initsetting['init']['disnum2']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname21'])&&$initsetting['init']['disname21']!=""){
														echo $initsetting['init']['disname21'];
													}
													else if($initsetting['init']['disnum2']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum2']/10).' '.$buttons1['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum2'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname22'])&&$initsetting['init']['disname22']!=""){
														echo $initsetting['init']['disname22'];
													}
													else if($initsetting['init']['disnum2']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum2']/10).' '.$buttons2['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum2'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis2' value='".$initsetting['init']['disnum2']."'>";
											/*}
											else{
												echo "<button class='listdisbut2' value='";
												if(isset($initsetting['init']['disname2'])){
													echo $initsetting['init']['disname2'];
												}
												else{
													echo $initsetting['init']['disnum2']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname2'])){
														echo $initsetting['init']['disname2'];
													}
													else{
														echo $initsetting['init']['disnum2'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname2'])){
														echo $initsetting['init']['disname2'];
													}
													else{
														echo $initsetting['init']['disnum2'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis2' value='".$initsetting['init']['disnum2']."'>";
											}*/
										}
										else if(intval($initsetting['init']['disnum2'])==0){
											echo "<button class='listdisbut2' value='免費招待'>";
											if(isset($initsetting['init']['disname21'])&&$initsetting['init']['disname21']!='')echo "<div id='name1'>".$initsetting['init']['disname21']."</div>";
											if(isset($initsetting['init']['disname22'])&&$initsetting['init']['disname22']!='')echo "<div id='name2'>".$initsetting['init']['disname22']."</div>";
											echo "</button>
												<input type='hidden' class='listdis2' value='".$initsetting['init']['disnum2']."'>";
										}
										else{
											echo "<button class='listdisbut2' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis2' value=''>";
										}
									}
									?>
								</td>
								<!-- <td>
									<?php
									if($initsetting['init']['useinv']==0){
										echo "<input type='button' class='manyinv' value='開立多張發票' disabled>";
									}
									else{
										if($initsetting['init']['manyinv']==0){
											echo "<input type='button' class='manyinv' value='開立多張發票' disabled>";
										}
										else{
											echo "<input type='button' class='manyinv' value='開立多張發票'>";
										}
									}
									?>
								</td> -->
								<td>
									<?php
									if(isset($itemdis['listdis'])&&isset($itemdis['listdis']['state3'])){//2022/3/17
										if($itemdis['listdis']['state3']==='1'){
											echo "<button class='listdisbut3' value='扣折'>";
											if($itemdis['listdis']['name31']!='')echo "<div id='name1'>".$itemdis['listdis']['name31']."</div>";
											if($itemdis['listdis']['name32']!='')echo "<div id='name2'>".$itemdis['listdis']['name32']."</div>";
											echo "</button>
												<input type='hidden' class='listdis3' value='".$itemdis['listdis']['number3']."'>
												<input type='hidden' class='listdistype3' value='".$itemdis['listdis']['type3']."'>";
										}
										else{
											echo "<button class='listdisbut3' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis3' value=''>";

										}
									}
									else if($initsetting['init']['disbut3']==0){
										echo "<button class='listdisbut3' value='無扣折' disabled>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
										echo "</button>
											<input type='hidden' class='listdis3' value=''>";
									}
									else{
										if(intval($initsetting['init']['disnum3'])>0){
											//if($initsetting['init']['disnum3']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
												echo "<button class='listdisbut3' value='";
												if(isset($initsetting['init']['disname31'])&&$initsetting['init']['disname31']!=""){
													echo $initsetting['init']['disname31'];
												}
												else if(isset($initsetting['init']['disname32'])&&$initsetting['init']['disname32']!=""){
													echo $initsetting['init']['disname32'];
												}
												else if($initsetting['init']['disnum3']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
													echo ($initsetting['init']['disnum3']/10)."折";
												}
												else{
													echo $initsetting['init']['disnum3']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname31'])&&$initsetting['init']['disname31']!=""){
														echo $initsetting['init']['disname31'];
													}
													else if($initsetting['init']['disnum3']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum3']/10).' '.$buttons1['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum3'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname32'])&&$initsetting['init']['disname32']!=""){
														echo $initsetting['init']['disname32'];
													}
													else if($initsetting['init']['disnum3']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum3']/10).' '.$buttons2['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum3'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis3' value='".$initsetting['init']['disnum3']."'>";
											/*}
											else{
												echo "<button class='listdisbut3' value='";
												if(isset($initsetting['init']['disname3'])){
													echo $initsetting['init']['disname3'];
												}
												else{
													echo $initsetting['init']['disnum3']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname3'])){
														echo $initsetting['init']['disname3'];
													}
													else{
														echo $initsetting['init']['disnum3'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname3'])){
														echo $$initsetting['init']['disname3'];
													}
													else{
														echo $initsetting['init']['disnum3'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis3' value='".$initsetting['init']['disnum3']."'>";
											}*/
										}
										else if(intval($initsetting['init']['disnum3'])==0){
											echo "<button class='listdisbut3' value='免費招待'>";
											if(isset($initsetting['init']['disname31'])&&$initsetting['init']['disname31']!='')echo "<div id='name1'>".$initsetting['init']['disname31']."</div>";
											if(isset($initsetting['init']['disname32'])&&$initsetting['init']['disname32']!='')echo "<div id='name2'>".$initsetting['init']['disname32']."</div>";
											echo "</button>
												<input type='hidden' class='listdis3' value='".$initsetting['init']['disnum3']."'>";
										}
										else{
											echo "<button class='listdisbut3' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis3' value=''>";
										}
									}
									?>
								</td>
							</tr>
							<tr>
								<td>
									<?php
									if(isset($itemdis['listdis'])&&isset($itemdis['listdis']['state4'])){//2022/3/17
										if($itemdis['listdis']['state4']==='1'){
											echo "<button class='listdisbut4' value='扣折'>";
											if($itemdis['listdis']['name41']!='')echo "<div id='name1'>".$itemdis['listdis']['name41']."</div>";
											if($itemdis['listdis']['name42']!='')echo "<div id='name2'>".$itemdis['listdis']['name42']."</div>";
											echo "</button>
												<input type='hidden' class='listdis4' value='".$itemdis['listdis']['number4']."'>
												<input type='hidden' class='listdistype4' value='".$itemdis['listdis']['type4']."'>";
										}
										else{
											echo "<button class='listdisbut4' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis4' value=''>";

										}
									}
									else if($initsetting['init']['disbut4']==0){
										echo "<button class='listdisbut4' value='無扣折' disabled>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
										echo "</button>
											<input type='hidden' class='listdis4' value=''>";
									}
									else{
										if(intval($initsetting['init']['disnum4'])>0){
											//if($initsetting['init']['disnum4']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
												echo "<button class='listdisbut4' value='";
												if(isset($initsetting['init']['disname41'])&&$initsetting['init']['disname41']!=""){
													echo $initsetting['init']['disname41'];
												}
												if(isset($initsetting['init']['disname42'])&&$initsetting['init']['disname42']!=""){
													echo $initsetting['init']['disname42'];
												}
												else if($initsetting['init']['disnum4']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
													echo ($initsetting['init']['disnum4']/10)."折";
												}
												else{
													echo $initsetting['init']['disnum4']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname41'])&&$initsetting['init']['disname41']!=""){
														echo $initsetting['init']['disname41'];
													}
													else if($initsetting['init']['disnum4']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum4']/10).' '.$buttons1['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum4'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname42'])&&$initsetting['init']['disname42']!=""){
														echo $initsetting['init']['disname42'];
													}
													else if($initsetting['init']['disnum4']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum4']/10).' '.$buttons2['name']['29'];
													}
													else{
														echo ($initsetting['init']['disnum4']/10).' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis4' value='".$initsetting['init']['disnum4']."'>";
											/*}
											else{
												echo "<button class='listdisbut4' value='";
												if(isset($initsetting['init']['disname4'])){
													echo $initsetting['init']['disname4'];
												}
												else{
													echo $initsetting['init']['disnum4']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname4'])){
														echo $initsetting['init']['disname4'];
													}
													else{
														echo $initsetting['init']['disnum4'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname4'])){
														echo $initsetting['init']['disname4'];
													}
													else{
														echo ($initsetting['init']['disnum4']/10).' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis4' value='".$initsetting['init']['disnum4']."'>";
											}*/
										}
										else if(intval($initsetting['init']['disnum4'])==0){
											echo "<button class='listdisbut4' value='免費招待'>";
											if(isset($initsetting['init']['disname41'])&&$initsetting['init']['disname41']!='')echo "<div id='name1'>".$initsetting['init']['disname41']."</div>";
											if(isset($initsetting['init']['disname42'])&&$initsetting['init']['disname42']!='')echo "<div id='name2'>".$initsetting['init']['disname42']."</div>";
											echo "</button>
												<input type='hidden' class='listdis4' value='".$initsetting['init']['disnum4']."'>";
										}
										else{
											echo "<button class='listdisbut4' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis4' value=''>";
										}
									}
									?>
								</td>
								<td>
									<?php
									if(isset($itemdis['listdis'])&&isset($itemdis['listdis']['state5'])){//2022/3/17
										if($itemdis['listdis']['state5']==='1'){
											echo "<button class='listdisbut5' value='扣折'>";
											if($itemdis['listdis']['name51']!='')echo "<div id='name1'>".$itemdis['listdis']['name51']."</div>";
											if($itemdis['listdis']['name52']!='')echo "<div id='name2'>".$itemdis['listdis']['name52']."</div>";
											echo "</button>
												<input type='hidden' class='listdis5' value='".$itemdis['listdis']['number5']."'>
												<input type='hidden' class='listdistype5' value='".$itemdis['listdis']['type5']."'>";
										}
										else{
											echo "<button class='listdisbut5' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis5' value=''>";

										}
									}
									else if($initsetting['init']['disbut5']==0){
										echo "<button class='listdisbut5' value='無扣折' disabled>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
										echo "</button>
											<input type='hidden' class='listdis5' value=''>";
									}
									else{
										if(intval($initsetting['init']['disnum5'])>0){
											//if($initsetting['init']['disnum5']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
												echo "<button class='listdisbut5' value='";
												if(isset($initsetting['init']['disname51'])&&$initsetting['init']['disname51']!=""){
													echo $initsetting['init']['disname51'];
												}
												if(isset($initsetting['init']['disname52'])&&$initsetting['init']['disname52']!=""){
													echo $initsetting['init']['disname52'];
												}
												else if($initsetting['init']['disnum5']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
													echo ($initsetting['init']['disnum5']/10)."折";
												}
												else{
													echo $initsetting['init']['disnum5']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname51'])&&$initsetting['init']['disname51']!=""){
														echo $initsetting['init']['disname51'];
													}
													else if($initsetting['init']['disnum5']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum5']/10).' '.$buttons1['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum5'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname52'])&&$initsetting['init']['disname52']!=""){
														echo $initsetting['init']['disname52'];
													}
													else if($initsetting['init']['disnum5']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum5']/10).' '.$buttons2['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum5'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis5' value='".$initsetting['init']['disnum5']."'>";
											/*}
											else{
												echo "<button class='listdisbut5' value='";
												if(isset($initsetting['init']['disname5'])){
													echo $initsetting['init']['disname5'];
												}
												else{
													echo $initsetting['init']['disnum5']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname5'])){
														echo $initsetting['init']['disname5'];
													}
													else{
														echo $initsetting['init']['disnum5'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname5'])){
														echo $initsetting['init']['disname5'];
													}
													else{
														echo $initsetting['init']['disnum5'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis5' value='".$initsetting['init']['disnum5']."'>";
											}*/
										}
										else if(intval($initsetting['init']['disnum5'])==0){
											echo "<button class='listdisbut5' value='免費招待'>";
											if(isset($initsetting['init']['disname51'])&&$initsetting['init']['disname51']!='')echo "<div id='name1'>".$initsetting['init']['disname51']."</div>";
											if(isset($initsetting['init']['disname52'])&&$initsetting['init']['disname52']!='')echo "<div id='name2'>".$initsetting['init']['disname52']."</div>";
											echo "</button>
												<input type='hidden' class='listdis5' value='".$initsetting['init']['disnum5']."'>";
										}
										else{
											echo "<button class='listdisbut5' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis5' value=''>";
										}
									}
									?>
								</td>
								<td>
									<?php
									if(isset($itemdis['listdis'])&&isset($itemdis['listdis']['state6'])){//2022/3/17
										if($itemdis['listdis']['state6']==='1'){
											echo "<button class='listdisbut6' value='扣折'>";
											if($itemdis['listdis']['name61']!='')echo "<div id='name1'>".$itemdis['listdis']['name61']."</div>";
											if($itemdis['listdis']['name62']!='')echo "<div id='name2'>".$itemdis['listdis']['name62']."</div>";
											echo "</button>
												<input type='hidden' class='listdis6' value='".$itemdis['listdis']['number6']."'>
												<input type='hidden' class='listdistype6' value='".$itemdis['listdis']['type6']."'>";
										}
										else{
											echo "<button class='listdisbut6' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis6' value=''>";

										}
									}
									else if($initsetting['init']['disbut6']==0){
										echo "<button class='listdisbut6' value='無扣折' disabled>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
										echo "</button>
											<input type='hidden' class='listdis6' value=''>";
									}
									else{
										if(intval($initsetting['init']['disnum6'])>0){
											//if($initsetting['init']['disnum6']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
												echo "<button class='listdisbut6' value='";
												if(isset($initsetting['init']['disname61'])&&$initsetting['init']['disname61']!=""){
													echo $initsetting['init']['disname61'];
												}
												if(isset($initsetting['init']['disname62'])&&$initsetting['init']['disname62']!=""){
													echo $initsetting['init']['disname62'];
												}
												else if($initsetting['init']['disnum6']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
													echo ($initsetting['init']['disnum6']/10)."折";
												}
												else{
													echo $initsetting['init']['disnum6']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname61'])&&$initsetting['init']['disname61']!=""){
														echo $initsetting['init']['disname61'];
													}
													else if($initsetting['init']['disnum6']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum6']/10).' '.$buttons1['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum6'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname62'])&&$initsetting['init']['disname62']!=""){
														echo $initsetting['init']['disname62'];
													}
													else if($initsetting['init']['disnum6']%10==0&&(!isset($initsetting['init']['disparam'])||$initsetting['init']['disparam']=='1')){
														echo ($initsetting['init']['disnum6']/10).' '.$buttons2['name']['29'];
													}
													else{
														echo $initsetting['init']['disnum6'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis6' value='".$initsetting['init']['disnum6']."'>";
											/*}
											else{
												echo "<button class='listdisbut5' value='";
												if(isset($initsetting['init']['disname5'])){
													echo $initsetting['init']['disname5'];
												}
												else{
													echo $initsetting['init']['disnum5']."折";
												}
												echo "'>";
												if($buttons1!='-1'){
													echo "<div id='name1'>";
													if(isset($initsetting['init']['disname5'])){
														echo $initsetting['init']['disname5'];
													}
													else{
														echo $initsetting['init']['disnum5'].' '.$buttons1['name']['29'];
													}
													echo "</div>";
												}
												if($buttons2!='-1'){
													echo "<div id='name2'>";
													if(isset($initsetting['init']['disname5'])){
														echo $initsetting['init']['disname5'];
													}
													else{
														echo $initsetting['init']['disnum5'].' '.$buttons2['name']['29'];
													}
													echo "</div>";
												}
												echo "</button>
													<input type='hidden' class='listdis5' value='".$initsetting['init']['disnum5']."'>";
											}*/
										}
										else if(intval($initsetting['init']['disnum6'])==0){
											echo "<button class='listdisbut6' value='免費招待'>";
											if(isset($initsetting['init']['disname61'])&&$initsetting['init']['disname61']!='')echo "<div id='name1'>".$initsetting['init']['disname61']."</div>";
											if(isset($initsetting['init']['disname62'])&&$initsetting['init']['disname62']!='')echo "<div id='name2'>".$initsetting['init']['disname62']."</div>";
											echo "</button>
												<input type='hidden' class='listdis6' value='".$initsetting['init']['disnum6']."'>";
										}
										else{
											echo "<button class='listdisbut6' value='無扣折' disabled>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['30']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['30']."</div>";
											echo "</button>
												<input type='hidden' class='listdis6' value=''>";
										}
									}
									?>
								</td>
							</tr>
							<tr>
								<td>
									<?php
									if($initsetting['init']['useinv']==0&&$initsetting['init']['useoinv']==0){
										/*echo "<button class='invbut' value='不開立發票' disabled><div id='name1'>不開立發票</div></button>";*/
										echo "<input type='hidden' class='invno' value='".$initsetting['init']['inv']."'>";
									}
									else{
										if($initsetting['init']['inv']==0){
											echo "<button class='invbut' value='不開立發票'><div id='name1'>不開立發票</div></button>
												<input type='hidden' class='invno' value='".$initsetting['init']['inv']."'>";
										}
										else{
											echo "<button class='invbut' value='開立發票'><div id='name1'>開立發票</div></button>
												<input type='hidden' class='invno' value='".$initsetting['init']['inv']."'>";
										}
									}
									?>
								</td>
								<td>
									<?php
									if($initsetting['init']['openchar']==0){
										/*echo "<button class='chargebut' value='不收服務費' disabled>";
										if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['28']."</div>";
										if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['28']."</div>";
										echo "</button>";*/
										echo "<input type='hidden' id='charno' value='0'>";
									}
									else{
										if($initsetting['init']['charge']==0){
											echo "<button class='chargebut' value='不收服務費'>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['28']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['28']."</div>";
											echo "</button>
													<input type='hidden' id='charno' value='0'>";
										}
										else{
											echo "<button class='chargebut' value='收服務費'>";
											if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['27']."</div>";
											if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['27']."</div>";
											echo "</button>
													<input type='hidden' id='charno' value='1'>";
										}
									}
									?>
								</td>
								<td>
									<button class='receiptbut' value='不印收據章'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['notreceipt']."</div>";if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['notreceipt']."</div>"; ?></button>
									<input type='hidden' id='receipt' value='0'>
								</td>
							</tr>
							<tr>
								<td>
								<?php
								/*if(isset($initsetting['init']['itri'])&&$initsetting['init']['itri']=='1'){//2020/2/11目前實際看來專案無法RUN，暫時移除
									echo "<button class='itri' value='商業獅'>";
									if($buttons1!='-1'&&isset($buttons1['name']['itri'])){
										echo "<div id='name1'>".$buttons1['name']['itri']."</div>";
									}
									else{
									}
									echo "</button>";
								}
								else{
								}*/
								?>
								<?php
								//2022/5/4 該功能將合併至其他付款的按鈕中，但功能連棟需要該按鈕作為媒介//2022/4/27 直接與line pay付款串接
								if(isset($initsetting['init']['directlinepay'])&&$initsetting['init']['directlinepay']=='1'){
									echo "<button class='directlinepay' value='LinePay' style='display:none;'>";
									if($buttons1!='-1'&&isset($buttons1['name']['linepay'])){
										echo "<div id='name1'>".$buttons1['name']['linepay']."</div>";
									}
									else{
									}
									echo "</button>";
								}
								else{
								}
								//2022/10/6 (仿照)//2022/5/4 該功能將合併至其他付款的按鈕中，但功能連棟需要該按鈕作為媒介//2022/4/27 直接與line pay付款串接
								if(isset($initsetting['init']['jkos'])&&$initsetting['init']['jkos']=='1'){
									echo "<button class='jkos' value='街口' style='display:none;'>";
									if($buttons1!='-1'&&isset($buttons1['name']['jkos'])){
										echo "<div id='name1'>".$buttons1['name']['jkos']."</div>";
									}
									else{
									}
									echo "</button>";
								}
								else{
								}
								//2022/10/28 (仿照)//2022/5/4 該功能將合併至其他付款的按鈕中，但功能連棟需要該按鈕作為媒介//2022/4/27 直接與line pay付款串接
								if(isset($initsetting['init']['pxpayplus'])&&$initsetting['init']['pxpayplus']=='1'){
									echo "<button class='pxpayplus' value='全支付' style='display:none;'>";
									if($buttons1!='-1'&&isset($buttons1['name']['pxpayplus'])){
										echo "<div id='name1'>".$buttons1['name']['pxpayplus']."</div>";
									}
									else{
									}
									echo "</button>";
								}
								else{
								}
								?>
								</td>
								<td>
								<?php
								if(isset($initsetting['init']['intellapay'])&&$initsetting['init']['intellapay']=='1'){
									echo "<button class='intellapay' value='英特拉付款'>";
									if($buttons1!='-1'&&isset($buttons1['name']['intellapay'])){
										echo "<div id='name1'>".$buttons1['name']['intellapay']."</div>";
									}
									else{
									}
									echo "</button>";
								}
								else{
								}
								?>
								</td>
								<td>
								<?php
								if(isset($initsetting['init']['pointtree'])&&$initsetting['init']['pointtree']=='1'){
									echo "<button class='pointtree' value='集點樹'>";
									if($buttons1!='-1'&&isset($buttons1['name']['pointtree'])){
										echo "<div id='name1'>".$buttons1['name']['pointtree']."</div>";
									}
									else{
									}
									echo "<div id='name3'></div>";
									echo "<div id='name2'></div>";
									echo "</button>";
								}
								else{
								}
								?>
								</td>
							</tr>
						</table>
						<div id='paywindow' style='width:calc(100% - 6px);height:200px;padding:0;margin:0;position: absolute;bottom: 0;left: 2px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<div id='th' style='overflow:hidden;'>
								<div style='width:20px;height:19px;float:left;'></div>
								<div style='width:calc(50% - 10px);float:left;text-align:center;'><?php if(isset($interface1['name']['24']))echo "<div id='name1'>".$interface1['name']['24']."</div>"; ?><?php if(isset($interface1['name']['24'])&&isset($interface2['name']['24']))echo "<div id='name2'>(".$interface2['name']['24'].')</div>';else if(!isset($interface1['name']['24'])&&isset($interface2['name']['24']))echo "<div id='name2'>".$interface2['name']['24'].'</div>';else; ?></div>
								<div style='width:calc(50% - 10px);float:left;text-align:center;'><?php if(isset($interface1['name']['25']))echo "<div id='name1'>".$interface1['name']['25']."</div>"; ?><?php if(isset($interface1['name']['25'])&&isset($interface2['name']['25']))echo "<div id='name2'>(".$interface2['name']['25'].')</div>';else if(!isset($interface1['name']['25'])&&isset($interface2['name']['25']))echo "<div id='name2'>".$interface2['name']['25'].'</div>';else ?></div>
							</div>
							<div id='paycontent' style='height:calc(100% - 27px);overflow:auto;'>
							</div>
						</div>
					</div>
					<div id='viewwindow' style='width:calc(100% - 500px);height:100%;float:left;position: relative;overflow-y: auto;'><?php /*Y-scroll for sec-language*/ ?>
						<form class='sendviewwindow'>
							<?php
							if($initsetting['init']['listprint']==1){
								echo "<input type='hidden' name='looptype' value='1'>";
							}
							else if($initsetting['init']['listprint']==2){
								echo "<input type='hidden' name='looptype' value='2'>";
							}
							else if($initsetting['init']['listprint']==3){
								echo "<input type='hidden' name='looptype' value='3'>";
							}
							else{
								echo "<input type='hidden' name='looptype' value='4'>";
							}

							if($initsetting['init']['intellapay']=='1'){
								echo "<input type='hidden' name='intellaconsecnumber' value=''>";
							}
							else{
							}
							?>
							<input type='hidden' name='sendtype' value='result'><?php //如果值為temp，則為暫結開啟，送出後，不需要將資料轉至正式表格，其他流程相同 ?>
							<input type='hidden' name='creditcard' value=''><?php //信用卡號後4碼 ?>
							<?php
							if($initsetting['init']['useinv']==0&&$initsetting['init']['useoinv']==0){
								echo "<table style='width:calc(100% - 10px);display: none;'>";
							}
							else{
								echo "<table style='width:calc(100% - 10px);'>";
							}
							?>
								<tr>
									<td id='title' style='width:20px;'>統編</td>
									<td><input type='text' name='tempban' value='' style='width:100%;background-color:#EFEBDE;text-align:right;' readonly></td>
								</tr>
								<tr>
									<td id='title' style='width:20px;'>載具</td>
									<td><input type='text' name='tempcontainer' value='' placeholder='' style='width:100%;background-color:#EFEBDE;text-align:right;' readonly><!-- <input type='hidden' name='temp'> --></td>
								</tr>
							</table>
							<?php
							if(isset($initsetting['init']['pointtree'])&&$initsetting['init']['pointtree']=='1'){
								echo "<input type='hidden' name='treetoken' value='";
								if(isset($machinedata['pointtree']['token'])) echo $machinedata['pointtree']['token'];
								echo "'>";
							}
							else{
							}
							?>
							<input type='hidden' name='treetel' value='' style='width:100%;background-color:#EFEBDE;text-align:right;' readonly>
							<input type='hidden' name='ttlfloor' value='0'>
							<table style='width:calc(100% - 10px);'>
								<tr>
									<td><?php if(isset($interface1['name']['26']))echo "<div id='name1'>".$interface1['name']['26']."</div>"; ?><?php if(isset($interface2['name']['26']))echo "<div id='name2'>".$interface2['name']['26']."</div>"; ?></td>
									<td style='text-align:right;background-color:#F1FBFD;border-top:1px solid #dfdfdf;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><?php echo $initsetting['init']['frontunit']; ?><span id='total'>0</span><input type='hidden' id='temptotal' value=''><?php echo $initsetting['init']['unit']; ?></td><!-- 此畫面中不可修改 -->
								</tr>
								<tr>
									<td><?php if(isset($interface1['name']['27']))echo "<div id='name1'>".$interface1['name']['27']."</div>"; ?><?php if(isset($interface2['name']['27']))echo "<div id='name2'>".$interface2['name']['27']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='itemdis'>0</span><input type='hidden' id='tempdis' value=''><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='itemdis' value='0'></td><!-- 此畫面中不可修改 -->
								</tr>
								<tr>
									<td><?php if(isset($interface1['name']['autodis']))echo "<div id='name1'>".$interface1['name']['autodis']."</div>"; ?><?php if(isset($interface2['name']['autodis']))echo "<div id='name2'>".$interface2['name']['autodis']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='autodis'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='autodis' value='0'><input type='hidden' name='autodiscontent' value=''><input type='hidden' name='autodispremoney' value='0'></td><!-- 自動計算 -->
								</tr>
								<tr>
									<td><?php if(isset($interface1['name']['listtotal']))echo "<div id='name1' style='font-size:17px;font-weight:bold;'>".$interface1['name']['listtotal']."</div>"; ?><?php if(isset($interface2['name']['listtotal']))echo "<div id='name2'>".$interface2['name']['listtotal']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='listtotal' style='font-size:20px;font-weight:bold;'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='listtotal' value='0'></td><!-- 自動計算 -->
								</tr>
								<?php
									if(isset($initsetting['init']['member'])&&$initsetting['init']['member']=='1'){
								?>
									<tr>
										<td><?php if(isset($interface1['name']['memdis']))echo "<div id='name1' style='font-size:17px;font-weight:bold;'>".$interface1['name']['memdis']."</div>"; ?><?php if(isset($interface2['name']['memdis']))echo "<div id='name2'>".$interface2['name']['memdis']."</div>"; ?></td>
										<td style='text-align:right;'><span id='memberdis'>0</span><input type='hidden' name='memberdis' value='0'></td><!-- 此畫面中不可修改 -->
									</tr>
								<?php
									}
									else{
								?>
									<span id='memberdis' style='display:none;'>0</span><input type='hidden' name='memberdis' value='0'>
								<?php
									}
								?>
								<?php
									if(isset($initsetting['init']['openchar'])&&$initsetting['init']['openchar']=='1'){
								?>
								<tr>
									<td><div id='name1'><?php if(isset($interface1['name']['31']))echo $interface1['name']['31']; ?></div><div id='name2'><?php if(isset($interface2['name']['31']))echo $interface2['name']['31']; ?></div></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='charge'>0</span><?php echo $initsetting['init']['unit']; ?></td><!-- 此畫面中不可修改 -->
								</tr>
								<?php
								}
								else{
								?>
									<span id='charge' style='display:none;'>0</span><input type='hidden' name='charge' value='0'>
								<?php
								}
								?>
								<tr>
									<td><div id='name1'><?php if(isset($interface1['name']['floor']))echo $interface1['name']['floor']; ?></div><div id='name2'><?php if(isset($interface2['name']['floor']))echo $interface2['name']['floor']; ?></div></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='floorspan'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='floorspan' value='0'></td>
								</tr>
								<!-- <tr>
									<td><div id='name1'><?php echo $interface1['name']['32']; ?></div><div id='name2'><?php if(isset($interface2['name']['32']))echo $interface2['name']['32']; ?></div></td>
									<td style='text-align:right;'>$ --><span id='coupon1' style='display:none;'>0</span><input type='hidden' name='coupon1' value='0'><!-- </td> --><!-- 自動計算 -->
								<!-- </tr>
								<tr>
									<td><div id='name1'><?php echo $interface1['name']['33']; ?></div><div id='name2'><?php if(isset($interface2['name']['33']))echo $interface2['name']['33']; ?></div></td>
									<td style='text-align:right;'>$ --><span id='coupon2' style='display:none;'>0</span><input type='hidden' name='coupon2' value='0'><!-- </td> --><!-- 自動計算 -->
								<!-- </tr> -->
								<tr>
									<td><?php if(isset($interface1['name']['29']))echo "<div id='name1'>".$interface1['name']['29']."</div>"; ?><?php if(isset($interface2['name']['29']))echo "<div id='name2'>".$interface2['name']['29']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='listdis1'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='listdis1' value='0'></td><!-- 自動計算 -->
								</tr>
								<tr>
									<td><?php if(isset($interface1['name']['30']))echo "<div id='name1'>".$interface1['name']['30']."</div>"; ?><?php if(isset($interface2['name']['30']))echo "<div id='name2'>".$interface2['name']['30']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='listdis2'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='listdis2' value='0'></td><!-- 自動計算 -->
								</tr>
								<tr>
									<td><?php if(isset($interface1['name']['34']))echo "<div id='name1' style='font-size:17px;font-weight:bold;'>".$interface1['name']['34']."</div>"; ?><?php if(isset($interface2['name']['34']))echo "<div id='name2'>".$interface2['name']['34']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='should' style='font-size:20px;font-weight:bold;'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='should' value='0'></td><!-- 自動計算 -->
								</tr>
								<?php
									if((isset($initsetting['init']['useoinv'])&&$initsetting['init']['useoinv']=='1')||(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1')){
								?>
								<tr>
									<td style='padding-left:15px;'><?php if(isset($interface1['name']['ininv']))echo "<div id='name1'>".$interface1['name']['ininv']."</div>"; ?><?php if(isset($interface2['name']['ininv']))echo "<div id='name2'>".$interface2['name']['ininv']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='ininv'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='ininv' value='0'></td><!-- 自動計算 -->
								</tr>
								<?php
									}
									else{
								?>
									<span id='ininv' style='display:none;'>0</span><input type='hidden' name='ininv' value='0'>
								<?php
									}
								?>
								<tr>
									<td style='padding-left:15px;'><?php if(isset($interface1['name']['freeinv']))echo "<div id='name1'>".$interface1['name']['freeinv']."</div>"; ?><?php if(isset($interface2['name']['freeinv']))echo "<div id='name2'>".$interface2['name']['freeinv']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='freeinv'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='freeinv' value='0'></td><!-- 自動計算 -->
								</tr>
								<?php
									if(isset($initsetting['init']['cashcomm'])&&$initsetting['init']['cashcomm']=='1'){
								?>
								<tr>
									<td><?php if(isset($interface1['name']['cashcomm']))echo "<div id='name1'>".$interface1['name']['cashcomm']."</div>"; ?><?php if(isset($interface2['name']['cashcomm']))echo "<div id='name2'>".$interface2['name']['cashcomm']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='cashcomm' >0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='cashcomm' value='0'></td><!-- 自動計算 -->
								</tr>
								<?php
									}
									else{
								?>
									<span id='cashcomm' style='display:none;'>0</span><input type='hidden' name='cashcomm' value='0'>
								<?php
									}
								?>
								<tr>
									<td><?php if(isset($interface1['name']['35']))echo "<div id='name1' style='font-size:17px;font-weight:bold;'>".$interface1['name']['35']."</div>"; ?><?php if(isset($interface2['name']['35']))echo "<div id='name2'>".$interface2['name']['35']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='already'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='already' value='0'></td><!-- 自動計算 -->
								</tr>

								<tr>
									<td style='padding-left:15px;'><?php if(isset($interface1['name']['money']))echo "<div id='name1'>".$interface1['name']['money']."</div>"; ?><?php if(isset($interface2['name']['money']))echo "<div id='name2'>".$interface2['name']['money']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='cashmoney'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='cashmoney' value='0'></td><!-- 自動計算 -->
								</tr>
								<?php 
									if(isset($initsetting['init']['cashbut'])&&$initsetting['init']['cashbut']=='1'){
								?>
								<tr>
									<td style='padding-left:15px;'><?php if(isset($interface1['name']['cash']))echo "<div id='name1'>".$interface1['name']['cash']."</div>"; ?><?php if(isset($interface2['name']['cash']))echo "<div id='name2'>".$interface2['name']['cash']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='cash'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='cash' value='0'></td><!-- 自動計算 -->
								</tr>
								<?php
									}
									else{
								?>
									<span id='cash' style='display:none;'>0</span><input type='hidden' name='cash' value='0'>
								<?php
									}
								?>
								<tr>
									<td style='padding-left:15px;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['other']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['other']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='other'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='other' value='0'><input type='hidden' name='otherstring' value=''></td><!-- 自動計算 -->
								</tr>
								<tr>
									<td style='padding-left:15px;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['otherfix']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['otherfix']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='otherfix'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='otherfix' value='0'></td><!-- 自動計算 -->
								</tr>
								<!-- <tr>
									<td style='padding-left:15px;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['other']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['other']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='other'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='other' value='0'></td>自動計算
								</tr> -->
								<tr>
									<td><?php if($interface1!='-1')echo "<div id='name1' style='font-size:17px;font-weight:bold;'>".$interface1['name']['36']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['36']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='notyet' style='font-size:20px;font-weight:bold;'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='notyet' value='0'></td><!-- 自動計算 -->
								</tr>
								<tr>
									<td><?php if($interface1!='-1')echo "<div id='name1' style='font-size:17px;font-weight:bold;'>".$interface1['name']['37']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['37']."</div>"; ?></td>
									<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='change' style='font-size:20px;font-weight:bold;'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='change' value='0'></td><!-- 自動計算 -->
								</tr>
							</table>
						</form>
						<div id='checkfun' style='<!-- bottom: 0;position: absolute; -->'>
							
							<button id='submit' value='確認' style='width:calc(50% - 4px);margin:0 1px 1px 1px;float: left;background-color:#D5DC75;color:#000000;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['40']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['40']."</div>"; ?></button>
							<button id='cancel' value='取消' style='width:calc(50% - 4px);margin:0 1px 1px 1px;float: left;background-color:#D5DC75;color:#000000;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['41']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['41']."</div>"; ?></button>
							<button id='notsale' value='暫出結帳明細' style='margin:1px 1px 0 1px;background-color:#D5DC75;color:#000000;width:calc(100% - 6px);float: left;<?php
							if(isset($initsetting['init']['notsale'])&&$initsetting['init']['notsale']=='1'){
							}
							else{
								echo 'display:none;';
							}
							?>'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['notsale']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['notsale']."</div>"; ?></button><br>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='taste2' style='padding:20px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['40'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['40'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['40'];else; ?>'><!-- 加料欄位 -->
			<div style='width:calc(54% - 1px);height:100%;float:left;margin-right:1px;'>
				<div class='tasteparameters' style='display:none;'>
					<input type='hidden' name='page' value='0'>
				</div>
				<div style='width:100%;height:calc(10% - 7px);float:left;'>
					<input type='text' style='width:calc(100% / 3 * 2 - 2px);height:calc(100% - 2px);margin:1px;float:left;border:1px solid #898989;border-radius:5px;background-color:#ffffff;padding:5px;font-size:4vh;' name='othertaste' value='' placeholder='<?php if(isset($interface1['name']['enternote']))echo $interface1['name']['enternote'];else echo '輸入備註...'; ?>'>
					<button class='sendothertaste' style='width:calc(100% / 3 - 2px);height:calc(100% - 2px);margin:1px;float:left;border:1px solid #898989;border-radius:5px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['46']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['46']."</div>";else; ?></button>
				</div>
				<div style='width:100%;border-top:2px solid #000000;margin:2px 0;float:left;'></div>
				<div style='width:100%;height:70%;float:left;'>
					<?php
					for($i=0;$i<($initsetting['init']['tasterow']*$initsetting['init']['tastecol']);$i++){
						echo "<div style='width:calc(100% / ".$initsetting['init']['tastecol']." - 2px);height:calc(100% / ".$initsetting['init']['tasterow']." - 2px);float:left;margin:1px;'>
								<button id='tastebut".$i."' value='' style='width:100%;height:100%;font-size:10px;'><div id='name1'></div><div id='name2'></div></button>
								<input type='hidden' id='tasteno".$i."' value=''>
								<input type='hidden' id='tastemoney".$i."' value=''>
							</div>";
					}
					?>
				</div>
				<div style='width:100%;height:20%;float:left;' id='fun'>
					<div style='width:calc(100% / 3 - 2px);height:calc(100% / 2 - 2px);float:left;margin:1px;'>
						<button id='tastefun1' value='+' style='width:100%;height:100%;'><div>+</div></button>
					</div>
					<div style='width:calc(100% / 3 - 2px);height:calc(100% / 2 - 2px);float:left;margin:1px;'>
						<button id='tastefun2' value='-' style='width:100%;height:100%;'><div>-</div></button>
					</div>
					<div style='width:calc(100% / 3 - 2px);height:calc(100% / 2 - 2px);float:left;margin:1px;'>
						<button id='tastefun5' class='delete' value='Xóa bỏ' style='width:100%;height:100%;background-color:#D5DC75;color:#000000;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['45']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['45']."</div>";else; ?></button>
					</div>
					<div style='width:calc(100% / 3 - 2px);height:calc(100% / 2 - 2px);float:left;margin:1px;'>
						<button id='pre' value='上一頁' style='width:100%;height:100%;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['43']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['43']."</div>";else; ?></button>
					</div>
					<div style='width:calc(100% / 3 - 2px);height:calc(100% / 2 - 2px);float:left;margin:1px;'>
						<button id='next' value='下一頁' style='width:100%;height:100%;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['44']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['44']."</div>";else; ?></button>
					</div>
					<div style='width:calc(100% / 3 - 2px);height:calc(100% / 2 - 2px);float:left;margin:1px;'>
						<button id='tastefun6' class='submit' value='確認' style='width:100%;height:100%;background-color:#D5DC75;color:#000000;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['46']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['46']."</div>";else; ?></button>
					</div>
				</div>
			</div>
			<div style='width:calc(46% - 1px);height:100%;margin-left:1px;float:left;border:1px solid #000000;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;background-color:#848284;'>
				<div id='tastetitle' style='width:100%;height:14px;float:left;font-size:12px;background-color:#EFEBDE;'>
					<div style='width:13%;height:40px;float:left;'>
					</div>
					<div style='width:34%;float:left;'>
						Items
						<!-- <div id='name1'><?php echo $interface1['name']['41']; ?></div>
						<?php
						if(isset($interface2['name']['41']))
							echo "<div id='name2'>(".$interface2['name']['41'].")</div>";
						?> -->
					</div>
					<div style='width:17%;float:left;text-align:center;'>
						U/P
						<!-- <div id='name1'><?php echo $interface1['name']['42']; ?></div>
						<?php
						if(isset($interface2['name']['42']))
							echo "<div id='name2'>(".$interface2['name']['42'].")</div>";
						?> -->
					</div>
					<div style='width:17%;float:left;text-align:center;'>
						QTY
						<!-- <div id='name1'><?php echo $interface1['name']['43']; ?></div>
						<?php
						if(isset($interface2['name']['43']))
							echo "<div id='name2'>(".$interface2['name']['43'].")</div>";
						?> -->
					</div>
					<div style='width:19%;float:left;text-align:center;'>
						Subtotal
						<!-- <div id='name1'><?php echo $interface1['name']['44']; ?></div>
						<?php
						if(isset($interface2['name']['44']))
							echo "<div id='name2'>(".$interface2['name']['44'].")</div>";
						?> -->
					</div>
				</div>
				<div id='tastecontentbox' style='width:100%;float:left;max-height:calc(100% - 36px)'>
					<div id='tastecontent' style='width:100%;'>
					</div>
				</div>
			</div>
		</div>
		<div class='itemdis' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['13'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['13'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['13'];else; ?>'>
			<div class='numbox' style='width:18%;height:100%;float:left;'>
				<button value='清除' style='width:100%;height:calc(100% / 13);margin:0 0 20px 0;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['16']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['16']."</div>";else; ?></button>
				<input type='button' value='1' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='2' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='3' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='4' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='5' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='6' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='7' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='8' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='9' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='0' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='.' style='width:100%;height:calc(100% / 13);'>
			</div>
			<div style='width:82%;height:100%;float:left;'>
				<div style='width:calc(50% - 10px);height:calc(100% - 10px);float:left;padding:0 5px;'>
					<input type='text' name='view' style='width:calc(100% - 10px);margin:0 5px 5px 5px;text-align:right;' onfocus>
					<table id='funbox'>
						<tr>
							<td><button id='free' value='免費招待(數量)'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['17']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['17']."</div>";else; ?></button></td>
							<td><button id='dis1' value='單品折扣(折)'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['18']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['18']."</div>";else; ?></button></td>
							<td><button id='dis2' value='單品折讓(元)'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['19']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['19']."</div>";else; ?></button></td>
						</tr>
						<tr>
						<?php
						if(isset($itemdis['itemdis'])&&isset($itemdis['itemdis']['state1'])){//2022/3/17
						?>
							<td><button id='itemdis1' value='折扣' <?php if($itemdis['itemdis']['state1']==='1');else echo 'disabled'; ?>><?php echo "<div id='name1'>".$itemdis['itemdis']['name11']."</div>"; ?><?php if($itemdis['itemdis']['name12']!='')echo "<div id='name2'>".$itemdis['itemdis']['name11']."</div>";else; ?></button><input type='hidden' name='itemdis1' value='<?php echo $itemdis['itemdis']['number1']; ?>'><input type='hidden' name='itemdistype1' value='<?php echo $itemdis['itemdis']['type1']; ?>'></td>
							<td><button id='itemdis2' value='折扣' <?php if($itemdis['itemdis']['state2']==='1');else echo 'disabled'; ?>><?php echo "<div id='name1'>".$itemdis['itemdis']['name21']."</div>"; ?><?php if($itemdis['itemdis']['name22']!='')echo "<div id='name2'>".$itemdis['itemdis']['name22']."</div>";else; ?></button><input type='hidden' name='itemdis2' value='<?php echo $itemdis['itemdis']['number2']; ?>'><input type='hidden' name='itemdistype2' value='<?php echo $itemdis['itemdis']['type2']; ?>'></td>
							<td><button id='itemdis3' value='折扣' <?php if($itemdis['itemdis']['state3']==='1');else echo 'disabled'; ?>><?php echo "<div id='name1'>".$itemdis['itemdis']['name31']."</div>"; ?><?php if($itemdis['itemdis']['name32']!='')echo "<div id='name2'>".$itemdis['itemdis']['name32']."</div>";else; ?></button><input type='hidden' name='itemdis3' value='<?php echo $itemdis['itemdis']['number3']; ?>'><input type='hidden' name='itemdistype3' value='<?php echo $itemdis['itemdis']['type3']; ?>'></td>
						</tr>
						<tr>
							<td><button id='itemdis4' value='折扣' <?php if($itemdis['itemdis']['state4']==1);else echo 'disabled'; ?>><?php echo "<div id='name1'>".$itemdis['itemdis']['name41']."</div>"; ?><?php if($itemdis['itemdis']['name42']!='')echo "<div id='name2'>".$itemdis['itemdis']['name42']."</div>";else; ?></button><input type='hidden' name='itemdis4' value='<?php echo $itemdis['itemdis']['number4']; ?>'><input type='hidden' name='itemdistype4' value='<?php echo $itemdis['itemdis']['type4']; ?>'></td>
							<td><button id='itemdis5' value='折扣' <?php if($itemdis['itemdis']['state5']==1);else echo 'disabled'; ?>><?php echo "<div id='name1'>".$itemdis['itemdis']['name51']."</div>"; ?><?php if($itemdis['itemdis']['name52']!='')echo "<div id='name2'>".$itemdis['itemdis']['name52']."</div>";else; ?></button><input type='hidden' name='itemdis5' value='<?php echo $itemdis['itemdis']['number5']; ?>'><input type='hidden' name='itemdistype5' value='<?php echo $itemdis['itemdis']['type5']; ?>'></td>
							<td><button id='itemdis6' value='折扣' <?php if($itemdis['itemdis']['state6']==1);else echo 'disabled'; ?>><?php echo "<div id='name1'>".$itemdis['itemdis']['name61']."</div>"; ?><?php if($itemdis['itemdis']['name62']!='')echo "<div id='name2'>".$itemdis['itemdis']['name62']."</div>";else; ?></button><input type='hidden' name='itemdis6' value='<?php echo $itemdis['itemdis']['number6']; ?>'><input type='hidden' name='itemdistype6' value='<?php echo $itemdis['itemdis']['type6']; ?>'></td>
						<?php
						}
						else if($itemdis=='-1'){
						?>
							<td><button id='sdis1' value='單一價' <?php if($initsetting['init']['opensingledis']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['sdis1'].' '.$initsetting['init']['frontunit'].$initsetting['init']['singledis'].$initsetting['init']['unit']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['sdis1']."</div>";else; ?></button></td>
						<?php
						}
						else{
						?>
							<td><button id='sdis1' value='單一價' <?php if($itemdis['item']['opensingledis']==1);else echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['sdis1'].' '.$initsetting['init']['frontunit'].$itemdis['item']['singledis'].$initsetting['init']['unit']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['sdis1']."</div>";else; ?></button></td>
							<td><button id='qdis1' value='快速折扣' style='<?php if($itemdis['item']['opendis1']==1);else echo 'display:none;'; ?>' <?php if($itemdis['item']['opendis1']==1);else echo 'disabled'; ?>><?php if($itemdis['item']['dis1text1']!='')echo "<div id='name1'>".$itemdis['item']['dis1text1']."</div>";else echo "<div id='name1'>快速折扣按鈕</div>"; ?><?php if($itemdis['item']['dis1text2']!='')echo "<div id='name2'>".$itemdis['item']['dis1text2']."</div>";else; ?></button></td>
							<td><button id='qdis2' value='快速折讓' style='<?php if($itemdis['item']['opendis2']==1);else echo 'display:none;'; ?>' <?php if($itemdis['item']['opendis2']==1);else echo 'disabled'; ?>><?php if($itemdis['item']['dis2text1']!='')echo "<div id='name1'>".$itemdis['item']['dis2text1']."</div>";else echo "<div id='name1'>快速折讓按鈕</div>"; ?><?php if($itemdis['item']['dis2text2']!='')echo "<div id='name2'>".$itemdis['item']['dis2text2']."</div>";else; ?></button></td>
						<?php
						}
						?>
						</tr>
						<tr><?php //2020/4/14 單品優惠只用點數 ?>
							<td><button id='memberpoint' value='會員點數折讓' <?php if(isset($itemdis['memberpoint']['openpaypoint'])&&$itemdis['memberpoint']['openpaypoint']=='1')echo '';else echo 'disabled'; ?>><?php echo '<div id="name1">';if(isset($itemdis['memberpoint']['text1']))echo $itemdis['memberpoint']['text1'];else echo '點數兌換';echo '</div>'; ?><?php if(isset($itemdis['memberpoint']['text2'])&&$itemdis['memberpoint']['text2']!='')echo '<div id="name2">'.$itemdis['memberpoint']['text2'].'</div>'; ?></button></td>
						</tr>
					</table>
				</div>
				<div id='viewwindow' style='width:50%;height:100%;float:left;'>
					<table style='width:calc(100% - 10px);'>
						<tr>
							<td style='background-color:#F1FBFD;border-top:1px solid #dfdfdf;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['14']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['14']."</div>";else ?></td>
							<td style='background-color:#F1FBFD;border-top:1px solid #dfdfdf;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><div id='name1' class='name1'></div><div id='name2' class='name2'></div></td>
						</tr>
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['15']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['15']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='unitprice'>0</span><?php echo $initsetting['init']['unit']; ?></td>
						</tr>
						<!-- <tr>
							<td>備註與加料</td>
							<td><span id='taste'></span></td>
						</tr> -->
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['17']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['17']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='tastemoney'>0</span><?php echo $initsetting['init']['unit']; ?></td>
						</tr>
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['18']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['18']."</div>"; ?></td>
							<td style='text-align:right;'><span id='number'>0</span></td>
						</tr>
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['19']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['19']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='subtotal'>0</span><?php echo $initsetting['init']['unit']; ?></td>
						</tr>
						<tr>
							<td><?php if(isset($interface1['name']['remainingmemberpoint']))echo "<div id='name1'>".$interface1['name']['remainingmemberpoint']."</div>";else echo "<div id='name1'>剩餘點數</div>"; ?><?php if(isset($interface2['name']['remainingmemberpoint']))echo "<div id='name2'>".$interface2['name']['remainingmemberpoint']."</div>"; ?></td>
							<td style='text-align:right;'><span id='initpoint'>0</span></td>
						</tr>
						<tr>
							<td><?php if(isset($interface1['name']['memberpoint']))echo "<div id='name1'>".$interface1['name']['memberpoint']."</div>";else echo "<div id='name1'>點數兌換</div>"; ?><?php if(isset($interface2['name']['memberpoint']))echo "<div id='name2'>".$interface2['name']['memberpoint']."</div>"; ?></td>
							<td style='text-align:right;'><span id='memberpoint'>0</span><input type='hidden' name='dispointtime' value='0'></td>
						</tr>
						<!-- <tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['20']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['20']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?> --><span id='dis1' style='display:none;'>0</span><!-- <?php echo $initsetting['init']['unit']; ?></td>
						</tr> -->
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['21']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['21']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='dis2'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' id='disbut'></td>
						</tr>
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['22']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['22']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='total'>0</span><?php echo $initsetting['init']['unit']; ?></td>
						</tr>
					</table>
					<div id='checkfun'>
						<button id='submit' value='確認' style='background-color:#D5DC75;color:#000000;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['20']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['20']."</div>";else; ?></button>
						<button id='cancel' value='取消' style='background-color:#D5DC75;color:#000000;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['21']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['21']."</div>"; ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
		if($initsetting['init']['changehint']=='1'){
		?>
		<div class='changehint' style='padding:0;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['45'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['45'];elseif($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['45'];else;?>'>
			<table>
				<?php
				if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0'){
				}
				else{
				?>
				<tr>
					<td>
						<div style='float:left;width:30%;padding:8px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<?php if($interface1!='-1')echo "<div id='name1'>電子發票</div>"; ?>
							<?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['46']."</div>";else; ?>
						</div>
						<input type='text' class='inv' style='width:70%;float:left;' readonly>
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td>
						<div style='float:left;width:30%;padding:8px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['46']."</div>"; ?>
							<?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['46']."</div>";else; ?>
						</div>
						<input type='text' class='should' style='width:70%;float:left;' readonly>
					</td>
				</tr>
				<tr>
					<td>
						<div style='float:left;width:30%;padding:8px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['47']."</div>"; ?>
							<?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['47']."</div>";else; ?>
						</div>
						<input type='text' class='already' style='width:70%;float:left;' readonly>
					</td>
				</tr>
				<tr>
					<td>
						<div style='float:left;width:30%;padding:8px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							<?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['48']."</div>"; ?>
							<?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['48']."</div>";else; ?>
						</div>
						<input type='text' class='change' style='width:70%;float:left;color:#ff0000;' readonly>
					</td>
				</tr>
				<tr>
					<td>
						<div id='timehint' style='width:30%;padding:8px;font-size:13px;color:#FF4500;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
							will be closed after <span id='time'></span> seconds
						</div>
						<button id='reload' style='width:70%;float:left;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['47']."</div>";if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['47']."</div>";else; ?></button>
					</td>
				</tr>
			</table>
		</div>
		<?php
		}
		else{
		}
		?>
		<div class='setchange' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['49'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['49'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['49'];else;?>'>
			<div class='numbox' style='width:18%;height:100%;float:left;'>
				<button value='清除' style='width:100%;height:calc(100% / 13);margin:0 0 20px 0;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['16']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['16']."</div>";else; ?></button>
				<input type='button' value='1' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='2' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='3' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='4' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='5' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='6' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='7' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='8' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='9' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='0' style='width:100%;height:calc(100% / 13);'>
				<input type='button' value='.' style='width:100%;height:calc(100% / 13);'>
			</div>
			<div style='width:calc(82% - 2px);height:100%;float:left;margin-left:2px;'>
				<div style='float:left;width:17%;'>
					<?php if($interface1!='-1')echo "<div id='name1' style='font-size:17px;'>".$interface1['name']['50']."</div>"; ?>
					<?php if($interface2!='-1')echo "<div id='name2' style='font-size:13px;'>".$interface2['name']['50']."</div>";else; ?>
				</div>
				<?php echo $initsetting['init']['frontunit']; ?><input type='text' name='view' style='width:calc(50% - 12px);margin:5px;text-align:right;float:left;' value='<?php echo $machinedata['basic']['change']; ?>' readonly><div style='float:left;height:35px;line-height:35px;'><?php echo $initsetting['init']['unit']; ?></div>
				<button id='settingchange' style='width:105px;height:55px;margin:10px calc((100% - 105px) / 2);background-color:#D5DC75;color:#000000;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['48']."</div>";if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['48']."</div>";else; ?></button>
			</div>
		</div>
		<div class='salelist' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['51'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['51'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['51'];else;?>'>
			<div style='width:calc(58% - 1px);height:calc(80% - 1px);margin:0 1px 1px 0;float:left;'>
				<div style='width:100%;height:23px;margin:0 1px 1px 0;float:left;'>
					BIZDATE：<span class='viewbizdate'></span>
				</div>
				<div id='salelist' style='width:100%;height:calc(100% - 23px);border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'>
					<div class='saletitle' style='background-color:#EFEBDE;width:max-content;overflow:hidden;'>
						<div style='width:97px;'>
							bizdate
						</div>
						<div style='width:90px;'>
							No
						</div>
						<div style='width:100px;'>
							CONSECNUMBER
						</div>
						<div style='width:121px;
						<?php
						if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0'){
							echo "display:none;";
						}
						else{
						}
						?>'>
							inv
						</div>
						<div style='width:121px;
						<?php
						if($initsetting['init']['useinv']=='1'){
						}
						else{
							echo "display:none;";
						}
						?>'>
							carrier
						</div>
						<div style='width:121px;'>
							money
						</div>
						<div style='width:121px;'>
							member
						</div>
						<div style='width:121px;'>
							createname
						</div>
						<div style='width:121px;'>
							delivery
						</div>
						<div style='width:152px;'>
							createdatetime
						</div>
						<div style='width:38px;height:1px;'>
						</div>
					</div>
					<div id='salecontent' style='background-color:#ffffff;width:max-content;height:max-content;overflow-x:hidden;overflow-y:auto;'>
					</div>
				</div>
			</div>
			<div style='width:calc(42% - 1px);height:69px;margin-left:1px;margin-bottom:1px;float:left;overflow:hidden;'>
				<div style='width:50%;height:calc(100% / 3);float:left;'>Date:<span id='date'></span><input type='hidden' id='credate'></div>
				<div style='width:50%;height:calc(100% / 3);float:left;'>NO:<span id='listno'></span></div>
				<div style='width:100%;height:calc(100% / 3);float:left;'><?php if(isset($interface1['name']['membername']))echo $interface1['name']['membername'];else echo '會員名稱'; ?>:<span id='membername'></span></div>
				<div style='width:100%;height:calc(100% / 3);float:left;'><?php if(isset($interface1['name']['memberphone']))echo $interface1['name']['memberphone'];else echo '會員電話'; ?>:<span id='membertel'></span></div>
			</div>
			<div id='list' style='width:calc(42% - 1px);height:calc(80% - 71px);margin:1px 0 1px 1px;border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'>
				<div class='listtitle' style='width:max-content;background-color:#EFEBDE;padding-right:3px;overflow:hidden;'>
					<div style='width:16px;min-height:1px;'>
					</div>
					<div style='width:55px;'>
						No
						<!-- <div id='name1'><?php echo $interface1['name']['7']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['7']))echo '('.$interface2['name']['7'].')';else; ?></div> -->
					</div>
					<div style='width:313px;'>
						Item
						<!-- <div id='name1'><?php echo $interface1['name']['8']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['8']))echo '('.$interface2['name']['8'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:right;'>
						U/P
						<!-- <div id='name1'><?php echo $interface1['name']['9']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['9']))echo '('.$interface2['name']['9'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:center;'>
						QTY
						<!-- <div id='name1'><?php echo $interface1['name']['10']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['10']))echo '('.$interface2['name']['10'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:right;'>
						Subtotal
						<!-- <div id='name1'><?php echo $interface1['name']['11']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['11']))echo '('.$interface2['name']['11'].')';else; ?></div> -->
					</div>
				</div>
				<div id='listcontent' style='background-color:#ffffff;width:max-content;font-size:17px;'>
				</div>
			</div>
			<div style='width:299px;height:calc(20% - 1px);margin:1px 1px 0 0;float:left;'>
				<button id='editpay' style='width:149px;height:calc(50% - 1px);margin:0 1px 1px 0;float:left;' disabled><div><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['editpay']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['editpay']."</div>";else; ?></div></button>
				<button id='reinv' style='width:149px;height:calc(50% - 1px);margin:0 0 1px 0;float:left;<?php if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0')echo 'visibility: hidden;'; ?>' disabled><div>重印發票</div></button>
				<button id='reorder' style='width:149px;height:calc(50% - 1px);margin:1px 1px 0 0;float:left;' disabled><div><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['editlist']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['editlist']."</div>";else; ?></div></button>
				<button id='rekvm' style='width:149px;height:calc(50% - 1px);margin:0 0 1px 0;float:left;<?php if(!isset($initsetting['init']['kvm'])||$initsetting['init']['kvm']=='0')echo 'visibility: hidden;'; ?>' disabled><div><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['rekvm']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['rekvm']."</div>";else; ?></div></button>
			</div>
			<div id='rightnow' style='width:calc(58% - 302px);height:calc(20% - 1px);margin:1px 1px 0 1px;float:left;<?php if(isset($initsetting['init']['salelistmoneydata'])&&$initsetting['init']['salelistmoneydata']=='0')echo 'visibility:hidden;';else ; ?>'>
				<table style='width:100%;height:100%;border-collapse:collapse;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					<tr>
						<td style='width:25%;'><?php if($interface1!='-1')echo $interface1['name']['summoney'];else;?></td>
						<td style='width:25%;padding-right:10px;text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='ttmoney'>0</span><?php if($initsetting['init']['unit']=='')echo '元';else echo $initsetting['init']['unit']; ?></td>
						<td style='width:25%;'><?php if($interface1!='-1')echo $interface1['name']['voidmoney'];else;?></td>
						<td style='width:25%;padding-right:10px;text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='voidmoney'>0</span><?php if($initsetting['init']['unit']=='')echo '元';else echo $initsetting['init']['unit']; ?></td>
					</tr>
					<tr>
						<td style='width:25%;'><?php if($interface1!='-1')echo $interface1['name']['sumqty'];else;?></td>
						<td id='ttcount' style='width:25%;padding-right:10px;text-align:right;'>0</td>
						<td style='width:25%;'><?php if($interface1!='-1')echo $interface1['name']['voidqty'];else;?></td>
						<td id='voidcount' style='width:25%;padding-right:10px;text-align:right;'>0</td>
					</tr>
				</table>
			</div>
			<div id='fun' style='width:calc(42% - 1px);height:calc(20% - 1px);margin-left:1px;margin-top:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<table style='width:100%;height:100%;'>
					<tr>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='forall' value='重印' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist4']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist4']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='list' value='明細單' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist5']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist5']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='tag' value='貼紙' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist6']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist6']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='kitchen' value='工作單' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist7']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist7']."</div>";else; ?></button></td>
					</tr>
					<tr>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='changecheck' value='反選' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist3']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist3']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='prebizdate' value='前一日' style='width:100%;height:100%;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist1']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist1']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='nextbizdate' value='後一日' style='width:100%;height:100%;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist2']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist2']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='exit' value='離開' style='width:100%;height:100%;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist8']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist8']."</div>";else; ?></button></td>
					</tr>
				</table>
			</div>
		</div>
		<div class='salevoid' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['52'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['52'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['52'];else;?>'>
			<div style='width:calc(58% - 1px);height:100%;margin-right:1px;float:left;'>
				<!-- <div class='bizdatelabel' style='width:100%;height:23px;margin-bottom:1px;float:left;'><span><?php if($interface1!='-1')echo $interface1['name']['salebizdatelabel'];if($interface1!='-1'&&$interface2!='-1'&&isset($interface2['name']['salebizdatelabel']))echo ' /'.$interface2['name']['salebizdatelabel'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['salebizdatelabel']))echo $interface2['name']['salebizdatelabel'];else;?>:</span><span id='salebizdate'></span></div>
				<div id='salelist' style='width:100%;height:calc(100% - 25px);margin-top:1px;border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'>-->
				<div style='width:100%;height:23px;margin:0 1px 1px 0;float:left;'>
					BIZDATE：<span class='viewbizdate'></span>
				</div>
				<div id='salelist' style='width:100%;height:calc(100% - 23px);border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'>
					<div class='saletitle' style='background-color:#EFEBDE;width:max-content;overflow:hidden;'>
						<div style='width:97px;'>
							bizdate
						</div>
						<div style='width:90px;'>
							No
						</div>
						<div style='width:100px;'>
							CONSECNUMBER
						</div>
						<div style='width:121px;
						<?php
						if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0'){
							echo "display:none;";
						}
						else{
						}
						?>'>
							inv
						</div>
						<div style='width:121px;
						<?php
						if($initsetting['init']['useinv']=='1'){
						}
						else{
							echo "display:none;";
						}
						?>'>
							carrier
						</div>
						<div style='width:121px;'>
							money
						</div>
						<div style='width:121px;'>
							member
						</div>
						<div style='width:121px;'>
							createname
						</div>
						<div style='width:121px;'>
							delivery
						</div>
						<div style='width:152px;'>
							createdatetime
						</div>
						<div style='width:38px;height:1px;'>
						</div>
					</div>
					<div id='salecontent' style='background-color:#ffffff;width:max-content;height:max-content;'>
					</div>
				</div>
			</div>
			<div style='width:calc(42% - 1px);height:69px;margin-left:1px;margin-bottom:1px;float:left;overflow:hidden;'>
				<div style='width:50%;height:calc(100% / 3);float:left;'>Date:<span id='date'></span><input type='hidden' id='credate'></div>
				<div style='width:50%;height:calc(100% / 3);float:left;'>NO:<span id='listno'></span></div>
				<div style='width:100%;height:calc(100% / 3);float:left;'><?php if(isset($interface1['name']['membername']))echo $interface1['name']['membername'];else echo '會員名稱'; ?>:<span id='membername'></span></div>
				<div style='width:100%;height:calc(100% / 3);float:left;'><?php if(isset($interface1['name']['memberphone']))echo $interface1['name']['memberphone'];else echo '會員電話'; ?>:<span id='membertel'></span></div>
			</div>
			<div id='list' style='width:calc(42% - 1px);height:calc(80% - 71px);margin:1px 0 1px 1px;border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'>
				<div class='listtitle' style='width:max-content;background-color:#EFEBDE;padding-right:3px;overflow:hidden;'>
					<div style='width:16px;min-height:1px;'>
					</div>
					<div style='width:55px;'>
						No
						<!-- <div id='name1'><?php echo $interface1['name']['7']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['7']))echo '('.$interface2['name']['7'].')';else; ?></div> -->
					</div>
					<div style='width:313px;'>
						Item
						<!-- <div id='name1'><?php echo $interface1['name']['8']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['8']))echo '('.$interface2['name']['8'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:right;'>
						U/P
						<!-- <div id='name1'><?php echo $interface1['name']['9']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['9']))echo '('.$interface2['name']['9'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:center;'>
						QTY
						<!-- <div id='name1'><?php echo $interface1['name']['10']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['10']))echo '('.$interface2['name']['10'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:right;'>
						Subtotal
						<!-- <div id='name1'><?php echo $interface1['name']['11']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['11']))echo '('.$interface2['name']['11'].')';else; ?></div> -->
					</div>
				</div>
				<div id='listcontent' style='background-color:#ffffff;width:max-content;font-size:17px;'>
				</div>
			</div>
			<div id='fun' style='width:calc(42% - 1px);height:calc(20% - 1px);margin-left:1px;margin-top:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<table style='width:100%;height:100%;'>
					<tr>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='salevoidbyinv' value='依發票作廢' style='width:100%;height:100%;' <?php if($initsetting['init']['useinv']=='0')echo 'disabled';else; ?>><?php if(isset($buttons1['name']['salevoidbyinv']))echo "<div id='name1'>".$buttons1['name']['salevoidbyinv']."</div>";else echo "<div id='name1'>依發票作廢</div>"; ?><?php if(isset($buttons2['name']['salevoidbyinv']))echo "<div id='name2'>".$buttons2['name']['salevoidbyinv']."</div>";else if($buttons2!='-1') echo "<div id='name2'>依發票作廢</div>"; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'></td>
					</tr>
					<tr>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='void' value='作廢' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salevoid1']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salevoid1']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='prebizdate' value='前一日' style='width:100%;height:100%;' <?php if(isset($initsetting['init']['voiddate'])&&$initsetting['init']['voiddate']=='0')echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salevoid2']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salevoid2']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='nextbizdate' value='後一日' style='width:100%;height:100%;' <?php if(isset($initsetting['init']['voiddate'])&&$initsetting['init']['voiddate']=='0')echo 'disabled'; ?>><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salevoid3']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salevoid3']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='exit' value='離開' style='width:100%;height:100%;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salevoid4']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salevoid4']."</div>";else; ?></button></td>
					</tr>
				</table>
			</div>
		</div>
		<?php
		if($initsetting['init']['useinv']=='1'){
		?>
		<div class='salevoidbyinv' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['salevoidbyinv']))echo $interface1['name']['salevoidbyinv'];else echo '依發票作廢';if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['52'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['52'];else;?>'>
			<input type='text' name='inv' placeholder='請輸入發票...' style='width:calc(100% - 122px);height:calc(60px - 2px);margin:1px;float:left;padding:3px 5px;border:1px solid #898989;border-radius:5px;background-color:#ffffff;font-size:30px;'>
			<button id='check' style='width:calc(120px - 2px);height:calc(60px - 2px);margin:1px;float:left;padding:0;border:1px solid #898989;border-radius:5px;' disabled>作廢</button>
			<div style='width:calc(100% - 4px);height:calc(100% - 62px);margin:1px;float:left;border:1px solid #898989;border-radius:5px;background-color: #848284;'>
				<div id='checkcontent' style='width:100%;background-color:#ffffff;border-top-left-radius:5px;border-top-right-radius:5px;'></div>
			</div>
		</div>
		<?php
		}
		else{
		}
		?>
		<div class='viewtemp' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['53'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['53'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['53'];else; ?>'>
		<?php
		if(!isset($initsetting['init']['searchbox'])||$initsetting['init']['searchbox']=='1'){//查詢帳單輸入框
		?>
			<div style='width:calc(58% - 1px);height:69px;margin-right:1px;margin-bottom:1px;float:left;overflow:hidden;'>
				<input type='text' style='width:calc(100% - 10px);height:39px;margin:15px 5px;padding:5px 10px;float:left;' placeholder='Search' name='salebarcode'>
			</div>
		<?php
		}
		else{
		}
		?>
			<div style='width:calc(42% - 1px);height:69px;margin-left:1px;margin-bottom:1px;float:right;overflow:hidden;'>
				<div style='width:50%;height:calc(100% / 3);float:left;'>Date:<span id='date'></span><input type='hidden' id='credate'><input type='hidden' id='credatetime'></div>
				<div style='width:50%;height:calc(100% / 3);float:left;'>NO:<span id='listno'></span></div>
				<div style='width:100%;height:calc(100% / 3);float:left;'><?php if(isset($interface1['name']['membername']))echo $interface1['name']['membername'];else echo '會員名稱'; ?>:<span id='membername'></span></div>
				<div style='width:100%;height:calc(100% / 3);float:left;'><?php if(isset($interface1['name']['memberphone']))echo $interface1['name']['memberphone'];else echo '會員電話'; ?>:<span id='membertel'></span></div>
			</div>
		<?php
		if(!isset($initsetting['init']['searchbox'])||$initsetting['init']['searchbox']=='1'){//查詢帳單輸入框
		?>
			<div style='width:calc(58% - 1px);height:calc(80% - 71px);margin:1px 1px 1px 0;float:left;'>
		<?php
		}
		else{
		?>
			<div style='width:calc(58% - 1px);height:calc(80%);margin:1px 1px 1px 0;float:left;'>
		<?php
		}
		?>
				<div id='salelist' style='width:100%;height:100%;border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'>
					<div class='saletitle' style='background-color:#EFEBDE;width:max-content;overflow:hidden;'>
						<div style='width:97px;'>
							bizdate
						</div>
						<?php
						/*if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0'){
							echo "<div style='width:calc(80% / 6 - 6px);'>";
						}
						else{*/
							echo "<div style='width:90px;'>";
						//}
						?>
							No
						</div>
						<?php
						/*if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0'){
							echo "<div style='width:calc(80% / 6 - 6px);'>";
						}
						else{*/
							echo "<div style='width:100px;'>";
						//}
						?>
							CONSECNUMBER
						</div>
						<div style='width:121px;
						<?php
						if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0'){
							echo "display:none;";
						}
						else{
						}
						?>'>
							inv
						</div>
						<div style='width:121px;
						<?php
						if($initsetting['init']['useinv']=='1'){
						}
						else{
							echo "display:none;";
						}
						?>'>
							carrier
						</div>
						<div style='width:121px;'>
							money
						</div>
						<div style='width:121px;'>
							member
						</div>
						<div style='width:121px;'>
							createname
						</div>
						<div style='width:121px;'>
							delivery
						</div>
						<div style='width:152px;'>
							createdatetime
						</div>
						<div style='width:38px;height:1px;'>
						</div>
					</div>
					<div id='salecontent' style='background-color:#ffffff;width:max-content;height:max-content;'>
					</div>
				</div>
			</div>
			<div id='list' style='width:calc(42% - 1px);height:calc(80% - 71px);margin:1px 0 1px 1px;border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'>
				<div class='listtitle' style='width:max-content;background-color:#EFEBDE;padding-right:3px;overflow:hidden;'>
					<div style='width:16px;min-height:1px;'>
					</div>
					<div style='width:55px;'>
						No
						<!-- <div id='name1'><?php echo $interface1['name']['7']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['7']))echo '('.$interface2['name']['7'].')';else; ?></div> -->
					</div>
					<div style='width:313px;'>
						Item
						<!-- <div id='name1'><?php echo $interface1['name']['8']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['8']))echo '('.$interface2['name']['8'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:right;'>
						U/P
						<!-- <div id='name1'><?php echo $interface1['name']['9']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['9']))echo '('.$interface2['name']['9'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:center;'>
						QTY
						<!-- <div id='name1'><?php echo $interface1['name']['10']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['10']))echo '('.$interface2['name']['10'].')';else; ?></div> -->
					</div>
					<div style='width:55px;text-align:right;'>
						Subtotal
						<!-- <div id='name1'><?php echo $interface1['name']['11']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['11']))echo '('.$interface2['name']['11'].')';else; ?></div> -->
					</div>
				</div>
				<div id='listcontent' style='background-color:#ffffff;width:max-content;font-size:17px;'>
				</div>
			</div>
			<!-- <div id='listhint' style='width:calc(42% - 1px);height:calc(20% - 1px);margin:1px 0 1px 1px;border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'><?php //預計顯示帳單備註 ?>
				<div style='width:max-content;background-color:#EFEBDE;padding-right:3px;overflow:hidden;'>
					<div>
						Hint:
					</div>
				</div>
				<div id='hint' style='background-color:#ffffff;width:max-content;font-size:17px;'>
				</div>
			</div> -->
			<div id='fun' style='width:calc(42% - 1px);height:calc(20% - 1px);margin-left:1px;margin-top:1px;float:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<table style='width:100%;height:100%;'>
					<tr>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='forall' value='重印' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist4']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist4']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='list' value='明細單' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist5']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist5']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='tag' value='貼紙' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist6']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist6']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='kitchen' value='工作單' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist7']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist7']."</div>";else; ?></button></td>
					</tr>
					<tr>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='changecheck' value='反選' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist3']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist3']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='editoutman' value='調整外送員' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1'&&isset($buttons1['name']['editoutman']))echo "<div id='name1'>".$buttons1['name']['editoutman']."</div>"; ?><?php if($buttons2!='-1'&&isset($buttons2['name']['editoutman']))echo "<div id='name2'>".$buttons2['name']['editoutman']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='' value='' style='width:100%;height:100%;' disabled></button></td>
						<td style='width:calc(100% / 4);height:calc(100% / 2);'><button id='exit' value='離開' style='width:100%;height:100%;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salelist8']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salelist8']."</div>";else; ?></button></td>
					</tr>
				</table>
			</div>
			<div id='otherfun' style='width:calc(58% - 1px);height:calc(10% - 2px);margin:1px 1px 1px 0;float:left;'>
				<button id='openinv' value='開立發票' style='width:calc(100% / 4 - 1px);height:100%;margin:0 1px 0 0;float:left;<?php if($initsetting['init']['useoinv']=='0'&&$initsetting['init']['useinv']=='0')echo 'visibility: hidden;'; ?>' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['openinv']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['openinv']."</div>";else; ?></button>
				<!-- <button id='reinv' value='補印發票' style='width:calc(100% / 4 - 2px);height:100%;margin:0 1px;float:left;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['reinv']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['reinv']."</div>";else; ?></button> -->
				<!-- <button id='plus' value='加點' style='width:calc(100% * 3 / 4 - 1px);height:100%;margin:0 0 0 1px;float:left;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['viewtemp1']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['viewtemp1']."</div>";else; ?></button> -->
				<button id='sale' value='結帳' style='width:calc(100% * 3 / 8 - 1px);height:100%;margin:0 0 0 1px;float:left;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['viewtemp2']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['viewtemp2']."</div>";else; ?></button>
				<button id='plus' value='加點' style='width:calc(100% * 3 / 8 - 1px);height:100%;margin:0 0 0 1px;float:left;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['viewtemp1']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['viewtemp1']."</div>";else; ?></button>
			</div>
			<div style='width:calc((58% / 4) - 1px);height:calc(10% - 1px);margin:1px 1px 0 0;margin-bottom:1px;float:left;'>
				<button id='viewvoid' style='width:100%;height:100%;' disabled><div id='name1'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['voidtemp']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['voidtemp']."</div>";else; ?></div></button>
			</div>
			<div id='rightnow' style='width:calc((58% / 4) * 3 - 2px);height:calc(10% - 1px);margin:1px 1px 0 1px;float:left;'>
				<table style='width:100%;height:100%;border-collapse:collapse;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					<tr>
						<td style='width:80px;'><?php if($interface1!='-1')echo $interface1['name']['tempmoney'];else;?></td>
						<td style='width:calc(50% - 180px);padding-right:100px;text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='ttmoney'>0</span><?php if($initsetting['init']['unit']=='')echo '元';else echo $initsetting['init']['unit']; ?></td>
						<td style='width:80px;'><?php if($interface1!='-1')echo $interface1['name']['tempqty'];else;?></td>
						<td id='ttcount' style='width:calc(50% - 180px);padding-right:100px;text-align:right;'>0</td>
					</tr>
				</table>
			</div>
		</div>
		<div class='tabinput' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title=<?php if($interface1!='-1'){
								echo $interface1['name']['tabnumtext'];
						  }
						  else{
								echo '桌號';
						   }
					?>>
			<div id='view' style='width:100%;overflow:hidden;padding:10px 0;'>
				<div style='width:80px;margin:0 10px;float:left;line-height:45px;'>
					<span><?php if($interface1!='-1'){
								echo $interface1['name']['tabnumtext'];
						  }
						  else{
								echo '桌號';
						   }
					?></span>
				</div>
				<div id='tabnum' style='width:50%;height:45px;border-radius: 5px;padding:10px 5px;float:left;text-align:right;overflow:hidden;border:1px solid #898989;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'></div>
			</div>
			<div id='buttons' style='width:100%;height:calc(100% - 65px);margin-top:2px;'>
				<input type='hidden' name='tag' value='<?php if(strlen($machinedata['basic']['startoinv'])==10)echo 'num';else echo 'en'; ?>'>
				<input type='button' class='input' style='font-size: 2.5em;' value='7'>
				<input type='button' class='input' style='font-size: 2.5em;'value='8'>
				<input type='button' class='input' style='font-size: 2.5em;'value='9'>
				<input type='button' id='pre' class='pre' style='background-color: #c6d3e3;' value='Pre' disabled>
				<input type='button' class='input' style='font-size: 2.5em;'value='4'>
				<input type='button' class='input' style='font-size: 2.5em;'value='5'>
				<input type='button' class='input' style='font-size: 2.5em;'value='6'>
				<input type='button' id='next' class='next' style='background-color: #c6d3e3;' value='Next' disabled>
				<input type='button' class='input' style='font-size: 2.5em;'value='1'>
				<input type='button' class='input' style='font-size: 2.5em;'value='2'>
				<input type='button' class='input' style='font-size: 2.5em;'value='3'>
				<input type='button' id='ac' style='background-color: #c6d3e3;' value='<?php if(isset($buttons1['numberpad']['ac']))echo $buttons1['numberpad']['ac'];else echo '重填' ?>'>
				<input type='button' class='input' style='font-size: 2.5em;'value='0'>
				<input type='button' class='input' style='font-size: 2.5em;'value='.'>
				<input type='button' class='input' style='font-size: 2.5em;'value='-'>
				<input type='button' id='change' style='background-color: #c6d3e3;' value='<?php if(isset($buttons1['numberpad']['keyboard']))echo $buttons1['numberpad']['keyboard'];else echo '英/數' ?>'>
				<button id='submit' style='background-color: #c6d3e3;' value='確定'><?php if(isset($buttons1['numberpad']['submit']))echo $buttons1['numberpad']['submit'];else echo '確定' ?></button>
				<button id='cancel' style='background-color: #c6d3e3;' value='取消'><?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?></button>
			</div>
		</div>
		<?php
		if(isset($initsetting['init']['salehint'])&&$initsetting['init']['salehint']=='0'){
		}
		else{
		?>
		<div class='spend' style='text-align:center;' title='消費金額提醒'>
			<table style='width:100%;height=100%;'>
				<caption>
				<?php
				echo '('.$basic['basic']['story'].')';
				echo '<br>';
				echo $basic['basic']['storyname'];
				?>
				</caption>
				<tr>
					<td>營業日期:<span id='nowbizdate'><?php if(isset($_GET['bizdate']))echo $_GET['bizdate'];else echo $timeini['time']['bizdate']; ?></span></td>
					<td>目前班別:<span id='nowzcounter'><?php echo $timeini['time']['zcounter']; ?></span></td>
				</tr>
				<tr>
					<td style='width:50%;text-align:right;'>原始價格</td>
					<td style='width:50%;text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='initspend'>0</span><?php echo $initsetting['init']['unit']; ?></td>
				</tr>
				<tr>
					<td style='text-align:right;'>折扣價格</td>
					<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='disspend'>0</span><?php echo $initsetting['init']['unit']; ?></td>
				</tr>
				<tr>
					<td style='text-align:right;'>消費金額</td>
					<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='spend'>0</span><?php echo $initsetting['init']['unit']; ?></td>
				</tr>
				<tr>
					<td style='text-align:right;'>低銷差價</td>
					<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='difffloor'>0</span><?php echo $initsetting['init']['unit']; ?></td>
				</tr>
				<div id="" class="">
					<tr>
					<td style='text-align:right;'>服務費</td>
					<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='charge'>0</span><?php echo $initsetting['init']['unit']; ?></td>
				</tr>
				</div>
				<tr>
					<td style='text-align:right;'>總金額</td>
					<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='tempsum'>0</span><?php echo $initsetting['init']['unit']; ?></td>
				</tr>
			</table>
			<br>
			<button class="closesysmeg" value="確認" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['42']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['42']."</div>"; ?></button>
			<div style="width:100%;">
				<table style="width:100%;">
					<tr>
						<td>桌加客服<br>line官方帳號</td>
						<td><img src='./img/clientservice.png' style='width:100px;height:100px;'></td>
					</tr>
				</table>
			</div>
			<div id=''>
				<table style="width:100%;">
					<caption>外送平台</caption>
					<tr>
						<td>UberEats</td>
						<td><?php
						if(isset($initsetting['ubereats']['openubereats'])&&$initsetting['ubereats']['openubereats']=='1'){//2022/8/1 開啟Ubereats串接
							if(isset($basic['ubereats']['id'])&&$basic['ubereats']['id']!=''){
								$postdata=array(
									'company' => $basic['basic']['company'],
									'dep' => $basic['basic']['story'],
									'store_id' => $basic['ubereats']['id']
								);
								//print_r($postdata);
								$ch = curl_init();
								//echo '<br>url='.$url.'<br>';
								curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/ubereats/check_posdata.php');
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($ch, CURLOPT_POST, 1);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
								// Edit: prior variable $postFields should be $postfields;
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
								//curl_setopt($ch, CURLOPT_HTTPHEADER, $header2);
								$tempdata = curl_exec($ch);
								$resule=json_decode($tempdata,true);
								curl_close($ch);

								//echo $tempdata;
								if($tempdata=='success'){
									$postdata=array(
										'store_id' => $basic['ubereats']['id']
									);
									//print_r($postdata);
									$ch = curl_init();
									//echo '<br>url='.$url.'<br>';
									curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/ubereats/check_storestatus.php');
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
									curl_setopt($ch, CURLOPT_POST, 1);
									curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
									// Edit: prior variable $postFields should be $postfields;
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
									//curl_setopt($ch, CURLOPT_HTTPHEADER, $header2);
									$tempdata = curl_exec($ch);
									$resule=json_decode($tempdata,true);
									curl_close($ch);
									//echo $resule['status'];
									if($resule['status']=='ONLINE'){
									}
									else if($resule['status']=='OFFLINE'){//閉店或暫停接單
									}
								}
								else{
								}
							}
							else{
								echo '<button class="loginubereats">註冊</button>';
							}
						}
						else{
						}
						?></td>
					</tr>
				</table>
			</div>
		</div>
		<?php
		}
		?>
		<div class='openmoney' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='價格'>
			<div id='view' style='width:100%;overflow:hidden;padding:10px 0;'>
				<div style='width:80px;margin:0 10px;float:left;line-height:45px;'>
					<span>價格</span>
				</div>
				<input type='number' value='0' id='money' style='width:50%;height:45px;border-radius: 5px;padding:10px 5px;float:left;text-align:right;overflow:hidden;border:1px solid #898989;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>>
			</div>
			<div id='buttons' style='width:100%;height:calc(100% - 65px);margin-top:2px;'>
				<input type='hidden' name='tag' value='<?php if(strlen($machinedata['basic']['startoinv'])==10)echo 'num';else echo 'en'; ?>'>
				<input type='button' class='input' style='font-size: 2.5em;' value='7'>
				<input type='button' class='input' style='font-size: 2.5em;' value='8'>
				<input type='button' class='input' style='font-size: 2.5em;' value='9'>
				<input type='button' id='changesign' style='font-size: 2.5em;background-color: #c6d3e3;' value='+/-'>
				<input type='button' class='input' style='font-size: 2.5em;' value='4'>
				<input type='button' class='input' style='font-size: 2.5em;' value='5'>
				<input type='button' class='input' style='font-size: 2.5em;' value='6'>
				<input type='button' id='ac' style='font-size: 2.5em;background-color: #c6d3e3;' value='<?php if(isset($buttons1['numberpad']['ac']))echo $buttons1['numberpad']['ac'];else echo '重填' ?>'>
				<input type='button' class='input' style='font-size: 2.5em;' value='1'>
				<input type='button' class='input' style='font-size: 2.5em;' value='2'>
				<input type='button' class='input' style='font-size: 2.5em;' value='3'>
				<input type='button' id='back' style='font-size: 2.5em;background-color: #c6d3e3;' value='倒退'>
				<input type='button' class='input' style='font-size: 2.5em;' value='0'>
				<input type='button' class='input' style='font-size: 2.5em;' value='.'>
				<input type='button' id='empty' style='background-color: #c6d3e3;' disabled>
				<input type='button' id='empty' style='background-color: #c6d3e3;' disabled>
				<button id='submit' style='font-size: 2.5em;background-color: #c6d3e3;' value='確定'><?php if(isset($buttons1['numberpad']['submit']))echo $buttons1['numberpad']['submit'];else echo '確定' ?></button>
				<button id='cancel' style='font-size: 2.5em;background-color: #c6d3e3;' value='取消'><?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?></button>
			</div>
		</div>
		<div class='meminput' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['memtitle']))echo $interface1['name']['memtitle'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['memtitle'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['memtitle'];else; ?>'>
			<input type='text' id='memtel' style='float:left;width:calc(150% / 3 - 2px);height:60px;line-height:60px;background-color:#ffffff;border:1px #b1b1b1 solid;border-radius:5px;margin:0 1px;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>>
			<button style='float:left;width:calc(50% / 3 - 2px);height:60px;margin:0 1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: transparent;float:left;' id='searchmem' value='<?php if($buttons1!='-1'&&isset($buttons1['name']['search']))echo $buttons1['name']['search'];else echo '查詢'; ?>'><?php if($buttons1!='-1'&&isset($buttons1['name']['search']))echo "<div id='name1'>".$buttons1['name']['search']."</div>";else;if($buttons2!='-1'&&isset($buttons2['name']['search']))echo "<div id='name2'>".$buttons2['name']['search']."</div>";else; ?></button>
			<input type='button' style='float:left;width:calc(50% / 3 - 2px);height:60px;margin:0 1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: transparent;float:left;' id='cremem' value='<?php if($buttons1!='-1'&&isset($buttons1['name']['create']))echo $buttons1['name']['create'];else echo '新增'; ?>'>
			<input type='button' style='float:left;width:calc(50% / 3 - 2px);height:60px;margin:0 1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: transparent;float:left;' id='reloadmem' value='<?php if($buttons1!='-1'&&isset($buttons1['name']['refresh']))echo $buttons1['name']['refresh'];else echo '更新會員'; ?>'>
			<div id='inputbox' style='width:calc(40% - 1px);height:calc(100% - 62px);margin-top:2px;margin-right:1px;padding:.2em;border:1px solid #898989;-moz-box-sizing: border-box;box-sizing: border-box;overflow: hidden;float:left;'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='7'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='8'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='9'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='4'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='5'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='6'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='1'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='2'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='3'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='0'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='-'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;float:left;font-size: 2.5em;' id='button' value='#'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: #c6d3e3;float:left;font-size: 2.5em;' id='button' value='' disabled>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: #c6d3e3;float:left;font-size: 2.5em;' id='button' value='<?php if($buttons1!='-1'&&isset($buttons1['name']['bs']))echo $buttons1['name']['bs'];else echo '倒退'; ?>'>
				<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% / 5 - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: #c6d3e3;float:left;font-size: 2.5em;' id='button' value='<?php if($buttons1!='-1'&&isset($buttons1['name']['reset']))echo $buttons1['name']['reset'];else echo '重填'; ?>'>
			</div>
			<div id='memslist' style='width:calc(60% - 1px);height:calc(100% - 62px);margin-top:2px;margin-left:1px;padding:.2em;border:1px solid #898989;-moz-box-sizing: border-box;box-sizing: border-box;overflow: hidden;float:left;'>
				<div id='listbox' style='width:100%;height:calc(100% / 4 * 3 - 1px);margin-bottom:1px;'>
					<input type='hidden' name='allitems' value=''>
					<input type='hidden' name='page' value=''>
					<div id='allresult' style='display:none;'></div>
					<label><?php if($interface1!='-1'&&isset($interface1['name']['memrestitle']))echo $interface1['name']['memrestitle'];else echo '會員查詢結果'; ?>:</label>
					<div id='lists' style='width:100%;height:calc(100% - 26px);border:1px solid #898989;background-color:#ffffff;-moz-box-sizing: border-box;box-sizing: border-box;overflow: auto;'>
					</div>
				</div>
				<div id='fun' style='width:100%;height:calc(100% / 4 - 1px);margin-top:1px;float:left;'>
					<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: transparent;float:left;' id='change' value='<?php if($buttons1!='-1'&&isset($buttons1['name']['changepage']))echo $buttons1['name']['changepage'];else echo '換頁'; ?>' disabled>
					<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: transparent;float:left;' id='check' value='<?php if($buttons1!='-1'&&isset($buttons1['name']['check']))echo $buttons1['name']['check'];else echo '確認'; ?>' disabled>
					<input type='button' style='width:calc(100% / 3 - 2px);height:calc(100% - 2px);margin:1px;padding:0;border-radius: 5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 1px solid #898989;background-color: transparent;float:left;' id='return' value='<?php if($buttons1!='-1'&&isset($buttons1['name']['exit']))echo $buttons1['name']['exit'];else echo '離開'; ?>'>
				</div>
			</div>
		</div>
		<?php
		if(!isset($initsetting['init']['quickcremember'])||$initsetting['init']['quickcremember']=='0'){
		?>
		<div class='cremem' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' title='<?php if($interface1!='-1'&&isset($interface1['name']['crememtitle']))echo $interface1['name']['crememtitle'];else echo '新增會員'; ?>'>
			<table style='width:100%;height:calc(100% - 61px);margin-bottom:1px;'>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['crenamelabel']))echo $interface1['name']['crenamelabel'];else echo '姓名'; ?></td>
					<td><input type='text' name='name' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['crephonelabel']))echo $interface1['name']['crephonelabel'];else echo '電話'; ?></td>
					<td><input type='text' name='tel' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;' autofocus></td>
				</tr>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['creaddresslabel']))echo $interface1['name']['creaddresslabel'];else echo '地址'; ?></td>
					<td><input type='text' name='address' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['recommend']))echo $interface1['name']['recommend'];else echo '推薦人編碼'; ?></td>
					<td><input type='text' name='recommend' style='width:80%;height:40px;line-height:40px;background-color:#ffffff;'><button id='fixrecommend' style='width:20%;height:40px;vertical-align:top;'><div>店家</div></button></td>
				</tr>
				<?php
				if($machinedata['memtitle']['open']='1'){
				?>
				<tr>
					<td><?php echo $machinedata['memtitle']['titlename']; ?></td>
					<td><input type='text' name='setting' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<?php
				}
				else{
				}
				?>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['creremarklabel']))echo $interface1['name']['creremarklabel'];else echo '備註'; ?></td>
					<td><textarea name='remark' rows='3' style='width:100%;line-height:40px;background-color:#ffffff;resize:none;'></textarea></td>
				</tr>
			</table>
			<!-- <button id='reflash' style='width:calc(100% / 3 - 1px);height:60px;margin:1px 1px 0 0;float:left;'><div style='width:100%;float:left;'>更新會員</div></button> -->
			<button id='create' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 1px 0 0;float:left;'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['cremem']))echo $buttons1['name']['cremem'];else echo '新增會員'; ?></div></button>
			<button id='close' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 0 0 1px;float:left;'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['return']))echo $buttons1['name']['return'];else echo '返回'; ?></div></button>
		</div>
		<?php
		}
		else{
		}
		?>
		<div class='memdata' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' title='會員資料'>
			<input type='hidden' id='recommend'>
			<table style='width:100%;height:calc(100% / 5 * 4 - 1px);margin-bottom:1px;'>
				<tr>
					<td style='width:110px;'>名稱</td>
					<td id='name'></td>
					<td id='recommendbox'>
						<button class='recommend' style='width:100%;height:100%;float:left;'><div style='width:100%;float:left;'><?php if($interface1!='-1'&&isset($interface1['name']['recommend']))echo $interface1['name']['recommend'];else echo '推薦人編碼'; ?></div></button>
					</td>
				</tr>
				<tr>
					<td>電話1</td>
					<td id='tel'></td>
					<td>
						<?php
							if(isset($initsetting['yunlincoins']['open'])&&$initsetting['yunlincoins']['open']=='1'){//2022/9/26 雲林幣轉換doubleplus儲值金(預計兩者比例為10:1)
						?>
								<button id='yunlincoins' style='width:100%;height:100%;,float:left;'><div style='width:100%;float:left;'>檢查雲林幣</div></button>
						<?php
							}
							else{
							}
						?>
					</td>
				</tr>
				<?php
				if($initsetting['init']['useinv']=='1'||$initsetting['init']['useoinv']=='1'){
				?>
				<tr>
					<td>統一編號</td>
					<td id='companynumber' colspan='2'></td>
				</tr>
				<?php
				}
				else{
				}
				?>
				<?php
				if($machinedata!='-1'&&isset($machinedata['memtitle']['titlename'])){
				?>
				<tr>
					<td><?php echo $machinedata['memtitle']['titlename']; ?></td>
					<td id='setting' colspan='2'></td>
				</tr>
				<?php
				}
				else{
				}
				?>
				<tr>
					<td>地址</td>
					<td id='address' colspan='2'></td>
				</tr>
				<tr>
					<td>備註</td>
					<td id='memremark' colspan='2'></td>
				</tr>
				<!-- <tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;光學度數</td>
					<td>閃光度數</td>
					<td>閃光軸度</td>
				</tr>
				<tr>
					<td>左 +2.50</td>
					<td>-0.75</td>
					<td>x180</td>
				</tr>
				<tr>
					<td>右 +2.25</td>
					<td>-1.00</td>
					<td>170</td>
				</tr> -->
				<tr>
					<td style='width:calc(100% / 3);'>
					<?php
					if(isset($otherpay)&&sizeof($otherpay)>1){
						foreach($otherpay as $ov){
							if(isset($ov['fromdb'])&&$ov['fromdb']=='member'&&$ov['dbname']=='point'){
								echo $ov['name'];
								$ovt=1;
								break;
							}
							else{
								$ovt=0;
							}
						}
						if(isset($ovt)&&$ovt==1){
						}
						else{
							echo '會員點數';
						}
					}
					else{
						echo '會員點數';
					}
					?>
					</td>
					<td  style='width:calc(100% / 3);' id='point'></td>
					<td style='width:calc(100% / 3);'>
						<button id='membermoneylist' style='width:100%;height:100%;float:left;' <?php if(!isset($initsetting['init']['posmembermoneylist'])||$initsetting['init']['posmembermoneylist']=='0')echo 'disabled'; ?>><div style='width:100%;float:left;'>本月儲值紀錄</div></button>
					</td>
				</tr>
				<tr>
					<td style='width:calc(100% / 3);'>
					<?php
					if(isset($otherpay)&&sizeof($otherpay)>1){
						foreach($otherpay as $ov){
							if(isset($ov['fromdb'])&&$ov['fromdb']=='member'&&$ov['dbname']=='money'){
								echo $ov['name'];
								$ovt=1;
								break;
							}
							else{
								$ovt=0;
							}
						}
						if(isset($ovt)&&$ovt==1){
						}
						else{
							echo '會員儲值金';
						}
					}
					else{
						echo '會員儲值金';
					}
					?>
					</td>
					<td style='width:calc(100% / 3);' id='money'></td>
					<td style='width:calc(100% / 3);'>
						<button id='getmembermoney' style='width:100%;height:100%;float:left;' <?php if(!isset($initsetting['init']['posgetmembermoney'])||$initsetting['init']['posgetmembermoney']=='0')echo 'disabled'; ?>><div style='width:100%;float:left;'>儲值</div></button>
					</td>
				</tr>
			</table>
			<button id='change' style='width:calc(200% / 9 - 1px);height:calc(100% / 5 - 1px);margin:1px 1px 0 0;float:left;'><div style='width:100%;float:left;'>切換會員</div></button>
			<button id='clear' style='width:calc(200% / 9 - 2px);height:calc(100% / 5 - 1px);margin:1px 1px 0 1px;float:left;'><div style='width:100%;float:left;'>清除會員</div></button>
			<button id='editpaypw' style='width:calc(200% / 9 - 2px);height:calc(100% / 5 - 1px);margin:1px 1px 0 1px;float:left;<?php
			if(isset($initsetting['init']['onlinemember'])&&$initsetting['init']['onlinemember']=='1'&&isset($member)&&isset($member['init']['openpaypw'])&&$member['init']['openpaypw']=='1'){//2020/8/24 先實作輸入密碼結帳
				echo 'visibility: visible;';
			}
			else{
				echo 'visibility: hidden;';
			}
			?>'><div style='width:100%;float:left;'>修改交易密碼</div></button>
			<button id='close' style='width:calc(100% / 3 - 1px);height:calc(100% / 5 - 1px);margin:1px 0 0 1px;float:left;'><div style='width:100%;float:left;'>返回</div></button>
		</div>
		<div class='getpaypw' title='輸入交易密碼'>
			<div class='paymethoddata' style='display:none;'>
				<input type='hidden' name='class'>
				<input type='hidden' name='dbname'>
				<input type='hidden' name='should'>
				<input type='hidden' name='fromdb'>
				<input type='hidden' name='pay'>
				<input type='hidden' name='location'>
				<input type='hidden' name='name'>
				<input type='hidden' name='inv'>
				<input type='hidden' name='price'>
				<input type='hidden' name='type'>
			</div>
			<input type='password' style='width:calc(100% - 12px);height: 50px;font-size: 25px;padding: 0 5px;background-color: #ffffff;' name='paypw' placeholder='輸入交易密碼'>
			<button id='send' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 1px 0 0;float:left;'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['check']))echo $buttons1['name']['check'];else echo '儲存'; ?></div></button>
			<button id='cancel' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 0 0 1px;float:left;'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['cancel']))echo $buttons1['name']['cancel'];else echo '返回'; ?></div></button>
		</div>
		<div class='editpaypw' title="修改交易密碼">
			<table style='width:100%;height:calc(100% - 123px);margin-bottom:1px;'>
				<tr id='initpw'>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['initpw']))echo $interface1['name']['initpw'];else echo '原始密碼'; ?></td>
					<td><input type='password' name='initpw' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['newpw1']))echo $interface1['name']['newpw1'];else echo '新密碼'; ?></td>
					<td><input type='password' name='newpw1' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['newpw2']))echo $interface1['name']['newpw2'];else echo '再次確認'; ?></td>
					<td><input type='password' name='newpw2' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
			</table>
			<button id='save' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 1px 0 0;float:left;'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['save']))echo $buttons1['name']['save'];else echo '儲存'; ?></div></button>
			<button id='close' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 0 0 1px;float:left;'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['return']))echo $buttons1['name']['return'];else echo '返回'; ?></div></button>
		</div>
		<!-- <div class='editmem' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' title='<?php if($interface1!='-1'&&isset($interface1['name']['editmemtitle']))echo $interface1['name']['editmemtitle'];else echo '修改資料'; ?>'>
			<table style='width:100%;height:calc(100% - 123px);margin-bottom:1px;'>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['crenamelabel']))echo $interface1['name']['crenamelabel'];else echo '姓名'; ?></td>
					<td><input type='text' name='name' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['crephonelabel']))echo $interface1['name']['crephonelabel'];else echo '電話'; ?></td>
					<td><input type='text' name='tel' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<tr>
					<td><?php if($interface1!='-1'&&isset($interface1['name']['creaddresslabel']))echo $interface1['name']['creaddresslabel'];else echo '地址'; ?></td>
					<td><input type='text' name='address' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<?php
				if($machinedata['memtitle']['open']='1'){
				?>
				<tr>
					<td><?php echo $machinedata['memtitle']['titlename']; ?></td>
					<td><input type='text' name='setting' style='width:100%;height:40px;line-height:40px;background-color:#ffffff;'></td>
				</tr>
				<?php
				}
				else{
				}
				?>
			</table>
			<button id='editpaypw' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 1px 1px 0;float:left;<?php
			if(isset($member)&&isset($member['init']['openpaypw'])&&$member['init']['openpaypw']=='1'){
				echo 'visibility: visible;';
			}
			else{
				echo 'visibility: hidden;';
			}
			?>'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['editpaypw']))echo $buttons1['name']['editpaypw'];else echo '修改交易密碼'; ?></div></button>
			<button style='width:calc(100% / 2 - 1px);height:60px;margin:1px 0 1px 1px;float:left;visibility: hidden;'><div style='width:100%;float:left;'></div></button>
			<button id='create' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 1px 0 0;float:left;'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['save']))echo $buttons1['name']['save'];else echo '儲存'; ?></div></button>
			<button id='close' style='width:calc(100% / 2 - 1px);height:60px;margin:1px 0 0 1px;float:left;'><div style='width:100%;float:left;'><?php if($buttons1!='-1'&&isset($buttons1['name']['return']))echo $buttons1['name']['return'];else echo '返回'; ?></div></button>
		</div> -->
		<div class='paymembermoney' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' title='會員儲值'>
			<div style='width:50%;height:calc(100% / 5 * 4 - 1px);margin-bottom:1px;float:left;'>
				<div style='width:100%;height:calc(100% / 8 - 2px);margin:1px;float:left;text-align:center;'>儲值前</div>
				<div style='width:100%;height:calc(100% / 8 - 2px);margin:1px;float:left;'><input type='text' class='remainingmoney' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;' readonly></div>
				<div style='width:100%;height:calc(100% / 8 - 2px);margin:1px;float:left;text-align:center;'>儲值金額</div>
				<div style='width:100%;height:calc(100% / 8 - 2px);margin:1px;float:left;'><input type='text' class='paymoney' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;background-color:#ffffff;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>></div>
				<div style='width:100%;height:calc(100% / 8 - 2px);margin:1px;float:left;text-align:center;'>會員儲值金</div>
				<div style='width:100%;height:calc(100% / 8 - 2px);margin:1px;float:left;'><input type='text' class='getmemmoney' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;' value='0' readonly></div>
				<div style='width:100%;height:calc(100% / 8 - 2px);margin:1px;float:left;text-align:center;'>儲值後</div>
				<div style='width:100%;height:calc(100% / 8 - 2px);margin:1px;float:left;'><input type='text' class='precompute' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;' value='0' readonly></div>
				<div style='width:100%;height:calc(100% / 4 - 2px);margin:1px;float:left;text-align:center;'></div>
			</div>
			<div style='width:50%;height:calc(100% / 5 * 4 - 1px);margin-bottom:1px;float:left;'>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>7</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>8</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>9</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>4</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>5</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>6</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>1</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>2</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>3</button>
				<button class='num' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>0</button>
				<button id='reset' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'><?php if(isset($buttons1['numberpad']['ac']))echo $buttons1['numberpad']['ac'];else echo '重填' ?></button>
				<button id='back' style='width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;'>倒退</button>
			</div>
			<button id='send' style='width:calc(100% / 2 - 1px);height:calc(100% / 5 - 1px);margin:1px 1px 0 0;float:left;'><div style='width:100%;float:left;'>儲值</div></button>
			<button id='cancel' style='width:calc(100% / 2 - 1px);height:calc(100% / 5 - 1px);margin:1px 0 0 1px;float:left;'><div style='width:100%;float:left;'>取消</div></button>
		</div>
		<div class='membermoneylist' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' title='儲值紀錄'>
			<div id='view' style='width:calc(100% - 2px);height:calc(100% / 6 * 4 - 3px);border:1px solid #898989;border-radius:5px;background-color: #848284;margin-bottom:1px;float:left;overflow:auto;'>
				<div style='width:max-content;font-size:12px;background-color: #EFEBDE;overflow:hidden;border-top-right-radius:5px;border-top-left-radius:5px;'>
					<div style='width:87.97px;text-align:center;float:left;margin:0 5px;'>營業日</div>
					<div style='width:109.97px;text-align:center;float:left;margin:0 5px;'>儲值時間</div>
					<div style='width:70px;text-align:right;float:left;margin:0 5px;'>儲值前</div>
					<div style='width:70px;text-align:right;float:left;margin:0 5px;'>儲值後</div>
					<div style='width:70px;text-align:right;float:left;margin:0 5px;'>現金收入</div>
					<div style='width:70px;text-align:right;float:left;margin:0 5px;'>取得儲值金</div>
					<div style='width:50px;text-align:right;float:left;margin:0 5px;'>狀態</div>
				</div>
				<div id='moneylist' style='width:max-content;overflow:hidden;'>
				</div>
			</div>
			<div id='paper' style='width:100%;height:calc(100% / 6 - 2px);margin:1px 0;float:left;'>
				<div style='width:50%;height:100%;margin:0;float:left;'>
					<div style='width:100%;height:50%;text-align:center;line-height:41px;float:left;'>總儲值次數</div>
					<div id='totaltime' style='width:calc(100% - 3px);height:calc(50% - 4px);margin:1px 1px 1px 0;border:1px solid #898989;border-radius:5px;background-color:#ffffff;text-align:center;line-height:41px;float:left;'></div>
				</div>
				<div style='width:50%;height:100%;margin:0;float:left;'>
					<div style='width:100%;height:50%;text-align:center;line-height:41px;float:left;'>總儲值金額</div>
					<div id='totalmoney' style='width:calc(100% - 3px);height:calc(50% - 4px);margin:1px 0 1px 1px;border:1px solid #898989;border-radius:5px;background-color:#ffffff;text-align:center;line-height:41px;float:right;'></div>
				</div>
			</div>
			<button id='delete' style='width:calc(100% / 2 - 1px);height:calc(100% / 6 - 1px);margin:1px 1px 0 0;float:left;' disabled><div style='width:100%;float:left;'>作廢</div></button>
			<button id='cancel' style='width:calc(100% / 2 - 1px);height:calc(100% / 6 - 1px);margin:1px 0 0 1px;float:left;'><div style='width:100%;float:left;'>取消</div></button>
		</div>
		<div class='checkdeletemommoney' style='text-align:center;' title='<?php if($interface1!='-1')echo $interface1['name']['38'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['38'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['38'];else; ?>'>
			<div id="text"></div>
			<br>
			<button class="yes" value="確認" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['20']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['20']."</div>"; ?></button>
			<button class="no" value="取消" style='width:105px;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['21']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['21']."</div>"; ?></button>
		</div>
		<?php
		if($initsetting['init']['moneycost']==1){
		?>
		<div id='outmoney' title='其他收/支' style='overflow:hidden;'>
			<div style='width:50%;height:100%;float:left;'>
				<div id='salelist' style='width:calc(100% - 5px);height:calc(100% - 100px);border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow: auto;margin:0 2px 0 0;'>
					<div class='saletitle' style='background-color:#EFEBDE;width:max-content;padding-right:3px;overflow:hidden;'>
						<div style='width:90px;padding:0 5px;'>
							營業日
						</div>
						<div style='width:45px;padding:0 5px;'>
							班別
						</div>
						<div style='width:90px;padding:0 5px;'>
							金額
						</div>
						<div style='width:90px;padding:0 5px;'>
							科目
						</div>
						<div style='width:90px;padding:0 5px;'>
							項目名稱
						</div>
					</div>
					<div id='salecontent' style='background-color:#ffffff;width:max-content;max-height:calc(100% - 22px);overflow-x:hidden;overflow-y:auto;'>
					</div>
				</div>
				<div style='width:calc(50% - 4px);height:calc(100px - 4px);margin:1px;float:left;position: relative;border:1px solid #89898950;'>
					<span style="float:left;font-size: 30px; position: absolute; top: 0; left: 0;">支出：</span><div class='out' style="float:left;position: absolute; bottom: 15px; right: 10px; font-size: 40px;">0</div>
				</div>
				<div style='width:calc(50% - 4px);height:calc(100px - 4px);margin:1px;float:left;position: relative;border:1px solid #89898950;'>
					<span style="float:left;font-size: 30px; position: absolute; top: 0; left: 0;">收入：</span><div class='in' style="float:left;position: absolute; bottom: 15px; right: 10px; font-size: 40px;">0</div>
				</div>
			</div>
			<div style='width:50%;height:100%;float:left;'>
				<div style='width:100%;float:left;margin:1px 0;'>
					<label style='font-size:3vw;'><input type='radio' name='aetype' value='1' style='-ms-transform: scale(1.7);-webkit-transform: scale(1.7);transform: scale(1.7);' checked>支出費用</label>
					<label style='font-size:3vw;'><input type='radio' name='aetype' value='2' style='-ms-transform: scale(1.7);-webkit-transform: scale(1.7);transform: scale(1.7);'>收入費用</label>
				</div>
				<div style='width:100%;float:left;margin:1px 0;'>
					<label style='font-size:2vw;'>營業日期：</label><input type='text' id='picbizdate' style='float:right;font-size:2vw;width:calc(100% - 10vw);border:1px solid #898989;'>
				</div>
				<div style='width:100%;float:left;margin:1px 0;'>
					<label style='font-size:2vw;'>當日班別：</label><select class='needsclick' name='zcounter' style='float:right;font-size:3vw;width:calc(100% - 10vw);border:1px solid #898989;'><?php if(file_exists('../database/sale/SALES_'.substr($timeini['time']['bizdate'],0,6).'.db')){$conn=sqlconnect('../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');$sql='SELECT DISTINCT ZCOUNTER FROM CST011 WHERE BIZDATE="'.$timeini['time']['bizdate'].'"';$options=sqlquery($conn,$sql,'sqlite');sqlclose($conn,'sqlite');for($i=0;$i<sizeof($options);$i++)echo '<option value="'.$options[$i]['ZCOUNTER'].'">'.$options[$i]['ZCOUNTER'].'</option>';}else{} ?></select>
				</div>
				<div style='width:100%;float:left;margin:1px 0;'>
					<label style='font-size:2vw;text-align:right;'>科目：</label><input type='text' id='moneytype' style='float:right;font-size:3vw;width:calc(100% - 10vw);border:1px solid #898989;'>
					<input type='hidden' name='moneytype'>
				</div>
				<div style='width:100%;float:left;margin:1px 0;'>
					<label style='font-size:2vw;'>項目名稱：</label><input type='text' name='moneysubtype' style='float:right;font-size:3vw;width:calc(100% - 10vw);border:1px solid #898989;'>
				</div>
				<div style='width:100%;float:left;margin:1px 0;'>
					<label style='font-size:2vw;'>金額：</label><input type='text' name='money' style='float:right;font-size:3vw;width:calc(100% - 10vw);text-align:right;border:1px solid #898989;' value='0' readonly>
				</div>
				<div style='width:100%;float:left;margin:1px 0;'>
					<label style='font-size:2vw;'>憑證：</label><label style='font-size:2vw;'><input type='radio' name='radius' value='1' style='-ms-transform: scale(1.7);-webkit-transform: scale(1.7);transform: scale(1.7);'>有</label>、<label style='font-size:2vw;'><input type='radio' name='radius' value='0' style='-ms-transform: scale(1.7);-webkit-transform: scale(1.7);transform: scale(1.7);' checked>無</label>
				</div>
				<div style='width:100%;height:calc(100% - 54px - 36px - 49px - 50px - 50px - 50px - 36px - 90px);float:left;margin:1px 0 5px 0;'>
					<label style='font-size:2vw;'>備註</label>
					<textarea name='remarks' style='width:calc(100% - 6px);height:calc(100% - 34px);resize:none;font-size:2vw;border:1px solid #898989;'></textarea>
				</div>
				<button id='cancel' style='float:right;font-size:3vw;margin:1px;position: absolute; bottom: 0; right: 103px;'><div>取消</div></button>
				<button id='send' style='float:right;font-size:3vw;margin:1px;position: absolute; bottom: 0; right: 16px;'><div>送出</div></button>
			</div>
		</div>
		<div class='selecttype' title='科目' style='overflow:hidden;'>
			<!-- <div id='left' style='width:100px;height:100%;position: absolute;top:0;left:0;'>
				<img src='../database/img/left.png' style='width:70px;height:70px;margin:27.5px 15px;visibility:hidden;'>
			</div> -->
			<div style='width:100%;height:100%;overflow-x:auto;overflow-y:hidden;'>
				<div style='min-width:max-content;max-height:100%;overflow:hidden;'>
					<?php
					$type=parse_ini_file('../database/type.ini',true);
					for($i=0;$i<sizeof($type['type']);$i++){
						echo '<button id="buttype" style="width:98px;height:123px;margin:1px;float:left;background-color:transparent;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>'.$type['type'][$i].'</div><input type="hidden" id="typevalue" value="'.$i.'"></button>';
					}
					?>
				</div>
			</div>
			<!-- <div id='right' style='width:100px;height:100%;position: absolute;top:0;right:0;'>
				<img src='../database/img/right.png' style='width:70px;height:70px;margin:27.5px 15px;visibility:hidden;'>
			</div> -->
		</div>
		<?php
		}
		else{
		}
		?>
		<div class='setmoney' title='金額' style='overflow:hidden;'>
			<input type='text' style='width:100%;font-size:2em;text-align:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin-bottom:1px;border:1px solid #898989;' name='viewnumber' value='0' readonly>
			<div style='width:100%;height:calc(100% - 50px);margin-top:1px;'>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>7</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>8</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>9</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>4</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>5</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>6</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>1</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>2</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;'"><div>3</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>0</div></button>
				<button id='reset' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;background-color: #c6d3e3;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>AC</div></button>
				<button id='send' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 4px);margin:1px;float:left;background-color: #c6d3e3;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;font-size: 2.5em;"><div>確認</div></button>
			</div>
		</div>
		<div id='reason' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='刪除原因'>
			<?php
			if($reason==''){
			}
			else{
				for($r=1;$r<=sizeof($reason['reason']);$r++){
					if($r==10){
						echo "<td style=''><button value='other' style='width:calc(100% / 5 - 2px);height:calc(100% / 2 - 2px);margin:1px;float:left;overflow:hidden;'><div>".$reason['reason'][$r]."</div></button>";
					}
					/*else if($r==9){//2022/12/1 拆單(依品項為單位；僅限暫結後的帳單使用)
						echo "<td style=''><button value='splitlist' style='width:calc(100% / 5 - 2px);height:calc(100% / 2 - 2px);margin:1px;float:left;overflow:hidden;'><div>拆單</div></button>";
					}*/
					else{
						if($reason['reason'][$r]==''){
							echo "<td style=''><button value='' style='width:calc(100% / 5 - 2px);height:calc(100% / 2 - 2px);margin:1px;float:left;overflow:hidden;' disabled><div></div></button>";
						}
						else{
							echo "<td style=''><button value='".$reason['reason'][$r]."' style='width:calc(100% / 5 - 2px);height:calc(100% / 2 - 2px);margin:1px;float:left;overflow:hidden;'><div>".$reason['reason'][$r]."</div></button>";
						}
					}
				}
			}
			?>
		</div>
		<div id='reasoninput' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='刪除原因'>
			<input type='text' name='reason' style='width:calc(80% - 1px);height:100%;margin-left:1px;float:left;background-color:#ffffff;'><input type='button' id='send' value='確定' style='width:calc(10% - 2px);height:100%;margin:0 1px;float:left;'><input type='button' id='cancel' value='取消' style='width:calc(10% - 1px);height:100%;margin-right:1px;float:left;'>
		</div>
		<div class='paywindow' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['otherpay'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['otherpay'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['otherpay'];else; ?>'>
			<input type='hidden' name='target' value=''>
			<?php
			for($i=1;$i<sizeof($otherpay);$i++){
				echo "<button ";
				if(!isset($otherpay['item'.$i]['location'])){
					echo "class='CST011'";
				}
				else{
					echo "class='".$otherpay['item'.$i]['location']."'";
				}
				echo " id='".$otherpay['item'.$i]['dbname']."' style='width:calc(50% - 2px);height:calc(20% - 2px);margin:1px;float:left;'>".$otherpay['item'.$i]['name'];
				if(isset($otherpay['item'.$i]['directlinepay'])&&$otherpay['item'.$i]['directlinepay']=='1'){//line pay直接串接
					echo "<input type='hidden' name='directlinepay' value='1'>";
				}
				else{
					echo "<input type='hidden' name='directlinepay' value='0'>";
				}
				if(isset($otherpay['item'.$i]['jkos'])&&$otherpay['item'.$i]['jkos']){//2022/10/6 街口支付
					echo "<input type='hidden' name='jkos' value='1'>";
				}
				else{
					echo "<input type='hidden' name='jkos' value='0'>";
				}
				if(isset($otherpay['item'.$i]['pxpayplus'])&&$otherpay['item'.$i]['pxpayplus']){//2022/10/28 全支付
					echo "<input type='hidden' name='pxpayplus' value='1'>";
				}
				else{
					echo "<input type='hidden' name='pxpayplus' value='0'>";
				}
				if(isset($otherpay['item'.$i]['fromdb'])){
					echo "<input type='hidden' name='fromdb' value='".$otherpay['item'.$i]['fromdb']."'>";
				}
				else{
				}
				if(isset($otherpay['item'.$i]['should'])){
					echo "<input type='hidden' name='should' value='".$otherpay['item'.$i]['should']."'>";
				}
				else{
				}
				if(isset($otherpay['item'.$i]['pay'])){
					echo "<input type='hidden' name='pay' value='".$otherpay['item'.$i]['pay']."'>";
				}
				else{
				}
				if(!isset($otherpay['item'.$i]['location'])){
					echo "<input type='hidden' name='location' value='CST011'>";
				}
				else{
					echo "<input type='hidden' name='location' value='".$otherpay['item'.$i]['location']."'>";
				}
				echo "<input type='hidden' name='name' value='".$otherpay['item'.$i]['name']."'>";
				if(!isset($otherpay['item'.$i]['inv'])){
					echo "<input type='hidden' name='inv' value='1'>";
				}
				else{
					echo "<input type='hidden' name='inv' value='".$otherpay['item'.$i]['inv']."'>";
				}
				if(!isset($otherpay['item'.$i]['price'])||$otherpay['item'.$i]['price']=='0'){
					echo "<input type='hidden' name='price' value='1'>";
				}
				else{
					echo "<input type='hidden' name='price' value='".$otherpay['item'.$i]['price']."'>";
				}
				if(!isset($otherpay['item'.$i]['type'])){
					echo "<input type='hidden' name='type' value='1'>";
				}
				else{
					echo "<input type='hidden' name='type' value='".$otherpay['item'.$i]['type']."'>";
				}
				echo "</button>";
			}
			?>
		</div>
		<?php
		if((isset($initsetting['init']['callkeybord'])&&$initsetting['init']['callkeybord']==1)||(isset($initsetting['init']['cashbut'])&&$initsetting['init']['cashbut']==1&&(isset($initsetting['init']['creditcode'])&&$initsetting['init']['creditcode']==1))){
		?>
		<div class='keybord' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['54'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['54'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['54'];else; ?>'>
			<input type='hidden' name='type' value='bord'>
			<input type='text' name='num' max='999' style='width:100%;height:50px;font-size:25px;margin-bottom:1px;padding:0 5px;background-color:#ffffff;text-align:right;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>>
			<button id='clear' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>清空</button>
			<button id='back' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>倒退</button>
			<button style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;background-color:#c6d3e3; color:#000000;'></button>
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
			<button class='needsclick' id='send' style='width:calc(200% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>送出</button>
			<button id='cancel' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>取消</button>
		</div>
		<?php
		}
		else{
		}
		?>
		<?php
		if(isset($initsetting['init']['openpunch'])&&$initsetting['init']['openpunch']==1){
		?>
		<div class='punch' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['punch'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['punch'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['punch'];else; ?>'>
			<input type='text' name='num' style='width:100%;height:50px;font-size:25px;margin-bottom:1px;padding:0 5px;background-color:#ffffff;text-align:right;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>>
			<button id='number' value='7' style='font-size: 2.5em;'>7</button>
			<button id='number' value='8' style='font-size: 2.5em;'>8</button>
			<button id='number' value='9' style='font-size: 2.5em;'>9</button>
			<button id='number' value='4' style='font-size: 2.5em;'>4</button>
			<button id='number' value='5' style='font-size: 2.5em;'>5</button>
			<button id='number' value='6' style='font-size: 2.5em;'>6</button>
			<button id='number' value='1' style='font-size: 2.5em;'>1</button>
			<button id='number' value='2' style='font-size: 2.5em;'>2</button>
			<button id='number' value='3' style='font-size: 2.5em;'>3</button>
			<button id='number' value='0' style='font-size: 2.5em;'>0</button>
			<button id='number' value='' disabled></button>
			<button id='number' value='' disabled></button>
			<button id='on' style='width:calc(150% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);background-color:#FFA801;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'><?php if($personnel!=''&&$personnel['basic']['punchtype']=='1'){if($buttons1!='-1'&&isset($buttons1['name']['onlyon']))echo $buttons1['name']['onlyon'];else echo '報班';}else{if($buttons1!='-1'&&isset($buttons1['name']['onpunch']))echo $buttons1['name']['onpunch'];else echo '上班';} ?></button>
			<button id='off' style='width:calc(150% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);background-color:#D5DC75;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;' <?php if($personnel!=''&&$personnel['basic']['punchtype']=='1')echo 'disabled';else ; ?>><?php if($buttons1!='-1'&&isset($buttons1['name']['offpunch']))echo $buttons1['name']['offpunch'];else echo '下班'; ?></button>
			<button id='clear' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'><?php if($buttons1!='-1'&&isset($buttons1['name']['reset']))echo $buttons1['name']['reset'];else echo '清空'; ?></button>
			<button id='back' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'><?php if($buttons1!='-1'&&isset($buttons1['name']['bs']))echo $buttons1['name']['bs'];else echo '倒退'; ?></button>
			<button id='return' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 6 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'><?php if($buttons1!='-1'&&isset($buttons1['name']['return']))echo $buttons1['name']['return'];else echo '返回'; ?></button>
		</div>
		<?php
		}
		else{
		}
		?>
		<div class='editpay' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['editpay'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['editpay'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['editpay'];else; ?>'>
			<div style='width:calc(62.5% - 1px);height:100%;margin:0 1px 0 0;padding:0;float:left;'>
				<input type='text' name='view' style='width:100%;height:50px;font-size:25px;margin-bottom:1px;padding:0 5px;background-color:#ffffff;text-align:right;' readonly>
				<button id='number' value='7'>7</button>
				<button id='number' value='8'>8</button>
				<button id='number' value='9'>9</button>
				<button id='number' value='4'>4</button>
				<button id='number' value='5'>5</button>
				<button id='number' value='6'>6</button>
				<button id='number' value='1'>1</button>
				<button id='number' value='2'>2</button>
				<button id='number' value='3'>3</button>
				<button id='number' value='0'>0</button>
				<button id='number' value='.' <?php if(isset($initsetting['init']['accuracy'])&&intval($initsetting['init']['accuracy'])>=1);else echo 'disabled'; ?>><?php if(isset($initsetting['init']['accuracy'])&&intval($initsetting['init']['accuracy'])>='1')echo '.';else echo ''; ?></button>
				<button id='clear' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>清空</button>
				<?php
				if(isset($otherpay['pay']['openpay'])&&$otherpay['pay']['openpay']=='1'&&sizeof($otherpay)<=3&&sizeof($otherpay)>1){
					for($otindex=1;$otindex<sizeof($otherpay);$otindex++){
						echo "<button class='otherfunction' id='".$otherpay['item'.$otindex]['dbname']."' value='".$otherpay['item'.$otindex]['name']."' style='width:calc(100% / ".(sizeof($otherpay)+1)." - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;' ";
						if((!isset($otherpay['item'.$otindex]['inv'])||$otherpay['item'.$otindex]['inv']=='1')&&(!isset($otherpay['item'.$otindex]['fromdb'])||$otherpay['item'.$otindex]['fromdb']=='')&&(!isset($otherpay['item'.$otindex]['directlinepay'])||$otherpay['item'.$otindex]['directlinepay']=='0')&&(!isset($otherpay['item'.$otindex]['jkos'])||$otherpay['item'.$otindex]['jkos']=='0')&&(!isset($otherpay['item'.$otindex]['pxpayplus'])||$otherpay['item'.$otindex]['pxpayplus']=='0')){
						}
						else{
							echo 'disabled';
						}
						echo ">";
						if(isset($otherpay['item'.$otindex]['name'])&&$otherpay['item'.$otindex]['name']!='')echo "<div id='name1'>".$otherpay['item'.$otindex]['name']."</div>";
						if(isset($otherpay['item'.$otindex]['name2'])&&$otherpay['item'.$otindex]['name2']!='')echo "<div id='name2'>".$otherpay['item'.$otindex]['name2']."</div>";
						if(isset($otherpay['item'.$otindex]['fromdb'])){
							echo "<input type='hidden' name='fromdb' value='".$otherpay['item'.$otindex]['fromdb']."'>";
						}
						else{
						}
						if(isset($otherpay['item'.$otindex]['should'])){
							echo "<input type='hidden' name='should' value='".$otherpay['item'.$otindex]['should']."'>";
						}
						else{
						}
						if(isset($otherpay['item'.$otindex]['pay'])){
							echo "<input type='hidden' name='pay' value='".$otherpay['item'.$otindex]['pay']."'>";
						}
						else{
						}
						if(!isset($otherpay['item'.$otindex]['location'])){
							echo "<input type='hidden' name='location' value='CST011'>";
						}
						else{
							echo "<input type='hidden' name='location' value='".$otherpay['item'.$otindex]['location']."'>";
						}
						echo "<input type='hidden' name='name' value='".$otherpay['item'.$otindex]['name']."'>";
						if(!isset($otherpay['item'.$otindex]['inv'])){
							echo "<input type='hidden' name='inv' value='1'>";
						}
						else{
							echo "<input type='hidden' name='inv' value='".$otherpay['item'.$otindex]['inv']."'>";
						}
						if(!isset($otherpay['item'.$otindex]['price'])||floatval($otherpay['item'.$otindex]['price'])<0){
							echo "<input type='hidden' name='price' value='1'>";
						}
						else{
							echo "<input type='hidden' name='price' value='".$otherpay['item'.$otindex]['price']."'>";
						}
						if(!isset($otherpay['item'.$otindex]['type'])){
							echo "<input type='hidden' name='type' value='1'>";
						}
						else{
							echo "<input type='hidden' name='type' value='".$otherpay['item'.$otindex]['type']."'>";
						}
						echo "</button>";
						//echo "<button class='otherfunction' id='".$otherpay['item'.$otindex]['dbname']."' value='".$otherpay['item'.$otindex]['name']."' style='width:calc(100% / ".(sizeof($otherpay)+1)." - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>".$otherpay['item'.$otindex]['name']."</button>";
					}
				?>
					<button id='cashbut' style='width:calc(100% / <?php echo (sizeof($otherpay)+1); ?> - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;' <?php if(isset($initsetting['init']['nccc'])&&$initsetting['init']['nccc']=='1') echo 'disabled'; ?>>信用卡</button>
					<button id='moneybut' style='width:calc(100% / <?php echo (sizeof($otherpay)+1); ?> - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>現金</button>
				<?php
				}
				else if(isset($otherpay['pay']['openpay'])&&$otherpay['pay']['openpay']=='1'&&sizeof($otherpay)>3){
				?>
					<button id='other' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>其他付款</button>
					<button id='cashbut' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;' <?php if(isset($initsetting['init']['nccc'])&&$initsetting['init']['nccc']=='1') echo 'disabled'; ?>>信用卡</button>
					<button id='moneybut' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>現金</button>
				<?php
				}
				else{
				?>
					<button id='' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;' disabled></button>
					<button id='cashbut' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;' <?php if(isset($initsetting['init']['nccc'])&&$initsetting['init']['nccc']=='1') echo 'disabled'; ?>>信用卡</button>
					<button id='moneybut' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'>現金</button>
				<?php
				}
				?>
			</div>
			<div id="viewwindow" style='width:calc(37.5% - 1px);height:100%;margin:0 0 0 1px;padding:0;float:left;position: relative;'>
				<form id='editform'>
					<input type='hidden' name='machinetype' value='<?php if(isset($_GET['submachine'])&&$_GET['submachine']!='')echo $_GET['submachine'];else if(isset($_GET['machine'])&&$_GET['machine']!='')echo $_GET['machine'];else echo 'm1'; ?>'>
					<input type='hidden' name='bizdate' value=''>
					<input type='hidden' name='consecnumber' value=''>
					<input type='hidden' name='usercode' value=''>
					<input type='hidden' name='username' value=''>
					<table style='width:100%;margin-bottom:1px;'>
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1' style='font-size:20px;font-weight:bold;'>".$interface1['name']['34']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['34']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='should' style='font-size:20px;font-weight:bold;'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='should' value='0'></td><!-- 自動計算 -->
						</tr>
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1' style='font-size:20px;font-weight:bold;'>".$interface1['name']['cashcomm']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['cashcomm']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='cashcomm' style='font-size:20px;font-weight:bold;'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='cashcomm' value='0'></td><!-- 自動計算 -->
						</tr>
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['35']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['35']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='already'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='already' value='0'></td><!-- 自動計算 -->
						</tr>
						<tr>
							<td style='padding-left:15px;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['money']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['money']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='cashmoney'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='cashmoney' value='0'></td><!-- 自動計算 -->
						</tr>
						<tr>
							<td style='padding-left:15px;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['cash']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['cash']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='cash'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='cash' value='0'></td><!-- 自動計算 -->
						</tr>
						<tr>
							<td style='padding-left:15px;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['other']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['other']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='other'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='other' value='0'><input type='hidden' name='otherstring' value=''><input type='hidden' id='nontax' name='nontax' value=''></td><!-- 自動計算 -->
						</tr>
						<tr>
							<td style='padding-left:15px;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['otherfix']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['otherfix']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='otherfix'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='otherfix' value='0'></td><!-- 自動計算 -->
						</tr>
						<!-- <tr>
							<td style='padding-left:15px;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['other']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['other']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='other'>0</span><?php echo $initsetting['init']['unit']; ?><input type='hidden' name='other' value='0'></td>自動計算
						</tr> -->
						<tr>
							<td><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['36']."</div>"; ?><?php if($interface2!='-1')echo "<div id='name2'>".$interface2['name']['36']."</div>"; ?></td>
							<td style='text-align:right;'><?php echo $initsetting['init']['frontunit']; ?><span id='notyet'>0</span><?php echo $initsetting['init']['unit']; ?></td><!-- 自動計算 -->
						</tr>
					</table>
					<div id='editpaywindow' style='width:calc(100% - 1px);height:195px;padding:0;margin:1px 0 0 1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<div id='th' style='overflow:hidden;'>
							<div style='width:20px;height:19px;float:left;'></div>
							<div style='width:calc(50% - 10px);float:left;text-align:center;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['24']."</div>"; ?><?php if($interface1!='-1'&&$interface2!='-1')echo "<div id='name2'>(".$interface2['name']['24'].')</div>';else if($interface1=='-1'&&$interface2!='-1')echo "<div id='name2'>".$interface2['name']['24'].'</div>';else; ?></div>
							<div style='width:calc(50% - 10px);float:left;text-align:center;'><?php if($interface1!='-1')echo "<div id='name1'>".$interface1['name']['25']."</div>"; ?><?php if($interface1!='-1'&&$interface2!='-1')echo "<div id='name2'>(".$interface2['name']['25'].')</div>';else if($interface1=='-1'&&$interface2!='-1')echo "<div id='name2'>".$interface2['name']['25'].'</div>';else ?></div>
						</div>
						<div id='paycontent' style='height:calc(100% - 27px);overflow:auto;'>
						</div>
					</div>
				</form>
				<button id='send' style='position: absolute;bottom: 0;left:calc(10% / 2);' value='確認'>確認</button>
				<button id='cancel' style='position: absolute;bottom: 0;right:calc(10% / 2);' value='取消'><?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?></button>
			</div>
		</div>
		<div class='temptoinv' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['temptoinv'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['temptoinv'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['temptoinv'];else; ?>'>
		</div>
		<?php
		if((isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']==1)||isset($initsetting['init']['voidsale'])){
		?>
		<div class='verpsw' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['verpsw'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['verpsw'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['verpsw'];else; ?>'>
			<table style='width:100%;'>
				<tr>
					<td><input type='hidden' name='type'>密碼</td>
					<td><input type='password' name='verpsw' style='width:100%;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>></td>
				</tr>
			</table>
			<div id='buttons' style='width:100%;height:calc(100% - 31px);margin-top:2px;'>
				<input type='button' class='input needsclick' value='7'>
				<input type='button' class='input needsclick' value='8'>
				<input type='button' class='input needsclick' value='9'>
				<input type='button' id='pre' class='pre' style='background-color: #c6d3e3;' value='Pre' disabled>
				<input type='button' class='input needsclick' value='4'>
				<input type='button' class='input needsclick' value='5'>
				<input type='button' class='input needsclick' value='6'>
				<input type='button' id='next' class='next' style='background-color: #c6d3e3;' value='Next' disabled>
				<input type='button' class='input needsclick' value='1'>
				<input type='button' class='input needsclick' value='2'>
				<input type='button' class='input needsclick' value='3'>
				<input type='button' id='ac' style='background-color: #c6d3e3;' value='<?php if(isset($buttons1['numberpad']['ac']))echo $buttons1['numberpad']['ac'];else echo '重填' ?>'>
				<input type='button' class='input needsclick' value='0'>
				<input type='button' class='input needsclick' value='' disabled>
				<input type='button' class='input needsclick' value='' disabled>
				<input type='button' id='change' style='background-color: #c6d3e3;' value='<?php if(isset($buttons1['numberpad']['keyboard']))echo $buttons1['numberpad']['keyboard'];else echo '英/數' ?>'>
				<button id='send' style='background-color: #c6d3e3;' value='確定'><?php if(isset($buttons1['numberpad']['submit']))echo $buttons1['numberpad']['submit'];else echo '確定' ?></button>
				<button id='cancel' style='background-color: #c6d3e3;' value='取消'><?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?></button>
			</div>
		</div>
		<?php
		}
		else{
		}
		?>
		<?php
		if((!isset($initsetting['init']['useoinv'])||$initsetting['init']['useoinv']=='0')&&isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1'){
		?>
		<div class='container' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['container'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['container'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['container'];else; ?>'>
			<table>
				<tr>
					<td colspan='3'><?php if($interface1!='-1')echo $interface1['name']['container'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['container'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['container'];else; ?></td>
				</tr>
				<tr>
					<td>/<input type='text' name='containercode'></td>
					<td><button id='check' style='width:80px;' value='送出'>送出</button></td>
					<td><button id='cancel' style='width:80px;' value='<?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?>'><?php if(isset($buttons1['numberpad']['cancel']))echo $buttons1['numberpad']['cancel'];else echo '取消' ?></button></td>
				</tr>
			</table>
		</div>
		<div class='container2' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['container2'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['container2'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['container2'];else; ?>'>
			<table>
				<tr>
					<td colspan='3'><?php if($interface1!='-1')echo $interface1['name']['container2'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['container2'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['container2'];else; ?></td>
				</tr>
				<tr>
					<td><input type='text' name='containercode'></td>
					<td><button id='check' style='width:80px;' value='送出'>送出</button></td>
					<td><button id='cancel' style='width:80px;' value='取消'>取消</button></td>
				</tr>
			</table>
		</div>
		<div class='donatewin' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['donate'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['donate'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['donate'];else; ?>'>
			<table>
				<tr>
					<td colspan='2'><?php if($interface1!='-1')echo $interface1['name']['donate'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['donate'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['donate'];else; ?></td>
				</tr>
				<tr>
					<td><input type='text' name='containercode'></td>
					<td><input type='text' name='label' style='background-color:#000000;color:#ffffff;' readonly></td>
				</tr>
				<tr>
					<td colspan='2' style='text-align:right;'><button id='check' style='width:80px;' value='送出'>送出</button><button id='cancel' style='width:80px;' value='取消'>取消</button></td>
				</tr>
			</table>
		</div>
		<?php
		}
		else{
		}
		?>
		<?php
		if(isset($initsetting['init']['historypaper'])&&$initsetting['init']['historypaper']=='1'){
		?>
		<div class='historypaper' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['historypaper'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['historypaper'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['historypaper'];else; ?>'>
			<div style='width:100%;float:left;margin:1px 0;'>
				<div style='font-size:2vw;'><?php if(isset($interface1['name']['hisbizdate']))echo $interface1['name']['hisbizdate'];else echo "營業日期"; ?>：</div><input type='text' id='papbizdateS' style='font-size:2vw;width:calc((100% - 23px) / 2);border:1px solid #898989;direction: rtl;' readonly><span>～</span><input type='text' id='papbizdateE' style='font-size:2vw;width:calc((100% - 23px) / 2);border:1px solid #898989;direction: rtl;' readonly>
			</div>
			<div style='width:100%;float:left;margin:1px 0;'>
				<div style='font-size:2vw;'><?php if(isset($interface1['name']['hiszcounter']))echo $interface1['name']['hiszcounter'];else echo "當日班別"; ?>：</div><select class='needsclick' name='zcounter' style='float:right;font-size:3vw;width:100%;border:1px solid #898989;'><?php if(file_exists('../database/sale/SALES_'.substr($timeini['time']['bizdate'],0,6).'.db')){$conn=sqlconnect('../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');$sql='SELECT DISTINCT ZCOUNTER FROM CST011 WHERE BIZDATE="'.$timeini['time']['bizdate'].'"';$options=sqlquery($conn,$sql,'sqlite');sqlclose($conn,'sqlite');echo '<option value="allday">';if(isset($buttons1['name']['allday']))echo $buttons1['name']['allday'];else echo '整個營業日';echo '</option>';for($i=0;$i<sizeof($options);$i++)echo '<option value="'.$options[$i]['ZCOUNTER'].'">'.$options[$i]['ZCOUNTER'].'</option>';}else{} ?></select>
			</div>
			<div style='width:100%;float:left;margin:1px 0 3px;'>
				<div style='font-size:2vw;'><?php if(isset($interface1['name']['hisselect']))echo $interface1['name']['hisselect'];else echo "選擇報表"; ?>：</div><select class='needsclick' name='paperlist' style='float:right;font-size:3vw;width:100%;border:1px solid #898989;'><?php if(isset($initsetting['init']['paper1'])&&$initsetting['init']['paper1']=='1'){echo "<option value='paper1'>";if(isset($buttons1['name']['hispaper1']))echo $buttons1['name']['hispaper1'];else echo "交班表.";echo "</option>";}if(isset($initsetting['init']['paper2'])&&$initsetting['init']['paper2']=='1'){echo "<option value='paper2'>";if(isset($buttons1['name']['hispaper1']))echo $buttons1['name']['hispaper2'];else echo "商品銷售彙總.";echo "</option>";}if(isset($initsetting['init']['paper3'])&&$initsetting['init']['paper3']=='1'){echo "<option value='paper3'>";if(isset($buttons1['name']['hispaper1']))echo $buttons1['name']['hispaper3'];else echo "時段金額彙總.";echo "</option>";}if(isset($initsetting['init']['paper4'])&&$initsetting['init']['paper4']=='1'){echo "<option value='paper4'>";if(isset($buttons1['name']['hispaper1']))echo $buttons1['name']['hispaper4'];else echo "時段人數彙總.";echo "</option>";} ?>
				<?php
				if(isset($initsetting['a1'])&&isset($initsetting['a1']['usea1erp'])&&$initsetting['a1']['usea1erp']=='1'){//2021/4/14 0>>關閉a1ERP串接1>>開啟a1ERP串接
					echo "<option value='paper5'>A1倉庫庫存表.</option>";
				}
				else{
				}
				?>
				</select>
			</div>
			<button id='view' style='float:left;font-size:3vw;margin:1px;'><div><?php if(isset($buttons1['name']['view']))echo $buttons1['name']['view'];else echo '瀏覽'; ?></div></button>
			<button id='cancel' style='float:right;font-size:3vw;margin:1px;'><div><?php if(isset($buttons1['name']['cancel']))echo $buttons1['name']['cancel'];else echo '取消'; ?></div></button>
			<button id='send' style='float:right;font-size:3vw;margin:1px;' autofocus><div><?php if(isset($buttons1['name']['hischeck']))echo $buttons1['name']['hischeck'];else echo '列印'; ?></div></button>
		</div>
		<div class='viewpaper' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['viewhistorypaper'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['viewhistorypaper'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['viewhistorypaper'];else; ?>'>
		</div>
		<?php
		}
		else{
		}
		?>
		<?php
		if(isset($initsetting['init']['kvm'])&&$initsetting['init']['kvm']=='1'){
		?>
		<div class='kvm' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['kvmtitle']))echo $interface1['name']['kvmtitle'];if($interface1!='-1'&&isset($interface1['name']['kvmtitle'])&&$interface2!='-1'&&isset($interface2['name']['kvmtitle']))echo ' /'.$interface2['name']['kvmtitle'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['kvmtitle']))echo $interface2['name']['kvmtitle'];else; ?>'>
			<div style='width:calc(50% - 1px);height:100%;margin-right:1px;float:left;'>
				<div id='salelist' style='width:100%;height:100%;border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					<div class='saletitle' style='background-color:#EFEBDE;width:calc(100% - 3px);padding-right:3px;overflow:hidden;'>
						<div style='width:25%;'>
							bizdate
						</div>
						<div style='width:25%;'>
							No
						</div>
						<div style='width:25%;'>
							createname
						</div>
						<div style='width:25%;'>
							createdatetime
						</div>
						<div style='width:5%;height:1px;'>
						</div>
					</div>
					<div id='salecontent' style='background-color:#ffffff;width:100%;max-height:calc(100% - 22px);overflow-x:hidden;overflow-y:auto;'>
					</div>
				</div>
			</div>
			<div style='width:calc(50% - 1px);height:23px;margin-left:1px;margin-bottom:1px;float:left;overflow:hidden;'>
				<div style='width:50%;height:100%;float:left;'>Date:<span id='date'></span><input type='hidden' id='credate'></div><div style='width:50%;height:100%;float:left;'>NO:<span id='listno'></span></div>
			</div>
			<div id='list' style='width:calc(50% - 1px);height:calc(80% - 25px);margin:1px 0 1px 1px;border:1px solid #898989;float:left;background-color: #848284;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:auto;'>
				<div class='listtitle' style='width:max-content;background-color:#EFEBDE;padding-right:3px;overflow:hidden;'>
					<div style='width:16px;min-height:1px;'>
					</div>
					<div style='width:55px;'>
						No
						<!-- <div id='name1'><?php echo $interface1['name']['7']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['7']))echo '('.$interface2['name']['7'].')';else; ?></div> -->
					</div>
					<div style='width:313px;'>
						Item
						<!-- <div id='name1'><?php echo $interface1['name']['8']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['8']))echo '('.$interface2['name']['8'].')';else; ?></div> -->
					</div>
					<div style='width:110px;text-align:center;'>
						QTY
						<!-- <div id='name1'><?php echo $interface1['name']['10']; ?></div>
						<div id='name2'><?php if(isset($interface2['name']['10']))echo '('.$interface2['name']['10'].')';else; ?></div> -->
					</div>
				</div>
				<div id='listcontent' style='background-color:#ffffff;width:max-content;font-size:17px;'>
				</div>
			</div>
			<div id='fun' style='width:calc(50% - 1px);height:calc(20% - 1px);margin-left:1px;margin-top:1px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
				<table style='width:100%;height:100%;'>
					<tr>
						<td style='width:calc(100% / 2);height:100%;'><button id='delete' value='已出餐' style='width:100%;height:100%;' disabled><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['alreadyout']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['alreadyout']."</div>";else; ?></button></td>
						<td style='width:calc(100% / 2);height:100%;'><button id='exit' value='離開' style='width:100%;height:100%;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['salevoid4']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['salevoid4']."</div>";else; ?></button></td>
					</tr>
				</table>
			</div>
		</div>
		<?php
		}
		else{
		}
		?>
		<div class='editpunch' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['editpunchtitle']))echo $interface1['name']['editpunchtitle'];if($interface1!='-1'&&isset($interface1['name']['editpunchtitle'])&&$interface2!='-1'&&isset($interface2['name']['editpunchtitle']))echo ' /'.$interface2['name']['editpunchtitle'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['editpunchtitle']))echo $interface2['name']['editpunchtitle'];else; ?>'>
			<div style='width:calc(50% - 1px);height:calc(100%);float:left;margin-right:1px;'>
				<div style='width:100%;height:calc(50% - 22px);float:left;margin-bottom:21px;border-bottom:1px groove #898989;'>
					<div style='width:100%;height:100%;'>
						<div style='width:100%;height:45px;line-height:45px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;font-size:40px;text-align:center;'>
							<?php
							if($interface1!='-1'&&isset($interface1['name']['editpunchsearchtitle'])){
								echo $interface1['name']['editpunchsearchtitle'];
							}
							else{
								echo '查詢打卡紀錄';
							}
							?>
						</div>
						<div style='width:100%;height:30px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;'>
							<?php
							if($interface1!='-1'&&isset($interface1['name']['editpunchsearchtime'])){
								echo $interface1['name']['editpunchsearchtime'];
							}
							else{
								echo "日期區間";
							}
							?>
						</div>
						<div style='width:100%;height:calc(100% / 6);-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;padding-left:80px;float:left;'>
							<input type='text' id='startpunch' style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;font-size:2vw;border:1px solid #898989;' readonly>
						</div>
						<div style='width:100%;height:calc(100% / 6);-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;padding-left:80px;float:left;'>
							<input type='text' id='endpunch' style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;font-size:2vw;border:1px solid #898989;' readonly>
						</div>
						<div style='width:100%;height:30px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;'>
							<?php
							if($interface1!='-1'&&isset($interface1['name']['editpunchsearchperson'])){
								echo $interface1['name']['editpunchsearchperson'];
							}
							else{
								echo "員工";
							}
							?>
						</div>
						<div style='width:100%;height:calc(100% / 6);-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;padding-left:80px;float:left;'>
							<select class='needsclick' name='punchname' style='width:60%;float:left;font-size:2vw;border:1px solid #898989;'>
								<option value='all' selected>全部員工</option>
								<?php
								//include_once '../tool/dbTool.inc.php';
								$conn=sqlconnect('../database/person','data.db','','','','sqlite');
								$sql='SELECT perno,percard,name,state FROM personnel ORDER BY perno ASC';
								$res=sqlquery($conn,$sql,'sqlite');
								sqlclose($conn,'sqlite');
								for($i=0;$i<sizeof($res);$i++){
									echo '<option value="'.$res[$i]['perno'].'">'.$res[$i]['percard'].$res[$i]['name'];
									if($res[$i]['state']=='0'){
										echo '<span style="color:#ff0000;">(停)</span>';
									}
									else{
									}
									echo '</option>';
								}
								?>
							</select>
							<button id='search' style='width:30%;height:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:right;border:1px solid #898989;background-color: #FCD7A3;' autofocus><div id='name1' style='font-size:2vw;'><?php if($buttons1!='-1'&&isset($buttons1['name']['punchsearch']))echo $buttons1['name']['punchsearch'];else echo '查詢'; ?></div></button>
						</div>
					</div>
				</div>
				<div style='width:100%;height:calc(50% - 1px);float:left;margin-top:1px;'>
					<div style='width:100%;height:45px;line-height:45px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;font-size:40px;text-align:center;'>
							<?php
							if($interface1!='-1'&&isset($interface1['name']['editpunchedittitle'])){
								echo $interface1['name']['editpunchedittitle'];
							}
							else{
								echo '修改打卡紀錄';
							}
							?>
						</div>
					<div style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;'>
						<?php
						if($interface1!='-1'&&isset($interface1['name']['editpuncheditperson'])){
							echo $interface1['name']['editpuncheditperson'];
						}
						else{
							echo "員工資訊";
						}
						?>
					</div>
					<div style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;padding-left:80px;'>
						<input type='text' id='editperson' style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;font-size:2vw;border:1px solid #898989;background-color:#b7b7b7;' readonly>
						<input type='hidden' id='editperno'>
					</div>
					<div style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;'>
						<?php
						if($interface1!='-1'&&isset($interface1['name']['editpunchbeforeedit'])){
							echo $interface1['name']['editpunchbeforeedit'];
						}
						else{
							echo "修改前";
						}
						?>
					</div>
					<div style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;padding-left:80px;'>
						<input type='hidden' id='initeditTYPE'>
						<input id='initeditDATE' style='width:40%;height:100%;margin-right:20px;float:left;font-size:2vw;background-color:#b7b7b7;border-radius: 5px;' value='' readonly>
						<input id='initeditH' style='width:calc(16% - 27px);height:100%;float:left;font-size:2vw;background-color:#b7b7b7;border-radius: 5px;' value='' readonly><div style='float:left;'>：</div><input id='initeditI' style='width:calc(16% - 27px);height:100%;float:left;font-size:2vw;background-color:#b7b7b7;border-radius: 5px;' value='' readonly>
						<input type='hidden' id='initeditTIME'>
					</div>
					<div style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;'>
						<?php
						if($interface1!='-1'&&isset($interface1['name']['editpunchafteredit'])){
							echo $interface1['name']['editpunchafteredit'];
						}
						else{
							echo "修改後";
						}
						?>
					</div>
					<div style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;padding-left:80px;'>
						<input type='text' id='editDATE' style='width:40%;height:100%;margin-right:20px;float:left;font-size:24px;' disabled>
						<select class='needsclick' id='editH' style='width:calc((75% / 4) - 27px);height:100%;float:left;font-size:24px;' disabled>
							<option id='empty' value=''></option>
							<?php
							for($i=0;$i<=23;$i++){
								echo '<option value="'.str_pad($i,2,'0',STR_PAD_LEFT).'">'.str_pad($i,2,'0',STR_PAD_LEFT).'</option>';
							}
							?>
						</select>
						<div style='float:left;'>：</div>
						<select class='needsclick' id='editI' style='width:calc((75% / 4) - 27px);height:100%;float:left;font-size:24px;' disabled>
							<option id='empty' value=''></option>
							<?php
							for($i=0;$i<=59;$i++){
								echo '<option value="'.str_pad($i,2,'0',STR_PAD_LEFT).'">'.str_pad($i,2,'0',STR_PAD_LEFT).'</option>';
							}
							?>
						</select>
					</div>
					<div style='width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;padding-left:80px;'>
						<button id='cancel' value='取消' style='width:calc(50% - 2px);height:60px;margin:10px 1px 0 1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;border:1px solid #898989;background-color: #FCD7A3;' disabled><div id='name1'><?php if($buttons1!='-1'&&isset($buttons1['name']['canceledit']))echo $buttons1['name']['canceledit'];else echo '取消修改'; ?></div></button>
						<button id='save' value='保存' style='width:calc(50% - 2px);height:60px;margin:10px 1px 0 1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;border:1px solid #898989;background-color: #FCD7A3;' disabled><div id='name1'><?php if($buttons1!='-1'&&isset($buttons1['name']['saveedit']))echo $buttons1['name']['saveedit'];else echo '保存修改'; ?></div></button>
					</div>
				</div>
			</div>
			<div style='width:calc(50% - 3px);height:calc(85% - 3px);float:left;border: 1px solid #898989;background-color: #848284;margin:0 0 1px 1px;'>
				<div></div>
				<div id='searchresult' style='width:100%;max-height:calc(100%);overflow-x: hidden;overflow-y: auto;'>
					
				</div>
			</div>
			<div style='width:calc(50% - 1px);height:calc(15% - 1px);float:left;margin:1px 0 0 1px;'>
				<button id='printpunch' style='width:calc(50% - 2px);height:calc(100% - 2px);margin:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;border:1px solid #898989;' disabled><div id='name1'><?php if($buttons1!='-1'&&isset($buttons1['name']['printpunch']))echo $buttons1['name']['printpunch'];else echo '列印打卡紀錄'; ?></div></button>
				<button id='exit' style='width:calc(50% - 2px);height:calc(100% - 2px);margin:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;border:1px solid #898989;'><div id='name1'><?php if($buttons1!='-1'&&isset($buttons1['name']['exitpunch']))echo $buttons1['name']['exitpunch'];else echo '離開'; ?></div></button>
			</div>
		</div>
		<?php
		if(isset($initsetting['init']['bolai'])&&$initsetting['init']['bolai']=='1'){
		?>
		<div class='memdeposit' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['memdeposit']))echo $interface1['name']['memdeposit'];if($interface1!='-1'&&isset($interface1['name']['memdeposit'])&&$interface2!='-1'&&isset($interface2['name']['memdeposit']))echo ' /'.$interface2['name']['memdeposit'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['memdeposit']))echo $interface2['name']['memdeposit'];else; ?>'>
			<div style='width:100%;height:80%;'>
				<div style='width:120px;height:20%;line-height:60.6px;float:left;text-align:center;font-size: 30px;'>型別</div>
				<div style='width:calc(100% - 120px);height:20%;padding:5px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;font-size:30px;line-height:60.6px;'><label><input type='radio' name='type' style='zoom: 1.6;' value='0' checked>會員卡</label><label><input type='radio' name='type' style='zoom: 1.6;' value='1'>QRcode</label></div>
				<div style='width:120px;height:20%;line-height:60.6px;float:left;text-align:center;font-size: 30px;'>會員卡號</div>
				<div style='width:calc(100% - 120px);height:20%;padding:5px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><input id='memcard' type='text' style='width:100%;height:100%;font-size:30px;padding:10px;background-color: #ffffff;'></div>
				<div style='width:120px;height:20%;line-height:60.6px;float:left;text-align:center;font-size: 30px;'>會員名稱</div>
				<div style='width:calc(100% - 120px);height:20%;padding:5px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><input id='memname' type='text' style='width:100%;height:100%;font-size:30px;padding:10px;background-color: #b7b7b7;' disabled></div>
				<div style='width:120px;height:20%;line-height:60.6px;float:left;text-align:center;font-size: 30px;'>剩餘金額</div>
				<div style='width:calc(100% - 120px);height:20%;padding:5px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><input id='remaining' type='text' style='width:100%;height:100%;font-size:30px;padding:10px;background-color: #b7b7b7;' disabled></div>
				<div style='width:120px;height:20%;line-height:60.6px;float:left;text-align:center;font-size: 30px;'>儲值金額</div>
				<div style='width:calc(100% - 120px);height:20%;padding:5px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'><input id='money' type='text' style='width:100%;height:100%;font-size:30px;padding:10px;background-color: #ffffff;'></div>
			</div>
			<div style='width:calc(100% - 120px);height:20%;padding-left:120px;'>
				<button id='send' style='width:calc(50% - 7px);height:calc(100% - 4px);margin:1px 1px 1px 5px;float:left;'><div id='name1'><?php if(isset($buttons1['name']['senddeposit']))echo $buttons1['name']['senddeposit'];else echo '儲值'; ?></div><?php if(isset($buttons2['name']['senddeposit']))echo '<div id="name2">'.$buttons2['name']['senddeposit'].'</div>'; ?></button>
				<button id='exit' style='width:calc(50% - 7px);height:calc(100% - 4px);margin:1px 5px 1px 1px;float:left;'><div id='name1'><?php if(isset($buttons1['name']['exitdeposit']))echo $buttons1['name']['exitdeposit'];else echo '離開'; ?></div><?php if(isset($buttons2['name']['exitdeposit']))echo '<div id="name2">'.$buttons2['name']['exitdeposit'].'</div>'; ?></button>
			</div>
		</div>
		<?php
		}
		else{
		}
		?>
		<?php
		if(isset($initsetting['init']['pointtree'])&&$initsetting['init']['pointtree']=='1'){
		?>
		<div class='pointtreememdata' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['pointtreememdata']))echo $interface1['name']['pointtreememdata'];if($interface1!='-1'&&isset($interface1['name']['pointtreememdata'])&&$interface2!='-1'&&isset($interface2['name']['pointtreememdata']))echo ' /'.$interface2['name']['pointtreememdata'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['pointtreememdata']))echo $interface2['name']['pointtreememdata'];else; ?>'>
			<div>
				<?php if($interface1!='-1'&&isset($interface1['name']['pointtreememtel']))echo $interface1['name']['pointtreememtel']; ?>：<span id='tel'></span>
			</div>
			<div>
				<?php if($interface1!='-1'&&isset($interface1['name']['pointtreememname']))echo $interface1['name']['pointtreememname']; ?>：<span id='name'></span>
			</div>
			<div>
				<?php if($interface1!='-1'&&isset($interface1['name']['pointtreememismem']))echo $interface1['name']['pointtreememismem']; ?>：<span id='ismem'></span>
			</div>
			<div>
				<?php if($interface1!='-1'&&isset($interface1['name']['pointtreememsex']))echo $interface1['name']['pointtreememsex']; ?>：<span id='sex'></span>
			</div>
			<div>
				<?php if($interface1!='-1'&&isset($interface1['name']['pointtreemembalance']))echo $interface1['name']['pointtreemembalance']; ?>：<span id='balance'></span>
			</div>
			<button id='exit' style='width:calc(50% - 7px);height:60px;margin:10px calc((50% + 7px) / 2) 1px;'><div id='name1'><?php if(isset($buttons1['name']['exitdeposit']))echo $buttons1['name']['exitdeposit'];else echo '離開'; ?></div><?php if(isset($buttons2['name']['exitdeposit']))echo '<div id="name2">'.$buttons2['name']['exitdeposit'].'</div>'; ?></button>
		</div>
		<?php
		}
		else{
		}
		?>
		<?php
		if(isset($initsetting['init']['intellapay'])&&$initsetting['init']['intellapay']=='1'){
		?>
		<div class='intellapaymethod' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['intellapay']))echo $interface1['name']['intellapay'];if($interface1!='-1'&&isset($interface1['name']['intellapay'])&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo ' /'.$interface2['name']['intellapay'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo $interface2['name']['intellapay'];else; ?>'>
			<?php
			if(isset($initsetting['init']['easycard'])&&$initsetting['init']['easycard']=='1'){
			?>
			<button class='needsclick' id='easycard' style='width:calc(50% - 2px);height:80px;margin:1px;float:left;'><div id='name1'><?php if(isset($buttons1['name']['easycard']))echo $buttons1['name']['easycard'];else echo '悠遊卡'; ?></div></button>
			<?php
			}
			else{
			}
			?>
			<?php
			if(isset($initsetting['init']['linepay'])&&$initsetting['init']['linepay']=='1'){
			?>
			<button class='needsclick' id='linepay' style='width:calc(50% - 2px);height:80px;margin:1px;float:left;'><div id='name1'><?php if(isset($buttons1['name']['linepay']))echo $buttons1['name']['linepay'];else echo 'LinePay'; ?></div></button>
			<?php
			}
			else{
			}
			?>
			<?php
			if(isset($initsetting['init']['creditcardpay'])&&$initsetting['init']['creditcardpay']=='1'){
			?>
			<button class='needsclick' id='creditcardpay' style='width:calc(50% - 2px);height:80px;margin:1px;float:left;'><div id='name1'><?php if(isset($buttons1['name']['creditcardpay']))echo $buttons1['name']['creditcardpay'];else echo '線上信用卡'; ?></div></button>
			<?php
			}
			else{
			}
			?>
			<?php
			if(isset($initsetting['init']['intellaother'])&&$initsetting['init']['intellaother']=='1'){
			?>
			<button class='needsclick' id='intellaother' style='width:calc(50% - 2px);height:80px;margin:1px;float:left;'><div id='name1'><?php if(isset($buttons1['name']['intellaother']))echo $buttons1['name']['intellaother'];else echo '消費者主掃'; ?></div></button>
			<?php
			}
			else{
			}
			?>
			<?php
			if(isset($initsetting['init']['intellauser'])&&$initsetting['init']['intellauser']=='1'){
			?>
			<button class='needsclick' id='intellauser' style='width:calc(50% - 2px);height:80px;margin:1px;float:left;'><div id='name1'><?php if(isset($buttons1['name']['intellauser']))echo $buttons1['name']['intellauser'];else echo '消費者被掃'; ?></div></button>
			<?php
			}
			else{
			}
			?>
			<button id='exit' style='width:calc(50% - 7px);height:60px;margin:10px calc((50% + 7px) / 2) 1px;'><div id='name1'><?php if(isset($buttons1['name']['exitdeposit']))echo $buttons1['name']['exitdeposit'];else echo '離開'; ?></div><?php if(isset($buttons2['name']['exitdeposit']))echo '<div id="name2">'.$buttons2['name']['exitdeposit'].'</div>'; ?></button>
		</div>
		<div class='checkintella' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['intellapay']))echo $interface1['name']['intellapay'];if($interface1!='-1'&&isset($interface1['name']['intellapay'])&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo ' /'.$interface2['name']['intellapay'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo $interface2['name']['intellapay'];else; ?>'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>QRcode逾時倒數...<span id='time'>19</span>秒</div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;'>取消交易</button></div>
		</div>
		<div class='checkintellauser' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['intellauser']))echo $interface1['name']['intellauser'];if($interface1!='-1'&&isset($interface1['name']['intellauser'])&&$interface2!='-1'&&isset($interface2['name']['intellauser']))echo ' /'.$interface2['name']['intellauser'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['intellauser']))echo $interface2['name']['intellauser'];else; ?>'>
			<div class='paras' style='display:none;'><input type='hidden' name='methodcode'><input type='hidden' name='money'></div>
			<div class='inputdiv'>AuthCode：<br><input type='text' name='authcode' style='width:calc(100% - 2px);margin-bottom:10px;padding:5px;' autofocus></div>
			<div class='loaddiv' style='display:none;'>檢查交易<span id='dot'>...</span></div>
			<div style='width: calc(100% - 40px); height: calc(100% / 3); position: absolute; bottom: 20px; left: 20px;'><button id='successsale' style='width:calc(100% / 3 - 2px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='retry' style='width:calc(100% / 3 - 2px);height:calc(100% - 2px);margin:1px;float:left;' disabled>重試交易</button><button id='cancelsale' style='width:calc(100% / 3 - 2px);height:calc(100% - 2px);margin:1px;float:left;'>取消交易</button></div>
		</div>
		<div class='checkeasycard' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['intellapay']))echo $interface1['name']['intellapay'];if($interface1!='-1'&&isset($interface1['name']['intellapay'])&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo ' /'.$interface2['name']['intellapay'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo $interface2['name']['intellapay'];else; ?>'>
			<input type='hidden' name='money'>
			<input type='hidden' name='methodcode'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'><span id='retry' style='display:none;'>自動重試<span id='time'></span> </span>偵測卡片<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消交易</button></div>
		</div>
		<div class='voidintella' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['intellapay']))echo $interface1['name']['intellapay'];if($interface1!='-1'&&isset($interface1['name']['intellapay'])&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo ' /'.$interface2['name']['intellapay'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo $interface2['name']['intellapay'];else; ?>'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'><span id='retry' style='display:none;'>自動重試<span id='time'></span> </span>偵測卡片<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消交易</button></div>
		</div>
		<div class='voidsalewithintellapay' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['intellapay']))echo $interface1['name']['intellapay'];if($interface1!='-1'&&isset($interface1['name']['intellapay'])&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo ' /'.$interface2['name']['intellapay'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['intellapay']))echo $interface2['name']['intellapay'];else; ?>'>
			<input type='hidden' name='type'>
			<input type='hidden' name='intellaconsecnumber'>
			<input type='hidden' name='paycode'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'><span id='retry' style='display:none;'>自動重試<span id='time'></span> </span>偵測卡片<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消交易</button></div>
		</div>
		<?php
		}
		else{
		}
		if((isset($initsetting['init']['intellapay'])&&$initsetting['init']['intellapay']=='1'&&isset($initsetting['init']['easycard'])&&$initsetting['init']['easycard']=='1')||(isset($initsetting['init']['nccc'])&&$initsetting['init']['nccc']=='1'&&isset($initsetting['init']['nccceasycard'])&&$initsetting['init']['nccceasycard']=='1')){
		?>
		<div class='intellabalancequery' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['easycard']))echo $interface1['name']['easycard'];if($interface1!='-1'&&isset($interface1['name']['easycard'])&&$interface2!='-1'&&isset($interface2['name']['easycard']))echo ' /'.$interface2['name']['easycard'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['easycard']))echo $interface2['name']['easycard'];else; ?>'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'><span id='retry' style='display:none;'>自動重試<span id='time'></span> </span>偵測卡片<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消交易</button></div>
		</div>
		<?php
		}
		else{
		}
		?>
		<div class='checknccc' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['nccc']))echo $interface1['name']['nccc'];if($interface1!='-1'&&isset($interface1['name']['nccc'])&&$interface2!='-1'&&isset($interface2['name']['nccc']))echo ' /'.$interface2['name']['nccc'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['nccc']))echo $interface2['name']['nccc'];else; ?>'>
			<input type='hidden' name='money'>
			<input type='hidden' name='commission'>
			<input type='hidden' name='methodcode'>
			<input type='hidden' name='date' value=''>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>等待交易<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='checksale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>檢查交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消交易</button></div>
		</div>
		<div class='voidnccc' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['nccc']))echo $interface1['name']['nccc'];if($interface1!='-1'&&isset($interface1['name']['nccc'])&&$interface2!='-1'&&isset($interface2['name']['nccc']))echo ' /'.$interface2['name']['nccc'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['nccc']))echo $interface2['name']['nccc'];else; ?>'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>退款處理中<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>退款成功</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消</button></div>
		</div>
		<div class='voidsalewithnccc' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['nccc']))echo $interface1['name']['nccc'];if($interface1!='-1'&&isset($interface1['name']['nccc'])&&$interface2!='-1'&&isset($interface2['name']['nccc']))echo ' /'.$interface2['name']['nccc'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['nccc']))echo $interface2['name']['nccc'];else; ?>'>
			<input type='hidden' name='type'>
			<input type='hidden' name='asm'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>退款處理中<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>退款成功</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消</button></div>
		</div>
		<div class='chosetype' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['nccc']))echo $interface1['name']['nccc'];if($interface1!='-1'&&isset($interface1['name']['nccc'])&&$interface2!='-1'&&isset($interface2['name']['nccc']))echo ' /'.$interface2['name']['nccc'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['nccc']))echo $interface2['name']['nccc'];else; ?>'>
			<button class='N' style='width:calc(50% - 5px);height:100%;float:left;margin-right:5px;'>信用卡</button>
			<button class='E' style='width:calc(50% - 5px);height:100%;float:left;'>悠遊卡</button>
		</div>
		<?php //2020/4/15 rfid讀取點餐，最先由三顧提出串接 ?>
		<div class='checkrfid' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if(isset($interface1['name']['readrfid']))echo $interface1['name']['readrfid'];if(isset($interface1['name']['readrfid'])&&isset($interface2['name']['readrfid']))echo ' /'.$interface2['name']['readrfid'];else if(isset($interface2['name']['readrfid']))echo $interface2['name']['readrfid'];else; ?>'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>讀取卡片中<span id='loading'>...</span></div>
		</div>
		<?php //2020/4/22 rfid讀取明細，最先由三顧提出串接 ?>
		<div class='rfidlist' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if(isset($interface1['name']['rfidlist']))echo $interface1['name']['rfidlist'];if(isset($interface1['name']['rfidlist'])&&isset($interface2['name']['rfidlist']))echo ' /'.$interface2['name']['rfidlist'];else if(isset($interface2['name']['rfidlist']))echo $interface2['name']['rfidlist'];else; ?>'>
			<div style='width:100%;height:44px;float:left;'>讀取張數：<input type='text' class='readnumber' style='width:60px;border:1px solid #898989;border-radius:5px;padding:10px;font-size:18px;text-align:right;' readonly><input type='hidden' class='listno'></div>
			<div class='list' style='width:100%;height:calc(100% - 44px - 50px - 12px);margin:3px 0;padding:3px 0;float:left;border:1px solid #898989;border-radius:5px;overflow:auto;'></div>
			<div style='width:100%;height:50px;float:left;'><button class='retry' style='width:calc((100% / 2) - 2px);height:calc(100% - 2px);margin:1px;float:left;'><div id='name1'><?php if(isset($initsetting['rfid']['open'])&&$initsetting['rfid']['open']=='1')echo '重試';else echo '停止讀取'; ?></div></button><button class='send' style='width:calc((100% / 2) - 2px);height:calc(100% - 2px);margin:1px;float:left;'><div id='name1'>送出</div></button></div>
		</div>
		<?php
		/*if(isset($initsetting['init']['itri'])&&$initsetting['init']['itri']=='1'){//2020/2/11目前實際看來專案無法RUN，暫時移除
		?>
		<div class='itritext' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['itri']))echo $interface1['name']['itri'];if($interface1!='-1'&&isset($interface1['name']['itri'])&&$interface2!='-1'&&isset($interface2['name']['itri']))echo ' /'.$interface2['name']['itri'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['itri']))echo $interface2['name']['itri'];else; ?>'>
			<input type='text' id='coupon' style='width:100%;height:60px;'>三顧茅廬 中和
			<button id='send' style='width:calc(30% - 7px);height:60px;margin:10px calc((20% + 7px) / 2) 1px;float:left;'><div id='name1'><?php if(isset($buttons1['name']['senditri']))echo $buttons1['name']['senditri'];else echo '兌換'; ?></div><?php if(isset($buttons2['name']['senditri']))echo '<div id="name2">'.$buttons2['name']['senditri'].'</div>'; ?></button>
			<button id='exit' style='width:calc(30% - 7px);height:60px;margin:10px calc((20% + 7px) / 2) 1px;float:left;' autofocus><div id='name1'><?php if(isset($buttons1['name']['exitdeposit']))echo $buttons1['name']['exitdeposit'];else echo '離開'; ?></div><?php if(isset($buttons2['name']['exitdeposit']))echo '<div id="name2">'.$buttons2['name']['exitdeposit'].'</div>'; ?></button>
		</div>
		<?php
		}
		else{
		}*/
		?>
		<div class='editoutman' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['editoutman'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['editoutman'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['editoutman'];else; ?>'>
			<input type='text' name='manname' style='width:calc(50% - 2px);height:50px;font-size:25px;margin:0 1px 1px 1px;padding:0 5px;background-color:#ffffff;text-align:left;float:left;' readonly>
			<input type='text' name='mancode' style='width:calc(50% - 2px);height:50px;font-size:25px;margin:0 1px 1px 1px;padding:0 5px;background-color:#ffffff;text-align:right;float:left;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>>
			<button id='number' value='7'>7</button>
			<button id='number' value='8'>8</button>
			<button id='number' value='9'>9</button>
			<button id='number' value='4'>4</button>
			<button id='number' value='5'>5</button>
			<button id='number' value='6'>6</button>
			<button id='number' value='1'>1</button>
			<button id='number' value='2'>2</button>
			<button id='number' value='3'>3</button>
			<button id='number' value='0'>0</button>
			<button id='number' value='' disabled></button>
			<button id='number' value='' disabled></button>
			<button id='send' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1'&&isset($buttons1['name']['check']))echo $buttons1['name']['check'];else echo '確認'; ?></button>
			<button id='back' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1'&&isset($buttons1['name']['bs']))echo $buttons1['name']['bs'];else echo '倒退'; ?></button>
			<button id='return' style='width:calc(100% / 3 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1'&&isset($buttons1['name']['return']))echo $buttons1['name']['return'];else echo '返回'; ?></button>
		</div>
		<div class='computemoney' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['computemoney'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['computemoney'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['computemoney'];else; ?>'>
			<div style='width:calc(50% - 1px);height:100%;float:left;margin-right:1px;'>
				<button id='number' value='7' style='font-size: 2.5em;'>7</button>
				<button id='number' value='8' style='font-size: 2.5em;'>8</button>
				<button id='number' value='9' style='font-size: 2.5em;'>9</button>
				<button id='number' value='4' style='font-size: 2.5em;'>4</button>
				<button id='number' value='5' style='font-size: 2.5em;'>5</button>
				<button id='number' value='6' style='font-size: 2.5em;'>6</button>
				<button id='number' value='1' style='font-size: 2.5em;'>1</button>
				<button id='number' value='2' style='font-size: 2.5em;'>2</button>
				<button id='number' value='3' style='font-size: 2.5em;'>3</button>
				<button id='number' value='0' style='font-size: 2.5em;'>0</button>
				<button id='number' value='AC' style='font-size: 2.5em;'>AC</button>
				<button id='number' value='NEXT' style='font-size: 2em;'>NEXT</button>
			</div>
			<div style='width:calc(50% - 1px);height:100%;float:left;margin-left:1px;font-size:1.3em;' onkeydown="return event.key != 'Enter';">
				<form id='sendbox'>
					<input type='hidden' name='machine' value='<?php echo $invmachine; ?>'>
					<input type='hidden' name='bizdate' value='<?php if(isset($_GET['bizdate']))echo $_GET['bizdate'];else echo $timeini['time']['bizdate']; ?>'>
					<input type='hidden' name='zcounter' value='<?php echo $timeini['time']['zcounter']; ?>'>
					<div class='div0' id='focus' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>1000<?php if(isset($interface1['name']['dolar']))echo $interface1['name']['dolar'];else echo '元'; ?> X</label><input type='hidden' name='coinvalue[]' value='1000'><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='coinnumber[]' value='0' readonly>
					</div>
					<div class='div1' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>500<?php if(isset($interface1['name']['dolar']))echo $interface1['name']['dolar'];else echo '元'; ?> X</label><input type='hidden' name='coinvalue[]' value='500'><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='coinnumber[]' value='0' readonly>
					</div>
					<div class='div2' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>100<?php if(isset($interface1['name']['dolar']))echo $interface1['name']['dolar'];else echo '元'; ?> X</label><input type='hidden' name='coinvalue[]' value='100'><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='coinnumber[]' value='0' readonly>
					</div>
					<div class='div3' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>50<?php if(isset($interface1['name']['dolar']))echo $interface1['name']['dolar'];else echo '元'; ?> X</label><input type='hidden' name='coinvalue[]' value='50'><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='coinnumber[]' value='0' readonly>
					</div>
					<div class='div4' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>10<?php if(isset($interface1['name']['dolar']))echo $interface1['name']['dolar'];else echo '元'; ?> X</label><input type='hidden' name='coinvalue[]' value='10'><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='coinnumber[]' value='0' readonly>
					</div>
					<div class='div5' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>5<?php if(isset($interface1['name']['dolar']))echo $interface1['name']['dolar'];else echo '元'; ?> X</label><input type='hidden' name='coinvalue[]' value='5'><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='coinnumber[]' value='0' readonly>
					</div>
					<div class='div6' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>1<?php if(isset($interface1['name']['dolar']))echo $interface1['name']['dolar'];else echo '元'; ?> X</label><input type='hidden' name='coinvalue[]' value='1'><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='coinnumber[]' value='0' readonly>
					</div>
					<div class='total' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'><?php if(isset($interface1['name']['tillmoney']))echo $interface1['name']['tillmoney'];else echo '錢櫃金額'; ?></label><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='total' value='0' readonly>
					</div>
					<div class='change' style='width:100%;margin:5px 0;overflow:hidden;display:none;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'><?php if(isset($interface1['name']['50']))echo $interface1['name']['50'];else echo '找零金'; ?></label><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='change' value='0' readonly>
					</div>
					<div class='TAX2' style='width:100%;margin:5px 0;overflow:hidden;display:none;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>現金收入</label><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='TAX2' value='0' readonly>
					</div>
					<div class='outmoney' style='width:100%;margin:5px 0;overflow:hidden;display:none;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'>其他收/支</label><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='outmoney' value='0' readonly>
					</div>
					<div class='previewmoney' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'><?php if(isset($interface1['name']['shouldmoney']))echo $interface1['name']['shouldmoney'];else echo '應有金額'; ?></label><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='previewmoney' value='0' readonly>
					</div>
					<div class='diffmoney' style='width:100%;margin:5px 0;overflow:hidden;'>
						<label style='width:calc(50% - 5px);float:left;text-align:right;padding:0 5px 0 0;'><?php if(isset($interface1['name']['differentmoney']))echo $interface1['name']['differentmoney'];else echo '差額'; ?></label><input type='number' style='width:calc(50% - 17px);float:right;padding:0 5px;margin:0 5px 0 0;text-align:right;' name='diffmoney' value='0' readonly>
					</div>
				</form>
				<button id='send' style='width:calc(100% / 2 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size:2em;'><?php if($buttons1!='-1'&&isset($buttons1['name']['check']))echo $buttons1['name']['check'];else echo '確認'; ?></button>
				<button id='return' style='width:calc(100% / 2 - 2px);height:calc((100% - 51px) / 5 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size:2em;'><?php if($buttons1!='-1'&&isset($buttons1['name']['return']))echo $buttons1['name']['return'];else echo '返回'; ?></button>
			</div>
		</div>
		<div class='salelisthint' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['salelisthint']))echo $interface1['name']['salelisthint'];else echo "帳單備註";if($interface1!='-1'&&isset($interface1['name']['salelisthint'])&&$interface2!='-1'&&isset($interface2['name']['salelisthint']))echo ' /'.$interface2['name']['salelisthint'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['salelisthint']))echo $interface2['name']['salelisthint'];else; ?>'>
			<textarea name='hintstring' rows='2' style='width:100%;font-size:25px;margin-bottom:1px;padding:0 5px;background-color:#ffffff;float:left;resize : none;' <?php if(preg_match('/('.$basic['mobilekeyword']['word'].')/',$_SERVER["HTTP_USER_AGENT"]))echo 'readonly'; ?>></textarea>
			<div style="width:100%;height:max-content;float:left;overflow:hidden;border-bottom:1px solid #00000080;padding-bottom:2px;">
				<button data-id='number' value='1' style='font-size: 2.5em;'>1</button>
				<button data-id='number' value='2' style='font-size: 2.5em;'>2</button>
				<button data-id='number' value='3' style='font-size: 2.5em;'>3</button>
				<button data-id='number' value='4' style='font-size: 2.5em;'>4</button>
				<button data-id='number' value='5' style='font-size: 2.5em;'>5</button>
				<button data-id='ntoe' value='eng'>ABC</button>
				<button data-id='number' value='6' style='font-size: 2.5em;'>6</button>
				<button data-id='number' value='7' style='font-size: 2.5em;'>7</button>
				<button data-id='number' value='8' style='font-size: 2.5em;'>8</button>
				<button data-id='number' value='9' style='font-size: 2.5em;'>9</button>
				<button data-id='number' value='0' style='font-size: 2.5em;'>0</button>
				<button data-id='chpage' value='1' disabled><?php if(isset($buttons1['name']['changepage']))echo $buttons1['name']['changepage'];else echo '換頁'; ?></button>
			</div>
			<div id='quickhint'>
				<?php
				if(file_exists('../database/salelisthint.ini')){
					$salelisthint=parse_ini_file('../database/salelisthint.ini',true);
					if(sizeof($salelisthint)>0){
						$sorthint=quicksort($salelisthint,'seq');
						//print_r($salelisthint);
						//print_r($sorthint);
						foreach($sorthint as $item){
							echo '<button class="hintitem" value="'.$item['name'].'">'.$item['name'].'</button>';
						}
					}
					else{
					}
				}
				else{
				}
				?>
			</div>
			<div style="width:100%;height:max-content;float:left;overflow:hidden;border-top:1px solid #00000080;padding-top:2px;">
				<button id='clear' style='width:calc(100% / 2 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'><?php if($buttons1!='-1'&&isset($buttons1['name']['reset']))echo $buttons1['name']['reset'];else echo '清空'; ?></button>
				<button id='check' style='width:calc(100% / 2 - 2px);background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;font-size: 2.5em;'><?php if($buttons1!='-1'&&isset($buttons1['name']['check']))echo $buttons1['name']['check'];else echo '確認'; ?></button>
			</div>
		</div>
		<div class='webbooking' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;background-color:#D6DCE5;' title='<?php if($interface1!='-1'&&isset($interface1['name']['weborder']))echo $interface1['name']['weborder'];else echo "網路訂單";if($interface1!='-1'&&isset($interface1['name']['weborder'])&&$interface2!='-1'&&isset($interface2['name']['weborder']))echo ' /'.$interface2['name']['weborder'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['weborder']))echo $interface2['name']['weborder'];else; ?>'>
			<ul>
				<?php
				if(isset($initsetting['nidin']['usenidin'])&&$initsetting['nidin']['usenidin']==1&&(!isset($initsetting['nidin']['autoaccept'])||$initsetting['nidin']['autoaccept']=='0')){
				?>
				<li><a href="#nidin">Nidin</a></li>
				<?php
				}
				else{
				}
				if(isset($initsetting['init']['quickclick'])&&$initsetting['init']['quickclick']=='1'&&isset($initsetting['init']['quickclicklisttype'])&&$initsetting['init']['quickclicklisttype']=='1'){
				?>
				<li><a href="#quickclick">QuickClick</a></li>
				<?php
				}
				else{
				}
				?>
			</ul>
			<?php
			//2022/10/20 原先設定若為自動接單，則不顯示nidin區塊，但這樣會導致自動化的部分，沒有按鈕可以觸發
			if(isset($initsetting['nidin']['usenidin'])&&$initsetting['nidin']['usenidin']==1){//(!isset($initsetting['nidin']['autoaccept'])||$initsetting['nidin']['autoaccept']=='0')
			?>
			<div id="nidin" style="padding:0;margin:0;<?php
			//2022/10/20 為了使自動化有按鈕可以觸發，因此這邊改成若為自動接單，則隱藏此區塊
			if(isset($initsetting['nidin']['autoaccept'])&&$initsetting['nidin']['autoaccept']=='1')echo 'display:none;'; 
			?>">
				<div class="listheader">
					<div id-data="button" class='disaccept'>接受訂單</div><div id-data="button" class='disreject'>拒絕訂單</div><!-- <div class='printbox'><div id-data="buttonlabel">列印狀態</div><div id-data="button" class='printtag'>列印貼紙</div><div id-data="button" class='printclient'>列印明細</div><input type='hidden' class='printtagvalue' value='1'><input type='hidden' class='printclientvalue' value='1'></div> -->
					<table class='headertable'>
						<tr>
							<td id-data="firstlabel">訂單編號</td>
							<td id-data="input" id="ordercode"></td>
							<td id-data="label">訂購人</td>
							<td id-data="input" id="memname"></td>
							<td id-data="label">連絡電話</td>
							<td id-data="input" id="memtel"></td>
							<td id-data="label">商品數量</td>
							<td id-data="input" style="text-align:right;" id="salettlqty">0</td>
							<td id-data="label">訂單金額</td>
							<td id-data="input" style="text-align:right;" id="salettlamt">0</td>
						</tr>
						<tr>
							<td id-data="firstlabel">取餐方式</td>
							<td id-data="input" id="listtype"></td>
							<td id-data="label">付款狀態</td>
							<td id-data="input" id="paystate"></td>
							<td id-data="label">付款方式</td>
							<td id-data="input" id="paymethod"></td>
							<td id-data="label">訂單狀態</td>
							<td id-data="input" id="liststate"></td>
							<td id-data="label">折扣</td>
							<td id-data="input" style="text-align:right;" id="discount">0</td>
						</tr>
						<tr>
							<td id-data="firstlabel">取餐時間</td>
							<td id-data="input" colspan="2" id="receverdatetime"></td>
							<td id-data="label">下訂時間</td>
							<td id-data="input" colspan="2" id="senddatetime"></td>
							<td id-data="label">統編/載具</td>
							<td id-data="input" id="carrier"></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td id-data="firstlabel">外送地址</td>
							<td id-data="input" colspan="9" id="memaddress"></td>
						</tr>
						<tr>
							<td id-data="firstlabel">訂購備註</td>
							<td id-data="input" class="remarks" colspan="9" id="listremarks"></td>
						</tr>
					</table>
				</div>
				<div class="itemlist">
					<table class="itemstable">
						<!-- label -->
						<tr>
							<th style="width:40px;">序</th>
							<th style="width:calc((100% - 490px) * 2 / 3);">商品名稱</th>
							<th style="width:60px;">規格</th>
							<th style="width:calc((100% - 490px) * 2 / 3);">加料</th>
							<th style="width:100px;">小計</th>
							<th style="width:40px;">數量</th>
							<th style="width:100px;">金額</th>
							<th style="width:150px;">備註</th>
						</tr>
						<!-- itemlist by loop -->
					</table>
				</div>
				<div>
					<div class="remaining">
						<table class="remainingtable">
							<tr>
								<td>待接受訂單：共<span style="margin:0 5px;" id="remainingnumber">0</span>筆</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<?php
			}
			else{
			}
			if(isset($initsetting['init']['quickclick'])&&$initsetting['init']['quickclick']=='1'&&isset($initsetting['init']['quickclicklisttype'])&&$initsetting['init']['quickclicklisttype']=='1'){
			?>
			<div id="quickclick" style="padding:0;margin:0;height:100%;">
				<div class="listheader">
					<div id-data="button" class='disaccept'>接受訂單</div><div id-data="button" class='disreject'>拒絕訂單</div><!-- <div class='printbox'><div id-data="buttonlabel">列印狀態</div><div id-data="button" class='printtag'>列印貼紙</div><div id-data="button" class='printclient'>列印明細</div><input type='hidden' class='printtagvalue' value='1'><input type='hidden' class='printclientvalue' value='1'></div> -->
					<table class='headertable'>
						<tr>
							<td id-data="firstlabel">訂單編號</td>
							<td id-data="input" id="ordercode"></td>
							<td id-data="label">訂購人</td>
							<td id-data="input" id="memname"></td>
							<td id-data="label">連絡電話</td>
							<td id-data="input" id="memtel"></td>
							<td id-data="label">商品數量</td>
							<td id-data="input" style="text-align:right;" id="salettlqty">0</td>
							<td id-data="label">訂單金額</td>
							<td id-data="input" style="text-align:right;" id="salettlamt">0</td>
						</tr>
						<tr>
							<td id-data="firstlabel">取餐方式</td>
							<td id-data="input" id="listtype"></td>
							<td id-data="label">付款狀態</td>
							<td id-data="input" id="paystate"></td>
							<td id-data="label">付款方式</td>
							<td id-data="input" id="paymethod"></td>
							<td id-data="label">訂單狀態</td>
							<td id-data="input" id="liststate"></td>
							<td id-data="label">折扣</td>
							<td id-data="input" style="text-align:right;" id="discount">0</td>
						</tr>
						<tr>
							<td id-data="firstlabel">取餐時間</td>
							<td id-data="input" colspan="2" id="receverdatetime"></td>
							<td id-data="label">下訂時間</td>
							<td id-data="input" colspan="2" id="senddatetime"></td>
							<td id-data="label">統編/載具</td>
							<td id-data="input" id="carrier"></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td id-data="firstlabel">外送地址</td>
							<td id-data="input" colspan="9" id="memaddress"></td>
						</tr>
						<tr>
							<td id-data="firstlabel">訂購備註</td>
							<td id-data="input" class="remarks" colspan="9" id="listremarks"></td>
						</tr>
					</table>
				</div>
				<div class="itemlist">
					<table class="itemstable">
						<!-- label -->
						<tr>
							<th style="width:40px;">序</th>
							<th style="width:calc((100% - 490px) * 2 / 3);">商品名稱</th>
							<th style="width:60px;">規格</th>
							<th style="width:calc((100% - 490px) * 2 / 3);">加料</th>
							<th style="width:100px;">小計</th>
							<th style="width:40px;">數量</th>
							<th style="width:100px;">金額</th>
							<th style="width:150px;">備註</th>
						</tr>
						<!-- itemlist by loop -->
					</table>
				</div>
				<div>
					<div class="remaining">
						<table class="remainingtable">
							<tr>
								<td>待接受訂單：共<span style="margin:0 5px;" id="remainingnumber">0</span>筆</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<?php
			}
			else{
			}
			?>
		</div>
		<div class='checkdeletesale' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['checkdeletesale'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['checkdeletesale'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['checkdeletesale'];else; ?>'>
			<input type='hidden' name='type'>
			<input type='hidden' name='psw'>
			<div style='width:calc(100% - 2px);height:calc(100% - 52px - 5px);margin-bottom:5px;border-radius:5px;border: 1px solid #898989;'>
				<div style='width: calc(100% - 6px); height: calc((100% / 5) - 1px); margin: 0 3px; border-bottom: 1px solid #3E3A39; float: left;'>
					<div style="height: 100%; float: left; width: calc((100% / 3) - 1px); border-right: 1px solid #898989;font-weight:bold;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos ' '.';"><span class="" style='grid-area: amos;'>Bizdate</span></div>
					<div style="width: calc((100% / 3) * 2 - 5px); height: 100%; float: left;background-color: #ffffff;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';padding-left:5px;"><span class="bizdate" style='grid-area: amos;'></span></div>
				</div>
				<div style='width: calc(100% - 6px); height: calc((100% / 5) - 1px); margin: 0 3px; border-bottom: 1px solid #3E3A39; float: left;'>
					<div style="height: 100%; float: left; width: calc((100% / 3) - 1px); border-right: 1px solid #898989;font-weight:bold;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';"><span class="" style='grid-area: amos;'>No</span></div>
					<div style="width: calc((100% / 3) * 2 - 5px); height: 100%; float: left;background-color: #ffffff;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';padding-left:5px;"><span class="saleno" style='grid-area: amos;'></span></div>
				</div>
				<div style='width: calc(100% - 6px); height: calc((100% / 5) - 1px); margin: 0 3px; border-bottom: 1px solid #3E3A39; float: left;'>
					<div style="height: 100%; float: left; width: calc((100% / 3) - 1px); border-right: 1px solid #898989;font-weight:bold;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';"><span class="" style='grid-area: amos;'>Consecnumber</span></div>
					<div style="width: calc((100% / 3) * 2 - 5px); height: 100%; float: left;background-color: #ffffff;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';padding-left:5px;"><span class="consecnumber" style='grid-area: amos;'></span></div>
				</div>
				<div style='width: calc(100% - 6px); height: calc((100% / 5) - 1px); margin: 0 3px; border-bottom: 1px solid #3E3A39; float: left;'>
					<div style="height: 100%; float: left; width: calc((100% / 3) - 1px); border-right: 1px solid #898989;font-weight:bold;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';"><span class="" style='grid-area: amos;'>Invoicenumber</span></div>
					<div style="width: calc((100% / 3) * 2 - 5px); height: 100%; float: left;background-color: #ffffff;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';padding-left:5px;"><span class="invoicenumber" style='grid-area: amos;'></span></div>
				</div>
				<div style='width: calc(100% - 6px); height: calc((100% / 5)); margin: 0 3px; float: left;'>
					<div style="height: 100%; float: left; width: calc((100% / 3) - 1px); border-right: 1px solid #898989;font-weight:bold;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';"><span class="" style='grid-area: amos;'>Money</span></div>
					<div style="width: calc((100% / 3) * 2 - 5px); height: 100%; float: left;background-color: #ffffff;display: grid; grid-template-rows: 1fr auto 1fr; grid-template-columns: 1fr auto 1fr; grid-template-areas: '.' 'amos' '.';padding-left:5px;"><span class="money" style='grid-area: amos;'></span></div>
				</div>
			</div>
			<button id='send' style='width:calc(100% / 2 - 2px);height:50px;background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1'&&isset($buttons1['name']['check']))echo $buttons1['name']['check'];else echo '確認'; ?></button>
			<button id='cancel' style='width:calc(100% / 2 - 2px);height:50px;background-color: #c6d3e3;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin:1px;'><?php if($buttons1!='-1'&&isset($buttons1['name']['cancel']))echo $buttons1['name']['cancel'];else echo '取消'; ?></button>
		</div>
		<?php
		if(isset($initsetting['a1'])&&isset($initsetting['a1']['usea1erp'])&&$initsetting['a1']['usea1erp']=='1'){//2021/4/14 0>>關閉a1ERP串接1>>開啟a1ERP串接
		?>
		<div class='waitstockload' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;background-color:#D6DCE5;' title='讀取庫存'>
			<div><span>Loading</span><span class='loadingdot'>...</span></div>
			<div><span class='loadingtext'></span> / <span class='loadinggoal'></span></div>
		</div>
		<?php
		}
		else{
		}
		?>
		<?php
		if(isset($initsetting['init']['directlinepay'])&&$initsetting['init']['directlinepay']=='1'){//2022/4/27 0>>關閉直接linepay付款串接1>>開啟直接linepay付款串接
		?>
		<div class='readlinecode' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readlinecode']))echo $interface1['name']['readlinecode'];if($interface1!='-1'&&isset($interface1['name']['readlinecode'])&&$interface2!='-1'&&isset($interface2['name']['readlinecode']))echo ' /'.$interface2['name']['readlinecode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readlinecode']))echo $interface2['name']['readlinecode'];else; ?>'>
			<div style='display:none;' id='buttonhtml'></div>
			<input type='text' style='width: calc(100% - 6px);padding: 3px;' name='linecode' value='' placeholder="請掃描LinePay付款條碼">
			<input type='hidden' name='orderid'>
			<input type='hidden' name='money'>
			<input type='hidden' name='price'>
			<input type='hidden' name='inv'>
			<input type='hidden' name='saverowname'>
			<input type='hidden' name='type'>
			<div class='qrhint' style='width:100%;height:max-content;'></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='checksale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>檢查交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;'>取消交易</button></div>
		</div>
		<div class='voidlinepay' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readlinecode']))echo $interface1['name']['readlinecode'];if($interface1!='-1'&&isset($interface1['name']['readlinecode'])&&$interface2!='-1'&&isset($interface2['name']['readlinecode']))echo ' /'.$interface2['name']['readlinecode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readlinecode']))echo $interface2['name']['readlinecode'];else; ?>'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>退款處理中<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>退款成功</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消</button></div>
		</div>
		<div class='voidsalewithlinepay' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readlinecode']))echo $interface1['name']['readlinecode'];if($interface1!='-1'&&isset($interface1['name']['readlinecode'])&&$interface2!='-1'&&isset($interface2['name']['readlinecode']))echo ' /'.$interface2['name']['readlinecode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readlinecode']))echo $interface2['name']['readlinecode'];else; ?>'>
			<input type='hidden' name='type'>
			<input type='hidden' name='saleno'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>退款處理中<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>退款成功</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消</button></div>
		</div>
		<?php
		}
		else{
		}
		if(isset($initsetting['init']['jkos'])&&$initsetting['init']['jkos']=='1'){//2022/4/27 0>>關閉街口付款串接1>>開啟街口付款串接
		?>
		<div class='readjkoscode' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readjkoscode']))echo $interface1['name']['readjkoscode'];if($interface1!='-1'&&isset($interface1['name']['readjkoscode'])&&$interface2!='-1'&&isset($interface2['name']['readjkoscode']))echo ' /'.$interface2['name']['readjkoscode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readjkoscode']))echo $interface2['name']['readjkoscode'];else; ?>'>
			<div style='display:none;' id='buttonhtml'></div>
			<input type='text' style='width: calc(100% - 6px);padding: 3px;' name='jkoscode' value='' placeholder="請掃描街口付款條碼">
			<input type='hidden' name='orderid'><!-- POS端交易號，取消使用，需紀錄資料庫 -->
			<input type='hidden' name='tradeno'><!-- 街口端交易號，退款使用，需紀錄資料庫 -->
			<input type='hidden' name='carrier'>
			<input type='hidden' name='money'>
			<input type='hidden' name='price'>
			<input type='hidden' name='inv'>
			<input type='hidden' name='saverowname'>
			<input type='hidden' name='type'>
			<div class='payTitle' style='width:100%;height:max-content;'></div>
			<div class='payqrhint' style='width:100%;height:max-content;'></div>
			<div class='payhintstring' style='width:calc(100% - 10px);height:calc(100% / 5);'></div>
			<div class='cancelTitle' style='width:100%;height:max-content;border-top:1px solid #898989;display:none;'>Cancel</div>
			<div class='cancelqrhint' style='width:100%;height:max-content;display:none;'></div>
			<div class='cancelhintstring' style='width:calc(100% - 10px);height:calc(100% / 5);display:none;'></div>
			<div style='width:100%;height:calc(100% / 5);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='checksale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>檢查交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;' disabled>取消交易</button><button id='closejkos' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;'>關閉</button></div>
		</div>
		<div class='voidjkospay' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readjkoscode']))echo $interface1['name']['readjkoscode'];if($interface1!='-1'&&isset($interface1['name']['readjkoscode'])&&$interface2!='-1'&&isset($interface2['name']['readjkoscode']))echo ' /'.$interface2['name']['readjkoscode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readjkoscode']))echo $interface2['name']['readjkoscode'];else; ?>'>
			<input type='hidden' name='orderid'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>退款處理中<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>退款成功</button><button id='checksale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;' disabled>檢查退款</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消</button></div>
		</div>
		<div class='voidsalewithjkospay' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readjkoscode']))echo $interface1['name']['readjkoscode'];if($interface1!='-1'&&isset($interface1['name']['readjkoscode'])&&$interface2!='-1'&&isset($interface2['name']['readjkoscode']))echo ' /'.$interface2['name']['readjkoscode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readjkoscode']))echo $interface2['name']['readjkoscode'];else; ?>'>
			<input type='hidden' name='type'>
			<input type='hidden' name='saleno'>
			<input type='hidden' name='tradeno'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>退款處理中<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>退款成功</button><button id='checksale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;' disabled>檢查退款</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消</button></div>
		</div>
		<?php
		}
		else{
		}
		if(isset($initsetting['init']['pxpayplus'])&&$initsetting['init']['pxpayplus']=='1'){//2022/10/28 0>>關閉全支付串接1>>開啟全支付串接
		?>
		<div class='readpxpaypluscode' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readpxpaypluscode']))echo $interface1['name']['readpxpaypluscode'];if($interface1!='-1'&&isset($interface1['name']['readpxpaypluscode'])&&$interface2!='-1'&&isset($interface2['name']['readpxpaypluscode']))echo ' /'.$interface2['name']['readpxpaypluscode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readpxpaypluscode']))echo $interface2['name']['readpxpaypluscode'];else; ?>'>
			<div style='display:none;' id='buttonhtml'></div>
			<input type='text' style='width: calc(100% - 6px);padding: 3px;' name='pxpaypluscode' value='' placeholder="請掃描全支付付款條碼">
			<input type='hidden' name='orderid'><!-- POS端交易號，取消使用，需紀錄資料庫 -->
			<input type='hidden' name='tradeno'><!-- 街口端交易號，退款使用，需紀錄資料庫 -->
			<input type='hidden' name='carrier'>
			<input type='hidden' name='money'>
			<input type='hidden' name='price'>
			<input type='hidden' name='inv'>
			<input type='hidden' name='saverowname'>
			<input type='hidden' name='type'>
			<div class='payTitle' style='width:100%;height:max-content;'></div>
			<div class='payqrhint' style='width:100%;height:max-content;'></div>
			<div class='payhintstring' style='width:calc(100% - 10px);height:calc(100% / 5);'></div>
			<div class='cancelTitle' style='width:100%;height:max-content;border-top:1px solid #898989;display:none;'>Cancel</div>
			<div class='cancelqrhint' style='width:100%;height:max-content;display:none;'></div>
			<div class='cancelhintstring' style='width:calc(100% - 10px);height:calc(100% / 5);display:none;'></div>
			<div style='width:100%;height:calc(100% / 5);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>成功交易</button><button id='checksale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>檢查交易</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;' disabled>取消交易</button><button id='closepxpayplus' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;'>關閉</button></div>
		</div>
		<div class='voidpxpaypluspay' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readpxpaypluscode']))echo $interface1['name']['readpxpaypluscode'];if($interface1!='-1'&&isset($interface1['name']['readpxpaypluscode'])&&$interface2!='-1'&&isset($interface2['name']['readpxpaypluscode']))echo ' /'.$interface2['name']['readpxpaypluscode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readpxpaypluscode']))echo $interface2['name']['readpxpaypluscode'];else; ?>'>
			<input type='hidden' name='orderid'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>退款處理中<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>退款成功</button><button id='checksale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;' disabled>檢查退款</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消</button></div>
		</div>
		<div class='voidsalewithpxpaypluspay' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1'&&isset($interface1['name']['readpxpaypluscode']))echo $interface1['name']['readpxpaypluscode'];if($interface1!='-1'&&isset($interface1['name']['readpxpaypluscode'])&&$interface2!='-1'&&isset($interface2['name']['readpxpaypluscode']))echo ' /'.$interface2['name']['readpxpaypluscode'];else if($interface1=='-1'&&$interface2!='-1'&&isset($interface2['name']['readpxpaypluscode']))echo $interface2['name']['readpxpaypluscode'];else; ?>'>
			<input type='hidden' name='type'>
			<input type='hidden' name='orderid'>
			<input type='hidden' name='saleno'>
			<input type='hidden' name='tradeno'>
			<input type='hidden' name='money'>
			<div class='qrhint' style='width:100%;height:calc(100% / 3);'>退款處理中<span id='loading'>...</span></div>
			<div class='hintstring' style='width:100%;height:calc(100% / 3);'></div>
			<div style='width:100%;height:calc(100% / 3);'><button id='successsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;' disabled>退款成功</button><button id='checksale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;' disabled>檢查退款</button><button id='cancelsale' style='width:calc(50% - 4px);height:calc(100% - 2px);margin:1px;float:left;display:none;'>取消</button></div>
		</div>
		<?php
		}
		else{
		}
		?>
	</div>
	<?php
	if(isset($initsetting['init']['posdvr'])&&$initsetting['init']['posdvr']=='1'){
	?>
	<iframe id='posdvrPort' style='display:none;'></iframe>
	<?php
	}
	else{
	}
	?>
	<?php
		if(isset($initsetting['yunlincoins']['open'])&&$initsetting['yunlincoins']['open']=='1'){//2022/9/26 雲林幣轉換doubleplus儲值金(預計兩者比例為10:1)
	?>
			<div class='yunlincoins'>
				<input type='hidden' name='yunlintoken' value=''>
				<div style='width:100%;height:calc(100% / 5 * 4 - 1px);margin-bottom:1px;float:left;'>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;text-align:center;'>轉換前</div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;'><input type='text' name='beforemoney' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;' readonly></div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;text-align:center;'>雲林幣餘額</div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;'><input type='text' name='yunlinbalance' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;' readonly></div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;text-align:center;'>轉換雲林幣</div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;'><input type='text' name='tranyunlincoins' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;background-color:#ffffff;' value='0' readonly></div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;text-align:center;'><button id='plus10' style="width:20%;height:100%;">+<?php echo $yunlincoins['yunlincoins']['coins']; ?></button><button id='plus50' style="width:20%;height:100%;">+<?php echo $yunlincoins['yunlincoins']['coins']*5; ?></button><button id='plus100' style="width:20%;height:100%;">+<?php echo $yunlincoins['yunlincoins']['coins']*10; ?></button><button id='plusall' style="width:20%;height:100%;">所有</button></button><button id='clear' style="width:20%;height:100%;">清除</button></div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;text-align:center;'>兌換儲值金</div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;'><input type='text' name='getmemmoney' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;' value='0' readonly></div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;text-align:center;'>轉換後</div>
					<div style='width:100%;height:calc(100% / 12 - 2px);margin:1px;float:left;'><input type='text' name='aftermoney' style='width:calc(90% - 10px);height:calc(100% - 10px);margin:0 5%;text-align:right;padding:5px;' value='0' readonly></div>
					<div style='width:100%;height:calc(100% / 4 - 2px);margin:1px;float:left;text-align:center;'></div>
				</div>
				<button class='tranyunlin' style='width:calc(100% / 2 - 1px);height:calc(100% / 5 - 1px);margin:1px 1px 0 0;float:left;'><div style='width:100%;float:left;'>轉換雲林幣</div></button>
				<button class='cancel' style='width:calc(100% / 2 - 1px);height:calc(100% / 5 - 1px);margin:1px 0 0 1px;float:left;'><div style='width:100%;float:left;'>取消</div></button>
			</div>
	<?php
		}
		else{
		}
	?>
	
	<?php
	if(!isset($initsetting['init']['controltable'])||$initsetting['init']['controltable']=='0'){
		if(isset($initsetting['init']['tabnum'])&&$initsetting['init']['tabnum']==1&&$listtype==1){
			if(isset($_GET['tabnum'])){
			}
			else{
			?>
			<script type="text/javascript">
			$(document).ready(function(){
				tabinput.dialog('open');
			});
			</script>
			<?php
			}
		}
		else{
		}
	}
	else{
	}
	?>
	
</body>
</html>
