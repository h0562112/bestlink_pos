<?php
if($_POST['type']=='in'){
	if(file_exists("./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$_POST['tabnum'].".ini")){//2020/3/16 iconv('utf-8','big5',$_POST['tabnum']) >> $_POST['tabnum'] �F�]���ɦW�ഫ�������覡�A�ҥH���|�X�{�����ɦW�A�]�N������X
		include_once '../tool/inilib.php';
		$tabini=parse_ini_file("./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$_POST['tabnum'].".ini",true);//2020/3/16 iconv('utf-8','big5',$_POST['tabnum']) >> $_POST['tabnum'] �F�]���ɦW�ഫ�������覡�A�ҥH���|�X�{�����ɦW�A�]�N������X
		if(strstr($tabini[$_POST['tabnum']]['table'],',')){
			$tablist=preg_split('/,/',$tabini[$_POST['tabnum']]['table']);
			foreach($tablist as $tl){
				$temp=parse_ini_file("./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$tl.".ini",true);//2020/3/16 iconv('utf-8','big5',$tl) >> $tl �F�]���ɦW�ഫ�������覡�A�ҥH���|�X�{�����ɦW�A�]�N������X
				$temp[$tl]['state']='999';
				if(isset($_POST['submachine'])&&$_POST['submachine']!='empty'){
					$temp[$tl]['machine']=$_POST['submachine'];
				}
				else if(isset($_POST['machine'])&&$_POST['machine']!='empty'){
					$temp[$tl]['machine']=$_POST['machine'];
				}
				else{
					$temp[$tl]['machine']="";
				}
				write_ini_file($temp,"./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$tl.".ini");//2020/3/16 iconv('utf-8','big5',$tl) >> $tl �F�]���ɦW�ഫ�������覡�A�ҥH���|�X�{�����ɦW�A�]�N������X
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
				$tabini[$_POST['tabnum']]['machine']="";
			}
			write_ini_file($tabini,"./table/".$_POST['bizdate'].";".$_POST['zcounter'].";".$_POST['tabnum'].".ini");//2020/3/16 iconv('utf-8','big5',$_POST['tabnum']) >> $_POST['tabnum'] �F�]���ɦW�ഫ�������覡�A�ҥH���|�X�{�����ɦW�A�]�N������X
		}
	}
	else{
	}
}
else{
}
?>