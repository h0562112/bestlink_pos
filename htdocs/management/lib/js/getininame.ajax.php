<?php
$temp=array("msytitle"=>"系統訊息","punchtitle"=>"查詢打卡時間","checkitem"=>"是否確認刪除產品？","itemhint"=>"請至少選擇類別與填入產品名稱(主語言部分)。","save"=>"儲存","cancel"=>"取消","create"=>"新增","newunittitle"=>"新單位名稱","newstrawnametitle"=>"新吸管名稱","newclasstitle"=>"新科目名稱");
if(file_exists('../../lan/'.$_POST['file'].$_POST['lan'].'.ini')){
	$interface=parse_ini_file('../../lan/'.$_POST['file'].$_POST['lan'].'.ini',true);
	if(isset($interface['name'][$_POST['name']])){
		echo $interface['name'][$_POST['name']];
	}
	else{
		echo $temp[$_POST['name']];
	}
}
else{
	echo $temp[$_POST['name']];
}
?>