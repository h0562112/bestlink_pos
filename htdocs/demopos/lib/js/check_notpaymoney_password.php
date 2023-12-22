<?php
class DES
{
    /**
     * @var string $method 加解密方法，可通过 openssl_get_cipher_methods() 获得
     */
    protected $method;
 
    /**
     * @var string $key 加解密的密钥
     */
    protected $key;
 
    /**
     * @var string $output 输出格式 无、base64、hex
     */
    protected $output;
 
    /**
     * @var string $iv 加解密的向量
     */
    protected $iv;
 
    /**
     * @var string $options
     */
    protected $options;
 
    // output 的类型
    const OUTPUT_NULL = '';
    const OUTPUT_BASE64 = 'base64';
    const OUTPUT_HEX = 'hex';
 
 
    /**
     * DES constructor.
     * @param string $key
     * @param string $method
     *      ECB DES-ECB、DES-EDE3 （为 ECB 模式时，$iv 为空即可）
     *      CBC DES-CBC、DES-EDE3-CBC、DESX-CBC
     *      CFB DES-CFB8、DES-EDE3-CFB8
     *      CTR
     *      OFB
     *
     * @param string $output
     *      base64、hex
     *
     * @param string $iv
     * @param int $options
     */
    public function __construct($key, $method = 'DES-ECB', $output = '', $iv = '', $options = OPENSSL_RAW_DATA | OPENSSL_NO_PADDING)
    {
        $this->key = $key;
        $this->method = $method;
        $this->output = $output;
        $this->iv = $iv;
        $this->options = $options;
    }
 
    /**
     * 加密
     *
     * @param $str
     * @return string
     */
    public function encrypt($str)
    {
        $str = $this->pkcsPadding($str, 8);
        $sign = openssl_encrypt($str, $this->method, $this->key, $this->options, $this->iv);
 
        if ($this->output == self::OUTPUT_BASE64) {
            $sign = base64_encode($sign);
        } else if ($this->output == self::OUTPUT_HEX) {
            $sign = bin2hex($sign);
        }
 
        return $sign;
    }
 
    /**
     * 解密
     *
     * @param $encrypted
     * @return string
     */
    public function decrypt($encrypted)
    {
        if ($this->output == self::OUTPUT_BASE64) {
            $encrypted = base64_decode($encrypted);
        } else if ($this->output == self::OUTPUT_HEX) {
            $encrypted = hex2bin($encrypted);
        }
 
        $sign = @openssl_decrypt($encrypted, $this->method, $this->key, $this->options, $this->iv);
        $sign = $this->unPkcsPadding($sign);
        $sign = rtrim($sign);
        return $sign;
    }
 
    /**
     * 填充
     *
     * @param $str
     * @param $blocksize
     * @return string
     */
    private function pkcsPadding($str, $blocksize)
    {
        $pad = $blocksize - (strlen($str) % $blocksize);
        return $str . str_repeat(chr($pad), $pad);
    }
 
    /**
     * 去填充
     *
     * @param $str
     * @return string
     */
    private function unPkcsPadding($str)
    {
        $pad = ord($str{strlen($str) - 1});
        if ($pad > strlen($str)) {
            return false;
        }
        return substr($str, 0, -1 * $pad);
    }
}

if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machine']])){
		$invmachine=$dbmapping['map'][$_POST['machine']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
$y=date('Y');
$m=date('m');
if(strlen($m)<2){
	$m='0'.$m;
}
include_once '../../../tool/dbTool.inc.php';
if(file_exists('../../../database/initsetting.ini')){
	$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
	date_default_timezone_set($initsetting['init']['settime']);
	
	if(isset($initsetting['init']['accounting'])&&$initsetting['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
		$content=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
	}
	else{//帳務以主機為主體計算
		$content=parse_ini_file('../../../database/timem1.ini',true);
	}
	$conn=sqlconnect('../../../database/sale','SALES_'.$y.$m.'.db','','','','sqlite');

	if(isset($initsetting['pospa'])){
		$key='tableplus';
		$iv='0424732003';
		$des = new DES($key, 'DES-CBC', DES::OUTPUT_BASE64, $iv);
		$message=$des->decrypt($initsetting['pospa']['password']);//2022/6/10 密碼+結束日期
		if(isset($initsetting['pospa']['pstartdate'])&&isset($initsetting['pospa']['penddate'])&&intval($initsetting['pospa']['pstartdate'])<=intval(date('Ymd'))&&intval($initsetting['pospa']['penddate'])>=intval(date('Ymd'))&&substr($message,8)==$initsetting['pospa']['penddate']){//檢查密碼是否於允許時間//2022/6/10 密碼後面再加上時效(結束日期)，以便解密後驗證時間是否有經過手動更改
			if($_POST['checkenterpassword']==substr($message,0,8)){
				$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['machine'].'","'.$content['time']['bizdate'].'"," ","success","system","臨時密碼驗證成功","9","9","99","'.$content['time']['zcounter'].'","'.date('YmdHis').'")';
				sqlnoresponse($conn,$sql,'sqlite');

				echo 'success';
			}
			else{//密碼錯誤
				$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['machine'].'","'.$content['time']['bizdate'].'"," ","fail","system","臨時密碼驗證失敗","9","9","99","'.$content['time']['zcounter'].'","'.date('YmdHis').'")';
				sqlnoresponse($conn,$sql,'sqlite');

				echo 'fail';
			}
		}
		else{//密碼過期
			$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['machine'].'","'.$content['time']['bizdate'].'"," ","timeout","system","時效過期或人為錯誤修改設定值","9","9","99","'.$content['time']['zcounter'].'","'.date('YmdHis').'")';
			sqlnoresponse($conn,$sql,'sqlite');

			echo 'timeout';
		}
	}
	else{//沒有新的公告區塊參數
		$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['machine'].'","'.$content['time']['bizdate'].'"," ","fail","system","臨時密碼不存在","9","9","99","'.$content['time']['zcounter'].'","'.date('YmdHis').'")';
		sqlnoresponse($conn,$sql,'sqlite');

		echo 'fail';
	}

	sqlclose($conn,'sqlite');
}
else{//不存在initsetting.ini
	date_default_timezone_set("Asia/Taipei");
	$conn=sqlconnect('../../../database/sale','SALES_'.$y.$m.'.db','','','','sqlite');
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['machine'].'","'.date('Ymd').'"," ","fail","system","initsetting不存在","9","9","99","0","'.date('YmdHis').'")';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');

	echo 'fail';
}
?>