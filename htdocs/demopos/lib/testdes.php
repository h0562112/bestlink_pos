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

$key=chr(102).chr(16).chr(93).chr(156).chr(78).chr(4).chr(218).chr(32);
$iv=chr(55).chr(103).chr(246).chr(79).chr(36).chr(99).chr(167).chr(3);
//$key={102, 16, 93, 156, 78, 4, 218, 32};
//$iv={55, 103, 246, 79, 36, 99, 167, 3};
//echo $key.'<br>';
//echo $iv.'<br>';

$xml="<?xml version='1.0' encoding='UTF-8'?>
<Outp xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>
    <Outps>
        <Tradeno>AA00001</Tradeno>
        <Ctmno>Y00001</Ctmno>
        <Tradedate>20220517</Tradedate>
        <Recpno>AA00000001</Recpno>
        <Addresss></Addresss>
        <Taxmark>2</Taxmark>
        <Amount>1500</Amount>
        <Tax>0</Tax>
        <Salesno>G00001</Salesno>
        <Stno>A01</Stno>
        <Moneypay>0</Moneypay>
        <Discount>0</Discount>
        <Tamount>1500</Tamount>
        <Remark></Remark>
        <Monthsec>202205</Monthsec>
        <Sendtype></Sendtype>
        <Tradetime>08:00</Tradetime>
        <Schno>02</Schno>
        <Machno></Machno>
        <Idno>12345678</Idno>
        <Cashpay>0</Cashpay>
        <Retcash>0</Retcash>
        <Crdno></Crdno>
        <Crdpay>0</Crdpay>
        <Billpay>0</Billpay>
        <Tkindno>網路訂單</Tkindno>
        <Prepay>0</Prepay>
        <Imaxno>2</Imaxno>
        <Deskno></Deskno>
        <Useno></Useno>
        <Bmaxno>0</Bmaxno>
        <Othpay1>0</Othpay1>
        <Othpay2>0</Othpay2>
        <Manqty>0</Manqty>
        <Outmark>0</Outmark>
        <Taxrate>5</Taxrate>
        <Othpay3>0</Othpay3>
        <Othpay4>0</Othpay4>
        <Othpay5>0</Othpay5>
        <Othpay6>0</Othpay6>
        <Atel>0226795661</Atel>
        <Aname>黃先生</Aname>
    </Outps>
    <OutpItem>
        <Line_no>1.0</Line_no>
        <Goodno>U0001</Goodno>
        <Saleprice>30</Saleprice>
        <Tradeqty>10</Tradeqty>
        <Cost>50</Cost>
        <Saleprice2>20</Saleprice2>
        <Rem></Rem>
        <Pkno>1</Pkno>
        <Goodname2>奶茶</Goodname2>
        <Oaddpri>10.0000</Oaddpri>
        <Omemo>大杯,冷飲</Omemo>
        <Mkdname>經典飲品</Mkdname>
    </OutpItem>
    <OutpItem>
        <Line_no>2.0</Line_no>
        <Goodno>U0002</Goodno>
        <Saleprice>25</Saleprice>
        <Tradeqty>10</Tradeqty>
        <Cost>50</Cost>
        <Saleprice2>20</Saleprice2>
        <Rem></Rem>
        <Pkno>2</Pkno>
        <Goodname2>德式香腸</Goodname2>
        <Oaddpri>5.0000</Oaddpri>
        <Omemo>茄汁肉醬_Bolognese</Omemo>
        <Mkdname>愜意點心</Mkdname>
    </OutpItem>
</Outp>";

// DES CBC 加解密
$des = new DES($key, 'DES-CBC', DES::OUTPUT_BASE64, $iv);
echo '<div style="width:100%;">';
echo $base64Sign = $des->encrypt($xmlstr);
echo '</div>';
echo "\n";
echo '<div style="width:100%;">';
echo $des->decrypt('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');
echo '</div>';
echo "\n";

// DES ECB 加解密
$des = new DES($key, 'DES-ECB', DES::OUTPUT_BASE64, $iv);
echo '<div style="width:100%;">';
echo $base64Sign = $des->encrypt($xmlstr);
echo '</div>';
echo "\n";
//echo '<div style="width:100%;">';
//echo $des->decrypt($base64Sign);
//echo '</div>';
echo "\n";
?>