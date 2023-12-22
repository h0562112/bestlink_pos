<!doctype html>
<html lang="en">
<head>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<script src="../tool/jquery-1.12.4.js"></script>
	<title>後台登入</title>
	<!-- <link rel="shortcut icon" href="icon.gif" /> -->
	<script>
	function submitenter(myfield,e){
		var keycode;
		if (window.event) keycode = window.event.keyCode;
		else if (e) keycode = e.which;
		else return true;
		if (keycode == 13){
			myfield.form.submit();
			return false;
		}
		else
			return true;
	}
	function initializeHeight(divName){//垂直置中初始化
		document.getElementById(divName).style.marginTop=(window.innerHeight*0.25)+"px";
	}
	$(document).ready(function(){ 
		var D=new Date();
		$(document).on('click','#content #loginbutton',function(){
			if($('#content #ID').val()==""||$('#content #psw').val()==""){
				alert("帳號與密碼 不得為空");
			}
			else {
				if(parseInt(D.getDate())<5){
					var date='0'+(2*parseInt(D.getDate())).toString();
				}
				else{
					var date=2*parseInt(D.getDate());
				}
				if(parseInt(D.getHours())<5){
					var time='0'+(2*parseInt(D.getHours())).toString();
				}
				else{
					var time=2*parseInt(D.getHours());
				}
				$('#content form input[name="date"]').val(date.toString()+time.toString());
				$('#content form').submit();
			}
		});
		$(document).on('keypress','#content #psw',function(event){
			if(event.which=='13'){
				if($('#content #ID').val()==""||$('#content #psw').val()==""){
					alert("帳號與密碼 不得為空");
				}
				else {
					if(parseInt(D.getDate())<5){
						var date='0'+(2*parseInt(D.getDate())).toString();
					}
					else{
						var date=2*parseInt(D.getDate());
					}
					if(parseInt(D.getHours())<5){
						var time='0'+(2*parseInt(D.getHours())).toString();
					}
					else{
						var time=2*parseInt(D.getHours());
					}
					$('#content form input[name="date"]').val(date.toString()+time.toString());
					$('#content form').submit();
				}
			}
			else{
			}
		});
		$(window).resize(function(){  //動態垂直置中
			$("#content").css("margin-top", (window.innerHeight*0.25)+"px"); 
		});
	});
	</script>
	<style>
	body {
		font-family:Microsoft JhengHei,MingLiU;
	}
	input {
		font-family:Microsoft JhengHei,MingLiU;
		border:1px solid #808080;
		height:25px;
	}
	input[type="button"],input[type="reset"] {
		height:29px;
	}
	#content {
		margin-right:auto;
		margin-bottom:auto;
		margin-left:auto;
		display:table;
	}
	.labeltd {
		width:48px;
	}
	#ID {
	   width:160px;
	}

	#psw {
	   width:160px;
	}
	#loginbutton {
		width:100%;
		border:1px solid #808080;
		background:#808080;
		color:#ffffff;
	}
	</style>
</head>
<body>
	<div id="content" style='border:1px solid #808080;width:300px;height:300px;padding:0 40px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		<script type="text/javascript">initializeHeight('content');</script>
		<form method='post' action='loginmethod.php' name='loginForm' style='width: 220px;height: 300px;position: absolute;'>
			<input type='hidden' id='temp'>
			<input type='hidden' name='company' value='<?php if(isset($_GET['company']))echo $_GET['company']; ?>'>
			<input type='hidden' name='date' value=''>
			<table>
				<caption><h1>雲端平台登入</h1></caption>
				<tr>
					<td class='labeltd'>帳號</td>
					<td><input type='text' id='ID' name='ID' title="請輸入帳號" placeholder="請輸入帳號" autofocus></td>
				</tr>
				<tr>
					<td class='labeltd'>密碼</td>
					<td><input type='password' id='psw' name='psw' title="請輸入密碼" placeholder="請輸入密碼" ></td><!-- onkeypress='return submitenter(this,event)' -->
				</tr>
				<tr>
					<td></td>
					<td><input id='loginbutton' type='button' value='登入'></td>
				</tr>
				<tr>
					<td></td>
					<td style='padding-top:10px;'><input type='reset' style='width:100%;border:1px solid #808080;background:#ffffff;color:#191919;' value='取消'></td>
				</tr>
			</table>
			<img src='./img/tableplus.png' style='width:218px;height:62px;margin:0 auto;position: absolute;bottom: 3px;'>
		</form>
	</div>
</body>
</html>
