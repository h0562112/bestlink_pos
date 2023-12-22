$(document).ready(function(){
	var maxlogouttime=300;
	var logouttime=maxlogouttime;
	var tim1;
	function timer1(){
		$.ajax({
			url:'./lib/js/check.service.php',
			method:'post',
			async:false,
			data:{'tablenumber':$('#main .table #table #tablenumber').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				if(d=='exists'&&$('#main .function #service .funimgbox').css('background-color')=='transparent'){
					$('#main .function #service .funimgbox .type').val('wait');
					$('#main .function #service .funimgbox').css({'background-color':'rgb(230, 93, 93)'});
					$('#main .function #service .funtitle').html('等待服務人員');
				}
				else if(d=='empty'&&$('#main .function #service .funimgbox').css('background-color')=='rgb(230, 93, 93)'){
					$('#main .function #service .funimgbox .type').val('idle');
					$('#main .function #service .funimgbox').css({'background-color':'transparent'});
					$('#main .function #service .funtitle').html('服務鈴');
				}
				else{
				}
			},
			error:function(e){
				//console.log(e);
			}
		});
		logouttime--;
		$('.timeout').html(logouttime);
		if(logouttime==0){
			location.href='./bord.php?tab='+$('#main .table #talbe #tablenumber').val();
		}
	}

	tim1=setInterval(function(){timer1()},1000);

	listlog=$('.list').dialog({
		autoOpen:false,
		height:768,
		width:650,
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			window.clearInterval(tim1);
		},
		close: function(event, ui) { 
		   tim1=setInterval(function(){timer1()},1000);
		},
		buttons:[
			{
				text:"繼續點餐",
				click:function(){
					for(var indexcounter=($('.listcontent #item').length-1);indexcounter>=0;indexcounter--){
						if(parseInt($('.listcontent #item:eq('+indexcounter+') .num').html())==0){
							$('.listcontent #item:eq('+indexcounter+')').remove();
							$('#orderlist input[name="name[]"]:eq('+indexcounter+')').remove();
							$('#orderlist input[name="size[]"]:eq('+indexcounter+')').remove();
							$('#orderlist input[name="price[]"]:eq('+indexcounter+')').remove();
							$('#orderlist input[name="number[]"]:eq('+indexcounter+')').remove();
							$('#orderlist input[name="taste[]"]:eq('+indexcounter+')').remove();
							$('#orderlist input[name="no[]"]:eq('+indexcounter+')').remove();
						}
						else{
							console.log(indexcounter);
						}
					}
					$(this).dialog('close');
				}
			},
			{
				text:"結帳",
				click:function(){
					if(parseInt($('.total .tmoy').html())==0){
					}
					else{
						console.log(parseInt($('.total .tmoy').html()));
						$(this).dialog('close');
						finish.dialog('open');
					}
				}
			}
		]
	});
	chose=$('.chose').dialog({
		autoOpen:false,
		title:'產品資訊',
		width:$(window).width(),
		height:$(window).height(),
		position:{
			my:'left top',
			at:'left top',
			of:'body'
		},
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			window.clearInterval(tim1);
		},
		close: function(event, ui) { 
		   tim1=setInterval(function(){timer1()},1000);
		  /* if($('.chose #m2').length>0){
			   $('.chose #m2').remove();
		   }
		   else{
		   }
		   if($('.chose #m3').length>0){
			   $('.chose #m3').remove();
		   }
		   else{
		   }*/
		   $('.chose #imgbox #img').attr('src','');
		   $('.chose #name').html('');
		   $('.chose #introductionbox').html('');
		   if(typeof $('.chose #selectbox select[name="mname"]').html()==="undefined"){
		   }
		   else{
			   $('.chose #selectbox select[name="mname"]').html('');
			   $('.chose #inputbox input[name="number"]').val('1');
		   }
		}
	});
	service=$('.service').dialog({
		autoOpen:false,
		title:'服務項目',
		width:$(window).width(),
		height:$(window).height(),
		position:{
			my:'left top',
			at:'left top',
			of:'body'
		},
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			window.clearInterval(tim1);
		},
		close: function() { 
		   tim1=setInterval(function(){timer1()},1000);
		   $('.service #fun .send').prop('disabled',true);
		   $('.service #item #items #serviceitems').prop('class','');
		   $('.service #item #items #serviceitems #sercheck:checked').prop('checked',false);
		}
	});
	checkcancelservice=$('.checkcancelservice').dialog({
		autoOpen:false,
		title:'系統訊息',
		width:550,
		height:250,
		position:{
			my:'center center',
			at:'center center',
			of:'body'
		},
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			window.clearInterval(tim1);
		},
		close: function() { 
		   tim1=setInterval(function(){timer1()},1000);
		}
	});
	ordernumber=$('.ordernumber').dialog({
		autoOpen:false,
		title:'輸入數量',
		width:500,
		height:600,
		position:{my:'center',at:'right top',of:window},
		resizable:false,
		modal:true,
		draggable:false
	});
	order=$('.order').dialog({
		autoOpen:false,
		title:'',
		width:1000,
		height:600,
		resizable:false,
		modal:true,
		draggable:false,
		buttons:[
			{
				text:'取消',
				click:function(){
					$(this).dialog('close');
				}
			},
			{
				text:'確認',
				click:function(){
					orsubutton();
				}
			}
		]
	});
	function orsubutton(){
		console.log('2');
		if($('.order .ornumber').val()==0){
		}
		else{
			if($('.plus:eq('+findex+')').html().match(/(img)/)){
				$('.plus:eq('+findex+')').html(parseInt($('.order .ornumber').val()));
			}
			else{
				$('.plus:eq('+findex+')').html(parseInt($('.plus:eq('+findex+')').html())+parseInt($('.order .ornumber').val()));
			}
			var tasteno='';
			var temptaste='';
			var tastemoney=0;
			for(var i=0;i<$('.order .tastecheck').length;i++){
				if($('.order #taste'+i+':checked').length>0){
					if(tasteno.length>0){
						tasteno=tasteno+',';
						temptaste=temptaste+',';
					}
					else{
					}
					tasteno=tasteno+$('.order #taste'+i).val();
					temptaste=temptaste+$('.order #taste'+i+'name').html();
					tastemoney=parseInt(tastemoney)+parseInt($('.order #tmoney'+i).val());
				}
				else{
				}
			}
			var testtag=-1;
			var nexttag=$('.list .listcontent #name').length;
			for(var i=0;i<$('.list .listcontent #name').length;i++){
				if($('.food:eq('+findex+') .inumber').val()==$('.list .listcontent #name:eq('+i+')').find('#no').val() && tasteno==$('.list .listcontent #name:eq('+i+')').find('#selecttasteno').val() && $('.order #name:eq('+i+') #orvaname').val()==$('.list .listcontent #size').val()){
					console.log('exist');
					testtag=i;
					break;
				}
				else{
				}
			}
			/*if($('.list .img'+findex).length>0){
				$('.list .listcontent .img'+findex+' .num').html(parseInt($('.list .listcontent .img'+findex+' .num').html())+parseInt($('.order #ornumber').val()));
				$('.list .tmoy').html(parseInt($('.list .tmoy').html())+(parseInt($('.order #ornumber').val())*parseInt($('.order #orvalue').val())));
				$('#orderlist input[name="number'+findex+'"]').val(parseInt($('#orderlist input[name="number'+findex+'"]').val())+parseInt($('.order #ornumber').val()));
			}
			else{
				if($('.order #orvaname').val().length>0){
					var ornamesize='('+$('.order #orvaname').val()+')';
				}
				else{
					var ornamesize='';
				}
				
				temptasteno='<input type="hidden" id="selecttasteno" value="'+temptasteno+'">';
				temptaste='<div id="selecttaste" style="width:100%;font-size:15px;float:left;">'+temptaste+'</div>';
				$('.list .listcontent').append('<div id="item" class="img'+findex+'"><div id="name">'+$('.order #orname').html()+ornamesize+'<input type="hidden" id="no" value="'+$('.food:eq('+findex+') .inumber').val()+'">'+temptasteno+'<input type="hidden" id="money" value="'+(parseInt($('.order #orvalue').val())+parseInt(tastemoney))+'"></div>'+'<div id="num"><img id="listfun" class="diffbun" src="ordermls.png"><div class="numbox"><span class="num">'+$('.order #ornumber').val()+'</span>份</div><img id="listfun" class="plusbun" src="plus.png"></div>'+'<div id="price" class="price" style="">'+(parseInt($('.order #orvalue').val())+parseInt(tastemoney))+'元</div>'+temptaste+'</div>');
				$('.list .tmoy').html(parseInt($('.list .tmoy').html())+(parseInt($('.order #ornumber').val())*(parseInt($('.order #orvalue').val())+parseInt(tastemoney))));
				$('#orderlist').append('<input type="hidden" name="no[]" value="'+findex+'"><input type="hidden" name="name'+findex+'" value="'+$('.order #orname').html()+'"><input type="hidden" name="price'+findex+'" value="'+(parseInt($('.order #orvalue').val())+parseInt(tastemoney))+'"><input type="hidden" name="number'+findex+'" value="1"><input type="hidden" name="taste'+findex+'" value="temptasteno"');
			}*/
			if(testtag>=0){
				$('.list .listcontent .img:eq('+testtag+') .num').html(parseInt($('.list .listcontent .img:eq('+testtag+') .num').html())+parseInt($('.order .ornumber').val()));
				$('.list .tmoy').html(parseInt($('.list .tmoy').html())+(parseInt($('.order .ornumber').val())*parseInt($('.order #orvalue').val())));
				$('#orderlist input[name="number"]:eq('+testtag+')').val(parseInt($('#orderlist input[name="number"]:eq('+testtag+')').val())+parseInt($('.order .ornumber').val()));
			}
			else{
				if($('.order #orvaname').val().length>0){
					var ornamesize='('+$('.order #orvaname').val()+')';
				}
				else{
					var ornamesize='';
				}
				
				temptasteno='<input type="hidden" id="selecttasteno" value="'+tasteno+'">';
				temptaste='<div id="selecttaste" style="width:100%;font-size:15px;float:left;">'+temptaste+'</div>';
				$('.list .listcontent').append('<div id="item" class="img"><div id="name">'+$('.order #orname').html()+ornamesize+'<input type="hidden" id="size" value="'+$('.order #orvaname').val()+'"><input type="hidden" id="no" value="'+$('.food:eq('+findex+') .inumber').val()+'">'+temptasteno+'<input type="hidden" id="money" value="'+(parseInt($('.order #orvalue').val())+parseInt(tastemoney))+'"></div>'+'<div id="num"><img id="listfun" class="diffbun" src="ordermls.png"><div class="numbox"><span class="num">'+$('.order .ornumber').val()+'</span>份</div><img id="listfun" class="plusbun" src="plus.png"></div>'+'<div id="price" class="price" style="">'+(parseInt($('.order #orvalue').val())+parseInt(tastemoney))+'元</div>'+temptaste+'</div>');
				$('.list .tmoy').html(parseInt($('.list .tmoy').html())+(parseInt($('.order .ornumber').val())*(parseInt($('.order #orvalue').val())+parseInt(tastemoney))));
				$('#orderlist').append('<input type="hidden" name="no[]" value="'+findex+'"><input type="hidden" name="name[]" value="'+$('.order #orname').html()+'"><input type="hidden" name="size[]" value="'+$('.order #orvaname').val()+'"><input type="hidden" name="price[]" value="'+(parseInt($('.order #orvalue').val())+parseInt(tastemoney))+'"><input type="hidden" name="number[]" value="'+parseInt($('.order .ornumber').val())+'"><input type="hidden" name="taste[]" value="'+tasteno+'">');
			}
			$('#orderlist input[name="tmoy"]').val(parseInt($('#orderlist input[name="tmoy"]').val())+(parseInt($('.order .ornumber').val())*(parseInt($('.order #orvalue').val())+parseInt(tastemoney))));
			order.dialog('close');
			chose.dialog('close');
			
			if($('.point').html().length==0){
				$('.point').css({'background':'url(\"redpoint.png\")'});
				$('.temppoint').css({'background':'url(\"redpoint.png\")'});
				$('.point').html($('.order .ornumber').val());
				$('.temppoint').html('0');
			}
			else{
				$('.temppoint').html($('.point').html());
				$('.point').html(parseInt($('.point').html())+parseInt($('.order .ornumber').val()));
			}
			$('.temppoint').css({'display':'inline'});
			$('.point').css({'display':'none'});

			$('<img id="movepoint" src="redpoint.png" style="width:20px;height:20px;">').fly({
				start: {top: Yaxis, left: Xaxis},
				end: {top: 465, left: 1080, width: 64, height: 64},
				onEnd: function(){
					//this.destory();
					$('#movepoint').remove();
					$('.temppoint').css({'display':'none'});
					$('.point').css({'display':'inline'});
				}
			});
		}
	}
	$('#main .function #service').click(function(){
		if($('#main .function #service .funimgbox .type').val()=='idle'){
			service.dialog('open');
		}
		else{//$('#main .function $service .funimgbox .type').val()=='wait'
			checkcancelservice.dialog('open');
		}
	});
	$('.service #item #items #serviceitems').click(function(){
		var index=$(this).index();
		$(this).toggleClass('check');
		if($(this).find('#sercheck').prop('checked')){
			$(this).find('#sercheck').prop('checked',false);
		}
		else{
			$(this).find('#sercheck').prop('checked',true);
		}
		if($('.service #item #items #serviceitems #sercheck:checked').length==0){
			$('.service #fun .send').prop('disabled',true);
		}
		else{
			$('.service #fun .send').prop('disabled',false);
		}
	});
	$('.service #fun .send').click(function(){
		var data='tablenumber='+$('#main .table #table #tablenumber').val();
		for(var i=0;i<$('.service #item #items #serviceitems.check #sercheck').length;i++){
			data += '&service[]='+$('.service #item #items #serviceitems.check:eq('+i+') #sercheck').val();
		}
		$.ajax({
			url:'./lib/js/sendservice.ajax.php',
			method:'post',
			async:false,
			data:data,
			dataType:'html',
			success:function(d){
				//console.log(d);
				service.dialog('close');
				$('#main .function #service .funimgbox .type').val('wait');
				$('#main .function #service .funimgbox').css({'background-color':'rgb(230, 93, 93)'});
				$('#main .function #service .funtitle').html('等待服務人員');
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$('.service #fun .return').click(function(){
		service.dialog('close');
	});
	$('.checkcancelservice #send').click(function(){
		$.ajax({
			url:'./lib/js/cancel.service.php',
			method:'post',
			async:false,
			data:{'tablenumber':$('#main .table #table #tablenumber').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#main .function #service .funimgbox .type').val('idle');
				$('#main .function #service .funimgbox').css({'background-color':'transparent'});
				$('#main .function #service .funtitle').html('服務鈴');
				checkcancelservice.dialog('close');
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$('.checkcancelservice #cancel').click(function(){
		checkcancelservice.dialog('close');
	});
	$('#main .content').on('click','.food',function(){
		logouttime=maxlogouttime;
		var findex=$('.food').index(this);
		$.ajax({
			url:'./lib/js/chose.ajax.php',
			method:'post',
			async:false,
			data:{company:$('.company').val(),dep:$('.dep').val(),inumber:$('.inumber:eq('+findex+')').val()},
			dataType:'json',
			success:function(value){
				console.log(value);
				if(value[0]['imgfile']==null){
					$('.chose #imgbox #img').attr('src','./img/emptyimg.png');
				}
				else{
					$('.chose #imgbox #img').attr('src','./img/'+value[0]['imgfile']);
				}
				$('.chose #name').html(value[0]['name']);
				$('.chose #name').after('<input type="hidden" id="inumber" value="'+$('.inumber:eq('+$('.food').index(this)+')').val()+'">');
				if(typeof value[0]['introduction']==="undefined"||value[0]['introduction'].length==0){
				}
				else{
					$.each(value[0]['introduction'],function(i,v){
						$('.chose #introductionbox').append('<div style="width:100%;color:'+value[0]['introcolor'][i]+';margin:0 0 5px 0;">'+v+'</div>');
					});
				}
				$('.chose #selectbox select[name="mname"]').html('');
				for(var i=1;i<=value[0]['mcounter'];i++){
					if(value[0]['mname'+i]==''){
						$('.chose #selectbox select[name="mname"]').append('<option value="'+value[0]['mname'+i]+';'+value[0]['money'+i]+'">'+value[0]['money'+i]+'</optioin>');
					}
					else{
						$('.chose #selectbox select[name="mname"]').append('<option value="'+value[0]['mname'+i]+';'+value[0]['money'+i]+'">'+value[0]['mname'+i]+'</optioin>');
					}
				}
			},
			error:function(e){
				console.log(e);
			}
		});
		chose.dialog('open');
	});
	$('.chose .return').click(function(){
		chose.dialog('close');
	});
	$('.chose #inputbox .diffbun').click(function(){
		if($('.chose #inputbox input[name="number"]').val()==1){
		}
		else{
			$('.chose #inputbox input[name="number"]').val(parseInt($('.chose #inputbox input[name="number"]').val())-1);
		}
	});
	$('.chose #inputbox .plusbun').click(function(){
		$('.chose #inputbox input[name="number"]').val(parseInt($('.chose #inputbox input[name="number"]').val())+1);
	});
	$(document).on('click','.chose .mbutton',function(){
		var orbuttonname=$(this).val();
		var orbuttonsize=$('.chose .mname'+$(this).attr('id')).val();
		var orbuttonvalue=$('.chose .mvalue'+$(this).attr('id')).val();
		$.ajax({
			url:'./tool/order.ajax.php',
			method:'post',
			title:$('.chose #name').html(),
			data:{company:'<?php echo $content["basic"]["company"]; ?>',dep:'<?php echo $content["basic"]["dep"]; ?>',inumber:$('.chose #inumber').val()},
			dataType:'json',
			success:function(value){
				/*初始設定*/
				$('.order #orimg').attr('src','');
				$('.order #orname').html('');
				$('.order #ortaste').html('');
				$('.order #ormoney').html('');
				$('.order .ornumber').val('0');
				/**********/
				$('.order #orimg').attr('src',$('.chose #img').attr('src'));
				$('.order #orname').html($('.chose #name').html());
				if(value==0){
					$('.order #ortaste').css({'background-color':'#F5F8FA'});
					$('.order #ortaste').html('<div style="font-size:20px;text-align:center;">備註區</div><span style="font-size:17px;">無可用備註</span>');
				}
				else{
					$('.order #ortaste').html('<div style="font-size:20px;text-align:center;">備註區</div>');
					$('.order #ortaste').css({'background-color':'#ffffff'});
					var temp=0;
					var temptaste='<div style="width:100%;height:calc(100% - 25px);overflow-y:auto;">';
					for(var i=0;i<value.length;i++){
						/*if(value[i]['type']==temp){
						}
						else{
							temp=value[i]['type'];
							if(value[i]['type']==1){
								$('.order #ortaste').append('<h3>口味選項</h3><hr>');
							}
							else{//if(value[i]['type']==2)
								$('.order #ortaste').append('<h3>加料選項</h3><hr>');
							}
						}*/
						if(value[i]['money']>0){
							temptaste=temptaste+'<label style="display:inline-block;font-size:30px;margin-right:10px;margin-bottom:10px;"><input type="checkbox" style="zoom: 1.5;" id="taste'+i+'" class="tastecheck" value="'+value[i]['taste']+'"><span id="taste'+i+'name">'+value[i]['name']+'(加'+value[i]['money']+'元)</span>'+'</label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'">';
						}
						else{
							temptaste=temptaste+'<label style="display:inline-block;font-size:30px;margin-right:10px;margin-bottom:10px;"><input type="checkbox" id="taste'+i+'" class="tastecheck" style="zoom: 1.5;" value="'+value[i]['taste']+'"><span id="taste'+i+'name">'+value[i]['name']+'</span></label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'">';
						}
					}
					$('.order #ortaste').append(temptaste+'</div>');
				}
				$('.order #ormoney').html('<span id="ormoname">'+orbuttonname+'</span><input type="hidden" id="orvaname" value="'+orbuttonsize+'"><input type="hidden" id="orvalue" value="'+orbuttonvalue+'">');
			},
			error:function(d){
				console.log(d);
			}
		});
		order.dialog('open');
	});
	$(document).on('click','.order #orplus',function(){
		$('.order .ornumber').val(parseInt($('.order .ornumber').val())+1);
	});
	$(document).on('click','.order #ordiff',function(){
		if($('.order .ornumber').val()==0){
		}
		else{
			$('.order .ornumber').val(parseInt($('.order .ornumber').val())-1);
		}
	});
	$(document).on('click','.order .ornumber',function(){
		$('.ordernumber #viewnumber').val($('.order .ornumber').val());
		//ordernumber.dialog('open');
	});
	$(document).on('click','.ordernumber .numbutton',function(){
		if(parseInt($('.ordernumber #viewnumber').val())==0){
			$('.ordernumber #viewnumber').val($('.ordernumber .numbutton:eq('+$('.ordernumber .numbutton').index(this)+')').val());
		}
		else{
			$('.ordernumber #viewnumber').val($('.ordernumber #viewnumber').val()+$('.ordernumber .numbutton:eq('+$('.ordernumber .numbutton').index(this)+')').val());
		}
	});
	$(document).on('click','.ordernumber #backspace',function(){
		if(parseInt($('.ordernumber #viewnumber').val())==0){
			
		}
		else if(parseInt($('.ordernumber #viewnumber').val())<10){
			$('.ordernumber #viewnumber').val('0');
		}
		else{
			$('.ordernumber #viewnumber').val($('.ordernumber #viewnumber').val().substr(0,$('.ordernumber #viewnumber').val().length-1));
		}
	});
	$(document).on('click','.ordernumber #cancel',function(){
		ordernumber.dialog('close');
	});
	$(document).on('click','.ordernumber #submit',function(){
		$('.order .ornumber').val($('.ordernumber #viewnumber').val());
		ordernumber.dialog('close');
	});
	/*$(document).on('touchstart','.listcontent .plusbun',function(e){
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		f = touch.pageX;
		fy = touch.pageY;
		findex=$('.plusbun').index(this);
	});
	$(document).on('touchend','.plusbun',function(e){
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		f1 = touch.pageX;
		fy1 = touch.pageY;
		if(f1-f<=30 && f1-f>=-30 && fy1-fy<=30 && fy1-fy>=-30){
			if($('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html().match(/(img)/)){
			//if($('.plus:eq('+$('.listcontent #item:eq('+findex+')').attr('class').substr(3)+')').html().match(/(img)/)){
				$('.listcontent #item:eq('+findex+') .num').html('1');
				$('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html('1');
				//$('.plus:eq('+$('.listcontent #item:eq('+findex+')').attr('class').substr(3)+')').html('1');
			}
			else{
				console.log('1');
				$('.listcontent #item:eq('+findex+') .num').html(parseInt($('.listcontent #item:eq('+findex+') .num').html())+1);
				$('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html(parseInt($('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html())+1);
				//$('.plus:eq('+$('.listcontent #item:eq('+findex+')').attr('class').substr(3)+')').html(parseInt($('.plus:eq('+$('.listcontent #item:eq('+findex+')').attr('class').substr(3)+')').html())+1);
			}
			$('.list .tmoy').html(parseInt($('.list .tmoy').html())+parseInt($('.list #name:eq('+findex+') #money').val()));
			$('#orderlist input[name="tmoy"]').val(parseInt($('#orderlist input[name="tmoy"]').val())+parseInt($('.list #name:eq('+findex+') #money').val()));
			$('#orderlist input[name="number[]"]:eq('+findex+')').val(parseInt($('#orderlist input[name="number[]"]:eq('+findex+')').val())+1);
			if($('.point').html().length==0){
				$('.point').html(1);
				$('.point').css({'background':'url(\"redpoint.png\")'});
			}
			else{
				$('.point').html(parseInt($('.point').html())+1);
			}
		}
		else{
		}
	});
	$(document).on('touchstart','.diffbun',function(e){
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		f = touch.pageX;
		fy = touch.pageY;
		findex=$('.diffbun').index(this);
	});
	$(document).on('touchend','.diffbun',function(e){
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		f1 = touch.pageX;
		fy1 = touch.pageY;
		if(f1-f<=30 && f1-f>=-30 && fy1-fy<=30 && fy1-fy>=-30){
			if(parseInt($('.listcontent #item:eq('+findex+') .num').html())-1<=0){
				if(parseInt($('.listcontent #item:eq('+findex+') .num').html())-1==0){
					$('.list .tmoy').html(parseInt($('.list .tmoy').html())-parseInt($('.list #name:eq('+findex+') #money').val()));
					$('#orderlist input[name="tmoy"]').val(parseInt($('#orderlist input[name="tmoy"]').val())-parseInt($('.list #name:eq('+findex+') #money').val()));
					$('#orderlist input[name="number[]"]:eq('+findex+')').val(parseInt($('#orderlist input[name="number[]"]:eq('+findex+')').val())-1);
					if($('.point').html().length==0){
					}
					else{
						$('.point').html(parseInt($('.point').html())-1);
						if(parseInt($('.point').html())==0){
							$('.point').html('');
							$('.point').css({'background':''});
						}
						else{
						}
					}
				}
				else{
				}
				$('.listcontent #item:eq('+findex+') .num').html('0');
				$('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html(parseInt($('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html())-1);
				if(parseInt($('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html())<=0){
					$('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html('<img  src="plus.png">');
				}
				else{
				}
			}
			else{
				$('.listcontent #item:eq('+findex+') .num').html(parseInt($('.listcontent #item:eq('+findex+') .num').html())-1);
				$('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html(parseInt($('.foodbox #number'+$('.listcontent #name:eq('+findex+') #no').val()+' .plus').html())-1);
				$('.list .tmoy').html(parseInt($('.list .tmoy').html())-parseInt($('.list #name:eq('+findex+') #money').val()));
				$('#orderlist input[name="tmoy"]').val(parseInt($('#orderlist input[name="tmoy"]').val())-parseInt($('.list #name:eq('+findex+') #money').val()));
				$('#orderlist input[name="number[]"]:eq('+findex+')').val(parseInt($('#orderlist input[name="number[]"]:eq('+findex+')').val())-1);
				$('.point').html(parseInt($('.point').html())-1);
			}
		}
		else{
		}
	});*/
	/*$(document).on('touchstart','.leftimg',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t = touch.pageX;
		ty = touch.pageY;
		findex=$('.leftimg').index(this);
		$('.leftimg').css({'opacity':'0.5'});
	});
	$(document).on('touchend','.leftimg',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t1 = touch.pageX;
		ty1 = touch.pageY;
		if(t1-t<=30 && t1-t>=-30 && ty1-ty<=30 && ty1-ty>=-30){
			$('.prev').trigger('click');
			setTimeout(function(){$('.leftimg').css({'opacity':'1'});},800);
		}
		else{
			$('.leftimg').css({'opacity':'1'});
		}
	});
	$(document).on('touchstart','.rightimg',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t = touch.pageX;
		ty = touch.pageY;
		findex=$('.rightimg').index(this);
		$('.rightimg').css({'opacity':'0.5'});
	});
	$(document).on('touchend','.rightimg',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t1 = touch.pageX;
		ty1 = touch.pageY;
		if(t1-t<=30 && t1-t>=-30 && ty1-ty<=30 && ty1-ty>=-30){
			$('.next').trigger('click');
			setTimeout(function(){$('.rightimg').css({'opacity':'1'});},800);
		}
		else{
			$('.rightimg').css({'opacity':'1'});
		}
	});*/
});