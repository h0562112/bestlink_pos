<?php
function myMail($type,$title,$sendfile,$receive_name,$receive_address,$message){
  //設定
  $to = "=?utf-8?B?".base64_encode($receive_name)."?=<".$receive_address.">";
  $from = "=?utf-8?B?".base64_encode("快客碼科技股份有限公司")."?=<quickcode.tw@gmail.com>";	
  $format = "html";
  $subject = "=?utf-8?B?".base64_encode($title)."?=";
  /*if($type=="mac"){
	$subject = "=?utf-8?B?".base64_encode("[NEW]".$title_name."網路註冊單申請")."?=";
  }
  else if($type=="repair"){
	$subject = "=?utf-8?B?".base64_encode("[NEW]".$title_name."資訊設備維修單申請")."?=";
  }*/
  if($type==1){//網路註冊申請、資訊設備維修單申請
	  $message="申請單";
  }
  else if($type==2){//會議室預借申請
	  $temp=preg_split("/;/",$message);
	  //echo sizeof($temp);
	  $date=preg_split("/-/",$temp[2]);
	  $message="申請者：".$temp[0]."<br>會議室編號：".$temp[1]."<br>會議時間：".$temp[2]."<br>開會時段：".$temp[3]."-".$temp[4]."<br>會議主題：".$temp[5]."<br>"."請盡快將審核結果輸入系統！<br><a href='http://web.ctda.com.tw/system/meet/BorrowMeet.php?MeetNo=".$temp[1]."&usedDateYear=".$date[0]."&usedDateMonth=".$date[1]."&usedDateDay=".$date[2]."'>會議室系統</a>";
  }
  //$message = "申請單";
  $mime_boundary = md5(uniqid(mt_rand(), TRUE));
	//郵件標頭
  $header  = "From: ".$from."\r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-Type: multipart/mixed; boundary=". $mime_boundary . "\r\n";
	
  //建立郵件內容
  $content  = "This is a multi-part message in MIME format.\r\n";
  $content .= "--$mime_boundary\r\n";
  $content .= "Content-Type: text/$format; charset=utf-8\r\n";
  $content .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
  $content .= "$message\r\n";
  $content .= "--$mime_boundary\r\n";		
	
  //附加檔案處理程序
  $filename=$sendfile;
  if ($filename != "")
  {
    $file = $filename;
    $file_name = basename($filename);
    $content_type = mime_content_type($filename);
    $fp = fopen($file, "rb");
    $data = fread($fp, filesize($file));
    $data = chunk_split(base64_encode($data));
    $content .= "Content-Type: $content_type; name=$file_name\r\n"; 
    $content .= "Content-Disposition: attachment; filename=$file_name\r\n";		
    $content .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $content .= "$data\r\n";
    $content .= "--$mime_boundary--\r\n";
  } 

  //一切無誤時傳送郵件
  if(mail($to, $subject, $content, $header))
   ;//echo "<script>alert('信件已經發送成功。');</script>";//寄信成功就會顯示的提示訊息
  else
   echo "<script>alert('部分功能錯誤！');</script>";//寄信失敗顯示的錯誤訊息
}
?>