<?php
	require_once './lib/PHPWord.php';
	include_once '../tool/inilib.php';

	$PHPWord = new PHPWord();
	if(isset($_POST['member'])){
		$document = $PHPWord->loadTemplate('templatemember.docx');
		$document->setValue('member',$_POST['member'].' 感謝您的購買');
	}
	else{
		$document = $PHPWord->loadTemplate('template.docx');
	}
	date_default_timezone_set('Asia/Taipei');
	$counter=parse_ini_file('../database/'.$company.'-menu.ini',true);
	$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
	$document->setValue('tel', '(04)2473-2003');
	$document->setValue('time', date('Y/m/s H:i:s'));
	$tindex=0;

	$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2700" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "項目";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1130" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "單價";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "小計";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$sum=0;
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2700" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		if($_POST['mcounter'][$i]==1){
			if(isset($_POST['mname'][$i])&&strlen($_POST['mname'][$i])>0){
				$table .= $_POST['name'][$i].'('.$_POST['mname'][$i].')x'.$_POST['number'][$i];
			}
			else{
				$table .= $_POST['name'][$i].'x'.$_POST['number'][$i];
			}
		}
		else{
			$table .= $_POST['name'][$i].'('.$_POST['mname'][$i].')x'.$_POST['number'][$i];
		}
		$counter[$_POST['no'][$i]]['counter']=intval($counter[$_POST['no'][$i]]['counter'])-intval($_POST['number'][$i]);
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1130" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $_POST['money'][$i];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= ($_POST['money'][$i]*$_POST['number'][$i]);
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		if(strlen($_POST['selecttasteno'][$i])>0){
			$temp=preg_split('/,/',$_POST['selecttasteno'][$i]);
			for($t=$tindex;$t<$tindex+sizeof($temp);$t++){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="exact" w:val="252"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2700" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="13"/><w:szCs w:val="13"/></w:rPr><w:t>';
				$table .= '-'.$_POST['taste'][$t];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1130" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
				$table .= '-';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
				$table .= '-';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			$tindex=$tindex+sizeof($temp);
		}
		else{
		}
	}
	$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>---------------------------------------------</w:t></w:r><w:r w:rsidR="00596374"><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>--</w:t></w:r></w:p>';

	$document->setValue('item',$table);
	$document->setValue('total','NT.'.$_POST['total']);
	$filename=date("YmdHis");
	$document->save("../print/noread/list_".$filename.".docx");

	write_ini_file($counter,'../database/'.$company.'-menu.ini');

	/*$PHPWord = new PHPWord();
	$document = $PHPWord->loadTemplate('template1.docx');
	date_default_timezone_set('Asia/Taipei');
	$tindex=0;
	
	$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "項目";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "單價";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "小計";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$sum=0;
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		if($_POST['mcounter'][$i]==1){
			$table .= $_POST['name'][$i].'x'.$_POST['number'][$i];
		}
		else{
			$table .= $_POST['name'][$i].'('.$_POST['mname'][$i].')x'.$_POST['number'][$i];
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= $_POST['money'][$i];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= ($_POST['money'][$i]*$_POST['number'][$i]);
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		if(strlen($_POST['selecttasteno'][$i])>0){
			$temp=preg_split('/,/',$_POST['selecttasteno'][$i]);
			for($t=$tindex;$t<$tindex+sizeof($temp);$t++){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="exact" w:val="252"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="13"/><w:szCs w:val="13"/></w:rPr><w:t>';
				$table .= '-'.$_POST['taste'][$t];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
				$table .= '-';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
				$table .= '-';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			$tindex=$tindex+sizeof($temp);
		}
		else{
		}
	}
	$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>---------------------------------------------</w:t></w:r><w:r w:rsidR="00596374"><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>--</w:t></w:r></w:p>';

	$document->setValue('item',$table);
	include '../qrcode/phpqrcode/qrlib.php'; 
	QRcode::png('table-'.$_POST['tablenumber'],'./tablenumber'.$_POST['tablenumber'].'.jpg');
	$arrImagenes = array('./tablenumber'.$_POST['tablenumber'].'.jpg');
	$document->replaceStrToImg('qrcode', $arrImagenes);
	$document->setValue('total','NT.'.$_POST['total']);*/
	/*$filename=date("YmdHis");
	$document->save("./list/noread/clientlist_".$filename.".docx");*/

	/*include_once '../tool/dbTool.inc.php';
	date_default_timezone_set('Asia/Taipei');
	$init=parse_ini_file('../demopos/database/orderinitset.ini',true);
	$machinedata=parse_ini_file('../demopos/database/machinedata.ini',true);
	$filename='SALES_'.date('Ym');
	if(file_exists("../demopos/database/".$filename.".DB")){
	}
	else{
		copy("../demopos/database/empty.DB","../demopos/database/".$filename.".DB");
	}
	$conn=sqlconnect('../demopos/database',$filename.'.DB','','','','sqlite');
	if($init['init']['opentemp']==0){
		$sql="INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
	}
	else{
		$sql="INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,UNITPRICELINK,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
	}
	$values='';
	for($i=0;$i<sizeof($_POST['no']);$i++){
		if(strlen($values)==0){
			if(isset($_POST['tablenumber'])){
				$values='("'.$_POST['tablenumber'].'"';
			}
			else{
				$values='("'.$machinedata['basic']['terminalnumber'].'"';
			}
			if($init['init']['mainpos']==0){
				$values=$values.'"'.date('Ymd').'",';
			}
			else{
				$values=$values.'"'.$machinedata['basic']['bizdate'].'",';
			}
			$values=$values.'"'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'","'.str_pad(($i+1),3,'0',STR_PAD_LEFT).'","","","1","1","01","'.str_pad($_POST['no'][$i],16,'0',STR_PAD_LEFT).'","'.$_POST['name'][$i].'","'.str_pad($_POST['typeno'][$i],6,'0',STR_PAD_LEFT).'","'.$_POST['type'][$i].'","'.str_pad($_POST['typeno'][$i],6,'0',STR_PAD_LEFT).'","'.$_POST['type'][$i].'",';
			$temptasteno=preg_split('/,/',$_POST['selecttasteno'][$i]);
			for($j=0;$j<10;$j++){
				if(isset($temptasteno[$j])){
					$values=$values.'"'.str_pad($temptasteno[$j],6,'0',STR_PAD_LEFT).'",';
				}
				else{
					$values=$values.'"000000",';
				}
			}
			if(isset($_POST['mname'][$i])){
				$values=$values.'"'.$_POST['mname'][$i].'",';
			}
			else{
				$values=$values.'NULL,';
			}
			$values=$values.'0,'.$_POST['number'][$i].','.$_POST['unitprice'][$i].','.$_POST['money'][$i].',';
			if($init['init']['mainpos']==0){
				$values=$values.'"1",';
			}
			else{
				$values=$values.'"'.$machinedata['basic']['zcounter'].'",';
			}
			$values=$values.'NULL,"'.date('YmdHis').'")';
		}
		else{
			if(isset($_POST['tablenumber'])){
				$values=$values.',("'.$_POST['tablenumber'].'"';
			}
			else{
				$values=$values.',("'.$machinedata['basic']['terminalnumber'].'"';
			}
			if($init['init']['mainpos']==0){
				$values=$values.'"'.date('Ymd').'",';
			}
			else{
				$values=$values.'"'.$machinedata['basic']['bizdate'].'",';
			}
			$values=$values.'"'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'","'.str_pad(($i+1),3,'0',STR_PAD_LEFT).'","","","1","1","01","'.str_pad($_POST['no'][$i],16,'0',STR_PAD_LEFT).'","'.$_POST['name'][$i].'","'.str_pad($_POST['typeno'][$i],6,'0',STR_PAD_LEFT).'","'.$_POST['type'][$i].'","'.str_pad($_POST['typeno'][$i],6,'0',STR_PAD_LEFT).'","'.$_POST['type'][$i].'",';
			$temptasteno=preg_split('/,/',$_POST['taste1'][$i]);
			for($j=0;$j<10;$j++){
				if(isset($temptasteno[$j])){
					$values=$values.'"'.str_pad($temptasteno[$j],6,'0',STR_PAD_LEFT).'",';
				}
				else{
					$values=$values.'"000000",';
				}
			}
			if(isset($_POST['mname'][$i])){
				$values=$values.'"'.$_POST['mname'][$i].'",';
			}
			else{
				$values=$values.'NULL,';
			}
			$values=$values.'0,'.$_POST['number'][$i].','.$_POST['unitprice'][$i].','.$_POST['money'][$i].',';
			if($init['init']['mainpos']==0){
				$values=$values.'"1",';
			}
			else{
				$values=$values.'"'.$machinedata['basic']['zcounter'].'",';
			}
			$values=$values.'NULL,"'.date('YmdHis').'")';
		}
	}
	$sql=$sql.$values;
	$lasttime=sqlnoresponse($conn,$sql,'sqlite');
	if($init['init']['opentemp']==0){
		if(isset($_POST['tablenumber'])){
			$sql='INSERT INTO tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLQTY,SALESTTLAMT,TABLENUMBER,REMARKS,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['tablenumber'].'",';
			if($init['init']['mainpos']==0){
				$sql=$sql.'"'.date('Ymd').'",';
			}
			else{
				$sql=$sql.'"'.$machinedata['basic']['bizdate'].'",';
			}
			$sql=$sql.'"'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'","","","",'.$_POST['times'].','.$_POST['total'].',"'.$machinedata['basic']['saleno'].'",NULL,';
			if($init['init']['mainpos']==0){
				$sql=$sql.'"1",';
			}
			else{
				$sql=$sql.'"'.$machinedata['basic']['zcounter'].'",';
			}
			$sql=$sql.'"'.date('YmdHis').'")';
		}
		else{
			$sql='INSERT INTO tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLQTY,SALESTTLAMT,TABLENUMBER,REMARKS,ZCOUNTER,CREATEDATETIME) VALUES ("'.$machinedata['basic']['terminalnumber'].'",';
			if($init['init']['mainpos']==0){
				$sql=$sql.'"'.date('Ymd').'",';
			}
			else{
				$sql=$sql.'"'.$machinedata['basic']['bizdate'].'",';
			}
			$sql=$sql.'"'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'","","","",'.$_POST['times'].','.$_POST['total'].',"'.$machinedata['basic']['saleno'].'",NULL,';
			if($init['init']['mainpos']==0){
				$sql=$sql.'"1",';
			}
			else{
				$sql=$sql.'"'.$machinedata['basic']['zcounter'].'",';
			}
			$sql=$sql.'"'.date('YmdHis').'")';
		}
	}
	else{
		if(isset($_POST['tablenumber'])){
			$sql='INSERT INTO CST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLQTY,SALESTTLAMT,TABLENUMBER,REMARKS,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['tablenumber'].'",';
			if($init['init']['mainpos']==0){
				$sql=$sql.'"'.date('Ymd').'",';
			}
			else{
				$sql=$sql.'"'.$machinedata['basic']['bizdate'].'",';
			}
			$sql=$sql.'"'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'","","","",'.$_POST['times'].','.$_POST['total'].',"'.$machinedata['basic']['saleno'].'",NULL,';
			if($init['init']['mainpos']==0){
				$sql=$sql.'"1",';
			}
			else{
				$sql=$sql.'"'.$machinedata['basic']['zcounter'].'",';
			}
			$sql=$sql.'"'.date('YmdHis').'")';
		}
		else{
			$sql='INSERT INTO CST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLQTY,SALESTTLAMT,TABLENUMBER,REMARKS,ZCOUNTER,CREATEDATETIME) VALUES ("'.$machinedata['basic']['terminalnumber'].'",';
			if($init['init']['mainpos']==0){
				$sql=$sql.'"'.date('Ymd').'",';
			}
			else{
				$sql=$sql.'"'.$machinedata['basic']['bizdate'].'",';
			}
			$sql=$sql.'"'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'","","","",'.$_POST['times'].','.$_POST['total'].',"'.$machinedata['basic']['saleno'].'",NULL,';
			if($init['init']['mainpos']==0){
				$sql=$sql.'"1",';
			}
			else{
				$sql=$sql.'"'.$machinedata['basic']['zcounter'].'",';
			}
			$sql=$sql.'"'.date('YmdHis').'")';
		}
	}
	$orderdate=sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');*/







	//echo "<script>location.href='./';</script>";

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