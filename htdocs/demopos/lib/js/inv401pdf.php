<?php
include_once '../../../tool/AES/lib.php';
include_once '../../../tool/phpqrcode/qrlib.php';
include_once '../../../tool/phpbarcode/src/BarcodeGenerator.php';
include_once '../../../tool/phpbarcode/src/BarcodeGeneratorPNG.php';
include_once '../../../tool/AES/lib.php';
include_once '../../../tool/phpqrcode/qrlib.php';

date_default_timezone_set('Asia/Taipei');

//產生電子發票開立資訊PDF
require_once('../../../tool/TCPDF/examples/tcpdf_include.php');

$pdf = new TCPDF("P", "mm", array(72,297), true, "UTF-8", false);
//$pdf = new TCPDF("P", "mm", array(72,100), true, "UTF-8", false);
//$pdf = new TCPDF(P:直式、L:橫式, 單位(mm), 紙張大小(長短邊；不分長寬：array(,) ), true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('invoice');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->setPrintHeader(false);
$pdf->SetHeaderMargin(0);
$pdf->setPrintFooter(false);

$pdf->SetMargins(2, 0, 22);

if (@file_exists(dirname(__FILE__).'/../tool/TCPDF/examples/lang/eng.php')) {
	require_once(dirname(__FILE__).'/../tool/TCPDF/examples/lang/eng.php');
	$pdf->setLanguageArray($l);
}
//$pdf->SetFont('DroidSansFallback', '', 10);
$pdf->AddPage();

//$pdf->MultiCell(寬, 高, 內容, 框線, 對齊：L靠左、C置中、R靠右, 是否填塞, 下一個元件的位置：「0（預設）右邊；1下行最左邊；2目前元件下方」, X軸, Y軸, 若true會重設最後一格的高度, 0不延伸；1字大於格寬才縮放文字；2一律縮放文字到格寬；3字大於格寬才縮放字距；4一律縮放字距到格寬、「$ignore_min_height」自動忽略最小高度, 0, 自動調整內距, 高度上限, 垂直對齊T、C、B, 自動縮放字大小到格內);

$pdf->SetFont('DroidSansFallback', '', 12);
$pdf->MultiCell('', '', "敝姓鍋", 0, 'C', 0, 0, 0, 0, 1, 0, 0, 0, 10, 'T', 0);

//$pdf->SetFont('DroidSansFallback', 'B', 17);
//$pdf->MultiCell('', '', "電子發票證明聯", 0, 'C', 0, 0, 0, 8.5, 1, 0, 0, 0, 10, 'T', 0);

$pdf->SetFont('DroidSansFallback', 'B', 14);
$pdf->MultiCell('', '', "電子發票證明聯補印", 0, 'C', 0, 0, 0, 8.5, 1, 0, 0, 0, 10, 'T', 0);

$pdf->SetFont('DroidSansFallback', 'B', 17);
$pdf->MultiCell('', '', "110年01－02月", 0, 'C', 0, 0, 0, 15, 1, 0, 0, 0, 10, 'T', 0);

$pdf->MultiCell('', '', "BB－12345678", 0, 'C', 0, 0, 0, 21.5, 1, 0, 0, 0, 10, 'T', 0);

$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
file_put_contents('../../../print/barcode/11002BB123456785971barcode.png', $generator->getBarcodeNoText('11002BB123456785971', $generator::TYPE_CODE_39));
//$html = '<img width="150" height="50" src="../../../print/barcode/11002BB123456785971barcode.png">';

//echo $html;

// output the HTML content
//$pdf->writeHTMLCell(50, 10, 3, 35, $html, 0, 0, 0, 0, 'C', 1);
$pdf->Image('../../../print/barcode/11002BB123456785971barcode.png',3,35,45,20,'png','','M',1,300);

$pdf->SetFont('DroidSansFallback', '', 9);
$pdf->MultiCell('', '', "2021-01-02 10:38:14", 0, 'L', 0, 0, 3, 29, 1, 0, 0, 0, 10, 'T', 0);

//具有買方統編
//$pdf->MultiCell('', '', "格式:25", 0, 'L', 0, 0, 35, 29, 1, 0, 0, 0, 10, 'T', 0);

$pdf->MultiCell('', '', "隨機碼:5971", 0, 'L', 0, 0, 3, 32.5, 1, 0, 0, 0, 10, 'T', 0);

$pdf->MultiCell('', '', "總計:20000", 0, 'L', 0, 0, 25, 32.5, 1, 0, 0, 0, 10, 'T', 0);

$pdf->MultiCell('', '', "賣方87216652", 0, 'L', 0, 0, 3, 36, 1, 0, 0, 0, 10, 'T', 0);

//具有買方統編
//$pdf->MultiCell('', '', "買方60353288", 0, 'L', 0, 0, 26, 36, 1, 0, 0, 0, 10, 'T', 0);

