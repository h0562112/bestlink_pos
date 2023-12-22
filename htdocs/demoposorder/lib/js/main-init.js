$(document).ready(function(){
	var x=0,x1=0;
	var t=0,t1=0,ty=0,ty1=0;
	var f=0,f1=0;
	var Xaxis=0,Yaxis=0;
	var findex=0;
	var maxlogouttime=300;
	var logouttime=maxlogouttime;
	var tim1;
	function timer1(){
		logouttime--;
		$('.timeout').html(logouttime);
		if(logouttime==0){
			location.href='./bord.php';
			/*$.ajax({
				url:'../logoutmethod/',
				success:function(){
					location.href='../login/';
				},
				error:function(){
					console.log('error');
				}
			});*/
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
	finish=$('.finish').dialog({
		autoOpen:false,
		width:500,
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
				text:"內用",
				click:function(){
					$(this).dialog('close');
					$('#orderlist').append('<input type="hidden" name="fodtype" value="1">');
					$('#orderlist').submit();
				}
			},
			{
				text:"外帶",
				click:function(){
					$(this).dialog('close');
					$('#orderlist').append('<input type="hidden" name="fodtype" value="2">');
					$('#orderlist').submit();
				}
			}
		]
	});
	chose=$('.chose').dialog({
		autoOpen:false,
		title:'選擇金額',
		width:1000,
		height:600,
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			window.clearInterval(tim1);
		},
		close: function(event, ui) { 
		   tim1=setInterval(function(){timer1()},1000);
		   if($('.chose #m2').length>0){
			   $('.chose #m2').remove();
		   }
		   else{
		   }
		   if($('.chose #m3').length>0){
			   $('.chose #m3').remove();
		   }
		   else{
		   }
		   $('.chose #img').attr('src','');
		   $('.chose #name').html('');
		   $('.chose #introduction').html('');
		   $('.chose #1').val('');
		   $('.chose #1').attr('disabled',true);
		   $('.chose #2').val('');
		   $('.chose #2').attr('disabled',true);
		   $('.chose #3').val('');
		   $('.chose #3').attr('disabled',true);
		   $('.chose #more').attr('disabled',true);
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
	$(document).on('touchstart','.foodbox',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		x = touch.pageX;
	});
	$(document).on('touchmove','.foodbox',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
	});
	$(document).on('touchend','.foodbox',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		x1 = touch.pageX;
		if(x1-x>30){
			$('.prev').trigger('click');
			$('.leftimg').css({'opacity':'0.5'});
			setTimeout(function(){$('.leftimg').css({'opacity':'1'});},800);
		}
		else if(x1-x<-30){
			$('.next').trigger('click');
			$('.rightimg').css({'opacity':'0.5'});
			setTimeout(function(){$('.rightimg').css({'opacity':'1'});},800);
		}
	});
	$(document).on('touchstart','.food',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t = touch.pageX;
		ty = touch.pageY;
		findex=$('.food').index(this);
	});
	$(document).on('touchend','.food',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t1 = touch.pageX;
		ty1 = touch.pageY;
		Xaxis=t1;
		Yaxis=ty1;
		if(t1-t<=30 && t1-t>=-30 && ty1-ty<=30 && ty1-ty>=-30){
			/*if($('.plus:eq('+findex+')').html().match(/(img)/)){
				$('.plus:eq('+findex+')').html('1');
			}
			else{
				$('.plus:eq('+findex+')').html(parseInt($('.plus:eq('+findex+')').html())+1);
			}
			if($('.list .img'+findex).length>0){
				$('.list .listcontent .img'+findex+' .num').html(parseInt($('.list .listcontent .img'+findex+' .num').html())+1);
				$('.list .tmoy').html(parseInt($('.list .tmoy').html())+parseInt($('.food:eq('+findex+') .price').val()));
				$('#orderlist input[name="number'+findex+'"]').val(parseInt($('#orderlist input[name="number'+findex+'"]').val())+1);
			}
			else{
				$('.list .listcontent').append('<div id="item" class="img'+findex+'"><div id="name">'+$('.food:eq('+findex+') .name').val()+'</div>'+'<div id="num"><img id="listfun" class="diffbun" src="ordermls.png"><div class="numbox"><span class="num">1</span>份</div><img id="listfun" class="plusbun" src="plus.png"></div>'+'<div id="price" class="price" style="">'+$('.food:eq('+findex+') .price').val()+'元</div></div>');
				$('.list .tmoy').html(parseInt($('.list .tmoy').html())+parseInt($('.food:eq('+findex+') .price').val()));
				$('#orderlist').append('<input type="hidden" name="no[]" value="'+findex+'"><input type="hidden" name="name'+findex+'" value="'+$('.food:eq('+findex+') .name').val()+'"><input type="hidden" name="price'+findex+'" value="'+$('.food:eq('+findex+') .price').val()+'"><input type="hidden" name="number'+findex+'" value="1">');
			}
			$('#orderlist input[name="tmoy"]').val(parseInt($('#orderlist input[name="tmoy"]').val())+parseInt($('.food:eq('+findex+') .price').val()));
			$('<img id="movepoint" src="redpoint.png" style="width:20px;height:20px;">').fly({
				start: {top: touch.pageY, left: touch.pageX},
				end: {top: 465, left: 1080, width: 64, height: 64},
				onEnd: function(){
					//this.destory();
					$('#movepoint').remove();
					$('.point').css({'background':'url(\"redpoint.png\")'});
					if($('.point').html().length==0){
						$('.point').html(1);
					}
					else{
						$('.point').html(parseInt($('.point').html())+1);
					}
				}
			});*/
			$.ajax({
				url:'./tool/chose.ajax.php',
				method:'post',
				data:{company:'<?php echo $content["basic"]["company"]; ?>',dep:'<?php echo $content["basic"]["dep"]; ?>',inumber:$('.inumber:eq('+findex+')').val()},
				dataType:'json',
				success:function(value){
					$('.chose #img').attr('src',value[0]['imgfile']);
					$('.chose #name').html(value[0]['name']);
					$('.chose #name').after('<input type="hidden" id="inumber" value="'+$('.inumber:eq('+findex+')').val()+'">');
					$('.chose #introduction').html(value[0]['introduction']);
					for(var i=1;i<=value[0]['mcounter'];i++){
						if(i>3&&i<7){
							$('.chose #moncontent').append('<div class="money" id="m2" style="width:100%;height:75%;"><input type="button" id="4" class="mbutton" value="" style="width:100%;height:30%;margin-bottom:1%;" disabled><input type="hidden" class="mname4" value=""><input type="hidden" class="mvalue4" value=""><input type="button" id="5" class="mbutton" value="" style="width:100%;height:30%;margin-bottom:1%;" disabled><input type="hidden" class="mname5" value=""><input type="hidden" class="mvalue5" value=""><input type="button" id="6" class="mbutton" value="" style="width:100%;height:30%;margin-bottom:1%;" disabled><input type="hidden" class="mname6" value=""><input type="hidden" class="mvalue6" value=""></div>');
						}
						else if(i>6){
							$('.chose #moncontent').append('<div class="money" id="m3" style="width:100%;height:75%;"><input type="button" id="7" class="mbutton" value="" style="width:100%;height:30%;margin-bottom:1%;" disabled><input type="hidden" class="mname7" value=""><input type="hidden" class="mvalue7" value=""><input type="button" id="8" class="mbutton" value="" style="width:100%;height:30%;margin-bottom:1%;" disabled><input type="hidden" class="mname8" value=""><input type="hidden" class="mvalue8" value=""><input type="button" id="9" class="mbutton" value="" style="width:100%;height:30%;margin-bottom:1%;" disabled><input type="hidden" class="mname9" value=""><input type="hidden" class="mvalue9" value=""></div>');
						}
						$('.chose #'+i).val(value[0]['mname'+i]+' '+value[0]['money'+i]+' 元');
						$('.chose .mname'+i).val(value[0]['mname'+i]);
						$('.chose .mvalue'+i).val(value[0]['money'+i]);
						$('.chose #'+i).attr('disabled',false);
					}
					if(i<=4){
					}
					else{
						$('.chose .more').attr('disabled',false);
					}
				}
			});
			chose.dialog('open');
		}
		else{
		}
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
	$('#getlist').on('touchstart',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		f = touch.pageX;
		fy = touch.pageY;
	});
	$('#getlist').on('touchend',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		f1 = touch.pageX;
		fy1 = touch.pageY;
		if(f1-f<=30 && f1-f>=-30 && fy1-fy<=30 && fy1-fy>=-30){
			window.clearInterval(tim1);
			listlog.dialog('open');
		}
		else{
		}
	});
	$(document).on('touchstart','.listcontent .plusbun',function(e){
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
	});
	$(document).on('touchstart','.leftimg',function(e){
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
	});
});