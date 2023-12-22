<?php
include_once '../../../tool/checkweb.php';
$yn=check_mobile();
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini','w');
	fwrite($f,'[pay]'.PHP_EOL);
	fwrite($f,'openpay=0'.PHP_EOL);
	fclose($f);
}
$otherpay=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini',true);
?>
<script>
otherpay=$('.otherpay').tabs();
otherpay.tabs('option','disabled',[1]);
$(document).ready(function(){
	/*$('.otherpay').on('click','#create',function(){
		$.ajax({
			url:'./lib/js/getotherpaydata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('.otherpay #setpaydata').html(d);
				otherpay.tabs('option','disabled',[]);
				otherpay.tabs('option','active','1');
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$('.otherpay #allpaydata').on('click','.itemrow',function(){
		var index=$('.otherpay #allpaydata .itemrow').index(this);
		$('.otherpay #allpaydata .itemrow').prop('id',index);
		$('.otherpay #allpaydata .itemrow:eq('+index+')').prop('id','focus');
		otherpay.tabs('option','disabled',[]);
		if($('.otherpay #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]:checked').length>0){
			$('.otherpay #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',false);
			$('.otherpay #allpaydata .itemrow:eq('+index+') #chimg').attr('src','./img/noch.png');
		}
		else{
			$('.otherpay #allpaydata .itemrow:eq('+index+') input[type=\"checkbox\"]').prop('checked',true);
			$('.otherpay #allpaydata .itemrow:eq('+index+') #chimg').attr('src','./img/onch.png');
		}
		//console.log($('.otherpay #allpaydata .itemrow#focus input[name="no[]"]').val());
		$.ajax({
			url:'./lib/js/getotherpaydata.php',
			method:'post',
			data:{'lan':$('.lan').val(),'number':$('.otherpay #allpaydata .itemrow#focus input[name="no[]"]').val(),'company':$('input[name="company"]').val(),'dep':$('input[name="db"]').val()},
			dataType:'html',
			success:function(d){
				$('.otherpay #setpaydata').html(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});*/
});
$('#allpaydata #otherpayTable').tableHeadFixer();
$('#setpaydata #otherpayTable').tableHeadFixer();
</script>
<style>
.otherpay #allpaydata table,
.otherpay #setpaydata table {
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
.otherpay #allpaydata table thead,
.otherpay #setpaydata table thead {
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
.otherpay #allpaydata table td,
.otherpay #allpaydata table th,
.otherpay #setpaydata table td,
.otherpay #setpaydata table th,
.otherpay #setpaydata table td {
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
.otherpay #allpaydata table tbody tr:nth-child(odd) {
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
<div class='otherpay' style="width:100%;height:100%;overflow:auto;">
	<ul>
		<li><a id='allpay' href='#allpaydata'>全部其他付款</a></li>
		<li><a id='setpay' href='#setpaydata'>設定</a></li>
	</ul>
	<div id='allpaydata' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center>其他付款列表</center></h1>
		<div id='param' style='display:none;'>
			<input type='hidden' id='prev' value=''>
			<input type='hidden' id='focus' value=''>
			<input type='hidden' id='next' value=''>
		</div>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='create' value='新增'>
			<input type='button' class='initbutton' id='edit' value='修改'>
			<input type='button' class='initbutton' id='delete' value='刪除'>
		</div>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='otherpayTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<table id='otherpayTable'>
					<thead>
						<tr>
							<th></th>
							<th>付款方式</th>
							<th>別稱</th>
						</tr>
					</thead>
					<tbody>
				<?php
				if(isset($otherpay['item1'])){
					for($i=1;$i<sizeof($otherpay);$i++){
						echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='item".$i."'></td><td>".$otherpay['item'.$i]['name']."</td><td>".$otherpay['item'.$i]['dbname']."</td></tr>";
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
	<div id='setpaydata' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
	</div>
</div>