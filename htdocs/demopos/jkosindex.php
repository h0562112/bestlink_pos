<script type="text/javascript" src="../tool/jquery-1.12.4.js"></script>
<script type="text/javascript" src="./lib/api/jkos/jkos_api.js"></script>
<script>
	$(document).ready(function(){
		$('#pass').click(function(){
			var res='';
			if($('input[name="apitype"]:checked').val()=='payment'){
				$('#content').append('payment<br><br>');
				res=Payment($('input[name="cardtoken"]').val(),$('input[name="tradeamount"]').val());
			}
			else if($('input[name="apitype"]:checked').val()=='inquiry'){
				$('#content').append('inquiry');
				res=Inquiry();
			}
			else if($('input[name="apitype"]:checked').val()=='cancel'){
				$('#content').append('cancel');
				res=Cancel($('input[name="cardtoken"]').val(),$('input[name="tradeamount"]').val(),$('input[name="merchanttradeno"]').val());
			}
			else{
			}
			res.done(function(res){
				$('#content').append(res);
				$('#content').append('<br>');
			});
			$('#result').scrollTop($('#content').height());
		});
		$('#clear').click(function(){
			$('#content').html('');
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
			<td>MerchantTradeNo</td>
			<td><input type='text'  name='merchanttradeno'></td>
		</tr>
		<tr>
			<td colspan='2'><label><input type='radio' name='apitype' value='payment' checked>Payment</label>、<label><input type='radio' name='apitype' value='inquiry'>Inquiry</label>、<label><input type='radio' name='apitype' value='cancel'>Cancel</label></td>
		</tr>
	</table>
	<button id='pass'>測試</button><button id='clear'>清空訊息</button>
</div>
<div id='result' style="height:50%;overflow:auto;">
	<div id="content"></div>
</div>