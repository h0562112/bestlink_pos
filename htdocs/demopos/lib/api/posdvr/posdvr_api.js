/**
function參數不建議使用預設值，因為ipad的safari瀏覽器"不允許"
**/
/*錢都錄監控傳送訊息*/
function api_sendmessage_posdvr(filename){
	if($('#posdvrPort').length>0){
		/*$.ajax({
			url:'./lib/api/posdvr/passmessage.php',
			method:'get',
			async:false,
			data:{'filename':filename},
			dataType:'html',
			success:function(d){
				console.log(d);
				return d;
			},
			error:function(e){
				console.log(e);
				return e;
			}
		});*/
		$('#posdvrPort').prop('src','./lib/api/posdvr/passmessage.php?filename='+filename);
		//console.log($('#posdvrPort').prop('src'));
	}
	else{
		return 'empty';
	}
}
