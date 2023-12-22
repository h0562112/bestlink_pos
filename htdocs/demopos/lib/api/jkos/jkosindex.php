<script type="text/javascript" src="../tool/jquery-1.12.4.js"></script>
<script type="text/javascript" src="./lib/api/jkos/jkos_api.js"></script>
<script>
	$(document).ready(function(){
		$('#pass').click(function(){
			$('#result').append(Payment($('input[name="cardtoken"]').val(),$('input[name="tradeamount"]').val()));
			$('#result').append('<br>');
		});
		$('#clear').click(function(){
			$('#result').html('');
		});
	});
</script>
<div>
	<table>
		<tr>
			<td>CardToken:</td>
			<td><input type='text' name='cardtoken'></td>
		</tr>
		<tr>
			<td>TradeAmount:</td>
			<td><input type='text'  name='tradeamount'></td>
		</tr>
		<tr>
			<td colspan='2'><label><input type='radio' name='apitype' value='payment' checked>Payment</label></td>
		</tr>
	</table>
	<button id='pass'>測試</button><button id='clear'>清空訊息</button>
</div>
<div id='result'>
</div>