<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['startdate'])){
	if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/otherpay.ini')){
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
		$list=array();//暫存帳單資料
		$totalsum=array();
		if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/Cover.db')){
			$cover=array();//暫存當月修改金額資料(修改月份可能跟紀錄月份不同)
			$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'Cover.db','','','','sqlite');
			$sql='SELECT bizdate,consecnumber,SUM(salesttlamt + tax1 + tax9) AS amt,SUM(tax2) AS tax2,SUM(tax3) AS tax3,SUM(tax4) AS tax4';
			for($i=1;$i<=10;$i++){
				$sql=$sql.',(CASE posTA'.$i.' WHEN 0 THEN ta'.$i.' ELSE SUBSTR(ta'.$i.',1,posTA'.$i.'-1) END) pointta'.$i.',(CASE posTA'.$i.' WHEN 0 THEN ta'.$i.' ELSE SUBSTR(ta'.$i.',posTA'.$i.'+1) END) ta'.$i;
			}
			$sql=$sql.',(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) nontax';
			if(isset($otherpay)){
				for($i=1;$i<sizeof($otherpay);$i++){
					if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
					}
					else{
						$sql=$sql.',(CASE pos'.strtoupper($otherpay['item'.$i]['location']).' WHEN 0 THEN '.$otherpay['item'.$i]['location'].' ELSE SUBSTR('.$otherpay['item'.$i]['location'].',1,pos'.strtoupper($otherpay['item'.$i]['location']).'-1) END) point'.$otherpay['item'.$i]['location'].',(CASE pos'.strtoupper($otherpay['item'.$i]['location']).' WHEN 0 THEN '.$otherpay['item'.$i]['location'].' ELSE SUBSTR('.$otherpay['item'.$i]['location'].',pos'.strtoupper($otherpay['item'.$i]['location']).'+1) END) '.$otherpay['item'.$i]['location'];
					}
				}
			}
			else{
			}
			$sql=$sql.' FROM (SELECT *,INSTR(ta1,"=") AS posTA1,INSTR(ta2,"=") AS posTA2,INSTR(ta3,"=") AS posTA3,INSTR(ta4,"=") AS posTA4,INSTR(ta5,"=") AS posTA5,INSTR(ta6,"=") AS posTA6,INSTR(ta7,"=") AS posTA7,INSTR(ta8,"=") AS posTA8,INSTR(ta9,"=") AS posTA9,INSTR(ta10,"=") AS posTA10,INSTR(nontax,"=") AS posNONTAX';
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
			$sql=$sql.' FROM list WHERE bizdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=1 GROUP BY bizdate,consecnumber)';
			//echo $sql;
			$tempcover=sqlquery($conn,$sql,'sqlite');
			//print_r($tempcover);
			sqlclose($conn,'sqlite');
			if(sizeof($tempcover)>0&&isset($tempcover[0]['bizdate'])){
				$cover=array();
				foreach($tempcover as $tc){
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta1']=$tc['ta1'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta2']=$tc['ta2'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta3']=$tc['ta3'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta4']=$tc['ta4'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta5']=$tc['ta5'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta6']=$tc['ta6'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta7']=$tc['ta7'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta8']=$tc['ta8'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta9']=$tc['ta9'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['ta10']=$tc['ta10'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['nontax']=$tc['nontax'];
					if(isset($otherpay)){
						for($i=1;$i<sizeof($otherpay);$i++){
							if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							}
							else{
								$cover[$tc['bizdate']][intval($tc['consecnumber'])][$otherpay['item'.$i]['location']]=$tc[$otherpay['item'.$i]['location']];
							}
						}
					}
					else{
					}
				}
				//print_r($cover);
			}
			else{
			}
		}
		else{
		}
		$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
		$complete=0;
		for($mon=0;$mon<=$totalMon;$mon++){
			if($_SESSION['DB']==''){
				$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/temp','SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$mon.' month')).'.db','','','','sqlite');
			}
			else{
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/temp','SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$mon.' month')).'.db','','','','sqlite');
			}
			if(!$conn){
				echo '資料庫尚未上傳資料。';
			}
			else{
				$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
				$res=sqlquery($conn,$sql,'sqlite');
				if(isset($res[0]['name'])){
					if(isset($init['init']['cclasstime'])&&$init['init']['cclasstime']=='1'){
						$class=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/class.ini',true);
						$tran='SELECT BIZDATE,CONSECNUMBER,SALESTTLAMT,TAX1,TAX2,TAX3,CASE WHEN (TAX6+TAX7+TAX8)=0 THEN 1 ELSE TAX6 END AS TAX6,TAX7,TAX8,(CASE posTA1 WHEN 0 THEN TA1 ELSE SUBSTR(TA1,1,posTA1-1) END) pointTA1,(CASE posTA1 WHEN 0 THEN TA1 ELSE SUBSTR(TA1,posTA1+1) END) TA1,(CASE posTA2 WHEN 0 THEN TA2 ELSE SUBSTR(TA2,1,posTA2-1) END) pointTA2,(CASE posTA2 WHEN 0 THEN TA2 ELSE SUBSTR(TA2,posTA2+1) END) TA2,(CASE posTA3 WHEN 0 THEN TA3 ELSE SUBSTR(TA3,1,posTA3-1) END) pointTA3,(CASE posTA3 WHEN 0 THEN TA3 ELSE SUBSTR(TA3,posTA3+1) END) TA3,(CASE posTA4 WHEN 0 THEN TA4 ELSE SUBSTR(TA4,1,posTA4-1) END) pointTA4,(CASE posTA4 WHEN 0 THEN TA4 ELSE SUBSTR(TA4,posTA4+1) END) TA4,(CASE posTA5 WHEN 0 THEN TA5 ELSE SUBSTR(TA5,1,posTA5-1) END) pointTA5,(CASE posTA5 WHEN 0 THEN TA5 ELSE SUBSTR(TA5,posTA5+1) END) TA5,(CASE posTA6 WHEN 0 THEN TA6 ELSE SUBSTR(TA6,1,posTA6-1) END) pointTA6,(CASE posTA6 WHEN 0 THEN TA6 ELSE SUBSTR(TA6,posTA6+1) END) TA6,(CASE posTA7 WHEN 0 THEN TA7 ELSE SUBSTR(TA7,1,posTA7-1) END) pointTA7,(CASE posTA7 WHEN 0 THEN TA7 ELSE SUBSTR(TA7,posTA7+1) END) TA7,(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(TA8,1,posTA8-1) END) pointTA8,(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(TA8,posTA8+1) END) TA8,(CASE posTA9 WHEN 0 THEN TA9 ELSE SUBSTR(TA9,1,posTA9-1) END) pointTA9,(CASE posTA9 WHEN 0 THEN TA9 ELSE SUBSTR(TA9,posTA9+1) END) TA9,(CASE posTA10 WHEN 0 THEN TA10 ELSE SUBSTR(TA10,1,posTA10-1) END) pointTA10,(CASE posTA10 WHEN 0 THEN TA10 ELSE SUBSTR(TA10,posTA10+1) END) TA10,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,posNONTAX+1) END) NONTAX';
						if(!isset($otherpay)){
						}
						else{
							foreach($otherpay as $io=>$iv){
								if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
								}
								else{
									$tran=$tran.',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',1,pos'.strtoupper($iv['location']).'-1) END) point'.$iv['location'].',(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',pos'.strtoupper($iv['location']).'+1) END) '.$iv['location'];
								}
							}
						}
						$tran=$tran.',CASE';
						for($c=1;$c<=$class['class']['number'];$c++){
							if($c==$class['class']['number']){
								$tran=$tran.' ELSE '.$c;
							}
							else{
								$tran=$tran.' WHEN BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND CAST(SUBSTR(CREATEDATETIME,9,4) AS INTEGER)>=CAST("'.$class['c'.$c]['start'].'" AS INTEGER) AND CAST(SUBSTR(CREATEDATETIME,9,4) AS INTEGER)<=CAST("'.$class['c'.$c]['end'].'" AS INTEGER) THEN "'.$c.'"';
							}
							
						}
						$tran=$tran.' END AS ZCOUNTER FROM (SELECT *,INSTR(TA1,"=") AS posTA1,INSTR(TA2,"=") AS posTA2,INSTR(TA3,"=") AS posTA3,INSTR(TA4,"=") AS posTA4,INSTR(TA5,"=") AS posTA5,INSTR(TA6,"=") AS posTA6,INSTR(TA7,"=") AS posTA7,INSTR(TA8,"=") AS posTA8,INSTR(TA9,"=") AS posTA9,INSTR(TA10,"=") AS posTA10,INSTR(NONTAX,"=") AS posNONTAX';
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
						$tran=$tran.' FROM CST011 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND NBCHKNUMBER IS NULL)';

						$sql1='SELECT SUM(TAX6+TAX7+TAX8) AS QTY,BIZDATE,CONSECNUMBER,ZCOUNTER,SUM(SALESTTLAMT+TAX1) AS AMT,SUM(TAX2) AS cashmoney,SUM(TAX3) AS cash,NONTAX'; 
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
						$sql1=$sql1.' FROM ('.$tran.') GROUP BY BIZDATE,CONSECNUMBER,ZCOUNTER ORDER BY BIZDATE ASC,CAST(ZCOUNTER AS INTEGER) ASC';
						$listarray=sqlquery($conn,$sql1,'sqlite');
						$sql2='SELECT SUM(AMT) AS AMT,CST012.BIZDATE,A.ZCOUNTER,A.NONTAX FROM CST012 JOIN ('.$tran.') AS A ON A.CONSECNUMBER=CST012.CONSECNUMBER AND A.BIZDATE=CST012.BIZDATE WHERE DTLMODE="4" AND DTLTYPE="1" AND DTLFUNC="01" GROUP BY CST012.BIZDATE,A.ZCOUNTER ORDER BY A.ZCOUNTER';
						$tempoutmoney=sqlquery($conn,$sql2,'sqlite');
						//echo "<input type='hidden' value='".$sql1."'>";
					}
					else{
						$sql='SELECT SUM(TAX6+TAX7+TAX8) AS QTY,BIZDATE,CONSECNUMBER,SUM(SALESTTLAMT+TAX1) AS AMT,SUM(TAX2) AS cashmoney,SUM(TAX3) AS cash'; 
						if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
							for($i=1;$i<sizeof($otherpay);$i++){
								if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
									$sql=$sql.',SUM('.$otherpay['item'.$i]['dbname'].') AS '.$otherpay['item'.$i]['dbname'];
								}
								else{
									$sql=$sql.',SUM('.$otherpay['item'.$i]['location'].') AS '.$otherpay['item'.$i]['location'];
								}
							}
						}
						else{
						}
						$sql=$sql.' FROM (SELECT BIZDATE,CONSECNUMBER,ZCOUNTER,SALESTTLAMT,TAX1,TAX2,TAX3,TAX4,CASE WHEN (TAX6+TAX7+TAX8)=0 THEN 1 ELSE TAX6 END AS TAX6,TAX7,TAX8,(CASE posTA1 WHEN 0 THEN TA1 ELSE SUBSTR(TA1,1,posTA1-1) END) pointTA1,(CASE posTA1 WHEN 0 THEN TA1 ELSE SUBSTR(TA1,posTA1+1) END) TA1,(CASE posTA2 WHEN 0 THEN TA2 ELSE SUBSTR(TA2,1,posTA2-1) END) pointTA2,(CASE posTA2 WHEN 0 THEN TA2 ELSE SUBSTR(TA2,posTA2+1) END) TA2,(CASE posTA3 WHEN 0 THEN TA3 ELSE SUBSTR(TA3,1,posTA3-1) END) pointTA3,(CASE posTA3 WHEN 0 THEN TA3 ELSE SUBSTR(TA3,posTA3+1) END) TA3,(CASE posTA4 WHEN 0 THEN TA4 ELSE SUBSTR(TA4,1,posTA4-1) END) pointTA4,(CASE posTA4 WHEN 0 THEN TA4 ELSE SUBSTR(TA4,posTA4+1) END) TA4,(CASE posTA5 WHEN 0 THEN TA5 ELSE SUBSTR(TA5,1,posTA5-1) END) pointTA5,(CASE posTA5 WHEN 0 THEN TA5 ELSE SUBSTR(TA5,posTA5+1) END) TA5,(CASE posTA6 WHEN 0 THEN TA6 ELSE SUBSTR(TA6,1,posTA6-1) END) pointTA6,(CASE posTA6 WHEN 0 THEN TA6 ELSE SUBSTR(TA6,posTA6+1) END) TA6,(CASE posTA7 WHEN 0 THEN TA7 ELSE SUBSTR(TA7,1,posTA7-1) END) pointTA7,(CASE posTA7 WHEN 0 THEN TA7 ELSE SUBSTR(TA7,posTA7+1) END) TA7,(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(TA8,1,posTA8-1) END) pointTA8,(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(TA8,posTA8+1) END) TA8,(CASE posTA9 WHEN 0 THEN TA9 ELSE SUBSTR(TA9,1,posTA9-1) END) pointTA9,(CASE posTA9 WHEN 0 THEN TA9 ELSE SUBSTR(TA9,posTA9+1) END) TA9,(CASE posTA10 WHEN 0 THEN TA10 ELSE SUBSTR(TA10,1,posTA10-1) END) pointTA10,(CASE posTA10 WHEN 0 THEN TA10 ELSE SUBSTR(TA10,posTA10+1) END) TA10,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,posNONTAX+1) END) NONTAX';
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
						$sql=$sql.' FROM (SELECT *,INSTR(TA1,"=") AS posTA1,INSTR(TA2,"=") AS posTA2,INSTR(TA3,"=") AS posTA3,INSTR(TA4,"=") AS posTA4,INSTR(TA5,"=") AS posTA5,INSTR(TA6,"=") AS posTA6,INSTR(TA7,"=") AS posTA7,INSTR(TA8,"=") AS posTA8,INSTR(TA9,"=") AS posTA9,INSTR(TA10,"=") AS posTA10,INSTR(NONTAX,"=") AS posNONTAX';
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
						$sql=$sql.' FROM CST011 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND NBCHKNUMBER IS NULL)) GROUP BY BIZDATE,CONSECNUMBER,ZCOUNTER ORDER BY BIZDATE ASC,ZCOUNTER ASC';
						$listarray=sqlquery($conn,$sql,'sqlite');
						//echo $sql;
						$sql='SELECT SUM(AMT) AS AMT,BIZDATE,ZCOUNTER FROM CST012 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND DTLMODE="4" AND DTLTYPE="1" AND DTLFUNC="01" GROUP BY BIZDATE,CONSECNUMBER,ZCOUNTER';
						$tempoutmoney=sqlquery($conn,$sql,'sqlite');
					}
					//echo $sql;
					
					if(sizeof($listarray)==0){
						echo '搜尋時間區間並無資料。';
					}
					else{
						
						foreach($listarray as $l){
							if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
								for($i=1;$i<sizeof($otherpay);$i++){
									if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
										if(isset($list[$l['BIZDATE']][$otherpay['item'.$i]['dbname']]['name'])){
											if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['dbname'])])){
												$list[$l['BIZDATE']][$otherpay['item'.$i]['dbname']]['money']=floatval($list[$l['BIZDATE']][$otherpay['item'.$i]['dbname']]['money'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['dbname'])]);
											}
											else{
												$list[$l['BIZDATE']][$otherpay['item'.$i]['dbname']]['money']=floatval($list[$l['BIZDATE']][$otherpay['item'.$i]['dbname']]['money'])+floatval($l[$otherpay['item'.$i]['dbname']]);
											}
										}
										else{
											$list[$l['BIZDATE']][$otherpay['item'.$i]['dbname']]['name']=$otherpay['item'.$i]['name'];
											if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['dbname'])])){
												$list[$l['BIZDATE']][$otherpay['item'.$i]['dbname']]['money']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['dbname'])];
											}
											else{
												$list[$l['BIZDATE']][$otherpay['item'.$i]['dbname']]['money']=$l[$otherpay['item'.$i]['dbname']];
											}
										}
									}
									else{
										if(isset($list[$l['BIZDATE']][$otherpay['item'.$i]['location']]['name'])){
											if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['location'])])){
												$list[$l['BIZDATE']][$otherpay['item'.$i]['location']]['money']=floatval($list[$l['BIZDATE']][$otherpay['item'.$i]['location']]['money'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['location'])]);
											}
											else{
												$list[$l['BIZDATE']][$otherpay['item'.$i]['location']]['money']=floatval($list[$l['BIZDATE']][$otherpay['item'.$i]['location']]['money'])+floatval($l[$otherpay['item'.$i]['location']]);
											}
										}
										else{
											$list[$l['BIZDATE']][$otherpay['item'.$i]['location']]['name']=$otherpay['item'.$i]['name'];
											if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['location'])])){
												$list[$l['BIZDATE']][$otherpay['item'.$i]['location']]['money']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['location'])];
											}
											else{
												$list[$l['BIZDATE']][$otherpay['item'.$i]['location']]['money']=$l[$otherpay['item'.$i]['location']];
											}
										}
									}
								}
							}
							else{
							}

							if(sizeof($totalsum)==0){
								if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
									for($i=1;$i<sizeof($otherpay);$i++){
										if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
											$totalsum[$otherpay['item'.$i]['dbname']]['name']=$otherpay['item'.$i]['name'];
											if(isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['dbname'])])){
												$totalsum[$otherpay['item'.$i]['dbname']]['money']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['dbname'])];
											}
											else{
												$totalsum[$otherpay['item'.$i]['dbname']]['money']=$l[$otherpay['item'.$i]['dbname']];
											}
										}
										else{
											$totalsum[$otherpay['item'.$i]['location']]['name']=$otherpay['item'.$i]['name'];
											if(isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['location'])])){
												$totalsum[$otherpay['item'.$i]['location']]['money']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['location'])];
											}
											else{
												$totalsum[$otherpay['item'.$i]['location']]['money']=$l[$otherpay['item'.$i]['location']];
											}
										}
									}
								}
								else{
								}
							}
							else{
								if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
									for($i=1;$i<sizeof($otherpay);$i++){
										if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
											$totalsum[$otherpay['item'.$i]['dbname']]['name']=$otherpay['item'.$i]['name'];
											if(isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['dbname'])])){
												$totalsum[$otherpay['item'.$i]['dbname']]['money']=floatval($totalsum[$otherpay['item'.$i]['dbname']]['money'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['dbname'])]);
											}
											else{
												$totalsum[$otherpay['item'.$i]['dbname']]['money']=floatval($totalsum[$otherpay['item'.$i]['dbname']]['money'])+floatval($l[$otherpay['item'.$i]['dbname']]);
											}
										}
										else{
											$totalsum[$otherpay['item'.$i]['location']]['name']=$otherpay['item'.$i]['name'];
											if(isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['location'])])){
												$totalsum[$otherpay['item'.$i]['location']]['money']=floatval($totalsum[$otherpay['item'.$i]['location']]['money'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])][strtolower($otherpay['item'.$i]['location'])]);
											}
											else{
												$totalsum[$otherpay['item'.$i]['location']]['money']=floatval($totalsum[$otherpay['item'.$i]['location']]['money'])+floatval($l[$otherpay['item'.$i]['location']]);
											}
										}
									}
								}
								else{
								}
							}
						}
					}
				}
				else{
					$complete++;
				}
			}
			sqlclose($conn,'sqlite');
		}
		if($complete>=($totalMon+1)){
			echo '資料庫未完整上傳。';
		}
		else{
			if($complete>0){
				echo '部分月份資料庫未完整上傳。';
			}
			else{
			}
			if(!isset($totalsum)||sizeof($totalsum)==0){
				echo "目前暫無使用其他方式付款的紀錄。";
			}
			else{
				$name=array();
				$money=array();
				if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
					for($i=1;$i<sizeof($otherpay);$i++){
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							$name[]=$totalsum[$otherpay['item'.$i]['dbname']]['name'];
							$money[]=$totalsum[$otherpay['item'.$i]['dbname']]['money'];
						}
						else{
							$name[]=$totalsum[$otherpay['item'.$i]['location']]['name'];
							$money[]=$totalsum[$otherpay['item'.$i]['location']]['money'];
						}
					}
					echo '<img src="./lib/graph/pie.php?'.date('YmdHis').'&title='.urlencode('其他付款資訊').'&name='.urlencode(implode(',',$name)).'&money='.implode(',',$money).'">';
				}
				else{
				}
				echo '<div><table id="fixTable" class="table">';
				echo '<tr style="border-bottom:2px solid #000000;border-top:1px dotted #000000;"><td colspan="2" style="font-weight:bold;text-align:center;">彙總</td></tr>';
				if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
					for($i=1;$i<sizeof($otherpay);$i++){
						if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
							echo '<tr><td style="text-align:right;">'.$totalsum[$otherpay['item'.$i]['dbname']]['name'].'</td><td style="text-align:right;">'.number_format($totalsum[$otherpay['item'.$i]['dbname']]['money']).'</td></tr>';
						}
						else{
							echo '<tr><td style="text-align:right;">'.$totalsum[$otherpay['item'.$i]['location']]['name'].'</td><td style="text-align:right;">'.number_format($totalsum[$otherpay['item'.$i]['location']]['money']).'</td></tr>';
						}
					}
				}
				else{
				}
				echo '</table></div>';
				foreach($list as $k=>$l){
					echo '<div style="margin:5px 5px;overflow:hidden;float:left;"><table id="fixTable" class="table"><tr><td colspan="2" style="text-align:center;border-bottom:2px solid #000000;border-top:1px dotted #000000;">'.$k.'</td></tr>';
					if(isset($otherpay)&&$otherpay['pay']['openpay']==1){
						for($i=1;$i<sizeof($otherpay);$i++){
							if(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011'||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
								echo '<tr><td style="text-align:right;">'.$l[$otherpay['item'.$i]['dbname']]['name'].'</td><td style="text-align:right;">'.number_format($l[$otherpay['item'.$i]['dbname']]['money']).'</td></tr>';
							}
							else{
								echo '<tr><td style="text-align:right;">'.$l[$otherpay['item'.$i]['location']]['name'].'</td><td style="text-align:right;">'.number_format($l[$otherpay['item'.$i]['location']]['money']).'</td></tr>';
							}
						}
					}
					else{
					}
					echo '</table></div>';
				}
			}
		}
	}
	else{
		echo "目前尚未開啟其他付款方式。";
	}
}
else{
}
?>