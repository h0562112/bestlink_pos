<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
$res=[["state"=>"success","message"=>"pass connsect"]];
echo json_encode($res);
?>