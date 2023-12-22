<?php
require_once '../../../tool/PHPWord.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
if(file_exists('../../../template/pointtree.docx')){
	$print=parse_ini_file('../../../database/printlisttag.ini',true);
	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
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
	$filename=date('YmdHis')."_point";
	if(isset($_POST['message'])&&!isset($_POST['code'])){//system error
		$PHPWord = new PHPWord();
		$document = $PHPWord->loadTemplate('../../../template/pointtree.docx');
		if(isset($_POST['type'])&&isset($machinedata[$_POST['type']]['apiname'])){
			$document->setValue('title','POS＆'.$machinedata[$_POST['type']]['apiname'].'－錯誤訊息');
		}
		else{
			$document->setValue('title','錯誤訊息');
		}
		$document->setValue('datetime',date('Y/m/d H:i:s'));
		$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
		$table .= "錯誤訊息";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
		$table .= $_POST['message'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		$table .= "</w:tbl>";
		$document->setValue('dataitem',$table);
		if(isset($print['item']['point'])&&$print['item']['point']=='1'){
			$prt=fopen("../../../print/noread/".$filename.".prt",'w');
			fclose($prt);
		}
		else{
		}
		$document->save("../../../print/read/".$filename.".docx");
		$f=fopen('./pointtreelog.txt','a');
		fwrite($f,date('Y/m/d H:i:s').' --- error message:'.$_POST['message'].PHP_EOL);
		fclose($f);
	}
	else if(isset($_POST['status'])){//api return error
		if($_POST['message']=='Transfer point amount is 0'){
		}
		else{
			$PHPWord = new PHPWord();
			$document = $PHPWord->loadTemplate('../../../template/pointtree.docx');
			if(isset($_POST['type'])&&isset($machinedata[$_POST['type']]['apiname'])){
				$document->setValue('title','POS＆'.$machinedata[$_POST['type']]['apiname'].'－錯誤訊息');
			}
			else{
				$document->setValue('title','錯誤訊息');
			}
			$document->setValue('datetime',date('Y/m/d H:i:s'));
			$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= "status";
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= $_POST['status'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= "code";
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= $_POST['code'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= "message";
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= $_POST['message'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			$table .= "</w:tbl>";
			$document->setValue('dataitem',$table);
			if(isset($print['item']['point'])&&$print['item']['point']=='1'){
				$prt=fopen("../../../print/noread/".$filename.".prt",'w');
				fclose($prt);
			}
			else{
			}
			$document->save("../../../print/read/".$filename.".docx");
		}
		$f=fopen('./pointtreelog.txt','a');
		fwrite($f,date('Y/m/d H:i:s').' --- api return error message'.PHP_EOL);
		fwrite($f,'------------------- --- status:'.$_POST['status'].PHP_EOL);
		fwrite($f,'------------------- --- code:'.$_POST['code'].PHP_EOL);
		fwrite($f,'------------------- --- message:'.$_POST['message'].PHP_EOL);
		fclose($f);
	}
	else{//success
		$PHPWord = new PHPWord();
		$document = $PHPWord->loadTemplate('../../../template/pointtree.docx');
		if(isset($_POST['type'])&&isset($machinedata[$_POST['type']]['apiname'])){
			$document->setValue('title',$machinedata[$_POST['type']]['apiname'].'－集點明細');
		}
		else{
			$document->setValue('title','集點明細');
		}
		$document->setValue('datetime',date('m/d H:i'));
		$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
		if(isset($_POST['tel'])){
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= "電話";
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= substr($_POST['tel'],0,4).'***'.substr($_POST['tel'],-3);
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
		}
		else{
		}
		if(isset($_POST['give_balance'])){
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= "贈與點數";
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= $_POST['give_balance'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
		}
		else{
		}
		if(isset($_POST['user_balance'])){
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= "剩餘點數";
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$potitle.'"/><w:szCs w:val="'.$potitle.'"/></w:rPr><w:t>';
			$table .= $_POST['user_balance'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
		}
		else{
		}
		$table .= "</w:tbl>";
		$document->setValue('dataitem',$table);
		if(isset($print['item']['point'])&&$print['item']['point']=='1'){
			$prt=fopen("../../../print/noread/".$filename.".prt",'w');
			fclose($prt);
		}
		else{
		}
		$document->save("../../../print/read/".$filename.".docx");
		$f=fopen('./pointtreelog.txt','a');
		fwrite($f,date('Y/m/d H:i:s').' --- success'.PHP_EOL);
		if(isset($_POST['tel'])){
			fwrite($f,'------------------- --- tel:'.$_POST['tel'].PHP_EOL);
		}
		else{
		}
		if(isset($_POST['give_balance'])){
			fwrite($f,'------------------- --- give_balance:'.$_POST['give_balance'].PHP_EOL);
		}
		else{
		}
		if(isset($_POST['user_balance'])){
			fwrite($f,'------------------- --- user_balance:'.$_POST['user_balance'].PHP_EOL);
		}
		else{
		}
		fclose($f);
	}
}
else{
	$f=fopen('./pointtreelog.txt','a');
	fwrite($f,date('Y/m/d H:i:s').' --- template is not exists.(./template/pointtree.docx)'.PHP_EOL);
	fclose($f);
}
?>