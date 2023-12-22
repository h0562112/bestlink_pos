<?php
function con_taste($ID,$company,$DB,$usergroup,$startdate,$enddate){
	echo "<h1>加料(口味)設定</h1>";
	$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
	if($DB==''){
		$filename='./data/'.$company;
		if(file_exists($filename.'-taste.ini')){
			$content=parse_ini_file($filename.'-taste.ini',true);
		}
		else{
			$content=0;
		}
		$sql='SELECT * FROM tastemap WHERE company="'.$company.'" ORDER BY type,taste';
	}
	else{
		$filename='./data/'.$DB;
		if(file_exists($filename.'-taste.ini')){
			$content=parse_ini_file($filename.'-taste.ini',true);
		}
		else{
			$content=0;
		}
		$sql='SELECT * FROM tastemap WHERE company="'.$company.'" AND depnumber="'.$DB.'" ORDER BY type,taste';
	}
	$tastes=sqlquery($conn,$sql,'mysql');
	echo "<script>
			$(document).ready(function(){
				$('#createtaste').click(function(){
					if($('input[name=\"tastetype\"]:checked').length>0 && $.trim($('input[name=\"name\"]').val()).length>0){
						$('.createtasteform').submit();
					}
					else{
						
					}
				});
			});
		</script>";
	echo "<form method='post' action='./tool/createtaste.php' class='createtasteform'>
			<input type='hidden' name='company' value='".$company."'>
			<table>
				<tr>
					<td><label><input type='radio' name='tastetype' value='1'>口味</label><br><label><input type='radio' name='tastetype' value='2'>加料</label></td>
					<td><input type='text' name='name' placeholder='口味(加料)名稱'></td>
					<td><input type='number' name='money' placeholder='加價'></td>
					<td><input type='button' id='createtaste' value='新增選項'></td>
				</tr>
			</table>
		</form>";
	if(sizeof($tastes)==0){
		echo '目前尚未設定加料(口味)選項';
	}
	else{
		echo "<table>";
		$temptype=0;
		$i=0;
		$j=0;
		$modnumber=5;
		for($i=0,$j=0;$i<sizeof($tastes);$i++,$j++){
			if($temptype==$tastes[$i]['type']){
			}
			else{
				if($j==0){
				}
				else{
					do{
						echo "<td></td>";
						$j++;
					}while($j%$modnumber>0);
					echo "</tr>";
				}				
				$temptype=$tastes[$i]['type'];
				if($tastes[$i]['type']==1){
					echo "<tr colspan='5'>
							<td>口味選項</td>
						</tr>";
				}
				else{
					echo "<tr colspan='5'>
							<td>加料選項</td>
						</tr>";
				}
				$j=0;
			}
			if($j%$modnumber==0){
				echo "<tr>";
			}
			else{
			}
			if($content==0){
				echo "<td>".$tastes[$i]['taste']."<input type='hidden' value='IniIsNotExist'><input type='hidden' value='IniIsNotExist'></td>";
			}
			else{
				echo "<td>";
				if(isset($content[$tastes[$i]['taste']]['name'])){
					echo $content[$tastes[$i]['taste']]['name'];
				}
				else{
					echo $tastes[$i]['taste']."<input type='hidden' value='ParameterIsNotExistInIni'>";
				}
				if(isset($content[$tastes[$i]['taste']]['money'])){
					echo ' '.$content[$tastes[$i]['taste']]['money']." 元";
				}
				else{
					echo " <input type='hidden' value='ParameterIsNotExistInIni'>";
				}
				echo "</td>";
			}
			if($j%$modnumber==($modnumber-1)){
				echo "</tr>";
			}
			else{
			}
		}
		if($j%$modnumber==0){
		}
		else{
			do{
				echo "<td></td>";
				$j++;
			}while($j%$modnumber>0);
		}
		echo "</table>";
	}
	sqlclose($conn,'mysql');
}
?>