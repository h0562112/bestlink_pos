<?php
include_once "./nidin_api_inc.php";

$setup=parse_ini_file('../../../../database/setup.ini',true);

echo json_encode(Logout($setup['nidin']['url'],"post",$_POST["Token"],$_POST["User"]));
?>