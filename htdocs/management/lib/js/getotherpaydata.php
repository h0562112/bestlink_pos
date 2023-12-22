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
function quicksort($origArray,$type) {//快速排序//for最低價、最高價
	if (sizeof($origArray) == 1) { 
		return $origArray;
	}
	else if(sizeof($origArray) == 0){
		return 'null';
	}
	else {
		$left = array();
		$right = array();
		$newArray = array();
		$pivot = array_pop($origArray);
		$length = sizeof($origArray);
		for ($i = 0; $i < $length; $i++) {
			if(isset($origArray[$i][$type])&&isset($pivot[$type])){
				if (floatval($origArray[$i][$type]) <= floatval($pivot[$type])) {
					array_push($left,$origArray[$i]);
				} else {
					array_push($right,$origArray[$i]);
				}
			}
			else if(isset($origArray[$i][$type])){
				array_push($right,$origArray[$i]);
			}
			else{
				array_push($left,$origArray[$i]);
			}
		}
		if(sizeof($left)==0){
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort($right,$type);
				$newArray=array_merge(array($pivot),$tempright);
			}
		}
		else{
			$templeft=quicksort($left,$type);
			$n=sizeof($templeft);
			$start=$n;
			$newArray=array_merge($templeft,array($pivot));
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort($right,$type);
				$newArray=array_merge($newArray,$tempright);
			}
		}
		return $newArray;
	}
}
if(isset($_POST['lan'])&&$_POST['lan']!=''){
	if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
		$interface=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('../../lan/interfaceTW.ini')){
		$interface=parse_ini_file('../../lan/interfaceTW.ini',true);
	}
	else{
		$interface='-1';
	}
}
?>
<script>
$(document).ready(function(){
	$('.otherpay input[name="location"]').click(function(){
		if($(this).val()=='CST011'){
			$('.otherpay input[name="should"]').val('1');
			$('.otherpay input[name="pay"]').val('1');
			$('.otherpay input[name="should"]').prop('disabled',true);
			$('.otherpay input[name="pay"]').prop('disabled',true);
		}
		else{
			$('.otherpay input[name="should"]').prop('disabled',false);
			$('.otherpay input[name="pay"]').prop('disabled',false);
		}
	});
});
</script>
<?php
if(isset($_POST['number'])){
	$number=$_POST['number'];
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/otherpay.ini')){
	}
	else{
		$f=fopen('../../../menudata/'.$company.'/'.$dep.'/otherpay.ini','w');
		fwrite($f,'[pay]'.PHP_EOL);
		fwrite($f,'openpay=0'.PHP_EOL);
		fclose($f);
	}
	$otherpay=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/otherpay.ini',true);
