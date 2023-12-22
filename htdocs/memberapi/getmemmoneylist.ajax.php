<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../tool/dbTool.inc.php';
$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
$sql='SELECT * FROM memsalelist'.substr($_POST['bizdate'],0,6).' WHERE memno="'.$_POST['memno'].'" AND company="'.$_POST['company'].'" AND dep="'.$_POST['dep'].'" AND consecnumber="paymemmoney" ORDER BY datetime DESC';
$res=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
foreach($res as $r){
	echo "<div class='moneylist' style='overflow:hidden;";
	if($r['state']==0){
		echo 'color:#ff0000;';
	}
	else{
	}
	echo "'>";
		echo "<div style='width:87.97px;line-height:46px;text-align:center;'><input type='hidden' name='bizdate' value='".$r['bizdate']."'>".$r['bizdate']."</div>";
		echo "<div style='width:109.97px;text-align:center;'><input type='hidden' name='datetime' value='".$r['datetime']."'>".substr($r['datetime'],0,4).'/'.substr($r['datetime'],4,2).'/'.substr($r['datetime'],6,2).'<br>'.substr($r['datetime'],8,2).':'.substr($r['datetime'],10,2).':'.substr($r['datetime'],12,2)."</div>";
		echo "<div style='width:70px;line-height:46px;'><input type='hidden' name='initmoney' value='".$r['initmoney']."'>".$r['initmoney']."</div>";
		echo "<div style='width:70px;line-height:46px;'><input type='hidden' name='remainingmoney' value='".$r['remainingmoney']."'>".$r['remainingmoney']."</div>";
		echo "<div style='width:70px;line-height:46px;'><input type='hidden' name='money' value='".$r['money']."'>".$r['money']."</div>";
		echo "<div style='width:70px;line-height:46px;'><input type='hidden' name='membermoney' value='".$r['membermoney']."'>".$r['membermoney']."</div>";
		echo "<div style='width:50px;line-height:46px;'><input type='hidden' name='state' value='".$r['state']."'>";
		if(floatval($r['money'])<0){
			echo '沖銷';
		}
		else{
			if($r['state']==0){
				echo '作廢';
			}
			else{
			}
		}
		echo "</div>";
	echo "</div>";
}
?>