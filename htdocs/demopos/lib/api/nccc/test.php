<script type="text/javascript" src="../../../../tool/jquery-1.12.4.js?<?php echo date('His'); ?>"></script>
<script type="text/javascript" src="./nccc_api.js?<?php echo date('His'); ?>"></script>
<script>
	$(document).ready(function(){
		var res='';
		res=nccc_iscomplete('20220331');
		res.done(function(res){
			console.log(res);
		});
	});
</script>

<button>操作</button>
<?php

?>