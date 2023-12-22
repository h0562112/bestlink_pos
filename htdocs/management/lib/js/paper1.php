<?php
session_start();
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
?>
<script>
paper=$('.paper').tabs();
function strtotime(time, now) {
	var d = new Date();
	d.setTime(now);

	var ParsedTime = new RegExp('([+-][0-9]+) (\\w+)', 'i').exec(time);
	if(!ParsedTime) return 0;

	switch(ParsedTime[2]) {
		case 'second':
			d.setSeconds(d.getSeconds() + parseInt(ParsedTime[1], 10));
			break;
		case 'minute':
			d.setMinutes(d.getMinutes() + parseInt(ParsedTime[1], 10));
			break;
		case 'hour':
			d.setHours(d.getHours() + parseInt(ParsedTime[1], 10));
			break;
		case 'day':
			d.setDate(d.getDate() + parseInt(ParsedTime[1], 10));
			break;
		case 'month':
			d.setMonth(d.getMonth() + parseInt(ParsedTime[1], 10));
			break;
		case 'year':
			d.setFullYear(d.getFullYear() + parseInt(ParsedTime[1], 10));
			break;
	}

	return d.getTime();
}
$(document).ready(function(){
	$(document).on('click','.paper #paper1 #search',function(){
		var start=new Date($('.paper #paper1 input[name="startdate"]').val());
		var end=new Date($('.paper #paper1 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper1.ajax.php',
				method:'post',
				data:$('#paper1 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper1 .table').html(d);
					$("#paper1 #fixTable").tableHeadFixer({"left" : 1,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper1 #expinit',function(){
		var start=new Date($('.paper #paper1 input[name="startdate"]').val());
		var end=new Date($('.paper #paper1 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/expPaper1.ajax.php',
				method:'post',
				data:$('#paper1 #detail').serialize(),
				dateType:'html',
				success:function(d){
					window.open(d);
					console.log(d);
					//$('.paper #paper1 .table').html(d);
					//$("#paper1 #fixTable").tableHeadFixer({"left" : 1,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper1 #expuis',function(){
		var start=new Date($('.paper #paper1 input[name="startdate"]').val());
		var end=new Date($('.paper #paper1 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/test.php',
				method:'post',
				data:$('#paper1 #detail').serialize(),
				dateType:'html',
				success:function(d){
					if(d.match(/;-;/)){
						var temp=d.split(';-;');
						for(var i=0;i<temp.length;i++){
							window.open(temp[i]);
						}
					}
					else{
						window.open(d);
					}
					console.log(d);
					//$('.paper #paper1 .table').html(d);
					//$("#paper1 #fixTable").tableHeadFixer({"left" : 1,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper2 #search',function(){
		var start=new Date($('.paper #paper2 input[name="startdate"]').val());
		var end=new Date($('.paper #paper2 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper2.ajax.php',
				method:'post',
				data:$('#paper2 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper2 .table').html(d);
					$("#paper2 #fixTable").tableHeadFixer({"left" : 1,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper3 #search',function(){
		var start1=new Date($('.paper #paper3 input[name="startdate1"]').val());
		var end1=new Date($('.paper #paper3 input[name="enddate1"]').val());
		var start2=new Date($('.paper #paper3 input[name="startdate2"]').val());
		var end2=new Date($('.paper #paper3 input[name="enddate2"]').val());
		var now=new Date();
		if(start1.getTime()>end1.getTime()||start2.getTime()>end2.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start1.getTime()>now.getTime()||start2.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper3.ajax.php',
				method:'post',
				data:$('#paper3 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper3 .table').html(d);
					$("#paper3 #fixTable").tableHeadFixer({"left" : 1,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper4 #search',function(){
		var start1=new Date($('.paper #paper4 input[name="startdate1"]').val());
		var end1=new Date($('.paper #paper4 input[name="enddate1"]').val());
		var start2=new Date($('.paper #paper4 input[name="startdate2"]').val());
		var end2=new Date($('.paper #paper4 input[name="enddate2"]').val());
		var now=new Date();
		if(start1.getTime()>end1.getTime()||start2.getTime()>end2.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start1.getTime()>now.getTime()||start2.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper4.ajax.php',
				method:'post',
				data:$('#paper4 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper4 .table').html(d);
					$("#paper4 #fixTable").tableHeadFixer({"left" : 1,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper5 #search',function(){
		var start=new Date($('.paper #paper5 input[name="startdate"]').val());
		var end=new Date($('.paper #paper5 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper5.ajax.php',
				method:'post',
				data:$('#paper5 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper5 .table').html(d);
					$("#paper5 #fixTable").tableHeadFixer({"left" : 1}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper6 #search',function(){
		var end=new Date($('.paper #paper6 input[name="enddate"]').val());
		var now=new Date();
		$.ajax({
			url:'./lib/js/getPaper6.ajax.php',
			method:'post',
			data:$('#paper6 #detail').serialize(),
			dateType:'html',
			success:function(d){
				$('.paper #paper6 .table').html(d);
				$("#paper6 #fixTable").tableHeadFixer({"left" : 1}); 
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.paper #paper6 .table tr[class^="zcounter"], .paper #paper6 .table .totalzcounter',function(){
		$('.paper #otherlink').trigger('click');
	});
	$(document).on('mouseenter','.paper #paper6 .table tr[class^="zcounter"], .paper #paper6 .table .totalzcounter',function(e){
		$('.mytooltip').css({'display':'none'});
		var xaxis=e.clientX+15;
		var yaxis=e.clientY;
		if((yaxis+$('#'+$(this).prop('class')).height()+12)>window.innerHeight){
			yaxis=window.innerHeight-$('#'+$(this).prop('class')).height()-12-5;
		}
		else{
		}
		if((xaxis+$('#'+$(this).prop('class')).width()+12+15)>window.innerWidth){
			xaxis=e.clientX-$('#'+$(this).prop('class')).width()-12-5;
		}
		else{
		}
		console.log(window.innerWidth);
		$('#'+$(this).prop('class')).css({'display':'block','top':yaxis,'left':xaxis});
		//$(this).prop('class');
	});
	$(document).on('mousemove','.paper #paper6 .table tr[class^="zcounter"], .paper #paper6 .table .totalzcounter',function(e){
		var xaxis=e.clientX+15;
		var yaxis=e.clientY;
		if((yaxis+$('#'+$(this).prop('class')).height()+12)>window.innerHeight){
			yaxis=window.innerHeight-$('#'+$(this).prop('class')).height()-12-5;
		}
		else{
		}
		if((xaxis+$('#'+$(this).prop('class')).width()+12+15)>window.innerWidth){
			xaxis=e.clientX-$('#'+$(this).prop('class')).width()-12-5;
		}
		else{
		}
		$('#'+$(this).prop('class')).css({'display':'block','top':yaxis,'left':xaxis});
		//$(this).prop('class');
	});
	$(document).on('mouseout','.paper #paper6 .table tr[class^="zcounter"], .paper #paper6 .table .totalzcounter',function(){
		$('.mytooltip').css({'display':'none'});
		//$(this).prop('class');
	});
	$(document).on('click','.paper #paper6 .intellalink',function(){
		window.open('https://a1.intella.co/mgt/','_blank');
	});
	$(document).on('click','.paper #paper7 #search',function(){
		var start=new Date($('.paper #paper7 input[name="startdate"]').val());
		var end=new Date($('.paper #paper7 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper7.ajax.php',
				method:'post',
				data:$('#paper7 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper7 .table').html(d);
					$("#paper7 #fixTable").tableHeadFixer({"left" : 1}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper8 #search',function(){
		var start=new Date($('.paper #paper8 input[name="startdate"]').val());
		var end=new Date($('.paper #paper8 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper8.ajax.php',
				method:'post',
				data:$('#paper8 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper8 .table').html(d);
					$("#paper8 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper9 #search',function(){
		var start=new Date($('.paper #paper9 input[name="startdate"]').val());
		var end=new Date($('.paper #paper9 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper9.ajax.php',
				method:'post',
				data:$('#paper9 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper9 .table').html(d);
					$("#paper9 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper10 #search',function(){
		var start=new Date($('.paper #paper10 input[name="startdate"]').val());
		var end=new Date($('.paper #paper10 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper10.ajax.php',
				method:'post',
				data:$('#paper10 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper10 .table').html(d);
					$("#paper10 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper11 #search',function(){
		var start=new Date($('.paper #paper11 input[name="startdate"]').val());
		var end=new Date($('.paper #paper11 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper11.ajax.php',
				method:'post',
				data:$('#paper11 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper11 .table').html(d);
					$("#paper11 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper12 #search',function(){
		var start=new Date($('.paper #paper12 input[name="startdate"]').val());
		var end=new Date($('.paper #paper12 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper12.ajax.php',
				method:'post',
				data:$('#paper12 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper12 .table').html(d);
					$("#paper12 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper13 #search',function(){
		var start=new Date($('.paper #paper13 input[name="startdate"]').val());
		var end=new Date($('.paper #paper13 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper13.ajax.php',
				method:'post',
				data:$('#paper13 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper13 .table').html(d);
					$("#paper13 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper13 #exp',function(){
		var start=new Date($('.paper #paper13 input[name="startdate"]').val());
		var end=new Date($('.paper #paper13 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/expPaper13.ajax.php',
				method:'post',
				data:$('#paper13 #detail').serialize(),
				dateType:'html',
				success:function(d){
					console.log(d);
					window.open(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper13 .C0501',function(){
		$.ajax({
			url:'./lib/inv/printC0501.php',
			method:'post',
			async:false,
			data:{'company':$('.paper #paper13 #detail input[name="company"]').val(),'dep':$('.paper #paper13 #detail input[name="dep"]').val(),'invnumber':$(this).find('.inv').val(),'month':$(this).find('.month').val(),'machine':$(this).find('.machine').val()},
			dataType:'html',
			success:function(d){
				console.log(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.paper #paper13 .C0701',function(){
		$.ajax({
			url:'./lib/inv/printC0701.php',
			method:'post',
			async:false,
			data:{'company':$('.paper #paper13 #detail input[name="company"]').val(),'dep':$('.paper #paper13 #detail input[name="dep"]').val(),'invnumber':$(this).find('.inv').val(),'month':$(this).find('.month').val(),'machine':$(this).find('.machine').val()},
			dataType:'html',
			success:function(d){
				console.log(d);
			},
			error:function(e){
				console.log(e);
			}
		});
	});
	$(document).on('click','.paper #paper14 #search',function(){
		var start=new Date($('.paper #paper14 input[name="startdate"]').val());
		var end=new Date($('.paper #paper14 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper14.ajax.php',
				method:'post',
				data:$('#paper14 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper14 .table').html(d);
					$("#paper14 #fixTable").tableHeadFixer({"left" : 1}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper15 #search',function(){
		var start=new Date($('.paper #paper15 input[name="startdate"]').val());
		var end=new Date($('.paper #paper15 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper15.ajax.php',
				method:'post',
				data:$('#paper15 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper15 .table').html(d);
					if($('.paper #paper15 #detail input[name="classgroup"]:checked').length>0){
						$("#paper15 #fixTable").tableHeadFixer({"left" : 2}); 
					}
					else{
						$("#paper15 #fixTable").tableHeadFixer({"left" : 1}); 
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper16 #search',function(){
		var start=new Date($('.paper #paper16 input[name="startdate"]').val());
		var end=new Date($('.paper #paper16 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper16.ajax.php',
				method:'post',
				data:$('#paper16 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper16 .table').html(d);
					$("#paper16 #fixTable").tableHeadFixer({"left" : 1}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper17 #search',function(){
		var start=new Date($('.paper #paper17 input[name="startdate"]').val());
		var now=new Date();
		if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper17.ajax.php',
				method:'post',
				data:$('#paper17 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper17 .table').html(d);
					$("#paper17 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper17 #exp',function(){
		var start=new Date($('.paper #paper17 input[name="startdate"]').val());
		var now=new Date();
		if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/expPaper17.ajax.php',
				method:'post',
				data:$('#paper17 #detail').serialize(),
				dateType:'html',
				success:function(d){
					console.log(d);
					window.open(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('keypress','.paper #paper17 .setmachinedata input[name="total"]',function(event){
		if(event.which==13){
			$('.paper #paper17 .setmachinedata #send').trigger('click');
		}
	});
	$(document).on('click','.paper #paper17 .setmachinedata #send',function(){
		if($('.paper #paper17 .setmachinedata input[name="total"]').val()!=''){
			$.ajax({
				url:'./lib/js/setmachinedata.ajax.php',
				method:'post',
				async:false,
				data:{'company':'<?php echo $_SESSION["company"] ?>','dep':'<?php if($_SESSION["DB"]!="")echo $_SESSION["DB"];else echo $_SESSION["dbname"]; ?>','total':$('.paper #paper17 .setmachinedata input[name="total"]').val()},
				dataType:'html',
				success:function(d){
					if(d=='success'){
						$('.paper #paper17 #detail #search').trigger('click');
					}
					else{
						console.log(d);
					}
				},
				error:function(e){
					console.log(e);
				}
			});
		}
		else{
			alert('請填入發票組數。');
		}
	});
	$(document).on('click','.paper #paper18 #search',function(){
		var start=new Date($('.paper #paper18 input[name="startdate"]').val());
		var end=new Date($('.paper #paper18 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper18.ajax.php',
				method:'post',
				data:$('#paper18 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper18 .table').html(d);
					$("#paper18 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper18 #exp',function(){
		var start=new Date($('.paper #paper18 input[name="startdate"]').val());
		var end=new Date($('.paper #paper18 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/expPaper18.ajax.php',
				method:'post',
				data:$('#paper18 #detail').serialize(),
				dateType:'html',
				success:function(d){
					console.log(d);
					window.open(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper19 #search',function(){
		var start=new Date($('.paper #paper19 input[name="startdate"]').val());
		var end=new Date($('.paper #paper19 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/getPaper19.ajax.php',
				method:'post',
				data:$('#paper19 #detail').serialize(),
				dateType:'html',
				success:function(d){
					$('.paper #paper19 .table').html(d);
					$("#paper19 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});
	$(document).on('click','.paper #paper19 #expinit',function(){
		var start=new Date($('.paper #paper19 input[name="startdate"]').val());
		var end=new Date($('.paper #paper19 input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			$.ajax({
				url:'./lib/js/expPaper19.ajax.php',
				method:'post',
				data:$('#paper19 #detail').serialize(),
				dateType:'html',
				success:function(d){
					window.open(d);
					console.log(d);
					//$('.paper #paper19 .table').html(d);
					//$("#paper19 #fixTable").tableHeadFixer({"left" : 1,'foot':true}); 
				},
				error:function(e){
					console.log(e);
				}
			});
		}
	});

	$("#paper1 #frontbox.select_box").click(function(event){
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
    $("#paper1 #frontmod #frontbox .option a").click(function(){
        var value=$(this).text();
		var index=$('.option a').index(this);
        $(this).parent().siblings(".select_txt").text(value);
        $("input[name='front']#select_value").val($('.option a:eq('+index+')').attr('id'));
    });
});
</script>
<?php
$unit=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/unit.ini',true);
?>
<style>
.paper #paper1 .table table,
.paper #paper14 .table table,
.paper #paper15 .table table {
	font-family: Consolas,Microsoft JhengHei,sans-serif;
	border-collapse: collapse;
}
.paper #paper1 .table .title,
.paper #paper14 .table .title,
.paper #paper15 .table .title {
	border-top: 1px solid #898989;
    padding-top: 5px;
	font-weight:bold;
}
.paper #paper1 .table tr td:nth-child(even),
.paper #paper14 .table tr td:nth-child(even),
.paper #paper15 .table tr td:nth-child(even) {
	background-color:#f0f0f0;
}
.paper #paper14 .table #preday,
.paper #paper15 .table #preday {
	font-weight:bold;
}
.paper #paper1 .table #dis,
.paper #paper14 .table #dis,
.paper #paper15 .table #dis {
	color:#ff0000;
}
.paper #paper1 .table #top th,
.paper #paper14 .table #top th,
.paper #paper15 .table #top th {
	border-top: 3px solid blue;
}
.paper #paper1 .table table thead,
.paper #paper14 .table table thead,
.paper #paper15 .table table thead {
	color:#898989;
	font-size:12px;
}
.paper #paper1 .table table thead th,
.paper #paper14 .table table thead th,
.paper #paper15 .table table thead th {
	padding:0 5px;
}
.paper #paper1 .table table th,
.paper #paper14 .table table th,
.paper #paper15 .table table th {
	font-weight:normal;
}
.paper #paper1 .table table #bold,
.paper #paper14 .table table #bold,
.paper #paper15 .table table #bold {
	font-weight:bold;
	font-size:16px;
}

.paper #paper2 .table table,
.paper #paper3 .table table,
.paper #paper4 .table table,
.paper #paper5 .table table,
.paper #paper6 .table table,
.paper #paper7 .table table,
.paper #paper8 .table table,
.paper #paper9 .table table,
.paper #paper10 .table table,
.paper #paper11 .table table,
.paper #paper12 .table table,
.paper #paper13 .table table,
.paper #paper16 .table table,
.paper #paper18 .table table,
.paper #paper19 .table table {
	font-family: Consolas,Microsoft JhengHei,sans-serif;
	border-collapse: collapse;
}
.paper #paper2 .table tr:nth-child(even),
.paper #paper3 .table tr:nth-child(even),
.paper #paper4 .table tr:nth-child(even),
.paper #paper5 .table tr:nth-child(even),
.paper #paper6 .table tr:nth-child(even),
.paper #paper7 .table tr:nth-child(even),
.paper #paper8 .table tr:nth-child(even),
.paper #paper9 .table tr:nth-child(even),
.paper #paper10 .table tr:nth-child(even),
.paper #paper11 .table tr:nth-child(even),
.paper #paper12 .table tr:nth-child(even),
.paper #paper13 .table tr:nth-child(even),
.paper #paper16 .table tr:nth-child(even),
.paper #paper18 .table tr:nth-child(even),
.paper #paper19 .table tr:nth-child(even) {
	background-color:#f0f0f0;
}
.paper #paper1 .table .money div::before,
.paper #paper2 .table .money div::before,
.paper #paper3 .table .money div::before,
.paper #paper4 .table .money div::before,
.paper #paper5 .table .money div::before,
.paper #paper6 .table .money div::before,
.paper #paper7 .table .money div::before,
.paper #paper8 .table .money div::before,
.paper #paper9 .table .money div::before,
.paper #paper10 .table .money div::before,
.paper #paper11 .table .money div::before,
.paper #paper12 .table .money div::before,
.paper #paper13 .table .money div::before,
.paper #paper14 .table .money div::before,
.paper #paper15 .table .money div::before,
.paper #paper16 .table .money div::before,
.paper #paper18 .table .money div::before,
.paper #paper19 .table .money div::before {
	content:'<?php echo $unit["init"]["frontunit"]; ?>';
	margin-right:5px;
}
.paper #paper1 .table .money div::after,
.paper #paper2 .table .money div::after,
.paper #paper3 .table .money div::after,
.paper #paper4 .table .money div::after,
.paper #paper5 .table .money div::after,
.paper #paper6 .table .money div::after,
.paper #paper7 .table .money div::after,
.paper #paper8 .table .money div::after,
.paper #paper9 .table .money div::after,
.paper #paper10 .table .money div::after,
.paper #paper11 .table .money div::after,
.paper #paper12 .table .money div::after,
.paper #paper13 .table .money div::after,
.paper #paper14 .table .money div::after,
.paper #paper15 .table .money div::after,
.paper #paper16 .table .money div::after,
.paper #paper18 .table .money div::after,
.paper #paper19 .table .money div::after {
	content:'<?php echo $unit["init"]["unit"]; ?>';
}
.paper #paper2 .table #preday,
.paper #paper3 .table #preday,
.paper #paper4 .table #preday,
.paper #paper5 .table #preday,
.paper #paper6 .table #preday,
.paper #paper7 .table #preday,
.paper #paper8 .table #preday,
.paper #paper9 .table #preday,
.paper #paper10 .table #preday,
.paper #paper11 .table #preday,
.paper #paper12 .table #preday,
.paper #paper13 .table #preday,
.paper #paper16 .table #preday,
.paper #paper18 .table #preday,
.paper #paper19 .table #preday {
	font-weight:bold;
	color:#ff0000;
}
.paper #paper2 .table table thead,
.paper #paper3 .table table thead,
.paper #paper4 .table table thead,
.paper #paper5 .table table thead,
.paper #paper6 .table table thead,
.paper #paper7 .table table thead,
.paper #paper8 .table table thead,
.paper #paper9 .table table thead,
.paper #paper10 .table table thead,
.paper #paper11 .table table thead,
.paper #paper12 .table table thead,
.paper #paper13 .table table thead,
.paper #paper16 .table table thead,
.paper #paper18 .table table thead,
.paper #paper19 .table table thead {
	color:#898989;
	font-size:12px;
}
.paper #paper2 .table table th,
.paper #paper3 .table table th,
.paper #paper4 .table table th,
.paper #paper5 .table table th,
.paper #paper6 .table table th,
.paper #paper7 .table table th,
.paper #paper8 .table table th,
.paper #paper9 .table table th,
.paper #paper10 .table table th,
.paper #paper11 .table table th,
.paper #paper12 .table table th,
.paper #paper13 .table table th,
.paper #paper16 .table table th,
.paper #paper18 .table table th,
.paper #paper19 .table table th {
	padding:5px;
	font-weight:normal;
}
.paper #paper2 .table table td,
.paper #paper3 .table table td,
.paper #paper4 .table table td,
.paper #paper5 .table table td,
.paper #paper6 .table table td,
.paper #paper7 .table table td,
.paper #paper8 .table table td,
.paper #paper10 .table table td,
.paper #paper11 .table table td,
.paper #paper12 .table table td,
.paper #paper13 .table table td,
.paper #paper16 .table table td,
.paper #paper18 .table table td {
	padding:5px;
}
.paper #paper9 .table table td,
.paper #paper12 .table table td,
.paper #paper13 .table table td,
.paper #paper18 .table table td,
.paper #paper19 .table table td {
	padding:5px;
}
.paper #paper6 .table tr[class^="zcounter"],
.paper #paper6 .table .totalzcounter {
	cursor: pointer;
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
.ui-tabs .ui-tabs-nav li {
	margin:10px .2em 0 0;
}
</style>
<div class='paper' style="overflow:hidden;margin-bottom:3px;">
	<ul style='width:100%;float:left;-webkit-box-sizing: efborder-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		<li><a href='#paper6'><?php if($interface!='-1'&&isset($interface['name']['paper6tag']))echo $interface['name']['paper6tag'];else echo "即時班別資訊"; ?></a></li>
		<li><a href='#paper8' id='otherlink'><?php if($interface!='-1'&&isset($interface['name']['paper8tag']))echo $interface['name']['paper8tag'];else echo "即時其他付款資訊"; ?></a></li>
		<li><a href='#paper11'><?php if($interface!='-1'&&isset($interface['name']['paper11tag']))echo $interface['name']['paper11tag'];else echo '營業彙總'; ?></a></li>
		<li><a href='#paper1'><?php if($interface!='-1'&&isset($interface['name']['paper1tag']))echo $interface['name']['paper1tag'];else echo '商品銷售彚總'; ?></a></li>
		<li><a href='#paper14'><?php if($interface!='-1'&&isset($interface['name']['paper14tag'])){ echo $interface['name']['paper14tag']; } else{ echo '類別彚總'; } ?></a></li>
		<li><a href='#paper15'><?php if($interface!='-1'&&isset($interface['name']['paper15tag'])){ echo $interface['name']['paper15tag']; } else{ echo '分析類別彚總'; } ?></a></li>
		<li><a href='#paper2'><?php if($interface!='-1'&&isset($interface['name']['paper2tag'])){ echo $interface['name']['paper2tag']; } else{ echo '作廢帳單列表'; } ?></a></li>
		<li><a href='#paper12'><?php if($interface!='-1'&&isset($interface['name']['paper12tag'])){ echo $interface['name']['paper12tag']; } else{ echo '收/支費用明細'; } ?></a></li>
		<li><a href='#paper3'><?php if($interface!='-1'&&isset($interface['name']['paper3tag'])){ echo $interface['name']['paper3tag']; } else{ echo '商品銷售量排行'; } ?></a></li>
		<li><a href='#paper4'><?php if($interface!='-1'&&isset($interface['name']['paper4tag'])){ echo $interface['name']['paper4tag']; } else{ echo '商品銷售金額排行'; } ?></a></li>
		<li><a href='#paper5'><?php if($interface!='-1'&&isset($interface['name']['paper5tag'])){ echo $interface['name']['paper5tag']; } else{ echo '時段金額彙總'; } ?></a></li>
		<li><a href='#paper7'><?php if($interface!='-1'&&isset($interface['name']['paper7tag'])){ echo $interface['name']['paper7tag']; } else{ echo '時段來客數彙總'; } ?></a></li>
		<li><a href='#paper16'><?php if($interface!='-1'&&isset($interface['name']['paper16tag'])){ echo $interface['name']['paper16tag']; } else{ echo '時段分析類別彙總'; } ?></a></li>
		<li><a href='#paper9'><?php if($interface!='-1'&&isset($interface['name']['paper9tag'])){ echo $interface['name']['paper9tag']; } else{ echo '結帳與作廢/註銷紀錄'; } ?></a></li>
		<li><a href='#paper10'><?php if($interface!='-1'&&isset($interface['name']['paper10tag'])){ echo $interface['name']['paper10tag']; } else{ echo '免費招待統計表'; } ?></a></li>
	<?php
	if(isset($_POST['lan'])&&$_POST['lan']=='TW'){
	?>
		<li><a href='#paper18'><?php if($interface!='-1'&&isset($interface['name']['paper18tag'])){ echo $interface['name']['paper18tag']; } else{ echo '發票開立彙總'; } ?></a></li>
		<li><a href='#paper13'><?php if($interface!='-1'&&isset($interface['name']['paper13tag'])){ echo $interface['name']['paper13tag']; } else{ echo '發票開立明細表'; } ?></a></li>
		<li><a href='#paper17'><?php if($interface!='-1'&&isset($interface['name']['paper17tag'])){ echo $interface['name']['paper17tag']; } else{ echo '空白發票列表'; } ?></a></li>
	<?php
	}
	else{
	}
	?>
		<li><a href='#paper19'><?php if($interface!='-1'&&isset($interface['name']['paper19tag'])){ echo $interface['name']['paper19tag']; } else{ echo '英特拉明細'; } ?></a></li>
	</ul>
	<div id='paper1' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['paper1tag'])){ echo $interface['name']['paper1tag']; } else{ echo '商品銷售彚總'; } ?></center></h1>
		<form id='detail' style='float:left;'>
			<input type='hidden' name='lan' value='<?php if(isset($_POST['lan'])&&$_POST['lan']!='')echo $_POST['lan'];else echo '1'; ?>'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['timerange']))echo $interface['name']['timerange'];else echo '時間區間'; ?>：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='<?php if($interface!='-1'&&isset($interface['name']['search']))echo $interface['name']['search'];else echo '查詢'; ?>'><input type='button' id='expinit' value='<?php if($interface!='-1'&&isset($interface['name']['expinitdata']))echo $interface['name']['expinitdata'];else echo '匯出原始資料'; ?>'></td>
					<?php
					if(1){
					?>
					<td><input type='button' id='expuis' value='<?php if($interface!='-1'&&isset($interface['name']['expuis']))echo $interface['name']['expuis'];else echo '匯出聯合ERP檔案'; ?>'></td>
					<?php
					}
					else{
					}
					?>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper2' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>作廢帳單列表</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper3' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>商品銷售量排行</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
				</tr>
				<tr>
					<td>主區間：</td>
					<td><input type='date' name='startdate1' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate1' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
				</tr>
				<tr>
					<td>副區間：</td>
					<td><input type='date' name='startdate2' value='<?php if(date('d')==date('t'))echo date('Y-m-01',strtotime(date('Ym01').' -1 month'));else echo date('Y-m-01',strtotime(date('Ymd').' -1 month')); ?>'>～<input type='date' name='enddate2' value='<?php if(date('d')==date('t'))echo date('Y-m-t',strtotime(date('Ym01').' -1 month'));else echo date('Y-m-d',strtotime(date('Ymd').' -1 month')); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper4' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>商品銷售金額排行</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
				</tr>
				<tr>
					<td>主區間：</td>
					<td><input type='date' name='startdate1' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate1' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
				</tr>
				<tr>
					<td>副區間：</td>
					<!-- <td><input type='date' name='startdate2' value='<?php echo date('Y-m-01',strtotime(date('Ymd').' -1 month')); ?>'>～<input type='date' name='enddate2' value='<?php echo date('Y-m-d',strtotime(date('Ymd').' -1 month')); ?>'></td> -->
					<td><input type='date' name='startdate2' value='<?php if(date('d')==date('t'))echo date('Y-m-01',strtotime(date('Ym01').' -1 month'));else echo date('Y-m-01',strtotime(date('Ymd').' -1 month')); ?>'>～<input type='date' name='enddate2' value='<?php if(date('d')==date('t'))echo date('Y-m-t',strtotime(date('Ym01').' -1 month'));else echo date('Y-m-d',strtotime(date('Ymd').' -1 month')); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper5' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>時段金額彚總</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='sql' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper6' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['paper6tag']))echo $interface['name']['paper6tag'];else echo "即時班別資訊"; ?></center></h1>
		<form id='detail' style='float:left;'>
			<input type='hidden' name='lan' value='<?php if(isset($_POST['lan'])&&$_POST['lan']!='')echo $_POST['lan'];else echo '1'; ?>'>
			<table>
				<?php
				/*include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../menudata/'.$_SESSION['company'].'/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".$_SESSION['usergroup'].") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}*/
				?>
				<input type='hidden' name='dbname' value='<?php echo $_SESSION['DB']; ?>'>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['bizdate']))echo $interface['name']['bizdate'];else echo '營業日'; ?>：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='<?php if($interface!='-1'&&isset($interface['name']['search']))echo $interface['name']['search'];else echo '查詢'; ?>'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper7' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>時段來客數彚總</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
				<tr>
					<td>結算時間：</td>
					<td><label><input type='radio' name='type' value='CREATEDATETIME' checked>開單時間</label>、<label><input type='radio' name='type' value='UPDATEDATETIME'>結帳時間</label></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper8' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>即時其他付款資訊</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<input type='hidden' name='dbname' value='<?php echo $_SESSION['DB']; ?>'>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper9' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>結帳與作廢/註銷紀錄</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper10' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>免費招待統計表</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper11' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>營業彙總</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<input type='hidden' name='dbname' value='<?php echo $_SESSION['DB']; ?>'>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper12' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>收/支費用明細</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<?php
	if(isset($_POST['lan'])&&$_POST['lan']=='TW'){
	?>
	<div id='paper13' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>發票開立明細表</center></h1>
		<form id='detail' style='float:left;'>
			<?php
			if(isset($_POST['admin'])&&$_POST['admin']==1){
				echo '<input type="hidden" name="admin">';
			}
			else{
			}
			?>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'><input type='hidden' name='dep' value='<?php echo $_SESSION['DB']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'><input type='button' id='exp' value='匯出'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<?php
	}
	else{
	}
	?>
	<div id='paper14' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>類別彚總</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='rearmod'>
							<ul>
								<li>
									<div class='select_box' id='rearbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='rear' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper15' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>分析類別彚總</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='rearmod'>
							<ul>
								<li>
									<div class='select_box' id='rearbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='rear' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
				</tr>
				<tr>
					<td></td>
					<td><label style='margin-right:20px;'><input type='checkbox' name='classgroup' checked>依照每日班別</label><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper16' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>時段分析類別彚總</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<?php
	if(isset($_POST['lan'])&&$_POST['lan']=='TW'){
	?>
	<div id='paper17' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>空白發票列表</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>字軌期別：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'>
						<select name='startdate'>
						<?php
						//回推5期
						if(intval(date('m'))%2==0){
							$sinvdate=strtotime(date('Y-m').' -11 month');
						}
						else{
							$sinvdate=strtotime(date('Y-m').' -10 month');
						}
						for($i=0;$i<6;$i++){
							echo '<option value="'.date('Ym',strtotime(date('Y-m',$sinvdate).' +1 month')).'" ';
							if($i==5){
								echo 'selected';
							}
							else{
							}
							echo '>'.date('Y/m',$sinvdate).'~'.date('Y/m',strtotime(date('Y-m',$sinvdate).' +1 month')).'</option>';
							$sinvdate=strtotime(date('Y-m',$sinvdate).' +2 month');
						}
						?>
						</select>
					</td>
					<td><input type='button' id='search' value='查詢'><input type='button' id='exp' value='匯出空白發票區段'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<div id='paper18' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>發票開立彙總</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'><input type='button' id='exp' value='匯出'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<?php
	}
	else{
	}
	?>
	<div id='paper19' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center>英特拉明細</center></h1>
		<form id='detail' style='float:left;'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
				if(substr($_SESSION['usergroup'],0,5)=='boss-'){
					$sql="SELECT * FROM dblist WHERE `group`=0 AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else if(substr($_SESSION['usergroup'],0,6)=='group-'){
					$sql="SELECT * FROM dblist WHERE `group`=".substr($_SESSION['usergroup'],6)." AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				else{
					$sql="SELECT DISTINCT no,dep,name FROM dblist WHERE no IN (".substr($_SESSION['usergroup'],5).") AND state=1";
					$db=sqlquery($conn,$sql,'sqlite');
				}
				sqlclose($conn,'sqlite');
				if(sizeof($db)>1){
					echo "<div class='mod_select' id='frontmod'>
							<ul>
								<li>
									<div class='select_box' id='frontbox'>";
								echo '<span class="select_txt">'.$db[0]['name'].'</span><a class="selet_open">▼</a><div class="option">';
					foreach($db as $t){
						echo '<a id="'.$t['dep'].'">'.$t['name'].'</a>';
					}
								echo "</div>
								</li>
							</ul>";
						echo "<input type='hidden' name='front' id='select_value' value='".$db[0]['dep']."'>";
					echo "</div>";
				}
				else if(sizeof($db)==0){
				?>
				<input type='hidden' name='dbname' value='<?php echo $sql; ?>'>
				<?php
				}
				else{
				?>
				<input type='hidden' name='dbname' value='<?php echo $db[0]['dep']; ?>'>
				<?php
				}
				?>
				<tr>
					<td>時間區間：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='查詢'><input type='button' id='expinit' value='<?php if($interface!='-1'&&isset($interface['name']['expinitdata']))echo $interface['name']['expinitdata'];else echo '匯出原始資料'; ?>'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
</div>