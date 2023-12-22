<!doctype html>
<html lang="en">
<?php
$machinedata=parse_ini_file('../database/machinedata.ini',true);
if(isset($machinedata['posdvr']['key'])&&$machinedata['posdvr']['key']!=''){
	setCookie('auth',$machinedata['posdvr']['key'],time()+86400,'/','quickcode.com.tw');
}
else{
}
?>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- <meta name="mobile-web-app-capable" content="yes"> -->
	<meta name="apple-mobile-web-app-capable" content="yes">
	<?php
	if(isset($_POST['machine'])&&$_POST['machine']!=''){
	?>
	<link rel="manifest" href="../database/json/<?php echo $_POST['machine']?>orderpos.json?<?php echo date('YmdHis'); ?>">
	<?php
	}
	else if(isset($_POST['submachine'])&&$_POST['submachine']!=''){
	?>
	<link rel="manifest" href="../database/json/<?php echo $_POST['submachine']?>orderpos.json?<?php echo date('YmdHis'); ?>">
	<?php
	}
	else{
	?>
	<link rel="manifest" href="../database/json/mainorderpos.json?<?php echo date('YmdHis'); ?>">
	<?php
	}
	if(file_exists('../database/json/logo.png')){
	?>
	<link rel="apple-touch-icon" sizes="57x57" href="../database/json/logo.png" />
	<?php
	}
	else{
	}
	?>
	<script src="../tool/jquery-1.12.4.js"></script>
	<script src="../tool/fastclick/lib/fastclick.js"></script>
	<title>點餐畫面</title>
	<script>
		$(function() {
			FastClick.attach(document.body);
		});
		$(document).ready(function(){
			$('#content #loginbutton').click(function(){
				if($('#content #ID').val()==""||$('#content #psw').val()==""){
					alert("帳號與密碼 不得為空");
				}
				else {
					if($('#content #ID').val().length>0){
						$.ajax({
							url:'../demopos/loginmethod.php',
							method:'post',
							data:{'id':$('#content #ID').val(),'psw':$('#content #psw').val()},
							dataType:'json',
							success:function(d){
								if(d.length>0){
									$.ajax({
										url:'../demopos/check.controltable.php',
										method:'post',
										data:{'machinetype':'<?php if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo $_GET["submachine"];else if(isset($_GET["machine"])&&$_GET["machine"]!="")echo $_GET["machine"];else echo "m1"; ?>'},
										dataType:'json',
										success:function(c){
											if(c[0]=='control'){//使用桌控
												/*if(c[1]=='1'){
													r=confirm('目前POS營業日為：'+c[2]+'\n將要自動交班？');
													if(r==true){
														$.ajax({
															url:'../demopos/lib/js/close.ajax.php',
															method:'post',
															async:false,
															data:{'usercode':d['0']['id'],'username':d['0']['name'],'machinetype':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
															dataType:'html',
															success:function(dd){
																if(dd.length>20){
																	$.ajax({
																		url:'../demopos/lib/js/print.php',
																		method:'post',
																		data:{'html':'orderpos control open.ajax.php '+dd},
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
																if(dd=='error'){
																	console.log(dd);
																}
																else{
																	$.ajax({
																		url:'../demopos/lib/js/change.class.php',
																		method:'post',
																		async:false,
																		data:{'type':'isopen','machinetype':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
																		dataType:'html',
																		success:function(v){
																			if(v.length>20){
																				$.ajax({
																					url:'../demopos/lib/js/print.php',
																					method:'post',
																					data:{'html':'orderpos control change.class.php '+v},
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
																				url:'../demopos/lib/js/shift.paper.php',
																				method:'post',
																				async:false,
																				data:{'zcounter':v,'machinename':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
																				dataType:'html',
																				success:function(d){
																					if(d.length>20){
																						$.ajax({
																							url:'../demopos/lib/js/print.php',
																							method:'post',
																							data:{'html':'orderpos control shift.paper.php '+d},
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
																						url:'../demopos/lib/js/create.cmdtxt.php',
																						method:'post',
																						async: false,
																						data:{'cmd':'upload'},
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
																			$('#setup input[name="machine"]').val('<?php if(isset($_GET["machine"])&&$_GET["machine"]!='')echo $_GET["machine"];else if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo "";else echo "m1"; ?>');
																			$('#setup input[name="submachine"]').val('<?php if(isset($_GET["submachine"])&&$_GET["submachine"]!='')echo $_GET["submachine"];else echo ""; ?>');
																			$('#setup input[name="machinetype"]').val('<?php if(isset($_GET["machine"])&&$_GET["machine"]!='')echo $_GET["machine"];else if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo $_GET["submachine"];else echo "m1"; ?>');
																			if(d[0]['id']==null){
																				$('#setup input[name="usercode"]').val('');
																			}
																			else{
																				$('#setup input[name="usercode"]').val(d[0]['id']);
																			}
																			if(d[0]['name']==null){
																				$('#setup input[name="username"]').val('');
																			}
																			else{
																				$('#setup input[name="username"]').val(d[0]['name']);
																			}
																			if(d[0]['voidpw']==null){
																				$('#setup input[name="v"]').val('');
																			}
																			else{
																				$('#setup input[name="v"]').val(d[0]['voidpw']);
																			}
																			if(d[0]['paperpw']==null){
																				$('#setup input[name="p"]').val('');
																			}
																			else{
																				$('#setup input[name="p"]').val(d[0]['paperpw']);
																			}
																			if(d[0]['punchpw']==null){
																				$('#setup input[name="u"]').val('');
																			}
																			else{
																				$('#setup input[name="u"]').val(d[0]['punchpw']);
																			}
																			if(d[0]['reprintpw']==null){
																				$('#setup input[name="r"]').val('');
																			}
																			else{
																				$('#setup input[name="r"]').val(d[0]['reprintpw']);
																			}
																			$('#setup').submit();
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
													else{
														//手機POS一定得開班進入
													}
												}
												else{*/
													//location.href='./control.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode='+d['0']['id']+'&username='+d['0']['name'];
													if(d[0]['id']==null){
														$('#setup input[name="usercode"]').val('');
													}
													else{
														$('#setup input[name="usercode"]').val(d[0]['id']);
													}
													if(d[0]['name']==null){
														$('#setup input[name="username"]').val('');
													}
													else{
														$('#setup input[name="username"]').val(d[0]['name']);
													}
													if(d[0]['voidpw']==null){
														$('#setup input[name="v"]').val('');
													}
													else{
														$('#setup input[name="v"]').val(d[0]['voidpw']);
													}
													if(d[0]['paperpw']==null){
														$('#setup input[name="p"]').val('');
													}
													else{
														$('#setup input[name="p"]').val(d[0]['paperpw']);
													}
													if(d[0]['punchpw']==null){
														$('#setup input[name="u"]').val('');
													}
													else{
														$('#setup input[name="u"]').val(d[0]['punchpw']);
													}
													if(d[0]['reprintpw']==null){
														$('#setup input[name="r"]').val('');
													}
													else{
														$('#setup input[name="r"]').val(d[0]['reprintpw']);
													}
													$('#setup').submit();
												//}
											}
											else{//未使用桌控
												/*if(c[1]=='1'){
													r=confirm('目前POS營業日為：'+c[2]+'\n將要自動交班？');
													if(r==true){
														$.ajax({
															url:'../demopos/lib/js/close.ajax.php',
															method:'post',
															async:false,
															data:{'usercode':d['0']['id'],'username':d['0']['name'],'machinetype':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
															dataType:'html',
															success:function(dd){
																if(dd.length>20){
																	$.ajax({
																		url:'../demopos/lib/js/print.php',
																		method:'post',
																		data:{'html':'orderpos control open.ajax.php '+dd},
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
																if(dd=='error'){
																	console.log(dd);
																}
																else{
																	$.ajax({
																		url:'../demopos/lib/js/change.class.php',
																		method:'post',
																		async:false,
																		data:{'type':'isopen','machinetype':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
																		dataType:'html',
																		success:function(v){
																			if(v.length>20){
																				$.ajax({
																					url:'../demopos/lib/js/print.php',
																					method:'post',
																					data:{'html':'orderpos control change.class.php '+v},
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
																				url:'../demopos/lib/js/shift.paper.php',
																				method:'post',
																				async:false,
																				data:{'zcounter':v,'machinename':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else; ?>'},
																				dataType:'html',
																				success:function(dd){
																					if(d.length>20){
																						$.ajax({
																							url:'../demopos/lib/js/print.php',
																							method:'post',
																							data:{'html':'orderpos control shift.paper.php '+dd},
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
																						url:'../demopos/lib/js/create.cmdtxt.php',
																						method:'post',
																						async: false,
																						data:{'cmd':'upload'},
																						dataType:'html',
																						success:function(d){
																							//console.log(d);
																						},
																						error:function(e){
																							//console.log(e);
																						}
																					});
																					console.log(dd);
																					//var mywin=window.open('cashdrawer://upload','','width=1px,height=1px');
																					//mywin.document.title='cashdrawer';
																				},
																				error:function(e){
																					console.log(e);
																				}
																			});
																			console.log(v);
																			$('#setup input[name="machine"]').val('<?php if(isset($_GET["machine"])&&$_GET["machine"]!='')echo $_GET["machine"];else if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo "";else echo "m1"; ?>');
																			$('#setup input[name="submachine"]').val('<?php if(isset($_GET["submachine"])&&$_GET["submachine"]!='')echo $_GET["submachine"];else echo ""; ?>');
																			$('#setup input[name="machinetype"]').val('<?php if(isset($_GET["machine"])&&$_GET["machine"]!='')echo $_GET["machine"];else if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo $_GET["submachine"];else echo "m1"; ?>');
																			if(d[0]['id']==null){
																				$('#setup input[name="usercode"]').val('');
																			}
																			else{
																				$('#setup input[name="usercode"]').val(d[0]['id']);
																			}
																			if(d[0]['name']==null){
																				$('#setup input[name="username"]').val('');
																			}
																			else{
																				$('#setup input[name="username"]').val(d[0]['name']);
																			}
																			if(d[0]['voidpw']==null){
																				$('#setup input[name="v"]').val('');
																			}
																			else{
																				$('#setup input[name="v"]').val(d[0]['voidpw']);
																			}
																			if(d[0]['paperpw']==null){
																				$('#setup input[name="p"]').val('');
																			}
																			else{
																				$('#setup input[name="p"]').val(d[0]['paperpw']);
																			}
																			if(d[0]['punchpw']==null){
																				$('#setup input[name="u"]').val('');
																			}
																			else{
																				$('#setup input[name="u"]').val(d[0]['punchpw']);
																			}
																			if(d[0]['reprintpw']==null){
																				$('#setup input[name="r"]').val('');
																			}
																			else{
																				$('#setup input[name="r"]').val(d[0]['reprintpw']);
																			}
																			$('#setup').submit();
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
													else{
														//手機POS一定得開班進入
													}
												}
												else{*/
													//console.log(c);
													if(d[0]['id']==null){
														$('#setup input[name="usercode"]').val('');
													}
													else{
														$('#setup input[name="usercode"]').val(d[0]['id']);
													}
													if(d[0]['name']==null){
														$('#setup input[name="username"]').val('');
													}
													else{
														$('#setup input[name="username"]').val(d[0]['name']);
													}
													if(d[0]['voidpw']==null){
														$('#setup input[name="v"]').val('');
													}
													else{
														$('#setup input[name="v"]').val(d[0]['voidpw']);
													}
													if(d[0]['paperpw']==null){
														$('#setup input[name="p"]').val('');
													}
													else{
														$('#setup input[name="p"]').val(d[0]['paperpw']);
													}
													if(d[0]['punchpw']==null){
														$('#setup input[name="u"]').val('');
													}
													else{
														$('#setup input[name="u"]').val(d[0]['punchpw']);
													}
													if(d[0]['reprintpw']==null){
														$('#setup input[name="r"]').val('');
													}
													else{
														$('#setup input[name="r"]').val(d[0]['reprintpw']);
													}
													$('#setup').submit();
												//}
											}
										},
										error:function(e){
											console.log(e);
										}
									});
								}
								else{
									alert('enter id or psw is error.');
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
			$('#content #reset').click(function(){
				$('#content #ID').val('');
				$('#content #psw').val('');
			});
		});
	</script>
	<style>
		body {
			font-family:Microsoft JhengHei,MingLiU;
			font-size:20px;
		}
		input,
		button {
			font-family:Microsoft JhengHei,MingLiU;
			border:1px solid #808080;
			height:45px;
			border-radius:5px;
			padding:2px 5px;
			font-size:16px;
		}
		#content {
			margin-right:auto;
			margin-bottom:auto;
			margin-left:auto;
			display:table;
		}
		td {
			padding:10px 0;
		}
		.labeltd {
			width:60px;
		}
		#ID,
		#psw {
		   width:calc(100% - 12px);
		}

		#loginbutton {
			width:100%;
			border:1px solid #808080;
			background:#808080;
			color:#ffffff;
		}
	</style>
</head>
<body>
	<div id="content" style='border:1px solid #808080;width:300px;height:300px;padding:0;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border-radius:5px;'>
		<form id='setup' method="post" action='./seltab.php' style='display:none;'>
			<input type='hidden' name='machine' value='<?php if(isset($_GET["machine"])&&$_GET["machine"]!='')echo $_GET["machine"];else if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo "";else echo "m1"; ?>'>
			<input type='hidden' name='submachine' value='<?php if(isset($_GET["submachine"])&&$_GET["submachine"]!='')echo $_GET["submachine"];else echo ""; ?>'>
			<input type='hidden' name='machinetype' value='<?php if(isset($_GET["machine"])&&$_GET["machine"]!='')echo $_GET["machine"];else if(isset($_GET["submachine"])&&$_GET["submachine"]!="")echo $_GET["submachine"];else echo "m1"; ?>'>
			<input type='hidden' name='bizdate' value='<?php echo date('Ymd'); ?>'>
			<input type='hidden' name='usercode' value=''>
			<input type='hidden' name='username' value=''>
			<input type='hidden' name='v' value=''>
			<input type='hidden' name='p' value=''>
			<input type='hidden' name='u' value=''>
			<input type='hidden' name='r' value=''>
		</form>
		<div style='width: 220px;height: max-content;margin:0 auto;position: relative;padding:0 0 62px 0;'>
			<table style='width:100%;border-collapse:collapse;'>
				<caption><h1>登入</h1></caption>
				<tr>
					<td><input type='text' class='needsclick' id='ID' name='ID' title="請輸入帳號" placeholder="請輸入帳號" autofocus></td>
				</tr>
				<tr>
					<td><input type='password' class='needsclick' id='psw' name='psw' title="請輸入密碼" placeholder="請輸入密碼"></td>
				</tr>
				<tr>
					<td><button class='needsclick' id='loginbutton' value='登入'>登入</button></td>
				</tr>
				<tr>
					<td style='padding-top:25px;'><button id='reset' style='width:100%;border:1px solid #808080;background:#ffffff;color:#191919;' value='取消'>取消</button></td>
				</tr>
			</table>
			<img src='./img/tableplus.png' style='width:218px;height:62px;margin:0 auto;position: absolute;bottom: 3px;'>
		</div>
	</div>
</body>
</html>