/*$pdf->SetFont('DroidSansFallback', '', 10);
$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => 4,
    'fitwidth' => 0,
    'cellfitalign' => '',
    'border' => 0,
    'hpadding' => '0',
    'vpadding' => '0',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => 0,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);

// PRINT VARIOUS 1D BARCODES

// CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
$pdf->write1DBarcode('11002BB123456785971', 'C39', 3, 40, '', '', 0.4, $style, 'T');

$pdf->Ln();*/

$qrcodeClass = new encryQrcode();

$aesKey = "1905B9C0E27FB708712E42CED49178AB";// input your aeskey
$invoiceNumAndRandomCode = "BB123456785971";// input your invoiceNumber And RandomCode
$encry=$qrcodeClass->aes128_cbc_encrypt($aesKey, $invoiceNumAndRandomCode);

QRcode::png("BB12345678110012059710000000000004E200000000087216652".$encry.":**********:1:1:1::1:20000", "../../../print/qrcode/leftqrcode.png", "L", "4", 2);

$pdf->Image("../../../print/qrcode/leftqrcode.png",5,51,20,20,'png','','M',1,300);

QRcode::png("**                                                                                                                                   ", "../../../print/qrcode/rightqrcode.png", "L", "4", 2);

$pdf->Image("../../../print/qrcode/rightqrcode.png",28,51,20,20,'png','','M',1,300);

$pdf->SetFont('DroidSansFallback', '', 8.5);
$pdf->MultiCell('', '', "**退貨時請攜帶電子發票證明聯", 0, 'L', 0, 0, 3, 71, 1, 0, 0, 0, 10, 'T', 0);

$pagey=75;
$pdf->lastPage();
$pdf->AddPage();
$pagey=0;


$pdf->SetFont('DroidSansFallback', '', 12);
$pdf->MultiCell('', '', "敝姓鍋", 0, 'C', 0, 0, 0, (0+$pagey), 1, 0, 0, 0, 10, 'T', 0);

$pdf->MultiCell('', '', "交易明細", 0, 'C', 0, 0, 0, (5+$pagey), 1, 0, 0, 0, 10, 'T', 0);

