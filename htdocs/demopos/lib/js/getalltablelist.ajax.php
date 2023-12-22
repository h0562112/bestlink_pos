	連接內用帳單
	<select name='linklist' style='width:100%;-webkit-appearance:menulist;padding:5px;margin:5px 0;'>
	<?php
	include_once '../../../tool/dbTool.inc.php';
	$map=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($map['map'][$_POST['machinetype']])){
		if(file_exists('../../../database/time'.$map['map'][$_POST['machinetype']].'.ini')){
			$timeini=parse_ini_file('../../../database/time'.$map['map'][$_POST['machinetype']].'.ini',true);
		}
		else{
			$timeini=parse_ini_file('../../../database/timem1.ini',true);
		}
	}
	else{	
		$timeini=parse_ini_file('../../../database/timem1.ini',true);
	}
	$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
	if(file_exists('../../syspram/clientlist-'.$initsetting['init']['firlan'].'.ini')){
		$clientname=parse_ini_file('../../syspram/clientlist-'.$initsetting['init']['firlan'].'.ini',true);
	}
	else if(file_exists('../../syspram/clientlist-zh-TW.ini')){
		$clientname=parse_ini_file('../../syspram/clientlist-zh-TW.ini',true);
	}
	else{
		$clientname=parse_ini_file('../../syspram/clientlist-1.ini',true);
	}

	$conn=sqlconnect('../../../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT tempCST011.CONSECNUMBER,tempCST011.TABLENUMBER,tempCST011.REMARKS,salemap.saleno AS saleno FROM tempCST011 JOIN salemap ON salemap.bizdate="'.$timeini['time']['bizdate'].'"  AND salemap.consecnumber=tempCST011.CONSECNUMBER WHERE tempCST011.BIZDATE="'.$timeini['time']['bizdate'].'" AND SUBSTR(REMARKS,1,1)="1" ORDER BY tempCST011.CONSECNUMBER ASC';
	$res=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	//print_r($res);
	$result=0;
	for($i=0;$i<sizeof($res);$i++){
		if($res[$i]['TABLENUMBER']!=''){
			echo '<option value="'.$res[$i]['CONSECNUMBER'].'"';
			if($res[$i]['CONSECNUMBER']==$_POST['linklist']){
				$result=1;
				echo ' selected';
			}
			else{
			}
			echo '>'.$res[$i]['TABLENUMBER'].$clientname['name']['table'].'('.$res[$i]['CONSECNUMBER'].')</option>';
		}
		else{
			echo '<option value="'.$res[$i]['CONSECNUMBER'].'"';
			if($res[$i]['CONSECNUMBER']==$_POST['linklist']){
				$result=1;
				echo ' selected';
			}
			else{
			}
			echo '>'.$res[$i]['saleno'].'('.$res[$i]['CONSECNUMBER'].')'.'</option>';
		}
	}
	if($result==1){
		echo '<option value="">不連接</option>';
	}
	else{
		echo '<option value="" selected>不連接</option>';
	}
	?>
	</select>
	<button id='sendlink' style='width: calc(100% / 2 - 2px);height:calc(50% - 1px);margin:1px 1px 0 1px;' value='暫結出單'>暫結出單</button>
	<input type='hidden' id='continue'>
	<button id='cancel' style='width: calc(100% / 2 - 2px);height:calc(50% - 1px);margin:1px 1px 0 1px;' value='取消'>取消</button>