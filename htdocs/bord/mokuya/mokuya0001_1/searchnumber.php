<!DOCTYPE html>
<?php
$content=parse_ini_file('./data/content.ini',true);
$myfile = fopen("./now.txt", "r") or die("Unable to open file!");
$now=fread($myfile,filesize("./now.txt"));
fclose($myfile);
$web=parse_ini_file('webaddress.ini',true);
//echo "<h1>查詢號碼介面</h1>";
?>
<script src='./lib/jquery-1.12.4.js'></script>
<script>
	$(document).ready(function(){
		setInterval(function upload(){
			$.ajax({
				url:'./changenumber.php',
				dataType:'html',
				success:function(d){
					$('.div2 #nownumber').html(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}, 3000);
	});
</script>
<style>
	body {
		padding:0;
		margin:0;
	}
	.logo {
		width:100%;
		height:395px;
		background-color:#000000;
		background-image:url("../img/searchnumber/searchlogo.jpg?<?php echo date('YmdHis'); ?>");
		background-repeat:no-repeat;
		background-position:center top;
	}
	.content {
		width:100%;
		height:940px;
		background-color:#ffffff;
		/*background-image:url(\"../img/searchnumber/searchcontent.png\");*/
		background-repeat:no-repeat;
		background-position:center top;
	}
	.div1,.div2,.div3,.div4,.div5,.div6,.div7 {
		background-color:#ffffff;
		width:750px;
		margin:0 auto;
		border:#ffffff 0 solid; 
		font-family: Consolas,Microsoft JhengHei,sans-serif;
	}
	a {
		text-decoration:none;
		color:#003399;
	}
</style>
<div class='logo'>
</div>
<div class='content'>
	<div class='div1'>
		<img src='../img/searchnumber/div1.png'>
	</div>
	<div class='div2'>
		<div id='nownumber' style='width:100%;margin:0 auto;font:160px/160px Arial;text-align:center;font-weight: bold;color:#c9caca;'><?php echo str_pad ( $now , 3 , '0' , STR_PAD_LEFT ); ?></div>
	</div>
	<div class='div3'>
		<img src='../img/searchnumber/div3.png'>
	</div>
	<div class='div4'>
		<div id='nownumber' style='width:100%;margin:0 auto;font:160px/160px Arial;text-align:center;font-weight: bold;color:#231815;'><?php if(isset($_GET['num']))echo str_pad ( $_GET['num'] , 3 , '0' , STR_PAD_LEFT );else echo 'NULL'; ?></div>
	</div>
	<div class='div5' style='width:100%;text-align:center;margin:20px 0;font-size:30px;'>
		<center>如已過號，請洽服務人員</center>
		<!-- <img src='../img/searchnumber/div5.png'> -->
	</div>
	<div class='div6'>
		<div style='width:max-content;margin:0 auto;padding:0;'>
			<input type='image' style='' src='../img/searchnumber/fbimg.png' 
			<?php
			if(isset($content['fbid']['id'])&&$content['fbid']['id']!=''){
				if(preg_match('/(iOS|iPad|iPhone)/',$_SERVER["HTTP_USER_AGENT"])){
				?>
				onclick='window.open("fb://profile/<?php echo $content['fbid']['id']; ?>");'
				<?php
				}
				else if(preg_match('/(Android)/',$_SERVER["HTTP_USER_AGENT"])){
				?>
				onclick='window.open("fb://page/<?php echo $content['fbid']['id']; ?>");'
				<?php
				}
				else{
				?>
				onclick='window.open("https://www.facebook.com/<?php echo $content['fbid']['id']; ?>");'
				<?php
				}
			}
			else{
				if(preg_match('/(iOS|iPad|iPhone)/',$_SERVER["HTTP_USER_AGENT"])){
				?>
				onclick='window.open("fb://profile/371532990382127");'
				<?php
				}
				else if(preg_match('/(Android)/',$_SERVER["HTTP_USER_AGENT"])){
				?>
				onclick='window.open("fb://page/id=371532990382127");'
				<?php
				}
				else{
				?>
				onclick='window.open("https://www.facebook.com/371532990382127");'
				<?php
				}
			}
			?>
			>
			<input type='image' src='../img/searchnumber/logoimg.png' style='border-radius:100%;'
			<?php
			if(isset($web['web']['address'])&&$web['web']['address']!=''){
			?>
			onclick='window.open("<?php echo $web['web']['address']; ?>");'
			<?php
			}
			?>
			>
		</div>
	</div>
	<div class='div7' style='width:100%;text-align:center;margin-top:20px;font-size:30px;'>
		<!-- <img src='../img/searchnumber/div7.png'> -->
		<?php
		/*echo $_SERVER["HTTP_USER_AGENT"];
		if(preg_match('/(iOS|iPad|iPhone)/',$_SERVER["HTTP_USER_AGENT"])){
			echo '1';
		}
		else if(preg_match('/(Android)/',$_SERVER["HTTP_USER_AGENT"])){
			echo '2';
		}
		else{
			echo '3';
		}*/
		?>
		<a href="http://www.tableplus.com.tw" target="_blank">copyright @ by TABLE PLUE</a>
	</div>
</div>
