<title>遠端多媒體看板</title>
<?php
$content=parse_ini_file('./data/content.ini',true);
if($content['bord']['type']=='Straight3'){
}
else{
	$temp=preg_split('/(Straight)/',$content['bord']['type']);
	echo "<script>
			location.href='index".strtolower(substr($content['bord']['type'],0,1)).$temp[1].".php';
		</script>";
}
echo "<script type='text/javascript' src='tran.js'></script>";
echo "<script src='https://code.jquery.com/jquery-1.12.4.js'></script>";
echo "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>";
echo "<script src='http://malsup.github.com/jquery.cycle2.js'></script>";

echo "<link rel='stylesheet' type='text/css' href='tran.css'>";
echo "<style>
		body {
			background-image:url('img/".$content['bord']['type']."/".$content['background']['image']."');
		}
	</style>";
echo "<script>
		$.extend($.support, { touch: 'ontouchend' in document });
		var tag = document.createElement('script');

		tag.src = 'https://www.youtube.com/iframe_api';
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		// 3. This function creates an <iframe> (and YouTube player)
		//    after the API code downloads.\n";
		echo "var player1;
			var player2;
			function onYouTubeIframeAPIReady() {";
		if($content['div1']['type']==1){
		}
		else{
			echo "player1 = new YT.Player('player1', {
					height: '580',
					width: '1080',
					videoId: '";if(strlen($content['div1']['beginvideo'])!=0)echo $content['div1']['beginvideo'];echo "',";
					if(strlen($content['div1']['videolist'])!=0){
						echo "playerVars: {
								loop:1,
								listType:'playlist',
								list: '".$content['div1']['videolist']."'
							},";
					}
				echo "events: {
						'onReady': function (event){
							player1.setVolume(".$content['div1']['maxvolum'].");
							event.target.playVideo();
						},
						'onStateChange': function(event){
							if(event.data==0){
								event.target.seekTo(0);
							}
						}
					}
				});";
		}
		if($content['div2']['type']==1){
		}
		else{
			echo "player2 = new YT.Player('player2', {
					height: '580',
					width: '1080',
					videoId: '";if(strlen($content['div2']['beginvideo'])!=0)echo $content['div2']['beginvideo'];echo "',";
					if(strlen($content['div2']['videolist'])!=0){
						echo "playerVars: {
								loop:1,
								listType:'playlist',
								list: '".$content['div2']['videolist']."'
							},";
					}
				echo "events: {
						'onReady': function (event){
							player2.setVolume(".$content['div2']['maxvolum'].");
							event.target.playVideo();
						},
						'onStateChange': function(event){
							if(event.data==0){
								event.target.seekTo(0);
							}
						}
					}
				});";
		}
		echo "}";
		
	echo "// 4. The API will call this function when the video player is ready.
		function onPlayerReady2(event) {
			
		}

		// 5. The API calls this function when the player's state changes.
		//    The function indicates that when playing a video (state=1),
		//    the player should play for six seconds and then stop.
		/*var done = false;
		function onPlayerStateChange(event) {
			if (event.data == YT.PlayerState.PLAYING && !done) {
				setTimeout(stopVideo, 6000);
				done = true;
			}
		}
		function stopVideo() {
			player.stopVideo();
		}*/

		$(document).ready(function(){
			var string1='';
			var string2='';

			$.getScript('http://ezoapp.github.io/sailsx2/assets/modelPush.js',function () {
				var channel; // refer to https://github.com/ezoapp/sailsx2
				channel = new MyChannel('".$content['channel']['name']."',function (data) {
					if(data=='".$content['initial']['company']."_".$content['initial']['story']."_reload'){
						console.log('reload');
						location.reload();
					}
					else if(data=='newnumber'){
						if(typeof player1 != 'undefined'){
							player1.setVolume(10);
						}
						if(typeof player2 != 'undefined'){
							player2.setVolume(10);
						}
						document.getElementById('frame').contentWindow.location.reload();
						if(typeof player1 != 'undefined'){
							setTimeout(function (){player1.setVolume(100)}, 5000);
						}
						if(typeof player2 != 'undefined'){
							setTimeout(function (){player2.setVolume(".$content['div2']['maxvolum'].")}, 5000);
						}
					}
					else{
						console.log('success');
					}
				});
			});
		});
	</script>";
