<?php
$target_dir = "../print/noread/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
	 // Allow certain file formats
	if($imageFileType != "csv" ) {
	  echo "非常抱歉，只允許 CSV 類型檔案上傳。";
	  $uploadOk = 0;
	}
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "非常抱歉，檔案並沒有成功上傳。";
// if everything is ok, try to upload file
} 
else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "批次帳單檔案 ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " 已經上傳成功。";
  } 
  else {
    echo "非常抱歉，檔案上傳出了一點問題！";
  }
}
?>