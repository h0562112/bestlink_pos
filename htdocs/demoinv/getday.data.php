<?php include_once '../tool/dbTool.inc.php'; $conn=sqlconnect('../database','menu.db','','','','sqlite'); $date=preg_replace('/-/','',$_POST['searchdate']); $sql='SELECT COUNT(*) AS num,SUM(totalamount) AS total FROM invlist WHERE createdate="'.$date.'"'; $alldata=sqlquery($conn,$sql,'sqlite'); $sql='SELECT COUNT(*) AS num,SUM(totalamount) AS total FROM invlist WHERE state=0 AND createdate="'.$date.'"'; $voiddata=sqlquery($conn,$sql,'sqlite'); sqlclose($conn,'sqlite'); ?> <table> <tr> <td>已開張數</td> <td style='width:500px;text-align:right;'><?php echo $alldata[0]['num']; ?>張</td> </tr> <tr> <td>作廢張數</td> <td style='width:500px;text-align:right;'><?php echo $voiddata[0]['num']; ?>張</td> </tr> <tr> <td>已開金額</td> <td style='width:500px;text-align:right;'><?php if($alldata[0]['total']==null)echo '0';else echo $alldata[0]['total']; ?>元</td> </tr> <tr> <td>作廢金額</td> <td style='width:500px;text-align:right;'><?php if($voiddata[0]['total']==null)echo '0';else echo $voiddata[0]['total']; ?>元</td> </tr> </table> <input type='button' style='height:150px;width:20%;float:right;margin:200px 10px 10px 10px;' id='research' value='返回'>