$pdf->SetFont('DroidSansFallback', '', 8);
//if($invlist=='2'){//總項
	$pdf->MultiCell('', '', "餐費123456123455612342345234512432345353461243144353456142345", 0, 'L', 0, 2, 3, (10+$pagey), 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(15, '', "20000x", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(10, '', "1", 0, 'R', 0, 0, 18, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(20, '', "20000TX", 0, 'R', 0, 2, 28, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell('', '', "餐費", 0, 'L', 0, 2, 3, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(15, '', "20000x", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(10, '', "1", 0, 'R', 0, 0, 18, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(20, '', "20000TX", 0, 'R', 0, 2, 28, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell('', '', "餐費", 0, 'L', 0, 2, 3, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(15, '', "20000x", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(10, '', "1", 0, 'R', 0, 0, 18, '', 1, 0, 0, 0, 10, 'T', 0);
	$pdf->MultiCell(20, '', "20000TX", 0, 'R', 0, 2, 28, '', 1, 0, 0, 0, 10, 'T', 0);
/*}
else{//明細
	for($j=0,$i=0;$j<sizeof($listitems);$j++){
		if($j%15==0&&$j>0){
			$pdf->lastPage();
			$pdf->AddPage();
			if(file_exists('./logo/'.$companycode.'.png')){
				$html = '<div>
							<img src="./logo/'.$companycode.'.png" style="height:48px;">
						</div>';

				// output the HTML content
				$pdf->SetFillColor(255, 255, 255);
				$pdf->writeHTMLCell('',20,10,10,$html,0,1,0,true,'J',true);
			}
			else{
			}

			$html = '<div style="text-align:center;">
						'.substr($invdate,0,4).'-'.substr($invdate,4,2).'-'.substr($invdate,6,2).'
					</div>';

			// output the HTML content
			$pdf->SetFillColor(255, 255, 255);
			$pdf->writeHTMLCell('',20,15,38,$html,0,1,0,true,'J',true);

			$html = '<div>
				<table style="width:100%;">
					<tr>
						<td style="width:330px;">'.$invoicedata['InvoiceNO'].'</td>
					</tr>
					<tr>
						<td style="font-size:9px;"></td>
					</tr>
					<tr>
						<td style="width:330px;">';
						if($invoicedata['CompanyName']!=''){
							$html .= $invoicedata['CompanyName'];
						}
						else{
						}
						$html .= '</td>
						<td style="width:75px;"></td>
						<td style="width:43px;text-align:center;"></td>
					</tr>
					<tr>
						<td style="font-size:8px;"></td>
					</tr>
					<tr>
						<td style="width:330px;">';
						if($invoicedata['TaxRegNO']!=''){
							$html .= $invoicedata['TaxRegNO'];
						}
						else{
						}
						$html .= '</td>
					</tr>
					<tr>
						<td style="font-size:9px;"></td>
					</tr>
					<tr>
						<td style="width:330px;">';
				if(!isset($_POST['printaddress'])||$_POST['printaddress']==='1'){
					$html .= $invoicedata['Address'];
				}
				else{
				}
				$html .= '</td>
						<td style="width:40px;text-align:right;">'.(intval($j/15)+1).'</td>
						<td style="width:70px;text-align:right;">'.(intval(sizeof($listitems)/15)+1).'</td>
					</tr>
				</table>
			</div>';

			// output the HTML content
			$pdf->SetFillColor(255, 255, 255);
			$pdf->writeHTMLCell(159,37,38,46,$html,0,1,0,true,'J',true);
			$html = '<div>
						<table style="width:100%;">
							<tr>
								<td style="width:373px;text-align:right;font-size:12px;height:23.5px;">'.number_format(intval($invoicedata['Amount'])).'</td>
							</tr>
							<tr>
								<td style="width:373px;text-align:right;font-size:12px;height:23.5px;">'.number_format(intval($invoicedata['TaxAmt'])).'</td>
								<td rowspan="3">'.$companyname.'<br>'.$companyid.'<br>'.$companyaddress.'</td>
							</tr>
							<tr>
								<td style="width:373px;text-align:right;font-size:12px;height:23.5px;">'.number_format(intval($invoicedata['Total'])).'</td>
							</tr>
							<tr>
								<td style="width:373px;text-align:right;font-size:12px;height:23.5px;">'.num2Zh(intval($invoicedata['Total'])).'元整</td>
							</tr>
						</table>
					</div>';

			// output the HTML content
			$pdf->SetFillColor(255, 255, 255);
			$pdf->writeHTMLCell(183,35.5,13.5,241,$html,0,1,0,true,'J',true);

			for(;$i<sizeof($stkBillSub)&&$i<$maxi;$i++){
				$pdf->MultiCell(50.7, 10, '銷貨:'.$stkBillSub[$i]['BillNO'].' 客戶單:'.$stkBillSub[$i]['CustBillNO'], 0, 'L', 0, 1, 146.5, (91.5 + ($i%15)*10), 1, 1, 0, 0, 10, 'T', 1);
			}
		}
		else{
		}
		if($j!=0){
			$pdf->MultiCell(70.3, 10, $listitems[$j]['ProdName'], 0, 'L', 0, 0, 13.5, (91.5 + ($j%15)*10), 1, 0, 0, 0, 10, 'T', 0);
			$pdf->MultiCell(17, 10, number_format(intval($listitems[$j]['Quantity'])), 0, 'C', 0, 0, 83.8, (91.5 + ($j%15)*10), 1, 1, 0, 0, 10, 'T', 1);
			$pdf->MultiCell(23, 10, number_format(floatval($listitems[$j]['Price']),3), 0, 'R', 0, 0, 100.8, (91.5 + ($j%15)*10), 1, 1, 0, 0, 10, 'T', 1);
			$pdf->MultiCell(22.6, 10, number_format(floatval($listitems[$j]['Quantity'])*floatval($listitems[$j]['Price'])), 0, 'R', 0, 1, 123.4, (91.5 + ($j%15)*10), 1, 1, 0, 0, 10, 'T', 1);
			$maxi=$i+15;
		}
		else{
			$pdf->MultiCell(70.3, 10, $listitems[$j]['ProdName'], 0, 'L', 0, 0, 13.5, 91.5, 1, 0, 0, 0, 10, 'T', 0);
			$pdf->MultiCell(17, 10, number_format(intval($listitems[$j]['Quantity'])), 0, 'C', 0, 0, 83.8, 91.5, 1, 1, 0, 0, 10, 'T', 1);
			$pdf->MultiCell(23, 10, number_format(floatval($listitems[$j]['Price']),3), 0, 'R', 0, 0, 100.8, 91.5, 1, 1, 0, 0, 10, 'T', 1);
			$pdf->MultiCell(22.6, 10, number_format(floatval($listitems[$j]['Quantity'])*floatval($listitems[$j]['Price'])), 0, 'R', 0, 1, 123.4, 91.5, 1, 1, 0, 0, 10, 'T', 1);
			for(;$i<sizeof($stkBillSub)&&$i<15;$i++){
				$pdf->MultiCell(50.7, 10, '銷貨:'.$stkBillSub[$i]['BillNO'].' 客戶單:'.$stkBillSub[$i]['CustBillNO'], 0, 'L', 0, 1, 146.5, (91.5 + ($i%15)*10), 1, 1, 0, 0, 10, 'T', 1);
			}
		}
	}
}*/

$pdf->SetFont('DroidSansFallback', '', 10);
$pdf->MultiCell('', '', "---------------------------------------", 0, 'C', 0, 1, 3, '', 1, 0, 0, 0, 10, 'T', 0);

$pdf->SetFont('DroidSansFallback', '', 8);
$pdf->MultiCell('', '', "總計", 0, 'L', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);
$pdf->MultiCell('', '', "＄20", 0, 'R', 0, 0, 3, '', 1, 0, 0, 0, 10, 'T', 0);

$pdf->lastPage();
$pdf->Output(dirname(__FILE__).'/../../../print/read/C0401_60353288_BB12345678_0120103814.pdf', 'F');
?>