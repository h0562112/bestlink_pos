<?php
session_start();
$broke='';
$company='';
$dep='';
function myErrorHandler($errno, $errstr, $errfile, $errline){
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
    }

    switch ($errno) {
	case E_WARNING:
		if(preg_match('/database disk image is malformed/',$errstr)&&$GLOBALS['broke']!=''){
			echo $GLOBALS['broke'].'資料庫上傳不完整，導致資料庫損毀。<br>';
			$GLOBALS['broke']='';
		}
		else{
		}
		break;

    default:
		if(file_exists('../../../menudata/'.$GLOBALS['company'].'/'.$GLOBALS['dep'].'/log')){
		}
		else{
			mkdir('../../../menudata/'.$GLOBALS['company'].'/'.$GLOBALS['dep'].'/log');
		}
        $f=fopen('../../../menudata/'.$GLOBALS['company'].'/'.$GLOBALS['dep'].'/log/error.txt','a');
		fwrite($f,date('Y/m/d H:i:s').' ---- Unknown error type: ['.$errno.'] '.$errstr.'['.$errfile.' in line '.$errline.']'.PHP_EOL);
		fclose($f);
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}
$old_error_handler = set_error_handler("myErrorHandler");
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';
$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);
if(isset($_POST['startdate'])){
	$list=array();//暫存帳單資料
	$totalsum=array();
	$xaxis=array();//圖表X軸欄位
	$yaxis1=array();//圖表Y軸內容(小計金額)
	$yaxis2=array();//圖表Y軸內容(來客數)

	if((!array_key_exists('DB',$_SESSION)||empty($_SESSION['DB']))||$_SESSION['DB']==''){
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
		if(file_exists('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'].'/Cover.db')){
			$Cconn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'Cover.db','','','','sqlite');
			$sql='SELECT bizdate,consecnumber,SUM(salesttlamt + tax1 + tax9) AS amt,SUM(tax2) AS tax2,SUM(tax3) AS tax3,SUM(tax4) AS tax4,SUM(tax9) AS tax9,SUM(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,1,posTA1-1) END) pointTA1,SUM(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,posTA1+1) END) ta1,SUM(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,1,posTA2-1) END) pointTA2,SUM(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,posTA2+1) END) ta2,SUM(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,1,posTA3-1) END) pointTA3,SUM(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,posTA3+1) END) ta3,SUM(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,1,posTA4-1) END) pointTA4,SUM(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,posTA4+1) END) ta4,SUM(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,1,posTA5-1) END) pointTA5,SUM(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,posTA5+1) END) ta5,SUM(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,1,posTA6-1) END) pointTA6,SUM(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,posTA6+1) END) ta6,SUM(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,1,posTA7-1) END) pointTA7,SUM(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,posTA7+1) END) ta7,SUM(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(ta8,1,posTA8-1) END) pointTA8,SUM(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,posTA8+1) END) ta8,SUM(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,1,posTA9-1) END) pointTA9,SUM(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,posTA9+1) END) ta9,SUM(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,1,posTA10-1) END) pointTA10,SUM(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,posTA10+1) END) ta10,SUM(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) pointNONTAX,SUM(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) nontax';
			if(!isset($otherpay)){
			}
			else{
				foreach($otherpay as $io=>$iv){
					if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
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
					if($io=='pay'||(!isset($iv['location'])||$iv['location']=='CST011')||(isset($otherpay['item'.$i]['fromdb'])&&$otherpay['item'.$i]['fromdb']=='member')){
					}
					else{
						$sql=$sql.',INSTR('.$iv['location'].',"=") AS pos'.strtoupper($iv['location']);
					}
				}
			}
			$sql=$sql.' FROM list WHERE bizdate BETWEEN "'.$start.'" AND "'.$end.'" AND state=1) GROUP BY bizdate,consecnumber';
			$tempcover=sqlquery($Cconn,$sql,'sqlite');
			sqlclose($Cconn,'sqlite');
			if(sizeof($tempcover)>0&&isset($tempcover[0]['bizdate'])){
				$cover=array();
				foreach($tempcover as $tc){
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax2']=$tc['tax2'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax3']=$tc['tax3'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax4']=$tc['tax4'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax9']=$tc['tax9'];
				}
			}
			else{
			}
		}
		else{
		}
	}
	else{
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
		if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/Cover.db')){
			$Cconn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'Cover.db','','','','sqlite');
			$sql='SELECT bizdate,consecnumber,SUM(salesttlamt + tax1 + tax9) AS amt,SUM(tax2) AS tax2,SUM(tax3) AS tax3,SUM(tax4) AS tax4,SUM(tax9) AS tax9,SUM(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,1,posTA1-1) END) pointTA1,SUM(CASE posTA1 WHEN 0 THEN ta1 ELSE SUBSTR(ta1,posTA1+1) END) ta1,SUM(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,1,posTA2-1) END) pointTA2,SUM(CASE posTA2 WHEN 0 THEN ta2 ELSE SUBSTR(ta2,posTA2+1) END) ta2,SUM(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,1,posTA3-1) END) pointTA3,SUM(CASE posTA3 WHEN 0 THEN ta3 ELSE SUBSTR(ta3,posTA3+1) END) ta3,SUM(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,1,posTA4-1) END) pointTA4,SUM(CASE posTA4 WHEN 0 THEN ta4 ELSE SUBSTR(ta4,posTA4+1) END) ta4,SUM(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,1,posTA5-1) END) pointTA5,SUM(CASE posTA5 WHEN 0 THEN ta5 ELSE SUBSTR(ta5,posTA5+1) END) ta5,SUM(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,1,posTA6-1) END) pointTA6,SUM(CASE posTA6 WHEN 0 THEN ta6 ELSE SUBSTR(ta6,posTA6+1) END) ta6,SUM(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,1,posTA7-1) END) pointTA7,SUM(CASE posTA7 WHEN 0 THEN ta7 ELSE SUBSTR(ta7,posTA7+1) END) ta7,SUM(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(ta8,1,posTA8-1) END) pointTA8,SUM(CASE posTA8 WHEN 0 THEN ta8 ELSE SUBSTR(ta8,posTA8+1) END) ta8,SUM(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,1,posTA9-1) END) pointTA9,SUM(CASE posTA9 WHEN 0 THEN ta9 ELSE SUBSTR(ta9,posTA9+1) END) ta9,SUM(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,1,posTA10-1) END) pointTA10,SUM(CASE posTA10 WHEN 0 THEN ta10 ELSE SUBSTR(ta10,posTA10+1) END) ta10,SUM(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,1,posNONTAX-1) END) pointNONTAX,SUM(CASE posNONTAX WHEN 0 THEN nontax ELSE SUBSTR(nontax,posNONTAX+1) END) nontax';
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
			sqlclose($Cconn,'sqlite');
			if(sizeof($tempcover)>0&&isset($tempcover[0]['bizdate'])){
				$cover=array();
				foreach($tempcover as $tc){
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax2']=$tc['tax2'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax3']=$tc['tax3'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax4']=$tc['tax4'];
					$cover[$tc['bizdate']][intval($tc['consecnumber'])]['tax9']=$tc['tax9'];
				}
			}
			else{
			}
		}
		else{
		}
	}
	
	$totalMon=getMon($_POST['startdate'],$_POST['enddate']);
	$complete=0;
	for($mon=0;$mon<=$totalMon;$mon++){
		$GLOBALS['broke']=date("Y/m",strtotime(substr($start,0,6).'01 +'.$mon.' month'));
		if((!array_key_exists('DB',$_SESSION)||empty($_SESSION['DB']))||$_SESSION['DB']==''){
			$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dbname'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$mon.' month')).'.db','','','','sqlite');
			$GLOBALS['company']=$_POST['company'];
			$GLOBALS['dep']=$_POST['dbname'];
		}
		else{
			$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date("Ym",strtotime(substr($start,0,6).'01 +'.$mon.' month')).'.db','','','','sqlite');
			$GLOBALS['company']=$_SESSION['company'];
			$GLOBALS['dep']=$_SESSION['DB'];
		}
		if(!$conn){
			echo '資料庫尚未上傳資料。';
		}
		else{
			$sql='SELECT name FROM sqlite_master WHERE type="table" AND name="CST011"';
			$res=sqlquery($conn,$sql,'sqlite');
			if(isset($res[0]['name'])){
				$sql='SELECT *,(CASE posTA1 WHEN 0 THEN TA1 ELSE SUBSTR(TA1,1,posTA1-1) END) pointTA1,(CASE posTA1 WHEN 0 THEN TA1 ELSE SUBSTR(TA1,posTA1+1) END) TA1,(CASE posTA2 WHEN 0 THEN TA2 ELSE SUBSTR(TA2,1,posTA2-1) END) pointTA2,(CASE posTA2 WHEN 0 THEN TA2 ELSE SUBSTR(TA2,posTA2+1) END) TA2,(CASE posTA3 WHEN 0 THEN TA3 ELSE SUBSTR(TA3,1,posTA3-1) END) pointTA3,(CASE posTA3 WHEN 0 THEN TA3 ELSE SUBSTR(TA3,posTA3+1) END) TA3,(CASE posTA4 WHEN 0 THEN TA4 ELSE SUBSTR(TA4,1,posTA4-1) END) pointTA4,(CASE posTA4 WHEN 0 THEN TA4 ELSE SUBSTR(TA4,posTA4+1) END) TA4,(CASE posTA5 WHEN 0 THEN TA5 ELSE SUBSTR(TA5,1,posTA5-1) END) pointTA5,(CASE posTA5 WHEN 0 THEN TA5 ELSE SUBSTR(TA5,posTA5+1) END) TA5,(CASE posTA6 WHEN 0 THEN TA6 ELSE SUBSTR(TA6,1,posTA6-1) END) pointTA6,(CASE posTA6 WHEN 0 THEN TA6 ELSE SUBSTR(TA6,posTA6+1) END) TA6,(CASE posTA7 WHEN 0 THEN TA7 ELSE SUBSTR(TA7,1,posTA7-1) END) pointTA7,(CASE posTA7 WHEN 0 THEN TA7 ELSE SUBSTR(TA7,posTA7+1) END) TA7,(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(TA8,1,posTA8-1) END) pointTA8,(CASE posTA8 WHEN 0 THEN TA8 ELSE SUBSTR(TA8,posTA8+1) END) TA8,(CASE posTA9 WHEN 0 THEN TA9 ELSE SUBSTR(TA9,1,posTA9-1) END) pointTA9,(CASE posTA9 WHEN 0 THEN TA9 ELSE SUBSTR(TA9,posTA9+1) END) TA9,(CASE posTA10 WHEN 0 THEN TA10 ELSE SUBSTR(TA10,1,posTA10-1) END) pointTA10,(CASE posTA10 WHEN 0 THEN TA10 ELSE SUBSTR(TA10,posTA10+1) END) TA10,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,1,posNONTAX-1) END) pointNONTAX,(CASE posNONTAX WHEN 0 THEN NONTAX ELSE SUBSTR(NONTAX,posNONTAX+1) END) NONTAX';
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
				$sql=$sql.' FROM CST011 WHERE BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND NBCHKNUMBER IS NULL) ORDER BY BIZDATE ASC,CREATEDATETIME ASC';
				$listarray=sqlquery($conn,$sql,'sqlite');
				$sql='SELECT SUM(AMT) AS AMT,BIZDATE FROM CST012 WHERE DTLMODE="4" AND DTLTYPE="1" AND DTLFUNC="01" AND BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" GROUP BY BIZDATE';
				$outpay=sqlquery($conn,$sql,'sqlite');
				//echo $sql;
				if(sizeof($listarray)==0&&sizeof($outpay)==0){
					echo '搜尋時間區間並無資料。';
				}
				else{
					foreach($listarray as $l){
						if(isset($list[$l['BIZDATE']])){
							if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax2'])){
								$list[$l['BIZDATE']]['money']=floatval($list[$l['BIZDATE']]['money'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax2']);
								$list[$l['BIZDATE']]['cash']=floatval($list[$l['BIZDATE']]['cash'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax3']);
								$list[$l['BIZDATE']]['cashcomm']=floatval($list[$l['BIZDATE']]['cashcomm'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax9']);
								$list[$l['BIZDATE']]['other']=floatval($list[$l['BIZDATE']]['other'])+floatval($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax4']);
							}
							else{
								$list[$l['BIZDATE']]['money']=floatval($list[$l['BIZDATE']]['money'])+floatval($l['TAX2']);
								$list[$l['BIZDATE']]['cash']=floatval($list[$l['BIZDATE']]['cash'])+floatval($l['TAX3']);
								$list[$l['BIZDATE']]['cashcomm']=floatval($list[$l['BIZDATE']]['cashcomm'])+floatval($l['TAX9']);
								$list[$l['BIZDATE']]['other']=floatval($list[$l['BIZDATE']]['other'])+floatval($l['TAX4']);
							}
							$list[$l['BIZDATE']]['person']=floatval($list[$l['BIZDATE']]['person'])+floatval($l['TAX6'])+floatval($l['TAX7'])+floatval($l['TAX8']);
						}
						else{
							if(isset($cover)&&isset($cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax2'])){
								$list[$l['BIZDATE']]['money']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax2'];
								$list[$l['BIZDATE']]['cash']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax3'];
								$list[$l['BIZDATE']]['cashcomm']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax9'];
								$list[$l['BIZDATE']]['other']=$cover[$l['BIZDATE']][intval($l['CONSECNUMBER'])]['tax4'];
							}
							else{
								$list[$l['BIZDATE']]['money']=$l['TAX2'];
								$list[$l['BIZDATE']]['cash']=$l['TAX3'];
								$list[$l['BIZDATE']]['cashcomm']=$l['TAX9'];
								$list[$l['BIZDATE']]['other']=$l['TAX4'];
							}
							$list[$l['BIZDATE']]['person']=floatval($l['TAX6'])+floatval($l['TAX7'])+floatval($l['TAX8']);
						}
					}
					foreach($outpay as $op){
						if(isset($list[$op['BIZDATE']]['outpat'])){
							$list[$op['BIZDATE']]['outpat']=floatval($list[$op['BIZDATE']]['outpat'])+floatval($op['AMT']);
						}
						else{
							$list[$op['BIZDATE']]['outpat']=$op['AMT'];
						}
					}
				}
			}
			else{
				$complete++;
			}
			//print_r(error_get_last());
			sqlclose($conn,'sqlite');
		}
		$GLOBALS['broke']='';
	}
	//print_r($list);
	if($complete>=($totalMon+1)){
		//echo '資料庫未完整上傳。';
	}
	else{
		/*if($complete>0){
			//echo '部分月份未完整上傳。';
		}
		else{
		}*/
		if(!isset($list)||sizeof($list)==0){
		}
		else{
			if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
				$ENDDATE=strtotime(date('Ymd'));
			}
			else{
				$ENDDATE=strtotime(date('Ymd',strtotime($end)));
			}
			for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
				$datestring=substr(date('Ymd',$d),2,6);
				switch (date("N",$d)) {
					case 1:
						$datestring .= "(一)";
						break;
					case 2:
						$datestring .= "(二)";
						break;
					case 3:
						$datestring .= "(三)";
						break;
					case 4:
						$datestring .= "(四)";
						break;
					case 5:
						$datestring .= "(五)";
						break;
					case 6:
						$datestring .= "(六)";
						break;
					case 7:
						$datestring .= "(日)";
						break;
					default:
						break;
				}
				array_push($xaxis,$datestring);
				if(isset($list[date('Ymd',$d)]['money'])){
					array_push($yaxis1,(floatval($list[date('Ymd',$d)]['money'])+floatval($list[date('Ymd',$d)]['cash'])+floatval($list[date('Ymd',$d)]['other'])));
				}
				else{
					array_push($yaxis1,'0');
				}
				if(isset($list[date('Ymd',$d)]['person'])){
					array_push($yaxis2,$list[date('Ymd',$d)]['person']);
				}
				else{
					array_push($yaxis2,'0');
				}
			}
			if(sizeof($xaxis)>1){
				echo '<img src="./lib/graph/line.php?'.date('YmdHis').'&title='.urlencode('營業彙總').'&xaxis='.urlencode(implode(',',$xaxis)).'&yaxis1='.implode(',',$yaxis1).'&yaxis2='.implode(',',$yaxis2).'">';
			}
			else{
			}
			$totalmoney=0;
			$totalcash=0;
			$totalcashcomm=0;
			$totalother=0;
			$totaloutpay=0;
			$total=0;
			$totalperson=0;
			echo '<div><table id="fixTable" class="table">';
			echo '<tr style="border-bottom:2px solid #000000;border-top:1px dotted #000000;"><td style="font-weight:bold;text-align:center;">日期</td><td style="font-weight:bold;text-align:center;">現金收入</td><td style="font-weight:bold;text-align:center;">信用卡收入</td><td style="font-weight:bold;text-align:center;">信用卡手續費</td><td style="font-weight:bold;text-align:center;">其他付款收入</td><td style="font-weight:bold;text-align:center;">來客數</td><td style="font-weight:bold;text-align:center;">其他費用支出</td><td style="font-weight:bold;text-align:center;">小計</td></tr>';
			for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 day')){
				echo "<tr>";
				echo "<td style='padding:5px;text-align:center;'>".substr(date('Ymd',$d),2,6)."<br>";
				switch (date("N",$d)) {
					case 1:
						echo "(一)";
						break;
					case 2:
						echo "(二)";
						break;
					case 3:
						echo "(三)";
						break;
					case 4:
						echo "(四)";
						break;
					case 5:
						echo "(五)";
						break;
					case 6:
						echo "<span style='font-weight:bold;color:#C13333;'>(六)</span>";
						break;
					case 7:
						echo "<span style='font-weight:bold;color:#C13333;'>(日)</span>";
						break;
					default:
						break;
				}
				echo "</td>";
				if(isset($list[date('Ymd',$d)])){
					if(isset($list[date('Ymd',$d)]['money'])){
						$totalmoney=floatval($totalmoney)+floatval($list[date('Ymd',$d)]['money']);
						$totalcash=floatval($totalcash)+floatval(floatval($list[date('Ymd',$d)]['cash'])-floatval($list[date('Ymd',$d)]['cashcomm']));
						$totalcashcomm=floatval($totalcashcomm)+floatval($list[date('Ymd',$d)]['cashcomm']);
						$totalother=floatval($totalother)+floatval($list[date('Ymd',$d)]['other']);
						$totalperson=floatval($totalperson)+floatval($list[date('Ymd',$d)]['person']);
						echo '<td style="text-align:right;">'.number_format($list[date('Ymd',$d)]['money']).'</td>';
						echo '<td style="text-align:right;">'.number_format(floatval($list[date('Ymd',$d)]['cash'])-floatval($list[date('Ymd',$d)]['cashcomm'])).'</td>';
						echo '<td style="text-align:right;">'.number_format($list[date('Ymd',$d)]['cashcomm']).'</td>';
						echo '<td style="text-align:right;">'.number_format($list[date('Ymd',$d)]['other']).'</td>';
						echo '<td style="text-align:right;">'.number_format($list[date('Ymd',$d)]['person']).'</td>';
					}
					else{
						echo '<td style="text-align:right;">0</td><td style="text-align:right;">0</td><td style="text-align:right;">0</td><td style="text-align:right;">0</td><td style="text-align:right;">0</td>';
					}
					if(isset($list[date('Ymd',$d)]['outpat'])){
						$totaloutpay=floatval($totaloutpay)+floatval($list[date('Ymd',$d)]['outpat']);
						echo '<td style="text-align:right;">'.number_format($list[date('Ymd',$d)]['outpat']).'</td>';
						$total=floatval($total)+floatval((floatval($list[date('Ymd',$d)]['money'])+floatval($list[date('Ymd',$d)]['cash'])+floatval($list[date('Ymd',$d)]['other'])+floatval($list[date('Ymd',$d)]['outpat'])));
						echo '<td style="text-align:right;">'.number_format((floatval($list[date('Ymd',$d)]['money'])+floatval($list[date('Ymd',$d)]['cash'])+floatval($list[date('Ymd',$d)]['other'])+floatval($list[date('Ymd',$d)]['outpat']))).'</td>';
					}
					else{
						echo '<td style="text-align:right;">0</td>';
						$total=floatval($total)+floatval((floatval($list[date('Ymd',$d)]['money'])+floatval($list[date('Ymd',$d)]['cash'])+floatval($list[date('Ymd',$d)]['other'])));
						echo '<td style="text-align:right;">'.number_format((floatval($list[date('Ymd',$d)]['money'])+floatval($list[date('Ymd',$d)]['cash'])+floatval($list[date('Ymd',$d)]['other']))).'</td>';
					}
				}
				else{
					echo '<td style="text-align:right;">0</td><td style="text-align:right;">0</td><td style="text-align:right;">0</td><td style="text-align:right;">0</td><td style="text-align:right;">0</td><td style="text-align:right;">0</td><td style="text-align:right;">0</td>';
				}
				echo "</tr>";
			}
			echo '<tr><td>合計</td><td style="text-align:right;">'.number_format($totalmoney).'</td><td style="text-align:right;">'.number_format($totalcash).'</td><td style="text-align:right;">'.number_format($totalcashcomm).'</td><td style="text-align:right;">'.number_format($totalother).'</td><td style="text-align:right;">'.number_format($totalperson).'</td><td style="text-align:right;">'.number_format($totaloutpay).'</td><td style="text-align:right;">'.number_format($total).'</td></tr>';
			echo '</table></div>';
		}
	}
}
else{
}
?>