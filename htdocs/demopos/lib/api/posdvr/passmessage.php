<?php
$machinedata=parse_ini_file('../../../../database/machinedata.ini',true);
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
if($_GET['filename']=='cashdrawer'){
}
else{
	if(file_exists('../../../../print/posdvr/'.$_GET['filename'].'.txt')){
		$content='';
		$f=fopen('../../../../print/posdvr/'.$_GET['filename'].'.txt','r');
		while(!feof($f)){
			$content.=fgets($f);
		}
		fclose($f);
		unlink('../../../../print/posdvr/'.$_GET['filename'].'.txt');
		$f=fopen('../../../../posdvr-log.txt','a');
		fwrite($f,date('Y/m/d H:i:s').' --- '.$content.PHP_EOL);
		fclose($f);
	}
	else{
		date_default_timezone_set('Asia/Taipei');
		$f=fopen('../../../../posdvr-error.txt','a');
		fwrite($f,date('Y/m/d H:i:s').' --- '.$_GET['filename'].' file is not exists'.PHP_EOL);
		fclose($f);
	}
}

//echo $key;
/*
$tempcontent='';
if(isset($initsetting['init']['posdvrmachine'])){
	$tempcontent=$initsetting['init']['posdvrmachine']." ";
}
else{
	$tempcontent="01 ";
}
if($_GET['filename']=='cashdrawer'){
	$tempcontent=$tempcontent.$_GET['filename']." 開錢櫃";
}
else{
	$tempcontent=$tempcontent.$content;
}
$PostData = array(
	"input" =>$tempcontent
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://'.$machinedata['posdvr']['path'].'/protect/PosWrite.htm');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept:BIG5','accept-charset:BIG5'));
curl_setopt($ch, CURLOPT_COOKIE, 'auth='.$machinedata['posdvr']['key']);
$Result = curl_exec($ch);
curl_close($ch);
echo $Result;
*/
?>
<script src="../../../../tool/jquery-1.12.4.js"></script>
<script src="../../../../tool/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).ready(function(){
	document.cookie="auth=<?php echo $machinedata['posdvr']['key']; ?>";
	$('#pass').submit();
});
</script>
<form id='pass' method='post' action='http://<?php echo $machinedata["posdvr"]["path"]; ?>/protect/PosWrite.htm' accept="BIG5" accept-charset="BIG5">
	<textarea name='input'><?php 
	if(isset($initsetting['init']['posdvrmachine'])){
		echo $initsetting['init']['posdvrmachine']." ";
	}
	else{
		echo "01 ";
	}
	if($_GET['filename']=='cashdrawer'){
		echo $_GET['filename']." 開錢櫃";
	}
	else{
		echo $content;
	}
	?></textarea>
</form>
