<?php
require_once '../../../tool/PHPWord.php';
//include '../tool/dbTool.inc.php';
$content=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);
//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');

$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$setup=parse_ini_file('../../../database/setup.ini',true);
$menu=parse_ini_file('../../../database/'.$setup['basic']['company'].'-menu.ini',true);
if(isset($_POST['looptype'])){
	$looptype=$_POST['looptype'];
}
else{
	$looptype=$content['init']['listprint'];
}
$no=$_POST['typename'].' '.$machinedata['basic']['saleno'];
$index=1;
for($i=0;$i<sizeof($_POST['order']);$i++){
	for($j=0;$j<$_POST['number'][$i];$j++){
		$PHPWord = new PHPWord();
		$document = $PHPWord->loadTemplate('../../../template/tag.docx');
		$document->setValue('no', $no);
		$document->setValue('size', $_POST['mname1'][$i]);
		$document->setValue('number',$index.'/'.$_POST['totalnumber']);
		if(isset($_POST['name'][$i])){
			$document->setValue('name',$_POST['name'][$i]);
		}
		else{
			$document->setValue('name',' ');
		}
		if(isset($_POST['name2'][$i])){
			$document->setValue('name2',$_POST['name2'][$i]);
		}
		else{
			$document->setValue('name2',' ');
		}
		$temp=preg_split('/,/',$_POST['taste1name'][$i]);
		$tt='';
		for($g=0;$g<sizeof($temp);$g++){
			$aa=preg_split('/\//',$temp[$g]);
			if(strlen($tt)==0){
				$tt=$aa[0];
			}
			else{
				$tt=$tt.','.$aa[0];
			}
		}
		$document->setValue('taste',$tt);
		$document->setValue('time',substr(date('Ymd H:i'),2));
		$document->setValue('hint',$print['item']['taghint']);
		$document->setValue('money',$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit']);
		$filename=date("Ymd").'_'.$i.'_'.$j;
		if($print['item']['tag']=='1'&&$menu[$_POST['no'][$i]]['printtype']!=''&&$pti[$menu[$_POST['no'][$i]]['printtype']]['tag'.$_POST['listtype']]=='1'&&($looptype=='1'||$looptype=='4')){
			$document->save("../../../print/noread/tag_".$machinedata['basic']['consecnumber']."_".$filename.".docx");
		}
		else{
			if($print['item']['tag']=='0'){//假設沒有貼紙機，所以設備初始設定不出貼紙，則不產生檔案
			}
			else{
				$document->save("../../../print/read/tag_".$machinedata['basic']['consecnumber']."_".$filename.".docx");
			}
		}
		$index++;
	}
}
?>