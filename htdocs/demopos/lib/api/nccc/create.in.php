<?php
//include_once '../../../../tool/inilib.php';
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
date_default_timezone_set($initsetting['init']['settime']);
//基本字串，只要替換交易別、主機別、調閱編號、金額、時間
if(!isset($_POST['type'])){
	$_POST['type']='01';//一般交易
}
else{
	//$_POST['type']='02';//退貨
	//or
	//$_POST['type']='66';//悠遊卡餘額//2022/5/11
}
if(isset($_POST['asm'])&&preg_match('/;/',$_POST['asm'])){//2022/7/20 補上電子票證交易序號，方便電子票證退款使用//2022/5/17 因為原先沒有考慮電子票證的代號不同，因此交易完成後會將代號與優惠參數一倂儲存(以;串接)
	$temp=preg_split('/;/',$_POST['asm']);
	$_POST['asm']=$temp[0];//' ' or 'A'
	$_POST['cardtype']=$temp[1];//'N'一般信用卡交易 or 'E'電子票證
	$_POST['rfcode']=str_pad($temp[2],20,' ',STR_PAD_RIGHT);//2022/10/21 前面額外串上原交易日期(YYYYMMDD)，因此要將長度從12補到20//電子票證交易序號(退款使用)
	$_POST['approval']=str_pad($temp[3],9,' ',STR_PAD_RIGHT);//授權碼(退款使用)
}
else{
	if(!isset($_POST['asm'])||$_POST['asm']!='A'){
		$_POST['asm']=' ';
	}
	else{
		//$_POST['asm']=;//是否符合優惠活動
	}
}

$datetime=substr(date('YmdHis'),2);

if($_POST['type']!='66'){//2022/5/11 一般交易、退貨
	$money=str_pad($_POST['money'],10,'0',STR_PAD_LEFT).'00';
	if(isset($_POST['cardtype'])&&strlen($_POST['cardtype'])=='1'){//2022/5/17 E:電子票證交易、N:一般信用卡交易
	}
	else{
		$_POST['cardtype']='N';
	}
	if(isset($_POST['rfcode'])){
	}
	else{
		$_POST['rfcode']='                    ';
	}
	if(isset($_POST['approval'])){
	}
	else{
		$_POST['approval']='         ';
	}
	$text='E       '.$_POST['type'].$_POST['cardtype'].'                               '.$money.$datetime.$_POST['approval'].'                                                                                                                                                            '.$_POST['rfcode'].'                                                             '.$_POST['asm'].'                                                                                       ';
}
else{//2022/5/11 悠遊卡餘額
	$text='E       '.$_POST['type'].'E                                           '.$datetime.'                                                                                                                                                                                                                                                                                                                                              ';
}
$Y=date('Y');
$m=date('m');
$d=date('d');
if(!file_exists('../../../../print/card/log/'.$Y.'/'.$m.'/'.$d)){
	if(!file_exists('../../../../print/card/log/'.$Y.'/'.$m)){
		if(!file_exists('../../../../print/card/log/'.$Y)){
			if(!file_exists('../../../../print/card/log')){
				if(!file_exists('../../../../print/card')){
					mkdir('../../../../print/card');
				}
				else{
				}
				mkdir('../../../../print/card/log');
			}
			else{
			}
			mkdir('../../../../print/card/log/'.$Y);
		}
		else{
		}
		mkdir('../../../../print/card/log/'.$Y.'/'.$m);
	}
	else{
	}
	mkdir('../../../../print/card/log/'.$Y.'/'.$m.'/'.$d);
}
else{
}

$l=fopen('../../../../print/card/log/'.$Y.'/'.$m.'/'.$d.'/nccc.log','a');
fwrite($l,date('Y/m/d H:i:s').' --- in.dat='.$text.PHP_EOL);
if($_POST['type']!='66'){//2022/5/11 一般交易、退貨
	fwrite($l,'                    --- money='.$_POST['money'].PHP_EOL);
	if(isset($_POST['type'])&&$_POST['type']=='02'){//退貨
		fwrite($l,'                    --- ASM Award flag='.$_POST['asm'].PHP_EOL);
	}
	else{//if($_POST['type']=='01'){//一般交易
	}
}
else{
	fwrite($l,'                    --- easycardbalance'.PHP_EOL);
}
fclose($l);

if(file_exists('../../../../print/card/out.dat')){
	unlink('../../../../print/card/out.dat');
}
else{
}
if(file_exists('../../../../print/card/in.dat')){
	unlink('../../../../print/card/in.dat');
}
else{
}

$f=fopen('../../../../print/card/in.dat','w');
fwrite($f,$text);
fclose($f);

$f=fopen('../../../../print/noread/nccc.txt','w');
fclose($f);

$res['date']=$Y.$m.$d;
//$res['consecnumber']=$str2;
echo json_encode($res);
?>