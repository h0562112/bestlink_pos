<?php
session_start();
date_default_timezone_set('Asia/Taipei');
include_once '../../../tool/dbTool.inc.php';

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

//define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once '../../../tool/phpexcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$EC_POHD = new PHPExcel();
$EC_POHD = PHPExcel_IOFactory :: load ('./template/template.xls');

$EC_POLN = new PHPExcel();
$EC_POLN = PHPExcel_IOFactory :: load ('./template/template.xls');

$UAINVM = new PHPExcel();
$UAINVM = PHPExcel_IOFactory :: load ('./template/template.xls');

/*$UAINVD = new PHPExcel();
$UAINVD = PHPExcel_IOFactory :: load ('./template/template.xls');*/

$start=preg_replace('/-/','',$_POST['startdate']);
$end=preg_replace('/-/','',$_POST['enddate']);

$list=array();
if(strtotime(date('Ymd',strtotime($end)))>strtotime(date('Ymd'))){
	$ENDDATE=strtotime(date('Ymd'));
}
else{
	$ENDDATE=strtotime(date('Ymd',strtotime($end)));
}
if(is_dir('../../../doc/')){
}
else{
	mkdir('../../../doc');
}
$filepath='../doc/'.date('Ymd');
if(is_dir('../../'.$filepath.'/')){
}
else{
	mkdir('../../'.$filepath);
}
//銷售主檔
$fEC_POHD=$filepath.'/'.$_SESSION['company'].'-'.date('YmdHis').'-EC_POHD.xls';
$fEC_POLN=$filepath.'/'.$_SESSION['company'].'-'.date('YmdHis').'-EC_POLN.xls';
//發票主檔
$fUAINVM=$filepath.'/'.$_SESSION['company'].'-'.date('YmdHis').'-UAINVM.xls';
$fUAINVD=$filepath.'/'.$_SESSION['company'].'-'.date('YmdHis').'-UAINVD.xls';

