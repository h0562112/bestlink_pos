<?php
/**
 * openssl 实现的 DES 加密类，支持各种 PHP 版本
 */
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

include_once './sample.php';

$key='tableplus';
$iv='0424732003';
//$key={102, 16, 93, 156, 78, 4, 218, 32};
//$iv={55, 103, 246, 79, 36, 99, 167, 3};
//echo $key.'<br>';
//echo $iv.'<br>';

// DES CBC 加解密
$des = new DES($key, 'DES-CBC', DES::OUTPUT_BASE64, $iv);
echo '<div style="width:100%;">';
srand(date('YmdHis'));
echo $base64Sign = $des->encrypt(str_pad(rand(0,99999999),8,'0',STR_PAD_LEFT));
echo '</div>';
echo "\n";
echo '<div style="width:100%;">';
echo $des->decrypt($base64Sign);
echo '</div>';
echo "\n";
?>