<?php
include_once '../../../tool/myerrorlog.php';
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$content=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT * FROM voiditem WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND TERMINALNUMBER="'.$_POST['machine'].'-'.$_POST['tablenumber'].'" AND STATE=1';
$voidlist=sqlquery($conn,$sql,'sqlite');
$sql='SELECT saleno FROM salemap WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'"';
$saleno=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT inumber,isgroup FROM itemsdata';
$isgroupset=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$igs=array();
foreach($isgroupset as $v){
	$igs[intval($v['inumber'])]=intval($v['isgroup']);
}
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(sizeof($voidlist)>0&&isset($print['item']['voidkitchen'])&&$print['item']['voidkitchen']==1){
	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
	if(isset($_POST['consecnumber'])&&$_POST['consecnumber']==''){
		$consecnumber=$machinedata['basic']['consecnumber'];
	}
	else{
		$consecnumber=$_POST['consecnumber'];
	}
	$data=parse_ini_file('../../../database/setup.ini',true);
	
	$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
	//$oinvlisttype=parse_ini_file('../../syspram/buttons-1.ini',true);
	if(file_exists('../../syspram/kitchen-'.$content['init']['firlan'].'.ini')){
		$ininame=parse_ini_file('../../syspram/kitchen-'.$content['init']['firlan'].'.ini',true);
	}
	else{
		$ininame='-1';
	}
	$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
	$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
	$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
	$looptype=substr($voidlist[0]['REMARKS'],0,1);


	if(isset($print['item']['kittype'])&&($print['item']['kittype']=='2'||$print['item']['kittype']=='3')&&file_exists('../../../database/itemprinttype.ini')){//廚房分類單
		$conarray=array();
		$itemlist=array();//產品列表暫存(彙總或不彙總)
		//[順序編號1]['no']、[順序編號1]['name'].....
		//[順序編號2]['no']、[順序編號2]['name'].....
		//....
		$tempitemlist=array();//彙總使用:利於判斷
		//['no1']['taste1','mname']=順序編號1
		//['no2']['taste2','mname']=順序編號2
		//....
		foreach($pti as $k=>$v){//設定與列印類別數量相同大小之暫存陣列
			$conarray[$k]='';
		}
		$conarray['-1']='';//系統設定為分類單，但產品無設定列印類別，視為總單之暫存
		$grtitle='';
		$subitem=-1;
		for($i=0;$i<sizeof($voidlist);$i=$i+2){
			$tastecontent='';
			$tastecontentname='';
			$tastecontentnumber='';
			for($t=1;$t<=10;$t++){
				if($voidlist[$i]['SELECTIVEITEM'.$t]==null){
					break;
				}
				else{
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					$temptaste=preg_split('/,/',$voidlist[$i]['SELECTIVEITEM'.$t]);
					for($j=0;$j<sizeof($temptaste);$j++){
						if(preg_match('/99999/',$temptaste[$j])){//手打備註
							if(strlen($tastecontent)==0){
								$tastecontent='99999';
								$tastecontentname=substr($temptaste[$j],7);
								$tastecontentnumber='1';
							}
							else{
								$tastecontent=$tastecontent.',99999';
								$tastecontentname=$tastecontentname.','.substr($temptaste[$j],7);
								$tastecontentnumber=$tastecontentnumber.',1';
							}
						}
						else{
							if(strlen($tastecontent)==0){
								$tastecontent=intval(substr($temptaste[$j],0,5));
								$tastecontentname=$taste[intval(substr($temptaste[$j],0,5))]['name1'];
								$tastecontentnumber=substr($temptaste[$j],5,1);
							}
							else{
								$tastecontent=$tastecontent.','.intval(substr($temptaste[$j],0,5));
								$tastecontentname=$tastecontentname.','.$taste[intval(substr($temptaste[$j],0,5))]['name1'];
								$tastecontentnumber=$tastecontentnumber.','.substr($temptaste[$j],5,1);
							}
						}
					}
				}
				/*else if(preg_match('/99999/',$voidlist[$i]['SELECTIVEITEM'.$t])){//手打備註
					if(strlen($tastecontent)==0){
						$tastecontent='99999';
						$tastecontentname=substr($voidlist[$i]['SELECTIVEITEM'.$t],7);
						$tastecontentnumber='1';
					}
					else{
						$tastecontent=$tastecontent.',99999';
						$tastecontentname=$tastecontentname.','.substr($voidlist[$i]['SELECTIVEITEM'.$t],7);
						$tastecontentnumber=$tastecontentnumber.',1';
					}
				}
				else{
					if(strlen($tastecontent)==0){
						$tastecontent=intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5));
						$tastecontentname=$taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
						$tastecontentnumber=substr($voidlist[$i]['SELECTIVEITEM'.$t],5,1);
					}
					else{
						$tastecontent=$tastecontent.','.intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5));
						$tastecontentname=$tastecontentname.','.$taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
						$tastecontentnumber=$tastecontentnumber.','.substr($voidlist[$i]['SELECTIVEITEM'.$t],5,1);
					}
				}*/
			}
			if($menu[intval($voidlist[$i]['ITEMCODE'])]['printtype']!='' && ($pti[$menu[intval($voidlist[$i]['ITEMCODE'])]['printtype']]['type']=='1' || $pti[$menu[intval($voidlist[$i]['ITEMCODE'])]['printtype']]['type']=='3')){//自動彙總(一類一單、一項一單)
				if(isset($tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]) && $itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['no']==$voidlist[$i]['ITEMCODE'] && $itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['taste1']==$tastecontent){
					//fwrite($file,'false'.PHP_EOL);
					if(isset($voidlist[$i+1])&&$voidlist[$i+1]['ITEMCODE']=='item'){
						$grtitle=$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']];
						$subitem=1;
					}
					else{
						if($subitem==-1||intval($subitem)>$igs[intval($itemlist[$grtitle]['no'])]){
							$subitem=-1;
						}
						else{
							$subitem++;
						}
					}
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['number']+=$voidlist[$i]['QTY'];
					
				}
				else{
					//echo sizeof($tempitemlist);
					$index=sizeof($tempitemlist);
					$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]=intval($index);
					if($igs[intval($voidlist[$i]['ITEMCODE'])]!="0"){
						$grtitle=$index;
						$subitem=1;
						$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['order']=$voidlist[$i]['LINENUMBER'];
					}
					else{
						if($subitem==-1||(isset($itemlist[$grtitle]['no'])&&intval($subitem)>intval($igs[$itemlist[$grtitle]['no']]))){
							$subitem=-1;
							$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['order']=$voidlist[$i]['LINENUMBER'];
						}
						else{
							$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['order']='－';
							$subitem++;
						}
					}
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['no']=intval($voidlist[$i]['ITEMCODE']);
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['name']=$voidlist[$i]['ITEMNAME'];
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['name2']='';
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['isgroup']=$igs[intval($voidlist[$i]['ITEMCODE'])];
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['mname1']=$voidlist[$i]['UNITPRICELINK'];
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['number']=$voidlist[$i]['QTY'];
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['taste1']=$tastecontent;
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['taste1name']=$tastecontentname;
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['taste1number']=$tastecontentnumber;
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE'])][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['reason']=$voidlist[$i]['reason'];
					/*foreach($tempitemlist as $a=>$b){
						//fwrite($file,'index= '.$a.';value= '.$b.PHP_EOL);
						foreach($b as $c=>$d){
							//fwrite($file,'  index= '.$c.';value= '.$d.PHP_EOL);
						}
					}*/
				}
			}
			else{
				$index=sizeof($tempitemlist);
				$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]=intval($index);
				if($igs[intval($voidlist[$i]['ITEMCODE'])]!="0"){
					$grtitle=$index;
					$subitem=1;
					$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['order']=$voidlist[0]['LINENUMBER'];
				}
				else{
					if($subitem==-1||intval($subitem)>intval($igs[$itemlist[$grtitle]['no']])){
						$subitem=-1;
						$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['order']=$voidlist[0]['LINENUMBER'];
					}
					else{
						$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['order']='－';
						$subitem++;
					}
				}
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['no']=intval($voidlist[$i]['ITEMCODE']);
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['name']=$voidlist[$i]['ITEMNAME'];
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['name2']='';
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['isgroup']=$igs[intval($voidlist[$i]['ITEMCODE'])];
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['mname1']=$voidlist[$i]['UNITPRICELINK'];
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['number']=$voidlist[$i]['QTY'];
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['taste1']=$tastecontent;
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['taste1name']=$tastecontentname;
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['taste1number']=$tastecontentnumber;
				$itemlist[$tempitemlist[intval($voidlist[$i]['ITEMCODE']).'-'.$voidlist[$i]['LINENUMBER']][$tastecontent.','.$voidlist[$i]['UNITPRICELINK']]]['reason']=$voidlist[$i]['reason'];
			}
		}

		//print_r($tempitemlist);
		//print_r($itemlist);
		//fwrite($file,'saleno= '.$saleno.PHP_EOL);
		$table='';
		$grtitle=-1;
		$grmax=-1;
		if(preg_match('/-/',$voidlist[0]['REMARKS'])){
			$tempreserve=preg_split('/;/',substr($voidlist[0]['REMARKS'],2));
		}
		else{
		}
		$kitcontent=array();
		for($i=0;$i<sizeof($itemlist);$i++){//for($i=0;$i<sizeof($_POST['no']);$i++){
			if($menu[$itemlist[$i]['no']]['printtype']!=''&&($pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='3'||$pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='4')){//一項一單
				if(!isset($kitcontent[$menu[$itemlist[$i]['no']]['printtype']])){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
				}
				else{
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:p w:rsidR="008C3284" w:rsidRDefault="008C3284"><w:pPr><w:widowControl/></w:pPr><w:r><w:br w:type="page"/></w:r></w:p>';
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
				}
				if(isset($print['kitchen']['title'])){
					$temptitle=preg_split('/,/',$print['kitchen']['title']);
					foreach($temptitle as $tempitem){
						switch($tempitem){
							case 'story':

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

								if(isset($ininame['name']['voidkitchen'])){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(".$ininame['name']['voidkitchen'].")";
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(退菜單)";
								}

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

								break;
							case 'type':

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
								
								if($_POST['tablenumber']==''){
									if(isset($tempreserve)){
										if(substr($voidlist[0]['REMARKS'],0,1)=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if(substr($voidlist[0]['REMARKS'],0,1)=='2'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if(substr($voidlist[0]['REMARKS'],0,1)=='3'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
									else{
										if($voidlist[0]['REMARKS']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if($voidlist[0]['REMARKS']=='2'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if($voidlist[0]['REMARKS']=='3'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
								}
								else{
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
									if(isset($tempreserve)){
										if($ininame!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype'.substr($remarks[0]['REMARKS'],0,1)].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype'.substr($remarks[0]['REMARKS'],0,1)].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
									}
									else{
										if($voidlist[0]['REMARKS']=='1'){
											if($ininame!='-1'){
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
										else if($voidlist[0]['REMARKS']=='2'){
											if($ininame!='-1'){
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
										else if($voidlist[0]['REMARKS']=='3'){
											if($ininame!='-1'){
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
									}
								}

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

								break;
							case 'time':

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
								
								if(isset($ininame['name']['voidman'])){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$voidlist[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['username']."\r\n".date('m/d H:i');
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$voidlist[0]['CLKNAME']."\r\n退菜人員:".$_POST['username']."\r\n".date('m/d H:i');
								}

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

								break;
							default:
								break;
						}
					}
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
				}
				else{
					//story
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

					if(isset($ininame['name']['voidkitchen'])){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(".$ininame['name']['voidkitchen'].")";
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(退菜單)";
					}

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					
					//type
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

					if($_POST['tablenumber']==''){
						if(isset($tempreserve)){
							if(substr($voidlist[0]['REMARKS'],0,1)=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else if(substr($voidlist[0]['REMARKS'],0,1)=='2'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else if(substr($voidlist[0]['REMARKS'],0,1)=='3'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
						else{
							if($voidlist[0]['REMARKS']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
							else if($voidlist[0]['REMARKS']=='2'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
							else if($voidlist[0]['REMARKS']=='3'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
						}
					}
					else{
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
						if(isset($tempreserve)){
							if($ininame!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype'.substr($remarks[0]['REMARKS'],0,1)].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype'.substr($remarks[0]['REMARKS'],0,1)].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
						}
						else{
							if($voidlist[0]['REMARKS']=='1'){
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
								}
							}
							else if($list[0]['REMARKS']=='2'){
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
								}
							}
							else if($list[0]['REMARKS']=='3'){
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
								}
							}
						}
					}

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					
					//time
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
					
					if(isset($ininame['name']['voidman'])){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$voidlist[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['username']."\r\n".date('m/d H:i');
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$voidlist[0]['CLKNAME']."\r\n退菜人員:".$_POST['username']."\r\n".date('m/d H:i');
					}

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
				}

				$tindex=0;

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "Items";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "QTY";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				$sum=0;
				if($itemlist[$i]['order']=='－'){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
					if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
					}
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2']!=''){
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'];
						}
						else{
							for($mname=1;$mname<=$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mnumber'];$mname++){
								if($itemlist[$itemlist[$i]['grtitle']]['mname1']==$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'1']){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'].'('.$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'2'].')';
								}
								else{
								}
							}
						}
					}
					else{
					}
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
					//$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				}
				else{
				}
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
				if(strlen($itemlist[$i]['mname1'])==''){
					if($itemlist[$i]['order']=='－'){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "－".$itemlist[$i]['name'];
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
					}
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'];
						}
						else{
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'];
						}
					}
					else{
					}
				}
				else{
					if($itemlist[$i]['order']=='－'){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "－".$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					}
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
						if(strlen($itemlist[$i]['mname1'])==''){
							for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
								if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
								}
								else{
								}
							}
						}
						else{
							for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
								if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
								}
								else{
								}
							}
						}
					}
					else{
					}
				}
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				if(strlen($itemlist[$i]['taste1'])>0){
					$temptasteno=preg_split('/,/',$itemlist[$i]['taste1']);
					$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
					$linetaste='';
					for($t=0;$t<sizeof($temp);$t++){
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
							$tt=preg_split('/\//',$temp[$t]);

							if($linetaste==''){
								$linetaste = '　+'.$tt[0];
							}
							else{
								$linetaste .= ','.$tt[0];
							}

							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
								$linetaste .= '/ '.$tt[1];
							}
							else{
							}
						}
						else{//備註一項一行
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
							$tt=preg_split('/\//',$temp[$t]);
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$taste[$temptasteno[$t]]['name2'];
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						}
					}
					if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '';
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					else{//備註一項一行
					}
				}
				else{
				}

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '▶'.$itemlist[$i]['reason'];
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc></w:tr>";

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
			}
			else{//if($pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='1'||$pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='2'){//一類一單
				if($menu[$itemlist[$i]['no']]['printtype']==''){
					if($itemlist[$i]['order']=='－'){
						if(in_array('-1',$atgroup)){
						}
						else{
							$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
							if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$conarray['-1'] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
							}
							else{
								$conarray['-1'] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2']!=''){
								if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
									$conarray['-1'] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'];
								}
								else{
									for($mname=1;$mname<=$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mnumber'];$mname++){
										if($itemlist[$itemlist[$i]['grtitle']]['mname1']==$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'1']){
											$conarray['-1'] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'].'('.$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'2'].')';
										}
										else{
										}
									}
								}
							}
							else{
							}
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
							//$conarray['-1'] .= $itemlist[$i]['number'];
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= "</w:tr>";
							array_push($atgroup,'-1');
						}
						$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
						$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['-1'] .= '－'.$itemlist[$i]['name'];
						}
						else{
							$conarray['-1'] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['-1'] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
									if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
										$conarray['-1'] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						$conarray['-1'] .= $itemlist[$i]['number'];
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= "</w:tr>";
					}
					else{
						$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
						$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['-1'] .= $itemlist[$i]['name'];
						}
						else{
							$conarray['-1'] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['-1'] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
									if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
										$conarray['-1'] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						$conarray['-1'] .= $itemlist[$i]['number'];
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= "</w:tr>";
					}
					
					if(strlen($itemlist[$i]['taste1'])>0){
						$temptasteno=preg_split('/,/',$itemlist[$i]['taste1']);
						$temp=preg_split(",",$itemlist[$i]['taste1name']);
						$linetaste='';
						for($t=0;$t<sizeof($temp);$t++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								$tt=preg_split('/\//',$temp[$t]);

								if($linetaste==''){
									$linetaste = '　+'.$tt[0];
								}
								else{
									$linetaste .= ','.$tt[0];
								}

								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
									$linetaste .= '/ '.$tt[1];
								}
								else{
								}
							}
							else{//備註一項一行
								$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
								//$table .= '-'.$_POST['taste1'][$t];
								$tt=preg_split('/\//',$temp[$t]);
								$conarray['-1'] .= '　+'.$tt[0];
								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
									$conarray['-1'] .= '/ '.$taste[$temptasteno[$t]]['name2'];
								}
								else{
								}
								$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$conarray['-1'] .= '';
								$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['-1'] .= "</w:tr>";
							}
						}
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
							$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$conarray['-1'] .= $linetaste;
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray['-1'] .= '';
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= "</w:tr>";
						}
						else{//備註一項一行
						}
					}
					else{
					}
					$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					$conarray['-1'] .= '▶'.$itemlist[$i]['reason'];
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc></w:tr>";
				}
				else{
					if($itemlist[$i]['order']=='－'){
						if(in_array($menu[$itemlist[$i]['no']]['printtype'],$atgroup)){
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
							if(!isset($itemlist[$itemlist[$i]['grtitle']])||strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2']!=''){
								if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'];
								}
								else{
									for($mname=1;$mname<=$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mnumber'];$mname++){
										if($itemlist[$itemlist[$i]['grtitle']]['mname1']==$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'1']){
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'].'('.$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'2'].')';
										}
										else{
										}
									}
								}
							}
							else{
							}
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
							//$conarray['-1'] .= $itemlist[$i]['number'];
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							array_push($atgroup,$menu[$itemlist[$i]['no']]['printtype']);
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'];
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
									if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					else{
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
									if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					
					if(strlen($itemlist[$i]['taste1'])>0){
						$temptasteno=preg_split('/,/',$itemlist[$i]['taste1']);
						$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
						$linetaste='';
						for($t=0;$t<sizeof($temp);$t++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								$tt=preg_split('/\//',$temp[$t]);

								if($linetaste==''){
									$linetaste = '　+'.$tt[0];
								}
								else{
									$linetaste .= ','.$tt[0];
								}

								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
									$linetaste .= '/ '.$tt[1];
								}
								else{
								}
							}
							else{//備註一項一行
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
								//$table .= '-'.$_POST['taste1'][$t];
								$tt=preg_split('/\//',$temp[$t]);
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$taste[$temptasteno[$t]]['name2'];
								}
								else{
								}
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							}
						}
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
								//$table .= '-'.$_POST['taste1'][$t];
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						}
						else{//備註一項一行
						}
					}
					else{
					}
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '▶'.$itemlist[$i]['reason'];
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc></w:tr>";
				}
			}
		}
		if(sizeof($kitcontent)>0){
			foreach($kitcontent as $printindex=>$indexcontent){
				$PHPWord = new PHPWord();
				if(isset($print['item']['kitchentype'])&&file_exists('../../../template/kitchen'.$print['item']['kitchentype'].'.docx')){
					$document = $PHPWord->loadTemplate('../../../template/kitchen'.$print['item']['kitchentype'].'.docx');
				}
				else{
					$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
				}
				$document->setValue('item',$indexcontent);
				$filename=date("YmdHis");
				if($pti[$printindex]['kitchen'.substr($voidlist[0]['REMARKS'],0,1)]=='1'){
					//$document->save("../../../print/noread/".$filename."_list".$menu[$itemlist[$i]['no']]['printtype']."_".intval($_POST['consecnumber'])."_".$i.".docx");
					$document->save("../../../print/read/".intval($_POST['consecnumber'])."_voidlist".$_POST['listtype'].$printindex."_".$filename.".docx");
					$prt=fopen("../../../print/noread/".intval($_POST['consecnumber'])."_voidlist".$_POST['listtype'].$printindex."_".$filename.".prt",'w');
					fclose($prt);
				}
				else{
					$document->save("../../../print/read/delete_voidlist".$_POST['listtype'].$printindex."_".intval($_POST['consecnumber']).".docx");
				}
			}
		}
		else{
		}
		foreach($conarray as $k=>$v){
			if($v==''){
				continue;
			}
			else{
				$PHPWord = new PHPWord();
				if(isset($print['item']['kitchentype'])&&file_exists('../../../template/kitchen'.$print['item']['kitchentype'].'.docx')){
					$document = $PHPWord->loadTemplate('../../../template/kitchen'.$print['item']['kitchentype'].'.docx');
				}
				else{
					$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
				}
				$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';

				if(isset($print['kitchen']['title'])){
					$temptitle=preg_split('/,/',$print['kitchen']['title']);
					foreach($temptitle as $tempitem){
						switch($tempitem){
							case 'story':

								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

								if(isset($ininame['name']['voidkitchen'])){
									$table .= "(".$ininame['name']['voidkitchen'].")";
								}
								else{
									$table .= "(退菜單)";
								}

								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";

								break;
							case 'type':

								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
								
								if($_POST['tablenumber']==''){
									if($voidlist[0]['REMARKS']=='1'){
										if($k=='-1'){
											$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
									else if($voidlist[0]['REMARKS']=='2'){
										if($k=='-1'){
											$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
									else if($voidlist[0]['REMARKS']=='3'){
										if($k=='-1'){
											$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
									else{
										if($k=='-1'){
											$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
								}
								else{
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
									if($voidlist[0]['REMARKS']=='1'){
										if($k=='-1'){
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$tablename."號桌";
											}
										}
									}
									else if($voidlist[0]['REMARKS']=='2'){
										if($k=='-1'){
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$tablename."號桌";
											}
										}
									}
									else if($voidlist[0]['REMARKS']=='3'){
										if($k=='-1'){
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$tablename."號桌";
											}
										}
									}
									else{
										if($k=='-1'){
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$tablename.$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$tablename."號桌";
											}
										}
									}
								}

								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";

								break;
							case 'time':

								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
								
								if(isset($ininame['name']['voidman'])){
									$table .= $_POST['consecnumber'].' '.$voidlist[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['username']."\r\n".date('m/d H:i');
								}
								else{
									$table .= $_POST['consecnumber'].' '.$voidlist[0]['CLKNAME']."\r\n退菜人員:".$_POST['username']."\r\n".date('m/d H:i');
								}

								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";

								break;
							default:
								break;
						}
					}
					$table .= '</w:tbl>';
				}
				else{
					//story
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

					if(isset($ininame['name']['voidkitchen'])){
						$table .= "(".$ininame['name']['voidkitchen'].")";
					}
					else{
						$table .= "(退菜單)";
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
					
					//type
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

					if($_POST['tablenumber']==''){
						if($voidlist[0]['REMARKS']=='1'){
							if($k=='-1'){
								$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
						else if($voidlist[0]['REMARKS']=='2'){
							if($k=='-1'){
								$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
						else if($voidlist[0]['REMARKS']=='3'){
							if($k=='-1'){
								$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
						else{
							if($k=='-1'){
								$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
					}
					else{
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
						if($voidlist[0]['REMARKS']=='1'){
							if($k=='-1'){
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$tablename."號桌";
								}
							}
						}
						else if($voidlist[0]['REMARKS']=='2'){
							if($k=='-1'){
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$tablename."號桌";
								}
							}
						}
						else if($voidlist[0]['REMARKS']=='3'){
							if($k=='-1'){
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$tablename."號桌";
								}
							}
						}
						else{
							if($k=='-1'){
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$tablename."號桌";
								}
							}
						}
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
					
					//time
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

					if(isset($ininame['name']['voidman'])){
						$table .= $_POST['consecnumber'].' '.$list[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['username']."\r\n".date('m/d H:i');
					}
					else{
						$table .= $_POST['consecnumber'].' '.$list[0]['CLKNAME']."\r\n退菜人員:".$_POST['username']."\r\n".date('m/d H:i');
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					$table .= '</w:tbl>';
				}

				$tindex=0;
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "Items";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "QTY";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$sum=0;
				$table .= $v;
				$table .= '</w:tbl>';
		
				$document->setValue('item',$table);
				//$document->setValue('total','NT.'.$_POST['total']);
				$filename=date("YmdHis");
				if($k=='-1'){
					//$document->save("../../../print/noread/".$filename."_listN_".intval($_POST['consecnumber']).".docx");
					$document->save("../../../print/read/".intval($_POST['consecnumber'])."_voidlist".$_POST['listtype']."N_".$filename.".docx");
					$prt=fopen("../../../print/noread/".intval($_POST['consecnumber'])."_voidlist".$_POST['listtype']."N_".$filename.".prt",'w');
					fclose($prt);
				}
				else{
					if($pti[$k]['kitchen'.$_POST['listtype']]=='1'){
						//$document->save("../../../print/noread/".$filename."_list".$k."_".intval($_POST['consecnumber']).".docx");
						$document->save("../../../print/read/".intval($_POST['consecnumber'])."_voidlist".$_POST['listtype'].$k."_".$filename.".docx");
						$prt=fopen("../../../print/noread/".intval($_POST['consecnumber'])."_voidlist".$_POST['listtype'].$k."_".$filename.".prt",'w');
						fclose($prt);
					}
					else{
						$document->save("../../../print/read/delete_voidlist".$_POST['listtype'].$k."_".intval($_POST['consecnumber']).".docx");
					}
				}
			}
		}
	}
	else{
	}
	if(isset($print['item']['kittype'])&&($print['item']['kittype']=='1'||$print['item']['kittype']=='3')){//廚房總單
		$PHPWord = new PHPWord();
		$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
		$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
		if(isset($print['kitchen']['title'])){
			$temptitle=preg_split('/,/',$print['kitchen']['title']);
			foreach($temptitle as $tempitem){
				switch($tempitem){
					case 'story':

						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

						if($ininame!='-1'){
							$table .= $ininame['name']['voidalllist'];
						}
						else{
							$table .= '退菜總單';
						}

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						break;
					case 'type':

						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
						
						if($_POST['tablenumber']==''){
						}
						else{
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
						if($looptype=='1'){
							if($_POST['tablenumber']==''){
								$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
								}
							}
						}
						else if($looptype=='2'){
							if($_POST['tablenumber']==''){
								$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
								}
							}
						}
						else if($looptype=='3'){
							if($_POST['tablenumber']==''){
								$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'];
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
								}
							}
						}
						else{
							if($_POST['tablenumber']==''){
								$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'];
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
								}
							}
						}

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						break;
					case 'time':

						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

						$table .= $voidlist[0]['CONSECNUMBER']." ".$voidlist[0]['CLKNAME']." ".date('m/d H:i');

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						break;
					default:
						break;
				}
			}
			$table .= '</w:tbl>';
		}
		else{
			//story
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

			if($ininame!='-1'){
				$table .= $ininame['name']['voidalllist'];
			}
			else{
				$table .= '退菜總單';
			}

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			
			//type
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
			
			if($_POST['tablenumber']==''){
			}
			else{
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
			if($looptype=='1'){
				if($_POST['tablenumber']==''){
					$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
				}
				else{
					if($ininame!='-1'){
						$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
					}
					else{
						$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
					}
				}
			}
			else if($looptype=='2'){
				if($_POST['tablenumber']==''){
					$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
				}
				else{
					if($ininame!='-1'){
						$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
					}
					else{
						$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
					}
				}
			}
			else if($looptype=='3'){
				if($_POST['tablenumber']==''){
					$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'];
				}
				else{
					if($ininame!='-1'){
						$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
					}
					else{
						$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
					}
				}
			}
			else{
				if($_POST['tablenumber']==''){
					$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'];
				}
				else{
					if($ininame!='-1'){
						$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table'];
					}
					else{
						$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌";
					}
				}
			}

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			
			//time
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

			$table .= $voidlist[0]['CONSECNUMBER']." ".$voidlist[0]['CLKNAME']." ".date('m/d H:i');

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";

			$table .= '</w:tbl>';
		}
		/*if($looptype=='1'){
			if($voidlist[0]['TERMINALNUMBER']==''){
				$document->setValue('type',$buttons['name']['listtype1']);
			}
			else{
				$document->setValue('type',$voidlist[0]['TERMINALNUMBER']."號桌\r\n".$buttons['name']['listtype1']);
			}
		}
		else if($looptype=='2'){
			if($voidlist[0]['TERMINALNUMBER']==''){
				$document->setValue('type',$buttons['name']['listtype2']);
			}
			else{
				$document->setValue('type',$voidlist[0]['TERMINALNUMBER']."號桌\r\n".$buttons['name']['listtype2']);
			}
		}
		else if($looptype=='3'){
			if($voidlist[0]['TERMINALNUMBER']==''){
				$document->setValue('type',$buttons['name']['listtype3']);
			}
			else{
				$document->setValue('type',$voidlist[0]['TERMINALNUMBER']."號桌\r\n".$buttons['name']['listtype3']);
			}
		}
		else{
			if($voidlist[0]['TERMINALNUMBER']==''){
				$document->setValue('type',$buttons['name']['listtype4']);
			}
			else{
				$document->setValue('type',$voidlist[0]['TERMINALNUMBER']."號桌\r\n".$buttons['name']['listtype4']);
			}
		}
		$document->setValue('time',date('m/d H:i'));
		$document->setValue('consecnumber',intval($consecnumber));
		//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
		//$document->setValue('tel', '(04)2473-2003');
		//$document->setValue('time', date('Y/m/s H:i:s'));
		$document->setValue('story',"退菜總單");*/
		$tindex=0;
		
		$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= "Items";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= "QTY";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		$sum=0;
		$temporderlist=1;
		//echo sizeof($_POST['no']);
		for($i=0;$i<sizeof($voidlist);$i=$i+2){
			$temporderlist=0;
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
			if(strlen($voidlist[$i]['UNITPRICELINK'])==0){
				$table .= $voidlist[$i]['ITEMNAME'];
			}
			else{
				$table .= $voidlist[$i]['ITEMNAME'].'('.$voidlist[$i]['UNITPRICELINK'].')';
			}
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
			$table .= $voidlist[$i]['QTY'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			for($t=1;$t<=10;$t++){
				if($voidlist[$i]['SELECTIVEITEM'.$t]==null){
					break;
				}
				else{
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					$temptaste=preg_split('/,/',$voidlist[$i]['SELECTIVEITEM'.$t]);
					for($j=0;$j<sizeof($temptaste);$j++){
						if(preg_match('/99999/',$temptaste[$j])){//手打備註
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$table .= '　+'.substr($temptaste[$j],7);
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$table .= '';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
						else{
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$table .= '　+'.$taste[intval(substr($temptaste[$j],0,5))]['name1'];
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$table .= '';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
					}
				}
				/*else if(preg_match('/99999/',$voidlist[$i]['SELECTIVEITEM'.$t])){//手打備註
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
					//$table .= '-'.$_POST['taste1'][$t];
					$table .= '　+'.substr($voidlist[$i]['SELECTIVEITEM'.$t],7);
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
					//$table .= '-'.$_POST['taste1'][$t];
					$table .= '　+'.$taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}*/
			}
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
			$table .= '▶'.$voidlist[$i]['reason'];
			$table .= "</w:t></w:r></w:p></w:tc></w:tr>";
		}
		$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>-----------------------------------------------</w:t></w:r></w:p>';
		
		$document->setValue('item',$table);
		//$document->setValue('total','NT.'.$_POST['total']);
		$filename=date("YmdHis");
		if($print['item']['voidkitchen']==1){
			//$document->save("../../../print/noread/".$filename."_voidlist_".intval($consecnumber).".docx");
			$document->save("../../../print/read/".$filename."_voidlist_".intval($consecnumber).".docx");
			$prt=fopen("../../../print/noread/".$filename."_voidlist_".intval($consecnumber).".prt",'w');
			fclose($prt);
		}
		else{
			$document->save("../../../print/read/delete_voidlist_".intval($consecnumber)."_".$filename.".docx");
		}
	}
	else{
	}
}
else{
}
?>