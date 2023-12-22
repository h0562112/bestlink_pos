function directlinepay_payment(company,dep,url,channelid,channelsecret,money,orderid,code){
	var res=$.Deferred();
	$.ajax({
		url:'http://api.tableplus.com.tw/outposandorder/directlinepay/payments.php',
		method:'post',
		//async:,
		data:{'company':company,'dep':dep,'url':url,'channelid':channelid,'channelsecret':channelsecret,'money':money,'orderid':orderid,'code':code},
		dataType:'json',
		timeout:25000,//25秒逾時(linepay是20秒逾時，這邊多給5秒等待)
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
function directlinepay_payment_statuscheck(company,dep,url,channelid,channelsecret,orderid){
	var res=$.Deferred();
	$.ajax({
		url:'http://api.tableplus.com.tw/outposandorder/directlinepay/status_check.php',
		method:'post',
		//async:,
		data:{'company':company,'dep':dep,'url':url,'channelid':channelid,'channelsecret':channelsecret,'orderid':orderid},
		dataType:'json',
		timeout:25000,//25秒逾時(linepay是20秒逾時，這邊多給5秒等待)
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
function directlinepay_refund(company,dep,url,channelid,channelsecret,orderid){
	var res=$.Deferred();
	$.ajax({
		url:'http://api.tableplus.com.tw/outposandorder/directlinepay/refund.php',
		method:'post',
		//async:,
		data:{'company':company,'dep':dep,'url':url,'channelid':channelid,'channelsecret':channelsecret,'orderid':orderid},
		dataType:'json',
		timeout:25000,//25秒逾時(linepay是20秒逾時，這邊多給5秒等待)
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