echo "<div style='width:430px;height:380px;float:left;background-image:url(\"img/".$content['bord']['type']."/".$content['logo']['image']."\");'>
</div>";
echo "<div style='width:650px;height:380px;float:right;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['callnumber']['image']."\");'>
</div>";
if($content['div1']['type']==1){
	echo "<div class='cycle-slideshow dyphoto' data-cycle-fx='scrollHorz' data-cycle-timeout='".$content['div1']['timeout']."' style='background-image:url(\"img/".$content['bord']['type']."/".$content['div1']['backimage']."\");width:1081px;height:580px;padding:0;margin:0;float:left;'> ";
	for($i=1;$i<=$content['div1']['number'];$i++){
		if($i==1){
			echo "<img src='img/".$content['bord']['type']."/".$content['div1']['image'.$i]."'>";
		}
		else{
			echo "<img src='img/".$content['bord']['type']."/".$content['div1']['image'.$i]."'>";
		}
	}
	echo "</div>";
}
else{
	echo "<div style='background-image:url(\"img/".$content['bord']['type']."/".$content['div1']['backimage']."\");width:1080px;height:580px;padding:0;margin:0;float:left;'>";
	echo "<div id='player1' style='float:left;'></div>";
	/*if(strlen($content['div1']['videolist'])!=0){
		echo '<div style="width:1080px;height:580px;padding:0;margin:0;">
				<iframe width="1080" height="580" src="https://www.youtube.com/embed/'.$content['div1']['beginvideo'].'?&autoplay=1&loop=1&playlist='.$content['div1']['beginvideo'].'" frameborder="0" allowfullscreen></iframe>
			</div>';
	}
	else{
		echo '<div style="width:1080px;height:580px;padding:0;margin:0;">
				<iframe width="1080" height="580" src="https://www.youtube.com/embed/videoseries?&autoplay=1&loop=1&list='.$content['div1']['videolist'].'" frameborder="0" allowfullscreen></iframe>
			</div>';
	}*/
	echo "</div>";
}
if($content['div2']['type']==1){
	echo "<div class='cycle-slideshow dyphoto' data-cycle-fx='scrollHorz' data-cycle-timeout='".$content['div2']['timeout']."' style='background-image:url(\"img/".$content['bord']['type']."/".$content['div2']['backimage']."\");width:1081px;height:580px;padding:0;margin:0;float:left;'> ";
	for($i=1;$i<=$content['div2']['number'];$i++){
		if($i==1){
			echo "<img src='img/".$content['bord']['type']."/".$content['div2']['image'.$i]."'>";
		}
		else{
			echo "<img src='img/".$content['bord']['type']."/".$content['div2']['image'.$i]."'>";
		}
	}
	echo "</div>";
}
else{
	echo "<div style='background-image:url(\"img/".$content['bord']['type']."/".$content['div2']['backimage']."\");width:1080px;height:580px;padding:0;margin:0;'>";
	echo "<div id='player2' style='float:left;'></div>";
	/*if(strlen($content['div2']['beginvideo'])!=0){
		echo '<div style="width:1080px;height:580px;padding:0;margin:0;">
				<iframe width="1080" height="580" src="https://www.youtube.com/embed/'.$content['div2']['beginvideo'].'?&autoplay=1&loop=1&playlist='.$content['div2']['beginvideo'].'" frameborder="0" allowfullscreen></iframe>
			</div>';
	}
	else{
		echo '<div style="width:1080px;height:580px;padding:0;margin:0;">
				<iframe width="1080" height="580" src="https://www.youtube.com/embed/videoseries?&autoplay=1&loop=1&list='.$content['div2']['videolist'].'" frameborder="0" allowfullscreen></iframe>
			</div>';
	}*/
	echo "</div>";
}
echo "<div style='width:620px;height:190px;float:right;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['fbid']['image']."\");float:left;'>
		<iframe frameborder='0' src='fbqrcode.php?id=".$content['fbid']['id']."' style='width:136px;height:136px;margin:29px 0 0 59px;float:left;'></iframe>
		<iframe frameborder='0' src='getname.php?id=".$content['fbid']['id']."' style='width:300px;height:40px;margin:24px 0 0 57px;float:left;'></iframe>
		<iframe src='getlike.php?id=".$content['fbid']['id']."' frameborder='0' style='height:95px;margin:10px 90px 0 0;float:right;'></iframe>
	</div>";
echo "<div style='width:460px;height:190px;float:right;overflow:hidden;float:left;'>
		<img src='img/".$content['bord']['type']."/data.png'>
	</div>";
echo "<div class='marquee'  style='width:1080px;height:190px;float:right;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['marquee']['image']."\");float:left;'>
		<marquee scrollamount='";echo ($content['marquee']['speed']/274)*1080;echo "' style='font:90px/90px Microsoft JhengHei;margin:50px 0 0 0;'><font color='".$content['marquee']['color']."'>".$content['marquee']['text']."</font></marquee>
	</div>";
?>