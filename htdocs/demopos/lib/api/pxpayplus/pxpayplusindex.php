<script type="text/javascript" src="../tool/jquery-1.12.4.js"></script>
<script type="text/javascript" src="./lib/api/jkos/jkos_api.js"></script>
<script>
	$(document).ready(function(){
		$('#check').click(function(){
			$('#result').append(Sign($('input[name="String"]').val(),$('input[name="secrectkey"]').val()));
			$('#result').append('<br>');
		});
		$('#pass').click(function(){
			$('#result').append(Payment($('input[name="cardtoken"]').val(),$('input[name="tradeamount"]').val()));
			$('#result').append('<br>');
		});
		$('#clear').click(function(){
			$('#result').html('');
		});
	});
</script>
<?php
$pxpayplus=parse_ini_file('./pxpayplus.ini',true);
?>
<div>
	<table>
		<input type='hidden' name='secrectkey' value='<?php echo $pxpayplus['data']['secrectkey']; ?>'>
		<tr>
			<td>String:</td>
			<td><input type='text' name='String' value='120210123000000120180301230111'></td>
		</tr>
		<tr>
			<td>TradeAmount:</td>
			<td><input type='text'  name='tradeamount'></td>
		</tr>
		<tr>
			<td colspan='2'><label><input type='radio' name='apitype' value='payment' checked>Payment</label></td>
		</tr>
	</table>
	<button id='check'>檢查加密</button><button id='pass'>測試</button><button id='clear'>清空訊息</button>
</div>
<div id='result'>
</div>