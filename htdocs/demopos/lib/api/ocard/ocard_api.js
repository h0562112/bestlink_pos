//取得授權
function ocard_auth(url,key,secret){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/auth.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'key':key,'secret':secret},
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

//取得會員資料
function ocard_getProfile(url,uid,token,searchtype,searchdata){
	/*
	searchtype:查詢1>>手機號碼或2>>會員條碼
	searchdata:查詢內容(手機號碼或會員條碼)
	*/
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/getProfile.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'searchtype':searchtype,'searchdata':searchdata},
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

//更新會員資料；這部分POS不打算主動修正資料，目前不實做
/*function ocard_updateProfile(url,uid,token,cell,name){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/updateProfile.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'cell':cell,'name':name},
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
}*/

//集點；進行紅利集點，同時對新顧客發送會員邀請。建議於完成結帳交易時呼叫。
function ocard_givePoint(url,uid,token,trans_serial,cell,transDetail,tag,visit_time,invoice_no){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/givePoint.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'trans_serial':trans_serial,'cell':cell,'transDetail':transDetail,'tag':tag,'visit_time':visit_time,'invoice_no':invoice_no},
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

//上傳交易明細；但沒有任何會員資訊，應該不會使用到
function ocard_transDetail(url,uid,token,trans_serial,detail){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/transDetail.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'trans_serial':trans_serial,'detail':detail},
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

//批次上傳交易明細；但沒有任何會員資訊，應該不會使用到
function ocard_batchTransDetail(url,uid,token,data){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/batchTransDetail.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'data':data},
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

//核銷；即時核銷點數商品或是禮物券
function ocard_redeem(url,uid,token,trans_serial,code,count){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/redeem.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'trans_serial':trans_serial,'code':code,'count':count},
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

//還原核銷
function ocard_recoverRedeem(url,uid,token,code){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/recoverRedeem.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'code':code},
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

//現金折抵；使用點數進行現金折抵(需預先設定好現金折抵之點數品項)
function ocard_cashDiscount(url,uid,token,trans_serial,searchtype,searchdata,amount){
	/*
	searchtype:查詢1>>手機號碼或2>>會員條碼
	searchdata:查詢內容(手機號碼或會員條碼)
	*/
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/cashDiscount.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'trans_serial':trans_serial,'searchtype':searchtype,'searchdata':searchdata,'amount':amount},
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

//還原現金折抵
function ocard_recoverCashDiscount(url,uid,token,trans_serial,searchtype,searchdata,amount){
	/*
	searchtype:查詢1>>手機號碼或2>>會員條碼
	searchdata:查詢內容(手機號碼或會員條碼)
	*/
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/recoverCashDiscount.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'trans_serial':trans_serial},
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

//計算點數；預先計算本次消費可以獲得之點數。
function ocard_calPoint(url,uid,token,spend){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/calPoint.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'spend':spend},
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

//檢查核銷；不進行核銷，只確認是否能核銷並取得 pos_code
function ocard_checkRedeem(url,uid,token,code,count,transDetail,checkSpend){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/checkRedeem.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'code':code,'count':count,'transDetail':transDetail,'checkSpend':checkSpend},
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

//複合式交易；於出發票時一次完成兌點、累點、兌券
function ocard_mixTrans(url,uid,token,trans_serial,searchtype,searchdata,redeem,spend,visit_time,point_redeem,transDetail,checkSpend,invoice_no){
	/*
	searchtype:查詢1>>手機號碼或2>>會員條碼
	searchdata:查詢內容(手機號碼或會員條碼)
	*/
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/mixTrans.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'trans_serial':trans_serial,'searchtype':searchtype,'searchdata':searchdata,'redeem':redeem,'spend':spend,'visit_time':visit_time,'point_redeem':point_redeem,'transDetail':transDetail,'checkSpend':checkSpend,'invoice_no':invoice_no},
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

//還原累點交易；還原複合交易(作廢)、累點交易，請注意使用此 API 的話，如果交易有使用禮物券、點數等行為都不會被還原。
function ocard_recoverGivePoint(url,uid,token,trans_serial){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/recoverGivePoint.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'trans_serial':trans_serial},
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

//還原交易；還原複合交易(作廢)、點數交易或核銷交易
function ocard_recoverTrans(url,uid,token,trans_serial){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/recoverTrans.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'trans_serial':trans_serial},
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

//取得品牌優惠資訊；
function ocard_getBasicData(url,uid,token,field){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/getBasicData.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'field':field},
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

//發送禮物劵(不打算實作這部分)
/*function ocard_giveCoupon(url,uid,token,coupon_model_id,cell,count){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/giveCoupon.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'coupon_model_id':coupon_model_id,'cell':cell,'count':count},
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
}*/

//發送刮刮卡(不打算實作這部分)
/*function ocard_giveDraw(url,uid,token,draw_model_id,cell){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/giveDraw.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'draw_model_id':draw_model_id,'cell':cell},
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
}*/

//給會員資格；發送會員邀請，預設發送第一級會員。若有要進行集點可以省略此流程。
function ocard_giveVip(url,uid,token,cell,name){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/giveVip.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'cell':cell,'name':name},
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

//修改會員資格；發送會員邀請，預設發送第一級會員。若有要進行集點可以省略此流程。
function ocard_changeVip(url,uid,token,cell,vip_id){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/ocard/changeVip.php',
		method:'post',
		//async:false,
		cache:false,
		dataType:'json',
		data:{'url':url,'uid':uid,'token':token,'cell':cell,'vip_id':vip_id},
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