if($_SESSION['DB']==''){
	$floorspan=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/floorspend.ini',true);
	$otherpay=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/otherpay.ini',true);
	$setup=parse_ini_file('../../../menudata/'.$_SESSION['company'].'/'.$_POST['dbname'].'/setup.ini',true);
}
else{
	$floorspan=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/floorspend.ini',true);
	$otherpay=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/otherpay.ini',true);
	$setup=parse_ini_file('../../../menudata/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/setup.ini',true);
}
if(isset($setup['erpno'])){
	if(isset($setup['erpno']['codecust'])){
		$codecust=$setup['erpno']['codecust'];
	}
	else{
		$codecust="";
	}
	if(isset($setup['erpno']['idinvmm'])){
		$idinvmm=$setup['erpno']['idinvmm'];
	}
	else{
		$idinvmm="";
	}
	if(isset($setup['erpno']['codedep'])){
		$codedep=$setup['erpno']['codedep'];
	}
	else{
		$codedep="";
	}
}
else{
	$codecust="";
	$idinvmm="";
	$codedep="";
}
if(file_exists('../../../ourpos/'.$_SESSION['company'].'/buttons-'.$_POST['lan'].'.ini')){
	$saletype=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/buttons-'.$_POST['lan'].'.ini',true);
}
else if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
	$saletype=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
}
else{
	$saletype=parse_ini_file('../../lan/interfaceTW.ini',true);
}
if($_SESSION['DB']==''){
	$initsetting=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/initsetting.ini',true);
	if($initsetting['init']['accounting']=='1'){//主機為主
		$mapping[0]='m1';
	}
	else{
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/mapping.ini');
		$mapping=array_unique(array_values($temp));
	}
}
else{
	$initsetting=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
	if($initsetting['init']['accounting']=='1'){//主機為主
		$mapping[0]='m1';
	}
	else{
		$temp=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/mapping.ini');
		$mapping=array_unique(array_values($temp));
	}
}
if(isset($_POST['startdate'])){
	$personsetting='';
	for($i=1;$i<4;$i++){
		if(isset($floorspan['person'.$i]['name'])&&$floorspan['person'.$i]['name']!=''){
			$personsetting.=$floorspan['person'.$i]['name'].',';
		}
		else{
		}
	}
	$paystring='';
	foreach($otherpay as $index=>$item){
		if($index=='pay'){
		}
		else{
			$paystring.=iconv("UTF-8","Big5",$item['name']).',';
		}
	}
	$check=0;

	//fwrite($f,'營業日,班別,類型,結帳時間,帳單編號,桌號,帳單金額,服務費,現金,信用卡,其他支付,電子發票,'.$personsetting.'序,品項(備註),單價,數量,小計,帳單備註,其他支付方式,'.$paystring.PHP_EOL);
	// Add some data

	
	$EC_POHD->setActiveSheetIndex(0)
    ->getStyle('A:S')
    ->getNumberFormat()
    ->setFormatCode(
        PHPExcel_Style_NumberFormat::FORMAT_TEXT
    );
	
	$EC_POHD->setActiveSheetIndex(0)
    ->getStyle('A:S')
	->getFont()
	->setName( 'Arial');
	$EC_POHD->setActiveSheetIndex(0)
    ->getStyle('A:S')
	->getFont()
	->setSize(10);

	$EC_POLN->setActiveSheetIndex(0)
    ->getStyle('A:U')
    ->getNumberFormat()
    ->setFormatCode(
        PHPExcel_Style_NumberFormat::FORMAT_TEXT
    );
	$EC_POLN->setActiveSheetIndex(0)
    ->getStyle('A:U')
	->getFont()
	->setName( 'Arial');
	$EC_POLN->setActiveSheetIndex(0)
    ->getStyle('A:U')
	->getFont()
	->setSize(10);

	$p=chr(65);

	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //客戶代號
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_CUST');		 //客戶代號
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂單編號
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NUM_PO');		 //訂單編號
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂單日期
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'DAT_PO');		 //訂單日期
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //接單日期
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'DAT_RFF');		 //接單日期
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //預定交期
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'DAT_DELS');		 //預定交期
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //登錄日期
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'DAT_KEYIN');		 //登錄日期
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂單交易金額
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'AMT_PO');		 //訂單交易金額
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //版本	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'SER_PO');		 //版本	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //交貨地點
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'POS_DEL');		 //交貨地點
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //付款條件
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_PAYM');		 //付款條件
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //稅別	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TAX_TYPE');		 //稅別	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //幣別	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_DOLA');		 //幣別	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //控管單位
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'DPT_CTL');		 //控管單位
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //接單部門 
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_DPT');		 //接單部門 
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //接單匯率 
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'RFF_RATE');		 //接單匯率 
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂單類別 
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TYP_POHD');		 //訂單類別 
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //狀況	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'STS_ECPO');		 //狀況	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //POS機台
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'ID_INVMM');		 //POS機台	
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //登錄人員代號
	$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_EMPIN');		 //登錄人員代號

	// Rename worksheet
	$EC_POHD->getActiveSheet()->setTitle('EC_POHD');

	$p=chr(65);
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);	 //客戶代號	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_CUST');		 //客戶代號	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂單編號	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NUM_PO');		 //訂單編號	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //預定交期	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'DAT_DELS');		 //預定交期	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂單項次	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NUM_LINE');		 //訂單項次	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //內部料號	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_ITEM');		 //內部料號	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //客戶料號	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_ITEMO');		 //客戶料號	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //內部數量	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'QTY_REQ');		 //內部數量	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //外部數量	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'QTY_POC');		 //外部數量	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //內部單位	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_UNIT');		 //內部單位	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //外部單位
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'UNT_POC');		 //外部單位 	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //內部單價
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'MNY_UNIT');		 //內部單價	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //外部單價	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'PRS_POC');		 //外部單價	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //折讓 (單筆)
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'MNY_DSC');		 //折讓 (單筆) 	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //小計	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'MNY_AMT');		 //小計		 
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //POS發票稅額 /整數
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'AMT_TAX');		 //POS發票稅額 /整數
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //控管單位	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'DPT_CTL');		 //控管單位	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //應收類別	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'CLS_ARMM');		 //應收類別	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //出貨儲位	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_LOC');		 //出貨儲位	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂購類別
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TYP_POLN');		 //訂購類別	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //POS發票稅別	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TAX_TYPE');		 //POS發票稅別	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //狀況	
	$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'STS_ECPO');		 //狀況		 

	// Rename worksheet
	$EC_POLN->getActiveSheet()->setTitle('EC_POLN');

	$UAINVM->setActiveSheetIndex(0)
    ->getStyle('A:R')
    ->getNumberFormat()
    ->setFormatCode(
        PHPExcel_Style_NumberFormat::FORMAT_TEXT
    );
	$UAINVM->setActiveSheetIndex(0)
    ->getStyle('A:R')
	->getFont()
	->setName( 'Arial');
	$UAINVM->setActiveSheetIndex(0)
    ->getStyle('A:R')
	->getFont()
	->setSize(10);

	$UAINVD->setActiveSheetIndex(0)
    ->getStyle('A:N')
    ->getNumberFormat()
    ->setFormatCode(
        PHPExcel_Style_NumberFormat::FORMAT_TEXT
    );
	$UAINVD->setActiveSheetIndex(0)
    ->getStyle('A:N')
	->getFont()
	->setName( 'Arial');
	$UAINVD->setActiveSheetIndex(0)
    ->getStyle('A:N')
	->getFont()
	->setSize(10);

	$p=chr(65);
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);	 //發票號碼
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NUM_TCKETB');		 //發票號碼	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //發票號碼	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NUM_INVM');		 //發票號碼	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //發票日期	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'DAT_INVM');		 //發票日期	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //統一編號	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NUM_REG');		 //統一編號	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //地區別	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_AREA');		 //地區別	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //金額小計(未稅)	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'AMT_INVM');		 //金額小計(未稅)		
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //稅額	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'AMT_TAX');		 //稅額	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //總額(含稅)	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'AMT_TOT');		 //總額(含稅)		
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //申報月份	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'MON_INVM');		 //申報月份	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //狀況
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'STS_INVM');		 //狀況 	
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //進銷項別 //1
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TYP_TRAD');		 //進銷項別	//1
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //使用單位
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_DPT');		 //使用單位
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //交易列別 //10
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'CLS_TRAD');		 //交易列別 //10
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //交易項目 //A1
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_TRAD');		 //交易項目 //A1
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //來源單證別 //ZZ
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'INV_FROM');		 //來源單證別 //ZZ
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //稅別 //5
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TAX_TYPE');		 //稅別 //5
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //發票聯式 //10
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TYP_INVM');		 //發票聯式 //10
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //憑證類別 //31
	$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'CLS_INVM');		 //憑證類別 //31

	// Rename worksheet
	$UAINVM->getActiveSheet()->setTitle('UAINVM');

	/*$p=chr(65);
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);	 //發票號碼
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NUM_INVM');		 //發票號碼	
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //小計	
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'AMT_INVM');		 //小計
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //幣別 //NTD
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_DOLA');		 //幣別 //NTD
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //匯率 //1
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'VAL_RATE');		 //匯率 //1
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //稅別
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TAX_TYPE');		 //稅別
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //POS發票稅額
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'AMT_TAX');		 //POS發票稅額
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //料號
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'COD_ITEM');		 //料號
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //發票項次
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'LIN_INVM');		 //發票項次
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //單價
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'PRS_ITEM');		 //單價
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂單編號
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NUM_PO');		 //訂單編號
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //訂單項次
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'LIN_PO');		 //訂單項次
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //品名
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'NAM_ITEM');		 //品名
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //數量
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'QTY_ITEM');		 //數量
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p.'1', $p);		 //進銷項別 //1
	$UAINVD->setActiveSheetIndex(0)->setCellValue( $p++.'2', 'TYP_TRAD');		 //進銷項別 //1	 

	// Rename worksheet
	$UAINVD->getActiveSheet()->setTitle('UAINVD');*/

	$cst011=array();
	$cst012=array();
	
	//銷售
	for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +1 month')){
		if($_SESSION['DB']==''){
			$taste=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.$_SESSION['company'].'-taste.ini',true);
			$conn=sqlconnect('../../../menudata/'.$_SESSION['company'].'/'.$_POST['dbname'],'menu.db','','','','sqlite');
			$sql='SELECT inumber,quickorder FROM itemsdata';
			$tempmenu=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
			$menu=array();
			for($i=0;$i<sizeof($tempmenu);$i++){
				$menu[$tempmenu[$i]['inumber']]=$tempmenu[$i]['quickorder'];
			}
			if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/SALES_'.date('Ym',$d).'.db')){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'],'SALES_'.date('Ym',$d).'.db','','','','sqlite');
			}
			else{
			}
		}
		else{
			$taste=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$_SESSION['company'].'-taste.ini',true);
			$conn=sqlconnect('../../../menudata/'.$_SESSION['company'].'/'.$_SESSION['DB'],'menu.db','','','','sqlite');
			$sql='SELECT inumber,quickorder FROM itemsdata';
			$tempmenu=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
			$menu=array();
			for($i=0;$i<sizeof($tempmenu);$i++){
				$menu[$tempmenu[$i]['inumber']]=$tempmenu[$i]['quickorder'];
			}
			if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/SALES_'.date('Ym',$d).'.db')){
				$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'],'SALES_'.date('Ym',$d).'.db','','','','sqlite');
			}
			else{
			}
		}
		if(!isset($conn)||!$conn){
			//echo '資料庫尚未上傳資料。';
		}
		else{
			$sql='SELECT CST012.*,CST011.INVOICENUMBER,CST011.SALESTTLAMT,CST011.TAX1,CST011.TAX2,CST011.TAX3,CST011.TAX4,CST011.TAX5,CST011.TAX6,CST011.TAX7,CST011.TAX8,CST011.TA1,CST011.TA2,CST011.TA3,CST011.TA4,CST011.TA5,CST011.TA6,CST011.TA7,CST011.TA8,CST011.TA9,CST011.TA10,CST011.TABLENUMBER,CST011.NBCHKNUMBER FROM CST012 JOIN CST011 ON CST011.BIZDATE=CST012.BIZDATE AND CST011.CONSECNUMBER=CST012.CONSECNUMBER WHERE CST012.BIZDATE BETWEEN "'.$start.'" AND "'.$end.'" AND ((DTLMODE="1" AND DTLTYPE="1" AND DTLFUNC="01")||(DTLMODE="1" AND DTLTYPE="3" AND DTLFUNC="02")) ORDER BY CST012.BIZDATE ASC,CST012.CONSECNUMBER ASC,CST012.LINENUMBER';
			$first=sqlquery($conn,$sql,'sqlite');
			if(sizeof($first)==0){
			}
			else{
				for($i=0;$i<sizeof($first);$i++){
					if($first[$i]['ITEMCODE']=='list'||$first[$i]['ITEMCODE']=='autodis'){//帳單折扣/讓或自動優惠
						/*2019/11/26
						**帳單折扣/讓
						**自動優惠
						**如何呈現
						*/
					}
					else if($first[$i]['ITEMCODE']=='item'){//單品折扣/讓
						/*2019/11/26
						**略過，於品項一併處理
						*/
					}
					else{
						//echo $first[$i]['ITEMNAME'];
						$temp=preg_split('/-/',$first[$i]['REMARKS']);
						$tempperson='';
						for($item=1;$item<4;$item++){
							if(isset($floorspan['person'.$item]['name'])&&$floorspan['person'.$item]['name']!=''){
								$tempperson.=$first[$i]['TAX'.(5+$item)].',';
							}
							else{
							}
						}
						$tempotherpay='';
						foreach($otherpay as $index=>$item){
							if($index=='pay'){
							}
							else{
								$tempotherpay.=$first[$i][$item['dbname']].',';
							}
						}
						$temp=preg_split('/-/',$first[$i]['REMARKS']);
						if($check!=$first[$i]['CONSECNUMBER']){
							/*fwrite($f,$first[$i]['BIZDATE'].','.$first[$i]['ZCOUNTER'].','.iconv("UTF-8","Big5",$saletype['name']['listtype'.$temp[0]]).','.substr($first[$i]['CREATEDATETIME'],0,4).'/'.substr($first[$i]['CREATEDATETIME'],4,2).'/'.substr($first[$i]['CREATEDATETIME'],6,2).' '.substr($first[$i]['CREATEDATETIME'],8,2).':'.substr($first[$i]['CREATEDATETIME'],10,2).','.$first[$i]['CONSECNUMBER'].','.$first[$i]['TABLENUMBER'].','.$first[$i]['SALESTTLAMT'].','.$first[$i]['TAX1'].','.$first[$i]['TAX2'].','.$first[$i]['TAX3'].','.$first[$i]['TAX4'].','.$first[$i]['INVOICENUMBER'].','.$tempperson.(intval($first[$i]['LINENUMBER']/2)+1).','.iconv("UTF-8","Big5",$first[$i]['ITEMNAME']).','.$first[$i]['UNITPRICE'].','.$first[$i]['QTY'].','.$first[$i]['AMT']);*/
							if($first[$i]['NBCHKNUMBER']==''){
								//fwrite($f,',');
								$cst011index=sizeof($cst011);
								$cst011[$cst011index]['COD_CUST']=$codecust;										//店碼		  
								if($_SESSION['DB']==''){
									$cst011[$cst011index]['NUM_PO']=substr($first[$i]['BIZDATE'],-6).substr($_POST['dbname'],-4).$first[$i]['CONSECNUMBER'];	//交易編號	  
								}
								else{
									$cst011[$cst011index]['NUM_PO']=substr($first[$i]['BIZDATE'],-6).substr($_SESSION['DB'],-4).$first[$i]['CONSECNUMBER'];		//交易編號	  
								}
								$cst011[$cst011index]['DAT_PO']=substr($first[$i]['CREATEDATETIME'],0,8);			//消費日期	  
								$cst011[$cst011index]['DAT_RFF']=substr($first[$i]['CREATEDATETIME'],0,8);			//消費日期	  
								$cst011[$cst011index]['DAT_DELS']=substr($first[$i]['CREATEDATETIME'],0,8);			//消費日期	  
								$cst011[$cst011index]['DAT_KEYIN']=substr($first[$i]['CREATEDATETIME'],0,8);		//消費日期	  
								$cst011[$cst011index]['AMT_PO']=($first[$i]['SALESTTLAMT']+$first[$i]['TAX1']);		//明細小計加總	  
								$cst011[$cst011index]['SER_PO']='00';												//放固定值 00	   
								$cst011[$cst011index]['POS_DEL']='0000';											//放固定值 0000	  
								$cst011[$cst011index]['COD_PAYM']='99';												//放固定值 99	  
								$cst011[$cst011index]['TAX_TYPE']='5';												//放固定值 5	   
								$cst011[$cst011index]['COD_DOLA']='NTD';											//放固定值 NTD	   
								$cst011[$cst011index]['DPT_CTL']=$codecust;											//店碼		  
								$cst011[$cst011index]['COD_DPT']=$codedep;											//部門代號 	  
								$cst011[$cst011index]['RFF_RATE']='1';												//放固定值 1	  
								$cst011[$cst011index]['TYP_POHD']='99';												//放固定值 99	  
								$cst011[$cst011index]['STS_ECPO']='00';												//放固定值 00	   
								$cst011[$cst011index]['ID_INVMM']=$codecust;										//店碼		   
								$cst011[$cst011index]['COD_EMPIN']='POS';											//放固定值 POS	  
								
								if(intval($menu[intval($first[$i]['ITEMCODE'])])>0){
									$cst012index=sizeof($cst012);
									$cst012[$cst012index]['COD_CUST']=$codecust;															//店碼		
									if($_SESSION['DB']==''){
										$cst012[$cst012index]['NUM_PO']=substr($first[$i]['BIZDATE'],-6).substr($_POST['dbname'],-4).$first[$i]['CONSECNUMBER'];	//交易編號	  
									}
									else{
										$cst012[$cst012index]['NUM_PO']=substr($first[$i]['BIZDATE'],-6).substr($_SESSION['DB'],-4).$first[$i]['CONSECNUMBER'];		//交易編號	  
									}	                   
									$cst012[$cst012index]['DAT_DELS']=substr($first[$i]['CREATEDATETIME'],0,8);								//消費日期		                   
									$cst012[$cst012index]['NUM_LINE']=str_pad((intval($first[$i]['LINENUMBER']/2)+1),3,'0',STR_PAD_LEFT);	//一筆明細一個項次		           
									$cst012[$cst012index]['COD_ITEM']=$menu[intval($first[$i]['ITEMCODE'])];																//內部料號		                   
									$cst012[$cst012index]['COD_ITEMO']=$menu[intval($first[$i]['ITEMCODE'])];															//同內部料號		                   
									$cst012[$cst012index]['QTY_REQ']=$first[$i]['QTY'];														//內部數量		                   
									$cst012[$cst012index]['QTY_POC']=$first[$i]['QTY'];														//同內部數量		                   
									$cst012[$cst012index]['COD_UNIT']='POS';																//放固定值 POS		                   
									$cst012[$cst012index]['UNT_POC']='POS';																	//放固定值 POS		                   
									$cst012[$cst012index]['MNY_UNIT']=$first[$i]['UNITPRICE'];												//內部單價		                   
									$cst012[$cst012index]['PRS_POC']=$first[$i]['UNITPRICE'];												//同內部單價		                   
									$cst012[$cst012index]['MNY_DSC']=$first[$i+1]['AMT'];													//折讓  =>新增一筆負項金額明細 (單獨一個品項)
									$cst012[$cst012index]['MNY_AMT']=($first[$i]['AMT']+$first[$i+1]['AMT']);								//金額		                           
									/*2019/11/26*/
									/*if(($first[$i]['AMT']+$first[$i+1]['AMT'])<=0){
										$cst012[$cst012index]['AMT_TAX']='0';
									}
									else{*///2019/11/29
										$cst012[$cst012index]['AMT_TAX']=round((($first[$i]['AMT']+$first[$i+1]['AMT'])/1.05)*0.05);			//小計/1.05*0.05		                   
									//}//2019/11/29
									/**/
									$cst012[$cst012index]['DPT_CTL']=$codecust;																//店碼		                           
									$cst012[$cst012index]['CLS_ARMM']='011';																//放固定值 011		                   
									$cst012[$cst012index]['COD_LOC']=$codecust;																//店碼		                           
									$cst012[$cst012index]['TYP_POLN']='99';																	//放固定值 99		                   
									$cst012[$cst012index]['TAX_TYPE']='5';																	//放固定值 5		                   
									$cst012[$cst012index]['STS_ECPO']='00';																	//放固定值 00		                   
								}
								else{
								}
							}
							else{
								//fwrite($f,',作廢');
							}

							/*2019/11/26
							**備註如何呈現
							*/

							//fwrite($f,',,'.$tempotherpay.PHP_EOL);
							
							/*if($first[$i]['SELECTIVEITEM1']!=''){
								for($j=1;$j<=10;$j++){
									if($first[$i]['SELECTIVEITEM'.$j]==''){
										break;
									}
									else{
										fwrite($f,',,,,,,,,,,,,'.(intval($first[$i]['LINENUMBER']/2)+1).','.iconv("UTF-8","Big5",$taste[intval(substr($first[$i]['SELECTIVEITEM'.$j],0,5))]['name1']).','.$taste[intval(substr($first[$i]['SELECTIVEITEM'.$j],0,5))]['money'].','.intval(substr($first[$i]['SELECTIVEITEM'.$j],5)).',');
										if($first[$i]['NBCHKNUMBER']==''){
											fwrite($f,',');
										}
										else{
											fwrite($f,',作廢');
										}
										fwrite($f,','.PHP_EOL);
										
									}
								}
							}
							else{
							}*/
							$check=$first[$i]['CONSECNUMBER'];
						}
						else{
							/*fwrite($f,',,,,,,,,,,,,'.(intval($first[$i]['LINENUMBER']/2)+1).','.iconv("UTF-8","Big5",$first[$i]['ITEMNAME']).','.$first[$i]['UNITPRICE'].','.$first[$i]['QTY'].','.$first[$i]['AMT']);*/
							if($first[$i]['NBCHKNUMBER']==''){
								//fwrite($f,',');
								if(intval($menu[intval($first[$i]['ITEMCODE'])])>0){
									$cst012index=sizeof($cst012);
									$cst012[$cst012index]['COD_CUST']=$codecust;															//店碼	
									if($_SESSION['DB']==''){
										$cst012[$cst012index]['NUM_PO']=substr($first[$i]['BIZDATE'],-6).substr($_POST['dbname'],-4).$first[$i]['CONSECNUMBER'];	//交易編號	  
									}
									else{
										$cst012[$cst012index]['NUM_PO']=substr($first[$i]['BIZDATE'],-6).substr($_SESSION['DB'],-4).$first[$i]['CONSECNUMBER'];		//交易編號	  
									}	                   
									$cst012[$cst012index]['DAT_DELS']=substr($first[$i]['CREATEDATETIME'],0,8);								//消費日期		                   
									$cst012[$cst012index]['NUM_LINE']=str_pad((intval($first[$i]['LINENUMBER']/2)+1),3,'0',STR_PAD_LEFT);	//一筆明細一個項次		           
									$cst012[$cst012index]['COD_ITEM']=$menu[intval($first[$i]['ITEMCODE'])];																//內部料號		                   
									$cst012[$cst012index]['COD_ITEMO']=$menu[intval($first[$i]['ITEMCODE'])];															//同內部料號		                   
									$cst012[$cst012index]['QTY_REQ']=$first[$i]['QTY'];														//內部數量		                   
									$cst012[$cst012index]['QTY_POC']=$first[$i]['QTY'];														//同內部數量		                   
									$cst012[$cst012index]['COD_UNIT']='POS';																//放固定值 POS		                   
									$cst012[$cst012index]['UNT_POC']='POS';																	//放固定值 POS		                   
									$cst012[$cst012index]['MNY_UNIT']=$first[$i]['UNITPRICE'];												//內部單價		                   
									$cst012[$cst012index]['PRS_POC']=$first[$i]['UNITPRICE'];												//同內部單價		                   
									$cst012[$cst012index]['MNY_DSC']=$first[$i+1]['AMT'];													//折讓  =>新增一筆負項金額明細 (單獨一個品項)
									$cst012[$cst012index]['MNY_AMT']=($first[$i]['AMT']+$first[$i+1]['AMT']);								//金額		                           
									/*2019/11/26*/
									/*if(($first[$i]['AMT']+$first[$i+1]['AMT'])<=0){
										$cst012[$cst012index]['AMT_TAX']='0';
									}
									else{*///2019/11/29
										$cst012[$cst012index]['AMT_TAX']=round((($first[$i]['AMT']+$first[$i+1]['AMT'])/1.05)*0.05);			//小計/1.05*0.05		                   
									//}//2019/11/29
									/**/
									$cst012[$cst012index]['DPT_CTL']=$codecust;																//店碼		                           
									$cst012[$cst012index]['CLS_ARMM']='011';																//放固定值 011		                   
									$cst012[$cst012index]['COD_LOC']=$codecust;																//店碼		                           
									$cst012[$cst012index]['TYP_POLN']='99';																	//放固定值 99		                   
									$cst012[$cst012index]['TAX_TYPE']='5';																	//放固定值 5		                   
									$cst012[$cst012index]['STS_ECPO']='00';																	//放固定值 00		                   
								}
								else{
								}
							}
							else{
								//fwrite($f,',作廢');
							}
							//fwrite($f,','.PHP_EOL);
							
							/*2019/11/26
							**備註如何呈現
							*/

							/*if($first[$i]['SELECTIVEITEM1']!=''){
								for($j=1;$j<=10;$j++){
									if($first[$i]['SELECTIVEITEM'.$j]==''){
										break;
									}
									else{
										fwrite($f,',,,,,,,,,,,,'.(intval($first[$i]['LINENUMBER']/2)+1).','.iconv("UTF-8","Big5",$taste[intval(substr($first[$i]['SELECTIVEITEM'.$j],0,5))]['name1']).','.$taste[intval(substr($first[$i]['SELECTIVEITEM'.$j],0,5))]['money'].','.intval(substr($first[$i]['SELECTIVEITEM'.$j],5)).',');
										if($first[$i]['NBCHKNUMBER']==''){
											fwrite($f,',');
										}
										else{
											fwrite($f,',作廢');
										}
										fwrite($f,','.PHP_EOL);
										
									}
								}
							}
							else{
							}*/
						}
					}
				}
			}
		}
		sqlclose($conn,'sqlite');
	}

	//發票
	for($d=strtotime(date('Ymd',strtotime($start)));$d<=$ENDDATE;$d=strtotime(date('Ymd',$d).' +2 month')){
		unset($conn);
		foreach($mapping as $m){
			if($_SESSION['DB']==''){
				if(intval(date('m',$d))%2==0){//偶數月
					$invdate=date('Ym',$d);
				}
				else{//奇數月
					$invdate=date('Y',$d).str_pad((intval(date('m',$d))+1),2,'0',STR_PAD_LEFT);
				}
				if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.$invdate.'/invdata_'.$invdate.'_'.$m.'.db')){
					$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_POST['dbname'].'/'.$invdate,'invdata_'.$invdate.'_'.$m.'.db','','','','sqlite');
				}
				else{
				}
			}
			else{
				if(intval(date('m',$d))%2==0){//偶數月
					$invdate=date('Ym',$d);
				}
				else{//奇數月
					$invdate=date('Y',$d).str_pad((intval(date('m',$d))+1),2,'0',STR_PAD_LEFT);
				}
				if(file_exists('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$invdate.'/invdata_'.$invdate.'_'.$m.'.db')){
					$conn=sqlconnect('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/'.$invdate,'invdata_'.$invdate.'_'.$m.'.db','','','','sqlite');
				}
				else{
				}
			}
			if(isset($conn)){
				$sql='SELECT * FROM invlist WHERE createdate BETWEEN "'.$start.'" AND "'.$end.'"';
				$data=sqlquery($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
				if(isset($data[0]['invnumber'])){
					$UAINVMdata=array();
					for($i=0;$i<sizeof($data);$i++){
						$dataindex=sizeof($UAINVMdata);
						$UAINVMdata[$dataindex]['NUM_TCKETB']=$data[$i]['invnumber'];//發票號碼
						$UAINVMdata[$dataindex]['NUM_INVM']=$data[$i]['invnumber'];//發票號碼
						$UAINVMdata[$dataindex]['DAT_INVM']=$data[$i]['createdate'];//發票日期
						if($data[$i]['buyerid']=='0000000000'){
							$UAINVMdata[$dataindex]['NUM_REG']='';//統一編號
						}
						else{
							$UAINVMdata[$dataindex]['NUM_REG']=$data[$i]['buyerid'];//統一編號
						}
						$UAINVMdata[$dataindex]['COD_AREA']=$codecust;//店碼
						$UAINVMdata[$dataindex]['AMT_INVM']=round($data[$i]['totalamount']/1.05);//金額小計(未稅)
						$UAINVMdata[$dataindex]['AMT_TAX']=intval($data[$i]['totalamount'])-intval(round($data[$i]['totalamount']/1.05));//稅額
						$UAINVMdata[$dataindex]['AMT_TOT']=$data[$i]['totalamount'];//總額(含稅)
						$UAINVMdata[$dataindex]['MON_INVM']=date('Ymd',strtotime($invdate.' +1 month'));//申報月份
						if($data[$i]['state']=='1'){
							$UAINVMdata[$dataindex]['STS_INVM']='00';//00>>正常-1>>作廢
						}
						else{
							$UAINVMdata[$dataindex]['STS_INVM']='-1';//00>>正常-1>>作廢
						}
						$UAINVMdata[$dataindex]['TYP_TRAD']='1';//1
						$UAINVMdata[$dataindex]['COD_DPT']=$codedep;//部門代號
						$UAINVMdata[$dataindex]['CLS_TRAD']='10';//10
						$UAINVMdata[$dataindex]['COD_TRAD']='A1';//A1
						$UAINVMdata[$dataindex]['INV_FROM']='ZZ';//ZZ
						$UAINVMdata[$dataindex]['TAX_TYPE']='5';//5
						$UAINVMdata[$dataindex]['TYP_INVM']='10';//10
						$UAINVMdata[$dataindex]['CLS_INVM']='31';//31
					}
				}
				else{
				}
			}
			else{
			}
		}
	}
	//fclose($f);
	
}
else{
}

