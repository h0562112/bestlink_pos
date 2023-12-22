<?php
include_once '../../../tool/dbTool.inc.php';
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
$conn2=sqlconnect('../../../menudata/'.$company.'/'.$dep,'menu.db','','','','sqlite');
$sql='SELECT fronttype,inumber FROM itemsdata WHERE state=0 AND state IS NOT NULL ORDER BY replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(fronttype))), "\'", ""), "0", "0")||fronttype,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(frontsq))), "\'", ""), "0", "0")||frontsq,replace(replace(substr(quote(zeroblob((10 + 1) / 2)), 3, (10 - length(inumber))), "\'", ""), "0", "0")||inumber';
$items=sqlquery($conn2,$sql,'sqlite');
sqlclose($conn2,'sqlite');
$itemname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-menu.ini',true);
$frontname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-front.ini',true);
$laninit=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/laninit.ini',true);
$unit=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/unit.ini',true);
echo '<div>
		<input id="return" class="initbutton" type="button" value="';if($interface!='-1'&&isset($interface['name']['return']))echo $interface['name']['return'];else echo '復原';echo'">
	</div>
	<div style="width:100%;float:left;">
		<table style="border-collapse:collapse;">
			<tr>
				<td></td>
				<td style=width:62px;>';if($interface!='-1'&&isset($interface['name']['itemnumber=']))echo $interface['name']['itemnumber='];else echo '編號';echo'</td>
				<td>';if($interface!='-1'&&isset($interface['name']['typelabel']))echo $interface['name']['typelabel'];else echo '類別';echo'</td>
				<td></td>
				<td>';if($interface!='-1'&&isset($interface['name']['itemnamelabel']))echo $interface['name']['itemnamelabel'];else echo '產品名稱';echo'</td>
				<td></td>
				<td>';if($interface!='-1'&&isset($interface['name']['checkstock']))echo $interface['name']['checkstock'];else echo '是否紀錄庫存';echo'</td>
				<td>';if($interface!='-1'&&isset($interface['name']['moneynamelabel']))echo $interface['name']['moneynamelabel'];else echo '價格名稱';echo'</td>
				<td></td>
				<td>';if($interface!='-1'&&isset($interface['name']['moneylabel']))echo $interface['name']['moneylabel'];else echo '價格';echo'</td>
				<td></td>
			</tr>';
	for($i=0;$i<sizeof($items);$i++){
		if(isset($itemname[$items[$i]['inumber']]['state'])&&$itemname[$items[$i]['inumber']]['state']=='0'){
			echo "<tr class='itemrow'>";
			echo "<td style='width:62px;'><img id='chimg' src='./img/noch.png'><input type='checkbox' style='display:none;'></td>";
			echo "<td>".$items[$i]['inumber']."</td>";
			if($frontname[$items[$i]['fronttype']]['state']=='1'){
				echo "<td>".$frontname[$items[$i]['fronttype']]['name'.$laninit['init']['firlan']]."</td>";
				echo "<td>".$frontname[$items[$i]['fronttype']]['name'.$laninit['init']['seclan']]."</td>";
			}
			else{
				echo "<td>".$frontname[$items[$i]['fronttype']]['name'.$laninit['init']['firlan']]."<span style='color:#ff0000;'>⊘</span></td>";
				echo "<td>".$frontname[$items[$i]['fronttype']]['name'.$laninit['init']['seclan']]."<span style='color:#ff0000;'>⊘</span></td>";
			}
			echo "<td><input type='hidden' name='itemdep' value='".$items[$i]['fronttype']."'><input type='hidden' name='number' value='".$items[$i]['inumber']."'>".$itemname[$items[$i]['inumber']]['name'.$laninit['init']['firlan']]."</td>";
			echo "<td>".$itemname[$items[$i]['inumber']]['name'.$laninit['init']['seclan']]."</td>";
			echo "<td>";
			if($itemname[$items[$i]['inumber']]['counter']=='0'||$itemname[$items[$i]['inumber']]['counter']=='-999'){
			}
			else{
				if($itemname[$items[$i]['inumber']]['counter']=='2'){
					if($interface!='-1'&&isset($interface['name']['stockname2view'])){
						echo $interface['name']['stockname2'];
					}
					else{
						echo "限量商品(商品數量)";
					}
				}
				else if($itemname[$items[$i]['inumber']]['counter']=='3'){
					if($interface!='-1'&&isset($interface['name']['stockname3view'])){
						echo $interface['name']['stockname3'];
					}
					else{
						echo "限量商品(帳單數量)";
					}
				}
				else{
					if($interface!='-1'&&isset($interface['name']['stockname1view'])){
						echo $interface['name']['stockname1'];
					}
					else{
						echo "紀錄庫存";
					}
				}
			}
			echo "</td>";
			echo "<td>".$itemname[$items[$i]['inumber']]['mname1'.$laninit['init']['firlan']]."</td>";
			echo "<td>".$itemname[$items[$i]['inumber']]['mname1'.$laninit['init']['seclan']]."</td>";
			echo "<td>".$itemname[$items[$i]['inumber']]['money1']."</td>";
			echo "</tr>";
		}
		else{
		}
	}
	echo '</table>
	</div>';
?>