<?php
//include_once '../tool/dbTool.inc.php';
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
$initsetting=parse_ini_file('../database/initsetting.ini',true);
if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($initsetting['init']['settime']);
$floorspend=parse_ini_file('../database/floorspend.ini',true);
$dir=('./table');
$filelist=scandir($dir);
$max=0;
if(isset($floorspend['TA']['page'])&&intval($floorspend['TA']['page'])>1){//桌控有分樓層(區域)
	$startindex=1;
	for($page=1;$page<=intval($floorspend['TA']['page']);$page++){
		if(isset($floorspend['TA']['row'.$page])){
			$pagerow=$floorspend['TA']['row'.$page];
		}
		else{
			$pagerow=$floorspend['TA']['row'];
		}
		if(isset($floorspend['TA']['col'.$page])){
			$pagecol=$floorspend['TA']['col'.$page];
		}
		else{
			$pagecol=$floorspend['TA']['col'];
		}
		if(isset($_POST['tablepage'])&&$_POST['tablepage']=='page'.$page.'button'){
			for($i=$startindex;$i<=(intval($pagecol)*intval($pagerow)+$startindex-1);$i++){
				if($floorspend['T'.$i]['tablename']!=""){
					$t='';
					$time='';
					$bizdate='';
					$consecnumber='';
					$saleamt='';
					$person='';
					$createdatetime='';
					$nowtime=date_create(date('YmdHis'));
					$splitnum=0;
					foreach($filelist as $fl){
						if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))||preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))){//strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.')||strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'-')
							$tbindex=preg_split('/;/',$fl);
							$tbindex=substr($tbindex[2],0,strlen($tbindex[2])-4);
							if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))/*$fl=='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.ini'*/&&($splitnum==0||(!isset($result[$floorspend['T'.$i]['tablename']]['inittablenum'])||preg_match('/-/',$result[$floorspend['T'.$i]['tablename']]['inittablenum'])))){
								$t='';
								$tabledata=parse_ini_file('./table/'.$fl,true);
								//echo $fl;
								if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
								}
								else{
									foreach($tabledata as $tdname=>$td){
										if(/*$td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&*/$td['consecnumber']!=""){
											if(strlen($t)==0){
												$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
												$diff=date_diff($nowtime,$maxtime);
												$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
												if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
													$t=1;
													$time=floatval($mins);
												}
												else if(floatval($mins)<=0){
													$t=-1;
													$time=0;
												}
												else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
													$t=-2;
													$time=floatval($mins);
												}
												else{
													$t=2;
													$time=floatval($mins);
												}

												$result[$floorspend['T'.$i]['tablename']]['inittablenum']=$tdname;
												$result[$floorspend['T'.$i]['tablename']]['persons']=$td['person'];
												$result[$floorspend['T'.$i]['tablename']]['money']=$td['saleamt'];
												$result[$floorspend['T'.$i]['tablename']]['bizdate']=$td['bizdate'];
												$result[$floorspend['T'.$i]['tablename']]['state']=$td['state'];
												$result[$floorspend['T'.$i]['tablename']]['consecnumber']=$td['consecnumber'];
												$result[$floorspend['T'.$i]['tablename']]['remarks']='1';
												$result[$floorspend['T'.$i]['tablename']]['mins']=$time;
												if(isset($result[$floorspend['T'.$i]['tablename']]['splitnum'])){
													$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
												}
												else{
													$result[$floorspend['T'.$i]['tablename']]['splitnum']=1;
												}
												$splitnum=$result[$floorspend['T'.$i]['tablename']]['splitnum'];
											}
											else{
											}
										}
										else{
										}
									}
								}
							}
							else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))/*$fl=='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.ini'*/){
								$tabledata=parse_ini_file('./table/'.$fl,true);
								if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
								}
								else{
									$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
								}
							}
							else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))/*$fl!='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.ini'*/&&$splitnum==0){
								$tabledata=parse_ini_file('./table/'.$fl,true);
								if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
								}
								else{
									$splitnum++;
									foreach($tabledata as $tdname=>$td){
										if(/*$td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&*/$td['consecnumber']!=""){
											if(strlen($t)==0){
												$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
												$diff=date_diff($nowtime,$maxtime);
												$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
												if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
													$t=1;
													$time=floatval($mins);
												}
												else if(floatval($mins)<=0){
													$t=-1;
													$time=0;
												}
												else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
													$t=-2;
													$time=floatval($mins);
												}
												else{
													$t=2;
													$time=floatval($mins);
												}

												$result[$floorspend['T'.$i]['tablename']]['inittablenum']=$tdname;
												$result[$floorspend['T'.$i]['tablename']]['persons']=$td['person'];
												$result[$floorspend['T'.$i]['tablename']]['money']=$td['saleamt'];
												$result[$floorspend['T'.$i]['tablename']]['bizdate']=$td['bizdate'];
												$result[$floorspend['T'.$i]['tablename']]['state']=$td['state'];
												$result[$floorspend['T'.$i]['tablename']]['consecnumber']=$td['consecnumber'];
												$result[$floorspend['T'.$i]['tablename']]['remarks']='1';
												$result[$floorspend['T'.$i]['tablename']]['mins']=$time;
												$result[$floorspend['T'.$i]['tablename']]['splitnum']=1;
											}
											else{
											}
										}
										else{
										}
									}
								}
							}
							else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))/*$fl!='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.ini'*/){
								$tabledata=parse_ini_file('./table/'.$fl,true);
								if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
								}
								else{
									$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
								}
							}
						}
						else{
						}
					}
					$max++;
				}
				else{
					continue;
				}
			}
			break;
		}
		else if(isset($_POST['tablepage'])&&$_POST['tablepage']!='page'.$page.'button'){
		}
		else{
			for($i=$startindex;$i<=(intval($pagecol)*intval($pagerow)+$startindex-1);$i++){
				if($floorspend['T'.$i]['tablename']!=""){
					$t='';
					$time='';
					$bizdate='';
					$consecnumber='';
					$saleamt='';
					$person='';
					$createdatetime='';
					$nowtime=date_create(date('YmdHis'));
					$splitnum=0;
					foreach($filelist as $fl){
						if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))||preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))){
							$tbindex=preg_split('/;/',$fl);
							$tbindex=substr($tbindex[2],0,strlen($tbindex[2])-4);
							if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))/*$fl=='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$floorspend['T'.$i]['tablename'].'.ini'*/&&($splitnum==0||(!isset($result[$floorspend['T'.$i]['tablename']]['inittablenum'])||preg_match('/-/',$result[$floorspend['T'.$i]['tablename']]['inittablenum'])))){
								$t='';
								$tabledata=parse_ini_file('./table/'.$fl,true);
								if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
								}
								else{
									foreach($tabledata as $tdname=>$td){
										if(/*$td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&*/$td['consecnumber']!=""){
											if(strlen($t)==0){
												$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
												$diff=date_diff($nowtime,$maxtime);
												$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
												if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
													$t=1;
													$time=floatval($mins);
												}
												else if(floatval($mins)<=0){
													$t=-1;
													$time=0;
												}
												else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
													$t=-2;
													$time=floatval($mins);
												}
												else{
													$t=2;
													$time=floatval($mins);
												}

												$result[$floorspend['T'.$i]['tablename']]['inittablenum']=$tdname;
												$result[$floorspend['T'.$i]['tablename']]['persons']=$td['person'];
												$result[$floorspend['T'.$i]['tablename']]['money']=$td['saleamt'];
												$result[$floorspend['T'.$i]['tablename']]['bizdate']=$td['bizdate'];
												$result[$floorspend['T'.$i]['tablename']]['state']=$td['state'];
												$result[$floorspend['T'.$i]['tablename']]['consecnumber']=$td['consecnumber'];
												$result[$floorspend['T'.$i]['tablename']]['remarks']='1';
												$result[$floorspend['T'.$i]['tablename']]['mins']=$time;
												if(isset($result[$floorspend['T'.$i]['tablename']]['splitnum'])){
													$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
												}
												else{
													$result[$floorspend['T'.$i]['tablename']]['splitnum']=1;
												}
												$splitnum=$result[$floorspend['T'.$i]['tablename']]['splitnum'];
											}
											else{
											}
										}
										else{
										}
									}
								}
							}
							else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))/*$fl=='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$floorspend['T'.$i]['tablename'].'.ini'*/){
								$tabledata=parse_ini_file('./table/'.$fl,true);
								if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
								}
								else{
									$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
								}
							}
							else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))/*$fl!='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$floorspend['T'.$i]['tablename'].'.ini'*/&&$splitnum==0){
								$tabledata=parse_ini_file('./table/'.$fl,true);
								if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
								}
								else{
									$splitnum++;
									foreach($tabledata as $tdname=>$td){
										if(/*$td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&*/$td['consecnumber']!=""){
											if(strlen($t)==0){
												$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
												$diff=date_diff($nowtime,$maxtime);
												$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
												if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
													$t=1;
													$time=floatval($mins);
												}
												else if(floatval($mins)<=0){
													$t=-1;
													$time=0;
												}
												else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
													$t=-2;
													$time=floatval($mins);
												}
												else{
													$t=2;
													$time=floatval($mins);
												}

												$result[$floorspend['T'.$i]['tablename']]['inittablenum']=$tdname;
												$result[$floorspend['T'.$i]['tablename']]['persons']=$td['person'];
												$result[$floorspend['T'.$i]['tablename']]['money']=$td['saleamt'];
												$result[$floorspend['T'.$i]['tablename']]['bizdate']=$td['bizdate'];
												$result[$floorspend['T'.$i]['tablename']]['state']=$td['state'];
												$result[$floorspend['T'.$i]['tablename']]['consecnumber']=$td['consecnumber'];
												$result[$floorspend['T'.$i]['tablename']]['remarks']='1';
												$result[$floorspend['T'.$i]['tablename']]['mins']=$time;
												$result[$floorspend['T'.$i]['tablename']]['splitnum']=1;
											}
											else{
											}
										}
										else{
										}
									}
								}
							}
							else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))/*$fl!='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.$floorspend['T'.$i]['tablename'].'.ini'*/){
								$tabledata=parse_ini_file('./table/'.$fl,true);
								if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
								}
								else{
									$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
								}
							}
						}
						else{
						}
					}
					$max++;
				}
				else{
					continue;
				}
			}
		}
		$startindex+=(intval($pagecol)*intval($pagerow));
	}
}
else{//桌控不分樓層(區域)
	if(isset($floorspend['TA']['row1'])){
		$pagerow=$floorspend['TA']['row1'];
	}
	else{
		$pagerow=$floorspend['TA']['row'];
	}
	if(isset($floorspend['TA']['col1'])){
		$pagecol=$floorspend['TA']['col1'];
	}
	else{
		$pagecol=$floorspend['TA']['col'];
	}
	for($i=1;$i<=(intval($pagecol)*intval($pagerow));$i++){
		if($floorspend['T'.$i]['tablename']!=""){//具有桌號，表示該位置為實體桌；反之，表示該位置為空白區域(無桌子)
			$t='';
			$time='';
			$bizdate='';
			$consecnumber='';
			$saleamt='';
			$person='';
			$createdatetime='';
			$nowtime=date_create(date('YmdHis'));
			$splitnum=0;
			foreach($filelist as $fl){
				if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))||preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))){
					$tbindex=preg_split('/;/',$fl);
					$tbindex=substr($tbindex[2],0,strlen($tbindex[2])-4);
					if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))/*$fl=='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.ini'*/&&($splitnum==0||(!isset($result[$floorspend['T'.$i]['tablename']]['inittablenum'])||preg_match('/-/',$result[$floorspend['T'.$i]['tablename']]['inittablenum'])))){
						$t='';
						$tabledata=parse_ini_file('./table/'.$fl,true);
						if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
						}
						else{
							$splitnum++;
							foreach($tabledata as $tdname=>$td){
								if(/*$td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&*/$td['consecnumber']!=""){
									if(strlen($t)==0){
										$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
										$diff=date_diff($nowtime,$maxtime);
										$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
										if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
											$t=1;
											$time=floatval($mins);
										}
										else if(floatval($mins)<=0){
											$t=-1;
											$time=0;
										}
										else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
											$t=-2;
											$time=floatval($mins);
										}
										else{
											$t=2;
											$time=floatval($mins);
										}

										$result[$floorspend['T'.$i]['tablename']]['inittablenum']=$tdname;
										$result[$floorspend['T'.$i]['tablename']]['persons']=$td['person'];
										$result[$floorspend['T'.$i]['tablename']]['money']=$td['saleamt'];
										$result[$floorspend['T'.$i]['tablename']]['bizdate']=$td['bizdate'];
										$result[$floorspend['T'.$i]['tablename']]['state']=$td['state'];
										$result[$floorspend['T'.$i]['tablename']]['consecnumber']=$td['consecnumber'];
										$result[$floorspend['T'.$i]['tablename']]['remarks']='1';
										$result[$floorspend['T'.$i]['tablename']]['mins']=$time;
										if(isset($result[$floorspend['T'.$i]['tablename']]['splitnum'])){
											$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
										}
										else{
											$result[$floorspend['T'.$i]['tablename']]['splitnum']=1;
										}
										$splitnum=$result[$floorspend['T'.$i]['tablename']]['splitnum'];
									}
									else{
									}
								}
								else{
								}
							}
						}
					}
					else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'.ini/',iconv('big5','utf-8',$fl))/*$fl=='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.ini'*/){
						$tabledata=parse_ini_file('./table/'.$fl,true);
						if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
						}
						else{
							$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
						}
					}
					else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))/*$fl!='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.ini'*/&&$splitnum==0){
						$tabledata=parse_ini_file('./table/'.$fl,true);
						if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
						}
						else{
							$splitnum++;
							foreach($tabledata as $tdname=>$td){
								if(/*$td['bizdate']==$timeini['time']['bizdate']&&$td['zcounter']==$timeini['time']['zcounter']&&*/$td['consecnumber']!=""){
									if(strlen($t)==0){
										$maxtime=date_create(date('YmdHis',strtotime($td['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
										$diff=date_diff($nowtime,$maxtime);
										$mins=floatval(floatval($diff->format('%R%d'))*1440)+floatval(floatval($diff->format('%R%h'))*60)+floatval(floatval($diff->format('%R%i')));
										if(floatval($mins)>floatval($initsetting['init']['hinttime'])){
											$t=1;
											$time=floatval($mins);
										}
										else if(floatval($mins)<=0){
											$t=-1;
											$time=0;
										}
										else if(floatval($mins)<floatval($initsetting['init']['sechinttime'])){
											$t=-2;
											$time=floatval($mins);
										}
										else{
											$t=2;
											$time=floatval($mins);
										}

										$result[$floorspend['T'.$i]['tablename']]['inittablenum']=$tdname;
										$result[$floorspend['T'.$i]['tablename']]['persons']=$td['person'];
										$result[$floorspend['T'.$i]['tablename']]['money']=$td['saleamt'];
										$result[$floorspend['T'.$i]['tablename']]['bizdate']=$td['bizdate'];
										$result[$floorspend['T'.$i]['tablename']]['state']=$td['state'];
										$result[$floorspend['T'.$i]['tablename']]['consecnumber']=$td['consecnumber'];
										$result[$floorspend['T'.$i]['tablename']]['remarks']='1';
										$result[$floorspend['T'.$i]['tablename']]['mins']=$time;
										$result[$floorspend['T'.$i]['tablename']]['splitnum']=1;
									}
									else{
									}
								}
								else{
								}
							}
						}
					}
					else if(preg_match('/;'.$floorspend['T'.$i]['tablename'].'-\d.ini/',iconv('big5','utf-8',$fl))/*$fl!='./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$floorspend['T'.$i]['tablename']).'.ini'*/){
						$tabledata=parse_ini_file('./table/'.$fl,true);
						if($tabledata[iconv('big5','utf-8',$tbindex)]['consecnumber']==''){
						}
						else{
							$result[$floorspend['T'.$i]['tablename']]['splitnum']++;
						}
					}
				}
				else{
				}
			}
			$max++;
		}
		else{
			continue;
		}
	}
}

$dir='./table/outside';
$filelist=scandir($dir,1);
foreach($filelist as $fl){
	if(strstr($fl,$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'])){
		if(isset($result['outside'])){
			$result['outside']++;
		}
		else{
			$result['outside']=1;
		}
	}
	else{
		if(isset($result['outside'])){
		}
		else{
			$result['outside']=0;
		}
	}
}
//$result['time']='尚有'.(intval($max)-sizeof($result)+1).$floorspend['TA']['unit'].' '.date('H:i:s');
$result['remaining']='尚有'.(intval($max)-sizeof($result)+1).$floorspend['TA']['unit'];
$result['time']=date('H:i:s');
echo json_encode($result);

//2020/10/22 可能會轉由nodejs觸發，先註解
/*if(file_exists('./table/tablereload/reload.txt')){
	unlink('./table/tablereload/reload.txt');
}
else{
}*/
?>