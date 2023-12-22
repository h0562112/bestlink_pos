<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>批量發票</title>
	<script src="../../tool/jquery-1.12.4.js?<?php echo date('His'); ?>"></script>
	<script src="../../tool/ui/1.12.1/jquery-ui.js?<?php echo date('His'); ?>"></script>
	<link rel="stylesheet" href="../../tool/ui/1.12.1/themes/base/jquery-ui.css?<?php echo date('His'); ?>">
	<style>
		body {
			width:100vw;
			height:100vh;
			overflow:hidden;
			margin:0;
		}
		#title {
			width:100%;
			height:140px;
			text-align:center;
		}
		#title h1 {
			width:100%;
			text-align:center;
			margin-top:0;
		}
		button {
			border:1px solid #898989;
			background-color:transparent;
			border-radius:5px;
			padding:5px 10px;
			cursor: pointer;
		}
		#content {
			width:calc(100% - 10px);
			height:calc(100% - 140px - 10px);
			padding:5px;
			overflow:auto;
			border-top:1px solid #89898980;
		}
		#content #hidden,
		#content .tempselect {
			display:none;
		}
		body,
		div,
		button,
		div[data-id="dept"] button,
		div[data-id="items"] input,
		div[data-id="items"] select {
			font-family: Consolas,Microsoft JhengHei,sans-serif;
			font-size:18px;
		}
		div[data-id="dept"] {
			overflow:hidden;
			margin:5px 0;
			padding:5px;
			border:1px solid #89898980;
			border-radius:5px;
			position:relative;
		}
		div[data-id="dept"] .plus {
			position: absolute;
			bottom:5px;
			right:5px;
		}
		div[data-id="deptname"] {
			width: 100px;
			height:100%;
			float:left;
		}
		div[data-id="items"] {
			width:calc(100% - 100px);
			float:left;
			margin:30px 0 0 0;
		}
		div[data-id="items"] input {
			text-align:right;
		}
		div[data-id="items"] .item {
			margin:5px 0;
		}
		div[data-id="subtotal"] {
			width:calc(100% - 100px);
			float:right;
		}
		div[data-id="subtotal"] span[class="subtotal[]"] {
			margin:0 3px;
			padding:0 10px;
			border-bottom:1px solid #000000;
		}
	</style>
	<script>
		$(document).ready(function(){
			systemmessage=$('.systemmessage').dialog({
				autoOpen:false,
				width:300,
				height:150,
				title:'系統訊息',
				resizable:false,
				modal:true,
				draggable:false
			});
			var dotloading='';
			$('div[data-id="dept"] .plus').click(function(){
				$(this).parents('div[data-id="dept"]').find('div[data-id="items"]').append('<div class="item"> '+(parseInt($(this).parents('div[data-id="dept"]').find('div[data-id="items"] select[id="no[]"]').length)+1)+'. <select class="itemno" id="no[]">'+$('#content .tempselect').html()+'</select> 含稅價=<input type="hidden" name="no[]"><input type="hidden" name="type[]" value=""><input type="hidden" name="name[]" value=""><input type="hidden" name="mname[]"><input type="hidden" name="taste[]"><input type="hidden" name="tastename[]"><input type="hidden" name="tastenumber[]"><input type="hidden" name="number[]" value="1"><input class="money" type="text" name="money[]" value="0"> </div>');
				$(this).parents('div[data-id="dept"]').find('div[data-id="items"] select[id="no[]"]:eq('+(parseInt($(this).parents('div[data-id="dept"]').find('div[data-id="items"] select[id="no[]"]').length)-1)+')').focus();
			});
			$('div[data-id="items"]').on('change','.item select[id="no[]"]',function(){
				if($(this).find('option:selected').val()==''){
					$(this).parents('.item').find('input[name="no[]"]').val('');
					$(this).parents('.item').find('input[name="type[]"]').val('');
					$(this).parents('.item').find('input[name="name[]"]').val('');
					$(this).parents('.item').find('input[name="money[]"]').val('0');
					$(this).parents('.item').find('input[name="money[]"]').trigger('change');
				}
				else{
					var data=$(this).find('option:selected').val().split('-');
					//console.log(data);
					$(this).parents('.item').find('input[name="no[]"]').val(data[0]);
					$(this).parents('.item').find('input[name="type[]"]').val(data[1]);
					$(this).parents('.item').find('input[name="name[]"]').val($(this).find('option:selected').text());
				}
			});
			$('div[data-id="items"]').on('change','.item input[name="money[]"]',function(){
				var items=$(this).parents('div[data-id="items"]').find('.item').length;//產品數量
				//console.log('item='+items);
				$(this).parents('div[data-id="dept"]').find('div[data-id="subtotal"] span[class="subtotal[]"]').html('0');
				for(var i=0;i<items;i++){
					if($(this).parents('div[data-id="items"]').find('.item:eq('+i+') select[id="no[]"] option:selected').val()!=''){
						$(this).parents('div[data-id="dept"]').find('div[data-id="subtotal"] span[class="subtotal[]"]').html(parseInt($(this).parents('div[data-id="dept"]').find('div[data-id="subtotal"] span[class="subtotal[]"]').html())+parseInt($(this).parents('div[data-id="items"]').find('.item:eq('+i+') input[name="money[]"]').val()));
					}
					else{
					}
				}
				$(this).parents('div[data-id="dept"]').find('div[data-id="subtotal"] input[name="subtotal[]"]').val($(this).parents('div[data-id="dept"]').find('div[data-id="subtotal"] span[class="subtotal[]"]').html());
			});
			$('#title .send').click(function(){
				$.ajax({
					url:'./savecsv.php',
					method:'post',
					data:$('div[data-id="dept"] :input').serialize(),
					dataType:'html',
					success:function(d){
						//console.log(d);
					}
				});
				for(var i=0;i<$('div[data-id="dept"]').length;i++){
					if(parseInt($('div[data-id="dept"]:eq('+i+') div[data-id="subtotal"] span[class="subtotal[]"]').html())>0){//總計金額必須大於0
						var data=$('div[data-id="dept"]:eq('+i+') :input').serialize();
						$.ajax({
							url:'./getdata.php',
							method:'post',
							async:false,
							data:data,
							dataType:'html',
							success:function(d){
								console.log(d);
							},
							error:function(e){
								console.log(e);
							}
						});
					}
					else{
					}
				}
			});
			$('#title .reset').click(function(){
				$('select[id="no[]"] option').prop('selected',false);
				$('select[id="no[]"] option:eq(0)').prop('selected',true);
				$('input[name="money[]"]').val('0');
				$('span[class="subtotal[]"]').html('0');
			});
		});
	</script>
