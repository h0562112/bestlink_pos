<?php
include_once '../../../tool/myerrorlog.php';
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$logtag=0;//紀錄log 1>>開啟0>>關閉
//print_r($_POST);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
if(isset($_POST['consecnumber'])&&$_POST['consecnumber']==''){
	$consecnumber=$machinedata['basic']['consecnumber'];
	$saleno=$machinedata['basic']['saleno'];
}
else{
	$consecnumber=$_POST['consecnumber'];
	$saleno=$_POST['saleno'];
}

$data=parse_ini_file('../../../database/setup.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(isset($print['kitchen']['numberfontalign'])){
}
else{
	$print['kitchen']['numberfontalign']="center";
}
if(isset($print['item']['textfont'])){
}
else{
	$print['item']['textfont']="微軟正黑體";
}
if(isset($print['kitchen']['qtysize'])){
}
else{
	$print['kitchen']['qtysize']='32';
}
if(isset($print['kitchen']['kitchensize'])){
}
else{
	$print['kitchen']['kitchensize']='28';
}
if(isset($print['kitchen']['printtypesize'])){
}
else{
	$print['kitchen']['printtypesize']='22';
}
if(isset($print['item']['tastefront'])){
}
else{
	$print['item']['tastefront']='1';
}

$content=parse_ini_file('../../../database/initsetting.ini',true);
$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
$pti['-1']['name']='';
if(file_exists('../../syspram/kitchen-'.$content['init']['firlan'].'.ini')){
	$listlan=parse_ini_file('../../syspram/kitchen-'.$content['init']['firlan'].'.ini',true);
}
else{
	$listlan='-1';
}
$saleinvdata='';
if(isset($_POST['looptype'])){
	$looptype=$_POST['looptype'];
}
else{
	//$looptype=$content['init']['listprint'];
}

//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);

$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
if(isset($_POST['listtotal'])){//2021/8/4 已結單
	$sql='SELECT CONSECNUMBER,REMARKS,RELINVOICENUMBER FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'"';
}
else{
	$sql='SELECT CONSECNUMBER,REMARKS,RELINVOICENUMBER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'"';
}
$remarks=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

if(strlen($_POST['listtype'])==1){//POS點單
	$listtype=$_POST['listtype'];
}
else{//網路預約單
	$listtype=substr($_POST['listtype'],0,1);
}

if(isset($print['item']['kittype'])&&($print['item']['kittype']=='1'||$print['item']['kittype']=='3')){//廚房總單
	$PHPWord = new PHPWord();
	if(file_exists('../../../template/kitchen'.$print['item']['kitchentype'].'.docx')){
		$document = $PHPWord->loadTemplate('../../../template/kitchen'.$print['item']['kitchentype'].'.docx');
	}
	else{
		$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
	}
	$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
	if(isset($_POST['reusername'])){//2021/8/4 補印
		$reprintlabel='(補) ';
	}
	else{
		$reprintlabel='';
	}
	if(isset($print['kitchen']['title'])){
		$temptitle=preg_split('/,/',$print['kitchen']['title']);
		foreach($temptitle as $tempitem){
			switch($tempitem){
				case 'story':

					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

					if($listlan!='-1'){
						$table .= $reprintlabel.$listlan['name']['alllistname'];
					}
					else{
						$table .= $reprintlabel.'控餐總單';
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					break;
				case 'type':

					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
					
					/*if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
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
					}*/
					if($listtype=='1'){
						//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
							$table .= $buttons['name']['listtype1'].' '.$saleno;
						/*}
						else{
							if($listlan!='-1'){
								$table .= $buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$table .= $buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename."號桌";
							}
						}*/
					}
					else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
						//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
							$table .= $buttons['name']['listtype'.$listtype].' '.$saleno;
						/*}
						else{
							if($listlan!='-1'){
								$table .= $buttons['name']['listtype2'].' '.$saleno."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$table .= $buttons['name']['listtype2'].' '.$saleno."\r\n".$tablename."號桌";
							}
						}*/
					}
					else{
						//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
							$table .= $buttons['name']['listtype4'].' '.$saleno;
						/*}
						else{
							if($listlan!='-1'){
								$table .= $buttons['name']['listtype4'].' '.$saleno."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$table .= $buttons['name']['listtype4'].' '.$saleno."\r\n".$tablename."號桌";
							}
						}*/
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					break;
				case 'table'://2020/8/31 阿倫提出：把桌號獨立出來

					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					
					if($_POST['tablenumber']==''){
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
						$tablename='';
					}
					else{
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
					if($listlan!='-1'){
						$table .= $tablename.$listlan['name']['table'];
					}
					else{
						$table .= $tablename."號桌";
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					break;
				case 'time':

					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
					
					date_default_timezone_set($content['init']['settime']);
					if(isset($_POST['reusername'])){//2021/8/4 補印
						$table .= $_POST['consecnumber'].' '.$_POST['username'];
						$table .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
					}
					else{
						$table .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					break;
				case 'numman':
					
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/></w:rPr><w:t>';
					
					$persontext="";
					if(file_exists('../../../database/floorspend.ini')){
						$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
						if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($_POST['person1']!=0||$_POST['person2']!=0||$_POST['person3']!=0)){
							if($floorspend['person1']['name']!=''&&$_POST['person1']!=0){
								if($persontext!=""){
									$persontext=$persontext.',';
								}
								else{
								}
								$persontext=$persontext.$floorspend['person1']['name'].":".$_POST['person1'];
							}
							else{
							}
							if($floorspend['person2']['name']!=''&&$_POST['person2']!=0){
								if($persontext!=""){
									$persontext=$persontext.',';
								}
								else{
								}
								$persontext=$persontext.$floorspend['person2']['name'].":".$_POST['person2'];
							}
							else{
							}
							if($floorspend['person3']['name']!=''&&$_POST['person3']!=0){
								if($persontext!=""){
									$persontext=$persontext.',';
								}
								else{
								}
								$persontext=$persontext.$floorspend['person3']['name'].":".$_POST['person3'];
							}
							else{
							}
						}
						else{
						}
					}
					else{
					}
					$table .= $persontext;

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
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

		if($listlan!='-1'){
			$table .= $reprintlabel.$listlan['name']['alllistname'];
		}
		else{
			$table .= $reprintlabel.'控餐總單';
		}

		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		
		//type
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
		
		/*if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
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
		}*/
		if($listtype=='1'){
			//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
				$table .= $buttons['name']['listtype1'].' '.$saleno;
			/*}
			else{
				if($listlan!='-1'){
					$table .= $buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$listlan['name']['table'];
				}
				else{
					$table .= $buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename."號桌";
				}
			}*/
		}
		else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
			//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
				$table .= $buttons['name']['listtype'.$listtype].' '.$saleno;
			/*}
			else{
				if($listlan!='-1'){
					$table .= $buttons['name']['listtype2'].' '.$saleno."\r\n".$tablename.$listlan['name']['table'];
				}
				else{
					$table .= $buttons['name']['listtype2'].' '.$saleno."\r\n".$tablename."號桌";
				}
			}*/
		}
		else{
			//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
				$table .= $buttons['name']['listtype4'].' '.$saleno;
			/*}
			else{
				if($listlan!='-1'){
					$table .= $buttons['name']['listtype4'].' '.$saleno."\r\n".$tablename.$listlan['name']['table'];
				}
				else{
					$table .= $buttons['name']['listtype4'].' '.$saleno."\r\n".$tablename."號桌";
				}
			}*/
		}

		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";

		//table//2020/8/31 阿倫提出：把桌號獨立出來
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		
		if($_POST['tablenumber']==''){
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
			$tablename='';
		}
		else{
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
		if($listlan!='-1'){
			$table .= $tablename.$listlan['name']['table'];
		}
		else{
			$table .= $tablename."號桌";
		}

		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		
		//time
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

		date_default_timezone_set($content['init']['settime']);
		if(isset($_POST['reusername'])){//2021/8/4 補印
			$table .= $_POST['consecnumber'].' '.$_POST['username'];
			$table .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
		}
		else{
			$table .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
		}

		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";

		/*預設不顯示人數*/

		$table .= '</w:tbl>';
	}
	
	//$document->setValue('consecnumber',$consecnumber);
	//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
	//$document->setValue('tel', '(04)2473-2003');
	//$document->setValue('time', date('Y/m/s H:i:s'));
	//if($_POST['tablenumber']==''){
		/*if($listlan!='-1'){
			$document->setValue('story',$listlan['name']['alllistname']);
		}
		else{
			$document->setValue('story','控餐總單');
		}*/
		//$document->setValue('story','控餐總單');
	/*}
	else{
		$document->setValue('story',"控餐總單");
	}*/
	$tindex=0;
	
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "Items";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "QTY";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$sum=0;
	$temporderlist=1;
	$qty=0;
	//echo sizeof($_POST['no']);
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
	for($i=0;$i<sizeof($_POST['no']);$i++){
		if(isset($print['kitchen']['replacename'])&&$print['kitchen']['replacename']=='1'){//2021/9/2 列印品項名稱以0>>原品項名稱1>>列印名稱為主
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
		if(strlen($_POST['childtype'][$i])>0){
			$tempchildtype=preg_split('/,/',$_POST['childtype'][$i]);
			$_POST['isgroup'][$i]=sizeof($tempchildtype);
		}
		else{
		}
		//fwrite($file,$_POST['consecnumber'].'-'.$consecnumber.'-'.$_POST['no'][$i].'-'.$menu[$_POST['no'][$i]]['name1'].'-'.$menu[$_POST['no'][$i]]['printtype'].PHP_EOL);
		if(isset($_POST['templistitem'][$i])){//判斷是否為"加點"項目，若是則不印單與不新增至DB
		}
		else{
			if($menu[$_POST['no'][$i]]['printtype']!='' && ($pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='1' || $pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='3')){//自動彙總(一類一單)
				//fwrite($file,$_POST['consecnumber'].'-'.$consecnumber.'-'.$_POST['no'][$i].'-'.$menu[$_POST['no'][$i]]['name1'].'-'.$menu[$_POST['no'][$i]]['printtype'].PHP_EOL);
				if(isset($tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]) && $itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']==$_POST['no'][$i] && $itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']==$_POST['taste1'][$i]){
					if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
						$grtitle=$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]];
					}
					else{
					}
					//fwrite($file,'false'.PHP_EOL);
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']+=$_POST['discount'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']+=$_POST['number'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']+=$_POST['subtotal'][$i];
					
				}
				else{
					//echo sizeof($tempitemlist);
					$index=sizeof($tempitemlist);
					if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
						$grtitle=$index;
					}
					else{
					}
					$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]=intval($index);
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['grtitle']=$grtitle;
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['order']=$_POST['order'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['typeno']=$_POST['typeno'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['type']=$_POST['type'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']=$_POST['no'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name']=$_POST['name'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name2']=$_POST['name2'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['isgroup']=$_POST['isgroup'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['childtype']=$_POST['childtype'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname1']=$_POST['mname1'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname2']=$_POST['mname2'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['unitprice']=$_POST['unitprice'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['money']=$_POST['money'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']=$_POST['discount'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discontent']=$_POST['discontent'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']=$_POST['number'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']=$_POST['subtotal'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']=$_POST['taste1'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1name']=$_POST['taste1name'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1price']=$_POST['taste1price'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1number']=$_POST['taste1number'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1money']=$_POST['taste1money'][$i];
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
				if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
					$grtitle=$index;
				}
				else{
				}
				
				$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]=intval($index);
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['grtitle']=$grtitle;
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['order']=$_POST['order'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['typeno']=$_POST['typeno'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['type']=$_POST['type'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']=$_POST['no'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name']=$_POST['name'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name2']=$_POST['name2'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['isgroup']=$_POST['isgroup'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['childtype']=$_POST['childtype'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname1']=$_POST['mname1'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname2']=$_POST['mname2'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['unitprice']=$_POST['unitprice'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['money']=$_POST['money'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']=$_POST['discount'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discontent']=$_POST['discontent'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']=$_POST['number'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']=$_POST['subtotal'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']=$_POST['taste1'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1name']=$_POST['taste1name'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1price']=$_POST['taste1price'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1number']=$_POST['taste1number'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1money']=$_POST['taste1money'][$i];
			}
		}
	}
	$groupcode=-1;
	$atgroup=array();
	//print_r($itemlist);
	for($i=0;$i<sizeof($itemlist);$i++){
		if((isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['list'.$listtype])&&$pti[$menu[$itemlist[$i]['no']]['printtype']]['list'.$listtype]=='0')){
		}
		else{
			$temporderlist=0;
			if($itemlist[$i]['order']=='－'){
				if($groupcode==-1||$groupcode!=$itemlist[$i]['grtitle']){
					$groupcode=$itemlist[$i]['grtitle'];
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					if(isset($print['kitchen']['grouptitlesize'])){
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
					}
					else{
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
					}
					if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
						$table .= $itemlist[$itemlist[$i]['grtitle']]['name'];
						if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$itemlist[$itemlist[$i]['grtitle']]['name2']!=''){
							$table .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'];
						}
						else{
						}
					}
					else{
						$table .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
						if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$itemlist[$itemlist[$i]['grtitle']]['name2']!=''){
							$table .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname2'].')';
						}
						else{
						}
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
					//$conarray['-1'] .= $itemlist[$i]['number'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
					if(strlen($itemlist[$itemlist[$i]['grtitle']]['taste1'])>0){
						$tasteno=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1']);
						$temp=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1name']);
						$temp2=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1number']);
						$linetaste='';
						for($t=0;$t<sizeof($temp);$t++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								$tt=preg_split('/\//',$temp[$t]);

								/*if(isset($tt[1])){
									if(intval($temp2[$t])>1){
										$tt[0]=$tt[0].'*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}*/

								if($linetaste==''){
									$linetaste = '　+';
									if($tasteno[$t]!='999991'){
									}
									else{
										if($print['item']['tastefront']=='1'){
											$linetaste .= '註:';
										}
										else{
										}
									}
									$linetaste .= $tt[0];
								}
								else{
									$linetaste .= ',';
									if($tasteno[$t]!='999991'){
									}
									else{
										if($print['item']['tastefront']=='1'){
											$linetaste .= '註:';
										}
										else{
										}
									}
									$linetaste .= $tt[0];
								}

								if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&isset($tt[1])&&$tt[1]!=''){
									$linetaste .= '/ '.$tt[1];
								}
								else if(isset($tt[1])&&$tt[1]!=''){
									if(intval($temp2[$t])>1){
										$linetaste .= '*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}
							}
							else{//備註一項一行
								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								if(isset($print['kitchen']['tastesize'])){
									$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
								}
								else{
									$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
								}
								//$table .= '-'.$_POST['taste1'][$t];
								$tt=preg_split('/\//',$temp[$t]);
								if($tasteno[$t]!='999991'){
									$table .= '　+'.$tt[0];
								}
								else{
									if($print['item']['tastefront']=='1'){
										$table .= '　+註:'.$tt[0];
									}
									else{
										$table .= '　+'.$tt[0];
									}
								}
								if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&isset($tt[1])&&$tt[1]!=''){
									$table .= '/ '.$tt[1];
								}
								else if(isset($tt[1])&&$tt[1]!=''){
									if(intval($temp2[$t])>1){
										$table .= '*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$table .= '';
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";
							}
							/*$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							if(isset($print['kitchen']['tastesize'])){
								$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
							//$table .= '-'.$_POST['taste1'][$t];
							$tt=preg_split('/\//',$temp[$t]);
							$table .= '　+'.$tt[0];
							if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$table .= '/ '.$tt[1];
							}
							else{
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$table .= '';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";*/
						}
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							if(isset($print['kitchen']['tastesize'])){
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
							$table .= $linetaste;
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
						else{//備註一項一行
							
						}
					}
					else{
					}
					array_push($atgroup,$itemlist[$i]['grtitle']);
				}
				else{
				}
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
				//$table .= $itemlist[$i]['name'];
				if(strlen($itemlist[$i]['mname1'])==''){
					$table .= '－'.$itemlist[$i]['name'];
					if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$itemlist[$i]['name2']!=''){
						$table .= "\r\n－".$itemlist[$i]['name2'];
					}
					else{
					}
				}
				else{
					$table .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$itemlist[$i]['name2']!=''){
						$table .= "\r\n－".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
					}
					else{
					}
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
				$table .= $itemlist[$i]['number'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
				//$table .= $itemlist[$i]['name'];
				if(strlen($itemlist[$i]['mname1'])==''){
					$table .= $itemlist[$i]['name'];
					if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$itemlist[$i]['name2']!=''){
						$table .= "\r\n".$itemlist[$i]['name2'];
					}
					else{
					}
				}
				else{
					$table .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$itemlist[$i]['name2']!=''){
						$table .= "\r\n".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
					}
					else{
					}
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
				$table .= $itemlist[$i]['number'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			if(strlen($itemlist[$i]['taste1'])>0){
				$tasteno=preg_split('/,/',$itemlist[$i]['taste1']);
				$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
				$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
				$linetaste='';
				for($t=0;$t<sizeof($temp);$t++){
					if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
						$tt=preg_split('/\//',$temp[$t]);

						/*if(isset($tt[1])){
							if(intval($temp2[$t])>1){
								$tt[0]=$tt[0].'*'.$temp2[$t];
							}
							else{
							}
						}
						else{
						}*/

						if($linetaste==''){
							$linetaste = '　+';
							if($tasteno[$t]!='999991'){
							}
							else{
								if($print['item']['tastefront']=='1'){
									$linetaste .= '註:';
								}
								else{
								}
							}
							$linetaste .= $tt[0];
						}
						else{
							$linetaste .= ',';
							if($tasteno[$t]!='999991'){
							}
							else{
								if($print['item']['tastefront']=='1'){
									$linetaste .= '註:';
								}
								else{
								}
							}
							$linetaste .= $tt[0];
						}

						if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&isset($tt[1])&&$tt[1]!=''){
							$linetaste .= '/ '.$tt[1];
						}
						else if(isset($tt[1])&&$tt[1]!=''){
							if(intval($temp2[$t])>1){
								$linetaste .= '*'.$temp2[$t];
							}
							else{
							}
						}
						else{
						}
					}
					else{//備註一項一行
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(isset($print['kitchen']['tastesize'])){
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
						}
						else{
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
						}
						//$table .= '-'.$_POST['taste1'][$t];
						$tt=preg_split('/\//',$temp[$t]);
						if($tasteno[$t]!='999991'){
							$table .= '　+'.$tt[0];
						}
						else{
							if($print['item']['tastefront']=='1'){
								$table .= '　+註:'.$tt[0];
							}
							else{
								$table .= '　+'.$tt[0];
							}
						}
						if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&isset($tt[1])&&$tt[1]!=''){
							$table .= '/ '.$tt[1];
						}
						else if(isset($tt[1])&&$tt[1]!=''){
							if(intval($temp2[$t])>1){
								$table .= '*'.$temp2[$t];
							}
							else{
							}
						}
						else{
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$table .= '';
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
					/*$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					if(isset($print['kitchen']['tastesize'])){
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
					}
					else{
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
					}
					//$table .= '-'.$_POST['taste1'][$t];
					$tt=preg_split('/\//',$temp[$t]);
					$table .= '　+'.$tt[0];
					if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&isset($tt[1])&&$tt[1]!=''){
						$table .= '/ '.$tt[1];
					}
					else{
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";*/
				}
				if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					if(isset($print['kitchen']['tastesize'])){
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
					}
					else{
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
					}
					$table .= $linetaste;
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{//備註一項一行
					
				}
			}
			else{
			}
		}
	}
	$table .= "</w:tbl>";

	$document->setValue('item',$table);
	//$document->setValue('total','NT.'.$_POST['total']);
	date_default_timezone_set($content['init']['settime']);
	$filename=date("YmdHis");
	if($temporderlist==0){//有加點項目，則印單
		if($print['item']['kitchen']!='0'&&(!isset($looptype)||(isset($looptype)&&$looptype=='1'))){
		//2022/5/27 looptype=3只出總單改成只出明細單，不印工作單與總單 倫
		//if($print['item']['kitchen']!='0'&&(!isset($looptype)||(isset($looptype)&&($looptype=='1'||$looptype=='3')))){
			//$document->save("../../../print/noread/".$filename."_list_".$consecnumber.".docx");
			$document->save("../../../print/read/".$consecnumber."_list-_".$filename.".docx");
			if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
				$prt=fopen("../../../print/noread/".$consecnumber."_list-_".$filename.".".$_POST['machinetype'],'w');
			}
			else{
				$prt=fopen("../../../print/noread/".$consecnumber."_list-_".$filename.".prt",'w');
			}
			fclose($prt);
		}
		else{
			$document->save("../../../print/read/deleteE_list.docx");
		}
	}
	else{
		$document->save("../../../print/read/deleteP_list.docx");
	}
}
else{
}

