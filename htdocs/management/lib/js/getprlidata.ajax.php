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
$prlino=$_POST['prlino'];
$company=$_POST['company'];
$dep=$_POST['dep'];
$prli=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/itemprinttype.ini',true);
?>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['editcontent']))echo $interface['name']['editcontent'];else echo '修改內容'; ?></center></h1>
<div id='fun' style='width:100%;float:left;'>
	<input id='save' name='submit' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>" disabled>
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<div style='width:100%;overflow:hidden;'>
	<form id='prliform' style='overflow:hidden;'>
		<input type='hidden' name='number' value='<?php echo $prlino; ?>'>
		<input type='hidden' name='company' value='<?php echo $company; ?>'>
		<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
		<table style='float:left;'>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['typename']))echo $interface['name']['typename'];else echo '類別名稱'; ?></td>
				<td id='name'><input type='text' name='name' value='<?php echo $prli[$prlino]['name']; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['printmethod']))echo $interface['name']['printmethod'];else echo '列印方式'; ?></td>
				<td><input type='radio' name='type' value='1' <?php if($prli[$prlino]['type']=='1')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['printtype1']))echo $interface['name']['printtype1'];else echo '一類一單(自動彙總)'; ?>、<input type='radio' name='type' value='2' <?php if($prli[$prlino]['type']=='2')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['printtype2']))echo $interface['name']['printtype2'];else echo '一類一單'; ?>、<input type='radio' name='type' value='3' <?php if($prli[$prlino]['type']=='3')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['printtype3']))echo $interface['name']['printtype3'];else echo '一項一單(自動彙總)'; ?>、<input type='radio' name='type' value='4' <?php if($prli[$prlino]['type']=='4')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['printtype4']))echo $interface['name']['printtype4'];else echo '一項一單'; ?></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['listtype1']))echo $interface['name']['listtype1'];else echo '內用'; ?></td>
				<td></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printlistlabel']))echo $interface['name']['printlistlabel'];else echo '列印明細單'; ?></td>
				<td><input type='checkbox' name='clientlist1' value='1' <?php if($prli[$prlino]['clientlist1']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printtaglabel']))echo $interface['name']['printtaglabel'];else echo '列印貼紙'; ?></td>
				<td><input type='checkbox' name='tag1' value='1' <?php if($prli[$prlino]['tag1']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printkitchenlabel']))echo $interface['name']['printkitchenlabel'];else echo '列印工作單'; ?></td>
				<td><input type='checkbox' name='kitchen1' value='1' <?php if($prli[$prlino]['kitchen1']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printttkitchenlabel']))echo $interface['name']['printttkitchenlabel'];else echo '列印工作總單'; ?></td>
				<td><input type='checkbox' name='list1' value='1' <?php if(isset($prli[$prlino]['list1'])&&$prli[$prlino]['list1']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['listtype2']))echo $interface['name']['listtype2'];else echo '外帶'; ?></td>
				<td></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printlistlabel']))echo $interface['name']['printlistlabel'];else echo '列印明細單'; ?></td>
				<td><input type='checkbox' name='clientlist2' value='1' <?php if($prli[$prlino]['clientlist2']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printtaglabel']))echo $interface['name']['printtaglabel'];else echo '列印貼紙'; ?></td>
				<td><input type='checkbox' name='tag2' value='1' <?php if($prli[$prlino]['tag2']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printkitchenlabel']))echo $interface['name']['printkitchenlabel'];else echo '列印工作單'; ?></td>
				<td><input type='checkbox' name='kitchen2' value='1' <?php if($prli[$prlino]['kitchen2']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printttkitchenlabel']))echo $interface['name']['printttkitchenlabel'];else echo '列印工作總單'; ?></td>
				<td><input type='checkbox' name='list2' value='1' <?php if(isset($prli[$prlino]['list2'])&&$prli[$prlino]['list2']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['listtype3']))echo $interface['name']['listtype3'];else echo '外送'; ?></td>
				<td></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printlistlabel']))echo $interface['name']['printlistlabel'];else echo '列印明細單'; ?></td>
				<td><input type='checkbox' name='clientlist3' value='1' <?php if($prli[$prlino]['clientlist3']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printtaglabel']))echo $interface['name']['printtaglabel'];else echo '列印貼紙'; ?></td>
				<td><input type='checkbox' name='tag3' value='1' <?php if($prli[$prlino]['tag3']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printkitchenlabel']))echo $interface['name']['printkitchenlabel'];else echo '列印工作單'; ?></td>
				<td><input type='checkbox' name='kitchen3' value='1' <?php if($prli[$prlino]['kitchen3']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printttkitchenlabel']))echo $interface['name']['printttkitchenlabel'];else echo '列印工作總單'; ?></td>
				<td><input type='checkbox' name='list3' value='1' <?php if(isset($prli[$prlino]['list3'])&&$prli[$prlino]['list3']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['listtype4']))echo $interface['name']['listtype4'];else echo '自取'; ?></td>
				<td></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printlistlabel']))echo $interface['name']['printlistlabel'];else echo '列印明細單'; ?></td>
				<td><input type='checkbox' name='clientlist4' value='1' <?php if(isset($prli[$prlino]['clientlist4'])&&$prli[$prlino]['clientlist4']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printtaglabel']))echo $interface['name']['printtaglabel'];else echo '列印貼紙'; ?></td>
				<td><input type='checkbox' name='tag4' value='1' <?php if(isset($prli[$prlino]['tag4'])&&$prli[$prlino]['tag4']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printkitchenlabel']))echo $interface['name']['printkitchenlabel'];else echo '列印工作單'; ?></td>
				<td><input type='checkbox' name='kitchen4' value='1' <?php if(isset($prli[$prlino]['kitchen4'])&&$prli[$prlino]['kitchen4']=='1')echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['printttkitchenlabel']))echo $interface['name']['printttkitchenlabel'];else echo '列印工作總單'; ?></td>
				<td><input type='checkbox' name='list4' value='1' <?php if(isset($prli[$prlino]['list4'])&&$prli[$prlino]['list4']=='1')echo 'checked'; ?>></td>
			</tr>
		</table>
	</form>
</div>