<script type="text/javascript" src="../../../../tool/jquery-1.12.4.js"></script>
<script>
	$(document).ready(function(){
		$('#pass').click(function(){
			if($('input[name="apitype"]:checked').val()=='gettoken'){//登入
				$.ajax({
					url:'./GetToken.php',
					method:'post',
					async:false,
					dataType:'json',
					success:function(d){
						$('#result').append('Get_Token<br>'+d+'<br><br>');
						d=JSON.parse(d);
						$('input[name="token"]').val(d['value']['access_token'])
					},
					error:function(e){
						$('#result').append(e+'<br>');
					}
				});
			}
			else if($('input[name="apitype"]:checked').val()=='checkuser'){//檢查帳號
				$.ajax({
					url:'./CheckPhone.php',
					method:'post',
					async:false,
					data:{'token':$('input[name="token"]').val(),'account':$('input[name="phone"]').val()},
					dataType:'html',
					success:function(d){
						$('#result').append('CheckWalletBalance<br>'+d+'<br><br>');
					},
					error:function(e){
						$('#result').append(e+'<br>');
					}
				});
			}
			else if($('input[name="apitype"]:checked').val()=='givecoins'){//給予雲林斃(退款雲林斃)
				$.ajax({
					url:'./GiveCoins.php',
					method:'post',
					async:false,
					data:{'token':$('input[name="token"]').val(),'receiveraccount':$('input[name="phone"]').val(),'receiveramount':$('input[name="coins"]').val()},
					dataType:'html',
					success:function(d){
						$('#result').append('GiveNubi<br>'+d+'<br><br>');
					},
					error:function(e){
						$('#result').append(e+'<br>');
					}
				});
			}
			else if($('input[name="apitype"]:checked').val()=='usecoins'){//使用雲林斃(扣款雲林斃)
				$.ajax({
					url:'./UseCoins.php',
					method:'post',
					async:false,
					data:{'token':$('input[name="token"]').val(),'account':$('input[name="phone"]').val(),'consumeamount':$('input[name="coins"]').val(),'consumeitems':$('input[name="saleitems"]').val(),'commodityholder':$('input[name="storename"]').val()},
					dataType:'html',
					success:function(d){
						$('#result').append('ConsumeNubi<br>'+d+'<br><br>');
					},
					error:function(e){
						$('#result').append(e+'<br>');
					}
				});
			}
			else{
			}
		});
		$('#clear').click(function(){
			$('#result').html('');
		});
	});
</script>
<div>
	<table>
		<tr>
			<td>Token:</td>
			<td><input type='text' name='token'></td>
		</tr>
		<tr>
			<td>User Phone:</td>
			<td><input type='text' name='phone' value='0978320351'></td>
		</tr>
		<tr>
			<td>Coins:</td>
			<td><input type='text'  name='coins'></td>
		</tr>
		<tr>
			<td>Sale Items:</td>
			<td><input type='text'  name='saleitems'></td>
		</tr>
		<tr>
			<td>Store Name:</td>
			<td><input type='text'  name='storename'></td>
		</tr>
		<tr>
			<td colspan='2'><label><input type='radio' name='apitype' value='gettoken' checked>GetToken</label>、<label><input type='radio' name='apitype' value='checkuser'>CheckWalletBalance</label>、<label><input type='radio' name='apitype' value='givecoins'>GiveNubi</label>、<label><input type='radio' name='apitype' value='usecoins'>ConsumeNubi</label></td>
		</tr>
	</table>
	<button id='pass'>測試</button><button id='clear'>清空訊息</button>
</div>
<div id='result'>
</div>