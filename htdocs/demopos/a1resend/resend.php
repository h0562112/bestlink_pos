<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>補上傳A1ERP</title>
	<script type="text/javascript" src="../../tool/jquery-1.12.4.js?<?php echo date('His'); ?>"></script>
	<script>
		$(document).ready(function(){
			function async_for(date,consecnumber,lastconsecnumber){
				var res=$.Deferred();
				$.ajax({
					url:'./resend.data.ajax.php',
					method:'post',
					data:{'date':date,'consecnumber':consecnumber},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('.result').append('<div>'+d+'</div><br><br>');
						res.resolve(date,parseInt(consecnumber)+1,lastconsecnumber);
					},
					error:function(e){
						//console.log(e);
						//$('.sysmeg #message').html($('#depcode option:selected').text()+'ERROR:'+e);
						$('.result').append('<div>ERROR! '+e+'</div><br><br>');
						//$('.modal').css({'display':'block'});
						//$('.sysmeg').css({'display':'block'});
						res.resolve(date,parseInt(consecnumber)+1,lastconsecnumber);
					}
				});
				res.done(function(date,consecnumber,lastconsecnumber){
					if(consecnumber<=lastconsecnumber){
						async_for(date,consecnumber,lastconsecnumber);
					}
					else{
						$('.singleempty').prop('disabled',false);
						$('.nowsingleempty').prop('disabled',false);
						$('.lastoneofall').prop('disabled',false);
						$('.allempty').prop('disabled',false);
					}
				});
			}
			function async_vfor(date,consecnumber,lastconsecnumber){
				var res=$.Deferred();
				$.ajax({
					url:'./resend.vdata.ajax.php',
					method:'post',
					data:{'date':date,'consecnumber':consecnumber},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('.result').append('<div>'+d+'</div><br><br>');
						res.resolve(date,parseInt(consecnumber)+1,lastconsecnumber);
					},
					error:function(e){
						//console.log(e);
						//$('.sysmeg #message').html($('#depcode option:selected').text()+'ERROR:'+e);
						$('.result').append('<div>ERROR! '+e+'</div><br><br>');
						//$('.modal').css({'display':'block'});
						//$('.sysmeg').css({'display':'block'});
						res.resolve(date,parseInt(consecnumber)+1,lastconsecnumber);
					}
				});
				res.done(function(date,consecnumber,lastconsecnumber){
					if(consecnumber<=lastconsecnumber){
						async_vfor(date,consecnumber,lastconsecnumber);
					}
					else{
						$('.singleempty').prop('disabled',false);
						$('.nowsingleempty').prop('disabled',false);
						$('.lastoneofall').prop('disabled',false);
						$('.allempty').prop('disabled',false);
					}
				});
			}
			$('.condition .send').click(function(){
				$('.result').html('');
				$.ajax({
					url:'./getlistdat.ajax.php',
					method:'post',
					async:false,
					data:{'date':$('.condition input[name="date"]').val()},
					dataType:'json',
					success:function(d){
						console.log(d);
						async_for(d['date'],d['consecnumber'],d['lastconsecnumber']);
						//async_vfor(d['date'],d['consecnumber'],d['lastconsecnumber']);
					},
					error:function(e){
						console.log(e);
					}
				});
			});
			$('.condition .vsend').click(function(){
				$('.result').html('');
				$.ajax({
					url:'./getlistdat.ajax.php',
					method:'post',
					async:false,
					data:{'date':$('.condition input[name="date"]').val()},
					dataType:'json',
					success:function(d){
						console.log(d);
						//async_for(d['date'],d['consecnumber'],d['lastconsecnumber']);
						async_vfor(d['date'],d['consecnumber'],d['lastconsecnumber']);
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
	<form class='condition'>
		<table>
			<tr>
				<td>營業日期：<input type="date" name="date" value="<?php echo date('Y-m-d'); ?>"></td>
				<td><input type="button" class='send' value='補上傳'></td>
				<td><input type="button" class='vsend' value='消退補上傳'></td>
			</tr>
		</table>
	</form>
	<div class="result">
		
	</div>
</body>
</html>
