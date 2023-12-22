function roundfun(number,precision){
	return Math.round(Number(number) * Math.pow(10, Number((precision || 0)))) / Math.pow(10, Number((precision || 0)));
}
function ceilfun(number,precision){
	return Math.ceil(Math.ceil(Number(number) * Math.pow(10, Number((precision || 0)) + 1)) / 10) / Math.pow(10, Number((precision || 0)));
}
function floorfun(number,precision){
	return Math.floor(Math.floor(Number(number) * Math.pow(10, Number((precision || 0)) + 1)) / 10) / Math.pow(10, Number((precision || 0)));
}
$(function() {
	FastClick.attach(document.body);
});
function listenForScrollEvent(el){//2021/6/1 模擬 .on(scroll,'',function(){})
    el.on("scroll", function(){
        el.trigger("custom-scroll");
    });
}
$(document).ready(function(){
	//2021/9/3 圖片延遲讀取
	$("img.lazy").lazyload({
		effect : "fadeIn"
	});
	var itemtype={};
	//if($('#content #items div[name^="itemtype"]').length>0){
		$.map($('#content #items div[name="itemtype"]'),function(n,i){
			return itemtype[i]=$('#content #items div[name="itemtype"]:eq('+i+')').offset().top-120;
		});
		/*for(var t=0;t<$('#content #items div[name^="itemtype"]').length;t++){//2021/6/2 與上方$.map同結果
			itemtype[$('#content #items div[name^="itemtype"]:eq('+t+')').attr('name').substr(8)]=$('#content #items div[name="itemtype'+$('#content #items div[name^="itemtype"]:eq('+t+')').attr('name').substr(8)+'"]').offset().top-120;
		}*/
		listenForScrollEvent($('#content #items'));
	/*}
	else{
	}*/
	$('#content').on('custom-scroll','#items',function(){
		$.map($('#content #items div[name="itemtype"]'),function(n,i){
			itemtype[i]=$('#content #items div[name="itemtype"]:eq('+i+')').offset().top-120;
			if(($('#content #items div[name="itemtype"]:eq('+(i+1)+')').length==0&&($('#content #items div[name="itemtype"]:eq('+i+')').offset().top-120)<54)||($('#content #items div[name="itemtype"]:eq('+(i+1)+')').length>0&&($('#content #items div[name="itemtype"]:eq('+(i+1)+')').offset().top-120)>=54&&($('#content #items div[name="itemtype"]:eq('+i+')').offset().top-120)<54)){
				//$('#content .type').css({'box-shadow':'rgba(0, 0, 0, 0.2) 2px 2px'});
				$('#content .type#checked').prop('id','');
				$('#content .type:eq('+i+')').prop('id','checked');
				//$('#content .type#checked').css({'box-shadow':'inset rgba(0, 0, 0, 0.2) 2px 2px'});
				$('input[class="itemtype"]').val($('#content .type:eq('+i+')').attr('name'));
			}
			else{
			}
		});
	});
	$('#title #getbasic').click(function(){
		console.log('1');
		if($('#title #titlename').html()!=''){//店家名稱
			$('.viewbasic #basicdata').append('<caption>'+$('#title #titlename').html()+'</caption>');
		}
		else{
		}
		$('.viewbasic #basicdata').append('<tr style="border-top:1px dotted #898989;"><td style="text-align:center;">營業/開單日</td><td>'+$('#setup input[name="bizdate"]').val()+'</td></tr>');
		$('.viewbasic #basicdata').append('<tr style="border-top:1px dotted #898989;"><td style="text-align:center;">目前/開單班別</td><td>'+$('#setup input[name="zcounter"]').val()+'</td></tr>');
		if($('#setup input[name="tablenumber"]').val()!=''){
			$.ajax({
				url:'./lib/js/gettablename.ajax.php',
				method:'post',
				async:false,
				data:{'tablenumber':$('#setup input[name="tablenumber"]').val()},
				dataType:'html',
				success:function(d){
					//console.log(d);
					$('.viewbasic #basicdata').append('<tr style="border-top:1px dotted #898989;"><td style="text-align:center;">桌號</td><td>'+d+'</td></tr>');
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
		}
		$('.viewbasic #basicdata').append('<tr style="border-top:1px dotted #898989;"><td style="text-align:center;">帳單號</td><td>'+$('#setup input[name="consecnumber"]').val()+'</td></tr>');
		$('.viewbasic #basicdata').append('<tr style="border-top:1px dotted #898989;"><td style="text-align:center;">帳單流水號</td><td>'+$('#setup input[name="saleno"]').val()+'</td></tr>');
		$('.viewbasic').css({'display':'block'});
		$('.modal').css({'display':'block'});
	});
	$('.setwin').on('click','#openclass',function(){
		/*$('.setwin #openclass').css('cursor','');
		$('.setwin #openclass').css('-webkit-filter','grayscale(100%)');
		$('.setwin #openclass').css('filter','grayscale(100%)');
		$('.setwin #openclass').prop('id','disopenclass');
		$('.setwin #discloseclass').prop('id','closeclass');
		$('.setwin #closeclass').css('cursor','pointer');
		$('.setwin #closeclass').css('-webkit-filter','');
		$('.setwin #closeclass').css('filter','');*/

		$.ajax({
			url:'../demopos/lib/js/open.ajax.php',
			method:'post',
			async: false,
			data:{'usercode':$('.basic input[name="usercode"]').val(),'username':$('.basic input[name="username"]').val(),'machinetype':'m1'},
			dataType:'html',
			success:function(d){
				console.log(d);
				if(d.length>20){
					$.ajax({
						url:'../demopos/lib/js/print.php',
						method:'post',
						data:{'html':'open.ajax.php '+d},
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
				if(d=='error'){
					//console.log(d);
				}
				else{
					var bizdate=d.split('-');
					$('.basic input[name="date"]').val(bizdate[1].substr(0,4)+'-'+bizdate[1].substr(4,2)+'-'+bizdate[1].substr(6,2));
					/*$('.order#order #billfun #billfun2 .outmoney').prop('disabled',false);
					$('.order#order #billfun #billfun2 #salevoid').prop('disabled',false);
					$('#outmoney #picbizdate').val(bizdate[1].substr(0,4)+'-'+bizdate[1].substr(4,2)+'-'+bizdate[1].substr(6,2));
					$('#outmoney #picbizdate').trigger('change');
					$('.spend #nowbizdate').val(bizdate[1]);
					$('.spend #nowzcounter').val(bizdate[2]);*/
					$.ajax({
						url:'../demopos/lib/js/change.class.php',
						method:'post',
						async: false,
						data:{'type':'isclose','machinetype':'m1','whopass':'orderpos-open.ajax.php'},
						dataType:'html',
						success:function(d){
							var tempdata=d.split('-');
							if(d.length>20){
								$.ajax({
									url:'../demopos/lib/js/print.php',
									method:'post',
									data:{'html':'change.class.php '+d},
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
							$('.setwin #openclass').css('cursor','');
							$('.setwin #openclass').css('-webkit-filter','grayscale(100%)');
							$('.setwin #openclass').css('filter','grayscale(100%)');
							$('.setwin #openclass').prop('id','disopenclass');
							$('.setwin #discloseclass').prop('id','closeclass');
							$('.setwin #closeclass').css('cursor','pointer');
							$('.setwin #closeclass').css('-webkit-filter','');
							$('.setwin #closeclass').css('filter','');
							//2017/12/29var mywin=window.open('cashdrawer://upload','','width=1px,height=1px');
							//2017/12/29mywin.document.title='cashdrawer';
							/*$('.order#order #billfun #billfun2 .close').prop('disabled',false);
							if($('.order#order .initsetting #controltable').val()=='1'){
								$('.control#control #bizdate').val(tempdata[0]);
								$('.control#control #zcounter').val(tempdata[1]);
								$('.inittable .funcmap #tablesplit').prop('disabled',false);
								$('.inittable .funcmap #combine').prop('disabled',false);
								$('.inittable .funcmap #tablecombine').prop('disabled',false);
								$('.inittable .funcmap #changetable').prop('disabled',false);
								var temptitle=inittable.dialog('option','title');
								var temptitle1=temptitle.split(':');
								var temptitle2=temptitle1[1].split('；');
								var temptitle3=temptitle1[2].split('；');
								inittable.dialog('option','title',temptitle1[0]+':'+$('.control#control #bizdate').val()+'；'+temptitle2[1]+':'+$('.control#control #zcounter').val()+'；'+temptitle3[1]);
								$('.funbox #close').prop('disabled',false);
								$('.funbox #salelist').prop('disabled',false);
								$('.funbox #voidsale').prop('disabled',false);
								if($('.order#order .initsetting #kvm').val()=='1'){
									$('.funbox #kvm').prop('disabled',false);
								}
								else{
								}
								$('.funbox #return').prop('disabled',false);
								if($('.order#order .initsetting #moneycost').val()=='1'){
									$('.funbox #AE').prop('disabled',false);
								}
								else{
								}
								if($('.order#order .initsetting #openpunch').val()=='1'){
									$('.funbox #punch').prop('disabled',false);
									$('.funbox #editpunch').prop('disabled',false);
								}
								else{
								}
								if($('.order#order .initsetting #historypaper').val()=='1'){
									$('.funbox #historypaper').prop('disabled',false);
								}
								else{
								}
								if($('.order#order .initsetting #openindex').val()=='1'){
									$('.funbox #logout').prop('disabled',false);
								}
								else{
								}
								$('.funbox #updatemenu').prop('disabled',false);
							}
							else{
							}*/
							//setchange.dialog('open');
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
	$('.setwin').on('click','#closeclass',function(){
		/*$('.setwin #closeclass').css('cursor','');
		$('.setwin #closeclass').css('-webkit-filter','grayscale(100%)');
		$('.setwin #closeclass').css('filter','grayscale(100%)');
		$('.setwin #closeclass').prop('id','discloseclass');
		$('.setwin #disopenclass').prop('id','openclass');
		$('.setwin #openclass').css('cursor','pointer');
		$('.setwin #openclass').css('-webkit-filter','');
		$('.setwin #openclass').css('filter','');*/

		$('.message2 input[name="msgtype"]').val('class');
		$('.message2 #text').html('確定要交班嗎?');
		$('.message2').css('z-index','2021');
		$('.message2').css('display','block');
	});
	$('#content').on('touchstart','.type',function(){
		//$(this).css({'box-shadow':'inset rgba(0, 0, 0, 0.2) 2px 2px'});
	});
	$('#content').on('touchend','.type',function(){
		//$('#content .type').css({'box-shadow':'rgba(0, 0, 0, 0.2) 2px 2px'});
		//$('#content .type#checked').css({'box-shadow':'inset rgba(0, 0, 0, 0.2) 2px 2px'});
	});
	$('#content').on('click','.type',function(){
		//$('#content .type').css({'box-shadow':'rgba(0, 0, 0, 0.2) 2px 2px'});
		$('#content .type#checked').prop('id','');
		$(this).prop('id','checked');
		//$('#content .type#checked').css({'box-shadow':'inset rgba(0, 0, 0, 0.2) 2px 2px'});
		var index=$(this).index('#content .type');
		/*$.ajax({
			url:'./lib/js/getitems.ajax.php',
			method:'post',
			async:false,
			data:{'story':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val(),'type':$('#content .type:eq('+index+') input[name="type"]').val()},
			dataType:'html',
			success:function(d){
				$('input[class="type"]').val('items');
				$('input[class="itemtype"]').val($('#content .type:eq('+index+') input[name="type"]').val());
				$('#items').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});*/
		$('input[class="itemtype"]').val($('#content .type:eq('+index+') input[name="type"]').val());
		$('#content #items').scrollTop($('#content #items div[name="itemtype"]:eq('+index+')').offset().top-120+$('#content #items').scrollTop());
	});
	$('#content').on('touchstart','.item',function(){
		$(this).css({'box-shadow':'inset rgba(0, 0, 0, 0.2) 2px 2px'});
	});
	$('#content').on('touchend','.item',function(){
		$('#content .item').css({'box-shadow':'rgba(0, 0, 0, 0.2) 2px 2px'});
	});
	$('#content').on('click','.item',function(){
		var index=$(this).index('#content .item');
		$('html').css({'overflow':'hidden'});
		$.ajax({
			url:'./lib/js/getdetail.ajax.php',
			method:'post',
			async:false,
			data:{'story':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val(),'item':$('#content .item:eq('+index+') input[name="item"]').val()},
			dataType:'html',
			success:function(d){
				$('input[class="type"]').val('detail');
				$('.detail').css({'display':'block'});
				$('.detail #data').html(d);
				$('.detail #footer .money').html($('.detail #data #detail input[name="amt"]').val());
			},
			error:function(e){
				console.log(e);
			}
		});
		$('.detail #data').scrollTop(0);
	});
	$('.detail #data').scroll(function(){
		//console.log('1');
		if($('.detail #data .itemdata .pagetop').length>0){
			if($('.detail #data').scrollTop()!=0){
				$('.detail #data .itemdata .pagetop').css({'display':'block'});
			}
			else{
				$('.detail #data .itemdata .pagetop').css({'display':'none'});
			}
		}
		else{
		}
	});
	$('.detail #data').on('click','.itemdata .pagetop',function(){
		$('.detail #data').scrollTop(0);
	});
	$('#title .return').click(function(){
		if($('input[class="type"]').val()=='detail'){
			if($('.detail #data .itemdata input[name="itemseq"]').length>0){
				$('html').css({'overflow':'auto'});
				$('input[class="type"]').val('itemlist');
				$('.detail').css({'display':'none'});
				$('.detail #data').html('');
				$('.detail #footer .money').html('');
			}
			else{
				$('html').css({'overflow':'auto'});
				$('input[class="type"]').val('items');
				$('.detail').css({'display':'none'});
				$('.detail #data').html('');
				$('.detail #footer .money').html('');
				var clicktype=$('input[class="itemtype"]').val();
				$.ajax({
					url:'./lib/js/gettypes.ajax.php',
					method:'post',
					async:false,
					data:{'story':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val(),'itemtype':$('input[class="itemtype"]').val()},
					dataType:'html',
					success:function(d){
						$('input[class="type"]').val('items');
						$('input[class="itemtype"]').val('');
						$('#content').css({'padding':'50px 0 0 0'});
						$('#content').html(d);
						listenForScrollEvent($('#content #items'));
						$('#content #type .type[name="'+clicktype+'"]').trigger('click');
						//2021/9/3 圖片延遲讀取
						$("img.lazy").lazyload({
							effect : "fadeIn"
						});
					},
					error:function(e){
						console.log(e);
					}
				});
			}
		}
		else if($('input[class="type"]').val()=='itemlist'){
			var clicktype=$('input[class="itemtype"]').val();
			$.ajax({
				url:'./lib/js/gettypes.ajax.php',
				method:'post',
				async:false,
				data:{'story':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val()},
				dataType:'html',
				success:function(d){
					$('input[class="type"]').val('items');
					$('input[class="itemtype"]').val('');
					$('#content').css({'padding':'50px 0 0 0'});
					$('#content').html(d);
					listenForScrollEvent($('#content #items'));
					$('#content #type .type[name="'+clicktype+'"]').trigger('click');
					//$('#keybox .funkey1').prop('id','');
					//$('#keybox .funkey1').html("");
					$('#keybox .funkey2 div:eq(0)').css({'width':'100%','display':'block'});
					$('#keybox .funkey2 div:eq(1)').css({'display':'none'});
					$('#keybox .funkey2 div:eq(0)').prop('id','list');
					$('#keybox .funkey2 div:eq(0)').html('下一步');
					$('#keybox .funkey2 #point').css({'display':'block'});
					//2021/9/3 圖片延遲讀取
					$("img.lazy").lazyload({
						effect : "fadeIn"
					});
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else if($('input[class="type"]').val().substr(0,6)=='mylist'){
			$('input[class="type"]').val($('input[class="type"]').val().substr(7));
			//console.log($('input[class="type"]').val());
			$('#title .return').trigger('click');
		}
		else if($('input[class="type"]').val().substr(0,6)=='items'){
			$('.message2 input[name="msgtype"]').val('outorder');
			$('.message2 #text').html('返回將會取消本次點選品項，是否返回？');
			//msg2.dialog('open');
			$('.message2').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else{
		}
	});
	function compsub(){
		var money=$('.detail #data #detail #money option:selected').val().split(';');
		if($('.detail #data #detail #totalsubmoney').length>0){//2021/7/6 套餐部分
			var totalsubmoney=$('.detail #data #detail #totalsubmoney').html();
		}
		else{
			var totalsubmoney=0;
		}
		$('.detail #data #detail input[name="amt"]').val((parseInt($('.detail #data #detail #tastemoney').html())+parseInt(totalsubmoney)+parseInt(money[1]))*parseInt($('.detail #data #detail input[name="qty"]').val()));
		$('.detail #footer .money').html($('.detail #data #detail input[name="amt"]').val());

		//2021/7/6 價格名稱對應的贈點規則
		if($('.detail #data #detail input[name="getpointtype'+$('.detail #data #detail #money option:selected').prop('id')+'"]').length>0){
			$('.detail #data #detail input[name="getpointtype"]').val($('.detail #data #detail input[name="getpointtype'+$('.detail #data #detail #money option:selected').prop('id')+'"]').val());
		}
		else{
			$('.detail #data #detail input[name="getpointtype"]').val('1');
		}
		if($('.detail #data #detail input[name="getpoint'+$('.detail #data #detail #money option:selected').prop('id')+'"]').length>0){
			$('.detail #data #detail input[name="initgetpoint"]').val($('.detail #data #detail input[name="getpoint'+$('.detail #data #detail #money option:selected').prop('id')+'"]').val());
			$('.detail #data #detail input[name="getpoint"]').val(parseInt($('.detail #data #detail input[name="getpoint'+$('.detail #data #detail #money option:selected').prop('id')+'"]').val())*parseInt($('.detail #data #detail input[name="qty"]').val()));
		}
		else{
			$('.detail #data #detail input[name="initgetpoint"]').val('0');
			$('.detail #data #detail input[name="getpoint"]').val('0');
		}
	}
	function compsubtaste(){
		var money=$('.subtaste .tastecontent #detail #money option:selected').val().split(';');
		if($('.subtaste .tastecontent #detail #totalsubmoney').length>0){
			var totalsubmoney=$('.subtaste .tastecontent #detail #totalsubmoney').html();
		}
		else{
			var totalsubmoney=0;
		}
		$('.subtaste .tastecontent #detail input[name="amt"]').val(parseInt($('.subtaste .tastecontent #detail #tastemoney').html())+parseInt(totalsubmoney)+parseInt(money[1]));
		$('.subtaste .tastefunbox .money').html($('.subtaste .tastecontent #detail input[name="amt"]').val());//2021/6/16 確認右方增加"小計"區塊(與外層格式相同)
		
		//2020/5/19 價格名稱對應的贈點規則
		if($('.subtaste .tastecontent #detail input[name="getpointtype'+$('.subtaste .tastecontent #detail #money option:selected').prop('id')+'"]').length>0){
			$('.subtaste .tastecontent #detail input[name="getpointtype"]').val($('.subtaste .tastecontent #detail input[name="getpointtype'+$('.subtaste .tastecontent #detail #money option:selected').prop('id')+'"]').val());
		}
		else{
			$('.subtaste .tastecontent #detail input[name="getpointtype"]').val('1');
		}
		if($('.subtaste .tastecontent #detail input[name="getpoint'+$('.subtaste .tastecontent #detail #money option:selected').prop('id')+'"]').length>0){
			$('.subtaste .tastecontent #detail input[name="initgetpoint"]').val($('.subtaste .tastecontent #detail input[name="getpoint'+$('.subtaste .tastecontent #detail #money option:selected').prop('id')+'"]').val());
			$('.subtaste .tastecontent #detail input[name="getpoint"]').val(parseInt($('.subtaste .tastecontent #detail input[name="getpoint'+$('.subtaste .tastecontent #detail #money option:selected').prop('id')+'"]').val()));
		}
		else{
			$('.subtaste .tastecontent #detail input[name="initgetpoint"]').val('0');
			$('.subtaste .tastecontent #detail input[name="getpoint"]').val('0');
		}
	};
	$('.detail #data').on('click','#diff',function(){
		if($('.detail #data #detail input[name="qty"]').val()==1){
		}
		else{
			$('.detail #data #detail input[name="qty"]').val(parseInt($('.detail #data #detail input[name="qty"]').val())-1);
			$('.detail #data .itemdata input[name="qty"]').val(parseInt($('.detail #data #detail input[name="qty"]').val()));
			compsub();
		}
	});
	$('.detail #data').on('click','#plus',function(){
		$('.detail #data #detail input[name="qty"]').val(parseInt($('.detail #data #detail input[name="qty"]').val())+1);
		$('.detail #data .itemdata input[name="qty"]').val(parseInt($('.detail #data #detail input[name="qty"]').val()));
		compsub();
	});
	$('.detail #data').on('change','#detail input[name="qty"],#detail #money',function(){
		if(parseInt($('.detail #data #detail input[name="qty"]').val())<=0){
			$('.detail #data input[name="qty"]').val('1');
		}
		else{
		}
		compsub();
	});
	$('.detail #data').on('change','#detail #openmoney',function(){
		if($('.detail #data #detail #openmoney').val().length==0||!$.isNumeric($('.detail #data #detail #openmoney').val())){
			$('.detail #data #detail #openmoney').val('0');
		}
		else{
			$('.detail #data #detail #openmoney').val(parseInt($('.detail #data #detail #openmoney').val()));
		}
		if($('.detail #data #detail #money option:selected').length==0){
			var selindex=0;
		}
		else{
			var selindex=$('.detail #data #detail #money option:selected').index('.detail #data #detail #money option');
		}
		var temp=$('.detail #data #detail #money option:eq('+selindex+')').val().split(';');
		$('.detail #data #detail #money option:eq('+selindex+')').val(temp[0]+';'+parseInt($('.detail #data #detail #openmoney').val()));
		$('.detail #data #detail #money').trigger('change');
	});
	$('.detail #data').on('click','#detail tr[class^="tastelabel"]',function(){
		var idname=$(this).prop('class');
		
		if($('.detail #data #detail #'+idname).css('display')=='none'){//2021/7/6 展開
			$(this).find('img').css({'transform':'rotate(90deg)'});

			$('.detail #data #detail #'+idname).css({'display':'table-row'});
		}
		else{
			$(this).find('img').css({'transform':'rotate(-90deg)'});

			$('.detail #data #detail #'+idname).css({'display':'none'});
		}
		$('.detail #data .tastenumberbox').css({'display':'none'});
	});
	$('.detail #data').on('click','#detail .nochoseitem',function(){		
		if($('.detail #data #detail #nochoseitem').css('display')=='none'){//2021/7/6 展開
			$(this).find('img').css({'transform':'rotate(90deg)'});

			$('.detail #data #detail #nochoseitem').css({'display':'table-row'});
		}
		else{
			$(this).find('img').css({'transform':'rotate(-90deg)'});

			$('.detail #data #detail #nochoseitem').css({'display':'none'});
		}
	});
	$('.detail #data').on('click','#detail .choseitem',function(){
		var idname=$(this).prop('id');
		if($('.detail #data #detail #choseitem'+idname).css('display')=='none'){//2021/7/6 展開
			$(this).find('img').css({'transform':'rotate(90deg)'});

			$('.detail #data #detail #choseitem'+idname).css({'display':'table-row'});
		}
		else{
			$(this).find('img').css({'transform':'rotate(-90deg)'});

			$('.detail #data #detail #choseitem'+idname).css({'display':'none'});
		}
	});
	$('.detail #data').on('click','#detail #label',function(){
		var index=$(this).index('.detail #data #detail #label');
		$('.detail .tastenumberbox').css({'display':'none'});
		if($('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked')){
			$('.detail #data #detail .switch input[name="tastenumber[]"]:eq('+index+')').prop('checked',false);
			$('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked',false);
		}
		else{
			$('.detail #data .tastenumberbox').css({'top':'calc('+$(this).offset().top+'px + '+$('.detail #data').scrollTop()+'px - 6px - 60px - 5%)','left':$(this).offset().left+'px'});
			$('.detail #data .tastenumberbox .numberinput').val('1');
			$('.detail #data .tastenumberbox .tasteindex').val(index);
			$('.detail #data #detail .switch input[name="tastenumber[]"]:eq('+index+')').prop('checked',true);
			$('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked',true);
		}
		$('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').trigger('change');
		/*if($('.detail #data #detail .switch input[type="checkbox"]:eq('+index+')').prop('checked')){
			$('.detail #data #detail .switch input[type="checkbox"]:eq('+index+')').prop('checked',false);
		}
		else{
			$('.detail #data #detail .switch input[type="checkbox"]:eq('+index+')').prop('checked',true);
		}
		$('.detail #data #detail .switch input[type="checkbox"]:eq('+index+')').trigger('change');*/
	});
	$('.detail #data').on('change','#detail .switch input[name="tasteno[]"]',function(){
		var index=$(this).index('.detail #data #detail .switch input[name="tasteno[]"]');
		var mobiletastetype=$(this).parents('div.switch').find('input[name="maxlimit[]"]').val();//2021/7/6 每個備註設定可選最大值
		var tastetypeid=$(this).parents('tr[id^="tastelabel"]').prop('id');
		var sametypeindex=$(this).index('.detail #data #detail #'+tastetypeid+' .switch input[name="tasteno[]"]');
		if($('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked')){
			var target=$('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').val();
		}
		else{
			var target='';
		}
		//alert(target);
		$.ajax({
			url:'./lib/js/check.tastegroup.php',
			method:'post',
			async:false,
			data:$('.detail #data .itemdata').serialize()+"&company="+$('.basic input[name="story"]').val()+"&dep="+$('.basic input[name="dep"]').val()+"&target="+target,
			dataType:'json',
			timeout:5000,
			success:function(d){
				if(d['state']=='pass'){
					//console.log(d);
					if($('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked')){
						$('.detail #data #detail .switch:eq('+index+')').parents('#label').css({'background-color':'rgb(26,26,26,0.5)','color':'#ffffff'});
						//$('.detail #data #detail #n:eq('+index+')').css({'color':'#dcdcdc','font-weight':'normal'});
						//$('.detail #data #detail #y:eq('+index+')').css({'color':'#4a4a4a','font-weight':'bold'});
						$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())+parseInt($('.detail #data #detail .switch input[name="tastenumber[]"]:eq('+index+')').val()));
						if(mobiletastetype>1&&($('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()==-1||$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val()<$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val())){
							$('.detail .tastenumberbox').css({'display':'block'});
						}
						else{
						}
						$('.detail #data #detail #tastemoney').html(parseFloat($('.detail #data #detail #tastemoney').html())+Number($('.detail #data #detail .switch:eq('+index+') input[name="money[]"]').val()));
					}
					else{
						$('.detail #data #detail .switch:eq('+index+')').parents('#label').css({'background-color':'#ffffff','color':'#000000'});
						//$('.detail #data #detail #n:eq('+index+')').css({'color':'#4a4a4a','font-weight':'bold'});
						//$('.detail #data #detail #y:eq('+index+')').css({'color':'#dcdcdc','font-weight':'normal'});
						$('.detail .tastenumberbox').css({'display':'none'});
						$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())-parseInt($('.detail #data #detail .switch input[name="tastenumber[]"]:eq('+index+')').val()));
						$('.detail #data #detail #tastemoney').html(parseFloat($('.detail #data #detail #tastemoney').html())-(Number($('.detail #data #detail .switch:eq('+index+') input[name="money[]"]').val())*Number($('.detail #data #detail .switch:eq('+index+') input[name="tastenumber[]"]').val())));
						$('.detail #data #detail .switch:eq('+index+') span[data-id="tastenumber"]').html('');
						$('.detail #data #detail .switch:eq('+index+') span[data-id="tastenumber"]').css({'display':'none'});
						$('.detail #data #detail .switch input[name="tastenumber[]"]:eq('+index+')').val('1');
					}
				}
				else{
					if($('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked')){
						$('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked',false);
						$('.detail #data #detail .switch input[name="tastenumber[]"]:eq('+index+')').prop('checked',false);
					}
					else{
						$('.detail #data #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked',true);
						$('.detail #data #detail .switch input[name="tastenumber[]"]:eq('+index+')').prop('checked',true);
					}
				}
			},
			error:function(e){
				//console.log(e);
			}
		});
		/*var index=$(this).index('#content #detail .switch input[type="checkbox"]');
		//console.log(index);
		if($('#content #detail .switch input[type="checkbox"]:eq('+index+')').prop('checked')){
			$('#content #detail #n:eq('+index+')').css({'color':'#898989','font-weight':'normal'});
			$('#content #detail #y:eq('+index+')').css({'color':'#4a4a4a','font-weight':'bold'});
			$('#content #detail #tastemoney').html(parseFloat($('#content #detail #tastemoney').html())+Number($('#content #detail .switch:eq('+index+') input[name="money[]"]').val()));
		}
		else{
			$('#content #detail #n:eq('+index+')').css({'color':'#4a4a4a','font-weight':'bold'});
			$('#content #detail #y:eq('+index+')').css({'color':'#898989','font-weight':'normal'});
			$('#content #detail #tastemoney').html(parseFloat($('#content #detail #tastemoney').html())-Number($('#content #detail .switch:eq('+index+') input[name="money[]"]').val()));
		}*/
		compsub();
	});
	$('.detail #data').on('click','.tastenumberbox .difftaste',function(){//2021/7/6 遞減備註選項數量
		if($('.detail #data .tastenumberbox .numberinput').val()>1){
			$('.detail #data .tastenumberbox .numberinput').val(parseInt($('.detail #data .tastenumberbox .numberinput').val())-1);
			$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') input[name="tastenumber[]"]').val(parseInt($('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') input[name="tastenumber[]"]').val())-1);
			$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())-1);
			$('.detail #data #detail #tastemoney').html(parseFloat($('.detail #data #detail #tastemoney').html())-Number($('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') input[name="money[]"]').val()));
			if($('.detail #data .tastenumberbox .numberinput').val()>1){
				$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').css({'display':'contents'});
				$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').html('*'+$('.detail #data .tastenumberbox .numberinput').val());
			}
			else{
				$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').css({'display':'none'});
				$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').html('');
			}
			compsub();
		}
		else{
		}
	});
	$('.detail #data').on('click','.tastenumberbox .addtaste',function(){//2021/7/6 遞增備註選項數量
		if($('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()=='-1'||$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()>$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val()){
			$('.detail #data .tastenumberbox .numberinput').val(parseInt($('.detail #data .tastenumberbox .numberinput').val())+1);
			$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') input[name="tastenumber[]"]').val(parseInt($('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') input[name="tastenumber[]"]').val())+1);
			$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())+1);
			$('.detail #data #detail #tastemoney').html(parseFloat($('.detail #data #detail #tastemoney').html())+Number($('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') input[name="money[]"]').val()));
			if($('.detail #data .tastenumberbox .numberinput').val()>1){
				$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').css({'display':'contents'});
				$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').html('*'+$('.detail #data .tastenumberbox .numberinput').val());
			}
			else{
			}
			if(($('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()==$('.detail #data #detail .'+$('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())||($('.detail #data #detail .switch:eq('+$('.detail #data .tastenumberbox .tasteindex').val()+') input[name="maxlimit[]"]').val()==$('.detail #data .tastenumberbox .numberinput').val())){
				$('.detail .tastenumberbox').css({'display':'none'});
			}
			else{
			}
			compsub();
		}
		else{
		}
	});
	$('.detail #data').on('click','#detail #subitem',function(){
		var index=$(this).index('.detail #data #detail #subitem');
		if($('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').prop('checked')){
			$('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').prop('checked',false);
		}
		else{
			$('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').prop('checked',true);
		}
		$('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').trigger('change');
	});
	$('.detail #data').on('change','#detail #subitem input[name="subvarno[]"]',function(){
		var trid=$(this).parents('tr[id^="choseitem"]').prop('id').substr(9);
		var index=$(this).index('.detail #data #detail #subitem input[name="subvarno[]"]');
		
		if($('.detail #data #detail .choseitem#'+trid+' input[name="chosenumber"]').val()>=$('.detail #data #detail #choseitem'+trid+' input[name="subvarno[]"]:checked').length){
			//console.log(d);
			if($('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').prop('checked')){
				$('.detail #data #detail #subitem #n:eq('+index+')').css({'color':'#dcdcdc','font-weight':'normal'});
				$('.detail #data #detail #subitem #y:eq('+index+')').css({'color':'#4a4a4a','font-weight':'bold'});
				$('.detail #data #detail #totalsubmoney').html(parseFloat($('.detail #data #detail #totalsubmoney').html())+Number($('.detail #data #detail #subitem input[name="subvarmoney[]"]:eq('+index+')').val()));
			}
			else{
				$('.detail #data #detail #subitem #n:eq('+index+')').css({'color':'#4a4a4a','font-weight':'bold'});
				$('.detail #data #detail #subitem #y:eq('+index+')').css({'color':'#dcdcdc','font-weight':'normal'});
				$('.detail #data #detail #totalsubmoney').html(parseFloat($('.detail #data #detail #totalsubmoney').html())-Number($('.detail #data #detail #subitem input[name="subvarmoney[]"]:eq('+index+')').val()));
			}
		}
		else{
			if($('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').prop('checked')){
				$('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').prop('checked',false);
			}
			else{
				$('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').prop('checked',true);
			}
		}
		compsub();

		if($('.detail #data #detail #subitem input[name="subvarno[]"]:eq('+index+')').prop('checked')){
			//$('.detail #data #detail div[class^="subvaritemmoney"]:eq('+index+')').css({'display':'block'});
			$('.detail #data #detail div[class^="choseitem"]:eq('+index+')').css({'display':'block'});
		}
		else{
			//$('.detail #data #detail div[class^="subvaritemmoney"]:eq('+index+')').css({'display':'none'});
			$('.detail #data #detail div[class^="choseitem"]:eq('+index+')').css({'display':'none'});
		}
	});
	$('.detail #data').on('click','#detail #taste[class^="choseitem"], #detail #taste[class^="nochoseitem"]',function(){
		var trid=$(this).parents('tr').prop('id');
		var seq=$(this).prop('class').substr(trid.length);

		$('.subtaste .trid').val(trid);
		$('.subtaste .seq').val(seq);
		if(trid.substr(0,9)=='choseitem'){
			$('.subtaste .tastetitle').html($('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subvarname[]"]').val());
			var itemno=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subvarno[]"]').val();
			var mname=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subvarmname1[]"]').val();
			var money=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subvarunitprice[]"]').val();
			var tasteno=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subvartaste1[]"]').val();
			var tastemoney=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subvartaste1money[]"]').val();
			var tastename=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subvartaste1name[]"]').val();
			var tastenumber=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subvartaste1number[]"]').val();
		}
		else{
			$('.subtaste .tastetitle').html($('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subfixname[]"]').val());
			var itemno=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subfixno[]"]').val();
			var mname=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subfixmname1[]"]').val();
			var money=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subfixunitprice[]"]').val();
			var tasteno=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subfixtaste1[]"]').val();
			var tastemoney=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subfixtaste1money[]"]').val();
			var tastename=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subfixtaste1name[]"]').val();
			var tastenumber=$('.detail #data #detail #'+trid+':eq('+(seq-1)+') .switch input[name="subfixtaste1number[]"]').val();
		}
		$.ajax({
			url:'./lib/js/getsubitem.taste.ajax.php',
			method:'post',
			async:false,
			data:{'story':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val(),'itemno':itemno,'mname':mname,'money':money,'tasteno':tasteno,'tastemoney':tastemoney,'tastename':tastename,'tastenumber':tastenumber},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.subtaste .tastecontent').html(d);
				compsubtaste();
			},
			error:function(e){
				//console.log(e);
			}
		});
		$('.subtaste').css({'display':'block'});
		$('.subtaste').animate({
			top:'0'
		},500);
	});
	$('.subtaste .tastecontent').on('change','#detail #money',function(){
		compsubtaste();
	});
	$('.subtaste .tastecontent').on('click','#detail #label',function(){
		var index=$(this).index('.subtaste .tastecontent #detail #label');
		$('.subtaste .tastenumberbox').css({'display':'none'});
		if($('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked')){
			$('.subtaste .tastecontent #detail .switch input[name="tastenumber[]"]:eq('+index+')').prop('checked',false);
			$('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked',false);
		}
		else{
			$('.subtaste .tastenumberbox').css({'top':'calc('+$(this).offset().top+'px + '+$('.subtaste .tastecontent').scrollTop()+'px - 5%)','left':$(this).offset().left+'px'});
			$('.subtaste .tastenumberbox .numberinput').val('1');
			$('.subtaste .tastenumberbox .tasteindex').val(index);
			$('.subtaste .tastecontent #detail .switch input[name="tastenumber[]"]:eq('+index+')').prop('checked',true);
			$('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked',true);
		}
		$('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').trigger('change');
	});
	$('.subtaste .tastecontent').on('change','#detail .switch input[name="tasteno[]"]',function(){
		var index=$(this).index('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]');
		var mobiletastetype=$(this).parents('div.switch').find('input[name="maxlimit[]"]').val();//2021/7/6 每個備註設定可選最大值
		var tastetypeid=$(this).parents('tr[id^="tastelabel"]').prop('id');
		var sametypeindex=$(this).index('.subtaste .tastecontent #detail #'+tastetypeid+' .switch input[name="tasteno[]"]');
		if($('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked')){
			var target=$('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').val();
		}
		else{
			var target='';
		}
		//alert(target);
		$.ajax({
			url:'./lib/js/check.tastegroup.php',
			method:'post',
			async:false,
			data:$('.subtaste .tastecontent .itemdata').serialize()+"&company="+$('.basic input[name="story"]').val()+"&dep="+$('.basic input[name="dep"]').val()+"&target="+target,
			dataType:'json',
			timeout:5000,
			success:function(d){
				if(d['state']=='replace'){//2021/7/6 當備註為單選時，點選其他備註後，直接切換過去(與POS介面相同操作)
					for(var e=0;e<$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch input[name="tasteno[]"]').length;e++){
						if(e!=sametypeindex){
							$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+e+')').parents('#label').css({'background-color':'#ffffff','color':'#000000'});
							$('.subtaste .tastenumberbox').css({'display':'none'});
							if($('.subtaste .tastecontent #detail #'+tastetypeid+' .switch input[name="tasteno[]"]:eq('+e+')').prop('checked')){
								$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+e+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+e+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())-parseInt($('.subtaste .tastecontent #detail #'+tastetypeid+' .switch input[name="tastenumber[]"]:eq('+e+')').val()));
								$('.subtaste .tastecontent #detail #tastemoney').html(parseFloat($('.subtaste .tastecontent #detail #tastemoney').html())-(Number($('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+e+') input[name="money[]"]').val())*Number($('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+e+') input[name="tastenumber[]"]').val())));
								$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch input[name="tasteno[]"]:eq('+e+')').prop('checked',false);
							}
							else{
							}
							$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+e+') span[data-id="tastenumber"]').html('');
							$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+e+') span[data-id="tastenumber"]').css({'display':'none'});
							$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch input[name="tastenumber[]"]:eq('+e+')').val('1');
						}
						else{
							$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+sametypeindex+')').parents('#label').css({'background-color':'rgb(26,26,26,0.5)','color':'#ffffff'});
							$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+sametypeindex+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+sametypeindex+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())+parseInt($('.subtaste .tastecontent #detail #'+tastetypeid+' .switch input[name="tastenumber[]"]:eq('+sametypeindex+')').val()));
							if(mobiletastetype>1&&($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+sametypeindex+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()==-1||$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+sametypeindex+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val()<$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+sametypeindex+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val())){
								$('.subtaste .tastenumberbox').css({'display':'block'});
							}
							else{
							}
							$('.subtaste .tastecontent #detail #tastemoney').html(parseFloat($('.subtaste .tastecontent #detail #tastemoney').html())+Number($('.subtaste .tastecontent #detail #'+tastetypeid+' .switch:eq('+sametypeindex+') input[name="money[]"]').val()));
						}
					}
				}
				else if(d['state']=='pass'){
					//console.log(d);
					if($('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked')){
						//$('.subtaste .tastecontent #detail #n:eq('+index+')').css({'color':'#dcdcdc','font-weight':'normal'});
						//$('.subtaste .tastecontent #detail #y:eq('+index+')').css({'color':'#4a4a4a','font-weight':'bold'});
						$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('#label').css({'background-color':'rgb(26,26,26,0.5)','color':'#ffffff'});
						$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())+parseInt($('.subtaste .tastecontent #detail .switch input[name="tastenumber[]"]:eq('+index+')').val()));
						if(mobiletastetype>1&&($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()==-1||$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val()<$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val())){
							$('.subtaste .tastenumberbox').css({'display':'block'});
						}
						else{
						}
						$('.subtaste .tastecontent #detail #tastemoney').html(parseFloat($('.subtaste .tastecontent #detail #tastemoney').html())+Number($('.subtaste .tastecontent #detail .switch:eq('+index+') input[name="money[]"]').val()));
					}
					else{
						//$('.subtaste .tastecontent #detail #n:eq('+index+')').css({'color':'#4a4a4a','font-weight':'bold'});
						//$('.subtaste .tastecontent #detail #y:eq('+index+')').css({'color':'#dcdcdc','font-weight':'normal'});
						$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('#label').css({'background-color':'#ffffff','color':'#000000'});
						$('.subtaste .tastenumberbox').css({'display':'none'});
						$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+index+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())-parseInt($('.subtaste .tastecontent #detail .switch input[name="tastenumber[]"]:eq('+index+')').val()));
						$('.subtaste .tastecontent #detail #tastemoney').html(parseFloat($('.subtaste .tastecontent #detail #tastemoney').html())-(Number($('.subtaste .tastecontent #detail .switch:eq('+index+') input[name="money[]"]').val())*Number($('.subtaste .tastecontent #detail .switch:eq('+index+') input[name="tastenumber[]"]').val())));
						$('.subtaste .tastecontent #detail .switch:eq('+index+') span[data-id="tastenumber"]').html('');
						$('.subtaste .tastecontent #detail .switch:eq('+index+') span[data-id="tastenumber"]').css({'display':'none'});
						$('.subtaste .tastecontent #detail .switch input[name="tastenumber[]"]:eq('+index+')').val('1');
					}
				}
				else{
					if($('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked')){
						$('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked',false);
						$('.subtaste .tastecontent #detail .switch input[name="tastenumber[]"]:eq('+index+')').prop('checked',false);
					}
					else{
						$('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]:eq('+index+')').prop('checked',true);
						$('.subtaste .tastecontent #detail .switch input[name="tastenumber[]"]:eq('+index+')').prop('checked',true);
					}
				}
			},
			error:function(e){
				//console.log(e);
			}
		});
		compsubtaste();
	});
	$('.subtaste .tastecontent').on('click','.tastenumberbox .difftaste',function(){//2021/7/6 遞減備註選項數量
		if($('.subtaste .tastecontent .tastenumberbox .numberinput').val()>1){
			$('.subtaste .tastecontent .tastenumberbox .numberinput').val(parseInt($('.subtaste .tastecontent .tastenumberbox .numberinput').val())-1);
			$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') input[name="tastenumber[]"]').val(parseInt($('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') input[name="tastenumber[]"]').val())-1);
			$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())-1);
			$('.subtaste .tastecontent #detail #tastemoney').html(parseFloat($('.subtaste .tastecontent #detail #tastemoney').html())-Number($('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') input[name="money[]"]').val()));
			if($('.subtaste .tastecontent .tastenumberbox .numberinput').val()>1){
				$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').css({'display':'contents'});
				$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').html('*'+$('.subtaste .tastecontent .tastenumberbox .numberinput').val());
			}
			else{
				$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').css({'display':'none'});
				$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').html('');
			}
			compsubtaste();
		}
		else{
		}
	});
	$('.subtaste .tastecontent').on('click','.tastenumberbox .addtaste',function(){//2021/7/6 遞增備註選項數量
		if($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()=='-1'||$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()>$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val()){
			$('.subtaste .tastecontent .tastenumberbox .numberinput').val(parseInt($('.subtaste .tastecontent .tastenumberbox .numberinput').val())+1);
			$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') input[name="tastenumber[]"]').val(parseInt($('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') input[name="tastenumber[]"]').val())+1);
			$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val(parseInt($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())+1);
			$('.subtaste .tastecontent #detail #tastemoney').html(parseFloat($('.subtaste .tastecontent #detail #tastemoney').html())+Number($('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') input[name="money[]"]').val()));
			if($('.subtaste .tastecontent .tastenumberbox .numberinput').val()>1){
				$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').css({'display':'contents'});
				$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') span[data-id="tastenumber"]').html('*'+$('.subtaste .tastecontent .tastenumberbox .numberinput').val());
			}
			else{
			}
			if(($('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastelimit[]"]').val()==$('.subtaste .tastecontent #detail .'+$('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+')').parents('tr[id^="tastelabel"]').prop('id')+' input[name="tastesumnumber[]"]').val())||($('.subtaste .tastecontent #detail .switch:eq('+$('.subtaste .tastecontent .tastenumberbox .tasteindex').val()+') input[name="maxlimit[]"]').val()==$('.subtaste .tastecontent .tastenumberbox .numberinput').val())){
				$('.subtaste .tastenumberbox').css({'display':'none'});
			}
			else{
			}
			compsubtaste();
		}
		else{
		}
	});
	$('.subtaste .tastefunbox .save').click(function(){
		var requiredtaste=false;
		var requiredtastetypename='';
		if($('.subtaste .tastecontent .itemdata input[name="mobilerequired[]"]').length>0){
			for(var i=0;i<$('.subtaste .tastecontent .itemdata input[name="mobilerequired[]"]').length;i++){
				if(parseInt($('.subtaste .tastecontent .itemdata input[name="mobilerequired[]"]:eq('+i+')').val())>=1&&parseInt($('.subtaste .tastecontent .itemdata input[name="mobilerequired[]"]:eq('+i+')').val())>parseInt($('.subtaste .tastecontent .itemdata input[name="tastesumnumber[]"]:eq('+i+')').val())){
					requiredtaste=true;//2021/7/6 尚有必選備註為勾選完成
					if(requiredtastetypename!=''){
						requiredtastetypename += '、'+$('.subtaste .tastecontent .itemdata span[data-id="tastetypename"]:eq('+i+')').html();
					}
					else{
						requiredtastetypename=$('.subtaste .tastecontent .itemdata span[data-id="tastetypename"]:eq('+i+')').html();
					}
				}
				else{
				}
			}
		}
		else{
		}
		//console.log(requiredtaste);
		if(requiredtaste){//2021/7/6 判斷必選備註是否勾選完成
			$('.message1 .window').val('');
			$.ajax({
				url:'./lib/js/getinterface.name.php',
				method:'post',
				async:false,
				data:{'len':$('.basic input[name="language"]').val(),'para':'requiredtaste'},
				dataType:'json',
				success:function(d){
					$('.message1 #text').html(requiredtastetypename+d['name']);
				},
				error:function(e){
					//console.log(e);
				}
			});
			//$('.message1 #text').html('暫無選購紀錄。');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else{
			$('.subtaste .tastetitle').html('');
			
			var money=$('.subtaste .tastecontent #detail #money option:selected').val().split(';');

			if($('.subtaste .trid').val().substr(0,9)=='choseitem'){
				var target='subvar';
				var moneytarget='subvaritemmoney'+$('.subtaste .trid').val().substr(9);
			}
			else{
				var target='subfix';
				var moneytarget='subfixitemmoney'+$('.subtaste .trid').val().substr(9);
			}
			
			if($('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'no[]"]').prop('checked')){
				$('.detail #data #detail #totalsubmoney').html(parseInt($('.detail #data #detail #totalsubmoney').html())-parseInt($('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val()));
			}
			else{
			}

			$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'mname1[]"]').val(money[0]);
			$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'unitprice[]"]').val(money[1]);

			var tasteno='';
			var tastename='';
			var tasteprice='';
			var tastenumber='';
			for(var i=0;i<$('.subtaste .tastecontent #detail .switch input[name="tasteno[]"]').length;i++){
				if($('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="tasteno[]"]').prop('checked')==true){
					if(tasteno==''){
						tasteno=$('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
						tastename=$('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="tastename[]"]').val();
						tasteprice=$('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="money[]"]').val();
						tastenumber=$('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
					}
					else{
						tasteno=tasteno+','+$('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
						tastename=tastename+','+$('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="tastename[]"]').val();
						tasteprice=tasteprice+','+$('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="money[]"]').val();
						tastenumber=tastenumber+','+$('.subtaste .tastecontent #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();;
					}
				}
				else{
				}
			}

			//2021/7/6 開放備註
			if($('.subtaste .tastecontent #detail textarea[name="othertaste"]').length>0&&$('.subtaste .tastecontent #detail textarea[name="othertaste"]').val()!=''){
				//console.log($('.subtaste .tastecontent #detail textarea[name="othertaste"]').val());
				if(tasteno==''){
					tasteno='99999';
					tastename=$('.subtaste .tastecontent #detail textarea[name="othertaste"]').val();
					tasteprice='0';
					tastenumber='1';
				}
				else{
					tasteno=tasteno+',99999';
					tastename=tastename+','+$('.subtaste .tastecontent #detail textarea[name="othertaste"]').val();
					tasteprice=tasteprice+',0';
					tastenumber=tastenumber+',1';
				}
			}
			else{
			}
			
			$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'taste1[]"]').val(tasteno);//使用的加料備註代號(陣列字串)
			$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'taste1name[]"]').val(tastename);//使用的加料備註名稱(陣列字串)
			$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'taste1price[]"]').val(tasteprice);//使用的加料備註單價(陣列字串)
			$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'taste1number[]"]').val(tastenumber);//使用的加料備註數量(陣列字串)
			$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'taste1money[]"]').val($('.subtaste .tastecontent #detail #tastemoney').html());//使用的加料備註小計
			$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val((parseFloat($('.subtaste .tastecontent #detail #tastemoney').html())+parseFloat(money[1])));//1個(含加料備註)的金額
			//2021/7/6 修改套餐品項顯示的金額(包含備註)
			if(target=='subfix'){
				$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') div[class^="subfixitemmoney"]:eq('+(parseInt($('.subtaste .seq').val())-1)+')').html('('+$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val()+')');
				if($('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val()>0){
					$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') div[class^="subfixitemmoney"]:eq('+(parseInt($('.subtaste .seq').val())-1)+')').css({'display':'block'});
				}
				else{
					$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') div[class^="subfixitemmoney"]:eq('+(parseInt($('.subtaste .seq').val())-1)+')').css({'display':'none'});
				}
			}
			else{
				$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') div[class^="subvaritemmoney"]:eq('+(parseInt($('.subtaste .seq').val())-1)+')').html('('+$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val+')');
				if($('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val()>0){
					$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') div[class^="subvaritemmoney"]:eq('+(parseInt($('.subtaste .seq').val())-1)+')').css({'display':'block'});
				}
				else{
					$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') div[class^="subvaritemmoney"]:eq('+(parseInt($('.subtaste .seq').val())-1)+')').css({'display':'none'});
				}
			}

			$('.subtaste .tastecontent').html('');
			//$('.subtaste').css({'display':'none'});
			$('.subtaste').animate({
				top:'100%'
			},500,function(){
				$('.subtaste').css({'display':'none'});
			});
			
			if($('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'no[]"]').prop('checked')){
				$('.detail #data #detail #totalsubmoney').html(parseInt($('.detail #data #detail #totalsubmoney').html())+parseInt($('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val()));
			}
			else{
			}

			if(tastenumber.length>0){//如有備註，強調按鈕
				$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .'+$('.subtaste .trid').val()+$('.subtaste .seq').val()+'#taste').css({'color':'#ff0000','font-weight':'bold'});
				$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .'+moneytarget+$('.subtaste .seq').val()).html('('+$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val()+')');
			}
			else{
				$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .'+$('.subtaste .trid').val()+$('.subtaste .seq').val()+'#taste').css({'color':'#898989','font-weight':'normal'});
				$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .'+moneytarget+$('.subtaste .seq').val()).html('('+$('.detail #data #detail #'+$('.subtaste .trid').val()+':eq('+(parseInt($('.subtaste .seq').val())-1)+') .switch input[name="'+target+'money[]"]').val()+')');
			}

			compsub();
		}
	});
	$('.subtaste .cancel').click(function(){//2021/7/6 新增套餐品項備註"取消"按鈕
		$('.subtaste .tastetitle').html('');
		$('.subtaste .tastecontent').html('');
		//$('.subtaste').css({'display':'none'});
		$('.subtaste').animate({
			top:'100%'
		},500,function(){
			$('.subtaste').css({'display':'none'});
		});
	});
	$('.detail #footer .send').on('click',function(){
		var chosetotal=false;
		var chosename='';
		var requiredtaste=false;
		var requiredtastetypename='';
		var subrequiredtaste=false;
		var subrequiredtempname={};
		var subrequiredtastetypename='';
		if($('.detail #data .itemdata .choseitem').length>0){
			for(var i=0;i<$('.detail #data .itemdata .choseitem').length;i++){
				//2021/6/22
				if($('.detail #data .itemdata .choseitem:eq('+i+') input[name="required"]').val()=='1'&&parseInt($('.detail #data .itemdata .choseitem:eq('+i+') input[name="chosenumber"]').val())>$('.detail #data .itemdata #choseitem'+$('.detail #data .itemdata .choseitem:eq('+i+')').prop('id')+' input[type="checkbox"]:checked').length){
					chosetotal=true;
					if(chosename!=''){
						chosename += '、';
					}
					else{
					}
					chosename += $('.detail #data .itemdata .choseitem:eq('+i+') span[data-id="subvartypename"]').html();
				}
				else{
					//chosetotal=parseInt(chosetotal)+parseInt($('.detail #data .itemdata .choseitem:eq('+i+') input[name="chosenumber"]').val());
				}
			}
		}
		else{
		}
		if($('.detail #data .itemdata input[name="mobilerequired[]"]').length>0){
			for(var i=0;i<$('.detail #data .itemdata input[name="mobilerequired[]"]').length;i++){
				if(parseInt($('.detail #data .itemdata input[name="mobilerequired[]"]:eq('+i+')').val())>=1&&parseInt($('.detail #data .itemdata input[name="mobilerequired[]"]:eq('+i+')').val())>parseInt($('.detail #data .itemdata input[name="tastesumnumber[]"]:eq('+i+')').val())){
					requiredtaste=true;//2021/6/11 尚有必選備註為勾選完成
					if(requiredtastetypename!=''){
						requiredtastetypename += '、'+$('.detail #data .itemdata span[data-id="tastetypename"]:eq('+i+')').html();
					}
					else{
						requiredtastetypename=$('.detail #data .itemdata span[data-id="tastetypename"]:eq('+i+')').html();
					}
				}
				else{
				}
			}
		}
		else{
		}
		if($('.detail #data .itemdata input[name="subvarrequired[]"]').length>0){
			for(var i=0;i<$('.detail #data .itemdata input[name="subvarrequired[]"]').length;i++){
				if($('.detail #data .itemdata input[name="subvarrequired[]"]:eq('+i+')').val()==1&&$('.detail #data .itemdata input[name="subvarno[]"]:eq('+i+')').prop('checked')==true&&$('.detail #data .itemdata input[name="subvartaste1[]"]:eq('+i+')').val()==''){
					subrequiredtaste=true;//2021/6/11 尚有必選備註為勾選完成
					if(typeof subrequiredtempname['s'+$('.detail #data .itemdata input[name="subvarrequired[]"]:eq('+i+')').parents('tr[id^="choseitem"]').prop('id').substr(9)]==='undefined'){
						subrequiredtempname['s'+$('.detail #data .itemdata input[name="subvarrequired[]"]:eq('+i+')').parents('tr[id^="choseitem"]').prop('id').substr(9)]=[];
						subrequiredtempname['s'+$('.detail #data .itemdata input[name="subvarrequired[]"]:eq('+i+')').parents('tr[id^="choseitem"]').prop('id').substr(9)]['name']=$('.detail #data .itemdata .choseitem#'+$('.detail #data .itemdata input[name="subvarrequired[]"]:eq('+i+')').parents('tr[id^="choseitem"]').prop('id').substr(9)+' span[data-id="subvartypename"]').html();
					}
					else{
					}
					subrequiredtempname['s'+$('.detail #data .itemdata input[name="subvarrequired[]"]:eq('+i+')').parents('tr[id^="choseitem"]').prop('id').substr(9)][subrequiredtempname['s'+$('.detail #data .itemdata input[name="subvarrequired[]"]:eq('+i+')').parents('tr[id^="choseitem"]').prop('id').substr(9)].length]=$('.detail #data .itemdata input[name="subvarname[]"]:eq('+i+')').val();
					/*if(subrequiredtastetypename!=''){
						subrequiredtastetypename += '、';
					}
					else{
					}
					subrequiredtastetypename += $('.detail #data .itemdata .choseitem#'+$('.detail #data .itemdata input[name="subvarname[]"]:eq('+i+')').parents('tr[id^="choseitem"]').prop('id').substr(9)+' span[data-id="subvartypename"]').html();
					subrequiredtastetypename += $('.detail #data .itemdata input[name="subvarname[]"]:eq('+i+')').val();*/
				}
				else{
				}
			}
		}
		else{
		}
		if($('.detail #data .itemdata input[name="subfixrequired[]"]').length>0){
			for(var i=0;i<$('.detail #data .itemdata input[name="subfixrequired[]"]').length;i++){
				if($('.detail #data .itemdata input[name="subfixrequired[]"]:eq('+i+')').val()==1&&$('.detail #data .itemdata input[name="subfixno[]"]:eq('+i+')').prop('checked')==true&&$('.detail #data .itemdata input[name="subfixtaste1[]"]:eq('+i+')').val()==''){
					subrequiredtaste=true;//2021/6/11 尚有必選備註為勾選完成
					if(typeof subrequiredtempname['ssubfix']==='undefined'){
						subrequiredtempname['ssubfix']=[];
						subrequiredtempname['ssubfix']['name']=$('.detail #data .itemdata .nochoseitem span[data-id="subfixtypename"]').html();
					}
					else{
					}
					subrequiredtempname['ssubfix'][subrequiredtempname['ssubfix'].length]=$('.detail #data .itemdata input[name="subfixname[]"]:eq('+i+')').val();
					/*if(subrequiredtastetypename!=''){
						subrequiredtastetypename += '、';
					}
					else{
					}
					subrequiredtastetypename += $('.detail #data .itemdata .choseitem#'+$('.detail #data .itemdata input[name="subvarname[]"]:eq('+i+')').parents('tr[id^="nochoseitem"]').prop('id').substr(9)+' span[data-id="subvartypename"]').html();
					subrequiredtastetypename += $('.detail #data .itemdata input[name="subvarname[]"]:eq('+i+')').val();*/
				}
				else{
				}
			}
		}
		else{
		}
		if(requiredtaste){//2021/6/11 判斷必選備註是否勾選完成
			$('.message1 .window').val('');
			$('.message1 #text').html(requiredtastetypename+'備註未完成。');
			//$('.message1 #text').html('暫無選購紀錄。');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else if(subrequiredtaste){//2021/6/17 判斷套餐品項必選備註是否勾選完成
			$('.message1 .window').val('');
			$.each(subrequiredtempname,function(k,v){
				if(subrequiredtastetypename!=''){
					subrequiredtastetypename += '<br>';
				}
				else{
				}
				subrequiredtastetypename += '<strong>'+v['name']+'</strong>:';
				for(var i=0;i<v.length;i++){
					if(i>0){
						subrequiredtastetypename += '、';
					}
					else{
					}
					subrequiredtastetypename += v[i];
				}
				subrequiredtastetypename += ';';
			});
			$('.message1 #text').html('以下品項必選備註未選擇。<br>'+subrequiredtastetypename);
			//$('.message1 #text').html('暫無選購紀錄。');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else if(chosetotal){//2021/6/22 //2020/8/19 判斷套餐品項是否勾選完成
			$('.message1 .window').val('');
			$('.message1 #text').html('以下套餐必選類別未完成。<br>'+chosename);
			//$('.message1 #text').html('暫無選購紀錄。');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else{
			if($('.detail #data .itemdata input[name="itemseq"]').length>0){
				var money=$('.detail #data #detail #money option:selected').val().split(';');
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="mname1[]"]').val(money[0]);
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="unitprice[]"]').val(money[1]);
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="money[]"]').val((parseFloat($('.detail #data #detail #tastemoney').html())+parseFloat(money[1])));
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="number[]"]').val($('.detail #data #detail input[name="qty"]').val());
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="subtotal[]"]').val((parseFloat((parseFloat($('.detail #data #detail #tastemoney').html())+parseFloat(money[1])))*parseFloat($('.detail #data #detail input[name="qty"]').val())));

				var tasteno='';
				var tastename='';
				var tasteprice='';
				var tastenumber='';
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tasteno[]"]').val('');
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tastename[]"]').val('');
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tasteprice[]"]').val('');
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tastenumber[]"]').val('');
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tastemoney[]"]').val('');
				for(var i=0;i<$('.detail #data #detail .switch input[name="tasteno[]"]').length;i++){
					if($('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').prop('checked')==true){
						if(tasteno==''){
							tasteno=$('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
							tastename=$('.detail #data #detail .switch:eq('+i+') input[name="tastename[]"]').val();
							tasteprice=$('.detail #data #detail .switch:eq('+i+') input[name="money[]"]').val();
							if(typeof $('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]')!=='undefined'){
								tastenumber=$('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
								if($('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val()>1){
									tastename=tastename+'*'+$('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
								}
								else{
								}
							}
							else{
								tastenumber='1';
							}
						}
						else{
							tasteno=tasteno+','+$('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
							tastename=tastename+','+$('.detail #data #detail .switch:eq('+i+') input[name="tastename[]"]').val();
							tasteprice=tasteprice+','+$('.detail #data #detail .switch:eq('+i+') input[name="money[]"]').val();
							if(typeof $('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]')!=='undefined'){
								tastenumber=tastenumber+','+$('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
								if($('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val()>1){
									tastename=tastename+'*'+$('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
								}
								else{
								}
							}
							else{
								tastenumber=tastenumber+',1';
							}
						}
					}
					else{
					}
				}

				//2021/7/6 自訂備註，先不處理
				/*if($('.detail #data #detail textarea[name="othertaste"]').length>0&&$('.detail #data #detail textarea[name="othertaste"]').val()!=''){
					//console.log($('.detail #data #detail textarea[name="othertaste"]').val());
					if(tasteno==''){
						tasteno='99999';
						tastename=$('.detail #data #detail textarea[name="othertaste"]').val();
						tasteprice='0';
						tastenumber='1';
					}
					else{
						tasteno=tasteno+',99999';
						tastename=tastename+','+$('.detail #data #detail textarea[name="othertaste"]').val();
						tasteprice=tasteprice+',0';
						tastenumber=tastenumber+',1';
					}
				}
				else{
				}*/

				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1[]"]').val(tasteno);
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1name[]"]').val(tastename);
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1price[]"]').val(tasteprice);
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1number[]"]').val(tastenumber);
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1money[]"]').val($('.detail #data #detail #tastemoney').html());
				
				//2020/5/19 贈與點數規則
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="getpointtype[]"]').val($('.detail #data #detail input[name="getpointtype"]').val());
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="initgetpoint[]"]').val($('.detail #data #detail input[name="initgetpoint"]').val());
				$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="getpoint[]"]').val($('.detail #data #detail input[name="getpoint"]').val());
				
				var itemtimes=1;//2020/8/18 品項數量遞增數
				if($('.detail #data #detail input[name="subfixno[]"]').length>0){//2020/8/18 跟隨品項
					for(var i=0;i<$('.detail #data #detail input[name="subfixno[]"]').length;i++,itemtimes++){
						var itemseq=(parseInt($('.detail #data .itemdata input[name="itemseq"]').val())+parseInt(itemtimes));

						$('.items .'+itemseq+'#item input[name="typeno[]"]').val($('.detail #data #detail input[name="subfixtypeno[]"]:eq('+i+')').val());//類別代號
						$('.items .'+itemseq+'#item input[name="no[]"]').val($('.detail #data #detail input[name="subfixno[]"]:eq('+i+')').val());//產品代號
						$('.items .'+itemseq+'#item input[name="personcount[]"]').val($('.detail #data #detail input[name="subfixpersoncount[]"]:eq('+i+')').val());//人數勾機(補充POS端變數)
						$('.items .'+itemseq+'#item input[name="needcharge[]"]').val($('.detail #data #detail input[name="subfixneedcharge[]"]:eq('+i+')').val());//計算服務費(補充POS端變數)
						$('.items .'+itemseq+'#item input[name="name[]"]').val($('.detail #data #detail input[name="subfixname[]"]:eq('+i+')').val());//產品名稱
						$('.items .'+itemseq+'#item input[name="name2[]"]').val($('.detail #data #detail input[name="subfixname2[]"]:eq('+i+')').val());//產品第二名稱(補充POS端變數)
						$('.items .'+itemseq+'#item input[name="isgroup[]"]').val($('.detail #data #detail input[name="subfixisgroup[]"]:eq('+i+')').val());//是否為套餐(補充POS端變數)
						$('.items .'+itemseq+'#item input[name="childtype[]"]').val($('.detail #data #detail input[name="subfixchildtype[]"]:eq('+i+')').val());//是否為套餐選項(補充POS端變數)
						$('.items .'+itemseq+'#item input[name="mname1[]"]').val($('.detail #data #detail input[name="subfixmname1[]"]:eq('+i+')').val());//價格名稱
						$('.items .'+itemseq+'#item input[name="insaleinv[]"]').val($('.detail #data #detail input[name="subfixinsaleinv[]"]:eq('+i+')').val());//計算發票金額(補充POS端變數)
						$('.items .'+itemseq+'#item input[name="unitprice[]"]').val($('.detail #data #detail input[name="subfixunitprice[]"]:eq('+i+')').val());//單價
						$('.items .'+itemseq+'#item input[name="taste1[]"]').val($('.detail #data #detail input[name="subfixtaste1[]"]:eq('+i+')').val());//使用的加料備註代號(陣列字串)
						$('.items .'+itemseq+'#item input[name="taste1name[]"]').val($('.detail #data #detail input[name="subfixtaste1name[]"]:eq('+i+')').val());//使用的加料備註名稱(陣列字串)
						$('.items .'+itemseq+'#item input[name="taste1price[]"]').val($('.detail #data #detail input[name="subfixtaste1price[]"]:eq('+i+')').val());//使用的加料備註單價(陣列字串)
						$('.items .'+itemseq+'#item input[name="taste1number[]"]').val($('.detail #data #detail input[name="subfixtaste1number[]"]:eq('+i+')').val());//使用的加料備註數量(陣列字串)
						$('.items .'+itemseq+'#item input[name="taste1money[]"]').val($('.detail #data #detail input[name="subfixtaste1money[]"]:eq('+i+')').val());//使用的加料備註小計
						$('.items .'+itemseq+'#item input[name="money[]"]').val((parseFloat($('.detail #data #detail input[name="subfixtaste1money[]"]:eq('+i+')').val())+parseFloat($('.detail #data #detail input[name="subfixunitprice[]"]:eq('+i+')').val())));//1個(含加料備註)的金額
						$('.items .'+itemseq+'#item input[name="dis1[]"]').val($('.detail #data #detail input[name="subfixdis1[]"]:eq('+i+')').val());
						$('.items .'+itemseq+'#item input[name="dis2[]"]').val($('.detail #data #detail input[name="subfixdis2[]"]:eq('+i+')').val());
						$('.items .'+itemseq+'#item input[name="dis3[]"]').val($('.detail #data #detail input[name="subfixdis3[]"]:eq('+i+')').val());
						$('.items .'+itemseq+'#item input[name="dis4[]"]').val($('.detail #data #detail input[name="subfixdis4[]"]:eq('+i+')').val());
						
						//2020/5/19 贈與點數規則
						$('.items .'+itemseq+'#item input[name="getpointtype[]"]').val($('.detail #data #detail input[name="subfixgetpointtype[]"]:eq('+i+')').val());
						$('.items .'+itemseq+'#item input[name="initgetpoint[]"]').val($('.detail #data #detail input[name="subfixinitgetpoint[]"]:eq('+i+')').val());
						$('.items .'+itemseq+'#item input[name="getpoint[]"]').val($('.detail #data #detail input[name="subfixgetpoint[]"]:eq('+i+')').val());
														
						$('.items .'+itemseq+'#item input[name="number[]"]').val($('.detail #data #detail input[name="qty"]').val());//數量
						$('.items .'+itemseq+'#item input[name="subtotal[]"]').val((parseFloat((parseFloat($('.detail #data #detail input[name="subfixtaste1money[]"]:eq('+i+')').val())+parseFloat($('.detail #data #detail input[name="subfixunitprice[]"]:eq('+i+')').val())))*parseFloat($('.detail #data #detail input[name="qty"]').val())));//小計
					}
				}
				else{
				}
				if($('.detail #data #detail input[name="subvarno[]"]').length>0){//2020/8/18 套餐品項
					for(var i=0;i<$('.detail #data #detail input[name="subvarno[]"]').length;i++){
						if(!$('.detail #data #detail input[name="subvarno[]"]:eq('+i+')').prop('checked')){
						}
						else{
							var itemseq=(parseInt($('.detail #data .itemdata input[name="itemseq"]').val())+parseInt(itemtimes));

							$('.items .'+itemseq+'#item input[name="typeno[]"]').val($('.detail #data #detail input[name="subvartypeno[]"]:eq('+i+')').val());//類別代號
							$('.items .'+itemseq+'#item input[name="no[]"]').val($('.detail #data #detail input[name="subvarno[]"]:eq('+i+')').val());//產品代號
							$('.items .'+itemseq+'#item input[name="personcount[]"]').val($('.detail #data #detail input[name="subvarpersoncount[]"]:eq('+i+')').val());//人數勾機(補充POS端變數)
							$('.items .'+itemseq+'#item input[name="needcharge[]"]').val($('.detail #data #detail input[name="subvarneedcharge[]"]:eq('+i+')').val());//計算服務費(補充POS端變數)
							$('.items .'+itemseq+'#item input[name="name[]"]').val($('.detail #data #detail input[name="subvarname[]"]:eq('+i+')').val());//產品名稱
							$('.items .'+itemseq+'#item input[name="name2[]"]').val($('.detail #data #detail input[name="subvarname2[]"]:eq('+i+')').val());//產品第二名稱(補充POS端變數)
							$('.items .'+itemseq+'#item input[name="isgroup[]"]').val($('.detail #data #detail input[name="subvarisgroup[]"]:eq('+i+')').val());//是否為套餐(補充POS端變數)
							$('.items .'+itemseq+'#item input[name="childtype[]"]').val($('.detail #data #detail input[name="subvarchildtype[]"]:eq('+i+')').val());//是否為套餐選項(補充POS端變數)
							$('.items .'+itemseq+'#item input[name="mname1[]"]').val($('.detail #data #detail input[name="subvarmname1[]"]:eq('+i+')').val());//價格名稱
							$('.items .'+itemseq+'#item input[name="insaleinv[]"]').val($('.detail #data #detail input[name="subvarinsaleinv[]"]:eq('+i+')').val());//計算發票金額(補充POS端變數)
							$('.items .'+itemseq+'#item input[name="unitprice[]"]').val($('.detail #data #detail input[name="subvarunitprice[]"]:eq('+i+')').val());//單價
							$('.items .'+itemseq+'#item input[name="taste1[]"]').val($('.detail #data #detail input[name="subvartaste1[]"]:eq('+i+')').val());//使用的加料備註代號(陣列字串)
							$('.items .'+itemseq+'#item input[name="taste1name[]"]').val($('.detail #data #detail input[name="subvartaste1name[]"]:eq('+i+')').val());//使用的加料備註名稱(陣列字串)
							$('.items .'+itemseq+'#item input[name="taste1price[]"]').val($('.detail #data #detail input[name="subvartaste1price[]"]:eq('+i+')').val());//使用的加料備註單價(陣列字串)
							$('.items .'+itemseq+'#item input[name="taste1number[]"]').val($('.detail #data #detail input[name="subvartaste1number[]"]:eq('+i+')').val());//使用的加料備註數量(陣列字串)
							$('.items .'+itemseq+'#item input[name="taste1money[]"]').val($('.detail #data #detail input[name="subvartaste1money[]"]:eq('+i+')').val());//使用的加料備註小計
							$('.items .'+itemseq+'#item input[name="money[]"]').val((parseFloat($('.detail #data #detail input[name="subvartaste1money[]"]:eq('+i+')').val())+parseFloat($('.detail #data #detail input[name="subvarunitprice[]"]:eq('+i+')').val())));//1個(含加料備註)的金額
							$('.items .'+itemseq+'#item input[name="dis1[]"]').val($('.detail #data #detail input[name="subvardis1[]"]:eq('+i+')').val());
							$('.items .'+itemseq+'#item input[name="dis2[]"]').val($('.detail #data #detail input[name="subvardis2[]"]:eq('+i+')').val());
							$('.items .'+itemseq+'#item input[name="dis3[]"]').val($('.detail #data #detail input[name="subvardis3[]"]:eq('+i+')').val());
							$('.items .'+itemseq+'#item input[name="dis4[]"]').val($('.detail #data #detail input[name="subvardis4[]"]:eq('+i+')').val());
							
							//2020/5/19 贈與點數規則
							$('.items .'+itemseq+'#item input[name="getpointtype[]"]').val($('.detail #data #detail input[name="subvargetpointtype[]"]:eq('+i+')').val());
							$('.items .'+itemseq+'#item input[name="initgetpoint[]"]').val($('.detail #data #detail input[name="subvarinitgetpoint[]"]:eq('+i+')').val());
							$('.items .'+itemseq+'#item input[name="getpoint[]"]').val($('.detail #data #detail input[name="subvargetpoint[]"]:eq('+i+')').val());
															
							$('.items .'+itemseq+'#item input[name="number[]"]').val($('.detail #data #detail input[name="qty"]').val());//數量
							$('.items .'+itemseq+'#item input[name="subtotal[]"]').val((parseFloat((parseFloat($('.detail #data #detail input[name="subvartaste1money[]"]:eq('+i+')').val())+parseFloat($('.detail #data #detail input[name="subvarunitprice[]"]:eq('+i+')').val())))*parseFloat($('.detail #data #detail input[name="qty"]').val())));//小計
							itemtimes++;
						}
					}
				}
				else{
				}


				$('#title .return').trigger('click');
				/*if($('.items #item').length==0){
					$('#keybox .funkey2 #point').css({'border-top':'','border-bottom':'','border-left':'','border-bottom-left-radius':'','border-top-left-radius':''});
					$('#keybox .funkey2 #point').html('');
				}
				else{
					$('#keybox .funkey2 #point').css({'border-top':'2px solid rgba(26, 26, 26, 0.5)','border-bottom':'2px solid rgba(26, 26, 26, 0.5)','border-left':'2px solid rgba(26, 26, 26, 0.5)','border-bottom-left-radius':'10px','border-top-left-radius':'10px'});
					$('#keybox .funkey2 #point').html('x'+comptotalqty()+$('.basic .unit').val()+'<br>'+$('.basic .moneypreunit').val()+comptotal()+$('.basic .moneysufunit').val());
				}*/
				if($('.items #item').length==0){
					/*$('#keybox .funkey2 #point').css({'border-top':'','border-bottom':'','border-left':'','border-bottom-left-radius':'','border-top-left-radius':''});*/
					$('#keybox .funkey2 #point').html('');
					$('#setup input[name="total"]').val('0');
					$('#setup input[name="totalnumber"]').val('0');
				}
				else{
					/*$('#keybox .funkey2 #point').css({'border-top':'2px solid rgba(26, 26, 26, 0.5)','border-bottom':'2px solid rgba(26, 26, 26, 0.5)','border-left':'2px solid rgba(26, 26, 26, 0.5)','border-bottom-left-radius':'10px','border-top-left-radius':'10px'});*/
					$('#keybox .funkey2 #point').html('x'+comptotalqty()+$('.basic .unit').val()+'<br>'+$('.basic .moneypreunit').val()+comptotal()+$('.basic .moneysufunit').val());
					$('#setup input[name="total"]').val(comptotal());
					$('#setup input[name="totalnumber"]').val(comptotalqty());
				}
				$('#keybox .funkey2 div:eq(0)').prop('id','list');
				$('#keybox .funkey2 #list').trigger('click');
			}
			else{
				var tempres='';
				if($('.items div#item').length==0){
					tempres=tempres+'<div class="1" id="item">';
				}
				else{
					tempres=tempres+'<div class="'+(parseInt($('.items div#item:eq('+($('.items div#item').length-1)+')').attr('class'))+1)+'" id="item">';
				}
				tempres=tempres+"<input type='hidden' name='linenumber[]' value='";
				if($('.items div#item').length==0){
					tempres=tempres+"1";
				}
				else{
					tempres=tempres+(parseInt($('.items div#item:eq('+($('.items div#item').length-1)+') input[name="linenumber[]"]').val())+2);
				}
				tempres=tempres+"'>";//資料庫順序(補充POS端變數)
				tempres=tempres+"<input type='hidden' name='order[]' value='";
				if($('.items div#item').length==0){
					tempres=tempres+"1";
				}
				else{
					tempres=tempres+(parseInt($('.items div#item:eq('+($('.items div#item').length-1)+')').attr('class'))+1);
				}
				tempres=tempres+"'>";//點餐列表順序(補充POS端變數)
				tempres=tempres+"<input type='hidden' name='typeno[]' value='"+$('.detail #data #detail input[name="typeno"]').val()+"'>";//類別代號
				tempres=tempres+"<input type='hidden' name='type[]' value='"+$('.detail #data #detail input[name="type"]').val()+"'>";//類別名稱(補充POS端變數，故意留空)
				tempres=tempres+"<input type='hidden' name='no[]' value='"+$('.detail #data #detail input[name="no"]').val()+"'>";//產品代號
				tempres=tempres+"<input type='hidden' name='personcount[]' value='"+$('.detail #data #detail input[name="personcount"]').val()+"'>";//人數勾機(補充POS端變數)
				tempres=tempres+"<input type='hidden' name='needcharge[]' value='"+$('.detail #data #detail input[name="needcharge"]').val()+"'>";//計算服務費(補充POS端變數)
				tempres=tempres+"<input type='hidden' name='seq[]' value='"+$('.detail #data #detail input[name="seq"]').val()+"'>";//產品排序
				tempres=tempres+"<input type='hidden' name='name[]' value='"+$('.detail #data #detail input[name="name"]').val()+"'>";//產品名稱
				tempres=tempres+"<input type='hidden' name='name2[]' value='"+$('.detail #data #detail input[name="name2"]').val()+"'>";//產品第二名稱(補充POS端變數)
				tempres=tempres+"<input type='hidden' name='isgroup[]' value='"+$('.detail #data #detail input[name="isgroup"]').val()+"'>";//是否為套餐(補充POS端變數)
				tempres=tempres+"<input type='hidden' name='childtype[]' value='"+$('.detail #data #detail input[name="childtype"]').val()+"'>";//是否為套餐選項(補充POS端變數)
				var money=$('.detail #data #detail #money option:selected').val().split(';');
				tempres=tempres+"<input type='hidden' name='mname1[]' value='"+money[0]+"'>";//價格名稱
				tempres=tempres+"<input type='hidden' name='mname2[]' value=''>";//價格名稱2(補充POS端變數)
				tempres=tempres+"<input type='hidden' name='insaleinv[]' value='"+$('.detail #data #detail input[name="insaleinv"]').val()+"'>";//計算發票金額(補充POS端變數)
				tempres=tempres+"<input type='hidden' name='unitprice[]' value='"+money[1]+"'>";//單價
				var tasteno='';
				var tastename='';
				var tasteprice='';
				var tastenumber='';
				for(var i=0;i<$('.detail #data #detail .switch input[name="tasteno[]"]').length;i++){
					if($('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').prop('checked')==true){
						if(tasteno==''){
							tasteno=$('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
							tastename=$('.detail #data #detail .switch:eq('+i+') input[name="tastename[]"]').val();
							tasteprice=$('.detail #data #detail .switch:eq('+i+') input[name="money[]"]').val();
							if(typeof $('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]')!=='undefined'){
								tastenumber=$('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
								if($('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val()>1){
									tastename=tastename+'*'+$('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
								}
								else{
								}
							}
							else{
								tastenumber='1';
							}
						}
						else{
							tasteno=tasteno+','+$('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
							tastename=tastename+','+$('.detail #data #detail .switch:eq('+i+') input[name="tastename[]"]').val();
							tasteprice=tasteprice+','+$('.detail #data #detail .switch:eq('+i+') input[name="money[]"]').val();
							if(typeof $('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]')!=='undefined'){
								tastenumber=tastenumber+','+$('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
								if($('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val()>1){
									tastename=tastename+'*'+$('.detail #data #detail .switch:eq('+i+') input[name="tastenumber[]"]').val();
								}
								else{
								}
							}
							else{
								tastenumber=tastenumber+',1';
							}
						}
					}
					else{
					}
				}

				//2021/7/6 自訂備註，先不處理
				/*if($('.detail #data #detail textarea[name="othertaste"]').length>0&&$('.detail #data #detail textarea[name="othertaste"]').val()!=''){
					//console.log($('.detail #data #detail textarea[name="othertaste"]').val());
					if(tasteno==''){
						tasteno='99999';
						tastename=$('.detail #data #detail textarea[name="othertaste"]').val();
						tasteprice='0';
						tastenumber='1';
					}
					else{
						tasteno=tasteno+',99999';
						tastename=tastename+','+$('.detail #data #detail textarea[name="othertaste"]').val();
						tasteprice=tasteprice+',0';
						tastenumber=tastenumber+',1';
					}
				}
				else{
				}*/

				tempres=tempres+"<input type='hidden' name='taste1[]' value='"+tasteno+"'>";//使用的加料備註代號(陣列字串)
				tempres=tempres+"<input type='hidden' name='taste1name[]' value='"+tastename+"'>";//使用的加料備註名稱(陣列字串)
				tempres=tempres+"<input type='hidden' name='taste1price[]' value='"+tasteprice+"'>";//使用的加料備註單價(陣列字串)
				tempres=tempres+"<input type='hidden' name='taste1number[]' value='"+tastenumber+"'>";//使用的加料備註數量(陣列字串)
				tempres=tempres+"<input type='hidden' name='taste1money[]' value='"+$('.detail #data #detail #tastemoney').html()+"'>";//使用的加料備註小計
				tempres=tempres+"<input type='hidden' name='money[]' value='"+(parseFloat($('.detail #data #detail #tastemoney').html())+parseFloat(money[1]))+"'>";//1個(含加料備註)的金額
				tempres=tempres+"<input type='hidden' name='discount[]' value='0'>";//單品折扣金額(補充POS端變數，故意留空)
				tempres=tempres+"<input type='hidden' name='discontent[]' value=''>";//折扣方式(補充POS端變數，故意留空)
				tempres=tempres+"<input type='hidden' name='dis1[]' value='"+$('.detail #data #detail input[name="dis1"]').val()+"'>";
				tempres=tempres+"<input type='hidden' name='dis2[]' value='"+$('.detail #data #detail input[name="dis2"]').val()+"'>";
				tempres=tempres+"<input type='hidden' name='dis3[]' value='"+$('.detail #data #detail input[name="dis3"]').val()+"'>";
				tempres=tempres+"<input type='hidden' name='dis4[]' value='"+$('.detail #data #detail input[name="dis4"]').val()+"'>";
				
				//2020/5/19 贈與點數規則
				tempres=tempres+"<input type='hidden' name='getpointtype[]' value='"+$('.detail #data #detail input[name="getpointtype"]').val()+"'>";
				tempres=tempres+"<input type='hidden' name='initgetpoint[]' value='"+$('.detail #data #detail input[name="initgetpoint"]').val()+"'>";
				tempres=tempres+"<input type='hidden' name='getpoint[]' value='"+$('.detail #data #detail input[name="getpoint"]').val()+"'>";

				tempres=tempres+"<input type='hidden' name='number[]' value='"+$('.detail #data #detail input[name="qty"]').val()+"'>";//數量
				tempres=tempres+"<input type='hidden' name='subtotal[]' value='"+(parseFloat((parseFloat($('.detail #data #detail #tastemoney').html())+parseFloat(money[1])))*parseFloat($('.detail #data #detail input[name="qty"]').val()))+"'>";//小計
				//$('.items .'+rows).append("<input type='hidden' name='tasteno[]' value='"+tasteno+"'>");
				//$('.items .'+rows).append("<input type='hidden' name='tastename[]' value='"+tastename+"'>");
				//$('.items .'+rows).append("<input type='hidden' name='tasteprice[]' value='"+tasteprice+"'>");
				//$('.items .'+rows).append("<input type='hidden' name='tastenumber[]' value='"+tastenumber+"'>");
				//$('.items .'+rows).append("<input type='hidden' name='tastemoney[]' value='"+$('.detail #data #detail #tastemoney').html()+"'>");
				tempres=tempres+'</div>';
				
				var itemtimes=1;//2020/8/18 品項數量遞增數
				if($('.detail #data #detail input[name="subfixno[]"]').length>0){//2020/8/18 跟隨品項
					for(var i=0;i<$('.detail #data #detail input[name="subfixno[]"]').length;i++,itemtimes++){
						if($('.items div#item').length==0){
							tempres += '<div class="'+(1+parseInt(itemtimes))+'" id="item">';
						}
						else{
							tempres += '<div class="'+(parseInt($('.items div#item:eq('+($('.items div#item').length-1)+')').attr('class'))+1+parseInt(itemtimes))+'" id="item">';
						}
						tempres += "<input type='hidden' name='linenumber[]' value='";
						if($('.items div#item').length==0){
							tempres += (1+(parseInt(itemtimes)*2));
						}
						else{
							tempres += (parseInt($('.items div#item:eq('+($('.items div#item').length-1)+') input[name="linenumber[]"]').val())+((parseInt(itemtimes)+1)*2));
						}
						tempres += "'>";//資料庫順序(補充POS端變數)
						tempres += "<input type='hidden' name='order[]' value='－'>";//點餐列表順序(補充POS端變數)
						tempres += "<input type='hidden' name='typeno[]' value='"+$('.detail #data #detail input[name="subfixtypeno[]"]:eq('+i+')').val()+"'>";//類別代號
						tempres += "<input type='hidden' name='type[]' value='"+$('.detail #data #detail input[name="subfixtype[]"]:eq('+i+')').val()+"'>";//類別名稱(補充POS端變數，故意留空)
						tempres += "<input type='hidden' name='no[]' value='"+$('.detail #data #detail input[name="subfixno[]"]:eq('+i+')').val()+"'>";//產品代號
						tempres += "<input type='hidden' name='personcount[]' value='"+$('.detail #data #detail input[name="subfixpersoncount[]"]:eq('+i+')').val()+"'>";//人數勾機(補充POS端變數)
						tempres += "<input type='hidden' name='needcharge[]' value='"+$('.detail #data #detail input[name="subfixneedcharge[]"]:eq('+i+')').val()+"'>";//計算服務費(補充POS端變數)
						tempres += "<input type='hidden' name='name[]' value='"+$('.detail #data #detail input[name="subfixname[]"]:eq('+i+')').val()+"'>";//產品名稱
						tempres += "<input type='hidden' name='name2[]' value='"+$('.detail #data #detail input[name="subfixname2[]"]:eq('+i+')').val()+"'>";//產品第二名稱(補充POS端變數)
						tempres += "<input type='hidden' name='isgroup[]' value='"+$('.detail #data #detail input[name="subfixisgroup[]"]:eq('+i+')').val()+"'>";//是否為套餐(補充POS端變數)
						tempres += "<input type='hidden' name='childtype[]' value='"+$('.detail #data #detail input[name="subfixchildtype[]"]:eq('+i+')').val()+"'>";//是否為套餐選項(補充POS端變數)
						tempres += "<input type='hidden' name='mname1[]' value='"+$('.detail #data #detail input[name="subfixmname1[]"]:eq('+i+')').val()+"'>";//價格名稱
						tempres += "<input type='hidden' name='mname2[]' value=''>";//價格名稱2(補充POS端變數)
						tempres += "<input type='hidden' name='insaleinv[]' value='"+$('.detail #data #detail input[name="subfixinsaleinv[]"]:eq('+i+')').val()+"'>";//計算發票金額(補充POS端變數)
						tempres += "<input type='hidden' name='unitprice[]' value='"+$('.detail #data #detail input[name="subfixunitprice[]"]:eq('+i+')').val()+"'>";//單價
						tempres += "<input type='hidden' name='taste1[]' value='"+$('.detail #data #detail input[name="subfixtaste1[]"]:eq('+i+')').val()+"'>";//使用的加料備註代號(陣列字串)
						tempres += "<input type='hidden' name='taste1name[]' value='"+$('.detail #data #detail input[name="subfixtaste1name[]"]:eq('+i+')').val()+"'>";//使用的加料備註名稱(陣列字串)
						tempres += "<input type='hidden' name='taste1price[]' value='"+$('.detail #data #detail input[name="subfixtaste1price[]"]:eq('+i+')').val()+"'>";//使用的加料備註單價(陣列字串)
						tempres += "<input type='hidden' name='taste1number[]' value='"+$('.detail #data #detail input[name="subfixtaste1number[]"]:eq('+i+')').val()+"'>";//使用的加料備註數量(陣列字串)
						tempres += "<input type='hidden' name='taste1money[]' value='"+$('.detail #data #detail input[name="subfixtaste1money[]"]:eq('+i+')').val()+"'>";//使用的加料備註小計
						tempres += "<input type='hidden' name='money[]' value='"+(parseFloat($('.detail #data #detail input[name="subfixtaste1money[]"]:eq('+i+')').val())+parseFloat($('.detail #data #detail input[name="subfixunitprice[]"]:eq('+i+')').val()))+"'>";//1個(含加料備註)的金額
						tempres += "<input type='hidden' name='discount[]' value='0'>";//單品折扣金額(補充POS端變數，故意留空)
						tempres += "<input type='hidden' name='discontent[]' value=''>";//折扣方式(補充POS端變數，故意留空)
						tempres += "<input type='hidden' name='dis1[]' value='"+$('.detail #data #detail input[name="subfixdis1[]"]:eq('+i+')').val()+"'>";
						tempres += "<input type='hidden' name='dis2[]' value='"+$('.detail #data #detail input[name="subfixdis2[]"]:eq('+i+')').val()+"'>";
						tempres += "<input type='hidden' name='dis3[]' value='"+$('.detail #data #detail input[name="subfixdis3[]"]:eq('+i+')').val()+"'>";
						tempres += "<input type='hidden' name='dis4[]' value='"+$('.detail #data #detail input[name="subfixdis4[]"]:eq('+i+')').val()+"'>";
						
						//2020/5/19 贈與點數規則
						tempres += "<input type='hidden' name='getpointtype[]' value='"+$('.detail #data #detail input[name="subfixgetpointtype[]"]:eq('+i+')').val()+"'>";
						tempres += "<input type='hidden' name='initgetpoint[]' value='"+$('.detail #data #detail input[name="subfixinitgetpoint[]"]:eq('+i+')').val()+"'>";
						tempres += "<input type='hidden' name='getpoint[]' value='"+$('.detail #data #detail input[name="subfixgetpoint[]"]:eq('+i+')').val()+"'>";

						tempres += "<input type='hidden' name='number[]' value='"+$('.detail #data #detail input[name="qty"]').val()+"'>";//數量
						tempres += "<input type='hidden' name='subtotal[]' value='"+(parseFloat((parseFloat($('.detail #data #detail input[name="subfixtaste1money[]"]:eq('+i+')').val())+parseFloat($('.detail #data #detail input[name="subfixunitprice[]"]:eq('+i+')').val())))*parseFloat($('.detail #data #detail input[name="qty"]').val()))+"'>";//小計
						tempres += '</div>';
					}
				}
				else{
				}
				if($('.detail #data #detail input[name="subvarno[]"]').length>0){//2020/8/18 套餐品項
					for(var i=0;i<$('.detail #data #detail input[name="subvarno[]"]').length;i++){
						if(!$('.detail #data #detail input[name="subvarno[]"]:eq('+i+')').prop('checked')){
						}
						else{
							if($('.items div#item').length==0){
								tempres += '<div class="'+(1+parseInt(itemtimes))+'" id="item">';
							}
							else{
								tempres += '<div class="'+(parseInt($('.items div#item:eq('+($('.items div#item').length-1)+')').attr('class'))+1+parseInt(itemtimes))+'" id="item">';
							}
							tempres += "<input type='hidden' name='linenumber[]' value='";
							if($('.items div#item').length==0){
								tempres += (1+(parseInt(itemtimes)*2));
							}
							else{
								tempres += (parseInt($('.items div#item:eq('+($('.items div#item').length-1)+') input[name="linenumber[]"]').val())+((parseInt(itemtimes)+1)*2));
							}
							tempres += "'>";//資料庫順序(補充POS端變數)
							tempres += "<input type='hidden' name='order[]' value='－'>";//點餐列表順序(補充POS端變數)
							tempres += "<input type='hidden' name='typeno[]' value='"+$('.detail #data #detail input[name="subvartypeno[]"]:eq('+i+')').val()+"'>";//類別代號
							tempres += "<input type='hidden' name='type[]' value='"+$('.detail #data #detail input[name="subvartype[]"]:eq('+i+')').val()+"'>";//類別名稱(補充POS端變數，故意留空)
							tempres += "<input type='hidden' name='no[]' value='"+$('.detail #data #detail input[name="subvarno[]"]:eq('+i+')').val()+"'>";//產品代號
							tempres += "<input type='hidden' name='personcount[]' value='"+$('.detail #data #detail input[name="subvarpersoncount[]"]:eq('+i+')').val()+"'>";//人數勾機(補充POS端變數)
							tempres += "<input type='hidden' name='needcharge[]' value='"+$('.detail #data #detail input[name="subvarneedcharge[]"]:eq('+i+')').val()+"'>";//計算服務費(補充POS端變數)
							tempres += "<input type='hidden' name='name[]' value='"+$('.detail #data #detail input[name="subvarname[]"]:eq('+i+')').val()+"'>";//產品名稱
							tempres += "<input type='hidden' name='name2[]' value='"+$('.detail #data #detail input[name="subvarname2[]"]:eq('+i+')').val()+"'>";//產品第二名稱(補充POS端變數)
							tempres += "<input type='hidden' name='isgroup[]' value='"+$('.detail #data #detail input[name="subvarisgroup[]"]:eq('+i+')').val()+"'>";//是否為套餐(補充POS端變數)
							tempres += "<input type='hidden' name='childtype[]' value='"+$('.detail #data #detail input[name="subvarchildtype[]"]:eq('+i+')').val()+"'>";//是否為套餐選項(補充POS端變數)
							tempres += "<input type='hidden' name='mname1[]' value='"+$('.detail #data #detail input[name="subvarmname1[]"]:eq('+i+')').val()+"'>";//價格名稱
							tempres += "<input type='hidden' name='mname2[]' value=''>";//價格名稱2(補充POS端變數)
							tempres += "<input type='hidden' name='insaleinv[]' value='"+$('.detail #data #detail input[name="subvarinsaleinv[]"]:eq('+i+')').val()+"'>";//計算發票金額(補充POS端變數)
							tempres += "<input type='hidden' name='unitprice[]' value='"+$('.detail #data #detail input[name="subvarunitprice[]"]:eq('+i+')').val()+"'>";//單價
							tempres += "<input type='hidden' name='taste1[]' value='"+$('.detail #data #detail input[name="subvartaste1[]"]:eq('+i+')').val()+"'>";//使用的加料備註代號(陣列字串)
							tempres += "<input type='hidden' name='taste1name[]' value='"+$('.detail #data #detail input[name="subvartaste1name[]"]:eq('+i+')').val()+"'>";//使用的加料備註名稱(陣列字串)
							tempres += "<input type='hidden' name='taste1price[]' value='"+$('.detail #data #detail input[name="subvartaste1price[]"]:eq('+i+')').val()+"'>";//使用的加料備註單價(陣列字串)
							tempres += "<input type='hidden' name='taste1number[]' value='"+$('.detail #data #detail input[name="subvartaste1number[]"]:eq('+i+')').val()+"'>";//使用的加料備註數量(陣列字串)
							tempres += "<input type='hidden' name='taste1money[]' value='"+$('.detail #data #detail input[name="subvartaste1money[]"]:eq('+i+')').val()+"'>";//使用的加料備註小計
							tempres += "<input type='hidden' name='money[]' value='"+(parseFloat($('.detail #data #detail input[name="subvartaste1money[]"]:eq('+i+')').val())+parseFloat($('.detail #data #detail input[name="subvarunitprice[]"]:eq('+i+')').val()))+"'>";//1個(含加料備註)的金額
							tempres += "<input type='hidden' name='discount[]' value='0'>";//單品折扣金額(補充POS端變數，故意留空)
							tempres += "<input type='hidden' name='discontent[]' value=''>";//折扣方式(補充POS端變數，故意留空)
							tempres += "<input type='hidden' name='dis1[]' value='"+$('.detail #data #detail input[name="subvardis1[]"]:eq('+i+')').val()+"'>";
							tempres += "<input type='hidden' name='dis2[]' value='"+$('.detail #data #detail input[name="subvardis2[]"]:eq('+i+')').val()+"'>";
							tempres += "<input type='hidden' name='dis3[]' value='"+$('.detail #data #detail input[name="subvardis3[]"]:eq('+i+')').val()+"'>";
							tempres += "<input type='hidden' name='dis4[]' value='"+$('.detail #data #detail input[name="subvardis4[]"]:eq('+i+')').val()+"'>";
							
							//2020/5/19 贈與點數規則
							tempres += "<input type='hidden' name='getpointtype[]' value='"+$('.detail #data #detail input[name="subvargetpointtype[]"]:eq('+i+')').val()+"'>";
							tempres += "<input type='hidden' name='initgetpoint[]' value='"+$('.detail #data #detail input[name="subvarinitgetpoint[]"]:eq('+i+')').val()+"'>";
							tempres += "<input type='hidden' name='getpoint[]' value='"+$('.detail #data #detail input[name="subvargetpoint[]"]:eq('+i+')').val()+"'>";

							tempres += "<input type='hidden' name='number[]' value='"+$('.detail #data #detail input[name="qty"]').val()+"'>";//數量
							tempres += "<input type='hidden' name='subtotal[]' value='"+(parseFloat((parseFloat($('.detail #data #detail input[name="subvartaste1money[]"]:eq('+i+')').val())+parseFloat($('.detail #data #detail input[name="subvarunitprice[]"]:eq('+i+')').val())))*parseFloat($('.detail #data #detail input[name="qty"]').val()))+"'>";//小計
							tempres += '</div>';
							itemtimes++;
						}
					}
				}
				else{
				}

				$('.items').append(tempres);
				$('#title .return').trigger('click');
				if($('.items #item').length==0){
					/*$('#keybox .funkey2 #point').css({'border-top':'','border-bottom':'','border-left':'','border-bottom-left-radius':'','border-top-left-radius':''});*/
					$('#keybox .funkey2 #point').html('');
					$('#setup input[name="total"]').val('0');
					$('#setup input[name="totalnumber"]').val('0');
				}
				else{
					/*$('#keybox .funkey2 #point').css({'border-top':'2px solid rgba(26, 26, 26, 0.5)','border-bottom':'2px solid rgba(26, 26, 26, 0.5)','border-left':'2px solid rgba(26, 26, 26, 0.5)','border-bottom-left-radius':'10px','border-top-left-radius':'10px'});*/
					$('#keybox .funkey2 #point').html('x'+comptotalqty()+$('.basic .unit').val()+'<br>'+$('.basic .moneypreunit').val()+comptotal()+$('.basic .moneysufunit').val());
					$('#setup input[name="total"]').val(comptotal());
					$('#setup input[name="totalnumber"]').val(comptotalqty());
				}
			}
		}
	});
	/*$('.detail #footer .send').on('click',function(){
		if($('.detail #data .itemdata input[name="itemseq"]').length>0){
			var money=$('.detail #data #detail #money option:selected').val().split(';');
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="mname1[]"]').val(money[0]);
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="unitprice[]"]').val(money[1]);
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="money[]"]').val((parseFloat($('.detail #data #detail #tastemoney').html())+parseFloat(money[1])));
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="number[]"]').val($('.detail #data #detail input[name="qty"]').val());
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="subtotal[]"]').val((parseFloat((parseFloat($('.detail #data #detail #tastemoney').html())+parseFloat(money[1])))*parseFloat($('.detail #data #detail input[name="qty"]').val())));

			var tasteno='';
			var tastename='';
			var tasteprice='';
			var tastenumber='';
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tasteno[]"]').val('');
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tastename[]"]').val('');
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tasteprice[]"]').val('');
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tastenumber[]"]').val('');
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="tastemoney[]"]').val('');
			for(var i=0;i<$('.detail #data #detail .switch').length;i++){
				if($('.detail #data #detail .switch:eq('+i+') input[type="checkbox"]').prop('checked')==true){
					if(tasteno==''){
						tasteno=$('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
						tastename=$('.detail #data #detail .switch:eq('+i+') input[name="tastename[]"]').val();
						tasteprice=$('.detail #data #detail .switch:eq('+i+') input[name="money[]"]').val();
						tastenumber='1';
					}
					else{
						tasteno=tasteno+','+$('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
						tastename=tastename+','+$('.detail #data #detail .switch:eq('+i+') input[name="tastename[]"]').val();
						tasteprice=tasteprice+','+$('.detail #data #detail .switch:eq('+i+') input[name="money[]"]').val();
						tastenumber=tastenumber+',1';
					}
				}
				else{
				}
			}
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1[]"]').val(tasteno);
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1name[]"]').val(tastename);
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1price[]"]').val(tasteprice);
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1number[]"]').val(tastenumber);
			$('.items .'+$('.detail #data .itemdata input[name="itemseq"]').val()+'#item input[name="taste1money[]"]').val($('.detail #data #detail #tastemoney').html());

			$('#title .return').trigger('click');
			if($('.items #item').length==0){
				//$('#keybox .funkey2 #point').css({'border-top':'','border-bottom':'','border-left':'','border-bottom-left-radius':'','border-top-left-radius':''});
				$('#keybox .funkey2 #point').html('');
				$('#setup input[name="total"]').val('0');
				$('#setup input[name="totalnumber"]').val('0');
			}
			else{
				//$('#keybox .funkey2 #point').css({'border-top':'2px solid rgba(26, 26, 26, 0.5)','border-bottom':'2px solid rgba(26, 26, 26, 0.5)','border-left':'2px solid rgba(26, 26, 26, 0.5)','border-bottom-left-radius':'10px','border-top-left-radius':'10px'});
				$('#keybox .funkey2 #point').html('x'+comptotalqty()+$('.basic .unit').val()+'<br>'+$('.basic .moneypreunit').val()+comptotal()+$('.basic .moneysufunit').val());
				$('#setup input[name="total"]').val(comptotal());
				$('#setup input[name="totalnumber"]').val(comptotalqty());
			}
			$('#keybox .funkey2 div:eq(0)').prop('id','list');
			$('#keybox .funkey2 #list').trigger('click');
		}
		else{
			var tempres='';
			if($('.items div#item').length==0){
				tempres=tempres+'<div class="1" id="item">';
			}
			else{
				tempres=tempres+'<div class="'+(parseInt($('.items div#item:eq('+($('.items div#item').length-1)+')').attr('class'))+1)+'" id="item">';
			}
			tempres=tempres+"<input type='hidden' name='linenumber[]' value='";
			if($('.items div#item').length==0){
				tempres=tempres+"1";
			}
			else{
				tempres=tempres+($('.items div#item:eq('+($('.items div#item').length-1)+') input[name="linenumber[]"]').val()+2);
			}
			tempres=tempres+"'>";//資料庫順序(補充POS端變數)
			tempres=tempres+"<input type='hidden' name='order[]' value='";
			if($('.items div#item').length==0){
				tempres=tempres+"1";
			}
			else{
				tempres=tempres+(parseInt($('.items div#item:eq('+($('.items div#item').length-1)+')').attr('class'))+1);
			}
			tempres=tempres+"'>";//點餐列表順序(補充POS端變數)
			tempres=tempres+"<input type='hidden' name='typeno[]' value='"+$('.detail #data #detail input[name="typeno"]').val()+"'>";//類別代號
			tempres=tempres+"<input type='hidden' name='type[]' value='"+$('.detail #data #detail input[name="type"]').val()+"'>";//類別名稱(補充POS端變數，故意留空)
			tempres=tempres+"<input type='hidden' name='no[]' value='"+$('.detail #data #detail input[name="no"]').val()+"'>";//產品代號
			tempres=tempres+"<input type='hidden' name='personcount[]' value='"+$('.detail #data #detail input[name="personcount"]').val()+"'>";//人數勾機(補充POS端變數)
			tempres=tempres+"<input type='hidden' name='needcharge[]' value='"+$('.detail #data #detail input[name="needcharge"]').val()+"'>";//計算服務費(補充POS端變數)
			tempres=tempres+"<input type='hidden' name='seq[]' value='"+$('.detail #data #detail input[name="seq"]').val()+"'>";//產品排序
			tempres=tempres+"<input type='hidden' name='name[]' value='"+$('.detail #data #detail input[name="name"]').val()+"'>";//產品名稱
			tempres=tempres+"<input type='hidden' name='name2[]' value='"+$('.detail #data #detail input[name="name2"]').val()+"'>";//產品第二名稱(補充POS端變數)
			tempres=tempres+"<input type='hidden' name='isgroup[]' value='"+$('.detail #data #detail input[name="isgroup"]').val()+"'>";//是否為套餐(補充POS端變數)
			tempres=tempres+"<input type='hidden' name='childtype[]' value='"+$('.detail #data #detail input[name="childtype"]').val()+"'>";//是否為套餐選項(補充POS端變數)
			var money=$('.detail #data #detail #money option:selected').val().split(';');
			tempres=tempres+"<input type='hidden' name='mname1[]' value='"+money[0]+"'>";//價格名稱
			tempres=tempres+"<input type='hidden' name='mname2[]' value=''>";//價格名稱2(補充POS端變數)
			tempres=tempres+"<input type='hidden' name='insaleinv[]' value='"+$('.detail #data #detail input[name="insaleinv"]').val()+"'>";//計算發票金額(補充POS端變數)
			tempres=tempres+"<input type='hidden' name='unitprice[]' value='"+money[1]+"'>";//單價
			var tasteno='';
			var tastename='';
			var tasteprice='';
			var tastenumber='';
			for(var i=0;i<$('.detail #data #detail .switch').length;i++){
				if($('.detail #data #detail .switch:eq('+i+') input[type="checkbox"]').prop('checked')==true){
					if(tasteno==''){
						tasteno=$('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
						tastename=$('.detail #data #detail .switch:eq('+i+') input[name="tastename[]"]').val();
						tasteprice=$('.detail #data #detail .switch:eq('+i+') input[name="money[]"]').val();
						tastenumber='1';
					}
					else{
						tasteno=tasteno+','+$('.detail #data #detail .switch:eq('+i+') input[name="tasteno[]"]').val();
						tastename=tastename+','+$('.detail #data #detail .switch:eq('+i+') input[name="tastename[]"]').val();
						tasteprice=tasteprice+','+$('.detail #data #detail .switch:eq('+i+') input[name="money[]"]').val();
						tastenumber=tastenumber+',1';
					}
				}
				else{
				}
			}
			tempres=tempres+"<input type='hidden' name='taste1[]' value='"+tasteno+"'>";//使用的加料備註代號(陣列字串)
			tempres=tempres+"<input type='hidden' name='taste1name[]' value='"+tastename+"'>";//使用的加料備註名稱(陣列字串)
			tempres=tempres+"<input type='hidden' name='taste1price[]' value='"+tasteprice+"'>";//使用的加料備註單價(陣列字串)
			tempres=tempres+"<input type='hidden' name='taste1number[]' value='"+tastenumber+"'>";//使用的加料備註數量(陣列字串)
			tempres=tempres+"<input type='hidden' name='taste1money[]' value='"+$('.detail #data #detail #tastemoney').html()+"'>";//使用的加料備註小計
			tempres=tempres+"<input type='hidden' name='money[]' value='"+(parseFloat($('.detail #data #detail #tastemoney').html())+parseFloat(money[1]))+"'>";//1個(含加料備註)的金額
			tempres=tempres+"<input type='hidden' name='discount[]' value='0'>";//單品折扣金額(補充POS端變數，故意留空)
			tempres=tempres+"<input type='hidden' name='discontent[]' value=''>";//折扣方式(補充POS端變數，故意留空)
			tempres=tempres+"<input type='hidden' name='dis1[]' value='"+$('.detail #data #detail input[name="dis1"]').val()+"'>";
			tempres=tempres+"<input type='hidden' name='dis2[]' value='"+$('.detail #data #detail input[name="dis2"]').val()+"'>";
			tempres=tempres+"<input type='hidden' name='dis3[]' value='"+$('.detail #data #detail input[name="dis3"]').val()+"'>";
			tempres=tempres+"<input type='hidden' name='dis4[]' value='"+$('.detail #data #detail input[name="dis4"]').val()+"'>";
			tempres=tempres+"<input type='hidden' name='number[]' value='"+$('.detail #data #detail input[name="qty"]').val()+"'>";//數量
			tempres=tempres+"<input type='hidden' name='subtotal[]' value='"+(parseFloat((parseFloat($('.detail #data #detail #tastemoney').html())+parseFloat(money[1])))*parseFloat($('.detail #data #detail input[name="qty"]').val()))+"'>";//小計
			//$('.items .'+rows).append("<input type='hidden' name='tasteno[]' value='"+tasteno+"'>");
			//$('.items .'+rows).append("<input type='hidden' name='tastename[]' value='"+tastename+"'>");
			//$('.items .'+rows).append("<input type='hidden' name='tasteprice[]' value='"+tasteprice+"'>");
			//$('.items .'+rows).append("<input type='hidden' name='tastenumber[]' value='"+tastenumber+"'>");
			//$('.items .'+rows).append("<input type='hidden' name='tastemoney[]' value='"+$('.detail #data #detail #tastemoney').html()+"'>");
			tempres=tempres+'</div>';
			$('.items').append(tempres);
			$('#title .return').trigger('click');
			if($('.items #item').length==0){
				//$('#keybox .funkey2 #point').css({'border-top':'','border-bottom':'','border-left':'','border-bottom-left-radius':'','border-top-left-radius':''});
				$('#keybox .funkey2 #point').html('');
				$('#setup input[name="total"]').val('0');
				$('#setup input[name="totalnumber"]').val('0');
			}
			else{
				//$('#keybox .funkey2 #point').css({'border-top':'2px solid rgba(26, 26, 26, 0.5)','border-bottom':'2px solid rgba(26, 26, 26, 0.5)','border-left':'2px solid rgba(26, 26, 26, 0.5)','border-bottom-left-radius':'10px','border-top-left-radius':'10px'});
				$('#keybox .funkey2 #point').html('x'+comptotalqty()+$('.basic .unit').val()+'<br>'+$('.basic .moneypreunit').val()+comptotal()+$('.basic .moneysufunit').val());
				$('#setup input[name="total"]').val(comptotal());
				$('#setup input[name="totalnumber"]').val(comptotalqty());
			}
		}
	});*/
	function comptotal(){
		var totalamt=0;
		for(var i=0;i<$('.items #item input[name="subtotal[]"]').length;i++){
			totalamt=parseFloat(totalamt)+parseFloat($('.items #item:eq('+i+') input[name="subtotal[]"]').val());
		}
		totalamt=parseFloat(totalamt)-parseFloat($('#setup input[name="autodis"]').val())+parseFloat($('#setup input[name="charge"]').val());
		if(totalamt<=0){
			totalamt=0;
		}
		else{
		}
		return totalamt;
	}
	function comptotalqty(){
		var totalqty=0;
		for(var i=0;i<$('.items #item input[name="number[]"]').length;i++){
			totalqty=parseFloat(totalqty)+parseFloat($('.items #item:eq('+i+') input[name="number[]"]').val());
		}
		return totalqty;
	}
	$('#keybox').on('click','#list',function(){
		//console.log('1');
		if($('.items #item').length==0){
			//alert('暫無點餐紀錄。');
			$('.message1 #text').html('暫無選購紀錄。');
			//msg1.dialog('open');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else{
			var res='';
			res='<div id="itemlist" style="width:100%;margin:5px 0;border-collapse: collapse;font-size:20px;">';//<div style="width:calc(100% - 10px);height:50px;line-height:50px;margin:25px 5px 0 5px;float:left;padding-bottom:15px;border-bottom:1px solid #dcdcdc;color:'+$('#title').css('background-color')+';position: relative;">
			/*if($('.basic input[name="name"]').val()!=''){
				res=res+'<div class="memname" style="width:50px;height:50px;font-size:45px;line-height:50px;margin-right:10px;text-align:center;border-radius:100%;background-color:'+$('#keybox').css('background-color')+';color:#ffffff;float:left;">'+$('.basic input[name="name"]').val().substr(0,1).toUpperCase()+'</div><span id="name">'+$('.basic input[name="name"]').val()+'</span><div class="memnamemodal" style="width:100%;height:100%;position:absolute;top:0;left:0;background-color:#ffffff;display:none;"></div>';
			}
			else{
				res=res+'<div class="memname" style="width:50px;height:50px;font-size:45px;line-height:50px;margin-right:10px;text-align:center;border-radius:100%;background-color:'+$('#keybox').css('background-color')+';color:#ffffff;float:left;display:none;">'+$('.basic input[name="name"]').val().substr(0,1).toUpperCase()+'</div><span id="name">'+$('.basic input[name="name"]').val()+'</span><div class="memnamemodal" style="width:100%;height:100%;position:absolute;top:0;left:0;background-color:#ffffff;"></div>';
			}*/
			//res=res+'</div>';//<h2 style="width:max-content;float:left;margin-left:5px;margin-right:5px;margin-bottom:0;padding-bottom:25px;border-bottom:1px solid #dcdcdc;">選購清單</h2>
			res=res+'<h2 style="width:calc(100% - 10px);float:left;margin-left:5px;margin-right:5px;margin-bottom:0;padding-bottom:25px;border-bottom:1px solid #dcdcdc;">選購清單</h2>';
			res=res+'<table id="receverdata"><tr><td>帳單類別</td><td>';
			$.ajax({
				url:'./lib/js/getlisttype.php',
				method:'post',
				async:false,
				data:{'listtype':$('#setup input[name="listtype"]').val()},
				dataType:'html',
				success:function(d){
					res=res+d;
					//console.log(d);
				},
				error:function(e){
					//console.log(e);
				}
			});
			res=res+'</td></tr>';
			if($('.basic input[name="openmember"]').val()=='1'){
				res=res+'<tr><td>會員電話</td><td><input type="tel" name="memtel" style="width:calc(100% - 47px);float:left;margin:0;padding:5px 3px;border:1px solid #898989;font-size:16px;border-radius:5px;" value="'+$('#setup input[name="memtel"]').val()+'"><img id="createmember" style="width:30px;height:30px;float:left;background-color:rgb(26,26,26,0.5);margin:0 0 0 5px;border:1px solid #898989;border-radius:5px;cursor: pointer;" src="./img/plus.png"></td></tr>';
				res=res+'<tr><td>會員姓名</td><td><select name="memname" style="width:100%;font-size:16px;padding:5px 3px;border-radius:5px;"><option value="'+$('#setup input[name="memno"]').val()+'">'+$('#setup input[name="memname"]').val()+'</option></select></td></tr>';
			}
			else{
			}
			/*if($('#setup input[name="listtype"]').val()=='1'||$('#setup input[name="listtype"]').val()=='2'){
			}
			else{
				res=res+'<tr><td>抵達時間</td><td>';
				$.ajax({
					url:'./lib/js/getrecever.time.php',
					method:'post',
					async:false,
					data:{'story':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val()},
					dataType:'json',
					success:function(d){
						res=res+'<select name="date" style="float:left;">'+d['option']+'</select>';
						res=res+'<select name="nowtime" style="float:left;"><option value="now">越快越好</option>'+d['nowtime']+'</select>';
						res=res+'<select name="time" style="float:left;display:none;">'+d['time']+'</select>';
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
				res=res+'</td></tr>';
			}*/
			//<tr><td>收貨人</td><td><input type="text" style="width:90%;border:0px;font-size:18px;text-align:left;color:'+$('#title').css('background-color')+';" name="name" value="'+$('.basic input[name="name"]').val()+'" placeholder="請填寫收貨人名稱"></td></tr><tr><td>聯絡電話</td><td><input type="tel" style="width:90%;border:0px;font-size:18px;text-align:left;color:'+$('#title').css('background-color')+';" name="phone" value="'+$('.basic input[name="phone"]').val()+'" placeholder="必填"></td></tr><tr id="address" style="display:none;"><td>外送地址</td><td><input type="text" style="width:90%;border:0px;font-size:18px;text-align:left;color:'+$('#title').css('background-color')+';" name="address" value="'+$('.basic input[name="address"]').val()+'" placeholder="地址"></td></tr></tr><tr><td>單位名稱</td><td><input type="text" style="width:90%;border:0px;font-size:18px;text-align:left;color:'+$('#title').css('background-color')+';" name="" placeholder="公司/大樓/學校"></td></tr><tr><td>統一編號</td><td><input type="tel " style="width:90%;border:0px;font-size:18px;text-align:left;color:'+$('#title').css('background-color')+';" name="" placeholder="選填"></td></tr><tr><td>備註</td><td><textarea rows="3" style="width:90%;border:0px;font-size:18px;text-align:left;color:'+$('#title').css('background-color')+';" name="" placeholder="有其他需求可以在這裡告訴我們。"></textarea></td></tr>
			res=res+'</table>';
			var total=0;
			var subtotal=0;
			var qty=0;
			var temptotal=0;
			var tempdis=0;
			for(var i=0;i<$('.items #item').length;i++){
				total=Number(total)+(Number($('.items #item:eq('+i+') input[name="money[]"]').val())*Number($('.items #item:eq('+i+') input[name="number[]"]').val()));
				subtotal=Number(subtotal)+(Number($('.items #item:eq('+i+') input[name="subtotal[]"]').val()));
				qty=Number(qty)+(Number($('.items #item:eq('+i+') input[name="number[]"]').val()));
				if($('.items #item:eq('+i+') input[name="needcharge[]"]').val()=='1'){
					temptotal=Number(temptotal)+(Number($('.items #item:eq('+i+') input[name="money[]"]').val())*Number($('.items #item:eq('+i+') input[name="number[]"]').val()));
					tempdis=Number(tempdis)+Number($('.items #item:eq('+i+') input[name="discount[]"]').val());
				}
				else{
				}
				res=res+'<div class="row" style="width:calc(100% - 10px);padding:10px 5px;background-color:#f8f8f8;overflow:hidden;"><input type="hidden" name="itemseq" value="';
				if($('.items #item:eq('+i+') input[name="order[]"]').val()=='－'){//2020/8/18 套餐品項
					res=res+'－';
				}
				else{
					res=res+$('.items #item:eq('+i+')').prop('class');
				}
				res=res+'"><div id="data" style="width:100%;float:left;"><div style="width:100%;float:left;"><div style="float:left;overflow:hidden;font-size:22px;';
				if($('.items #item:eq('+i+') input[name="order[]"]').val()=='－'){//2020/8/18 套餐品項
					res=res+'width:calc(100% - 153.2px);padding-left:20px;';
				}
				else{
					res=res+'width:calc(100% - 133.2px);';
				}
				res=res+'">'+$('.items #item:eq('+i+') input[name="name[]"]').val()+'</div><div style="width:max-content;float:right;text-align:right;font-size:16px;margin:0 0 0 5px;">';
				if ($('.items #item:eq('+i+') input[name="subtotal[]"]').val() > 0 )
				{
					res=res+$('.basic .moneypreunit').val()+$('.items #item:eq('+i+') input[name="subtotal[]"]').val();
				}
				res=res+'</div><div style="width:max-content;float:right;text-align:right;font-size:20px;color:#000000;margin:0 5px 0 0;"> x'+$('.items #item:eq('+i+') input[name="number[]"]').val()+'</div></div><div style="min-height:1px;float:left;font-size:14px;color:#616161;';
				if($('.items #item:eq('+i+') input[name="order[]"]').val()=='－'){//2020/8/18 套餐品項
					res=res+'width:calc(100% - 40px);padding-left:20px;';
				}
				else{
					res=res+'width:calc(100% - 20px);';
				}
				res=res+'">'+$('.items #item:eq('+i+') input[name="mname1[]"]').val();
				if($('.items #item:eq('+i+') input[name="mname1[]"]').val()!=''&&$('.items #item:eq('+i+') input[name="taste1name[]"]').val()!=''){
					res=res+',';
				}
				else{
				}
				if($('.items #item:eq('+i+') input[name="taste1name[]"]').val()!=''){
					var spltastename=$('.items #item:eq('+i+') input[name="taste1name[]"]').val().split(',');
					var spltastenumber=$('.items #item:eq('+i+') input[name="taste1number[]"]').val().split(',');
					for(var stn=0;stn<spltastename.length;stn++){
						if(stn!=0){
							res += ',';
						}
						else{
						}
						res += spltastename[stn];
						//2021/12/23 因為明細列印方式有改，數量提前寫入備註名稱後面
						/*if(spltastenumber[stn]!=1){
							res += '*'+spltastenumber[stn];
						}
						else{
						}*/
					}
					//res=res+$('.items #item:eq('+i+') input[name="taste1name[]"]').val();
				}
				else{
				}
				res=res+'</div>';
				res=res+'<div class="itemfunbox" style="';
				/*if($('.items #item:eq('+i+') input[name="templistitem[]"]').length>0){
					res=res+'display:none;';
				}
				else{
				}*/
				if($('.items #item:eq('+i+') input[name="order[]"]').val()=='－'){//2020/8/18 套餐品項
					res=res+'display:none;';
				}
				else{
				}
				res=res+'width:20px;float:left;cursor: pointer;"><img src="./img/pencil.png"></div>';
				res=res+'</div></div>';
			}
			/*for(var i=0;i<$('.items #item').length;i++){
				total=Number(total)+(Number($('.items #item:eq('+i+') input[name="money[]"]').val())*Number($('.items #item:eq('+i+') input[name="number[]"]').val()));
				subtotal=Number(subtotal)+(Number($('.items #item:eq('+i+') input[name="subtotal[]"]').val()));
				qty=Number(qty)+(Number($('.items #item:eq('+i+') input[name="number[]"]').val()));
				if($('.items #item:eq('+i+') input[name="needcharge[]"]').val()=='1'){
					temptotal=Number(temptotal)+(Number($('.items #item:eq('+i+') input[name="money[]"]').val())*Number($('.items #item:eq('+i+') input[name="number[]"]').val()));
					tempdis=Number(tempdis)+Number($('.items #item:eq('+i+') input[name="discount[]"]').val());
				}
				else{
				}
				res=res+'<div class="row" style="width:calc(100% - 10px);padding:10px 5px;background-color:#f8f8f8;overflow:hidden;"><input type="hidden" name="itemseq" value="'+$('.items #item:eq('+i+')').prop('class')+'"><div id="data" style="width:100%;float:left;"><div style="width:100%;float:left;"><div style="width:calc(100% - 133.2px);float:left;overflow:hidden;font-size:18px;">'+$('.items #item:eq('+i+') input[name="name[]"]').val()+'</div><div style="width:57.6px;float:left;text-align:right;font-size:16px;color:#b5b5b5;"> x'+$('.items #item:eq('+i+') input[name="number[]"]').val()+'</div><div style="width:75.6px;float:left;text-align:right;font-size:16px;">'+$('.basic .moneypreunit').val()+$('.items #item:eq('+i+') input[name="subtotal[]"]').val()+'</div></div><div style="width:calc(100% - 20px);min-height:1px;float:left;font-size:14px;color:#b5b5b5;">'+$('.items #item:eq('+i+') input[name="mname1[]"]').val();
				if($('.items #item:eq('+i+') input[name="mname1[]"]').val()!=''&&$('.items #item:eq('+i+') input[name="taste1name[]"]').val()!=''){
					res=res+',';
				}
				else{
				}
				res=res+$('.items #item:eq('+i+') input[name="taste1name[]"]').val()+'</div>';
				res=res+'<div class="itemfunbox" style="';
				res=res+'width:20px;float:left;cursor: pointer;"><img src="./img/pencil.png"></div>';
				res=res+'</div></div>';
			}*/
			res=res+'<div style="width:calc(100% - 10px);border-bottom:1px solid #898989;margin:5px;"></div>';
			if($('.basic input[name="autodis"]').val()=='1'){//開啟自動優惠
				var autodisarray=$('.items').serialize();
				autodisarray=autodisarray+'&listtype='+$('#setup input[name="listtype"]').val();
				$.ajax({
					url:'../demopos/lib/js/compdis.ajax.php',
					method:'post',
					async: false,
					data:autodisarray,
					dataType:'html',
					success:function(d){
						if(d.length>20){
							$.ajax({
								url:'../demopos/lib/js/print.php',
								method:'post',
								data:{'html':'orderpos compdis.ajax.php '+d},
								dataType:'html',
								success:function(d){/*console.log(d);*/},
								error:function(e){/*console.log(e);*/}
							});
						}
						else{
						}
						var temp=d.split(';');
						if($.isNumeric(temp[0])){
							$('#setup input[name="autodis"]').val(temp[0]);
							$('#setup input[name="autodiscontent"]').val(temp[1]);
							$('#setup input[name="autodispremoney"]').val(temp[2]);
						}
						else{
							$('#setup input[name="autodis"]').val('0');
							$('#setup input[name="autodiscontent"]').val('');
							$('#setup input[name="autodispremoney"]').val('0');
						}
						//console.log(d);
					},
					error:function(e){
						//console.log(e)
					}
				});
			}
			else{
				$('#setup input[name="autodis"]').val('0');
				$('#setup input[name="autodiscontent"]').val('');
				$('#setup input[name="autodispremoney"]').val('0');
			}
			$('#setup input[name="cashmoney"]').val('0');
			$('#setup input[name="cash"]').val('0');
			$('#setup input[name="other"]').val('0');
			$('#setup input[name="otherstring"]').val('');
			$('#setup input[name="otherfix"]').val('0');
			if($('#setup input[name="autodis"]').val()!='0'){
				res=res+'<div style="width:calc(100% - 10px);margin:10px 0 -10px 0;padding-left:5px;padding-right:5px;overflow:hidden;font-size:20px;"><div style="float:left;">自動優惠</div><div style="width:75.6px;float:right;font-size:18px;text-align:right;">'+$('.basic .moneypreunit').val()+'<span class="totalamt">'+$('#setup input[name="autodis"]').val()+'</span>'+$('.basic .moneysufunit').val()+'</div></div>';
			}
			else{
			}
			if($('.basic input[name="openchar"]').val()=='1'&&$('.basic input[name="charge"]').val()=='1'){
				if($('.basic input[name="chargeeq"]').val()=='2'){//服務費以折扣後之價格計算
					$('#setup input[name="charge"]').val((Number(temptotal)-Number(tempdis))*Number($('.basic input[name="chargenumber"]').val())/100);
				}
				else{//服務費以原價格計算
					$('#setup input[name="charge"]').val(Number(temptotal)*Number($('.bssic input[name="chargenumber"]').val())/100);
				}
				var precision=parseInt($('.basic input[name="accuracy"]').val());//可由設定檔設定精準度(e.g.精準度小數點第二位 填2)
				/*設定檔內可設定使用何種進位*/
				if($('.basic input[name="accuracytype"]').val()=='1'){//四捨五入
					$('#setup input[name="charge"]').val( roundfun($('#setup input[name="charge"]').val(),precision));
				}
				else if($('.basic input[name="accuracytype"]').val()=='2'){//無條件進位
					$('#setup input[name="charge"]').val( ceilfun($('#setup input[name="charge"]').val(),precision));
				}
				else{//無條件捨去
					$('#setup input[name="charge"]').val( floorfun($('#setup input[name="charge"]').val(),precision));
				}
				if($('#setup input[name="charge"]').val()>0){
					res=res+'<div style="width:calc(100% - 10px);margin:10px 0 -10px 0;padding-left:5px;padding-right:5px;overflow:hidden;font-size:20px;"><div style="float:left;">服務費</div><div style="width:75.6px;float:right;font-size:18px;text-align:right;">'+$('.basic .moneypreunit').val()+'<span class="totalamt">'+$('#setup input[name="charge"]').val()+'</span>'+$('.basic .moneysufunit').val()+'</div></div>';
				}
				else{
				}
			}
			else{
			}
			$('#setup input[name="listtotal"]').val(comptotal()-$('#setup input[name="charge"]').val());
			$('#setup input[name="should"]').val(comptotal());
			res=res+'<div style="width:calc(100% - 10px);margin-top:10px;padding-left:5px;padding-right:5px;overflow:hidden;font-size:20px;"><div style="float:left;">總計</div><div style="width:75.6px;float:right;font-size:18px;text-align:right;">'+$('.basic .moneypreunit').val()+'<span class="totalamt">'+comptotal()+'</span>'+$('.basic .moneysufunit').val()+'</div><div style="width:57.6px;height:50px;text-align:right;float:right;font-size:18px;">x<span class="totalqty">'+comptotalqty()+'</span>'+$('.basic .unit').val()+'</div></div>';
			res=res+'</div>';
			$('input[class="type"]').val('itemlist');
			$('input[class="itemtype"]').val('');
			$('#content').css({'padding':'0'});
			$('#content').html(res);
			//$('#keybox .funkey1').prop('id','del');
			//$('#keybox .funkey1').html("<img src='./img/delete.png' style='width:50px;height:50px;'>");
			$('#keybox .funkey2 div:eq(0)').attr('id','sale');
			$('#keybox .funkey2 div:eq(0)').html('結帳');
			if($('.items #item').length==0){
				//$('#keybox .funkey2 #point').css({'border-top':'','border-bottom':'','border-left':'','border-bottom-left-radius':'','border-top-left-radius':''});
				$('#keybox .funkey2 #point').html('');
			}
			else{
				//$('#keybox .funkey2 #point').css({'border-top':'2px solid rgba(26, 26, 26, 0.5)','border-bottom':'2px solid rgba(26, 26, 26, 0.5)','border-left':'2px solid rgba(26, 26, 26, 0.5)','border-bottom-left-radius':'10px','border-top-left-radius':'10px'});
				$('#keybox .funkey2 #point').html('x'+comptotalqty()+$('.basic .unit').val()+'<br>'+$('.basic .moneypreunit').val()+comptotal()+$('.basic .moneysufunit').val());
			}

			if($('.basic input[name="sale"]').val()=='1'&&($('#setup input[name="machine"]').val().length>0&&$('#setup input[name="submachine"]').val().length==0)){
				$('#keybox .funkey2 div:eq(0)').css({'width':'calc(50% - 5px)','display':'block'});
			}
			else{
				$('#keybox .funkey2 div:eq(0)').css({'display':'none'});
			}

			if($('.basic input[name="tempsale"]').val()=='1'){
				$('#keybox .funkey2 div:eq(1)').css({'width':'calc(50% - 5px)','display':'block'});
			}
			else{
				$('#keybox .funkey2 div:eq(1)').css({'display':'none'});
			}
			$('#keybox .funkey2 #point').css({'display':'none'});
			$('#setup input[name="already"]').val('0');
			$('#setup input[name="notyet"]').val(comptotal());
			$('#setup input[name="change"]').val('0');
			$('.salepay input[name="notyet"]').val(comptotal());
		}
	});
	$('#content').on('change','#itemlist #receverdata input[name="memtel"]',function(){
		$('#content #itemlist #receverdata select[name="memname"]').html('');
		if($('#content #itemlist #receverdata input[name="memtel"]').val()==''){
		}
		else{
			if($('.basic input[name="onlinemember"]').val()=='1'){//網路會員
				$.ajax({
					url:'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php',
					method:'post',
					async:false,
					data:{'type':'online','membertype':$('.basic input[name="membertype"]').val(),'company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('#content #receverdata input[name="memtel"]').val()},
					dataType:'json',
					success:function(d){
						console.log(d);
						/*if(d.length==1){
						}
						else{
							//$('#content #itemlist #receverdata select[name="memname"]').append('<option value="create" selected>新增會員</option>');
						}*/
						for(var i=0;i<d.length;i++){
							$('#content #itemlist #receverdata select[name="memname"]').append('<option value="'+d[i]['memno']+';php;'+d[i]['name']+';php;'+d[i]['tel']+'">'+d[i]['name']+'</option>');
						}
						if($('#content #itemlist #receverdata select[name="memname"] option').length>0){
							$('#content #itemlist #receverdata select[name="memname"] option:eq(0)').prop('selected',true);
							var tempmemdata=$('#content #itemlist #receverdata select[name="memname"] option:selected').val().split(';php;');
							$('#setup input[name="memtel"]').val(tempmemdata[2]);
							$('#setup input[name="memno"]').val(tempmemdata[0]);
							$('#setup input[name="memname"]').val(tempmemdata[1]);
						}
						else{
							$('#setup input[name="memtel"]').val('');
							$('#setup input[name="memno"]').val('');
							$('#setup input[name="memname"]').val('');
						}
						//$('#content #itemlist #receverdata input[name="memname"]').val(d[0]['name']);
					},
					error:function(e){
						//console.log(e);
					}
				});
			}
			else{//本地會員
				$.ajax({
					url:'http://'+$('.basic input[name="serverip"]').val()+'/memberapi/getmemdata.ajax.php',
					method:'post',
					async:false,
					data:{'type':'offline','membertype':$('.basic input[name="membertype"]').val(),'company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('#content #receverdata input[name="memtel"]').val()},
					dataType:'json',
					success:function(d){
						/*if(d.length==1){
						}
						else{
							//$('#content #itemlist #receverdata select[name="memname"]').append('<option value="create" selected>新增會員</option>');
						}*/
						for(var i=0;i<d.length;i++){
							$('#content #itemlist #receverdata select[name="memname"]').append('<option value="'+d[i]['memno']+';php;'+d[i]['name']+';php;'+d[i]['tel']+'">'+d[i]['name']+'</option>');
						}
						if($('#content #itemlist #receverdata select[name="memname"] option').length>0){
							$('#content #itemlist #receverdata select[name="memname"] option:eq(0)').prop('selected',true);
							var tempmemdata=$('#content #itemlist #receverdata select[name="memname"] option:selected').val().split(';php;');
							$('#setup input[name="memtel"]').val(tempmemdata[2]);
							$('#setup input[name="memno"]').val(tempmemdata[0]);
							$('#setup input[name="memname"]').val(tempmemdata[1]);
						}
						else{
							$('#setup input[name="memtel"]').val('');
							$('#setup input[name="memno"]').val('');
							$('#setup input[name="memname"]').val('');
						}
					},
					error:function(e){
						//console.log(e);
					}
				});
			}
		}
	});
	$('#content').on('change','#itemlist #receverdata select[name="memname"]',function(){
		var tempmemdata=$('#content #itemlist #receverdata select[name="memname"] option:selected').val().split(';php;');
		$('#setup input[name="memtel"]').val(tempmemdata[2]);
		$('#setup input[name="memno"]').val(tempmemdata[0]);
		$('#setup input[name="memname"]').val(tempmemdata[1]);
	});
	$('#content').on('change','#itemlist #receverdata select[name="date"]',function(){
		if($(this).find('option:selected').val()==$('.basic input[name="date"]').val()){
			$('#content #itemlist #receverdata select[name="nowtime"]').css({'display':'block'});
			$('#content #itemlist #receverdata select[name="nowtime"] option').prop('selected',false);
			$('#content #itemlist #receverdata select[name="nowtime"] option:eq(0)').prop('selected',true);
			$('#content #itemlist #receverdata select[name="time"]').css({'display':'none'});
			$('#content #itemlist #receverdata select[name="time"] option').prop('selected',false);
			$('#content #itemlist #receverdata select[name="time"] option:eq(0)').prop('selected',true);
		}
		else{
			$('#content #itemlist #receverdata select[name="nowtime"]').css({'display':'none'});
			$('#content #itemlist #receverdata select[name="nowtime"] option').prop('selected',false);
			$('#content #itemlist #receverdata select[name="nowtime"] option:eq(0)').prop('selected',true);
			$('#content #itemlist #receverdata select[name="time"]').css({'display':'block'});
			$('#content #itemlist #receverdata select[name="time"] option').prop('selected',false);
			$('#content #itemlist #receverdata select[name="time"] option:eq(0)').prop('selected',true);
		}
	});
	$('#content').on('change','#itemlist #receverdata select[name="listtype"]',function(){
		if($(this).find('option:selected').val()=='1'){//內用
			$('#content #itemlist #receverdata #address').css({'display':'none'});
		}
		else if($(this).find('option:selected').val()=='2'){//外帶
			$('#content #itemlist #receverdata #address').css({'display':'none'});
		}
		else if($(this).find('option:selected').val()=='3'){//外送
			$('#content #itemlist #receverdata #address').css({'display':'table-row'});
		}
		else{//$($(this).find('option:selected').val()=='4'//自取
			$('#content #itemlist #receverdata #address').css({'display':'none'});
		}
	});
	$('#content').on('click','#itemlist .row #check',function(){
		var index=$('#content #itemlist .row #check').index(this);
		$('#content #itemlist .row').css({'background-color':'#ffffff'});
		$('#content #itemlist .row:eq('+index+')').css({'background-color':'#5A748B'});
	});
	/*$('#content').on('click','#itemlist .row #data',function(){
		var index=$('#content #itemlist .row #data').index(this);
		$('#content #itemlist .row').css({'background-color':'#ffffff'});
		$('#content #itemlist .row:eq('+index+')').css({'background-color':'#5A748B'});
		if($('#content #itemlist .row:eq('+index+') #check input[name="checkbox[]"]').prop('checked')==true){
			$('#content #itemlist .row:eq('+index+') #check input[name="checkbox[]"]').prop('checked',false);
		}
		else{
			$('#content #itemlist .row:eq('+index+') #check input[name="checkbox[]"]').prop('checked',true);
		}
	});*/
	$('#content').on('click','#itemlist .row #data .itemfunbox',function(){
		var index=$(this).index('#content #itemlist .row #data .itemfunbox');
		//console.log(index);
		$('.orderlist#funbox #index').val(index);
		$('.orderlist#funbox #itemseq').val($('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val());
		$('.orderlist#funbox #itemno').val($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="no[]"]').val());
		$('.orderlist#funbox #unitpricelink').val($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="mname1[]"]').val());
		$('.orderlist#funbox #unitprice').val($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="unitprice[]"]').val());
		$('.orderlist#funbox #qty').val($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="number[]"]').val());
		$('.orderlist#funbox #tasteno').val($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="taste1[]"]').val());
		$('.orderlist#funbox #tastemoney').val($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="taste1money[]"]').val());
		$('.orderlist#funbox #tastenumber').val($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="taste1number[]"]').val());
		$('.orderlist#funbox #subtotal').val($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="subtotal[]"]').val());
		if($('.items .'+$('#content #itemlist .row:eq('+index+') input[name="itemseq"]').val()+' input[name="templistitem[]"]').length>0){
			$('.orderlist#funbox .edit').css({'display':'none'});
		}
		else{
			$('.orderlist#funbox .edit').css({'display':'block'});
		}
		$('.modal').css({'display':'block'});
		$('.orderlist#funbox').css({'display':'block'});
	});
	$('.orderlist#funbox .delete').click(function(){
		if($('.items .'+$('.orderlist#funbox #itemseq').val()+' input[name="templistitem[]"]').length>0){
			$.ajax({
				url:'../demopos/lib/js/voiditem.ajax.php',
				method:'post',
				async: false,
				data:{'consecnumber':$('#setup input[name="consecnumber"]').val(),'bizdate':$('#setup input[name="bizdate"]').val(),'linenumber':$('.items .'+$('.orderlist#funbox #itemseq').val()+' input[name="linenumber[]"]').val(),'machine':$('#setup input[name="machinetype"]').val()},
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'../demopos/lib/js/print.php',
							method:'post',
							data:{'html':'orderpos voiditem.ajax.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
				},
				error:function(e){
					//console.log(e);
				}
			});
		}
		else{
		}
		$('.items .'+$('.orderlist#funbox #itemseq').val()).remove();
		$('.modal').trigger('click');
		$('#keybox .funkey2 div:eq(0)').prop('id','list');
		$('#keybox .funkey2 #list').trigger('click');
	});
	$('.orderlist#funbox .edit').click(function(){
		$('html').css({'overflow':'hidden'});
		if($('.items .'+$('.orderlist#funbox #itemseq').val()+' input[name="isgroup[]"]').val()=='0'&&$('.items .'+$('.orderlist#funbox #itemseq').val()+' input[name="childtype[]"]').val().length==0){//2021/7/6 非套餐
			$.ajax({
				url:'./lib/js/getdetail.ajax.php',
				method:'post',
				async:false,
				data:{'story':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val(),'itemseq':$('.orderlist#funbox #itemseq').val(),'item':$('.orderlist#funbox #itemno').val(),'unitpricelink':$('.orderlist#funbox #unitpricelink').val(),'unitprice':$('.orderlist#funbox #unitprice').val(),'qty':$('.orderlist#funbox #qty').val(),'tasteno':$('.orderlist#funbox #tasteno').val(),'tastemoney':$('.orderlist#funbox #tastemoney').val(),'tastenumber':$('.orderlist#funbox #tastenumber').val(),'subtotal':$('.orderlist#funbox #subtotal').val()},
				dataType:'html',
				success:function(d){
					$('input[class="type"]').val('detail');
					$('.detail').css({'display':'block'});
					$('.detail #data').html(d);
					$('.detail #footer .money').html($('.detail #data #detail input[name="amt"]').val());
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{//2021/7/6 套餐
			var sendarray='story='+$('.basic input[name="story"]').val()+'&dep='+$('.basic input[name="dep"]').val()+'&itemseq='+$('.orderlist#funbox #itemseq').val()+'&item[]='+$('.orderlist#funbox #itemno').val()+'&unitpricelink[]='+$('.orderlist#funbox #unitpricelink').val()+'&unitprice[]='+$('.orderlist#funbox #unitprice').val()+'&qty='+$('.orderlist#funbox #qty').val()+'&tasteno[]='+$('.orderlist#funbox #tasteno').val()+'&tastemoney[]='+$('.orderlist#funbox #tastemoney').val()+'&tastename[]='+$('.orderlist#funbox #tastename').val()+'&tasteprice[]=&tastenumber[]='+$('.orderlist#funbox #tastenumber').val()+'&subtotal='+$('.orderlist#funbox #subtotal').val();
			
			for(var i=(parseInt($('.orderlist#funbox #itemseq').val())+1);i<=$('.items #item:eq('+(parseInt($('.items #item').length)-1)+')').prop('class');i++){
				if($('.items .'+i).length>0&&$('.items .'+i+' input[name="order[]"]').val()!='－'){//非套餐選項
					break;
				}
				else if($('.items .'+i).length>0){//套餐選項
					sendarray += '&item[]='+$('.items .'+i+' input[name="no[]"]').val();
					sendarray += '&unitpricelink[]='+$('.items .'+i+' input[name="mname1[]"]').val();
					sendarray += '&unitprice[]='+$('.items .'+i+' input[name="unitprice[]"]').val();
					sendarray += '&tasteno[]='+$('.items .'+i+' input[name="taste1[]"]').val();
					sendarray += '&tastemoney[]='+$('.items .'+i+' input[name="taste1money[]"]').val();
					sendarray += '&tastename[]='+$('.items .'+i+' input[name="taste1name[]"]').val();
					sendarray += '&tasteprice[]='+$('.items .'+i+' input[name="taste1price[]"]').val();
					sendarray += '&tastenumber[]='+$('.items .'+i+' input[name="taste1number[]"]').val();
				}
				else{//不存在該順位品項(移除已點產品)
				}
			}

			$.ajax({
				url:'./lib/js/getdetail.ajax.php',
				method:'post',
				async:false,
				data:sendarray,
				dataType:'html',
				success:function(d){
					$('input[class="type"]').val('detail');
					$('.detail').css({'display':'block'});
					$('.detail').animate({
						top:'65px'
					},500);
					$('.detail #data').html(d);
					//$('.detail #footer .money').html($('.detail #data #detail input[name="amt"]').val());
					compsub();
				},
				error:function(e){
					//console.log(e);
				}
			});
		}
		$('.detail #data').scrollTop(0);
		$('.modal').trigger('click');
	});
	$('.modal').click(function(){
		$('.modal').css({'display':'none'});
		if($('.viewbasic').css('display')=='block'){
			$('.viewbasic').css({'display':'none'});
			$('.viewbasic #basicdata').html('');
		}
		else{
		}
		if($('.orderlist#funbox').css('display')=='block'){
			$('.orderlist#funbox').css({'display':'none'});
			$('.orderlist#funbox #index').val('');
			$('.orderlist#funbox #itemseq').val('');
			$('.orderlist#funbox #itemno').val('');
			$('.orderlist#funbox #unitpricelink').val('');
			$('.orderlist#funbox #unitprice').val('');
			$('.orderlist#funbox #qty').val('');
			$('.orderlist#funbox #tasteno').val('');
			$('.orderlist#funbox #tastemoney').val('');
		}
		else{
		}
		if($('.setwin').css('display')=='block'){
			$('.setwin').css({'display':'none'});
		}
		else{
		}
		if($('.pw').css('display')=='block'){
			$('.pw input[name="pw"]').val('');
			$('.pw input[name="newpw1"]').val('');
			$('.pw input[name="newpw2"]').val('');
			$('.pw input[name="pw"]').focus();
			$('.pw').css({'display':'none'});
		}
		else{
		}
		if($('.message1').css('display')=='block'){
			$('.message1').css({'display':'none'});
			$('.message1 #text').html('');
			//if(pw.dialog('isOpen')){
			/*if($('.pw').css('display')=='block'){
				$('.pw input[name="pw"]').val('');
				$('.pw input[name="newpw1"]').val('');
				$('.pw input[name="newpw2"]').val('');
				$('.pw input[name="pw"]').focus();
			}
			else{
			}*/
			if($('input[class="type"]').val()=='itemlist'){
				if($('.items #item').length==0){
					//$('#keybox .funkey2 #point').css({'border-top':'','border-bottom':'','border-left':'','border-bottom-left-radius':'','border-top-left-radius':''});
					$('#keybox .funkey2 #point').html('');
				}
				else{
					//$('#keybox .funkey2 #point').css({'border-top':'2px solid rgba(26, 26, 26, 0.5)','border-bottom':'2px solid rgba(26, 26, 26, 0.5)','border-left':'2px solid rgba(26, 26, 26, 0.5)','border-bottom-left-radius':'10px','border-top-left-radius':'10px'});
					$('#keybox .funkey2 #point').html('x'+comptotalqty()+$('.basic .unit').val()+'<br>'+$('.basic .moneypreunit').val()+comptotal()+$('.basic .moneysufunit').val());
				}
				$('#title .return').trigger('click');
			}
			else{
			}
		}
		else{
		}
		if($('.message2').css('display')=='block'){
			$('.message2').css({'display':'none'});
			$('.message2 input[name="msgtype"]').val('');
			$('.message2 #text').html('');
		}
		else{
		}
		if($('.message3').css('display')=='block'){
			$('.message3 input[name="name"]').val('');
			$('.message3 input[name="phone"]').val('');
			$('.message3 input[name="address"]').val('');
			$('.message3').css({'display':'none'});
		}
		else{
		}
		if($('.salepay').css('display')=='block'){
			$('.salepay input[name="money"]').val('');
			$('.salepay input[name="change"]').val('0');
			$('.salepay select option').prop('selected',false);
			$('.salepay select option:eq(0)').prop('selected',true);
			$('#setup input[name="cashmoney"]').val('0');
			$('#setup input[name="cash"]').val('0');
			$('#setup input[name="other"]').val('0');
			$('#setup input[name="otherstring"]').val('');
			$('#setup input[name="otherfix"]').val('0');
			$('#setup input[name="already"]').val('0');
			$('#setup input[name="notyet"]').val(comptotal());
			$('#setup input[name="change"]').val('0');
			$('.salepay').css({'display':'none'});
		}
		else{
		}
		if($('.cremem').css('display')=='block'){
			$('.cremem #memtel').val('');
			$('.cremem #memname').val('');
			$('.cremem #memaddress').val('');
			$('.cremem').css({'display':'none'});
		}
		else{
		}
	});
	/*$('#keybox').on('click','#del',function(){
		if($('#content #itemlist .row #check input[name="checkbox[]"]:checked').length>0){
			for(var i=$('#content #itemlist .row').length;i>=0;i--){
				if($('#content #itemlist .row:eq('+i+') #check input[name="checkbox[]"]').prop('checked')==true){
					$('.items .'+$('#content #itemlist .row:eq('+i+') #check input[name="checkbox[]"]').val()).remove();
					$('#content #itemlist .row:eq('+i+')').remove();
				}
				else{
				}
			}
			$('#content #itemlist .totalamt').html(comptotal());
		}
		else{
		}
		if($('#content #itemlist .row').length==0){
			$('#title .return').trigger('click');
			$('#keybox .funkey2 #point').css({'background-color':'','border-radius':''})
		}
		else{
		}
	});*/
	$('.message1 #check').click(function(){
		$('.message1 #text').html('');
		//msg1.dialog('close');
		$('.message1').css({'display':'none'});
		if($('.setwin').css('display')=='block'||$('.message2').css('display')=='block'||$('.message3').css('display')=='block'||$('.message4').css('display')=='block'||$('.pw').css('display')=='block'){
		}
		else{
			$('.modal').css({'display':'none'});
		}
		//if(pw.dialog('isOpen')){
		if($('.pw').css('display')=='block'){
			$('.pw input[name="pw"]').val('');
			$('.pw input[name="newpw1"]').val('');
			$('.pw input[name="newpw2"]').val('');
			$('.pw input[name="pw"]').focus();
		}
		else{
		}
		if($('input[class="type"]').val()=='itemlist'){
			if($('.items #item').length==0){
				//$('#keybox .funkey2 #point').css({'border-top':'','border-bottom':'','border-left':'','border-bottom-left-radius':'','border-top-left-radius':''});
				$('#keybox .funkey2 #point').html('');
			}
			else{
				//$('#keybox .funkey2 #point').css({'border-top':'2px solid rgba(26, 26, 26, 0.5)','border-bottom':'2px solid rgba(26, 26, 26, 0.5)','border-left':'2px solid rgba(26, 26, 26, 0.5)','border-bottom-left-radius':'10px','border-top-left-radius':'10px'});
				$('#keybox .funkey2 #point').html('x'+comptotalqty()+$('.basic .unit').val()+'<br>'+$('.basic .moneypreunit').val()+comptotal()+$('.basic .moneysufunit').val());
			}
			$('#title .return').trigger('click');
		}
		else{
		}
	});
	$('#keybox').on('click','#sale',function(){
		if($('.items #item').length>0){
			/*$('.message2 input[name="msgtype"]').val('msg');
			$('.message2 #text').html('確認送出選購清單？');
			//msg2.dialog('open');
			$('.message2').css({'display':'block'});*/
			$('.salepay').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else{
			$('.message1 #text').html('選購清單變數錯誤，請重整頁面。');
			//msg1.dialog('open');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
	});
	$('#keybox').on('click','#tempsale',function(){//暫結
		if($('.items #item').length>0){
			$('.message2 input[name="msgtype"]').val('tempmsg');
			$('.message2 #text').html('確認暫結選購清單？');
			//msg2.dialog('open');
			$('.message2').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else{
			$('.message1 #text').html('選購清單變數錯誤，請重整頁面。');
			//msg1.dialog('open');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
	});
	$('.message2 #cancel').click(function(){
		if($('.message2 input[name="msgtype"]').val()=='class'){
		}
		else{
			$('.modal').css({'display':'none'});
		}
		$('.message2').css({'display':'none'});
		$('.message2 input[name="msgtype"]').val('');
		$('.message2 #text').html('');
		//msg2.dialog('close');
	});
	$('.message2 #check').click(function(){
		$('.message2 #check').prop('disabled',true);
		if($('.message2 input[name="msgtype"]').val()=='tempmsg'){//暫結
			$('#setup input[name="listtotal"]').prop('disabled',true);
			var array=$('.items, #setup').serialize();
			array=array+'&story='+$('.basic input[name="story"]').val()+'&dep='+$('.basic input[name="dep"]').val();
			//console.log(array);
			$.ajax({
				url:'../demopos/lib/js/checkopen.ajax.php',
				method:'post',
				async:false,
				data:{'machinetype':$('#setup input[name="machinetype"]').val()},
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'../demopos/lib/js/print.php',
							method:'post',
							data:{'html':'orderpos temp checkopen.ajax.php '+d},
							dataType:'html',
							success:function(d){/*console.log(d);*/},
							error:function(e){/*console.log(e);*/}
						});
					}
					else{
					}
					//console.log(d);
					if(d=='success'){
						$('#setup input[name="invsalemoney"]').val($('#setup input[name="total"]').val());
						for(var i=0;i<$('.items #item input[name="insaleinv[]"]').length;i++){
							if($('.items #item input[name="insaleinv[]"]:eq('+i+')').val()=='0'){
								$('#setup input[name="invsalemoney"]').val(Number($('#setup input[name="invsalemoney"]').val())-Number($('.items #item input[name="insaleinv[]"]:eq('+i+')').val()));
							}
							else{
							}
						}
						//console.log($('.order#order #tabs4 form[data-id="listform"] input[name="invsalemoney"]').val());
						/*if($('.order#order #MemberBill #tabs4 form[data-id="listform"] .label').length==0){//手機POS不會有未點選餐點且點選暫結的狀況
							if($('.order#order .initsetting #controltable').val()=='1'&&$('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val()!=''){
								emptyitemsvoidlist.dialog('open');
							}
							else{
								var nowbizdate=$('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val();
								$.ajax({
									url:'./lib/js/create.voidtempdb.php',
									method:'post',
									async: false,
									data:{'bizdate':$('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val(),'consecnumber':$('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val(),'tablenumber':$('.order#order #tabs4 form[data-id="listform"] input[name="tablenumber"]').val(),'machine':$('.order#order .companydata #terminalnumber').val()},
									dataType:'html',
									success:function(d){
										if(d.length>20){
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												data:{'html':'temp create.voidtempdb.php '+d},
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
								if($('.order#order .initsetting #secview').val()=='1'){
									$.ajax({
										url:'../secview/cleartemp.ajax.php',
										method:'post',
										async: false,
										data:{'machinename':$('.order#order .companydata #terminalnumber').val()},
										dataType:'html',
										success:function(d){
											if(d.length>20){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'temp cleartemp.ajax.php '+d},
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
								if($('.order#order .initsetting #controltable').val()=='1'){
									$.ajax({
										url:'./lib/js/changetabini.ajax.php',
										method:'post',
										async:false,
										data:{'bizdate':$('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val(),'tabnum':$('.order#order #tabs4 form[data-id="listform"] input[name="tablenumber"]').val(),'consecnumber':$('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val(),'listtype':$('.order#order #tabs4 form[data-id="listform"] input[name="listtype"]').val(),'machinename':$('.order#order .companydata #terminalnumber').val()},
										dataType:'html',
										success:function(d){
											console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});

									inittable.dialog('open');
									ClearWindow('','');
								}
								else{
									ClearWindow('','');
								}
								$('.order#order #billfun1 button:eq(4)').prop('disabled',false);
								if($('.order#order .initsetting #controltable').val()=='1'){
									var buttype='b';
								}
								else{
									var buttype='a';
								}
								$.ajax({
									url:'./lib/js/change.button4.php',
									method:'post',
									data:{'type':buttype},
									dataType:'json',
									success:function(d){
										if($('.order#order #billfun #billfun1 button:eq(4) #name1').length>0){
											$('.order#order #billfun #billfun1 button:eq(4) #name1').html(d[0]);
										}
										else{
										}
										if($('.order#order #billfun #billfun1 button:eq(4) #name2').length>0){
											$('.order#order #billfun #billfun1 button:eq(4) #name2').html(d[1]);
										}
										else{
										}
										if($('.order#order .initsetting #controltable').val()=='1'&&$('.order#order .initsetting #opentemp').val()=='0'){
											if(buttype=='a'){
												$('.order#order #billfun #billfun1 button:eq(4)').prop('disabled',true);
											}
											else{
												$('.order#order #billfun #billfun1 button:eq(4)').prop('disabled',false);
											}
										}
										else{
										}
										$('.order#order #billfun #billfun1 button:eq(4)').val(d[2]);
									},
									error:function(e){
										console.log(e);
									}
								});
							}
						}
						else{*/
							/*if(($('.order#order #tabs4 form[data-id="listform"] input[name="listtype"]').val()==3||$('.order#order #tabs4 form[data-id="listform"] input[name="listtype"]').val()==4)&&$('.order#order .initsetting #reserve').val()==1){//開啟電子發票且暫結可以開發票//手機POS不做預約單
								var total=0,subtotal=0,qty=0,temptotal=0,tempdis=0;
								$.each($('.order#order #tabs4 form[data-id="listform"] .label'),function(index,value){
									total=Number(total)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
									subtotal=Number(subtotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="subtotal[]"]').val()));
									qty=Number(qty)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="qty[]"]').val()));
									if($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="needcharge[]"]').val()=='1'){
										temptotal=Number(temptotal)+(Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="money[]"]').val())*Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="number[]"]').val()));
										tempdis=Number(tempdis)+Number($('.order#order #tabs4 form[data-id="listform"] .label:eq('+index+') input[name="discount[]"]').val());
									}
									else{
									}
								});
								if($('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').length>0&&Number($('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val())==''){
									if($('.order#order .initsetting #openchar').val()=='1'&&$('.order#order .initsetting #charge').val()=='1'){
										if($('.order#order .initsetting #chargeeq').val()=='2'){//服務費以折扣後之價格計算
											$('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val((Number(temptotal)-Number(tempdis))*Number($('.order#order .initsetting #chargenumber').val())/100);
										}
										else{//服務費以原價格計算
											$('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val(Number(temptotal)*Number($('.order#order .initsetting #chargenumber').val())/100);
										}
										var precision=parseInt($('.order#order .initsetting #accuracy').val());//可由設定檔設定精準度(e.g.精準度小數點第二位 填2)
										//設定檔內可設定使用何種進位
										if($('.order#order .initsetting #accuracytype').val()=='1'){//四捨五入
											$('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val( roundfun($('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val(),precision));
										}
										else if($('.order#order .initsetting #accuracytype').val()=='2'){//無條件進位
											$('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val( ceilfun($('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val(),precision));
										}
										else{//無條件捨去
											$('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val( floorfun($('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val(),precision));
										}
									}
									else{
									}
								}
								else{
								}
								var autodis=0;
								if($('.order#order .initsetting #autodis').val()=='1'){//開啟自動優惠
									$.ajax({
										url:'./lib/js/compdis.ajax.php',
										method:'post',
										async: false,
										data:$('.order#order #tabs4 form[data-id="listform"]').serialize(),
										dataType:'html',
										success:function(d){
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
											//console.log(d);
											var temp=d.split(';');
											autodis=temp[0];
										},
										error:function(e){
											//console.log(e)
										}
									});
								}
								else{
								}
								var d=new Date(),newd=new Date();
								newd.setDate(newd.getDate()+28);
								var thtml="<table style='widht:100%;height:calc(75% - 1px);margin:0 1px 1px 1px;'><tr><td>金額</td><td>"+(Number(subtotal)-Number(autodis)+Number($('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val()))+"</td></tr><tr><td>日期</td><td>";
								thtml=thtml+"<select class='needsclick' class='needsclick' style='width:150px;' name='date'><option value='0' ";
								if($('.order#order #banner #type input[name="reservedatetime"]').length>0&&$.trim($('.order#order #banner #type input[name="reservedatetime"]').val()).length>0){
									var temp=$('.order#order #banner #type input[name="reservedatetime"]').val().toString().split(";");
									var seltag=temp[1];
									var selyy=temp[0].substr(0,4),selmm=temp[0].substr(4,2),seldd=temp[0].substr(6,2),selhour=temp[0].substr(8,2),selmin=temp[0].substr(10,2);
								}
								else{
									var seltag='',selyy='',selmm='',seldd='',selhour='',selmin='';
									thtml=thtml+"selected";
								}
								thtml=thtml+">選擇日期</option>";
								var tempd=new Date(d);
								var wd=tempd.getDay();
								var dd=tempd.getDate();
								if(dd.toString().length<2){
									dd="0"+dd;
								}
								else{
								}
								var mm=tempd.getMonth()+1;
								if(mm.toString().length<2){
									mm="0"+mm;
								}
								else{
								}
								var yy=tempd.getFullYear();
								var newdd=newd.getDate();
								if(newdd.toString().length<2){
									newdd="0"+newdd;
								}
								else{
								}
								var newmm=newd.getMonth()+1;
								if(newmm.toString().length<2){
									newmm="0"+newmm;
								}
								else{
								}
								var newyy=newd.getFullYear();
								var newdatetime=(newyy+'/'+newmm+'/'+newdd);
								do{
									thtml=thtml+"<option value='"+yy+mm+dd+"' ";
									if((yy+mm+dd)==(selyy+selmm+seldd)){
										thtml=thtml+"selected";
									}
									else{
									}
									thtml=thtml+">"+mm+'/'+dd;
									if(wd==0){
										thtml=thtml+'(日)';
									}
									else if(wd==1){
										thtml=thtml+'(一)';
									}
									else if(wd==2){
										thtml=thtml+'(二)';
									}
									else if(wd==3){
										thtml=thtml+'(三)';
									}
									else if(wd==4){
										thtml=thtml+'(四)';
									}
									else if(wd==5){
										thtml=thtml+'(五)';
									}
									else{
										thtml=thtml+'(六)';
									}
									thtml=thtml+"</option>";
									tempd.setDate(tempd.getDate()+1);
									yy=tempd.getFullYear();
									mm=tempd.getMonth()+1;
									if(mm.toString().length<2){
										mm="0"+mm;
									}
									else{
									}
									dd=tempd.getDate();
									if(dd.toString().length<2){
										dd="0"+dd;
									}
									else{
									}
									wd=tempd.getDay();
								}while((yy+'/'+mm+'/'+dd)!=newdatetime);
								thtml=thtml+"</select>";
								thtml=thtml+"</td></tr><tr><td>時間</td><td><select class='needsclick' style='width: 80px;' name='hour'>";
								for(var hour=8;hour<25;hour++){
									thtml=thtml+"<option value='";
									if(hour.toString().length<2){
										thtml=thtml+"0"+hour.toString()+"' ";
										if(("0"+hour.toString())==selhour.toString()){
											thtml=thtml+"selected";
										}
										else{
										}
										thtml=thtml+">0"+hour.toString();
									}
									else{
										if(hour==24){
											hour="00";
										}
										else{
										}
										thtml=thtml+hour.toString()+"' ";
										if(hour.toString()==selhour.toString()){
											thtml=thtml+"selected";
										}
										else{
										}
										thtml=thtml+">"+hour.toString();
										if(hour=="00"){
											hour=24;
										}
										else{
										}
									}
									thtml=thtml+"</option>";
								}
								thtml=thtml+"</select>：<select class='needsclick' style='width: 80px;' name='min'>";
								for(var min=0;min<12;min++){
									thtml=thtml+"<option value='";
									if((min*5).toString().length<2){
										thtml=thtml+"0"+(min*5).toString()+"' ";
										if(("0"+(min*5).toString())==selmin.toString()){
											thtml=thtml+"selected";
										}
										else{
										}
										thtml=thtml+">"+"0"+(min*5).toString();
									}
									else{
										thtml=thtml+(min*5).toString()+"' ";
										if((min*5).toString()==selmin.toString()){
											thtml=thtml+"selected";
										}
										else{
										}
										thtml=thtml+">"+(min*5).toString();
									}
									thtml=thtml+"</option>";
								}
								thtml=thtml+"</select></td></tr><tr><td>貼紙</td><td><label><input type='checkbox' style='zoom:1.5;' name='printtag' ";
								if(seltag==''||seltag=='0'){
								}
								else{
									thtml=thtml+"checked";
								}
								thtml=thtml+">出貼紙</label></td></tr></table>";
								$('.temptoinv').html(thtml);
								$('.temptoinv').append("<button id='reserve' style='height:calc(25% - 1px);margin:1px 1px 0 1px;' value='開立預約單'>開立預約單</button>");
								$('.temptoinv').append("<button id='continue' style='height:calc(25% - 1px);margin:1px 1px 0 1px;' value='暫結出單'>暫結出單</button>");
								$('.temptoinv').append("<button id='cancel' style='height:calc(25% - 1px);margin:1px 1px 0 1px;' value='取消'>取消</button>");
								temptoinv.dialog('option','height',500);
								temptoinv.dialog('open');
							}
							else{*/
								/*if($('.order#order .initsetting #secview').val()=='1'){//手機POS不做客顯
									$.ajax({
										url:'../secview/cleartemp.ajax.php',
										method:'post',
										async: false,
										data:{'machinename':$('.order#order .companydata #terminalnumber').val()},
										dataType:'html',
										success:function(d){
											if(d.length>20){
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													data:{'html':'temp cleartemp.ajax.php '+d},
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
								}
								else{
								}*/
								var total=0;
								var subtotal=0;
								var qty=0;
								var temptotal=0;
								var tempdis=0;
								$.each($('.items #item'),function(index,value){
									total=Number(total)+(Number($('.items #item:eq('+index+') input[name="money[]"]').val())*Number($('.items #item:eq('+index+') input[name="number[]"]').val()));
									subtotal=Number(subtotal)+(Number($('.items #item:eq('+index+') input[name="subtotal[]"]').val()));
									qty=Number(qty)+(Number($('.items #item:eq('+index+') input[name="number[]"]').val()));
									if($('.items #item:eq('+index+') input[name="needcharge[]"]').val()=='1'){
										temptotal=Number(temptotal)+(Number($('.items #item:eq('+index+') input[name="money[]"]').val())*Number($('.items #item:eq('+index+') input[name="number[]"]').val()));
										tempdis=Number(tempdis)+Number($('.items #item:eq('+index+') input[name="discount[]"]').val());
									}
									else{
									}
								});
								//手機POS服務費計算已提前至前面步驟完成
								/*if($('#setup input[name="charge"]').length>0&&Number($('#setup input[name="charge"]').val())==0){
									if($('.basic input[name="openchar"]').val()=='1'&&$('.basic input[name="charge"]').val()=='1'){
										if($('.basic input[name="chargeeq"]').val()=='2'){//服務費以折扣後之價格計算
											$('#setup input[name="charge"]').val((Number(temptotal)-Number(tempdis))*Number($('.basic input[name="chargenumber"]').val())/100);
										}
										else{//服務費以原價格計算
											$('#setup input[name="charge"]').val(Number(temptotal)*Number($('.bssic input[name="chargenumber"]').val())/100);
										}
										var precision=parseInt($('.basic input[name="accuracy"]').val());//可由設定檔設定精準度(e.g.精準度小數點第二位 填2)
										//設定檔內可設定使用何種進位
										if($('.basic input[name="accuracytype"]').val()=='1'){//四捨五入
											$('#setup input[name="charge"]').val( roundfun($('#setup input[name="charge"]').val(),precision));
										}
										else if($('.basic input[name="accuracytype"]').val()=='2'){//無條件進位
											$('#setup input[name="charge"]').val( ceilfun($('#setup input[name="charge"]').val(),precision));
										}
										else{//無條件捨去
											$('#setup input[name="charge"]').val( floorfun($('#setup input[name="charge"]').val(),precision));
										}
									}
									else{
									}
								}
								else{
								}*/
								var consecnumber='';
								//手機POS不做錢都錄
								/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
									var posdvrfile='';
								}
								else{
								}*/
								//$('.order#order #billfun1 button:eq(4)').prop('disabled',true);
								//var dbrequery='';
								$.ajax({
									url:'../demopos/lib/js/create.tempdb.php',
									method:'post',
									async: false,
									data:array,
									dataType:'html',
									success:function(d){
										if(d.length>20){
											$.ajax({
												url:'../demopos/lib/js/print.php',
												method:'post',
												data:{'html':'orderpos temp create.tempdb.php '+d},
												dataType:'html',
												success:function(d){/*console.log(d);*/},
												error:function(e){/*console.log(e);*/}
											});
										}
										else{
										}//database is locked
										//dbrequery=d;
										/*if(dbrequery.match(/database is locked/g)){
											//產生db locked
										}
										else{*/
											var tempd=d.split('-');
											consecnumber=tempd[1];
											if($('#setup input[name="consecnumber"]').val()==''){
												$('#setup input[name="saleno"]').val(tempd[0]);
												$('#setup input[name="consecnumber"]').val(('000000'+tempd[1]).substr(-6));
												array=$('.items, #setup').serialize();
												array=array+'&story='+$('.basic input[name="story"]').val()+'&dep='+$('.basic input[name="dep"]').val();
											}
											else{
											}
											/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
												posdvrfile=tempd[2];
											}
											else{
											}*/
										//}
										//console.log(d);
									},
									error:function(e){
										//console.log(e);
									}
								});
								/*if(dbrequery.match(/database is locked/g)){
									//產生db locked
									dblock.dialog('open');
								}
								else{*/
									/*手機POS不使用錢都錄借接*/
									//console.log(posdvrfile);
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
											error:function(e){console.log(e);}
										});
										if(typeof api_sendmessage_posdvr!=="undefined"&&typeof api_sendmessage_posdvr==="function"){
											var res=api_sendmessage_posdvr(posdvrfile);
											//console.log(res);
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
									/*if($('.order#order .initsetting #ticketlisttype').val().match($('.order#order #tabs4 form[data-id="listform"] input[name="listtype"]').val())&&($('.order#order .initsetting #ticket').val()=='1'||$('.order#order .initsetting #ticket').val()=='3')){//出"兌換卷"//手機POS不做兌換卷
										$.ajax({
											url:'./lib/js/create.ticket.php',
											method:'post',
											async:false,
											data:$('#tabs4 form[data-id="listform"]').serialize(),
											dataType:'html',
											success:function(d){
												if(d.length>20){
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														data:{'html':'temp create.ticket.php '+d},
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
												consecnumber=d;
											},
											error:function(e){
												console.log(e);
											}
										});
									}
									else{
									}*/
									//手機POS自動優惠已提前至前面步驟完成
									/*if($('.basic input[name="autodis"]').val()=='1'){//開啟自動優惠
										$.ajax({
											url:'../demopos/lib/js/compdis.ajax.php',
											method:'post',
											async: false,
											data:array,
											dataType:'html',
											success:function(d){
												//console.log(d);
												if(d.length>20){
													$.ajax({
														url:'../demopos/lib/js/print.php',
														method:'post',
														data:{'html':'orderpos temp compdis.ajax.php '+d},
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
												var temp=d.split(';');
												if($.isNumeric(temp[0])){
													$.ajax({
														url:'../demopos/lib/js/wttempdis.ajax.php',
														method:'post',
														async: false,
														data:{'bizdate':$('#setup input[name="bizdate"]').val(),'consecnumber':consecnumber,'autodis':temp[0],'autodiscontent':temp[1],'autodispermoney':temp[2]},
														dataType:'html',
														success:function(d){
															if(d.length>20){
																$.ajax({
																	url:'../demopos/lib/js/print.php',
																	method:'post',
																	data:{'html':'orderpos temp wttempdis.ajax.php '+d},
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
									if($('#setup input[name="consecnumber"]').val()==''){
									}
									else{
										$.ajax({
											url:'../demopos/lib/js/create.voidlist.php',
											method:'post',
											async: false,
											data:{'bizdate':$('#setup input[name="bizdate"]').val(),'consecnumber':$('#setup input[name="consecnumber"]').val(),'tablenumber':$('#setup input[name="tablenumber"]').val(),'machine':$('#setup input[name="machinetype"]').val()},
											dataType:'html',
											success:function(d){
												if(d.length>20){
													$.ajax({
														url:'../demopos/lib/js/print.php',
														method:'post',
														data:{'html':'orderpos temp create.voidlist.php '+d},
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
									}
									$.ajax({
										url:'../demopos/lib/js/create.list.php',
										method:'post',
										async: false,
										data:array,
										dataType:'html',
										success:function(d){
											if(d.length>20){
												$.ajax({
													url:'../demopos/lib/js/print.php',
													method:'post',
													data:{'html':'orderpos temp create.list.php '+d},
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
											//console.log(e);
										}
									});
									/*if($('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val()==''){//手機POS不做退菜單
									}
									else{
										$.ajax({
											url:'./lib/js/create.voidkitchen.php',
											method:'post',
											async: false,
											data:{'bizdate':$('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val(),'consecnumber':$('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val(),'tablenumber':$('.order#order #tabs4 form[data-id="listform"] input[name="tablenumber"]').val(),'machine':$('.order#order .companydata #terminalnumber').val()},
											dataType:'html',
											success:function(d){
												if(d.length>20){
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														data:{'html':'temp create.voidkitchen.php '+d},
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
									}*/
									$.ajax({
										url:'../demopos/lib/js/create.kitchen.php',
										method:'post',
										async: false,
										data:array,
										dataType:'html',
										success:function(d){
											//console.log(d);
											if(d.length>20){
												$.ajax({
													url:'../demopos/lib/js/print.php',
													method:'post',
													data:{'html':'orderpos temp create.kitchen.php '+d},
													dataType:'html',
													success:function(d){/*console.log(d);*/},
													error:function(e){/*console.log(e);*/}
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
										url:'../demopos/lib/js/create.tag.php',
										method:'post',
										async: false,
										data:array,
										dataType:'html',
										success:function(d){
											if(d.length>20){
												$.ajax({
													url:'../demopos/lib/js/print.php',
													method:'post',
													data:{'html':'orderpos temp create.tag.php '+d},
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
											//console.log(e);
										}
									});
									/*if($('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val()==''){//手機POS不做
									}
									else{
										$.ajax({
											url:'./lib/js/create.voidtempdb.php',
											method:'post',
											async: false,
											data:{'bizdate':$('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val(),'consecnumber':$('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val(),'tablenumber':$('.order#order #tabs4 form[data-id="listform"] input[name="tablenumber"]').val(),'machine':$('.order#order .companydata #terminalnumber').val()},
											dataType:'html',
											success:function(d){
												if(d.length>20){
													$.ajax({
														url:'./lib/js/print.php',
														method:'post',
														data:{'html':'temp create.voidtempdb.php '+d},
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
									}*/
									/*$.ajax({
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
									});*/
									/*if($('.order#order .initsetting #controltable').val()=='1'){//手機POS無論哪一種情況，只會跳轉至seltab.php
										inittable.dialog('open');
										ClearWindow('','');
									}
									else{
										if($('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="usercode"]').val().length>0){
											ClearWindow('','');
										}
										else{
											ClearWindow('','');
										}
									}*/
									/*$('.order#order #billfun1 button:eq(4)').prop('disabled',false);
									if($('.order#order .initsetting #controltable').val()=='1'){
										var buttype='b';
									}
									else{
										var buttype='a';
									}*/
									/*$.ajax({
										url:'./lib/js/change.button4.php',
										method:'post',
										data:{'type':buttype},
										dataType:'json',
										success:function(d){
											if($('.order#order #billfun #billfun1 button:eq(4) #name1').length>0){
												$('.order#order #billfun #billfun1 button:eq(4) #name1').html(d[0]);
											}
											else{
											}
											if($('.order#order #billfun #billfun1 button:eq(4) #name2').length>0){
												$('.order#order #billfun #billfun1 button:eq(4) #name2').html(d[1]);
											}
											else{
											}
											if($('.order#order .initsetting #controltable').val()=='1'&&$('.order#order .initsetting #opentemp').val()=='0'){
												if(buttype=='a'){
													$('.order#order #billfun #billfun1 button:eq(4)').prop('disabled',true);
												}
												else{
													$('.order#order #billfun #billfun1 button:eq(4)').prop('disabled',false);
												}
											}
											else{
											}
											$('.order#order #billfun #billfun1 button:eq(4)').val(d[2]);
										},
										error:function(e){
											console.log(e);
										}
									});*/
									$('#setup').submit();
								//}
							//}
						//}
					}
					else{
						alert('目前尚未開班，請確認是否開班或重啟系統。');
					}
				},
				error:function(e){
					//console.log(e);
				}
			});
		}
		else if($('.message2 input[name="msgtype"]').val()=='class'){//交班
			$.ajax({
				url:'../demopos/lib/js/close.ajax.php',
				method:'post',
				async: false,
				data:{'usercode':$('.basic input[name="usercode"]').val(),'username':$('.basic input[name="username"]').val(),'machinetype':'m1'},
				dataType:'html',
				success:function(d){
					if(d.length>20){
						$.ajax({
							url:'../demopos/lib/js/print.php',
							method:'post',
							data:{'html':'close.ajax.php '+d},
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
						$.ajax({
							url:'../demopos/lib/js/change.class.php',
							method:'post',
							async: false,
							data:{'type':'isopen','machinetype':'m1','whopass':'orderpos-message2-close.ajax.php'},
							dataType:'html',
							success:function(d){
								if(d.length>20){
									$.ajax({
										url:'../demopos/lib/js/print.php',
										method:'post',
										data:{'html':'change.class.php '+d},
										dataType:'html',
										success:function(d){/*console.log(d);*/},
										error:function(e){/*console.log(e);*/}
									});
								}
								else{
								}
								$('.setwin #closeclass').css('cursor','');
								$('.setwin #closeclass').css('-webkit-filter','grayscale(100%)');
								$('.setwin #closeclass').css('filter','grayscale(100%)');
								$('.setwin #closeclass').prop('id','discloseclass');
								$('.setwin #disopenclass').prop('id','openclass');
								$('.setwin #openclass').css('cursor','pointer');
								$('.setwin #openclass').css('-webkit-filter','');
								$('.setwin #openclass').css('filter','');
								$('.message2 #cancel').trigger('click');
								//console.log(d);
								/*$('.order#order #MemberBill #billfun #billfun1button').prop('disabled',true);
								//$billfun.tabs('disable','#billfun1');
								$('.order#order #billfun #billfun2 .outmoney').prop('disabled',true);
								$('.order#order #billfun #billfun2 .open').prop('disabled',false);
								$('.order#order #MemberBill #billfun #billfun2button').trigger('click');
								sysmeg4.dialog('close');
								if($('.order#order .initsetting #controltable').val()=='1'){
									$('.inittable .funcmap #tablesplit').prop('disabled',true);
									$('.inittable .funcmap #combine').prop('disabled',true);
									$('.inittable .funcmap #tablecombine').prop('disabled',true);
									$('.inittable .funcmap #changetable').prop('disabled',true);
									$('.funbox #open').prop('disabled',false);
									$('.funbox #return').prop('disabled',false);
									funbox.dialog('close');
								}
								else{
								}*/
								$.ajax({
									url:'../demopos/lib/js/shift.paper.php',
									method:'post',
									async: false,
									data:{'machinename':'m1','zcounter':d},
									dataType:'html',
									success:function(d){
										if(d.length>20){
											$.ajax({
												url:'../demopos/lib/js/print.php',
												method:'post',
												data:{'html':'shift.paper.php '+d},
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
											data:{'cmd':'m1-upload_m1'},
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
		}
		else if($('.message2 input[name="msgtype"]').val()=='clear'){
			$('.message2 input[name="msgtype"]').val('');
			$('.message2 #text').html('');
			//msg2.dialog('close');
			$('.message2').css({'display':'none'});
			$('.modal').css({'display':'none'});
			$.ajax({
				url:'./lib/js/gettitlename.ajax.php',
				method:'post',
				async:false,
				data:{'story':$('.basic input[name="story"]').val()},
				dataType:'html',
				success:function(d){
					$('#title #titlename').html(d);
				},
				error:function(e){
					console.log(e);
				}
			});
			$.ajax({
				url:'./lib/js/getdeplist.ajax.php',
				method:'post',
				async:false,
				data:{'story':$('.basic input[name="story"]').val()},
				dataType:'html',
				success:function(d){
					$('.items').html('');
					//$('#title #setbutton').prop('class','');
					//$('#title #setbutton').html('');
					$('#keybox .funkey2 div:eq(0)').prop('id','');
					$('#keybox .funkey2 div:eq(0)').html('');
					$('.basic input[name="dep"]').val('');
					$('input[class="type"]').val('deps');
					$('input[class="itemtype"]').val('');
					$('#content').css({'padding':'0'});
					$('#content').html(d);
					/*if($('.setwin input[name="logout"]').length>0){
						$('.setwin input[name="logout"]').trigger('click');
					}
					else{
					}*/
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else if($('.message2 input[name="msgtype"]').val()=='outorder'){//登出(切換人員)
			$('#setup').submit();
		}
		else{
		}
		$('.message2 #check').prop('disabled',false);
	});
	$('.message3 input[name="phone"]').keyup(function(){
		$('.message3 input[name="phone"]').val($('.message3 input[name="phone"]').val().match(/\d*/));
	});
	$('.message3 #check').click(function(){
		if($('.message3 input[name="name"]').val()==''||$('.message3 input[name="phone"]').val()==''||$('.message3 input[name="address"]').val()==''){
			$('.message1 #text').html('請將表格填寫完畢。');
			//msg1.dialog('open');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else{
			if($('.basic input[name="onlinemember"]').val()=='1'){//網路會員
				$.ajax({
					url:'http://api.tableplus.com.tw/outposandorder/memberapi/create.member.php',
					method:'post',
					async:false,
					data:{'membertype':$('.basic input[name="membertype"]').val(),'type':'online','company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('.message3 input[name="phone"]').val(),'name':$('.message3 input[name="name"]').val(),'address':$('.message3 input[name="address"]').val(),'remark':''},
					dataType:'html',
					timeout:5000,
					success:function(d){
						console.log(d);
						if(d.substr(0,7)=='success'||d.substr(0,2)=='OK'||d.substr(0,9)=='OKsuccess'){
							//console.log(d);
							if(d.substr(0,9)=='OKsuccess'){
								$('.basic input[name="memno"]').val(d.substr(10));
							}
							else if(d.substr(0,7)=='success'){
								$('.basic input[name="memno"]').val(d.substr(8));
							}
							else{
								$('.basic input[name="memno"]').val(d.substr(3));
							}
							$.ajax({
								url:'./lib/js/getmemdata.session.php',
								method:'post',
								async:false,
								data:{'company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'memno':$('.basic input[name="memno"]').val()},
								dataType:'html',
								success:function(d){
									if(d.substr(0,7)=='success'){
										var tempd=d.split(';-;');
										$('.message3 input[name="phone"]').val('');
										$('.message3 input[name="name"]').val('');
										$('.message3 input[name="address"]').val('');
										$('.basic input[name="phone"]').val(tempd[1]);
										$('.basic input[name="memno"]').val(tempd[2]);
										$('.basic input[name="name"]').val(tempd[3]);
										$('.setwin').html('');
										$('.setwin').append("<div style='width:80%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('#title').css('background-color')+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='mylist'>我的訂單</div></div><div style='width:80%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('#keybox').css('background-color')+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='changepw'>變更密碼</div></div><div style='width:50%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('.basic input[name="color"]').val()+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='logout'>登出</div></div>");
										//msg3.dialog('close');
										$('.message3').css({'display':'none'});
										//setwin.dialog('open');
										$('.setwin').css({'display':'block'});
										//if(msg2.dialog('isOpen')){
										if($('.message2').css('display')=='block'){
											$('.basic input[name="memno"]').val(d.substr(8));
											var array=$('.items').serialize();
											//console.log(array);
											array=array+'&story='+$('.basic input[name="story"]').val()+'&dep='+$('.basic input[name="dep"]').val()+'&listtype='+$('.basic input[name="listtype"]').val()+'&memno='+$('.basic input[name="memno"]').val()+'&phone='+$('.basic input[name="phone"]').val()+'&name='+$('.basic input[name="name"]').val();
											//console.log(array);
											$.ajax({
												url:'./lib/js/create.tempdb.php',
												method:'post',
												async:false,
												data:array,
												timeout:5000,
												dataType:'html',
												success:function(d){
													$('.message4 #text').html('成功送出訂單，門市人員將會盡快與您連絡。');
													//msg4.dialog('open');
													$('.message4').css({'display':'block'});
													//console.log(d);
												},
												error:function(e,status){
													console.log(e+status);
													if(status==='timeout'){
														$('.message1 #text').html('連線逾時，請稍後再試。');
														//msg1.dialog('open');
														$('.message1').css({'display':'block'});
														$('.modal').css({'display':'block'});
													}
													else{
														$('.message1 #text').html(e+status);
														//msg1.dialog('open');
														$('.message1').css({'display':'block'});
														$('.modal').css({'display':'block'});
													}
												}
											});
										}
										else{
										}
									}
									else{
										$('.message3 input[name="phone"]').val('');
										$('.message3 input[name="name"]').val('');
										$('.message3 input[name="address"]').val('');
										$('.basic input[name="phone"]').val('');
										$('.basic input[name="memno"]').val('');
										$('.basic input[name="name"]').val('');
										$('.message1 #text').html('查無資料。');
										//msg1.dialog('open');
										$('.message1').css({'display':'block'});
									}
									//console.log(d);
								},
								error:function(e){
									console.log(e);
								}
							});
						}
						else if(d.substr(0,6)=='exists'){
							$('.message1 #text').html('該電話已註冊過。');
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
							//console.log(d);
						}
						/*else if(d=='OK'){
							$('.message1 #text').html('註冊成功，請重新登入。');
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
							console.log(d);
						}*/
						else{
							$('.message1 #text').html(d);
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
							//console.log(d);
						}
					},
					error:function(e,status){
						if(status==='timeout'){
							$('.message1 #text').html('連線逾時，請稍後再試。');
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
						}
						else{
							$('.message1 #text').html(e);
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
						}
						console.log(e);
					}
				});
			}
			else{//本地會員
				$.ajax({
					url:'../../../memberapi/create.member.php',
					method:'post',
					async:false,
					data:{'membertype':$('.basic input[name="membertype"]').val(),'type':'offline','company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('.message3 input[name="phone"]').val(),'name':$('.message3 input[name="name"]').val(),'address':$('.message3 input[name="address"]').val(),'remark':''},
					dataType:'html',
					timeout:5000,
					success:function(d){
						console.log(d);
						if(d.substr(0,7)=='success'||d.substr(0,2)=='OK'||d.substr(0,9)=='OKsuccess'){
							//console.log(d);
							if(d.substr(0,9)=='OKsuccess'){
								$('.basic input[name="memno"]').val(d.substr(10));
							}
							else if(d.substr(0,7)=='success'){
								$('.basic input[name="memno"]').val(d.substr(8));
							}
							else{
								$('.basic input[name="memno"]').val(d.substr(3));
							}
							$.ajax({
								url:'./lib/js/getmemdata.session.php',
								method:'post',
								async:false,
								data:{'company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'memno':$('.basic input[name="memno"]').val()},
								dataType:'html',
								success:function(d){
									if(d.substr(0,7)=='success'){
										var tempd=d.split(';-;');
										$('.message3 input[name="phone"]').val('');
										$('.message3 input[name="name"]').val('');
										$('.message3 input[name="address"]').val('');
										$('.basic input[name="phone"]').val(tempd[1]);
										$('.basic input[name="memno"]').val(tempd[2]);
										$('.basic input[name="name"]').val(tempd[3]);
										$('.setwin').html('');
										$('.setwin').append("<div style='width:80%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('#title').css('background-color')+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='mylist'>我的訂單</div></div><div style='width:80%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('#keybox').css('background-color')+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='changepw'>變更密碼</div></div><div style='width:50%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('.basic input[name="color"]').val()+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='logout'>登出</div></div>");
										//msg3.dialog('close');
										$('.message3').css({'display':'none'});
										//setwin.dialog('open');
										$('.setwin').css({'display':'block'});
										//if(msg2.dialog('isOpen')){
										if($('.message2').css('display')=='block'){
											$('.basic input[name="memno"]').val(d.substr(8));
											var array=$('.items').serialize();
											//console.log(array);
											array=array+'&story='+$('.basic input[name="story"]').val()+'&dep='+$('.basic input[name="dep"]').val()+'&listtype='+$('.basic input[name="listtype"]').val()+'&memno='+$('.basic input[name="memno"]').val()+'&phone='+$('.basic input[name="phone"]').val()+'&name='+$('.basic input[name="name"]').val();
											//console.log(array);
											$.ajax({
												url:'./lib/js/create.tempdb.php',
												method:'post',
												async:false,
												data:array,
												timeout:5000,
												dataType:'html',
												success:function(d){
													$('.message4 #text').html('成功送出訂單，門市人員將會盡快與您連絡。');
													//msg4.dialog('open');
													$('.message4').css({'display':'block'});
													//console.log(d);
												},
												error:function(e,status){
													console.log(e+status);
													if(status==='timeout'){
														$('.message1 #text').html('連線逾時，請稍後再試。');
														//msg1.dialog('open');
														$('.message1').css({'display':'block'});
														$('.modal').css({'display':'block'});
													}
													else{
														$('.message1 #text').html(e+status);
														//msg1.dialog('open');
														$('.message1').css({'display':'block'});
														$('.modal').css({'display':'block'});
													}
												}
											});
										}
										else{
										}
									}
									else{
										$('.message3 input[name="phone"]').val('');
										$('.message3 input[name="name"]').val('');
										$('.message3 input[name="address"]').val('');
										$('.basic input[name="phone"]').val('');
										$('.basic input[name="memno"]').val('');
										$('.basic input[name="name"]').val('');
										$('.message1 #text').html('查無資料。');
										//msg1.dialog('open');
										$('.message1').css({'display':'block'});
									}
									//console.log(d);
								},
								error:function(e){
									console.log(e);
								}
							});
						}
						else if(d.substr(0,6)=='exists'){
							$('.message1 #text').html('該電話已註冊過。');
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
							//console.log(d);
						}
						/*else if(d=='OK'){
							$('.message1 #text').html('註冊成功，請重新登入。');
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
							console.log(d);
						}*/
						else{
							$('.message1 #text').html(d);
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
							//console.log(d);
						}
					},
					error:function(e,status){
						if(status==='timeout'){
							$('.message1 #text').html('連線逾時，請稍後再試。');
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
						}
						else{
							$('.message1 #text').html(e);
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
						}
						console.log(e);
					}
				});
			}
		}
	});
	$('.message3 #cancel').click(function(){
		$('.message3 input[name="name"]').val('');
		$('.message3 input[name="phone"]').val('');
		$('.message3 input[name="address"]').val('');
		//msg3.dialog('close');
		$('.message3').css({'display':'none'});
		//setwin.dialog('open');
		$('.setwin').css({'display':'block'});
	});
	$('.message4 #check').click(function(){
		location.href='./?story='+$('.basic input[name="story"]').val()+'&dep='+$('.basic input[name="dep"]').val();
	});
	$('#title').on('click','.setbutton',function(){
		//setwin.dialog('open');
		$('.setwin').css({'display':'block'});
		$('.modal').css({'display':'block'});
	});
	$('.setwin').on('click','#registered',function(){
		//msg3.dialog('open');
		//setwin.dialog('close');
		$('.setwin').css({'display':'none'});
		$('.message3').css({'display':'block'});
		//console.log('會員註冊');
	});
	$('.setwin').on('keypress','input[name="id"]',function(event){
		if(event.which==13){
			$('.setwin input[name="pw"]').focus();
		}
		else{
		}
	});
	/*$('.setwin').on('click','#login',function(){
		$.ajax({
			url:'./lib/js/checkid.pw.ajax.php',
			method:'post',
			async:false,
			data:{'company':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val(),'id':$('.setwin input[name="id"]').val(),'pw':$('.setwin input[name="pw"]').val()},
			dataType:'html',
			success:function(d){
				if(d.substr(0,7)=='success'){
					$('.basic input[name="memno"]').val(d.substr(8));
					$.ajax({
						url:'./lib/js/getmemdata.session.php',
						method:'post',
						async:false,
						data:{'company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'memno':$('.basic input[name="memno"]').val()},
						dataType:'html',
						success:function(d){
							if(d.substr(0,7)=='success'){
								var tempd=d.split(';-;');
								$('.basic input[name="phone"]').val(tempd[1]);
								$('.basic input[name="memno"]').val(tempd[2]);
								$('.basic input[name="name"]').val(tempd[3]);
								$('.basic input[name="address"]').val(tempd[4]);
								$('.setwin').html('');
								$('.setwin').append("<div style='margin:10px 0;text-align:center;'>設定</div><div style='width:80%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('#title').css('background-color')+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='mylist'>我的訂單</div></div><div style='width:80%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('#keybox').css('background-color')+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='changepw'>變更密碼</div></div><div style='width:50%;margin:5px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 5px;text-align:center;background-color:"+$('.basic input[name="color"]').val()+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='logout'>登出</div></div>");
								//msg3.dialog('close');
								$('.message3').css({'display':'none'});
								if($('#content #itemlist .memname').length>0&&$('#content #itemlist .memname').css('display')=='none'){
									$('#keybox .funkey2 div:eq(0)').prop('id','list');
									$('#keybox .funkey2 #list').trigger('click');
								}
								else{
								}
								//if(msg2.dialog('isOpen')){
								if($('.message2').css('display')=='block'){
									$.ajax({
										url:'./lib/js/create.member.php',
										method:'post',
										async:false,
										data:{'company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('.message3 input[name="phone"]').val(),'name':$('.message3 input[name="name"]').val(),'address':$('.message3 input[name="address"]').val(),'remark':''},
										dataType:'html',
										timeout:5000,
										success:function(d){
											//console.log(d);
											if(d.substr(0,7)=='success'||$('.message2').css('display')=='block'){//msg2.dialog('isOpen')
												$('.basic input[name="memno"]').val(d.substr(8));
												var array=$('.items').serialize();
												//console.log(array);
												array=array+'&story='+$('.basic input[name="story"]').val()+'&dep='+$('.basic input[name="dep"]').val()+'&listtype='+$('.basic input[name="listtype"]').val()+'&memno='+$('.basic input[name="memno"]').val()+'&phone='+$('.basic input[name="phone"]').val()+'&name='+$('.basic input[name="name"]').val();
												//console.log(array);
												$.ajax({
													url:'./lib/js/create.tempdb.php',
													method:'post',
													async:false,
													data:array,
													timeout:5000,
													dataType:'html',
													success:function(d){
														$('.message4 #text').html('成功送出訂單，門市人員將會盡快與您連絡。');
														//msg4.dialog('open');
														$('.message4').css({'display':'block'});
													},
													error:function(e,status){
														if(status==='timeout'){
															$('.message1 #text').html('連線逾時，請稍後再試。');
															//msg1.dialog('open');
															$('.message1').css({'display':'block'});
															$('.modal').css({'display':'block'});
														}
														else{
															$('.message1 #text').html(e);
															//msg1.dialog('open');
															$('.message1').css({'display':'block'});
															$('.modal').css({'display':'block'});
														}
													}
												});
											}
											else{
												$('.message1 #text').html('資料格式有誤。');
												//msg1.dialog('open');
												$('.message1').css({'display':'block'});
												$('.modal').css({'display':'block'});
											}
										},
										error:function(e,status){
											if(status==='timeout'){
												$('.message1 #text').html('連線逾時，請稍後再試。');
												//msg1.dialog('open');
												$('.message1').css({'display':'block'});
												$('.modal').css({'display':'block'});
											}
											else{
												$('.message1 #text').html(e);
												//msg1.dialog('open');
												$('.message1').css({'display':'block'});
												$('.modal').css({'display':'block'});
											}
										}
									});
								}
								else{
								}
							}
							else{
								$('.basic input[name="phone"]').val('');
								$('.basic input[name="memno"]').val('');
								$('.basic input[name="name"]').val('');
								$('.basic input[name="address"]').val('');
								$('.message1 #text').html('查無資料。');
								//msg1.dialog('open');
								$('.message1').css({'display':'block'});
								$('.modal').css({'display':'block'});
							}
							//console.log(d);
						},
						error:function(e){
							console.log(e);
						}
					});
					//$('.setwin').html('');
					//$('.setwin').append("<div style='width:80%;margin:5px auto 15px auto;'><input type='button' style='width:100%;' name='mylist' value='我的訂單'></div><div style='width:80%;margin:5px auto 15px auto;'><input type='button' style='width:100%;' name='changepw' value='變更密碼'></div><div style='width:50%;margin:0 auto;'><input type='button' style='width:100%;' name='logout' value='登出'></div>");
				}
				else{
					$('.message1 #text').html('帳號、密碼輸入錯誤。');
					//msg1.dialog('open');
					$('.message1').css({'display':'block'});
					$('.modal').css({'display':'block'});
				}
				//console.log(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});*/
	$('.setwin').on('click','#changepw',function(){
		//pw.dialog('open');
		//setwin.dialog('close');
		$('.setwin').css({'display':'none'});
		$('.pw').css({'display':'block'});
	});
	$('.pw input[name="pw"]').keypress(function(event){
		if(event.which==13){
			$('.pw input[name="newpw1"]').focus();
		}
		else{
		}
	});
	$('.pw input[name="newpw1"]').keypress(function(event){
		if(event.which==13){
			$('.pw input[name="newpw2"]').focus();
		}
		else{
		}
	});
	$('.pw input[name="newpw2"]').keypress(function(event){
		if(event.which==13){
			$('.pw #change').focus();
		}
		else{
		}
	});
	$('.pw #change').click(function(){
		if($('.pw input[name="pw"]').val()==''||$('.pw input[name="newpw1"]').val()==''||$('.pw input[name="newpw2"]').val()==''){
			$('.message1 #text').html('請填寫完整表格。');
			//msg1.dialog('open');
			$('.message1').css({'display':'block'});
			$('.modal').css({'display':'block'});
		}
		else{
			if($('.pw input[name="newpw1"]').val()==$('.pw input[name="newpw2"]').val()){
				$('.pw #pwhint').html('');
				$.ajax({
					url:'./lib/js/checkid.pw.ajax.php',
					method:'post',
					async:false,
					data:{'company':$('.basic input[name="story"]').val(),'dep':$('.basic input[name="dep"]').val(),'id':$('.basic input[name="phone"]').val(),'pw':$('.pw input[name="pw"]').val()},
					dataType:'html',
					success:function(d){
						var tempd=d.split('-');
						if(tempd[0]=='success'){
							//console.log('success');
						}
						else{
							$('.message1 #text').html('原始密碼輸入錯誤，請確認密碼。');
							//msg1.dialog('open');
							$('.message1').css({'display':'block'});
							$('.modal').css({'display':'block'});
						}
					},
					error:function(e){
						if(e['status']=='timeout'){
							console.log('timeout');
						}
						else{
							console.log('error');
						}
					}
				});
			}
			else{
				$('.pw #pwhint').html('密碼不符。');
			}
		}
	});
	$('.pw #cancel').click(function(){
		$('.pw input[name="pw"]').val('');
		$('.pw input[name="newpw1"]').val('');
		$('.pw input[name="newpw2"]').val('');
		$('.pw #pwhint').html('');
		//pw.dialog('close');
		$('.pw').css({'display':'none'});
		//setwin.dialog('open');
		$('.setwin').css({'display':'block'});
	});
	$('.setwin').on('click','#logout',function(){
		if($('#setup input[name="submachine"]').val().length>0){
			location.href='./index.php?submachine='+$('#setup input[name="submachine"]').val();
		}
		else{
			location.href='./index.php';
		}
		/*$.ajax({
			url:'./lib/js/logout.ajax.php',
			async:false,
			dataType:'html',
			success:function(d){
				//location.href='./?story='+$('.basic input[name="story"]').val();
				$('.basic input[name="memno"]').val('');
				$('.basic input[name="phone"]').val('');
				$('.basic input[name="name"]').val('');
				$('.basic input[name="address"]').val('');
				$('.setwin').html("<table style='width:100%;font-size:18px;'><caption>登入</caption><tr><td>會員帳號：</td></tr><tr><td><input type='text' style='width:100%;border:1px #4a4a4a solid;border-radius:5px;font-size:18px;' name='id' value=''></td></tr><tr><td>會員密碼：</td></tr><tr><td><input type='password' style='width:100%;border:1px #4a4a4a solid;border-radius:5px;font-size:18px;' name='pw' value=''></td></tr></table><div style='width:80%;margin:15px auto 20px auto;'><div style='width:100%;height:35px;line-height:35px;margin:0 auto;text-align:center;background-color:"+$('#title').css('background-color')+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='login'>會員登入</div></div><div style='width:50%;margin:0 auto;'><div style='width:100%;height:35px;line-height:35px;margin:5px auto 20px auto;;text-align:center;background-color:"+$('#keybox').css('background-color')+";color:#ffffff;font-size:16px;border-radius:50px;cursor: pointer;' id='registered'>馬上註冊</div></div>");
				//console.log(d);
				if($('#content #itemlist .memname').length>0&&$('#content #itemlist .memnamemodal').css('display')=='none'){
					$('#keybox .funkey2').prop('id','list');
					$('#keybox #list').trigger('click');
				}
				else{
				}
				if($('.type').val().substr(0,6)=='mylist'){
					$('#title .return').trigger('click');
				}
				else{
				}
			},
			error:function(e){
				console.log(e);
			}
		});*/
	});
	$('#content').on('click','#itemlist #receverdata #createmember',function(){
		if($('.basic input[name="onlinemember"]').val()=='1'){//網路會員
			$.ajax({
				url:'http://api.tableplus.com.tw/outposandorder/memberapi/getmemdata.ajax.php',
				method:'post',
				async:false,
				data:{'membertype':$('.basic input[name="membertype"]').val(),'type':'online','company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('#content #receverdata input[name="memtel"]').val(),'search':''},
				dataType:'json',
				success:function(d){
					console.log(d);
					if(d=='empty'||d.length==0){
						$('.cremem #memtel').val($('#content #itemlist #receverdata input[name="memtel"]').val());
						$('.cremem').css({'display':'block'});
						$('.modal').css({'display':'block'});
					}
					else{
						for(var i=0;i<d.length;i++){
							$('#content #itemlist #receverdata select[name="memname"]').append('<option value="'+d[i]['memno']+'">'+d[i]['name']+'</option>');
						}
						$('#content #itemlist #receverdata select[name="memname"] option:eq(0)').prop('select',true);
						//$('#content #itemlist #receverdata input[name="memname"]').val(d[0]['name']);
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{//本地會員
			$.ajax({
				url:'http://'+$('.basic input[name="serverip"]').val()+'/memberapi/getmemdata.ajax.php',
				method:'post',
				async:false,
				data:{'membertype':$('.basic input[name="membertype"]').val(),'type':'online','company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('#content #receverdata input[name="memtel"]').val(),'search':''},
				dataType:'json',
				success:function(d){
					console.log(d);
					if(d=='empty'||d.length==0){
						$('.cremem #memtel').val($('#content #itemlist #receverdata input[name="memtel"]').val());
						$('.cremem').css({'display':'block'});
						$('.modal').css({'display':'block'});
					}
					else{
						for(var i=0;i<d.length;i++){
							$('#content #itemlist #receverdata select[name="memname"]').append('<option value="'+d[i]['memno']+'">'+d[i]['name']+'</option>');
						}
						$('#content #itemlist #receverdata select[name="memname"] option:eq(0)').prop('select',true);
						//$('#content #itemlist #receverdata input[name="memname"]').val(d[0]['name']);
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$('.cremem #check').click(function(){
		if($('.cremem #memname').val()!=''){
			if($('.basic input[name="onlinemember"]').val()=='1'){//網路會員
				$.ajax({
					url:'http://api.tableplus.com.tw/outposandorder/memberapi/create.member.php',
					method:'post',
					async:false,
					data:{'membertype':$('.basic input[name="membertype"]').val(),'type':'online','company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('.cremem #memtel').val(),'name':$('.cremem #memname').val(),'address':$('.cremem #memaddress').val()},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('.modal').trigger('click');
						$('#content #itemlist #receverdata input[name="memtel"]').trigger('change');
					},
					error:function(e){
						//console.log(e);
					}
				});
			}
			else{//本地會員
				$.ajax({
					url:'http://'+$('.basic input[name="serverip"]').val()+'/memberapi/create.member.php',
					method:'post',
					async:false,
					data:{'membertype':$('.basic input[name="membertype"]').val(),'type':'offline','company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'tel':$('.cremem #memtel').val(),'name':$('.cremem #memname').val(),'address':$('.cremem #memaddress').val()},
					dataType:'html',
					success:function(d){
						//console.log(d);
						$('.modal').trigger('click');
						$('#content #itemlist #receverdata input[name="memtel"]').trigger('change');
					},
					error:function(e){
						//console.log(e);
					}
				});
			}
		}
		else{
		}
	});
	$('.salepay select[name="paytype"]').change(function(){
		$('#setup input[name="cashmoney"]').val('0');
		$('#setup input[name="cash"]').val('0');
		$('#setup input[name="other"]').val('0');
		$('#setup input[name="otherstring"]').val('');
		$('#setup input[name="otherfix"]').val('0');
		$('#setup input[name="already"]').val('0');
		$('#setup input[name="notyet"]').val(comptotal());
		$('#setup input[name="change"]').val('0');
		$('.salepay input[name="money"]').val('');
		$('.salepay input[name="change"]').val('0');
	});
	$('.salepay input[name="money"]').change(function(){
		var paytype='';
		var paymoney=0;
		var otherstring='';

		if($('.salepay select[name="paytype"] option:selected').val()!='cashmoney'){//2020/4/13 如果使用現金以外的付款方式，計算付款方式所代表的金額
			$.ajax({
				url:'./lib/js/compute.paymoney.ajax.php',
				method:'post',
				async:false,
				data:{'paysession':$('.salepay select[name="paytype"] option:selected').val(),'notyet':$('.salepay input[name="notyet"]').val(),'money':$('.salepay input[name="money"]').val()},
				dataType:'json',
				success:function(d){
					//console.log(d);
					
					paytype=d['paytype'];
					paymoney=d['paymoney'];
					otherstring=d['otherstring'];

					$('.salepay input[name="money"]').val(paymoney);
				},
				error:function(e){
					//console.log(e);
				}
			});
		}
		else{
			paytype='cashmoney';
			if(parseFloat($('.salepay input[name="money"]').val())<=parseFloat($('.salepay input[name="notyet"]').val())){//2020/4/9 填入支付金額小於應付金額，則將支付金額修改為應付金額(此情況視為操作人員誤操作)
				paymoney=$('.salepay input[name="money"]').val();
			}
			else{
				paymoney=$('.salepay input[name="notyet"]').val();
			}
			otherstring='';
		}

		$('#setup input[name="otherstring"]').val(otherstring);

		$('#setup input[name="'+paytype+'"]').val(paymoney);
		
		if($.isNumeric($('.salepay input[name="money"]').val())){
			$('#setup input[name="already"]').val($('.salepay input[name="money"]').val());//2020/4/9 判斷輸入金額為數字
		}
		else{
			$('#setup input[name="already"]').val('0');//2020/4/9 判斷輸入金額為非數字，將內容清空
		}
		if(parseFloat($('.salepay input[name="notyet"]').val())<=parseFloat($('#setup input[name="already"]').val())){
			$('#setup input[name="notyet"]').val('0');
			$('#setup input[name="change"]').val(parseFloat($('#setup input[name="already"]').val())-parseFloat($('.salepay input[name="notyet"]').val()));
			$('.salepay input[name="change"]').val(parseFloat($('#setup input[name="already"]').val())-parseFloat($('.salepay input[name="notyet"]').val()));
		}
		else{
			$('#setup input[name="notyet"]').val(parseFloat($('.salepay input[name="notyet"]').val())-parseFloat($('#setup input[name="already"]').val()));
			$('#setup input[name="change"]').val('0');
			$('.salepay input[name="change"]').val('0');
		}
	});
	$('.salepay input[name="money"]').keyup(function(){
		//if($('.basic input[name="accuracy"]').val()=='0'){
		//	$('.salepay input[name="money"]').val($('.salepay input[name="money"]').val().match(/[\d]*/));
		//}
		//else if($('.basic input[name="accuracy"]').val()=='1'){
		//	$('.salepay input[name="money"]').val($('.salepay input[name="money"]').val().match(/[\d]*.{0,1}[\d]{0,1}/));
		//}
		//else{
		//	$('.salepay input[name="money"]').val($('.salepay input[name="money"]').val().match(/[\d]*.[\d]*/));
		//}
	});
	$('.salepay #check').click(function(){//結帳
		if(($('.salepay input[name="money"]').val()==''||Number($('.salepay input[name="money"]').val())==0||!$.isNumeric($('.salepay input[name="money"]').val()))&&parseFloat($('#setup input[name="notyet"]').val())>0){
			$('.salepay input[name="money"]').val($('.salepay input[name="notyet"]').val());
			$('.salepay input[name="money"]').trigger('change');
			$('.salepay #check').trigger('click');
		}
		else if(parseFloat($('#setup input[name="notyet"]').val())>0){
		}
		else{
			$('#setup input[name="tempbuytype"]').val('1');
			$('.salepay #check').prop('disabled',true);
			$('#setup input[name="listtotal"]').prop('disabled',false);
			var gotin=1;
			/*if($('.order#order .initsetting #pointtree').val()=='1'&&$('.result .sendviewwindow input[name="otherstring"]').val().match("pointtree")){//手機POS不使用集點樹
				var tempors=$('.result .sendviewwindow input[name="otherstring"]').val().split("pointtree-value:");
				var tempors=tempors[1].split("=");
				if(Number(tempors[0])>0&&(!$.isNumeric($('.result #funbox .pointtree #name2').html())||$('.result #funbox .pointtree #name3').html()=="")){
					gotin=0;
				}
				else{
				}
			}
			else{
			}*/
			/*if($('.order#order #tabs4 form[data-id="listform"] input[name="listtype"]').val()=='1'&&(Number($('.setperson #view input[name="personfloor1"]').val())>0||Number($('.setperson #view input[name="personfloor2"]').val())>0||Number($('.setperson #view input[name="personfloor3"]').val())>0)&&parseInt($('.order#order #MemberBill #tabs1 #persons').val())==0){
				//personhint.dialog('open');
				alert('請填入用餐人數');
			}
			else if($('.order#order .initsetting #pointtree').val()=='1'&&gotin==0){
				alert('請輸入集點樹會員');
			}
			else{*/
				/*if($('.basic input[name="onlinemember"]').length>0&&$('.basic input[name="onlinemember"]').val()=='1'){//網路會員
					//檢查連線狀態
					if($('.initsetting #ourmempointmoney').val()=='1'&&$('.order#order #tabs4 .listcontentbox input[name="memno"]').val()!=''&&typeof api_point_money_checkserver!=="undefined"&&typeof api_point_money_checkserver==="function"){
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
				}
				else{
				}*/
				/*發票金額，改成帳單折扣與其他付款即時變動*/
				//手機POS發票模組尚未結合
				/*$('.order#order #tabs4 form[data-id="listform"] input[name="invsalemoney"]').val($('.result #viewwindow #should').html());
				if($('.result #paywindow #paycontent .paywaybox .otherpay').length==0){//無其他付款則無需重新計算發票金額
				}
				else{
					for(var i=0;i<$('.result #paywindow #paycontent .paywaybox .otherpay').length;i++){
						if($('.result #paywindow #paycontent .paywaybox .otherpay input[name="inv"]').val()=='0'){
							$('.order#order #tabs4 form[data-id="listform"] input[name="invsalemoney"]').val(Number($('.order#order #tabs4 form[data-id="listform"] input[name="invsalemoney"]').val())-Number($('.result #paywindow #paycontent .paywaybox .otherpay input[name="viewmoney"]').val()));
						}
						else{
						}
					}
				}*/

				/*$('.result #viewwindow input[name="listtotal"]').val($('.result #viewwindow #total').html());
				$('.result #viewwindow input[name="itemdis"]').val($('.result #viewwindow #itemdis').html());
				$('.result #viewwindow input[name="memberdis"]').val($('.result #viewwindow #memberdis').html());
				$('.result #viewwindow input[name="listdis1"]').val($('.result #viewwindow #listdis1').html());
				$('.result #viewwindow input[name="listdis2"]').val($('.result #viewwindow #listdis2').html());
				$('.order#order #tabs4 form[data-id="listform"] input[name="charge"]').val($('.result #viewwindow #charge').html());
				$('.result #viewwindow input[name="coupon1"]').val($('.result #viewwindow #coupon1').html());
				$('.result #viewwindow input[name="coupon2"]').val($('.result #viewwindow #coupon2').html());
				$('.result #viewwindow input[name="should"]').val($('.result #viewwindow #should').html());
				$('.result #viewwindow input[name="cashcomm"]').val($('.result #viewwindow #cashcomm').html());
				$('.result #viewwindow input[name="already"]').val($('.result #viewwindow #already').html());
				$('.result #viewwindow input[name="cashmoney"]').val($('.result #viewwindow #cashmoney').html());
				$('.result #viewwindow input[name="cash"]').val($('.result #viewwindow #cash').html());
				$('.result #viewwindow input[name="other"]').val($('.result #viewwindow #other').html());
				$('.result #viewwindow input[name="otherfix"]').val($('.result #viewwindow #otherfix').html());
				$('.result #viewwindow input[name="change"]').val($('.result #viewwindow #change').html());
				$('.result #viewwindow input[name="autodis"]').val($('.result #viewwindow #autodis').html());
				$('.order#order #tabs4 form[data-id="listform"] input[name="tempbuytype"]').val('1');*/
				
				var istrue=1;
				//手機POS統編檢查與發票模組同進退
				/*if($('.result #viewwindow input[name="tempban"]').val().length>0){
					if(parseInt($('.result #viewwindow input[name="tempban"]').val())==0){
					}
					else if($('.result #viewwindow input[name="tempban"]').val().length!=8){
						if($('.sysmeg #name1.syshint4').length>0){
							$('.sysmeg #name1.syshint4').css({'display':''});
						}
						else{
						}
						if($('.sysmeg #name2.syshint4').length>0){
							$('.sysmeg #name2.syshint4').css({'display':''});
						}
						else{
						}
						sysmeg.dialog('open');
						istrue=0;
					}
					else{
						var ban=$('.result #viewwindow input[name="tempban"]').val();
						var value=0;
						var t=[1,2,1,2,1,2,4,1];
						var temp=0;
						for(var i=0;i<8;i++){
							temp=parseInt(ban.substr(i,1))*t[i];
							//console.log('temp='+ban.substr(i,1)+'*'+t[i]+'='+temp);
							if(parseInt(temp)>=10){
								//console.log('value='+value+'+'+temp.toString().substr(0,1)+'+'+temp.toString().substr(1,1)+'='+(parseInt(value)+parseInt(temp.toString().substr(0,1))+parseInt(temp.toString().substr(1,1))));
								value=parseInt(value)+parseInt(temp.toString().substr(0,1))+parseInt(temp.toString().substr(1,1));
							}
							else{
								//console.log('value='+value+'+'+temp+'='+(parseInt(value)+parseInt(temp)));
								value=parseInt(value)+parseInt(temp);
							}
						}
						if(value%10==0){
						}
						else if(parseInt(ban.substr(6,1))==7&&(value+1)%10==0){
						}
						else{
							if($('.sysmeg #name1.syshint4').length>0){
								$('.sysmeg #name1.syshint4').css({'display':''});
							}
							else{
							}
							if($('.sysmeg #name2.syshint4').length>0){
								$('.sysmeg #name2.syshint4').css({'display':''});
							}
							else{
							}
							sysmeg.dialog('open');
							istrue=0;
						}
					}
				}
				else{
				}*/
				if(istrue==0){//由於統編格示錯誤，不做處理
				}
				else{
					
					/*if($('.result #paywindow .paywaybox .paycash').length>0&&$('.order#order .initsetting #creditcode').val()=='1'){//使用信用卡付款
						$('.keybord input[name="type"]').val('creditcard');
						//console.log($('.keybord input[name="type"]').val());
						keybord.dialog('option','title','輸入卡號');
						keybord.dialog('open');
					}
					else{*///無使用信用卡付款
						var nowbizdate=$('#setup input[name="bizdate"]').val();
						var array1=$('#setup , .items').serialize();
						/*if(typeof $('.result #viewwindow input[name="sendtype"]')=="undefuned"||$('.result #viewwindow input[name="sendtype"]').val()=='result'){
							var array1=$('#tabs4 form[data-id="listform"] , .result #viewwindow .sendviewwindow').serialize();
							//console.log(array1);
						}
						else{
							var array1=$(".result #viewwindow .sendviewwindow").serialize();
							array1=array1+'&charge='+$('.result #viewwindow #charge').html()+'&machinetype='+$('.order#order #tabs4 form[data-id="listform"] input[name="machinetype"]').val()+'&bizdate='+$('.viewtemp #date').html()+'&consecnumber='+$('.viewtemp #listno input[name="consecnumber"]').val();
							//console.log(array1);
						}*/
						var invnumber='';
						var consecnumber='';
						//手機POS不使用錢都錄
						/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
							var posdvrfile='';
						}
						else{
						}*/
						//$('.result #checkfun #submit').prop('disabled',true);
						$.ajax({//寫入暫存資料表
							url:'../demopos/lib/js/create.tempdb.php',
							method:'post',
							async:false,
							data:array1,
							dataType:'html',
							success:function(d){
								console.log(d);
								if(d.length>20){
									$.ajax({
										url:'../demopos/lib/js/print.php',
										method:'post',
										data:{'html':'orderpos sale create.tempdb.php '+d},
										dataType:'html',
										success:function(d){/*console.log(d);*/},
										error:function(e){/*console.log(e);*/}
									});
								}
								else{
								}
								var tempd=d.split('-');
								consecnumber=tempd[1];
								if($('#setup input[name="consecnumber"]').val()==''){
									$('#setup input[name="saleno"]').val(tempd[0]);
									$('#setup input[name="consecnumber"]').val(tempd[1]);
									array1=$('#setup , .items').serialize();
								}
								else{
								}
								//手機POS不使用錢都錄
								/*if($('.order#order .initsetting #posdvr').length>0&&$('.order#order .initsetting #posdvr').val()=='1'){
									posdvrfile=tempd[2];
								}
								else{
								}*/
							},
							error:function(e){
								console.log(e);
							}
						});
						var memberapi='';
						//2021/11/30 社服要使用(志)；開啟網路會員部分；本地會員部分保持關閉
						$.ajax({
							url:'../demopos/lib/js/checkmember.pointmoney.ajax.php',
							method:'post',
							async:false,
							data:{'company':$('.basic input[name="story"]').val(),'machinetype':$('#setup input[name="machinetype"]').val(),'consecnumber':$('#setup input[name="consecnumber"]').val()},
							dataType:'json',
							success:function(d){
								//console.log(d);
								if(d[0].length==0){
								}
								else{
									if(typeof api_point_money_ourmember!=="undefined"&&typeof api_point_money_ourmember==="function"){//網路會員
										//console.log(d);
										memberapi=api_point_money_ourmember(d[0]['company'],d[0]['story'],d[0]['memno'],d[0]['paymoney'],d[0]['giftpoint'],d[0]['memberpoint'],d[0]['membermoney']);
										//console.log(memberapi);
										if(typeof memberapi[0]!=="undefined"&&typeof memberapi[0]['state']!=="undefined"&&memberapi[0]['state']=='success'){
											$.ajax({
												url:'http://api.tableplus.com.tw/outposandorder/memberapi/change.memberdata.php',
												method:'post',
												async:false,
												data:{'company':$('.basic input[name="story"]').val(),'data':memberapi},
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
											data:{'type':'online','company':$('.basic input[name="story"]').val(),'story':$('.basic input[name="dep"]').val(),'data':memberapi,'senddata':d},
											dataType:'html',
											success:function(d){
												console.log(d);
											},
											error:function(e){
												console.log(e);
											}
										});
									}
									else{//本地會員
										/*$.ajax({
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
										//console.log(memberapi);
										if(typeof memberapi[0]!=="undefined"&&typeof memberapi[0]['state']!=="undefined"&&memberapi[0]['state']=='success'){
											$.ajax({
												url:'../memberapi/change.memberdata.php',
												method:'post',
												async:false,
												data:{'company':$('.order#order .companydata #company').val(),'data':memberapi},
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
												console.log(d);
											},
											error:function(e){
												console.log(e);
											}
										});*/
									}
								}
							},
							error:function(e){
								//console.log(e);
							}
						});
						/*錢都錄借接*/
						//手機POS不使用錢都錄
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
						//手機POS不出兌換卷
						/*if($('.order#order .initsetting #ticketlisttype').val().match($('.order#order #tabs4 form[data-id="listform"] input[name="listtype"]').val())&&($('.order#order .initsetting #ticket').val()=='2'||$('.order#order .initsetting #ticket').val()=='3')){//出"兌換卷"
							$.ajax({
								url:'./lib/js/create.ticket.php',
								method:'post',
								async:false,
								data:$('#tabs4 form[data-id="listform"]').serialize(),
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'temp create.ticket.php '+d},
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
									consecnumber=d;
								},
								error:function(e){
									console.log(e);
								}
							});
						}
						else{
						}*/

						if($('#setup input[name="consecnumber"]').val()==''){
						}
						else{
							$.ajax({
								url:'../demopos/lib/js/create.voidlist.php',
								method:'post',
								async: false,
								data:{'bizdate':$('#setup input[name="bizdate"]').val(),'consecnumber':$('#setup input[name="consecnumber"]').val(),'tablenumber':$('#setup input[name="tablenumber"]').val(),'machine':$('#setup input[name="machinetype"]').val()},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos sale create.voidlist.php '+d},
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
									//console.log(e);
								}
							});
						}
						/*if($('.result input[name="sendtype"]').val()=='buytemp'||$('.result #viewwindow input[name="sendtype"]').val()=='tempsale'){
							consecnumber=$('.viewtemp #listno input[name="consecnumber"]').val();
						}
						else{*/
							/*if(typeof memberapi[0]!=="undefined"){
								array1=array1+'&'+decodeURIComponent($.param(memberapi[0]));
							}
							else{
							}*/
							$.ajax({
								url:'../demopos/lib/js/create.list.php',
								method:'post',
								async: false,
								data:array1,
								dataType:'html',
								success:function(d){
									if(d.length>10){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos sale create.list.php '+d},
											dataType:'html',
											success:function(d){/*console.log(d);*/},
											error:function(e){/*console.log(e);*/}
										});
									}
									else{
									}
									console.log('明細單'+d);
									consecnumber=d;
								},
								error:function(e){
									console.log(e);
								}
							});
						//}
						
						if($('#setup input[name="consecnumber"]').val()==''){
						}
						else{
							$.ajax({
								url:'../demopos/lib/js/create.voidkitchen.php',
								method:'post',
								async: false,
								data:{'bizdate':$('#setup input[name="bizdate"]').val(),'consecnumber':$('#setup input[name="consecnumber"]').val(),'tablenumber':$('#setup input[name="tablenumber"]').val(),'machine':$('#setup input[name="machinetype"]').val()},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos sale create.voidkitchen.php '+d},
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
									//console.log(e);
								}
							});
						}
						//手機POS發票模組尚未結合
						/*if(($('.result input[name="sendtype"]').val()=='result'&&!$('.result #funbox .invbut').prop('disabled'))&&(typeof $('.result #funbox .invno').val()=="undefined"||$('.result #funbox .invno').val()=='1')&&$('.order#order .initsetting #useinv').val()=='1'&&Number($('.order#order #tabs4 form[data-id="listform"] input[name="invsalemoney"]').val())>0){//啟用發票&&開立發票
							$.ajax({
								url:'./lib/js/open.inv.php',
								method:'post',
								async:false,
								data:{'machinename':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val(),'consecnumber':$('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val(),'tempban':$('.result #viewwindow input[name="tempban"]').val(),'tempcontainer':$('.result #viewwindow input[name="tempcontainer"]').val(),'invlist':$('.order#order #tabs4 form[data-id="listform"] input[name="invlist"]').val()},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'sale open.inv.php '+d},
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
									//console.log('發票'+d);
									invnumber=d;
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
						else if(($('.result input[name="sendtype"]').val()=='tempsale'&&!$('.result #funbox .invbut').prop('disabled'))&&(typeof $('.result #funbox .invno').val()=="undefined"||$('.result #funbox .invno').val()=='1')&&$('.order#order .initsetting #useinv').val()=='1'&&Number($('.order#order #tabs4 form[data-id="listform"] input[name="invsalemoney"]').val())>0){//啟用發票&&開立發票
							$.ajax({
								url:'./lib/js/open.inv.php',
								method:'post',
								async:false,
								data:{'machinename':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'tempban':$('.result #viewwindow input[name="tempban"]').val(),'tempcontainer':$('.result #viewwindow input[name="tempcontainer"]').val(),'invlist':$('.order#order #tabs4 form[data-id="listform"] input[name="invlist"]').val()},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'sale open.inv.php '+d},
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
									//console.log('發票'+d);
									invnumber=d;
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
						else if(typeof $('.result input[name="sendtype"]')!="undefined"&&($('.result input[name="sendtype"]').val()=='buytemp'||$('.result #viewwindow input[name="sendtype"]').val()=='tempsale')&&invnumber==''){
							invnumber=$('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html();
						}*/
						//手機POS不開立二聯式發票
						/*if(($('.result input[name="sendtype"]').val()=='result'&&!$('.result #funbox .invbut').prop('disabled'))&&(typeof $('.result #funbox .invno').val()=="undefined"||$('.result #funbox .invno').val()=='1')&&$('.order#order .initsetting #useoinv').val()=='1'&&Number($('.order#order #tabs4 form[data-id="listform"] input[name="invsalemoney"]').val())>0){//啟用傳統發票&&開立傳統發票
							$.ajax({
								url:'./lib/js/open.oinv.php',
								method:'post',
								async:false,
								data:{'machinename':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val(),'consecnumber':$('.order#order #tabs4 form[data-id="listform"] input[name="consecnumber"]').val(),'tempban':$('.result #viewwindow input[name="tempban"]').val(),'tempcontainer':$('.result #viewwindow input[name="tempcontainer"]').val(),'invlist':$('.order#order #tabs4 form[data-id="listform"] input[name="invlist"]').val()},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'sale open.oinv.php '+d},
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
									console.log('發票'+d);
									invnumber=d;
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
						else if(($('.result input[name="sendtype"]').val()=='tempsale'&&!$('.result #funbox .invbut').prop('disabled'))&&(typeof $('.result #funbox .invno').val()=="undefined"||$('.result #funbox .invno').val()=='1')&&$('.order#order .initsetting #useoinv').val()=='1'&&Number($('.order#order #tabs4 form[data-id="listform"] input[name="invsalemoney"]').val())>0){//啟用傳統發票&&開立傳統發票
							$.ajax({
								url:'./lib/js/open.oinv.php',
								method:'post',
								async:false,
								data:{'machinename':$('.order#order .companydata #terminalnumber').val(),'bizdate':$('.viewtemp #date').html(),'consecnumber':$('.viewtemp #listno input[name="consecnumber"]').val(),'tempban':$('.result #viewwindow input[name="tempban"]').val(),'tempcontainer':$('.result #viewwindow input[name="tempcontainer"]').val(),'invlist':$('.order#order #tabs4 form[data-id="listform"] input[name="invlist"]').val()},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'sale open.oinv.php '+d},
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
									//console.log('發票'+d);
									invnumber=d;
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
						else if(typeof $('.result input[name="sendtype"]')!="undefined"&&($('.result input[name="sendtype"]').val()=='buytemp'||$('.result #viewwindow input[name="sendtype"]').val()=='tempsale')&&invnumber==''){
							invnumber=$('.viewtemp #salelist #salecontent .listitems#focus div:eq(3)').html();
						}
						else{
						}*/
						/*if(typeof $('.result input[name="sendtype"]')!="undefined"&&$('.result input[name="sendtype"]').val()=='tempsale'){//暫結需開發票之情況
						}
						else{*/
							/*if(typeof $('.result input[name="sendtype"]')!="undefined"&&$('.result input[name="sendtype"]').val()=='buytemp'){
								$.ajax({
									url:'./lib/js/temptodb.ajax.php',
									method:'post',
									async:false,
									data:{'bizdate':$('.viewtemp #date').html(),'terminalnumber':$('.order#order .companydata #terminalnumber').val(),'numbertag':consecnumber},
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
							else{*/
								$.ajax({
									url:'../demopos/lib/js/temptodb.ajax.php',
									method:'post',
									async:false,
									data:{'bizdate':$('#setup input[name="bizdate"]').val(),'terminalnumber':$('#setup input[name="machinetype"]').val(),'numbertag':consecnumber},
									dataType:'html',
									success:function(d){
										if(d.length>20){
											$.ajax({
												url:'../demopos/lib/js/print.php',
												method:'post',
												data:{'html':'orderpos sale temptodb.ajax.php '+d},
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
										console.log(d);
										console.log('轉移正式'+consecnumber);
									},
									error:function(e){
										//console.log(e);
									}
								});
							//}
						//}
						/*if(typeof $('.result input[name="sendtype"]')!="undefined"&&($('.result input[name="sendtype"]').val()=='buytemp'||$('.result #viewwindow input[name="sendtype"]').val()=='tempsale')){
						}
						else{*/
							$.ajax({//產生工作單
								url:'../demopos/lib/js/create.kitchen.php',
								method:'post',
								async:false,
								data:array1,
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos sale create.kitchen.php '+d},
											dataType:'html',
											success:function(d){/*console.log(d);*/},
											error:function(e){/*console.log(e);*/}
										});
									}
									else{
									}
									console.log('工作單'+d);
								},
								error:function(e){
									//console.log(e);
								}
							});
						//}
						/*if(typeof $('.result input[name="sendtype"]')!="undefined"&&($('.result input[name="sendtype"]').val()=='buytemp'||$('.result #viewwindow input[name="sendtype"]').val()=='tempsale')){
						}
						else{*/
							$.ajax({//產生貼紙
								url:'../demopos/lib/js/create.tag.php',
								method:'post',
								async:false,
								data:array1,
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'../demopos/lib/js/print.php',
											method:'post',
											data:{'html':'orderpos sale create.tag.php '+d},
											dataType:'html',
											success:function(d){/*console.log(d);*/},
											error:function(e){/*console.log(e);*/}
										});
									}
									else{
									}
									console.log('貼紙'+d);
								},
								error:function(e){
									//console.log(e);
								}
							});
						//}
						//手機POS不使用客顯
						/*if($('.order#order .initsetting #secview').val()=='1'){
							$.ajax({
								url:'../secview/cleartemp.ajax.php',
								method:'post',
								async: false,
								data:{'machinename':$('.order#order .companydata #terminalnumber').val()},
								dataType:'html',
								success:function(d){
									if(d.length>20){
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											data:{'html':'sale cleartemp.ajax.php '+d},
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
						}
						else{
						}*/
						$.ajax({
							url:'../demopos/lib/js/create.cmdtxt.php',
							method:'post',
							async: false,
							data:{'cmd':$('#setup input[name="machinetype"]').val()+'-cashdrawer_'+$('#setup input[name="machinetype"]').val()},
							dataType:'html',
							success:function(d){
								//console.log(d);
							},
							error:function(e){
								//console.log(e);
							}
						});

						/*集點樹贈點流程*/
						//手機POS不使用集點樹
						/*if(typeof $('.result input[name="sendtype"]')!="undefined"&&$('.result input[name="sendtype"]').val()=='tempsale'){//暫結需開發票之情況
						}
						else{
							var ptpoint=0;//兌換點數
							var ptpmoney=0;//兌換點數折扣金額
							var ptmoney=parseFloat($('.result #viewwindow #cash').html())+parseFloat($('.result #viewwindow #cashmoney').html());//集點金額:現金+信用卡金額
							if(typeof $('.result .sendviewwindow input[name="otherstring"]').val()!=="undefined"&&$('.result .sendviewwindow input[name="otherstring"]').val().length>0){
								var tempotherstring=$('.result .sendviewwindow input[name="otherstring"]').val().split(",");
								$.each(tempotherstring,function(v,i){
									var t1=i.split(":");
									var t1name=t1[0].split("-");
									var t1point=t1[1].split("=");
									if(t1name[0]=='pointtree'){
										ptpoint=t1point[0];
										ptppoint=t1point[1];
										return false;
									}
									else{
									}
								});
								if($('.result #viewwindow input[name="treetel"]').val().length>0&&Number(ptpoint)>0){
									//start tag
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										data:{'html':'start point-tree-exchange redeem','file':'point-tree'},
										dataType:'html',
										success:function(d){
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
									if(typeof api_exchange_pointtree!=="undefined"&&typeof api_exchange_pointtree==="function"){
										if(typeof $('.result input[name="sendtype"]')!="undefined"&&$('.result input[name="sendtype"]').val()=='buytemp'){
											var exchangeres=api_exchange_pointtree($('.viewtemp #date').html(),consecnumber,$('.result #viewwindow input[name="treetoken"]').val(),$('.result #viewwindow input[name="treetel"]').val(),ptpoint);
										}
										else{
											var exchangeres=api_exchange_pointtree($('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val(),consecnumber,$('.result #viewwindow input[name="treetoken"]').val(),$('.result #viewwindow input[name="treetel"]').val(),ptpoint);
										}
										console.log(exchangeres);
										if(exchangeres==''){
											$.ajax({
												url:'./lib/api/print.ticket.php',
												method:'post',
												async:false,
												data:{'type':'pointtree','message':'point-tree-exchange getrandom 意外錯誤'},
												dataType:'html',
												success:function(d){
													console.log(d);
												},
												error:function(e){
													console.log(e);
												}
											});
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												async:false,
												data:{'html':'point-tree-exchange getrandom 意外錯誤','file':'point-tree'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
										}
										else if(typeof exchangeres['data']!=="undefined"){//success
											//$.ajax({
												//url:'./lib/api/print.ticket.php',
												//method:'post',
												//async:false,
												//data:{'type':'pointtree','tel':$('.result #viewwindow input[name="treetel"]').val(),'user_balance':exchangeres['data']['user_balance']},
												//dataType:'html',
												//success:function(d){
												//	console.log(d);
												//},
												//error:function(e){
												//	console.log(e);
												//}
											//});
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												async:false,
												data:{'html':'pass point-tree-exchange redeem -PHP_EOL-pos_token_tx_id:'+exchangeres['data']['pos_token_tx_id'],'file':'point-tree'},
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
											var message=JSON.parse(exchangeres['responseText']);
											if(exchangeres['status']==='timeout'){
												$.ajax({
													url:'./lib/api/print.ticket.php',
													method:'post',
													async:false,
													data:{'type':'pointtree','message':'point-tree-exchange redeem timeout'},
													dataType:'html',
													success:function(d){
														console.log(d);
													},
													error:function(e){
														console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'point-tree-exchange redeem timeout','file':'point-tree'},
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
											}
											else if(exchangeres['status']=='400'||exchangeres['status']=='406'){
												$.ajax({
													url:'./lib/api/print.ticket.php',
													method:'post',
													async:false,
													data:{'type':'pointtree','status':exchangeres['status'],'code':message['errors'][0]['code'],'message':message['errors'][0]['message']},
													dataType:'html',
													success:function(d){
														console.log(d);
													},
													error:function(e){
														console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'point-tree-exchange redeem error -PHP_EOL-status:'+exchangeres['status']+'-PHP_EOL-code:'+message['errors'][0]['code']+'-PHP_EOL-message:'+message['errors'][0]['message'],'file':'point-tree'},
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
													url:'./lib/api/print.ticket.php',
													method:'post',
													async:false,
													data:{'type':'pointtree','message':'point-tree-exchange redeem 意外錯誤'},
													dataType:'html',
													success:function(d){
														console.log(d);
													},
													error:function(e){
														console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'point-tree-exchange redeem 意外錯誤','file':'point-tree'},
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
											url:'./lib/api/print.ticket.php',
											method:'post',
											async:false,
											data:{'type':'pointtree','message':'api exchange pointtree is not function'},
											dataType:'html',
											success:function(d){
												console.log(d);
											},
											error:function(e){
												console.log(e);
											}
										});
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											async:false,
											data:{'html':'api exchange pointtree is not function','file':'point-tree'},
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
										data:{'html':'end point-tree-exchange redeem','file':'point-tree'},
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
							if($('.order#order .initsetting #pointtree').length>0&&$('.order#order .initsetting #pointtree').val()=='1'){
								if($('.result #viewwindow input[name="treetel"]').val().length>0&&Number(ptmoney)>0){
									//start tag
									$.ajax({
										url:'./lib/js/print.php',
										method:'post',
										data:{'html':'start point-tree-gift transfer','file':'point-tree'},
										dataType:'html',
										success:function(d){
											//console.log(d);
										},
										error:function(e){
											//console.log(e);
										}
									});
									if(typeof api_gift_pointtree!=="undefined"&&typeof api_gift_pointtree==="function"){
										if(typeof $('.result input[name="sendtype"]')!="undefined"&&$('.result input[name="sendtype"]').val()=='buytemp'){
											var giftres=api_gift_pointtree($('.viewtemp #date').html(),consecnumber,$('.result #viewwindow input[name="treetoken"]').val(),$('.result #viewwindow input[name="treetel"]').val(),ptmoney);
										}
										else{
											var giftres=api_gift_pointtree($('.order#order #tabs4 form[data-id="listform"] input[name="bizdate"]').val(),consecnumber,$('.result #viewwindow input[name="treetoken"]').val(),$('.result #viewwindow input[name="treetel"]').val(),ptmoney);
										}
										//console.log(giftres);
										if(giftres==''){
											$.ajax({
												url:'./lib/api/print.ticket.php',
												method:'post',
												async:false,
												data:{'type':'pointtree','message':'point-tree-gift getrandom 意外錯誤'},
												dataType:'html',
												success:function(d){
													console.log(d);
												},
												error:function(e){
													console.log(e);
												}
											});
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												async:false,
												data:{'html':'point-tree-gift getrandom 意外錯誤','file':'point-tree'},
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													//console.log(e);
												}
											});
										}
										else if(typeof giftres['data']!=="undefined"){//success
											$.ajax({
												url:'./lib/api/print.ticket.php',
												method:'post',
												async:false,
												data:{'type':'pointtree','tel':$('.result #viewwindow input[name="treetel"]').val(),'user_balance':giftres['data']['user_balance']},//'give_balance':giftres['data']['give_balance'],
												dataType:'html',
												success:function(d){
													//console.log(d);
												},
												error:function(e){
													console.log(e);
												}
											});
											$.ajax({
												url:'./lib/js/print.php',
												method:'post',
												async:false,
												data:{'html':'pass point-tree-gift transfer -PHP_EOL-pos_token_tx_id:'+giftres['data']['pos_token_tx_id']+'-PHP_EOL-store_balance:'+giftres['data']['store_balance']+'-PHP_EOL-user_balance:'+giftres['data']['user_balance'],'file':'point-tree'},
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
											var message=JSON.parse(giftres['responseText']);
											if(giftres['status']==='timeout'){
												$.ajax({
													url:'./lib/api/print.ticket.php',
													method:'post',
													async:false,
													data:{'type':'pointtree','message':'point-tree-gift transfer timeout'},
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'point-tree-gift transfer timeout','file':'point-tree'},
													dataType:'html',
													success:function(d){
														//console.log(d);
													},
													error:function(e){
														//console.log(e);
													}
												});
											}
											else if(giftres['status']=='400'||giftres['status']=='406'){
												$.ajax({
													url:'./lib/api/print.ticket.php',
													method:'post',
													async:false,
													data:{'type':'pointtree','status':giftres['status'],'code':message['errors'][0]['code'],'message':message['errors'][0]['message']},
													dataType:'html',
													success:function(d){
														console.log(d);
													},
													error:function(e){
														console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'point-tree-gift transfer error -PHP_EOL-status:'+giftres['status']+'-PHP_EOL-code:'+message['errors'][0]['code']+'-PHP_EOL-message:'+message['errors'][0]['message'],'file':'point-tree'},
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
													url:'./lib/api/print.ticket.php',
													method:'post',
													async:false,
													data:{'type':'pointtree','message':'point-tree-gift transfer 意外錯誤'},
													dataType:'html',
													success:function(d){
														console.log(d);
													},
													error:function(e){
														console.log(e);
													}
												});
												$.ajax({
													url:'./lib/js/print.php',
													method:'post',
													async:false,
													data:{'html':'point-tree-gift transfer 意外錯誤','file':'point-tree'},
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
											url:'./lib/api/print.ticket.php',
											method:'post',
											async:false,
											data:{'type':'pointtree','message':'api gift pointtree is not function'},
											dataType:'html',
											success:function(d){
												console.log(d);
											},
											error:function(e){
												console.log(e);
											}
										});
										$.ajax({
											url:'./lib/js/print.php',
											method:'post',
											async:false,
											data:{'html':'api gift pointtree is not function','file':'point-tree'},
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
										data:{'html':'end point-tree-gift transfer','file':'point-tree'},
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
						}*/
						
						//手機POS不使用找零提示
						/*if($('.order#order .initsetting #changehint').val()=='1'){//找零提示視窗
							result.dialog('close');
							$('.changehint .inv').val(invnumber);
							$('.changehint .should').val($('.result #viewwindow #should').html());
							$('.changehint .already').val($('.result #viewwindow #already').html());
							$('.changehint .change').val($('.result #viewwindow #change').html());
							change.dialog('open');
						}
						else{
							if(viewtemp.dialog('isOpen')){//暫結開立發票
								alert('發票開立完成。');
								ClearWindow('','');
								result.dialog('close');
								viewtemp.dialog('close');
								$('.order#order #list #funbox #view').trigger('click');
								$.ajax({
									url:'./lib/js/check.booking.php',
									method:'post',
									async:false,
									data:{'machinetype':$('.order#order .companydata #terminalnumber').val()},
									dataType:'html',
									success:function(d){
										if(d=='success'){
											if($('.order#order #content #MemberBill #list #funbox #point').css('background-color')=='red'){
											}
											else{
												$('.order#order #content #MemberBill #list #funbox #point').css({'background-color':'red','z-index':'3'});
											}
										}
										else{
											$('.order#order #content #MemberBill #list #funbox #point').css({'background-color':'','z-index':''});
										}
										//console.log(d);
									},
									error:function(e){
										console.log(e);
									}
								});
							}
							else{
								window.setTimeout(function(){
									if($('.order#order .initsetting #controltable').val()=='1'){
										result.dialog('close');
										inittable.dialog('open');
										ClearWindow('','');
									}
									else{
										//if($('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="usercode"]').val().length>0){
											//location.href='./order.php?usercode='+$('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="usercode"]').val()+'&username='+$('.order#order #MemberBill #tabs4 form[data-id="listform"] input[name="username"]').val();
											result.dialog('close');
											ClearWindow('','');
										//}
										//else{
										//	location.href='./order.php';
										//}
									}},500);
							}
						}*/
					//}
					$('#setup').submit();
				}
			//}
		}
	});
});