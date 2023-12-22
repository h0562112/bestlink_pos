<?php
function prime($wantN){//只針對100以內的質數
	if(intval($wantN)<=25){
		if(file_exists('./prime')){
		}
		else{
			mkdir('./prime');
		}
		if(file_exists('./prime/prime.seq.ini')){
		}
		else{
			$f=fopen('./prime/prime.seq.ini','w');
			fwrite($f,'[seq]'.PHP_EOL);
			fclose($f);
		}
		$primearray=parse_ini_file('./prime/prime.seq.ini',true);
		if(isset($primearray['seq'][$wantN])){
			return $primearray['seq'][$wantN];
		}
		else{
			include_once '../../../tool/inilib.php';
			$index=1;
			for($i=2;$i<100;$i++){
				$primes=0;
				for($k=1;$k<=$i;$k++){
					if($i%$k===0){
						$primes++;
					}
					else{
					}
					if($primes>=3){
						break;
					}
					else{
					}
				}
				if(intval($primes)<=2){//能除以1和自身的整數(不包括0)
					if(isset($primearray['seq'][$index])){
					}
					else{
						$primearray['seq'][$index]=$i;
					}
					$index++;
				}
				else{
				}
				if(isset($primearray['seq'][$wantN])){
					write_ini_file($primearray,'./prime/prime.seq.ini');
					//echo $primearray['seq'][$wantN];
					return $primearray['seq'][$wantN];
					break;
				}
				else{
					continue;
				}
			}
		}
	}
	else{//超出範圍(100以內的質數只有25個)
		return 'prime overflow';
	}
}
?>