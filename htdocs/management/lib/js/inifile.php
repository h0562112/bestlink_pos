<?php
include_once '../../../tool/checkweb.php';
$yn=check_mobile();
if(isset($_POST['lan'])&&$_POST['lan']!=''){
	if(file_exists('../../lan/interface'.$_POST['lan'].'.ini')){
		$interface=parse_ini_file('../../lan/interface'.$_POST['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('../../lan/interface1.ini')){
		$interface=parse_ini_file('../../lan/interface1.ini',true);
	}
	else{
		$interface='-1';
	}
}
?>
<script>
inifile=$('.inifile').tabs();
$(document).ready(function(){
});
</script>
<style>
</style>

<div class='inifile' style="width:100%;overflow:auto;">
	<ul>
		<li><a id='initsetting' href='#setinitsetting'>
			<?php if($interface!='-1'&&isset($interface['name']['initsetting']))
					{
						echo $interface['name']['initsetting'];
					}
					else{
						echo 'initsetting';
					}
			?>
		</a></li>			
		<li><a id='printlisttag' href='#setprintlisttag'>
			<?php if($interface!='-1'&&isset($interface['name']['printlisttag']))
					{
						echo $interface['name']['printlisttag'];
					}
					else{
						echo 'printlisttag';
					}
			?>
		</a></li>
		<li><a id='setup' href='#setsetup'><?php if($interface!='-1'&&isset($interface['name']['setup']))
					{
						echo $interface['name']['setup'];
					}
					else{
						echo 'setup';
					}
			?>
		</a></li>
	</ul>
	<div id='setinitsetting' style="width:100%;height:max-content;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
	</div>
	<div id='setprintlisttag' style="width:100%;height:max-content;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
	</div>
	<div id='setsetup' style="width:100%;height:max-content;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
	</div>
</div>