?>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?></center></h1>
<div id='fun' style='width:100%;float:left;'>
	<input id='save' name='submit' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<div style='width:100%;overflow:hidden;'>
	<form id='itemform' enctype='multipart/form-data' style='overflow:hidden;'>
		<input type='hidden' name='number' value='<?php echo $number; ?>'>
		<input type='hidden' name='company' value='<?php echo $company; ?>'>
		<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
		<table style='float:left;'>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpayname']))echo $interface['name']['otherpayname'];else echo '付款方式'; ?></td>
				<td><input type='text' name='name' value='<?php echo $otherpay[$_POST['number']]['name']; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpaydbname']))echo $interface['name']['otherpaydbname'];else echo '別稱'; ?></td>
				<td>
					<select name='dbname'>
					<?php
					for($i=1;$i<=10;$i++){
						echo '<option value="TA'.$i.'" ';
						if($_POST['number']==('item'.$i)){
							echo 'selected';
						}
						else{
						}
						echo '>TA'.$i.'</option>';
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpaymem']))echo $interface['name']['otherpaymem'];else echo 'POS會員付款'; ?></td>
				<td><label><input type='radio' name='location' value='memberpoint' <?php if(isset($otherpay[$_POST['number']]['location'])&&$otherpay[$_POST['number']]['location']=='memberpoint')echo 'checked'; ?>>會員點數</label>、<label><input type='radio' name='location' value='membermoney' <?php if(isset($otherpay[$_POST['number']]['location'])&&$otherpay[$_POST['number']]['location']=='membermoney')echo 'checked'; ?>>會員儲值金</label>、<label><input type='radio' name='location' value='CST011' <?php if(!isset($otherpay[$_POST['number']]['location'])||$otherpay[$_POST['number']]['location']=='CST011')echo 'checked'; ?>>以上皆非</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpayshould']))echo $interface['name']['otherpayshould'];else echo '付款門檻'; ?></td>
				<td><input type='text' name='should' value='<?php if(isset($otherpay[$_POST['number']]['should']))echo $otherpay[$_POST['number']]['should'];else echo '1'; ?>' <?php if(!isset($otherpay[$_POST['number']]['location'])||$otherpay[$_POST['number']]['location']=='CST011')echo 'disabled'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpaypay']))echo $interface['name']['otherpaypay'];else echo '付款基數'; ?></td>
				<td><input type='text' name='pay' value='<?php if(isset($otherpay[$_POST['number']]['pay']))echo $otherpay[$_POST['number']]['pay'];else echo '1'; ?>' <?php if(!isset($otherpay[$_POST['number']]['location'])||$otherpay[$_POST['number']]['location']=='CST011')echo 'disabled'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpayinv']))echo $interface['name']['otherpayinv'];else echo '納入發票金額'; ?></td>
				<td><label><input type='radio' name='inv' value='0' <?php if(isset($otherpay[$_POST['number']]['inv'])&&$otherpay[$_POST['number']]['inv']=='0')echo 'checked'; ?>>不納入發票</label>、<label><input type='radio' name='inv' value='1' <?php if(!isset($otherpay[$_POST['number']]['inv'])||$otherpay[$_POST['number']]['inv']=='1')echo 'checked'; ?>>納入發票</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpaytype']))echo $interface['name']['otherpaytype'];else echo '找零設定'; ?></td>
				<td><label><input type='radio' name='type' value='1' <?php if($otherpay[$_POST['number']]['type']=='1')echo 'checked'; ?>>找零</label>、<label><input type='radio' name='type' value='2' <?php if($otherpay[$_POST['number']]['type']=='2')echo 'checked'; ?>>不找零</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpayprice']))echo $interface['name']['otherpayprice'];else echo '面額'; ?></td>
				<td><input type='number' name='price' value='<?php echo $otherpay[$_POST['number']]['price']; ?>'></td>
			</tr>
		</table>
	</form>
</div>
<?php
}
else{
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/otherpay.ini')){
	}
	else{
		$f=fopen('../../../menudata/'.$company.'/'.$dep.'/otherpay.ini','w');
		fwrite($f,'[pay]'.PHP_EOL);
		fwrite($f,'openpay=0'.PHP_EOL);
		fclose($f);
	}
	$otherpay=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/otherpay.ini',true);
?>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?></center></h1>
<div style='width:100%;float:left;'>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<div style='width:100%;overflow:hidden;'>
	<form id='itemform' style='overflow:hidden;'>
		<input type='hidden' name='number' value=''>
		<input type='hidden' name='company' value='<?php echo $company; ?>'>
		<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
		<table style='float;'>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpayname']))echo $interface['name']['otherpayname'];else echo '付款方式'; ?></td>
				<td><input type='text' name='name' value=''></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpaydbname']))echo $interface['name']['otherpaydbname'];else echo '別稱'; ?></td>
				<td>
					<select name='dbname'>
					<?php
					for($i=1;$i<=10;$i++){
						echo '<option value="TA'.$i.'" ';
						if($i==1){
							echo 'selected';
						}
						else{
						}
						echo '>TA'.$i.'</option>';
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpaymem']))echo $interface['name']['otherpaymem'];else echo 'POS會員付款'; ?></td>
				<td><label><input type='radio' name='location' value='memberpoint'>會員點數</label>、<label><input type='radio' name='location' value='membermoney'>會員儲值金</label>、<label><input type='radio' name='location' value='CST011' checked>以上皆非</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpayshould']))echo $interface['name']['otherpayshould'];else echo '付款門檻'; ?></td>
				<td><input type='text' name='should' value='1' disabled></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpaypay']))echo $interface['name']['otherpaypay'];else echo '付款基數'; ?></td>
				<td><input type='text' name='pay' value='1' disabled></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpayinv']))echo $interface['name']['otherpayinv'];else echo '納入發票金額'; ?></td>
				<td><label><input type='radio' name='inv' value='0'>不納入發票</label>、<label><input type='radio' name='inv' value='1' checked>納入發票</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpaytype']))echo $interface['name']['otherpaytype'];else echo '找零設定'; ?></td>
				<td><label><input type='radio' name='type' value='1' checked>找零</label>、<label><input type='radio' name='type' value='2'>不找零</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['otherpayprice']))echo $interface['name']['otherpayprice'];else echo '面額'; ?></td>
				<td><input type='number' name='price' value='1'></td>
			</tr>
		</table>
	</form>
</div>
<?php
}
?>