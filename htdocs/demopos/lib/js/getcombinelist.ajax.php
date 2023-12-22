<?php
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
if(file_exists('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$_POST['tablenumber'].'.ini')){
	$tabledata=parse_ini_file('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$_POST['tablenumber'].'.ini',true);
	if($tabledata[$_POST['tablenumber']]['consecnumber']==$_POST['consecnumber']){
		if(preg_match('/,/',$tabledata[$_POST['tablenumber']]['table'])){
			if(($_POST['tablenumber'].',')==substr($tabledata[$_POST['tablenumber']]['table'],0,(strlen($_POST['tablenumber'])+1))){
				$tabledata[$_POST['tablenumber']]['table']=substr($tabledata[$_POST['tablenumber']]['table'],(strlen($_POST['tablenumber'])+1));
			}
			else if((','.$_POST['tablenumber'])==substr($tabledata[$_POST['tablenumber']]['table'],-(strlen($_POST['tablenumber'])+1))){
				$tabledata[$_POST['tablenumber']]['table']=substr($tabledata[$_POST['tablenumber']]['table'],0,(strlen($tabledata[$_POST['tablenumber']]['table'])-strlen($_POST['tablenumber'])-1));
			}
			else{
				$tabledata[$_POST['tablenumber']]['table']=preg_replace('/,'.$_POST['tablenumber'].',/',',',$tabledata[$_POST['tablenumber']]['table']);
			}
			//echo $tabledata[$_POST['tablenumber']]['table'];
			$sptablelist=preg_split('/,/',$tabledata[$_POST['tablenumber']]['table']);
			for($i=0;$i<sizeof($sptablelist);$i++){
				if(!preg_match('/-/',$sptablelist[$i])){
				}
				else{
					$temp=preg_split('/-/',$sptablelist[$i]);
					$sptablelist[$i]=$temp[0];
				}
			}
			$tempspend='';
			$max=0;
			if(isset($floorspend['TA']['page'])&&$floorspend['TA']['page']>1){
				for($page=1;$page<=$floorspend['TA']['page'];$page++){
					if(isset($floorspend['TA']['row'.$page])&&isset($floorspend['TA']['col'.$page])){
						$max+=$floorspend['TA']['row'.$page]*$floorspend['TA']['col'.$page];
					}
					else{
						$max+=$floorspend['TA']['row']*$floorspend['TA']['col'];
					}
				}
			}
			else{
				$max=$floorspend['TA']['row']*$floorspend['TA']['col'];
			}
			for($i=1;$i<=$max;$i++){
				if($floorspend['T'.$i]['tablename']!=''&&in_array($floorspend['T'.$i]['tablename'],$sptablelist)){
					if($tempspend==''){
						$tempspend=$i;
					}
					else{
						$tempspend.=','.$i;
					}
				}
				else{
				}
			}
			echo $tempspend;
		}
		else{
		}
	}
	else{
	}
}
else{
}
?>