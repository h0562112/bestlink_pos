<?php
function name($id){
	$json_url ='http://data.gcis.nat.gov.tw/od/data/api/F05D1060-7D57-4763-BDCE-0DAF5975AFE0?$format=json&$filter=Business_Accounting_NO eq '.$id;
	$json = file_get_contents($json_url);
	$json_output = json_decode($json);
	//Extract the likes count from the JSON object
	if($json_output->Company_Name){
		return $fan_count = $json_output->Company_Name;
	}else{
		return 0;
	}
}
if(isset($_GET['id'])){
	echo "<style>
			#number {
				font:30px/10px Arial;
			}
			@media screen and (min-device-width:1030px) and (min-device-height:750px),screen and (min-device-height:1030px) and (min-device-width:750px) {
				#number {
					font:40px/40px Arial;
				}
			}
			@media screen and (min-device-width:1370px) and (min-device-height:1070px),screen and (min-device-height:1370px) and (min-device-width:1070px) {
				#number {
					font:70px/70px Arial;
				}
			}
		</style>";
	echo "<span id='number' style='float:right;color:#ffffff;'>".name($_GET['id'])."</span>";
}
?>