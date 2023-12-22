<!DOCTYPE html>
<?php
include_once '../../../../tool/inilib.php';
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
$setup=parse_ini_file('../../../../database/setup.ini',true);

date_default_timezone_set($initsetting['init']['settime']);
//2020/10/30 nodejs設定值
if(isset($setup['nodejsaddress']['nodejsip'])){
}
else{
	$setup['nodejsaddress']['nodejsip']='127.0.0.1';
}
if(isset($setup['nodejsaddress']['nodejsport'])){
}
else{
	$setup['nodejsaddress']['nodejsport']='3700';
}
?>
<html>
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>NovaFace辨識</title>
	<script src="../../../../tool/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src='../../../../nodejs/node_modules/socket.io-client/dist/socket.io.js'></script>
	<script>
		$(document).ready(function(){
			var socket = io.connect('http://<?php echo $setup["nodejsaddress"]["nodejsip"]; ?>:<?php echo $setup["nodejsaddress"]["nodejsport"]; ?>');
			//console.log(mydata['name']);
			socket.emit('join','faceid');
			socket.on('joinsuccess',function(msg){
				//mydata['id']=id;
				console.log(msg);
			});
			socket.on('disconnect',function(){//2020/11/11 server離線
				console.log('server disconnect');
				socket.emit('join','faceid');//重新登入
			});
			<?php
			if(!file_exists('../../../../print/faceid/'.date('Y').'/'.date('m').'/'.date('d').'/faceid_log.ini')){
				if(!file_exists('../../../../print/faceid/'.date('Y').'/'.date('m').'/'.date('d'))){
					if(!file_exists('../../../../print/faceid/'.date('Y').'/'.date('m'))){
						if(!file_exists('../../../../print/faceid/'.date('Y'))){
							mkdir('../../../../print/faceid/'.date('Y'));
						}
						else{
						}
						mkdir('../../../../print/faceid/'.date('Y').'/'.date('m'));
					}
					else{
					}
					mkdir('../../../../print/faceid/'.date('Y').'/'.date('m').'/'.date('d'));
				}
				else{
				}
				$f=fopen('../../../../print/faceid/'.date('Y').'/'.date('m').'/'.date('d').'/faceid_log.ini','w');
				fclose($f);
			}
			else{
			}
			$faceid_log=parse_ini_file('../../../../print/faceid/'.date('Y').'/'.date('m').'/'.date('d').'/faceid_log.ini',true);
			
			$timetag=date('YmdHis');
			$faceid_log[$timetag]['tel']=$_GET['id'];
			$faceid_log[$timetag]['name']=$_GET['name'];
			$faceid_log[$timetag]['type']=$_GET['type'];
			$faceid_log[$timetag]['status']=$_GET['status'];
			write_ini_file($faceid_log,'../../../../print/faceid/'.date('Y').'/'.date('m').'/'.date('d').'/faceid_log.ini');
			echo "socket.emit('identify',['m1','".$timetag."']);";
			$('#identify_message').html('會員：<?php echo $_GET['name'].' '.$_GET['id'].' '.$_GET['msg'] ;?>');
			?>
			/*status  = getUrlParameter("status");
			type    = getUrlParameter("type");
			uid     = getUrlParameter("id");
			name    = getUrlParameter("name");*/

		});
	</script>
</head>
<body>
	<div class="container-fluid" align="center">
		<div class="container">
      			<font id="identify_message" size="5"></font>
    		</div>
		<button style='font-size:25px;' onclick='window.close()'>關閉網頁</button>
	</div>    
</body>

</html>