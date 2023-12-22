function pxpayplus_Payment(url,MerchantCode,SecrectKey,PayToken,MerEnName,pxstorecode,depname,tradeno,Amount,item_json){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/pxpayplus/Payment.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'MerchantCode':MerchantCode,'SecrectKey':SecrectKey,'PayToken':PayToken,'MerEnName':MerEnName,'storeid':pxstorecode,'storename':depname,'tradeno':tradeno,'Amount':Amount,'item_json':item_json},
		timeout:15000,//15秒逾時
		success:function(d){
			//console.log(d);
			res.resolve(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			//console.log(XMLHttpRequest);
			res.resolve(XMLHttpRequest);
		}
	});

	return res.promise();
}

function pxpayplus_Refund(url,MerchantCode,SecrectKey,MerEnName,pxstorecode,depname,mertradeno,oldmertradeno,pxtradeno,Amount,item_json){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/pxpayplus/Refund.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'MerchantCode':MerchantCode,'SecrectKey':SecrectKey,'MerEnName':MerEnName,'storeid':pxstorecode,'storename':depname,'mertradeno':mertradeno,'oldmertradeno':oldmertradeno,'pxtradeno':pxtradeno,'Amount':Amount,'item_json':item_json},
		timeout:15000,//15秒逾時
		success:function(d){
			//console.log(d);
			res.resolve(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			//console.log(XMLHttpRequest);
			res.resolve(XMLHttpRequest);
		}
	});

	return res.promise();
}

function pxpayplus_Reversal(url,MerchantCode,SecrectKey,PayToken,MerEnName,pxstorecode,depname,mertradeno,Amount){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/pxpayplus/Reversal.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'MerchantCode':MerchantCode,'SecrectKey':SecrectKey,'PayToken':PayToken,'MerEnName':MerEnName,'storeid':pxstorecode,'storename':depname,'mertradeno':mertradeno,'Amount':Amount},
		timeout:15000,//15秒逾時
		success:function(d){
			//console.log(d);
			res.resolve(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			//console.log(XMLHttpRequest);
			res.resolve(XMLHttpRequest);
		}
	});

	return res.promise();
}

function pxpayplus_OrderStatus(url,MerchantCode,SecrectKey,depname,ordernotype,tradeno){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/pxpayplus/OrderStatus.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'MerchantCode':MerchantCode,'SecrectKey':SecrectKey,'MerEnName':depname,'ordernotype':ordernotype,'tradeno':tradeno},
		timeout:15000,//15秒逾時
		success:function(d){
			//console.log(d);
			res.resolve(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			//console.log(XMLHttpRequest);
			res.resolve(XMLHttpRequest);
		}
	});

	return res.promise();
}