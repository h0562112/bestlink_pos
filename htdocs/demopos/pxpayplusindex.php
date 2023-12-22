<script type="text/javascript" src="../tool/jquery-1.12.4.js"></script>
<script type="text/javascript" src="./lib/api/pxpayplus/pxpayplus_api.js"></script>
<script>
	$(document).ready(function(){
		$('#pass').click(function(){
			var res='';
			if($('input[name="apitype"]:checked').val()=='payment'){
				res=pxpayplus_Payment($('input[name="url"]').val(),$('input[name="merchantcode"]').val(),$('input[name="secrectkey"]').val(),$('input[name="paytoken"]').val(),$('input[name="depcode"]').val(),$('input[name="depname"]').val(),'2022110411',$('input[name="amount"]').val());
				res.done(function(res){
					$('#result').append(res);
					$('#result').append('<br>');
					res=JSON.parse(res);
					$('input[name="mertradeno"]').val(res['mer_trade_no']);
					$('input[name="pxtradeno"]').val(res['px_trade_no']);
				});
			}
			else if($('input[name="apitype"]:checked').val()=='refund'){
				res=pxpayplus_Refund($('input[name="url"]').val(),$('input[name="merchantcode"]').val(),$('input[name="secrectkey"]').val(),$('input[name="depcode"]').val(),$('input[name="depname"]').val(),'2022110422',$('input[name="mertradeno"]').val(),$('input[name="pxtradeno"]').val(),$('input[name="amount"]').val());
				res.done(function(res){
					$('#result').append(res);
					$('#result').append('<br>');
				});
			}
			else if($('input[name="apitype"]:checked').val()=='reversal'){
				res=pxpayplus_Reversal($('input[name="url"]').val(),$('input[name="merchantcode"]').val(),$('input[name="secrectkey"]').val(),$('input[name="paytoken"]').val(),$('input[name="depcode"]').val(),$('input[name="depname"]').val(),'m1',$('input[name="amount"]').val());
				res.done(function(res){
					$('#result').append(res);
					$('#result').append('<br>');
				});
			}
			else if($('input[name="apitype"]:checked').val()=='sign'){
				$.ajax({
					url:'./lib/api/pxpayplus/Sign.php',
					method:'post',
					async:false,
					data:{'data':'abula0001m120221104114531abula000120221104114329abula0001202211041145332020221104114531','key':$('input[name="secrectkey"]').val()},
					dataType:'html',
					success:function(d){
						$('#result').append(d);
						$('#result').append('<br>');
					},
					error:function(e){
						$('#result').append(e);
						$('#result').append('<br>');
					}
				});
			}
			else{
				res=pxpayplus_OrderStatus($('input[name="url"]').val(),$('input[name="merchantcode"]').val(),$('input[name="secrectkey"]').val(),$('input[name="depname"]').val(),1,$('input[name="mertradeno"]').val());
				res.done(function(res){
					$('#result').append(res);
					$('#result').append('<br>');
				});
			}
		});
		$('#clear').click(function(){
			$('#result').html('');
		});
	});
</script>
<?php
$pxpayplus=parse_ini_file('./lib/api/pxpayplus/pxpayplus.ini',true);
?>
<div>
	<table>
		<input type='hidden' name='url' value='<?php echo $pxpayplus['data']['url']; ?>'>
		<input type='hidden' name='merchantcode' value='<?php echo $pxpayplus['data']['merchantcode']; ?>'>
		<input type='hidden' name='secrectkey' value='<?php echo $pxpayplus['data']['secrectkey']; ?>'>
		<tr>
			<td>PayToken：</td>
			<td><input type='text' name="paytoken" value=""></td>
		</tr>
		<tr>
			<td>Depcode：</td>
			<td><input type='text' name="depcode" value=""></td>
		</tr>
		<tr>
			<td>Depname</td>
			<td><input type='text' name="depname" value=""></td>
		</tr>
		<tr>
			<td>Amount:</td>
			<td><input type='text'  name='amount'></td>
		</tr>
		<tr>
			<td>Mertradeno：</td>
			<td><input type='text' name="mertradeno" value=""></td>
		</tr>
		<tr>
			<td>Pxtradeno：</td>
			<td><input type='text' name="pxtradeno" value=""></td>
		</tr>
		<tr>
			<td colspan='2'><label><input type='radio' name='apitype' value='payment' checked>Payment</label>、<label><input type='radio' name='apitype' value='refund'>Refund</label>、<label><input type='radio' name='apitype' value='reversal'>Reversal</label>、<label><input type='radio' name='apitype' value='orderstatus'>OrderStatus</label>、<label><input type='radio' name='apitype' value='sign'>Sign</label></td>
		</tr>
	</table>
	<button id='pass'>測試</button><button id='clear'>清空訊息</button>
</div>
<div id='result'>
</div>