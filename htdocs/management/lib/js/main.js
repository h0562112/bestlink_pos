function openCity(cityClass,cityName) {
	//console.log('1');
	$('.w3-bar-item.w3-button').removeClass('focus');
	$('#'+cityName+'button').addClass('focus');
	$('.'+cityClass+'Item').css({'display':'none'});
	$('#'+cityName).css({'display':'block'});
	//console.log('2');
}
$(window).resize(function() {
	if ($('#menu_div').width() == 130 && $('#nav').is(':hidden')) {
		$('#content').css('width','100vw');
		$('#nav').removeAttr('style');
	}
	else{
		$('#content').css('height','calc(100% - '+($('#logo').height()+80)+'px)');
	}
});
$(document).ready(function(){
	var myint;//即時桌控更新使用，方便關閉更新
	$('#sidebar.accordion').accordion({
		collapsible: true
	});
	var mystitle='';
	billboard=$('.billboard').dialog({
		autoOpen:false,
		width:$(window).width() - 20,
		height:$(window).height() - 20,
		//title:mystitle,
		position:{my:'center center',at:'center center',of:window},
		resizable:false,
		modal:true,
		draggable:false
	});
	$.ajax({
		url:'./lib/js/billboard.check.php',
		method:'post',
		async:false,
		dataType:'html',
		success:function(d){
			if(d=='empty'){
			}
			else{
				$('.billboard #content').html(d);
				billboard.dialog('open');
			}
			//console.log(d);
		},
		error:function(e){
			console.log(e);
		}
	});
	$(document).on('click','.billboard .checkbillboard',function(){
		billboard.dialog('close');
	});
	$.ajax({
		url:'./lib/js/getininame.ajax.php',
		method:'post',
		async:false,
		data:{'file':'interface','lan':$('.lan').val(),'name':'msytitle'},
		dataType:'html',
		success:function(d){
			mystitle=mystitle+d;
		},
		error:function(e){
			console.log(e);
		}
	});
	mys=$('.mys').dialog({
		autoOpen:false,
		width:450,
		height:300,
		title:mystitle,
		position:{my:'top',at:'top',of:'body'},
		resizable:false,
		modal:true,
		draggable:false,
		close:function(){
			$('.mys').html('');
		}
	});
	mystitle='';
	$.ajax({
		url:'./lib/js/getininame.ajax.php',
		method:'post',
		async:false,
		data:{'file':'interface','lan':$('.lan').val(),'name':'punchtitle'},
		dataType:'html',
		success:function(d){
			mystitle=mystitle+d;
		},
		error:function(e){
			console.log(e);
		}
	});
	expmsg=$('.expmsg').dialog({
		autoOpen:false,
		width:350,
		height:250,
		title:mystitle,
		position:{my:'center',at:'center',of:'body'},
		resizable:false,
		modal:true,
		draggable:false
	});
	var nav = $('#nav');
	var win = $(document);
	if ($('#menu_div').width() == 130 && $('#nav').is(':hidden')) {
		$('#nav').removeAttr('style');
	}
	else{
	}
	$(document).on('touchstart','#menu_div',function(){
	});
	$(document).on('click','#menu_div', function(e) {
		$('#menu_div').toggleClass('open');
		e.preventDefault();
		nav.slideToggle();
	});
	$(document).on('touchend','#menu_div',function(e) {
		$('#menu_div').toggleClass('open');
		e.preventDefault();
		nav.slideToggle();
	});
	$('#hyper').click(function(e){
		$.ajax({
			url:'./lib/js/hyper.php',
			dataType:'html',
			success:function(d){
				$('#content').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$('#allunit').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/getallunit.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#setkds').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/kdssetting.ajax.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#allitems').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/getalldata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#alltaste').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/getalltaste.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#alltype').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/getalltype.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#allsectype').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/getallsectype.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#printlisttag').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/printlisttag.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#secview').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/secview.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#table').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/table.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		myint=setInterval(function(){
			//console.log('1');
			$('#sidebar #table').trigger('click');
		},30000);
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#paper1').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/paper1.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'admin':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#memsalelist').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/memsalelist.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#editpw').click(function(){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/setpw.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('#content').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#allmanufact').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/setmanufact.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			if($('#menu_div').prop('class')=='open'){
				$('#menu_div').toggleClass('open');
			}
			else{
			}
			e.preventDefault();
			if($('#menu_div').prop('class')=='open'){
				nav.slideToggle();
			}
			else{
			}
		}
	});
	$('#allpersons').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/setperson.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#allpersonnels').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/setpersonnel.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#allmembers').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/setmember.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#otherpay').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/setotherpay.php',
			method:'post',
			async:false,
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#content').html('');
				$('#content').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#autodis').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/setautodis.php',
			method:'post',
			async:false,
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#content').html('');
				$('#content').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#inifile').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/inifile.php',
			method:'post',
			async:false,
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#content').html('');
				$('#content').html(d);
				$('.inifile #initsetting').trigger('click');
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#inoutmoney').click(function(e){
		clearInterval(myint);
		$.ajax({
			url:'./lib/js/inoutmoney.php',
			method:'post',
			async:false,
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#content').html('');
				$('#content').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
		if ($('#menu_div').width() == 130){
		}
		else{
			$('#menu_div').toggleClass('open');
			e.preventDefault();
			nav.slideToggle();
		}
	});
	$('#logout').click(function(){
		//console.log(location.href.substr(-8)=="?company");
		if(location.href.substr(-8)=="?company"){
			location.href='./logoutmethod.php'+location.href.substr(-8)+'='+$('input[name="company"]').val();
		}
		else{
			location.href='./logoutmethod.php';
		}
	});
	$(document).on('click','#item #allitems .itemrow',function(){
		var index=$('#item #allitems .itemrow').index(this);
		$('#item #allitems .itemrow').css({'background-color':'#ffffff'});
		$('#item #allitems .itemrow').prop('id',index);
		$('#item #allitems .itemrow:eq('+index+')').prop('id','focus');
		$('#item #allitems .itemrow:eq('+index+')').css({'background-color':'#E9E9E9'});
		items.tabs('option','disabled',[]);
		if($('#item #allitems .itemrow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#item #allitems .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#item #allitems .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#item #allitems .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#item #allitems .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		$.ajax({
			url:'./lib/js/getitemdata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'itemdep':$('#item #allitems .itemrow#focus input[name=\"itemdep\"]').val(),'number':$('#item #allitems .itemrow#focus input[name=\"number\"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#item #edititem').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#item #allitems #create',function(){
		$('#item #allitems .itemrow').css({'background-color':'#ffffff'});
		$('#item #allitems .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#item #allitems .itemrow #chimg').attr('src','./img/noch.png');
		$.ajax({
			url:'./lib/js/getitemdata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#item #edititem').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		items.tabs('option','disabled',[]);
		items.tabs('option','active',[1]);
	});
	$(document).on('click','#item #allitems #edit',function(){
		if(items.tabs('option','disabled')[0]==1){
		}
		else{
			items.tabs('option','disabled',[]);
			items.tabs('option','active',[1]);
		}
	});
	$(document).on('click','#item #allitems #delete',function(){
		if($('#item #allitems .itemrow input[type=\"checkbox\"]:checked').length>0){
			var contenttext='';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'checkitem'},
				dataType:'html',
				success:function(d){
					contenttext=contenttext+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			t=confirm(contenttext);
			if(t==true){
				var numberarray=$('input[name="company"]').val()+','+$('input[name="db"]').val();
				for(var i=0;i<$('#item #allitems .itemrow input[type=\"checkbox\"]').length;i++){
					if($('#item #allitems .itemrow:eq('+i+') input[type=\"checkbox\"]:checked').length>0){
						numberarray=numberarray+','+$('#item #allitems .itemrow:eq('+i+') input[name=\"number\"]').val();
					}
					else{
					}
				}
				$.ajax({
					url:'./lib/js/deleteitem.ajax.php',
					method:'post',
					data:{'numbergroup':numberarray},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('#sidebar #allitems').trigger('click');
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
		}
	});
	$(document).on('click','#item #edititem #save',function(){
		if($('#item #edititem #itemform input[name="front"]').val()==''||$('#item #edititem #itemform .mainname').val()==''){
			var contenttext='';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'itemhint'},
				dataType:'html',
				success:function(d){
					contenttext=contenttext+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			alert(contenttext);
		}
		else{
			$('#item #edititem #save').prop('disabled',true);
			$('#item #edititem #save').css({'opacity':'0.5','cursor':'inherit'});
			if($('#item #edititem .quickorder').val()!=''){
				if($('#item #edititem input[name="number"]').val()==''){
					var inumber='null';
				}
				else{
					var inumber=$('#item #edititem input[name="number"]').val();
				}
				$.ajax({
					url:'./lib/js/hasquickorder.ajax.php',
					method:'post',
					data:{'company':$('#item #edititem input[name="company"]').val(),'dep':$('#item #edititem input[name="dep"]').val(),'inumber':inumber,'quickorder':$('#item #edititem .quickorder').val()},
					dataType:'html',
					success:function(d){
						console.log(d);
						$('#item #edititem #save').prop('disabled',false);
						if(d=='notexists'){
							$.ajax({
								url:'./lib/js/saveitem.ajax.php',
								method:'post',
								cache: false,
								data:new FormData($('#item #edititem #itemform')[0]),
								contentType:false,
								processData: false,
								dataType:'html',
								success:function(d){
									//console.log(d);
									//$('#sidebar #allitems').trigger('click');
									$('.mys').html('<div style="width:90%;font-size:3vw;text-align:center;margin:0 auto;">儲存成功。</div>');
									mys.dialog('open');
									setTimeout("mys.dialog('close')",3000);
									$('#item #edititem #save').prop('disabled',false);
									$('#item #edititem #save').css({'opacity':'1','cursor':'pointer'});
								},
								error:function(e){
									console.log(e);
									$('#item #edititem #save').prop('disabled',false);
									$('#item #edititem #save').css({'opacity':'1','cursor':'pointer'});
								}
							});
						}
						else{
							alert('快點代碼重複，請確認輸入無誤。');
							$('#item #edititem #save').prop('disabled',false);
							$('#item #edititem #save').css({'opacity':'1','cursor':'pointer'});
						}
					},
					error:function(e){
						console.log(e);
						$('#item #edititem #save').prop('disabled',false);
						$('#item #edititem #save').css({'opacity':'1','cursor':'pointer'});
					}
				});
			}
			else{
				$.ajax({
					url:'./lib/js/saveitem.ajax.php',
					method:'post',
					cache: false,
					data:new FormData($('#item #edititem #itemform')[0]),
					contentType:false,
					processData: false,
					dataType:'html',
					success:function(d){
						//console.log(d);
						//$('#sidebar #allitems').trigger('click');
						$('.mys').html('<div style="width:90%;font-size:3vw;text-align:center;margin:0 auto;">儲存成功。</div>');
						mys.dialog('open');
						setTimeout("mys.dialog('close')",3000);
						$('#item #edititem #save').prop('disabled',false);
						$('#item #edititem #save').css({'opacity':'1','cursor':'pointer'});
					},
					error:function(e){
						console.log(e);
						$('#item #edititem #save').prop('disabled',false);
						$('#item #edititem #save').css({'opacity':'1','cursor':'pointer'});
					}
				});
			}
		}
	});
	$(document).on('click','#item #edititem #fun #pre',function(){
		if($('#item #allitems .itemrow:eq(0)').prop('id')=='focus'){
			$('#item #allitems .itemrow').prop('id',parseInt($('#item #allitems .itemrow').length)-1);
			$('#item #allitems .itemrow:eq('+(parseInt($('#item #allitems .itemrow').length)-1)+')').prop('id','focus');
		}
		else{
			$('#item #allitems .itemrow').prop('id',(parseInt($('#item #allitems .itemrow:eq(0)').prop('id'))-1));
			$('#item #allitems .itemrow:eq('+(parseInt($('#item #allitems .itemrow:eq(0)').prop('id')))+')').prop('id','focus');
		}
		
		$.ajax({
			url:'./lib/js/getitemdata.php',
			method:'post',
			data:{'itemdep':$('#item #allitems .itemrow#focus input[name=\"itemdep\"]').val(),'number':$('#item #allitems .itemrow#focus input[name=\"number\"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#item #edititem').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#item #edititem #fun #next',function(){
		if($('#item #allitems .itemrow:eq('+(parseInt($('#item #allitems .itemrow').length)-1)+')').prop('id')=='focus'){
			$('#item #allitems .itemrow').prop('id','0');
			$('#item #allitems .itemrow:eq(0)').prop('id','focus');
		}
		else{
			$('#item #allitems .itemrow').prop('id',(parseInt($('#item #allitems .itemrow:eq('+(parseInt($('#item #allitems .itemrow').length)-1)+')').prop('id'))+1));
			$('#item #allitems .itemrow:eq('+(parseInt($('#item #allitems .itemrow:eq('+(parseInt($('#item #allitems .itemrow').length)-1)+')').prop('id')))+')').prop('id','focus');
		}
		
		$.ajax({
			url:'./lib/js/getitemdata.php',
			method:'post',
			data:{'itemdep':$('#item #allitems .itemrow#focus input[name=\"itemdep\"]').val(),'number':$('#item #allitems .itemrow#focus input[name=\"number\"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#item #edititem').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#item #edititem #cancel',function(){
		items.tabs('option','disabled',[1]);
		items.tabs('option','active',[0]);
		$('#item #edititem').html('');
		$('#item #allitems .itemrow').css({'background-color':'#ffffff'});
		$('#item #allitems .itemrow').prop('id','');
		$('#item #allitems .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#item #allitems .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('change','#item #edititem input',function(){
		$('#item #edititem button').css({'background-color':$('#item #edititem input[name="bgcolor"]').val()});

		$('#item #edititem button #name1').css({'font-size':$('#item #edititem input[name="size1"]').val()});
		$('#item #edititem button #name1').css({'color':$('#item #edititem input[name="color1"]').val()});
		if($('#item #edititem input[name="bold1"]:checked').length>0){
			$('#item #edititem button #name1').css({'font-size':'bold'});
		}
		else{
			$('#item #edititem button #name1').css({'font-size':'normal'});
		}
		$('#item #edititem button #name1').html($('#item #edititem input[name="name1"]').val());

		$('#item #edititem button #name2').css({'font-size':$('#item #edititem input[name="size2"]').val()});
		$('#item #edititem button #name2').css({'color':$('#item #edititem input[name="color2"]').val()});
		if($('#item #edititem input[name="bold2"]:checked').length>0){
			$('#item #edititem button #name2').css({'font-size':'bold'});
		}
		else{
			$('#item #edititem button #name2').css({'font-size':'normal'});
		}
		$('#item #edititem button #name2').html($('#item #edititem input[name="name2"]').val());
	});
	$(document).on('change','#item #edititem input[name=\"isgroup\"]',function(){
		var index=$('#item #edititem input[name=\"isgroup\"]').index(this);
		if($('#item #edititem input[name=\"isgroup\"]:eq('+index+')').prop('checked')){
			$('#item #edititem #subtype:eq('+index+')').css({'display':''});
		}
		else{
			$('#item #edititem #subtype:eq('+index+')').css({'display':'none'});
		}
	});
	$(document).on('click','#item ul li:eq(0)',function(){
		$.ajax({
			url:'./lib/js/getalldata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#item ul li:eq(2)',function(){
		$('#item #allitems .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#item #allitems .itemrow #chimg').attr('src','./img/noch.png');
		items.tabs('option','disabled',[1]);
		$.ajax({
			url:'./lib/js/getvoiditem.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('#item #voiditem').html(d);
			},
			error:function(e){
				console.log(e)
			}
		});
	});
	$(document).on('click','#item #voiditem .itemrow',function(){
		var index=$('#item #voiditem .itemrow').index(this);
		$('#item #voiditem .itemrow').css({'background-color':'#ffffff'});
		$('#item #voiditem .itemrow').prop('id',index);
		$('#item #voiditem .itemrow:eq('+index+')').prop('id','focus');
		$('#item #voiditem .itemrow:eq('+index+')').css({'background-color':'#E9E9E9'});
		items.tabs('option','disabled',[1]);
		if($('#item #voiditem .itemrow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#item #voiditem .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#item #voiditem .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#item #voiditem .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#item #voiditem .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#item #voiditem #return',function(){
		if($('#item #voiditem .itemrow input[type="checkbox"]:checked').length>0){//有勾選品項
			var num=[];
			var front=[];
			for(var i=0;i<$('#item #voiditem .itemrow input[type="checkbox"]').length;i++){
				if($('#item #voiditem .itemrow:eq('+i+') input[type="checkbox"]:checked').length>0){
					num.push($('#item #voiditem .itemrow:eq('+i+') input[name="number"]').val());
					front.push($('#item #voiditem .itemrow:eq('+i+') input[name="itemdep"]').val());
				}
				else{
				}
			}
			if(num.length>0){
				$.ajax({
					url:'./lib/js/return.item.ajax.php',
					method:'post',
					async:false,
					data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'num':num,'front':front},
					dataType:'json',
					success:function(d){
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
				$('#item ul li:eq(2)').trigger('click');
			}
			else{
			}
		}
		else{
		}
	});
	$(document).on('click','#taste #alltastes .tasterow',function(){
		var index=$('#taste #alltastes .tasterow').index(this);
		$('#taste #alltastes .tasterow').css({'background-color':'#ffffff'});
		$('#taste #alltastes .tasterow').prop('id',index);
		$('#taste #alltastes .tasterow:eq('+index+')').prop('id','focus');
		$('#taste #alltastes .tasterow:eq('+index+')').css({'background-color':'#E9E9E9'});
		tastes.tabs('option','disabled',[4]);
		if($('#taste #alltastes .tasterow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#taste #alltastes .tasterow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#taste #alltastes .tasterow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#taste #alltastes .tasterow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#taste #alltastes .tasterow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		$.ajax({
			url:'./lib/js/gettastedata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':$('#taste #alltastes .tasterow#focus input[name=\"number\"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#taste #edittaste').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#taste #alltastes #create',function(){
		$('#taste #alltastes .tasterow').css({'background-color':'#ffffff'});
		$('#taste #alltastes .tasterow input[type=\"checkbox\"]').prop('checked',false);
		$('#taste #alltastes .tasterow #chimg').attr('src','./img/noch.png');
		$.ajax({
			url:'./lib/js/gettastedata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#taste #edittaste').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		tastes.tabs('option','disabled',[4]);
		tastes.tabs('option','active',[1]);
	});
	$(document).on('click','#taste #alltastes #edit',function(){
		if(tastes.tabs('option','disabled')[0]==1){
		}
		else{
			tastes.tabs('option','disabled',[4]);
			tastes.tabs('option','active',[1]);
		}
	});
	$(document).on('click','#taste #alltastes #delete',function(){
		if($('#taste #alltastes .tasterow input[type=\"checkbox\"]:checked').length>0){
			t=confirm('是否確認刪除產品？');
			if(t==true){
				var numberarray=$('input[name="company"]').val()+','+$('input[name="db"]').val();
				for(var i=0;i<$('#taste #alltastes .tasterow input[type=\"checkbox\"]').length;i++){
					if($('#taste #alltastes .tasterow:eq('+i+') input[type=\"checkbox\"]:checked').length>0){
						numberarray=numberarray+','+$('#taste #alltastes .tasterow:eq('+i+') input[name=\"number\"]').val();
					}
					else{
					}
				}
				$.ajax({
					url:'./lib/js/deletetaste.ajax.php',
					method:'post',
					data:{'numbergroup':numberarray},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('#sidebar #alltaste').trigger('click');
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
		}
	});
	$(document).on('click','#taste #edittaste #save',function(){
		if($('#taste #edittaste input[name="name1"]').val()==''&&$('#taste #edittaste input[name="name2"]').val()==''){
			alert('請至少輸入一個名稱。');
		}
		else{
			$('#taste #edittaste #save').prop('disabled',true);
			$('#taste #edittaste #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/savetaste.ajax.php',
				method:'post',
				data:$('#taste #edittaste #tasteform').serialize(),
				dataType:'html',
				success:function(d){
					//console.log(d);
					//$('#sidebar #alltaste').trigger('click');
					$('.mys').html('<div style="width:90%;font-size:3vw;text-align:center;margin:0 auto;">儲存成功。</div>');
					mys.dialog('open');
					setTimeout("mys.dialog('close')",3000);
					$('#taste #edittaste #save').prop('disabled',false);
					$('#taste #edittaste #save').css({'opacity':'1','cursor':'pointer'});
				},
				error:function(e){
					console.log(e);
					$('#taste #edittaste #save').prop('disabled',false);
					$('#taste #edittaste #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','#taste #edittaste #fun #pre',function(){
		if($('#taste #alltastes .tasterow:eq(0)').prop('id')=='focus'){
			$('#taste #alltastes .tasterow').prop('id',parseInt($('#taste #alltastes .tasterow').length)-1);
			$('#taste #alltastes .tasterow:eq('+(parseInt($('#taste #alltastes .tasterow').length)-1)+')').prop('id','focus');
		}
		else{
			$('#taste #alltastes .tasterow').prop('id',(parseInt($('#taste #alltastes .tasterow:eq(0)').prop('id'))-1));
			$('#taste #alltastes .tasterow:eq('+(parseInt($('#taste #alltastes .tasterow:eq(0)').prop('id')))+')').prop('id','focus');
		}
		
		$.ajax({
			url:'./lib/js/gettastedata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':$('#taste #alltastes .tasterow#focus input[name=\"number\"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#taste #edittaste').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#taste #edittaste #fun #next',function(){
		if($('#taste #alltastes .tasterow:eq('+(parseInt($('#taste #alltastes .tasterow').length)-1)+')').prop('id')=='focus'){
			$('#taste #alltastes .tasterow').prop('id','0');
			$('#taste #alltastes .tasterow:eq(0)').prop('id','focus');
		}
		else{
			$('#taste #alltastes .tasterow').prop('id',(parseInt($('#taste #alltastes .tasterow:eq('+(parseInt($('#taste #alltastes .tasterow').length)-1)+')').prop('id'))+1));
			$('#taste #alltastes .tasterow:eq('+(parseInt($('#taste #alltastes .tasterow:eq('+(parseInt($('#taste #alltastes .tasterow').length)-1)+')').prop('id')))+')').prop('id','focus');
		}
		
		$.ajax({
			url:'./lib/js/gettastedata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':$('#taste #alltastes .tasterow#focus input[name=\"number\"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#taste #edittaste').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#taste #edittaste #cancel',function(){
		tastes.tabs('option','disabled',[1,4]);
		tastes.tabs('option','active',[0]);
		$('#taste #edittaste').html('');
		$('#taste ul li:eq(0)').trigger('click');
	});
	$(document).on('click',"#taste #edittaste #strawbox.select_box",function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
	/*赋值给文本框*/
    $(document).on('click',"#taste #edittaste #strawbox .option a",function(){
		var index=$('#taste #strawbox .option a').index(this);
		var value=$("#taste #strawbox .option a:eq("+index+")").text();
        $("#taste #strawbox .option a").parent().siblings(".select_txt").text(value);
        $("#taste input[name='straw']#select_value").val($('#taste #strawbox .option a:eq('+index+')').attr('id'));
    });
	$(document).on('click',document,function(event){
        var eo=$(event.target);
        if($("#taste .select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('#taste .option').hide();
    });
	/*$(document).on('change','#taste #edittaste input',function(){
		$('#taste #edittaste #save').prop('disabled',false);
	});*/
	$(document).on('change','#taste #edittaste input[name=\"isgroup\"]',function(){
		var index=$('#taste #edittaste input[name=\"isgroup\"]').index(this);
		if($('#taste #edittaste input[name=\"isgroup\"]:eq('+index+')').prop('checked')){
			$('#taste #edittaste #subtype:eq('+index+')').css({'display':''});
		}
		else{
			$('#taste #edittaste #subtype:eq('+index+')').css({'display':'none'});
		}
	});
	$(document).on('click','#taste ul li:eq(0)',function(){
		$.ajax({
			url:'./lib/js/getalltaste.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#taste ul li:eq(2)',function(){
		$('#taste #alltastes .tasterow input[type=\"checkbox\"]').prop('checked',false);
		$('#taste #alltastes .tasterow #chimg').attr('src','./img/noch.png');
		tastes.tabs('option','disabled',[1,4]);
		$.ajax({
			url:'./lib/js/getvoidtaste.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('#taste #voidtaste').html(d);
			},
			error:function(e){
				console.log(e)
			}
		});
	});
	$(document).on('click','#taste #voidtaste .tasterow',function(){
		var index=$('#taste #voidtaste .tasterow').index(this);
		$('#taste #voidtaste .tasterow').css({'background-color':'#ffffff'});
		$('#taste #voidtaste .tasterow').prop('id',index);
		$('#taste #voidtaste .tasterow:eq('+index+')').prop('id','focus');
		$('#taste #voidtaste .tasterow:eq('+index+')').css({'background-color':'#E9E9E9'});
		tastes.tabs('option','disabled',[1,4]);
		if($('#taste #voidtaste .tasterow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#taste #voidtaste .tasterow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#taste #voidtaste .tasterow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#taste #voidtaste .tasterow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#taste #voidtaste .tasterow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#taste #voidtaste #return',function(){
		if($('#taste #voidtaste .tasterow input[type="checkbox"]:checked').length>0){//有勾選品項
			var num=[];
			for(var i=0;i<$('#taste #voidtaste .tasterow input[type="checkbox"]').length;i++){
				if($('#taste #voidtaste .tasterow:eq('+i+') input[type="checkbox"]:checked').length>0){
					num.push($('#taste #voidtaste .tasterow:eq('+i+') input[name="number"]').val());
				}
				else{
				}
			}
			if(num.length>0){
				$.ajax({
					url:'./lib/js/return.taste.ajax.php',
					method:'post',
					async:false,
					data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'num':num},
					dataType:'json',
					success:function(d){
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
				$('#taste ul li:eq(2)').trigger('click');
			}
			else{
			}
		}
		else{
		}
	});
	$(document).on('click','#taste .alltastegroup',function(){
		$.ajax({
			url:'./lib/js/getalltastegroup.php',
			method:'post',
			async:false,
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(d){
				$('#taste #alltastegroup').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
		tastes.tabs('option','disabled',[1,4]);
	});
	$(document).on('click','#taste #alltastegroup #create',function(){
		$.ajax({
			url:'./lib/js/gettastegroupdata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#taste #edittastegroup').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		tastes.tabs('option','disabled',[1]);
		tastes.tabs('option','active',[4]);
	});
	$(document).on('click','#taste #alltastegroup .tasterow',function(){
		var index=$('#taste #alltastegroup .tasterow').index(this);
		$('#taste #alltastegroup .tasterow').css({'background-color':'#ffffff'});
		$('#taste #alltastegroup .tasterow').prop('id',index);
		$('#taste #alltastegroup .tasterow:eq('+index+')').prop('id','focus');
		$('#taste #alltastegroup .tasterow:eq('+index+')').css({'background-color':'#E9E9E9'});
		tastes.tabs('option','disabled',[1,4]);
		if($('#taste #alltastegroup .tasterow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#taste #alltastegroup .tasterow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#taste #alltastegroup .tasterow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#taste #alltastegroup .tasterow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#taste #alltastegroup .tasterow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#taste #alltastegroup #edit',function(){
		if($('#taste #alltastegroup .tasterow#focus').length>0){
			$.ajax({
				url:'./lib/js/gettastegroupdata.php',
				method:'post',
				data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'number':$('#taste #alltastegroup .tasterow#focus input[name="number"]').val()},
				dataType:'html',
				success:function(string){
					$('#taste #edittastegroup').html(string);
				},
				error:function(e){
					console.log(e);
				}
			});
			tastes.tabs('option','disabled',[1]);
			tastes.tabs('option','active',[4]);
		}
		else{
			console.log($('#taste #alltastegroup .tasterow#focus').length);
		}
	});
	$(document).on('click','#taste #edittastegroup #save',function(){
		if($('#taste #edittastegroup input[name="name"]').val().trim!=''&&$('#taste #edittastegroup input[name="pos"]').val().trim!=''){
			$.ajax({
				url:'./lib/js/savetastegtoup.ajax.php',
				method:'post',
				async:false,
				data:$('#taste #edittastegroup #tasteform').serializeArray(),
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('#taste .alltastegroup').trigger('click');
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
		}
	});
	$(document).on('click','#taste #edittastegroup #cancel',function(){
		$('#taste .alltastegroup').trigger('click');
	});
	$(document).on('click','#type #alltypes .typerow',function(){
		var index=$('#type #alltypes .typerow').index(this);
		$('#type #alltypes .typerow').css({'background-color':'#ffffff'});
		$('#type #alltypes .typerow:eq('+index+')').css({'background-color':'#E9E9E9'});
		types.tabs('option','disabled',[]);
		var g='';
		var f2='';
		for(var i=0;i<$('#type #alltypes .typerow').length;i++){
			if(index==i){
				if(g.length==0){
					g=$('#type #alltypes .typerow:eq('+index+') input[name=\"typedep\"]').val()+'-'+$('#type #alltypes .typerow:eq('+index+') input[name=\"number\"]').val();
				}
				else{
					g=g+','+$('#type #alltypes .typerow:eq('+index+') input[name=\"typedep\"]').val()+'-'+$('#type #alltypes .typerow:eq('+index+') input[name=\"number\"]').val();
				}
				if(f2.length==0){
					f2=$('#type #alltypes .typerow:eq('+index+') input[name=\"number\"]').val();
				}
				else{
				}
			}
			else{
				if($('#type #alltypes .typerow:eq('+i+') input[type=\"checkbox\"]').prop('checked')){
					if(g.length==0){
						g=$('#type #alltypes .typerow:eq('+i+') input[name=\"typedep\"]').val()+'-'+$('#type #alltypes .typerow:eq('+i+') input[name=\"number\"]').val();
					}
					else{
						g=g+','+$('#type #alltypes .typerow:eq('+i+') input[name=\"typedep\"]').val()+'-'+$('#type #alltypes .typerow:eq('+i+') input[name=\"number\"]').val();
					}
					if(f2.length==0){
						f2=$('#type #alltypes .typerow:eq('+i+') input[name=\"number\"]').val();
					}
					else{
					}
				}
				else{
				}
			}
		}
		if($('#type #alltypes .typerow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#type #alltypes .typerow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#type #alltypes .typerow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#type #alltypes .typerow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#type #alltypes .typerow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		$('#type input[name=\"typegroup\"]').val(g);
		$.ajax({
			url:'./lib/js/gettypedata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':f2,'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#type #edittype').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#type #alltypes #create',function(){
		$('#type #alltypes .typerow').css({'background-color':'#ffffff'});
		$('#type #alltypes .typerow input[type=\"checkbox\"]').prop('checked',false);
		$('#type #alltypes .typerow #chimg').attr('src','./img/noch.png');
		$.ajax({
			url:'./lib/js/gettypedata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#type #edittype').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		types.tabs('option','disabled',[]);
		types.tabs('option','active',[1]);
	});
	$(document).on('click','#type #alltypes #edit',function(){
		if(types.tabs('option','disabled')[0]==1){
		}
		else{
			types.tabs('option','disabled',[]);
			types.tabs('option','active',[1]);
		}
	});
	$(document).on('click','#type #alltypes #delete',function(){
		if($('#type #alltypes .typerow input[type=\"checkbox\"]:checked').length>0){
			t=confirm('是否確認刪除類別？');
			if(t==true){
				var numberarray=$('input[name="company"]').val()+','+$('input[name="db"]').val();
				for(var i=0;i<$('#type #alltypes .typerow input[type=\"checkbox\"]').length;i++){
					if($('#type #alltypes .typerow:eq('+i+') input[type=\"checkbox\"]:checked').length>0){
						numberarray=numberarray+','+$('#type #alltypes .typerow:eq('+i+') input[name=\"number\"]').val();
					}
					else{
					}
				}
				$.ajax({
					url:'./lib/js/deletetype.ajax.php',
					method:'post',
					data:{'numbergroup':numberarray},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('#sidebar #alltype').trigger('click');
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
		}
	});
	$(document).on('click','#type #edittype #save',function(){
		if($('#type #edittype input[name="name1"]').val()==''&&$('#type #edittype input[name="name2"]').val()==''){
			alert('請至少輸入一個名稱。');
		}
		else{
			$('#type #edittype #save').prop('disabled',true);
			$('#type #edittype #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/savetype.ajax.php',
				method:'post',
				data:$('#type #edittype #typeform').serialize(),
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('#sidebar #alltype').trigger('click');
					$('#type #edittype #save').prop('disabled',false);
					$('#type #edittype #save').css({'opacity':'1','cursor':'pointer'});
				},
				error:function(e){
					console.log(e);
					$('#type #edittype #save').prop('disabled',false);
					$('#type #edittype #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','#type #edittype #cancel',function(){
		types.tabs('option','disabled',[1]);
		types.tabs('option','active',[0]);
		$('#type #edittype').html('');
		$('#type #alltypes .typerow').css({'background-color':'#ffffff'});
	});
	$(document).on('change','#type #edittype input',function(){
		$('#type #edittype button').css({'background-color':$('#type #edittype input[name="bgcolor"]').val()});

		$('#type #edittype button #name1').css({'font-size':$('#type #edittype input[name="size1"]').val()});
		$('#type #edittype button #name1').css({'color':$('#type #edittype input[name="color1"]').val()});
		if($('#type #edittype input[name="bold1"]').prop('checked')){
			$('#type #edittype button #name1').css({'font-weight':'bold'});
		}
		else{
			$('#type #edittype button #name1').css({'font-weight':'normal'});
		}
		$('#type #edittype button #name1').html($('#type #edittype input[name="name1"]').val());

		$('#type #edittype button #name2').css({'font-size':$('#type #edittype input[name="size2"]').val()});
		$('#type #edittype button #name2').css({'color':$('#type #edittype input[name="color2"]').val()});
		if($('#type #edittype input[name="bold2"]').prop('checked')){
			$('#type #edittype button #name2').css({'font-weight':'bold'});
		}
		else{
			$('#type #edittype button #name2').css({'font-weight':'normal'});
		}
		$('#type #edittype button #name2').html($('#type #edittype input[name="name2"]').val());
	});
	$(document).on('click','#type ul li:eq(0)',function(){
		$.ajax({
			url:'./lib/js/getalltype.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#type ul li:eq(2)',function(){
		$('#type #alltypes .typerow input[type=\"checkbox\"]').prop('checked',false);
		$('#type #alltypes .typerow #chimg').attr('src','./img/noch.png');
		types.tabs('option','disabled',[1]);
		$.ajax({
			url:'./lib/js/getvoidtype.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('#type #voidtype').html(d);
			},
			error:function(e){
				console.log(e)
			}
		});
	});
	$(document).on('click','#type #voidtype .typerow',function(){
		var index=$('#type #voidtype .typerow').index(this);
		$('#type #voidtype .typerow').css({'background-color':'#ffffff'});
		$('#type #voidtype .typerow').prop('id',index);
		$('#type #voidtype .typerow:eq('+index+')').prop('id','focus');
		$('#type #voidtype .typerow:eq('+index+')').css({'background-color':'#E9E9E9'});
		types.tabs('option','disabled',[1]);
		if($('#type #voidtype .typerow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#type #voidtype .typerow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#type #voidtype .typerow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#type #voidtype .typerow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#type #voidtype .typerow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#type #voidtype #return',function(){
		if($('#type #voidtype .typerow input[type="checkbox"]:checked').length>0){//有勾選品項
			var num=[];
			for(var i=0;i<$('#type #voidtype .typerow input[type="checkbox"]').length;i++){
				if($('#type #voidtype .typerow:eq('+i+') input[type="checkbox"]:checked').length>0){
					num.push($('#type #voidtype .typerow:eq('+i+') input[name="number"]').val());
				}
				else{
				}
			}
			if(num.length>0){
				$.ajax({
					url:'./lib/js/return.type.ajax.php',
					method:'post',
					async:false,
					data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'num':num},
					dataType:'json',
					success:function(d){
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
				$('#type ul li:eq(2)').trigger('click');
			}
			else{
			}
		}
		else{
		}
	});
	$(document).on('click','#sectype #alltypes .typerow',function(){
		var index=$('#sectype #alltypes .typerow').index(this);
		$('#sectype #alltypes .typerow').css({'background-color':'#ffffff'});
		$('#sectype #alltypes .typerow:eq('+index+')').css({'background-color':'#E9E9E9'});
		sectypes.tabs('option','disabled',[]);
		var g='';
		var f2='';
		for(var i=0;i<$('#sectype #alltypes .typerow').length;i++){
			if(index==i){
				if(g.length==0){
					g=$('#sectype #alltypes .typerow:eq('+index+') input[name=\"typedep\"]').val()+'-'+$('#sectype #alltypes .typerow:eq('+index+') input[name=\"number\"]').val();
				}
				else{
					g=g+','+$('#sectype #alltypes .typerow:eq('+index+') input[name=\"typedep\"]').val()+'-'+$('#sectype #alltypes .typerow:eq('+index+') input[name=\"number\"]').val();
				}
				if(f2.length==0){
					f2=$('#sectype #alltypes .typerow:eq('+index+') input[name=\"number\"]').val();
				}
				else{
				}
			}
			else{
				if($('#sectype #alltypes .typerow:eq('+i+') input[type=\"checkbox\"]').prop('checked')){
					if(g.length==0){
						g=$('#sectype #alltypes .typerow:eq('+i+') input[name=\"typedep\"]').val()+'-'+$('#sectype #alltypes .typerow:eq('+i+') input[name=\"number\"]').val();
					}
					else{
						g=g+','+$('#sectype #alltypes .typerow:eq('+i+') input[name=\"typedep\"]').val()+'-'+$('#sectype #alltypes .typerow:eq('+i+') input[name=\"number\"]').val();
					}
					if(f2.length==0){
						f2=$('#sectype #alltypes .typerow:eq('+i+') input[name=\"number\"]').val();
					}
					else{
					}
				}
				else{
				}
			}
		}
		if($('#sectype #alltypes .typerow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#sectype #alltypes .typerow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#sectype #alltypes .typerow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#sectype #alltypes .typerow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#sectype #alltypes .typerow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		$('#sectype input[name=\"typegroup\"]').val(g);
		$.ajax({
			url:'./lib/js/getsectypedata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':f2,'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#sectype #edittype').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#sectype #alltypes #create',function(){
		$('#sectype #alltypes .typerow').css({'background-color':'#ffffff'});
		$('#sectype #alltypes .typerow input[type=\"checkbox\"]').prop('checked',false);
		$('#sectype #alltypes .typerow #chimg').attr('src','./img/noch.png');
		$.ajax({
			url:'./lib/js/getsectypedata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(string){
				$('#sectype #edittype').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
		sectypes.tabs('option','disabled',[]);
		sectypes.tabs('option','active',[1]);
	});
	$(document).on('click','#sectype #alltypes #edit',function(){
		if(sectypes.tabs('option','disabled')[0]==1){
		}
		else{
			sectypes.tabs('option','disabled',[]);
			sectypes.tabs('option','active',[1]);
		}
	});
	$(document).on('click','#sectype #alltypes #delete',function(){
		if($('#sectype #alltypes .typerow input[type=\"checkbox\"]:checked').length>0){
			t=confirm('是否確認刪除類別？');
			if(t==true){
				var numberarray=$('input[name="company"]').val()+','+$('input[name="db"]').val();
				for(var i=0;i<$('#sectype #alltypes .typerow input[type=\"checkbox\"]').length;i++){
					if($('#sectype #alltypes .typerow:eq('+i+') input[type=\"checkbox\"]:checked').length>0){
						numberarray=numberarray+','+$('#sectype #alltypes .typerow:eq('+i+') input[name=\"number\"]').val();
					}
					else{
					}
				}
				$.ajax({
					url:'./lib/js/deletesectype.ajax.php',
					method:'post',
					data:{'numbergroup':numberarray},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('#sidebar #allsectype').trigger('click');
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
		}
	});
	$(document).on('click','#sectype #edittype #save',function(){
		if($('#sectype #edittype input[name="name1"]').val()==''&&$('#sectype #edittype input[name="name2"]').val()==''){
			alert('請至少輸入一個名稱。');
		}
		else{
			$('#sectype #edittype #save').prop('disabled',true);
			$('#sectype #edittype #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/savesectype.ajax.php',
				method:'post',
				data:$('#sectype #edittype #sectypeform').serialize(),
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('#sidebar #allsectype').trigger('click');
					$('#sectype #edittype #save').prop('disabled',false);
					$('#sectype #edittype #save').css({'opacity':'1','cursor':'pointer'});
				},
				error:function(e){
					console.log(e);
					$('#sectype #edittype #save').prop('disabled',false);
					$('#sectype #edittype #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','#sectype #edittype #cancel',function(){
		sectypes.tabs('option','disabled',[1]);
		sectypes.tabs('option','active',[0]);
		$('#sectype #edittype').html('');
		$('#sectype #alltypes .typerow').css({'background-color':'#ffffff'});
	});
	
	$(document).on('click','#sectype ul li:eq(0)',function(){
		$.ajax({
			url:'./lib/js/getallsectype.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'management':$('.management').length},
			dataType:'html',
			success:function(string){
				$('#content').html('');
				$('#content').html(string);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#sectype ul li:eq(2)',function(){
		$('#sectype #alltypes .typerow input[type=\"checkbox\"]').prop('checked',false);
		$('#sectype #alltypes .typerow #chimg').attr('src','./img/noch.png');
		sectypes.tabs('option','disabled',[1]);
		$.ajax({
			url:'./lib/js/getvoidsectype.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('#sectype #voidtype').html(d);
			},
			error:function(e){
				console.log(e)
			}
		});
	});
	$(document).on('click','#sectype #voidtype .typerow',function(){
		var index=$('#sectype #voidtype .typerow').index(this);
		$('#sectype #voidtype .typerow').css({'background-color':'#ffffff'});
		$('#sectype #voidtype .typerow').prop('id',index);
		$('#sectype #voidtype .typerow:eq('+index+')').prop('id','focus');
		$('#sectype #voidtype .typerow:eq('+index+')').css({'background-color':'#E9E9E9'});
		sectypes.tabs('option','disabled',[1]);
		if($('#sectype #voidtype .typerow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('#sectype #voidtype .typerow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#sectype #voidtype .typerow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#sectype #voidtype .typerow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#sectype #voidtype .typerow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#sectype #voidtype #return',function(){
		if($('#sectype #voidtype .typerow input[type="checkbox"]:checked').length>0){//有勾選品項
			var num=[];
			for(var i=0;i<$('#sectype #voidtype .typerow input[type="checkbox"]').length;i++){
				if($('#sectype #voidtype .typerow:eq('+i+') input[type="checkbox"]:checked').length>0){
					num.push($('#sectype #voidtype .typerow:eq('+i+') input[name="number"]').val());
				}
				else{
				}
			}
			if(num.length>0){
				$.ajax({
					url:'./lib/js/return.sectype.ajax.php',
					method:'post',
					async:false,
					data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'num':num},
					dataType:'json',
					success:function(d){
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
				$('#sectype ul li:eq(2)').trigger('click');
			}
			else{
			}
		}
		else{
		}
	});
	/*員工維護*/
	/*自訂下拉式選單3個functions*/
	$(document).on('click',"#person11 #powerbox.select_box",function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
    $(document).on('click',document,function(event){
        var eo=$(event.target);
        if($("#person11 .select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('#person11 .option').hide();
    });
	/*赋值给文本框*/
    $(document).on('click',"#person11 #powerbox .option a",function(){
		var index=$('#person11 #powerbox .option a').index(this);
		var value=$("#person11 #powerbox .option a:eq("+index+")").text();
        $("#person11 #powerbox .option a").parent().siblings(".select_txt").text(value);
        $("#person11 input[name='power']#select_value").val($('#person11 #powerbox .option a:eq('+index+')').attr('id'));
    });
	$(document).on('click','.person #person1 .table #personTable .row',function(){
		var index=$('.person #person1 .table #personTable .row').index(this);
		$('.person #person1 .table #personTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.person #person1 .table #personTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.person #person1 .table #personTable .row:eq('+index+')').css({'background-color':'#bccad9'});
		if($('.person #person1 .table #personTable .row:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.person #person1 .table #personTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.person #person1 .table #personTable .row:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.person #person1 .table #personTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.person #person1 .table #personTable .row:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		if(index==0){
			$('.person #person1 #param #prev').val('');
		}
		else{
			$('.person #person1 #param #prev').val($('.person #person1 .table #personTable .row:eq('+(index-1)+') input[type="checkbox"]').val());
		}
		$('.person #person1 #param #focus').val($('.person #person1 .table #personTable .row:eq('+index+') input[type="checkbox"]').val());
		if(index==$('.person #person1 .table #personTable .row').length){
			$('.person #person1 #param #next').val('');
		}
		else{
			$('.person #person1 #param #next').val($('.person #person1 .table #personTable .row:eq('+(index+1)+') input[type="checkbox"]').val());
		}
	});
	$(document).on('click','.person ul li:eq(0)',function(){
		person.tabs('option','disabled',[1,3]);
		person.tabs('option','active',0);
		/*$('.person #person1 .table #personTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.person #person1 .table #personTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.person #person1 .table #personTable .row input[type=\"checkbox\"]').prop('checked',false);
		$('.person #person1 .table #personTable .row #chimg').attr('src','./img/noch.png');*/
		$('.person #person2 .table #personTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.person #person2 .table #personTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.person #person2 .table #personTable .row input[type=\"checkbox\"]').prop('checked',false);
		$('.person #person2 .table #personTable .row #chimg').attr('src','./img/noch.png');
		$.ajax({
			url:'./lib/js/getperson.list.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'json',
			success:function(d){
				$('.person #person1 .table #personTable tbody').html('');
				$.each(d,function(i,v){
					if(v['id']=='admin'){
					}
					else{
						var st="<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='"+v['cardno']+"'></td><td>"+v['cardno']+"</td><td>"+v['id']+"</td><td>"+v['name']+"</td><td>"+v['power']+"</td><td>"+v['pname']+"</td><td>"+v['firstdate']+"</td><td>";
						if(v['out']=='0'){
							st=st+'在職'
						}
						else{
							st=st+'<font color="#ff0000">離職</font>'
						}
						st=st+"</td></tr>";

						$('.person #person1 .table #personTable tbody').append(st);
					}
				});
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.person #person1 #create',function(){
		person.tabs('enable','#person11');
		person.tabs('option','active',1);
		$('.person #person11 h1 center').html('新增帳號');
		/*清空員工表格*/
		$('.person #person11 #cardno').val('');
		$('.person #person11 #cardno').prop('readonly',false);
		$('.person #person11 #cardno').css({'background-color':'#ffffff'});
		$('.person #person11 #type').val('new');
		$('.person #person11 input[data-id="name"]').val('');
		$('.person #person11 #id').val('');
		$('.person #person11 #id').prop('disabled',false);
		$('.person #person11 #pw').val('');
		$('.person #person11 #pw').prop('disabled',false);
		$('.person #person11 #voidpw').val('');
		$('.person #person11 #voidpw').prop('disabled',false);
		$('.person #person11 #paperpw').val('');
		$('.person #person11 #paperpw').prop('disabled',false);
		$('.person #person11 #punchpw').val('');
		$('.person #person11 #punchpw').prop('disabled',false);
		$('.person #person11 #reprintpw').val('');
		$('.person #person11 #reprintpw').prop('disabled',false);
		$('.person #person11 #b').prop('checked',false);
		$('.person #person11 #g').prop('checked',false);
		$('.person #person11 #birth').val('');
		$('.person #person11 #tel').val('');
		$('.person #person11 #address').val('');
		$('.person #person11 #power .select_txt').html('');
		$('.person #person11 #power #select_value').html('');
		$.ajax({
			url:'./lib/js/getpowgroup.list.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="dep"]').val()},
			dataType:'json',
			success:function(d){
				$('.person #person11 #power #powerbox .option').html('');
				$.each(d,function(i,v){
					$('.person #person11 #power #powerbox .option').append('<a id="'+v['pno']+'">'+v['name']+'</a>');
				});
			},
			error:function(e){
				console.log(e);
			}
		});
		$('.person #person11 #firstdate').val('');
		$('.person #person11 #lastdate').val('');
		$('.person #person11 .stories input[class="checkbox[]"]').prop('checked',false);
		/**/
	});
	$(document).on('click','.person #person1 #edit',function(){
		if($('.person #person1 #param #focus').val().length==0){
		}
		else{
			$.ajax({
				url:'./lib/js/getpersondata.ajax.php',
				method:'post',
				data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'focus':$('.person #person1 #param #focus').val(),'prev':$('.person #person1 #param #prev').val(),'next':$('.person #person1 #param #next').val()},
				dataType:'json',
				success:function(d){
					//console.log(d);
					$('.person #person11 h1 center').html('修改帳號');
					$('.person #person11 #type').val('edit');
					$('.person #person11 #cardno').val(d[0]['cardno']);
					$('.person #person11 #cardno').prop('readonly',true);
					$('.person #person11 #cardno').css({'background-color':'#EBEBE4'});
					$('.person #person11 input[data-id="name"]').val(d[0]['name']);
					$('.person #person11 #id').val(d[0]['id']);
					$('.person #person11 #id').prop('disabled',false);
					$('.person #person11 #pw').val(d[0]['pw']);
					$('.person #person11 #pw').prop('disabled',false);
					if(typeof d[0]['voidpw']==="undefined"){
						$('.person #person11 #voidpw').val('');
						$('.person #person11 #voidpw').prop('disabled',false);
					}
					else{
						$('.person #person11 #voidpw').val(d[0]['voidpw']);
						$('.person #person11 #voidpw').prop('disabled',false);
					}
					if(typeof d[0]['paperpw']==="undefined"){
						$('.person #person11 #paperpw').val('');
						$('.person #person11 #paperpw').prop('disabled',false);
					}
					else{
						$('.person #person11 #paperpw').val(d[0]['paperpw']);
						$('.person #person11 #paperpw').prop('disabled',false);
					}
					if(typeof d[0]['punchpw']==="undefined"){
						$('.person #person11 #punchpw').val('');
						$('.person #person11 #punchpw').prop('disabled',false);
					}
					else{
						$('.person #person11 #punchpw').val(d[0]['punchpw']);
						$('.person #person11 #punchpw').prop('disabled',false);
					}
					if(typeof d[0]['reprintpw']==="undefined"){
						$('.person #person11 #reprintpw').val('');
						$('.person #person11 #reprintpw').prop('disabled',false);
					}
					else{
						$('.person #person11 #reprintpw').val(d[0]['reprintpw']);
						$('.person #person11 #reprintpw').prop('disabled',false);
					}
					if(d[0]['sex']==1){
						$('.person #person11 #b').prop('checked',true);
					}
					else{
						$('.person #person11 #b').prop('checked',false);
					}
					if(d[0]['sex']==2){
						$('.person #person11 #g').prop('checked',true);
					}
					else{
						$('.person #person11 #g').prop('checked',false);
					}
					if(d[0]['birth']!=null){
						$('.person #person11 #birth').val(d[0]['birth']);
					}
					else{
						$('.person #person11 #birth').val('');
					}
					if(d[0]['tel']!=null){
						$('.person #person11 #tel').val(d[0]['tel']);
					}
					else{
						$('.person #person11 #tel').val('');
					}
					if(d[0]['address']!=null){
						$('.person #person11 #address').val(d[0]['address']);
					}
					else{
						$('.person #person11 #address').val('');
					}
					$.ajax({
						url:'./lib/js/getpowerlist.ajax.php',
						method:'post',
						data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},//,'power':d[0]['seq']
						dataType:'json',
						success:function(v){
							//console.log(v);
							if(v.length==1){
								$('.person #person11 #power .select_txt').html(v[0]['name']);
								$('.person #person11 #power #select_value').val(v[0]['pno']);
							}
							else{
								var index=1;
								$('.person #person11 #powerbox .option').html('');
								$.each(v,function(i,t){
									if(t['pno']==d[0]['pno']){
										$('.person #person11 #power .select_txt').html(t['name']);
										$('.person #person11 #power #select_value').val(t['pno']);
									}
									else{
									}
									$('.person #person11 #powerbox .option').append('<a id="'+t['pno']+'">'+t['name']+'</a>');
								});
							}
						},
						error:function(e){
							console.log(e);
						}
					});
					$('.person #person11 #firstdate').val(d[0]['firstdate']);
					$('.person #person11 #lastdate').val(d[0]['lastdate']);
					$('.person #person11 #database .persondata .stories input[name="stories[]"]').prop('checked',false);
					var stories=$('.person #person11 #datatable .persondata .stories input[name="stories[]"]').map(function(i,v){
						return $(v).val();
					}).get();
					if(d[0]['viewdb']==null){
					}
					else if(d[0]['viewdb'].match(/only/g).length>0){
						var starr=d[0]['viewdb'].substr(5).split('/,/');
						$.each(starr,function(i,v){
							if($.inArray(v,stories)>=0){
								$('.person #person11 #datatable .persondata .stories input[name="stories[]"]:eq('+$.inArray(v,stories)+')').prop('checked',true);
							}
							else{
							}
						});
					}
					else{//if(d[0]['viewdb'].match('/(boss-)/')||d[0]['viewdb'].match('/(only-)/'))
						$.ajax({
							url:'./lib/js/getdblist.ajax.php',
							method:'post',
							data:{'company':$('input[name="company"]').val(),'type':d[0]['viewdb']},
							dataType:'json',
							success:function(d){
								//console.log(d);
								if(d.length==1){
									if($.inArray(d[0]['no'].toString(),stories)>=0){
										$('.person #person11 #datatable .persondata .stories input[name="stories[]"]:eq('+$.inArray(d[0]['no'].toString(),stories)+')').prop('checked',true);
									}
									else{
									}
								}
								else if(d.legnth>1){
									$.each(d,function(i,v){
										if($.inArray(v['no'].toString(),stories)>=0){
											$('.person #person11 #datatable .persondata .stories input[name="stories[]"]:eq('+$.inArray(v['no'].toString(),stories)+')').prop('checked',true);
										}
										else{
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
					}
					person.tabs('enable','#person11');
					person.tabs('option','active',1);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.person #person1 #delete',function(){
		if($('.person #person1 #param #focus').val().length==0||$('.person #person1 .table #personTable input[class="checkbox[]"]:checked').length==0){
			//console.log('empty');
		}
		else{
			var t=confirm('是否確認刪除帳號資料？');
			if(t==true){
				$.ajax({
					url:'./lib/js/person.delete.php',
					method:'post',
					data:$('.person #person1 .table .personTable').serialize(),
					dataType:'html',
					success:function(d){
						$('.person ul li:eq(0)').trigger('click');
					},
					error:function(e){
						console.log(e);
					}
				});
			}
			else{
			}
		}
	});
	$(document).on('click','.person #person11 #save',function(){
		if($('.person #person11 #datatable #cardno').val().length==0||$('.person #person11 #datatable input[data-id="name"]').val().length==0||$('.person #person11 #datatable #id').length==0||$('.person #person11 #datatable #power input[name="power"]').val()==''){
			$('.mys').html('工號、姓名、帳號與權限不得為空。');
			mys.dialog('open');
		}
		else{
			$('.person #person11 #save').prop('disabled',true);
			$('.person #person11 #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/person.save.php',
				method:'post',
				data:$('.person #person11 #datatable .persondata').serialize(),
				dataType:'html',
				success:function(d){
					//console.log(d);
					if(d=='already'){
						alert('工號或帳號已存在。');
					}
					else{
						$('.person ul li:eq(0)').trigger('click');
					}
					$('.person #person11 #save').prop('disabled',false);
					$('.person #person11 #save').css({'opacity':'1','cursor':'pointer'});
				},
				error:function(e){
					console.log(e);
					$('.person #person11 #save').prop('disabled',false);
					$('.person #person11 #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','.person #person11 #cancel',function(){
		person.tabs('option','disabled',[1,3]);
		person.tabs('option','active',0);
	});
	$(document).on('click','.person #person2 .table #powerTable .row',function(){
		var index=$('.person #person2 .table #powerTable .row').index(this);
		$('.person #person2 .table #powerTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.person #person2 .table #powerTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.person #person2 .table #powerTable .row:eq('+index+')').css({'background-color':'#bccad9'});
		if($('.person #person2 .table #powerTable .row:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.person #person2 .table #powerTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.person #person2 .table #powerTable .row:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.person #person2 .table #powerTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.person #person2 .table #powerTable .row:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		if(index==0){
			$('.person #person2 #param #prev').val('');
		}
		else{
			$('.person #person2 #param #prev').val($('.person #person2 .table #powerTable .row:eq('+(index-1)+') input[type="checkbox"]').val());
		}
		$('.person #person2 #param #focus').val($('.person #person2 .table #powerTable .row:eq('+index+') input[type="checkbox"]').val());
		if(index==$('.person #person2 .table #powerTable .row').length){
			$('.person #person2 #param #next').val('');
		}
		else{
			$('.person #person2 #param #next').val($('.person #person2 .table #powerTable .row:eq('+(index+1)+') input[type="checkbox"]').val());
		}
	});
	$(document).on('click','.person ul li:eq(2)',function(){
		person.tabs('option','disabled',[1,3]);
		person.tabs('option','active',2);
		$('.person #person1 .table #personTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.person #person1 .table #personTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.person #person1 .table #personTable .row input[type=\"checkbox\"]').prop('checked',false);
		$('.person #person1 .table #personTable .row #chimg').attr('src','./img/noch.png');
		/*$('.person #person2 .table #powerTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.person #person2 .table #powerTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.person #person2 .table #powerTable .row input[type=\"checkbox\"]').prop('checked',false);
		$('.person #person2 .table #powerTable .row #chimg').attr('src','./img/noch.png');*/
		$.ajax({
			url:'./lib/js/getpowgroup.list.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="dep"]').val()},
			dataType:'json',
			success:function(d){
				$('.person #person2 .table #powerTable tbody').html('');
				$.each(d,function(i,v){
					var st="<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='pg[]' style='display:none;' value='"+v['pno']+"'></td><td>"+v['seq']+"</td><td>"+v['name']+"</td><td>";
					if(v['state']=='1'){
						st=st+'啟用'
					}
					else{
						st=st+'<font color="#ff0000">停用</font>'
					}
					st=st+"</td></tr>";

					$('.person #person2 .table #powerTable tbody').append(st);
				});
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.person #person21 #datatable .fun label',function(){
		var index=$('.person #person21 #datatable .fun label').index(this);
		var temp=$('.person #person21 #datatable .fun label:eq('+index+') input').prop('class').split('-');
		var name=$('.person #person21 #datatable .fun label:eq('+index+') input').prop('name').substr(0,$('.person #person21 #datatable .fun label:eq('+index+') input').prop('name').length-2);
		if(temp.length==2){
			if($('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked')){
				$('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked',false);
				$('.person #person21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/noch.png');

				$('.person #person21 #datatable .fun div.'+name+' input[class="'+temp[0]+'"]').prop('checked',false);
				$('.person #person21 #datatable .fun div.'+name+' input[class="'+temp[0]+'"]').parent().find('#chimg').prop('src','./img/noch.png');

				$('.person #person21 #datatable .fun input[name="'+name+'[]"]:eq(0)').prop('checked',false);
				$('.person #person21 #datatable .fun input[name="'+name+'[]"]:eq(0)').parent().find('#chimg').prop('src','./img/noch.png');
			}
			else{
				$('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked',true);
				$('.person #person21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/onch.png');
				
				if($('.person #person21 #datatable .fun div.'+name+' div.'+temp[0]+' input').length==$('.person #person21 #datatable .fun div.'+name+' div.'+temp[0]+' input:checked').length){
					$('.person #person21 #datatable .fun div.'+name+' input[class="'+temp[0]+'"]').prop('checked',true);
					$('.person #person21 #datatable .fun div.'+name+' input[class="'+temp[0]+'"]').parent().find('#chimg').prop('src','./img/onch.png');

				}
				else{
				}
				
				if($('.person #person21 #datatable .fun div.'+name+' input').length==$('.person #person21 #datatable .fun div.'+name+' input:checked').length){
					$('.person #person21 #datatable .fun input[name="'+name+'[]"]:eq(0)').prop('checked',true);
					$('.person #person21 #datatable .fun input[name="'+name+'[]"]:eq(0)').parent().find('#chimg').prop('src','./img/onch.png');
				}
				else{
				}
			}
		}
		else{
			if(temp[0]!='0'){
				if($('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked')){
					$('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked',false);
					$('.person #person21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/noch.png');

					$('.person #person21 #datatable .fun div.'+name+' div.'+temp[0]+' input').prop('checked',false);
					$('.person #person21 #datatable .fun div.'+name+' div.'+temp[0]+' input').parent().find('#chimg').prop('src','./img/noch.png');

					$('.person #person21 #datatable .fun input[name="'+name+'[]"]:eq(0)').prop('checked',false);
					$('.person #person21 #datatable .fun input[name="'+name+'[]"]:eq(0)').parent().find('#chimg').prop('src','./img/noch.png');
				}
				else{
					$('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked',true);
					$('.person #person21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/onch.png');

					$('.person #person21 #datatable .fun div.'+name+' div.'+temp[0]+' input').prop('checked',true);
					$('.person #person21 #datatable .fun div.'+name+' div.'+temp[0]+' input').parent().find('#chimg').prop('src','./img/onch.png');
					
					if($('.person #person21 #datatable .fun div.'+name+' input').length==$('.person #person21 #datatable .fun div.'+name+' input:checked').length){
						$('.person #person21 #datatable .fun input[name="'+name+'[]"]:eq(0)').prop('checked',true);
						$('.person #person21 #datatable .fun input[name="'+name+'[]"]:eq(0)').parent().find('#chimg').prop('src','./img/onch.png');
					}
					else{
					}
				}
			}
			else{
				if($('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked')){
					$('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked',false);
					$('.person #person21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/noch.png');

					$('.person #person21 #datatable .fun div.'+name+' input').prop('checked',false);
					$('.person #person21 #datatable .fun div.'+name+' input').parent().find('#chimg').prop('src','./img/noch.png');
				}
				else{
					$('.person #person21 #datatable .fun label:eq('+index+') input').prop('checked',true);
					$('.person #person21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/onch.png');
					
					$('.person #person21 #datatable .fun div.'+name+' input').prop('checked',true);
					$('.person #person21 #datatable .fun div.'+name+' input').parent().find('#chimg').prop('src','./img/onch.png');
				}
			}
		}
	});
	$(document).on('click','.person #person2 #create',function(){
		person.tabs('enable','#person21');
		person.tabs('option','active',3);
		$('.person #person21 h1 center').html('新增權限');
		/*清空員工表格*/
		$('.person #person21 #pno').val('');
		$('.person #person21 #seq').val($('.person #person21 #seq').prop('min'));
		$('.person #person21 input[data-id="name"]').val('');
		$('.person #person21 input[name^="rear"]').prop('checked',false);
		$('.person #person21 input[name^="rear"]').parent().find('#chimg').prop('src','./img/noch.png');
		$('.person #person21 input[name^="front"]').prop('checked',false);
		$('.person #person21 input[name^="front"]').parent().find('#chimg').prop('src','./img/noch.png');
		$('.person #person21 #stop').prop('checked',false);
		/**/
	});
	$(document).on('click','.person #person2 #edit',function(){
		if($('.person #person2 #param #focus').val().length==0){
		}
		else{
			$.ajax({
				url:'./lib/js/getpowerdata.ajax.php',
				method:'post',
				data:{'company':$('input[name="company"]').val(),'dep':$('input[name="dep"]').val(),'focus':$('.person #person2 #param #focus').val(),'prev':$('.person #person2 #param #prev').val(),'next':$('.person #person2 #param #next').val()},
				dataType:'json',
				success:function(d){
					//console.log(d);
					$('.person #person21 h1 center').html('修改權限');
					$('.person #person21 #pno').val(d[0]['pno']);
					$('.person #person21 #seq').val(d[0]['seq']);
					$('.person #person21 input[data-id="name"]').val(d[0]['name']);
					var rear=$('.person #person21 input[name="rear[]"]').map(function(i,v){
						return $(v).val();
					}).get();
					var front=$('.person #person21 input[name="front[]"]').map(function(i,v){
						return $(v).val();
					}).get();
					/*for(var i=0;i<d.length;i++){
						if($.inArray(d[i]['no'].toString(),rear)>=0){
							$('.person #person21 input[name="rear[]"]:eq('+$.inArray(d[i]['no'].toString(),rear)+')').prop('checked',true);
							$('.person #person21 input[name="rear[]"]:eq('+$.inArray(d[i]['no'].toString(),rear)+')').parent().find('#chimg').prop('src','./img/onch.png');
						}
						else{
						}
						if($.inArray(d[i]['no'].toString(),front)>=0){
							$('.person #person21 input[name="front[]"]:eq('+$.inArray(d[i]['no'].toString(),front)+')').prop('checked',true);
							$('.person #person21 input[name="front[]"]:eq('+$.inArray(d[i]['no'].toString(),front)+')').parent().find('#chimg').prop('src','./img/onch.png');
						}
						else{
						}
					}*/
					if(d[0]['state']==1){
						$('.person #person21 #stop').prop('checked',false);
					}
					else{
						$('.person #person21 #stop').prop('checked',true);
					}
					person.tabs('enable','#person21');
					person.tabs('option','active',3);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.person #person2 #delete',function(){
		if($('.person #person2 #param #focus').val().length==0||$('.person #person2 .table #powerTable input[class="checkbox[]"]:checked').length==0){
			//console.log('empty');
		}
		else{
			$.ajax({
				url:'./lib/js/powergroup.delete.php',
				method:'post',
				data:$('.person #person2 .table .powerTable').serialize(),
				dataType:'html',
				success:function(d){
					$('.person ul li:eq(2)').trigger('click');
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.person #person21 #save',function(){
		if(parseInt($('.person #person21 #datatable #seq').val())<parseInt($('.person #person21 #datatable #seq').prop('min'))||$('.person #person21 #datatable input[data-id="name"]').val().length==0){
			$('.mys').html('權限權重不合法、權限名稱不得為空。');
			mys.dialog('open');
		}
		else{
			$('.person #person21 #save').prop('disabled',true);
			$('.person #person21 #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/powergroup.save.php',
				method:'post',
				data:$('.person #person21 #datatable .powergroup').serialize(),
				dataType:'html',
				success:function(d){
					$('.person ul li:eq(2)').trigger('click');
					//console.log(d);
					$('.person #person21 #save').prop('disabled',false);
					$('.person #person21 #save').css({'opacity':'1','cursor':'pointer'});
				},
				error:function(e){
					console.log(e);
					$('.person #person21 #save').prop('disabled',false);
					$('.person #person21 #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','.person #person21 #cancel',function(){
		person.tabs('option','disabled',[1,3]);
		person.tabs('option','active',2);
	});
	$(document).on('click','.personnel #personnel1 .table #personnelTable .row',function(){
		var index=$('.personnel #personnel1 .table #personnelTable .row').index(this);
		$('.personnel #personnel1 .table #personnelTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.personnel #personnel1 .table #personnelTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.personnel #personnel1 .table #personnelTable .row:eq('+index+')').css({'background-color':'#bccad9'});
		if($('.personnel #personnel1 .table #personnelTable .row:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.personnel #personnel1 .table #personnelTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.personnel #personnel1 .table #personnelTable .row:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.personnel #personnel1 .table #personnelTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.personnel #personnel1 .table #personnelTable .row:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		if(index==0){
			$('.personnel #personnel1 #param #prev').val('');
		}
		else{
			$('.personnel #personnel1 #param #prev').val($('.personnel #personnel1 .table #personnelTable .row:eq('+(index-1)+') input[type="checkbox"]').val());
		}
		$('.personnel #personnel1 #param #focus').val($('.personnel #personnel1 .table #personnelTable .row:eq('+index+') input[type="checkbox"]').val());
		if(index==$('.personnel #personnel1 .table #personnelTable .row').length){
			$('.personnel #personnel1 #param #next').val('');
		}
		else{
			$('.personnel #personnel1 #param #next').val($('.personnel #personnel1 .table #personnelTable .row:eq('+(index+1)+') input[type="checkbox"]').val());
		}
		$('.personnel #personnel12 #data #perno').val($('.personnel #personnel1 .table #personnelTable .row:eq('+index+') input[type="checkbox"]').val());
		$('.personnel #personnel12 #data #percard').html($('.personnel #personnel1 .table #personnelTable .row:eq('+index+') td:eq(1)').html());
		$('.personnel #personnel12 #data #name').html($('.personnel #personnel1 .table #personnelTable .row:eq('+index+') td:eq(2)').html());
	});
	$(document).on('click','.personnel ul li:eq(0)',function(){
		personnel.tabs('option','disabled',[1,2]);
		personnel.tabs('option','active',0);
		$.ajax({
			url:'./lib/js/getpersonnel.list.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="dep"]').val()},
			dataType:'json',
			success:function(d){
				$('.personnel #personnel1 .table #personnelTable tbody').html('');
				$.each(d,function(i,v){
					var st="<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='"+v['perno']+"'></td><td>"+v['percard']+"</td><td>";
					if(v['name']==null){
					}
					else{
						st=st+v['name'];
					}
					st=st+"</td><td>";
					if(v['tel']==null){
					}
					else{
						st=st+v['tel'];
					}
					st=st+"</td><td>";
					if(v['address']==null){
					}
					else{
						st=st+v['address'];
					}
					st=st+"</td><td>";
					if(v['sosname']==null){
					}
					else{
						st=st+v['sosname'];
					}
					st=st+"</td><td>";
					if(v['sostel']==null){
					}
					else{
						st=st+v['sostel'];
					}
					st=st+"</td><td>";
					if(v['state']=='0'){
						st=st+'停用';
					}
					else{
					}
					st=st+"</td></tr>";

					$('.personnel #personnel1 .table #personnelTable tbody').append(st);
					$('.personnel #param #focus').val('');
					$('.personnel #personnel12 #datatable').html('');
				});
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.personnel #personnel1 #create',function(){
		personnel.tabs('enable','#personnel11');
		personnel.tabs('option','active',1);
		var htmlst='';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				console.log(d);
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		$('.personnel #personnel11 h1 center').html(htmlst);
		/*清空員工表格*/
		$('.personnel #personnel11 #perno').val('');
		$('.personnel #personnel11 #percard').val('');
		$('.personnel #personnel11 #type').val('new');
		$('.personnel #personnel11 #name').val('');
		$('.personnel #personnel11 #tel').val('');
		$('.personnel #personnel11 #address').val('');
		$('.personnel #personnel11 #sosname').val('');
		$('.personnel #personnel11 #sostel').val('');
		/**/
	});
	$(document).on('click','.personnel #personnel1 #punch',function(){
		if($('.personnel #personnel1 #param #focus').val().length==0){
		}
		else{
			personnel.tabs('enable','#personnel12');
			personnel.tabs('option','active',2);
			/*清空員工表格*/
		}
	});
	$(document).on('click','.personnel #personnel1 #edit',function(){
		if($('.personnel #personnel1 #param #focus').val().length==0){
		}
		else{
			$.ajax({
				url:'./lib/js/getpersonneldata.ajax.php',
				method:'post',
				data:{'company':$('input[name="company"]').val(),'dep':$('input[name="dep"]').val(),'focus':$('.personnel #personnel1 #param #focus').val(),'prev':$('.personnel #personnel1 #param #prev').val(),'next':$('.personnel #personnel1 #param #next').val()},
				dataType:'json',
				success:function(d){
					var htmlst='';
					$.ajax({
						url:'./lib/js/getininame.ajax.php',
						method:'post',
						async:false,
						data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
						dataType:'html',
						success:function(d){
							htmlst=htmlst+d;
						},
						error:function(e){
							console.log(e);
						}
					});
					$('.personnel #personnel11 h1 center').html(htmlst);
					$('.personnel #personnel11 #type').val('edit');
					$('.personnel #personnel11 #perno').val(d[0]['perno']);
					$('.personnel #personnel11 #percard').val(d[0]['percard']);
					$('.personnel #personnel11 #name').val(d[0]['name']);
					if(d[0]['tel']!=null){
						$('.personnel #personnel11 #tel').val(d[0]['tel']);
					}
					else{
						$('.personnel #personnel11 #tel').val('');
					}
					if(d[0]['address']!=null){
						$('.personnel #personnel11 #address').val(d[0]['address']);
					}
					else{
						$('.personnel #personnel11 #address').val('');
					}
					if(d[0]['sosname']!=null){
						$('.personnel #personnel11 #sosname').val(d[0]['sosname']);
					}
					else{
						$('.personnel #personnel11 #sosname').val('');
					}
					if(d[0]['sostel']!=null){
						$('.personnel #personnel11 #sostel').val(d[0]['sostel']);
					}
					else{
						$('.personnel #personnel11 #sostel').val('');
					}
					personnel.tabs('enable','#personnel11');
					personnel.tabs('option','active',1);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.personnel #personnel1 #delete',function(){
		if($('.personnel #personnel1 #param #focus').val().length==0||$('.personnel #personnel1 .table #personnelTable input[class="checkbox[]"]:checked').length==0){
			//console.log('empty');
		}
		else{
			var t=confirm('是否確認刪除員工資料？');
			if(t==true){
				$.ajax({
					url:'./lib/js/personnel.delete.php',
					method:'post',
					data:$('.personnel #personnel1 .table .personnelTable').serialize(),
					dataType:'html',
					success:function(d){
						$('.personnel ul li:eq(0)').trigger('click');
					},
					error:function(e){
						console.log(e);
					}
				});
			}
			else{
			}
		}
	});
	$(document).on('click','.personnel #personnel11 #save',function(){
		if($('.personnel #personnel11 #datatable #percard').val().length==0||$('.personnel #personnel11 #datatable #name').val().length==0){
			$('.mys').html('員工編號、員工姓名不得為空。');
			mys.dialog('open');
		}
		else{
			$('.personnel #personnel11 #save').prop('disabled',true);
			$('.personnel #personnel11 #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/personnel.save.php',
				method:'post',
				data:$('.personnel #personnel11 #datatable .personneldata').serialize(),
				dataType:'html',
				success:function(d){
					console.log(d);
					if(d=='already'){
						alert('員工編號已存在。');
					}
					else{
						$('.personnel ul li:eq(0)').trigger('click');
					}
					$('.personnel #personnel11 #save').prop('disabled',false);
					$('.personnel #personnel11 #save').css({'opacity':'1','cursor':'pointer'});
				},
				error:function(e){
					console.log(e);
					$('.personnel #personnel11 #save').prop('disabled',false);
					$('.personnel #personnel11 #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','.personnel #personnel11 #cancel',function(){
		personnel.tabs('option','disabled',[1,2]);
		personnel.tabs('option','active',0);
	});
	$(document).on('click','.personnel #personnel12 #data #search',function(){
		var start=new Date($('.personnel #personnel12 #data input[name="startdate"]').val());
		var end=new Date($('.personnel #personnel12 #data input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('請輸入合法時間。');
		}
		else if(start.getTime()>now.getTime()){
			alert('請輸入合法時間。');
		}
		else{
			$.ajax({
				url:'./lib/js/getpunch.ajax.php',
				method:'post',
				data:$('.personnel #personnel12 #data').serialize(),
				dateType:'html',
				success:function(d){
					//console.log(d);
					if(d=='empty'){
						$('.personnel #personnel12 #datatable').html('查詢時段中，該人員無打卡記錄。\n請確認查詢時段是否正確。');
					}
					else{
						$('.personnel #personnel12 #datatable').html(d);
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.personnel #personnel1 #expall',function(){
		expmsg.dialog('open');
	});
	$('.expmsg').on('click','#cancel',function(){
		expmsg.dialog('close');
	});
	$('.expmsg').on('click','#exp',function(){
		var start=new Date($('.expmsg input[name="startdate"]').val());
		var end=new Date($('.personnel input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('請輸入合法時間。');
		}
		else if(start.getTime()>now.getTime()){
			alert('請輸入合法時間。');
		}
		else{
			var filename='';
			$.ajax({
				url:'./lib/js/getallexpdata.ajax.php',
				method:'post',
				async: false,
				data:{'company':$('.personnel #personnel12 #data input[name="company"]').val(),'dep':$('.personnel #personnel12 #data input[name="dep"]').val(),'startdate':$('.expmsg #condition input[name="startdate"]').val(),'enddate':$('.expmsg #condition input[name="enddate"]').val()},
				dataType:'html',
				success:function(d){
					console.log(d);
					filename=d;
					window.open('./lib/js/getCSV.php?company='+$('.personnel #personnel12 input[name="company"]').val()+'&dep='+$('.personnel #personnel12 input[name="dep"]').val()+'&file='+d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.personnel #personnel12 #data #exp',function(){
		var start=new Date($('.personnel #personnel12 #data input[name="startdate"]').val());
		var end=new Date($('.personnel #personnel12 #data input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('請輸入合法時間。');
		}
		else if(start.getTime()>now.getTime()){
			alert('請輸入合法時間。');
		}
		else{
			var filename='';

			$.ajax({
				url:'./lib/js/getexpdata.ajax.php',
				method:'post',
				async: false,
				data:{'company':$('.personnel #personnel12 #data input[name="company"]').val(),'dep':$('.personnel #personnel12 #data input[name="dep"]').val(),'perno':$('.personnel #personnel12 #data input[name="perno"]').val(),'startdate':$('.personnel #personnel12 #data input[name="startdate"]').val(),'enddate':$('.personnel #personnel12 #data input[name="enddate"]').val()},
				dataType:'html',
				success:function(d){
					console.log(d);
					filename=d;
					window.open('./lib/js/getCSV.php?company='+$('.personnel #personnel12 input[name="company"]').val()+'&dep='+$('.personnel #personnel12 input[name="dep"]').val()+'&file='+d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	/**/
	$(document).on('click',"#member11 #powerbox.select_box",function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
    $(document).on('click',document,function(event){
        var eo=$(event.target);
        if($("#member11 .select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('#member11 .option').hide();
    });
	/*赋值给文本框*/
    $(document).on('click',"#member11 #powerbox .option a",function(){
		var index=$('#member11 #powerbox .option a').index(this);
		var value=$("#member11 #powerbox .option a:eq("+index+")").text();
        $("#member11 #powerbox .option a").parent().siblings(".select_txt").text(value);
        $("#member11 input[name='power']#select_value").val($('#member11 #powerbox .option a:eq('+index+')').attr('id'));
    });
	$(document).on('click',"#member11 #howknowbox.select_box",function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
	/*赋值给文本框*/
    $(document).on('click',"#member11 #howknowbox .option a",function(){
		var index=$('#member11 #howknowbox .option a').index(this);
		var value=$("#member11 #howknowbox .option a:eq("+index+")").text();
        $("#member11 #howknowbox .option a").parent().siblings(".select_txt").text(value);
        $("#member11 input[name='howknow']#select_value").val($('#member11 #howknowbox .option a:eq('+index+')').attr('id'));
		if($('#member11 #howknowbox .option a:eq('+index+')').attr('id')=='other'){
			$('#member11 input[name="othhow"]').prop('type','text');
		}
		else{
			$('#member11 input[name="othhow"]').prop('type','hidden');
		}
    });
	$(document).on('click','.member #member1 .table #memberTable .row',function(){
		var index=$('.member #member1 .table #memberTable .row').index(this);
		$('.member #member1 .table #memberTable .row').prop('id','');
		$('.member #member1 .table #memberTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.member #member1 .table #memberTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.member #member1 .table #memberTable .row:eq('+index+')').css({'background-color':'#bccad9'});
		$('.member #member1 .table #memberTable .row:eq('+index+')').prop('id','focus');
		if($('.member #member1 .table #memberTable .row:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.member #member1 .table #memberTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.member #member1 .table #memberTable .row:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.member #member1 .table #memberTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.member #member1 .table #memberTable .row:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		if(index==0){
			$('.member #member1 #param #prev').val('');
		}
		else{
			$('.member #member1 #param #prev').val($('.member #member1 .table #memberTable .row:eq('+(index-1)+') input[type="checkbox"]').val());
		}
		$('.member #member1 #param #focus').val($('.member #member1 .table #memberTable .row:eq('+index+') input[type="checkbox"]').val());
		if(index==$('.member #member1 .table #memberTable .row').length){
			$('.member #member1 #param #next').val('');
		}
		else{
			$('.member #member1 #param #next').val($('.member #member1 .table #memberTable .row:eq('+(index+1)+') input[type="checkbox"]').val());
		}
	});
	$(document).on('click','.member ul li:eq(0)',function(){
		member.tabs('option','disabled',[1,3]);
		member.tabs('option','active',0);
		/*$('.member #member1 .table #memberTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.member #member1 .table #memberTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.member #member1 .table #memberTable .row input[type=\"checkbox\"]').prop('checked',false);
		$('.member #member1 .table #memberTable .row #chimg').attr('src','./img/noch.png');*/
		$('.member #member2 .table #memberTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.member #member2 .table #memberTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.member #member2 .table #memberTable .row input[type=\"checkbox\"]').prop('checked',false);
		$('.member #member2 .table #memberTable .row #chimg').attr('src','./img/noch.png');
		$.ajax({
			url:'./lib/js/getmember.list.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'json',
			success:function(d){
				$('.member #member1 .table #memberTable tbody').html('');
				$.each(d,function(i,v){
					if(v['lastdate']!=null){
					}
					else{
						var st="<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='"+v['memno']+"'></td><td>"+v['cardno']+"</td><td>"+v['name']+"</td><td>"+v['pname']+"</td><td>"+v['tel']+"</td><td style='text-align:right;'>";
						if(v['point']==null){
						}
						else{
							st=st+v['point'];
						}
						if(v['money']==''||v['money']==null){
							st=st+"</td><td></td>";
						}
						else{
							st=st+"</td><td style='text-align:right;'>"+v['money']+"</td>";
						}
						if(v['remark']==null){
							st=st+"<td></td>";
						}
						else{
							st=st+"<td>"+v['remark']+"</td>";
						}
						st=st+"<td>"+v['firstdate']+"</td>";
						st=st+"</tr>";
					}

					$('.member #member1 .table #memberTable tbody').append(st);
				});
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.member #member1 #memsalelist',function(){
		if($('.member #member1 .table #memberTable .row#focus').length>0){
			$('.member #member3 #senddata select[name="memno"] option').prop('selected',false);
			$('.member #member3 #senddata select[name="memno"] #'+$('.member #member1 .table #memberTable .row#focus input[name="no[]"]').val()).prop('selected',true);
		}
		else{
			$('.member #member3 #senddata select[name="memno"] option').prop('selected',false);
			$('.member #member3 #senddata select[name="memno"] option:eq(0)').prop('selected',true);
		}
		$('.member #member3 .table').html('');
		member.tabs('option','active',4);
	});
	$(document).on('click','.member #member1 #create',function(){
		member.tabs('enable','#member11');
		member.tabs('option','active',1);
		var htmlst='';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		$('.member #member11 h1 center').html(htmlst);
		/*清空會員表格*/
		$('.member #member11 #memno').val('');
		$('.member #member11 #cardno').val('');
		$('.member #member11 #cardno').prop('readonly',false);
		$('.member #member11 #cardno').css({'background-color':'#ffffff'});
		$('.member #member11 #type').val('new');
		$('.member #member11 #name').val('');
		$('.member #member11 #id').val('');
		$('.member #member11 #id').prop('disabled',false);
		$('.member #member11 #pw').val('');
		$('.member #member11 #pw').prop('disabled',false);
		$('.member #member11 #b').prop('checked',false);
		$('.member #member11 #g').prop('checked',false);
		$('.member #member11 #birth').val('');
		$('.member #member11 #tel').val('');
		$('.member #member11 #tel2').val('');
		$('.member #member11 #setting').val('');
		$('.member #member11 #point').val('0');
		$('.member #member11 #money').val('0');
		$('.member #member11 #companynumber').val('');
		$('.member #member11 #email').val('');
		$('.member #member11 input[name="receve"]').prop('checked',false);
		$('.member #member11 #local option').prop('selected',false);
		$('.member #member11 #local').find('#TW').prop('selected',true);
		$('.member #member11 #twlocal').css({'display':''});
		$('.member #member11 #chlocal').css({'display':'none'});
		$('.member #member11 #remark').val('');
		$('.member #member11 #zip').val('');
		$('.member #member11 #address').val('');
		$('.member #member11 #howknow .select_txt').html('');
		$('.member #member11 #howknow #select_value').html('');
		$('.member #member11 #power .select_txt').html('');
		$('.member #member11 #power #select_value').html('');
		$.ajax({
			url:'./lib/js/getlevellist.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'json',
			success:function(d){
				$('.member #member11 #power #powerbox .option').html('');
				$.each(d,function(i,v){
					$('.member #member11 #power #powerbox .option').append('<a id="'+v['pno']+'">'+v['name']+'</a>');
				});
			},
			error:function(e){
				console.log(e);
			}
		});
		var dt = new Date();
		$('.member #member11 #firstdate').val(dt.getFullYear()+'-'+(parseInt(dt.getMonth())+1)+'-'+dt.getDate());
		$('.member #member11 .stories input[class="checkbox[]"]').prop('checked',false);
		/**/
	});
	$(document).on('change','.member #member11 #local',function(){
		if($('#local').val()=='TW'){
			$('#twlocal').css({'display':''});
			$('#chlocal').css({'display':'none'});
			/*$('.r1').css({'background-color':'#ffffff'});
			$('.r3').css({'background-color':'#f0f0f0'});
			$('.r4').css({'background-color':'#ffffff'});*/
		}
		else if($('#local').val()=='CN'){
			$('#twlocal').css({'display':'none'});
			$('#chlocal').css({'display':''});
			/*$('.r2').css({'background-color':'#ffffff'});
			$('.r3').css({'background-color':'#f0f0f0'});
			$('.r4').css({'background-color':'#ffffff'});*/
		}
		else{
			$('#twlocal').css({'display':'none'});
			$('#chlocal').css({'display':'none'});
			/*$('.r3').css({'background-color':'#ffffff'});
			$('.r4').css({'background-color':'#f0f0f0'});*/
		}
	});
	$(document).on('click','.member #member1 #edit',function(){
		if($('.member #member1 #param #focus').val().length==0){
		}
		else{
			$.ajax({
				url:'./lib/js/getmemberdata.ajax.php',
				method:'post',
				data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'focus':$('.member #member1 #param #focus').val(),'prev':$('.member #member1 #param #prev').val(),'next':$('.member #member1 #param #next').val()},
				dataType:'json',
				success:function(d){
					//console.log(d);
					var htmlst='';
					$.ajax({
						url:'./lib/js/getininame.ajax.php',
						method:'post',
						async:false,
						data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
						dataType:'html',
						success:function(d){
							htmlst=htmlst+d;
						},
						error:function(e){
							console.log(e);
						}
					});
					$('.member #member11 h1 center').html(htmlst);
					$('.member #member11 #type').val('edit');
					$('.member #member11 #memno').val(d[0]['memno']);
					$('.member #member11 #cardno').val(d[0]['cardno']);
					$('.member #member11 #cardno').prop('readonly',true);
					$('.member #member11 #cardno').css({'background-color':'#EBEBE4'});
					$('.member #member11 #name').val(d[0]['name']);
					$('.member #member11 #id').val(d[0]['id']);
					$('.member #member11 #id').prop('disabled',false);
					$('.member #member11 #pw').val(d[0]['pw']);
					$('.member #member11 #pw').prop('disabled',false);
					if(d[0]['sex']==1){
						$('.member #member11 #b').prop('checked',true);
					}
					else{
						$('.member #member11 #b').prop('checked',false);
					}
					if(d[0]['sex']==2){
						$('.member #member11 #g').prop('checked',true);
					}
					else{
						$('.member #member11 #g').prop('checked',false);
					}
					if(d[0]['birth']!=null){
						$('.member #member11 #birth').val(d[0]['birth']);
					}
					else{
						$('.member #member11 #birth').val('');
					}
					if(d[0]['tel']!=null){
						$('.member #member11 #tel').val(d[0]['tel']);
					}
					else{
						$('.member #member11 #tel').val('');
					}
					if(d[0]['tel2']!=null){
						$('.member #member11 #tel2').val(d[0]['tel2']);
					}
					else{
						$('.member #member11 #tel2').val('');
					}
					$('.member #member11 #setting').val(d[0]['setting']);
					$('.member #member11 #point').val(d[0]['point']);
					$('.member #member11 #money').val(d[0]['money']);
					$('.member #member11 #companynumber').val(d[0]['companynumber']);
					if(d[0]['email']!=null){
						$('.member #member11 #email').val(d[0]['email']);
					}
					else{
						$('.member #member11 #email').val('');
					}
					if(d[0]['receve']!=null){
						$('.member #member11 input[name="receve"]').prop('checked',true);
					}
					else{
						$('.member #member11 input[name="receve"]').prop('checked',false);
					}
					if(d[0]['country']!=null){
						$('.member #member11 #local option').prop('selected',false);
						$('.member #member11 #local').find('#'+d[0]['country']).prop('selected',true);
						if(d[0]['country']=='TW'){
							$('.member #member11 #twlocal').css({'display':''});
							$('.member #member11 #twlocal select[name="sublocal"] option[value="'+d[0]['local']+'"]').prop('selected',true);
							$('.member #member11 #chlocal').css({'display':'none'});
						}
						else if(d[0]['country']=='CN'){
							$('.member #member11 #twlocal').css({'display':'none'});
							$('.member #member11 #chlocal').css({'display':''});
							$('.membet #member11 #chlocal select[name="subclocal"] option[value="'+d[0]['local']+'"]').prop('selected',true);
						}
						else{
							$('.member #member11 #twlocal').css({'display':'none'});
							$('.member #member11 #chlocal').css({'display':'none'});
						}
					}
					else{
						$('.member #member11 #email').val('');
					}
					if(d[0]['zip']!=null){
						$('.member #member11 #zip').val(d[0]['zip']);
					}
					else{
						$('.member #member11 #zip').val('');
					}
					if(d[0]['address']!=null){
						$('.member #member11 #address').val(d[0]['address']);
					}
					else{
						$('.member #member11 #address').val('');
					}
					$.ajax({
						url:'./lib/js/getlevellist.ajax.php',
						method:'post',
						data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
						dataType:'json',
						success:function(d){
							console.log(d);
							if(d.length==1){
								$('.member #member11 #power .select_txt').html(d[0]['name']);
								$('.member #member11 #power #select_value').val(d[0]['pno']);
							}
							else{
								var index=1;
								$('.member #member11 #powerbox .option').html('');
								$.each(d,function(i,v){
									$('.member #member11 #powerbox .option').append('<a id="'+v['pno']+'">'+v['name']+'</a>');
								});
								$('.member #member11 #power .select_txt').html(d[0]['name']);
								$('.member #member11 #power #select_value').val(d[0]['pno']);
							}
						},
						error:function(e){
							console.log(e);
						}
					});
					$('.member #member11 #howknowbox .select_txt').html(d[0]['howknow']);
					$('.member #member11 input[name="howknow"]#select_value').val(d[0]['howknow']);
					$('.member #member11 #remark').val(d[0]['remark']);
					$('.member #member11 #firstdate').val(d[0]['firstdate']);
					member.tabs('enable','#member11');
					member.tabs('option','active',1);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.member #member1 #delete',function(){
		if($('.member #member1 #param #focus').val().length==0||$('.member #member1 .table #memberTable input[class="checkbox[]"]:checked').length==0){
			//console.log('empty');
		}
		else{
			$.ajax({
				url:'./lib/js/member.delete.php',
				method:'post',
				data:$('.member #member1 .table .memberTable').serialize(),
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('.member ul li:eq(0)').trigger('click');
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.member #member11 #save',function(){
		if($('.member #member11 #datatable #tel').val().length==0||$('.member #member11 #datatable #name').val().length==0||$('.member #member11 #datatable #power input[name="power"]').val()==''){
			$('.mys').html('電話1、姓名、會員等級為必填欄位。');
			mys.dialog('open');
		}
		else{
			$('.member #member11 #save').prop('disabled',true);
			$('.member #member11 #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/member.save.php',
				method:'post',
				data:$('.member #member11 #datatable .memberdata').serialize(),
				dataType:'html',
				success:function(d){
					if(d=='already'){
						alert('該電話已存在相同會員等級。');
					}
					else{
						//console.log(d);
						$('.member ul li:eq(0)').trigger('click');
					}
					$('.member #member11 #save').prop('disabled',false);
					$('.member #member11 #save').css({'opacity':'1','cursor':'pointer'});
					//console.log(d);
				},
				error:function(e){
					console.log(e);
					$('.member #member11 #save').prop('disabled',false);
					$('.member #member11 #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','.member #member11 #cancel',function(){
		member.tabs('option','disabled',[1,3]);
		member.tabs('option','active',0);
	});
	$(document).on('click','.member #member2 .table #powerTable .row',function(){
		var index=$('.member #member2 .table #powerTable .row').index(this);
		$('.member #member2 .table #powerTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.member #member2 .table #powerTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.member #member2 .table #powerTable .row:eq('+index+')').css({'background-color':'#bccad9'});
		if($('.member #member2 .table #powerTable .row:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.member #member2 .table #powerTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.member #member2 .table #powerTable .row:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.member #member2 .table #powerTable .row:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.member #member2 .table #powerTable .row:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		if(index==0){
			$('.member #member2 #param #prev').val('');
		}
		else{
			$('.member #member2 #param #prev').val($('.member #member2 .table #powerTable .row:eq('+(index-1)+') input[type="checkbox"]').val());
		}
		$('.member #member2 #param #focus').val($('.member #member2 .table #powerTable .row:eq('+index+') input[type="checkbox"]').val());
		if(index==$('.member #member2 .table #powerTable .row').length){
			$('.member #member2 #param #next').val('');
		}
		else{
			$('.member #member2 #param #next').val($('.member #member2 .table #powerTable .row:eq('+(index+1)+') input[type="checkbox"]').val());
		}
	});
	$(document).on('click','.member ul li:eq(2)',function(){
		member.tabs('option','disabled',[1,3]);
		member.tabs('option','active',2);
		$('.member #member1 .table #memberTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.member #member1 .table #memberTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.member #member1 .table #memberTable .row input[type=\"checkbox\"]').prop('checked',false);
		$('.member #member1 .table #memberTable .row #chimg').attr('src','./img/noch.png');
		/*$('.member #member2 .table #powerTable .row:nth-child(even)').css({'background-color':'#ffffff'});
		$('.member #member2 .table #powerTable .row:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.member #member2 .table #powerTable .row input[type=\"checkbox\"]').prop('checked',false);
		$('.member #member2 .table #powerTable .row #chimg').attr('src','./img/noch.png');*/
		$.ajax({
			url:'./lib/js/getlevellist.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'json',
			success:function(d){
				$('.member #member2 .table #powerTable tbody').html('');
				$.each(d,function(i,v){
					var st="<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='pg[]' style='display:none;' value='"+v['pno']+"'></td><td>"+v['seq']+"</td><td>"+v['name']+"</td><td>";
					if(v['state']=='1'){
						st=st+'啟用'
					}
					else{
						st=st+'<font color="#ff0000">停用</font>'
					}
					st=st+"</td></tr>";

					$('.member #member2 .table #powerTable tbody').append(st);
				});
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.member #member21 #datatable .fun label',function(){
		var index=$('.member #member21 #datatable .fun label').index(this);
		var temp=$('.member #member21 #datatable .fun label:eq('+index+') input').prop('class').split('-');
		var name=$('.member #member21 #datatable .fun label:eq('+index+') input').prop('name').substr(0,$('.member #member21 #datatable .fun label:eq('+index+') input').prop('name').length-2);
		if(temp.length==2){
			if($('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked')){
				$('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked',false);
				$('.member #member21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/noch.png');

				$('.member #member21 #datatable .fun div.'+name+' input[class="'+temp[0]+'"]').prop('checked',false);
				$('.member #member21 #datatable .fun div.'+name+' input[class="'+temp[0]+'"]').parent().find('#chimg').prop('src','./img/noch.png');

				$('.member #member21 #datatable .fun input[name="'+name+'[]"]:eq(0)').prop('checked',false);
				$('.member #member21 #datatable .fun input[name="'+name+'[]"]:eq(0)').parent().find('#chimg').prop('src','./img/noch.png');
			}
			else{
				$('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked',true);
				$('.member #member21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/onch.png');
				
				if($('.member #member21 #datatable .fun div.'+name+' div.'+temp[0]+' input').length==$('.member #member21 #datatable .fun div.'+name+' div.'+temp[0]+' input:checked').length){
					$('.member #member21 #datatable .fun div.'+name+' input[class="'+temp[0]+'"]').prop('checked',true);
					$('.member #member21 #datatable .fun div.'+name+' input[class="'+temp[0]+'"]').parent().find('#chimg').prop('src','./img/onch.png');

				}
				else{
				}
				
				if($('.member #member21 #datatable .fun div.'+name+' input').length==$('.member #member21 #datatable .fun div.'+name+' input:checked').length){
					$('.member #member21 #datatable .fun input[name="'+name+'[]"]:eq(0)').prop('checked',true);
					$('.member #member21 #datatable .fun input[name="'+name+'[]"]:eq(0)').parent().find('#chimg').prop('src','./img/onch.png');
				}
				else{
				}
			}
		}
		else{
			if(temp[0]!='0'){
				if($('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked')){
					$('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked',false);
					$('.member #member21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/noch.png');

					$('.member #member21 #datatable .fun div.'+name+' div.'+temp[0]+' input').prop('checked',false);
					$('.member #member21 #datatable .fun div.'+name+' div.'+temp[0]+' input').parent().find('#chimg').prop('src','./img/noch.png');

					$('.member #member21 #datatable .fun input[name="'+name+'[]"]:eq(0)').prop('checked',false);
					$('.member #member21 #datatable .fun input[name="'+name+'[]"]:eq(0)').parent().find('#chimg').prop('src','./img/noch.png');
				}
				else{
					$('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked',true);
					$('.member #member21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/onch.png');

					$('.member #member21 #datatable .fun div.'+name+' div.'+temp[0]+' input').prop('checked',true);
					$('.member #member21 #datatable .fun div.'+name+' div.'+temp[0]+' input').parent().find('#chimg').prop('src','./img/onch.png');
					
					if($('.member #member21 #datatable .fun div.'+name+' input').length==$('.member #member21 #datatable .fun div.'+name+' input:checked').length){
						$('.member #member21 #datatable .fun input[name="'+name+'[]"]:eq(0)').prop('checked',true);
						$('.member #member21 #datatable .fun input[name="'+name+'[]"]:eq(0)').parent().find('#chimg').prop('src','./img/onch.png');
					}
					else{
					}
				}
			}
			else{
				if($('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked')){
					$('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked',false);
					$('.member #member21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/noch.png');

					$('.member #member21 #datatable .fun div.'+name+' input').prop('checked',false);
					$('.member #member21 #datatable .fun div.'+name+' input').parent().find('#chimg').prop('src','./img/noch.png');
				}
				else{
					$('.member #member21 #datatable .fun label:eq('+index+') input').prop('checked',true);
					$('.member #member21 #datatable .fun label:eq('+index+') input').parent().find('#chimg').prop('src','./img/onch.png');
					
					$('.member #member21 #datatable .fun div.'+name+' input').prop('checked',true);
					$('.member #member21 #datatable .fun div.'+name+' input').parent().find('#chimg').prop('src','./img/onch.png');
				}
			}
		}
	});
	$(document).on('click','.member #member2 #create',function(){
		member.tabs('enable','#member21');
		member.tabs('option','active',3);
		var htmlst='';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		$('.member #member21 h1 center').html(htmlst);
		/*清空員工表格*/
		$('.member #member21 #pno').val('');
		$('.member #member21 #seq').val($('.member #member21 #seq').prop('min'));
		$('.member #member21 #name').val('');
		$('.member #member21 #stop').prop('checked',false);
		$('.member #member21 #discount').val('100');
		/**/
	});
	$(document).on('click','.member #member2 #edit',function(){
		if($('.member #member2 #param #focus').val().length==0){
		}
		else{
			$.ajax({
				url:'./lib/js/getleveldata.ajax.php',
				method:'post',
				data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'focus':$('.member #member2 #param #focus').val(),'prev':$('.member #member2 #param #prev').val(),'next':$('.member #member2 #param #next').val()},
				dataType:'json',
				success:function(d){
					var htmlst='';
					$.ajax({
						url:'./lib/js/getininame.ajax.php',
						method:'post',
						async:false,
						data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
						dataType:'html',
						success:function(d){
							htmlst=htmlst+d;
						},
						error:function(e){
							console.log(e);
						}
					});
					$('.member #member21 h1 center').html(htmlst);
					$('.member #member21 #pno').val(d[0]['pno']);
					$('.member #member21 #seq').val(d[0]['seq']);
					$('.member #member21 #name').val(d[0]['name']);
					if(d[0]['state']==1){
						$('.member #member21 #stop').prop('checked',false);
					}
					else{
						$('.member #member21 #stop').prop('checked',true);
					}
					$('.member #member21 #discount').val(d[0]['discount']);
					member.tabs('enable','#member21');
					member.tabs('option','active',3);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.member #member2 #delete',function(){
		if($('.member #member2 #param #focus').val().length==0||$('.member #member2 .table #powerTable input[class="checkbox[]"]:checked').length==0){
			//console.log('empty');
		}
		else{
			$.ajax({
				url:'./lib/js/level.delete.php',
				method:'post',
				data:$('.member #member2 .table .powerTable').serialize(),
				dataType:'html',
				success:function(d){
					$('.member ul li:eq(2)').trigger('click');
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.member #member21 #save',function(){
		if(parseInt($('.member #member21 #datatable #seq').val())<parseInt($('.member #member21 #datatable #seq').prop('min'))||$('.member #member21 #datatable #name').val().length==0||(parseInt($('.member #member21 #datatable #discount').val())>100||parseInt($('.member #member21 #datatable #discount').val())<0)){
			$('.mys').html('等級權重不合法、等級名稱不得為空與折扣不合法(0~100之間)。');
			mys.dialog('open');
		}
		else{
			$('.member #member21 #save').prop('disabled',true);
			$('.member #member21 #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/level.save.php',
				method:'post',
				data:$('.member #member21 #datatable .powergroup').serialize(),
				dataType:'html',
				success:function(d){
					if(d=='already'){
					}
					else{
						$('.member ul li:eq(2)').trigger('click');
					}
					$('.member #member21 #save').prop('disabled',false);
					$('.member #member21 #save').css({'opacity':'1','cursor':'pointer'});
					//console.log(d);
				},
				error:function(e){
					console.log(e);
					$('.member #member21 #save').prop('disabled',false);
					$('.member #member21 #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','.member #member21 #cancel',function(){
		member.tabs('option','disabled',[1,3]);
		member.tabs('option','active',2);
	});
	$(document).on('click','.member #member3 #senddata #search',function(){
		$('.member #member3 .table').html('');
		var data=$('.member #member3 #senddata').serialize();
		data=data+'&company='+$('input[name="company"]').val()+'&dep='+$('input[name="db"]').val();
		$.ajax({
			url:'./lib/js/getmemsalelist.php',
			method:'post',
			async:false,
			data:data,
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.member #member3 .table').html(d);
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','#prlitagbox #prlitag .itemrow',function(){
		var index=$('#prlitagbox #prlitag .itemrow').index(this);
		prli.tabs('option','disabled',[]);
		$('#prlitagbox #prlitag .itemrow').css({'background-color':'#ffffff'});
		$('#prlitagbox #prlitag .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#prlitagbox #prlitag .itemrow #chimg').attr('src','./img/noch.png');
		$('#prlitagbox #prlitag .itemrow:eq('+index+')').css({'background-color':'#E9E9E9'});
		$('#prlitagbox #prlitag .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
		$('#prlitagbox #prlitag .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		if($('#prlitagbox #prlitag input[name="prlicheckbox[]"]:checked').length==0){
		}
		else{
			$.ajax({
				url:'./lib/js/getprlidata.ajax.php',
				method:'post',
				data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="dep"]').val(),'prlino':$('#prlitagbox #prlitag input[name="prlicheckbox[]"]:checked').val()},
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('#prlitagbox #editprli').html(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','#prlitagbox #prlitag #edit',function(){
		if(prli.tabs('option','disabled')[0]==1){
		}
		else{
			prli.tabs('option','disabled',[]);
			prli.tabs('option','active',[1]);
		}
	});
	$(document).on('click','#prlitagbox #editprli #fun #cancel',function(){
		prli.tabs('option','disabled',[1]);
		prli.tabs('option','active',[0]);
		$('#prlitagbox #editprli').html('');
		$('#prlitagbox #prlitag .itemrow').css({'background-color':'#ffffff'});
		$('#prlitagbox #prlitag .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#prlitagbox #prlitag .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('change','#prlitagbox #editprli input',function(){
		$('#prlitagbox #editprli #save').prop('disabled',false);
	});
	$(document).on('click','#prlitagbox #editprli #save',function(){
		$('#prlitagbox #editprli #save').prop('disabled',true);
		$('#prlitagbox #editprli #save').css({'opacity':'0.5','cursor':'inherit'});
		$.ajax({
			url:'./lib/js/saveprli.ajax.php',
			method:'post',
			data:$('#prlitagbox #editprli #prliform').serialize(),
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#printlisttag').trigger('click');
				$('#prlitagbox #editprli #save').prop('disabled',false);
				$('#prlitagbox #editprli #save').css({'opacity':'1','cursor':'pointer'});
			},
			error:function(e){
				console.log(e);
				$('#prlitagbox #editprli #save').prop('disabled',false);
				$('#prlitagbox #editprli #save').css({'opacity':'1','cursor':'pointer'});
			}
		});
	});
	/*$(document).on('click','#prlitagbox #prlitag #delete',function(){
		if($('#prlitagbox #prlitag input[type="checkbox"]:checked').length){
			t=confirm('是否確認刪除產品？');
			if(t==true){
				$.ajax({
					url:'./lib/js/prlitag.delete.php',
					method:'post',
					data:$('#prlitagbox #prlitag #prliform').serialize(),
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('#printlisttag').trigger('click');
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
		}
	});*/
	$(document).on('click','#unit #allunits .itemrow.click',function(){
		var index=$('#unit #allunits .itemrow').index(this);
		if($('#unit #allunits .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked')){
			$('#unit #allunits .itemrow:eq('+index+')').css({'background-color':'#ffffff'});
			$('#unit #allunits .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#unit #allunits .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#unit #allunits .itemrow:eq('+index+')').css({'background-color':'#E9E9E9'});
			$('#unit #allunits .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#unit #allunits .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#kds #partitions .itemrow.click',function(){
		var index=$('#kds #partitions .itemrow').index(this);
		if($('#kds #partitions .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked')){
			$('#kds #partitions .itemrow:eq('+index+')').css({'background-color':'#ffffff'});
			$('#kds #partitions .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#kds #partitions .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#kds #partitions .itemrow:eq('+index+')').css({'background-color':'#E9E9E9'});
			$('#kds #partitions .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#kds #partitions .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#kds #groupofpts .itemrow.click',function(){
		var index=$('#kds #groupofpts .itemrow').index(this);
		if($('#kds #groupofpts .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked')){
			$('#kds #groupofpts .itemrow:eq('+index+')').css({'background-color':'#ffffff'});
			$('#kds #groupofpts .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#kds #groupofpts .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#kds #groupofpts .itemrow:eq('+index+')').css({'background-color':'#E9E9E9'});
			$('#kds #groupofpts .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#kds #groupofpts .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#unit #allunits .fun #create',function(){
		$('#unit #allunits .itemrow').css({'background-color':'#ffffff'});
		$('#unit #allunits .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#unit #allunits .itemrow #chimg').attr('src','./img/noch.png');
		$('#unit #allunits .itemrow').removeClass('click');
		var htmlst='<input id="save" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="crecancel" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#unit #allunits .fun').html(htmlst);
		htmlst='<tr style="background-color:#E9E9E9;"><td><input type="checkbox" style="display:none;" name="createnew" checked><img id="chimg" src="./img/onch.png"></td><td>';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'</td><td><input type="text" name="createname"></td></tr>';
		$('#unit #allunits .unitform table').append(htmlst);

	});
	$(document).on('click','#kds #partitions .fun #create',function(){
		$('#kds #partitions .itemrow').css({'background-color':'#ffffff'});
		$('#kds #partitions .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#kds #partitions .itemrow #chimg').attr('src','./img/noch.png');
		$('#kds #partitions .itemrow').removeClass('click');
		var htmlst='<input id="save" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="crecancel" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#kds #partitions .fun').html(htmlst);
		htmlst='<tr style="background-color:#E9E9E9;"><td><input type="checkbox" style="display:none;" name="createnew" checked><img id="chimg" src="./img/onch.png"></td><td>';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'</td><td><input type="text" name="createname"></td></tr>';
		$('#kds #partitions .partitionform table').append(htmlst);

	});
	$(document).on('click','#kds #groupofpts .fun #create',function(){
		$('#kds #groupofpts .itemrow').css({'background-color':'#ffffff'});
		$('#kds #groupofpts .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#kds #groupofpts .itemrow #chimg').attr('src','./img/noch.png');
		$('#kds #groupofpts .itemrow').removeClass('click');
		var htmlst='<input id="save" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="crecancel" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#kds #groupofpts .fun').html(htmlst);
		htmlst='<tr style="background-color:#E9E9E9;"><td><input type="checkbox" style="display:none;" name="createnew" checked><img id="chimg" src="./img/onch.png"></td><td>';
		$.ajax({
			url:'./lib/js/getpartitionlist.ajax.php',
			method:'post',
			async:false,
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'</td><td><input type="text" name="createname"></td><td><input type="number" name="createlimit"></td></tr>';
		$('#kds #groupofpts .groupform table').append(htmlst);

	});
	$(document).on('click','#unit #allunits .fun #edit',function(){
		if($('#unit #allunits .itemrow input[type="checkbox"]:checked').length>0){
			$('#unit #allunits .itemrow').removeClass('click');
			var htmlst='<input id="save" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'"><input id="cancel" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'">';
			$('#unit #allunits .fun').html(htmlst);
			htmlst='';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'newunittitle'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			$('#unit #allunits .unitform .newunit').html(htmlst);
			for(var i=0;i<$('#unit #allunits .itemrow').length;i++){
				if($('#unit #allunits .itemrow:eq('+i+') input[type="checkbox"]').prop('checked')){
					$('#unit #allunits .itemrow:eq('+i+') #newunit').html('<input type="text" name="newunit[]">');
				}
				else{
				}
			}
		}
		else{
		}
	});
	$(document).on('click','#kds #partitions .fun #edit',function(){
		if($('#kds #partitions .itemrow input[type="checkbox"]:checked').length>0){
			$('#kds #partitions .itemrow').removeClass('click');
			var htmlst='<input id="save" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'"><input id="cancel" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'">';
			$('#kds #partitions .fun').html(htmlst);
			htmlst='';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'newname'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			$('#kds #partitions .partitionform .newpartition').html(htmlst);
			for(var i=0;i<$('#kds #partitions .itemrow').length;i++){
				if($('#kds #partitions .itemrow:eq('+i+') input[type="checkbox"]').prop('checked')){
					$('#kds #partitions .itemrow:eq('+i+') #newpartition').html('<input type="text" name="newpartition[]">');
				}
				else{
				}
			}
		}
		else{
		}
	});
	$(document).on('click','#kds #groupofpts .fun #edit',function(){
		if($('#kds #groupofpts .itemrow input[type="checkbox"]:checked').length>0){
			$('#kds #groupofpts .itemrow').removeClass('click');
			var htmlst='<input id="save" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'"><input id="cancel" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'">';
			$('#kds #groupofpts .fun').html(htmlst);
			htmlst='';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'newname'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			$('#kds #groupofpts .groupform .newgroup').html(htmlst);
			htmlst='';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'newlimit'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			$('#kds #groupofpts .groupform .newlimit').html(htmlst);
			for(var i=0;i<$('#kds #groupofpts .itemrow').length;i++){
				if($('#kds #groupofpts .itemrow:eq('+i+') input[type="checkbox"]').prop('checked')){
					$.ajax({
						url:'./lib/js/getgroupname.ajax.php',
						method:'post',
						async:false,
						data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'oldpt':$('#kds #groupofpts .itemrow:eq('+i+') input[name="no[]"]').val()},
						dataType:'html',
						success:function(d){
							$('#kds #groupofpts .itemrow:eq('+i+') #newgroup').html('<input type="text" name="newgroup[]" value="'+d+'">');
						},
						error:function(e){
							console.log(e);
						}
					});
					$.ajax({
						url:'./lib/js/getgrouplimit.ajax.php',
						method:'post',
						async:false,
						data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'oldpt':$('#kds #groupofpts .itemrow:eq('+i+') input[name="no[]"]').val()},
						dataType:'html',
						success:function(d){
							$('#kds #groupofpts .itemrow:eq('+i+') #newlimit').html('<input type="number" name="newlimit[]" style="text-align:right;" value="'+d+'">');
						},
						error:function(e){
							console.log(e);
						}
					});
				}
				else{
				}
			}
		}
		else{
		}
	});
	$(document).on('click','#unit #allunits .fun #save',function(){
		$('#unit #allunits .fun #save').prop('disabled',true);
		$('#unit #allunits .fun #save').css({'opacity':'0.5','cursor':'inherit'});
		$.ajax({
			url:'./lib/js/saveunit.ajax.php',
			method:'post',
			data:$('#unit #allunits .unitform').serialize(),
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#allunit').trigger('click');
				$('#unit #allunits .fun #save').prop('disabled',false);
				$('#unit #allunits .fun #save').css({'opacity':'1','cursor':'pointer'});
			},
			error:function(e){
				console.log(e);
				$('#unit #allunits .fun #save').prop('disabled',false);
				$('#unit #allunits .fun #save').css({'opacity':'1','cursor':'pointer'});
			}
		});
	});
	$(document).on('click','#kds #partitions .fun #save',function(){
		$('#kds #partitions .fun #save').prop('disabled',true);
		$('#kds #partitions .fun #save').css({'opacity':'0.5','cursor':'inherit'});
		$.ajax({
			url:'./lib/js/savekds.partition.php',
			method:'post',
			data:$('#kds #partitions .partitionform').serialize(),
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#setkds').trigger('click');
				$('#kds #partitions .fun #save').prop('disabled',false);
				$('#kds #partitions .fun #save').css({'opacity':'1','cursor':'pointer'});
			},
			error:function(e){
				console.log(e);
				$('#kds #partitions .fun #save').prop('disabled',false);
				$('#kds #partitions .fun #save').css({'opacity':'1','cursor':'pointer'});
			}
		});
	});
	$(document).on('click','#kds #groupofpts .fun #save',function(){
		$('#kds #groupofpts .fun #save').prop('disabled',true);
		$('#kds #groupofpts .fun #save').css({'opacity':'0.5','cursor':'inherit'});
		$.ajax({
			url:'./lib/js/savekds.group.php',
			method:'post',
			data:$('#kds #groupofpts .groupform').serialize(),
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#kds .allgroupofpts').trigger('click');
				$('#kds #groupofpts .fun #save').prop('disabled',false);
				$('#kds #groupofpts .fun #save').css({'opacity':'1','cursor':'pointer'});
			},
			error:function(e){
				console.log(e);
				$('#kds #groupofpts .fun #save').prop('disabled',false);
				$('#kds #groupofpts .fun #save').css({'opacity':'1','cursor':'pointer'});
			}
		});
	});
	$(document).on('click','#unit #allunits .fun #cancel',function(){
		var htmlst='<input id="create" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#unit #allunits .fun').html(htmlst);
		$('#unit #allunits .unitform .newunit').html('');
		$('#unit #allunits .itemrow').addClass('click');
		$('#unit #allunits #newunit').html('');
		$('#unit #allunits .itemrow').css({'background-color':'#ffffff'});
		$('#unit #allunits .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#unit #allunits .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('click','#kds #partitions .fun #cancel',function(){
		var htmlst='<input id="create" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#kds #partitions .fun').html(htmlst);
		$('#kds #partitions .partitionform .newpartition').html('');
		$('#kds #partitions .itemrow').addClass('click');
		$('#kds #partitions #newpartition').html('');
		$('#kds #partitions .itemrow').css({'background-color':'#ffffff'});
		$('#kds #partitions .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#kds #partitions .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('click','#kds #groupofpts .fun #cancel',function(){
		var htmlst='<input id="create" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#kds #groupofpts .fun').html(htmlst);
		$('#kds #groupofpts .groupform .newgroup').html('');
		$('#kds #groupofpts .groupform .newlimit').html('');
		$('#kds #groupofpts .itemrow').addClass('click');
		$('#kds #groupofpts #newgroup').html('');
		$('#kds #groupofpts #newlimit').html('');
		$('#kds #groupofpts .itemrow').css({'background-color':'#ffffff'});
		$('#kds #groupofpts .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#kds #groupofpts .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('click','#unit #allunits .fun #crecancel',function(){
		var htmlst='<input id="create" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#unit #allunits .fun').html(htmlst);
		$('#unit #allunits .unitform .newunit').html('');
		$('#unit #allunits .itemrow').addClass('click');
		$('#unit #allunits .unitform table tr:eq('+(parseInt($('#unit #allunits .unitform table tr').length)-1)+')').remove();
	});
	$(document).on('click','#kds #partitions .fun #crecancel',function(){
		var htmlst='<input id="create" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#kds #partitions .fun').html(htmlst);
		$('#kds #partitions .partitionform .newpartition').html('');
		$('#kds #partitions .itemrow').addClass('click');
		$('#kds #partitions .partitionform table tr:eq('+(parseInt($('#kds #partitions .partitionform table tr').length)-1)+')').remove();
	});
	$(document).on('click','#kds #groupofpts .fun #crecancel',function(){
		var htmlst='<input id="create" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#kds #groupofpts .fun').html(htmlst);
		$('#kds #groupofpts .groupform .newgroup').html('');
		$('#kds #groupofpts .groupform .newlimit').html('');
		$('#kds #groupofpts .itemrow').addClass('click');
		$('#kds #groupofpts .groupform table tr:eq('+(parseInt($('#kds #partitions .groupform table tr').length)-1)+')').remove();
	});
	$(document).on('click','#kds .allgroupofpts',function(){
		$.ajax({
			url:'./lib/js/reloadkds.group.php',
			method:'post',
			async:false,
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'lan':$('.lan').val()},
			dataType:'html',
			success:function(d){
				$('#kds #groupofpts').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','#unit #allstraws .itemrow.click',function(){
		var index=$('#unit #allstraws .itemrow').index(this);
		if($('#unit #allstraws .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked')){
			$('#unit #allstraws .itemrow:eq('+index+')').css({'background-color':'#ffffff'});
			$('#unit #allstraws .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('#unit #allstraws .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('#unit #allstraws .itemrow:eq('+index+')').css({'background-color':'#E9E9E9'});
			$('#unit #allstraws .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('#unit #allstraws .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','#unit #allstraws .fun #edit',function(){
		if($('#unit #allstraws .itemrow input[type="checkbox"]:checked').length>0){
			$('#unit #allstraws .itemrow').removeClass('click');
			var htmlst='<input id="save" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'"><input id="cancel" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'">';
			$('#unit #allstraws .fun').html(htmlst);
			htmlst='';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'newstrawnametitle'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			$('#unit #allstraws .strawform .newstraw').html(htmlst);
			for(var i=0;i<$('#unit #allstraws .itemrow').length;i++){
				if($('#unit #allstraws .itemrow:eq('+i+') input[type="checkbox"]').prop('checked')){
					$('#unit #allstraws .itemrow:eq('+i+') #newstraw').html('<input type="text" name="newstraw[]">');
				}
				else{
				}
			}
		}
		else{
		}
	});
	$(document).on('click','#unit #allstraws .fun #save',function(){
		$('#unit #allstraws .fun #save').prop('disabled',true);
		$('#unit #allstraws .fun #save').css({'opacity':'0.5','cursor':'inherit'});
		$.ajax({
			url:'./lib/js/savestraw.ajax.php',
			method:'post',
			data:$('#unit #allstraws .strawform').serialize(),
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#allunit').trigger('click');
				$('#unit #allstraws .fun #save').prop('disabled',false);
				$('#unit #allstraws .fun #save').css({'opacity':'1','cursor':'pointer'});
			},
			error:function(e){
				console.log(e);
				$('#unit #allstraws .fun #save').prop('disabled',false);
				$('#unit #allstraws .fun #save').css({'opacity':'1','cursor':'pointer'});
			}
		});
	});
	$(document).on('click','#unit #allstraws .fun #cancel',function(){
		var htmlst='<input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('#unit #allstraws .fun').html(htmlst);
		$('#unit #allstraws .strawform .newstraw').html('');
		$('#unit #allstraws .itemrow').addClass('click');
		$('#unit #allstraws #newstraw').html('');
		$('#unit #allstraws .itemrow').css({'background-color':'#ffffff'});
		$('#unit #allstraws .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('#unit #allstraws .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('click','.manufact #manufact1 #manufactTable .itemrow',function(){
		var index=$('.manufact #manufact1 #manufactTable .itemrow').index(this);
		manufact.tabs('option','disabled',[2]);
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact1 #manufactTable .itemrow:eq('+index+')').css({'background-color':'#bccad9'});
		if($('.manufact #manufact1 #manufactTable .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked')){
			$('.manufact #manufact1 #manufactTable .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.manufact #manufact1 #manufactTable .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.manufact #manufact1 #manufactTable .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.manufact #manufact1 #manufactTable .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		$.ajax({
			url:'./lib/js/getmanuata.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'no':$('.manufact #manufact1 #manufactTable .itemrow:eq('+index+') input[name="no[]"]').val()},
			dataType:'json',
			success:function(d){
				//console.log(d);
				/*清空廠商表格*/
				$('.manufact #manufact11 #type').val('edit');
				$('.manufact #manufact11 #no').val(d[0]['no']);
				$('.manufact #manufact11 #manuno').val(d[0]['manuno']);
				$('.manufact #manufact11 #manuname').val(d[0]['manuname']);
				$('.manufact #manufact11 #mainperson').val(d[0]['mainperson']);
				$('.manufact #manufact11 #conperson').val(d[0]['conperson']);
				$('.manufact #manufact11 #tel').val(d[0]['tel']);
				$('.manufact #manufact11 #tel2').val(d[0]['tel2']);
				$('.manufact #manufact11 #fax').val(d[0]['fax']);
				$('.manufact #manufact11 #email').val(d[0]['email']);
				$('.manufact #manufact11 #banno').val(d[0]['banno']);
				$('.manufact #manufact11 #zip1').val(d[0]['zip1']);
				$('.manufact #manufact11 #sendaddress').val(d[0]['sendaddress']);
				$('.manufact #manufact11 #zip2').val(d[0]['zip2']);
				$('.manufact #manufact11 #billaddress').val(d[0]['billaddress']);
				$('.manufact #manufact11 #remark').val(d[0]['remark']);
				/**/
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.manufact #manufact1 #edit',function(){
		if(manufact.tabs('option','disabled')[0]==4){
		}
		else{
			manufact.tabs('option','disabled',[2]);
			manufact.tabs('option','active',[4]);
		}
	});
	$(document).on('click','.manufact #manufact1 #create',function(){
		manufact.tabs('enable','#manufact11');
		manufact.tabs('option','active',4);
		$('.manufact #manufact11 h1 center').html('新增廠商');
		/*清空廠商表格*/
		$('.manufact #manufact11 #type').val('new');
		$('.manufact #manufact11 #no').val('');
		$('.manufact #manufact11 #manuno').val('');
		$('.manufact #manufact11 #manuname').val('');
		$('.manufact #manufact11 #mainperson').val('');
		$('.manufact #manufact11 #conperson').val('');
		$('.manufact #manufact11 #tel').val('');
		$('.manufact #manufact11 #tel2').val('');
		$('.manufact #manufact11 #fax').val('');
		$('.manufact #manufact11 #email').val('');
		$('.manufact #manufact11 #banno').val('');
		$('.manufact #manufact11 #sendaddress').val('');
		$('.manufact #manufact11 #billaddress').val('');
		$('.manufact #manufact11 #remark').val('');
		/**/
	});
	$(document).on('click','.manufact #manufact11 #save',function(){
		if($('.manufact #manufact11 #manuno').val()==''||$('.manufact #manufact11 #manuname').val()==''||($('.manufact #manufact11 #tel').val()==''&&$('.manufact #manufact11 #tel2').val()=='')){
			alert('編號、廠商名稱不得為空。\n市話與手機至少須輸入一項。');
		}
		else{
			$('.manufact #manufact11 #save').prop('disabled',true);
			$('.manufact #manufact11 #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/savemanufact.ajax.php',
				method:'post',
				data:$('.manufact #manufact11 .manufactdata').serialize(),
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('#allmanufact').trigger('click');
					$('.manufact #manufact11 #save').prop('disabled',false);
					$('.manufact #manufact11 #save').css({'opacity':'1','cursor':'pointer'});
				},
				error:function(e){
					console.log(e);
					$('.manufact #manufact11 #save').prop('disabled',false);
					$('.manufact #manufact11 #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','.manufact #manufact11 #cancel',function(){
		manufact.tabs('option','active',3);
		manufact.tabs('option','disabled',[2,4]);
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact1 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact1 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact2 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact2 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('click','.manufact #manufact1 #delete',function(){
		if($('.manufact #manufact1 #manufactTable .itemrow input[type="checkbox"]:checked').length>0){
			t=confirm('是否確認刪除廠商？');
			if(t==true){
				$.ajax({
					url:'./lib/js/deletemanu.ajax.php',
					method:'post',
					data:$('.manufact #manufact1 .manufactTable').serialize(),
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('#allmanufact').trigger('click');
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
		}
	});
	$(document).on('click','.manufact #manufact2 #manufactTable .itemrow',function(){
		$('.manufact #manufact21 h1 center').html('修改進貨單');
		var index=$('.manufact #manufact2 #manufactTable .itemrow').index(this);
		manufact.tabs('option','disabled',[4]);
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact2 #manufactTable .itemrow:eq('+index+')').css({'background-color':'#bccad9'});
		if($('.manufact #manufact2 #manufactTable .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked')){
			$('.manufact #manufact2 #manufactTable .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.manufact #manufact2 #manufactTable .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.manufact #manufact2 #manufactTable .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.manufact #manufact2 #manufactTable .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		$.ajax({
			url:'./lib/js/getpushdata.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'no':$('.manufact #manufact2 #manufactTable .itemrow:eq('+index+') input[name="no[]"]').val()},
			dataType:'json',
			success:function(d){
				//console.log(d);
				/*清空廠商表格*/
				$('.manufact #manufact21 #type').val('edit');
				$('.manufact #manufact21 #no').val(d[0]['no']);
				$('.manufact #manufact21 #listno').val(d[0]['listno']);
				$('.manufact #manufact21 #listno').prop('readonly',true);
				$.ajax({
					url:'./lib/js/getmanulist.select.php',
					method:'post',
					data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'manuno':d[0]['manuno']},
					dataType:'html',
					success:function(d){
						$('.manufact #manufact21 .manufactdata #manuselect').html(d);
					},
					error:function(e){
						console.log(e);
					}
				});
				$('.manufact #manufact21 #items tbody').html('');
				for(var i=0;i<d.length;i++){
					//console.log(d[i]['itemno']);
					/*if(i==d.length){
						$.ajax({
							url:'./lib/js/getitemlist.select.php',
							method:'post',
							data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
							dataType:'html',
							success:function(d){
								//console.log(d);
								$('.manufact #manufact21 #datatable #items tbody').append(d);
							},
							error:function(e){
								console.log(e);
							}
						});
					}*/
					//else{
						$.ajax({
							url:'./lib/js/getitemlist.select.php',
							method:'post',
							data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'itemno':d[i]['itemno'],'qty':d[i]['qty'],'unit':d[i]['unit'],'subtotal':d[i]['subtotal']},
							dataType:'html',
							success:function(d){
								//console.log(d);
								$('.manufact #manufact21 #datatable #items tbody').append(d);
							},
							error:function(e){
								console.log(e);
							}
						});
					//}
				}
				//$('.manufact #manufact21 #manufact').val('');
				//$('.manufact #manufact21 #items').val('');
				$('.manufact #manufact21 #invnumber').val(d[0]['invnumber']);
				$('.manufact #manufact21 #ttmoney').val(d[0]['ttmoney']);
				if(d[0]['paystate']=='1'){
					$('.manufact #manufact21 input[name="paystate"]:eq(0)').prop('checked',false);
					$('.manufact #manufact21 input[name="paystate"]:eq(1)').prop('checked',true);
					$('.manufact #manufact21 #paydate').prop('disabled',false);
				}
				else{
					$('.manufact #manufact21 input[name="paystate"]:eq(0)').prop('checked',true);
					$('.manufact #manufact21 input[name="paystate"]:eq(1)').prop('checked',false);
					$('.manufact #manufact21 #paydate').prop('disabled',true);
				}
				$('.manufact #manufact21 #paydate').val(d[0]['paydate']);
				$('.manufact #manufact21 #remark').val(d[0]['remark']);
				/**/
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.manufact #manufact2 #create',function(){
		manufact.tabs('enable','#manufact21');
		manufact.tabs('option','active',2);
		$('.manufact #manufact21 h1 center').html('新增進貨單');
		/*清空廠商表格*/
		$('.manufact #manufact21 #type').val('new');
		$('.manufact #manufact21 #no').val('');
		$.ajax({
			url:'./lib/js/getnewlistno.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.manufact #manufact21 #listno').val(d);
				$('.manufact #manufact21 #listno').prop('readonly',false);
			},
			error:function(e){
				console.log(e);
				$('.manufact #manufact21 #listno').val('');
				$('.manufact #manufact21 #listno').prop('readonly',false);
			}
		});
		$.ajax({
			url:'./lib/js/getmanulist.select.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('.manufact #manufact21 .manufactdata #manuselect').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
		$('.manufact #manufact21 #datatable #items tbody').html('');
		$.ajax({
			url:'./lib/js/getitemlist.select.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.manufact #manufact21 #datatable #items tbody').append(d);
			},
			error:function(e){
				console.log(e);
			}
		});
		$('.manufact #manufact21 #invnumber').val('');
		$('.manufact #manufact21 #ttmoney').val('0');
		$('.manufact #manufact21 input[name="paystate"]:eq(0)').prop('checked',true);
		$('.manufact #manufact21 input[name="paystate"]:eq(1)').prop('checked',false);
		$('.manufact #manufact21 #paydate').val('');
		$('.manufact #manufact21 #paydate').prop('disabled',true);
		$('.manufact #manufact21 #remark').val('');
		/**/
	});
	$(document).on('click','.manufact #manufact2 #edit',function(){
		if(manufact.tabs('option','disabled')[1]==4){
		}
		else{
			manufact.tabs('option','disabled',[4]);
			manufact.tabs('option','active',[2]);
		}
	});
	/**/
	$(document).on('click',".manufact #manufact21 #manufactbox.select_box",function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
    $(document).on('click',document,function(event){
        var eo=$(event.target);
        if($(".manufact #manufact21 .select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('.manufact #manufact21 .option').hide();
    });
	/*赋值给文本框*/
    $(document).on('click',".manufact #manufact21 #manufactbox .option a",function(){
		var index=$('.manufact #manufact21 #manufactbox .option a').index(this);
		var value=$(".manufact #manufact21 #manufactbox .option a:eq("+index+")").text();
        $(".manufact #manufact21 #manufactbox .option a").parent().siblings(".select_txt").text(value);
        $(".manufact #manufact21 input[name='manufact']#select_value").val($('.manufact #manufact21 #manufactbox .option a:eq('+index+')').attr('id'));
    });
	//產品下拉選單
	var itemindex='';
	$(document).on('click',".manufact #manufact21 #pushitembox.select_box",function(event){
		itemindex=$(".manufact #manufact21 #pushitembox.select_box").index(this);
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
	//赋值给文本框
    $(document).on('click',".manufact #manufact21 #pushitembox .option a",function(){
		var index=$('.manufact #manufact21 #pushitembox:eq('+itemindex+') .option a').index(this);
		var value=$(".manufact #manufact21 #pushitembox:eq("+itemindex+") .option a:eq("+index+")").text();
        $(".manufact #manufact21 #pushitembox:eq("+itemindex+") .option a").parent().siblings(".select_txt").text(value);
        $(".manufact #manufact21 input[name='pushitem[]']#select_value:eq("+itemindex+")").val($('.manufact #manufact21 #pushitembox:eq('+itemindex+') .option a:eq('+index+')').attr('id'));
		$.ajax({
			url:'./lib/js/getunit.ajax.php',
			method:'post',
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'itemno':$('.manufact #manufact21 #pushitembox:eq('+itemindex+') .option a:eq('+index+')').attr('id')},
			dataType:'html',
			success:function(d){
				$('.manufact #manufact21 input[name="unit[]"]:eq('+itemindex+')').val(d);
			},
			error:function(e){
				console.log(e);
			}
		});
		if((parseInt(itemindex)+1)==$(".manufact #manufact21 #pushitembox.select_box").length){
			$.ajax({
				url:'./lib/js/getitemlist.select.php',
				method:'post',
				data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('.manufact #manufact21 #datatable #items tbody').append(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
		}
		$('.manufact #manufact21 .manufactdata input[name="subtotal[]"]').trigger('keyup');
    });
	$(document).on('keyup','.manufact #manufact21 .manufactdata input[name="subtotal[]"]',function(){
		var index=$('.manufact #manufact21 .manufactdata input[name="subtotal[]"]').length;
		var subsum=0;
		for(var i=0;i<index;i++){
			subsum=Number(subsum)+Number($('.manufact #manufact21 .manufactdata input[name="subtotal[]"]:eq('+i+')').val());
		}
		$('.manufact #manufact21 .manufactdata #ttmoney').val(subsum);
	});
	$(document).on('click','.manufact #manufact21 .manufactdata input[name="paystate"]',function(){
		if($('.manufact #manufact21 .manufactdata input[name="paystate"]:checked').val()=='1'){
			$('.manufact #manufact21 .manufactdata #paydate').prop('disabled',false);
		}
		else{
			$('.manufact #manufact21 .manufactdata #paydate').prop('disabled',true);
			$('.manufact #manufact21 .manufactdata #paydate').val('');
		}
	});
	$(document).on('click','.manufact #manufact21 #save',function(){
		if($('.manufact #manufact21 #type').val()=='edit'&&($('.manufact #manufact21 #listno').val()==''||$('.manufact #manufact21 input[name="manufact"]').val()==''||($('.manufact #manufact21 #pushitem').length<1))){
			alert('進貨單編號、廠商與進貨產品不得為空。');
		}
		else if($('.manufact #manufact21 #type').val()=='new'&&($('.manufact #manufact21 #listno').val()==''||$('.manufact #manufact21 input[name="manufact"]').val()==''||($('.manufact #manufact21 #pushitem').length==1))){
			alert('進貨單編號、廠商與進貨產品不得為空。');
		}
		else{
			$('.manufact #manufact21 #save').prop('disabled',true);
			$('.manufact #manufact21 #save').css({'opacity':'0.5','cursor':'inherit'});
			$.ajax({
				url:'./lib/js/savepushitem.ajax.php',
				method:'post',
				data:$('.manufact #manufact21 .manufactdata').serialize(),
				dataType:'html',
				success:function(d){
					console.log(d);
					if(d=='already'){
						alert('該進貨單編號已存在。');
					}
					else{
						//console.log(d);
						$('#allmanufact').trigger('click');
						$('.manufact ul #pushlist').trigger('click');
					}
					$('.manufact #manufact21 #save').prop('disabled',false);
					$('.manufact #manufact21 #save').css({'opacity':'1','cursor':'pointer'});
				},
				error:function(e){
					console.log(e);
					$('.manufact #manufact21 #save').prop('disabled',false);
					$('.manufact #manufact21 #save').css({'opacity':'1','cursor':'pointer'});
				}
			});
		}
	});
	$(document).on('click','.manufact #manufact21 #cancel',function(){
		manufact.tabs('option','active',1);
		manufact.tabs('option','disabled',[2,4]);
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact1 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact1 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact2 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact2 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('click','.manufact #manufact2 #delete',function(){
		if($('.manufact #manufact2 #manufactTable .itemrow input[type="checkbox"]:checked').length>0){
			t=confirm('是否確認刪除進貨單？');
			if(t==true){
				$.ajax({
					url:'./lib/js/deletepushlist.ajax.php',
					method:'post',
					data:$('.manufact #manufact2 .manufactTable').serialize(),
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('#allmanufact').trigger('click');
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
		}
	});
	$(document).on('click','.manufact #nowstock #reflash',function(){
		$.ajax({
			url:'./lib/js/reflashstock.ajax.php',
			method:'post',
			data:{'company':$('.manufact #nowstock input[name="company"]').val(),'dep':$('.manufact #nowstock input[name="dep"]').val(),'lastdate':$('.manufact #nowstock input[name="lastdate"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#allmanufact').trigger('click');
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.secview .secviewform #savesecview',function(){
		$.ajax({
			url:'./lib/js/savesecview.ajax.php',
			method:'post',
			async:false,
			enctype: 'multipart/form-data',
			data:new FormData($('.secview .secviewform')[0]),
			dataType:'html',
			processData: false,
			contentType: false,
			success:function(d){
				if(d=='img error'){
					alert('圖片格式錯誤，請使用jpg(jpeg)與png檔案。');
				}
				else{
					$('#secview').trigger('click');
				}
				//console.log(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.otherpay #create',function(){
		$.ajax({
			url:'./lib/js/getotherpaydata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.otherpay #setpaydata').html(d);
				otherpay.tabs('option','disabled',[]);
				otherpay.tabs('option','active','1');
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','.otherpay #edit',function(){
		if($('.otherpay #allpaydata .itemrow#focus input[name="no[]"]').length>0){
			otherpay.tabs('option','active','1');
		}
		else{
		}
	});
	$(document).on('click','.otherpay #delete',function(){
		if($('.otherpay #allpaydata .itemrow#focus input[name="no[]"]').length>0){
			var itemno='';
			for(var i=0;i<$('.otherpay #allpaydata .itemrow#focus input[name="no[]"]').length;i++){
				if(itemno!=''){
					itemno += ','+$('.otherpay #allpaydata .itemrow#focus input[name="no[]"]:eq('+i+')').val();
				}
				else{
					itemno += $('.otherpay #allpaydata .itemrow#focus input[name="no[]"]:eq('+i+')').val();
				}
			}
			$.ajax({
				url:'./lib/js/delete.otherpay.php',
				method:'post',
				async:false,
				data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'itemno':itemno},
				dataType:'html',
				success:function(d){
					console.log(d);
					$('.otherpay #allpay').trigger('click');
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
		}
	});
	$(document).on('click','.otherpay #allpaydata .itemrow',function(){
		var index=$('.otherpay #allpaydata .itemrow').index(this);
		$('.otherpay #allpaydata .itemrow').prop('id',index);
		$('.otherpay #allpaydata .itemrow:eq('+index+')').prop('id','focus');
		otherpay.tabs('option','disabled',[]);
		if($('.otherpay #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.otherpay #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.otherpay #allpaydata .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.otherpay #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.otherpay #allpaydata .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		//console.log($('.otherpay #allpaydata .itemrow#focus input[name="no[]"]').val());
		$.ajax({
			url:'./lib/js/getotherpaydata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':$('.otherpay #allpaydata .itemrow#focus input[name="no[]"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('.otherpay #setpaydata').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.otherpay #allpay',function(){
		$.ajax({
			url:'./lib/js/getpaylist.ajax.php',
			method:'post',
			async:false,
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.otherpay #allpaydata .otherpayTable').html(d);
				otherpay.tabs('option','disabled',[1]);
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','.otherpay #setpaydata #save',function(){
		var array1=$('.otherpay #setpaydata #itemform').serialize();
		$.ajax({
			url:'./lib/js/save.otherpay.php',
			method:'post',
			async:false,
			data:array1,
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.otherpay #allpay').trigger('click');
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','.otherpay #setpaydata #cancel',function(){
		$('.otherpay #allpay').trigger('click');
	});
	$(document).on('click','.autodis #create',function(){
		$.ajax({
			url:'./lib/js/getautodisdata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.autodis #setdisdata').html(d);
				autodis.tabs('option','disabled',[]);
				autodis.tabs('option','active','1');
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','.autodis #edit',function(){
		if($('.autodis #alldisdata .itemrow#focus input[name="no[]"]').length>0){
			autodis.tabs('option','active','1');
		}
		else{
		}
	});
	$(document).on('click','.autodis #alldisdata .itemrow',function(){
		var index=$('.autodis #alldisdata .itemrow').index(this);
		$('.autodis #alldisdata .itemrow').prop('id',index);
		$('.autodis #alldisdata .itemrow:eq('+index+')').prop('id','focus');
		autodis.tabs('option','disabled',[]);
		if($('.autodis #alldisdata .itemrow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.autodis #alldisdata .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.autodis #alldisdata .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.autodis #alldisdata .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.autodis #alldisdata .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		//console.log($('.autodis #alldisdata .itemrow#focus input[name="no[]"]').val());
		$.ajax({
			url:'./lib/js/getautodisdata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':$('.autodis #alldisdata .itemrow#focus input[name="no[]"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('.autodis #setdisdata').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.autodis #allautodis',function(){
		$.ajax({
			url:'./lib/js/getdislist.ajax.php',
			method:'post',
			async:false,
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.autodis #alldisdata .autodisTable').html(d);
				autodis.tabs('option','disabled',[1]);
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','.autodis #setdisdata #save',function(){
		if($('.autodis #setdisdata #itemform input[name="name"]').val().trim()==''){
		}
		else{
			var array1=$('.autodis #setdisdata #itemform').serialize();
			$.ajax({
				url:'./lib/js/save.autodis.php',
				method:'post',
				async:false,
				data:array1,
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('.autodis #allautodis').trigger('click');
				},
				error:function(e){
					//console.log(e);
				}
			});
		}
	});
	$(document).on('click','.autodis #setdisdata #cancel',function(){
		$('.autodis #allautodis').trigger('click');
	});
	$(document).on('click','.inifile #initsetting',function(){
		$.ajax({
			url:'./lib/js/get.initsetting.php',
			method:'post',
			async:false,
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'ini':$('.inifileswitch').length},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.inifile #setinitsetting').html(d);
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','.inifile #setinitsetting #save',function(){
		var inputdata={'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()};
		var temp=$('.inifile #setinitsetting .sectionstring').val().split('-');
		//console.log('start');
		for(var i=0;i<temp.length;i++){
			//console.log(temp[i]);
			//console.log($('.inifile #setinitsetting .'+temp[i]).serialize());
			//console.log(i);
			inputdata[temp[i]]=$('.inifile #setinitsetting .'+temp[i]).serialize();
		}
		//console.log(inputdata);
		$.ajax({
			url:'./lib/js/save.initsetting.php',
			method:'post',
			async:false,
			data:inputdata,
			dataType:'html',
			success:function(d){
				console.log(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.inifile #printlisttag',function(){
		$.ajax({
			url:'./lib/js/get.printlisttag.php',
			method:'post',
			async:false,
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'ini':$('.inifileswitch').length},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.inifile #setprintlisttag').html(d);
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','.inifile #setprintlisttag #save',function(){
		var inputdata={'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()};
		var temp=$('.inifile #setprintlisttag .sectionstring').val().split('-');
		//console.log('start');
		for(var i=0;i<temp.length;i++){
			//console.log(temp[i]);
			//console.log($('.inifile #setprintlisttag .'+temp[i]).serialize());
			//console.log(i);
			inputdata[temp[i]]=$('.inifile #setprintlisttag .'+temp[i]).serialize();
		}
		//console.log(inputdata);
		$.ajax({
			url:'./lib/js/save.printlisttag.php',
			method:'post',
			async:false,
			data:inputdata,
			dataType:'html',
			success:function(d){
				console.log(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.inifile #setup',function(){
		$.ajax({
			url:'./lib/js/get.setup.php',
			method:'post',
			async:false,
			data:{'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val(),'ini':$('.inifileswitch').length},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.inifile #setsetup').html(d);
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
	$(document).on('click','.inifile #setsetup #save',function(){
		var inputdata={'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()};
		var temp=$('.inifile #setsetup .sectionstring').val().split('-');
		//console.log('start');
		for(var i=0;i<temp.length;i++){
			//console.log(temp[i]);
			//console.log($('.inifile #setprintlisttag .'+temp[i]).serialize());
			//console.log(i);
			inputdata[temp[i]]=$('.inifile #setsetup .'+temp[i]).serialize();
		}
		//console.log(inputdata);
		$.ajax({
			url:'./lib/js/save.setup.php',
			method:'post',
			async:false,
			data:inputdata,
			dataType:'html',
			success:function(d){
				console.log(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.inoutmoney #setinoutmoney .itemrow.click',function(){
		var index=$('.inoutmoney #setinoutmoney .itemrow').index(this);
		if($('.inoutmoney #setinoutmoney .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked')){
			$('.inoutmoney #setinoutmoney .itemrow:eq('+index+')').css({'background-color':'#ffffff'});
			$('.inoutmoney #setinoutmoney .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.inoutmoney #setinoutmoney .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.inoutmoney #setinoutmoney .itemrow:eq('+index+')').css({'background-color':'#E9E9E9'});
			$('.inoutmoney #setinoutmoney .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.inoutmoney #setinoutmoney .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
	});
	$(document).on('click','.inoutmoney #setinoutmoney .fun #create',function(){
		$('.inoutmoney #setinoutmoney .itemrow').css({'background-color':'#ffffff'});
		$('.inoutmoney #setinoutmoney .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.inoutmoney #setinoutmoney .itemrow #chimg').attr('src','./img/noch.png');
		$('.inoutmoney #setinoutmoney .itemrow').removeClass('click');
		var htmlst='<input id="save" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="crecancel" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('.inoutmoney #setinoutmoney .fun').html(htmlst);
		htmlst='<tr style="background-color:#E9E9E9;"><td><input type="checkbox" style="display:none;" name="createnew" checked><img id="chimg" src="./img/onch.png"></td><td>';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'</td><td><input type="text" name="createname"></td></tr>';
		$('.inoutmoney #setinoutmoney .classform table').append(htmlst);

	});
	$(document).on('click','.inoutmoney #setinoutmoney .fun #edit',function(){
		if($('.inoutmoney #setinoutmoney .itemrow input[type="checkbox"]:checked').length>0){
			$('.inoutmoney #setinoutmoney .itemrow').removeClass('click');
			var htmlst='<input id="save" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'save'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'"><input id="cancel" class="initbutton" type="button" value="';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'cancel'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			htmlst=htmlst+'">';
			$('.inoutmoney #setinoutmoney .fun').html(htmlst);
			htmlst='';
			$.ajax({
				url:'./lib/js/getininame.ajax.php',
				method:'post',
				async:false,
				data:{'file':'interface','lan':$('.lan').val(),'name':'newclasstitle'},
				dataType:'html',
				success:function(d){
					htmlst=htmlst+d;
				},
				error:function(e){
					console.log(e);
				}
			});
			$('.inoutmoney #setinoutmoney .classform .newclass').html(htmlst);
			for(var i=0;i<$('.inoutmoney #setinoutmoney .itemrow').length;i++){
				if($('.inoutmoney #setinoutmoney .itemrow:eq('+i+') input[type="checkbox"]').prop('checked')){
					$('.inoutmoney #setinoutmoney .itemrow:eq('+i+') #newclass').html('<input type="text" name="newclass[]">');
				}
				else{
				}
			}
		}
		else{
		}
	});
	$(document).on('click','.inoutmoney #setinoutmoney .fun #save',function(){
		$('.inoutmoney #setinoutmoney .fun #save').prop('disabled',true);
		$('.inoutmoney #setinoutmoney .fun #save').css({'opacity':'0.5','cursor':'inherit'});
		$.ajax({
			url:'./lib/js/saveclass.ajax.php',
			method:'post',
			data:$('.inoutmoney #setinoutmoney .classform').serialize(),
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('#inoutmoney').trigger('click');
				$('.inoutmoney #setinoutmoney .fun #save').prop('disabled',false);
				$('.inoutmoney #setinoutmoney .fun #save').css({'opacity':'1','cursor':'pointer'});
			},
			error:function(e){
				console.log(e);
				$('.inoutmoney #setinoutmoney .fun #save').prop('disabled',false);
				$('.inoutmoney #setinoutmoney .fun #save').css({'opacity':'1','cursor':'pointer'});
			}
		});
	});
	$(document).on('click','.inoutmoney #setinoutmoney .fun #cancel',function(){
		var htmlst='<input id="create" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('.inoutmoney #setinoutmoney .fun').html(htmlst);
		$('.inoutmoney #setinoutmoney .classform .newclass').html('');
		$('.inoutmoney #setinoutmoney .itemrow').addClass('click');
		$('.inoutmoney #setinoutmoney #newclass').html('');
		$('.inoutmoney #setinoutmoney .itemrow').css({'background-color':'#ffffff'});
		$('.inoutmoney #setinoutmoney .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.inoutmoney #setinoutmoney .itemrow #chimg').attr('src','./img/noch.png');
	});
	$(document).on('click','.inoutmoney #setinoutmoney .fun #crecancel',function(){
		var htmlst='<input id="create" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'create'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'"><input id="edit" class="initbutton" type="button" value="';
		$.ajax({
			url:'./lib/js/getininame.ajax.php',
			method:'post',
			async:false,
			data:{'file':'interface','lan':$('.lan').val(),'name':'edit'},
			dataType:'html',
			success:function(d){
				htmlst=htmlst+d;
			},
			error:function(e){
				console.log(e);
			}
		});
		htmlst=htmlst+'">';
		$('.inoutmoney #setinoutmoney .fun').html(htmlst);
		$('.inoutmoney #setinoutmoney .classform .newclass').html('');
		$('.inoutmoney #setinoutmoney .itemrow').addClass('click');
		$('.inoutmoney #setinoutmoney .classform table tr:eq('+(parseInt($('.inoutmoney #setinoutmoney .classform table tr').length)-1)+')').remove();
	});
});