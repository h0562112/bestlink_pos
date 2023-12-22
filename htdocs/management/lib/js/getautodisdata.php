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
	$('#setdisdata input[name="listtype"]').click(function(){
		if($(this).val()<1){//優惠次數無上限OR關閉優惠(0次)
			$('#setdisdata input[name="listtypenumber"]').val('');
			$('#setdisdata input[name="listtypenumber"]').prop('disabled',true);
		}
		else{//填寫優惠次數
			//$('#setdisdata input[name="listtypenumber"]').val('');
			$('#setdisdata input[name="listtypenumber"]').prop('disabled',false);
		}
	});
	$('#setdisdata input[name="distype"]').click(function(){
		if($(this).val()=='1'){//折讓
			$('#setdisdata input[name="max"]').prop('checked',false);
			$('#setdisdata input[name="max"]:eq(0)').prop('checked',true);
			$('#setdisdata input[name="max"]').prop('disabled',false);
			$('#setdisdata input[name="maxnumber"]').prop('disabled',true);
			$('#setdisdata input[name="maxnumber"]').val('');
			$('#setdisdata input[name="dismoney"]').prop('disabled',true);
			$('#setdisdata input[name="dismoney"]').val('0');
		}
		else if($(this).val()=='2'||$(this).val()=='3'){//折扣or單一價
			$('#setdisdata input[name="max"]').prop('checked',false);
			$('#setdisdata input[name="max"]:eq(0)').prop('checked',true);
			$('#setdisdata input[name="max"]').prop('disabled',true);
			$('#setdisdata input[name="maxnumber"]').prop('disabled',true);
			$('#setdisdata input[name="maxnumber"]').val('');
			$('#setdisdata input[name="dismoney"]').prop('disabled',false);
			//$('#setdisdata input[name="dismoney"]').val('0');
		}
		else{
		}

		if($(this).val()=='2'){//折扣
			$('#setdisdata #dismoney1').css({'display':'block'});
			$('#setdisdata #dismoney2').css({'display':'none'});
			$('#setdisdata #dismoney1hint').css({'display':'block'});
		}
		else if($(this).val()=='3'){//單一價
			$('#setdisdata #dismoney1').css({'display':'none'});
			$('#setdisdata #dismoney2').css({'display':'block'});
			$('#setdisdata #dismoney1hint').css({'display':'none'});
		}
		else{
		}
	});
	$('#setdisdata input[name="max"]').click(function(){
		if($(this).val()=='-1'){//折讓金額無上限
			$('#setdisdata input[name="maxnumber"]').val('');
			$('#setdisdata input[name="maxnumber"]').prop('disabled',true);
		}
		else{//填寫折讓金額
			//$('#setdisdata input[name="maxnumber"]').val('');
			$('#setdisdata input[name="maxnumber"]').prop('disabled',false);
		}
	});
	$('#setdisdata input[name="group"]').click(function(){
		if($(this).val()=='0'){//不分群
			$('#setdisdata input[name="gpstart"]').prop('disabled',true);
		}
		else{//分群
			$('#setdisdata input[name="gpstart"]').prop('disabled',false);
		}
	});
});
</script>
<?php
if(isset($_POST['number'])){
	$para=preg_split('/-/',$_POST['number']);
	$number=$para[1];
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	$dis=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$para[0].'.ini',true);
?>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?></center></h1>
<div id='fun' style='width:100%;float:left;'>
	<input id='save' name='submit' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<div style='width:100%;overflow:hidden;'>
	<form id='itemform' enctype='multipart/form-data' style='overflow:hidden;'>
		<input type='hidden' name='disfile' value='<?php echo $para[0]; ?>'>
		<input type='hidden' name='number' value='<?php echo $number; ?>'>
		<input type='hidden' name='company' value='<?php echo $company; ?>'>
		<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
		<table style='float:left;'>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisname']))echo $interface['name']['autodisname'];else echo '優惠名稱'; ?></td>
				<td><input type='text' name='name' value='<?php echo $dis[$number]['name']; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisgnumber']))echo $interface['name']['autodisgnumber'];else echo '判斷質數'; ?></td>
				<td><input type='text' name='gnumber' value='<?php echo $dis[$number]['gnumber']; ?>' readonly></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodislisttype']))echo $interface['name']['autodislisttype'];else echo '優惠次數'; ?></td>
				<td><label><input type='radio' name='listtype' value='0' <?php if($dis[$number]['listtype']=='0')echo 'checked'; ?>>關閉優惠(0次)</label>、<label><input type='radio' name='listtype' value='-1' <?php if($dis[$number]['listtype']=='-1')echo 'checked'; ?>>無上限</label>、<label><input type='radio' name='listtype' value='other' <?php if($dis[$number]['listtype']>0)echo 'checked'; ?>>優惠次數</label><input type='number' name='listtypenumber' value='<?php if($dis[$number]['listtype']>0)echo $dis[$number]['listtype']; ?>' <?php if($dis[$number]['listtype']<1)echo 'disabled'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisbuy']))echo $interface['name']['autodisbuy'];else echo '買N'; ?></td>
				<td><input type='number' name='buy' value='<?php echo $dis[$number]['buy']; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisfree']))echo $interface['name']['autodisfree'];else echo '送M'; ?></td>
				<td><input type='number' name='free' value='<?php echo $dis[$number]['free']; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['distype']))echo $interface['name']['distype'];else echo '優惠類別'; ?></td>
				<td><label><input type='radio' name='distype' value='1' <?php if(!isset($dis[$number]['distype'])||$dis[$number]['distype']=='1')echo 'checked'; ?>>折讓</label>、<label><input type='radio' name='distype' value='2' <?php if(isset($dis[$number]['distype'])&&$dis[$number]['distype']=='2')echo 'checked'; ?>>折扣</label>、<label><input type='radio' name='distype' value='3' <?php if(isset($dis[$number]['distype'])&&$dis[$number]['distype']=='3')echo 'checked'; ?>>單一價</label><!-- 、<label><input type='radio' name='type' value='4'>單一價</label> --></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodistype']))echo $interface['name']['autodistype'];else echo '優惠基礎'; ?></td>
				<td><label><input type='radio' name='type' value='1' <?php if($dis[$number]['type']=='1')echo 'checked'; ?>>最低價</label>、<label><input type='radio' name='type' value='2' <?php if($dis[$number]['type']=='2')echo 'checked'; ?>>最高價</label><!-- 、<label><input type='radio' name='type' value='3' <?php if($dis[$number]['type']=='3')echo 'checked'; ?>>均價</label>、<label><input type='radio' name='type' value='4' <?php if($dis[$number]['type']=='4')echo 'checked'; ?>>單一價</label> --></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodismax']))echo $interface['name']['autodismax'];else echo '折讓上限'; ?></td>
				<td><label><input type='radio' name='max' value='-1' <?php if($dis[$number]['max']=='-1')echo 'checked'; ?>>無上限</label>、<label><input type='radio' name='max' value='other' <?php if($dis[$number]['max']!='-1')echo 'checked'; ?>>上限金額</label><input type='number' name='maxnumber' value='<?php if($dis[$number]['max']!='-1')echo $dis[$number]['max']; ?>' <?php if($dis[$number]['max']=='-1')echo 'disabled'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><span id='dismoney1' <?php if(isset($dis[$number]['distype'])&&$dis[$number]['distype']=='3')echo 'style="display:none;"'; ?>><?php if($interface!='-1'&&isset($interface['name']['dismoney1']))echo $interface['name']['dismoney1'];else echo '折扣'; ?></span><span id='dismoney2' <?php if(isset($dis[$number]['distype'])&&$dis[$number]['distype']=='3')echo '';else echo 'style="display:none;"'; ?>><?php if($interface!='-1'&&isset($interface['name']['dismoney2']))echo $interface['name']['dismoney2'];else echo '單一價'; ?></span></td>
				<td><input type='number' name='dismoney' value='<?php if(isset($dis[$number]['dismoney']))echo $dis[$number]['dismoney'];else echo '0'; ?>' <?php if(!isset($dis[$number]['distype'])||$dis[$number]['distype']=='1')echo 'disabled'; ?>><br><span id='dismoney1hint' style='color:#ff0000;font-size:12px;'>e.g.9折請填入90</span></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodistaste']))echo $interface['name']['autodistaste'];else echo '金額判斷'; ?></td>
				<td><label><input type='radio' name='taste' value='0' <?php if($dis[$number]['taste']=='0')echo 'checked'; ?>>不含加料(原價)</label>、<label><input type='radio' name='taste' value='1' <?php if($dis[$number]['taste']=='1')echo 'checked'; ?>>含加料</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisgroup']))echo $interface['name']['autodisgroup'];else echo '群組判斷'; ?></td>
				<td><label><input type='radio' name='group' value='0' <?php if($dis[$number]['group']=='0')echo 'checked'; ?>>不分群</label>、<label><input type='radio' name='group' value='1' <?php if($dis[$number]['group']=='1')echo 'checked'; ?>>分群</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisgpstart']))echo $interface['name']['autodisgpstart'];else echo '分群起始值'; ?></td>
				<td><label><input type='radio' name='gpstart' value='1' <?php if($dis[$number]['gpstart']=='1')echo 'checked'; ?> <?php if($dis[$number]['group']=='0')echo 'disabled'; ?>>由高到低</label>、<label><input type='radio' name='gpstart' value='2' <?php if($dis[$number]['gpstart']=='2')echo 'checked'; ?> <?php if($dis[$number]['group']=='0')echo 'disabled'; ?>>由低到高</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisdisitem']))echo $interface['name']['autodisdisitem'];else echo '包含折扣商品'; ?></td>
				<td><label><input type='radio' name='disitem' value='0' <?php if($dis[$number]['disitem']=='0')echo 'checked'; ?>>不含已折扣商品</label>、<label><input type='radio' name='disitem' value='2' <?php if($dis[$number]['disitem']=='1')echo 'checked'; ?>>包含折扣商品</label></td>
			</tr>
		</table>
	</form>
