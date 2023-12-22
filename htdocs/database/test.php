<?php
$csv = array_map('str_getcsv', file('./love.csv'));
print_r($csv);
?>