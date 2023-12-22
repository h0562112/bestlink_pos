<?php
include_once '../../../tool/myerrorlog.php';
$no=$_POST['no'];
$date=$_POST['date'];
$type=$_POST['type'];
//echo $type;

$content=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);

if(isset($_POST['index'])){
	$index=preg_split('/,/',$_POST['index']);
}
else{
}
if(isset($_POST['qty'])){
	$qty=preg_split('/,/',$_POST['qty']);
}
else{
}
if(isset($_POST['inumber'])){
	$inumber=preg_split('/,/',$_POST['inumber']);
}
else{
}
if($type=='all'||$type=='list'){
	/*if(file_exists('../../../print/read/clientlist_'.intval($no).'_'.$date.'.docx')){
		copy('../../../print/read/clientlist_'.intval($no).'_'.$date.'.docx','../../../print/noread/clientlist_'.intval($no).'_'.$date.'.docx');
	}
	else{
	}*/

	////echo $no;
	include_once '../../../tool/dbTool.inc.php';
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'" ORDER BY LINENUMBER ASC';
	$list=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'"';
	$sale=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT saleno FROM salemap WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$no.'"';
	$saleno=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT SUM(AMT) AS dis FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'" AND (ITEMCODE="list" OR ITEMCODE="member")';
	$dis=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'" AND ITEMCODE="autodis"';
	$autodis=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(file_exists('../../../database/sale/cover.db')){
		$conn=sqlconnect('../../../database/sale','cover.db','','','','sqlite');
		$sql='SELECT * FROM list WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$no.'"';
		$cover=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
	}
	else{
	}
	$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
	$sql='SELECT inumber,isgroup FROM itemsdata';
	$isgroupset=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$igs=array();
	foreach($isgroupset as $v){
		$igs[intval($v['inumber'])]=intval($v['isgroup']);
	}
	require_once '../../../tool/PHPWord.php';
	

	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
	$data=parse_ini_file('../../../database/setup.ini',true);
	$print=parse_ini_file('../../../database/printlisttag.ini',true);
	if(isset($print['item']['clititle'])&&$print['item']['clititle']!=''){
		$clititle=$print['item']['clititle'];
	}
	else{
		$clititle=32;
	}
	if(isset($print['item']['clicont'])&&$print['item']['clicont']!=''){
		$clicont=$print['item']['clicont'];
	}
	else{
		$clicont=32;
	}
	if(isset($print['clientlist']['invsize'])){
	}
	else{
		$print['clientlist']['invsize']="20";
	}
	
	$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
	$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
	$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
	$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
	if(file_exists('../../syspram/clientlist-'.$content['init']['firlan'].'.ini')){
		$ininame=parse_ini_file('../../syspram/clientlist-'.$content['init']['firlan'].'.ini',true);
	}
	else{
		$ininame='-1';
	}
	$otherpay=parse_ini_file('../../../database/otherpay.ini',true);

	if(!file_exists('../../../database/straw.ini')||$content['init']['comstraw']=='0'){
		$strawarray='-1';
	}
	else{
		$straw=parse_ini_file('../../../database/straw.ini',true);
		$strawarray=array();
		foreach($straw['straw'] as $k=>$s){
			if($k=='999'){
				continue;
			}
			else{
				$strawarray[$k]['name']=$s;
				$strawarray[$k]['number']=0;
			}
		}
	}

	$document1='';
	$PHPWord = new PHPWord();
	if(isset($print['item']['clienttype'])&&file_exists('../../../template/clientlist'.$print['item']['clienttype'].'.docx')){
		$document1 = $PHPWord->loadTemplate('../../../template/clientlist'.$print['item']['clienttype'].'.docx');
	}
	else{
		$document1 = $PHPWord->loadTemplate('../../../template/clientlist.docx');
	}

	$tablename='';
	if(isset($content['init']['controltable'])&&$content['init']['controltable']=='1'){//2020/3/23 開啟桌控
		if(file_exists('../../../database/floorspend.ini')){
			$tablemap=parse_ini_file('../../../database/floorspend.ini',true);
		}
		else{
		}
		if(preg_match('/,/',$sale[0]['TABLENUMBER'])){//併桌
			$splittable=preg_split('/,/',$sale[0]['TABLENUMBER']);
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
			if(preg_match('/-/',$sale[0]['TABLENUMBER'])){//拆桌
				$inittable=preg_split('/-/',$sale[0]['TABLENUMBER']);
				if(isset($tablemap['Tname'][$inittable[0]])){
					$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
				}
				else{
					$tablename .= $sale[0]['TABLENUMBER'];
				}
			}
			else{
				if(isset($tablemap['Tname'][$sale[0]['TABLENUMBER']])){
					$tablename .= $tablemap['Tname'][$sale[0]['TABLENUMBER']];
				}
				else{
					$tablename .= $sale[0]['TABLENUMBER'];
				}
			}
		}
	}
	else{
		$tablename=$sale[0]['TABLENUMBER'];
	}
	if($list[0]['REMARKS']=='1'){
		if($sale[0]['TABLENUMBER']==''){
			if($ininame!='-1'){
				$document1->setValue('type',"(".$ininame['name']['reprint'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"(補)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
			}
		}
		else{
			////echo $buttons['name']['listtype1'];
			if($ininame!='-1'){
				$document1->setValue('type',"(".$ininame['name']['reprint'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table']);
			}
			else{
				$document1->setValue('type',"(補)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌");
			}
		}
	}
	else if($list[0]['REMARKS']=='2'){
		if($ininame!='-1'){
			$document1->setValue('type',"(".$ininame['name']['reprint'].")".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
		}
		else{
			$document1->setValue('type',"(補)".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
		}
	}
	else if($list[0]['REMARKS']=='3'){
		if($ininame!='-1'){
			$document1->setValue('type',"(".$ininame['name']['reprint'].")".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
		}
		else{
			$document1->setValue('type',"(補)".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
		}
	}
	else if($list[0]['REMARKS']=='4'){
		if($ininame!='-1'){
			$document1->setValue('type',"(".$ininame['name']['reprint'].")".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
		}
		else{
			$document1->setValue('type',"(補)".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
		}
	}
	else{
		if($sale[0]['TABLENUMBER']==''){
			if($ininame!='-1'){
				$document1->setValue('type',"(".$ininame['name']['reprint'].") ".$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"(補) ".$saleno[0]['saleno']);
			}
		}
		else{
			if($ininame!='-1'){
				$document1->setValue('type',"(".$ininame['name']['reprint'].") ".$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table']);
			}
			else{
				$document1->setValue('type',"(補) ".$saleno[0]['saleno']."\r\n".$tablename.'號桌');
			}
		}
	}
	if($ininame!='-1'&&isset($ininame['name']['bizdatelabel'])){
		$document1->setValue('bizdate',$ininame['name']['bizdatelabel'].':'.substr($sale[0]['BIZDATE'],0,4).'/'.substr($sale[0]['BIZDATE'],4,2).'/'.substr($sale[0]['BIZDATE'],6,2));
		//$document1->setValue('bizdate',date('Y/m/d H:i:s'));
	}
	else{
		$document1->setValue('bizdate','營業日:'.substr($sale[0]['BIZDATE'],0,4).'/'.substr($sale[0]['BIZDATE'],4,2).'/'.substr($sale[0]['BIZDATE'],6,2));
	}
	
	if($ininame!='-1'&&isset($ininame['name']['datetimelabel'])){
		$document1->setValue('datetime',$ininame['name']['datetimelabel'].':'.substr($sale[0]['CREATEDATETIME'],8,2).':'.substr($sale[0]['CREATEDATETIME'],10,2).':'.substr($sale[0]['CREATEDATETIME'],12,2));
	}
	else{
		$document1->setValue('datetime','開單時間:'.substr($sale[0]['CREATEDATETIME'],8,2).':'.substr($sale[0]['CREATEDATETIME'],10,2).':'.substr($sale[0]['CREATEDATETIME'],12,2));
	}
	
	date_default_timezone_set($content['init']['settime']);
	if($ininame!='-1'&&isset($ininame['name']['saletimelabel'])){
		if($sale[0]['UPDATEDATETIME']!=''){
			$document1->setValue('saletime',$ininame['name']['saletimelabel'].':'.substr($sale[0]['UPDATEDATETIME'],8,2).':'.substr($sale[0]['UPDATEDATETIME'],10,2).':'.substr($sale[0]['UPDATEDATETIME'],12,2)."\r\n".date('Y/m/d H:i:s'));
		}
		else{
			$document1->setValue('saletime',$ininame['name']['saletimelabel'].":\r\n".date('Y/m/d H:i:s'));
		}
	}
	else{
		if($sale[0]['UPDATEDATETIME']!=''){
			$document1->setValue('saletime','結帳(加點)時間:'.substr($sale[0]['UPDATEDATETIME'],8,2).':'.substr($sale[0]['UPDATEDATETIME'],10,2).':'.substr($sale[0]['UPDATEDATETIME'],12,2)."\r\n".date('Y/m/d H:i:s'));
		}
		else{
			$document1->setValue('saletime',"結帳(加點)時間:\r\n".date('Y/m/d H:i:s'));
		}
	}
	
	$persontext="";
	$context="";
	if(isset($print['clientlist']['numberman'])&&$print['clientlist']['numberman']=='1'){
		if(file_exists('../../../database/floorspend.ini')){
			$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
			if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($sale[0]['TAX6']!=0||$sale[0]['TAX7']!=0||$sale[0]['TAX8']!=0)){
				if($floorspend['person1']['name']!=''&&$sale[0]['TAX6']!=0){
					if($persontext!=""){
						$persontext=$persontext.',';
					}
					else{
					}
					$persontext=$persontext.$floorspend['person1']['name'].":".$sale[0]['TAX6'];
				}
				else{
				}
				if($floorspend['person2']['name']!=''&&$sale[0]['TAX7']!=0){
					if($persontext!=""){
						$persontext=$persontext.',';
					}
					else{
					}
					$persontext=$persontext.$floorspend['person2']['name'].":".$sale[0]['TAX7'];
				}
				else{
				}
				if($floorspend['person3']['name']!=''&&$sale[0]['TAX8']!=0){
					if($persontext!=""){
						$persontext=$persontext.',';
					}
					else{
					}
					$persontext=$persontext.$floorspend['person3']['name'].":".$sale[0]['TAX8'];
				}
				else{
				}
			}
			else{
			}
		}
		else{
		}
	}
	else{
	}

	if(isset($print['clientlist']['orderman'])&&$print['clientlist']['orderman']=='1'){
		if($ininame!='-1'){
			$context=$ininame['name']['orderman'].":".$sale[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].":".$_POST['username']."(".$_POST['machinetype'].")";
		}
		else{
			$context="點餐人員:".$sale[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."(".$_POST['machinetype'].")";
		}
	}
	else if(!isset($print['clientlist']['orderman'])){
		if($ininame!='-1'){
			$context=$ininame['name']['orderman'].":".$sale[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].":".$_POST['username']."(".$_POST['machinetype'].")";
		}
		else{
			$context="點餐人員:".$sale[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."(".$_POST['machinetype'].")";
		}
	}
	else{
	}

	if($persontext!=""&&$context!=""){
		$document1->setValue('consecnumber',$no.$persontext."\r\n".$context);
	}
	else if($persontext!=""){
		$document1->setValue('consecnumber',$no."\r\n".$persontext);
	}
	else if($context!=""){
		$document1->setValue('consecnumber',$no."\r\n".$context);
	}
	else{
		$document1->setValue('consecnumber',$no);
	}

	/*if($ininame!='-1'){
		$document1->setValue('consecnumber',$no."\r\n".$ininame['name']['orderman'].":".$sale[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].":".$_POST['username']."(".$_POST['machinetype'].")");
	}
	else{
		$document1->setValue('consecnumber',$no."\r\n點餐人員:".$sale[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."(".$_POST['machinetype'].")");
	}*/

	$document1->setValue('story',$data['basic']['storyname']);
	$tindex=0;
	$temporderlist=1;
	$table='';
	
	//2020/9/18 發票資訊
	if(isset($sale[0])&&isset($sale[0]['INVOICENUMBER'])&&$sale[0]['INVOICENUMBER']!=''){
		//考慮是否放入載具、統編資訊
		$table .= '<w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p><w:p w:rsidR="008C4EB0" w:rsidRPr="001E039E" w:rsidRDefault="00FC09EC" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['clientlist']['invsize'].'"/><w:szCs w:val="'.$print['clientlist']['invsize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['clientlist']['invsize'].'"/><w:szCs w:val="'.$print['clientlist']['invsize'].'"/></w:rPr><w:t>';
		if($sale!='-1'&&isset($sale['name']['invoicenumber'])){
			$table.=$sale['name']['invoicenumber'].":";
		}
		else{
			$table .= "發票號碼:";
		}
		$table .= $sale[0]['INVOICENUMBER'].'</w:t></w:r></w:p>';
	}
	else{
	}

	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="1250"/><w:gridCol w:w="1250"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
	$totalqty=0;
	$totalamt=0;
	$tempindex=0;
	$subitemnumber=0;
	$subindex=-1;
	for($i=0;$i<sizeof($list);$i=$i+2){
		if($list[$i]['ITEMCODE']=='autodis'||$list[$i]['ITEMCODE']=='list'||$list[$i]['ITEMCODE']=='member'){
		}
		else{
			$straw=999;
			//判斷吸管屬性權重
			if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&isset($menu[intval($list[$i]['ITEMCODE'])]['straw'])&&$menu[intval($list[$i]['ITEMCODE'])]['straw']!=''&&$menu[intval($list[$i]['ITEMCODE'])]['straw']!=null&&intval($menu[intval($list[$i]['ITEMCODE'])]['straw'])<intval($straw)){
				$straw=intval($menu[intval($list[$i]['ITEMCODE'])]['straw']);
			}
			else{
			}
			$totalamt=intval($totalamt)+intval($list[$i]['AMT']);
			$totalqty=intval($totalqty)+intval($list[$i]['QTY']);
			if($igs[intval($list[$i]['ITEMCODE'])]>0){
				$subitemnumber=$igs[intval($list[$i]['ITEMCODE'])];
				$subindex=0;
			}
			else{
			}
			if(isset($pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['clientlist'.$list[$i]['REMARKS']])&&$pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['clientlist'.$list[$i]['REMARKS']]=='0'){
				$tempindex++;
				if($igs[intval($list[$i]['ITEMCODE'])]>0){
					$subindex++;
				}
				else{
					if(intval($subindex)>0&&intval($subindex)<=intval($subitemnumber)){
						$subindex++;
					}
					else{
					}
				}
			}
			else{
				$temporderlist=0;
			
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&($list[$i]['SELECTIVEITEM1']==NULL||$list[$i]['SELECTIVEITEM1']==''||strlen($list[$i]['SELECTIVEITEM1'])==0)&&$list[$i+1]['AMT']==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
				if($igs[intval($list[$i]['ITEMCODE'])]>0){
					if(strlen($list[$i]['UNITPRICELINK'])==0){
						$table .= $list[$i]['ITEMNAME'].'x'.$list[$i]['QTY'];
					}
					else{
						$table .= $list[$i]['ITEMNAME'].'('.$list[$i]['UNITPRICELINK'].')x'.$list[$i]['QTY'];
					}
					if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$menu[intval($list[$i]['ITEMCODE'])]['name2']!=''){
						if(strlen($list[$i]['UNITPRICELINK'])==0){
							$table .= "\r\n".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'x'.$list[$i]['QTY'];
						}
						else{
							for($mname=1;$mname<=$menu[intval($list[$i]['ITEMCODE'])]['mnumber'];$mname++){
								if($menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'1']==$list[$i]['UNITPRICELINK']){
									$table .= "\r\n".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'('.$menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'2'].')x'.$list[$i]['QTY'];
									break;
								}
								else{
								}
							}
						}
					}
					else{
					}
					$subindex++;
				}
				else{
					if(intval($subindex)>0&&intval($subindex)<=intval($subitemnumber)){
						if(strlen($list[$i]['UNITPRICELINK'])==0){
							$table .= '－'.$list[$i]['ITEMNAME'].'x'.$list[$i]['QTY'];
						}
						else{
							$table .= '－'.$list[$i]['ITEMNAME'].'('.$list[$i]['UNITPRICELINK'].')x'.$list[$i]['QTY'];
						}
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$menu[intval($list[$i]['ITEMCODE'])]['name2']!=''){
							if(strlen($list[$i]['UNITPRICELINK'])==0){
								$table .= "\r\n－".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'x'.$list[$i]['QTY'];
							}
							else{
								for($mname=1;$mname<=$menu[intval($list[$i]['ITEMCODE'])]['mnumber'];$mname++){
									if($menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'1']==$list[$i]['UNITPRICELINK']){
										$table .= "\r\n－".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'('.$menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'2'].')x'.$list[$i]['QTY'];
										break;
									}
									else{
									}
								}
							}
						}
						else{
						}
						$subindex++;
					}
					else{
						if(strlen($list[$i]['UNITPRICELINK'])==0){
							$table .= $list[$i]['ITEMNAME'].'x'.$list[$i]['QTY'];
						}
						else{
							$table .= $list[$i]['ITEMNAME'].'('.$list[$i]['UNITPRICELINK'].')x'.$list[$i]['QTY'];
						}
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$menu[intval($list[$i]['ITEMCODE'])]['name2']!=''){
							if(strlen($list[$i]['UNITPRICELINK'])==0){
								$table .= "\r\n".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'x'.$list[$i]['QTY'];
							}
							else{
								for($mname=1;$mname<=$menu[intval($list[$i]['ITEMCODE'])]['mnumber'];$mname++){
									if($menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'1']==$list[$i]['UNITPRICELINK']){
										$table .= "\r\n".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'('.$menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'2'].')x'.$list[$i]['QTY'];
										break;
									}
									else{
									}
								}
							}
						}
						else{
						}
						$subitemnumber=0;
						$subindex=-1;
					}
				}
				
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&($list[$i]['SELECTIVEITEM1']==NULL||$list[$i]['SELECTIVEITEM1']==''||strlen($list[$i]['SELECTIVEITEM1'])==0)&&$list[$i+1]['AMT']==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$list[$i]['UNITPRICE'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&($list[$i]['SELECTIVEITEM1']==NULL||$list[$i]['SELECTIVEITEM1']==''||strlen($list[$i]['SELECTIVEITEM1'])==0)&&$list[$i+1]['AMT']==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].($list[$i]['AMT']).$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				
				$linetaste='';
				for($t=1;$t<=10;$t++){
					if($list[$i]['SELECTIVEITEM'.$t]==null){
						break;
					}
					else{
						//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
						$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
						for($st=0;$st<sizeof($temptaste);$st++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								if(preg_match('/99999/',$temptaste[$st])){//手打備註
									if($linetaste==''){
										$linetaste = '　+'.substr($temptaste[$st],7);
									}
									else{
										$linetaste .= ','.substr($temptaste[$st],7);
									}
								}
								else{
									$tasteno=intval(substr($temptaste[$st],0,5));
									$tasteqty=intval(substr($temptaste[$st],5,1));
									//判斷吸管屬性權重
									if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&$tasteno!='99999'&&isset($taste[$tasteno]['straw'])&&$taste[$tasteno]['straw']!=''&&$taste[$tasteno]['straw']!=null&&intval($taste[$tasteno]['straw'])<intval($straw)){
										$straw=intval($taste[$tasteno]['straw']);
									}
									else{
									}
									
									if($linetaste==''){
										$linetaste = '　+'.$taste[$tasteno]['name1'];
									}
									else{
										$linetaste .= ','.$taste[$tasteno]['name1'];
									}

									if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&isset($taste[$tasteno]['name2'])&&$taste[$tasteno]['name2']!=''){
										$linetaste .= '/ '.$taste[$tasteno]['name1'];
									}
									else{
									}
								}
							}
							else if(preg_match('/99999/',$temptaste[$st])){//手打備註
								$tasteno='99999';
								$tasteqty='1';
								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
								$table .= '　+'.substr($temptaste[$st],7);
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";
							}
							else{
								$tasteno=intval(substr($temptaste[$st],0,5));
								$tasteqty=intval(substr($temptaste[$st],5,1));

								//判斷吸管屬性權重
								if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&$tasteno!='99999'&&isset($taste[$tasteno]['straw'])&&$taste[$tasteno]['straw']!=''&&$taste[$tasteno]['straw']!=null&&intval($taste[$tasteno]['straw'])<intval($straw)){
									$straw=intval($taste[$tasteno]['straw']);
								}
								else{
								}

								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
								$table .= '　+'.$taste[$tasteno]['name1'];
								if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$taste[$tasteno]['name2']!=''){
									$table .= ' /'.$taste[$tasteno]['name2'];
								}
								else{
								}
								if($tasteqty==1){
								}
								else{
									$table .= '*'.$tasteqty;
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								if(intval($taste[$tasteno]['money'])==0){
								}
								else{
									$table .= $taste[$tasteno]['money'];
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								if(intval($taste[$tasteno]['money'])*intval($tasteqty)==0){
								}
								else{
									$table .= $content['init']['frontunit'].(intval($taste[$tasteno]['money'])*intval($tasteqty)).$content['init']['unit'];
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";
							}
						}
					}
					/*else{
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
							if(preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
								if($linetaste==''){
									$linetaste = '　+'.substr($list[$i]['SELECTIVEITEM'.$t],7);
								}
								else{
									$linetaste .= ','.substr($list[$i]['SELECTIVEITEM'.$t],7);
								}
							}
							else{
								$tasteno=intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5));
								$tasteqty=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
								//判斷吸管屬性權重
								if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&$tasteno!='99999'&&isset($taste[$tasteno]['straw'])&&$taste[$tasteno]['straw']!=''&&$taste[$tasteno]['straw']!=null&&intval($taste[$tasteno]['straw'])<intval($straw)){
									$straw=intval($taste[$tasteno]['straw']);
								}
								else{
								}
								
								if($linetaste==''){
									$linetaste = '　+'.$taste[$tasteno]['name1'];
								}
								else{
									$linetaste .= ','.$taste[$tasteno]['name1'];
								}

								if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&isset($taste[$tasteno]['name2'])&&$taste[$tasteno]['name2']!=''){
									$linetaste .= '/ '.$taste[$tasteno]['name1'];
								}
								else{
								}
							}
						}
						else if(preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
							$tasteno='99999';
							$tasteqty='1';
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							$table .= '　+'.substr($list[$i]['SELECTIVEITEM'.$t],7);
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
						else{
							$tasteno=intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5));
							$tasteqty=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));

							//判斷吸管屬性權重
							if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&$tasteno!='99999'&&isset($taste[$tasteno]['straw'])&&$taste[$tasteno]['straw']!=''&&$taste[$tasteno]['straw']!=null&&intval($taste[$tasteno]['straw'])<intval($straw)){
								$straw=intval($taste[$tasteno]['straw']);
							}
							else{
							}

							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							$table .= '　+'.$taste[$tasteno]['name1'];
							if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$taste[$tasteno]['name2']!=''){
								$table .= ' /'.$taste[$tasteno]['name2'];
							}
							else{
							}
							if($tasteqty==1){
							}
							else{
								$table .= '*'.$tasteqty;
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							if(intval($taste[$tasteno]['money'])==0){
							}
							else{
								$table .= $taste[$tasteno]['money'];
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							if(intval($taste[$tasteno]['money'])*intval($tasteqty)==0){
							}
							else{
								$table .= $content['init']['frontunit'].(intval($taste[$tasteno]['money'])*intval($tasteqty)).$content['init']['unit'];
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
					}*/
				}
				if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/>';
					//2020/4/29 演算法暫時沒有想法，先註解
					/*if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0)||$_POST['discount'][$i]==0)){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					else if($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}*/
					$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					
					$table .= $linetaste;
					
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
					//備註一項一行
				}

				if($list[$i+1]['AMT']!=0){
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
					if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($list)-2)){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					else{
					}
					$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					if($ininame!='-1'){
						$table .= '　+'.$ininame['name']['itemdis'];
					}
					else{
						$table .= '　+優惠折抵';
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
					if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($list)-2)){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					else{
					}
					$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
					if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($list)-2)){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					else{
					}
					$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= $content['init']['frontunit'].$list[$i+1]['AMT'].$content['init']['unit'];

					$totalamt=intval($totalamt)+intval($list[$i+1]['AMT']);

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
				}
			}
			//計算吸管數量
			if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&intval($straw)!=999){
				$strawarray[intval($straw)]['number']=intval($strawarray[intval($straw)]['number'])+1*$list[$i]['QTY'];
			}
			else{
			}
		}
	}
	if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'){
		//print_r($strawarray);
		foreach($strawarray as $st){
			if(intval($st['number'])>0){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $st['name'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $st['number'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
				continue;
			}
		}
	}
	else{
	}
	if(sizeof($list)>0){
		$ITEMCODE=array_column($list,'ITEMCODE');
		if(in_array('list',$ITEMCODE)){
			$totalamt=intval($totalamt)+intval($list[array_search('list',$ITEMCODE)]['AMT']);
		}
		else{
		}
		if(in_array('autodis',$ITEMCODE)){
			$totalamt=intval($totalamt)+intval($list[array_search('autodis',$ITEMCODE)]['AMT']);
		}
		else{
		}
		if(in_array('member',$ITEMCODE)){
			$totalamt=intval($totalamt)+intval($list[array_search('member',$ITEMCODE)]['AMT']);
		}
		else{
		}
	}
	else{
	}
	$table .= '</w:tbl>';
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
	if($ininame!='-1'){
		$table .= $ininame['name']['qty'];
	}
	else{
		$table .= '商品數量';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
	$table .= $totalqty;
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	if(isset($dis[0]['dis'])){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$table .= $ininame['name']['listdis'];
		}
		else{
			$table .= '優惠折抵';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$dis[0]['dis'].$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	if(isset($autodis[0]['AMT'])){
		$autodiscontent=preg_split('/,/',$autodis[0]['ITEMGRPCODE']);
		$autodispremoney=preg_split('/,/',$autodis[0]['ITEMGRPNAME']);
		////echo 'auto='.sizeof($autodiscontent);
		for($di=0;$di<sizeof($autodiscontent);$di++){
			if(isset($discount[$autodiscontent[$di]])){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$table .= $discount[$autodiscontent[$di]]['name'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].'-'.$autodispremoney[$di].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
			}
		}
	}
	else{
	}
	if(isset($sale[0]['TAX1'])&&$sale[0]['TAX1']>0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$table .= $ininame['name']['charge'];
		}
		else{
			$table .= '服務費';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$sale[0]['TAX1'].$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	if(isset($sale[0]['SALESTTLAMT'])&&intval($sale[0]['SALESTTLAMT'])!=intval($totalamt)){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$table .= $ininame['name']['floorspan'];
		}
		else{
			$table .= '低銷差價';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].(intval($sale[0]['SALESTTLAMT'])-intval($totalamt)).$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	$checktime=0;
	$temptable='';
	if((!isset($cover)||!isset($cover[0]['bizdate']))&&isset($sale[0]['TAX2'])&&floatval($sale[0]['TAX2'])>0){//無修改
		$checktime++;
		$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$temptable .= $ininame['name']['cashmoney'];
		}
		else{
			$temptable .= '現金';
		}
		$temptable .= "</w:t></w:r></w:p></w:tc>";
		$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$temptable .= $content['init']['frontunit'].$sale[0]['TAX2'].$content['init']['unit'];
		$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	else if(isset($cover[0]['tax2'])&&floatval($cover[0]['tax2'])>0){
		$checktime++;
		$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$temptable .= $ininame['name']['cashmoney'];
		}
		else{
			$temptable .= '現金';
		}
		$temptable .= "</w:t></w:r></w:p></w:tc>";
		$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$temptable .= $content['init']['frontunit'].$cover[0]['tax2'].$content['init']['unit'];
		$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	else{
	}
	if((!isset($cover)||!isset($cover[0]['bizdate']))&&isset($sale[0]['TAX3'])&&isset($sale[0]['TAX9'])&&(floatval($sale[0]['TAX3'])>0||floatval($sale[0]['TAX9'])>0)){//無修改
		$checktime++;
		$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$temptable .= $ininame['name']['cash'];
		}
		else{
			$temptable .= '信用卡';
		}
		$temptable .= "</w:t></w:r></w:p></w:tc>";
		$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$temptable .= $content['init']['frontunit'].(floatval($sale[0]['TAX3'])+floatval($sale[0]['TAX9'])).$content['init']['unit'];
		$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	else if(isset($cover[0]['tax3'])&&isset($cover[0]['tax9'])&&(floatval($cover[0]['tax3'])>0||floatval($cover[0]['tax9'])>0)){
		$checktime++;
		$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$temptable .= $ininame['name']['cash'];
		}
		else{
			$temptable .= '信用卡';
		}
		$temptable .= "</w:t></w:r></w:p></w:tc>";
		$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$temptable .= $content['init']['frontunit'].(floatval($cover[0]['tax3'])+floatval($cover[0]['tax9'])).$content['init']['unit'];
		$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	else{
	}
	foreach($otherpay as $oi=>$oa){
		if(!isset($cover)||!isset($cover[0]['bizdate'])){//無修改
			if($oi=='pay'){
			}
			else{
				if((!isset($oa['location'])||$oa['location']=='CST011')&&isset($sale[0][$oa['dbname']])){
					$tempsaleoa=preg_split('/=/',$sale[0][$oa['dbname']]);
					if(floatval($tempsaleoa[0])>0){
						$checktime++;
						$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
						$temptable .= $oa['name'];
						$temptable .= "</w:t></w:r></w:p></w:tc>";
						$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
						$temptable .= $tempsaleoa[0];
						$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
					}
					else{
					}
				}
				else if((isset($oa['location'])&&$oa['location']!='CST011')&&isset($sale[0][$oa['location']])){
					$tempsaleoa=preg_split('/=/',$sale[0][$oa['location']]);
					if(floatval($tempsaleoa[0])>0){
						$checktime++;
						$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
						$temptable .= $oa['name'];
						$temptable .= "</w:t></w:r></w:p></w:tc>";
						$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
						$temptable .= $tempsaleoa[0];
						$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
					}
					else{
					}
				}
				else{
				}
			}
		}
		else{
			if($oi=='pay'){
			}
			else{
				if((!isset($oa['location'])||$oa['location']=='CST011')&&isset($cover[0][strtolower($oa['dbname'])])){
					$tempsaleoa=preg_split('/=/',$cover[0][strtolower($oa['dbname'])]);
					if(floatval($tempsaleoa[0])>0){
						$checktime++;
						$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
						$temptable .= $oa['name'];
						$temptable .= "</w:t></w:r></w:p></w:tc>";
						$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
						$temptable .= $tempsaleoa[0];
						$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
					}
					else{
					}
				}
				else if((isset($oa['location'])&&$oa['location']!='CST011')&&isset($cover[0][strtolower($oa['location'])])){
					$tempsaleoa=preg_split('/=/',$cover[0][strtolower($oa['location'])]);
					if(floatval($tempsaleoa[0])>0){
						$checktime++;
						$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
						$temptable .= $oa['name'];
						$temptable .= "</w:t></w:r></w:p></w:tc>";
						$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
						$temptable .= $tempsaleoa[0];
						$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
					}
					else{
					}
				}
				else{
				}
			}
		}
	}
	$table .= $temptable;

	if(isset($sale[0]['RELINVOICENUMBER'])&&$sale[0]['RELINVOICENUMBER']!=''&&$sale[0]['RELINVOICENUMBER']!=NULL){//<w:gridSpan w:val="2"/>
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';

		$table .= $sale[0]['RELINVOICENUMBER'];

		$table .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	else{
	}

	$table .= "</w:tbl>";
	
	$memtable='';
	$memtable .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
	if(isset($sale[0]['CUSTCODE'])&&$sale[0]['CUSTCODE']!=null&&$sale[0]['CUSTCODE']!=''){
		if(preg_match('/;-;/',$sale[0]['CUSTCODE'])){
			$tempmemno=preg_split('/;-;/',$sale[0]['CUSTCODE']);
		}
		else{
			$tempmemno[0]=$sale[0]['CUSTCODE'];
		}
		include_once '../../../tool/dbTool.inc.php';
		if($content['init']['onlinemember']=='1'){//網路會員
			$PostData = array(
				"type"=>"online",
				"ajax" => "",
				"company" => $data['basic']['company'],
				"memno" => $tempmemno[0]
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php');//
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			// Edit: prior variable $postFields should be $postfields;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
			$memdata = curl_exec($ch);
			$mem=json_decode($memdata,1);
			if(curl_errno($ch) !== 0) {
				//print_r('cURL error when connecting to http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php : ' . curl_error($ch));
			}
			curl_close($ch);
		}
		else{
			$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
			$sql='SELECT * FROM person WHERE memno="'.$tempmemno[0].'"';
			$mem=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
		}
		if(isset($mem[0]['name'])){
			if((!isset($print['clientlist']['memberdata'])||$print['clientlist']['memberdata']=='1')){
				if(isset($content['init']['writememdata'.$list[0]['REMARKS']])&&$content['init']['writememdata'.$list[0]['REMARKS']]==1){
					$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					if(isset($mem[0]['setting'])&&$mem[0]['setting']!=null&&strlen($mem[0]['setting'])>0){
						$memtable .= $mem[0]['name']."(".$mem[0]['setting'].")";
						//$document1->setValue('memname',$mem[0]['name']."(".$mem[0]['setting'].")");
					}
					else{
						$memtable .= $mem[0]['name'];
						//$document1->setValue('memname',$mem[0]['name']);
					}
					$memtable .= "</w:t></w:r></w:p></w:tc>";
					$memtable .= "</w:tr>";
					$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					$memtable .= $mem[0]['tel'];
					//$document1->setValue('memtel',$memdata[0]['tel']);
					$memtable .= "</w:t></w:r></w:p></w:tc>";
					$memtable .= "</w:tr>";
					$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					if(preg_match('/;*;/',$mem[0]['address'])){
						$addselect=preg_split('/\;\*\;/',$mem[0]['address']);
						if(isset($tempmemno[1])&&isset($addselect[$tempmemno[1]-1])){
							$memtable .= $addselect[($tempmemno[1]-1)];
						}
						else{
							$memtable .= $addselect[0];
						}
					}
					else{
						$memtable .= $mem[0]['address'];
					}
					//$memtable .= $mem[0]['address'];
					//$document1->setValue('memaddress',$memdata[0]['address']);
					$memtable .= "</w:t></w:r></w:p></w:tc>";
					$memtable .= "</w:tr>";
					$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/>';
					if(!isset($print['clientlist']['mempointmoney'])||$print['clientlist']['mempointmoney']=='1'){
					}
					else{
						$memtable .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					$memtable .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					$memtable .= $mem[0]['remark'];
					//$document1->setValue('memremarks',"備註:".$memdata[0]['remark']);
					$memtable .= "</w:t></w:r></w:p></w:tc>";
					$memtable .= "</w:tr>";
				}
				else{
				}
			}
			else{
			}
		}
		else{
			/*$document1->setValue('memname','');
			$document1->setValue('memtel',"");
			$document1->setValue('memaddress','');
			$document1->setValue('memremarks','');*/
		}
	}
	else{
		/*$document1->setValue('memname','');
		$document1->setValue('memtel',"");
		$document1->setValue('memaddress','');
		$document1->setValue('memremarks','');*/
	}
	$memtable .= "</w:tbl>";
	$document1->setValue('memtable',$memtable);

	$document1->setValue('item',$table);
	//date_default_timezone_set('Asia/Taipei');
	//$datetime=date('YmdHis');

	date_default_timezone_set($content['init']['settime']);
	$filename=date('YmdHis');
	$imgarray='empty';

	/*create barcode*/
	include('../../../tool/phpbarcode/src/BarcodeGenerator.php');
	include('../../../tool/phpbarcode/src/BarcodeGeneratorPNG.php');

	$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
	//echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode(($_POST['bizdate'].$consecnumber), $generator::TYPE_CODE_128)) . '">';
	file_put_contents('../../../print/barcode/'.$_POST['machinetype'].'.png', $generator->getBarcode(($sale[0]['BIZDATE'].$no), $generator::TYPE_CODE_128));
	/*create barcode*/

	$imgarray=array('barcode'=>'../../../print/barcode/'.$_POST['machinetype'].'.png');

	/*if(isset($_POST['receipt'])&&$_POST['receipt']=='1'){//收據章//2020/1/31後續在設定檔加入補印產生收據章參數
		if(file_exists('../../../print/receipt.png')){
			$imgarray['receipt']='../../../print/receipt.png';
		}
		else{
		}
	}
	else{
	}*/

	/*if(isset($print['clientlist']['tcqrcode'])&&$print['clientlist']['tcqrcode']=='1'&&isset($data['tcqrcode']['dep'])&&isset($data['tcqrcode']['dep'])){//串接騰雲商場POSQRcode//2020/1/31於補印的狀況會缺少一些關鍵參數，後續調整
		include_once '../../../tool/phpqrcode/qrlib.php';
		if(file_exists('../../../print/tcqrcode')){
		}
		else{
			mkdir('../../../print/tcqrcode');
		}

		$qrcodestring='1'.str_pad($data['tcqrcode']['dep'],'10','0',STR_PAD_LEFT).str_pad($data['tcqrcode']['depclass'],'3','0',STR_PAD_LEFT);
		if(isset($_POST['listtotal'])){
			$qrcodestring .= str_pad($_POST['should'],'8','0',STR_PAD_LEFT).str_pad($_POST['should'],'8','0',STR_PAD_LEFT);
		}
		else{
			if(isset($_POST['floorspan'])){
				if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
					$qrcodestring.=str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']),'8','0',STR_PAD_LEFT).str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']),'8','0',STR_PAD_LEFT);
				}
				else{
					$qrcodestring.=str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']),'8','0',STR_PAD_LEFT).str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']),'8','0',STR_PAD_LEFT);
				}
			}
			else{
				if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
					$qrcodestring.=str_pad(($_POST['total']+$_POST['charge']+$autodis[0]['AMT']),'8','0',STR_PAD_LEFT).str_pad(($_POST['total']+$_POST['charge']+$autodis[0]['AMT']),'8','0',STR_PAD_LEFT);
				}
				else{
					$qrcodestring.=str_pad(($_POST['total']+$_POST['charge']),'8','0',STR_PAD_LEFT).str_pad(($_POST['total']+$_POST['charge']),'8','0',STR_PAD_LEFT);
				}
			}
		}
		$tempqrcodestring=str_split($qrcodestring);//分割字元
		$onegroup=0;
		$twogroup=0;
		for($tcindex=1;$tcindex<=sizeof($tempqrcodestring);$tcindex++){
			if($tcindex%2==1){//奇數位
				$onegroup=intval($onegroup)+intval($tempqrcodestring[$tcindex-1]);
			}
			else{//偶數位
				$twogroup=intval($twogroup)+intval($tempqrcodestring[$tcindex-1]);
			}
		}
		$twogroup=$twogroup*3;
		$subtotalgroup=intval($onegroup)+intval($twogroup);//奇數位+偶數位*3
		if($subtotalgroup%10==0){
			$checknumber=0;//$subtotalgroup的個位數為0；則檢查碼為0
		}
		else{
			$checknumber=10-($subtotalgroup%10);//10-$subtotalgroup的個位數；即是檢查碼
		}
		$qrcodestring .= $checknumber.';';
		QRcode::png('A+'.$qrcodestring.str_pad($consecnumber,'18','0',STR_PAD_LEFT),'../../../print/tcqrcode/'.$consecnumber.'.png','H',6);
		$imgarray['qrcode']='../../../print/tcqrcode/'.$consecnumber.'.png';
	}
	else{
	}*/

	$document1->replaceStrToMyImg('qrcode',$imgarray);
	//$document1->replaceStrToQrcode('qrcode','empty');//2020/1/31產生快速結帳的barcode

	//$document1->save("../../../print/noread/".$filename."_".$_POST['machinetype']."clientlist_".intval($no).".docx");
	$document1->save("../../../print/read/".intval($no)."_clientlist".$list[0]['REMARKS'].$_POST['machinetype']."_".$filename.".docx");
	if(is_numeric($list[0]['REMARKS'])){
		if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
			$prt=fopen("../../../print/noread/".intval($no)."_clientlist".$list[0]['REMARKS'].$_POST['machinetype']."_".$filename.".".$_POST['machinetype'],'w');
		}
		else{
			$prt=fopen("../../../print/noread/".intval($no)."_clientlist".$list[0]['REMARKS'].$_POST['machinetype']."_".$filename.".prt",'w');
		}
	}
	else{
		if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
			$prt=fopen("../../../print/noread/".intval($no)."_clientlist".$list[0]['REMARKS'].$_POST['machinetype']."_".$filename.".".$_POST['machinetype'],'w');
		}
		else{
			$prt=fopen("../../../print/noread/".intval($no)."_clientlist".$list[0]['REMARKS'].$_POST['machinetype']."_".$filename.".prt",'w');
		}
	}
	fclose($prt);
}
else{
}
if($type=='tempall'||$type=='templist'){
	////echo $no;
	include_once '../../../tool/dbTool.inc.php';
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'" ORDER BY LINENUMBER ASC';
	$list=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'"';
	$sale=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT saleno FROM salemap WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$no.'"';
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
	require_once '../../../tool/PHPWord.php';
	$content=parse_ini_file('../../../database/initsetting.ini',true);
	//date_default_timezone_set('Asia/Taipei');
	//date_default_timezone_set($content['init']['settime']);

	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
	$data=parse_ini_file('../../../database/setup.ini',true);
	$print=parse_ini_file('../../../database/printlisttag.ini',true);
	if(isset($print['item']['clititle'])&&$print['item']['clititle']!=''){
		$clititle=$print['item']['clititle'];
	}
	else{
		$clititle=32;
	}
	if(isset($print['item']['clicont'])&&$print['item']['clicont']!=''){
		$clicont=$print['item']['clicont'];
	}
	else{
		$clicont=32;
	}
	if(isset($print['clientlist']['invsize'])){
	}
	else{
		$print['clientlist']['invsize']="20";
	}
	
	$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
	$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
	$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
	$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
	if(file_exists('../../syspram/clientlist-'.$content['init']['firlan'].'.ini')){
		$ininame=parse_ini_file('../../syspram/clientlist-'.$content['init']['firlan'].'.ini',true);
	}
	else{
		$ininame='-1';
	}

	if(!file_exists('../../../database/straw.ini')||$content['init']['comstraw']=='0'){
		$strawarray='-1';
	}
	else{
		$straw=parse_ini_file('../../../database/straw.ini',true);
		$strawarray=array();
		foreach($straw['straw'] as $k=>$s){
			if($k=='999'){
				continue;
			}
			else{
				$strawarray[$k]['name']=$s;
				$strawarray[$k]['number']=0;
			}
		}
	}

	$document1='';
	$PHPWord = new PHPWord();
	if(isset($print['item']['clienttype'])&&file_exists('../../../template/clientlist'.$print['item']['clienttype'].'.docx')){
		$document1 = $PHPWord->loadTemplate('../../../template/clientlist'.$print['item']['clienttype'].'.docx');
	}
	else{
		$document1 = $PHPWord->loadTemplate('../../../template/clientlist.docx');
	}
	if(strlen($_POST['listtype'])==1){//POS點單
		$listtype=$_POST['listtype'];
	}
	else{//網路預約單
		$listtype=substr($_POST['listtype'],0,1);
	}
	if($sale[0]['TABLENUMBER']==''){
	}
	else{
		$tablename='';
		if(isset($content['init']['controltable'])&&$content['init']['controltable']=='1'){//2020/3/23 開啟桌控
			if(file_exists('../../../database/floorspend.ini')){
				$tablemap=parse_ini_file('../../../database/floorspend.ini',true);
			}
			else{
			}
			if(preg_match('/,/',$sale[0]['TABLENUMBER'])){//併桌
				$splittable=preg_split('/,/',$sale[0]['TABLENUMBER']);
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
				if(preg_match('/-/',$sale[0]['TABLENUMBER'])){//拆桌
					$inittable=preg_split('/-/',$sale[0]['TABLENUMBER']);
					if(isset($tablemap['Tname'][$inittable[0]])){
						$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
					}
					else{
						$tablename .= $sale[0]['TABLENUMBER'];
					}
				}
				else{
					if(isset($tablemap['Tname'][$sale[0]['TABLENUMBER']])){
						$tablename .= $tablemap['Tname'][$sale[0]['TABLENUMBER']];
					}
					else{
						$tablename .= $sale[0]['TABLENUMBER'];
					}
				}
			}
		}
		else{
			$tablename=$sale[0]['TABLENUMBER'];
		}
	}
	if(substr($list[0]['REMARKS'],0,1)=='1'){
		if($sale[0]['TABLENUMBER']==''){
			if(sizeof($sale)>0&&isset($sale[0]['REMARKS'])&&preg_match('/-/',$sale[0]['REMARKS'])){
				$temp=preg_split('/;/',substr($sale[0]['REMARKS'],2));
				if($ininame!='-1'){
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
				}
				else{
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (補暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
				}
			}
			else{
				if($ininame!='-1'){
					$document1->setValue('type',"(".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
				}
				else{
					$document1->setValue('type',"(補暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
				}
			}
		}
		else{
			if(sizeof($sale)>0&&isset($sale[0]['REMARKS'])&&preg_match('/-/',$sale[0]['REMARKS'])){
				$temp=preg_split('/;/',substr($sale[0]['REMARKS'],2));
				if($ininame!='-1'){
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table']);
				}
				else{
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (補暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌");
				}
			}
			else{
				////echo $buttons['name']['listtype1'];
				if($ininame!='-1'){
					$document1->setValue('type',"(".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table']);
				}
				else{
					$document1->setValue('type',"(補暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$tablename."號桌");
				}
			}
		}
	}
	else if(substr($list[0]['REMARKS'],0,1)=='2'){
		if(sizeof($sale)>0&&isset($sale[0]['REMARKS'])&&preg_match('/-/',$sale[0]['REMARKS'])){
			$temp=preg_split('/;/',substr($sale[0]['REMARKS'],2));
			if($ininame!='-1'){
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (補暫)".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
		}
		else{
			if($ininame!='-1'){
				$document1->setValue('type',"(".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"(補暫)".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
		}
	}
	else if(substr($list[0]['REMARKS'],0,1)=='3'){
		if(sizeof($sale)>0&&isset($sale[0]['REMARKS'])&&preg_match('/-/',$sale[0]['REMARKS'])){
			$temp=preg_split('/;/',substr($sale[0]['REMARKS'],2));
			if($ininame!='-1'){
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (補暫)".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
		}
		else{
			if($ininame!='-1'){
				$document1->setValue('type',"(".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"(補暫)".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
		}
	}
	else if(substr($list[0]['REMARKS'],0,1)=='4'){
		if(sizeof($sale)>0&&isset($sale[0]['REMARKS'])&&preg_match('/-/',$sale[0]['REMARKS'])){
			$temp=preg_split('/;/',substr($sale[0]['REMARKS'],2));
			if($ininame!='-1'){
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (補暫)".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
		}
		else{
			if($ininame!='-1'){
				$document1->setValue('type',"(".$ininame['name']['reprinttemp'].")".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"(補暫)".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
		}
	}
	else{
		if($sale[0]['TABLENUMBER']==''){
			if(sizeof($sale)>0&&isset($sale[0]['REMARKS'])&&preg_match('/-/',$sale[0]['REMARKS'])){
				$temp=preg_split('/;/',substr($sale[0]['REMARKS'],2));
				if($ininame!='-1'){
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (".$ininame['name']['reprinttemp'].") ".$saleno[0]['saleno']);
				}
				else{
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (補暫) ".$saleno[0]['saleno']);
				}
			}
			else{
				if($ininame!='-1'){
					$document1->setValue('type',"(".$ininame['name']['reprinttemp'].") ".$saleno[0]['saleno']);
				}
				else{
					$document1->setValue('type',"(補暫) ".$saleno[0]['saleno']);
				}
			}
		}
		else{
			if(sizeof($sale)>0&&isset($sale[0]['REMARKS'])&&preg_match('/-/',$sale[0]['REMARKS'])){
				$temp=preg_split('/;/',substr($sale[0]['REMARKS'],2));
				if($ininame!='-1'){
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (".$ininame['name']['reprinttemp'].") ".$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table']);
				}
				else{
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n (補暫) ".$saleno[0]['saleno']."\r\n".$tablename.'號桌');
				}
			}
			else{
				if($ininame!='-1'){
					$document1->setValue('type',"(".$ininame['name']['reprinttemp'].") ".$saleno[0]['saleno']."\r\n".$tablename.$ininame['name']['table']);
				}
				else{
					$document1->setValue('type',"(補暫) ".$saleno[0]['saleno']."\r\n".$tablename.'號桌');
				}
			}
		}
	}

	if($ininame!='-1'&&isset($ininame['name']['bizdatelabel'])){
		$document1->setValue('bizdate',$ininame['name']['bizdatelabel'].':'.substr($sale[0]['BIZDATE'],0,4).'/'.substr($sale[0]['BIZDATE'],4,2).'/'.substr($sale[0]['BIZDATE'],6,2));
		//$document1->setValue('bizdate',date('Y/m/d H:i:s'));
	}
	else{
		$document1->setValue('bizdate','營業日:'.substr($sale[0]['BIZDATE'],0,4).'/'.substr($sale[0]['BIZDATE'],4,2).'/'.substr($sale[0]['BIZDATE'],6,2));
	}
	
	if($ininame!='-1'&&isset($ininame['name']['datetimelabel'])){
		$document1->setValue('datetime',$ininame['name']['datetimelabel'].':'.substr($sale[0]['CREATEDATETIME'],8,2).':'.substr($sale[0]['CREATEDATETIME'],10,2).':'.substr($sale[0]['CREATEDATETIME'],12,2));
	}
	else{
		$document1->setValue('datetime','開單時間:'.substr($sale[0]['CREATEDATETIME'],8,2).':'.substr($sale[0]['CREATEDATETIME'],10,2).':'.substr($sale[0]['CREATEDATETIME'],12,2));
	}
	
	date_default_timezone_set($content['init']['settime']);
	if($ininame!='-1'&&isset($ininame['name']['saletimelabel'])){
		if($sale[0]['UPDATEDATETIME']!=''){
			$document1->setValue('saletime',$ininame['name']['saletimelabel'].':'.substr($sale[0]['UPDATEDATETIME'],8,2).':'.substr($sale[0]['UPDATEDATETIME'],10,2).':'.substr($sale[0]['UPDATEDATETIME'],12,2)."\r\n".date('Y/m/d H:i:s'));
		}
		else{
			$document1->setValue('saletime',$ininame['name']['saletimelabel'].":\r\n".date('Y/m/d H:i:s'));
		}
	}
	else{
		if($sale[0]['UPDATEDATETIME']!=''){
			$document1->setValue('saletime','結帳(加點)時間:'.substr($sale[0]['UPDATEDATETIME'],8,2).':'.substr($sale[0]['UPDATEDATETIME'],10,2).':'.substr($sale[0]['UPDATEDATETIME'],12,2)."\r\n".date('Y/m/d H:i:s'));
		}
		else{
			$document1->setValue('saletime',"結帳(加點)時間:\r\n".date('Y/m/d H:i:s'));
		}
	}
	
	$persontext="";
	$context="";
	if(isset($print['clientlist']['numberman'])&&$print['clientlist']['numberman']=='1'){
		if(file_exists('../../../database/floorspend.ini')){
			$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
			if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($sale[0]['TAX6']!=0||$sale[0]['TAX7']!=0||$sale[0]['TAX8']!=0)){
				if($floorspend['person1']['name']!=''&&$sale[0]['TAX6']!=0){
					if($persontext!=""){
						$persontext=$persontext.',';
					}
					else{
					}
					$persontext=$persontext.$floorspend['person1']['name'].":".$sale[0]['TAX6'];
				}
				else{
				}
				if($floorspend['person2']['name']!=''&&$sale[0]['TAX7']!=0){
					if($persontext!=""){
						$persontext=$persontext.',';
					}
					else{
					}
					$persontext=$persontext.$floorspend['person2']['name'].":".$sale[0]['TAX7'];
				}
				else{
				}
				if($floorspend['person3']['name']!=''&&$sale[0]['TAX8']!=0){
					if($persontext!=""){
						$persontext=$persontext.',';
					}
					else{
					}
					$persontext=$persontext.$floorspend['person3']['name'].":".$sale[0]['TAX8'];
				}
				else{
				}
			}
			else{
			}
		}
		else{
		}
	}
	else{
	}

	if(isset($print['clientlist']['orderman'])&&$print['clientlist']['orderman']=='1'){
		if($ininame!='-1'){
			$context=$ininame['name']['orderman'].":".$sale[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].":".$_POST['username']."(".$_POST['machinetype'].")";
		}
		else{
			$context="點餐人員:".$sale[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."(".$_POST['machinetype'].")";
		}
	}
	else if(!isset($print['clientlist']['orderman'])){
		if($ininame!='-1'){
			$context=$ininame['name']['orderman'].":".$sale[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].":".$_POST['username']."(".$_POST['machinetype'].")";
		}
		else{
			$context="點餐人員:".$sale[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."(".$_POST['machinetype'].")";
		}
	}
	else{
	}

	if($persontext!=""&&$context!=""){
		$document1->setValue('consecnumber',$no.$persontext."\r\n".$context);
	}
	else if($persontext!=""){
		$document1->setValue('consecnumber',$no."\r\n".$persontext);
	}
	else if($context!=""){
		$document1->setValue('consecnumber',$no."\r\n".$context);
	}
	else{
		$document1->setValue('consecnumber',$no);
	}

	/*if($ininame!='-1'){
		$document1->setValue('consecnumber',$no."\r\n".$ininame['name']['orderman'].":".$sale[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].":".$_POST['username']."(".$_POST['machinetype'].")");
	}
	else{
		$document1->setValue('consecnumber',$no."\r\n點餐人員:".$sale[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."(".$_POST['machinetype'].")");
	}*/

	$document1->setValue('story',$data['basic']['storyname']);
	$tindex=0;
	$temporderlist=1;
	$table='';
	
	//2020/9/18 發票資訊
	if(isset($sale[0])&&isset($sale[0]['INVOICENUMBER'])&&$sale[0]['INVOICENUMBER']!=''){
		//考慮是否放入載具、統編資訊
		$table .= '<w:p w:rsidR="009F34C5" w:rsidRDefault="009F34C5" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr></w:p><w:p w:rsidR="008C4EB0" w:rsidRPr="001E039E" w:rsidRDefault="00FC09EC" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['clientlist']['invsize'].'"/><w:szCs w:val="'.$print['clientlist']['invsize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['clientlist']['invsize'].'"/><w:szCs w:val="'.$print['clientlist']['invsize'].'"/></w:rPr><w:t>';
		if($sale!='-1'&&isset($sale['name']['invoicenumber'])){
			$table.=$sale['name']['invoicenumber'].":";
		}
		else{
			$table .= "發票號碼:";
		}
		$table .= $sale[0]['INVOICENUMBER'].'</w:t></w:r></w:p>';
	}
	else{
	}

	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="1250"/><w:gridCol w:w="1250"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
	$totalqty=0;
	$totalamt=0;
	$tempindex=0;
	$subitemnumber=0;
	$subindex=-1;
	for($i=0;$i<sizeof($list);$i=$i+2){
		if($list[$i]['ITEMCODE']=='autodis'){
		}
		else{
			$straw=999;
			$totalamt=intval($totalamt)+intval($list[$i]['AMT']);
			$totalqty=intval($totalqty)+intval($list[$i]['QTY']);
			//判斷吸管屬性權重
			if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&isset($menu[intval($list[$i]['ITEMCODE'])]['straw'])&&$menu[intval($list[$i]['ITEMCODE'])]['straw']!=''&&$menu[intval($list[$i]['ITEMCODE'])]['straw']!=null&&intval($menu[intval($list[$i]['ITEMCODE'])]['straw'])<intval($straw)){
				$straw=intval($menu[intval($list[$i]['ITEMCODE'])]['straw']);
			}
			else{
			}
			if(isset($igs[intval($list[$i]['ITEMCODE'])])&&$igs[intval($list[$i]['ITEMCODE'])]>0){
				$subitemnumber=$igs[intval($list[$i]['ITEMCODE'])];
				$subindex=0;
			}
			else{
			}
			//print_r($list);
			//print_r($pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]);
			if(isset($pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['clientlist'.substr($list[$i]['REMARKS'],0,1)])&&$pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['clientlist'.substr($list[$i]['REMARKS'],0,1)]=='0'){
				$tempindex++;
				if($igs[intval($list[$i]['ITEMCODE'])]>0){
					$subindex++;
				}
				else{
					if(intval($subindex)>0&&intval($subindex)<=intval($subitemnumber)){
						$subindex++;
					}
					else{
					}
				}
			}
			else{
				$temporderlist=0;
			
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&($list[$i]['SELECTIVEITEM1']==NULL||$list[$i]['SELECTIVEITEM1']==''||strlen($list[$i]['SELECTIVEITEM1'])==0)&&$list[$i+1]['AMT']==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
				if($igs[intval($list[$i]['ITEMCODE'])]>0){
					if(strlen($list[$i]['UNITPRICELINK'])==0){
						$table .= $list[$i]['ITEMNAME'].'x'.$list[$i]['QTY'];
					}
					else{
						$table .= $list[$i]['ITEMNAME'].'('.$list[$i]['UNITPRICELINK'].')x'.$list[$i]['QTY'];
					}
					if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$menu[intval($list[$i]['ITEMCODE'])]['name2']!=''){
						if(strlen($list[$i]['UNITPRICELINK'])==0){
							$table .= "\r\n".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'x'.$list[$i]['QTY'];
						}
						else{
							for($mname=1;$mname<=$menu[intval($list[$i]['ITEMCODE'])]['mnumber'];$mname++){
								if($menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'1']==$list[$i]['UNITPRICELINK']){
									$table .= "\r\n".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'('.$menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'2'].')x'.$list[$i]['QTY'];
									break;
								}
								else{
								}
							}
						}
					}
					else{
					}
					$subindex++;
				}
				else{
					if(intval($subindex)>0&&intval($subindex)<=intval($subitemnumber)){
						if(strlen($list[$i]['UNITPRICELINK'])==0){
							$table .= '－'.$list[$i]['ITEMNAME'].'x'.$list[$i]['QTY'];
						}
						else{
							$table .= '－'.$list[$i]['ITEMNAME'].'('.$list[$i]['UNITPRICELINK'].')x'.$list[$i]['QTY'];
						}
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$menu[intval($list[$i]['ITEMCODE'])]['name2']!=''){
							if(strlen($list[$i]['UNITPRICELINK'])==0){
								$table .= "\r\n－".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'x'.$list[$i]['QTY'];
							}
							else{
								for($mname=1;$mname<=$menu[intval($list[$i]['ITEMCODE'])]['mnumber'];$mname++){
									if($menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'1']==$list[$i]['UNITPRICELINK']){
										$table .= "\r\n－".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'('.$menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'2'].')x'.$list[$i]['QTY'];
										break;
									}
									else{
									}
								}
							}
						}
						else{
						}
						$subindex++;
					}
					else{
						if(strlen($list[$i]['UNITPRICELINK'])==0){
							$table .= $list[$i]['ITEMNAME'].'x'.$list[$i]['QTY'];
						}
						else{
							$table .= $list[$i]['ITEMNAME'].'('.$list[$i]['UNITPRICELINK'].')x'.$list[$i]['QTY'];
						}
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$menu[intval($list[$i]['ITEMCODE'])]['name2']!=''){
							if(strlen($list[$i]['UNITPRICELINK'])==0){
								$table .= "\r\n".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'x'.$list[$i]['QTY'];
							}
							else{
								for($mname=1;$mname<=$menu[intval($list[$i]['ITEMCODE'])]['mnumber'];$mname++){
									if($menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'1']==$list[$i]['UNITPRICELINK']){
										$table .= "\r\n".$menu[intval($list[$i]['ITEMCODE'])]['name2'].'('.$menu[intval($list[$i]['ITEMCODE'])]['mname'.$mname.'2'].')x'.$list[$i]['QTY'];
										break;
									}
									else{
									}
								}
							}
						}
						else{
						}
						$subitemnumber=0;
						$subindex=-1;
					}
				}
				
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&($list[$i]['SELECTIVEITEM1']==NULL||$list[$i]['SELECTIVEITEM1']==''||strlen($list[$i]['SELECTIVEITEM1'])==0)&&$list[$i+1]['AMT']==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$list[$i]['UNITPRICE'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&($list[$i]['SELECTIVEITEM1']==NULL||$list[$i]['SELECTIVEITEM1']==''||strlen($list[$i]['SELECTIVEITEM1'])==0)&&$list[$i+1]['AMT']==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].($list[$i]['AMT']).$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				
				$linetaste='';
				for($t=1;$t<=10;$t++){
					if($list[$i]['SELECTIVEITEM'.$t]==null){
						break;
					}
					else{
						//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
						$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
						for($st=0;$st<sizeof($temptaste);$st++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								if(preg_match('/99999/',$temptaste[$st])){//手打備註
									if($linetaste==''){
										$linetaste = '　+'.substr($temptaste[$st],7);
									}
									else{
										$linetaste .= ','.substr($temptaste[$st],7);
									}
								}
								else{
									$tasteno=intval(substr($temptaste[$st],0,5));
									$tasteqty=intval(substr($temptaste[$st],5,1));
									//判斷吸管屬性權重
									if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&$tasteno!='99999'&&isset($taste[$tasteno]['straw'])&&$taste[$tasteno]['straw']!=''&&$taste[$tasteno]['straw']!=null&&intval($taste[$tasteno]['straw'])<intval($straw)){
										$straw=intval($taste[$tasteno]['straw']);
									}
									else{
									}
									
									if($linetaste==''){
										$linetaste = '　+'.$taste[$tasteno]['name1'];
									}
									else{
										$linetaste .= ','.$taste[$tasteno]['name1'];
									}

									if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&isset($taste[$tasteno]['name2'])&&$taste[$tasteno]['name2']!=''){
										$linetaste .= '/ '.$taste[$tasteno]['name1'];
									}
									else{
									}
								}
							}
							else if(preg_match('/99999/',$temptaste[$st])){//手打備註
								$tasteno='99999';
								$tasteqty='1';
								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
								$table .= '　+'.substr($temptaste[$st],7);
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";
							}
							else{
								$tasteno=intval(substr($temptaste[$st],0,5));
								$tasteqty=intval(substr($temptaste[$st],5,1));
								//判斷吸管屬性權重
								if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&$tasteno!='99999'&&isset($taste[$tasteno]['straw'])&&$taste[$tasteno]['straw']!=''&&$taste[$tasteno]['straw']!=null&&intval($taste[$tasteno]['straw'])<intval($straw)){
									$straw=intval($taste[$tasteno]['straw']);
								}
								else{
								}
								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
								$table .= '　+'.$taste[$tasteno]['name1'];
								if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$taste[$tasteno]['name2']!=''){
									$table .= " /".$taste[$tasteno]['name2'];
								}
								else{
								}
								if($tasteqty==1){
								}
								else{
									$table .= '*'.$tasteqty;
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								if(intval($taste[$tasteno]['money'])==0){
								}
								else{
									$table .= $taste[$tasteno]['money'];
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
								if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&!isset($temptaste[$st+1])&&$list[$i+1]['AMT']==0)){
									$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
								}
								else{
								}
								$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								if(intval($taste[$tasteno]['money'])*intval($tasteqty)==0){
								}
								else{
									$table .= $content['init']['frontunit'].(intval($taste[$tasteno]['money'])*intval($tasteqty)).$content['init']['unit'];
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";
							}
						}
					}
					/*else{
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
							if(preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
								if($linetaste==''){
									$linetaste = '　+'.substr($list[$i]['SELECTIVEITEM'.$t],7);
								}
								else{
									$linetaste .= ','.substr($list[$i]['SELECTIVEITEM'.$t],7);
								}
							}
							else{
								$tasteno=intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5));
								$tasteqty=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
								//判斷吸管屬性權重
								if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&$tasteno!='99999'&&isset($taste[$tasteno]['straw'])&&$taste[$tasteno]['straw']!=''&&$taste[$tasteno]['straw']!=null&&intval($taste[$tasteno]['straw'])<intval($straw)){
									$straw=intval($taste[$tasteno]['straw']);
								}
								else{
								}
								
								if($linetaste==''){
									$linetaste = '　+'.$taste[$tasteno]['name1'];
								}
								else{
									$linetaste .= ','.$taste[$tasteno]['name1'];
								}

								if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&isset($taste[$tasteno]['name2'])&&$taste[$tasteno]['name2']!=''){
									$linetaste .= '/ '.$taste[$tasteno]['name1'];
								}
								else{
								}
							}
						}
						else if(preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
							$tasteno='99999';
							$tasteqty='1';
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							$table .= '　+'.substr($list[$i]['SELECTIVEITEM'.$t],7);
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
						else{
							$tasteno=intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5));
							$tasteqty=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
							//判斷吸管屬性權重
							if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&$tasteno!='99999'&&isset($taste[$tasteno]['straw'])&&$taste[$tasteno]['straw']!=''&&$taste[$tasteno]['straw']!=null&&intval($taste[$tasteno]['straw'])<intval($straw)){
								$straw=intval($taste[$tasteno]['straw']);
							}
							else{
							}
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							$table .= '　+'.$taste[$tasteno]['name1'];
							if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$taste[$tasteno]['name2']!=''){
								$table .= " /".$taste[$tasteno]['name2'];
							}
							else{
							}
							if($tasteqty==1){
							}
							else{
								$table .= '*'.$tasteqty;
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							if(intval($taste[$tasteno]['money'])==0){
							}
							else{
								$table .= $taste[$tasteno]['money'];
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||($i==(sizeof($list)-2)&&(!isset($list[$i]['SELECTIVEITEM'.($t+1)])||$list[$i]['SELECTIVEITEM'.($t+1)]==null)&&$list[$i+1]['AMT']==0)){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else{
							}
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							if(intval($taste[$tasteno]['money'])*intval($tasteqty)==0){
							}
							else{
								$table .= $content['init']['frontunit'].(intval($taste[$tasteno]['money'])*intval($tasteqty)).$content['init']['unit'];
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
					}*/
				}
				if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/>';
					//2020/4/29 演算法暫時沒有想法，先註解
					/*if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0)||$_POST['discount'][$i]==0)){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					else if($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}*/
					$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					
					$table .= $linetaste;
					
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
					//備註一項一行
				}

				if($list[$i+1]['AMT']!=0){
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
					if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($list)-2)){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					else{
					}
					$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					if($ininame!='-1'){
						$table .= '　+'.$ininame['name']['itemdis'];
					}
					else{
						$table .= '　+優惠折抵';
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
					if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($list)-2)){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					else{
					}
					$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
					if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($list)-2)){
						$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					else{
					}
					$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= $content['init']['frontunit'].'-'.$list[$i+1]['AMT'].$content['init']['unit'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
				}
			}
			//計算吸管數量
			if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&intval($straw)!=999){
				$strawarray[intval($straw)]['number']=intval($strawarray[intval($straw)]['number'])+1*$list[$i]['QTY'];
			}
			else{
			}
		}
	}
	if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'){
		foreach($strawarray as $st){
			if(intval($st['number'])>0){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $st['name'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $st['number'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
				continue;
			}
		}
	}
	else{
	}
	$table .= '</w:tbl>';
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
	if($ininame!='-1'){
		$table .= $ininame['name']['qty'];
	}
	else{
		$table .= '商品數量';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
	$table .= $totalqty;
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	if(isset($sale[0]['TAX1'])&&$sale[0]['TAX1']>0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$table .= $ininame['name']['charge'];
		}
		else{
			$table .= '服務費';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$sale[0]['TAX1'].$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	if(isset($sale[0]['SALESTTLAMT'])&&intval($sale[0]['SALESTTLAMT'])!=intval($totalamt)){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($ininame!='-1'){
			$table .= $ininame['name']['floorspan'];
		}
		else{
			$table .= '低銷差價';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].(intval($sale[0]['SALESTTLAMT'])-intval($totalamt)).$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
	if($ininame!='-1'){
		$table .= $ininame['name']['total'];
	}
	else{
		$table .= '應收金額';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
	$table .= intval($sale[0]['SALESTTLAMT'])+intval($sale[0]['TAX1']);
	$table .= "</w:t></w:r></w:p></w:tc></w:tr>";

	if(isset($sale[0]['RELINVOICENUMBER'])&&$sale[0]['RELINVOICENUMBER']!=''&&$sale[0]['RELINVOICENUMBER']!=NULL){//<w:gridSpan w:val="2"/>
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';

		$table .= $sale[0]['RELINVOICENUMBER'];

		$table .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	else{
	}

	$table .= "</w:tbl>";
	
	$memtable='';
	$memtable .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
	if(isset($sale[0]['CUSTCODE'])&&$sale[0]['CUSTCODE']!=null&&$sale[0]['CUSTCODE']!=''){
		if(preg_match('/;-;/',$sale[0]['CUSTCODE'])){
			$tempmemno=preg_split('/;-;/',$sale[0]['CUSTCODE']);
		}
		else{
			$tempmemno[0]=$sale[0]['CUSTCODE'];
		}
		include_once '../../../tool/dbTool.inc.php';
		if($content['init']['onlinemember']=='1'){//網路會員
			$PostData = array(
				"type"=>"online",
				"ajax" => "",
				"company" => $data['basic']['company'],
				"memno" => $tempmemno[0]
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php');//
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			// Edit: prior variable $postFields should be $postfields;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
			$memdata = curl_exec($ch);
			$mem=json_decode($memdata,1);
			if(curl_errno($ch) !== 0) {
				//print_r('cURL error when connecting to http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php : ' . curl_error($ch));
			}
			curl_close($ch);
		}
		else{
			$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
			$sql='SELECT * FROM person WHERE memno="'.$sale[0]['CUSTCODE'].'"';
			$mem=sqlquery($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
		}
		if(isset($mem[0]['name'])){
			if((!isset($print['clientlist']['memberdata'])||$print['clientlist']['memberdata']=='1')){
				if(isset($content['init']['writememdata'.substr($list[0]['REMARKS'],0,1)])&&$content['init']['writememdata'.substr($list[0]['REMARKS'],0,1)]==1){
					$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					if(isset($mem[0]['setting'])&&$mem[0]['setting']!=null&&strlen($mem[0]['setting'])>0){
						$memtable .= $mem[0]['name']."(".$mem[0]['setting'].")";
						//$document1->setValue('memname',$mem[0]['name']."(".$mem[0]['setting'].")");
					}
					else{
						$memtable .= $mem[0]['name'];
						//$document1->setValue('memname',$mem[0]['name']);
					}
					$memtable .= "</w:t></w:r></w:p></w:tc>";
					$memtable .= "</w:tr>";
					$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					$memtable .= $mem[0]['tel'];
					//$document1->setValue('memtel',$memdata[0]['tel']);
					$memtable .= "</w:t></w:r></w:p></w:tc>";
					$memtable .= "</w:tr>";
					$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					if(preg_match('/;*;/',$mem[0]['address'])){
						$addselect=preg_split('/\;\*\;/',$mem[0]['address']);
						if(isset($tempmemno[2])&&isset($addselect[$tempmemno[2]-1])){
							$memtable .= $addselect[($tempmemno[2]-1)];
						}
						else{
							$memtable .= $addselect[0];
						}
					}
					else{
						$memtable .= $mem[0]['address'];
					}
					//$document1->setValue('memaddress',$memdata[0]['address']);
					$memtable .= "</w:t></w:r></w:p></w:tc>";
					$memtable .= "</w:tr>";
					$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/>';
					if(!isset($print['clientlist']['mempointmoney'])||$print['clientlist']['mempointmoney']=='1'){
					}
					else{
						$memtable .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
					}
					$memtable .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					$memtable .= $mem[0]['remark'];
					//$document1->setValue('memremarks',"備註:".$memdata[0]['remark']);
					$memtable .= "</w:t></w:r></w:p></w:tc>";
					$memtable .= "</w:tr>";
				}
				else{
				}
			}
			else{
			}
		}
		else{
			/*$document1->setValue('memname','');
			$document1->setValue('memtel',"");
			$document1->setValue('memaddress','');
			$document1->setValue('memremarks','');*/
		}
	}
	else{
		/*$document1->setValue('memname','');
		$document1->setValue('memtel',"");
		$document1->setValue('memaddress','');
		$document1->setValue('memremarks','');*/
	}
	$memtable .= "</w:tbl>";
	/*$document1->setValue('memname','');
	$document1->setValue('memtel',"");
	$document1->setValue('memaddress','');
	$document1->setValue('memremarks','');*/
	$document1->setValue('memtable',$memtable);

	$document1->setValue('item',$table);
	//date_default_timezone_set('Asia/Taipei');
	//$datetime=date('YmdHis');

	date_default_timezone_set($content['init']['settime']);
	$filename=date('YmdHis');

	$imgarray='empty';

	/*create barcode*/
	include('../../../tool/phpbarcode/src/BarcodeGenerator.php');
	include('../../../tool/phpbarcode/src/BarcodeGeneratorPNG.php');

	$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
	//echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode(($_POST['bizdate'].$consecnumber), $generator::TYPE_CODE_128)) . '">';
	file_put_contents('../../../print/barcode/'.$_POST['machinetype'].'.png', $generator->getBarcode(($sale[0]['BIZDATE'].$no), $generator::TYPE_CODE_128));
	/*create barcode*/

	$imgarray=array('barcode'=>'../../../print/barcode/'.$_POST['machinetype'].'.png');

	/*if(isset($_POST['receipt'])&&$_POST['receipt']=='1'){//收據章//2020/1/31後續在設定檔加入補印產生收據章參數
		if(file_exists('../../../print/receipt.png')){
			$imgarray['receipt']='../../../print/receipt.png';
		}
		else{
		}
	}
	else{
	}*/

	/*if(isset($print['clientlist']['tcqrcode'])&&$print['clientlist']['tcqrcode']=='1'&&isset($data['tcqrcode']['dep'])&&isset($data['tcqrcode']['dep'])){//串接騰雲商場POSQRcode//2020/1/31於補印的狀況會缺少一些關鍵參數，後續調整
		include_once '../../../tool/phpqrcode/qrlib.php';
		if(file_exists('../../../print/tcqrcode')){
		}
		else{
			mkdir('../../../print/tcqrcode');
		}

		$qrcodestring='1'.str_pad($data['tcqrcode']['dep'],'10','0',STR_PAD_LEFT).str_pad($data['tcqrcode']['depclass'],'3','0',STR_PAD_LEFT);
		if(isset($_POST['listtotal'])){
			$qrcodestring .= str_pad($_POST['should'],'8','0',STR_PAD_LEFT).str_pad($_POST['should'],'8','0',STR_PAD_LEFT);
		}
		else{
			if(isset($_POST['floorspan'])){
				if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
					$qrcodestring.=str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']),'8','0',STR_PAD_LEFT).str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']),'8','0',STR_PAD_LEFT);
				}
				else{
					$qrcodestring.=str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']),'8','0',STR_PAD_LEFT).str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']),'8','0',STR_PAD_LEFT);
				}
			}
			else{
				if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
					$qrcodestring.=str_pad(($_POST['total']+$_POST['charge']+$autodis[0]['AMT']),'8','0',STR_PAD_LEFT).str_pad(($_POST['total']+$_POST['charge']+$autodis[0]['AMT']),'8','0',STR_PAD_LEFT);
				}
				else{
					$qrcodestring.=str_pad(($_POST['total']+$_POST['charge']),'8','0',STR_PAD_LEFT).str_pad(($_POST['total']+$_POST['charge']),'8','0',STR_PAD_LEFT);
				}
			}
		}
		$tempqrcodestring=str_split($qrcodestring);//分割字元
		$onegroup=0;
		$twogroup=0;
		for($tcindex=1;$tcindex<=sizeof($tempqrcodestring);$tcindex++){
			if($tcindex%2==1){//奇數位
				$onegroup=intval($onegroup)+intval($tempqrcodestring[$tcindex-1]);
			}
			else{//偶數位
				$twogroup=intval($twogroup)+intval($tempqrcodestring[$tcindex-1]);
			}
		}
		$twogroup=$twogroup*3;
		$subtotalgroup=intval($onegroup)+intval($twogroup);//奇數位+偶數位*3
		if($subtotalgroup%10==0){
			$checknumber=0;//$subtotalgroup的個位數為0；則檢查碼為0
		}
		else{
			$checknumber=10-($subtotalgroup%10);//10-$subtotalgroup的個位數；即是檢查碼
		}
		$qrcodestring .= $checknumber.';';
		QRcode::png('A+'.$qrcodestring.str_pad($consecnumber,'18','0',STR_PAD_LEFT),'../../../print/tcqrcode/'.$consecnumber.'.png','H',6);
		$imgarray['qrcode']='../../../print/tcqrcode/'.$consecnumber.'.png';
	}
	else{
	}*/

	$document1->replaceStrToMyImg('qrcode',$imgarray);
	//$document1->replaceStrToQrcode('qrcode','empty');//2020/1/31產生快速結帳的barcode

	//$document1->save("../../../print/noread/".$filename."_".$_POST['machinetype']."clientlist_".intval($no).".docx");
	$document1->save("../../../print/read/".intval($no)."_clientlist".$listtype.$_POST['machinetype']."_".$filename.".docx");
	if(is_numeric(substr($list[0]['REMARKS'],0,1))){
		if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
			$prt=fopen("../../../print/noread/".intval($no)."_clientlist".$listtype.$_POST['machinetype']."_".$filename.".".$_POST['machinetype'],'w');
		}
		else{
			$prt=fopen("../../../print/noread/".intval($no)."_clientlist".$listtype.$_POST['machinetype']."_".$filename.".prt",'w');
		}
	}
	else{
		if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
			$prt=fopen("../../../print/noread/".intval($no)."_clientlist".$listtype.$_POST['machinetype']."_".$filename.".".$_POST['machinetype'],'w');
		}
		else{
			$prt=fopen("../../../print/noread/".intval($no)."_clientlist".$listtype.$_POST['machinetype']."_".$filename.".prt",'w');
		}
	}
	fclose($prt);
}
else{
}
if($type=='tempall'||$type=='temptag'||$type=='all'||$type=='tag'){
	//copy('../../../print/read/tag_'.intval($no).'.docx','../../../print/noread/tag_'.intval($no).'.docx');
	include_once '../../../tool/dbTool.inc.php';
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	if($type=='tempall'||$type=='temptag'){
		$sql='SELECT TABLENUMBER,TERMINALNUMBER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'"';
		$table=sqlquery($conn,$sql,'sqlite');
		$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'" AND LINENUMBER IN ("'.preg_replace('/,/','","',$_POST['linenumber']).'") ORDER BY LINENUMBER ASC';
		$list=sqlquery($conn,$sql,'sqlite');
	}
	else if($type=='all'||$type='tag'){
		$sql='SELECT TABLENUMBER,TERMINALNUMBER FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'"';
		$table=sqlquery($conn,$sql,'sqlite');
		$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'" AND LINENUMBER IN ("'.preg_replace('/,/','","',$_POST['linenumber']).'") ORDER BY LINENUMBER ASC';
		$list=sqlquery($conn,$sql,'sqlite');

	}
	$machine=$table[0]['TERMINALNUMBER'];
	//echo $sql;
	$sql='SELECT * FROM salemap WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$no.'"';
	$sale=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	//print_r($list);
	require_once '../../../tool/PHPWord.php';
	$content=parse_ini_file('../../../database/initsetting.ini',true);
	//date_default_timezone_set('Asia/Taipei');
	//date_default_timezone_set($content['init']['settime']);

	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
	$data=parse_ini_file('../../../database/setup.ini',true);
	$print=parse_ini_file('../../../database/printlisttag.ini',true);
	
	$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
	$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
	$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
	$pti=parse_ini_file('../../../database/itemprinttype.ini',true);

	$document1='';
	
	//echo $print['item']['tagtemplate'];
	if(isset($print['item']['tagtemplate'])&&file_exists('../../../database/tag'.$print['item']['tagtemplate'].'.ini')){
		$tag=parse_ini_file('../../../database/tag'.$print['item']['tagtemplate'].'.ini',true);
	}
	else{
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
	
	$item='';
	$index=1;
	$totalqty=0;
	for($i=0;$i<sizeof($list);$i++){
		if($list[$i]['ITEMCODE']=='item'||$list[$i]['ITEMCODE']=='list'){
			continue;
		}
		else{
			if(preg_match('/-/',$list[$i]['REMARKS'])){
				$remarks=substr($list[$i]['REMARKS'],0,1);
			}
			else{
				$remarks=$list[$i]['REMARKS'];
			}
			if(isset($pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['tag'.$remarks])&&$pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['tag'.$remarks]=='0'){
				//$tempindex++;
			}
			else{
				for($j=0;$j<$list[$i]['QTY'];$j++){
					$totalqty++;
				}
			}
		}
	}

	$tagcontent=array();
	for($i=0;$i<sizeof($list);$i++){
		if($list[$i]['ITEMCODE']=='item'||$list[$i]['ITEMCODE']=='list'){
			continue;
		}
		else{
			for($j=0;$j<$list[$i]['QTY'];$j++){
				if(preg_match('/-/',$list[$i]['REMARKS'])){
					$remarks=substr($list[$i]['REMARKS'],0,1);
				}
				else{
					$remarks=$list[$i]['REMARKS'];
				}
				if($menu[intval($list[$i]['ITEMCODE'])]['printtype']!=''&&$pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['tag'.$remarks]=='1'){
					if(isset($tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']])){
					}
					else{
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]='';
					}
					if(intval($print['item']['tagtemplate'])==9){
						
						if(isset($tag)){
							//row1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
							//row1td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1000" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$list[$i]['UNITPRICELINK'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						if(isset($tag)){
							//row1td2
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2697" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						
						if(isset($tag)){
							//row1td3
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$index.'/'.$totalqty;
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
						
						//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
						if(isset($tag)){
							//row2
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
							//row2td1
							if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
							}
							if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else if(isset($print['item']['tagsize'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
							}
						}
						if(isset($menu[intval($list[$i]['ITEMCODE'])]['name1'])){
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
						}
						else{
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
						if(isset($tag)){
							//row3
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
							//row3td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="4"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
						
						$tt='';
						for($t=1;$t<=10;$t++){
							//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
							if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
								$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
								for($st=0;$st<sizeof($temptaste);$st++){
									if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
										$temp=substr($temptaste[$st],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
										$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
										$num=intval(substr($temptaste[$st],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
								}
							}
							else{
								break;
							}
							/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
								$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
								if(strlen($tt)==0){
									$tt=$temp;
								}
								else{
									$tt=$tt.','.$temp;
								}
							}
							else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
								$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
								$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
								if(strlen($tt)==0){
									$tt=$temp;
									if(intval($num)>1){
										$tt=$tt.'*'.$num;
									}
									else{
									}
								}
								else{
									$tt=$tt.','.$temp;
									if(intval($num)>1){
										$tt=$tt.'*'.$num;
									}
									else{
									}
								}
							}
							else{
								break;
							}*/
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
						
						if(isset($tag)){
							//row4
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
							//row4td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="4046" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="4046" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="4046" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="4046" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
							}
						}
						//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						if(isset($tag)){
							//row4td2
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="954" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0036656A" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="954" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0036656A" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="954" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0036656A" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="954" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="0036656A" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
							}
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$content['init']['frontunit'].($list[$i]['AMT']/$list[$i]['QTY']).$content['init']['unit'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
						if(isset($tag)){
							//row5
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
							//row5td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="4"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							if(isset($tagtype)&&$tagtype=='3225'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="4"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
							}
							else if(isset($tagtype)&&$tagtype=='4030'){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="4"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="4"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
							}
						}
						//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
						date_default_timezone_set($content['init']['settime']);
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=date('m/d H:i').' '.$setup['basic']['storyname'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
						$index++;
					}
					else if(intval($print['item']['tagtemplate'])==7){
						if(isset($tag)){
							//row1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:cantSplit/><w:trHeight w:hRule="exact" w:val="1120"/></w:trPr>';
							//row1td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row1width'].'" w:type="pct"/><w:vAlign w:val="top"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="374" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						if($remarks=='1'){
							if(isset($table[0]['TABLENUMBER'])&&$table[0]['TABLENUMBER']!=''){
								$tablename='';
								if(isset($content['init']['controltable'])&&$content['init']['controltable']=='1'){//2020/3/23 開啟桌控
									if(file_exists('../../../database/floorspend.ini')){
										$tablemap=parse_ini_file('../../../database/floorspend.ini',true);
									}
									else{
									}
									if(preg_match('/,/',$table[0]['TABLENUMBER'])){//併桌
										$splittable=preg_split('/,/',$table[0]['TABLENUMBER']);
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
										if(preg_match('/-/',$table[0]['TABLENUMBER'])){//拆桌
											$inittable=preg_split('/-/',$table[0]['TABLENUMBER']);
											if(isset($tablemap['Tname'][$inittable[0]])){
												$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
											}
											else{
												$tablename .= $table[0]['TABLENUMBER'];
											}
										}
										else{
											if(isset($tablemap['Tname'][$table[0]['TABLENUMBER']])){
												$tablename .= $tablemap['Tname'][$table[0]['TABLENUMBER']];
											}
											else{
												$tablename .= $table[0]['TABLENUMBER'];
											}
										}
									}
								}
								else{
									$tablename=$table[0]['TABLENUMBER'];
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks].$tablename;
							}
							else if(isset($sale[0]['saleno'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks].$sale[0]['saleno'];
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks];
							}
						}
						else{
							if(isset($sale[0]['saleno'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks].$sale[0]['saleno'];
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks];
							}
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='-'.str_pad($index, 2, "0", STR_PAD_LEFT);
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td2
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row2width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="280" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
						}
						if(isset($menu[intval($list[$i]['ITEMCODE'])]['name1'])){
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
						}
						else{
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td3
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row3width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="290" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['introtitle1'].$menu[intval($list[$i]['ITEMCODE'])]['introduction1'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td4
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row4width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td4size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td4size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td4size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td4size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="350" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$content['init']['frontunit'].(floatval($list[$i]['AMT'])/floatval($list[$i]['QTY'])).$content['init']['unit'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td5
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row5width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td5size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td5size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td5size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td5size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="800" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tt='';
						for($t=1;$t<=10;$t++){
							//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
							if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
								$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
								for($st=0;$st<sizeof($temptaste);$st++){
									if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
										$temp=substr($temptaste[$st],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
										$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
										$num=intval(substr($temptaste[$st],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
								}
							}
							else{
								break;
							}
							/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
								$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
								if(strlen($tt)==0){
									$tt=$temp;
								}
								else{
									$tt=$tt.','.$temp;
								}
							}
							else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
								$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
								$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
								if(strlen($tt)==0){
									$tt=$temp;
									if(intval($num)>1){
										$tt=$tt.'*'.$num;
									}
									else{
									}
								}
								else{
									$tt=$tt.','.$temp;
									if(intval($num)>1){
										$tt=$tt.'*'.$num;
									}
									else{
									}
								}
							}
							else{
								break;
							}*/
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td6
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row6width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td6size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td6size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td6size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td6size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="260" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						date_default_timezone_set($content['init']['settime']);
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=date('m/d H:i');
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';

						if(isset($tag)){
							//row1td7
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row7width'].'" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td7size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td7size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td7size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td7size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="260" w:type="pct"/><w:vMerge w:val="restart"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';

						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';			

						if(isset($tag)){
							//row2
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:cantSplit/><w:trHeight w:hRule="exact" w:val="320"/></w:trPr>';
							//row2td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row1width'].'" w:type="pct"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:cantSplit/><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="280" w:type="pct"/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$list[$i]['UNITPRICELINK'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						for($tcindex=2;$tcindex<=7;$tcindex++){
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="'.$tag[$tagtype]['row'.$tcindex.'width'].'" w:type="pct"/><w:vMerge/><w:textDirection w:val="btLr"/></w:tcPr><w:p w:rsidR="004E01B5" w:rsidP="004E01B5" w:rsidRDefault="004E01B5"><w:pPr><w:spacing w:lineRule="atLeast" w:line="0"/></w:pPr></w:p></w:tc>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';						

						$index++;
					}
					else if(intval($print['item']['tagtemplate'])==6){
						if(isset($tag)){
							//row1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
							//row1td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="973" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="973" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$list[$i]['UNITPRICELINK'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						if(isset($tag)){
							//row1td2
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1909" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1909" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						if(isset($tag)){
							//row1td3
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2118" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2118" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=str_pad($index, 2, "0", STR_PAD_LEFT).'/'.str_pad($totalqty, 2, "0", STR_PAD_LEFT);
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row2
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
							//row2td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row2td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
						}
						if(isset($menu[intval($list[$i]['ITEMCODE'])]['name1'])){
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
						}
						else{
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row3
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
							//row3td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
						}
						if(isset($menu[intval($list[$i]['ITEMCODE'])]['name2'])){
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name2'];
						}
						else{
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row4
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
							//row4td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$content['init']['frontunit'].(floatval($list[$i]['AMT'])/floatval($list[$i]['QTY'])).$content['init']['unit'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row5
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
							//row5td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tt='';
						for($t=1;$t<=10;$t++){
							//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
							if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
								$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
								for($st=0;$st<sizeof($temptaste);$st++){
									if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
										$temp=substr($temptaste[$st],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
										$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
										$num=intval(substr($temptaste[$st],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
								}
							}
							else{
								break;
							}
							/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
								$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
								if(strlen($tt)==0){
									$tt=$temp;
								}
								else{
									$tt=$tt.','.$temp;
								}
							}
							else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
								$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
								$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
								if(strlen($tt)==0){
									$tt=$temp;
									if(intval($num)>1){
										$tt=$tt.'*'.$num;
									}
									else{
									}
								}
								else{
									$tt=$tt.','.$temp;
									if(intval($num)>1){
										$tt=$tt.'*'.$num;
									}
									else{
									}
								}
							}
							else{
								break;
							}*/
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row6
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row6height'].'"/></w:trPr>';
							//row6td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row6td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row6td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row6td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row6td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=substr($list[$i]['CREATEDATETIME'],4,2).'/'.substr($list[$i]['CREATEDATETIME'],6,2).' '.substr($list[$i]['CREATEDATETIME'],8,2).':'.substr($list[$i]['CREATEDATETIME'],10,2);
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

						if(isset($tag)){
							//row7
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row7height'].'"/></w:trPr>';
							//row7td1
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row7td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row7td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row7td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row7td1size'])*2).'"/></w:rPr><w:t>';
						}
						else{
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="280"/></w:trPr>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

						$index++;
					}
					else{
						if(intval($print['item']['tagtemplate'])==5||intval($print['item']['tagtemplate'])==8){
							if(isset($tag)){
								//row1
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
								//row1td1
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
							}
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks];
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';

							if(isset($tag)){
								//row1td2
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="bottom"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
							}
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=str_pad($index, 2, "0", STR_PAD_LEFT).'/'.str_pad($totalqty, 2, "0", STR_PAD_LEFT);
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
						}
						else{
							if(isset($tag)){
								//row1
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row1height'].'"/></w:trPr>';
								//row1td1
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td1size'])*2).'"/></w:rPr><w:t>';
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="00945C14" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1877" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
								}
							}
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$buttons['name']['listtype'.$remarks];
							if($remarks=='1'&&isset($table[0]['TABLENUMBER'])&&$table[0]['TABLENUMBER']!=''){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$table[0]['TABLENUMBER'];
							}
							else{
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$sale[0]['saleno'];
							}
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
							
							if(isset($print['item']['tagtemplate'])&&intval($print['item']['tagtemplate'])>0){
								if(intval($print['item']['tagtemplate'])==1||intval($print['item']['tagtemplate'])==3){
									if(isset($tag)){
										//row1td2
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$list[$i]['UNITPRICELINK'];
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
									
									if(isset($tag)){
										//row1td3
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$index.'/'.$totalqty;
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								}
								else if(intval($print['item']['tagtemplate'])==2||intval($print['item']['tagtemplate'])==4){
									if(isset($tag)){
										//row1td2
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3123" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3123" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3123" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3123" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									date_default_timezone_set($content['init']['settime']);
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=str_pad($index, 2, "0", STR_PAD_LEFT).'/'.str_pad($totalqty, 2, "0", STR_PAD_LEFT).'  '.date('H:i');
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								}
							}
							else{
								if(isset($tag)){
									//row1td2
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1820" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$list[$i]['UNITPRICELINK'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								
								if(isset($tag)){
									//row1td3
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row1td3size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$index.'/'.$totalqty;
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
							}
						}
						$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

						if(intval($print['item']['tagtemplate'])==4){
							if(isset($tag)){
								//row2
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
								//row2td1
								if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($print['item']['tagsize'])){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
									}
								}
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							if(isset($menu[intval($list[$i]['ITEMCODE'])]['name1'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
							}
							else{
							}
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							if(intval($print['item']['tagtemplate'])>0){
								if(isset($tag)){
									//row3
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$list[$i]['UNITPRICELINK'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row4
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								
								$tt='';
								for($t=1;$t<=10;$t++){
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
										for($st=0;$st<sizeof($temptaste);$st++){
											if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
												$temp=substr($temptaste[$st],7);
												if(strlen($tt)==0){
													$tt=$temp;
												}
												else{
													$tt=$tt.','.$temp;
												}
											}
											else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
												$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
												$num=intval(substr($temptaste[$st],5,1));
												if(strlen($tt)==0){
													$tt=$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
												else{
													$tt=$tt.','.$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
											}
										}
									}
									else{
										break;
									}
									/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
										$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
										$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
									else{
										break;
									}*/
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row5
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
									//row5td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
									}
								}
								//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							}
							else{
								if(isset($tag)){
									//row3
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tt='';
								for($t=1;$t<=10;$t++){
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
										for($st=0;$st<sizeof($temptaste);$st++){
											if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
												$temp=substr($temptaste[$st],7);
												if(strlen($tt)==0){
													$tt=$temp;
												}
												else{
													$tt=$tt.','.$temp;
												}
											}
											else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
												$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
												$num=intval(substr($temptaste[$st],5,1));
												if(strlen($tt)==0){
													$tt=$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
												else{
													$tt=$tt.','.$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
											}
										}
									}
									else{
										break;
									}
									/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
										$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
										$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
									else{
										break;
									}*/
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row4
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
								}
								//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								if(isset($tag)){
									//row4td2
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$content['init']['frontunit'].($list[$i]['AMT']/$list[$i]['QTY']).$content['init']['unit'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							}
							$index++;
						}
						else if(intval($print['item']['tagtemplate'])==5){
							if(isset($tag)){
								//row2
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
								//row2td1
								if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($print['item']['tagsize'])){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
									}
								}
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							if(isset($menu[intval($list[$i]['ITEMCODE'])]['name1'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
							}
							else{
							}
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							if(intval($print['item']['tagtemplate'])>0){
								if(isset($tag)){
									//row3
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$list[$i]['UNITPRICELINK'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row4
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tt='';
								for($t=1;$t<=10;$t++){
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
										for($st=0;$st<sizeof($temptaste);$st++){
											if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
												$temp=substr($temptaste[$st],7);
												if(strlen($tt)==0){
													$tt=$temp;
												}
												else{
													$tt=$tt.','.$temp;
												}
											}
											else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
												$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
												$num=intval(substr($temptaste[$st],5,1));
												if(strlen($tt)==0){
													$tt=$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
												else{
													$tt=$tt.','.$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
											}
										}
									}
									else{
										break;
									}
									/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
										$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
										$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
									else{
										break;
									}*/
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							}
							else{
								if(isset($tag)){
									//row3
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$item.='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tt='';
								for($t=1;$t<=10;$t++){
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
										for($st=0;$st<sizeof($temptaste);$st++){
											if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
												$temp=substr($temptaste[$st],7);
												if(strlen($tt)==0){
													$tt=$temp;
												}
												else{
													$tt=$tt.','.$temp;
												}
											}
											else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
												$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
												$num=intval(substr($temptaste[$st],5,1));
												if(strlen($tt)==0){
													$tt=$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
												else{
													$tt=$tt.','.$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
											}
										}
									}
									else{
										break;
									}
									/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
										$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
										$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
									else{
										break;
									}*/
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row4
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
								}
								//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								if(isset($tag)){
									//row4td2
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$content['init']['frontunit'].$_POST['money'][$i].$content['init']['unit'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							}
							$index++;
						}
						else if(intval($print['item']['tagtemplate'])==8){
							if(isset($tag)){
								//row2
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
								//row2td1
								if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($print['item']['tagsize'])){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
									}
								}
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							if(isset($menu[intval($list[$i]['ITEMCODE'])]['name1'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
							}
							else{
							}
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							if(intval($print['item']['tagtemplate'])>0){
								if(isset($tag)){
									//row3
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name2'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row4
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$list[$i]['UNITPRICELINK'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row5
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
									//row5td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tt='';
								for($t=1;$t<=10;$t++){
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
										for($st=0;$st<sizeof($temptaste);$st++){
											if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
												$temp=substr($temptaste[$st],7);
												if(strlen($tt)==0){
													$tt=$temp;
												}
												else{
													$tt=$tt.','.$temp;
												}
											}
											else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
												$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
												$num=intval(substr($temptaste[$st],5,1));
												if(strlen($tt)==0){
													$tt=$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
												else{
													$tt=$tt.','.$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
											}
										}
									}
									else{
										break;
									}
									/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
										$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
										$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
									else{
										break;
									}*/
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							}
							else{
								if(isset($tag)){
									//row3
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
									//row3td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name2'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';

								if(isset($tag)){
									//row4
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
									//row4td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
								
								$tt='';
								for($t=1;$t<=10;$t++){
									//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
									if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
										for($st=0;$st<sizeof($temptaste);$st++){
											if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
												$temp=substr($temptaste[$st],7);
												if(strlen($tt)==0){
													$tt=$temp;
												}
												else{
													$tt=$tt.','.$temp;
												}
											}
											else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
												$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
												$num=intval(substr($temptaste[$st],5,1));
												if(strlen($tt)==0){
													$tt=$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
												else{
													$tt=$tt.','.$temp;
													if(intval($num)>1){
														$tt=$tt.'*'.$num;
													}
													else{
													}
												}
											}
										}
									}
									else{
										break;
									}
									/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
										$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
										if(strlen($tt)==0){
											$tt=$temp;
										}
										else{
											$tt=$tt.','.$temp;
										}
									}
									else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
										$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
										$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
										if(strlen($tt)==0){
											$tt=$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
										else{
											$tt=$tt.','.$temp;
											if(intval($num)>1){
												$tt=$tt.'*'.$num;
											}
											else{
											}
										}
									}
									else{
										break;
									}*/
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
								
								if(isset($tag)){
									//row5
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
									//row5td1
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
									}
								}
								//$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								if(isset($tag)){
									//row5td2
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td2size'])*2).'"/></w:rPr><w:t>';
								}
								else{
									if(isset($tagtype)&&$tagtype=='3225'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
									}
									else if(isset($tagtype)&&$tagtype=='4030'){
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
									else{
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
									}
								}
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$content['init']['frontunit'].($list[$i]['AMT']/$list[$i]['QTY']).$content['init']['unit'];
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							}
							$index++;
						}
						else{
							if(isset($tag)){
								//row2
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row2height'].'"/></w:trPr>';
								//row2td1
								if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							else{
								if(isset($tagtype)&&$tagtype=='3225'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="340"/></w:trPr>';
								}
								else if(isset($tagtype)&&$tagtype=='4030'){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="394"/></w:trPr>';
								}
								if(isset($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($menu[intval($list[$i]['ITEMCODE'])]['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else if(isset($print['item']['tagsize'])){
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($print['item']['tagsize'])*2).'"/><w:szCs w:val="'.(floatval($print['item']['tagsize'])*2).'"/></w:rPr><w:t>';
								}
								else{
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>';
								}
							}
							if(isset($menu[intval($list[$i]['ITEMCODE'])]['name1'])){
								$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
							}
							else{
							}
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
							$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
							
							if(isset($print['item']['tagtemplate'])&&intval($print['item']['tagtemplate'])>0){
								if(intval($print['item']['tagtemplate'])==1||intval($print['item']['tagtemplate'])==2){
									if(isset($tag)){
										//row3
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
										//row3td1
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="457"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="670"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
									$tt='';
									for($t=1;$t<=10;$t++){
										//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
										if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
											$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
											for($st=0;$st<sizeof($temptaste);$st++){
												if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
													$temp=substr($temptaste[$st],7);
													if(strlen($tt)==0){
														$tt=$temp;
													}
													else{
														$tt=$tt.','.$temp;
													}
												}
												else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
													$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
													$num=intval(substr($temptaste[$st],5,1));
													if(strlen($tt)==0){
														$tt=$temp;
														if(intval($num)>1){
															$tt=$tt.'*'.$num;
														}
														else{
														}
													}
													else{
														$tt=$tt.','.$temp;
														if(intval($num)>1){
															$tt=$tt.'*'.$num;
														}
														else{
														}
													}
												}
											}
										}
										else{
											break;
										}
										/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
											$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
											if(strlen($tt)==0){
												$tt=$temp;
											}
											else{
												$tt=$tt.','.$temp;
											}
										}
										else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
											$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
											$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
											if(strlen($tt)==0){
												$tt=$temp;
												if(intval($num)>1){
													$tt=$tt.'*'.$num;
												}
												else{
												}
											}
											else{
												$tt=$tt.','.$temp;
												if(intval($num)>1){
													$tt=$tt.'*'.$num;
												}
												else{
												}
											}
										}
										else{
											break;
										}*/
									}
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
									if(isset($tag)){
										//row4
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
										//row4td1
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
									}
									//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
									if(isset($tag)){
										//row4td2
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$content['init']['frontunit'].($list[$i]['AMT']/$list[$i]['QTY']).$content['init']['unit'];
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
								}
								else if(intval($print['item']['tagtemplate'])==3){
									if(isset($tag)){
										//row3
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row3height'].'"/></w:trPr>';
										//row3td1
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row3td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="212"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="425"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/></w:tcPr><w:p w:rsidR="008F4BB6" w:rsidRDefault="008F4BB6" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450"><w:trPr><w:trHeight w:hRule="exact" w:val="284"/></w:trPr>';
									$tt='';
									for($t=1;$t<=10;$t++){
										//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
										if($list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
											$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$t]);
											for($st=0;$st<sizeof($temptaste);$st++){
												if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
													$temp=substr($temptaste[$st],7);
													if(strlen($tt)==0){
														$tt=$temp;
													}
													else{
														$tt=$tt.','.$temp;
													}
												}
												else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
													$temp=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
													$num=intval(substr($temptaste[$st],5,1));
													if(strlen($tt)==0){
														$tt=$temp;
														if(intval($num)>1){
															$tt=$tt.'*'.$num;
														}
														else{
														}
													}
													else{
														$tt=$tt.','.$temp;
														if(intval($num)>1){
															$tt=$tt.'*'.$num;
														}
														else{
														}
													}
												}
											}
										}
										else{
											break;
										}
										/*if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$t])){//手打備註
											$temp=substr($list[$i]['SELECTIVEITEM'.$t],7);
											if(strlen($tt)==0){
												$tt=$temp;
											}
											else{
												$tt=$tt.','.$temp;
											}
										}
										else if(isset($list[$i]['SELECTIVEITEM'.$t])&&$list[$i]['SELECTIVEITEM'.$t]!=''&&$list[$i]['SELECTIVEITEM'.$t]!=null){
											$temp=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
											$num=intval(substr($list[$i]['SELECTIVEITEM'.$t],5,1));
											if(strlen($tt)==0){
												$tt=$temp;
												if(intval($num)>1){
													$tt=$tt.'*'.$num;
												}
												else{
												}
											}
											else{
												$tt=$tt.','.$temp;
												if(intval($num)>1){
													$tt=$tt.'*'.$num;
												}
												else{
												}
											}
										}
										else{
											break;
										}*/
									}
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$tt;
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
									
									if(isset($tag)){
										//row4
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row4height'].'"/></w:trPr>';
										//row4td1
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="3697" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="9"/><w:szCs w:val="9"/></w:rPr><w:t>';
										}
									}
									//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
									date_default_timezone_set($content['init']['settime']);
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=date('Y/m/d H:i:s');
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
									if(isset($tag)){
										//row4td2
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas"/><w:sz w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row4td2size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="1303" w:type="pct"/></w:tcPr><w:p w:rsidR="0048424D" w:rsidRPr="0048424D" w:rsidRDefault="001E2380" w:rsidP="0048424D"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>';
										}
									}
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$content['init']['frontunit'].($list[$i]['AMT']/$list[$i]['QTY']).$content['init']['unit'];
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
									
									if(isset($tag)){
										//row5
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="'.$tag[$tagtype]['row5height'].'"/></w:trPr>';
										//row5td1
										$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/><w:szCs w:val="'.(floatval($tag[$tagtype]['row5td1size'])*2).'"/></w:rPr><w:t>';
									}
									else{
										if(isset($tagtype)&&$tagtype=='3225'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
										}
										else if(isset($tagtype)&&$tagtype=='4030'){
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
										}
										else{
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tr w:rsidR="0048424D" w:rsidTr="00945C14"><w:trPr><w:trHeight w:hRule="exact" w:val="245"/></w:trPr>';
											$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/><w:vAlign w:val="bottom"/></w:tcPr><w:p w:rsidR="00945C14" w:rsidRPr="0048424D" w:rsidRDefault="00945C14" w:rsidP="00945C14"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:sz w:val="17"/><w:szCs w:val="17"/></w:rPr><w:t>';
										}
									}
									//$item.='<w:tr w:rsidR="0048424D" w:rsidTr="00A26450">';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].=$print['item']['taghint'];
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:t></w:r></w:p></w:tc>';
									$tagcontent[$menu[intval($list[$i]['ITEMCODE'])]['printtype']].='</w:tr>';
								}
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
	foreach($tagcontent as $index=>$value){
		$PHPWord = new PHPWord();
		if(isset($print['item']['tagtemplate'])&&file_exists('../../../template/tag'.$print['item']['tagtemplate'].$tagtype.'.docx')){
			$document1 = $PHPWord->loadTemplate('../../../template/tag'.$print['item']['tagtemplate'].$tagtype.'.docx');
		}
		else if(isset($print['item']['tagtemplate'])&&file_exists('../../../template/tag'.$print['item']['tagtemplate'].'.docx')){
			$document1 = $PHPWord->loadTemplate('../../../template/tag'.$print['item']['tagtemplate'].'.docx');
		}
		else{
			$document1 = $PHPWord->loadTemplate('../../../template/tag.docx');
		}

		if(strlen($value)>0){
			if(intval($print['item']['tagtemplate'])==7){
				$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="'.$tag[$tagtype]['row1width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row2width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row3width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row4width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row5width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row6width'].'"/><w:gridCol w:w="'.$tag[$tagtype]['row7width'].'"/></w:tblGrid>'.$value.'</w:tbl>';
			}
			else{
				$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>'.$value.'</w:tbl>';
			}
			$document1->setValue('table',$table);
			//$document->save("../../../print/noread/".$filename."_tag_".intval($consecnumber).".docx");
			if(strlen($machine)==0){
				$document1->save("../../../print/read/".intval($no)."_tag".$index."m1_".$filename.".docx");
				if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
					$prt=fopen("../../../print/noread/".intval($no)."_tag".$index."m1_".$filename.".".$machine,'w');
				}
				else{
					$prt=fopen("../../../print/noread/".intval($no)."_tag".$index."m1_".$filename.".prt",'w');
				}
				fclose($prt);
			}
			else{
				$document1->save("../../../print/read/".intval($no)."_tag".$index.$machine."_".$filename.".docx");
				if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
					$prt=fopen("../../../print/noread/".intval($no)."_tag".$index.$machine."_".$filename.".".$machine,'w');
				}
				else{
					$prt=fopen("../../../print/noread/".intval($no)."_tag".$index.$machine."_".$filename.".prt",'w');
				}
				fclose($prt);
			}
		}
		else{
			$table='<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="618"/><w:gridCol w:w="598"/><w:gridCol w:w="428"/></w:tblGrid>'.$value.'</w:tbl>';
			$document1->setValue('table',$table);
			$document1->save("../../../print/read/delete_tag".$index."2_".intval($no).".docx");
		}
	
	}
}
else{
}
if($type=='all'||$type=='tempall'||$type=='kitchen'||$type=='tempkitchen'){
	include_once '../../../tool/dbTool.inc.php';
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	if($type=='tempall'||$type=='tempkitchen'){
		$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'" AND LINENUMBER IN ("'.preg_replace('/,/','","',$_POST['linenumber']).'") ORDER BY LINENUMBER ASC';
		$list=sqlquery($conn,$sql,'sqlite');
		$sql='SELECT TABLENUMBER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'"';
		$tab=sqlquery($conn,$sql,'sqlite');
		$sql='SELECT REMARKS,TAX6,TAX7,TAX8 FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'"';
		$remarks=sqlquery($conn,$sql,'sqlite');
	}
	else if($type=='all'||$type=='kitchen'){
		$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'" AND LINENUMBER IN ("'.preg_replace('/,/','","',$_POST['linenumber']).'") ORDER BY LINENUMBER ASC';
		$list=sqlquery($conn,$sql,'sqlite');
		$sql='SELECT TABLENUMBER FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'"';
		$tab=sqlquery($conn,$sql,'sqlite');
		$sql='SELECT REMARKS,TAX6,TAX7,TAX8 FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$no.'"';
		$remarks=sqlquery($conn,$sql,'sqlite');
	}
	$sql='SELECT saleno FROM salemap WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$no.'"';
	$saleno=sqlquery($conn,$sql,'sqlite');
	if(isset($saleno[0]['saleno'])){
	}
	else{
		$saleno[0]['saleno']='';
	}
	sqlclose($conn,'sqlite');
	$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
	$sql='SELECT inumber,isgroup FROM itemsdata';
	$isgroupset=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$igs=array();
	foreach($isgroupset as $v){
		$igs[intval($v['inumber'])]=intval($v['isgroup']);
	}
	require_once '../../../tool/PHPWord.php';
	$content=parse_ini_file('../../../database/initsetting.ini',true);
	//date_default_timezone_set('Asia/Taipei');
	//date_default_timezone_set($content['init']['settime']);

	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
	$data=parse_ini_file('../../../database/setup.ini',true);
	$print=parse_ini_file('../../../database/printlisttag.ini',true);
	
	$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
	$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
	$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
	$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
	if(file_exists('../../syspram/kitchen-'.$content['init']['firlan'].'.ini')){
		$ininame=parse_ini_file('../../syspram/kitchen-'.$content['init']['firlan'].'.ini',true);
	}
	else{
		$ininame='-1';
	}

	$document1='';
	if(isset($print['item']['kittype'])&&($print['item']['kittype']=='2'||$print['item']['kittype']=='3')){//廚房分類單
		$conarray=array();
		$itemlist=array();
		$tempitemlist=array();
		foreach($pti as $k=>$v){//設定與列印類別數量相同大小之暫存陣列
			$conarray[$k]='';
			$conarray['grouptype'][$k]='';//2020/4/22 列印類別設定為 依列印類別分類 暫存陣列
		}
		$grouptypecontent=0;
		$conarray['-1']='';//系統設定為分類單，但產品無設定列印類別，視為總單之暫存
		$grtitle='';
		$subitem=-1;
		for($i=0;$i<sizeof($list);$i++){
			if($list[$i]['ITEMCODE']=='item'||$list[$i]['ITEMCODE']=='list'||$list[$i]['ITEMCODE']=='autodis'){
				continue;
			}
			else{
				$tt='';
				$tno='';
				$tname='';
				$tnumber='';
				for($tas=1;$tas<=10;$tas++){
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					if($list[$i]['SELECTIVEITEM'.$tas]!=''&&$list[$i]['SELECTIVEITEM'.$tas]!=null){
						$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$tas]);
						for($st=0;$st<sizeof($temptaste);$st++){
							if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
								if(strlen($tt)==0){
									$tno='99999';
									$tname=substr($temptaste[$st],7);
									$tt=$tt.substr($temptaste[$st],7);
								}
								else{
									$tno=$tno.',99999';
									$tname=$tname.','.substr($temptaste[$st],7);
									$tt=$tt.','.substr($temptaste[$st],7);
								}
							}
							else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
								if(strlen($tt)==0){
									$tno=intval(substr($temptaste[$st],0,5));
									$tname=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
									$tnumber=intval(substr($temptaste[$st],5,1));
									$tt=$tt.$taste[intval(substr($temptaste[$st],0,5))]['name1'];
									if(intval(substr($temptaste[$st],5,1))==1){
									}
									else{
										$tt=$tt.'*'.intval(substr($temptaste[$st],5,1));
									}
								}
								else{
									$tno=$tno.','.intval(substr($temptaste[$st],0,5));
									$tname=$tname.','.$taste[intval(substr($temptaste[$st],0,5))]['name1'];
									$tnumber=$tnumber.','.intval(substr($temptaste[$st],5,1));
									$tt=$tt.','.$taste[intval(substr($temptaste[$st],0,5))]['name1'];
									if(intval(substr($temptaste[$st],5,1))==1){
									}
									else{
										$tt=$tt.'*'.intval(substr($temptaste[$st],5,1));
									}
								}
							}
						}
					}
					else{
						break;
					}
					/*if(isset($list[$i]['SELECTIVEITEM'.$tas])&&$list[$i]['SELECTIVEITEM'.$tas]!=''&&$list[$i]['SELECTIVEITEM'.$tas]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$tas])){//手打備註
						if(strlen($tt)==0){
							$tno='99999';
							$tname=substr($list[$i]['SELECTIVEITEM'.$tas],7);
							$tt=$tt.substr($list[$i]['SELECTIVEITEM'.$tas],7);
						}
						else{
							$tno=$tno.',99999';
							$tname=$tname.','.substr($list[$i]['SELECTIVEITEM'.$tas],7);
							$tt=$tt.','.substr($list[$i]['SELECTIVEITEM'.$tas],7);
						}
					}
					else if(isset($list[$i]['SELECTIVEITEM'.$tas])&&$list[$i]['SELECTIVEITEM'.$tas]!=''&&$list[$i]['SELECTIVEITEM'.$tas]!=null){
						if(strlen($tt)==0){
							$tno=intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5));
							$tname=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5))]['name1'];
							$tnumber=intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1));
							$tt=$tt.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5))]['name1'];
							if(intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1))==1){
							}
							else{
								$tt=$tt.'*'.intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1));
							}
						}
						else{
							$tno=$tno.','.intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5));
							$tname=$tname.','.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5))]['name1'];
							$tnumber=$tnumber.','.intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1));
							$tt=$tt.','.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5))]['name1'];
							if(intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1))==1){
							}
							else{
								$tt=$tt.'*'.intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1));
							}
						}
					}
					else{
						break;
					}*/
				}
				/*if(isset($_POST['templistitem'][$i])){//判斷是否為"加點"項目，若是則不印單與不新增至DB
				}
				else{*/
					if($menu[intval($list[$i]['ITEMCODE'])]['printtype']!='' && ($pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['type']=='1' || $pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['type']=='3' || $pti[$menu[intval($list[$i]['ITEMCODE'])]['printtype']]['type']=='5')){//自動彙總(一類一單、一項一單、2020/4/22 依列印類別分類)
						if(isset($tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]) && $itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['no']==intval($list[$i]['ITEMCODE']) && $itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['taste1']==$tt){
							if($igs[intval($list[$i]['ITEMCODE'])]!="0"){
								$grtitle=$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']];
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
							//fwrite($file,'false'.PHP_EOL);
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['number']+=$list[$i]['QTY'];
							
						}
						else{
							////echo sizeof($tempitemlist);
							$index=sizeof($tempitemlist);
							$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]=intval($index);
							if($igs[intval($list[$i]['ITEMCODE'])]!="0"){
								$grtitle=$index;
								$subitem=1;
								$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['order']=$list[0]['LINENUMBER'];
							}
							else{
								if($subitem==-1||(isset($itemlist[$grtitle]['no'])&&intval($subitem)>intval($igs[$itemlist[$grtitle]['no']]))){
									$subitem=-1;
									$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['order']=$list[0]['LINENUMBER'];
								}
								else{
									$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['order']='－';
									$subitem++;
								}
							}
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['no']=intval($list[$i]['ITEMCODE']);
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['name']=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['name2']=$menu[intval($list[$i]['ITEMCODE'])]['name2'];
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['isgroup']=$igs[intval($list[$i]['ITEMCODE'])];
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['mname1']=$list[$i]['UNITPRICELINK'];
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['number']=$list[$i]['QTY'];
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['taste1']=$tno;
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['taste1name']=$tname;
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).','.$tt.','.$list[$i]['UNITPRICELINK']]]['taste1number']=$tnumber;
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
						$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]=intval($index);
						if($igs[intval($list[$i]['ITEMCODE'])]!="0"){
							$grtitle=$index;
							$subitem=1;
							$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['order']=$list[0]['LINENUMBER'];
						}
						else{
							if($subitem==-1||intval($subitem)>intval($igs[$itemlist[$grtitle]['no']])){
								$subitem=-1;
								$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['order']=$list[0]['LINENUMBER'];
							}
							else{
								$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['order']='－';
								$subitem++;
							}
						}
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['no']=intval($list[$i]['ITEMCODE']);
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['name']=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['name2']=$menu[intval($list[$i]['ITEMCODE'])]['name2'];
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['isgroup']=$igs[intval($list[$i]['ITEMCODE'])];
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['mname1']=$list[$i]['UNITPRICELINK'];
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['number']=$list[$i]['QTY'];
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['taste1']=$tno;
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['taste1name']=$tname;
						$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER']][$tno.','.$list[$i]['UNITPRICELINK']]]['taste1number']=$tnumber;
					}
				//}
			}
		}
		$grtitle=-1;
		$grmax=-1;
		$atgroup=array();
		if(isset($remarks)&&sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])&&preg_match('/-/',$remarks[0]['REMARKS'])){
			$tempreserve=preg_split('/;/',substr($remarks[0]['REMARKS'],2));
		}
		else{
		}
		$kitcontent=array();
		for($i=0;$i<sizeof($itemlist);$i++){//for($i=0;$i<sizeof($_POST['no']);$i++){
			//echo $menu[$itemlist[$i]['no']]['printtype'].$itemlist[$i]['name'];
			if($menu[$itemlist[$i]['no']]['printtype']!=''&&($pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='3'||$pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='4')){//一項一單
				/*$PHPWord = new PHPWord();
				if(isset($print['item']['kitchentype'])&&file_exists('../../../template/kitchen'.$print['item']['kitchentype'].'.docx')){
					$document = $PHPWord->loadTemplate('../../../template/kitchen'.$print['item']['kitchentype'].'.docx');
				}
				else{
					$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
				}*/
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

								if($type=='all'||$type=='kitchen'){
									if($ininame!='-1'){
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(".$ininame['name']['reprint'].")".$ininame['name']['listname'];
									}
									else{
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(補)廚房工作單";
									}
								}
								else{
									if($ininame!='-1'){
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(".$ininame['name']['reprinttemp'].")".$ininame['name']['listname'];
									}
									else{
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(補暫)廚房工作單";
									}
								}

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

								break;
							case 'type':

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
								
								if($tab[0]['TABLENUMBER']==''){
									if(isset($tempreserve)){
										if(substr($remarks[0]['REMARKS'],0,1)=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if(substr($remarks[0]['REMARKS'],0,1)=='2'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if(substr($remarks[0]['REMARKS'],0,1)=='3'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
									else{
										if($list[0]['REMARKS']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if($list[0]['REMARKS']=='2'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if($list[0]['REMARKS']=='3'){
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
										if(preg_match('/,/',$tab[0]['TABLENUMBER'])){//併桌
											$splittable=preg_split('/,/',$tab[0]['TABLENUMBER']);
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
											if(preg_match('/-/',$tab[0]['TABLENUMBER'])){//拆桌
												$inittable=preg_split('/-/',$tab[0]['TABLENUMBER']);
												if(isset($tablemap['Tname'][$inittable[0]])){
													$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
												}
												else{
													$tablename .= $tab[0]['TABLENUMBER'];
												}
											}
											else{
												if(isset($tablemap['Tname'][$tab[0]['TABLENUMBER']])){
													$tablename .= $tablemap['Tname'][$tab[0]['TABLENUMBER']];
												}
												else{
													$tablename .= $tab[0]['TABLENUMBER'];
												}
											}
										}
									}
									else{
										$tablename=$tab[0]['TABLENUMBER'];
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
										if($list[0]['REMARKS']=='1'){
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

								break;
							case 'time':

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
								
								date_default_timezone_set($content['init']['settime']);
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $no.' '.$list[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].':'.$_POST['username']."\r\n".date('m/d H:i');
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $no.' '.$list[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."\r\n".date('m/d H:i');
								}

								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

								break;
							case 'numman':
								
								$persontext="";
								if(file_exists('../../../database/floorspend.ini')){
									$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
									if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($remarks[0]['TAX6']!=0||$remarks[0]['TAX7']!=0||$remarks[0]['TAX8']!=0)){
										
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/></w:rPr><w:t>';
										
										if($floorspend['person1']['name']!=''&&$remarks[0]['TAX6']!=0){
											if($persontext!=""){
												$persontext=$persontext.',';
											}
											else{
											}
											$persontext=$persontext.$floorspend['person1']['name'].":".$remarks[0]['TAX6'];
										}
										else{
										}
										if($floorspend['person2']['name']!=''&&$remarks[0]['TAX7']!=0){
											if($persontext!=""){
												$persontext=$persontext.',';
											}
											else{
											}
											$persontext=$persontext.$floorspend['person2']['name'].":".$remarks[0]['TAX7'];
										}
										else{
										}
										if($floorspend['person3']['name']!=''&&$remarks[0]['TAX8']!=0){
											if($persontext!=""){
												$persontext=$persontext.',';
											}
											else{
											}
											$persontext=$persontext.$floorspend['person3']['name'].":".$remarks[0]['TAX8'];
										}
										else{
										}

										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $persontext;

										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
										$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
									}
									else{
									}
								}
								else{
								}

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

					if($type=='all'||$type=='kitchen'){
						if($ininame!='-1'){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(".$ininame['name']['reprint'].")".$ininame['name']['listname'];
						}
						else{
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(補)廚房工作單";
						}
					}
					else{
						if($ininame!='-1'){
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(".$ininame['name']['reprinttemp'].")".$ininame['name']['listname'];
						}
						else{
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "(補暫)廚房工作單";
						}
					}

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					
					//type
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

					if($tab[0]['TABLENUMBER']==''){
						if(isset($tempreserve)){
							if(substr($remarks[0]['REMARKS'],0,1)=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else if(substr($remarks[0]['REMARKS'],0,1)=='2'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else if(substr($remarks[0]['REMARKS'],0,1)=='3'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
						else{
							if($list[0]['REMARKS']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
							else if($list[0]['REMARKS']=='2'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
							else if($list[0]['REMARKS']=='3'){
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
							if(preg_match('/,/',$tab[0]['TABLENUMBER'])){//併桌
								$splittable=preg_split('/,/',$tab[0]['TABLENUMBER']);
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
								if(preg_match('/-/',$tab[0]['TABLENUMBER'])){//拆桌
									$inittable=preg_split('/-/',$tab[0]['TABLENUMBER']);
									if(isset($tablemap['Tname'][$inittable[0]])){
										$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
									}
									else{
										$tablename .= $tab[0]['TABLENUMBER'];
									}
								}
								else{
									if(isset($tablemap['Tname'][$tab[0]['TABLENUMBER']])){
										$tablename .= $tablemap['Tname'][$tab[0]['TABLENUMBER']];
									}
									else{
										$tablename .= $tab[0]['TABLENUMBER'];
									}
								}
							}
						}
						else{
							$tablename=$tab[0]['TABLENUMBER'];
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
							if($list[0]['REMARKS']=='1'){
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
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
					
					date_default_timezone_set($content['init']['settime']);
					if($ininame!='-1'){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $no.' '.$list[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].':'.$_POST['username']."\r\n".date('m/d H:i');
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $no.' '.$list[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."\r\n".date('m/d H:i');
					}

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';

					/*預設不顯示人數*/
				}
				
				$tindex=0;

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "Items";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "QTY";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
				$sum=0;
				if($itemlist[$i]['order']=='－'){
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
					$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
					$linetaste='';
					for($t=0;$t<sizeof($temp);$t++){
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
							$tt=preg_split('/\//',$temp[$t]);

							if(isset($tt[1])){
								if(intval($temp2[$t])>1){
									$tt[0]=$tt[0].'*'.$temp2[$t];
								}
								else{
								}
							}
							else{
							}

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
							$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '-'.$_POST['taste1'][$t];
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '';
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					else{
					}
				}
				else{
				}
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
			}
			else if($pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='1'||$pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='2'){//一類一單
				if($menu[$itemlist[$i]['no']]['printtype']==''){
					if($itemlist[$i]['order']=='－'){
						if(in_array('-1',$atgroup)){
						}
						else{
							$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
						$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
						$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
						$temp=preg_split("/,/",$itemlist[$i]['taste1name']);
						$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
						$linetaste='';
						for($t=0;$t<sizeof($temp);$t++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								$tt=preg_split('/\//',$temp[$t]);

								if(isset($tt[1])){
									if(intval($temp2[$t])>1){
										$tt[0]=$tt[0].'*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}
								
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
								$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
							$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
				}
				else{
					if($itemlist[$i]['order']=='－'){
						if(in_array($menu[$itemlist[$i]['no']]['printtype'],$atgroup)){
						}
						else{
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
						$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
						$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
						$linetaste='';
						for($t=0;$t<sizeof($temp);$t++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								$tt=preg_split('/\//',$temp[$t]);

								if(isset($tt[1])){
									if(intval($temp2[$t])>1){
										$tt[0]=$tt[0].'*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}

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
								$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
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
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						}
						else{
							//備註一項一行
						}
					}
					else{
					}
				}
			}
			else{//if($pti[$menu[$itemlist['no'][$i]]['printtype']]['type']=='5'||$pti[$menu[$itemlist['no'][$i]]['printtype']]['type']=='6'){//2020/4/22 依列印類別分類
				if($menu[$itemlist[$i]['no']]['printtype']==''){
					if($itemlist[$i]['order']=='－'){
						if(in_array('-1',$atgroup)){
						}
						else{
							$conarray['grouptype']['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
							if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$conarray['grouptype']['-1'] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
							}
							else{
								$conarray['grouptype']['-1'] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2']!=''){
								if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
									$conarray['grouptype']['-1'] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'];
								}
								else{
									for($mname=1;$mname<=$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mnumber'];$mname++){
										if($itemlist[$itemlist[$i]['grtitle']]['mname1']==$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'1']){
											$conarray['grouptype']['-1'] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'].'('.$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'2'].')';
										}
										else{
										}
									}
								}
							}
							else{
							}
							$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
							//$conarray['grouptype']['-1'] .= $itemlist[$i]['number'];
							$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype']['-1'] .= "</w:tr>";
							array_push($atgroup,'-1');
						}
						$conarray['grouptype']['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['grouptype']['-1'] .= '－'.$itemlist[$i]['name'];
						}
						else{
							$conarray['grouptype']['-1'] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['grouptype']['-1'] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
									if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
										$conarray['grouptype']['-1'] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						$conarray['grouptype']['-1'] .= $itemlist[$i]['number'];
						$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['grouptype']['-1'] .= "</w:tr>";
					}
					else{
						$conarray['grouptype']['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['grouptype']['-1'] .= $itemlist[$i]['name'];
						}
						else{
							$conarray['grouptype']['-1'] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['grouptype']['-1'] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
									if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
										$conarray['grouptype']['-1'] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						$conarray['grouptype']['-1'] .= $itemlist[$i]['number'];
						$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['grouptype']['-1'] .= "</w:tr>";
					}
					
					if(strlen($itemlist[$i]['taste1'])>0){
						$temptasteno=preg_split('/,/',$itemlist[$i]['taste1']);
						$temp=preg_split("/,/",$itemlist[$i]['taste1name']);
						$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
						$linetaste='';
						for($t=0;$t<sizeof($temp);$t++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								$tt=preg_split('/\//',$temp[$t]);

								if(isset($tt[1])){
									if(intval($temp2[$t])>1){
										$tt[0]=$tt[0].'*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}
								
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
								$conarray['grouptype']['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
								//$table .= '-'.$_POST['taste1'][$t];
								$tt=preg_split('/\//',$temp[$t]);
								$conarray['grouptype']['-1'] .= '　+'.$tt[0];
								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
									$conarray['grouptype']['-1'] .= '/ '.$taste[$temptasteno[$t]]['name2'];
								}
								else{
								}
								$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$conarray['grouptype']['-1'] .= '';
								$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['grouptype']['-1'] .= "</w:tr>";
							}
						}
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
							$conarray['grouptype']['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$conarray['grouptype']['-1'] .= $linetaste;
							$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype']['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray['grouptype']['-1'] .= '';
							$conarray['grouptype']['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype']['-1'] .= "</w:tr>";
						}
						else{//備註一項一行
						}
					}
					else{
					}
				}
				else{
					if($itemlist[$i]['order']=='－'){
						if(in_array($menu[$itemlist[$i]['no']]['printtype'],$atgroup)){
						}
						else{
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
							if(!isset($itemlist[$itemlist[$i]['grtitle']])||strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'];
							}
							else{
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
							}
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2']!=''){
								if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'];
								}
								else{
									for($mname=1;$mname<=$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mnumber'];$mname++){
										if($itemlist[$itemlist[$i]['grtitle']]['mname1']==$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'1']){
											$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'].'('.$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'2'].')';
										}
										else{
										}
									}
								}
							}
							else{
							}
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
							//$conarray['grouptype']['-1'] .= $itemlist[$i]['number'];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							array_push($atgroup,$menu[$itemlist[$i]['no']]['printtype']);
						}
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'];
						}
						else{
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
									if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					else{
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						if(strlen($itemlist[$i]['mname1'])==''){
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
						}
						else{
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
						}
						if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
							if(strlen($itemlist[$i]['mname1'])==''){
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
									if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
										$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "\r\n".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
						$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					}
					
					if(strlen($itemlist[$i]['taste1'])>0){
						$temptasteno=preg_split('/,/',$itemlist[$i]['taste1']);
						$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
						$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
						$linetaste='';
						for($t=0;$t<sizeof($temp);$t++){
							if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
								$tt=preg_split('/\//',$temp[$t]);

								if(isset($tt[1])){
									if(intval($temp2[$t])>1){
										$tt[0]=$tt[0].'*'.$temp2[$t];
									}
									else{
									}
								}
								else{
								}

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
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
								//$table .= '-'.$_POST['taste1'][$t];
								$tt=preg_split('/\//',$temp[$t]);
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
									$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$taste[$temptasteno[$t]]['name2'];
								}
								else{
								}
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '';
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
								$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
							}
						}
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= $linetaste;
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= '';
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['grouptype'][$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						}
						else{
							//備註一項一行
						}
					}
					else{
					}
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
				date_default_timezone_set($content['init']['settime']);
				$filename=date("YmdHis");
				if($pti[$printindex]['kitchen'.substr($_POST['listtype'],0,1)]=='1'){
					//$document->save("../../../print/noread/".$filename."_list".$menu[$itemlist[$i]['no']]['printtype']."_".intval($no)."_".$i.".docx");
					if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
						$document->save("../../../print/read/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1).$printindex."_".$filename.".".$_POST['machinetype']);
					}
					else{
						$document->save("../../../print/read/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1).$printindex."_".$filename.".docx");
					}
					$prt=fopen("../../../print/noread/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1).$printindex."_".$filename.".prt",'w');
					fclose($prt);
				}
				else{
					$document->save("../../../print/read/delete_list".$printindex."_".intval($no).".docx");
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

								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

								if($type=='all'||$type=='kitchen'){
									if($ininame!='-1'){
										$table .= "(".$ininame['name']['reprint'].")".$ininame['name']['listname'];
									}
									else{
										$table .= "(補)廚房工作單";
									}
								}
								else{
									if($ininame!='-1'){
										$table .= "(".$ininame['name']['reprinttemp'].")".$ininame['name']['listname'];
									}
									else{
										$table .= "(補暫)廚房工作單";
									}
								}

								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";

								break;
							case 'type':

								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
								
								if($tab[0]['TABLENUMBER']==''){
									if($list[0]['REMARKS']=='1'){
										if($k=='-1' || ($k!='0'&&$k=='grouptype')){
											$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
									else if($list[0]['REMARKS']=='2'){
										if($k=='-1' || ($k!='0'&&$k=='grouptype')){
											$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
									else if($list[0]['REMARKS']=='3'){
										if($k=='-1' || ($k!='0'&&$k=='grouptype')){
											$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
									else{
										if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
										if(preg_match('/,/',$tab[0]['TABLENUMBER'])){//併桌
											$splittable=preg_split('/,/',$tab[0]['TABLENUMBER']);
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
											if(preg_match('/-/',$tab[0]['TABLENUMBER'])){//拆桌
												$inittable=preg_split('/-/',$tab[0]['TABLENUMBER']);
												if(isset($tablemap['Tname'][$inittable[0]])){
													$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
												}
												else{
													$tablename .= $tab[0]['TABLENUMBER'];
												}
											}
											else{
												if(isset($tablemap['Tname'][$tab[0]['TABLENUMBER']])){
													$tablename .= $tablemap['Tname'][$tab[0]['TABLENUMBER']];
												}
												else{
													$tablename .= $tab[0]['TABLENUMBER'];
												}
											}
										}
									}
									else{
										$tablename=$tab[0]['TABLENUMBER'];
									}
									if($list[0]['REMARKS']=='1'){
										if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
									else if($list[0]['REMARKS']=='2'){
										if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
									else if($list[0]['REMARKS']=='3'){
										if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
										if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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

								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
								
								date_default_timezone_set($content['init']['settime']);
								if($ininame!='-1'){
									$table .= $no.' '.$list[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].':'.$_POST['username']."\r\n".date('m/d H:i');
								}
								else{
									$table .= $no.' '.$list[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."\r\n".date('m/d H:i');
								}

								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";

								break;
							case 'numman':
								
								$persontext="";
								if(file_exists('../../../database/floorspend.ini')){
									$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
									if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($remarks[0]['TAX6']!=0||$remarks[0]['TAX7']!=0||$remarks[0]['TAX8']!=0)){
										
										$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
										$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/></w:rPr><w:t>';
										
										if($floorspend['person1']['name']!=''&&$remarks[0]['TAX6']!=0){
											if($persontext!=""){
												$persontext=$persontext.',';
											}
											else{
											}
											$persontext=$persontext.$floorspend['person1']['name'].":".$remarks[0]['TAX6'];
										}
										else{
										}
										if($floorspend['person2']['name']!=''&&$remarks[0]['TAX7']!=0){
											if($persontext!=""){
												$persontext=$persontext.',';
											}
											else{
											}
											$persontext=$persontext.$floorspend['person2']['name'].":".$remarks[0]['TAX7'];
										}
										else{
										}
										if($floorspend['person3']['name']!=''&&$remarks[0]['TAX8']!=0){
											if($persontext!=""){
												$persontext=$persontext.',';
											}
											else{
											}
											$persontext=$persontext.$floorspend['person3']['name'].":".$remarks[0]['TAX8'];
										}
										else{
										}

										$table .= $persontext;

										$table .= "</w:t></w:r></w:p></w:tc>";
										$table .= "</w:tr>";

									}
									else{
									}
								}
								else{
								}

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

					if($type=='all'||$type=='kitchen'){
						if($ininame!='-1'){
							$table .= "(".$ininame['name']['reprint'].")".$ininame['name']['listname'];
						}
						else{
							$table .= "(補)廚房工作單";
						}
					}
					else{
						if($ininame!='-1'){
							$table .= "(".$ininame['name']['reprinttemp'].")".$ininame['name']['listname'];
						}
						else{
							$table .= "(補暫)廚房工作單";
						}
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
					
					//type
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

					if($tab[0]['TABLENUMBER']==''){
						if($list[0]['REMARKS']=='1'){
							if($k=='-1' || ($k!='0'&&$k=='grouptype')){
								$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
						else if($list[0]['REMARKS']=='2'){
							if($k=='-1' || ($k!='0'&&$k=='grouptype')){
								$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
						else if($list[0]['REMARKS']=='3'){
							if($k=='-1' || ($k!='0'&&$k=='grouptype')){
								$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
						else{
							if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
							if(preg_match('/,/',$tab[0]['TABLENUMBER'])){//併桌
								$splittable=preg_split('/,/',$tab[0]['TABLENUMBER']);
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
								if(preg_match('/-/',$tab[0]['TABLENUMBER'])){//拆桌
									$inittable=preg_split('/-/',$tab[0]['TABLENUMBER']);
									if(isset($tablemap['Tname'][$inittable[0]])){
										$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
									}
									else{
										$tablename .= $tab[0]['TABLENUMBER'];
									}
								}
								else{
									if(isset($tablemap['Tname'][$tab[0]['TABLENUMBER']])){
										$tablename .= $tablemap['Tname'][$tab[0]['TABLENUMBER']];
									}
									else{
										$tablename .= $tab[0]['TABLENUMBER'];
									}
								}
							}
						}
						else{
							$tablename=$tab[0]['TABLENUMBER'];
						}
						if($list[0]['REMARKS']=='1'){
							if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
						else if($list[0]['REMARKS']=='2'){
							if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
						else if($list[0]['REMARKS']=='3'){
							if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
							if($k=='-1' || ($k!='0'&&$k=='grouptype')){
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
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
					
					date_default_timezone_set($content['init']['settime']);
					if($ininame!='-1'){
						$table .= $no.' '.$list[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].':'.$_POST['username']."\r\n".date('m/d H:i');
					}
					else{
						$table .= $no.' '.$list[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."\r\n".date('m/d H:i');
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					/*預設不顯示人數*/

					$table .= '</w:tbl>';
				}

				$tindex=0;
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "Items";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "QTY";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$sum=0;
				if(!is_numeric($k)&&($k!='0'&&$k=='grouptype')){
					foreach($v as $pt=>$pv){
						if($pv!=''){
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						
							$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

							//$table .= '-'.$_POST['taste1'][$t];
							$table .= $pti[$pt]['name'];
							$table .= "</w:t></w:r></w:p></w:tc>";
							
							$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";

							$table .= $pv;

							$grouptypecontent++;
						}
						else{
						}
					}
				}
				else{
					$table .= $v;
				}
				$table .= '</w:tbl>';
		
				$document->setValue('item',$table);
				//$document->setValue('total','NT.'.$_POST['total']);
				date_default_timezone_set($content['init']['settime']);
				$filename=date("YmdHis");
				if($k=='-1'){
					//$document->save("../../../print/noread/".$filename."_listN_".intval($no).".docx");
					if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
						$document->save("../../../print/read/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1)."N_".$filename.".".$_POST['machinetype']);
					}
					else{
						$document->save("../../../print/read/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1)."N_".$filename.".docx");
					}
					$prt=fopen("../../../print/noread/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1)."N_".$filename.".prt",'w');
					fclose($prt);
				}
				else{
					if((($k!='0'&&$k=='grouptype')&&$grouptypecontent!=0) || (isset($pti[$k])&&$pti[$k]['kitchen'.$_POST['listtype']]=='1')){
						//$document->save("../../../print/noread/".$filename."_list".$k."_".intval($no).".docx");
						if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
							$document->save("../../../print/read/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1).$k."_".$filename.".".$_POST['machinetype']);
						}
						else{
							$document->save("../../../print/read/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1).$k."_".$filename.".docx");
						}
						$prt=fopen("../../../print/noread/".intval($no)."_list".substr($remarks[0]['REMARKS'],0,1).$k."_".$filename.".prt",'w');
						fclose($prt);
					}
					else{
						$document->save("../../../print/read/delete_list".$k."_".intval($no).".docx");
					}
				}
			}
		}
	}
	else{
	}
	if(isset($print['item']['kittype'])&&($print['item']['kittype']=='1'||$print['item']['kittype']=='3')){//廚房總單
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
						
						if($type=='all'||$type=='kitchen'){
							if($ininame!='-1'){
								$table .= '('.$ininame['name']['reprint'].')'.$ininame['name']['alllistname'];
							}
							else{
								$table .= '(補)控餐總單';
							}
						}
						else{
							if($ininame!='-1'){
								$table .= '('.$ininame['name']['reprinttemp'].')'.$ininame['name']['alllistname'];
							}
							else{
								$table .= '(補暫)控餐總單';
							}
						}

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						break;
					case 'type':
						//print_r($list);
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
						
						if($tab[0]['TABLENUMBER']==''){
						}
						else{
							$tablename='';
							if(isset($content['init']['controltable'])&&$content['init']['controltable']=='1'){//2020/3/23 開啟桌控
								if(file_exists('../../../database/floorspend.ini')){
									$tablemap=parse_ini_file('../../../database/floorspend.ini',true);
								}
								else{
								}
								if(preg_match('/,/',$tab[0]['TABLENUMBER'])){//併桌
									$splittable=preg_split('/,/',$tab[0]['TABLENUMBER']);
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
									if(preg_match('/-/',$tab[0]['TABLENUMBER'])){//拆桌
										$inittable=preg_split('/-/',$tab[0]['TABLENUMBER']);
										if(isset($tablemap['Tname'][$inittable[0]])){
											$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
										}
										else{
											$tablename .= $tab[0]['TABLENUMBER'];
										}
									}
									else{
										if(isset($tablemap['Tname'][$tab[0]['TABLENUMBER']])){
											$tablename .= $tablemap['Tname'][$tab[0]['TABLENUMBER']];
										}
										else{
											$tablename .= $tab[0]['TABLENUMBER'];
										}
									}
								}
							}
							else{
								$tablename=$tab[0]['TABLENUMBER'];
							}
						}
						if($list[0]['REMARKS']=='1'){
							if($tab[0]['TABLENUMBER']==''){
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
						else if($list[0]['REMARKS']=='2'){
							if($tab[0]['TABLENUMBER']==''){
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
						else if($list[0]['REMARKS']=='3'){
							if($tab[0]['TABLENUMBER']==''){
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
							if($tab[0]['TABLENUMBER']==''){
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

						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
						
						date_default_timezone_set($content['init']['settime']);
						if($ininame!='-1'){
							$table .= $no.' '.$list[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].':'.$_POST['username']."\r\n".date('m/d H:i');
						}
						else{
							$table .= $no.' '.$list[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."\r\n".date('m/d H:i');
						}

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						break;
					case 'numman':
						
						$persontext="";
						if(file_exists('../../../database/floorspend.ini')){
							$floorspend=parse_ini_file('../../../database/floorspend.ini',true);
							if(($floorspend['person1']['name']!=''||$floorspend['person2']['name']!=''||$floorspend['person3']['name']!='')&&($remarks[0]['TAX6']!=0||$remarks[0]['TAX7']!=0||$remarks[0]['TAX8']!=0)){
								
								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['nummanfontsize'])*2).'"/></w:rPr><w:t>';
								
								if($floorspend['person1']['name']!=''&&$remarks[0]['TAX6']!=0){
									if($persontext!=""){
										$persontext=$persontext.',';
									}
									else{
									}
									$persontext=$persontext.$floorspend['person1']['name'].":".$remarks[0]['TAX6'];
								}
								else{
								}
								if($floorspend['person2']['name']!=''&&$remarks[0]['TAX7']!=0){
									if($persontext!=""){
										$persontext=$persontext.',';
									}
									else{
									}
									$persontext=$persontext.$floorspend['person2']['name'].":".$remarks[0]['TAX7'];
								}
								else{
								}
								if($floorspend['person3']['name']!=''&&$remarks[0]['TAX8']!=0){
									if($persontext!=""){
										$persontext=$persontext.',';
									}
									else{
									}
									$persontext=$persontext.$floorspend['person3']['name'].":".$remarks[0]['TAX8'];
								}
								else{
								}

								$table .= $persontext;

								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";

							}
							else{
							}
						}
						else{
						}

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

			if($type=='all'||$type=='kitchen'){
				if($ininame!='-1'){
					$table .= '('.$ininame['name']['reprint'].')'.$ininame['name']['alllistname'];
				}
				else{
					$table .= '(補)控餐總單';
				}
			}
			else{
				if($ininame!='-1'){
					$table .= '('.$ininame['name']['reprinttemp'].')'.$ininame['name']['alllistname'];
				}
				else{
					$table .= '(補暫)控餐總單';
				}
			}

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			
			//type
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';
			
			if($tab[0]['TABLENUMBER']==''){
			}
			else{
				$tablename='';
				if(isset($content['init']['controltable'])&&$content['init']['controltable']=='1'){//2020/3/23 開啟桌控
					if(file_exists('../../../database/floorspend.ini')){
						$tablemap=parse_ini_file('../../../database/floorspend.ini',true);
					}
					else{
					}
					if(preg_match('/,/',$tab[0]['TABLENUMBER'])){//併桌
						$splittable=preg_split('/,/',$tab[0]['TABLENUMBER']);
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
						if(preg_match('/-/',$tab[0]['TABLENUMBER'])){//拆桌
							$inittable=preg_split('/-/',$tab[0]['TABLENUMBER']);
							if(isset($tablemap['Tname'][$inittable[0]])){
								$tablename .= $tablemap['Tname'][$inittable[0]].'-'.$inittable[1];
							}
							else{
								$tablename .= $tab[0]['TABLENUMBER'];
							}
						}
						else{
							if(isset($tablemap['Tname'][$tab[0]['TABLENUMBER']])){
								$tablename .= $tablemap['Tname'][$tab[0]['TABLENUMBER']];
							}
							else{
								$tablename .= $tab[0]['TABLENUMBER'];
							}
						}
					}
				}
				else{
					$tablename=$tab[0]['TABLENUMBER'];
				}
			}
			if($list[0]['REMARKS']=='1'){
				if($tab[0]['TABLENUMBER']==''){
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
			else if($list[0]['REMARKS']=='2'){
				if($tab[0]['TABLENUMBER']==''){
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
			else if($list[0]['REMARKS']=='3'){
				if($tab[0]['TABLENUMBER']==''){
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
				if($tab[0]['TABLENUMBER']==''){
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
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
			
			date_default_timezone_set($content['init']['settime']);
			if($ininame!='-1'){
				$table .= $no.' '.$list[0]['CLKNAME']."\r\n".$ininame['name']['reorderman'].':'.$_POST['username']."\r\n".date('m/d H:i');
			}
			else{
				$table .= $no.' '.$list[0]['CLKNAME']."\r\n補印人員:".$_POST['username']."\r\n".date('m/d H:i');
			}

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";

			/*預設不顯示人數*/

			$table .= '</w:tbl>';
		}
		//$document->setValue('consecnumber',intval($no));
		//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
		//$document->setValue('tel', '(04)2473-2003');
		//$document->setValue('time', date('Y/m/s H:i:s'));
		//if($tab[0]['TABLENUMBER']==''){
			/*if($ininame!='-1'){
				$document->setValue('story','('.$ininame['name']['reprint'].')'.$ininame['name']['alllistname']);
			}
			else{
				$document->setValue('story','(補)控餐總單');
			}*/
		/*}
		else{
			if($ininame!='-1'){
				$document->setValue('story',"(".$ininame['name']['reprint'].")".$ininame['name']['alllistname']."\r\n".$tab[0]['TABLENUMBER'].$ininame['name']['table']);
			}
			else{
				$document->setValue('story',"(補)控餐總單\r\n".$tab[0]['TABLENUMBER'].'號桌');
			}
		}*/
		//$document->setValue('time',date('m/d H:i'));
		$tindex=0;
		
		$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= "Items";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= "QTY";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		$sum=0;
		$temporderlist=1;
		////echo sizeof($_POST['no']);
		$itemlist=array();
		$tempitemlist=array();
		$grtitle=-1;
		$subitem=0;
		for($i=0;$i<sizeof($list);$i++){
			$tt='';
			$tno='';
			$tname='';
			$tnumber='';
			for($tas=1;$tas<=10;$tas++){
				//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
				if($list[$i]['SELECTIVEITEM'.$tas]!=''&&$list[$i]['SELECTIVEITEM'.$tas]!=null){
					$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$tas]);
					for($st=0;$st<sizeof($temptaste);$st++){
						if(isset($temptaste[$st])&&preg_match('/99999/',$temptaste[$st])){//手打備註
							if(strlen($tt)==0){
								$tno='99999';
								$tname=substr($temptaste[$st],7);
								$tt=$tt.substr($temptaste[$st],7);
							}
							else{
								$tno=$tno.',99999';
								$tname=$tname.','.substr($temptaste[$st],7);
								$tt=$tt.','.substr($temptaste[$st],7);
							}
						}
						else if(isset($temptaste[$st])&&$temptaste[$st]!=''){
							if(strlen($tt)==0){
								$tno=intval(substr($temptaste[$st],0,5));
								$tname=$taste[intval(substr($temptaste[$st],0,5))]['name1'];
								$tnumber=intval(substr($temptaste[$st],5,1));
								$tt=$tt.$taste[intval(substr($temptaste[$st],0,5))]['name1'];
								if(intval(substr($temptaste[$st],5,1))==1){
								}
								else{
									$tt=$tt.'*'.intval(substr($temptaste[$st],5,1));
								}
							}
							else{
								$tno=$tno.','.intval(substr($temptaste[$st],0,5));
								$tname=$tname.','.$taste[intval(substr($temptaste[$st],0,5))]['name1'];
								$tnumber=$tnumber.','.intval(substr($temptaste[$st],5,1));
								$tt=$tt.','.$taste[intval(substr($temptaste[$st],0,5))]['name1'];
								if(intval(substr($temptaste[$st],5,1))==1){
								}
								else{
									$tt=$tt.'*'.intval(substr($temptaste[$st],5,1));
								}
							}
						}
					}
				}
				else{
					break;
				}
				/*if(isset($list[$i]['SELECTIVEITEM'.$tas])&&$list[$i]['SELECTIVEITEM'.$tas]!=''&&$list[$i]['SELECTIVEITEM'.$tas]!=null&&preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$tas])){//手打備註
					if(strlen($tt)==0){
						$tno='99999';
						$tname=substr($list[$i]['SELECTIVEITEM'.$tas],7);
						$tt=$tt.substr($list[$i]['SELECTIVEITEM'.$tas],7);
					}
					else{
						$tno=$tno.',99999';
						$tname=$tname.','.substr($list[$i]['SELECTIVEITEM'.$tas],7);
						$tt=$tt.','.substr($list[$i]['SELECTIVEITEM'.$tas],7);
					}
				}
				else if(isset($list[$i]['SELECTIVEITEM'.$tas])&&$list[$i]['SELECTIVEITEM'.$tas]!=''&&$list[$i]['SELECTIVEITEM'.$tas]!=null){
					if(strlen($tt)==0){
						$tno=intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5));
						$tname=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5))]['name1'];
						$tnumber=intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1));
						$tt=$tt.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5))]['name1'];
						if(intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1))==1){
						}
						else{
							$tt=$tt.'*'.intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1));
						}
					}
					else{
						$tno=$tno.','.intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5));
						$tname=$tname.','.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5))]['name1'];
						$tnumber=$tnumber.','.intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1));
						$tt=$tt.','.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$tas],0,5))]['name1'];
						if(intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1))==1){
						}
						else{
							$tt=$tt.'*'.intval(substr($list[$i]['SELECTIVEITEM'.$tas],5,1));
						}
					}
				}
				else{
					break;
				}*/
			}
			$index=sizeof($tempitemlist);
			$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]=intval($index);
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['no']=intval($list[$i]['ITEMCODE']);
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['name']=$menu[intval($list[$i]['ITEMCODE'])]['name1'];
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['name2']=$menu[intval($list[$i]['ITEMCODE'])]['name2'];
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['isgroup']=$igs[intval($list[$i]['ITEMCODE'])];
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['mname1']=$list[$i]['UNITPRICELINK'];
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['number']=$list[$i]['QTY'];
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['taste1']=$tno;
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['taste1name']=$tname;
			$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['taste1number']=$tnumber;
			
			//print_r($tempitemlist);
			if(isset($igs[intval($list[$i]['ITEMCODE'])])&&$igs[intval($list[$i]['ITEMCODE'])]!="0"){
				$grtitle=$index;
				$subitem=1;
				$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
				$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['order']=$list[$i]['LINENUMBER'];
			}
			else{
				$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
				if($subitem==0||$grtitle==-1||intval($subitem)>$igs[intval($itemlist[$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['grtitle']]['no'])]){
					$subitem=0;
					$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['order']=$list[$i]['LINENUMBER'];
				}
				else{
					$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['order']='－';
					$subitem++;
				}
			}
			
			//$itemlist[$tempitemlist[intval($list[$i]['ITEMCODE']).'-'.$list[$i]['LINENUMBER'].','.$list[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
		}
		$groupcode=-1;
		//print_r($itemlist);
		for($i=0;$i<sizeof($itemlist);$i++){
			/*if($list[$i]['ITEMCODE']=='item'||$list[$i]['ITEMCODE']=='list'){
				continue;
			}
			else{*/
				$temporderlist=0;
				if($itemlist[$i]['order']=='－'){
					if($groupcode==-1||$groupcode!=$itemlist[$i]['no']){
						$groupcode=$itemlist[$i]['grtitle'];
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						if(isset($print['kitchen']['grouptitlesize'])){
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['grouptitlesize'].'"/><w:szCs w:val="'.$print['kitchen']['grouptitlesize'].'"/></w:rPr><w:t>';
						}
						else{
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						}
						if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==0){
							$table .= $itemlist[$itemlist[$i]['grtitle']]['name'];
						}
						else{
							$table .= $itemlist[$itemlist[$i]['grtitle']]['name'].'('.$itemlist[$itemlist[$i]['grtitle']]['mname1'].')';
						}
						if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2']!=''){
							if(strlen($itemlist[$itemlist[$i]['grtitle']]['mname1'])==''){
								$table .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'];
							}
							else{
								for($mname=1;$mname<=$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mnumber'];$mname++){
									if($itemlist[$itemlist[$i]['grtitle']]['mname1']==$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'1']){
										$table .= "\r\n".$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['name2'].'('.$menu[$itemlist[$itemlist[$i]['grtitle']]['no']]['mname'.$mname.'2'].')';
									}
									else{
									}
								}
							}
						}
						else{
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
						//$conarray['-1'] .= $itemlist[$i]['number'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
						array_push($atgroup,$itemlist[$i]['grtitle']);
					}
					else{
					}
				}
				else{
				}

				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/>';
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
				if(strlen($itemlist[$i]['mname1'])==0){
					if($itemlist[$i]['order']=='－'){
						$table .= '－'.$itemlist[$i]['name'];
					}
					else{
						$table .= $itemlist[$i]['name'];
					}
					if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
						if($itemlist[$i]['order']=='－'){
							$table .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'];
						}
						else{
							$table .= "\r\n".$menu[$itemlist[$i]['no']]['name2'];
						}
					}
					else{
					}
				}
				else{
					if($itemlist[$i]['order']=='－'){
						$table .= '－'.$itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					}
					else{
						$table .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					}
					if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$menu[$itemlist[$i]['no']]['name2']!=''){
						if($itemlist[$i]['order']=='－'){
							for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
								if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
									$table .= "\r\n－".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
								}
								else{
								}
							}
						}
						else{
							for($mname=1;$mname<=$menu[$itemlist[$i]['no']]['mnumber'];$mname++){
								if($itemlist[$i]['mname1']==$menu[$itemlist[$i]['no']]['mname'.$mname.'1']){
									$table .= "\r\n".$menu[$itemlist[$i]['no']]['name2'].'('.$menu[$itemlist[$i]['no']]['mname'.$mname.'2'].')';
								}
								else{
								}
							}
						}
					}
					else{
					}
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/>';
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
				$table .= $itemlist[$i]['number'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				
				if(strlen($itemlist[$i]['taste1'])>0){
					$temptasteno=preg_split('/,/',$itemlist[$i]['taste1']);
					$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
					$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
					for($t=0;$t<sizeof($temp);$t++){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:val="20"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/>';
						if($t==(sizeof($temp)-1)){
							//$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
						}
						else{
						}
						$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['kitchen']['kitchensize'].'"/><w:szCs w:val="'.$print['kitchen']['kitchensize'].'"/></w:rPr><w:t>';
						//$table .= '-'.$_POST['taste1'][$t];
						$tt=preg_split('/\//',$temp[$t]);

						if(isset($tt[1])){
							if(intval($temp2[$t])>1){
								$tt[0]=$tt[0].'*'.$temp2[$t];
							}
							else{
							}
						}
						else{
						}

						$table .= '　+'.$tt[0];
						if(isset($print['kitchen']['listsecname'])&&$print['kitchen']['listsecname']=='1'&&$taste[$temptasteno[$t]]['name2']!=''){
							$table .= '/ '.$taste[$temptasteno[$t]]['name2'];
						}
						else{
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/>';
						if($t==(sizeof($temp)-1)){
							//$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
						}
						else{
						}
						$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$table .= '';
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
				}
				else{
				}
			//}
		}
		$table .= "</w:tbl>";
		
		$document->setValue('item',$table);
		//$document->setValue('total','NT.'.$_POST['total']);
		date_default_timezone_set($content['init']['settime']);
		$filename=date("YmdHis");
		//$document->save("../../../print/noread/".$filename."_list_".intval($no).".docx");
		if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
			$document->save("../../../print/read/".intval($no)."_list-_".$filename.".".$_POST['machinetype']);
		}
		else{
			$document->save("../../../print/read/".intval($no)."_list-_".$filename.".docx");
		}
		$prt=fopen("../../../print/noread/".intval($no)."_list-_".$filename.".prt",'w');
		fclose($prt);
	}
	else{
	}
}
else{
}
?>