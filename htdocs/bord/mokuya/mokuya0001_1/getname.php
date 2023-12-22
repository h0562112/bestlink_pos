<?php
/*function fbLikeCount($id,$appid,$appsecret){
  $json_url ='https://graph.facebook.com/'.$id.'?access_token='.$appid.'|'.$appsecret.'&fields=name,fan_count';
  $json = file_get_contents($json_url);
  $json_output = json_decode($json);
  //Extract the likes count from the JSON object
  if($json_output->name){
	return $fan_count = $json_output->name;
  }else{
    return 0;
  }
}*/
$content=parse_ini_file('./data/content.ini',true);
//if(isset($content['fbid']['name'])&&strlen($content['fbid']['name'])>0){
	echo "<span style='float:left;font-size:20px;font-family: Arial,Microsoft JhengHei;color:#000000;'>".$content['fbid']['name']."</span>";
/*}
else{
	if(isset($_GET['id'])){
		echo "<span style='float:left;font-size:20px;font-family: Arial,Microsoft JhengHei;color:#000000;'>".fbLikeCount($_GET['id'],$_GET['appid'],$_GET['appsecret'])."</span>";
	}
}*/
?>