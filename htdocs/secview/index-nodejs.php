<?php
$content=parse_ini_file('../database/machinedata.ini',true);
$secview=parse_ini_file('./img/secview.ini',true);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>遠端多媒體看板</title>
	<style>
	body {
		width:100vw;
		height:100vh;
		margin:0;
		padding:20px;
		overflow:hidden;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		background-color:<?php if(isset($secview['background']['color']))echo $secview['background']['color'];else echo '#231815'; ?>;
		color:#ffffff;
	}
	body,div,label,span {
		font-family: Consolas,Microsoft JhengHei,sans-serif;
	}
	div {
		margin:0;
		padding:0;
		border:0;
	}
	</style>
	<script src='../tool/jquery-1.12.4.js'></script>
	<script src='../tool/jquery.cycle2.js'></script>
	<script src='../nodejs/node_modules/socket.io-client/dist/socket.io.js'></script>

	<script>
	$.extend($.support, { touch: 'ontouchend' in document });
	var tag = document.createElement('script');
	tag.src = 'https://www.youtube.com/iframe_api';
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	<?php
	if(!isset($secview['leftimg']['type'])||$secview['leftimg']['type']==1){
	}
	else{
	?>
		var player1;
		function onYouTubeIframeAPIReady() {
			player1 = new YT.Player('player1', {
				height: '100%',
				width: '100%',
				videoId: "<?php if(strlen($secview['leftimg']['beginvideo'])!=0)echo $secview['leftimg']['beginvideo']; ?>",
			<?php
			if(strlen($secview['leftimg']['videolist'])!=0){
			?>
				playerVars: {
					loop:1,
					listType:'playlist',
					list: "<?php echo $secview['leftimg']['videolist']; ?>"
				},
			<?php
			}
			?>
				events: {
					'onReady': function (event){
						player1.setVolume(<?php echo $secview['leftimg']['maxvolum']; ?>);
						event.target.playVideo();
					},
					'onStateChange': function(event){
						if(event.data==0){
							event.target.seekTo(0);
						}
					}
				}
			});
		}
		function onPlayerReady2(event) {
			
		}
	<?php
	}
	?>
	$(document).ready(function(){
		setInterval(function(){
			$.ajax({
				url:'./check.qrcode.php',
				method:'post',
				async:false,
				cache:false,
				data:{'machine':"<?php if(isset($_GET['machine'])&&$_GET['machine']!='')echo $_GET['machine'];else echo 'm1'; ?>"},
				dataType:'html',
				success:function(d){
					//console.log(d);
					if(d=='exists'){
						$('#qrcode #qrimg').prop('src',"../print/intellaqrcode/<?php if(isset($_GET['machine'])&&$_GET['machine']!='')echo $_GET['machine'];else echo 'm1'; ?>.png");
						//$('#left').css({'display':'none'});
						$('#qrcode').css({'display':'table'});
					}
					else{
						$('#qrcode').css({'display':'none'});
						//$('#left').css({'display':'block'});
						$('#qrcode #qrimg').prop('src',"");
					}
				},
				error:function(e){
					console.log(e);
				}
			});
			$.ajax({
				url:'./check.pwstring.php',
				method:'post',
				async:false,
				cache:false,
				data:{'machine':"<?php if(isset($_GET['machine'])&&$_GET['machine']!='')echo $_GET['machine'];else echo 'm1'; ?>"},
				dataType:'html',
				success:function(d){
					//console.log(d);
					if(d!='notexitst'){
						$('#mempaypw input[name="pwstring"]').val(d);
						//$('#left').css({'display':'none'});
						$('#mempaypw').css({'display':'table'});
					}
					else{
						$('#mempaypw').css({'display':'none'});
						//$('#left').css({'display':'block'});
						$('#mempaypw input[name="pwstring"]').val('');
					}
				},
				error:function(e){
					console.log(e);
				}
			});
			if($('#mempaypw').css('display')=='none'&&$('#qrcode').css('display')=='none'){
				$('#left').css({'display':'block'});
			}
			else{
				$('#left').css({'display':'none'});
			}
		},1000);
		//setInterval(function(){ $('.orderlist').attr('src',$('.orderlist').attr('src')) }, <?php if(isset($secview['rightlist']['timeout']))echo ($secview['rightlist']['timeout']*1000);else echo '1000'; ?>);
		//var io = require(['socket.io'])(http);
		var mydata={'name':'secview<?php echo date("His"); ?>'};
		var socket = io.connect('http://www.quickcode.com.tw:3700');
		//console.log(mydata['name']);
		socket.emit('join',mydata['name']);
		socket.on('joinsuccess',function(msg){
			//mydata['id']=id;
			console.log(msg);
		});
		socket.emit('message', 'i am secview.');
		socket.on('message',function(msg){
			console.log(msg);
		});
	});
	</script>
