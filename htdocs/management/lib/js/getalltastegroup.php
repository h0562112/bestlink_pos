<?php
include_once '../../../tool/dbTool.inc.php';
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
if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini')){
}
else{
	$f=fopen('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini','w');
	fwrite($f,";[編號]".PHP_EOL);
	fwrite($f,";編號(方便排序使用)".PHP_EOL);
	fwrite($f,";群組名稱".PHP_EOL);
	fwrite($f,";是否為公開備註群組".PHP_EOL);
	fwrite($f,";群組中的元素是否為獨立存在(POS端);-1>>不限制數量1>>單選2>>複選(最多可選兩項)".PHP_EOL);
	fwrite($f,";群組中的元素是否為合併顯示(KDS端);0>>不合併1>>相同項目合併2>>不同項目合併(限同群組中的元素)".PHP_EOL);
	fwrite($f,";排序(不可填入-1)".PHP_EOL);
	fclose($f);
}
$tastegroup=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini',true);
$sorttaste=quicksort($tastegroup,'seq');
/*echo '<div style="display:none;">';
print_r($sorttaste);
echo '</div>';*/
$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
if(file_exists('../../../menudata/disabled.ini')){
	$disabled=parse_ini_file('../../../menudata/disabled.ini',true);
}
else{
	$disabled='-1';
}
echo '<div>
		<input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增';echo '"';if($disabled!='-1'&&isset($disabled[$_POST['company']])&&isset($_POST['management'])&&$_POST['management']=='0')echo ' disabled';echo '>
		<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改';echo '">
	</div>
	<table style="border-collapse:collapse;">
		<tr>
			<td></td>
			<td>';if($interface!='-1'&&isset($interface['name']['tastegroupname']))echo $interface['name']['tastegroupname'];else echo '群組';echo '</td>
			<td>';if($interface!='-1'&&isset($interface['name']['tastegrouptype']))echo $interface['name']['tastegrouptype'];else echo '可選數量';echo '</td>
		</tr>';
if(isset($sorttaste[0]['name'])){
	for($i=0;$i<sizeof($sorttaste);$i++){
		echo "<tr class='tasterow'>";
		echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' style='display:none;'><input type='hidden' name='number' value='".$sorttaste[$i]['groupno']."'></td>";
		echo "<td>".$sorttaste[$i]['name']."</td>";
		echo "<td style='text-align:center;'>";if($sorttaste[$i]['pos']==1)echo "單選";else if($sorttaste[$i]['pos']==-1)echo "不限";else echo "複選";echo "</td>";
		echo "</tr>";
	}
}
else{
}
echo '</table>';
?>