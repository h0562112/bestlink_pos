<?php
session_start();
include_once "../../../tool/dbTool.inc.php";
include_once "../../../tool/date.inc.php";
$start=preg_replace("/-/","",$_POST["startdate"]);
$end=preg_replace("/-/","",$_POST["enddate"]);
if(isset($_POST["startdate"])){
	if(is_dir("../../../doc/")){
	}
	else{
		mkdir("../../../doc");
	}
	$filepath="../doc/".date("Ymd");
	if(is_dir("../../".$filepath."/")){
	}
	else{
		mkdir("../../".$filepath);
	}
	$file=$filepath."/".$_SESSION["company"]."-".date("YmdHis").".csv";
	$f=fopen("../../".$file,"w");
	$cover=array();//�Ȧs����ק���B���(�ק����i������������P)
	if($_SESSION["DB"]==""&&file_exists("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["dbname"]."/Cover.db")){
		$Cconn=sqlconnect("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["dbname"],"Cover.db","","","","sqlite");
		/*if(file_exists("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["dbname"]."/otherpay.ini")){
			$otherpay=parse_ini_file("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["dbname"]."/otherpay.ini",true);
		}
		else{
		}*/
	}
	else if(file_exists("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["DB"]."/Cover.db")){
		$Cconn=sqlconnect("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["DB"],"Cover.db","","","","sqlite");
		/*if(file_exists("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["DB"]."/otherpay.ini")){
			$otherpay=parse_ini_file("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["DB"]."/otherpay.ini",true);
		}
		else{
		}*/
	}
	else{
	}
	if(file_exists("../../methodmap.ini")){
		$intellamap=parse_ini_file("../../methodmap.ini",true);
	}
	else{
	}
	if(!isset($Cconn)||!$Cconn){
	}
	else{
		$checkintella="PRAGMA table_info(list)";
		$allcolumn=sqlquery($Cconn,$checkintella,"sqlite");
		if(!in_array("intella",array_column($allcolumn,"name"))){
			//$sql="SELECT bizdate,consecnumber,SUM(salesttlamt + tax1 + tax9) AS amt,SUM(tax2) AS tax2,SUM(tax3) AS tax3,SUM(tax4) AS tax4 FROM list WHERE bizdate BETWEEN '".$start."' AND '".$end."' AND state=1 GROUP BY bizdate,consecnumber";
			$tempcover="";
		}
		else{
			$sql="SELECT bizdate,consecnumber,SUM(salesttlamt + tax1 + tax9) AS amt,SUM(tax2) AS tax2,SUM(tax3) AS tax3,SUM(tax4) AS tax4 FROM list WHERE bizdate BETWEEN '".$start."' AND '".$end."' AND state=1 GROUP BY bizdate,consecnumber";
			$tempcover=sqlquery($Cconn,$sql,"sqlite");
		}
		if(sizeof($tempcover)>0&&isset($tempcover[0]["bizdate"])){
			foreach($tempcover as $tc){
				$cover[$tc["bizdate"]][intval($tc["consecnumber"])]["tax2"]=$tc["tax2"];
				$cover[$tc["bizdate"]][intval($tc["consecnumber"])]["tax3"]=$tc["tax3"];
				$cover[$tc["bizdate"]][intval($tc["consecnumber"])]["tax4"]=$tc["tax4"];
				$cover[$tc["bizdate"]][intval($tc["consecnumber"])]["intella"]=$tc["intella"];
			}
		}
		else{
		}
	}
	$totalMon=getMon($_POST["startdate"],$_POST["enddate"]);
	$complete=0;
	$summoney=0;
	$list=array();
	for($m=0;$m<=$totalMon;$m++){
		if($_SESSION["DB"]==""){
			$conn=sqlconnect("../../../ourpos/".$_POST["company"]."/".$_POST["dbname"],"SALES_".date('Ym',strtotime(substr($start,0,6)."01 +".$m." month")).".db","","","","sqlite");
		}
		else{
			$conn=sqlconnect("../../../ourpos/".$_SESSION["company"]."/".$_SESSION["DB"],"SALES_".date('Ym',strtotime(substr($start,0,6)."01 +".$m." month")).".db","","","","sqlite");
		}
		if(!$conn){
			echo "��Ʈw�|���W�Ǹ�ơC";
		}
		else{
			$sql="SELECT name FROM sqlite_master WHERE type='table' AND name='CST011'";
			$res=sqlquery($conn,$sql,"sqlite");
			if(isset($res[0]["name"])){
				$checkintella="PRAGMA table_info(CST011)";
				$allcolumn=sqlquery($conn,$checkintella,"sqlite");
				if(!in_array("intella",array_column($allcolumn,"name"))){
					//$sql="SELECT BIZDATE,INVOICENUMBER,CONSECNUMBER,CLKCODE,(SALESTTLAMT+TAX1) AS SALESTTLAMT,TAX2,TAX3,TAX4,NBCHKDATE AS VOIDTIME,NBCHKTIME AS VOIDPERSONCODE,NBCHKNUMBER AS VOIDTAG,ZCOUNTER,CREATEDATETIME,UPDATEDATETIME,REMARKS FROM CST011 WHERE BIZDATE BETWEEN '".$start."' AND '".$end."' ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC";
					$first="";
				}
				else{
					$sql="SELECT BIZDATE,INVOICENUMBER,CONSECNUMBER,CLKCODE,(SALESTTLAMT+TAX1) AS SALESTTLAMT,TAX2,TAX3,TAX4,NBCHKDATE AS VOIDTIME,NBCHKTIME AS VOIDPERSONCODE,NBCHKNUMBER AS VOIDTAG,ZCOUNTER,CREATEDATETIME,UPDATEDATETIME,REMARKS,intella FROM CST011 WHERE BIZDATE BETWEEN '".$start."' AND '".$end."' AND intella IS NOT NULL AND intella!='0' ORDER BY BIZDATE ASC,CREATEDATETIME ASC,ZCOUNTER ASC,CONSECNUMBER ASC";
					$first=sqlquery($conn,$sql,"sqlite");
				}
				if(sizeof($first)==0){
				}
				else{
					foreach($first as $item){
						//if(isset($list)&&strlen($list)>0){
							$list[]=$item["BIZDATE"].",".$item["CONSECNUMBER"].",".$item["SALESTTLAMT"].",";
							if(isset($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax2"])){
								$list[sizeof($list)-1] .= $cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax2"];
							}
							else{
								$list[sizeof($list)-1] .= $item["TAX2"];
							}
							$list[sizeof($list)-1] .= ",";
							if(isset($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax3"])){
								$list[sizeof($list)-1] .= $cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax3"];
							}
							else{
								$list[sizeof($list)-1] .= $item["TAX3"];
							}
							$list[sizeof($list)-1] .= ",";
							if(isset($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax4"])){
								$list[sizeof($list)-1] .= $cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax4"];
							}
							else{
								$list[sizeof($list)-1] .= $item["TAX4"];
							}
							$list[sizeof($list)-1] .= ",";
							$intelladata=preg_split("/:/",$item["intella"]);
							$list[sizeof($list)-1] .= "'".$intelladata[0].",";
							if(isset($intellamap)&&isset($intellamap["map"][$intelladata[1]])){
								$list[sizeof($list)-1] .= iconv("utf-8","big5",$intellamap["map"][$intelladata[1]]." ".$intelladata[1]);
							}
							else{
								$list[sizeof($list)-1] .= $intelladata[1];
							}
							$list[sizeof($list)-1] .= ",".$intelladata[2];
							$summoney=floatval($summoney)+floatval($intelladata[2]);

							$list[sizeof($list)-1] .= ",'".$item["UPDATEDATETIME"];
							if(strlen($item["VOIDTIME"])>0){
								$list[sizeof($list)-1] .= ",'".$item["VOIDTIME"];
							}
							else{
								$list[sizeof($list)-1] .= ",";
							}
							if(strlen($item["VOIDTAG"])>1){
								$list[sizeof($list)-1] .= ",".iconv("utf-8","big5",$item["VOIDTAG"]);
							}
							else if(strlen($item["VOIDTAG"])==1){
								if(preg_match("/-/",$item["REMARKS"])){
									$voidtag=preg_split("/-/",$item["REMARKS"]);
									if($voidtag[0]=="editsale"){
										$list[sizeof($list)-1] .= ",�ק�b��";
									}
									else{
										$list[sizeof($list)-1] .= ",�@�o/���P";
									}
								}
								else{
									if($item["REMARKS"]=="tempvoid"){
										$list[sizeof($list)-1] .= ",�@�o�ȵ���";
									}
									else{
										$list[sizeof($list)-1] .= ",�@�o/���P";
									}
								}
							}
							else{
								$list[sizeof($list)-1] .= ",";
							}
						/*}
						else{
							$list="<table class='table' style='border-bottom:1px solid #000000;margin-top:10px;'><tr><td colspan='11' style='padding:5px;'><h2>".$item["BIZDATE"]."</h2></td></tr><tr><td style='text-align:center;'>��~��</td><td>�b�渹</td><td style='text-align:right;'>�`���B</td><td style='text-align:right;'>�{��</td><td style='text-align:right;'>�H�Υd</td><td style='text-align:right;'>��L</td><td colspan='3' style='text-align:center;'>�^�S��</td><td style='text-align:center;'>���b�ɶ�</td><td>�@�o/���P�ɶ�</td><td>�Ƶ�</td></tr><tr><td colspan='6'></td><td style='text-align:center;'>�����</td><td style='text-align:center;'>�I�ڤ覡</td><td style='text-align:right;'>�I�ڪ��B</td><td colspan='3'></td></tr><tr";
							if(strlen($item["VOIDTAG"])>=1){
								$list=$list." style='color:#ff0000;'";
							}
							else{
							}
							$list=$list."><td>".$item["BIZDATE"]."</td><td>".$item["CONSECNUMBER"]."</td><td style='text-align:right;'>".number_format($item["SALESTTLAMT"])."</td><td style='text-align:right;'>";
							if(isset($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax2"])){
								$list=$list.number_format($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax2"]);
							}
							else{
								$list=$list.number_format($item["TAX2"]);
							}
							$list=$list."</td><td style='text-align:right;'>";
							if(isset($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax3"])){
								$list=$list.number_format($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax3"]);
							}
							else{
								$list=$list.number_format($item["TAX3"]);
							}
							$list=$list."</td><td style='text-align:right;'>";
							if(isset($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax4"])){
								$list=$list.number_format($cover[$item["BIZDATE"]][intval($item["CONSECNUMBER"])]["tax4"]);
							}
							else{
								$list=$list.number_format($item["TAX4"]);
							}
							$list=$list."</td>";
							$intelladata=preg_split("/:/",$item["intella"]);
							$list=$list."<td>".$intelladata[0]."</td><td style='text-align:center;'>";
							if(isset($intellamap)&&isset($intellamap["map"][$intelladata[1]])){
								$list=$list.$intellamap["map"][$intelladata[1]]."<br><span style='font-weight:bold;'>".$intelladata[1]."</span>";
							}
							else{
								$list=$list.$intelladata[1];
							}
							$list=$list."</td><td style='text-align:right;'>".number_format($intelladata[2])."</td>";

							$list=$list."<td>".$item["UPDATEDATETIME"]."</td><td>".$item["VOIDTIME"]."</td>";
							if(strlen($item["VOIDTAG"])>1){
								$list=$list."<td>".$item["VOIDTAG"]."</td>";
							}
							else if(strlen($item["VOIDTAG"])==1){
								if(preg_match("/-/",$item["REMARKS"])){
									$voidtag=preg_split("/-/",$item["REMARKS"]);
									if($voidtag[0]=="editsale"){
										$list=$list."<td>�ק�b��</td>";
									}
									else{
										$list=$list."<td>�@�o/���P</td>";
									}
								}
								else{
									if($item["REMARKS"]=="tempvoid"){
										$list=$list."<td>�@�o�ȵ���</td>";
									}
									else{
										$list=$list."<td>�@�o/���P</td>";
									}
								}
							}
							else{
								$list=$list."<td></td>";
							}
							$list=$list."</tr>";
						}*/
						/*if(isset($list["allqty"])){
							$list["allqty"]=intval($list["allqty"])+1;
							$list["allamt"]=floatval($list["allamt"])+floatval($item["SALESTTLAMT"]);
						}
						else{
							$list["allqty"]=1;
							$list["allamt"]=floatval($item["SALESTTLAMT"]);
						}*/
					}
				}
			}
			else{
				$complete++;
			}
		}
		sqlclose($conn,"sqlite");
	}
	
	if($complete>=($totalMon+1)){
		fwrite($f,"��Ʈw������W�ǡC".PHP_EOL);
	}
	else{
		if($complete>0){
			fwrite($f,"���������Ʈw������W�ǡC".PHP_EOL);
		}
		else{
		}
		if(sizeof($list)==0){
			fwrite($f,"�j�M�ɶ��϶��õL�@�o��ơC".PHP_EOL);
		}
		else{
			fwrite($f,"��~��,�b�渹,�`���B,�{��,�H�Υd,��L,,�^�S��,,���b�ɶ�,�@�o/���P�ɶ�,�Ƶ�".PHP_EOL);
			fwrite($f,",,,,,,,�X�p���B,".$summoney.PHP_EOL);
			fwrite($f,",,,,,,�����,�I�ڤ覡,�I�ڪ��B".PHP_EOL);
			foreach($list as $l){
				fwrite($f,$l.PHP_EOL);
			}
			/*foreach($list as $l){
				echo $l;
				echo "</table>";
			}*/
		}
	}
	fclose($f);
	echo $file;
}
else{
}
?>