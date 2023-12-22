<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<script src="../tool/jquery-1.12.4.js"></script>
	<script>
	$(document).ready(function(){
		$('.keybord button#clear').click(function(){
			$('.keybord input[name="num"]').val('0');
		});
		$('.keybord button#back').click(function(){
			if(Number($('.keybord input[name="num"]').val())<10){
				$('.keybord input[name="num"]').val('0');
			}
			else{
				$('.keybord input[name="num"]').val($('.keybord input[name="num"]').val().substr(0,$('.keybord input[name="num"]').val().length-1));
			}
		});
		$('.keybord button#number').click(function(){
			if(Number($('.keybord input[name="num"]').val())==0){
				$('.keybord input[name="num"]').val($(this).val());
			}
			else{
				$('.keybord input[name="num"]').val($('.keybord input[name="num"]').val()+$(this).val());
			}
			if(Number($('.keybord input[name="num"]').val())>=999){
				$('.keybord input[name="num"]').val('999');
			}
			else{
			}
		});
		$('.keybord button#add').click(function(){
			if(Number($('.keybord input[name="num"]').val())==999){
				$('.keybord input[name="num"]').val();
			}
			else{
				$('.keybord input[name="num"]').val(Number($('.keybord input[name="num"]').val())+1);
			}
		});
		$('.keybord button#diff').click(function(){
			if(Number($('.keybord input[name="num"]').val())==0){
				$('.keybord input[name="num"]').val('0');
			}
			else{
				$('.keybord input[name="num"]').val(Number($('.keybord input[name="num"]').val())-1);
			}
		});
		$('.keybord button#send').click(function(){
			$.ajax({
				url:'./writephonenumber.php',
				method:'post',
				async:false,
				data:{'num':$('.keybord input[name="num"]').val()},
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
	<style>
	body {
		width:100vw;
		height:90vh;
		margin:0;
	}
	.keybord {
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
	input,button {
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		border-radius: 5px;
		border:1px solid #a9a9a9;
		margin:1px;
		padding:0 6px;
		font-family: Consolas,Microsoft JhengHei,sans-serif;
	}
	button {
		width:calc(100% / 3 - 2px);
		height:calc(100% / 6 - 2px);
		font-size:calc(100vh / 6 - 10vh);
		float:left;
		background-color: #f3f3f3;
	}
	</style>
	<title>Table+手機叫號鍵盤</title>
</head>
<body>
	<div class='keybord' style='width:100%;height:100%;padding:20px;overflow:hidden;overflow:hidden;'>
		<input type='text' name='num' max='999' style='width:calc(100% - 2px);height:calc(100% / 6 - 2px);font-size:calc(100vh / 7 - 5vh);background-color:#ffffff;text-align:right;' value="<?php
		if(file_exists('../print/now.txt')){
			$file=fopen("../print/now.txt","r") or die("Unable to open file!");
			$now=fread($file,filesize("../print/now.txt"));
			fclose($file);
			echo $now;
		}
		else{
			echo '0';
		}
		?>" readonly>
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
		<button id='add'>+</button>
		<button id='diff'>-</button>
		<button id='clear'>清空</button>
		<button id='back'>倒退</button>
		<button id='send'>叫號</button>
	</div>
</body>
</html>
