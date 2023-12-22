<script>
$(document).ready(function(){
	var temparray=$('#taste input[name="tastegroup"]').val().split(',');
	if(temparray.length>1){
		$('#taste #edittaste #fun').append("<input id='prev' type='button' value='上一筆'>");
		$('#taste #edittaste #fun').append("<input id='next' type='button' value='下一筆'>");
	}
	else{
	}
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
	top:-70px;
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
	$number=$_POST['number'];
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	$tastegroup=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini',true);
	$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
?>
<h1><center><?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?></center></h1>
<div id='fun'>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<form id='tasteform'>
	<input type='hidden' name='number' value='<?php echo $number; ?>'>
	<input type='hidden' name='company' value='<?php echo $company; ?>'>
	<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
	<table>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['groupname']))echo $interface['name']['groupname'];else echo '群組名稱'; ?></td>
			<td><input type='text' name='name' value='<?php echo $tastegroup[$number]['name']; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['grouppos']))echo $interface['name']['grouppos'];else echo '可選數量'; ?></td>
			<td><input type='text' name='pos' value='<?php echo $tastegroup[$number]['pos']; ?>'></td>
		</tr>
		<tr>
			<td></td>
			<td style='color: #ff0000;font-size: 15px;'>(-1:不限數量；1:單選；大於1的數則表示複選最大數量)</td>
		</tr>
	</table>
</form>
<?php
}
else{
	include_once '../../../tool/dbTool.inc.php';
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
?>
<h1><center><?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?></center></h1>
<div>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<form id='tasteform'>
	<input type='hidden' name='number' value=''>
	<input type='hidden' name='company' value='<?php echo $company; ?>'>
	<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
	<table>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['groupname']))echo $interface['name']['groupname'];else echo '群組名稱'; ?></td>
			<td><input type='text' name='name' value=''></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['grouppos']))echo $interface['name']['grouppos'];else echo '可選數量'; ?></td>
			<td><input type='number' name='pos' value=''></td>
		</tr>
		<tr>
			<td></td>
			<td style='color: #ff0000;font-size: 15px;'>(-1:不限數量；1:單選；大於1的數則表示複選最大數量)</td>
		</tr>
	</table>
</form>
<?php
}
?>