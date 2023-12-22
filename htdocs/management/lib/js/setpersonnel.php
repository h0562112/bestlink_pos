<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
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
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql="SELECT * FROM personnel WHERE state=1 ORDER BY credatetime ASC";
$content=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
include_once '../../../tool/checkweb.php';
$yn=check_mobile();
?>
<script>
personnel=$('.personnel').tabs();
personnel.tabs('option','disabled',[1,2]);
$('#personnelTable').tableHeadFixer();
$('#powerTable').tableHeadFixer();
$(document).ready(function(){
	$('.personnel #personnel12 #data').on('click','#search',function(){
		var start=new Date($('.personnel #personnel12 #data input[name="startdate"]').val());
		var end=new Date($('.personnel #personnel12 #data input[name="enddate"]').val());
		var now=new Date();
		if(start.getTime()>end.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else if(start.getTime()>now.getTime()){
			alert('<?php if($interface!="-1"&&isset($interface["name"]["sysmsghint"]))echo $interface["name"]["sysmsghint"];else echo "請輸入合法時間。"; ?>');
		}
		else{
			/*$.ajax({
				url:'./lib/js/getpunch.ajax.php',
				method:'post',
				data:$('.personnel #personnel12 #data').serialize(),
				dateType:'html',
				success:function(d){
					$('.personnel #personnel12 #datatable').html(d);
					$("#paper8 #fixTable").tableHeadFixer({"left" : 1,"right":2,'foot':true});
				},
				error:function(e){
					console.log(e);
				}
			});*/
			console.log('1');
		}
	});
});
</script>
<style>
.personnel #personnel1 table,
.personnel #personnel11 table,
.personnel #personnel2 table,
.personnel #personnel21 table {
	font-family: Consolas,Microsoft JhengHei,sans-serif;
	border-collapse: collapse;
	<?php
	if($yn){
		echo 'font-size:40px;';
	}
	else{
		echo 'font-size:20px;';
	}
	?>
}
.personnel #personnel1 table thead,
.personnel #personnel2 table thead {
	color:#898989;
	<?php
	if($yn){
		echo 'font-size:20px;';
	}
	else{
		echo 'font-size:12px;';
	}
	?>
}
.personnel #personnel1 table td,
.personnel #personnel1 table th,
.personnel #personnel11 table td,
.personnel #personnel2 table td,
.personnel #personnel2 table th,
.personnel #personnel21 table td {
	<?php
	if($yn){
		echo 'padding:10px 10px 6px 20px;';
	}
	else{
		echo 'padding:5px 5px 3px 10px;';
	}
	?>
	white-space: nowrap;
}
.personnel #personnel1 table tbody tr:nth-child(odd),
.personnel #personnel2 table tbody tr:nth-child(odd) {
	background-color:#f0f0f0;
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
</style>
<div class='personnel' style="overflow: hidden;margin-bottom: 3px;">
	<ul>
		<li><a href='#personnel1'><?php if($interface!='-1'&&isset($interface['name']['emplist']))echo $interface['name']['emplist'];else echo '員工列表'; ?></a></li>
		<li><a href='#personnel11'><?php if($interface!='-1'&&isset($interface['name']['empdata']))echo $interface['name']['empdata'];else echo '員工資料'; ?></a></li>
		<li><a href='#personnel12'><?php if($interface!='-1'&&isset($interface['name']['punchdata']))echo $interface['name']['punchdata'];else echo '打卡紀錄'; ?></a></li>
	</ul>
	<div id='personnel1' style="width:100%;float:left;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center><?php if($interface!='-1'&&isset($interface['name']['emplist']))echo $interface['name']['emplist'];else echo '員工列表'; ?></center></h1>
		<div id='param' style='display:none;'>
			<input type='hidden' id='prev' value=''>
			<input type='hidden' id='focus' value=''>
			<input type='hidden' id='next' value=''>
		</div>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='create' value='<?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?>'>
			<input type='button' class='initbutton' id='punch' value='<?php if($interface!='-1'&&isset($interface['name']['punchdata']))echo $interface['name']['punchdata'];else echo '打卡紀錄'; ?>'>
			<input type='button' class='initbutton' id='edit' value='<?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?>'>
			<input type='button' class='initbutton' id='delete' value='<?php if($interface!='-1'&&isset($interface['name']['delete']))echo $interface['name']['delete'];else echo '刪除'; ?>'>
			<input type='button' class='initbutton' id='expall' value='<?php if($interface!='-1'&&isset($interface['name']['exppunchdata']))echo $interface['name']['exppunchdata'];else echo '匯出打卡紀錄'; ?>'>
		</div>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='personnelTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<table id='personnelTable'>
					<thead>
						<tr>
							<th></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['number']))echo $interface['name']['number'];else echo '編號'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['name']))echo $interface['name']['name'];else echo '姓名'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['tel']))echo $interface['name']['tel'];else echo '聯絡電話'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['address']))echo $interface['name']['address'];else echo '聯絡地址'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['helper']))echo $interface['name']['helper'];else echo '緊急聯絡人'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['helpertel']))echo $interface['name']['helpertel'];else echo '緊急聯絡電話'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['state']))echo $interface['name']['state'];else echo '狀態'; ?></th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($content as $con){
					echo "<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='".$con['perno']."'></td><td>".$con['percard']."</td><td>".$con['name']."</td><td>".$con['tel']."</td><td>".$con['address']."</td><td>".$con['sosname']."</td><td>".$con['sostel']."</td><td>";
					if($con['state']==0){
						echo '<font color="#ff0000">';if($interface!='-1'&&isset($interface['name']['stopstate']))echo $interface['name']['stopstate'];else echo '停用';echo '</font>';
					}
					else{
					}
					echo "</td></tr>";
				}
				?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div id='personnel11' style="width:100%;float:left;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center></center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='save' value='<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>'>
			<input type='button' class='initbutton' id='cancel' value='<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>'>
		</div>
		<div id='datatable' style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='personneldata'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<input type='hidden' id='type' name='type' value=''>
				<table>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['number']))echo $interface['name']['number'];else echo '編號'; ?></td>
						<td><input type='hidden' id='perno' name='perno'><input type='text' id='percard' name='percard'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['name']))echo $interface['name']['name'];else echo '姓名'; ?></td>
						<td><input type='text' id='name' name='name'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['tel']))echo $interface['name']['tel'];else echo '聯絡電話'; ?></td>
						<td><input type='tel' id='tel' name='tel'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['address']))echo $interface['name']['address'];else echo '聯絡地址'; ?></td>
						<td><input type='text' id='address' name='address'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['helper']))echo $interface['name']['helper'];else echo '緊急聯絡人'; ?></td>
						<td><input type='text' id='sosname' name='sosname'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['helpertel']))echo $interface['name']['helpertel'];else echo '緊急聯絡電話'; ?></td>
						<td><input type='tel' id='sostel' name='sostel'></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div id='personnel12' style="width:100%;float:left;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center><?php if($interface!='-1'&&isset($interface['name']['punchdata']))echo $interface['name']['punchdata'];else echo '打卡紀錄'; ?></center></h1>
		<form id='data'>
			<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
			<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
			<input type='hidden' id='perno' name='perno'>
			<table>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['number']))echo $interface['name']['number'];else echo '編號'; ?>：</td>
					<td><span id='percard'></span></td>
				</tr>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['name']))echo $interface['name']['name'];else echo '姓名'; ?>：</td>
					<td><span id='name'></span></td>
				</tr>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['timerange']))echo $interface['name']['timerange'];else echo '時間區間'; ?>：</td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'>～<input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd').' +1 days')); ?>'></td>
					<td><input type='button' id='search' value='<?php if($interface!='-1'&&isset($interface['name']['search']))echo $interface['name']['search'];else echo '查詢'; ?>'></td>
					<td><input type='button' id='exp' value='<?php if($interface!='-1'&&isset($interface['name']['exp']))echo $interface['name']['exp'];else echo '匯出'; ?>'></td>
				</tr>
			</table>
		</form>
		<div id='datatable' style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
</div>