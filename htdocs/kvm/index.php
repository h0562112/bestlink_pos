<!DOCTYPE html>
<html lang="en">
<head>
	<title>遠端多媒體看板</title>
	<script src='../tool/jquery-1.12.4.js'></script>
	<style>
		.view {
			display: flex;
			flex-wrap: wrap; /* 換行 */
			font-size: 50px;
			font-weight: bold;
			font-family: Consolas, Microsoft JhengHei, sans-serif;
		}

		.view div {
			/* flex: 0 0 50%; 每行最多顯示兩次，所以佔據 50% 的寬度 */
			box-sizing: border-box; /* 確保 padding 和 border 不會增加元素的寬度 */
			padding: 10px;
		}
	</style>
	<script>
		$(document).ready(function(){
			setInterval(function(){
				$.ajax({
					url:'./viewlist.ajax.php',
					async:false,
					dataType:'html',
					success:function(d){
						console.log(d);
						$('.view').html(d);
					},
					error:function(e){
						console.log(e);
					}
				});
			},1000);
		});
	</script>
</head>
<body style='background-color:#000000;color:#ffffff;'>
	<div class='view'>
	</div>
</body>
</html>