</head>
<body>
	<div id='title'>
		<h1>批量發票</h1>
		<button class='send'>開立發票</button>
		<button class='reset'>重設</button>
	</div>
	<div id='content'>
	<?php
	
	$csv=array();
	$file = fopen('mycsv.csv', 'r');
	$index=0;
	while (($line = fgetcsv($file)) !== FALSE) {
		if($index){
			array_push($csv,$line);//[0=>統編,1=>抬頭,2=>總計,3=>產品編號,4=>含稅價,5=>產品編號.....]
		}
		else{
			$index++;
		}
	}
	fclose($file);
	$banlist=array_column($csv,0);
	
	include_once '../../tool/dbTool.inc.php';
	$setup=parse_ini_file('../../database/setup.ini',true);
	$menu=parse_ini_file('../../database/'.$setup['basic']['company'].'-menu.ini',true);
	$conn=sqlconnect('../../database','menu.db','','','','sqlite');
	$sql='SELECT inumber,fronttype FROM itemsdata WHERE state=1 OR state IS NULL';
	$tempdata=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$itemdata=array();
	if(isset($tempdata[0]['inumber'])){
		echo '<div id="hidden">';
		foreach($tempdata as $tp){
			$itemdata[$tp['inumber']]['fronttype']=$tp['fronttype'];
			echo '<input type="hidden" name="no[]" value="'.$tp['inumber'].'"><input type="hidden" class="'.$tp['inumber'].'" value="'.$tp['fronttype'].'">';
		}
		echo '</div>';
	}
	else{
	}

	$items='<option value="" selected>選擇產品</option>';
	foreach($menu as $no=>$m){
		if($m['state']=='1'){
			$items.='<option value="'.$no.'-'.$itemdata[$no]['fronttype'].'">'.$m['name1'].'</option>';
		}
		else{
		}
	}
	echo '<div class="tempselect">'.$items.'</div>';

	$PostData = array(
		"company" =>$setup['basic']['company'],
		"dep"=>$setup['basic']['story']
	);
	
	//print_r($PostData);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://api.tableplus.com.tw/outposandorder/demopos/lib/api/ourmember/getmemlist.php');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	// Edit: prior variable $postFields should be $postfields;
	curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	$Result = curl_exec($ch);
	if(curl_errno($ch) !== 0) {
		print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
	}
	curl_close($ch);
	$memlist = json_decode($Result,true);

	foreach($memlist as $m){
		echo '<div class="'.$m['memno'].'" data-id="dept">
				<div data-id="deptname">'.$m['companynumber'].'<br>'.$m['name'].'<input type="hidden" name="memno[]" value="'.$m['memno'].'"><input type="hidden" class="companynumber[]" name="tempban[]" value="'.$m['companynumber'].'"><input type="hidden" name="tempbanname[]" value="'.$m['name'].'"></div>
				<div data-id="items"><input type="hidden" name="no[]" value="start"><input type="hidden" name="type[]" value="start"><input type="hidden" name="name[]" value="start"><input type="hidden" name="mname[]" value="start"><input type="hidden" name="taste[]" value="start"><input type="hidden" name="tastename[]" value="start"><input type="hidden" name="tastenumber[]" value="start"><input type="hidden" name="number[]" value="start"><input type="hidden" name="money[]" value="start">';
				$subtotal=0;
				if(in_array($m['companynumber'],$banlist)){
					$index=array_search($m['companynumber'],$banlist);
					if(isset($csv[$index][3])){
						for($i=3;$i<sizeof($csv[$index]);$i=$i+2){
							if($csv[$index][$i]!=''){
								echo '<div class="item">
										'.intval($i/2).'. <select class="itemno" id="no[]">';
										echo '<option value="">選擇產品</option>';
										$temphtml='';
										foreach($menu as $no=>$data){
											if($data['state']=='1'){
												echo '<option value="'.$no.'-'.$itemdata[$no]['fronttype'].'"';
												if($no==$csv[$index][$i]){
													$temphtml='<input type="hidden" name="no[]" value="'.$no.'"><input type="hidden" name="type[]" value="'.$itemdata[$no]['fronttype'].'"><input type="hidden" name="name[]" value="'.$data['name1'].'">';
													echo 'selected';
													$subtotal=floatval($subtotal)+floatval($csv[$index][$i+1]);
												}
												else{
												}
												echo '>'.$data['name1'].'</option>';
											}
											else{
											}
										}

									echo '</select> 含稅價='.$temphtml.'<input type="hidden" name="mname[]"><input type="hidden" name="taste[]"><input type="hidden" name="tastename[]"><input type="hidden" name="tastenumber[]"><input type="hidden" name="number[]" value="1"><input class="money" type="text" name="money[]" value="'.$csv[$index][$i+1].'">
									</div>';
							}
							else{
							}
						}
					}
					else{
						echo '<div class="item">
								1. <select class="itemno" id="no[]" style="font-size:">'.$items.'</select> 含稅價=<input type="hidden" name="no[]"><input type="hidden" name="type[]"><input type="hidden" name="name[]"><input type="hidden" name="mname[]"><input type="hidden" name="taste[]"><input type="hidden" name="tastename[]"><input type="hidden" name="tastenumber[]"><input type="hidden" name="number[]" value="1"><input class="money" type="text" name="money[]" value="0">
							</div>';
					}
				}
				else{
					echo '<div class="item">
							1. <select class="itemno" id="no[]" style="font-size:">'.$items.'</select> 含稅價=<input type="hidden" name="no[]"><input type="hidden" name="type[]"><input type="hidden" name="name[]"><input type="hidden" name="mname[]"><input type="hidden" name="taste[]"><input type="hidden" name="tastename[]"><input type="hidden" name="tastenumber[]"><input type="hidden" name="number[]" value="1"><input class="money" type="text" name="money[]" value="0">
						</div>';
				}
			echo '</div>
				<button class="plus">增加</button>
				<div data-id="subtotal">
					總計：<span class="subtotal[]">'.$subtotal.'</span><input type="hidden" name="subtotal[]" value="'.$subtotal.'">元
				</div>
			</div>';
	}
	?>
	</div>
	<div class='systemmessage'>開立發票中<span>.</span></div>
</body>
</html>