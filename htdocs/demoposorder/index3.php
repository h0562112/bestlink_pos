<!doctype html>
<html lang="en">
<head>
	<?php
	include_once './lib/dbTool.inc.php';
	if(file_exists('setup.ini')){
		$content=parse_ini_file('setup.ini',true);
	}
	else{
		$content['basic']['company']='testorder';
		$content['basic']['dep']='';
	}
	?>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="./lib/css/jquery-ui.css">
	<script src='./lib/js/jquery-1.12.4.js'></script>
	<script src="./lib/js/jquery-ui.js"></script>
	<!-- <script src='http://malsup.github.com/jquery.cycle2.js'></script> -->
	<script src="./jquery.fly.min.js"></script>
	
	<!-- <link rel="stylesheet" href="pager.css"> -->
	<title>茶葉銷售機DEMO</title>
	<style>
		body {
			width:1080px;
			height:1920px;
			padding:0;
			margin:0;
			overflow:hidden;
			font-family: Arial,Microsoft JhengHei,sans-serif;
		}
		.logo {
			width:1080px;
			height:370px;
		}
		#sidebar {
			width:250px;
			height:978px;
			float:left;
			overflow:hidden;
		}
		#main {
			width:680px;
			height:768px;
			float:left;
			overflow:hidden;
			padding:105px 75px 105px 75px;
			position:relative;
		}
		.content {
			width:100%;
			height:100%;
			float:left;
			vertical-align:baseline;
		}
		.foodbox {
			width:100%;
			height:100%;
		}
		.food {
			width:calc(calc(100% - 50px) / 2);
			height:calc(calc(100% - 50px) / 2);
			float:left;
		}
		.bottom {
			margin-bottom:50px;
		}
		.right {
			margin-right:50px;
		}
		.fdimgbox {
			width:315px;
			height:315px;
			position: relative;
		}
		.foodimg {
			width:100%;
			height:100%;
		}
		.plus {
			width:65px;
			height:65px;
			position: absolute;
			bottom:15px;
			right:15px;
			font-size:60px;
			text-align:center;
		}
		.foodtitle {
			width:100%;
			height:calc(100% - 315px);
			font-size:37px;
			color:#3e3a39;
			text-align:center;
		}
		.function {
			width:330px;
			height:660px;
			margin-right:10px;
			float:right;
		}
		.fun {
			width:330px;
			height:327px;
			float:left;
		}
		.funimgbox {
			width:330px;
			height:285px;
			border:1px #000000 solid;
			float:left;
			position:relative;
		}
		.point,
		.temppoint {
			width:64px;
			height:64px;
			position:absolute;
			top:0;
			left:30px;
			color:#ffffff;
			font-size:55px;
			text-align:center;
		}
		.temppoint {
			display:none;
		}
		.funimg {
			width:330px;
			height:285px;
		}
		.funtitle {
			width:330px;
			font-size:30px;
			text-align:center;
			float:left;
			border:1px #000000 solid;
		}
		.chose #img,
		.order #orimg,
		.editbox #orimg {
			width:605px;
			height:605px;
			float:left;
		}
		.chose #name,
		.order #orname,
		.editbox #orname {
			width:605px;
			margin:40px 0;
			font-size:55px;
			text-align:center;
			color:#3e3a39;float:left;
		}
		.chose .money,
		.order #ortaste,
		.editbox #ortaste {
			width:100%;
			height:580px;
			padding:25px 0 0 0;
		}
		.chose .money div {
			width:100%;
			height:123px;
			text-align:center;
			margin:0 0 25px 0;
		}
		.chose .money div .mbutton,
		.chose .cancel,
		.order #buttons .orfun,
		.finalfun .mbutton,
		.editbox #buttons .orfun,
		.result #buttons .orfun,
		.sysmeg .mbutton {
			width:272px;
			height:123px;
			font-size:55px;
			padding:0;
			font-family: Arial,Microsoft JhengHei,sans-serif;
			font-weight:bold;
			color:#3e3a39;
			border:0;
			background-image:url("./img/small.png");
			background-repeat:no-repeat;
			background-position:center;
			background-color:transparent;
		}
		.bill .mbutton {
			width:685px;
			height:174px;
			font-size:80px;
			padding:0;
			font-family: Arial,Microsoft JhengHei,sans-serif;
			font-weight:bold;
			color:#3e3a39;
			border:0;
			background-image:url("./img/big.png");
			background-repeat:no-repeat;
			background-position:center;
			background-color:transparent;
		}
		.bill ul {
			list-style-type:none;
			padding-left:0;
		}
		.bill ul li {
			font-weight: bold;
			font-size:30px;
			color:#3e3a39;
		}
		.bill ul li:before {
			line-height:45px;
			content:'●';
			font-size:45px;
		}
		.list {
			width:1080px;
			height:380px;
			background-image:url('./img/orderlist.png');
			float:left;
			position:relative;
		}
		.list .listcontent {
			width:100%;
			height:calc(100% - 105px);
			overflow-x:auto;
			overflow-y:hidden;
		}
		.list .listcontent .box {
			height:100%;
			white-space:nowrap;
		}
		.list .listcontent #item {
			margin-left:15px;
			height:275px;
			float:left;
			position: relative;
			display:inline-block;
		}
		.list .listcontent #item #name {
			color:#3e3a39;
			height:calc(100% - 55px);
			overflow:hidden;
		}
		.list .listcontent #item #name ul {
			list-style-type:none;
			padding-left:0;
		}
		.list .listcontent #item #name ul li {
			font-weight: bold;
			font-size:37px;
			color:#3e3a39;
		}
		.list .listcontent #item #name ul li:before {
			line-height:50px;
			content:'●';
			font-size:60px;
		}
		.list .listcontent #item #name ul ul {
			list-style-type:none;
			padding-left:0;
		}
		.list .listcontent #item #name ul ul li {
			font-weight: normal;
			font-size:31px;
			color:#3e3a39;
		}
		.list .listcontent #item #name ul ul li:before {
			line-height:41px;
			content:'-';
			font-size:40px;
			margin:0 15px 0 10px;
		}
		.list .listcontent #item #num {
			width:100%;
			float:left;
			overflow:hidden;
			position:absolute;
			left:0;
			bottom:10px;
		}
		.list .listcontent #item .numbox {
			width:calc(100% - 40px);
			margin-left:40px;
			font-size:31px;
			font-weight:900;
			float:left;
		}
		.list .listcontent #item #price {
			width:15%;
			margin-right:15px;
			float:right;
			text-align:right;
		}
		.list .listcontent::-webkit-scrollbar {
			display:none;
		}
		.list #listfun {
			width:45px;
			height:45px;
			vertical-align:top;
			float:left;
			margin-top:-5.5px;
		}
		.list .listfunbox {
			width:100%;
			height:calc(100% - 275px);
			float:left;
		}
		.finalfun {
			width:1080px;
			height:190px;
			float:left;
		}
		.result {
			z-index:200;
		}
		.result .rescontent .listbox #list #itemlist #name {
			color:#3e3a39;
			width:calc(calc(100% / 3) - 16px);
			padding:0 0 0 16px;
			overflow:hidden;
			float:left;
		}
		.result .rescontent .listbox #list #itemlist #name ul {
			list-style-type:none;
			padding-left:0;
		}
		.result .rescontent .listbox #list #itemlist #name ul li {
			font-weight: bold;
			font-size:33px;
			color:#3e3a39;
		}
		.result .rescontent .listbox #list #itemlist #name ul li:before {
			line-height:47px;
			content:'●';
			font-size:58px;
		}
		.result .rescontent .listbox #list #itemlist #name ul ul {
			list-style-type:none;
			padding-left:0;
		}
		.result .rescontent .listbox #list #itemlist #name ul ul li {
			font-weight: normal;
			font-size:28px;
			color:#3e3a39;
		}
		.result .rescontent .listbox #list #itemlist #name ul ul li:before {
			line-height:39px;
			content:'-';
			font-size:38px;
			margin:0 15px 0 10px;
		}
		.result .rescontent .listbox #list #itemlist #number {
			font-size:33px;
			line-height:98px;
			font-weight:bold;
			color:#3e3a39;
			width:calc(calc(100% / 3) - 4px);
			text-align:center;
			overflow:hidden;
			float:left;
			border-left:2px solid #898989;
			border-right:2px solid #898989;
		}
		.result .rescontent .listbox #list #itemlist #money {
			font-size:33px;
			line-height:98px;
			font-weight:bold;
			color:#3e3a39;
			width:calc(100% / 3);
			text-align:center;
			overflow:hidden;
			float:left;
		}
		/*.finish {
			font-size:28px;
		}*/
		.ui-widget {
			font-family: Arial,Microsoft JhengHei,sans-serif;
		}
		.ui-widget .ui-widget {
			font-size:30px;
		}
		.ui-dialog-titlebar {
			display:none;
		}
		.ui-dialog {
			padding:0;
		}
		.ui-dialog .ui-dialog-content {
			padding:0;
		}

		/* pager */
		.pagers,
		.listpagers { 
			text-align: center;
			width:calc(100% - 140px);
			z-index: 100;
			position: absolute;
			bottom: 30px;
			overflow: hidden;
			float:left;
		}
		.pagers span,
		.listpagers span { 
			font-family: arial;
			font-size: 50px;
			width: 30px; 
			height: 16px; 
			display: inline-block;
			color: #ddd;
			cursor: pointer; 
		}
		.pagers span.cycle-pager-active,
		.listpagers span.cycle-pager-active { 
			color: #D69746;
		}
		.pagers > *,
		.listpagers > * { 
			cursor: pointer;
		}

		.leftimg,
		.rightimg,
		.listleftimg,
		.listrightimg {
			width:29px;
			height:55px;
			margin:0;
			padding:0;
			z-index:50;
		}
		.leftimg,
		.listleftimg {
			position:absolute;
			bottom:25px;
			left:23px;
		}
		.rightimg,
		.listrightimg {
			position:absolute;
			bottom:25px;
			right:23px;
		}
		/* 12. Owl Carousel */
		.owl-carousel {
			position: relative;
			z-index: 1;
			display: none;
			margin-left:20px;
			width: calc(100% - 40px);
			-webkit-tap-highlight-color: transparent;
		}

		.owl-carousel.owl-slider {
			margin-bottom: 1.5em;
			background-color: #fafafa;
		}

		.owl-carousel .owl-stage {
			position: relative;
			-ms-touch-action: pan-Y;
		}

		.owl-carousel .owl-stage:after {
			content: ".";
			display: block;
			clear: both;
			visibility: hidden;
			line-height: 0;
			height: 0;
		}

		.owl-carousel .owl-stage-outer {
			position: relative;
			overflow: hidden;
			-webkit-transform: translate3d(0px, 0px, 0px);
		}

		.owl-carousel .owl-controls {
			text-align: center;
			-webkit-tap-highlight-color: transparent;
		}

		.owl-carousel .owl-controls .owl-nav .owl-prev,
		.owl-carousel .owl-controls .owl-nav .owl-next,
		.owl-carousel .owl-controls .owl-dot,
		.pagers .owl-dot {
			cursor: pointer;
			cursor: hand;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		.owl-carousel.owl-loaded {
			display: block;
		}

		.owl-carousel.owl-loading {
			opacity: 0;
			display: block;
		}

		.owl-carousel.owl-hidden {
			opacity: 0;
		}

		.owl-carousel .owl-refresh .owl-item {
			display: none;
		}

		.owl-carousel .owl-item {
			position: relative;
			min-height: 1px;
			float: left;
			-webkit-backface-visibility: hidden;
			-webkit-tap-highlight-color: transparent;
			-webkit-touch-callout: none;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		.owl-carousel .owl-item img {
			display: block;
			width: 100%;
			-webkit-transform-style: preserve-3d;
		}

		.owl-carousel.owl-text-select-on .owl-item {
			-webkit-user-select: auto;
			-moz-user-select: auto;
			-ms-user-select: auto;
			user-select: auto;
		}

		.owl-carousel .animated {
			-webkit-animation-duration: 1000ms;
			animation-duration: 1000ms;
			-webkit-animation-fill-mode: both;
			animation-fill-mode: both;
		}
		.owl-carousel .owl-animated-in {
			z-index: 0;
		}
		.owl-carousel .owl-animated-out {
			z-index: 1;
		}
		.owl-carousel .fadeOut {
			-webkit-animation-name: fadeOut;
			animation-name: fadeOut;
		}

		@-webkit-keyframes fadeOut {
			0% {
				opacity: 1;
			}
			100% {
				opacity: 0;
			}
		}

		@keyframes fadeOut {
			0% {
				opacity: 1;
			}
			100% {
				opacity: 0;
			}
		}

		.owl-height {
			-webkit-transition: height 0.3s ease-in-out;
			transition: height 0.3s ease-in-out;
		}

		.owl-carousel .owl-grab {
			cursor: move;
			cursor: -webkit-grab;
			cursor: -o-grab;
			cursor: -ms-grab;
			cursor: grab;
		}

		.owl-carousel.owl-rtl {
			direction: rtl;
		}

		.owl-carousel.owl-rtl .owl-item {
			float: right;
		}

		.no-js .owl-carousel {
			display: block;
		}

		.owl-carousel .owl-item .owl-lazy {
			opacity: 0;
			-webkit-transition: opacity 0.3s ease;
			transition: opacity 0.3s ease;
		}

		.owl-carousel .owl-item img {
			transform-style: preserve-3d;
		}

		.owl-carousel .owl-video-wrapper {
			position: relative;
			height: 100%;
			background: #000;
		}

		.owl-carousel .owl-video-play-icon {
			position: absolute;
			height: 80px;
			width: 80px;
			left: 50%;
			top: 50%;
			margin-left: -40px;
			margin-top: -40px;
			background: #666;
			cursor: pointer;
			z-index: 1;
			-webkit-backface-visibility: hidden;
		}

		.owl-carousel .owl-video-playing .owl-video-tn,
		.owl-carousel .owl-video-playing .owl-video-play-icon {
			display: none;
		}

		.owl-carousel .owl-video-tn {
			opacity: 0;
			height: 100%;
			background-position: center center;
			background-repeat: no-repeat;
			background-size: contain;
			-webkit-transition: opacity 0.3s ease;
			transition: opacity 0.3s ease;
		}

		.owl-carousel .owl-video-frame {
			position: relative;
			z-index: 1;
		}

		.owl-carousel .owl-nav {
			display: none;
		}

		.owl-carousel .owl-prev,
		.owl-carousel .owl-next {
			position: absolute;
			z-index: 5;
			top: 50%;
			width: 20px;
			height: 40px;
			margin-top: -20px;
			background-repeat: no-repeat;
			background-position: center center;
			color: #8899a6;
		}

		.owl-carousel .owl-prev:hover,
		.owl-carousel .owl-next:hover {
			color: #292f33;
		}

		.owl-carousel .owl-prev i,
		.owl-carousel .owl-next i {
			display: none;
		}

		.owl-carousel .owl-prev {
			left: -20px;
		}

		.owl-carousel .owl-next {
			right: -20px;
		}

		.owl-carousel.owl-round-arrows .owl-prev,
		.owl-carousel.owl-round-arrows .owl-next {
			top: 50%;
			width: 60px;
			height: 60px;
			margin-top: -30px;
			background: #fff;
			border-radius: 100%;
		}

		.owl-carousel.owl-round-arrows .owl-prev i,
		.owl-carousel.owl-round-arrows .owl-next i {
			display: block;
			font-size: 1.5em;
			line-height: 59px;
		}

		.owl-carousel.owl-round-arrows .owl-prev {
			left: 0;
		}

		.owl-carousel.owl-round-arrows .owl-prev i {
			margin-right: 1px;
		}

		.owl-carousel.owl-round-arrows .owl-next {
			right: 0;
		}

		.owl-carousel.owl-round-arrows .owl-next i {
			margin-left: 1px;
		}

		.owl-carousel .owl-dots,
		.pagers .owl-dot {
			position: absolute;
			z-index: 6;
			left: 0;
			width: 100%;
			bottom: 10px;
			pointer-events: none;
		}

		.owl-carousel .owl-dots .owl-dot,
		.pagers .owl-dot {
			display: inline-block;
			padding: 0 4px;
			pointer-events: auto;
		}

		.owl-carousel .owl-dots .owl-dot span,
		.pagers .owl-dot span {
			display: inline-block;
			width: 15px;
			height: 4px;
			border-radius: 1px;
			background-color: #fff;
		}

		.owl-carousel .owl-dots .owl-dot.active span,
		.pagers .owl-dot.active span {
			background-color: #292f33;
		}

		.owl-red-dots .owl-dots .owl-dot span {
			background-color: #ccd6dd;
		}

		.owl-red-dots .owl-dots .owl-dot.active span {
			background-color: #ff2641;
		}
	</style>
</head>
<body>
	<div class="logo">
		<img src='img/banner.png'>
	</div>
	<div id='sidebar'>
		<div style='width:100%;text-align:center;margin:38px 0;'>
			<img src='img/up.png' style='vertical-align:middle;'>
		</div>
		<div style='width:100%;height:calc(100% - 210px);'>
			<a href='#' style='text-decoration:none;'>
				<div style='width:100%;min-height:96px;text-align:center;background-image:url("./img/group.png");background-position:center;background-repeat:no-repeat;padding:0;font-size:40px;color:#3e3a39;line-height:96px;'>
					<strong>便當</strong>
				</div>
			</a>
			<a href='#' style='text-decoration:none;'>
				<div style='width:100%;min-height:96px;text-align:center;background-image:url("./img/group.png");background-position:center;background-repeat:no-repeat;padding:0;font-size:40px;color:#3e3a39;line-height:96px;'>
					<strong>主菜單點</strong>
				</div>
			</a>
			<a href='#' style='text-decoration:none;'>
				<div style='width:100%;min-height:96px;text-align:center;background-image:url("./img/group.png");background-position:center;background-repeat:no-repeat;padding:0;font-size:40px;color:#3e3a39;line-height:96px;'>
					<strong>飲品</strong>
				</div>
			</a>
			<a href='#' style='text-decoration:none;'>
				<div style='width:100%;min-height:96px;text-align:center;background-image:url("./img/group.png");background-position:center;background-repeat:no-repeat;padding:0;font-size:40px;color:#3e3a39;line-height:96px;'>
					<strong>湯品</strong>
				</div>
			</a>
		</div>
		<div style='width:100%;text-align:center;margin:38px 0;'>
			<img src='img/down.png' style='vertical-align:middle;'>
		</div>
	</div>
	<div id='main'>
		<!-- <div class='owl-carousel owl-thumbs content' data-cycle-fx='scrollHorz' data-cycle-pager=".pagers" data-cycle-timeout='0' data-cycle-speed='800' data-cycle-slides="> div[class='foodbox']" data-cycle-prev=".prev" data-cycle-next=".next"> -->
		<div class='owl-carousel owl-thumbs content'>
		<?php
		$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
		if(isset($content['basic']['dep']) && strlen(trim($content['basic']['dep']))>0){
			$sql='SELECT itemsdata.*,a.depnumber,a.money,a.counter FROM itemsdata JOIN (SELECT company,inumber,depnumber,money,counter FROM imoney WHERE company="'.$content['basic']['company'].'" AND depnumber="'.$content['basic']['dep'].'") AS a ON a.inumber=itemsdata.inumber AND a.company=itemsdata.company';
			$itemname=parse_ini_file('data/'.$content['basic']['dep'].'-menu.ini',true);
		}
		else{
			$sql='SELECT * FROM itemsdata WHERE company="testorder"';
			$itemname=parse_ini_file('data/'.$content['basic']['company'].'-menu.ini',true);
		}
		$items=sqlquery($conn,$sql,'mysql');
		sqlclose($conn,'mysql');
		if(sizeof($items)==0){
		}
		else{
			$rownumber=2;
			$colnumber=2;
			$pagenumber=$rownumber*$colnumber;
			for($i=0;$i<sizeof($items);$i++){
				$mcounter=0;
				for($m=1;$m<=$pagenumber;$m++){
					if($itemname[$items[$i]['inumber']]['money'.$m]>0){
						$mcounter++;
						$money=$itemname[$items[$i]['inumber']]['money'.$m];
						$mname=$itemname[$items[$i]['inumber']]['mname'.$m];
					}
					else{
					}
					if($mcounter==2){
						break;
					}
					else{
					}
				}
				if($i%$pagenumber==0){
					echo "<div class='thumb foodbox'>";
				}
				else{
				}
				echo "<div class='food";
						if($i%$rownumber==($rownumber-1)){
						}
						else{
							echo " right";
						}
						if(floor($i/$rownumber)==($colnumber-1)){
						}
						else{
							echo " bottom";
						}
				echo "' id='number".$items[$i]['inumber']."'>
						<div class='fdimgbox'>
							<img class='foodimg' src='".$itemname[$items[$i]['inumber']]['image']."?".date('YmdHis')."'>
							<div class='plus'>
								<img src='./img/wplus.png'>
							</div>
						</div>
						<div class='foodtitle'><strong>".$itemname[$items[$i]['inumber']]['name']."</strong></div>
						<input type='hidden' class='name' value='".$itemname[$items[$i]['inumber']]['name']."'>
						<input type='hidden' class='inumber' value='".$items[$i]['inumber']."'>";
				echo "</div>";
				if($i%$pagenumber==($pagenumber-1)){
					echo "</div>";
				}
				else{
				}
			}
			if($i%$pagenumber<=($pagenumber-1) && $i%$pagenumber>=1){
				echo "</div>";
			}
			else{
			}
		}
		?>
		</div>
		<img class='leftimg' src='./img/left.png'>
		<div class='nav'></div>
		<div class="pagers"></div>
		<img class='rightimg' src='./img/right.png'>
		<!-- <div class="function">
			<div id="service" class="fun">
				<div class='funimgbox'>
					<img class='funimg' src='service.png'>
				</div>
				<div class='funtitle'>服務鈴</div>
			</div>
			<div id="getlist" class="fun">
				<div class='funimgbox'>
					<div class='point'></div>
					<div class='temppoint'></div>
					<img class='funimg' src='getlist.png'>
				</div>
				<div class='funtitle'>紀錄/結帳</div>
			</div>
		</div> -->
	</div>
	<div class='list'>
		<!-- <form id='orderlist' method='post' action='result.php'> -->
			<div class='listcontent'><table><tr class='box'></tr></table></div>
			<!-- <div class='total' style='display:none;'><span id='tolbox'>總共<span class='tmoy'>0</span>元</span></div> -->
			<div class='listfunbox'><img class='listleftimg' src='./img/left.png'><div class="listpagers"></div><img class='listrightimg' src='./img/right.png'></div>
			<!-- <input type='hidden' name='tmoy' value='0'>
		</form> -->
	</div>
	<div class='finalfun'>
		<input type='button' class='mbutton' id='home' style='margin:33.5px 0 0 31px;float:left;background-image:url("./img/home.png");padding:0 0 0 75px;' value='首頁'>
		<div class='total' style='width:calc(100% - 606px - 30px);height:100%;line-height:190px;margin:0 15px;text-align:center;float:left;font-size:37px;color:#3e3a39;font-weight:bold;'>應付金額<div style='display:inline;' class='tmoy'>0</div>元</div>
		<input type='button' class='mbutton' id='submit' style='margin:33.5px 31px 0 0;float:right;' value='送出'>
	</div>
	<script type="text/javascript" src="tool/jquery.min.js"></script>
	<script type="text/javascript" src="tool/site.js"></script>
	<script type="text/javascript" src="tool/owl.carousel.min.js"></script>
	<script>
		jQuery(".owl-carousel.owl-slider").owlCarousel({
			items   : 1,
			nav     : true,
			navText : ['', ''],
			loop    : true
		});
		jQuery(".owl-carousel.owl-thumbs").owlCarousel({
			items   : 1,
			margin  : 30,
			nav     : true,
			navText : ['', ''],
			dots : true,
			loop    : false,
			navText: ["<img src='./tool/left.png'>","<img src='./tool/right.png'>"]
		});
	</script>
</body>
</html>
