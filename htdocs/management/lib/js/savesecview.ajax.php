<?php
//print_r($_POST);
print_r($_FILES);
$temp='';
if(isset($_FILES)){
	if(isset($_FILES['logo']['type'])&&($_FILES['logo']['type']!=""&&$_FILES['logo']['type']!='image/png'&&$_FILES['logo']['type']!='image/jpeg')){
		$temp=1;
	}
	else{
	}
	if(isset($_FILES['imglist']['type'])&&$_FILES['imglist']['type'][0]!=""){
		for($i=0;$i<sizeof($_FILES['imglist']['type']);$i++){
			if($_FILES['imglist']['type'][$i]!='image/png'&&$_FILES['imglist']['type'][$i]!='image/jpeg'){
				$temp=1;
				break;
			}
			else{
			}
		}
	}
	else{
	}
}
else{
}
if($temp==1){
	echo 'img error';
}
else{
	include_once '../../../tool/inilib.php';
	if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img')){
	}
	else{
		mkdir('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img');
	}
	if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/secview.ini')){
	}
	else{
		$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/secview.ini','w');
		fwrite($f,'[title]'.PHP_EOL);
		fwrite($f,'text=""'.PHP_EOL);
		fwrite($f,'[marquee]'.PHP_EOL);
		fwrite($f,'text=""'.PHP_EOL);
		fwrite($f,'color=""'.PHP_EOL);
		fwrite($f,'speed=""'.PHP_EOL);
		fwrite($f,'[leftimg]'.PHP_EOL);
		fwrite($f,'type="1"'.PHP_EOL);
		fwrite($f,'imgspeed="2.5"'.PHP_EOL);
		fwrite($f,'imgnum="1"'.PHP_EOL);
		fwrite($f,'trnspeed=""'.PHP_EOL);
		fwrite($f,'beginvideo=""'.PHP_EOL);
		fwrite($f,'videolist=""'.PHP_EOL);
		fwrite($f,'maxvolum=""'.PHP_EOL);
		fwrite($f,'[rightlist]'.PHP_EOL);
		fwrite($f,'maxitems="20"'.PHP_EOL);
		fclose($f);
	}
	$secview=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/secview.ini',true);
	$secview['title']['text']=$_POST['greetings'];
	$secview['marquee']['text']=$_POST['marquee'];
	$secview['marquee']['color']=$_POST['marqueecolor'];
	$secview['marquee']['speed']=$_POST['speed'];
	$secview['leftimg']['imgnum']=$_POST['imgnum'];
	write_ini_file($secview,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/secview.ini');
	//print_r($_FILES);
	if(isset($_FILES['logo'])&&$_FILES['logo']['name']!=''){
		if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/logo.png')){
			unlink('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/logo.png');
		}
		else{
		}
		if(move_uploaded_file($_FILES['logo']['tmp_name'],'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/logo.png')){
			//echo 'upload success';
		}
		else{
			//echo 'upload fail';
		}
	}
	else{
	}
	for($i=1;$i<=$_POST['imgnum'];$i++){
		if(isset($_FILES['imglist'.$i])&&$_FILES['imglist'.$i]['name']!=''){
			if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist')){
			}
			else{
				mkdir('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist');
			}
			if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist/'.$_FILES['imglist'.$i]['name'])){
				unlink('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist/'.$_FILES['imglist'.$i]['name']);
			}
			else{
			}
			$tempname=preg_split('/\./',$_FILES['imglist'.$i]['name']);
			//echo $_FILES['imglist'.$i]['name'];
			//print_r($tempname);
			if(move_uploaded_file($_FILES['imglist'.$i]['tmp_name'],'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist/'.$i.'.png')){
				//echo 'upload success';
			}
			else{
				//echo 'upload fail';
			}
		}
		else{
		}
	}
	/*if(isset($_FILES['imglist'])&&$_FILES['imglist']['name'][0]!=''){
		if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist')){
		}
		else{
			mkdir('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist');
		}
		for($i=0;$i<sizeof($_FILES['imglist']['name']);$i++){
			if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist/'.$_FILES['imglist']['name'][$i])){
				unlink('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist/'.$_FILES['imglist']['name'][$i]);
			}
			else{
			}
			if(move_uploaded_file($_FILES['imglist']['tmp_name'][$i],'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/img/imglist/'.$_FILES['imglist']['name'][$i])){
				//echo 'upload success';
			}
			else{
				//echo 'upload fail';
			}
		}
	}
	else{
	}*/
}
?>