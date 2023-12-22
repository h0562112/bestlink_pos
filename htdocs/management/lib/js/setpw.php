<?php
session_start();
if(isset($_POST['lan'])&&$_POST['lan']!=''){
	if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
		$interface=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('../../lan/interface1.ini')){
		$interface=parse_ini_file('../../lan/interface1.ini',true);
	}
	else{
		$interface='-1';
	}
}
?>
<script>
pwedit=$('.pwedit').tabs();
$(document).ready(function(){
	if($('.management').length>0){
		$('.pwedit #setpw .rechangepw').css({'display':''});
	}
	else{
	}
	$(document).on('click','#rechangepw',function(){
		$.ajax({
			url:'./lib/js/rechangepw.ajax.php',
			//method:'post',
			async:false,
			//data:$('.pwedit #setpw #pwdetail').serialize(),
			dataType:'html',
			success:function(d){
				if(d=='success'){
					$('#logout').trigger('click');
				}
				else{
					$('.mys').html("<div style='width:90%;font-size:3vw;text-align:center;margin:0 auto;'>"+d+"</div>");
					mys.dialog('open');
				}
				//console.log(d);
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('keypress','.pwedit #setpw input[name="oldpw"]',function(event){
		if(event.which=='13'){
			$('.pwedit #setpw input[name="newpw"]').focus();
		}
		else{
		}
	});
	$(document).on('keypress','.pwedit #setpw input[name="newpw"]',function(event){
		if(event.which=='13'){
			$('.pwedit #setpw input[name="newpw2"]').focus();
		}
		else{
		}
	});
	$(document).on('keypress','.pwedit #setpw input[name="newpw2"]',function(event){
		if(event.which=='13'){
			$('.pwedit #setpw #send').trigger('click');
		}
		else{
		}
	});
	$(document).on('click','#send',function(){
		if($('.pwedit #setpw input[name="newpw"]').val()!=$('.pwedit #setpw input[name="newpw2"]').val()){
			$('.mys').html("<div style='width:90%;font-size:3vw;text-align:center;margin:0 auto;'><?php if($interface!='-1'&&isset($interface['name']['entersecpwerror']))echo $interface['name']['entersecpwerror'];else echo '新密碼輸入錯誤'; ?></div>");
			mys.dialog('open');
		}
		else{
			$.ajax({
				url:'./lib/js/change.pw.php',
				method:'post',
				async:false,
				data:$('.pwedit #setpw #pwdetail').serialize(),
				dataType:'html',
				success:function(d){
					if(d=='oldpwerror'){
						$('.mys').html("<div style='width:90%;font-size:3vw;text-align:center;margin:0 auto;'><?php if($interface!='-1'&&isset($interface['name']['enteroldpwerror']))echo $interface['name']['enteroldpwerror'];else echo '密碼驗證錯誤'; ?></div>");
						mys.dialog('open');
					}
					else if(d=='success'){
						$('#logout').trigger('click');
					}
					else{
						$('.mys').html("<div style='width:90%;font-size:3vw;text-align:center;margin:0 auto;'>"+d+"</div>");
						mys.dialog('open');
					}
					//console.log(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
		}
	});
});
</script>
<div class='pwedit' style="overflow:hidden;margin-bottom:3px;">
	<ul style='width:100%;float:left;-webkit-box-sizing: efborder-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		<li><a href='#setpw'><?php if($interface!='-1'&&isset($interface['name']['editpw']))echo $interface['name']['editpw'];else echo "修改密碼"; ?></a></li>
	</ul>
	<div id='setpw' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['editpw'])){ echo $interface['name']['editpw']; } else{ echo '修改密碼'; } ?></center></h1>
		<div class='rechangepw' style='display:none;'>
			<button id='rechangepw' style='margin:10px;'><div><?php if($interface!='-1'&&isset($interface['name']['rechangepw']))echo $interface['name']['rechangepw'];else echo "密碼還原"; ?></div></button>
		</div>
		<form id='pwdetail' style='float:left;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<input type='hidden' name='lan' value='<?php if(isset($_POST['lan'])&&$_POST['lan']!='')echo $_POST['lan'];else echo '1'; ?>'>
			<table>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['oldpw'])){ echo $interface['name']['oldpw']; } else{ echo '舊密碼'; } ?></td>
					<td><input type='password' name='oldpw' autofocus></td>
				</tr>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['newpw'])){ echo $interface['name']['newpw']; } else{ echo '新密碼'; } ?></td>
					<td><input type='password' name='newpw'></td>
				</tr>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['newpw2'])){ echo $interface['name']['newpw2']; } else{ echo '再次輸入'; } ?></td>
					<td><input type='password' name='newpw2'></td>
				</tr>
				<tr>
					<td colspan='2'><input type='button' id='send' value='<?php if($interface!='-1'&&isset($interface['name']['send'])){ echo $interface['name']['send']; } else{ echo '送出'; } ?>'></td>
				</tr>
			</table>
		</form>
	</div>
</div>