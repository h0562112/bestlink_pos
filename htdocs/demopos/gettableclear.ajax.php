<?php
$tb=parse_ini_file('../database/floorspend.ini',true);
$filelist=scandir('./table',1);
foreach($filelist as $fl){
	if($fl=='.'||$fl=='..'||$fl=='outside'){
	}
	else{
		if(preg_match('/;'.$_POST['tablenum'].'.ini/',$fl)||preg_match('/;'.$_POST['tablenum'].'-\d.ini/',$fl)){
			$data=parse_ini_file('./table/'.$fl,true);
			break;
		}
		else{
		}
	}
}

if(isset($data)){
	echo '<input type="hidden" name="lastnum" value="'.$data[$_POST['tablenum']]['table'].'">';
}
else{
	echo '<input type="hidden" name="lastnum" value="'.$_POST['tablenum'].'">';
}

if($_POST['state']=='0'){
	if(isset($data)){
		echo '桌號: ';
		if(preg_match('/,/',$data[$_POST['tablenum']]['table'])){
			$splittable=preg_split('/,/',$data[$_POST['tablenum']]['table']);
			for($sti=0;$sti<sizeof($splittable);$sti++){
				if($sti!=0){
					echo ',';
				}
				else{
				}
				if(preg_match('/-/',$splittable[$sti])){
					$inittable=preg_split('/-/',$splittable[$sti]);
					if(isset($tb['Tname'][$inittable[0]])){
						echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
					}
					else{
						echo $splittable[$sti];
					}
				}
				else{
					if(isset($tb['Tname'][$splittable[$sti]])){
						echo $tb['Tname'][$splittable[$sti]];
					}
					else{
						echo $splittable[$sti];
					}
				}
			}
		}
		else{
			if(preg_match('/-/',$data[$_POST['tablenum']]['table'])){
				$inittable=preg_split('/-/',$data[$_POST['tablenum']]['table']);
				if(isset($tb['Tname'][$inittable[0]])){
					echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
				}
				else{
					echo $data[$_POST['tablenum']]['table'];
				}
			}
			else{
				if(isset($tb['Tname'][$data[$_POST['tablenum']]['table']])){
					echo $tb['Tname'][$data[$_POST['tablenum']]['table']];
				}
				else{
					echo $data[$_POST['tablenum']]['table'];
				}
			}
		}
		echo '<br>狀態: 已結帳';
	}
	else{
		echo '桌號: ';
		if(preg_match('/,/',$_POST['tablenum'])){
			$splittable=preg_split('/,/',$_POST['tablenum']);
			for($sti=0;$sti<sizeof($splittable);$sti++){
				if($sti!=0){
					echo ',';
				}
				else{
				}
				if(preg_match('/-/',$splittable[$sti])){
					$inittable=preg_split('/-/',$splittable[$sti]);
					if(isset($tb['Tname'][$inittable[0]])){
						echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
					}
					else{
						echo $splittable[$sti];
					}
				}
				else{
					if(isset($tb['Tname'][$splittable[$sti]])){
						echo $tb['Tname'][$splittable[$sti]];
					}
					else{
						echo $splittable[$sti];
					}
				}
			}
		}
		else{
			if(preg_match('/-/',$_POST['tablenum'])){
				$inittable=preg_split('/-/',$_POST['tablenum']);
				if(isset($tb['Tname'][$inittable[0]])){
					echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
				}
				else{
					echo $_POST['tablenum'];
				}
			}
			else{
				if(isset($tb['Tname'][$_POST['tablenum']])){
					echo $tb['Tname'][$_POST['tablenum']];
				}
				else{
					echo $_POST['tablenum'];
				}
			}
		}
		echo '<br>狀態: 已結帳';
	}
}
else if($_POST['state']=='999'){
	if(isset($data)){
		echo '桌號: ';
		if(preg_match('/,/',$data[$_POST['tablenum']]['table'])){
			$splittable=preg_split('/,/',$data[$_POST['tablenum']]['table']);
			for($sti=0;$sti<sizeof($splittable);$sti++){
				if($sti!=0){
					echo ',';
				}
				else{
				}
				if(preg_match('/-/',$splittable[$sti])){
					$inittable=preg_split('/-/',$splittable[$sti]);
					if(isset($tb['Tname'][$inittable[0]])){
						echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
					}
					else{
						echo $splittable[$sti];
					}
				}
				else{
					if(isset($tb['Tname'][$splittable[$sti]])){
						echo $tb['Tname'][$splittable[$sti]];
					}
					else{
						echo $splittable[$sti];
					}
				}
			}
		}
		else{
			if(preg_match('/-/',$data[$_POST['tablenum']]['table'])){
				$inittable=preg_split('/-/',$data[$_POST['tablenum']]['table']);
				if(isset($tb['Tname'][$inittable[0]])){
					echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
				}
				else{
					echo $data[$_POST['tablenum']]['table'];
				}
			}
			else{
				if(isset($tb['Tname'][$data[$_POST['tablenum']]['table']])){
					echo $tb['Tname'][$data[$_POST['tablenum']]['table']];
				}
				else{
					echo $data[$_POST['tablenum']]['table'];
				}
			}
		}
		echo '<br>狀態: <span style="color:#ff0000;">點餐中</span><br>點餐機號: '.$data[$_POST['tablenum']]['machine'];
	}
	else{
		echo '桌號: ';
		if(preg_match('/,/',$_POST['tablenum'])){
			$splittable=preg_split('/,/',$_POST['tablenum']);
			for($sti=0;$sti<sizeof($splittable);$sti++){
				if($sti!=0){
					echo ',';
				}
				else{
				}
				if(preg_match('/-/',$splittable[$sti])){
					$inittable=preg_split('/-/',$splittable[$sti]);
					if(isset($tb['Tname'][$inittable[0]])){
						echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
					}
					else{
						echo $splittable[$sti];
					}
				}
				else{
					if(isset($tb['Tname'][$splittable[$sti]])){
						echo $tb['Tname'][$splittable[$sti]];
					}
					else{
						echo $splittable[$sti];
					}
				}
			}
		}
		else{
			if(preg_match('/-/',$_POST['tablenum'])){
				$inittable=preg_split('/-/',$_POST['tablenum']);
				if(isset($tb['Tname'][$inittable[0]])){
					echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
				}
				else{
					echo $_POST['tablenum'];
				}
			}
			else{
				if(isset($tb['Tname'][$_POST['tablenum']])){
					echo $tb['Tname'][$_POST['tablenum']];
				}
				else{
					echo $_POST['tablenum'];
				}
			}
		}
		echo '<br>狀態: <span style="color:#ff0000;">點餐中</span><br>點餐機號: '.$data[$_POST['tablenum']]['machine'];
	}
	
}
else{
	if(isset($data)){
		echo '桌號: ';
		if(preg_match('/,/',$data[$_POST['tablenum']]['table'])){
			$splittable=preg_split('/,/',$data[$_POST['tablenum']]['table']);
			for($sti=0;$sti<sizeof($splittable);$sti++){
				if($sti!=0){
					echo ',';
				}
				else{
				}
				if(preg_match('/-/',$splittable[$sti])){
					$inittable=preg_split('/-/',$splittable[$sti]);
					if(isset($tb['Tname'][$inittable[0]])){
						echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
					}
					else{
						echo $splittable[$sti];
					}
				}
				else{
					if(isset($tb['Tname'][$splittable[$sti]])){
						echo $tb['Tname'][$splittable[$sti]];
					}
					else{
						echo $splittable[$sti];
					}
				}
			}
		}
		else{
			if(preg_match('/-/',$data[$_POST['tablenum']]['table'])){
				$inittable=preg_split('/-/',$data[$_POST['tablenum']]['table']);
				if(isset($tb['Tname'][$inittable[0]])){
					echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
				}
				else{
					echo $data[$_POST['tablenum']]['table'];
				}
			}
			else{
				if(isset($tb['Tname'][$data[$_POST['tablenum']]['table']])){
					echo $tb['Tname'][$data[$_POST['tablenum']]['table']];
				}
				else{
					echo $data[$_POST['tablenum']]['table'];
				}
			}
		}
		echo '<br>狀態: <span style="color:#ff0000;">未結帳</span>';
	}
	else{
		echo '桌號: ';
		if(preg_match('/,/',$_POST['tablenum'])){
			$splittable=preg_split('/,/',$_POST['tablenum']);
			for($sti=0;$sti<sizeof($splittable);$sti++){
				if($sti!=0){
					echo ',';
				}
				else{
				}
				if(preg_match('/-/',$splittable[$sti])){
					$inittable=preg_split('/-/',$splittable[$sti]);
					if(isset($tb['Tname'][$inittable[0]])){
						echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
					}
					else{
						echo $splittable[$sti];
					}
				}
				else{
					if(isset($tb['Tname'][$splittable[$sti]])){
						echo $tb['Tname'][$splittable[$sti]];
					}
					else{
						echo $splittable[$sti];
					}
				}
			}
		}
		else{
			if(preg_match('/-/',$_POST['tablenum'])){
				$inittable=preg_split('/-/',$_POST['tablenum']);
				if(isset($tb['Tname'][$inittable[0]])){
					echo $tb['Tname'][$inittable[0]].'-'.$inittable[1];
				}
				else{
					echo $_POST['tablenum'];
				}
			}
			else{
				if(isset($tb['Tname'][$_POST['tablenum']])){
					echo $tb['Tname'][$_POST['tablenum']];
				}
				else{
					echo $_POST['tablenum'];
				}
			}
		}
		echo '<br>狀態: <span style="color:#ff0000;">未結帳</span>';
	}
	
}
?>