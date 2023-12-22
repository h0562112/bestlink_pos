<?php
include_once "./nidin_api_inc.php";

$setup=parse_ini_file('../../../../database/setup.ini',true);
if(file_exists('../../../../database/initsetting.ini')){
	$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
	if(isset($initsetting['nidin'])&&isset($initsetting['nidin']['usenidin'])&&$initsetting['nidin']['usenidin']=='1'&&isset($initsetting['nidin']['autoaccept'])&&$initsetting['nidin']['autoaccept']=='1'){//2022/3/16 直接將nidin訂單下載至暫結並由蜂鳴器出聲提醒
		$f=fopen('../../../../print/noread/beeper.txt','w');
		fclose($f);
	}
	else{
	}
}

echo json_encode(Accept($setup['nidin']['url'],"post",$_POST["Token"],$_POST["User"],$_POST['orderid'],$_POST['consecnumber'],$_POST['machine'],$_POST['saleno']));
?>