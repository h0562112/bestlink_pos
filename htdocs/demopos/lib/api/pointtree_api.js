/**
function參數不建議使用預設值，因為ipad的safari瀏覽器"不允許"
**/
/*集點樹查詢剩餘點數*/
function api_search_pointtree(token,mobile){
	var res='';
	$.ajax({
		url:'https://pointtree.com.tw/api/v1/pos/members/information',
		method:'get',
		contentType:'application/x-www-form-urlencoded; charset=UTF-8',
		async:false,
		data:{'token':token,'mobile_number':mobile},
		dataType:'json',
		timeout:5000,
		success:function(d){
			//console.log(d);
			res=d;
			//return d;
		},
		error:function(e){
			//console.log(e);
			res=e;
			//return e;
		}
	});
	//console.log(res);
	return res;
}
/*集點樹贈點流程*/
function api_gift_pointtree(bizdate,consecnumber,token,mobile,total){
	var res='';
	$.ajax({
		url:'./lib/api/getrandom.ajax.php',
		method:'post',
		async:false,
		data:{'bizdate':bizdate,'consecnumber':consecnumber,'token':token,'mobilephone':mobile,'total':total},
		dataType:'html',
		success:function(d){
			//console.log(d);
			if(d.match(';PHP;')){
				var tempd=d.split(';PHP;');
				$.ajax({
					url:'https://pointtree.com.tw/api/v1/pos/transfer',
					method:'post',
					async:false,
					timeour:5000,
					data:{'token':token,'mobile_number':mobile,'idempotency_key':tempd[0],'product_total_price':total,'branch_store_information':tempd[1]},
					dataType:'json',
					success:function(d){
						res=d;
						if(d['data']['pos_token_tx_id'].length==0){
							console.log(d);
						}
						else{
							$.ajax({
								url:'./lib/api/pointtree/save.id.php',
								method:'post',
								async:false,
								data:{'bizdate':bizdate,'consecnumber':consecnumber,'token':token,'mobile_number':mobile,'pos_token_tx_id':d['data']['pos_token_tx_id']},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
					},
					error:function(e){
						res=e;
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
	//console.log(res);
	return res;
}
/*集點樹收點流程*/
function api_void_pointtree(bizdate,consecnumber){
	var res='';
	$.ajax({
		url:'./lib/api/voidpoint.ajax.php',
		method:'post',
		async:false,
		data:{'bizdate':bizdate,'consecnumber':consecnumber},
		dataType:'html',
		success:function(d){
			//console.log(d);
			if(d.match(';PHP;')){
				var tempd=d.split(';PHP;');
				$.ajax({
					url:'https://pointtree.com.tw/api/v1/pos/reclaim',
					method:'post',
					async:false,
					timeour:5000,
					data:{'token':tempd[0],'mobile_number':tempd[1],'idempotency_key':tempd[2],'price':tempd[3],'branch_store_information':tempd[4]},
					dataType:'json',
					success:function(d){
						res=d;
					},
					error:function(e){
						res=e;
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
	return res;
}
/*集點樹兌換點數*/
function api_exchange_pointtree(bizdate,consecnumber,token,mobile,point){
	var res='';
	$.ajax({
		url:'./lib/api/getrandom.ajax.php',
		method:'post',
		async:false,
		data:{'bizdate':bizdate,'consecnumber':consecnumber,'token':token,'mobilephone':mobile,'point':point},
		dataType:'html',
		success:function(d){
			//console.log(d);
			if(d.match(';PHP;')){
				var tempd=d.split(';PHP;');
				$.ajax({
					url:'https://pointtree.com.tw/api/v1/pos/points/redeem',
					method:'post',
					async:false,
					timeour:5000,
					data:{'token':token,'mobile_number':mobile,'idempotency_key':tempd[0],'point_amount':point,'branch_store_information':tempd[1]},
					dataType:'json',
					success:function(d){
						res=d;
						if(d['data']['pos_token_tx_id'].length==0){
							console.log(d);
						}
						else{
							$.ajax({
								url:'./lib/api/pointtree/save.id.php',
								method:'post',
								async:false,
								data:{'bizdate':bizdate,'consecnumber':consecnumber,'token':token,'mobile_number':mobile,'pos_token_tx_id':d['data']['pos_token_tx_id']},
								dataType:'html',
								success:function(d){
									//console.log(d);
								},
								error:function(e){
									//console.log(e);
								}
							});
						}
					},
					error:function(e){
						res=e;
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
	//console.log(res);
	return res;
}