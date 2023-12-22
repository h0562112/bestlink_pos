<?php
function php_curl_ajax($method,$url,$header,$postdata){
	if($method=='get'){
		if(sizeof($postdata)>0){
			$url .= '?';
			foreach($postdata as $name=>$value){
				$url .= $name.'='.$value.'&';
			}
		}
		else{
		}
	}
	else{
	}
	$ch = curl_init();
	//echo '<br>url='.$url.'<br>';
	curl_setopt($ch, CURLOPT_URL, $url);//
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	if($method=='post'){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
	}
	else{
	}
	// Edit: prior variable $postFields should be $postfields;
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$tempdata = curl_exec($ch);
	$resule=json_decode($tempdata,true);
	curl_close($ch);
	return $resule;
}
function Login($url,$method,$account,$password,$device_id,$app_version,$app_type){
	if(isset($app_type)&&$app_type!=''){
	}
	else{
		$app_type=99;
	}
	if(isset($app_version)&&$app_version!=''){
	}
	else{
		$app_version='0.0.0.1';
	}
	$header=array('Content-Type: application/json');
	$PostData = array(
		"account"=>$account,
		"password" => hash('sha256',$password),
		"device_id" => $device_id,
		"app_type" => $app_type,
		"app_version" => $app_version
	);
	return php_curl_ajax($method,$url.'/pos/login',$header,$PostData);
}
function Logout($url,$method,$MC_API_Token,$MC_API_User){
	$PostData = array();
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/pos/logout',$header,$PostData);
}
function GetClass($url,$method,$MC_API_Token,$MC_API_User){
	$PostData = array("order_method"=>"all");
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/order/classStatus',$header,$PostData);
}
function GetList($url,$method,$MC_API_Token,$MC_API_User,$page,$count,$type,$status){
	$PostData = array(
		"page"=>$page,
		"count"=>$count,
		"type"=>$type,
		"status"=>$status
	);
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/order/list/detail',$header,$PostData);
}
function GetTheList($url,$method,$MC_API_Token,$MC_API_User,$order_id){
	$PostData = array();
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/order/'.$order_id.'/detail',$header,$PostData);
}
function Accept($url,$method,$MC_API_Token,$MC_API_User,$order_id,$pos_order_sn,$pos_cashier_no,$pos_take_no){
	$PostData = array(
		"pos_order_sn"=>$pos_order_sn,
		"pos_cashier_no"=>$pos_cashier_no,
		"pos_take_no"=>$pos_take_no
	);
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/order/'.$order_id.'/accept',$header,$PostData);
}
function Finish($url,$method,$MC_API_Token,$MC_API_User,$order_id,$payment_status,$payment_method,$settle_date,$settle_time){
	//2021/3/8 payment_status付款狀態預設13"已付款"，payment_method付款方式"1"現金，目前API只支援該方式
	if($payment_status===''){//2021/8/18 已付款不用傳任何參數，參數用於來店付款的帳單使用
		$PostData = array(
		);
	}
	else{
		$PostData = array(
			"payment_status" => $payment_status,
			"payment_method" => $payment_method,
			"settle_date" => $settle_date,
			"settle_time" => $settle_time
		);
	}
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/order/'.$order_id.'/finish',$header,$PostData);
}
function Reject($url,$method,$MC_API_Token,$MC_API_User,$order_id,$reject_code){
	$PostData = array(
		"reject_code"=>$reject_code
	);
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/order/'.$order_id.'/reject',$header,$PostData);
}
function Cancel($url,$method,$MC_API_Token,$MC_API_User,$order_id){
	$PostData = array(
	);
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/order/'.$order_id.'/cancel',$header,$PostData);
}
function Void($url,$method,$MC_API_Token,$MC_API_User,$order_id){
	$PostData = array(
	);
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;
	return php_curl_ajax($method,$url.'/order/'.$order_id.'/void',$header,$PostData);
}

