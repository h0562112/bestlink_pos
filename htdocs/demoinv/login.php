<html>
	<head>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<meta charset="utf-8">
		<script src='https://code.jquery.com/jquery-1.12.4.js'></script>
		<script src="https://blockly.webduino.io/lib/runtime.min.js"></script>
		<script src="https://blockly.webduino.io/webduino-blockly.js"></script>
		<script src="https://webduino.io/components/webduino-js/dist/webduino-all.min.js"></script>
		<script src="https://blockly.webduino.io/lib/firebase.js"></script>
		<title>開立電子發票系統</title>
	</head>
	<body>
		<h1>請將員工卡放入卡匣</h1>

		<script type="text/javascript">
		var rfid;

		boardReady({device: 'Ylg6'}, function (board) {
		  board.systemReset();
		  board.samplingInterval = 250;
		  rfid = getRFID(board);
		  rfid.read();
		  rfid.on("enter",function(_uid){
			location.href='../order/id='+_uid;
			/*$.ajax({
				method:'GET',
				url:'./loginmethod.php',
				data:{id:_uid},
				success:function(msg){
					location.href='../order/';
				}
			});*/
		  });
		  rfid.on("leave",function(_uid){
		  });
		});

		</script>
	</body>
</html>
