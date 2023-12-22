<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
require_once '../../../tool/PHPWord.php';
//print_r($_POST);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$data=parse_ini_file('../../../database/setup.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
$content=parse_ini_file('../../../database/initsetting.ini',true);
$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
$tastemap=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
date_default_timezone_set($content['init']['settime']);

if(isset($print['item']['kitchensize'])){
}
else{
	$print['item']['kitchensize']=28;
}
if(file_exists('../../syspram/kitchen-'.$content['init']['firlan'].'.ini')){
	$ininame=parse_ini_file('../../syspram/kitchen-'.$content['init']['firlan'].'.ini',true);
}
else{
	//$ininame='-1';
}
if(file_exists('../../syspram/clientlist-'.$content['init']['firlan'].'.ini')){
	$clientname=parse_ini_file('../../syspram/clientlist-'.$content['init']['firlan'].'.ini',true);
}
else{
	//$ininame='-1';
}
if(isset($content['init']['posdvr'])&&$content['init']['posdvr']=='1'){
	date_default_timezone_set($content['init']['settime']);
	$tempposdvr=date('YmdHis');
	$posdvr=fopen('../../../print/posdvr/'.$tempposdvr.';'.$_POST['terminalnumber'].'.txt','w');
	$tempdvrcontent='';
	if(file_exists('../../syspram/clientlist-'.$content['init']['firlan'].'.ini')){
		$list=parse_ini_file('../../syspram/clientlist-'.$content['init']['firlan'].'.ini',true);
	}
	else if(file_exists('../../syspram/clientlist-1.ini')){
		$list=parse_ini_file('../../syspram/clientlist-1.ini',true);
	}
	else if(file_exists('../../syspram/clientlist-TW.ini')){
		$list=parse_ini_file('../../syspram/clientlist-TW.ini',true);
	}
	else{
		$list='-1';
	}
}
else{
}
if(isset($content['init']['kvm'])&&$content['init']['kvm']=='1'){
	if(file_exists('../../../print/kvm/'.$_POST['bizdate'].';'.$_POST['consecnumber'].'.ini')){
		unlink('../../../print/kvm/'.$_POST['bizdate'].';'.$_POST['consecnumber'].'.ini');
	}
	else{
	}
}
else{
}

$date=$_POST['bizdate'];
$listno=$_POST['consecnumber'];
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT inumber,isgroup FROM itemsdata';
$isgroupset=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../database/sale','SALES_'.substr($date,0,6).'.db','','','','sqlite');

//2022/2/15 quickclick串接 作廢
if(isset($content['init']['quickclick'])&&$content['init']['quickclick']=='1'){
	$sql="SELECT CLKCODE FROM tempCST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."'";
	//echo $sql;
	$clkcode=sqlquery($conn,$sql,'sqlite');

	$ts = strtotime(date('YmdHis'));
	//$secret = 'acc6fe509825b263eb9aa0bee09e96bb120f195d';
	$secret = $data['quickclick']['secret'];
	$sig = hash_hmac('sha256', $ts, $secret, true);
	$res = base64_encode($sig);
	//echo $ts.PHP_EOL;
	//$accesskeyid = 'S_20220104124376';
	$accesskeyid = $data['quickclick']['accesskeyid'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $data['quickclick']['url']."/orders/".str_pad($clkcode[0]['CLKCODE'],10,'0',STR_PAD_LEFT)."/cancel");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{
	  \"reason\": \"帳單作廢\"
	}");

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  "Content-Type: application/json",
	  "Authorization: QC ".$accesskeyid.':'.$res,
	  "Seed: ".$ts
	));

	$response = curl_exec($ch);
	curl_close($ch);
}
else{
}

