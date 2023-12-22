<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0;">
	<title>遠端多媒體看板-廚房控菜系統</title>
	<script src='../tool/jquery-1.12.4.js'></script>
	<style>
	body {
		width:calc(100vw - 40px);
		height:calc(100vh - 40px);
		padding:20px;
		margin:0;
		font-size:60px;
		background-color:#fbf4ec;
	}

	button {
		border-radius: 5px;
		padding:0;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		background-color: transparent;
	}

	button div {
		width:100%;
	}

	.maincontent,
	.headerfunbox {
		width:100%;
		float:left;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		background-color: transparent;
	}

	.maincontent {
		height:calc(100% - 80px);
	}

	.maincontent .content,
	.maincontent .list {
		height:100%;
		float:left;
		padding:3px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		overflow-y:auto;
		overflow-x:hidden;
	}

	.maincontent .content::-webkit-scrollbar-track,
	.maincontent .content #items::-webkit-scrollbar-track,
	.maincontent .list::-webkit-scrollbar-track  {
		-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
		border-radius: 10px;
		background-color: #F5F5F5;
	}

	.maincontent .content::-webkit-scrollbar,
	.maincontent .content #items::-webkit-scrollbar,
	.maincontent .list::-webkit-scrollbar  {
		width: 10px;
		background-color: #F5F5F5;
	}

	.maincontent .content::-webkit-scrollbar-thumb,
	.maincontent .content #items::-webkit-scrollbar-thumb,
	.maincontent .list::-webkit-scrollbar-thumb  {
		border-radius: 10px;
		-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
		background-color: #898989;
	}

	.maincontent .content{
		width:80%;
		border-right:1px solid #898989;
	}

	.content .maindiv {
		width:100%;
		float:left;
	}

	.content .subdiv {
		width:calc((100% - 90px) / 3 - 10px);
		float:left;
		margin:5px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		border-radius:5px;
		border:1px solid #898989;
	}

	.content .subdiv #itemdiv {
		width:calc(100% - 50px);
		float:right;
	}

	.maincontent .list {
		width:20%;
		border-left:1px solid #898989;
		font-size:40px;
	}

	.headerfunbox {
		padding:4px;
		height:80px;
		position: relative;
	}

	.headerfunbox #kdsgroupmenu,
	.headerfunbox #kdsgroupname,
	.headerfunbox #time,
	.headerfunbox #reflash {
		height:100%;
		margin:0 1px;
		font-size:30px;
		float:right;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}

	.headerfunbox #kdsgroupmenu,
	.headerfunbox #kdsgroupname {
		float:left;
	}

	.headerfunbox #kdsgroupname {
		padding: 19px 0;
		position: absolute;
		top: 0;
		left: 50px;
	}

	.headerfunbox #kdsgroupmenu {
		position: relative;
	}

	#kdsgroupmenu .site-menu-toggle {
		display: block;
		position: absolute;
		top: 10px;
		left: 0;
		float: right;
		padding: 10px;
	}

	#kdsgroupmenu .site-menu-toggle em {
		position: relative;
		display: block;
		width: 30px;
		height: 2px;
		margin: 4px 0;
		background-color: #000000;
		-o-transform: translateZ(0);
		-webkit-transform: translateZ(0);
		transform: translateZ(0);
		-o-backface-visibility: hidden;
		-webkit-backface-visibility: hidden;
		backface-visibility: hidden;
		-o-transition: -o-transform 0.3s ease-in-out, background 0.3s ease-in-out, right 0.2s ease-in-out;
		-webkit-transition: -webkit-transform 0.3s ease-in-out, background 0.3s ease-in-out, right 0.2s ease-in-out;
		transition: transform 0.3s ease-in-out, background 0.3s ease-in-out, right 0.2s ease-in-out;
	}

	#kdsgroupmenu .site-menu-toggle em.first {
		/*right: 2px;*/
		-o-transform: translateY(0) rotate(0deg);
		-webkit-transform: translateY(0) rotate(0deg);
		-ms-transform: translateY(0) rotate(0deg);
		transform: translateY(0) rotate(0deg);
	}

	#kdsgroupmenu .site-menu-toggle em.last {
		/*right: 5px;*/
		-o-transform: translateY(0) rotate(0deg);
		-webkit-transform: translateY(0) rotate(0deg);
		-ms-transform: translateY(0) rotate(0deg);
		transform: translateY(0) rotate(0deg);
	}

	#kdsgroupmenu .site-menu-toggle:focus em.first,
	#kdsgroupmenu .site-menu-toggle:focus em.last,
	#kdsgroupmenu .site-menu-toggle:hover em.first,
	#kdsgroupmenu .site-menu-toggle:hover em.last { 
		right: 0;
	}

	#kdsgroupmenu.open-mobile-menu .site-menu-toggle em.first {
		right: 0;
		-o-transform: translateY(6px) rotate(45deg);
		-webkit-transform: translateY(6px) rotate(45deg);
		-ms-transform: translateY(6px) rotate(45deg);
		transform: translateY(6px) rotate(45deg);
	}

	#kdsgroupmenu.open-mobile-menu .site-menu-toggle em.middle { 
		background-color: transparent;
		background-color: #ffffff\9\0;
		-o-transition: background 0.1s ease-in-out;
		-webkit-transition: background 0.1s ease-in-out;
		transition: background 0.1s ease-in-out;
	}

	#kdsgroupmenu.open-mobile-menu .site-menu-toggle em.last {
		right: 0;
		-o-transform: translateY(-6px) rotate(-45deg);
		-webkit-transform: translateY(-6px) rotate(-45deg);
		-ms-transform: translateY(-6px) rotate(-45deg);
		transform: translateY(-6px) rotate(-45deg);
	}

	#site-menu {
		visibility: hidden;
		max-height: 0;
		overflow: hidden;
		background-color: #fbf4ec;
		opacity: 0;
		margin-top:55px;
		-o-transition: max-height 0.3s ease-in-out, opacity 0.4s ease-in-out, visibility 0.4s ease-in-out;
		-webkit-transition: max-height 0.3s ease-in-out, opacity 0.4s ease-in-out, visibility 0.4s ease-in-out;
		transition: max-height 0.3s ease-in-out, opacity 0.4s ease-in-out, visibility 0.4s ease-in-out;
	}

	#kdsgroupmenu.open-mobile-menu #site-menu {
		visibility: visible;
		max-height: 150em;
		opacity: 1;
	}

	#site-menu ul {
		padding-left: 0;
		margin: 0;
		font-family: Arial,Microsoft JhengHei,sans-serif;
		list-style: none;
	}

	#site-menu li {
		margin: 5px 0;
		border-top: 1px solid #e1e8ed;
		cursor: pointer;
	}

	.headerfunbox #time {
		font-size:30px;
		padding:19px 0;
	}

	.headerfunbox #reflash {
		width:120px;
	}
	</style>
	<?php
	$initsetting=parse_ini_file('../database/initsetting.ini',true);
	//date_default_timezone_set('Asia/Taipei');
	date_default_timezone_set($initsetting['init']['settime']);
	$setup=parse_ini_file('../database/setup.ini',true);
	
	$basicdata=parse_ini_file('./database/initsetting.ini',true);
	if(file_exists('../database/'.$setup['basic']['company'].'-kds.ini')&&isset($initsetting['init']['kds'])&&$initsetting['init']['kds']=='1'){
		$kdstype=parse_ini_file('../database/'.$setup['basic']['company'].'-kds.ini',true);
	}
	else{
		$kdstype='-1';
	}
	$lan=parse_ini_file('./database/lan.ini',true);
	?>
	<script>
	$(document).ready(function(){
		var maxtime=<?php echo $basicdata['init']['reflash']; ?>;
		var nowtime=<?php echo $basicdata['init']['reflash']; ?>;
		$.ajax({
			url:'./reflash.ajax.php',
			method:'post',
			async:false,
			data:{'groupno':$('.headerfunbox #kdsgroupname input[name="groupno"]').val()},
			dataType:'json',
			success:function(d){
				$('.maincontent .content').html(d[0]);
				$('.maincontent .list').html(d[1]);
				//console.log('1');
			},
			error:function(e){
				//console.log(e);
			}
		});
		setInterval(function(){
			$.ajax({
				url:'./gettime.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('#time').html(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
			nowtime--;
			if(nowtime==0){
				//console.log('S');
				$('.headerfunbox #reflash').trigger('click');
				nowtime=maxtime;
				$('.headerfunbox #reflash #retime').html(nowtime);
				//console.log('E');
			}
			else if(nowtime<0){
				$('.headerfunbox #reflash #retime').html(0);
			}
			else{
				$('.headerfunbox #reflash #retime').html(nowtime);
			}
		},1000);
		$('#kdsgroupmenu .site-menu-toggle').on('click',function(e) {
			$('#kdsgroupmenu').toggleClass('open-mobile-menu');
			$('.kdsgroupmenu').toggleClass('open-mobile-menu');
			e.preventDefault();
		});
		$('.headerfunbox #reflash').on('click',function(){
			$.ajax({
				url:'./reflash.ajax.php',
				method:'post',
				async:false,
				data:{'groupno':$('.headerfunbox #kdsgroupname input[name="groupno"]').val()},
				dataType:'json',
				success:function(d){
					$('.maincontent .content').html(d[0]);
					$('.maincontent .list').html(d[1]);
					//console.log(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
			nowtime=maxtime;
			$('.headerfunbox #reflash #retime').html(nowtime);
		});
		$('.headerfunbox #kdsgroupmenu #site-menu ul li').on('click',function(e){
			$('.headerfunbox #kdsgroupname').html($(this).html());
			nowtime=maxtime;
			$('.headerfunbox #reflash #retime').html(nowtime);
			$.ajax({
				url:'./reflash.ajax.php',
				method:'post',
				async:false,
				data:{'groupno':$(this).find('input[name="groupno"]').val()},
				dataType:'json',
				success:function(d){
					$('.maincontent .content').html(d[0]);
					$('.maincontent .list').html(d[1]);
					$('#kdsgroupmenu .site-menu-toggle').trigger('click');
					//console.log(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
		});
		$('.content').on('click','.maindiv input[type="button"]',function(){
			var index=$('.content .maindiv input[type="button"]').index(this);
			$.ajax({
				url:'./deleteitem.ajax.php',
				method:'post',
				async:false,
				data:{'tablenumber':$('.content .maindiv:eq('+index+') #data input[name="tablenumber"]').val(),'label':'items','type':'all','kdsgroup':$('.headerfunbox #kdsgroupname input[name="groupno"]').val(),'consecnumber':$('.content .maindiv:eq('+index+') #data input[name="listindex"]').val(),'linenumber':$('.content .maindiv:eq('+index+') #data input[name="itemindex"]').val(),'number':$('.content .maindiv:eq('+index+') #data input[name="itemnumber"]').val()},
				dataType:'html',
				success:function(d){
					$('.headerfunbox #reflash').trigger('click');
					//console.log(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
		});
		$('.content').on('click','.subdiv #itemdiv',function(){
			var index=$('.content .subdiv #itemdiv').index(this);
			$.ajax({
				url:'./deleteitem.ajax.php',
				method:'post',
				async:false,
				data:{'label':'items','type':'all','kdsgroup':$('.headerfunbox #kdsgroupname input[name="groupno"]').val(),'consecnumber':$('.content .subdiv:eq('+index+') #data input[name="listindex"]').val(),'linenumber':$('.content .subdiv:eq('+index+') #data input[name="itemindex"]').val(),'number':$('.content .subdiv:eq('+index+') #data input[name="itemnumber"]').val()},
				dataType:'html',
				success:function(d){
					$('.headerfunbox #reflash').trigger('click');
					//console.log(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
		});
		$('.content').on('click','.subdiv input[type="button"]',function(){
			var index=$('.content .subdiv input[type="button"]').index(this);
			$.ajax({
				url:'./deleteitem.ajax.php',
				method:'post',
				async:false,
				data:{'label':'items','type':'one','kdsgroup':$('.headerfunbox #kdsgroupname input[name="groupno"]').val(),'consecnumber':$('.content .subdiv:eq('+index+') #data input[name="listindex"]').val(),'linenumber':$('.content .subdiv:eq('+index+') #data input[name="itemindex"]').val(),'number':$('.content .subdiv:eq('+index+') #data input[name="itemnumber"]').val()},
				dataType:'html',
				success:function(d){
					$('.headerfunbox #reflash').trigger('click');
					//console.log(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
		});
		$('.content').on('click','.maindivtype1 .itemdiv',function(){
			$.ajax({
				url:'./deleteitem.ajax.php',
				method:'post',
				async:false,
				data:{'label':'lists','type':'all','kdsgroup':$('.headerfunbox #kdsgroupname input[name="groupno"]').val(),'consecnumber':$(this).find('#data input[name="listindex"]').val(),'linenumber':$(this).find('#data input[name="itemindex"]').val(),'number':$(this).find('#data input[name="ordertag"]').val()},
				dataType:'html',
				success:function(d){
					$('.headerfunbox #reflash').trigger('click');
					//console.log(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
		});
		$('.headerfunbox #time').click(function(){
			$.ajax({
				url:'../demopos/lib/js/create.cmdtxt.php',
				method:'post',
				async: false,
				data:{'cmd':'m1-printchange_m1'},
				dataType:'html',
				success:function(d){
					console.log(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		});
	});
	</script>
</head>
<body>
	<div class='headerfunbox'>
		<div id='kdsgroupmenu'>
			<a href="#site-menu" class="site-menu-toggle"><em class="first"></em><em class="middle"></em><em class="last"></em></a>
			<div id='site-menu'>
				<ul>
					<?php
					if(isset($initsetting['init']['kds'])&&$initsetting['init']['kds']=='1'){
						foreach($kdstype['type']['name'] as $i=>$v){
							echo '<li>'.$v.'<input type="hidden" name="groupno" value="'.$i.'"></li>';
						}
					}
					else{
					}
					if(isset($initsetting['init']['posservice'])&&$initsetting['init']['posservice']=='1'){
						echo '<li>'.$lan['name']['posservice'].'<input type="hidden" name="groupno" value="service"></li>';
					}
					else{
					}
					?>
				</ul>
			</div>
		</div>
		<div id='kdsgroupname'><?php 
		if(isset($initsetting['init']['kds'])&&$initsetting['init']['kds']=='1'){
			foreach($kdstype['type']['name'] as $i=>$v){
				echo $v.'<input type="hidden" name="groupno" value="'.$i.'">';
				break;
			}
		}
		else{
			echo $lan['name']['posservice'].'<input type="hidden" name="groupno" value="service">';
		}
		?></div>
		<div id='time'><?php echo substr(date('Y/m/d H:i'),2); ?></div>
		<button id='reflash'><div>刷新 <span id='retime'><?php echo $basicdata['init']['reflash']; ?></span></div></button>
	</div>
	<div class='maincontent'>
		<div class='content'>
		</div>
		<div class='list'>
		</div>
	</div>
</body>
</html>
