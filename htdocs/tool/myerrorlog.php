<?php
function myErrorHandler($errno, $errstr, $errfile, $errline){
	date_default_timezone_set("Asia/Taipei");
	//echo __DIR__;
	if(file_exists(__DIR__."/errorcode/error.ini")){
		$errormessage=parse_ini_file(__DIR__."/errorcode/error.ini",true);
	}
	else{
	}
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
    }

    switch ($errno) {
		case E_ERROR:
			echo "[E_ERROR]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_WARNING:
			echo "[E_WARNING] ".$errstr."[".$errfile." in line ".$errline."]<br>";
			if(isset($errormessage["E_WARNING"])){
				$errortag=0;
				foreach($errormessage["E_WARNING"] as $token=>$message){
					if(preg_match("/(".$token.")/",$errstr)){
						echo PHP_EOL."ERROR HINT: ".$message."<br>".PHP_EOL;
						$errortag=1;
					}
					else{
					}
				}
				if($errortag){
				}
				else{
					echo PHP_EOL."ERROR HINT: 無對應錯誤提示。<br>".PHP_EOL;
				}
			}
			else{
			}
			break;
		
		case E_PARSE:
			echo "[E_PARSE]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_NOTICE:
			echo "[E_NOTICE]".$errstr."[".$errfile." in line ".$errline."]<br>";
			if(isset($errormessage["E_NOTICE"])){
				$errortag=0;
				foreach($errormessage["E_NOTICE"] as $token=>$message){
					if(preg_match("/(".$token.")/",$errstr)){
						echo PHP_EOL."ERROR HINT: ".$message."<br>".PHP_EOL;
						$errortag=1;
					}
					else{
					}
				}
				if($errortag){
				}
				else{
					echo PHP_EOL."ERROR HINT: 無對應錯誤提示。<br>".PHP_EOL;
				}
			}
			else{
			}
			break;

		case E_CORE_ERROR:
			echo "[E_CORE_ERROR]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_CORE_WARNING:
			echo "[E_CORE_WARNING]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_COMPILE_ERROR:
			echo "[E_COMPILE_ERROR]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_COMPILE_WARNING:
			echo "[E_COMPILE_WARNING]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_USER_ERROR:
			echo "[E_USER_ERROR]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_USER_WARNING:
			echo "[E_USER_WARNING]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_USER_NOTICE:
			echo "[E_USER_NOTICE]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_STRICT:
			echo "[E_STRICT]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_RECOVERABLE_ERROR:
			echo "[E_RECOVERABLE_ERROR]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_DEPRECATED:
			echo "[E_DEPRECATED]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		case E_USER_DEPRECATED:
			echo "[E_USER_DEPRECATED]".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;

		default:
			echo date("Y/m/d H:i:s")." ---- [".$errno."] ".$errstr."[".$errfile." in line ".$errline."]<br>";
			break;
    }

    // Don"t execute PHP internal error handler
    return true;
}
$old_error_handler = set_error_handler("myErrorHandler");
?>