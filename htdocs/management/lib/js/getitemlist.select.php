<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata ORDER BY createtime DESC';
$itemlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$itemname=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini',true);
?>
<tr>
	<td>
		<div class="mod_select" id="pushitem">
			<ul>
				<li>
			<?php
			if(isset($_POST['itemno'])){
				echo '<div class="select_box"><span class="select_txt">';
				if(isset($itemname[$_POST['itemno']]['state'])&&$itemname[$_POST['itemno']]['state']=='1'){
					echo $itemname[$_POST['itemno']]['name1'];
				}
				else if(isset($itemname[$_POST['itemno']]['state'])&&$itemname[$_POST['itemno']]['state']=='0'){
					echo $itemname[$_POST['itemno']]['name1'].'<span style="color:#ff0000;">(停用)</span>';
				}
				else{
					echo '產品不存在';
				}
				if(!isset($itemname[$_POST['itemno']]['counter'])||$itemname[$_POST['itemno']]['counter']!='1'){
					echo '(不紀錄庫存)';
				}
				else{
				}
				echo '</span></div>';
			}
			else{
				echo '<div class="select_box" id="pushitembox">
						<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">';
				if(sizeof($itemlist)==0){
				}
				else{
					foreach($itemlist as $l){
						if($itemname[$l['inumber']]['state']==0||$itemname[$l['inumber']]['counter']!='1'){
						}
						else{
							echo "<a id='".$l['inumber']."'>".$itemname[$l['inumber']]['name1']."</a>";
						}
					}
				}
					echo '</div>
					</div>';
			}
			?>
				</li>
			</ul>
			<?php
			if(isset($_POST['itemno'])){
				echo '<input type="hidden" name="pushitem[]" id="select_value" value="'.$_POST['itemno'].'">';
			}
			else{
				echo '<input type="hidden" name="pushitem[]" id="select_value" value="">';
			}
			?>
		</div>
	</td>
	<td><input type='tel' style='width:117.5px;text-align:right;' name='qty[]' value='<?php if(isset($_POST['qty']))echo $_POST['qty'];else echo '1'; ?>' <?php if(isset($_POST['itemno']))echo "readonly"; ?>></td>
	<td><input type='tel' style='width:60px;text-align:center;' name='unit[]' value='<?php if(isset($_POST['unit']))echo $_POST['unit']; ?>' readonly></td>
	<td><input type='tel' style='width:117.5px;text-align:right;' name='subtotal[]' value='<?php if(isset($_POST['subtotal']))echo $_POST['subtotal'];else echo '0' ?>' <?php if(isset($_POST['itemno']))echo "readonly"; ?>>元</td>
</tr>