<?php
$initsetting=parse_ini_file('../database/initsetting.ini',true);
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
	<script src="../tool/jquery.mobile-1.4.5/demos/js/jquery.js"></script>
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
		//setInterval(function(){ $('.orderlist').attr('src',$('.orderlist').attr('src')) }, <?php if(isset($secview['rightlist']['timeout']))echo ($secview['rightlist']['timeout']*1000);else echo '1000'; ?>);
		<?php
			if(isset($initsetting['init']['usenodejs'])&&$initsetting['init']['usenodejs']=='1'&&file_exists('../nodejs/node_modules/socket.io-client/dist/socket.io.js')){//0>>遵循舊有流程1>>套用nodejs流程
			}
			else{
		?>
			setInterval(function(){ $('.orderlist').attr('src',$('.orderlist').attr('src')) }, <?php if(isset($secview['rightlist']['timeout']))echo ($secview['rightlist']['timeout']*1000);else echo '1000'; ?>);
		<?php
			}
		?>
	});
	</script>
</head>
<body>
	<!-- <div id='top' style='width:70%;height:103px;font-size:40px;line-height:60px;float:left;background-color:transparent;'>
		<div style='width:100%;float:left;height:100%;line-height:103px;background-color:transparent;'>
			<?php
			if(file_exists('./img/logo.png')){
				echo '<img src="./img/logo.png">';
			}
			else{
				echo $secview['basic']['story'];
			}
			?>
		</div>
	</div> -->
	<div id='right' style='width:30%;height:100%;float:right;'>
		<div id='orderlist' style='width:calc(100% - 2px);height:calc(100% - 4px);margin:2px 0 2px 2px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<iframe class='orderlist' src='./orderlist.php?machine=<?php if(isset($_GET['machine'])&&$_GET['machine']!='')echo $_GET['machine'];else echo 'm1'; ?>' frameborder='0' style='width:100%;height:100%;'></iframe>
		</div>
	</div>
	<div id='left' style='width:70%;height:100%;float:left;'>
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
			$filelist=scandir('./img/imglist');
			$index=1;
			foreach($filelist as $v){
				if(!in_array($v,array(".","..","Thumbs.db"))){
					if (is_dir('./img/imglist' . DIRECTORY_SEPARATOR . $v)){
						continue;
					}
					else{
						echo "<img style='width:100%;height:100%;' src='./img/imglist/".$v."?".date('YmdHis')."'>";
						$index++;
					}
				}
				else{
				}
			}
			?>
			</div>
		<?php
		}
		?>
	</div>
	<!-- <div id='bottom' style='width:100%;height:103px;float:left;background-color:transparent;background-image:url("./img/bk6.png");background-size:100% 100%;'>
		<marquee scrollamount='<?php echo ($secview['marquee']['speed']/274)*1080; ?>' style='font-size:40px;line-height:103px;'><font color='<?php echo $secview['marquee']['color']; ?>'><?php echo $secview['marquee']['text']; ?></font></marquee>
	</div> -->
</body>
</html>
