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
$company=$_POST['company'];
$dep=$_POST['dep'];
	echo "<script>
			sectypes=$('#sectype').tabs();
			sectypes.tabs('option','disabled',[1]);
			$(document).ready(function(){
				$('#sectype ul .alltypes').click(function(){
					sectypes.tabs('option','disabled',[1]);
				});
			});
		</script>";
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-rear.ini')){
		$rearname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-rear.ini',true);
	}
	else{
		$rearname='-1';
	}
	if(file_exists('../../../menudata/disabled.ini')){
		$disabled=parse_ini_file('../../../menudata/disabled.ini',true);
	}
	else{
		$disabled='-1';
	}
	echo '<div id="sectype" style="overflow:hidden;margin-bottom:3px;">';
		echo "<input type='hidden' name='typegroup' value=''>";
		echo "<ul style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<li><a class='alltypes' href='#alltypes'>";if($interface!='-1'&&isset($interface['name']['allsectypetag']))echo $interface['name']['allsectypetag'];else echo "全部分析類別";echo "</a></li>
			<li><a href='#edittype'>";if($interface!='-1'&&isset($interface['name']['singlesectypetag']))echo $interface['name']['singlesectypetag'];else echo "單一分析類別";echo "</a></li>
			<li><a class='voidtype' href='#voidtype'>";if($interface!='-1'&&isset($interface['name']['voidsectype']))echo $interface['name']['voidsectype'];else echo "已刪除分析類別";echo "</a></li>
		</ul>";
		echo '<div id="alltypes" style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
		if($rearname!='-1'){
			echo '<div>
					<input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo "新增";echo '"';if($disabled!='-1'&&isset($disabled[$_POST['company']])&&isset($_POST['management'])&&$_POST['management']=='0')echo ' disabled';echo '>
					<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo "修改";echo '">
					<input id="delete" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['delete']))echo $interface['name']['delete'];else echo "刪除";echo '">
				</div>
				<table style="border-collapse:collapse;">
					<tr>
						<td></td>
						<td colspan="2"><center>';if($interface!='-1'&&isset($interface['name']['typename']))echo $interface['name']['typename'];else echo "類別名稱";echo '</center></td>
					</tr>';
			for($i=0;$i<sizeof($rearname);$i++){
				if($rearname[$i]['state']=='1'){
					echo "<tr class='typerow'>";
					echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' style='display:none;'><input type='hidden' name='number' value='".$i."'></td>";
					echo "<td>".$rearname[$i]['name']."</td>";
					echo "</tr>";
				}
				else{
				}
			}
			echo '</table>';
		}
		else{
			if($interface!='-1'&&isset($interface['name']['notopenfun'])){
				echo $interface['name']['notopenfun'];
			}
			else{
				echo '尚未開啟此功能。';
			}
		}
			
		echo "</div>";
		echo "<div id='edittype' style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>";
		echo "</div>";
		echo "<div id='voidtype' style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>";
		echo "</div>";
	echo "</div>";
?>