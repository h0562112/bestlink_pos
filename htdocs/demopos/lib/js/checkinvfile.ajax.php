<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
$content=parse_ini_file('../../../database/setup.ini',true);
if(!isset($content['basic']['sendinvlocation'])||$content['basic']['sendinvlocation']=='1'){//亮點
	if(!file_exists('../../../trnx/Log/'.$_POST['invfile'])){
		echo 'notexists';
	}
	else{
		echo 'exists';
	}
}
else if($content['basic']['sendinvlocation']=='2'){//神通
}
else if($content['basic']['sendinvlocation']=='3'){//中鼎
	if(!isset($content['zdninv']['useEIP'])||$content['zdninv']['useEIP']=='1'){
		$tempdata=preg_split('/;/',$_POST['invfile']);
		if(!file_exists('../../../trnx/Log/invoice/'.substr($tempdata[1],0,4).'/'.substr($tempdata[1],4,2).'/'.substr($tempdata[1],6,2).'/'.$tempdata[0])&&!file_exists('../../../trnx/Log/xml/invoice/'.substr($tempdata[1],0,4).'/'.substr($tempdata[1],4,2).'/'.substr($tempdata[1],6,2).'/'.$tempdata[0])){
			echo 'notexists';
		}
		else{
			echo 'exists';
		}
	}
	else{
		$tempdata=preg_split('/;/',$_POST['invfile']);
		if(!file_exists('../../../print/invuploadlog/waitupload/'.$tempdata[0])&&!file_exists('../../../print/invuploadlog/uploaded/'.$tempdata[0])){
			$tempdata[0]=substr($tempdata[0],0,-4).'xml';
			if(!file_exists('../../../trnx/Log/invoice/'.substr($tempdata[1],0,4).'/'.substr($tempdata[1],4,2).'/'.substr($tempdata[1],6,2).'/'.$tempdata[0])&&!file_exists('../../../trnx/Log/xml/invoice/'.substr($tempdata[1],0,4).'/'.substr($tempdata[1],4,2).'/'.substr($tempdata[1],6,2).'/'.$tempdata[0])){
				echo 'notexists';
			}
			else{
				echo 'exists';
			}
		}
		else{
			echo 'exists';
		}
	}
}
?>