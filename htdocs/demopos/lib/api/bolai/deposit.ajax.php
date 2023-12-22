<?php
$machinedata=parse_ini_file('../../../../database/machinedata.ini',true);

$ch = curl_init("http://pos.mytouch.tw/pos/api/v1/index.php/point/deposit");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, 'type='.$_POST['type'].'&value='.$_POST['memcard'].'&price='.$_POST['money']);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'api_key: '.$machinedata['bolai']['api_key']
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
echo $result;
?>