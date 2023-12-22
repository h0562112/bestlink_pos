<!DOCTYPE html>
<?php
/*if(isset($_GET['id'])){
}
else{
	echo "<script>alert('id:".isset($_GET['id'])."');location.href='../login/';</script>";
}*/

include '../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$year=(intval(date('Y'))-1911);
if(intval(date('m'))%2==0){
	$month=date('m');
}
else{
	$month=intval(date('m'))+1;
}
if(strlen($month)<2){
	$month='0'.$month;
}
$content=parse_ini_file('../database/setup.ini',true);
$len=parse_ini_file('./key.ini',true);
$conn=sqlconnect("../database","menu.db","","","","sqlite");
$sql='SELECT banno FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND dateTime="'.$year.$month.'" ORDER BY banno LIMIT 1';
$table=sqlquery($conn,$sql,'sqlite');
$sql='SELECT COUNT(banno) as number FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND dateTime="'.$year.$month.'"';
$count=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src='https://code.jquery.com/jquery-1.12.4.js'></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<meta name="viewport" id='viewport' content="user-scalable=0,initial-scale=1,maximum-scale=1,width=device-width">
<title>電子發票系統</title>
<script>
$(document).ready(function(){
	var contitle='載具號碼';
	dialog=$('#sysmeg1').dialog({
		autoOpen:false,
		height:600,
		width:800,
		modal:true,
		title:'系統提示'
	});
	fun=$('.fun').dialog({
		autoOpen:false,
		height:840,
		width:800,
		modal:true,
		title:'功能區',
		open:function(){
			fun.dialog('option','title','功能區');
			$('.fun').html("<table style='width:100%;height:100%;'><tr><td style='width:25%;height:20%;'><input type='button' id='container' style='width:100%;height:100%;' value='手機載具'></td><td style='width:25%;height:20%;'><input type='button' id='predaydata' style='width:100%;height:100%;' value='發票彙總'></td></tr><tr><td style='width:25%;height:20%;'><input type='button' id='delete' style='width:100%;height:100%;' value='作廢發票'></td><!-- <tr><td style='width:25%;'><input type='button' id='reopen' style='width:100%;height:100%;' value='重開發票'></td></tr> --><td style='width:25%;height:20%;'><input type='button' id='reprint' style='width:100%;height:100%;' value='補印發票'></td></tr><tr><td style='width:25%;height:20%;'><input type='button' id='cancel' style='width:100%;height:100%;' value='返回'></td><td style='width:25%;height:20%;'><input type='button' id='exit' style='width:100%;height:100%;' value='離開系統'></td></tr></table>");
		}
	});
	<?php
	if(intval($count[0]['number'])<intval($content['basic']['safe'])){
	?>
		$('#sysmeg1').html('<center>發票存量已低於安全量</center><input type="button" style="width:130px;height:150px;float:right;" value="確認" onclick="dialog.dialog(\'close\');">');
		dialog.dialog('open');
	<?php
	}
	echo '$(document).on("click","#addinv",function(){var mywin=window.open(\'ban://company='.$content['basic']['company'].',dep='.$content['basic']['story'].',cmd=add,date='.$year.$month.'\',\'\',\'width=1px,height=1px\');});';
	?>

	$('body').css({'height':$(window).height()});
	$(window).resize(function(){
		$('body').css({'height':$(window).height()});
		if($(window).width()>=1366){
			$('.funtable .button').css({'font-size':'50px'});
		}
		else if($(window).width()<900){
			$('.funtable .button').css({'font-size':'25px'});
		}
		else{
		}
	});
	if($('input[name="taxtype"]').val()=='1'){
		$('#tax').attr('disabled',true);
	}
	else{
		$('.viewtr:eq(3)').after("<div class='viewtr'><div class='viewtitle'>稅金</div><div><input type='text' id='viewinput' name='tax' value='0' style='text-align:right;background-color:#f7f7f7;' readonly></div></div>");
		$('#notax').attr('disabled',true);
	}
	$('#back').click(function(){
		if($('input[name="'+$('input[name="target"]').val()+'"]').val().length==1){
			if($('input[name="target"]').val()=='ban'){
				$('input[name="'+$('input[name="target"]').val()+'"]').val('');
			}
			else{
				$('input[name="'+$('input[name="target"]').val()+'"]').val('0');
			}
		}
		else{
			$('input[name="'+$('input[name="target"]').val()+'"]').val($('input[name="'+$('input[name="target"]').val()+'"]').val().substr(0,$('input[name="'+$('input[name="target"]').val()+'"]').val().length-1));
		}
	});
	$('#ac').click(function(){
		if($('input[name="target"]').val()=='ban'){
			$('input[name="'+$('input[name="target"]').val()+'"]').val('');
		}
		else{
			$('input[name="'+$('input[name="target"]').val()+'"]').val('0');
		}
		if($('input[name="target"]').val()=='view'){
			$('input[name="sign"]').val('1');
			$('input[name="temp"]').val('0');
			if($('input[name="taxtype"]').val()=='1'){
			}
			else{
				$('input[name="tax"]').val('0');
			}
		}
		else{
		}
	});
	$('input[id^=input]').click(function(){
		if($('input[name="target"]').val()=='ban'){//檢查focus是否在統編
			var maxlen=8;
		}
		else{
			var maxlen=<?php echo $len['numlen']['len']; ?>;
		}
		if($('input[name="sign"]').val()=='0'){//如果已按等於、計算稅金或切換輸入統編
			$('input[name="temp"]').val('0');
			$('input[name="sign"]').val('1');
		}
		else{
		}
		if($('input[name="'+$('input[name="target"]').val()+'"]').val().length==maxlen){//檢察focus input是否超過容許長度
			
		}
		else if($('input[name="target"]').val()=='ban'){
			$('input[name="'+$('input[name="target"]').val()+'"]').val($('input[name="'+$('input[name="target"]').val()+'"]').val()+$('input[id^=input]:eq('+$('input[id^=input]').index(this)+')').val());
		}
		else{
			if($('input[name="'+$('input[name="target"]').val()+'"]').val()=='0'){//檢察統編是否尚未輸入或消費金額部分為0
				if($('input[name="new"]').val()=='1'){//主要用於消費金額部分
					$('input[name="new"]').val('0');
				}
				else{
				}
				$('input[name="'+$('input[name="target"]').val()+'"]').val($('input[id^=input]:eq('+$('input[id^=input]').index(this)+')').val());
			}
			else{
				if($('input[name="new"]').val()=='1'){
					$('input[name="new"]').val('0');
					$('input[name="'+$('input[name="target"]').val()+'"]').val($('input[id^=input]:eq('+$('input[id^=input]').index(this)+')').val());
				}
				else{
					$('input[name="'+$('input[name="target"]').val()+'"]').val($('input[name="'+$('input[name="target"]').val()+'"]').val()+$('input[id^=input]:eq('+$('input[id^=input]').index(this)+')').val());
				}
			}
		}
	});
	function sum(){
		if($.isNumeric(parseInt($('input[name="view"]').val()))){
			if($('input[name="new"]').val()=='1'||$('input[name="target"]').val()=='ban'){
				$('input[name="temp"]').val(parseInt($('input[name="view"]').val()));
			}
			else{
				$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="view"]').val())*parseInt($('input[name="sign"]').val()));
			}
		}
		else{
			//$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="view"]').val())*parseInt($('input[name="sign"]').val()));
		}
		$('input[name="new"]').val('1');
		$('input[name="tax"]').val(Math.round(parseInt($('input[name="temp"]').val())*0.05));
		$('input[name="view"]').val($('input[name="temp"]').val());
	}
	function submitform(){
		$(".targetform").submit();
		dialog.dialog('close');
	}
	$('#plus').click(function(){
		//$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="view"]').val())*parseInt($('input[name="sign"]').val()));
		sum();
		$('input[name="sign"]').val('1');
	});
	$('#diff').click(function(){
		//$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="view"]').val())*parseInt($('input[name="sign"]').val()));
		sum();
		$('input[name="sign"]').val('-1');
	});
	$('#same').click(function(){
		//$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="view"]').val())*parseInt($('input[name="sign"]').val()));
		sum();
		$('input[name="sign"]').val('0');
	});
	$('input[name="view"]').click(function(){
		sum();
		if($('input[name="target"]').val()=="ban"){
			$('#plus').attr('disabled',false);
			$('#diff').attr('disabled',false);
			$('#same').attr('disabled',false);
			$('input[name="view"]').css({'background-color':'#ffffff'});
			$('input[name="ban"]').css({'background-color':'#f7f7f7'});
			$('input[name="target"]').val('view');
		}
		else{
		}
	});
	$('input[name="ban"]').click(function(){
		sum();
		if($('input[name="target"]').val()=="view"){
			$('#plus').attr('disabled',true);
			$('#diff').attr('disabled',true);
			$('#same').attr('disabled',true);
			$('input[name="view"]').css({'background-color':'#f7f7f7'});
			$('input[name="ban"]').css({'background-color':'#ffffff'});
			$('input[name="target"]').val('ban');
		}
		else{
		}
	});
	$('#submitbutton').click(function(){
		var istrue=1;
		sum();
		var tempinv="<form method='post' action='./create.php' class='targetform'><table><caption>暫結發票資訊</caption><tr><td>發票號碼</td><td><input type='text' name='tempinv' value='"+$('.viewtable input[name="inv"]').val()+"' readonly></td></tr><tr><td>統編</td><td><input type='text' name='tempban' value='"+$('.viewtable input[name="ban"]').val()+"' readonly>";
		if($('.viewtable input[name="ban"]').val().length>0){
			if(parseInt($('.viewtable input[name="ban"]').val())==0){
			}
			else if($('.viewtable input[name="ban"]').val().length!=8){
				tempinv=tempinv+'<br><font color="#ff3399">輸入之統編有誤</font>';
				istrue=0;
			}
			else{
				var ban=$('.viewtable input[name="ban"]').val();
				var value=0;
				var t=[1,2,1,2,1,2,4,1];
				var temp=0;
				for(var i=0;i<8;i++){
					temp=parseInt(ban.substr(i,1))*t[i];
					console.log('temp='+ban.substr(i,1)+'*'+t[i]+'='+temp);
					if(parseInt(temp)>=10){
						console.log('value='+value+'+'+temp.toString().substr(0,1)+'+'+temp.toString().substr(1,1)+'='+(parseInt(value)+parseInt(temp.toString().substr(0,1))+parseInt(temp.toString().substr(1,1))));
						value=parseInt(value)+parseInt(temp.toString().substr(0,1))+parseInt(temp.toString().substr(1,1));
					}
					else{
						console.log('value='+value+'+'+temp+'='+(parseInt(value)+parseInt(temp)));
						value=parseInt(value)+parseInt(temp);
					}
				}
				if(value%10==0){
				}
				else if(parseInt(ban.substr(6,1))==7&&(value+1)%10==0){
				}
				else{
					tempinv=tempinv+'<br><font color="#ff3399">輸入之統編有誤</font>';
					istrue=0;
				}
			}
		}
		else{
		}
		tempinv=tempinv+"</td></tr><tr><td>消費總金額</td><td><input type='text' name='total'";
		if($('input[name="taxtype"]').val()==1){
			tempinv=tempinv+" value='"+$('.viewtable input[name="view"]').val()+"'";
			if(parseFloat($('.viewtable input[name="view"]').val())<=0){
				var nage=1;
			}
			else{
				var nage=0;
			}
		}
		else{
			tempinv=tempinv+" value='"+(parseInt($('.viewtable input[name="view"]').val())+parseInt($('.viewtable input[name="tax"]').val()))+"'";
			if(parseFloat((parseInt($('.viewtable input[name="view"]').val())+parseInt($('.viewtable input[name="tax"]').val())))<=0){
				var nage=1;
			}
			else{
				var nage=0;
			}
		}
		tempinv=tempinv+" readonly></td></tr><tr><td>"+contitle+"</td><td><input type='text' name='tempcontainer' value='"+$('input[name="usecontainer"]').val()+"' readonly></td></tr></table><form>";
		if($('.viewtable input[name="inv"]').val().length==0||parseInt($('.viewtable input[name="view"]').val())==0||istrue==0){
			tempinv=tempinv+"<input type='button' style='height:150px;width:130px;margin:10px;' value='取消' onclick='dialog.dialog(\"close\");'>";
		}
		else if(nage==1){
			tempinv=tempinv+"<input type='button' style='height:150px;width:130px;margin:10px;' value='取消' onclick='dialog.dialog(\"close\");'>";
		}
		else{
			tempinv=tempinv+"<input type='button' style='height:150px;width:130px;margin:10px;' value='繼續' onclick='$(\".targetform\").submit();'><input type='button' style='height:150px;width:130px;margin:10px;' value='取消' onclick='dialog.dialog(\"close\");'>";
		}

		$('#sysmeg1').html(tempinv);
		dialog.dialog('open');
	});
	$('#tax').click(function(){
		$('input[name="taxtype"]').val('1');
		$('#tax').attr('disabled',true);
		$('#notax').attr('disabled',false);
		if($('.viewtr').length>4){
			$('.viewtr:eq(4)').remove();
		}
		else{
		}
		$('#same').val('=');
	});
	$('#notax').click(function(){
		$('input[name="taxtype"]').val('0');
		$('#tax').attr('disabled',false);
		$('#notax').attr('disabled',true);
		$('.viewtr:eq(3)').after("<div class='viewtr'><div class='viewtitle'>稅金</div><div><input type='text' id='viewinput' name='tax' value='0' style='text-align:right;background-color:#f7f7f7;' readonly></div></div>");
		$('input[name="tax"]').val(Math.round(parseInt($('input[name="view"]').val())*0.05));
		$('#same').val('計算稅金');
	});
	$('.funbutton').click(function(){
		fun.dialog('open');
	});
	$(document).on('click','#cancel',function(){
		fun.dialog('close');
	});
	$(document).on('click','#container',function(){
		fun.dialog('option','title','手機載具');
		$('.fun').html("<table style='width:100%;height:100%;'><tr><td>手機載具：<input type='text' id='coninput' name='container' value='' style='text-transform:uppercase;width:calc(100% - 220px);' autofocus><input type='button' id='review' style='height:150px;width:20%;float:right;margin:10px;' value='返回'><input type='button' id='consubmit' style='height:150px;width:20%;float:right;margin:10px;' value='確定'></td></tr></table>");
	});
	$(document).on('keypress','#coninput',function(event){
		if(event.which==13){
			$('input[name="usecontainer"]').val($('.fun input[name="container"]').val());
			fun.dialog('close');
		}
		else{
		}
	});
	$(document).on('click','#consubmit',function(){
		if($('.fun input[name="container"]').val().substr(0,1)=='/'){
			contitle='手機載具';
			$('input[name="usecontainer"]').val($('.fun input[name="container"]').val().toUpperCase());
			fun.dialog('close');
		}
		else{
			contitle='載具號碼';
			$('input[name="usecontainer"]').val('');
			$('#sysmeg1').html('<center>手機載具格式錯誤</center><input type="button" style="width:130px;height:150px;float:right;" value="確認" onclick="dialog.dialog(\'close\');">');
			dialog.dialog('open');
		}
	});
	$(document).on('click','#reopen',function(){
		fun.dialog('option','title','重開發票');
		$('.fun').html("<table style='width:100%;height:100%;'><tr><td>發票號：<input type='text' id='invnumber' name='invnumber' value='' autofocus><input type='button' id='reopensubmit' value='確定'><input type='button' id='review' value='返回'><br><div id='hint' style='color:#ff3399;text-align:center;'>限定當期發票</div></td></tr></table>");
	});
	$(document).on('click','#reprint',function(){
		fun.dialog('option','title','權限驗證');
		$('.fun').html("<table style='width:100%;height:100%;'><tr><td><span>驗證密碼：</span><input type='password' id='psw' style='text-transform:uppercase;width:calc(100% - 220px);' name='psw' value='' autofocus><br><input type='button' id='review' style='height:150px;width:20%;float:right;margin:10px;' value='離開'><input type='button' id='pswsubmit' style='height:150px;width:20%;float:right;margin:10px;' value='驗證'></td></tr></table>");
		//fun.dialog('option','title','補印發票');
		//$('.fun').html("<table style='width:100%;height:100%;'><tr><td>發票號：<input type='text' id='rinvnumber' style='text-transform:uppercase;' name='rinvnumber' value='' autofocus><input type='button' id='reprintsubmit' value='確定'><input type='button' id='review' value='離開'><br><div id='hint' style='color:#ff3399;text-align:center;'>限定當期發票</div></td></tr></table><div id='hint2'></div>");
	});
	$(document).on('keypress','#invnumber',function(event){
		if(event.which==13){
			$.ajax({
				url:'./reopen.ajax.php',
				method:'post',
				data:{'number':$('.fun #invnumber').val()},
				dataType:'html',
				success:function(d){
					console.log(d);
					//location.reload(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
		}
	});
	$(document).on('click','#reopensubmit',function(){
		$.ajax({
			url:'./reopen.ajax.php',
			method:'post',
			data:{'number':$('.fun #invnumber').val()},
			dataType:'html',
			success:function(d){
				console.log(d);
				//location.reload();
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('keypress','#psw',function(event){
		if(event.which==13){
			$.ajax({
				url:'./passpsw.ajax.php',
				method:'post',
				data:{'psw':$('.fun #psw').val()},
				dataType:'html',
				success:function(d){
					console.log(d);
					if(d=='pass'){
						fun.dialog('option','title','補印發票');
						$('.fun').html("<table style='width:100%;height:100%;'><tr><td><span style='float:left;'>發票號：</span><form id='reprintform' method='post' style='width:300px;float:left;' action='reprint.ajax.php'><input type='text' id='rinvnumber' style='text-transform:uppercase;' name='rinvnumber' value='' autofocus></form><input type='button' id='reprintsubmit' value='確定'><input type='button' id='review' value='離開'><br><div id='hint' style='color:#ff3399;text-align:center;'>限定當期發票</div></td></tr></table><div id='hint2'></div>");
					}
					else{
						$('.fun #psw').val('');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
		}
	});
	$(document).on('click','#pswsubmit',function(){
		$.ajax({
			url:'./passpsw.ajax.php',
			method:'post',
			data:{'psw':$('.fun #psw').val()},
			dataType:'html',
			success:function(d){
				console.log(d);
				if(d=='pass'){
					fun.dialog('option','title','補印發票');
					$('.fun').html("<table style='width:100%;height:100%;'><tr><td><span style='float:left;'>發票號：</span><form id='reprintform' method='post' style='width:300px;float:left;' action='reprint.ajax.php'><input type='text' id='rinvnumber' style='text-transform:uppercase;' name='rinvnumber' value='' autofocus></form><input type='button' id='reprintsubmit' value='確定'><input type='button' id='review' value='離開'><br><div id='hint' style='color:#ff3399;text-align:center;'>限定當期發票</div></td></tr></table><div id='hint2'></div>");
				}
				else{
					$('.fun #psw').val('');
				}
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('keypress','#rinvnumber',function(event){
		if(event.which==13){
			$('#reprintform').submit();
			/*$.ajax({
				url:'./reprint.ajax.php',
				method:'post',
				data:{'number':$('.fun #rinvnumber').val()},
				dataType:'html',
				success:function(d){
					if(d=='data_is_empty'){
						alert('輸入之發票號錯誤或已作廢。');
					}
					else{
						console.log(d);
						var mywin=window.open("'"+d+"'");
						window.setTimeout(function(){location.reload(d);},5000);
					}
					//location.reload(d);
				},
				error:function(e){
					console.log(e);
				}
			});*/
		}
		else{
		}
	});
	$(document).on('click','#reprintsubmit',function(){
		$('#reprintform').submit();
		/*$.ajax({
			url:'./reprint.ajax.php',
			method:'post',
			data:{'number':$('.fun #rinvnumber').val()},
			dataType:'html',
			success:function(d){
				if(d=='data_is_empty'){
						alert('輸入之發票號錯誤或已作廢。');
					}
					else{
						console.log(d);
						var mywin=window.open("'"+d+"'");
						window.setTimeout(function(){location.reload(d);},5000);
					}
				//location.reload();
			},
			error:function(e){
				console.log(e);
			}
		});*/
	});
	$(document).on('click','#delete',function(){
		fun.dialog('option','title','作廢發票');
		$('.fun').html("<table style='width:100%;height:80%;'><tr><td><span>發票號：</span><input type='text' id='deleteinvnumber' style='text-transform:uppercase;width:calc(100% - 180px)' name='invnumber' value='' autofocus><div id='hint' style='color:#ff3399;text-align:center;'>限定當期發票</div><input type='button' style='height:150px;width:20%;float:right;margin:10px;' id='review' value='離開'><input type='button' style='height:150px;width:20%;float:right;margin:10px;' id='deletesubmit' value='確定'></td></tr></table><div id='hint2'></div>");
	});
	$(document).on('keypress','#deleteinvnumber',function(event){
		if(event.which==13){
			if($('#deleteinvnumber').val().length==19){
				$.ajax({
					url:'./delete.ajax.php',
					method:'post',
					data:{'number':$('.fun #deleteinvnumber').val().substr(5,10)},
					dataType:'html',
					success:function(d){
						if(d=='is not now'){
							$('#hint2').html('該發票號非當期發票。');
						}
						else if(d.substr(0,16)=='invlist is empty'){
							$('#hint2').html('該發票號錯誤或已作廢。');
						}
						else{
							var mywin=window.open('ban://xml='+d+',company=<?php echo $content["basic"]["company"]; ?>,dep=<?php echo $content["basic"]["story"]; ?>,cmd=delete,date=<?php echo date("Ym"); ?>','','width=1px,height=1px');
							$('#deleteinvnumber').val('');
							$('#hint2').html('');
							$('#sysmeg1').html('<center>作廢完成</center><input type="button" style="width:130px;height:150px;float:right;" value="確認" onclick="dialog.dialog(\'close\');">');
							dialog.dialog('open');
						}
					},
					error:function(e){
						console.log(e);
					}
				});
			}
			else if($('#deleteinvnumber').val().length==10){
				$.ajax({
					url:'./delete.ajax.php',
					method:'post',
					data:{'number':$('.fun #deleteinvnumber').val()},
					dataType:'html',
					success:function(d){
						if(d=='is not now'){
							$('#hint2').html('該發票號非當期發票。');
						}
						else if(d.substr(0,16)=='invlist is empty'){
							$('#hint2').html('該發票號錯誤或已作廢。');
						}
						else{
							var mywin=window.open('ban://xml='+d+',company=<?php echo $content["basic"]["company"]; ?>,dep=<?php echo $content["basic"]["story"]; ?>,cmd=delete,date=<?php echo date("Ym"); ?>','','width=1px,height=1px');
							$('#deleteinvnumber').val('');
							$('#hint2').html('');
							$('#sysmeg1').html('<center>作廢完成</center><input type="button" style="width:130px;height:150px;float:right;" value="確認" onclick="dialog.dialog(\'close\');">');
							dialog.dialog('open');
						}
					},
					error:function(e){
						console.log(e);
					}
				});
			}
			else{
			}
		}
		else{
		}

	});
	$(document).on('click','#deletesubmit',function(){
		if($('#deleteinvnumber').val().length==19){
			$.ajax({
				url:'./delete.ajax.php',
				method:'post',
				data:{'number':$('.fun #deleteinvnumber').val().substr(5,10)},
				dataType:'html',
				success:function(d){
					if(d=='is not now'){
						$('#hint2').html('該發票號非當期發票。');
					}
					else if(d.substr(0,16)=='invlist is empty'){
						$('#hint2').html('該發票號錯誤或已作廢。');
					}
					else{
						var mywin=window.open('ban://xml='+d+',company=<?php echo $content["basic"]["company"]; ?>,dep=<?php echo $content["basic"]["story"]; ?>,cmd=delete,date=<?php echo date("Ym"); ?>','','width=1px,height=1px');
						$('#deleteinvnumber').val('');
						$('#hint2').html('');
						$('#sysmeg1').html('<center>作廢完成</center><input type="button" style="width:130px;height:150px;float:right;" value="確認" onclick="dialog.dialog(\'close\');">');
						dialog.dialog('open');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else if($('#deleteinvnumber').val().length==10){
			$.ajax({
				url:'./delete.ajax.php',
				method:'post',
				data:{'number':$('.fun #deleteinvnumber').val()},
				dataType:'html',
				success:function(d){
					if(d=='is not now'){
						$('#hint2').html('該發票號非當期發票。');
					}
					else if(d.substr(0,16)=='invlist is empty'){
						$('#hint2').html('該發票號錯誤或已作廢。');
					}
					else{
						var mywin=window.open('ban://xml='+d+',company=<?php echo $content["basic"]["company"]; ?>,dep=<?php echo $content["basic"]["story"]; ?>,cmd=delete,date=<?php echo date("Ym"); ?>','','width=1px,height=1px');
						$('#deleteinvnumber').val('');
						$('#hint2').html('');
						$('#sysmeg1').html('<center>作廢完成</center><input type="button" style="width:130px;height:150px;float:right;" value="確認" onclick="dialog.dialog(\'close\');">');
						dialog.dialog('open');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
		}
	});
	$(document).on('click','.fun #review',function(){
		fun.dialog('option','title','功能區');
		$('.fun').html("<table style='width:100%;height:100%;'><tr><td style='width:25%;height:20%;'><input type='button' id='container' style='width:100%;height:100%;' value='手機載具'></td><td style='width:25%;height:20%;'><input type='button' id='predaydata' style='width:100%;height:100%;' value='發票彙總'></td></tr><tr><td style='width:25%;height:20%;'><input type='button' id='delete' style='width:100%;height:100%;' value='作廢發票'></td><!-- <tr><td style='width:25%;'><input type='button' id='reopen' style='width:100%;height:100%;' value='重開發票'></td></tr> --><td style='width:25%;height:20%;'><input type='button' id='reprint' style='width:100%;height:100%;' value='補印發票'></td></tr><tr><td style='width:25%;height:20%;'><input type='button' id='cancel' style='width:100%;height:100%;' value='返回'></td><td style='width:25%;height:20%;'><input type='button' id='exit' style='width:100%;height:100%;' value='離開系統'></td></tr></table>");
	});
	$(document).on('click','#exit',function(){
		$('#sysmeg1').html("<center>是否離開系統？</center><input type='button' style='height:150px;width:20%;float:right;margin:10px;' value='離開' onclick='window.open(\"exitinv://\",\"\",\"width=1px,height=1px\");'><input type='button' style='height:150px;width:20%;float:right;margin:10px;' value='取消' onclick='dialog.dialog(\"close\");'>");
		dialog.dialog('open');
	});
	$(document).on('click', '.fun #predaydata', function() {
        fun.dialog('option', 'title', '發票彙總');
        $('.fun').html("<table style='width:100%;'><tr><td>查詢日期<input type='date' name='searinvdate' value='<?php echo date('Y-m-d'); ?>'></td></tr></table><input type='button' style='height:150px;width:20%;float:right;margin:300px 10px 10px 10px;' id='review' value='離開'><input type='button' style='height:150px;width:20%;float:right;margin:300px 10px 10px 10px;' id='searchsubmit' value='確定'>");
    });
    $(document).on('click', '.fun #searchsubmit', function() {
        if ($('.fun input[name="searinvdate"]').val().length == 10) {
            $.ajax({
                url: 'getday.data.php',
                method: 'post',
                data: {
                    'searchdate': $('.fun input[name="searinvdate"]').val()
                },
                dataType: 'html',
                success: function(d) {
                    $('.fun').html(d);
                },
                error: function(e) {
                    console.log(e);
                }
            });
        } else {
            $('#sysmeg1').html('<center>請填入完整之查詢日期</center><input type="button" style="width:130px;height:150px;float:right;" value="確認" onclick="dialog.dialog(\'close\');">');
            dialog.dialog('open');
        }
    });
    $(document).on('click', '.fun #research', function() {
        fun.dialog('option', 'title', '發票彙總');
        $('.fun').html("<table style='width:100%;'><tr><td>查詢日期<input type='date' name='searinvdate' value='<?php echo date('Y-m-d'); ?>'></td></tr></table><input type='button' style='height:150px;width:20%;float:right;margin:300px 10px 10px 10px;' id='review' value='離開'><input type='button' style='height:150px;width:20%;float:right;margin:300px 10px 10px 10px;' id='searchsubmit' value='確定'>");
    });
});
</script>
<style>
body {
	width:100%;
	height:100%;
	margin:0;
	padding:0;
	border:0;
	font-family: Arial,Microsoft JhengHei,sans-serif;
}
input {
	font-family: Arial,Microsoft JhengHei,sans-serif;
}#reprintform
form {
	overflow:hidden;
	font-family: Arial,Microsoft JhengHei,sans-serif;
}
form input,
.ui-widget input,
td {
	font-family: Arial,Microsoft JhengHei,sans-serif;
}
.viewtable {
	width:calc(25% - 5px);
	height:100%;
	float:left;
	position:relative;
}
.viewtr {
	width:100%;
	float:left;
	margin-top:5px;
}
.viewtitle {
	font-size:40px;
	float:left;
}
#othinput {
	float:right;
}
#viewinput,
.viewinput {
	width:100%;
	font-size:40px;
	float:left;
}
.funbox {
	width:100%;
	height:55%;
	position:absolute;
	left:0;
	bottom:0;
}
.funtable {
	width:100%;
	height:100%;
	margin:0;
}
.keybord {
	width:calc(75% - 10px);
	height:100%;
	margin:0 5px 0 0;
	float:left;
}
.numberkey {
	width:25%;
	height:25%;
	padding:0;
}
.button {
	width:100%;
	height:100%;
	background-color:buttonface;
}
.numberkey .button {
	font-size:100px;
}
.funtable .button {
	font-size:50px;
}
@media screen and (max-device-width:768px) {
	.funtable .button {
		font-size:25px;
	}
}
#submitbutton {
	font-size:50px;
}
#sysmeg1,
#sysmeg1 input {
	font-size:40px;
}
#sysmeg1 input {
	background-color:#f7f7f7;
}
.fun,
.fun input {
	font-size:40px;
}
.fun #hint {
	font-size:20px;
}
.fun #coninput,
.fun #invnumber,
.fun #psw,
.fun #rinvnumber,
.fun #deleteinvnumber,
#reprintform {
	width:300px;
}
</style>
<body>
<input type='hidden' id='full'>
<div id="sysmeg1" title='系統提示'></div>
<div class='fun' title='功能區'></div>
<!-- <form method='post' class='targetform' action='../create/'> -->
	<input type='hidden' name='temp' value='0'>
	<input type='hidden' name='sign' value='1'>
	<input type='hidden' name='new' value='1'>
	<input type='hidden' name='target' value='view'>
	<input type='hidden' name='taxtype' value='1'>
	<input type='hidden' name='usecontainer' value=''>
	<table class='keybord'>
		<tr>
			<!-- <td class='numberkey'><input type="button" class='button' id='back' style='background-image:url(../img.png);background-size:100% 100%;'></td> -->
			<td class='numberkey'><input type="button" class='button' id='input[]' value="7"></td>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="8"></td>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="9"></td>
			<td class='numberkey' rowspan='2'><input type="button" class='button' id='diff' value="-"></td>
		</tr>
		<tr>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="4"></td>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="5"></td>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="6"></td>
		</tr>
		<tr>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="1"></td>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="2"></td>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="3"></td>
			<td class='numberkey' rowspan='2'><input type="button" class='button' id='plus' value="+"></td>
		</tr>
		<tr>
			<td class='numberkey'><input type="button" class='button' id='input[]' value="0"></td>
			<td class='numberkey' colspan='2'><input type="button" class='button' id='same' value="="></td>
		</tr>
	</table>
	<div class='viewtable'>
		<div class='viewtr'>
			<span class='viewtitle'>剩餘量</span>
			<div id='othinput' style='width:calc(100% - 125px);'><?php if($count[0]['number']<$content['basic']['safe']){echo "<input type='button' id='addinv' class='viewinput' value='補充發票' style='text-align:center;'>";}else{echo "<input type='text' id='viewinput' name='invnumber' value='剩餘 ".$count[0]['number']."張' style='text-align:right;background-color:#f7f7f7;' readonly>";} ?></div>
		</div>
		<div class='viewtr'>
			<span class='viewtitle'>發票號</span>
			<div id='othinput' style='width:calc(100% - 125px);'><input type='text' id='viewinput' name='inv' value='<?php if(sizeof($table)==0){echo "";}else{echo $table[0]['banno'];} ?>' style='text-align:right;background-color:#f7f7f7;' readonly></div>
		</div>
		<div class='viewtr'>
			<span class='viewtitle'>統編</span>
			<div id='othinput' style='width:calc(100% - 125px);'><input type='text' id='viewinput' name='ban' style='text-align:right;background-color:#f7f7f7;' value='' readonly></div>
		</div>
		<div class='viewtr'>
			<span class='viewtitle'>消費金額</span>
			<div><input type='text' id='viewinput' name='view' value='<?php if(isset($_GET['num'])){echo $_GET['num'];}else{echo '0';} ?>' style='text-align:right;' readonly></div>
		</div>
<?php
if(isset($_GET['tax'])){
	echo "<div class='viewtr'>
			<span class='viewtitle'>稅金</span>
			<div><input type='text' id='viewinput' name='tax' value='0' style='text-align:right;background-color:#f7f7f7;' readonly></div>
		</div>";
}
else{
}
?>
	<div class='content' style='display:none;'></div>
		<div class='funbox'>
			<table class='funtable'>
				<tr height='20%'>
					<td colspan='2'><input type='button' class='funbutton button' value='功能'><!-- <input type="button" class='button' id='logout' value='登出'> --></td>
				</tr>
				<tr height='20%'>
					<td><input type="button" class='button' id='tax' value='稅內'></td>
					<td><input type="button" class='button' id='notax' value='稅外'></td>
				</tr>
				<tr height='20%'>
					<td><input type="button" class='button' id='back' value='刪除'></td>
					<td><input type="button" class='button' id='ac' value='歸零'></td>
				</tr>
				<tr height='40%'>
					<td colspan='2'><input type='button' class='button' id='submitbutton' value='結帳'></td>
				</tr>
			</table>
		</div>
	</div>
<!-- </form> -->
</body>