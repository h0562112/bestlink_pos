<?php
session_start();
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
paper=$('.memsalelist').tabs();
function strtotime(time, now) {
	var d = new Date();
	d.setTime(now);

	var ParsedTime = new RegExp('([+-][0-9]+) (\\w+)', 'i').exec(time);
	if(!ParsedTime) return 0;

	switch(ParsedTime[2]) {
		case 'second':
			d.setSeconds(d.getSeconds() + parseInt(ParsedTime[1], 10));
			break;
		case 'minute':
			d.setMinutes(d.getMinutes() + parseInt(ParsedTime[1], 10));
			break;
		case 'hour':
			d.setHours(d.getHours() + parseInt(ParsedTime[1], 10));
			break;
		case 'day':
			d.setDate(d.getDate() + parseInt(ParsedTime[1], 10));
			break;
		case 'month':
			d.setMonth(d.getMonth() + parseInt(ParsedTime[1], 10));
			break;
		case 'year':
			d.setFullYear(d.getFullYear() + parseInt(ParsedTime[1], 10));
			break;
	}

	return d.getTime();
}
$(document).ready(function(){
	$('.memsalelist #memsale #detail #search').click(function(){
		$.ajax({
			url:'./lib/js/getmemsalelist.php',
			method:'post',
			async:false,
			data:{'company':"<?php echo $_SESSION['company']; ?>",'dep':"<?php if($_SESSION['DB']!='')echo $_SESSION['DB'];else echo $_SESSION['dbname']; ?>",'memno':$('.memsalelist #memsale #detail select[name="memno"] option:selected').val(),'date':$('.memsalelist #memsale #detail input[name="startdate"]').val()},
			dataType:'html',
			success:function(d){
				//console.log(d);
				$('.memsalelist #memsale .table').html(d);
			},
			error:function(e){
				//console.log(e);
			}
		});
	});
});
</script>
<?php
$unit=parse_ini_file('../../../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/unit.ini',true);
?>
<style>
.memsalelist #memsale .table table {
	font-family: Consolas,Microsoft JhengHei,sans-serif;
	border-collapse: collapse;
}
.memsalelist #memsale .table .title {
	border-top: 1px solid #898989;
    padding-top: 5px;
	font-weight:bold;
}
.memsalelist #memsale .table tr:nth-child(even) {
	background-color:#f0f0f0;
}
.memsalelist #memsale .table #preday {
	font-weight:bold;
}
.memsalelist #memsale .table #dis {
	color:#ff0000;
}
.memsalelist #memsale .table #top th {
	border-top: 3px solid blue;
}
.memsalelist #memsale .table table thead {
	color:#898989;
	font-size:12px;
}
.memsalelist #memsale .table table thead th {
	padding:0 5px;
}
.memsalelist #memsale .table table th {
	font-weight:normal;
}
.memsalelist #memsale .table table td {
	padding:5px;
}
.memsalelist #memsale .table table #bold {
	font-weight:bold;
	font-size:16px;
}
.memsalelist #memsale .table .money div::before {
	content:'<?php echo $unit["init"]["frontunit"]; ?>';
	margin-right:5px;
}
.memsalelist #memsale .table .money div::after {
	content:'<?php echo $unit["init"]["unit"]; ?>';
}

