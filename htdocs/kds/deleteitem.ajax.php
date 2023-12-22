<?php
if(file_exists('./items/kdsgroup-'.$_POST['kdsgroup'].'.ini')){
	$kdsgroup=parse_ini_file('./items/kdsgroup-'.$_POST['kdsgroup'].'.ini',true);
	if($_POST['kdsgroup']=='service'){//服務鈴
		include_once '../tool/inilib.php';
		if(isset($kdsgroup[$_POST['tablenumber']])){
			unset($kdsgroup[$_POST['tablenumber']]);
		}
		else{
		}
		write_ini_file($kdsgroup,'./items/kdsgroup-'.$_POST['kdsgroup'].'.ini');
		if(file_exists('../demoposorder/serviceitems/'.$_POST['tablenumber'].'.ini')){
			unlink('../demoposorder/serviceitems/'.$_POST['tablenumber'].'.ini');
		}
		else{
		}
	}
	else if($_POST['label']=='lists'){//以單控餐
		include_once '../tool/inilib.php';
		$listindex=$_POST['consecnumber'];
		$itemindex=$_POST['linenumber'];
		$itemnumber=$_POST['number'];//2020/7/2 ordertag

		echo $listindex.'<br>'.$itemindex.'<br>'.$itemnumber;
		
		$file=parse_ini_file('./items/kdsgroup-'.$_POST['kdsgroup'].'.ini',true);

		foreach($file as $consecnumber=>$data){
			if($consecnumber!=$listindex){//2020/7/2 非目標單據
				if(isset($data['ordertag'])){
				}
				else{
					for($i=0;$i<sizeof($data['itemno']);$i++){
						$file[$consecnumber]['ordertag'][$i]='1';
					}
				}
			}
			else{//2020/7/2 目標單據
				if(isset($data['ordertag'])){
					if($itemnumber==0){//2020/7/2 已出餐狀態修改為未出餐
						$file[$consecnumber]['ordertag'][$itemindex]='1';
					}
					else{
						$ordertag[$consecnumber]=0;
						$file[$consecnumber]['ordertag'][$itemindex]='0';
						for($i=0;$i<sizeof($data['ordertag']);$i++){
							$ordertag[$consecnumber]=intval($ordertag[$consecnumber])+intval($file[$consecnumber]['ordertag'][$i]);
						}
						if($ordertag[$consecnumber]==0){//2020/7/2 目標單據中所有品項皆出餐，將目標單據刪除
							$delete=$consecnumber;
						}
						else{
						}
					}
				}
				else{
					if(sizeof($data['itemno'])>1){
						for($i=0;$i<sizeof($data['itemno']);$i++){
							if($i!=$itemindex){//2020/7/2 非目標品項
								$file[$consecnumber]['ordertag'][$i]='1';
							}
							else{//2020/7/2 目標品項
								$file[$consecnumber]['ordertag'][$i]='0';
							}
						}
					}
					else{//2020/7/2 目標單據品項只有一項，直接刪除
						$delete=$consecnumber;
					}
				}
			}
		}

		if(isset($delete)){
			unset($file[$delete]);
		}
		else{
		}

		write_ini_file($file,'./items/kdsgroup-'.$_POST['kdsgroup'].'.ini');
	}
	else{//$_POST['label']=='items'//以餐控單
		$listindex=preg_split('/js;js/',$_POST['consecnumber']);
		$itemindex=preg_split('/js;js/',$_POST['linenumber']);
		$itemnumber=preg_split('/js;js/',$_POST['number']);
		if($_POST['type']=='one'){//產品數量遞減
			$file=fopen('./items/kdsgroup-'.$_POST['kdsgroup'].'.ini','w');
			fwrite($file,'[basic]'.PHP_EOL);
			$delete=0;
			foreach($kdsgroup as $i=>$v){
				$index=0;
				if(isset($v['itemno'])){
					if($i!=$listindex[0]){
						fwrite($file,'['.$i.']'.PHP_EOL);
						for($ii=0;$ii<sizeof($v['itemno']);$ii++){
							fwrite($file,'itemno[]='.$v['itemno'][$ii].PHP_EOL);
							fwrite($file,'itemname[]='.$v['itemname'][$ii].PHP_EOL);
							fwrite($file,'taste1[]='.$v['taste1'][$ii].PHP_EOL);
							fwrite($file,'taste1name[]='.$v['taste1name'][$ii].PHP_EOL);
							fwrite($file,'moneyname[]='.$v['moneyname'][$ii].PHP_EOL);
							fwrite($file,'number[]='.$v['number'][$ii].PHP_EOL);
							fwrite($file,'kdsno[]='.$v['kdsno'][$ii].PHP_EOL);
							fwrite($file,'kdsgroup[]='.$v['kdsgroup'][$ii].PHP_EOL);
						}
					}
					else{
						for($ii=0;$ii<sizeof($v['itemno']);$ii++){
							if($ii==$itemindex[0]&&$delete==0){
								if($v['number'][$ii]==1){//刪除產品
									$delete++;
								}
								else{//數量遞減
									$delete++;
									if($index==0){
										fwrite($file,'['.$i.']'.PHP_EOL);
										$index++;
									}
									else{
									}
									fwrite($file,'itemno[]='.$v['itemno'][$ii].PHP_EOL);
									fwrite($file,'itemname[]='.$v['itemname'][$ii].PHP_EOL);
									fwrite($file,'taste1[]='.$v['taste1'][$ii].PHP_EOL);
									fwrite($file,'taste1name[]='.$v['taste1name'][$ii].PHP_EOL);
									fwrite($file,'moneyname[]='.$v['moneyname'][$ii].PHP_EOL);
									fwrite($file,'number[]='.(intval($v['number'][$ii])-1).PHP_EOL);
									fwrite($file,'kdsno[]='.$v['kdsno'][$ii].PHP_EOL);
									fwrite($file,'kdsgroup[]='.$v['kdsgroup'][$ii].PHP_EOL);
								}
							}
							else{
								if($index==0){
									fwrite($file,'['.$i.']'.PHP_EOL);
									$index++;
								}
								else{
								}
								fwrite($file,'itemno[]='.$v['itemno'][$ii].PHP_EOL);
								fwrite($file,'itemname[]='.$v['itemname'][$ii].PHP_EOL);
								fwrite($file,'taste1[]='.$v['taste1'][$ii].PHP_EOL);
								fwrite($file,'taste1name[]='.$v['taste1name'][$ii].PHP_EOL);
								fwrite($file,'moneyname[]='.$v['moneyname'][$ii].PHP_EOL);
								fwrite($file,'number[]='.$v['number'][$ii].PHP_EOL);
								fwrite($file,'kdsno[]='.$v['kdsno'][$ii].PHP_EOL);
								fwrite($file,'kdsgroup[]='.$v['kdsgroup'][$ii].PHP_EOL);
							}
						}
					}
				}
				else{
				}
			}
			fclose($file);
		}
		else{
			$file=fopen('./items/kdsgroup-'.$_POST['kdsgroup'].'.ini','w');
			fwrite($file,'[basic]'.PHP_EOL);
			$delete=0;
			foreach($kdsgroup as $i=>$v){
				if(isset($v['itemno'])){
					if(!isset($listindex[$delete])||$i!=$listindex[$delete]){
						fwrite($file,'['.$i.']'.PHP_EOL);
						for($ii=0;$ii<sizeof($v['itemno']);$ii++){
							fwrite($file,'itemno[]='.$v['itemno'][$ii].PHP_EOL);
							fwrite($file,'itemname[]='.$v['itemname'][$ii].PHP_EOL);
							fwrite($file,'taste1[]='.$v['taste1'][$ii].PHP_EOL);
							fwrite($file,'taste1name[]='.$v['taste1name'][$ii].PHP_EOL);
							fwrite($file,'moneyname[]='.$v['moneyname'][$ii].PHP_EOL);
							fwrite($file,'number[]='.$v['number'][$ii].PHP_EOL);
							fwrite($file,'kdsno[]='.$v['kdsno'][$ii].PHP_EOL);
							fwrite($file,'kdsgroup[]='.$v['kdsgroup'][$ii].PHP_EOL);
						}
					}
					else if(isset($listindex[$delete])){
						$sessiondata='';
						for($ii=0;$ii<sizeof($v['itemno']);$ii++){
							if(isset($itemindex[$delete])&&$ii==$itemindex[$delete]){
								if($itemnumber[$delete]==$v['number'][$ii]){//刪除產品
								}
								else{//刪除N數量
									$sessiondata .= 'itemno[]='.$v['itemno'][$ii].PHP_EOL;
									$sessiondata .= 'itemname[]='.$v['itemname'][$ii].PHP_EOL;
									$sessiondata .= 'taste1[]='.$v['taste1'][$ii].PHP_EOL;
									$sessiondata .= 'taste1name[]='.$v['taste1name'][$ii].PHP_EOL;
									$sessiondata .= 'moneyname[]='.$v['moneyname'][$ii].PHP_EOL;
									$sessiondata .= 'number[]='.(intval($v['number'][$ii])-intval($itemnumber[$delete])).PHP_EOL;
									$sessiondata .= 'kdsno[]='.$v['kdsno'][$ii].PHP_EOL;
									$sessiondata .= 'kdsgroup[]='.$v['kdsgroup'][$ii].PHP_EOL;
								}
								$delete++;
							}
							else{
								$sessiondata .= 'itemno[]='.$v['itemno'][$ii].PHP_EOL;
								$sessiondata .= 'itemname[]='.$v['itemname'][$ii].PHP_EOL;
								$sessiondata .= 'taste1[]='.$v['taste1'][$ii].PHP_EOL;
								$sessiondata .= 'taste1name[]='.$v['taste1name'][$ii].PHP_EOL;
								$sessiondata .= 'moneyname[]='.$v['moneyname'][$ii].PHP_EOL;
								$sessiondata .= 'number[]='.$v['number'][$ii].PHP_EOL;
								$sessiondata .= 'kdsno[]='.$v['kdsno'][$ii].PHP_EOL;
								$sessiondata .= 'kdsgroup[]='.$v['kdsgroup'][$ii].PHP_EOL;
							}
						}
						if($sessiondata!=''){
							fwrite($file,'['.$i.']'.PHP_EOL.$sessiondata);
						}
						else{
							//echo $sessiondata;
						}
					}
					else{
					}
				}
				else{
				}
			}
			fclose($file);
		}
	}
}
else{
}
?>