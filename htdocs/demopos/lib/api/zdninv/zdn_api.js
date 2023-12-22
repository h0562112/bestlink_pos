/**
function參數不建議使用預設值，因為ipad的safari瀏覽器"不允許"
**/
function api_zdn_gettoken(url,id,psw,type){//type:getCurToken>>取得中鼎驗證token,renew>>更新中鼎驗證token
	if(typeof type==='undefined'){
		type='getCurToken';
	}
	else{
	}
	//console.log(response);
	var res=$.Deferred();
	$.ajax({
		url:'./lib/api/zdninv/'+type+'.php',
		method:'post',
		//async:false,
		data:{'url':url,'id':id,'psw':psw},
		dataType:'json',
		timeout:30000,//30秒逾時
		success:function(d){
			res.resolve(d);
			//console.log(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			res.resolve(XMLHttpRequest);
			//console.log(XMLHttpRequest);
		}
	});
	//console.log(res);
	return res.promise();
}
function api_zdn_showTrack(url,id,token,period){//發票狀況
	//console.log(response);
	var res=$.Deferred();
	$.ajax({
		url:'./lib/api/zdninv/showTrack.php',
		method:'post',
		//async:false,
		data:{'url':url,'id':id,'token':token,'period':period},
		dataType:'json',
		timeout:30000,//30秒逾時
		success:function(d){
			res.resolve(d);
			//console.log(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			res.resolve(XMLHttpRequest);
			//console.log(XMLHttpRequest);
		}
	});
	//console.log(res);
	return res.promise();
}
function api_zdn_getInv(url,id,token,booklet){//取得發票
	if(typeof booklet==='undefined'){
		booklet='1';
	}
	else{
	}
	//console.log(response);
	var res=$.Deferred();
	$.ajax({
		url:'./lib/api/zdninv/getInv.php',
		method:'post',
		//async:false,
		data:{'url':url,'id':id,'token':token,'booklet':booklet},
		dataType:'json',
		timeout:30000,//30秒逾時
		success:function(d){
			res.resolve(d);
			//console.log(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			res.resolve(XMLHttpRequest);
			//console.log(XMLHttpRequest);
		}
	});
	//console.log(res);
	return res.promise();
}
