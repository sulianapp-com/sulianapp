<?php

namespace app\backend\modules\setting\services;
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/7/8
 * Time: 14:33
 */
class RsaServices {

    private static $PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----MIICXgIBAAKBgQCoZZ8iUBprOIc0kGckr5ax6/Fd9IKKMc/XHayKEAvqpS0oz0b1ojEkpkdZBk0OWNhp73YNV+YLKBwwxOwb3u3hl8nBLoG/RilEbBMdCf55cUzNsfn/XF5CiLr/aci/OHuTe6ULvXs280T5M+nUh3iKdiT6z9XrFbH69C+xFoNInwIDAQABAoGAe+ape7msdo+VC5vkCB4ZprePVC3/jmawIfr3ZG4CFpeJ7qjz8O9xcSHXBS2ZrKC6Otex6Idv/213sHpzrt4L7+rSrgMOauWNjSVjr4T4Z168uvsnNocn+3GWfzbBPQj3PhjE64R/MkWDvuq2UK945WYtqFaC6LT1mJAXhjxqpiECQQDYGWYbCsUgQS0LnDzReyotkb9Eyr5UGlI8Nzn3PvwwkIS3N3yUsm2t3UokOw02DlhkC4f1aT097fM1w0FruSNNAkEAx31taitIGwgJg+yPmvwTs8AENm0wxi/V6loEXPBPxX2R4NjSG+ExYzA7/daDq//McKsX0EcYcsFN0E3HwSANmwJBAJUXGOHpUU1Kiihrd25TWissVdjBRATEUB4pP/2738QlwNqjFnmEjLUaak+KyjeUOBl19ywymkUCyPw7pQQMLDUCQQC84DKSDPyuK0PnFjk5QmXdEHZsmaFOY8gjpKrw286La8KMonz8TJCYGvkR8uKkHQMRwcxANLAfJopoKNxyK8j1AkEAwcY3EHeKe4i3FhCjGSqAGAzFFBS1jzTNZxw/cxMMCbfxFH4WvhowqoC1iAKDyZ7HF7V+RcxcfuhoBJi/3+ImEg==-----END RSA PRIVATE KEY-----';

    private static $PUBLIC_KEY = '-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCoZZ8iUBprOIc0kGckr5ax6/Fd9IKKMc/XHayKEAvqpS0oz0b1ojEkpkdZBk0OWNhp73YNV+YLKBwwxOwb3u3hl8nBLoG/RilEbBMdCf55cUzNsfn/XF5CiLr/aci/OHuTe6ULvXs280T5M+nUh3iKdiT6z9XrFbH69C+xFoNInwIDAQAB-----END PUBLIC KEY-----';


    /**

     * 获取私钥

     * @return bool|resource

     */

    private static function getPrivateKey()

    {

        $privKey = self::$PRIVATE_KEY;

        return openssl_pkey_get_private($privKey);

    }



    /**

     * 获取公钥

     * @return bool|resource

     */

    private static function getPublicKey()

    {

        $publicKey = self::$PUBLIC_KEY;

        return openssl_pkey_get_public($publicKey);

    }



    /**

     * 私钥加密

     * @param string $data

     * @return null|string

     */

    public static function privEncrypt($data = '')

    {
        if (!is_string($data)) {

            return null;

        }

        return openssl_private_encrypt($data,$encrypted,self::getPrivateKey()) ? base64_encode($encrypted) : null;

    }



    /**

     * 公钥加密

     * @param string $data

     * @return null|string

     */

    public static function publicEncrypt($data = '')

    {

        if (!is_string($data)) {

            return null;

        }

        return openssl_public_encrypt($data,$encrypted,self::getPublicKey()) ? base64_encode($encrypted) : null;

    }



    /**

     * 私钥解密

     * @param string $encrypted

     * @return null

     */

    public static function privDecrypt($encrypted = '')

    {

        if (!is_string($encrypted)) {

            return null;

        }

        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, self::getPrivateKey())) ? $decrypted : null;

    }



    /**

     * 公钥解密

     * @param string $encrypted

     * @return null

     */

    public static function publicDecrypt($encrypted = '')

    {

        if (!is_string($encrypted)) {

            return null;

        }

        return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, self::getPublicKey())) ? $decrypted : null;

    }

}