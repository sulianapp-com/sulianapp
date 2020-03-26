<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/7/7
 * Time: 16:21
 */

namespace app\common\modules\yop\sdk\Util;

abstract class BlowfishEncrypter{
    /**
     * 算法,另外还有192和256两种长度
     */
    const CIPHER = MCRYPT_BLOWFISH;
    /**
     * 模式
     */
    const MODE = MCRYPT_MODE_CFB;

    /**
     * 加密
     * @param string $str	需加密的字符串
     * @param string $key	密钥
     * @return type
     */
    static public function encode( $str, $key){
        $md5Key = md5($key);
        return base64_encode(mcrypt_encrypt(self::CIPHER, substr($md5Key,0,16), $str, self::MODE, substr($md5Key,0,8)));
    }

    /**
     * 解密
     * @param type $str
     * @param type $key
     * @return type
     */
    static public function decode( $str, $key ){
        $md5Key = md5($key);

        return mcrypt_decrypt(self::CIPHER, substr($md5Key,0,16),base64_decode($str), self::MODE, substr($md5Key,0,8));
    }
}


