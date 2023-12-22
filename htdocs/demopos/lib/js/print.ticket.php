<?php
date_default_timezone_set('Asia/Taipei');

include_once '../../../tool/PHPWord.php';

$date=date('Y/m/d');
$time=date('H:i:s');
$print=parse_ini_file('../../../database/printlisttag.ini',true);

$document='';
$PHPWord = new PHPWord();
if(isset($print['item']['numbertagtype'])&&$print['item']['numbertagtype']!=''&&file_exists('../../../template/numbertag'.$print['item']['numbertagtype'].'.docx')){
	$document = $PHPWord->loadTemplate('../../../template/numbertag'.$print['item']['numbertagtype'].'.docx');
}
else{
	$document = $PHPWord->loadTemplate('../../../template/numbertag.docx');
}

$document->setValue('story','統計錢櫃金額('.$_POST['machine'].')<w:br/>'.$_POST['bizdate'].'-'.$_POST['zcounter']);
$document->setValue('datetime',$date.'<w:br/>'.$time);

$table='';
$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';

for($i=0;$i<sizeof($_POST['coinvalue']);$i++){
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

	$table .= $_POST['coinvalue'][$i].'元 X ';

	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
	
	$table .= $_POST['coinnumber'][$i];

	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
}

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "實收小計";

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= $_POST['total'];

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "應有金額";

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= $_POST['previewmoney'];

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

/*$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "├現金收入";

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= $_POST['TAX2'];

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "└其他收/支";

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= $_POST['outmoney'];

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";*/

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "差額";

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= $_POST['diffmoney'];

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:lineRule="auto" w:line="480"/><w:jc w:val="left"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "收銀人員簽名：";

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:lineRule="auto" w:line="480"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="left"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:lineRule="auto" w:line="480"/><w:jc w:val="left"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "覆核人員簽名：";

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="right"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:lineRule="auto" w:line="480"/><w:jc w:val="right"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';

$table .= "</w:t></w:r></w:p></w:tc>";
$table .= "</w:tr>";

$table .= '</w:tbl>';

$document->setValue('type',$table);

$document->replaceStrToQrcode('qrcode','empty');

$filename=preg_replace('/\//','',$date).preg_replace('/:/','',$time)."_moneytag".$_POST['machine'];
$document->save("../../../print/read/".$filename.".docx");
if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
	$prt=fopen("../../../print/noread/".$filename.".".$_POST['machinetype'],'w');
}
else{
	$prt=fopen("../../../print/noread/".$filename.".prt",'w');
}
fclose($prt);
?>