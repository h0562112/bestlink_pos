<?php
include_once 'yunlincoins_api.php';

$yunlincoins=parse_ini_file('../../../../database/yunlincoins.ini',true);

if(isset($_POST['senderaccount'])){
}
else{
	$_POST['senderaccount']='';
}
if(isset($_POST['senderamount'])){
}
else{
	$_POST['senderamount']='0';
}

print_r(GiveCoins($yunlincoins['init']['url'],'post',$_POST['token'],$yunlincoins['init']['key'],$yunlincoins['init']['iv'],$_POST['senderaccount'],$_POST['receiveraccount'],$_POST['senderamount'],$_POST['receiveramount']));
?>