<?php
/*
$tempmenu這個陣列，需要改連伺服器中的mysql資料庫，會將每家店的菜單存在伺服器


圖形化報表:
現行步驟 將點餐明細做簡單查詢分類＞再次查詢菜單、班別
改版步驟 先查詢班別，判斷單月起訖班別號＞利用班別號當區間index
*/
function content($ID,$company,$DB,$usergroup,$startdate,$enddate){
	//echo $usergroup."<br>";
	if(isset($_POST['conttype'])&&$_POST['conttype']=='menu'){
		include_once 'con_fun_menu.inc.php';
		con_menu($ID,$company,$DB,$usergroup,$startdate,$enddate);
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='type'){
		include_once 'con_fun_type.inc.php';
		con_type($ID,$company,$DB,$usergroup,$startdate,$enddate);
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='taste'){
		include_once 'con_fun_taste.inc.php';
		con_taste($ID,$company,$DB,$usergroup,$startdate,$enddate);
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='sale'){
		include_once 'con_fun_sale.inc.php';
		con_sale($ID,$company,$DB,$usergroup,$startdate,$enddate);
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='sort'){
		echo "建構中...";
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='monnumber'){
		if(strlen($enddate)==10){
			$enddate=substr($enddate,0,7);
		}
		echo "<form method='post' action='' id='search'>
				<input type='hidden' name='conttype' value='monnumber'>
				<table>
					<!-- <caption>設定時間區間</caption> -->";
				echo "<tr>
						<td>時間區間</td>
						<td>
							<input type='month' name='enddate' id='enddate'";if(strlen($enddate)>0)echo " value=".$enddate;echo ">
						</td>
					</tr>
					<tr>
						<td colspan='2'><input type='button' value='送出' onclick='mondaycheck(this.form)'></td>
					</tr>
				</table>
			</form>";
		if(empty($enddate)){
			echo "請先至基本選項設定想瀏覽的時間。";
		}
		else{
			$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
			if(!$conn){
				echo "資料庫發生錯誤或尚未上傳資料。";
			}
			else{
				$table=array();//儲存月營業匯總表
				$story=array();//儲存所有門市
				$csv=array();//匯出檔案內容
				$sql="SELECT C.dept,C.deptname,A.amt,B.number
				FROM UserLogin
				JOIN (
					SELECT company,deptname,dept FROM UserLogin WHERE dept<>'0'
				) AS C ON UserLogin.company=C.company
				JOIN (
					SELECT company,bizdate,zcounter,SUM(amt) AS amt FROM alldetails WHERE bizdate BETWEEN '".$enddate."-01' AND '".$enddate."-".date("t",mktime('0','0','0','1',substr($enddate,5,2),substr($enddate,0,4)))."' GROUP BY company
				) AS A ON C.dept=A.company
				JOIN (
					SELECT company,SUM(number) AS number
					FROM (
						SELECT bizdate,zcounter,company,SUM(number) AS number 
						FROM (
							SELECT DISTINCT bizdate,company,consecnumber,salesttlamt,zcounter,CASE WHEN salesttlamt>0 THEN 1 WHEN salesttlamt=0 THEN 0 ELSE -1 END AS number FROM alldetails WHERE CAST(invoicenumber AS INT)>0 AND bizdate BETWEEN '".$enddate."-01' AND '".$enddate."-".date("t",mktime('0','0','0','1',substr($enddate,5,2),substr($enddate,0,4)))."'
						) AS table1 GROUP BY bizdate,zcounter,company
					) AS table2 GROUP BY company
				) AS B ON B.company=A.company
				WHERE UserLogin.id='".$ID."' AND UserLogin.function='pos'";
				$table=sqlquery($conn,$sql,'mysql');
				$sql="SELECT C.dept,C.deptname FROM UserLogin 
				JOIN (
					SELECT company,deptname,dept FROM UserLogin WHERE dept<>'0' AND function='pos'
				) AS C ON UserLogin.company=C.company WHERE UserLogin.id='".$ID."'";
				$story=sqlquery($conn,$sql,'mysql');
				if(sizeof($table)==0){
					echo "查無資料。";
				}
				else if($table[0]=="SQL語法錯誤"||$table[0]=="連線失敗"){
					if($dubug==1){
						echo $table[0]."(select)".$sql;
					}
					else{
						echo $table[0]."(select)";
					}
				}
				else{
					$mondetail=array();//月營業匯總表
					foreach($table as $temp){
						$mondetail[$temp['dept']]['amt']=$temp['amt'];
						$mondetail[$temp['dept']]['number']=$temp['number'];
					}
					echo "<div id='title'><form method='post' action='toCSV.php' target='_blank'><input type='submit' value='匯出檔案'></form><div id='paper'>";
					echo "<table>
							<caption><h3>門市月營業匯總表</h3></caption>
							<tr>
								<td>編號</td>
								<td>名稱</td>
								<td>總營業額</td>
								<td>總帳單數</td>
							</tr>";
					array_push($csv,array(substr($enddate,5,2)."月營業匯總表"));
					array_push($csv,array("編號","名稱","總營業額","總帳單數"));
					foreach($story as $temp){
						
						echo "<tr>
								<td>".$temp['dept']."</td>
								<td>".$temp['deptname']."</td>";
						if(isset($mondetail[$temp['dept']]['amt'])){
							echo "<td class='item'>".$mondetail[$temp['dept']]['amt']."</td>
								<td class='item'>".$mondetail[$temp['dept']]['number']."</td>";
							array_push($csv,array($temp['dept'],$temp['deptname'],$mondetail[$temp['dept']]['amt'],$mondetail[$temp['dept']]['number']));
						}
						else{
							echo "<td class='item'>0</td>
								<td class='item'>0</td>";
							array_push($csv,array($temp['dept'],$temp['deptname'],0,0));
						}
						echo "</tr>";
					}
					echo "</table>";
					echo "</div></div>";
				}
				//用session的方式傳遞檔案匯出陣列
				if(isset($_SESSION['array'])){
					unset($_SESSION['array']);
					$_SESSION['array']=$csv;
				}
				else{
					$_SESSION['array']=$csv;
				}
			}
			sqlclose($conn,"mysql");
		}
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='daynumber'){
		if(strlen($enddate)==10){
			$datetime1 = new DateTime(substr($enddate,0,4)."-".substr($enddate,5,2)."-".substr($enddate,8,2));
			$datetime2 = new DateTime(date("Y-m-").intval(date("d"))-1);
			$interval = $datetime1->diff($datetime2);
			if($interval->format("%R")=="-"){
				if(strlen(strval(intval(date("d"))-1))<2){
					$d="0".(strval(intval(date("d"))-1));
				}
				else{
					$d=strval(intval(date("d"))-1);
				}
				$enddate=date("Y-m-").$d;
			}
			else{
				$enddate=$enddate;
			}
		}
		else{
			if(strlen(strval(intval(date("d"))-1))<2){
				$d="0".(strval(intval(date("d"))-1));
			}
			else{
				$d=strval(intval(date("d"))-1);
			}
			$enddate=date("Y-m-").$d;
		}
		/*$datetime1 = new DateTime('2009-10-11');
		$datetime2 = new DateTime('2009-10-13');
		$interval = $datetime1->diff($datetime2);
		echo "<input type='hidden' value='".$interval->format("%R")."'>";*/
		/*if(strlen($enddate)<10){
			$enddate=date("Y-m-").(intval(date("d"))-1);
		}*/
		echo "<form method='post' action='' id='search'>
				<input type='hidden' name='conttype' value='daynumber'>
				<table>
					<!-- <caption>設定時間區間</caption> -->";
				echo "<tr>
						<td>時間區間</td>
						<td>
							<input type='date' name='enddate' id='enddate'";if(strlen($enddate)>0)echo " value=".$enddate;echo ">
						</td>
					</tr>
					<tr>
						<td colspan='2'><input type='button' value='送出' onclick='mondaycheck(this.form)'></td>
					</tr>
				</table>
			</form>";
		if(empty($enddate)){
			echo "請先至基本選項設定想瀏覽的時間。";
		}
		else{
			$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
			if(!$conn){
				echo "資料庫發生錯誤或尚未上傳資料。";
			}
			else{
				$table=array();//儲存日營業匯總表
				$story=array();//儲存所有門市
				$csv=array();//匯出檔案內容
				$sql="SELECT C.dept,C.deptname,A.amt,B.number
				FROM UserLogin
				JOIN (
					SELECT company,deptname,dept FROM UserLogin WHERE dept<>'0'
				) AS C ON UserLogin.company=C.company
				JOIN (
					SELECT company,bizdate,zcounter,SUM(amt) AS amt FROM alldetails WHERE bizdate='".$enddate."' GROUP BY company
				) AS A ON C.dept=A.company
				JOIN (
					SELECT company,SUM(number) AS number
					FROM (
						SELECT bizdate,zcounter,company,SUM(number) AS number 
						FROM (
							SELECT DISTINCT bizdate,company,consecnumber,salesttlamt,zcounter,CASE WHEN salesttlamt>0 THEN 1 WHEN salesttlamt=0 THEN 0 ELSE -1 END AS number FROM alldetails WHERE CAST(invoicenumber AS INT)>0 AND bizdate='".$enddate."'
						) AS table1 GROUP BY bizdate,zcounter,company
					) AS table2 GROUP BY company
				) AS B ON B.company=A.company
				WHERE UserLogin.id='".$ID."' AND UserLogin.function='pos'";
				$table=sqlquery($conn,$sql,'mysql');
				$sql="SELECT C.dept,C.deptname FROM UserLogin 
				JOIN (
					SELECT company,deptname,dept FROM UserLogin WHERE dept<>'0' AND function='pos'
				) AS C ON UserLogin.company=C.company WHERE UserLogin.id='".$ID."'";
				$story=sqlquery($conn,$sql,'mysql');
				if(sizeof($table)==0){
					echo "查無資料。";
				}
				else if($table[0]=="SQL語法錯誤"||$table[0]=="連線失敗"){
					if($dubug==1){
						echo $table[0]."(select)".$sql;
					}
					else{
						echo $table[0]."(select)";
					}
				}
				else{
					$daydetail=array();//日營業匯總表
					foreach($table as $temp){
						$daydetail[$temp['dept']]['amt']=$temp['amt'];
						$daydetail[$temp['dept']]['number']=$temp['number'];
					}
					echo "<div id='title'><form method='post' action='toCSV.php' target='_blank'><input type='submit' value='匯出檔案'></form><div id='paper'>";
					echo "<table>
							<caption><h3>門市日營業匯總表</h3></caption>
							<tr>
								<td>編號</td>
								<td>名稱</td>
								<td>總營業額</td>
								<td>總帳單數</td>
							</tr>";
					array_push($csv,array(date("Y/m/").(intval(date("d"))-1)."營業匯總表"));
					array_push($csv,array("編號","名稱","總營業額","總帳單數"));
					foreach($story as $temp){
						
						echo "<tr>
								<td>".$temp['dept']."</td>
								<td>".$temp['deptname']."</td>";
						if(isset($daydetail[$temp['dept']]['amt'])){
							echo "<td class='item'>".$daydetail[$temp['dept']]['amt']."</td>
								<td class='item'>".$daydetail[$temp['dept']]['number']."</td>";
							array_push($csv,array($temp['dept'],$temp['deptname'],$daydetail[$temp['dept']]['amt'],$daydetail[$temp['dept']]['number']));
						}
						else{
							echo "<td class='item'>0</td>
								<td class='item'>0</td>";
							array_push($csv,array($temp['dept'],$temp['deptname'],0,0));
						}
						echo "</tr>";
					}
					echo "</table>";
					echo "</div></div>";
				}
				//用session的方式傳遞檔案匯出陣列
				if(isset($_SESSION['array'])){
					unset($_SESSION['array']);
					$_SESSION['array']=$csv;
				}
				else{
					$_SESSION['array']=$csv;
				}
			}
			sqlclose($conn,"mysql");
		}
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='storynumber'){
		echo "<form method='post' action='' id='search'>
				<input type='hidden' name='conttype' value='storynumber'>
				<table>
					<!-- <caption>設定時間區間</caption> -->";
				if($_SESSION['ID']=='admin'){
					echo "<tr>
							<td>資料庫</td>
							<td><select name='DB'>";
					$Path="../../DB";
					foreach(glob($Path."/*") as $entry) {
					  if (in_array($entry, array(".", "..")) === false) {
						 $temp=preg_split("(".$Path."/)",$entry);
						 echo "<option value='".$temp[1]."'>".$temp[1]."</option>";
					  }
					}
					echo "</select></td>
						</tr>";
				}
				/*else if($usergroup=='boss'){
					$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
					$sql="SELECT usedb,deptname FROM UserLogin WHERE company=(SELECT company FROM UserLogin WHERE id='".$_SESSION['ID']."') AND usergroup IS NULL";
					$temp=sqlquery($conn,$sql,"mysql");
					sqlclose($conn,"mysql");
					echo "<tr>
							<td>資料庫</td>
							<td><select name='DB'>";
					foreach($temp as $value){
						echo "<option value='".$value['usedb']."' ";
						if(!empty($_SESSION['DB'])&&$_SESSION['DB']==$value['usedb']){
							echo "selected";
						}
						echo ">".$value['deptname']."</optioin>";
					}
						echo "</select></td>
						</tr>";
				}
				else{
				}*/
				echo "<tr>
						<td>時間區間</td>
						<td>
							<input type='date' name='startdate' id='startdate'";if(strlen($startdate)>0)echo " value=".$startdate;echo ">～<input type='date' name='enddate' id='enddate'";if(strlen($enddate)>0)echo " value=".$enddate;echo ">
						</td>
					</tr>
					<tr>
						<td colspan='2'><input type='button' value='送出' onclick='basiccheck(this.form)'></td>
					</tr>
				</table>
			</form>";
		if(empty($startdate)&&empty($enddate)){
			echo "請先至基本選項設定想瀏覽的時間。";
		}
		else{
			$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
			if(!$conn){
				echo "資料庫發生錯誤或尚未上傳資料。";
			}
			else{
				$table=array();//暫存店家商品明細
				$tabarray=array();//將上方陣列整理成>>$tabarray[company][itemdeptcode][itemcode][qty]=商品銷售量；$tabarray[company][itemdeptcode][itemcode][amt]=商品銷售金額
				$menu=array();//儲存所有門市所使用之商品
				$menuarray=array();//將上方陣列整理成>>$menuarray['deptnumber']=分類數量；$menuarray[itemdeptcode]['totalnumber']=分類中數量；$menuarray[itemdeptcode]['money']=分類銷售總額；$menuarray[itemdeptcode][itemcode0~N]['name']=商品名(編號0為分類名)；$menuarray[itemdeptcode][itemcode1~N]['code']=商品編號；$menuarray[itemdeptcode][itemcode1~N]['number']=商品銷售量
				$story=array();//儲存所有門市
				$stoarray=array();//將上方陣列整理成>>$stoarray[company]=companyname;
				$discount=array();//暫存所有店家折扣資訊
				$cond=array();//將上方陣列整理成>>$cond[company]=門市折扣金額
				$totalcond=0;//總折扣
				$totalsingle=0;//單點總額
				$totalmoney=0;//總營收
				$totalnumber=0;//總帳單數
				$csv=array();//匯出檔案內容
				echo "<div id='title'>";
				//表格B>>各門市之商品銷售量與金額；表格C>>各班別之營業額；表格E>>各班別之帳單數
				$sql="SELECT DISTINCT A.company,A.itemcode,A.itemname,A.itemgrpcode,A.itemdeptcode,A.itemdeptname,A.qty,A.amt,B.number
					FROM (
						SELECT DISTINCT company,itemcode,itemname,itemgrpcode,itemgrpname,itemdeptcode,itemdeptname,SUM(qty) AS qty,SUM(amt) AS amt
						FROM alldetails WHERE bizdate BETWEEN '".$startdate."' AND '".$enddate."' AND dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008' GROUP BY company,itemcode ORDER BY company,itemcode
					) AS A
					JOIN (
						SELECT company,SUM(number) AS number 
						FROM (
							SELECT DISTINCT bizdate,company,consecnumber,salesttlamt,zcounter,CASE WHEN salesttlamt>0 THEN 1 WHEN salesttlamt=0 THEN 0 ELSE -1 END AS number FROM alldetails WHERE bizdate BETWEEN '".$startdate."' AND '".$enddate."' AND CAST(invoicenumber AS INT)>0
						) AS A GROUP BY company
					) AS B ON B.company=A.company
					WHERE qty>0";
				$table=sqlquery($conn,$sql,"mysql");
				foreach($table as $temp){
					if(!isset($tabarray[strtoupper($temp['company'])]['number'])){
						$tabarray[strtoupper($temp['company'])]['number']=$temp['number'];
					}
					$tabarray[strtoupper($temp['company'])][intval($temp['itemdeptcode'])][intval($temp['itemcode'])]['qty']=$temp['qty'];
					$tabarray[strtoupper($temp['company'])][intval($temp['itemdeptcode'])][intval($temp['itemcode'])]['amt']=$temp['amt'];
				}
				$sql="SELECT C.dept,C.deptname FROM UserLogin 
				JOIN (
					SELECT company,deptname,dept FROM UserLogin WHERE dept<>'0' AND function='pos'
				) AS C ON UserLogin.company=C.company WHERE UserLogin.id='".$ID."'";
				$story=sqlquery($conn,$sql,'mysql');
				$condition="";
				foreach($story as $temp){
					$stoarray[$temp['dept']]=$temp['deptname'];
					if(strlen($condition)==0){
						$condition='"'.$temp['dept'].'"';
					}
					else{
						$condition=$condition.",".'"'.$temp['dept'].'"';
					}
				}
				$sql="SELECT DISTINCT itemcode,itemname,itemdeptcode,itemdeptname FROM (SELECT itemcode,itemname,itemdeptcode,itemdeptname FROM alldetails WHERE dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008' AND company IN (".$condition.") AND bizdate BETWEEN '".$startdate."' AND '".$enddate."') AS A ORDER BY itemdeptcode,itemcode";
				$menu=sqlquery($conn,$sql,"mysql");
				$menuarray['deptnumber']=intval($menu[sizeof($menu)-1]['itemdeptcode']);
				foreach($menu as $temp){
					if(isset($menuarray[intval($temp['itemdeptcode'])])){
						$menuarray[intval($temp['itemdeptcode'])]['totalnumber']=$menuarray[intval($temp['itemdeptcode'])]['totalnumber']+1;
						$menuarray[intval($temp['itemdeptcode'])]['itemcode'.$menuarray[intval($temp['itemdeptcode'])]['totalnumber']]['name']=$temp['itemname'];
						$menuarray[intval($temp['itemdeptcode'])]['itemcode'.$menuarray[intval($temp['itemdeptcode'])]['totalnumber']]['code']=intval($temp['itemcode']);
						$menuarray[intval($temp['itemdeptcode'])]['itemcode'.$menuarray[intval($temp['itemdeptcode'])]['totalnumber']]['number']=0;
					}
					else{
						$menuarray[intval($temp['itemdeptcode'])]['money']=0;
						$menuarray[intval($temp['itemdeptcode'])]['totalnumber']=1;
						$menuarray[intval($temp['itemdeptcode'])]['itemcode0']['name']=$temp['itemdeptname'];
						$menuarray[intval($temp['itemdeptcode'])]['itemcode'.$menuarray[intval($temp['itemdeptcode'])]['totalnumber']]['name']=$temp['itemname'];
						$menuarray[intval($temp['itemdeptcode'])]['itemcode'.$menuarray[intval($temp['itemdeptcode'])]['totalnumber']]['code']=intval($temp['itemcode']);
						$menuarray[intval($temp['itemdeptcode'])]['itemcode'.$menuarray[intval($temp['itemdeptcode'])]['totalnumber']]['number']=0;
					}
				}
				$sql="SELECT company,SUM(amt) AS discount FROM alldetails WHERE dtlmode='1' AND (dtltype='2' OR dtltype='3') AND bizdate BETWEEN '".$startdate."' AND '".$enddate."' GROUP BY company";
				$discount=sqlquery($conn,$sql,'mysql');
				foreach($discount as $temp){
					$cond[strtoupper($temp['company'])]=$temp['discount'];
				}
				if(sizeof($table)==0){
					echo "查無資料。";
				}
				else if($table[0]=="SQL語法錯誤"||$table[0]=="連線失敗"){
					if($dubug==1){
						echo $table[0]."(select)".$sql;
					}
					else{
						echo $table[0]."(select)";
					}
				}
				else{
					echo "<form method='post' action='toCSV.php' target='_blank'><input type='submit' value='匯出檔案'></form><div id='paper'></div>";
					echo "<div style='display:none'>";
					echo "<table id='source' border='1'>
							<tr>
								<td class='itemdept'>
									<table class='dept' style='width:120px;'>
										<tr>
											<td>門市</td>
										</tr>
										<tr>
											<td>商品</td>
										</tr>
									</table>
								</td>";
							array_push($csv,array('門市'));
							array_push($csv,array('商品'));
						for($dept=1;$dept<=$menuarray['deptnumber'];$dept++){
							if(isset($menuarray[$dept])){
								for($i=0;$i<=$menuarray[$dept]['totalnumber'];$i++){
									echo "<td ";
									if($i==0){
										echo "class='title'>";
									}
									else{
										echo "><input type='hidden' value='".$menuarray[$dept]['itemcode'.$i]['code']."'>";
									}
									echo $menuarray[$dept]['itemcode'.$i]['name']."</td>";
									array_push($csv,array($menuarray[$dept]['itemcode'.$i]['name']));
								}
							}
							else{
							}
						}
							echo "<td class='total'>折扣</td>";
							echo "<td>單點</td>";
							echo "<td>總營收</td>";
							echo "<td>帳單數量</td>";
							echo "<td>平均金額</td>";
							array_push($csv,array('折扣'));
							array_push($csv,array('單點'));
							array_push($csv,array('總營收'));
							array_push($csv,array('帳單數量'));
							array_push($csv,array('平均金額'));
						echo "</tr>";
					foreach($story as $s){
						$ss=3;
						$startdept=2;
						$single=0;//各門市單點金額
						$combine=0;//各門市套餐金額
						echo "<tr>";
						echo "<td class='dept'>".$s['deptname']."</td>";
						array_push($csv[0],$s['deptname']);
						for($dept=1;$dept<=$menuarray['deptnumber'];$dept++){
							if(isset($menuarray[$dept])){
								$HTMLstring="";
								$deptmoney=0;//分類項目總銷售額
								for($i=1;$i<=$menuarray[$dept]['totalnumber'];$i++){
									if(isset($tabarray[strtoupper($s['dept'])][$dept][$menuarray[$dept]['itemcode'.$i]['code']])){
										$HTMLstring=$HTMLstring."<td class='item'>".$tabarray[strtoupper($s['dept'])][$dept][$menuarray[$dept]['itemcode'.$i]['code']]['qty']."</td>";
										$deptmoney=$deptmoney+$tabarray[strtoupper($s['dept'])][$dept][$menuarray[$dept]['itemcode'.$i]['code']]['amt'];
										array_push($csv[$ss],$tabarray[strtoupper($s['dept'])][$dept][$menuarray[$dept]['itemcode'.$i]['code']]['qty']);
										if($menuarray[$dept]['itemcode0']['name']=="套餐"){
											$combine=$combine+$tabarray[strtoupper($s['dept'])][$dept][$menuarray[$dept]['itemcode'.$i]['code']]['amt'];
										}
										else{
											$single=$single+$tabarray[strtoupper($s['dept'])][$dept][$menuarray[$dept]['itemcode'.$i]['code']]['amt'];
										}
										$menuarray[$dept]['money']=$menuarray[$dept]['money']+$tabarray[strtoupper($s['dept'])][$dept][$menuarray[$dept]['itemcode'.$i]['code']]['amt'];//分類銷售總額
										$menuarray[$dept]['itemcode'.$i]['number']=$menuarray[$dept]['itemcode'.$i]['number']+$tabarray[strtoupper($s['dept'])][$dept][$menuarray[$dept]['itemcode'.$i]['code']]['qty'];//商品銷售總數
									}
									else{
										$HTMLstring=$HTMLstring."<td class='item'>0</td>";
										array_push($csv[$ss],"0");
									}
									$ss++;
								}
								$HTMLstring="<td class='item title'>".$deptmoney."</td>".$HTMLstring;
								array_push($csv[$startdept],$deptmoney);
								echo $HTMLstring;
								$startdept=$ss;
								$ss++;
							}
							else{
							}
						}
						if(isset($tabarray[strtoupper($s['dept'])]['number'])){
							if(isset($cond[strtoupper($s['dept'])])){
								echo "<td class='item total'>".$cond[strtoupper($s['dept'])]."</td>";
								array_push($csv[$ss-1],$cond[strtoupper($s['dept'])]);
								$totalcond=$totalcond+$cond[strtoupper($s['dept'])];
							}
							else{
								echo "<td class='item total'>0</td>";
								array_push($csv[$ss-1],"0");
							}
							echo "<td class='item'>".$single."</td>";
							array_push($csv[$ss],$single);
							$totalsingle=$totalsingle+$single;
							if(isset($cond[strtoupper($s['dept'])])){
								$perdatmoney=($single+$combine+$cond[strtoupper($s['dept'])]);
								echo "<td class='item'>".($single+$combine+$cond[strtoupper($s['dept'])])."</td>";
								array_push($csv[$ss+1],($single+$combine+$cond[strtoupper($s['dept'])]));
								$totalmoney=$totalmoney+($single+$combine+$cond[strtoupper($s['dept'])]);
							}
							else{
								$perdatmoney=($single+$combine);
								echo "<td class='item'>".($single+$combine)."</td>";
								array_push($csv[$ss+1],($single+$combine));
								$totalmoney=$totalmoney+($single+$combine);
							}
							echo "<td class='item'>".$tabarray[strtoupper($s['dept'])]['number']."</td>";
							$totalnumber=$totalnumber+$tabarray[strtoupper($s['dept'])]['number'];
							echo "<td class='item'>".round(($perdatmoney/$tabarray[strtoupper($s['dept'])]['number']),2)."</td>";
							array_push($csv[$ss+2],$tabarray[strtoupper($s['dept'])]['number']);
							array_push($csv[$ss+3],round(($perdatmoney/$tabarray[strtoupper($s['dept'])]['number']),2));
						}
						else{
							echo "<td class='item total'>0</td>";
							echo "<td class='item'>0</td>";
							echo "<td class='item'>0</td>";
							echo "<td class='item'>0</td>";
							echo "<td class='item'>0</td>";
							array_push($csv[$ss-1],"0");
							array_push($csv[$ss],"0");
							array_push($csv[$ss+1],"0");
							array_push($csv[$ss+2],"0");
							array_push($csv[$ss+3],"0");
						}
						echo "</tr>";
					}
						echo "<tr>";
							echo "<td class='dept'>小計</td>";
							array_push($csv[0],'小計');
						$ss=2;
						for($dept=1;$dept<=$menuarray['deptnumber'];$dept++){
							if(isset($menuarray[$dept])){
								for($i=0;$i<=$menuarray[$dept]['totalnumber'];$i++){
									if($i==0){
										echo "<td class='item title'>".$menuarray[$dept]['money']."</td>";
										array_push($csv[$ss],$menuarray[$dept]['money']);
									}
									else{
										echo "<td class='item'>".$menuarray[$dept]['itemcode'.$i]['number']."</td>";
										array_push($csv[$ss],$menuarray[$dept]['itemcode'.$i]['number']);
									}
									$ss++;
								}
							}
							else{
							}
						}
						echo "<td class='item total'>".$totalcond."</td>";
						array_push($csv[$ss],$totalcond);
						echo "<td class='item'>".$totalsingle."</td>";
						array_push($csv[$ss+1],$totalsingle);
						echo "<td class='item'>".$totalmoney."</td>";
						array_push($csv[$ss+2],$totalmoney);
						echo "<td class='item'></td>";
						array_push($csv[$ss+3],"");
						echo "<td class='item'></td>";
						array_push($csv[$ss+4],"");
						echo "</tr>";
						echo "<tr>";
							echo "<td class='dept'>合計</td>";
							array_push($csv[0],'合計');
						$ss=2;
						$deptnumber=0;//各分類總銷售量
						$deptcount=1;
						for($dept=1;$dept<=$menuarray['deptnumber'];$dept++){
							if(isset($menuarray[$dept])){
								for($i=0;$i<=$menuarray[$dept]['totalnumber'];$i++){
									if($i==0){
										echo "<td class='item title'><input type='hidden' id='dept".$deptcount."' value='".$menuarray[$dept]['totalnumber']."'>".$menuarray[$dept]['money']."</td>";
										array_push($csv[$ss],$menuarray[$dept]['money']);
										$ss++;
										$deptcount++;
									}
									else{
										$deptnumber=$deptnumber+$menuarray[$dept]['itemcode'.$i]['number'];
									}
								}
								echo "<td class='item'>".$deptnumber."</td>";
								array_push($csv[$ss],$deptnumber);
								$ss++;
								for($cells=1;$cells<$menuarray[$dept]['totalnumber'];$cells++){
									echo "<td></td>";
									array_push($csv[$ss],"");
									$ss++;
								}
							}
							else{
							}
						}
						echo "<td class='item total'>".$totalcond."</td>";
						array_push($csv[$ss],$totalcond);
						echo "<td class='item'>".$totalsingle."</td>";
						array_push($csv[$ss+1],$totalsingle);
						echo "<td class='item'>".$totalmoney."</td>";
						array_push($csv[$ss+2],$totalmoney);
						echo "<td class='item'>".$totalnumber."</td>";
						array_push($csv[$ss+3],$totalnumber);
						echo "<td class='item'>".round(($totalmoney/$totalnumber),2)."</td>";
						array_push($csv[$ss+4],round(($totalmoney/$totalnumber),2));
						echo "</tr>";
					echo "</table>
						</div>
						<input type='hidden' id='taRow' value='".$deptcount."'>
						<script>transpose('source','paper','門市商品銷售統計');deleteCells('dCells','taRow');</script>";
				}
				echo "</div>";
				if(isset($_SESSION['array'])){
					unset($_SESSION['array']);
					$_SESSION['array']=$csv;
				}
				else{
					$_SESSION['array']=$csv;
				}
			}
			sqlclose($conn,"mysql");
		}
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='newnumberofday'){
		if(strlen($enddate)<10){
			$enddate=$enddate."-".date("t",mktime('0','0','0','1',substr($enddate,5,2),substr($enddate,0,4)));
		}
		echo "<script>
				function view(){
					document.getElementById('hidden').innerHTML=document.getElementsByName('DB')[0].value;
				}
			</script>
			<form method='post' action='' id='search'>
				<input type='hidden' name='conttype' value='newnumberofday'>
				<table>
					<!-- <caption>設定時間區間</caption> -->";
				if($_SESSION['ID']=='admin'){
					echo "<tr>
							<td>資料庫</td>
							<td><select name='DB'>";
					$Path="../../DB";
					foreach(glob($Path."/*") as $entry) {
					  if (in_array($entry, array(".", "..")) === false) {
						 $temp=preg_split("(".$Path."/)",$entry);
						 echo "<option value='".$temp[1]."'>".$temp[1]."</option>";
					  }
					}
					echo "</select></td>
						</tr>";
				}
				else if($usergroup=='boss'){
					$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
					$sql="SELECT usedb,deptname FROM UserLogin WHERE company=(SELECT company FROM UserLogin WHERE id='".$_SESSION['ID']."') AND usergroup IS NULL AND function LIKE '%pos%'";
					$temp=sqlquery($conn,$sql,"mysql");
					sqlclose($conn,"mysql");
					echo "<tr>
							<td>資料庫</td>
							<td><select name='DB' onchange='view()'>";
					foreach($temp as $value){
						echo "<option value='".$value['usedb']."' ";
						if(!empty($_SESSION['DB'])&&$_SESSION['DB']==$value['usedb']){
							echo "selected";
						}
						echo ">".$value['deptname']."</optioin>";
					}
						echo "</select></td>
						</tr>";
				}
				else{
				}
				echo "<tr>
						<td>時間區間</td>
						<td>
							<input type='date' name='startdate' id='startdate'";if(strlen($startdate)>0)echo " value=".$startdate;echo ">～<input type='date' name='enddate' id='enddate'";if(strlen($enddate)>0)echo " value=".$enddate;echo ">
						</td>
					</tr>
					<tr>
						<td colspan='2'><input type='button' value='送出' onclick='basiccheck(this.form)'></td>
					</tr>
				</table>
			</form>";
		if(empty($startdate)&&empty($enddate)){
			echo "請先設定想瀏覽的時間。";
		}
		else{
			$list=array();//點餐之餐點明細
			$menu=array();//產品編號與產品名稱;A[產品編號][itemname]=產品名稱,A[產品編號][total]=產品在所選時段內的總銷售額
			$zcounter=array();//每日的班別;a[bizdate][total]=n,a[bizdate][counter1]~a[bizdate][counterN]=counter;
			$paper=array();//點餐之餐點明細轉成新的陣列；$paper[班別][產品編號]=該班別此產品的總銷售金額,$paper[班別][ztotal]=該班別的總銷售金額(所有產品),$paper[班別][discount]=該班別的總折扣(所有產品),$paper[班別][itemdeptcode]=產品分類編號,$paper[班別][qty]=該班別此產品的總銷售數量
			$csv=array();//匯出檔案內容
			//$conn=sqlconnect("../DB/".$DB,"SALES_".$year.$month.".DB","","","","sqlite");
			$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
			if(!$conn){
				echo "資料庫發生錯誤或尚未上傳資料。";
			}
			else{
				//因為sqlite沒有支援if的語法，只好先查詢折扣的數量，分為有折扣與沒有折扣兩種情況
				//$sql="SELECT BIZDATE,ZCOUNTER,SUM(AMT) AS DISCOUNT FROM CST012 WHERE DTLMODE='1' AND (DTLTYPE='2' OR DTLTYPE='3') GROUP BY ZCOUNTER";
				$sql="SELECT bizdate,zcounter,SUM(amt) AS discount FROM alldetails WHERE company='".$DB."' AND dtlmode='1' AND (dtltype='2' OR dtltype='3') AND bizdate BETWEEN '".$startdate."' AND '".$enddate."' GROUP BY zcounter";
				//$condition=sqlquery($conn,$sql,'sqlite');
				$condition=sqlquery($conn,$sql,'mysql');
				$cond=array();
				foreach($condition as $value){
					//$cond[$value['BIZDATE']][$value['ZCOUNTER']]=$value['DISCOUNT'];
					$cond[$value['bizdate']][$value['zcounter']]=$value['discount'];
				}
				//if(sizeof($condition)>0){//有折扣//問題sql
					//$sql="SELECT DISTINCT B.BIZDATE,B.ITEMCODE,B.ITEMGRPCODE,B.ITEMDEPTCODE,B.QTY,B.UNITPRICE,B.AMT,B.ZCOUNTER,B.CREATEDATETIME,C.ZTOTAL,D.DISCOUNT,E.NUMBER FROM (SELECT DISTINCT BIZDATE,ITEMCODE, ITEMNAME,FUNCKEYCODE,FUNCKEYNAME,FUNCTIONCODE,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SUM(QTY) AS QTY,UNITPRICE,SUM(AMT) AS AMT,ZCOUNTER,CREATEDATETIME FROM (SELECT * FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008' ORDER BY ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER DESC) AS A GROUP BY BIZDATE,ITEMCODE,ZCOUNTER ORDER BY ZCOUNTER) AS B JOIN (SELECT DISTINCT BIZDATE,ZCOUNTER,SUM(AMT) AS ZTOTAL FROM (SELECT DISTINCT BIZDATE,ITEMCODE, ITEMNAME,FUNCKEYCODE,FUNCKEYNAME,FUNCTIONCODE,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SUM(QTY) AS QTY,UNITPRICE,SUM(AMT) AS AMT,ZCOUNTER,CREATEDATETIME FROM (SELECT * FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008' ORDER BY ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER DESC) AS A GROUP BY BIZDATE,ITEMCODE,ZCOUNTER ORDER BY ZCOUNTER) GROUP BY BIZDATE,ZCOUNTER) AS C ON C.ZCOUNTER=B.ZCOUNTER AND C.BIZDATE=B.BIZDATE JOIN (SELECT BIZDATE,ZCOUNTER,SUM(AMT) AS DISCOUNT FROM CST012 WHERE DTLMODE='1' AND (DTLTYPE='2' OR DTLTYPE='3') GROUP BY BIZDATE,ZCOUNTER) AS D ON D.ZCOUNTER=B.ZCOUNTER AND D.BIZDATE=B.BIZDATE JOIN (SELECT DISTINCT BIZDATE,ZCOUNTER,SUM(NUMBER) AS NUMBER FROM (SELECT BIZDATE,CONSECNUMBER,SALESTTLAMT,ZCOUNTER,CASE WHEN salesttlamt>0 THEN 1 WHEN salesttlamt=0 THEN 0 ELSE -1 END AS number FROM CST011 WHERE CAST(INVOICENUMBER AS INT)>0) AS A GROUP BY BIZDATE,ZCOUNTER) AS E ON E.ZCOUNTER=B.ZCOUNTER AND E.BIZDATE=B.BIZDATE WHERE QTY>0";//只抓當天銷售明細需在最後面加上過濾條件AND ZCOUNTER=(SELECT ZCOUNTER FROM CST012 WHERE ITEMORDERDATE='20161018' ORDER BY ZCOUNTER DESC LIMIT 1)
				//}
				//else{//沒折扣
					//$sql="SELECT DISTINCT B.BIZDATE,B.ITEMCODE,B.ITEMGRPCODE,B.ITEMDEPTCODE,B.QTY,B.UNITPRICE,B.AMT,B.ZCOUNTER,B.CREATEDATETIME,C.ZTOTAL,E.NUMBER FROM (SELECT DISTINCT BIZDATE,ITEMCODE, ITEMNAME,FUNCKEYCODE,FUNCKEYNAME,FUNCTIONCODE,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SUM(QTY) AS QTY,UNITPRICE,SUM(AMT) AS AMT,ZCOUNTER,CREATEDATETIME FROM (SELECT * FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008' ORDER BY ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER DESC) AS A GROUP BY BIZDATE,ITEMCODE,ZCOUNTER ORDER BY ZCOUNTER) AS B JOIN (SELECT DISTINCT BIZDATE,ZCOUNTER,SUM(AMT) AS ZTOTAL FROM (SELECT DISTINCT BIZDATE,ITEMCODE, ITEMNAME,FUNCKEYCODE,FUNCKEYNAME,FUNCTIONCODE,ITEMGRPCODE,ITEMGRPNAME,ITEMDEPTCODE,ITEMDEPTNAME,SUM(QTY) AS QTY,UNITPRICE,SUM(AMT) AS AMT,ZCOUNTER,CREATEDATETIME FROM (SELECT * FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008' ORDER BY ZCOUNTER ASC,CONSECNUMBER ASC,LINENUMBER DESC) AS A GROUP BY BIZDATE,ITEMCODE,ZCOUNTER ORDER BY ZCOUNTER) GROUP BY BIZDATE,ZCOUNTER) AS C ON C.ZCOUNTER=B.ZCOUNTER AND C.BIZDATE=B.BIZDATE JOIN (SELECT DISTINCT BIZDATE,ZCOUNTER,SUM(NUMBER) AS NUMBER FROM (SELECT BIZDATE,CONSECNUMBER,SALESTTLAMT,ZCOUNTER,CASE WHEN salesttlamt>0 THEN 1 WHEN salesttlamt=0 THEN 0 ELSE -1 END AS number FROM CST011 WHERE CAST(INVOICENUMBER AS INT)>0) AS A GROUP BY BIZDATE,ZCOUNTER) AS E ON E.ZCOUNTER=B.ZCOUNTER AND E.BIZDATE=B.BIZDATE WHERE QTY>0";//只抓當天銷售明細需在最後面加上過濾條件AND ZCOUNTER=(SELECT ZCOUNTER FROM CST012 WHERE ITEMORDERDATE='20161018' ORDER BY ZCOUNTER DESC LIMIT 1)
					$sql="SELECT DISTINCT B.company,B.bizdate,B.itemcode,B.itemgrpcode,B.itemdeptcode,B.qty,B.unitprice,B.amt,B.zcounter,B.createdatetime,C.ztotal,E.number 
					FROM (
						SELECT DISTINCT company,bizdate,itemcode,itemname,itemgrpcode,itemgrpname,itemdeptcode,itemdeptname,SUM(qty) AS qty,unitprice,SUM(amt) AS amt,zcounter,createdatetime 
						FROM (
							SELECT * FROM alldetails WHERE company='".$DB."' AND dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008' ORDER BY zcounter ASC,consecnumber ASC,linenumber DESC
						) AS A GROUP BY bizdate,itemcode,zcounter ORDER BY zcounter
					) AS B 
					JOIN (
						SELECT DISTINCT bizdate,zcounter,SUM(amt) AS ztotal 
						FROM (
							SELECT DISTINCT bizdate,itemcode,itemname,itemgrpcode,itemgrpname,itemdeptcode,itemdeptname,SUM(qty) AS qty,unitprice,SUM(amt) AS amt,zcounter,createdatetime 
							FROM (
								SELECT * FROM alldetails WHERE company='".$DB."' AND dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008' ORDER BY zcounter ASC,consecnumber ASC,linenumber DESC
							) AS A GROUP BY bizdate,itemcode,zcounter ORDER BY zcounter
						) AS table1 GROUP BY bizdate,zcounter
					) AS C ON C.zcounter=B.zcounter AND C.bizdate=B.bizdate 
					JOIN (
						SELECT bizdate,zcounter,SUM(number) AS number 
						FROM (
							SELECT DISTINCT bizdate,company,consecnumber,salesttlamt,zcounter,CASE WHEN salesttlamt>0 THEN 1 WHEN salesttlamt=0 THEN 0 ELSE -1 END AS number FROM alldetails WHERE company='".$DB."' AND CAST(invoicenumber AS INT)>0
						) AS A GROUP BY bizdate,zcounter
					) AS E ON E.zcounter=B.zcounter AND E.bizdate=B.bizdate 
					WHERE B.company='".$DB."' AND qty>0 AND B.bizdate BETWEEN '".$startdate."' AND '".$enddate."'";
				//}
				//$list=sqlquery($conn,$sql,'sqlite');
				$list=sqlquery($conn,$sql,'mysql');
				$sql='SELECT finaltime FROM userlogin WHERE dept="'.strtoupper($DB).'" AND function LIKE "%pos%"';
				$finaltime=sqlquery($conn,$sql,'mysql');
				if(sizeof($list)==0){
					if(sizeof($finaltime)==0){
						//echo "查無資料。<br>";
					}
					else{
						echo "查無資料。<br><span>最後更新時間：".$finaltime[0]['finaltime']."</span>";
					}
				}
				else if($list[0]=="SQL語法錯誤"||$list[0]=="連線失敗"){
					if($dubug==1){
						echo $list[0]."(select)".$sql;
					}
					else{
						echo $list[0]."(select)";
					}
				}
				else{
					//$a=$list[0]['CREATEDATETIME'];
					$a=$list[0]['createdatetime'];
					//$maxDay=cal_days_in_month(CAL_GREGORIAN,substr($a,4,2),substr($a,0,4));//自動判斷某年某月的天數
					$maxDay=date("t");
					//$sql="SELECT DISTINCT ITEMCODE,ITEMNAME,SUM(AMT) AS TOTAL FROM (SELECT * FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008') GROUP BY ITEMCODE";
					//$sql="SELECT DISTINCT ITEMDEPTCODE,ITEMDEPTNAME,ITEMCODE,ITEMNAME,SUM(QTY) AS QTY,SUM(AMT) AS AMT FROM (SELECT * FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008') GROUP BY ITEMDEPTCODE,ITEMCODE ORDER BY ITEMDEPTCODE ASC,ITEMCODE ASC";
					$sql="SELECT DISTINCT A.itemdeptcode,A.itemdeptname,A.itemcode,A.itemname,SUM(A.qty) AS qty,SUM(A.amt) AS amt FROM (SELECT * FROM alldetails WHERE company='".$DB."' AND dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008') AS A WHERE bizdate BETWEEN '".$startdate."' AND '".$enddate."' GROUP BY itemdeptcode,itemcode ORDER BY itemdeptcode ASC,itemcode ASC";
					//$tempmenu=sqlquery($conn,$sql,'sqlite');//暫存菜單；產品編號與產品名稱
					$tempmenu=sqlquery($conn,$sql,'mysql');
					//$sql="SELECT BIZDATE,ZCOUNTER FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008' GROUP BY BIZDATE,ZCOUNTER";//之前使用
					$sql="SELECT bizdate,zcounter FROM alldetails WHERE company='".$DB."' AND dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008' AND bizdate BETWEEN '".$startdate."' AND '".$enddate."' GROUP BY bizdate,zcounter";
					//$tempzcounter=sqlquery($conn,$sql,'sqlite');//班別；營業日期與當天班別
					$tempzcounter=sqlquery($conn,$sql,'mysql');
					$mtotal=0;//單月總銷售額(不含折扣)
					$mdiscount=0;//單月總折扣
					$mnumber=0;//單月帳單總數
					$msingle=0;//單月單點
					$mcombine=0;//單月套餐
					$ptotal="";//單月總銷售金額折線圖變數數值(字串表示)
					//$mitemdept=array();//單月產品分類統計；$mitemdept[產品分類編號]=此分類的銷售數量
					//$menu[0]['value']=intval($tempmenu[sizeof($tempmenu)-1]['ITEMDEPTCODE']);
					$menu[0]['value']=intval($tempmenu[sizeof($tempmenu)-1]['itemdeptcode']);
					foreach($tempmenu as $b){//將暫存菜單轉成新的陣列；$menu[產品類別編號][total]=該類別中的商品數量,$menu[產品類別編號][name]=類別名稱,$menu[產品類別編號][itemcode1~N]=類別中的商品編號,$menu[產品類別編號][itemname1~N]=類別中的商品名稱,$menu[產品類別編號][itemqty1~N]=類別中的商品銷售數量,$menu[產品類別編號][itemamt1~N]=類別中的商品銷售金額
						/*if(isset($menu[intval($b['ITEMDEPTCODE'])]['total'])){
							$menu[intval($b['ITEMDEPTCODE'])]['total']=$menu[intval($b['ITEMDEPTCODE'])]['total']+1;
							$menu[intval($b['ITEMDEPTCODE'])]['itemcode'.$menu[intval($b['ITEMDEPTCODE'])]['total']]=intval($b['ITEMCODE']);
							$menu[intval($b['ITEMDEPTCODE'])]['itemname'.$menu[intval($b['ITEMDEPTCODE'])]['total']]=$b['ITEMNAME'];
							$menu[intval($b['ITEMDEPTCODE'])]['itemqty'.$menu[intval($b['ITEMDEPTCODE'])]['total']]=$b['QTY'];
							$menu[intval($b['ITEMDEPTCODE'])]['itemamt'.$menu[intval($b['ITEMDEPTCODE'])]['total']]=$b['AMT'];
						}
						else{
							$menu[intval($b['ITEMDEPTCODE'])]['total']=1;
							$menu[intval($b['ITEMDEPTCODE'])]['name']=$b['ITEMDEPTNAME'];
							$menu[intval($b['ITEMDEPTCODE'])]['itemcode1']=intval($b['ITEMCODE']);
							$menu[intval($b['ITEMDEPTCODE'])]['itemname1']=$b['ITEMNAME'];
							$menu[intval($b['ITEMDEPTCODE'])]['itemqty1']=$b['QTY'];
							$menu[intval($b['ITEMDEPTCODE'])]['itemamt1']=$b['AMT'];
						}*/
						if(isset($menu[intval($b['itemdeptcode'])]['total'])){
							$menu[intval($b['itemdeptcode'])]['total']=$menu[intval($b['itemdeptcode'])]['total']+1;
							$menu[intval($b['itemdeptcode'])]['itemcode'.$menu[intval($b['itemdeptcode'])]['total']]=intval($b['itemcode']);
							$menu[intval($b['itemdeptcode'])]['itemname'.$menu[intval($b['itemdeptcode'])]['total']]=$b['itemname'];
							$menu[intval($b['itemdeptcode'])]['itemqty'.$menu[intval($b['itemdeptcode'])]['total']]=$b['qty'];
							$menu[intval($b['itemdeptcode'])]['itemamt'.$menu[intval($b['itemdeptcode'])]['total']]=$b['amt'];
						}
						else{
							$menu[intval($b['itemdeptcode'])]['total']=1;
							$menu[intval($b['itemdeptcode'])]['name']=$b['itemdeptname'];
							$menu[intval($b['itemdeptcode'])]['itemcode1']=intval($b['itemcode']);
							$menu[intval($b['itemdeptcode'])]['itemname1']=$b['itemname'];
							$menu[intval($b['itemdeptcode'])]['itemqty1']=$b['qty'];
							$menu[intval($b['itemdeptcode'])]['itemamt1']=$b['amt'];
						}
					}
					$nowcounter=0;
					foreach($tempzcounter as $b){//將zcounter陣列改成非方正陣列;a[bizdate][total]=n,a[bizdate][counter1]~a[bizdate][counterN]=counter;這樣能夠同時解決一天1個班別以上以及跨夜的業績歸屬前一天的兩個問題
						if(sizeof($zcounter)==0){
							/*$zcounter[intval(substr($b['BIZDATE'],6,2))]['total']=1;
							$zcounter[intval(substr($b['BIZDATE'],6,2))]['counter1']=intval($b['ZCOUNTER']);
							$nowcounter=intval($b['ZCOUNTER']);*/
							$zcounter[$b['bizdate']]['total']=1;
							$zcounter[$b['bizdate']]['counter1']=intval($b['zcounter']);
							$nowcounter=intval($b['zcounter']);
						}
						else{
							/*if(isset($zcounter[intval(substr($b['BIZDATE'],6,2))]['total'])){
								$zcounter[intval(substr($b['BIZDATE'],6,2))]['total']=$zcounter[intval(substr($b['BIZDATE'],6,2))]['total']+1;
								$zcounter[intval(substr($b['BIZDATE'],6,2))]['counter'.$zcounter[intval(substr($b['BIZDATE'],6,2))]['total']]=intval($b['ZCOUNTER']);
								$nowcounter=intval($b['ZCOUNTER']);
							}
							else{
								if(intval($b['ZCOUNTER'])==$nowcounter){
									//基本上ZCOUNTER隔天的值至少是大於等於，如果是等於則表示跨夜班，業績歸屬前一天
									//由於會發生人為疏忽忘記交班的情況，需要由我們幫忙重設交班計數器，如此一來，大於等於的狀況就不再為正常判斷標準
									//因此小於的情況，與大於的情況類似
								}
								else if(intval($b['ZCOUNTER'])<$nowcounter){
									$zcounter[intval(substr($b['BIZDATE'],6,2))]['total']=1;
									$zcounter[intval(substr($b['BIZDATE'],6,2))]['counter1']=intval($b['ZCOUNTER']);
									$nowcounter=intval($b['ZCOUNTER']);
								}
								else{//新的ZCOUNTER如果大於，則表示為新的一天
									$zcounter[intval(substr($b['BIZDATE'],6,2))]['total']=1;
									$zcounter[intval(substr($b['BIZDATE'],6,2))]['counter1']=intval($b['ZCOUNTER']);
									$nowcounter=intval($b['ZCOUNTER']);
								}
							}*/
							if(isset($zcounter[$b['bizdate']]['total'])){
								$zcounter[$b['bizdate']]['total']=$zcounter[$b['bizdate']]['total']+1;
								$zcounter[$b['bizdate']]['counter'.$zcounter[$b['bizdate']]['total']]=intval($b['zcounter']);
								$nowcounter=intval($b['zcounter']);
							}
							else{
								if(intval($b['zcounter'])==$nowcounter){
									//基本上ZCOUNTER隔天的值至少是大於等於，如果是等於則表示跨夜班，業績歸屬前一天
									//由於會發生人為疏忽忘記交班的情況，需要由我們幫忙重設交班計數器，如此一來，大於等於的狀況就不再為正常判斷標準
									//因此小於的情況，與大於的情況類似
								}
								else if(intval($b['zcounter'])<$nowcounter){
									$zcounter[$b['bizdate']]['total']=1;
									$zcounter[$b['bizdate']]['counter1']=intval($b['zcounter']);
									$nowcounter=intval($b['zcounter']);
								}
								else{//新的ZCOUNTER如果大於，則表示為新的一天
									$zcounter[$b['bizdate']]['total']=1;
									$zcounter[$b['bizdate']]['counter1']=intval($b['zcounter']);
									$nowcounter=intval($b['zcounter']);
								}
							}
						}
					}
					foreach($list as $c){//點餐之餐點明細轉成新的陣列；$paper[班別][產品類別編號][產品編號]=該班別此產品的總銷售金額,$paper[班別][footer][ztotal]=該班別的總銷售金額(所有產品),$paper[班別][footer][discount]=該班別的總折扣(所有產品),$paper[班別][footer][itemdeptcode]=產品分類編號,$paper[班別][footer][qty]=該班別此產品的總銷售數量
						//$paper[intval($c['ZCOUNTER'])][intval($c['ITEMCODE'])]=$c['AMT'];
						/*$paper[intval(substr($c['BIZDATE'],6,2))][intval($c['ZCOUNTER'])][intval($c['ITEMDEPTCODE'])][intval($c['ITEMCODE'])."qty"]=$c['QTY'];
						$paper[intval(substr($c['BIZDATE'],6,2))][intval($c['ZCOUNTER'])][intval($c['ITEMDEPTCODE'])][intval($c['ITEMCODE'])."amt"]=$c['AMT'];
						$paper[intval(substr($c['BIZDATE'],6,2))][intval($c['ZCOUNTER'])]['footer']['ztotal']=$c['ZTOTAL'];
						if(sizeof($condition)>0&&isset($cond[$c['BIZDATE']][$c['ZCOUNTER']])){
							$paper[intval(substr($c['BIZDATE'],6,2))][intval($c['ZCOUNTER'])]['footer']['discount']=$cond[$c['BIZDATE']][$c['ZCOUNTER']];
						}
						else{
							$paper[intval(substr($c['BIZDATE'],6,2))][intval($c['ZCOUNTER'])]['footer']['discount']=0;
						}
						$paper[intval(substr($c['BIZDATE'],6,2))][intval($c['ZCOUNTER'])]['footer']['itemdeptcode']=intval($c['ITEMDEPTCODE']);
						$paper[intval(substr($c['BIZDATE'],6,2))][intval($c['ZCOUNTER'])]['footer']['number']=$c['NUMBER'];*/
						$paper[$c['bizdate']][intval($c['zcounter'])][intval($c['itemdeptcode'])][intval($c['itemcode'])."qty"]=$c['qty'];
						$paper[$c['bizdate']][intval($c['zcounter'])][intval($c['itemdeptcode'])][intval($c['itemcode'])."amt"]=$c['amt'];
						$paper[$c['bizdate']][intval($c['zcounter'])]['footer']['ztotal']=$c['ztotal'];
						if(isset($cond[$c['bizdate']][$c['zcounter']])){
							$paper[$c['bizdate']][intval($c['zcounter'])]['footer']['discount']=$cond[$c['bizdate']][$c['zcounter']];
						}
						else{
							$paper[$c['bizdate']][intval($c['zcounter'])]['footer']['discount']=0;
						}
						$paper[$c['bizdate']][intval($c['zcounter'])]['footer']['itemdeptcode']=intval($c['itemdeptcode']);
						$paper[$c['bizdate']][intval($c['zcounter'])]['footer']['number']=$c['number'];
						//$paper[intval($c['ZCOUNTER'])][intval($c['ITEMDEPTCODE'])]['qty']=$c['QTY'];
						/*if(isset($mitemdept[$c['ITEMDEPTCODE']])){
							$mitemdept[$c['ITEMDEPTCODE']]=$mitemdept[$c['ITEMDEPTCODE']]+$c['QTY'];
						}
						else{
							$mitemdept[$c['ITEMDEPTCODE']]=$c['QTY'];
						}*/
					}
					echo "<div id='title'><form method='post' action='toCSV.php' target='_blank'><input type='submit' value='匯出檔案'></form><div id='paper'>
						</div></div>";
					echo "<div style='display:none'>";
					echo "<table id='source' border='1'>";
					echo "<tr>";
					$index=1;
					$bgcolor1='#ffffff';
					$bgcolor2='#ffffff';
					for($i=0;$i<=$menu[0]['value'];$i++){
						if($i==0){
							array_push($csv,array("營業日期"));
							array_push($csv,array("商品名稱"));
							echo "<td class='itemdept'><table class='dept' style='width:120px;'><tr><td>營業日期</td></tr><tr><td>商品名稱</td></tr></table></td>";
						}
						else{
							if(isset($menu[$i]['total'])){
								echo "<td class='title'";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo ">".$menu[$i]['name']."</td>";
								array_push($csv,array($menu[$i]['name']));
								for($code=1;$code<=$menu[$i]['total'];$code++){
									echo "<td";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo "><input type='hidden' value='".$menu[$i]['itemcode'.$code]."'>".$menu[$i]['itemname'.$code]."</td>";
									array_push($csv,array($menu[$i]['itemname'.$code]));
								}
								$index++;
							}
							else{
							}
						}
					}
					//echo "<td>小計</td>";
					echo "<td class='total'>折扣</td>";
					//echo "<td>總計</td>";
					echo "<td><strong>單點</strong></td>";
					echo "<td><strong>每日營收</strong></td>";
					echo "<td><strong>帳單數量</strong></td>";
					echo "<td><strong>平均金額</strong></td>";
					array_push($csv,array("折扣"));
					array_push($csv,array("單點"));
					array_push($csv,array("每日營收"));
					array_push($csv,array("帳單數量"));
					array_push($csv,array("平均金額"));
					echo "</tr>";
					/*$date=intval(substr($startdate,strlen($startdate)-2,2));
					for(;$date<=$maxDay;$date++){*/
					for($i=0;date( "Y-m-d", strtotime( $startdate." +".$i." day" ))!=date( "Y-m-d", strtotime( $enddate." +1 day" ) );$i++){
						$date=date( "Y-m-d", strtotime( $startdate." +".$i." day" ));
						$tempdate=preg_replace("/-/","",$date);
						$index=1;
						$temparray=array();//暫存陣列 $temparray[zcounter][total]=此班別的總營收；$temparray[zcounter][number]=此班別的帳單數
						$ztotal=0;//單日總銷售額(不含折扣)
						$zdiscount=0;//單日總折扣
						$znumber=0;//帳單數量
						$money=0;//每日營收
						$single=0;//單點數量
						$combine=0;//套餐數量
						$csvstart=2;//匯出檔案陣列之開始row
						$aaa=1;
						if(isset($zcounter[$date]['total'])){
							echo "<tr>";
							echo "<td class='date'>".substr($tempdate,2)."</td>";
							array_push($csv[0],$date);
							for($deptcode=1;$deptcode<=$menu[0]['value'];$deptcode++){
								$HTMLstring="";
								if(isset($menu[$deptcode]['total'])){
									$dmoney=0;//類別加總金額
									for($code=1;$code<=$menu[$deptcode]['total'];$code++){
										$zmoney=0;//單一商品加總數量
										$tempdmoney=0;//單一商品加總金額
										if($zcounter[$date]['total']==1){
											if(isset($paper[$date][$zcounter[$date]['counter1']][$deptcode][$menu[$deptcode]['itemcode'.$code]."qty"])){
												$zmoney=$paper[$date][$zcounter[$date]['counter1']][$deptcode][$menu[$deptcode]['itemcode'.$code]."qty"];
												if(in_array($zcounter[$date]['counter1'],$temparray,true)){
												}
												else{
													$temparray[$zcounter[$date]['counter1']]['total']=$paper[$date][$zcounter[$date]['counter1']]['footer']['ztotal'];
													$temparray[$zcounter[$date]['counter1']]['number']=$paper[$date][$zcounter[$date]['counter1']]['footer']['number'];
												}
												//$ztotal=$paper[$date][$zcounter[$date]['counter1']]['footer']['ztotal'];
												//$zdiscount=$paper[$date][$zcounter[$date]['counter1']]['footer']['discount'];
												$tempdmoney=$paper[$date][$zcounter[$date]['counter1']][$deptcode][$menu[$deptcode]['itemcode'.$code]."amt"];
												//$znumber=$paper[$date][$zcounter[$date]['counter1']]['footer']['number'];
											}
											else{
											}
										}
										else{
											for($z=1;$z<=$zcounter[$date]['total'];$z++){
												if(isset($paper[$date][$zcounter[$date]['counter'.$z]][$deptcode][$menu[$deptcode]['itemcode'.$code]."qty"])){
													$zmoney=$zmoney+$paper[$date][$zcounter[$date]['counter'.$z]][$deptcode][$menu[$deptcode]['itemcode'.$code]."qty"];
													if(in_array($zcounter[$date]['counter'.$z],$temparray,true)){
													}
													else{
														$temparray[$zcounter[$date]['counter'.$z]]['total']=$paper[$date][$zcounter[$date]['counter'.$z]]['footer']['ztotal'];
														$temparray[$zcounter[$date]['counter'.$z]]['number']=$paper[$date][$zcounter[$date]['counter'.$z]]['footer']['number'];
													}
													//$ztotal=$ztotal+$paper[$date][$zcounter[$date]['counter'.$z]]['footer']['ztotal'];
													//$zdiscount=$zdiscount+$paper[$date][$zcounter[$date]['counter'.$z]]['footer']['discount'];
													$tempdmoney=$tempdmoney+$paper[$date][$zcounter[$date]['counter'.$z]][$deptcode][$menu[$deptcode]['itemcode'.$code]."amt"];
													//$znumber=$znumber+$paper[$date][$zcounter[$date]['counter'.$z]]['footer']['number'];
												}
												else{
												}
											}
										}
										if(sizeof($temparray)>0){
											$ztotal=0;
											$znumber=0;
										}
										else{
										}
										foreach($temparray as $ZTOTAL){
											$ztotal=$ztotal+$ZTOTAL['total'];
											$znumber=$znumber+$ZTOTAL['number'];
										}
										//echo "<script>alert('".$zmoney."');</script>";
										$dmoney=$dmoney+$tempdmoney;
										array_push($csv[$csvstart+$code],$zmoney);
										$HTMLstring=$HTMLstring."<td class='item'";if($index%2)$HTMLstring=$HTMLstring." style='background-color:".$bgcolor1."'";else $HTMLstring=$HTMLstring." style='background-color:".$bgcolor2."'";$HTMLstring=$HTMLstring.">".$zmoney."</td>";
									}
								}
								else{
								}
								if(strlen($HTMLstring)>0){
									array_push($csv[$csvstart],$dmoney);
									$tempstring="<td class='item title'";if($index%2)$tempstring=$tempstring." style='background-color:".$bgcolor1."'";else $tempstring=$tempstring." style='background-color:".$bgcolor2."'";$tempstring=$tempstring.">".$dmoney."</td>";
									$HTMLstring=$tempstring.$HTMLstring;
									$index++;
									$csvstart=$csvstart+$menu[$deptcode]['total']+1;
									echo $HTMLstring;
									if($menu[$deptcode]['name']=="套餐"){
										$combine=$combine+$dmoney;
									}
									else{
										$single=$single+$dmoney;
									}
								}
								else{
								}
							}
							for($z=1;$z<=$zcounter[$date]['total'];$z++){//因為在原本位置計算時，如果有交班，會重複計算
								$zdiscount=$zdiscount+$paper[$date][$zcounter[$date]['counter'.$z]]['footer']['discount'];
							}
							//echo "<td class='item'><font color='#ff0000'>".$ztotal."</font></td>";
							echo "<td class='item total'><font color='#ff0000'>".$zdiscount."</font></td>";
							//echo "<td class='item'><font color='#0033ff'>".($ztotal+$zdiscount)."</font></td>";
							echo "<td class='item'><strong>".$single."</strong></td>";
							echo "<td class='item'><strong>".($single+$combine+$zdiscount)."</strong></td>";
							echo "<td class='item'><strong>".$znumber."</strong></td>";
							echo "<td class='item'><strong>".round((($ztotal+$zdiscount)/$znumber),2)."</strong></td>";
							array_push($csv[$csvstart],$zdiscount);
							array_push($csv[$csvstart+1],$single);
							array_push($csv[$csvstart+2],($single+$combine+$zdiscount));
							array_push($csv[$csvstart+3],$znumber);
							array_push($csv[$csvstart+4],round((($ztotal+$zdiscount)/$znumber),2));
							$mtotal=$mtotal+$ztotal;
							$mdiscount=$mdiscount+$zdiscount;
							$mnumber=$mnumber+$znumber;
							$msingle=$msingle+$single;
							$mcombine=$mcombine+$combine;
							if(strlen($ptotal)==0){
								$ptotal=$ptotal.($ztotal+$zdiscount);
							}
							else{
								$ptotal=$ptotal.",".($ztotal+$zdiscount);
							}
							echo "</tr>";
						}
						else if($date<max(array_keys($zcounter))){
							echo "<tr>";
							echo "<td class='date'>".substr($tempdate,2)."</td>";
							array_push($csv[0],$date);
							for($deptcode=1;$deptcode<=$menu[0]['value'];$deptcode++){
								if(isset($menu[$deptcode]['total'])){
									echo "<td class='item title'";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo ">0</td>";
									array_push($csv[$csvstart],"0");
									for($code=1;$code<=$menu[$deptcode]['total'];$code++){
										echo "<td class='item'";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo ">0</td>";
										array_push($csv[$csvstart+$code],"0");
									}
									$csvstart=$csvstart+$menu[$deptcode]['total']+1;
									$index++;
								}
								else{
								}
							}
							//echo "<td class='item'><font color='#ff0000'>0</font></td>";
							echo "<td class='item total'><font color='#ff0000'>0</font></td>";
							//echo "<td class='item'><font color='#0033ff'>0</font></td>";
							echo "<td class='item'><strong>0</strong></td>";
							echo "<td class='item'><strong>0</strong></td>";
							echo "<td class='item'><strong>0</strong></td>";
							echo "<td class='item'><strong>0</strong></td>";
							array_push($csv[$csvstart],"0");
							array_push($csv[$csvstart+1],"0");
							array_push($csv[$csvstart+2],"0");
							array_push($csv[$csvstart+3],"0");
							array_push($csv[$csvstart+4],"0");
							if(strlen($ptotal)==0){
								$ptotal=$ptotal."0";
							}
							else{
								$ptotal=$ptotal.",0";
							}
							echo "</tr>";
						}
					}
					echo "<tr>";
					$index=1;
					$csvstart=2;//匯出檔案陣列之開始row
					for($i=0;$i<=$menu[0]['value'];$i++){
						$HTMLstring="";
						$tempamt=0;//類別加總金額
						$tempqty=0;//類別加總數量
						if($i==0){
							echo "<td><center>小計</center></td>";
							array_push($csv[0],"小計");
						}
						else{
							if(isset($menu[$i]['total'])){
								for($code=1;$code<=$menu[$i]['total'];$code++){
									$HTMLstring=$HTMLstring."<td class='item'";if($index%2)$HTMLstring=$HTMLstring." style='background-color:".$bgcolor1."'";else $HTMLstring=$HTMLstring." style='background-color:".$bgcolor2."'";$HTMLstring=$HTMLstring.">".$menu[$i]['itemqty'.$code]."</td>";
									array_push($csv[$csvstart+$code],$menu[$i]['itemqty'.$code]);
									$tempamt=$tempamt+$menu[$i]['itemamt'.$code];
									$tempqty=$tempqty+$menu[$i]['itemqty'.$code];
								}
							}
							else{
							}
						}
						$tempstring="";
						if(strlen($HTMLstring)>0){
							array_push($csv[$csvstart],$tempamt);
							$tempstring=$tempstring."<td class='item title'";if($index%2)$tempstring=$tempstring." style='background-color:".$bgcolor1."'";else $tempstring=$tempstring." style='background-color:".$bgcolor2."'";$tempstring=$tempstring.">".$tempamt."</td>";
							$csvstart=$csvstart+$menu[$i]['total']+1;
							$index++;
						}
						else{
						}
						echo $tempstring.$HTMLstring;
						$menu[$i]['tempamt']=$tempamt;
						$menu[$i]['tempqty']=$tempqty;
					}
					echo "<td class='item total'><font color='#ff0000'>".$mdiscount."</font></td>";
					echo "<td class='item'><strong>".$msingle."</strong></td>";
					echo "<td class='item'><strong>".($msingle+$mcombine+$mdiscount)."</strong></td>";
					echo "<td class='item'><strong></strong></td>";
					echo "<td class='item'><strong></strong></td>";
					array_push($csv[$csvstart],$mdiscount);
					array_push($csv[$csvstart+1],$msingle);
					array_push($csv[$csvstart+2],($msingle+$mcombine+$mdiscount));
					array_push($csv[$csvstart+3],"");
					array_push($csv[$csvstart+4],"");
					echo "</tr>";
					echo "<tr>";
					$index=0;
					$dept=1;//類別計數器
					$csvstart=2;//匯出檔案陣列之開始row
					for($i=0;$i<=$menu[0]['value'];$i++){
						if($i==0){
							echo "<td><center>合計</center></td>";
							array_push($csv[0],"合計");
							$index++;
						}
						else{
							if(isset($menu[$i]['total'])){
								echo "<td class='item title'";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo "><input type='hidden' id='dept".$dept."' value='".$menu[$i]['total']."'>".$menu[$i]['tempamt']."</td>";
								array_push($csv[$csvstart],$menu[$i]['tempamt']);
								//echo "<td colspan='".$menu[$i]['total']."' class='item'";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo ">".$menu[$i]['tempqty']."</td>";
								echo "<td class='item'";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo ">".$menu[$i]['tempqty']."</td>";
								array_push($csv[$csvstart+1],$menu[$i]['tempqty']);
								for($cells=1;$cells<$menu[$i]['total'];$cells++){
									echo "<td></td>";
									array_push($csv[$csvstart+1+$cells],"");
								}
								$dept++;
								$csvstart=$csvstart+$menu[$i]['total']+1;
								$index++;
							}
							else{
							}
							/*echo "<td class='item'";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo "></td>";
							for($code=1;$code<=$menu[$i]['total'];$code++){
								if(isset($menu[$i]['total'])){
									echo "<td class='item'";if($index%2)echo " style='background-color:".$bgcolor1."'";else echo " style='background-color:".$bgcolor2."'";echo ">".$menu[$i]['itemtotal'.$code]."</td>";
									
								}
								else{
								}
							}*/
						}
					}
					//echo "<td class='item'><font color='#ff0000'>".$mtotal."</font></td>";
					echo "<td class='item total'><font color='#ff0000'>".$mdiscount."</font></td>";
					//echo "<td class='item'><font color='#0033ff'>".($mtotal+$mdiscount)."</font></td>";
					echo "<td class='item'><strong>".$msingle."</strong></td>";
					echo "<td class='item'><strong>".($msingle+$mcombine+$mdiscount)."</strong></td>";
					echo "<td class='item'><strong>".$mnumber."</strong></td>";
					echo "<td class='item'><strong>".round((($mtotal+$mdiscount)/$mnumber),2)."</strong></td>";
					array_push($csv[$csvstart],$mdiscount);
					array_push($csv[$csvstart+1],$msingle);
					array_push($csv[$csvstart+2],($msingle+$mcombine+$mdiscount));
					array_push($csv[$csvstart+3],$mnumber);
					array_push($csv[$csvstart+4],round((($mtotal+$mdiscount)/$mnumber),2));
					echo "</tr>";
					echo "</table></div>
						<input type='hidden' id='taRow' value='".$dept."'>
						<script>transpose('source','paper','商品銷售數量統計','".$finaltime[0]['finaltime']."');deleteCells('dCells','taRow');</script>";
				}
				$conn->close();
				/*echo "<div id='paperimg'>";
				echo "<iframe name='myframe' width='800px' height='300px'>
					</iframe>
					<form method='post' action='PaperImg.php' id='myform' target='myframe'>
						<input type='hidden' name='pttl' value='".$ptotal."'>
						<input type='hidden' name='width' value='800'>
						<input type='hidden' name='height' value='300'>
					</form>
					<script>document.getElementById(\"myform\").submit();</script>";
				echo "</div>";*/
				//用session的方式傳遞檔案匯出陣列
				if(isset($_SESSION['array'])){
					unset($_SESSION['array']);
					$_SESSION['array']=$csv;
				}
				else{
					$_SESSION['array']=$csv;
				}
			}
		}
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='newstototal'){
		if(strlen($enddate)<10){
			$enddate=$enddate."-".date("t",mktime('0','0','0','1',substr($enddate,5,2),substr($enddate,0,4)));
		}
		echo "<form method='post' action='' id='search'>
				<input type='hidden' name='conttype' value='newstototal'>
				<table>
					<!-- <caption>設定時間區間</caption> -->";
				if($_SESSION['ID']=='admin'){
					echo "<tr>
							<td>資料庫</td>
							<td><select name='DB'>";
					$Path="../../DB";
					foreach(glob($Path."/*") as $entry) {
					  if (in_array($entry, array(".", "..")) === false) {
						 $temp=preg_split("(".$Path."/)",$entry);
						 echo "<option value='".$temp[1]."'>".$temp[1]."</option>";
					  }
					}
					echo "</select></td>
						</tr>";
				}
				else if($usergroup=='boss'){
					$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
					$sql="SELECT usedb,deptname FROM UserLogin WHERE company=(SELECT company FROM UserLogin WHERE id='".$_SESSION['ID']."') AND usergroup IS NULL AND function LIKE '%pos%'";
					$temp=sqlquery($conn,$sql,"mysql");
					sqlclose($conn,"mysql");
					echo "<tr>
							<td>資料庫</td>
							<td><select name='DB'>";
					foreach($temp as $value){
						echo "<option value='".$value['usedb']."' ";
						if(!empty($_SESSION['DB'])&&$_SESSION['DB']==$value['usedb']){
							echo "selected";
						}
						echo ">".$value['deptname']."</optioin>";
					}
						echo "</select></td>
						</tr>";
				}
				else{
				}
				echo "<tr>
						<td>時間區間</td>
						<td>
							<input type='date' name='startdate' id='startdate'";if(strlen($startdate)>0)echo " value=".$startdate;echo ">～<input type='date' name='enddate' id='enddate'";if(strlen($enddate)>0)echo " value=".$enddate;echo ">
						</td>
					</tr>
					<tr>
						<td colspan='2'><input type='button' value='送出' onclick='basiccheck(this.form)'></td>
					</tr>
				</table>
			</form>";
		if(empty($startdate)&&empty($enddate)){
			echo "請先設定想瀏覽的時間。";
		}
		else{
			$table1=array();//商品類別統計
			$menu=array();//$menu[類別編號]=類別名稱
			$zcounter=array();//每日的班別;a[bizdate][total]=n,a[bizdate][counter1]~a[bizdate][counterN]=counter;
			$paper=array();//
			//$conn=sqlconnect("../DB/".$DB,"SALES_".$year.$month.".DB","","","","sqlite");
			$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
			if(!$conn){
				echo "資料庫發生錯誤或尚未上傳資料。";
			}
			else{
				echo "<div id='title'>";
				$sql="SELECT bizdate,itemcode,itemname,SUM(qty) AS qty,SUM(amt) AS amt,itemdeptcode,itemdeptname,itemgrpcode,itemgrpname,zcounter,createdatetime FROM alldetails WHERE company='".$DB."' AND dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008' AND bizdate BETWEEN '".$startdate."' AND '".$enddate."' GROUP BY bizdate,itemcode,itemdeptcode,itemgrpcode,zcounter";
				//$table1=sqlquery($conn,$sql,'sqlite');
				$table1=sqlquery($conn,$sql,'mysql');
				if(sizeof($table1)==0){
					echo "查無資料。";
				}
				else if($table1[0]=="SQL語法錯誤"||$table1[0]=="連線失敗"){
					if($dubug==1){
						echo $table1[0]."(select)".$sql;
					}
					else{
						echo $table1[0]."(select)";
					}
				}
				else{
					//$a=$table1[0]['CREATEDATETIME'];
					$a=$table1[0]['createdatetime'];
					//$maxDay=cal_days_in_month(CAL_GREGORIAN,substr($a,4,2),substr($a,0,4));//自動判斷某年某月的天數
					$maxDay=date("t");
					/*$sql="SELECT CST012.ITEMDEPTCODE,CST012.ITEMDEPTNAME,CST012.ITEMCODE,CST012.ITEMNAME 
					FROM CST012 
					JOIN (
						SELECT DISTINCT ITEMDEPTCODE,ITEMCODE 
						FROM (
							SELECT * FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008'
						)
					) as a ON a.ITEMDEPTCODE=CST012.ITEMDEPTCODE WHERE CST012.DTLMODE='1' AND CST012.DTLTYPE='1' AND (CST012.DTLFUNC='01' OR CST012.DTLFUNC='03') AND CST012.ITEMDEPTCODE<>'000008' GROUP BY CST012.ITEMDEPTCODE,CST012.ITEMCODE";*/
					$sql="SELECT DISTINCT itemdeptcode,itemdeptname,itemcode,itemname FROM alldetails WHERE company='".$DB."' AND dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008' AND bizdate BETWEEN '".$startdate."' AND '".$enddate."'";
					//$tempmenu=sqlquery($conn,$sql,'sqlite');//暫存類別對照表；類別編號與類別名稱
					$tempmenu=sqlquery($conn,$sql,'mysql');
					//$sql="SELECT BIZDATE,ZCOUNTER FROM CST012 WHERE DTLMODE='1' AND DTLTYPE='1' AND (DTLFUNC='01' OR DTLFUNC='03') AND ITEMDEPTCODE<>'000008' GROUP BY BIZDATE,ZCOUNTER";//之前使用
					$sql="SELECT bizdate,zcounter FROM alldetails WHERE dtlmode='1' AND dtltype='1' AND (dtlfunc='01' OR dtlfunc='03') AND itemdeptcode<>'000008' GROUP BY bizdate,zcounter";//之前使用
					//$tempzcounter=sqlquery($conn,$sql,'sqlite');//班別；營業日期與當天班別
					$tempzcounter=sqlquery($conn,$sql,'mysql');

					foreach($tempmenu as $b){//將暫存菜單轉成新的陣列；$menu[類別編號][產品編號][dept]=類別名稱,$menu[類別編號][產品編號][item]=產品名稱
						if(isset($menu[intval($b['itemdeptcode'])]['total'])){
							$menu[intval($b['itemdeptcode'])]['total']=$menu[intval($b['itemdeptcode'])]['total']+1;
							$menu[intval($b['itemdeptcode'])]['itemcode'.$menu[intval($b['itemdeptcode'])]['total']]=intval($b['itemcode']);
							$menu[intval($b['itemdeptcode'])]['itemname'.$menu[intval($b['itemdeptcode'])]['total']]=$b['itemname'];
						}
						else{
							$menu[intval($b['itemdeptcode'])]['total']=1;
							$menu[intval($b['itemdeptcode'])]['name']=$b['itemdeptname'];
							$menu[intval($b['itemdeptcode'])]['itemcode1']=intval($b['itemcode']);
							$menu[intval($b['itemdeptcode'])]['itemname1']=$b['itemname'];
						}
					}
					$menu[0]['value']=max(array_keys($menu));
					$zcounterList=array();
					$nowcounter=0;
					foreach($tempzcounter as $b){//將zcounter陣列改成非方正陣列;a[bizdate][total]=n,a[bizdate][counter1]~a[bizdate][counterN]=counter;這樣能夠同時解決一天1個班別以上以及跨夜的業績歸屬前一天的兩個問題
						if(sizeof($zcounter)==0){
							$zcounter[intval(substr($b['bizdate'],strlen($b['bizdate'])-2,2))]['total']=1;
							$zcounter[intval(substr($b['bizdate'],strlen($b['bizdate'])-2,2))]['counter1']=intval($b['zcounter']);
							$nowcounter=intval($b['zcounter']);
							$zcounterList[]=intval($b['zcounter']);
						}
						else{
							if(isset($zcounter[intval(substr($b['bizdate'],strlen($b['bizdate'])-2,2))]['total'])){
								$zcounter[intval(substr($b['bizdate'],strlen($b['bizdate'])-2,2))]['total']=$zcounter[intval(substr($b['bizdate'],strlen($b['bizdate'])-2,2))]['total']+1;
								$zcounter[intval(substr($b['bizdate'],strlen($b['bizdate'])-2,2))]['counter'.$zcounter[intval(substr($b['bizdate'],strlen($b['bizdate'])-2,2))]['total']]=intval($b['zcounter']);
								$nowcounter=intval($b['zcounter']);
								$zcounterList[]=intval($b['zcounter']);
							}
							else{
								if(intval($b['zcounter'])==$nowcounter){
									//基本上ZCOUNTER隔天的值至少是大於等於，如果是等於則表示跨夜班，業績歸屬前一天
									//由於會發生人為疏忽忘記交班的情況，需要由我們幫忙重設交班計數器，如此一來，大於等於的狀況就不再為正常判斷標準
									//因此小於的情況，與大於的情況類似
								}
								else if(intval($b['zcounter'])<$nowcounter){
									$zcounter[intval(substr($b['bizdate'],6,2))]['total']=1;
									$zcounter[intval(substr($b['bizdate'],6,2))]['counter1']=intval($b['zcounter']);
									$nowcounter=intval($b['zcounter']);
									$zcounterList[]=intval($b['zcounter']);
								}
								else{//新的ZCOUNTER如果大於，則表示為新的一天
									$zcounter[intval(substr($b['bizdate'],6,2))]['total']=1;
									$zcounter[intval(substr($b['bizdate'],6,2))]['counter1']=intval($b['zcounter']);
									$nowcounter=intval($b['zcounter']);
									$zcounterList[]=intval($b['zcounter']);
								}
							}
						}
					}
					foreach($table1 as $c){//轉存成$paper[類別編號][產品編號][qty]=銷售數量,$paper[類別編號][產品編號][amt]=銷售金額
						if(in_array(intval($c['zcounter']),$zcounterList)){
							if(isset($paper[intval($c['itemdeptcode'])][intval($c['itemcode'])]['qty'])){
								$paper[intval($c['itemdeptcode'])][intval($c['itemcode'])]['qty']=$paper[intval($c['itemdeptcode'])][intval($c['itemcode'])]['qty']+$c['qty'];
								$paper[intval($c['itemdeptcode'])][intval($c['itemcode'])]['amt']=$paper[intval($c['itemdeptcode'])][intval($c['itemcode'])]['amt']+$c['amt'];
							}
							else{
								$paper[intval($c['itemdeptcode'])][intval($c['itemcode'])]['qty']=$c['qty'];
								$paper[intval($c['itemdeptcode'])][intval($c['itemcode'])]['amt']=$c['amt'];
							}
						}
						else{
						}
					}
					//print_r($menu);
					$width=500;
					$height=300;
					echo "<div id='paperTable' style='margin-top:10px;'>
							<!--<div id='paper' style='float:left'>
								<table>
									<caption>湯頭數量佔比</caption>
									<tr>
										<td>名稱</td>
										<td>數量</td>
									</tr>";
							$pnumber="";
							//$pmoney="";
							$pdept="";
							$dept=1;
							if(isset($menu[$dept]['total'])&&$menu[$dept]['total']==1){
								echo "<tr>
										<td>".$menu[$dept]['itemname1']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['qty']."</td>
									</tr>";
								$pdept=$pdept.$menu[$dept]['itemname1']."\n(%.1f%%)";
								$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode1']]['qty'];
							}
							else if(isset($menu[$dept]['total'])&&$menu[$dept]['total']>1){
								for($i=1;$i<=$menu[$dept]['total'];$i++){
									echo "<tr>
										<td>".$menu[$dept]['itemname'.$i]."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty']."</td>
										</tr>";
									if(strlen($pdept)==0){
										$pdept=$pdept.$menu[$dept]['itemname'.$i]."\n(%.1f%%)";
									}
									else{
										$pdept=$pdept.",".$menu[$dept]['itemname'.$i]."\n(%.1f%%)";
									}
									if(strlen($pnumber)==0){
										$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
									else{
										$pnumber=$pnumber.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
								}
							}
							else{
							}
							echo "</table>
							</div>-->
							<div style='float:left;margin-left:10px;'>";
								echo "<iframe name='myframe2' width='".$width."px' height='".$height."px'></iframe>
								<form method='post' action='pitable.php' id='myform2' target='myframe2'>
									<input type='hidden' name='ydata' value='".$pnumber."'>
									<input type='hidden' name='xdata' value='".$pdept."'>
									<input type='hidden' name='title' value='湯頭'>
									<input type='hidden' name='width' value='".$width."'>
									<input type='hidden' name='height' value='".$height."'>
								</form>
								<script>document.getElementById(\"myform2\").submit();</script>";
						echo "</div>
						</div>";
					echo "<div id='paperTable' style='margin-top:10px;'>
							<!--<div id='paper' style='float:left'>
								<table>
									<caption>套餐與單點佔比</caption>
									<tr>
										<td></td>
										<td>數量</td>
										<td>金額</td>
									</tr>";
							$pnumber="";
							$pmoney="";
							$pdept="";
							$temp7andeither=array();
							for($dept=1;$dept<=$menu[0]['value'];$dept++){
								if($dept==7){
									if(isset($menu[$dept]['total'])&&$menu[$dept]['total']==1){
										if(isset($temp7andeither['group']['qty'])){
											$temp7andeither['group']['qty']=$temp7andeither['group']['qty']+$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
											$temp7andeither['group']['amt']=$temp7andeither['group']['amt']+$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
										}
										else{
											$temp7andeither['group']['qty']=$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
											$temp7andeither['group']['amt']=$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
										}
									}
									else if(isset($menu[$dept]['total'])&&$menu[$dept]['total']>1){
										for($i=1;$i<=$menu[$dept]['total'];$i++){
											if(isset($temp7andeither['group']['qty'])){
												$temp7andeither['group']['qty']=$temp7andeither['group']['qty']+$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
												$temp7andeither['group']['amt']=$temp7andeither['group']['amt']+$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
											}
											else{
												$temp7andeither['group']['qty']=$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
												$temp7andeither['group']['amt']=$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
											}
										}
									}
									else{
									}
								}
								else if($dept!=7&&$dept!=1){
									if(isset($menu[$dept]['total'])&&$menu[$dept]['total']==1){
										if(isset($temp7andeither['single']['qty'])){
											$temp7andeither['single']['qty']=$temp7andeither['single']['qty']+$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
											$temp7andeither['single']['amt']=$temp7andeither['single']['amt']+$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
										}
										else{
											$temp7andeither['single']['qty']=$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
											$temp7andeither['single']['amt']=$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
										}
									}
									else if(isset($menu[$dept]['total'])&&$menu[$dept]['total']>1){
										for($i=1;$i<=$menu[$dept]['total'];$i++){
											if(isset($temp7andeither['single']['qty'])){
												$temp7andeither['single']['qty']=$temp7andeither['single']['qty']+$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
												$temp7andeither['single']['amt']=$temp7andeither['single']['amt']+$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
											}
											else{
												$temp7andeither['single']['qty']=$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
												$temp7andeither['single']['amt']=$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
											}
										}
									}
									else{
									}
								}
							}
							echo "<tr>";
							echo "<td>套餐</td>
								<td>".$temp7andeither['group']['qty']."</td>
								<td>".$temp7andeither['group']['amt']."</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>單點</td>
								<td>".$temp7andeither['single']['qty']."</td>
								<td>".$temp7andeither['single']['amt']."</td>";
							echo "</tr>";
							$pdept="套餐\n(%.1f%%),單點\n(%.1f%%)";
							$pnumber=$temp7andeither['group']['qty'].",".$temp7andeither['single']['qty'];
							$pmoney=$temp7andeither['group']['amt'].",".$temp7andeither['single']['amt'];
							echo "</table>
							</div>-->
							<div style='float:left;margin-left:10px;'>";
								echo "<iframe name='myframe4' width='".$width."px' height='".$height."px' style='float:left'></iframe>
								<form method='post' action='pitable.php' id='myform4' target='myframe4'>
									<input type='hidden' name='ydata' value='".$pmoney."'>
									<input type='hidden' name='xdata' value='".$pdept."'>
									<input type='hidden' name='title' value='套餐/單點'>
									<input type='hidden' name='width' value='".$width."'>
									<input type='hidden' name='height' value='".$height."'>
								</form>
								<script>document.getElementById(\"myform4\").submit();</script>";
					echo "</div>
						</div>";
					echo "<div id='paperTable' style='margin-top:10px;'>
							<!--<div id='paper' style='float:left'>
								<table>
									<caption>商品類別統計</caption>
									<tr>
										<td>商品類別</td>
										<td>數量</td>
										<td>金額</td>
									</tr>";
							$pdept="";
							$pnumber="";
							$pmoney="";
							for($dept=1;$dept<=$menu[0]['value'];$dept++){
								if(isset($menu[$dept]['name'])&&$dept!=1){
									$tempqty=0;
									$tempamt=0;
									if(isset($menu[$dept]['total'])&&$menu[$dept]['total']==1){
										$tempqty=$paper[$dept][$menu[$dept]['itemcode1']]['qty'];
										$tempamt=$paper[$dept][$menu[$dept]['itemcode1']]['amt'];
									}
									else if(isset($menu[$dept]['total'])&&$menu[$dept]['total']>1){
										for($i=1;$i<=$menu[$dept]['total'];$i++){
											$tempqty=$tempqty+$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
											$tempamt=$tempamt+$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
										}
									}
									else{
									}
									echo "<tr>";
									echo "<td>".$menu[$dept]['name']."</td>
										<td>".$tempqty."</td>
										<td>".$tempamt."</td>";
									echo "</tr>";
									if(strlen($pdept)==0){
										$pdept=$pdept.$menu[$dept]['name']."(%.1f%%)";
									}
									else{
										$pdept=$pdept.",".$menu[$dept]['name']."(%.1f%%)";
									}
									if(strlen($pnumber)==0){
										$pnumber=$pnumber.$tempqty;
									}
									else{
										$pnumber=$pnumber.",".$tempqty;
									}
									if(strlen($pmoney)==0){
										$pmoney=$pmoney.$tempamt;
									}
									else{
										$pmoney=$pmoney.",".$tempamt;
									}
								}
								else{
								}
							}
							echo "</table>
							</div>-->
							<div style='float:left;margin-left:10px;'>";
							echo "<iframe name='myframe1' width='".$width."px' height='".$height."px'></iframe>
								<form method='post' action='pitable.php' id='myform1' target='myframe1'>
									<input type='hidden' name='number' value='".$pnumber."'>
									<input type='hidden' name='ydata' value='".$pmoney."'>
									<input type='hidden' name='xdata' value='".$pdept."'>
									<input type='hidden' name='title' value='各品項佔比'>
									<input type='hidden' name='width' value='".$width."'>
									<input type='hidden' name='height' value='".$height."'>
								</form>
								<script>document.getElementById(\"myform1\").submit();</script>";
						echo "</div>
							</div>";
					echo "<div id='paperTable' style='margin-top:10px;'>
							<!--<div id='paper' style='float:left'>
								<table>
									<caption>套餐組佔比</caption>
									<tr>
										<td>套餐名稱</td>
										<td>數量</td>
										<td>金額</td>
									</tr>";
							$pnumber="";
							$pmoney="";
							$pdept="";
							$dept=9;
							if(isset($menu[$dept]['total'])&&$menu[$dept]['total']==1){
								echo "<tr>
										<td>".$menu[$dept]['itemname1']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['qty']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['amt']."</td>
									</tr>";
								$pdept=$pdept.$menu[$dept]['itemname1'];
								$pmoney=$pmoney.$paper[$dept][$menu[$dept]['itemcode1']]['amt'];
								$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode1']]['qty'];
							}
							else if(isset($menu[$dept]['total'])&&$menu[1]['total']>1){
								for($i=1;$i<=$menu[$dept]['total'];$i++){
									echo "<tr>
										<td>".$menu[$dept]['itemname'.$i]."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt']."</td>
										</tr>";
									if(strlen($pdept)==0){
										$pdept=$pdept.$menu[$dept]['itemname'.$i]."(%.1f%%)";
									}
									else{
										$pdept=$pdept.",".$menu[$dept]['itemname'.$i]."(%.1f%%)";
									}
									if(strlen($pmoney)==0){
										$pmoney=$pmoney.$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
									}
									else{
										$pmoney=$pmoney.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
									}
									if(strlen($pnumber)==0){
										$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
									else{
										$pnumber=$pnumber.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
								}
							}
							else{
							}
							echo "</table>
							</div>-->
							<div style='float:left;margin-left:10px;'>";
								echo "<iframe name='myframe5' width='".$width."px' height='".$height."px'></iframe>
								<form method='post' action='pitable.php' id='myform5' target='myframe5'>
									<input type='hidden' name='ydata' value='".$pnumber."'>
									<input type='hidden' name='xdata' value='".$pdept."'>
									<input type='hidden' name='title' value='秘製品'>
									<input type='hidden' name='width' value='".$width."'>
									<input type='hidden' name='height' value='".$height."'>
								</form>
								<script>document.getElementById(\"myform5\").submit();</script>";
					echo "</div>
						</div>";
					echo "<div id='paperTable' style='margin-top:10px;'>
							<!--<div id='paper' style='float:left'>
								<table>
									<caption>麵類</caption>
									<tr>
										<td></td>
										<td>數量</td>
										<td>金額</td>
									</tr>";
							$pnumber="";
							$pmoney="";
							$pdept="";
							$dept=5;
							if(isset($menu[$dept]['total'])&&$menu[$dept]['total']==1){
								echo "<tr>
										<td>".$menu[$dept]['itemname1']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['qty']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['amt']."</td>
									</tr>";
								$pdept=$pdept.$menu[$dept]['itemname1'];
								$pmoney=$pmoney.$paper[$dept][$menu[$dept]['itemcode1']]['amt'];
								$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode1']]['qty'];
							}
							else if(isset($menu[$dept]['total'])&&$menu[1]['total']>1){
								for($i=1;$i<=$menu[$dept]['total'];$i++){
									echo "<tr>
										<td>".$menu[$dept]['itemname'.$i]."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt']."</td>
										</tr>";
									if(strlen($pdept)==0){
										$pdept=$pdept.$menu[$dept]['itemname'.$i]."(%.1f%%)";
									}
									else{
										$pdept=$pdept.",".$menu[$dept]['itemname'.$i]."(%.1f%%)";
									}
									if(strlen($pmoney)==0){
										$pmoney=$pmoney.$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
									}
									else{
										$pmoney=$pmoney.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
									}
									if(strlen($pnumber)==0){
										$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
									else{
										$pnumber=$pnumber.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
								}
							}
							else{
							}
							echo "</table>
							</div>-->
							<div style='float:left;margin-left:10px;'>";
								echo "<iframe name='myframe6' width='".$width."px' height='".$height."px'></iframe>
								<form method='post' action='pitable.php' id='myform6' target='myframe6'>
									<input type='hidden' name='ydata' value='".$pnumber."'>
									<input type='hidden' name='xdata' value='".$pdept."'>
									<input type='hidden' name='title' value='麵類'>
									<input type='hidden' name='width' value='".$width."'>
									<input type='hidden' name='height' value='".$height."'>
								</form>
								<script>document.getElementById(\"myform6\").submit();</script>";
					echo "</div>
						</div>";
					echo "<div id='paperTable' style='margin-top:10px;'>
							<!--<div id='paper' style='float:left'>
								<table>
									<caption>肉類</caption>
									<tr>
										<td></td>
										<td>數量</td>
										<td>金額</td>
									</tr>";
							$pnumber="";
							$pmoney="";
							$pdept="";
							$dept=6;
							if(isset($menu[$dept]['total'])&&$menu[$dept]['total']==1){
								echo "<tr>
										<td>".$menu[$dept]['itemname1']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['qty']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['amt']."</td>
									</tr>";
								$pdept=$pdept.$menu[$dept]['itemname1'];
								$pmoney=$pmoney.$paper[$dept][$menu[$dept]['itemcode1']]['amt'];
								$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode1']]['qty'];
							}
							else if(isset($menu[$dept]['total'])&&$menu[1]['total']>1){
								for($i=1;$i<=$menu[$dept]['total'];$i++){
									echo "<tr>
										<td>".$menu[$dept]['itemname'.$i]."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt']."</td>
										</tr>";
									if(strlen($pdept)==0){
										$pdept=$pdept.$menu[$dept]['itemname'.$i]."(%.1f%%)";
									}
									else{
										$pdept=$pdept.",".$menu[$dept]['itemname'.$i]."(%.1f%%)";
									}
									if(strlen($pmoney)==0){
										$pmoney=$pmoney.$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
									}
									else{
										$pmoney=$pmoney.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
									}
									if(strlen($pnumber)==0){
										$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
									else{
										$pnumber=$pnumber.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
								}
							}
							else{
							}
							echo "</table>
							</div>-->
							<div style='float:left;margin-left:10px;'>";
								echo "<iframe name='myframe7' width='".$width."px' height='".$height."px'></iframe>
								<form method='post' action='pitable.php' id='myform7' target='myframe7'>
									<input type='hidden' name='ydata' value='".$pnumber."'>
									<input type='hidden' name='xdata' value='".$pdept."'>
									<input type='hidden' name='title' value='肉類'>
									<input type='hidden' name='width' value='".$width."'>
									<input type='hidden' name='height' value='".$height."'>
								</form>
								<script>document.getElementById(\"myform7\").submit();</script>";
					echo "</div>
						</div>";
					echo "<div id='paperTable' style='margin-top:10px;'>
							<!--<div id='paper' style='float:left'>
								<table>
									<caption>套餐組佔比</caption>
									<tr>
										<td>套餐名稱</td>
										<td>數量</td>
										<td>金額</td>
									</tr>";
							$pnumber="";
							$pmoney="";
							$pdept="";
							$dept=7;
							if(isset($menu[$dept]['total'])&&$menu[$dept]['total']==1){
								echo "<tr>
										<td>".$menu[$dept]['itemname1']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['qty']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode1']]['amt']."</td>
									</tr>";
								$pdept=$pdept.$menu[$dept]['itemname1'];
								$pmoney=$pmoney.$paper[$dept][$menu[$dept]['itemcode1']]['amt'];
								$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode1']]['qty'];
							}
							else if(isset($menu[$dept]['total'])&&$menu[1]['total']>1){
								for($i=1;$i<=$menu[$dept]['total'];$i++){
									echo "<tr>
										<td>".$menu[$dept]['itemname'.$i]."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty']."</td>
										<td>".$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt']."</td>
										</tr>";
									if(strlen($pdept)==0){
										$pdept=$pdept.$menu[$dept]['itemname'.$i]."(%.1f%%)";
									}
									else{
										$pdept=$pdept.",".$menu[$dept]['itemname'.$i]."(%.1f%%)";
									}
									if(strlen($pmoney)==0){
										$pmoney=$pmoney.$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
									}
									else{
										$pmoney=$pmoney.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['amt'];
									}
									if(strlen($pnumber)==0){
										$pnumber=$pnumber.$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
									else{
										$pnumber=$pnumber.",".$paper[$dept][$menu[$dept]['itemcode'.$i]]['qty'];
									}
								}
							}
							else{
							}
							echo "</table>
							</div>-->
							<div style='float:left;margin-left:10px;'>";
								echo "<iframe name='myframe3' width='".$width."px' height='".$height."px'></iframe>
								<form method='post' action='pitable.php' id='myform3' target='myframe3'>
									<input type='hidden' name='ydata' value='".$pnumber."'>
									<input type='hidden' name='xdata' value='".$pdept."'>
									<input type='hidden' name='title' value='套餐'>
									<input type='hidden' name='width' value='".$width."'>
									<input type='hidden' name='height' value='".$height."'>
								</form>
								<script>document.getElementById(\"myform3\").submit();</script>";
					echo "</div>
						</div>";
				}
				$conn->close();
				echo "</div>";
			}
		}
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='atmoment'){
		echo "<form method='post' action='' id='search'>
				<input type='hidden' name='conttype' value='atmoment'>
				<table>
					<!-- <caption>設定時間區間</caption> -->";
				if($_SESSION['ID']=='admin'){
					echo "<tr>
							<td>資料庫</td>
							<td><select name='DB'>";
					$Path="../../DB";
					foreach(glob($Path."/*") as $entry) {
					  if (in_array($entry, array(".", "..")) === false) {
						 $temp=preg_split("(".$Path."/)",$entry);
						 echo "<option value='".$temp[1]."'>".$temp[1]."</option>";
					  }
					}
					echo "</select></td>
						</tr>";
				}
				else if($usergroup=='boss'){
					$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
					$sql="SELECT usedb,deptname FROM UserLogin WHERE company=(SELECT company FROM UserLogin WHERE id='".$_SESSION['ID']."') AND usergroup IS NULL AND function LIKE '%pos%'";
					$temp=sqlquery($conn,$sql,"mysql");
					sqlclose($conn,"mysql");
					echo "<tr>
							<td>資料庫</td>
							<td><select name='DB'>";
					foreach($temp as $value){
						echo "<option value='".$value['usedb']."' ";
						if(!empty($_SESSION['DB'])&&$_SESSION['DB']==$value['usedb']){
							echo "selected";
						}
						echo ">".$value['deptname']."</optioin>";
					}
						echo "</select></td>
						</tr>";
				}
				else{
				}
				echo "<tr>
						<td colspan='2'><input type='submit' value='送出'></td>
					</tr>
				</table>
			</form>";
		$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","","mysql");
		if(!$conn){
			echo "資料庫發生錯誤。";
		}
		else{
			$sql="SELECT atmoment FROM UserLogin WHERE usedb='".$_SESSION['DB']."' AND function LIKE '%pos%'";
			$temp=sqlquery($conn,$sql,"mysql");
			if(sizeof($temp)==0){
				$table=1;
			}
			else{
				$company=substr($temp[0]['atmoment'],0,4);
				$dept=substr($temp[0]['atmoment'],5,4);
				$table=array();
				$sql="SELECT DISTINCT company,dept,filename,itemcode,itemname,itemdeptcode,itemdeptname,itemgrpcode,itemgrpname,SUM(qty) AS qty,unitprice,SUM(amt) AS amt FROM AtMoment WHERE company='".$company."' AND dept='".$dept."' GROUP BY filename,itemcode,itemdeptcode";
				$table=sqlquery($conn,$sql,"mysql");
				$sql="SELECT COUNT(filename) AS number FROM (SELECT DISTINCT filename FROM AtMoment WHERE company='".$company."' AND dept='".$dept."') AS A";
				$table2=sqlquery($conn,$sql,"mysql");
			}
			if($table==1){
			}
			else if(sizeof($table)==0){
				echo "查無資料。";
			}
			else if($table[0]=="SQL語法錯誤"||$table[0]=="連線失敗"){
				if($dubug==1){
					echo $table[0]."(select)".$sql;
				}
				else{
					echo $table[0]."(select)";
				}
			}
			else{
				$item=array();
				foreach($table as $a){
					if(isset($item[intval($a['itemcode'])]['name'])){
						$item[intval($a['itemcode'])]['qty']=$item[intval($a['itemcode'])]['qty']+$a['qty'];
						$item[intval($a['itemcode'])]['amt']=$item[intval($a['itemcode'])]['amt']+$a['amt'];
					}
					else{
						$item[intval($a['itemcode'])]['name']=$a['itemname'];
						$item[intval($a['itemcode'])]['qty']=$a['qty'];
						$item[intval($a['itemcode'])]['amt']=$a['amt'];
					}
					if(isset($item[0]['total'])){
						$item[0]['total']=$item[0]['total']+$a['amt'];
					}
					else{
						$item[0]['total']=$a['amt'];
					}
				}
				echo "<div class='atmoment'>
					<table>
						<tr>
							<td>目前營業額</td>
						</tr>
						<tr>
							<td>".$item[0]['total']."</td>
						</tr>
					</table><br><br>";
				echo "<table>
						<tr>
							<td>目前帳單數</td>
						</tr>
						<tr>
							<td>".$table2[0]['number']."</td>
						</tr>
					</table><br><br>";
				echo "<table>";
				echo "<captioin>商品銷售統計</caption>";
				echo "<tr><td>商品名稱</td><td>數量</td><td>金額</td></tr>";
				for($i=1;$i<=max(array_keys($item));$i++){
					if(isset($item[$i])){
						echo "<tr>";
						echo "<td>".$item[$i]['name']."</td>";
						echo "<td>".$item[$i]['qty']."</td>";
						echo "<td>".$item[$i]['amt']."</td>";
						echo "</tr>";
					}
					else{
					}					
				}
				echo "</table>
					</div>";
			}
			$conn->close();
		}
	}
	else if(isset($_POST['conttype'])&&$_POST['conttype']=='editpsw'){
		echo "<script>
				function checkpsw(form){
					if(document.getElementById('newpsw').value!=document.getElementById('newpsw2').value){
						alert('修改密碼需要輸入兩次相同的新密碼。');
					}
					else{
						form.submit();
					}
				}
			</script>";
		echo "<form method='post' action='EditPsw.php' name='form'>
				<table>
					<tr>
						<td>登入帳號</td>
						<td>".$_SESSION['ID']."<input type='hidden' name='id' value='".$_SESSION['ID']."'></td>
					</tr>
					<tr>
						<td>原始密碼</td>
						<td><input type='password' name='oldpsw'></td>
					</tr>
					<tr>
						<td>新密碼</td>
						<td><input type='password' id='newpsw' name='newpsw'></td>
					</tr>
					<tr>
						<td>重新輸入</td>
						<td><input type='password' id='newpsw2' name='newpsw2'></td>
					</tr>
					<tr>
						<td colspan='2'><input type='button' value='修改' onclick='checkpsw(this.form)'></td>
					</tr>
				</table>
			</form>";
	}
	else{
		echo "<h1>歡迎回來！</h1>";
	}
}
?>