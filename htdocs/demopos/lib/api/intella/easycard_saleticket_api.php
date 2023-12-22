<?php
require_once '../../../../tool/PHPWord.php';
$content=parse_ini_file('../../../../database/initsetting.ini',true);
$print=parse_ini_file('../../../../database/printlisttag.ini',true);

date_default_timezone_set($content['init']['settime']);

if(isset($print['item']['ectitle'])&&$print['item']['ectitle']!=''){
	$clititle=$print['item']['ectitle'];
}
else{
	$clititle=14;
}
if(isset($print['item']['eccont'])&&$print['item']['eccont']!=''){
	$clicont=$print['item']['eccont'];
}
else{
	$clicont=14;
}

$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate('../../../../template/easycardticket.docx');

$item='';
$item .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';

$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '交易日期';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= substr($_POST['data']['Data']['Date'],0,4).'/'.substr($_POST['data']['Data']['Date'],4,2).'/'.substr($_POST['data']['Data']['Date'],6,2);
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '交易時間';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= substr($_POST['data']['Data']['Time'],0,2).':'.substr($_POST['data']['Data']['Time'],2,2).':'.substr($_POST['data']['Data']['Time'],4,2);
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '交易類別';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
if($_POST['data']['Data']['request']['ServiceType']=='Payment'){
	$item .= '卡機扣款';
}
else if($_POST['data']['Data']['request']['ServiceType']=='Refund'){
	$item .= '卡機退款';
}
else{
}
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '設備編號';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= $_POST['data']['Data']['DeviceNumber'];
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '批次號碼';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= $_POST['data']['Data']['request']['BatchNumber'];
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= 'RRN';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= $_POST['data']['Data']['RRNumber'];
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '卡號';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= $_POST['data']['Data']['EZCardID'];
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '交易前金額';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= number_format($_POST['data']['Data']['BeforeTXNBalance']);
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '自動加值金額';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
if(isset($_POST['data']['Data']['AutoTopUpAmount'])&&$_POST['data']['Data']['AutoTopUpAmount']!='No'){
	$item .= number_format($_POST['data']['Data']['AutoTopUpAmount']);
}
else{
	$item .= '0';
}
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '扣款金額';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= number_format($_POST['data']['Data']['Amount']);
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";
$item .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= '後款後金額';
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
$item .= number_format($_POST['data']['Data']['Balance']);
$item .= "</w:t></w:r></w:p></w:tc>";
$item .= "</w:tr>";

$item .= "</w:tbl>";

$document->setValue('item',$item);
$filename=date("YmdHis");
//$document->save("../../../../print/noread/number_".$filename.".docx");

$document->save("../../../../print/read/".$_POST['machine']."_easycardticket_".$filename.".docx");

if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
	$prt=fopen("../../../../print/noread/".$_POST['machine']."_easycardticket_".$filename.".".$_POST['machine'],'w');
}
else{
	$prt=fopen("../../../../print/noread/".$_POST['machine']."_easycardticket_".$filename.".prt",'w');
}
fclose($prt);
?>