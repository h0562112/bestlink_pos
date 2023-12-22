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
$frontname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-front.ini',true);
$sortfront=quicksort($frontname,'seq');
$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
echo '<div>
		<input id="return" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['return']))echo $interface['name']['return'];else echo "復原";echo '">
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
	if(isset($sortfront[$i]['state'])&&$sortfront[$i]['state']=='0'){
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
?>