<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$content=parse_ini_file('../../../database/setup.ini',true);

$inv=$_POST['invno'];
$Y=date('Y');
$month=date('m');
if(intval($month)%2==0){
	$m=$month;
}
else{
	$m=intval($month)+1;
}
if(strlen($m)<2){
	$m='0'.$m;
}
$invdate=$Y.$m;
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
	$invmachine='m1';
}
if(file_exists("../../../database/sale/".$invdate."/invdata_".$invdate."_".$invmachine.".db")){
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
		$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
	}
	else{
		$timeini=parse_ini_file('../../../database/timem1.ini',true);
	}
	$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
	$sql='SELECT sellerid,createdate,createtime FROM invlist WHERE invnumber="'.$inv.'" AND state=1';
	$item=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$result=array();
	$result[0]=$timeini['inv']['ip'];

	if(!isset($content['basic']['sendinvlocation'])||$content['basic']['sendinvlocation']=='1'){//亮點
		if(isset($item[0]['createtime'])){
			$time=preg_split('/:/',$item[0]['createtime']);
			$result[1]='C0401_'.strtoupper($inv).'_'.$item[0]['createdate'].$time[0].$time[1].$time[2].'_'.$invmachine.'.xml';
		}
		else{
			$tempres=glob('../../../trnx/log/C0401_'.$inv.'*.xml');
			$res=preg_split('/\//',$tempres[0]);
			$result[1]=$res[sizeof($res)-1];
			$tempxml=file_get_contents($tempres[0]);
			$xml=json_decode(json_encode(simplexml_load_string($tempxml, "SimpleXMLElement", LIBXML_NOCDATA)),1);
			//print_r($xml);
			$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
			$sql='INSERT INTO invlist (invnumber,createdate,createtime,buyerid,buyname,sellerid,sellername,relatenumber,invtype,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount,canceldate,canceltime,cancelreason,replaceinv,state) VALUES ("'.$xml['Main']['InvoiceNumber'].'","'.$xml['Main']['InvoiceDate'].'","'.$xml['Main']['InvoiceTime'].'","'.$xml['Main']['Buyer']['Identifier'].'","';
			if($xml['Main']['Buyer']['Name']=='0000'){
				$sql.='0000000000';
			}
			else{
				$sql.=$xml['Main']['Buyer']['Name'];
			}
			$sql.='","'.$xml['Main']['Seller']['Identifier'].'","'.$xml['Main']['Seller']['Name'].'","'.$xml['Main']['RelateNumber'].'","'.$xml['Main']['InvoiceType'].'","'.$xml['Main']['DonateMark'].'",';
			if(isset($xml['Main']['CarrierType'])){
				$sql.='"'.$xml['Main']['CarrierType'].'"';
			}
			else{
				$sql.='NULL';
			}
			$sql.=',';
			if(isset($xml['Main']['CarrierId1'])){
				$sql.='"'.$xml['Main']['CarrierId1'].'"';
			}
			else{
				$sql.='NULL';
			}
			$sql.=',';
			if(isset($xml['Main']['CarrierId2'])){
				$sql.='"'.$xml['Main']['CarrierId2'].'"';
			}
			else{
				$sql.='NULL';
			}
			$sql.=',"'.$xml['Main']['PrintMark'].'",';
			if($xml['Main']['DonateMark']=='1'){
				$sql.='"'.$xml['Main']['NPOBAN'].'"';
			}
			else{
				$sql.='NULL';
			}
			$sql.=',"'.$xml['Main']['RandomNumber'].'",'.$xml['Amount']['TotalAmount'].',NULL,NULL,NULL,NULL,1)';
			//echo $sql;
			sqlnoresponse($conn,$sql,'sqlite');
			sqlclose($conn,'sqlite');
		}
	}
	else if($content['basic']['sendinvlocation']=='2'){//神通
	}
	else if($content['basic']['sendinvlocation']=='3'){//中鼎
		if(!isset($content['zdninv']['useEIP'])||$content['zdninv']['useEIP']=='1'){
			if(isset($item[0]['createtime'])){
				$time=preg_split('/:/',$item[0]['createtime']);
				$result[1]='C0401_'.$item[0]['sellerid'].'_'.strtoupper($inv).'_'.substr($item[0]['createdate'],4,4).$time[0].$time[1].$time[2].'.xml;'.$item[0]['createdate'];
			}
			else{
				$tempres=glob('../../../print/read/C0401_'.$inv.'*.xml');
				$res=preg_split('/\//',$tempres[0]);
				$tempdata=preg_split('/_/',$res[sizeof($res)-1]);

				$result[1]='C0401_'.$content['basic']['Identifier'].'_'.strtoupper($inv).'_'.substr($tempdata[2],4).'.xml;'.substr($tempdata[2],0,8);

				//$tempxml=file_get_contents($tempres[0]);
				//$xml=json_decode(json_encode(simplexml_load_string($tempxml, "SimpleXMLElement", LIBXML_NOCDATA)),1);
				//print_r($xml);
				$xml=file($tempres[0]);

				$conn=sqlconnect("../../../database/sale/".$invdate,"invdata_".$invdate."_".$invmachine.".db","","","","sqlite");
				$sql='INSERT INTO invlist (invnumber,createdate,createtime,buyerid,buyname,sellerid,sellername,relatenumber,invtype,donatemark,carriertype,carrierid1,carrierid2,printmark,npoban,randomnumber,totalamount,canceldate,canceltime,cancelreason,replaceinv,state) VALUES ("'.$xml[8].'","'.substr($xml[10],0,8).'","'.(substr($xml[10],9,2).':'.substr($xml[10],11,2).':'.substr($xml[10],13,2)).'","'.$xml[16].'","';
				//if($xml['Main']['Buyer']['Name']=='0000'){
					$sql.='0000000000';
				/*}
				else{
					$sql.=$xml['Main']['Buyer']['Name'];
				}*/
				$sql.='","'.$xml[7].'","'.$xml[1].'","'.$xml[17].'","07","';
				if($xml[11]!=''||$xml[12]!=''){
					$sql.='1';
				}
				else{
					$sql.='0';
				}
				$sql.='",';
				/*if(isset($xml['Main']['CarrierType'])){//無法判斷
					$sql.='"'.$xml['Main']['CarrierType'].'"';
				}
				else{*/
					$sql.='NULL';
				//}
				$sql.=',';
				if($xml[11]!=''){
					$sql.='"'.$xml[11].'"';
				}
				else{
					$sql.='NULL';
				}
				$sql.=',';
				if($xml[11]!=''){
					$sql.='"'.$xml[11].'"';
				}
				else{
					$sql.='NULL';
				}
				if($xml[11]!=''||$xml[12]!=''){
					$sql.=',"N",';
				}
				else{
					$sql.=',"Y",';
				}
				if($xml[12]!=''){
					$sql.='"'.$xml[12].'"';
				}
				else{
					$sql.='NULL';
				}
				$sql.=',"'.$xml[9].'",'.$xml[6].',NULL,NULL,NULL,NULL,1)';
				//echo $sql;
				sqlnoresponse($conn,$sql,'sqlite');
				sqlclose($conn,'sqlite');
			}
		}
		else{
			if(isset($item[0]['createtime'])){
				$time=preg_split('/:/',$item[0]['createtime']);
				$result[1]='C0401_'.$item[0]['sellerid'].'_'.strtoupper($inv).'_'.substr($item[0]['createdate'],4,4).$time[0].$time[1].$time[2].'.json;'.$item[0]['createdate'];
			}
			else{
				$tempres=glob('../../../print/invuploadlog/uploaded/C0401_'.$inv.'*.json');
				$tempresw=glob('../../../print/invuploadlog/waitupload/C0401_'.$inv.'*.json');
				$tempresxml=glob('../../../print/read/C0401_'.$inv.'*.xml');
				if(isset($tempresxml[0])){
					$res=preg_split('/\//',$tempresxml[0]);
					$tempdata=preg_split('/_/',$res[sizeof($res)-1]);
					$date=$tempdata[2];
				}
				else{
					if(isset($temppres[0])){
						$res=preg_split('/\//',$tempres[0]);
					}
					else{
						$res=preg_split('/\//',$tempresw[0]);
					}
					$tempdata=preg_split('/_/',$res[sizeof($res)-1]);
					$date=$tempdata[3];
				}

				$result[1]='C0401_'.$content['basic']['Identifier'].'_'.strtoupper($inv).'_'.$date.'.json;'.substr($date,0,8);
			}
		}
	}

	echo json_encode($result);
}
else{
	echo 'dbempty';
}
?>