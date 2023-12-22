<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['startdate'])){
	$list=array();
	$cover=array();//暫存當月修改金額資料(修改月份可能跟紀錄月份不同)
	if($_SESSION['DB']==''&&file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['dbname'].'/Cover.db')){
		$Cconn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['dbname'],'Cover.db','','','','sqlite');
		if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['dbname'].'/otherpay.ini')){
			$otherpay=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['dbname'].'/otherpay.ini',true);
		}
		else{
		}
	}
	else if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/Cover.db')){
		$Cconn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'Cover.db','','','','sqlite');
		if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/otherpay.ini')){
			$otherpay=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/otherpay.ini',true);
		}
		else{
		}
	}
	else{
	}
	if(!isset($Cconn)||!$Cconn){
	}
	else{
		$sql='SELECT bizdate,consecnumber,SUM(salesttlamt + tax1 + tax9) AS amt,SUM(tax2) AS tax2,SUM(tax3) AS tax3,SUM(tax4) AS tax4,SUM(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,posTA1+1) END) ta1,SUM(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,1,posTA2-1) END) pointTA2,SUM(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,posTA2+1) END) ta2,SUM(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,1,posTA3-1) END) pointTA3,SUM(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,posTA3+1) END) ta3,SUM(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,1,posTA4-1) END) pointTA4,SUM(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,posTA4+1) END) ta4,SUM(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,1,posTA5-1) END) pointTA5,SUM(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,posTA5+1) END) ta5,SUM(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,1,posTA6-1) END) pointTA6,SUM(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,posTA6+1) END) ta6,SUM(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,1,posTA7-1) END) pointTA7,SUM(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,posTA7+1) END) ta7,SUM(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,1,posTA8-1) END) pointTA8,SUM(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,posTA8+1) END) ta8,SUM(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,1,posTA9-1) END) pointTA9,SUM(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,posTA9+1) END) ta9,SUM(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,1,posTA10-1) END) pointTA10,SUM(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,posTA10+1) END) ta10,SUM(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) pointNONTAX,SUM(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) nontax';
		if(!isset($otherpay)){
		}
		else{
			foreach($otherpay as $io=>$iv){
				if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($iv['fromdb'])&&$iv['fromdb']=='member')){
				}
				else{
					$sql=$sql.',SUM(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',1,pos'.strtoupper($iv['location']).'-1) END) point'.$iv['location'].',SUM(CASE pos'.strtoupper($iv['location']).' WHEN 0 THEN '.$iv['location'].' ELSE SUBSTR('.$iv['location'].',pos'.strtoupper($iv['location']).'+1) END) '.$iv['location'];
				}
			}
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
		$sql=$sql.' FROM list WHERE bizdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=1) GROUP BY bizdate,consecnumber';
		$tempcover=sqlquery($Cconn,$sql,'sqlite');
		if(sizeof($tempcover)>0&&isset($tempcover[0]['bizdate'])){
			foreach($tempcover as $tc){
				$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax2']=$tc['tax2'];
				$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax3']=$tc['tax3'];
				$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax4']=$tc['tax4'];
			}
		}
		else{
		}
	}
	$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
	$complete=0;
	for($m=0;$m<=$totalMon;$m++){
		if($_SESSION['DB']==''){
			$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
		}
		else{
			$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$m.' month')).'.db','','','','sqlite');
		}
		if(!$conn){
			echo '資料庫尚未上傳資料。';
		}
		else{
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql='SELECT BIZDATE,INVOICENUMBER,CONSECNUMBER,CLKCODE,(SALESTTLAMT+TAX1) AS SALESTTLAMT,TAX2,TAX3,TAX4,NBCHKDATE AS VOIDTIME,NBCHKTIME AS VOIDPERSONCODE,NBCHKNUMBER AS VOIDTAG,ZCOUNTER,CREATEDATETIME,UPDATEDATETIME,REMARKS FROM CST011 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC';
				$first=sqlquery($conn,$sql,'sqlite');
				if(sizeof($first)==0){
				}
				else{
					foreach($first as $item){
						if(isset($list[$item['BIZDATE']])){
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<tr';
							if(strlen($item['VOIDTAG'])>=1){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].' style="color:#ff0000;"';
							}
							else{
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'><td>'.$item['CONSECNUMBER'].'</td><td>';
							if(strlen($item['INVOICENUMBER'])==10){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].$item['INVOICENUMBER'];
							}
							else{
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</td><td style="text-align:right;">'.number_format($item['SALESTTLAMT']).'</td><td style="text-align:right;">';
							if(isset($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax2'])){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax2']);
							}
							else{
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($item['TAX2']);
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</td><td style="text-align:right;">';
							if(isset($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax3'])){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax3']);
							}
							else{
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($item['TAX3']);
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</td><td style="text-align:right;">';
							if(isset($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax4'])){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax4']);
							}
							else{
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($item['TAX4']);
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</td><td style="text-align:center;">'.$item['CLKCODE'].'</td><td>'.$item['CREATEDATETIME'].'</td><td>'.$item['UPDATEDATETIME'].'</td><td style="text-align:center;">'.$item['VOIDPERSONCODE'].'</td><td>'.$item['VOIDTIME'].'</td>';
							if(strlen($item['VOIDTAG'])>1){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>'.$item['VOIDTAG'].'</td>';
							}
							else if(strlen($item['VOIDTAG'])==1){
								if(preg_match('/-/',$item['REMARKS'])){
									$voidtag=preg_split('/-/',$item['REMARKS']);
									if($voidtag[0]=='editsale'){
										$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>修改帳單</td>';
									}
									else{
										$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>作廢/註銷</td>';
									}
								}
								else{
									if($item['REMARKS']=='tempvoid'){
										$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>作廢暫結單</td>';
									}
									else{
										$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>作廢/註銷</td>';
									}
								}
							}
							else{
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td></td>';
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</tr>';
						}
						else{
							$list[$item['BIZDATE']]='<table class="table" style="border-bottom:1px solid #000000;margin-top:10px;"><tr><td colspan="11" style="padding:5px;"><h2>'.$item['BIZDATE'].'</h2></td></tr><tr><td>帳單號</td><td>電子發票號</td><td style="text-align:right;">總金額</td><td style="text-align:right;">現金</td><td style="text-align:right;">信用卡</td><td style="text-align:right;">其他</td><td style="text-align:center;">結帳人員代號</td><td style="text-align:center;">開單時間</td><td style="text-align:center;">結帳時間</td><td style="text-align:center;">作廢/註銷人員代號</td><td>作廢/註銷時間</td><td>備註</td></tr><tr';
							if(strlen($item['VOIDTAG'])>=1){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].' style="color:#ff0000;"';
							}
							else{
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'><td>'.$item['CONSECNUMBER'].'</td><td>';
							if(strlen($item['INVOICENUMBER'])==10){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].$item['INVOICENUMBER'];
							}
							else{
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</td><td style="text-align:right;">'.number_format($item['SALESTTLAMT']).'</td><td style="text-align:right;">';
							if(isset($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax2'])){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax2']);
							}
							else{
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($item['TAX2']);
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</td><td style="text-align:right;">';
							if(isset($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax3'])){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax3']);
							}
							else{
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($item['TAX3']);
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</td><td style="text-align:right;">';
							if(isset($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax4'])){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($cover[$item['BIZDATE']][intval($item['CONSECNUMBER'])]['tax4']);
							}
							else{
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].number_format($item['TAX4']);
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</td><td style="text-align:center;">'.$item['CLKCODE'].'</td><td>'.$item['CREATEDATETIME'].'</td><td>'.$item['UPDATEDATETIME'].'</td><td style="text-align:center;">'.$item['VOIDPERSONCODE'].'</td><td>'.$item['VOIDTIME'].'</td>';
							if(strlen($item['VOIDTAG'])>1){
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>'.$item['VOIDTAG'].'</td>';
							}
							else if(strlen($item['VOIDTAG'])==1){
								if(preg_match('/-/',$item['REMARKS'])){
									$voidtag=preg_split('/-/',$item['REMARKS']);
									if($voidtag[0]=='editsale'){
										$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>修改帳單</td>';
									}
									else{
										$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>作廢/註銷</td>';
									}
								}
								else{
									if($item['REMARKS']=='tempvoid'){
										$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>作廢暫結單</td>';
									}
									else{
										$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td>作廢/註銷</td>';
									}
								}
							}
							else{
								$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'<td></td>';
							}
							$list[$item['BIZDATE']]=$list[$item['BIZDATE']].'</tr>';
						}
						/*if(isset($list['allqty'])){
							$list['allqty']=intval($list['allqty'])+1;
							$list['allamt']=floatval($list['allamt'])+floatval($item['SALESTTLAMT']);
						}
						else{
							$list['allqty']=1;
							$list['allamt']=floatval($item['SALESTTLAMT']);
						}*/
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
		if(sizeof($list)==0){
			echo '搜尋時間區間並無作廢資料。';
		}
		else{
			foreach($list as $l){
				echo $l;
				echo '</table>';
			}
		}
	}
}
else{
}
?>