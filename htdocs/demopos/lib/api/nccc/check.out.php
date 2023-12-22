<?php
include_once '../../../../tool/myerrorlog.php';
require_once '../../../../tool/PHPWord.php';
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
$tradecode=parse_ini_file('./tradecode.ini',true);//2022/3/31 交易碼對應
date_default_timezone_set($initsetting['init']['settime']);

while(!file_exists('../../../../print/card/out.dat')){
	;
}
sleep(1);//等待1秒
$f=fopen('../../../../print/card/out.dat','r');
$content=fread($f,400);
fclose($f);

if(file_exists('../../../../print/card/in.dat')){
	unlink('../../../../print/card/in.dat');
}
else{
}
unlink('../../../../print/card/out.dat');

$f=fopen('../../../../print/card/log/'.substr($_POST['date'],0,4).'/'.substr($_POST['date'],4,2).'/'.substr($_POST['date'],6,2).'/nccc.log','a');
fwrite($f,date('Y/m/d H:i:s').' --- out.dat='.$content.PHP_EOL);
fclose($f);

$status='回傳內容長度錯誤';
$recode='';
$balance='';
if(strlen($content)=='400'){
	$code=substr($content,76,4);//交易碼
	if(isset($tradecode['code'][$code])){
		$status=$tradecode['code'][$code];
	}
	else{
		$status='例外狀況';
	}
	$recode=substr($content,312,1).';'.substr($content,10,1).';20'.substr($content,54,6).trim(substr($content,239,12)).';'.trim(substr($content,66,9));//2022/10/21 RFcode前面加上回傳的交易日期(但回傳的"年"只有後兩位，因此先固定在字首加上"20"形成20XX年)，方便後續給電子票證退款用//2022/9/12 尾端再補上授權碼，原本訓練機不用，正式機退款需要//2022/7/20 尾端補上電子票證交易序號，方便電子票證退款使用//2022/5/17 額外串接上代號，因為原先沒有考慮到電子票證代號不同//是否符合優惠活動('A':符合優惠；' ':不符合優惠)
	$balance=intval(substr($content,188,10));//2022/5/11 欄位原始長度為12，但後兩位為小數，因為只有整數故不讀取

	if(substr($content,8,2)=='01'&&$code=='0000'){//2022/9/13 交易成功(侷限付款)
		//2022/9/13 列印刷卡票單(產生PDF檔，因為列印速度較好)
		require_once('../../../../tool/TCPDF/examples/tcpdf_include.php');
		//$pdf = new TCPDF(P:直式、L:橫式, 單位(mm), 紙張大小(長短邊；不分長寬：array(,) ), true, 'UTF-8', false);
		$pdf = new TCPDF("P", "mm", array(72,297), true, "UTF-8", false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('NCCCTicket');
		$pdf->setPrintHeader(false);
		$pdf->SetHeaderMargin(0);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(2, 0, 22);
		if (@file_exists(dirname(__FILE__).'/../../../tool/TCPDF/examples/lang/eng.php')) {
			require_once(dirname(__FILE__).'/../../../tool/TCPDF/examples/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		$pdf->AddPage();
		//$pdf->MultiCell(寬, 高, 內容, 框線, 對齊：L靠左、C置中、R靠右, 是否填塞, 下一個元件的位置：「0（預設）右邊；1下行最左邊；2目前元件下方」, X軸, Y軸, 若true會重設最後一格的高度, 0不延伸；1字大於格寬才縮放文字；2一律縮放文字到格寬；3字大於格寬才縮放字距；4一律縮放字距到格寬、「$ignore_min_height」自動忽略最小高度, 0, 自動調整內距, 高度上限, 垂直對齊T、C、B, 自動縮放字大小到格內);
		$pdf->SetFont('DroidSansFallback', 'B', 12);
		$pdf->MultiCell('', '', "聯合信用卡中心", 0, 'C', 0, 1, 0, 0, 1, 0, 0, 0, 10, 'T', 0);
		$pdf->SetFont('DroidSansFallback', '', 10);
		$pdf->MultiCell('', '', "交易金額：NT＄".intval(substr($content,42,10)), 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		$pdf->MultiCell('', '', "收單機構：聯信中心", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		switch(substr($content,75,1)){
			case 'V':
				$pdf->MultiCell('', '', "卡別：VISA", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'M':
				$pdf->MultiCell('', '', "卡別：M／C", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'J':
				$pdf->MultiCell('', '', "卡別：JCB", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'C':
				$pdf->MultiCell('', '', "卡別：CUP", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'A':
				$pdf->MultiCell('', '', "卡別：AMEX", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'D':
				$pdf->MultiCell('', '', "卡別：DFS", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'Z':
				$pdf->MultiCell('', '', "卡別：悠遊卡", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'P':
				$pdf->MultiCell('', '', "卡別：一卡通", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'G':
				$pdf->MultiCell('', '', "卡別：愛金卡", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'H':
				$pdf->MultiCell('', '', "卡別：有錢卡", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'T':
				$pdf->MultiCell('', '', "卡別：TWIN Card", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			case 'O':
				$pdf->MultiCell('', '', "卡別：其他卡", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
			default:
				$pdf->MultiCell('', '', "卡別：", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
				break;
		}
		$pdf->MultiCell('', '', "卡號", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		$pdf->MultiCell('', '', "    ".trim(substr($content,19,19)), 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		$pdf->MultiCell('', '', "端末機：".trim(substr($content,95,8)), 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		$pdf->MultiCell('', '', "調閱編號：".trim(substr($content,13,6)), 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		if(trim(substr($content,66,9))!=''){//授權碼
			$pdf->MultiCell('', '', "授權碼／RF：".trim(substr($content,66,9)), 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		}
		else if(trim(substr($content,239,12))!=''){//RF code
			$pdf->MultiCell('', '', "授權碼／RF：".trim(substr($content,239,12)), 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		}
		else{
			$pdf->MultiCell('', '', "授權碼／RF：", 0, 'L', 0, 1, '', '', 1, 0, 0, 0, 10, 'T', 0);
		}
		$pdf->lastPage();
		$pdf->Output(dirname(__FILE__).'/../../../../print/noread/'.date('YmdHis').'_ncccticket.pdf', 'F');
	}
	else{
	}
}
else{
}

$res['data']=$status;
$res['code']=$recode;
$res['balance']=$balance;

echo json_encode($res);
?>