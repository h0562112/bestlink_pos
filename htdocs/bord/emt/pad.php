<!DOCTYPE html>
<?php
$set=parse_ini_file('./set.ini',true);
?>
<head>
	<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'>
	<script src='../tool/jquery-1.12.4.js'></script>
	<script src="../tool/fastclick/lib/fastclick.js"></script>
	<script>
		$(document).ready(function(){
			$(function() {
				if(typeof FastClick!=="undefined"){
					FastClick.attach(document.body);
				}
				else{
				}
			});
			$('button[data-id="numbut"]').click(function(){
				if($('#state').val()=='0'){
					$('#state').val('0');
				}
				else{
					$('#state').val('0');
					$('#Num').val('');
				}
				if($('#Num').val()!='0'){
					if($('#Num').val().length < 3){
						$('#Num').val($('#Num').val()+$(this).find('#num').val());
					}
				}
				else{
					if($(this).find('#num').val()==0){
					}
					else{
						$('#Num').val($(this).find('#num').val());
					}
				}
			});
			$('#diff').click(function(){
				if($('#Num').val()==''||$('#Num').val()=='0'){
				}
				else{
					$('#Num').val(parseInt($('#Num').val())-1);
				}	
			});
			$('#add').click(function(){
				if($('#Num').val()=='999'){
				}
				else{
					$('#Num').val(parseInt($('#Num').val())+1);
				}	
			});
			$('#ac').click(function(){
				$('#Num').val('0');
				$('#state').val('0');
			});
			$('#call').click(function(){
				if($('#Num').val()==''){
				}
				else{
					$.ajax({
						url:'./notrealpush.ajax.php',
						method:'post',
						async:false,
						data:{'target':"<?php echo $set['set']['target']; ?>",'sendvar':$('#Num').val()},
						dataType:'html',
						success:function(d){
							//console.log(d);
						},
						error:function(e){
							//console.log(e);
						}
					});
					$('#state').val('1');
				}
			});
			$('#printticket').click(function(){
				$.ajax({
					url:'./bordticket.ajax.php',
					method:'post',
					async:false,
					dataType:'json',
					success:function(d){
						//console.log(d);
					},
					error:function(e){
						//console.log(e);
					}
				});
			});
		});
	</script>
	<style>
		body {
			width:100vw;
			height:90vh;
			margin:0;
			padding:0;
			overflow:hidden;
		}
		#Num {
			width:calc(100% - 4px);
			height:calc(100% - 6px);
			margin:1px;
			padding:1px 0;
			font-size:90px;
			color:#ff0000;
			text-align:right;
			border:1px solid #000000;
			border-radius:5px;
		}
		button[data-id="numbut"],
		#ac,
		#add,
		#diff,
		#call,
		#printticket {
			width:calc(25% - 2px);
			height:calc(20% - 2px);
			margin:1px;
			float:left;
			padding:0;
			font-size:15vw;
			text-align:center;
			border:1px solid #898989;
			border-radius:5px;
			font-family: Consolas,Microsoft JhengHei,sans-serif;
		}
	</style>
</head>
<body>
	<div style='width:100%;height:20%;float:left;overflow:hidden;'>
		<input type="text" id="Num" readonly value="<?php
		$var=0;
		if(isset($_GET['var'])){
			$var=$_GET['var'];
		}
		if($var!=0){
			echo $var;
		}
		else{
			echo '0';
		}   
		?>">
		<input type="hidden" id="state" value="0">
	</div>
	<button class='needsclick' data-id='numbut'>7<input type='hidden' id='num' value='7'></button>
	<button class='needsclick' data-id='numbut'>8<input type='hidden' id='num' value='8'></button>
	<button class='needsclick' data-id='numbut'>9<input type='hidden' id='num' value='9'></button>
	<button class='needsclick' id='ac'>AC</button>
	<button class='needsclick' data-id='numbut'>4<input type='hidden' id='num' value='4'></button>
	<button class='needsclick' data-id='numbut'>5<input type='hidden' id='num' value='5'></button>
	<button class='needsclick' data-id='numbut'>6<input type='hidden' id='num' value='6'></button>
	<button class='needsclick' id='add'>+</div>
	<button class='needsclick' data-id='numbut'>1<input type='hidden' id='num' value='1'></button>
	<button class='needsclick' data-id='numbut'>2<input type='hidden' id='num' value='2'></button>
	<button class='needsclick' data-id='numbut'>3<input type='hidden' id='num' value='3'></button>
	<button class='needsclick' id='diff'>-</button>
	<button class='needsclick' data-id='numbut'>0<input type='hidden' id='num' value='0'></button>
	<button class='needsclick' id='call' style="width:calc(50% - 2px);">叫號</button>
	<button class='needsclick' id='printticket' style='font-size:12vw;line-height:8vh;'>出單</button>
</body>