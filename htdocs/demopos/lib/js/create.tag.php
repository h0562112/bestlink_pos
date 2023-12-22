<?php
include_once '../../../tool/myerrorlog.php';
//3225
require_once '../../../tool/PHPWord.php';
//include '../tool/dbTool.inc.php';
$content=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);
//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');

$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
$unit=parse_ini_file('../../../database/unit.ini',true);
if(isset($print['item']['textfont'])){
}
else{
	$print['item']['textfont']="微軟正黑體";
}
if(isset($print['item']['tastefront'])){
}
else{
	$print['item']['tastefront']="1";
}
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
if(isset($_POST['consecnumber'])&&$_POST['consecnumber']==''){
	$consecnumber=$machinedata['basic']['consecnumber'];
}
else{
	$consecnumber=$_POST['consecnumber'];
}
$setup=parse_ini_file('../../../database/setup.ini',true);
if(isset($setup['basic']['storyname'])){
}
else{
	$setup['basic']['storyname']='';
}
$menu=parse_ini_file('../../../database/'.$setup['basic']['company'].'-menu.ini',true);
$butname=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
if(isset($_POST['looptype'])){
	$looptype=$_POST['looptype'];
}
else{
	//$looptype=$content['init']['listprint'];
}
//$no=$_POST['typename'].$machinedata['basic']['saleno'];
$index=1;
$totalqty=0;
for($i=0;$i<sizeof($_POST['order']);$i++){
	if(isset($print['tag']['replacename'])&&$print['tag']['replacename']=='1'){//2021/9/2 列印品項名稱以0>>原品項名稱1>>列印名稱為主
		if(isset($menu[$_POST['no'][$i]]['printname'])&&$menu[$_POST['no'][$i]]['printname']!=''){
			$_POST['name'][$i]=$menu[$_POST['no'][$i]]['printname'];
		}
		else{
		}
		//2022/5/18 尋找價格名稱的列印名稱
		for($m=1;$m<=6;$m++){
			if($menu[$_POST['no'][$i]]['mname'.$m.'1']==$_POST['mname1'][$i]&&$menu[$_POST['no'][$i]]['money'.$m]==$_POST['unitprice'][$i]&&isset($menu[$_POST['no'][$i]]['mname'.$m.'printname'])&&$menu[$_POST['no'][$i]]['mname'.$m.'printname']!=''){//2022/5/18 尋找對應的價格名稱，並如有設定列印名稱則取代
				$_POST['mname1'][$i]=$menu[$_POST['no'][$i]]['mname'.$m.'printname'];
				break;
			}
			else{
			}
		}
	}
	else{
	}
	if(isset($_POST['tempbuytype'])&&$_POST['tempbuytype']=='2'&&isset($_POST['templistitem'][$i])){
		//$tempindex++;
	}
	else if(isset($pti[$menu[$_POST['no'][$i]]['printtype']]['tag'.substr($_POST['listtype'],0,1)])&&$pti[$menu[$_POST['no'][$i]]['printtype']]['tag'.substr($_POST['listtype'],0,1)]=='0'){
		//$tempindex++;
	}
	else{
		for($j=0;$j<$_POST['number'][$i];$j++){
			$totalqty++;
		}
	}
}

