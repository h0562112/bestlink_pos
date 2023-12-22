<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>點餐畫面</title>
	<link rel="stylesheet" href="./lib/css/jquery-ui.css">
	<script src='./lib/js/jquery-1.12.4.js'></script>
	<script src="./lib/js/jquery-ui.js"></script>
	<style>
		body {
			width:800px;/*1080px*/
			height:1280px;/*1920px*/
			padding:0;
			margin:0;
			font-size:60px;
			color:#3C3B39;
			font-weight:bold;
			overflow:hidden;
			font-family: Consolas,Microsoft JhengHei,sans-serif;
		}
		div {
			font-family: Consolas,Microsoft JhengHei,sans-serif;
		}
		/*.ui-widget .button,
		.button {
			width:400px;
			height:175px;
			font-size:80px;
			padding:0;
			font-family: Consolas,Microsoft JhengHei,sans-serif;
			font-weight:bold;
			color:#3e3a39;
			border:0;
			background-repeat:no-repeat;
			background-position:center;
			background-color:transparent;
		}*/
		.ui-widget {
			font-family: Consolas,Microsoft JhengHei,sans-serif;
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
		.ui-widget input {
			font-size:80px;
			font-family: Consolas,Microsoft JhengHei,sans-serif;
			color:#3e3a39;
		}
		input.button {
			width:calc(100% / 4 - 5px);
			height:calc((100% - 82.5px) / 11 - 5px);
			float:left;
			background-color: transparent;
			border:3px solid #898989;
			border-radius: 5px;
			color:#898989;
			margin:2.5px;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
	</style>
	<script>
		$(document).ready(function(){
			/*type=$('.type').dialog({
				autoOpen:false,
				width:$(document).width(),//1080
				height:$(document).height(),//1920
				position:{my:'left top',at:'left top',of:'body'},
				resizable:false,
				modal:true,
				draggable:false
			});*/
			/*member=$('.member').dialog({
				autoOpen:false,
				width:768,//1080
				height:1366,//1920
				position:{my:'left top',at:'left top',of:'body'},
				resizable:false,
				modal:true,
				draggable:false
			});*/
			/*number=$('.number').dialog({
				autoOpen:false,
				width:$(document).width(),//1080
				height:$(document).height(),//1920
				position:{my:'left top',at:'left top',of:'body'},
				resizable:false,
				modal:true,
				draggable:false
			});*/
			person=$('.person').dialog({
				autoOpen:false,
				width:800,//1080
				height:1280,//1920
				position:{my:'left top',at:'left top',of:'body'},
				resizable:false,
				modal:true,
				draggable:false
			});
			$(document).on('click','#home',function(){
				person.dialog('open');
			});
			$(document).on('click','.person input[name="id"]',function(){
				$('.person input[name="tag"]').val('id');
			});
			$(document).on('click','.person input[name="pw"]',function(){
				$('.person input[name="tag"]').val('pw');
			});
			$(document).on('click','.person input.button',function(){
				var index=$('.person input.button').index(this);
				$('.person input[name="'+$('.person input[name="tag"]').val()+'"]').val($('.person input[name="'+$('.person input[name="tag"]').val()+'"]').val()+$('.person input.button:eq('+index+')').val());
			});
			$(document).on('click','.person #AC',function(){
				$('.person input[name="'+$('.person input[name="tag"]').val()+'"]').val('');
			});
			$(document).on('click','.person #BKSP',function(){
				if($('.person input[name="'+$('.person input[name="tag"]').val()+'"]').val().length==0){
				}
				else{
					$('.person input[name="'+$('.person input[name="tag"]').val()+'"]').val($('.person input[name="'+$('.person input[name="tag"]').val()+'"]').val().substr(0,$('.person input[name="'+$('.person input[name="tag"]').val()+'"]').val().length-1));
				}
			});
			$(document).on('click','.person #cancel',function(){
				$('.person input[name="pw"]').val('');
				person.dialog('close');
			});
			$(document).on('click','.person #submit',function(){
				if($('.person input[name="tag"]').val()=='id'){
					$('.person input[name="tag"]').val('pw');
				}
				else{
					$.ajax({
						url:'./login.php',
						method:'post',
						data:{'id':$('.person input[name="id"]').val(),'pw':$('.person input[name="pw"]').val()},
						dataType:'html',
						success:function(d){
							if(d=='login'){
								location.href='./index2.php?userid='+$('.person input[name="id"]').val();
							}
							else{
								alert('帳號密碼錯誤。');
							}
						},
						error:function(e){
							console.log(e);
						}
					});
				}
			});
			/*$(document).on('click','#out',function(){
				location.href='./index2.php?type=out';
			});
			$(document).on('click','#in',function(){
				location.href='./index2.php?type=in';
			});
			$(document).on('click','#member',function(){
				type.dialog('close');
				member.dialog('open');
			});
			$(document).on('click','#number',function(){
				type.dialog('close');
				number.dialog('open');
			});*/
			/*$(document).on('touchstart','.member .button',function(){
				var index=$('.member .button').index(this);
				$('.member .button').css({'opacity':'1'});
				$('.member .button:eq('+index+')').css({'opacity':'0.5'});
			});
			$(document).on('click','.member .button',function(){
				var index=$('.member .button').index(this);
				if($('.member input[name="memno"]').val().length<10){
					$('.member input[name="memno"]').val($('.member input[name="memno"]').val()+$('.member .button:eq('+index+')').attr('id'));
				}
				else{
				}
			});
			$(document).on('touchend','.member .button',function(){
				var index=$('.member .button').index(this);
				$('.member .button').css({'opacity':'1'});
			});
			$(document).on('touchstart','.member #AC',function(){
				$('.member #AC').css({'opacity':'0.5'});
			});
			$(document).on('click','.member #AC',function(){
				$('.member input[name="memno"]').val('');
			});
			$(document).on('touchend','.member #AC',function(){
				$('.member #AC').css({'opacity':'1'});
			});
			$(document).on('touchstart','.member #BKSP',function(){
				$('.member #BKSP').css({'opacity':'0.5'});
			});
			$(document).on('click','.member #BKSP',function(){
				if($('.member input[name="memno"]').val().length==1){
					$('.member input[name="memno"]').val('');
				}
				else if($('.member input[name="memno"]').val().length>0){
					$('.member input[name="memno"]').val($('.member input[name="memno"]').val().substr(0,($('.member input[name="memno"]').val().length-1)));
				}
				else{
				}
			});
			$(document).on('touchend','.member #BKSP',function(){
				$('.member #BKSP').css({'opacity':'1'});
			});
			$(document).on('click','.member #cancel',function(){
				member.dialog('close');
				//type.dialog('open');
			});
			$(document).on('click','.member #submit',function(){
				if($('.member input[name="memno"]').val().length==10){
					location.href='./index2.php?memno='+$('.member input[name="memno"]').val();
				}
				else{
					//alert('請輸入10碼會員編號');
					location.href='./index2.php?memno=0000000000';
				}
			});*/

			/*$(document).on('touchstart','.number .button',function(){
				var index=$('.number .button').index(this);
				$('.number .button').css({'opacity':'1'});
				$('.number .button:eq('+index+')').css({'opacity':'0.5'});
			});
			$(document).on('click','.number .button',function(){
				var index=$('.number .button').index(this);
				if($('.number input[name="memno"]').val().length<10){
					$('.number input[name="memno"]').val($('.number input[name="memno"]').val()+$('.number .button:eq('+index+')').attr('id'));
				}
				else{
				}
			});
			$(document).on('touchend','.number .button',function(){
				var index=$('.number .button').index(this);
				$('.number .button').css({'opacity':'1'});
			});
			$(document).on('touchstart','.number #AC',function(){
				$('.number #AC').css({'opacity':'0.5'});
			});
			$(document).on('click','.number #AC',function(){
				$('.number input[name="memno"]').val('');
			});
			$(document).on('touchend','.number #AC',function(){
				$('.number #AC').css({'opacity':'1'});
			});
			$(document).on('touchstart','.number #BKSP',function(){
				$('.number #BKSP').css({'opacity':'0.5'});
			});
			$(document).on('click','.number #BKSP',function(){
				if($('.number input[name="memno"]').val().length==1){
					$('.number input[name="memno"]').val('');
				}
				else if($('.number input[name="memno"]').val().length>0){
					$('.number input[name="memno"]').val($('.number input[name="memno"]').val().substr(0,($('.number input[name="memno"]').val().length-1)));
				}
				else{
				}
			});
			$(document).on('touchend','.number #BKSP',function(){
				$('.number #BKSP').css({'opacity':'1'});
			});
			$(document).on('click','.number #cancel',function(){
				number.dialog('close');
				type.dialog('open');
			});
			$(document).on('click','.number #submit',function(){
				if($('.number input[name="memno"]').val().length>0){
					location.href='./index2.php?number='+$('.number input[name="memno"]').val();
				}
				else{
					alert('請輸入桌號。');
				}
			});*/
			/*$(document).on('click','#nonmember',function(){
				location.href='./index2.php?memno=0000000000';
			});*/
		});
	</script>
</head>
<body>	
	<!-- <span class='timeout' style='display:none;'></span> -->
	<!-- <div class='type'>
		<div style='width:700px;height:400px;margin:758px 196.5px'>
			/*<button class='button' id='out' style='background-image:url("./index2img/bigbuttonborder.png");margin:0 0 52px 0;'><div style='font-size:80px;color:#898989;'>外帶</div><div style='font-size:37px;color:#CDCECE;'>To go</div></button>
			<button class='button' id='in' style='background-image:url("./index2img/bigbuttonborder.png");'><div style='font-size:80px;color:#898989;'>內用</div><div style='font-size:37px;color:#CDCECE;'>Dine in</div></button>
			<button class='button' id='nonmember' style='background-image:url("./index2img/bigbuttonborder.png");'><div style='font-size:80px;color:#898989;'>非會員</div><div style='font-size:37px;color:#CDCECE;'>Nonmember</div></button>
			<button class='button' id='member' style='background-image:url("./index2img/bigbuttonborder.png");'><div style='font-size:80px;color:#898989;'>會員</div><div style='font-size:37px;color:#CDCECE;'>Member</div></button>*/
			<button class='button' id='number' style='background-image:url("./index2img/bigbuttonborder.png");background-size:100% 100%;'><div style='font-size:80px;color:#898989;'>桌號</div><div style='font-size:37px;color:#CDCECE;'>Number</div></button>
		</div>
	</div> -->
	<!-- <div class='member'>
		<div style='width:calc(100% - 100px);height:calc(100% - 100px);margin:50px'>
			<input type='text' name='memno' style='width:100%;height:120px;float:left;text-align:right;font-size:100px;margin-bottom:2.5px;' readonly>
			<input type='button' class='button' id='7' value='7' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='8' value='8' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='9' value='9' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='4' value='4' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='5' value='5' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='6' value='6' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='1' value='1' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='2' value='2' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='3' value='3' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<input type='button' class='button' id='0' value='0' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;color:#898989;margin:2.5px;'>
			<button id='AC' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='font-weight:bold;font-size:90px;color:#898989;'>重填</div><div style='font-weight:bold;font-size:45px;color:#CDCECE;'>AC</div></button>
			<button id='BKSP' style='width:calc(100% / 3 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='font-weight:bold;font-size:90px;color:#898989;'>倒退</div><div style='font-weight:bold;font-size:45px;color:#CDCECE;'>BKSP</div></button>
			<button id='cancel' style='width:calc(100% / 2 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='font-weight:bold;font-size:70px;color:#898989;'>取消</div><div style='font-weight:bold;font-size:35px;color:#CDCECE;'>Cancel</div></button>
			<button id='submit' style='width:calc(100% / 2 - 5px);height:calc((100% - 122.5px) / 5 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='font-weight:bold;font-size:70px;color:#898989;'>確認</div><div style='font-weight:bold;font-size:35px;color:#CDCECE;'>Submit</div></button>
		</div>
	</div> -->
	<div class='person'>
		<div style='width:calc(100% - 100px);height:calc(100% - 100px);margin:50px;overflow:hidden;'>
			<input type='hidden' name='tag' value='id'>
			<input type='text' name='id' style='width:calc(50% - 5px);height:80px;float:left;text-align:right;font-size:60px;margin:0 2.5px 2.5px 0;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' placeholder="ID">
			<input type='password' name='pw' style='width:calc(50% - 5px);height:80px;float:left;text-align:right;font-size:60px;margin:0 0 2.5px 2.5px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' placeholder="PASSWORD">
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
			<button id='cancel' style='width:calc(100% / 4 - 5px);height:calc((100% - 82.5px) / 11 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='font-weight:bold;font-size:70px;color:#898989;'>取消</div><div style='font-weight:bold;font-size:35px;color:#CDCECE;'>Cancel</div></button>
			<button id='submit' style='width:calc(100% / 2 - 5px);height:calc((100% - 82.5px) / 11 - 5px);float:left;background-color: transparent;border:3px solid #898989;border-radius: 5px;margin:2.5px;'><div style='font-weight:bold;font-size:70px;color:#898989;'>確認</div><div style='font-weight:bold;font-size:35px;color:#CDCECE;'>Submit</div></button>
		</div>
	</div>
	<!-- <div class='number'>
		<div style='width:351px;height:585px;margin:200px;'>
			<input type='text' name='memno' style='width:100%;height:117px;float:left;text-align:right;font-size:120px;' readonly>
			<input type='button' class='button' id='7' value='7' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='8' value='8' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='9' value='9' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='4' value='4' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='5' value='5' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='6' value='6' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='1' value='1' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='2' value='2' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='3' value='3' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<input type='button' class='button' id='0' value='0' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;color:#898989;'>
			<button id='AC' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;'><div style='font-weight:bold;font-size:45px;color:#898989;'>重填</div><div style='font-weight:bold;font-size:22.5px;color:#CDCECE;'>AC</div></button>
			<button id='BKSP' style='width:117px;height:117px;float:left;background-color: transparent;background-image: url("./index2img/numbutton.png");background-size: 100% 100%;border:0;'><div style='font-weight:bold;font-size:45px;color:#898989;'>倒退</div><div style='font-weight:bold;font-size:22.5px;color:#CDCECE;'>BKSP</div></button>
			<button id='cancel' style='width:351px;height:171px;margin-top:10px;float:left;background-color: transparent;background-image: url("./index2img/buttonborder.png");background-size: 100% 100%;border:0;'><div style='font-weight:bold;font-size:50px;color:#898989;'>取消</div><div style='font-weight:bold;font-size:25px;color:#CDCECE;'>Cancel</div></button>
			<button id='submit' style='width:351px;height:171px;margin-top:10px;float:left;background-color: transparent;background-image: url("./index2img/buttonborder.png");background-size: 100% 100%;border:0;'><div style='font-weight:bold;font-size:50px;color:#898989;'>確認</div><div style='font-weight:bold;font-size:25px;color:#CDCECE;'>Submit</div></button>
		</div>
	</div> -->
	<div id='home' style='text-align:center;margin:500px 0;'>
		<img src='../database/img/click.png?<?php echo date('His'); ?>'>
		<div style='margin:30px 0 0 0;'>
			觸碰螢幕進入點餐畫面<br><span style='font-size:35px;color:#B5B5B5;'>Touch the screen to enter the meal order</span>
		</div>
	</div>
</body>
</html>
