<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$set=parse_ini_file('../../../database/setup.ini',true);
$menu=parse_ini_file('../../../database/'.$set['basic']['company'].'-menu.ini',true);
$otherpay=parse_ini_file('../../../database/otherpay.ini',true);
/*$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata ORDER BY fronttype ASC,inumber ASC';
$items=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');*/
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER ASC';
$listitem=sqlquery($conn,$sql,'sqlite');
$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$list=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$tt=array();
$tt['person1']=$list[0]['TAX6'];
$tt['person2']=$list[0]['TAX7'];
$tt['person3']=$list[0]['TAX8'];
$tt['amt']=$list[0]['SALESTTLAMT'];
$tt['charge']=$list[0]['TAX1'];
$tt['listtype']=$listitem[0]['REMARKS'];
$tt['listdis2']=0;
$tt['ininv']=0;
$tt['freeinv']=0;

$paycode='EX10';
if($list[0]['CLKNAME']=='FoodPanda'&&$list[0]['TAX4']!='0'){//2022/2/15 foodpanda���x�걵����l�äw�I��
	//2022/5/19 �Τ@�s���|�s�b�b��Ƶ����A�ݭn�r��ާ@
	if(preg_match('/(�Τ@�s��: )/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 �ˬd�O�_�ݭn�Τ@�s��
		$container=preg_split('/(�Τ@�s��: )/',$list[0]['RELINVOICENUMBER']);
		$tt['ban']=substr($container[1],0,8);
		$tt['container']='';//2022/5/19 Foodpanda�S�����ȤH��J����
	}
	else if(preg_match('/(�Τ@�s��:)/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 �ˬd�O�_�ݭn�Τ@�s���F�֤@�I���U�Ӫ�Foodpanda�Ƶ����A�|�N�ťծ���(�����D���̬�����n�ܰʮ榡)
		$container=preg_split('/(�Τ@�s��:)/',$list[0]['RELINVOICENUMBER']);
		$tt['ban']=substr($container[1],0,8);
		$tt['container']='';//2022/5/19 Foodpanda�S�����ȤH��J����
	}
	else{
	}
	if($list[0]['EX10']!='0'){//2022/2/15 �Y��l�Y�U�Ӫ��ɭԨS���]�wfoodpanda���I�ڤ覡�A�|�s�bEX10����줤
		$tt['payment']['foodpanda']['money']=$list[0]['EX10'];
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
		$tt['payment']['foodpanda']['method'][]=$paycode;
		$tt['payment']['foodpanda']['money'][]=$list[0][$paycode];
	}
}
else if($list[0]['CLKNAME']=='FoodPanda'){//2022/5/19 ���I�ڪ���l���n�ˬd�Τ@�s��
	//2022/5/19 �Τ@�s���|�s�b�b��Ƶ����A�ݭn�r��ާ@
	if(preg_match('/(�Τ@�s��: )/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 �ˬd�O�_�ݭn�Τ@�s��
		$container=preg_split('/(�Τ@�s��: )/',$list[0]['RELINVOICENUMBER']);
		$tt['ban']=substr($container[1],0,8);
		$tt['container']='';//2022/5/19 Foodpanda�S�����ȤH��J����
	}
	else if(preg_match('/(�Τ@�s��:)/',$list[0]['RELINVOICENUMBER'])){//2022/5/19 �ˬd�O�_�ݭn�Τ@�s���F�֤@�I���U�Ӫ�Foodpanda�Ƶ����A�|�N�ťծ���(�����D���̬�����n�ܰʮ榡)
		$container=preg_split('/(�Τ@�s��:)/',$list[0]['RELINVOICENUMBER']);
		$tt['ban']=substr($container[1],0,8);
		$tt['container']='';//2022/5/19 Foodpanda�S�����ȤH��J����
	}
	else{
	}
}
else{
}
if($list[0]['CLKNAME']=='UberEats'&&$list[0]['TAX4']!='0'){//2022/10/3 ubereats���x�걵����l�äw�I��
	if($list[0]['EX10']!='0'){//2022/2/15 �Y��l�Y�U�Ӫ��ɭԨS���]�wubereats���I�ڤ覡�A�|�s�bEX10����줤
		$tt['payment']['ubereats']['money']=$list[0]['EX10'];
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
		$tt['payment']['ubereats']['method'][]=$paycode;
		$tt['payment']['ubereats']['money'][]=$list[0][$paycode];
	}
}
else{
}
if($list[0]['CLKNAME']=='QuickClick'&&$list[0]['TAX4']!='0'){//2022/2/15 quickclick���x�걵����l�äw�I��
	if($list[0]['EX10']!='0'){//2022/2/15 �Y��l�Y�U�Ӫ��ɭԨS���]�wquickclick���I�ڤ覡�A�|�s�bEX10����줤
		$tt['payment']['quickclick']['money']=$list[0]['EX10'];
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
			$tt['payment']['foodpanda']['method'][]=$paycode;
			$tt['payment']['foodpanda']['money'][]=$list[0][$paycode];
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
			$tt['payment']['ubereats']['method'][]=$paycode;
			$tt['payment']['ubereats']['money'][]=$list[0][$paycode];
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
			$tt['payment']['quickclick']['method'][]=$paycode;
			$tt['payment']['quickclick']['money'][]=$list[0][$paycode];
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
	$tt['ban']=$nidinpaytype[0];
	$tt['container']=$nidinpaytype[1];
	if($nidinpaytype[sizeof($nidinpaytype)-1]=='50'){//2021/3/10 50:POS�I�ڡA�����u���F��x���R�����ϥ�
	}
	else{//10:nidin�u�W�I��
		for($i=1;$i<sizeof($nidinarray);$i=$i+2){
			$tt['payment']['nidin']['method'][]=$nidinarray[$i];
			$tt['payment']['nidin']['money'][]=$nidinarray[$i+1];
		}
	}
}
else{
}
if(isset($list[0]['intella'])){//2021/9/13 intella�I�ڤ覡
	$intellaarray=preg_split('/:/',$list[0]['intella']);
	//$intellapaytype=preg_split('/-/',$intellaarray[0]);
	//$tt['ban']=$nidinpaytype[0];
	//$tt['container']=$nidinpaytype[1];
	/*if($nidinpaytype[sizeof($nidinpaytype)-1]=='50'){//2021/9/13 50:POS�I�ڡA�����u���F��x���R�����ϥ�
	}
	else{//10:nidin�u�W�I��*/
		for($i=1;$i<sizeof($intellaarray);$i=$i+2){
			$tt['payment']['intella']['method'][]=$intellaarray[$i];
			$tt['payment']['intella']['money'][]=$intellaarray[$i+1];
		}
	//}
}
else{
}
for($i=0;$i<sizeof($listitem);$i=$i+2){
	if($listitem[$i]['ITEMCODE']=='autodis'){
	}
	else if($listitem[$i]['ITEMCODE']=='list'){
		$tt['listdis2']=$listitem[$i]['AMT'];
	}
	else{
		if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['insaleinv'])&&$menu[intval($listitem[$i]['ITEMCODE'])]['insaleinv']=='0'){//�K�|
			$tt['freeinv']=intval($tt['freeinv'])+intval(floatval($listitem[$i]['AMT'])+floatval($listitem[$i+1]['AMT']));
		}
		else{//���|
			$tt['ininv']=intval($tt['ininv'])+intval(floatval($listitem[$i]['AMT'])+floatval($listitem[$i+1]['AMT']));
		}
		if(isset($tt['no'])){
			$tt['no'][sizeof($tt['no'])]=intval($listitem[$i]['ITEMCODE']);
			for($j=1;$j<=4;$j++){
				$tt['dis'.$j][sizeof($tt['dis'.$j])]=$menu[intval($listitem[$i]['ITEMCODE'])]['dis'.$j];
			}
			$tt['discount'][sizeof($tt['discount'])]=$listitem[$i+1]['AMT'];
			$tt['getpointtype'][sizeof($tt['getpointtype'])]=$listitem[$i]['TAXCODE4'];//2020/4/20 1>>�T�w�I��2>>���B�I��
			$tt['dispoint'][sizeof($tt['dispoint'])]=-(intval($listitem[$i+1]['TAXCODE5'])+intval($listitem[$i]['TAXCODE5']));//2020/4/20 ��~�P�P���I�ƧI���ҨϥΪ��I��
			$tt['unitprice'][sizeof($tt['unitprice'])]=$listitem[$i]['UNITPRICE'];
			$tt['money'][sizeof($tt['money'])]=(floatval($listitem[$i]['AMT'])/floatval($listitem[$i]['QTY']));
			$tt['subtotal'][sizeof($tt['subtotal'])]=floatval($listitem[$i]['AMT']);//���O���B
			$tt['number'][sizeof($tt['number'])]=$listitem[$i]['QTY'];
			//$tt['itemdis'][sizeof($tt['itemdis'])]=$listitem[$i+1]['AMT'];
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['charge'])&&$menu[intval($listitem[$i]['ITEMCODE'])]['charge']!=''){
				$tt['needcharge'][sizeof($tt['needcharge'])]=$menu[intval($listitem[$i]['ITEMCODE'])]['charge'];
			}
			else{
				$tt['needcharge'][sizeof($tt['needcharge'])]='1';
			}
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['listdis'])){
				$tt['listdis'][sizeof($tt['listdis'])]=$menu[intval($listitem[$i]['ITEMCODE'])]['listdis'];
			}
			else{
				$tt['listdis'][sizeof($tt['listdis'])]="1";
			}
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['bothdis'])){
				$tt['bothdis'][sizeof($tt['bothdis'])]=$menu[intval($listitem[$i]['ITEMCODE'])]['bothdis'];
			}
			else{
				$tt['bothdis'][sizeof($tt['bothdis'])]="1";
			}
		}
		else{
			$tt['no'][0]=intval($listitem[$i]['ITEMCODE']);
			for($j=1;$j<=4;$j++){
				$tt['dis'.$j][0]=$menu[intval($listitem[$i]['ITEMCODE'])]['dis'.$j];
			}
			$tt['discount'][0]=$listitem[$i+1]['AMT'];
			$tt['getpointtype'][0]=$listitem[$i]['TAXCODE4'];//2020/4/20 1>>�T�w�I��2>>���B�I��
			$tt['dispoint'][0]=-(intval($listitem[$i+1]['TAXCODE5'])+intval($listitem[$i]['TAXCODE5']));//2020/4/20 ��~�P�P���I�ƧI���ҨϥΪ��I��
			$tt['unitprice'][0]=$listitem[$i]['UNITPRICE'];
			$tt['money'][0]=(floatval($listitem[$i]['AMT'])/floatval($listitem[$i]['QTY']));
			$tt['subtotal'][0]=floatval($listitem[$i]['AMT']);//���O���B
			$tt['number'][0]=$listitem[$i]['QTY'];
			//$tt['itemdis'][0]=$listitem[$i+1]['AMT'];
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['charge'])&&$menu[intval($listitem[$i]['ITEMCODE'])]['charge']!=''){
				$tt['needcharge'][0]=$menu[intval($listitem[$i]['ITEMCODE'])]['charge'];
			}
			else{
				$tt['needcharge'][0]='1';
			}
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['listdis'])){
				$tt['listdis'][0]=$menu[intval($listitem[$i]['ITEMCODE'])]['listdis'];
			}
			else{
				$tt['listdis'][0]="1";
			}
			if(isset($menu[intval($listitem[$i]['ITEMCODE'])]['bothdis'])){
				$tt['bothdis'][0]=$menu[intval($listitem[$i]['ITEMCODE'])]['bothdis'];
			}
			else{
				$tt['bothdis'][0]="1";
			}
		}
	}
}

echo json_encode($tt);
?>