/**
function參數不建議使用預設值，因為ipad的safari瀏覽器"不允許"
**/
function api_readrfid(){//讀取RFID
	//console.log(response);
	var res=$.Deferred();

	$.ajax({
		url:'./lib/api/rfid/splitrfidstring.php',
		//async:false,
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
	
	return res.promise();
}

function api_single_readrfid(){//讀取RFID
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/rfid/readsingle.php',
		//async:false,
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

	return res.promise();
}