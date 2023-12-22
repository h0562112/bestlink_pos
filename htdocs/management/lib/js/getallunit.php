<?php
include_once '../../../tool/inilib.php';
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
		unit=$('#unit').tabs();
	</script>";
$unit=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/unit.ini',true);
$straw=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/straw.ini',true);
echo '<div id="unit" style="overflow:hidden;margin-bottom:3px;">';
	echo "<ul style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<li><a class='allunit' href='#allunits'>";if($interface!='-1'&&isset($interface['name']['allunittag']))echo $interface['name']['allunittag'];else echo '全部單位';echo "</a></li>
			<li><a class='allstraw' href='#allstraws'>";if($interface!='-1'&&isset($interface['name']['strawtag']))echo $interface['name']['strawtag'];else echo '吸管設定';echo "</a></li>
		</ul>";
	echo '<div id="allunits" style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
			<div class="fun">
				<input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增';echo '">
				<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改';echo '">
			</div>
			<div style="width:100%;float:left;">
				<form class="unitform">
					<input type="hidden" name="company" value="'.$company.'">
					<input type="hidden" name="dep" value="'.$dep.'">
					<table style="border-collapse:collapse;">
						<tr>
							<td></td>
							<td>';if($interface!='-1'&&isset($interface['name']['unit']))echo $interface['name']['unittitle'];else echo '單位';echo '</td>
							<td class="newunit"></td>
						</tr>';
				foreach($unit['unit'] as $k=>$u){
					echo "<tr class='itemrow click'>";
					echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' name='no[]' value='".$k."' style='display:none;'></td>";
					echo "<td>".$u."</td>";
					echo "<td id='newunit'></td>";
					echo "</tr>";
				}
			echo '</table>
				</form>
			</div>';
	echo "</div>";
	echo '<div id="allstraws" style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
			<div class="fun">
				<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改';echo '">
			</div>
			<div style="width:100%;float:left;">
				<form class="strawform">
					<input type="hidden" name="company" value="'.$company.'">
					<input type="hidden" name="dep" value="'.$dep.'">
					<table style="border-collapse:collapse;">
						<tr>
							<td></td>
							<td>';if($interface!='-1'&&isset($interface['name']['seqtitle']))echo $interface['name']['seqtitle'];else echo '優先順序';echo '</td>
							<td>';if($interface!='-1'&&isset($interface['name']['strawnametitle']))echo $interface['name']['strawnametitle'];else echo '吸管名稱';echo '</td>
							<td class="newstraw"></td>
						</tr>';
				foreach($straw['straw'] as $k=>$u){
					if($k=='999'){
						continue;
					}
					else{
						echo "<tr class='itemrow click'>";
						echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' name='no[]' value='".$k."' style='display:none;'></td>";
						echo "<td>".$k."</td>";
						echo "<td>".$u."</td>";
						echo "<td id='newstraw'></td>";
						echo "</tr>";
					}
				}
			echo '</table>
				</form>
			</div>';
	echo "</div>";
echo "</div>";
?>