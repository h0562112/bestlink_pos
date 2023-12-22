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
		kds=$('#kds').tabs();
	</script>";
if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini')){
}
else{
	$f=fopen('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini','w');
	fclose($f);
}
$kds=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini',true);
echo '<div id="kds" style="overflow:hidden;margin-bottom:3px;">';
	echo "<ul style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<li><a class='allpartitions' href='#partitions'>";if($interface!='-1'&&isset($interface['name']['setpartition']))echo $interface['name']['setpartition'];else echo '區域設定';echo "</a></li>
			<li><a class='allgroupofpts' href='#groupofpts'>";if($interface!='-1'&&isset($interface['name']['setgroupofpt']))echo $interface['name']['setgroupofpt'];else echo '群組設定';echo "</a></li>
		</ul>";
	echo '<div id="partitions" style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
			<div class="fun">
				<input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增';echo '">
				<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改';echo '">
			</div>
			<div style="width:100%;float:left;">
				<form class="partitionform">
					<input type="hidden" name="company" value="'.$company.'">
					<input type="hidden" name="dep" value="'.$dep.'">';
				if(isset($_POST['lan'])&&$_POST['lan']!=''){
					echo '<input type="hidden" name="lan" value="'.$_POST['lan'].'">';
				}
				else{
					echo '<input type="hidden" name="lan" value="">';
				}
				echo '<table style="border-collapse:collapse;">
						<tr>
							<td></td>
							<td>';if($interface!='-1'&&isset($interface['name']['partition']))echo $interface['name']['partition'];else echo '區域';echo '</td>
							<td class="newpartition" style="text-align:center;"></td>
						</tr>';
				if(isset($kds['type']['name'])){
					foreach($kds['type']['name'] as $k=>$u){
						echo "<tr class='itemrow click'>";
						echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' name='no[]' value='".$k."' style='display:none;'></td>";
						echo "<td>".$u."</td>";
						echo "<td id='newpartition'></td>";
						echo "</tr>";
					}
				}
				else{
				}
			echo '</table>
				</form>
			</div>';
	echo "</div>";
	echo '<div id="groupofpts" style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
			<div class="fun">
				<input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增';echo '">
				<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改';echo '">
			</div>
			<div style="width:100%;float:left;">
				<form class="groupform">
					<input type="hidden" name="company" value="'.$company.'">
					<input type="hidden" name="dep" value="'.$dep.'">
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
	echo "</div>";
echo "</div>";
?>