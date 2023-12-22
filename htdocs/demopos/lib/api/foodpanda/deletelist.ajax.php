<?php
include_once '../../../../tool/dbTool.inc.php';
require_once '../../../../tool/PHPWord.php';

$listdata=[];
for($i=0;$i<sizeof($_POST['data'][0]);$i++){
	$conn=sqlconnect('../../../../database/sale','SALES_'.substr($_POST['data'][0][$i]['listbizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT * FROM tempCST011 WHERE CREATEDATETIME="'.$_POST['data'][0][$i]['CREATEDATETIME'].'"';
	$res1=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT * FROM CST011 WHERE CREATEDATETIME="'.$_POST['data'][0][$i]['CREATEDATETIME'].'"';
	$res2=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	
	if(isset($res2[0]['NBCHKNUMBER'])&&$res2[0]['NBCHKNUMBER']=='Y'){//POS作廢單
		$listdata[]=array($_POST['data'][0][$i]['listbizdate'],$res2[0]['CONSECNUMBER'],'void',$_POST['data'][0][$i]['CONSECNUMBER'],$res2[0]['CLKCODE'],$_POST['data'][0][$i]['BIZDATE']);
	}
	else if(isset($res2[0]['BIZDATE'])){//POS已結帳
		$listdata[]=array($_POST['data'][0][$i]['listbizdate'],$res2[0]['CONSECNUMBER'],'saled',$_POST['data'][0][$i]['CONSECNUMBER'],$res2[0]['CLKCODE'],$_POST['data'][0][$i]['BIZDATE']);
	}
	else if(isset($res1[0]['INVOICENUMBER'])&&strlen(trim($res1[0]['INVOICENUMBER']))==10){//未結帳但已開發票
		$listdata[]=array($_POST['data'][0][$i]['listbizdate'],$res1[0]['CONSECNUMBER'],'openinv',$_POST['data'][0][$i]['CONSECNUMBER'],$res1[0]['CLKCODE'],$_POST['data'][0][$i]['BIZDATE']);
	}
	else if(isset($res1[0]['BIZDATE'])){//未結帳
		$listdata[]=array($_POST['data'][0][$i]['listbizdate'],$res1[0]['CONSECNUMBER'],'delete',$_POST['data'][0][$i]['CONSECNUMBER'],$res1[0]['CLKCODE'],$_POST['data'][0][$i]['BIZDATE']);
	}
	else{//不存在
		$listdata[]=array($_POST['data'][0][$i]['listbizdate'],'','empty',$_POST['data'][0][$i]['CONSECNUMBER'],$_POST['data'][0][$i]['CLKCODE'],$_POST['data'][0][$i]['BIZDATE']);
	}
}

//echo json_encode($listdata);

for($i=0;$i<sizeof($listdata);$i++){
	$PHPWord = new PHPWord();
	$document = $PHPWord->loadTemplate('../../../../template/deletelisttag.docx');
	$document->setValue('datetime',date('Y/m/d H:i:s'));
	if($listdata[$i][2]=='void'){//POS作廢單
		$document->setValue('result','作廢POS已作廢單');
	}
	else if($listdata[$i][2]=='saled'){//POS已結帳
		$document->setValue('result','作廢POS已結單');
	}
	else if($listdata[$i][2]=='openinv'){//未結帳但已開發票
		$document->setValue('result','作廢POS未結單(invoice)');
	}
	else if($listdata[$i][2]=='delete'){//未結帳
		$document->setValue('result','作廢POS未結單');
	}
	else{//$listdata[$i][2]=='empty'//不存在
		$document->setValue('result','未對應帳單號');
	}
	$document->setValue('consecnumber',$listdata[$i][1].'(POS_'.$listdata[$i][4].'_'.$listdata[$i][5].')');

	$filename=date('YmdHis');
	$document->save("../../../../print/read/".$listdata[$i][1]."_clientlistm1_".$filename.".docx");
	$f=fopen("../../../../print/noread/".$listdata[$i][1]."_clientlistm1_".$filename.".prt",'w');
	fclose($f);
}

echo json_encode($listdata);
?>