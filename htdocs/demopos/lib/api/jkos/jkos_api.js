function jkos_Payment(url,SystemName,MerchantKey,MerchantID,StoreID,StoreName,MerchantTradeNo,CardToken,TradeAmount){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/jkos/Payment.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'SystemName':SystemName,'MerchantKey':MerchantKey,'MerchantID':MerchantID,'StoreID':StoreID,'StoreName':StoreName,'MerchantTradeNo':MerchantTradeNo,'CardToken':CardToken,'TradeAmount':TradeAmount},
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

function jkos_Cancel(url,SystemName,MerchantKey,MerchantID,StoreID,StoreName,CardToken,TradeAmount,MerchantTradeNo){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/jkos/Cancel.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'SystemName':SystemName,'MerchantKey':MerchantKey,'MerchantID':MerchantID,'StoreID':StoreID,'StoreName':StoreName,'CardToken':CardToken,'CardToken':CardToken,'TradeAmount':TradeAmount,'MerchantTradeNo':MerchantTradeNo},
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

function jkos_Refund(url,SystemName,MerchantKey,MerchantID,StoreID,StoreName,MerchantTradeNo,TradeNo,TradeAmount){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/jkos/Refund.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'SystemName':SystemName,'MerchantKey':MerchantKey,'MerchantID':MerchantID,'StoreID':StoreID,'StoreName':StoreName,'MerchantTradeNo':MerchantTradeNo,'TradeNo':TradeNo,'TradeAmount':TradeAmount},
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
//2022/10/20 該API還是有存在必要，因為退款仍然要用到查詢機制//2022/10/19 API文件：除了像停車場繳費機設備這樣交易流程不是需要高校的時效性，像是門市人員面對顧客交易的情況，一般建議若有異常，直接進入取消交易流程
function jkos_Inquiry(url,SystemName,MerchantKey,MerchantID,StoreID,StoreName,InquiryType,MerchantTradeNo){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/jkos/Inquiry.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'SystemName':SystemName,'MerchantKey':MerchantKey,'MerchantID':MerchantID,'StoreID':StoreID,'StoreName':StoreName,'InquiryType':InquiryType,'MerchantTradeNo':MerchantTradeNo},
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