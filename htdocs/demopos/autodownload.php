<!doctype html>
<html lang="en">
<head>
<?php

$initsetting=parse_ini_file('../database/initsetting.ini',true);
$machinedata=parse_ini_file('../database/machinedata.ini',true);
$setup=parse_ini_file('../database/setup.ini',true);
if(isset($machinedata['posdvr']['key'])&&$machinedata['posdvr']['key']!=''){
	setCookie('auth',$machinedata['posdvr']['key'],time()+86400,'/','quickcode.com.tw');
}
else{
}
if(isset($initsetting['init']['posdvr'])&&$initsetting['init']['posdvr']=='1'){//錢都錄API介接function
?>
<script src="./lib/api/posdvr/posdvr_api.js?<?php echo date('YmdHis'); ?>"></script>
<?php
}
else{
}
?>
	  <meta charset="UTF-8">
	  <script type="text/javascript" src='../nodejs/node_modules/socket.io-client/dist/socket.io.js'></script>
	  <!-- <title>line自動下載</title> -->
	  <script type="text/javascript" src="../tool/jquery-1.12.4.js"></script>
	  <script>
		function roundfun(number,precision){
			return Math.round(Number(number) * Math.pow(10, Number((precision || 0)))) / Math.pow(10, Number((precision || 0)));
		}
		function ceilfun(number,precision){
			return Math.ceil(Math.ceil(Number(number) * Math.pow(10, Number((precision || 0)) + 1)) / 10) / Math.pow(10, Number((precision || 0)));
		}
		function floorfun(number,precision){
			return Math.floor(Math.floor(Number(number) * Math.pow(10, Number((precision || 0)) + 1)) / 10) / Math.pow(10, Number((precision || 0)));
		}
		$(document).ready(function(){
			document.title="line自動下載";
			setInterval(function(){
				$.ajax({
					url:'./lib/js/create.cmdtxt.php',
					method:'post',
					async: false,
					data:{'cmd':'report'},
					dataType:'html',
					success:function(d){
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
			},30000);
			var sec=0;
			var lock=0;
			var lock2=0;
			var start=1;
			function check(){
				var classstate='1';
				$.ajax({
					url:'./lib/js/checkopen.ajax.php',
					method:'post',
					async:false,
					cache:false,
					data:{'machinetype':'m1'},
					dataType:'html',
					success:function(d){
						//console.log(d);
						if(d=='success'){//已開班
							classstate='1';
						}
						else{//已關班
							classstate='0';
						}
					},
					error:function(e){
						//console.log(e);
					}
				});
				$.ajax({
					url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/js/downweb.ajax.php',
					method:'post',
					async:false,
					cache:false,
					data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>",'classstate':classstate},
					dataType:'json',
					timeout:5000,
					success:function(d){
						if(JSON.stringify(d).match('Notice')){
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php '+d}
							});
						}
						else{
						}
						//console.log(d);
						if(d=='empty'){<?php /*無網路訂單*/ ?>
						}
						else{<?php /*有網路訂單*/ ?>
							/*$.ajax({//若在此更新訂單狀態，"疑似"會產生一張單下載兩次的情況，因此將更新訂單狀態提前至downweb.ajax.php中
								url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/js/receveweb.ajax.php',
								method:'post',
								async:false,
								data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>"},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});*/
							d[-1]="<?php echo $setup['basic']['company']; ?>";
							d[-2]="<?php echo $setup['basic']['story']; ?>";
							//console.log(d);
							$.ajax({
								url:'./lib/js/pushlistdata.ajax.php',
								method:'post',
								async:false,
								data:{'data':d,'machinetype':'rightnow'},
								dataType:'json',
								success:function(d){
									if(JSON.stringify(d).match('Notice')){
										//console.log('match');
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'autodownload.php pushlistdata.ajax.php '+d}
										});
									}
									else{
										//console.log('error');
									}
									//console.log(d);
									$.ajax({
										url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/js/changeweb.ajax.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>",'data':d},
										dataType:'html',
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php changeweb.ajax.php '+d}
												});
											}
											else{
											}
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
									$.ajax({
										url:'./lib/js/weborder.content.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'autosale':<?php if(isset($initsetting['init']['webautosale']))echo $initsetting['init']['webautosale'];else echo '0'; ?>,'data':d},
										dataType:'json',
										timeout:5000,
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php weborder.content.php '+d}
												});
											}
											else{
											}
											//console.log(d);
											$.each(d,function(consecnumber,data){
												//console.log(consecnumber);
												//console.log(data);
												var total=0;
												var subtotal=0;
												var qty=0;
												var temptotal=0;
												var tempdis=0;
												for(var item=0;item<data['linenumber[]'].length;item++){
													total=Number(total)+(Number(data['money[]'][item])*Number(data['number[]'][item]));
													subtotal=Number(subtotal)+(Number(data['subtotal[]'][item]));
													qty=Number(qty)+(Number(data['number[]'][item]));
													if(data['needcharge[]'][item]=='1'){
														temptotal=Number(temptotal)+(Number(data['money[]'][item])*Number(data['number[]'][item]));
														tempdis=Number(tempdis)+Number(data['discount[]'][item]);
													}
													else{
													}
												}
												/**$.each($('.order#order #tabs4 form[data-id="listform"] .label'),function(index,value){
													total=Number(total)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
													subtotal=Number(subtotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="subtotal[]"]').val()));
													qty=Number(qty)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="qty[]"]').val()));
													if($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="needcharge[]"]').val()=='1'){
														temptotal=Number(temptotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
														tempdis=Number(tempdis)+Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="discount[]"]').val());
													}
													else{
													}
												});*/
												if(<?php if(isset($initsetting['init']['charge'])&&$initsetting['init']['charge']=='1')echo '1';else echo '0'; ?>){
													if(<?php if(isset($initsetting['init']['openchar'])&&$initsetting['init']['openchar']=='1'&&isset($initsetting['init']['charge'])&&$initsetting['init']['charge']=='1')echo '1';else echo '0'; ?>){
														if(<?php if(isset($initsetting['init']['chargeeq'])&&$initsetting['init']['chargeeq']=='2')echo '1';else echo '0'; ?>){//服務費以折扣後之價格計算
															data['charge']=(Number(temptotal)-Number(tempdis))*Number(<?php echo $initsetting['init']['chargenumber']; ?>)/100;
														}
														else{//服務費以原價格計算
															data['charge']=Number(temptotal)*Number(<?php echo $initsetting['init']['chargenumber']; ?>)/100;
														}
														var precision=parseInt(<?php echo $initsetting['init']['accuracy']; ?>);//可由設定檔設定精準度(e.g.精準度小數點第二位 填2)
														//設定檔內可設定使用何種進位
														if(<?php if($initsetting['init']['accuracytype']=='1')echo '1';else echo '0'; ?>){//四捨五入
															data['charge']=roundfun(data['charge'],precision);
														}
														else if(<?php if($initsetting['init']['accuracytype']=='2')echo '1';else echo '0'; ?>){//無條件進位
															data['charge']=ceilfun(data['charge'],precision);
														}
														else{//無條件捨去
															data['charge']=floorfun(data['charge'],precision);
														}
													}
													else{
													}
												}
												else{
												}
												//console.log(data);
												if(<?php if($initsetting['init']['autodis']=='1')echo '1';else echo '0'; ?>){//開啟自動優惠
													$.ajax({
														url:'./lib/js/compdis.ajax.php',
														method:'post',
														async: false,
														data:data,
														dataType:'html',
														success:function(d){
															//console.log(d);
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'temp compdis.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(data);
															var temp=d.split(';');
															if($.isNumeric(temp[0])){
																$.ajax({
																	url:'./lib/js/wttempdis.ajax.php',
																	method:'post',
																	async: false,
																	data:{'bizdate':data['bizdate'],'consecnumber':consecnumber,'autodis':temp[0],'autodiscontent':temp[1],'autodispermoney':temp[2]},
																	dataType:'html',
																	success:function(d){
																		if(d.length>20){
																			$.ajax({
																				url:'./lib/js/print.php',
																				method:'post',
																				data:{'html':'temp wttempdis.ajax.php '+d},
																				dataType:'html',
																				success:function(d){
																					//console.log(d);
																				},
																				error:function(e){
																					//console.log(e);
																				}
																			});
																		}
																		else{
																		}
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
																//$('.result #viewwindow #autodis').html(temp[0]);
																//$('.result #viewwindow input[name="autodiscontent"]').val(temp[1]);
																//$('.result #viewwindow input[name="autodispremoney"]').val(temp[2]);
															}
															else{
																//console.log('fail');
															}
															//console.log(d);
														},
														error:function(e){
															//console.log(e)
														}
													});
												}
												else{
												}
												data['machinetype']='m1';
												//console.log(data);
												//console.log(data['consecnumber']);
												$.ajax({
													url:'./lib/js/create.webkds.php',
													method:'post',
													async:false,
													data:data,
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.list.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.list.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.kitchen.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														//console.log(d);
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.kitchen.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.tag.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.tag.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												//console.log('consecnumber:'+consecnumber);
												if(<?php if(isset($initsetting['init']['webautosale'])&&$initsetting['init']['webautosale']=='1')echo '1';else echo '0'; ?>){//下載後直接結帳
													$.ajax({
														url:'./lib/js/temptodb.ajax.php',
														method:'post',
														async:false,
														data:{'bizdate':data['bizdate'],'terminalnumber':'rightnow','numbertag':consecnumber},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'sale temptodb.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(d);
															//console.log('轉移正式'+consecnumber);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
												else{
													//console.log('error');
												}
												
											});
										},
										error:function(e){
											//console.log(e);
										}
									});
								},
								error:function(e){
									//console.log(e.responseText);
									if(e.responseText.match('Notice')){
										//console.log('match');
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'autodownload.php pushlistdata.ajax.php error '+e.responseText}
										});
									}
									else{
										//console.log('error');
									}
								}
							});
						}
					},
					error:function(e,status){
						if(e.responseText.match('Notice')){
							//console.log('match');
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php error '+e.responseText}
							});
						}
						else{
							//console.log('error');
						}
						if(status==='timeout'){
							//console.log('timeout');
						}
						else{
							//console.log(e);
						}
					}
				});
			};
			function checkfoodpanda(){
				$.ajax({
					url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/foodpanda/getlist.ajax.php',
					method:'post',
					async:false,
					cache:false,
					data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>"},
					dataType:'json',
					timeout:5000,
					success:function(d){
						if(JSON.stringify(d).match('Notice')){
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php '+d}
							});
						}
						else{
						}
						//console.log(d);
						if(d=='empty'){<?php /*無網路訂單*/ ?>
						}
						else{<?php /*有網路訂單*/ ?>
							/*$.ajax({//若在此更新訂單狀態，"疑似"會產生一張單下載兩次的情況，因此將更新訂單狀態提前至downweb.ajax.php中
								url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/js/receveweb.ajax.php',
								method:'post',
								async:false,
								data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>"},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});*/
							d[-1]="<?php echo $setup['basic']['company']; ?>";
							d[-2]="<?php echo $setup['basic']['story']; ?>";
							//console.log(d);
							$.ajax({
								url:'./lib/api/foodpanda/pushlistdata.ajax.php',
								method:'post',
								async:false,
								cache:false,
								data:{'data':d,'machinetype':'rightnow'},
								dataType:'json',
								success:function(d){
									if(JSON.stringify(d).match('Notice')){
										//console.log('match');
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'autodownload.php pushlistdata.ajax.php '+d}
										});
									}
									else{
										//console.log('error');
									}
									//console.log(d);
									$.ajax({
										url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/foodpanda/changeweb.ajax.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>",'data':d},
										dataType:'html',
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php changeweb.ajax.php '+d}
												});
											}
											else{
											}
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
									$.ajax({
										url:'./lib/js/weborder.content.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'autosale':<?php if(isset($initsetting['init']['webautosale']))echo $initsetting['init']['webautosale'];else echo '0'; ?>,'data':d},
										dataType:'json',
										timeout:5000,
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php weborder.content.php '+d}
												});
											}
											else{
											}
											//console.log(d);
											$.each(d,function(consecnumber,data){
												//console.log(consecnumber);
												//console.log(data);
												var total=0;
												var subtotal=0;
												var qty=0;
												var temptotal=0;
												var tempdis=0;
												for(var item=0;item<data['linenumber[]'].length;item++){
													total=Number(total)+(Number(data['money[]'][item])*Number(data['number[]'][item]));
													subtotal=Number(subtotal)+(Number(data['subtotal[]'][item]));
													qty=Number(qty)+(Number(data['number[]'][item]));
													if(data['needcharge[]'][item]=='1'){
														temptotal=Number(temptotal)+(Number(data['money[]'][item])*Number(data['number[]'][item]));
														tempdis=Number(tempdis)+Number(data['discount[]'][item]);
													}
													else{
													}
												}
												/**$.each($('.order#order #tabs4 form[data-id="listform"] .label'),function(index,value){
													total=Number(total)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
													subtotal=Number(subtotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="subtotal[]"]').val()));
													qty=Number(qty)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="qty[]"]').val()));
													if($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="needcharge[]"]').val()=='1'){
														temptotal=Number(temptotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
														tempdis=Number(tempdis)+Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="discount[]"]').val());
													}
													else{
													}
												});*/
												if(<?php if(isset($initsetting['init']['charge'])&&$initsetting['init']['charge']=='1')echo '1';else echo '0'; ?>){
													if(<?php if(isset($initsetting['init']['openchar'])&&$initsetting['init']['openchar']=='1'&&isset($initsetting['init']['charge'])&&$initsetting['init']['charge']=='1')echo '1';else echo '0'; ?>){
														if(<?php if(isset($initsetting['init']['chargeeq'])&&$initsetting['init']['chargeeq']=='2')echo '1';else echo '0'; ?>){//服務費以折扣後之價格計算
															data['charge']=(Number(temptotal)-Number(tempdis))*Number(<?php echo $initsetting['init']['chargenumber']; ?>)/100;
														}
														else{//服務費以原價格計算
															data['charge']=Number(temptotal)*Number(<?php echo $initsetting['init']['chargenumber']; ?>)/100;
														}
														var precision=parseInt(<?php echo $initsetting['init']['accuracy']; ?>);//可由設定檔設定精準度(e.g.精準度小數點第二位 填2)
														//設定檔內可設定使用何種進位
														if(<?php if($initsetting['init']['accuracytype']=='1')echo '1';else echo '0'; ?>){//四捨五入
															data['charge']=roundfun(data['charge'],precision);
														}
														else if(<?php if($initsetting['init']['accuracytype']=='2')echo '1';else echo '0'; ?>){//無條件進位
															data['charge']=ceilfun(data['charge'],precision);
														}
														else{//無條件捨去
															data['charge']=floorfun(data['charge'],precision);
														}
													}
													else{
													}
												}
												else{
												}
												//console.log(data);
												//2021/10/22 foodpanda帳單不判斷POS系統優惠
												/*if(<?php if($initsetting['init']['autodis']=='1')echo '1';else echo '0'; ?>){//開啟自動優惠
													$.ajax({
														url:'./lib/js/compdis.ajax.php',
														method:'post',
														async: false,
														data:data,
														dataType:'html',
														success:function(d){
															//console.log(d);
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'temp compdis.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(data);
															var temp=d.split(';');
															if($.isNumeric(temp[0])){
																$.ajax({
																	url:'./lib/js/wttempdis.ajax.php',
																	method:'post',
																	async: false,
																	data:{'bizdate':data['bizdate'],'consecnumber':consecnumber,'autodis':temp[0],'autodiscontent':temp[1],'autodispermoney':temp[2]},
																	dataType:'html',
																	success:function(d){
																		if(d.length>20){
																			$.ajax({
																				url:'./lib/js/print.php',
																				method:'post',
																				data:{'html':'temp wttempdis.ajax.php '+d},
																				dataType:'html',
																				success:function(d){
																					//console.log(d);
																				},
																				error:function(e){
																					//console.log(e);
																				}
																			});
																		}
																		else{
																		}
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
																//$('.result #viewwindow #autodis').html(temp[0]);
																//$('.result #viewwindow input[name="autodiscontent"]').val(temp[1]);
																//$('.result #viewwindow input[name="autodispremoney"]').val(temp[2]);
															}
															else{
																//console.log('fail');
															}
															//console.log(d);
														},
														error:function(e){
															//console.log(e)
														}
													});
												}
												else{
												}*/
												data['machinetype']='m1';
												//console.log(data);
												//console.log(data['consecnumber']);
												$.ajax({
													url:'./lib/js/create.webkds.php',
													method:'post',
													async:false,
													data:data,
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												
												//2022/5/16 結帳流程應該要在最後面//2022/2/25 自動結帳(問題點：開立發票的參數－統編與載具記錄來源)
												/*if(<?php if(isset($initsetting['init']['foodpandaautosale'])&&$initsetting['init']['foodpandaautosale']=='1')echo '1';else echo '0'; ?>){//下載後直接結帳
													if(<?php if(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1')echo '1';else echo '0'; ?>){
														$.ajax({
															url:'./lib/js/open.inv.php',
															method:'post',
															async:false,
															data:{'machinename':data['machinetype'],'bizdate':data['bizdate'],'consecnumber':data['consecnumber'],'tempban':$('.result #viewwindow input[name="tempban"]').val(),'tempcontainer':$('.result #viewwindow input[name="tempcontainer"]').val(),'invlist':<?php echo $initsetting['init']['invlist']; ?>},
															dataType:'html',
															success:function(d){
																console.log(d);
															},
															error:function(e){
																console.log(e);
															}
														});
													}
													else{
													}
													$.ajax({
														url:'./lib/js/temptodb.ajax.php',
														method:'post',
														async:false,
														data:{'bizdate':data['bizdate'],'terminalnumber':'rightnow','numbertag':consecnumber},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'sale temptodb.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(d);
															//console.log('轉移正式'+consecnumber);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
												else{
													//console.log('error');
												}*/

												$.ajax({
													url:'./lib/js/create.list.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.list.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.kitchen.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														//console.log(d);
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.kitchen.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.tag.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.tag.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												//console.log('consecnumber:'+consecnumber);
												//2022/5/16 結帳流程判斷參數拆分開來
												/*if(<?php if(isset($initsetting['init']['webautosale'])&&$initsetting['init']['webautosale']=='1')echo '1';else echo '0'; ?>){//下載後直接結帳
													$.ajax({
														url:'./lib/js/temptodb.ajax.php',
														method:'post',
														async:false,
														data:{'bizdate':data['bizdate'],'terminalnumber':'rightnow','numbertag':consecnumber},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'sale temptodb.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(d);
															//console.log('轉移正式'+consecnumber);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
												else{
													//console.log('error');
												}*/
												//2022/5/16 預計使用以下流程直接結帳
												if(<?php if(isset($initsetting['init']['foodpandaautosale'])&&$initsetting['init']['foodpandaautosale']=='1')echo '1';else echo '0'; ?>){//下載後直接結帳
													if(<?php if(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1')echo '1';else echo '0'; ?>){
														$.ajax({
															url:'./lib/js/open.inv.php',
															method:'post',
															async:false,
															data:{'machinename':data['machinetype'],'bizdate':data['bizdate'],'consecnumber':data['consecnumber'],'tempban':'','tempcontainer':'','invlist':<?php echo $initsetting['init']['invlist']; ?>},
															dataType:'html',
															success:function(d){
																console.log(d);
															},
															error:function(e){
																console.log(e);
															}
														});
													}
													else{
													}
													$.ajax({
														url:'./lib/js/temptodb.ajax.php',
														method:'post',
														async:false,
														data:{'bizdate':data['bizdate'],'terminalnumber':'rightnow','numbertag':data['consecnumber']},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'sale temptodb.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(d);
															//console.log('轉移正式'+consecnumber);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
												else{
													//console.log('error');
												}
												
											});
										},
										error:function(e){
											console.log(e);
										}
									});
								},
								error:function(e){
									//console.log(e.responseText);
									if(e.responseText.match('Notice')){
										//console.log('match');
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'autodownload.php pushlistdata.ajax.php error '+e.responseText}
										});
									}
									else{
										//console.log('error');
									}
								}
							});
						}
					},
					error:function(e,status){
						if(e.responseText.match('Notice')){
							//console.log('match');
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php error '+e.responseText}
							});
						}
						else{
							//console.log('error');
						}
						if(status==='timeout'){
							//console.log('timeout');
						}
						else{
							//console.log(e);
						}
					}
				});
			};
			function deletefoodpanda(){
				$.ajax({
					url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/foodpanda/getlist.ajax.php',
					method:'post',
					async:false,
					cache:false,
					data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>",'type':'delete'},
					dataType:'json',
					timeout:5000,
					success:function(d){
						if(JSON.stringify(d).match('Notice')){
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php '+d}
							});
						}
						else{
						}
						//console.log(d);
						if(d=='empty'){<?php /*無作廢單*/ ?>
						}
						else{
							$.ajax({
								url:'./lib/api/foodpanda/deletelist.ajax.php',
								method:'post',
								async:false,
								cache:false,
								data:{'data':d},
								dataType:'json',
								success:function(d){
									console.log(d);
									console.log('列印作廢單');
									$.ajax({
										url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/foodpanda/changeweb.ajax.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>",'data':d},
										dataType:'html',
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php changeweb.ajax.php '+d}
												});
											}
											else{
											}
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
								},
								error:function(e){
									console.log(e);
								}
							});
						}
					},
					error:function(e){
						console.log(e);
					}
				});
			}
			function checkubereats(){
				$.ajax({
					url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/ubereats/getlist.ajax.php',
					method:'post',
					async:false,
					cache:false,
					data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>"},
					dataType:'json',
					timeout:5000,
					success:function(d){
						if(JSON.stringify(d).match('Notice')){
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php '+d}
							});
						}
						else{
						}
						//console.log(d);
						if(d=='empty'){<?php /*無網路訂單*/ ?>
						}
						else{<?php /*有網路訂單*/ ?>
							/*$.ajax({//若在此更新訂單狀態，"疑似"會產生一張單下載兩次的情況，因此將更新訂單狀態提前至downweb.ajax.php中
								url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/js/receveweb.ajax.php',
								method:'post',
								async:false,
								data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>"},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});*/
							d[-1]="<?php echo $setup['basic']['company']; ?>";
							d[-2]="<?php echo $setup['basic']['story']; ?>";
							//console.log(d);
							$.ajax({
								url:'./lib/api/ubereats/pushlistdata.ajax.php',
								method:'post',
								async:false,
								cache:false,
								data:{'data':d,'machinetype':'rightnow'},
								dataType:'json',
								success:function(d){
									if(JSON.stringify(d).match('Notice')){
										//console.log('match');
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'autodownload.php pushlistdata.ajax.php '+d}
										});
									}
									else{
										//console.log('error');
									}
									//console.log(d);
									$.ajax({
										url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/ubereats/changeweb.ajax.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>",'data':d},
										dataType:'html',
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php changeweb.ajax.php '+d}
												});
											}
											else{
											}
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
									$.ajax({
										url:'./lib/js/weborder.content.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'autosale':<?php if(isset($initsetting['init']['webautosale']))echo $initsetting['init']['webautosale'];else echo '0'; ?>,'data':d},
										dataType:'json',
										timeout:5000,
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php weborder.content.php '+d}
												});
											}
											else{
											}
											//console.log(d);
											$.each(d,function(consecnumber,data){
												//console.log(consecnumber);
												//console.log(data);
												var total=0;
												var subtotal=0;
												var qty=0;
												var temptotal=0;
												var tempdis=0;
												for(var item=0;item<data['linenumber[]'].length;item++){
													total=Number(total)+(Number(data['money[]'][item])*Number(data['number[]'][item]));
													subtotal=Number(subtotal)+(Number(data['subtotal[]'][item]));
													qty=Number(qty)+(Number(data['number[]'][item]));
													if(data['needcharge[]'][item]=='1'){
														temptotal=Number(temptotal)+(Number(data['money[]'][item])*Number(data['number[]'][item]));
														tempdis=Number(tempdis)+Number(data['discount[]'][item]);
													}
													else{
													}
												}
												/**$.each($('.order#order #tabs4 form[data-id="listform"] .label'),function(index,value){
													total=Number(total)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
													subtotal=Number(subtotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="subtotal[]"]').val()));
													qty=Number(qty)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="qty[]"]').val()));
													if($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="needcharge[]"]').val()=='1'){
														temptotal=Number(temptotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
														tempdis=Number(tempdis)+Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="discount[]"]').val());
													}
													else{
													}
												});*/
												if(<?php if(isset($initsetting['init']['charge'])&&$initsetting['init']['charge']=='1')echo '1';else echo '0'; ?>){
													if(<?php if(isset($initsetting['init']['openchar'])&&$initsetting['init']['openchar']=='1'&&isset($initsetting['init']['charge'])&&$initsetting['init']['charge']=='1')echo '1';else echo '0'; ?>){
														if(<?php if(isset($initsetting['init']['chargeeq'])&&$initsetting['init']['chargeeq']=='2')echo '1';else echo '0'; ?>){//服務費以折扣後之價格計算
															data['charge']=(Number(temptotal)-Number(tempdis))*Number(<?php echo $initsetting['init']['chargenumber']; ?>)/100;
														}
														else{//服務費以原價格計算
															data['charge']=Number(temptotal)*Number(<?php echo $initsetting['init']['chargenumber']; ?>)/100;
														}
														var precision=parseInt(<?php echo $initsetting['init']['accuracy']; ?>);//可由設定檔設定精準度(e.g.精準度小數點第二位 填2)
														//設定檔內可設定使用何種進位
														if(<?php if($initsetting['init']['accuracytype']=='1')echo '1';else echo '0'; ?>){//四捨五入
															data['charge']=roundfun(data['charge'],precision);
														}
														else if(<?php if($initsetting['init']['accuracytype']=='2')echo '1';else echo '0'; ?>){//無條件進位
															data['charge']=ceilfun(data['charge'],precision);
														}
														else{//無條件捨去
															data['charge']=floorfun(data['charge'],precision);
														}
													}
													else{
													}
												}
												else{
												}
												//console.log(data);
												//2021/10/22 foodpanda帳單不判斷POS系統優惠
												/*if(<?php if($initsetting['init']['autodis']=='1')echo '1';else echo '0'; ?>){//開啟自動優惠
													$.ajax({
														url:'./lib/js/compdis.ajax.php',
														method:'post',
														async: false,
														data:data,
														dataType:'html',
														success:function(d){
															//console.log(d);
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'temp compdis.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(data);
															var temp=d.split(';');
															if($.isNumeric(temp[0])){
																$.ajax({
																	url:'./lib/js/wttempdis.ajax.php',
																	method:'post',
																	async: false,
																	data:{'bizdate':data['bizdate'],'consecnumber':consecnumber,'autodis':temp[0],'autodiscontent':temp[1],'autodispermoney':temp[2]},
																	dataType:'html',
																	success:function(d){
																		if(d.length>20){
																			$.ajax({
																				url:'./lib/js/print.php',
																				method:'post',
																				data:{'html':'temp wttempdis.ajax.php '+d},
																				dataType:'html',
																				success:function(d){
																					//console.log(d);
																				},
																				error:function(e){
																					//console.log(e);
																				}
																			});
																		}
																		else{
																		}
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
																//$('.result #viewwindow #autodis').html(temp[0]);
																//$('.result #viewwindow input[name="autodiscontent"]').val(temp[1]);
																//$('.result #viewwindow input[name="autodispremoney"]').val(temp[2]);
															}
															else{
																//console.log('fail');
															}
															//console.log(d);
														},
														error:function(e){
															//console.log(e)
														}
													});
												}
												else{
												}*/
												data['machinetype']='m1';
												//console.log(data);
												//console.log(data['consecnumber']);
												$.ajax({
													url:'./lib/js/create.webkds.php',
													method:'post',
													async:false,
													data:data,
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												
												//2022/5/16 結帳流程應該要在最後面//2022/2/25 自動結帳(問題點：開立發票的參數－統編與載具記錄來源)
												/*if(<?php if(isset($initsetting['init']['foodpandaautosale'])&&$initsetting['init']['foodpandaautosale']=='1')echo '1';else echo '0'; ?>){//下載後直接結帳
													if(<?php if(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1')echo '1';else echo '0'; ?>){
														$.ajax({
															url:'./lib/js/open.inv.php',
															method:'post',
															async:false,
															data:{'machinename':data['machinetype'],'bizdate':data['bizdate'],'consecnumber':data['consecnumber'],'tempban':$('.result #viewwindow input[name="tempban"]').val(),'tempcontainer':$('.result #viewwindow input[name="tempcontainer"]').val(),'invlist':<?php echo $initsetting['init']['invlist']; ?>},
															dataType:'html',
															success:function(d){
																console.log(d);
															},
															error:function(e){
																console.log(e);
															}
														});
													}
													else{
													}
													$.ajax({
														url:'./lib/js/temptodb.ajax.php',
														method:'post',
														async:false,
														data:{'bizdate':data['bizdate'],'terminalnumber':'rightnow','numbertag':consecnumber},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'sale temptodb.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(d);
															//console.log('轉移正式'+consecnumber);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
												else{
													//console.log('error');
												}*/

												$.ajax({
													url:'./lib/js/create.list.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.list.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.kitchen.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														//console.log(d);
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.kitchen.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.tag.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.tag.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												//console.log('consecnumber:'+consecnumber);
												//2022/5/16 結帳流程判斷參數拆分開來
												/*if(<?php if(isset($initsetting['init']['webautosale'])&&$initsetting['init']['webautosale']=='1')echo '1';else echo '0'; ?>){//下載後直接結帳
													$.ajax({
														url:'./lib/js/temptodb.ajax.php',
														method:'post',
														async:false,
														data:{'bizdate':data['bizdate'],'terminalnumber':'rightnow','numbertag':consecnumber},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'sale temptodb.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(d);
															//console.log('轉移正式'+consecnumber);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
												else{
													//console.log('error');
												}*/
												//2022/5/16 預計使用以下流程直接結帳
												if(<?php if(isset($initsetting['init']['foodpandaautosale'])&&$initsetting['init']['foodpandaautosale']=='1')echo '1';else echo '0'; ?>){//下載後直接結帳
													if(<?php if(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1')echo '1';else echo '0'; ?>){
														$.ajax({
															url:'./lib/js/open.inv.php',
															method:'post',
															async:false,
															data:{'machinename':data['machinetype'],'bizdate':data['bizdate'],'consecnumber':data['consecnumber'],'tempban':'','tempcontainer':'','invlist':<?php echo $initsetting['init']['invlist']; ?>},
															dataType:'html',
															success:function(d){
																console.log(d);
															},
															error:function(e){
																console.log(e);
															}
														});
													}
													else{
													}
													$.ajax({
														url:'./lib/js/temptodb.ajax.php',
														method:'post',
														async:false,
														data:{'bizdate':data['bizdate'],'terminalnumber':'rightnow','numbertag':data['consecnumber']},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'sale temptodb.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(d);
															//console.log('轉移正式'+consecnumber);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
												else{
													//console.log('error');
												}
												
											});
										},
										error:function(e){
											console.log(e);
										}
									});
								},
								error:function(e){
									//console.log(e.responseText);
									if(e.responseText.match('Notice')){
										//console.log('match');
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'autodownload.php pushlistdata.ajax.php error '+e.responseText}
										});
									}
									else{
										//console.log('error');
									}
								}
							});
						}
					},
					error:function(e,status){
						if(e.responseText.match('Notice')){
							//console.log('match');
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php error '+e.responseText}
							});
						}
						else{
							//console.log('error');
						}
						if(status==='timeout'){
							//console.log('timeout');
						}
						else{
							//console.log(e);
						}
					}
				});
			};
			function checkquickclick(){
				$.ajax({
					url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/quickclick/getlist.ajax.php',
					method:'post',
					async:false,
					cache:false,
					data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>","getlisttype":"<?php if(isset($initsetting['init']['quickclicklisttype']))echo $initsetting['init']['quickclicklisttype'];else echo '0'; ?>"},
					dataType:'json',
					timeout:5000,
					success:function(d){
						if(JSON.stringify(d).match('Notice')){
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php '+d}
							});
						}
						else{
						}
						//console.log(d);
						if(d=='empty'){<?php /*無網路訂單*/ ?>
						}
						else{<?php /*有網路訂單*/ ?>
							/*$.ajax({//若在此更新訂單狀態，"疑似"會產生一張單下載兩次的情況，因此將更新訂單狀態提前至downweb.ajax.php中
								url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/js/receveweb.ajax.php',
								method:'post',
								async:false,
								data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>"},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});*/
							d[-1]="<?php echo $setup['basic']['company']; ?>";
							d[-2]="<?php echo $setup['basic']['story']; ?>";
							//console.log(d);
							$.ajax({
								url:'./lib/api/quickclick/pushlistdata.ajax.php',
								method:'post',
								async:false,
								cache:false,
								data:{'data':d,'machinetype':'rightnow'},
								dataType:'json',
								success:function(d){
									if(JSON.stringify(d).match('Notice')){
										//console.log('match');
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'autodownload.php pushlistdata.ajax.php '+d}
										});
									}
									else{
										//console.log('error');
									}
									//console.log(d);
									$.ajax({
										url:'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/quickclick/changeweb.ajax.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'dep':"<?php echo $setup['basic']['story']; ?>",'data':d},
										dataType:'html',
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php changeweb.ajax.php '+d}
												});
											}
											else{
											}
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
									$.ajax({
										url:'./lib/js/weborder.content.php',
										method:'post',
										async:false,
										data:{'company':"<?php echo $setup['basic']['company']; ?>",'autosale':<?php if(isset($initsetting['init']['webautosale']))echo $initsetting['init']['webautosale'];else echo '0'; ?>,'data':d},
										dataType:'json',
										timeout:5000,
										success:function(d){
											if(JSON.stringify(d).match('Notice')){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'autodownload.php weborder.content.php '+d}
												});
											}
											else{
											}
											//console.log(d);
											$.each(d,function(consecnumber,data){
												//console.log(consecnumber);
												//console.log(data);
												var total=0;
												var subtotal=0;
												var qty=0;
												var temptotal=0;
												var tempdis=0;
												for(var item=0;item<data['linenumber[]'].length;item++){
													total=Number(total)+(Number(data['money[]'][item])*Number(data['number[]'][item]));
													subtotal=Number(subtotal)+(Number(data['subtotal[]'][item]));
													qty=Number(qty)+(Number(data['number[]'][item]));
													if(data['needcharge[]'][item]=='1'){
														temptotal=Number(temptotal)+(Number(data['money[]'][item])*Number(data['number[]'][item]));
														tempdis=Number(tempdis)+Number(data['discount[]'][item]);
													}
													else{
													}
												}
												/**$.each($('.order#order #tabs4 form[data-id="listform"] .label'),function(index,value){
													total=Number(total)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
													subtotal=Number(subtotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="subtotal[]"]').val()));
													qty=Number(qty)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="qty[]"]').val()));
													if($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="needcharge[]"]').val()=='1'){
														temptotal=Number(temptotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
														tempdis=Number(tempdis)+Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="discount[]"]').val());
													}
													else{
													}
												});*/
												if(<?php if(isset($initsetting['init']['charge'])&&$initsetting['init']['charge']=='1')echo '1';else echo '0'; ?>){
													if(<?php if(isset($initsetting['init']['openchar'])&&$initsetting['init']['openchar']=='1'&&isset($initsetting['init']['charge'])&&$initsetting['init']['charge']=='1')echo '1';else echo '0'; ?>){
														if(<?php if(isset($initsetting['init']['chargeeq'])&&$initsetting['init']['chargeeq']=='2')echo '1';else echo '0'; ?>){//服務費以折扣後之價格計算
															data['charge']=(Number(temptotal)-Number(tempdis))*Number(<?php echo $initsetting['init']['chargenumber']; ?>)/100;
														}
														else{//服務費以原價格計算
															data['charge']=Number(temptotal)*Number(<?php echo $initsetting['init']['chargenumber']; ?>)/100;
														}
														var precision=parseInt(<?php echo $initsetting['init']['accuracy']; ?>);//可由設定檔設定精準度(e.g.精準度小數點第二位 填2)
														//設定檔內可設定使用何種進位
														if(<?php if($initsetting['init']['accuracytype']=='1')echo '1';else echo '0'; ?>){//四捨五入
															data['charge']=roundfun(data['charge'],precision);
														}
														else if(<?php if($initsetting['init']['accuracytype']=='2')echo '1';else echo '0'; ?>){//無條件進位
															data['charge']=ceilfun(data['charge'],precision);
														}
														else{//無條件捨去
															data['charge']=floorfun(data['charge'],precision);
														}
													}
													else{
													}
												}
												else{
												}
												//console.log(data);
												//2021/10/22 foodpanda帳單不判斷POS系統優惠
												/*if(<?php if($initsetting['init']['autodis']=='1')echo '1';else echo '0'; ?>){//開啟自動優惠
													$.ajax({
														url:'./lib/js/compdis.ajax.php',
														method:'post',
														async: false,
														data:data,
														dataType:'html',
														success:function(d){
															//console.log(d);
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'temp compdis.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(data);
															var temp=d.split(';');
															if($.isNumeric(temp[0])){
																$.ajax({
																	url:'./lib/js/wttempdis.ajax.php',
																	method:'post',
																	async: false,
																	data:{'bizdate':data['bizdate'],'consecnumber':consecnumber,'autodis':temp[0],'autodiscontent':temp[1],'autodispermoney':temp[2]},
																	dataType:'html',
																	success:function(d){
																		if(d.length>20){
																			$.ajax({
																				url:'./lib/js/print.php',
																				method:'post',
																				data:{'html':'temp wttempdis.ajax.php '+d},
																				dataType:'html',
																				success:function(d){
																					//console.log(d);
																				},
																				error:function(e){
																					//console.log(e);
																				}
																			});
																		}
																		else{
																		}
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
																//$('.result #viewwindow #autodis').html(temp[0]);
																//$('.result #viewwindow input[name="autodiscontent"]').val(temp[1]);
																//$('.result #viewwindow input[name="autodispremoney"]').val(temp[2]);
															}
															else{
																//console.log('fail');
															}
															//console.log(d);
														},
														error:function(e){
															//console.log(e)
														}
													});
												}
												else{
												}*/
												data['machinetype']='m1';
												//console.log(data);
												//console.log(data['consecnumber']);
												$.ajax({
													url:'./lib/js/create.webkds.php',
													method:'post',
													async:false,
													data:data,
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.list.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.list.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.kitchen.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														//console.log(d);
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.kitchen.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
													},
													error:function(e){
														//console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/create.tag.php',
													method:'post',
													async: false,
													data:data,
													dataType:'html',
													success:function(d){
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																data:{'html':'temp create.tag.php '+d},
																dataType:'html',
																success:function(d){
																	//console.log(d);
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
														}
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												//console.log('consecnumber:'+consecnumber);
												if(<?php if(isset($initsetting['init']['webautosale'])&&$initsetting['init']['webautosale']=='1')echo '1';else echo '0'; ?>){//下載後直接結帳
													$.ajax({
														url:'./lib/js/temptodb.ajax.php',
														method:'post',
														async:false,
														data:{'bizdate':data['bizdate'],'terminalnumber':'rightnow','numbertag':consecnumber},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'./lib/js/print.php',
																	method:'post',
																	data:{'html':'sale temptodb.ajax.php '+d},
																	dataType:'html',
																	success:function(d){
																		//console.log(d);
																	},
																	error:function(e){
																		//console.log(e);
																	}
																});
															}
															else{
															}
															//console.log(d);
															//console.log('轉移正式'+consecnumber);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
												else{
													//console.log('error');
												}
												
											});
										},
										error:function(e){
											console.log(e);
										}
									});
								},
								error:function(e){
									//console.log(e.responseText);
									if(e.responseText.match('Notice')){
										//console.log('match');
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'autodownload.php pushlistdata.ajax.php error '+e.responseText}
										});
									}
									else{
										//console.log('error');
									}
								}
							});
						}
					},
					error:function(e,status){
						if(e.responseText.match('Notice')){
							//console.log('match');
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'autodownload.php downweb.ajax.php error '+e.responseText}
							});
						}
						else{
							//console.log('error');
						}
						if(status==='timeout'){
							//console.log('timeout');
						}
						else{
							//console.log(e);
						}
					}
				});
			};
			if(<?php if(isset($initsetting['init']['webordersec'])&&$initsetting['init']['webordersec']!='')echo '1';else echo '0'; ?>){
				var checksec="<?php if(isset($initsetting['init']['webordersec'])&&$initsetting['init']['webordersec']!='')echo $initsetting['init']['webordersec']; ?>";
			}
			else{
				var checksec='0.5';
			}
			var temp=setInterval(check,parseInt(checksec)*1000);
			setInterval(function(){
				if(lock2==1){
				}
				else{
					lock2=1;
					$.ajax({
						url:'./isstop.php',
						async:false,
						dataType:'html',
						success:function(d){
							//console.log(d);
							if(d=='stop'){
								clearInterval(temp);
								start=0;
							}
							else{
								if(start==0){
									start=1;
									temp=setInterval(check,parseInt(checksec)*1000);
								}
								else{
								}
							}
						},
						error:function(e){
							if(start==0){
								start=1;
								temp=setInterval(check,parseInt(checksec)*1000);
							}
							else{
							}
						}
					});
					lock2=0;
				}
			},1000);
			/*setInterval(function(){
				if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
					api_sendmessage_posdvr('');
					$.ajax({
						url:'./lib/js/print.php',
						method:'post',
						data:{'html':'success search file','file':'posdvr'},
						dataType:'html',
						success:function(d){
							//console.log(d);
						},
						error:function(e){
							//console.log(e);
						}
					});
				}
				else{
					$.ajax({
						url:'./lib/js/print.php',
						method:'post',
						async:false,
						data:{'html':'error sendmessage is not function','file':'posdvr'},
						dataType:'html',
						success:function(d){
							//console.log(d);
						},
						error:function(e){
							//console.log(e);
						}
					});
				}
			},1000);*/
			if(<?php if((isset($initsetting['init']['foodpanda'])&&$initsetting['init']['foodpanda']=='1')||(isset($initsetting['ubereats']['openubereats'])&&$initsetting['ubereats']['openubereats']=='1')||(isset($initsetting['init']['quickclick'])&&$initsetting['init']['quickclick']=='1'))echo 1;else echo 0; ?>){
				var download;
				download = io.connect('http://api.tableplus.com.tw:3700');
				if(<?php if(isset($initsetting['init']['foodpanda'])&&$initsetting['init']['foodpanda']=='1')echo '1';else echo '0'; ?>){
					download.emit('foodpandajoin','<?php echo $setup["basic"]["story"]; ?>dow');
				}
				else{
				}
				if(<?php if(isset($initsetting['ubereats']['openubereats'])&&$initsetting['ubereats']['openubereats']=='1')echo '1';else echo '0'; ?>){
					download.emit('ubereatsjoin','<?php echo $setup["basic"]["story"]; ?>dow');
				}
				else{
				}
				if(<?php if(isset($initsetting['init']['quickclick'])&&$initsetting['init']['quickclick']=='1')echo '1';else echo '0'; ?>){
					download.emit('quickclickjoin','<?php echo $setup["basic"]["story"]; ?>dow');
				}
				else{
				}
				//download.emit('foodpandajoin','<?php echo $setup["basic"]["story"]; ?>dow');

				//download.emit('foodpandajoin','3goodnidindow');
				download.on('foodpanda',function(msg){
					//console.log(msg);
					checkfoodpanda();
				});
				download.on('deletefoodpanda',function(msg){
					//console.log(msg);
					deletefoodpanda();
				});
				download.on('ubereats',function(msg){
					//console.log(msg);
					checkubereats();
				});
				download.on('sping',function(msg){
					download.emit('rping','<?php echo $setup["basic"]["story"]; ?>dow');
					//download.emit('rping','3goodnidindow');
					//console.log(msg);
				});
				download.on('quickclick',function(msg){
					//console.log(msg);
					checkquickclick();
				});
				download.on('disconnect',function(){//2020/11/11 server離線
					console.log('server disconnect');
					if(<?php if(isset($initsetting['init']['foodpanda'])&&$initsetting['init']['foodpanda']=='1')echo '1';else echo '0'; ?>){
						download.emit('foodpandajoin','<?php echo $setup["basic"]["story"]; ?>dow');//重新登入
					}
					else{
					}
					if(<?php if(isset($initsetting['ubereats']['openubereats'])&&$initsetting['ubereats']['openubereats']=='1')echo '1';else echo '0'; ?>){
						download.emit('ubereatsjoin','<?php echo $setup["basic"]["story"]; ?>dow');//重新登入
					}
					else{
					}
					if(<?php if(isset($initsetting['init']['quickclick'])&&$initsetting['init']['quickclick']=='1')echo '1';else echo '0'; ?>){
						download.emit('quickclickjoin','<?php echo $setup["basic"]["story"]; ?>dow');//重新登入
					}
					else{
					}
					//download.emit('foodpandajoin','<?php echo $setup["basic"]["story"]; ?>dow');//重新登入
					//download.emit('foodpandajoin','3goodnidindow');//重新登入
				});
			}
			else{
			}
		});
	  </script>
</head>
<body style='width:calc(100vw - 20px);height:calc(100vh - 20px);padding:10px;margin:0;overflow:hidden;'>
	<img src="../database/img/tablepluslogo.png" style='width:100%;height:100%;object-fit:contain;'>
</body>
</html>
