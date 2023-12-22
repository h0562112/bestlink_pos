function nccc_iscomplete(ncccdate){
	var res=$.Deferred();
	
	$.ajax({
		url:'./lib/api/nccc/check.out.php',
		method:'post',
		/*async:false,*/
		data:{'date':ncccdate},
		dataType:'json',
		timeout:65000,//刷卡機為60秒逾時，這邊多給30秒來檢查(90秒逾時)
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