<?php
$company=$_POST['company'];
$dep=$_POST['dep'];
?>
<script>
$(document).ready(function(){
	/*function readURL(input,name){
		//console.log(".secview #secviewbrowser .preview"+name);
		if(input.files[0]){
			var reader = new FileReader();
			reader.onload = function (e) {
				$(".secview #secviewbrowser .preview"+name).attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files);
		}
	};*/
	$('.secview #secviewbrowser select[name="imgnum"]').change(function(){
		var htmlstring='';
		$('.secview #secviewbrowser .imglist').html('');
		for(var i=0;i<$('.secview #secviewbrowser select[name="imgnum"] option:selected').val();i++){
			$('.secview #secviewbrowser .imglist').append('<div style="overflow:hidden;margin:10px 0;"><span style="float:left;">'+(i+1)+'.</span><img class="img'+(i+1)+'" src="../menudata/<?php echo $company; ?>/<?php echo $dep; ?>/img/imglist/'+(i+1)+'.png" style="width:25px;height:25px;float:left;cursor: pointer;margin:0 10px;"><input style="float:left;" id="img'+(i+1)+'" type="file" name="imglist'+(i+1)+'"><img class="previewimg'+(i+1)+'" src="" style="width:25px;height:25px;float:left;cursor: pointer;margin:0 10px;"></div>');
		}
	});
	$('.secview #secviewbrowser').on('change','input[name^=imglist]',function(e){
		var name=$(this).prop('id');
		const file = this.files[0];
		const fr = new FileReader();
		fr.onload = function (e) {
			//console.log(".secview #secviewbrowser .preview"+name);
			//console.log($(".secview #secviewbrowser .preview"+name).attr('src'));
			$(".secview #secviewbrowser .preview"+name).attr('src', e.target.result);
		};
		  
		// 使用 readAsDataURL 將圖片轉成 Base64
		fr.readAsDataURL(file);
	});
	$('.secview #secviewbrowser').on('click','img[class^=img]',function(){
		//console.log('1');
		$('.secview .cloud .img').prop('src',$(this).prop('src'));
		$('.secview .cloud').css({'display':'block'});
	});
	$('.secview #secviewbrowser').on('click','img[class^=previewimg]',function(){
		//console.log('1');
		$('.secview .cloud .img').prop('src',$(this).prop('src'));
		$('.secview .cloud').css({'display':'block'});
	});
	$('.secview .cloud').click(function(){
		$(this).css({'display':'none'});
	});
});
secview=$('.secview').tabs();
</script>
<style>
.secview #secviewbrowser .browser {
	margin:0;
	padding:20px;
	background-color:#231815;
	color:#ffffff;
}
@media screen and (min-width: 680px) and (max-width: 1057px) {
	.secview #secviewbrowser {
		overflow:auto;
	}
	.secview #secviewbrowser .browser {
		width:800px;
		height:calc(800px * 768 / 1366);
	}
}
@media screen and (min-width: 1058px) and (max-width: 1365px) {
	.secview #secviewbrowser {
		overflow:auto;
	}
	.secview #secviewbrowser .browser {
		width:calc(100vw - 210px - 20px - 10px - 2px - .4em - 2em);
		height:calc((100vw - 210px - 20px - 10px - 2px - .4em - 2em) * 768 / 1366);
	}
}
@media screen and (min-width: 1366px) {
	.secview #secviewbrowser {
		overflow:auto;
	}
	.secview #secviewbrowser .browser {
		width:calc(1366px - 210px - 20px - 10px - 2px - .4em - 2em);
		height:calc((1366px - 210px - 20px - 10px - 2px - .4em - 2em) * 768 / 1366);
	}
}
@media screen and (max-width: 680px) {
	.secview #secviewbrowser {
		overflow:auto;
	}
	.secview #secviewbrowser .browser {
		width:800px;
		height:calc(800px * 768 / 1366);
	}
}
</style>
<?php
include_once '../../../tool/inilib.php';
if(isset($_POST['lan'])&&$_POST['lan']!=''){
	if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
		$interface=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('../../lan/interface1.ini')){
		$interface=parse_ini_file('../../lan/interface1.ini',true);
	}
	else{
		$interface='-1';
	}
}

