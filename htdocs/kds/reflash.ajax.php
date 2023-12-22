<?php
$path='./items/noread/'.$_POST['groupno'];
if(file_exists($path)){
	$filelist=scandir($path);
	if(sizeof($filelist)>2){//檔案列表中必定會有"當層目錄(.)"與"上層目錄(..)"，因此須大於2
		if(file_exists('./items/kdsgroup-'.$_POST['groupno'].'.ini')){
			$f=fopen('./items/kdsgroup-'.$_POST['groupno'].'.ini','a');
		}
		else{
			$f=fopen('./items/kdsgroup-'.$_POST['groupno'].'.ini','a');
			fwrite($f,"[basic]".PHP_EOL);
		}
		if($_POST['groupno']=='service'){
			include_once '../tool/inilib.php';
			fclose($f);
			$temp=array();
			$f=parse_ini_file('./items/kdsgroup-'.$_POST['groupno'].'.ini',true);
			for($i=2;$i<sizeof($filelist);$i++){
				if(preg_match('/temp/',$filelist[$i])){//尚未完整寫入資料前暫存的檔名，完整檔名會由數字組成
				}
				else if(preg_match('/cancel/',$filelist[$i])){
					$t=preg_split('/_/',$filelist[$i]);
					array_push($temp,substr($t[1],0,(strlen($t[1])-4)));
					unlink($path.'/'.$filelist[$i]);
				}
				else{
					$list=parse_ini_file($path.'/'.$filelist[$i],true);
					foreach($list as $ii=>$v){
						for($index=0;$index<sizeof($v['name']);$index++){
							if(isset($f[$ii]['no'])){
								if(in_array($v['no'][$index],$f[$ii]['no'])){
								}
								else{
									$f[$ii]['no'][sizeof($f[$ii]['no'])]=$v['no'][$index];
									$f[$ii]['name'][sizeof($f[$ii]['name'])]=$v['name'][$index];
								}
							}
							else{
								$f[$ii]['no'][0]=$v['no'][$index];
								$f[$ii]['name'][0]=$v['name'][$index];
							}
						}
					}
					$list='';
					unlink($path.'/'.$filelist[$i]);
				}
			}
			if(sizeof($temp)>0){
				for($i=0;$i<sizeof($temp);$i++){
					unset($f[$temp[$i]]);
				}
			}
			else{
			}
			write_ini_file($f,'./items/kdsgroup-'.$_POST['groupno'].'.ini');
		}
		else{
			for($i=2;$i<sizeof($filelist);$i++){
				if(preg_match('/temp/',$filelist[$i])){//尚未完整寫入資料前暫存的檔名，完整檔名會由數字組成
				}
				else{
					$list=parse_ini_file($path.'/'.$filelist[$i],true);
					foreach($list as $ii=>$v){
						fwrite($f,"[".$ii."]".PHP_EOL);
						if(isset($v['saleno'][0])){
							$init=parse_ini_file('../database/initsetting.ini',true);
							if(file_exists('../demopos/syspram/buttons-'.$init['init']['firlan'].'.ini')){
								$lan=parse_ini_file('../demopos/syspram/buttons-'.$init['init']['firlan'].'.ini',true);
								fwrite($f,'saleno="'.$lan['name']['listtype'.$v['remarks'][0]].$v['saleno'][0].'"'.PHP_EOL);
							}
							else{
								fwrite($f,'saleno="'.$v['saleno'][0].'"'.PHP_EOL);
							}
							
						}
						else{
						}
						if(isset($v['tablenumber'][0])){
							fwrite($f,'tablenumber="'.$v['tablenumber'][0].'"'.PHP_EOL);
						}
						else{
						}
						for($index=0;$index<sizeof($v['itemno']);$index++){
							fwrite($f,'itemno[]="'.$v['itemno'][$index].'"'.PHP_EOL);
							fwrite($f,'itemname[]="'.$v['itemname'][$index].'"'.PHP_EOL);
							fwrite($f,'taste1[]="'.$v['taste1'][$index].'"'.PHP_EOL);
							fwrite($f,'taste1name[]="'.$v['taste1name'][$index].'"'.PHP_EOL);
							fwrite($f,'moneyname[]="'.$v['moneyname'][$index].'"'.PHP_EOL);
							fwrite($f,'number[]="'.$v['number'][$index].'"'.PHP_EOL);
							fwrite($f,'kdsno[]="'.$v['kdsno'][$index].'"'.PHP_EOL);
							fwrite($f,'kdsgroup[]="'.$v['kdsgroup'][$index].'"'.PHP_EOL);
						}
					}
					$list='';
					unlink($path.'/'.$filelist[$i]);
				}
			}
			fclose($f);
		}
	}
	else{
	}
}
else{//產生對應暫存路徑
	mkdir($path);
}
$res=['',''];
if($_POST['groupno']=='service'){
	$kdsgroup=parse_ini_file('./items/kdsgroup-'.$_POST['groupno'].'.ini',true);
	if(sizeof($kdsgroup)>1){
		foreach($kdsgroup as $i=>$v){
			if($i=='basic'){
			}
			else{
				$res[0] .= '<div class="box" style="width:calc(100% / 3 - 10px);height:calc(80% - 10px);float:left;border-radius:5px;border:1px solid #898989;padding:10px;margin:5px;	-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;"><div class="maindiv"><div id="data" style="display:none;"><input type="hidden" name="tablenumber" value="'.$i.'"></div><input type="button" style="background-color: transparent;background-image:url(\'./database/diff.png\');background-size:60px 60px;vertical-align:middle;width:60px;height:60px;border:0;">'.$i.'</div><div id="items" style="width:100%;height:calc(100% - 71px);font-size:40px;float:left;width:100%;overflow-x:hidden;overflow-y:auto;">';
				for($index=0;$index<sizeof($v['name']);$index++){
					$res[0] .= '<div style="width:100%;border-bottom:1px #000000 dashed;margin-bottom:2px;float:left;">'.$v['name'][$index].'</div>';
					if(isset($itemtotal[$v['no'][$index]])){
						$itemtotal[$v['no'][$index]]['counter']++;
					}
					else{
						$itemtotal[$v['no'][$index]]['name']=$v['name'][$index];
						$itemtotal[$v['no'][$index]]['counter']=1;
					}
				}
				$res[0] .= '</div></div>';
			}
		}
		foreach($itemtotal as $i=>$v){
			$res[1] .= '<div>';
			$res[1] .= $v['name'].'*'.$v['counter'];
			$res[1] .= '</div>';
		}
	}
	else{
	}
	echo json_encode($res);
}
else if(file_exists('./items/kdsgroup-'.$_POST['groupno'].'.ini')){
	$setup=parse_ini_file('../database/setup.ini',true);
	$kdstype=parse_ini_file('../database/'.$setup['basic']['company'].'-kds.ini',true);
	$kdsgroup=parse_ini_file('./items/kdsgroup-'.$_POST['groupno'].'.ini',true);
	$itemprinttype=parse_ini_file('../database/itemprinttype.ini',true);
	$menu=parse_ini_file('../database/'.$setup['basic']['company'].'-menu.ini',true);
	$itemtotal=array();
	if(file_exists('./database/initsetting.ini')){
		$initsetting=parse_ini_file('./database/initsetting.ini',true);
	}
	else{
	}
	if(!isset($initsetting['init']['type'])||$initsetting['init']['type']=='1'){//預設為"以單控菜"
		foreach($kdsgroup as $i=>$v){
			if($i=='basic'){
			}
			else{
				$consecnumber=preg_split('/consecnumber/',$i);
				$res[0] .= '<div style="border-radius:5px;border:1px solid #898989;overflow:hidden;padding:10px;margin:5px;"><div class="maindivtype1"><span class="consecnumber" style="float:left;font-size:'.$kdstype['basic']['consecnumbersize'].'px;">';
				if(isset($v['saleno'])){
					$res[0] .= $v['saleno'];
				}
				else{
					$res[0] .= $consecnumber[0];
				}
				if(isset($v['tablenumber'])&&$v['tablenumber']!=''){
					$res[0] .= '&nbsp;&nbsp;(<span style="font-weight:bold;">'.$v['tablenumber'].'桌</span>)';
				}
				else{
				}
				$res[0] .= '</span>';
				for($index=0;$index<sizeof($v['itemno']);$index++){
					$res[0] .= '<div class="itemdiv" style="width:calc(100% - 20px);float:left;border-top:1px solid #898989;margin:5px;padding:5px 5px 5px 65px;font-size:'.$kdstype['basic']['itemsize'].'px;';

					if(!isset($v['ordertag'])||$v['ordertag'][$index]=='1'){//2020/6/30 未出餐
						//$res[0] .= '';
					}
					else{//已出餐
						$res[0] .= 'background-color:#4bd1ffe6;';
					}
					$res[0] .= '"><div id="data" style="display:none;"><input type="hidden" name="listindex" value="'.$i.'"><input type="hidden" name="itemindex" value="'.$index.'"><input type="hidden" name="ordertag" value="';

					if(!isset($v['ordertag'])||$v['ordertag'][$index]=='1'){//2020/6/30 未出餐
						$res[0] .= '1';
					}
					else{//已出餐
						$res[0] .= '0';
					}

					$res[0] .= '"></div>'.$v['itemname'][$index];
					if($v['moneyname'][$index]!=''){
						$res[0] .= '('.$v['moneyname'][$index].')';
					}
					else{
					}
					$res[0] .= '*'.$v['number'][$index].'<br><div style="font-size:'.$kdstype['basic']['tastesize'].'px;">'.$v['taste1name'][$index].'</div></div>';
					if(isset($itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['itemno'])){
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['number']=floatval($itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['number'])+floatval($v['number'][$index]);
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['listindex'] .= 'js;js'.$i;
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['itemindex'] .= 'js;js'.$index;
					}
					else{
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['listindex']=$i;
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['itemindex']=$index;
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['itemno']=$v['itemno'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['itemname']=$v['itemname'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['taste1']=$v['taste1'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['taste1name']=$v['taste1name'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['moneyname']=$v['moneyname'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['number']=$v['number'][$index];
					}
				}
				$res[0] .= '</div></div>';
			}
		}
		foreach($itemtotal as $i=>$v){
			$res[1] .= '<div style="font-size:'.$kdstype['basic']['listitemsize'].'px">';
			$res[1] .= $v['itemname'].'*'.$v['number'];
			$res[1] .= '</div>';
		}
	}
	else{//以餐控單
		$grouplist=array();//餐點分群
		$itemgroup=array();//餐點分群index
		foreach($kdsgroup as $i=>$v){
			if($i=='basic'){
			}
			else{
				for($index=0;$index<sizeof($v['itemno']);$index++){
					if(isset($itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['itemno'])){
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['number']=floatval($itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['number'])+floatval($v['number'][$index]);
					}
					else{
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['itemno']=$v['itemno'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['itemname']=$v['itemname'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['taste1']=$v['taste1'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['taste1name']=$v['taste1name'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['moneyname']=$v['moneyname'][$index];
						$itemtotal[$v['itemno'][$index].'-'.$v['taste1'][$index].'-'.$v['moneyname'][$index]]['number']=$v['number'][$index];
					}

					if(isset($v['kdsgroup'][$index])&&floatval($v['kdsgroup'][$index])>=0){
						$groupindex='k'.$v['kdsgroup'][$index].'-'.$v['moneyname'][$index];
					}
					else{
						$groupindex='i'.$v['itemno'][$index].'-'.$v['moneyname'][$index];
					}
					if(isset($itemgroup[$groupindex][0])){
						if(isset($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])&&$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]>0){//有上限分群
							if($itemgroup[$groupindex][sizeof($itemgroup[$groupindex])-1]==$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]){//最後一個群組的數量已達到上限
								$tempnumber=$v['number'][$index];
								for(;$tempnumber>0;$tempnumber=floatval($tempnumber)-floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])){
									if($tempnumber>$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]){
										$gindex=sizeof($itemgroup[$groupindex]);
										$itemgroup[$groupindex][$gindex]=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										if(isset($grouplist[$groupindex.'-'.$gindex]['listindex'])){
											$grouplist[$groupindex.'-'.$gindex]['listindex'] .= 'js;js'.$i;
											$grouplist[$groupindex.'-'.$gindex]['itemindex'] .= 'js;js'.$index;
											$grouplist[$groupindex.'-'.$gindex]['itemnumber'] .= 'js;js'.$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										}
										else{
											$grouplist[$groupindex.'-'.$gindex]['listindex']=$i;
											$grouplist[$groupindex.'-'.$gindex]['itemindex']=$index;
											$grouplist[$groupindex.'-'.$gindex]['itemnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										}
										if(isset($v['kdsgroup'][$index])&&floatval($v['kdsgroup'][$index])>=0){
											$grouplist[$groupindex.'-'.$gindex]['printname']=$kdstype['group'.$v['kdsno'][$index]]['name'][$v['kdsgroup'][$index]];
										}
										else{
										}
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['listindex']=$i;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemindex']=$index;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemno']=$v['itemno'][$index];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemname']=$v['itemname'][$index];

										if(isset($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'])){
											$arrayindex=sizeof($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1']);
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['times']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['name']=$v['taste1name'][$index];
										}
										else{
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['times']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['name']=$v['taste1name'][$index];
										}
										
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['moneyname']=$v['moneyname'][$index];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['subnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										$grouplist[$groupindex.'-'.$gindex]['number']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
									}
									else{
										$gindex=sizeof($itemgroup[$groupindex]);
										$itemgroup[$groupindex][$gindex]=$tempnumber;
										if(isset($grouplist[$groupindex.'-'.$gindex]['listindex'])){
											$grouplist[$groupindex.'-'.$gindex]['listindex'] .= 'js;js'.$i;
											$grouplist[$groupindex.'-'.$gindex]['itemindex'] .= 'js;js'.$index;
											$grouplist[$groupindex.'-'.$gindex]['itemnumber'] .= 'js;js'.$tempnumber;
										}
										else{
											$grouplist[$groupindex.'-'.$gindex]['listindex']=$i;
											$grouplist[$groupindex.'-'.$gindex]['itemindex']=$index;
											$grouplist[$groupindex.'-'.$gindex]['itemnumber']=$tempnumber;
										}
										if(isset($v['kdsgroup'][$index])&&floatval($v['kdsgroup'][$index])>=0){
											$grouplist[$groupindex.'-'.$gindex]['printname']=$kdstype['group'.$v['kdsno'][$index]]['name'][$v['kdsgroup'][$index]];
										}
										else{
										}
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['listindex']=$i;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemindex']=$index;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemnumber']=$tempnumber;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemno']=$v['itemno'][$index];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemname']=$v['itemname'][$index];
										if(isset($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'])){
											$arrayindex=sizeof($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1']);
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['times']=$tempnumber;
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['name']=$v['taste1name'][$index];
										}
										else{
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['times']=$tempnumber;
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['name']=$v['taste1name'][$index];
										}
										
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['moneyname']=$v['moneyname'][$index];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['subnumber']=$tempnumber;
										$grouplist[$groupindex.'-'.$gindex]['number']=$tempnumber;
										break;
									}
								}
							}
							else if(floatval($itemgroup[$groupindex][sizeof($itemgroup[$groupindex])-1])+floatval($v['number'][$index])>$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]){//加上新數量後超出上限
								$gindex=sizeof($itemgroup[$groupindex]);
								if($itemgroup[$groupindex][$gindex-1]!=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]){
									if(isset($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemno'])){
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber'] = floatval($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber'])+(floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($grouplist[$groupindex.'-'.($gindex-1)]['number']));
										$grouplist[$groupindex.'-'.($gindex-1)]['number'] = $kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['listindex'] .= 'js;js'.$i;
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemindex'] .= 'js;js'.$index;
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemnumber'] .= 'js;js'.(floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($grouplist[$groupindex.'-'.($gindex-1)]['number']));

										if(isset($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'])){
											$arrayindex=sizeof($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1']);
											$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][$arrayindex]['times'] = (floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($grouplist[$groupindex.'-'.($gindex-1)]['number']));
											$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][$arrayindex]['name'] = $v['taste1name'][$index];
										}
										else{
											$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][0]['times'] = (floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($grouplist[$groupindex.'-'.($gindex-1)]['number']));
											$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][0]['name'] = $v['taste1name'][$index];
										}
									}
									else{
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['listindex']=$i;
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemindex']=$index;
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemnumber'] = (floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($itemgroup[$groupindex][$gindex-1]));
										$grouplist[$groupindex.'-'.($gindex-1)]['number']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemno'] = $v['itemno'][$index];
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemname'] = $v['itemname'][$index];
										
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['moneyname'] = $v['moneyname'][$index];
										$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber'] = (floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($itemgroup[$groupindex][$gindex-1]));

										if(isset($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'])){
											$arrayindex=sizeof($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1']);
											$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][$arrayindex]['times'] = (floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($itemgroup[$groupindex][$gindex-1]));
											$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][$arrayindex]['name'] = $v['taste1name'][$index];
										}
										else{
											$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][0]['times'] = (floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($itemgroup[$groupindex][$gindex-1]));
											$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][0]['name'] = $v['taste1name'][$index];
										}
									}
									
									$v['number'][$index]=floatval($v['number'][$index])-(floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($itemgroup[$groupindex][$gindex-1]));
									$itemgroup[$groupindex][$gindex-1]=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
									if(isset($grouplist[$groupindex.'-'.($gindex-1)]['listindex'])){
										$grouplist[$groupindex.'-'.($gindex-1)]['listindex'] .= 'js;js'.$i;
										$grouplist[$groupindex.'-'.($gindex-1)]['itemindex'] .= 'js;js'.$index;
										$grouplist[$groupindex.'-'.($gindex-1)]['itemnumber'] .= 'js;js'.(floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($grouplist[$groupindex.'-'.($gindex-1)]['number']));
									}
									else{
										$grouplist[$groupindex.'-'.($gindex-1)]['listindex']=$i;
										$grouplist[$groupindex.'-'.($gindex-1)]['itemindex']=$index;
										$grouplist[$groupindex.'-'.($gindex-1)]['itemnumber']=(floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])-floatval($grouplist[$groupindex.'-'.($gindex-1)]['number']));
									}
								}
								else{
								}
								$tempnumber=$v['number'][$index];
								for($tempnumber=floatval($tempnumber)-floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])+floatval($itemgroup[$groupindex][$gindex-1]);$tempnumber>0;$tempnumber=floatval($tempnumber)-floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])){
									if($tempnumber>$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]){
										$itemgroup[$groupindex][$gindex]=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										if(isset($grouplist[$groupindex.'-'.$gindex]['listindex'])){
											$grouplist[$groupindex.'-'.$gindex]['listindex'] .= 'js;js'.$i;
											$grouplist[$groupindex.'-'.$gindex]['itemindex'] .= 'js;js'.$index;
											$grouplist[$groupindex.'-'.$gindex]['itemnumber'] .= 'js;js'.$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										}
										else{
											$grouplist[$groupindex.'-'.$gindex]['listindex']=$i;
											$grouplist[$groupindex.'-'.$gindex]['itemindex']=$index;
											$grouplist[$groupindex.'-'.$gindex]['itemnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										}
										if(isset($v['kdsgroup'][$index])&&floatval($v['kdsgroup'][$index])>=0){
											$grouplist[$groupindex.'-'.$gindex]['printname']=$kdstype['group'.$v['kdsno'][$index]]['name'][$v['kdsgroup'][$index]];
										}
										else{
										}
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['listindex']=$i;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemindex']=$index;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemno']=$v['itemno'][$index];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemname']=$v['itemname'][$index];
										if(isset($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'])){
											$arrayindex=sizeof($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1']);
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['times']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['name']=$v['taste1name'][$index];
										}
										else{
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['times']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['name']=$v['taste1name'][$index];
										}
										
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['moneyname']=$v['moneyname'][$index];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['subnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										$grouplist[$groupindex.'-'.$gindex]['number']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
									}
									else{
										$gindex=sizeof($itemgroup[$groupindex]);
										$itemgroup[$groupindex][$gindex]=$tempnumber;
										if(isset($grouplist[$groupindex.'-'.$gindex]['listindex'])){
											$grouplist[$groupindex.'-'.$gindex]['listindex'] .= 'js;js'.$i;
											$grouplist[$groupindex.'-'.$gindex]['itemindex'] .= 'js;js'.$index;
											$grouplist[$groupindex.'-'.$gindex]['itemnumber'] .= 'js;js'.$tempnumber;
										}
										else{
											$grouplist[$groupindex.'-'.$gindex]['listindex']=$i;
											$grouplist[$groupindex.'-'.$gindex]['itemindex']=$index;
											$grouplist[$groupindex.'-'.$gindex]['itemnumber']=$tempnumber;
										}
										if(isset($v['kdsgroup'][$index])&&floatval($v['kdsgroup'][$index])>=0){
											$grouplist[$groupindex.'-'.$gindex]['printname']=$kdstype['group'.$v['kdsno'][$index]]['name'][$v['kdsgroup'][$index]];
										}
										else{
										}
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['listindex']=$i;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemindex']=$index;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemnumber']=$tempnumber;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemno']=$v['itemno'][$index];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemname']=$v['itemname'][$index];
										if(isset($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'])){
											$arrayindex=sizeof($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1']);
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['times']=$tempnumber;
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['name']=$v['taste1name'][$index];
										}
										else{
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['times']=$tempnumber;
											$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['name']=$v['taste1name'][$index];
										}
										
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['moneyname']=$v['moneyname'][$index];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['subnumber']=$tempnumber;
										$grouplist[$groupindex.'-'.$gindex]['number']=$tempnumber;
										break;
									}
								}
							}
							else{
								$gindex=sizeof($itemgroup[$groupindex]);
								$itemgroup[$groupindex][$gindex-1]=floatval($itemgroup[$groupindex][$gindex-1])+floatval($v['number'][$index]);
								$grouplist[$groupindex.'-'.($gindex-1)]['number']=floatval($grouplist[$groupindex.'-'.($gindex-1)]['number'])+floatval($v['number'][$index]);
								if(isset($grouplist[$groupindex.'-'.($gindex-1)]['listindex'])){
									$grouplist[$groupindex.'-'.($gindex-1)]['listindex'] .= 'js;js'.$i;
									$grouplist[$groupindex.'-'.($gindex-1)]['itemindex'] .= 'js;js'.$index;
									$grouplist[$groupindex.'-'.($gindex-1)]['itemnumber'] .= 'js;js'.$v['number'][$index];
								}
								else{
									$grouplist[$groupindex.'-'.($gindex-1)]['listindex']=$i;
									$grouplist[$groupindex.'-'.($gindex-1)]['itemindex']=$index;
									$grouplist[$groupindex.'-'.($gindex-1)]['itemnumber']=$v['number'][$index];
								}
								if(isset($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemno'])){
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber']=floatval($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber'])+floatval($v['number'][$index]);
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['listindex'] .= 'js;js'.$i;
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemindex'] .= 'js;js'.$index;
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemnumber'] .= 'js;js'.$v['number'][$index];
								}
								else{
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['listindex']=$i;
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemindex']=$index;
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemnumber']=$v['number'][$index];
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemno']=$v['itemno'][$index];
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemname']=$v['itemname'][$index];
									
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['moneyname']=$v['moneyname'][$index];
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber']=$v['number'][$index];
								}
								if(isset($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'])){
									$arrayindex=sizeof($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1']);
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][$arrayindex]['times']=$v['number'][$index];
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][$arrayindex]['name']=$v['taste1name'][$index];
								}
								else{
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][0]['times']=$v['number'][$index];
									$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][0]['name']=$v['taste1name'][$index];
								}
							}
						}
						else{//無上限分群
							$gindex=sizeof($itemgroup[$groupindex]);
							$itemgroup[$groupindex][$gindex-1]=floatval($itemgroup[$groupindex][$gindex-1])+floatval($v['number'][$index]);
							$grouplist[$groupindex.'-'.($gindex-1)]['number']=floatval($grouplist[$groupindex.'-'.($gindex-1)]['number'])+floatval($v['number'][$index]);
							if(isset($grouplist[$groupindex.'-'.($gindex-1)]['listindex'])){
								$grouplist[$groupindex.'-'.($gindex-1)]['listindex'] .= 'js;js'.$i;
								$grouplist[$groupindex.'-'.($gindex-1)]['itemindex'] .= 'js;js'.$index;
								$grouplist[$groupindex.'-'.($gindex-1)]['itemnumber'] .= 'js;js'.$v['number'][$index];
							}
							else{
								$grouplist[$groupindex.'-'.($gindex-1)]['listindex']=$i;
								$grouplist[$groupindex.'-'.($gindex-1)]['itemindex']=$index;
								$grouplist[$groupindex.'-'.($gindex-1)]['itemnumber']=$v['number'][$index];
							}
							if(isset($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemno'])){
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber'] = floatval($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber'])+floatval($v['number'][$index]);
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['listindex'] .= 'js;js'.$i;
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemindex'] .= 'js;js'.$index;
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemnumber'] .= 'js;js'.$v['number'][$index];
							}
							else{
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['listindex']=$i;
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemindex']=$index;
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemnumber'] = floatval($v['number'][$index]);
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemno'] = $v['itemno'][$index];
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['itemname'] = $v['itemname'][$index];
								
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['moneyname'] = $v['moneyname'][$index];
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['subnumber'] = floatval($v['number'][$index]);
							}
							if(isset($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'])){
								$arrayindex=sizeof($grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1']);
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][$arrayindex]['times'] = floatval($v['number'][$index]);
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][$arrayindex]['name'] = $v['taste1name'][$index];
							}
							else{
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][0]['times'] = floatval($v['number'][$index]);
								$grouplist[$groupindex.'-'.($gindex-1)][$v['itemno'][$index]]['taste1'][0]['name'] = $v['taste1name'][$index];
							}
						}
					}
					else{
						if(isset($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])&&$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]>0&&$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]<$v['number'][$index]){//首次數量已超出上限
							$tempnumber=$v['number'][$index];
							for(;$tempnumber>0;$tempnumber=floatval($tempnumber)-floatval($kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]])){
								if($tempnumber>$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]]){
									if(isset($itemgroup[$groupindex])){
										$gindex=sizeof($itemgroup[$groupindex]);
									}
									else{
										$gindex=0;
									}
									$itemgroup[$groupindex][$gindex]=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
									if(isset($grouplist[$groupindex.'-'.$gindex]['listindex'])){
										$grouplist[$groupindex.'-'.$gindex]['listindex'] .= 'js;js'.$i;
										$grouplist[$groupindex.'-'.$gindex]['itemindex'] .= 'js;js'.$index;
										$grouplist[$groupindex.'-'.$gindex]['itemnumber'] .= 'js;js'.$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
									}
									else{
										$grouplist[$groupindex.'-'.$gindex]['listindex']=$i;
										$grouplist[$groupindex.'-'.$gindex]['itemindex']=$index;
										$grouplist[$groupindex.'-'.$gindex]['itemnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
									}
									if(isset($v['kdsgroup'][$index])&&floatval($v['kdsgroup'][$index])>=0){
										$grouplist[$groupindex.'-'.$gindex]['printname']=$kdstype['group'.$v['kdsno'][$index]]['name'][$v['kdsgroup'][$index]];
									}
									else{
									}
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['listindex']=$i;
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemindex']=$index;
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemno']=$v['itemno'][$index];
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemname']=$v['itemname'][$index];
									if(isset($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'])){
										$arrayindex=sizeof($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1']);
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['times']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['name']=$v['taste1name'][$index];
									}
									else{
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['times']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['name']=$v['taste1name'][$index];
									}
									
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['moneyname']=$v['moneyname'][$index];
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['subnumber']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
									$grouplist[$groupindex.'-'.$gindex]['number']=$kdstype['group'.$v['kdsno'][$index]]['limit'][$v['kdsgroup'][$index]];
								}
								else{
									if(isset($itemgroup[$groupindex])){
										$gindex=sizeof($itemgroup[$groupindex]);
									}
									else{
										$gindex=0;
									}
									$itemgroup[$groupindex][$gindex]=$tempnumber;
									if(isset($grouplist[$groupindex.'-'.$gindex]['listindex'])){
										$grouplist[$groupindex.'-'.$gindex]['listindex'] .= 'js;js'.$i;
										$grouplist[$groupindex.'-'.$gindex]['itemindex'] .= 'js;js'.$index;
										$grouplist[$groupindex.'-'.$gindex]['itemnumber'] .= 'js;js'.$tempnumber;
									}
									else{
										$grouplist[$groupindex.'-'.$gindex]['listindex']=$i;
										$grouplist[$groupindex.'-'.$gindex]['itemindex']=$index;
										$grouplist[$groupindex.'-'.$gindex]['itemnumber']=$tempnumber;
									}
									if(isset($v['kdsgroup'][$index])&&floatval($v['kdsgroup'][$index])>=0){
										$grouplist[$groupindex.'-'.$gindex]['printname']=$kdstype['group'.$v['kdsno'][$index]]['name'][$v['kdsgroup'][$index]];
									}
									else{
									}
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['listindex']=$i;
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemindex']=$index;
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemnumber']=$tempnumber;
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemno']=$v['itemno'][$index];
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['itemname']=$v['itemname'][$index];
									if(isset($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'])){
										$arrayindex=sizeof($grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1']);
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['times']=$tempnumber;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][$arrayindex]['name']=$v['taste1name'][$index];
									}
									else{
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['times']=$tempnumber;
										$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['taste1'][0]['name']=$v['taste1name'][$index];
									}
									
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['moneyname']=$v['moneyname'][$index];
									$grouplist[$groupindex.'-'.$gindex][$v['itemno'][$index]]['subnumber']=$tempnumber;
									$grouplist[$groupindex.'-'.$gindex]['number']=$tempnumber;
									break;
								}
								//print_r($grouplist);
							}
						}
						else{//無上限分群或不超出上限
							$itemgroup[$groupindex][0]=$v['number'][$index];
							if(isset($v['kdsgroup'][$index])&&floatval($v['kdsgroup'][$index])>=0){
								$grouplist[$groupindex.'-0']['printname']=$kdstype['group'.$v['kdsno'][$index]]['name'][$v['kdsgroup'][$index]];
							}
							else{
							}
							if(isset($grouplist[$groupindex.'-0']['listindex'])){
								$grouplist[$groupindex.'-0']['listindex'].= 'js;js'.$i;
								$grouplist[$groupindex.'-0']['itemindex'] .= 'js;js'.$index;
								$grouplist[$groupindex.'-0']['itemnumber'] .= 'js;js'.$v['number'][$index];
							}
							else{
								$grouplist[$groupindex.'-0']['listindex']=$i;
								$grouplist[$groupindex.'-0']['itemindex']=$index;
								$grouplist[$groupindex.'-0']['itemnumber']=$v['number'][$index];
							}
							$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['listindex']=$i;
							$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['itemindex']=$index;
							$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['itemnumber']=$v['number'][$index];
							$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['itemno']=$v['itemno'][$index];
							$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['itemname']=$v['itemname'][$index];
							if(isset($grouplist[$groupindex.'-0'][$v['itemno'][$index]]['taste1'])){
								$arrayindex=sizeof($grouplist[$groupindex.'-0'][$v['itemno'][$index]]['taste1']);
								$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['taste1'][$arrayindex]['times']=$v['number'][$index];
								$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['taste1'][$arrayindex]['name']=$v['taste1name'][$index];
							}
							else{
								$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['taste1'][0]['times']=$v['number'][$index];
								$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['taste1'][0]['name']=$v['taste1name'][$index];
							}
							
							$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['moneyname']=$v['moneyname'][$index];
							$grouplist[$groupindex.'-0'][$v['itemno'][$index]]['subnumber']=$v['number'][$index];
							$grouplist[$groupindex.'-0']['number']=$v['number'][$index];
						}
					}
				}
			}
			//print_r($grouplist);
		}
		//print_r($grouplist);
		foreach($grouplist as $i=>$v){
			$res[0] .= '<div style="border-radius:5px;border:1px solid #898989;overflow:hidden;padding:10px;margin:5px;"><div class="maindiv"><div id="data" style="display:none;"><input type="hidden" name="listindex" value="'.$v['listindex'].'"><input type="hidden" name="itemindex" value="'.$v['itemindex'].'"><input type="hidden" name="itemnumber" value="'.$v['itemnumber'].'"></div><input type="button" style="background-color: transparent;background-image:url(\'./database/diff.png\');background-size:60px 60px;vertical-align:middle;width:60px;height:60px;border:0;">';
			if(isset($v['printname'])){
				$res[0] .= $v['printname'].'*'.$v['number'].'</div>';
				$iitime=0;
				foreach($v as $ii=>$vv){
					if($ii=='printname'||$ii=='number'||$ii=='listindex'||$ii=='itemindex'||$ii=='itemnumber'){
					}
					else{
						$res[0] .= '<div class="subdiv" style="';
						if($iitime==0){
							$res[0] .= 'margin-left:90px;';
							$iitime++;
						}
						else{
						}
						$res[0] .= 'font-size:40px;font-weight:bold;';
						if(isset($menu[intval($vv['itemno'])])){
							$res[0] .= 'color:'.$menu[intval($vv['itemno'])]['color1'].';background-color:'.$menu[intval($vv['itemno'])]['bgcolor'].';';
						}
						else{
						}
						$res[0] .= '"><div id="data" style="display:none;"><input type="hidden" name="listindex" value="'.$vv['listindex'].'"><input type="hidden" name="itemindex" value="'.$vv['itemindex'].'"><input type="hidden" name="itemnumber" value="'.$vv['itemnumber'].'"></div><input type="button" style="margin-right:10px;background-color: transparent;background-image:url(\'./database/diff.png\');background-size:40px 40px;vertical-align:middle;width:40px;height:40px;border:0;"><div id="itemdiv" style="font-size=10px;">'.$vv['itemname'].'*'.$vv['subnumber'].'</div>';
						foreach($vv['taste1'] as $ti=>$tv){
							//if($ti!=''){
								$res[0] .= '<div id="itemdiv"><div style="width:calc(100% - 50px);margin:2px;border:2px dotted #898989;border-radius:5px;float:left;font-size=10px;">'.$tv['name'].'</div>';
								if(isset($tv['times'])&&intval($tv['times'])>1){
									$res[0] .= '*'.$tv['times'];
								}
								else{
								}
								$res[0] .= '</div>';
							/*}
							else{
							}*/
						}
						$res[0] .= '</div>';
					}
				}
				$res[0] .= '</div>';
			}
			else{
				//$res[0] .= $v['printname'].'*'.$v['number'].'<br>';
				foreach($v as $ii=>$vv){
					if($ii=='printname'||$ii=='number'||$ii=='listindex'||$ii=='itemindex'||$ii=='itemnumber'){
					}
					else{
						$res[0] .= $vv['itemname'].'*'.$vv['subnumber'].'</div>';
					}
				}
			}
			$res[0] .= '</div>';
		}
		foreach($itemtotal as $i=>$v){
			$res[1] .= '<div>';
			$res[1] .= $v['itemname'].'*'.$v['number'];
			$res[1] .= '</div>';
		}
	}
	echo json_encode($res);
}
else{
	echo json_encode($res);
}
?>