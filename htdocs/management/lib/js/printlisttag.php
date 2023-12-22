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
$list=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/itemprinttype.ini',true);
echo "<script>
		prli=$('#prlitagbox').tabs();
		prli.tabs('option','disabled',[1]);
		$(document).ready(function(){
			$('#prlitagbox ul .allprli').click(function(){
				prli.tabs('option','disabled',[1]);
				$('#prlitagbox #prlitag .itemrow').css({'background-color':'#ffffff'});
				$('#prlitagbox #prlitag .itemrow input[type=\"checkbox\"]').prop('checked',false);
				$('#prlitagbox #prlitag .itemrow #chimg').attr('src','./img/noch.png');
			});
		});
	</script>";
echo '<div id="prlitagbox" style="overflow:hidden;margin-bottom:3px;">';
	echo "<ul style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<li><a class='allprli' href='#prlitag'>";if($interface!='-1'&&isset($interface['name']['printmenu']))echo $interface['name']['printmenu'];else echo '列印類別';echo "</a></li>
			<li><a href='#editprli'>";if($interface!='-1'&&isset($interface['name']['editcontent']))echo $interface['name']['editcontent'];else echo '修改內容';echo "</a></li>
		</ul>";
	echo '<div id="prlitag" style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
			<div class="funbox">
				<!-- <input id="create" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增';echo '"> -->
				<input id="edit" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改';echo '">
				<!-- <input id="delete" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['delete']))echo $interface['name']['delete'];else echo '刪除';echo '"> -->
			</div>
			<div style="width:100%;float:left;">
				<form id="prliform">
					<input type="hidden" name="company" value="'.$company.'">
					<input type="hidden" name="dep" value="'.$dep.'">
					<table style="border-collapse:collapse;">
						<tr>
							<td></td>
							<td>';if($interface!='-1'&&isset($interface['name']['typename']))echo $interface['name']['typename'];else echo '類別名稱';echo '</td>
							<td id="newname"></td>
						</tr>';
				foreach($list as $k=>$v){
					if($v['state']=='1'){
						echo "<tr class='itemrow' style='cursor:pointer;'>";
						echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' name='prlicheckbox[]' value='".$k."' style='display:none;'></td>";
						echo "<td>".$v['name']."</td>";
						echo "<td id='newname'></td>";
						echo "</tr>";
					}
					else{
					}
				}
			echo '</table>
				</form>
			</div>';
	echo "</div>
		<div id='editprli' style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>";
echo "</div>";
?>