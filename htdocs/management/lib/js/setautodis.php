<?php
include_once '../../../tool/checkweb.php';
$yn=check_mobile();
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount1.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount1.ini','w');
	fclose($f);
}
$dis1=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount1.ini',true);
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount2.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount2.ini','w');
	fclose($f);
}
$dis2=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount2.ini',true);
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount3.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount3.ini','w');
	fclose($f);
}
$dis3=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount3.ini',true);
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount4.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount4.ini','w');
	fclose($f);
}
$dis4=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/discount4.ini',true);
?>
<script>
autodis=$('.autodis').tabs();
autodis.tabs('option','disabled',[1]);
$(document).ready(function(){
	/*$('.autodis').on('click','#create',function(){
		$.ajax({
			url:'./lib/js/getautodisdata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('.autodis #setpaydata').html(d);
				autodis.tabs('option','disabled',[]);
				autodis.tabs('option','active','1');
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$('.autodis #allpaydata').on('click','.itemrow',function(){
		var index=$('.autodis #allpaydata .itemrow').index(this);
		$('.autodis #allpaydata .itemrow').prop('id',index);
		$('.autodis #allpaydata .itemrow:eq('+index+')').prop('id','focus');
		autodis.tabs('option','disabled',[]);
		if($('.autodis #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.autodis #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.autodis #allpaydata .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.autodis #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.autodis #allpaydata .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		//console.log($('.autodis #allpaydata .itemrow#focus input[name="no[]"]').val());
		$.ajax({
			url:'./lib/js/getautodisdata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':$('.autodis #allpaydata .itemrow#focus input[name="no[]"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('.autodis #setpaydata').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});*/
});
$('#alldisdata #autodisTable').tableHeadFixer();
$('#setdisdata #autodisTable').tableHeadFixer();
</script>
<style>
.autodis #alldisdata table,
.autodis #setdisdata table {
	font-family: Consolas,Microsoft JhengHei,sans-serif;
	border-collapse: collapse;
	<?php
	if($yn){
		echo 'font-size:40px;';
	}
	else{
		echo 'font-size:20px;';
	}
	?>
}
.autodis #alldisdata table thead,
.autodis #setdisdata table thead {
	color:#898989;
	<?php
	if($yn){
		echo 'font-size:20px;';
	}
	else{
		echo 'font-size:12px;';
	}
	?>
}
.autodis #alldisdata table td,
.autodis #alldisdata table th,
.autodis #setdisdata table td,
.autodis #setdisdata table th,
.autodis #setdisdata table td {
	<?php
	if($yn){
		echo 'padding:10px 10px 6px 20px;';
	}
	else{
		echo 'padding:5px 5px 3px 10px;';
	}
	?>
	white-space: nowrap;
}
.autodis #alldisdata table tbody tr:nth-child(odd) {
	background-color:#f0f0f0;
}
.mod_select ul {
	margin:0;
	padding:0;
}
.mod_select ul:after {
	display: block;
    clear: both;
    visibility: hidden;
    height: 0;
    content: '';
}
.mod_select ul li {
	list-style-type:none;
	float:left;
	height:24px;
}
.select_label {
	color:#982F4D;
	float:left;
	line-height:24px;
	padding-right:10px;
	font-size:12px;
	font-weight:700;
}
.select_box {
	float:left;
	border:solid 1px #ccc;
	color:#444;
	position:relative;
	cursor:pointer;
	width:300px;
	font-size:14px;
}
.selet_open {
	display:inline-block;
	position:absolute;
	right:0;
	top:0;
	width:30px;
	height:100%;
	line-height:24px;
	text-align:center;
	content:'▼';
}
.select_txt {
	display:inline-block;
	padding-left:10px;
	width:300px;
	line-height:24px;
	height:24px;
	cursor:pointer;
	overflow:hidden;
}
.option {
	width:300px;
	border:solid 1px #ccc;
	position:absolute;
	top:24px;
	left:-1px;
	z-index:2;
	overflow:hidden;
	display:none;
}
.option a {
	display:block;
	height:26px;
	line-height:26px;
	text-align:left;
	padding:0 10px;
	width:100%;
	background:#fff;
}
.option a:hover {
	background:#aaa;
}
</style>
<div class='autodis' style="width:100%;height:100%;overflow:auto;">
	<ul>
		<li><a id='allautodis' href='#alldisdata'>全部優惠方案</a></li>
		<li><a id='setautodis' href='#setdisdata'>設定</a></li>
	</ul>
	<div id='alldisdata' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center>優惠方案列表</center></h1>
		<div id='param' style='display:none;'>
			<input type='hidden' id='prev' value=''>
			<input type='hidden' id='focus' value=''>
			<input type='hidden' id='next' value=''>
		</div>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='create' value='新增'>
			<input type='button' class='initbutton' id='edit' value='修改'>
		</div>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='autodisTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<table id='autodisTable' style='border-collapse: separate; border-spacing: 0;margin:5px;'>
					<thead>
						<tr>
							<th></th>
							<th>優惠名稱</th>
							<th>買N</th>
							<th>送M</th>
							<th>優惠類別</th>
							<th>優惠次數</th>
						</tr>
					</thead>
					<tbody>
				<?php
				if(isset($dis1['1'])){
					echo '<tr style="background-color: transparent;"><th colspan="6" style="border: 1px solid #898989; border-radius: 5px;">內用(discount1)</th></tr>';
					for($i=1;$i<=sizeof($dis1);$i++){
						echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='discount1-".$i."'></td><td>".$dis1[$i]['name']."</td><td style='text-align:right;'>".$dis1[$i]['buy']."</td><td style='text-align:right;'>".$dis1[$i]['free']."</td><td style='text-align:right;'>";
						if(!isset($dis1[$i]['distype'])||$dis1[$i]['distype']=='1'){
							echo '折讓';
						}
						else if($dis1[$i]['distype']=='2'){
							echo '折扣';
						}
						else{//無優惠上限
							echo '單一價';
						}
						echo "</td><td style='text-align:right;'>";
						if($dis1[$i]['listtype']=='0'){//關閉優惠
							echo '<span style="color:#ff0000;">停用優惠</span>';
						}
						else if($dis1[$i]['listtype']=='-1'){
							echo '無上限';
						}
						else if($dis1[$i]['listtype']>0){
							echo $dis1[$i]['listtype'].'次';
						}
						else{
							echo 'parameter error';
						}
						echo "</td></tr>";
					}
				}
				else{
				}
				if(isset($dis2['1'])){
					echo '<tr style="background-color: transparent;"><th colspan="6" style="border: 1px solid #898989; border-radius: 5px;">外帶(discount2)</th></tr>';
					for($i=1;$i<=sizeof($dis2);$i++){
						echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='discount2-".$i."'></td><td>".$dis2[$i]['name']."</td><td style='text-align:right;'>".$dis2[$i]['buy']."</td><td style='text-align:right;'>".$dis2[$i]['free']."</td><td style='text-align:right;'>";
						if(!isset($dis2[$i]['distype'])||$dis2[$i]['distype']=='1'){
							echo '折讓';
						}
						else if($dis2[$i]['distype']=='2'){
							echo '折扣';
						}
						else{//無優惠上限
							echo '單一價';
						}
						echo "</td><td style='text-align:right;'>";
						if($dis2[$i]['listtype']=='0'){//關閉優惠
							echo '<span style="color:#ff0000;">停用優惠</span>';
						}
						else if($dis2[$i]['listtype']=='-1'){
							echo '無上限';
						}
						else if($dis2[$i]['listtype']>0){
							echo $dis2[$i]['listtype'].'次';
						}
						else{
							echo 'parameter error';
						}
						echo "</td></tr>";
					}
				}
				else{
				}
				if(isset($dis3['1'])){
					echo '<tr style="background-color: transparent;"><th colspan="6" style="border: 1px solid #898989; border-radius: 5px;">外送(discount3)</th></tr>';
					for($i=1;$i<=sizeof($dis3);$i++){
						echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='discount3-".$i."'></td><td>".$dis3[$i]['name']."</td><td style='text-align:right;'>".$dis3[$i]['buy']."</td><td style='text-align:right;'>".$dis3[$i]['free']."</td><td style='text-align:right;'>";
						if(!isset($dis3[$i]['distype'])||$dis3[$i]['distype']=='1'){
							echo '折讓';
						}
						else if($dis3[$i]['distype']=='2'){
							echo '折扣';
						}
						else{//無優惠上限
							echo '單一價';
						}
						echo "</td><td style='text-align:right;'>";
						if($dis3[$i]['listtype']=='0'){//關閉優惠
							echo '<span style="color:#ff0000;">停用優惠</span>';
						}
						else if($dis3[$i]['listtype']=='-1'){
							echo '無上限';
						}
						else if($dis3[$i]['listtype']>0){
							echo $dis3[$i]['listtype'].'次';
						}
						else{
							echo 'parameter error';
						}
						echo "</td></tr>";
					}
				}
				else{
				}
				if(isset($dis4['1'])){
					echo '<tr style="background-color: transparent;"><th colspan="6" style="border: 1px solid #898989; border-radius: 5px;">自取(discount4)</th></tr>';
					for($i=1;$i<=sizeof($dis4);$i++){
						echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='discount4-".$i."'></td><td>".$dis4[$i]['name']."</td><td style='text-align:right;'>".$dis4[$i]['buy']."</td><td style='text-align:right;'>".$dis4[$i]['free']."</td><td style='text-align:right;'>";
						if(!isset($dis4[$i]['distype'])||$dis4[$i]['distype']=='1'){
							echo '折讓';
						}
						else if($dis4[$i]['distype']=='2'){
							echo '折扣';
						}
						else{//無優惠上限
							echo '單一價';
						}
						echo "</td><td style='text-align:right;'>";
						if($dis4[$i]['listtype']=='0'){//關閉優惠
							echo '<span style="color:#ff0000;">停用優惠</span>';
						}
						else if($dis4[$i]['listtype']=='-1'){
							echo '無上限';
						}
						else if($dis4[$i]['listtype']>0){
							echo $dis4[$i]['listtype'].'次';
						}
						else{
							echo 'parameter error';
						}
						echo "</td></tr>";
					}
				}
				else{
				}
				?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div id='setdisdata' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
	</div>
</div>