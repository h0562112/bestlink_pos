<script>
$(document).ready(function(){
	$("#rearbox.select_box").click(function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
	 $("#rearmod #rearbox .option a").click(function(){
        var value=$(this).text();
		var index=$('.option a').index(this);
        $(this).parent().siblings(".select_txt").text(value);
        $("input[name='rear']#select_value").val($('.option a:eq('+index+')').attr('id'));
		$('#item #edititem #save').prop('disabled',false);
    });
    $("#frontbox.select_box").click(function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
    $(document).click(function(event){
        var eo=$(event.target);
        if($(".select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('.option').hide();
    });
    /*赋值给文本框*/
    $("#frontmod #frontbox .option a").click(function(){
        var value=$(this).text();
		var index=$('.option a').index(this);
        $(this).parent().siblings(".select_txt").text(value);
        $("input[name='front']#select_value").val($('.option a:eq('+index+')').attr('id'));
		$('#item #edititem #save').prop('disabled',false);
    });
	$("#prlibox.select_box").click(function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
    /*$(document).click(function(event){
        var eo=$(event.target);
        if($(".select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('.option').hide();
    });*/
    /*赋值给文本框*/
    $("#prlimod #prlibox .option a").click(function(){
        var value=$(this).text();
		var index=$('.option a').index(this);
        $(this).parent().siblings(".select_txt").text(value);
        $("input[name='printtype']#select_value").val($('.option a:eq('+index+')').attr('id'));
    });
	$("#kdsbox.select_box").click(function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
    /*$(document).click(function(event){
        var eo=$(event.target);
        if($(".select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('.option').hide();
    });*/
    /*赋值给文本框*/
    $("#kdsmod #kdsbox .option a").click(function(){
        var value=$(this).text();
		var index=$('.option a').index(this);
        $(this).parent().siblings(".select_txt").text(value);
        $("input[name='kds']#select_value").val($('.option a:eq('+index+')').attr('id'));
		$.ajax({
			url:'./lib/js/getgrouplist.ajax.php',
			method:'post',
			async:false,
			data:{'company':$('#itemform input[name="company"]').val(),'dep':$('#itemform input[name="dep"]').val(),'partition':$('.option a:eq('+index+')').attr('id')},
			dataType:'html',
			success:function(d){
				$('#kdsgroupmod #kdsgroupbox').html(d);
				$('#kdsgroupmod input[name="kdsgroup"]').val('');
				//console.log(d);
			},
			error:function(e){
				console.log(e);
			}
		});
    });
	$("#kdsgroupbox.select_box").click(function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
    /*$(document).click(function(event){
        var eo=$(event.target);
        if($(".select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('.option').hide();
    });*/
    /*赋值给文本框*/
    $("#kdsgroupmod #kdsgroupbox").on('click','.option a',function(){
        var value=$(this).text();
		var index=$('.option a').index(this);
        $(this).parent().siblings(".select_txt").text(value);
        $("input[name='kdsgroup']#select_value").val($('.option a:eq('+index+')').attr('id'));
    });
	$("#unitbox.select_box").click(function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
    /*$(document).click(function(event){
        var eo=$(event.target);
        if($(".select_box").is(":visible") && eo.attr("class")!="option" && !eo.parent(".option").length)
            $('.option').hide();
    });*/
    /*赋值给文本框*/
    $("#unitmod #unitbox .option a").click(function(){
        var value=$(this).text();
		var index=$('.option a').index(this);
        $(this).parent().siblings(".select_txt").text(value);
        $("input[name='unit']#select_value").val($('.option a:eq('+index+')').attr('id'));
		$('.unit').html(value);
    });
	$('#strawbox.select_box').click(function(event){
		event.stopPropagation();
        $(this).find(".option").toggle();
        $(this).parent().siblings().find(".option").hide();
    });
	/*赋值给文本框*/
    $('#strawmod #strawbox .option a').click(function(){
		var value=$(this).text();
		var index=$('.option a').index(this);
        $(this).parent().siblings(".select_txt").text(value);
        $("input[name='straw']#select_value").val($('.option a:eq('+index+')').attr('id'));
		$('.straw').html(value);
    });
	$('#tastediv input[class^=g]').click(function(){
		if($(this).prop('checked')==true){
			$('#tastediv input[class="'+$(this).prop('class').substr(1)+'"]').prop('checked',true);
		}
		else{
			$('#tastediv input[class="'+$(this).prop('class').substr(1)+'"]').prop('checked',false);
		}
	});
	$('#tastediv input[name="taste[]"]').click(function(){
		if($(this).prop('checked')==true&&$('#tastediv input[class="'+$(this).prop('class')+'"]:checked').length==$('#tastediv input[class="'+$(this).prop('class')+'"]').length){
			$('#tastediv input[class="g'+$(this).prop('class')+'"]').prop('checked',true);
		}
		else if($(this).prop('checked')==false){
			$('#tastediv input[class="g'+$(this).prop('class')+'"]').prop('checked',false);
		}
		else{
		}
	});
});
</script>
<style>
.hide { 
	display: none; 
}
input { 
	outline: none;
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
<?php
function quicksort($origArray,$type) {//快速排序//for最低價、最高價
	if (sizeof($origArray) == 1) { 
		return $origArray;
	}
	else if(sizeof($origArray) == 0){
		return 'null';
	}
	else {
		$left = array();
		$right = array();
		$newArray = array();
		$pivot = array_pop($origArray);
		$length = sizeof($origArray);
		for ($i = 0; $i < $length; $i++) {
			if(isset($origArray[$i][$type])&&isset($pivot[$type])){
				if (floatval($origArray[$i][$type]) <= floatval($pivot[$type])) {
					array_push($left,$origArray[$i]);
				} else {
					array_push($right,$origArray[$i]);
				}
			}
			else if(isset($origArray[$i][$type])){
				array_push($right,$origArray[$i]);
			}
			else{
				array_push($left,$origArray[$i]);
			}
		}
		if(sizeof($left)==0){
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort($right,$type);
				$newArray=array_merge(array($pivot),$tempright);
			}
		}
		else{
			$templeft=quicksort($left,$type);
			$n=sizeof($templeft);
			$start=$n;
			$newArray=array_merge($templeft,array($pivot));
			if(sizeof($right)==0){
			}
			else{
				$tempright=quicksort($right,$type);
				$newArray=array_merge($newArray,$tempright);
			}
		}
		return $newArray;
	}
}
if(isset($_POST['lan'])&&$_POST['lan']!=''){
	if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
		$interface=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('../../lan/interfaceTW.ini')){
		$interface=parse_ini_file('../../lan/interfaceTW.ini',true);
	}
	else{
		$interface='-1';
	}
}
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
if(isset($_POST['number'])){
	include_once '../../../tool/dbTool.inc.php';
	include_once '../../../tool/create.prime.php';
	$itemdep=$_POST['itemdep'];
	$number=$_POST['number'];
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	$conn=sqlconnect('../../../menudata/'.$company.'/'.$dep,'menu.db','','','','sqlite');
	$sql='SELECT isgroup,childtype,frontsq,quickorder,reartype,taste FROM itemsdata WHERE fronttype="'.$itemdep.'" AND inumber="'.$number.'"';
	$items=sqlquery($conn,$sql,'sqlite');
	$sql='SELECT fronttype FROM itemsdata WHERE fronttype LIKE "g%"';
	$groupfront=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(sizeof($items)>0){
		$isgroup=preg_split('/-/',$items[0]['isgroup']);
	}
	else{
	}
	$childgroup=preg_split('/;/',$items[0]['childtype']);
	$printlist=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/printlisttag.ini',true);
	$frontname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-front.ini',true);
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-rear.ini')){
		$rearname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-rear.ini',true);
	}
	else{
		$rearname='-1';
	}
	$itemname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-menu.ini',true);
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/initsetting.ini')){
		$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/initsetting.ini',true);
	}
	else{
	}
	$floorspend=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/floorspend.ini',true);
	$unit=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/unit.ini',true);
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/stock.ini')){
		$stock=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/stock.ini',true);
	}
	else{
		$stock='-1';
	}
	if(file_exists('../../../menudata/editdisabled.ini')){
		$editdisabled=parse_ini_file('../../../menudata/editdisabled.ini',true);
	}
	else{
		$editdisabled='-1';
	}
?>
<script>
$(document).ready(function(){
	$("#color1").colorpicker({
		color:"<?php if(isset($itemname[$number]['color1']))echo $itemname[$number]['color1'];else echo '#000000'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$("#color2").colorpicker({
		color:"<?php if(isset($itemname[$number]['color2']))echo $itemname[$number]['color2'];else echo '#898989'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#bgcolor').colorpicker({
		color:"<?php if(isset($itemname[$number]['bgcolor']))echo $itemname[$number]['bgcolor']; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor1').colorpicker({
		color:"<?php if(isset($itemname[$number]['introcolor1']))echo $itemname[$number]['introcolor1'];else echo '#000000'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor2').colorpicker({
		color:"<?php if(isset($itemname[$number]['introcolor2']))echo $itemname[$number]['introcolor2'];else echo '#000000'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor3').colorpicker({
		color:"<?php if(isset($itemname[$number]['introcolor3']))echo $itemname[$number]['introcolor3'];else echo '#000000'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor4').colorpicker({
		color:"<?php if(isset($itemname[$number]['introcolor4']))echo $itemname[$number]['introcolor4'];else echo '#000000'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor5').colorpicker({
		color:"<?php if(isset($itemname[$number]['introcolor5']))echo $itemname[$number]['introcolor5'];else echo '#000000'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor6').colorpicker({
		color:"<?php if(isset($itemname[$number]['introcolor6']))echo $itemname[$number]['introcolor6'];else echo '#000000'; ?>",
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
});
</script>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?></center></h1>
<div id='fun' style='width:100%;float:left;'>
	<input id='save' name='submit' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='pre' class="initbutton" type='button' value='上一筆'>
	<input id='next' class="initbutton" type='button' value='下一筆'>
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<div style='width:100%;overflow:hidden;'>
	<form id='itemform' enctype='multipart/form-data' style='overflow:hidden;'>
		<input type='hidden' name='itemdep' value='<?php echo $itemdep; ?>'>
		<input type='hidden' name='number' value='<?php echo $number; ?>'>
		<input type='hidden' name='company' value='<?php echo $company; ?>'>
		<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
		<table style='float:left;'>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemnumber']))echo $interface['name']['itemnumber'];else echo '編號'; ?></td>
				<td><input type="text" value=<?php echo $number; ?> readonly></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['typelabel']))echo $interface['name']['typelabel'];else echo '類別'; ?></td>
				<td>
					<div class="mod_select" id='frontmod'>
						<ul>
							<li>
								<div class="select_box" id='frontbox'>
									<?php
									$option='';
									$tempoption='';
									for($i=0;$i<sizeof($frontname);$i++){
										if($frontname[$itemdep]['state']=='0'){//該產品的類別已停用，顯示所有類別(含已停用)2020/1/2阿志提出
											if($frontname[$i]['state']=='0'){
												if($itemdep==$i){
													$tempoption='<span class="select_txt">'.$frontname[$itemdep]['name1'].'(<font color="#ff0000">';
													if($interface!='-1'&&isset($interface['name']['stopstate']))$tempoption=$tempoption.$interface['name']['stopstate'];
													else $tempoption=$tempoption.'停用';
													$option=$tempoption.'</font>)/'.$frontname[$itemdep]['name2'].'</span><a class="selet_open">▼</a><div class="option">'.$option;

													/*$checked='<span class="select_txt">'.$frontname[$itemdep]['name1'].'(<font color="#ff0000">';
													if($interface!='-1'&&isset($interface['name']['stopstate']))$option=$option.$interface['name']['stopstate'];
													else $option=$option.'停用';
													$option=$option.'</font>)/'.$frontname[$itemdep]['name2'].'</span><a class="selet_open">▼</a>';*/
												}
												else{
												}
												$option=$option.'<a id="'.$i.'-'.$frontname[$i]['seq'].'">'.$frontname[$i]['name1'].'(<font color="#ff0000">';if($interface!='-1'&&isset($interface['name']['stopstate']))$option=$option.$interface['name']['stopstate'];else $option=$option.'停用';$option=$option.'</font>)/'.$frontname[$i]['name2'].'</a>';
											}
											else{
												if($itemdep==$i){
													$option='<span class="select_txt">'.$frontname[$itemdep]['name1'].'/'.$frontname[$itemdep]['name2'].'</span><a class="selet_open">▼</a><div class="option">'.$option;
													//$checked='<span class="select_txt">'.$frontname[$itemdep]['name1'].'/'.$frontname[$itemdep]['name2'].'</span><a class="selet_open">▼</a>';
												}
												else{
												}
												$option=$option.'<a id="'.$i.'-'.$frontname[$i]['seq'].'">'.$frontname[$i]['name1'].'/'.$frontname[$i]['name2'].'</a>';
											}
										}
										else{//該產品的類別未被停用，隱藏所有已停用的類別2020/1/2阿志提出
											if($frontname[$i]['state']=='0'){
												
											}
											else{
												if($itemdep==$i){
													$option='<span class="select_txt">'.$frontname[$itemdep]['name1'].'/'.$frontname[$itemdep]['name2'].'</span><a class="selet_open">▼</a><div class="option">'.$option;
													//$checked='<span class="select_txt">'.$frontname[$itemdep]['name1'].'/'.$frontname[$itemdep]['name2'].'</span><a class="selet_open">▼</a>';
												}
												else{
												}
												$option=$option.'<a id="'.$i.'-'.$frontname[$i]['seq'].'">'.$frontname[$i]['name1'].'/'.$frontname[$i]['name2'].'</a>';
											}
										}
									}
									$option=$option.'</div>';
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='front' id="select_value" value='<?php echo $itemdep.'-'.$frontname[$itemdep]['seq']; ?>'>
					</div>
				</td>
			</tr>
		<?php
		if($rearname!='-1'){
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['anatype']))echo $interface['name']['anatype'];else echo '分析類別'; ?></td>
				<td>
					<div class="mod_select" id='rearmod'>
						<ul>
							<li>
								<div class="select_box" id='rearbox'>
								
									<?php
									$option='';
									$tempoption='';
									for($i=0;$i<sizeof($rearname);$i++){
										if($rearname[$i]['state']=='0'){
											if($items[0]['reartype']==$i){
												
												$tempoption='<span class="select_txt">'.$rearname[$items[0]['reartype']]['name'].'(<font color="#ff0000">';
												if($interface!='-1'&&isset($interface['name']['stopstate']))$tempoption=$tempoption.$interface['name']['stopstate'];
												else $tempoption=$tempoption.'停用';
												$option=$tempoption.'</font>)</span><a class="selet_open">▼</a><div class="option">'.$option;

												/*$checked='<span class="select_txt">'.$rearname[$items[0]['reartype']]['name'].'(<font color="#ff0000">';if($interface!='-1'&&isset($interface['name']['stopstate']))$option=$option.$interface['name']['stopstate'];else $option=$option.'停用';$option=$option.'</font>)</span><a class="selet_open">▼</a>';*/
											}
											else{
											}
											$option=$option.'<a id="'.$i.'">'.$rearname[$i]['name'].'(<font color="#ff0000">';if($interface!='-1'&&isset($interface['name']['stopstate']))$option=$option.$interface['name']['stopstate'];else $option=$option.'停用';$option=$option.'</font>)</a>';
										}
										else{
											if($items[0]['reartype']==$i){
												$option='<span class="select_txt">'.$rearname[$items[0]['reartype']]['name'].'</span><a class="selet_open">▼</a><div class="option">'.$option;
												//$checked='<span class="select_txt">'.$rearname[$items[0]['reartype']]['name'].'</span><a class="selet_open">▼</a>';
											}
											else{
											}
											$option=$option.'<a id="'.$i.'">'.$rearname[$i]['name'].'</a>';
										}
									}
									$option=$option.'</div>';
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='rear' id="select_value" value='<?php echo $items[0]['reartype']; ?>'>
					</div>
				</td>
			</tr>
		<?php
		}
		else{
		?>
			<input type="hidden" name='rear' id="select_value" value='0'>
		<?php
		}
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['printmenu']))echo $interface['name']['printmenu'];else echo '列印類別'; ?></td>
				<td>
					<div class="mod_select" id='prlimod'>
						<ul>
							<li>
								<div class="select_box" id='prlibox'>
									<?php
									$prlist=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/itemprinttype.ini',true);
									$option='';
									foreach($prlist as $k=>$v){
										$option=$option.'<a id="'.$k.'">'.$v['name'].'</a>';
									}
									if($itemname[$number]['printtype']==''){
										$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">'.$option;
									}
									else{
										$option='<span class="select_txt">'.$prlist[$itemname[$number]['printtype']]['name'].'</span><a class="selet_open">▼</a><div class="option">'.$option.'</div>';
									}
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='printtype' id="select_value" value='<?php echo $itemname[$number]['printtype']; ?>'>
					</div>
				</td>
			</tr>
		<?php
		if(isset($initsetting['init']['kds'])&&$initsetting['init']['kds']=='1'){
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['setkds']))echo $interface['name']['setkds'];else echo '廚房控餐設定'; ?></td>
			</tr>
			<tr>
				<td style='padding:14px 0;'><?php if($interface!='-1'&&isset($interface['name']['partition']))echo $interface['name']['partition'];else echo '區域'; ?></td>
				<td>
					<div class="mod_select" id='kdsmod'>
						<ul>
							<li>
								<div class="select_box" id='kdsbox'>
									<?php
									if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini')){
										$kds=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini',true);
										$option='';
										if(isset($kds['type']['name'])){
											foreach($kds['type']['name'] as $k=>$v){
												$option=$option.'<a id="'.$k.'">'.$v.'</a>';
											}
											if(!isset($itemname[$number]['kds'])||$itemname[$number]['kds']==''){
												$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">'.$option;
											}
											else{
												$option='<span class="select_txt">'.$kds['type']['name'][$itemname[$number]['kds']].'</span><a class="selet_open">▼</a><div class="option">'.$option.'</div>';
											}
											echo $option;
										}
										else{
										}
									}
									else{
									}
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='kds' id="select_value" value='<?php if(isset($itemname[$number]['kds']))echo $itemname[$number]['kds']; ?>'>
					</div>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;'><?php if($interface!='-1'&&isset($interface['name']['groupofpt']))echo $interface['name']['groupofpt'];else echo '群組'; ?></td>
				<td>
					<div class="mod_select" id='kdsgroupmod'>
						<ul>
							<li>
								<div class="select_box" id='kdsgroupbox'>
									<?php
									if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini')){
										$kds=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini',true);
										$option='';
										if(isset($kds['type']['name'])&&isset($itemname[$number]['kds'])&&$itemname[$number]['kds']!=''){
											foreach($kds['group'.$itemname[$number]['kds']]['name'] as $k=>$v){
												$option=$option.'<a id="'.$k.'">'.$v.'</a>';
											}
											if((!isset($itemname[$number]['kds'])||$itemname[$number]['kds']=='')||(!isset($itemname[$number]['kdsgroup'])||$itemname[$number]['kdsgroup']=='')){
												$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">'.$option;
											}
											else{
												$option='<span class="select_txt">'.$kds['group'.$itemname[$number]['kds']]['name'][$itemname[$number]['kdsgroup']].'</span><a class="selet_open">▼</a><div class="option">'.$option.'</div>';
											}
											echo $option;
										}
										else{
										}
									}
									else{
									}
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='kdsgroup' id="select_value" value='<?php if(isset($itemname[$number]['kdsgroup']))echo $itemname[$number]['kdsgroup']; ?>'>
					</div>
				</td>
			</tr>
		<?php
		}
		else{
		}
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['viewseq']))echo $interface['name']['viewseq'];else echo '顯示順序'; ?></td>
				<td><input type='tel' class='seq' name='seq' value='<?php if(isset($items[0]['frontsq']))echo $items[0]['frontsq'];else echo '1'; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['quicknumberlabel']))echo $interface['name']['quicknumberlabel'];else echo '快點代碼(限定數字)'; ?></td>
				<td><input type='text' class='quickorder' name='quickorder' value='<?php if(isset($items[0]['quickorder']))echo $items[0]['quickorder'];else echo ''; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemnamelabel']))echo $interface['name']['itemnamelabel'];else echo '產品名稱'; ?></td>
				<td></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mainname']))echo $interface['name']['mainname'];else echo '主語言'; ?></td>
				<td><input type='text' class='mainname' name='name1' value='<?php echo $itemname[$number]['name1']; ?>' <?php if(isset($editdisabled[$company]))echo 'readonly'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontsize']))echo $interface['name']['mainfontsize'];else echo '字體大小'; ?></td>
				<td><input type='tel' name='size1' value='<?php if(isset($itemname[$number]['size1']))echo $itemname[$number]['size1'];else echo '14'; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontcolor']))echo $interface['name']['mainfontcolor'];else echo '字體顏色'; ?></td>
				<td><input id='color1' name='color1'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontweight']))echo $interface['name']['mainfontweight'];else echo '是否粗體'; ?></td>
				<td><input type='checkbox' name='bold1' <?php if(isset($itemname[$number]['bold1'])&&$itemname[$number]['bold1']=="1")echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['secname']))echo $interface['name']['secname'];else echo '次語言'; ?></td>
				<td><input type='text' name='name2' value='<?php echo $itemname[$number]['name2']; ?>' <?php if(isset($editdisabled[$company]))echo 'readonly'; ?>></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['secfontsize']))echo $interface['name']['secfontsize'];else echo '字體大小'; ?></td>
				<td><input type='tel' name='size2' value='<?php if(isset($itemname[$number]['size2']))echo $itemname[$number]['size2'];else echo '14'; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['secfontcolor']))echo $interface['name']['secfontcolor'];else echo '字體顏色'; ?></td>
				<td><input id='color2' name='color2'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['secfontweight']))echo $interface['name']['secfontweight'];else echo '是否粗體'; ?></td>
				<td><input type='checkbox' name='bold2' <?php if(isset($itemname[$number]['bold2'])&&$itemname[$number]['bold2']=="1")echo 'checked'; ?>></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['bgcolor']))echo $interface['name']['bgcolor'];else echo '按鈕底色'; ?></td>
				<td><input id='bgcolor' name='bgcolor'></td>
			</tr>
			<tr>
				<td colspan='2'>
					<?php if($interface!='-1'&&isset($interface['name']['tempview']))echo $interface['name']['tempview'];else echo '檢視效果(僅供參考)'; ?>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<button style='min-width:261px;max-width:261px;height:100px;text-align:center;font-family:Consolas,Microsoft JhengHei,sans-serif;background-color:<?php if(isset($itemname[$number]['bgcolor']))echo $itemname[$number]['bgcolor'];else echo '#84FEFF'; ?>;border: 1px solid #898989;border-radius: 5px;overflow:hidden;' disabled>
						<div id='name1' style='font-size:<?php if(isset($itemname[$number]['size1']))echo $itemname[$number]['size1'].'px';else echo '14px'; ?>;color:<?php if(isset($itemname[$number]['color1']))echo $itemname[$number]['color1'];else echo '#000000'; ?>;font-weight:<?php if(isset($itemname[$number]['bold1'])&&$itemname[$number]['bold1']==1)echo 'bold';else echo 'normal'; ?>;'>
							<?php
							echo $itemname[$number]['name1'];
							?>
						</div>
						<div id='name2' style='font-size:<?php if(isset($itemname[$number]['size2']))echo $itemname[$number]['size2'].'px';else echo '14px;'; ?>;color:<?php if(isset($itemname[$number]['color2']))echo $itemname[$number]['color2'];else echo '#898989' ?>;font-weight:<?php if(isset($itemname[$number]['bold2'])&&$itemname[$number]['bold2']==1)echo 'bold';else echo 'normal'; ?>;'>
							<?php
							echo $itemname[$number]['name2'];
							?>
						</div>
					</button>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname1label']))echo $interface['name']['mname1label'];else echo '價格一'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname11' value='<?php echo $itemname[$number]['mname11']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname12' value='<?php echo $itemname[$number]['mname12']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money1' value='<?php echo $itemname[$number]['money1']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit1' value='<?php if(isset($itemname[$number]['unit1']))echo $itemname[$number]['unit1'];else echo '1'; ?>'><span class='unit'><?php if(isset($unit['unit'])&&isset($unit['unit'][$itemname[$number]['unit']]))echo $unit['unit'][$itemname[$number]['unit']];else if(isset($unit['unit'][0]))echo $unit['unit'][0];else if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup' <?php if(isset($isgroup[0])&&$isgroup[0]=='1')echo 'checked';?>></td>
			</tr> -->
			<tr id='subtype' <?php if(isset($isgroup[0])&&$isgroup[0]=='1');else echo "style='display:none;'"; ?>>
				<td>附餐類別</td>
				<td>
					<div class="mod_select">
						<ul>
							<li>
								<div class="select_box">
									<!-- <?php
									if(isset($childgroup[0])){
										$childtype=preg_split('/,/',$childgroup[0]);
										foreach($groupfront as $gf){
											if(in_array($gf['front'],$childtype)){
												echo "<option selected>".$frontname[$gf['front']]['name']."</option>";
											}
											else{
												echo "<option>".$frontname[$gf['front']]['name']."</option>";
											}
										}
									}
									else{
										foreach($groupfront as $gf){
											echo "<option>".$frontname[$gf['front']]['name']."</option>";
										}
									}
									?> -->
									<span class="select_txt">1</span><a class="selet_open">▼</a>
									<div class="option">
										<a>1</a>
										<a>2</a>
										<a>3</a>
									</div>
								</div>
							</li>
						</ul>
						<input type="hidden" id="select_value" />
					</div>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname2label']))echo $interface['name']['mname2label'];else echo '價格二'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname21' value='<?php echo $itemname[$number]['mname21']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname22' value='<?php echo $itemname[$number]['mname22']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money2' value='<?php echo $itemname[$number]['money2']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit2' value='<?php if(isset($itemname[$number]['unit1']))echo $itemname[$number]['unit2'];else echo '1'; ?>'><span class='unit'><?php if(isset($unit['unit'])&&isset($unit['unit'][$itemname[$number]['unit']]))echo $unit['unit'][$itemname[$number]['unit']];else if(isset($unit['unit'][0]))echo $unit['unit'][0];else if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup' <?php if(isset($isgroup[1])&&$isgroup[1]=='1')echo 'checked';?>></td>
			</tr> -->
			<tr id='subtype' <?php if(isset($isgroup[1])&&$isgroup[1]=='1');else echo "style='display:none;'"; ?>>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					if(isset($childgroup[1])){
						$childtype=preg_split('/,/',$childgroup[1]);
						foreach($groupfront as $gf){
							if(in_array($gf['front'],$childtype)){
								echo "<option selected>".$frontname[$gf['front']]['name']."</option>";
							}
							else{
								echo "<option>".$frontname[$gf['front']]['name']."</option>";
							}
						}
					}
					else{
						foreach($groupfront as $gf){
							echo "<option>".$frontname[$gf['front']]['name']."</option>";
						}
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname3label']))echo $interface['name']['mname3label'];else echo '價格三'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname31' value='<?php echo $itemname[$number]['mname31']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname32' value='<?php echo $itemname[$number]['mname32']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money3' value='<?php echo $itemname[$number]['money3']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit3' value='<?php if(isset($itemname[$number]['unit1']))echo $itemname[$number]['unit3'];else echo '1'; ?>'><span class='unit'><?php if(isset($unit['unit'])&&isset($unit['unit'][$itemname[$number]['unit']]))echo $unit['unit'][$itemname[$number]['unit']];else if(isset($unit['unit'][0]))echo $unit['unit'][0];else if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup' <?php if(isset($isgroup[2])&&$isgroup[2]=='1')echo 'checked';?>></td>
			</tr> -->
			<tr id='subtype' <?php if(isset($isgroup[2])&&$isgroup[2]=='1');else echo "style='display:none;'"; ?>>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					if(isset($childgroup[2])){
						$childtype=preg_split('/,/',$childgroup[2]);
						foreach($groupfront as $gf){
							if(in_array($gf['front'],$childtype)){
								echo "<option selected>".$frontname[$gf['front']]['name']."</option>";
							}
							else{
								echo "<option>".$frontname[$gf['front']]['name']."</option>";
							}
						}
					}
					else{
						foreach($groupfront as $gf){
							echo "<option>".$frontname[$gf['front']]['name']."</option>";
						}
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname4label']))echo $interface['name']['mname4label'];else echo '價格四'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname41' value='<?php echo $itemname[$number]['mname41']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname42' value='<?php echo $itemname[$number]['mname42']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money4' value='<?php echo $itemname[$number]['money4']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit4' value='<?php if(isset($itemname[$number]['unit1']))echo $itemname[$number]['unit4'];else echo '1'; ?>'><span class='unit'><?php if(isset($unit['unit'])&&isset($unit['unit'][$itemname[$number]['unit']]))echo $unit['unit'][$itemname[$number]['unit']];else if(isset($unit['unit'][0]))echo $unit['unit'][0];else if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup' <?php if(isset($isgroup[3])&&$isgroup[3]=='1')echo 'checked';?>></td>
			</tr> -->
			<tr id='subtype' <?php if(isset($isgroup[3])&&$isgroup[3]=='1');else echo "style='display:none;'"; ?>>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					if(isset($childgroup[3])){
						$childtype=preg_split('/,/',$childgroup[3]);
						foreach($groupfront as $gf){
							if(in_array($gf['front'],$childtype)){
								echo "<option selected>".$frontname[$gf['front']]['name']."</option>";
							}
							else{
								echo "<option>".$frontname[$gf['front']]['name']."</option>";
							}
						}
					}
					else{
						foreach($groupfront as $gf){
							echo "<option>".$frontname[$gf['front']]['name']."</option>";
						}
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname5label']))echo $interface['name']['mname5label'];else echo '價格五'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname51' value='<?php echo $itemname[$number]['mname51']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname52' value='<?php echo $itemname[$number]['mname52']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money5' value='<?php echo $itemname[$number]['money5']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit5' value='<?php if(isset($itemname[$number]['unit1']))echo $itemname[$number]['unit5'];else echo '1'; ?>'><span class='unit'><?php if(isset($unit['unit'])&&isset($unit['unit'][$itemname[$number]['unit']]))echo $unit['unit'][$itemname[$number]['unit']];else if(isset($unit['unit'][0]))echo $unit['unit'][0];else if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup' <?php if(isset($isgroup[4])&&$isgroup[4]=='1')echo 'checked';?>></td>
			</tr> -->
			<tr id='subtype' <?php if(isset($isgroup[4])&&$isgroup[4]=='1');else echo "style='display:none;'"; ?>>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					if(isset($childgroup[4])){
						$childtype=preg_split('/,/',$childgroup[4]);
						foreach($groupfront as $gf){
							if(in_array($gf['front'],$childtype)){
								echo "<option selected>".$frontname[$gf['front']]['name']."</option>";
							}
							else{
								echo "<option>".$frontname[$gf['front']]['name']."</option>";
							}
						}
					}
					else{
						foreach($groupfront as $gf){
							echo "<option>".$frontname[$gf['front']]['name']."</option>";
						}
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname6label']))echo $interface['name']['mname6label'];else echo '價格六'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname61' value='<?php echo $itemname[$number]['mname61']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname62' value='<?php echo $itemname[$number]['mname62']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money6' value='<?php echo $itemname[$number]['money6']; ?>'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit6' value='<?php if(isset($itemname[$number]['unit1']))echo $itemname[$number]['unit6'];else echo '1'; ?>'><span class='unit'><?php if(isset($unit['unit'])&&isset($unit['unit'][$itemname[$number]['unit']]))echo $unit['unit'][$itemname[$number]['unit']];else if(isset($unit['unit'][0]))echo $unit['unit'][0];else if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup' <?php if(isset($isgroup[5])&&$isgroup[5]=='1')echo 'checked';?>></td>
			</tr> -->
			<tr id='subtype' <?php if(isset($isgroup[5])&&$isgroup[5]=='1');else echo "style='display:none;'"; ?>>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					if(isset($childgroup[5])){
						$childtype=preg_split('/,/',$childgroup[5]);
						foreach($groupfront as $gf){
							if(in_array($gf['front'],$childtype)){
								echo "<option selected>".$frontname[$gf['front']]['name']."</option>";
							}
							else{
								echo "<option>".$frontname[$gf['front']]['name']."</option>";
							}
						}
					}
					else{
						foreach($groupfront as $gf){
							echo "<option>".$frontname[$gf['front']]['name']."</option>";
						}
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['tagfontsize']))echo $interface['name']['tagfontsize'];else echo '貼紙產品文字大小'; ?></td>
				<td><input type='number' name='tagsize' style='text-align:right;' value='<?php if(isset($itemname[$number]['tagsize']))echo $itemname[$number]['tagsize'];else echo '12'; ?>'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td>
					<div class="mod_select" id='unitmod'>
						<ul>
							<li>
								<div class="select_box" id='unitbox'>
									<?php
									if(isset($itemname[$number]['unit'])){
										$option='';
										for($i=0;$i<sizeof($unit['unit']);$i++){
											$option=$option.'<a id="'.$i.'">'.$unit['unit'][$i].'</a>';
										}
										if($itemname[$number]['unit']==''){
											$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">'.$option;
										}
										else{
											$option='<span class="select_txt">'.$unit['unit'][$itemname[$number]['unit']].'</span><a class="selet_open">▼</a><div class="option">'.$option.'</div>';
										}
										echo $option;
									}
									else{
										if(isset($unit['unit'][0])){
											echo '<span class="select_txt">'.$unit['unit'][0].'</span><a class="selet_open">▼</a><div class="option"><a id="0">';if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份';echo '</a></div>';
										}
										else{
											echo '<span class="select_txt">';if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份';echo '</span><a class="selet_open">▼</a><div class="option"><a id="0">';if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份';echo '</a></div>';
										}
									}
									?>
								</div>
							</li>
						</ul>
						<?php
						if(isset($itemname[$number]['unit'])){
						?>
						<input type="hidden" name='unit' id="select_value" value='<?php echo $itemname[$number]['unit']; ?>'>
						<?php
						}
						else{
						?>
						<input type="hidden" name='unit' id="select_value" value='0'>
						<?php
						}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['strawlabel']))echo $interface['name']['strawlabel'];else echo '吸管設定'; ?></td>
				<td>
					<div class="mod_select" id='strawmod'>
						<ul>
							<li>
								<div class="select_box" id='strawbox'>
									<?php
									$straw=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/straw.ini',true);
									$option='';
									foreach($straw['straw'] as $k=>$v){
										$option=$option.'<a id="'.$k.'">'.$v.'</a>';
									}
									if(!isset($itemname[$number]['straw'])||$itemname[$number]['straw']==''){
										$option='<span class="select_txt">'.$straw['straw']['999'].'</span><a class="selet_open">▼</a><div class="option">'.$option;
									}
									else{
										$option='<span class="select_txt">'.$straw['straw'][$itemname[$number]['straw']].'</span><a class="selet_open">▼</a><div class="option">'.$option.'</div>';
									}
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='straw' id="select_value" value='<?php if(!isset($itemname[$number]['straw'])||$itemname[$number]['straw']=='')echo '999';else echo $itemname[$number]['straw']; ?>'>
					</div>
				</td>
			</tr>
		<?php
		if(isset($initsetting['init']['tastegroup'])&&$initsetting['init']['tastegroup']=='1'){
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['tastelabel']))echo $interface['name']['tastelabel'];else echo '專屬備註'; ?></td>
				<td style='border-top:1px solid #898989;border-bottom:1px solid #898989;border-radius:5px;' id='tastediv'>
					<?php
					$tastename=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-taste.ini',true);
					$sorttaste=quicksort($tastename,'seq');
					$tastegroup=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini',true);
					if(isset($items[0]['taste'])&&$items[0]['taste']!=''){
						$tasteno=preg_split('/;/',$items[0]['taste']);
						$tasteno=preg_split('/,/',$tasteno[1]);
					}
					else{
						$tasteno=array();
					}
					$taste=array();
					for($i=0;$i<sizeof($sorttaste);$i++){
						if(!isset($sorttaste[$i]['state'])||$sorttaste[$i]['state']=='1'){
							if(isset($sorttaste[$i]['public'])&&$sorttaste[$i]['public']=='0'){//專屬備註
								if(isset($sorttaste[$i]['group'])&&$sorttaste[$i]['group']!=''){//具有備註群組
									if(isset($taste[$sorttaste[$i]['group']])){
									}
									else{
										$taste[$sorttaste[$i]['group']]=array();
									}
									array_push($taste[$sorttaste[$i]['group']],$sorttaste[$i]['tasteno']);
								}
								else{
									
									if(isset($taste[-1])){
									}
									else{
										$taste[-1]=array();
									}
									array_push($taste[-1],$sorttaste[$i]['tasteno']);
								}
							}
							else{//公開備註
								continue;
							}
						}
						else{//已刪除備註
							continue;
						}
					}
					foreach($taste as $tg=>$t){
						if($tg!='-1'){
							$tgname=$tastegroup[$tg]['name'];
						}
						else{
							$tgname='未設定群組';
						}
						$ts='';
						$n=0;
						$c=0;
						for($i=0;$i<sizeof($t);$i++){
							if($i!=0){
								$ts=$ts.'、';
							}
							else{
							}
							$ts=$ts.'<label><input type="checkbox" class="'.$tg.'" name="taste[]" value="'.$t[$i].'"';
							if(in_array($t[$i],$tasteno)){
								$ts=$ts.' checked';
								$c++;
							}
							else{
							}
							$ts=$ts.'>'.$tastename[$t[$i]]['name1'].'</label>';
							$n++;

						}
						if($n==$c){
							$ts='<label style="margin:5px 0 0 0;"><input type="checkbox" class="g'.$tg.'" checked>'.$tgname.'</label><div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980;margin: 10px 0;">'.$ts.'</div>';
						}
						else{
							$ts='<label style="margin:5px 0 0 0;"><input type="checkbox" class="g'.$tg.'">'.$tgname.'</label><div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980;margin: 10px 0;">'.$ts.'</div>';
						}
						echo $ts;
					}
					?>
				</td>
			</tr>
		<?php
		}
		else{
		}
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodis']))echo $interface['name']['autodis'];else echo '自動優惠'; ?></td>
				<td style='border-top:1px solid #898989;border-bottom:1px solid #898989;border-radius:5px;'>
					<?php
					prime(25);//確保prime.seq.ini具有25個質數
					$prime=parse_ini_file('./prime/prime.seq.ini',true);
					if(isset($dis1[1])){
						echo '<div style="margin: 5px 0 0 0;">內用(discount1)</div>';
						$index=0;
						echo '<div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980; margin: 10px 0;">';
						for($i=1;$i<=sizeof($dis1);$i++){
							if(isset($dis1[$i])&&$dis1[$i]['listtype']!=0){//啟用優惠方案
								if($index!=0){
									echo '、';
								}
								else{
									$index=1;
								}
								echo '<label><input type="checkbox" name="discount1[]" value="'.$dis1[$i]['gnumber'].'" ';
								if(isset($itemname[$number]['dis1'])&&$itemname[$number]['dis1']!=''&&($itemname[$number]['dis1']%$dis1[$i]['gnumber'])==0){
									echo 'checked';
								}
								else{
								}
								echo '>'.$dis1[$i]['name'].'</label>';
							}
							else{
							}
						}
						echo '</div>';
					}
					else{
					}
					if(isset($dis2[1])){
						echo '<div style="margin: 5px 0 0 0;">外帶(discount2)</div>';
						$index=0;
						echo '<div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980; margin: 10px 0;">';
						for($i=1;$i<=sizeof($dis2);$i++){
							if(isset($dis2[$i])&&$dis2[$i]['listtype']!=0){//啟用優惠方案
								if($index!=0){
									echo '、';
								}
								else{
									$index=1;
								}
								echo '<label><input type="checkbox" name="discount2[]" value="'.$dis2[$i]['gnumber'].'" ';
								if(isset($itemname[$number]['dis2'])&&$itemname[$number]['dis2']!=''&&($itemname[$number]['dis2']%$dis2[$i]['gnumber'])==0){
									echo 'checked';
								}
								else{
								}
								echo '>'.$dis2[$i]['name'].'</label>';
							}
							else{
							}
						}
						echo '</div>';
					}
					else{
					}
					if(isset($dis3[1])){
						echo '<div style="margin: 5px 0 0 0;">外送(discount3)</div>';
						$index=0;
						echo '<div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980; margin: 10px 0;">';
						for($i=1;$i<=sizeof($dis3);$i++){
							if(isset($dis3[$i])&&$dis3[$i]['listtype']!=0){//啟用優惠方案
								if($index!=0){
									echo '、';
								}
								else{
									$index=1;
								}
								echo '<label><input type="checkbox" name="discount3[]" value="'.$dis3[$i]['gnumber'].'" ';
								if(isset($itemname[$number]['dis3'])&&$itemname[$number]['dis3']!=''&&($itemname[$number]['dis3']%$dis3[$i]['gnumber'])==0){
									echo 'checked';
								}
								else{
								}
								echo '>'.$dis3[$i]['name'].'</label>';
							}
							else{
							}
						}
						echo '</div>';
					}
					else{
					}
					if(isset($dis4[1])){
						echo '<div style="margin: 5px 0 0 0;">自取(discount4)</div>';
						$index=0;
						echo '<div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980; margin: 10px 0;">';
						for($i=1;$i<=sizeof($dis4);$i++){
							if(isset($dis4[$i])&&$dis4[$i]['listtype']!=0){//啟用優惠方案
								if($index!=0){
									echo '、';
								}
								else{
									$index=1;
								}
								echo '<label><input type="checkbox" name="discount4[]" value="'.$dis4[$i]['gnumber'].'" ';
								if(isset($itemname[$number]['dis4'])&&$itemname[$number]['dis4']!=''&&($itemname[$number]['dis4']%$dis4[$i]['gnumber'])==0){
									echo 'checked';
								}
								else{
								}
								echo '>'.$dis4[$i]['name'].'</label>';
							}
							else{
							}
						}
						echo '</div>';
					}
					else{
					}
					?>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['salemodel']))echo $interface['name']['salemodel'];else echo '銷售模組'; ?></td>
				<td><label><input type='radio' name='counter' value='1' <?php if(isset($itemname[$number]['counter'])&&$itemname[$number]['counter']=='1')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['stockname1']))echo $interface['name']['stockname1'];else echo '庫存'; ?></label>&#9;<label><input type='radio' name='counter' value='2' <?php if(isset($itemname[$number]['counter'])&&$itemname[$number]['counter']=='2')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['stockname2']))echo $interface['name']['stockname2'];else echo '限量(計算暫結與已結－已商品數為主)'; ?></label>&#9;<label><input type='radio' name='counter' value='3' <?php if(isset($itemname[$number]['counter'])&&$itemname[$number]['counter']=='3')echo 'checked'; ?>><?php if($interface!='-1'&&isset($interface['name']['stockname3']))echo $interface['name']['stockname3'];else echo '限量(只計算暫結－已帳單數為主)'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['hint1']))echo $interface['name']['hint1'];else echo '產品可銷售量'; ?><br><span style='font-size:12px;'><?php if($interface!='-1'&&isset($interface['name']['hint2']))echo $interface['name']['hint2'];else echo '(若不紀錄庫存或不限量銷售請留空)'; ?></span></td>
				<td><input type='tel' style='text-align:right;' name='limit' value='<?php 
				if(isset($itemname[$number]['counter'])&&intval($itemname[$number]['counter'])>0&&isset($stock[$number]))
					echo $stock[$number]['stock'];
				else ;
				?>'><span class='unit'><?php if(isset($unit['unit'])&&isset($unit['unit'][$itemname[$number]['unit']]))echo $unit['unit'][$itemname[$number]['unit']];else if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></span></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['computeperson']))echo $interface['name']['computeperson'];else echo '計算人頭'; ?></td>
				<td><label><input type='radio' name='personcount' value='0' <?php if(!isset($itemname[$number]['personcount'])||$itemname[$number]['personcount']=='0')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['notcomputeperson']))echo $interface['name']['notcomputeperson'];else echo '不計算人頭費'; ?></label>&#9;<?php if($floorspend['person1']['name']=='');else {echo "<label><input type='radio' name='personcount' value='1' ";if(isset($itemname[$number]['personcount'])&&$itemname[$number]['personcount']=='1')echo 'checked';else;echo ">".$floorspend['person1']['name']."(".$floorspend['person1']['floor'].")"."</label>&#9;";} ?><?php if($floorspend['person2']['name']=='');else {echo "<label><input type='radio' name='personcount' value='2' ";if(isset($itemname[$number]['personcount'])&&$itemname[$number]['personcount']=='2')echo 'checked';else;echo ">".$floorspend['person2']['name']."(".$floorspend['person2']['floor'].")"."</label>&#9;";} ?><?php if($floorspend['person3']['name']=='');else {echo "<label><input type='radio' name='personcount' value='3' ";if(isset($itemname[$number]['personcount'])&&$itemname[$number]['personcount']=='3')echo 'checked';else;echo ">".$floorspend['person3']['name']."(".$floorspend['person3']['floor'].")"."</label>&#9;";} ?></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['openmoneylabel']))echo $interface['name']['openmoneylabel'];else echo '開放價格'; ?></td>
				<td><label><input type='radio' name='openmoney' value='0' <?php if(!isset($itemname[$number]['openmoney'])||$itemname[$number]['openmoney']=='0')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='openmoney' value='1' <?php if(isset($itemname[$number]['openmoney'])&&$itemname[$number]['openmoney']=='1')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemdis']))echo $interface['name']['itemdis'];else echo '允許單品促銷'; ?></td>
				<td><label><input type='radio' name='itemdis' value='0' <?php if(!isset($itemname[$number]['itemdis'])||$itemname[$number]['itemdis']=='0')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='itemdis' value='1' <?php if(isset($itemname[$number]['itemdis'])&&$itemname[$number]['itemdis']=='1')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['listdis']))echo $interface['name']['listdis'];else echo '允許帳單促銷'; ?></td>
				<td><label><input type='radio' name='listdis' value='0' <?php if(!isset($itemname[$number]['listdis'])||$itemname[$number]['listdis']=='0')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='listdis' value='1' <?php if(isset($itemname[$number]['listdis'])&&$itemname[$number]['listdis']=='1')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['computecharge']))echo $interface['name']['computecharge'];else echo '計算服務費'; ?></td>
				<td><label><input type='radio' name='charge' value='0' <?php if(isset($itemname[$number]['charge'])&&$itemname[$number]['charge']=='0')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='charge' value='1' <?php if(!isset($itemname[$number]['charge'])||$itemname[$number]['charge']=='1')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['webvisible']))echo $interface['name']['webvisible'];else echo '顯示於手機點餐'; ?></td>
				<td><label><input type='radio' name='webvisible' value='0' <?php if(isset($itemname[$number]['webvisible'])&&$itemname[$number]['webvisible']=='0')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='webvisible' value='1' <?php if(!isset($itemname[$number]['webvisible'])||$itemname[$number]['webvisible']=='1')echo 'checked';else; ?>><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemimglabel']))echo $interface['name']['itemimglabel'];else echo '產品圖片'; ?></td>
				<td><input type='file' name='itemimg' accept='image/*' value=''></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemintroduction']))echo $interface['name']['itemintroduction'];else echo '產品簡介'; ?></td>
				<td><!-- <input type='text' name='introduction' value='<?php if(isset($itemname[$number]['introduction']))echo $itemname[$number]['introduction']; ?>'> --></td>
			</tr>
			<tr>
				<td style='text-align:center;'><?php if($interface!='-1'&&isset($interface['name']['titlelabel']))echo $interface['name']['titlelabel'];else echo '標題'; ?></td>
				<td style='text-align:center;'><?php if($interface!='-1'&&isset($interface['name']['contentlabel']))echo $interface['name']['contentlabel'];else echo '內容'; ?></td>
				<td style='text-align:center;'><?php if($interface!='-1'&&isset($interface['name']['mainfontcolor']))echo $interface['name']['mainfontcolor'];else echo '字體顏色'; ?></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle1' value='<?php if(isset($itemname[$number]['introtitle1']))echo $itemname[$number]['introtitle1']; ?>'></td>
				<td><input type='text' name='introduction1' style='width:100%;' value='<?php if(isset($itemname[$number]['introduction1']))echo $itemname[$number]['introduction1']; ?>'></td>
				<td><input id='introcolor1' name='introcolor1'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle2' value='<?php if(isset($itemname[$number]['introtitle2']))echo $itemname[$number]['introtitle2']; ?>'></td>
				<td><input type='text' name='introduction2' style='width:100%;' value='<?php if(isset($itemname[$number]['introduction2']))echo $itemname[$number]['introduction2']; ?>'></td>
				<td><input id='introcolor2' name='introcolor2'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle3' value='<?php if(isset($itemname[$number]['introtitle3']))echo $itemname[$number]['introtitle3']; ?>'></td>
				<td><input type='text' name='introduction3' style='width:100%;' value='<?php if(isset($itemname[$number]['introduction3']))echo $itemname[$number]['introduction3']; ?>'></td>
				<td><input id='introcolor3' name='introcolor3'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle4' value='<?php if(isset($itemname[$number]['introtitle4']))echo $itemname[$number]['introtitle4']; ?>'></td>
				<td><input type='text' name='introduction4' style='width:100%;' value='<?php if(isset($itemname[$number]['introduction4']))echo $itemname[$number]['introduction4']; ?>'></td>
				<td><input id='introcolor4' name='introcolor4'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle5' value='<?php if(isset($itemname[$number]['introtitle5']))echo $itemname[$number]['introtitle5']; ?>'></td>
				<td><input type='text' name='introduction5' style='width:100%;' value='<?php if(isset($itemname[$number]['introduction5']))echo $itemname[$number]['introduction5']; ?>'></td>
				<td><input id='introcolor5' name='introcolor5'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle6' value='<?php if(isset($itemname[$number]['introtitle6']))echo $itemname[$number]['introtitle6']; ?>'></td>
				<td><input type='text' name='introduction6' style='width:100%;' value='<?php if(isset($itemname[$number]['introduction6']))echo $itemname[$number]['introduction6']; ?>'></td>
				<td><input id='introcolor6' name='introcolor6'></td>
			</tr>
		</table>
	</form>
