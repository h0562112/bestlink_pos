<?php
require_once '../../../tool/PHPWord.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(file_exists('../../syspram/membermoney.ini')){
	$mapname=parse_ini_file('../../syspram/membermoney.ini',true);
}
else{
}
if(isset($print['item']['potitle'])){
	$potitle=$print['item']['potitle'];
}
else{
	$potitle=12;
}
if(isset($print['item']['potext'])){
	$potext=$print['item']['potext'];
}
else{
	$potext=12;
}
if(isset($_POST['moneytype'])){
	$moneytype=$_POST['moneytype'];
}
else{
	$moneytype='';
}
date_default_timezone_set($init['init']['settime']);
$PostData = array(
	"type"=> "online",
	"memno" => $_POST['memno'],
	//"CouponApiKey" => $itrisetup['itri']['couponapikey'],
	"company" => $_POST['company'],
	"ajax" => ""
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php');//
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
$memdata = curl_exec($ch);
$memdata=json_decode($memdata,1);
if(curl_errno($ch) !== 0) {
	//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
}
curl_close($ch);
//print_r($memdata);

$filename=date('YmdHis')."_paymoney";
$PHPWord = new PHPWord();
if(isset($print['item']['clienttype'])&&file_exists('../../../template/pointtree'.$print['item']['clienttype'].'.docx')){
	$document = $PHPWord->loadTemplate('../../../template/pointtree'.$print['item']['clienttype'].'.docx');
}
else{//舊版明細單
	$document = $PHPWord->loadTemplate('../../../template/pointtree.docx');
}
//$document = $PHPWord->loadTemplate('../../../template/pointtree.docx');
if(isset($mapname['name']['title'])){
	$document->setValue('title',$mapname['name']['title']);
}
else{
	$document->setValue('title','儲值確認單');
}
$document->setValue('datetime',date('Y/m/d H:i:s'));
$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';

if(isset($mapname['name']['tel'])){
	$table .= $mapname['name']['tel'];
}
else{
	$table .= "會員電話";
}

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';

if(strlen($memdata[0]['tel'])==10){
	$table .= substr($memdata[0]['tel'],0,4).'***'.substr($memdata[0]['tel'],-3);
}
else if(strlen($memdata[0]['tel'])==8){
	$table .= '*****'.substr($memdata[0]['tel'],-3);
}
else{
	$table .= $memdata[0]['tel'];
}

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';

if($moneytype=='yunlincoins'){
	$table .= "轉換雲林幣";
}
else{
	if(isset($mapname['name']['paymoney'])){
		$table .= $mapname['name']['paymoney'];
	}
	else{
		$table .= "儲值金額";
	}
}

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
$table .= $_POST['paymoney'];
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';

if(isset($mapname['name']['remaining'])){
	$table .= $mapname['name']['remaining'];
}
else{
	$table .= "剩餘儲值金";
}

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
$table .= $memdata[0]['money'];
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="600"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= "</w:tbl>";
$document->setValue('dataitem',$table);
$prt=fopen("../../../print/noread/".$filename.".prt",'w');
fclose($prt);
$document->save("../../../print/read/".$filename.".docx");
?>