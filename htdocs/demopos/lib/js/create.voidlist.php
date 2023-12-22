<?php
include_once '../../../tool/myerrorlog.php';
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$content=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);
//$file=fopen('../../log.txt','w');
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT * FROM voiditem WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND TERMINALNUMBER="'.$_POST['machine'].'-'.$_POST['tablenumber'].'" AND state=1';
$voidlist=sqlquery($conn,$sql,'sqlite');
$linenumber='';
foreach($voidlist as $v){
	//2020/12/2 在原先的SQL基礎上加入時間，避免將後續點入的品項刪除
	$sql='DELETE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER="'.$v['LINENUMBER'].'" AND CREATEDATETIME="'.$v['CREATEDATETIME'].'"';
	sqlnoresponse($conn,$sql,'sqlite');
	
	//2020/12/2 因為在刪除品項的流程，就已將品項刪除，這邊若再使用原先的SQL刪除，會將後面新點進去的品項刪除
	/*if($linenumber==''){
		$linenumber='"'.$v['LINENUMBER'].'"';
	}
	else{
		$linenumber=$linenumber.',"'.$v['LINENUMBER'].'"';
	}*/
}
//2020/12/2 因為在刪除品項的流程，就已將品項刪除，這邊若再使用原先的SQL刪除，會將後面新點進去的品項刪除
//$sql='DELETE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND LINENUMBER IN ('.$linenumber.')';
//sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$print=parse_ini_file('../../../database/printlisttag.ini',true);
if(file_exists('../../syspram/clientlist-'.$content['init']['firlan'].'.ini')){
	$list=parse_ini_file('../../syspram/clientlist-'.$content['init']['firlan'].'.ini',true);
}
else if(file_exists('../../syspram/clientlist-1.ini')){
	$list=parse_ini_file('../../syspram/clientlist-1.ini',true);
}
else{
	$list='-1';
}
if(sizeof($voidlist)>0&&isset($print['item']['voidclient'])&&$print['item']['voidclient']==1){
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
	$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
	$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
	$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
	$saleinvdata='';
	$looptype=$voidlist[0]['REMARKS'];

	$document1='';
	$oinv='';
	$PHPWord = new PHPWord();
	if(isset($print['item']['clienttype'])&&file_exists('../../../template/clientlist'.$print['item']['clienttype'].'.docx')){
		$document1 = $PHPWord->loadTemplate('../../../template/clientlist'.$print['item']['clienttype'].'.docx');
	}
	else{//舊版明細單
		$document1 = $PHPWord->loadTemplate('../../../template/clientlist.docx');
	}
	//$document1 = $PHPWord->loadTemplate('../../../template/clientlist.docx');
	if($looptype=='1'){
		if($_POST['tablenumber']==''){
			$document1->setValue('type',"退菜明細\r\n".$buttons['name']['listtype1']);
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
			$document1->setValue('type',"退菜明細\r\n".$buttons['name']['listtype1']."\r\n".$tablename.'號桌');
		}
		
	}
	else if($looptype=='2'){
		$document1->setValue('type',"退菜明細\r\n".$buttons['name']['listtype2']);
	}
	else if($looptype=='3'){
		$document1->setValue('type',"退菜明細\r\n".$buttons['name']['listtype3']);
	}
	else{
		$document1->setValue('type',"退菜明細\r\n".$buttons['name']['listtype4']);
	}
	$document1->setValue('consecnumber',intval($consecnumber));
	
	if($list!='-1'&&isset($list['name']['bizdatelabel'])){
		$document1->setValue('bizdate',$list['name']['bizdatelabel'].':'.substr($voidlist[0]['BIZDATE'],0,4).'/'.substr($voidlist[0]['BIZDATE'],4,2).'/'.substr($voidlist[0]['BIZDATE'],6,2));
	}
	else{
		$document1->setValue('bizdate','營業日:'.substr($voidlist[0]['BIZDATE'],0,4).'/'.substr($voidlist[0]['BIZDATE'],4,2).'/'.substr($voidlist[0]['BIZDATE'],6,2));
	}
	if($list!='-1'&&isset($list['name']['datetimelabel'])){
		$document1->setValue('datetime',$list['name']['datetimelabel'].':'.substr($voidlist[0]['CREATEDATETIME'],8,2).':'.substr($voidlist[0]['CREATEDATETIME'],10,2).':'.substr($voidlist[0]['CREATEDATETIME'],12,2));
	}
	else{
		$document1->setValue('datetime','開單時間:'.substr($voidlist[0]['CREATEDATETIME'],8,2).':'.substr($voidlist[0]['CREATEDATETIME'],10,2).':'.substr($voidlist[0]['CREATEDATETIME'],12,2));
	}
	if($list!='-1'&&isset($list['name']['voidtimelabel'])){
		$document1->setValue('saletime',$list['name']['voidtimelabel'].':'.date('H:i:s'));
	}
	else{
		$document1->setValue('saletime','作廢時間:'.date('H:i:s'));
	}
	
	$document1->setValue('story',$data['basic']['storyname']);
	$tindex=0;
	$temporderlist=1;
	$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "Items";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "U/P";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= "Sub";
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	$sum=0;
	$totalqty=0;
	//$newitem=0;
	$tempindex=0;
	for($i=0;$i<sizeof($voidlist);$i=$i+2){
		$totalqty=intval($totalqty)+intval($voidlist[$i]['QTY']);
		/*if(isset($_POST['tempbuytype'])&&$_POST['tempbuytype']=='2'&&isset($_POST['templistitem'][$i])){
			$tempindex++;
		}
		else if(isset($pti[$menu[$_POST['no'][$i]]['printtype']]['clientlist'.$looptype])&&$pti[$menu[$_POST['no'][$i]]['printtype']]['clientlist'.$looptype]=='0'){
			$tempindex++;
		}
		else{*/
			//$newitem++;
			$temporderlist=0;
			/*if(strlen($menu[intval($voidlist[$i]['ITEMCODE'])]['name1'])>$print['item']['clientlength']){
				if(strlen($voidlist[$i]['UNITPRICELINK'])==0){
					$temp=$menu[intval($voidlist[$i]['ITEMCODE'])]['name1'].' x '.$voidlist[$i]['QTY'];
				}
				else{
					$temp=$menu[intval($voidlist[$i]['ITEMCODE'])]['name1'].' ( '.$voidlist[$i]['UNITPRICELINK'].' ) x '.$voidlist[$i]['QTY'];
				}

				$ttt=preg_split('/ /',$temp);
				$substr=['',''];
				$index=0;
				for($a=0;$a<sizeof($ttt);$a++){
					if(strlen($substr[$index].$ttt[$a])<$print['item']['clientlength']){
						$substr[$index]=$substr[$index].$ttt[$a];
					}
					else{
						if($substr[$index]==''){
							$substr[$index]=$ttt[$a];
						}
						else{
							if($index=='0'){
								$index++;
							}
							else{
							}
							$substr[$index]=$substr[$index].$ttt[$a];
						}
					}
				}
				//$substr[0]=substr($temp,0,$print['item']['clientlength']);
				//$substr[1]=substr($temp,$print['item']['clientlength'],(strlen($temp)-$print['item']['clientlength']));
				for($j=0;$j<2;$j++){
					if($j==0){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
						$table .= $substr[$j];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $content['init']['frontunit'].$voidlist[$i]['UNITPRICE'].$content['init']['unit'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $content['init']['frontunit'].$voidlist[$i]['AMT'].$content['init']['unit'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
					else{
						if(strlen($substr[$j])==0){
						}
						else{
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3025" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							$table .= $substr[$j];
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
					}
				}
			}*/
			//else{
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
				if(strlen($voidlist[$i]['UNITPRICELINK'])==0){
					$table .= $voidlist[$i]['ITEMNAME'].'x'.$voidlist[$i]['QTY'];
				}
				else{
					$table .= $voidlist[$i]['ITEMNAME'].'('.$voidlist[$i]['UNITPRICELINK'].')x'.$voidlist[$i]['QTY'];
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$voidlist[$i]['UNITPRICE'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$voidlist[$i]['AMT'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			//}
			for($t=1;$t<=10;$t++){
				if($voidlist[$i]['SELECTIVEITEM'.$t]==null){
					break;
				}
				else{
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					$temptaste=preg_split('/,/',$voidlist[$i]['SELECTIVEITEM'.$t]);
					for($j=0;$j<sizeof($temptaste);$j++){
						if(preg_match('/99999/',$temptaste[$j])){//手打備註
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							$table .= '　+'.substr($temptaste[$j],7);
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
						else{
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							$tt='';
							if(intval(substr($temptaste[$j],5,1))==1){
								$tt=$taste[intval(substr($temptaste[$j],0,5))]['name1'];
							}
							else{
								$tt=$taste[intval(substr($temptaste[$j],0,5))]['name1'].'*'.substr($temptaste[$j],5,1);
							}
							$table .= '　+'.$tt;
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							if(intval($taste[intval(substr($temptaste[$j],0,5))]['money'])==0){
							}
							else{
								$table .= $taste[intval(substr($temptaste[$j],0,5))]['money'];
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							if((intval($taste[intval(substr($temptaste[$j],0,5))]['money'])*intval(substr($temptaste[$j],5,1)))==0){
							}
							else{
								$table .= $content['init']['frontunit'].(intval($taste[intval(substr($temptaste[$j],0,5))]['money'])*intval(substr($temptaste[$j],5,1))).$content['init']['unit'];
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
					}
				}
				/*else if(preg_match('/99999/',$voidlist[$i]['SELECTIVEITEM'.$t])){//手打備註
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					$table .= '　+'.substr($voidlist[$i]['SELECTIVEITEM'.$t],7);
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					$tt='';
					if(intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],5,1))==1){
						$tt=$taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
					}
					else{
						$tt=$taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'].'*'.substr($voidlist[$i]['SELECTIVEITEM'.$t],5,1);
					}
					$table .= '　+'.$tt;
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					if(intval($taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])==0){
					}
					else{
						$table .= $taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'];
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					if((intval($taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],5,1)))==0){
					}
					else{
						$table .= $content['init']['frontunit'].(intval($taste[intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($voidlist[$i]['SELECTIVEITEM'.$t],5,1))).$content['init']['unit'];
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}*/
			}
			if(isset($voidlist[$i+1])&&$voidlist[$i+1]['ITEMCODE']=='item'&&$voidlist[$i+1]['AMT']!=0){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
				//$table .= '-'.$_POST['taste1'][$t];
				$table .= '　+優惠折抵';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$voidlist[$i+1]['AMT'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
			}
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
			$table .= '▶'.$voidlist[$i]['reason'];
			$table .= "</w:t></w:r></w:p></w:tc></w:tr>";
		//}
	}
	//echo 'sizeof:'.sizeof($_POST['no'])."\r\n";
	//echo 'length:'.$tempindex."\r\n";
	$table .= '</w:tbl>';
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clititle'].'"/><w:szCs w:val="'.$print['item']['clititle'].'"/></w:rPr><w:t>';
	if($list!='-1'){
		$table .= $list['name']['qty'];
	}
	else{
		$table .= '商品數量';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clicont'].'"/><w:szCs w:val="'.$print['item']['clicont'].'"/></w:rPr><w:t>';
	$table .= $totalqty;
	$table .= "</w:t></w:r></w:p></w:tc>";
	
	
	/*$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	//$table .= '-'.$_POST['taste1'][$t];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= 'QTY';
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= $totalqty;
	$table .= "</w:t></w:r></w:p></w:tc>";*/
	$table .= "</w:tr></w:tbl>";
	$document1->setValue('memtable','');
	/*$document1->setValue('memtel','');
	$document1->setValue('memaddress','');
	$document1->setValue('memremarks','');*/

	$document1->setValue('item',$table);
	date_default_timezone_set('Asia/Taipei');
	//$datetime=date('YmdHis');

	$document1->replaceStrToQrcode('qrcode','empty');

	$filename=date('YmdHis');
	if($print['item']['voidclient']==1){
		//$document1->save("../../../print/noread/".$filename."_voidclientlist_".intval($consecnumber).".docx");
		$document1->save("../../../print/read/".$filename."_voidclientlist_".intval($consecnumber).".docx");
		$prt=fopen("../../../print/noread/".$filename."_voidclientlist_".intval($consecnumber).".prt",'w');
		fclose($prt);
	}
	else{
		$document1->save("../../../print/read/delete_voidclientlist_".intval($consecnumber)."_".$filename.".docx");
	}
}
else{
}
?>