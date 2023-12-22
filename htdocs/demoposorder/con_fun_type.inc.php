<?php
function con_type($ID,$company,$DB,$usergroup,$startdate,$enddate){
	echo "<h1>類別設定</h1>";
	$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
	$filename='./data/'.$company;
	if(file_exists($filename.'-type.ini')){
		$content=parse_ini_file($filename.'-type.ini',true);
	}
	else{
		$content=0;
	}
	$sql1='SELECT * FROM frtypemap WHERE company="'.$company.'" ORDER BY typesq,fronttype';
	$sql2='SELECT * FROM retypemap WHERE company="'.$company.'" ORDER BY typesq,reartype';
	$frtype=sqlquery($conn,$sql1,'mysql');
	$retype=sqlquery($conn,$sql2,'mysql');
	echo "<script>
			$(document).ready(function(){
				$('#createtype').click(function(){
					if($('.createtypeform input[name=\"type\"]:checked').length>0 && $.trim($('.createtypeform input[name=\"name\"]').val()).length>0){
						$('.createtypeform').submit();
					}
					else{
					}
				});
			});
		</script>";
	echo "<form method='post' action='./tool/createtype.php' class='createtypeform'>
			<input type='hidden' name='company' value='".$company."'>
			<table>
				<tr>
					<td><label><input type='radio' name='type' value='front'>點餐類別</label><br><label><input type='radio' name='type' value='rear'>統計類別</label></td>
					<td><input type='text' name='name' placeholder='類別名稱'></td>
					<td><input type='button' id='createtype' value='新增類別選項'></td>
				</tr>
			</table>
		</form>";
	if(sizeof($frtype)+sizeof($retype)==0){
		echo '目前尚未設定類別選項';
	}
	else{
		echo "<table>";
		if(sizeof($frtype)==0){
		}
		else{
			echo "<tr>
					<td colspan='5'>點餐類別</td>
				</tr>";
			if($content==0){
				for($i=0;$i<sizeof($frtype);$i++){
					if($i%5==0){
						echo "<tr>";
					}
					else{
					}
					echo "<td>".$frtype[$i]['fronttype']."<input type='hidden' value='IniIsNotExist'></td>";
					if($i%5==4){
						echo "</tr>";
					}
					else{
					}
				}
				if($i%5==0){
				}
				else{
					do{
						echo "<td></td>";
						$i++;
					}while($i%5>0);
					echo "</tr>";
				}
			}
			else{
				for($i=0;$i<sizeof($frtype);$i++){
					if($i%5==0){
						echo "<tr>";
					}
					else{
					}
					if(isset($content['front'.$frtype[$i]['fronttype']]['name'])){
						echo "<td>".$content['front'.$frtype[$i]['fronttype']]['name']."</td>";
					}
					else{
						echo "<td>".$frtype[$i]['fronttype']."<input type='hidden' value='ParameterIsNotExistInIni'></td>";
					}
					if($i%5==4){
						echo "</tr>";
					}
					else{
					}
				}
				if($i%5==0){
				}
				else{
					do{
						echo "<td></td>";
						$i++;
					}while($i%5>0);
					echo "</tr>";
				}
			}
		}
		if(sizeof($retype)==0){
		}
		else{
			echo "<tr>
					<td colspan='5'>統計類別</td>
				</tr>";
			if($content==0){
				for($i=0;$i<sizeof($retype);$i++){
					if($i%5==0){
						echo "<tr>";
					}
					else{
					}
					echo "<td>".$retype[$i]['reartype']."<input type='hidden' value='IniIsNotExist'></td>";
					if($i%5==4){
						echo "</tr>";
					}
					else{
					}
				}
				if($i%5==0){
				}
				else{
					do{
						echo "<td></td>";
						$i++;
					}while($i%5>0);
					echo "</tr>";
				}
			}
			else{
				for($i=0;$i<sizeof($retype);$i++){
					if($i%5==0){
						echo "<tr>";
					}
					else{
					}
					if(isset($content['rear'.$retype[$i]['reartype']]['name'])){
						echo "<td>".$content['rear'.$retype[$i]['reartype']]['name']."</td>";
					}
					else{
						echo "<td>".$retype[$i]['reartype']."<input type='hidden' value='ParameterIsNotExistInIni'></td>";
					}
					if($i%5==4){
						echo "</tr>";
					}
					else{
					}
				}
				if($i%5==0){
				}
				else{
					do{
						echo "<td></td>";
						$i++;
					}while($i%5>0);
					echo "</tr>";
				}
			}
		}
		echo "</table>";
	}
	sqlclose($conn,'mysql');
}
?>