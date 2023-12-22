<?php
if(isset($_POST['location'])){
	$machinedata=parse_ini_file($_POST['location'].'database/machinedata.ini',true);
	$floorspend=parse_ini_file($_POST['location'].'database/floorspend.ini',true);
}
else{
	$machinedata=parse_ini_file('../database/machinedata.ini',true);
	$floorspend=parse_ini_file('../database/floorspend.ini',true);
}
$maxtabnum=0;
if(isset($floorspend['TA']['page'])&&isset($floorspend['TA']['row1'])){
	for($i=1;$i<=$floorspend['TA']['page'];$i++){
		$maxtabnum+=$floorspend['TA']['row'.$i]*$floorspend['TA']['col'.$i];
	}
}
else if(!isset($floorspend['TA']['row1'])){
	$maxtabnum=$floorspend['TA']['row']*$floorspend['TA']['col'];
}
else{
}
$PostData=array(
	"machinetype"=>"m1"
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $machinedata['orderpos']['serverip'].'/demopos/comput.ajax.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	print_r('cURL error when connecting to ' . $machinedata['orderpos']['serverip'].'/demopos/comput.ajax.php : ' . curl_error($machinedata['orderpos']['serverip'].'/demopos/comput.ajax.php'));
}
curl_close($ch);
$tablist = json_decode($Result,1);
//print_r($tablist);
if(isset($_POST['location'])){
	$dir=($_POST['location'].'demopos/table');
}
else{
	$dir=('../demopos/table');
}
$filelist=scandir($dir);
echo '<option id="empty" value="" selected></option>';
for($i=1,$tab=1;$i<=$maxtabnum;$i++){
	if($tab<=$floorspend['TA']['number']){
		if(isset($floorspend['T'.$i])){
			if(isset($floorspend['T'.$i]['tablename'])&&$floorspend['T'.$i]['tablename']!=''){
				if(isset($tablist[$floorspend['T'.$i]['tablename']])){
					if($tablist[$floorspend['T'.$i]['tablename']]['splitnum']=='1'){
						echo '<option value="'.$tablist[$floorspend['T'.$i]['tablename']]['bizdate'].';-;'.$tablist[$floorspend['T'.$i]['tablename']]['consecnumber'].'" style="background-color:#ff0000;">★';
						if(isset($floorspend['Tname'][$tablist[$floorspend['T'.$i]['tablename']]['inittablenum']])){
							echo $floorspend['Tname'][$tablist[$floorspend['T'.$i]['tablename']]['inittablenum']];
						}
						else{
							echo $tablist[$floorspend['T'.$i]['tablename']]['inittablenum'];
						}
						echo '</option>';
					}
					else{
						foreach($filelist as $fl){
							if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',$fl)||preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',$fl)){
								if(isset($_POST['location'])){
									$tabledata=parse_ini_file($_POST['location'].'demopos/table/'.$fl,true);
								}
								else{
									$tabledata=parse_ini_file('../demopos/table/'.$fl,true);
								}
								foreach($tabledata as $tdname=>$td){
									if($td['consecnumber']!=''){
										echo '<option value="'.$td['bizdate'].';-;'.$td['consecnumber'].'" style="background-color:#ff0000;">★';
										if(preg_match('/-/',$tdname)){
											$inittable=preg_split('/-/',$tdname);
											if(isset($floorspend['Tname'][$inittable[0]])){
												echo $floorspend['Tname'][$inittable[0]].'-'.$inittable[1];
											}
											else{
												echo $tdname;
											}
										}
										else{
											if(isset($floorspend['Tname'][$tdname])){
												echo $floorspend['Tname'][$tdname];
											}
											else{
												echo $tdname;
											}
										}
										echo '</option>';
										break;
									}
									else{
									}
								}
							}
							else{
							}
						}
					}
				}
				else{
					echo '<option value="'.$floorspend['T'.$i]['tablename'].'">';
					if(isset($floorspend['Tname'][$floorspend['T'.$i]['tablename']])){
						echo $floorspend['Tname'][$floorspend['T'.$i]['tablename']];
					}
					else{
						echo $floorspend['T'.$i]['tablename'];
					}
					echo '</option>';
				}
				$tab++;
			}
			else{
			}
		}
		else{
			//break;
		}
	}
	else{
	}
}
if(isset($_POST['location'])){
	$dir=($_POST['location'].'demopos/table/outside');
}
else{
	$dir=('../demopos/table/outside');
}
$filelist=scandir($dir);
if(sizeof($filelist)>2){
	foreach($filelist as $fl){
		if($fl=='..'||$fl=='.'){
			continue;
		}
		else{
			$tabname=preg_split('/;/',$fl);
			if(sizeof($tabname)==3){
				echo '<option value="'.$fl.'">♞';
				if(isset($floorspend['Tname'][substr($tabname[2],0,-4)])){
					echo $floorspend['Tname'][substr($tabname[2],0,-4)];
				}
				else{
					echo substr($tabname[2],0,-4);
				}
				echo '</option>';
			}
			else{
			}
		}
	}
}
else{
}
?>