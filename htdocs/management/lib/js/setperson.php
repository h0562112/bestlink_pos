<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql='UPDATE person SET id="'.$_SESSION['ID'].'",pw="'.$_SESSION['ID'].'",power=1 WHERE id="emt0001"';
sqlnoresponse($conn,$sql,'sqlite');
$sql="SELECT powergroup.seq FROM powergroup JOIN person ON person.id='".$_SESSION['ID']."' AND person.power=powergroup.pno AND powergroup.`delete`=0";
$powerseq=sqlquery($conn,$sql,'sqlite');
$sql="SELECT cardno,id,person.name,power,powergroup.name AS pname,firstdate,lastdate,person.state,powergroup.seq AS seq FROM person JOIN powergroup ON powergroup.pno=person.power WHERE powergroup.seq>=(SELECT powergroup.seq FROM powergroup JOIN person ON person.id='".$_SESSION['ID']."' AND person.power=powergroup.pno) AND id<>'admin' AND person.id!='".$_SESSION['ID']."' AND person.state='1'";
$content=sqlquery($conn,$sql,'sqlite');
$sql="SELECT * FROM powergroup WHERE seq>=(SELECT powergroup.seq FROM powergroup JOIN person ON person.id='".$_SESSION['ID']."' AND person.power=powergroup.pno) AND powergroup.`delete`=0 ORDER BY seq";
$power=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
include_once '../../../tool/checkweb.php';
$yn=check_mobile();
?>
<script>
person=$('.person').tabs();
person.tabs('option','disabled',[1,3]);
$('#personTable').tableHeadFixer();
$('#powerTable').tableHeadFixer();
</script>
<style>
.person #person1 table,
.person #person11 table,
.person #person2 table,
.person #person21 table {
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
.person #person1 table thead,
.person #person2 table thead {
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
.person #person1 table td,
.person #person1 table th,
.person #person11 table td,
.person #person2 table td,
.person #person2 table th,
.person #person21 table td {
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
.person #person1 table tbody tr:nth-child(odd),
.person #person2 table tbody tr:nth-child(odd) {
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
<div class='person' style="width:100%;height:100%;overflow:auto;">
	<ul>
		<li><a href='#person1'>登入帳號列表</a></li>
		<li><a href='#person11'>帳號資料</a></li>
		<li><a href='#person2'>權限列表</a></li>
		<li><a href='#person21'>權限資料</a></li>
	</ul>
	<div id='person1' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center>登入帳號列表</center></h1>
		<div id='param' style='display:none;'>
			<input type='hidden' id='prev' value=''>
			<input type='hidden' id='focus' value=''>
			<input type='hidden' id='next' value=''>
		</div>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='create' value='新增'>
			<input type='button' class='initbutton' id='edit' value='修改'>
			<input type='button' class='initbutton' id='delete' value='刪除'>
		</div>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='personTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<table id='personTable'>
					<thead>
						<tr>
							<th></th>
							<th>工號</th>
							<th>帳號</th>
							<th>姓名</th>
							<th>權限編號</th>
							<th>權限</th>
							<th>到職日</th>
							<th>狀態</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($content as $con){
					if($con['id']=='admin'){
						continue;
					}
					else{
						echo "<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='".$con['cardno']."'></td><td>".$con['cardno']."</td><td>".$con['id']."</td><td>".$con['name']."</td><td>".$con['power']."</td><td>".$con['pname']."</td><td>".$con['firstdate']."</td><td>";
						if(strlen($con['lastdate'])==0||strtotime($con['firstdate'])>strtotime($con['lastdate'])){
							echo '在職';
						}
						else{
							echo '<font color="#ff0000">已離職</font>';
						}
						echo "</td></tr>";
					}
				}
				?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div id='person11' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center></center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='save' value='儲存'>
			<input type='button' class='initbutton' id='cancel' value='取消'>
		</div>
		<div id='datatable' style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='persondata'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<input type='hidden' id='type' name='type' value=''>
				<table>
					<tr>
						<td>工號</td>
						<td><input type='text' id='cardno' name='cardno'></td>
					</tr>
					<tr>
						<td>姓名</td>
						<td><input type='text' data-id='name' name='name'></td>
					</tr>
					<tr>
						<td>性別</td>
						<td><label><input type='radio' id='b' name='sex' value='1'>男性</label><label><input type='radio' id='g' name='sex' value='2'>女性</label></td>
					</tr>
					<tr>
						<td>帳號</td>
						<td><input type='text' id='id' name='id'></td>
					</tr>
					<tr>
						<td>密碼</td>
						<td><input type='password' id='pw' name='pw'></td>
					</tr>
					<tr>
						<td>前台作廢密碼</td>
						<td><input type='password' id='voidpw' name='voidpw'><span style='color:#ff0000;'>(若為空，則表示該帳號無修改帳單、作廢與補印發票功能。)</span></td>
					</tr>
					<!-- <tr>
						<td>補印發票密碼</td>
						<td><input type='password' id='invpw' name='invpw'><span style='color:#ff0000;'>(若為空，則表示該帳號無補印發票功能。)</span></td>
					</tr> -->
					<tr>
						<td>前台報表密碼</td>
						<td><input type='password' id='paperpw' name='paperpw'><span style='color:#ff0000;'>(若為空，則表示該帳號無瀏覽報表功能。)</span></td>
					</tr>
					<tr>
						<td>修改打卡密碼</td>
						<td><input type='password' id='punchpw' name='punchpw'><span style='color:#ff0000;'>(若為空，則表示該帳號無法修改打卡紀錄。)</span></td>
					</tr>
					<tr>
						<td>補印單據密碼<br><span style='font-size:10px;'>(包含明細單、工作單與貼紙)</span></td>
						<td><input type='password' id='reprintpw' name='reprintpw'><span style='color:#ff0000;'>(若為空，則表示該帳號無法補印單據。)</span></td>
					</tr>
					<tr>
						<td>生日</td>
						<td><input type='date' id='birth' name='birth'></td>
					</tr>
					<tr>
						<td>聯絡電話</td>
						<td><input type='tel' id='tel' name='tel'></td>
					</tr>
					<tr>
						<td>聯絡地址</td>
						<td><input type='text' id='address' name='address'></td>
					</tr>
					<tr>
						<td>權限</td>
						<td>
							<div class="mod_select" id='power'>
								<ul>
									<li>
										<div class="select_box" id='powerbox'>
											<?php
											$option='<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">';
											foreach($power as $p){
												$option=$option.'<a id="'.$p['pno'].'">'.$p['name'].'</a>';
											}
											$option=$option.'</div>';
											echo $option;
											?>
										</div>
									</li>
								</ul>
								<input type="hidden" name='power' id="select_value" value=''>
							</div>
						</td>
					</tr>
					<tr>
						<td>到職日期</td>
						<td><input type='date' id='firstdate' name='firstdate'></td>
					</tr>
					<tr>
						<td>已離職</td>
						<td><input type='date' id='lastdate' name='lastdate'></td>
					</tr>
					<tr>
						<td>瀏覽店家</td>
						<td>
							<div class='stories'>
								<?php
								$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','data.db','','','','sqlite');
								$sql='SELECT * FROM dblist WHERE state=1';
								$stories=sqlquery($conn,$sql,'sqlite');
								sqlclose($conn,'sqlite');
								foreach($stories as $s){
									echo "<label><input type='checkbox' class='checkbox[]' name='stories[]' value='".$s['no']."'>".$s['name']."</label><br>";
								}
								?>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div id='person2' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center>權限列表</center></h1>
		<div id='param' style='display:none;'>
			<input type='hidden' id='prev' value=''>
			<input type='hidden' id='focus' value=''>
			<input type='hidden' id='next' value=''>
		</div>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='create' value='新增'>
			<input type='button' class='initbutton' id='edit' value='修改'>
			<input type='button' class='initbutton' id='delete' value='刪除'>
		</div>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='powerTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<table id='powerTable'>
					<thead>
						<tr>
							<th></th>
							<th>權限權重</th>
							<th>權限名稱</th>
							<th>狀態</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($power as $p){
					if($p['seq']=='0'){
						continue;
					}
					else{
						echo "<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='pg[]' style='display:none;' value='".$p['pno']."'></td><td>".$p['seq']."</td><td>".$p['name']."</td><td>";
						if($p['state']==1){
							if($yn){
								echo '<span style="font-size:16px;">啟用</span>';
							}
							else{
								echo '<span style="font-size:8px;">啟用</span>';
							}
						}
						else{
							echo '<font color="#ff0000">停用</font>';
						}
						echo "</td></tr>";
					}
				}
				?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div id='person21' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center></center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='save' value='儲存'>
			<input type='button' class='initbutton' id='cancel' value='取消'>
		</div>
		<div id='datatable' style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='powergroup'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<input type='hidden' id='pno' name='pno'>
				<table>
					<tr>
						<td>權限權重</td>
						<td><input type='number' id='seq' name='seq' min='<?php echo $powerseq[0]['seq']; ?>' value='<?php echo $powerseq[0]['seq']; ?>'><br><span style='font-size:10px;color:#ff0000;'>數值越小權重越高</span></td>
					</tr>
					<tr>
						<td>權限名稱</td>
						<td><input type='text' data-id='name' name='name'></td>
					</tr>
					<tr>
						<td>停用</td>
						<td><input type='checkbox' id='stop' name='stop'></td>
					</tr>
					<!-- <tr>
						<td style='vertical-align:text-top;'>權限內容</td>
						<td>
							<div class='fun'>
							<?php
							for($i=0;$i<sizeof($powerlist);$i++){
								if($powerlist[$i]['subtype']=='0'){
									if($powerlist[$i]['type']=='rear'){
										echo "<hr><label><img id='chimg' src='./img/noch.png'><input type='checkbox' name='rear[]' class='".$powerlist[$i]['subtype']."' style='display:none;' value='".$powerlist[$i]['no']."'";
									}
									else{
										echo "<label><img id='chimg' src='./img/noch.png'><input type='checkbox' name='front[]' class='".$powerlist[$i]['subtype']."' style='display:none;' value='".$powerlist[$i]['no']."'";
									}
									if($powerlist[$i]['state']==1){
									}
									else{
										echo ' disabled';
									}
									echo ">".$powerlist[$i]['name']."</label>";
									echo '<div class="'.$powerlist[$i]['type'].'">';
								}
								else{
									if(preg_match('/-/',$powerlist[$i]['subtype'])){
										echo '<label style="margin:0 0 0 40px;">';
										if($powerlist[$i]['type']=='rear'){
											echo "<img id='chimg' src='./img/noch.png'><input type='checkbox' name='rear[]' class='".$powerlist[$i]['subtype']."' style='display:none;' value='".$powerlist[$i]['no']."'";
										}
										else{
											echo "<img id='chimg' src='./img/noch.png'><input type='checkbox' name='front[]' class='".$powerlist[$i]['subtype']."' style='display:none;' value='".$powerlist[$i]['no']."'";
										}
										if($powerlist[$i]['state']==1){
										}
										else{
											echo ' disabled';
										}
										echo ">".$powerlist[$i]['name']."</label>";
										if(!isset($powerlist[$i+1]['subtype'])||!preg_match('/-/',$powerlist[$i+1]['subtype'])){
										}
										else{
											echo '<br>';
										}

									}
									else{
										echo '<label style="margin:0 0 0 20px;">';
										if($powerlist[$i]['type']=='rear'){
											echo "<img id='chimg' src='./img/noch.png'><input type='checkbox' name='rear[]' class='".$powerlist[$i]['subtype']."' style='display:none;' value='".$powerlist[$i]['no']."'";
										}
										else{
											echo "<img id='chimg' src='./img/noch.png'><input type='checkbox' name='front[]' class='".$powerlist[$i]['subtype']."' style='display:none;' value='".$powerlist[$i]['no']."'";
										}
										if($powerlist[$i]['state']==1){
										}
										else{
											echo ' disabled';
										}
										echo ">".$powerlist[$i]['name']."</label>";
										echo '<div class="'.$powerlist[$i]['subtype'].'">';
									}
									if(!isset($powerlist[$i+1]['subtype'])||!preg_match('/-/',$powerlist[$i+1]['subtype'])){
										echo '</div>';
									}
									else{
									}
								}
								if(!isset($powerlist[$i+1]['type'])||$powerlist[$i+1]['type']!=$powerlist[$i]['type']){
									echo '</div>';
								}
								else{
								}
							}
							?>
							</div>
						</td>
					</tr> -->
				</table>
			</form>
		</div>
	</div>
</div>