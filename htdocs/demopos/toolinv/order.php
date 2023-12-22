<!DOCTYPE html>
<?php
/*if(isset($_GET['id'])){
}
else{
	echo "<script>alert('id:".isset($_GET['id'])."');location.href='../login/';</script>";
}*/

include '../../tool/dbTool.inc.php';
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
$content=parse_ini_file('./setup.ini',true);
$conn=sqlconnect("localhost","ban","banuser","1qaz2wsx","utf-8","mysql");
$sql='SELECT banno FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND dateTime="'.$year.$month.'" ORDER BY banno LIMIT 1';
$table=sqlquery($conn,$sql,'mysql');
$sql='SELECT COUNT(banno) as number FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND dateTime="'.$year.$month.'"';
$count=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src='https://code.jquery.com/jquery-1.12.4.js'></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://blockly.webduino.io/lib/runtime.min.js"></script>
<script src="https://blockly.webduino.io/webduino-blockly.js"></script>
<script src="https://webduino.io/components/webduino-js/dist/webduino-all.min.js"></script>
<script src="https://blockly.webduino.io/lib/firebase.js"></script>
<meta name="viewport" id='viewport' content="user-scalable=0,initial-scale=1,maximum-scale=1,width=device-width">
<title>開立電子發票系統</title>
<script>
function submitenter(e){
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
	{
		$('input[name="usecontainer"]').val($('.fun input[name="container"]').val());
		fun.dialog('close');
		return false;
	}
	else
	return true;
}
$(document).ready(function(){
	var rfid;
	//var maxlogouttime=20;
	//var logouttime=maxlogouttime;
	//var tim1;
	/*function timer1(){
		logouttime--;
		$('.content').html(logouttime);
		if(logouttime==0){
			//location.href='../login/';
			$.ajax({
				url:'../logoutmethod/',
				success:function(){
					location.href='../login/';
				},
				error:function(){
					console.log('error');
				}
			});
		}
	}*/

	<?php
	if($count[0]['number']<$content['basic']['safe']){
		echo 'alert("發票存量已低於安全量");';
	}
	echo '$(document).on("click","#addinv",function(){var mywin=window.open(\'ban://company='.$content['basic']['company'].',dep='.$content['basic']['story'].',cmd=add,date='.$year.$month.'\',\'\',\'width=1px,height=1px\');});';
	?>
	
	/*boardReady({device: 'Ylg6'}, function (board) {
		board.systemReset();
		board.samplingInterval = 250;
		rfid = getRFID(board);
		rfid.read();
		rfid.on("enter",function(_uid){
			window.clearInterval(tim1);
			logouttime=maxlogouttime;
			$('.content').html(logouttime);
			//$.ajax({
				//method:'GET',
				//url:'../loginmethod/',
				//data:{id:_uid},
				//success:function(msg){
					//logouttime=maxlogouttime;
					//$('.content').html('');
				//}
			//});
		});
		rfid.on("leave",function(_uid){
			tim1=setInterval(function(){timer1()},1000);
		});
	});*/

	/*$(document).on('click','#logout',function(){
		$.ajax({
			url:'../logoutmethod/',
			success:function(){
				location.href='../login/';
			},
			error:function(){
				console.log('error');
			}
		});
	});*/

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
		//logouttime=maxlogouttime;
		if($('input[name="'+$('input[name="target"]').val()+'"]').val().length==1){
			$('input[name="'+$('input[name="target"]').val()+'"]').val('0');
		}
		else{
			$('input[name="'+$('input[name="target"]').val()+'"]').val($('input[name="'+$('input[name="target"]').val()+'"]').val().substr(0,$('input[name="'+$('input[name="target"]').val()+'"]').val().length-1));
		}
	});
	$('#ac').click(function(){
		//logouttime=maxlogouttime;
		$('input[name="'+$('input[name="target"]').val()+'"]').val('0');
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
		//logouttime=maxlogouttime;
		if($('input[name="target"]').val()=='ban'){//檢查focus是否在統編
			var maxlen=8;
		}
		else{
			var maxlen=100;
		}
		if($('input[name="sign"]').val()=='0'){//如果已按等於、計算稅金或切換輸入統編
			$('input[name="temp"]').val('0');
			$('input[name="sign"]').val('1');
		}
		else{
		}
		if($('input[name="'+$('input[name="target"]').val()+'"]').val().length==maxlen){//檢察focus input是否超過容許長度
			
		}
		else if($('input[name="'+$('input[name="target"]').val()+'"]').val()=='0'){//檢察統編是否尚未輸入或消費金額部分為0
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
	});
	dialog=$('#sysmeg1').dialog({
		autoOpen:false,
		height:500,
		width:800,
		modal:true,
		title:'系統提示',
		buttons:{
			
		}
	});
	fun=$('.fun').dialog({
		autoOpen:false,
		height:300,
		width:800,
		modal:true,
		title:'功能區',
		open:function(){
			fun.dialog('option','title','功能區');
			$('.fun').html("<table style='width:100%;'><tr><td style='width:25%;height:50px;'><input type='button' id='container' style='width:100%;height:100%;' value='電子載具'></td><td style='width:25%;height:50px;'><input type='button' id='reprint' style='width:100%;height:100%;' value='重開發票'></td><td style='width:25%;height:50px;'><input type='button' id='delete' style='width:100%;height:100%;' value='作廢發票'></td><td style='width:25%;height:50px;'><input type='button' id='cancel' style='width:100%;height:100%;' value='返回'></td></tr></table>");
		}
	});
	function sum(){
		if($.isNumeric(parseInt($('input[name="temptotal"]').val()))){
			if($('input[name="new"]').val()=='1'||$('input[name="target"]').val()=='ban'){
				$('input[name="temp"]').val(parseInt($('input[name="temptotal"]').val()));
			}
			else{
				$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="temptotal"]').val())*parseInt($('input[name="sign"]').val()));
			}
		}
		else{
			//$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="temptotal"]').val())*parseInt($('input[name="sign"]').val()));
		}
		$('input[name="new"]').val('1');
		$('input[name="tax"]').val(Math.round(parseInt($('input[name="temp"]').val())*0.05));
	}
	function submitform(){
		$(".targetform").submit();
		dialog.dialog('close');
	}
	$('#plus').click(function(){
		//logouttime=maxlogouttime;
		//$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="temptotal"]').val())*parseInt($('input[name="sign"]').val()));
		sum();
		$('input[name="sign"]').val('1');
		$('input[name="temptotal"]').val($('input[name="temp"]').val());
	});
	$('#diff').click(function(){
		//logouttime=maxlogouttime;
		//$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="temptotal"]').val())*parseInt($('input[name="sign"]').val()));
		sum();
		$('input[name="sign"]').val('-1');
		$('input[name="temptotal"]').val($('input[name="temp"]').val());
	});
	$('#same').click(function(){
		//logouttime=maxlogouttime;
		//$('input[name="temp"]').val(parseInt($('input[name="temp"]').val())+parseInt($('input[name="temptotal"]').val())*parseInt($('input[name="sign"]').val()));
		sum();
		$('input[name="temptotal"]').val($('input[name="temp"]').val());
		$('input[name="sign"]').val('0');
	});
	$('input[name="temptotal"]').click(function(){
		//logouttime=maxlogouttime;
		sum();
		if($('input[name="target"]').val()=="ban"){
			$('#plus').attr('disabled',false);
			$('#diff').attr('disabled',false);
			$('#same').attr('disabled',false);
			$('input[name="temptotal"]').css({'background-color':'#ffffff'});
			$('input[name="ban"]').css({'background-color':'#f7f7f7'});
			$('input[name="target"]').val('view');
		}
		else{
		}
	});
	$('input[name="ban"]').click(function(){
		//logouttime=maxlogouttime;
		sum();
		if($('input[name="target"]').val()=="view"){
			$('#plus').attr('disabled',true);
			$('#diff').attr('disabled',true);
			$('#same').attr('disabled',true);
			$('input[name="temptotal"]').css({'background-color':'#f7f7f7'});
			$('input[name="ban"]').css({'background-color':'#ffffff'});
			$('input[name="target"]').val('ban');
		}
		else{
		}
	});
	$('#submitbutton').click(function(){
		//logouttime=maxlogouttime;
		sum();
		var tempinv="<form method='post' action='./create.php' class='targetform'><table><caption>暫結發票資訊</caption><tr><td>發票號碼</td><td><input type='text' name='tempinv' value='"+$('.viewtable input[name="inv"]').val()+"' readonly></td></tr><tr><td>統編</td><td><input type='text' name='tempban' value='"+$('.viewtable input[name="ban"]').val()+"' readonly></td></tr><tr><td>消費總金額</td><td><input type='text' name='total'";
		if($('input[name="taxtype"]').val()==1){
			tempinv=tempinv+" value='"+$('.viewtable input[name="view"]').val()+"'";
		}
		else{
			tempinv=tempinv+" value='"+(parseInt($('.viewtable input[name="view"]').val())+parseInt($('.viewtable input[name="tax"]').val()))+"'";
		}
		tempinv=tempinv+" readonly></td></tr><tr><td>電子載具</td><td><input type='text' name='tempcontainer' value='"+$('input[name="usecontainer"]').val()+"' readonly></td></tr></table><form>";
		if($('input[name="taxtype"]').val()==1){
			if(parseInt($('.viewtable input[name="temptotal"]').val())==0){
				tempinv=tempinv+"<input type='button' value='取消' onclick='dialog.dialog(\"close\");'>";
			}
			else{
				tempinv=tempinv+"<input type='button' value='繼續' onclick='$(\".targetform\").submit();'><input type='button' value='取消' onclick='dialog.dialog(\"close\");'>";
			}
		}
		else{
			
		}

		$('#sysmeg1').html(tempinv);
		dialog.dialog('open');
	});
	$('#tax').click(function(){
		//logouttime=maxlogouttime;
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
		//logouttime=maxlogouttime;
		$('input[name="taxtype"]').val('0');
		$('#tax').attr('disabled',false);
		$('#notax').attr('disabled',true);
		//if($('.viewtr').length==4){
			$('.viewtr:eq(3)').after("<div class='viewtr'><div class='viewtitle'>稅金</div><div><input type='text' id='viewinput' name='tax' value='0' style='text-align:right;background-color:#f7f7f7;' readonly></div></div>");
			$('input[name="tax"]').val(Math.round(parseInt($('input[name="temptotal"]').val())*0.05));
		//}
		//else{
		//}
		$('#same').val('計算稅金');
	});
	$('.funbutton').click(function(){
		fun.dialog('open');
	});
	$(document).on('click','#cancel',function(){
		fun.dialog('close');
	});
	$(document).on('click','#container',function(){
		fun.dialog('option','title','電子載具');
		$('.fun').html("<table style='width:100%;height:100%;'><tr><td>電子載具：<input type='text' id='coninput' name='container' value='' onkeypress='return submitenter(event)' autofocus><input type='button' id='review' value='返回'></td></tr></table>");
	});
	$(document).on('click','.fun #review',function(){
		fun.dialog('option','title','功能區');
		$('.fun').html("<table style='width:100%;'><tr><td style='width:25%;height:50px;'><input type='button' id='container' style='width:100%;height:100%;' value='電子載具'></td><td style='width:25%;height:50px;'><input type='button' id='reprint' style='width:100%;height:100%;' value='重開發票'></td><td style='width:25%;height:50px;'><input type='button' id='delete' style='width:100%;height:100%;' value='作廢發票'></td><td style='width:25%;height:50px;'><input type='button' id='cancel' style='width:100%;height:100%;' value='返回'></td></tr></table>");
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
}
form {
	width:100%;
	height:100%;
	overflow:hidden;
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
.fun #coninput {
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
			<div id='othinput' style='width:calc(100% - 125px);'><input type='text' id='viewinput' name='ban' style='text-align:right;background-color:#f7f7f7;' value='0' readonly></div>
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