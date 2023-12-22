<?php
function fbLikeCount($id,$appid,$appsecret){
  $json_url ='https://graph.facebook.com/'.$id.'?access_token='.$appid.'|'.$appsecret.'&fields=fan_count';
  $json = file_get_contents($json_url);
  $json_output = json_decode($json);
  //Extract the likes count from the JSON object
  if($json_output->fan_count){
	return $fan_count = $json_output->fan_count;
  }else{
    return 0;
  }
}
$content=parse_ini_file('./data/content.ini',true);
if(isset($content['fbid']['person'])&&intval($content['fbid']['person'])>0){
	echo "<span style='float:right;font-size:40px;font-family: Microsoft JhengHei;color:#000000;font-weight:bold;'>".$content['fbid']['person']."</span>";
}
else{
	if(isset($_GET['id'])&&strlen($_GET['id'])>0){
		echo "<span style='float:right;font-size:40px;font-family: Microsoft JhengHei;color:#000000;font-weight:bold;'>".fbLikeCount($_GET['id'],$_GET['appid'],$_GET['appsecret'])."</span>";
	}
}
//header('refresh: 5;url="getlike.php?id='.$_GET['id'].'"');
?>