$sql="SELECT COUNT(*) AS number,TABLENUMBER,ZCOUNTER,REMARKS FROM tempCST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."'";
//echo $sql;
$c=sqlquery($conn,$sql,'sqlite');
//print_r($c);
$igs=array();
foreach($isgroupset as $v){
	$igs[intval($v['inumber'])]=intval($v['isgroup']);
}
if($c[0]['number']>=1){
	if($c[0]['REMARKS']=='2'){
		if($content['init']['controltable']==1&&file_exists('../../table/outside/'.$date.';'.$c[0]['ZCOUNTER'].';'.str_pad($listno,6,'0',STR_PAD_LEFT).'.ini')){
			unlink('../../table/outside/'.$date.';'.$c[0]['ZCOUNTER'].';'.str_pad($listno,6,'0',STR_PAD_LEFT).'.ini');
		}
		else{
		}
	}
	else if($c[0]['REMARKS']=='1'){
		if(strstr($c[0]['TABLENUMBER'],',')){
			$tabarray=preg_split('/,/',$c[0]['TABLENUMBER']);
			foreach($tabarray as $tab){
				if($content['init']['controltable']==1&&strlen($c[0]['TABLENUMBER'])>0&&file_exists('../../table/'.$date.';'.$c[0]['ZCOUNTER'].';'.iconv('utf-8','big5',$tab).'.ini')){
					unlink('../../table/'.$date.';'.$c[0]['ZCOUNTER'].';'.iconv('utf-8','big5',$tab).'.ini');
				}
				else{
				}
			}
		}
		else{
			if($content['init']['controltable']==1&&strlen($c[0]['TABLENUMBER'])>0&&file_exists('../../table/'.$date.';'.$c[0]['ZCOUNTER'].';'.iconv('utf-8','big5',$c[0]['TABLENUMBER']).'.ini')){
				unlink('../../table/'.$date.';'.$c[0]['ZCOUNTER'].';'.iconv('utf-8','big5',$c[0]['TABLENUMBER']).'.ini');
			}
			else{
			}
		}
	}
	$sql="SELECT * FROM tempCST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."'";
	$saledata=sqlquery($conn,$sql,'sqlite');
	//print_r($saledata);
	$sql="SELECT * FROM tempCST012 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."' ORDER BY LINENUMBER";
	$salelist=sqlquery($conn,$sql,'sqlite');
	$sql="SELECT * FROM salemap WHERE consecnumber='".str_pad($listno,6,'0',STR_PAD_LEFT)."' AND bizdate='".$date."'";
	$saleno=sqlquery($conn,$sql,'sqlite');
	date_default_timezone_set($content['init']['settime']);
	$sql='UPDATE tempCST011 SET REMARKS="tempvoid",NBCHKDATE="'.date("YmdHis").'",NBCHKTIME="'.$_POST['code'].'",NBCHKNUMBER="Y" WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'";';
	sqlnoresponse($conn,$sql,'sqlite');

	$sql='INSERT INTO CST011 SELECT * FROM tempCST011 WHERE tempCST011.BIZDATE="'.$date.'" AND tempCST011.CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'";';
	sqlnoresponse($conn,$sql,'sqlite');

	$sql='DELETE FROM tempCST011 WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'";';
	sqlnoresponse($conn,$sql,'sqlite');

	$sql='INSERT INTO CST012 SELECT * FROM tempCST012 WHERE tempCST012.BIZDATE="'.$date.'" AND tempCST012.CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'";';
	sqlnoresponse($conn,$sql,'sqlite');

	$sql='DELETE FROM tempCST012 WHERE BIZDATE="'.$date.'" AND CONSECNUMBER="'.str_pad($listno,6,'0',STR_PAD_LEFT).'";';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	
	if($saledata[0]['CLKCODE']=='web'&&$saledata[0]['CLKNAME']=='網路訂購'&&isset($saleno[0]['onlinebizdate'])&&isset($saleno[0]['onlineconsecnumber'])){
		$PostData = array(
			"type"=> 'temp',
			"company"=> $data['basic']['company'],
			"bizdate" => $saleno[0]['onlinebizdate'],
			"consecnumber" => $saleno[0]['onlineconsecnumber']
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/orderweb/lib/js/webchangelist.ajax.php');//
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		// Edit: prior variable $postFields should be $postfields;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		$memdata = curl_exec($ch);
		print_r($memdata);
		$memdata=json_decode($memdata,1);
		print_r($memdata);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
		}
		curl_close($ch);
	}
	else{
		//echo $saledata[0]['CLKCODE'];
		//echo $saledata[0]['CLKNAME'];
		//echo isset($saleno[0]['onlinebizdate']);
		//echo isset($saleno[0]['onlineconsecnumber']);
	}
	
	$looptype=substr($saledata[0]['REMARKS'],0,1);

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
		for($i=0;$i<sizeof($salelist);$i++){
			if($salelist[$i]['DTLMODE']=='1'&&$salelist[$i]['DTLTYPE']=='1'&&$salelist[$i]['DTLFUNC']=='01'&&intval($salelist[$i]['ITEMCODE'])>0){
				$taste='';
				$tastename='';
				$tastenumber='';
				for($s=1;$s<=10;$s++){
					if($salelist[$i]['SELECTIVEITEM'.$s]==null){
						break;
					}
					else{
						//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
						$temptaste=preg_split('/,/',$salelist[$i]['SELECTIVEITEM'.$s]);
						for($j=0;$j<sizeof($temptaste);$j++){
							if(preg_match('/99999/',$temptaste[$j])){//手打備註
								if($taste==''){
									$taste='99999';
									$tastename=substr($temptaste[$j],7);
									$tastenumber='1';
								}
								else{
									$taste=$taste.',99999';
									$tastename=$tastename.','.substr($temptaste[$j],7);
									$tastenumber=$tastenumber.',1';
								}
							}
							else{
								if($taste==''){
									$taste=$taste.intval(substr($temptaste[$j],0,5));
									$tastename=$tastename.$tastemap[intval(substr($temptaste[$j],0,5))]['name1'];
									$tastenumber=$tastenumber.intval(substr($temptaste[$j],5));
								}
								else{
									$taste=$taste.','.intval(substr($temptaste[$j],0,5));
									$tastename=$tastename.','.$tastemap[intval(substr($temptaste[$j],0,5))]['name1'];
									$tastenumber=$tastenumber.','.intval(substr($temptaste[$j],5));
								}
							}
						}
					}
					/*else if(preg_match('/99999/',$salelist[$i]['SELECTIVEITEM'.$s])){//手打備註
						if($taste==''){
							$taste='99999';
							$tastename=substr($salelist[$i]['SELECTIVEITEM'.$s],7);
							$tastenumber='1';
						}
						else{
							$taste=$taste.',99999';
							$tastename=$tastename.','.substr($salelist[$i]['SELECTIVEITEM'.$s],7);
							$tastenumber=$tastenumber.',1';
						}
					}
					else{
						if($taste==''){
							$taste=$taste.intval(substr($salelist[$i]['SELECTIVEITEM'.$s],0,5));
							$tastename=$tastename.$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$s],0,5))]['name1'];
							$tastenumber=$tastenumber.intval(substr($salelist[$i]['SELECTIVEITEM'.$s],5));
						}
						else{
							$taste=$taste.','.intval(substr($salelist[$i]['SELECTIVEITEM'.$s],0,5));
							$tastename=$tastename.','.$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$s],0,5))]['name1'];
							$tastenumber=$tastenumber.','.intval(substr($salelist[$i]['SELECTIVEITEM'.$s],5));
						}
					}*/
				}
				if($menu[intval($salelist[$i]['ITEMCODE'])]['printtype']!='' && ($pti[$menu[intval($salelist[$i]['ITEMCODE'])]['printtype']]['type']=='1' || $pti[$menu[intval($salelist[$i]['ITEMCODE'])]['printtype']]['type']=='3')){//自動彙總(一類一單、一項一單)
					//fwrite($file,$_POST['consecnumber'].'-'.$salelist[0]['CONSECNUMBER'].'-'.$_POST['no'][$i].'-'.$menu[$_POST['no'][$i]]['name1'].'-'.$menu[$_POST['no'][$i]]['printtype'].PHP_EOL);
					if(isset($tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]) && $itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['no']==intval($salelist[$i]['ITEMCODE']) && $itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['taste1']==$taste){
						//fwrite($file,'false'.PHP_EOL);
						if(isset($salelist[$i+1])&&$salelist[$i+1]['ITEMCODE']=='item'){
							$grtitle=$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']];
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
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['number']+=$salelist[$i]['QTY'];
						
					}
					else{
						//echo sizeof($tempitemlist);
						$index=sizeof($tempitemlist);
						$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]=intval($index);
						if($igs[intval($salelist[$i]['ITEMCODE'])]!="0"){
							$grtitle=$index;
							$subitem=1;
							$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['order']=$salelist[$i]['LINENUMBER'];
						}
						else{
							if($subitem==-1||(isset($itemlist[$grtitle]['no'])&&intval($subitem)>intval($igs[$itemlist[$grtitle]['no']]))){
								$subitem=-1;
								$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['order']=$salelist[$i]['LINENUMBER'];
							}
							else{
								$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['order']='－';
								$subitem++;
							}
						}
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['no']=intval($salelist[$i]['ITEMCODE']);
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['name']=$menu[intval($salelist[$i]['ITEMCODE'])]['name1'];
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['name2']=$menu[intval($salelist[$i]['ITEMCODE'])]['name2'];
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['isgroup']=$igs[intval($salelist[$i]['ITEMCODE'])];
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['mname1']=$salelist[$i]['UNITPRICELINK'];
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['number']=$salelist[$i]['QTY'];
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['taste1']=$taste;
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['taste1name']=$tastename;
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['taste1number']=$tastenumber;
						foreach($tempitemlist as $a=>$b){
							//fwrite($file,'index= '.$a.';value= '.$b.PHP_EOL);
							foreach($b as $c=>$d){
								//fwrite($file,'  index= '.$c.';value= '.$d.PHP_EOL);
							}
						}
					}
				}
				else{
					$index=sizeof($tempitemlist);
					$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]=intval($index);
					if($igs[intval($salelist[$i]['ITEMCODE'])]!="0"){
						$grtitle=$index;
						$subitem=1;
						$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['order']=$salelist[0]['LINENUMBER'];
					}
					else{
						if($subitem==-1||intval($subitem)>intval($igs[$itemlist[$grtitle]['no']])){
							$subitem=-1;
							$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['order']=$salelist[0]['LINENUMBER'];
						}
						else{
							$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['order']='－';
							$subitem++;
						}
					}
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['grtitle']=$grtitle;
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['no']=intval($salelist[$i]['ITEMCODE']);
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['name']=$menu[intval($salelist[$i]['ITEMCODE'])]['name1'];
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['name2']=$menu[intval($salelist[$i]['ITEMCODE'])]['name2'];
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['isgroup']=$igs[intval($salelist[$i]['ITEMCODE'])];
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['mname1']=$salelist[$i]['UNITPRICELINK'];
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['number']=$salelist[$i]['QTY'];
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['taste1']=$taste;
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['taste1name']=$tastename;
					$itemlist[$tempitemlist[intval($salelist[$i]['ITEMCODE']).'-'.intval($salelist[$i]['LINENUMBER'])][$taste.','.$salelist[$i]['UNITPRICELINK']]]['taste1number']=$tastenumber;
				}
			}
			else{
			}
		}
		//print_r($tempitemlist);
		//print_r($itemlist);
		//fwrite($file,'saleno= '.$salelist[0]['CONSECNUMBER'].PHP_EOL);
		$kitcontent=array();
		
		if(preg_match('/-/',$saledata[0]['REMARKS'])){
			$tempreserve=preg_split('/;/',substr($saledata[0]['REMARKS'],2));
		}
		else{
		}
		for($i=0;$i<sizeof($itemlist);$i++){//for($i=0;$i<sizeof($_POST['no']);$i++){
			if($menu[$itemlist[$i]['no']]['printtype']!=''&&($pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='3'||$pti[$menu[$itemlist[$i]['no']]['printtype']]['type']=='4')){//一項一單
				//$PHPWord = new PHPWord();
				/*if(isset($print['item']['kitchentype'])&&file_exists('../../../template/kitchen'.$print['item']['kitchentype'].'.docx')){
					//$document = $PHPWord->loadTemplate('../../../template/kitchen'.$print['item']['kitchentype'].'.docx');
					if($saledata[0]['TABLENUMBER']==''){
						if($saledata[0]['REMARKS']=='1'){
							$document->setValue('type',$buttons['name']['listtype1'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else if($saledata[0]['REMARKS']=='2'){
							$document->setValue('type',$buttons['name']['listtype2'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else if($saledata[0]['REMARKS']=='3'){
							$document->setValue('type',$buttons['name']['listtype3'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else{
							$document->setValue('type',$buttons['name']['listtype4'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						$document->setValue('time',date('m/d H:i'));
					}
					else{
						if($saledata[0]['REMARKS']=='1'){
							$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌 '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
							$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype1']);
						}
						else if($saledata[0]['REMARKS']=='2'){
							$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌 '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
							$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype2']);
						}
						else if($saledata[0]['REMARKS']=='3'){
							$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌 '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
							$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype3']);
						}
						else{
							$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌 '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
							$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype4']);
						}
					}
				}
				else{
					$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
					if($saledata[0]['TABLENUMBER']==''){
						if($saledata[0]['REMARKS']=='1'){
							$document->setValue('type',$buttons['name']['listtype1'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else if($saledata[0]['REMARKS']=='2'){
							$document->setValue('type',$buttons['name']['listtype2'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else if($saledata[0]['REMARKS']=='3'){
							$document->setValue('type',$buttons['name']['listtype3'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else{
							$document->setValue('type',$buttons['name']['listtype4'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
					}
					else{
						if($saledata[0]['REMARKS']=='1'){
							$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌\r\n".$buttons['name']['listtype1'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else if($saledata[0]['REMARKS']=='2'){
							$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌\r\n".$buttons['name']['listtype2'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else if($saledata[0]['REMARKS']=='3'){
							$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌\r\n".$buttons['name']['listtype3'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
						else{
							$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌\r\n".$buttons['name']['listtype4'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']);
						}
					}
					$document->setValue('time',date('m/d H:i'));
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
								
								if($saledata[0]['TABLENUMBER']==''){
									if(isset($tempreserve)){
										if(substr($saledata[0]['REMARKS'],0,1)=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if(substr($saledata[0]['REMARKS'],0,1)=='2'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if(substr($saledata[0]['REMARKS'],0,1)=='3'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
									else{
										if($saledata[0]['REMARKS']=='1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if($saledata[0]['REMARKS']=='2'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else if($saledata[0]['REMARKS']=='3'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
										}
									}
								}
								else{
									if(isset($tempreserve)){
										if($ininame!='-1'){
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype'.substr($saledata[0]['REMARKS'],0,1)].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
										}
										else{
											$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype'.substr($saledata[0]['REMARKS'],0,1)].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
										}
									}
									else{
										if($saledata[0]['REMARKS']=='1'){
											if($ininame!='-1'){
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
										else if($saledata[0]['REMARKS']=='2'){
											if($ininame!='-1'){
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
										else if($saledata[0]['REMARKS']=='3'){
											if($ininame!='-1'){
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
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
								
								date_default_timezone_set($content['init']['settime']);
								if(isset($ininame['name']['voidman'])){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['name']."\r\n".date('m/d H:i');
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n退菜人員:".$_POST['name']."\r\n".date('m/d H:i');
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

					if($saledata[0]['TABLENUMBER']==''){
						if(isset($tempreserve)){
							if(substr($saledata[0]['REMARKS'],0,1)=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else if(substr($saledata[0]['REMARKS'],0,1)=='2'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else if(substr($saledata[0]['REMARKS'],0,1)=='3'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name'];
							}
						}
						else{
							if($saledata[0]['REMARKS']=='1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
							else if($saledata[0]['REMARKS']=='2'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
							else if($saledata[0]['REMARKS']=='3'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n";
							}
						}
					}
					else{
						if(isset($tempreserve)){
							if($ininame!='-1'){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype'.substr($saledata[0]['REMARKS'],0,1)].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
							}
							else{
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= substr($tempreserve[0],0,4)."/".substr($tempreserve[0],4,2)."/".substr($tempreserve[0],6,2)." ".substr($tempreserve[0],8,2).":".substr($tempreserve[0],10,2)."\r\n".$buttons['name']['listtype'.substr($saledata[0]['REMARKS'],0,1)].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
							}
						}
						else{
							if($saledata[0]['REMARKS']=='1'){
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
							else if($saledata[0]['REMARKS']=='2'){
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
							else if($saledata[0]['REMARKS']=='3'){
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']." ".$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$menu[$itemlist[$i]['no']]['printtype']]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}
					}

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					
					//time
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
					
					date_default_timezone_set($content['init']['settime']);
					if(isset($ininame['name']['voidman'])){
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['name']."\r\n".date('m/d H:i');
					}
					else{
						$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n退菜人員:".$_POST['name']."\r\n".date('m/d H:i');
					}

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";

					$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';
				}

				$tindex=0;

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "Items";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
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

							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tastemap[$temptasteno[$t]])&&$tastemap[$temptasteno[$t]]['name2']!=''){
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
							if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tastemap[$temptasteno[$t]])&&$tastemap[$temptasteno[$t]]['name2']!=''){
								$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$tastemap[$temptasteno[$t]]['name2'];
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

				$kitcontent[$menu[$itemlist[$i]['no']]['printtype']] .= '</w:tbl>';

				/*$document->setValue('consecnumber',intval($saledata[0]['CONSECNUMBER']));
				//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
				//$document->setValue('tel', '(04)2473-2003');
				//$document->setValue('time', date('Y/m/s H:i:s'));
				$document->setValue('story','退菜單');

				$tindex=0;

				$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "Items";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "QTY";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$sum=0;
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
				//if(strlen($itemlist[$i]['mname1'])==''){
				//	$table .= $itemlist[$i]['name'];
				//}
				//else{
					$table .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
				//}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
				$table .= $itemlist[$i]['number'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				if(strlen($itemlist[$i]['taste1'])>0){
					$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
					for($t=0;$t<sizeof($temp);$t++){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
						//$table .= '-'.$_POST['taste1'][$t];
						$table .= '　+'.$temp[$t];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$table .= '';
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
				}
				else{
				}
				$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>---------------------------------------------</w:t></w:r><w:r w:rsidR="00596374"><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>--</w:t></w:r></w:p>';
		
				$document->setValue('item',$table);
				//$document->setValue('total','NT.'.$_POST['total']);
				$filename=date("YmdHis");
				if(isset($print['item']['voidkitchen'])&&$print['item']['voidkitchen']!='0'&&$pti[$menu[$itemlist[$i]['no']]['printtype']]['kitchen'.$saledata[0]['REMARKS']]=='1'){
					//$document->save("../../../print/noread/".$filename."_list".$menu[$itemlist[$i]['no']]['printtype']."_".intval($salelist[0]['CONSECNUMBER'])."_".$i.".docx");
					$document->save("../../../print/read/".intval($saledata[0]['CONSECNUMBER'])."_list".$menu[$itemlist[$i]['no']]['printtype']."_".$filename."_".$i.".docx");
					$prt=fopen("../../../print/noread/".intval($saledata[0]['CONSECNUMBER'])."_list".$menu[$itemlist[$i]['no']]['printtype']."_".$filename."_".$i.".prt",'w');
					fclose($prt);
					//if(intval($print['item']['voidkitchen'])>1){
					//	for($j=1;$j<intval($print['item']['kitchen']);$j++){
					//		copy("../../../print/noread/list".$pt."_".intval($salelist[0]['CONSECNUMBER'])."_".$filename.".docx","../../../print/noread/list".$pt."_".intval($salelist[0]['CONSECNUMBER'])."_".$filename."(".$j.").docx");
					//	}
					//}
					//else{
					//}
				}
				else{
					$document->save("../../../print/read/delete_list".$menu[$itemlist[$i]['no']]['printtype'].".docx");
				}*/
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

								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tastemap[$temptasteno[$t]])&&$tastemap[$temptasteno[$t]]['name2']!=''){
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
								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tastemap[$temptasteno[$t]])&&$tastemap[$temptasteno[$t]]['name2']!=''){
									$conarray['-1'] .= '/ '.$tastemap[$temptasteno[$t]]['name2'];
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

								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tastemap[$temptasteno[$t]])&&$tastemap[$temptasteno[$t]]['name2']!=''){
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
								if(isset($print['kitchen']['secname'])&&$print['kitchen']['secname']=='1'&&isset($tastemap[$temptasteno[$t]])&&$tastemap[$temptasteno[$t]]['name2']!=''){
									$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '/ '.$tastemap[$temptasteno[$t]]['name2'];
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
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc></w:tr>";
				}


				/*if($menu[$itemlist[$i]['no']]['printtype']==''){
					$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
					//if(strlen($itemlist[$i]['mname1'])==''){
					//	$conarray['-1'] .= $itemlist[$i]['name'];
					//}
					//else{
						$conarray['-1'] .= $itemlist[$i]['name'].'('.$itemlist[$i]['mname1'].')';
					//}
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
					$conarray['-1'] .= $itemlist[$i]['number'];
					$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
					$conarray['-1'] .= "</w:tr>";
					if(strlen($itemlist[$i]['taste1'])>0){
						$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
						for($t=0;$t<sizeof($temp);$t++){
							$conarray['-1'] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$conarray['-1'] .= '　+'.$temp[$t];
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray['-1'] .= '';
							$conarray['-1'] .= "</w:t></w:r></w:p></w:tc>";
							$conarray['-1'] .= "</w:tr>";
						}
					}
					else{
					}
				}
				else{
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['name'];
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= $itemlist[$i]['number'];
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
					$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
					if(strlen($taste)>0){
						$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
						for($t=0;$t<sizeof($temp);$t++){
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$tt=preg_split('/\//',$temp[$t]);
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '　+'.$tt[0];
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= '';
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:t></w:r></w:p></w:tc>";
							$conarray[$menu[$itemlist[$i]['no']]['printtype']] .= "</w:tr>";
						}
					}
					else{
					}
				}*/
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
				if($pti[$printindex]['kitchen'.substr($saledata[0]['REMARKS'],0,1)]=='1'&&isset($print['item']['voidkitchen'])&&$print['item']['voidkitchen']=='1'){
					//$document->save("../../../print/noread/".$filename."_list".$menu[$itemlist[$i]['no']]['printtype']."_".intval($_POST['consecnumber'])."_".$i.".docx");
					$document->save("../../../print/read/".$saledata[0]['CONSECNUMBER']."_voidlist".substr($saledata[0]['REMARKS'],0,1).$printindex."_".$filename.".docx");
					$prt=fopen("../../../print/noread/".$saledata[0]['CONSECNUMBER']."_voidlist".substr($saledata[0]['REMARKS'],0,1).$printindex."_".$filename.".prt",'w');
					fclose($prt);
				}
				else{
					$document->save("../../../print/read/delete_voidlist".substr($saledata[0]['REMARKS'],0,1).$printindex."_".$saledata[0]['CONSECNUMBER'].".docx");
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
								
								if($saledata[0]['TABLENUMBER']==''){
									if($saledata[0]['REMARKS']=='1'){
										if($k=='-1'){
											$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
									else if($saledata[0]['REMARKS']=='2'){
										if($k=='-1'){
											$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
										}
										else{
											$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
										}
									}
									else if($saledata[0]['REMARKS']=='3'){
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
									if($saledata[0]['REMARKS']=='1'){
										if($k=='-1'){
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
									}
									else if($saledata[0]['REMARKS']=='2'){
										if($k=='-1'){
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
									}
									else if($saledata[0]['REMARKS']=='3'){
										if($k=='-1'){
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
									}
									else{
										if($k=='-1'){
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
											}
										}
										else{
											if($ininame!='-1'){
												$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
											}
											else{
												$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
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
								
								date_default_timezone_set($content['init']['settime']);
								if(isset($ininame['name']['voidman'])){
									$table .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['name']."\r\n".date('m/d H:i');
								}
								else{
									$table .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n退菜人員:".$_POST['name']."\r\n".date('m/d H:i');
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

					if($saledata[0]['TABLENUMBER']==''){
						if($saledata[0]['REMARKS']=='1'){
							if($k=='-1'){
								$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
						else if($saledata[0]['REMARKS']=='2'){
							if($k=='-1'){
								$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
							}
							else{
								$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name'];
							}
						}
						else if($saledata[0]['REMARKS']=='3'){
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
						if($saledata[0]['REMARKS']=='1'){
							if($k=='-1'){
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}
						else if($saledata[0]['REMARKS']=='2'){
							if($k=='-1'){
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}
						else if($saledata[0]['REMARKS']=='3'){
							if($k=='-1'){
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}
						else{
							if($k=='-1'){
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
							else{
								if($ininame!='-1'){
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']." ".$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'].' '.$pti[$k]['name']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
					
					//time
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
					
					date_default_timezone_set($content['init']['settime']);
					if(isset($ininame['name']['voidman'])){
						$table .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['name']."\r\n".date('m/d H:i');
					}
					else{
						$table .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n退菜人員:".$_POST['name']."\r\n".date('m/d H:i');
					}

					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					$table .= '</w:tbl>';
				}

				$tindex=0;
				$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="3333"/><w:gridCol w:w="1667"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3333" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "Items";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1667" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "QTY";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$sum=0;
				$table .= $v;
				$table .= '</w:tbl>';
		
				$document->setValue('item',$table);
				//$document->setValue('total','NT.'.$_POST['total']);
				date_default_timezone_set($content['init']['settime']);
				$filename=date("YmdHis");
				if($k=='-1'){
					//$document->save("../../../print/noread/".$filename."_listN_".intval($_POST['consecnumber']).".docx");
					$document->save("../../../print/read/".$saledata[0]['CONSECNUMBER']."_listN_".$filename.".docx");
					$prt=fopen("../../../print/noread/".$saledata[0]['CONSECNUMBER']."_listN_".$filename.".prt",'w');
					fclose($prt);
				}
				else{
					if($pti[$k]['kitchen'.substr($saledata[0]['REMARKS'],0,1)]=='1'&&isset($print['item']['voidkitchen'])&&$print['item']['voidkitchen']=='1'){
						//$document->save("../../../print/noread/".$filename."_list".$k."_".intval($_POST['consecnumber']).".docx");
						$document->save("../../../print/read/".$saledata[0]['CONSECNUMBER']."_list".substr($saledata[0]['REMARKS'],0,1).$k."_".$filename.".docx");
						$prt=fopen("../../../print/noread/".$saledata[0]['CONSECNUMBER']."_list".substr($saledata[0]['REMARKS'],0,1).$k."_".$filename.".prt",'w');
						fclose($prt);
					}
					else{
						$document->save("../../../print/read/delete_list".substr($saledata[0]['REMARKS'],0,1).$k."_".$saledata[0]['CONSECNUMBER'].".docx");
					}
				}
			}
		}

		/*foreach($conarray as $k=>$v){
			if($v==''){
				continue;
			}
			else{
				$PHPWord = new PHPWord();
				if(isset($print['item']['kitchentype'])&&file_exists('../../../template/kitchen'.$print['item']['kitchentype'].'.docx')){
					$document = $PHPWord->loadTemplate('../../../template/kitchen'.$print['item']['kitchentype'].'.docx');
					if($saledata[0]['TABLENUMBER']==''){
						if($saledata[0]['REMARKS']=='1'){
							if($k=='-1'){
								$document->setValue('type',$buttons['name']['listtype1']);
							}
							else{
								$document->setValue('type',$buttons['name']['listtype1'].' '.$pti[$k]['name']);
							}
						}
						else if($saledata[0]['REMARKS']=='2'){
							if($k=='-1'){
								$document->setValue('type',$buttons['name']['listtype2']);
							}
							else{
								$document->setValue('type',$buttons['name']['listtype2'].' '.$pti[$k]['name']);
							}
						}
						else if($saledata[0]['REMARKS']=='3'){
							if($k=='-1'){
								$document->setValue('type',$buttons['name']['listtype3']);
							}
							else{
								$document->setValue('type',$buttons['name']['listtype3'].' '.$pti[$k]['name']);
							}
						}
						else{
							if($k=='-1'){
								$document->setValue('type',$buttons['name']['listtype4']);
							}
							else{
								$document->setValue('type',$buttons['name']['listtype4'].' '.$pti[$k]['name']);
							}
						}
						$document->setValue('time',date('m/d H:i'));
					}
					else{
						if($saledata[0]['REMARKS']=='1'){
							if($k=='-1'){
								$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌');
							}
							else{
								$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌 '.$pti[$k]['name']);
							}
							$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype1']);
						}
						else if($saledata[0]['REMARKS']=='2'){
							if($k=='-1'){
								$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌');
							}
							else{
								$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌 '.$pti[$k]['name']);
							}
							$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype2']);
						}
						else if($saledata[0]['REMARKS']=='3'){
							if($k=='-1'){
								$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌');
							}
							else{
								$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌 '.$pti[$k]['name']);
							}
							$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype3']);
						}
						else{
							if($k=='-1'){
								$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌');
							}
							else{
								$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌 '.$pti[$k]['name']);
							}
							$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype4']);
						}
					}
				}
				else{
					$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
					if($saledata[0]['TABLENUMBER']==''){
						if($saledata[0]['REMARKS']=='1'){
							if($k=='-1'){
								$document->setValue('type',$buttons['name']['listtype1']);
							}
							else{
								$document->setValue('type',$buttons['name']['listtype1'].' '.$pti[$k]['name']);
							}
						}
						else if($saledata[0]['REMARKS']=='2'){
							if($k=='-1'){
								$document->setValue('type',$buttons['name']['listtype2']);
							}
							else{
								$document->setValue('type',$buttons['name']['listtype2'].' '.$pti[$k]['name']);
							}
						}
						else if($saledata[0]['REMARKS']=='3'){
							if($k=='-1'){
								$document->setValue('type',$buttons['name']['listtype3']);
							}
							else{
								$document->setValue('type',$buttons['name']['listtype3'].' '.$pti[$k]['name']);
							}
						}
						else{
							if($k=='-1'){
								$document->setValue('type',$buttons['name']['listtype4']);
							}
							else{
								$document->setValue('type',$buttons['name']['listtype4'].' '.$pti[$k]['name']);
							}
						}
						
					}
					else{
						if($saledata[0]['REMARKS']=='1'){
							if($k=='-1'){
								$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌 ".$buttons['name']['listtype1']);
							}
							else{
								$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌\r\n".$buttons['name']['listtype1'].' '.$pti[$k]['name']);
							}
						}
						else if($saledata[0]['REMARKS']=='2'){
							if($k=='-1'){
								$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌 ".$buttons['name']['listtype2']);
							}
							else{
								$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌\r\n".$buttons['name']['listtype2'].' '.$pti[$k]['name']);
							}
						}
						else if($saledata[0]['REMARKS']=='3'){
							if($k=='-1'){
								$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌 ".$buttons['name']['listtype3']);
							}
							else{
								$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌\r\n".$buttons['name']['listtype3'].' '.$pti[$k]['name']);
							}
						}
						else{
							if($k=='-1'){
								$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌 ".$buttons['name']['listtype4']);
							}
							else{
								$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌\r\n".$buttons['name']['listtype4'].' '.$pti[$k]['name']);
							}
						}
					}
					$document->setValue('time',date('m/d H:i'));
				}
				
				
				$document->setValue('consecnumber',intval($saledata[0]['CONSECNUMBER']));
				//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
				//$document->setValue('tel', '(04)2473-2003');
				//$document->setValue('time', date('Y/m/s H:i:s'));
				$document->setValue('story','退菜單');
				
				$tindex=0;
				$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "Items";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "QTY";
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$sum=0;
				$table .= $v;
				$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>---------------------------------------------</w:t></w:r><w:r w:rsidR="00596374"><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>--</w:t></w:r></w:p>';
		
				$document->setValue('item',$table);
				//$document->setValue('total','NT.'.$_POST['total']);
				$filename=date("YmdHis");
				if($k=='-1'){
					if(isset($print['item']['voidkitchen'])&&$print['item']['voidkitchen']!='0'){
						//$document->save("../../../print/noread/".$filename."_listN_".intval($salelist[0]['CONSECNUMBER']).".docx");
						$document->save("../../../print/read/".intval($saledata[0]['CONSECNUMBER'])."_listN_".$filename.".docx");
						$prt=fopen("../../../print/noread/".intval($saledata[0]['CONSECNUMBER'])."_listN_".$filename.".prt",'w');
						fclose($prt);
					}
					else{
						$document->save("../../../print/read/delete_listN.docx");
					}
				}
				else{
					if(isset($print['item']['voidkitchen'])&&$print['item']['voidkitchen']!='0'&&$pti[$k]['kitchen'.$saledata[0]['REMARKS']]=='1'){
						//$document->save("../../../print/noread/".$filename."_list".$k."_".intval($salelist[0]['CONSECNUMBER']).".docx");
						$document->save("../../../print/read/".intval($saledata[0]['CONSECNUMBER'])."_list".$k."_".$filename.".docx");
						$prt=fopen("../../../print/noread/".intval($saledata[0]['CONSECNUMBER'])."_list".$k."_".$filename.".prt",'w');
						fclose($prt);
					}
					else{
						$document->save("../../../print/read/delete_list".$k.".docx");
					}
				}
			}
		}*/
	}
	if(isset($print['item']['kittype'])&&($print['item']['kittype']=='1'||$print['item']['kittype']=='3')){//廚房總單
		$PHPWord = new PHPWord();
		/*if(isset($print['item']['kitchentype'])&&file_exists('../../../template/kitchen'.$print['item']['kitchentype'].'.docx')){
			$document = $PHPWord->loadTemplate('../../../template/kitchen'.$print['item']['kitchentype'].'.docx');
			if($saledata[0]['TABLENUMBER']==''){
				if($saledata[0]['REMARKS']=='1'){
					$document->setValue('type',$buttons['name']['listtype1']);
				}
				else if($saledata[0]['REMARKS']=='2'){
					$document->setValue('type',$buttons['name']['listtype2']);
				}
				else if($saledata[0]['REMARKS']=='3'){
					$document->setValue('type',$buttons['name']['listtype3']);
				}
				else{
					$document->setValue('type',$buttons['name']['listtype4']);
				}
				$document->setValue('time',date('m/d H:i'));
			}
			else{
				if($saledata[0]['REMARKS']=='1'){
					$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌');
					$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype1']);
				}
				else if($saledata[0]['REMARKS']=='2'){
					$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌');
					$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype2']);
				}
				else if($saledata[0]['REMARKS']=='3'){
					$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌');
					$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype3']);
				}
				else{
					$document->setValue('type',$saledata[0]['TABLENUMBER'].'號桌');
					$document->setValue('time',date('m/d H:i').' '.$buttons['name']['listtype4']);
				}
			}
		}
		else{
			$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
			if($saledata[0]['TABLENUMBER']==''){
				if($saledata[0]['REMARKS']=='1'){
					$document->setValue('type',$buttons['name']['listtype1']);
				}
				else if($saledata[0]['REMARKS']=='2'){
					$document->setValue('type',$buttons['name']['listtype2']);
				}
				else if($saledata[0]['REMARKS']=='3'){
					$document->setValue('type',$buttons['name']['listtype3']);
				}
				else{
					$document->setValue('type',$buttons['name']['listtype4']);
				}
			}
			else{
				if($saledata[0]['REMARKS']=='1'){
					$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌 ".$buttons['name']['listtype1']);
				}
				else if($saledata[0]['REMARKS']=='2'){
					$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌 ".$buttons['name']['listtype2']);
				}
				else if($saledata[0]['REMARKS']=='3'){
					$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌 ".$buttons['name']['listtype3']);
				}
				else{
					$document->setValue('type',$saledata[0]['TABLENUMBER']."號桌 ".$buttons['name']['listtype4']);
				}
			}
			$document->setValue('time',date('m/d H:i'));
		}*/

		$document = $PHPWord->loadTemplate('../../../template/kitchen.docx');
		$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';

		if(isset($print['kitchen']['title'])){
			$temptitle=preg_split('/,/',$print['kitchen']['title']);
			foreach($temptitle as $tempitem){
				switch($tempitem){
					case 'story':

						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['storyfontsize'])*2).'"/></w:rPr><w:t>';

						if(isset($ininame['name']['voidkitchen'])){
							$table .= $ininame['name']['voidkitchen'];
						}
						else{
							$table .= '退菜單';
						}

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						break;
					case 'type':

						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

						if($looptype=='1'){
							if($saledata[0]['TABLENUMBER']==''){
								$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
							}
							else{
								if(isset($ininame['name']['table'])){
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}
						else if($looptype=='2'){
							if($saledata[0]['TABLENUMBER']==''){
								$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
							}
							else{
								if(isset($ininame['name']['table'])){
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}
						else if($looptype=='3'){
							if($saledata[0]['TABLENUMBER']==''){
								$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'];
							}
							else{
								if(isset($ininame['name']['table'])){
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}
						else{
							if($saledata[0]['TABLENUMBER']==''){
								$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'];
							}
							else{
								if(isset($ininame['name']['table'])){
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
								}
								else{
									$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
								}
							}
						}

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						break;
					case 'time':

						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
						
						date_default_timezone_set($content['init']['settime']);
						if(isset($ininame['name']['voidman'])){
							$table .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['name']."\r\n".date('m/d H:i');
						}
						else{
							$table .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n退菜人員:".$_POST['name']."\r\n".date('m/d H:i');
						}
						//$table .= $saledata[0]['CONSECNUMBER']." ".$saledata[0]['CLKNAME']." ".date('m/d H:i');

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
				$table .= $ininame['name']['voidkitchen'];
			}
			else{
				$table .= '退菜單';
			}

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			
			//type
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['typefontsize'])*2).'"/></w:rPr><w:t>';

			if($looptype=='1'){
				if($saledata[0]['TABLENUMBER']==''){
					$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno'];
				}
				else{
					if(isset($ininame['name']['table'])){
						$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
					}
					else{
						$table .= $buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
					}
				}
			}
			else if($looptype=='2'){
				if($saledata[0]['TABLENUMBER']==''){
					$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno'];
				}
				else{
					if(isset($ininame['name']['table'])){
						$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
					}
					else{
						$table .= $buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
					}
				}
			}
			else if($looptype=='3'){
				if($saledata[0]['TABLENUMBER']==''){
					$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno'];
				}
				else{
					if(isset($ininame['name']['table'])){
						$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
					}
					else{
						$table .= $buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
					}
				}
			}
			else{
				if($saledata[0]['TABLENUMBER']==''){
					$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno'];
				}
				else{
					if(isset($ininame['name']['table'])){
						$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$ininame['name']['table'];
					}
					else{
						$table .= $buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER']."號桌";
					}
				}
			}

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			
			//time
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="'.$print['kitchen']['titlefont'].'" w:hAnsi="Consolas" w:hint="eastAsia"/><w:b/><w:sz w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/><w:szCs w:val="'.(floatval($print['kitchen']['timefontsize'])*2).'"/></w:rPr><w:t>';
			
			date_default_timezone_set($content['init']['settime']);
			if(isset($ininame['name']['voidman'])){
				$table .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n".$ininame['name']['voidman'].':'.$_POST['name']."\r\n".date('m/d H:i');
			}
			else{
				$table .= $_POST['consecnumber'].' '.$saledata[0]['CLKNAME']."\r\n退菜人員:".$_POST['name']."\r\n".date('m/d H:i');
			}
			//$table .= $saledata[0]['CONSECNUMBER']." ".$saledata[0]['CLKNAME']." ".date('m/d H:i');

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";

			$table .= '</w:tbl>';
		}

		$tindex=0;
		
		$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="5000"/></w:tblGrid>';
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
		for($i=0;$i<sizeof($salelist);$i=$i+2){
			$temporderlist=0;
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
			if(strlen($salelist[$i]['UNITPRICELINK'])==0){
				$table .= $menu[intval($salelist[$i]['ITEMCODE'])]['name1'];
			}
			else{
				$table .= $menu[intval($salelist[$i]['ITEMCODE'])]['name1'].'('.$salelist[$i]['UNITPRICELINK'].')';
			}
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
			$table .= $salelist[$i]['QTY'];
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			for($t=1;$t<=10;$t++){
				if($salelist[$i]['SELECTIVEITEM'.$t]==null){
					break;
				}
				else{
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					$temptaste=preg_split('/,/',$salelist[$i]['SELECTIVEITEM'.$t]);
					for($j=0;$j<sizeof($temptaste);$j++){
						if(preg_match('/99999/',$temptaste[$j])){//手打備註
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
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
							$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$table .= '　+'.$tastemap[intval(substr($temptaste[$j],0,5))]['name1'];
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
							$table .= '';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
					}
				}
				/*else if(preg_match('/99999/',$salelist[$i]['SELECTIVEITEM'.$t])){//手打備註
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
					//$table .= '-'.$_POST['taste1'][$t];
					$table .= '　+'.substr($salelist[$i]['SELECTIVEITEM'.$t],7);
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
					//$table .= '-'.$_POST['taste1'][$t];
					$table .= '　+'.$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
					$table .= '';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}*/
			}
		}
		$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>-----------------------------------------------</w:t></w:r></w:p>';
		
		$document->setValue('item',$table);
		//$document->setValue('total','NT.'.$_POST['total']);
		date_default_timezone_set($content['init']['settime']);
		$filename=date("YmdHis");
		if($print['item']['voidkitchen']==1){
			//$document->save("../../../print/noread/".$filename."_voidlist_".intval($consecnumber).".docx");
			$document->save("../../../print/read/".$filename."_list_".$saledata[0]['CONSECNUMBER'].".docx");
			$prt=fopen("../../../print/noread/".$filename."_list_".$saledata[0]['CONSECNUMBER'].".prt",'w');
			fclose($prt);
		}
		else{
			$document->save("../../../print/read/delete_list".$saledata[0]['CONSECNUMBER']."_".$filename.".docx");
		}
		
		
		/*$document->setValue('consecnumber',intval($saledata[0]['CONSECNUMBER']));
		//$document->setValue('address', '台中市南屯區文心路一段73號7樓之3');
		//$document->setValue('tel', '(04)2473-2003');
		//$document->setValue('time', date('Y/m/s H:i:s'));
		$document->setValue('story','退菜單');
		
		$tindex=0;
		
		$table = '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="1702"/><w:gridCol w:w="990"/><w:gridCol w:w="822"/></w:tblGrid>';
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00A41CE3" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= "Items";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
		$table .= "QTY";
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
		$sum=0;
		for($i=0;$i<sizeof($salelist);$i++){
			if($salelist[$i]['DTLMODE']=='1'&&$salelist[$i]['DTLTYPE']=='1'&&$salelist[$i]['DTLFUNC']=='01'){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
				//if(strlen($salelist[$i]['UNITPRICELINK'])==0){
				//	$table .= $menu[intval($salelist[$i]['ITEMCODE'])]['name1'];
				//}
				//else{
					$table .= $menu[intval($salelist[$i]['ITEMCODE'])]['name1'].'('.$salelist[$i]['UNITPRICELINK'].')';
				//}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr><w:t>';
				$table .= $salelist[$i]['QTY'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				for($t=1;$t<=10;$t++){
					if($salelist[$i]['SELECTIVEITEM'.$t]==null){
						break;
					}
					else{
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="524"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['item']['kitchensize'].'"/><w:szCs w:val="'.$print['item']['kitchensize'].'"/></w:rPr><w:t>';
						//$table .= '-'.$_POST['taste1'][$t];
						$table .= '　+'.$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="微軟正黑體" w:eastAsia="微軟正黑體" w:hAnsi="微軟正黑體" w:hint="eastAsia"/><w:b/><w:sz w:val="10"/><w:szCs w:val="10"/></w:rPr><w:t>';
						$table .= '';
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
				}
			}
			else{
			}
		}
		$table .= '</w:tbl><w:p w:rsidR="00A41CE3" w:rsidRDefault="00A41CE3" w:rsidP="00A41CE3"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:b/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>---------------------------------------------</w:t></w:r><w:r w:rsidR="00596374"><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:szCs w:val="24"/></w:rPr><w:t>--</w:t></w:r></w:p>';
		
		$document->setValue('item',$table);
		//$document->setValue('total','NT.'.$_POST['total']);
		$filename=date("YmdHis");
		if(isset($print['item']['voidkitchen'])&&$print['item']['voidkitchen']!='0'){
			//$document->save("../../../print/noread/".$filename."_list_".intval($salelist[0]['CONSECNUMBER']).".docx");
			$document->save("../../../print/read/".intval($saledata[0]['CONSECNUMBER'])."_list_".$filename.".docx");
			$prt=fopen("../../../print/noread/".intval($saledata[0]['CONSECNUMBER'])."_list_".$filename.".prt",'w');
			fclose($prt);
		}
		else{
			$document->save("../../../print/read/delete_list.docx");
		}*/
	}
	else{
	}

	//fclose($file);
	
	$PHPWord = new PHPWord();
	if(isset($print['item']['clienttype'])&&file_exists('../../../template/clientlist'.$print['item']['clienttype'].'.docx')){
		$document1 = $PHPWord->loadTemplate('../../../template/clientlist'.$print['item']['clienttype'].'.docx');
		/*if($saledata[0]['TABLENUMBER']==''){
			if($saledata[0]['REMARKS']=='1'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
			}
			else if($saledata[0]['REMARKS']=='2'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
			else if($saledata[0]['REMARKS']=='3'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
			$document1->setValue('datetime',date('Y/m/d H:i:s'));
		}
		else{
			if($saledata[0]['REMARKS']=='1'){
				$document1->setValue('type',"作廢\r\n".$saledata[0]['TABLENUMBER'].'號桌');
				$document1->setValue('datetime',date('Y/m/d H:i:s').' (暫)'.$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
			}
			else if($saledata[0]['REMARKS']=='2'){
				$document1->setValue('type',"作廢\r\n".$saledata[0]['TABLENUMBER'].'號桌');
				$document1->setValue('datetime',date('Y/m/d H:i:s').' (暫)'.$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
			else if($saledata[0]['REMARKS']=='3'){
				$document1->setValue('type',"作廢\r\n".$saledata[0]['TABLENUMBER'].'號桌');
				$document1->setValue('datetime',date('Y/m/d H:i:s').' (暫)'.$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"作廢\r\n".$saledata[0]['TABLENUMBER'].'號桌');
				$document1->setValue('datetime',date('Y/m/d H:i:s').' (暫)'.$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}

		}*/
	}
	else{
		$document1 = $PHPWord->loadTemplate('../../../template/clientlist.docx');
		/*if($saledata[0]['TABLENUMBER']==''){
			if($saledata[0]['REMARKS']=='1'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
			}
			else if($saledata[0]['REMARKS']=='2'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
			else if($saledata[0]['REMARKS']=='3'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
		}
		else{
			if($saledata[0]['REMARKS']=='1'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].'號桌');
			}
			else if($saledata[0]['REMARKS']=='2'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].'號桌');
			}
			else if($saledata[0]['REMARKS']=='3'){
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].'號桌');
			}
			else{
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].'號桌');
			}
		}
		$document1->setValue('datetime',date('Y/m/d H:i:s'));*/
	}

	if($looptype=='1'){
		if($saledata[0]['TABLENUMBER']==''){
			if(isset($saledata[0]['REMARKS'])&&preg_match('/-/',$saledata[0]['REMARKS'])){
				$temp=preg_split('/;/',substr($saledata[0]['REMARKS'],2));
				if($clientname!='-1'){
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n".$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
				}
				else{
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n作廢\r\n(暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
				}
			}
			else{
				if($clientname!='-1'){
					$document1->setValue('type',$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
				}
				else{
					$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']);
				}
			}
		}
		else{
			if(isset($saledata[0]['REMARKS'])&&preg_match('/-/',$saledata[0]['REMARKS'])){
				$temp=preg_split('/;/',substr($saledata[0]['REMARKS'],2));
				if($clientname!='-1'){
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n".$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$clientname['name']['table']);
				}
				else{
					$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n作廢\r\n(暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].'號桌');
				}
			}
			else{
				if($clientname!='-1'){
					$document1->setValue('type',$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].$clientname['name']['table']);
				}
				else{
					$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype1'].' '.$saleno[0]['saleno']."\r\n".$saledata[0]['TABLENUMBER'].'號桌');
				}
			}
		}
	}
	else if($looptype=='2'){
		if(isset($saledata[0]['REMARKS'])&&preg_match('/-/',$saledata[0]['REMARKS'])){
			$temp=preg_split('/;/',substr($saledata[0]['REMARKS'],2));
			if($clientname!='-1'){
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n".$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n作廢\r\n(暫)".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
		}
		else{
			if($clientname!='-1'){
				$document1->setValue('type',$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].") ".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"作廢\r\n(暫) ".$buttons['name']['listtype2'].' '.$saleno[0]['saleno']);
			}
		}
	}
	else if($looptype=='3'){
		if(isset($saledata[0]['REMARKS'])&&preg_match('/-/',$saledata[0]['REMARKS'])){
			$temp=preg_split('/;/',substr($saledata[0]['REMARKS'],2));
			if($clientname!='-1'){
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n".$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n作廢\r\n(暫)".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
		}
		else{
			if($clientname!='-1'){
				$document1->setValue('type',$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype3'].' '.$saleno[0]['saleno']);
			}
		}
	}
	else{
		if(isset($saledata[0]['REMARKS'])&&preg_match('/-/',$saledata[0]['REMARKS'])){
			$temp=preg_split('/;/',substr($saledata[0]['REMARKS'],2));
			if($clientname!='-1'){
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n".$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n作廢\r\n(暫)".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
		}
		else{
			if($clientname!='-1'){
				$document1->setValue('type',$clientname['name']['voidlistlabel']."\r\n(".$clientname['name']['temp'].")".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
			else{
				$document1->setValue('type',"作廢\r\n(暫)".$buttons['name']['listtype4'].' '.$saleno[0]['saleno']);
			}
		}
	}

	if($clientname!='-1'&&isset($clientname['name']['bizdatelabel'])){
		$document1->setValue('bizdate',$clientname['name']['bizdatelabel'].':'.substr($saledata[0]['BIZDATE'],0,4).'/'.substr($saledata[0]['BIZDATE'],4,2).'/'.substr($saledata[0]['BIZDATE'],6,2));
	}
	else{
		$document1->setValue('bizdate','營業日:'.substr($saledata[0]['BIZDATE'],0,4).'/'.substr($saledata[0]['BIZDATE'],4,2).'/'.substr($saledata[0]['BIZDATE'],6,2));
	}
	if($clientname!='-1'&&isset($clientname['name']['datetimelabel'])){
		$document1->setValue('datetime',$clientname['name']['datetimelabel'].':'.substr($saledata[0]['CREATEDATETIME'],8,2).':'.substr($saledata[0]['CREATEDATETIME'],10,2).':'.substr($saledata[0]['CREATEDATETIME'],12,2));
	}
	else{
		$document1->setValue('datetime','開單時間:'.substr($saledata[0]['CREATEDATETIME'],8,2).':'.substr($saledata[0]['CREATEDATETIME'],10,2).':'.substr($saledata[0]['CREATEDATETIME'],12,2));
	}
	date_default_timezone_set($content['init']['settime']);
	if($clientname!='-1'&&isset($clientname['name']['voidtimelabel'])){
		$document1->setValue('saletime',$clientname['name']['voidtimelabel'].':'.date('H:i:s'));
	}
	else{
		$document1->setValue('saletime','作廢時間:'.date('H:i:s'));
	}
	
	$document1->setValue('consecnumber',$saledata[0]['CONSECNUMBER']);
	
	
	/*if(isset($data['basic']['address'])&&$data['basic']['address']!=''){
		$document1->setValue('address', $data['basic']['address']);
	}
	else{
	}
	if(isset($data['basic']['tel'])&&$data['basic']['tel']!=''){
		$document1->setValue('phone', $data['basic']['tel']);
	}
	else{
	}*/
	$document1->setValue('story',$data['basic']['storyname']);
	$tindex=0;
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
	//print_r($salelist);
	/*for($i=0;$i<sizeof($salelist);$i++){
		if($salelist[$i]['DTLMODE']=='1'&&$salelist[$i]['DTLTYPE']=='3'&&$salelist[$i]['DTLFUNC']=='02'&&$salelist[$i]['ITEMCODE']=='list'){
			continue;
		}
		else{
			if(intval($salelist[$i]['ITEMCODE'])>0){
				if($salelist[$i]['DTLMODE']=='1'&&$salelist[$i]['DTLTYPE']=='1'&&$salelist[$i]['DTLFUNC']=='01'){
					if((isset($pti[$menu[intval($salelist[$i]['ITEMCODE'])]['printtype']]['clientlist'.$saledata[0]['REMARKS']])&&$pti[$menu[intval($salelist[$i]['ITEMCODE'])]['printtype']]['clientlist'.$saledata[0]['REMARKS']]=='1')){
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
						if(strlen($salelist[$i]['UNITPRICELINK'])==0){
							$table .= $menu[intval($salelist[$i]['ITEMCODE'])]['name1'].'x'.$salelist[$i]['QTY'];
						}
						else{
							$table .= $menu[intval($salelist[$i]['ITEMCODE'])]['name1'].'('.$salelist[$i]['UNITPRICELINK'].')x'.$salelist[$i]['QTY'];
						}
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $content['init']['frontunit'].$salelist[$i]['UNITPRICE'].$content['init']['unit'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						$table .= $content['init']['frontunit'].($salelist[$i]['UNITPRICE']*$salelist[$i]['QTY']).$content['init']['unit'];
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";

						if(isset($posdvr)){
							$tempdvrcontent .= $salelist[$i]['ITEMNAME']."X".$salelist[$i]['QTY']."  ";
							if(floatval($salelist[$i]['UNITPRICE'])==0){
								$tempdvrcontent .= "0  0".PHP_EOL;;
							}
							else{
								$tempdvrcontent .= preg_replace('/{.}/','!46',$salelist[$i]['UNITPRICE'])."  ".preg_replace('/{.}/','!46',($salelist[$i]['AMT'])).PHP_EOL;
							}
						}
						else{
						}
						$totalqty=intval($totalqty)+intval($salelist[$i]['QTY']);
						for($t=1;$t<=10;$t++){
							if($salelist[$i]['SELECTIVEITEM'.$t]==null){
								break;
							}
							else{
								$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
								$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
								//$table .= '-'.$_POST['taste1'][$t];
								if(intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5))==1){
									$tt=$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
								}
								else{
									$tt=$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'].'*'.intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5));
								}
								$table .= '　+'.$tt;
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								if(intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])==0){
								}
								else{
									$table .= intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money']);
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
								if(intval((intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5))))==0){
								}
								else{
									$table .= $content['init']['frontunit'].(intval((intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5))))).$content['init']['unit'];
								}
								$table .= "</w:t></w:r></w:p></w:tc>";
								$table .= "</w:tr>";

								if(isset($posdvr)){
									if(intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5))==1){
										$tempdvrcontent .= " !43".$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1']."  ".preg_replace('/{.}/','!46',intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money']))."  ".preg_replace('/{.}/','!46',intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])).PHP_EOL;
									}
									else{
										$tempdvrcontent .= " !43".$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1']."X".intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5))."  ".preg_replace('/{.}/','!46',intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money']))."  ".preg_replace('/{.}/','!46',(intval((intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5)))))).PHP_EOL;
									}
								}
								else{
								}
							}
						}
					}
					else{
					}
				}
				else if($salelist[$i]['ITEMCODE']=='item'&&floatval($salelist[$i]['AMT'])!=0){
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					//$table .= '-'.$_POST['taste1'][$t];
					if(isset($list['name']['itemdis'])){
						$table .= '　+'.$list['name']['itemdis'];
					}
					else{
						$table .= '　+優惠折扣';
					}
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
					$table .= $content['init']['frontunit'].$salelist[$i]['AMT'].$content['init']['unit'];
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";

					if(isset($posdvr)){
						if(isset($list['name']['itemdis'])){
							$tempdvrcontent .= " !43".$list['name']['itemdis']."    ".preg_replace('/{.}/','!46',$salelist[$i]['AMT']).PHP_EOL;
						}
						else{
							$tempdvrcontent .= " !43優惠折扣    ".preg_replace('/{.}/','!46',$salelist[$i]['AMT']).PHP_EOL;
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
		}
	}*/
	for($i=0;$i<sizeof($salelist);$i++){
		if($salelist[$i]['DTLMODE']=='1'&&$salelist[$i]['DTLTYPE']=='3'&&$salelist[$i]['DTLFUNC']=='02'&&$salelist[$i]['ITEMCODE']=='list'){
			continue;
		}
		else{
			if($salelist[$i]['DTLMODE']=='1'&&$salelist[$i]['DTLTYPE']=='1'&&$salelist[$i]['DTLFUNC']=='01'){
				$totalqty=intval($totalqty)+intval($salelist[$i]['QTY']);
				$temporderlist=0;
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
				if(strlen($salelist[$i]['UNITPRICELINK'])==0){
					$table .= $menu[intval($salelist[$i]['ITEMCODE'])]['name1'].'x'.$salelist[$i]['QTY'];
				}
				else{
					$table .= $menu[intval($salelist[$i]['ITEMCODE'])]['name1'].'('.$salelist[$i]['UNITPRICELINK'].')x'.$salelist[$i]['QTY'];
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$salelist[$i]['UNITPRICE'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$salelist[$i]['AMT'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
				$linetaste='';
				for($t=1;$t<=10;$t++){
					if($salelist[$i]['SELECTIVEITEM'.$t]==null){
						break;
					}
					else{
						//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
						$temptaste=preg_split('/,/',$salelist[$i]['SELECTIVEITEM'.$t]);
						for($j=0;$j<sizeof($temptaste);$j++){
							if(preg_match('/99999/',$temptaste[$j])){//手打備註
								if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行							
									$tt=substr($temptaste[$j],7);
									if($linetaste==''){
										$linetaste = '　+'.$tt;
									}
									else{
										$linetaste .= ','.$tt;
									}
								}
								else{//備註一項一行
									$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
									$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
									$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
									//$table .= '-'.$_POST['taste1'][$t];
									$table .= '　+'.substr($temptaste[$j],7);
									$table .= "</w:t></w:r></w:p></w:tc>";
									$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
									$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
									$table .= "</w:t></w:r></w:p></w:tc>";
									$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
									$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
									$table .= "</w:t></w:r></w:p></w:tc>";
									$table .= "</w:tr>";
								}
							}
							else{
								if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行							
									if(intval(substr($temptaste[$j],5,1))==1){
										$tt=$tastemap[intval(substr($temptaste[$j],0,5))]['name1'];
									}
									else{
										$tt=$tastemap[intval(substr($temptaste[$j],0,5))]['name1'].'*'.substr($temptaste[$j],5,1);
									}
									if($linetaste==''){
										$linetaste = '　+'.$tt;
									}
									else{
										$linetaste .= ','.$tt;
									}
								}
								else{//備註一項一行
									$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
									$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
									$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
									//$table .= '-'.$_POST['taste1'][$t];
									if(intval(substr($temptaste[$j],5,1))==1){
										$tt=$tastemap[intval(substr($temptaste[$j],0,5))]['name1'];
									}
									else{
										$tt=$tastemap[intval(substr($temptaste[$j],0,5))]['name1'].'*'.substr($temptaste[$j],5,1);
									}
									$table .= '　+'.$tt;
									$table .= "</w:t></w:r></w:p></w:tc>";
									$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
									$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
									if(intval($tastemap[intval(substr($temptaste[$j],0,5))]['money'])==0){
									}
									else{
										$table .= $tastemap[intval(substr($temptaste[$j],0,5))]['money'];
									}
									$table .= "</w:t></w:r></w:p></w:tc>";
									$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
									$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
									if((intval($tastemap[intval(substr($temptaste[$j],0,5))]['money'])*intval(substr($temptaste[$j],5,1)))==0){
									}
									else{
										$table .= $content['init']['frontunit'].(intval($tastemap[intval(substr($temptaste[$j],0,5))]['money'])*intval(substr($temptaste[$j],5,1))).$content['init']['unit'];
									}
									$table .= "</w:t></w:r></w:p></w:tc>";
									$table .= "</w:tr>";
								}
							}
						}
					}
					/*else if(preg_match('/99999/',$salelist[$i]['SELECTIVEITEM'.$t])){//手打備註
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行							
							$tt=substr($salelist[$i]['SELECTIVEITEM'.$t],7);
							if($linetaste==''){
								$linetaste = '　+'.$tt;
							}
							else{
								$linetaste .= ','.$tt;
							}
						}
						else{//備註一項一行
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
							$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							$table .= '　+'.substr($salelist[$i]['SELECTIVEITEM'.$t],7);
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
					}
					else{
						if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行							
							if(intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5,1))==1){
								$tt=$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
							}
							else{
								$tt=$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'].'*'.substr($salelist[$i]['SELECTIVEITEM'.$t],5,1);
							}
							if($linetaste==''){
								$linetaste = '　+'.$tt;
							}
							else{
								$linetaste .= ','.$tt;
							}
						}
						else{//備註一項一行
							$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
							$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
							$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
							//$table .= '-'.$_POST['taste1'][$t];
							if(intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5,1))==1){
								$tt=$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
							}
							else{
								$tt=$tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['name1'].'*'.substr($salelist[$i]['SELECTIVEITEM'.$t],5,1);
							}
							$table .= '　+'.$tt;
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							if(intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])==0){
							}
							else{
								$table .= $tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'];
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
							$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
							if((intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5,1)))==0){
							}
							else{
								$table .= $content['init']['frontunit'].(intval($tastemap[intval(substr($salelist[$i]['SELECTIVEITEM'.$t],0,5))]['money'])*intval(substr($salelist[$i]['SELECTIVEITEM'.$t],5,1))).$content['init']['unit'];
							}
							$table .= "</w:t></w:r></w:p></w:tc>";
							$table .= "</w:tr>";
						}
					}*/
				}
				if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/>';
					$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					
					$table .= $linetaste;
					
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
					//備註一項一行
				}
			}
			else if($salelist[$i]['ITEMCODE']=='item'&&floatval($salelist[$i]['AMT'])!=0){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
				//$table .= '-'.$_POST['taste1'][$t];
				if(isset($clientname['name']['itemdis'])){
					$table .= '　+'.$clientname['name']['itemdis'];
				}
				else{
					$table .= '　+優惠折扣';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].$salelist[$i]['AMT'].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";

				if(isset($posdvr)){
					if(isset($clientname['name']['itemdis'])){
						$tempdvrcontent .= " !43".$clientname['name']['itemdis']."    ".preg_replace('/{.}/','!46',$salelist[$i]['AMT']).PHP_EOL;
					}
					else{
						$tempdvrcontent .= " !43優惠折扣    ".preg_replace('/{.}/','!46',$salelist[$i]['AMT']).PHP_EOL;
					}
				}
				else{
				}
			}
			else{
			}
		}
	}
	if(isset($posdvr)){
		$tempdvrcontent .= "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL;
	}
	else{
	}
	$table .= '</w:tbl>';
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';

	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clititle'].'"/><w:szCs w:val="'.$print['item']['clititle'].'"/></w:rPr><w:t>';
	if($clientname!='-1'){
		$table .= $clientname['name']['qty'];
	}
	else{
		$table .= '商品數量';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clicont'].'"/><w:szCs w:val="'.$print['item']['clicont'].'"/></w:rPr><w:t>';
	$table .= $totalqty;
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clititle'].'"/><w:szCs w:val="'.$print['item']['clititle'].'"/></w:rPr><w:t>';
	if($clientname!='-1'){
		$table .= $clientname['name']['voidmoneylabel'];
	}
	else{
		$table .= '作廢金額';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clicont'].'"/><w:szCs w:val="'.$print['item']['clicont'].'"/></w:rPr><w:t>';
	$table .= $content['init']['frontunit'].$saledata[0]['SALESTTLAMT'].$content['init']['unit'];
	$table .= "</w:t></w:r></w:p></w:tc></w:tr>";

	/*$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2421" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	//$table .= '-'.$_POST['taste1'][$t];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1409" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= 'AMT';
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="1171" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
	$table .= $content['init']['frontunit'].$saledata[0]['SALESTTLAMT'].$content['init']['unit'];
	$table .= "</w:t></w:r></w:p></w:tc></w:tr>";*/
	$table .= '</w:tbl>';

	$document1->setValue('item',$table);

	/*$document1->setValue('memname','');
	$document1->setValue('memtel','');
	$document1->setValue('memaddress','');
	$document1->setValue('memremarks','');*/
	
	//$memtable .= "</w:tbl>";
	$document1->setValue('memtable','');
	//date_default_timezone_set('Asia/Taipei');
	//$datetime=date('YmdHis');
	date_default_timezone_set($content['init']['settime']);
	$y=date('Y');
	$m=date('m');
	$d=date('d');
	$h=date('H');
	$i=date('i');
	$s=date('s');
	
	date_default_timezone_set($content['init']['settime']);
	$filename=date('YmdHis');
	if(!isset($print['item']['voidclient'])||$print['item']['voidclient']=='0'){
		$document1->save("../../../print/read/delete_clientlist.docx");
	}
	else{
		//$document1->save("../../../print/noread/".$filename."_clientlist_".intval($salelist[0]['CONSECNUMBER']).".docx");
		if(!isset($_POST['terminalnumber'])||$_POST['terminalnumber']==''){
			$document1->save("../../../print/read/".$saledata[0]['CONSECNUMBER']."_clientlistm1_".$filename.".docx");
			$prt=fopen("../../../print/noread/".$saledata[0]['CONSECNUMBER']."_clientlistm1_".$filename.".prt",'w');
			fclose($prt);
		}
		else{
			$document1->save("../../../print/read/".$saledata[0]['CONSECNUMBER']."_clientlist".$_POST['terminalnumber']."_".$filename.".docx");
			$prt=fopen("../../../print/noread/".$saledata[0]['CONSECNUMBER']."_clientlist".$_POST['terminalnumber']."_".$filename.".prt",'w');
			fclose($prt);
		}
	}
	echo 'success-';
	if(isset($posdvr)){
		$tempdvrcontent = "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "名稱   單價   小計".PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL.$tempdvrcontent;
		if(isset($saledata[0]['CLKNAME'])){
			$tempdvrcontent = "服務員!58".$saledata[0]['CLKNAME'].PHP_EOL.$tempdvrcontent;
		}
		else{
			$tempdvrcontent = "服務員!58".PHP_EOL.$tempdvrcontent;
		}
		$tempdvrcontent = "時間!58".substr($saledata[0]['CREATEDATETIME'],0,4).'!47'.substr($saledata[0]['CREATEDATETIME'],4,2).'!47'.substr($saledata[0]['CREATEDATETIME'],6,2).' '.substr($saledata[0]['CREATEDATETIME'],8,2).'!58'.substr($saledata[0]['CREATEDATETIME'],10,2).'!58'.substr($saledata[0]['CREATEDATETIME'],12,2).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "機!58".$saledata[0]['TERMINALNUMBER'].PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "編號!58".str_pad($listno,6,'0',STR_PAD_LEFT).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent = "voidtemplist".PHP_EOL."桌號!58".trim($saledata[0]['TABLENUMBER']).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent .= "小 計  ".preg_replace('/{.}/','!46',$saledata[0]['SALESTTLAMT']).PHP_EOL;
		fwrite($posdvr,$tempdvrcontent);
		fclose($posdvr);
	}
	else{
	}
	if(isset($content['init']['posdvr'])&&$content['init']['posdvr']=='1'){
		echo $tempposdvr.';'.$_POST['terminalnumber'];
	}
	else{
	}
}
else{
	sqlclose($conn,'sqlite');
	echo 'fail';
}
?>