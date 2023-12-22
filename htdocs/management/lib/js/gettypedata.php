<script>
$(document).ready(function(){
	var temparray=$('#type input[name="typegroup"]').val().split(',');
	if(temparray.length>1){
		/*$('#type #edittype #fun').append("<input id='prev' type='button' value='上一筆'>");
		$('#type #edittype #fun').append("<input id='next' type='button' value='下一筆'>");*/
	}
	else{
	}
	$(document).on('change','#type #edittype input[name="name1"]',function(){
		$('#type #edittype #fun #save').prop('disabled',false);
	});
	$(document).on('change','#type #edittype input[name="name2"]',function(){
		$('#type #edittype #fun #save').prop('disabled',false);
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
	$frontname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-front.ini',true);
	$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
?>
<script>
$(document).ready(function(){
	$("#color<?php echo $initsetting['init']['firlan']; ?>").colorpicker({
		color:"<?php if(isset($frontname[$number]['color'.$initsetting['init']['firlan']]))echo $frontname[$number]['color'.$initsetting['init']['firlan']];else echo '#000000'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$("#color<?php echo $initsetting['init']['seclan']; ?>").colorpicker({
		color:"<?php if(isset($frontname[$number]['color'.$initsetting['init']['seclan']]))echo $frontname[$number]['color'.$initsetting['init']['seclan']];else echo '#898989'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#color').colorpicker({
		color:"<?php echo $frontname[$number]['bgcolor']; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
});
</script>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?></center></h1>
<div id='fun' style='width:100%;float:left;'>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<form id='typeform' style='overflow:hidden;float:left;'>
	<input type='hidden' name='number' value='<?php echo $number; ?>'>
	<input type='hidden' name='company' value='<?php echo $company; ?>'>
	<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
	<table style='float:left;margin:0 0 80px 0;'>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainname']))echo $interface['name']['mainname'];else echo '主語言'; ?></td>
			<td><input type='text' name='name<?php echo $initsetting['init']['firlan']; ?>' value='<?php echo $frontname[$number]['name'.$initsetting['init']['firlan']]; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontsize']))echo $interface['name']['mainfontsize'];else echo '字體大小'; ?></td>
			<td><input type='tel' name='size<?php echo $initsetting['init']['firlan']; ?>' value='<?php if(isset($frontname[$number]['size'.$initsetting['init']['firlan']]))echo $frontname[$number]['size'.$initsetting['init']['firlan']];else echo '24'; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontcolor']))echo $interface['name']['mainfontcolor'];else echo '字體顏色'; ?></td>
			<td><input id='color<?php echo $initsetting['init']['firlan']; ?>' name='color<?php echo $initsetting['init']['firlan']; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontweight']))echo $interface['name']['mainfontweight'];else echo '是否粗體'; ?></td>
			<td><input type='checkbox' name='bold<?php echo $initsetting['init']['firlan']; ?>' <?php if(isset($frontname[$number]['bold'.$initsetting['init']['firlan']])&&$frontname[$number]['bold'.$initsetting['init']['firlan']]==1)echo 'checked';else echo ''; ?>></td>
		</tr>
		<tr>
			<td><?php if($interface!='-1'&&isset($interface['name']['secname']))echo $interface['name']['secname'];else echo '次語言'; ?></td>
			<td><input type='text' name='name<?php echo $initsetting['init']['seclan']; ?>' value='<?php echo $frontname[$number]['name'.$initsetting['init']['seclan']]; ?>'></td>
		</tr>
		<tr>
			<td><?php if($interface!='-1'&&isset($interface['name']['secfontsize']))echo $interface['name']['secfontsize'];else echo '字體大小'; ?></td>
			<td><input type='tel' name='size<?php echo $initsetting['init']['seclan']; ?>' value='<?php if(isset($frontname[$number]['size'.$initsetting['init']['seclan']]))echo $frontname[$number]['size'.$initsetting['init']['seclan']];else echo '14'; ?>'></td>
		</tr>
		<tr>
			<td><?php if($interface!='-1'&&isset($interface['name']['secfontcolor']))echo $interface['name']['secfontcolor'];else echo '字體顏色'; ?></td>
			<td><input id='color<?php echo $initsetting['init']['seclan']; ?>' name='color<?php echo $initsetting['init']['seclan']; ?>'></td>
		</tr>
		<tr>
			<td><?php if($interface!='-1'&&isset($interface['name']['secfontweight']))echo $interface['name']['secfontweight'];else echo '是否粗體'; ?></td>
			<td><input type='checkbox' name='bold<?php echo $initsetting['init']['seclan']; ?>' <?php if(isset($frontname[$number]['bold'.$initsetting['init']['seclan']])&&$frontname[$number]['bold'.$initsetting['init']['seclan']]==1)echo 'checked';else echo ''; ?>></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['viewseq']))echo $interface['name']['viewseq'];else echo '顯示順序'; ?></td>
			<td><input type='text' name='seq' value='<?php if(isset($frontname[$number]['seq']))echo $frontname[$number]['seq'];else echo '1'; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['bgcolor']))echo $interface['name']['bgcolor'];else echo '按鈕底色'; ?></td>
			<td><input id='color' name='bgcolor' value=''></td>
		</tr>
		<tr>
			<td colspan='2'>
				<?php if($interface!='-1'&&isset($interface['name']['tempview']))echo $interface['name']['tempview'];else echo '檢視效果(僅供參考)'; ?>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<button style='min-width:261px;max-width:261px;height:100px;text-align:center;font-family:Consolas,Microsoft JhengHei,sans-serif;background-color:<?php if(isset($frontname[$number]['bgcolor']))echo $frontname[$number]['bgcolor'];else echo '#84FEFF'; ?>;border: 1px solid #898989;border-radius: 5px;overflow:hidden;' disabled>
					<div id='name1' style='font-size:<?php if(isset($frontname[$number]['size'.$initsetting['init']['firlan']]))echo $frontname[$number]['size'.$initsetting['init']['firlan']].'px';else echo '14px'; ?>;color:<?php if(isset($frontname[$number]['color'.$initsetting['init']['firlan']]))echo $frontname[$number]['color'.$initsetting['init']['firlan']];else echo '#000000'; ?>;font-weight:<?php if(isset($frontname[$number]['bold'.$initsetting['init']['firlan']])&&$frontname[$number]['bold'.$initsetting['init']['firlan']]==1)echo 'bold';else echo 'normal'; ?>;'>
						<?php
						echo $frontname[$number]['name'.$initsetting['init']['firlan']];
						?>
					</div>
					<div id='name2' style='font-size:<?php if(isset($frontname[$number]['size'.$initsetting['init']['seclan']]))echo $frontname[$number]['size'.$initsetting['init']['seclan']];else echo '14px'; ?>;color:<?php if(isset($frontname[$number]['color'.$initsetting['init']['seclan']]))echo $frontname[$number]['color'.$initsetting['init']['seclan']];else echo '#898989'; ?>;font-weight:<?php if(isset($frontname[$number]['bold'.$initsetting['init']['seclan']])&&$frontname[$number]['bold'.$initsetting['init']['seclan']]==1)echo 'bold';else echo 'normal'; ?>;'>
						<?php
						echo $frontname[$number]['name'.$initsetting['init']['seclan']];
						?>
					</div>
				</button>
			</td>
		</tr>
	</table>
