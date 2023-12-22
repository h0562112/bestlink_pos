<title>遠端多媒體看板</title>
<?php
$content=parse_ini_file('./data/content.ini',true);
if($content['bord']['type']=='Horizontal16'){
}
else{
	$temp=preg_split('/(Horizontal)/',$content['bord']['type']);
	echo "<script>
			location.href='index".strtolower(substr($content['bord']['type'],0,1)).$temp[1].".php';
		</script>";
}
echo "<script type='text/javascript' src='tran.js'></script>";
echo "<script src='./lib/jquery-1.12.4.js'></script>";
echo "<script src='./lib/ajax/libs/jquery/1/jquery.min.js'></script>";
echo "<script src='./lib/jquery.cycle2.js'></script>";

echo "<link rel='stylesheet' type='text/css' href='tran.css'>";
echo "<style>
		body {
			font-family: Arial,Microsoft JhengHei,sans-serif;
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

			setInterval(function(){ $('.fblike').attr('src',$('.fblike').attr('src')) }, 5000);
			$(document).on('focusout','#callnumber',function(){
				console.log('focus');
				$('#callnumber').focus();
			});
			$(document).on('keypress','#callnumber',function(event){
				//$('#callnumber').val(pad($('#callnumber').val(),3));
				
				if(event.which==42){
					string1='';
					if($('#callnumber').val().length<=1||parseInt($('#callnumber').val())==0){
						$('#callnumber').val('000');
					}
					else{
						$('#callnumber').val(pad($('#callnumber').val(),3));
						$('#callnumber').val('0'+$('#callnumber').val().substr(0,($('#callnumber').val().length-1)));
					}
					//console.log('backspace');
				}
				if(event.which==43){
					$.ajax({
						url:'./addnumber.php',
						dataType:'html',
						success:function(d){
							$('#callnumber').val(d);
						},
						error:function(e){
							console.log(e);
						}
					});
					/*string1='';
					if(parseInt($('#callnumber').val())==999){
						$('#callnumber').val('001');
					}
					else{
						$('#callnumber').val(pad(parseInt($('#callnumber').val())+1,3));
					}*/
					console.log($('#callnumber').val());
					//console.log('+');
				}
				if(event.which==13){
					if($('#callnumber').val().length>0&&parseInt($('#callnumber').val())!='0'&&parseInt($('#callnumber').val())!='NaN'){
						//console.log($('#callnumber').val());
						$.ajax({
							url:'method.php',
							method:'get',
							data:{'sendvar':$('#callnumber').val()},
							success:function(){
								$.ajax({
									url:'log.php',
									method:'post',
									data:{'type':'success','number':$('#callnumber').val()},
									dataType:'html',
									async: false,
									success:function(d){
										//channel.push('newnumber');
										if(typeof player1 != 'undefined'){
											player1.setVolume(10);
										}
										//document.getElementById('frame').contentWindow.location.reload();
										if($('#frame').attr('src').match('call=')){
											$('#frame').attr('src', $('#frame').attr('src'));
											//console.log('1');
										}
										else{
											$('#frame').attr('src', $('#frame').attr('src')+'call=');
											//console.log('2');
										}
										if(typeof player1 != 'undefined'){
											setTimeout(function (){player1.setVolume(".$content['div1']['maxvolum'].")}, 5000);
										}
										//console.log(d);
										$.ajax({
											url:'http://www.quickcode.com.tw/bord/".$content['initial']['company']."/".$content['initial']['story']."/upnumber.php',
											data:{'num':d},
											success:function(){
											},
											error:function(e){
												console.log(e);
											}
										});
									},
									error:function(){
									}
								});
								$('#callnumber').val('');
							},
							error:function(){
								$.ajax({
									url:'log.php',
									method:'post',
									data:{'type':'error','number':$('#callnumber').val().substr(0,3)},
									success:function(){
									},
									error:function(){
									}
								});
							}
						});
					}
				}
				if(event.which==45){
					$.ajax({
						url:'./diffnumber.php',
						dataType:'html',
						success:function(d){
							$('#callnumber').val(d);
						},
						error:function(e){
							console.log(e);
						}
					});
					/*string1='';
					if(parseInt($('#callnumber').val())==0){
						$('#callnumber').val('000');
					}
					else{
						$('#callnumber').val(pad(parseInt($('#callnumber').val())-1,3));
					}*/
					//console.log($('#callnumber').val());
					//console.log('-');
				}
				if(event.which==47){
					string1='0';
					$('#callnumber').val('000');
					//console.log('ac');
				}
				//console.log($('#callnumber').val());
			});
			function pad (str, max) {
				str = str.toString();
				return str.length < max ? pad('0' + str, max) : str;
			}
			$.getScript('http://ezoapp.github.io/sailsx2/assets/modelPush.js',function () {
				var channel; // refer to https://github.com/ezoapp/sailsx2
				channel = new MyChannel('".$content['channel']['name']."',function (data) {
					if(data=='".$content['initial']['company']."_".$content['initial']['story']."_reload'){
						console.log('reload');
						location.reload();
					}
					else{
						console.log('success');
					}
				});
			});
		});
	</script>";
if($content['div1']['type']==1){
	echo "<div class='cycle-slideshow dyphoto' data-cycle-fx='scrollHorz' data-cycle-timeout='".$content['div1']['timeout']."' style='width:1920px;height:1080px;padding:0;margin:0;float:left;'> ";
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
	echo "<div style='width:1920px;height:1080px;padding:0;margin:0;float:left;'>";
	echo "<div id='player1' style='float:left;'></div>";
	echo "</div>";
}
echo "<div style='width:600px;height:350px;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['callnumber']['image']."?".date('His')."\");background-size:100% 100%;border-bottom:9px solid #ffffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;position: fixed;top: 0;right: 20px;'>
<iframe id='frame' src='callnum.php?' frameborder='0' style='width:100%;height:100%;margin:-25px 0 0 -5px;'></iframe>
</div>";
echo "<div style='width:409px;height:180px;background-color:#000000;background-image:url(\"img/".$content['bord']['type']."/".$content['logo']['image']."\");background-position: center center;background-repeat: no-repeat;position: fixed;bottom: 0;left: 0;'>
</div>";
echo "<div class='marquee'  style='width:1511px;height:180px;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['marquee']['image']."?".date('His')."\");background-size:100% 100%;position: fixed;bottom: 0;right: 0;'>
		<marquee scrollamount='";echo ($content['marquee']['speed']/274)*1185;echo "' style='font:110px Microsoft JhengHei;line-height:180px;'><font color='".$content['marquee']['color']."'>".$content['marquee']['text']."</font></marquee>
	</div>";
echo "<input type='text' id='callnumber' style='position: fixed;bottom: -100px;right: -100px;' autofocus>";
?>