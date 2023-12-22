<!doctype html>
<html lang="en">
<head>
	<?php
	include_once '../tool/dbTool.inc.php';
	date_default_timezone_set('Asia/Taipei');
	$initsetting=parse_ini_file('../database/initsetting.ini',true);
	$setup=parse_ini_file('../database/setup.ini',true);
	if(isset($_POST['machinetype'])&&file_exists('../database/time'.$_POST['machinetype'].'.ini')){
		$time=parse_ini_file('../database/time'.$_POST['machinetype'].'.ini',true);
	}
	else{
		$time=parse_ini_file('../database/timem1.ini',true);
	}
	$machinedata=parse_ini_file('../database/machinedata.ini',true);
	$typename=parse_ini_file('../database/'.$setup['basic']['company'].'-front.ini',true);
	$itemname=parse_ini_file('../database/'.$setup['basic']['company'].'-menu.ini',true);
	$tastename=parse_ini_file('../database/'.$setup['basic']['company'].'-taste.ini',true);
	if(file_exists('../database/orderpos.setup.ini')){
		$orderpos=parse_ini_file('../database/orderpos.setup.ini',true);
	}
	else{
		$orderpos='-1';
	}
	?>
	<meta charset="UTF-8">
	<title>桌加點餐網</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="manifest" href="./lib/json/manifest.json">
	<script src="../tool/jquery-1.12.4.js"></script>
	<script src="../tool/ui/1.12.1/jquery-ui.js"></script>
	<script src="./lib/js/main.js?<?php echo date('YmdHis'); ?>"></script>
	<script src="../tool/fastclick/lib/fastclick.js"></script>
	<script src="../demopos/lib/api/ourmember/member_api.js"></script>
	<?php //2021/9/3 圖片延遲讀取 ?>
	<script src="../tool/lazy/lazyload.js"></script>
	<link rel="stylesheet" href="../tool/ui/1.12.1/themes/base/jquery-ui.css">
	<style>
		body {
			width:100%;
			height:100%;
			margin:0;
			padding:0;
			position: absolute;
			font-family: Consolas,Microsoft JhengHei,sans-serif;
			color:#4a4a4a;
		}
		input,
		select {
			font-family: Consolas,Microsoft JhengHei,sans-serif;
			color:#4a4a4a;
		}
		#title {
			width:calc(100% - 10px);
			height:50px;
			position: fixed;
			top: 0;
			margin:0;
			padding:5px;
			overflow:hidden;
			z-index:1;
			box-shadow: rgba(0, 0, 0, 0.2) 0px 0px 8px;
		}
		#title .setbutton {
			width:22px;
			height:22px;
			padding:12px 14px;
			text-align:center;
			z-index:1;
			position: absolute;
			right:5px;
		}
		#title .setbutton .funkey {
			width: 22px;
			height: 3px;
			margin: 4px 0;
			background-color: rgb(26,26,26);
			border-radius:3px;
		}
		#content .dep,
		#content .type,
		#content .item,
		#content .company,
		#title .return,
		#title .setbutton,
		#keybox #del,
		#keybox #list,
		#keybox #sale,
		#keybox #tempsale {
			cursor: pointer;
		}
		#content .type {
			height:calc(50px - 6px);
			line-height:50px;
			margin:2px 0 2px 5px;
			padding:0 10px;
			text-align:center;
			font-size:20px;
			color:#4a4a4a;
			font-weight:bold;
			border:1px solid #898989;
			border-radius:5px;
			float:left;
			box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;
		}
		#content .type#checked {
			box-shadow:inset rgba(0, 0, 0, 0.2) 2px 2px;
			background-color:rgb(200,200,200,0.5);
		}
		#content #items .itemsbox {
			width:100%;
			display:flex;
			flex-wrap:wrap;
		}
		#content #items .itemsbox .item {
			width:calc(50% - 12px);
			/*height:max-content;
			line-height:max-content;*/
			margin:5px 5px 10px 5px;
			padding:5px 0 5px;
			text-align:center;
			font-size:20px;
			color:#4a4a4a;
			/*border:1px solid #898989;
			border-radius:5px;
			box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;*/
			float:left;
			display:flex;
		}
		#content #items .itemsbox .item #itemtext span#name2 {
			font-size:15px;
		}
		#content #items .itemsbox .item #itemtext span#money {
			font-weight:bold;
		}
		#content #items .itemsbox .item #itemcontain {
			width:100%;
			display:grid;
		}
		#content #items .itemsbox .item #itemcontain #itemimg {
			width:100%;
			height:150px;
			justify-content:center;
			align-items:center;
			flex-direction:column;
			display:inline-flex;
		}
		#content #items .itemsbox .item #itemcontain #itemimg img {
			width: auto;
			height: auto;
			max-width: 100%;
			max-height: 100%;
			border-radius: 20px;
			padding:10px;
			vertical-align: middle;
			object-fit:contain;
		}
		#content #items .itemsbox .item #itemcontain #itemtext {
			width:100%;
			text-align:center;
			flex-direction:column;
			display:flex;
		}
		#content .typelabel {
			width:calc(100% - 12px);
			height:max-content;
			line-height:max-content;
			margin:5px 5px 10px 5px;
			padding:5px 0 5px;
			text-align:center;
			font-weight:bold;
			font-size:23px;
			color:#000000;
			float:left;
		}
		/*#content .item {
			width:calc(100% - 12px);
			height:40px;
			line-height:40px;
			margin:5px 5px 10px 5px;
			padding:5px 0;
			text-align:center;
			font-size:20px;
			color:#4a4a4a;
			border:1px solid #898989;
			border-radius:5px;
			box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;
		}*/
		.detail #detail tr {
			height:50px;
		}
		.tastenumberbox {
			width: calc((100% - 20px) / 3 - 4px);
			height: 5%;
			min-height: 25px;
			position: absolute;
			z-index: 3;
			display: none;
			background-color: #ffffff;
		}
		.tastenumberbox div {
			float: left;
			width: calc(100% / 3 - 1px);
			height: calc(100% - 2px);
			text-align: center;
			border: 1px solid #000000;
			background-color: #898989;
		}
		.tastenumberbox .difftaste {
			border-right: 0;
			border-top-left-radius: 5px;
			border-bottom-left-radius: 5px;
		}
		.tastenumberbox .addtaste {
			border-left: 0;
			border-top-right-radius: 5px;
			border-bottom-right-radius: 5px;
		}
		.tastenumberbox input[type="text"] {
			width: calc(100% / 3 - 2px);
			height: calc(100% - 2px);
			margin: 0;
			padding: 0;
			text-align: center;
			float: left;
			border: 1px solid #000000;
			border-radius: 0;
		}
		.switch {
			text-align: center;
			width: 100%;
			height: calc(100% - 20px);
			min-height: 30px;
			padding: 10px 0;
			/* overflow-wrap: anywhere; */
			word-break: break-word;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		}
		.switch#movebox {
			float: left;
			position: relative;
			display: inline-block;
			width: 34px;
			height: 20px;
			min-height: 0;
			margin: 13px 0;
			word-break: normal;
			padding: 0;
			justify-content: normal;
			align-items: normal;
		}

		.switch input {display:none;}

		.switch .slider {
			position: absolute;
			cursor: pointer;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: #dcdcdc;
			-webkit-transition: .4s;
			transition: .4s;
			z-index:-1;
			border-radius: 5px;
		}

		.switch .slider:before {
			position: absolute;
			content: "";
			height: 13px;
			width: 13px;
			left: 4px;
			bottom: 3.5px;
			background-color: black;
			-webkit-transition: .4s;
			transition: .4s;
			border-radius: 100%;
		}

		.switch input[type="checkbox"]:checked + .slider {
			background-color: #ff5e73;
		}

		.switch input:focus + .slider {
			box-shadow: 0 0 1px #2196F3;
		}

		.switch input[type="checkbox"]:checked + .slider:before {
			-webkit-transform: translateX(13px);
			-ms-transform: translateX(13px);
			transform: translateX(13px);
		}
		#content #receverdata {
			width:calc(100% - 10px);
			margin:0 5px;
			border-collapse: collapse;
			font-size:16px;
		}
		#content #receverdata td {
			padding:16px 0 8px 0;
			border-bottom:1px solid #dcdcdc;
		}
		#content #receverdata td:nth-child(odd) {
			white-space:nowrap;
			width:max-content;
		}
		#content #receverdata td:nth-child(even) {
			text-align:left;
			padding-left:5%;
			width:100%;
		}
		#content #receverdata select {
			height:calc(40px - 2px);
			border:1px solid #898989;
			font-size:16px;
			background-color:#ffffff;
			margin:0;
		}
		.salepay table,
		.cremem table {
			width:100%;
			border-collapse: collapse;
		}
		.salepay table td,
		.cremem table td {
			padding:10px 5px;
		}
		.salepay table select,
		.salepay table #check,
		.cremem table #check {
			width:100%;
			height:55px;
			border:1px solid #898989;
			border-radius:5px;
			line-height:55px;
			font-family:Microsoft JhengHei,MingLiU;
			font-size:30px;
			padding:5px 3px;
			margin:0;
		}
		.salepay table #check,
		.cremem table #check {
			line-height:43px;
		}
		.salepay table input,
		.cremem table input {
			width:calc(100% - 8px);
			border:1px solid #898989;
			border-radius:5px;
			line-height:55px;
			font-family:Microsoft JhengHei,MingLiU;
			font-size:30px;
			padding:5px 3px;
			margin:0;
			text-align:right;
		}
	</style>
