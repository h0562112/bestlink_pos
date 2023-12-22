/**
function參數不建議使用預設值，因為ipad的safari瀏覽器"不允許"
**/
function api_itri(type,couponsn){//工研院商業獅優惠卷
	if(typeof couponsn==='undefined'){
		couponsn='';
	}
	else{
	}
	var res='';
	if(type=='Coupon_Exchange_CouponCert'){
		$.ajax({
			url:'./lib/api/itri/exchange_coupon_api.php',
			method:'post',
			async:false,
			data:{'couponsn':couponsn},
			dataType:'json',
			timeout:30000,//30秒逾時
			success:function(d){
				res=d;
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				res=textStatus;
			}
		});
	}
	/*else if(type=='Coupon_Get_Detail'){
		$.ajax({
			url:'./lib/api/itri/get_coupon_detail_api.php',
			method:'post',
			async:false,
			data:{'couponsn':couponsn},
			dataType:'json',
			timeout:30000,//30秒逾時
			success:function(d){
				res=d;
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				res=textStatus;
			}
		});
	}
	else if(type=='Coupon_Get_CouponCert'){
		$.ajax({
			url:'./lib/api/itri/get_coupon_cert_api.php',
			method:'post',
			async:false,
			data:{'couponsn':couponsn},
			dataType:'json',
			timeout:30000,//30秒逾時
			success:function(d){
				res=d;
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				res=textStatus;
			}
		});
	}
	else if(type=='Coupon_Cancel_CouponCert'){
		$.ajax({
			url:'./lib/api/itri/cancel_coupon_cert_api.php',
			method:'post',
			async:false,
			data:{'couponsn':couponsn},
			dataType:'json',
			timeout:30000,//30秒逾時
			success:function(d){
				res=d;
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				res=textStatus;
			}
		});
	}
	else if(type=='Coupon_Query_GiftSnStatus'){
		$.ajax({
			url:'./lib/api/itri/query_coupon_status_api.php',
			method:'post',
			async:false,
			data:{'couponsn':couponsn},
			dataType:'json',
			timeout:30000,//30秒逾時
			success:function(d){
				res=d;
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				res=textStatus;
			}
		});
	}*/
	else{
	}
	return res;
};