<?php
include_once '../../../tool/dbTool.inc.php';

$_POST['bizdate']=preg_replace('/-/','',$_POST['bizdate']);

if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machine']])){
		$invmachine=$dbmapping['map'][$_POST['machine']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}

$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND TERMINALNUMBER="'.$invmachine.'" AND (DTLMODE="4" OR DTLMODE="3") AND DTLTYPE="1" AND DTLFUNC="01"';
$list=sqlquery($conn,$sql,'sqlite');
$sql='SELECT SUM(AMT) AS AMT FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND TERMINALNUMBER="'.$invmachine.'" AND (DTLMODE="4" OR DTLMODE="3") AND DTLTYPE="1" AND DTLFUNC="01" AND AMT>0';
$in=sqlquery($conn,$sql,'sqlite');
$sql='SELECT SUM(AMT) AS AMT FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND TERMINALNUMBER="'.$invmachine.'" AND (DTLMODE="4" OR DTLMODE="3") AND DTLTYPE="1" AND DTLFUNC="01" AND AMT<0';
$out=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

if(isset($in[0]['AMT'])){
	echo '<input type="hidden" data-name="in" value="'.$in[0]['AMT'].'">';
}
else{
	echo '<input type="hidden" data-name="in" value="0">';
}
if(isset($out[0]['AMT'])){
	echo '<input type="hidden" data-name="out" value="'.$out[0]['AMT'].'">';
}
else{
	echo '<input type="hidden" data-name="out" value="0">';
}
foreach($list as $l){
	echo '<div class="listitems" style="width:max-content;padding:10px 0;border-bottom:1px solid #898989;overflow:hidden;">';
	
	echo '<div style="width:90px;padding:0 5px;float:left;min-height:1px;">'.$l['BIZDATE'].'</div>';
	echo '<div style="width:45px;padding:0 5px;float:left;min-height:1px;">'.$l['ZCOUNTER'].'</div>';
	echo '<div style="width:90px;padding:0 5px;float:left;min-height:1px;">'.$l['AMT'].'</div>';
	echo '<div style="width:90px;padding:0 5px;float:left;min-height:1px;">'.$l['ITEMDEPTNAME'].'</div>';
	echo '<div style="width:90px;padding:0 5px;float:left;min-height:1px;">'.$l['ITEMNAME'].'</div>';

	echo '</div>';
}
?>