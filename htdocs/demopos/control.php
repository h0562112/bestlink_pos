<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script src="../tool/jquery-1.12.4.js"></script>
	<script src="../tool/ui/1.12.1/jquery-ui.js"></script>
	<script src="../tool/ui/1.12.1/datepicker-zh-TW.js"></script>
	<link rel="stylesheet" href="../tool/ui/1.12.1/themes/base/jquery-ui.css">
	<title>點餐畫面</title>
	<?php
	include_once '../tool/dbTool.inc.php';
	$initsetting=parse_ini_file('../database/initsetting.ini',true);
	//date_default_timezone_set('Asia/Taipei');
	date_default_timezone_set($initsetting['init']['settime']);
	
	$machinedata=parse_ini_file('../database/machinedata.ini',true);
	$timeini=parse_ini_file('../database/time.ini',true);
	$tb=parse_ini_file('../database/floorspend.ini',true);
	?>
	<style>
	body {
		width:100vw;
		height:100vh;
		padding:3px;
		margin:0;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
        box-sizing: border-box;
		font-family: Consolas,Microsoft JhengHei,sans-serif;
	}
	button,
	div {
		font-family: Consolas,Microsoft JhengHei,sans-serif;
	}
	.tablemap button,
	.funcmap button {
		width:100%;
		height:100%;
		font-size:1.5vw;
		font-family: Consolas,Microsoft JhengHei,sans-serif;
	}
	.tablemap button #amt span:before {
		content:'<?php echo $initsetting["init"]["frontunit"];  ?>';
	}
	.tablemap button #amt span:after {
		content:'<?php echo $initsetting["init"]["unit"];  ?>';
	}
	.tablemap button[id="comput notempty "] {
		border:5px solid #898989;
		border-radius: 5px;
	    background-color: #ff0066;
		color:#ffffff;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
        box-sizing: border-box;
	}
	.outsidelist button,
	.splittablelist button {
		width:100%;
		height:80px;
		margin-bottom:3px;
		float:left;
	}
	button div#tablenumber,
	button div#persons {
		width:50%;
		height:50%;
		line-height:130%;
		float:left;
	}
	button div#checkbox {
		width:50%;
		height:100%;
		font-size:300%;
		float:left;
	}
	button div#QTYlabel,
	button div#QTY {
		width:50%;
		height:100%;
		line-height:420%;
		float:left;
	}
	button div#amt,
	button div#createdatetime {
		width:50%;
		height:50%;
		line-height:130%;
		text-align:right;
		float:right;
	}
	.inittable td,
	.changetable td,
	.combine td,
	.tablecombine td {
		 width:calc(100% / <?php echo intval($tb['TA']['col'])+1; ?>);
		 height:calc(100% / <?php echo intval($tb['TA']['row']); ?>);
	}
	.changetable #notempty,
	.combine #notempty,
	.combine #check,
	.tablecombine #notempty,
	.tablecombine #check,
	.tablesplit #notempty,
	.tablesplit #check {
		border:5px solid #898989;
		border-radius: 5px;
	    background-color: #ff0066;
		color:#ffffff;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
        box-sizing: border-box;
	}
	#ui-datepicker-div.ui-datepicker {
		font-size:3vw;
	}
	</style>
	<script>
	$(document).ready(function(){
		setInterval(function(){
			$.ajax({
				url:'./comput.ajax.php',
				dataType:'json',
				success:function(d){
					$('#time').val(d['time']);
					//console.log(d);
					for(var i=0;i<$('.table[id^="comput"]').length;i++){
						if($('.table[id^="comput"]:eq('+i+') #tablenumber input[name="tabnum"]').val()!='outside'){
							var temptabnum=$('.table[id^="comput"]:eq('+i+') #tablenumber').html().split('<input');
							//console.log( d[$('.table[id^="comput"]:eq('+i+') #tablenumber input[name="tabnum"]').val()]);
							if(typeof d[temptabnum[0]]!="undefined"){
								$('.table[id^="comput"]:eq('+i+')').prop('id','comput notempty ');
								$('.table[id^="comput"]:eq('+i+') #tablenumber input[name="tabnum"]').val(d[temptabnum[0]]['inittablenum']);
								$('.table[id^="comput"]:eq('+i+') #tablenumber input[name="consecnumber"]').val(d[temptabnum[0]]['consecnumber']);
								$('.table[id^="comput"]:eq('+i+') #createdatetime input[name="bizdate"]').val(d[temptabnum[0]]['bizdate']);
								$('.table[id^="comput"]:eq('+i+') #amt').html('<span>'+d[temptabnum[0]]['money']+'</span>');
								if(Number(d[temptabnum[0]]['persons'])>0){
									//console.log($('.table[id^="comput"]:eq('+i+') #persons').length);
									$('.table[id^="comput"]:eq('+i+') #persons').html(d[temptabnum[0]]['persons']+'位');
								}
								else{
									$('.table[id^="comput"]:eq('+i+') #persons').html('');
								}
								if(parseInt(d[temptabnum[0]]['mins'])><?php echo $initsetting['init']['hinttime']; ?>){
									$('.table[id^="comput"]:eq('+i+')').css({'background-color':''});
								}
								else if(parseInt(d[temptabnum[0]]['mins'])<=0){
									$('.table[id^="comput"]:eq('+i+')').css({'background-color':'#00d941'});
								}
								else if(parseInt(d[temptabnum[0]]['mins'])<=<?php echo $initsetting['init']['sechinttime']; ?>){
									$('.table[id^="comput"]:eq('+i+')').css({'background-color':'#e7f12c'});
								}
								else{
									$('.table[id^="comput"]:eq('+i+')').css({'background-color':'#73d7ec'});
								}
								$('.table[id^="comput"]:eq('+i+') #createdatetime #val').html(d[temptabnum[0]]['mins']);

								if($('.changetable .chtable:eq('+i+')[id="notempty check"]').length>0){
									//$('.combine .chtable:eq('+i+')').prop('id','notempty');
								}
								else{
									$('.changetable .chtable:eq('+i+')').prop('id','notempty');
									$('.changetable .chtable:eq('+i+')').css({'border':'5px solid #898989','border-radius':'5px','background-color':'#ff0066','color':'#ffffff'});
								}
								$('.changetable .chtable:eq('+i+') input[name="tabnum"]').val(d[temptabnum[0]]['inittablenum']);
								$('.changetable .chtable:eq('+i+') input[name="consecnumber"]').val(d[temptabnum[0]]['consecnumber']);
								if($('.combine .chtable:eq('+i+')[id="check"]').length>0){
									//$('.combine .chtable:eq('+i+')').prop('id','notempty');
								}
								else{
									$('.combine .chtable:eq('+i+')').prop('id','notempty');
									$('.combine .chtable:eq('+i+')').css({'border':'5px solid #898989','border-radius':'5px','background-color':'#ff0066','color':'#ffffff'});
								}
								$('.combine .chtable:eq('+i+') input[name="tabnum"]').val(d[temptabnum[0]]['inittablenum']);
								$('.combine .chtable:eq('+i+') input[name="consecnumber"]').val(d[temptabnum[0]]['consecnumber']);
								if($('.tablecombine .chtable:eq('+i+')[id="check"]').length>0){
									//$('.combine .chtable:eq('+i+')').prop('id','notempty');
								}
								else{
									$('.tablecombine .chtable:eq('+i+')').prop('id','notempty');
									$('.tablecombine .chtable:eq('+i+')').css({'border':'5px solid #898989','border-radius':'5px','background-color':'#ff0066','color':'#ffffff'});
								}
								$('.tablecombine .chtable:eq('+i+') input[name="tabnum"]').val(d[temptabnum[0]]['inittablenum']);
								$('.tablecombine .chtable:eq('+i+') input[name="consecnumber"]').val(d[temptabnum[0]]['consecnumber']);
								if($('.tablesplit .chtable:eq('+i+')[id="notempty check"]').length>0){
									//$('.combine .chtable:eq('+i+')').prop('id','notempty');
								}
								else{
									$('.tablesplit .chtable:eq('+i+')').prop('id','notempty');
									$('.tablesplit .chtable:eq('+i+')').css({'border':'5px solid #898989','border-radius':'5px','background-color':'#ff0066','color':'#ffffff'});
								}
								$('.tablesplit .chtable:eq('+i+') input[name="tabnum"]').val(d[temptabnum[0]]['inittablenum']);
								$('.tablesplit .chtable:eq('+i+') input[name="consecnumber"]').val(d[temptabnum[0]]['consecnumber']);
							}
							else{
								$('.table[id^="comput"]:eq('+i+')').prop('id','comput');
								var temptabnum=$('.table[id^="comput"]:eq('+i+') #tablenumber').html().split('<input');
								$('.table[id^="comput"]:eq('+i+') #tablenumber input[name="tabnum"]').val(temptabnum[0]);
								$('.table[id^="comput"]:eq('+i+') #tablenumber input[name="consecnumber"]').val('');
								$('.table[id^="comput"]:eq('+i+') #createdatetime input[name="bizdate"]').val('');
								$('.table[id^="comput"]:eq('+i+') #amt').html('');
								$('.table[id^="comput"]:eq('+i+') #persons').html('');
								$('.table[id^="comput"]:eq('+i+')').css({'background-color':''});
								$('.table[id^="comput"]:eq('+i+') #createdatetime #val').html('');
								if($('.changetable .chtable:eq('+i+')[id="check"]').length>0){
									//$('.changetable .chtable:eq('+i+')').prop('id','');
								}
								else{
									$('.changetable .chtable:eq('+i+')').prop('id','');
									$('.changetable .chtable:eq('+i+')').css({'border':'','border-radius':'','background-color':'','color':''});
								}
								$('.changetable .chtable:eq('+i+') input[name="consecnumber"]').val('');
								if($('.combine .chtable:eq('+i+')[id="check"]').length>0){
									//$('.changetable .chtable:eq('+i+')').prop('id','');
								}
								else{
									$('.combine .chtable:eq('+i+')').prop('id','');
									$('.combine .chtable:eq('+i+')').css({'border':'','border-radius':'','background-color':'','color':''});
								}
								$('.combine .chtable:eq('+i+') input[name="consecnumber"]').val('');
								if($('.tablecombine .chtable:eq('+i+')[id="oncheck"]').length>0){
									//$('.changetable .chtable:eq('+i+')').prop('id','');
								}
								else{
									$('.tablecombine .chtable:eq('+i+')').prop('id','');
									$('.tablecombine .chtable:eq('+i+')').css({'border':'','border-radius':'','background-color':'','color':''});
								}
								$('.tablecombine .chtable:eq('+i+') input[name="consecnumber"]').val('');
								if($('.tablesplit .chtable:eq('+i+')[id="check"]').length>0){
									//$('.changetable .chtable:eq('+i+')').prop('id','');
								}
								else{
									$('.tablesplit .chtable:eq('+i+')').prop('id','');
									$('.tablesplit .chtable:eq('+i+')').css({'border':'','border-radius':'','background-color':'','color':''});
								}
								$('.tablesplit .chtable:eq('+i+') input[name="consecnumber"]').val('');
							}
						}
						else{
						}
					}
					if(d['outside']>0){
						$('.table #QTY').html('尚有'+d['outside']+'單');
					}
					else{
						$('.table #QTY').html('');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		},1000);
		inittable=$('.inittable').dialog({
			autoOpen:true,
			width:$(window).width(),
			height:$(window).height(),
			title:'桌控畫面(營業日期:<?php echo $machinedata["basic"]["bizdate"]; ?>；目前班別:<?php echo $machinedata["basic"]["zcounter"]; ?>；<?php if(isset($_GET["submachine"]))echo "S-".$_GET["submachine"];else if(isset($_GET["machine"]))echo "M-".$_GET["machine"];else echo "M-m1"; ?>)',
			resizable:false,
			modal:true,
			draggable:false,
			close:function(){
				inittable.dialog('open');
			}
		});
		changetable=$('.changetable').dialog({
			autoOpen:false,
			width:$(window).width(),
			height:$(window).height(),
			/*title:'換桌',*/
			resizable:false,
			modal:true,
			draggable:false,
			close:function(){
				$('.changetable #change').prop('disabled',true);
				$('.changetable #change #c1t').html('');
				$('.changetable #change #c2t').html('');
				$('.changetable #c1').val('empty');
				$('.changetable #c1consecnumber').val('');
				$('.changetable #c2').val('empty');
				$('.changetable .tablemap .chtable[id="notempty check"]').css({'border':'5px solid #898989','border-radius':'5px','background-color':'#ff0066','color':'#ffffff'});
				$('.changetable .tablemap .chtable[id="notempty check"]').prop('id','notempty');
				$('.changetable .tablemap .chtable#check').css({'border':'','border-radius':'','background-color':'','color':''});
				$('.changetable .tablemap .chtable#check').prop('id','');
			}
		});
		combine=$('.combine').dialog({
			autoOpen:false,
			width:$(window).width(),
			height:$(window).height(),
			/*title:'合併結帳',*/
			resizable:false,
			modal:true,
			draggable:false,
			close:function(){
				$('.combine .chtable').prop('id','');
				$('.combine .chtable #checkbox').html('');
			}
		});
		tablecombine=$('.tablecombine').dialog({
			autoOpen:false,
			width:$(window).width(),
			height:$(window).height(),
			/*title:'併桌',*/
			resizable:false,
			modal:true,
			draggable:false,
			close:function(){
				$('.tablecombine .chtable').prop('id','');
				$('.tablecombine .chtable #checkbox').html('');
			}
		});
		tablesplit=$('.tablesplit').dialog({
			autoOpen:false,
			width:$(window).width(),
			height:$(window).height(),
			/*title:'拆桌',*/
			resizable:false,
			modal:true,
			draggable:false,
			close:function(){
				$('.tablesplit .chtable').prop('id','');
				$('.tablesplit .chtable #checkbox').html('');
			}
		});
		AE=$('.AE').dialog({
			autoOpen:false,
			width:600,
			height:$(window).height(),
			/*title:'支出費用',*/
			resizable:false,
			modal:true,
			draggable:false,
			open:function(){
				getzcounter();
			},
			close:function(){
				$('.AE select[name="zcounter"]').html('');
				$('.AE #moneytype').val('');
				$('.AE input[name="moneytype"]').val('');
				$('.AE input[name="moneysubtype"]').val('');
				$('.AE input[name="money"]').val('0');
				$('.AE textarea[name="remarks"]').val('');
			}
		});
		selecttype=$('.selecttype').dialog({
			autoOpen:false,
			width:$(window).width()*0.9,
			height:170,
			/*title:'選擇類別',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		setmoney=$('.setmoney').dialog({
			autoOpen:false,
			width:600,
			height:$(window).height()*7/10,
			/*title:'金額',*/
			position:{my:'bottom',at:'bottom',of:'body'},
			resizable:false,
			modal:true,
			draggable:false
		});
		outsidelist=$('.outsidelist').dialog({
			autoOpen:false,
			width:500,
			height:768,
			/*title:'外帶帳單',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		splittablelist=$('.splittablelist').dialog({
			autoOpen:false,
			width:500,
			height:768,
			/*title:'拆桌帳單',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		exitsys=$('.exitsys').dialog({//系統訊息
			autoOpen:false,
			/*width:450,
			height:300,*/
			/*title:'系統訊息',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		if($(window).width()==1920){
			exitsys.dialog('option','width',450);
			exitsys.dialog('option','height',300);
		}
		else{//if($(window).width()==1366)
			exitsys.dialog('option','width',(450));
			exitsys.dialog('option','height',(220));
		}
		checkcombine=$('.checkcombine').dialog({
			autoOpen:false,
			/*width:450,
			height:300,*/
			/*title:'系統訊息',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		if($(window).width()==1920){
			checkcombine.dialog('option','width',450);
			checkcombine.dialog('option','height',400);
		}
		else{//if($(window).width()==1366)
			checkcombine.dialog('option','width',(450));
			checkcombine.dialog('option','height',(400));
		}
		checktablecombine=$('.checktablecombine').dialog({
			autoOpen:false,
			/*width:450,
			height:300,*/
			/*title:'系統訊息',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		if($(window).width()==1920){
			checktablecombine.dialog('option','width',450);
			checktablecombine.dialog('option','height',400);
		}
		else{//if($(window).width()==1366)
			checktablecombine.dialog('option','width',(450));
			checktablecombine.dialog('option','height',(400));
		}
		checktablesplit=$('.checktablesplit').dialog({
			autoOpen:false,
			/*width:450,
			height:300,*/
			/*title:'系統訊息',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		if($(window).width()==1920){
			checktablesplit.dialog('option','width',450);
			checktablesplit.dialog('option','height',400);
		}
		else{//if($(window).width()==1366)
			checktablesplit.dialog('option','width',(450));
			checktablesplit.dialog('option','height',(400));
		}
		tablehint=$('.tablehint').dialog({
			autoOpen:false,
			width:450,
			height:500,
			/*title:'時段提示',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		funbox=$('.funbox').dialog({//功能區
			autoOpen:false,
			width:1024,
			height:500,
			/*title:'系統訊息',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		setchange=$('.setchange').dialog({//設定找零金
			autoOpen:false,
			/*width:1044,
			height:966,*/
			/*title:'設定找零金',*/
			position:{my:'right bottom',at:'right bottom',of:'body'},
			resizable:false,
			modal:true,
			draggable:false,
			close:function(){
				$.ajax({
					url:'./lib/js/setchange.php',
					method:'post',
					data:{'change':$('.setchange input[name="view"]').val(),'usercode':$('#tabs4 form[data="listform"] input[name="usercode"]').val(),'username':$('#tabs4 #listform input[name="username"]').val(),'machinetype':$('#tabs4 #listform input[name="machinetype"]').val()},
					dataType:'html',
					success:function(){
					}
				});
				$('#MemberBill #billfun #billfun1button').prop('disabled',false);
				$('#MemberBill #billfun #billfun1button').trigger('click');
				//$billfun.tabs('enable',0);
				//$billfun.tabs('option','active',[0]);
			}
		});
		if($(window).width()==1920){
			setchange.dialog('option','width',1044);
			setchange.dialog('option','height',966);
		}
		else if($(window).width()==1366){
			setchange.dialog('option','width',(1366*0.63-6.4-72.3));
			setchange.dialog('option','height',(768*0.9-16));
		}
		else if($(window).width()==1280){
			setchange.dialog('option','width',(1280*0.63-72.3));
			setchange.dialog('option','height',(800*0.9-16));
		}
		else if($(window).width()==1024){
			setchange.dialog('option','width',1024);
			setchange.dialog('option','height',(768*0.9-16));
		}
		verpsw=$('.verpsw').dialog({//補印發票之驗證密碼視窗
			autoOpen:false,
			width:500,
			height:200,
			/*title:'驗證密碼',*/
			resizable:false,
			modal:true,
			draggable:false
		});
		$('.verpsw #cancel').click(function(){
			$('.verpsw input[name="verpsw"]').val('');
			verpsw.dialog('close');
		});
		$('.verpsw #send').click(function(){
			if($('.verpsw input[name="verpsw"]').val()=="<?php echo $initsetting['init']['voidpsw']; ?>"){
				$.ajax({
					url:'./lib/js/create.cmdtxt.php',
					method:'post',
					async: false,
					data:{'cmd':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else echo "m1"; ?>-cashdrawer'},
					dataType:'html',
					success:function(d){
						//console.log(d);
					},
					error:function(e){
						console.log(e);
					}
				});
				$('.verpsw input[name="verpsw"]').val('');
				verpsw.dialog('close');
			}
			else{
				alert("密碼輸入錯誤。");
			}
		});
		$('#funbox').click(function(){
			funbox.dialog('open');
		});
		$('.changetable #action').css({'margin-top':'1'});
		$('#changetable').click(function(){
			$.ajax({
				url:'./lib/js/checkopen.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'./lib/js/print.php',
							method:'post',
							data:{'html':'control checkopen.ajax.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					//console.log(d);
					if(d=='success'){
						changetable.dialog('open');
					}
					else{
						alert('目前尚未開班，請確認是否開班或重啟系統。');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$(document).on('click','#exitsys',function(){
			exitsys.dialog('open');
		});
		$(document).on('click','.exitsys .no',function(){
			exitsys.dialog('close');
		});
		$(document).on('click','.exitsys .yes',function(){
			if(<?php if(isset($_GET['submachine'])||isset($_GET['machine']))echo '1';else echo '0'; ?>){
				var mywin=window.open('exit://','','width=1px,height=1px');
			}
			else{
				if(<?php if(isset($_GET['machine'])&&$_GET['machine']!='m1')echo '1';else echo '0'; ?>){
					var mywin=window.open('exit://','','width=1px,height=1px');
				}
				else{
					$.ajax({
						url:'./lib/js/create.cmdtxt.php',
						method:'post',
						async: false,
						data:{'cmd':'exit'},
						dataType:'html',
						success:function(d){
							if(d.length>20){
								$.ajax({
									url:'./lib/js/print.php',
									method:'post',
									data:{'html':'control create.cmdtxt.php '+d},
									dataType:'html',
									success:function(d){/*console.log(d);*/},
									error:function(e){/*console.log(e);*/}
								});
							}
							else{
							}
							//console.log(d);
						},
						error:function(e){
							console.log(e);
						}
					});
				}
			}
		});
		$('.funbox #AE').click(function(){
			$.ajax({
				url:'./lib/js/checkopen.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					//console.log(d);
					if(d.length>20){
						$.ajax({
							url:'./lib/js/print.php',
							method:'post',
							data:{'html':'control checkopen.ajax.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					if(d=='success'){
						AE.dialog('open');
						if($('.AE #moneytype').val()==''){
							selecttype.dialog('open');
						}
						else{
						}
					}
					else{
						alert('目前尚未開班，請確認是否開班或重啟系統。');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$(document).on('click','.tablemap .table',function(){
			var index=$('button').index(this);
			var tabnum=$(this).find('#tablenumber input[name="tabnum"]').val();
			if($('button:eq('+index+')[name="split"]').length>0){
				$.ajax({
					url:'./getsplittablelist.ajax.php',
					method:'post',
					async:false,
					data:{'bizdate':'<?php echo $machinedata["basic"]["bizdate"]; ?>','zcounter':'<?php echo $machinedata["basic"]["zcounter"]; ?>','tabnum':tabnum},
					dataType:'json',
					success:function(d){
						//console.log(d);
						$('.splittablelist').html('<button id="splittablebut" value="'+$('button:eq('+index+')[name="split"] #tablenumber input[name="tabnum"]').val()+'">拆桌</button>');
						$.each(d,function(index,value){
							if(index=='time'){
								return false;
							}
							else{
								$('.splittablelist').append('<button><div id="tablenum">'+value['tablenumber']+'</div><div id="consecnumber">'+index+'</div><div id="bizdate">'+value['bizdate']+'</div></button>');
							}
						});
					},
					error:function(e){
						console.log(e);
					}
				});
				splittablelist.dialog('open');
			}
			else{
				$.ajax({
					url:'./checktableini.ajax.php',
					method:'post',
					async:false,
					data:{'bizdate':'<?php echo $machinedata["basic"]["bizdate"]; ?>','zcounter':'<?php echo $machinedata["basic"]["zcounter"]; ?>','tabnum':tabnum,'submachine':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else echo "empty"; ?>','machine':'<?php if(isset($_GET["machine"]))echo $_GET["machine"];else if(!isset($_GET["submachine"])&&!isset($_GET["machine"]))echo "m1";else echo "empty"; ?>','type':'in'},
					dataType:'html',
					success:function(d){
						//console.log(d);
						var tempd=d.split('-');
						if(tempd[0]=='lock'){
							var res=confirm(tempd[1]+'點餐中。\n是否仍要點餐？');
							if(res==true){
								$.ajax({
									url:'./mandchange.ajax.php',
									method:'post',
									async:false,
									data:{'bizdate':'<?php echo $machinedata["basic"]["bizdate"]; ?>','zcounter':'<?php echo $machinedata["basic"]["zcounter"]; ?>','tabnum':tabnum,'submachine':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else echo "empty"; ?>','machine':'<?php if(isset($_GET["machine"]))echo $_GET["machine"];else if(!isset($_GET["submachine"])&&!isset($_GET["machine"]))echo "m1";else echo "empty"; ?>','type':'in'},
									dataType:'html',
									success:function(d){
										if($('button:eq('+index+') input[name="consecnumber"]').val().length>0&&$('button:eq('+index+') input[name="bizdate"]').val().length>0){
											location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&tabnum&bizdate='+$('button:eq('+index+') input[name="bizdate"]').val()+'&consecnumber='+$('button:eq('+index+') input[name="consecnumber"]').val();
										}
										else{
											location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&tabnum='+$('button:eq('+index+') input[name="tabnum"]').val();
										}
									},
									error:function(e){
										console.log(e);
									}
								});
							}
							else{
							}
						}
						else{
							if($('button:eq('+index+') input[name="consecnumber"]').val().length>0&&$('button:eq('+index+') input[name="bizdate"]').val().length>0){
								location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&tabnum&bizdate='+$('button:eq('+index+') input[name="bizdate"]').val()+'&consecnumber='+$('button:eq('+index+') input[name="consecnumber"]').val();
							}
							else{
								location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&tabnum='+$('button:eq('+index+') input[name="tabnum"]').val();
							}
						}
					},
					error:function(e){
						console.log(d);
					}
				});
			}
		});
		$(document).on('click','.funcmap .table',function(){
			var index=$('button').index(this);
			if($('button:eq('+index+') input[name="tabnum"]').val()=="outside"){
				$.ajax({
					url:'./getoutlist.ajax.php',
					dataType:'json',
					success:function(d){
						$('.outsidelist').html('<button id="newlist">新增外帶單</button>');
						$.each(d,function(index,value){
							if(index=='time'){
								return false;
							}
							else{
								$('.outsidelist').append('<button><div id="consecnumber">'+index+'</div><div id="bizdate">'+value+'</div></button>');
							}
						});
					},
					error:function(e){
						console.log(e);
					}
				});
				outsidelist.dialog('open');
			}
			else{
			}
		});
		$(document).on('click','.outsidelist button',function(){
			var index=$('.outsidelist button').index(this);
			if($(this).prop('id')=='newlist'){
				location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=2';
			}
			else{
				location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=2&bizdate='+$('.outsidelist button:eq('+index+') #bizdate').html()+'&consecnumber='+$('.outsidelist button:eq('+index+') #consecnumber').html();
			}
		});
		$(document).on('click','.splittablelist button',function(){
			var index=$('.splittablelist button').index(this);
			if($(this).prop('id')=='splittablebut'){
				$.ajax({
					url:'./gettablelist.ajax.php',
					method:'post',
					data:{'bizdate':'<?php echo $machinedata["basic"]["bizdate"]; ?>','zcounter':'<?php echo $machinedata["basic"]["zcounter"]; ?>','tablenum':$(this).val()},
					dataType:'html',
					success:function(d){
						if(d=="目前點選之桌號已併桌，無法進行拆桌動作。"){
							$('.checktablesplit #text').html(d);
							$('.checktablesplit #sale').prop('disabled',true);
							checktablesplit.dialog('open');
						}
						else{
							$('.checktablesplit #text').html(d);
							$('.checktablesplit #sale').prop('disabled',false);
							checktablesplit.dialog('open');
						}
					},
					error:function(e){
						console.log(e);
					}
				});
			}
			else{
				$.ajax({
					url:'./checktableini.ajax.php',
					method:'post',
					async:false,
					data:{'bizdate':'<?php echo $machinedata["basic"]["bizdate"]; ?>','zcounter':'<?php echo $machinedata["basic"]["zcounter"]; ?>','tabnum':$('.splittablelist button:eq('+index+') #tablenum').html(),'submachine':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else echo "empty"; ?>','machine':'<?php if(isset($_GET["machine"]))echo $_GET["machine"];else echo "empty"; ?>','type':'in'},
					dataType:'html',
					success:function(d){
						console.log(d);
						var tempd=d.split('-');
						if(tempd[0]=='lock'){
							var res=confirm(tempd[1]+'點餐中。\n是否仍要點餐？');
							if(res==true){
								$.ajax({
									url:'./mandchange.ajax.php',
									method:'post',
									async:false,
									data:{'bizdate':'<?php echo $machinedata["basic"]["bizdate"]; ?>','zcounter':'<?php echo $machinedata["basic"]["zcounter"]; ?>','tabnum':tabnum,'submachine':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else echo "empty"; ?>','machine':'<?php if(isset($_GET["machine"]))echo $_GET["machine"];else echo "empty"; ?>','type':'in'},
									dataType:'html',
									success:function(d){
										location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&bizdate='+$('.splittablelist button:eq('+index+') #bizdate').html()+'&consecnumber='+$('.splittablelist button:eq('+index+') #consecnumber').html()+'&tabnum='+$('.splittablelist button:eq('+index+') #tablenum').html();
									},
									error:function(e){
										console.log(e);
									}
								});
							}
							else{
							}
						}
						else{
							location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&bizdate='+$('.splittablelist button:eq('+index+') #bizdate').html()+'&consecnumber='+$('.splittablelist button:eq('+index+') #consecnumber').html()+'&tabnum='+$('.splittablelist button:eq('+index+') #tablenum').html();
						}
					},
					error:function(e){
						console.log(d);
					}
				});
			}
		});
		$('.changetable .tablemap .chtable').click(function(){
			//console.log($(this).find('#tablenumber input[name="tabnum"]').val());
			if($(this).find('#tablenumber input[name="consecnumber"]').val()==''){
				if($('.changetable #c2').val()=='empty'){
					$('.changetable #c2').val($(this).find('#tablenumber input[name="tabnum"]').val());
					$(this).prop('id','check');
					$(this).css({'border':'5px solid #898989','border-radius':'5px','background-color':'#00ff66','color':'#ffffff'});
					var temptable=$(this).find('#tablenumber').html().split('<input');
					$('.changetable #change #c2t').html(temptable[0]);
				}
				else{
					if($('.changetable #c2').val()==$(this).find('#tablenumber input[name="tabnum"]').val()){
						$('.changetable #c2').val('empty');
						$(this).prop('id','');
						$(this).css({'border':'','border-radius':'','background-color':'','color':''});
						$('.changetable #change #c2t').html('');
					}
					else{
					}
				}
			}
			else{
				if($('.changetable #c1').val()=='empty'){
					$('.changetable #c1').val($(this).find('#tablenumber input[name="tabnum"]').val());
					$('.changetable #c1consecnumber').val($(this).find('#tablenumber input[name="consecnumber"]').val());
					$(this).prop('id','notempty check');
					$(this).css({'border':'5px solid #898989','border-radius':'5px','background-color':'#00ffff','color':'#ffffff'});
					var temptable=$(this).find('#tablenumber').html().split('<input');
					$('.changetable #change #c1t').html(temptable[0]);
				}
				else{
					if($('.changetable #c1').val()==$(this).find('#tablenumber input[name="tabnum"]').val()){
						$('.changetable #c1').val('empty');
						$('.changetable #c1consecnumber').val('');
						$(this).prop('id','notempty');
						$(this).css({'border':'5px solid #898989','border-radius':'5px','background-color':'#ff0066','color':'#ffffff'});
						$('.changetable #change #c1t').html('');
					}
					else{
					}
				}
			}
			if($('.changetable #change #c1t').html()==''||$('.changetable #change #c2t').html()==''){
				$('.changetable #change').prop('disabled',true);
			}
			else{
				$('.changetable #change').prop('disabled',false);
			}
		});
		$('.changetable .funcmap #return').click(function(){
			changetable.dialog('close');
		});
		$('.changetable .funcmap #change').click(function(){
			if($('.chnagetable #c1').val()=='empty'||$('.chnagetable #c2').val()=='empty'){
			}
			else{
				$('.changetable .funcmap #change').prop('disabled',true);
				$.ajax({
					url:'./lib/js/changetable.ajax.php',
					method:'post',
					data:{'c1':$('.changetable #c1').val(),'c1consecnumber':$('.changetable #c1consecnumber').val(),'c2':$('.changetable #c2').val(),'c2consecnumber':$('.changetable #c2consecnumber').val()},
					dataType:'html',
					success:function(d){
						if(d.length>20){
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'control changetable.ajax.php '+d},
								dataType:'html',
								success:function(d){/*console.log(d);*/},
								error:function(e){/*console.log(e);*/}
							});
						}
						else{
						}
						console.log(d);
						$('.changetable .funcmap #change').prop('disabled',false);
						changetable.dialog('close');
						//location.reload();
					},
					error:function(e){
						console.log(e);
					}
				});
			}
		});
		$( ".AE #bizdate" ).datepicker();
		$( ".AE #bizdate" ).datepicker('setDate','<?php echo substr($machinedata["basic"]["bizdate"],0,4)."-".substr($machinedata["basic"]["bizdate"],4,2)."-".substr($machinedata["basic"]["bizdate"],6,2); ?>');
		$( ".AE #bizdate" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
		$('.AE #bizdate').change(function(){
			getzcounter();
		});
		function getzcounter(){
			$.ajax({
				url:'./getzcounter.ajax.php',
				method:'post',
				data:{'bizdate':$('.AE #bizdate').val()},
				dataType:'json',
				success:function(d){
					//console.log(d);
					$('.AE select[name="zcounter"]').html('');
					for(var i=0;i<d.length;i++){
						if(d[i]['ZCOUNTER']==null||d[i]['ZCOUNTER']==''){
						}
						else{
							if(i==d.length-1){
								$('.AE select[name="zcounter"]').append('<option value='+d[i]['ZCOUNTER']+' selected>'+d[i]['ZCOUNTER']+'</option>');
							}
							else{
								$('.AE select[name="zcounter"]').append('<option value='+d[i]['ZCOUNTER']+'>'+d[i]['ZCOUNTER']+'</option>');
							}
						}
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		$('.AE #moneytype').click(function(){
			selecttype.dialog('open');
		});
		$('.selecttype #buttype').click(function(){
			var index=$('.selecttype #buttype').index(this);
			$('.AE #moneytype').val($(this).find('div').html());
			$('.AE input[name="moneytype"]').val($(this).find('#typevalue').val());
			selecttype.dialog('close');
			if(Number($('.AE input[name="money"]').val())<=0){
				setmoney.dialog('open');
			}
			else{
			}
		});
		$('.AE input[name="money"]').click(function(){
			if(Number($('.AE input[name="money"]').val())<=0){
				$('.setmoney input[name="viewnumber"]').val('0');
			}
			else{
				$('.setmoney input[name="viewnumber"]').val($('.AE input[name="money"]').val());
			}
			setmoney.dialog('open');
		});
		$('.setmoney #numbut').click(function(){
			if(Number($('.setmoney input[name="viewnumber"]').val())<=0){
				$('.setmoney input[name="viewnumber"]').val($(this).find('div').html());
			}
			else{
				$('.setmoney input[name="viewnumber"]').val($('.setmoney input[name="viewnumber"]').val()+$(this).find('div').html());
			}
		});
		$('.setmoney #reset').click(function(){
			$('.setmoney input[name="viewnumber"]').val('0');
		});
		$('.setmoney #send').click(function(){
			$('.AE input[name="money"]').val($('.setmoney input[name="viewnumber"]').val());
			$('.setmoney input[name="viewnumber"]').val('');
			setmoney.dialog('close');
		});
		$('.AE #send').click(function(){
			if($('.AE #bizdate').val()==''||$('.AE select[name="zcounter"] option:selected').length==0||$('.AE input[name="moneytype"]').val()==''){
				alert('請填寫營業日期、班別與選擇一樣科目。');
			}
			else{
				$.ajax({
					url:'./write.AE.php',
					method:'post',
					data:{'machinetype':'<?php if(isset($_GET["submachine"])){ if(strlen($_GET["submachine"])==0){ echo "m2"; } else { echo $_GET["submachine"]; } } else if(isset($_GET["machine"])){ if(strlen($_GET["machine"])==0){ echo "m1"; } else{ echo $_GET["machine"]; } } else{ echo "m1"; }?>','usercode':'<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>','username':'<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>','type':$('.AE input[name="aetype"]:checked').val(),'bizdate':$('.AE #bizdate').val(),'zcounter':$('.AE select[name="zcounter"] option:selected').val(),'moneytype':$('.AE input[name="moneytype"]').val(),'moneytypename':$('.AE #moneytype').val(),'subtype':$('.AE input[name="moneysubtype"]').val(),'money':$('.AE input[name="money"]').val(),'radius':$('.AE input[name="radius"]:checked').val(),'remarks':$('.AE textarea[name="remarks"]').val()},
					dataType:'html',
					success:function(d){
						if(d.length>20){
							$.ajax({
								url:'./lib/js/print.php',
								method:'post',
								data:{'html':'control write.AE.php '+d},
								dataType:'html',
								success:function(d){/*console.log(d);*/},
								error:function(e){/*console.log(e);*/}
							});
						}
						else{
						}
						//console.log(d);
						alert('支出費用輸入成功');
						AE.dialog('close');
					},
					error:function(e){
						console.log(e);
					}
				});
			}
		});
		$('.AE #cancel').click(function(){
			$('.AE #moneytype').val('');
			$('.AE input[name="moneytype"]').val('');
			$('.AE input[name="moneysubtype"]').val('');
			$('.AE input[name="money"]').val('0');
			$('.AE textarea[name="remarks"]').val('');
			AE.dialog('close');
		});
		$('.funbox #open').click(function(){
			$('.funbox button').prop('disabled',true);
			$.ajax({
				url:'./lib/js/checkopen.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					console.log(d);
					if(d=='success'){
						$('.funbox #close').prop('disabled',false);
						$('.funbox #return').prop('disabled',false);
					}
					else{
						$.ajax({
							url:'./lib/js/open.ajax.php',
							method:'post',
							async:false,
							data:{'usercode':'<?php if(isset($_GET["usercode"]))echo $_GET["usercode"];else echo ""; ?>','username':'<?php if(isset($_GET["username"]))echo $_GET["username"];else echo ""; ?>','machinetype':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else echo ""; ?>'},
							dataType:'html',
							success:function(d){
								if(d.length>20){
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										data:{'html':'control open.ajax.php '+d},
										dataType:'html',
										success:function(d){/*console.log(d);*/},
										error:function(e){/*console.log(e);*/}
									});
								}
								else{
								}
								if(d=='error'){
									console.log(d);
								}
								else{
									$.ajax({
										url:'./lib/js/change.class.php',
										method:'post',
										async:false,
										data:{'type':'isclose'},
										dataType:'html',
										success:function(d){
											$('.funbox #close').prop('disabled',false);
											$('.funbox #AE').prop('disabled',false);
											$('.funbox #return').prop('disabled',false);
											if(d.length>20){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'control change.class.php '+d},
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
												data:{'cmd':'upload'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
											setchange.dialog('open');
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
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$('.funbox #close').click(function(){
			$('.funbox button').prop('disabled',true);
			$.ajax({
				url:'./lib/js/checkopen.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					console.log(d);
					if(d=='success'){
						$.ajax({
							url:'./lib/js/close.ajax.php',
							method:'post',
							async:false,
							data:{'usercode':'<?php if(isset($_GET["usercode"]))echo $_GET["usercode"];else echo ""; ?>','username':'<?php if(isset($_GET["username"]))echo $_GET["username"];else echo ""; ?>','machinetype':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else echo ""; ?>'},
							dataType:'html',
							success:function(d){
								if(d.length>20){
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										data:{'html':'control close.ajax.php '+d},
										dataType:'html',
										success:function(d){/*console.log(d);*/},
										error:function(e){/*console.log(e);*/}
									});
								}
								else{
								}
								if(d=='error'){
									console.log(d);
								}
								else{
									$.ajax({
										url:'./lib/js/change.class.php',
										method:'post',
										async:false,
										data:{'type':'isopen'},
										dataType:'html',
										success:function(d){
											if(d.length>20){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'control change.class.php '+d},
													dataType:'html',
													success:function(d){/*console.log(d);*/},
													error:function(e){/*console.log(e);*/}
												});
											}
											else{
											}
											//console.log(d);
											//$billfun.tabs('disable','#billfun1');
											//$('.funbox #close').prop('disabled',false);
											$('.funbox #open').prop('disabled',false);
											$('.funbox #return').prop('disabled',false);
											$.ajax({
												url:'./lib/js/shift.paper.php',
												method:'post',
												async:false,
												data:{'zcounter':d},
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
						$('.funbox #open').prop('disabled',false);
						$('.funbox #return').prop('disabled',false);
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$('.inittable #cashdrawer').click(function(){
			//var mywin=window.open('cashdrawer://','','width=1px,height=1px');
			//mywin.document.title='cashdrawer';
			if(<?php echo $initsetting['init']['voidsale']; ?>){
				verpsw.dialog('open');
			}
			else{
				$.ajax({
					url:'./lib/js/create.cmdtxt.php',
					method:'post',
					async: false,
					data:{'cmd':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else echo "m1"; ?>-cashdrawer'},
					dataType:'html',
					success:function(d){
						console.log(d);
					},
					error:function(e){
						console.log(e);
					}
				});
			}
		});
		$('.inittable #combine').click(function(){
			$.ajax({
				url:'./lib/js/checkopen.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'./lib/js/print.php',
							method:'post',
							data:{'html':'control checkopen.ajax.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					//console.log(d);
					if(d=='success'){
						combine.dialog('open');
					}
					else{
						alert('目前尚未開班，請確認是否開班或重啟系統。');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$('.inittable #tablecombine').click(function(){
			$.ajax({
				url:'./lib/js/checkopen.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'./lib/js/print.php',
							method:'post',
							data:{'html':'control checkopen.ajax.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					//console.log(d);
					if(d=='success'){
						tablecombine.dialog('open');
					}
					else{
						alert('目前尚未開班，請確認是否開班或重啟系統。');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$('.inittable #tablesplit').click(function(){
			$.ajax({
				url:'./lib/js/checkopen.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'./lib/js/print.php',
							method:'post',
							data:{'html':'control checkopen.ajax.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					//console.log(d);
					if(d=='success'){
						tablesplit.dialog('open');
					}
					else{
						alert('目前尚未開班，請確認是否開班或重啟系統。');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$('.inittable #tablehint').click(function(){
			$.ajax({
				url:'./gettimelist.ajax.php',
				async:false,
				dataType:'html',
				success:function(d){
					$('.tablehint #timelist').html(d);
					tablehint.dialog('open');
				},
				errpr:function(e){
					console.log(e);
				}
			});
			//console.log('1');
		});
		$('.tablehint #cancel').click(function(){
			$('.tablehint #tiemlist').html('');
			tablehint.dialog('close');
		});
		$(document).on('click','.combine .chtable#notempty',function(){
			$(this).prop('id','check');
			$(this).find('#checkbox').html('✔');
			if($('.combine #check').length>=2){
				$('.combine #sale').prop('disabled',false);
			}
			else{
				$('.combine #sale').prop('disabled',true);
			}
		});
		$(document).on('click','.tablecombine .chtable',function(){
			if($(this).prop('id')=='notempty'){
				if($('.tablecombine .chtable#check').length>0){
				}
				else{
					$(this).prop('id','check');
					$(this).find('#checkbox').html('✔');
					var chnum=Number($('.tablecombine #check').length)+Number($('.tablecombine #oncheck').length);
					if(Number(chnum)>=2){
						$('.tablecombine #sale').prop('disabled',false);
					}
					else{
						$('.tablecombine #sale').prop('disabled',true);
					}
				}
			}
			else if($(this).prop('id')=='check'){
				$(this).prop('id','notempty');
				$(this).find('#checkbox').html('');
				var chnum=Number($('.tablecombine #check').length)+Number($('.tablecombine #oncheck').length);
				if(Number(chnum)>=2){
					$('.tablecombine #sale').prop('disabled',false);
				}
				else{
					$('.tablecombine #sale').prop('disabled',true);
				}
			}
			else if($(this).prop('id')=='oncheck'){
				$(this).prop('id','');
				$(this).find('#checkbox').html('');
				var chnum=Number($('.tablecombine #check').length)+Number($('.tablecombine #oncheck').length);
				if(Number(chnum)>=2){
					$('.tablecombine #sale').prop('disabled',false);
				}
				else{
					$('.tablecombine #sale').prop('disabled',true);
				}
			}
			else if($(this).prop('id')==''){
				$(this).prop('id','oncheck');
				$(this).find('#checkbox').html('✔');
				var chnum=Number($('.tablecombine #check').length)+Number($('.tablecombine #oncheck').length);
				if(Number(chnum)>=2){
					$('.tablecombine #sale').prop('disabled',false);
				}
				else{
					$('.tablecombine #sale').prop('disabled',true);
				}
			}
			console.log(chnum);
		});
		$(document).on('click','.tablesplit .chtable#notempty',function(){
			$.ajax({
				url:'./gettablelist.ajax.php',
				method:'post',
				data:{'bizdate':'<?php echo $machinedata["basic"]["bizdate"]; ?>','zcounter':'<?php echo $machinedata["basic"]["zcounter"]; ?>','tablenum':$(this).find('#tablenumber input[name="tabnum"]').val()},
				dataType:'html',
				success:function(d){
					if(d=="目前點選之桌號已併桌，無法進行拆桌動作。"){
						$('.checktablesplit #text').html(d);
						$('.checktablesplit #sale').prop('disabled',true);
						checktablesplit.dialog('open');
					}
					else{
						$('.checktablesplit #text').html(d);
						$('.checktablesplit #sale').prop('disabled',false);
						checktablesplit.dialog('open');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$(document).on('click','.combine .chtable#check',function(){
			$(this).prop('id','notempty');
			$(this).find('#checkbox').html('');
			if($('.combine #check').length>=2){
				$('.combine #sale').prop('disabled',false);
			}
			else{
				$('.combine #sale').prop('disabled',true);
			}
		});
		$('.combine #sale').click(function(){
			var tt='';
			var tconsecnumber='';
			var tabnum=$('.combine #check:eq(0) #tablenumber input[name="tabnum"]').val();
			for(var i=0;i<$('.combine #check').length;i++){
				if(tt==''){
					tt=$('.combine #check:eq('+i+') #tablenumber input[name="tabnum"]').val();
					tconsecnumber=$('.combine #check:eq('+i+') #tablenumber input[name="consecnumber"]').val();
				}
				else{
					tt=tt+','+$('.combine #check:eq('+i+') #tablenumber input[name="tabnum"]').val();
					tconsecnumber=tconsecnumber+','+$('.combine #check:eq('+i+') #tablenumber input[name="consecnumber"]').val();
				}
			}
			$('.checkcombine #text').append('合併結帳之桌號：'+tt);
			$('.checkcombine #text').append('<input type="hidden" name="consecnumbers" value="'+tconsecnumber+'">');
			$('.checkcombine #text').append('<input type="hidden" name="tabnum" value="'+tabnum+'">');
			$('.checkcombine #text').append('<input type="hidden" name="tabini" value="'+tt+'">');
			checkcombine.dialog('open');
		});
		$('.tablecombine #sale').click(function(){
			var tt='';
			if($('.tablecombine #check').length>0){
				var consecnumber=$('.tablecombine #check:eq(0) #tablenumber input[name="consecnumber"]').val();
			}
			else{
				var consecnumber='';
			}
			for(var i=0;i<$('.tablecombine .chtable').length;i++){
				if($('.tablecombine .chtable:eq('+i+')').prop('id')=='oncheck'||$('.tablecombine .chtable:eq('+i+')').prop('id')=='check'){
					if(tt==''){
						tt=$('.tablecombine .chtable:eq('+i+') #tablenumber input[name="tabnum"]').val();
						tconsecnumber=$('.tablecombine .chtable:eq('+i+') #tablenumber input[name="consecnumber"]').val();
					}
					else{
						if($('.tablecombine .chtable:eq('+i+') #tablenumber input[name="consecnumber"]').val()!=''){
							tt=$('.tablecombine .chtable:eq('+i+') #tablenumber input[name="tabnum"]').val()+','+tt;
							tconsecnumber=$('.tablecombine .chtable:eq('+i+') #tablenumber input[name="consecnumber"]').val()+','+tconsecnumber;
						}
						else{
							tt=tt+','+$('.tablecombine .chtable:eq('+i+') #tablenumber input[name="tabnum"]').val();
							tconsecnumber=tconsecnumber+','+$('.tablecombine .chtable:eq('+i+') #tablenumber input[name="consecnumber"]').val();
						}
					}
				}
				else{
				}
			}
			$('.checktablecombine #text').append('欲併桌之桌號：'+tt);
			$('.checktablecombine #text').append('<input type="hidden" name="consecnumbers" value="'+consecnumber+'">');
			$('.checktablecombine #text').append('<input type="hidden" name="tabnum" value="'+tt+'">');
			checktablecombine.dialog('open');
		});
		$('.checkcombine #cancel').click(function(){
			$('.checkcombine #text').html('');
			checkcombine.dialog('close');
		});
		$('.checktablecombine #cancel').click(function(){
			$('.checktablecombine #text').html('');
			checktablecombine.dialog('close');
		});
		$('.checktablesplit #cancel').click(function(){
			$('.checktablesplit #text').html('');
			$('.checktablesplit #sale').prop('disabled',false);
			checktablesplit.dialog('close');
		});
		$('.checkcombine #sale').click(function(){
			$('.checkcombine #sale').prop('disabled',true);
			$.ajax({
				url:'./combinelist.php',
				method:'post',
				data:{'usercode':'<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>','username':'<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>','consecnumbers':$('.checkcombine #text input[name="consecnumbers"]').val(),'tabnum':$('.checkcombine #text input[name="tabnum"]').val(),'tabini':$('.checkcombine #text input[name="tabini"]').val(),'machine':'<?php if(isset($_GET["submachine"]))echo $_GET["submachine"];else if(isset($_GET["machine"]))echo $_GET["machine"];else echo ""; ?>'},
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'./lib/js/print.php',
							method:'post',
							data:{'html':'control combinelist.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					console.log(d);
					var t=d.split(',');
					location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&tabnum&bizdate='+t[0]+'&consecnumber='+t[1];
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$('.checktablecombine #sale').click(function(){
			$('.checktablecombine #sale').prop('disabled',true);
			$.ajax({
				url:'./combinetablelist.php',
				method:'post',
				data:{'machinetype':'<?php if(isset($_GET["machinetype"]))echo $_GET["machinetype"];else echo "m1"; ?>','usercode':'<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>','username':'<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>','consecnumber':$('.checktablecombine #text input[name="consecnumbers"]').val(),'tabnum':$('.checktablecombine #text input[name="tabnum"]').val()},
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'./lib/js/print.php',
							method:'post',
							data:{'html':'control combinetablelist.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					console.log(d);
					var t=d.split(',');
					location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&tabnum&bizdate='+t[0]+'&consecnumber='+t[1];
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$('.checktablesplit #sale').click(function(){
			$('.checktablesplit #sale').prop('disabled',true);//拆單－新增帳單
			$.ajax({
				url:'./splittable.php',
				method:'post',
				data:{'machinetype':'<?php if(isset($_GET["machinetype"]))echo $_GET["machinetype"];else echo "m1"; ?>','usercode':'<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>','username':'<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>','tabnum':$('.checktablesplit #text input[name="lastnum"]').val()},
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'./lib/js/print.php',
							method:'post',
							data:{'html':'control splittable.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					//console.log(d);
					var t=d.split(',');
					location.href='./order.php?<?php if(isset($_GET["submachine"]))echo "submachine=".$_GET["submachine"]."&";else if(isset($_GET["machine"]))echo "machine=".$_GET["machine"]."&";else; ?>usercode=<?php if(isset($_GET["usercode"]))echo $_GET["usercode"]; ?>&username=<?php if(isset($_GET["username"]))echo $_GET["username"]; ?>&listtype=1&tabnum='+t[2]+'&bizdate='+t[0]+'&consecnumber='+t[1];
				},
				error:function(e){
					console.log(e);
				}
			});
		});
		$('.combine #return').click(function(){
			combine.dialog('close');
		});
		$('.tablecombine #return').click(function(){
			tablecombine.dialog('close');
		});
		$('.tablesplit #return').click(function(){
			tablesplit.dialog('close');
		});
		$('.funbox #return').click(function(){
			funbox.dialog('close');
		});
		$('.setchange .numbox').on('click','input',function(){
			var index=$('.setchange .numbox input').index(this);
			if($('.setchange input[name="view"]').val()==''&&$('.setchange .numbox input:eq('+index+')').val()=='.'){
				$('.setchange input[name="view"]').val('0.');
			}
			else if($('.setchange input[name="view"]').val()=='0'){
				$('.setchange input[name="view"]').val($('.setchange .numbox input:eq('+index+')').val());
			}
			else{
				$('.setchange input[name="view"]').val($('.setchange input[name="view"]').val()+$('.setchange .numbox input:eq('+index+')').val());
			}
		});
		$('.setchange .numbox').on('click','button',function(){
			$('.setchange input[name="view"]').val('0');
		});
		$('.setchange').on('click','#settingchange',function(){
			setchange.dialog('close');
		});
	});
	</script>
</head>
<body>
	<?php
	if(file_exists('./syspram/buttons-'.$initsetting['init']['firlan'].'.ini')){
		$buttons1=parse_ini_file('./syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
	}
	else{
		$buttons1='-1';
	}
	if(file_exists('./syspram/buttons-'.$initsetting['init']['seclan'].'.ini')){
		$buttons2=parse_ini_file('./syspram/buttons-'.$initsetting['init']['seclan'].'.ini',true);
	}
	else{
		$buttons2='-1';
	}
	if(file_exists('./syspram/interface-'.$initsetting['init']['firlan'].'.ini')){
		$interface1=parse_ini_file('./syspram/interface-'.$initsetting['init']['firlan'].'.ini',true);
	}
	else{
		$interface1='-1';
	}
	if(file_exists('./syspram/interface-'.$initsetting['init']['seclan'].'.ini')){
		$interface2=parse_ini_file('./syspram/interface-'.$initsetting['init']['seclan'].'.ini',true);
	}
	else{
		$interface2='-1';
	}
	date_default_timezone_set('Asia/Taipei');
	include_once '../tool/dbTool.inc.php';
	if(file_exists('../database/sale/SALES_'.substr($machinedata['basic']['bizdate'],0,6).'.db')){
		/*$conn=sqlconnect('../database/sale','SALES_'.substr($machinedata['basic']['bizdate'],0,6).'.db','','','','sqlite');
		$sql='SELECT * FROM tempCST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE="'.$machinedata['basic']['bizdate'].'" AND ZCOUNTER="'.$machinedata['basic']['zcounter'].'" ORDER BY CREATEDATETIME DESC';
		$list=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
		$ttt=array();
		foreach($list as $l){
			if($l['REMARKS']=='1'){
				if(preg_match('/,/',$l['TABLENUMBER'])){
					$temp=preg_split('/,/',$l['TABLENUMBER']);
					foreach($temp as $t){
						$st=preg_split('/-/',$t);
						for($stl=1;$stl<sizeof($st)-1;$stl++){
							$st[0]=$st[0].'-'.$st[$stl];
						}
						if(isset($ttt[$st[0]])){
							$ttt[$st[0]]['split']=1;
						}
						else{
							$ttt[$st[0]]['split']=0;
						}
						$ttt[$st[0]]['inittablenum']=$t;
						$ttt[$st[0]]['consecnumber']=$l['CONSECNUMBER'];
						$ttt[$st[0]]['bizdate']=$l['BIZDATE'];
						$ttt[$st[0]]['amt']=$l['SALESTTLAMT'];
						$ttt[$st[0]]['persons']=intval($l['TAX6'])+intval($l['TAX7'])+intval($l['TAX8']);
						$ttt[$st[0]]['createdatetime']=$l['CREATEDATETIME'];
					}
				}
				else{
					$st=preg_split('/-/',$l['TABLENUMBER']);
					for($stl=1;$stl<sizeof($st)-1;$stl++){
						$st[0]=$st[0].'-'.$st[$stl];
					}
					if(isset($ttt[$st[0]])){
						$ttt[$st[0]]['split']=1;
					}
					else{
						$ttt[$st[0]]['split']=0;
					}
					$ttt[$st[0]]['inittablenum']=$l['TABLENUMBER'];
					$ttt[$st[0]]['consecnumber']=$l['CONSECNUMBER'];
					$ttt[$st[0]]['bizdate']=$l['BIZDATE'];
					$ttt[$st[0]]['amt']=$l['SALESTTLAMT'];
					$ttt[$st[0]]['persons']=intval($l['TAX6'])+intval($l['TAX7'])+intval($l['TAX8']);
					$ttt[$st[0]]['createdatetime']=$l['CREATEDATETIME'];
				}
			}
			else{
			}
		}*/
	}
	else{
	}
	?>
	<div class='outsidelist' title='外帶帳單'>
		<button id='newlist'>新增外帶單</button>
		<?php
		$dir='./table/outside';
		$filelist=scandir($dir,1);
		foreach($filelist as $fl){
			if(strstr($fl,$machinedata['basic']['bizdate'].';'.$machinedata['basic']['zcounter'])){
				if(isset($count)){
					$count++;
				}
				else{
					$count=1;
				}
			}
			else{
				if(isset($count)){
				}
				else{
					$count=0;
				}
			}
		}
		/*if(isset($list)){
			for($i=0;$i<sizeof($list);$i++){
				if($list[$i]['REMARKS']=='2'){
					$count++;
					echo '<button><div id="consecnumber">'.$list[$i]['CONSECNUMBER'].'</div><div id="bizdate">'.$list[$i]['BIZDATE'].'</div></button>';
				}
				else{
				}
			}
		}
		else{
		}*/
		?>
	</div>
	<?php
	$nowtime=date_create(date('YmdHis'));
	?>
	<div class='inittable'>
		<!-- <table style='width:100%;height:100%;text-align:center;'> -->
		<div class='tablemap' style='width:calc(500% / 6);height:100%;float:left;margin:0;padding:0;'>
			<?php
			$totaltable=0;
			$ordertable=0;
			for($i=1;$i<=(intval($tb['TA']['row'])*intval($tb['TA']['col']));$i++){
				echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
				if($tb['T'.$i]['tablename']==''){
				}
				else{
					$splitnum=0;
					if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
					?>
						<?php 
						$tablenumname='';
						$t='';
						$time='';
						$bizdate='';
						$consecnumber='';
						$saleamt='';
						$person='';
						$createdatetime='';
						$dir=('./table');
						$filelist=scandir($dir);
						$maps=0;
						foreach($filelist as $fl){
							//echo $fl;
							if(file_exists('./table/'.$machinedata['basic']['bizdate'].';'.$machinedata['basic']['zcounter'].';'.$tb['T'.$i]['tablename'].'.ini')&&$maps==0){
								$maps=-1;
								$tabledata=parse_ini_file('./table/'.$machinedata['basic']['bizdate'].';'.$machinedata['basic']['zcounter'].';'.$tb['T'.$i]['tablename'].'.ini',true);
								foreach($tabledata as $tdindex=>$td){
									if($td['bizdate']==$machinedata['basic']['bizdate']&&$td['zcounter']==$machinedata['basic']['zcounter']&&$td['consecnumber']!=""){
										$splitnum++;
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
											$tablenumname=$tdindex;
											$bizdate=$td['bizdate'];
											$consecnumber=$td['consecnumber'];
											$saleamt=$td['saleamt'];
											$person=$td['person'];
											$createdatetime=$td['createdatetime'];
										}
										else{
										}
									}
									else{
									}
								}
							}
							else{
							}
							if(strstr($fl,$machinedata['basic']['bizdate'].';'.$machinedata['basic']['zcounter'].';'.$tb['T'.$i]['tablename'].'-')&&$fl!=$machinedata['basic']['bizdate'].';'.$machinedata['basic']['zcounter'].';'.$tb['T'.$i]['tablename'].'.ini'&&$maps==0){
								$maps=1;
								$tabledata=parse_ini_file('./table/'.$fl,true);
								foreach($tabledata as $tdindex=>$td){
									if($td['bizdate']==$machinedata['basic']['bizdate']&&$td['zcounter']==$machinedata['basic']['zcounter']&&$td['consecnumber']!=""){
										$splitnum++;
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
											$tablenumname=$tdindex;
											$bizdate=$td['bizdate'];
											$consecnumber=$td['consecnumber'];
											$saleamt=$td['saleamt'];
											$person=$td['person'];
											$createdatetime=$td['createdatetime'];
										}
										else{
										}
									}
									else{
									}
								}
							}
							else if(strstr($fl,$machinedata['basic']['bizdate'].';'.$machinedata['basic']['zcounter'].';'.$tb['T'.$i]['tablename'])&&$fl!=$machinedata['basic']['bizdate'].';'.$machinedata['basic']['zcounter'].';'.$tb['T'.$i]['tablename'].'.ini'&&($maps==1||$maps==-1)){
								$maps=1;
								$splitnum++;
								/*foreach($tabledata as $tdindex=>$td){
									if($td['bizdate']==$machinedata['basic']['bizdate']&&$td['zcounter']==$machinedata['basic']['zcounter']&&$td['consecnumber']!=""){
										$splitnum++;
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
											$tablenumname=$tdindex;
											$bizdate=$td['bizdate'];
											$consecnumber=$td['consecnumber'];
											$saleamt=$td['saleamt'];
											$person=$td['person'];
											$createdatetime=$td['createdatetime'];
										}
										else{
										}
									}
									else{
									}
								}*/
							}
							else{
								if(($maps==1)){
									break;
								}
								else{
								}
							}
						}
						/*if(file_exists('./table/'.$tb['T'.$i]['tablename'].'.ini')){
						//if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
							$tabledata=parse_ini_file('./table/'.$tb['T'.$i]['tablename'].'.ini',true);
							foreach($tabledata as $td){
								if($td['bizdate']==$machinedata['basic']['bizdate']&&$td['zcounter']==$machinedata['basic']['zcounter']&&$td['consecnumber']!=""){
									$splitnum++;
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
										$bizdate=$td['bizdate'];
										$consecnumber=$td['consecnumber'];
										$saleamt=$td['saleamt'];
										$person=$td['person'];
										$createdatetime=$td['createdatetime'];
									}
									else{
									}
								}
								else{
								}
							}
							//$mins=intval(substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],8,2))*60+intval(substr($ttt[$tb['T'.$i]['tablename']]['createdatetime'],10,2));
							/*$maxtime=date_create(date('YmdHis',strtotime($ttt[$tb['T'.$i]['tablename']]['createdatetime'].' +'.$initsetting['init']['maxtime'].' minute')));
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
							}*/
						/*}
						else{
							$t='';
							$time='';
						}*/
						?>
						<button class='table' <?php 
						if(strlen($t)!=0&&strlen($time)!=0){
						//if(isset($ttt[$tb['T'.$i]['tablename']]['createdatetime'])){
							echo 'id="comput notempty "';
							$ordertable++;
							$totaltable++;
						}
						else{
							echo 'id="comput"';
							$totaltable++;
						}
						if($t==1||$t=='');
						else if($t==2)echo 'style="background-color:#73d7ec;"';
						else if($t==-2)echo 'style="background-color:#e7f12c;"';
						else echo 'style="background-color:#00d941;"'; 
						?> <?php /*if(isset($ttt[$tb['T'.$i]['tablename']]['split'])&&$ttt[$tb['T'.$i]['tablename']]['split']==1)*/if(isset($splitnum)&&intval($splitnum)>1)echo 'name="split"';echo $splitnum; ?>>
							<div id='tablenumber'><?php echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php if(isset($tablenumname)&&$tablenumname!='')echo $tablenumname;else echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value='<?php echo $consecnumber; ?>'></div>
							<div id='amt'><?php echo "<span>".$saleamt."</span>"; ?></div>
							<div id='persons'><?php if(intval($person)!='')echo $person.'位'; ?></div>
							<div id='createdatetime'><span id='val'><?php echo $time; ?></span><input type='hidden' name='createdatetime' value='<?php echo substr($createdatetime,4,2).'/'.substr($createdatetime,6,2).' '.substr($createdatetime,8,2).':'.substr($createdatetime,10,2); ?>'><input type='hidden' name='bizdate' value='<?php echo $bizdate; ?>'></div>
						</button>
					<?php
					}
					else{
					}
				}
				echo '</div>';
			}
			?>
		</div>
		<div class='funcmap' style='width:calc((100% - 2em) / 6);height:calc(100% - 1em);float:left;margin:0;padding:0;position: absolute;right: 1em;bottom: .5em;'>
			<div style='width:100%;'><input type='text' style='width:100%;height:30px;font-size:20px;border:0;text-align:right;font-family: Consolas,Microsoft JhengHei,sans-serif;' id='time' value='<?php echo '尚有'.(intval($totaltable)-intval($ordertable)).$tb['TA']['unit'].' '.date('H:i:s'); ?>' readonly></div>
			<?php
			for($i=0;$i<9;$i++){
				if($i==0){
					?>
					<div style='width:100%;height:calc(100% / 6 - 10px);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button class='table' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?>>
							<div id='QTYlabel'>外帶<input type='hidden' name='tabnum' value='outside'></div>
							<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
						</button>
					</div>
					<?php
				}
				else if($i==1){
					if(isset($initsetting['init']['controlhinttime'])&&$initsetting['init']['controlhinttime']=='1'){
					?>
					<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 70%;'>						<button id='tablehint' style='background-color:#898989;color:#ffffff;'><div>時段提示</div></button>
					</div>
					<?php
					}
					else{
					}
				}
				else if($i==2){
					?>
					<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 50%;'>						<!-- <button id='AE' style='background-color:#898989;color:#ffffff;' <?php if(isset($initsetting['init']['moneycost'])&&$initsetting['init']['moneycost']==1&&$timeini['time']['isclose']!='0'&&!isset($_GET['submachine']));else echo 'disabled'; ?>><div>支出費用</div></button> -->
						<button id='cashdrawer' style='background-color:#898989;color:#ffffff;' <?php if(!isset($_GET['submachine']));else echo 'disabled'; ?>><div>開錢櫃</div></button>
					</div>
					<?php
				}
				else if($i==3){
					?>
					<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 40%;'>						<button id='tablesplit' style='background-color:#898989;color:#ffffff;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><div>拆桌</div></button>
					</div>
					<?php
				}
				else if($i==4){
					?>
					<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 30%;'>
						<button id='combine' style='background-color:#898989;color:#ffffff;' <?php if($timeini['time']['isclose']!='0'&&!isset($_GET['submachine']));else echo 'disabled'; ?>><div>合併結帳</div></button>
					</div>
					<?php
				}
				else if($i==5){
					?>
					<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 20%;'>
						<button id='tablecombine' style='background-color:#898989;color:#ffffff;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><div>併桌</div></button>
						<!-- <button id='cashdrawer'><div>錢櫃</div></button> -->
					</div>
					<?php
				}
				else if($i==6){
					?>
					<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 10%;'>
						<button id='changetable' style='background-color:#898989;color:#ffffff;' <?php if($timeini['time']['isclose']!='0');else echo 'disabled'; ?>><div>換桌</div></button>
					</div>
					<?php
				}
				else if($i==7){
					?>
					<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 0;'>
						<button id="exitsys" style='background-color:#898989;color:#ffffff;'>
							<div>離開系統</div>
						</button>
					</div>
					<?php
				}
				else if($i==8){
					?>
					<div style='width:100%;height:10%;float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: absolute;right: 0;bottom: 60%;'>
						<button id='funbox' style='background-color:#898989;color:#ffffff;' <?php if(isset($_GET['submachine']))echo 'disabled'; ?>><div>功能區</div></button>
					</div>
					<?php
				}
				else{
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
	if(isset($initsetting['init']['moneycost'])&&$initsetting['init']['moneycost']==1&&$timeini['time']['isclose']!='0'&&!isset($_GET['submachine'])){
	?>
		<div class='AE' title='支出費用' style='overflow:hidden;'>
			<div style='width:100%;float:left;margin:1px 0;'>
				<label style='font-size:3vw;'><input type='radio' name='aetype' value='1' style='-ms-transform: scale(1.7);-webkit-transform: scale(1.7);transform: scale(1.7);' checked>支出費用</label>
			</div>
			<div style='width:100%;float:left;margin:1px 0;'>
				<label style='font-size:2vw;'>營業日期：</label><input type='text' id='bizdate' style='float:right;font-size:2vw;width:calc(600px - 8.4px - 32px - 10vw);border:1px solid #898989;'>
			</div>
			<div style='width:100%;float:left;margin:1px 0;'>
				<label style='font-size:2vw;'>當日班別：</label><select name='zcounter' style='float:right;font-size:3vw;width:calc(600px - 8.4px - 30px - 10vw);border:1px solid #898989;'><?php if(file_exists('../database/sale/SALES_'.substr($machinedata['basic']['bizdate'],0,6).'.db')){$conn=sqlconnect('../database/sale','SALES_'.substr($machinedata['basic']['bizdate'],0,6).'.db','','','','sqlite');$sql='SELECT DISTINCT ZCOUNTER FROM CST011 WHERE BIZDATE="'.$machinedata['basic']['bizdate'].'"';$options=sqlquery($conn,$sql,'sqlite');sqlclose($conn,'sqlite');for($i=0;$i<sizeof($options);$i++)echo '<option value="'.$options[$i]['ZCOUNTER'].'">'.$options[$i]['ZCOUNTER'].'</option>';}else{} ?></select>
			</div>
			<div style='width:100%;float:left;margin:1px 0;'>
				<label style='font-size:2vw;'>支出科目：</label><input type='text' id='moneytype' style='float:right;font-size:3vw;width:calc(600px - 8.4px - 32px - 10vw);border:1px solid #898989;'>
				<input type='hidden' name='moneytype'>
			</div>
			<div style='width:100%;float:left;margin:1px 0;'>
				<label style='font-size:2vw;'>項目名稱：</label><input type='text' name='moneysubtype' style='float:right;font-size:3vw;width:calc(600px - 8.4px - 32px - 10vw);border:1px solid #898989;'>
			</div>
			<div style='width:100%;float:left;margin:1px 0;'>
				<label style='font-size:2vw;'>支出金額：</label><input type='text' name='money' style='float:right;font-size:3vw;width:calc(600px - 8.4px - 32px - 10vw);text-align:right;border:1px solid #898989;' value='0' readonly>
			</div>
			<div style='width:100%;float:left;margin:1px 0;'>
				<label style='font-size:2vw;'>憑證：</label><label style='font-size:2vw;'><input type='radio' name='radius' value='1' style='-ms-transform: scale(1.7);-webkit-transform: scale(1.7);transform: scale(1.7);'>有</label>、<label style='font-size:2vw;'><input type='radio' name='radius' value='0' style='-ms-transform: scale(1.7);-webkit-transform: scale(1.7);transform: scale(1.7);' checked>無</label>
			</div>
			<div style='width:100%;float:left;margin:1px 0;'>
				<label style='font-size:2vw;'>備註</label>
				<textarea name='remarks' rows='8' style='width:calc(100% - 6px);resize:none;font-size:2vw;border:1px solid #898989;'></textarea>
			</div>
			<button id='cancel' style='float:right;font-size:3vw;margin:1px;'><div>取消</div></button>
			<button id='send' style='float:right;font-size:3vw;margin:1px;'><div>送出</div></button>
		</div>
		<div class='selecttype' title='支出科目' style='overflow:hidden;'>
			<div id='left' style='width:100px;height:100%;position: absolute;top:0;left:0;'>
				<img src='../database/img/left.png' style='width:70px;height:70px;margin:27.5px 15px;visibility:hidden;'>
			</div>
			<div style='width:calc(100% - 200px);height:100%;position: absolute;top:0;left:100px;overflow-x:auto;overflow-y:hidden;'>
				<div style='min-width:100%;max-height:100%;overflow:hidden;'>
					<?php
					$type=parse_ini_file('../database/type.ini',true);
					for($i=0;$i<sizeof($type['type']);$i++){
						echo '<button id="buttype" style="width:98px;height:123px;margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>'.$type['type'][$i].'</div><input type="hidden" id="typevalue" value="'.$i.'"></button>';
					}
					?>
				</div>
			</div>
			<div id='right' style='width:100px;height:100%;position: absolute;top:0;right:0;'>
				<img src='../database/img/right.png' style='width:70px;height:70px;margin:27.5px 15px;visibility:hidden;'>
			</div>
		</div>
		<div class='setmoney' title='金額' style='overflow:hidden;'>
			<input type='text' style='width:100%;font-size:2vw;text-align:right;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin-bottom:1px;border:1px solid #898989;' name='viewnumber' value='0' readonly>
			<div style='width:100%;height:calc(100% - 37px);margin-top:1px;'>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>7</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>8</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>9</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>4</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>5</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>6</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>1</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>2</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>3</div></button>
				<button id='numbut' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>0</div></button>
				<button id='reset' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>AC</div></button>
				<button id='send' style="width:calc(100% / 3 - 2px);height:calc(100% / 4 - 2px);margin:1px;float:left;background-color:#ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border:1px solid #898989;border-radius:5px;"><div>確認</div></button>
			</div>
		</div>
	<?php
	}
	else{
	}
	?>
	<div class='changetable' title='換桌'>
		<input type='hidden' id='c1' value='empty'>
		<input type='hidden' id='c1consecnumber'>
		<input type='hidden' id='c2' value='empty'>
		<input type='hidden' id='c2consecnumber'>
		<div class='tablemap' style='width:calc(500% / 6);height:100%;float:left;margin:0;padding:0;'>
		<?php
		for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
			echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
			if($tb['T'.$i]['tablename']==''){
			}
			else{
				if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
				?>
					<button class='chtable'>
						<div id='tablenumber'><?php echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value=''></div>
					</button>
				<?php
				}
				else{
				}
			}
			echo '</div>';
		}
		?>
		</div>
		<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
			<?php
			for($i=0;$i<6;$i++){
				if($i==0){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
							<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
							<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
						</button>
					</div>
					<?php
				}
				else if($i==4){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button id='change' style='text-align:center;background-color:#898989;color:#ffffff;' disabled>
							<span id='c1t' style='font-size:2vw;'></span>
							<input type='hidden' name='c1t'>
							<span style='margin:0 5px;'>換</span>
							<span id='c2t' style='font-size:2vw;'></span>
							<input type='hidden' name='c2t'>
						</button>
					</div>
					<?php
				}
				else if($i==5){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button id="return" style='background-color:#898989;color:#ffffff;'>
							<div>返回</div>
						</button>
					</div>
					<?php
				}
				else{
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<div class='exitsys' style='text-align:center;' title='系統訊息'>
		<div id="name1">離開系統?</div>
		<br>
		<button class="yes" value="確認" style='width:105px;height:70px;'><div id='name1'>確認</div></button>
		<button class="no" value="取消" style='width:105px;height:70px;'><div id='name1'>取消</div></button>
	</div>
	<div class='funbox' title='功能區'>
		<?php
		if(!isset($_GET['submachine'])){
		?>
		<button id='open' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isopen'])&&$timeini['time']['isopen']=='1')echo '';else echo 'disabled'; ?>><div style='font-weight:bold;'>開班</div></button>
		<button id='close' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isclose'])&&$timeini['time']['isclose']=='1')echo '';else echo 'disabled'; ?>><div style='font-weight:bold;'>交班</div></button>
		<!-- <button id='gotable' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isclose'])&&$timeini['time']['isclose']=='1')echo '';else echo 'disabled'; ?>><div style='font-weight:bold;'>帶位</div></button>
		<button id='cleartable' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isclose'])&&$timeini['time']['isclose']=='1')echo '';else echo 'disabled'; ?>><div style='font-weight:bold;'>清桌</div></button>
		<button id='salelist' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>帳單瀏覽</div></button>
		<button id='voidsale' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>帳單作廢</div></button>
		<button id='notyet' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>未結帳單</div></button> -->
		<button id='AE' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;' <?php if(isset($timeini['time']['isclose'])&&$timeini['time']['isclose']=='1')echo '';else echo 'disabled'; ?>><div style='font-weight:bold;'>支出費用</div></button>
		<!-- <button id='punch' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>員工打卡</div></button>
		<button id='logout' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>切換人員</div></button>
		<button id='updatemenu' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>更新菜單</div></button> -->
		<?php
		}
		else{
		?>
		<!-- <button id='salelist' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>帳單瀏覽</div></button>
		<button id='voidsale' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>帳單作廢</div></button>
		<button id='notyet' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>未結帳單</div></button>
		<button id='punch' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>員工打卡</div></button>
		<button id='logout' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>切換人員</div></button>
		<button id='updatemenu' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>更新菜單</div></button> -->
		<?php
		}
		?>
		<button id='return' style='width:calc(25% - 2px);height:70px;margin:1px;float:left;'><div style='font-weight:bold;'>返回</div></button>
	</div>
	<div class='combine' title='合併結帳'>
		<div class='tablemap' style='width:calc(500% / 6);height:100%;float:left;margin:0;padding:0;'>
		<?php
		for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
			echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
			if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
			?>
				<button class='chtable'>
					<div id='tablenumber'><?php echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value=''></div>
					<div id='checkbox'></div>
				</button>
			<?php
			}
			else{
			}
			echo '</div>';
		}
		?>
		</div>
		<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
			<?php
			for($i=0;$i<6;$i++){
				if($i==0){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
							<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
							<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
						</button>
					</div>
					<?php
				}
				else if($i==4){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button id='sale' style='text-align:center;background-color:#898989;color:#ffffff;' disabled>
							<div style='margin:0 5px;'>合併結帳</div>
						</button>
					</div>
					<?php
				}
				else if($i==5){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button id="return" style='background-color:#898989;color:#ffffff;'>
							<div>返回</div>
						</button>
					</div>
					<?php
				}
				else{
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<div class='tablecombine' title='併桌'>
		<div class='tablemap' style='width:calc(500% / 6);height:100%;float:left;margin:0;padding:0;'>
		<?php
		for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
			echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
			if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
			?>
				<button class='chtable'>
					<div id='tablenumber'><?php echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value=''></div>
					<div id='checkbox'></div>
				</button>
			<?php
			}
			else{
			}
			echo '</div>';
		}
		?>
		</div>
		<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
			<?php
			for($i=0;$i<6;$i++){
				if($i==0){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
							<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
							<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
						</button>
					</div>
					<?php
				}
				else if($i==4){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button id='sale' style='text-align:center;background-color:#898989;color:#ffffff;' disabled>
							<div style='margin:0 5px;'>併桌</div>
						</button>
					</div>
					<?php
				}
				else if($i==5){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button id="return" style='background-color:#898989;color:#ffffff;'>
							<div>返回</div>
						</button>
					</div>
					<?php
				}
				else{
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<div class='tablesplit' title='拆桌'>
		<div class='tablemap' style='width:calc(500% / 6);height:100%;float:left;margin:0;padding:0;'>
		<?php
		for($i=1;$i<=(intval($tb['TA']['col'])*intval($tb['TA']['row']));$i++){
			echo '<div style="width:calc(100% / '.$tb['TA']['col'].');height:calc(100% / '.$tb['TA']['row'].');float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">';
			if(isset($tb['T'.$i])&&$tb['T'.$i]['tablename']!=""){
			?>
				<button class='chtable'>
					<div id='tablenumber'><?php echo $tb['T'.$i]['tablename']; ?><input type='hidden' name='tabnum' value='<?php echo $tb['T'.$i]['tablename']; ?>'><input type='hidden' name='consecnumber' value=''></div>
					<div id='checkbox'></div>
				</button>
			<?php
			}
			else{
			}
			echo '</div>';
		}
		?>
		</div>
		<div class='funcmap' style='width:calc(100% / 6);height:100%;float:left;margin:0;padding:0;'>
			<?php
			for($i=0;$i<6;$i++){
				if($i==0){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button class='outchtable' <?php if(intval($count)>0)echo 'id="notempty"';else echo ''; ?> disabled>
							<div id='tablenumber'>外帶<input type='hidden' name='tabnum' value='outside'></div>
							<div id='QTY'><?php if(intval($count)>0)echo '尚有'.$count.'單'; ?></div>
						</button>
					</div>
					<?php
				}
				else if($i==5){
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
						<button id="return" style='background-color:#898989;color:#ffffff;'>
							<div>返回</div>
						</button>
					</div>
					<?php
				}
				else{
					?>
					<div style='width:100%;height:calc(100% / 6);float:left;padding:1px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<div class='checktablecombine' title='併桌確認視窗'>
		<div id='text' style='width:100%;height:calc(100% - 48px);'>
		</div>
		<button id='cancel' style='font-size:30px;margin:1px;float:right;'><div>取消</div></button>
		<button id='sale' style='font-size:30px;margin:1px;float:right;'><div>確認併桌</div></button>
	</div>
	<div class='checkcombine' title='合併結帳確認視窗'>
		<div id='text' style='width:100%;height:calc(100% - 48px);'>
		</div>
		<button id='cancel' style='font-size:30px;margin:1px;float:right;'><div>取消</div></button>
		<button id='sale' style='font-size:30px;margin:1px;float:right;'><div>確認合併</div></button>
	</div>
	<div class='checktablesplit' title='拆桌確認視窗'>
		<div id='text' style='width:100%;height:calc(100% - 48px);'>
		</div>
		<button id='cancel' style='font-size:30px;margin:1px;float:right;'><div>取消</div></button>
		<button id='sale' style='font-size:30px;margin:1px;float:right;'><div>確認拆桌</div></button>
	</div>
	<div class='splittablelist' title='拆桌帳單'>
	</div>
	<?php
	if(isset($initsetting['init']['controlhinttime'])&&$initsetting['init']['controlhinttime']=='1'){
	?>
		<div class='tablehint' title='時段提示'>
			<div id='timelist' style='width:100%;height:calc(85% - 1px);max-height:calc(85% - 1px);float:left;margin:0 0 1px 0;padding:0;overflow-y:auto;'>
			</div>
			<div style='width:100%;height:calc(15% - 1px);float:left;margin:1px 0 0 0;padding:0;'>
				<button id='cancel' style='width:100px;height:100%;float:right;' value='取消'>取消</button>
			</div>
		</div>
	<?php
	}
	else{
	}
	?>
	<div class='setchange' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['49'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['49'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['49'];else;?>'>
		<div class='numbox' style='width:18%;height:100%;float:left;'>
			<button value='清除' style='width:100%;height:calc(100% / 13);margin:0 0 20px 0;'><?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['16']."</div>"; ?><?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['16']."</div>";else; ?></button>
			<input type='button' value='1' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='2' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='3' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='4' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='5' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='6' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='7' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='8' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='9' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='0' style='width:100%;height:calc(100% / 13);'>
			<input type='button' value='.' style='width:100%;height:calc(100% / 13);'>
		</div>
		<div style='width:calc(82% - 2px);height:100%;float:left;margin-left:2px;'>
			<div style='float:left;width:17%;'>
				<?php if($interface1!='-1')echo "<div id='name1' style='font-size:17px;'>".$interface1['name']['50']."</div>"; ?>
				<?php if($interface2!='-1')echo "<div id='name2' style='font-size:13px;'>".$interface2['name']['50']."</div>";else; ?>
			</div>
			<?php echo $initsetting['init']['frontunit']; ?><input type='text' name='view' style='width:calc(50% - 12px);margin:5px;text-align:right;float:left;' value='<?php echo $machinedata['basic']['change']; ?>' onfocus><div style='float:left;height:35px;line-height:35px;'><?php echo $initsetting['init']['unit']; ?></div>
			<button id='settingchange' style='width:105px;height:55px;margin:10px calc((100% - 105px) / 2);background-color:#D5DC75;color:#000000;'>
				<?php if($buttons1!='-1')echo "<div id='name1'>".$buttons1['name']['48']."</div>"; ?>
				<?php if($buttons2!='-1')echo "<div id='name2'>".$buttons2['name']['48']."</div>";else; ?>
			</button>
		</div>
	</div>
	<?php
	if($initsetting['init']['voidsale']=='1'){
	?>
	<div class='verpsw' style='padding:20px;overflow:hidden;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;' title='<?php if($interface1!='-1')echo $interface1['name']['verpsw'];if($interface1!='-1'&&$interface2!='-1')echo ' /'.$interface2['name']['verpsw'];else if($interface1=='-1'&&$interface2!='-1')echo $interface2['name']['verpsw'];else; ?>'>
		<table style='width:100%;height:100%;'>
			<tr>
				<td>密碼</td>
				<td><input type='password' name='verpsw'></td>
				<td><button id='send' style='width:80px;' value='驗證'>驗證</button></td>
				<td><button id='cancel' style='width:80px;' value='取消'>取消</button></td>
			</tr>
		</table>
	</div>
	<?php
	}
	else{
	}
	?>
</body>
</html>
