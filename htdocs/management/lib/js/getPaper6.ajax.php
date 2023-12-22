<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
if(isset($_POST['lan'])&&$_POST['lan']!=''){
	if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
		$interface=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('../../lan/interface1.ini')){
		$interface=parse_ini_file('../../lan/interface1.ini',true);
	}
	else{
		$interface='-1';
	}
}
//$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['enddate'])){
	$list=array();//暫存帳單資料
	$templist=array();//暫存未結帳單資料
	$tooltip=array();//其他付款的明細(各班別與匯總)
	if(!isset($_SESSION['DB'])||$_SESSION['DB']==''){
		$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/temp','SALES_'.substr($end,0,6).'.db','','','','sqlite');
		if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/Cover.db')){
			$Cconn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'Cover.db','','','','sqlite');
		}
		else{
		}
		if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/initsetting.ini')){
			$init=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/initsetting.ini',true);
		}
		else{
		}
		if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/otherpay.ini')){
			$otherpay=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/otherpay.ini',true);
		}
		else{
		}
	}
	else{
		$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/temp','SALES_'.substr($end,0,6).'.db','','','','sqlite');
		if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/Cover.db')){
			$Cconn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'Cover.db','','','','sqlite');
		}
		else{
		}
		if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini')){
			$init=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
		}
		else{
		}
		if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/otherpay.ini')){
			$otherpay=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/otherpay.ini',true);
		}
		else{
		}
	}
	if(!$conn){
		if($interface!='-1'&&isset($interface['name']['menudatanotup'])){
			echo $interface['name']['menudatanotup'];
		}
		else{
			echo '資料庫尚未上傳資料。';
		}
	}
	else{
		if(!isset($Cconn)||!$Cconn){//Cover資料庫不存在或連線建立失敗
		}
		else{
			$sql='SELECT coverbizdate,coverzcounter,usercode,username,bizdate,consecnumber,salesttlamt,tax1,tax2,tax3,tax4,tax9,(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,1,posTA1-1) END) pointTA1,(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,posTA1+1) END) ta1,(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,1,posTA2-1) END) pointTA2,(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,posTA2+1) END) ta2,(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,1,posTA3-1) END) pointTA3,(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,posTA3+1) END) ta3,(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,1,posTA4-1) END) pointTA4,(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,posTA4+1) END) ta4,(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,1,posTA5-1) END) pointTA5,(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,posTA5+1) END) ta5,(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,1,posTA6-1) END) pointTA6,(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,posTA6+1) END) ta6,(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,1,posTA7-1) END) pointTA7,(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,posTA7+1) END) ta7,(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,1,posTA8-1) END) pointTA8,(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,posTA8+1) END) ta8,(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,1,posTA9-1) END) pointTA9,(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,posTA9+1) END) ta9,(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,1,posTA10-1) END) pointTA10,(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,posTA10+1) END) ta10,(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) nontax';
			$checkintella='PRAGMA table_info(list)';
			$allcolumn=sqlquery($Cconn,$checkintella,'sqlite');
			if(!in_array('intella',array_column($allcolumn,'name'))){
				//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
			}
			else{
				$sql=$sql.',intella';
			}
			if(!isset($otherpay)){
			}
			else{
				foreach($otherpay as $io=>$iv){
					if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
					}
					else{
						$sql=$sql.',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',1,pos'.strtoupper($iv['location']).'-1) END) point'.$iv['location'].',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',pos'.strtoupper($iv['location']).'+1) END) '.$iv['location'];
					}
				}
			}
			$sql=$sql.',nbchkdate,nbchktime,nbchknumber,createdatetime,state FROM (SELECT *,INSTR(ta1,"=") AS posTA1,INSTR(ta2,"=") AS posTA2,INSTR(ta3,"=") AS posTA3,INSTR(ta4,"=") AS posTA4,INSTR(ta5,"=") AS posTA5,INSTR(ta6,"=") AS posTA6,INSTR(ta7,"=") AS posTA7,INSTR(ta8,"=") AS posTA8,INSTR(ta9,"=") AS posTA9,INSTR(ta10,"=") AS posTA10,INSTR(nontax,"=") AS posNONTAX';
			if(!isset($otherpay)){
			}
			else{
				foreach($otherpay as $io=>$iv){
					if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
					}
					else{
						$sql=$sql.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
					}
				}
			}
			$sql=$sql.' FROM list WHERE bizdate="'.$end.'")';
			$tempcover=sqlquery($Cconn,$sql,'sqlite');
			sqlclose($Cconn,'sqlite');
			if(sizeof($tempcover)>0&&isset($tempcover[0]['bizdate'])){
				$cover=array();
				foreach($tempcover as $tc){
					if(isset($tc['intella'])){
						$tempintella=preg_split('/:/',$tc['intella']);
					}
					else{
					}
					if(isset($tempintella[2])){
					}
					else{
						$tempintella[2]=0;
					}
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TAX1']=$tc['tax1'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TAX2']=$tc['tax2'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TAX3']=floatval($tc['tax3'])-floatval($tc['tax9']);
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TAX4']=$tc['tax4'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TAX9']=$tc['tax9'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA1']=$tc['ta1'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA2']=$tc['ta2'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA3']=$tc['ta3'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA4']=$tc['ta4'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA5']=$tc['ta5'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA6']=$tc['ta6'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA7']=$tc['ta7'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA8']=$tc['ta8'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA9']=$tc['ta9'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['TA10']=$tc['ta10'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['intella']=$tempintella[2];
					if(isset($otherpay)){
						foreach($otherpay as $oi=>$ov){
							if($oi=='pay'||(!isset($ov['location'])||$ov['location']=='CST011')||(isset($ov['fromdb'])&&$ov['fromdb']=='member')){
							}
							else{
								$cover[$tc['bizdate']][intval($tc['consecnumber'])][$ov['location']]=$tc[$ov['location']];
							}
						}
					}
					else{
					}
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['NONTAX']=$tc['nontax'];
				}
			}
			else{
			}
		}
		//$sql='SELECT BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMDEPTCODE,SUM(QTY) AS QTY,UNITPRICE,SUM(AMT) AS AMT FROM CST012 WHERE ((DTLMODE<>"9" AND DTLTYPE<>"9" AND DTLFUNC<>"99") OR (DTLMODE<>"4" AND DTLTYPE<>"1" AND DTLFUNC<>"01") OR (DTLMODE<>"3" AND DTLTYPE<>"1" AND DTLFUNC<>"01")) AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'") GROUP BY  BIZDATE,DTLMODE,DTLTYPE,DTLFUNC,UNITPRICE,ITEMCODE,ITEMDEPTCODE ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER ASC';
		//$first=sqlquery($conn,$sql,'sqlite');
		//$sql='SELECT BIZDATE,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,QTY,UNITPRICE,AMT FROM CST012 WHERE ((DTLMODE<>"9" AND DTLTYPE<>"9" AND DTLFUNC<>"99") OR (DTLMODE<>"4" AND DTLTYPE<>"1" AND DTLFUNC<>"01") OR (DTLMODE<>"3" AND DTLTYPE<>"1" AND DTLFUNC<>"01")) AND SELECTIVEITEM1 IS NOT NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CONSECNUMBER IN (SELECT CONSECNUMBER FROM CST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'")';
		//$second=sqlquery($conn,$sql,'sqlite');
		$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
		$res=sqlquery($conn,$sql,'sqlite');
		$complete=0;
		if(isset($res[0]['name'])){
			if(isset($init['init']['cclasstime'])&&$init['init']['cclasstime']=='1'){
				$class=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/class.ini',true);
				$tran='SELECT BIZDATE,CONSECNUMBER,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,CASE WHEN (TAX6+TAX7+TAX8)=0 THEN 1 ELSE TAX6 END AS TAX6,TAX7,TAX8,TAX9,NONTAX,';

				$checkintella='PRAGMA table_info(tempCST011)';
				$allcolumn=sqlquery($conn,$checkintella,'sqlite');
				if(!in_array('intella',array_column($allcolumn,'name'))){
					//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
				}
				else{
					$tran=$tran.'intella,';
				}

				if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
					for($i=1;$i<sizeof($otherpay);$i++){
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							$tran=$tran.'(CASE pos'.$otherpay['item'.$i]['dbname'].' WHEN 0 THEN '.$otherpay['item'.$i]['dbname'].' ELSE SUBSTR('.$otherpay['item'.$i]['dbname'].',1,pos'.$otherpay['item'.$i]['dbname'].'-1) END) point'.$otherpay['item'.$i]['dbname'].',(CASE pos'.$otherpay['item'.$i]['dbname'].' WHEN 0 THEN '.$otherpay['item'.$i]['dbname'].' ELSE SUBSTR('.$otherpay['item'.$i]['dbname'].',pos'.$otherpay['item'.$i]['dbname'].'+1) END) '.$otherpay['item'.$i]['dbname'].',';
						}
						else{
							$tran=$tran.'(CASE pos'.strtoupper($otherpay['item'.$i]['location']).' WHEN 0 THEN '.$otherpay['item'.$i]['location'].' ELSE SUBSTR('.$otherpay['item'.$i]['location'].',1,pos'.strtoupper($otherpay['item'.$i]['location']).'-1) END) point'.$otherpay['item'.$i]['location'].',(CASE pos'.strtoupper($otherpay['item'.$i]['location']).' WHEN 0 THEN '.$otherpay['item'.$i]['location'].' ELSE SUBSTR('.$otherpay['item'.$i]['location'].',pos'.strtoupper($otherpay['item'.$i]['location']).'+1) END) '.$otherpay['item'.$i]['location'].',';
						}
					}
				}
				else{
				}
				$tran=$tran.'CASE';
				for($c=1;$c<=$class['class']['number'];$c++){
					if($c==$class['class']['number']){
						$tran=$tran.' ELSE '.$c;
					}
					else{
						$tran=$tran.' WHEN SUBSTR(CREATEDATETIME,1,8)="'.$end.'" AND CAST(SUBSTR(CREATEDATETIME,9,4) AS INTEGER)>=CAST("'.$class['c'.$c]['start'].'" AS INTEGER) AND CAST(SUBSTR(CREATEDATETIME,9,4) AS INTEGER)<=CAST("'.$class['c'.$c]['end'].'" AS INTEGER) THEN "'.$c.'"';
					}
				}
				$tran=$tran.' END AS ZCOUNTER FROM ';

				$temptran=$tran.'(SELECT *,INSTR(TA1,"=") AS posTA1,INSTR(TA2,"=") AS posTA2,INSTR(TA3,"=") AS posTA3,INSTR(TA4,"=") AS posTA4,INSTR(TA5,"=") AS posTA5,INSTR(TA6,"=") AS posTA6,INSTR(TA7,"=") AS posTA7,INSTR(TA8,"=") AS posTA8,INSTR(TA9,"=") AS posTA9,INSTR(TA10,"=") AS posTA10,INSTR(NONTAX,"=") AS posNONTAX';
				if(!isset($otherpay)){
				}
				else{
					foreach($otherpay as $io=>$iv){
						if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
						}
						else{
							$temptran=$temptran.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
						}
					}
				}
				$temptran=$temptran.' FROM tempCST011 WHERE BIZDATE="'.$end.'" AND NBCHKNUMBER IS NULL)';

				$tran=$tran.'(SELECT *,INSTR(TA1,"=") AS posTA1,INSTR(TA2,"=") AS posTA2,INSTR(TA3,"=") AS posTA3,INSTR(TA4,"=") AS posTA4,INSTR(TA5,"=") AS posTA5,INSTR(TA6,"=") AS posTA6,INSTR(TA7,"=") AS posTA7,INSTR(TA8,"=") AS posTA8,INSTR(TA9,"=") AS posTA9,INSTR(TA10,"=") AS posTA10,INSTR(NONTAX,"=") AS posNONTAX';
				if(!isset($otherpay)){
				}
				else{
					foreach($otherpay as $io=>$iv){
						if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
						}
						else{
							$tran=$tran.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
						}
					}
				}
				$tran=$tran.' FROM CST011 WHERE BIZDATE="'.$end.'" AND NBCHKNUMBER IS NULL)';
				

				$sql1='SELECT CONSECNUMBER,SUM(TAX6+TAX7+TAX8) AS QTY,BIZDATE,ZCOUNTER,SUM(SALESTTLAMT+TAX1) AS AMT,SUM(TAX2) AS cashmoney,SUM(TAX3-TAX9) AS cash,SUM(TAX9) AS cashcomm,NONTAX'; 
				//$checkintella='PRAGMA table_info(tempCST011)';
				//$allcolumn=sqlquery($conn,$checkintella,'sqlite');
				if(!in_array('intella',array_column($allcolumn,'name'))){
					//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
				}
				else{
					$sql1=$sql1.',intella';
				}
				if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
					for($i=1;$i<sizeof($otherpay);$i++){
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							$sql1=$sql1.',SUM('.$otherpay['item'.$i]['dbname'].') AS '.$otherpay['item'.$i]['dbname'];
						}
						else{
							$sql1=$sql1.',SUM('.$otherpay['item'.$i]['location'].') AS '.$otherpay['item'.$i]['location'];
						}
					}
				}
				else{
				}
				$tempsql1=$sql1.' FROM ('.$temptran.') GROUP BY BIZDATE,CONSECNUMBER,ZCOUNTER ORDER BY BIZDATE ASC,CAST(ZCOUNTER AS INTEGER) ASC';
				$sql1=$sql1.' FROM ('.$tran.') GROUP BY BIZDATE,CONSECNUMBER,ZCOUNTER ORDER BY BIZDATE ASC,CAST(ZCOUNTER AS INTEGER) ASC';
				
				$listarray=sqlquery($conn,$sql1,'sqlite');
				$templistarray=sqlquery($conn,$tempsql1,'sqlite');
				//print_r($templistarray);
				$sql2='SELECT CST012.DTLMODE,SUM(CST012.AMT) AS AMT,CST012.BIZDATE,A.ZCOUNTER FROM CST012 JOIN ('.$tran.') AS A ON A.CONSECNUMBER=CST012.CONSECNUMBER AND A.BIZDATE=CST012.BIZDATE WHERE (CST012.DTLMODE="4" OR CST012.DTLMODE="3") AND CST012.DTLTYPE="1" AND CST012.DTLFUNC="01" GROUP BY CST012.DTLMODE,CST012.BIZDATE,A.ZCOUNTER ORDER BY A.ZCOUNTER';
				$tempoutmoney=sqlquery($conn,$sql2,'sqlite');
			}
			else{
				$sql='SELECT CONSECNUMBER,SUM(TAX6+TAX7+TAX8) AS QTY,BIZDATE,ZCOUNTER,SUM(SALESTTLAMT+TAX1) AS AMT,SUM(TAX2) AS cashmoney,SUM(TAX3-TAX9) AS cash,SUM(TAX9) AS cashcomm'; 
				$checkintella='PRAGMA table_info(tempCST011)';
				$allcolumn=sqlquery($conn,$checkintella,'sqlite');
				if(!in_array('intella',array_column($allcolumn,'name'))){
					//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
				}
				else{
					$sql=$sql.',intella';
				}
				if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
					for($i=1;$i<sizeof($otherpay);$i++){
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							$sql=$sql.',(CASE pos'.$otherpay['item'.$i]['dbname'].' WHEN 0 THEN '.$otherpay['item'.$i]['dbname'].' ELSE SUBSTR('.$otherpay['item'.$i]['dbname'].',1,pos'.$otherpay['item'.$i]['dbname'].'-1) END) point'.$otherpay['item'.$i]['dbname'].',(CASE pos'.$otherpay['item'.$i]['dbname'].' WHEN 0 THEN '.$otherpay['item'.$i]['dbname'].' ELSE SUBSTR('.$otherpay['item'.$i]['dbname'].',pos'.$otherpay['item'.$i]['dbname'].'+1) END) '.$otherpay['item'.$i]['dbname'];
						}
						else{
							$sql=$sql.',(CASE pos'.strtoupper($otherpay['item'.$i]['location']).' WHEN 0 THEN '.$otherpay['item'.$i]['location'].' ELSE SUBSTR('.$otherpay['item'.$i]['location'].',1,pos'.strtoupper($otherpay['item'.$i]['location']).'-1) END) point'.$otherpay['item'.$i]['location'].',(CASE pos'.strtoupper($otherpay['item'.$i]['location']).' WHEN 0 THEN '.$otherpay['item'.$i]['location'].' ELSE SUBSTR('.$otherpay['item'.$i]['location'].',pos'.strtoupper($otherpay['item'.$i]['location']).'+1) END) '.$otherpay['item'.$i]['location'];
						}
					}
				}
				else{
				}
				$sql=$sql.' FROM (SELECT BIZDATE,CONSECNUMBER,ZCOUNTER,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,TAX7,TAX8,TAX9,NONTAX,';
				//$checkintella='PRAGMA table_info(tempCST011)';
				//$allcolumn=sqlquery($conn,$checkintella,'sqlite');
				if(!in_array('intella',array_column($allcolumn,'name'))){
					//echo '<div style="display:none;">'.print_r($allcolumn,true).'</div>';
				}
				else{
					$sql=$sql.'intella,';
				}
				if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
					for($i=1;$i<sizeof($otherpay);$i++){
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							$sql=$sql.$otherpay['item'.$i]['dbname'].',';
						}
						else{
							$sql=$sql.$otherpay['item'.$i]['location'].',';
						}
					}
				}
				else{
				}
				$sql=$sql.'CASE WHEN (TAX6+TAX7+TAX8)=0 THEN 1 ELSE TAX6 END AS TAX6,INSTR(TA1,"=") AS posTA1,INSTR(TA2,"=") AS posTA2,INSTR(TA3,"=") AS posTA3,INSTR(TA4,"=") AS posTA4,INSTR(TA5,"=") AS posTA5,INSTR(TA6,"=") AS posTA6,INSTR(TA7,"=") AS posTA7,INSTR(TA8,"=") AS posTA8,INSTR(TA9,"=") AS posTA9,INSTR(TA10,"=") AS posTA10,INSTR(NONTAX,"=") AS posNONTAX';
				if(!isset($otherpay)){
				}
				else{
					foreach($otherpay as $io=>$iv){
						if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
						}
						else{
							$sql=$sql.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
						}
					}
				}
				$tempsql=$sql.' FROM tempCST011 WHERE BIZDATE="'.$end.'" AND NBCHKNUMBER IS NULL) GROUP BY BIZDATE,CONSECNUMBER,ZCOUNTER ORDER BY BIZDATE ASC,ZCOUNTER ASC';
				$sql=$sql.' FROM CST011 WHERE BIZDATE="'.$end.'" AND NBCHKNUMBER IS NULL) GROUP BY BIZDATE,CONSECNUMBER,ZCOUNTER ORDER BY BIZDATE ASC,ZCOUNTER ASC';
				//echo $tempsql;
				$listarray=sqlquery($conn,$sql,'sqlite');
				$templistarray=sqlquery($conn,$tempsql,'sqlite');
				$sql='SELECT DTLMODE,SUM(AMT) AS AMT,BIZDATE,ZCOUNTER FROM CST012 WHERE BIZDATE="'.$end.'" AND (DTLMODE="4" OR DTLMODE="3") AND DTLTYPE="1" AND DTLFUNC="01" GROUP BY DTLMODE,BIZDATE,ZCOUNTER';
				$tempoutmoney=sqlquery($conn,$sql,'sqlite');
			}
		}
		else{
			$complete++;
		}
		if($complete==1){
			echo '資料庫未完整上傳。';
		}
		else{
		}
		if(!isset($listarray)||sizeof($listarray)==0){
			if($interface!='-1'&&isset($interface['name']['searchdataempty'])){
				echo $interface['name']['searchdataempty'];
			}
			else{
				echo '搜尋時間區間並無資料。';
			}
		}
		else{
			$totalsum=array();
			$temptotalsum=array();
			$tempzcounter=0;
			foreach($listarray as $l){

				if(isset($list[$l['BIZDATE']]['ZCOUNTER'])&&$tempzcounter!=$l['ZCOUNTER']){
					$tempzcounter=$l['ZCOUNTER'];
					$list[$l['BIZDATE']]['ZCOUNTER']++;
					$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['zcounter']=$l['ZCOUNTER'];
					$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['money']=$l['AMT'];
					$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['qty']=$l['QTY'];

					if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])])){
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashmoney']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX2'];
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashcomm']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX9'];
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cash']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX3'];
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella'])){
							$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella']);
						}
						else{
							$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella'];
						}
					}
					else{
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashmoney']=$l['cashmoney'];
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashcomm']=$l['cashcomm'];
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cash']=$l['cash'];
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=$l[$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=$l[$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=$l[$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=$l[$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($l['intella'])){
							$tempintella=preg_split('/:/',$l['intella']);
						}
						else{
						}
						if(isset($tempintella[2])){
						}
						else{
							$tempintella[2]=0;
						}
						if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella'])){
							$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella'])+floatval($tempintella[2]);
						}
						else{
							$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella']=$tempintella[2];
						}
					}
				}
				else if(isset($list[$l['BIZDATE']]['ZCOUNTER'])&&$tempzcounter==$l['ZCOUNTER']){
					$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['money']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['money'])+floatval($l['AMT']);
					$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['qty']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['qty'])+floatval($l['QTY']);

					if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])])){
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashmoney']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashmoney'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX2']);
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashcomm']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashcomm'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX9']);
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cash']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cash'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX3']);
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella'])){
							$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella']);
						}
						else{
							$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella'];
						}
					}
					else{
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashmoney']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashmoney'])+floatval($l['cashmoney']);
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashcomm']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cashcomm'])+floatval($l['cashcomm']);
						$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cash']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['cash'])+floatval($l['cash']);
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=$l[$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=$l[$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=$l[$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=$l[$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($l['intella'])){
							$tempintella=preg_split('/:/',$l['intella']);
						}
						else{
						}
						if(isset($tempintella[2])){
						}
						else{
							$tempintella[2]=0;
						}
						if(isset($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella'])){
							$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella']=floatval($list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella'])+floatval($tempintella[2]);
						}
						else{
							$list[$l['BIZDATE']]['z'.$list[$l['BIZDATE']]['ZCOUNTER']]['intella']=$tempintella[2];
						}
					}
				}
				else{
					$tempzcounter=$l['ZCOUNTER'];
					$list[$l['BIZDATE']]['ZCOUNTER']=1;
					$list[$l['BIZDATE']]['z1']['zcounter']=$l['ZCOUNTER'];
					$list[$l['BIZDATE']]['z1']['money']=$l['AMT'];
					$list[$l['BIZDATE']]['z1']['qty']=$l['QTY'];

					if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])])){
						$list[$l['BIZDATE']]['z1']['cashmoney']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX2'];
						$list[$l['BIZDATE']]['z1']['cashcomm']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX9'];
						$list[$l['BIZDATE']]['z1']['cash']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX3'];
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($list[$l['BIZDATE']]['z1']['otherpay'])){
										$list[$l['BIZDATE']]['z1']['otherpay']=floatval($list[$l['BIZDATE']]['z1']['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$list[$l['BIZDATE']]['z1']['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($list[$l['BIZDATE']]['z1']['otherpay'])){
										$list[$l['BIZDATE']]['z1']['otherpay']=floatval($list[$l['BIZDATE']]['z1']['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$list[$l['BIZDATE']]['z1']['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($list[$l['BIZDATE']]['z1']['intella'])){
							$list[$l['BIZDATE']]['z1']['intella']=floatval($list[$l['BIZDATE']]['z1']['intella'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella']);
						}
						else{
							$list[$l['BIZDATE']]['z1']['intella']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella'];
						}
					}
					else{
						$list[$l['BIZDATE']]['z1']['cashmoney']=$l['cashmoney'];
						$list[$l['BIZDATE']]['z1']['cashcomm']=$l['cashcomm'];
						$list[$l['BIZDATE']]['z1']['cash']=$l['cash'];
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($list[$l['BIZDATE']]['z1']['otherpay'])){
										$list[$l['BIZDATE']]['z1']['otherpay']=floatval($list[$l['BIZDATE']]['z1']['otherpay'])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$list[$l['BIZDATE']]['z1']['otherpay']=$l[$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['dbname']]=$l[$otherpay['item'.$i]['dbname']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['dbname']]=$l[$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($list[$l['BIZDATE']]['z1']['otherpay'])){
										$list[$l['BIZDATE']]['z1']['otherpay']=floatval($list[$l['BIZDATE']]['z1']['otherpay'])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$list[$l['BIZDATE']]['z1']['otherpay']=$l[$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['zcounter'.$list[$l['BIZDATE']]['ZCOUNTER']][$otherpay['item'.$i]['location']]=$l[$otherpay['item'.$i]['location']];
									}
									if(isset($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])){
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=floatval($tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$tooltip[$l['BIZDATE']]['totalzcounter'][$otherpay['item'.$i]['location']]=$l[$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($l['intella'])){
							$tempintella=preg_split('/:/',$l['intella']);
						}
						else{
						}
						if(isset($tempintella[2])){
						}
						else{
							$tempintella[2]=0;
						}
						if(isset($list[$l['BIZDATE']]['z1']['intella'])){
							$list[$l['BIZDATE']]['z1']['intella']=floatval($list[$l['BIZDATE']]['z1']['intella'])+floatval($tempintella[2]);
						}
						else{
							$list[$l['BIZDATE']]['z1']['intella']=$tempintella[2];
						}
					}
				}

				if(sizeof($totalsum)==0){
					$totalsum['money']=$l['AMT'];
					$totalsum['qty']=$l['QTY'];

					if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])])){
						$totalsum['cashmoney']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX2'];
						$totalsum['cashcomm']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX9'];
						$totalsum['cash']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX3'];
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($totalsum['otherpay'])){
										$totalsum['otherpay']=floatval($totalsum['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$totalsum['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($totalsum['otherpay'])){
										$totalsum['otherpay']=floatval($totalsum['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$totalsum['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($totalsum['intella'])){
							$totalsum['intella']=floatval($totalsum['intella'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella']);
						}
						else{
							$totalsum['intella']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella'];
						}
					}
					else{
						$totalsum['cashmoney']=$l['cashmoney'];
						$totalsum['cashcomm']=$l['cashcomm'];
						$totalsum['cash']=$l['cash'];
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($totalsum['otherpay'])){
										$totalsum['otherpay']=floatval($totalsum['otherpay'])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$totalsum['otherpay']=$l[$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($totalsum['otherpay'])){
										$totalsum['otherpay']=floatval($totalsum['otherpay'])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$totalsum['otherpay']=$l[$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($l['intella'])){
							$tempintella=preg_split('/:/',$l['intella']);
						}
						else{
						}
						if(isset($tempintella[2])){
						}
						else{
							$tempintella[2]=0;
						}
						if(isset($totalsum['intella'])){
							$totalsum['intella']=floatval($totalsum['intella'])+floatval($tempintella[2]);
						}
						else{
							$totalsum['intella']=$tempintella[2];
						}
					}
					
					/*if(isset($outmoney[$l['BIZDATE']][$l['ZCOUNTER']])){
						$totalsum['outmoney']=$outmoney[$l['BIZDATE']][$l['ZCOUNTER']];
					}
					else{
						$totalsum['outmoney']=0;
					}*/
				}
				else{
					$totalsum['money']=floatval($totalsum['money'])+floatval($l['AMT']);
					$totalsum['qty']=floatval($totalsum['qty'])+floatval($l['QTY']);

					if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])])){
						$totalsum['cashmoney']=floatval($totalsum['cashmoney'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX2']);
						$totalsum['cashcomm']=floatval($totalsum['cashcomm'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX9']);
						$totalsum['cash']=floatval($totalsum['cash'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['TAX3']);
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($totalsum['otherpay'])){
										$totalsum['otherpay']=floatval($totalsum['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']]);
									}
									else{
										$totalsum['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($totalsum['otherpay'])){
										$totalsum['otherpay']=floatval($totalsum['otherpay'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']]);
									}
									else{
										$totalsum['otherpay']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($totalsum['intella'])){
							$totalsum['intella']=floatval($totalsum['intella'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella']);
						}
						else{
							$totalsum['intella']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['intella'];
						}
					}
					else{
						$totalsum['cashmoney']=floatval($totalsum['cashmoney'])+floatval($l['cashmoney']);
						$totalsum['cashcomm']=floatval($totalsum['cashcomm'])+floatval($l['cashcomm']);
						$totalsum['cash']=floatval($totalsum['cash'])+floatval($l['cash']);
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									if(isset($totalsum['otherpay'])){
										$totalsum['otherpay']=floatval($totalsum['otherpay'])+floatval($l[$otherpay['item'.$i]['dbname']]);
									}
									else{
										$totalsum['otherpay']=$l[$otherpay['item'.$i]['dbname']];
									}
								}
								else{
									if(isset($totalsum['otherpay'])){
										$totalsum['otherpay']=floatval($totalsum['otherpay'])+floatval($l[$otherpay['item'.$i]['location']]);
									}
									else{
										$totalsum['otherpay']=$l[$otherpay['item'.$i]['location']];
									}
								}
							}
						}
						else{
						}
						if(isset($l['intella'])){
							$tempintella=preg_split('/:/',$l['intella']);
						}
						else{
						}
						if(isset($tempintella[2])){
						}
						else{
							$tempintella[2]=0;
						}
						if(isset($totalsum['intella'])){
							$totalsum['intella']=floatval($totalsum['intella'])+floatval($tempintella[2]);
						}
						else{
							$totalsum['intella']=$tempintella[2];
						}
					}
					
					/*if(isset($outmoney[$l['BIZDATE']][$l['ZCOUNTER']])){
						$totalsum['outmoney']=floatval($totalsum['outmoney'])+floatval($outmoney[$l['BIZDATE']][$l['ZCOUNTER']]);
					}
					else{
					}*/
				}
			}
			//print_r($list);
			
			if(sizeof($tempoutmoney)&&isset($tempoutmoney[0]['AMT'])){
				foreach($tempoutmoney as $to){
					for($i=1;$i<=intval($list[$to['BIZDATE']]['ZCOUNTER']);$i++){
						if($list[$to['BIZDATE']]['z'.$i]['zcounter']==$to['ZCOUNTER']){
							if($to['DTLMODE']=='4'){//支出
								if(isset($list[$l['BIZDATE']]['z'.$i]['outmoney'])){
									$list[$l['BIZDATE']]['z'.$i]['outmoney']=floatval($list[$l['BIZDATE']]['z'.$i]['outmoney'])+floatval($to['AMT']);
								}
								else{
									$list[$l['BIZDATE']]['z'.$i]['outmoney']=$to['AMT'];
								}
								if(isset($totalsum['outmoney'])){
									$totalsum['outmoney']=floatval($totalsum['outmoney'])+floatval($to['AMT']);
								}
								else{
									$totalsum['outmoney']=$to['AMT'];
								}
							}
							else if($to['DTLMODE']=='3'){//收入
								if(isset($list[$l['BIZDATE']]['z'.$i]['inmoney'])){
									$list[$l['BIZDATE']]['z'.$i]['inmoney']=floatval($list[$l['BIZDATE']]['z'.$i]['inmoney'])+floatval($to['AMT']);
								}
								else{
									$list[$l['BIZDATE']]['z'.$i]['inmoney']=$to['AMT'];
								}
								if(isset($totalsum['inmoney'])){
									$totalsum['inmoney']=floatval($totalsum['inmoney'])+floatval($to['AMT']);
								}
								else{
									$totalsum['inmoney']=$to['AMT'];
								}
							}
							else{
							}
						}
						else{
						}
					}
				}
			}
			else{
			}
			
			//print_r($templistarray);
			foreach($templistarray as $l){

				if(isset($templist[$l['BIZDATE']]['ZCOUNTER'])&&$templist[$l['BIZDATE']]['ZCOUNTER']!=$l['ZCOUNTER']){
					$templist[$l['BIZDATE']]['ZCOUNTER']=$l['ZCOUNTER'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['zcounter']=$l['ZCOUNTER'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['money']=floatval($l['AMT']);
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['qty']=floatval($l['QTY']);
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cashmoney']=floatval($l['cashmoney']);
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cashcomm']=floatval($l['cashcomm']);
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cash']=floatval($l['cash']);
					if(isset($outmoney[$l['BIZDATE']][$l['ZCOUNTER']])){
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['outmoney']=floatval($outmoney[$l['BIZDATE']][$l['ZCOUNTER']]);
					}
					else{
						//$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['outmoney']=0;
					}
					if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
						for($i=1;$i<sizeof($otherpay);$i++){
							if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
								if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['dbname']]);
								}
								else{
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['dbname']];
								}
							}
							else{
								if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['location']]);
								}
								else{
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['location']];
								}
							}
						}
					}
					else{
					}
					if(isset($l['intella'])){
						$tempintella=preg_split('/:/',$l['intella']);
					}
					else{
					}
					if(isset($tempintella[2])){
					}
					else{
						$tempintella[2]=0;
					}
					if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['intella'])){
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['intella']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['intella'])+floatval($tempintella[2]);
					}
					else{
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['intella']=$tempintella[2];
					}
				}
				else if(isset($templist[$l['BIZDATE']]['ZCOUNTER'])&&$templist[$l['BIZDATE']]['ZCOUNTER']==$l['ZCOUNTER']){
					//$templist[$l['BIZDATE']]['ZCOUNTER']++;
					//$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['zcounter']=$l['ZCOUNTER'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['money']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['money'])+floatval($l['AMT']);
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['qty']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['qty'])+floatval($l['QTY']);
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cashmoney']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cashmoney'])+floatval($l['cashmoney']);
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cashcomm']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cashcomm'])+floatval($l['cashcomm']);
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cash']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cash'])+floatval($l['cash']);
					if(isset($outmoney[$l['BIZDATE']][$l['ZCOUNTER']])){
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['outmoney']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['outmoney'])+floatval($outmoney[$l['BIZDATE']][$l['ZCOUNTER']]);
					}
					else{
						//$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['outmoney']=0;
					}
					if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
						for($i=1;$i<sizeof($otherpay);$i++){
							if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
								if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['dbname']]);
								}
								else{
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['dbname']];
								}
							}
							else{
								if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['location']]);
								}
								else{
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['location']];
								}
							}
						}
					}
					else{
					}
					if(isset($l['intella'])){
						$tempintella=preg_split('/:/',$l['intella']);
					}
					else{
					}
					if(isset($tempintella[2])){
					}
					else{
						$tempintella[2]=0;
					}
					if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['intella'])){
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['intella']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['intella'])+floatval($tempintella[2]);
					}
					else{
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['intella']=$tempintella[2];
					}
				}
				else{
					$templist[$l['BIZDATE']]['ZCOUNTER']=$l['ZCOUNTER'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['zcounter']=$l['ZCOUNTER'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['money']=$l['AMT'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['qty']=$l['QTY'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cashmoney']=$l['cashmoney'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cashcomm']=$l['cashcomm'];
					$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['cash']=$l['cash'];
					if(isset($outmoney[$l['BIZDATE']][$l['ZCOUNTER']])){
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['outmoney']=$outmoney[$l['BIZDATE']][$l['ZCOUNTER']];
					}
					else{
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['outmoney']=0;
					}
					if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
						for($i=1;$i<sizeof($otherpay);$i++){
							if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
								if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['dbname']]);
								}
								else{
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['dbname']];
								}
							}
							else{
								if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($l[$otherpay['item'.$i]['location']]);
								}
								else{
									$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$l[$otherpay['item'.$i]['location']];
								}
							}
						}
					}
					else{
					}
					if(isset($l['intella'])){
						$tempintella=preg_split('/:/',$l['intella']);
					}
					else{
					}
					if(isset($tempintella[2])){
					}
					else{
						$tempintella[2]=0;
					}
					if(isset($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])){
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=floatval($templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay'])+floatval($tempintella[2]);
					}
					else{
						$templist[$l['BIZDATE']]['z'.$templist[$l['BIZDATE']]['ZCOUNTER']]['otherpay']=$tempintella[2];
					}
				}

				if(sizeof($temptotalsum)==0){
					$temptotalsum['money']=$l['AMT'];
					$temptotalsum['qty']=$l['QTY'];
					$temptotalsum['cashmoney']=$l['cashmoney'];
					$temptotalsum['cashcomm']=$l['cashcomm'];
					$temptotalsum['cash']=$l['cash'];
				}
				else{
					$temptotalsum['money']=floatval($temptotalsum['money'])+floatval($l['AMT']);
					$temptotalsum['qty']=floatval($temptotalsum['qty'])+floatval($l['QTY']);
					$temptotalsum['cashmoney']=floatval($temptotalsum['cashmoney'])+floatval($l['cashmoney']);
					$temptotalsum['cashcomm']=floatval($temptotalsum['cashcomm'])+floatval($l['cashcomm']);
					$temptotalsum['cash']=floatval($temptotalsum['cash'])+floatval($l['cash']);
				}
			}
			//print_r($templist);
		}

		if(!isset($list)||sizeof($list)==0){
		}
		else{
			echo '<table id="fixTable" style="display: block;float: left;margin: 0 1px 0 0;" class="table"><tr><td colspan="2" style="text-align:center;">'.substr(date('Ymd',strtotime($end)),2,6).'</td></tr>';
			for($z=$list[date('Ymd',strtotime($end))]['ZCOUNTER'];$z>0;$z--){
				if(isset($init['init']['cclasstime'])&&$init['init']['cclasstime']=='1'){
					echo '<tr style="border-bottom:2px solid #000000;border-top:1px dotted #000000;"><td style="font-weight:bold;text-align:right;">';if($interface!='-1'&&isset($interface['name']['classlabel']))echo $interface['name']['classlabel'];else echo '班別';echo '</td><td style="font-weight:bold;text-align:right;">'.$class['c'.$list[date('Ymd',strtotime($end))]['z'.$z]['zcounter']]['name'].'</td></tr>';
				}
				else{
					echo '<tr style="border-bottom:2px solid #000000;border-top:1px dotted #000000;"><td style="font-weight:bold;text-align:right;">';if($interface!='-1'&&isset($interface['name']['classlabel']))echo $interface['name']['classlabel'];else echo '班別';echo '</td><td style="font-weight:bold;text-align:right;">'.$list[date('Ymd',strtotime($end))]['z'.$z]['zcounter'].'</td></tr>';
				}
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalsalelabel']))echo $interface['name']['totalsalelabel'];else echo '銷售總額';echo '</td><td style="text-align:right;">'.number_format($list[date('Ymd',strtotime($end))]['z'.$z]['money']).'</td></tr>';
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalpersonlabel']))echo $interface['name']['totalpersonlabel'];else echo '來客數';echo '</td><td style="text-align:right;">'.number_format($list[date('Ymd',strtotime($end))]['z'.$z]['qty']).'</td></tr>';
				if($list[date('Ymd',strtotime($end))]['z'.$z]['qty']=='0'){
					echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalavg']))echo $interface['name']['totalavg'];else echo '平均客單價';echo '</td><td style="text-align:right;">0</td></tr>';
				}
				else{
					echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalavg']))echo $interface['name']['totalavg'];else echo '平均客單價';echo '</td><td style="text-align:right;">'.number_format(round($list[date('Ymd',strtotime($end))]['z'.$z]['money']/$list[date('Ymd',strtotime($end))]['z'.$z]['qty'],1)).'</td></tr>';
				}
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalinmoney']))echo $interface['name']['totalinmoney'];else echo '收入費用';echo '</td><td style="text-align:right;">';
				if(isset($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney'])){
					echo number_format($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney']);
				}
				else{
					echo '0';
				}
				echo '</td></tr>';
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totaloutmoney']))echo $interface['name']['totaloutmoney'];else echo '支出費用';echo '</td><td style="text-align:right;">';
				if(isset($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney'])){
					echo number_format($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney']);
				}
				else{
					echo '0';
				}
				echo '</td></tr>';
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalmoney']))echo $interface['name']['totalmoney'];else echo '現金收入';echo '</td><td style="text-align:right;">'.number_format($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney']).'</td></tr>';
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalcashcomm']))echo $interface['name']['totalcashcomm'];else echo '信用卡手續費';echo '</td><td style="text-align:right;">'.number_format($list[date('Ymd',strtotime($end))]['z'.$z]['cashcomm']).'</td></tr>';
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalcashlabel']))echo $interface['name']['totalcashlabel'];else echo '信用卡收入';echo '</td><td style="text-align:right;">'.number_format($list[date('Ymd',strtotime($end))]['z'.$z]['cash']).'</td></tr>';
				if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
					echo '<tr class="zcounter'.$z.'"><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalotherlabel']))echo $interface['name']['totalotherlabel'];else echo '其他付款';echo '</td><td style="text-align:right;">'.number_format($list[date('Ymd',strtotime($end))]['z'.$z]['otherpay']).'</td></tr>';
				}
				else{
					echo '<tr class="zcounter'.$z.'"><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalotherlabel']))echo $interface['name']['totalotherlabel'];else echo '其他付款';echo '</td><td style="text-align:right;">0</td></tr>';
				}
				if(isset($init['init']['intellapay'])&&$init['init']['intellapay']=='1'){
					echo '<tr class="intellalink" style="cursor: pointer;"><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalintellalabel']))echo $interface['name']['totalintellalabel'];else echo '英特拉支付';echo '</td><td style="text-align:right;">'.number_format($list[date('Ymd',strtotime($end))]['z'.$z]['intella']).'</td></tr>';
				}
				else{
				}
				/*echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalclearsalelabel']))echo $interface['name']['totalclearsalelabel'];else echo '銷售淨額';echo '</td><td style="text-align:right;">';
				if(isset($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney'])){
					echo number_format(floatval($list[date('Ymd',strtotime($end))]['z'.$z]['money'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashcomm'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney']));
				}
				else{
					echo number_format(floatval($list[date('Ymd',strtotime($end))]['z'.$z]['money'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashcomm']));
				}
				echo '</td></tr>';*/
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['returnmoney']))echo $interface['name']['returnmoney'];else echo '繳回金額';echo '</td><td style="text-align:right;">';
				if(isset($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney'])&&isset($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney'])){
					echo number_format(floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney']));
					if(isset($totalsum['returnmoney'])){
						$totalsum['returnmoney']=floatval($totalsum['returnmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney']);
					}
					else{
						$totalsum['returnmoney']=floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney']);
					}
				}
				else if(isset($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney'])){
					echo number_format(floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney']));
					if(isset($totalsum['returnmoney'])){
						$totalsum['returnmoney']=floatval($totalsum['returnmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney']);
					}
					else{
						$totalsum['returnmoney']=floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['outmoney']);
					}
				}
				else if(isset($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney'])){
					echo number_format(floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney']));
					if(isset($totalsum['returnmoney'])){
						$totalsum['returnmoney']=floatval($totalsum['returnmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney']);
					}
					else{
						$totalsum['returnmoney']=floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['inmoney']);
					}
				}
				else{
					echo number_format(floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney']));
					if(isset($totalsum['returnmoney'])){
						$totalsum['returnmoney']=floatval($totalsum['returnmoney'])+floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney']);
					}
					else{
						$totalsum['returnmoney']=floatval($list[date('Ymd',strtotime($end))]['z'.$z]['cashmoney']);
					}
				}
				echo '</td></tr>';
				if(isset($templist[date('Ymd',strtotime($end))]['z'.$z]['money'])){
					echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalnotsalelabel']))echo $interface['name']['totalnotsalelabel'];else echo '未結銷售額';echo '</td><td style="text-align:right;">'.number_format($templist[date('Ymd',strtotime($end))]['z'.$z]['money']).'</td></tr>';
				}
				else{
					echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalnotsalelabel']))echo $interface['name']['totalnotsalelabel'];else echo '未結銷售額';echo '</td><td style="text-align:right;">0</td></tr>';
				}
				if(isset($templist[date('Ymd',strtotime($end))]['z'.$z]['qty'])){
					echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalnotpersonlabel']))echo $interface['name']['totalnotpersonlabel'];else echo '未結來客數';echo '</td><td style="text-align:right;">'.number_format($templist[date('Ymd',strtotime($end))]['z'.$z]['qty']).'</td></tr>';
				}
				else{
					echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalnotpersonlabel']))echo $interface['name']['totalnotpersonlabel'];else echo '未結來客數';echo '</td><td style="text-align:right;">0</td></tr>';
				}
			}
			echo '</table>';
			echo '<table id="fixTable" style="display: block;float: left;margin: 0 0 0 1px;" class="table"><tr><td colspan="2" style="text-align:center;"><span style="visibility: hidden;">'.substr(date('Ymd',strtotime($end)),2,6).'</span></td></tr>';
			echo '<tr style="border-bottom:2px solid #000000;border-top:1px dotted #000000;"><td style="font-weight:bold;text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalamt']))echo $interface['name']['totalamt'];else echo '彙總';echo '</td><td style="font-weight:bold;text-align:right;"></td></tr>';
			echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalsalelabel']))echo $interface['name']['totalsalelabel'];else echo '銷售總額';echo '</td><td style="text-align:right;">'.number_format($totalsum['money']).'</td></tr>';
			echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalpersonlabel']))echo $interface['name']['totalpersonlabel'];else echo '來客數';echo '</td><td style="text-align:right;">'.number_format($totalsum['qty']).'</td></tr>';
			if($totalsum['qty']=='0'){
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalavg']))echo $interface['name']['totalavg'];else echo '平均客單價';echo '</td><td style="text-align:right;">0</td></tr>';
			}
			else{
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalavg']))echo $interface['name']['totalavg'];else echo '平均客單價';echo '</td><td style="text-align:right;">'.number_format(round($totalsum['money']/$totalsum['qty'],1)).'</td></tr>';
			}
			if(isset($totalsum['inmoney'])){
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalinmoney']))echo $interface['name']['totalinmoney'];else echo '收入費用';echo '</td><td style="text-align:right;">'.number_format($totalsum['inmoney']).'</td></tr>';
			}
			else{
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalinmoney']))echo $interface['name']['totalinmoney'];else echo '收入費用';echo '</td><td style="text-align:right;">0</td></tr>';
			}
			if(isset($totalsum['outmoney'])){
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totaloutmoney']))echo $interface['name']['totaloutmoney'];else echo '支出費用';echo '</td><td style="text-align:right;">'.number_format($totalsum['outmoney']).'</td></tr>';
			}
			else{
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totaloutmoney']))echo $interface['name']['totaloutmoney'];else echo '支出費用';echo '</td><td style="text-align:right;">0</td></tr>';
			}
			echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalmoney']))echo $interface['name']['totalmoney'];else echo '現金收入';echo '</td><td style="text-align:right;">'.number_format($totalsum['cashmoney']).'</td></tr>';
			echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalcashcomm']))echo $interface['name']['totalcashcomm'];else echo '信用卡手續費';echo '</td><td style="text-align:right;">'.number_format($totalsum['cashcomm']).'</td></tr>';
			echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalcashlabel']))echo $interface['name']['totalcashlabel'];else echo '信用卡收入';echo '</td><td style="text-align:right;">'.number_format($totalsum['cash']).'</td></tr>';
			if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
				echo '<tr class="totalzcounter"><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalotherlabel']))echo $interface['name']['totalotherlabel'];else echo '其他付款';echo '</td><td style="text-align:right;">'.number_format($totalsum['otherpay']).'</td></tr>';
			}
			else{
				echo '<tr class="totalzcounter"><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalotherlabel']))echo $interface['name']['totalotherlabel'];else echo '其他付款';echo '</td><td style="text-align:right;">0</td></tr>';
			}
			if(isset($init['init']['intellapay'])&&$init['init']['intellapay']=='1'){
				echo '<tr class="intellalink" style="cursor: pointer;"><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalintellalabel']))echo $interface['name']['totalintellalabel'];else echo '英特拉支付';echo '</td><td style="text-align:right;">'.number_format($totalsum['intella']).'</td></tr>';
			}
			else{
			}
			/*echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalclearsalelabel']))echo $interface['name']['totalclearsalelabel'];else echo '銷售淨額';echo '</td><td style="text-align:right;">';
			if(isset($totalsum['outmoney'])){
				echo number_format(floatval($totalsum['money'])+floatval($totalsum['cashcomm'])+floatval($totalsum['outmoney']));
			}
			else{
				echo number_format(floatval($totalsum['money'])+floatval($totalsum['cashcomm']));
			}
			echo '</td></tr>';*/
			echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['returnmoney']))echo $interface['name']['returnmoney'];else echo '繳回金額';echo '</td><td style="text-align:right;">';
			if(isset($totalsum['returnmoney'])){
				echo number_format($totalsum['returnmoney']);
			}
			else{
				echo '0';
			}
			echo '</td></tr>';
			if(isset($temptotalsum['money'])){
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalnotsalelabel']))echo $interface['name']['totalnotsalelabel'];else echo '未結銷售額';echo '</td><td style="text-align:right;">'.number_format($temptotalsum['money']).'</td></tr>';
			}
			else{
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalnotsalelabel']))echo $interface['name']['totalnotsalelabel'];else echo '未結銷售額';echo '</td><td style="text-align:right;">0</td></tr>';
			}
			if(isset($temptotalsum['qty'])){
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalnotpersonlabel']))echo $interface['name']['totalnotpersonlabel'];else echo '未結來客數';echo '</td><td style="text-align:right;">'.number_format($temptotalsum['qty']).'</td></tr>';
			}
			else{
				echo '<tr><td style="text-align:right;">';if($interface!='-1'&&isset($interface['name']['totalnotpersonlabel']))echo $interface['name']['totalnotpersonlabel'];else echo '未結來客數';echo '</td><td style="text-align:right;">0</td></tr>';
			}
			echo '</table>';
		}

		if(sizeof($tooltip)>0){
			echo '<div style="display:none;">'.print_r($tooltip,true).'</div>';
			foreach($tooltip as $data){
				foreach($data as $class=>$value){
					echo '<div class="mytooltip" id="'.$class.'" style="display:none;position: fixed;top:5px;z-index:10;background-color: #ffffff; padding: 5px; border: 1px solid #89898980; border-radius: 5px;"><table><tr><th colspan="2">其他付款明細</th></tr>';
					foreach($value as $name=>$value){
						echo '<tr>';
						echo '<td>';
						if(isset($otherpay['item1'])){
							for($o=1;$o<sizeof($otherpay);$o++){
								if((isset($otherpay['item'.$o]['dbname'])&&$otherpay['item'.$o]['dbname']==$name)||(isset($otherpay['item'.$o]['location'])&&$otherpay['item'.$o]['location']==$name)){
									echo $otherpay['item'.$o]['name'];
									break;
								}
								else{
								}
							}
						}
						else{
							echo $name;
						}
						echo '</td>';
						echo '<td style="text-align:right;">'.number_format($value).'</td>';
						echo '</tr>';
					}
					echo '</table>';
					echo '</div>';
				}
			}
		}
		else{
		}
	}
	sqlclose($conn,'sqlite');
}
else{
}
?>