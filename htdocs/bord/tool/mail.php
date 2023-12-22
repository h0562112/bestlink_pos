<?php
  $to ="=?utf-8?B?".base64_encode("游孟霖")."?="."<menglin@ctda.com.tw>"; //收件者
  $subject = "設備維修單與網路註冊單"; //信件標題

  //信件內容
  $msg = "smtp發信測試1";
  $filename='./1041118001.pdf';
  $boundary = uniqid( ""); // 產生分隔字串 
  if($filename){ 
        $mimeType = mime_content_type($filename); // 判斷檔案類型 
        if(!$mimeType)$mimeType ="application/unknown"; // 若判斷不出則設為未知 
        $fp = fopen($filename, "r"); // 開啟檔案 
        $read = fread($fp, filesize($filename)); // 取得檔案內容 
        fclose($fp); // 關閉檔案 
        $read = base64_encode($read);//使用base64編碼 
        $read = chunk_split($read);  //把檔案所轉成的長字串切開成多個小字串 
        $file = basename($filename); //傳回不包含路徑的檔案名稱(mail中會顯示的檔名) 

        // 附檔處理開始 
        $msg .= '--'.$boundary ."\r\n"; 
        // 設定附加檔案HEADER 
        $msg .= 'Content-type: '.$mimeType.'; name='.$file."\r\n"; 
        $msg .= 'Content-transfer-encoding: base64'."\r\n"; 
        $msg .= 'Content-disposition: attachment; filename='.$file."\r\n";
        // 加入附加檔案內容 
        $msg .= $read ."\r\n"; 
    }//處理附加檔案完畢 

  $headers = "From: "."=?utf-8?B?".base64_encode("青松大愛資訊組")."?="."<ctdainfo@ctda.com.tw>\r\n"; //寄件者
  $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
  
  if(mail("$to", "$subject", "$msg", "$headers"))
   echo "<script>alert('信件已經發送成功。');</script>";//寄信成功就會顯示的提示訊息
  else
   echo "<script>alert('信件發送失敗！');</script>";//寄信失敗顯示的錯誤訊息
?>