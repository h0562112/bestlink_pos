<script>
$(document).ready(function(){
	var temparray=$('#sectype input[name="typegroup"]').val().split(',');
	if(temparray.length>1){
		/*$('#sectype #edittype #fun').append("<input id='prev' type='button' value='上一筆'>");
		$('#sectype #edittype #fun').append("<input id='next' type='button' value='下一筆'>");*/
	}
	else{
	}
	$(document).on('change','#sectype #edittype input[name="name1"]',function(){
		$('#sectype #edittype #fun #save').prop('disabled',false);
	});
	$(document).on('change','#sectype #edittype input[name="name2"]',function(){
		$('#sectype #edittype #fun #save').prop('disabled',false);
	});
});
</script>
<style>
.hide { 
	display: none; 
}
input { 
	outline: none;
}
.mod_select ul {
	margin:0;
	padding:0;
}
.mod_select ul:after {
	display: block;
    clear: both;
    visibility: hidden;
    height: 0;
    content: '';
}
.mod_select ul li {
	list-style-type:none;
	float:left;
	height:24px;
}
.select_label {
	color:#982F4D;
	float:left;
	line-height:24px;
	padding-right:10px;
	font-size:12px;
	font-weight:700;
}
.select_box {
	float:left;
	border:solid 1px #ccc;
	color:#444;
	position:relative;
	cursor:pointer;
	width:300px;
	font-size:14px;
}
.selet_open {
	display:inline-block;
	position:absolute;
	right:0;
	top:0;
	width:30px;
	height:100%;
	line-height:24px;
	text-align:center;
	content:'▼';
}
.select_txt {
	display:inline-block;
	padding-left:10px;
	width:300px;
	line-height:24px;
	height:24px;
	cursor:pointer;
	overflow:hidden;
}
.option {
	width:300px;
	border:solid 1px #ccc;
	position:absolute;
	top:24px;
	left:-1px;
	z-index:2;
	overflow:hidden;
	display:none;
}
.option a {
	display:block;
	height:26px;
	line-height:26px;
	text-align:left;
	padding:0 10px;
	width:100%;
	background:#fff;
}
.option a:hover {
	background:#aaa;
}
</style>
<?php
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
if(isset($_POST['number'])){
	include_once '../../../tool/dbTool.inc.php';
	$number=$_POST['number'];
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	$rearname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-rear.ini',true);
?>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?></center></h1>
<div id='fun' style='width:100%;float:left;'>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<form id='sectypeform' style='overflow:hidden;float:left;'>
	<input type='hidden' name='number' value='<?php echo $number; ?>'>
	<input type='hidden' name='company' value='<?php echo $company; ?>'>
	<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
	<table style='float:left;'>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['typename']))echo $interface['name']['typename'];else echo '類別名稱'; ?></td>
			<td><input type='text' name='name' value='<?php echo $rearname[$number]['name']; ?>'></td>
		</tr>
	</table>
</form>
<?php
}
else{
	include_once '../../../tool/dbTool.inc.php';
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	$rearname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-rear.ini',true);
?>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?></center></h1>
<div style='width:100%;float:left;'>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<form id='sectypeform' style='float:left;overflow:hidden;'>
	<input type='hidden' name='number' value=''>
	<input type='hidden' name='company' value='<?php echo $company; ?>'>
	<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
	<table style='float:left;'>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['typename']))echo $interface['name']['typename'];else echo '類別名稱'; ?></td>
			<td><input type='text' name='name' value=''></td>
		</tr>
	</table>
</form>
<?php
}
?>