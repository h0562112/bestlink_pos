<?php
include_once '../../../tool/myerrorlog.php';
include '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$set=parse_ini_file('../../../database/setup.ini',true);
$menu=parse_ini_file('../../../database/'.$set['basic']['company'].'-menu.ini',true);
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$list=sqlquery($conn,$sql,'sqlite');
$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER ASC';
$listitem=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machine']])){
		$invmachine=$dbmapping['map'][$_POST['machine']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
$year=intval(substr($list[0]['CREATEDATETIME'],0,4))-1911;
$month=substr($list[0]['CREATEDATETIME'],4,2);
if(intval($month)%2==0){
	$m=$month;
}
else{
	$m=intval($month)+1;
}
if(strlen($m)<2){
	$m='0'.$m;
}
if(strlen(trim($list[0]['INVOICENUMBER']))==0){
}
else{
	$conn=sqlconnect('../../../database/sale/'.substr($list[0]['CREATEDATETIME'],0,4).$m,'invdata_'.substr($list[0]['CREATEDATETIME'],0,4).$m.'_'.$invmachine.'.db','','','','sqlite');
	$sql='SELECT buyerid,donatemark,carriertype,carrierid1,carrierid2,npoban FROM invlist WHERE invnumber="'.$list[0]['INVOICENUMBER'].'"';
	$bandata=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}

$data=array();
if(isset($bandata)&&sizeof($bandata)){
	$data['buyerid']=$bandata[0]['buyerid'];
	$data['donatemark']=$bandata[0]['donatemark'];
	$data['carriertype']=$bandata[0]['carriertype'];
	$data['carrierid1']=$bandata[0]['carrierid1'];
	$data['carrierid2']=$bandata[0]['carrierid2'];
	$data['npoban']=$bandata[0]['npoban'];
}
else{
}

$paycode='EX10';
if($list[0]['CLKNAME']=='FoodPanda'&&$list[0]['TAX4']!='0'){//2022/2/15 foodpanda���x�걵����l�äw�I��
	//2022/5/19 �Τ@�s���|�s�b�b��Ƶ����A�ݭn�r��ާ@
	if(preg_match('/(�Τ@�s��: )/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 �ˬd�O�_�ݭn�Τ@�s��
		$container=preg_split('/(�Τ@�s��: )/',$list[0]['RELINVOICENUMBER']);
		$data['ban']=substr($container[1],0,8);
		$data['container']='';//2022/5/19 Foodpanda�S�����ȤH��J����
	}
	else if(preg_match('/(�Τ@�s��:)/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 �ˬd�O�_�ݭn�Τ@�s���F�֤@�I���U�Ӫ�Foodpanda�Ƶ����A�|�N�ťծ���(�����D���̬�����n�ܰʮ榡)
		$container=preg_split('/(�Τ@�s��:)/',$list[0]['RELINVOICENUMBER']);
		$data['ban']=substr($container[1],0,8);
		$data['container']='';//2022/5/19 Foodpanda�S�����ȤH��J����
	}
	else{
	}
	if($list[0]['EX10']!='0'){//2022/2/15 �Y��l�Y�U�Ӫ��ɭԨS���]�wfoodpanda���I�ڤ覡�A�|�s�bEX10����줤
		$data['payment']['foodpanda']['money']=$list[0]['EX10'];
	}
	else{
		for($i=1;$i<sizeof($otherpay);$i++){
			if(strtolower($otherpay['item'.$i]['name'])=='foodpanda'){
				$paycode=$otherpay['item'.$i]['dbname'];
				break;
			}
			else{
			}
		}
		$data['payment']['foodpanda']['method'][]=$paycode;
		$data['payment']['foodpanda']['money'][]=$list[0][$paycode];
	}
}
else if($list[0]['CLKNAME']=='FoodPanda'){//2022/5/19 ���I�ڪ���l���n�ˬd�Τ@�s��
	//2022/5/19 �Τ@�s���|�s�b�b��Ƶ����A�ݭn�r��ާ@
	if(preg_match('/(�Τ@�s��: )/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 �ˬd�O�_�ݭn�Τ@�s��
		$container=preg_split('/(�Τ@�s��: )/',$list[0]['RELINVOICENUMBER']);
		$data['ban']=substr($container[1],0,8);
		$data['container']='';//2022/5/19 Foodpanda�S�����ȤH��J����
	}
	else if(preg_match('/(�Τ@�s��:)/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 �ˬd�O�_�ݭn�Τ@�s���F�֤@�I���U�Ӫ�Foodpanda�Ƶ����A�|�N�ťծ���(�����D���̬�����n�ܰʮ榡)
		$container=preg_split('/(�Τ@�s��:)/',$list[0]['RELINVOICENUMBER']);
		$data['ban']=substr($container[1],0,8);
		$data['container']='';//2022/5/19 Foodpanda�S�����ȤH��J����
	}
	else{
	}
}
else{
}
if($list[0]['CLKNAME']=='UberEats'&&$list[0]['TAX4']!='0'){//2022/10/3 ubereats���x�걵����l�äw�I��
	if($list[0]['EX10']!='0'){//2022/2/15 �Y��l�Y�U�Ӫ��ɭԨS���]�wubereats���I�ڤ覡�A�|�s�bEX10����줤
		$data['payment']['ubereats']['money']=$list[0]['EX10'];
	}
	else{
		for($i=1;$i<sizeof($otherpay);$i++){
			if(strtolower($otherpay['item'.$i]['name'])=='ubereats'){
				$paycode=$otherpay['item'.$i]['dbname'];
				break;
			}
			else{
			}
		}
		$data['payment']['ubereats']['method'][]=$paycode;
		$data['payment']['ubereats']['money'][]=$list[0][$paycode];
	}
}
else{
}
if($list[0]['CLKNAME']=='QuickClick'&&$list[0]['TAX4']!='0'){//2022/2/15 quickclick���x�걵����l�äw�I��
	if($list[0]['EX10']!='0'){//2022/2/15 �Y��l�Y�U�Ӫ��ɭԨS���]�wquickclick���I�ڤ覡�A�|�s�bEX10����줤
		$data['payment']['quickclick']['money']=$list[0]['EX10'];
	}
	else{
		if(substr($list[0]['CLKCODE'],0,2)=='FP'){//2022/10/3 quickclick���ꪺfoodpanda�q��
			for($i=1;$i<sizeof($otherpay);$i++){
				if(strtolower($otherpay['item'.$i]['name'])=='foodpanda'){
					$paycode=$otherpay['item'.$i]['dbname'];
					break;
				}
				else{
				}
			}
			$data['payment']['foodpanda']['method'][]=$paycode;
			$data['payment']['foodpanda']['money'][]=$list[0][$paycode];
		}
		else if(substr($list[0]['CLKCODE'],0,2)=='UE'){//2022/10/3 quickclick���ꪺubereats�q��
			for($i=1;$i<sizeof($otherpay);$i++){
				if(strtolower($otherpay['item'.$i]['name'])=='ubereats'){
					$paycode=$otherpay['item'.$i]['dbname'];
					break;
				}
				else{
				}
			}
			$data['payment']['ubereats']['method'][]=$paycode;
			$data['payment']['ubereats']['money'][]=$list[0][$paycode];
		}
		else{//2022/10/3 quickclick�q��
			for($i=1;$i<sizeof($otherpay);$i++){
				if(strtolower($otherpay['item'.$i]['name'])=='quickclick'){
					$paycode=$otherpay['item'.$i]['dbname'];
					break;
				}
				else{
				}
			}
			$data['payment']['quickclick']['method'][]=$paycode;
			$data['payment']['quickclick']['money'][]=$list[0][$paycode];
		}
	}
}
else{
}
if(isset($list[0]['nidin'])){//2021/3/10 nidin�I�ڤ覡
	$nidinarray=preg_split('/:/',$list[0]['nidin']);
	$nidinpaytype=preg_split('/-/',$nidinarray[0]);
	//2021/11/8 �]������r���]�t�F - �A�]���b���ήɤ]�|�P�ɳQ����A�ݭn�t�~�ɦ^��
	if(sizeof($nidinpaytype)>3){
		for($i=2;$i<(sizeof($nidinpaytype)-1);$i++){
			$nidinpaytype[1].='-'.$nidinpaytype[$i];
		}
		$nidinpaytype[2]=$nidinpaytype[(sizeof($nidinpaytype)-1)];
	}
	else{
	}
	$data['ban']=$nidinpaytype[0];
	$data['container']=$nidinpaytype[1];
	if($nidinpaytype[sizeof($nidinpaytype)-1]=='50'){//2021/3/10 50:POS�I�ڡA�����u���F��x���R����ϥ�
	}
	else{//10:nidin�u�W�I��
		for($i=1;$i<sizeof($nidinarray);$i=$i+2){
			$data['payment']['nidin']['method'][]=$nidinarray[$i];
			$data['payment']['nidin']['money'][]=$nidinarray[$i+1];
		}
	}
}
else{
}
if(isset($list[0]['intella'])){//2021/9/13 intella�I�ڤ覡
	$intellaarray=preg_split('/:/',$list[0]['intella']);
	//$intellapaytype=preg_split('/-/',$intellaarray[0]);
	//$data['ban']=$nidinpaytype[0];
	//$data['container']=$nidinpaytype[1];
	/*if($nidinpaytype[sizeof($nidinpaytype)-1]=='50'){//2021/9/13 50:POS�I�ڡA�����u���F��x���R����ϥ�
	}
	else{//10:nidin�u�W�I��*/
		for($i=1;$i<sizeof($intellaarray);$i=$i+2){
			$data['payment']['intella']['method'][]=$intellaarray[$i];
			$data['payment']['intella']['money'][]=$intellaarray[$i+1];
		}
	//}
}
else{
}

//2020/2/5
$ininv=0;
$freeinv=0;
for($i=0;$i<sizeof($listitem);$i++){
	if($listitem[$i]['ITEMCODE']=='list'){//�b��馩/��
		if(isset($data['dislist'])){
			$data['dislist']=floatval($data['dislist'])+floatval($listitem[$i]['AMT']);
		}
		else{
			$data['dislist']=$listitem[$i]['AMT'];
		}
	}
	else if($listitem[$i]['ITEMCODE']=='item'){//��~�馩/��
		if(isset($data['disitem'])){
			$data['disitem']=floatval($data['disitem'])+floatval($listitem[$i]['AMT']);
		}
		else{
			$data['disitem']=$listitem[$i]['AMT'];
		}
		if(isset($menu[intval($listitem[$i-1]['ITEMCODE'])]['charge'])&&$menu[intval($listitem[$i-1]['ITEMCODE'])]['charge']=='0'){
		}
		else{
			if(isset($data['tempdis'])){
				$data['tempdis']=floatval($data['tempdis'])+floatval($listitem[$i-1]['AMT']);
			}
			else{
				$data['tempdis']=$listitem[$i-1]['AMT'];
			}
		}
		
		//2020/2/5
		if(isset($menu[intval($listitem[$i-1]['ITEMCODE'])]['insaleinv'])&&$menu[intval($listitem[$i-1]['ITEMCODE'])]['insaleinv']=='0'){//�K�|
			$freeinv=intval($freeinv)+intval($listitem[$i]['AMT']);
		}
		else{//���|
			$ininv=intval($ininv)+intval($listitem[$i]['AMT']);
		}
	}
	else if(strlen($listitem[$i]['ITEMCODE'])==16){//���O���B
		if(isset($data['item'])){
			$data['item']=floatval($data['item'])+floatval($listitem[$i]['AMT']);
		}
		else{
			$data['item']=$listitem[$i]['AMT'];
		}
		if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['charge'])&&$menu[intval($listitem[$i]['ITEMCODE'])]['charge']=='0'){
		}
		else{
			if(isset($data['temptotal'])){
				$data['temptotal']=floatval($data['temptotal'])+floatval($listitem[$i]['AMT']);
			}
			else{
				$data['temptotal']=$listitem[$i]['AMT'];
			}
		}

		//2020/2/5
		if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['insaleinv'])&&$menu[intval($listitem[$i]['ITEMCODE'])]['insaleinv']=='0'){//�K�|
			$freeinv=intval($freeinv)+intval($listitem[$i]['AMT']);
		}
		else{//���|
			$ininv=intval($ininv)+intval($listitem[$i]['AMT']);
		}
	}
	else if($listitem[$i]['ITEMCODE']=='member'){//�|���u�f
		if(isset($data['dismember'])){
			$data['dismember']=floatval($data['dismember'])+floatval($listitem[$i]['AMT']);
		}
		else{
			$data['dismember']=$listitem[$i]['AMT'];
		}
	}
	else{//if($li['ITEMCODE']=='autodis')//�t���u�f
		if(isset($data['autodis'])){
			$data['autodis']=floatval($data['autodis'])+floatval($listitem[$i]['AMT']);//�u�f���B
			$data['autodiscontent']=$data['autodiscontent'].','.$listitem[$i]['ITEMGRPCODE'];//�ϥ��u�f���
			$data['autodispremoney']=$data['autodispremoney'].','.$listitem[$i]['ITEMGRPNAME'];//�C���u�f��פ��O�u�f���B
		}
		else{
			$data['autodis']=$listitem[$i]['AMT'];
			$data['autodiscontent']=$listitem[$i]['ITEMGRPCODE'];
			$data['autodispremoney']=$listitem[$i]['ITEMGRPNAME'];
		}
	}
}

//2020/2/5
$dismoney=0;
if(isset($data['dislist'])){
	$dismoney=intval($dismoney)+intval($data['dislist']);
}
else{
}
if(isset($data['dismember'])){
	$dismoney=intval($dismoney)+intval($data['dismember']);
}
else{
}
if(isset($data['autodis'])){
	$dismoney=intval($dismoney)+intval($data['autodis']);
}
else{
}
$freeinv=intval($freeinv)+intval($dismoney);
if(intval($freeinv)<0){
	$ininv=intval($ininv)+intval($freeinv);
	
	$freeinv=0;
}
else{
}

$data['charge']=$list[0]['TAX1'];
$data['should']=floatval($list[0]['SALESTTLAMT'])+floatval($list[0]['TAX1']);
$data['ininv']=intval($ininv)+intval($list[0]['TAX1']);
$data['freeinv']=$freeinv;
echo json_encode($data);
?>