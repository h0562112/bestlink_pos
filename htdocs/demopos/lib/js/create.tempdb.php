<?php
include_once '../../../tool/myerrorlog.php';
//print_r($_POST);
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
//include_once '../../../tool/json2array.php';
//$tempPOST=j2a($_POST['str']);
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
$content=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($content['init']['settime']);

if(isset($content['init']['accounting'])&&$content['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
if(file_exists('../../../database/otherpay.ini')){
	$temp=parse_ini_file('../../../database/otherpay.ini',true);
	$otherpaydata=array();
	foreach($temp as $i=>$v){
		if($i=='pay'||(!isset($v['location'])||$v['location']=='CST011')||(isset($v['fromdb'])&&$v['fromdb']=='member')){
		}
		else{
			array_push($otherpaydata,$v['location']);
		}
	}
}
else{
}

if(!isset($_POST['consecnumber'])||$_POST['consecnumber']==''){//如不具有帳單編號，則使用ini中的bizdate
	date_default_timezone_set($content['init']['settime']);
	srand(date('YmdHis'));
	while(file_exists('../../../print/stop.ini')){
		usleep(100000*rand(0,5));//1 seconds = 1000000
	}
	$f=fopen('../../../print/stop.ini','w');
	fclose($f);
	$filename='SALES_'.substr($timeini['time']['bizdate'],0,6);
	$bizdate=$timeini['time']['bizdate'];
	//fwrite($file,'exist'.PHP_EOL);
	if(file_exists("../../../database/sale/".$filename.".DB")){
	}
	else{
		if(file_exists("../../../database/sale/empty.DB")){
		}
		else{
			include_once 'create.emptyDB.php';
			create('empty');
		}
		copy("../../../database/sale/empty.DB","../../../database/sale/".$filename.".DB");
	}
	$conn=sqlconnect('../../../database/sale',$filename.'.DB','','','','sqlite');
	$sql='SELECT (SELECT CONSECNUMBER FROM CST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) AS one,(SELECT CONSECNUMBER FROM tempCST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) AS two';
	$s=sqlquery($conn,$sql,'sqlite');

	//2021/10/18 查詢網路訂單的編號
	$sql='SELECT (SELECT SUBSTR(CONSECNUMBER,2) FROM CST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS one,(SELECT SUBSTR(CONSECNUMBER,2) FROM tempCST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS two';
	$w=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	//echo $s[0]['two'];
	
	//2021/10/18
	if($s[0]['one']==null){
		$s[0]['one']=$w[0]['one'];
	}
	else{
		if(floatval($s[0]['one'])<floatval($w[0]['one'])){
			$s[0]['one']=$w[0]['one'];
		}
		else{
		}
	}
	if($s[0]['two']==null){
		$s[0]['two']=$w[0]['two'];
	}
	else{
		if(floatval($s[0]['two'])<floatval($w[0]['two'])){
			$s[0]['two']=$w[0]['two'];
		}
		else{
		}
	}

	if($s[0]['one']==null&&$s[0]['two']==null){
		$machinedata['basic']['consecnumber']='1';
	}
	else if($s[0]['one']==null){
		if(floatval($machinedata['basic']['consecnumber'])>floatval($s[0]['two'])){
			$machinedata['basic']['consecnumber']=floatval($machinedata['basic']['consecnumber'])+1;
		}
		else{
			$machinedata['basic']['consecnumber']=floatval($s[0]['two'])+1;
		}
	}
	else if($s[0]['two']==null){
		if(floatval($machinedata['basic']['consecnumber'])>floatval($s[0]['one'])){
			$machinedata['basic']['consecnumber']=floatval($machinedata['basic']['consecnumber'])+1;
		}
		else{
			$machinedata['basic']['consecnumber']=floatval($s[0]['one'])+1;
		}
	}
	else{
		if(floatval($s[0]['one'])>floatval($s[0]['two'])){
			if(floatval($machinedata['basic']['consecnumber'])>floatval($s[0]['one'])){
				$machinedata['basic']['consecnumber']=floatval($machinedata['basic']['consecnumber'])+1;
			}
			else{
				$machinedata['basic']['consecnumber']=floatval($s[0]['one'])+1;
			}
		}
		else{
			if(floatval($machinedata['basic']['consecnumber'])>floatval($s[0]['two'])){
				$machinedata['basic']['consecnumber']=floatval($machinedata['basic']['consecnumber'])+1;
			}
			else{
				$machinedata['basic']['consecnumber']=floatval($s[0]['two'])+1;
			}
		}
	}
	//$machinedata['basic']['consecnumber']=intval($machinedata['basic']['consecnumber'])+1;
	$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
	$consecnumber=str_pad($machinedata['basic']['consecnumber'],6,"0",STR_PAD_LEFT);
	$saleno=$machinedata['basic']['saleno'];
	if(intval($machinedata['basic']['saleno'])>=intval($machinedata['basic']['maxsaleno'])){
		if(isset($machinedata['basic']['strsaleno'])){
			$machinedata['basic']['saleno']=$machinedata['basic']['strsaleno'];
		}
		else{
			$machinedata['basic']['saleno']=0;
		}
	}
	else{
	}
	if(sizeof($machinedata['basic'])>3){
		write_ini_file($machinedata,'../../../database/machinedata.ini');
	}
	else{
		date_default_timezone_set($content['init']['settime']);
		$f=fopen('../../../database/'.date('YmdHis').'machinedata.ini','w');
		//fwrite($f,print_r($machinedata,true).PHP_EOL);
		fclose($f);
	}
}
else{//如原本有帳單編號，則使用它本身的bizdate
	$filename='SALES_'.substr($_POST['bizdate'],0,6);
	$bizdate=$_POST['bizdate'];
	//fwrite($file,'not exist'.PHP_EOL);
	$consecnumber=$_POST['consecnumber'];
	//$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
	$conn=sqlconnect('../../../database/sale',$filename.'.DB','','','','sqlite');
	$sql='SELECT saleno FROM salemap WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'"';
	$s=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($s[0]['saleno'])){
		$saleno=$s[0]['saleno'];
	}
	else{
		$saleno='';
	}
	//write_ini_file($machinedata,'../../../database/machinedata.ini');
}
echo $saleno.'-';
$consecnumber=str_pad($consecnumber,6,'0',STR_PAD_LEFT);
echo $consecnumber.'-';
$data=parse_ini_file('../../../database/setup.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
//$content=parse_ini_file('../../../database/initsetting.ini',true);
$buttons=parse_ini_file('../../syspram/buttons-'.$content['init']['firlan'].'.ini',true);
$menu=parse_ini_file('../../../database/'.$data['basic']['company'].'-menu.ini',true);
if(file_exists('../../../database/'.$data['basic']['company'].'-kds.ini')){
	$kdstype=parse_ini_file('../../../database/'.$data['basic']['company'].'-kds.ini',true);
}
else{
}
$pti=parse_ini_file('../../../database/itemprinttype.ini',true);
if($_POST['listtype']=='1'){
	if(file_exists('../../../database/discount1.ini')){
		$discount=parse_ini_file('../../../database/discount1.ini',true);
	}
	else{
		//$discount=parse_ini_file('../../../database/discount1.ini',true);
	}
}
else if($_POST['listtype']=='2'){
	if(file_exists('../../../database/discount2.ini')){
		$discount=parse_ini_file('../../../database/discount2.ini',true);
	}
	else{
		//$discount=parse_ini_file('../../../database/discount2.ini',true);
	}
}
else if($_POST['listtype']=='3'){
	if(file_exists('../../../database/discount3.ini')){
		$discount=parse_ini_file('../../../database/discount3.ini',true);
	}
	else{
		//$discount=parse_ini_file('../../../database/discount3.ini',true);
	}
}
else{//$_POST['listtype']=='4'
	if(file_exists('../../../database/discount4.ini')){
		$discount=parse_ini_file('../../../database/discount4.ini',true);
	}
	else{
		//$discount=parse_ini_file('../../../database/discount4.ini',true);
	}
}
if(!isset($_POST['otherstring'])||strlen($_POST['otherstring'])==0){
}
else{
	$otherpay1=preg_split('/,/',$_POST['otherstring']);
	$otherpay=array();
	//2022/5/26 初始化TA1-10，以便處理quickclick訂單，付款方式中quickclick付款無法移除
	$otherpay['TA1']=0;
	$otherpay['TA2']=0;
	$otherpay['TA3']=0;
	$otherpay['TA4']=0;
	$otherpay['TA5']=0;
	$otherpay['TA6']=0;
	$otherpay['TA7']=0;
	$otherpay['TA8']=0;
	$otherpay['TA9']=0;
	$otherpay['TA10']=0;
	foreach($otherpay1 as $ot1){
		$tot1=preg_split('/:/',$ot1);
		if(preg_match('/CST011-TA/',$tot1[0])){
			$otherpay[substr($tot1[0],7)]=$tot1[1];
		}
		else if(preg_match('/memberpoint/',$tot1[0])){
			$otherpay[substr($tot1[0],12)]=$tot1[1];
		}
		else if(preg_match('/membermoney/',$tot1[0])){
			$otherpay[substr($tot1[0],12)]=$tot1[1];
		}
		else if(preg_match('/intella/',$tot1[0])){
			$tempother=preg_split('/-/',$tot1[0]);
			$otherpay[$tempother[0]]=$tempother[1].':'.$tot1[1];
		}
		else if(preg_match('/nidin/',$tot1[0])){
			$tempother=preg_split('/-/',$tot1[0]);
			$otherpay[$tempother[0]]=$tempother[1].':'.$tot1[1];
		}
		else{
			$tempother=preg_split('/-/',$tot1[0]);
			$otherpay[$tempother[0]][$tempother[1]]=$tot1[1];
		}
	}
}
if(isset($_POST['memno'])&&strlen($_POST['memno'])!=0){
	if(isset($content['init']['onlinemember'])&&$content['init']['onlinemember']=='1'){
		$PostData = array(
			"type"=> "online",
			"memno" => $_POST['memno'],
			//"CouponApiKey" => $itrisetup['itri']['couponapikey'],
			"company" => $data['basic']['company'],
			"ajax" => ""
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
		$memdata=json_decode($memdata,1);
		if(curl_errno($ch) !== 0) {
			//print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
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

date_default_timezone_set($content['init']['settime']);
$datetime=date('YmdHis');

if(isset($_POST['no'])){
	$totalqty=0;
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$totalqty=intval($totalqty)+intval($_POST['number'][$i]);
	}
}
else{
}

$insertdate=$datetime;

$conn=sqlconnect('../../../database/sale',$filename.'.DB','','','','sqlite');

$saleinvdata='';

$taste=parse_ini_file('../../../database/'.$data['basic']['company'].'-taste.ini',true);
if(isset($content['init']['posdvr'])&&$content['init']['posdvr']=='1'){
	date_default_timezone_set($content['init']['settime']);
	$tempposdvr=date('YmdHis');
	$posdvr=fopen('../../../print/posdvr/'.$tempposdvr.';'.$_POST['machinetype'].'.txt','w');
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
	echo $tempposdvr.';'.$_POST['machinetype'];
}
else{
}
if(isset($content['init']['kvm'])&&$content['init']['kvm']=='1'){
	date_default_timezone_set($content['init']['settime']);
	$tempkvm=date('YmdHis');

	$selectValue = isset($_POST['selectValue']) ? $_POST['selectValue'] : '';
	if($selectValue !=''){
		$kvm=fopen('../../../print/kvm/'.$selectValue.';'.$consecnumber.'.ini','w');	
	}else{
		$kvm=fopen('../../../print/kvm/'.$bizdate.';'.$consecnumber.'.ini','w');	
	}
	

	$tempkvmcontent=PHP_EOL.'[item]'.PHP_EOL;
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
$handle=fopen('../../'.$_POST['machinetype'].'tempdb.log.txt','a');
date_default_timezone_set($content['init']['settime']);
fwrite($handle,date('Y/m/d H:i:s').' -- CST012 - '.$consecnumber.PHP_EOL);
$sql='SELECT LINENUMBER FROM tempCST012 WHERE CONSECNUMBER="'.$consecnumber.'" ORDER BY LINENUMBER DESC LIMIT 1';
$indarray=sqlquery($conn,$sql,'sqlite');
if(sizeof($indarray)==0){
	$index=1;
}
else{
	$index=intval($indarray[0]['LINENUMBER'])+1;
}

$sql='';
$kds=array();
if(isset($_POST['no'])){
	for($i=0;$i<sizeof($_POST['no']);$i++){
		if(isset($_POST['templistitem'][$i])){//判斷是否為"加點"項目，若是則不印單與不新增至DB
			$sqli='SELECT AMT FROM tempCST012 WHERE CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER="'.str_pad(intval($_POST['linenumber'][$i])+1,3,'0',STR_PAD_LEFT).'"';
			$amt=sqlquery($conn,$sqli,'sqlite');
			//echo $sql;
			if($_POST['discount'][$i]>0){
				if($content['init']['accuracytype']==1){//四捨五入
					$_POST['discount'][$i]=(floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-round(((floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-$_POST['discount'][$i]),$content['init']['accuracy']);
				}
				else if($content['init']['accuracytype']==2){//無條件進位
					$_POST['discount'][$i]=(floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-ceil(((floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-$_POST['discount'][$i]),$content['init']['accuracy']);
				}
				else{//無條件捨去
					$_POST['discount'][$i]=(floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-floor(((floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-$_POST['discount'][$i]),$content['init']['accuracy']);
				}
			}
			else{
				$_POST['discount'][$i]=0;
			}
			if(isset($amt[0]['AMT'])&&$_POST['discount'][$i]==$amt[0]['AMT']){
			}
			else{
				$sqli='UPDATE tempCST012 SET ITEMGRPCODE="'.$_POST['discontent'][$i].'",AMT="-'.$_POST['discount'][$i].'" WHERE CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER="'.str_pad(intval($_POST['linenumber'][$i])+1,3,'0',STR_PAD_LEFT).'"';
				sqlnoresponse($conn,$sqli,'sqlite');
			}
			//$index++;
			if(isset($kvm)){
				if($_POST['mname1'][$i]!=''){
					$tempkvmcontent .= 'label[]="'.$_POST['name'][$i]."(".$_POST['mname1'][$i].")X".$_POST['number'][$i];
				}
				else{
					$tempkvmcontent .= 'label[]="'.$_POST['name'][$i]."X".$_POST['number'][$i];
				}
				$temptasteno=preg_split('/,/',$_POST['taste1'][$i]);
				$temptastenumber=preg_split('/,/',$_POST['taste1number'][$i]);
				$temptastename=preg_split('/,/',$_POST['taste1name'][$i]);
				for($j=0;$j<10;$j++){
					if(isset($temptasteno[$j])&&strlen($temptasteno[$j])>0&&$temptasteno[$j]=='99999'){//手打備註
						$tempkvmcontent .= "+".$temptastename[$j];
					}
					else if(isset($temptasteno[$j])&&strlen($temptasteno[$j])>0){
						if((intval($temptastenumber[$j])%10)==1){
							$tempkvmcontent .= "+".$taste[$temptasteno[$j]]['name1'];
						}
						else{
							$tempkvmcontent .= "+".$taste[$temptasteno[$j]]['name1']."X".(intval($temptastenumber[$j])%10);
						}
					}
					else{
					}
				}
				$tempkvmcontent .= '"'.PHP_EOL;
			}
			else{
			}
		}
		else{
			if(isset($content['init']['kds'])&&$content['init']['kds']=='1'&&isset($kdstype)){//是否開啟kds
				if(isset($menu[$_POST['no'][$i]]['kdslabel'])&&$menu[$_POST['no'][$i]]['kdslabel']!=''){//2021/9/29 KDS LABEL 設定
					for($kdsindex=0;$kdsindex<sizeof($kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type']);$kdsindex++){
						if(isset($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]])){
						}
						else{
							$kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['dir']='../../../kds/items/noread/'.$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex];
							date_default_timezone_set($content['init']['settime']);
							$kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['filename']='temp'.date('YmdHis').'.ini';
							if(file_exists($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['dir'])){
							}
							else{
								mkdir($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['dir']);
							}
							$kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file']=fopen($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['dir'].'/'.$kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['filename'],'w');
							date_default_timezone_set($content['init']['settime']);
							fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'['.$consecnumber.'consecnumber'.date('YmdHis').']'.PHP_EOL);						
						}
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'terminalnumber[]='.$_POST['machinetype'].PHP_EOL);
						if(isset($_POST['memno'])&&$_POST['memno']!=''){
							fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'customname[]='.$memdata[0]['name'].PHP_EOL);
						}
						else{
							fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'customname[]='.PHP_EOL);
						}
						if(isset($_POST['tablenumber'])){
							fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'tablenumber[]='.$_POST['tablenumber'].PHP_EOL);
						}
						else{
						}
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'remarks[]='.$_POST['listtype'].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'saleno[]='.$saleno.PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'itemtype[]='.$_POST['typeno'][$i].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'itemno[]='.$_POST['no'][$i].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'itemname[]='.$_POST['name'][$i].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'taste1[]='.$_POST['taste1'][$i].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'taste1name[]='.$_POST['taste1name'][$i].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'moneyname[]='.$_POST['mname1'][$i].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'number[]='.$_POST['number'][$i].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'kdsno[]='.$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex].PHP_EOL);
						fwrite($kds[$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['type'][$kdsindex]]['file'],'kdsgroup[]='.$kdstype['label'.$menu[$_POST['no'][$i]]['kdslabel']]['group'][$kdsindex].PHP_EOL);
					}
				}
				else if(isset($menu[$_POST['no'][$i]]['kds'])&&$menu[$_POST['no'][$i]]['kds']!=''){//產品是否設定完成kds群組
					if(isset($kds[$menu[$_POST['no'][$i]]['kds']])){
					}
					else{
						$kds[$menu[$_POST['no'][$i]]['kds']]['dir']='../../../kds/items/noread/'.$menu[$_POST['no'][$i]]['kds'];
						date_default_timezone_set($content['init']['settime']);
						$kds[$menu[$_POST['no'][$i]]['kds']]['filename']='temp'.date('YmdHis').'.ini';
						if(file_exists($kds[$menu[$_POST['no'][$i]]['kds']]['dir'])){
						}
						else{
							mkdir($kds[$menu[$_POST['no'][$i]]['kds']]['dir']);
						}
						$kds[$menu[$_POST['no'][$i]]['kds']]['file']=fopen($kds[$menu[$_POST['no'][$i]]['kds']]['dir'].'/'.$kds[$menu[$_POST['no'][$i]]['kds']]['filename'],'w');
						date_default_timezone_set($content['init']['settime']);
						fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'['.$consecnumber.'consecnumber'.date('YmdHis').']'.PHP_EOL);						
					}
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'terminalnumber[]='.$_POST['machinetype'].PHP_EOL);
					if(isset($_POST['memno'])&&$_POST['memno']!=''){
						fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'customname[]='.$memdata[0]['name'].PHP_EOL);
					}
					else{
						fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'customname[]='.PHP_EOL);
					}
					if(isset($_POST['tablenumber'])){
						fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'tablenumber[]='.$_POST['tablenumber'].PHP_EOL);
					}
					else{
					}
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'remarks[]='.$_POST['listtype'].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'saleno[]='.$saleno.PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'itemtype[]='.$_POST['typeno'][$i].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'itemno[]='.$_POST['no'][$i].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'itemname[]='.$_POST['name'][$i].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'taste1[]='.$_POST['taste1'][$i].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'taste1name[]='.$_POST['taste1name'][$i].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'moneyname[]='.$_POST['mname1'][$i].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'number[]='.$_POST['number'][$i].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'kdsno[]='.$menu[$_POST['no'][$i]]['kds'].PHP_EOL);
					fwrite($kds[$menu[$_POST['no'][$i]]['kds']]['file'],'kdsgroup[]='.$menu[$_POST['no'][$i]]['kdsgroup'].PHP_EOL);
				}
				else{
				}
			}
			else{
			}
			if(isset($posdvr)){
				$tempdvrcontent .= $_POST['name'][$i]."X".$_POST['number'][$i]."  ";
				if($_POST['unitprice'][$i]==''||strlen(trim($_POST['unitprice'][$i]))==0){
					$tempdvrcontent .= "0  0".PHP_EOL;;
				}
				else{
					$tempdvrcontent .= preg_replace('/[.]/','!46',$_POST['unitprice'][$i])."  ".preg_replace('/[.]/','!46',(floatval($_POST['number'][$i])*floatval($_POST['unitprice'][$i]))).PHP_EOL;
				}
			}
			else{
			}
			if(isset($kvm)){
				if($_POST['mname1'][$i]!=''){
					$tempkvmcontent .= 'label[]="'.$_POST['name'][$i]."(".$_POST['mname1'][$i].")X".$_POST['number'][$i];
				}
				else{
					$tempkvmcontent .= 'label[]="'.$_POST['name'][$i]."X".$_POST['number'][$i];
				}
			}
			else{
			}

			

			$selectValue = $_POST['selectValue'];
		$bizdate = $_POST['bizdate']; // 這個是代表當 $selectValue 為空時要使用的值

		$sql = $sql . "INSERT INTO tempCST012 (TERMINALNUMBER, BIZDATE, CONSECNUMBER, LINENUMBER, CLKCODE, CLKNAME, DTLMODE, DTLTYPE, DTLFUNC, ITEMCODE, ITEMNAME, ITEMGRPCODE, ITEMGRPNAME, ITEMDEPTCODE, ITEMDEPTNAME, SELECTIVEITEM1, SELECTIVEITEM2, SELECTIVEITEM3, SELECTIVEITEM4, SELECTIVEITEM5, SELECTIVEITEM6, SELECTIVEITEM7, SELECTIVEITEM8, SELECTIVEITEM9, SELECTIVEITEM10, UNITPRICELINK, WEIGHT, QTY, UNITPRICE, AMT, TAXCODE2, TAXCODE3, TAXCODE4, TAXCODE5, ZCOUNTER, REMARKS, CREATEDATETIME) VALUES ";

		$values = '("'.$_POST['machinetype'].'","'. ($selectValue ? $selectValue : $bizdate) .'","'.$consecnumber.'","'.str_pad($index, 3, '0', STR_PAD_LEFT).'",';

		// 接下來的程式碼檢查是否有 $_POST['usercode']，如果有則加入對應的值到 $values 中
		if (isset($_POST['usercode']) && strlen($_POST['usercode'])) {
			$values = $values . '"' . $_POST['usercode'] . '","' . $_POST['username'] . '",';
		} else {
			$values = $values . '"","",';
		}


			$values=$values.'"1","1","01","'.str_pad($_POST['no'][$i],16,'0',STR_PAD_LEFT).'","'.$_POST['name'][$i].'","'.str_pad($_POST['typeno'][$i],6,'0',STR_PAD_LEFT).'","'.$_POST['type'][$i].'","'.str_pad($_POST['typeno'][$i],6,'0',STR_PAD_LEFT).'","'.$_POST['type'][$i].'",';
			$temptasteno=preg_split('/,/',$_POST['taste1'][$i]);
			$temptastenumber=preg_split('/,/',$_POST['taste1number'][$i]);
			$temptastename=preg_split('/,/',$_POST['taste1name'][$i]);
			if(sizeof($temptasteno)>0){
				for($j=0;$j<sizeof($temptasteno);$j++){
				//for($j=0;$j<10;$j++){//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式
					if(isset($temptasteno[$j])&&strlen($temptasteno[$j])>0&&$temptasteno[$j]=='99999'){//手打備註
						if($j>0){//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式，判斷串接符號
							$values=$values.',';
						}
						else{
							$values=$values.'"';
						}
						//$values=$values.'"999991:'.preg_replace('/[,\'"$]/', '', $temptastename[$j]).'",';
						$values=$values.'999991:'.preg_replace('/[,\'"$]/', '', $temptastename[$j]);//2021/7/9 過濾一些特定符號
						if(isset($posdvr)){
							$tempdvrcontent .= " !43".$temptastename[$j].PHP_EOL;
						}
						else{
						}
						if(isset($kvm)){
							$tempkvmcontent .= "+".$temptastename[$j];
						}
						else{
						}
					}
					else if(isset($temptasteno[$j])&&strlen($temptasteno[$j])>0){
						if($j>0){//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式，判斷串接符號
							$values=$values.',';
						}
						else{
							$values=$values.'"';
						}
						$values=$values.str_pad($temptasteno[$j].(intval($temptastenumber[$j])%10),6,'0',STR_PAD_LEFT);
						if(isset($posdvr)){
							if((intval($temptastenumber[$j])%10)==1){
								$tempdvrcontent .= " !43".$taste[$temptasteno[$j]]['name1']."  ".preg_replace('/[.]/','!46',$taste[$temptasteno[$j]]['money'])."  ".preg_replace('/[.]/','!46',floatval($taste[$temptasteno[$j]]['money'])).PHP_EOL;
							}
							else{
								$tempdvrcontent .= " !43".$taste[$temptasteno[$j]]['name1']."X".(intval($temptastenumber[$j])%10)."  ".preg_replace('/[.]/','!46',$taste[$temptasteno[$j]]['money'])."  ".preg_replace('/[.]/','!46',((intval($temptastenumber[$j])%10)*floatval($taste[$temptasteno[$j]]['money']))).PHP_EOL;
							}
						}
						else{
						}
						if(isset($kvm)){
							if((intval($temptastenumber[$j])%10)==1){
								$tempkvmcontent .= "+".$taste[$temptasteno[$j]]['name1'];
							}
							else{
								$tempkvmcontent .= "+".$taste[$temptasteno[$j]]['name1']."X".(intval($temptastenumber[$j])%10);
							}
						}
						else{
						}
					}
					else{
						$values=$values.'NULL,';
					}
				}
				if(isset($temptasteno[$j-1])&&strlen($temptasteno[$j-1])>0){
					$values=$values.'",';//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式，因此需要補上欄位結尾
				}
				else{
				}
			}
			else{
				$values=$values.'NULL,';//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式，若為空則補上NULL
			}
			for($j=0;$j<9;$j++){//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式，因此需要補上後面9個欄位的NULL
				$values=$values.'NULL,';
			}
			if(isset($kvm)){
				$tempkvmcontent .= '"'.PHP_EOL;
			}
			else{
			}
			$values=$values.'"'.$_POST['mname1'][$i].'",0,'.$_POST['number'][$i].',';
			if($_POST['unitprice'][$i]==''||strlen(trim($_POST['unitprice'][$i]))==0){
				$values=$values.'0';
			}
			else{
				$values=$values.$_POST['unitprice'][$i];
			}
			$values=$values.','.(floatval($_POST['number'][$i])*floatval($_POST['money'][$i])).',';

			//2020/4/14 TAXCODE2=>優惠在幾項產品上
			$values=$values.'0,';
			//2020/4/14 TAXCODE3=>單品象給予點數
			$values=$values.'0,';
			//2020/4/14 TAXCODE4=>品項贈點類別(getpointtype);1>>固定點數(預設) 2>>金額點數
			if(isset($_POST['getpointtype'][$i])){
				$values=$values.$_POST['getpointtype'][$i].',';
			}
			else{
				$values=$values.'1,';
			}
			//2020/4/14 TAXCODE5=>固定點數(getpoint):預設0點
			if(isset($_POST['getpoint'][$i])){
				$values=$values.$_POST['getpoint'][$i].',';
			}
			else{
				$values=$values.'0,';
			}

			$values=$values.'"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
			$index++;



			if($selectValue == ""){
				$values=$values.',("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
			}else{
				$values=$values.',("'.$_POST['machinetype'].'","'.$selectValue.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
			}



			if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
				$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
			}
			else{
				$values=$values.'"","",';
			}
			$values=$values.'"1","3","02","item","單品優惠","'.$_POST['discontent'][$i].'","","","",';
			for($j=0;$j<10;$j++){
				$values=$values.'NULL,';
			}
			if($_POST['discount'][$i]>0){
				if($content['init']['accuracytype']==1){//四捨五入
					$_POST['discount'][$i]=(floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-round(((floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-$_POST['discount'][$i]),$content['init']['accuracy']);
				}
				else if($content['init']['accuracytype']==2){//無條件進位
					$_POST['discount'][$i]=(floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-ceil(((floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-$_POST['discount'][$i]),$content['init']['accuracy']);
				}
				else{//無條件捨去
					$_POST['discount'][$i]=(floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-floor(((floatval($_POST['number'][$i])*floatval($_POST['money'][$i]))-$_POST['discount'][$i]),$content['init']['accuracy']);
				}
			}
			else{
				$_POST['discount'][$i]=0;
			}
			if(floatval($_POST['discount'][$i])!=0){
				if(isset($posdvr)){
					if(isset($list['name']['itemdis'])){
						$tempdvrcontent .= " !43".$list['name']['itemdis']."    ".preg_replace('/[.]/','!46',$_POST['discount'][$i]).PHP_EOL;
					}
					else{
						$tempdvrcontent .= " !43優惠折扣    ".preg_replace('/[.]/','!46',$_POST['discount'][$i]).PHP_EOL;
					}
				}
				else{
				}
			}
			else{
			}
			$values=$values.'NULL,0,0,0,-'.$_POST['discount'][$i].',';
			//2020/4/14 TAXCODE2=>優惠在幾項產品上
			if(isset($_POST['dispointtime'][$i])){
				$values=$values.$_POST['dispointtime'][$i].',';
			}
			else{
				$values=$values.'0,';
			}
			//2020/4/14 TAXCODE3=>單品象給予點數
			if(isset($_POST['initgetpoint'][$i])){
				$values=$values.$_POST['initgetpoint'][$i].',';
			}
			else{
				$values=$values.'0,';
			}
			$values=$values.'1,';//2020/4/14 TAXCODE4=>品項贈點類別(getpointtype);1>>固定點數(預設) 2>>金額點數；單品促銷點數優惠只能使用在固定點數
			if(isset($_POST['dispoint'][$i])){//2020/4/14 TAXCODE5=>固定點數(getpoint)；單品促銷點數優惠所使用點數
				$values=$values.'-'.$_POST['dispoint'][$i].',';
			}
			else{
				$values=$values.'0,';
			}
			$values=$values.'"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
			$index++;
			date_default_timezone_set($content['init']['settime']);
			fwrite($handle,date('Y/m/d H:i:s').' -- '.$values.PHP_EOL);
			$sql=$sql.$values.';';
		
		}
	}
	if(sizeof($kds)>0){
		foreach($kds as $v){
			fclose($v['file']);
			rename($v['dir'].'/'.$v['filename'],$v['dir'].'/'.substr($v['filename'],4));
		}
	}
	else{
	}
}
else{
}
if(isset($posdvr)){
	$tempdvrcontent .= "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL;
}
else{
}

//echo $sql;
//sqlnoresponse($conn,$sql,'sqliteexec');
date_default_timezone_set($content['init']['settime']);
fwrite($handle,date('Y/m/d H:i:s').' -- CST011 - '.$consecnumber.PHP_EOL);
if(isset($_POST['templistitem'])){//存在已出單品項
	if(isset($_POST['listtotal'])){//利用'結帳'功能結帳
		if(isset($_POST['memberdis'])&&$_POST['memberdis']>0){//會員優惠
			$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
			
			if($selectValue == ""){
				$values='("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
			
			}else{
				$values='("'.$_POST['machinetype'].'","'.$selectValue.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
			
			}
			// $values='("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
			
			
			if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
				$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
			}
			else{
				$values=$values.'"","",';
			}
			$values=$values.'"1","3","02","member","會員優惠","","","","",';
			for($j=0;$j<10;$j++){
				$values=$values.'NULL,';
			}
			if($content['init']['accuracytype']==1){//四捨五入
				$_POST['memberdis']=round($_POST['memberdis'],$content['init']['accuracy']);
			}
			else if($content['init']['accuracytype']==2){//無條件進位
				$_POST['memberdis']=ceil($_POST['memberdis'],$content['init']['accuracy']);
			}
			else{//無條件捨去
				$_POST['memberdis']=floor($_POST['memberdis'],$content['init']['accuracy']);
			}
			$values=$values.'0,0,0,-'.$_POST['memberdis'].',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
			$index++;
			$sql=$sql.$values.';';
			//sqlnoresponse($conn,$sql.$values,'sqlite');
		}
		else{
		}
		if($_POST['listdis1']>0||$_POST['listdis2']>0){
			$sql=$sql."DELETE FROM tempCST012 WHERE BIZDATE='".$bizdate."' AND CONSECNUMBER='".$consecnumber."' AND ITEMCODE='list';INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
			$values='("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
			if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
				$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
			}
			else{
				$values=$values.'"","",';
			}
			
			$values=$values.'"1","3","02","list","帳單優惠","","","","",';
			for($j=0;$j<10;$j++){
				$values=$values.'NULL,';
			}
			/*$temp=($_POST['listdis1']+$_POST['listdis2']);
			if($content['init']['accuracytype']==1){//四捨五入
				$temp=round($temp,$content['init']['accuracy']);
			}
			else if($content['init']['accuracytype']==1){//無條件進位
				$temp=ceil($temp,$content['init']['accuracy']);
			}
			else{//無條件捨去
				$temp=floor($temp,$content['init']['accuracy']);
			}*/
			$temp=floatval($_POST['listtotal'])-floatval($_POST['itemdis'])-floatval($_POST['autodis'])+floatval($_POST['charge'])+floatval($_POST['floorspan'])-floatval($_POST['should']);
			if(isset($_POST['memberdis'])&&$_POST['memberdis']>0){//2021/8/3 若不將會員折扣扣除，帳單優惠的金額會包含到該金額
				$temp=floatval($temp)-floatval($_POST['memberdis']);
			}
			else{
			}
			$values=$values.'0,0,0,-'.$temp.',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
			$index++;
			$sql=$sql.$values.';';
			//sqlnoresponse($conn,$sql.$values,'sqlite');
		}
		else{
		}
		if(isset($_POST['autodis'])&&$_POST['autodis']>0){
			$sqlselect='SELECT COUNT(*) AS num FROM tempCST012 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis"';
			$tesel=sqlquery($conn,$sqlselect,'sqlite');
			if(sizeof($tesel)>0&&isset($tesel[0]['num'])&&intval($tesel[0]['num'])>0){
				//echo 'ABCDE';
				$sql=$sql."UPDATE tempCST012 SET AMT=";
				
				if($content['init']['accuracytype']==1){//四捨五入
					$_POST['autodis']=round($_POST['autodis'],$content['init']['accuracy']);
				}
				else if($content['init']['accuracytype']==2){//無條件進位
					$_POST['autodis']=ceil($_POST['autodis'],$content['init']['accuracy']);
				}
				else{//無條件捨去
					$_POST['autodis']=floor($_POST['autodis'],$content['init']['accuracy']);
				}
				$values='-'.$_POST['autodis'].' WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis"';
			}
			else{
				$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
				$values='("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
				if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
					$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
				}
				else{
					$values=$values.'"","",';
				}
				$values=$values.'"1","3","02","autodis","自動優惠","'.$_POST['autodiscontent'].'","'.$_POST['autodispremoney'].'","","",';
				for($j=0;$j<10;$j++){
					$values=$values.'NULL,';
				}
				if($content['init']['accuracytype']==1){//四捨五入
					$_POST['autodis']=round($_POST['autodis'],$content['init']['accuracy']);
				}
				else if($content['init']['accuracytype']==2){//無條件進位
					$_POST['autodis']=ceil($_POST['autodis'],$content['init']['accuracy']);
				}
				else{//無條件捨去
					$_POST['autodis']=floor($_POST['autodis'],$content['init']['accuracy']);
				}
				$values=$values.'0,0,0,-'.$_POST['autodis'].',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
			}
			$index++;
			$sql=$sql.$values.';';
			//sqlnoresponse($conn,$sql.$values,'sqlite');
		}
		else{
		}
		/*if($_POST['charge']>0){
			$values='';
			if(isset($_POST['tablenumber'])&&strlen($_POST['tablenumber'])>0){
				$values='("'.trim($_POST['tablenumber']).'","';
			}
			else{
				$values='("'.$machinedata['basic']['terminalnumber'].'","';
			}
			$values=$values.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
			if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
				$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
			}
			else{
				$values=$values.'"","",';
			}
			$values=$values.'"1","3","02","charge","服務費","","","","",';
			for($j=0;$j<10;$j++){
				$values=$values.'NULL,';
			}
			$values=$values.'0,0,0,'.$_POST['charge'].',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
			$index++;
			sqlnoresponse($conn,$sql.$values,'sqlite');
		}
		else{
		}*/
		$sql=$sql.'UPDATE tempCST011 SET TAX1=';
		if(isset($_POST['charge'])&&$_POST['charge']>0){
			$sql=$sql.$_POST['charge'].',';
		}
		else{
			$sql=$sql.'0,';
		}
		$sql=$sql.'TAX2=';
		if(isset($_POST['cashmoney'])&&$_POST['cashmoney']>0){
			$sql=$sql.$_POST['cashmoney'].',';
		}
		else{
			$sql=$sql.'0,';
		}
		$sql=$sql.'TAX3=';
		if(isset($_POST['cash'])&&$_POST['cash']>0){
			$sql=$sql.$_POST['cash'].',';
		}
		else{
			$sql=$sql.'0,';
		}
		$sql=$sql.'TAX4=';
		if(isset($_POST['other'])&&$_POST['other']>0&&isset($_POST['otherfix'])&&$_POST['otherfix']>0){
			$sql=$sql.($_POST['other']+$_POST['otherfix']).',';
		}
		else if(isset($_POST['other'])&&$_POST['other']>0){
			$sql=$sql.$_POST['other'].',';
		}
		else if(isset($_POST['otherfix'])&&$_POST['otherfix']>0){
			$sql=$sql.$_POST['otherfix'].',';
		}
		else{
			$sql=$sql.'0,';
		}
		if(isset($_POST['ininv'])){//2020/2/6優先判斷
			if($_POST['ininv']>0){
				$sql=$sql.'TAX5='.$_POST['ininv'].',';
			}
			else{
				$sql=$sql.'TAX5=0,';
			}
		}
		else if(isset($_POST['invshould'])){
			if($_POST['invshould']>0){
				$sql=$sql.'TAX5='.$_POST['invshould'].',';
			}
			else{
				//$sql=$sql.'TAX5=0,';
			}
		}
		else if(isset($_POST['invsalemoney'])&&$_POST['invsalemoney']>0){
			$sql=$sql.'TAX5=';
			$sql=$sql.$_POST['invsalemoney'].',';
		}
		else{
			//$sql=$sql.'0,';
		}
		$sql=$sql.'TAX6=';
		if(isset($_POST['person1'])&&$_POST['person1']>0){
			$sql=$sql.$_POST['person1'].',';
		}
		else{
			$sql=$sql.'0,';
			$_POST['person1']=0;
		}
		$sql=$sql.'TAX7=';
		if(isset($_POST['person2'])&&$_POST['person2']>0){
			$sql=$sql.$_POST['person2'].',';
		}
		else{
			$sql=$sql.'0,';
			$_POST['person2']=0;
		}
		$sql=$sql.'TAX8=';
		if(isset($_POST['person3'])&&$_POST['person3']>0){
			$sql=$sql.$_POST['person3'].',';
		}
		else{
			$sql=$sql.'0,';
			$_POST['person3']=0;
		}
		$sql=$sql.'TAX9=';
		if(isset($_POST['cashcomm'])&&$_POST['cashcomm']>0){
			$sql=$sql.$_POST['cashcomm'].',';
		}
		else{
			$sql=$sql.'0,';
		}
		if(isset($otherpay)&&sizeof($otherpay)>0){
			foreach($otherpay as $otindex=>$otvalue){
				if(preg_match('/intella/',$otindex)){
					$sql=$sql.'intella="'.$_POST['intellaconsecnumber'].':'.$otvalue.'",';
					$selectsql='PRAGMA table_info(tempCST011)';
					$column=sqlquery($conn,$selectsql,'sqlite');
					$columnname=array_column($column,'name');
					if(in_array('intella',$columnname)){
					}
					else{
						$insertsql='ALTER TABLE tempCST011 ADD COLUMN intella TEXT';
						sqlnoresponse($conn,$insertsql,'sqlite');
					}
					$selectsql='PRAGMA table_info(CST011)';
					$column=sqlquery($conn,$selectsql,'sqlite');
					$columnname=array_column($column,'name');
					if(in_array('intella',$columnname)){
					}
					else{
						$insertsql='ALTER TABLE CST011 ADD COLUMN intella TEXT';
						sqlnoresponse($conn,$insertsql,'sqlite');
					}
				}
				else if(preg_match('/nidin/',$otindex)){//2021/3/10 nidin付款因為無法修改，因此不用修改資料庫，只要過濾
				}
				else if(preg_match('/TA[1-9]/',$otindex)){
					$sql=$sql.$otindex.'="'.$otvalue.'",';
				}
				else{
					/*if($otindex=='member'){
					}
					else{*/
						$sql=$sql.$otindex.'="'.$otvalue['value'].'",';
					//}
				}
			}
		}
		else{
			for($rowindex=1;$rowindex<=10;$rowindex++){
				$sql=$sql.'TA'.$rowindex.'=0,';
			}
			if(isset($otherpaydata[0])){
				for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
					$sql=$sql.$otherpaydata[$rowindex].'=0,';
				}
			}
			else{
			}
		}
		if(isset($_POST['mancode'])&&$_POST['mancode']!=''&&isset($_POST['listtype'])&&($_POST['listtype']=='3'||$_POST['listtype']=='4')){
			$sql=$sql.'CUSTGPCODE="'.$_POST['mancode'].'",CUSTGPNAME="'.$_POST['manname'].'",';
		}
		else{
			$sql=$sql.'CUSTGPCODE=NULL,CUSTGPNAME=NULL,';
		}
		if(isset($_POST['memno'])&&$_POST['memno']!=''){
			$sql=$sql.'CUSTCODE="'.$memdata[0]['memno'].';-;'.$memdata[0]['tel'];
			if(isset($_POST['memaddno'])){
				$sql=$sql.';-;'.$_POST['memaddno'];
			}
			else{
			}
			$sql=$sql.'",CUSTNAME="'.$memdata[0]['name'].'",';
		}
		else{
			$sql=$sql.'CUSTCODE=NULL,CUSTNAME=NULL,';
		}
		$sql=$sql.'SALESTTLQTY='.$totalqty.',SALESTTLAMT='.($_POST['should']-$_POST['charge']).',';
		if(isset($_POST['creditcard'])&&$_POST['creditcard']!=''){
			$sql=$sql.'CREDITCARD="'.$_POST['creditcard'].'"';
		}
		else{
			$sql=$sql.'CREDITCARD=NULL';
		}

		if(isset($content['init']['linklist'])&&$content['init']['linklist']=='1'&&isset($_POST['linklist'])&&$_POST['listtype']=='2'){//開啟連接內用帳單
			$sql=$sql.',TABLENUMBER="'.$_POST['linklist'].'"';
		}
		else{
		}

		if(isset($_POST['salelisthint'])){
			$sql=$sql.',RELINVOICENUMBER="'.$_POST['salelisthint'].'"';
		}
		else{
		}
		if(isset($_POST['buyerdata'])&&$_POST['buyerdata']!=null&&$_POST['buyerdata']!=''){
			$sql=$sql.',RELINVOICETIME="'.$_POST['buyerdata'].'"';
		}
		else{
		}

		$sql=$sql.' WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'";';
		if($content['init']['controltable']==1){
			$tablist=preg_split('/,/',trim($_POST['tablenumber']));
			$datasql='SELECT ZCOUNTER,CREATEDATETIME FROM tempCST011 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'"';
			$zcounterdata=sqlquery($conn,$datasql,'sqlite');
			if(isset($zcounterdata)&&sizeof($zcounterdata)>0){
				foreach($tablist as $tl){
					if(file_exists('../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini')){//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						$tabdata=parse_ini_file('../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini',true);//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						$tabdata[$tl]['consecnumber']=$consecnumber;
						$tabdata[$tl]['saleamt']=($_POST['should']-$_POST['charge']);
						$tabdata[$tl]['person']=($_POST['person1']+$_POST['person2']+$_POST['person3']);

						$tabdata[$tl]['state']="1";
						$tabdata[$tl]['machine']="";
						write_ini_file($tabdata,'../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini');//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
					}
					else{
						$file='../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini';//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						$f=fopen($file,'a');
						fwrite($f,'['.$tl.']'.PHP_EOL);
						fwrite($f,'bizdate="'.$bizdate.'"'.PHP_EOL);
						fwrite($f,'zcounter="'.$zcounterdata[0]['ZCOUNTER'].'"'.PHP_EOL);
						fwrite($f,'consecnumber="'.$consecnumber.'"'.PHP_EOL);
						fwrite($f,'saleamt="'.($_POST['should']-$_POST['charge']).'"'.PHP_EOL);
						fwrite($f,'person="'.($_POST['person1']+$_POST['person2']+$_POST['person3']).'"'.PHP_EOL);
						fwrite($f,'createdatetime="'.$zcounterdata[0]['CREATEDATETIME'].'"'.PHP_EOL);
						fwrite($f,'table="'.$_POST['tablenumber'].'"'.PHP_EOL);
						if(strstr($_POST['tablenumber'],',')){
							fwrite($f,'tablestate="1"'.PHP_EOL);
						}
						else{
							fwrite($f,'tablestate="0"'.PHP_EOL);
						}
						fwrite($f,'state="1"'.PHP_EOL);
						fwrite($f,'machine=""'.PHP_EOL);
						fclose($f);
					}
				}
			}
			else{
			}
		}
		else{
		}
	}
	else{
		$sql=$sql.'UPDATE tempCST011 SET TAX1=';
		if(isset($_POST['charge'])&&$_POST['charge']>0){
			$sql=$sql.$_POST['charge'].',';
			$sql=$sql.'TAX2='.(intval($_POST['total'])+intval($_POST['charge'])).',';
		}
		else{
			$sql=$sql.'0,';
			$sql=$sql.'TAX2='.(intval($_POST['total'])).',';
		}
		if(isset($_POST['ininv'])){//2020/2/6優先判斷
			if($_POST['ininv']>0){
				$sql=$sql.'TAX5='.$_POST['ininv'].',';
			}
			else{
				$sql=$sql.'TAX5=0,';
			}
		}
		else if(isset($_POST['invshould'])){
			if($_POST['invshould']>0){
				$sql=$sql.'TAX5='.$_POST['invshould'].',';
			}
			else{
				//$sql=$sql.'TAX5=0,';
			}
		}
		else if(isset($_POST['invsalemoney'])&&$_POST['invsalemoney']>0){
			$sql=$sql.'TAX5=';
			$sql=$sql.$_POST['invsalemoney'].',';
		}
		else{
			//$sql=$sql.'0,';
		}
		$sql=$sql.'TAX6=';
		if(isset($_POST['person1'])&&$_POST['person1']>0){
			$sql=$sql.$_POST['person1'].',';
		}
		else{
			$sql=$sql.'0,';
			$_POST['person1']=0;
		}
		$sql=$sql.'TAX7=';
		if(isset($_POST['person2'])&&$_POST['person2']>0){
			$sql=$sql.$_POST['person2'].',';
		}
		else{
			$sql=$sql.'0,';
			$_POST['person2']=0;
		}
		$sql=$sql.'TAX8=';
		if(isset($_POST['person3'])&&$_POST['person3']>0){
			$sql=$sql.$_POST['person3'].',';
		}
		else{
			$sql=$sql.'0,';
			$_POST['person3']=0;
		}
		$sql=$sql.'TAX9=';
		if(isset($_POST['cashcomm'])&&$_POST['cashcomm']>0){
			$sql=$sql.$_POST['cashcomm'].',';
		}
		else{
			$sql=$sql.'0,';
		}
		if(isset($_POST['mancode'])&&$_POST['mancode']!=''&&isset($_POST['listtype'])&&($_POST['listtype']=='3'||$_POST['listtype']=='4')){
			$sql=$sql.'CUSTGPCODE="'.$_POST['mancode'].'",CUSTGPNAME="'.$_POST['manname'].'",';
		}
		else{
			$sql=$sql.'CUSTGPCODE=NULL,CUSTGPNAME=NULL,';
		}
		if(isset($_POST['memno'])&&$_POST['memno']!=''){
			$sql=$sql.'CUSTCODE="'.$memdata[0]['memno'].';-;'.$memdata[0]['tel'];
			if(isset($_POST['memaddno'])){
				$sql=$sql.';-;'.$_POST['memaddno'];
			}
			else{
			}
			$sql=$sql.'",CUSTNAME="'.$memdata[0]['name'].'",';
		}
		else{
			$sql=$sql.'CUSTCODE=NULL,CUSTNAME=NULL,';
		}

		if(isset($content['init']['linklist'])&&$content['init']['linklist']=='1'&&isset($_POST['linklist'])&&$_POST['listtype']=='2'){//開啟連接內用帳單
			$sql=$sql.'TABLENUMBER="'.$_POST['linklist'].'",';
		}
		else{
		}

		$sql=$sql.'SALESTTLQTY='.$totalqty.',SALESTTLAMT='.$_POST['total'];
		if(isset($_POST['salelisthint'])){
			$sql=$sql.',RELINVOICENUMBER="'.$_POST['salelisthint'].'"';
		}
		else{
		}
		if(isset($_POST['buyerdata'])&&$_POST['buyerdata']!=null&&$_POST['buyerdata']!=''){
			$sql=$sql.',RELINVOICETIME="'.$_POST['buyerdata'].'"';
		}
		else{
		}
		$sql=$sql.' WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'";';
		//echo $_POST['total'];
		if($content['init']['controltable']==1){
			$tablist=preg_split('/,/',trim($_POST['tablenumber']));
			$datasql='SELECT ZCOUNTER,CREATEDATETIME FROM tempCST011 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'"';
			$zcounterdata=sqlquery($conn,$datasql,'sqlite');
			if(isset($zcounterdata)&&sizeof($zcounterdata)>0){
				foreach($tablist as $tl){
					if(file_exists('../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini')){//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						$tabdata=parse_ini_file('../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini',true);//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						$tabdata[$tl]['consecnumber']=$consecnumber;
						$tabdata[$tl]['saleamt']=$_POST['total'];
						$tabdata[$tl]['person']=($_POST['person1']+$_POST['person2']+$_POST['person3']);

						$tabdata[$tl]['state']="1";
						$tabdata[$tl]['machine']="";
						write_ini_file($tabdata,'../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini');//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
					}
					else{
						$file='../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini';//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						$f=fopen($file,'a');
						fwrite($f,'['.$tl.']'.PHP_EOL);
						fwrite($f,'bizdate="'.$bizdate.'"'.PHP_EOL);
						fwrite($f,'zcounter="'.$zcounterdata[0]['ZCOUNTER'].'"'.PHP_EOL);
						fwrite($f,'consecnumber="'.$consecnumber.'"'.PHP_EOL);
						fwrite($f,'saleamt="'.$_POST['total'].'"'.PHP_EOL);
						fwrite($f,'person="'.($_POST['person1']+$_POST['person2']+$_POST['person3']).'"'.PHP_EOL);
						fwrite($f,'createdatetime="'.$zcounterdata[0]['CREATEDATETIME'].'"'.PHP_EOL);
						fwrite($f,'table="'.$_POST['tablenumber'].'"'.PHP_EOL);
						if(strstr($_POST['tablenumber'],',')){
							fwrite($f,'tablestate="1"'.PHP_EOL);
						}
						else{
							fwrite($f,'tablestate="0"'.PHP_EOL);
						}
						fwrite($f,'state="1"'.PHP_EOL);
						fwrite($f,'machine=""'.PHP_EOL);
						fclose($f);
					}
				}
			}
			else{
			}
		}
		else{
		}
	}
	if(isset($_POST['listtype'])){
		$sql=$sql.'UPDATE tempCST011 SET REMARKS="'.$_POST['listtype'].'" WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'";';
		$sql=$sql.'UPDATE tempCST012 SET REMARKS="'.$_POST['listtype'].'" WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'";';
	}
	else{
	}
}
else{
	$sqlselect='SELECT COUNT(*) AS num FROM tempCST011 WHERE CONSECNUMBER="'.$consecnumber.'"';
	$test=sqlquery($conn,$sqlselect,'sqlite');
	if($test[0]['num']!=null&&intval($test[0]['num'])>0){//帳單已暫結過
		if(isset($_POST['listtotal'])){//暫結單且已開發票之帳單結帳僅會出現在這，因為不會有templistitem、tempCST011一定找的到
			if(isset($_POST['memberdis'])&&$_POST['memberdis']>0){
				$sqlselect='SELECT COUNT(*) AS num FROM tempCST012 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="member"';
				$tesel=sqlquery($conn,$sqlselect,'sqlite');
				if(sizeof($tesel)>0&&isset($tesel[0]['num'])&&intval($tesel[0]['num'])>0){
					$sql=$sql."UPDATE tempCST012 SET AMT=";
					
					if($content['init']['accuracytype']==1){//四捨五入
						$_POST['memberdis']=round($_POST['memberdis'],$content['init']['accuracy']);
					}
					else if($content['init']['accuracytype']==2){//無條件進位
						$_POST['memberdis']=ceil($_POST['memberdis'],$content['init']['accuracy']);
					}
					else{//無條件捨去
						$_POST['memberdis']=floor($_POST['memberdis'],$content['init']['accuracy']);
					}
					$values='-'.$_POST['memberdis'].'WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="member"';

				}
				else{
					$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME)";
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$sql=$sql." VALUES (";
					}
					else{
						$sql=$sql." SELECT ";
					}
					$values='"'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
					if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
						$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
					}
					else{
						$values=$values.'"","",';
					}
					$values=$values.'"1","3","02","member","會員優惠","","","","",';
					for($j=0;$j<10;$j++){
						$values=$values.'NULL,';
					}
					if($content['init']['accuracytype']==1){//四捨五入
						$_POST['memberdis']=round($_POST['memberdis'],$content['init']['accuracy']);
					}
					else if($content['init']['accuracytype']==2){//無條件進位
						$_POST['memberdis']=ceil($_POST['memberdis'],$content['init']['accuracy']);
					}
					else{//無條件捨去
						$_POST['memberdis']=floor($_POST['memberdis'],$content['init']['accuracy']);
					}
					$values=$values.'0,0,0,-'.$_POST['memberdis'].',"'.$timeini['time']['zcounter'].'",';
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$values=$values.'"'.$_POST['listtype'].'"';
					}
					else{
						$values=$values.'REMARKS';
					}
					$values=$values.',"'.$insertdate.'"';
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$values=$values.')';
					}
					else{
						$values=$values.' FROM tempCST012 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER ASC LIMIT 1';
					}
				}
				$index++;
				$sql=$sql.$values.';';
				//sqlnoresponse($conn,$sql.$values,'sqlite');
			}
			else{
			}
			if($_POST['listdis1']>0||$_POST['listdis2']>0){
				$sqlselect='SELECT COUNT(*) AS num FROM tempCST012 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="list"';
				$tesel=sqlquery($conn,$sqlselect,'sqlite');
				if(sizeof($tesel)>0&&isset($tesel[0]['num'])&&intval($tesel[0]['num'])>0){
					$sql=$sql."UPDATE tempCST012 SET AMT=";
					
					$temp=floatval($_POST['listtotal'])-floatval($_POST['itemdis'])-floatval($_POST['autodis'])+floatval($_POST['charge'])+floatval($_POST['floorspan'])-floatval($_POST['should']);
					if(isset($_POST['memberdis'])&&$_POST['memberdis']>0){//2021/8/3 若不將會員折扣扣除，帳單優惠的金額會包含到該金額
						$temp=floatval($temp)-floatval($_POST['memberdis']);
					}
					else{
					}
					/*$temp=($_POST['listdis1']+$_POST['listdis2']);
					if($content['init']['accuracytype']==1){//四捨五入
						$temp=round($temp,$content['init']['accuracy']);
					}
					else if($content['init']['accuracytype']==1){//無條件進位
						$temp=ceil($temp,$content['init']['accuracy']);
					}
					else{//無條件捨去
						$temp=floor($temp,$content['init']['accuracy']);
					}*/
					$values='-'.$temp.' WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="list"';
					
				}
				else{
					$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME)";
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$sql=$sql." VALUES (";
					}
					else{
						$sql=$sql." SELECT ";
					}
					$values='"'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
					if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
						$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
					}
					else{
						$values=$values.'"","",';
					}
					$values=$values.'"1","3","02","list","帳單優惠","","","","",';
					for($j=0;$j<10;$j++){
						$values=$values.'NULL,';
					}
					$temp=floatval($_POST['listtotal'])-floatval($_POST['itemdis'])-floatval($_POST['autodis'])+floatval($_POST['charge'])+floatval($_POST['floorspan'])-floatval($_POST['should']);
					if(isset($_POST['memberdis'])&&$_POST['memberdis']>0){//2021/8/3 若不將會員折扣扣除，帳單優惠的金額會包含到該金額
						$temp=floatval($temp)-floatval($_POST['memberdis']);
					}
					else{
					}
					/*$temp=($_POST['listdis1']+$_POST['listdis2']);
					if($content['init']['accuracytype']==1){//四捨五入
						$temp=round($temp,$content['init']['accuracy']);
					}
					else if($content['init']['accuracytype']==1){//無條件進位
						$temp=ceil($temp,$content['init']['accuracy']);
					}
					else{//無條件捨去
						$temp=floor($temp,$content['init']['accuracy']);
					}*/
					$values=$values.'0,0,0,-'.$temp.',"'.$timeini['time']['zcounter'].'",';
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$values=$values.'"'.$_POST['listtype'].'"';
					}
					else{
						$values=$values.'REMARKS';
					}
					$values=$values.',"'.$insertdate.'"';
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$values=$values.')';
					}
					else{
						$values=$values.' FROM tempCST012 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER ASC LIMIT 1';
					}
				}
				$index++;
				$sql=$sql.$values.';';
				//echo $sql;
				//sqlnoresponse($conn,$sql.$values,'sqlite');
			}
			else{
			}
			if($_POST['autodis']>0){
				$sqlselect='SELECT COUNT(*) AS num FROM tempCST012 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis"';
				$tesel=sqlquery($conn,$sqlselect,'sqlite');
				if(sizeof($tesel)>0&&isset($tesel[0]['num'])&&intval($tesel[0]['num'])>0){
					//echo 'ABCDE';
					$sql=$sql."UPDATE tempCST012 SET AMT=";
					
					if($content['init']['accuracytype']==1){//四捨五入
						$_POST['autodis']=round($_POST['autodis'],$content['init']['accuracy']);
					}
					else if($content['init']['accuracytype']==2){//無條件進位
						$_POST['autodis']=ceil($_POST['autodis'],$content['init']['accuracy']);
					}
					else{//無條件捨去
						$_POST['autodis']=floor($_POST['autodis'],$content['init']['accuracy']);
					}
					$values='-'.$_POST['autodis'].' WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis"';
				}
				else{
					$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME)";
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$sql=$sql." VALUES (";
					}
					else{
						$sql=$sql." SELECT ";
					}
					$values='"'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
					if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
						$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
					}
					else{
						$values=$values.'"","",';
					}
					$values=$values.'"1","3","02","autodis","自動優惠","'.$_POST['autodiscontent'].'","'.$_POST['autodispremoney'].'","","",';
					for($j=0;$j<10;$j++){
						$values=$values.'NULL,';
					}
					if($content['init']['accuracytype']==1){//四捨五入
						$_POST['autodis']=round($_POST['autodis'],$content['init']['accuracy']);
					}
					else if($content['init']['accuracytype']==2){//無條件進位
						$_POST['autodis']=ceil($_POST['autodis'],$content['init']['accuracy']);
					}
					else{//無條件捨去
						$_POST['autodis']=floor($_POST['autodis'],$content['init']['accuracy']);
					}
					$values=$values.'0,0,0,-'.$_POST['autodis'].',"'.$timeini['time']['zcounter'].'",';
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$values=$values.'"'.$_POST['listtype'].'"';
					}
					else{
						$values=$values.'REMARKS';
					}
					$values=$values.',"'.$insertdate.'"';
					if(!isset($_POST['sendtype'])||$_POST['sendtype']=='result'){
						$values=$values.')';
					}
					else{
						$values=$values.' FROM tempCST012 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER ASC LIMIT 1';
					}
				}
				$index++;
				$sql=$sql.$values.';';
				//sqlnoresponse($conn,$sql.$values,'sqlite');
			}
			else{
			}
			$sql=$sql.'UPDATE tempCST011 SET TAX1=';
			if(isset($_POST['charge'])&&$_POST['charge']>0){
				$sql=$sql.$_POST['charge'].',';
			}
			else{
				$sql=$sql.'0,';
			}
			$sql=$sql.'TAX2=';
			if(isset($_POST['cashmoney'])&&$_POST['cashmoney']>0){
				$sql=$sql.$_POST['cashmoney'].',';
			}
			else{
				$sql=$sql.'0,';
			}
			$sql=$sql.'TAX3=';
			if(isset($_POST['cash'])&&$_POST['cash']>0){
				$sql=$sql.$_POST['cash'].',';
			}
			else{
				$sql=$sql.'0,';
			}
			$sql=$sql.'TAX4=';
			if(isset($_POST['other'])&&$_POST['other']>0&&isset($_POST['otherfix'])&&$_POST['otherfix']>0){
				$sql=$sql.($_POST['other']+$_POST['otherfix']).',';
			}
			else if(isset($_POST['other'])&&$_POST['other']>0){
				$sql=$sql.$_POST['other'].',';
			}
			else if(isset($_POST['otherfix'])&&$_POST['otherfix']>0){
				$sql=$sql.$_POST['otherfix'].',';
			}
			else{
				$sql=$sql.'0,';
			}
			if($_POST['sendtype']=='buytemp'||$_POST['sendtype']=='tempsale'){
				if(isset($_POST['ininv'])){//2020/2/6優先判斷
					if($_POST['ininv']>0){
						$sql=$sql.'TAX5='.$_POST['ininv'].',';
					}
					else{
						$sql=$sql.'TAX5=0,';
					}
				}
			}
			else{
				if(isset($_POST['ininv'])){//2020/2/6優先判斷
					if($_POST['ininv']>0){
						$sql=$sql.'TAX5='.$_POST['ininv'].',';
					}
					else{
						$sql=$sql.'TAX5=0,';
					}
				}
				else if(isset($_POST['invshould'])){
					if($_POST['invshould']>0){
						$sql=$sql.'TAX5='.$_POST['invshould'].',';
					}
					else{
						//$sql=$sql.'TAX5=0,';
					}
				}
				else if(isset($_POST['invsalemoney'])&&$_POST['invsalemoney']>0){
					$sql=$sql.'TAX5=';
					$sql=$sql.$_POST['invsalemoney'].',';
				}
				else{
					//$sql=$sql.'0,';
				}
				$sql=$sql.'TAX6=';
				if(isset($_POST['person1'])&&$_POST['person1']>0){
					$sql=$sql.$_POST['person1'].',';
				}
				else{
					$sql=$sql.'0,';
					$_POST['person1']=0;
				}
				$sql=$sql.'TAX7=';
				if(isset($_POST['person2'])&&$_POST['person2']>0){
					$sql=$sql.$_POST['person2'].',';
				}
				else{
					$sql=$sql.'0,';
					$_POST['person2']=0;
				}
				$sql=$sql.'TAX8=';
				if(isset($_POST['person3'])&&$_POST['person3']>0){
					$sql=$sql.$_POST['person3'].',';
				}
				else{
					$sql=$sql.'0,';
					$_POST['person3']=0;
				}
			}
			$sql=$sql.'TAX9=';
			if(isset($_POST['cashcomm'])&&$_POST['cashcomm']>0){
				$sql=$sql.$_POST['cashcomm'].',';
			}
			else{
				$sql=$sql.'0,';
			}
			if(isset($otherpay)&&sizeof($otherpay)>0){
				foreach($otherpay as $otindex=>$otvalue){
					if(preg_match('/TA[1-9]/',$otindex)){
						$sql=$sql.$otindex.'="'.$otvalue.'",';
					}
					else{
						$sql=$sql.$otindex.'="'.$otvalue['value'].'",';
					}
				}
			}
			else{
				for($rowindex=1;$rowindex<=10;$rowindex++){
					$sql=$sql.'TA'.$rowindex.'=0,';
				}
				if(isset($otherpaydata[0])){
					for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
						$sql=$sql.$otherpaydata[$rowindex].'=0,';
					}
				}
				else{
				}
			}
			if($_POST['sendtype']=='buytemp'||$_POST['sendtype']=='tempsale'){
			}
			else{
				if(isset($_POST['mancode'])&&$_POST['mancode']!=''&&isset($_POST['listtype'])&&($_POST['listtype']=='3'||$_POST['listtype']=='4')){
					$sql=$sql.'CUSTGPCODE="'.$_POST['mancode'].'",CUSTGPNAME="'.$_POST['manname'].'",';
				}
				else{
					$sql=$sql.'CUSTGPCODE=NULL,CUSTGPNAME=NULL,';
				}
				if(isset($_POST['memno'])&&$_POST['memno']!=''){
					$sql=$sql.'CUSTCODE="'.$memdata[0]['memno'].';-;'.$memdata[0]['tel'];
					if(isset($_POST['memaddno'])){
						$sql=$sql.';-;'.$_POST['memaddno'];
					}
					else{
					}
					$sql=$sql.'",CUSTNAME="'.$memdata[0]['name'].'",';
				}
				else{
					$sql=$sql.'CUSTCODE=NULL,CUSTNAME=NULL,';
				}
			}
			if($_POST['sendtype']=='buytemp'||$_POST['sendtype']=='tempsale'){
			}
			else{
				$sql=$sql.'SALESTTLQTY='.$totalqty.',';
			}
			$sql=$sql.'SALESTTLAMT='.($_POST['should']-$_POST['charge']).',';
			if(isset($_POST['creditcard'])&&$_POST['creditcard']!=''){
				$sql=$sql.'CREDITCARD="'.$_POST['creditcard'].'"';
			}
			else{
				$sql=$sql.'CREDITCARD=NULL';
			}

			if(isset($content['init']['linklist'])&&$content['init']['linklist']=='1'&&isset($_POST['linklist'])&&$_POST['listtype']=='2'){//開啟連接內用帳單
				$sql=$sql.',TABLENUMBER="'.$_POST['linklist'].'"';
			}
			else{
			}

			if(isset($_POST['salelisthint'])){
				$sql=$sql.',RELINVOICENUMBER="'.$_POST['salelisthint'].'"';
			}
			else{
			}
			if(isset($_POST['buyerdata'])&&$_POST['buyerdata']!=null&&$_POST['buyerdata']!=''){
				$sql=$sql.',RELINVOICETIME="'.$_POST['buyerdata'].'"';
			}
			else{
			}

			$sql=$sql.' WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'";';
			//echo $sql;
			if($content['init']['controltable']==1){
				$tablist=preg_split('/,/',trim($_POST['tablenumber']));
				$datasql='SELECT ZCOUNTER,CREATEDATETIME FROM tempCST011 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'"';
				$zcounterdata=sqlquery($conn,$datasql,'sqlite');
				if(isset($zcounterdata)&&sizeof($zcounterdata)>0){
					foreach($tablist as $tl){
						if(file_exists('../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini')){//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$tabdata=parse_ini_file('../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini',true);//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$tabdata[$tl]['consecnumber']=$consecnumber;
							$tabdata[$tl]['saleamt']=($_POST['should']-$_POST['charge']);
							$tabdata[$tl]['person']=($_POST['person1']+$_POST['person2']+$_POST['person3']);

							$tabdata[$tl]['state']="1";
							$tabdata[$tl]['machine']="";
							write_ini_file($tabdata,'../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini');//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						}
						else{
							$file='../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini';//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$f=fopen($file,'a');
							fwrite($f,'['.$tl.']'.PHP_EOL);
							fwrite($f,'bizdate="'.$bizdate.'"'.PHP_EOL);
							fwrite($f,'zcounter="'.$zcounterdata[0]['ZCOUNTER'].'"'.PHP_EOL);
							fwrite($f,'consecnumber="'.$consecnumber.'"'.PHP_EOL);
							fwrite($f,'saleamt="'.($_POST['should']-$_POST['charge']).'"'.PHP_EOL);
							fwrite($f,'person="'.($_POST['person1']+$_POST['person2']+$_POST['person3']).'"'.PHP_EOL);
							fwrite($f,'createdatetime="'.$zcounterdata[0]['CREATEDATETIME'].'"'.PHP_EOL);
							fwrite($f,'table="'.$_POST['tablenumber'].'"'.PHP_EOL);
							if(strstr($_POST['tablenumber'],',')){
								fwrite($f,'tablestate="1"'.PHP_EOL);
							}
							else{
								fwrite($f,'tablestate="0"'.PHP_EOL);
							}
							fwrite($f,'state="1"'.PHP_EOL);
							fwrite($f,'machine=""'.PHP_EOL);
							fclose($f);
						}
					}
				}
				else{
				}
			}
			else{
			}
		}
		else{
			$sql=$sql.'UPDATE tempCST011 SET TAX1=';
			if(isset($_POST['charge'])&&$_POST['charge']>0){
				$sql=$sql.$_POST['charge'].',';
				$sql=$sql.'TAX2='.(intval($_POST['total'])+intval($_POST['charge'])).',';
			}
			else{
				$sql=$sql.'0,';
				$sql=$sql.'TAX2='.(intval($_POST['total'])).',';
			}
			if(isset($_POST['ininv'])){//2020/2/6優先判斷
				if($_POST['ininv']>0){
					$sql=$sql.'TAX5='.$_POST['ininv'].',';
				}
				else{
					$sql=$sql.'TAX5=0,';
				}
			}
			else if(isset($_POST['invshould'])){
				if($_POST['invshould']>0){
					$sql=$sql.'TAX5='.$_POST['invshould'].',';
				}
				else{
					//$sql=$sql.'TAX5=0,';
				}
			}
			else if(isset($_POST['invsalemoney'])&&$_POST['invsalemoney']>0){
				$sql=$sql.'TAX5=';
				$sql=$sql.$_POST['invsalemoney'].',';
			}
			else{
				//$sql=$sql.'0,';
			}
			$sql=$sql.'TAX6=';
			if(isset($_POST['person1'])&&$_POST['person1']>0){
				$sql=$sql.$_POST['person1'].',';
			}
			else{
				$sql=$sql.'0,';
				$_POST['person1']=0;
			}
			$sql=$sql.'TAX7=';
			if(isset($_POST['person2'])&&$_POST['person2']>0){
				$sql=$sql.$_POST['person2'].',';
			}
			else{
				$sql=$sql.'0,';
				$_POST['person2']=0;
			}
			$sql=$sql.'TAX8=';
			if(isset($_POST['person3'])&&$_POST['person3']>0){
				$sql=$sql.$_POST['person3'].',';
			}
			else{
				$sql=$sql.'0,';
				$_POST['person3']=0;
			}
			$sql=$sql.'TAX9=';
			if(isset($_POST['cashcomm'])&&$_POST['cashcomm']>0){
				$sql=$sql.$_POST['cashcomm'].',';
			}
			else{
				$sql=$sql.'0,';
			}
			if(isset($_POST['mancode'])&&$_POST['mancode']!=''&&isset($_POST['listtype'])&&($_POST['listtype']=='3'||$_POST['listtype']=='4')){
				$sql=$sql.'CUSTGPCODE="'.$_POST['mancode'].'",CUSTGPNAME="'.$_POST['manname'].'",';
			}
			else{
				$sql=$sql.'CUSTGPCODE=NULL,CUSTGPNAME=NULL,';
			}
			if(isset($_POST['memno'])&&$_POST['memno']!=''){
				$sql=$sql.'CUSTCODE="'.$memdata[0]['memno'].';-;'.$memdata[0]['tel'];
				if(isset($_POST['memaddno'])){
					$sql=$sql.';-;'.$_POST['memaddno'];
				}
				else{
				}
				$sql=$sql.'",CUSTNAME="'.$memdata[0]['name'].'",';
			}
			else{
				$sql=$sql.'CUSTCODE=NULL,CUSTNAME=NULL,';
			}

			if(isset($content['init']['linklist'])&&$content['init']['linklist']=='1'&&isset($_POST['linklist'])&&$_POST['listtype']=='2'){//開啟連接內用帳單
				$sql=$sql.'TABLENUMBER="'.$_POST['linklist'].'",';
			}
			else{
			}

			$sql=$sql.'SALESTTLQTY='.$totalqty.',SALESTTLAMT='.$_POST['total'];
			if(isset($_POST['salelisthint'])){
				$sql=$sql.',RELINVOICENUMBER="'.$_POST['salelisthint'].'"';
			}
			else{
			}
			if(isset($_POST['buyerdata'])&&$_POST['buyerdata']!=null&&$_POST['buyerdata']!=''){
				$sql=$sql.',RELINVOICETIME="'.$_POST['buyerdata'].'"';
			}
			else{
			}
			$sql=$sql.' WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'";';
			if($content['init']['controltable']==1){
				$tablist=preg_split('/,/',trim($_POST['tablenumber']));
				$datasql='SELECT ZCOUNTER,CREATEDATETIME FROM tempCST011 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'"';
				$zcounterdata=sqlquery($conn,$datasql,'sqlite');
				if(isset($zcounterdata)&&sizeof($zcounterdata)>0){
					foreach($tablist as $tl){
						if(file_exists('../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini')){//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$tabdata=parse_ini_file('../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini',true);//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$tabdata[$tl]['consecnumber']=$consecnumber;
							$tabdata[$tl]['saleamt']=$_POST['total'];
							$tabdata[$tl]['person']=($_POST['person1']+$_POST['person2']+$_POST['person3']);

							$tabdata[$tl]['state']="1";
							$tabdata[$tl]['machine']="";
							write_ini_file($tabdata,'../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini');//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						}
						else{
							$file='../../table/'.$bizdate.';'.$zcounterdata[0]['ZCOUNTER'].';'.$tl.'.ini';//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$f=fopen($file,'a');
							fwrite($f,'['.$tl.']'.PHP_EOL);
							fwrite($f,'bizdate="'.$bizdate.'"'.PHP_EOL);
							fwrite($f,'zcounter="'.$zcounterdata[0]['ZCOUNTER'].'"'.PHP_EOL);
							fwrite($f,'consecnumber="'.$consecnumber.'"'.PHP_EOL);
							fwrite($f,'saleamt="'.$_POST['total'].'"'.PHP_EOL);
							fwrite($f,'person="'.($_POST['person1']+$_POST['person2']+$_POST['person3']).'"'.PHP_EOL);
							fwrite($f,'createdatetime="'.$zcounterdata[0]['CREATEDATETIME'].'"'.PHP_EOL);
							fwrite($f,'table="'.$_POST['tablenumber'].'"'.PHP_EOL);
							if(strstr($_POST['tablenumber'],',')){
								fwrite($f,'tablestate="1"'.PHP_EOL);
							}
							else{
								fwrite($f,'tablestate="0"'.PHP_EOL);
							}
							fwrite($f,'state="1"'.PHP_EOL);
							fwrite($f,'machine=""'.PHP_EOL);
							fclose($f);
						}
					}
				}
				else{
				}
			}
			else{
			}
		}
	}
	else{

		$selectValue = isset($_POST['selectValue']) ? $_POST['selectValue'] : '';
	if($selectValue !=''){
		$salenomap='INSERT INTO salemap (bizdate,consecnumber,saleno) VALUES ("'.$selectValue.'","'.$consecnumber.'","'.$saleno.'")';
	}else{
		$salenomap='INSERT INTO salemap (bizdate,consecnumber,saleno) VALUES ("'.$bizdate.'","'.$consecnumber.'","'.$saleno.'")';
	}

		
		sqlnoresponse($conn,$salenomap,'sqlite');
		if(isset($_POST['listtotal'])){
			if(isset($_POST['memberdis'])&&$_POST['memberdis']>0){
				$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
				$values='("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
				if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
					$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
				}
				else{
					$values=$values.'"","",';
				}
				$values=$values.'"1","3","02","member","會員優惠","","","","",';
				for($j=0;$j<10;$j++){
					$values=$values.'NULL,';
				}
				if($content['init']['accuracytype']==1){//四捨五入
					$_POST['memberdis']=round($_POST['memberdis'],$content['init']['accuracy']);
				}
				else if($content['init']['accuracytype']==2){//無條件進位
					$_POST['memberdis']=ceil($_POST['memberdis'],$content['init']['accuracy']);
				}
				else{//無條件捨去
					$_POST['memberdis']=floor($_POST['memberdis'],$content['init']['accuracy']);
				}
				$values=$values.'0,0,0,-'.$_POST['memberdis'].',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
				$index++;
				$sql=$sql.$values.';';
				//sqlnoresponse($conn,$sql.$values,'sqlite');
			}
			else{
			}
			if($_POST['listdis1']>0||$_POST['listdis2']>0){
				$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
				$values='("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
				if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
					$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
				}
				else{
					$values=$values.'"","",';
				}
				$values=$values.'"1","3","02","list","帳單優惠","","","","",';
				for($j=0;$j<10;$j++){
					$values=$values.'NULL,';
				}
				$temp=floatval($_POST['listtotal'])-floatval($_POST['itemdis'])-floatval($_POST['autodis'])+floatval($_POST['charge'])+floatval($_POST['floorspan'])-floatval($_POST['should']);
				if(isset($_POST['memberdis'])&&$_POST['memberdis']>0){//2021/8/3 若不將會員折扣扣除，帳單優惠的金額會包含到該金額
					$temp=floatval($temp)-floatval($_POST['memberdis']);
				}
				else{
				}
				/*$temp=($_POST['listdis1']+$_POST['listdis2']);
				if($content['init']['accuracytype']==1){//四捨五入
					$temp=round($temp,$content['init']['accuracy']);
				}
				else if($content['init']['accuracytype']==1){//無條件進位
					$temp=ceil($temp,$content['init']['accuracy']);
				}
				else{//無條件捨去
					$temp=floor($temp,$content['init']['accuracy']);
				}*/
				$values=$values.'0,0,0,-'.$temp.',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
				$index++;
				$sql=$sql.$values.';';
				//echo $sql;
				//sqlnoresponse($conn,$sql.$values,'sqlite');
			}
			else{
			}
			if($_POST['autodis']>0){
				$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
				$values='("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
				if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
					$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
				}
				else{
					$values=$values.'"","",';
				}
				$values=$values.'"1","3","02","autodis","自動優惠","'.$_POST['autodiscontent'].'","'.$_POST['autodispremoney'].'","","",';
				for($j=0;$j<10;$j++){
					$values=$values.'NULL,';
				}
				if($content['init']['accuracytype']==1){//四捨五入
					$_POST['autodis']=round($_POST['autodis'],$content['init']['accuracy']);
				}
				else if($content['init']['accuracytype']==2){//無條件進位
					$_POST['autodis']=ceil($_POST['autodis'],$content['init']['accuracy']);
				}
				else{//無條件捨去
					$_POST['autodis']=floor($_POST['autodis'],$content['init']['accuracy']);
				}
				$values=$values.'0,0,0,-'.$_POST['autodis'].',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
				$index++;
				$sql=$sql.$values.';';
				//sqlnoresponse($conn,$sql.$values,'sqlite');
			}
			else{
			}
			/*if($_POST['charge']>0){
				$values='';
				if(isset($_POST['tablenumber'])&&strlen($_POST['tablenumber'])>0){
					$values='("'.trim($_POST['tablenumber']).'","';
				}
				else{
					$values='("'.$machinedata['basic']['terminalnumber'].'","';
				}
				$values=$values.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
				if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
					$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
				}
				else{
					$values=$values.'"","",';
				}
				$values=$values.'"1","3","02","charge","服務費","","","","",';
				for($j=0;$j<10;$j++){
					$values=$values.'NULL,';
				}
				$values=$values.'0,0,0,'.$_POST['charge'].',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
				$index++;
				sqlnoresponse($conn,$sql.$values,'sqlite');
			}
			else{
			}*/
			$sql=$sql.'INSERT INTO tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLQTY,SALESTTLAMT,TABLENUMBER,REMARKS,ZCOUNTER,CREATEDATETIME,TAX1,TAX2,TAX3,TAX4,TAX5,TAX6,TAX7,TAX8,TAX9,TA1,TA2,TA3,TA4,TA5,TA6,TA7,TA8,TA9,TA10,intella,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME,CREDITCARD';
			if(isset($otherpaydata[0])){
				for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
					$sql=$sql.','.$otherpaydata[$rowindex];
				}
			}
			else{
			}
			if(isset($_POST['salelisthint'])){
				$sql=$sql.',RELINVOICENUMBER';
			}
			else{
			}
			if(isset($_POST['buyerdata'])&&$_POST['buyerdata']!=null&&$_POST['buyerdata']!=''){
				$sql=$sql.',RELINVOICETIME';
			}
			else{
			}
			$sql=$sql.') VALUES ("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.$saleinvdata.'",';
			if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
				$sql=$sql.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
			}
			else{
				$sql=$sql.'"","",';
			}
			$sql=$sql.$totalqty.','.($_POST['should']-$_POST['charge']).',';
			
			if(isset($content['init']['linklist'])&&$content['init']['linklist']=='1'&&isset($_POST['linklist'])&&$_POST['listtype']=='2'){//開啟連接內用帳單
				$sql=$sql.'"'.$_POST['linklist'].'"';
			}
			else{
				$sql=$sql.'"'.trim($_POST['tablenumber']).'"';
			}

			$sql=$sql.',"'.$_POST['listtype'].'","'.$timeini['time']['zcounter'].'","'.$insertdate.'",';
			if(isset($_POST['charge'])&&$_POST['charge']>0){
				$sql=$sql.$_POST['charge'];
			}
			else{
				$sql=$sql.'0';
			}

			//2020/11/19 若沒有對應參數，給予預設金額
			if(isset($_POST['cashmoney'])){
			}
			else{
				$_POST['cashmoney']=0;
			}
			if(isset($_POST['cash'])){
			}
			else{
				$_POST['cash']=0;
			}
			if(isset($_POST['other'])){
			}
			else{
				$_POST['other']=0;
			}
			if(isset($_POST['otherfix'])){
			}
			else{
				$_POST['otherfix']=0;
			}
			if(($_POST['cashmoney']+$_POST['cash']+$_POST['other']+$_POST['otherfix'])==0){//2020/11/19 計算付款金額，若為零則將使用現金填入
				$sql=$sql.','.($_POST['should']-$_POST['charge']);
			}
			else if($_POST['cashmoney']>0){
				$sql=$sql.','.$_POST['cashmoney'];
			}
			else{
				$sql=$sql.',0';
			}
			if($_POST['cash']>0){
				$sql=$sql.','.$_POST['cash'];
			}
			else{
				$sql=$sql.',0';
			}
			if($_POST['other']>0&&isset($_POST['otherfix'])&&$_POST['otherfix']>0){
				$sql=$sql.','.($_POST['other']+$_POST['otherfix']);
			}
			else if(isset($_POST['other'])&&$_POST['other']>0){
				$sql=$sql.','.$_POST['other'];
			}
			else if(isset($_POST['otherfix'])&&$_POST['otherfix']>0){
				$sql=$sql.','.$_POST['otherfix'];
			}
			else{
				$sql=$sql.',0';
			}

			if(isset($_POST['ininv'])){//2020/2/6優先判斷
				if($_POST['ininv']>0){
					$sql=$sql.','.$_POST['ininv'];
				}
				else{
					$sql=$sql.',0';
				}
			}
			else if(isset($_POST['invshould'])){
				if($_POST['invshould']>0){
					$sql=$sql.','.$_POST['invshould'];
				}
				else{
					$sql=$sql.',0';
				}
			}
			else if(isset($_POST['invsalemoney'])&&$_POST['invsalemoney']>0){
				$sql=$sql.','.$_POST['invsalemoney'];
				//echo $_POST['invsalemoney'];
			}
			else{
				$sql=$sql.',0';
			}
			if(isset($_POST['person1'])&&$_POST['person1']>0){
				$sql=$sql.','.$_POST['person1'];
			}
			else{
				$sql=$sql.',0';
				$_POST['person1']=0;
			}
			if(isset($_POST['person2'])&&$_POST['person2']>0){
				$sql=$sql.','.$_POST['person2'];
			}
			else{
				$sql=$sql.',0';
				$_POST['person2']=0;
			}
			if(isset($_POST['person3'])&&$_POST['person3']>0){
				$sql=$sql.','.$_POST['person3'];
			}
			else{
				$sql=$sql.',0';
				$_POST['person3']=0;
			}
			if(isset($_POST['cashcomm'])&&$_POST['cashcomm']>0){
				$sql=$sql.','.$_POST['cashcomm'];
			}
			else{
				$sql=$sql.',0';
			}
			for($otindex=1;$otindex<=10;$otindex++){
				if(isset($otherpay['TA'.$otindex])){
					$sql=$sql.',"'.$otherpay['TA'.$otindex].'"';
				}
				else{
					$sql=$sql.',0';
				}
			}
			if(isset($otherpay['intellaother'])){
				$sql=$sql.',"'.$_POST['intellaconsecnumber'].':'.$otherpay['intellaother'].'"';
				$selectsql='PRAGMA table_info(tempCST011)';
				$column=sqlquery($conn,$selectsql,'sqlite');
				$columnname=array_column($column,'name');
				if(in_array('intella',$columnname)){
				}
				else{
					$insertsql='ALTER TABLE tempCST011 ADD COLUMN intella TEXT';
					sqlnoresponse($conn,$insertsql,'sqlite');
				}
				$selectsql='PRAGMA table_info(CST011)';
				$column=sqlquery($conn,$selectsql,'sqlite');
				$columnname=array_column($column,'name');
				if(in_array('intella',$columnname)){
				}
				else{
					$insertsql='ALTER TABLE CST011 ADD COLUMN intella TEXT';
					sqlnoresponse($conn,$insertsql,'sqlite');
				}
			}
			else{
				$sql=$sql.',0';
			}
			if(isset($_POST['mancode'])&&$_POST['mancode']!=''&&isset($_POST['listtype'])&&($_POST['listtype']=='3'||$_POST['listtype']=='4')){
				$sql=$sql.',"'.$_POST['mancode'].'","'.$_POST['manname'].'"';
			}
			else{
				$sql=$sql.',NULL,NULL';
			}
			if(isset($_POST['memno'])&&$_POST['memno']!=''){
				$sql=$sql.',"'.$memdata[0]['memno'].';-;'.$memdata[0]['tel'];
				if(isset($_POST['memaddno'])){
					$sql=$sql.';-;'.$_POST['memaddno'];
				}
				else{
				}
				$sql=$sql.'","'.$memdata[0]['name'].'"';
			}
			else{
				$sql=$sql.',NULL,NULL';
			}
			$sql=$sql.',';
			if(isset($_POST['creditcard'])&&$_POST['creditcard']!=''){
				$sql=$sql.'"'.$_POST['creditcard'].'"';
			}
			else{
				$sql=$sql.'NULL';
			}
			if(isset($otherpaydata[0])){
				for($rowindex=0;$rowindex<sizeof($otherpaydata);$rowindex++){
					if(isset($otherpay[$otherpaydata[$rowindex]])){
						$sql=$sql.',"'.$otherpay[$otherpaydata[$rowindex]]['value'].'"';
					}
					else{
						$sql=$sql.',0';
					}
				}
			}
			else{
			}
			if(isset($_POST['salelisthint'])){
				$sql=$sql.',"'.$_POST['salelisthint'].'"';
			}
			else{
			}
			if(isset($_POST['buyerdata'])&&$_POST['buyerdata']!=null&&$_POST['buyerdata']!=''){
				$sql=$sql.',"'.$_POST['buyerdata'].'"';
			}
			else{
			}
			$sql=$sql.');';
			//echo $sql;
			//echo ($_POST['listtotal']-$_POST['itemdis']-$_POST['memberdis']-$_POST['listdis1']-$_POST['listdis2']);
			if($content['init']['controltable']==1){
				if(strstr($_POST['listtype'],'-')){
					$tempremarks=preg_split('/-/',$_POST['listtype']);
					$tremarks='';
					for($i=0;$i<sizeof($tempremarks)-1;$i++){
						if($tremarks==''){
							$tremarks=$tempremarks[$i];
						}
						else{
							$tremarks=$tremarks.'-'.$tempremarks[$i];
						}
					}
				}
				else{
					$tremarks=$_POST['listtype'];
				}
				if($tremarks=='1'){
					$tablist=preg_split('/,/',trim($_POST['tablenumber']));
					foreach($tablist as $tl){
						if(file_exists('../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini')){//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							//unlink('../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini');
							$tabdata=parse_ini_file('../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini',true);//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$tabdata[$tl]['consecnumber']=$consecnumber;
							$tabdata[$tl]['saleamt']=($_POST['should']-$_POST['charge']);
							$tabdata[$tl]['person']=($_POST['person1']+$_POST['person2']+$_POST['person3']);

							$tabdata[$tl]['state']="1";
							$tabdata[$tl]['machine']="";
							write_ini_file($tabdata,'../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini');//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						}
						else{
							$file='../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini';//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$f=fopen($file,'a');
							fwrite($f,'['.$tl.']'.PHP_EOL);
							fwrite($f,'bizdate="'.$bizdate.'"'.PHP_EOL);
							fwrite($f,'zcounter="'.$timeini['time']['zcounter'].'"'.PHP_EOL);
							fwrite($f,'consecnumber="'.$consecnumber.'"'.PHP_EOL);
							fwrite($f,'saleamt="'.($_POST['should']-$_POST['charge']).'"'.PHP_EOL);
							fwrite($f,'person="'.($_POST['person1']+$_POST['person2']+$_POST['person3']).'"'.PHP_EOL);
							fwrite($f,'createdatetime="'.$insertdate.'"'.PHP_EOL);
							fwrite($f,'table="'.$_POST['tablenumber'].'"'.PHP_EOL);
							if(strstr($_POST['tablenumber'],',')){
								fwrite($f,'tablestate="1"'.PHP_EOL);
							}
							else{
								fwrite($f,'tablestate="0"'.PHP_EOL);
							}
							fwrite($f,'state="1"'.PHP_EOL);
							fwrite($f,'machine=""'.PHP_EOL);
							fclose($f);
						}
						/*$fileini=fopen('../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$tl).'.ini','a');
						fwrite($fileini,'['.$tl.']'.PHP_EOL);
						fwrite($fileini,'bizdate="'.$bizdate.'"'.PHP_EOL);
						fwrite($fileini,'zcounter="'.$timeini['time']['zcounter'].'"'.PHP_EOL);
						fwrite($fileini,'consecnumber="'.$consecnumber.'"'.PHP_EOL);
						fwrite($fileini,'saleamt="'.($_POST['should']-$_POST['charge']).'"'.PHP_EOL);
						fwrite($fileini,'person="'.($_POST['person1']+$_POST['person2']+$_POST['person3']).'"'.PHP_EOL);
						fwrite($fileini,'createdatetime="'.$insertdate.'"'.PHP_EOL);
						fwrite($fileini,'table="'.$_POST['tablenumber'].'"'.PHP_EOL);
						if(strstr($_POST['tablenumber'],',')){
							fwrite($fileini,'tablestate="1"'.PHP_EOL);
						}
						else{
							fwrite($fileini,'tablestate="0"'.PHP_EOL);
						}
						fwrite($fileini,'state="1"'.PHP_EOL);
						fwrite($fileini,'machine=""'.PHP_EOL);
						fclose($fileini);*/
					}
				}
				else if($tremarks=='2'){
					$fileini=fopen('../../table/outside/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$consecnumber.'.ini','a');
					fclose($fileini);
				}
				else{
				}
			}
			else{
			}
		}
		else{
			if(isset($_POST['autodis'])&&$_POST['autodis']>0){
				$sqlselect='SELECT COUNT(*) AS num FROM tempCST012 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis"';
				$tesel=sqlquery($conn,$sqlselect,'sqlite');
				if(sizeof($tesel)>0&&isset($tesel[0]['num'])&&intval($tesel[0]['num'])>0){
					//echo 'ABCDE';
					$sql=$sql."UPDATE tempCST012 SET AMT=";
					
					if($content['init']['accuracytype']==1){//四捨五入
						$_POST['autodis']=round($_POST['autodis'],$content['init']['accuracy']);
					}
					else if($content['init']['accuracytype']==2){//無條件進位
						$_POST['autodis']=ceil($_POST['autodis'],$content['init']['accuracy']);
					}
					else{//無條件捨去
						$_POST['autodis']=floor($_POST['autodis'],$content['init']['accuracy']);
					}
					$values='-'.$_POST['autodis'].' WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND ITEMCODE="autodis"';
				}
				else{
					$sql=$sql."INSERT INTO tempCST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,ITEMNAME,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,SELECTIVEITEM2,SELECTIVEITEM3,SELECTIVEITEM4,SELECTIVEITEM5,SELECTIVEITEM6,SELECTIVEITEM7,SELECTIVEITEM8,SELECTIVEITEM9,SELECTIVEITEM10,WEIGHT,QTY,UNITPRICE,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ";
					$values='("'.$_POST['machinetype'].'","'.$bizdate.'","'.$consecnumber.'","'.str_pad($index,3,'0',STR_PAD_LEFT).'",';
					if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
						$values=$values.'"'.$_POST['usercode'].'","'.$_POST['username'].'",';
					}
					else{
						$values=$values.'"","",';
					}
					$values=$values.'"1","3","02","autodis","自動優惠","'.$_POST['autodiscontent'].'","'.$_POST['autodispremoney'].'","","",';
					for($j=0;$j<10;$j++){
						$values=$values.'NULL,';
					}
					if($content['init']['accuracytype']==1){//四捨五入
						$_POST['autodis']=round($_POST['autodis'],$content['init']['accuracy']);
					}
					else if($content['init']['accuracytype']==2){//無條件進位
						$_POST['autodis']=ceil($_POST['autodis'],$content['init']['accuracy']);
					}
					else{//無條件捨去
						$_POST['autodis']=floor($_POST['autodis'],$content['init']['accuracy']);
					}
					$values=$values.'0,0,0,-'.$_POST['autodis'].',"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.$insertdate.'")';
				}
				$index++;
				$sql=$sql.$values.';';
				//sqlnoresponse($conn,$sql.$values,'sqlite');
			}
			else{
			}
			$sql=$sql.'INSERT INTO tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLQTY,SALESTTLAMT,TABLENUMBER,REMARKS,ZCOUNTER,CREATEDATETIME,TAX1,TAX2,TAX5,TAX6,TAX7,TAX8,TAX9,CUSTGPCODE,CUSTGPNAME,CUSTCODE,CUSTNAME';
			if(isset($_POST['salelisthint'])){
				$sql=$sql.',RELINVOICENUMBER';
			}
			else{
			}
			if(isset($_POST['buyerdata'])&&$_POST['buyerdata']!=null&&$_POST['buyerdata']!=''){
				$sql=$sql.',RELINVOICETIME';
			}
			else{
			}
			$selectValue = isset($_POST['selectValue']) ? $_POST['selectValue'] : '';

			// 根據 selectValue 是否為空值來選擇使用 $bizdate 或 $selectValue
			$sql = $sql . ') VALUES ("'.$_POST['machinetype'].'",';

			if (!empty($selectValue)) {
				$sql = $sql . '"'.$selectValue.'",';
			} else {
				$sql = $sql . '"'.$bizdate.'",';
			}

			// 繼續添加其他值
			$sql = $sql . '"'.$consecnumber.'","'.$saleinvdata.'",';

			// 檢查並添加額外的參數
			if (isset($_POST['usercode']) && strlen($_POST['usercode'])) {
				$sql = $sql . '"' . $_POST['usercode'] . '","' . $_POST['username'] . '",';
			}
			else{
				$sql=$sql.'"","",';
			}
			$sql=$sql.$totalqty.','.$_POST['total'].',';

			if(isset($content['init']['linklist'])&&$content['init']['linklist']=='1'&&isset($_POST['linklist'])&&$_POST['listtype']=='2'){//開啟連接內用帳單
				$sql=$sql.'"'.$_POST['linklist'].'"';
			}
			else{
				$sql=$sql.'"'.trim($_POST['tablenumber']).'"';
			}

			$sql=$sql.',"'.$_POST['listtype'].'","'.$timeini['time']['zcounter'].'","'.$insertdate.'",';
			if(isset($_POST['charge'])&&$_POST['charge']>0){
				$sql=$sql.$_POST['charge'];
				$sql=$sql.','.(intval($_POST['total'])+intval($_POST['charge']));
			}
			else{
				$sql=$sql.'0';
				$sql=$sql.','.(intval($_POST['total']));
			}
			if(isset($_POST['ininv'])){//2020/2/6優先判斷
				if($_POST['ininv']>0){
					$sql=$sql.','.$_POST['ininv'];
				}
				else{
					$sql=$sql.',0';
				}
			}
			else if(isset($_POST['invshould'])){
				if($_POST['invshould']>0){
					$sql=$sql.','.$_POST['invshould'];
				}
				else{
					$sql=$sql.',0';
				}
			}
			else if(isset($_POST['invsalemoney'])&&$_POST['invsalemoney']>0){
				$sql=$sql.','.$_POST['invsalemoney'];
				//echo $_POST['invsalemoney'];
			}
			else{
				$sql=$sql.',0';
			}
			if(isset($_POST['person1'])&&$_POST['person1']>0){
				$sql=$sql.','.$_POST['person1'];
			}
			else{
				$sql=$sql.',0';
				$_POST['person1']=0;
			}
			if(isset($_POST['person2'])&&$_POST['person2']>0){
				$sql=$sql.','.$_POST['person2'];
			}
			else{
				$sql=$sql.',0';
				$_POST['person2']=0;
			}
			if(isset($_POST['person3'])&&$_POST['person3']>0){
				$sql=$sql.','.$_POST['person3'];
			}
			else{
				$sql=$sql.',0';
				$_POST['person3']=0;
			}
			if(isset($_POST['cashcomm'])&&$_POST['cashcomm']>0){
				$sql=$sql.','.$_POST['cashcomm'];
			}
			else{
				$sql=$sql.',0';
			}
			if(isset($_POST['mancode'])&&$_POST['mancode']!=''&&isset($_POST['listtype'])&&($_POST['listtype']=='3'||$_POST['listtype']=='4')){
				$sql=$sql.',"'.$_POST['mancode'].'","'.$_POST['manname'].'"';
			}
			else{
				$sql=$sql.',NULL,NULL';
			}
			if(isset($_POST['memno'])&&$_POST['memno']!=''){
				$sql=$sql.',"'.$memdata[0]['memno'].';-;'.$memdata[0]['tel'];
				if(isset($_POST['memaddno'])){
					$sql=$sql.';-;'.$_POST['memaddno'];
				}
				else{
				}
				$sql=$sql.'","'.$memdata[0]['name'].'"';
			}
			else{
				$sql=$sql.',NULL,NULL';
			}
			if(isset($_POST['salelisthint'])){
				$sql=$sql.',"'.$_POST['salelisthint'].'"';
			}
			else{
			}
			if(isset($_POST['buyerdata'])&&$_POST['buyerdata']!=null&&$_POST['buyerdata']!=''){
				$sql=$sql.',"'.$_POST['buyerdata'].'"';
			}
			else{
			}
			$sql=$sql.');';
			if($content['init']['controltable']==1){
				if(strstr($_POST['listtype'],'-')){
					$tempremarks=preg_split('/-/',$_POST['listtype']);
					$tremarks='';
					for($i=0;$i<sizeof($tempremarks)-1;$i++){
						if($tremarks==''){
							$tremarks=$tempremarks[$i];
						}
						else{
							$tremarks=$tremarks.'-'.$tempremarks[$i];
						}
					}
				}
				else{
					$tremarks=$_POST['listtype'];
				}
				if($tremarks=='1'){
					$tablist=preg_split('/,/',trim($_POST['tablenumber']));
					foreach($tablist as $tl){
						if(file_exists('../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini')){//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							//unlink('../../table/'.$_POST['basic']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$tl).'.ini');
							$tabdata=parse_ini_file('../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini',true);//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$tabdata[$tl]['consecnumber']=$consecnumber;
							$tabdata[$tl]['saleamt']=$_POST['total'];
							$tabdata[$tl]['person']=($_POST['person1']+$_POST['person2']+$_POST['person3']);

							$tabdata[$tl]['state']="1";
							$tabdata[$tl]['machine']="";
							write_ini_file($tabdata,'../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini');//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
						}
						else{
							$file='../../table/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$tl.'.ini';//2020/3/25 iconv('utf-8','big5',$tl) >> $tl
							$f=fopen($file,'a');
							fwrite($f,'['.$tl.']'.PHP_EOL);
							fwrite($f,'bizdate="'.$bizdate.'"'.PHP_EOL);
							fwrite($f,'zcounter="'.$timeini['time']['zcounter'].'"'.PHP_EOL);
							fwrite($f,'consecnumber="'.$consecnumber.'"'.PHP_EOL);
							fwrite($f,'saleamt="'.$_POST['total'].'"'.PHP_EOL);
							fwrite($f,'person="'.($_POST['person1']+$_POST['person2']+$_POST['person3']).'"'.PHP_EOL);
							fwrite($f,'createdatetime="'.$insertdate.'"'.PHP_EOL);
							fwrite($f,'table="'.$_POST['tablenumber'].'"'.PHP_EOL);
							if(strstr($_POST['tablenumber'],',')){
								fwrite($f,'tablestate="1"'.PHP_EOL);
							}
							else{
								fwrite($f,'tablestate="0"'.PHP_EOL);
							}
							fwrite($f,'state="1"'.PHP_EOL);
							fwrite($f,'machine=""'.PHP_EOL);
							fclose($f);
						}
						/*$fileini=fopen("../../table/".$bizdate.";".$timeini['time']['zcounter'].";".iconv('utf-8','big5',$tl).".ini",'a');
						fwrite($fileini,'['.$tl.']'.PHP_EOL);
						fwrite($fileini,'bizdate="'.$bizdate.'"'.PHP_EOL);
						fwrite($fileini,'zcounter="'.$timeini['time']['zcounter'].'"'.PHP_EOL);
						fwrite($fileini,'consecnumber="'.$consecnumber.'"'.PHP_EOL);
						fwrite($fileini,'saleamt="'.$_POST['total'].'"'.PHP_EOL);
						fwrite($fileini,'person="'.($_POST['person1']+$_POST['person2']+$_POST['person3']).'"'.PHP_EOL);
						fwrite($fileini,'createdatetime="'.$insertdate.'"'.PHP_EOL);
						fwrite($fileini,'table="'.$_POST['tablenumber'].'"'.PHP_EOL);
						if(strstr($_POST['tablenumber'],',')){
							fwrite($fileini,'tablestate="1"'.PHP_EOL);
						}
						else{
							fwrite($fileini,'tablestate="0"'.PHP_EOL);
						}
						fwrite($fileini,'state="1"'.PHP_EOL);
						fwrite($fileini,'machine=""'.PHP_EOL);
						fclose($fileini);*/
					}
				}
				else if($tremarks=='2'){
					$fileini=fopen('../../table/outside/'.$bizdate.';'.$timeini['time']['zcounter'].';'.$consecnumber.'.ini','a');
					fclose($fileini);
				}
				else{
				}
			}
			else{
			}
		}
	}
	if(isset($_POST['listtype'])){
		$sql=$sql.'UPDATE tempCST011 SET REMARKS="'.$_POST['listtype'].'" WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'";';
		$sql=$sql.'UPDATE tempCST012 SET REMARKS="'.$_POST['listtype'].'" WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$consecnumber.'";';
	}
	else{
	}
}
if(isset($posdvr)){
	$tempdvrcontent = "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL.$tempdvrcontent;
	$tempdvrcontent = "名稱   單價   小計".PHP_EOL.$tempdvrcontent;
	$tempdvrcontent = "!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61!61".PHP_EOL.$tempdvrcontent;
	if(isset($_POST['usercode'])&&strlen($_POST['usercode'])){
		$tempdvrcontent = "服務員!58".$_POST['username'].PHP_EOL.$tempdvrcontent;
	}
	else{
		$tempdvrcontent = "服務員!58".PHP_EOL.$tempdvrcontent;
	}
	$tempdvrcontent = "時間!58".substr($tempposdvr,0,4).'!47'.substr($tempposdvr,4,2).'!47'.substr($tempposdvr,6,2).' '.substr($tempposdvr,8,2).'!58'.substr($tempposdvr,10,2).'!58'.substr($tempposdvr,12,2).PHP_EOL.$tempdvrcontent;
	$tempdvrcontent = "機!58".$_POST['machinetype'].PHP_EOL.$tempdvrcontent;
	$tempdvrcontent = "編號!58".$consecnumber.PHP_EOL.$tempdvrcontent;
	if(isset($_POST['should'])){
		$tempdvrcontent = "cashdrawer".PHP_EOL."桌號!58".trim($_POST['tablenumber']).PHP_EOL.$tempdvrcontent;
		//$tempdvrcontent .= "小 計  ".preg_replace('/[.]/','!46',($_POST['should']-$_POST['charge'])).PHP_EOL;
		if(isset($_POST['memberdis'])&&isset($_POST['listdis1'])&&isset($_POST['listdis2'])&&($_POST['memberdis']+$_POST['listdis1']+$_POST['listdis2'])>0){
			$tempdvrcontent .= "優惠折抵  !45".preg_replace("/[.]/","!46",($_POST['memberdis']+$_POST['listdis1']+$_POST['listdis2'])).PHP_EOL;
		}
		else{
		}
		$autodiscontent=preg_split('/,/',$_POST['autodiscontent']);
		$autodispremoney=preg_split('/,/',$_POST['autodispremoney']);
		for($di=0;$di<sizeof($autodiscontent);$di++){
			if(isset($discount[$autodiscontent[$di]])){
				$tempdvrcontent .= $discount[$autodiscontent[$di]]['name']."  !45".$autodispremoney[$di].PHP_EOL;
			}
			else{
			}
		}
		
		$tempdvrcontent .= "應收金額  ";
		if(isset($_POST['listtotal'])){
			$tempdvrcontent .= $_POST['should'].PHP_EOL;
		}
		else{
			if(isset($_POST['floorspan'])){
				if(isset($_POST['autodis'])){
					$tempdvrcontent .= ($_POST['total']+$_POST['floorspan']+$_POST['charge']-$_POST['autodis']).PHP_EOL;
				}
				else{
					$tempdvrcontent .= ($_POST['total']+$_POST['floorspan']+$_POST['charge']).PHP_EOL;
				}
			}
			else{
				if(isset($_POST['autodis'])){
					$tempdvrcontent .= ($_POST['total']+$_POST['charge']-$_POST['autodis']).PHP_EOL;
				}
				else{
					$tempdvrcontent .= ($_POST['total']+$_POST['charge']).PHP_EOL;
				}
			}
		}
	}
	else{
		$tempdvrcontent = "temp".PHP_EOL."桌號!58".trim($_POST['tablenumber']).PHP_EOL.$tempdvrcontent;
		$tempdvrcontent .= "小 計  ".preg_replace('/[.]/','!46',($_POST['total'])).PHP_EOL;
	}
	fwrite($posdvr,$tempdvrcontent);
	fclose($posdvr);
}
else{
}
if(isset($kvm)){
	$tempkvmcontent = '編號:'.$saleno.'"'.$tempkvmcontent;
	if($_POST['listtype']=='1'&&$_POST['tablenumber']!=''){
		$tempkvmcontent = '桌號:'.trim($_POST['tablenumber']).' '.$tempkvmcontent;
	}
	else{
	}
	$tempkvmcontent = $buttons['name']['listtype'.$_POST['listtype']].' '.$tempkvmcontent;
	$tempkvmcontent = 'title="'.$tempkvmcontent;

	$Nowtimee = 'time="'.$bizdate.'"';
	$Selecttimee = 'stime="'.$selectValue.'"';
	$Billn = 'bill="'.$consecnumber.'"';
	$Namee = 'Namee="'.$memdata[0]['name'].'"';
	$CarNamee = 'CarNamee="'.$memdata[0]['tel'].'"';

	fwrite($kvm, '[list]' . PHP_EOL . $tempkvmcontent . PHP_EOL .'[NowTime]'. PHP_EOL . $Nowtimee. PHP_EOL .'[Selecttime]'. PHP_EOL .$Selecttimee. PHP_EOL .'[BillNumber]'. PHP_EOL .$Billn. PHP_EOL .'[Name]'. PHP_EOL .$Namee
	. PHP_EOL .'[CarNumber]'. PHP_EOL .$CarNamee		);

	
	fclose($kvm);
}
else{
}
date_default_timezone_set($content['init']['settime']);
fwrite($handle,date('Y/m/d H:i:s').' -- '.$sql.PHP_EOL);
fclose($handle);
sqlnoresponse($conn,$sql,'sqliteexec');
sqlclose($conn,'sqlite');
//echo $sql;
//$f=fopen('../../../print/noread/report.txt','w');
//fclose($f);
//echo ' database is locked '
if(file_exists('../../../print/stop.ini')){
	unlink('../../../print/stop.ini');
}
else{
}
?>