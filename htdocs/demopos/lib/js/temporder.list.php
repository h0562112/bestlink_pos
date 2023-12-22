<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT TABLENUMBER,SALESTTLQTY,SALESTTLAMT,REMARKS,CUSTCODE,CUSTNAME,TAX5,TAX6,TAX7,TAX8,CUSTGPCODE,CUSTGPNAME,TABLENUMBER,ZCOUNTER FROM tempCST011 WHERE CONSECNUMBER="'.$_POST['consecnumber'].'" AND BIZDATE="'.$_POST['bizdate'].'"';
$data=sqlquery($conn,$sql,'sqlite');
$sql='SELECT * FROM tempCST012 WHERE CONSECNUMBER="'.$_POST['consecnumber'].'" AND BIZDATE="'.$_POST['bizdate'].'" ORDER BY LINENUMBER ASC';
$list=sqlquery($conn,$sql,'sqlite');
$sql='SELECT saleno FROM salemap WHERE consecnumber="'.$_POST['consecnumber'].'"';
$saleno=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT inumber,fronttype,isgroup,childtype FROM itemsdata';
$meno=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
if(strstr($data[0]['TABLENUMBER'],',')){
	$templist=preg_split('/,/',$data[0]['TABLENUMBER']);
	foreach($templist as $tl){
		if(file_exists("../../table/".$_POST['bizdate'].";".$data[0]['ZCOUNTER'].";".$tl.".ini")){
			$tempini=parse_ini_file("../../table/".$_POST['bizdate'].";".$data[0]['ZCOUNTER'].";".$tl.".ini",true);
			$tempini[$tl]['state']='999';
			$tempini[$tl]['machine']=$_POST['machinetype'];
			write_ini_file($tempini,"../../table/".$_POST['bizdate'].";".$data[0]['ZCOUNTER'].";".$tl.".ini");
		}
		else{
		}
	}
}
else{
	if(file_exists("../../table/".$_POST['bizdate'].";".$data[0]['ZCOUNTER'].";".$data[0]['TABLENUMBER'].".ini")){
		$tempini=parse_ini_file("../../table/".$_POST['bizdate'].";".$data[0]['ZCOUNTER'].";".$data[0]['TABLENUMBER'].".ini",true);
		$tempini[$data[0]['TABLENUMBER']]['state']='999';
		$tempini[$data[0]['TABLENUMBER']]['machine']=$_POST['machinetype'];
		write_ini_file($tempini,"../../table/".$_POST['bizdate'].";".$data[0]['ZCOUNTER'].";".$data[0]['TABLENUMBER'].".ini");
	}
	else{
	}
}
if(file_exists('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini')){
	$buttons1=parse_ini_file('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
}
else{
	$buttons1='-1';
}
$menu=parse_ini_file('../../../database/'.$_POST['company'].'-menu.ini',true);
$front=parse_ini_file('../../../database/'.$_POST['company'].'-front.ini',true);
$taste=parse_ini_file('../../../database/'.$_POST['company'].'-taste.ini',true);
$print=parse_ini_file('../../../database/printlisttag.ini',true);
?>
<input type='hidden' name='machinetype' value='<?php if(isset($_POST['machinetype']))echo $_POST['machinetype']; ?>'>
<input type='hidden' name='memno' value='<?php if(preg_match('/;-;/',$data[0]['CUSTCODE'])&&preg_match('/'.$_POST['company'].'/',$data[0]['CUSTCODE'])){$memdata=preg_split('/;-;/',$data[0]['CUSTCODE']);echo $memdata[0];}else echo $data[0]['CUSTCODE']; ?>'>
<input type='hidden' name='memtel' value='<?php if(preg_match('/;-;/',$data[0]['CUSTCODE'])&&preg_match('/'.$_POST['company'].'/',$data[0]['CUSTCODE'])){$memdata=preg_split('/;-;/',$data[0]['CUSTCODE']);if(isset($memdata[1]))echo $memdata[1];else echo '';}else echo ''; ?>'>
<input type='hidden' name='memaddno' value='<?php if(preg_match('/;-;/',$data[0]['CUSTCODE'])&&preg_match('/'.$_POST['company'].'/',$data[0]['CUSTCODE'])){$memdata=preg_split('/;-;/',$data[0]['CUSTCODE']);if(isset($memdata[2]))echo $memdata[2];else echo '1';}else echo '1'; ?>'>
<input type='hidden' name='consecnumber' value='<?php echo $_POST['consecnumber']; ?>'>
<input type='hidden' name='saleno' value='<?php if(isset($saleno[0]['saleno']))echo $saleno[0]['saleno']; ?>'>
<input type='hidden' name='bizdate' value='<?php echo $_POST['bizdate']; ?>'>
<?php
if(preg_match('/-/',$data[0]['REMARKS'])){
	$temp=preg_split('/-/',$data[0]['REMARKS']);
?>
<input type='hidden' name='listtype' value='<?php echo $temp[0]; ?>'>
<input type='hidden' name='typename' value='<?php if($buttons1!='-1')echo $buttons1['name']['listtype'.$temp[0]]; ?>'>
<?php
}
else{
?>
<input type='hidden' name='listtype' value='<?php echo $data[0]['REMARKS']; ?>'>
<input type='hidden' name='typename' value='<?php if($buttons1!='-1')echo $buttons1['name']['listtype'.$data[0]['REMARKS']]; ?>'>
<?php
}
?>
<input type='hidden' name='invsalemoney' value='<?php echo $data[0]['TAX5']; ?>'>
<input type='hidden' name='charge' value=''>
<input type='hidden' name='tempban' value=''>
<input type='hidden' name='tempbuytype' value='<?php echo $print['item']['tempbuytype']; ?>'>
<input type='hidden' name='printclientlist' value='<?php if(isset($print['item']['printclientlist']))echo $print['item']['printclientlist']; ?>'>
<input type='hidden' name='total' value='<?php echo $data[0]['SALESTTLAMT']; ?>'>
<input type='hidden' name='totalnumber' value='<?php echo $data[0]['SALESTTLQTY']; ?>'>
<input type='hidden' name='tablenumber' value='<?php if($data[0]['REMARKS']=='1')echo trim($data[0]['TABLENUMBER']);else echo ''; ?>'>
<input type='hidden' name='usercode' value='<?php echo $_POST['usercode']; ?>'>
<input type='hidden' name='username' value='<?php echo $_POST['username']; ?>'>
<input type='hidden' name='invlist' value='<?php echo $initsetting['init']['invlist']; ?>'>
<input type='hidden' name='person1' value='<?php echo $data[0]['TAX6']; ?>'>
<input type='hidden' name='person2' value='<?php echo $data[0]['TAX7']; ?>'>
<input type='hidden' name='person3' value='<?php echo $data[0]['TAX8']; ?>'>
<input type='hidden' name='mancode' value='<?php if($data[0]['CUSTGPCODE']!='')echo $data[0]['CUSTGPCODE'];else echo ''; ?>'>
<input type='hidden' name='manname' value='<?php if($data[0]['CUSTGPNAME']!='')echo $data[0]['CUSTGPNAME'];else echo ''; ?>'>
<input type='hidden' name='linklist' value='<?php if($data[0]['REMARKS']=='2')echo $data[0]['TABLENUMBER']; ?>'>
<div class='listcontent' style='padding-right:5px;width:calc(100% - 5px);'>
<?php
$itemfront=array();
foreach($meno as $m){
	$itemfront[$m['inumber']]['front']=$m['fronttype'];
	$itemfront[$m['inumber']]['isgroup']=$m['isgroup'];
	$itemfront[$m['inumber']]['childtype']=$m['childtype'];
}
$index=1;
$gritem=0;
for($i=0;$i<sizeof($list);$i=$i+2){
	if($list[$i]['ITEMCODE']=='list'){
		continue;
	}
	else if($list[$i]['ITEMCODE']=='autodis'){
		$i--;
		continue;
	}
	else{
		echo '<div class="label" style="width:100%;border-top:1px solid #dfdfdf;">';
		echo '<input type="hidden" name="templistitem[]">';
		echo '<input type="hidden" name="linenumber[]" value="'.intval($list[$i]['LINENUMBER']).'">';
		if(intval($itemfront[intval($list[$i]['ITEMCODE'])]['isgroup'])>0){
			$gritem=intval($itemfront[intval($list[$i]['ITEMCODE'])]['isgroup']);
			echo '<input type="hidden" name="order[]" value="'.$index.'">';
		}
		else if(intval($itemfront[intval($list[$i]['ITEMCODE'])]['isgroup'])==0&&strstr($itemfront[intval($list[$i]['ITEMCODE'])]['childtype'],'-')){
			if(strstr($itemfront[$m['inumber']]['childtype'],',')){
				$secarray=preg_split('/,/',$itemfront[intval($list[$i]['ITEMCODE'])]['childtype']);
				$gritem=sizeof($secarray);
				echo '<input type="hidden" name="order[]" value="'.$index.'">';
			}
			else{
				$gritem=1;
				echo '<input type="hidden" name="order[]" value="'.$index.'">';
			}
		}
		else{
			if($gritem>0){
				echo '<input type="hidden" name="order[]" value="－">';
			}
			else{
				echo '<input type="hidden" name="order[]" value="'.$index.'">';
			}
			$gritem--;
		}
		echo '<input type="hidden" name="typeno[]" value="'.$itemfront[intval($list[$i]['ITEMCODE'])]['front'].'">';
		echo '<input type="hidden" name="type[]" value="">';
		echo '<input type="hidden" name="no[]" value="'.intval($list[$i]['ITEMCODE']).'">';
		if(isset($menu[intval($list[$i]['ITEMCODE'])]['personcount'])&&$menu[intval($list[$i]['ITEMCODE'])]['personcount']>0){
			echo '<input type="hidden" name="personcount[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['personcount'].'">';
		}
		else{
			echo '<input type="hidden" name="personcount[]" value="0">';
		}
		if(isset($menu[intval($list[$i]['ITEMCODE'])]['charge'])&&$menu[intval($list[$i]['ITEMCODE'])]['charge']!=''){
			echo '<input type="hidden" name="needcharge[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['charge'].'">';
		}
		else{
			echo '<input type="hidden" name="needcharge[]" value="1">';
		}
		echo "<input type='hidden' name='dis1[]' value='".$menu[intval($list[$i]['ITEMCODE'])]['dis1']."'>";
		echo "<input type='hidden' name='dis2[]' value='".$menu[intval($list[$i]['ITEMCODE'])]['dis2']."'>";
		echo "<input type='hidden' name='dis3[]' value='".$menu[intval($list[$i]['ITEMCODE'])]['dis3']."'>";
		echo "<input type='hidden' name='dis4[]' value='".$menu[intval($list[$i]['ITEMCODE'])]['dis4']."'>";
		echo '<input type="hidden" name="name[]" value="'.$list[$i]['ITEMNAME'].'">';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
		echo '<input type="hidden" name="name2[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['name2'].'">';
		echo '<input type="hidden" name="isgroup[]" value="'.$itemfront[intval($list[$i]['ITEMCODE'])]['isgroup'].'">';//判斷點
		echo '<input type="hidden" name="childtype[]" value="">';
		$unitprice=0;
		$mname1='';
		$mname2='';
		if($list[$i]['UNITPRICELINK']==''){
			echo '<input type="hidden" name="mname1[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['mname11'].'">';
			echo '<input type="hidden" name="mname2[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['mname12'].'">';
			if(isset($menu[intval($list[$i]['ITEMCODE'])]['insaleinv'])){
				echo '<input type="hidden" name="insaleinv[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['insaleinv'].'">';
			}
			else{
				echo '<input type="hidden" name="insaleinv[]" value="1">';
			}
			echo '<input type="hidden" name="unitprice[]" value="'.$list[$i]['UNITPRICE'].'">';
			$unitprice=$list[$i]['UNITPRICE'];
			if(isset($menu[intval($list[$i]['ITEMCODE'])]['getpointtype1'])){
				echo '<input type="hidden" name="getpointtype[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['getpointtype1'].'">';
			}
			else{
				echo '<input type="hidden" name="getpointtype[]" value="1">';
			}
			echo '<input type="hidden" name="initgetpoint[]" value="'.intval($list[$i+1]['TAXCODE3']).'">';
			echo '<input type="hidden" name="getpoint[]" value="'.intval($list[$i]['TAXCODE5']).'">';
			$mname1=$menu[intval($list[$i]['ITEMCODE'])]['mname11'];
			$mname2=$menu[intval($list[$i]['ITEMCODE'])]['mname12'];
		}
		else{
			for($j=1;$j<=intval($menu[intval($list[$i]['ITEMCODE'])]['mnumber']);$j++){
				if($list[$i]['UNITPRICELINK']==$menu[intval($list[$i]['ITEMCODE'])]['mname'.$j.'1']){
					echo '<input type="hidden" name="mname1[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['mname'.$j.'1'].'">';
					echo '<input type="hidden" name="mname2[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['mname'.$j.'2'].'">';
					if(isset($menu[intval($list[$i]['ITEMCODE'])]['insaleinv'])){
						echo '<input type="hidden" name="insaleinv[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['insaleinv'].'">';
					}
					else{
						echo '<input type="hidden" name="insaleinv[]" value="1">';
					}
					if($menu[intval($list[$i]['ITEMCODE'])]['money'.$j]==$list[$i]['UNITPRICE']){
						echo '<input type="hidden" name="unitprice[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['money'.$j].'">';
						$unitprice=$menu[intval($list[$i]['ITEMCODE'])]['money'.$j];
					}
					else{
						echo '<input type="hidden" name="unitprice[]" value="'.$list[$i]['UNITPRICE'].'">';
						$unitprice=$list[$i]['UNITPRICE'];
					}
					if(isset($menu[intval($list[$i]['ITEMCODE'])]['getpointtype'.$j])){
						echo '<input type="hidden" name="getpointtype[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['getpointtype'.$j].'">';
					}
					else{
						echo '<input type="hidden" name="getpointtype[]" value="1">';
					}
					echo '<input type="hidden" name="initgetpoint[]" value="'.intval($list[$i+1]['TAXCODE3']).'">';
					echo '<input type="hidden" name="getpoint[]" value="'.intval($list[$i]['TAXCODE5']).'">';
					$mname1=$menu[intval($list[$i]['ITEMCODE'])]['mname'.$j.'1'];
					$mname2=$menu[intval($list[$i]['ITEMCODE'])]['mname'.$j.'2'];
					break;
				}
				else{
					continue;
				}
			}
			if($j>intval($menu[intval($list[$i]['ITEMCODE'])]['mnumber'])){
				echo '<input type="hidden" name="mname1[]" value="">';
				echo '<input type="hidden" name="mname2[]" value="">';
				echo '<input type="hidden" name="insaleinv[]" value="1">';
				echo '<input type="hidden" name="unitprice[]" value="'.$list[$i]['UNITPRICE'].'">';
				$unitprice=$list[$i]['UNITPRICE'];
				echo '<input type="hidden" name="getpointtype[]" value="1">';
				echo '<input type="hidden" name="getpoint[]" value="0">';
				$mname1='';
				$mname2='';
			}
			else{
			}
		}
		$taste1='';
		$taste1name='';
		$taste1price='';
		$taste1number='';
		$taste1money='0';
		$money=$unitprice;
		if($list[$i]['SELECTIVEITEM1']==null){
			echo '<input type="hidden" name="taste1[]" value="">';
			echo '<input type="hidden" name="taste1name[]" value="">';
			echo '<input type="hidden" name="taste1price[]" value="">';
			echo '<input type="hidden" name="taste1number[]" value="">';
			echo '<input type="hidden" name="taste1money[]" value="0">';
			echo '<input type="hidden" name="money[]" value="'.$money.'">';
		}
		else{
			for($j=1;$j<=10;$j++){
				if($list[$i]['SELECTIVEITEM'.$j]==null){
					break;
				}
				else{
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$j]);
					for($k=0;$k<sizeof($temptaste);$k++){
						if(preg_match('/99999/',$temptaste[$k])){//手打備註
							if($taste1==''&&strlen($taste1)==0){
								$taste1='99999';
								$taste1name=substr($temptaste[$k],7);
								$taste1price='0';
								$taste1number='1';
								$taste1money='0';
							}
							else{
								$taste1=$taste1.',99999';
								$taste1name=$taste1name.','.substr($temptaste[$k],7);
								$taste1price=$taste1price.',0';
								$taste1number=$taste1number.',1';
								//$taste1money=$taste1money.',0';//2020/12/10 該值為金額，而不是字串
								$taste1money=floatval($taste1money)+floatval(0);
							}
						}
						else{
							if($taste1==''&&strlen($taste1)==0){
								$taste1=intval(substr($temptaste[$k],0,5));
								$taste1name=$taste[intval(substr($temptaste[$k],0,5))]['name1'];
								if(intval(substr($temptaste[$k],5,1))==1){
								}
								else{
									$taste1name=$taste1name.'*'.intval(substr($temptaste[$k],5,1));
								}
								$taste1price=$taste[floatval(substr($temptaste[$k],0,5))]['money'];
								$taste1number=intval(substr($temptaste[$k],5,1));
								$taste1money=(floatval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1)));
							}
							else{
								$taste1=$taste1.','.intval(substr($temptaste[$k],0,5));
								$taste1name=$taste1name.','.$taste[intval(substr($temptaste[$k],0,5))]['name1'];
								if(intval(substr($temptaste[$k],5,1))==1){
								}
								else{
									$taste1name=$taste1name.'*'.intval(substr($temptaste[$k],5,1));
								}
								$taste1price=$taste1price.','.$taste[intval(substr($temptaste[$k],0,5))]['money'];
								$taste1number=$taste1number.','.intval(substr($temptaste[$k],5,1));
								//$taste1money=$taste1money.','.(intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1)));//2020/12/10 該值為金額，而不是字串
								$taste1money=floatval($taste1money)+floatval((intval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1))));
							}
							$money=floatval($money)+(floatval($taste[intval(substr($temptaste[$k],0,5))]['money'])*intval(substr($temptaste[$k],5,1)));
						}
					}
				}
				/*else if(preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$j])){//手打備註
					if($taste1==''&&strlen($taste1)==0){
						$taste1='99999';
						$taste1name=substr($list[$i]['SELECTIVEITEM'.$j],7);
						$taste1price='0';
						$taste1number='1';
						$taste1money='0';
					}
					else{
						$taste1=$taste1.',99999';
						$taste1name=$taste1name.','.substr($list[$i]['SELECTIVEITEM'.$j],7);
						$taste1price=$taste1price.',0';
						$taste1number=$taste1number.',1';
						//$taste1money=$taste1money.',0';//2020/12/10 該值為金額，而不是字串
						$taste1money=floatval($taste1money)+floatval(0);
					}
				}
				else{
					if($taste1==''&&strlen($taste1)==0){
						$taste1=intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5));
						$taste1name=$taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['name1'];
						if(intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1))==1){
						}
						else{
							$taste1name=$taste1name.'*'.intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1));
						}
						$taste1price=$taste[floatval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['money'];
						$taste1number=intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1));
						$taste1money=(floatval($taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['money'])*intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1)));
					}
					else{
						$taste1=$taste1.','.intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5));
						$taste1name=$taste1name.','.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['name1'];
						if(intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1))==1){
						}
						else{
							$taste1name=$taste1name.'*'.intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1));
						}
						$taste1price=$taste1price.','.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['money'];
						$taste1number=$taste1number.','.intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1));
						//$taste1money=$taste1money.','.(intval($taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['money'])*intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1)));//2020/12/10 該值為金額，而不是字串
						$taste1money=floatval($taste1money)+floatval((intval($taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['money'])*intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1))));
					}
					$money=floatval($money)+(floatval($taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['money'])*intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1)));
				}*/
			}
			echo '<input type="hidden" name="taste1[]" value="'.$taste1.'">';
			echo '<input type="hidden" name="taste1name[]" value="'.$taste1name.'">';
			echo '<input type="hidden" name="taste1price[]" value="'.$taste1price.'">';
			echo '<input type="hidden" name="taste1number[]" value="'.$taste1number.'">';
			echo '<input type="hidden" name="taste1money[]" value="'.$taste1money.'">';
			echo '<input type="hidden" name="money[]" value="'.$money.'">';
		}
		$discount=0;
		if(isset($list[$i+1]['ITEMCODE'])&&$list[$i+1]['ITEMCODE']=='item'){
			echo '<input type="hidden" name="discount[]" value="'.(0-floatval($list[$i+1]['AMT'])).'">';
			$discount=$list[$i+1]['AMT'];
			echo '<input type="hidden" name="discontent[]" value="'.$list[$i+1]['ITEMGRPCODE'].'">';
			echo '<input type="hidden" name="dispoint[]" value="'.(-intval($list[$i+1]['TAXCODE5'])).'">';
			echo '<input type="hidden" name="dispointtime[]" value="'.intval($list[$i+1]['TAXCODE2']).'">';
		}
		else{
			echo '<input type="hidden" name="discount[]" value="0">';
			echo '<input type="hidden" name="discontent[]" value="">';
			echo '<input type="hidden" name="dispoint[]" value="0">';
			echo '<input type="hidden" name="dispointtime[]" value="0">';
		}
		echo '<input type="hidden" name="number[]" value="'.$list[$i]['QTY'].'">';
		echo '<input type="hidden" name="subtotal[]" value="'.((floatval($list[$i]['QTY'])*floatval($money))+floatval($discount)).'">';
		if(isset($menu[intval($list[$i]['ITEMCODE'])]['itemdis'])){
			echo '<input type="hidden" name="itemdis[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['itemdis'].'">';
		}
		else{
			echo '<input type="hidden" name="itemdis[]" value="1">';
		}
		if(isset($menu[intval($list[$i]['ITEMCODE'])]['listdis'])){
			echo '<input type="hidden" name="listdis[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['listdis'].'">';
		}
		else{
			echo '<input type="hidden" name="listdis[]" value="1">';
		}
		if(isset($menu[intval($list[$i]['ITEMCODE'])]['mempoint'])){
			echo '<input type="hidden" name="usemempoint[]" value="'.$menu[intval($list[$i]['ITEMCODE'])]['mempoint'].'">';
		}
		else{
			echo '<input type="hidden" name="usemempoint[]" value="1">';
		}
		echo '<div style="width:3%;padding:3px 0;"><input type="checkbox" data-id="checkbox[]"></div>';
		echo '<div style="width:10%;padding:3px 0 3px 3px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">'.$index.'</div>';
		if($list[$i]['SELECTIVEITEM1']==null){
			if(strlen($menu[intval($list[$i]['ITEMCODE'])]['name2'])==0){
				if(intval($menu[intval($list[$i]['ITEMCODE'])]['mnumber'])>1){
					if(strlen($mname1)==0&&strlen($mname2)==0){
						echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].'</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
					}
					else if(strlen($mname2)==0){
						echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].'('.$mname1.')</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
					}
					else{
						echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].'('.$mname1.' /'.$mname2.')</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
					}
				}
				else{
					echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].'</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
				}
			}
			else{
				echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].' /'.$menu[intval($list[$i]['ITEMCODE'])]['name2'].'</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
			}
		}
		else{
			$tastelist='';
			for($j=1;$j<=10;$j++){
				if($list[$i]['SELECTIVEITEM'.$j]==null){
					break;
				}
				else{
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					$temptaste=preg_split('/,/',$list[$i]['SELECTIVEITEM'.$j]);
					for($k=0;$k<sizeof($temptaste);$k++){
						if(preg_match('/99999/',$temptaste[$k])){//手打備註
							if($taste1==''){
								$tastelist='<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.substr($temptaste[$k],7);
							}
							else{
								$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.substr($temptaste[$k],7);
							}
						}
						else{
							if($taste1==''){
								$tastelist='<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$taste[intval(substr($temptaste[$k],0,5))]['name1'];
							}
							else{
								$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$taste[intval(substr($temptaste[$k],0,5))]['name1'];
							}
							if(intval(substr($temptaste[$k],5,1))>1){
								$tastelist=$tastelist.'*'.intval(substr($temptaste[$k],5,1));
							}
							else{
							}
						}
					}
				}
				/*else if(preg_match('/99999/',$list[$i]['SELECTIVEITEM'.$j])){//手打備註
					if($taste1==''){
						$tastelist='<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.substr($list[$i]['SELECTIVEITEM'.$j],7);
					}
					else{
						$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.substr($list[$i]['SELECTIVEITEM'.$j],7);
					}
				}
				else{
					if($taste1==''){
						$tastelist='<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['name1'];
					}
					else{
						$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$taste[intval(substr($list[$i]['SELECTIVEITEM'.$j],0,5))]['name1'];
					}
					if(intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1))>1){
						$tastelist=$tastelist.'*'.intval(substr($list[$i]['SELECTIVEITEM'.$j],5,1));
					}
					else{
					}
				}*/
			}
			if(strlen($menu[intval($list[$i]['ITEMCODE'])]['name2'])==0){
				if(intval($menu[intval($list[$i]['ITEMCODE'])]['mnumber'])>1){
					if(strlen($mname1)==0&&strlen($mname2)==0){
						echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].$tastelist.'</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
					}
					else if(strlen($mname2)==0){
						echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].'('.$mname1.')'.$tastelist.'</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
					}
					else{
						echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].'('.$mname1.' /'.$mname2.')'.$tastelist.'</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
					}
				}
				else{
					echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].$tastelist.'</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
				}
			}
			else{
				echo '<div style="width:calc(57%);padding:3px 0;" id="view">'.$list[$i]['ITEMNAME'].' /'.$menu[intval($list[$i]['ITEMCODE'])]['name2'].$tastelist.'</div>';//2022/1/10 名稱從菜單設定中的name1 ($menu[intval($list[$i]['ITEMCODE'])]['name1']) 改為CST012中的ITEMCODE ($list[$i]['ITEMNAME'])
			}
			
		}
		echo '<div style="width:10%;text-align:right;padding:3px 0;" id="unitprice">'.$list[$i]['UNITPRICE'].'</div>';
		echo '<div style="width:10%;text-align:center;padding:3px 0;" id="number">'.$list[$i]['QTY'].'</div>';
		echo '<div style="width:10%;text-align:right;padding:3px 0;" id="subtotal">'.((floatval($list[$i]['QTY'])*floatval($money))+floatval($discount)).'</div>';
		echo '</div>';
		$index++;
	}
}
if(file_exists('../../../database/sale/temp'.$_POST['machinetype'].'.db')){
	$tempdbname='temp'.$_POST['machinetype'];
}
else{
	$tempdbname='temp';
}
if($initsetting['init']['secview']=='1'&&file_exists('../../../database/sale/'.$tempdbname.'.db')){
	$file=fopen('./temp.txt','a');
	$conn=sqlconnect('../../../database/sale',$tempdbname.'.db','','','','sqlite');
	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
	if(file_exists('d://xampp/htdocs/outposandorder/database/sale/SALES_'.substr($_POST['bizdate'],0,6).'.db')){
		$r=$conn->exec("ATTACH 'd://xampp/htdocs/outposandorder/database/sale/SALES_".substr($_POST['bizdate'],0,6).".db' AS listdb");
	}
	else{
		$r=$conn->exec("ATTACH '".$initsetting['db']['dbfile']."SALES_".substr($_POST['bizdate'],0,6).".db' AS listdb");
	}
	fwrite($file,$r.PHP_EOL);
	$conn->exec('BEGIN');
	$sql='DELETE FROM list WHERE TERMINALNUMBER="'.$_POST['machinetype'].'";DELETE FROM ban WHERE TERMINALNUMBER="'.$_POST['machinetype'].'";INSERT INTO list SELECT * FROM listdb.tempCST012 WHERE listdb.tempCST012.CONSECNUMBER="'.$_POST['consecnumber'].'" AND listdb.tempCST012.BIZDATE="'.$_POST['bizdate'].'" ORDER BY listdb.tempCST012.LINENUMBER ASC;UPDATE list SET TERMINALNUMBER="'.$_POST['machinetype'].'" WHERE CONSECNUMBER="'.$_POST['consecnumber'].'" AND BIZDATE="'.$_POST['bizdate'].'";';
	//echo $sql;
	
	$l=$conn->exec($sql);
	$conn->exec('COMMIT');
	//$file=fopen('./temp.txt','a');
	/*foreach($l as $temp){
		fwrite($file,$temp['ITEMNAME'].PHP_EOL);
	}*/
	
	sqlclose($conn,'sqlite');
	/*$bulkload_connection = new SQLite3("../../../database/sale/SALES_".substr($_POST['bizdate'],0,6).".db"); 

	//retrieve the create statement query for the source table; 
	$sourcetbl_create_statement = $bulkload_connection->querySingle("select sql from sqlite_master where type='table' and name='tempCST012'"); 
	if ($sourcetbl_create_statement===false) {exit($bulkload_connection->lastErrorMsg()); fwrite($file,$bulkload_connection->lastErrorMsg().PHP_EOL);}

	//build the create statement query for the target table; 
	//$targettbl_create_statement = str_replace('CREATE TABLE tempCST012', 'CREATE TABLE temp.db.list', $sourcetbl_create_statement); 

	//attach the target database file to the bulkload connection object - and reference it as the database called [bulkload]; 
	$result=$bulkload_connection->exec("attach 'd://xampp/htdocs/outposandorder/database/sale/temp.db' as bulkload"); 
	if ($result===false) exit($bulkload_connection->lastErrorMsg()); 

	//issue the query to create the target table within the target database file; 
	//$result=$bulkload_connection->exec($targettbl_create_statement); 
	//if ($result===false) exit($bulkload_connection->lastErrorMsg()); 

	//copy the rows from the source table to the target table as quickly as possible; 
	$result=$bulkload_connection->exec("insert into bulkload.list select * from tempCST012"); 
	if ($result===false) exit($bulkload_connection->lastErrorMsg()); 

	//release the OS file locks on the attached database files; 
	$bulkload_connection->close(); 
	unset($bulkload_connection); */
	fclose($file);
}
else{
}
?>
</div>