<?php
//用於補印明細單、工作單、貼紙，將需要補印的資料轉換好對應的資料
//想取代掉reprint.ajax.php
//將轉換好的資料丟進對應的程式(create.list.php、create.kitchen.php、create.tag.php)中產生檔案
//2021/8/3 補印明細單部分已完成。
//2021/8/3 目前只套用在 單獨"補印明細單"。
//print_r($_POST);

include_once '../../../tool/dbTool.inc.php';

$res=[];

$res['company']=$_POST['company'];
$res['listtype']=$_POST['listtype'];
$res['consecnumber']=$_POST['consecnumber'];
$res['bizdate']=$_POST['bizdate'];
$res['credate']=$_POST['credate'];
$res['remachinetype']=$_POST['machinetype'];//補印機號
$res['reusername']=$_POST['username'];//補印人員
$res['looptype']='1';//出單(明細單、貼紙、工作單)(固定)
$res['tempbuytype']='1';//列印完整明細單(固定)

$itemname=parse_ini_file('../../../database/'.$res['company'].'-menu.ini',true);
$itemfront=parse_ini_file('../../../database/'.$res['company'].'-front.ini',true);
$itemtaste=parse_ini_file('../../../database/'.$res['company'].'-taste.ini',true);
//2021/9/22 補印時要印出付款方式
$otherpay=parse_ini_file('../../../database/otherpay.ini',true);

$itemconn=sqlconnect('../../../database','menu.db','','','','sqlite');
/*$sql='SELECT * FROM itemsdata WHERE company="'.$res['bizdate'].'" AND inumber IN ("'.preg_replace('/,/','","',$_POST['inumber']).'")';
$itemdata=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');*/
$itemdata=[];

