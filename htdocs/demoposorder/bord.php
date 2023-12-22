<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>EDM</title>
	<script src='../tool/jquery-1.12.4.js'></script>
	<script src='../tool/jquery.cycle2.js'></script>
	<script>
		$(document).ready(function(){
			$(document).click(function(){
				location.href='./';
			});
		});
	</script>
</head>
<body style='margin:0;padding:0;position:relative;'>
	<div class='cycle-slideshow content' data-cycle-fx='scrollHorz' data-cycle-timeout='3000' data-cycle-speed='800' data-cycle-slides="> img">
		<img src='01.png' style='width:1366px;height:768px;'>
		<img src='02.png' style='width:1366px;height:768px;'>
		<img src='03.png' style='width:1366px;height:768px;'>
		<img src='04.png' style='width:1366px;height:768px;'>
		<img src='05.png' style='width:1366px;height:768px;'>
	</div>
	<div style='width:280px;height:60px;padding-top:5px;text-align:center;background-color:#333333;color:#ffffff;font-size:40px;position:absolute;right:0;bottom:0;z-index:100;'>
		請碰觸螢幕
	</div>
</body>
</html>
