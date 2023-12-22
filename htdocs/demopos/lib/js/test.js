$(document).ready(function(){
	$list=$('#MemberBill #list').tabs();
	$list.tabs('option','disabled',[1]);
	$billfun=$('#MemberBill #billfun').tabs();
	spend=$('.spend').dialog({//系統訊息
		autoOpen:false,
		/*width:450,
		height:300,*/
		/*title:'系統訊息',*/
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		spend.dialog('option','width',450);
		spend.dialog('option','height',380);
	}
	else if($(window).width()==1366){
		spend.dialog('option','width',(450));
		spend.dialog('option','height',(380));
	}
	sysmeg=$('.sysmeg').dialog({//系統訊息
		autoOpen:false,
		/*width:450,
		height:300,*/
		/*title:'系統訊息',*/
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		sysmeg.dialog('option','width',450);
		sysmeg.dialog('option','height',300);
	}
	else if($(window).width()==1366){
		sysmeg.dialog('option','width',(450));
		sysmeg.dialog('option','height',(220));
	}
	sysmeg2=$('.sysmeg2').dialog({//系統訊息
		autoOpen:false,
		/*width:450,
		height:300,*/
		/*title:'系統訊息',*/
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		sysmeg2.dialog('option','width',450);
		sysmeg2.dialog('option','height',300);
	}
	else if($(window).width()==1366){
		sysmeg2.dialog('option','width',(450));
		sysmeg2.dialog('option','height',(220));
	}
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
	else if($(window).width()==1366){
		exitsys.dialog('option','width',(450));
		exitsys.dialog('option','height',(220));
	}
	setperson=$('.setperson').dialog({//人數設定
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'人數設定',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		setperson.dialog('option','width',1044);
		setperson.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		setperson.dialog('option','width',(1366*0.63-6.4-72.3));
		setperson.dialog('option','height',(768*0.9-16));
	}
	invhint=$('.invhint').dialog({//統一編號輸入錯誤視窗
		autoOpen:false,
		/*width:450,
		height:300,*/
		/*title:'系統訊息',*/
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		invhint.dialog('option','width',450);
		invhint.dialog('option','height',300);
	}
	else if($(window).width()==1366){
		invhint.dialog('option','width',(450));
		invhint.dialog('option','height',(220));
	}
	/*taste1=$('.taste1').dialog({//備註
		autoOpen:false,
		width:1044,
		height:966,
		title:'商品備註',
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});*/
	taste2=$('.taste2').dialog({//備註與加料
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'商品備註與加料',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		taste2.dialog('option','width',1044);
		taste2.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		taste2.dialog('option','width',(1366*0.63-6.4-72.3));
		taste2.dialog('option','height',(768*0.9-16));
	}
	result=$('.result').dialog({//結帳畫面
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'結帳',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		result.dialog('option','width',1044);
		result.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		result.dialog('option','width',(1366*0.63-6.4-72.3));
		result.dialog('option','height',(768*0.9-16));
	}
	itemdis=$('.itemdis').dialog({//單品促銷畫面
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'單品促銷',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		itemdis.dialog('option','width',1044);
		itemdis.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		itemdis.dialog('option','width',(1366*0.63-6.4-72.3));
		itemdis.dialog('option','height',(768*0.9-16));
	}
	change=$('.changehint').dialog({//找零提示視窗
		autoOpen:false,
		/*width:450,
		height:300,*/
		/*title:'系統訊息',*/
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			$('.changehint #timehint #time').html($('.initsetting #changeclose').val());
			tim1=setInterval(function(){timer1()},1000);//啟動倒數
		},
		close:function(){
			if($('.initsetting #controltable').val()=='1'){
				location.href='./control.php?usercode='+$('#MemberBill #tabs4 form[data-id="listform"] input[name="usercode"]').val()+'&username='+$('#MemberBill #tabs4 form[data-id="listform"] input[name="username"]').val();
			}
			else{
				location.reload();
			}
		}
	});
	if($(window).width()==1920){
		change.dialog('option','width',450);
		change.dialog('option','height',400);
	}
	else if($(window).width()==1366){
		change.dialog('option','width',(450));
		change.dialog('option','height',(400));
	}
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
				data:{'change':$('.setchange input[name="view"]').val()},
				dataType:'html',
				success:function(){
				}
			});
			$billfun.tabs('enable',0);
			$billfun.tabs('option','active',[0]);
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
	oinv=$('.oinv').dialog({//設定發票
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'設定發票',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		oinv.dialog('option','width',1044);
		oinv.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		oinv.dialog('option','width',(1366*0.63-6.4-72.3));
		oinv.dialog('option','height',(768*0.9-16));
	}
	cancelinv=$('.cancelinv').dialog({//作廢發票
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'設定發票',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		cancelinv.dialog('option','width',1044);
		cancelinv.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		cancelinv.dialog('option','width',(1366*0.63-6.4-72.3));
		cancelinv.dialog('option','height',(768*0.9-16));
	}
	salelist=$('.salelist').dialog({//瀏覽帳單
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'設定發票',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		salelist.dialog('option','width',1044);
		salelist.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		salelist.dialog('option','width',1360);
		salelist.dialog('option','height',768);
	}
	salevoid=$('.salevoid').dialog({//作廢帳單
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'設定發票',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		salevoid.dialog('option','width',1044);
		salevoid.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		salevoid.dialog('option','width',1360);
		salevoid.dialog('option','height',768);
	}
	viewtemp=$('.viewtemp').dialog({//未結帳單
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'設定發票',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		viewtemp.dialog('option','width',1044);
		viewtemp.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		viewtemp.dialog('option','width',1360);
		viewtemp.dialog('option','height',768);
	}
	tabinput=$('.tabinput').dialog({//內用桌號
		autoOpen:false,
		/*width:1044,
		height:966,*/
		/*title:'設定發票',*/
		position:{my:'right bottom',at:'right bottom',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	if($(window).width()==1920){
		tabinput.dialog('option','width',1044);
		tabinput.dialog('option','height',966);
	}
	else if($(window).width()==1366){
		tabinput.dialog('option','width',(1366*0.63-6.4-72.3));
		tabinput.dialog('option','height',(768*0.9-16));
	}
});