</head>
<body>
	<div id='top' style='width:70%;height:103px;font-size:40px;line-height:60px;float:left;background-color:transparent;'>
		<div style='width:100%;float:left;height:100%;line-height:103px;background-color:transparent;'>
			<?php
			if(file_exists('./img/logo.png')){
				echo '<img src="./img/logo.png" style="width:100%;height:100%;">';
			}
			else{
				echo $content['basic']['story'];
			}
			?>
		</div>
	</div>
	<div id='right' style='width:30%;height:calc(100% - 103px);float:right;'>
		<div id='orderlist' style='width:calc(100% - 2px);height:calc(100% - 4px);margin:2px 0 2px 2px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<iframe class='orderlist' src='' frameborder='0' style='width:100%;height:100%;'></iframe>
		</div>
	</div>
	<div id='left' style='width:70%;height:calc(100% - 206px);float:left;position:relative;'>
		<?php
		if(isset($secview['leftimg']['type'])&&$secview['leftimg']['type']=='2'){
		?>
			<div style='width:100%;height:100%;padding:0;margin:0;float:left;'>
				<div id='player1' style='float:left;'></div>
			</div>
		<?php
		}
		else{
		?>
			<div class='cycle-slideshow dyphoto' data-cycle-fx='scrollHorz' data-cycle-timeout='<?php echo floatval($secview['leftimg']['imgspeed'])*1000; ?>' style='background-image:url("");width:100%;height:100%;padding:0;margin:0;float:left;'>
			<?php
			if(isset($secview['leftimg']['imgnum'])){
				for($i=1;$i<=$secview['leftimg']['imgnum'];$i++){
					echo "<img style='width:100%;height:100%;object-fit:contain;display: block;' src='./img/imglist/".$i.".png'>";
				}
			}
			else{
				$filelist=scandir('./img/imglist');
				foreach($filelist as $v){
					if(!in_array($v,array(".","..","Thumbs.db"))){
						if (is_dir('./img/imglist' . DIRECTORY_SEPARATOR . $v)){
							continue;
						}
						else{
							echo "<img style='width:100%;height:100%;object-fit:contain;display: block;' src='./img/imglist/".$v."'>";
						}
					}
					else{
					}
				}
			}
			?>
			</div>
		<?php
		}
		?>
	</div>
	<div id='qrcode' style='width:70%;height:calc(100% - 206px);float:left;position:relative;display:none;'>
		<div style='display: table-cell; vertical-align: middle; text-align: center;'>
			<img id='qrimg' src="">
		</div>
	</div>
	<div id='mempaypw' style='width:70%;height:calc(100% - 206px);float:left;position:relative;display:none;'>
		<div style='display: table-cell; vertical-align: middle; text-align: center;font-size:50px;'>
			<label>會員交易密碼</label><input type='text' name='pwstring' style='font-size:50px;' readonly>
		</div>
	</div>
	<div id='bottom' style='width:70%;height:103px;float:left;background-color:transparent;background-image:url("./img/bk6.png");background-size:100% 100%;'>
		<marquee scrollamount='<?php echo ($secview['marquee']['speed']/274)*1080; ?>' style='font-size:40px;line-height:103px;'><font color='<?php echo $secview['marquee']['color']; ?>'><?php echo $secview['marquee']['text']; ?></font></marquee>
	</div>
	<div id='bottom' style='width:30%;height:103px;float:left;background-color:transparent;'>
		<img src="img/table.png" width="100%" height="100%" border="0" alt=""></img>
	</div>
</body>
</html>
