<?php
include_once '../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT * FROM tempCST012 WHERE CONSECNUMBER="'.$_POST['consecnumber'].'" AND BIZDATE="'.$_POST['bizdate'].'" ORDER BY LINENUMBER ASC';
$list=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

$usepoint['memberpoint']=0;
for($i=0;$i<sizeof($list);$i=$i+2){
	if($list[$i]['ITEMCODE']=='list'){
		continue;
	}
	else if($list[$i]['ITEMCODE']=='autodis'){
		$i--;
		continue;
	}
	else{
		if($list[$i+1]['ITEMCODE']=='item'&&$list[$i+1]['ITEMGRPCODE']=='memberpoint'){
			$usepoint['memberpoint']=intval($usepoint['memberpoint'])+intval($list[$i+1]['TAXCODE5'])+intval($list[$i+1]['TAXCODE2'])*intval($list[$i+1]['TAXCODE3']);//扣除點數兌換所使用到的點數
		}
		else{
		}
	}
}

echo json_encode($usepoint);
?>