</div>
<?php
}
else{
	include_once '../../../tool/dbTool.inc.php';
	$company=$_POST['company'];
	$dep=$_POST['dep'];
	$conn=sqlconnect('../../../menudata/'.$company.'/'.$dep,'menu.db','','','','sqlite');
	$sql='SELECT fronttype FROM itemsdata WHERE fronttype LIKE "g%"';
	$groupfront=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	$printlist=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/printlisttag.ini',true);
	$frontname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-front.ini',true);
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-rear.ini')){
		$rearname=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-rear.ini',true);
	}
	else{
		$rearname='-1';
	}
	if(file_exists('../../../menudata/'.$company.'/'.$dep.'/initsetting.ini')){
		$initsetting=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/initsetting.ini',true);
	}
	else{
	}
	$floorspend=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/floorspend.ini',true);
	$unit=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/unit.ini',true);
?>
<script>
$(document).ready(function(){
	$("#color1").colorpicker({
		color:'#000000',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$("#color2").colorpicker({
		color:'#898989',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#bgcolor').colorpicker({
		color:'#FFFF85',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor1').colorpicker({
		color:'#000000',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor2').colorpicker({
		color:'#000000',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor3').colorpicker({
		color:'#000000',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor4').colorpicker({
		color:'#000000',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor5').colorpicker({
		color:'#000000',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
	$('#introcolor6').colorpicker({
		color:'#000000',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	});
});
</script>
<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?></center></h1>
<div style='width:100%;float:left;'>
	<input id='save' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>">
	<input id='cancel' class="initbutton" type="button" value="<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>">
</div>
<div style='width:100%;overflow:hidden;'>
	<form id='itemform' style='overflow:hidden;'>
		<input type='hidden' name='itemdep' value=''>
		<input type='hidden' name='number' value=''>
		<input type='hidden' name='company' value='<?php echo $company; ?>'>
		<input type='hidden' name='dep' value='<?php echo $dep; ?>'>
		<table style='float;'>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['typelabel']))echo $interface['name']['typelabel'];else echo '類別'; ?></td>
				<td>
					<div class="mod_select" id='frontmod'>
						<ul>
							<li>
								<div class="select_box" id='frontbox'>
									<?php
									$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">';
									for($i=0;$i<sizeof($frontname);$i++){
										if($frontname[$i]['state']=='0'){
										}
										else{
											$option=$option.'<a id="'.$i.'-'.$frontname[$i]['seq'].'">'.$frontname[$i]['name1'].'/'.$frontname[$i]['name2'].'</a>';
										}
									}
									$option=$option.'</div>';
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='front' id="select_value" value=''>
					</div>
				</td>
			</tr>
		<?php
		if($rearname!='-1'){
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['anatype']))echo $interface['name']['anatype'];else echo '分析類別'; ?></td>
				<td>
					<div class="mod_select" id='rearmod'>
						<ul>
							<li>
								<div class="select_box" id='rearbox'>
									<?php
									$option='';
									$initvalue='';
									for($i=0;$i<sizeof($rearname);$i++){
										if($rearname[$i]['state']=='0'){
										}
										else{
											if(strlen($initvalue)==0){
												$initvalue=$i;
												$option='<span class="select_txt">'.$rearname[$i]['name'].'</span><a class="selet_open">▼</a><div class="option">'.$option;
												$checked='<span class="select_txt">'.$rearname[$i]['name'].'</span><a class="selet_open">▼</a>';
											}
											else{
											}
											$option=$option.'<a id="'.$i.'">'.$rearname[$i]['name'].'</a>';
										}
									}
									$option=$option.'</div>';
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='rear' id="select_value" value='<?php echo $initvalue; ?>'>
					</div>
				</td>
			</tr>
		<?php
		}
		else{
		?>
			<input type="hidden" name='rear' id="select_value" value='0'>
		<?php
		}
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['printmenu']))echo $interface['name']['printmenu'];else echo '列印類別'; ?></td>
				<td>
					<div class="mod_select" id='prlimod'>
						<ul>
							<li>
								<div class="select_box" id='prlibox'>
									<?php
									$prlist=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/itemprinttype.ini',true);
									$option='';
									foreach($prlist as $k=>$v){
										$option=$option.'<a id="'.$k.'">'.$v['name'].'</a>';
									}
									$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">'.$option;
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='printtype' id="select_value" value=''>
					</div>
				</td>
			</tr>
		<?php
		if(isset($initsetting['init']['kds'])&&$initsetting['init']['kds']=='1'){
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['setkds']))echo $interface['name']['setkds'];else echo '廚房控餐設定'; ?></td>
			</tr>
			<tr>
				<td style='padding:14px 0;'><?php if($interface!='-1'&&isset($interface['name']['partition']))echo $interface['name']['partition'];else echo '區域'; ?></td>
				<td>
					<div class="mod_select" id='kdsmod'>
						<ul>
							<li>
								<div class="select_box" id='kdsbox'>
									<?php
									if(file_exists('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini')){
										$kds=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-kds.ini',true);
										$option='';
										if(isset($kds['type']['name'])){
											foreach($kds['type']['name'] as $k=>$v){
												$option=$option.'<a id="'.$k.'">'.$v.'</a>';
											}
											$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">'.$option;
											echo $option;
										}
										else{
										}
									}
									else{
									}
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='kds' id="select_value" value=''>
					</div>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;'><?php if($interface!='-1'&&isset($interface['name']['groupofpt']))echo $interface['name']['groupofpt'];else echo '群組'; ?></td>
				<td>
					<div class="mod_select" id='kdsgroupmod'>
						<ul>
							<li>
								<div class="select_box" id='kdsgroupbox'>
								</div>
							</li>
						</ul>
						<input type="hidden" name='kdsgroup' id="select_value" value=''>
					</div>
				</td>
			</tr>
		<?php
		}
		else{
		}
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['viewseq']))echo $interface['name']['viewseq'];else echo '顯示順序'; ?></td>
				<td><input type='tel' class='seq' name='seq' value='1'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['quicknumberlabel']))echo $interface['name']['quicknumberlabel'];else echo '快點代碼(限定數字)'; ?></td>
				<td><input type='text' class='quickorder' name='quickorder' value=''></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemnamelabel']))echo $interface['name']['itemnamelabel'];else echo '產品名稱'; ?></td>
				<td></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mainname']))echo $interface['name']['mainname'];else echo '主語言'; ?></td>
				<td><input type='text' class='mainname' name='name1' value=''></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontsize']))echo $interface['name']['mainfontsize'];else echo '字體大小'; ?></td>
				<td><input type='tel' name='size1' value='20'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontcolor']))echo $interface['name']['mainfontcolor'];else echo '字體顏色'; ?></td>
				<td><input id='color1' name='color1'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mainfontweight']))echo $interface['name']['mainfontweight'];else echo '是否粗體'; ?></td>
				<td><input type='checkbox' name='bold1' checked></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['secname']))echo $interface['name']['secname'];else echo '次語言'; ?></td>
				<td><input type='text' name='name2' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['secfontsize']))echo $interface['name']['secfontsize'];else echo '字體大小'; ?></td>
				<td><input type='tel' name='size2' value='14'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['secfontcolor']))echo $interface['name']['secfontcolor'];else echo '字體顏色'; ?></td>
				<td><input id='color2' name='color2'></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['secfontweight']))echo $interface['name']['secfontweight'];else echo '是否粗體'; ?></td>
				<td><input type='checkbox' name='bold2'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['bgcolor']))echo $interface['name']['bgcolor'];else echo '按鈕底色'; ?></td>
				<td><input id='bgcolor' name='bgcolor'></td>
			</tr>
			<tr>
				<td colspan='2'>
					<?php if($interface!='-1'&&isset($interface['name']['tempview']))echo $interface['name']['tempview'];else echo '檢視效果(僅供參考)'; ?>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<button style='min-width:261px;max-width:261px;height:100px;text-align:center;font-family:Consolas,Microsoft JhengHei,sans-serif;background-color:#FFFF85;border: 1px solid #898989;border-radius: 5px;overflow:hidden;' disabled>
						<div id='name1' style='font-size:14px;color:#000000;font-weight:normal;'>
						</div>
						<div id='name2' style='font-size:14px;color:#898989;font-weight:normal;'>
						</div>
					</button>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname1label']))echo $interface['name']['mname1label'];else echo '價格一'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname11' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname12' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money1' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit1' value='1'><span class='unit'><?php echo $unit['unit'][0]; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup'></td>
			</tr> -->
			<tr id='subtype' style='display:none;'>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					foreach($groupfront as $gf){
						echo "<option>".$frontname[$gf['front']]['name']."</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname2label']))echo $interface['name']['mname2label'];else echo '價格二'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname21' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname22' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money2' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit2' value='1'><span class='unit'><?php echo $unit['unit'][0]; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup'></td>
			</tr> -->
			<tr id='subtype' style='display:none;'>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					foreach($groupfront as $gf){
						echo "<option>".$frontname[$gf['front']]['name']."</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname3label']))echo $interface['name']['mname3label'];else echo '價格三'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname31' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname32' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money3' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit3' value='1'><span class='unit'><?php echo $unit['unit'][0]; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup'></td>
			</tr> -->
			<tr id='subtype' style='display:none;'>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					foreach($groupfront as $gf){
						echo "<option>".$frontname[$gf['front']]['name']."</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname4label']))echo $interface['name']['mname4label'];else echo '價格四'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname41' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname42' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money4' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit4' value='1'><span class='unit'><?php echo $unit['unit'][0]; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup'></td>
			</tr> -->
			<tr id='subtype' style='display:none;'>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					foreach($groupfront as $gf){
						echo "<option>".$frontname[$gf['front']]['name']."</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname5label']))echo $interface['name']['mname5label'];else echo '價格五'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname51' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname52' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money5' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit5' value='1'><span class='unit'><?php echo $unit['unit'][0]; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup'></td>
			</tr> -->
			<tr id='subtype' style='display:none;'>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					foreach($groupfront as $gf){
						echo "<option>".$frontname[$gf['front']]['name']."</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['mname6label']))echo $interface['name']['mname6label'];else echo '價格六'; ?></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname1']))echo $interface['name']['mname1'];else echo '名稱(主)'; ?></td>
				<td><input type='text' name='mname61' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['mname2']))echo $interface['name']['mname2'];else echo '名稱(次)'; ?></td>
				<td><input type='text' name='mname62' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['pricelabel']))echo $interface['name']['pricelabel'];else echo '金額'; ?></td>
				<td><input type='tel' style='text-align:right;' name='money6' value=''></td>
			</tr>
			<tr>
				<td><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td><input type='tel' style='text-align:right;' name='unit6' value='1'><span class='unit'><?php echo $unit['unit'][0]; ?></span>/1<?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></td>
			</tr>
			<!-- <tr>
				<td>是否為套餐？</td>
				<td><input type='checkbox' name='isgroup'></td>
			</tr> -->
			<tr id='subtype' style='display:none;'>
				<td>附餐類別</td>
				<td>
					<select>
					<?php
					foreach($groupfront as $gf){
						echo "<option>".$frontname[$gf['front']]['name']."</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['tagfontsize']))echo $interface['name']['tagfontsize'];else echo '貼紙產品文字大小'; ?></td>
				<td><input type='number' name='tagsize' style='text-align:right;' value='12'></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['useunit']))echo $interface['name']['useunit'];else echo '使用單位'; ?></td>
				<td>
					<div class="mod_select" id='unitmod'>
						<ul>
							<li>
								<div class="select_box" id='unitbox'>
									<?php
									$option='';
									for($i=0;$i<sizeof($unit['unit']);$i++){
										$option=$option.'<a id="'.$i.'">'.$unit['unit'][$i].'</a>';
									}
									$option='<span class="select_txt">'.$unit['unit'][0].'</span><a class="selet_open">▼</a><div class="option">'.$option;
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='unit' id="select_value" value='0'>
					</div>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['strawlabel']))echo $interface['name']['strawlabel'];else echo '吸管設定'; ?></td>
				<td>
					<div class="mod_select" id='strawmod'>
						<ul>
							<li>
								<div class="select_box" id='strawbox'>
									<?php
									$straw=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/straw.ini',true);
									$option='';
									foreach($straw['straw'] as $k=>$v){
										$option=$option.'<a id="'.$k.'">'.$v.'</a>';
									}
									$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">'.$option;
									echo $option;
									?>
								</div>
							</li>
						</ul>
						<input type="hidden" name='straw' id="select_value" value=''>
					</div>
				</td>
			</tr>
		<?php
		if(isset($initsetting['init']['tastegroup'])&&$initsetting['init']['tastegroup']=='1'){
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['tastelabel']))echo $interface['name']['tastelabel'];else echo '專屬備註'; ?></td>
				<td style='border-top:1px solid #898989;border-bottom:1px solid #898989;border-radius:5px;' id='tastediv'>
					<?php
					$tastename=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-taste.ini',true);
					$sorttaste=quicksort($tastename,'seq');
					$tastegroup=parse_ini_file('../../../menudata/'.$company.'/'.$dep.'/'.$company.'-tastegroup.ini',true);
					$taste=array();
					for($i=0;$i<sizeof($sorttaste);$i++){
						if(!isset($sorttaste[$i]['state'])||$sorttaste[$i]['state']=='1'){
							if(isset($sorttaste[$i]['public'])&&$sorttaste[$i]['public']=='0'){//專屬備註
								if(isset($sorttaste[$i]['group'])&&$sorttaste[$i]['group']!=''){//具有備註群組
									if(isset($taste[$sorttaste[$i]['group']])){
									}
									else{
										$taste[$sorttaste[$i]['group']]=array();
									}
									array_push($taste[$sorttaste[$i]['group']],$sorttaste[$i]['tasteno']);
								}
								else{
									
									if(isset($taste[-1])){
									}
									else{
										$taste[-1]=array();
									}
									array_push($taste[-1],$sorttaste[$i]['tasteno']);
								}
							}
							else{//公開備註
								continue;
							}
						}
						else{//已刪除備註
							continue;
						}
					}
					foreach($taste as $tg=>$t){
						if($tg!='-1'){
							$tgname=$tastegroup[$tg]['name'];
						}
						else{
							$tgname='未設定群組';
						}
						$ts='';
						$n=0;
						$c=0;
						for($i=0;$i<sizeof($t);$i++){
							if($i!=0){
								$ts=$ts.'、';
							}
							else{
							}
							$ts=$ts.'<label><input type="checkbox" class="'.$tg.'" name="taste[]" value="'.$t[$i].'">'.$tastename[$t[$i]]['name1'].'</label>';

						}
						$ts='<label style="margin:5px 0 0 0;"><input type="checkbox" class="g'.$tg.'">'.$tgname.'</label><div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980;margin: 10px 0;">'.$ts.'</div>';
						echo $ts;
					}
					?>
				</td>
			</tr>
		<?php
		}
		else{
		}
		?>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['autodis']))echo $interface['name']['autodis'];else echo '自動優惠'; ?></td>
				<td style='border-top:1px solid #898989;border-bottom:1px solid #898989;border-radius:5px;'>
					<?php
					if(isset($dis1[1])){
						echo '<div style="margin: 5px 0 0 0;">內用(discount1)</div>';
						$index=0;
						echo '<div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980; margin: 10px 0;">';
						for($i=1;$i<=sizeof($dis1);$i++){
							if(isset($dis1[$i])&&$dis1[$i]['listtype']!=0){//啟用優惠方案
								if($index!=0){
									echo '、';
								}
								else{
									$index=1;
								}
								echo '<label><input type="checkbox" name="discount1[]" value="'.$dis1[$i]['gnumber'].'">'.$dis1[$i]['name'].'</label>';
							}
							else{
							}
						}
						echo '</div>';
					}
					else{
					}
					if(isset($dis2[1])){
						echo '<div style="margin: 5px 0 0 0;">外帶(discount2)</div>';
						$index=0;
						echo '<div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980; margin: 10px 0;">';
						for($i=1;$i<=sizeof($dis2);$i++){
							if(isset($dis2[$i])&&$dis2[$i]['listtype']!=0){//啟用優惠方案
								if($index!=0){
									echo '、';
								}
								else{
									$index=1;
								}
								echo '<label><input type="checkbox" name="discount2[]" value="'.$dis2[$i]['gnumber'].'">'.$dis2[$i]['name'].'</label>';
							}
							else{
							}
						}
						echo '</div>';
					}
					else{
					}
					if(isset($dis3[1])){
						echo '<div style="margin: 5px 0 0 0;">外送(discount3)</div>';
						$index=0;
						echo '<div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980; margin: 10px 0;">';
						for($i=1;$i<=sizeof($dis3);$i++){
							if(isset($dis3[$i])&&$dis3[$i]['listtype']!=0){//啟用優惠方案
								if($index!=0){
									echo '、';
								}
								else{
									$index=1;
								}
								echo '<label><input type="checkbox" name="discount3[]" value="'.$dis3[$i]['gnumber'].'">'.$dis3[$i]['name'].'</label>';
							}
							else{
							}
						}
						echo '</div>';
					}
					else{
					}
					if(isset($dis4[1])){
						echo '<div style="margin: 5px 0 0 0;">自取(discount4)</div>';
						$index=0;
						echo '<div style="padding: 5px 0 5px 20px; border-bottom: 1px solid #89898980; margin: 10px 0;">';
						for($i=1;$i<=sizeof($dis4);$i++){
							if(isset($dis4[$i])&&$dis4[$i]['listtype']!=0){//啟用優惠方案
								if($index!=0){
									echo '、';
								}
								else{
									$index=1;
								}
								echo '<label><input type="checkbox" name="discount4[]" value="'.$dis4[$i]['gnumber'].'">'.$dis4[$i]['name'].'</label>';
							}
							else{
							}
						}
						echo '</div>';
					}
					else{
					}
					?>
				</td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['salemodel']))echo $interface['name']['salemodel'];else echo '銷售模組'; ?></td>
				<td><label><input type='radio' name='counter' value='1'><?php if($interface!='-1'&&isset($interface['name']['stockname1']))echo $interface['name']['stockname1'];else echo '庫存'; ?></label>&#9;<label><input type='radio' name='counter' value='2'><?php if($interface!='-1'&&isset($interface['name']['stockname2']))echo $interface['name']['stockname2'];else echo '限量(計算暫結與已結－已商品數為主)'; ?></label>&#9;<label><input type='radio' name='counter' value='3'><?php if($interface!='-1'&&isset($interface['name']['stockname3']))echo $interface['name']['stockname3'];else echo '限量(只計算暫結－已帳單數為主)'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['hint1']))echo $interface['name']['hint1'];else echo '產品可銷售量'; ?><br><span style='font-size:12px;'><?php if($interface!='-1'&&isset($interface['name']['hint2']))echo $interface['name']['hint2'];else echo '(若不紀錄庫存或不限量銷售請留空)'; ?></span></td>
				<td><input type='tel' style='text-align:right;' name='limit' value=''><span class='unit'><?php if($interface!='-1'&&isset($interface['name']['qtyunit']))echo $interface['name']['qtyunit'];else echo '份'; ?></span></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['computeperson']))echo $interface['name']['computeperson'];else echo '計算人頭'; ?></td>
				<td><label><input type='radio' name='personcount' value='0' checked><?php if($interface!='-1'&&isset($interface['name']['notcomputeperson']))echo $interface['name']['notcomputeperson'];else echo '不計算人頭費'; ?></label>&#9;<?php if($floorspend['person1']['name']=='');else {echo "<label><input type='radio' name='personcount' value='1' ";echo ">".$floorspend['person1']['name']."(".$floorspend['person1']['floor'].")"."</label>&#9;";} ?><?php if($floorspend['person2']['name']=='');else {echo "<label><input type='radio' name='personcount' value='2' ";echo ">".$floorspend['person2']['name']."(".$floorspend['person2']['floor'].")"."</label>&#9;";} ?><?php if($floorspend['person3']['name']=='');else {echo "<label><input type='radio' name='personcount' value='3' ";echo ">".$floorspend['person3']['name']."(".$floorspend['person3']['floor'].")"."</label>&#9;";} ?></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['openmoneylabel']))echo $interface['name']['openmoneylabel'];else echo '開放價格'; ?></td>
				<td><label><input type='radio' name='openmoney' value='0' checked><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='openmoney' value='1'><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemdis']))echo $interface['name']['itemdis'];else echo '允許單品促銷'; ?></td>
				<td><label><input type='radio' name='itemdis' value='0'><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='itemdis' value='1' checked><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['listdis']))echo $interface['name']['listdis'];else echo '允許帳單促銷'; ?></td>
				<td><label><input type='radio' name='listdis' value='0'><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='listdis' value='1' checked><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['computecharge']))echo $interface['name']['computecharge'];else echo '計算服務費'; ?></td>
				<td><label><input type='radio' name='charge' value='0'><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='charge' value='1' checked><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['webvisible']))echo $interface['name']['webvisible'];else echo '顯示於手機點餐'; ?></td>
				<td><label><input type='radio' name='webvisible' value='0'><?php if($interface!='-1'&&isset($interface['name']['not']))echo $interface['name']['not'];else echo '否'; ?></label>&#9;<label><input type='radio' name='webvisible' value='1' checked><?php if($interface!='-1'&&isset($interface['name']['pass']))echo $interface['name']['pass'];else echo '是'; ?></label></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemimglabel']))echo $interface['name']['itemimglabel'];else echo '產品圖片'; ?></td>
				<td><input type='file' name='itemimg' accept='image/*' value=''></td>
			</tr>
			<tr>
				<td style='padding:14px 0;font-weight:bold;'><?php if($interface!='-1'&&isset($interface['name']['itemintroduction']))echo $interface['name']['itemintroduction'];else echo '產品簡介'; ?></td>
				<td><!-- <input type='text' name='introduction' value=''> --></td>
			</tr>
			<tr>
				<td style='text-align:center;'><?php if($interface!='-1'&&isset($interface['name']['titlelabel']))echo $interface['name']['titlelabel'];else echo '標題'; ?></td>
				<td style='text-align:center;'><?php if($interface!='-1'&&isset($interface['name']['contentlabel']))echo $interface['name']['contentlabel'];else echo '內容'; ?></td>
				<td style='text-align:center;'><?php if($interface!='-1'&&isset($interface['name']['mainfontcolor']))echo $interface['name']['mainfontcolor'];else echo '字體顏色'; ?></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle1' value=''></td>
				<td><input type='text' style='width:100%;' name='introduction1' value=''></td>
				<td><input id='introcolor1' name='introcolor1'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle2' value=''></td>
				<td><input type='text' style='width:100%;' name='introduction2' value=''></td>
				<td><input id='introcolor2' name='introcolor2'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle3' value=''></td>
				<td><input type='text' style='width:100%;' name='introduction3' value=''></td>
				<td><input id='introcolor3' name='introcolor3'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle4' value=''></td>
				<td><input type='text' style='width:100%;' name='introduction4' value=''></td>
				<td><input id='introcolor4' name='introcolor4'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle5' value=''></td>
				<td><input type='text' style='width:100%;' name='introduction5' value=''></td>
				<td><input id='introcolor5' name='introcolor5'></td>
			</tr>
			<tr>
				<td><input type='text' name='introtitle6' value=''></td>
				<td><input type='text' style='width:100%;' name='introduction6' value=''></td>
				<td><input id='introcolor6' name='introcolor6'></td>
			</tr>
		</table>
	</form>
</div>
<?php
}
?>