</div>
<?php
}
else{
	$company=$_POST['company'];
	$dep=$_POST['dep'];
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
		<table style='float:left;'>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisname']))echo $interface['name']['autodisname'];else echo '優惠名稱'; ?></td>
				<td><input type='text' name='name' value=''></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisdisfile']))echo $interface['name']['autodisdisfile'];else echo '套用優惠'; ?></td>
				<td>
					<select name='disfile'>
						<option value='discount1' selected>discount1</option>
						<option value='discount2'>discount2</option>
						<option value='discount3'>discount3</option>
						<option value='discount4'>discount4</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodislisttype']))echo $interface['name']['autodislisttype'];else echo '優惠次數'; ?></td>
				<td><label><input type='radio' name='listtype' value='0'>關閉優惠(0次)</label>、<label><input type='radio' name='listtype' value='-1' checked>無上限</label>、<label><input type='radio' name='listtype' value='other'>優惠次數</label><input type='number' name='listtypenumber' value='' disabled></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisbuy']))echo $interface['name']['autodisbuy'];else echo '買N'; ?></td>
				<td><input type='number' name='buy' value='1'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisfree']))echo $interface['name']['autodisfree'];else echo '送M'; ?></td>
				<td><input type='number' name='free' value='1'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['distype']))echo $interface['name']['distype'];else echo '優惠類別'; ?></td>
				<td><label><input type='radio' name='distype' value='1' checked>折讓</label>、<label><input type='radio' name='distype' value='2'>折扣</label>、<label><input type='radio' name='distype' value='3'>單一價</label><!-- 、<label><input type='radio' name='type' value='4'>單一價</label> --></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodistype']))echo $interface['name']['autodistype'];else echo '優惠基礎'; ?></td>
				<td><label><input type='radio' name='type' value='1' checked>最低價</label>、<label><input type='radio' name='type' value='2'>最高價</label><!-- 、<label><input type='radio' name='type' value='3'>均價</label>、<label><input type='radio' name='type' value='4'>單一價</label> --></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodismax']))echo $interface['name']['autodismax'];else echo '折讓上限'; ?></td>
				<td><label><input type='radio' name='max' value='-1' checked>無上限</label>、<label><input type='radio' name='max' value='other'>上限金額</label><input type='number' name='maxnumber' value='' disabled></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><span id='dismoney1'><?php if($interface!='-1'&&isset($interface['name']['dismoney1']))echo $interface['name']['dismoney1'];else echo '折扣'; ?></span><span  id='dismoney2' style='display:none;'><?php if($interface!='-1'&&isset($interface['name']['dismoney2']))echo $interface['name']['dismoney2'];else echo '單一價'; ?></span></td>
				<td><input type='number' name='dismoney' value='0' disabled><br><span id='dismoney1hint' style='color:#ff0000;font-size:12px;'>e.g.9折請填入90</span></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodistaste']))echo $interface['name']['autodistaste'];else echo '金額判斷'; ?></td>
				<td><label><input type='radio' name='taste' value='0' checked>不含加料(原價)</label>、<label><input type='radio' name='taste' value='1'>含加料</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisgroup']))echo $interface['name']['autodisgroup'];else echo '群組判斷'; ?></td>
				<td><label><input type='radio' name='group' value='0' checked>不分群</label>、<label><input type='radio' name='group' value='1'>分群</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisgpstart']))echo $interface['name']['autodisgpstart'];else echo '分群起始值'; ?></td>
				<td><label><input type='radio' name='gpstart' value='1' checked disabled>由高到低</label>、<label><input type='radio' name='gpstart' value='2' disabled>由低到高</label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodisdisitem']))echo $interface['name']['autodisdisitem'];else echo '包含折扣商品'; ?></td>
				<td><label><input type='radio' name='disitem' value='0' checked>不含已折扣商品</label>、<label><input type='radio' name='disitem' value='2'>包含折扣商品</label></td>
			</tr>
		</table>
	</form>
</div>
<?php
}
?>