.mod_select ul {
	margin:0;
	padding:0;
}
.mod_select ul:after {
	display: block;
    clear: both;
    visibility: hidden;
    height: 0;
    content: '';
}
.mod_select ul li {
	list-style-type:none;
	float:left;
	height:24px;
}
.select_label {
	color:#982F4D;
	float:left;
	line-height:24px;
	padding-right:10px;
	font-size:12px;
	font-weight:700;
}
.select_box {
	float:left;
	border:solid 1px #ccc;
	color:#444;
	position:relative;
	cursor:pointer;
	width:300px;
	font-size:14px;
}
.selet_open {
	display:inline-block;
	position:absolute;
	right:0;
	top:0;
	width:30px;
	height:100%;
	line-height:24px;
	text-align:center;
	content:'▼';
}
.select_txt {
	display:inline-block;
	padding-left:10px;
	width:300px;
	line-height:24px;
	height:24px;
	cursor:pointer;
	overflow:hidden;
}
.option {
	width:300px;
	border:solid 1px #ccc;
	position:absolute;
	top:24px;
	left:-1px;
	z-index:2;
	overflow:hidden;
	display:none;
}
.option a {
	display:block;
	height:26px;
	line-height:26px;
	text-align:left;
	padding:0 10px;
	width:100%;
	background:#fff;
}
.option a:hover {
	background:#aaa;
}
.ui-tabs .ui-tabs-nav li {
	margin:10px .2em 0 0;
}
</style>
<div class='memsalelist' style="overflow:hidden;margin-bottom:3px;">
	<ul style='width:100%;float:left;-webkit-box-sizing: efborder-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		<li><a href='#memsale'><?php if($interface!='-1'&&isset($interface['name']['memsale']))echo $interface['name']['memsale'];else echo "會員銷售紀錄"; ?></a></li>
	</ul>
	<div id='memsale' style="width:100%;float:left;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">
		<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['memsale'])){ echo $interface['name']['memsale']; } else{ echo '會員銷售紀錄'; } ?></center></h1>
		<form id='detail' style='float:left;'>
			<input type='hidden' name='lan' value='<?php if(isset($_POST['lan'])&&$_POST['lan']!='')echo $_POST['lan'];else echo '1'; ?>'>
			<table>
				<?php
				include_once '../../../tool/dbTool.inc.php';
				if($_SESSION["DB"]!=""){
					$initsetting=parse_ini_file('../../../menudata/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
				}
				else{
					$initsetting=parse_ini_file('../../../menudata/'.$_SESSION['company'].'/'.$_SESSION['dbname'].'/initsetting.ini',true);
				}
				
				if(isset($initsetting['init']['openmember'])&&$initsetting['init']['openmember']=='1'){//開啟會員
					if(isset($initsetting['init']['onlinemember'])&&$initsetting['init']['onlinemember']=='1'){//網路會員
						$conn=sqlconnect('localhost',$_SESSION['company'],'posmana','1qaz2wsx','utf-8','mysql');
						$sql='SELECT memno,cardno,name,tel FROM member WHERE SUBSTR(memno,1,LENGTH("';
						if($_SESSION["DB"]!=""){
							$sql.=$_SESSION['DB'];
						}
						else{
							$sql.=$_SESSION['dbname'];
						}
						$sql.='"))="';
						if($_SESSION["DB"]!=""){
							$sql.=$_SESSION['DB'];
						}
						else{
							$sql.=$_SESSION['dbname'];
						}
						$sql.='" AND state="1"';
						$memlist=sqlquery($conn,$sql,'mysql');
						sqlclose($conn,'mysql');
					}
					else{
						$conn=sqlconnect('../../../management/menudata/'.$_SESSION['company'].'/person','member.db','','','','sqlite');
						$sql='SELECT memno,cardno,name,tel FROM person WHERE SUBSTR(memno,1,LENGTH("';
						if($_SESSION["DB"]!=""){
							$sql.=$_SESSION['DB'];
						}
						else{
							$sql.=$_SESSION['dbname'];
						}
						$sql.='"))="';
						if($_SESSION["DB"]!=""){
							$sql.=$_SESSION['DB'];
						}
						else{
							$sql.=$_SESSION['dbname'];
						}
						$sql.='" AND state="1"';
						$memlist=sqlquery($conn,$sql,'sqlite');
						sqlclose($conn,'sqlite');
					}
				}
				else{
				}
				?>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['memberlist']))echo $interface['name']['memberlist'];else echo '會員列表'; ?>：<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'></td>
					<td colspan='2'>
						<select name="memno">
						<?php
						if(isset($memlist[0]['memno'])){
							for($i=0;$i<sizeof($memlist);$i++){
								echo '<option value="'.$memlist[$i]['memno'].'" ';
								if($i!=0){
								}
								else{
									echo 'selected';
								}
								echo '>'.$memlist[$i]['cardno'].'-'.$memlist[$i]['name'].'-'.$memlist[$i]['tel'].'</option>';
							}
						}
						else{
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type='month' name='startdate' value='<?php echo date('Y-m',strtotime(date('Ymd'))); ?>'></td>
					<td><input type='button' id='search' value='<?php if($interface!='-1'&&isset($interface['name']['search']))echo $interface['name']['search'];else echo '查詢'; ?>'><input type='button' id='expinit' value='<?php if($interface!='-1'&&isset($interface['name']['expinitdata']))echo $interface['name']['expinitdata'];else echo '匯出原始資料'; ?>'></td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
</div>