if(sizeof($cst011)>0){
	for($i=0;$i<sizeof($cst011);$i++){
		$p=chr(65);
		foreach($cst011[$i] as $c){
			$EC_POHD->setActiveSheetIndex(0)->setCellValue( $p++.($i+3), $c);
		}
	}

	for($i=0;$i<sizeof($cst012);$i++){
		$p=chr(65);
		foreach($cst012[$i] as $c){
			$EC_POLN->setActiveSheetIndex(0)->setCellValue( $p++.($i+3), $c);
		}
	}
}
else{
}

if(isset($UAINVMdata)){
	for($i=0;$i<sizeof($UAINVMdata);$i++){
		$p=chr(65);
		foreach($UAINVMdata[$i] as $c){
			$UAINVM->setActiveSheetIndex(0)->setCellValue( $p++.($i+3), $c);
		}
	}
}
else{
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$EC_POHD->setActiveSheetIndex(0);
$EC_POLN->setActiveSheetIndex(0);


// Save Excel 95 file
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($EC_POHD, 'Excel5');
$objWriter->save('../../'.$fEC_POHD);

$objWriter = PHPExcel_IOFactory::createWriter($EC_POLN, 'Excel5');
$objWriter->save('../../'.$fEC_POLN);

$objWriter = PHPExcel_IOFactory::createWriter($UAINVM, 'Excel5');
$objWriter->save('../../'.$fUAINVM);

/*$objWriter = PHPExcel_IOFactory::createWriter($UAINVD, 'Excel5');
$objWriter->save('../../'.$fUAINVD);*/
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
echo $fEC_POHD.';-;'.$fEC_POLN.';-;'.$fUAINVM;
?>