$conn=sqlconnect('../../../database/sale','SALES_'.substr($res['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT * FROM salemap WHERE bizdate="'.$res['bizdate'].'" AND consecnumber="'.$res['consecnumber'].'"';
$dbdata=sqlquery($conn,$sql,'sqlite');
if(isset($dbdata[0]['saleno'])){
	$res['saleno']=$dbdata[0]['saleno'];
}
else{
	$res['saleno']='';
}

if($_POST['reprint']=='clientlist'){//明細單
	$res['printclientlist']='1';//印出明細(固定)
	$res['memno']='';
	$res['memaddno']='';
	$res['memname']='';
	$res['memtel']='';
	$res['tablenumber']='';
	$res['person1']=0;
	$res['person2']=0;
	$res['person3']=0;
	$res['username']='';
	$res['machinetype']='';
	$res['totalnumber']=0;
	$res['charge']=0;
	$res['total']=0;
	$res['no']=[];
	$res['childtype']=[];
	$res['isgroup']=[];
	$res['number']=[];
	$res['taste1']=[];
	$res['taste1name']=[];
	$res['taste1number']=[];
	$res['taste1price']=[];
	$res['taste1money']=[];
	$res['discount']=[];
	$res['mname1']=[];
	$res['order']=[];
	$res['name']=[];
	$res['name2']=[];
	$res['unitprice']=[];
	$res['subtotal']=[];
	//以下參數為針對已結帳單的補印
	//$res['listtotal']=0;//商品小計；用於判斷是否為結帳單，沒有實際印出
	//$res['should']=0;//應付金額
	//$res['memberdis']=;//會員折扣；該參數不一定會顯示，有用到才會產生
	//$res['listdis1']=;//帳單折扣；該參數不一定會顯示，有用到才會產生(成對)
	//$res['listdis2']=;//帳單折讓；該參數不一定會顯示，有用到才會產生(成對)


	if($_POST['saletype']=='temp'){//暫結單
		$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$res['bizdate'].'" AND CONSECNUMBER="'.$res['consecnumber'].'"';
		$cst011=sqlquery($conn,$sql,'sqlite');
		
		$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$res['bizdate'].'" AND CONSECNUMBER="'.$res['consecnumber'].'" ORDER BY LINENUMBER ASC';
		$cst012=sqlquery($conn,$sql,'sqlite');
	}
	else{
		$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$res['bizdate'].'" AND CONSECNUMBER="'.$res['consecnumber'].'"';
		$cst011=sqlquery($conn,$sql,'sqlite');
		
		$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$res['bizdate'].'" AND CONSECNUMBER="'.$res['consecnumber'].'" ORDER BY LINENUMBER ASC';
		$cst012=sqlquery($conn,$sql,'sqlite');

		if(isset($cst011[0]['BIZDATE'])){
			$res['listtotal']=$cst011[0]['SALESTTLAMT']+$cst011[0]['TAX1'];//2021/9/22 銷售額與服務費不同欄位，需要另外加總
			$res['should']=$cst011[0]['SALESTTLAMT']+$cst011[0]['TAX1'];//2021/9/22 銷售額與服務費不同欄位，需要另外加總
		}
		else{
		}

		//if(strlen($cst011[0]['REMARKS'])>=8&&substr($cst011[0]['REMARKS'],0,8)=='editsale'){//2021/9/23 判斷錯誤，該參數為修改帳單
			if(file_exists('../../../database/sale/Cover.db')){//2021/9/22 修改付款；補印時要印出付款方式
				$cvconn=sqlconnect('../../../database/sale','Cover.db','','','','sqlite');
				$sql='SELECT * FROM list WHERE bizdate="'.$res['bizdate'].'" AND consecnumber="'.$res['consecnumber'].'"';
				$cover=sqlquery($cvconn,$sql,'sqlite');
				sqlclose($cvconn,'sqlite');
				if(isset($cover[0])){//有修改付款紀錄
					$cst011[0]['TAX1']=$cover[0]['tax1'];
					$cst011[0]['TAX2']=$cover[0]['tax2'];
					$cst011[0]['TAX3']=$cover[0]['tax3'];
					$cst011[0]['TAX4']=$cover[0]['tax4'];
					$cst011[0]['TAX9']=$cover[0]['tax9'];
					$cst011[0]['TA1']=$cover[0]['ta1'];
					$cst011[0]['TA2']=$cover[0]['ta2'];
					$cst011[0]['TA3']=$cover[0]['ta3'];
					$cst011[0]['TA4']=$cover[0]['ta4'];
					$cst011[0]['TA5']=$cover[0]['ta5'];
					$cst011[0]['TA6']=$cover[0]['ta6'];
					$cst011[0]['TA7']=$cover[0]['ta7'];
					$cst011[0]['TA8']=$cover[0]['ta8'];
					$cst011[0]['TA9']=$cover[0]['ta9'];
					$cst011[0]['TA10']=$cover[0]['ta10'];
				}
				else{
				}
			}
			else{
			}
		/*}
		else{
		}*/
	}

	if(isset($cst011[0]['BIZDATE'])){
		$res['memno']=$cst011[0]['CUSTCODE'];
		if(preg_match('/;-;/',$cst011[0]['CUSTCODE'])){
			$tempdata=preg_split('/;-;/',$cst011[0]['CUSTCODE']);
			if(isset($tempdata[2])){
				$res['memaddno']=$tempdata[2];
			}
			else{
				$res['memaddno']=1;
			}
		}
		else{
			$res['memaddno']=1;
		}
		$res['memname']=$_POST['memname'];
		$res['memtel']=$_POST['memtel'];
		$res['tablenumber']=$cst011[0]['TABLENUMBER'];
		$res['person1']=$cst011[0]['TAX6'];
		$res['person2']=$cst011[0]['TAX7'];
		$res['person3']=$cst011[0]['TAX8'];
		$res['username']=$cst011[0]['CLKNAME'];
		$res['machinetype']=$cst011[0]['TERMINALNUMBER'];
		$res['totalnumber']=$cst011[0]['SALESTTLQTY'];
		$res['charge']=$cst011[0]['TAX1'];
		$res['total']=$cst011[0]['SALESTTLAMT']+$cst011[0]['TAX1'];//2021/9/22 銷售額與服務費不同欄位，需要另外加總
		$res['TAX1']=$cst011[0]['TAX1'];
		$res['TAX2']=$cst011[0]['TAX2'];
		$res['TAX3']=$cst011[0]['TAX3'];
		$res['TAX4']=$cst011[0]['TAX4'];
		$res['TAX9']=$cst011[0]['TAX9'];
		$res['TA1']=$cst011[0]['TA1'];
		$res['TA2']=$cst011[0]['TA2'];
		$res['TA3']=$cst011[0]['TA3'];
		$res['TA4']=$cst011[0]['TA4'];
		$res['TA5']=$cst011[0]['TA5'];
		$res['TA6']=$cst011[0]['TA6'];
		$res['TA7']=$cst011[0]['TA7'];
		$res['TA8']=$cst011[0]['TA8'];
		$res['TA9']=$cst011[0]['TA9'];
		$res['TA10']=$cst011[0]['TA10'];
		if(isset($res['listtotal'])){//2021/9/22 補印結帳單
			$res['already']=$cst011[0]['SALESTTLAMT']+$cst011[0]['TAX1'];
			$res['cashmoney']=$cst011[0]['TAX2'];
			$res['cash']=$cst011[0]['TAX3'];
			$res['change']=0;
			$res['other']=0;
			$res['otherfix']=0;
			$res['otherstring']='';
		}
		else{
			$res['change']=0;
			$res['other']=0;
			$res['otherfix']=0;
			$res['otherstring']='';
		}
		if($res['TAX4']>0){//2021/9/22 其他付款
			for($i=1;$i<sizeof($otherpay);$i++){
				if(isset($otherpay['item'.$i]['dbname'])&&(!isset($otherpay['item'.$i]['location'])||$otherpay['item'.$i]['location']=='CST011')){
					if(preg_match('/=/',$res[$otherpay['item'.$i]['dbname']])){
					}
					else{
						$res[$otherpay['item'.$i]['dbname']]='1='.$res[$otherpay['item'.$i]['dbname']];
					}

					$paydata=preg_split('/=/',$res[$otherpay['item'.$i]['dbname']]);

					if(isset($otherpay['item'.$i]['type'])&&$otherpay['item'.$i]['type']=='2'){//不找零
						$res['otherfix'] += $paydata[1];
					}
					else{//找零
						$res['other'] += $paydata[1];
					}

					if($res['otherstring']!=''){
						$res['otherstring'] .= ',';
					}
					else{
					}
					$res['otherstring'] .= 'CST011-'.$otherpay['item'.$i]['dbname'].':'.$res[$otherpay['item'.$i]['dbname']];
				}
				else if(isset($otherpay['item'.$i]['dbname'])){
					if(isset($res[$otherpay['item'.$i]['location']])){
						if(preg_match('/=/',$res[$otherpay['item'.$i]['location']])){
						}
						else{
							$res[$otherpay['item'.$i]['location']]='1='.$res[$otherpay['item'.$i]['location']];
						}

						$paydata=preg_split('/=/',$res[$otherpay['item'.$i]['location']]);

						if(isset($otherpay['item'.$i]['type'])&&$otherpay['item'.$i]['type']=='2'){//不找零
							$res['otherfix'] += $paydata[1];
						}
						else{//找零
							$res['other'] += $paydata[1];
						}

						if($res['otherstring']!=''){
							$res['otherstring'] .= ',';
						}
						else{
						}
						$res['otherstring'] .= 'CST011-'.$otherpay['item'.$i]['location'].':'.$res[$otherpay['item'.$i]['location']];
					}
					else{
						if(preg_match('/=/',$res[$otherpay['item'.$i]['dbname']])){
						}
						else{
							$res[$otherpay['item'.$i]['dbname']]='1='.$res[$otherpay['item'.$i]['dbname']];
						}

						$paydata=preg_split('/=/',$res[$otherpay['item'.$i]['dbname']]);

						if(isset($otherpay['item'.$i]['type'])&&$otherpay['item'.$i]['type']=='2'){//不找零
							$res['otherfix'] += $paydata[1];
						}
						else{//找零
							$res['other'] += $paydata[1];
						}

						if($res['otherstring']!=''){
							$res['otherstring'] .= ',';
						}
						else{
						}
						$res['otherstring'] .= 'CST011-'.$otherpay['item'.$i]['dbname'].':'.$res[$otherpay['item'.$i]['dbname']];
					}
				}
				else{
					$res['otherstring'] .= 'CST011-'.$otherpay['item'.$i]['dbname'].$otherpay['item'.$i]['location'].':'.$res[$otherpay['item'.$i]['dbname']].$res[$otherpay['item'.$i]['location']];
					continue;
				}
				
			}
		}
		else{
		}
	}
	else{
	}
	if(isset($cst012[0]['BIZDATE'])){
		for($i=0;$i<sizeof($cst012);$i++){
			if(is_numeric($cst012[$i]['ITEMCODE'])){//為品項records
				$index=sizeof($res['no']);
				$res['no'][$index]=intval($cst012[$i]['ITEMCODE']);
				if(isset($itemdata[intval($cst012[$i]['ITEMCODE'])])){
					$res['childtype'][$index]=($itemdata[intval($cst012[$i]['ITEMCODE'])]['childtype']==null)?(''):($itemdata[intval($cst012[$i]['ITEMCODE'])]['childtype']);
					$res['isgroup'][$index]=$itemdata[intval($cst012[$i]['ITEMCODE'])]['isgroup'];
				}
				else{
					$itemsql='SELECT * FROM itemsdata WHERE company="'.$res['company'].'" AND inumber="'.intval($cst012[$i]['ITEMCODE']).'"';
					$getitemdata=sqlquery($itemconn,$itemsql,'sqlite');
					if(isset($getitemdata[0]['company'])){
						$res['childtype'][$index]=($getitemdata[0]['childtype']==null)?(''):($getitemdata[0]['childtype']);
						$res['isgroup'][$index]=$getitemdata[0]['isgroup'];
						$itemdata[intval($cst012[$i]['ITEMCODE'])]=$getitemdata[0];
					}
					else{
						$res['childtype'][$index]='';
						$res['isgroup'][$index]='';
					}
				}
				$res['number'][$index]=$cst012[$i]['QTY'];
				$res['taste1'][$index]='';
				$res['taste1name'][$index]='';
				$res['taste1number'][$index]='';
				$res['taste1price'][$index]='';
				$res['taste1money'][$index]=0;
				for($t=1;$t<=10;$t++){
					if($cst012[$i]['SELECTIVEITEM'.$t]!=null&&$cst012[$i]['SELECTIVEITEM'.$t]!=''){
						if(preg_match('/,/',$cst012[$i]['SELECTIVEITEM'.$t])){//新版備註(都寫在第一個欄位中；以 , 分隔)
							$temptaste=preg_split('/,/',$cst012[$i]['SELECTIVEITEM'.$t]);
							for($tt=0;$tt<sizeof($temptaste);$tt++){
								if($res['taste1'][$index]!=''){
									$res['taste1'][$index] .= ',';
									$res['taste1name'][$index] .= ',';
									$res['taste1number'][$index] .= ',';
									$res['taste1price'][$index] .= ',';
								}
								else{
								}
								if(substr($temptaste[$tt],0,5)!='99999'){//一般備註
									$res['taste1'][$index] .= intval(substr($temptaste[$tt],0,5));
									$res['taste1name'][$index] .= $itemtaste[intval(substr($temptaste[$tt],0,5))]['name1'];
									if(trim($itemtaste[intval(substr($temptaste[$tt],0,5))]['name2'])!=''){
										$res['taste1name'][$index] .= '/'.$itemtaste[intval(substr($temptaste[$tt],0,5))]['name2'];
									}
									else{
									}
									if(intval(substr($temptaste[$tt],5))>1){
										$res['taste1name'][$index] .= '*'.substr($temptaste[$tt],5);
									}
									else{
									}
									$res['taste1number'][$index] .= substr($temptaste[$tt],5);
									$res['taste1price'][$index] .= $itemtaste[intval(substr($temptaste[$tt],0,5))]['money'];
									$res['taste1money'][$index] += ($itemtaste[intval(substr($temptaste[$tt],0,5))]['money']*substr($temptaste[$tt],5));
								}
								else{//帳單備註
									$res['taste1'][$index] .= '999991';
									$res['taste1name'][$index] .= substr($temptaste[$tt],7);
									$res['taste1number'][$index] .= '1';
									$res['taste1price'][$index] .= '0';
									$res['taste1money'][$index] += '0';
								}
							}
						}
						else{//舊版備註(每個備註都寫在單獨欄位，最多10個備註；包含帳單備註)
							if($res['taste1'][$index]!=''){
								$res['taste1'][$index] .= ',';
								$res['taste1name'][$index] .= ',';
								$res['taste1number'][$index] .= ',';
								$res['taste1price'][$index] .= ',';
							}
							else{
							}
							if(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5)!='99999'){//一般備註
								$res['taste1'][$index] .= intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5));
								$res['taste1name'][$index] .= $itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
								if(trim($itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['name2'])!=''){
									$res['taste1name'][$index] .= '/'.$itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['name2'];
								}
								else{
								}
								if(intval(substr($cst012[$i]['SELECTIVEITEM'.$t],5))>1){
									$res['taste1name'][$index] .= '*'.substr($cst012[$i]['SELECTIVEITEM'.$t],5);
								}
								else{
								}
								$res['taste1number'][$index] .= substr($cst012[$i]['SELECTIVEITEM'.$t],5);
								$res['taste1price'][$index] .= $itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['money'];
								$res['taste1money'][$index] += ($itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['money']*substr($cst012[$i]['SELECTIVEITEM'.$t],5));
							}
							else{//帳單備註
								$res['taste1'][$index] .= '999991';
								$res['taste1name'][$index] .= substr($cst012[$i]['SELECTIVEITEM'.$t],7);
								$res['taste1number'][$index] .= '1';
								$res['taste1price'][$index] .= '0';
								$res['taste1money'][$index] += '0';
							}
						}
					}
					else{
						break;
					}
				}
				$res['discount'][$index]=-$cst012[($i+1)]['AMT'];
				$res['mname1'][$index]=$cst012[$i]['UNITPRICELINK'];
				if(isset($itemfront[intval($cst012[$i]['ITEMGRPCODE'])])&&isset($itemfront[intval($cst012[$i]['ITEMGRPCODE'])]['subtype'])&&$itemfront[intval($cst012[$i]['ITEMGRPCODE'])]['subtype']=='1'){
					$res['order'][$index]='－';
				}
				else{
					$res['order'][$index]='';
				}
				$res['name'][$index]=$cst012[$i]['ITEMNAME'];
				$res['name2'][$index]=$itemname[intval($cst012[$i]['ITEMCODE'])]['name2'];
				$res['unitprice'][$index]=$cst012[$i]['UNITPRICE'];
				$res['subtotal'][$index]=$cst012[$i]['AMT']+$cst012[($i+1)]['AMT'];
			}
			else{
				if($cst012[$i]['ITEMCODE']=='member'){//會員折扣
					$res['memberdis']=-$cst012[$i]['AMT'];
				}
				else if($cst012[$i]['ITEMCODE']=='list'){//帳單折扣
					$res['listdis1']=-$cst012[$i]['AMT'];
					$res['listdis2']=0;
				}
				else{
				}
			}
		}
	}
	else{
	}
}
else{//貼紙、工作單
	$getlinenumber=preg_split('/,/',$_POST['linenumber']);//補印的序號
	$max=sizeof($getlinenumber);
	for($i=0;$i<$max;$i++){//除了品項的records以外，還要撈出單品折扣的records(工作單使用)
		$getlinenumber[]=str_pad((intval($getlinenumber[$i])+1),3,'0',STR_PAD_LEFT);
	}
	$res['memno']='';
	$res['tablenumber']='';
	$res['username']='';
	$res['machinetype']='';
	$res['no']=[];
	$res['childtype']=[];
	$res['isgroup']=[];
	$res['number']=[];
	$res['taste1']=[];
	$res['taste1name']=[];
	$res['taste1number']=[];
	$res['taste1price']=[];
	$res['taste1money']=[];
	$res['discount']=[];
	$res['mname1']=[];
	$res['mname2']=[];
	$res['order']=[];
	$res['name']=[];
	$res['name2']=[];
	$res['unitprice']=[];
	$res['subtotal']=[];
	$res['money']=[];
	$res['typeno']=[];
	$res['type']=[];
	$res['discontent']=[];
	$res['linenumber']=[];
	
	if($_POST['saletype']=='temp'){//暫結單
		$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$res['bizdate'].'" AND CONSECNUMBER="'.$res['consecnumber'].'"';
		$cst011=sqlquery($conn,$sql,'sqlite');

		$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$res['bizdate'].'" AND CONSECNUMBER="'.$res['consecnumber'].'" AND LINENUMBER IN ("'.implode('","',$getlinenumber).'") ORDER BY LINENUMBER ASC';
		$cst012=sqlquery($conn,$sql,'sqlite');
	}
	else{
		$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$res['bizdate'].'" AND CONSECNUMBER="'.$res['consecnumber'].'"';
		$cst011=sqlquery($conn,$sql,'sqlite');

		$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$res['bizdate'].'" AND CONSECNUMBER="'.$res['consecnumber'].'" AND LINENUMBER IN ("'.implode('","',$getlinenumber).'") ORDER BY LINENUMBER ASC';
		$cst012=sqlquery($conn,$sql,'sqlite');

		if(isset($cst011[0]['BIZDATE'])){
			$res['listtotal']=$cst011[0]['SALESTTLAMT'];
			$res['should']=$cst011[0]['SALESTTLAMT'];
		}
		else{
		}
	}

	$res['person1']=$cst011[0]['TAX6'];
	$res['person2']=$cst011[0]['TAX7'];
	$res['person3']=$cst011[0]['TAX8'];
	
	if(isset($cst011[0]['BIZDATE'])){
		$res['memno']=$cst011[0]['CUSTCODE'];
		$res['tablenumber']=$cst011[0]['TABLENUMBER'];
		$res['username']=$cst011[0]['CLKNAME'];
		if(strlen($_POST['saletype'])>3){//2023/1/9 外送單暫結時，terminalnumber會存外送單號，強制改成m1，避免後續補印出單錯誤
			$res['machinetype']='m1';
		}
		else{
			$res['machinetype']=$cst011[0]['TERMINALNUMBER'];
		}
	}
	else{
	}
	if(isset($cst012[0]['BIZDATE'])){
		for($i=0;$i<sizeof($cst012);$i++){
			if(is_numeric($cst012[$i]['ITEMCODE'])){//為品項records
				$index=sizeof($res['no']);
				$res['no'][$index]=intval($cst012[$i]['ITEMCODE']);
				if(isset($itemdata[intval($cst012[$i]['ITEMCODE'])])){
					$res['childtype'][$index]=($itemdata[intval($cst012[$i]['ITEMCODE'])]['childtype']==null)?(''):($itemdata[intval($cst012[$i]['ITEMCODE'])]['childtype']);
					$res['isgroup'][$index]=$itemdata[intval($cst012[$i]['ITEMCODE'])]['isgroup'];
					$res['typeno'][$index]=$itemdata[intval($cst012[$i]['ITEMCODE'])]['fronttype'];
				}
				else{
					$itemsql='SELECT * FROM itemsdata WHERE company="'.$res['company'].'" AND inumber="'.intval($cst012[$i]['ITEMCODE']).'"';
					$getitemdata=sqlquery($itemconn,$itemsql,'sqlite');
					if(isset($getitemdata[0]['company'])){
						$res['childtype'][$index]=($getitemdata[0]['childtype']==null)?(''):($getitemdata[0]['childtype']);
						$res['isgroup'][$index]=$getitemdata[0]['isgroup'];
						$itemdata[intval($cst012[$i]['ITEMCODE'])]=$getitemdata[0];
						$res['typeno'][$index]=$getitemdata[0]['fronttype'];
					}
					else{
						$res['childtype'][$index]='';
						$res['isgroup'][$index]='';
					}
				}
				$res['number'][$index]=$cst012[$i]['QTY'];
				$res['taste1'][$index]='';
				$res['taste1name'][$index]='';
				$res['taste1number'][$index]='';
				$res['taste1price'][$index]='';
				$res['taste1money'][$index]=0;
				$res['money'][$index]=$cst012[$i]['UNITPRICE'];
				for($t=1;$t<=10;$t++){
					if($cst012[$i]['SELECTIVEITEM'.$t]!=null&&$cst012[$i]['SELECTIVEITEM'.$t]!=''){
						if(preg_match('/,/',$cst012[$i]['SELECTIVEITEM'.$t])){//新版備註(都寫在第一個欄位中；以 , 分隔)
							$temptaste=preg_split('/,/',$cst012[$i]['SELECTIVEITEM'.$t]);
							for($tt=0;$tt<sizeof($temptaste);$tt++){
								if($res['taste1'][$index]!=''){
									$res['taste1'][$index] .= ',';
									$res['taste1name'][$index] .= ',';
									$res['taste1number'][$index] .= ',';
									$res['taste1price'][$index] .= ',';
								}
								else{
								}
								if(substr($temptaste[$tt],0,5)!='99999'){//一般備註
									$res['taste1'][$index] .= intval(substr($temptaste[$tt],0,5));
									$res['taste1name'][$index] .= $itemtaste[intval(substr($temptaste[$tt],0,5))]['name1'];
									if(trim($itemtaste[intval(substr($temptaste[$tt],0,5))]['name2'])!=''){
										$res['taste1name'][$index] .= '/'.$itemtaste[intval(substr($temptaste[$tt],0,5))]['name2'];
									}
									else{
									}
									if(intval(substr($temptaste[$tt],5))>1){
										$res['taste1name'][$index] .= '*'.substr($temptaste[$tt],5);
									}
									else{
									}
									$res['taste1number'][$index] .= substr($temptaste[$tt],5);
									$res['taste1price'][$index] .= $itemtaste[intval(substr($temptaste[$tt],0,5))]['money'];
									$res['taste1money'][$index] += ($itemtaste[intval(substr($temptaste[$tt],0,5))]['money']*substr($temptaste[$tt],5));
									$res['money'][$index] += ($itemtaste[intval(substr($temptaste[$tt],0,5))]['money']*substr($temptaste[$tt],5));
								}
								else{//帳單備註
									$res['taste1'][$index] .= '999991';
									$res['taste1name'][$index] .= substr($temptaste[$tt],7);
									$res['taste1number'][$index] .= '1';
									$res['taste1price'][$index] .= '0';
									$res['taste1money'][$index] += '0';
								}
							}
						}
						else{//舊版備註(每個備註都寫在單獨欄位，最多10個備註；包含帳單備註)
							if($res['taste1'][$index]!=''){
								$res['taste1'][$index] .= ',';
								$res['taste1name'][$index] .= ',';
								$res['taste1number'][$index] .= ',';
								$res['taste1price'][$index] .= ',';
							}
							else{
							}
							if(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5)!='99999'){//一般備註
								$res['taste1'][$index] .= intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5));
								$res['taste1name'][$index] .= $itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['name1'];
								if(trim($itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['name2'])!=''){
									$res['taste1name'][$index] .= '/'.$itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['name2'];
								}
								else{
								}
								if(intval(substr($cst012[$i]['SELECTIVEITEM'.$t],5))>1){
									$res['taste1name'][$index] .= '*'.substr($cst012[$i]['SELECTIVEITEM'.$t],5);
								}
								else{
								}
								$res['taste1number'][$index] .= substr($cst012[$i]['SELECTIVEITEM'.$t],5);
								$res['taste1price'][$index] .= $itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['money'];
								$res['taste1money'][$index] += ($itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['money']*substr($cst012[$i]['SELECTIVEITEM'.$t],5));
								$res['money'][$index] += ($itemtaste[intval(substr($cst012[$i]['SELECTIVEITEM'.$t],0,5))]['money']*substr($cst012[$i]['SELECTIVEITEM'.$t],5));
							}
							else{//帳單備註
								$res['taste1'][$index] .= '999991';
								$res['taste1name'][$index] .= substr($cst012[$i]['SELECTIVEITEM'.$t],7);
								$res['taste1number'][$index] .= '1';
								$res['taste1price'][$index] .= '0';
								$res['taste1money'][$index] += '0';
							}
						}
					}
					else{
						break;
					}
				}
				$res['discount'][$index]=-$cst012[($i+1)]['AMT'];
				$res['mname1'][$index]=$cst012[$i]['UNITPRICELINK'];
				$res['mname2'][$index]='';
				if(isset($itemfront[intval($cst012[$i]['ITEMGRPCODE'])])&&isset($itemfront[intval($cst012[$i]['ITEMGRPCODE'])]['subtype'])&&$itemfront[intval($cst012[$i]['ITEMGRPCODE'])]['subtype']=='1'){
					$res['order'][$index]='－';
				}
				else{
					$res['order'][$index]='';
				}
				$res['name'][$index]=$cst012[$i]['ITEMNAME'];
				$res['name2'][$index]=$itemname[intval($cst012[$i]['ITEMCODE'])]['name2'];
				//$res['typeno'][$index]=$itemname[intval($cst012[$i]['ITEMCODE'])]['fronttype'];
				$res['type'][$index]='';
				$res['discontent'][$index]='';
				$res['linenumber'][$index]=intval($cst012[$i]['LINENUMBER']);
				$res['unitprice'][$index]=$cst012[$i]['UNITPRICE'];
				$res['subtotal'][$index]=$cst012[$i]['AMT']+$cst012[($i+1)]['AMT'];
			}
			else{
				if($cst012[$i]['ITEMCODE']=='member'){//會員折扣
					$res['memberdis']=-$cst012[$i]['AMT'];
				}
				else if($cst012[$i]['ITEMCODE']=='list'){//帳單折扣
					$res['listdis1']=-$cst012[$i]['AMT'];
					$res['listdis2']=0;
				}
				else{
				}
			}
		}
	}
	else{
	}
}

sqlclose($conn,'sqlite');
sqlclose($itemconn,'sqlite');

echo json_encode($res);
?>