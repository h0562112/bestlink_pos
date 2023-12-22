/**
function參數不建議使用預設值，因為ipad的safari瀏覽器"不允許"
**/
/*會員點數與儲值金*/
/*會員結帳與作廢帳單(非"儲值"儲值金)更新雲端會員的點數與儲值金*/
/*
 company:門市體系代碼
 story:門市代碼
 memno:會員編號
 paymoney:支付金額；用於計算該贈與多少點數(＊"儲值"儲值金要使用另一個function)；作廢時符號為 -
 giftpoint:贈與點數；作廢時符號為 -
 memberpoint:使用多少會員點數；作廢時符號為 -
 membermoney:使用多少會員儲值金；作廢時符號為 -
*/
function api_point_money_checkserver(company,story){
	$.ajax({
		url:'http://api.tableplus.com.tw/outposandorder/memberapi/check_server.ajax.php',
		method:'post',
		async:false,
		data:{'company':company,'story':story},
		dataType:'json',
		timeout:5000,
		success:function(d){
			res=d;
			$.ajax({
				url:'./lib/js/print.php',
				method:'post',
				data:{'html':'api.tableplus.com.tw/outposandorder/memberapi/check_server.ajax.php(online success) '+d},
				dataType:'html',
				success:function(d){/*console.log(d);*/},
				error:function(e){/*console.log(e);*/}
			});
		},
		error:function(e,t){
			$.ajax({
				url:'./lib/js/print.php',
				method:'post',
				data:{'html':'api.tableplus.com.tw/outposandorder/memberapi/check_server.ajax.php(online error) '+e},
				dataType:'html',
				success:function(d){/*console.log(d);*/},
				error:function(e){/*console.log(e);*/}
			});
			if(t==="timeout"){
				res='[{"state":"fail","message":"AJAX timeout"}]';
			}
			else{
				res=e;
			}
		}
	});
	return res;
};
function api_point_money_ourmember(company,story,memno,paymoney,giftpoint,memberpoint,membermoney,usemoney,giftmoney,state,recommend1,recom1point,recommend2,recom2point){
	if(typeof giftpoint==='undefined'){
		giftpoint=0;
	}
	else{
	}
	if(typeof memberpoint==='undefined'){
		memberpoint=0;
	}
	else{
	}
	if(typeof membermoney==='undefined'){
		membermoney=0;
	}
	else{
	}
	if(typeof usemoney==='undefined'){
		usemoney=0;
	}
	else{
	}
	if(typeof giftmoney==='undefined'){
		giftmoney=0;
	}
	else{
	}
	if(typeof state==='undefined'){
		state='';
	}
	else{
	}
	if(typeof recommend1==='undefined'){
		recommend1='';
	}
	else{
	}
	if(typeof recom1point==='undefined'){
		recom1point='0';
	}
	else{
	}
	if(typeof recommend2==='undefined'){
		recommend2='';
	}
	else{
	}
	if(typeof recom2point==='undefined'){
		recom2point='0';
	}
	else{
	}
	var res='';
	$.ajax({
		url:'http://api.tableplus.com.tw/outposandorder/memberapi/point_money.ajax.php',
		method:'post',
		async:false,
		data:{'company':company,'story':story,'memno':memno,'paymoney':paymoney,'giftpoint':giftpoint,'memberpoint':memberpoint,'membermoney':membermoney,'usemoney':usemoney,'giftmoney':giftmoney,'state':state,'recommend1':recommend1,'recom1point':recom1point,'recommend2':recommend2,'recom2point':recom2point},
		dataType:'json',
		timeout:5000,
		success:function(d){
			res=d;
			$.ajax({
				url:'./lib/js/print.php',
				method:'post',
				data:{'html':'api.tableplus.com.tw/outposandorder/memberapi/point_money.ajax.php(online success) '+d},
				dataType:'html',
				success:function(d){/*console.log(d);*/},
				error:function(e){/*console.log(e);*/}
			});
			//console.log(res);
		},
		error:function(e,t){
			$.ajax({
				url:'./lib/js/print.php',
				method:'post',
				data:{'html':'api.tableplus.com.tw/outposandorder/memberapi/point_money.ajax.php(online error) '+e},
				dataType:'html',
				success:function(d){/*console.log(d);*/},
				error:function(e){/*console.log(e);*/}
			});
			if(t==="timeout"){
				res='[{"state":"fail","message":"AJAX timeout"}]';
			}
			else{
				res=e;
			}
		}
	});
	return res;
}
/*會員"儲值"儲值金*/
/*
未實作
 company:門市體系代碼
 story:門市代碼
 memno:會員編號
 paymoney:"儲值"的金額
 */
/*function api_save_membermoney_ourmember(company,story,memno,paymoney){
	var res='';
	$.ajax({
		url:'http://www.quickcode.com.tw/outposandorder/memberapi/save_membermoney.ajax.php',
		method:'post',
		async:false,
		data:{'company':company,'story':story,'memno':memno,'paymoney':paymoney},
		dataType:'json',
		timeout:5000,
		success:function(d){
			res=d;
		},
		error:function(e){
			res=e;
		}
	});
	return res;
}*/