if(file_exists('../../../ourpos/'.$company.'/'.$dep.'/machinedata.ini')){
	$content=parse_ini_file('../../../ourpos/'.$company.'/'.$dep.'/machinedata.ini',true);
}
else{
}
if(file_exists('../../../menudata/'.$company.'/'.$dep.'/img/secview.ini')){
	$secview=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/img/secview.ini',true);
}
else{
}
echo '<div class="secview" style="overflow:hidden;margin-bottom:3px;">';
	echo "<div class='cloud' style='width:100%;height:100%;background-color:#ffffff;top:0;left:0;position:fixed;z-index:100;display:none;'><img class='img' style='width:100%;height:100%;object-fit:contain;display:block;' src=''><div style='border: 2px solid #000000; border-radius: 5px; margin: 5px;position:fixed;top:0;right:0;font-size:20px;font-weight:bold;'>close</div></div>
		<ul style='width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<li><a class='secviewbrowser' href='#secviewbrowser'>";if($interface!='-1'&&isset($interface['name']['secviewbrowser']))echo $interface['name']['secviewbrowser'];else echo '客顯設定';echo "</a></li>
		</ul>";
	echo '<div id="secviewbrowser" style="width:calc(100% - 2em);float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;padding:0;margin:1em;">
			<div class="browser" style="margin-bottom:10px;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
				<div style="width:70%;height:calc(103 / 728 * 100%);float:left;background-color:transparent;">';
				if(file_exists('../../../menudata/'.$company.'/'.$dep.'/img/logo.png')){
					echo '<img src="./menudata/'.$company.'/'.$dep.'/img/logo.png" style="width:100%;height:100%;">';
				}
				else if(isset($content)){
					echo $content['basic']['story'];
				}
				else {
				}
			echo '</div>
				<div style="width:30%;height:calc(103 / 728 * 100%);border-bottom:3px solid #ffff;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:right;">';
					if(isset($secview)){
						echo $secview['title']['text'];
					}
					else{
					}
			echo '</div>
				<div style="width:30%;height:calc(522 / 728 * 100%);float:right;">
				</div>
				<div style="width:70%;height:calc(522 / 728 * 100%);float:left;">';
				if(isset($secview)){
					echo '<div width:100%;height:100%;padding:0;margin:0;float:left;">';
					if(file_exists('../../../menudata/'.$company.'/'.$dep.'/img/imglist')){
						$filelist=scandir('../../../menudata/'.$company.'/'.$dep.'/img/imglist');
						foreach($filelist as $v){
							if(!in_array($v,array(".","..","Thumbs.db"))){
								if (is_dir('./img/imglist' . DIRECTORY_SEPARATOR . $v)){
									continue;
								}
								else{
									echo "<img style='width:100%;height:100%;' src='./menudata/".$company."/".$dep."/img/imglist/".$v."'>";
									break;
								}
							}
							else{
							}
						}
					}
					else{
					}
					echo '</div>';
				}
				else{
				}
			echo '</div>
				<div style="width:70%;height:calc(103 / 728 * 100%);float:left;background-color:transparent;';
				if(file_exists('../../../menudata/'.$company.'/'.$dep.'/img/bk6.png')){
					echo 'background-image:url(\'./menudata/'.$company.'/'.$dep.'/img/bk6.png\');';
				}
				else{
				}
				echo 'background-size:100% 100%;overflow:hidden;">';
				if(isset($secview)){
					echo '<div style="margin:10px 0;white-space:nowrap;font-size:40px;color:'.$secview['marquee']['color'].'">'.$secview['marquee']['text'].'</div>';
				}
				else{
				}
			echo '</div>
				<div style="width:30%;height:calc(103 / 728 * 100%);float:left;background-color:transparent;">';
				if(file_exists('../../../menudata/'.$company.'/'.$dep.'/img/table.png')){
					echo '<img src="./menudata/'.$company.'/'.$dep.'/img/table.png" style="width:100%;height:100%;">';
				}
				else{
				}
			echo '</div>
			</div>
			<form class="secviewform" enctype="multipart/form-data">
				<input type="hidden" name="company" value="'.$company.'">
				<input type="hidden" name="dep" value="'.$dep.'">
				<table style="border-collapse:collapse;">
					<tr>
						<td style="font-weight:bold;">';if($interface!='-1'&&isset($interface['name']['seclogo']))echo $interface['name']['seclogo'];else echo 'LOGO圖';echo '</td>
						<td><input type="file" name="logo"></td>
					</tr>
					<tr>
						<td style="font-weight:bold;">';if($interface!='-1'&&isset($interface['name']['greetings']))echo $interface['name']['greetings'];else echo '招呼語';echo '</td>
						<td><input type="text" name="greetings" value="';
						if(isset($secview)){
							echo $secview['title']['text'];
						}
						else{
						}
						echo '"></td>
					</tr>
					<tr>
						<td style="font-weight:bold;">';if($interface!='-1'&&isset($interface['name']['marqueetitle']))echo $interface['name']['marqueetitle'];else echo '跑馬燈';echo '</td>
						<td></td>
					</tr>
					<tr>
						<td style="font-weight:bold;">&nbsp;&nbsp;';if($interface!='-1'&&isset($interface['name']['marquee']))echo $interface['name']['marquee'];else echo '跑馬燈文字';echo '</td>
						<td><input type="text" name="marquee" value="';
						if(isset($secview)){
							echo $secview['marquee']['text'];
						}
						else{
						}
						echo '"></td>
					</tr>
					<tr>
						<td style="font-weight:bold;">&nbsp;&nbsp;';if($interface!='-1'&&isset($interface['name']['marqueecolor']))echo $interface['name']['marqueecolor'];else echo '跑馬燈顏色';echo '</td>
						<td><input type="color" name="marqueecolor" value="';
						if(isset($secview)){
							echo $secview['marquee']['color'];
						}
						else{
						}
						echo '"></td>
					</tr>
					<tr>
						<td style="font-weight:bold;">&nbsp;&nbsp;';if($interface!='-1'&&isset($interface['name']['marqueespeed']))echo $interface['name']['marqueespeed'];else echo '跑馬燈速率';echo '</td>
						<td><input type="number" name="speed" value="';
						if(isset($secview)){
							echo $secview['marquee']['speed'];
						}
						else{
						}
						echo '"</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">';if($interface!='-1'&&isset($interface['name']['tranimglistnum']))echo $interface['name']['tranimglistnum'];else echo '輪播圖片張數';echo '</td>
						<td>
							<select name="imgnum">';
							if(isset($secview)&&isset($secview['leftimg']['imgnum'])){
								$max=$secview['leftimg']['imgnum'];
							}
							else{
								$max=10;
							}
							for($i=1;$i<=$max||$i<=10;$i++){
								echo '<option value="'.$i.'" ';
								if((isset($secview)&&isset($secview['leftimg']['imgnum'])&&$i==$max)||((!isset($secview)||!isset($secview['leftimg']['imgnum']))&&$i==1)){
									echo 'selected';
								}
								else{
								}
								echo '>'.$i.'張</option>';
							}
						echo '</select>
						</td>
					</tr>
					<tr class="imglistlabel">
						<td style="font-weight:bold;">';if($interface!='-1'&&isset($interface['name']['tranimglist']))echo $interface['name']['tranimglist'];else echo '輪播圖片清單';echo '</td>
						<td></td>
					</tr>';
					if(isset($secview)&&isset($secview['leftimg']['imgnum'])){
						echo '<tr>
								<td colspan="2" class="imglist">
									<div style="overflow:hidden;margin:10px 0;"><span style="float:left;">1.</span><img class="img1" src="../menudata/'.$company.'/'.$dep.'/img/imglist/1.png" style="width:25px;height:25px;float:left;cursor: pointer;margin:0 10px;"><input style="float:left;" id="img1" type="file" name="imglist1"><img class="previewimg1" src="" style="width:25px;height:25px;float:left;cursor: pointer;margin:0 10px;"></div>';
						for($i=2;$i<=$secview['leftimg']['imgnum'];$i++){
							echo '<div style="overflow:hidden;margin:10px 0;"><span style="float:left;">'.$i.'.</span><img class="img'.$i.'" src="../menudata/'.$company.'/'.$dep.'/img/imglist/'.$i.'.png" style="width:25px;height:25px;float:left;cursor: pointer;margin:0 10px;"><input style="float:left;" id="img'.$i.'" type="file" name="imglist'.$i.'"><img class="previewimg'.$i.'" src="" style="width:25px;height:25px;float:left;cursor: pointer;margin:0 10px;"></div>';
						}
						echo '</td>
							</tr>';
					}
					else{
						echo '<tr>
								<td colspan="2" class="imglist"><div style="overflow:hidden;margin:10px 0;"><span style="float:left;">1.</span><img class="img1" src="../menudata/'.$company.'/'.$dep.'/img/imglist/1.png" style="width:25px;height:25px;float:left;cursor: pointer;margin:0 10px;"><input style="float:left;" id="img1" type="file" name="imglist1"><img class="previewimg1" src="" style="width:25px;height:25px;float:left;cursor: pointer;margin:0 10px;"></div></td>
							</tr>';
					}
				echo '<tr>
						<td colspan="2"><input type="button" class="initbutton" id="savesecview" value="';if($interface!='-1'&&isset($interface['name']['savesecview']))echo $interface['name']['savesecview'];else echo '修改客顯';echo '"></td>
					</tr>';
		echo '</table>
			</form>
		</div>';
echo "</div>";
?>