//菜單相關
function getmenudata($company,$dep){
	$menudata=parse_ini_file("../menudata/".$company."/".$dep."/".$company."-menu.ini",true);
	$frontdata=parse_ini_file("../menudata/".$company."/".$dep."/".$company."-front.ini",true);
	$tastedata=parse_ini_file("../menudata/".$company."/".$dep."/".$company."-taste.ini",true);
	if(file_exists("../menudata/".$company."/".$dep."/".$company."-tastegroup.ini")){
		$tastegroupdata=parse_ini_file("../menudata/".$company."/".$dep."/".$company."-tastegroup.ini",true);
	}
	else{
	}

	$conn=sqlconnect("../menudata/".$company."/".$dep,"menu.db","","","","sqlite");
	$sql="SELECT DISTINCT fronttype FROM itemsdata WHERE state=1 OR state IS NULL";
	$front=sqlquery($conn,$sql,"sqlite");
	$sql="SELECT * FROM itemsdata WHERE state=1 OR state IS NULL";
	$menu=sqlquery($conn,$sql,"sqlite");
	//echo "menu.db=".print_r($menu,true)."<br>";
	sqlclose($conn,"sqlite");

	$product_list=array();
	$category=array();
	$category_list=array();
	$adjust_list=array();
	$combination_list=array();
	$package_list=array();

	$publictasteitem=array();
	$publictastegroup=array();
	$insertedtaste=array();

	for($t=0;$t<sizeof($tastedata);$t++){
		if(!isset($tastedata[$t]['state'])||$tastedata[$t]['state']==='1'){
			if((!isset($tastedata[$t]['public'])||$tastedata[$t]['public']==="1")&&(!isset($tastedata[$t]['webvisible'])||$tastedata[$t]['webvisible']==="1")){
				$publictasteitem[]=$t;
				if(isset($tastedata[$t]['group'])&&isset($tastegroupdata)&&isset($tastegroupdata[$tastedata[$t]['group']])&&isset($tastegroupdata[$tastedata[$t]['group']]['type'])&&$tastegroupdata[$tastedata[$t]['group']]['type']!='0'){
					$index=sizeof($product_list);
					$product_list[$index]['id']=$t;
					$product_list[$index]['name']=$tastedata[$t]['name1'];
					$product_list[$index]['type']=2;
					$product_list[$index]['sub_type']=(isset($tastedata[$t]['sub_type']))?($tastedata[$t]['sub_type']):((isset($tastegroupdata[$tastedata[$t]['group']]['type'])&&$tastegroupdata[$tastedata[$t]['group']]['type']==='30')?(31):((isset($tastegroupdata[$tastedata[$t]['group']]['type'])&&$tastegroupdata[$tastedata[$t]['group']]['type']==='40')?(42):(0)));
					$product_list[$index]['sort']=$tastedata[$t]['seq'];
					$product_list[$index]['price']=($tastedata[$t]['money']!=='')?($tastedata[$t]['money']):(0);
					$product_list[$index]['description']=null;
					$product_list[$index]['is_mandatory']=0;
					$product_list[$index]['is_common']=0;
					$product_list[$index]['is_visible']=1;
					$product_list[$index]['min_limit']=0;
					$product_list[$index]['max_limit']=(isset($tastegroupdata[$tastedata[$t]['group']]['type'])&&($tastegroupdata[$tastedata[$t]['group']]['type']==='30'||$tastegroupdata[$tastedata[$t]['group']]['type']==='40'))?(1):(($tastedata[$t]['money']!==''&&$tastedata[$t]['money']!=='0')?(0):(1));
					$product_list[$index]['calorie']=null;
				}
				else{
					$index=sizeof($product_list);
					$product_list[$index]['id']=$t;
					$product_list[$index]['name']=$tastedata[$t]['name1'];
					$product_list[$index]['type']=2;
					$product_list[$index]['sub_type']=0;
					$product_list[$index]['sort']=$tastedata[$t]['seq'];
					$product_list[$index]['price']=($tastedata[$t]['money']!='')?($tastedata[$t]['money']):(0);
					$product_list[$index]['description']=null;
					$product_list[$index]['is_mandatory']=0;
					$product_list[$index]['is_common']=0;
					$product_list[$index]['is_visible']=1;
					$product_list[$index]['min_limit']=0;
					$product_list[$index]['max_limit']=($tastedata[$t]['money']!==''&&$tastedata[$t]['money']!=='0')?(0):(1);
					$product_list[$index]['calorie']=null;
				}
			}
			else{
			}
		}
		else{
		}
	}

	if(sizeof($publictasteitem)>0){
		for($pt=0;$pt<sizeof($publictasteitem);$pt++){
			if(!isset($tastedata[$publictasteitem[$pt]]['state'])||$tastedata[$publictasteitem[$pt]]['state']==='1'){
				if(isset($tastedata[$publictasteitem[$pt]]['group'])&&$tastedata[$publictasteitem[$pt]]['group']!=''){
				}
				else{
					$tastedata[$publictasteitem[$pt]]['group']='public';
				}
				if(isset($publictastegroup[$tastedata[$publictasteitem[$pt]]['group']])){
				}
				else{
					$adindex=sizeof($adjust_list);
					$adjust_list[$adindex]['parent_id']=($tastedata[$publictasteitem[$pt]]['group']==='public')?('public'):($tastedata[$publictasteitem[$pt]]['group']);
					$publictastegroup[$tastedata[$publictasteitem[$pt]]['group']]=$adindex;
					
					if(isset($tastedata)&&isset($tastedata[$publictasteitem[$pt]])){
						$index=sizeof($product_list);
						$product_list[$index]['id']=$tastedata[$publictasteitem[$pt]]['group'];
						$product_list[$index]['name']=$tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['name'];
						$product_list[$index]['type']=20;
						$product_list[$index]['sub_type']=(isset($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['type']))?($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['type']):(0);
						$product_list[$index]['sort']=$tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['seq'];
						$product_list[$index]['price']=null;
						$product_list[$index]['description']=null;
						$product_list[$index]['is_mandatory']=0;
						$product_list[$index]['is_common']=0;
						$product_list[$index]['is_visible']=1;
						$product_list[$index]['min_limit']=(isset($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['type'])&&$tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['type']!=='0')?(1):((isset($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos'])&&$tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos']!=='-1')?($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos']):(0));
						$product_list[$index]['max_limit']=(isset($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['type'])&&$tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['type']!=='0')?(1):((isset($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos'])&&$tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos']!=='-1')?($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos']):(0));
						$product_list[$index]['calorie']=null;
					}
					else{
						$index=sizeof($product_list);
						$product_list[$index]['id']="public";
						$product_list[$index]['name']="調整項目";
						$product_list[$index]['type']=20;
						$product_list[$index]['sub_type']=0;
						$product_list[$index]['sort']=1;
						$product_list[$index]['price']=null;
						$product_list[$index]['description']=null;
						$product_list[$index]['is_mandatory']=0;
						$product_list[$index]['is_common']=0;
						$product_list[$index]['is_visible']=1;
						$product_list[$index]['min_limit']=0;
						$product_list[$index]['max_limit']=(isset($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos'])&&$tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos']!=='-1')?($tastegroupdata[$tastedata[$publictasteitem[$pt]]['group']]['pos']):(0);
						$product_list[$index]['calorie']=null;
					}
				}
				$adjust_list[$publictastegroup[$tastedata[$publictasteitem[$pt]]['group']]]['child_list'][]=$publictasteitem[$pt];
			}
			else{
			}
		}
	}
	else{
	}

	for($f=0;$f<sizeof($front);$f++){
		if((!isset($frontdata[$front[$f]['fronttype']]['state'])||$frontdata[$front[$f]['fronttype']]['state']==='1')&&(!isset($frontdata[$front[$f]['fronttype']]['webvisible'])||$frontdata[$front[$f]['fronttype']]['webvisible']==='1')){
			$index=sizeof($product_list);
			$product_list[$index]['id']=$front[$f]['fronttype'];
			$product_list[$index]['name']=$frontdata[$front[$f]['fronttype']]['name1'];
			$product_list[$index]['type']=10;
			$product_list[$index]['sub_type']=0;
			$product_list[$index]['sort']=$frontdata[$front[$f]['fronttype']]['seq'];
			$product_list[$index]['price']=null;
			$product_list[$index]['description']=null;
			$product_list[$index]['is_mandatory']=0;
			$product_list[$index]['is_common']=0;
			$product_list[$index]['is_visible']=1;
			$product_list[$index]['min_limit']=0;
			$product_list[$index]['max_limit']=0;
			$product_list[$index]['calorie']=null;
		}
		else{
		}
	}

	for($m=0;$m<sizeof($menu);$m++){
		if(isset($menudata[$menu[$m]['inumber']])&&(!isset($menudata[$menu[$m]['inumber']]['webvisible'])||$menudata[$menu[$m]['inumber']]['webvisible']==='1')&&(!isset($frontdata[$menu[$m]['fronttype']]['webvisible'])||$frontdata[$menu[$m]['fronttype']]['webvisible']==='1')){
			$privatetastegroup=array();
			$cindex='';
			$index=sizeof($product_list);
			$product_list[$index]['id']=$menu[$m]['inumber'];
			$product_list[$index]['name']=$menudata[$menu[$m]['inumber']]['name1'];
			$product_list[$index]['type']=(intval($menu[$m]['isgroup'])==0&&strlen($menu[$m]['childtype'])===0)?(1):(3);
			$product_list[$index]['sub_type']=0;
			$product_list[$index]['sort']=$menu[$m]['frontseq'];
			$product_list[$index]['price']=($menudata[$menu[$m]['inumber']]['money1']!='')?($menudata[$menu[$m]['inumber']]['money1']):(0);
			$product_list[$index]['description']=($menudata[$menu[$m]['inumber']]['name2']==="")?($menudata[$menu[$m]['inumber']]['name2']):(null);//if($menudata[$menu[$m]['inumber']]['name2']=="")$menudata[$menu[$m]['inumber']]['name2'];else null;
			$product_list[$index]['is_mandatory']=0;
			$product_list[$index]['is_common']=0;
			$product_list[$index]['is_visible']=1;
			$product_list[$index]['min_limit']=0;
			$product_list[$index]['max_limit']=0;
			$product_list[$index]['calorie']=null;

			if(sizeof($category)>0&&in_array($menu[$m]['fronttype'],$category)){
				$cindex=array_search($menu[$m]['fronttype'],$category);
			}
			else{
				$cindex=sizeof($category);
				$category[]=$menu[$m]['fronttype'];
			}
			
			if(!isset($frontdata[$menu[$m]['fronttype']]['subtype'])||$frontdata[$menu[$m]['fronttype']]['subtype']==='0'){
				if(isset($category_list[$cindex])){
				}
				else{
					$category_list[$cindex]['parent_id']=$menu[$m]['fronttype'];
				}
				$category_list[$cindex]['child_list'][]=$menu[$m]['inumber'];
			}
			else{
			}

			if(intval($menu[$m]['isgroup'])==0&&strlen($menu[$m]['childtype'])===0){
				if(isset($menudata[$menu[$m]['inumber']]['mnumber'])){
					$product_list[($index+1)]['id']='size'.$menu[$m]['inumber'];
					$product_list[($index+1)]['name']="容量";
					$product_list[($index+1)]['type']=20;
					$product_list[($index+1)]['sub_type']=10;
					$product_list[($index+1)]['sort']=$menu[$m]['frontseq'];
					$product_list[($index+1)]['price']=null;
					$product_list[($index+1)]['description']=null;
					$product_list[($index+1)]['is_mandatory']=1;
					$product_list[($index+1)]['is_common']=0;
					$product_list[($index+1)]['is_visible']=1;
					$product_list[($index+1)]['min_limit']=1;
					$product_list[($index+1)]['max_limit']=1;
					$product_list[($index+1)]['calorie']=null;
					$adindex=sizeof($adjust_list);
					$adjust_list[$adindex]['parent_id']='size'.$menu[$m]['inumber'];
					$checkmoney=1;
					for($money=1;$money<=6;$money++){
						if(!isset($menudata[$menu[$m]['inumber']]['mwebvisible'.$money])||$menudata[$menu[$m]['inumber']]['mwebvisible'.$money]==='1'){
							if($checkmoney<=$menudata[$menu[$m]['inumber']]['mnumber']&&$menudata[$menu[$m]['inumber']]['money'.$money]!=""){
								$product_list[($index+1+$checkmoney)]['id']='size'.$menu[$m]['inumber'].'money'.$money;
								$product_list[($index+1+$checkmoney)]['name']=($menudata[$menu[$m]['inumber']]['mname'.$money.'1']==="")?($menudata[$menu[$m]['inumber']]['money'.$money]):($menudata[$menu[$m]['inumber']]['mname'.$money.'1']);
								$product_list[($index+1+$checkmoney)]['type']=2;
								$product_list[($index+1+$checkmoney)]['sub_type']=12;
								$product_list[($index+1+$checkmoney)]['sort']=$menu[$m]['frontseq'];
								$product_list[($index+1+$checkmoney)]['price']=($menudata[$menu[$m]['inumber']]['money'.$money]-$menudata[$menu[$m]['inumber']]['money1']);
								$product_list[($index+1+$checkmoney)]['description']=null;
								$product_list[($index+1+$checkmoney)]['is_mandatory']=($checkmoney==1&&$menudata[$menu[$m]['inumber']]['mnumber']==1)?(1):(0);
								$product_list[($index+1+$checkmoney)]['is_common']=($checkmoney==1)?(1):(0);
								$product_list[($index+1+$checkmoney)]['is_visible']=1;
								$product_list[($index+1+$checkmoney)]['min_limit']=($checkmoney==1&&$menudata[$menu[$m]['inumber']]['mnumber']==1)?(1):(0);
								$product_list[($index+1+$checkmoney)]['max_limit']=($checkmoney==1&&$menudata[$menu[$m]['inumber']]['mnumber']==1)?(1):(0);
								$product_list[($index+1+$checkmoney)]['calorie']=null;
								$checkmoney++;

								$adjust_list[$adindex]['child_list'][]='size'.$menu[$m]['inumber'].'money'.$money;
							}
							else{
								break;
							}
						}
						else{
						}
					}
				}
				else{
				}

				$comindex=sizeof($combination_list);
				$combination_list[$comindex]['name']=$menudata[$menu[$m]['inumber']]['name1'].'一般';
				$combination_list[$comindex]['price']=null;
				$combination_list[$comindex]['product']=array("product_id"=>$menu[$m]['inumber'],"price"=>null);
				$combination_list[$comindex]['adjust_list'][]=array("product_id"=>'size'.$menu[$m]['inumber'],"price"=>null);

				if($menu[$m]['taste']!=''){
					$temp=preg_split('/;/',$menu[$m]['taste']);
					$privatetaste=preg_split('/,/',$temp[1]);
					for($prt=0;$prt<sizeof($privatetaste);$prt++){
						if((!isset($tastedata[$privatetaste[$prt]]['state'])||$tastedata[$privatetaste[$prt]]['state']==='1')&&(!isset($tastedata[$privatetaste[$prt]]['webvisible'])||$tastedata[$privatetaste[$prt]]['webvisible']==='1')){
							if(isset($tastedata[$privatetaste[$prt]]['public'])&&$tastedata[$privatetaste[$prt]]['public']==='0'){
								if(in_array($privatetaste[$prt],$insertedtaste)){
								}
								else{
									$insertedtaste[]=$privatetaste[$prt];
									if(isset($tastedata[$privatetaste[$prt]]['group'])&&isset($tastegroupdata)&&isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']])&&isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type'])&&$tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type']!='0'){
										$index=sizeof($product_list);
										$product_list[$index]['id']=$privatetaste[$prt];
										$product_list[$index]['name']=$tastedata[$privatetaste[$prt]]['name1'];
										$product_list[$index]['type']=2;
										$product_list[$index]['sub_type']=(isset($tastedata[$privatetaste[$prt]]['sub_type']))?($tastedata[$privatetaste[$prt]]['sub_type']):((isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type'])&&$tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type']==='30')?(31):((isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type'])&&$tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type']==='40')?(42):(0)));
										$product_list[$index]['sort']=$tastedata[$privatetaste[$prt]]['seq'];
										$product_list[$index]['price']=($tastedata[$privatetaste[$prt]]['money']!='')?($tastedata[$privatetaste[$prt]]['money']):(0);
										$product_list[$index]['description']=null;
										$product_list[$index]['is_mandatory']=0;
										$product_list[$index]['is_common']=0;
										$product_list[$index]['is_visible']=1;
										$product_list[$index]['min_limit']=0;
										$product_list[$index]['max_limit']=(isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type'])&&($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type']==='30'||$tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['type']==='40'))?(1):(($tastedata[$privatetaste[$prt]]['money']!==''||$tastedata[$privatetaste[$prt]]['money']!=='0')?(0):(1));
										$product_list[$index]['calorie']=null;
									}
									else{
										$index=sizeof($product_list);
										$product_list[$index]['id']=$privatetaste[$prt];
										$product_list[$index]['name']=$tastedata[$privatetaste[$prt]]['name1'];
										$product_list[$index]['type']=2;
										$product_list[$index]['sub_type']=0;
										$product_list[$index]['sort']=$tastedata[$privatetaste[$prt]]['seq'];
										$product_list[$index]['price']=($tastedata[$privatetaste[$prt]]['money']!='')?($tastedata[$privatetaste[$prt]]['money']):(0);
										$product_list[$index]['description']=null;
										$product_list[$index]['is_mandatory']=0;
										$product_list[$index]['is_common']=0;
										$product_list[$index]['is_visible']=1;
										$product_list[$index]['min_limit']=0;
										$product_list[$index]['max_limit']=($tastedata[$privatetaste[$prt]]['money']!==''&&$tastedata[$privatetaste[$prt]]['money']!=='0')?(0):(1);
										$product_list[$index]['calorie']=null;
									}
								}
								if(isset($tastedata[$privatetaste[$prt]]['group'])&&$tastedata[$privatetaste[$prt]]['group']!=''){
								}
								else{
									$tastedata[$privatetaste[$prt]]['group']='public';
								}
								if(isset($privatetastegroup[$tastedata[$privatetaste[$prt]]['group']])){
								}
								else{
									$adindex=sizeof($adjust_list);
									$adjust_list[$adindex]['parent_id']=$menu[$m]['inumber'].'private'.$tastedata[$privatetaste[$prt]]['group'];
									$privatetastegroup[$tastedata[$privatetaste[$prt]]['group']]=$adindex;
									$combination_list[$comindex]['adjust_list'][]=array("product_id"=>$menu[$m]['inumber'].'private'.$tastedata[$privatetaste[$prt]]['group'],"price"=>null);

									$index=sizeof($product_list);
									$product_list[$index]['id']=$menu[$m]['inumber'].'private'.$tastedata[$privatetaste[$prt]]['group'];
									$product_list[$index]['name']=(isset($tastegroupdata)&&isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']])&&isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['name']))?($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['name']):("調整項目");
									$product_list[$index]['type']=20;
									$product_list[$index]['sub_type']=0;
									$product_list[$index]['sort']=1;
									$product_list[$index]['price']=null;
									$product_list[$index]['description']=null;
									$product_list[$index]['is_mandatory']=0;
									$product_list[$index]['is_common']=0;
									$product_list[$index]['is_visible']=1;
									$product_list[$index]['min_limit']=0;
									$product_list[$index]['max_limit']=(isset($tastedata[$privatetaste[$prt]])&&isset($tastegroupdata)&&isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']])&&isset($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['pos'])&&$tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['pos']!=='-1')?($tastegroupdata[$tastedata[$privatetaste[$prt]]['group']]['pos']):(0);
									$product_list[$index]['calorie']=null;
								}
								$adjust_list[$privatetastegroup[$tastedata[$privatetaste[$prt]]['group']]]['child_list'][]=$privatetaste[$prt];
							}
							else{
							}
						}
						else{
						}
					}
					if(sizeof($publictasteitem)>0){
						foreach($publictastegroup as $groupcode=>$groupindex){
							$combination_list[$comindex]['adjust_list'][]=array("product_id"=>($groupcode==='public')?('public'):($groupcode),"price"=>null);
						}
					}
					else{
					}
				}
				else{//沒有專屬備註
					if(sizeof($publictasteitem)>0){
						foreach($publictastegroup as $groupcode=>$groupindex){
							$combination_list[$comindex]['adjust_list'][]=array("product_id"=>($groupcode==='public')?('public'):($groupcode),"price"=>null);
						}
					}
					else{
					}
				}
			}
			else{
				$packindex=sizeof($package_list);
				$package_list[$packindex]['product_id']=$menu[$m]['inumber'];
				$childgroup=preg_split('/,/',$menu[$m]['childtype']);
				for($cg=0;$cg<sizeof($childgroup);$cg++){
					$childitem=preg_split('/;/',$childgroup[$cg]);

					for($ci=0;$ci<sizeof($childitem);$ci++){
						$childcode=preg_split('/-/',$childitem[$ci]);

						if($ci!=0){
						}
						else{
							$blockindex=sizeof($package_list[$packindex]['block_list']);
							$package_list[$packindex]['block_list'][$blockindex]['name']=null;
							$package_list[$packindex]['block_list'][$blockindex]['is_mandatory']=1;
							$package_list[$packindex]['block_list'][$blockindex]['min_limit']=(isset($childcode[2]))?($childcode[2]):(1);
							$package_list[$packindex]['block_list'][$blockindex]['max_limit']=(isset($childcode[2]))?($childcode[2]):(1);
						}
						
						$blockofproductindex=sizeof($package_list[$packindex]['block_list'][$blockindex]['product_list']);
						$package_list[$packindex]['block_list'][$blockindex]['product_list'][$blockofproductindex]['product_id']=$childcode[1];
						$package_list[$packindex]['block_list'][$blockindex]['product_list'][$blockofproductindex]['is_mandatory']=(isset($childcode[2]))?(0):(1);
						$package_list[$packindex]['block_list'][$blockindex]['product_list'][$blockofproductindex]['min_limit']=(isset($childcode[2]))?($childcode[2]):(1);
						$package_list[$packindex]['block_list'][$blockindex]['product_list'][$blockofproductindex]['max_limit']=(isset($childcode[2]))?($childcode[2]):(1);
						$package_list[$packindex]['block_list'][$blockindex]['product_list'][$blockofproductindex]['price']=0;
					}
				}
			}
		}
		else{
		}
	}
	$menufornidin=array('product_list'=>$product_list,'category_list'=>$category_list,'adjust_list'=>$adjust_list,'combination_list'=>$combination_list,'package_list'=>$package_list);

	return $menufornidin;
}
function SyncMenu($url,$method,$MC_API_Token,$MC_API_User,$menu){
	$PostData = array(
		"menu_type"=>1,
		"menu"=>$menu
	);
	$header=array('Content-Type: application/json');
	$header[]='MC-API-Token: '.$MC_API_Token;
	$header[]='MC-API-User: '.$MC_API_User;

	$f=fopen('./menujson.txt','w');
	fwrite($f,print_r($PostData,true));
	fclose($f);
	return php_curl_ajax($method,$url.'/menu/syncStoreMenu',$header,$PostData);
}
?>