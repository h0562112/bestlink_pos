<h1><center>initsetting</center></h1>
<div style='margin-bottom:15px;'>
	<input type='button' class='initbutton' id='save' value='儲存'>
</div>
<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
	<?php
	if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini')){
	}
	else{
		include_once '../../../tool/create.inifile.php';
		creinitsetting($_POST['company'],$_POST['dep']);
	}
	$initsetting=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini',true);
	$initsetting_con=parse_ini_file('./inifile/initsetting_con.ini',true);
	$sectionstring='';
	foreach($initsetting as $section=>$data){
		echo '<form class="'.$section.'" style="overflow: hidden;">';
		if($sectionstring!=''){
			$sectionstring .= '-';
		}
		else{
		}
		$sectionstring .= $section;
		foreach($data as $name=>$value){
			if($_POST['ini']==0){
				if($section=='init'){
					if($name=='orderlocation'||$name=='ordertype'||$name=='menutype'||$name=='menutyperow'||$name=='menutypecol'||$name=='menurow'||$name=='menucol'||$name=='groupchildtyperow'||$name=='groupchildtypecol'||$name=='groupchildrow'||$name=='groupchilecol'||$name=='tasterow'||$name=='tastecol'||$name=='publicseq'||$name=='listprint'||$name=='tastegroup'||$name=='posdvr'||$name=='weborder'||$name=='webordersec'||$name=='intellapay'||$name=='easycard'||$name=='creditcardpay'||$name=='intellaother'||$name=='intellauser'||$name=='openchar'||$name=='chargenumber'||$name=='chargeeq'||$name=='disbut1'||$name=='disnum1'||$name=='disname11'||$name=='disbut2'||$name=='disnum2'||$name=='disname21'||$name=='disbut3'||$name=='disnum3'||$name=='disname31'||$name=='disbut4'||$name=='disnum4'||$name=='disname41'||$name=='disbut5'||$name=='disnum5'||$name=='disname51'||$name=='disbut6'||$name=='disnum6'||$name=='disname61'||$name=='controltable'||$name=='tabnum'||$name=='opentemp'||$name=='faceidmember'||$name=='secview'||$name=='intellaotherprint'||$name=='useinv'||$name=='useoinv'||$name=='posgetmembermoney'||$name=='posmembermoneylist'||$name=='openindex'){
						echo '<div style="overflow:hidden;">
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$initsetting_con[$section][$name].'</span></div>
							</div>';
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
				}
				else if($section=='mempoint'){
					if(0){
						echo '<div style="overflow:hidden;">
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$initsetting_con[$section][$name].'</span></div>
							</div>';
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
				}
				else if($section=='db'){
					if($name=='dbfile'){
						echo '<div style="overflow:hidden;">
								<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
								<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$initsetting_con[$section][$name].'</span></div>
							</div>';
					}
					else{
						echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
					}
				}
				else{
					echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
				}
			}
			else{
				echo '<div style="overflow:hidden;">
						<div style="float:left;margin: 0 10px;font-size: 18px;">'.$name.'=</div>
						<div style="float:left;"><input type="text" name="'.$name.'" value="'.$value.'" style="font-size: 18px; border: 0px; border-bottom: 1px solid #898989; padding: 0 10px 5px 10px;"><br><span style="color:#ff0000;font-size: 12px;">＊'.$initsetting_con[$section][$name].'</span></div>
					</div>';
			}
		}
		echo '</form>';
	}
	echo '<input type="hidden" class="sectionstring" value="'.$sectionstring.'">';
	?>
</div>