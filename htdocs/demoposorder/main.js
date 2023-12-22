$(document).ready(function(){
	var x=0,x1=0;
	var t=0,t1=0,ty=0,ty1=0;
	var f=0,f1=0;
	var Xaxis=0,Yaxis=0;
	var findex=0;
	var maxlogouttime=300;
	var logouttime=maxlogouttime;
	var tim1;
	function timer1(){//待機逾時後，跳轉至待機畫面
		logouttime--;
		$('.timeout').html(logouttime);
			if(logouttime==0){
				location.href='./bord2.php';
			}
	}

	//tim1=setInterval(function(){timer1()},1000);//啟動倒數

	chose=$('.chose').dialog({//選擇價格畫面
		autoOpen:false,
		width:800,
		height:1280,
		position:{my:'left top',at:'left top',of:'body'},
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			window.clearInterval(tim1);
		},
		close: function(event, ui) { 
			//tim1=setInterval(function(){timer1()},1000);
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
			$('.chose #introduction td[id^="introtitle"]').css({'font-weight':''});
			$('.chose #introduction td[id^="introtitle"]').html('');
			$('.chose #introduction td[id^="introduction"').css({'font-weight':''});
			$('.chose #introduction td[id^="introduction"').html('');
			$('.chose #introduction tr[id^="color"').css({'color':''});
		}
	});
	order=$('.order').dialog({//點餐畫面
		autoOpen:false,
		title:'',
		width:800,
		height:1280,
		position:{my:'left top',at:'left top',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	editbox=$('.editbox').dialog({//修改畫面
		autoOpen:false,
		width:800,
		height:1280,
		position:{my:'left top',at:'left top',of:'body'},
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			window.clearInterval(tim1);
		}
	});
	result=$('.result').dialog({//明細總覽
		autoOpen:false,
		width:(1080 / 1080 * 800),
		height:(1920 / 1920 * 1280),
		position:{my:'left top',at:'left top',of:'body'},
		resizable:false,
		modal:true,
		draggable:false,
		style:{'z-index':'200'},
		open:function(){
			window.clearInterval(tim1);
			var tempheight=parseInt($('.result .rescontent .listbox #list').height())-parseInt($('.result .rescontent .listbox #list #div').height());
			if(parseInt(tempheight)<50){
				$('.result .rescontent .listbox #list #emptydiv').css({"height":'50px'});
			}
			else{
				$('.result .rescontent .listbox #list #emptydiv').css({"height":tempheight+'px'});
			}
			for(var i=0;i<$('.result .rescontent .listbox #list #div div[id="name"]').length;i++){
				var height=$('.result .rescontent .listbox #list #div div[id="name"]:eq('+i+')').height();
				$('.result .rescontent .listbox #list #div div[id="number"]:eq('+i+')').css({"height":height});
				$('.result .rescontent .listbox #list #div div[id="money"]:eq('+i+')').css({"height":height});
			}
		},
		close: function(event, ui) { 
			//tim1=setInterval(function(){timer1()},1000);
		}
	});
	sysmeg=$('.sysmeg').dialog({//系統訊息
		autoOpen:false,
		width:(981 / 1080 * 800),
		height:(321 / 1920 * 1280),
		position:{my:'left top',at:'left+'+(49.5 / 1080 * 800)+' top+'+(600 / 1920 * 1280),of:'body'},
		resizable:false,
		modal:true,
		draggable:false,
		open:function(){
			$('.sysmeg').parent().css({'border':'0','background-color':'transparent'});
		}
	});
	bill=$('.bill').dialog({//結帳畫面
		autoOpen:false,
		width:(1078 / 1080 * 800),
		height:(1918 / 1920 * 1280),
		position:{my:'left top',at:'left top',of:'body'},
		resizable:false,
		modal:true,
		draggable:false,
		style:{'z-index':'200'},
		open:function(){
			window.clearInterval(tim1);
		},
		close: function(event, ui) { 
			//tim1=setInterval(function(){timer1()},1000);
		}
	});
	print=$('.print').dialog({//出單畫面
		autoOpen:false,
		width:(1080 / 1080 * 800),
		height:(1920 / 1920 * 1280),
		resizable:false,
		modal:true,
		draggable:false
	});
	chosetype=$('.chosetype').dialog({//展開產品類別選項
		autoOpen:false,
		width:(1080 / 1080 * 800),
		height:(565 / 1920 * 1280),
		position:{my:'left top',at:'left top+121',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	keybord=$('.keybord').dialog({
		autoOpen:false,
		width:568,
		height:866,
		position:{my:'left top',at:'left+100 top+100',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	selmem=$('.selmem').dialog({
		autoOpen:false,
		width:800,
		height:400,
		position:{my:'left top',at:'left+100 top+100',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	memlist=$('.memlist').dialog({
		autoOpen:false,
		width:568,
		height:605,
		position:{my:'left top',at:'left+100 top+100',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	checkbord=$('.checkbord').dialog({
		autoOpen:false,
		width:800,
		height:1280,
		position:{my:'left top',at:'left+100 top+100',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	$('.checkbord .inputcode input[type="button"]').click(function(){//功能區－認證－密碼輸入
		$('.checkbord .inputcode input[name="pw"]').val($('.checkbord .inputcode input[name="pw"]').val()+$(this).val());
	});
	$('.checkbord .inputcode #AC').click(function(){//功能區－認證－重填
		$('.checkbord .inputcode input[name="pw"]').val('');
	});
	$('.checkbord .inputcode #BKSP').click(function(){//功能區－認證－倒退
		if($('.checkbord .inputcode input[name="pw"]').val().length>0){
			$('.checkbord .inputcode input[name="pw"]').val($('.checkbord .inputcode input[name="pw"]').val().substr(0,($('.checkbord .inputcode input[name="pw"]').val().length-1)));
		}
		else{
		}
	});
	$('.checkbord .inputcode #submit').click(function(){//功能區－認證－確認
		if($('.checkbord .inputcode input[name="pw"]').val().length>0){
			$.ajax({
				url:'./tool/open.function.php',
				method:'post',
				data:{'code':$('.checkbord .inputcode input[name="pw"]').val()},
				dataType:'html',
				success:function(d){
					console.log(d);
					if(d=='error'){
						checkbord.dialog('close');
					}
					else{
						$('.checkbord .inputcode').css({'display':'none'});
						$('.checkbord .funbox').css({'display':''});
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
		}
		$('.checkbord .inputcode input[name="pw"]').val('');
	});
	$('.checkbord .inputcode #cancel').click(function(){//功能區－認證－返回
		$('.checkbord .inputcode input[name="pw"]').val('');
		checkbord.dialog('close');
	});
	$('.checkbord .funbox #openclass').click(function(){//功能區－開班
		$.ajax({
			url:'./tool/open.ajax.php',
			method:'post',
			data:{'usercode':$('.result input[name="userid"]').val(),'username':' '},
			dataType:'html',
			success:function(d){
				if(d=='success'){
					$.ajax({
						url:'./tool/change.class.php',
						method:'post',
						data:{'type':'isclose'},
						dataType:'html',
						success:function(d){
							$('.checkbord .funbox #openclass').css({'color':'#f0f0f0'});
							$('.checkbord .funbox #openclass').prop('disabled',true);
							$('.checkbord .funbox #closeclass').css({'color':'#898989'});
							$('.checkbord .funbox #closeclass').prop('disabled',false);
							checkbord.dialog('close');
						},
						error:function(e){
							console.log(e);
						}
					});
				}
				else{
				}
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$('.checkbord .funbox #closeclass').click(function(){//功能區－交班
		$.ajax({
			url:'./tool/close.ajax.php',
			method:'post',
			data:{'usercode':$('.result input[name="userid"]').val(),'username':' '},
			dataType:'html',
			success:function(d){
				if(d=='success'){
					$.ajax({
						url:'./tool/change.class.php',
						method:'post',
						data:{'type':'isopen'},
						dataType:'html',
						success:function(d){
							$('.checkbord .funbox #openclass').css({'color':'#898989'});
							$('.checkbord .funbox #openclass').prop('disabled',false);
							$('.checkbord .funbox #closeclass').css({'color':'#f0f0f0'});
							$('.checkbord .funbox #closeclass').prop('disabled',true);
							checkbord.dialog('close');
						},
						error:function(e){
							console.log(e);
						}
					});
				}
				else{
				}
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$('.checkbord .funbox #voidsale').click(function(){//功能區－作廢帳單
		$.ajax({
			url:'./tool/getsalelist.ajax.php',
			dataType:'html',
			success:function(d){
				$('.checkbord .funbox #content').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$('.checkbord .funbox #viewsale').click(function(){//功能區－瀏覽帳單
		$.ajax({
			url:'./tool/getsalelist.ajax.php',
			method:'post',
			data:{'sale':'sale'},
			dataType:'html',
			success:function(d){
				$('.checkbord .funbox #content').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$('.checkbord .funbox #close').click(function(){//功能區－關閉程式
		$(document).prop('title','點餐畫面－關閉程式');
	});
	$('.checkbord .funbox #help').click(function(){//功能區－遠端協助
		$(document).prop('title','點餐畫面－遠端協助');
		$('.checkbord .funbox #content').html('<div style="width:100%;height:100%;"><div id="teamviewer" style="margin:100px auto 0 auto;"><table><tr><td>ID</td><td><input type="text" value="'+$('.checkbord #tvid').val()+'"></td></tr><tr><td>PW</td><td><input type="text" id="tvpw"></td></tr></table></div></div>');
	});
	$(document).on('keypress',document,function(event){//輸入teamviewer密碼
		if($('.checkbord .funbox #content #tvpw').length>0){
			$('.checkbord .funbox #content #tvpw').val($('.checkbord .funbox #content #tvpw').val()+(event.which-48));
		}
		else{
		}
	});
	$('.checkbord .funbox #return').click(function(){//功能區－返回
		$(document).prop('title','點餐畫面');
		$('.checkbord').dialog('close');
		$('.checkbord .funbox #content').html('');
		$('.checkbord .funbox').css({'display':'none'});
		$('.checkbord .inputcode').css({'display':''});
	});
	$(document).on('click','.logo .memno',function(){
		if($('.logo .memno').length>0&&$('.logo .memno').html()=='輸入會員電話'){
			keybord.dialog('open');
		}
		else{
			memlist.dialog('open');
		}
	});
	$(document).on('click','.keybord input[type="button"]',function(){
		var index=$('.keybord input[type="button"]').index(this);
		$('.keybord input[name="memno"]').val($('.keybord input[name="memno"]').val()+$('.keybord input[type="button"]:eq('+index+')').val());
	});
	$(document).on('click','.keybord #AC',function(){
		$('.keybord input[name="memno"]').val('');
	});
	$(document).on('click','.keybord #BKSP',function(){
		if($('.keybord input[name="memno"]').val().length==1){
			$('.keybord input[name="memno"]').val('');
		}
		else if($('.keybord input[name="memno"]').val().length>1){
			$('.keybord input[name="memno"]').val($('.keybord input[name="memno"]').val().substr(0,$('.keybord input[name="memno"]').val().length-1));
		}
		else{
		}
	});
	$(document).on('click','.keybord #cancel',function(){
		$('.keybord input[name="memno"]').val('');
		keybord.dialog('close');
	});
	$(document).on('click','.keybord #submit',function(){
		if($('.keybord input[name="memno"]').val()!=''){
			if($('.keybord input[name="memno"]').val()=='0123456789'){//開啟功能區
				$('.keybord input[name="memno"]').val('');
				$('.checkbord .inputcode').css({'display':''});
				$('.checkbord .funbox').css({'display':'none'});
				checkbord.dialog('open');
				keybord.dialog('close');
			}
			else{
				$.ajax({
					url:'getmemname.ajax.php',
					method:'post',
					data:{'memno':$('.keybord input[name="memno"]').val()},
					dataType:'html',
					success:function(d){
						if(d=='該電話不存在，請確認輸入為正確電話。'){
							alert('該電話不存在，請確認輸入為正確電話。');
						}
						else{
							var t=d.match(/-/g);
							if(t.length>1){
								$('.selmem').html(d);
								selmem.dialog('open');
								keybord.dialog('close');
							}
							else{
								var tt=d.split('-');
								$('.result .membox').html('<input type="hidden" name="memno" value="'+tt[0]+'">'+tt[1]);
								$('.logo .memno').html(tt[1]);
								$.ajax({
									url:'getmemdata.ajax.php',
									method:'post',
									data:{'cardno':tt[0]},
									dataType:'json',
									success:function(d){
										var detail='<table style="width:100%;height:100%;"><tr><td style="width:23%;">會員編號</td><td style="width:27%;">'+d[0]['cardno']+'</td><td style="width:23%;">姓名</td><td style="width:27%;">'+d[0]['name']+'</td></tr><tr><td>生日</td><td>';
										if(d[0]['birth']==null){
											detail=detail+'';
										}
										else{
											detail=detail+d[0]['birth'];
										}
										detail=detail+'</td><td>性別</td><td>';
										if(d[0]['sex']==1){
											detail=detail+'男性';
										}
										else if(d[0]['sex']==2){
											detail=detail+'女性';
										}
										else{
											detail=detail+'';
										}
										detail=detail+'</td></tr><tr><td>連絡電話1</td><td>'+d[0]['tel']+'</td><td>連絡電話2</td><td>';
										if(d[0]['tel2']==null){
											detail=detail+'';
										}
										else{
											detail=detail+d[0]['tel2'];
										}
										detail=detail+'</td></tr><tr><td>聯絡地址</td><td colspan="3">';
										if(d[0]['address']==null){
											detail=detail+'';
										}
										else{
											detail=detail+d[0]['address'];
										}
										detail=detail+'</td></tr></table>';
										$('.memlist #memdata').html(detail);
										
										$('.memlist #memsale').html('');
										if(d[0]['BIZDATE'].length>0){
											$.each(d,function(index,value){
												$('.memlist #memsale').append('<div style="width:100%;margin-bottom:5px;float:left;">'+value['ITEMNAME']+'&nbsp;&nbsp;'+value['QTY']+'&nbsp;&nbsp;'+value['AMT']+'</div>');
											});
										}
										else{
										}
									},
									error:function(e){
										console.log(e);
									}
								});
								keybord.dialog('close');
							}
						}
					},
					error:function(e){
						console.log(e);
					}
				});
			}
		}
		else{
			$('.result .membox').html('');
			$('.logo .memno').html('輸入會員電話');
			keybord.dialog('close');
		}
	});
	$(document).on('click','.selmem #submit',function(){
		var tt=$('.selmem select[name="temp"] option:selected').val().split("-");
		$('.result .membox').html('<input type="hidden" name="memno" value="'+tt[0]+'">'+tt[1]);
		$('.logo .memno').html(tt[1]);
		$.ajax({
			url:'getmemdata.ajax.php',
			method:'post',
			data:{'cardno':tt[0]},
			dataType:'json',
			success:function(d){
				var detail='<table style="width:100%;height:100%;"><tr><td style="width:23%;">會員編號</td><td style="width:27%;">'+d[0]['cardno']+'</td><td style="width:23%;">姓名</td><td style="width:27%;">'+d[0]['name']+'</td></tr><tr><td>生日</td><td>';
				if(d[0]['birth']==null){
					detail=detail+'';
				}
				else{
					detail=detail+d[0]['birth'];
				}
				detail=detail+'</td><td>性別</td><td>';
				if(d[0]['sex']==1){
					detail=detail+'男性';
				}
				else if(d[0]['sex']==2){
					detail=detail+'女性';
				}
				else{
					detail=detail+'';
				}
				detail=detail+'</td></tr><tr><td>連絡電話1</td><td>'+d[0]['tel']+'</td><td>連絡電話2</td><td>';
				if(d[0]['tel2']==null){
					detail=detail+'';
				}
				else{
					detail=detail+d[0]['tel2'];
				}
				detail=detail+'</td></tr><tr><td>聯絡地址</td><td colspan="3">';
				if(d[0]['address']==null){
					detail=detail+'';
				}
				else{
					detail=detail+d[0]['address'];
				}
				detail=detail+'</td></tr></table>';
				$('.memlist #memdata').html(detail);
				
				$('.memlist #memsale').html('');
				if($.type(d[0]['BIZDATE'])!=="undefined"&&d[0]['BIZDATE'].length>0){
					$.each(d,function(index,value){
						$('.memlist #memsale').append('<div style="width:100%;margin-bottom:5px;float:left;">'+value['ITEMNAME']+'&nbsp;&nbsp;'+value['QTY']+'&nbsp;&nbsp;'+value['AMT']+'</div>');
					});
				}
				else{
				}
			},
			error:function(e){
				console.log(e);
			}
		});
		selmem.dialog('close');
	});
	$(document).on('click','.memlist #close',function(){
		memlist.dialog('close');
	});
	$(document).on('click','.memlist #change',function(){
		$('.keybord input[name="memno"]').val('');
		memlist.dialog('close');
		keybord.dialog('open');
	});
	$('#opentype').click(function(){
		chosetype.dialog('open');
	});
	$(document).on('click','#closetype',function(){
		chosetype.dialog('close');
	});
	function orsubutton(){//將點完餐點的資料產生明細欄位
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
			var tastename='';
			var tasteprice='';
			var tastenumber='';
			var tastemoney=0;
			for(var i=0;i<$('.order .tastecheck').length;i++){
				if($('.order #taste'+i+':checked').length>0){
					if(tasteno.length>0){
						tasteno=tasteno+',';
						tastename=tastename+',';
						tasteprice=tasteprice+',';
						tastenumber=tastenumber+',';
						//temptaste=temptaste+',';
					}
					else{
						temptaste="<ul>";
					}
					tasteno=tasteno+$('.order #taste'+i).val();
					tastename=tastename+$('.order #taste'+i+'name').html();
					tasteprice=tasteprice+$('.order #tmoney'+i).val();
					tastenumber=tastenumber+'1';
					temptaste=temptaste+'<li>'+$('.order #taste'+i+'name').html()+'</li>';
					tastemoney=parseInt(tastemoney)+parseInt($('.order #tmoney'+i).val());
				}
				else{
				}
			}
			if(tasteno.length>0){
				temptaste=temptaste+'</ul>';
			}
			else{
			}
			var testtag=-1;
			for(var i=0;i<$('.list .listcontent .box td').length;i++){
				if($('.chose input[name="inumber"]').val()==$('.list .listcontent .box td:eq('+i+') input[name="no[]"]').val() && (tasteno==$('.list .listcontent .box td:eq('+i+') input[name="taste[]"]').val() || (tasteno=='' && $('.list .listcontent .box td:eq('+i+') input[name="taste[]"]').val()=='')) && ($('.order #orname #orvaname').val()==$('.list .listcontent .box td:eq('+i+') input[name="mname[]"]').val() || ($('.order #orname #orvaname').val()=='' && $('.list .listcontent .box td:eq('+i+') input[name="mname[]"]').val()==''))){
					testtag=i;
					break;
				}
				else{
				}
			}
			if($('.order #orvaname').val().length>0){
				var ornamemname='('+$('.order #orvaname').val()+')';
			}
			else{
				var ornamemname='';
			}
			if(testtag>=0){
				//$('.list .listcontent td:eq('+testtag+') #item #num .numbox .num').html(parseInt($('.list .listcontent td:eq('+testtag+') #item #num .numbox .num').html())+parseInt($('.order .ornumber').val()));
				$('.result .rescontent .totalbox #timediv #time').html(parseInt($('.result .rescontent .totalbox #timediv #time').html())+parseInt($('.order .ornumber').val()));
				$('.result .rescontent .listbox #list #div #itemlist:eq('+testtag+') td[id="number"]').html($('.list .listcontent td:eq('+testtag+') #item #num .numbox .num').html());
				$('.result .rescontent .listbox #list #div #itemlist:eq('+testtag+') input[name^="number"]').val($('.result .rescontent .listbox #list #div #itemlist:eq('+testtag+') div[id="number"]').html());
				if($('.order #disbox #view #dismoney').val().length>0){
					$('.total .tmoy').html(Number($('.total .tmoy').html())+(Number($('.order .ornumber').val())*Number($('.listcontent td:eq('+testtag+') input[name="money[]"]').val()))+Number($('.order #disbox #view #dismoney').val()));
					$('.result .rescontent .totalbox #moneydiv #money').html($('.total .tmoy').html());
				}
				else{
					$('.total .tmoy').html(Number($('.total .tmoy').html())+(Number($('.order .ornumber').val())*Number($('.listcontent td:eq('+testtag+') input[name="money[]"]').val())));
					$('.result .rescontent .totalbox #moneydiv #money').html($('.total .tmoy').html());
				}
				$('.result .rescontent .listbox #list #div #itemlist:eq('+testtag+') td[id="money"]').html(Number($('.result .rescontent .listbox #list #div #itemlist:eq('+testtag+') td[id="money"]').html())+Number(Number($('.order .ornumber').val())*Number($('.listcontent td:eq('+testtag+') input[name="money[]"]').val())));
				//$('.listcontent td:eq('+testtag+') input[name="number[]"]').val(parseInt($('.listcontent td:eq('+testtag+') input[name="number[]"]').val())+parseInt($('.order .ornumber').val()));
			}
			else{
				temptasteno='<input type="hidden" id="selecttasteno" name="selecttasteno[]" value="'+tasteno+'">';
				
				$('.result .rescontent .totalbox #timediv #time').html(parseInt($('.result .rescontent .totalbox #timediv #time').html())+parseInt($('.order .ornumber').val()));
				if($('.order #disbox #view #dismoney').val().length>0){
					$('.total .tmoy').html(Number($('.total .tmoy').html())+(Number($('.order .ornumber').val())*(Number($('.order #orvalue').val())+Number(tastemoney)))+Number($('.order #disbox #view #dismoney').val()));

					$('.result .rescontent .totalbox #moneydiv #money').html($('.total .tmoy').html());
					$('.result .rescontent .totalbox #moneydiv #total').val(Number($('.result .rescontent .totalbox #moneydiv #money').html())+Number($('.order #disbox #view #dismoney').val()));
				}
				else{
					$('.total .tmoy').html(Number($('.total .tmoy').html())+(Number($('.order .ornumber').val())*(Number($('.order #orvalue').val())+Number(tastemoney))));

					$('.result .rescontent .totalbox #moneydiv #money').html($('.total .tmoy').html());
					$('.result .rescontent .totalbox #moneydiv #total').val(Number($('.result .rescontent .totalbox #moneydiv #money').html()));					
				}
				
				
				var maxrow=$('.result .rescontent .listbox #list #div tr').length-1;
				while(parseInt(maxrow)>=0&&$('.result .rescontent .listbox #list #div tr:eq('+maxrow+') td:eq(0)').html()==''){
					maxrow--;
				}
				if(maxrow>=-1&&maxrow<7){
				}
				else{
					$('.result .rescontent .listbox #list #div').append("<tr id='itemlist' style='width:100%; min-height:calc((1267 / 1920 * 1280px) / 8 - 4px);'><td id='name' style='height:calc((1267 / 1920 * 1280px) / 8 - 2px);'></td><td id='number' style='width:20%;height:calc((1267 / 1920 * 1280px) / 8 - 2px);'></td><td id='price' style='width:20%;height:calc((1267 / 1920 * 1280px) / 8 - 2px);'></td><td id='money' style='width:20%;height:calc((1267 / 1920 * 1280px) / 8 - 2px);'></td></tr>");
				}
				$('.result .rescontent .listbox #list #div tr:eq('+(parseInt(maxrow)+1)+') td:eq(0)').html('<ul><li><span id="itemname">'+$('.chose #name').html()+'('+$('.order #orvaname').val()+')</span></li>'+temptaste+'</ul>'+'<input type="hidden" id="typeno" name="typeno[]" value="'+$('.food:eq('+findex+') .typeno').val()+'"><input type="hidden" id="type" name="type[]" value="'+$('.food:eq('+findex+') .typename').val()+'"><input type="hidden" id="no" name="no[]" value="'+$('.food:eq('+findex+') .inumber').val()+'"><input type="hidden" name="name[]" value="'+$('.chose #name').html()+'"><input type="hidden" id="mname" name="mname[]" value="'+$('.order #orvaname').val()+'"><input type="hidden" id="initmoney" name="unitprice[]" value="'+$('.order #orvalue').val()+'"><input type="hidden" id="money" name="money[]" value="'+(parseInt($('.order #orvalue').val())+parseInt(tastemoney))+'"><input type="hidden" name="number[]" value="'+$('.order .ornumber').val()+'"><input type="hidden" name="subtotal[]" value="'+(parseInt($('.order .ornumber').val())*(parseInt($('.order #orvalue').val())+parseInt(tastemoney)))+'">'+temptasteno+'<input type="hidden" name="mcounter[]" value="'+$('.chose .mbutton').length+'">');
				$('.result .rescontent .listbox #list #div tr:eq('+(parseInt(maxrow)+1)+') td:eq(1)').html($('.order .ornumber').val());
				$('.result .rescontent .listbox #list #div tr:eq('+(parseInt(maxrow)+1)+') td:eq(2)').html($('.order #orvalue').val());
				$('.result .rescontent .listbox #list #div tr:eq('+(parseInt(maxrow)+1)+') td:eq(3)').html((parseInt($('.order .ornumber').val())*(parseInt($('.order #orvalue').val())+parseInt(tastemoney))));

				
			}
			var cyitem='<td><div id="item" class="img"><div id="name"><ul><li><span id="itemname">'+$('.chose #name').html()+'</span>';
			cyitem=cyitem+'&nbsp;'+$('#frontunit').val()+ornamemname+$('.order #orvalue').val()+$('#unit').val();
			cyitem=cyitem+'</li>'+temptaste+'</ul></div>';
			if($('.order #disbox #view #dismoney').val().length>0){
				cyitem=cyitem+'<div id="dis"><div class="disbox">折扣&nbsp;<span class="dis">'+$('.order #disbox #view #dismoney').val()+'</span></div></div>';
			}
			else{
			}
			cyitem=cyitem+'<div id="num"><div class="numbox">數量&nbsp;<span class="num">'+$('.order .ornumber').val()+'</span>&nbsp;份</div></div></div><input type="hidden" name="typeno[]" value="'+$('.food:eq('+findex+') .typeno').val()+'"><input type="hidden" name="type[]" value="'+$('.food:eq('+findex+') .type').val()+'"><input type="hidden" name="no[]" value="'+$('.food:eq('+findex+') .inumber').val()+'"><input type="hidden" name="name[]" value="'+$('.chose #name').html()+'"><input type="hidden" name="mname[]" value="'+$('.order #orvaname').val()+'"><input type="hidden" name="unitprice[]" value="'+$('.order #orvalue').val()+'"><input type="hidden" name="money[]" value="'+(parseInt($('.order #orvalue').val())+parseInt(tastemoney))+'"><input type="hidden" name="number[]" value="'+parseInt($('.order .ornumber').val())+'"><input type="hidden" name="subtotal[]" value="'+(parseInt($('.order .ornumber').val())*(parseInt($('.order #orvalue').val())+parseInt(tastemoney)))+'"><input type="hidden" name="taste[]" value="'+tasteno+'"><input type="hidden" name="tastename[]" value="'+tastename+'"><input type="hidden" name="tasteprice[]" value="'+tasteprice+'"><input type="hidden" name="tastenumber[]" value="'+tastenumber+'"><input type="hidden" name="tastemoney[]" value="'+tastemoney+'"><input type="hidden" name="mcounter" value="'+$('.chose .money .mbutton').length+'"></td>';
			$('.list .listcontent .box').append(cyitem);
			order.dialog('close');
			chose.dialog('close');
		}
	}
	function editsubutton(){//將修改後的資料，填回相關欄位中
		var temptaste='';
		var temptastemoney=0;
		var money=0;
		var index=$('.editbox input[name="index"]').val();
		$('.foodbox #number'+$('.box td:eq('+index+') input[name="no[]"]').val()+' .fdimgbox .plus').html(parseInt($('.foodbox #number'+$('.box td:eq('+index+') input[name="no[]"]').val()+' .fdimgbox .plus').html())-parseInt($('.box td:eq('+index+') input[name="number[]"]').val())+parseInt($('.editbox .orviewnum').html()));//修改產品圖右下角之總數字
		if(parseInt($('.foodbox #number'+$('.box td:eq('+index+') input[name="no[]"]').val()+' .fdimgbox .plus').html())==0){
			$('.foodbox #number'+$('.box td:eq('+index+') input[name="no[]"]').val()+' .fdimgbox .plus').html('<img src="../database/img/wplus.png">');
		}
		else{
		}
		$('.result .rescontent .totalbox #timediv #time').html(parseInt($('.result .rescontent .totalbox #timediv #time').html())-parseInt($('.box td:eq('+index+') input[name="number[]"]').val())+parseInt($('.editbox .orviewnum').html()));
		for(var i=0;i<$('.editbox #ortaste .tastecheck').length;i++){
			if($('.editbox #taste'+i+':checked').length>0){
				if(temptaste.length>0){
					temptaste=temptaste+',';
				}
				else{
				}
				temptaste=temptaste+$('.editbox #taste'+i).val();
				temptastemoney=parseInt(temptastemoney)+parseInt($('.editbox #tmoney'+i).val());
			}
			else{
			}
		}
		if($('.editbox .orviewnum').html()==0){//數量為0，直接刪除
			$('.finalfun .total .tmoy').html(parseInt($('.finalfun .total .tmoy').html())-parseInt($('.listcontent .box td:eq('+index+') input[name="number[]"]').val())*parseInt($('.listcontent .box td:eq('+index+') input[name="money[]"]').val()));
			$('.listcontent .box td:eq('+index+')').remove();
			$('.result #itemlist:eq('+index+')').remove();
		}
		else{
			if($('.box td:eq('+index+') input[name="taste[]"]').val()==temptaste){//口味選項不變，變更同項目數量
				$('.finalfun .total .tmoy').html(parseInt($('.finalfun .total .tmoy').html())+(parseInt($('.editbox .orviewnum').html())-parseInt($('.listcontent .box td:eq('+index+') input[name="number[]"]').val()))*parseInt($('.listcontent .box td:eq('+index+') input[name="money[]"]').val()));
				$('.listcontent .box td:eq('+index+') #item #num .numbox .num').html($('.editbox .orviewnum').html());
				$('.listcontent td:eq('+index+') input[name="number[]"]').val($('.editbox .orviewnum').html());
				$('.listcontent td:eq('+index+') input[name="subtotal[]"]').val(parseInt($('.editbox .orviewnum').html())*parseInt($('.listcontent .box td:eq('+index+') input[name="money[]"]').val()));
				$('.result #itemlist:eq('+index+') #number').html($('.editbox .orviewnum').html());
				$('.result #itemlist:eq('+index+') input[name="number"]').val($('.editbox .orviewnum').html());
				$('.result #itemlist:eq('+index+') #money').html(parseInt($('.result #itemlist:eq('+index+') #number').html())*parseInt($('.result #itemlist:eq('+index+') input[name="money"]').val()));
			}
			else{
				var noindex=-1;
				for(var i=0;i<$('.box td').length;i++){//尋找相同產品及口味選項
					if(i==index){
					}
					else{
						if($('.listcontent td:eq('+i+') input[name="no[]"]').val()==$('.listcontent .box td:eq('+index+') input[name="no[]"]').val() && $('.listcontent .box td:eq('+i+') input[name="taste[]"]').val()==temptaste){
							noindex=i;
							break;
						}
						else{
						}
					}
				}
				$('.finalfun .total .tmoy').html(parseInt($('.finalfun .total .tmoy').html())-parseInt($('.listcontent .box td:eq('+index+') input[name="number[]"]').val())*parseInt($('.listcontent .box td:eq('+index+') input[name="money[]"]').val()));
				if(noindex==-1){
					$('.box td:eq('+index+') input[name="money[]"]').val(parseInt($('.box td:eq('+index+') input[name="unitprice[]"]').val())+parseInt(temptastemoney));
					$('.box td:eq('+index+') #name #money').val(parseInt($('.box td:eq('+index+') input[name="unitprice[]"]').val())+parseInt(temptastemoney));
					$('.box td:eq('+index+') input[name="number[]"]').val($('.editbox .orviewnum').html());
					$('.box td:eq('+index+') #num .numbox .num').html($('.box td:eq('+index+') input[name="number[]"]').val());
					$('.box td:eq('+index+') input[name="taste[]"]').val(temptaste);
					if(temptaste.length==0){
					$('.box td:eq('+index+') #item #name ul ul').remove();
					}
					else{
						$.ajax({//修改明細餐點備註(加料)
							url:'./tool/gettaste.ajax.php',
							method:'post',
							data:{company:$('input[name="basiccompany"]').val(),dep:$('input[name="basicdep"]').val(),taste:temptaste},
							dataType:'json',
							success:function(value){
								var temtaste='<ul>';
								$.each(value,function(index,v){
									temtaste=temtaste+'<li><strong>'+v['name'];
									if(parseInt(v['money'])>0){
										temtaste=temtaste+'('+$('#frontunit').val()+v['money']+$('#unit').val()+')';
									}
									else{
									}
									temtaste=temtaste+'</strong></li>';
								});
								temtaste=temtaste+'</ul>';
								if($('.box td:eq('+index+') #item #name ul ul').length>0){
									$('.box td:eq('+index+') #item #name ul ul').remove();
								}
								else{
								}
								$('.box td:eq('+index+') #item #name ul').append(temtaste);
							}
						});
					}
					money=$('.box td:eq('+index+') input[name="money[]"]').val();
				}
				else{
					$('.box td:eq('+noindex+') input[name="number[]"]').val(parseInt($('.box td:eq('+noindex+') input[name="number[]"]').val())+parseInt($('.editbox .orviewnum').html()));
					$('.box td:eq('+noindex+') #item #num .numbox .num').html($('.box td:eq('+noindex+') input[name="number[]"]').val());
					money=$('.box td:eq('+noindex+') input[name="money[]"]').val();
					$('.box td:eq('+index+')').remove();
				}
				$('.finalfun .total .tmoy').html(parseInt($('.finalfun .total .tmoy').html())+parseInt($('.editbox .orviewnum').html())*parseInt(money));
			}
			$('.result #itemlist:eq('+index+') #number').html($('.editbox .orviewnum').html());
			$('.result #itemlist:eq('+index+') input[name="number[]"]').val($('.editbox .orviewnum').html());
			$('.result #itemlist:eq('+index+') #money').html(parseInt($('.result #itemlist:eq('+index+') #number').html())*parseInt($('.result #itemlist:eq('+index+') input[name="money[]"]').val()));
		}
		$('.result .rescontent .totalbox #moneydiv #money').html(parseInt($('.finalfun .total .tmoy').html()));
		$('.result .rescontent .totalbox #moneydiv #total').val(parseInt($('.finalfun .total .tmoy').html()));
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
	$(document).on('touchend','.foodbox',function(e){//產品列表左右翻頁
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
	$(document).on('touchend','.food',function(e){//點選產品撈出相關資料
		console.log('touch');
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t1 = touch.pageX;
		ty1 = touch.pageY;
		Xaxis=t1;
		Yaxis=ty1;
		if(t1-t<=30 && t1-t>=-30 && ty1-ty<=30 && ty1-ty>=-30){
			$.ajax({
				url:'./tool/chose.ajax.php',
				method:'post',
				data:{company:$('input[name="basiccompany"]').val(),dep:$('input[name="basicdep"]').val(),inumber:$('.inumber:eq('+findex+')').val(),temp:''},
				dataType:'json',
				success:function(value){
					$('.chose #img').prop('src',$('.food:eq('+findex+') .foodimg').prop('src'));
					$('.chose #name').html(value[0]['name']);
					$('.chose input[name="inumber"]').val($('.inumber:eq('+findex+')').val());
					for(var i=1;i<=6;i++){
						if(value[0]['introtitle'+i].length>0){
							var introtitle=value[0]['introtitle'+i].replace(/\n/g,'<br>');
							$('.chose #introduction #introtitle'+i).css({'font-weight':'bold'});
							$('.chose #introduction #introtitle'+i).html(introtitle);
						}
						else{
							$('.chose #introduction #introtitle'+i).html('');
						}
						if(value[0]['introduction'+i].length>0){
							var introduction=value[0]['introduction'+i].replace(/\n/g,'<br>');
							$('.chose #introduction #introduction'+i).css({'font-weight':'bold'});
							$('.chose #introduction #introduction'+i).html(introduction);
						}
						else{
							$('.chose #introduction #introtitle'+i).html('');
						}
						if(value[0]['introcolor'+i].length>0){
							$('.chose #introduction #color'+i).css({'color':value[0]['introcolor'+i]});
						}
						else{
							$('.chose #introduction #color'+i).css({'color':'#000000'});
						}
					}
					$('.chose .money').html('');
					for(var i=1;i<=value[0]['mcounter'];i++){
						var t='<div style="width:calc(100% / 2);height:calc(100% / 8);float:left;margin:calc(100 / 16 * 1vw) 0;"><button id="'+i+'" class="mbutton" style="width:calc(100% - 10px);height:calc(100% - 10px);margin:5px;border:3px solid #898989;border-radius: 7px;"><div style="width:100%;height:70%;font-size:calc(60 / 1280 * 100vw);font-weight:bold;">';
						if(value[0]['mname'+i].length>0){
							t=t+value[0]['mname'+i]+'</div><div style="width:100%;height:30%;font-size:calc(35 / 1280 * 100vw);font-weight:bold;color:#898989;">'+$('#frontunit').val()+' '+value[0]['money'+i]+$('#unit').val()+'</div></button><input type="hidden" class="mname'+i+'" value="'+value[0]['mname'+i]+'"><input type="hidden" class="mvalue'+i+'" value="'+value[0]['money'+i]+'"></div>';
						}
						else{
							t=t+$('#frontunit').val()+' '+value[0]['money'+i]+$('#unit').val()+'</div><div style="width:100%;height:30%;font-size:calc(35 / 1280 * 100vw);font-weight:bold;color:#898989;"></div></button><input type="hidden" class="mname'+i+'" value="'+value[0]['mname'+i]+'"><input type="hidden" class="mvalue'+i+'" value="'+value[0]['money'+i]+'"></div>';
						}
						$('.chose .money').append(t);
					}
					if(value[0]['mcounter']==1){
						$('.chose .mbutton:eq(0)').trigger('click');
					}
					else{
					}
				},
				error:function(e){
					console.log(e);
				}
			});
			chose.dialog('open');
		}
		else{
		}
	});
	$(document).on('click','.food',function(){
		console.log('click');
		findex=$('.food').index(this);
		$.ajax({
			url:'./tool/chose.ajax.php',
			method:'post',
			data:{company:$('input[name="basiccompany"]').val(),dep:$('input[name="basicdep"]').val(),inumber:$('.inumber:eq('+findex+')').val(),temp:''},
			dataType:'json',
			success:function(value){
				$('.chose #img').prop('src',$('.food:eq('+findex+') .foodimg').prop('src'));
				$('.chose #name').html(value[0]['name']);
				$('.chose input[name="inumber"]').val($('.inumber:eq('+findex+')').val());
				for(var i=1;i<=6;i++){
					if(value[0]['introtitle'+i].length>0){
						var introtitle=value[0]['introtitle'+i].replace(/\n/g,'<br>');
						$('.chose #introduction #introtitle'+i).css({'font-weight':'bold'});
						$('.chose #introduction #introtitle'+i).html(introtitle);
					}
					else{
						$('.chose #introduction #introtitle'+i).html('');
					}
					if(value[0]['introduction'+i].length>0){
						var introduction=value[0]['introduction'+i].replace(/\n/g,'<br>');
						$('.chose #introduction #introduction'+i).css({'font-weight':'bold'});
						$('.chose #introduction #introduction'+i).html(introduction);
					}
					else{
						$('.chose #introduction #introtitle'+i).html('');
					}
					if(value[0]['introcolor'+i].length>0){
						$('.chose #introduction #color'+i).css({'color':value[0]['introcolor'+i]});
					}
					else{
						$('.chose #introduction #color'+i).css({'color':'#000000'});
					}
				}
				$('.chose .money').html('');
				for(var i=1;i<=value[0]['mcounter'];i++){
					var t='<div style="width:calc(100% / 2);height:calc(100% / 8);float:left;margin:calc(100 / 16 * 1vw) 0;"><button id="'+i+'" class="mbutton" style="width:calc(100% - 10px);height:calc(100% - 10px);margin:5px;border:3px solid #898989;border-radius: 7px;"><div style="width:100%;height:70%;font-size:calc(60 / 1280 * 100vw);font-weight:bold;">';
					if(value[0]['mname'+i].length>0){
						t=t+value[0]['mname'+i]+'</div><div style="width:100%;height:30%;font-size:calc(35 / 1280 * 100vw);font-weight:bold;color:#898989;">'+$('#frontunit').val()+' '+value[0]['money'+i]+$('#unit').val()+'</div></button><input type="hidden" class="mname'+i+'" value="'+value[0]['mname'+i]+'"><input type="hidden" class="mvalue'+i+'" value="'+value[0]['money'+i]+'"></div>';
					}
					else{
						t=t+$('#frontunit').val()+' '+value[0]['money'+i]+$('#unit').val()+'</div><div style="width:100%;height:30%;font-size:calc(35 / 1280 * 100vw);font-weight:bold;color:#898989;"></div></button><input type="hidden" class="mname'+i+'" value="'+value[0]['mname'+i]+'"><input type="hidden" class="mvalue'+i+'" value="'+value[0]['money'+i]+'"></div>';
					}
					$('.chose .money').append(t);
				}
				if(value[0]['mcounter']==1){
					$('.chose .mbutton:eq(0)').trigger('click');
				}
				else{
				}
			},
			error:function(e){
				console.log(e);
			}
		});
		chose.dialog('open');
	});
	$(document).on('click','.chose .cancel',function(){//價格－'取消'按鈕
		chose.dialog('close');
	});
	$(document).on('click','.chose .mbutton',function(){//價格－'價格'按鈕
		var orbuttonname=$(this).val();
		var orbuttonmname=$('.chose .mname'+$(this).prop('id')).val();
		var orbuttonvalue=$('.chose .mvalue'+$(this).prop('id')).val();
		$.ajax({
			url:'./tool/order.ajax.php',
			method:'post',
			title:$('.chose #name').html(),
			data:{company:$('input[name="basiccompany"]').val(),dep:$('input[name="basicdep"]').val(),inumber:$('.chose input[name="inumber"]').val()},
			dataType:'json',
			success:function(value){
				/*初始設定*/
				$('.order #orimg').attr('src','');
				$('.order #orname').html('');
				$('.order #con').html('');
				$('.order #ortaste').html('');
				$('.order .orviewnum').html('1');
				$('.order .ornumber').val('1');
				/**********/
				$('.order #orimg').attr('src',$('.chose #img').attr('src'));
				$('.order #orname').html($('.chose #name').html()+'<input type="hidden" id="orvaname" value="'+orbuttonmname+'"><input type="hidden" id="orvalue" value="'+orbuttonvalue+'">');
				$('.order #ormname').html(orbuttonmname+$('#frontunit').val()+' '+orbuttonvalue+$('#unit').val());
				$('.order #con').html($('.chose #introduction').html());

				$('.order #disbox #view #initmoney').val(orbuttonvalue);
				$('.order #disbox #view #money').val(orbuttonvalue);

				if(typeof value[0]['taste']=='undefined'){
				}
				else{
					$('.order #ortaste').html('');
					$('.order #ortaste').css({'background-color':'#ffffff'});
					var temp=0;
					var temptaste='';
					for(var i=0;i<value.length;i++){
						if(value[i]['money']>0){
							temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:90px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:90px;float:left;word-break:break-all;"><strong><label style="margin:auto 0;font-size:50px;color:#3e3a39;"><span id="taste'+i+'name">'+value[i]['name']+'('+$('#frontunit').val()+value[i]['money']+$('#unit').val()+')</span>'+'</label></strong><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" style="display:none;" id="taste'+i+'" class="tastecheck" value="'+value[i]['taste']+'"><img id="chimg'+i+'" class="chimg"  src="../database/img/noch.png" style="float:left;position:absolute;top:34px;right:10px;"></div>';
						}
						else{
							temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:90px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:90px;float:left;word-break: break-all;"><strong><label style="font-size:50px;color:#3e3a39;"><span id="taste'+i+'name">'+value[i]['name']+'</span></label></strong><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" id="taste'+i+'" class="tastecheck" style="display:none;" value="'+value[i]['taste']+'"><img id="chimg'+i+'" class="chimg" src="../database/img/noch.png" style="float:left;position:absolute;top:34px;right:10px;"></div>';
						}
					}
					if(temptaste==''){
					}
					else{
						$('.order #ortaste').html('<div style="width:100%;height:calc(100% - 25px);overflow-y:auto;">111'+temptaste+'</div>');
					}
				}
			},
			error:function(d){
				console.log(d);
			}
		});
		order.dialog('open');
	});
	$(document).on('click','.order #buttons #taste',function(){//點餐－'加料'按鈕
		$('.order #disbox').css({'display':'none'});
		if($('.order #ortastebox').css('display')=='none'){
			$('.order #ortastebox').css({'display':''});
		}
		else{
			$('.order #ortastebox').css({'display':'none'});
		}
	});
	$(document).on('click','.order #buttons #cancel',function(){//點餐－'取消'按鈕
		$('.order #ortastebox').css({'display':'none'});
		if($('.chose .money .mbutton').length==1){
			chose.dialog('close');
			order.dialog('close');
		}
		else{
			order.dialog('close');
		}
		$('.order .funbox').css({'display':''});
		$('.order #disbox').css({'display':'none'});
		$('.order #disbox #view input[type="text"]').val('');
	});
	$(document).on('click','.order #buttons #sub',function(){//點餐－'送出'按鈕
		$('.order #ortastebox').css({'display':'none'});
		orsubutton();
		$('.order .funbox').css({'display':''});
		$('.order #disbox').css({'display':'none'});
		$('.order #disbox #view input[type="text"]').val('');
	});
	/*促銷*/
	$('.order .funbox #dis').click(function(){
		$('.order .funbox').css({'display':'none'});
		$('.order #disbox').css({'display':''});
	});
	$('.order #disbox #keybord button').click(function(){//輸入鍵盤
		if($(this).val()=='AC'){
			$('.order #disbox #view #inputbox').val('');
			$('.order #disbox #view #dismoney').val('');
			$('.order #disbox #view #money').val($('.order #disbox #view #initmoney').val());
		}
		else{
			$('.order #disbox #view #inputbox').val($('.order #disbox #view #inputbox').val()+$(this).val());
		}
	});
	$('.order #disbox #keybord #free').click(function(){//招待
		$('.order #disbox #view #inputbox').val('');
		$('.order #disbox #view #dismoney').val('-'+$('.order #disbox #view #initmoney').val());
		$('.order #disbox #view #money').val('0');
	});
	$('.order #disbox #keybord #dis1').click(function(){//折讓
		if(Number($('.order #disbox #view #inputbox').val())>0){
			if(Number($('.order #disbox #view #inputbox').val())>=Number($('.order #disbox #view #initmoney').val())){
				$('.order #disbox #view #dismoney').val('-'+$('.order #disbox #view #initmoney').val());
				$('.order #disbox #view #money').val('0');
			}
			else{
				$('.order #disbox #view #dismoney').val('-'+$('.order #disbox #view #inputbox').val());
				$('.order #disbox #view #money').val(Number($('.order #disbox #view #initmoney').val())-Number($('.order #disbox #view #inputbox').val()));
			}
			$('.order #disbox #view #inputbox').val('');
		}
		else{
		}
	});
	$('.order #disbox #keybord #dis2').click(function(){//折扣
		if(Number($('.order #disbox #view #inputbox').val())>0){
			if(Number($('.order #disbox #view #inputbox').val())>100){
				$('.order #disbox #view #dismoney').val('-'+$('.order #disbox #view #initmoney').val());
				$('.order #disbox #view #money').val('0');
			}
			else{
				$('.order #disbox #view #dismoney').val(Number($('.order #disbox #view #initmoney').val())*($('.order #disbox #view #inputbox').val()/100)-Number($('.order #disbox #view #initmoney').val()));
				$('.order #disbox #view #money').val(Number($('.order #disbox #view #initmoney').val())*($('.order #disbox #view #inputbox').val()/100));
			}
			$('.order #disbox #view #inputbox').val('');
			//$('.order #disbox #view #dismoney').val('-'+$('.order #disbox #view #initmoney').val());
			//$('.order #disbox #view #money').val('0');
		}
		else{
		}
	});
	$('.order #disbox #view #return').click(function(){//取消折扣
		$('.order #disbox #view #inputbox').val('');
		$('.order #disbox #view #dismoney').val('');
		$('.order #disbox #view #money').val($('.order #disbox #view #initmoney').val());
		$('.order .funbox').css({'display':''});
		$('.order #disbox').css({'display':'none'});
	});
	/*促銷*/
	$('.editbox .funbox #dis').click(function(){
		$('.editbox .funbox').css({'display':'none'});
		$('.editbox #disbox').css({'display':''});
	});
	$('.editbox #disbox #keybord button').click(function(){//輸入鍵盤
		if($(this).val()=='AC'){
			$('.editbox #disbox #view #inputbox').val('');
			$('.editbox #disbox #view #dismoney').val('');
			$('.editbox #disbox #view #money').val($('.editbox #disbox #view #initmoney').val());
		}
		else{
			$('.editbox #disbox #view #inputbox').val($('.editbox #disbox #view #inputbox').val()+$(this).val());
		}
	});
	$('.editbox #disbox #keybord #free').click(function(){//招待
		$('.editbox #disbox #view #inputbox').val('');
		$('.editbox #disbox #view #dismoney').val('-'+$('.editbox #disbox #view #initmoney').val());
		$('.editbox #disbox #view #money').val('0');
	});
	$('.editbox #disbox #keybord #dis1').click(function(){//折讓
		if(Number($('.editbox #disbox #view #inputbox').val())>0){
			if(Number($('.editbox #disbox #view #inputbox').val())>=Number($('.editbox #disbox #view #initmoney').val())){
				$('.editbox #disbox #view #dismoney').val('-'+$('.editbox #disbox #view #initmoney').val());
				$('.editbox #disbox #view #money').val('0');
			}
			else{
				$('.editbox #disbox #view #dismoney').val('-'+$('.editbox #disbox #view #inputbox').val());
				$('.editbox #disbox #view #money').val(Number($('.editbox #disbox #view #initmoney').val())-Number($('.editbox #disbox #view #inputbox').val()));
			}
			$('.editbox #disbox #view #inputbox').val('');
		}
		else{
		}
	});
	$('.editbox #disbox #keybord #dis2').click(function(){//折扣
		if(Number($('.editbox #disbox #view #inputbox').val())>0){
			if(Number($('.editbox #disbox #view #inputbox').val())>100){
				$('.editbox #disbox #view #dismoney').val('-'+$('.editbox #disbox #view #initmoney').val());
				$('.editbox #disbox #view #money').val('0');
			}
			else{
				$('.editbox #disbox #view #dismoney').val(Number($('.editbox #disbox #view #initmoney').val())*($('.editbox #disbox #view #inputbox').val()/100)-Number($('.editbox #disbox #view #initmoney').val()));
				$('.editbox #disbox #view #money').val(Number($('.editbox #disbox #view #initmoney').val())*($('.editbox #disbox #view #inputbox').val()/100));
			}
			$('.editbox #disbox #view #inputbox').val('');
			//$('.editbox #disbox #view #dismoney').val('-'+$('.editbox #disbox #view #initmoney').val());
			//$('.editbox #disbox #view #money').val('0');
		}
		else{
		}
	});
	$('.editbox #disbox #view #return').click(function(){//取消折扣
		$('.editbox #disbox #view #inputbox').val('');
		$('.editbox #disbox #view #dismoney').val('');
		$('.editbox #disbox #view #money').val($('.editbox #disbox #view #initmoney').val());
		$('.editbox .funbox').css({'display':''});
		$('.editbox #disbox').css({'display':'none'});
	});
	$(document).on('click','.editbox #buttons #taste',function(){//修改－'加料'按鈕
		if($('.editbox #ortastebox').css('display')=='none'){
			$('.editbox #ortastebox').css({'display':''});
		}
		else{
			$('.editbox #ortastebox').css({'display':'none'});
		}
	});
	$(document).on('click','.editbox #buttons #cancel',function(){//修改－'取消'按鈕
		$('.editbox #ortastebox').css({'display':'none'});
		editbox.dialog('close');
	});
	$(document).on('click','.editbox #buttons #sub',function(){//修改－'送出'按鈕
		$('.editbox #ortastebox').css({'display':'none'});
		if($('.box td:eq('+findex+') input[name^="number"]').val()==0){
			$('.total .tmoy').html(parseInt($('.total .tmoy').html())-(parseInt($('.box td:eq('+findex+') input[name="money[]"]').val())*parseInt($('.box td:eq('+findex+') input[name^="number"]').val())));
			$('.result .rescontent .totalbox #timediv #time').html(parseInt($('.result .rescontent .totalbox #timediv #time').html())-parseInt($('.box td:eq('+findex+') input[name^="number"]').val()));
			$('.result .rescontent .totalbox #moneydiv #money').html(parseInt($('.total .tmoy').html()));
			$('.result .rescontent .totalbox #moneydiv #total').val(parseInt($('.total .tmoy').html()));
			$('.foodbox #number'+$('.box td:eq('+findex+') input[name="no[]"]').val()+' .fdimgbox .plus').html('<img src="../database/img/wplus.png">');
			$('.box td:eq('+findex+')').remove();
			$('.result #list #itemlist:eq('+findex+')').remove();
			editbox.dialog('close');
			if($('.result .rescontent .listbox #list #div tr').length<8){
				$('.result .rescontent .listbox #list #div').append("<tr id='itemlist' style='width:100%; min-height:calc(1267px / 8 - 4px);'><td id='name' style='height:calc(1267px / 8 - 2px);'></td><td id='number' style='width:20%;height:calc(1267px / 8 - 2px);'></td><td id='price' style='width:20%;height:calc(1267px / 8 - 2px);'></td><td id='money' style='width:20%;height:calc(1267px / 8 - 2px);'></td></tr>");
			}
			else{
			}
		}
		else{
			editsubutton();
			editbox.dialog('close');
		}
	});
	$(document).on('click','.result #reset',function(){//明細總覽－'返回點餐'按鈕
		result.dialog('close');
	});
	$(document).on('click','.result #cancel',function(){//明細總覽－'取消'按鈕
		sysmeg.dialog('open')
	});
	$(document).on('click','.result #sub',function(){//明細總覽－'送出'按鈕
		if(parseInt($('.result .rescontent .totalbox #timediv #time').html())==0){
		}
		else{
			result.dialog('close');
			bill.dialog('open');
		}
	});
	$(document).on('click','.sysmeg #yes',function(){//系統訊息－'是'按鈕
		location.href='./bord2.php';
	});
	$(document).on('click','.sysmeg #no',function(){//系統訊息－'否'按鈕
		sysmeg.dialog('close');
	});
	$(document).on('click','.bill #print',function(){//結帳－'櫃台結帳'按鈕
		bill.dialog('close');
		print.dialog('open');
		$.ajax({
			url:'create.list.php',
			method:'post',
			data:$(".resultform").serialize(),
			dataType:'html',
			success:function(a){
				//console.log(a);
				location.href='./bord2.php';
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.bill #return',function(){//結帳－'返回點餐'按鈕
		bill.dialog('close');
	});
	$(document).on('click','.order #chlabel',function(){//點餐－'備註與加料'按鈕
		if($('.order #chlabel #taste'+$(this).find('input[id^="taste"]').attr('id').substr(5)+':checked').length>0){
			$('.order #chlabel #taste'+$(this).find('input[id^="taste"]').attr('id').substr(5)).prop('checked',false);
			$('.order #chlabel #chimg'+$(this).find('input[id^="taste"]').attr('id').substr(5)).attr('src','../database/img/noch.png');
			$('.order #chlabel #chimg'+$(this).find('input[id^="taste"]').attr('id').substr(5)).css({'top':'34px','right':'10px'});
		}
		else{
			$('.order #chlabel #taste'+$(this).find('input[id^="taste"]').attr('id').substr(5)).prop('checked',true);
			$('.order #chlabel #chimg'+$(this).find('input[id^="taste"]').attr('id').substr(5)).attr('src','../database/img/onch.png');
			$('.order #chlabel #chimg'+$(this).find('input[id^="taste"]').attr('id').substr(5)).css({'top':'0','right':'0'});
		}
	});
	$(document).on('click','.editbox #chlabel',function(){//修改－'備註與加料'按鈕
		if($('.editbox #chlabel #taste'+$(this).find('input[id^="taste"]').attr('id').substr(5)+':checked').length>0){
			$('.editbox #chlabel #taste'+$(this).find('input[id^="taste"]').attr('id').substr(5)).prop('checked',false);
			$('.editbox #chlabel #chimg'+$(this).find('input[id^="taste"]').attr('id').substr(5)).attr('src','../database/img/noch.png');
			$('.editbox #chlabel #chimg'+$(this).find('input[id^="taste"]').attr('id').substr(5)).css({'top':'34px','right':'10px'});
		}
		else{
			$('.editbox #chlabel #taste'+$(this).find('input[id^="taste"]').attr('id').substr(5)).prop('checked',true);
			$('.editbox #chlabel #chimg'+$(this).find('input[id^="taste"]').attr('id').substr(5)).attr('src','../database/img/onch.png');
			$('.editbox #chlabel #chimg'+$(this).find('input[id^="taste"]').attr('id').substr(5)).css({'top':'0','right':'0'});
		}
	});
	$(document).on('click','.order #orplus',function(){//點餐－'+'按鈕
		$('.order .orviewnum').html(parseInt($('.order .orviewnum').html())+1);
		$('.order .ornumber').val(parseInt($('.order .ornumber').val())+1);
		$('.order #disbox #view #inputbox').val('');
		$('.order #disbox #view #initmoney').val((parseInt($('.order .orviewnum').html())*$('.order #orvalue').val()));
		$('.order #disbox #view #dismoney').val('');
		$('.order #disbox #view #money').val((parseInt($('.order .orviewnum').html())*$('.order #orvalue').val()+Number($('.order #disbox #view #dismoney').val())+Number($('.order #disbox #view #dismoney').val())));
	});
	$(document).on('click','.order #ordiff',function(){//點餐－'-'按鈕
		if($('.order .ornumber').val()==0){
		}
		else{
			$('.order .orviewnum').html(parseInt($('.order .orviewnum').html())-1);
			$('.order .ornumber').val(parseInt($('.order .ornumber').val())-1);
			$('.order #disbox #view #inputbox').val('');
			$('.order #disbox #view #initmoney').val((parseInt($('.order .orviewnum').html())*$('.order #orvalue').val()));
			$('.order #disbox #view #dismoney').val('');
			$('.order #disbox #view #money').val((parseInt($('.order .orviewnum').html())*$('.order #orvalue').val()+Number($('.order #disbox #view #dismoney').val())+Number($('.order #disbox #view #dismoney').val())));
		}
	});
	$(document).on('click','.editbox #orplus',function(){//修改－'+'按鈕
		$('.editbox .orviewnum').html(parseInt($('.editbox .orviewnum').html())+1);
	});
	$(document).on('click','.editbox #ordiff',function(){//修改－'-'按鈕
		if($('.editbox .orviewnum').html()==0){
		}
		else{
			$('.editbox .orviewnum').html(parseInt($('.editbox .orviewnum').html())-1);
		}
	});
	/*$(document).on('click','.order .ornumber',function(){//數字鍵盤進入點
		$('.ordernumber #viewnumber').val($('.order .ornumber').val());
		//ordernumber.dialog('open');
	});*/
	/*$(document).on('click','.ordernumber .numbutton',function(){//數字鍵盤(暫無使用)
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
	});*/
	/*$('#getlist').on('touchstart',function(e){//前版使用結帳/明細
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
	});*/
	/*$(document).on('touchstart','.plusbun',function(e){//前版使用+-按鈕
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
			$('.total .tmoy').html(parseInt($('.total .tmoy').html())+parseInt($('.list #name:eq('+findex+') #money').val()));
			//$('#orderlist input[name="tmoy"]').val(parseInt($('#orderlist input[name="tmoy"]').val())+parseInt($('.list #name:eq('+findex+') #money').val()));
			$('listcontent td:eq('+findex+') input[name="number[]"]').val(parseInt($('.listcontent td:eq('+findex+') input[name="number[]"]').val())+1);
			//if($('.point').html().length==0){
			//	$('.point').html(1);
			//	$('.point').css({'background':'url(\"redpoint.png\")'});
			//}
			//else{
			//	$('.point').html(parseInt($('.point').html())+1);
			//}
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
					$('.total .tmoy').html(parseInt($('.total .tmoy').html())-parseInt($('.list #name:eq('+findex+') #money').val()));
					//$('#orderlist input[name="tmoy"]').val(parseInt($('#orderlist input[name="tmoy"]').val())-parseInt($('.list #name:eq('+findex+') #money').val()));
					$('.listcontent td:eq('+findex+') input[name="number[]"]').val(parseInt($('.listcontent td:eq('+findex+') input[name="number[]"]').val())-1);
					//if($('.point').html().length==0){
					//}
					//else{
					//	$('.point').html(parseInt($('.point').html())-1);
					//	if(parseInt($('.point').html())==0){
					//		$('.point').html('');
					//		$('.point').css({'background':''});
					//	}
					//	else{
					//	}
					//}
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
				$('.total .tmoy').html(parseInt($('.total .tmoy').html())-parseInt($('.list #name:eq('+findex+') #money').val()));
				//$('#orderlist input[name="tmoy"]').val(parseInt($('#orderlist input[name="tmoy"]').val())-parseInt($('.list #name:eq('+findex+') #money').val()));
				$('.listcontent td:eq('+findex+') input[name="number[]"]').val(parseInt($('.listcontent td:eq('+findex+') input[name="number[]"]').val())-1);
				//$('.point').html(parseInt($('.point').html())-1);
			}
		}
		else{
		}
	});*/
	$(document).on('touchstart','.box td',function(e){
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t = touch.pageX;
		ty = touch.pageY;
		findex=$('.box td').index(this);
	});
	$(document).on('touchend','.box td',function(e){//觸發成功－修改該項明細的資料
		logouttime=maxlogouttime;
		e.cancelable && e.preventDefault();
		var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		t1 = touch.pageX;
		ty1 = touch.pageY;
		if(t1-t<=30 && t1-t>=-30 && ty1-ty<=30 && ty1-ty>=-30){
			$('.editbox #orname').html($('.box td:eq('+findex+') #item #name #itemname').html());
			var orbuttonmname=$('.chose .mname'+$(this).attr('id')).val();
			var orbuttonvalue=$('.chose .mvalue'+$(this).attr('id')).val();
			$.ajax({
				url:'./tool/chose.ajax.php',
				method:'post',
				data:{company:$('input[name="basiccompany"]').val(),dep:$('input[name="basicdep"]').val(),inumber:$('.box td:eq('+findex+') input[name="no[]"]').val(),temp:''},
				dataType:'json',
				success:function(value){
					$('.editbox #con').html('<strong>'+value[0]['introduction']+'</strong>');
					$('.editbox #orimg').attr('src','../database/img/'+$('input[name="basiccompany"]').val()+'/'+value[0]['image']);
				}
			});
			$.ajax({
				url:'./tool/order.ajax.php',
				method:'post',
				title:'修改明細',
				data:{company:$('input[name="basiccompany"]').val(),dep:$('input[name="basicdep"]').val(),inumber:$('.box td:eq('+findex+') input[name="no[]"]').val()},
				dataType:'json',
				success:function(value){
					//初始設定
					$('.editbox #orimg').attr('src','');
					$('.editbox #orname').html('');
					$('.editbox #ormname').html('');
					$('.editbox #ortaste').html('');
					//
					$('.editbox input[name="index"]').val(findex);
					$('.editbox #orimg').attr('src','../database/img/'+$('input[name="basiccompany"]').val()+'/'+value[0]['img']);
					$('.editbox #orname').html(value[0]['itemname']+'<input type="hidden" id="orvaname" value="'+$('.box td:eq('+findex+') input[name="mname[]"]').val()+'"><input type="hidden" id="orvalue" value="'+$('.box td:eq('+findex+') input[name="unitprice[]"]').val()+'">');
					/*var strhtml=value[0]['itemname'];
					if($('.box td:eq('+findex+') input[name="mcounter"]').val()==1){
						strhtml=strhtml+'&nbsp;'+$('#frontunit').val()+$('.box td:eq('+findex+') input[name="unitprice[]"]').val()+$('#unit').val();
					}
					else{
						strhtml=strhtml+'&nbsp;'+$('.box td:eq('+findex+') input[name="mname[]"]').val()+$('#frontunit').val()+$('.box td:eq('+findex+') input[name="unitprice[]"]').val()+$('#unit').val();
					}
					strhtml=strhtml+'<input type="hidden" id="orvaname" value="'+$('.box td:eq('+findex+') input[name="mname[]"]').val()+'"><input type="hidden" id="orvalue" value="'+$('.box td:eq('+findex+') input[name="unitprice[]"]').val()+'">';
					$('.editbox #orname').html(strhtml);*/
					$('.editbox #ormname').html($('.box td:eq('+findex+') input[name="mname[]"]').val()+$('#frontunit').val()+$('.box td:eq('+findex+') input[name="unitprice[]"]').val()+$('#unit').val());
					$('.editbox .orviewnum').html($('.box td:eq('+findex+') input[name="number[]"]').val());
					if(typeof value[0]['taste']=='undefined'){
					}
					else{
						var splitchosetaste=$('.box td:eq('+findex+') input[name="taste[]"]').val().split(',');
						$('.order #ortaste').html('');
						$('.order #ortaste').css({'background-color':'#ffffff'});
						var temp=0;
						var temptaste='<div style="width:100%;height:calc(100% - 25px);overflow-y:auto;">';
						for(var i=0;i<value.length;i++){
							if(value[i]['money']>0){
								if($.inArray(value[i]['taste'],splitchosetaste)>=0){
									temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:111px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:111px;float:left;word-break:break-all;"><label style="margin:auto 0;font-size:50px;color:#3e3a39;"><span id="taste'+i+'name"><strong>'+value[i]['name']+'('+$('#frontunit').val()+value[i]['money']+$('#unit').val()+')</strong></span>'+'</label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" style="display:none;" id="taste'+i+'" class="tastecheck" value="'+value[i]['taste']+'" checked=true><img id="chimg'+i+'" class="chimg"  src="../database/img/onch.png" style="float:left;position:absolute;top:0;right:0;"></div>';
								}
								else{
									temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:111px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:111px;float:left;word-break:break-all;"><label style="margin:auto 0;font-size:50px;color:#3e3a39;"><span id="taste'+i+'name"><strong>'+value[i]['name']+'('+$('#frontunit').val()+value[i]['money']+$('#unit').val()+')</strong></span>'+'</label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" style="display:none;" id="taste'+i+'" class="tastecheck" value="'+value[i]['taste']+'"><img id="chimg'+i+'" class="chimg"  src="../database/img/noch.png" style="float:left;position:absolute;top:34px;right:10px;"></div>';
								}
							}
							else{
								if($.inArray(value[i]['taste'],splitchosetaste)>=0){
									temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:111px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:111px;float:left;word-break: break-all;"><label style="font-size:50px;color:#3e3a39;"><span id="taste'+i+'name"><strong>'+value[i]['name']+'</strong></span></label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" id="taste'+i+'" class="tastecheck" style="display:none;" value="'+value[i]['taste']+'" checked=true><img id="chimg'+i+'" class="chimg" src="../database/img/onch.png" style="float:left;position:absolute;top:0;right:0;"></div>';
								}
								else{
									temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:111px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:111px;float:left;word-break: break-all;"><label style="font-size:50px;color:#3e3a39;"><span id="taste'+i+'name"><strong>'+value[i]['name']+'</strong></span></label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" id="taste'+i+'" class="tastecheck" style="display:none;" value="'+value[i]['taste']+'"><img id="chimg'+i+'" class="chimg" src="../database/img/noch.png" style="float:left;position:absolute;top:34px;right:10px;"></div>';
								}
							}
						}
						$('.editbox #ortaste').append(temptaste+'</div>');
					}
				},
				error:function(d){
					console.log(d);
				}
			});
			editbox.dialog('open');
		}
		else{
		}
	});
	$(document).on('click','.box td',function(){
		$('.editbox #orname').html($('.box td:eq('+findex+') #item #name #itemname').html());
		var orbuttonmname=$('.chose .mname'+$(this).attr('id')).val();
		var orbuttonvalue=$('.chose .mvalue'+$(this).attr('id')).val();
		findex=$('.box td').index(this);
		$.ajax({
			url:'./tool/chose.ajax.php',
			method:'post',
			data:{company:$('input[name="basiccompany"]').val(),dep:$('input[name="basicdep"]').val(),inumber:$('.box td:eq('+findex+') input[name="no[]"]').val(),temp:''},
			dataType:'json',
			success:function(value){
				for(var i=1;i<=6;i++){
					if(value[0]['introtitle'+i].length>0){
						var introtitle=value[0]['introtitle'+i].replace(/\n/g,'<br>');
						$('.editbox #con #introtitle'+i).css({'font-weight':'bold'});
						$('.editbox #con #introtitle'+i).html(introtitle);
					}
					else{
						$('.editbox #con #introtitle'+i).html('');
					}
					if(value[0]['introduction'+i].length>0){
						var introduction=value[0]['introduction'+i].replace(/\n/g,'<br>');
						$('.editbox #con #introduction'+i).css({'font-weight':'bold'});
						$('.editbox #con #introduction'+i).html(introduction);
					}
					else{
						$('.editbox #con #introtitle'+i).html('');
					}
					if(value[0]['introcolor'+i].length>0){
						$('.editbox #con #color'+i).css({'color':value[0]['introcolor'+i]});
					}
					else{
						$('.editbox #con #color'+i).css({'color':'#000000'});
					}
				}
				//$('.editbox #con').html('<strong>'+value[0]['introduction']+'</strong>');
				$('.editbox #orimg').attr('src','../database/img/'+$('input[name="basiccompany"]').val()+'/'+value[0]['image']);
			}
		});
		$.ajax({
			url:'./tool/order.ajax.php',
			method:'post',
			title:'修改明細',
			data:{company:$('input[name="basiccompany"]').val(),dep:$('input[name="basicdep"]').val(),inumber:$('.box td:eq('+findex+') input[name="no[]"]').val()},
			dataType:'json',
			success:function(value){
				//初始設定
				$('.editbox #orimg').attr('src','');
				$('.editbox #orname').html('');
				$('.editbox #ortaste').html('');
				//
				$('.editbox input[name="index"]').val(findex);
				$('.editbox #orimg').attr('src','../database/img/'+$('input[name="basiccompany"]').val()+'/'+value[0]['img']);
				var strhtml=value[0]['itemname'];
				if($('.box td:eq('+findex+') input[name="mcounter"]').val()==1){
					strhtml=strhtml+'&nbsp;'+$('#frontunit').val()+$('.box td:eq('+findex+') input[name="unitprice[]"]').val()+$('#unit').val();
				}
				else{
					strhtml=strhtml+'&nbsp;'+$('#frontunit').val()+$('.box td:eq('+findex+') input[name="mname[]"]').val()+$('.box td:eq('+findex+') input[name="unitprice[]"]').val()+$('#unit').val();
				}
				strhtml=strhtml+'<input type="hidden" id="orvaname" value="'+$('.box td:eq('+findex+') input[name="mname[]"]').val()+'"><input type="hidden" id="orvalue" value="'+$('.box td:eq('+findex+') input[name="unitprice[]"]').val()+'">';
				$('.editbox #orname').html(strhtml);
				$('.editbox .orviewnum').html($('.box td:eq('+findex+') input[name="number[]"]').val());
				if(typeof value[0]['taste']=='undefined'){
				}
				else{
					var splitchosetaste=$('.box td:eq('+findex+') input[name="taste[]"]').val().split(',');
					$('.order #ortaste').html('');
					$('.order #ortaste').css({'background-color':'#ffffff'});
					var temp=0;
					var temptaste='<div style="width:100%;height:calc(100% - 25px);overflow-y:auto;">';
					for(var i=0;i<value.length;i++){
						if(value[i]['money']>0){
							if($.inArray(value[i]['taste'],splitchosetaste)>=0){
								temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:111px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:111px;float:left;word-break:break-all;"><label style="margin:auto 0;font-size:50px;color:#3e3a39;"><span id="taste'+i+'name"><strong>'+value[i]['name']+'('+$('#frontunit').val()+value[i]['money']+$('#unit').val()+')</strong></span>'+'</label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" style="display:none;" id="taste'+i+'" class="tastecheck" value="'+value[i]['taste']+'" checked=true><img id="chimg'+i+'" class="chimg"  src="../database/img/onch.png" style="float:left;position:absolute;top:0;right:0;"></div>';
							}
							else{
								temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:111px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:111px;float:left;word-break:break-all;"><label style="margin:auto 0;font-size:50px;color:#3e3a39;"><span id="taste'+i+'name"><strong>'+value[i]['name']+'('+$('#frontunit').val()+value[i]['money']+$('#unit').val()+')</strong></span>'+'</label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" style="display:none;" id="taste'+i+'" class="tastecheck" value="'+value[i]['taste']+'"><img id="chimg'+i+'" class="chimg"  src="../database/img/noch.png" style="float:left;position:absolute;top:34px;right:10px;"></div>';
							}
						}
						else{
							if($.inArray(value[i]['taste'],splitchosetaste)>=0){
								temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:111px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:111px;float:left;word-break: break-all;"><label style="font-size:50px;color:#3e3a39;"><span id="taste'+i+'name"><strong>'+value[i]['name']+'</strong></span></label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" id="taste'+i+'" class="tastecheck" style="display:none;" value="'+value[i]['taste']+'" checked=true><img id="chimg'+i+'" class="chimg" src="../database/img/onch.png" style="float:left;position:absolute;top:0;right:0;"></div>';
							}
							else{
								temptaste=temptaste+'<div id="chlabel" style="width:calc(100% - 70px);min-height:111px;margin:0 0 0 35px;float:left;position:relative;"><div style="width:calc(100% - 62px);min-height:111px;float:left;word-break: break-all;"><label style="font-size:50px;color:#3e3a39;"><span id="taste'+i+'name"><strong>'+value[i]['name']+'</strong></span></label><input type="hidden" id="tmoney'+i+'" value="'+value[i]['money']+'"><input type="hidden" id="tname'+i+'" value="'+value[i]['name']+'"></div><input type="checkbox" id="taste'+i+'" class="tastecheck" style="display:none;" value="'+value[i]['taste']+'"><img id="chimg'+i+'" class="chimg" src="../database/img/noch.png" style="float:left;position:absolute;top:34px;right:10px;"></div>';
							}
						}
					}
					$('.editbox #ortaste').append(temptaste+'</div>');
				}
			},
			error:function(d){
				console.log(d);
			}
		});
		editbox.dialog('open');
	});
	$(document).on('click','.finalfun #home',function(){//首頁按鈕
		sysmeg.dialog('open');
	});
	$(document).on('click','.finalfun #submit',function(){//送出按鈕－進入明細總覽
		result.dialog('open');
	});
});