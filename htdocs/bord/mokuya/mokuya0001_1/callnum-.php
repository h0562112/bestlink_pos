<?php
$content=parse_ini_file('./data/content.ini',true);//讀取設定檔

echo "<link rel='stylesheet' type='text/css' href='num.css'>";
$number=(isset($_GET['number']))?($_GET['number']):("");
$myfile = fopen("now.txt", "r") or die("Unable to open file!");
$nownumber=fread($myfile,filesize("now.txt"));
fclose($myfile);
//echo strlen($nownumber)."<br>";

$call=(isset($_GET['call']))?('1'):('');//長度==0不叫號

$nownumber=str_pad ( $nownumber , 3 , '0' , STR_PAD_LEFT );//自動補零

if(isset($_GET['x'])&&$_GET['x']==1){
	echo "<div id='tran'>";
}
else{
	echo "<div>";
}
for ($i=0;$i<strlen($nownumber);$i++){
	$picnum=substr($nownumber,$i,1);
	//echo $picnum;
	switch ($picnum) {
    case 0:
		if ($picnum != "") {
			//echo '<img src="img/num_0.png" border="0" alt="">';
			echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>0</span>";
		}
        break;
    case 1:
		//echo '<img src="img/num_1.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>1</span>";
        break;
    case 2:
        //echo '<img src="img/num_2.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>2</span>";
        break;
	case 3:
        //echo '<img src="img/num_3.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>3</span>";
        break;
	case 4:
        //echo '<img src="img/num_4.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>4</span>";
        break;
	case 5:
        //echo '<img src="img/num_5.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>5</span>";
        break;
	case 6:
        //echo '<img src="img/num_6.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>6</span>";
        break;
	case 7:
        //echo '<img src="img/num_7.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>7</span>";
        break;
	case 8:
        //echo '<img src="img/num_8.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>8</span>";
        break;
	case 9:
        //echo '<img src="img/num_9.png" border="0" alt="">';
		echo "<span id='numbertext' style='color:".$content['callnumber']['color']."'>9</span>";
        break;
	}
}
	echo "</center></td>
		</tr>
	</table>";

/*echo "</div>";
if(isset($_GET['x'])&&$_GET['x']==1){
	echo "<div id='tran1'>";
}
else{
	echo "<div>";
}
echo '<img src="img/3good2.jpg" border="0" alt="">';
echo "</div>";*/
if($number!=$nownumber&&$call=='1'){//判斷數字是否有更換，如果有則新增HTML5播放器來播放清單，其中nextVideo的陣列設定需要更改。才能做到數字長度可變動
	$nownumber=str_pad ( $nownumber , 3 , '0' , STR_PAD_LEFT );
	echo '<audio id="player" autoplay preload="metadata" volume="1">
			<source src="../test/ding.mp3" type="audio/mpeg">
				Sorry, this browser does not support HTML 5.0
		</audio>
		<script>
			var nextVideo = ["../test/ding.mp3","../test/guest.wav",';
			if(substr($nownumber,0,1)==0){//百位數
				if(substr($nownumber,1,1)==0){
					if(substr($nownumber,2,1)==0){
					}
					else{
						echo '"../test/'.substr($nownumber,2,1).'.mp3",';
					}
				}
				else{
					if(substr($nownumber,1,1)==1){
						if(substr($nownumber,2,1)==0){
							echo '"../test/10.mp3",';
						}
						else{
							echo '"../test/10.mp3","../test/'.substr($nownumber,2,1).'.mp3",';
						}
					}
					else{
						if(substr($nownumber,2,1)==0){
							echo '"../test/'.substr($nownumber,1,1).'0.mp3",';
						}
						else{
							echo '"../test/'.substr($nownumber,1,1).'0.mp3","../test/'.substr($nownumber,2,1).'.mp3",';
						}
					}
				}
			}
			else{
				if(substr($nownumber,1,1)==0){
					if(substr($nownumber,2,1)==0){
						echo '"../test/'.substr($nownumber,0,1).'00.mp3",';
					}
					else{
						echo '"../test/'.substr($nownumber,0,1).'00.mp3","../test/0.mp3","../test/'.substr($nownumber,2,1).'.mp3",';
					}
				}
				else{
					if(substr($nownumber,1,1)==1){
						if(substr($nownumber,2,1)==0){
							echo '"../test/'.substr($nownumber,0,1).'00.mp3","../test/'.substr($nownumber,1,1).'10.mp3",';
						}
						else{
							echo '"../test/'.substr($nownumber,0,1).'00.mp3","../test/'.substr($nownumber,1,1).'10.mp3","../test/'.substr($nownumber,2,1).'.mp3",';
						}
					}
					else{
						if(substr($nownumber,2,1)==0){
							echo '"../test/'.substr($nownumber,0,1).'00.mp3","../test/'.substr($nownumber,1,1).'0.mp3",';
						}
						else{
							echo '"../test/'.substr($nownumber,0,1).'00.mp3","../test/'.substr($nownumber,1,1).'0.mp3","../test/'.substr($nownumber,2,1).'.mp3",';
						}
					}
				}
			}
			echo '"../test/number.wav"];
			var curVideo = 0;
			var videoPlayer = document.getElementById("player");
			videoPlayer.onended = function(){
					++curVideo;
				if(curVideo < nextVideo.length){    		
					videoPlayer.src = nextVideo[curVideo];        
				} 
			}
        </script>';
}
/*if(isset($_GET['x'])&&$_GET['x']==1){
	header('refresh: 5;url="callnum.php?x=1&number='.$nownumber.'"');//在網址後面加上目前使用的數字，已利判斷是否有更換數字
}
else{
	header('refresh: 5;url="callnum.php?number='.$nownumber.'"');//在網址後面加上目前使用的數字，已利判斷是否有更換數字
}
*/
?>