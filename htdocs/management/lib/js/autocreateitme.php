<script src="../../../tool/jquery-1.12.4.js"></script>
<script src="../../../tool/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).ready(function(){
	function add(i){
		if(i>=80){
		}
		else{
			setTimeout(function(){ 
				$.ajax({
					url:'./saveitem.ajax.php',
					method:'post',
					async:false,
					data:{'company':'hktv','dep':'hktv0001','seq':'','number':'','front':'1','name1':'小姐'+i,'size1':'14','color1':'#000000','name2':'','size2':'14','color2':'#000000','money1':'0','mname11':'','mname12':'','money2':'','mname21':'','mname22':'','money3':'','mname31':'','mname32':'','money4':'','mname41':'','mname42':'','money5':'','mname51':'','mname52':'','money6':'','mname61':'','mname62':''},
					dataType:'html',
					success:function(d){
						console.log(i);
						add(++i);
						console.log(i);
					},
					error:function(e){
						console.log(e);
					}
				});
			}, 1000);
		}
	};
	add(0);
});
</script>