<?php
$initsetting=parse_ini_file('../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($initsetting['init']['settime']);
if($_POST['type']=='in'){
	if(file_exists("./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$_POST['tabnum'].".ini")){//2020/3/16 iconv('utf-8','gb2312',$_POST['tabnum']) >> $_POST['tabnum'] ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
		include_once '../tool/inilib.php';
		$tabini=parse_ini_file("./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$_POST['tabnum'].".ini",true);//2020/3/16 iconv('utf-8','big5',$_POST['tabnum']) >> $_POST['tabnum'] ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
		if(isset($tabini[$_POST['tabnum']]['state'])&&$tabini[$_POST['tabnum']]['state']=='0'){
			echo 'empty-'.$tabini[$_POST['tabnum']]['machine'];
			if(strstr($tabini[$_POST['tabnum']]['table'],',')){
				$tablist=preg_split('/,/',$tabini[$_POST['tabnum']]['table']);
				foreach($tablist as $tl){
					$temp=parse_ini_file("./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$tl.".ini",true);//2020/3/16 iconv('utf-8','big5',$tl) >> $tl ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
					$temp[$tl]['state']='999';
					if(isset($_POST['submachine'])&&$_POST['submachine']!='empty'){
						$temp[$tl]['machine']=$_POST['submachine'];
					}
					else if(isset($_POST['machine'])&&$_POST['machine']!='empty'){
						$temp[$tl]['machine']=$_POST['machine'];
					}
					else{
						$temp[$tl]['machine']="m1";
					}
					write_ini_file($temp,"./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$tl.".ini");//2020/3/16 iconv('utf-8','big5',$tl) >> $tl ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
				}
			}
			else{
				$tabini[$_POST['tabnum']]['state']='999';
				if(isset($_POST['submachine'])&&$_POST['submachine']!='empty'){
					$tabini[$_POST['tabnum']]['machine']=$_POST['submachine'];
				}
				else if(isset($_POST['machine'])&&$_POST['machine']!='empty'){
					$tabini[$_POST['tabnum']]['machine']=$_POST['machine'];
				}
				else{
					$tabini[$_POST['tabnum']]['machine']="m1";
				}
				write_ini_file($tabini,"./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$_POST['tabnum'].".ini");//2020/3/16 iconv('utf-8','big5',$_POST['tabnum']) >> $_POST['tabnum'] ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
			}
		}
		else if(isset($tabini[$_POST['tabnum']]['state'])&&$tabini[$_POST['tabnum']]['state']=='999'&&($tabini[$_POST['tabnum']]['machine']!=$_POST['submachine']||$_POST['submachine']=='empty')&&($tabini[$_POST['tabnum']]['machine']!=$_POST['machine']||$_POST['machine']=='empty')){
			echo 'lock-'.$tabini[$_POST['tabnum']]['machine'];
		}
		else{
			echo 'unlock-'.$tabini[$_POST['tabnum']]['machine'];
			if(strstr($tabini[$_POST['tabnum']]['table'],',')){
				$tablist=preg_split('/,/',$tabini[$_POST['tabnum']]['table']);
				foreach($tablist as $tl){
					$temp=parse_ini_file("./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$tl.".ini",true);//2020/3/16 iconv('utf-8','big5',$tl) >> $tl ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
					$temp[$tl]['state']='999';
					if(isset($_POST['submachine'])&&$_POST['submachine']!='empty'){
						$temp[$tl]['machine']=$_POST['submachine'];
					}
					else if(isset($_POST['machine'])&&$_POST['machine']!='empty'){
						$temp[$tl]['machine']=$_POST['machine'];
					}
					else{
						$temp[$tl]['machine']="m1";
					}
					write_ini_file($temp,"./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$tl.".ini");//2020/3/16 iconv('utf-8','big5',$tl) >> $tl ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
				}
			}
			else{
				$tabini[$_POST['tabnum']]['state']='999';
				if(isset($_POST['submachine'])&&$_POST['submachine']!='empty'){
					$tabini[$_POST['tabnum']]['machine']=$_POST['submachine'];
				}
				else if(isset($_POST['machine'])&&$_POST['machine']!='empty'){
					$tabini[$_POST['tabnum']]['machine']=$_POST['machine'];
				}
				else{
					$tabini[$_POST['tabnum']]['machine']="m1";
				}
				write_ini_file($tabini,"./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$_POST['tabnum'].".ini");//2020/3/16 iconv('utf-8','big5',$_POST['tabnum']) >> $_POST['tabnum'] ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
			}
		}
	}
	else{
		$fileini=fopen("./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$_POST['tabnum'].".ini",'a');//2020/3/16 iconv('utf-8','big5',$_POST['tabnum']) >> $_POST['tabnum'] ；因為檔名轉換成對應方式，所以不會出現中文檔名，也就不用轉碼
		fwrite($fileini,'['.$_POST['tabnum'].']'.PHP_EOL);
		fwrite($fileini,'bizdate="'.$_POST['bizdate'].'"'.PHP_EOL);
		fwrite($fileini,'zcounter="'.$_POST['zcounter'].'"'.PHP_EOL);
		fwrite($fileini,'consecnumber=""'.PHP_EOL);
		fwrite($fileini,'saleamt=""'.PHP_EOL);
		fwrite($fileini,'person=""'.PHP_EOL);
		fwrite($fileini,'createdatetime="'.date('YmdHis').'"'.PHP_EOL);
		fwrite($fileini,'table="'.$_POST['tabnum'].'"'.PHP_EOL);
		if(strstr($_POST['tabnum'],',')){
			fwrite($fileini,'tablestate="1"'.PHP_EOL);
		}
		else{
			fwrite($fileini,'tablestate="0"'.PHP_EOL);
		}
		fwrite($fileini,'state="999"'.PHP_EOL);
		if(isset($_POST['submachine'])&&$_POST['submachine']!='empty'){
			fwrite($fileini,'machine="'.$_POST['submachine'].'"'.PHP_EOL);
			echo 'unlock-'.$_POST['submachine'];
		}
		else if(isset($_POST['machine'])&&$_POST['machine']!='empty'){
			fwrite($fileini,'machine="'.$_POST['machine'].'"'.PHP_EOL);
			echo 'unlock-'.$_POST['machine'];
		}
		else{
			fwrite($fileini,'machine="m1"'.PHP_EOL);
			echo 'unlock-m1';
		}
		fclose($fileini);
	}
}
else{
}
?>