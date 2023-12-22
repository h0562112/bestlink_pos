<title>遠端多媒體看板</title>
<?php
$content=parse_ini_file('./data/content.ini',true);
if($content['bord']['type']=='Horizontal9'){
}
else{
	$temp=preg_split('/(Horizontal)/',$content['bord']['type']);
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
					height: '100%',
					width: '100%',
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
							player1.setVolume(".$content['div1']['maxvolum'].");";
					if(isset($content['div1']['randplay'])&&$content['div1']['randplay']=='1'){
						echo "setTimeout( function() { 
								event.target.setShuffle(true); 
								event.target.playVideoAt(0);
								event.target.playVideo();
							}, 1000);";
					}
					else{
						echo "event.target.playVideo();";
					}
				echo "},
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
					height: '100%',
					width: '100%',
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
							player2.setVolume(".$content['div2']['maxvolum'].");";
					if(isset($content['div2']['randplay'])&&$content['div2']['randplay']=='1'){
						echo "setTimeout( function() { 
								event.target.setShuffle(true); 
								event.target.playVideoAt(0);
								event.target.playVideo();
							}, 1000);";
					}
					else{
						echo "event.target.playVideo();";
					}
				echo "},
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

			setInterval(function(){ $('.fblike').attr('src',$('.fblike').attr('src')) }, 5000);

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

						if($('#frame').attr('src').match('call=')){
							$('#frame').attr('src', $('#frame').attr('src'));
							console.log('1');
						}
						else{
							$('#frame').attr('src', $('#frame').attr('src')+'call=');
							console.log('2');
						}

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
				$(document).keypress(function(event){
					console.log(event.which);
					if((event.which-48)==0){
						string1='';
						if(string2.length==0){
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'0';
						}
						else{
							string2=string2+'0';
						}
						//console.log('0');
					}
					if((event.which-48)==1){
						string1='';
						if(parseInt(string2)==0){
							string2='1';
						}
						else if(string2.length<3){
							string2=string2+'1';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'1';
						}
						//console.log('1');
					}
					if((event.which-48)==2){
						string1='';
						if(parseInt(string2)==0){
							string2='2';
						}
						else if(string2.length<3){
							string2=string2+'2';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'2';
						}
						//console.log('2');
					}
					if((event.which-48)==3){
						string1='';
						if(parseInt(string2)==0){
							string2='3';
						}
						else if(string2.length<3){
							string2=string2+'3';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'3';
						}
						//console.log('3');
					}
					if((event.which-48)==4){
						string1='';
						if(parseInt(string2)==0){
							string2='4';
						}
						else if(string2.length<3){
							string2=string2+'4';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'4';
						}
						//console.log('4');
					}
					if((event.which-48)==5){
						string1='';
						if(parseInt(string2)==0){
							string2='5';
						}
						else if(string2.length<3){
							string2=string2+'5';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'5';
						}
						//console.log('5');
					}
					if((event.which-48)==6){
						string1='';
						if(parseInt(string2)==0){
							string2='6';
						}
						else if(string2.length<3){
							string2=string2+'6';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'6';
						}
						//console.log('6');
					}
					if((event.which-48)==7){
						string1='';
						if(parseInt(string2)==0){
							string2='7';
						}
						else if(string2.length<3){
							string2=string2+'7';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'7';
						}
						//console.log('7');
					}
					if((event.which-48)==8){
						string1='';
						if(parseInt(string2)==0){
							string2='8';
						}
						else if(string2.length<3){
							string2=string2+'8';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'8';
						}
						//console.log('8');
					}
					if((event.which-48)==9){
						string1='';
						if(parseInt(string2)==0){
							string2='9';
						}
						else if(string2.length<3){
							string2=string2+'9';
						}
						else if(string2.length==3){
							string2=string2.substr(1,2)+'9';
						}
						//console.log('9');
					}
					if(event.which==42){
						string1='';
						if(string2.length<=1){
							string2='0';
						}
						else{
							string2=string2.substr(0,(string2.length-1));
						}
						console.log('backspace');
					}
					if(event.which==43){
						string1='';
						if(parseInt(string2)==999){
							string2='999';
						}
						else{
							string2=(parseInt(string2)+1).toString();
						}
						//console.log(string2);
						console.log('+');
					}
					if(event.which==13){
						if(string2.length>0&&string2!='0'&&string2!='NaN'){
							console.log('submit');
							$.ajax({
								method:'GET',
								url:'method.php',
								data:{sendvar:string2},
								success:function(data){
									channel.push('newnumber');
								}
							});
						}		
					}
					if(event.which==45){
						string1='';
						if(parseInt(string2)==0){
							string2='0';
						}
						else{
							string2=(parseInt(string2)-1).toString();
						}
						//console.log(string2);
						console.log('-');
					}
					if(event.which==47){
						string1='0';
						string2='0';
						console.log('ac');
					}
					console.log(string2);
				});
			});
		});
	</script>";
