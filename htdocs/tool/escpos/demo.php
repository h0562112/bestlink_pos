<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
//require_once './PHPWord.php';
date_default_timezone_set('Asia/Taipei');
//$class=parse_ini_file('../../database/class.ini',true);
//$init=parse_ini_file('../../database/initsetting.ini',true);
/* Change to the correct path if you copy this example! */
require __DIR__ . '/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfiles\SimpleCapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

/* Most printers are open on port 9100, so you just need to know the IP 
 * address of your receipt printer, and then fsockopen() it on that port.
 */
try {
	/*if(!isset($_POST['machine'])||$_POST['machine']==''){
		if($init['init']['type']=='1'){
			$connector = new WindowsPrintConnector($init['init']['printname']);
		}
		else{
			$connector = new NetworkPrintConnector($init['init']['printname'], 9100);
		}
	}
	else{
		if($init[$_POST['machine']]['type']=='1'){
			$connector = new WindowsPrintConnector($init[$_POST['machine']]['printname']);
		}
		else{
			$connector = new NetworkPrintConnector($init[$_POST['machine']]['printname'], 9100);
		}
	}*/
	$connector = new NetworkPrintConnector('192.168.88.254', 9100);
    
    
    /* Print a "Hello world" receipt" */
    $printer = new Printer($connector);
	$printer -> setJustification(Printer::JUSTIFY_CENTER);
	/*foreach($class as $index=>$value){
		if(!isset($value['start'])){
		}
		else{
			if($value['cross']=='0'){
				if(strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($value['start'],0,2).':'.substr($value['start'],2,2))<=strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2))&&strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($value['end'],0,2).':'.substr($value['end'],2,2))>strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2))){
					$printer -> setTextSize(2, 2);
					$printer -> text(iconv("UTF-8", "big5",$class[$_POST['machine']]['depname'].$value['name']),1);
					$printer -> setTextSize(3, 3);
					$printer -> text(iconv("UTF-8", "big5",' '.$_POST['seq'])."\n",1);
					$printer -> setTextSize(1, 1);
					$printer -> text("\n",1);
					//$document->setValue('type',$value['name'].'餐卷'.$_POST['seq']);
					break;
				}
				else{
				}
			}
			else{
				if(strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($value['start'],0,2).':'.substr($value['start'],2,2))<=strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2))&&strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($value['end'],0,2).':'.substr($value['end'],2,2))>strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2))){
					$printer -> setTextSize(2, 2);
					$printer -> text(iconv("UTF-8", "big5",$class[$_POST['machine']]['depname'].$value['name']),1);
					$printer -> setTextSize(4, 4);
					$printer -> text(iconv("UTF-8", "big5",' '.$_POST['seq'])."\n",1);
					$printer -> setTextSize(1, 1);
					$printer -> text("\n",1);
					//$document->setValue('type',$value['name'].'餐卷'.$_POST['seq']);
					break;
				}
				else{
				}
			}
		}
	}*/
	$_POST['date']=date('Ymd');
	$_POST['time']=date('His');
	$_POST['no']='1';
	$printer -> setTextSize(1, 1);
    $printer -> text(substr($_POST['date'],0,4).'/'.substr($_POST['date'],4,2).'/'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2).':'.substr($_POST['time'],4,2)."\n");
	$printer -> setTextSize(1, 1);
	$printer -> text("\n",1);
	$printer -> setTextSize(1, 1);
    $printer -> text($_POST['no'].iconv("UTF-8", "big5"," 卡號")."\n");

	//$document->setValue('datetime',substr($_POST['date'],0,4).'/'.substr($_POST['date'],4,2).'/'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2).':'.substr($_POST['time'],4,2));
	//$document->setValue('no',$_POST['no']);
	
	
    $printer -> cut();
	$printer -> pulse(0);
    
    /* Close printer */
    $printer -> close();
	/*$file=fopen('../../print/noread/report.txt','w');
	fclose($file);*/
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}

/*$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate('../../print/template.docx');
foreach($class as $index=>$value){
	if($index=='machine'){
	}
	else{
		if($value['cross']=='0'){
			if(strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($value['start'],0,2).':'.substr($value['start'],2,2))<=strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2))&&strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($value['end'],0,2).':'.substr($value['end'],2,2))>strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2))){
				$document->setValue('type',$value['name'].'餐卷'.$_POST['seq']);
				break;
			}
			else{
			}
		}
		else{
			if(strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($value['start'],0,2).':'.substr($value['start'],2,2))<=strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2))&&strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($value['end'],0,2).':'.substr($value['end'],2,2))>strtotime(substr($_POST['date'],0,4).'-'.substr($_POST['date'],4,2).'-'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2))){
				$document->setValue('type',$value['name'].'餐卷'.$_POST['seq']);
				break;
			}
			else{
			}
		}
	}
}
$document->setValue('datetime',substr($_POST['date'],0,4).'/'.substr($_POST['date'],4,2).'/'.substr($_POST['date'],6,2).' '.substr($_POST['time'],0,2).':'.substr($_POST['time'],2,2).':'.substr($_POST['time'],4,2));
$document->setValue('no',$_POST['no']);
$filename=date("YmdHis");
$document->save("../../print/read/".$filename."_tag.docx");
$file=fopen("../../print/noread/".$filename."_tag.prt",'w');
fclose($file);*/
?>