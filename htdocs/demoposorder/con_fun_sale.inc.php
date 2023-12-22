<?php
function con_sale($ID,$company,$DB,$usergroup,$startdate,$enddate){
	echo "<h1>折扣設定</h1>";
	$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
	if($DB==''){
		$filename='./data/'.$company;
		if(file_exists($filename.'-sale.ini')){
			$content=parse_ini_file($filename.'-sale.ini',true);
		}
		else{
		}
		$sql='SELECT * FROM salemap WHERE company="'.$company.'"';
	}
	else{
		$filename='./data/'.$DB;
		if(file_exists($filename.'-sale.ini')){
			$content=parse_ini_file($filename.'-sale.ini',true);
		}
		else{
		}
		$sql='SELECT * FROM salemap WHERE company="'.$company.'" AND depnumber="'.$DB.'"';
	}
	$tastes=sqlquery($conn,$sql,'mysql');
	echo "<script>
			$(document).ready(function(){
			});
		</script>";
	echo "<form method='post' action='./tool/createsale.php' class='createsaleform'>
			<input type='hidden' name='company' value='".$company."'>
			<table>
				<tr>
					<td><input type='text' name='name' placeholder='折扣名稱'></td>
					<td><input type='number' name='money' placeholder='公式'></td>
					<td><input type='button' id='createtaste' value='新增選項'></td>
				</tr>
			</table>
		</form>";
	if(sizeof($tastes)==0){
		echo '目前尚未設定折扣選項';
	}
	else{
		echo "<table>
				<tr>
					<td>口味(加料)</td>
					<td>加價</td>
				</tr>";
		$temptype=0;
		foreach($tastes as $taste){
			if($temptype==$taste['type']){
			}
			else{
				$temptype=$taste['type'];
				if($taste['type']==1){
					echo "<tr>
							<td>口味選項</td>
						</tr>";
				}
				else{
					echo "<tr>
							<td>加料選項</td>
						</tr>";
				}
				echo "<tr>
						<td>".$content[$taste['type'].'.'.$taste['taste']]['name']."</td>
						<td>".$taste['money']."</td>
					</tr>";
			}
		}
		echo "</table>";
	}
	sqlclose($conn,'mysql');
}
?>