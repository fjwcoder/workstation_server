<?php
namespace aes;

class AES
{
    /**
     * AES加密、解密类
     * 用法：
     * <pre>
     * // 实例化类
     * // 参数$_bit：格式，支持256、192、128，默认为128字节的
     * // 参数$_type：加密/解密方式，支持cfb、cbc、nofb、ofb、stream、ecb，默认为ecb
     * // 参数$_key：密钥，默认为 _Mikkle_AES_Key_
     * $tcaes = new TCAES();
     * $string = 'laohu';
     * // 加密
     * $encodeString = $tcaes->encode($string);
     * // 解密
     * $decodeString = $tcaes->decode($encodeString);
     * </pre>
     */
    private $_bit = MCRYPT_RIJNDAEL_256;
    private $_type = MCRYPT_MODE_CBC;
    private $_key = '_MAMI_tianshi_RT'; // 密钥 必须16位 24位
    private $_use_base64 = true;
    private $_iv_size = null;
    private $_iv = null;

    /**
     * @param string $_key 密钥
     * @param int $_bit 默认使用128字节
     * @param string $_type 加密解密方式
     * @param boolean $_use_base64 默认使用base64二次加密
     */
    public function __construct($_key = '', $_bit = 128, $_type = 'ecb', $_use_base64 = true){
        // 加密字节
        if(192 === $_bit){
            $this->_bit = MCRYPT_RIJNDAEL_192;
        }elseif(128 === $_bit){
            $this->_bit = MCRYPT_RIJNDAEL_128;
        }else{
            $this->_bit = MCRYPT_RIJNDAEL_256;
        }
        // 加密方法
        if('cfb' === $_type){
            $this->_type = MCRYPT_MODE_CFB;
        }elseif('cbc' === $_type){
            $this->_type = MCRYPT_MODE_CBC;
        }elseif('nofb' === $_type){
            $this->_type = MCRYPT_MODE_NOFB;
        }elseif('ofb' === $_type){
            $this->_type = MCRYPT_MODE_OFB;
        }elseif('stream' === $_type){
            $this->_type = MCRYPT_MODE_STREAM;
        }else{
            $this->_type = MCRYPT_MODE_ECB;
        }
        // 密钥
        if(!empty($_key)){
            $this->_key = $_key;
        }
        // 是否使用base64
        $this->_use_base64 = $_use_base64;

        $this->_iv_size = mcrypt_get_iv_size($this->_bit, $this->_type);
        $this->_iv = mcrypt_create_iv($this->_iv_size, MCRYPT_RAND);
    }

    /**
     * 加密
     * @param string $string 待加密字符串
     * @return string
     */
    // 19.9.2 by fqm 修改加密方式
    public function encode($string){

        $size = mcrypt_get_block_size($this->_bit, MCRYPT_MODE_ECB);
 
        $input = Aes::pkcs5_pad($string, $size);

        $td = mcrypt_module_open($this->_bit, '', MCRYPT_MODE_ECB, '');

        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        mcrypt_generic_init($td, $this->_key, $iv);

        $data = mcrypt_generic($td, $input);

        mcrypt_generic_deinit($td);

        mcrypt_module_close($td);

        $data = base64_encode($data);

        return $data;

        //      if(MCRYPT_MODE_ECB === $this->_type){
    //          $encodeString = mcrypt_encrypt($this->_bit, $this->_key, $string, $this->_type);
    //      }else{
    //          $encodeString = mcrypt_encrypt($this->_bit, $this->_key, $string, $this->_type, $this->_iv);
    //      }
    //      if($this->_use_base64){
    //          $encodeString = base64_encode($encodeString);
    //      }
    //      return $encodeString;
    }
    
 
     /**
      * 解密
      * @param string $string 待解密字符串
      * @return string
      */
      // 19.9.2 by fqm 修改解密方式
     public function decode($string){

        $decrypted= mcrypt_decrypt($this->_bit, $this->_key,base64_decode($string),MCRYPT_MODE_ECB);

        $dec_s = strlen($decrypted);

        $padding = ord($decrypted[$dec_s-1]);

        $decrypted = substr($decrypted, 0, -$padding);

        return $decrypted;


        //  if($this->_use_base64){
        //      $string = base64_decode($string);
        //  }
        //  if(MCRYPT_MODE_ECB === $this->_type){
        //      $decodeString = mcrypt_decrypt($this->_bit, $this->_key, $string, $this->_type);
        //  }else{
        //      $decodeString = mcrypt_decrypt($this->_bit, $this->_key, $string, $this->_type, $this->_iv);
        //  }
        //  return $decodeString;
     }


    private static function pkcs5_pad ($text, $blocksize) {
 
        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);

    }

     /**
      * 获取秘钥
      */
      public function key(){
          return $this->_key;
      }
}
