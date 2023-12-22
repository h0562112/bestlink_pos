<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','papermanagement','paperadmin','1qaz2wsx','utf-8','mysql');
$sql='SELECT deptname,dept,companyname FROM userlogin WHERE company="'.$_POST['story'].'" AND function LIKE "%ourpos%" AND deptname!="總部"';
$depdata=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'sqlite');
foreach($depdata as $k=>$v){
	echo '<div class="dep" style="width:calc(100% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="dep" value="'.$v['dept'].'"><input type="hidden" name="depaddress" value="';
	if(file_exists('../../../management/menudata/'.$_POST['story'].'/'.$v['dept'].'/setup.ini')){
		$setup=parse_ini_file('../../../management/menudata/'.$_POST['story'].'/'.$v['dept'].'/setup.ini');
		echo $setup['basic']['address'];
	}
	else{
	}
	echo '"><span id="depname">'.$v['companyname'].$v['deptname'].'</span></div>';
}
?>