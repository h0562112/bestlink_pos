<head>
	<meta charset="UTF-8">
	<title>遠端多媒體看板</title>
</head>
<?php
$machinedata=parse_ini_file('../../../../database/machinedata.ini',true);
/*$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://'.$machinedata['posdvr']['path'].'/login.cgi?na=admin&pa=admin');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:text/html;charset=BIG5'));
$Result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
	print_r('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
}
curl_close($ch);
echo $Result;*/
/*

*/
?>
<script src="../../../../tool/jquery-1.12.4.js"></script>
<script src="../../../../tool/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).ready(function(){
	$('#pass').submit();
});
</script>
<form id='pass' action='http://<?php echo $machinedata['posdvr']['path']; ?>/login.cgi' method='post'>
<input type='hidden' name='na' value='admin'>
<input type='hidden' name='pa' value='admin'>
</form>