if(isset($print['item']['tag'])&&$print['item']['tag']=='1'){
	$type=0;
	//$PHPWord = new PHPWord();
	if(isset($print['item']['tagtemplate'])&&file_exists('../../../database/tag'.$print['item']['tagtemplate'].'.ini')){
		//$document = $PHPWord->loadTemplate('../../../template/tag'.$print['item']['tagtemplate'].'.docx');
		$type=$print['item']['tagtemplate'];
		//echo $type;
		$tag=parse_ini_file('../../../database/tag'.$print['item']['tagtemplate'].'.ini',true);
	}
	else{
		//$document = $PHPWord->loadTemplate('../../../template/tag.docx');
		if(file_exists('../../../database/tag.ini')){
			$tag=parse_ini_file('../../../database/tag.ini',true);
		}
		else{
		}
	}
	if(isset($print['tag']['type'])&&$print['tag']['type']!=''){
		$tagtype=$print['tag']['type'];
	}
	else{
		if(isset($tag['item']['type'])&&$tag['item']['type']!=''){
			$tagtype=$tag['item']['type'];
		}
		else{
			if(isset($print['item']['tagtype'])&&$print['item']['tagtype']!=''){
				$tagtype=$print['item']['tagtype'];
			}
			else{
				$tagtype='4030';
			}
		}
	}

	if(isset($_POST['tablenumber'])&&$_POST['tablenumber']!=""){
		$tablename='';
		if(isset($content['init']['controltable'])&&$content['init']['controltable']=='1'){//2020/3/23 開啟桌控
			if(file_exists('../../../database/floorspend.ini')){
				$tablemap=parse_ini_file('../../../database/floorspend.ini',true);
			}
			else{
			}
			if(preg_match('/,/',$_POST['tablenumber'])){//併桌
				$splittable=preg_split('/,/',$_POST['tablenumber']);
				for($sti=0;$sti<sizeof($splittable);$sti++){
					if($sti!=0){
						$tablename .= ',';
					}
					else{
					}
					if(preg_match('/-/',$splittable[$sti])){//拆桌
						$inittable=preg_split('/-/',$splittable[$sti]);
						if(isset($tablemap['Tname'][$inittable[0]])){
							$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
						}
						else{
							$tablename .= $splittable[$sti];
						}
					}
					else{
						if(isset($tablemap['Tname'][$splittable[$sti]])){
							$tablename .= $tablemap['Tname'][$splittable[$sti]];
						}
						else{
							$tablename .= $splittable[$sti];
						}
					}
				}
			}
			else{
				if(preg_match('/-/',$_POST['tablenumber'])){//拆桌
					$inittable=preg_split('/-/',$_POST['tablenumber']);
					if(isset($tablemap['Tname'][$inittable[0]])){
						$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
					}
					else{
						$tablename .= $_POST['tablenumber'];
					}
				}
				else{
					if(isset($tablemap['Tname'][$_POST['tablenumber']])){
						$tablename .= $tablemap['Tname'][$_POST['tablenumber']];
					}
					else{
						$tablename .= $_POST['tablenumber'];
					}
				}
			}
		}
		else{
			$tablename=$_POST['tablenumber'];
		}
	}
	else{
	}

	$tagcontent=array();
	for($i=0;$i<sizeof($_POST['order']);$i++){
		if(isset($print['tag']['replacename'])&&$print['tag']['replacename']=='1'){//2021/9/2 列印品項名稱以0>>原品項名稱1>>列印名稱為主
			if(isset($menu[$_POST['no'][$i]]['printname'])&&$menu[$_POST['no'][$i]]['printname']!=''){
				$_POST['name'][$i]=$menu[$_POST['no'][$i]]['printname'];
			}
			else{
			}
			//2022/5/18 尋找價格名稱的列印名稱
			for($m=1;$m<=6;$m++){
				if($menu[$_POST['no'][$i]]['mname'.$m.'1']==$_POST['mname1'][$i]&&$menu[$_POST['no'][$i]]['money'.$m]==$_POST['unitprice'][$i]&&isset($menu[$_POST['no'][$i]]['mname'.$m.'printname'])&&$menu[$_POST['no'][$i]]['mname'.$m.'printname']!=''){//2022/5/18 尋找對應的價格名稱，並如有設定列印名稱則取代
					$_POST['mname1'][$i]=$menu[$_POST['no'][$i]]['mname'.$m.'printname'];
					break;
				}
				else{
				}
			}
		}
		else{
		}
		if(isset($_POST['templistitem'][$i])){//2020/8/14 移除判斷參數isset($_POST['tempbuytype'])&&$_POST['tempbuytype']=='2'，無論暫結或結帳都只印出加點品項，該參數為設定暫結該印出完整或加點品項，因為結帳強迫明細印出完整品項，所以該參數會被強制修改。
			//$tempindex++;
		}
		else if(isset($pti[$menu[$_POST['no'][$i]]['printtype']]['tag'.substr($_POST['listtype'],0,1)])&&$pti[$menu[$_POST['no'][$i]]['printtype']]['tag'.substr($_POST['listtype'],0,1)]=='0'){
			//$tempindex++;
		}
		else{
			for($j=0;$j<$_POST['number'][$i];$j++){
				if($menu[$_POST['no'][$i]]['printtype']!=''&&$pti[$menu[$_POST['no'][$i]]['printtype']]['tag'.substr($_POST['listtype'],0,1)]=='1'){
					if(isset($tagcontent[$menu[$_POST['no'][$i]]['printtype']])){
					}
					else{
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']]='';
					}
					if(intval($type)==10){//2022/1/7 老先覺開版
						if(isset($tag)){
							//row1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
							//row1td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1219" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1219" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1219" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1219" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$index.'/'.$totalqty;
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1482" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1482" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1482" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1482" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=substr(str_pad($consecnumber,6,'0',STR_PAD_LEFT),-3);
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td3
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2299" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2299" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2299" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2299" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						date_default_timezone_set($content['init']['settime']);
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=date('m/d H:i');
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
							//row2td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/><w:gridSpan w:val="3"/><w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/><w:gridSpan w:val="3"/><w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/><w:gridSpan w:val="3"/><w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/><w:gridSpan w:val="3"/><w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='1'){
							if(isset($_POST['tablenumber'])&&$_POST['tablenumber']!=""){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$tablename;
							}
							else{
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
						}
						else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='2'){
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='3'){
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						else{
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row3
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
							//row3td1
							if(isset($menu[$_POST['no'][$i]]['tagsize'])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
							}
							if(isset($menu[$_POST['no'][$i]]['tagsize'])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else if(isset($print['item']['tagsize'])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
							}
						}
						if(isset($_POST['name'][$i])){
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name'][$i];
						}
						else{
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row4
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
							//row4td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
						
						if(isset($tag)){
							//row5
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
							//row5td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
						$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
						$temp=preg_split('/,/',$_POST['taste1name'][$i]);
						$num=preg_split('/,/',$_POST['taste1number'][$i]);
						$tt='';
						for($g=0;$g<sizeof($temp);$g++){
							$aa=preg_split('/\//',$temp[$g]);

							if(isset($aa[1])){
								if(intval($num[$g])>1){
									$aa[0]=$aa[0].'*'.$num[$g];
								}
								else{
								}
							}
							else{
							}

							if(strlen($tt)==0){
								if($tasteno[$g]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$tt='註:';
									}
									else{
									}
								}
								$tt=$aa[0];
							}
							else{
								$tt=$tt.',';
								if($tasteno[$g]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$tt=$tt.'註:';
									}
									else{
									}
								}
								$tt=$tt.$aa[0];
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
						
						$index++;
					}
					else if(intval($type)==9){
						
						if(isset($tag)){
							//row1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
							//row1td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						if(isset($tag)){
							//row1td2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='1'){
							if(isset($_POST['tablenumber'])&&$_POST['tablenumber']!=""){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$tablename;
							}
							else{
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
						}
						else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='2'){
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='3'){
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						else{
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						
						if(isset($tag)){
							//row1td3
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$index.'/'.$totalqty;
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
						
						//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
						if(isset($tag)){
							//row2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
							//row2td1
							if(isset($menu[$_POST['no'][$i]]['tagsize'])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
							}
							if(isset($menu[$_POST['no'][$i]]['tagsize'])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else if(isset($print['item']['tagsize'])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
							}
						}
						if(isset($_POST['name'][$i])){
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name'][$i];
						}
						else{
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
						if(isset($tag)){
							//row3
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
							//row3td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
						$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
						$temp=preg_split('/,/',$_POST['taste1name'][$i]);
						$num=preg_split('/,/',$_POST['taste1number'][$i]);
						$tt='';
						for($g=0;$g<sizeof($temp);$g++){
							$aa=preg_split('/\//',$temp[$g]);

							if(isset($aa[1])){
								if(intval($num[$g])>1){
									$aa[0]=$aa[0].'*'.$num[$g];
								}
								else{
								}
							}
							else{
							}

							if(strlen($tt)==0){
								if($tasteno[$g]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$tt='註:';
									}
									else{
									}
								}
								$tt=$aa[0];
							}
							else{
								$tt=$tt.',';
								if($tasteno[$g]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$tt=$tt.'註:';
									}
									else{
									}
								}
								$tt=$tt.$aa[0];
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
						
						if(isset($tag)){
							//row4
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
							//row4td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="4046" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="4046" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="4046" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="4046" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
							}
						}
						//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						if(isset($tag)){
							//row4td2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="954" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0036656A" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="954" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0036656A" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="954" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0036656A" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="954" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0036656A" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
						if(isset($tag)){
							//row5
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
							//row5td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="4"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="4"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="4"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="4"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
							}
						}
						//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
						date_default_timezone_set($content['init']['settime']);
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=date('m/d H:i').' '.$setup['basic']['storyname'];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
						$index++;
					}
					else if(intval($type)==7){
						if(isset($tag)){
							//row1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:cantSplit/><w:trHeight w:hRule="exact" w:val="1120"/></w:trPr>';
							//row1td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row1width'].'" w:type="pct"/><w:vAlign w:val="top"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="374" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='1'){
							if(isset($_POST['tablenumber'])&&$_POST['tablenumber']!=""){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$tablename;
							}
							else{
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
						}
						else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='2'){
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='3'){
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						else{
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='-'.str_pad($index, 2, "0", STR_PAD_LEFT);
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row2width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="280" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name'][$i];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td3
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row3width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="290" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$menu[$_POST['no'][$i]]['introtitle1'].$menu[$_POST['no'][$i]]['introduction1'];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td4
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row4width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td4size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td4size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td4size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td4size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="350" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['money'][$i];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td5
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row5width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td5size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td5size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td5size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td5size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="800" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
						$temp=preg_split('/,/',$_POST['taste1name'][$i]);
						$num=preg_split('/,/',$_POST['taste1number'][$i]);
						$tt='';
						for($g=0;$g<sizeof($temp);$g++){
							$aa=preg_split('/\//',$temp[$g]);

							if(isset($aa[1])){
								if(intval($num[$g])>1){
									$aa[0]=$aa[0].'*'.$num[$g];
								}
								else{
								}
							}
							else{
							}

							if(strlen($tt)==0){
								if($tasteno[$g]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$tt='註:';
									}
									else{
									}
								}
								$tt=$aa[0];
							}
							else{
								$tt=$tt.',';
								if($tasteno[$g]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$tt=$tt.'註:';
									}
									else{
									}
								}
								$tt=$tt.$aa[0];
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td6
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row6width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td6size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td6size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td6size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td6size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="260" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						date_default_timezone_set($content['init']['settime']);
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=date('m/d H:i');
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td7
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row7width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td7size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td7size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td7size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td7size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="260" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';

						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';			

						if(isset($tag)){
							//row2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:cantSplit/><w:trHeight w:hRule="exact" w:val="320"/></w:trPr>';
							//row2td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row1width'].'" w:type="pct"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:cantSplit/><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="280" w:type="pct"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						for($tcindex=2;$tcindex<=7;$tcindex++){
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row'.$tcindex.'width'].'" w:type="pct"/><w:vMerge/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="004E01B5" w:rsidP="004E01B5" w:rsidRDefault="004E01B5"><w:pPr><w:spacing w:lineRule="atLeast" w:line="0"/></w:pPr></w:p></w:tc>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';						

						$index++;
					}
					else if(intval($type)==6){
						if(isset($tag)){
							//row1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:cantSplit/><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
							//row1td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="973" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="973" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						if(isset($tag)){
							//row1td2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1909" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1909" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='1'){
							if(isset($_POST['tablenumber'])&&$_POST['tablenumber']!=""){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$tablename;
							}
							else{
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
						}
						else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='2'){
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='3'){
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						else{
							if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
							}
							else{
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						if(isset($tag)){
							//row1td3
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2118" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2118" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=str_pad($index, 2, "0", STR_PAD_LEFT).'/'.str_pad($totalqty, 2, "0", STR_PAD_LEFT);
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row2
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
							//row2td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name'][$i];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row3
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
							//row3td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name2'][$i];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row4
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
							//row4td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row5
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
							//row5td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
						$temp=preg_split('/,/',$_POST['taste1name'][$i]);
						$num=preg_split('/,/',$_POST['taste1number'][$i]);
						$tt='';
						for($g=0;$g<sizeof($temp);$g++){
							$aa=preg_split('/\//',$temp[$g]);

							if(isset($aa[1])){
								if(intval($num[$g])>1){
									$aa[0]=$aa[0].'*'.$num[$g];
								}
								else{
								}
							}
							else{
							}

							if(strlen($tt)==0){
								if($tasteno[$g]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$tt='註:';
									}
									else{
									}
								}
								$tt=$aa[0];
							}
							else{
								$tt=$tt.',';
								if($tasteno[$g]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$tt=$tt.'註:';
									}
									else{
									}
								}
								$tt=$tt.$aa[0];
							}
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row6
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row6height'].'"/></w:trPr>';
							//row6td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row6td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row6td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row6td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row6td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						date_default_timezone_set($content['init']['settime']);
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=date('m/d H:i');
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row7
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row7height'].'"/></w:trPr>';
							//row7td1
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row7td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row7td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row7td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row7td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

						$index++;
					}
					else{
						if(intval($type)==5||intval($type)==8){
							if(isset($tag)){
								//row1
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
								//row1td1
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
							}
							if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='1'){
								if(isset($_POST['tablenumber'])&&$_POST['tablenumber']!=""){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$tablename;
								}
								else{
									if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
									}
								}
							}
							else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='2'){
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
							else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='3'){
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
							else{
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
							if(isset($tag)){
								//row1td2
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
							}
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=str_pad($index, 2, "0", STR_PAD_LEFT).'/'.str_pad($totalqty, 2, "0", STR_PAD_LEFT);
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
						}
						else{
							if(isset($tag)){
								//row1
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
								//row1td1
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
							}
							if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='1'){
								if(isset($_POST['tablenumber'])&&$_POST['tablenumber']!=""){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$tablename;
								}
								else{
									if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
									}
								}
							}
							else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='2'){
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
							else if(isset($_POST['listtype'])&&substr($_POST['listtype'],0,1)=='3'){
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
							else{
								if(isset($_POST['saleno'])&&$_POST['saleno']!=''){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$_POST['saleno'];
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$butname['name']['listtype'.substr($_POST['listtype'],0,1)].$machinedata['basic']['saleno'];
								}
							}
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
							if(intval($type)>0){
								if(intval($type)==1||intval($type)==3){
									if(isset($tag)){
										//row1td2
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
									
									if(isset($tag)){
										//row1td3
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$index.'/'.$totalqty;
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								}
								else if(intval($type)==2||intval($type)==4){
									if(isset($tag)){
										//row1td2
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3123" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3123" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3123" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3123" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									date_default_timezone_set($content['init']['settime']);
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=str_pad($index, 2, "0", STR_PAD_LEFT).'/'.str_pad($totalqty, 2, "0", STR_PAD_LEFT).'  '.date('H:i');
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								}
							}
							else{
								if(isset($tag)){
									//row1td2
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								
								if(isset($tag)){
									//row1td3
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$index.'/'.$totalqty;
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
							}
						}
						
						$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
						
						//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
						if(intval($type)==4){
							if(isset($tag)){
								//row2
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
								//row2td1
								if(isset($menu[$_POST['no'][$i]]['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($print['item']['tagsize'])){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
									}
								}
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								if(isset($menu[$_POST['no'][$i]]['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							if(isset($_POST['name'][$i])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name'][$i];
							}
							else{
							}
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							if(intval($type)>0){
								if(isset($tag)){
									//row3
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row4
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
								$temp=preg_split('/,/',$_POST['taste1name'][$i]);
								$num=preg_split('/,/',$_POST['taste1number'][$i]);
								$tt='';
								for($g=0;$g<sizeof($temp);$g++){
									$aa=preg_split('/\//',$temp[$g]);

									if(isset($aa[1])){
										if(intval($num[$g])>1){
											$aa[0]=$aa[0].'*'.$num[$g];
										}
										else{
										}
									}
									else{
									}

									if(strlen($tt)==0){
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt='註:';
											}
											else{
											}
										}
										$tt=$aa[0];
									}
									else{
										$tt=$tt.',';
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt=$tt.'註:';
											}
											else{
											}
										}
										$tt=$tt.$aa[0];
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row5
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
									//row5td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							}
							else{
								if(isset($tag)){
									//row3
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
								$temp=preg_split('/,/',$_POST['taste1name'][$i]);
								$num=preg_split('/,/',$_POST['taste1number'][$i]);
								$tt='';
								for($g=0;$g<sizeof($temp);$g++){
									$aa=preg_split('/\//',$temp[$g]);

									if(isset($aa[1])){
										if(intval($num[$g])>1){
											$aa[0]=$aa[0].'*'.$num[$g];
										}
										else{
										}
									}
									else{
									}

									if(strlen($tt)==0){
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt='註:';
											}
											else{
											}
										}
										$tt=$aa[0];
									}
									else{
										$tt=$tt.',';
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt=$tt.'註:';
											}
											else{
											}
										}
										$tt=$tt.$aa[0];
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row4
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								if(isset($tag)){
									//row4td2
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							}
							$index++;
						}
						else if(intval($type)==5){
							if(isset($tag)){
								//row2
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
								//row2td1
								if(isset($menu[$_POST['no'][$i]]['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($print['item']['tagsize'])){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
									}
								}
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								if(isset($menu[$_POST['no'][$i]]['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							if(isset($_POST['name'][$i])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name'][$i];
							}
							else{
							}
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							if(intval($type)>0){
								if(isset($tag)){
									//row3
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row4
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
								$temp=preg_split('/,/',$_POST['taste1name'][$i]);
								$num=preg_split('/,/',$_POST['taste1number'][$i]);
								$tt='';
								for($g=0;$g<sizeof($temp);$g++){
									$aa=preg_split('/\//',$temp[$g]);

									if(isset($aa[1])){
										if(intval($num[$g])>1){
											$aa[0]=$aa[0].'*'.$num[$g];
										}
										else{
										}
									}
									else{
									}

									if(strlen($tt)==0){
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt='註:';
											}
											else{
											}
										}
										$tt=$aa[0];
									}
									else{
										$tt=$tt.',';
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt=$tt.'註:';
											}
											else{
											}
										}
										$tt=$tt.$aa[0];
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							}
							else{
								if(isset($tag)){
									//row3
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
								$temp=preg_split('/,/',$_POST['taste1name'][$i]);
								$num=preg_split('/,/',$_POST['taste1number'][$i]);
								$tt='';
								for($g=0;$g<sizeof($temp);$g++){
									$aa=preg_split('/\//',$temp[$g]);

									if(isset($aa[1])){
										if(intval($num[$g])>1){
											$aa[0]=$aa[0].'*'.$num[$g];
										}
										else{
										}
									}
									else{
									}

									if(strlen($tt)==0){
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt='註:';
											}
											else{
											}
										}
										$tt=$aa[0];
									}
									else{
										$tt=$tt.',';
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt=$tt.'註:';
											}
											else{
											}
										}
										$tt=$tt.$aa[0];
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row4
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								if(isset($tag)){
									//row4td2
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							}
							$index++;
						}
						else if(intval($type)==8){
							if(isset($tag)){
								//row2
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
								//row2td1
								if(isset($menu[$_POST['no'][$i]]['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($print['item']['tagsize'])){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
									}
								}
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								if(isset($menu[$_POST['no'][$i]]['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							if(isset($_POST['name'][$i])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name'][$i];
							}
							else{
							}
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							if(intval($type)>0){
								if(isset($tag)){
									//row3
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name2'][$i];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row4
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['mname1'][$i];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row5
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
									//row5td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
								$temp=preg_split('/,/',$_POST['taste1name'][$i]);
								$num=preg_split('/,/',$_POST['taste1number'][$i]);
								$tt='';
								for($g=0;$g<sizeof($temp);$g++){
									$aa=preg_split('/\//',$temp[$g]);

									if(isset($aa[1])){
										if(intval($num[$g])>1){
											$aa[0]=$aa[0].'*'.$num[$g];
										}
										else{
										}
									}
									else{
									}

									if(strlen($tt)==0){
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt='註:';
											}
											else{
											}
										}
										$tt=$aa[0];
									}
									else{
										$tt=$tt.',';
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt=$tt.'註:';
											}
											else{
											}
										}
										$tt=$tt.$aa[0];
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							}
							else{
								if(isset($tag)){
									//row3
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name2'][$i];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row4
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
								$temp=preg_split('/,/',$_POST['taste1name'][$i]);
								$num=preg_split('/,/',$_POST['taste1number'][$i]);
								$tt='';
								for($g=0;$g<sizeof($temp);$g++){
									$aa=preg_split('/\//',$temp[$g]);

									if(isset($aa[1])){
										if(intval($num[$g])>1){
											$aa[0]=$aa[0].'*'.$num[$g];
										}
										else{
										}
									}
									else{
									}

									if(strlen($tt)==0){
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt='註:';
											}
											else{
											}
										}
										$tt=$aa[0];
									}
									else{
										$tt=$tt.',';
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt=$tt.'註:';
											}
											else{
											}
										}
										$tt=$tt.$aa[0];
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row5
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
									//row5td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								if(isset($tag)){
									//row5td2
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							}
							$index++;
						}
						else{
							if(isset($tag)){
								//row2
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
								//row2td1
								if(isset($menu[$_POST['no'][$i]]['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($print['item']['tagsize'])){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
									}
								}
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								if(isset($menu[$_POST['no'][$i]]['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[$_POST['no'][$i]]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							if(isset($_POST['name'][$i])){
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$_POST['name'][$i];
							}
							else{
							}
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
							$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							if(intval($type)>0){
								if(intval($type)==1||intval($type)==2){
									if(isset($tag)){
										//row3
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
										//row3td1
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
									$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
									$temp=preg_split('/,/',$_POST['taste1name'][$i]);
									$num=preg_split('/,/',$_POST['taste1number'][$i]);
									$tt='';
									for($g=0;$g<sizeof($temp);$g++){
										$aa=preg_split('/\//',$temp[$g]);

										if(isset($aa[1])){
											if(intval($num[$g])>1){
												$aa[0]=$aa[0].'*'.$num[$g];
											}
											else{
											}
										}
										else{
										}

										if(strlen($tt)==0){
											if($tasteno[$g]!='999991'){
											}
											else{
												if($print['item']['tastefront']=='1'){
													$tt='註:';
												}
												else{
												}
											}
											$tt=$aa[0];
										}
										else{
											$tt=$tt.',';
											if($tasteno[$g]!='999991'){
											}
											else{
												if($print['item']['tastefront']=='1'){
													$tt=$tt.'註:';
												}
												else{
												}
											}
											$tt=$tt.$aa[0];
										}
									}
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
									
									if(isset($tag)){
										//row4
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
										//row4td1
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
									}
									//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
									if(isset($tag)){
										//row4td2
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
								}
								else if(intval($type)==3){
									if(isset($tag)){
										//row3
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
										//row3td1
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
									$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
									$temp=preg_split('/,/',$_POST['taste1name'][$i]);
									$num=preg_split('/,/',$_POST['taste1number'][$i]);
									$tt='';
									for($g=0;$g<sizeof($temp);$g++){
										$aa=preg_split('/\//',$temp[$g]);

										if(isset($aa[1])){
											if(intval($num[$g])>1){
												$aa[0]=$aa[0].'*'.$num[$g];
											}
											else{
											}
										}
										else{
										}

										if(strlen($tt)==0){
											if($tasteno[$g]!='999991'){
											}
											else{
												if($print['item']['tastefront']=='1'){
													$tt='註:';
												}
												else{
												}
											}
											$tt=$aa[0];
										}
										else{
											$tt=$tt.',';
											if($tasteno[$g]!='999991'){
											}
											else{
												if($print['item']['tastefront']=='1'){
													$tt=$tt.'註:';
												}
												else{
												}
											}
											$tt=$tt.$aa[0];
										}
									}
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
									
									if(isset($tag)){
										//row4
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
										//row4td1
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
									}
									//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
									date_default_timezone_set($content['init']['settime']);
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=date('Y/m/d H:i:s');
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
									if(isset($tag)){
										//row4td2
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
									if(isset($tag)){
										//row5
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
										//row5td1
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
										}
									}
									//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
								}
							}
							else{
								if(isset($tag)){
									//row3
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
								$temp=preg_split('/,/',$_POST['taste1name'][$i]);
								$num=preg_split('/,/',$_POST['taste1number'][$i]);
								$tt='';
								for($g=0;$g<sizeof($temp);$g++){
									$aa=preg_split('/\//',$temp[$g]);

									if(isset($aa[1])){
										if(intval($num[$g])>1){
											$aa[0]=$aa[0].'*'.$num[$g];
										}
										else{
										}
									}
									else{
									}

									if(strlen($tt)==0){
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt='註:';
											}
											else{
											}
										}
										$tt=$aa[0];
									}
									else{
										$tt=$tt.',';
										if($tasteno[$g]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$tt=$tt.'註:';
											}
											else{
											}
										}
										$tt=$tt.$aa[0];
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$tt;
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row4
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								if(isset($tag)){
									//row4td2
									$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[$_POST['no'][$i]]['printtype']].='</w:tr>';
							}
							$index++;
						}
					}
				}
				else{
				}
			}
		}
	}
	
	date_default_timezone_set($content['init']['settime']);
	$filename=date('YmdHis');
	if(isset($_POST['templistitem'])&&sizeof($_POST['templistitem'])==sizeof($_POST['no'])){
		$PHPWord = new PHPWord();
		if(isset($print['item']['tagtemplate'])&&file_exists('../../../template/tag'.$print['item']['tagtemplate'].$tagtype.'.docx')){
			$document = $PHPWord->loadTemplate('../../../template/tag'.$print['item']['tagtemplate'].$tagtype.'.docx');
		}
		else if(isset($print['item']['tagtemplate'])&&file_exists('../../../template/tag'.$print['item']['tagtemplate'].'.docx')){
			$document = $PHPWord->loadTemplate('../../../template/tag'.$print['item']['tagtemplate'].'.docx');
		}
		else{
			$document = $PHPWord->loadTemplate('../../../template/tag.docx');
		}
		$document->save("../../../print/read/delete_tag1.docx");
	}
	else if(isset($tagcontent)&&sizeof($tagcontent)>0){
		foreach($tagcontent as $index=>$value){
			$PHPWord = new PHPWord();
			if(isset($print['item']['tagtemplate'])&&file_exists('../../../template/tag'.$print['item']['tagtemplate'].$tagtype.'.docx')){
				$document = $PHPWord->loadTemplate('../../../template/tag'.$print['item']['tagtemplate'].$tagtype.'.docx');
			}
			else if(isset($print['item']['tagtemplate'])&&file_exists('../../../template/tag'.$print['item']['tagtemplate'].'.docx')){
				$document = $PHPWord->loadTemplate('../../../template/tag'.$print['item']['tagtemplate'].'.docx');
			}
			else{
				$document = $PHPWord->loadTemplate('../../../template/tag.docx');
			}
			$table='';
			if($print['item']['tag']=='1'&&(!isset($looptype)||(isset($looptype)&&($looptype=='1'||$looptype=='4')))){
				if(strlen($value)>0){
					if(intval($type)==10){//2022/1/7 老先覺開版
						$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="'.$tag[$tagtype]['row1width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row2width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row3width'].'"/></w:tblGrid>'.$value.'</w:tbl>';
					}
					else if(intval($type)==7){
						$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="'.$tag[$tagtype]['row1width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row2width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row3width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row4width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row5width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row6width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row7width'].'"/></w:tblGrid>'.$value.'</w:tbl>';
					}
					else if(intval($type)==9){
						$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="1200"/><w:gridCol w:w="2845"/><w:gridCol w:w="703"/><w:gridCol w:w="600"/></w:tblGrid>'.$value.'</w:tbl>';
					}
					else{
						$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>'.$value.'</w:tbl>';
					}
					$document->setValue('table',$table);
					//$document->save("../../../print/noread/".$filename."_tag_".$consecnumber.".docx");
					if(isset($_POST['machinetype'])&&strlen($_POST['machinetype'])==0){
						$document->save("../../../print/read/".$consecnumber."_tag".$index."m1_".$filename.".docx");
						if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
							$prt=fopen("../../../print/noread/".$consecnumber."_tag".$index."m1_".$filename.".".$_POST['machinetype'],'w');
						}
						else{
							$prt=fopen("../../../print/noread/".$consecnumber."_tag".$index."m1_".$filename.".prt",'w');
						}
						fclose($prt);
					}
					else{
						$document->save("../../../print/read/".$consecnumber."_tag".$index.$_POST['machinetype']."_".$filename.".docx");
						if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
							$prt=fopen("../../../print/noread/".$consecnumber."_tag".$index.$_POST['machinetype']."_".$filename.".".$_POST['machinetype'],'w');
						}
						else{
							$prt=fopen("../../../print/noread/".$consecnumber."_tag".$index.$_POST['machinetype']."_".$filename.".prt",'w');
						}
						fclose($prt);
					}
				}
				else{
					$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>'.$value.'</w:tbl>';
					$document->setValue('table',$table);
					$document->save("../../../print/read/delete_tag".$index."2_".$consecnumber.".docx");
				}
			}
			else{
				if($print['item']['tag']=='0'){//假設沒有貼紙機，所以設備初始設定不出貼紙，則不產生檔案
					$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>'.$value.'</w:tbl>';
					$document->setValue('table',$table);
					$document->save("../../../print/read/delete_tag".$index."3_".$consecnumber.".docx");
				}
				else{
					if(strlen($value)>0){
						$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>'.$value.'</w:tbl>';
						$document->setValue('table',$table);
						$document->save("../../../print/read/delete_tag".$index."4_".$consecnumber.".docx");
					}
					else{
						$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>'.$value.'</w:tbl>';
						$document->setValue('table',$table);
						$document->save("../../../print/read/delete_tag".$index."5_".$consecnumber.".docx");
					}
				}
			}
		}
	}
}
else{
}
?>