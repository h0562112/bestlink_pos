/**
function參數不建議使用預設值，因為ipad的safari瀏覽器"不允許"
**/
function api_easycard(type,machine,intellaconsecnumber,total,response){//悠遊卡
	if(typeof intellaconsecnumber==='undefined'){
		intellaconsecnumber='';
	}
	else{
	}
	if(typeof total==='undefined'){
		total=0;
	}
	else{
	}
	if(typeof response==='undefined'){
		response='';
	}
	else{
	}
	//console.log(response);
	var res=$.Deferred();
	if(type=='Payment'){//扣款
		$.ajax({
			url:'./lib/api/intella/easycard_payment_api.php',
			method:'post',
			//async:false,
			data:{'type':type,'machine':machine,'intellaconsecnumber':intellaconsecnumber,'total':total},
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
	}
	else if(type=='Refund'){//退款
		$.ajax({
			url:'./lib/api/intella/easycard_refund_api.php',
			method:'post',
			//async:false,
			data:{'type':type,'machine':machine,'intellaconsecnumber':intellaconsecnumber,'total':total},
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
	}
	else if(type=='SignOn'){//登入
		$.ajax({
			url:'./lib/api/intella/easycard_signon_api.php',
			method:'post',
			//async:false,
			data:{'type':type,'machine':machine},
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
	}
	else if(type=='BalanceQuery'){//查詢餘額
		$.ajax({
			url:'./lib/api/intella/easycard_balancequery_api.php',
			method:'post',
			//async:false,
			data:{'type':type,'machine':machine},
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
	}
	else if(type=='Retry'){//重試
		$.ajax({
			url:'./lib/api/intella/easycard_retry_api.php',
			method:'post',
			//async:false,
			data:{'type':type,'machine':machine,'intellaconsecnumber':intellaconsecnumber,'total':total,'response':response},
			dataType:'json',
			timeout:30000,//30秒逾時
			success:function(d){
				data=JSON.parse(d);
				//console.log(d);
				//console.log('retry fail '+response['Data']['Retry']+d['Data']['ErrorCode']);
				if(data['Data']['request']['Retry']>=3&&(data['Data']['ErrorCode']=='000125'||data['Data']['ErrorCode']=='0462xx')){//重複嘗試三次交易失敗
					$.ajax({
						url:'./lib/api/intella/easycard_log_api.php',
						method:'post',
						//async:false,
						data:{'type':data['Header']['ServiceType'],'machine':machine,'response':d},
						dataType:'json',
						success:function(d){
							//res.resolve({message:"retryfail"});
							//console.log(d);
						},
						error:function(XMLHttpRequest, textStatus, errorThrown){
							//res.resolve(XMLHttpRequest);
							//console.log(XMLHttpRequest);
						}
					});
					res.resolve(d);
				}
				else{//重送交易
					res.resolve(d);
					//d['Data']['Retry']=parseInt(response['Data']['Retry'])+1;
					//res=api_easycard(type,machine,intellaconsecnumber,total,d);
				}
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				res.resolve(XMLHttpRequest);
				//console.log(XMLHttpRequest);
			}
		});
	}
	else{
		res.resolve({message:"errorinput"});
	}
	//console.log(res);
	return res.promise();
}
function api_intellaother(consecnumber,total,dep,usercode,machine,type,path,datapath,callbackurl){//消費者主掃
	if(typeof machine==='undefined'){
		machine='m1';
	}
	else{
	}
	if(typeof type==='undefined'){
		type='1';
	}
	else{
	}
	if(typeof path==='undefined'){
		path='.';
	}
	else{
	}
	if(typeof datapath==='undefined'){
		datapath='../../../..';
	}
	else{
	}
	if(typeof callbackurl==='undefined'){
		callbackurl='';
	}
	else{
	}
	$.ajax({
		url:path+'/lib/api/intella/intellaother_api.php',
		method:'post',
		async:false,
		data:{'consecnumber':consecnumber,'total':total,'dep':dep,'usercode':usercode,'machine':machine,'type':type,'datapath':datapath,'callbackurl':callbackurl},
		dataType:'html',
		timeout:20000,//20秒逾時
		success:function(d){
			//console.log(d);
			res=d;
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			console.log(textStatus);
			res=textStatus;
		}
	});
	return res;
};
function api_void_intellaother(consecnumber,total,dep,usercode,machine){
	if(typeof machine==='undefined'){
		machine='m1';
	}
	else{
	}
	var res=$.Deferred();
	$.ajax({
		url:'./lib/api/intella/void_intellaother_api.php',
		method:'post',
		//async:false,
		data:{'consecnumber':consecnumber,'total':total,'dep':dep,'usercode':usercode,'machine':machine},
		dataType:'json',
		timeout:30000,//20秒逾時
		success:function(d){
			//console.log(d);
			res.resolve(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			res.resolve(XMLHttpRequest);
		}
	});
	return res.promise();
};
function api_intellauser(consecnumber,total,dep,usercode,authcode,machine){//消費者被掃
	if(typeof machine==='undefined'){
		machine='m1';
	}
	else{
	}
	var res=$.Deferred();
	$.ajax({
		url:'./lib/api/intella/intellauser_api.php',
		method:'post',
		async:false,
		data:{'consecnumber':consecnumber,'total':total,'dep':dep,'usercode':usercode,'machine':machine,'authcode':authcode},
		dataType:'json',
		timeout:20000,//20秒逾時
		success:function(d){
			//console.log(d);
			res.resolve(d);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			res.resolve(XMLHttpRequest);
		}
	});
	return res;
};