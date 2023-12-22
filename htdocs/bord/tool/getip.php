<?php
/*
可回傳IP
若local端與此伺服器端在同網路下
則回傳的IP為區網IP
相反
則回傳對外IP
*/
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
   $myip = $_SERVER['HTTP_CLIENT_IP'];
}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
   $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
   $myip= $_SERVER['REMOTE_ADDR'];
}
echo $myip;
?>