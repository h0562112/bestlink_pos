<?php
function creinitsetting($company,$dep){
	include_once '../../../tool/inilib.php';
	$f=fopen('../../../menudata/'.$company.'/'.$dep.'/initsetting.ini','w');
	fclose($f);
	$initsetting_val=parse_ini_file('./inifile/initsetting_val.ini',true);
	$initsetting=array();
	foreach($initsetting_val as $section=>$data){
		foreach($data as $name=>$value){
			$initsetting[$section][$name]=$value;
		}
	}
	write_ini_file($initsetting,'../../../menudata/'.$company.'/'.$dep.'/initsetting.ini');
}
function creprintlisttag($company,$dep){
	include_once '../../../tool/inilib.php';
	$f=fopen('../../../menudata/'.$company.'/'.$dep.'/printlisttag.ini','w');
	fclose($f);
	$printlisttag_val=parse_ini_file('./inifile/printlisttag_val.ini',true);
	$printlisttag=array();
	foreach($printlisttag_val as $section=>$data){
		foreach($data as $name=>$value){
			$printlisttag[$section][$name]=$value;
		}
	}
	write_ini_file($printlisttag,'../../../menudata/'.$company.'/'.$dep.'/printlisttag.ini');
}
function cresetup($company,$dep){
	include_once '../../../tool/inilib.php';
	$f=fopen('../../../menudata/'.$company.'/'.$dep.'/setup.ini','w');
	fclose($f);
	$setup_val=parse_ini_file('./inifile/setup_val.ini',true);
	$setup=array();
	foreach($setup_val as $section=>$data){
		foreach($data as $name=>$value){
			$setup[$section][$name]=$value;
		}
	}
	write_ini_file($setup,'../../../menudata/'.$company.'/'.$dep.'/setup.ini');
}
function creorderweb($company,$dep){
	include_once '../../../tool/inilib.php';
	$f=fopen('../../../menudata/'.$company.'/'.$dep.'/orderweb.ini','w');
	fclose($f);
	$orderweb_val=parse_ini_file('./inifile/orderweb_val.ini',true);
	$orderweb=array();
	foreach($orderweb_val as $section=>$data){
		foreach($data as $name=>$value){
			$orderweb[$section][$name]=$value;
		}
	}
	write_ini_file($orderweb,'../../../menudata/'.$company.'/'.$dep.'/orderweb.ini');
}
?>