<?php
date_default_timezone_set('Asia/Taipei');

require __DIR__ . '../../../tool/escpos/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfiles\SimpleCapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

$setup=parse_ini_file('../../../database/setup.ini',true);

/* Most printers are open on port 9100, so you just need to know the IP 
 * address of your receipt printer, and then fsockopen() it on that port.
 */
try {
	if($setup['printer']['type']=='1'){
		$connector = new WindowsPrintConnector($setup['printer']['path']);
	}
	else{
		$connector = new NetworkPrintConnector($setup['printer']['path'], 9100);
	}
    
    
    /* Print a "Hello world" receipt" */
    $printer = new Printer($connector);
	$printer -> setJustification(Printer::JUSTIFY_CENTER);
	
	$_POST['date']=date('Ymd');
	$_POST['time']=date('His');
	$printer -> setTextSize(1, 1);
    $printer -> text(date('Y/m/d H:i:s')."\n");
	$printer -> setTextSize(1, 1);
	$printer -> text("\n",1);
	$printer -> setTextSize(1, 1);
    $printer -> text($_POST['no'].iconv("UTF-8", "big5"," 卡號")."\n");	
	
    $printer -> cut();
	$printer -> pulse(0);
    
    /* Close printer */
    $printer -> close();
	/*$file=fopen('../../print/noread/report.txt','w');
	fclose($file);*/
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}
?>