<?php
session_start();
date_default_timezone_set('Asia/Taipei');
include_once '../../../tool/dbTool.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if($_SESSION['DB']==''){
	if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini')){
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini');
		$machinemap=array_unique($temp);
	}
	else{
		$machinemap[]='m1';
	}
}
else{
	if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/mapping.ini')){
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/mapping.ini');
		$machinemap=array_unique($temp);
	}
	else{
		$machinemap[]='m1';
	}
}
if(isset($_POST['startdate'])){
	$list=array();
	if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
		$ENDDATE=strtotime(date('Ymd'));
	}
	else{
		$ENDDATE=strtotime(date('Ymd',strtotime($end)));
	}
	if(is_dir('../../../doc/')){
	}
	else{
		mkdir('../../../doc');
	}
	$filepath='../doc/'.date('Ymd');
	if(is_dir('../../'.$filepath.'/')){
	}
	else{
		mkdir('../../'.$filepath);
	}
	$file=$filepath.'/'.date('YmdHis').'.csv';
	$f=fopen('../../'.$file,'w');
	fwrite($f,'格式代號,稅籍編號,流水號,資料所屬年月,買受人統編/發票迄號,銷售人統編,發票起號,銷售金額,課稅別,營業稅額,扣抵代號,空白,特種稅額稅率,彙加註記,通關方式註記'.PHP_EOL);
	$index=1;
	for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +2 month')){
		$dbarray=array();
		foreach($machinemap as $machine){
			if($_SESSION['DB']==''){
				$setup=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/setup.ini',true);
				if(intval(date('m',$d))%2==0){
					if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.date('Ym',$d).'/invdata_'.date('Ym',$d).'_'.$machine.'.db')){
						if(!isset($conn)||!$conn){
							$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.date('Ym',$d),'invdata_'.date('Ym',$d).'_'.$machine.'.db','','','','sqlite');
						}
						else{
							$r=$conn->exec("ATTACH 'd://xampp/htdocs/outposandorder/ourpos/".$_SESSION['company']."/".$_POST['dbname']."/".date('Ym',$d)."/invdata_".date('Ym',$d)."_".$machine.".db' AS ".$machine);
							$dbarray[$machine]='1';
						}
					}
					else{
						if(!isset($conn)||!$conn){
							$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.date('Ym',$d).'.db','','','','sqlite');
						}
						else{
						}
					}
				}
				else{
					if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.date('Ym',strtotime(date('Ymd',$d).' +1 month')).'/invdata_'.date('Ym',strtotime(date('Ymd',$d).' +1 month')).'_'.$machine.'.db')){
						if(!isset($conn)||!$conn){
							$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.date('Ym',strtotime(date('Ymd',$d).' +1 month')),'invdata_'.date('Ym',strtotime(date('Ymd',$d).' +1 month')).'_'.$machine.'.db','','','','sqlite');
						}
						else{
							$r=$conn->exec("ATTACH 'd://xampp/htdocs/outposandorder/ourpos/".$_SESSION['company']."/".$_POST['dbname']."/".date('Ym',strtotime(date('Ymd',$d).' +1 month'))."/invdata_".date('Ym',strtotime(date('Ymd',$d).' +1 month'))."_".$machine.".db' AS ".$machine);
							$dbarray[$machine]='1';
						}
					}
					else{
						if(!isset($conn)||!$conn){
							$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'invdata_'.date('Ym',strtotime(date('Ymd',$d).' +1 month')).'.db','','','','sqlite');
						}
						else{
						}
					}
				}
			}
			else{
				$setup=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/setup.ini',true);
				if(intval(date('m',$d))%2==0){
					if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.date('Ym',$d).'/invdata_'.date('Ym',$d).'_'.$machine.'.db')){
						if(!isset($conn)||!$conn){
							$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.date('Ym',$d),'invdata_'.date('Ym',$d).'_'.$machine.'.db','','','','sqlite');
						}
						else{
							$r=$conn->exec("ATTACH 'd://xampp/htdocs/outposandorder/ourpos/".$_SESSION['company']."/".$_SESSION['DB']."/".date('Ym',$d)."/invdata_".date('Ym',$d)."_".$machine.".db' AS ".$machine);
							$dbarray[$machine]='1';
						}
					}
					else{
						if(!isset($conn)||!$conn){
							$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.date('Ym',$d).'.db','','','','sqlite');
						}
						else{
						}
					}
				}
				else{
					if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.date('Ym',strtotime(date('Ymd',$d).' +1 month')).'/invdata_'.date('Ym',strtotime(date('Ymd',$d).' +1 month')).'_'.$machine.'.db')){
						if(!isset($conn)||!$conn){
							$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.date('Ym',strtotime(date('Ymd',$d).' +1 month')),'invdata_'.date('Ym',strtotime(date('Ymd',$d).' +1 month')).'_'.$machine.'.db','','','','sqlite');
						}
						else{
							$r=$conn->exec("ATTACH 'd://xampp/htdocs/outposandorder/ourpos/".$_SESSION['company']."/".$_SESSION['DB']."/".date('Ym',strtotime(date('Ymd',$d).' +1 month'))."/invdata_".date('Ym',strtotime(date('Ymd',$d).' +1 month'))."_".$machine.".db' AS ".$machine);
							$dbarray[$machine]='1';
						}
					}
					else{
						if(!isset($conn)||!$conn){
							$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'invdata_'.date('Ym',strtotime(date('Ymd',$d).' +1 month')).'.db','','','','sqlite');
						}
						else{
						}
					}
				}
			}
		}
		
		if(!$conn){
			//echo '資料庫尚未上傳資料。';
		}
		else{
			if(sizeof($dbarray)>0){
				$sql='SELECT * FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'"';
				foreach($dbarray as $machine=>$value){
					if($value=='1'){
						$sql .= 'UNION SELECT * FROM '.$machine.'.invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'"';
					}
					else{
					}
				}
				$sql='SELECT createdate AS BIZDATE,createtime AS CRETIME,invnumber AS INVOICENUMBER,buyerid,relatenumber AS CONSECNUMBER,totalamount AS SALESTTLAMT,state FROM ('.$sql.') ORDER BY invnumber ASC,createdate ASC';
			}
			else{
				$sql='SELECT createdate AS BIZDATE,createtime AS CRETIME,invnumber AS INVOICENUMBER,buyerid,relatenumber AS CONSECNUMBER,totalamount AS SALESTTLAMT,state FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'" ORDER BY invnumber ASC,createdate ASC';
			}
			$first=sqlquery($conn,$sql,'sqlite');
			if(sizeof($first)==0){
			}
			else{
				foreach($first as $item){
					fwrite($f,'35');
					if(isset($setup['basic']['storynumber'])){
						fwrite($f,','.$setup['basic']['storynumber']);
					}
					else{
						fwrite($f,',');
					}
					fwrite($f,','.$index.','.(intval(substr($item['BIZDATE'],0,4))-1911).substr($item['BIZDATE'],4,2));
					if($item['buyerid']=='0000000000'){
						fwrite($f,',');
					}
					else{
						fwrite($f,','.$item['buyerid']);
					}
					fwrite($f,','.$setup['basic']['Identifier'].','.$item['INVOICENUMBER']);
					if($item['state']=='99'){
						fwrite($f,',0,F');
					}
					else{
						if($item['buyerid']=='0000000000'){
							fwrite($f,','.$item['SALESTTLAMT'].',1');
						}
						else{
							fwrite($f,','.intval(round($item['SALESTTLAMT']/1.05)).',1');
						}
					}
					if($item['buyerid']=='0000000000'){
						fwrite($f,',0');
					}
					else{
						fwrite($f,','.(intval($item['SALESTTLAMT'])-intval(round($item['SALESTTLAMT']/1.05))));
					}
					fwrite($f,',,,,,');
					fwrite($f,PHP_EOL);
					$index++;
				}
			}
		}
		sqlclose($conn,'sqlite');
	}
	fclose($f);
	echo $file;
}
else{
}
?>