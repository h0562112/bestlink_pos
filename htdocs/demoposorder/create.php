<?php
	require_once '../tool/PHPWord.php';
	//include '../tool/dbTool.inc.php';

	$PHPWord = new PHPWord();
	$document = $PHPWord->loadTemplate('template.docx');
	date_default_timezone_set('Asia/Taipei');
	$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
	$document->setValue('tel', '(04)2473-2003');
	$document->setValue('time', date('Y/m/s H:i:s'));
	
	$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="113"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "項目";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "數量";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "單價";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$sum=0;
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="342"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $_POST['name'][$i].'('.$_POST['size'][$i].')';
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $_POST['number'][$i]."份";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $_POST['price'][$i]."元";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	$table .= '</w:tbl>';

	$document->setValue('item',$table);
	$document->setValue('total',$_POST['tmoy']."元");
	$filename=date("YmdHis");
	$document->save("./list/".$filename.".docx");
	echo "<script>location.href='./';</script>";

	/*try{
	   $word = new COM("word.application") or die("Unable to instanciate Word"); 
	   echo "Strat...\n";
	   // set it to 1 to see the MS Word window (the actual opening of the document)
	   $word->Visible = 0;
	   // recommend to set to 0, disables alerts like "Do you want MS Word to be the default .. etc"
	   $word->DisplayAlerts = 0;
	   // open the word 2007-2013 document 
	   $word->Documents->Open(realpath(iconv('utf-8','big5',"D:\\\\xampp\\htdocs\\QuoteStock\\QuoteList\\".$filename."_".$_POST['name']."_".$who.".docx")));
	   // save it as word 2003
	   // $word->ActiveDocument->SaveAs('newdocument.pdf');
	   // convert word 2007-2013 to PDF
	   $word->ActiveDocument->ExportAsFixedFormat(iconv('utf-8','big5',"D:\\\\xampp\\htdocs\\QuoteStock\\pdf\\".$filename."_".$_POST['name']."_".$who.".pdf"), 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);
	   // quit the Word process
	   $word->Quit(false);
	   // clean up
	   unset($word);
	   echo "<script>location.href='./pdf/".$filename."_".$_POST['name']."_".$who.".pdf';</script>";
	   //echo "<script>location.href='./';</script>";
	}catch (Exception $e) {
		echo iconv("big5","utf-8",$e->getMessage());
	}*/
?>