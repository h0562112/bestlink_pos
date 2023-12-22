<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/date.inc.php';

$initsetting=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini',true);
$startdate=preg_replace('/-/','',$_POST['startdate']);
$enddate=preg_replace('/-/','',$_POST['enddate']);

$totalMon=getMon($_POST['startdate'],$_POST['enddate']);

$times=0;

//print_r($_POST);
for($mon=0;$mon<=$totalMon;$mon++){
	if(isset($_POST['memno'])&&$_POST['memno']!=''){//會員編號
		if(!isset($initsetting['init']['onlinemember'])||$initsetting['init']['onlinemember']=='1'){//網路會員
			$conn=sqlconnect('localhost',$_POST['company'],'posmana','1qaz2wsx','utf-8','mysql');
			$sql='SHOW TABLES LIKE "memsalelist'.date("Ym",strtotime(substr($startdate,0,6).'01 +'.$mon.' month')).'"';
			$exists=sqlquery($conn,$sql,'mysql');
			if(isset($exists[0])){//查詢月份有銷售資料
				$sql='SELECT * FROM memsalelist'.date("Ym",strtotime(substr($startdate,0,6).'01 +'.$mon.' month')).' WHERE memno="'.$_POST['memno'].'" AND dep="'.$_POST['dep'].'" AND bizdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND requery="success"';
				if(isset($_POST['searchtype'])&&$_POST['searchtype']=='1'){//購買紀錄
					$sql.=' AND consecnumber!="paymemmoney"';
				}
				else if(isset($_POST['searchtype'])&&$_POST['searchtype']=='2'){//儲值紀錄
					$sql.=' AND consecnumber="paymemmoney"';
				}
				else{//以上皆有
				}
				$sql.=' ORDER BY datetime ASC,memno ASC';
				$res=sqlquery($conn,$sql,'mysql');
			}
			else{
			}
			sqlclose($conn,'mysql');
		}
		else{//本地會員
		}

		if(isset($res[0])){
			//print_r($res);
			echo '<table><caption style="font-weight:bold;padding:10px 0 0 0;">'.date("Y/m",strtotime(substr($startdate,0,6).'01 +'.$mon.' month')).'</caption>';
			echo '<tr><th style="padding:0 10px;">營業日</th><th style="padding:0 10px;">帳單號</th><th style="padding:0 10px;">支付金額</th><th style="padding:0 10px;">支付點數</th><th style="padding:0 10px;">支付儲值金</th><th style="padding:0 10px;">剩餘點數</th><th style="padding:0 10px;">剩餘儲值金</th><th style="padding:0 10px;">帳單時間</th></tr>';
			foreach($res as $d){
				echo '<tr>';
				echo '<td style="padding:0 10px;">'.$d['bizdate'].'</td>';
				echo '<td style="text-align:center;padding:0 10px;">';
				if($d['consecnumber']=='paymemmoney'){
					echo '儲值';
				}
				else{
					echo str_pad($d['consecnumber'],6,'0',STR_PAD_LEFT);
				}
				echo '</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['money'].'</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['memberpoint'].'</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['membermoney'].'</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['remainingpoint'].'</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['remainingmoney'].'</td>';
				echo '<td style="padding:0 10px;">'.$d['datetime'].'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		else{
		}
	}
	else{
		if(!isset($initsetting['init']['onlinemember'])||$initsetting['init']['onlinemember']=='1'){//網路會員
			$conn=sqlconnect('localhost',$_POST['company'],'posmana','1qaz2wsx','utf-8','mysql');
			$sql='SHOW TABLES LIKE "memsalelist'.date("Ym",strtotime(substr($startdate,0,6).'01 +'.$mon.' month')).'"';
			$exists=sqlquery($conn,$sql,'mysql');
			if(isset($exists[0])){//查詢月份有銷售資料
				$sql='SELECT DISTINCT memno FROM memsalelist'.date("Ym",strtotime(substr($startdate,0,6).'01 +'.$mon.' month')).' WHERE dep="'.$_POST['dep'].'" AND bizdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"';
				$memno=sqlquery($conn,$sql,'mysql');
				if(isset($memno[0]['memno'])){
					$sql='SELECT memno,cardno,name FROM member WHERE memno IN ("'.(implode('","',array_column($memno,'memno'))).'")';
					$tempmemname=sqlquery($conn,$sql,'mysql');
					foreach($tempmemname as $tm){
						$memname[$tm['memno']]['cardno']=$tm['cardno'];
						$memname[$tm['memno']]['name']=$tm['name'];
					}
				}
				else{
				}
				$sql='SELECT * FROM memsalelist'.date("Ym",strtotime(substr($startdate,0,6).'01 +'.$mon.' month')).' WHERE dep="'.$_POST['dep'].'" AND bizdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND requery="success"';
				if(isset($_POST['searchtype'])&&$_POST['searchtype']=='1'){//購買紀錄
					$sql.=' AND consecnumber!="paymemmoney"';
				}
				else if(isset($_POST['searchtype'])&&$_POST['searchtype']=='2'){//儲值紀錄
					$sql.=' AND consecnumber="paymemmoney"';
				}
				else{//以上皆有
				}
				$sql.=' ORDER BY datetime ASC,memno ASC';
				//echo $sql;
				$res=sqlquery($conn,$sql,'mysql');
			}
			else{
			}
			sqlclose($conn,'mysql');
		}
		else{//本地會員
		}

		if(isset($res[0]['memno'])){
			//print_r($res);
			echo '<table><caption style="font-weight:bold;padding:10px 0 0 0;">'.date("Y/m",strtotime(substr($startdate,0,6).'01 +'.$mon.' month')).'</caption>';
			echo '<tr><th style="padding:0 10px;">營業日</th><th style="padding:0 10px;">會員</th><th style="padding:0 10px;">帳單號</th><th style="padding:0 10px;">支付金額</th><th style="padding:0 10px;">支付點數</th><th style="padding:0 10px;">支付儲值金</th><th style="padding:0 10px;">剩餘點數</th><th style="padding:0 10px;">剩餘儲值金</th><th style="padding:0 10px;">帳單時間</th></tr>';
			foreach($res as $d){
				echo '<tr>';
				echo '<td style="padding:0 10px;">'.$d['bizdate'].'</td>';
				if(isset($memname)&&isset($memname[$d['memno']])){
					echo '<td style="padding:0 10px;">'.$memname[$d['memno']]['cardno'].'-'.$memname[$d['memno']]['name'].'</td>';
				}
				else{
					echo '<td style="padding:0 10px;">'.$d['memno'].'</td>';
				}
				echo '<td style="text-align:center;padding:0 10px;">';
				if($d['consecnumber']=='paymemmoney'){
					echo '儲值';
				}
				else{
					echo str_pad($d['consecnumber'],6,'0',STR_PAD_LEFT);
				}
				echo '</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['money'].'</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['memberpoint'].'</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['membermoney'].'</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['remainingpoint'].'</td>';
				echo '<td style="text-align:right;padding:0 10px;">'.$d['remainingmoney'].'</td>';
				echo '<td style="padding:0 10px;">'.$d['datetime'].'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		else{
			echo '查無資料。';
		}
	}
}
?>