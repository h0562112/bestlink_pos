<?php
include_once '../../../tool/myerrorlog.php';
require_once '../../../tool/PHPWord.php';
include_once '../../../tool/dbTool.inc.php';
$content=parse_ini_file('../../../database/initsetting.ini',true);
$data=parse_ini_file('../../../database/setup.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);
//$file=fopen('../../log.txt','w');
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
if(isset($_POST['notconsecnumber'])){//用於暫出明細，不印consecnumber&saleno
	//$consecnumber='';
	//$saleno='';
	if(isset($_POST['consecnumber'])&&$_POST['consecnumber']==''){
		$consecnumber=$machinedata['basic']['consecnumber']+1;
		$saleno=$machinedata['basic']['saleno']+1;
	}
	else{
		$consecnumber=$_POST['consecnumber'];
		$saleno=$_POST['saleno'];
	}
}
else{
	if(isset($_POST['consecnumber'])&&$_POST['consecnumber']==''){
		$consecnumber=$machinedata['basic']['consecnumber'];
		$saleno=$machinedata['basic']['saleno'];
	}
	else{
		$consecnumber=$_POST['consecnumber'];
		$saleno=$_POST['saleno'];
	}
}
$consecnumber=str_pad($consecnumber,6,'0',STR_PAD_LEFT);

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

if(isset($print['item']['textfont'])){
}
else{
	$print['item']['textfont']="微軟正黑體";
}
if(isset($print['clientlist']['invsize'])){
}
else{
	$print['clientlist']['invsize']="20";
}
if(isset($print['item']['tastefront'])){
}
else{
	$print['item']['tastefront']="1";
}

$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
if(file_exists('../../syspram/clientlist-'.$content['init']['firlan'].'.ini')){
	$list=parse_ini_file('../../syspram/clientlist-'.$content['init']['firlan'].'.ini',true);
}
else if(file_exists('../../syspram/clientlist-1.ini')){
	$list=parse_ini_file('../../syspram/clientlist-1.ini',true);
}
else{
	$list='-1';
}
if(strlen($_POST['listtype'])==1){//POS點單
	$listtype=$_POST['listtype'];
}
else{//網路預約單
	$listtype=substr($_POST['listtype'],0,1);
}
if($listtype=='1'){
	if(file_exists('../../../database/discount1.ini')){
		$discount=parse_ini_file('../../../database/discount1.ini',true);
	}
	else{
		//$discount=parse_ini_file('../../../database/discount1.ini',true);
	}
}
else if($listtype=='2'){
	if(file_exists('../../../database/discount2.ini')){
		$discount=parse_ini_file('../../../database/discount2.ini',true);
	}
	else{
		//$discount=parse_ini_file('../../../database/discount2.ini',true);
	}
}
else if($listtype=='3'){
	if(file_exists('../../../database/discount3.ini')){
		$discount=parse_ini_file('../../../database/discount3.ini',true);
	}
	else{
		//$discount=parse_ini_file('../../../database/discount3.ini',true);
	}
}
else{//$listtype=='4'
	if(file_exists('../../../database/discount4.ini')){
		$discount=parse_ini_file('../../../database/discount4.ini',true);
	}
	else{
		//$discount=parse_ini_file('../../../database/discount4.ini',true);
	}
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
$saleinvdata='';
if(isset($_POST['printtempclient'])){
	$looptype='1';
}
else if(isset($_POST['looptype'])){
	$looptype=$_POST['looptype'];
}
else{
	//$looptype=$content['init']['listprint'];
}

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

/*if(isset($print['item']['clitype'])&&$print['item']['clitype']==80){
	$clititle=32;
	$clicont=32;
}
else if(isset($print['item']['clitype'])&&$print['item']['clitype']==58){
	$clititle=20;
	$clicont=16;
}
else{
	$clititle=32;
	$clicont=32;
}*/

$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
if(!isset($_POST['reusername'])||!isset($_POST['listtotal'])){
	$remarksql='SELECT INVOICENUMBER,UPDATEDATETIME,REMARKS,CREATEDATETIME,RELINVOICETIME,RELINVOICENUMBER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'"';
}
else{//2021/8/3 補印結帳明細
	$remarksql='SELECT INVOICENUMBER,UPDATEDATETIME,REMARKS,CREATEDATETIME,RELINVOICETIME,RELINVOICENUMBER FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'"';
}
$remarks=sqlquery($conn,$remarksql,'sqlite');
if(!isset($_POST['reusername'])||!isset($_POST['listtotal'])){
	$sql='SELECT DISTINCT CREATEDATETIME FROM (SELECT CREATEDATETIME FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'" UNION ALL SELECT CREATEDATETIME FROM voiditem WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'")';
}
else{//2021/8/3 補印結帳明細
	$sql='SELECT DISTINCT CREATEDATETIME FROM (SELECT CREATEDATETIME FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'" UNION ALL SELECT CREATEDATETIME FROM voiditem WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'")';
}
$times=sqlquery($conn,$sql,'sqlite');
if(!isset($_POST['reusername'])||!isset($_POST['listtotal'])){
	$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'" AND ITEMCODE="autodis"';
}
else{//2021/8/3 補印結帳明細
	$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'" AND ITEMCODE="autodis"';
}
//echo $sql;
$autodis=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

