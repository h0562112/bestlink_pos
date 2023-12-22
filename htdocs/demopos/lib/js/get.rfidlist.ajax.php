<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';

$setup=parse_ini_file('../../../database/setup.ini',true);
$menuname=parse_ini_file('../../../database/'.$setup['basic']['company'].'-menu.ini',true);

$inumber=preg_split('/,/',$_POST['list']);
for($i=0;$i<sizeof($inumber);$i++){
	$inumber[$i]=intval($inumber[$i]);
}

$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata WHERE quickorder IN ("'.implode('","',$inumber).'") AND (state!="0" OR state IS NULL) ORDER BY quickorder';
$itemdata=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

$quickcode=array_column($itemdata,'quickorder');
$index=0;
for($i=0;$i<sizeof($inumber);$i++){
	if(in_array($inumber[$i],$quickcode)){
		$dbindex=array_search($inumber[$i],$quickcode);
		$itemno=$itemdata[$dbindex]['inumber'];
		if(isset($menuname[$itemno]['insaleinv'])){
		}
		else{
			$menuname[$itemno]['insaleinv']='1';
		}
		if(isset($menuname[$itemno]['itemdis'])){
		}
		else{
			$menuname[$itemno]['itemdis']='1';
		}
		if(isset($menuname[$itemno]['listdis'])){
		}
		else{
			$menuname[$itemno]['listdis']='1';
		}
		if(isset($menuname[$itemno]['bothdis'])){
		}
		else{
			$menuname[$itemno]['bothdis']='1';
		}
		if(isset($menuname[$itemno]['mempoint'])){
		}
		else{
			$menuname[$itemno]['mempoint']='1';
		}
		if(isset($menuname[$itemno]['getpointtype'])){
		}
		else{
			$menuname[$itemno]['getpointtype']='1';
		}
		if(isset($menuname[$itemno]['getpoint'])){
		}
		else{
			$menuname[$itemno]['getpoint']='0';
		}
		echo '<div class="items">';
		echo '<input type="hidden" name="linenumber[]" value="'.($_POST['lastone']+$index*2).'">';
		echo '<input type="hidden" name="order[]" value="'.($_POST['lastorder']+$index).'">';
		echo '<input type="hidden" name="typeno[]" value="'.$itemdata[$dbindex]['fronttype'].'">';
		echo '<input type="hidden" name="type[]" value="">';
		echo '<input type="hidden" name="no[]" value="'.$itemno.'">';
		echo '<input type="hidden" name="needcharge[]" value="'.$menuname[$itemno]['charge'].'">';
		echo '<input type="hidden" name="personcount[]" value="'.$menuname[$itemno]['personcount'].'">';
		echo '<input type="hidden" name="dis1[]" value="'.$menuname[$itemno]['dis1'].'">';
		echo '<input type="hidden" name="dis2[]" value="'.$menuname[$itemno]['dis2'].'">';
		echo '<input type="hidden" name="dis3[]" value="'.$menuname[$itemno]['dis3'].'">';
		echo '<input type="hidden" name="dis4[]" value="'.$menuname[$itemno]['dis4'].'">';
		echo '<input type="hidden" name="name[]" value="'.$menuname[$itemno]['name1'].'">';
		echo '<input type="hidden" name="name2[]" value="'.$menuname[$itemno]['name2'].'">';
		echo '<input type="hidden" name="isgroup[]" value="'.$itemdata[$dbindex]['isgroup'].'">';
		echo '<input type="hidden" name="childtype[]" value="'.$itemdata[$dbindex]['childtype'].'">';
		echo '<input type="hidden" name="mname1[]" value="'.$menuname[$itemno]['mname11'].'">';
		echo '<input type="hidden" name="mname2[]" value="'.$menuname[$itemno]['mname12'].'">';
		echo '<input type="hidden" name="insaleinv[]" value="'.$menuname[$itemno]['insaleinv'].'">';
		echo '<input type="hidden" name="unitprice[]" value="'.$menuname[$itemno]['money1'].'">';
		echo '<input type="hidden" name="money[]" value="'.$menuname[$itemno]['money1'].'">';
		echo '<input type="hidden" name="discount[]" value="0">';
		echo '<input type="hidden" name="discontent[]" value="">';
		echo '<input type="hidden" name="dispoint[]" value="0">';
		echo '<input type="hidden" name="dispointtime[]" value="0">';
		echo '<input type="hidden" name="number[]" value="1">';
		echo '<input type="hidden" name="subtotal[]" value="'.$menuname[$itemno]['money1'].'">';
		echo '<input type="hidden" name="taste1[]" value="">';
		echo '<input type="hidden" name="taste1name[]" value="">';
		echo '<input type="hidden" name="taste1price[]" value="">';
		echo '<input type="hidden" name="taste1number[]" value="">';
		echo '<input type="hidden" name="taste1money[]" value="0">';
		echo '<input type="hidden" name="itemdis[]" value="'.$menuname[$itemno]['itemdis'].'">';
		echo '<input type="hidden" name="listdis[]" value="'.$menuname[$itemno]['listdis'].'">';
		echo '<input type="hidden" name="bothdis[]" value="'.$menuname[$itemno]['bothdis'].'">';
		echo '<input type="hidden" name="usemempoint[]" value="'.$menuname[$itemno]['mempoint'].'">';
		echo '<input type="hidden" name="getpointtype[]" value="'.$menuname[$itemno]['getpointtype'].'">';
		echo '<input type="hidden" name="initgetpoint[]" value="'.$menuname[$itemno]['getpoint'].'">';
		echo '<input type="hidden" name="getpoint[]" value="'.$menuname[$itemno]['getpoint'].'">';
		if(isset($_POST['type'])&&$_POST['type']=='2'){//³æ¥dÅª¨ú
			echo intval($_POST['readlast'])+intval($index+1).'. '.$menuname[$itemno]['name1'];
		}
		else{
			echo ($index+1).'. '.$menuname[$itemno]['name1'];
		}
		echo '</div>';

		$index++;
	}
	else{
	}
}
?>