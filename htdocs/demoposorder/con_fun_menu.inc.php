<?php
function con_menu($ID,$company,$DB,$usergroup,$startdate,$enddate){
	echo "<h1>餐點設定</h1>";
	$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
	if($DB==''){
		$filename='./data/'.$company;
		if(file_exists($filename.'-menu.ini')){
			$content=parse_ini_file($filename.'-menu.ini',true);
		}
		else{
			$content=0;
		}
		$sql='SELECT * FROM itemsdata WHERE company="'.$company.'"';
	}
	else{
		$filename='./data/'.$DB;
		if(file_exists($filename.'-menu.ini')){
			$content=parse_ini_file($filename.'-menu.ini',true);
		}
		else{
			$content=0;
		}
		$sql='SELECT itemsdata.*,depitemmenu.depnumber FROM itemsdata JOIN depitemmenu ON depitemmenu.inumber=itemsdata.inumber WHERE depitemmenu.company="'.$company.'" AND itemsdata.inumber IN (SELECT inumber FROM depitemmenu WHERE depnumber="'.$DB.'")';
	}
	$items=sqlquery($conn,$sql,'mysql');
	$sql='SELECT * FROM frtypemap WHERE company="'.$company.'" ORDER BY typesq,fronttype';
	$frtype=sqlquery($conn,$sql,'mysql');
	$sql='SELECT * FROM retypemap WHERE company="'.$company.'" ORDER BY typesq,reartype';
	$retype=sqlquery($conn,$sql,'mysql');
	if(sizeof($frtype)+sizeof($retype)==0){
	}
	else{
		if(file_exists($filename.'-type.ini')){
			$typename=parse_ini_file($filename.'-type.ini',true);
		}
		else{
			$typename=0;
		}
	}
	echo "<script>
			$(document).ready(function(){
				rdialog=$('.radiodialog').dialog({
					autoOpen:false,
					height:768,
					width:650,
					resizable:false,
					modal:true,
					draggable:false,
					buttons:[
						{
							text:'確定',
							click:function(){
								if($('.radiodialog .type').val()=='front'){
									$('#newitemdata #choseftype').val($('.radiodialog input[name^=\"frtype\"]:checked').val());
									$('#newitemdata #fronttype').val($('.radiodialog input[name^=\"frtype\"]:checked').attr('id'));
								}
								else if($('.radiodialog .type').val()=='rear'){
									$('#newitemdata #chosertype').val($('.radiodialog input[name^=\"retype\"]:checked').val());
									$('#newitemdata #reartype').val($('.radiodialog input[name^=\"retype\"]:checked').attr('id'));
								}
								else{
									
								}
								rdialog.dialog('close');
							}
						},
						{
							text:'取消',
							click:function(){
								rdialog.dialog('close');
							}
						}
					]
				});
				cdialog=$('.checkdialog').dialog({
					autoOpen:false,
					height:768,
					width:650,
					resizable:false,
					modal:true,
					draggable:false,
					buttons:[
						{
							text:'確定',
							click:function(){
								if($('.checkdialog .type').val()=='taste'){
									$('#newitemdata #taste').val('');
									$('#newitemdata #chosetaste').val('');
									$.each($('.checkdialog input[name^=\"taste\"]'),function(index,element){
										if($(element).is(':checked')){
											if($('#newitemdata #chosetaste').val().length==0){
												$('#newitemdata #chosetaste').val($(element).val());
												$('#newitemdata #taste').val($(element).attr('id'));
											}
											else{
												$('#newitemdata #chosetaste').val($('#newitemdata #chosetaste').val()+','+$(element).val());
												$('#newitemdata #taste').val($('#newitemdata #taste').val()+','+$(element).attr('id'));
											}
										}
										else{
										}
									});
								}
								else if($('.checkdialog .type').val()=='sale'){
									$('#newitemdata #sale').val('');
									$('#newitemdata #chosesale').val('');
									$.each($('.checkdialog input[name^=\"sale\"]'),function(index,element){
										if($(element).is(':checked')){
											if($('#newitemdata #chosesale').val().length==0){
												$('#newitemdata #chosesale').val($(element).val());
												$('#newitemdata #sale').val($(element).attr('id'));
											}
											else{
												$('#newitemdata #chosesale').val($('#newitemdata #chosesale').val()+','+$(element).val());
												$('#newitemdata #sale').val($('#newitemdata #sale').val()+','+$(element).attr('id'));
											}
										}
										else{
										}
									});
								}
								
								cdialog.dialog('close');
							}
						},
						{
							text:'取消',
							click:function(){
								cdialog.dialog('close');
							}
						}
					]
				});
				$(document).on('click','.radiodialog input[name^=\"frtype\"]',function(){
					if($(this).attr('id')=='createtype'){
						$('.radiodialog input[name=\"newtype\"]').attr('disabled',false);
					}
					else{
						$('.radiodialog input[name=\"newtype\"]').val('');
						$('.radiodialog input[name=\"newtype\"]').attr('disabled',true);
					}
				});
				$(document).on('click','.radiodialog input[name^=\"retype\"]',function(){
					if($(this).attr('id')=='createtype'){
						$('.radiodialog input[name=\"newtype\"]').attr('disabled',false);
					}
					else{
						$('.radiodialog input[name=\"newtype\"]').val('');
						$('.radiodialog input[name=\"newtype\"]').attr('disabled',true);
					}
				});
				$(document).on('click','.checkdialog input[name=\"createtaste\"]',function(){
					if($(this).is(':checked')){
						$('.checkdialog input[name=\"newtaste\"]').attr('disabled',false);
						$('.checkdialog input[name=\"newmoney\"]').attr('disabled',false);
					}
					else{
						$('.checkdialog input[name=\"newtaste\"]').val('');
						$('.checkdialog input[name=\"newtaste\"]').attr('disabled',true);
						$('.checkdialog input[name=\"newmoney\"]').val('');
						$('.checkdialog input[name=\"newmoney\"]').attr('disabled',true);
					}
				});
				$('#newitemdata #taste').click(function(){
					$.ajax({
						url:'./tool/gettaste.php',
						method:'post',
						data:{company:'".$company."',depnumber:'".$DB."'},
						dataType:'json',
						success:function(data){
							var tempindex=0;
							var temptaste=$('#chosetaste').val().split(',');
							var temptype=0;
							cdialog.html('');
							$.each(data,function(index,value){
								if(value['type']==temptype){
								}
								else if(value['type']==1){
									temptype=1;
									cdialog.append('口味選項<br>');
								}
								else{
									temptype=2;
									cdialog.append('<hr>加料選項<br>');
								}
								if(temptaste[tempindex]==value['taste']){
									if(value['money']==0){
										cdialog.append('<label><input type=\"checkbox\" name=\"taste[]\" id=\"'+value['name']+'\" value=\"'+value['taste']+'\" checked>'+value['name']+'</label>');
									}
									else{
										cdialog.append('<label><input type=\"checkbox\" name=\"taste[]\" id=\"'+value['name']+'(加'+value['money']+'元)\" value=\"'+value['taste']+'\" checked>'+value['name']+'(加'+value['money']+'元)</label>');
									}
									tempindex++;
								}
								else{
									if(value['money']==0){
										cdialog.append('<label><input type=\"checkbox\" name=\"taste[]\" id=\"'+value['name']+'\" value=\"'+value['taste']+'\">'+value['name']+'</label>');
									}
									else{
										cdialog.append('<label><input type=\"checkbox\" name=\"taste[]\" id=\"'+value['name']+'(加'+value['money']+'元)\" value=\"'+value['taste']+'\">'+value['name']+'(加'+value['money']+'元)</label>');
									}
								}
							});
							cdialog.append('<input type=\"hidden\" class=\"type\" value=\"taste\">');
							cdialog.dialog('open');
							cdialog.dialog('option','title','口味與加料選項');
						},
						error:function(data){
							console.log(data.responseText);
						}
					});
				});
				$('#newitemdata #sale').click(function(){
				$.ajax({
						url:'./tool/getsale.php',
						method:'post',
						data:{company:'".$company."'},
						dataType:'json',
						success:function(data){
							var tempindex=0;
							var tempsale=$('#chosesale').val().split(',');
							cdialog.html('');
							$.each(data,function(index,value){
								if(tempsale[tempindex]==value['sale']){
									cdialog.append('<label><input type=\"checkbox\" name=\"sale[]\" id=\"'+value['name']+'\" value=\"'+value['sale']+'\" checked>'+value['name']+'</label>');
									tempindex++;
								}
								else{
									cdialog.append('<label><input type=\"checkbox\" name=\"sale[]\" id=\"'+value['name']+'\" value=\"'+value['sale']+'\">'+value['name']+'</label>');
								}
						});
							cdialog.append('<input type=\"hidden\" class=\"type\" value=\"sale\">');
							cdialog.dialog('open');
							cdialog.dialog('option','title','折扣選項');
						},
					error:function(data){
							console.log(data);
						}
				});
				});
				$('#subutton').click(function(){
					if($.trim($('#newitemdata input[name=\"name\"]').val()).length>0 && $('#newitemdata input[name=\"fronttype\"]:selected').val()!=''){
					$('#newitemdata').submit();
					}
					else{
						if($('.mark').length>0){
					}
						else{
							$('#newitemdata input[name=\"name\"]').after('<font class=\"mark\" color=\"#ff0000\"><strong>*</strong></font>');
							$('#newitemdata input[name=\"fronttype\"]').after('<font color=\"#ff0000\"><strong>*</strong></font>');
						}
					}
				});
				$('#img').change(function(){
					if(this.files && this.files[0]){
						var reader=new FileReader();
						reader.onload=function(e){
							$('#preview').attr('src',e.target.result);
						}
						reader.readAsDataURL(this.files[0]);
					}
					else{
					}
				});
			});
		</script>";
	echo "<div class='checkdialog' title='checkbox message'></div>
		<form method='post' action='./tool/createitemdata.php' id='newitemdata' enctype='multipart/form-data'>
			<table>
				<tr>
					<td><input type='file' id='img' name='imgfile'></td>
					<td><input type='text' name='name' placeholder='名稱'></td>";
				echo "<td>";
				if(sizeof($frtype)==0){
					echo "<select name='fronttype' disabled>
							<option>無點餐類別選項</option>
						</select>";
				}
				else{
					echo "<select name='fronttype'>
							<option value=''>點餐類別選項</option>";
					if($typename==0){
						foreach($frtype as $frvalue){
							echo "<option value='".$frvalue['fronttype']."'>".$frvalue['fronttype']."</option>";
						}
					}
					else{
						foreach($frtype as $frvalue){
							echo "<option value='".$frvalue['fronttype']."'>".$typename['front'.$frvalue['fronttype']]['name']."</option>";
						}
					}
					echo "</select>";
				}
				/*if(sizeof($retype)==0){//統計類別暫時移除
					echo "<select name='reartype' disabled>
							<option>無統計類別選項</option>
						</select>";
				}
				else{
					echo "<select name='reartype'>
							<option value=''>統計類別選項</option>";
					if($typename==0){
						foreach($retype as $revalue){
							echo "<option value='".$revalue['reartype']."'>".$revalue['reartype']."</option>";
						}
					}
					else{
						foreach($retype as $revalue){
							echo "<option value='".$revalue['reartype']."'>".$typename['rear'.$revalue['reartype']]['name']."</option>";
						}
					}
					
					echo "</select>";
				}*/
				echo "<input type='hidden' name='reartype' name=' '>";
				echo "</td>";
				echo "<td rowspan='13'><input type='hidden' name='company' value='".$company."'><input type='button' id='subutton' value='新增餐點'></td>
				</tr>
				<tr>
					<td rowspan='13'><img id='preview'></td>";
				echo "<td rowspan='9'>價格</td>";
				echo "<td><input type='text' name='mname1' placeholder='價格名稱1(可空)'><input type='number' name='money1' placeholder='金額1'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input type='text' name='mname2' placeholder='價格名稱2(可空)'><input type='number' name='money2' placeholder='金額2'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input type='text' name='mname3' placeholder='價格名稱3(可空)'><input type='number' name='money3' placeholder='金額3'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input type='text' name='mname4' placeholder='價格名稱4(可空)'><input type='number' name='money4' placeholder='金額4'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input type='text' name='mname5' placeholder='價格名稱5(可空)'><input type='number' name='money5' placeholder='金額5'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input type='text' name='mname6' placeholder='價格名稱6(可空)'><input type='number' name='money6' placeholder='金額6'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input type='text' name='mname7' placeholder='價格名稱7(可空)'><input type='number' name='money7' placeholder='金額7'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input type='text' name='mname8' placeholder='價格名稱8(可空)'><input type='number' name='money8' placeholder='金額9'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input type='text' name='mname9' placeholder='價格名稱9(可空)'><input type='number' name='money9' placeholder='金額8'></td>";
			echo "</tr>";
			echo "<tr>
					<td colspan='2'><textarea id='taste' placeholder='點擊增加口味' style='width:100%;' readonly></textarea><input type='hidden' id='chosetaste' name='taste'></td>
				</tr>
				<!-- <tr>
					<td colspan='2'><textarea id='sale' placeholder='點擊增加折扣' style='width:100%;' readonly></textarea><input type='hidden' id='chosesale' name='sale'></td>
				</tr> -->
				<input type='hidden' name='sale' value=' '>
				<tr>
					<td colspan='2'><textarea name='introduction' placeholder='產品介紹' style='width:100%;'></textarea></td>
				</tr>
			</table>
		</form>";
	if(sizeof($items)==0){
		echo "目前尚未設定餐點";
	}
	else{
		echo "<table>";
		echo "<tr><td>餐點名稱</td><td>圖片</td><td>價格</td><td>點餐類別</td><!-- <td>統計類別</td> --><td>口味</td><!-- <td>折扣</td> --><td>產品介紹</td></tr>";
		foreach($items as $item){
			echo "<tr>";
			if($content==0){
				echo "<td><input type='hidden' value='IniIsNotExist'></td>";
				echo "<td><img src='".$item['imgfile']."?".date('YmdHis')."'></td>";
				echo "<td><input type='hidden' value='IniIsNotExist'> 元</td>";
			}
			else{
				if(isset($content[$item['inumber']]['name'])){
					echo "<td>".$content[$item['inumber']]['name']."</td>";
				}
				else{
					echo "<td><input type='hidden' value='ParameterIsNotExistInIni'></td>";
				}

				echo "<td><img src='".$item['imgfile']."?".date('YmdHis')."'></td>";
				
				echo "<td>";
				$modnumber=9;
				for($n=1;$n<=$modnumber;$n++){
					if(isset($content[$item['inumber']]['mname'.$n])){
						echo $content[$item['inumber']]['mname'.$n];
					}
					else{
						echo "<input type='hidden' value='ParameterIsNotExitInIni'>";
					}
					if(isset($content[$item['inumber']]['money'.$n])){
						if(strlen($content[$item['inumber']]['money'.$n])==0){
						}
						else{
							echo ' '.$content[$item['inumber']]['money'.$n].' 元';
							if($n!=$modnumber){
								echo "<br>";
							}
							else{
							}
						}
					}
					else{
						echo "<input type='hidden' value='ParameterIsNotExitInIni'>";
					}
				}
				/*if(isset($content[$item['inumber']]['money1'])){
					echo "<td>".$content[$item['inumber']]['money1']." 元</td>";
				}
				else{
					echo "<td><input type='hidden' value='ParameterIsNotExistInIni'> 元</td>";
				}*/
				echo "</td>";
			}
			
			if($typename==0){
				echo "<td>".$item['fronttype']."<input type='hidden' value='IniIsNotExist'></td>";
				echo "<td>".$item['reartype']."<input type='hidden' value='IniIsNotExist'></td>";
			}
			else{
				if(isset($typename['front'.$item['fronttype']]['name'])){
					echo "<td>".$typename['front'.$item['fronttype']]['name']."</td>";
				}
				else{
					echo "<td>".$item['fronttype']."<input type='hidden' value='ParameterIsNotExistInIni'></td>";
				}

				/*if(isset($typename['rear'.$item['reartype']]['name'])){//統計類別暫時移除
					echo "<td>".$typename['rear'.$item['reartype']]['name']."</td>";
				}
				else{
					echo "<td>".$item['reartype']."<input type='hidden' value='ParameterIsNotExistInIni'></td>";
				}*/
			}
			echo "<td>";
			if(strlen(trim($item['taste']))>0){
				if(isset($taste)){
				}
				else{
					if(file_exists($filename.'-taste.ini')){
						$taste=parse_ini_file($filename.'-taste.ini',true);
					}
					else{
						$taste=0;
					}
				}
				if($taste==0){
					echo "<td>".$item['taste']."<input type='hidden' value='IniIsNotExist'></td>";
				}
				else{
					$temptaste=preg_split('/,/',$item['taste']);
					for($t=0;$t<sizeof($temptaste);$t++){
						if($t!=0){
							echo ",";
						}
						else{
						}
						if(isset($taste[$temptaste[$t]]['name'])){
							echo $taste[$temptaste[$t]]['name'];
						}
						else{
							echo "<input type='hidden' value='ParameterIsNotExistInIni'>";
						}
						if(isset($taste[$temptaste[$t]]['money']) && intval($taste[$temptaste[$t]]['money'])>0){
							echo "(加".$taste[$temptaste[$t]]['money']."元)";
						}
						else{
							echo "<input type='hidden' value='ParameterIsNotExistInIni'>";
						}
					}
				}
			}
			else{
			}
			echo "</td>";
			/*echo "<td>".$item['sale']."</td>";//折扣暫時移除*/
			echo "<td>";
			if($content==0){
				echo "<input type='hidden' value='IniIsNotExist'>";
			}
			else{
				if(isset($content[$item['inumber']]['introduction'])){
					echo $content[$item['inumber']]['introduction'];
				}
				else{
					echo "<input type='hidden' value='ParameterIsNotExistInIni'>";
				}
			}
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	sqlclose($conn,'mysql');
}
?>