</form>
<?php
}
else{
	include_once '../../../tool/dbTool.inc.php';
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	$frontname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-front.ini',true);
	$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
?>
<script>
$(document).ready(function(){
	$("#color<?php echo $initsetting['init']['firlan']; ?>").colorpicker({
		color:"#000000",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$("#color<?php echo $initsetting['init']['seclan']; ?>").colorpicker({
		color:"#898989",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#color').colorpicker({
		color:"#84FEFF",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
});
</script>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?></center></h1>
<div style='width:100%;float:left;'>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<form id='typeform' style='float:left;overflow:hidden;'>
	<input type='hidden' name='number' value=''>
	<input type='hidden' name='company' value='<?php echo $company; ?>'>
	<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
	<table style='float:left;margin:0 0 80px 0;'>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainname']))echo $interface['name']['mainname'];else echo '主語言'; ?></td>
			<td><input type='text' name='name<?php echo $initsetting['init']['firlan']; ?>' value=''></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontsize']))echo $interface['name']['mainfontsize'];else echo '字體大小'; ?></td>
			<td><input type='tel' name='size<?php echo $initsetting['init']['firlan']; ?>' value='24'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontcolor']))echo $interface['name']['mainfontcolor'];else echo '字體顏色'; ?></td>
			<td><input id='color<?php echo $initsetting['init']['firlan']; ?>' name='color<?php echo $initsetting['init']['firlan']; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontweight']))echo $interface['name']['mainfontweight'];else echo '是否粗體'; ?></td>
			<td><input type='checkbox' name='bold<?php echo $initsetting['init']['firlan']; ?>'></td>
		</tr>
		<tr>
			<td><?php if($interface!='-1'&&isset($interface['name']['secname']))echo $interface['name']['secname'];else echo '次語言'; ?></td>
			<td><input type='text' name='name<?php echo $initsetting['init']['seclan']; ?>' value=''></td>
		</tr>
		<tr>
			<td><?php if($interface!='-1'&&isset($interface['name']['secfontsize']))echo $interface['name']['secfontsize'];else echo '字體大小'; ?></td>
			<td><input type='tel' name='size<?php echo $initsetting['init']['seclan']; ?>' value='14'></td>
		</tr>
		<tr>
			<td><?php if($interface!='-1'&&isset($interface['name']['secfontcolor']))echo $interface['name']['secfontcolor'];else echo '字體顏色'; ?></td>
			<td><input type='color<?php echo $initsetting['init']['seclan']; ?>' name='color<?php echo $initsetting['init']['seclan']; ?>'></td>
		</tr>
		<tr>
			<td><?php if($interface!='-1'&&isset($interface['name']['secfontweight']))echo $interface['name']['secfontweight'];else echo '是否粗體'; ?></td>
			<td><input type='checkbox' name='bold<?php echo $initsetting['init']['seclan']; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['viewseq']))echo $interface['name']['viewseq'];else echo '顯示順序'; ?></td>
			<td><input type='text' name='seq' value='1'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['bgcolor']))echo $interface['name']['bgcolor'];else echo '按鈕底色'; ?></td>
			<td><input type='color' name='bgcolor'></td>
		</tr>
		<tr>
			<td colspan='2'>
				<?php if($interface!='-1'&&isset($interface['name']['tempview']))echo $interface['name']['tempview'];else echo '檢視效果(僅供參考)'; ?>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<button style='min-width:261px;max-width:261px;height:100px;text-align:center;font-family:Consolas,Microsoft JhengHei,sans-serif;background-color:#84FEFF;border: 1px solid #898989;border-radius: 5px;overflow:hidden;' disabled>
					<div id='name1' style='font-size:14px;color:#000000;font-weight:normal;'>
					</div>
					<div id='name2' style='font-size:14px;color:#898989;font-weight:normal;'>
					</div>
				</button>
			</td>
		</tr>
	</table>
</form>
<?php
}
?>