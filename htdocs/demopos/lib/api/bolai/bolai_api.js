/**
function參數不建議使用預設值，因為ipad的safari瀏覽器"不允許"
**/
/*查詢會員資訊*/
//function api_search_bolai(){
//}
/*儲值金額*/
function api_deposit_bolai(type,memcard,money){
	var res='';
	$.ajax({
		url:'./lib/api/bolai/deposit.ajax.php',
		method:'post',
		async:false,
		data:{'type':type,'memcard':memcard,'money':money},
		dataType:'json',
		success:function(d){
			res=d;
		},
		error:function(e){
			res=e;
		}
	});
	return res;
}