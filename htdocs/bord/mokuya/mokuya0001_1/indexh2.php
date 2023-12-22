<title>遠端多媒體看板</title>
<?php
$content=parse_ini_file('./data/content.ini',true);
if($content['bord']['type']=='Horizontal2'){
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
			background-image:url('img/".$content['bord']['type']."/".$content['background']['image']."');
		}
	</style>";
echo "<script>
		var datetag='";if(file_exists("./callnumber/callnumber.ini")){$temp=parse_ini_file("./callnumber/callnumber.ini",true);echo $temp["data"]["time"];}echo "';
		$.extend($.support, { touch: 'ontouchend' in document });
		var tag = document.createElement('script');

		tag.src = 'https://www.youtube.com/iframe_api';
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		// 3. This function creates an <iframe> (and YouTube player)
		//    after the API code downloads.\n";
		/*偽PUSH*/	
		echo "var lock=0;
			var time=setInterval(function(){
				if(lock==0){
					lock=1;
					$.ajax({
						url:'./checknumber.ajax.php',
						method:'post',
						async:false,
						data:{'datetag':datetag},
						dataType:'html',
						success:function(d){
							console.log(datetag);
							if(d!='error'&&d!=datetag){
								datetag=d;
								if($('#frame').attr('src').match('call=')){
									$('#frame').attr('src', $('#frame').attr('src'));
								}
								else{
									$('#frame').attr('src', $('#frame').attr('src')+'call=');
									lock=2;
								}
							}
							else{
								//console.log(d);
							}
							console.log(d);
						},
						error:function(e){
							console.log(e);
						}
					});
					lock=0;
				}
				else{
				}
			},1000);";
		/*偽PUSH*/
		echo "var player1;
			function onYouTubeIframeAPIReady() {";
		if($content['div1']['type']==1){
		}
		else{
			echo "player1 = new YT.Player('player1', {
					height: '1080',
					width: '1280',
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
				if((event.which-48)<10&&(event.which-48)>=0){
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
						console.log($('#callnumber').val());
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
			};
		});
	</script>";
if($content['div1']['type']==1){
	echo "<div class='cycle-slideshow dyphoto' data-cycle-fx='scrollHorz' data-cycle-timeout='".$content['div1']['timeout']."' style='background-image:url(\"img/".$content['bord']['type']."/".$content['div1']['backimage']."\");width:1280px;height:1080px;padding:0;margin:0;float:left;'> ";
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
	echo "<div style='background-image:url(\"img/".$content['bord']['type']."/".$content['div1']['backimage']."\");width:1280px;height:1080px;padding:0;margin:0;float:left;'>";
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
echo "<div style='width:640px;height:540px;float:right;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['callnumber']['image']."\");'>
<iframe id='frame' src='callnum.php?' frameborder='0' style='width:387px;height:176px;margin:285px 0 0 128px;'></iframe>
</div>";
echo "<div style='width:232px;height:200px;position:fixed;right:0;background-image:url(\"img/".$content['bord']['type']."/".$content['logo']['image']."\");margin:58px 74px 0 0;'>
</div>";

echo "<div style='width:640px;height:200px;float:right;overflow:hidden;float:left;'>
		<!-- <img src='../gettarget.jpg' width='200' height='200' style='border:0;margin:0;float:left;'> -->
		<img src='img/".$content['bord']['type']."/data.png' width='640' height='200' style='border:0;margin:0;float:left;'>
	</div>";
/*echo "<div style='width:640px;height:230px;float:right;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['fbid']['image']."\");float:left;'>
		<iframe frameborder='0' src='fbqrcode.php?id=".$content['fbid']['id']."' scrolling='no' style='width:136px;height:136px;margin:45px 0 0 98px;float:left;'></iframe>
		<iframe frameborder='0' src='getname.php?id=".$content['fbid']['id']."' style='width:300px;height:40px;margin:40px 0 0 57px;float:left;'></iframe>
		<iframe class='fblike' src='getlike.php?id=".$content['fbid']['id']."' frameborder='0' style='height:95px;margin:15px 90px 0 0;float:right;'></iframe>
	</div>";*/
echo "<div style='width:640px;height:230px;float:right;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['fbid']['image']."\");float:left;'>
		<iframe frameborder='0' src='search.php?company=".$content['initial']['company']."&dep=".$content['initial']['story']."' style='width: 112px;height: 112px; margin: 99px 0 0 341px; float: left; border-radius: 5px;'></iframe>
	</div>";
echo "<div class='marquee'  style='width:640px;height:110px;position:fixed;right:0;bottom:0;overflow:hidden;background-image:url(\"img/".$content['bord']['type']."/".$content['marquee']['image']."\");float:left;'>
		<marquee scrollamount='";echo ($content['marquee']['speed']/274)*1185;echo "' style='font:80px/80px Microsoft JhengHei;margin:15px 0 0 0;'><font color='".$content['marquee']['color']."'>".$content['marquee']['text']."</font></marquee>
	</div>";
echo "<input type='text' id='callnumber' value='000' style='position: fixed;bottom: -100px;right: -100px;' autofocus maxlength='3'>";
?>