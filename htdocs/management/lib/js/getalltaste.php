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
	echo "<script>
			tastes=$('#taste').tabs();
			tastes.tabs('option','disabled',[1,4]);
			$(document).ready(function(){
				$('#taste ul .alltastes').click(function(){
					tastes.tabs('option','disabled',[1,4]);
				});
			});
		</script>";
	$tastename=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-taste.ini',true);
	$sorttaste=quicksort($tastename,'seq');
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini')){
		$tastegroup=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini',true);
	}
	else{
	}
	/*echo '<div style="display:none;">';
	print_r($sorttaste);
	echo '</div>';*/
	$straw=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/straw.ini',true);
	$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
	if(file_exists('../../../menudata/disabled.ini')){
		$disabled=parse_ini_file('../../../menudata/disabled.ini',true);
	}
	else{
		$disabled='-1';
	}
	echo '<div id="taste" style="overflow:hidden;">';
		echo "<input type='hidden' name='tastegroup' value=''>";
		echo "<ul style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<li><a class='alltastes' href='#alltastes'>";if($interface!='-1'&&isset($interface['name']['alltastetag']))echo $interface['name']['alltastetag'];else echo '全部備註與加料';echo "</a></li>
			<li><a href='#edittaste'>";if($interface!='-1'&&isset($interface['name']['singletastetag']))echo $interface['name']['singletastetag'];else echo '單一備註或加料';echo "</a></li>
			<li><a class='voidtaste' href='#voidtaste'>";if($interface!='-1'&&isset($interface['name']['voidtaste']))echo $interface['name']['voidtaste'];else echo '已刪除備註與加料';echo"</a></li>
			<li><a class='alltastegroup' href='#alltastegroup'>";if($interface!='-1'&&isset($interface['name']['alltastegroup']))echo $interface['name']['alltastegroup'];else echo '備註與加料群組';echo"</a></li>
			<li><a class='edittastegroup' href='#edittastegroup'>";if($interface!='-1'&&isset($interface['name']['edittastegroup']))echo $interface['name']['edittastegroup'];else echo '設定群組';echo"</a></li>
		</ul>";
		echo '<div id="alltastes" style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
				<div>
					<input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增';echo '"';if($disabled!='-1'&&isset($disabled[$_POST['company']])&&isset($_POST['management'])&&$_POST['management']=='0')echo ' disabled';echo '>
					<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改';echo '">
					<input id="delete" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['delete']))echo $interface['name']['delete'];else echo '刪除';echo '">
				</div>
				<table style="border-collapse:collapse;">
					<tr>
						<td></td>
						<td colspan="2"><center>';if($interface!='-1'&&isset($interface['name']['itemname']))echo $interface['name']['itemname'];else echo '名稱';echo '</center></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td>';if($interface!='-1'&&isset($interface['name']['mainname']))echo $interface['name']['mainname'];else echo '主語言';echo '</td>
						<td>';if($interface!='-1'&&isset($interface['name']['secname']))echo $interface['name']['secname'];else echo '次語言';echo '</td>
						<td>';if($interface!='-1'&&isset($interface['name']['groupname']))echo $interface['name']['groupname'];else echo '群組';echo '</td>
						<td>';if($interface!='-1'&&isset($interface['name']['moneylabel']))echo $interface['name']['moneylabel'];else echo '價格';echo '</td>
						<td>';if($interface!='-1'&&isset($interface['name']['strawlabel']))echo $interface['name']['strawlabel'];else echo '吸管設定';echo '</td>
					</tr>';
			for($i=0;$i<sizeof($sorttaste);$i++){
				if(isset($sorttaste[$i]['state'])&&$sorttaste[$i]['state']=='1'){
					echo "<tr class='tasterow'>";
					echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' style='display:none;'><input type='hidden' name='number' value='".$sorttaste[$i]['tasteno']."'></td>";
					echo "<td>".$sorttaste[$i]['name'.$initsetting['init']['firlan']]."</td>";
					echo "<td>".$sorttaste[$i]['name'.$initsetting['init']['seclan']]."</td>";
					echo "<td>";
					if(isset($sorttaste[$i]['group'])){
						if(isset($tastegroup)&&isset($tastegroup[$sorttaste[$i]['group']])){
							echo $tastegroup[$sorttaste[$i]['group']]['name'];
						}
						else{
							echo $sorttaste[$i]['group'];
						}
					}
					else{
						echo '<span style="color:#ff0000;">未設定群組</span>';
					}
					echo "</td>";
					echo "<td>".$sorttaste[$i]['money']."</td>";
					echo "<td>";if(isset($sorttaste[$i]['straw'])&&$sorttaste[$i]['straw']!='999'&&$sorttaste[$i]['straw']!=''&&$sorttaste[$i]['straw']!=null)echo $straw['straw'][$sorttaste[$i]['straw']];else ;echo "</td>";
					echo "</tr>";
				}
				else{
				}
			}
			/*for($i=0;$i<sizeof($tastename);$i++){
				if(isset($tastename[$i])&&$tastename[$i]['state']=='1'){
					echo "<tr class='tasterow'>";
					echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' style='display:none;'><input type='hidden' name='number' value='".$i."'></td>";
					echo "<td>".$tastename[$i]['name'.$initsetting['init']['firlan']]."</td>";
					echo "<td>".$tastename[$i]['name'.$initsetting['init']['seclan']]."</td>";
					echo "<td>".$tastename[$i]['money']."</td>";
					echo "<td>";if(isset($tastename[$i]['straw'])&&$tastename[$i]['straw']!='999'&&$tastename[$i]['straw']!=''&&$tastename[$i]['straw']!=null)echo $straw['straw'][$tastename[$i]['straw']];else ;echo "</td>";
					echo "</tr>";
				}
				else{
				}
			}*/
			echo '</table>';
		echo "</div>";
		echo "<div id='edittaste'>";
		echo "</div>";
		echo "<div id='voidtaste'>";
		echo "</div>";
		echo "<div id='alltastegroup'>";
		echo "</div>";
		echo "<div id='edittastegroup'>";
		echo "</div>";
	echo "</div>";
?>