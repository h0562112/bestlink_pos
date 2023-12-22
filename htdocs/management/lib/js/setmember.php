<?php
session_start();
date_default_timezone_set('Asia/Taipei');
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
$initsetting=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini',true);
include_once '../../../tool/dbTool.inc.php';
if($initsetting['init']['onlinemember']=='1'){//網路會員
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
	$dbtype='mysql';
	if($_POST['dep']=='rabbit0001'){
		$sql="SELECT memno,cardno,member.name,tel,tel2,sex,power,powergroup.name AS pname,howknow,remark,firstdate,lastdate,member.state,powergroup.seq AS seq,point,money FROM member JOIN powergroup ON powergroup.pno=member.power WHERE memno LIKE '".$_POST['dep']."%' OR memno NOT LIKE 'rabbit%' ORDER BY powergroup.seq,member.firstdate DESC";
	}
	else{
		$sql="SELECT member.*,powergroup.name AS pname,powergroup.seq AS seq FROM member JOIN powergroup ON powergroup.pno=member.power WHERE memno LIKE '".$_POST['dep']."%' ORDER BY powergroup.seq,member.firstdate DESC,member.memno DESC";
	}
}
else{
	$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
	$dbtype='sqlite';
	if($_POST['dep']=='rabbit0001'){
		$sql="SELECT memno,cardno,person.name,tel,tel2,sex,power,powergroup.name AS pname,howknow,remark,firstdate,lastdate,person.state,powergroup.seq AS seq,point,money FROM person JOIN powergroup ON powergroup.pno=person.power WHERE memno LIKE '".$_POST['dep']."%' OR memno NOT LIKE 'rabbit%' ORDER BY powergroup.seq,person.firstdate DESC";
	}
	else{
		$sql="SELECT memno,cardno,person.name,tel,tel2,sex,power,powergroup.name AS pname,howknow,remark,firstdate,lastdate,person.state,powergroup.seq AS seq,point,money FROM person JOIN powergroup ON powergroup.pno=person.power WHERE memno LIKE '".$_POST['dep']."%' ORDER BY powergroup.seq,person.firstdate DESC";
	}
}
$content=sqlquery($conn,$sql,$dbtype);
$sql="SELECT * FROM powergroup WHERE state=1 ORDER BY seq";
$power=sqlquery($conn,$sql,$dbtype);
/*$sql="SELECT * FROM powerlist WHERE `group`=1 ORDER BY type,subtype,no";
$powerlist=sqlquery($conn,$sql,$dbtype);*/
sqlclose($conn,$dbtype);
$machinedata=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/machinedata.ini',true);
?>
<script>
member=$('.member').tabs();
member.tabs('option','disabled',[1,3]);
$('.member #memberTable').tableHeadFixer();
$('.member #powerTable').tableHeadFixer();
</script>
<style>
.member #member1 table,
.member #member11 table,
.member #member2 table,
.member #member21 table {
	font-family: Consolas,Microsoft JhengHei,sans-serif;
	border-collapse: collapse;
	font-size:20px;
}
.member #member1 table thead,
.member #member2 table thead {
	color:#898989;
	font-size:12px;
}
.member #member1 table td,
.member #member1 table th,
.member #member11 table td,
.member #member2 table td,
.member #member2 table th,
.member #member21 table td {
	padding:5px 5px 3px 10px;
	white-space: nowrap;
}
.member #member1 table tbody tr:nth-child(odd),
.member #member2 table tbody tr:nth-child(odd) {
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
.member #member1 #stop {
	color:#ff0000;
}
</style>
<div class='member' style="margin:0 3px 3px 0;overflow:hidden;">
	<ul style='width:100%;float:left;'>
		<li><a href='#member1'><?php if($interface!='-1'&&isset($interface['name']['memberlist']))echo $interface['name']['memberlist'];else echo '會員列表'; ?></a></li>
		<li><a href='#member11'><?php if($interface!='-1'&&isset($interface['name']['memberdata']))echo $interface['name']['memberdata'];else echo '會員資料'; ?></a></li>
		<li><a href='#member2'><?php if($interface!='-1'&&isset($interface['name']['levellist']))echo $interface['name']['levellist'];else echo '等級列表'; ?></a></li>
		<li><a href='#member21'><?php if($interface!='-1'&&isset($interface['name']['leveldata']))echo $interface['name']['leveldata'];else echo '等級資料'; ?></a></li>
		<li><a href='#member3'><?php if($interface!='-1'&&isset($interface['name']['memsalelist']))echo $interface['name']['memsalelist'];else echo '會員銷售紀錄'; ?></a></li>
		<!-- <li><a href='#other'>其他項目</a></li> -->
	</ul>
	<div id='member1' style="width:100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;float:left;">
		<h1 style='width:100%;float:left;'><center><?php if($interface!='-1'&&isset($interface['name']['memberlist']))echo $interface['name']['memberlist'];else echo '會員列表'; ?></center></h1>
		<div id='param' style='float:left;display:none;'>
			<input type='hidden' id='prev' value=''>
			<input type='hidden' id='focus' value=''>
			<input type='hidden' id='next' value=''>
		</div>
		<div style='margin-bottom:15px;float:left;'>
			<input type='button' class='initbutton' id='memsalelist' value='<?php if($interface!='-1'&&isset($interface['name']['memsalelist']))echo $interface['name']['memsalelist'];else echo '會員銷售紀錄'; ?>'>
			<input type='button' class='initbutton' id='create' value='<?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?>'>
			<input type='button' class='initbutton' id='edit' value='<?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?>'>
			<input type='button' class='initbutton' id='delete' value='<?php if($interface!='-1'&&isset($interface['name']['stopstate']))echo $interface['name']['stopstate'];else echo '停用'; ?>'>
		</div>
		<div class='table' id="parent" style='width:100%;border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;float:left;'>
			<form class='memberTable' style='float:left;'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<table id='memberTable' style='float:left;'>
					<thead>
						<tr>
							<th></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['number']))echo $interface['name']['number'];else echo '編號'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['name']))echo $interface['name']['name'];else echo '姓名'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['level']))echo $interface['name']['level'];else echo '等級'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['tel1']))echo $interface['name']['tel1'];else echo '電話1'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['point']))echo $interface['name']['point'];else echo '點數'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['money']))echo $interface['name']['money'];else echo '儲值金'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['note']))echo $interface['name']['note'];else echo '備註'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['startdate']))echo $interface['name']['startdate'];else echo '啟用日'; ?></th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($content as $con){
					if(strlen($con['lastdate'])>0){
					}
					else{
						echo "<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='".$con['memno']."'></td><td>".$con['cardno']."</td><td>".$con['name']."</td><td>".$con['pname']."</td><td>".$con['tel']."</td><td style='text-align:right;'>";
						if(!isset($con['point'])||$con['point']==null||$con['point']==''){
							echo '0';
						}
						else{
							echo $con['point'];
						}
						echo "</td><td style='text-align:right;'>";
						if(!isset($con['money'])||$con['money']==null||$con['money']==''){
							echo '0';
						}
						else{
							echo $con['money'];
						}
						echo "</td><td>".$con['remark']."</td><td>";
						if(strlen($con['firstdate'])==10){
							echo preg_replace('/-/','/',$con['firstdate']);
						}
						else{
							echo substr($con['firstdate'],0,4).'/'.substr($con['firstdate'],4,2).'/'.substr($con['firstdate'],6,2);
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
	<div id='member11' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center></center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='save' value='<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>'>
			<input type='button' class='initbutton' id='cancel' value='<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>'>
		</div>
		<div id='datatable' style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='memberdata'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<input type='hidden' id='type' name='type' value=''>
				<input type='hidden' id='memno' name='memno' value=''>
				<table>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['number']))echo $interface['name']['number'];else echo '編號'; ?></td>
						<td><input type='text' id='cardno' name='cardno'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['name']))echo $interface['name']['name'];else echo '姓名'; ?></td>
						<td><input type='text' id='name' name='name'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['sex']))echo $interface['name']['sex'];else echo '性別'; ?></td>
						<td><label><input type='radio' id='b' name='sex' value='1'><?php if($interface!='-1'&&isset($interface['name']['male']))echo $interface['name']['male'];else echo '男性'; ?></label><label><input type='radio' id='g' name='sex' value='2'><?php if($interface!='-1'&&isset($interface['name']['female']))echo $interface['name']['female'];else echo '女性'; ?></label></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['birthdate']))echo $interface['name']['birthdate'];else echo '生日'; ?></td>
						<td><input type='date' id='birth' name='birth'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['tel1']))echo $interface['name']['tel1'];else echo '電話1'; ?></td>
						<td><input type='tel' id='tel' name='tel'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['tel2']))echo $interface['name']['tel2'];else echo '電話2'; ?></td>
						<td><input type='tel' id='tel2' name='tel2'></td>
					</tr>
					<?php
					if(isset($machinedata['memtitle'])&&$machinedata['memtitle']['open']=='1'){
						echo "<tr>
								<td>".$machinedata['memtitle']['titlename']."</td>
								<td><input type='text' id='setting' name='setting'></td>
							</tr>";
					}
					else{
					}
					?>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['point']))echo $interface['name']['point'];else echo '會員點數'; ?></td>
						<td><input type='number' id='point' name='point' value='0'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['money']))echo $interface['name']['money'];else echo '會員儲值金'; ?></td>
						<td><input type='number' id='money' name='money' value='0'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['companynumber']))echo $interface['name']['companynumber'];else echo '帶入統編'; ?></td>
						<td><input type='number' id='companynumber' name='companynumber' value=''></td>
					</tr>
					<tr>
						<td>e-mail</td>
						<td><input type='email' id='email' name='email'></td>
					</tr>
					<tr>
						<td></td>
						<td><label><input type='checkbox' name='receve' value='1' style='zoom:1.8;'><?php if($interface!='-1'&&isset($interface['name']['recevemsg']))echo $interface['name']['recevemsg'];else echo '願意收到本店任何優惠訊息'; ?></label></td>
					</tr>
					<!-- <tr>
						<td>國家</td>
						<td>
							<select id='local' name='country'>
								<?php
								include_once '../../locallist.php';
								foreach($local as $k=>$l){
									if($k=='TW'){
										echo '<option id="'.$k.'" value="'.$k.'" selected>'.$l.'</option>';
									}
									else{
										echo '<option id="'.$k.'" value="'.$k.'">'.$l.'</option>';
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr id='twlocal' class='r1' style=''>
						<td id='title'>縣市</td>
						<td id='local'>
							<select name='sublocal'>
								<?php
								include_once '../../TWsublocal.php';
								foreach($sublocal as $k=>$l){
									echo '<option value="'.$k.'">'.$l.'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr id='chlocal' class='r2' style='display:none;'>
						<td id='title'>區域</td>
						<td id='local'>
							<select name='subclocal'>
								<?php
								include_once '../../chlocal.php';
								foreach($clocal as $k=>$l){
									echo '<optgroup label="'.$k.'">';
									foreach($l as $i=>$v){
										echo '<option value="'.$i.'">'.$v.'</option>';
									}
									echo '</optgroup>';
								}
								?>
							</select>
						</td>
					</tr> -->
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['zip']))echo $interface['name']['zip'];else echo '郵遞區號'; ?></td>
						<td><input type='text' id='zip' name='zip'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['address']))echo $interface['name']['address'];else echo '聯絡地址'; ?></td>
						<td><input type='text' id='address' name='address'></td>
					</tr>
					<!-- <tr>
						<td>如何得知本店</td>
						<td>
							<div class="mod_select" id='howknow'>
								<ul>
									<li>
										<div class="select_box" id='howknowbox'>
											<span class="select_txt"></span><a class="selet_open">▼</a><div class="option">
												<a id='舊顧客介紹'>舊顧客介紹</a>
												<a id="路上經過">路上經過</a>
												<a id="google地圖">google地圖</a>
												<a id="FB">FB</a>
												<a id="LINE@生活圈">LINE@生活圈</a>
											</div>
										</div>
									</li>
								</ul>
								<input type="hidden" name='howknow' id="select_value" value=''>
							</div>
							<input type='hidden' name='othhow' value=''>
						</td>
					</tr> -->
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['level']))echo $interface['name']['level'];else echo '等級'; ?></td>
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
						<td><?php if($interface!='-1'&&isset($interface['name']['startdate']))echo $interface['name']['startdate'];else echo '啟用日'; ?></td>
						<td><input type='date' id='firstdate' name='firstdate' value='<?php echo date('Y-m-d'); ?>'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['note']))echo $interface['name']['note'];else echo '備註'; ?></td>
						<td><textarea id='remark' name="remark" style='height: 200px;resize: none;'></textarea></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div id='member2' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center><?php if($interface!='-1'&&isset($interface['name']['levellist']))echo $interface['name']['levellist'];else echo '等級列表'; ?></center></h1>
		<div id='param' style='display:none;'>
			<input type='hidden' id='prev' value=''>
			<input type='hidden' id='focus' value=''>
			<input type='hidden' id='next' value=''>
		</div>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='create' value='<?php if($interface!='-1'&&isset($interface['name']['create']))echo $interface['name']['create'];else echo '新增'; ?>'>
			<input type='button' class='initbutton' id='edit' value='<?php if($interface!='-1'&&isset($interface['name']['edit']))echo $interface['name']['edit'];else echo '修改'; ?>'>
			<input type='button' class='initbutton' id='delete' value='<?php if($interface!='-1'&&isset($interface['name']['stopstate']))echo $interface['name']['stopstate'];else echo '停用'; ?>'>
		</div>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='powerTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<table id='powerTable'>
					<thead>
						<tr>
							<th></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['levelrank']))echo $interface['name']['levelrank'];else echo '等級權重'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['itemname']))echo $interface['name']['itemname'];else echo '名稱'; ?></th>
							<th><?php if($interface!='-1'&&isset($interface['name']['state']))echo $interface['name']['state'];else echo '狀態'; ?></th>
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
							/*if($yn){
								echo '<span style="font-size:16px;">';if($interface!='-1'&&isset($interface['name']['startstate']))echo $interface['name']['startstate'];else echo '啟用';echo '</span>';
							}
							else{*/
								echo '<span style="font-size:8px;">';if($interface!='-1'&&isset($interface['name']['startstate']))echo $interface['name']['startstate'];else echo '啟用';echo '</span>';
							//}
						}
						else{
							echo '<font color="#ff0000">';if($interface!='-1'&&isset($interface['name']['stopstate']))echo $interface['name']['stopstate'];else echo '停用';echo '</font>';
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
	<div id='member21' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center></center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='save' value='<?php if($interface!='-1'&&isset($interface['name']['save']))echo $interface['name']['save'];else echo '儲存'; ?>'>
			<input type='button' class='initbutton' id='cancel' value='<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>'>
		</div>
		<div id='datatable' style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='powergroup'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<input type='hidden' id='pno' name='pno'>
				<table>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['levelrank']))echo $interface['name']['levelrank'];else echo '等級權重'; ?></td>
						<td><input type='number' id='seq' name='seq' min='1' value='1'><br><span style='font-size:10px;color:#ff0000;'><?php if($interface!='-1'&&isset($interface['name']['levelrankhint']))echo $interface['name']['levelrankhint'];else echo '數值越小權重越高'; ?></span></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['itemname']))echo $interface['name']['itemname'];else echo '名稱'; ?></td>
						<td><input type='text' id='name' name='name'></td>
					</tr>
					<tr>
						<td><?php if($interface!='-1'&&isset($interface['name']['stopstate']))echo $interface['name']['stopstate'];else echo '停用'; ?></td>
						<td><input type='checkbox' id='stop' name='stop'></td>
					</tr>
					<tr>
						<td style='vertical-align:text-top;'><?php if($interface!='-1'&&isset($interface['name']['specialdiscount']))echo $interface['name']['specialdiscount'];else echo '會員折扣'; ?></td>
						<td>
							<input type='tel' id='discount' name='discount' value='100'>%<br><span style='font-size:10px;color:#ff0000;'>e.g.<?php if($interface!='-1'&&isset($interface['name']['specialdisnote']))echo $interface['name']['specialdisnote'];else echo '九折請填入90；無折扣請填入100'; ?></span>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div id='member3' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center><?php if($interface!='-1'&&isset($interface['name']['memsalelist']))echo $interface['name']['memsalelist'];else echo '會員銷售紀錄'; ?></center></h1>
		<form id='senddata' style='margin-bottom:15px;'>
			<table>
				<tr>
					<td>
						查詢會員：
						<select name='memno'>
							<option value='' selected>全部會員</option>
							<?php
							foreach($content as $con){
								if(strlen($con['lastdate'])>0){
								}
								else{
									echo "<option id='".$con['memno']."' value='".$con['memno']."'>".$con['cardno']."-".$con['name']."</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						查詢日期：
						<input type='date' name='startdate' value='<?php echo date('Y-m-d'); ?>'>－<input type='date' name='enddate' value='<?php echo date('Y-m-d'); ?>'>
					</td>
				</tr>
				<tr>
					<td>
						查詢內容：
						<label><input type='radio' name='searchtype' value='1'>購買紀錄</label>、
						<label><input type='radio' name='searchtype' value='2'>儲值紀錄</label>、
						<label><input type='radio' name='searchtype' value='3' checked>以上皆有</label>
					</td>
					<td>
						<input type='button' id='search' value='<?php if($interface!='-1'&&isset($interface['name']['search']))echo $interface['name']['search'];else echo '查詢'; ?>'>
					</td>
				</tr>
			</table>
		</form>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 185.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
		</div>
	</div>
	<!-- <div id='other' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center>如何得知</center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='create' value='新增'>
			<input type='button' class='initbutton' id='edit' value='修改'>
			<input type='button' class='initbutton' id='delete' value='停用'>
		</div>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='selectTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<table id='selectTable'>
					<thead>
						<tr>
							<th></th>
							<th>項目名稱</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($power as $p){
					echo "<tr class='row'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='pg[]' style='display:none;' value='".$p['pno']."'></td><td>".$p['seq']."</td></tr>";
				}
				?>
					</tbody>
				</table>
			</form>
		</div>
	</div> -->
</div>