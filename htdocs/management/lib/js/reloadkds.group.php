<?php
$kds=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-kds.ini',true);
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
echo '<div class="fun">
		<input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增';echo '">
		<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改';echo '">
	</div>
	<div style="width:100%;float:left;">
		<form class="groupform">
			<input type="hidden" name="company" value="'.$_POST['company'].'">
			<input type="hidden" name="dep" value="'.$_POST['dep'].'">
			<table style="border-collapse:collapse;">
				<tr>
					<td></td>
					<td>';if($interface!='-1'&&isset($interface['name']['partition']))echo $interface['name']['partition'];else echo '區域';echo '</td>
					<td>';if($interface!='-1'&&isset($interface['name']['groupofpt']))echo $interface['name']['groupofpt'];else echo '群組';echo '</td>
					<td>';if($interface!='-1'&&isset($interface['name']['ptoflimit']))echo $interface['name']['ptoflimit'];else echo '群組上限';echo '</td>
					<td class="newgroup"></td>
					<td class="newlimit"></td>
				</tr>';
		if(isset($kds)&&sizeof($kds)>1){
			foreach($kds['type']['name'] as $i=>$v){
				foreach($kds['group'.$i]['name'] as $ii=>$vv){
					echo "<tr class='itemrow click'>";
					echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' name='no[]' value='group".$i.'_'.$ii."' style='display:none;'></td>";
					echo "<td>".$v."</td>";
					echo "<td>".$vv."</td>";
					if($kds['group'.$i]['limit'][$ii]!=0){
						echo "<td style='text-align:right;'>".$kds['group'.$i]['limit'][$ii]."</td>";
					}
					else{
						if(isset($interface['name']['notlimit'])){
							echo "<td style='text-align:right;'>".$interface['name']['notlimit']."</td>";
						}
						else{
							echo "<td style='text-align:right;'>無上限</td>";
						}
					}
					echo '<td id="newgroup"></td>
						<td id="newlimit"></td>';
					echo "</tr>";
				}
			}
		}
		else{
		}
	echo '</table>
		</form>
	</div>';
?>