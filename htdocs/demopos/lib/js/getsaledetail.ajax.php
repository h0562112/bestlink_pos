<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$taste=parse_ini_file('../../../database/'.$_POST['company'].'-taste.ini',true);
$menu=parse_ini_file('../../../database/'.$_POST['company'].'-menu.ini',true);
$front=parse_ini_file('../../../database/'.$_POST['company'].'-front.ini',true);
$init=parse_ini_file('../../../database/initsetting.ini',true);
$bizdate=$_POST['bizdate'];
$no=$_POST['no'];
$conn=sqlconnect('../../../database/sale','SALES_'.substr($bizdate,0,6).'.db','','','','sqlite');
if(isset($_POST['temp'])&&$_POST['temp']=='temp'){
	$sql='SELECT * FROM tempCST012 WHERE ((DTLMODE="1" AND DTLTYPE="1" AND DTLFUNC="01") OR (DTLMODE="1" AND DTLTYPE="3" AND DTLFUNC="02" AND ITEMCODE="item")) AND BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.trim($no).'" ORDER BY LINENUMBER';
}
else{
	$sql='SELECT * FROM CST012 WHERE ((DTLMODE="1" AND DTLTYPE="1" AND DTLFUNC="01") OR (DTLMODE="1" AND DTLTYPE="3" AND DTLFUNC="02" AND ITEMCODE="item")) AND BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$no.'" ORDER BY LINENUMBER';
	$sql2='SELECT INVOICENUMBER FROM CST011 WHERE BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.$no.'"';
}
$detail=sqlquery($conn,$sql,'sqlite');
if(isset($_POST['temp'])&&$_POST['temp']=='kvm'){
	if(isset($detail[0]['BIZDATE'])){
	}
	else{
		$sql='SELECT * FROM tempCST012 WHERE ((DTLMODE="1" AND DTLTYPE="1" AND DTLFUNC="01") OR (DTLMODE="1" AND DTLTYPE="3" AND DTLFUNC="02" AND ITEMCODE="item")) AND BIZDATE="'.$bizdate.'" AND CONSECNUMBER="'.trim($no).'" ORDER BY LINENUMBER';
		$detail=sqlquery($conn,$sql,'sqlite');
	}
}
else{
}
if(isset($sql2)){
	$inv=sqlquery($conn,$sql2,'sqlite');
}
else{
	$inv='';
}
sqlclose($conn,'sqlite');
if(!isset($detail)||sizeof($detail)==0){
}
else{
	$index=0;
	$html='<input type="hidden" name="listtype" value="'.$detail[0]['REMARKS'].'"><input type="hidden" name="consecnumber" value="'.$detail[0]['CONSECNUMBER'].'"><input type="hidden" name="invnumber" value="';if(!isset($inv[0]['INVOICENUMBER'])||$inv=='');else $html=$html.$inv[0]['INVOICENUMBER'];$html=$html.'">';
	for($item=0;$item<sizeof($detail);$item=$item+2){
	//foreach($detail as $d){
		$index++;
		if(isset($_POST['temp'])&&$_POST['temp']=='kvm'){
			$html=$html."<div class='label check' style='width:max-content;border-bottom:1px solid #dfdfdf;overflow:hidden;'><input type='hidden' name='inumber' value='".intval($detail[$item]['ITEMCODE'])."'><input type='hidden' name='linenumber' value='".$detail[$item]['LINENUMBER']."'><input type='hidden' name='order' value='";
			if(isset($front[intval($detail[$item]['ITEMGRPCODE'])]['subtype'])&&$front[intval($detail[$item]['ITEMGRPCODE'])]['subtype']==1){
				$html=$html.'－';//2021/7/29 套餐品項
			}
			else{
				$html=$html.'';
			}
			$html=$html."'><div style='width:16px;padding:3px 0;float:left;position: relative;'><p style=' position: absolute; width: 100%; height: 100%; margin:0;'></p><input type='checkbox' data-id='checkbox[]' readonly checked></div><div style='width:55px;padding:3px 0 3px 3px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;'>".$index."</div><div style='width:313px;padding:3px 0;float:left;' id='view'>";
			$name='';
			$name .= $detail[$item]['ITEMNAME'];
			//2021/10/22 foodpanda產品名稱可能會與設定檔不合(麻辣湯-小辣；名稱-備註)
			/*if(strlen($menu[intval($detail[$item]['ITEMCODE'])]['name1'])>0){
				if(strlen($name)==0){
					$name=$name.$menu[intval($detail[$item]['ITEMCODE'])]['name1'];
				}
				else{
					$name=$name.' /'.$menu[intval($detail[$item]['ITEMCODE'])]['name1'];
				}
			}
			else{
			}*/
			if(strlen($menu[intval($detail[$item]['ITEMCODE'])]['name2'])>0){
				if(strlen($name)==0){
					$name=$name.$menu[intval($detail[$item]['ITEMCODE'])]['name2'];
				}
				else{
					$name=$name.' /'.$menu[intval($detail[$item]['ITEMCODE'])]['name2'];
				}
			}
			else{
			}
			$html=$html.$name;
			for($i=1;$i<=$menu[intval($detail[$item]['ITEMCODE'])]['mnumber'];$i++){
				if($menu[intval($detail[$item]['ITEMCODE'])]['money'.$i]==$detail[$item]['UNITPRICE']&&$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'1']==$detail[$item]['UNITPRICELINK']){
					$mname='';
					if(strlen($menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'1'])>0){
						if(strlen($mname)==0){
							$mname=$mname.'('.$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'1'];
						}
						else{
							$mname=$mname.'/'.$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'1'];
						}
					}
					else{
					}
					if(strlen($menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'2'])>0){
						if(strlen($mname)==0){
							$mname=$mname.'('.$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'2'];
						}
						else{
							$mname=$mname.'/'.$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'2'];
						}
					}
					else{
					}
					if(strlen($mname)>0){
						$html=$html.$mname.')';
					}
					else{
					}
					break;
				}
				else{
				}
			}
			$tastelist='';
			for($i=1;$i<=10;$i++){
				if(strlen($detail[$item]['SELECTIVEITEM'.$i])==0){
					break;
				}
				else{
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					$temptaste=preg_split('/,/',$detail[$item]['SELECTIVEITEM'.$i]);
					for($j=0;$j<sizeof($temptaste);$j++){
						if(preg_match('/99999/',$temptaste[$j])){//手打備註
							$tastename=substr($temptaste[$j],7);
							$no='99999';
							$number='1';
							
							$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+註:'.$tastename;
						}
						else{
							$tastename='';
							$no=(int)intval($temptaste[$j])/10;
							$number=intval($temptaste[$j])%10;
							if(strlen($taste[$no]['name1'])>0){
								if(strlen($tastename)==0){
									$tastename=$tastename.$taste[$no]['name1'];
								}
								else{
									$tastename=$tastename.' /'.$taste[$no]['name1'];
								}
							}
							else{
							}
							if(strlen($taste[$no]['name2'])>0){
								if(strlen($tastename)==0){
									$tastename=$tastename.$taste[$no]['name2'];
								}
								else{
									$tastename=$tastename.' /'.$taste[$no]['name2'];
								}
							}
							else{
							}
							if($number>1){
								$tastename=$tastename.'*'.$number;
							}
							else{
							}
							$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$tastename;
						}
					}
				}
				/*else if(preg_match('/99999/',$detail[$item]['SELECTIVEITEM'.$i])){//手打備註
					$tastename=substr($detail[$item]['SELECTIVEITEM'.$i],7);
					$no='99999';
					$number='1';
					
					$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$tastename;
				}
				else{
					$tastename='';
					$no=(int)intval($detail[$item]['SELECTIVEITEM'.$i])/10;
					$number=intval($detail[$item]['SELECTIVEITEM'.$i])%10;
					if(strlen($taste[$no]['name1'])>0){
						if(strlen($tastename)==0){
							$tastename=$tastename.$taste[$no]['name1'];
						}
						else{
							$tastename=$tastename.' /'.$taste[$no]['name1'];
						}
					}
					else{
					}
					if(strlen($taste[$no]['name2'])>0){
						if(strlen($tastename)==0){
							$tastename=$tastename.$taste[$no]['name2'];
						}
						else{
							$tastename=$tastename.' /'.$taste[$no]['name2'];
						}
					}
					else{
					}
					if($number>1){
						$tastename=$tastename.$taste[$no]['name2'].'*'.$number;
					}
					else{
					}
					$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$tastename;
				}*/
			}
			$html=$html.$tastelist."</div><div style='width:20%;text-align:center;padding:3px 0;float:left;' id='number'>".$detail[$item]['QTY']."</div></div>";
		}
		else{
			$html=$html."<div class='label check' style='width:max-content;border-bottom:1px solid #dfdfdf;overflow:hidden;'><input type='hidden' name='inumber' value='".intval($detail[$item]['ITEMCODE'])."'><input type='hidden' name='linenumber' value='".$detail[$item]['LINENUMBER']."'><input type='hidden' name='order' value='";
			if(isset($front[intval($detail[$item]['ITEMGRPCODE'])]['subtype'])&&$front[intval($detail[$item]['ITEMGRPCODE'])]['subtype']==1){
				$html=$html.'－';//2021/7/29 套餐品項
			}
			else{
				$html=$html.'';
			}
			$html=$html."'><div style='width:16px;padding:3px 0;float:left;position: relative;'><p style=' position: absolute; width: 100%; height: 100%; margin:0;'></p><input type='checkbox' data-id='checkbox[]' readonly checked></div><div style='width:55px;padding:3px 0 3px 3px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;'>".$index."</div><div style='width:313px;padding:3px 0;float:left;' id='view'>";
			$name='';
			$name .= $detail[$item]['ITEMNAME'];
			//2021/10/22 foodpanda產品名稱可能會與設定檔不合(麻辣湯-小辣；名稱-備註)
			/*if(strlen($menu[intval($detail[$item]['ITEMCODE'])]['name1'])>0){
				if(strlen($name)==0){
					$name=$name.$menu[intval($detail[$item]['ITEMCODE'])]['name1'];
				}
				else{
					$name=$name.' /'.$menu[intval($detail[$item]['ITEMCODE'])]['name1'];
				}
			}
			else{
			}*/
			if(strlen($menu[intval($detail[$item]['ITEMCODE'])]['name2'])>0){
				if(strlen($name)==0){
					$name=$name.$menu[intval($detail[$item]['ITEMCODE'])]['name2'];
				}
				else{
					$name=$name.' /'.$menu[intval($detail[$item]['ITEMCODE'])]['name2'];
				}
			}
			else{
			}
			$html=$html.$name;
			for($i=1;$i<=$menu[intval($detail[$item]['ITEMCODE'])]['mnumber'];$i++){
				if($menu[intval($detail[$item]['ITEMCODE'])]['money'.$i]==$detail[$item]['UNITPRICE']&&$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'1']==$detail[$item]['UNITPRICELINK']){
					$mname='';
					if(strlen($menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'1'])>0){
						if(strlen($mname)==0){
							$mname=$mname.'('.$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'1'];
						}
						else{
							$mname=$mname.'/'.$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'1'];
						}
					}
					else{
					}
					if(strlen($menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'2'])>0){
						if(strlen($mname)==0){
							$mname=$mname.'('.$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'2'];
						}
						else{
							$mname=$mname.'/'.$menu[intval($detail[$item]['ITEMCODE'])]['mname'.$i.'2'];
						}
					}
					else{
					}
					if(strlen($mname)>0){
						$html=$html.$mname.')';
					}
					else{
					}
					break;
				}
				else{
				}
			}
			$tastelist='';
			for($i=1;$i<=10;$i++){
				if(strlen($detail[$item]['SELECTIVEITEM'.$i])==0){
					break;
				}
				else{
					//2021/7/9 修改備註只能儲存10種的限制，利用字串串接的方式(與原本結構通用)
					$temptaste=preg_split('/,/',$detail[$item]['SELECTIVEITEM'.$i]);
					for($j=0;$j<sizeof($temptaste);$j++){
						if(preg_match('/99999/',$temptaste[$j])){//手打備註
							$tastename=substr($temptaste[$j],7);
							$no='99999';
							$number='1';
							
							$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+註:'.$tastename;
						}
						else{
							$tastename='';
							$no=(int)intval($temptaste[$j])/10;
							$number=intval($temptaste[$j])%10;
							if(strlen($taste[$no]['name1'])>0){
								if(strlen($tastename)==0){
									$tastename=$tastename.$taste[$no]['name1'];
								}
								else{
									$tastename=$tastename.' /'.$taste[$no]['name1'];
								}
							}
							else{
							}
							if(strlen($taste[$no]['name2'])>0){
								if(strlen($tastename)==0){
									$tastename=$tastename.$taste[$no]['name2'];
								}
								else{
									$tastename=$tastename.' /'.$taste[$no]['name2'];
								}
							}
							else{
							}
							if($number>1){
								$tastename=$tastename.'*'.$number;
							}
							else{
							}
							$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$tastename;
						}
					}
				}
				/*else if(preg_match('/99999/',$detail[$item]['SELECTIVEITEM'.$i])){//手打備註
					$tastename=substr($detail[$item]['SELECTIVEITEM'.$i],7);
					$no='99999';
					$number='1';
					
					$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$tastename;
				}
				else{
					$tastename='';
					$no=(int)intval($detail[$item]['SELECTIVEITEM'.$i])/10;
					$number=intval($detail[$item]['SELECTIVEITEM'.$i])%10;
					if(strlen($taste[$no]['name1'])>0){
						if(strlen($tastename)==0){
							$tastename=$tastename.$taste[$no]['name1'];
						}
						else{
							$tastename=$tastename.' /'.$taste[$no]['name1'];
						}
					}
					else{
					}
					if(strlen($taste[$no]['name2'])>0){
						if(strlen($tastename)==0){
							$tastename=$tastename.$taste[$no]['name2'];
						}
						else{
							$tastename=$tastename.' /'.$taste[$no]['name2'];
						}
					}
					else{
					}
					if($number>1){
						$tastename=$tastename.$taste[$no]['name2'].'*'.$number;
					}
					else{
					}
					$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$tastename;
				}*/
			}
			/*if($detail[$item+1]['AMT']!=0){
				$tastelist=$tastelist.'<br>&nbsp;&nbsp;&nbsp;&nbsp;+'.$tastename;
			}
			else{
			}*/
			$html=$html.$tastelist."</div><div style='width:55px;text-align:right;padding:3px 0;float:left;' id='unitprice'>".$detail[$item]['UNITPRICE']."</div><div style='width:55px;text-align:center;padding:3px 0;float:left;' id='number'>".$detail[$item]['QTY']."</div><div style='width:55px;text-align:right;padding:3px 0;float:left;' id='subtotal'>".($detail[$item]['AMT']+$detail[$item+1]['AMT'])."</div></div>";
			//echo "<div class='label' style='width:100%;border-bottom:1px solid #dfdfdf;'><div style='width:3%;padding:3px 0;'><input type='checkbox' id='checkbox[]'></div><div style='width:10%;padding:3px 0 3px 3px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>".$index."</div><div style='width:calc(57%);padding:3px 0;' id='view'>".$detail[$item]['ITEMNAME']."(".$menu[intval($detail[$item]['ITEMCODE'])][].")"."</div><div style='width:10%;text-align:right;padding:3px 0;' id='unitprice'>".$('#tabs1 #tempbox input[name="unitprice"]').val()."</div><div style='width:10%;text-align:center;padding:3px 0;' id='number'>1</div><div style='width:10%;text-align:right;padding:3px 0;' id='subtotal'>".$('#tabs1 #tempbox input[name="money"]').val()."</div></div>";
		}
	}
	echo $html;
}
?>