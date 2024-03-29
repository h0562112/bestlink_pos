<script>
$(document).ready(function(){
	/*var temparray=$('#taste input[name="tastegroup"]').val().split(',');
	if(temparray.length>1){
		$('#taste #edittaste #fun').append("<input id='prev' type='button' value='上一筆'>");
		$('#taste #edittaste #fun').append("<input id='next' type='button' value='下一筆'>");
	}
	else{
	}*/
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
	$tastename=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-taste.ini',true);
	$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
?>
<script>
$(document).ready(function(){
	$("#color").colorpicker({
		color:"<?php echo $tastename[$number]['background']; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
});
</script>
<h1><center><?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?></center></h1>
<div id='fun'>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='pre' class="initbutton" type='button' value='上一筆'>
	<input id='next' class="initbutton" type='button' value='下一筆'>
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<form id='tasteform'>
	<input type='hidden' name='number' value='<?php echo $number; ?>'>
	<input type='hidden' name='company' value='<?php echo $company; ?>'>
	<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
	<table style='margin:0 0 200px; 0;'>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainname']))echo $interface['name']['mainname'];else echo '主語言'; ?></td>
			<td><input type='text' name='name<?php echo $initsetting['init']['firlan']; ?>' value='<?php echo $tastename[$number]['name'.$initsetting['init']['firlan']]; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['secname']))echo $interface['name']['secname'];else echo '次語言'; ?></td>
			<td><input type='text' name='name<?php echo $initsetting['init']['seclan']; ?>' value='<?php echo $tastename[$number]['name'.$initsetting['init']['seclan']]; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['viewseq']))echo $interface['name']['viewseq'];else echo '顯示順序'; ?></td>
			<td><input type='text' name='seq' value='<?php if(isset($tastename[$number]['seq']))echo $tastename[$number]['seq'];else echo '1'; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['moneylabel']))echo $interface['name']['moneylabel'];else echo '價格'; ?></td>
			<td><input type='text' name='money' value='<?php echo $tastename[$number]['money']; ?>'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['tastetype']))echo $interface['name']['tastetype'];else echo '備註類別'; ?></td>
			<td><label><input type='radio' name='public' value='1' <?php if(!isset($tastename[$number]['public'])||$tastename[$number]['public']=='1')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['publictaste']))echo $interface['name']['publictaste'];else echo '公開備註'; ?></label>、<label><input type='radio' name='public' value='0' <?php if(isset($tastename[$number]['public'])&&$tastename[$number]['public']=='0')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['privatetaste']))echo $interface['name']['privatetaste'];else echo '專屬備註'; ?></label></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['strawlabel']))echo $interface['name']['strawlabel'];else echo '吸管設定'; ?></td>
			<td>
				<div class="mod_select" id='strawmod'>
					<ul>
						<li>
							<div class="select_box" id='strawbox'>
								<?php
								$straw=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/straw.ini',true);
								$option='';
								foreach($straw['straw'] as $k=>$v){
									$option=$option.'<a id="'.$k.'">'.$v.'</a>';
								}
								if(!isset($tastename[$number]['straw'])||$tastename[$number]['straw']==''){
									$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">'.$option;
								}
								else{
									$option='<span class="select_txt">'.$straw['straw'][$tastename[$number]['straw']].'</span><a class="selet_open">▼</a><div class="option">'.$option.'</div>';
								}
								echo $option;
								?>
							</div>
						</li>
					</ul>
					<input type="hidden" name='straw' id="select_value" value='<?php if(isset($tastename[$number]['straw'])&&$tastename[$number]['straw']!=''&&$tastename[$number]['straw']!=null)echo $tastename[$number]['straw'];else echo '999'; ?>'>
				</div>
			</td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['setgroup']))echo $interface['name']['setgroup'];else echo '設定群組'; ?></td>
			<td>
				<select name="group">
					<?php
					$tastegroup=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini',true);
					for($i=0;$i<sizeof($tastegroup);$i++){
						if(isset($tastename[$number]['group'])&&$tastename[$number]['group']==$tastegroup[$i]['groupno']){
							echo '<option value="'.$tastegroup[$i]['groupno'].'" selected>'.$tastegroup[$i]['name'].'</option>';
						}
						else{
							echo '<option value="'.$tastegroup[$i]['groupno'].'">'.$tastegroup[$i]['name'].'</option>';
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['bgcolor']))echo $interface['name']['bgcolor'];else echo '按鈕底色'; ?></td>
			<td><input id='color' name='bgcolor'></td>
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
<script>
$(document).ready(function(){
	$("#color").colorpicker({
		color:"#F0C916",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
});
</script>
<h1><center><?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?></center></h1>
<div>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<form id='tasteform'>
	<input type='hidden' name='number' value=''>
	<input type='hidden' name='company' value='<?php echo $company; ?>'>
	<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
	<table style='margin:0 0 200px; 0;'>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainname']))echo $interface['name']['mainname'];else echo '主語言'; ?></td>
			<td><input type='text' name='name<?php echo $initsetting['init']['firlan']; ?>' value=''></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['secname']))echo $interface['name']['secname'];else echo '次語言'; ?></td>
			<td><input type='text' name='name<?php echo $initsetting['init']['seclan']; ?>' value=''></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['viewseq']))echo $interface['name']['viewseq'];else echo '顯示順序'; ?></td>
			<td><input type='text' name='seq' value='1'></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['moneylabel']))echo $interface['name']['moneylabel'];else echo '價格'; ?></td>
			<td><input type='text' name='money' value=''></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['tastetype']))echo $interface['name']['tastetype'];else echo '備註類別'; ?></td>
			<td><label><input type='radio' name='public' value='1' checked><?php if($interface!='-1'&&isset($interface['name']['publictaste']))echo $interface['name']['publictaste'];else echo '公開備註'; ?></label>、<label><input type='radio' name='public' value='0'><?php if($interface!='-1'&&isset($interface['name']['privatetaste']))echo $interface['name']['privatetaste'];else echo '專屬備註'; ?></label></td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['strawlabel']))echo $interface['name']['strawlabel'];else echo '吸管設定'; ?></td>
			<td>
				<div class="mod_select" id='strawmod'>
					<ul>
						<li>
							<div class="select_box" id='strawbox'>
								<?php
								$straw=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/straw.ini',true);
								$option='';
								echo '<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">';
								foreach($straw['straw'] as $k=>$v){
									echo '<a id="'.$k.'">'.$v.'</a>';
								}
								?>
							</div>
						</li>
					</ul>
					<input type="hidden" name='straw' id="select_value" value='999'>
				</div>
			</td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['setgroup']))echo $interface['name']['setgroup'];else echo '設定群組'; ?></td>
			<td>
				<select name="group">
					<?php
					$tastegroup=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini',true);
					for($i=0;$i<sizeof($tastegroup);$i++){
						echo '<option value="'.$tastegroup[$i]['groupno'].'">'.$tastegroup[$i]['name'].'</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['bgcolor']))echo $interface['name']['bgcolor'];else echo '按鈕底色'; ?></td>
			<td><input id='color' name='bgcolor' value='#F0C916'></td>
		</tr>
	</table>
	</table>
</form>
<?php
}
?>