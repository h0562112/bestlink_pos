<?php
$print=parse_ini_file('../../../database/printlisttag.ini',true);
echo $print['item']['tempbuytype'].',';
if(isset($print['item']['printclientlist'])){
	echo $print['item']['printclientlist'];
}
else{
}
?>