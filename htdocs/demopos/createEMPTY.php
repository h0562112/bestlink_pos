<?php
include_once './lib/js/create.emptyDB.php';
//create($filename,$path1='../sql/',$path2='../../../database/sale/',$toolpath='../../../tool/')
if(!file_exists('../database/sale/EMinvdata.db')&&file_exists('./lib/sql/EMinvdata.ini')){
	create('EMinvdata','./lib/sql/','../database/sale/','../tool/');
}
else{
}
if(!file_exists('../database/sale/EMpoint.db')&&file_exists('./lib/sql/EMpoint.ini')){
	create('EMpoint','./lib/sql/','../database/sale/','../tool/');
}
else{
}
if(!file_exists('../database/sale/empty.db')&&file_exists('./lib/sql/empty.ini')){
	create('empty','./lib/sql/','../database/sale/','../tool/');
}
else{
}
if(!file_exists('../database/sale/EMtemp.db')&&file_exists('./lib/sql/EMtemp.ini')){
	create('EMtemp','./lib/sql/','../database/sale/','../tool/');
}
else{
}
if(!file_exists('../database/sale/memsalelist.db')&&file_exists('./lib/sql/memsalelist.ini')){
	create('memsalelist','./lib/sql/','../database/sale/','../tool/');
}
else{
}
if(!file_exists('../database/person/punch.db')&&file_exists('./lib/sql/punch.ini')){
	create('punch','./lib/sql/','../database/person/','../tool/');
}
else{
}
if(!file_exists('../database/sale/Cover.db')&&file_exists('./lib/sql/Cover.ini')){
	create('Cover','./lib/sql/','../database/sale/','../tool/');
}
else{
}
?>