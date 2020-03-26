<?php
/**
 * Created by PhpStorm.
 * User: yp-tc-7176
 * Date: 17/7/16
 * Time: 20:28
 */


namespace app\common\modules\yop\sdk\Util;

use app\common\modules\yop\sdk\Util\StringBuilder;

abstract class HttpUtils
{
    /**
     * Normalize a string for use in url path. The algorithm is:
     * <p>
     * <p>
     * <ol>
     * <li>Normalize the string</li>
     * <li>replace all "%2F" with "/"</li>
     * <li>replace all "//" with "/%2F"</li>
     * </ol>
     * <p>
     * <p>
     * object key can contain arbitrary characters, which may result double slash in the url path. Apache http
     * client will replace "//" in the path with a single '/', which makes the object key incorrect. Thus we replace
     * "//" with "/%2F" here.
     *
     * @param path the path string to normalize.
     * @return the normalized path string.
     * @see #normalize(String)
     */
    public static function normalizePath($path)
    {
        return str_replace("%2F", "/",HttpUtils::normalize($path));
    }

    /**
     * @param $value
     * @return string
     */
    public static function normalize($value)
    {

        return rawurlencode($value);

    }

    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    /**
     * @param $path
     * @return string
     */
    public static function getCanonicalURIPath($path)
    {
        if ($path == null) {
            return "/";
        } else if (HttpUtils::startsWith($path,'/')) {
            return HttpUtils::normalizePath($path);
        } else {
            return "/" + HttpUtils::normalizePath($path);
        }
    }



}