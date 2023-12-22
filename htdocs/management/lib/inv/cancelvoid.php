<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script src="../../../tool/jquery-1.12.4.js"></script>
	<script src="../../../tool/ui/1.12.1/jquery-ui.js"></script>
	<script src="../../../tool/ui/1.12.1/datepicker-zh-TW.js"></script>
	<title>取消作廢</title>
	<script>
	$(document).ready(function(){
		$('#send').click(function(){
			if($('#company').val()!=''&&$('#dpe').val()!=''&&$('#month').val()!=''&&$('#invnumber').val!=''){
				$.ajax({
					url:'./printC0701.php',
					method:'post',
					async:false,
					data:{'company':$('#company').val(),'dep':$('#dep').val(),'month':$('#month').val(),'invnumber':$('#invnumber').val()},
					dataType:'html',
					success:function(d){
						alert(d);
					},
					error:function(e){
						alert(e);
					}
				});
			}
			else{
				alert('請將資料完整填寫');
			}
		});
	});
	</script>
</head>
<body>
	<form>
		<table>
			<tr>
				<td>體系代號</td>
				<td><input type='text' id='company'></td>
			</tr>
			<tr>
				<td>門市代號</td>
				<td><input type='text' id='dep'></td>
			</tr>
			<tr>
				<td>月份</td>
				<td><input type='text' id='month' readonly value='<?php
				date_default_timezone_set('Asia/Taipei');
				echo date('Y');
				if(intval(date('m'))%2==0){
					$m=date('m');
				}
				else{
					$m=intval(date('m'))+1;
				}
				if(strlen($m)<2){
					echo '0'.$m;
				}
				else{
					echo $m;
				}
				?>'></td>
			</tr>
			<tr>
				<td>發票號</td>
				<td><input type='text' id='invnumber'></td>
			</tr>
			<tr>
				<td colspan='2'><input type='button' id='send' value='送出'></td>
			</tr>
		</table>
	</form>
</body>
</html>
