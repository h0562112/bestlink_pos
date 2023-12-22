<?php
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount1.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount1.ini','w');
	fclose($f);
}
$dis1=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount1.ini',true);
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount2.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount2.ini','w');
	fclose($f);
}
$dis2=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount2.ini',true);
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount3.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount3.ini','w');
	fclose($f);
}
$dis3=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount3.ini',true);
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount4.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount4.ini','w');
	fclose($f);
}
$dis4=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount4.ini',true);
?>
<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
<table id='autodisTable' style='border-collapse: separate; border-spacing: 0;margin:5px;'>
	<thead>
		<tr>
			<th></th>
			<th>優惠名稱</th>
			<th>買N</th>
			<th>送M</th>
			<th>優惠類別</th>
			<th>優惠次數</th>
		</tr>
	</thead>
	<tbody>
<?php
if(isset($dis1['1'])){
	echo '<tr style="background-color: transparent;"><th colspan="6" style="border: 1px solid #898989; border-radius: 5px;">內用(discount1)</th></tr>';
	for($i=1;$i<=sizeof($dis1);$i++){
		echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='discount1-".$i."'></td><td>".$dis1[$i]['name']."</td><td style='text-align:right;'>".$dis1[$i]['buy']."</td><td style='text-align:right;'>".$dis1[$i]['free']."</td><td style='text-align:right;'>";
		if(!isset($dis1[$i]['distype'])||$dis1[$i]['distype']=='1'){
			echo '折讓';
		}
		else if($dis1[$i]['distype']=='2'){
			echo '折扣';
		}
		else{//無優惠上限
			echo '單一價';
		}
		echo "</td><td style='text-align:right;'>";
		if($dis1[$i]['listtype']=='0'){//關閉優惠
			echo '<span style="color:#ff0000;">停用優惠</span>';
		}
		else if($dis1[$i]['listtype']=='-1'){
			echo '無上限';
		}
		else if($dis1[$i]['listtype']>0){
			echo $dis1[$i]['listtype'].'次';
		}
		else{
			echo 'parameter error';
		}
		echo "</td></tr>";
	}
}
else{
}
if(isset($dis2['1'])){
	echo '<tr style="background-color: transparent;"><th colspan="6" style="border: 1px solid #898989; border-radius: 5px;">外帶(discount2)</th></tr>';
	for($i=1;$i<=sizeof($dis2);$i++){
		echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='discount2-".$i."'></td><td>".$dis2[$i]['name']."</td><td style='text-align:right;'>".$dis2[$i]['buy']."</td><td style='text-align:right;'>".$dis2[$i]['free']."</td><td style='text-align:right;'>";
		if(!isset($dis2[$i]['distype'])||$dis2[$i]['distype']=='1'){
			echo '折讓';
		}
		else if($dis2[$i]['distype']=='2'){
			echo '折扣';
		}
		else{//無優惠上限
			echo '單一價';
		}
		echo "</td><td style='text-align:right;'>";
		if($dis2[$i]['listtype']=='0'){//關閉優惠
			echo '<span style="color:#ff0000;">停用優惠</span>';
		}
		else if($dis2[$i]['listtype']=='-1'){
			echo '無上限';
		}
		else if($dis2[$i]['listtype']>0){
			echo $dis2[$i]['listtype'].'次';
		}
		else{
			echo 'parameter error';
		}
		echo "</td></tr>";
	}
}
else{
}
if(isset($dis3['1'])){
	echo '<tr style="background-color: transparent;"><th colspan="6" style="border: 1px solid #898989; border-radius: 5px;">外送(discount3)</th></tr>';
	for($i=1;$i<=sizeof($dis3);$i++){
		echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='discount3-".$i."'></td><td>".$dis3[$i]['name']."</td><td style='text-align:right;'>".$dis3[$i]['buy']."</td><td style='text-align:right;'>".$dis3[$i]['free']."</td><td style='text-align:right;'>";
		if(!isset($dis3[$i]['distype'])||$dis3[$i]['distype']=='1'){
			echo '折讓';
		}
		else if($dis3[$i]['distype']=='2'){
			echo '折扣';
		}
		else{//無優惠上限
			echo '單一價';
		}
		echo "</td><td style='text-align:right;'>";
		if($dis3[$i]['listtype']=='0'){//關閉優惠
			echo '<span style="color:#ff0000;">停用優惠</span>';
		}
		else if($dis3[$i]['listtype']=='-1'){
			echo '無上限';
		}
		else if($dis3[$i]['listtype']>0){
			echo $dis3[$i]['listtype'].'次';
		}
		else{
			echo 'parameter error';
		}
		echo "</td></tr>";
	}
}
else{
}
if(isset($dis4['1'])){
	echo '<tr style="background-color: transparent;"><th colspan="6" style="border: 1px solid #898989; border-radius: 5px;">自取(discount4)</th></tr>';
	for($i=1;$i<=sizeof($dis4);$i++){
		echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='discount4-".$i."'></td><td>".$dis4[$i]['name']."</td><td style='text-align:right;'>".$dis4[$i]['buy']."</td><td style='text-align:right;'>".$dis4[$i]['free']."</td><td style='text-align:right;'>";
		if(!isset($dis4[$i]['distype'])||$dis4[$i]['distype']=='1'){
			echo '折讓';
		}
		else if($dis4[$i]['distype']=='2'){
			echo '折扣';
		}
		else{//無優惠上限
			echo '單一價';
		}
		echo "</td><td style='text-align:right;'>";
		if($dis4[$i]['listtype']=='0'){//關閉優惠
			echo '<span style="color:#ff0000;">停用優惠</span>';
		}
		else if($dis4[$i]['listtype']=='-1'){
			echo '無上限';
		}
		else if($dis4[$i]['listtype']>0){
			echo $dis4[$i]['listtype'].'次';
		}
		else{
			echo 'parameter error';
		}
		echo "</td></tr>";
	}
}
else{
}
?>
	</tbody>
</table>