if(isset($_POST['memno'])&&strlen($_POST['memno'])!=0){
	if(isset($content['init']['onlinemember'])&&$content['init']['onlinemember']=='1'){
		if(preg_match('/(;-;)/',$_POST['memno'])){
			$tempmemno=preg_split('/(;-;)/',$_POST['memno']);
			$PostData = array(
				"type"=>"online",
				"ajax" => "",
				"company" => $data['basic']['company'],
				"memno" => $tempmemno[0]
			);
		}
		else{
			$PostData = array(
				"type"=>"online",
				"ajax" => "",
				"company" => $data['basic']['company'],
				"memno" => $_POST['memno']
			);
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php');//
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$getmemdata = curl_exec($ch);
		
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php : ' . curl_error($ch));
		}
		else{
			$memdata=json_decode($getmemdata,true);
		}
		curl_close($ch);
	}
	else{
		$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
		$sql='SELECT * FROM person WHERE memno="'.$_POST['memno'].'" AND state=1';
		$memdata=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
	}
}
else{
}

if(isset($print['item']['kittype'])&&($print['item']['kittype']=='2'||$print['item']['kittype']=='3')&&file_exists('../../../database/itemprinttype.ini')){//廚房分類單
	if(isset($_POST['reusername'])){//2021/8/4 補印
		$reprintlabel='(補) ';
	}
	else{
		$reprintlabel='';
	}
	$conarray=array();
	$conamt=array();
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
		$conarray['grouptype'][$k]='';//2020/4/22 列印類別設定為 依列印類別分類 暫存陣列
	}
	$grouptypecontent=0;
	$conarray['-1']='';//系統設定為分類單，但產品無設定列印類別，視為總單之暫存
	$grtitle='';
	for($i=0;$i<sizeof($_POST['no']);$i++){
		if(isset($print['kitchen']['replacename'])&&$print['kitchen']['replacename']=='1'){//2021/9/2 列印品項名稱以0>>原品項名稱1>>列印名稱為主
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
		if(strlen($_POST['childtype'][$i])>0){
			$tempchildtype=preg_split('/,/',$_POST['childtype'][$i]);
			$_POST['isgroup'][$i]=sizeof($tempchildtype);
		}
		else{
		}
		//fwrite($file,$_POST['consecnumber'].'-'.$consecnumber.'-'.$_POST['no'][$i].'-'.$menu[$_POST['no'][$i]]['name1'].'-'.$menu[$_POST['no'][$i]]['printtype'].PHP_EOL);
		if(isset($_POST['templistitem'][$i])){//判斷是否為"加點"項目，若是則不印單與不新增至DB
		}
		else{
			if($menu[$_POST['no'][$i]]['printtype']!='' && ($pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='1' || $pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='5')){//自動彙總(一類一單、依列印類別分類)
				//fwrite($file,$_POST['consecnumber'].'-'.$consecnumber.'-'.$_POST['no'][$i].'-'.$menu[$_POST['no'][$i]]['name1'].'-'.$menu[$_POST['no'][$i]]['printtype'].PHP_EOL);
				if(isset($tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]) && $itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']==$_POST['no'][$i] && $itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']==$_POST['taste1'][$i]){
					if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
						$grtitle=$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]];
					}
					else{
					}
					//fwrite($file,'false'.PHP_EOL);
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']+=$_POST['discount'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']+=$_POST['number'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']+=$_POST['subtotal'][$i];
					
				}
				else{
					//echo sizeof($tempitemlist);
					$index=sizeof($tempitemlist);
					if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
						$grtitle=$index;
					}
					else{
					}
					$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]=intval($index);
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['grtitle']=$grtitle;
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['order']=$_POST['order'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['typeno']=$_POST['typeno'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['type']=$_POST['type'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']=$_POST['no'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name']=$_POST['name'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name2']=$_POST['name2'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['isgroup']=$_POST['isgroup'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['childtype']=$_POST['childtype'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname1']=$_POST['mname1'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname2']=$_POST['mname2'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['unitprice']=$_POST['unitprice'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['money']=$_POST['money'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']=$_POST['discount'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discontent']=$_POST['discontent'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']=$_POST['number'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']=$_POST['subtotal'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']=$_POST['taste1'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1name']=$_POST['taste1name'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1price']=$_POST['taste1price'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1number']=$_POST['taste1number'][$i];
					$itemlist[$tempitemlist[$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1money']=$_POST['taste1money'][$i];
					/*foreach($tempitemlist as $a=>$b){
						//fwrite($file,'index= '.$a.';value= '.$b.PHP_EOL);
						foreach($b as $c=>$d){
							//fwrite($file,'  index= '.$c.';value= '.$d.PHP_EOL);
						}
					}*/
				}
			}
			else if($menu[$_POST['no'][$i]]['printtype']!='' && $pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='3'){//自動彙總(一項一單)
				//fwrite($file,$_POST['consecnumber'].'-'.$consecnumber.'-'.$_POST['no'][$i].'-'.$menu[$_POST['no'][$i]]['name1'].'-'.$menu[$_POST['no'][$i]]['printtype'].PHP_EOL);
				if(isset($tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]) && $itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']==$_POST['no'][$i] && $itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']==$_POST['taste1'][$i]){
					if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
						$grtitle=$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]];
					}
					else{
					}
					//fwrite($file,'false'.PHP_EOL);
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']+=$_POST['discount'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']+=$_POST['number'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']+=$_POST['subtotal'][$i];
					
				}
				else{
					//echo sizeof($tempitemlist);
					$index=sizeof($tempitemlist);
					if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
						$grtitle=$index;
					}
					else{
					}
					$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]=intval($index);
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['grtitle']=$grtitle;
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['order']=$_POST['order'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['typeno']=$_POST['typeno'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['type']=$_POST['type'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']=$_POST['no'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name']=$_POST['name'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name2']=$_POST['name2'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['isgroup']=$_POST['isgroup'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['childtype']=$_POST['childtype'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname1']=$_POST['mname1'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname2']=$_POST['mname2'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['unitprice']=$_POST['unitprice'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['money']=$_POST['money'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']=$_POST['discount'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discontent']=$_POST['discontent'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']=$_POST['number'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']=$_POST['subtotal'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']=$_POST['taste1'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1name']=$_POST['taste1name'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1price']=$_POST['taste1price'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1number']=$_POST['taste1number'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].','.$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1money']=$_POST['taste1money'][$i];
					/*foreach($tempitemlist as $a=>$b){
						//fwrite($file,'index= '.$a.';value= '.$b.PHP_EOL);
						foreach($b as $c=>$d){
							//fwrite($file,'  index= '.$c.';value= '.$d.PHP_EOL);
						}
					}*/
				}
			}
			else if($menu[$_POST['no'][$i]]['printtype']!='' && ($pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='7' || $pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='8' || $pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='9') && ($_POST['order'][$i]!='－' && intval($_POST['isgroup'][$i])==0)){//品項拆分(一項一單、一類一單、依列印類別分類)；不處理套餐
				for($itemqty=0;$itemqty<$_POST['number'][$i];$itemqty++){
					$index=sizeof($tempitemlist);
					if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
						$grtitle=$index;
					}
					else{
					}
					
					$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]=intval($index);
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['grtitle']=$grtitle;
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['order']=$_POST['order'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['typeno']=$_POST['typeno'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['type']=$_POST['type'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']=$_POST['no'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name']=$_POST['name'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name2']=$_POST['name2'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['isgroup']=$_POST['isgroup'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['childtype']=$_POST['childtype'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname1']=$_POST['mname1'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname2']=$_POST['mname2'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['unitprice']=$_POST['unitprice'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['money']=$_POST['money'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']=floatval($_POST['discount'][$i])/floatval($_POST['number'][$i]);
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discontent']=$_POST['discontent'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']='1';
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']=floatval($_POST['subtotal'][$i])/floatval($_POST['number'][$i]);
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']=$_POST['taste1'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1name']=$_POST['taste1name'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1price']=$_POST['taste1price'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1number']=$_POST['taste1number'][$i];
					$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i].','.$itemqty][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1money']=$_POST['taste1money'][$i];
				}
			}
			else{
				$index=sizeof($tempitemlist);
				if($_POST['isgroup'][$i]!="0"&&$_POST['isgroup'][$i]!=""){
					$grtitle=$index;
				}
				else{
				}
				
				$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]=intval($index);
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['grtitle']=$grtitle;
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['order']=$_POST['order'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['typeno']=$_POST['typeno'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['type']=$_POST['type'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['no']=$_POST['no'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name']=$_POST['name'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['name2']=$_POST['name2'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['isgroup']=$_POST['isgroup'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['childtype']=$_POST['childtype'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname1']=$_POST['mname1'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['mname2']=$_POST['mname2'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['unitprice']=$_POST['unitprice'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['money']=$_POST['money'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discount']=$_POST['discount'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['discontent']=$_POST['discontent'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['number']=$_POST['number'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['subtotal']=$_POST['subtotal'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1']=$_POST['taste1'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1name']=$_POST['taste1name'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1price']=$_POST['taste1price'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1number']=$_POST['taste1number'][$i];
				$itemlist[$tempitemlist[$_POST['linenumber'][$i].','.$_POST['no'][$i].'-'.$_POST['linenumber'][$i]][$_POST['taste1'][$i].','.$_POST['mname1'][$i]]]['taste1money']=$_POST['taste1money'][$i];
			}
		}
	}
	//print_r($tempitemlist);
	//print_r($itemlist);
	//fwrite($file,'saleno= '.$saleno.PHP_EOL);
	$grtitle=-1;
	$grmax=-1;
	$atgroup=array();
	if(isset($remarks)&&sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])&&preg_match('/-/',$remarks[0]['REMARKS'])){
		$tempreserve=preg_split('/;/',substr($remarks[0]['REMARKS'],2));
	}
	else{
	}
	if(isset($remarks)&&sizeof($remarks)>0&&isset($remarks[0]['RELINVOICENUMBER'])&&$remarks[0]['RELINVOICENUMBER']!=''){
		if($print['item']['tastefront']=='1'){
			$listhint='註：'.$remarks[0]['RELINVOICENUMBER'];
		}
		else{
		}
	}
	else{
	}
	$groupcode=-1;
	$kitcontent=array();
	//print_r($tempitemlist);
	for($i=0;$i<sizeof($itemlist);$i++){//for($i=0;$i<sizeof($_POST['no']);$i++){
		if($menu[$itemlist[$i]['no']]['printtype']!=''&&($pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='3'||$pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='4'||$pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='8')){//一項一單
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

							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $reprintlabel.$listlan['name']['listname'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $reprintlabel."廚房工作單";
							}

							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

							break;
						case 'type':

							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

							//if($_POST['tablenumber']==''){////2020/8/31 阿倫提出：把桌號獨立出來
								if($listtype=='1'){
									if(isset($tempreserve)){
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
										}
										else{
										}
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
									}
									else{
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
										}
										else{
										}
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
									}
								}
								else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
									if(isset($tempreserve)){
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
										}
										else{
										}
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
									}
									else{
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
										}
										else{
										}
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
									}
								}
								else{
									if(isset($tempreserve)){
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
										}
										else{
										}
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
									}
									else{
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
										}
										else{
										}
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
									}
								}
							/*}
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
								if($listtype=='1'){
									if(isset($tempreserve)){
										if($listlan!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
									}
									else{
										if($listlan!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
										
									}
								}
								else if($listtype=='2'){
									if(isset($tempreserve)){
										if($listlan!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
									}
									else{
										if($listlan!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
									}
								}
								else if($listtype=='3'){
									if(isset($tempreserve)){
										if($listlan!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
									}
									else{
										if($listlan!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
									}
								}
								else{
									if(isset($tempreserve)){
										if($listlan!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
									}
									else{
										if($listlan!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
										}
									}
								}
							}*/

							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

							break;
						case 'table'://2020/8/31 阿倫提出：把桌號獨立出來

							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

							if($_POST['tablenumber']==''){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
								if($listlan!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $tablename.$listlan['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $tablename."號桌";
								}
							}

							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

							break;
						case 'time':

							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

							date_default_timezone_set($content['init']['settime']);
							if(isset($_POST['reusername'])){//2021/8/4 補印
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$_POST['username'];
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
							}

							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

							break;
						case 'numman':
							
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/></w:rPr><w:t>';
							
							$persontext="";
							if(file_exists('../../../database/floorspend.ini')){
								$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
								if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($_POST['person1']!=0||$_POST['person2']!=0||$_POST['person3']!=0)){
									if($floorspend['person1']['name']!=''&&$_POST['person1']!=0){
										if($persontext!=""){
											$persontext=$persontext.',';
										}
										else{
										}
										$persontext=$persontext.$floorspend['person1']['name'].":".$_POST['person1'];
									}
									else{
									}
									if($floorspend['person2']['name']!=''&&$_POST['person2']!=0){
										if($persontext!=""){
											$persontext=$persontext.',';
										}
										else{
										}
										$persontext=$persontext.$floorspend['person2']['name'].":".$_POST['person2'];
									}
									else{
									}
									if($floorspend['person3']['name']!=''&&$_POST['person3']!=0){
										if($persontext!=""){
											$persontext=$persontext.',';
										}
										else{
										}
										$persontext=$persontext.$floorspend['person3']['name'].":".$_POST['person3'];
									}
									else{
									}
								}
								else{
								}
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $persontext;

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
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

				if($listlan!='-1'){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $reprintlabel.$listlan['name']['listname'];
				}
				else{
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $reprintlabel."廚房工作單";
				}

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				
				//type
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

				//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
					if($listtype=='1'){
						if(isset($tempreserve)){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
						else{
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
					}
					else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
						if(isset($tempreserve)){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
						else{
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
					}
					else{
						if(isset($tempreserve)){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
						else{
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
					}
				/*}
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
					if($listtype=='1'){
						if(isset($tempreserve)){
							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
						}
						else{
							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
							
						}
					}
					else if($listtype=='2'){
						if(isset($tempreserve)){
							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
						}
						else{
							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
						}
					}
					else if($listtype=='3'){
						if(isset($tempreserve)){
							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
						}
						else{
							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
						}
					}
					else{
						if(isset($tempreserve)){
							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
						}
						else{
							if($listlan!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
							}
						}
					}
				}*/

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

				//table//2020/8/31 阿倫提出：把桌號獨立出來
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

				if($_POST['tablenumber']==''){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
				}
				else{
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
					if($listlan!='-1'){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $tablename.$listlan['name']['table'];
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $tablename."號桌";
					}
				}

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				
				//time
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

				date_default_timezone_set($content['init']['settime']);
				if(isset($_POST['reusername'])){//2021/8/4 補印
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$_POST['username'];
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
				}
				else{
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
				}

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

				/*預設不顯示人數*/

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
			}
			
			$tindex=0;
			if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "Items";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "QTY";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
			}
			else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2822"/><w:gridCol w:w="880"/><w:gridCol w:w="1298"/></w:tblGrid>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2822" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "Items";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="880" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "QTY";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "sub";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
			}
			$sum=0;
			if($itemlist[$i]['order']=='－'){
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				if(isset($print['kitchen']['grouptitlesize'])){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
				}
				else{
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
				}
				if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$itemlist[$i]['grtitle']]['name2']!=''){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'];
					}
					else{
					}
				}
				else{
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$itemlist[$i]['grtitle']]['name2']!=''){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname2'].')';
					}
					else{
					}
				}
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
				//$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
				}
				else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
					//$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				}
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				if(strlen($itemlist[$itemlist[$i]['grtitle']]['taste1'])>0){
					$tasteno=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1']);
					$temp=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1name']);
					$temp2=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1number']);
					$linetaste='';
					for($t=0;$t<sizeof($temp);$t++){
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
							$tt=preg_split('/\//',$temp[$t]);

							/*if(isset($tt[1])){
								if(intval($temp2[$t])>1){
									$tt[0]=$tt[0].'*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}*/

							if($linetaste==''){
								$linetaste = '　+';
								if($tasteno[$t]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$linetaste .= '註:';
									}
									else{
									}
								}
								$linetaste .= $tt[0];
							}
							else{
								$linetaste .= ',';
								if($tasteno[$t]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$linetaste .= '註:';
									}
									else{
									}
								}
								$linetaste .= $tt[0];
							}

							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$linetaste .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])>1){
									$linetaste .= '*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}
						}
						else{//備註一項一行
							$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							if(isset($print['kitchen']['tastesize'])){
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
							//$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
							$tt=preg_split('/\//',$temp[$t]);
							if($tasteno[$t]!='999991'){
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '　+'.$tt[0];
							}
							else{
								if($print['item']['tastefront']=='1'){
									$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '　+註:'.$tt[0];
								}
								else{
									$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '　+'.$tt[0];
								}
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])>1){
									$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}
							$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '';
							$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
							}
							else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '';
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							}
							$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= "</w:tr>";
						}	
					}
					if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
						$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
							if(isset($print['kitchen']['tastesize'])){
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
						}
						else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
							if(isset($print['kitchen']['tastesize'])){
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}

						}
						//$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
						$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= $linetaste;
						$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$kitcontent[$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['printtype']] .= "</w:tr>";
					}
					else{//備註一項一行
						
					}
				}
				else{
				}
			}
			else{
			}
			$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
			$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
			if(strlen($itemlist[$i]['mname1'])==''){
				if($itemlist[$i]['order']=='－'){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "－".$itemlist[$i]['name'];
				}
				else{
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
				}
				if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
					if($itemlist[$i]['order']=='－'){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'];
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'];
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
				if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
					if($itemlist[$i]['order']=='－'){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
					}
				}
				else{
				}
			}
			$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
			$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
			$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
			$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
			if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
			}
			else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['subtotal'];
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
			}
			$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
			if(strlen($itemlist[$i]['taste1'])>0){
				$tasteno=preg_split('/,/',$itemlist[$i]['taste1']);
				$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
				$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
				$linetaste='';
				for($t=0;$t<sizeof($temp);$t++){
					if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
						$tt=preg_split('/\//',$temp[$t]);

						/*if(isset($tt[1])){
							if(intval($temp2[$t])>1){
								$tt[0]=$tt[0].'*'.$temp2[$t];
							}
							else{
							}
						}
						else{
						}*/

						if($linetaste==''){
							$linetaste = '　+';
							if($tasteno[$t]!='999991'){
							}
							else{
								if($print['item']['tastefront']=='1'){
									$linetaste .= '註:';
								}
								else{
								}
							}
							$linetaste .= $tt[0];
						}
						else{
							$linetaste .= ',';
							if($tasteno[$t]!='999991'){
							}
							else{
								if($print['item']['tastefront']=='1'){
									$linetaste .= '註:';
								}
								else{
								}
							}
							$linetaste .= $tt[0];
						}

						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
							$linetaste .= '/ '.$tt[1];
						}
						else if(isset($tt[1])&&$tt[1]!=''){
							if(intval($temp2[$t])>1){
								$linetaste .= '*'.$temp2[$t];
							}
							else{
							}
						}
						else{
						}
					}
					else{//備註一項一行
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(isset($print['kitchen']['tastesize'])){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
						}
						else{
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
						}
						//$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
						$tt=preg_split('/\//',$temp[$t]);
						if($tasteno[$t]!='999991'){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
						}
						else{
							if($print['item']['tastefront']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '　+註:'.$tt[0];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
							}
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$tt[1];
						}
						else if(isset($tt[1])&&$tt[1]!=''){
							if(intval($temp2[$t])>1){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '*'.$temp2[$t];
							}
							else{
							}
						}
						else{
						}
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '';
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
						}
						else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '';
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						}
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
				}
				if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
						if(isset($print['kitchen']['tastesize'])){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
						}
						else{
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
						}
					}
					else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
						if(isset($print['kitchen']['tastesize'])){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
						}
						else{
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
						}
					}
					//$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				}
				else{//備註一項一行
					
				}
			}
			else{
			}
			if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
			}
			else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:b/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "小計";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['subtotal'];
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
			}
			$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
			if(isset($memdata[0]['name'])&&isset($print['kitchen']['memberdata'])&&$print['kitchen']['memberdata']=='1'){
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "會員電話";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if(strlen($memdata[0]['tel'])==10){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($memdata[0]['tel'],0,4).'***'.substr($memdata[0]['tel'],-3);
				}
				else if(strlen($memdata[0]['tel'])==8){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '*****'.substr($memdata[0]['tel'],-3);
				}
				else{
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $memdata[0]['tel'];
				}
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "會員姓名";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $memdata[0]['name'];
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
			}
			else{
			}
	
			/*$document->setValue('item',$table);
			//$document->setValue('total','NT.'.$_POST['total']);
			$filename=date("YmdHis");
			if($print['item']['kitchen']!='0'&&$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchen'.$listtype]=='1'&&(!isset($looptype)||(isset($looptype)&&($looptype=='1'||$looptype=='3')))){
				//$document->save("../../../print/noread/".$filename."_list".$menu[$itemlist[$i]['no']]['printtype']."_".$consecnumber."_".$i.".docx");
				$document->save("../../../print/read/".$consecnumber."_list".$menu[$itemlist[$i]['no']]['printtype']."_".$filename."_".$i.".docx");
				$prt=fopen("../../../print/noread/".$consecnumber."_list".$menu[$itemlist[$i]['no']]['printtype']."_".$filename."_".$i.".prt",'w');
				fclose($prt);
				//if(intval($print['item']['kitchen'])>1){
					//for($j=1;$j<intval($print['item']['kitchen']);$j++){
						//copy("../../../print/noread/list".$pt."_".$consecnumber."_".$filename.".docx","../../../print/noread/list".$pt."_".$consecnumber."_".$filename."(".$j.").docx");
					//}
				//}
				//else{
				//}
			}
			else{
				$document->save("../../../print/read/delete_list_".$print['item']['kitchen']."_".$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchen'.$listtype].".docx");
			}*/
		}
		else if($pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='1'||$pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='2'||$pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='7'){//一類一單
			if($menu[$itemlist[$i]['no']]['printtype']==''){
				if($itemlist[$i]['order']=='－'){
					if(in_array('-1',$atgroup)){
					}
					else{
						if($conarray['-1']!=''&&isset($print['kitchen']['grouptype'])&&$print['kitchen']['grouptype']=='1'){
							$conarray['-1'] .= '</w:tbl>';
							$conarray['-1'] .= '<w:p w:rsidR="008C3284" w:rsidRDefault="008C3284"><w:pPr><w:widowControl/></w:pPr><w:r><w:br w:type="page"/></w:r></w:p>';
							$conarray['-1'] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
							if(isset($print['kitchen']['title'])){
								$temptitle=preg_split('/,/',$print['kitchen']['title']);
								foreach($temptitle as $tempitem){
									switch($tempitem){
										case 'story':

											$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

											if($listlan!='-1'){
												$conarray['-1'] .= $reprintlabel.$listlan['name']['listname'];
											}
											else{
												$conarray['-1'] .= $reprintlabel."廚房工作單";
											}

											$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
											$conarray['-1'] .= "</w:tr>";

											break;
										case 'type':

											$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

											//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
												if($listtype=='1'){
													if(isset($tempreserve)){
														$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray['-1'] .= $listhint."\r\n";
														}
														else{
														}
														$conarray['-1'] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
													else{
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray['-1'] .= $listhint."\r\n";
														}
														else{
														}
														$conarray['-1'] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
												}
												else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
													if(isset($tempreserve)){
														$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray['-1'] .= $listhint."\r\n";
														}
														else{
														}
														$conarray['-1'] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
													else{
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray['-1'] .= $listhint."\r\n";
														}
														else{
														}
														$conarray['-1'] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
												}
												else{
													if(isset($tempreserve)){
														$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray['-1'] .= $listhint."\r\n";
														}
														else{
														}
														$conarray['-1'] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
													else{
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray['-1'] .= $listhint."\r\n";
														}
														else{
														}
														$conarray['-1'] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
												}
												if($k!='0'&&$k=='grouptype'){
												}
												else{//2020/4/24 非依列印類別分類，產生列印類別名稱
													$conarray['-1'] .= ' '.$pti[$k]['name'];
												}
											/*}
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
												if($listtype=='1'){
													if(isset($tempreserve)){
														if($listlan!='-1'){
															$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														else{
															$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
													}
													else{
														if($listlan!='-1'){
															$conarray['-1'] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														else{
															$conarray['-1'] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														
													}
												}
												else if($listtype=='2'){
													if(isset($tempreserve)){
														if($listlan!='-1'){
															$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														else{
															$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
													}
													else{
														if($listlan!='-1'){
															$conarray['-1'] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														else{
															$conarray['-1'] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
													}
												}
												else if($listtype=='3'){
													if(isset($tempreserve)){
														if($listlan!='-1'){
															$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														else{
															$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
													}
													else{
														if($listlan!='-1'){
															$conarray['-1'] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														else{
															$conarray['-1'] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
													}
												}
												else{
													if(isset($tempreserve)){
														if($listlan!='-1'){
															$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														else{
															$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
													}
													else{
														if($listlan!='-1'){
															$conarray['-1'] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
														else{
															$conarray['-1'] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
														}
													}
												}
												if($k!='0'&&$k=='grouptype'){
													if($listlan!='-1'){
														$conarray['-1'] .= "\r\n".$tablename.$listlan['name']['table'];
													}
													else{
														$conarray['-1'] .= "\r\n".$tablename."號桌";
													}
												}
												else{//2020/4/24 非依列印類別分類，產生列印類別名稱
													if($listlan!='-1'){
														$conarray['-1'] .= " ".$pti[$k]['name']."\r\n".$tablename.$listlan['name']['table'];
													}
													else{
														$conarray['-1'] .= " ".$pti[$k]['name']."\r\n".$tablename."號桌";
													}
												}
											}*/

											$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
											$conarray['-1'] .= "</w:tr>";

											break;
										case 'table'://2020/8/31 阿倫提出：把桌號獨立出來

											$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

											if($_POST['tablenumber']==''){
												$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
											}
											else{
												$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
												if($listlan!='-1'){
													$conarray['-1'] .= $tablename.$listlan['name']['table'];
												}
												else{
													$conarray['-1'] .= $tablename."號桌";
												}
											}

											$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
											$conarray['-1'] .= "</w:tr>";

											break;
										case 'time':

											$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

											date_default_timezone_set($content['init']['settime']);
											if(isset($_POST['reusername'])){//2021/8/4 補印
												$conarray['-1'] .= $_POST['consecnumber'].' '.$_POST['username'];
												$conarray['-1'] .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
											}
											else{
												$conarray['-1'] .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
											}

											$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
											$conarray['-1'] .= "</w:tr>";

											break;
										case 'numman':
											
											$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/></w:rPr><w:t>';
											
											$persontext="";
											if(file_exists('../../../database/floorspend.ini')){
												$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
												if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($_POST['person1']!=0||$_POST['person2']!=0||$_POST['person3']!=0)){
													if($floorspend['person1']['name']!=''&&$_POST['person1']!=0){
														if($persontext!=""){
															$persontext=$persontext.',';
														}
														else{
														}
														$persontext=$persontext.$floorspend['person1']['name'].":".$_POST['person1'];
													}
													else{
													}
													if($floorspend['person2']['name']!=''&&$_POST['person2']!=0){
														if($persontext!=""){
															$persontext=$persontext.',';
														}
														else{
														}
														$persontext=$persontext.$floorspend['person2']['name'].":".$_POST['person2'];
													}
													else{
													}
													if($floorspend['person3']['name']!=''&&$_POST['person3']!=0){
														if($persontext!=""){
															$persontext=$persontext.',';
														}
														else{
														}
														$persontext=$persontext.$floorspend['person3']['name'].":".$_POST['person3'];
													}
													else{
													}
												}
												else{
												}
											}
											else{
											}
											$conarray['-1'] .= $persontext;

											$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
											$conarray['-1'] .= "</w:tr>";

											break;
										default:
											break;
									}
								}
							}
							else{
								//story
								$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

								if($listlan!='-1'){
									$conarray['-1'] .= $reprintlabel.$listlan['name']['listname'];
								}
								else{
									$conarray['-1'] .= $reprintlabel."廚房工作單";
								}

								$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['-1'] .= "</w:tr>";
								
								//type
								$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

								//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
									if($listtype=='1'){
										if(isset($tempreserve)){
											$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray['-1'] .= $listhint."\r\n";
											}
											else{
											}
											$conarray['-1'] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray['-1'] .= $listhint."\r\n";
											}
											else{
											}
											$conarray['-1'] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
									else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
										if(isset($tempreserve)){
											$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray['-1'] .= $listhint."\r\n";
											}
											else{
											}
											$conarray['-1'] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray['-1'] .= $listhint."\r\n";
											}
											else{
											}
											$conarray['-1'] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
									else{
										if(isset($tempreserve)){
											$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray['-1'] .= $listhint."\r\n";
											}
											else{
											}
											$conarray['-1'] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray['-1'] .= $listhint."\r\n";
											}
											else{
											}
											$conarray['-1'] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
									if($k!='0'&&$k=='grouptype'){
									}
									else{//2020/4/24 分依列印類別分類，產生列印類別名稱
										$conarray['-1'] .= ' '.$pti[$k]['name'];
									}
								/*}
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
									if($listtype=='1'){
										if(isset($tempreserve)){
											if($listlan!='-1'){
												$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											else{
												$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
										}
										else{
											if($listlan!='-1'){
												$conarray['-1'] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											else{
												$conarray['-1'] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											
										}
									}
									else if($listtype=='2'){
										if(isset($tempreserve)){
											if($listlan!='-1'){
												$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											else{
												$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
										}
										else{
											if($listlan!='-1'){
												$conarray['-1'] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											else{
												$conarray['-1'] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
										}
									}
									else if($listtype=='3'){
										if(isset($tempreserve)){
											if($listlan!='-1'){
												$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											else{
												$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
										}
										else{
											if($listlan!='-1'){
												$conarray['-1'] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											else{
												$conarray['-1'] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
										}
									}
									else{
										if(isset($tempreserve)){
											if($listlan!='-1'){
												$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											else{
												$conarray['-1'] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
										}
										else{
											if($listlan!='-1'){
												$conarray['-1'] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
											else{
												$conarray['-1'] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
											}
										}
									}
									if($k!='0'&&$k=='grouptype'){
										if($listlan!='-1'){
											$conarray['-1'] .= "\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$conarray['-1'] .= "\r\n".$tablename."號桌";
										}
									}
									else{//2020/4/24 非依列印類別分類，產生列印類別名稱
										if($listlan!='-1'){
											$conarray['-1'] .= " ".$pti[$k]['name']."\r\n".$tablename.$listlan['name']['table'];
										}
										else{
											$conarray['-1'] .= " ".$pti[$k]['name']."\r\n".$tablename."號桌";
										}
									}
								}*/

								$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['-1'] .= "</w:tr>";

								//table//2020/8/31 阿倫提出：把桌號獨立出來
								$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

								if($_POST['tablenumber']==''){
									$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
								}
								else{
									$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
									if($listlan!='-1'){
										$conarray['-1'] .= $tablename.$listlan['name']['table'];
									}
									else{
										$conarray['-1'] .= $tablename."號桌";
									}
								}

								$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['-1'] .= "</w:tr>";
								
								//time
								$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

								date_default_timezone_set($content['init']['settime']);
								if(isset($_POST['reusername'])){//2021/8/4 補印
									$conarray['-1'] .= $_POST['consecnumber'].' '.$_POST['username'];
									$conarray['-1'] .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
								}
								else{
									$conarray['-1'] .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
								}

								$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['-1'] .= "</w:tr>";

								/*預設不顯示人數*/
							}
							$conarray['-1'] .= '</w:tbl>';
							
							/*$document->setValue('consecnumber',$consecnumber);
							//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
							//$document->setValue('tel', '(04)2473-2003');
							//$document->setValue('time', date('Y/m/s H:i:s'));
							if($listlan!='-1'){
								$document->setValue('story',$listlan['name']['listname']);
							}
							else{
								$document->setValue('story',"廚房工作單");
							}*/
							$tindex=0;
							$conarray['-1'] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
							$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$conarray['-1'] .= "Items";
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$conarray['-1'] .= "QTY";
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= "</w:tr>";
						}
						else{
						}
						$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(isset($print['kitchen']['grouptitlesize'])){
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
						}
						else{
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						}
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
							$conarray['-1'] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
						}
						else{
							$conarray['-1'] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$itemlist[$i]['grtitle']]['name2']!=''){
							if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$conarray['-1'] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'];
							}
							else{
								$conarray['-1'] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname2'].')';
							}
						}
						else{
						}
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						//$conarray['-1'] .= $itemlist[$i]['number'];
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= "</w:tr>";
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['taste1'])>0){
							$tasteno=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1']);
							$temp=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1name']);
							$temp2=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1number']);
							$linetaste='';
							for($t=0;$t<sizeof($temp);$t++){
								if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
									$tt=preg_split('/\//',$temp[$t]);

									/*if(isset($tt[1])){
										if(intval($temp2[$t])>1){
											$tt[0]=$tt[0].'*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}*/

									if($linetaste==''){
										$linetaste = '　+';
										if($tasteno[$t]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$linetaste .= '註:';
											}
											else{
											}
										}
										$linetaste .= $tt[0];
									}
									else{
										$linetaste .= ',';
										if($tasteno[$t]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$linetaste .= '註:';
											}
											else{
											}
										}
										$linetaste .= $tt[0];
									}

									if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
										$linetaste .= '/ '.$tt[1];
									}
									else if(isset($tt[1])&&$tt[1]!=''){
										if(intval($temp2[$t])>1){
											$linetaste .= '*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}
								}
								else{//備註一項一行
									$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
									if(isset($print['kitchen']['tastesize'])){
										$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
									}
									else{
										$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
									}
									//$conarray['-1'] .= '-'.$_POST['taste1'][$t];
									$tt=preg_split('/\//',$temp[$t]);
									if($tasteno[$t]!='999991'){
										$conarray['-1'] .= '　+'.$tt[0];
									}
									else{
										if($print['item']['tastefront']=='1'){
											$conarray['-1'] .= '　+註:'.$tt[0];
										}
										else{
											$conarray['-1'] .= '　+'.$tt[0];
										}
									}
									if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
										$conarray['-1'] .= '/ '.$tt[1];
									}
									else if(isset($tt[1])&&$tt[1]!=''){
										if(intval($temp2[$t])>1){
											$conarray['-1'] .= '*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}
									$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
									$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
									$conarray['-1'] .= '';
									$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
									$conarray['-1'] .= "</w:tr>";
								}
							}
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
								$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								if(isset($print['kitchen']['tastesize'])){
									$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
								}
								else{
									$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
								}
								//$conarray['-1'] .= '-'.$_POST['taste1'][$t];
								$conarray['-1'] .= $linetaste;
								$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['-1'] .= "</w:tr>";
							}
							else{//備註一項一行
								
							}
						}
						else{
						}
						array_push($atgroup,'-1');
					}
					$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
					if(strlen($itemlist[$i]['mname1'])==''){
						$conarray['-1'] .= '－'.$itemlist[$i]['name'];
					}
					else{
						$conarray['-1'] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					}
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['-1'] .= "\r\n－".$itemlist[$i]['name2'];
						}
						else{
							$conarray['-1'] .= "\r\n－".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
						}
					}
					else{
					}
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
					$conarray['-1'] .= $itemlist[$i]['number'];
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= "</w:tr>";
				}
				else{
					$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
					if(strlen($itemlist[$i]['mname1'])==''){
						$conarray['-1'] .= $itemlist[$i]['name'];
					}
					else{
						$conarray['-1'] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					}
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['-1'] .= "\r\n".$itemlist[$i]['name2'];
						}
						else{
							$conarray['-1'] .= "\r\n".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
						}
					}
					else{
					}
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
					$conarray['-1'] .= $itemlist[$i]['number'];
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= "</w:tr>";
				}
				
				if(strlen($itemlist[$i]['taste1'])>0){
					$tasteno=preg_split(",",$itemlist[$i]['taste1']);
					$temp=preg_split(",",$itemlist[$i]['taste1name']);
					$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
					$linetaste='';
					for($t=0;$t<sizeof($temp);$t++){
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
							$tt=preg_split('/\//',$temp[$t]);

							/*if(isset($tt[1])){
								if(intval($temp2[$t])>1){
									$tt[0]=$tt[0].'*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}*/

							if($linetaste==''){
								$linetaste = '　+';
								if($tasteno[$t]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$linetaste .= '註:';
									}
									else{
									}
								}
								$linetaste .= $tt[0];
							}
							else{
								$linetaste .= ',';
								if($tasteno[$t]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$linetaste .= '註:';
									}
									else{
									}
								}
								$linetaste .= $tt[0];
							}

							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$linetaste .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])>1){
									$linetaste .= '*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}
						}
						else{//備註一項一行
							$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							if(isset($print['kitchen']['tastesize'])){
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
							//$table .= '-'.$_POST['taste1'][$t];
							$tt=preg_split('/\//',$temp[$t]);
							if($tasteno[$t]!='999991'){
								$conarray['-1'] .= '　+'.$tt[0];
							}
							else{
								if($print['item']['tastefront']=='1'){
									$conarray['-1'] .= '　+註:'.$tt[0];
								}
								else{
									$conarray['-1'] .= '　+'.$tt[0];
								}
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$conarray['-1'] .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])>1){
									$conarray['-1'] .= '*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray['-1'] .= '';
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= "</w:tr>";
						}
					}
					if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
						$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(isset($print['kitchen']['tastesize'])){
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
						}
						else{
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
						}
						//$table .= '-'.$_POST['taste1'][$t];
						$conarray['-1'] .= $linetaste;
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= "</w:tr>";
					}
					else{//備註一項一行
						
					}
				}
				else{
				}
			}
			else{
				if($itemlist[$i]['order']=='－'){
					//echo 'groupcode='.$groupcode.PHP_EOL;
					//echo 'grtitle='.$itemlist[$i]['grtitle'].PHP_EOL;
					//echo 'itemname='.$itemlist[$i]['name'].PHP_EOL;
					if(in_array($itemlist[$i]['grtitle'],$atgroup)){
					}
					else{
						if($conarray[$menu[$itemlist[$i]['no']]['printtype']]!=''&&isset($print['kitchen']['grouptype'])&&$print['kitchen']['grouptype']=='1'){
							if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
							}
							else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3702" w:type="pct"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:b/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "小計";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $conamt[$menu[$itemlist[$i]['no']]['printtype']];
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

								$conamt[$menu[$itemlist[$i]['no']]['printtype']]=0;
							}
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:p w:rsidR="008C3284" w:rsidRDefault="008C3284"><w:pPr><w:widowControl/></w:pPr><w:r><w:br w:type="page"/></w:r></w:p>';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
							if(isset($print['kitchen']['title'])){
								$temptitle=preg_split('/,/',$print['kitchen']['title']);
								foreach($temptitle as $tempitem){
									switch($tempitem){
										case 'story':

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $reprintlabel.$listlan['name']['listname'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $reprintlabel."廚房工作單";
											}

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

											break;
										case 'type':

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

											//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
												if($listtype=='1'){
													if(isset($tempreserve)){
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
														}
														else{
														}
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
													else{
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
														}
														else{
														}
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
												}
												else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
													if(isset($tempreserve)){
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
														}
														else{
														}
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
													else{
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
														}
														else{
														}
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
												}
												else{
													if(isset($tempreserve)){
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
														}
														else{
														}
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
													else{
														if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
														}
														else{
														}
														$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
													}
												}
											/*}
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
												if($listtype=='1'){
													if(isset($tempreserve)){
														if($listlan!='-1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
														}
														else{
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
														}
													}
													else{
														if($listlan!='-1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
														}
														else{
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
														}
														
													}
												}
												else if($listtype=='2'){
													if(isset($tempreserve)){
														if($listlan!='-1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
														}
														else{
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
														}
													}
													else{
														if($listlan!='-1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
														}
														else{
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
														}
													}
												}
												else if($listtype=='3'){
													if(isset($tempreserve)){
														if($listlan!='-1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
														}
														else{
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
														}
													}
													else{
														if($listlan!='-1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
														}
														else{
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
														}
													}
												}
												else{
													if(isset($tempreserve)){
														if($listlan!='-1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
														}
														else{
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
														}
													}
													else{
														if($listlan!='-1'){
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
														}
														else{
															$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
														}
													}
												}
											}*/

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

											break;
										case 'table'://2020/8/31 阿倫提出：把桌號獨立出來

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

											if($_POST['tablenumber']==''){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
												if($listlan!='-1'){
													$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $tablename.$listlan['name']['table'];
												}
												else{
													$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $tablename."號桌";
												}
											}

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

											break;
										case 'time':

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

											date_default_timezone_set($content['init']['settime']);
											if(isset($_POST['reusername'])){//2021/8/4 補印
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$_POST['username'];
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
											}

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

											break;
										case 'numman':
											
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/></w:rPr><w:t>';
											
											$persontext="";
											if(file_exists('../../../database/floorspend.ini')){
												$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
												if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($_POST['person1']!=0||$_POST['person2']!=0||$_POST['person3']!=0)){
													if($floorspend['person1']['name']!=''&&$_POST['person1']!=0){
														if($persontext!=""){
															$persontext=$persontext.',';
														}
														else{
														}
														$persontext=$persontext.$floorspend['person1']['name'].":".$_POST['person1'];
													}
													else{
													}
													if($floorspend['person2']['name']!=''&&$_POST['person2']!=0){
														if($persontext!=""){
															$persontext=$persontext.',';
														}
														else{
														}
														$persontext=$persontext.$floorspend['person2']['name'].":".$_POST['person2'];
													}
													else{
													}
													if($floorspend['person3']['name']!=''&&$_POST['person3']!=0){
														if($persontext!=""){
															$persontext=$persontext.',';
														}
														else{
														}
														$persontext=$persontext.$floorspend['person3']['name'].":".$_POST['person3'];
													}
													else{
													}
												}
												else{
												}
											}
											else{
											}
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $persontext;

											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

											break;
										default:
											break;
									}
								}
							}
							else{
								//story
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

								if($listlan!='-1'){
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $reprintlabel.$listlan['name']['listname'];
								}
								else{
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $reprintlabel."廚房工作單";
								}

								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
								
								//type
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

								//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
									if($listtype=='1'){
										if(isset($tempreserve)){
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
											}
											else{
											}
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
											}
											else{
											}
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
									else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
										if(isset($tempreserve)){
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
											}
											else{
											}
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
											}
											else{
											}
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
									else{
										if(isset($tempreserve)){
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
											}
											else{
											}
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $listhint."\r\n";
											}
											else{
											}
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
								/*}
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
									if($listtype=='1'){
										if(isset($tempreserve)){
											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
											
										}
									}
									else if($listtype=='2'){
										if(isset($tempreserve)){
											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
									}
									else if($listtype=='3'){
										if(isset($tempreserve)){
											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
									}
									else{
										if(isset($tempreserve)){
											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
										else{
											if($listlan!='-1'){
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename.$listlan['name']['table'];
											}
											else{
												$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$tablename."號桌";
											}
										}
									}
								}*/

								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

								//table//2020/8/31 阿倫提出：把桌號獨立出來
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

								if($_POST['tablenumber']==''){
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
								}
								else{
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
									if($listlan!='-1'){
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $tablename.$listlan['name']['table'];
									}
									else{
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $tablename."號桌";
									}
								}

								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
								
								//time
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

								date_default_timezone_set($content['init']['settime']);
								if(isset($_POST['reusername'])){//2021/8/4 補印
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$_POST['username'];
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
								}
								else{
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
								}

								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

								/*預設不顯示人數*/
							}
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
							
							/*$document->setValue('consecnumber',$consecnumber);
							//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
							//$document->setValue('tel', '(04)2473-2003');
							//$document->setValue('time', date('Y/m/s H:i:s'));
							if($listlan!='-1'){
								$document->setValue('story',$listlan['name']['listname']);
							}
							else{
								$document->setValue('story',"廚房工作單");
							}*/
							$tindex=0;
							if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "Items";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "QTY";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							}
							else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2822"/><w:gridCol w:w="880"/><w:gridCol w:w="1298"/></w:tblGrid>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2822" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "Items";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="880" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "QTY";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "sub";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							}
						}
						else{
						}
						$groupcode=$itemlist[$i]['grtitle'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(isset($print['kitchen']['grouptitlesize'])){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						}
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$itemlist[$i]['grtitle']]['name2']!=''){
							if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'];
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname2'].')';
							}
						}
						else{
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						//$conarray['-1'] .= $itemlist[$i]['number'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
						}
						else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
							//$conarray['-1'] .= $itemlist[$i]['number'];
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['taste1'])>0){
							$tasteno=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1']);
							$temp=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1name']);
							$temp2=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1number']);
							$linetaste='';
							for($t=0;$t<sizeof($temp);$t++){
								if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
									$tt=preg_split('/\//',$temp[$t]);

									/*if(isset($tt[1])){
										if(intval($temp2[$t])>1){
											$tt[0]=$tt[0].'*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}*/

									if($linetaste==''){
										$linetaste = '　+';
										if($tasteno[$t]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$linetaste .= '註:';
											}
											else{
											}
										}
										$linetaste .= $tt[0];
									}
									else{
										$linetaste .= ',';
										if($tasteno[$t]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$linetaste .= '註:';
											}
											else{
											}
										}
										$linetaste .= $tt[0];
									}

									if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
										$linetaste .= '/ '.$tt[1];
									}
									else if(isset($tt[1])&&$tt[1]!=''){
										if(intval($temp2[$t])>1){
											$linetaste .= '*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}
								}
								else{//備註一項一行
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
									if(isset($print['kitchen']['tastesize'])){
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
									}
									else{
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
									}
									//$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
									$tt=preg_split('/\//',$temp[$t]);
									if($tasteno[$t]!='999991'){
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
									}
									else{
										if($print['item']['tastefront']=='1'){
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '　+註:'.$tt[0];
										}
										else{
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
										}
									}
									if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$tt[1];
									}
									else if(isset($tt[1])&&$tt[1]!=''){
										if(intval($temp2[$t])>1){
											$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '';
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
									if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
									}
									else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '';
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
									}
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
								}
							}
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
									if(isset($print['kitchen']['tastesize'])){
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
									}
									else{
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
									}
								}
								else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
									if(isset($print['kitchen']['tastesize'])){
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
									}
									else{
										$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
									}
								}
								//$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							}
							else{//備註一項一行
								
							}
						}
						else{
						}
						array_push($atgroup,$itemlist[$i]['grtitle']);
					}
					/*if(in_array($itemlist[$i]['grtitle'],$atgroup)){
					}
					else{
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						//$conarray['-1'] .= $itemlist[$i]['number'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						array_push($atgroup,$itemlist[$i]['grtitle']);
					}*/
					if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'];
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'];
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
							}
						}
						else{
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					}
					else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2822" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'];
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'];
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
							}
						}
						else{
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="880" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['subtotal'];
						if(isset($conamt[$menu[$itemlist[$i]['no']]['printtype']])){
							$conamt[$menu[$itemlist[$i]['no']]['printtype']]+=$itemlist[$i]['subtotal'];
						}
						else{
							$conamt[$menu[$itemlist[$i]['no']]['printtype']]=$itemlist[$i]['subtotal'];
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					}
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				}
				else{
					if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'];
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
							}
						}
						else{
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					}
					else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2822" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'];
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
							}
						}
						else{
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="880" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['subtotal'];
						if(isset($conamt[$menu[$itemlist[$i]['no']]['printtype']])){
							$conamt[$menu[$itemlist[$i]['no']]['printtype']]+=$itemlist[$i]['subtotal'];
						}
						else{
							$conamt[$menu[$itemlist[$i]['no']]['printtype']]=$itemlist[$i]['subtotal'];
						}
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					}
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				}
				
				if(strlen($itemlist[$i]['taste1'])>0){
					$tasteno=preg_split('/,/',$itemlist[$i]['taste1']);
					$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
					$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
					$linetaste='';
					for($t=0;$t<sizeof($temp);$t++){
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
							$tt=preg_split('/\//',$temp[$t]);

							/*if(isset($tt[1])){
								if(intval($temp2[$t])>1){
									$tt[0]=$tt[0].'*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}*/
							
							if($linetaste==''){
								$linetaste = '　+';
								if($tasteno[$t]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$linetaste .= '註:';
									}
									else{
									}
								}
								$linetaste .= $tt[0];
							}
							else{
								$linetaste .= ',';
								if($tasteno[$t]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$linetaste .= '註:';
									}
									else{
									}
								}
								$linetaste .= $tt[0];
							}

							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$linetaste .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])>1){
									$linetaste .= '*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}
						}
						else{//備註一項一行
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							if(isset($print['kitchen']['tastesize'])){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
							//$table .= '-'.$_POST['taste1'][$t];
							$tt=preg_split('/\//',$temp[$t]);
							if($tasteno[$t]!='999991'){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
							}
							else{
								if($print['item']['tastefront']=='1'){
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '　+註:'.$tt[0];
								}
								else{
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
								}
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])>1){
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
							}
							else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '';
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							}
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						}
					}
					if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
							if(isset($print['kitchen']['tastesize'])){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
						}
						else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
							if(isset($print['kitchen']['tastesize'])){
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
						}
						//$table .= '-'.$_POST['taste1'][$t];
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					else{//備註一項一行
						
					}
				}
				else{
				}
			}
		}
		else{//2020/4/22 if($pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='5'||$pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='6'||$pti[$menu[$_POST['no'][$i]]['printtype']]['type']=='9')//依列印類別分類
			if($menu[$itemlist[$i]['no']]['printtype']==''){
				if($itemlist[$i]['order']=='－'){
					if(in_array('-1',$atgroup)){
					}
					else{
						$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(isset($print['kitchen']['grouptitlesize'])){
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
						}
						else{
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						}
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
							$conarray['-1'] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
						}
						else{
							$conarray['-1'] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$itemlist[$i]['grtitle']]['name2']!=''){
							if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$conarray['-1'] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'];
							}
							else{
								$conarray['-1'] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname2'].')';
							}
						}
						else{
						}
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						//$conarray['-1'] .= $itemlist[$i]['number'];
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= "</w:tr>";
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['taste1'])>0){
							$tasteno=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1']);
							$temp=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1name']);
							$temp2=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1number']);
							$linetaste='';
							for($t=0;$t<sizeof($temp);$t++){
								if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
									$tt=preg_split('/\//',$temp[$t]);

									/*if(isset($tt[1])){
										if(intval($temp2[$t])>1){
											$tt[0]=$tt[0].'*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}*/

									if($linetaste==''){
										$linetaste = '　+';
										if($tasteno[$t]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$linetaste .= '註:';
											}
											else{
											}
										}
										$linetaste .= $tt[0];
									}
									else{
										$linetaste .= ',';
										if($tasteno[$t]!='999991'){
										}
										else{
											if($print['item']['tastefront']=='1'){
												$linetaste .= '註:';
											}
											else{
											}
										}
										$linetaste .= $tt[0];
									}

									if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
										$linetaste .= '/ '.$tt[1];
									}
									else if(isset($tt[1])&&$tt[1]!=''){
										if(intval($temp2[$t])>1){
											$linetaste .= '*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}
								}
								else{//備註一項一行
									$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
									if(isset($print['kitchen']['tastesize'])){
										$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
									}
									else{
										$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
									}
									//$conarray['-1'] .= '-'.$_POST['taste1'][$t];
									$tt=preg_split('/\//',$temp[$t]);
									if($tasteno[$t]!='999991'){
										$conarray['-1'] .= '　+'.$tt[0];
									}
									else{
										if($print['item']['tastefront']=='1'){
											$conarray['-1'] .= '　+註:'.$tt[0];
										}
										else{
											$conarray['-1'] .= '　+'.$tt[0];
										}
									}
									if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
										$conarray['-1'] .= '/ '.$tt[1];
									}
									else if(isset($tt[1])&&$tt[1]!=''){
										if(intval($temp2[$t])>1){
											$conarray['-1'] .= '*'.$temp2[$t];
										}
										else{
										}
									}
									else{
									}
									$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
									$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
									$conarray['-1'] .= '';
									$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
									$conarray['-1'] .= "</w:tr>";
								}
							}
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
								$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								if(isset($print['kitchen']['tastesize'])){
									$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
								}
								else{
									$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
								}
								//$conarray['-1'] .= '-'.$_POST['taste1'][$t];
								$conarray['-1'] .= $linetaste;
								$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['-1'] .= "</w:tr>";
							}
							else{//備註一項一行
								
							}
						}
						else{
						}
						array_push($atgroup,'-1');
					}
					$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
					if(strlen($itemlist[$i]['mname1'])==''){
						$conarray['-1'] .= '－'.$itemlist[$i]['name'];
					}
					else{
						$conarray['-1'] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					}
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['-1'] .= "\r\n－".$itemlist[$i]['name2'];
						}
						else{
							$conarray['-1'] .= "\r\n－".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
						}
					}
					else{
					}
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
					$conarray['-1'] .= $itemlist[$i]['number'];
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= "</w:tr>";
				}
				else{
					$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
					if(strlen($itemlist[$i]['mname1'])==''){
						$conarray['-1'] .= $itemlist[$i]['name'];
					}
					else{
						$conarray['-1'] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					}
					if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['-1'] .= "\r\n".$itemlist[$i]['name2'];
						}
						else{
							$conarray['-1'] .= "\r\n".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
						}
					}
					else{
					}
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
					$conarray['-1'] .= $itemlist[$i]['number'];
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= "</w:tr>";
				}
				
				if(strlen($itemlist[$i]['taste1'])>0){
					$tasteno=preg_split(",",$itemlist[$i]['taste1']);
					$temp=preg_split(",",$itemlist[$i]['taste1name']);
					$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
					$linetaste='';
					for($t=0;$t<sizeof($temp);$t++){
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
							$tt=preg_split('/\//',$temp[$t]);

							/*if(isset($tt[1])){
								if(intval($temp2[$t])>1){
									$tt[0]=$tt[0].'*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}*/

							if($linetaste==''){
								$linetaste = '　+';
								if($tasteno[$t]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$linetaste .= '註:';
									}
									else{
									}
								}
								$linetaste .= $tt[0];
							}
							else{
								$linetaste .= ',';
								if($tasteno[$t]!='999991'){
								}
								else{
									if($print['item']['tastefront']=='1'){
										$linetaste .= '註:';
									}
									else{
									}
								}
								$linetaste .= $tt[0];
							}

							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$linetaste .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])>1){
									$linetaste .= '*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}
						}
						else{//備註一項一行
							$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							if(isset($print['kitchen']['tastesize'])){
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
							}
							else{
								$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
							}
							//$table .= '-'.$_POST['taste1'][$t];
							$tt=preg_split('/\//',$temp[$t]);
							if($tasteno[$t]!='999991'){
								$conarray['-1'] .= '　+'.$tt[0];
							}
							else{
								if($print['item']['tastefront']=='1'){
									$conarray['-1'] .= '　+註:'.$tt[0];
								}
								else{
									$conarray['-1'] .= '　+'.$tt[0];
								}
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$conarray['-1'] .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])>1){
									$conarray['-1'] .= '*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray['-1'] .= '';
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= "</w:tr>";
						}
					}
					if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
						$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(isset($print['kitchen']['tastesize'])){
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
						}
						else{
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
						}
						//$table .= '-'.$_POST['taste1'][$t];
						$conarray['-1'] .= $linetaste;
						$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['-1'] .= "</w:tr>";
					}
					else{//備註一項一行
						
					}
				}
				else{
				}
			}
			else{
				if((isset($pti[$menu[$itemlist[$i]['no']]['printtype']])&&$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchen'.$listtype]=='1')){//2021/6/21 要先在這邊判斷是否要印在工作單上，因為分類單是印在同一張工作單上，後面無法判斷內部那些品項不要印在工作單上
					if($itemlist[$i]['order']=='－'){
						//echo 'groupcode='.$groupcode.PHP_EOL;
						//echo 'grtitle='.$itemlist[$i]['grtitle'].PHP_EOL;
						//echo 'itemname='.$itemlist[$i]['name'].PHP_EOL;
						//if($groupcode==-1||$groupcode!=$itemlist[$i]['grtitle']){
							if(in_array($menu[$itemlist[$i]['no']]['printtype'],$atgroup)){
							}
							else{
								$groupcode=$itemlist[$i]['grtitle'];
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								if(isset($print['kitchen']['grouptitlesize'])){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
								}
								if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
								}
								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$itemlist[$i]['grtitle']]['name2']!=''){
									if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'];
									}
									else{
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$itemlist[$i]['grtitle']]['name2'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname2'].')';
									}
								}
								else{
								}
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
								//$conarray['grouptype']['-1'] .= $itemlist[$i]['number'];
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
								}
								else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
									//$conarray['grouptype']['-1'] .= $itemlist[$i]['number'];
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								}
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
								if(strlen($itemlist[$itemlist[$i]['grtitle']]['taste1'])>0){
									$tasteno=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1']);
									$temp=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1name']);
									$temp2=preg_split('/,/',$itemlist[$itemlist[$i]['grtitle']]['taste1number']);
									$linetaste='';
									for($t=0;$t<sizeof($temp);$t++){
										if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
											$tt=preg_split('/\//',$temp[$t]);

											/*if(isset($tt[1])){
												if(intval($temp2[$t])>1){
													$tt[0]=$tt[0].'*'.$temp2[$t];
												}
												else{
												}
											}
											else{
											}*/

											if($linetaste==''){
												$linetaste = '　+';
												if($tasteno[$t]!='999991'){
												}
												else{
													if($print['item']['tastefront']=='1'){
														$linetaste .= '註:';
													}
													else{
													}
												}
												$linetaste .= $tt[0];
											}
											else{
												$linetaste .= ',';
												if($tasteno[$t]!='999991'){
												}
												else{
													if($print['item']['tastefront']=='1'){
														$linetaste .= '註:';
													}
													else{
													}
												}
												$linetaste .= $tt[0];
											}

											if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
												$linetaste .= '/ '.$tt[1];
											}
											else if(isset($tt[1])&&$tt[1]!=''){
												if(intval($temp2[$t])>1){
													$linetaste .= '*'.$temp2[$t];
												}
												else{
												}
											}
											else{
											}
										}
										else{//備註一項一行
											$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
											if(isset($print['kitchen']['tastesize'])){
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
											}
											else{
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
											}
											//$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
											$tt=preg_split('/\//',$temp[$t]);
											if($tasteno[$t]!='999991'){
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
											}
											else{
												if($print['item']['tastefront']=='1'){
													$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '　+註:'.$tt[0];
												}
												else{
													$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
												}
											}
											if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$tt[1];
											}
											else if(isset($tt[1])&&$tt[1]!=''){
												if(intval($temp2[$t])>1){
													$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '*'.$temp2[$t];
												}
												else{
												}
											}
											else{
											}
											/*if(intval($temp2[$t])>1){
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '*'.$temp2[$t];
											}
											else{
											}*/
											$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
											$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
											$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '';
											$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
											if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
											}
											else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '';
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
											}
											$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
										}
									}
									if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
										if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
											if(isset($print['kitchen']['tastesize'])){
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
											}
											else{
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
											}
										}
										else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
											if(isset($print['kitchen']['tastesize'])){
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
											}
											else{
												$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
											}
										}
										//$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
									}
									else{//備註一項一行
										
									}
								}
								else{
								}
								array_push($atgroup,$itemlist[$i]['grtitle']);
							}
						/*}
						else{
						}*/
						/*if(in_array($itemlist[$i]['grtitle'],$atgroup)){
						}
						else{
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
							}
							else{
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
							}
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
							//$conarray['grouptype']['-1'] .= $itemlist[$i]['number'];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							array_push($atgroup,$itemlist[$i]['grtitle']);
						}*/
						if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'];
							}
							else{
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
								if(strlen($itemlist[$i]['mname1'])==''){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'];
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
								}
							}
							else{
							}
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						}
						else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2822" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'];
							}
							else{
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
								if(strlen($itemlist[$i]['mname1'])==''){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'];
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
								}
							}
							else{
							}
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="880" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['subtotal'];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							if(isset($conamt[$menu[$itemlist[$i]['no']]['printtype']])){
								$conamt[$menu[$itemlist[$i]['no']]['printtype']]+=$itemlist[$i]['subtotal'];
							}
							else{
								$conamt[$menu[$itemlist[$i]['no']]['printtype']]=$itemlist[$i]['subtotal'];
							}
						}
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					else{
						if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
							}
							else{
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
								if(strlen($itemlist[$i]['mname1'])==''){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'];
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
								}
							}
							else{
							}
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						}
						else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2822" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
							}
							else{
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$itemlist[$i]['name2']!=''){
								if(strlen($itemlist[$i]['mname1'])==''){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'];
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$itemlist[$i]['name2'].'('.$itemlist[$i]['mname2'].')';
								}
							}
							else{
							}
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="880" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['subtotal'];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							if(isset($conamt[$menu[$itemlist[$i]['no']]['printtype']])){
								$conamt[$menu[$itemlist[$i]['no']]['printtype']]+=$itemlist[$i]['subtotal'];
							}
							else{
								$conamt[$menu[$itemlist[$i]['no']]['printtype']]=$itemlist[$i]['subtotal'];
							}
						}
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					
					if(strlen($itemlist[$i]['taste1'])>0){
						$tasteno=preg_split('/,/',$itemlist[$i]['taste1']);
						$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
						$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
						$linetaste='';
						for($t=0;$t<sizeof($temp);$t++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								$tt=preg_split('/\//',$temp[$t]);

								/*if(isset($tt[1])){
									if(intval($temp2[$t])>1){
										$tt[0]=$tt[0].'*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}*/
								
								if($linetaste==''){
									$linetaste = '　+';
									if($tasteno[$t]!='999991'){
									}
									else{
										if($print['item']['tastefront']=='1'){
											$linetaste .= '註:';
										}
										else{
										}
									}
									$linetaste .= $tt[0];
								}
								else{
									$linetaste .= ',';
									if($tasteno[$t]!='999991'){
									}
									else{
										if($print['item']['tastefront']=='1'){
											$linetaste .= '註:';
										}
										else{
										}
									}
									$linetaste .= $tt[0];
								}

								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
									$linetaste .= '/ '.$tt[1];
								}
								else if(isset($tt[1])&&$tt[1]!=''){
									if(intval($temp2[$t])>1){
										$linetaste .= '*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}
							}
							else{//備註一項一行
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								if(isset($print['kitchen']['tastesize'])){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
								}
								//$table .= '-'.$_POST['taste1'][$t];
								$tt=preg_split('/\//',$temp[$t]);
								if($tasteno[$t]!='999991'){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
								}
								else{
									if($print['item']['tastefront']=='1'){
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '　+註:'.$tt[0];
									}
									else{
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
									}
								}
								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$tt[1];
								}
								else if(isset($tt[1])&&$tt[1]!=''){
									if(intval($temp2[$t])>1){
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '';
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
								}
								else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '';
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								}
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							}
						}
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							if(!isset($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype'])||$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
								if(isset($print['kitchen']['tastesize'])){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
								}
							}
							else{//if($pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchentype']=='1')//2022/1/21 印價格
								if(isset($print['kitchen']['tastesize'])){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['tastesize'].'"/><w:szCs w:val="'.$print['kitchen']['tastesize'].'"/></w:rPr><w:t>';
								}
								else{
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr><w:t>';
								}
							}
							//$table .= '-'.$_POST['taste1'][$t];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						}
						else{//備註一項一行
							
						}
					}
					else{
					}
				}
				else{
				}
			}
		}
	}
	if(sizeof($kitcontent)>0){
		//print_r($kitcontent);
		foreach($kitcontent as $printindex=>$indexcontent){
			$PHPWord = new PHPWord();
			if(isset($print['item']['kitchentype'])&&file_exists('../../../template/kitchen'.$print['item']['kitchentype'].'.docx')){
				$document = $PHPWord->loadTemplate('../../../template/kitchen'.$print['item']['kitchentype'].'.docx');
			}
			else{
				$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
			}
			$document->setValue('item',$indexcontent);
			date_default_timezone_set($content['init']['settime']);
			$filename=date("YmdHis");
			if($print['item']['kitchen']!='0'&&$pti[$printindex]['kitchen'.$listtype]=='1'&&(!isset($looptype)||(isset($looptype)&&$looptype=='1'))){
			//2022/5/27 looptype=3只出總單改成只出明細單，不印工作單與總單 倫
			//if($print['item']['kitchen']!='0'&&$pti[$printindex]['kitchen'.$listtype]=='1'&&(!isset($looptype)||(isset($looptype)&&($looptype=='1'||$looptype=='3')))){
				//$document->save("../../../print/noread/".$filename."_list".$menu[$itemlist[$i]['no']]['printtype']."_".$consecnumber."_".$i.".docx");
				$document->save("../../../print/read/".$consecnumber."_list".$listtype.$printindex."_".$filename.".docx");
				if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
					$prt=fopen("../../../print/noread/".$consecnumber."_list".$listtype.$printindex."_".$filename.".".$_POST['machinetype'],'w');
				}
				else{
					$prt=fopen("../../../print/noread/".$consecnumber."_list".$listtype.$printindex."_".$filename.".prt",'w');
				}
				fclose($prt);
				/*if(intval($print['item']['kitchen'])>1){
					for($j=1;$j<intval($print['item']['kitchen']);$j++){
						copy("../../../print/noread/list".$pt."_".$consecnumber."_".$filename.".docx","../../../print/noread/list".$pt."_".$consecnumber."_".$filename."(".$j.").docx");
					}
				}
				else{
				}*/
			}
			else{
				$document->save("../../../print/read/delete1_list".$listtype.$print['item']['kitchen']."_".$pti[$printindex]['kitchen'.$listtype].".docx");
			}
		}
	}
	else{
	}
	//print_r($conarray);
	foreach($conarray as $k=>$v){
		if($v==''||sizeof($v)==0){
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
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

							if($listlan!='-1'){
								$table .= $reprintlabel.$listlan['name']['listname'];
							}
							else{
								$table .= $reprintlabel."廚房工作單";
							}

							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";

							break;
						case 'type':
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

							//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
								if($listtype=='1'){
									if(isset($tempreserve)){
										$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$table .= $listhint."\r\n";
										}
										else{
										}
										$table .= $buttons['name']['listtype1'].' '.$saleno;
									}
									else{
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$table .= $listhint."\r\n";
										}
										else{
										}
										$table .= $buttons['name']['listtype1'].' '.$saleno;
									}
								}
								else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
									if(isset($tempreserve)){
										$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$table .= $listhint."\r\n";
										}
										else{
										}
										$table .= $buttons['name']['listtype'.$listtype].' '.$saleno;
									}
									else{
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$table .= $listhint."\r\n";
										}
										else{
										}
										$table .= $buttons['name']['listtype'.$listtype].' '.$saleno;
									}
								}
								else{
									if(isset($tempreserve)){
										$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$table .= $listhint."\r\n";
										}
										else{
										}
										$table .= $buttons['name']['listtype4'].' '.$saleno;
									}
									else{
										if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
											$table .= $listhint."\r\n";
										}
										else{
										}
										$table .= $buttons['name']['listtype4'].' '.$saleno;
									}
								}
								if($k!='0'&&$k=='grouptype'){
								}
								else{//2020/4/24 非依列印類別分類，產生列印類別名稱
									$table .= ' '.$pti[$k]['name'];
								}
							/*}
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
								if($listtype=='1'){
									if(isset($tempreserve)){
										if($listlan!='-1'){
											$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno;
										}
										else{
											$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno;
										}
									}
									else{
										if($listlan!='-1'){
											$table .= $buttons['name']['listtype1'].' '.$saleno;
										}
										else{
											$table .= $buttons['name']['listtype1'].' '.$saleno;
										}
										
									}
								}
								else if($listtype=='2'){
									if(isset($tempreserve)){
										if($listlan!='-1'){
											$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno;
										}
										else{
											$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno;
										}
									}
									else{
										if($listlan!='-1'){
											$table .= $buttons['name']['listtype2'].' '.$saleno;
										}
										else{
											$table .= $buttons['name']['listtype2'].' '.$saleno;
										}
									}
								}
								else if($listtype=='3'){
									if(isset($tempreserve)){
										if($listlan!='-1'){
											$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno;
										}
										else{
											$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno;
										}
									}
									else{
										if($listlan!='-1'){
											$table .= $buttons['name']['listtype3'].' '.$saleno;
										}
										else{
											$table .= $buttons['name']['listtype3'].' '.$saleno;
										}
									}
								}
								else{
									if(isset($tempreserve)){
										if($listlan!='-1'){
											$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno;
										}
										else{
											$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno;
										}
									}
									else{
										if($listlan!='-1'){
											$table .= $buttons['name']['listtype4'].' '.$saleno;
										}
										else{
											$table .= $buttons['name']['listtype4'].' '.$saleno;
										}
									}
								}
								if($k!='0'&&$k=='grouptype'){
									if($listlan!='-1'){
										$table .= "\r\n".$tablename.$listlan['name']['table'];
									}
									else{
										$table .= "\r\n".$tablename."號桌";
									}
								}
								else{//2020/4/24 非依列印類別分類，產生列印名稱
									if($listlan!='-1'){
										$table .= " ".$pti[$k]['name']."\r\n".$tablename.$listlan['name']['table'];
									}
									else{
										$table .= " ".$pti[$k]['name']."\r\n".$tablename."號桌";
									}
								}
							}*/

							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";

							break;
						case 'table'://2020/8/31 阿倫提出：把桌號獨立出來
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

							if($_POST['tablenumber']==''){
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
							}
							else{
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
								if($listlan!='-1'){
									$table .= $tablename.$listlan['name']['table'];
								}
								else{
									$table .= $tablename."號桌";
								}
							}

							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";

							break;
						case 'time':
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

							date_default_timezone_set($content['init']['settime']);
							if(isset($_POST['reusername'])){//2021/8/4 補印
								$table .= $_POST['consecnumber'].' '.$_POST['username'];
								$table .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
							}
							else{
								$table .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
							}

							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";

							break;
						case 'numman':
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/></w:rPr><w:t>';
							
							$persontext="";
							if(file_exists('../../../database/floorspend.ini')){
								$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
								if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($_POST['person1']!=0||$_POST['person2']!=0||$_POST['person3']!=0)){
									if($floorspend['person1']['name']!=''&&$_POST['person1']!=0){
										if($persontext!=""){
											$persontext=$persontext.',';
										}
										else{
										}
										$persontext=$persontext.$floorspend['person1']['name'].":".$_POST['person1'];
									}
									else{
									}
									if($floorspend['person2']['name']!=''&&$_POST['person2']!=0){
										if($persontext!=""){
											$persontext=$persontext.',';
										}
										else{
										}
										$persontext=$persontext.$floorspend['person2']['name'].":".$_POST['person2'];
									}
									else{
									}
									if($floorspend['person3']['name']!=''&&$_POST['person3']!=0){
										if($persontext!=""){
											$persontext=$persontext.',';
										}
										else{
										}
										$persontext=$persontext.$floorspend['person3']['name'].":".$_POST['person3'];
									}
									else{
									}
								}
								else{
								}
							}
							else{
							}
							$table .= $persontext;

							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";

							break;
						default:
							break;
					}
				}
			}
			else{
				//story
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

				if($listlan!='-1'){
					$table .= $reprintlabel.$listlan['name']['listname'];
				}
				else{
					$table .= $reprintlabel."廚房工作單";
				}

				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				
				//type
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

				//if($_POST['tablenumber']==''){//2020/8/31 阿倫提出：把桌號獨立出來
					if($listtype=='1'){
						if(isset($tempreserve)){
							$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$table .= $listhint."\r\n";
							}
							else{
							}
							$table .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
						else{
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$table .= $listhint."\r\n";
							}
							else{
							}
							$table .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
					}
					else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
						if(isset($tempreserve)){
							$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$table .= $listhint."\r\n";
							}
							else{
							}
							$table .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
						else{
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$table .= $listhint."\r\n";
							}
							else{
							}
							$table .= $buttons['name']['listtype'.$listtype].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
					}
					else{
						if(isset($tempreserve)){
							$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n";
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$table .= $listhint."\r\n";
							}
							else{
							}
							$table .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
						else{
							if(isset($listhint)&&isset($print['kitchen']['listhint'])&&$print['kitchen']['listhint']=='1'){
								$table .= $listhint."\r\n";
							}
							else{
							}
							$table .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
						}
					}
					if($k!='0'&&$k=='grouptype'){
					}
					else{//2020/4/24 非依列印類別分類，產生列印類別名稱
						$table .= ' '.$pti[$k]['name'];
					}
				/*}
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
					if($listtype=='1'){
						if(isset($tempreserve)){
							if($listlan!='-1'){
								$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
						else{
							if($listlan!='-1'){
								$table .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$table .= $buttons['name']['listtype1'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							
						}
					}
					else if($listtype=='2'){
						if(isset($tempreserve)){
							if($listlan!='-1'){
								$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
						else{
							if($listlan!='-1'){
								$table .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$table .= $buttons['name']['listtype2'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
					}
					else if($listtype=='3'){
						if(isset($tempreserve)){
							if($listlan!='-1'){
								$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
						else{
							if($listlan!='-1'){
								$table .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$table .= $buttons['name']['listtype3'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
					}
					else{
						if(isset($tempreserve)){
							if($listlan!='-1'){
								$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$table .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2).' '.substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
						else{
							if($listlan!='-1'){
								$table .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$table .= $buttons['name']['listtype4'].' '.$saleno.' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
					}
					if($k!='0'&&$k=='grouptype'){
						if($listlan!='-1'){
							$table .= "\r\n".$tablename.$listlan['name']['table'];
						}
						else{
							$table .= "\r\n".$tablename."號桌";
						}
					}
					else{//2020/4/24 非依列印類別分類，產生列印類別名稱
						if($listlan!='-1'){
							$table .= " ".$pti[$k]['name']."\r\n".$tablename.$listlan['name']['table'];
						}
						else{
							$table .= " ".$pti[$k]['name']."\r\n".$tablename."號桌";
						}
					}
				}*/

				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";

				//table//2020/8/31 阿倫提出：把桌號獨立出來
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

				if($_POST['tablenumber']==''){
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="2"/><w:szCs w:val="2"/></w:rPr><w:t>';
				}
				else{
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['tablefontsize'])*2).'"/></w:rPr><w:t>';
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
					if($listlan!='-1'){
						$table .= $tablename.$listlan['name']['table'];
					}
					else{
						$table .= $tablename."號桌";
					}
				}

				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				
				//time
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';

				date_default_timezone_set($content['init']['settime']);
				if(isset($_POST['reusername'])){//2021/8/4 補印
					$table .= $_POST['consecnumber'].' '.$_POST['username'];
					$table .= "\r\n".$reprintlabel.$_POST['reusername'].' '.date('m/d H:i');
				}
				else{
					$table .= $_POST['consecnumber'].' '.$_POST['username'].' '.date('m/d H:i');
				}

				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";

				/*預設不顯示人數*/
			}
			$table .= '</w:tbl>';
			
			/*$document->setValue('consecnumber',$consecnumber);
			//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
			//$document->setValue('tel', '(04)2473-2003');
			//$document->setValue('time', date('Y/m/s H:i:s'));
			if($listlan!='-1'){
				$document->setValue('story',$listlan['name']['listname']);
			}
			else{
				$document->setValue('story',"廚房工作單");
			}*/
			$tindex=0;
			if(!isset($pti[$k]['kitchentype'])||$pti[$k]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "Items";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "QTY";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{//if($pti[$k]['kitchentype']=='1')//2022/1/21 印價格
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2822"/><w:gridCol w:w="880"/><w:gridCol w:w="1298"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2822" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "Items";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="880" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "QTY";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "sub";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			$sum=0;
			if(!is_numeric($k)&&$k=="grouptype"){
				foreach((array)$v as $pt=>$pv){
					if($pv!=''){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';

						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($print['kitchen']['printtypesize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['printtypesize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($print['kitchen']['printtypesize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['printtypesize'])*2).'"/></w:rPr><w:t>';
						//$table .= '-'.$_POST['taste1'][$t];
						$table .= $pti[$pt]['name'];
						$table .= "</w:t></w:r></w:p></w:tc>";

						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($print['kitchen']['printtypesize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['printtypesize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['printtypesize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['printtypesize'])*2).'"/></w:rPr><w:t>';
						$table .= "</w:t></w:r></w:p></w:tc>";

						$table .= "</w:tr>";

						$table .= $pv;

						if(!isset($pti[$pt]['kitchentype'])||$pti[$pt]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
						}
						else{//if($pti[$pt]['kitchentype']=='1')//2022/1/21 印價格
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3702" w:type="pct"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:b/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
							$table .= "小計";
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
							$table .= $conamt[$pt];
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}

						$grouptypecontent++;
					}
					else{
					}
				}
			}
			else{
				$table .= $v;
				if(!isset($pti[$k]['kitchentype'])||$pti[$k]['kitchentype']=='0'){//2022/1/21 原始版本，不印價格
				}
				else{//if($pti[$k]['kitchentype']=='1')//2022/1/21 印價格
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3702" w:type="pct"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:b/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
					$table .= "小計";
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1298" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="'.$print['kitchen']['numberfontalign'].'"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="'.$print['kitchen']['numberfontalign'].'"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['qtysize'].'"/><w:szCs w:val="'.$print['kitchen']['qtysize'].'"/></w:rPr><w:t>';
					$table .= $conamt[$k];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
			}
			$table .= '</w:tbl>';

			if(isset($memdata[0]['name'])&&isset($print['kitchen']['memberdata'])&&$print['kitchen']['memberdata']=='1'){
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';

				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "會員電話";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				if(strlen($memdata[0]['tel'])==10){
					$table .= substr($memdata[0]['tel'],0,4).'***'.substr($memdata[0]['tel'],-3);
				}
				else if(strlen($memdata[0]['tel'])==8){
					$table .= '*****'.substr($memdata[0]['tel'],-3);
				}
				else{
					$table .= $memdata[0]['tel'];
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";

				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "會員姓名";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $memdata[0]['name'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";

				$table .= '</w:tbl>';
			}
			else{
			}
	
			$document->setValue('item',$table);
			//$document->setValue('total','NT.'.$_POST['total']);
			date_default_timezone_set($content['init']['settime']);
			$filename=date("YmdHis");
			if($k=='-1'){
				if($print['item']['kitchen']!='0'&&(!isset($looptype)||(isset($looptype)&&$looptype=='1'))){
				//2022/5/27 looptype=3只出總單改成只出明細單，不印工作單與總單 倫
				//if($print['item']['kitchen']!='0'&&(!isset($looptype)||(isset($looptype)&&($looptype=='1'||$looptype=='3')))){
					//$document->save("../../../print/noread/".$filename."_listN_".$consecnumber.".docx");
					$document->save("../../../print/read/".$consecnumber."_list".$listtype."N_".$filename.".docx");
					if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
						$prt=fopen("../../../print/noread/".$consecnumber."_list".$listtype."N_".$filename.".".$_POST['machinetype'],'w');
					}
					else{
						$prt=fopen("../../../print/noread/".$consecnumber."_list".$listtype."N_".$filename.".prt",'w');
					}
					fclose($prt);
				}
				else{
					$document->save("../../../print/read/delete2_list".$listtype."N.docx");
				}
			}
			else{
				if($print['item']['kitchen']!='0'&&(($k!='0'&&$k=='grouptype'&&$grouptypecontent!=0)||(isset($pti[$k])&&$pti[$k]['kitchen'.$listtype]=='1'))&&(!isset($looptype)||(isset($looptype)&&$looptype=='1'))){
				//2022/5/27 looptype=3只出總單改成只出明細單，不印工作單與總單 倫
				//if($print['item']['kitchen']!='0'&&(($k!='0'&&$k=='grouptype'&&$grouptypecontent!=0)||(isset($pti[$k])&&$pti[$k]['kitchen'.$listtype]=='1'))&&(!isset($looptype)||(isset($looptype)&&($looptype=='1'||$looptype=='3')))){
					//$document->save("../../../print/noread/".$filename."_list".$k."_".$consecnumber.".docx");
					$document->save("../../../print/read/".$consecnumber."_list".$listtype.$k."_".$filename.".docx");
					if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
						$prt=fopen("../../../print/noread/".$consecnumber."_list".$listtype.$k."_".$filename.".".$_POST['machinetype'],'w');
					}
					else{
						$prt=fopen("../../../print/noread/".$consecnumber."_list".$listtype.$k."_".$filename.".prt",'w');
					}
					fclose($prt);
				}
				else{
					$document->save("../../../print/read/delete3_list".$listtype.$k.".docx");
				}
			}
		}
	}
}
else{
}
?>