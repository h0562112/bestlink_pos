<!doctype html>
<html lang="en">
<link rel="shortcut icon" href="../../favicon.png">
<?php
$machinedata=parse_ini_file('../database/machinedata.ini',true);
if(isset($machinedata['posdvr']['key'])&&$machinedata['posdvr']['key']!=''){
	setCookie('auth',$machinedata['posdvr']['key'],time()+86400,'/','quickcode.com.tw');
}
else{
}
?>
<head>
	<?php
	$initsetting=parse_ini_file('../database/initsetting.ini',true);
	?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- <meta name="mobile-web-app-capable" content="yes"> -->
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-title" content="點餐畫面">
	<meta name="format-detection" content="telephone=no">
	<?php
	if(isset($_GET['machine'])&&$_GET['machine']!=''){
	?>
	<link rel="manifest" href="../database/json/<?php echo $_GET['machine']?>fest.json">
	<?php
	}
	else if(isset($_GET['submachine'])&&$_GET['submachine']!=''){
	?>
	<link rel="manifest" href="../database/json/<?php echo $_GET['submachine']?>fest.json">
	<?php
	}
	else{
	?>
	<link rel="manifest" href="../database/json/manifest.json">
	<?php
	}
	?>
	<script src="../tool/jquery-1.12.4.js?<?php echo date('YmdHis'); ?>"></script>
	<script src="../tool/fastclick/lib/fastclick.js?<?php echo date('YmdHis'); ?>"></script>
	<script src="../tool/ui/1.12.1/jquery-ui.js?<?php echo date('YmdHis'); ?>"></script>
	<link rel="stylesheet" href="../tool/ui/1.12.1/themes/base/jquery-ui.css?<?php echo date('YmdHis'); ?>">
	<title>點餐畫面</title>
	<style>
		body,input {
			width:100vw;/*1080px*/
			height:100vh;/*1920px*/
			padding:0;
			margin:0;
			/*font-size:60px;*/
			color:#3C3B39;
			overflow:hidden;
			font-family: Arial,Microsoft JhengHei,sans-serif;
		}
		div {
			font-family: Arial,Microsoft JhengHei,sans-serif;
		}
		/*.ui-widget .button,*/
		.button {
			font-size:80px;
			padding:0;
			font-family: Arial,Microsoft JhengHei,sans-serif;
			font-weight:bold;
			color:#3e3a39;
			border:0;
			background-repeat:no-repeat;
			background-position:center;
			background-color:transparent;
		}
		/*.ui-widget {
			font-family: Arial,Microsoft JhengHei,sans-serif;
		}*/
		/*.ui-widget .ui-widget {
			font-size:30px;
		}*/
		.ui-dialog-titlebar {
			display:none;
		}
		/*.ui-dialog {
			padding:0;
		}*/
		/*.ui-dialog .ui-dialog-content {
			padding:0;
		}*/
		/*.ui-widget input {
			font-size:80px;
			font-family: Arial,Microsoft JhengHei,sans-serif;
			color:#3e3a39;
		}*/
		.button,
		button {
			width:calc(100% / 10 - 1.2px);
			height:calc(100% / 4 - 2px);
			float:left;
			margin:0.5px;
			background-color: transparent;
			border:1px solid #898989;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			-webkit-appearance: none;
			color:#898989;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
		#str {
			background-color: #ffffff;
		}
		#num{
			background-color: #f0f0f0;
		}
		#exit,#ac,#submit{
			background-color: #E2DFDF;
		}
	</style>
	<script>
		$(function() {
			FastClick.attach(document.body);
		});
		$(document).ready(function(){
			sysmeg=$('.sysmeg').dialog({
				autoOpen:false,
				width:400,
				height:200,
				/*title:'123',*/
				resizable:false,
				modal:true,
				draggable:false
			});
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
			$(document).on('click','.number input[name="id"]',function(event){
				$('.number #tag').val('id');
				$('.number input[name="id"]').css({'background-color':'transparent'});
				$('.number input[name="psw"]').css({'background-color':'#B7B7B7'});
			});
			$(document).on('click','.number input[name="psw"]',function(event){
				$('.number #tag').val('psw');
				$('.number input[name="id"]').css({'background-color':'#B7B7B7'});
				$('.number input[name="psw"]').css({'background-color':'transparent'});
			});
			$(document).on('click','.number .button',function(event){
				var index=$('.number .button').index(this);
				if($('.number input[name="'+$('.number #tag').val()+'"]').val().length<10){
					$('.number input[name="'+$('.number #tag').val()+'"]').val($('.number input[name="'+$('.number #tag').val()+'"]').val()+$('.number .button:eq('+index+')').val());
				}
				else{
				}
			});
			$(document).on('click','.number #ac',function(event){
				$('.number input[name="'+$('.number #tag').val()+'"]').val('');
			});
			$(document).on('click','.number #exit',function(event){
				<?php
				if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'){
				?>
					var mywin=window.open('exit://','','width=1px,height=1px');
				<?php
				}
				else{
				?>
					$.ajax({
						url:'./lib/js/create.cmdtxt.php',
						method:'post',
						async: false,
						data:{'cmd':'<?php if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo $_GET["submachine"];else if(isset($_GET["machine"])&&$_GET["machine"]!="")echo $_GET["machine"];else echo "m1"; ?>-exit_<?php if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo $_GET["submachine"];else if(isset($_GET["machine"])&&$_GET["machine"]!="")echo $_GET["machine"];else echo "m1"; ?>'},
						dataType:'html',
						success:function(d){
							//console.log(d);
						},
						error:function(e){
							console.log(e);
						}
					});
				<?php
				}
				?>
			});
			$(document).on('click','.number #submit',function(event){
				if($('#tag').val()=='id'){
					$('.number #tag').val('psw');
					$('.number input[name="id"]').css({'background-color':'#B7B7B7'});
					$('.number input[name="psw"]').css({'background-color':'transparent'});
				}
				else{
					if($('.number input[name="id"]').val().length>0){
						$.ajax({
							url:'./loginmethod.php',
							method:'post',
							data:{'id':$('.number input[name="id"]').val(),'psw':$('.number input[name="psw"]').val(),'machine':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"])&&$_GET["machine"]!="")echo $_GET["machine"];else echo "m1"; ?>'},
							dataType:'json',
							success:function(d){
								if(d.length>0){
									$('.sysmeg input[name="id"]').val(d['0']['id']);
									$('.sysmeg input[name="name"]').val(d['0']['name']);
									if(typeof d['0']['voidpw']==='undefined'){
										$('.sysmeg input[name="voidpw"]').val('null');
									}
									else{
										$('.sysmeg input[name="voidpw"]').val(d['0']['voidpw']);
									}
									if(typeof d['0']['paperpw']==='undefined'){
										$('.sysmeg input[name="paperpw"]').val('null');
									}
									else{
										$('.sysmeg input[name="paperpw"]').val(d['0']['paperpw']);
									}
									if(typeof d['0']['punchpw']==='undefined'){
										$('.sysmeg input[name="punchpw"]').val('null');
									}
									else{
										$('.sysmeg input[name="punchpw"]').val(d['0']['punchpw']);
									}
									if(typeof d['0']['reprintpw']==='undefined'){
										$('.sysmeg input[name="reprintpw"]').val('null');
									}
									else{
										$('.sysmeg input[name="reprintpw"]').val(d['0']['reprintpw']);
									}
									$.ajax({
										url:'./check.controltable.php',
										method:'post',
										data:{'machinetype':'<?php if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo $_GET["submachine"];else if(isset($_GET["machine"])&&$_GET["machine"]!="")echo $_GET["machine"];else echo "m1"; ?>'},
										dataType:'json',
										success:function(c){
											if(c[0]=='control'){
												if(c[1]=='1'){
													var systemhint='';
													$.ajax({
														url:'./lib/js/getloginname.ajax.php',
														method:'post',
														async:false,
														data:{'name':'systemhint1'},
														dataType:'json',
														success:function(d){
															//console.log(d);
															systemhint += d[0];
														},
														error:function(e){
															//console.log(e);
															systemhint += '目前POS營業日為：';
														}
													});
													systemhint += c[2];
													$.ajax({
														url:'./lib/js/getloginname.ajax.php',
														method:'post',
														async:false,
														data:{'name':'systemhint2'},
														dataType:'json',
														success:function(d){
															//console.log(d);
															systemhint += '<br>'+d[0];
														},
														error:function(e){
															//console.log(e);
															systemhint += '<br>請問是否需要自動交班？';
														}
													});
													$('.sysmeg #text').html(systemhint);
													$('.sysmeg button').prop('disabled',false);
													$('.sysmeg .check').css({'display':'block'});
													$('.sysmeg .cancel').css({'display':'block'});
													$('.sysmeg .return').css({'display':'none'});
													sysmeg.dialog('open');
												}
												else{
													//console.log(c);
													var hrefstring='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode='+d['0']['id']+'&username='+d['0']['name'];
													if(typeof d[0]['voidpw']==="undefined"){
													}
													else if(d[0]['voidpw']==null){
														hrefstring+='&v';
													}
													else{
														hrefstring+='&v='+d[0]['voidpw'];
													}
													if(typeof d[0]['paperpw']==="undefined"){
													}
													else if(d[0]['paperpw']==null){
														hrefstring+='&p';
													}
													else{
														hrefstring+='&p='+d[0]['paperpw'];
													}
													if(typeof d[0]['punchpw']==="undefined"){
													}
													else if(d[0]['punchpw']==null){
														hrefstring+='&u';
													}
													else{
														hrefstring+='&u='+d[0]['punchpw'];
													}
													if(typeof d[0]['reprintpw']==="undefined"){
													}
													else if(d[0]['reprintpw']==null){
														hrefstring+='&r';
													}
													else{
														hrefstring+='&r='+d[0]['reprintpw'];
													}
													location.href=hrefstring;
												}
											}
											else{
												if(c[1]=='1'){
													var systemhint='';
													$.ajax({
														url:'./lib/js/getloginname.ajax.php',
														method:'post',
														async:false,
														data:{'name':'systemhint1'},
														dataType:'json',
														success:function(d){
															//console.log(d);
															systemhint += d[0];
														},
														error:function(e){
															//console.log(e);
															systemhint += '目前POS營業日為：';
														}
													});
													systemhint += c[2];
													$.ajax({
														url:'./lib/js/getloginname.ajax.php',
														method:'post',
														async:false,
														data:{'name':'systemhint2'},
														dataType:'json',
														success:function(d){
															//console.log(d);
															systemhint += '<br>'+d[0];
														},
														error:function(e){
															//console.log(e);
															systemhint += '<br>請問是否需要自動交班？';
														}
													});
													$('.sysmeg #text').html(systemhint);
													$('.sysmeg button').prop('disabled',false);
													$('.sysmeg .check').css({'display':'block'});
													$('.sysmeg .cancel').css({'display':'block'});
													$('.sysmeg .return').css({'display':'none'});
													sysmeg.dialog('open');
												}
												else{
													//console.log(c);
													var hrefstring='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode='+d['0']['id']+'&username='+d['0']['name'];
													if(typeof d[0]['voidpw']==="undefined"){
													}
													else if(d[0]['voidpw']==null){
														hrefstring+='&v';
													}
													else{
														hrefstring+='&v='+d[0]['voidpw'];
													}
													if(typeof d[0]['paperpw']==="undefined"){
													}
													else if(d[0]['paperpw']==null){
														hrefstring+='&p';
													}
													else{
														hrefstring+='&p='+d[0]['paperpw'];
													}
													if(typeof d[0]['punchpw']==="undefined"){
													}
													else if(d[0]['punchpw']==null){
														hrefstring+='&u';
													}
													else{
														hrefstring+='&u='+d[0]['punchpw'];
													}
													if(typeof d[0]['reprintpw']==="undefined"){
													}
													else if(d[0]['reprintpw']==null){
														hrefstring+='&r';
													}
													else{
														hrefstring+='&r='+d[0]['reprintpw'];
													}
													location.href=hrefstring;
												}
											}
										},
										error:function(e){
											console.log(e);
										}
									});
								}
								else{
									$('.sysmeg #text').html('enter id or psw is error.');
									//alert('enter id or psw is error.');
									$('.sysmeg button').prop('disabled',false);
									$('.sysmeg .check').css({'display':'none'});
									$('.sysmeg .cancel').css({'display':'none'});
									$('.sysmeg .return').css({'display':'block'});
									sysmeg.dialog('open');
								}
							},
							error:function(e){
								console.log(e);
							}
						});
					}
					else{
						alert('ID is not empty.');
					}
				}
			});
			$('.sysmeg .check').click(function(){
				$.ajax({
					url:'./lib/js/close.ajax.php',
					method:'post',
					async:false,
					data:{'usercode':$('.sysmeg input[name="id"]').val(),'username':$('.sysmeg input[name="name"]').val(),'machinetype':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
					dataType:'html',
					success:function(dd){
						if(dd.length>20){
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'control open.ajax.php '+dd},
								dataType:'html',
								success:function(d){/*console.log(d);*/},
								error:function(e){/*console.log(e);*/}
							});
						}
						else{
						}
						if(dd=='error'){
							console.log(dd);
						}
						else{
							$.ajax({
								url:'./lib/js/change.class.php',
								method:'post',
								async:false,
								data:{'type':'isopen','machinetype':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
								dataType:'html',
								success:function(v){
									$('.funbox #close').prop('disabled',false);
									$('.funbox #AE').prop('disabled',false);
									$('.funbox #return').prop('disabled',false);
									if(v.length>20){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'control change.class.php '+v},
											dataType:'html',
											success:function(d){/*console.log(d);*/},
											error:function(e){/*console.log(e);*/}
										});
									}
									else{
									}
									$.ajax({
										url:'./lib/js/shift.paper.php',
										method:'post',
										async:false,
										data:{'zcounter':v,'machinename':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
										dataType:'html',
										success:function(d){
											if(d.length>20){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'control shift.paper.php '+d},
													dataType:'html',
													success:function(d){/*console.log(d);*/},
													error:function(e){/*console.log(e);*/}
												});
											}
											else{
											}
											$.ajax({
												url:'./lib/js/create.cmdtxt.php',
												method:'post',
												async: false,
												data:{'cmd':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>-upload_<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else echo "m1"; ?>'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
											//console.log(d);
											//var mywin=window.open('cashdrawer://upload','','width=1px,height=1px');
											//mywin.document.title='cashdrawer';
										},
										error:function(e){
											console.log(e);
										}
									});
									//location.href='./control.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode='+d['0']['id']+'&username='+d['0']['name'];
									var hrefstring='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode='+$('.sysmeg input[name="id"]').val()+'&username='+$('.sysmeg input[name="name"]').val();
									if($('.sysmeg input[name="voidpw"]').val()=="null"){
									}
									else{
										hrefstring+='&v='+$('.sysmeg input[name="voidpw"]').val();
									}
									if($('.sysmeg input[name="paperpw"]').val()=="null"){
									}
									else{
										hrefstring+='&p='+$('.sysmeg input[name="paperpw"]').val();
									}
									if($('.sysmeg input[name="punchpw"]').val()=="null"){
									}
									else{
										hrefstring+='&u='+$('.sysmeg input[name="punchpw"]').val();
									}
									if($('.sysmeg input[name="reprintpw"]').val()=="null"){
									}
									else{
										hrefstring+='&r='+$('.sysmeg input[name="reprintpw"]').val();
									}
									location.href=hrefstring;
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
			});
			$('.sysmeg .cancel').click(function(){
				$('.sysmeg button').prop('disabled',true);
				var hrefstring='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode='+$('.sysmeg input[name="id"]').val()+'&username='+$('.sysmeg input[name="name"]').val();
				if($('.sysmeg input[name="voidpw"]').val()=="null"){
				}
				else{
					hrefstring+='&v='+$('.sysmeg input[name="voidpw"]').val();
				}
				if($('.sysmeg input[name="paperpw"]').val()=="null"){
				}
				else{
					hrefstring+='&p='+$('.sysmeg input[name="paperpw"]').val();
				}
				if($('.sysmeg input[name="punchpw"]').val()=="null"){
				}
				else{
					hrefstring+='&u='+$('.sysmeg input[name="punchpw"]').val();
				}
				if($('.sysmeg input[name="reprintpw"]').val()=="null"){
				}
				else{
					hrefstring+='&r='+$('.sysmeg input[name="reprintpw"]').val();
				}
				location.href=hrefstring;
			});
			$('.sysmeg .return').click(function(){
				$('.sysmeg button').prop('disabled',true);
				sysmeg.dialog('close');
				$('.sysmeg #text').html('');
			});
		});
	</script>
</head>
<?php

if(file_exists('./syspram/login-'.$initsetting['init']['firlan'].'.ini')){
	$login1=parse_ini_file('./syspram/login-'.$initsetting['init']['firlan'].'.ini',true);
}
else{
	$login1='-1';
}
if(file_exists('./syspram/login-'.$initsetting['init']['seclan'].'.ini')){
	$login2=parse_ini_file('./syspram/login-'.$initsetting['init']['seclan'].'.ini',true);
}
else{
	$login2='-1';
}
?>
<body>
	<div class='screenfull' style='display:none;'></div>
	<div class='number' style='width:100%;height:100%;'>
		<div style='width:100%;height:100%;padding:20px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;'>
			<input type='hidden' id='tag' value='id'>
			<div style='width:100%;height:129px;float:left;'>
				<input type='text' name='id' style='width:calc(50% - 2px);height:129px;margin:0 0.5px;border:1px solid #898989;border-radius:5px;float:left;text-align:left;font-size:60px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' readonly placeholder="<?php if($login1!='-1')echo $login1['name']['text1']?>">
				<input type='password' name='psw' style='width:calc(50% - 2px);height:129px;margin:0 1px;background-color:#B7B7B7;border:1px solid #898989;border-radius:5px;float:left;text-align:left;font-size:60px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;' readonly placeholder="<?php if($login1!='-1')echo $login1['name']['text2']?>">
				
			</div>
			<div style='width:100%;height:calc(100% - 129px - 2px);float:left;margin-top:0.5px;'>
				<input type='button' class='button' id='str' value='A'>
				<input type='button' class='button' id='str' value='B'>
				<input type='button' class='button' id='str' value='C'>
				<input type='button' class='button' id='str' value='D'>
				<input type='button' class='button' id='str' value='E'>
				<input type='button' class='button' id='str' value='F'>
				<input type='button' class='button' id='str' value='G'>
				<input type='button' class='button' id='num' value='7'>
				<input type='button' class='button' id='num' value='8'>
				<input type='button' class='button' id='num' value='9'>
				<input type='button' class='button' id='str' value='H'>
				<input type='button' class='button' id='str' value='I'>
				<input type='button' class='button' id='str' value='J'>
				<input type='button' class='button' id='str' value='K'>
				<input type='button' class='button' id='str' value='L'>
				<input type='button' class='button' id='str' value='M'>
				<input type='button' class='button' id='str' value='N'>
				<input type='button' class='button' id='num' value='4'>
				<input type='button' class='button' id='num' value='5'>
				<input type='button' class='button' id='num' value='6'>
				<input type='button' class='button' id='str' value='O'>
				<input type='button' class='button' id='str' value='P'>
				<input type='button' class='button' id='str' value='Q'>
				<input type='button' class='button' id='str' value='R'>
				<input type='button' class='button' id='str' value='S'>
				<input type='button' class='button' id='str' value='T'>
				<input type='button' class='button' id='str' value='U'>
				<input type='button' class='button' id='num' value='1'>
				<input type='button' class='button' id='num' value='2'>
				<input type='button' class='button' id='num' value='3'>
				<input type='button' class='button' id='str' value='V'>
				<input type='button' class='button' id='str' value='W'>
				<input type='button' class='button' id='str' value='X'>
				<input type='button' class='button' id='str' value='Y'>
				<input type='button' class='button' id='str' value='Z'>
				<button id='exit'><?php if($login1!='-1')echo "<div style='font-weight:bold;font-size:40px;color:#898989;'>".$login1['name']['button1']."</div>";if($login2!='-1')echo "<div style='font-weight:bold;font-size:22.5px;color:#CDCECE;'>".$login2['name']['button1']."</div>"; ?></button>
				<button id='ac'><?php if($login1!='-1')echo "<div style='font-weight:bold;font-size:40px;color:#898989;'>".$login1['name']['button2']."</div>";if($login2!='-1')echo "<div style='font-weight:bold;font-size:22.5px;color:#CDCECE;'>".$login2['name']['button2']."</div>"; ?></button>
				<input type='button' class='button' id='num' value='0'>
				<button id='submit' style='width:calc(100% / 5 - 2px);'><?php if($login1!='-1')echo "<div style='font-weight:bold;font-size:50px;color:#898989;'>".$login1['name']['button3']."</div>";if($login2!='-1')echo "<div style='font-weight:bold;font-size:25px;color:#CDCECE;'>".$login2['name']['button3']."</div>"; ?></button>
			</div>
		</div>
	</div>
	<div class='sysmeg' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;'>
		<input type='hidden' name='id' value=''>
		<input type='hidden' name='name' value=''>
		<input type='hidden' name='voidpw' value=''>
		<input type='hidden' name='paperpw' value=''>
		<input type='hidden' name='punchpw' value=''>
		<input type='hidden' name='reprintpw' value=''>
		<div id='text'></div>
		<div><button class='check' style='width:30%;height:90px;margin:15px calc(40% / 6) 5px calc(40% / 3);'><?php if(isset($login1['name']['button3']))echo $login1['name']['button3'];else echo '確認'; ?></button><button class='cancel' style='width:30%;height:90px;margin:15px calc(40% / 3) 5px calc(40% / 6);'><?php if(isset($login1['name']['cancel']))echo $login1['name']['cancel'];else echo '取消'; ?></button><button class='return' style='width:30%;height:90px;margin:15px calc(70% / 2) 5px calc(70% / 2);'><?php if(isset($login1['name']['cancel']))echo $login1['name']['cancel'];else echo '取消'; ?></button></div>
	</div>
</body>
</html>