</head>
<body>
	<div class='basic' style="display:none;">
		<input type='hidden' name="date" value="<?php echo date('Y-m-d');; ?>">
		<input type='hidden' name='story' value='<?php echo $setup['basic']['company']; ?>'>
		<input type='hidden' name='dep' value='<?php echo $setup['basic']['story']; ?>'>
		<input type='hidden' name='listtype' value=''>
		<input type='hidden' name='usercode' value='<?php if(isset($_GET['usercode']))echo $_GET['usercode'];else; ?>'>
		<input type='hidden' name='username' value='<?php if(isset($_GET['username']))echo $_GET['username'];else; ?>'>
		<?php
		if(isset($setup['basic']['company'])&&$setup['basic']['company']!=''&&(isset($setup['basic']['story'])&&$setup['basic']['story']!='')){
			if(file_exists('../database/unit.ini')){
				$depunit=parse_ini_file('../database/unit.ini',true);
			}
			else{
			}
		?>
		<input type='hidden' class='unit' value='<?php if(isset($depunit)&&isset($depunit['webunit']['qtyunit'])&&$depunit['webunit']['qtyunit']!='')echo $depunit['webunit']['qtyunit'];else echo '項'; ?>'>
		<input type='hidden' class='moneypreunit' value='<?php if(isset($depunit)&&isset($depunit['webunit']['moneypreunit'])&&$depunit['webunit']['moneypreunit']!='')echo $depunit['webunit']['moneypreunit'];else echo '＄'; ?>'>
		<input type='hidden' class='moneysufunit' value='<?php if(isset($depunit)&&isset($depunit['webunit']['moneysufunit'])&&$depunit['webunit']['moneysufunit']!='')echo $depunit['webunit']['moneysufunit'];else echo '元'; ?>'>
		<?php
		}
		else{
		?>
		<input type='hidden' class='unit' value=''>
		<input type='hidden' class='moneypreunit' value=''>
		<input type='hidden' class='moneysufunit' value=''>
		<?php
		}
		?>
		<input type='hidden' name='openchar' value='<?php if(isset($initsetting['init']['openchar']))echo $initsetting['init']['openchar'];else echo '0'; ?>'>
		<input type='hidden' name='charge' value='<?php if(isset($initsetting['init']['charge']))echo $initsetting['init']['charge'];else echo '0'; ?>'>
		<input type='hidden' name='chargeeq' value='<?php if(isset($initsetting['init']['chargeeq']))echo $initsetting['init']['chargeeq'];else echo '1'; ?>'>
		<input type='hidden' name='chargenumber' value='<?php if(isset($initsetting['init']['chargenumber']))echo $initsetting['init']['chargenumber'];else echo '0'; ?>'>
		<input type='hidden' name='accuracy' value='<?php if(isset($initsetting['init']['accuracy']))echo $initsetting['init']['accuracy'];else echo '1'; ?>'>
		<input type='hidden' name='accuracytype' value='<?php if(isset($initsetting['init']['accuracytype']))echo $initsetting['init']['accuracytype'];else echo '1'; ?>'>
		<input type='hidden' name='autodis' value='<?php if(isset($initsetting['init']['autodis']))echo $initsetting['init']['autodis'];else echo '0'; ?>'>
		<input type='hidden' name='openmember' value='<?php if(isset($initsetting['init']['openmember']))echo $initsetting['init']['openmember'];else echo '0'; ?>'>
		<input type='hidden' name='onlinemember' value='<?php if(isset($initsetting['init']['onlinemember']))echo $initsetting['init']['onlinemember'];else echo '0'; ?>'>
		<input type='hiddne' name='serverip' value='<?php echo $machinedata['orderpos']['serverip']; ?>'>
		<?php
		if($orderpos=='-1'||(isset($orderpos['init']['sale'])&&$orderpos['init']['sale']=='0'&&isset($orderpos['init']['tempsale'])&&$orderpos['init']['tempsale']=='0')){
		?>
		<input type='hidden' name='sale' value='1'><?php /*結帳功能*/ ?>
		<input type='hidden' name='tempsale' value='0'><?php /*暫帳功能*/ ?>
		<?php
		}
		else{
		?>
		<input type='hidden' name='sale' value='<?php echo $orderpos['init']['sale']; ?>'><?php /*結帳功能*/ ?>
		<input type='hidden' name='tempsale' value='<?php echo $orderpos['init']['tempsale']; ?>'><?php /*暫帳功能*/ ?>
		<?php
		}
		?>
		<?php
		if(isset($initsetting['init']['membertype'])){
		?>
		<input type='hidden' name='membertype' value='<?php echo $initsetting['init']['membertype']; ?>'>
		<?php
		}
		else{
		?>
		<input type='hidden' name='membertype' value='1'>
		<?php
		}
		?>
	</div>
	<form id='setup' method="post" action='./seltab.php' style='display:none;'>
		<input type='hidden' name='machine' value='<?php echo $_POST['machine']; ?>'><?php /*主機機號*/ ?>
		<input type='hidden' name='submachine' value='<?php echo $_POST['submachine']; ?>'><?php /*副機機號*/ ?>
		<input type='hidden' name='machinetype' value='<?php echo $_POST['machinetype']; ?>'><?php /*機號(補充POS端變數)*/ ?>
		<input type='hidden' name='looptype' value='<?php echo $initsetting['init']['listprint']; ?>'><?php /*出單類別1>>出單2>>不出單3>>只出總單4>>只出標籤(補充POS端變數)*/ ?>
		<input type='hidden' name='consecnumber' value='<?php
		if($_POST['tabnum']!=''){
			$posttabdata=preg_split('/(;-;)/',$_POST['tabnum']);
			if(isset($posttabdata)&&sizeof($posttabdata)>=2){
				echo $posttabdata[1];
				$conn=sqlconnect('../database/sale','SALES_'.substr($posttabdata[0],0,6).'.db','','','','sqlite');
				$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$posttabdata[0].'" AND CONSECNUMBER="'.$posttabdata[1].'"';
				$listtitle=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$posttabdata[0].'" AND CONSECNUMBER="'.$posttabdata[1].'" AND DTLMODE="1" AND ((DTLTYPE="1" AND DTLFUNC="01") OR (DTLTYPE="3" AND DTLFUNC="02")) ORDER BY LINENUMBER ASC';
				$listdetail=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$posttabdata[0].'" AND CONSECNUMBER="'.$posttabdata[1].'" AND DTLMODE="1" AND ((DTLTYPE="1" AND DTLFUNC="01") OR (DTLTYPE="3" AND DTLFUNC="02")) AND ITEMCODE="autodis" ORDER BY LINENUMBER ASC';
				$autodis=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
			}
			else{
				echo '';
			}
		}
		else{
		}
		?>'><?php /*帳單編號(補充POS端變數)*/ ?>
		<?php
		if(isset($listtitle[0])&&$listtitle[0]['CUSTCODE']!=''&&$listtitle[0]['CUSTCODE']!=null){
			if(preg_match('/;-;/',$listtitle[0]['CUSTCODE'])){
				$templisttitle=preg_split('/;-;/',$listtitle[0]['CUSTCODE']);
			}
			else{
				$templisttitle[0]=$listtitle[0]['CUSTCODE'];
			}
			if($initsetting['init']['onlinemember']=='1'){
				$ajaxtype='online';
			}
			else{
				$ajaxtype='';
			}
			$PostData=array(
				'ajax'=>'',
				'company'=>$setup['basic']['company'],
				'memno'=>$templisttitle[0],
				'type'=>$ajaxtype
			);
			//print_r($PostData);
			$ch = curl_init();
			if(isset($initsetting['init']['onlinemember'])&&$initsetting['init']['onlinemember']=='1'){//網路會員
				curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php');
			}
			else{//本地會員
				curl_setopt($ch, CURLOPT_URL, $machinedata['orderpos']['serverip'].'/memberapi/getmemdata.ajax.php');
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			// Edit: prior variable $postFields should be $postfields;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
			$Result = curl_exec($ch);
			if(curl_errno($ch) !== 0) {
				print_r('cURL error when connecting to http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php : ' . curl_error('http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php'));
			}
			curl_close($ch);
			$memtel = json_decode($Result,1);
			//print_r($memtel);
		?>
		<input type='hidden' name='memtel' value='<?php echo $memtel[0]['tel']; ?>'><?php /*會員電話*/ ?>
		<input type='hidden' name='memno' value='<?php echo $memtel[0]['memno']; ?>'><?php /*會員編號*/ ?>
		<input type='hidden' name='memname' value='<?php echo $memtel[0]['name']; ?>'><?php /*會員名稱*/ ?>
		<input type='hidden' name='memberdis' value='<?php echo $memtel[0]['name']; ?>'><?php /*會員折扣*/ ?>
		<?php
		}
		else{
		?>
		<input type='hidden' name='memtel' value=''><?php /*會員電話*/ ?>
		<input type='hidden' name='memno' value=''><?php /*會員編號*/ ?>
		<input type='hidden' name='memname' value=''><?php /*會員名稱*/ ?>
		<input type='hidden' name='memberdis' value='0'><?php /*會員折扣*/ ?>
		<?php
		}
		?>
		<input type='hidden' name='saleno' value='<?php
		if(isset($posttabdata)&&sizeof($posttabdata)>=2){
			$conn=sqlconnect('../database/sale','SALES_'.substr($posttabdata[0],0,6).'.db','','','','sqlite');
			$sql='SELECT * FROM salemap WHERE bizdate="'.$posttabdata[0].'" AND consecnumber="'.$posttabdata[1].'"';
			$saleno=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
			if(isset($saleno[0])){
				echo $saleno[0]['saleno'];
			}
			else{
				echo '';
			}
		}
		else{
		}
		?>'><?php /*帳單流水號(補充POS端變數)*/ ?>
		<input type='hidden' name='bizdate' value='<?php
		if(isset($posttabdata)&&sizeof($posttabdata)>=2){
			echo $posttabdata[0];
		}
		else{
			echo $time['time']['bizdate'];
		}
		?>'><?php /*營業日(補充POS端變數)*/ ?>
		<input type='hidden' name='zcounter' value='<?php
		if(isset($posttabdata)&&sizeof($posttabdata)>=2){
			echo $listtitle[0]['ZCOUNTER'];
		}
		else{
			echo $time['time']['zcounter'];
		}
		?>'><?php /*營業日(補充POS端變數)*/ ?>
		<input type='hidden' name='listtype' value='<?php echo $_POST['listtype']; ?>'><?php /*帳單類別(補充POS端變數)*/ ?>
		<input type='hidden' name='invsalemoney' value='<?php
		if(isset($listtitle[0]['TAX5'])){
			echo $listtitle[0]['TAX5'];
		}
		else{
			echo '0';
		}
		?>'><?php /*發票金額(補充POS端變數)*/ ?>
		<input type='hidden' name='listtotal' value='0'><?php /*商品小計(包含自動優惠)(補充POS端變數)*/ ?>
		<input type='hidden' name='charge' value='0'><?php /*服務費(補充POS端變數)*/ ?>
		<input type='hidden' name='should' value='0'><?php /*應收金額(補充POS端變數)*/ ?>
		<input type='hidden' name='tempban' value=''><?php /*統編(補充POS端變數)*/ ?>
		<input type='hidden' name='tempbuytype' value='<?php
		$print=parse_ini_file('../database/printlisttag.ini',true);
		echo  $print['item']['tempbuytype'];
		?>'><?php /*1>>暫結時列印完整明細2>>暫結時列印加點明細(補充POS端變數)*/ ?>
		<input type='hidden' name='printclientlist' value='<?php
		if(isset($print['item']['printclientlist'])){
			echo $print['item']['printclientlist'];
		}
		else{
		}
		?>'><?php /*1>>暫結時列印完整明細2>>暫結時列印加點明細(補充POS端變數)*/ ?>
		<input type='hidden' name='total' value='0'><?php /*消費總金額(補充POS端變數)*/ ?>
		<input type='hidden' name='totalnumber' value='0'><?php /*消費總數量(補充POS端變數)*/ ?>
		<input type='hidden' name='autodis' value='<?php if(isset($autodis[0]['ITEMCODE']))echo (0-$autodis[0]['AMT']);else echo '0'; ?>'><?php /*自動優惠總金額*/ ?>
		<input type='hidden' name='autodiscontent' value='<?php if(isset($autodis[0]['ITEMCODE']))echo $autodis[0]['ITEMGRPCODE'];else echo ''; ?>'><?php /*自動優惠方式*/ ?>
		<input type='hidden' name='autodispremoney' value='<?php if(isset($autodis[0]['ITEMCODE']))echo $autodis[0]['ITEMGRPNAME'];else echo '0'; ?>'><?php /*自動優惠個別金額*/ ?>
		<input type='hidden' name='listdis1' value='0'><?php /*帳單折扣(補充POS端變數)*/ ?>
		<input type='hidden' name='listdis2' value='0'><?php /*帳單折讓(補充POS端變數)*/ ?>
		<input type='hidden' name='already' value='0'><?php /*已收金額*/ ?>
		<input type='hidden' name='cashmoney' value='0'><?php /*現金*/ ?>
		<input type='hidden' name='cash' value='0'><?php /*信用卡*/ ?>
		<input type='hidden' name='other' value='0'><?php /*其他付款*/ ?>
		<input type='hidden' name='otherstring' value=''><?php /*其他付款方式*/ ?>
		<input type='hidden' name='otherfix' value='0'><?php /*其他付款(不找零)*/ ?>
		<input type='hidden' name='notyet' value='0'><?php /*未收金額*/ ?>
		<input type='hidden' name='change' value='0'><?php /*找零金額*/ ?>
		<?php
		if($_POST['tabnum']!=''){
		?>
		<input type='hidden' name='tablenumber' value='<?php if(isset($listtitle[0]['TABLENUMBER']))echo $listtitle[0]['TABLENUMBER'];else echo $posttabdata[0]; ?>'><?php /*桌號*/ ?>
		<?php
		}
		else{
		?>
		<input type='hidden' name='tablenumber' value=''><?php /*桌號*/ ?>
		<?php
		}
		?>
		<input type='hidden' name='usercode' value='<?php echo $_POST['usercode']; ?>'><?php /*點餐人員代號*/ ?>
		<input type='hidden' name='username' value='<?php echo $_POST['username']; ?>'><?php /*點餐人員名稱*/ ?>
		<input type='hidden' name='invlist' value='<?php echo $initsetting['init']['invlist']; ?>'><?php /*1>>發票列印明細2>>發票列印總額(補充POS端變數)*/ ?>
		<input type='hidden' name='person1' value='<?php
		if(isset($listtitle[0])){
			echo $listtitle[0]['TAX6'];
		}
		else{
			echo '0';
		}
		?>'><?php /*人數1(補充POS端變數)*/ ?>
		<input type='hidden' name='person2' value='<?php
		if(isset($listtitle[0])){
			echo $listtitle[0]['TAX7'];
		}
		else{
			echo '0';
		}
		?>'><?php /*人數2(補充POS端變數)*/ ?>
		<input type='hidden' name='person3' value='<?php
		if(isset($listtitle[0])){
			echo $listtitle[0]['TAX8'];
		}
		else{
			echo '0';
		}
		?>'><?php /*人數3(補充POS端變數)*/ ?>
		<input type='hidden' name='v' value='<?php echo $_POST['v']; ?>'><?php /*作廢密碼*/ ?>
		<input type='hidden' name='p' value='<?php echo $_POST['p']; ?>'><?php /*報表密碼*/ ?>
		<input type='hidden' name='u' value='<?php echo $_POST['u']; ?>'><?php /*修改打卡紀錄密碼*/ ?>
		<input type='hidden' name='r' value='<?php echo $_POST['r']; ?>'><?php /*重印密碼*/ ?>
		<?php /*外送人員*/
		if(isset($listtitle[0]['CUSTGPCODE'])&&$listtitle[0]['CUSTGPCODE']!=''&&$listtitle[0]['CUSTGPCODE']!=NULL){
		?>
		<input type='hidden' name='mancode' value='<?php echo $listtitle[0]['CUSTGPCODE']; ?>'>
		<input type='hidden' name='manname' value='<?php echo $listtitle[0]['CUSTGPNAME']; ?>'>
		<?php
		}
		else{
		}
		?>
	</form>
	<form class='items' style='display:none;' method='post' action='#'>
		<?php
		if(isset($listtitle)&&isset($listtitle[0]['CONSECNUMBER'])&&isset($listdetail)&&sizeof($listdetail)>0&&isset($listdetail[0]['ITEMCODE'])){
			$totalqty=0;
			$totalamt=0;
			for($i=0;$i<sizeof($listdetail);$i=$i+2){
				if($listdetail[$i]['ITEMCODE']!='autodis'){
					$conn=sqlconnect('../database','menu.db','','','','sqlite');
					$sql='SELECT * FROM itemsdata WHERE inumber="'.intval($listdetail[$i]['ITEMCODE']).'"';
					$itemdata=sqlquery($conn,$sql,'sqlite');
					sqlclose($conn,'sqlite');

					echo '<div class="'.(floor($i/2)+1).'" id="item">';
					echo '<input type="hidden" name="templistitem[]">';//已出單標籤
					echo '<input type="hidden" name="linenumber[]" value="'.intval($listdetail[$i]['LINENUMBER']).'">';//資料庫順序(補充POS端變數)
					if(!isset($typename[intval($listdetail[$i]['ITEMGRPCODE'])]['subtype'])||$typename[intval($listdetail[$i]['ITEMGRPCODE'])]['subtype']=='0'){
						echo '<input type="hidden" name="order[]" value="'.(floor($i/2)+1).'">';//點餐列表順序(補充POS端變數)
					}
					else{//2021/7/7 套餐選項
						echo '<input type="hidden" name="order[]" value="－">';//點餐列表順序(補充POS端變數)
					}
					echo '<input type="hidden" name="typeno[]" value="'.intval($listdetail[$i]['ITEMGRPCODE']).'">';//類別代號
					echo '<input type="hidden" name="type[]" value="">';//類別名稱(補充POS端變數，故意留空)
					echo '<input type="hidden" name="no[]" value="'.intval($listdetail[$i]['ITEMCODE']).'">';//產品代號
					echo '<input type="hidden" name="personcount[]" value="'.$itemname[intval($listdetail[$i]['ITEMCODE'])]['personcount'].'">';//人數勾機(補充POS端變數)
					echo '<input type="hidden" name="needcharge[]" value="'.$itemname[intval($listdetail[$i]['ITEMCODE'])]['charge'].'">';//服務費計算(補充POS端變數)
					echo '<input type="hidden" name="dis1[]" value="'.((isset($itemname[intval($listdetail[$i]['ITEMCODE'])]['dis1']))?($itemname[intval($listdetail[$i]['ITEMCODE'])]['dis1']):('')).'">';//內用自動優惠(補充POS端變數)
					echo '<input type="hidden" name="dis2[]" value="'.((isset($itemname[intval($listdetail[$i]['ITEMCODE'])]['dis2']))?($itemname[intval($listdetail[$i]['ITEMCODE'])]['dis2']):('')).'">';//外帶自動優惠(補充POS端變數)
					echo '<input type="hidden" name="dis3[]" value="'.((isset($itemname[intval($listdetail[$i]['ITEMCODE'])]['dis3']))?($itemname[intval($listdetail[$i]['ITEMCODE'])]['dis3']):('')).'">';//外送自動優惠(補充POS端變數)
					echo '<input type="hidden" name="dis4[]" value="'.((isset($itemname[intval($listdetail[$i]['ITEMCODE'])]['dis4']))?($itemname[intval($listdetail[$i]['ITEMCODE'])]['dis4']):('')).'">';//自取自動優惠(補充POS端變數)
					echo '<input type="hidden" name="name[]" value="'.$listdetail[$i]['ITEMNAME'].'">';//產品名稱
					echo '<input type="hidden" name="name2[]" value="">';//產品名稱2(補充POS端變數，故意留空)
					echo '<input type="hidden" name="isgroup[]" value="'.$itemdata[0]['isgroup'].'">';//是否為套餐(補充POS端變數)
					echo '<input type="hidden" name="childtype[]" value="'.$itemdata[0]['childtype'].'">';//是否為套餐選項(補充POS端變數)
					echo '<input type="hidden" name="mname1[]" value="'.$listdetail[$i]['UNITPRICELINK'].'">';//價格名稱
					echo '<input type="hidden" name="mname2[]" value="">';//價格名稱2(補充POS端變數，故意留空)
					echo '<input type="hidden" name="insaleinv[]" value="';if(isset($itemname[intval($listdetail[$i]['ITEMCODE'])]['insaleinv']))echo $itemname[intval($listdetail[$i]['ITEMCODE'])]['insaleinv'];else echo '';echo '">';//計算發票金額(補充POS端變數)
					echo '<input type="hidden" name="unitprice[]" value="'.$listdetail[$i]['UNITPRICE'].'">';//單價
					$tasteno='';
					$tastesname='';
					$tasteprice='';
					$tastenumber='';
					$tastemoney=0;
					for($t=1;$t<=10;$t++){
						if($listdetail[$i]['SELECTIVEITEM'.$t]!=null&&$listdetail[$i]['SELECTIVEITEM'.$t]!=''){
							if(strlen($tasteno)>0){
								$tasteno.=','.intval(substr($listdetail[$i]['SELECTIVEITEM'.$t],0,strlen($listdetail[$i]['SELECTIVEITEM'.$t])-1));
								$tastesname.=','.$tastename[intval(substr($listdetail[$i]['SELECTIVEITEM'.$t],0,strlen($listdetail[$i]['SELECTIVEITEM'.$t])-1))]['name1'];
								$tasteprice.=','.$tastename[intval(substr($listdetail[$i]['SELECTIVEITEM'.$t],0,strlen($listdetail[$i]['SELECTIVEITEM'.$t])-1))]['money'];
								$tastenumber.=','.substr($listdetail[$i]['SELECTIVEITEM'.$t],-1);
								$tastemoney=floatval($tastemoney)+(floatval($tastename[intval(substr($listdetail[$i]['SELECTIVEITEM'.$t],0,strlen($listdetail[$i]['SELECTIVEITEM'.$t])-1))]['money'])*floatval(substr($listdetail[$i]['SELECTIVEITEM'.$t],-1)));
							}
							else{
								$tasteno=intval(substr($listdetail[$i]['SELECTIVEITEM'.$t],0,strlen($listdetail[$i]['SELECTIVEITEM'.$t])-1));
								$tastesname=$tastename[intval(substr($listdetail[$i]['SELECTIVEITEM'.$t],0,strlen($listdetail[$i]['SELECTIVEITEM'.$t])-1))]['name1'];
								$tasteprice=$tastename[intval(substr($listdetail[$i]['SELECTIVEITEM'.$t],0,strlen($listdetail[$i]['SELECTIVEITEM'.$t])-1))]['money'];
								$tastenumber=substr($listdetail[$i]['SELECTIVEITEM'.$t],-1);
								$tastemoney=floatval($tastename[intval(substr($listdetail[$i]['SELECTIVEITEM'.$t],0,strlen($listdetail[$i]['SELECTIVEITEM'.$t])-1))]['money'])*floatval(substr($listdetail[$i]['SELECTIVEITEM'.$t],-1));
							}
						}
						else{
							break;
						}
					}
					echo '<input type="hidden" name="taste1[]" value="'.$tasteno.'">';//使用的加料備註代號(陣列字串)
					echo '<input type="hidden" name="taste1name[]" value="'.$tastesname.'">';//使用的加料備註名稱(陣列字串)
					echo '<input type="hidden" name="taste1price[]" value="'.$tasteprice.'">';//使用的加料備註單價(陣列字串)
					echo '<input type="hidden" name="taste1number[]" value="'.$tastenumber.'">';//使用的加料備註數量(陣列字串)
					echo '<input type="hidden" name="taste1money[]" value="'.$tastemoney.'">';//使用的加料備註小計
					$itemmoney=($listdetail[$i]['AMT']/$listdetail[$i]['QTY']);
					echo '<input type="hidden" name="money[]" value="'.$itemmoney.'">';//1個(含加料備註)的金額
					echo '<input type="hidden" name="discount[]" value="0">';//單品折扣金額(補充POS端變數，故意留空)
					echo '<input type="hidden" name="discontent[]" value="">';//折扣方式(補充POS端變數，故意留空)
					echo '<input type="hidden" name="number[]" value="'.$listdetail[$i]['QTY'].'">';//數量
					$totalqty=floatval($totalqty)+floatval($listdetail[$i]['QTY']);
					echo '<input type="hidden" name="subtotal[]" value="'.$listdetail[$i]['AMT'].'">';//小計(含加料備註)
					$totalamt=floatval($totalamt)+floatval($listdetail[$i]['AMT']);
					echo '<input type="hidden" name="itemdis[]" value="'.((isset($itemname[intval($listdetail[$i]['ITEMCODE'])]['itemdis']))?($itemname[intval($listdetail[$i]['ITEMCODE'])]['itemdis']):('')).'">';//允許單品折扣(補充POS端變數)
					echo '<input type="hidden" name="listdis[]" value="'.((isset($itemname[intval($listdetail[$i]['ITEMCODE'])]['listdis']))?($itemname[intval($listdetail[$i]['ITEMCODE'])]['listdis']):('')).'">';//允許帳單折扣(補充POS端變數)
					echo '<input type="hidden" name="seq[]" value="'.$itemdata[0]['frontsq'].'">';//產品排序
					//2021/7/7 贈與點數規則
					echo '<input type="hidden" name="getpointtype[]" value="'.((isset($listdetail[$i]['TAXCODE4']))?($listdetail[$i]['TAXCODE4']):('1')).'">';
					echo '<input type="hidden" name="initgetpoint[]" value="'.((isset($listdetail[$i+1]['TAXCODE3']))?($listdetail[$i+1]['TAXCODE3']):('0')).'">';
					echo '<input type="hidden" name="getpoint[]" value="'.((isset($listdetail[$i]['TAXCODE5']))?($listdetail[$i]['TAXCODE4']):('0')).'">';
					echo '</div>';
				}
				else{
				}
			}
		}
		else{
		}
		?>
	</form>
	<input type='hidden' class='itemtype' value=''>
	<?php
	if(isset($setup['basic']['company'])&&$setup['basic']['company']!=''&&(isset($setup['basic']['story'])&&$setup['basic']['story']!='')){
	?>
		<input type='hidden' class='type' value='items'>
		<div id='title'>
			<div class='return' style='height:50px;text-align:center;z-index:1;position: absolute;left:5px;'>
				<img src='./img/return.png?<?php echo date('YmdHis'); ?>' style='filter:invert(100%);width:16px;height:16px;padding: 3px;margin: 14px 14px;'>
			</div>
			<?php
			/*$conn=sqlconnect('localhost','papermanagement','paperadmin','1qaz2wsx','utf-8','mysql');
			$sql='SELECT deptname,dept,companyname FROM userlogin WHERE company="'.$setup['basic']['company'].'" AND dept="'.$setup['basic']['story'].'"';
			$depdata=sqlquery($conn,$sql,'mysql');
			sqlclose($conn,'mysql');*/
			//$conn=sqlconnect('../management/menudata/'.$setup['basic']['company'].'/'.$setup['basic']['story'],'menu.db','','','','sqlite');
			$conn=sqlconnect('../database','menu.db','','','','sqlite');
			//$sql='SELECT DISTINCT fronttype FROM itemsdata';
			$sql='SELECT DISTINCT fronttype,typeseq FROM itemsdata ORDER BY CAST(typeseq AS INT) ASC,CAST(fronttype AS INT) ASC';
			$type=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
			
			//$typename=parse_ini_file('../management/menudata/'.$setup['basic']['company'].'/'.$setup['basic']['story'].'/'.$setup['basic']['company'].'-front.ini',true);
			
			echo '<div id="titlename" style="width:calc(100% - 110px);overflow:hidden;height:50px;position: fixed;top:5px;left:55px;z-index:0;text-align:center;font-size:30px;line-height:50px;font-weight:bold;">'.$setup['basic']['storyname'].'</div>';
			echo '<div class="needsclick" id="getbasic" style="width:calc(100% - 110px);overflow:hidden;height:50px;position: fixed;top:5px;left:55px;z-index:0;text-align:center;font-size:30px;line-height:50px;font-weight:bold;"></div>';
			//echo '<div id="titlename" style="width:calc(100% - 110px);overflow:hidden;height:50px;position: fixed;top:5px;left:55px;z-index:0;text-align:center;font-size:30px;line-height:50px;font-weight:bold;">'.$depdata[0]['companyname'].$depdata[0]['deptname'].'</div>';
			?>
			<div class="setbutton" id='setbutton'>
				<div class='funkey'></div>
				<div class='funkey'></div>
				<div class='funkey'></div>
			</div>
		</div>
		<div id='content' style='width:100%;height:calc(100% - 120px);overflow:auto;margin: 60px 0 0 0;padding:60px 0 0 0;position: relative;'>
			<div id='types' style='width:100%;height:50px;overflow-x:auto;overflow-y:hidden;padding:5px 0;margin:0;position:fixed;top:60px;left:0;background-color:#ffffff;'>
				<div style='width:max-content;overflow:hidden;padding-right:5px;'>
				<?php
				$targettype='';
				$typelist='';
				$typearray=[];
				if(isset($type)&&isset($type[0]['fronttype'])){
					for($i=0;$i<sizeof($type);$i++){
						if(isset($typename[$type[$i]['fronttype']])&&$typename[$type[$i]['fronttype']]['state']=='1'&&(!isset($typename[$type[$i]['fronttype']]['subtype'])||$typename[$type[$i]['fronttype']]['subtype']=='0')&&(!isset($typename[$type[$i]['fronttype']]['webvisible'])||$typename[$type[$i]['fronttype']]['webvisible']=='1')){//2020/3/27 增加過濾"套餐選項"類別
							if($typelist!=''){
								$typelist.=',';
							}
							else{
							}
							$typelist.='"'.$type[$i]['fronttype'].'"';
							$typearray[$type[$i]['fronttype']]='<div class="type" name="'.$type[$i]['fronttype'].'"';
							//echo '<div class="type" name="'.$type[$i]['fronttype'].'"';
							if($targettype!=''){
							}
							else{
								$targettype=$type[$i]['fronttype'];
								$typearray[$type[$i]['fronttype']] .= 'id="checked" ';
								//echo 'id="checked" ';
							}
							$typearray[$type[$i]['fronttype']] .= '><input type="hidden" name="type" value="'.$type[$i]['fronttype'].'">'.$typename[$type[$i]['fronttype']]['name1'].'</div>';
							//echo '><input type="hidden" name="type" value="'.$type[$i]['fronttype'].'">'.$typename[$type[$i]['fronttype']]['name1'].'</div>';
						}
						else{
						}
					}
				}
				else{
				}

				$conn=sqlconnect('../database','menu.db','','','','sqlite');
				$sql='SELECT inumber,fronttype,imgfile FROM itemsdata WHERE (state!=0 OR state IS NULL) AND fronttype IN ('.$typelist.') ORDER BY CAST(typeseq AS INT) ASC,CAST(fronttype AS INT) ASC,CAST(frontsq AS INT) ASC';//2021/6/1 撈出所有品項
				//echo $sql;
				$item=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				
				$itemarray=[];
				if(isset($item)&&isset($item[0]['inumber'])){
					for($i=0;$i<sizeof($item);$i++){
						if(isset($itemname[$item[$i]['inumber']]['webvisible'])){
				
						}
						else{
							$itemname[$item[$i]['inumber']]['webvisible']='1';
						}
						if(isset($itemname[$item[$i]['inumber']])&&$itemname[$item[$i]['inumber']]['state']=='1'&&$itemname[$item[$i]['inumber']]['webvisible']=='1'){
							if(isset($itemarray[$item[$i]['fronttype']])){
							}
							else{
								$itemarray[$item[$i]['fronttype']]='<div class="typelabel" name="itemtype">'.$typename[$item[$i]['fronttype']]['name1'].'</div><div class="itemsbox">';
							}
							$itemarray[$item[$i]['fronttype']] .= '<div class="item"><input type="hidden" name="item" value="'.$item[$i]['inumber'].'"><div id="itemcontain"><div id="itemimg">';
							//http://api.tableplus.com.tw/outposandorder/menudata
							if($item[$i]['imgfile']!=''&&file_exists('http://api.tableplus.com.tw/outposandorder/menudata/'.$setup['basic']['company'].'/'.$setup['basic']['story'].'/img/'.$item[$i]['imgfile'])){
								$itemarray[$item[$i]['fronttype']] .= '<img class="lazy" data-src="http://api.tableplus.com.tw/outposandorder/menudata/'.$setup['basic']['company'].'/'.$setup['basic']['story'].'/img/'.$item[$i]['imgfile'].'">';
							}
							else if(file_exists('http://api.tableplus.com.tw/outposandorder/menudata/'.$setup['basic']['company'].'/'.$setup['basic']['story'].'/img/empty.png')){
								$itemarray[$item[$i]['fronttype']] .= '<img class="lazy" data-src="http://api.tableplus.com.tw/outposandorder/menudata/'.$setup['basic']['company'].'/'.$setup['basic']['story'].'/img/empty.png">';
								//echo '<img src="./img/empty.png">';
							}
							else if($item[$i]['imgfile']!=''&&file_exists('../database/img/'.$item[$i]['imgfile'])){
								$itemarray[$item[$i]['fronttype']] .= '<img class="lazy" data-src="../database/img/'.$item[$i]['imgfile'].'">';
							}
							else if(file_exists('../database/img/empty.png')){
								$itemarray[$item[$i]['fronttype']] .= '<img class="lazy" data-src="../database/img/empty.png">';
								//echo '<img src="./img/empty.png">';
							}
							else if(file_exists('./img/empty.png')){
								$itemarray[$item[$i]['fronttype']] .= '<img class="lazy" data-src="./img/empty.png">';
							}
							else{
								$itemarray[$item[$i]['fronttype']] .= '';
							}
							$itemarray[$item[$i]['fronttype']] .= '</div><div id="itemtext">'.$itemname[$item[$i]['inumber']]['name1'];
							if(strlen($itemname[$item[$i]['inumber']]['name2'])>0){
								$itemarray[$item[$i]['fronttype']] .= '<span id="name2">'.$itemname[$item[$i]['inumber']]['name2'].'</span>';
							}
							else{
							}
							if($itemname[$item[$i]['inumber']]['money1']!=0&&$itemname[$item[$i]['inumber']]['money1']!=''){
								$itemarray[$item[$i]['fronttype']] .= '<span id="money">＄'.number_format($itemname[$item[$i]['inumber']]['money1']).'</span>';
							}
							else{
							}
							$itemarray[$item[$i]['fronttype']] .= '</div></div></div>';
							if(isset($item[$i+1]['fronttype'])&&$item[$i]['fronttype']==$item[($i+1)]['fronttype']){
							}
							else{
								$itemarray[$item[$i]['fronttype']] .= '</div>';
							}
						}
						else if(isset($item[$i+1]['fronttype'])&&$item[$i]['fronttype']!=$item[($i+1)]['fronttype']){
							if(isset($itemarray[$item[$i]['fronttype']])){
								$itemarray[$item[$i]['fronttype']] .= '</div>';
							}
							else{
								//$itemarray[$item[$i]['fronttype']]='<div class="typelabel" name="itemtype">'.$typename[$item[$i]['fronttype']]['name1'].'</div><div class="itemsbox">';
							}
						}
					}
					if(isset($itemname[$item[$i-1]['inumber']])&&$itemname[$item[$i-1]['inumber']]['state']=='1'&&$itemname[$item[$i-1]['inumber']]['webvisible']=='1'){
					}
					else{
						if(isset($itemarray[$item[$i-1]['fronttype']])){
							$itemarray[$item[$i-1]['fronttype']] .= '</div>';
						}
						else{
							//$itemarray[$item[$i]['fronttype']]='<div class="typelabel" name="itemtype">'.$typename[$item[$i]['fronttype']]['name1'].'</div><div class="itemsbox">';
						}
					}
				}
				else{
				}

				if(sizeof($typearray)>0){
					foreach($typearray as $type=>$htmlstring){
						if(isset($itemarray[$type])){
							echo $htmlstring;
						}
						else{
						}
					}
				}
				else{
				}

				/*$targettype='';
				if(isset($type)&&isset($type[0]['fronttype'])){
					for($i=0;$i<sizeof($type);$i++){
						if(isset($typename[$type[$i]['fronttype']])&&$typename[$type[$i]['fronttype']]['state']=='1'&&(!isset($typename[$type[$i]['fronttype']]['subtype'])||$typename[$type[$i]['fronttype']]['subtype']=='0')){//2020/3/27 增加過濾"套餐選項"類別
							echo '<div class="type" ';
							if($targettype!=''){
							}
							else{
								$targettype=$type[$i]['fronttype'];
								echo 'id="checked" ';
							}
							echo '><input type="hidden" name="type" value="'.$type[$i]['fronttype'].'">'.$typename[$type[$i]['fronttype']]['name1'].'</div>';
						}
						else{
						}
					}
				}
				else{
				}*/
				?>
				</div>
			</div>
			<div id='items' style='width:100%;height:calc(100% - 180px);overflow:auto;position: fixed;top: 120px;'>
				<?php
				if(sizeof($itemarray)>0){
					foreach($itemarray as $htmlstring){
						echo $htmlstring;
					}
				}
				else{
				}

				/*$conn=sqlconnect('../database','menu.db','','','','sqlite');
				$sql='SELECT inumber FROM itemsdata WHERE fronttype="'.$targettype.'" ORDER BY frontsq ASC';
				$item=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				if(isset($item)&&isset($item[0]['inumber'])){
					for($i=0;$i<sizeof($item);$i++){
						if(isset($itemname[$item[$i]['inumber']])&&$itemname[$item[$i]['inumber']]['state']=='1'){
							echo '<div class="item"><input type="hidden" name="item" value="'.$item[$i]['inumber'].'">'.$itemname[$item[$i]['inumber']]['name1'].'</div>';
						}
						else{
						}
					}
				}
				else{
				}*/
				?>
			</div>
		</div>
		<div id='keybox' style='width:calc(100% - 10px);height:50px;margin:0;padding:5px;position: fixed;bottom: 0;z-index:1;background-color:#ffffff;box-shadow: rgba(0, 0, 0, 0.2) 0px 0px 8px;'>
			<!-- <div class='funkey1' id='' style='width:calc(50% - 5px);height:50px;float:left;text-align:center;'>
			</div> -->
			<div class='funkey2' style='width:calc(100% - 5px);height:50px;float:left;text-align:center;'>
				<div id='list' style="width:100%;height:50px;line-height:50px;float:left;z-index:1;margin: 0 auto;position: relative;font-weight:bold;font-size:30px;text-align:center;">下一步</div>
				<div id='tempsale' style="display:none;width:calc(50% - 5px);height:50px;line-height:50px;float:right;z-index:1;margin: 0 auto;position: relative;font-weight:bold;font-size:30px;text-align:center;">暫帳</div>
				<?php
				if(isset($listdetail[0]['ITEMCODE'])){
					echo '<div style="width:100px;height:44px;line-height:22px;padding-right:5px;text-align:right;margin:0;position: fixed;right: 0;bottom:5px;font-weight:bold;font-size:20px;color:rgba(26, 26, 26, 0.5);z-index:0;" id="point">';
					echo 'x'.$totalqty;
					if(isset($depunit)&&isset($depunit['webunit']['qtyunit'])&&$depunit['webunit']['qtyunit']!='')echo $depunit['webunit']['qtyunit'];else echo '項';
					echo '<br>';
					if(isset($depunit)&&isset($depunit['webunit']['moneypreunit'])&&$depunit['webunit']['moneypreunit']!='')echo $depunit['webunit']['moneypreunit'];else echo '＄';
					echo $totalamt;
					if(isset($depunit)&&isset($depunit['webunit']['moneysufunit'])&&$depunit['webunit']['moneysufunit']!='')echo $depunit['webunit']['moneysufunit'];else echo '元';
				}
				else{
					echo '<div style="width:100px;height:44px;line-height:22px;padding-right:5px;text-align:right;margin:0;position: fixed;right: 0;bottom:5px;font-weight:bold;font-size:20px;color:rgba(26, 26, 26, 0.5);z-index:0;" id="point">';
				}
				?></div>
			</div>
		</div>
	<?php
	}
	else{
	}
	?>
	<div class='salepay' style='width:calc(90vw - 10px);border:1px solid reb(74,74,74,0.5);border-radius:10px;position:fixed;top:5vh;left:5vw;z-index:2024;padding:5px;background-color:#ffffff;display:none;overflow:hidden;'>
		<div style="margin:10px 0;text-align:center;font-weight:bold;font-size:20px;">付款</div>
		<table>
			<tr>
				<td style='white-space:nowrap;font-family:Microsoft JhengHei,MingLiU;font-size:30px;'>
					應收
				</td>
				<td>
					<input type='text' style='background-color:rgb(200,200,200,0.5);' name='notyet' value='0' readonly>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<select name='paytype'>
						<option value='cashmoney' selected>現金</option>
						<?php
						if(isset($initsetting['init']['openpay'])&&$initsetting['init']['openpay']=='1'){//2020/4/13 其他付款開關
							if(file_exists('../database/otherpay.ini')){
								$otherpay=parse_ini_file('../database/otherpay.ini',true);
								for($i=1;$i<sizeof($otherpay);$i++){
									if((!isset($otherpay['item'.$i]['type'])||$otherpay['item'.$i]['type']=='1')&&(!isset($otherpay['item'.$i]['fromdb'])||$otherpay['item'.$i]['fromdb']!='member')&&(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']!='memberpoint')&&$otherpay['item'.$i]['price']=='1'){//2020/4/9 信用卡、會員點數與儲值金暫時排除在外，並只使用 找零 與 面額為1 的付款方式
										echo '<option value="item'.$i.'">'.$otherpay['item'.$i]['name'].'</option>';
									}
									else{
									}
								}
							}
							else{
							}
						}
						else{
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'><input type='tel' name='money' placeholder="請輸入金額" value=''></td>
			</tr>
			<tr>
				<td style='white-space:nowrap;font-family:Microsoft JhengHei,MingLiU;font-size:30px;'>
					找零
				</td>
				<td>
					<input type='text' style='background-color:rgb(200,200,200,0.5);' name='change' value='0' readonly>
				</td>
			</tr>
			<tr>
				<td colspan='2'><button id='check'>結帳</button></td>
			</tr>
		</table>
	</div>
	<div class='cremem' style='width:calc(90vw - 10px);border:1px solid reb(74,74,74,0.5);border-radius:10px;position:fixed;top:5vh;left:5vw;z-index:2024;padding:5px;background-color:#ffffff;display:none;overflow:hidden;'>
		<div style="margin:10px 0;text-align:center;font-weight:bold;font-size:20px;">新增會員</div>
		<table>
			<tr>
				<td>
					<input type='text' style='background-color:rgb(200,200,200,0.5);text-align:left;' id='memtel' value='' readonly>
				</td>
			</tr>
			<tr>
				<td>
					<input type='text' id='memname' placeholder="會員姓名(必填)" style='text-align:left;' value=''>
				</td>
			</tr>
			<tr>
				<td>
					<input type='text' id='memaddress' placeholder="會員地址(選填)" style='text-align:left;' value=''>
				</td>
			</tr>
			<tr>
				<td colspan='2'><button id='check'>新增</button></td>
			</tr>
		</table>
	</div>
	<div class='message1' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:30vh;left:5vw;z-index:2024;padding:5px;background-color:#ffffff;display:none;overflow:hidden;'>
		<div style="margin:10px 0;text-align:center;">系統訊息</div>
		<div style='width:100%;text-align:center;' id='text'></div>
		<div style='width:100%;text-align:center;overflow:hidden;'><div id='check' style='width:100px;height:35px;line-height:35px;margin:25px auto 15px auto;text-align:center;font-size:16px;border:2px solid #898989;border-radius:5px;cursor: pointer;'>確認</div></div>
	</div>
	<div class='message2' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:30vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;'>
		<input type='hidden' name='msgtype' value=''>
		<div style="margin:10px 0;text-align:center;">系統訊息</div>
		<div style='width:100%;text-align:center;' id='text'></div>
		<div style='width:220px;text-align:center;overflow:hidden;margin:25px auto 15px auto;'>
			<div style='width:calc(100px - 4px);height:35px;line-height:35px;margin:0 5px;float:left;text-align:center;font-size:16px;border:2px solid #898989;border-radius:5px;cursor: pointer;' id='check'>確認</div>
			<div style='width:calc(100px - 4px);height:35px;line-height:35px;margin:0 5px;float:left;text-align:center;font-size:16px;border:2px solid #898989;border-radius:5px;cursor: pointer;background-color:rgb(0,0,0,0.2);' id='cancel'>取消</div>
		</div>
	</div>
	<div class='message4' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:30vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;'>
		<div style="margin:10px 0;text-align:center;">系統訊息</div>
		<div style='width:100%;text-align:center;' id='text'></div>
		<div style='width:220px;margin:25px auto 15px auto;text-align:center;overflow:hidden;'>
			<div id='check' style="width:100px;height:35px;line-height:35px;margin:0 auto;text-align:center;font-size:16px;border:2px solid #898989;border-radius:5px;cursor: pointer;">確認</div>
		</div>
	</div>
	<div class='setwin' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:15vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;'>
		<div style="margin:10px 0;text-align:center;font-weight:bold;font-size:20px;">功能列表</div>
		<!-- <div style='width:80%;margin:20px auto;'>
			<div style='width:100%;height:35px;line-height:35px;margin:0 auto;text-align:center;background-color:<?php echo $init['init']['topcolor']; ?>;color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='weborder'>網路訂單</div>
		</div>
		<div style='width:80%;margin:20px auto;'>
			<div style='width:100%;height:35px;line-height:35px;margin:0 auto;text-align:center;background-color:<?php echo $init['init']['topcolor']; ?>;color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='temporder'>暫結清單</div>
		</div>
		<div style='width:80%;margin:5px auto 20px auto;'>
			<div style='width:100%;height:35px;line-height:35px;margin:0 auto;text-align:center;background-color:<?php echo $init['init']['topcolor']; ?>;color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='vieworder'>瀏覽帳單</div>
		</div> -->
		<div style='width:80%;margin:20px auto;'>
			<div style='width:100%;height:35px;line-height:35px;margin:0 auto;text-align:center;border:2px solid #898989;border-radius:5px;font-size:16px;cursor: pointer;' id='logout'>切換人員</div>
		</div>
	</div>
	<div class='viewbasic' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:15vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;'>
		<table id='basicdata' style='width:100%;border-collapse: collapse;font-size:20px;'>
		</table>
	</div>
	<div class='detail' style='width:100%;height:calc(100% - 65px);position:fixed;top:65px;left:0;overflow:hidden;font-size:20px;display:none;background-color:#ffffff;z-index:2'>
		<div id='data' style='width:calc(100% - 10px);height:calc(100% - 70px);padding:5px;overflow:auto;position: relative;'>
		</div>
		<div id='footer' style='width:calc(100% - 10px);height:50px;margin:0;padding:5px;position: fixed;bottom: 0;background-color: #ffffff;box-shadow: rgba(0, 0, 0, 0.2) 0px 0px 8px;'>
			<div class='send' id='' style='width:calc(50% - 7px);height:calc(50px - 2px);line-height:50px;float:left;text-align:center;font-size:30px;font-weight:bold;text-align:center;cursor: pointer;border:1px solid #898989;border-radius:5px;'>確定</div>
			<div style="width:calc(50% - 5px);height:50px;float:left;text-align:center;font-weight:bold;font-size:30px;text-align:center;position: relative;">
				<div style="position: absolute; top: 0; left: 10px; font-size: 18px; height: max-content;">小計</div>
				<div class="money" id="list" style="width: 100%; line-height:50px; position: absolute; top: 0; left: 0;"></div>
			</div>
			<!-- <div class='money' id='list' style='width:calc(50% - 5px);height:50px;line-height:50px;float:left;text-align:center;font-weight:bold;font-size:30px;text-align:center;'></div> -->
		</div>
	</div>
	<div class='modal' style="position:fixed;top:0;left:0;height:100vh;width:100vw;background-color:#4a4a4a;opacity:0.5;display:none;z-index:2019;">
	</div>
	<div class='orderlist' id='funbox' style='width:calc(90vw - 10px);border-radius:10px;position:fixed;top:30vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;'>
		<div class='datatemp' style="display:none;">
			<input type='hidden' id='index' value=''>
			<input type='hidden' id='itemseq' value=''>
			<input type='hidden' id='itemno' value=''>
			<input type='hidden' id='unitpricelink' value=''>
			<input type='hidden' id='unitprice' value=''>
			<input type='hidden' id='qty' value=''>
			<input type='hidden' id='tasteno' value=''>
			<input type='hidden' id='tastemoney' value=''>
			<input type='hidden' id='tastenumber' value=''>
			<input type='hidden' id='subtotal' value=''>
		</div>
		<div>
			<div style="margin:10px 0;text-align:center;">提示</div>
			<div style="text-align:center;">是否要修改或刪除產品？</div>
			<div style="width:220px;margin:25px auto 15px auto;text-align:center;overflow:hidden;">
				<div class='delete' style="width:calc(100px - 4px);height:35px;line-height:35px;margin:0 5px;float:left;text-align:center;font-size:16px;border:2px solid #898989;border-radius:5px;cursor: pointer;">刪除</div>
				<div class='edit' style="width:calc(100px - 4px);height:35px;line-height:35px;margin:0 5px;float:left;text-align:center;font-size:16px;border:2px solid #898989;border-radius:5px;cursor: pointer;">修改</div>
			</div>
		</div>
	</div>
	<div class='subtaste' style='width:100%;height:100%;position:fixed;top:100%;left:0;z-index:2024;background-color:#ffffff;display:none;overflow:hidden;'>
		<input type='hidden' class='trid' value=''>
		<input type='hidden' class='seq' value=''>
		<div class='cancel' style='height:50px;text-align:center;z-index:1;position: absolute;padding:5px;background-color:#f0f0f0;'>
			<img src='./img/return.png?<?php echo date('YmdHis'); ?>' style='filter:invert(100%);width:16px;height:16px;padding: 3px;margin: 14px 14px;'>
		</div>
		<div class='tastetitle' style='width:calc(100% - 50px);height:60px;line-height:60px;margin-left:50px;font-size:45px;text-align:center;font-weight:bold;background-color:#f0f0f0;'>
			
		</div>
		<div class='tastecontent' style='width:100%;height:calc(100% - 120px);background-color:#ffffff;overflow:auto;'>
			
		</div>
		<div class='tastefunbox' style='width:calc(100% - 10px);height:50px;text-align:center;font-weight:bold;padding:5px;background-color:#ffffff;color:#000000;box-shadow: rgba(0, 0, 0, 0.2) 0px 0px 8px;'>
			<div class='save' style='width: calc(50% - 7px); height: 50px; line-height: 50px; float: left; z-index: 1; position: relative; font-weight: bold; font-size: 30px; text-align: center;cursor: pointer;border: 1px solid #898989; border-radius: 5px;'>儲存</div>
			<div style='width: calc(50% - 5px); height: 50px; float: left; z-index: 1; position: relative; font-weight: bold; font-size: 30px; text-align: center;cursor: pointer;'>
				<div style='position: absolute; top: 0; left: 10px; font-size: 18px; height: max-content;'>小計</div>
				<div class='money' id='list' style='width: 100%; line-height:50px; position: absolute; top: 0; left: 0;'></div>
			</div>
		</div>
	</div>
</body>
</html>