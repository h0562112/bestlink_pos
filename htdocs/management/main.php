<?php
session_start();
include_once '../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
if(isset($_GET['lan'])&&$_GET['lan']!=''){
	if(file_exists('./lan/interface'.$_GET['lan'].'.ini')){
		$interface=parse_ini_file('./lan/interface'.$_GET['lan'].'.ini',true);
	}
	else{
		$interface='-1';
	}
}
else{
	if(file_exists('./lan/interface1.ini')){
		$interface=parse_ini_file('./lan/interface1.ini',true);
	}
	else{
		$interface='-1';
	}
}
?>
<head>
	<meta charset="UTF-8">
	<title><?php if($interface!='-1'&&isset($interface['name']['webtitle']))echo $interface['name']['webtitle'];else echo 'POS管理介面'; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<link rel="stylesheet" href="../tool/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="./lib/css/main.css">
	<link href="../tool/css/evol-colorpicker.min.css" rel="stylesheet" />
	<script src="../tool/jquery-1.12.4.js"></script>
	<script src="../tool/ui/1.12.1/jquery-ui.js"></script>
	<script src="../tool/TableHeadFixer/tableHeadFixer.js"></script>
	<script src="./lib/js/main.js?<?php echo date('YmdHis'); ?>"></script>
	<script>
	$(document).ready(function(){
		$(document).click(function(){
			$.ajax({
				url:'./lib/js/checklogin.ajax.php',
				dataType:'html',
				success:function(d){
					console.log(d);
				},
				error:function(e){
					console.log(e);
				}
			});
		});
	});
	</script>

	<script src="../tool/js/evol-colorpicker.min.js?<?php echo date('YmdHis'); ?>" type="text/javascript"></script>
	<?php
	if(isset($_POST['DB'])){
		$_SESSION['DB']=$_POST['DB'];
		if(isset($_POST['startdate'])){
			$_SESSION['startdate']=$_POST['startdate'];
		}
		else{
		}
		if(isset($_POST['enddate'])){
			$_SESSION['enddate']=$_POST['enddate'];
		}
		else{
		}
	}
	else{
		if(isset($_POST['startdate'])){
			$_SESSION['startdate']=$_POST['startdate'];
		}
		else{
		}
		if(isset($_POST['enddate'])){
			$_SESSION['enddate']=$_POST['enddate'];
		}
		else{
		}
	}
	if(!isset($_SESSION['ID'])||$_SESSION['ID']==""){
		echo "<script>location.href='./index.php';</script>";
	}
	else{
		if(isset($_POST['startdate'])){
			$_SESSION['startdate']=$_POST['startdate'];
		}
		else if(isset($_POST['enddate'])){
			$_SESSION['enddate']=$_POST['enddate'];
		}
		else{
			$_SESSION['startdate']=date("Y-m-01");
			$_SESSION['enddate']=date("Y-m-t");
		}
		$ID=$_SESSION['ID'];
		$name=$_SESSION['name'];
		if(isset($_SESSION['usergroup'])){
			$usergroup=$_SESSION['usergroup'];
		}
		else{
		}
		$DB=$_SESSION['DB'];
		if(isset($_SESSION['startdate'])||isset($_SESSION['enddate'])){
			$startdate=$_SESSION['startdate'];
			$enddate=$_SESSION['enddate'];
		}
		else{
			$startdate=date("Y-m-01");
			$enddate=date("Y-m-d");
		}
	}
	?>
