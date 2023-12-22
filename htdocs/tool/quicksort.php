<?php
function quick_sort($array,$index = 0,$type = 1){//$index 判斷欄位;$type 1=由小到大2=由大到小
	// find array size
	$length = count($array);
	
	// base case test, if array of length 0 then just return array to caller
	if($length <= 1){
		return $array;
	}
	else{
		
		$t='';

		// select an item to act as our pivot point, since list is unsorted first position is easiest
		//$pivot = $array[0];
		foreach($array as $k=>$a){
			$pivot[$k]['no'] = $k;
			if(is_array($a)){
				foreach($a as $ak=>$aa){
					$pivot[$k][$ak] = $aa;
				}
			}
			else{
				$pivot[$k][] = $a;
			}
			//$pivot[$k]['name'] = $a['name'];
			//$pivot[$k]['amt'] = $a['amt'];
			$t=$k;
			break;
		}
		
		// declare our two arrays to act as partitions
		$left = $right = array();
		
		// loop and compare each item in the array to the pivot value, place item in appropriate partition
		/*for($i = 1; $i < count($array); $i++)
		{
			if($array[$i] < $pivot){
				$left[] = $array[$i];
			}
			else{
				$right[] = $array[$i];
			}
		}*/
		foreach($array as $k=>$a){
			if($k==$t){
				continue;
			}
			else{
				if($type==1){
					if($a[$index] <= $pivot[$t][$index]){
						$left[$k]['no'] = $k;
						if(is_array($a)){
							foreach($a as $ak=>$aa){
								$left[$k][$ak] = $aa;
							}
						}
						else{
							$left[$k][] = $a;
						}
					}
					else{
						$right[$k]['no'] = $k;
						if(is_array($a)){
							foreach($a as $ak=>$aa){
								$right[$k][$ak] = $aa;
							}
						}
						else{
							$right[$k][] = $a;
						}
					}
				}
				else{
					if($a[$index] >= $pivot[$t][$index]){
						$left[$k]['no'] = $k;
						if(is_array($a)){
							foreach($a as $ak=>$aa){
								$left[$k][$ak] = $aa;
							}
						}
						else{
							$left[$k][] = $a;
						}
					}
					else{
						$right[$k]['no'] = $k;
						if(is_array($a)){
							foreach($a as $ak=>$aa){
								$right[$k][$ak] = $aa;
							}
						}
						else{
							$right[$k][] = $a;
						}
					}
				}
			}
		}
		
		// use recursion to now sort the left and right lists
		return array_merge(quick_sort($left,$index,$type), $pivot, quick_sort($right,$index,$type));
	}
}
?>