<?php
$otherpay=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini',true);
?>
<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
<table id='otherpayTable'>
	<thead>
		<tr>
			<th style='background-color: rgb(255, 255, 255); position: relative; top: 0px;'></th>
			<th style='background-color: rgb(255, 255, 255); position: relative; top: 0px;'>付款方式</th>
			<th style='background-color: rgb(255, 255, 255); position: relative; top: 0px;'>別稱</th>
		</tr>
	</thead>
	<tbody>
<?php
if(isset($otherpay['item1'])){
	for($i=1;$i<sizeof($otherpay);$i++){
		echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='item".$i."'></td><td>".$otherpay['item'.$i]['name']."</td><td>".$otherpay['item'.$i]['dbname']."</td></tr>";
	}
}
else{
}
?>
	</tbody>
</table>