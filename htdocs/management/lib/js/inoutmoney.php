<?php
include_once '../../../tool/checkweb.php';
$yn=check_mobile();
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
?>
<script>
inoutmoney=$('.inoutmoney').tabs();
$(document).ready(function(){
});
</script>
<style>
</style>
<?php
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/type.ini')){
	$inoutmoney=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/type.ini',true);
}
else{
	$inoutmoney=array("type"=>array("食材","清潔用品","雜支"));
}					  
?>					  
<div class='inoutmoney' style="width:100%;overflow:auto;">
	<ul>
		<li><a id='inoutmoney' href='#setinoutmoney'>
			<?php if($interface!='-1'&&isset($interface['name']['inoutmoney']))
					{
						echo $interface['name']['inoutmoney'];
					}
					else{
						echo 'inoutmoney';
					}
			?>
		</a></li>
	</ul>
	<div id='setinoutmoney' style="width:100%;height:max-content;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<div class="fun">
			<input id="create" class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?>">
			<input id="edit" class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?>">
		</div>
		<div style="width:100%;float:left;">
			<form class="classform">
				<input type="hidden" name="company" value="<?php echo $_POST['company']; ?>">
				<input type="hidden" name="dep" value="<?php echo $_POST['dep']; ?>">
				<table style="border-collapse:collapse;">
					<tr>
						<td></td>
						<td>科目</td>
						<td class="newclass"></td>
					</tr>
			<?php
			foreach($inoutmoney['type'] as $k=>$u){
				echo "<tr class='itemrow click'>";
				echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' name='no[]' value='".$k."' style='display:none;'></td>";
				echo "<td>".$u."</td>";
				echo "<td id='newclass'></td>";
				echo "</tr>";
			}
			?>
				</table>
			</form>
		</div>
	</div>
</div>