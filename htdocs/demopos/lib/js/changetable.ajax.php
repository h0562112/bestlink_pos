<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/PHPWord.php';
include_once '../../../tool/inilib.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
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

if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$conn=sqlconnect('../../../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT COUNT(*) AS num FROM tempCST011 WHERE BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER="'.$_POST['c1consecnumber'].'" AND (TABLENUMBER="'.$_POST['c1'].'" OR TABLENUMBER LIKE "%,'.$_POST['c1'].'" OR TABLENUMBER LIKE "'.$_POST['c1'].',%" OR TABLENUMBER LIKE "%,'.$_POST['c1'].',%")';
$list1=sqlquery($conn,$sql,'sqlite');
if(intval($list1[0]['num'])==0){
}
else{
	if(file_exists('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$_POST['c1']).'.ini')){
		rename('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$_POST['c1']).'.ini','../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$_POST['c2']).'.ini');
		$filedata=parse_ini_file('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$_POST['c2']).'.ini',true);
		$temp=array();
		$combine=0;
		foreach($filedata as $fd){
			foreach($fd as $fname=>$fv){
				$temp[$_POST['c2']][$fname]=$fv;
				if($fname=='table'){
					$splittabnum=preg_split('/,/',$temp[$_POST['c2']][$fname]);
					$temp[$_POST['c2']][$fname]=$_POST['c2'];
				}
				else{
				}
				if($fname=='tablestate'&&$fv=='1'){
					$temp[$_POST['c2']][$fname]="0";
					$combine=1;
				}
				else{
				}
			}
		}
		write_ini_file($temp,'../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$_POST['c2']).'.ini');
		//echo $combine;
		if($combine){
			foreach($splittabnum as $stn){
				echo '../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$stn.'.ini';
				if(file_exists('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$stn).'.ini')){
					unlink('../../table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$stn).'.ini');
				}
				else{
				}
			}
		}
		else{
		}
	}
	else{
	}
	$sql='UPDATE tempCST011 SET TABLENUMBER="'.$_POST['c2'].'" WHERE  BIZDATE="'.$timeini['time']['bizdate'].'" AND CONSECNUMBER="'.$_POST['c1consecnumber'].'"';
	sqlnoresponse($conn,$sql,'sqlite');
}
sqlclose($conn,'sqlite');
if(isset($print['changetable']['print'])&&$print['changetable']['print']=='0'){
}
else{
	$PHPWord = new PHPWord();
	$document = $PHPWord->loadTemplate('../../../template/changetable.docx');
	$document->setValue('date',date('m/d H:i'));

	$tablemap=parse_ini_file('../../../database/floorspend.ini',true);

	if(preg_match('/-/',$_POST['c1'])){//拆桌
		$inittable=preg_split('/-/',$_POST['c1']);
		if(isset($tablemap['Tname'][$inittable[0]])){
			$document->setValue('c1',$tablemap['Tname'][$inittable[0]].'-'.$inittable[1]);
			//$tablename = $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
		}
		else{
			$document->setValue('c1',$_POST['c1']);
			//$tablename = $_POST['tablenumber'];
		}
	}
	else{
		if(isset($tablemap['Tname'][$_POST['c1']])){
			$document->setValue('c1',$tablemap['Tname'][$_POST['c1']]);
			//$tablename = $tablemap['Tname'][$_POST['tablenumber']];
		}
		else{
			$document->setValue('c1',$_POST['c1']);
			//$tablename = $_POST['tablenumber'];
		}
	}
	//$document->setValue('c1',$_POST['c1']);
	if(preg_match('/-/',$_POST['c2'])){//拆桌
		$inittable=preg_split('/-/',$_POST['c2']);
		if(isset($tablemap['Tname'][$inittable[0]])){
			$document->setValue('c2',$tablemap['Tname'][$inittable[0]].'-'.$inittable[1]);
			//$tablename = $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
		}
		else{
			$document->setValue('c2',$_POST['c2']);
			//$tablename = $_POST['tablenumber'];
		}
	}
	else{
		if(isset($tablemap['Tname'][$_POST['c2']])){
			$document->setValue('c2',$tablemap['Tname'][$_POST['c2']]);
			//$tablename = $tablemap['Tname'][$_POST['tablenumber']];
		}
		else{
			$document->setValue('c2',$_POST['c2']);
			//$tablename = $_POST['tablenumber'];
		}
	}
	//$document->setValue('c2',$_POST['c2']);
	/*if(file_exists('../../table/'.$_POST['c1'])){
		rename('../../table/'.$_POST['c1'],'../../table/'.$_POST['c2'].'.ini');
	}
	else{
	}
	$tabledoc=fopen('../../table/'.$_POST['c2'],'ini','w');*/
	//$document->save("../../../print/noread/".date('YmdHis')."_changetable_".$_POST['c1']."_".$_POST['c2'].".docx");
	$document->save("../../../print/read/".date('YmdHis')."_changetable_".iconv('utf-8','big5',$_POST['c1'])."_".iconv('utf-8','big5',$_POST['c2']).".docx");
	$prt=fopen("../../../print/noread/".date('YmdHis')."_changetable_".iconv('utf-8','big5',$_POST['c1'])."_".iconv('utf-8','big5',$_POST['c2']).".prt",'w');
	fclose($prt);
}
?>