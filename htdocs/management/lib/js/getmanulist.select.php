<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','manufact.db','','','','sqlite');
$sql="SELECT * FROM manulist";
$manulist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>
<div class="mod_select" id='manufact'>
	<ul>
		<li>
		<?php
		if(isset($_POST['manuno'])){
			echo '<div class="select_box">
					<span class="select_txt">';
			foreach($manulist as $v){
				if($v['no']==$_POST['manuno']){
					echo $v['manuname'];
				}
				else{
				}
			}
			echo '</span></div>';
		}
		else{
			echo '<div class="select_box" id="manufactbox">
				<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">';
			if(sizeof($manulist)==0){
			}
			else{
				foreach($manulist as $l){
					echo "<a id='".$l['no']."'>".$l['manuname']."</a>";
				}
			}
				echo '</div>
				</div>';
		}
		?>
		</li>
	</ul>
	<?php
	if(isset($_POST['manuno'])){
		echo '<input type="hidden" name="manufact" id="select_value" value="'.$_POST['manuno'].'">';
	}
	else{
		echo '<input type="hidden" name="manufact" id="select_value" value="">';
	}
	?>
</div>