<?php
//4030
require_once '../../../tool/PHPWord.php';
//include '../tool/dbTool.inc.php';
$content=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);
//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');

$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
if(isset($_POST['consecnumber'])&&$_POST['consecnumber']==''){
	$consecnumber=$machinedata['basic']['consecnumber'];
}
else{
	$consecnumber=$_POST['consecnumber'];
}
$setup=parse_ini_file('../../../database/setup.ini',true);
$menu=parse_ini_file('../../../database/'.$setup['basic']['company'].'-menu.ini',true);
if(isset($_POST['looptype'])){
	$looptype=$_POST['looptype'];
}
else{
	$looptype=$content['init']['listprint'];
}
$no=$_POST['typename'].$machinedata['basic']['saleno'];
$index=1;
if(isset($print['item']['tag'])&&$print['item']['tag']=='1'){
	$PHPWord = new PHPWord();
	$document = $PHPWord->loadTemplate('../../../template/tag.docx');
	$item='';
	for($i=0;$i<sizeof($_POST['order']);$i++){
		for($j=0;$j<$_POST['number'][$i];$j++){
			if($menu[$_POST['no'][$i]]['printtype']!=''&&$pti[$menu[$_POST['no'][$i]]['printtype']]['tag'.$_POST['listtype']]=='1'){
				//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
				$item.='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
				//$item.='<w:tc><w:tcPr><w:tcW w:w="2900" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0048424D" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
				$item.='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
				$item.=$no;
				$item.='</w:t></w:r></w:p></w:tc>';
				$item.='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
				$item.=$_POST['mname1'][$i];
				$item.='</w:t></w:r></w:p></w:tc>';
				$item.='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
				$item.=$index.'/'.$_POST['totalnumber'];
				$item.='</w:t></w:r></w:p></w:tc>';
				$item.='</w:tr>';
				
				//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
				$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
				//$item.='<w:tc><w:tcPr><w:tcW w:w="2900" w:type="dxa"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0048424D" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
				$item.='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="28"/><w:szCs w:val="28"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="28"/><w:szCs w:val="28"/></w:rPr><w:t>';
				if(isset($_POST['name'][$i])){
					$item.=$_POST['name'][$i];
				}
				else{
				}
				$item.='</w:t></w:r></w:p></w:tc>';
				$item.='</w:tr>';
				
				//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
				$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
				//$item.='<w:tc><w:tcPr><w:tcW w:w="2900" w:type="dxa"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0048424D" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
				$item.='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
				$temp=preg_split('/,/',$_POST['taste1name'][$i]);
				$num=preg_split('/,/',$_POST['taste1number'][$i]);
				$tt='';
				for($g=0;$g<sizeof($temp);$g++){
					$aa=preg_split('/\//',$temp[$g]);
					if(strlen($tt)==0){
						$tt=$aa[0];
						if(intval($num[$g])>1){
							$tt=$tt.'*'.$num[$g];
						}
						else{
						}
					}
					else{
						$tt=$tt.','.$aa[0];
						if(intval($num[$g])>1){
							$tt=$tt.'*'.$num[$g];
						}
						else{
						}
					}
				}
				$item.=$tt;
				$item.='</w:t></w:r></w:p></w:tc>';
				$item.='</w:tr>';
				
				//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
				$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
				//$item.='<w:tc><w:tcPr><w:tcW w:w="1673" w:type="dxa"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0048424D" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
				$item.='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
				$item.=$print['item']['taghint'];
				$item.='</w:t></w:r></w:p></w:tc>';
				$item.='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
				$item.=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
				$item.='</w:t></w:r></w:p></w:tc>';
				$item.='</w:tr>';
				$index++;
			}
			else{
			}
		}
	}

	$filename=date('YmdHis');
	if($print['item']['tag']=='1'&&($looptype=='1'||$looptype=='4')){
		if(strlen($item)>0){
			$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>';
			$table.=$item;
			$table.='</w:tbl>';
			$document->setValue('table',$table);
			$document->save("../../../print/noread/".$filename."_tag_".intval($consecnumber).".docx");
		}
		else{
			$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>';
			$table.=$item;
			$table.='</w:tbl>';
			$document->setValue('table',$table);
			$document->save("../../../print/read/delete_tag_".intval($consecnumber).".docx");
		}
	}
	else{
		if($print['item']['tag']=='0'){//假設沒有貼紙機，所以設備初始設定不出貼紙，則不產生檔案
			$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>';
			$table.=$item;
			$table.='</w:tbl>';
			$document->setValue('table',$table);
			$document->save("../../../print/read/delete_tag_".intval($consecnumber).".docx");
		}
		else{
			if(strlen($item)>0){
				$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>';
				$table.=$item;
				$table.='</w:tbl>';
				$document->setValue('table',$table);
				$document->save("../../../print/read/delete_tag_".intval($consecnumber).".docx");
			}
			else{
				$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>';
				$table.=$item;
				$table.='</w:tbl>';
				$document->setValue('table',$table);
				$document->save("../../../print/read/delete_tag_".intval($consecnumber).".docx");
			}
		}
	}
}
else{
}
?>