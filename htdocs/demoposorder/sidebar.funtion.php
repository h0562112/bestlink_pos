<?php
function sidebar($usergroup){
	echo "<script>
			$(document).ready(function(){
				var liindex=-1;
				var dindex=-1;
				var uindex=-1;
				var nowview='";if(isset($_POST['conttype'])){echo $_POST['conttype'];}else{};echo "';
				if(nowview!=\"\"){
					$('li .'+nowview).css({'background-color':'#33ccff'});
				}
				else{
				}
				$('li').mouseover(function(){
					liindex=$('li').index(this);
					$('li:eq('+liindex+')').find('input[type=\"button\"]').css({'background-color':'#ffcc00'});
				});
				$('li').mousedown(function(){
					dindex=liindex;
					$('li:eq('+liindex+')').find('input[type=\"button\"]').css({'background-color':'#ffccbb'});
				});
				$('li').mouseup(function(){
					uindex=liindex;
					$('li:eq('+liindex+')').find('input[type=\"button\"]').css({'background-color':'#ffcc00'});
					if(dindex==uindex){
						$('input[name=\"conttype\"]').val($('li:eq('+liindex+')').find('input[type=\"button\"]').attr('class'));
						$('#submitform').submit();
					}
					else{
					}
				});
				$('li').mouseleave(function(){
					if($('li:eq('+liindex+')').find('input[type=\"button\"]').attr('class')==nowview){
						$('li:eq('+liindex+')').find('input[type=\"button\"]').css({'background-color':'#33ccff'});
					}
					else{
						$('li:eq('+liindex+')').find('input[type=\"button\"]').css({'background-color':'#ffffff'});
					}
					liindex=-1;
					dindex=-1;
					uindex=-1;
				});
			});
		</script>";
	$conttype=(isset($_POST['conttype']))?($_POST['conttype']):("");
	echo "<ul>";
		if($usergroup=='boss'){
		echo "<!-- <li><span>顯示排序</span></li><hr> -->
			<li><input type='button' class='menu' value='餐點設定'></li><hr>
			<li><input type='button' class='type' value='類別設定'></li><hr>
			<li><input type='button' class='taste' value='口味(加料)設定'></li><hr>
			<!-- <li><input type='button' class='sale' value='折扣設定'></li><hr> -->
			<li><input type='button' class='monnumber' value='門市營業匯總表(月)'></li><hr>
			<li><input type='button' class='daynumber' value='門市營業匯總表(日)'></li><hr>
			<li><input type='button' class='storynumber' value='門市商品銷售統計'></li><hr>";
		}
		echo "<li><input type='button' class='newnumberofday' value='商品銷售統計'></li><hr>
			<li><input type='button' class='newstototal' value='該店總圖表'></li><hr>
			<li><input type='button' class='atmoment' value='即時戰報'></li><hr>
			<li><input type='button' class='editpsw' value='修改密碼'></li>
		</ul>
		<div class='hiform' style='display:none;'>
			<form method='post' action='management.php' id='submitform'>
				<input type='hidden' name='conttype' value='newnumberofday'>
			</form>
		</div>";
}
?>