<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','manufact.db','','','','sqlite');
$sql="SELECT * FROM manulist";
$manulist=sqlquery($conn,$sql,'sqlite');
$sql="SELECT pushlist.*,manulist.manuname AS manuname,manulist.conperson AS conperson,manulist.tel AS tel,manulist.tel2 AS tel2 FROM pushlist JOIN manulist ON manulist.no=pushlist.manuno ORDER BY pushlist.no";
$pushlist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$itemname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini',true);
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/stock.ini')){
	$stock=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/stock.ini',true);
}
else{
	$stock='-1';
}
$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'],'menu.db','','','','sqlite');
$sql='SELECT inumber FROM itemsdata';
$templist=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$itemslist=array();
if($stock=='-1'){
}
else{
	foreach($templist as $tl){
		if($itemname[$tl['inumber']]['state']=='1'&&intval($itemname[$tl['inumber']]['counter'])>0){
			$itemslist[$tl['inumber']]['name']=$itemname[$tl['inumber']]['name1'];
			if(isset($stock[$tl['inumber']])){
				$itemslist[$tl['inumber']]['stock']=$stock[$tl['inumber']]['stock'];
			}
			else{
				$itemslist[$tl['inumber']]['stock']='0';
			}
		}
		else{
		}
	}
}
include_once '../../../tool/checkweb.php';
$yn=check_mobile();
?>
<script>
manufact=$('.manufact').tabs();
manufact.tabs('option','disabled',[2,4]);
$(document).ready(function(){
	$('.manufact ul #allmanufact').click(function(){
		manufact.tabs('option','disabled',[2,4]);
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact1 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact1 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact2 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact2 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
	});
	$('.manufact ul #pushlist').click(function(){
		manufact.tabs('option','disabled',[2,4]);
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact1 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact1 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact2 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact2 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
	});
	$('.manufact ul #stockbut').click(function(){
		manufact.tabs('option','disabled',[2,4]);
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact1 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact1 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact1 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(even)').css({'background-color':'#ffffff'});
		$('.manufact #manufact2 #manufactTable .itemrow:nth-child(odd)').css({'background-color':'#f0f0f0'});
		$('.manufact #manufact2 #manufactTable .itemrow input[type=\"checkbox\"]').prop('checked',false);
		$('.manufact #manufact2 #manufactTable .itemrow #chimg').attr('src','./img/noch.png');
	});
});
$('#manufact1 #manufactTable').tableHeadFixer();
$('#manufact2 #manufactTable').tableHeadFixer();
</script>
<style>
.manufact #manufact1 table,
.manufact #manufact11 table,
.manufact #manufact2 table,
.manufact #manufact21 table {
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
.manufact #manufact1 table thead,
.manufact #manufact2 table thead,
.manufact #manufact21 #items thead {
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
.manufact #manufact1 table td,
.manufact #manufact1 table th,
.manufact #manufact11 table td,
.manufact #manufact2 table td,
.manufact #manufact2 table th,
.manufact #manufact21 table td {
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
.manufact #manufact1 table tbody tr:nth-child(odd),
.manufact #manufact2 table tbody tr:nth-child(odd) {
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
<div class='manufact' style="width:100%;height:100%;overflow:auto;">
	<ul>
		<li><a id='stockbut' href='#nowstock'>庫存列表</a></li>
		<li><a id='pushlist' href='#manufact2'>進貨單</a></li>
		<li><a href='#manufact21'>進貨單資料</a></li>
		<li><a id='allmanufact' href='#manufact1'>廠商列表</a></li>
		<li><a href='#manufact11'>廠商資料</a></li>
	</ul>
	<div id='manufact1' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center>廠商列表</center></h1>
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
			<form class='manufactTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<table id='manufactTable'>
					<thead>
						<tr>
							<th></th>
							<th>公司名稱</th>
							<th>聯絡人</th>
							<th>市話</th>
							<th>手機</th>
							<th>email</th>
							<th>備註</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($manulist as $con){
					if($con['state']==0){
					}
					else{
						echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='".$con['no']."'></td><td>".$con['manuname']."</td><td>".$con['conperson']."</td><td>".$con['tel']."</td><td>".$con['tel2']."</td><td>".$con['email']."</td><td>".$con['remark']."</td></tr>";
					}
				}
				?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div id='manufact11' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center></center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='save' value='儲存'>
			<input type='button' class='initbutton' id='cancel' value='取消'>
		</div>
		<div id='datatable' style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='manufactdata'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' id='type' name='type' value=''>
				<input type='hidden' id='no' name='no' value=''>
				<table>
					<tr>
						<td>廠商編號</td>
						<td><input type='text' id='manuno' name='manuno'></td>
					</tr>
					<tr>
						<td>廠商名稱</td>
						<td><input type='text' id='manuname' name='manuname'></td>
					</tr>
					<tr>
						<td>負責人</td>
						<td><input type='text' id='mainperson' name='mainperson'></td>
					</tr>
					<tr>
						<td>連絡人</td>
						<td><input type='text' id='conperson' name='conperson'></td>
					</tr>
					<tr>
						<td>市話</td>
						<td><input type='tel' id='tel' name='tel'></td>
					</tr>
					<tr>
						<td>手機</td>
						<td><input type='tel' id='tel2' name='tel2'></td>
					</tr>
					<tr>
						<td>傳真</td>
						<td><input type='tel' id='fax' name='fax'></td>
					</tr>
					<tr>
						<td>email</td>
						<td><input type='text' id='email' name='email'></td>
					</tr>
					<tr>
						<td>統編</td>
						<td><input type='tel' id='banno' name='banno'></td>
					</tr>
					<tr>
						<td>郵遞區號</td>
						<td><input type='text' id='zip1' name='zip1'></td>
					</tr>
					<tr>
						<td>送貨地址</td>
						<td><input type='text' id='sendaddress' name='sendaddress'></td>
					</tr>
					<tr>
						<td>郵遞區號</td>
						<td><input type='text' id='zip2' name='zip2'></td>
					</tr>
					<tr>
						<td>帳單地址</td>
						<td><input type='text' id='billaddress' name='billaddress'></td>
					</tr>
					<tr>
						<td>備註</td>
						<td><textarea id='remark' name='remark' style='height:200px;resize:none;'></textarea></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div id='manufact2' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center>進貨單列表</center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='create' value='新增'>
			<input type='button' class='initbutton' id='edit' value='修改'>
			<input type='button' class='initbutton' id='delete' value='刪除'>
		</div>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='manufactTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<table id='manufactTable'>
					<thead>
						<tr>
							<th></th>
							<th>進貨單編號</th>
							<th>公司名稱</th>
							<th>聯絡人</th>
							<th>市話</th>
							<th>手機</th>
							<th>發票號</th>
							<th>應付金額</th>
							<th>付款狀況</th>
							<th>付款日期</th>
							<th>備註</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($pushlist as $con){
					if($con['state']==0){
					}
					else{
						echo "<tr class='itemrow'><td><img id='chimg' src='./img/noch.png'><input type='checkbox' class='checkbox[]' name='no[]' style='display:none;' value='".$con['no']."'></td><td>".$con['listno']."</td><td>".$con['manuname']."</td><td>".$con['conperson']."</td><td>".$con['tel']."</td><td>".$con['tel2']."</td><td>".$con['invnumber']."</td><td style='text-align:right;'>".$con['ttmoney']."</td>";
						if($con['paystate']=='1'){
							echo "<td>已付款</td>";
						}
						else{
							echo "<td>未付款</td>";
						}
						echo "<td>".$con['paydate']."</td><td>".$con['remark']."</td></tr>";
					}
				}
				?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div id='manufact21' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center></center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='save' value='儲存'>
			<input type='button' class='initbutton' id='cancel' value='取消'>
		</div>
		<div id='datatable' style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='manufactdata'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<input type='hidden' id='type' name='type' value=''>
				<input type='hidden' id='no' name='no' value=''>
				<table>
					<tr>
						<td>進貨單編號</td>
						<td><input type='text' id='listno' name='listno' value='<?php $conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','manufact.db','','','','sqlite');$sql="SELECT COUNT(*) AS num FROM pushlist WHERE createdatetime LIKE '".date('Ymd')."%'";$list=sqlquery($conn,$sql,'sqlite');sqlclose($conn,'sqlite');echo date('ymd').str_pad(intval($list[0]['num'])+1, 3, "0", STR_PAD_LEFT); ?>'></td>
					</tr>
					<tr>
						<td>廠商</td>
						<td id='manuselect'>
							<?php
							include_once 'getmanulist.select.php';
							?>
						</td>
					</tr>
					<tr>
						<td>品項</td>
						<td colspan='2'>
							<div style='width:100%;border:1px solid #898989;'>
								<table id='items' style='width:100%;'>
									<thead>
										<tr>
											<th>產品</th>
											<th>數量</th>
											<th>單位</th>
											<th>小計</th>
										</tr>
									</thead>
									<tbody>
									<?php
									include_once 'getitemlist.select.php';
									?>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td>應付金額</td>
						<td><input type='tel' id='ttmoney' name='ttmoney' style='text-align:right;' value='0' readonly>元</td>
					</tr>
					<tr>
						<td>付款狀況</td>
						<td><label><input type='radio' name='paystate' value='0'>未付款</label>、<label><input type='radio' name='paystate' value='1'>已付款</label></td>
					</tr>
					<tr>
						<td>發票號</td>
						<td><input type='text' id='invnumber' name='invnumber'></td>
					</tr>
					<tr>
						<td>付款日期</td>
						<td><input type='date' style='width:235px;' id='paydate' name='paydate' disabled></td>
					</tr>
					<tr>
						<td>備註</td>
						<td><textarea id='remark' name='remark' style='width:235px;height:200px;resize:none;'></textarea></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div id='nowstock' style="width:100%;height:calc(100% - 50px);overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;overflow:hidden;">
		<h1><center>庫存列表</center></h1>
		<div style='margin-bottom:15px;'>
			<input type='button' class='initbutton' id='reflash' value='更新庫存'>
		</div>
		<div class='table' id="parent" style='width:100%;height:calc(100% - 164.2px);border:1px solid #898989;overflow:auto;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;'>
			<form class='manufactTable'>
				<input type='hidden' name='company' value='<?php echo $_POST['company']; ?>'>
				<input type='hidden' name='dep' value='<?php echo $_POST['dep']; ?>'>
				<input type='hidden' name='lastdate' value='<?php echo date('YmdHis'); ?>'>
				<?php
				if($stock=='-1'||sizeof($itemslist)==0){
					echo '目前暫無任何產品有紀錄庫存。';
					if($stock=='-1'){
						echo '<input type="hidden" value="stock-1">';
					}
					else{
					}
					if(sizeof($itemslist)==0){
						echo '<input type="hidden" value="itemslist=0">';
					}
					else{
					}
				}
				else{
				?>
				<table id='manufactTable'>
					<thead>
						<tr>
							<th>產品名稱</th>
							<th>目前庫存</th>
							<th>備註</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach($itemslist as $index=>$con){
					echo "<tr class='itemrow' ";if(intval($con['stock'])==0)echo 'style="color:#ff0000;"';echo "><td><input type='hidden' name='itemno' value='".$index."'>".$con['name']."</td><td style='text-align:right;'>".$con['stock']."</td><td>";if(intval($con['stock'])==0)echo '無庫存';echo "</td></tr>";
				}
				?>
					</tbody>
				</table>
				<?php
				}
				?>
			</form>
		</div>
	</div>
</div>