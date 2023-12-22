<h1><center>printlisttag</center></h1>
<div style='margin-bottom:15px;'>
	<input type='button' class='initbutton' id='save' value='儲存'>
</div>
<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
	<?php
	if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/printlisttag.ini')){
	}
	else{
		include_once '../../../tool/create.inifile.php';
		creprintlisttag($_POST['company'],$_POST['dep']);
	}
	$printlisttag=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/printlisttag.ini',true);
	$printlisttag_con=parse_ini_file('./inifile/printlisttag_con.ini',true);
	$sectionstring='';
	$blockindex=0;
	$itemindex=0;
	$punchlistindex=0;
	$kitchenindex=0;
	$clientlistindex=0;
	$changetableindex=0;
	$otherindex=0;
	foreach($printlisttag as $section=>$data){
		//echo '<form class="'.$section.'" style="overflow: hidden;border-bottom: 1px solid #898989;">';
		if($sectionstring!=''){
			$sectionstring .= '-';
		}
		else{
		}
		$sectionstring .= $section;
		foreach($data as $name=>$value){
			if($_POST['ini']==0){
				if($section=='item'){
					if($itemindex==0){
						echo '<form class="'.$section.'" style="overflow: hidden;border-bottom: 1px solid #898989;">';
						echo '<span class="" style="font-size:20px;padding: 0  10%;font-weight: 600;border-bottom: 1px solid #898989;">'.$printlisttag_con[$section]['titletext'].'</span>';
					}
					if($name=='clientlist1'||$name=='clientlist2'||$name=='clientlist3'||$name=='clientlist4'||$name=='clientlist1temp'||$name=='clientlist2temp'||$name=='clientlist3temp'||$name=='clientlist4temp'||$name=='printchange'||$name=='clientsize'||$name=='kitchen'||$name=='kitchensize'||$name=='chatabletitlefont'||$name=='chatablecontentfont'||$name=='printclientlist'||$name=='grouptitlesize'||$name=='grouptitlesize'||$name=='taghint'||$name=='tagtemplate'){
						echo '<div style="overflow:hidden;">
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$printlisttag_con[$section][$name].'</span></div>
							</div>';
						
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
					$itemindex++;
					
				}
				else if($section=='block'){
					if($blockindex==0){
						echo '<form class="'.$section.'" style="overflow: hidden;border-bottom: 1px solid #898989;">';
						echo '<span class="" style="font-size:20px;padding: 0  10%;font-weight: 600;border-bottom: 1px solid #898989;">'.$printlisttag_con[$section]['titletext'].'</span>';
					}

					if($name=='saledetail'||$name=='allvoidlist'||$name=='otherpay'||$name=='inv'||$name=='rearlist'||$name=='salelist'||$name=='inoutmoney'||$name=='memmoney'){
						echo '<div style="overflow:hidden;"> 
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$printlisttag_con[$section][$name].'</span></div>
							</div>';
						
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
					$blockindex++;
				}
				else if($section=='kitchen'){
					if($kitchenindex==0){
						echo '<form class="'.$section.'" style="overflow: hidden;border-bottom: 1px solid #898989;">';
						echo '<span class="" style="font-size:20px;padding: 0  10%;font-weight: 600;border-bottom: 1px solid #898989;">'.$printlisttag_con[$section]['titletext'].'</span>';
					}
					if($name=='storyfontsize'||$name=='typefontsize'||$name=='timefontsize'||$name=='nummanfontsize'||$name=='grouptitlesize'||$name=='kitchensize'||$name=='tastesize'){
						echo '<div style="overflow:hidden;">
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$printlisttag_con[$section][$name].'</span></div>
							</div>';
						
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
					$kitchenindex++;
				}
				else if($section=='clientlist'){
					if($clientlistindex==0){
						echo '<form class="'.$section.'" style="overflow: hidden;border-bottom: 1px solid #898989;">';
						echo '<span class="" style="font-size:20px;padding: 0  10%;font-weight: 600;border-bottom: 1px solid #898989;">'.$printlisttag_con[$section]['titletext'].'</span>';
					}
					if($name=='qtysize'||$name=='itemendline'){
						echo '<div style="overflow:hidden;">
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$printlisttag_con[$section][$name].'</span></div>
							</div>';
						
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
					$clientlistindex++;
				}
				else if($section=='punchlist'){
					if($punchlistindex==0){
						echo '<form class="'.$section.'" style="overflow: hidden;border-bottom: 1px solid #898989;display:none;">';
						echo '<span class="" style="font-size:20px;padding: 0  10%;font-weight: 600;border-bottom: 1px solid #898989;">'.$printlisttag_con[$section]['titletext'].'</span>';
					}
					if(0){
						echo '<div style="overflow:hidden;">
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$printlisttag_con[$section][$name].'</span></div>
							</div>';
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
					$punchlistindex++;
				}
				else if($section=='changetable'){
					if($changetableindex==0){
						echo '<form class="'.$section.'" style="overflow: hidden;border-bottom: 1px solid #898989;display:none;">';
						echo '<span class="" style="font-size:20px;padding: 0  10%;font-weight: 600;border-bottom: 1px solid #898989;">'.$printlisttag_con[$section]['titletext'].'</span>';
					}
					if(0){
						echo '<div style="overflow:hidden;">
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$printlisttag_con[$section][$name].'</span></div>
							</div>';
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
					$changetableindex++;
				}
				else{
					if($otherindex==0){
						echo '<form class="'.$section.'" style="overflow: hidden;border-bottom: 1px solid #898989;display:none;">';
						echo '<span class="" style="font-size:20px;padding: 0  10%;font-weight: 600;border-bottom: 1px solid #898989;">'.$printlisttag_con[$section]['titletext'].'</span>';
					}
					echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					$otherindex++;
				}
			}
			else{
				echo '<div style="overflow:hidden;">
						<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
						<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$printlisttag_con[$section][$name].'</span></div>
					</div>';
			}
		}
		echo '</form>';
	}
	echo '<input type="hidden" class="sectionstring" value="'.$sectionstring.'">';
	?>
</div>