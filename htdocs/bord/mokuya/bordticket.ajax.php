<?php
include_once '../tool/inilib.php';
include_once '../tool/PHPWord.php';
include_once '../tool/phpqrcode/qrlib.php'; 
if(file_exists('../print/qrcode')){
}
else{
	mkdir('../print/qrcode',0777,true);
}
$set=parse_ini_file('./set.ini',true);
$content=parse_ini_file('./'.$set['ticket']['target'].'/data/content.ini',true);
$callnumber=parse_ini_file('./'.$set['ticket']['target'].'/callnumber/ticket.ini',true);

if(isset($content['initial']['settime'])){
	date_default_timezone_set($content['initial']['settime']);
}
else{
	date_default_timezone_set('Asia/Taipei');
}

if($callnumber['data']['now']==$callnumber['data']['end']){
	$callnumber['data']['now']=intval($callnumber['data']['start'])+1;
}
else{
	$callnumber['data']['now']=intval($callnumber['data']['now'])+1;
}
write_ini_file($callnumber,'./'.$set['ticket']['target'].'/callnumber/ticket.ini');

$document='';
$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate('../template.docx');

$table='';
$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
if(isset($content['initial']['company'])&&isset($content['initial']['dep'])){
	$table .= $content['initial']['company']."-".$content['initial']['dep'];
}
else{
	$table .= "桌加雲端多媒體系統-福雅總部";
}
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";
$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
$table .= date('Y/m/d H:i:s');
if(isset($content['weekmap'][date('l')])){
	$table .= ' '.$content['weekmap'][date('l')];
}
else{
	$table .= ' '.date('l');
}
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="60"/><w:szCs w:val="60"/></w:rPr><w:t>';
$table .= str_pad($callnumber['data']['now'],3,"0",STR_PAD_LEFT);
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '</w:tbl>';

$document->setValue('data',$table);

$filename=$callnumber['data']['now'].'.png';
QRcode::png('http://www.quickcode.com.tw/bord/'.$content['initial']['company'].'/'.$content['initial']['story'].'/searchnumber.php?num='.$callnumber['data']['now'],'../print/qrcode/'.$filename,'M',8);
$document->replaceStrToQrcode('qrcode','../print/qrcode/'.$filename);

$table='';
$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
$table .= "請掃描QRcode 即可顯示候位資訊";
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";
$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
$table .= "感謝您的耐心等候";
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";
$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
$table .= "＊過號不候請重新取牌＊";
$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '</w:tbl>';

$document->setValue('hint',$table);

$filename=date('YmdHis')."_bordticket";
$document->save("../print/read/".$filename.".docx");
$prt=fopen("../print/noread/".$filename.".prt",'w');
fclose($prt);
?>