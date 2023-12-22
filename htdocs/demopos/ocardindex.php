<script type="text/javascript" src="../tool/jquery-1.12.4.js"></script>
<script type="text/javascript" src="./lib/api/ocard/ocard_api.js"></script>
<script>
	$(document).ready(function(){
		$('.submit').click(function(){
			if($('input[name="method"]:checked').val()=='auth'){
				res=ocard_auth($('input[name="url"]').val(),$('input[name="key"]').val(),$('input[name="secret"]').val());
				res.done(function(res){
					//console.log(res);
					if(typeof res['code']!=='undefined'&&res['code']=='500'){
						$('input[name="uid"]').val(res['data']['uid']);
						$('input[name="token"]').val(res['data']['token']);
					}
					else{
						$('input[name="uid"]').val('');
						$('input[name="token"]').val('');
					}
					res=JSON.stringify(res);
					$('.result').append(res);
					$('.result').append('<br>');
					//res=JSON.parse(res);
				});
			}
			else if($('input[name="method"]:checked').val()=='getProfile'){
				res=ocard_getProfile($('input[name="url"]').val(),$('input[name="uid"]').val(),$('input[name="token"]').val(),$('input[name="searchtype"]:checked').val(),$('input[name="searchdata"]').val());
				res.done(function(res){
					//console.log(res);
					res=JSON.stringify(res);
					$('.result').append(res);
					$('.result').append('<br>');
					//res=JSON.parse(res);
				});
			}
			else if($('input[name="method"]:checked').val()=='giveVip'){
				res=ocard_giveVip($('input[name="url"]').val(),$('input[name="uid"]').val(),$('input[name="token"]').val(),$('input[name="cell"]').val(),$('input[name="name"]').val());
				res.done(function(res){
					//console.log(res);
					res=JSON.stringify(res);
					$('.result').append(res);
					$('.result').append('<br>');
					//res=JSON.parse(res);
				});
			}
			else if($('input[name="method"]:checked').val()=='checkRedeem'){
				res=ocard_checkRedeem($('input[name="url"]').val(),$('input[name="uid"]').val(),$('input[name="token"]').val(),$('input[name="code"]').val(),1,'',0);
				res.done(function(res){
					//console.log(res);
					res=JSON.stringify(res);
					$('.result').append(res);
					$('.result').append('<br>');
					//res=JSON.parse(res);
				});
			}
			else{
			}
		});
	});
</script>
<?php
$ocard=parse_ini_file('./lib/api/ocard/ocard.ini',true);
?>
<div>
	<table>
		<tr>
			<td>method:</td>
			<td><label><input type='radio' name='method' value='auth' checked>auth</label>、<label><input type='radio' name='method' value='getProfile'>getProfile</label>、<label><input type='radio' name='method' value='giveVip'>giveVip</label>、<label><input type='radio' name='method' value='checkRedeem'>checkRedeem</label></td>
		</tr>
		<tr>
			<td>url:</td>
			<td><input type='text' name="url" value="<?php echo $ocard['init']['url']; ?>"></td>
		</tr>
		<tr>
			<td>key:</td>
			<td><input type='text' name="key" value="<?php echo $ocard['init']['key']; ?>"></td>
		</tr>
		<tr>
			<td>secret:</td>
			<td><input type='text' name="secret" value="<?php echo $ocard['init']['secret']; ?>"></td>
		</tr>
		<tr>
			<td>uid:</td>
			<td><input type='text' name="uid"></td>
		</tr>
		<tr>
			<td>token:</td>
			<td><input type='text' name="token"></td>
		</tr>
		<tr>
			<td>查詢依據:</td>
			<td><label><input type='radio' name='searchtype' value='cell' checked>電話</label>、<label><input type='radio' name='searchtype' value='code'>條碼</label></td>
		</tr>
		<tr>
			<td>查詢內容:</td>
			<td><input type='text' name="searchdata"></td>
		</tr>
		<tr>
			<td>電話:</td>
			<td><input type='text' name="cell"></td>
		</tr>
		<tr>
			<td>姓名:</td>
			<td><input type='text' name="name"></td>
		</tr>
		<tr>
			<td>條碼:</td>
			<td><input type='text' name="code"></td>
		</tr>
	</table>
	<button class='submit'>送出</button>
</div>
<div class='result'>
</div>