<!doctype html>
<html lang="en">
<?php
$initsetting=parse_ini_file('../database/initsetting.ini',true);

$map=parse_ini_file('../database/mapping.ini',true);
if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'&&isset($_POST['machinetype'])&&isset($map['map'][$_POST['machinetype']])&&file_exists('../database/time'.$map['map'][$_POST['machinetype']].'.ini')){
	$time=parse_ini_file('../database/time'.$map['map'][$_POST['machinetype']].'.ini',true);
}
else{
	$time=parse_ini_file('../database/timem1.ini',true);
}
?>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">
	<script src="../tool/jquery-1.12.4.js"></script>
	<script src="../tool/ui/1.12.1/jquery-ui.js"></script>
	<link rel="stylesheet" href="../tool/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="../tool/fastclick/lib/fastclick.js"></script>
	<title>點餐畫面</title>
	<script>
		$(function() {
			FastClick.attach(document.body);
		});
		$(document).ready(function(){
			var responce=true;
			setInterval(function(){
				if(responce==true){
					responce=false;
					$.ajax({
						url:'../demopos/lib/js/gettime.ajax.php',
						dataType:'json',
						async:false,
						success:function(d){
							if($('#content #setup input[name="machine"]').val()!=''){
								if(d[1]%30==0){
									$.ajax({
										url:'../demopos/lib/js/create.cmdtxt.php',
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
								}
								else{
								}
							}
							else{
							}
							$('.order#order #banner div[data-id="time"]').html(d[0]);
						},
						error:function(e){
							//console.log(e);
						}
					});
					responce=true;
				}
				else{
				}
				$.ajax({
					url:'../demopos/lib/js/checkopen.ajax.php',
					method:'post',
					async:false,
					data:{'machinetype':$('#setup input[name="machinetype"]').val()},
					dataType:'html',
					success:function(d){
						if(d=='success'){
							$.ajax({
								url:'../demopos/lib/js/change.class.php',
								method:'post',
								async: false,
								data:{'type':'isclose','machinetype':$('#setup input[name="machinetype"]').val(),'view':''},
								dataType:'html',
								success:function(d){
									var tempdata=d.split('-');
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos change.class.php '+d},
											dataType:'html',
											success:function(d){/*console.log(d);*/},
											error:function(e){/*console.log(e);*/}
										});
									}
									else{
									}
									//console.log(d);
									
									$('#content caption').html(tempdata[0]);
									$('#content #open').css({'display':'none'});
									$('#content #close').css({'display':'block'});
									$('#content #tabnum').prop('disabled',false);
									$('#content .listtype').prop('disabled',false);
									$('#content #entertable').prop('disabled',false);
									$('#content #listtype').css({'display':'table-row'});
									if($('#content .listtype option:selected').val()=='1'){
										$('#content #tablab').css({'display':'table-row'});
									}
									else{
										//$('#content #tablab').css({'display':'table-row'});
									}
									$('#content #orderbut').css({'display':'table-row'});
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
						else{
							$.ajax({
								url:'../demopos/lib/js/change.class.php',
								method:'post',
								async: false,
								data:{'type':'isopen','machinetype':$('#setup input[name="machinetype"]').val(),'view':''},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos change.class.php '+d},
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
									
									$('#content #open').css({'display':'block'});
									$('#content #close').css({'display':'none'});
									$('#content #tabnum').prop('disabled',true);
									$('#content .listtype').prop('disabled',true);
									$('#content #entertable').prop('disabled',true);
									$('#content #listtype').css({'display':'none'});
									$('#content #tablab').css({'display':'none'});
									$('#content #orderbut').css({'display':'none'});
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
			},1000);
			$('#content #setbutton').click(function(){
				//setwin.dialog('open');
				$('.setwin').css({'display':'block'});
				$('.modal').css({'display':'block'});
			});
			$('.modal').click(function(){
				$('.modal').css({'display':'none'});
				if($('.setwin').css('display')=='block'){
					$('.setwin').css({'display':'none'});
				}
				else{
				}
				if($('.sysmeg').css('display')=='block'){
					$('.sysmeg').css({'display':'none'});
				}
				else{
				}
				if($('.setchange').css('display')=='block'){
					$('.setchange').css({'display':'none'});
				}
				else{
				}
			});
			$('.setwin #vieworder').click(function(){
				$.ajax({
					url:'../demopos/lib/js/getsalelist.ajax.php',
					method:'post',
					data:{'sale':'sale','machinetype':$('#setup input[name="machinetype"]').val()},
					dataType:'html',
					success:function(d){
						$('.viewlist #salecontent').html(d);
						$('.viewlist #listcontent').html('');
						$('.viewlist .ttmoney').html($('.viewlist #salecontent input[name="ttmoney"]').val());
						//$('.viewlist #rightnow #ttmoney').html($('.viewlist #salelist input[name="ttmoney"]').val());
						//$('.viewlist #rightnow #ttcount').html($('.viewlist #salelist input[name="ttcount"]').val());
					},
					error:function(e){
						//console.log(e);
					}
				});
				$('.setwin').css({'display':'none'});
				$('.modal').css({'display':'none'});
				$('.viewlist').css({'display':'block'});
			});
			$('.viewlist .ttmoney').click(function(){
				$.ajax({
					url:'./lib/js/getallpaylist.php',
					method:'post',
					async:false,
					data:{'machine':$('#setup input[name="machinetype"]').val()},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('.paylist #paycontent').html(d);
						$('.paylist').css({'display':'block'});
					},
					error:function(e){
						//console.log(e);
					}
				});
			});
			$('.paylist .exit').click(function(){
				$('.paylist').css({'display':'none'});
			});
			$('.viewlist .exit').click(function(){
				$('.viewlist').css({'display':'none'});
				$('.setwin').css({'display':'block'});
				$('.modal').css({'display':'block'});
				$('#content #setup input[name="tabnum"]').val('');
				$('#content #setup input[name="listtype"]').val('');
			});
			$('.setwin #temporder').click(function(){
				$.ajax({
					url:'../demopos/lib/js/getsalelist.ajax.php',
					method:'post',
					data:{'temp':'temp','machinetype':$('#setup input[name="machinetype"]').val()},
					dataType:'html',
					success:function(d){
						$('.viewtemp .reorder').prop('disabled',true);
						$('.viewtemp #salecontent').html(d);
						$('.viewtemp #listcontent').html('');
						//$('.viewtemp #rightnow #ttmoney').html($('.viewtemp #salelist input[name="ttmoney"]').val());
						//$('.viewtemp #rightnow #ttcount').html($('.viewtemp #salelist input[name="ttcount"]').val());
					},
					error:function(e){
						//console.log(e);
					}
				});
				$('.setwin').css({'display':'none'});
				$('.modal').css({'display':'none'});
				$('.viewtemp').css({'display':'block'});
			});
			$('.viewtemp .exit').click(function(){
				$('.viewtemp').css({'display':'none'});
				$('.setwin').css({'display':'block'});
				$('.modal').css({'display':'block'});
			});
			$('#content .listtype').change(function(){
				if($('#content .listtype option:selected').val()=='1'){
					if($('#content #tablab').length>0){
						$('#content #tablab').css({'display':'table-row'});
						if($('#content #tablab #tabnum option').length>0){
							$('#content #tablab #tabnum option').prop('selected',false);
							$('#content #tablab #tabnum #empty').prop('selected',true);
						}
						else{
							$('#content #tablab #tabnum').val('');
						}
					}
					else{
					}
				}
				else{
					if($('#content #tablab').length>0){
						$('#content #tablab').css({'display':'none'});
						if($('#content #tablab #tabnum option').length>0){
							$('#content #tablab #tabnum option').prop('selected',false);
							$('#content #tablab #tabnum #empty').prop('selected',true);
						}
						else{
							$('#content #tablab #tabnum').val('');
						}
					}
					else{
					}
				}
			});
			$('#content #tabnum').change(function(){
				if($('#content #tabnum option').length>0){
					//console.log($('#content #tabnum option:selected').val());
				}
				else{
					//console.log($('#content #tabnum').val());
				}
			});
			$('#content #entertable').click(function(){
				if($('#content .listtype option:selected').val()=='1'&&($('#content #tabnum').val()==''||$('#content #tabnum option:selected').val()=='')){
					$('.tabhint').css({'display':'block'});
					$('.modal').css({'display':'block'});
				}
				else{
					$('#setup input[name="listtype"]').val($('#content .listtype option:selected').val());
					if($('#content #tabnum option').length>0){
						$('#setup input[name="tabnum"]').val($('#content #tabnum option:selected').val());
					}
					else if($('#content #tabnum').length>0){
						$('#setup input[name="tabnum"]').val($('#content #tabnum').val());
					}
					else{
					}
					$('#setup').submit();
				}
			});
			$('.tabhint .check').click(function(){
				$('.tabhint').css({'display':'none'});
				$('.modal').css({'display':'none'});
			});
			$('#content #logout').click(function(){
				//sysmeg.dialog('open');
				$('.sysmeg').css({'display':'block'});
				$('.modal').css({'display':'block'});
			});
			$('.sysmeg .yes').click(function(){
				if($('#content #setup input[name="submachine"]').val().length>0){
					location.href='./index.php?submachine='+$('#content #setup input[name="submachine"]').val();
				}
				else{
					location.href='./index.php';
				}
			});
			$('.sysmeg .no').click(function(){
				//sysmeg.dialog('close');
				$('.sysmeg').css({'display':'none'});
				$('.modal').css({'display':'none'});
			});
			$('#content #open').click(function(){
				$.ajax({
					url:'../demopos/lib/js/open.ajax.php',
					method:'post',
					async: false,
					data:{'usercode':$('#setup input[name="usercode"]').val(),'username':$('#setup input[name="username"]').val(),'machinetype':$('#setup input[name="machinetype"]').val()},
					dataType:'html',
					success:function(d){
						//console.log(d);
						if(d.length>20){
							$.ajax({
								url:'../demopos/lib/js/print.php',
								method:'post',
								data:{'html':'orderpos open.ajax.php '+d},
								dataType:'html',
								success:function(d){/*console.log(d);*/},
								error:function(e){/*console.log(e);*/}
							});
						}
						else{
						}
						if(d=='error'){
							//console.log(d);
						}
						else{
							var bizdate=d.split('-');
							$.ajax({
								url:'../demopos/lib/js/change.class.php',
								method:'post',
								async: false,
								data:{'type':'isclose','machinetype':$('#setup input[name="machinetype"]').val()},
								dataType:'html',
								success:function(d){
									var tempdata=d.split('-');
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos change.class.php '+d},
											dataType:'html',
											success:function(d){/*console.log(d);*/},
											error:function(e){/*console.log(e);*/}
										});
									}
									else{
									}
									//console.log(d);
									$.ajax({
										url:'../demopos/lib/js/create.cmdtxt.php',
										method:'post',
										async: false,
										//data:{'cmd':$('.order#order .companydata #terminalnumber').val()+'-upload_'+$('.order#order .companydata #terminalnumber').val()},
										data:{'cmd':'report'},
										dataType:'html',
										success:function(d){
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
									
									$('#content caption').html(tempdata[0]);
									$('#content #open').css({'display':'none'});
									$('#content #close').css({'display':'block'});
									$('#content #tabnum').prop('disabled',false);
									$('#content .listtype').prop('disabled',false);
									$('#content #entertable').prop('disabled',false);
									$('#content #listtype').css({'display':'table-row'});
									if($('#content .listtype option:selected').val()=='1'){
										$('#content #tablab').css({'display':'table-row'});
									}
									else{
										//$('#content #tablab').css({'display':'table-row'});
									}
									$('#content #orderbut').css({'display':'table-row'});
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
					},
					error:function(e){
						//console.log(e);
					}
				});
			});
			$('#content #close').click(function(){
				$.ajax({
					url:'../demopos/lib/js/close.ajax.php',
					method:'post',
					async: false,
					data:{'usercode':$('#setup input[name="usercode"]').val(),'username':$('#setup input[name="username"]').val(),'machinetype':$('#setup input[name="machinetype"]').val()},
					dataType:'html',
					success:function(d){
						if(d.length>20){
							$.ajax({
								url:'../demopos/lib/js/print.php',
								method:'post',
								data:{'html':'orderpos close.ajax.php '+d},
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
						if(d=='error'){
							//console.log(d);
						}
						else{
							$.ajax({
								url:'../demopos/lib/js/change.class.php',
								method:'post',
								async: false,
								data:{'type':'isopen','machinetype':$('#setup input[name="machinetype"]').val()},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos change.class.php '+d},
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
									
									$('#content #open').css({'display':'block'});
									$('#content #close').css({'display':'none'});
									$('#content #tabnum').prop('disabled',true);
									$('#content .listtype').prop('disabled',true);
									$('#content #entertable').prop('disabled',true);
									$('#content #listtype').css({'display':'none'});
									$('#content #tablab').css({'display':'none'});
									$('#content #orderbut').css({'display':'none'});

									$.ajax({
										url:'../demopos/lib/js/shift.paper.php',
										method:'post',
										async: false,
										data:{'machinename':$('#setup input[name="machinetype"]').val(),'zcounter':d},
										dataType:'html',
										success:function(d){
											if(d.length>20){
												$.ajax({
													url:'../demopos/lib/js/print.php',
													method:'post',
													data:{'html':'orderpos shift.paper.php '+d},
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
											/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
												var tempd=d.split('-');
												posdvrfile=tempd[0];
											}
											else{
											}*/
											//console.log(d);
											$.ajax({
												url:'../demopos/lib/js/create.cmdtxt.php',
												method:'post',
												async: false,
												data:{'cmd':$('#setup input[name="machinetype"]').val()+'-upload_'+$('#setup input[name="machinetype"]').val()},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
											//2017/12/29var mywin=window.open('cashdrawer://upload','','width=1px,height=1px');
											//2017/12/29mywin.document.title='cashdrawer';
										},
										error:function(e){
											//console.log(e);
										}
									});
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
					},
					error:function(e){
						//console.log(e);
					}
				});
			});
			$('#content #reflash').click(function(){
				$.ajax({
					url:'./lib/js/gettable.option.php',
					method:'post',
					async:false,
					data:{'location':'../../../'},
					dataType:'html',
					success:function(d){
						$('#content #tabnum').html(d);
						//console.log(d);
					},
					error:function(e){
						console.log(e);
					}
				});
			});
			$('.viewlist #salecontent').on('click','.listitems',function(){
				$('.viewlist #salecontent .listitems').prop('id','');
				$(this).prop('id','checked');
				$('#content #setup input[name="tabnum"]').val($(this).find('div:eq(0)').html()+';-;'+$(this).find('div:eq(1) input[name="consecnumber"]').val());
				$('#content #setup input[name="listtype"]').val($(this).find('div:eq(1) input[name="listtype"]').val());
				$.ajax({
					url:'../demopos/lib/js/getsaledetail.ajax.php',
					method:'post',
					data:{'company':$('.basic input[name="company"]').val(),'bizdate':$(this).find('div:eq(0)').html(),'no':$(this).find('div:eq(1) input[name="consecnumber"]').val(),'createdatetime':$(this).find('div:eq(5)').html()},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('.viewlist #listcontent').html(d);
						
					},
					error:function(e){
						//console.log(e);
					}
				});
			});
			$('.viewlist #setbutton').click(function(){
				if($('.viewlist #salecontent .listitems#checked').length>0&&$('.viewlist #salecontent .listitems#checked div:eq('+($('.viewlist #salecontent .listitems#checked div').length-1)+')').html()!='Y'){
					$('.viewtempfun #type').val('sale');
					$('.viewtempfun .voidlist').css({'display':'block'});
					$('.viewtempfun .voidtemp').css({'display':'none'});
					$('.viewtempfun').css({'display':'block'});
				}
				else{
				}
			});
			$('.viewtempfun .voidlist').click(function(){
				var membercheck='';
				//if($('.order#order .initsetting #voidsale').val()=='0'){
					/*if($.trim($('.salevoid #salelist #salecontent .listitems#focus div:eq(3)').html()).length!=0){//檢查是否已開立發票
						var hint=confirm('請確認發票正本已收回。');
						if(hint==true){
							var nowbizdate=$('.salevoid #date').html();
							var voidbizdate=$('.salevoid #date').html();
							var voidconsecnumber=$('.salevoid #listno input[name="consecnumber"]').val();
							if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
								var posdvrfile='';
							}
							else{
							}
							//檢查連線狀態
							if($('.initsetting #ourmempointmoney').val()=='1'&&$('.salevoid #listno input[name="memno').val()!=''&&typeof api_point_money_checkserver!=="undefined"&&typeof api_point_money_checkserver==="function"){
								membercheck=api_point_money_checkserver($('.order#order .companydata #company').val(),$('.order#order .companydata #story').val());
								//console.log(membercheck);
								if(typeof membercheck[0]!=="undefined"&&typeof membercheck[0]['state']!=="undefined"&&membercheck[0]['state']=='success'){
								}
								else{
									$('.result #checkfun #submit').prop('disabled',false);
									if($('.sysmeg #name1.syshint8').length>0){
										$('.sysmeg #name1.syshint8').css({'display':''});
									}
									else{
									}
									if($('.sysmeg #name2.syshint8').length>0){
										$('.sysmeg #name2.syshint8').css({'display':''});
									}
									else{
									}
									$('.result #checkfun #submit').prop('disabled',false);
									sysmeg.dialog('open');
									return;
								}
							}
							else{
							}
							$.ajax({
								url:'./lib/js/salevoid.ajax.php',
								method:'post',
								async:false,
								data:{'terminalnumber':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.salevoid #date').html(),'createdatetime':$('.salevoid #credate').val(),'consecnumber':$('.salevoid #listno input[name="consecnumber"]').val(),'name':$('.order#order #tabs4 form[data-id="listform"] input[name="usercode"]').val()},
								dataType:'html',
								success:function(d){
									var temp=d.split('-');
									if(d.length>20){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'salevoid.ajax.php '+d},
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
									if(temp[0]=='success'){
										if(typeof api_point_money_ourmember!=="undefined"&&typeof api_point_money_ourmember==="function"){//網路會員
											var memtype='online';
										}
										else{
											var memtype='offline';
										}
										var memberapi='';
										$.ajax({
											url:'./lib/js/searchmember.pointmoney.ajax.php',
											method:'post',
											async:false,
											data:{'type':memtype,'company':$('.order#order .companydata #company').val(),'story':$('.order#order .companydata #story').val(),'machine':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.salevoid #date').html(),'consecnumber':$('.salevoid #listno input[name="consecnumber"]').val()},
											dataType:'json',
											success:function(d){
												//console.log(d);
												if(d[0].length==0){
												}
												else{
													if(typeof api_point_money_ourmember!=="undefined"&&typeof api_point_money_ourmember==="function"){//網路會員
														memberapi=api_point_money_ourmember(d[0]['company'],d[0]['story'],d[0]['memno'],d[0]['paymoney'],d[0]['giftpoint'],d[0]['memberpoint'],d[0]['membermoney']);
														if(memberapi[0]['state']=='success'){
															$.ajax({
																url:'http://api.tableplus.com.tw/outposandorder/memberapi/change.memberdata.php',
																method:'post',
																async:false,
																data:{'company':$('.order#order .companydata #company').val(),'dep':$('.order#order .companydata #story').val(),'data':memberapi},
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
														$.ajax({
															url:'http://api.tableplus.com.tw/outposandorder/memberapi/insertmemlist.ajax.php',
															method:'post',
															async:false,
															data:{'type':'online','company':$('.order#order .companydata #company').val(),'story':$('.order#order .companydata #story').val(),'settime':$('.order#order .companydata #settime').val(),'data':memberapi,'senddata':d},
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
															url:'../memberapi/point_money.ajax.php',
															method:'post',
															async:false,
															data:{'company':d[0]['company'],'story':d[0]['story'],'memno':d[0]['memno'],'paymoney':d[0]['paymoney'],'giftpoint':d[0]['giftpoint'],'memberpoint':d[0]['memberpoint'],'membermoney':d[0]['membermoney']},
															dataType:'json',
															timeout:5000,
															success:function(d){
																memberapi=d;
																//console.log(res);
															},
															error:function(e,t){
																if(t==="timeout"){
																	memberapi=[{"state":"fail","message":"AJAX timeout"}];
																}
																else{
																	memberapi=e;
																}
															}
														});
														//memberapi=api_point_money_ourmember(d[0]['company'],d[0]['story'],d[0]['memno'],d[0]['paymoney'],d[0]['giftpoint'],d[0]['memberpoint'],d[0]['membermoney']);
														if(memberapi[0]['state']=='success'){
															$.ajax({
																url:'../memberapi/change.memberdata.php',
																method:'post',
																async:false,
																data:{'company':$('.order#order .companydata #company').val(),'dep':$('.order#order .companydata #story').val(),'data':memberapi},
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
														$.ajax({
															url:'../memberapi/insertmemlist.ajax.php',
															method:'post',
															async:false,
															data:{'company':$('.order#order .companydata #company').val(),'story':$('.order#order .companydata #story').val(),'settime':$('.order#order .companydata #settime').val(),'data':memberapi,'senddata':d},
															dataType:'html',
															success:function(d){
																//console.log(d);
															},
															error:function(e){
																//console.log(e);
															}
														});
													}
												}
											},
											error:function(e){
												//console.log(e);
											}
										});
										//console.log($('.salevoid #list #listcontent input[name="invnumber"]').val());
										$.ajax({
											url:'./lib/js/deleteinv.ajax.php',
											method:'post',
											async:false,
											data:{'machinename':$('.salevoid #listno input[name="machinename"]').val(),'bizdate':$('.salevoid #date').html(),'number':$('.salevoid #list input[name="invnumber"]').val()},
											dataType:'html',
											success:function(d){
												if(d.length>40){
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														data:{'html':'deleteinv.ajax.php '+d},
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
												//2017/12/4var mywin=window.open('cashdrawer://reprint','','width=1px,height=1px');
												//2017/12/4mywin.document.title='cashdrawer';
											},
											error:function(e){
												//console.log(e);
											}
										});
										$('.order#order #billfun #billfun2 #salevoid').trigger('click');
										$('.salevoid #listno').html('');
										$('.salevoid #list #listcontent').html('');
										$('.salevoid #date').html('');
										$('.salevoid #credate').html('');
										if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
											var posdvrfile=temp[1];
										}
										else{
										}
									}
									else{
										//console.log(d);
									}
								},
								error:function(e){
									//console.log(e);
								}
							});
							$.ajax({
								url:'./lib/js/create.cmdtxt.php',
								method:'post',
								async: false,
								data:{'cmd':nowbizdate.substr(0,6)+'-change'},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});
							//錢都錄借接
							if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
								//start tag
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									async:false,
									data:{'html':'start posdvr-sendmessage','file':'posdvr'},
									dataType:'html',
									success:function(d){
										//console.log(d);
									},
									error:function(e){
										//console.log(e);
									}
								});
								if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
									var res=api_sendmessage_posdvr(posdvrfile);
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										data:{'html':'success '+posdvrfile,'file':'posdvr'},
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
								//end tag
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									async:false,
									data:{'html':'end posdvr-sendmessage','file':'posdvr'},
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
							//集點樹收點流程
							if($('.order#order .initsetting #pointtree').length>0&&$('.order#order .initsetting #pointtree').val()=='1'){
								//start tag
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									data:{'html':'start point-tree-void transfer','file':'point-tree'},
									dataType:'html',
									success:function(d){
										//console.log(d);
									},
									error:function(e){
										//console.log(e);
									}
								});
								if(typeof api_void_pointtree!=="undefined"&&typeof api_void_pointtree==="function"){
									var voidres=api_void_pointtree(voidbizdate,voidconsecnumber);
									if(voidres==''){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											async:false,
											data:{'html':'point-tree-void voidpoint 意外錯誤','file':'point-tree'},
											dataType:'html',
											success:function(d){
												//console.log(d);
											},
											error:function(e){
												//console.log(e);
											}
										});
									}
									else if(typeof voidres['data']!=="undefined"){//success
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											async:false,
											data:{'html':'pass point-tree-void transfer -PHP_EOL-pos_token_tx_id:'+voidres['data']['pos_token_tx_id']+'-PHP_EOL-store_balance:'+voidres['data']['store_balance']+'-PHP_EOL-user_balance:'+voidres['data']['user_balance'],'file':'point-tree'},
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
										var message=JSON.parse(voidres['responseText']);
										if(voidres['status']==='timeout'){
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												async:false,
												data:{'html':'point-tree-void transfer timeout','file':'point-tree'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
										}
										else if(voidres['status']=='400'||voidres['status']=='406'){
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												async:false,
												data:{'html':'point-tree-void transfer error -PHP_EOL-status:'+voidres['status']+'-PHP_EOL-code:'+message['errors'][0]['code']+'-PHP_EOL-message:'+message['errors'][0]['message'],'file':'point-tree'},
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
												data:{'html':'point-tree-void transfer 意外錯誤','file':'point-tree'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
										}
									}
								}
								else{
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										async:false,
										data:{'html':'api void pointtree is not function','file':'point-tree'},
										dataType:'html',
										success:function(d){
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
								}
								//end tag
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									data:{'html':'end point-tree-void transfer','file':'point-tree'},
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
						}
						else{
						}
					}
					else{*/
						var nowbizdate=$('.viewlist #salecontent .listitems#checked div:eq(0)').html();
						var voidbizdate=$('.viewlist #salecontent .listitems#checked div:eq(0)').html();
						var voidconsecnumber=$('.viewlist #salecontent .listitems#checked div:eq(2) input[name="consecnumber"]').val();
						/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
							var posdvrfile='';
						}
						else{
						}*/
						//檢查連線狀態
						if($('.initsetting #ourmempointmoney').val()=='1'&&$('.salevoid #listno input[name="memno').val()!=''&&typeof api_point_money_checkserver!=="undefined"&&typeof api_point_money_checkserver==="function"){
							membercheck=api_point_money_checkserver($('.order#order .companydata #company').val(),$('.order#order .companydata #story').val());
							//console.log(membercheck);
							/*if(typeof membercheck[0]!=="undefined"&&typeof membercheck[0]['state']!=="undefined"&&membercheck[0]['state']=='success'){
							}
							else{
								$('.result #checkfun #submit').prop('disabled',false);
								if($('.sysmeg #name1.syshint8').length>0){
									$('.sysmeg #name1.syshint8').css({'display':''});
								}
								else{
								}
								if($('.sysmeg #name2.syshint8').length>0){
									$('.sysmeg #name2.syshint8').css({'display':''});
								}
								else{
								}
								$('.result #checkfun #submit').prop('disabled',false);
								sysmeg.dialog('open');
								return;
							}*/
						}
						else{
						}
						$.ajax({
							url:'../demopos/lib/js/salevoid.ajax.php',
							method:'post',
							async:false,
							data:{'terminalnumber':$('#content #setup input[name="machinetype"]').val(),'bizdate':$('.viewlist #salecontent .listitems#checked div:eq(0)').html(),'createdatetime':$('#content #setup input[name="bizdate"]').val(),'consecnumber':$('.viewlist #salecontent .listitems#checked div:eq(1) input[name="consecnumber"]').val(),'name':$('#content #setup input[name="usercode"]').val(),'memno':$('.viewlist #salecontent .listitems#checked div:eq(1) input[name="memno"]').val()},
							dataType:'html',
							success:function(d){
								//console.log(d);
								var temp=d.split('-');
								if(d.length>20){
									$.ajax({
										url:'../demopos/lib/js/print.php',
										method:'post',
										data:{'html':'orderpos viewlist-viewvoid salevoid.ajax.php '+d},
										dataType:'html',
										success:function(d){/*console.log(d);*/},
										error:function(e){/*console.log(e);*/}
									});
								}
								else{
								}
								if(temp[0]=='success'){
									if(typeof api_point_money_ourmember!=="undefined"&&typeof api_point_money_ourmember==="function"){//網路會員
										var memtype='online';
									}
									else{
										var memtype='offline';
									}
									var memberapi='';
									$.ajax({
										url:'../demopos/lib/js/searchmember.pointmoney.ajax.php',
										method:'post',
										async:false,
										data:{'type':memtype,'company':$('.basic input[name="company"]').val(),'story':$('.basic input[name="dep"]').val(),'machine':$('#content #setup input[name="machinetype"]').val(),'bizdate':$('.viewlist #salecontent .listitems#checked div:eq(0)').html(),'consecnumber':$('.viewlist #salecontent .listitems#checked div:eq(1) input[name="consecnumber"]').val()},
										dataType:'json',
										success:function(d){
											//console.log(d);
											if(d[0].length==0){
											}
											else{
												if(typeof api_point_money_ourmember!=="undefined"&&typeof api_point_money_ourmember==="function"){//網路會員
													memberapi=api_point_money_ourmember(d[0]['company'],d[0]['story'],d[0]['memno'],d[0]['paymoney'],d[0]['giftpoint'],d[0]['memberpoint'],d[0]['membermoney']);
													if(memberapi[0]['state']=='success'){
														$.ajax({
															url:'http://api.tableplus.com.tw/outposandorder/memberapi/change.memberdata.php',
															method:'post',
															async:false,
															data:{'company':$('.basic input[name="company"]').val(),'dep':$('.basic input[name="dep"]').val(),'data':memberapi},
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
													$.ajax({
														url:'http://api.tableplus.com.tw/outposandorder/memberapi/insertmemlist.ajax.php',
														method:'post',
														async:false,
														data:{'type':'online','company':$('.basic input[name="company"]').val(),'story':$('.basic input[name="dep"]').val(),'settime':$('.basic input[name="settime"]').val(),'data':memberapi,'senddata':d},
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
														url:'../memberapi/point_money.ajax.php',
														method:'post',
														async:false,
														data:{'company':d[0]['company'],'story':d[0]['story'],'memno':d[0]['memno'],'paymoney':d[0]['paymoney'],'giftpoint':d[0]['giftpoint'],'memberpoint':d[0]['memberpoint'],'membermoney':d[0]['membermoney']},
														dataType:'json',
														timeout:5000,
														success:function(d){
															memberapi=d;
															//console.log(res);
														},
														error:function(e,t){
															if(t==="timeout"){
																memberapi=[{"state":"fail","message":"AJAX timeout"}];
															}
															else{
																memberapi=e;
															}
														}
													});
													//memberapi=api_point_money_ourmember(d[0]['company'],d[0]['story'],d[0]['memno'],d[0]['paymoney'],d[0]['giftpoint'],d[0]['memberpoint'],d[0]['membermoney']);
													if(memberapi[0]['state']=='success'){
														$.ajax({
															url:'../memberapi/change.memberdata.php',
															method:'post',
															async:false,
															data:{'company':$('.basic input[name="company"]').val(),'dep':$('.basic input[name="dep"]').val(),'data':memberapi},
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
													$.ajax({
														url:'../memberapi/insertmemlist.ajax.php',
														method:'post',
														async:false,
														data:{'company':$('.basic input[name="company"]').val(),'story':$('.basic input[name="dep"]').val(),'settime':$('.basic input[name="settime"]').val(),'data':memberapi,'senddata':d},
														dataType:'html',
														success:function(d){
															//console.log(d);
														},
														error:function(e){
															//console.log(e);
														}
													});
												}
											}
										},
										error:function(e){
											//console.log(e);
										}
									});
									//console.log($('.salevoid #list #listcontent input[name="invnumber"]').val());
									/*$.ajax({
										url:'./lib/js/deleteinv.ajax.php',
										method:'post',
										async:false,
										data:{'machinename':$('.salevoid #listno input[name="machinename"]').val(),'bizdate':$('.salevoid #date').html(),'number':$('.salevoid #list input[name="invnumber"]').val()},
										dataType:'html',
										success:function(d){
											if(d.length>40){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'deleteinv.ajax.php '+d},
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
											//2017/12/4var mywin=window.open('cashdrawer://reprint','','width=1px,height=1px');
											//2017/12/4mywin.document.title='cashdrawer';
										},
										error:function(e){
											//console.log(e);
										}
									});*/
									$('.viewtempfun').css({'display':'none'});
									$('.setwin #vieworder').trigger('click');
									/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
										var posdvrfile=temp[1];
									}
									else{
									}*/
								}
								else{
									//console.log(d);
								}
							},
							error:function(e){
								//console.log(e);
							}
						});
						$.ajax({
							url:'../demopos/lib/js/create.cmdtxt.php',
							method:'post',
							async: false,
							data:{'cmd':nowbizdate.substr(0,6)+'-change'},
							dataType:'html',
							success:function(d){
								//console.log(d);
							},
							error:function(e){
								//console.log(e);
							}
						});
						/*錢都錄借接*/
						/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
							//start tag
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								async:false,
								data:{'html':'start posdvr-sendmessage','file':'posdvr'},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});
							if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
								var res=api_sendmessage_posdvr(posdvrfile);
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									data:{'html':'success '+posdvrfile,'file':'posdvr'},
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
							//end tag
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								async:false,
								data:{'html':'end posdvr-sendmessage','file':'posdvr'},
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
						}*/
						/*集點樹收點流程*/
						/*if($('.order#order .initsetting #pointtree').length>0&&$('.order#order .initsetting #pointtree').val()=='1'){
							//start tag
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'start point-tree-void transfer','file':'point-tree'},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});
							if(typeof api_void_pointtree!=="undefined"&&typeof api_void_pointtree==="function"){
								var voidres=api_void_pointtree(voidbizdate,voidconsecnumber);
								if(voidres==''){
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										async:false,
										data:{'html':'point-tree-void voidpoint 意外錯誤','file':'point-tree'},
										dataType:'html',
										success:function(d){
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
								}
								else if(typeof voidres['data']!=="undefined"){//success
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										async:false,
										data:{'html':'pass point-tree-void transfer -PHP_EOL-pos_token_tx_id:'+voidres['data']['pos_token_tx_id']+'-PHP_EOL-store_balance:'+voidres['data']['store_balance']+'-PHP_EOL-user_balance:'+voidres['data']['user_balance'],'file':'point-tree'},
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
									var message=JSON.parse(voidres['responseText']);
									if(voidres['status']==='timeout'){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											async:false,
											data:{'html':'point-tree-void transfer timeout','file':'point-tree'},
											dataType:'html',
											success:function(d){
												//console.log(d);
											},
											error:function(e){
												//console.log(e);
											}
										});
									}
									else if(voidres['status']=='400'||voidres['status']=='406'){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											async:false,
											data:{'html':'point-tree-void transfer error -PHP_EOL-status:'+voidres['status']+'-PHP_EOL-code:'+message['errors'][0]['code']+'-PHP_EOL-message:'+message['errors'][0]['message'],'file':'point-tree'},
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
											data:{'html':'point-tree-void transfer 意外錯誤','file':'point-tree'},
											dataType:'html',
											success:function(d){
												//console.log(d);
											},
											error:function(e){
												//console.log(e);
											}
										});
									}
								}
							}
							else{
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									async:false,
									data:{'html':'api void pointtree is not function','file':'point-tree'},
									dataType:'html',
									success:function(d){
										//console.log(d);
									},
									error:function(e){
										//console.log(e);
									}
								});
							}
							//end tag
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'end point-tree-void transfer','file':'point-tree'},
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
						}*/
					//}
				/*}
				else{
					$('.verpsw input[name="type"]').val('void');
					verpsw.dialog('open');
				}*/
			});
			$('.viewtemp #salecontent').on('click','.listitems',function(){
				$('.viewtemp #salecontent .listitems').prop('id','');
				$(this).prop('id','checked');
				$('#content #setup input[name="tabnum"]').val($(this).find('div:eq(0)').html()+';-;'+$(this).find('div:eq(1) input[name="consecnumber"]').val());
				$('#content #setup input[name="listtype"]').val($(this).find('div:eq(1) input[name="listtype"]').val());
				$.ajax({
					url:'../demopos/lib/js/getsaledetail.ajax.php',
					method:'post',
					data:{'company':$('.basic input[name="company"]').val(),'bizdate':$(this).find('div:eq(0)').html(),'no':$(this).find('div:eq(1) input[name="consecnumber"]').val(),'createdatetime':$(this).find('div:eq(5)').html(),'temp':'temp'},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('.viewtemp .reorder').prop('disabled',false);
						$('.viewtemp #listcontent').html(d);
						/*if($('.viewtemp #salelist #salecontent .listitems:eq('+index+') div:eq(7)').html()=="Y"){
							$('.viewtemp #viewvoid').prop('disabled',true);
							$('.viewtemp #otherfun button#plus').prop('disabled',true);
							$('.viewtemp #otherfun button#openinv').prop('disabled',true);
							$('.viewtemp #fun button#tag').prop('disabled',true);
							$('.viewtemp #fun button#forall').prop('disabled',true);
							$('.viewtemp #fun button#kitchen').prop('disabled',true);
							$('.viewtemp #fun button#changecheck').prop('disabled',true);
						}
						else{
							if($('.order#order .companydata #ispad').val()=='submachine'){
								$('.viewtemp #viewvoid').prop('disabled',true);
							}
							else{
								$('.viewtemp #viewvoid').prop('disabled',false);
							}
							$('.viewtemp #otherfun button#plus').prop('disabled',false);
							if($.trim($('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html())==""&&$('.order#order .initsetting #useinv').val()==1&&$('.order#order .initsetting #temptoinv').val()==1){
								$('.viewtemp #otherfun button#openinv').prop('disabled',false);
								$('.viewtemp #otherfun button#reinv').prop('disabled',true);
							}
							else{
								$('.viewtemp #otherfun button#openinv').prop('disabled',true);
								$('.viewtemp #otherfun button#reinv').prop('disabled',false);
							}
							$('.viewtemp #fun button#tag').prop('disabled',false);
							$('.viewtemp #fun button#forall').prop('disabled',false);
							$('.viewtemp #fun button#kitchen').prop('disabled',false);
							$('.viewtemp #fun button#changecheck').prop('disabled',false);
						}*/
						//$('.viewtemp #fun button#list').prop('disabled',false);
						//$('.viewtemp #fun #kitchen').prop('disabled',false);
						
					},
					error:function(e){
						//console.log(e);
					}
				});
			});
			$('.viewtemp .reorder').click(function(){
				//與發票模組同進退
				/*if($.trim($('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html()).length!=0){//檢查是否開立發票
					$('.temptoinv').html('');
					$('.temptoinv').append("<button id='delplus' value='修改帳單'>修改帳單</button>");
					$('.temptoinv').append("<button id='buylist' value='結帳'>結帳</button>");
					$('.temptoinv').append("<button id='cancel' value='取消'>取消</button>");
					temptoinv.dialog('open');
				}
				else{*/
					//console.log($.trim($('.viewtemp #listno').html()));
					/*if($('.order#order .initsetting #controltable').val()=='1'){
						$.ajax({
							url:'./lib/js/checktabstate.ajax.php',
							method:'post',
							async:false,
							data:{'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'machine':$('.order#order #tabs4 form[data-id="listform"] input[name="machinetype"]').val()},
							dataType:'html',
							success:function(d){
								console.log(d);
								var tempd=d.split('-');
								if(tempd[0]=='unlock'){
									temporder($('.viewtemp #date').html(),$('.viewtemp #listno input[name="consecnumber"]').val(),$('.order#order #tabs4 form[data-id="listform"] input[name="machinetype"]').val(),$('.order#order .companydata #company').val(),$('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="usercode"]').val(),$('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="username"]').val());
									viewtemp.dialog('close');
									if($('.funbox').length>0&&funbox.dialog('isOpen')){
										funbox.dialog('close');
										inittable.dialog('close');
									}
									else{
									}
								}
								else{
									var res=confirm(tempd[1]+'點餐中。\n是否仍要點餐？');
									if(res==true){
										temporder($('.viewtemp #date').html(),$('.viewtemp #listno input[name="consecnumber"]').val(),$('.order#order #tabs4 form[data-id="listform"] input[name="machinetype"]').val(),$('.order#order .companydata #company').val(),$('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="usercode"]').val(),$('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="username"]').val());
										viewtemp.dialog('close');
										if($('.funbox').length>0&&funbox.dialog('isOpen')){
											funbox.dialog('close');
											inittable.dialog('close');
										}
										else{
										}
									}
									else{
									}
								}
							},
							error:function(e){
								console.log(e);
							}
						});
					}
					else{*/
						$('#content #setup').submit();
						//temporder($('.viewtemp #date').html(),$('.viewtemp #listno input[name="consecnumber"]').val(),$('.order#order #tabs4 form[data-id="listform"] input[name="machinetype"]').val(),$('.order#order .companydata #company').val(),$('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="usercode"]').val(),$('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="username"]').val());

						//viewtemp.dialog('close');
					//}
				//}
			});
			$('.viewtemp #setbutton').click(function(){
				if($('.viewtemp #salecontent .listitems#checked').length>0&&$('.viewtemp #salecontent .listitems#checked div:eq('+($('.viewtemp #salecontent .listitems#checked div').length-1)+')').html()!='Y'){
					$('.viewtempfun #type').val('temp');
					$('.viewtempfun .voidlist').css({'display':'none'});
					$('.viewtempfun .voidtemp').css({'display':'block'});
					$('.viewtempfun').css({'display':'block'});
				}
				else{
				}
			});
			$('.viewtempfun .voidtemp').click(function(){
				/*if($('.order#order .initsetting #controltable').val()=='1'){
					$.ajax({
						url:'./lib/js/checktabstate.ajax.php',
						method:'post',
						async:false,
						data:{'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'machine':$('.order#order #tabs4 form[data-id="listform"] input[name="machinetype"]').val()},
						dataType:'html',
						success:function(d){
							console.log(d);
							var tempd=d.split('-');
							if(tempd[0]=='unlock'){
								//$('.viewtemp #viewvoid').prop('disabled',true);
								if($('.order#order .initsetting #voidsale').val()=='0'){
									if($.trim($('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html()).length!=0){//檢查是否已開立發票
										var hint=confirm('請確認發票正本已收回。');
										if(hint==true){
											var nowbizdate=$('.viewtemp #date').html();
											if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
												var posdvrfile='';
											}
											else{
											}
											$.ajax({
												url:'./lib/js/viewvoid.ajax.php',
												method:'post',
												async:false,
												data:{'terminalnumber':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'name':$('.order#order #tabs4 form[data-id="listform"] input[name="usercode"]').val()},
												dataType:'html',
												success:function(d){
													var tempres=d.split('-');
													if(d.length>20){
														$.ajax({
															url:'./lib/js/print.php',
															method:'post',
															async:false,
															data:{'html':'viewtemp-viewvoid viewvoid.ajax.php '+d},
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
													if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
														var posdvrfile=tempres[1];
													}
													else{
													}
													//console.log(d);
													if(tempres[0]=='success'){
														$.ajax({//作廢發票
															url:'./lib/js/deleteinv.ajax.php',
															method:'post',
															async:false,
															data:{'machinename':$('.viewtemp #listno input[name="machinename"]').val(),'bizdate':$('.viewtemp #date').html(),'number':$('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html()},
															dataType:'html',
															success:function(d){
																if(d.length>40){
																	$.ajax({
																		url:'./lib/js/print.php',
																		method:'post',
																		data:{'html':'viewtemp-viewvoid deleteinv.ajax.php '+d},
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
																$('.viewtemp #salelist #salecontent .listitems').prop('id','');
																$('.viewtemp #listno').html('');
																$('.viewtemp #list #listcontent').html('');
																$('.viewtemp #date').html('');
																$('.viewtemp #credate').html('');
																$('.viewtemp #otherfun button#plus').prop('disabled',true);
																$('.viewtemp #otherfun button#openinv').prop('disabled',true);
																$('.viewtemp #viewvoid').prop('disabled',true);
																$('.viewtemp #fun button#forall').prop('disabled',true);
																$('.viewtemp #fun button#list').prop('disabled',true);
																$('.viewtemp #fun button#tag').prop('disabled',true);
																$('.viewtemp #fun button#kitchen').prop('disabled',true);
																$('.viewtemp #fun button#changecheck').prop('disabled',true);
																$('.order#order #MemberBill #list #funbox #view').trigger('click');
																//2017/12/29var mywin=window.open('cashdrawer://reprint','','width=1px,height=1px');
																//2017/12/29mywin.document.title='cashdrawer';
															},
															error:function(e){
																//console.log(e);
															}
														});
													}
													else{
														//console.log(d);
													}
												},
												error:function(e){
													//console.log(e);
												}
											});
											$.ajax({
												url:'./lib/js/create.cmdtxt.php',
												method:'post',
												async: false,
												data:{'cmd':nowbizdate.substr(0,6)+'-change'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
											//錢都錄借接
											if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
												//start tag
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'start posdvr-sendmessage','file':'posdvr'},
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
													var res=api_sendmessage_posdvr(posdvrfile);
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														data:{'html':'success '+posdvrfile,'file':'posdvr'},
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
												//end tag
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'end posdvr-sendmessage','file':'posdvr'},
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
										}
										else{
										}
										
									}
									else{
										var nowbizdate=$('.viewtemp #date').html();
										if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
											var posdvrfile='';
										}
										else{
										}
										$.ajax({
											url:'./lib/js/viewvoid.ajax.php',
											method:'post',
											data:{'terminalnumber':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'name':$('.order#order #tabs4 form[data-id="listform"] input[name="usercode"]').val()},
											dataType:'html',
											success:function(d){
												var tempres=d.split('-');
												if(d.length>20){
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														data:{'html':'viewtemp-viewvoid viewvoid.ajax.php '+d},
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
												if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
													var posdvrfile=tempres[1];
												}
												else{
												}
												//console.log(d);
												$('.viewtemp #salelist #salecontent .listitems').prop('id','');
												$('.viewtemp #listno').html('');
												$('.viewtemp #list #listcontent').html('');
												$('.viewtemp #date').html('');
												$('.viewtemp #credate').html('');
												$('.viewtemp #otherfun button#plus').prop('disabled',true);
												$('.viewtemp #otherfun button#openinv').prop('disabled',true);
												$('.viewtemp #viewvoid').prop('disabled',true);
												$('.viewtemp #fun button#forall').prop('disabled',true);
												$('.viewtemp #fun button#list').prop('disabled',true);
												$('.viewtemp #fun button#tag').prop('disabled',true);
												$('.viewtemp #fun button#kitchen').prop('disabled',true);
												$('.viewtemp #fun button#changecheck').prop('disabled',true);
												$('.order#order #MemberBill #list #funbox #view').trigger('click');
											},
											error:function(e){
												//console.log(e);
											}
										});
										//錢都錄借接
										if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
											//start tag
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												async:false,
												data:{'html':'start posdvr-sendmessage','file':'posdvr'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
											if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
												var res=api_sendmessage_posdvr(posdvrfile);
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'success '+posdvrfile,'file':'posdvr'},
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
											//end tag
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												async:false,
												data:{'html':'end posdvr-sendmessage','file':'posdvr'},
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
										$.ajax({
											url:'./lib/js/create.cmdtxt.php',
											method:'post',
											async: false,
											data:{'cmd':nowbizdate.substr(0,6)+'-change'},
											dataType:'html',
											success:function(d){
												//console.log(d);
											},
											error:function(e){
												//console.log(e);
											}
										});
									}
								}
								else{
									$('.verpsw input[name="type"]').val('viewvoid');
									verpsw.dialog('open');
								}
							}
							else{
								var res=confirm(tempd[1]+'點餐中。\n是否仍要點餐？');
								if(res==true){
									if($('.order#order .initsetting #voidsale').val()=='0'){
										if($.trim($('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html()).length!=0){//檢查是否已開立發票
											var hint=confirm('請確認發票正本已收回。');
											if(hint==true){
												var nowbizdate=$('.viewtemp #date').html();
												if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
													var posdvrfile='';
												}
												else{
												}
												$.ajax({
													url:'./lib/js/viewvoid.ajax.php',
													method:'post',
													async:false,
													data:{'terminalnumber':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'name':$('.order#order #tabs4 form[data-id="listform"] input[name="usercode"]').val()},
													dataType:'html',
													success:function(d){
														var tempres=d.split('-');
														if(d.length>20){
															$.ajax({
																url:'./lib/js/print.php',
																method:'post',
																async:false,
																data:{'html':'viewtemp-viewvoid viewvoid.ajax.php '+d},
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
														if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
															var posdvrfile=tempres[1];
														}
														else{
														}
														//console.log(d);
														if(tempres[0]=='success'){
															$.ajax({//作廢發票
																url:'./lib/js/deleteinv.ajax.php',
																method:'post',
																async:false,
																data:{'machinename':$('.viewtemp #listno input[name="machinename"]').val(),'bizdate':$('.viewtemp #date').html(),'number':$('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html()},
																dataType:'html',
																success:function(d){
																	if(d.length>40){
																		$.ajax({
																			url:'./lib/js/print.php',
																			method:'post',
																			async:false,
																			data:{'html':'viewtemp-viewvoid deleteinv.ajax.php '+d},
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
																	$('.viewtemp #salelist #salecontent .listitems').prop('id','');
																	$('.viewtemp #listno').html('');
																	$('.viewtemp #list #listcontent').html('');
																	$('.viewtemp #date').html('');
																	$('.viewtemp #credate').html('');
																	$('.viewtemp #otherfun button#plus').prop('disabled',true);
																	$('.viewtemp #otherfun button#openinv').prop('disabled',true);
																	$('.viewtemp #viewvoid').prop('disabled',true);
																	$('.viewtemp #fun button#forall').prop('disabled',true);
																	$('.viewtemp #fun button#list').prop('disabled',true);
																	$('.viewtemp #fun button#tag').prop('disabled',true);
																	$('.viewtemp #fun button#kitchen').prop('disabled',true);
																	$('.viewtemp #fun button#changecheck').prop('disabled',true);
																	$('.order#order #MemberBill #list #funbox #view').trigger('click');
																	//2017/12/29var mywin=window.open('cashdrawer://reprint','','width=1px,height=1px');
																	//2017/12/29mywin.document.title='cashdrawer';
																},
																error:function(e){
																	//console.log(e);
																}
															});
														}
														else{
															//console.log(d);
														}
													},
													error:function(e){
														//console.log(e);
													}
												});
												//錢都錄借接
												if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
													//start tag
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														async:false,
														data:{'html':'start posdvr-sendmessage','file':'posdvr'},
														dataType:'html',
														success:function(d){
															//console.log(d);
														},
														error:function(e){
															//console.log(e);
														}
													});
													if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
														var res=api_sendmessage_posdvr(posdvrfile);
														$.ajax({
															url:'./lib/js/print.php',
															method:'post',
															data:{'html':'success '+posdvrfile,'file':'posdvr'},
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
													//end tag
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														async:false,
														data:{'html':'end posdvr-sendmessage','file':'posdvr'},
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
												$.ajax({
													url:'./lib/js/create.cmdtxt.php',
													method:'post',
													async: false,
													data:{'cmd':nowbizdate.substr(0,6)+'-change'},
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
											
										}
										else{
											var nowbizdate=$('.viewtemp #date').html();
											if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
												var posdvrfile='';
											}
											else{
											}
											$.ajax({
												url:'./lib/js/viewvoid.ajax.php',
												method:'post',
												async:false,
												data:{'terminalnumber':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'name':$('.order#order #tabs4 form[data-id="listform"] input[name="usercode"]').val()},
												dataType:'html',
												success:function(d){
													var tempres=d.split('-');
													if(d.length>20){
														$.ajax({
															url:'./lib/js/print.php',
															method:'post',
															async:false,
															data:{'html':'viewtemp-viewvoid viewvoid.ajax.php '+d},
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
													if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
														var posdvrfile=tempres[1];
													}
													else{
													}
													//console.log(d);
													$('.viewtemp #salelist #salecontent .listitems').prop('id','');
													$('.viewtemp #listno').html('');
													$('.viewtemp #list #listcontent').html('');
													$('.viewtemp #date').html('');
													$('.viewtemp #credate').html('');
													$('.viewtemp #otherfun button#plus').prop('disabled',true);
													$('.viewtemp #otherfun button#openinv').prop('disabled',true);
													$('.viewtemp #viewvoid').prop('disabled',true);
													$('.viewtemp #fun button#forall').prop('disabled',true);
													$('.viewtemp #fun button#list').prop('disabled',true);
													$('.viewtemp #fun button#tag').prop('disabled',true);
													$('.viewtemp #fun button#kitchen').prop('disabled',true);
													$('.viewtemp #fun button#changecheck').prop('disabled',true);
													$('.order#order #MemberBill #list #funbox #view').trigger('click');
												},
												error:function(e){
													//console.log(e);
												}
											});
											//錢都錄借接
											if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
												//start tag
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'start posdvr-sendmessage','file':'posdvr'},
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
												if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
													var res=api_sendmessage_posdvr(posdvrfile);
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														data:{'html':'success '+posdvrfile,'file':'posdvr'},
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
												//end tag
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'end posdvr-sendmessage','file':'posdvr'},
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
											$.ajax({
												url:'./lib/js/create.cmdtxt.php',
												method:'post',
												async: false,
												data:{'cmd':nowbizdate.substr(0,6)+'-change'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
										}
									}
									else{
										$('.verpsw input[name="type"]').val('viewvoid');
										verpsw.dialog('open');
									}
								}
								else{
								}
							}
						},
						error:function(e){
							console.log(e);
						}
					});
				}
				else{*/
					//$('.viewtemp #viewvoid').prop('disabled',true);
					//if($('.order#order .initsetting #voidsale').val()=='0'){
						/*if($.trim($('.viewtemp #salecontent .listitems#checked div:eq(3)').html()).length!=0){//檢查是否已開立發票
							var hint=confirm('請確認發票正本已收回。');
							if(hint==true){
								var nowbizdate=$('.viewtemp #date').html();
								if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
									var posdvrfile='';
								}
								else{
								}
								$.ajax({
									url:'./lib/js/viewvoid.ajax.php',
									method:'post',
									async:false,
									data:{'terminalnumber':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'name':$('.order#order #tabs4 form[data-id="listform"] input[name="usercode"]').val()},
									dataType:'html',
									success:function(d){
										var tempres=d.split('-');
										if(d.length>20){
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												data:{'html':'viewtemp-viewvoid viewvoid.ajax.php '+d},
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
										if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
											var posdvrfile=tempres[1];
										}
										else{
										}
										//console.log(d);
										if(tempres[0]=='success'){
											$.ajax({//作廢發票
												url:'./lib/js/deleteinv.ajax.php',
												method:'post',
												async:false,
												data:{'machinename':$('.viewtemp #listno input[name="machinename"]').val(),'bizdate':$('.viewtemp #date').html(),'number':$('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html()},
												dataType:'html',
												success:function(d){
													if(d.length>40){
														$.ajax({
															url:'./lib/js/print.php',
															method:'post',
															async:false,
															data:{'html':'viewtemp-viewvoid deleteinv.ajax.php '+d},
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
													$('.viewtemp #salelist #salecontent .listitems').prop('id','');
													$('.viewtemp #listno').html('');
													$('.viewtemp #list #listcontent').html('');
													$('.viewtemp #date').html('');
													$('.viewtemp #credate').html('');
													$('.viewtemp #otherfun button#plus').prop('disabled',true);
													$('.viewtemp #otherfun button#openinv').prop('disabled',true);
													$('.viewtemp #viewvoid').prop('disabled',true);
													$('.viewtemp #fun button#forall').prop('disabled',true);
													$('.viewtemp #fun button#list').prop('disabled',true);
													$('.viewtemp #fun button#tag').prop('disabled',true);
													$('.viewtemp #fun button#kitchen').prop('disabled',true);
													$('.viewtemp #fun button#changecheck').prop('disabled',true);
													$('.order#order #MemberBill #list #funbox #view').trigger('click');
													//2017/12/29var mywin=window.open('cashdrawer://reprint','','width=1px,height=1px');
													//2017/12/29mywin.document.title='cashdrawer';
												},
												error:function(e){
													//console.log(e);
												}
											});
										}
										else{
											//console.log(d);
										}
									},
									error:function(e){
										//console.log(e);
									}
								});
								//錢都錄借接
								if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
									//start tag
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										async:false,
										data:{'html':'start posdvr-sendmessage','file':'posdvr'},
										dataType:'html',
										success:function(d){
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
									if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
										var res=api_sendmessage_posdvr(posdvrfile);
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'success '+posdvrfile,'file':'posdvr'},
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
									//end tag
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										async:false,
										data:{'html':'end posdvr-sendmessage','file':'posdvr'},
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
								$.ajax({
									url:'./lib/js/create.cmdtxt.php',
									method:'post',
									async: false,
									data:{'cmd':nowbizdate.substr(0,6)+'-change'},
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
							
						}
						else{*/
							var nowbizdate=$('.viewtemp #salecontent .listitems#checked div:eq(0)').html();
							//手機POS不做錢都錄
							/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
								var posdvrfile='';
							}
							else{
							}*/
							$.ajax({
								url:'../demopos/lib/js/viewvoid.ajax.php',
								method:'post',
								async:false,
								data:{'terminalnumber':$('#content #setup input[name="machinetype"]').val(),'bizdate':$('.viewtemp #salecontent .listitems#checked div:eq(0)').html(),'consecnumber':$('.viewtemp #salecontent .listitems#checked div:eq(1) input[name="consecnumber"]').val(),'name':$('#content #setup input[name="usercode"]').val()},
								dataType:'html',
								success:function(d){
									var tempres=d.split('-');
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos viewtemp-viewvoid viewvoid.ajax.php '+d},
											dataType:'html',
											success:function(d){/*console.log(d);*/},
											error:function(e){/*console.log(e);*/}
										});
									}
									else{
									}
									$('.viewtempfun').css({'display':'none'});
									$('.setwin #temporder').trigger('click');
									//手機POS不做錢都錄
									/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
										var posdvrfile=tempres[1];
									}
									else{
									}*/
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});
							//手機POS不做錢都錄
							/*錢都錄借接*/
							/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
								//start tag
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									async:false,
									data:{'html':'start posdvr-sendmessage','file':'posdvr'},
									dataType:'html',
									success:function(d){
										//console.log(d);
									},
									error:function(e){
										//console.log(e);
									}
								});
								if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
									var res=api_sendmessage_posdvr(posdvrfile);
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										data:{'html':'success '+posdvrfile,'file':'posdvr'},
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
								//end tag
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									async:false,
									data:{'html':'end posdvr-sendmessage','file':'posdvr'},
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
							}*/
							$.ajax({
								url:'../demopos/lib/js/create.cmdtxt.php',
								method:'post',
								async: false,
								data:{'cmd':nowbizdate.substr(0,6)+'-change'},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});
						//}
					/*}
					else{
						$('.verpsw input[name="type"]').val('viewvoid');
						verpsw.dialog('open');
					}*/
				//}
			});
			$('.viewtempfun .reprint').click(function(){
				/*if($('.order#order .initsetting #voidsale').val()=='1'){
					$('.verpsw input[name="type"]').val('tempsale_reprint_all');
					verpsw.dialog('open');
				}
				else{*/
					$('.viewtempfun .reprint').prop('disabled',true);
					if($('.viewtempfun #type').val()=='sale'){
						var targetdiv='viewlist';
						var type='all';
					}
					else{
						var targetdiv='viewtemp';
						var type='tempall';
					}
					var index='';
					var qty='';
					var inumber='';
					var linenumber='';
					for(var i=0;i<$('.'+targetdiv+' #listcontent .label').length;i++){
						if(index.length==0){
							index=index+$('.'+targetdiv+' #listcontent .label:eq('+i+') div:eq(1)').html();
							qty=qty+$('.'+targetdiv+' #listcontent .label:eq('+i+') div:eq(4)').html();
							inumber=inumber+$('.'+targetdiv+' #listcontent .label:eq('+i+') input[name="inumber"]').val();
							linenumber=linenumber+$('.'+targetdiv+' #listcontent .label:eq('+i+') input[name="linenumber"]').val();
						}
						else{
							index=index+','+$('.'+targetdiv+' #listcontent .label:eq('+i+') div:eq(1)').html();
							qty=qty+','+$('.'+targetdiv+' #listcontent .label:eq('+i+') div:eq(4)').html();
							inumber=inumber+','+$('.'+targetdiv+' #listcontent .label:eq('+i+') input[name="inumber"]').val();
							linenumber=linenumber+','+$('.'+targetdiv+' #listcontent .label:eq('+i+') input[name="linenumber"]').val();
						}
					}
					console.log(index);
					$.ajax({
						url:'../demopos/lib/js/reprint.ajax.php',
						method:'post',
						data:{'company':$('.basic input[name="company"]').val(),'type':type,'listtype':$('.'+targetdiv+' #salecontent .listitems#checked div:eq(1) input[name="listtype"]').val(),'bizdate':$('.'+targetdiv+' #salecontent .listitems#checked div:eq(0)').html(),'no':$('.'+targetdiv+' #salecontent .listitems#checked div:eq(1) input[name="consecnumber"]').val(),'date':$('.'+targetdiv+' #salecontent .listitems#checked div:eq(6)').html().substr(0,8),'index':index,'qty':qty,'inumber':inumber,'machinetype':$('#content #setup input[name="machinetype"]').val(),'linenumber':linenumber,'username':$('#content #setup input[name="username"]').val()},
						dataType:'html',
						success:function(d){
							if(d.length>20){
								$.ajax({
									url:'../demopos/lib/js/print.php',
									method:'post',
									data:{'html':'orderpos '+targetdiv+'-forall reprint.ajax.php '+d},
									dataType:'html',
									success:function(d){/*console.log(d);*/},
									error:function(e){/*console.log(e);*/}
								});
							}
							else{
							}
							console.log(d);
							//2017/12/4var mywin=window.open('cashdrawer://reprint','','width=1px,height=1px');
							//2017/12/4mywin.document.title='cashdrawer';
						},
						error:function(e){
							console.log(e);
						}
					});
					setTimeout(function(){
						$('.viewtempfun .reprint').prop('disabled',false);
					},500);
				//}
			});
			$('.viewtempfun .exit').click(function(){
				$('.viewtempfun').css({'display':'none'});
			});
			$('.setwin #cashdrawer').click(function(){
				$.ajax({
					url:'../demopos/lib/js/create.cmdtxt.php',
					method:'post',
					async: false,
					data:{'cmd':$('#content input[name="machinetype"]').val()+'-cashdrawer_'+$('#content input[name="machinetype"]').val()},
					dataType:'html',
					success:function(d){
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
				$.ajax({
					url:'./lib/js/getchange.ajax.php',
					//method:'post',
					async:false,
					//data:{},
					dataType:'json',
					success:function(d){
						//console.log(d);
						$('.setchange input[name="changemoney"]').val(d['money']);
					},
					error:function(e){
						//console.log(e);
					}
				});
			});
			$('.setwin #setchange').click(function(){
				$.ajax({
					url:'./lib/js/getchange.ajax.php',
					//method:'post',
					async:false,
					//data:{},
					dataType:'json',
					success:function(d){
						//console.log(d);
						$('.setchange input[name="changemoney"]').val(d['money']);
					},
					error:function(e){
						//console.log(e);
					}
				});
				$('.setwin').css({'display':'none'});
				$('.setchange').css({'display':'block'});
				$('.modal').css({'display':'block'});
			});
			$('.setchange .check').click(function(){
				$.ajax({
					url:'../demopos/lib/js/setchange.php',
					method:'post',
					data:{'change':$('.setchange input[name="changemoney"]').val(),'usercode':$('#content #setup input[name="usercode"]').val(),'username':$('#content #setup input[name="username"]').val(),'machinetype':$('#content #setup input[name="machinetype"]').val()},
					dataType:'html',
					success:function(){
						$('.modal').trigger('click');
					}
				});
			});
		});
	</script>
	<style>
		body {
			width:100%;
			height:100%;
			margin:0;
		}
		body,
		select,
		select option,
		button,
		input {
			font-family:Microsoft JhengHei,MingLiU;
			font-size:30px;
		}
		input {
			width:100%;
			border:1px solid #808080;
			height:40px;
		}
		input[type="button"],input[type="reset"] {
			height:50px;
		}
		#content {
			margin-right:auto;
			margin-bottom:auto;
			margin-left:auto;
			display:table;
		}
		.labeltd {
			width:60px;
		}
		#entertable {
			width:100%;
			border:1px solid #808080;
		}
		td {
			padding:10px 0;
		}
		#content #setbutton,
		.viewtemp #setbutton,
		.viewlist #setbutton {
			width: 22px;
			height: max-content;
			padding: 14px 14px;
			text-align: center;
			z-index: 1;
			position: absolute;
			top: 5px;
			right: 5px;
			cursor: pointer;
		}
		#content #setbutton .funkey,
		.viewtemp #setbutton .funkey,
		.viewlist #setbutton .funkey {
			width: 22px;
			height: 3px;
			margin: 4px 0;
			background-color: rgb(26,26,26);
			border-radius:3px;
		}
		.viewtemp #salecontent .listitems,
		.viewlist #salecontent .listitems {
			margin:10px 0;
			padding:0 5px;
			font-size:20px;
			cursor: pointer;
		}
		.viewtemp #salecontent .listitems#checked,
		.viewlist #salecontent .listitems#checked {
			background-color:rgb(200,200,200,0.5);
		}
		.viewtemp #listcontent .label,
		.viewlist #listcontent .label {
			margin:10px 0;
			padding:0 5px;
			font-size:20px;
		}
		.viewtemp #listcontent input[type="checkbox"],
		.viewlist #listcontent input[type="checkbox"] {
			width:16px;
			height:16px;
			margin:0;
		}
	</style>
</head>
<body>
	<div class='basic' style='display:none;'>
		<?php $setup=parse_ini_file('../database/setup.ini',true); ?>
		<input type='hidden' name='company' value='<?php echo $setup['basic']['company']; ?>'>
		<input type='hidden' name='dep' value='<?php echo $setup['basic']['story']; ?>'>
		<input type='hidden' name='settime' value='<?php echo $initsetting['init']['settime']; ?>'>
	</div>
	<div id="content" style='width:300px;border:0px solid #808080;margin:50px auto 0 auto;padding:0 40px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		<form id='setup' method='post' action='./main.php' style='display:none;float:left;'>
			<input type='hidden' name='machine' value='<?php if(isset($_POST['machine']))echo $_POST['machine']; ?>'>
			<input type='hidden' name='submachine' value='<?php if(isset($_POST['submachine']))echo $_POST['submachine']; ?>'>
			<input type='hidden' name='machinetype' value='<?php if(isset($_POST['machinetype']))echo $_POST['machinetype']; ?>'>
			<input type='hidden' name='bizdate' value='<?php echo $time['time']['bizdate']; ?>'>
			<input type='hidden' name='usercode' value='<?php if(isset($_POST['usercode']))echo $_POST['usercode']; ?>'>
			<input type='hidden' name='username' value='<?php if(isset($_POST['username']))echo $_POST['username']; ?>'>
			<input type='hidden' name='listtype' value=''>
			<input type='hidden' name='v' value='<?php if(isset($_POST['v']))echo $_POST['v']; ?>'>
			<input type='hidden' name='p' value='<?php if(isset($_POST['p']))echo $_POST['p']; ?>'>
			<input type='hidden' name='u' value='<?php if(isset($_POST['u']))echo $_POST['u']; ?>'>
			<input type='hidden' name='r' value='<?php if(isset($_POST['r']))echo $_POST['r']; ?>'>
			<input type='hidden' name='tabnum' value=''>
		</form>
		<div class="needsclick" id='setbutton' style="<?php
		if(isset($_POST['submachine'])&&strlen($_POST['submachine'])>0){
			echo 'display:none;';
		}
		else{
		}
		?>">
			<div class='funkey'></div>
			<div class='funkey'></div>
			<div class='funkey'></div>
		</div>
		<div style='width: 220px;float:left;'>
			<table style='width:100%;height:100%;'>
				<caption><?php echo $time['time']['bizdate']; ?></caption>
				<tr style="<?php
				if(isset($_POST['submachine'])&&strlen($_POST['submachine'])>0){
					echo 'display:none;';
				}
				else{
				}
				?>">
				<td>
					<button id='open' name='開班' style='<?php
					if($time['time']['isopen']=='1'){
					}
					else{
						echo 'display:none;';
					}
					?>width:100%;height:100%;float:left;background-color:#ffffff;border:1px #898989 solid;border-radius:5px;'>開班</button>
					<button id='close' name='交班' style='<?php
					if($time['time']['isclose']=='1'){
					}
					else{
						echo 'display:none;';
					}
					?>width:100%;height:100%;float:left;background-color:#ffffff;border:1px #898989 solid;border-radius:5px;'>交班</button>
				</td>
			</tr>
				<tr id='listtype' style='<?php
				if($time['time']['isopen']=='1'){
					echo 'display:none;';
				}
				else{
				}
				?>'>
					<td><select class='listtype' style='width:100%;height:50px;border-radius:5px;border:1px solid #898989;margin:0;padding:1px 0;' <?php
					if($time['time']['isopen']=='1'){
						echo 'disabled';
					}
					else{
					}
					?>><?php
					$typelist=preg_split('/,/',$initsetting['init']['orderlocation']);
					$buttons=parse_ini_file('../demopos/syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
					for($i=0;$i<sizeof($typelist);$i++){
						echo '<option value="'.$typelist[$i].'" ';
						if(isset($initsetting['init']['orderpostype'])&&$initsetting['init']['orderpostype']==$typelist[$i]){
							echo 'selected';
						}
						else{
						}
						echo '>'.$buttons['name']['listtype'.$typelist[$i]].'</option>';
					}
					?></select></td>
				</tr>
				<?php
				if($initsetting['init']['controltable']=='1'){
				?>
				<tr id='tablab' style='<?php
				if($time['time']['isopen']=='1'||$initsetting['init']['orderpostype']!='1'){
					echo 'display:none;';
				}
				else{
				}
				?>'>
					<td class='labeltd' style='text-align:center;position:relative;'>桌號<button class='needsclick' id='reflash' style='width: 30px;height: 30px;padding: 0;border: 0;background-color: transparent;position: absolute;top: 17px;right: 0;'><img src='./img/reflash.png' style='width:30px;height:30px;position: absolute;top: 0;left: 0;border-radius: 100%;'></button></td>
				</tr>
				<tr id='tablab' style='<?php
				if($time['time']['isopen']=='1'||$initsetting['init']['orderpostype']!='1'){
					echo 'display:none;';
				}
				else{
				}
				?>'>
					<td><select id='tabnum' style='width:100%;height:50px;border-radius:5px;' <?php
					if($time['time']['isopen']=='1'){
						echo 'disabled';
					}
					else{
					}
					?>><?php
					include_once './lib/js/gettable.option.php';
					?></select></td>
				</tr>
				<?php
				}
				else if($initsetting['init']['tabnum']=='1'){
				?>
				<tr id='tablab' style='<?php
				if($time['time']['isopen']=='1'||$initsetting['init']['orderpostype']!='1'){
					echo 'display:none;';
				}
				else{
				}
				?>'>
					<td class='labeltd' style='text-align:center;'>桌號</td>
				</tr>
				<tr id='tablab' style='<?php
				if($time['time']['isopen']=='1'||$initsetting['init']['orderpostype']!='1'){
					echo 'display:none;';
				}
				else{
				}
				?>'>
					<td><input type='text' id='tabnum' style='width:calc(100% - 8px);height:100%;border-radius:5px;border:1px solid #898989;margin:0;padding:2px 5px;' <?php
					if($time['time']['isopen']=='1'){
						echo 'disabled';
					}
					else{
					}
					?>></td>
				</tr>
				<?php
				}
				else{
				}
				?>
				<tr id='orderbut' style='<?php
				if($time['time']['isopen']=='1'){
					echo 'display:none;';
				}
				else{
				}
				?>'>
					<td><button id='entertable' type='button' style='background-color:#ffffff;border-radius:5px;' value='點餐' autofocus <?php
					if($time['time']['isopen']=='1'){
						echo 'disabled';
					}
					else{
					}
					?>>點餐</button></td>
				</tr>
				<tr>
					<td style='padding-top:30px;'><button id='logout' type='button' style='width:100%;background-color:#ffffff;border:1px solid #808080;border-radius:5px;' value='登出'>登出</button></td>
				</tr>
			</table>
		</div>
	</div>
	<div class='setwin' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:15vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;'>
		<div style="margin:10px 0;text-align:center;font-weight:bold;font-size:20px;">功能列表</div>
		<div style='width:80%;margin:20px auto;'>
			<div style='width:100%;height:45px;line-height:45px;margin:0 auto;text-align:center;border:2px solid #898989;border-radius:5px;cursor: pointer;' id='cashdrawer'>開錢櫃</div>
		</div>
		<div style='width:80%;margin:20px auto;'>
			<div style='width:100%;height:45px;line-height:45px;margin:0 auto;text-align:center;border:2px solid #898989;border-radius:5px;cursor: pointer;' id='setchange'>找零金設定</div>
		</div>
		<!-- <div style='width:80%;margin:20px auto;'>
			<div style='width:100%;height:45px;line-height:45px;margin:0 auto;text-align:center;border:2px solid #898989;border-radius:5px;cursor: pointer;' id='weborder'>網路訂單</div>
		</div> -->
		<div style='width:80%;margin:20px auto;'>
			<div style='width:100%;height:45px;line-height:45px;margin:0 auto;text-align:center;border:2px solid #898989;border-radius:5px;cursor: pointer;' id='temporder'>暫結清單</div>
		</div>
		<div style='width:80%;margin:20px auto;'>
			<div style='width:100%;height:45px;line-height:45px;margin:0 auto;text-align:center;border:2px solid #898989;border-radius:5px;cursor: pointer;' id='vieworder'>瀏覽帳單</div>
		</div>
	</div>
	<div class='setchange' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:15vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;font-size:16px;'>
		<div style='width:calc(100% - 10px);height:50px;line-height:50px;font-size:25px;padding:0 5px;background-color:#e9e9e9;color:#000000;'>找零金設定</div>
		<div style='width:calc(100% - 10px);padding:0 5px;margin:5px 0;'>
			<input type='tel' name='changemoney' style='width:calc(100% - 8px);padding:5px 3px;margin:0;border:1px solid #898989;border-radius:5px;text-align:right;' placeholder="找零金(必填)" value=''>
		</div>
		<div style='width:calc(100% - 10px);padding:0 5px;'>
			<button class='check' value='設定' style='width:100%;height:100%;float:left;margin:5px 0;border:1px solid #898989;border-radius:5px;'>設定</button>
		</div>
	</div>
	<div class='viewlist' style='width:calc(100% - 12px);height:calc(100% - 12px);border:1px solid rgb(74,74,74,0.5);position:fixed;top:0;left:0;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;font-size:16px;'>
		<div style='width:calc(100% - 10px);height:50px;line-height:50px;font-size:25px;padding:0 5px;background-color:#e9e9e9;color:#000000;position:relative;'>
			瀏覽帳單
			<div class="needsclick" id='setbutton' style='padding:12px 14px;top:0;'>
				<div class='funkey'></div>
				<div class='funkey'></div>
				<div class='funkey'></div>
			</div>
		</div>
		<div id='salecontent' style='width:calc(100% - 14px);height:calc((100% - 108px) / 2 - 6px);padding:0 5px;margin:2px 0 4px 0;border:1px solid #898989;border-radius:5px;overflow:auto;box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;'></div>
		<div id='listcontent' style='width:calc(100% - 14px);height:calc((100% - 108px) / 2 - 6px);padding:0 5px;margin:2px 0 4px 0;border:1px solid #898989;border-radius:5px;overflow:auto;box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;'></div>
		<div style='width:calc(100% - 10px);height:50px;padding:0 5px;'>
			<button class='ttmoney' value='加點' style='width:calc((100% - 4px) / 2);height:100%;float:left;margin-right:2px;border:1px solid #898989;border-radius:5px;'>加點</button>
			<button class='exit' value='返回' style='width:calc((100% - 4px) / 2);height:100%;float:right;margin-left:2px;border:1px solid #898989;border-radius:5px;'>返回</button>
		</div>
	</div>
	<div class='viewtemp' style='width:calc(100% - 12px);height:calc(100% - 12px);border:1px solid rgb(74,74,74,0.5);position:fixed;top:0;left:0;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;font-size:16px;'>
		<div style='width:calc(100% - 10px);height:50px;line-height:50px;font-size:25px;padding:0 5px;background-color:#e9e9e9;color:#000000;position:relative;'>
			暫結清單
			<div class="needsclick" id='setbutton' style='padding:12px 14px;top:0;'>
				<div class='funkey'></div>
				<div class='funkey'></div>
				<div class='funkey'></div>
			</div>
		</div>
		<div id='salecontent' style='width:calc(100% - 14px);height:calc((100% - 108px) / 2 - 6px);padding:0 5px;margin:2px 0 4px 0;border:1px solid #898989;border-radius:5px;overflow:auto;box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;'></div>
		<div id='listcontent' style='width:calc(100% - 14px);height:calc((100% - 108px) / 2 - 6px);padding:0 5px;margin:2px 0 4px 0;border:1px solid #898989;border-radius:5px;overflow:auto;box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;'></div>
		<div style='width:calc(100% - 10px);height:50px;padding:0 5px;'>
			<button class='reorder' value='加點' style='width:calc((100% - 4px) / 2);height:100%;float:left;margin-right:2px;border:1px solid #898989;border-radius:5px;'>加點</button>
			<button class='exit' value='返回' style='width:calc((100% - 4px) / 2);height:100%;float:left;margin-left:2px;border:1px solid #898989;border-radius:5px;'>返回</button>
		</div>
	</div>
	<div class='viewtempfun' style='width:calc(100% - 12px);height:calc(100% - 12px);border:1px solid rgb(74,74,74,0.5);position:fixed;top:0;left:0;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;font-size:16px;'>
		<input type='hidden' id='type' value=''>
		<div style='width:calc(100% - 10px);height:50px;line-height:50px;font-size:25px;padding:0 5px;background-color:#e9e9e9;color:#000000;position:relative;'>
			功能區
		</div>
		<button class='voidlist' value='作廢' style='width:calc(100% - 4px);height:calc((100% - 50px) / 6 - 4px);float:left;margin:2px 0;background-color:rgb(200,200,200,0.5);border:2px solid #898989;border-radius:5px;'>作廢</button>
		<button class='voidtemp' value='作廢' style='width:calc(100% - 4px);height:calc((100% - 50px) / 6 - 4px);float:left;margin:2px 0;background-color:rgb(200,200,200,0.5);border:2px solid #898989;border-radius:5px;'>作廢</button>
		<button class='reprint' value='重印' style='width:calc(100% - 4px);height:calc((100% - 50px) / 6 - 4px);float:left;margin:2px 0;background-color:#ffffff;border:2px solid #898989;border-radius:5px;'>重印</button>
		<!-- <button class='reclient' value='明細單' style='width:calc(100% - 4px);height:calc((100% - 50px) / 6 - 4px);float:left;margin:2px 0;background-color:#ffffff;border:2px solid #898989;border-radius:5px;'>明細單</button> -->
		<!-- <button class='rekitchen' value='工作單' style='width:calc(100% - 4px);height:calc((100% - 50px) / 6 - 4px);float:left;margin:2px 0;background-color:#ffffff;border:2px solid #898989;border-radius:5px;'>工作單</button> -->
		<!-- <button class='retag' value='貼紙' style='width:calc(100% - 4px);height:calc((100% - 50px) / 6 - 4px);float:left;margin:2px 0;background-color:#ffffff;border:2px solid #898989;border-radius:5px;'>貼紙</button> -->
		<button class='exit' value='返回' style='width:calc(100% - 4px);height:calc((100% - 50px) / 6 - 4px);float:left;margin:2px 0;background-color:#ffffff;border:2px solid #898989;border-radius:5px;'>返回</button>
	</div>
	<div class='sysmeg' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:30vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;font-size:16px;'>
		<div style="margin:10px 0;text-align:center;">系統訊息</div>
		<div id="name1" style='width:100%;text-align:center;'>確認登出嗎?</div>
		<div style='width:220px;text-align:center;overflow:hidden;margin:25px auto 15px auto;'>
			<div class="yes" value="確認" style='width:calc(100px - 4px);height:35px;line-height:35px;margin:0 5px;float:left;text-align:center;font-size:16px;border:2px solid #898989;border-radius:5px;cursor: pointer;'>確認</div>
			<div class="no" value="取消" style='width:calc(100px - 4px);height:35px;line-height:35px;margin:0 5px;float:left;text-align:center;background-color:rgb(200,200,200,0.5);font-size:16px;border:2px solid #898989;border-radius:5px;cursor: pointer;'>取消</div>
		</div>
	</div>
	<div class='tabhint' style='width:calc(90vw - 12px);border:1px solid rgb(74,74,74,0.5);border-radius:10px;position:fixed;top:30vh;left:5vw;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;font-size:16px;'>
		<div style="margin:10px 0;text-align:center;">系統訊息</div>
		<div id="name1" style='width:100%;text-align:center;'>請填入桌號</div>
		<div style='width:220px;text-align:center;overflow:hidden;margin:25px auto 15px auto;'>
			<div class="check" value="確認" style='width:100px;height:35px;line-height:35px;margin:0 auto;text-align:center;border:2px solid #898989;font-size:16px;border-radius:5px;cursor: pointer;'>確認</div>
		</div>
	</div>
	<div class='modal' style="position:fixed;top:0;left:0;height:100vh;width:100vw;background-color:#4a4a4a;opacity:0.5;display:none;z-index:2019;">
	</div>
	<div class='paylist' style='width:calc(100% - 12px);height:calc(100% - 12px);border:1px solid rgb(74,74,74,0.5);position:fixed;top:0;left:0;z-index:2020;padding:5px;background-color:#ffffff;display:none;overflow:hidden;font-size:16px;'>
		<div style='width:calc(100% - 10px);height:50px;line-height:50px;font-size:25px;padding:0 5px;background-color:#e9e9e9;color:#000000;position:relative;'>
			付款方式列表
			<!-- <div class="needsclick" id='setbutton' style='padding:12px 14px;top:0;'>
				<div class='funkey'></div>
				<div class='funkey'></div>
				<div class='funkey'></div>
			</div> -->
		</div>
		<div id='paycontent' style='width:calc(100% - 14px);height:calc(100% - 108px);padding:0 5px;margin:2px 0 4px 0;border:1px solid #898989;border-radius:5px;overflow:auto;box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;'></div>
		<!-- <div id='listcontent' style='width:calc(100% - 14px);height:calc((100% - 108px) / 2 - 6px);padding:0 5px;margin:2px 0 4px 0;border:1px solid #898989;border-radius:5px;overflow:auto;box-shadow: rgba(0, 0, 0, 0.2) 2px 2px;'></div> -->
		<div style='width:calc(100% - 10px);height:50px;padding:0 5px;'>
			<!-- <button class='ttmoney' value='加點' style='width:calc((100% - 4px) / 2);height:100%;float:left;margin-right:2px;border:1px solid #898989;border-radius:5px;'>加點</button> -->
			<button class='exit' value='返回' style='width:calc((100% - 4px) / 2);height:100%;float:right;margin-left:2px;border:1px solid #898989;border-radius:5px;'>返回</button>
		</div>
	</div>
</body>
</html>
