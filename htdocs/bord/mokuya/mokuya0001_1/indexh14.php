<title><?php if(isset($_GET['title']))echo $_GET['title'];else echo '遠端多媒體看板'; ?></title>
<?php
$content=parse_ini_file('./data/content.ini',true);
if($content['bord']['type']=='Horizontal14'){
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
			background-image:url('img/".$content['bord']['type']."/".$content['background']['image']."?".date('His')."');
		}
		#marquee {
			font:35px/35px Microsoft JhengHei;
		}
		@media screen and (min-device-width:1030px) and (min-device-height:770px),screen and (min-device-height:1030px) and (min-device-width:770px) {
			#marquee {
				font:50px/50px Microsoft JhengHei;
			}
		}
		@media screen and (min-device-width:1370px) and (min-device-height:1070px),screen and (min-device-height:1370px) and (min-device-width:1070px) {
			#marquee {
				font:85px/85px Microsoft JhengHei;
			}
		}
		.marquee:before {
			content:'';
			background-image:url(\"img/".$content['bord']['type']."/".$content['marquee']['image']."?".date('His')."\");
			opacity: 0.5;
			top: 0;
			left: 0;
			bottom: 0;
			right: 0;
			position: absolute;
			z-index: -1; 
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
			//var player2;
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
		/*if($content['div2']['type']==1){
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
		}*/
		echo "}";
		
	echo "// 4. The API will call this function when the video player is ready.
		function onPlayerReady2(event) {
			
		}

		// 5. The API calls this function when the player's state changes.
		//    The function indicates that when playing a video (state=1),
		//    the player should play for six seconds and then stop.

		$(document).ready(function(){
			var string1='';
			var string2='';
			$('iframe').click(function(){
				console.log('focus');
				$('#callnumber').focus();
			});
			$(document).click(function(){
				console.log('focus');
				$('#callnumber').focus();
			});
			$(document).on('focusout','#callnumber',function(){
				console.log('focus');
				$('#callnumber').focus();
			});
			$(document).on('keypress','#callnumber',function(event){
				//$('#callnumber').val(pad($('#callnumber').val(),3));
				if((event.which-48)==1){
					string1='';
					//console.log($('#callnumber').val());
					if($('#callnumber').val().length==3){
						$('#callnumber').val($('#callnumber').val().substr(1,3));
					}
				}
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
										/*if(typeof player2 != 'undefined'){
											player2.setVolume(10);
										}*/
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
										/*if(typeof player2 != 'undefined'){
											setTimeout(function (){player2.setVolume(".$content['div2']['maxvolum'].")}, 5000);
										}*/
										console.log(d);
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
			};";
			
		if($content['div1']['type']==3){
			echo "var nextVideo1 = [";
			for($i=1;$i<=$content['div1']['localvideonumber'];$i++){
				if($i==1){
					echo '"./video1/video'.$i.'.mp4"';
				}
				else{
					echo ',"./video1/video'.$i.'.mp4"';
				}
			}
			echo "];
				var curVideo1 = 0;
				var videoPlayer1 = document.getElementById('videoPlayer1');
				videoPlayer1.onended = function(){
						++curVideo1;
					if(curVideo1 < nextVideo1.length){    		
						videoPlayer1.src = nextVideo1[curVideo1];        
					} 
					else if(curVideo1 == nextVideo1.length){
						videoPlayer1.src = nextVideo1[0];
						curVideo1=0;
					}
				}
				document.getElementById('videoPlayer1').volume = ".$content['div1']['localmaxvolum'].";";
		}
		else{
		}
		if($content['div2']['type']==3){
			echo "var nextVideo2 = [";
			for($i=1;$i<=$content['div2']['localvideonumber'];$i++){
				if($i==1){
					echo '"./video2/video'.$i.'.mp4"';
				}
				else{
					echo ',"./video2/video'.$i.'.mp4"';
				}
			}
			echo "];
				var curVideo2 = 0;
				var videoPlayer2 = document.getElementById('videoPlayer2');
				videoPlayer2.onended = function(){
						++curVideo2;
					if(curVideo2 < nextVideo2.length){    		
						videoPlayer2.src = nextVideo2[curVideo2];        
					} 
					else if(curVideo2 == nextVideo2.length){
						videoPlayer2.src = nextVideo2[0];
						curVideo2=0;
					}
				}
				document.getElementById('videoPlayer2').volume = ".$content['div2']['localmaxvolum'].";";
		}
		else{
		}
			
	echo "});
	</script>";
echo "<body style='position: relative;'>";
echo "<div style='position:relative;width:".((400/1920)*100)."%;height:".((180/1080)*100)."%;position:fixed;right:0;top:0;overflow:hidden;'>
		<img src='img/".$content['bord']['type']."/".$content['callnumber']['image']."?".date('His')."' width='100%' height='100%' style='z-index:-1;position:absolute;top:0;left:0;'>
		<iframe id='frame' src='callnum.php?' frameborder='0' style='width:90%;height:108%;margin:-30px 0 0 13px;z-index:1;'></iframe>
	</div>";//<iframe id='frame' src='callnum.php?' frameborder='0' style='width:90%;height:100%;margin:-25px 0 0 30px;z-index:1;'></iframe>
if($content['div1']['type']==1){
	echo "<div class='cycle-slideshow dyphoto' data-cycle-fx='scrollHorz' data-cycle-timeout='".$content['div1']['timeout']."' style='background-image:url(\"img/".$content['bord']['type']."/".$content['div1']['backimage']."\");width:".((1920/1920)*100)."%;height:".((1080/1080)*100)."%;padding:0;margin:0;float:left;'> ";
	for($i=1;$i<=$content['div1']['number'];$i++){
		if($i==1){
			echo "<img src='img/".$content['bord']['type']."/".$content['div1']['image'.$i]."?".date('His')."' style='width:100%;height:100%;'>";
		}
		else{
			echo "<img src='img/".$content['bord']['type']."/".$content['div1']['image'.$i]."?".date('His')."' style='width:100%;height:100%;'>";
		}
	}
	echo "</div>";
}
else{
	echo "<div style='background-image:url(\"img/".$content['bord']['type']."/".$content['div1']['backimage']."\");width:".((1920/1920)*100)."%;height:".((1080/1080)*100)."%;padding:0;margin:0;float:left;'>";
	echo "<div id='player1' style='float:left;'></div>";
	echo "</div>";
}
echo "<input type='text' id='callnumber' value='000' style='position: fixed;bottom: -100px;right: -100px;' autofocus maxlength='3'>";
?>
<div style='width:100%;height:100%;position:absolute;z-index:10000;background-color:transparent;'></div>
</body>