if($content['div1']['type']==1){
	echo "<div class='cycle-slideshow dyphoto' data-cycle-fx='scrollHorz' data-cycle-timeout='".$content['div1']['timeout']."' style='width:1245px;height:700px;padding:0;margin:0;float:left;'> ";
	for($i=1;$i<=$content['div1']['number'];$i++){
		if($i==1){
			echo "<img src='img/".$content['bord']['type']."/".$content['div1']['image'.$i]."' style='width:100%;height:100%;'>";
		}
		else{
			echo "<img src='img/".$content['bord']['type']."/".$content['div1']['image'.$i]."' style='width:100%;height:100%;'>";
		}
	}
	echo "</div>";
}
else{
	echo "<div style='width:1245px;height:700px;padding:0;margin:0;float:left;'>";
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
	echo "<div class='cycle-slideshow dyphoto' data-cycle-fx='scrollHorz' data-cycle-timeout='".$content['div2']['timeout']."' style='width:675px;height:1080px;padding:0;margin:0;float:right;'> ";
	for($i=1;$i<=$content['div2']['number'];$i++){
		if($i==1){
			echo "<img src='img/".$content['bord']['type']."/".$content['div2']['image'.$i]."' style='width:100%;height:100%;'>";
		}
		else{
			echo "<img src='img/".$content['bord']['type']."/".$content['div2']['image'.$i]."' style='width:100%;height:100%;'>";
		}
	}
	echo "</div>";
}
else{
	echo "<div style='width:470px;height:1080px;padding:0;margin:0;'>";
	echo "<div id='player2' style='float:left;'></div>";
	//echo "<iframe src='' style='width:1280px;height:540px;float:left;padding:0;margin:0;border:0;'></iframe>";
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
echo "<div style='width:300px;height:380px;float:left;background-image:url(\"img/".$content['bord']['type']."/".$content['logo']['image']."\");'>
</div>";
echo "<div style='width:500px;height:380px;float:left;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['callnumber']['image']."?".date('His')."\");'>
<iframe id='frame' src='callnum.php?' frameborder='0' style='width:430px;height:300px;margin:40px 35px;'></iframe>
</div>";
echo "<div style='width:445px;height:270px;float:right;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['fbid']['image']."\");float:left;'>
		<iframe frameborder='0' src='fbqrcode.php?id=".$content['fbid']['id']."' scrolling='no' style='width:250px;height:250px;margin:74px 0 0 46px;float:left;'></iframe>
		<iframe frameborder='0' src='getname.php?id=".$content['fbid']['id']."' style='width:150px;height:40px;margin:-254px 0 0 260px;float:left;'></iframe>
		<iframe class='fblike' src='getlike.php?id=".$content['fbid']['id']."' frameborder='0' style='height:95px;margin:-150px 60px 0 0;float:right;'></iframe>
	</div>";
echo "<div style='width:445px;height:110px;float:right;overflow:hidden;float:left;'>
		<!-- <img src='../gettarget.jpg' width='445px' height='110px' style='border:0;margin:0;float:left;'> -->
		<img src='img/".$content['bord']['type']."/data.png' width='100%' height='100%' style='border:0;margin:0;float:left;background-size:100% 100%;'>
	</div>";
echo "<div class='marquee'  style='width:675px;height:75px;position:fixed;right:0;bottom:0;overflow:hidden;float:left;background-image:url(\"img/".$content['bord']['type']."/".$content['marquee']['image']."?".date('His')."\");background-size:100% 100%;'>
		<!-- <div style='width:100%;height:100%;z-index:5;'></div> -->
		<marquee scrollamount='";echo ($content['marquee']['speed']/274)*1185;echo "' style='font:40px Microsoft JhengHei;line-height:75px;position: absolute;right:0;bottom:0;'><font color='".$content['marquee']['color']."'>".$content['marquee']['text']."</font></marquee>
	</div>";
?>