</head>
<body style='width:100%;height:100vh;padding:0;margin:0;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;'>
	<?php
	if(isset($_GET['management'])){
	?>
	<input type='hidden' class='management'>
	<?php
	}
	else if(isset($_GET['inifile'])){
	?>
	<input type='hidden' class='inifileswitch'>
	<?php
	}
	else{
	}
	?>
	<input type='hidden' class='lan' value='<?php if(isset($_GET['lan'])&&$_GET['lan']!='')echo $_GET['lan'];else echo '1'; ?>'>
	<div style='margin:0 auto;max-width:1366px;overflow:auto;position: relative;'>
		<div style='position: absolute;top: 0;right: 0;margin: 10px;'>
		<?php
		if(isset($_SESSION['company'])&&isset($_SESSION['DB'])){
			if(file_exists('../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/machinedata.ini')){
				$machinedata=parse_ini_file('../ourpos/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/machinedata.ini',true);
				$initsetting=parse_ini_file('../menudata/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/initsetting.ini',true);
				$setup=parse_ini_file('../menudata/'.$_SESSION['company'].'/'.$_SESSION['DB'].'/setup.ini',true);
				if(isset($setup['basic']['storyname'])){
					echo $setup['basic']['storyname'].'<input type="hidden" value="'.$_SESSION['DB'].'">';
				}
				else{
					echo $machinedata['basic']['story'].'<input type="hidden" value="'.$_SESSION['DB'].'">';
				}
			}
			else{
			}
		}
		else{
		}
		?>
		</div>
		<input type='hidden' name='company' value='<?php echo $_SESSION['company']; ?>'>
		<input type='hidden' name='db' value='<?php echo $_SESSION['DB']; ?>'>
		<img id='logo' src='./img/banner.png' width='100%'>
		<div id='menu_div'>
			<em class="first"></em><em class="middle"></em><em class="last"></em>
		</div>
		<nav id='sidebar' <?php /*if($_SESSION['ID']=='admin')echo "class='accordion'";*/ ?>>
			<?php /*if($_SESSION['ID']=='admin')echo "<h3>產品設定</h3><div style='width:150px;padding:0;border:0;'>";*/ ?>
			<ul id='nav'>
				<?php
				if(isset($initsetting['init']['useinv'])&&$initsetting['init']['useinv']=='1'&&(!isset($_GET['lan'])||$_GET['lan']=='TW')){
				?>
				<li><input id='hyper' class='initbutton' type='button' value='<?php if($interface!='-1'&&isset($interface['name']['invmenu']))echo $interface['name']['invmenu'];else echo '電子發票專區'; ?>'></li>
				<?php
				}
				else{
				}
				?>
				<div style="border: 1px #898989 solid;border-radius: 5px;">
					<?php
					if($_SESSION['ID']!='teatalk001'){
					?>
					<li><input id='allunit' class='initbutton' type='button' value='<?php if($interface!='-1'&&isset($interface['name']['unitmenu']))echo $interface['name']['unitmenu'];else echo '單位維護'; ?>'></li>
					<?php
					}
					else{
					}
					?>
					<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='alltype' class='initbutton' type='button' value='<?php if($interface!='-1'&&isset($interface['name']['typemenu']))echo $interface['name']['typemenu'];else echo '類別維護'; ?>' <?php if($_SESSION['ID']=='teatalk001')echo 'disabled'; ?>></li>
					<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='allsectype' class='initbutton' type='button' value='<?php if($interface!='-1'&&isset($interface['name']['anatypemenu']))echo $interface['name']['anatypemenu'];else echo '分析類別維護'; ?>' <?php if($_SESSION['ID']=='teatalk001')echo 'disabled'; ?>></li>
					<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='alltaste' class='initbutton' type='button' value='<?php if($interface!='-1'&&isset($interface['name']['tastemenu']))echo $interface['name']['tastemenu'];else echo '備註與加料維護'; ?>' <?php if($_SESSION['ID']=='teatalk001')echo 'disabled'; ?>></li>
					<li><input id='printlisttag' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['printmenu']))echo $interface['name']['printmenu'];else echo '列印類別'; ?>"></li>
					<?php
					if(isset($initsetting['init']['kds'])&&$initsetting['init']['kds']=='1'){
					?>
					<li><input id='setkds' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['kds']))echo $interface['name']['kds'];else echo '廚房控餐設定'; ?>"></li>
					<?php
					}
					else{
					}
					?>
					<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='allitems' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['itemmenu']))echo $interface['name']['itemmenu'];else echo '商品維護'; ?>" <?php if($_SESSION['ID']=='teatalk001')echo 'disabled'; ?>></li>
				</div>
				<?php
				if($initsetting['init']['secview']=='1'){
				?>
				<li><input id='secview' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['secview']))echo $interface['name']['secview'];else echo '客顯設定'; ?>"></li>
				<?php
				}
				else{
				}
				?>
				<?php
				if($initsetting['init']['controltable']=='1'){
				?>
				<li><input id='table' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['tablemenu']))echo $interface['name']['tablemenu'];else echo '即時桌控'; ?>"></li>
				<?php
				}
				else{
				}
				?>
				<li><input id='editpw' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['editpw']))echo $interface['name']['editpw'];else echo '修改密碼'; ?>"></li>
				<li><input id='paper1' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['papermenu']))echo $interface['name']['papermenu'];else echo '銷售報表'; ?>"></li>
				<?php
				if(isset($_GET['management'])){
				?>
				<!-- <li><input id='memsalelist' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['memsalelist']))echo $interface['name']['memsalelist'];else echo '會員銷售紀錄'; ?>"></li> -->
				<?php
				}
				else{
				}
				?>
				<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='allmembers' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['membermenu']))echo $interface['name']['membermenu'];else echo '會員維護'; ?>" <?php if($_SESSION['ID']=='teatalk001')echo 'disabled'; ?>></li>
				<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='allpersonnels' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['personmenu']))echo $interface['name']['personmenu'];else echo '員工打卡維護'; ?>" <?php if($_SESSION['ID']=='teatalk001')echo 'disabled'; ?>></li>
				<?php
				if((!isset($_GET['lan'])||$_GET['lan']=='TW')){
				?>
				<li><input id='allmanufact' class='initbutton' type='button' value='<?php if($interface!='-1'&&isset($interface['name']['stockmenu']))echo $interface['name']['stockmenu'];else echo '庫存維護'; ?>'></li>
				<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='allpersons' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['loginmenu']))echo $interface['name']['loginmenu'];else echo '登入帳號維護'; ?>" <?php if($_SESSION['ID']=='teatalk001')echo 'disabled'; ?>></li>
				<?php
				}
				else{
				}
				?>
				<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='otherpay' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['otherpay']))echo $interface['name']['otherpay'];else echo '其他付款'; ?>"></li>
				<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='autodis' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['autodis']))echo $interface['name']['autodis'];else echo '自動優惠'; ?>"></li>
				<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='inoutmoney' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['inoutmoney']))echo $interface['name']['inoutmoney'];else echo '收/支費用科目'; ?>"></li>
				<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='inifile' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['inifile']))echo $interface['name']['inifile'];else echo '設定檔維護'; ?>"></li>
				<li <?php /*if($_SESSION['ID']=='admin')echo "style='padding:0;'";*/ ?>><input id='logout' class='initbutton' type="button" value="<?php if($interface!='-1'&&isset($interface['name']['logoutmenu']))echo $interface['name']['logoutmenu'];else echo '登出'; ?>"></li>
			</ul>
			<?php /*if($_SESSION['ID']=='admin')echo "</div>";*/ ?>
		</nav>
		<div id='content' style='float:left;'>
			<?php if($interface!='-1'&&isset($interface['name']['welcome']))echo $interface['name']['welcome'];else echo '歡迎使用POS管理平台'; ?>
		</div>
	</div>
	<div class='mys'>
	</div>
	<div class='billboard' style='padding:10px;overflow-x:hidden;overflow-y:auto;font-size:30px;' title='<?php if($interface!='-1'&&isset($interface['name']['billboardtitle']))echo $interface['name']['billboardtitle'];else echo '系統公告'; ?>'>
		<div id='content' style='width:100%;float:left;'>
		</div>
		<div style='width:100%;heigth:20px;float:left;text-align:center;'>
			<button class='checkbillboard initbutton'><div><?php if($interface!='-1'&&isset($interface['name']['checkbillboard']))echo $interface['name']['checkbillboard'];else echo '確認'; ?></div></button>
		</div>
	</div>
	<div class='expmsg'>
		<form id='condition' style='width:100%;overflow:hidden;margin:0;'>
			<table style='margin:22px auto;'>
				<caption><?php if($interface!='-1'&&isset($interface['name']['timerange']))echo $interface['name']['timerange'];else echo '時間區間'; ?></caption>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['timestart']))echo $interface['name']['timestart'];else echo '開始時間'; ?></td>
					<td><input type='date' name='startdate' value='<?php echo date('Y-m-01',strtotime(date('Ymd'))); ?>'></td>
				</tr>
				<tr>
					<td><?php if($interface!='-1'&&isset($interface['name']['timeend']))echo $interface['name']['timeend'];else echo '結束時間'; ?></td>
					<td><input type='date' name='enddate' value='<?php echo date('Y-m-d',strtotime(date('Ymd').' +1 days')); ?>'></td>
				</tr>
				<tr>
					<td style='text-align:center;' colspan='2'><input type='button' style='margin:1px;' id='exp' value='<?php if($interface!='-1'&&isset($interface['name']['exp']))echo $interface['name']['exp'];else echo '匯出'; ?>'><input type='button' style='margin:1px;' id='cancel' value='<?php if($interface!='-1'&&isset($interface['name']['cancel']))echo $interface['name']['cancel'];else echo '取消'; ?>'></td>
				</tr>
			</table>
		</form>
	</div>
</body>