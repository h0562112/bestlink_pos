<?php
//print_r($_POST);

$list=array(array(iconv('utf-8','big5','統編'),iconv('utf-8','big5','抬頭'),iconv('utf-8','big5','總計'),iconv('utf-8','big5','產品編號'),iconv('utf-8','big5','含稅價')));

$itemindex=1;
for($b=0;$b<sizeof($_POST['tempban']);$b++){
	$temp=array();
	array_push($temp,$_POST['tempban'][$b],iconv('utf-8','big5',$_POST['tempbanname'][$b]),$_POST['subtotal'][$b]);
	for(;$itemindex<sizeof($_POST['no']);$itemindex++){
		if($_POST['no'][$itemindex]!='start'){
			$tempdata=preg_split('/-/',$_POST['no'][$itemindex]);
			$_POST['no'][$itemindex]=$tempdata[0];
			if($_POST['no'][$itemindex]!=''){
				array_push($temp,$_POST['no'][$itemindex],$_POST['money'][$itemindex]);
			}
			else{
			}
		}
		else{
			$itemindex++;
			break;
		}
	}
	array_push($list,$temp);
}

//print_r($list);
$file = fopen('./mycsv.csv', 'w');
foreach ($list as $fields) {
    fputcsv($file, $fields);
}
fclose($file);
?>