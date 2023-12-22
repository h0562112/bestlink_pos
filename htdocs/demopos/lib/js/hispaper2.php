<?php
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$setup=parse_ini_file('../../../database/setup.ini',true);
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinename']])){
		$invmachine=$dbmapping['map'][$_POST['machinename']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}

if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(file_exists('../../syspram/paper-'.$init['init']['firlan'].'.ini')){
	$paper=parse_ini_file('../../syspram/paper-'.$init['init']['firlan'].'.ini',true);
}
else{
	$paper='-1';
}
if(isset($print['item']['textfont'])){
}
else{
	$print['item']['textfont']="微軟正黑體";
}
if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
	$filedb='../../../database/sale/SALES_'.substr($_POST['papbizdateS'],0,6).'.db';
}
else{
	$filedb='../../../database/sale/SALES_'.substr($timeini['time']['bizdate'],0,6).'.db';
}
if(file_exists($filedb)){
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$conn=sqlconnect("../../../database/sale","SALES_".substr($_POST['papbizdateS'],0,6).".db","","","","sqlite");
	}
	else{
		$conn=sqlconnect("../../../database/sale","SALES_".substr($timeini['time']['bizdate'],0,6).".db","","","","sqlite");
	}
	$sql='SELECT DISTINCT CST012.ITEMCODE AS ITEMCODE,CST012.ITEMDEPTCODE AS ITEMDEPTCODE,CST012.ITEMNAME AS ITEMNAME,SUM(CST012.QTY) AS QTY,CST012.UNITPRICELINK AS UNITPRICE,SUM(CST012.AMT+a.AMT) AS AMT FROM CST012 JOIN CST011 ON CST011.BIZDATE=CST012.BIZDATE AND CST011.ZCOUNTER=CST012.ZCOUNTER AND CST011.CONSECNUMBER=CST012.CONSECNUMBER AND NBCHKNUMBER IS NULL JOIN (SELECT * FROM CST012 WHERE CST012.ITEMCODE="item") AS a ON CST012.CONSECNUMBER=a.CONSECNUMBER AND CAST(CST012.LINENUMBER AS INT)+1=CAST(a.LINENUMBER AS INT) AND CST012.ZCOUNTER=a.ZCOUNTER WHERE CST012.BIZDATE';
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		$sql=$sql.' BETWEEN "'.$_POST['papbizdateS'].'" AND "'.$_POST['papbizdateE'].'"';
	}
	else{
		$sql=$sql.'="'.$timeini['time']['bizdate'].'"';
	}
	if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&$_POST['papbizdateS']!=''&&$_POST['papbizdateE']!=''){
		if(isset($_POST['zcounter'])&&$_POST['zcounter']!='allday'){
			$sql=$sql.' AND CST012.ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else if(isset($_POST['zcounter'])){
			//$sql=$sql.'ZCOUNTER="'.$_POST['zcounter'].'"';
		}
		else{
			$sql=$sql.' AND CST012.ZCOUNTER="'.$timeini['time']['zcounter'].'"';
		}
	}
	else{
		$sql=$sql.' AND CST012.ZCOUNTER="'.$timeini['time']['zcounter'].'"';
	}
	$sql=$sql.' AND (CST012.ITEMCODE!="list" OR CST012.ITEMCODE!="list" OR CST012.ITEMCODE!="autodis") GROUP BY CST012.ITEMNAME,CST012.UNITPRICELINK ORDER BY CST012.ITEMDEPTCODE ASC,CST012.ITEMCODE ASC';
	//echo $sql;
	$listdetail=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(sizeof($listdetail)==0){
		echo 'listdata is empty';
	}
	else{
		$date=date('Y/m/d');
		$time=date('H:i:s');
		$rearlist=array();//分析類別
		//$salemoney=0;//銷售總額
		//$itemdis=0;//單品折扣
		$listdis=0;//帳單折扣
		$totallist1=0;//帳單總數
		$money1=0;//總金額
		$totallist2=0;
		$money2=0;
		$totallist3=0;
		$money3=0;
		$sumotherpay=0;
		$cash=0;
		$cashcomm=0;
		if(file_exists('../../../database/'.$setup['basic']['company'].'-rear.ini')){
			$rearname=parse_ini_file('../../../database/'.$setup['basic']['company'].'-rear.ini',true);
			$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
			$sql='SELECT inumber,reartype FROM itemsdata WHERE inumber IN (';
			for($i=0;$i<sizeof($listdetail);$i++){
				if($i==0){
					$sql=$sql.intval($listdetail[$i]['ITEMCODE']);
				}
				else{
					$sql=$sql.','.intval($listdetail[$i]['ITEMCODE']);
				}
			}
			$sql=$sql.')';
			//echo $sql;
			$temprear=sqlquery($conn,$sql,'sqlite');
			$rearmap=array();
			//print_r($temprear);
			//print_r($listdetail);
			sqlclose($conn,'sqlite');
			if(isset($temprear)&&sizeof($temprear)>0&&isset($temprear[0]['inumber'])){
				foreach($temprear as $tr){
					$rearmap[$tr['inumber']]=$tr['reartype'];
				}
				foreach($listdetail as $ld){
					if(strlen($ld['ITEMCODE'])<15){
					}
					else{
						if(isset($rearlist[$rearmap[intval($ld['ITEMCODE'])]]['QTY'])){
							$rearlist[$rearmap[intval($ld['ITEMCODE'])]]['QTY']=intval($rearlist[$rearmap[intval($ld['ITEMCODE'])]]['QTY'])+intval($ld['QTY']);
						}
						else{
							$rearlist[$rearmap[intval($ld['ITEMCODE'])]]['QTY']=intval($ld['QTY']);
							$rearlist[$rearmap[intval($ld['ITEMCODE'])]]['name']=$rearname[$rearmap[intval($ld['ITEMCODE'])]]['name'];
						}
					}
				}
				//print_r($rearlist);
			}
			else{
			}
			
		}
		else{
		}
		
		$table='';
		if(!isset($_POST['type'])||$_POST['type']!='view'){
			$PHPWord = new PHPWord();
			if(isset($print['item']['hispapertype'])){
				if(file_exists('../../../template/historypaper'.$print['item']['hispapertype'].'.docx')){
					$document = $PHPWord->loadTemplate('../../../template/historypaper'.$print['item']['hispapertype'].'.docx');
				}
				else{
					$document = $PHPWord->loadTemplate('../../../template/historypaper.docx');
				}
			}
			else{
				$document = $PHPWord->loadTemplate('../../../template/historypaper.docx');
			}
			$document->setValue('story',$setup['basic']['storyname']);
			if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']==$_POST['papbizdateE']){
				if($_POST['zcounter']=='allday'){
					$document->setValue('bizdate',$_POST['papbizdateS']);
				}
				else{
					$document->setValue('bizdate',$_POST['papbizdateS'].'-'.$_POST['zcounter']);
				}
			}
			else if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']!=$_POST['papbizdateE']){
				$document->setValue('bizdate',$_POST['papbizdateS'].'~'.$_POST['papbizdateE']);
			}
			else{
				$document->setValue('bizdate',$timeini['time']['bizdate'].'-'.$timeini['time']['zcounter']);
			}
			if($paper!='-1'){
				$document->setValue('title',$paper['historypaper']['histitle2']);
			}
			else{
				$document->setValue('title','商品銷售彙總');
			}
			$document->setValue('date',date('Y/m/d'));
			$document->setValue('time',date('H:i:s'));
			//if($print!='-1'&&isset($print['block']['rearlist'])&&$print['block']['rearlist']=='1'){//2021/5/3 瀏覽時沒有判斷該值，因此列印時同樣不要判斷
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3161"/><w:gridCol w:w="734"/><w:gridCol w:w="1105"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['reartitle'];
				}
				else{
					$table .= "分析類別";
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:b/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['rearqty'];
				}
				else{
					$table .= "QTY";
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				foreach($rearlist as $item){
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= $item['name'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= $item['QTY'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				$table .= '</w:tbl><w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p>';
			/*}
			else{
			}*/
			//if($print!='-1'&&isset($print['block']['salelist'])&&$print['block']['salelist']=='1'){//2021/5/3 瀏覽時沒有判斷該值，因此列印時同樣不要判斷
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3161"/><w:gridCol w:w="734"/><w:gridCol w:w="1105"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['saleitem'];
				}
				else{
					$table .= 'Items';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['saleqty'];
				}
				else{
					$table .= 'QTY';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:b/><w:szCs w:val="16"/></w:rPr><w:t>';
				if($paper!='-1'){
					$table .= $paper['name']['saleamt'];
				}
				else{
					$table .= 'AMT';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$itemdeptcode='';
				foreach($listdetail as $item){
					if($itemdeptcode==''||$itemdeptcode!=$item['ITEMDEPTCODE']){
						$itemdeptcode=$item['ITEMDEPTCODE'];
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['ITEMNAME'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['QTY'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:tcBorders><w:top w:val="dashed" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['AMT'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
					else{
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00026A29"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3161" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DF79B5" w:rsidRPr="009F34C5" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['ITEMNAME'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="734" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['QTY'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1105" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00026A29" w:rsidP="005C5AF6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $item['AMT'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
				}
				$table .= '</w:tbl>';
			/*}
			else{
			}*/
			$document->setValue('data',$table);
			if((sizeof($listdetail)>0&&isset($listdetail[0]['ITEMNAME']))||(sizeof($rearlist)>0&&isset($rearlist[0]['name']))){
				//$document->save("../../../print/noread/".date('YmdHis')."_paper.docx");
				$filename=date('YmdHis');
				$document->save("../../../print/read/".$filename."_paper.docx");
				$prt=fopen("../../../print/noread/".$filename."_paper.prt",'w');
				fclose($prt);
				/*$prt=fopen("../../../print/noread/log_paper.txt",'w');
				fwrite($prt,$table);
				fclose($prt);*/
			}
			else{
				$document->save("../../../print/read/delete_paper.docx");
			}
		}
		else{
			echo '<div style="width:100%;height:100%;overflow-x:scroll;-moz-column-count: 3;-moz-column-gap: 10px;-webkit-column-count: 3;-webkit-column-gap: 10px;column-count: 3;column-gap: 10px;">';
			echo '<table style="border-collapse:collapse;text-align:right;">';
				echo '<caption>';
					echo '<table style="border-collapse:collapse;">';
						echo '<tr>
								<td nowrap="nowrap" style="text-align:center;">'.$setup['basic']['storyname'].'<td>
							</tr>
							<tr>
								<td nowrap="nowrap" style="text-align:center;">';
								if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']==$_POST['papbizdateE']){
									if($_POST['zcounter']=='allday'){
										echo $_POST['papbizdateS'];
									}
									else{
										echo $_POST['papbizdateS'].'-'.$_POST['zcounter'];
									}
								}
								else if(isset($_POST['papbizdateS'])&&isset($_POST['papbizdateE'])&&isset($_POST['zcounter'])&&$_POST['papbizdateS']!=$_POST['papbizdateE']){
									echo $_POST['papbizdateS'].'~'.$_POST['papbizdateE'];
								}
								else{
									echo $timeini['time']['bizdate'].'-'.$timeini['time']['zcounter'];
								}
								if($paper!='-1'){
									echo $paper['historypaper']['histitle2'];
								}
								else{
									echo '商品銷售彙總';
								}
							echo '</td>
							</tr>';
					echo '</table>';
				echo '</caption>';

			if(sizeof($rearlist)>0){
				echo '<tr>
						<td style="height:20px;"></td>
					</tr>';
				echo '<tr>
						<td style="text-align:left;border-top:1px solid #000000;">';
						if($paper!='-1'){
							echo $paper['name']['reartitle'];
						}
						else{
							echo "分析類別";
						}
					echo '</td>
						<td style="border-top:1px solid #000000;">';
						if($paper!='-1'){
							echo $paper['name']['rearqty'];
						}
						else{
							echo "QTY";
						}
					echo '</td>
					</tr>';
				foreach($rearlist as $item){
					echo '<tr>
							<td style="text-align:left;">'.$item['name'].'</td>
							<td>'.$item['QTY'].'</td>
						</tr>';
				}
			}
			else{
			}
			if(sizeof($listdetail)>0&&isset($listdetail[0]['ITEMNAME'])){
				echo '<tr>
						<td style="height:20px;"></td>
					</tr>';
				echo '<tr>
						<td style="text-align:left;border-top:1px solid #000000;">';
						if($paper!='-1'){
							echo $paper['name']['saleitem'];
						}
						else{
							echo 'Items';
						}
					echo '</td>
						<td style="border-top:1px solid #000000;">';
						if($paper!='-1'){
							echo $paper['name']['saleqty'];
						}
						else{
							echo 'QTY';
						}
					echo '</td>
						<td style="border-top:1px solid #000000;">';
						if($paper!='-1'){
							echo $paper['name']['saleamt'];
						}
						else{
							echo 'AMT';
						}
					echo '</td>
					</tr>';
				$itemdeptcode='';
				foreach($listdetail as $item){
					if($itemdeptcode==''||$itemdeptcode!=$item['ITEMDEPTCODE']){
						$itemdeptcode=$item['ITEMDEPTCODE'];
						echo '<tr>
								<td style="border-top:1px dashed #000000;text-align:left;">'.$item['ITEMNAME'].'</td>
								<td style="border-top:1px dashed #000000;">'.$item['QTY'].'</td>
								<td style="border-top:1px dashed #000000;">'.$item['AMT'].'</td>
							</tr>';
					}
					else{
						echo '<tr>
								<td style="text-align:left;">'.$item['ITEMNAME'].'</td>
								<td>'.$item['QTY'].'</td>
								<td>'.$item['AMT'].'</td>
							</tr>';
					}
				}
				echo '</table>';
			}
			else{
			}
		}
	}
}
else{
}
?>