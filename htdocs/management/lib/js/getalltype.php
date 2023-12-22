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
	if(file_exists('../../lan/interface1.ini')){
		$interface=parse_ini_file('../../lan/interface1.ini',true);
	}
	else{
		$interface='-1';
	}
}
$company=$_POST['company'];
$dep=$_POST['dep'];
	echo "<script>
			types=$('#type').tabs();
			types.tabs('option','disabled',[1]);
			$(document).ready(function(){
				$('#type ul .alltypes').click(function(){
					types.tabs('option','disabled',[1]);
				});
			});
		</script>";
	$frontname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-front.ini',true);
	$sortfront=quicksort($frontname,'seq');
	echo '<div style="display:none;">';
	print_r($sortfront);
	echo '</div>';
	$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
	if(file_exists('../../../menudata/disabled.ini')){
		$disabled=parse_ini_file('../../../menudata/disabled.ini',true);
	}
	else{
		$disabled='-1';
	}
	echo '<div id="type" style="overflow:hidden;margin-bottom:3px;">';
		echo "<input type='hidden' name='typegroup' value=''>";
		echo "<ul style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<li><a class='alltypes' href='#alltypes'>";if($interface!='-1'&&isset($interface['name']['alltypetag']))echo $interface['name']['alltypetag'];else echo "全部類別";echo "</a></li>
			<li><a href='#edittype'>";if($interface!='-1'&&isset($interface['name']['singletypetag']))echo $interface['name']['singletypetag'];else echo "單一類別";echo "</a></li>
			<li><a class='voidtype' href='#voidtype'>";if($interface!='-1'&&isset($interface['name']['voidtype']))echo $interface['name']['voidtype'];else echo "已刪除類別";echo "</a></li>
		</ul>";
		echo '<div id="alltypes" style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
				<div>
					<input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo "新增";echo '"';if($disabled!='-1'&&isset($disabled[$_POST['company']])&&isset($_POST['management'])&&$_POST['management']=='0')echo ' disabled';echo '>
					<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo "修改";echo '">
					<input id="delete" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['delete']))echo $interface['name']['delete'];else echo "刪除";echo '">
				</div>
				<table style="border-collapse:collapse;">
					<tr>
						<td></td>
						<td colspan="2"><center>';if($interface!='-1'&&isset($interface['name']['typename']))echo $interface['name']['typename'];else echo "類別名稱";echo '</center></td>
					</tr>
					<tr>
						<td></td>
						<td>';if($interface!='-1'&&isset($interface['name']['mainname']))echo $interface['name']['mainname'];else echo "主語言";echo '</td>
						<td>';if($interface!='-1'&&isset($interface['name']['secname']))echo $interface['name']['secname'];else echo "次語言";echo '</td>
					</tr>';
			for($i=0;$i<sizeof($sortfront);$i++){
				if(isset($sortfront[$i]['state'])&&$sortfront[$i]['state']=='1'){
					echo "<tr class='typerow'>";
					echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' style='display:none;'><input type='hidden' name='number' value='".$sortfront[$i]['typeno']."'></td>";
					echo "<td>".$sortfront[$i]['name'.$initsetting['init']['firlan']]."</td>";
					echo "<td>".$sortfront[$i]['name'.$initsetting['init']['seclan']]."</td>";
					echo "</tr>";
				}
				else{
				}
			}
			/*for($i=0;$i<sizeof($frontname);$i++){
				if($frontname[$i]['state']=='1'){
					echo "<tr class='typerow'>";
					echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' style='display:none;'><input type='hidden' name='number' value='".$i."'></td>";
					echo "<td>".$frontname[$i]['name'.$initsetting['init']['firlan']]."</td>";
					echo "<td>".$frontname[$i]['name'.$initsetting['init']['seclan']]."</td>";
					echo "</tr>";
				}
				else{
				}
			}*/
			echo '</table>';
		echo "</div>";
		echo "<div id='edittype' style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>";
		echo "</div>";
		echo "<div id='voidtype' style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>";
		echo "</div>";
	echo "</div>";
?>