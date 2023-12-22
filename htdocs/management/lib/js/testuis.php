<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once '../../../tool/phpexcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");
$p=chr(65);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'COD_CUST');		 //客戶代號
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'NUM_PO');		 //訂單編號
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'DAT_PO');		 //訂單日期
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'DAT_RFF');		 //接單日期
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'DAT_DELS');		 //預定交期
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'DAT_KEYIN');		 //登錄日期
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'AMT_PO');		 //訂單交易金額
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'SER_PO');		 //版本	
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'POS_DEL');		 //交貨地點
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'COD_PAYM');		 //付款條件
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'TAX_TYPE');		 //稅別	
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'COD_DOLA');		 //幣別	
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'DPT_CTL');		 //控管單位
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'COD_DPT');		 //接單部門 
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'RFF_RATE');		 //接單匯率 
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'TYP_POHD');		 //訂單類別 
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'STS_ECPO');		 //狀況	
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'ID_INVMM');		 //POS機台	
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.'1', 'COD_EMPIN');		 //登錄人員代號
// Rename worksheet
$objPHPExcel->setActiveSheetIndex(0)->setTitle('EC_POHD');

$p=chr(65);
$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'COD_CUST');		 //客戶代號	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'NUM_PO');		 //訂單編號	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'DAT_DELS');		 //預定交期	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'NUM_LINE');		 //訂單項次	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'COD_ITEM');		 //內部料號	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'COD_ITEMO');		 //客戶料號	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'QTY_REQ');		 //內部數量	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'QTY_POC');		 //外部數量	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'COD_UNIT');		 //內部單位	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'UNT_POC');		 //外部單位 	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'MNY_UNIT');		 //內部單價	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'PRS_POC');		 //外部單價	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'MNY_DSC');		 //折讓 (單筆) 	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'MNY_AMT');		 //小計		 
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'AMT_TAX');		 //POS發票稅額 /整數
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'DPT_CTL');		 //控管單位	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'CLS_ARMM');		 //應收類別	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'COD_LOC');		 //出貨儲位	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'TYP_POLN');		 //訂購類別	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'TAX_TYPE');		 //POS發票稅別	
$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.'1', 'STS_ECPO');		 //狀況		 
// Rename worksheet
$objPHPExcel->setActiveSheetIndex(1)->setTitle('EC_POLN');

for($i=0;$i<10;$i++){
	$p=chr(65);
	for($j=0;$j<19;$j++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $p++.($i+2), $j);
	}
}

for($i=0;$i<10;$i++){
	$p=chr(65);
	for($j=0;$j<21;$j++){
		$objPHPExcel->setActiveSheetIndex(1)->setCellValue( $p++.($i+2), $j);
	}
}


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 95 file
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('./ttt.xls');
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
?>