$document1='';
$oinv='';
$PHPWord = new PHPWord();
if(isset($print['item']['clienttype'])&&file_exists('../../../template/clientlist'.$print['item']['clienttype'].'.docx')){
	$document1 = $PHPWord->loadTemplate('../../../template/clientlist'.$print['item']['clienttype'].'.docx');
}
else{//舊版明細單
	$document1 = $PHPWord->loadTemplate('../../../template/clientlist.docx');
}
if($print['item']['clienttype']=='tableplus'){//公司耗材使用
	date_default_timezone_set($content['init']['settime']);
	$document1->setValue('printdate',date('Y/m/d H:i:s'));
	$document1->setValue('memname',$memdata[0]['name']);
	$document1->setValue('memtel',$memdata[0]['tel']);
	if(preg_match('/;php;/',$memdata[0]['address'])){
		//$document1->setValue('address',$memdata[0]['address']);
		$addressarray=preg_split('/;php;/',$memdata[0]['address']);
		$document1->setValue('address',$addressarray[$remarks[0]['RELINVOICENUMBER']]);
	}
	else{
		$document1->setValue('address',$memdata[0]['address']);
	}
	$document1->setValue('consecnumber',$consecnumber);

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
		if(isset($print['clientlist']['replacename'])&&$print['clientlist']['replacename']=='1'){//2021/9/2 列印品項名稱以0>>原品項名稱1>>列印名稱為主
			if(isset($menu[$_POST['no'][$i]]['printname'])&&$menu[$_POST['no'][$i]]['printname']!=''){
				$_POST['name'][$i]=$menu[$_POST['no'][$i]]['printname'];
			}
			else{
			}
			//2022/5/18 尋找價格名稱的列印名稱
			for($mn=1;$mn<=6;$mn++){
				if($menu[$_POST['no'][$i]]['mname'.$mn.'1']==$_POST['mname1'][$i]&&$menu[$_POST['no'][$i]]['money'.$mn]==$_POST['unitprice'][$i]&&isset($menu[$_POST['no'][$i]]['mname'.$mn.'printname'])&&$menu[$_POST['no'][$i]]['mname'.$mn.'printname']!=''){//2022/5/18 尋找對應的價格名稱，並如有設定列印名稱則取代
					$_POST['mname1'][$i]=$menu[$_POST['no'][$i]]['mname'.$mn.'printname'];
					break;
				}
				else{
				}
			}
		}
		else{
		}
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
		//$totalqty=intval($totalqty)+intval($_POST['number'][$i]);
		//$newitem++;
	}
	for($i=0;$i<sizeof($itemlist);$i++){
		$document1->setValue('no'.$i,$i+1);
		$document1->setValue('name'.$i,$itemlist[$i]['name']);
		$document1->setValue('qty'.$i,$itemlist[$i]['number']);
		$document1->setValue('mname'.$i,$itemlist[$i]['mname1']);
		$document1->setValue('unitprice'.$i,$itemlist[$i]['unitprice']);
		$document1->setValue('amt'.$i,($itemlist[$i]['subtotal']+$itemlist[$i]['discount']));
		
		$linetaste='';
		if(strlen($itemlist[$i]['taste1'])>0){
			$tasteno=preg_split('/,/',$itemlist[$i]['taste1']);
			$temp=preg_split('/,/',$itemlist[$i]['taste1name']);
			$temp2=preg_split('/,/',$itemlist[$i]['taste1number']);
			$temp3=preg_split('/,/',$itemlist[$i]['taste1price']);
			for($t=0;$t<sizeof($temp);$t++){
				if($tasteno[$t]!='999991'){
					$tt=preg_split('/\//',$temp[$t]);

					//if(isset($tt[1])){
						if(intval($temp2[$t])==1){
							$tt[0]=$tt[0];
						}
						else{
							$tt[0]=$tt[0].'*'.$temp2[$t];
						}
					/*}
					else{
					}*/
					
					if($linetaste==''){
						$linetaste .= $tt[0];
					}
					else{
						$linetaste .= ','.$tt[0];
					}
				}
				else{					
					if($linetaste==''){
						$linetaste .= $temp[$t];
					}
					else{
						$linetaste .= ','.$temp[$t];
					}
				}
			}
		}
		else{
		}
		$document1->setValue('taste'.$i,$linetaste);
	}
	for(;$i<5;$i++){
		$document1->setValue('no'.$i,'');
		$document1->setValue('name'.$i,'');
		$document1->setValue('qty'.$i,'');
		$document1->setValue('mname'.$i,'');
		$document1->setValue('unitprice'.$i,'');
		$document1->setValue('amt'.$i,'');
		$document1->setValue('taste'.$i,'');
	}
	if(isset($_POST['listtotal'])){
		$document1->setValue('notyet',$_POST['should']);
		$document1->setValue('total',$_POST['should']);
	}
	else{
		if(isset($_POST['floorspan'])){
			if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
				$document1->setValue('notyet',($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']));
				$document1->setValue('total',($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']));
			}
			else{
				$document1->setValue('notyet',($_POST['total']+$_POST['floorspan']+$_POST['charge']));
				$document1->setValue('total',($_POST['total']+$_POST['floorspan']+$_POST['charge']));
			}
		}
		else{
			if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
				$document1->setValue('notyet',($_POST['total']+$_POST['charge']+$autodis[0]['AMT']));
				$document1->setValue('total',($_POST['total']+$_POST['charge']+$autodis[0]['AMT']));
			}
			else{
				$document1->setValue('notyet',($_POST['total']+$_POST['charge']));
				$document1->setValue('total',($_POST['total']+$_POST['charge']));
			}
		}
	}
	if(sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])&&preg_match('/-/',$remarks[0]['REMARKS'])){
		$temp=preg_split('/;/',substr($remarks[0]['REMARKS'],2));
		if(substr($temp[0],8)=='now'){
			$document1->setValue('listtype',$buttons['name']['listtype'.substr($remarks[0]['REMARKS'],0,1)].':'.substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好");
		}
		else{
			$document1->setValue('listtype',$buttons['name']['listtype'.substr($remarks[0]['REMARKS'],0,1)].':'.substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2));
		}
	}
	else{
		$document1->setValue('listtype',$buttons['name']['listtype'.substr($remarks[0]['REMARKS'],0,1)]);
	}
	date_default_timezone_set($content['init']['settime']);
	$filename=date('YmdHis');
	$document1->save("../../../print/read/".$consecnumber."_clientlist".substr($remarks[0]['REMARKS'],0,1)."temp".$_POST['machinetype']."_".$filename.".docx");
	if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
		$prt=fopen("../../../print/noread/".$consecnumber."_clientlist".substr($remarks[0]['REMARKS'],0,1)."temp".$_POST['machinetype']."_".$filename.".".$_POST['machinetype'],'w');
	}
	else{
		$prt=fopen("../../../print/noread/".$consecnumber."_clientlist".substr($remarks[0]['REMARKS'],0,1)."temp".$_POST['machinetype']."_".$filename.".prt",'w');
	}
	fclose($prt);
}
else{
	if((isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1'&&isset($_POST['listtotal']))||(isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1*')){
		if($listtype=='1'){
			if(isset($_POST['reusername'])){//2021/8/3 補印
				$reprintlabel='補';
				$reprintlabel2='(補) ';
			}
			else{
				$reprintlabel='';
				$reprintlabel2='';
			}
			if($_POST['tablenumber']==''){
				if(isset($_POST['notconsecnumber'])){//用於暫出明細，不印consecnumber&saleno
					if($list!='-1'){
						$document1->setValue('type','('.$list['name']['temppriorder'].') '.$buttons['name']['listtype1'].' '.$saleno);
					}
					else{
						$document1->setValue('type','(暫出)'.$buttons['name']['listtype1'].' '.$saleno);
					}
				}
				else{
					if($list!='-1'){
						if(!isset($list['name']['clititle'])||strlen($list['name']['clititle']) == 0){
							$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype1'].' '.$saleno);
						}
						else{
							$document1->setValue('type','('.$reprintlabel.$list['name']['clititle'].') '.$buttons['name']['listtype1'].' '.$saleno);
						}
					}
					else{
						$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype1'].' '.$saleno);
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
				if($list!='-1'){
					if(isset($_POST['notconsecnumber'])){//用於暫出明細，不印consecnumber&saleno
						if($list!='-1'){
							$document1->setValue('type','('.$list['name']['temppriorder'].') '.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$list['name']['table']);
						}
						else{
							$document1->setValue('type','(暫出)'.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$list['name']['table']);
						}
					}
					else{
						if($list!='-1'){
							if(!isset($list['name']['clititle'])||strlen($list['name']['clititle']) == 0){
								$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$list['name']['table']);
							}
							else{
								$document1->setValue('type','('.$reprintlabel.$list['name']['clititle'].') '.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$list['name']['table']);
							}
						}
						else{
							$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$list['name']['table']);
						}
					}
				}
				else{
					if(isset($_POST['notconsecnumber'])){//用於暫出明細，不印consecnumber&saleno{
						if($list!='-1'){
							$document1->setValue('type','('.$list['name']['temppriorder'].') '.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.'號桌');
						}
						else{
							$document1->setValue('type','(暫出)'.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.'號桌');
						}
					}
					else{
						if($list!='-1'){
							if(!isset($list['name']['clititle'])||strlen($list['name']['clititle']) == 0){
								$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.'號桌');
							}
							else{
								$document1->setValue('type','('.$reprintlabel.$list['name']['clititle'].') '.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.'號桌');
							}
						}
						else{
							$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.'號桌');
						}
					}
				}
			}
			
		}
		else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
			if(isset($_POST['reusername'])){//2021/8/3 補印
				$reprintlabel='補';
				$reprintlabel2='(補) ';
			}
			else{
				$reprintlabel='';
				$reprintlabel2='';
			}
			if(isset($_POST['notconsecnumber'])){//用於暫出明細，不印consecnumber&saleno{
				if($list!='-1'){
					$document1->setValue('type','('.$list['name']['temppriorder'].') '.$buttons['name']['listtype'.$listtype].' '.$saleno);
				}
				else{
					$document1->setValue('type','(暫出)'.$buttons['name']['listtype'.$listtype].' '.$saleno);
				}
			}
			else{
				if($list!='-1'){
					if(!isset($list['name']['clititle'])||strlen($list['name']['clititle']) == 0){
						$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype'.$listtype].' '.$saleno);
					}
					else{
						$document1->setValue('type','('.$reprintlabel.$list['name']['clititle'].') '.$buttons['name']['listtype'.$listtype].' '.$saleno);
					}
				}
				else{
					$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype'.$listtype].' '.$saleno);
				}
			}
		}
		else{
			if(isset($_POST['reusername'])){//2021/8/3 補印
				$reprintlabel='補';
				$reprintlabel2='(補) ';
			}
			else{
				$reprintlabel='';
				$reprintlabel2='';
			}
			if(isset($_POST['notconsecnumber'])){//用於暫出明細，不印consecnumber&saleno{
				if($list!='-1'){
					$document1->setValue('type','('.$list['name']['temppriorder'].') '.$buttons['name']['listtype4'].' '.$saleno);
				}
				else{
					$document1->setValue('type','(暫出)'.$buttons['name']['listtype4'].' '.$saleno);
				}
			}
			else{
				if($list!='-1'){
					if(!isset($list['name']['clititle'])||strlen($list['name']['clititle']) == 0){
						$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype4'].' '.$saleno);
					}
					else{
						$document1->setValue('type','('.$reprintlabel.$list['name']['clititle'].') '.$buttons['name']['listtype4'].' '.$saleno);
					}
				}
				else{
					$document1->setValue('type',$reprintlabel2.$buttons['name']['listtype4'].' '.$saleno);
				}
			}
		}
	}
	else{
		if($listtype=='1'){
			if(isset($_POST['reusername'])){//2021/8/3 補印
				$reprintlabel='補';
			}
			else{
				$reprintlabel='';
			}
			if($_POST['tablenumber']==''){
				if(sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])&&preg_match('/-/',$remarks[0]['REMARKS'])){
					$temp=preg_split('/;/',substr($remarks[0]['REMARKS'],2));
					if($list!='-1'){
						if(substr($temp[0],8)=='now'){
							$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好\r\n(".$reprintlabel.$list['name']['temp'].") ".$buttons['name']['listtype1'].' '.$saleno);
						}
						else{
							$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n(".$reprintlabel.$list['name']['temp'].") ".$buttons['name']['listtype1'].' '.$saleno);
						}
					}
					else{
						if(substr($temp[0],8)=='now'){
							$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好\r\n(".$reprintlabel."暫) ".$buttons['name']['listtype1'].' '.$saleno);
						}
						else{
							$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n(".$reprintlabel."暫) ".$buttons['name']['listtype1'].' '.$saleno);
						}
					}
				}
				else{
					if($list!='-1'){
						$document1->setValue('type','('.$reprintlabel.$list['name']['temp'].') '.$buttons['name']['listtype1'].' '.$saleno);
					}
					else{
						$document1->setValue('type','('.$reprintlabel.'暫) '.$buttons['name']['listtype1'].' '.$saleno);
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
				if(sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])&&preg_match('/-/',$remarks[0]['REMARKS'])){
					$temp=preg_split('/;/',substr($remarks[0]['REMARKS'],2));
					if($list!='-1'){
						if(substr($temp[0],8)=='now'){
							$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好\r\n(".$reprintlabel.$list['name']['temp'].") ".$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$list['name']['table']);
						}
						else{
							$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n(".$reprintlabel.$list['name']['temp'].") ".$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$list['name']['table']);
						}
					}
					else{
						if(substr($temp[0],8)=='now'){
							$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好\r\n(".$reprintlabel."暫) ".$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.'號桌');
						}
						else{
							$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n(".$reprintlabel."暫) ".$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.'號桌');
						}
					}
				}
				else{
					if($list!='-1'){
						$document1->setValue('type','('.$reprintlabel.$list['name']['temp'].') '.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.$list['name']['table']);
					}
					else{
						$document1->setValue('type','('.$reprintlabel.'暫) '.$buttons['name']['listtype1'].' '.$saleno."\r\n".$tablename.'號桌');
					}
				}
			}
		}
		else if($listtype=='2'||$listtype=='3'||$listtype=='4'){
			if(isset($_POST['reusername'])){//2021/8/3 補印
				$reprintlabel='補';
			}
			else{
				$reprintlabel='';
			}
			if(sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])&&preg_match('/-/',$remarks[0]['REMARKS'])){
				$temp=preg_split('/;/',substr($remarks[0]['REMARKS'],2));
				if($list!='-1'){
					if(substr($temp[0],8)=='now'){
						$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好\r\n(".$reprintlabel.$list['name']['temp'].") ".$buttons['name']['listtype'.$listtype].' '.$saleno);
					}
					else{
						$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n(".$reprintlabel.$list['name']['temp'].") ".$buttons['name']['listtype'.$listtype].' '.$saleno);
					}
				}
				else{
					if(substr($temp[0],8)=='now'){
						$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好\r\n(".$reprintlabel."暫) ".$buttons['name']['listtype'.$listtype].' '.$saleno);
					}
					else{
						$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n(".$reprintlabel."暫) ".$buttons['name']['listtype'.$listtype].' '.$saleno);
					}
				}
			}
			else{
				if($list!='-1'){
					$document1->setValue('type','('.$reprintlabel.$list['name']['temp'].') '.$buttons['name']['listtype'.$listtype].' '.$saleno);
				}
				else{
					$document1->setValue('type','('.$reprintlabel.'暫) '.$buttons['name']['listtype'.$listtype].' '.$saleno);
				}
			}
		}
		else{
			if(isset($_POST['reusername'])){//2021/8/3 補印
				$reprintlabel='補';
			}
			else{
				$reprintlabel='';
			}
			if(sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])&&preg_match('/-/',$remarks[0]['REMARKS'])){
				$temp=preg_split('/;/',substr($remarks[0]['REMARKS'],2));
				if($list!='-1'){
					if(substr($temp[0],8)=='now'){
						$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好\r\n(".$reprintlabel.$list['name']['temp'].") ".$buttons['name']['listtype4'].' '.$saleno);
					}
					else{
						$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n(".$reprintlabel.$list['name']['temp'].") ".$buttons['name']['listtype4'].' '.$saleno);
					}
				}
				else{
					if(substr($temp[0],8)=='now'){
						$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)."\r\n越快越好\r\n(".$reprintlabel."暫) ".$buttons['name']['listtype4'].' '.$saleno);
					}
					else{
						$document1->setValue('type',substr($temp[0],0,4)."/".substr($temp[0],4,2)."/".substr($temp[0],6,2)." ".substr($temp[0],8,2).":".substr($temp[0],10,2)."\r\n(".$reprintlabel."暫) ".$buttons['name']['listtype4'].' '.$saleno);
					}
				}
			}
			else{
				if($list!='-1'){
					$document1->setValue('type','('.$reprintlabel.$list['name']['temp'].') '.$buttons['name']['listtype4'].' '.$saleno);
				}
				else{
					$document1->setValue('type','('.$reprintlabel.'暫) '.$buttons['name']['listtype4'].' '.$saleno);
				}
			}
		}
	}
	if($list!='-1'&&isset($list['name']['bizdatelabel'])){
		$document1->setValue('bizdate',$list['name']['bizdatelabel'].':'.substr($_POST['bizdate'],0,4).'/'.substr($_POST['bizdate'],4,2).'/'.substr($_POST['bizdate'],6,2));
	}
	else{
		$document1->setValue('bizdate','營業日:'.substr($_POST['bizdate'],0,4).'/'.substr($_POST['bizdate'],4,2).'/'.substr($_POST['bizdate'],6,2));
	}
	if(sizeof($remarks)>0&&isset($remarks[0]['CREATEDATETIME'])){
		if($list!='-1'&&isset($list['name']['datetimelabel'])){
			$document1->setValue('datetime',$list['name']['datetimelabel'].':'.substr($remarks[0]['CREATEDATETIME'],8,2).':'.substr($remarks[0]['CREATEDATETIME'],10,2).':'.substr($remarks[0]['CREATEDATETIME'],12,2));
		}
		else{
			$document1->setValue('datetime','開單時間:'.substr($remarks[0]['CREATEDATETIME'],8,2).':'.substr($remarks[0]['CREATEDATETIME'],10,2).':'.substr($remarks[0]['CREATEDATETIME'],12,2));
		}
	}
	else{
		if($list!='-1'&&isset($list['name']['datetimelabel'])){
			$document1->setValue('datetime',$list['name']['datetimelabel'].':');
		}
		else{
			$document1->setValue('datetime','開單時間:');
		}
	}
	
	if(isset($times)&&sizeof($times)>1&&(!((isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1'&&isset($_POST['listtotal']))||(isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1*'))||isset($_POST['notconsecnumber']))){//加點
		date_default_timezone_set($content['init']['settime']);
		if(isset($_POST['reusername'])){//2021/8/3 補印
			if($list!='-1'&&isset($list['name']['reprinttimelabel'])){
			}
			else{
				$list['name']['reprinttimelabel']="補印時間";
			}
			if($remarks[0]['UPDATEDATETIME']!=null&&$remarks[0]['UPDATEDATETIME']!=''&&$remarks[0]['UPDATEDATETIME']!='0'){
				if($list!='-1'&&isset($list['name']['saletimelabel'])){
					$document1->setValue('saletime',$list['name']['saletimelabel'].':'.(substr($remarks[0]['UPDATEDATETIME'],8,2).':'.substr($remarks[0]['UPDATEDATETIME'],10,2).':'.substr($remarks[0]['UPDATEDATETIME'],12,2)).'('.$list['name']['plus'].(sizeof($times)-1).")\r\n".$list['name']['reprinttimelabel'].":".date('Y/m/d H:i:s'));
				}
				else{
					$document1->setValue('saletime','結帳(加點)時間:'.(substr($remarks[0]['UPDATEDATETIME'],8,2).':'.substr($remarks[0]['UPDATEDATETIME'],10,2).':'.substr($remarks[0]['UPDATEDATETIME'],12,2)).'(加點'.(sizeof($times)-1).")\r\n".$list['name']['reprinttimelabel'].":".date('Y/m/d H:i:s'));
				}
			}
			else{
				$document1->setValue('saletime',$list['name']['reprinttimelabel'].":".date('Y/m/d H:i:s'));
			}
		}
		else{
			if($list!='-1'&&isset($list['name']['saletimelabel'])){
				$document1->setValue('saletime',$list['name']['saletimelabel'].':'.date('H:i:s').'('.$list['name']['plus'].(sizeof($times)-1).')');
			}
			else{
				$document1->setValue('saletime','結帳(加點)時間:'.date('H:i:s').'(加點'.(sizeof($times)-1).')');
			}
		}
	}
	else{
		date_default_timezone_set($content['init']['settime']);
		if(isset($_POST['reusername'])){//2021/8/3 補印
			if($list!='-1'&&isset($list['name']['reprinttimelabel'])){
			}
			else{
				$list['name']['reprinttimelabel']="補印時間";
			}
			if($remarks[0]['UPDATEDATETIME']!=null&&$remarks[0]['UPDATEDATETIME']!=''&&$remarks[0]['UPDATEDATETIME']!='0'){
				if($list!='-1'&&isset($list['name']['saletimelabel'])){
					$document1->setValue('saletime',$list['name']['saletimelabel'].':'.(substr($remarks[0]['UPDATEDATETIME'],8,2).':'.substr($remarks[0]['UPDATEDATETIME'],10,2).':'.substr($remarks[0]['UPDATEDATETIME'],12,2))."\r\n".$list['name']['reprinttimelabel'].":".date('Y/m/d H:i:s'));
				}
				else{
					$document1->setValue('saletime','結帳(加點)時間:'.(substr($remarks[0]['UPDATEDATETIME'],8,2).':'.substr($remarks[0]['UPDATEDATETIME'],10,2).':'.substr($remarks[0]['UPDATEDATETIME'],12,2))."\r\n".$list['name']['reprinttimelabel'].":".date('Y/m/d H:i:s'));
				}
			}
			else{
				if($list!='-1'&&isset($list['name']['saletimelabel'])){
					$document1->setValue('saletime',$list['name']['saletimelabel'].':'.(substr($remarks[0]['CREATEDATETIME'],8,2).':'.substr($remarks[0]['CREATEDATETIME'],10,2).':'.substr($remarks[0]['CREATEDATETIME'],12,2))."\r\n".$list['name']['reprinttimelabel'].":".date('Y/m/d H:i:s'));
				}
				else{
					$document1->setValue('saletime','結帳(加點)時間:'.(substr($remarks[0]['CREATEDATETIME'],8,2).':'.substr($remarks[0]['CREATEDATETIME'],10,2).':'.substr($remarks[0]['CREATEDATETIME'],12,2))."\r\n".$list['name']['reprinttimelabel'].":".date('Y/m/d H:i:s'));
				}
			}
		}
		else{
			if($list!='-1'&&isset($list['name']['saletimelabel'])){
				$document1->setValue('saletime',$list['name']['saletimelabel'].':'.date('H:i:s'));
			}
			else{
				$document1->setValue('saletime','結帳(加點)時間:'.date('H:i:s'));
			}
		}
	}

	if($consecnumber==''){
		$persontext="";
		$context="";
		if(isset($print['clientlist']['numberman'])&&$print['clientlist']['numberman']=='1'){
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
		}
		else{
		}

		
		if(!isset($print['clientlist']['orderman'])||$print['clientlist']['orderman']=='1'){
			if($list!='-1'){
				$context=$list['name']['orderman'].":".$_POST['username']."(".$_POST['machinetype'].")";
			}
			else{
				$context="點餐人員:".$_POST['username']."(".$_POST['machinetype'].")";
			}
			if(isset($_POST['reusername'])){//2021/8/3
				if($list!='-1'){
					$context .= "\r\n".$list['name']['reorderman'].":".$_POST['reusername']."(".$_POST['remachinetype'].")";
				}
				else{
					$context .= "\r\n補印人員:".$_POST['reusername']."(".$_POST['remachinetype'].")";
				}
			}
			else{
			}
		}
		else{
		}

		if($persontext!=""&&$context!=""){
			$document1->setValue('consecnumber',$persontext."\r\n".$context);
		}
		else if($persontext!=""||$context!=""){
			$document1->setValue('consecnumber',$persontext.$context);
		}
		else{
			$document1->setValue('consecnumber',"");
		}
	}
	else{
		$persontext="";
		$context="";
		if(isset($print['clientlist']['numberman'])&&$print['clientlist']['numberman']=='1'){
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
		}
		else{
		}

		if(!isset($print['clientlist']['orderman'])||$print['clientlist']['orderman']=='1'){
			if($list!='-1'){
				$context=$list['name']['orderman'].":".$_POST['username']."(".$_POST['machinetype'].")";
			}
			else{
				$context="點餐人員:".$_POST['username']."(".$_POST['machinetype'].")";
			}
			if(isset($_POST['reusername'])){//2021/8/3
				if($list!='-1'){
					$context .= "\r\n".$list['name']['reorderman'].":".$_POST['reusername']."(".$_POST['remachinetype'].")";
				}
				else{
					$context .= "\r\n補印人員:".$_POST['reusername']."(".$_POST['remachinetype'].")";
				}
			}
			else{
			}
		}
		else{
		}

		if($persontext!=""&&$context!=""){
			$document1->setValue('consecnumber',$consecnumber.$persontext."\r\n".$context);
		}
		else if($persontext!=""){
			$document1->setValue('consecnumber',$consecnumber."\r\n".$persontext);
		}
		else if($context!=""){
			$document1->setValue('consecnumber',$consecnumber."\r\n".$context);
		}
		else{
			$document1->setValue('consecnumber',$consecnumber);
		}
	}

	$document1->setValue('story',$data['basic']['storyname']);
	$tindex=0;
	$temporderlist=1;
	$table='';
	
	//print_r($remarks);
	//2020/9/18 發票資訊
	if(isset($remarks[0])&&isset($remarks[0]['INVOICENUMBER'])&&$remarks[0]['INVOICENUMBER']!=''){
		//考慮是否放入載具、統編資訊
		$table .= '<w:p w:rsidR="008C4EB0" w:rsidRPr="001E039E" w:rsidRDefault="00FC09EC" w:rsidP="007C4B46"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:jc w:val="left"/><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial"/><w:b/><w:sz w:val="'.$print['clientlist']['invsize'].'"/><w:szCs w:val="'.$print['clientlist']['invsize'].'"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:cs="Arial" w:hint="eastAsia"/><w:b/><w:sz w:val="'.$print['clientlist']['invsize'].'"/><w:szCs w:val="'.$print['clientlist']['invsize'].'"/></w:rPr><w:t>';
		if($list!='-1'&&isset($list['name']['invoicenumber'])){
			$table.=$list['name']['invoicenumber'].":";
		}
		else{
			$table .= "發票號碼:";
		}
		$table .= $remarks[0]['INVOICENUMBER'].'</w:t></w:r></w:p>';
	}
	else{
	}

	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="1250"/><w:gridCol w:w="1250"/></w:tblGrid>';
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
	for($i=0;$i<sizeof($_POST['no']);$i++){
		if(isset($print['clientlist']['replacename'])&&$print['clientlist']['replacename']=='1'){//2021/9/2 列印品項名稱以0>>原品項名稱1>>列印名稱為主
			if(isset($menu[$_POST['no'][$i]]['printname'])&&$menu[$_POST['no'][$i]]['printname']!=''){
				$_POST['name'][$i]=$menu[$_POST['no'][$i]]['printname'];
			}
			else{
			}
			//2022/5/18 尋找價格名稱的列印名稱
			for($mn=1;$mn<=6;$mn++){
				if($menu[$_POST['no'][$i]]['mname'.$mn.'1']==$_POST['mname1'][$i]&&$menu[$_POST['no'][$i]]['money'.$mn]==$_POST['unitprice'][$i]&&isset($menu[$_POST['no'][$i]]['mname'.$mn.'printname'])&&$menu[$_POST['no'][$i]]['mname'.$mn.'printname']!=''){//2022/5/18 尋找對應的價格名稱，並如有設定列印名稱則取代
					$_POST['mname1'][$i]=$menu[$_POST['no'][$i]]['mname'.$mn.'printname'];
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
		$totalqty=intval($totalqty)+intval($_POST['number'][$i]);
		if(isset($_POST['tempbuytype'])&&$_POST['tempbuytype']=='2'&&isset($_POST['templistitem'][$i])){
			$tempindex++;
		}
		else if(isset($pti[$menu[$_POST['no'][$i]]['printtype']]['clientlist'.$listtype])&&$pti[$menu[$_POST['no'][$i]]['printtype']]['clientlist'.$listtype]=='0'){
			$tempindex++;
		}
		else{
			//$newitem++;
			$temporderlist=0;
			$oinvitemlen=0;
			$straw=999;
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
			
			//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
			if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
			}
			else{
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0)||(strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0))){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if($i==(sizeof($_POST['no'])-1)&&strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
			}

			//判斷吸管屬性權重
			if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&isset($menu[$_POST['no'][$i]]['straw'])&&$menu[$_POST['no'][$i]]['straw']!=''&&$menu[$_POST['no'][$i]]['straw']!=null&&intval($menu[$_POST['no'][$i]]['straw'])<intval($straw)){
				$straw=intval($menu[$_POST['no'][$i]]['straw']);
			}
			else{
			}
			$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
			if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
				if(strlen($_POST['mname1'][$i])==0){
					if($_POST['order'][$i]=='－'){
						$table .= '－'.$_POST['name'][$i];//2022/11/3 .'x'.$_POST['number'][$i]
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$_POST['name2'][$i]!=''){
							$table .= "\r\n－".$_POST['name2'][$i];//2022/11/3 .'x'.$_POST['number'][$i]
						}
						else{
						}
					}
					else{
						$table .= $_POST['name'][$i];//2022/11/3 .'x'.$_POST['number'][$i]
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$_POST['name2'][$i]!=''){
							$table .= "\r\n".$_POST['name2'][$i];//2022/11/3 .'x'.$_POST['number'][$i]
						}
						else{
						}
					}
				}
				else{
					if($_POST['order'][$i]=='－'){
						$table .= '－'.$_POST['name'][$i].'('.$_POST['mname1'][$i].')';//2022/11/3 x'.$_POST['number'][$i]
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$_POST['name2'][$i]!=''){
							$table .= "\r\n－".$_POST['name2'][$i].'('.$_POST['mname2'][$i].')';//2022/11/3 x'.$_POST['number'][$i]
						}
						else{
						}
					}
					else{
						$table .= $_POST['name'][$i].'('.$_POST['mname1'][$i].')';//2022/11/3 x'.$_POST['number'][$i]
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$_POST['name2'][$i]!=''){
							$table .= "\r\n".$_POST['name2'][$i].'('.$_POST['mname2'][$i].')';//2022/11/3 x'.$_POST['number'][$i]
						}
						else{
						}
					}
				}
			}
			else{
				if(strlen($_POST['mname1'][$i])==0){
					if($_POST['order'][$i]=='－'){
						$table .= '－'.$_POST['name'][$i].'x'.$_POST['number'][$i];
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$_POST['name2'][$i]!=''){
							$table .= "\r\n－".$_POST['name2'][$i].'x'.$_POST['number'][$i];
						}
						else{
						}
					}
					else{
						$table .= $_POST['name'][$i].'x'.$_POST['number'][$i];
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$_POST['name2'][$i]!=''){
							$table .= "\r\n".$_POST['name2'][$i].'x'.$_POST['number'][$i];
						}
						else{
						}
					}
				}
				else{
					if($_POST['order'][$i]=='－'){
						$table .= '－'.$_POST['name'][$i].'('.$_POST['mname1'][$i].')x'.$_POST['number'][$i];
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$_POST['name2'][$i]!=''){
							$table .= "\r\n－".$_POST['name2'][$i].'('.$_POST['mname2'][$i].')x'.$_POST['number'][$i];
						}
						else{
						}
					}
					else{
						$table .= $_POST['name'][$i].'('.$_POST['mname1'][$i].')x'.$_POST['number'][$i];
						if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&$_POST['name2'][$i]!=''){
							$table .= "\r\n".$_POST['name2'][$i].'('.$_POST['mname2'][$i].')x'.$_POST['number'][$i];
						}
						else{
						}
					}
				}
			}
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
			
			//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
			if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
			}
			else{
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0)||(strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0))){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if($i==(sizeof($_POST['no'])-1)&&strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
			}

			$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';

			//2022/11/8 該部分保留，一併更改至原始版本//2022/11/3
			if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//2022/11/3 單價金額加上備註金額//備註統一一行
				$table .= $content['init']['frontunit'].intval(($_POST['subtotal'][$i]+$_POST['discount'][$i])/$_POST['number'][$i]).$content['init']['unit'];
			}
			else{
				$table .= $content['init']['frontunit'].$_POST['unitprice'][$i].$content['init']['unit'];
			}

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
			
			//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
			if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
			}
			else{
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0)||(strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0))){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if($i==(sizeof($_POST['no'])-1)&&strlen($_POST['taste1'][$i])==0&&$_POST['discount'][$i]==0){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
			}

			$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
			
			//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
			if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
			}
			else{
				$table .= $content['init']['frontunit'].($_POST['subtotal'][$i]+$_POST['discount'][$i]).$content['init']['unit'];
			}

			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= "</w:tr>";
			if(strlen($_POST['taste1'][$i])>0){
				$tasteno=preg_split('/,/',$_POST['taste1'][$i]);
				$temp=preg_split('/,/',$_POST['taste1name'][$i]);
				$temp2=preg_split('/,/',$_POST['taste1number'][$i]);
				$temp3=preg_split('/,/',$_POST['taste1price'][$i]);
				$linetaste='';
				for($t=0;$t<sizeof($temp);$t++){
					if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'){//備註統一一行
						//判斷吸管屬性權重
						if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&substr($tasteno[$t],0,5)!='99999'&&isset($taste[$tasteno[$t]]['straw'])&&$taste[$tasteno[$t]]['straw']!=''&&$taste[$tasteno[$t]]['straw']!=null&&intval($taste[$tasteno[$t]]['straw'])<intval($straw)){
							$straw=intval($taste[$tasteno[$t]]['straw']);
						}
						else{
						}
						if($tasteno[$t]!='999991'){
							$tt=preg_split('/\//',$temp[$t]);

							/*if(isset($tt[1])){
								if(intval($temp2[$t])==1){
									$tt[0]=$tt[0];
								}
								else{
									$tt[0]=$tt[0].'*'.$temp2[$t];
								}
							}
							else{
							}*/

							if($linetaste==''){
								$linetaste = '　+'.$tt[0];
							}
							else{
								$linetaste .= ','.$tt[0];
							}

							if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$linetaste .= '/ '.$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])==1){
								}
								else{
									$linetaste .= '*'.$temp2[$t];
								}
							}
							else{
							}
						}
						else{
							if($linetaste==''){
								if($print['item']['tastefront']=='1'){
									$linetaste = '　+註:'.$temp[$t];
								}
								else{
									$linetaste = '　+'.$temp[$t];
								}
							}
							else{
								if($print['item']['tastefront']=='1'){
									$linetaste .= ',註:'.$temp[$t];
								}
								else{
									$linetaste .= ','.$temp[$t];
								}
							}
						}

						/*if(isset($tt[1])){
						}
						else{
							if(intval($temp2[$t])==1){
							}
							else{
								$linetaste .= '*'.$temp2[$t];
							}
						}*/
					}
					else{//備註一項一行
						$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
						$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
						
						//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
						if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
						}
						else{
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0)||($t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0))){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else if($i==(sizeof($_POST['no'])-1)&&$t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
						}

						//判斷吸管屬性權重
						if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&substr($tasteno[$t],0,5)!='99999'&&isset($taste[$tasteno[$t]]['straw'])&&$taste[$tasteno[$t]]['straw']!=''&&$taste[$tasteno[$t]]['straw']!=null&&intval($taste[$tasteno[$t]]['straw'])<intval($straw)){
							$straw=intval($taste[$tasteno[$t]]['straw']);
						}
						else{
						}
						$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
						//$table .= '-'.$_POST['taste1'][$t];

						if($tasteno[$t]!='999991'){
							$tt=preg_split('/\//',$temp[$t]);
						
							/*if(isset($tt[1])){
								if(intval($temp2[$t])==1){
									$tt[0]=$tt[0];
								}
								else{
									$tt[0]=$tt[0].'*'.$temp2[$t];
								}
							}
							else{
							}*/

							/*if(intval($temp2[$t])==1){
								$tt[0]=$tt[0];
							}
							else{
								$tt[0]=$tt[0].'*'.$temp2[$t];
							}*/
							$table .= '　+'.$tt[0];
							if(isset($print['clientlist']['secname'])&&$print['clientlist']['secname']=='1'&&isset($tt[1])&&$tt[1]!=''){
								$table .= "/ ".$tt[1];
							}
							else if(isset($tt[1])&&$tt[1]!=''){
								if(intval($temp2[$t])==1){
								}
								else{
									$table .= '*'.$temp2[$t];
								}
							}
							else{
							}
						}
						else{
							if($print['item']['tastefront']=='1'){
								$table .= '　+註:'.$temp[$t];
							}
							else{
								$table .= '　+'.$temp[$t];
							}
						}
						/*if(isset($tt[1])){
						}
						else{
							if(intval($temp2[$t])==1){
							}
							else{
								$table .= '*'.$temp2[$t];
							}
						}*/
						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
						
						//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
						if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
						}
						else{
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0)||($t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0))){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else if($i==(sizeof($_POST['no'])-1)&&$t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
						}

						$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						
						//2022/11/8 備註價格部分更新至原始版本，備註不顯示單價，只顯示小計//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
						//if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
							if(intval(($temp3[$t]*$temp2[$t]))==0){
							}
							else{
								$table .= $content['init']['frontunit'].($temp3[$t]*$temp2[$t]).$content['init']['unit'];
							}
						/*}
						else{
							if(intval($temp3[$t])==0){
							}
							else{
								$table .= $temp3[$t];
							}
						}*/

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
						
						//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
						if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
						}
						else{
							if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0)||($t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0))){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
							else if($i==(sizeof($_POST['no'])-1)&&$t==(sizeof($temp)-1)&&$_POST['discount'][$i]==0){
								$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
							}
						}

						$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
						
						//2022/11/8 備註價格部分更新至原始版本，備註不顯示單價，只顯示小計//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
						//if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
						/*}
						else{
							if(intval(($temp3[$t]*$temp2[$t]))==0){
							}
							else{
								$table .= $content['init']['frontunit'].($temp3[$t]*$temp2[$t]).$content['init']['unit'];
							}
						}*/

						$table .= "</w:t></w:r></w:p></w:tc>";
						$table .= "</w:tr>";
					}
				}
				if(isset($print['item']['tastetype'])&&$print['item']['tastetype']=='2'&&$linetaste!=''){//備註統一一行
					$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$table .= '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:gridSpan w:val="3"/>';
					//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
					}
					else{
						if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0)||$_POST['discount'][$i]==0)){
							$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
						}
						else if($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0){
							$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
						}
					}
					$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
					
					$table .= $linetaste;
					
					$table .= "</w:t></w:r></w:p></w:tc>";
					$table .= "</w:tr>";
				}
				else{
					//備註一項一行
				}
			}
			else{
			}
			
			//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
			if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0)||$_POST['discount'][$i]==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0&&$_POST['order'][$i+1]!='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0&&$_POST['order'][$i+1]=='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="dotted" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= 'x'.$_POST['number'][$i];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0)||$_POST['discount'][$i]==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0&&$_POST['order'][$i+1]!='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0&&$_POST['order'][$i+1]=='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="dotted" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')&&(($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0)||$_POST['discount'][$i]==0)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if($i==(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0&&$_POST['order'][$i+1]!='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['discount'][$i]==0&&$_POST['order'][$i+1]=='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="dotted" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].($_POST['subtotal'][$i]+$_POST['discount'][$i]).$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
			}
			
			if($_POST['discount'][$i]>0){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="3200" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($_POST['no'])-1)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['order'][$i+1]!='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['order'][$i+1]=='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="dotted" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$print['item']['clientsize'].'"/><w:szCs w:val="'.$print['item']['clientsize'].'"/></w:rPr><w:t>';
				//$table .= '-'.$_POST['taste1'][$t];
				if($list!='-1'){
					$table .= '　+'.$list['name']['itemdis'];
				}
				else{
					$table .= '　+優惠折抵';
				}
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($_POST['no'])-1)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['order'][$i+1]!='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['order'][$i+1]=='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="dotted" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="900" w:type="pct"/>';
				if((isset($print['clientlist']['itemendline'])&&$print['clientlist']['itemendline']=='1')||$i==(sizeof($_POST['no'])-1)){
					$table .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['order'][$i+1]!='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="single" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else if(isset($print['clientlist']['clitype'])&&$print['clientlist']['clitype']=='2'&&$i<(sizeof($_POST['no'])-1)&&$_POST['order'][$i+1]=='－'){//2022/11/8 有人事先沒與客人溝通，直接更新，客人回饋：這樣多一行很"浪費紙"？？因此增加新舊版參數//2022/11/3
					$table .= '<w:tcBorders><w:bottom w:val="dotted" w:sz="4" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				else{
				}
				$table .= '<w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].'-'.$_POST['discount'][$i].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
			}
			//計算吸管數量
			if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'&&intval($straw)!=999){
				$strawarray[intval($straw)]['number']=intval($strawarray[intval($straw)]['number'])+1*$_POST['number'][$i];
			}
			else{
			}
		}
	}
	if(isset($content['init']['comstraw'])&&$content['init']['comstraw']=='1'&&$strawarray!='-1'){
		foreach($strawarray as $st){
			if(intval($st['number'])>0){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
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
	//echo 'sizeof:'.sizeof($_POST['no'])."\r\n";
	//echo 'length:'.$tempindex."\r\n";
	$table .= '</w:tbl>';
	$table .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
	$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';

	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
	if($list!='-1'){
		$table .= $list['name']['qty'];
	}
	else{
		$table .= '商品數量';
	}
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
	$table .= $_POST['totalnumber'];
	$table .= "</w:t></w:r></w:p></w:tc>";
	$table .= "</w:tr>";
	if(isset($_POST['memberdis'])&&isset($_POST['listdis1'])&&isset($_POST['listdis2'])&&($_POST['memberdis']+$_POST['listdis1']+$_POST['listdis2'])>0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$table .= $list['name']['listdis'];
		}
		else{
			$table .= '優惠折抵';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].'-'.($_POST['memberdis']+$_POST['listdis1']+$_POST['listdis2']).$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
	if(!isset($_POST['reusername'])||!isset($_POST['listtotal'])){
		$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'" AND ITEMCODE="autodis"';
	}
	else{//2021/8/3 補印結帳明細
		$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.str_pad($consecnumber,6,'0',STR_PAD_LEFT).'" AND ITEMCODE="autodis"';
	}
	//echo $sql;
	$autodis=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	//echo isset($autodis[0]['AMT']);
	if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
		$autodiscontent=preg_split('/,/',$autodis[0]['ITEMGRPCODE']);
		$autodispremoney=preg_split('/,/',$autodis[0]['ITEMGRPNAME']);
		//echo 'auto='.sizeof($autodiscontent);
		for($di=0;$di<sizeof($autodiscontent);$di++){
			if(isset($discount[$autodiscontent[$di]])){
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$table .= $discount[$autodiscontent[$di]]['name'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].'-'.$autodispremoney[$di].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
			else{
				$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$table .= '系統優惠';
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
				$table .= $content['init']['frontunit'].'-'.$autodispremoney[$di].$content['init']['unit'];
				$table .= "</w:t></w:r></w:p></w:tc>";
				$table .= "</w:tr>";
			}
		}
	}
	else{
	}
	if(isset($_POST['charge'])&&$_POST['charge']>0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$table .= $list['name']['charge'];
		}
		else{
			$table .= '服務費';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$_POST['charge'].$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	if(isset($_POST['floorspan'])&&$_POST['floorspan']>0){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$table .= $list['name']['floorspan'];
		}
		else{
			$table .= '低銷差價';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$_POST['floorspan'].$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= "</w:tr>";
	}
	else{
	}
	/*if(isset($_POST['reusername'])&&isset($_POST['listtotal'])){
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$table .= $list['name']['total'];
		}
		else{
			$table .= '應收金額';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$table .= $content['init']['frontunit'].$_POST['should'].$content['init']['unit'];
		$table .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	else */if(isset($_POST['listtotal'])){
		$checktime=0;
		$temptable='';
		if(isset($_POST['cashmoney'])&&floatval($_POST['cashmoney'])>0){
			$checktime++;
			$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
			if($list!='-1'){
				$temptable .= $list['name']['cashmoney'];
			}
			else{
				$temptable .= '現金';
			}
			$temptable .= "</w:t></w:r></w:p></w:tc>";
			$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
			$temptable .= $content['init']['frontunit'].(floatval($_POST['already'])-floatval($_POST['cash'])-floatval($_POST['other'])-floatval($_POST['otherfix'])).$content['init']['unit'];
			$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
		}
		else{
		}
		if(isset($_POST['cash'])&&floatval($_POST['cash'])>0){
			$checktime++;
			$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
			if($list!='-1'){
				$temptable .= $list['name']['cash'];
			}
			else{
				$temptable .= '信用卡';
			}
			$temptable .= "</w:t></w:r></w:p></w:tc>";
			$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
			$temptable .= $content['init']['frontunit'].$_POST['cash'].$content['init']['unit'];
			$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
		}
		else{
		}
		if((isset($_POST['other'])&&floatval($_POST['other'])>0)||(isset($_POST['otherfix'])&&floatval($_POST['otherfix'])>0)){
			$otherpay=parse_ini_file('../../../database/otherpay.ini',true);
			$mapopay=array();
			foreach($otherpay as $op){
				if(isset($op['dbname'])&&(!isset($op['location'])||$op['location']=='CST011')){
					$mapopay[$op['dbname']]=$op['name'];
				}
				else if(isset($op['dbname'])){
					$mapopay[$op['location']]=$op['name'];
				}
				else{
					continue;
				}
			}
			$otherarray=preg_split('/,/',$_POST['otherstring']);
			foreach($otherarray as $oa){
				//echo $oa;
				$toa=preg_split('/:/',$oa);
				//print_r($toa);
				$toan=preg_split('/-/',$toa[0]);
				//print_r($toan);
				$toav=preg_split('/=/',$toa[1]);
				//print_r($toav);
				if($toan[0]=='intellaother'){
					$intellaname=parse_ini_file('../api/intella/data/methodmap.ini',true);
					$checktime++;
					$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					if(isset($intellaname['map'][$toan[1]])){
						$temptable .= $intellaname['map'][$toan[1]];
					}
					else{
						$temptable .= '無對應方式';
					}
					$temptable .= "</w:t></w:r></w:p></w:tc>";
					$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
					$temptable .= $toav[0];
					$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
				}
				else if($toan[0]=='nidinother'){
					$nidinname=parse_ini_file('../api/nidin/paymentmethod.ini',true);
					$checktime++;
					$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					if(isset($nidinname['method'][$toan[1]])){
						$temptable .= $nidinname['method'][$toan[1]];
					}
					else{
						$temptable .= '無對應方式';
					}
					$temptable .= "</w:t></w:r></w:p></w:tc>";
					$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
					$temptable .= $toav[0];
					$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
				}
				else if(floatval($toav[1])>0){
					$checktime++;
					$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
					$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
					if($toan[0]=='CST011'){
						$temptable .= $mapopay[$toan[1]];
					}
					else{
						$temptable .= $mapopay[$toan[0]];
					}
					$temptable .= "</w:t></w:r></w:p></w:tc>";
					$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
					$temptable .= $toav[0];
					$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
				}
				else{
				}
			}
		}
		else{
		}
		if(isset($_POST['change'])&&floatval($_POST['change'])>0){
			$temptable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
			if($list!='-1'){
				$temptable .= $list['name']['change'];
			}
			else{
				$temptable .= '找零';
			}
			$temptable .= "</w:t></w:r></w:p></w:tc>";
			$temptable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
			$temptable .= $content['init']['frontunit'].$_POST['change'].$content['init']['unit'];
			$temptable .= "</w:t></w:r></w:p></w:tc></w:tr>";
		}
		else{
		}
		if(intval($checktime)>1||(isset($_POST['change'])&&floatval($_POST['change'])>0)||isset($_POST['printtempclient'])){
			$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
			if($list!='-1'){
				$table .= $list['name']['total'];
			}
			else{
				$table .= '應收金額';
			}
			$table .= "</w:t></w:r></w:p></w:tc>";
			$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
			if(isset($_POST['listtotal'])){
				$table .= $content['init']['frontunit'].$_POST['should'].$content['init']['unit'];
				$memamt=$content['init']['frontunit'].$_POST['should'].$content['init']['unit'];
			}
			else{
				if(isset($_POST['reusername'])){
					$table .= $content['init']['frontunit'].$_POST['total'].$content['init']['unit'];
					$memamt=$content['init']['frontunit'].$_POST['total'].$content['init']['unit'];
				}
				else if(isset($_POST['floorspan'])){
					if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
						$table .= $content['init']['frontunit'].($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']).$content['init']['unit'];
						$memamt=$content['init']['frontunit'].($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']).$content['init']['unit'];
					}
					else{
						$table .= $content['init']['frontunit'].($_POST['total']+$_POST['floorspan']+$_POST['charge']).$content['init']['unit'];
						$memamt=$content['init']['frontunit'].($_POST['total']+$_POST['floorspan']+$_POST['charge']).$content['init']['unit'];
					}
				}
				else{
					if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
						$table .= $content['init']['frontunit'].($_POST['total']+$_POST['charge']+$autodis[0]['AMT']).$content['init']['unit'];
						$memamt=$content['init']['frontunit'].($_POST['total']+$_POST['charge']+$autodis[0]['AMT']).$content['init']['unit'];
					}
					else{
						$table .= $content['init']['frontunit'].($_POST['total']+$_POST['charge']).$content['init']['unit'];
						$memamt=$content['init']['frontunit'].($_POST['total']+$_POST['charge']).$content['init']['unit'];
					}
				}
			}
			$table .= "</w:t></w:r></w:p></w:tc></w:tr>";
		}
		else{
		}
		$table .= $temptable;
	}
	else{
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$table .= $list['name']['total'];
		}
		else{
			$table .= '應收金額';
		}
		$table .= "</w:t></w:r></w:p></w:tc>";
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		if(isset($_POST['listtotal'])){
			$table .= $content['init']['frontunit'].$_POST['should'].$content['init']['unit'];
			$memamt=$content['init']['frontunit'].$_POST['should'].$content['init']['unit'];
		}
		else{
			if(isset($_POST['reusername'])){
				$table .= $content['init']['frontunit'].$_POST['total'].$content['init']['unit'];
				$memamt=$content['init']['frontunit'].$_POST['total'].$content['init']['unit'];
			}
			else if(isset($_POST['floorspan'])){
				if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
					$table .= $content['init']['frontunit'].($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']).$content['init']['unit'];
					$memamt=$content['init']['frontunit'].($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']).$content['init']['unit'];
				}
				else{
					$table .= $content['init']['frontunit'].($_POST['total']+$_POST['floorspan']+$_POST['charge']).$content['init']['unit'];
					$memamt=$content['init']['frontunit'].($_POST['total']+$_POST['floorspan']+$_POST['charge']).$content['init']['unit'];
				}
			}
			else{
				if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
					$table .= $content['init']['frontunit'].($_POST['total']+$_POST['charge']+$autodis[0]['AMT']).$content['init']['unit'];
					$memamt=$content['init']['frontunit'].($_POST['total']+$_POST['charge']+$autodis[0]['AMT']).$content['init']['unit'];
				}
				else{
					$table .= $content['init']['frontunit'].($_POST['total']+$_POST['charge']).$content['init']['unit'];
					$memamt=$content['init']['frontunit'].($_POST['total']+$_POST['charge']).$content['init']['unit'];
				}
			}
		}
		$table .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	if(isset($remarks[0]['RELINVOICENUMBER'])&&$remarks[0]['RELINVOICENUMBER']!=''&&$remarks[0]['RELINVOICENUMBER']!=NULL){//<w:gridSpan w:val="2"/>
		$table .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$table .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';

		$table .= $remarks[0]['RELINVOICENUMBER'];

		$table .= "</w:t></w:r></w:p></w:tc></w:tr>";
	}
	else{
	}
	$table .= "</w:tbl>";

	$memtable='';
	$memtable .= '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblCellMar><w:left w:w="0" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar><w:tblLook w:val="0000"/></w:tblPr><w:tblGrid><w:gridCol w:w="2500"/><w:gridCol w:w="2500"/></w:tblGrid>';
	if(isset($_POST['memno'])&&strlen($_POST['memno'])!=0&&(!isset($print['clientlist']['memberdata'])||$print['clientlist']['memberdata']=='1')){
		if(isset($content['init']['writememdata'.$listtype])&&$content['init']['writememdata'.$listtype]==1){
			//print_r($memdata);
			if(!isset($memdata)&&sizeof($memdata)==0&&(isset($_POST['memno'])&&strlen($_POST['memno'])!=0)){
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$memtable .= "offline";
				//$document1->setValue('memtel',$memdata[0]['tel']);
				$memtable .= "</w:t></w:r></w:p></w:tc>";
				$memtable .= "</w:tr>";
			}
			else{
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				if(isset($memdata[0]['setting'])&&$memdata[0]['setting']!=null&&strlen($memdata[0]['setting'])>0&&(!isset($print['clientlist']['memsetting'])||$print['clientlist']['memsetting']=='1')){
					$memtable .= $memdata[0]['name']."(".$memdata[0]['setting'].")";
					//$document1->setValue('memname',$memdata[0]['name']."(".$memdata[0]['setting'].")");
				}
				else{
					$memtable .= $memdata[0]['name'];
					//$document1->setValue('memname',$memdata[0]['name']);
				}
				$memtable .= "</w:t></w:r></w:p></w:tc>";
				$memtable .= "</w:tr>";
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$memtable .= $memdata[0]['tel'];
				//$document1->setValue('memtel',$memdata[0]['tel']);
				$memtable .= "</w:t></w:r></w:p></w:tc>";
				$memtable .= "</w:tr>";
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				if(preg_match('/;php;/',$memdata[0]['address'])){
					$addressarray=preg_split('/;php;/',$memdata[0]['address']);
					$memtable .= $addressarray[0];
				}
				else{
					if(preg_match('/\;\*\;/',$memdata[0]['address'])){
						$addselect=preg_split('/\;\*\;/',$memdata[0]['address']);
						if(isset($_POST['memaddno'])&&isset($addselect[$_POST['memaddno']-1])){
							$memtable .= $addselect[$_POST['memaddno']-1];
						}
						else{
							$memtable .= $addselect[0];
						}
					}
					else{
						$memtable .= $memdata[0]['address'];
					}
				}
				//$document1->setValue('memaddress',$memdata[0]['address']);
				$memtable .= "</w:t></w:r></w:p></w:tc>";
				$memtable .= "</w:tr>";
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/>';
				if(!isset($print['clientlist']['mempointmoney'])||$print['clientlist']['mempointmoney']=='1'){
				}
				else{
					$memtable .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
				}
				$memtable .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$memtable .= $memdata[0]['remark'];
				//$document1->setValue('memremarks',"備註:".$memdata[0]['remark']);
				$memtable .= "</w:t></w:r></w:p></w:tc>";
				$memtable .= "</w:tr>";
			}
		}
		else if(isset($remarks[0]['RELINVOICETIME'])&&$remarks[0]['RELINVOICETIME']!=''&&$remarks[0]['RELINVOICETIME']!=NULL){//2022/10/14 平台訂單儲存訂購人姓名與電話
			if((!isset($print['clientlist']['buyerdata'])||$print['clientlist']['buyerdata']=='1')&&substr($remarks[0]['RELINVOICENUMBER'],0,10)=='QuickClick'){//2022/10/14 是否列印訂購人的資訊(姓名與電話)
				$memdata=preg_split('/;/',$remarks[0]['RELINVOICETIME']);
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$memtable .= $memdata[0];
				$memtable .= "</w:t></w:r></w:p></w:tc>";
				$memtable .= "</w:tr>";
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$memtable .= $memdata[1];
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
		if(isset($remarks[0]['RELINVOICETIME'])&&$remarks[0]['RELINVOICETIME']!=''&&$remarks[0]['RELINVOICETIME']!=NULL){//2022/10/14 平台訂單儲存訂購人姓名與電話
			if((!isset($print['clientlist']['buyerdata'])||$print['clientlist']['buyerdata']=='1')&&substr($remarks[0]['RELINVOICENUMBER'],0,10)=='QuickClick'){//2022/10/14 是否列印訂購人的資訊(姓名與電話)
				$memdata=preg_split('/;/',$remarks[0]['RELINVOICETIME']);
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$memtable .= $memdata[0];
				$memtable .= "</w:t></w:r></w:p></w:tc>";
				$memtable .= "</w:tr>";
				$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
				$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:gridSpan w:val="2"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
				$memtable .= $memdata[1];
				$memtable .= "</w:t></w:r></w:p></w:tc>";
				$memtable .= "</w:tr>";
			}
			else{
			}
		}
		else{
		}
	}

	if(isset($_POST['state'])&&$_POST['state']=='success'&&(!isset($print['clientlist']['mempointmoney'])||$print['clientlist']['mempointmoney']=='1')){
		if(!isset($print['clientlist']['memberdata'])||$print['clientlist']['memberdata']=='1'){
		}
		else{
			$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
			$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
			$memtable .= $_POST['memname'];
			$memtable .= "</w:t></w:r></w:p></w:tc>";
			$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/><w:tcBorders><w:top w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
			$memtable .= $_POST['tel'];
			$memtable .= "</w:t></w:r></w:p></w:tc>";
			$memtable .= "</w:tr>";
			
		}
		$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';
		$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$memtable .= $list['name']['initpoint'];
		}
		else{
			$memtable .= '原先點數';
		}
		$memtable .= "</w:t></w:r></w:p></w:tc>";
		$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$memtable .= $_POST['initpoint'];
		$memtable .= "</w:t></w:r></w:p></w:tc>";
		$memtable .= "</w:tr>";
		$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';

		$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$memtable .= $list['name']['initmoney'];
		}
		else{
			$memtable .= '原先儲值金';
		}
		$memtable .= "</w:t></w:r></w:p></w:tc>";
		$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$memtable .= $_POST['initmoney'];
		$memtable .= "</w:t></w:r></w:p></w:tc>";
		$memtable .= "</w:tr>";
		$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';

		$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$memtable .= $list['name']['remainingpoint'];
		}
		else{
			$memtable .= '剩餘點數';
		}
		$memtable .= "</w:t></w:r></w:p></w:tc>";
		$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$memtable .= $_POST['remainingpoint'];
		$memtable .= "</w:t></w:r></w:p></w:tc>";
		$memtable .= "</w:tr>";
		$memtable .= '<w:tr w:rsidR="00DD6EF4" w:rsidTr="00ED1F78"><w:trPr><w:trHeight w:hRule="auto" w:val="284"/></w:trPr>';

		$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/>';
		if(!isset($print['clientlist']['memberdata'])||$print['clientlist']['memberdata']=='1'){
		}
		else{
			$memtable .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
		}
		$memtable .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="left"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="'.$print['item']['textfont'].'" w:eastAsia="'.$print['item']['textfont'].'" w:hAnsi="'.$print['item']['textfont'].'" w:hint="eastAsia"/><w:sz w:val="'.$clititle.'"/><w:szCs w:val="'.$clititle.'"/></w:rPr><w:t>';
		if($list!='-1'){
			$memtable .= $list['name']['remainingmoney'];
		}
		else{
			$memtable .= '剩餘儲值金';
		}
		$memtable .= "</w:t></w:r></w:p></w:tc>";
		$memtable .= '<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/>';
		if(!isset($print['clientlist']['memberdata'])||$print['clientlist']['memberdata']=='1'){
		}
		else{
			$memtable .= '<w:tcBorders><w:bottom w:val="dashSmallGap" w:sz="8" w:space="0" w:color="auto"/></w:tcBorders>';
		}
		$memtable .= '</w:tcPr><w:p w:rsidR="00DD6EF4" w:rsidRDefault="00155D44" w:rsidP="00ED1F78"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:ind w:left="40" w:rightChars="59" w:right="142"/><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Consolas" w:eastAsia="Consolas" w:hAnsi="Consolas" w:hint="eastAsia"/><w:sz w:val="'.$clicont.'"/><w:szCs w:val="'.$clicont.'"/></w:rPr><w:t>';
		$memtable .= $_POST['remainingmoney'];
		$memtable .= "</w:t></w:r></w:p></w:tc>";
		$memtable .= "</w:tr>";
	}
	else{
	}
	$memtable .= "</w:tbl>";
	$document1->setValue('memtable',$memtable);

	$document1->setValue('item',$table);
	if(isset($_POST['notconsecnumber'])){//用於暫出明細，不印consecnumber&saleno{
		if(file_exists('../../../print/clientintellaqrcode/'.$_POST['machinetype'].'.png')){
			$document1->replaceStrToQrcode('qrcode','../../../print/clientintellaqrcode/'.$_POST['machinetype'].'.png');
		}
		else{
			$document1->replaceStrToQrcode('qrcode','empty');
		}
	}
	else{
		$imgarray='empty';

		/*create barcode*/
		include('../../../tool/phpbarcode/src/BarcodeGenerator.php');
		include('../../../tool/phpbarcode/src/BarcodeGeneratorPNG.php');

		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();

		if(file_exists('../../../print/barcode')){
		}
		else{
			mkdir('../../../print/barcode');
		}

		//echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode(($_POST['bizdate'].$consecnumber), $generator::TYPE_CODE_128)) . '">';
		if(isset($data['basic']['barcodetext'])){
			$text=$data['basic']['barcodetext'];
		}
		else{
			$text="帳單編號";
		}
		file_put_contents('../../../print/barcode/'.$_POST['machinetype'].'.png', $generator->getBarcode(($_POST['bizdate'].$consecnumber), $generator::TYPE_CODE_128,$text));
		/*create barcode*/
		
		$imgarray=array('barcode'=>'../../../print/barcode/'.$_POST['machinetype'].'.png');

		if(isset($_POST['receipt'])&&$_POST['receipt']=='1'){//收據章
			if(file_exists('../../../print/receipt.png')){
				$imgarray['receipt']='../../../print/receipt.png';
			}
			else{
			}
		}
		else{
		}

		if(isset($print['clientlist']['tcqrcode'])&&$print['clientlist']['tcqrcode']=='1'&&isset($data['tcqrcode']['dep'])&&isset($data['tcqrcode']['dep'])){//串接騰雲商場POSQRcode
			include_once '../../../tool/phpqrcode/qrlib.php';
			if(file_exists('../../../print/apiposcode')){
			}
			else{
				mkdir('../../../print/apiposcode');
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
			QRcode::png('A+'.$qrcodestring.str_pad($consecnumber,'18','0',STR_PAD_LEFT),'../../../print/apiposcode/'.$consecnumber.'.png','H',6);
			$imgarray['qrcode']='../../../print/apiposcode/'.$consecnumber.'.png';
		}
		else if(isset($print['clientlist']['jdiqrcode'])&&$print['clientlist']['jdiqrcode']=='1'){//串接義美吉盛POSQRcode
			//print_r($_POST);
			if(file_exists('../../../print/apiposcode')){
			}
			else{
				mkdir('../../../print/apiposcode');
			}
			if(!isset($data['jdiqrcode']['printtext'])){
				$data['jdiqrcode']['printtext']="義美吉盛專用";
			}
			else{
			}
			//date_default_timezone_set('Asia/Taipei');
			date_default_timezone_set($content['init']['settime']);
			if(isset($_POST['ininv'])&&intval($_POST['ininv'])>0&&isset($data['jdiqrcode']['invcode'])&&$data['jdiqrcode']['invcode']!=''){//應稅金額
				$jdibarcode='A+'.str_pad($data['jdiqrcode']['invcode'],7,'0',STR_PAD_LEFT).str_pad($_POST['ininv'],7,'0',STR_PAD_LEFT);
				$jdibarcodename='../../../print/apiposcode/'.substr(date('YmdHis'),2).'ininv.png';
				file_put_contents($jdibarcodename, $generator->getBarcode($jdibarcode, $generator::TYPE_CODE_128,$data['jdiqrcode']['printtext']));
				/*create barcode*/

				$imgarray['barcodejdiininv']=$jdibarcodename;
			}
			else{
			}
			if(isset($_POST['freeinv'])&&intval($_POST['freeinv'])>0&&isset($data['jdiqrcode']['notinvcode'])&&$data['jdiqrcode']['notinvcode']!=''){//免稅金額
				$jdibarcode='A+'.str_pad($data['jdiqrcode']['notinvcode'],7,'0',STR_PAD_LEFT).str_pad($_POST['freeinv'],7,'0',STR_PAD_LEFT);
				$jdibarcodename='../../../print/apiposcode/'.substr(date('YmdHis'),2).'freeinv.png';
				file_put_contents($jdibarcodename, $generator->getBarcode($jdibarcode, $generator::TYPE_CODE_128,$data['jdiqrcode']['printtext']));
				/*create barcode*/

				$imgarray['barcodejdifreeinv']=$jdibarcodename;
			}
			else{
			}
		}
		else if(isset($print['clientlist']['sogobarcode'])&&$print['clientlist']['sogobarcode']=='1'){//串接sogoPOSQRcode
			if(file_exists('../../../print/apiposcode')){
			}
			else{
				mkdir('../../../print/apiposcode');
			}
			if(!isset($data['sogobarcode']['dep'])){
				$data['sogobarcode']['dep']='';
			}
			else{
			}
			$qrcodestring=str_pad($data['sogobarcode']['dep'],'8','0',STR_PAD_LEFT);
			if(isset($_POST['listtotal'])){
				$qrcodestring .= str_pad($_POST['should'],'5','0',STR_PAD_LEFT);
			}
			else{
				if(isset($_POST['floorspan'])){
					if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
						$qrcodestring.=str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']+$autodis[0]['AMT']),'5','0',STR_PAD_LEFT);
					}
					else{
						$qrcodestring.=str_pad(($_POST['total']+$_POST['floorspan']+$_POST['charge']),'5','0',STR_PAD_LEFT);
					}
				}
				else{
					if(sizeof($autodis)>0&&isset($autodis[0]['AMT'])){
						$qrcodestring.=str_pad(($_POST['total']+$_POST['charge']+$autodis[0]['AMT']),'5','0',STR_PAD_LEFT);
					}
					else{
						$qrcodestring.=str_pad(($_POST['total']+$_POST['charge']),'5','0',STR_PAD_LEFT);
					}
				}
			}
			$sogobarcodename='../../../print/apiposcode/'.$consecnumber.'.png';
			if(!isset($data['sogobarcode']['printtext'])){
				$data['sogobarcode']['printtext']="SOGO專用";
			}
			else{
			}
			file_put_contents($sogobarcodename, $generator->getBarcode($qrcodestring, $generator::TYPE_CODE_128,$data['sogobarcode']['printtext']));
			$imgarray['sogobarcode']=$sogobarcodename;
		}
		else{
		}
		//print_r($imgarray);

		$document1->replaceStrToMyImg('qrcode',$imgarray);
	}
	//date_default_timezone_set('Asia/Taipei');
	date_default_timezone_set($content['init']['settime']);
	$datetime=date('YmdHis');
	$y=date('Y');
	$m=date('m');
	$d=date('d');
	$h=date('H');
	$i=date('i');
	$s=date('s');

	//$filename=substr($datetime,0,8);
	$filename=date('YmdHis');
	if(!isset($_POST['printtempclient'])&&$_POST['tempbuytype']=='2'&&isset($_POST['templistitem'])&&sizeof($_POST['templistitem'])==sizeof($_POST['no'])){
		//$document1->save("../../../print/read/clientlist_".$consecnumber."_".$filename.".docx");
		$document1->save("../../../print/read/delete_".$_POST['machinetype']."clientlist1_".$listtype."_".$consecnumber.".docx");
	}
	else if(!isset($_POST['printtempclient'])&&isset($_POST['printclientlist'])&&$_POST['printclientlist']=='0'&&isset($_POST['listtotal'])){//結帳情況
		//$document1->save("../../../print/read/clientlist_".$consecnumber."_".$filename.".docx");
		$document1->save("../../../print/read/delete_".$_POST['machinetype']."clientlist2_".$listtype."_".$consecnumber.".docx");
	}
	else if(!isset($_POST['printtempclient'])&&isset($_POST['printclientlist'])&&$_POST['printclientlist']=="0*"){//現金結帳情況，因為沒有結帳畫面，因此變更值來方便判斷
		//$document1->save("../../../print/read/clientlist_".$consecnumber."_".$filename.".docx");
		$document1->save("../../../print/read/delete_".$_POST['machinetype']."clientlist3_".$listtype."_".$consecnumber.".docx");
	}
	else{
		if(!isset($_POST['printtempclient'])&&((isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1'&&isset($_POST['listtotal'])&&$print['item']['clientlist'.$listtype]=='0')||(isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1*'&&$print['item']['clientlist'.$listtype]=='0')||(!isset($_POST['listtotal'])&&isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1'&&$print['item']['clientlist'.$listtype.'temp']=='0'))){
			//$document1->save("../../../print/read/clientlist_".$consecnumber."_".$filename.".docx");
			$document1->save("../../../print/read/delete_".$_POST['machinetype']."clientlist4_".$listtype."_".$consecnumber.".docx");
		}
		else{
			if(isset($looptype)&&($looptype=='2'||$looptype=='4')){
				//$document1->save("../../../print/read/clientlist_".$consecnumber."_".$filename.".docx");
				$document1->save("../../../print/read/delete_".$_POST['machinetype']."clientlist5_".$listtype."_".$consecnumber.".docx");
			}
			else if($tempindex==sizeof($_POST['no'])){
				$document1->save("../../../print/read/delete_".$_POST['machinetype']."clientlist6_".$listtype."_".$consecnumber.".docx");
			}
			else{
				//if($newitem>0){
					//echo "clientlist_".$consecnumber."_".$filename.".docx";
					//$document1->save("../../../print/noread/clientlist_".$consecnumber."_".$filename.".docx");
					if((isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1'&&isset($_POST['listtotal']))||(isset($_POST['printclientlist'])&&$_POST['printclientlist']=='1*')){
						//$document1->save("../../../print/noread/".$filename."_".$_POST['machinetype']."clientlist".$listtype."_".$consecnumber.".docx");//修改列印流程
						$document1->save("../../../print/read/".$consecnumber."_clientlist".$listtype.$_POST['machinetype']."_".$filename.".docx");
						if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
							$prt=fopen("../../../print/noread/".$consecnumber."_clientlist".$listtype.$_POST['machinetype']."_".$filename.".".$_POST['machinetype'],'w');
						}
						else{
							$prt=fopen("../../../print/noread/".$consecnumber."_clientlist".$listtype.$_POST['machinetype']."_".$filename.".prt",'w');
						}
						fclose($prt);
					}
					else{
						//$document1->save("../../../print/noread/".$filename."_".$_POST['machinetype']."clientlist".$listtype."temp_".$consecnumber.".docx");
						$document1->save("../../../print/read/".$consecnumber."_clientlist".$listtype."temp".$_POST['machinetype']."_".$filename.".docx");
						if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
							$prt=fopen("../../../print/noread/".$consecnumber."_clientlist".$listtype."temp".$_POST['machinetype']."_".$filename.".".$_POST['machinetype'],'w');
						}
						else{
							$prt=fopen("../../../print/noread/".$consecnumber."_clientlist".$listtype."temp".$_POST['machinetype']."_".$filename.".prt",'w');
						}
						fclose($prt);
					}
					/*if(intval($print['item']['clientlist'])>1){
						for($i=1;$i<intval($print['item']['clientlist']);$i++){
							copy("../../../print/noread/clientlist_".$consecnumber."_".$filename.".docx","../../../print/noread/clientlist_".$consecnumber."_".$filename."(".$i.").docx");
						}
					}
					else{
					}*/
				/*}
				else{
					$document1->save("../../../print/read/clientlist_".$consecnumber."_".$filename.".docx");
					if(intval($print['item']['clientlist'])>1){
						for($i=1;$i<intval($print['item']['clientlist']);$i++){
							copy("../../../print/read/clientlist_".$consecnumber."_".$filename.".docx","../../../print/read/clientlist_".$consecnumber."_".$filename."(".$i.").docx");
						}
					}
					else{
					}
				}*/
			}
		}
		if(isset($_POST['templistitem'])){//存在已點產品，則不印單
		}
		else{
			if($print['item']['numbertag']!='0'){
				$PHPWord = new PHPWord();

				if(isset($print['item']['numbertagtype'])&&$print['item']['numbertagtype']!=''&&file_exists('../../../template/numbertag'.$print['item']['numbertagtype'].'.docx')){
					$document2 = $PHPWord->loadTemplate('../../../template/numbertag'.$print['item']['numbertagtype'].'.docx');
				}
				else{
					$document2 = $PHPWord->loadTemplate('../../../template/numbertag.docx');
				}
				
				$document2->setValue('story',$data['basic']['storyname']);
				date_default_timezone_set($content['init']['settime']);
				$document2->setValue('datetime',date('Y/m/d H:i:s'));
				if($listtype=='1'){
					$document2->setValue('type',$buttons['name']['listtype1'].' '.$saleno);
				}
				else if($listtype=='2'){
					$document2->setValue('type',$buttons['name']['listtype2'].' '.$saleno);
				}
				else if($listtype=='3'){
					$document2->setValue('type',$buttons['name']['listtype3'].' '.$saleno);
				}
				else{
					$document2->setValue('type',$buttons['name']['listtype4'].' '.$saleno);
				}

				if(isset($content['init']['qrcallnumber'])&&$content['init']['qrcallnumber']=='1'&&isset($machinedata['pigo'])){
					include_once '../../../tool/phpqrcode/qrlib.php'; 
					// outputs image directly into browser, as PNG stream 
					if(file_exists('../../../print/qrcode')){
					}
					else{
						mkdir('../../../print/qrcode',0777,true);
					}
					$filename=$saleno.'.png';

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $machinedata['pigo']['path'].$machinedata['pigo']['deviceid'].'/'.str_pad($saleno,4,'0',STR_PAD_LEFT).'/');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					//curl_setopt($ch, CURLOPT_POST, 1);
					// Edit: prior variable $postFields should be $postfields;
					//curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
					//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$Result = curl_exec($ch);
					if(curl_errno($ch) !== 0) {
						//print_r('cURL error when connecting to ' . $machinedata['pigo']['path'].$machinedata['pigo']['deviceid'].'/'.str_pad($saleno,4,'0',STR_PAD_LEFT).'/' . ': ' . curl_error($machinedata['pigo']['path'].$machinedata['pigo']['deviceid'].'/'.str_pad($saleno,4,'0',STR_PAD_LEFT).'/'));
					}
					curl_close($ch);
					$Response=json_decode($Result);
					//echo $Result;
					QRcode::png($Response->content,'../../../print/qrcode/'.$filename,'M',8);

					/*$qrcode = '<w:tbl><w:tblPr><w:tblStyle w:val="a9"/><w:tblW w:w="0" w:type="auto"/><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="3570"/></w:tblGrid>';
					$qrcode .= '<w:tr w:rsidR="001C7437" w:rsidTr="001C7437"><w:tc><w:tcPr><w:tcW w:w="3570" w:type="dxa"/><w:tcBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/><w:right w:val="nil"/></w:tcBorders></w:tcPr><w:p w:rsidR="001C7437" w:rsidRDefault="001C7437" w:rsidP="005115D6"><w:pPr><w:spacing w:line="0" w:lineRule="atLeast"/><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hint="eastAsia"/><w:b/><w:noProof/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr>';

					$qrcode .= '<w:drawing><wp:anchor distT="0" distB="0" distL="114300" distR="114300" simplePos="0" relativeHeight="251658240" behindDoc="0" locked="0" layoutInCell="1" allowOverlap="1"><wp:simplePos x="0" y="0"/><wp:positionH relativeFrom="margin"><wp:align>center</wp:align></wp:positionH><wp:positionV relativeFrom="paragraph"><wp:posOffset>-635</wp:posOffset></wp:positionV><wp:extent cx="1038225" cy="1035685"/><wp:effectExtent l="19050" t="0" r="9525" b="0"/><wp:wrapSquare wrapText="bothSides"/><wp:docPr id="1" name="圖片 0" descr="'.'../../../print/qrcode/'.$filename.'"/><wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"/></wp:cNvGraphicFramePr><a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:nvPicPr><pic:cNvPr id="0" name="'.'../../../print/qrcode/'.$filename.'"/><pic:cNvPicPr/></pic:nvPicPr><pic:blipFill><a:blip r:embed="rId6"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill><pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="1038225" cy="1035685"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic></a:graphicData></a:graphic></wp:anchor></w:drawing>';

					$qrcode .= '</w:r></w:p></w:tc></w:tr></w:tbl>';*/
					$document2->replaceStrToQrcode('qrcode','../../../print/qrcode/'.$filename);
				}
				else{
					$document2->replaceStrToQrcode('qrcode','empty');
				}

				$document2->setValue('consecnumber',$consecnumber);
				date_default_timezone_set($content['init']['settime']);
				$filename=date("YmdHis");
				//$document2->save("../../../print/noread/number_".$filename.".docx");

				$document2->save("../../../print/read/".$filename."_number".$listtype.".docx");
				
				if(isset($print['item']['printbymachine'])&&$print['item']['printbymachine']=='2'){
					$prt=fopen("../../../print/noread/".$filename."_number".$listtype.".".$_POST['machinetype'],'w');
				}
				else{
					$prt=fopen("../../../print/noread/".$filename."_number".$listtype.".prt",'w');
				}
				fclose($prt);
			}
			else{
			}
		}
	}
	if(isset($_POST['notconsecnumber'])){//用於暫出明細
		if(file_exists('../../../print/clientintellaqrcode/'.$_POST['machinetype'].'.png')){
			unlink('../../../print/clientintellaqrcode/'.$_POST['machinetype'].'.png');
		}
		else{
			//$document1->replaceStrToQrcode('qrcode','empty');
		}
	}
	else{
		//$document1->replaceStrToQrcode('qrcode','empty');
	}
}

echo str_pad($consecnumber,6,'0',STR_PAD_LEFT);
?>