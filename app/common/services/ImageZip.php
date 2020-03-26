<?php

namespace app\common\services;
    /*+-----------------------------------------------------------------------+*/
    /*+ jpg,gif,png图片等比例压缩                                             +*/
    /*+-----------------------------------------------------------------------+*/
class ImageZip{
     /**
      * 指定长宽压缩
      */
    public function getNewSizeInFix($maxWidth, $maxHeight, $srcWidth, $srcHeight) {
        if($srcWidth > $maxWidth) {
           $maxWidth = $maxWidth;
           if($srcHeight > $maxHeight) {
            $maxHeight = ($srcHeight/$srcHeight) * $maxWidth;
           } else {
             $maxHeight = $srcHeight;
           }
           return [$maxHeight, $maxWidth, $srcHeight, $srcWidth];
           return array('width' => $maxWidth,'height' => $maxHeight);
        }

        if($srcHeight > $maxHeight) {
          $maxHeight = $maxHeight;
          if($srcWidth > $maxWidth) {
            $maxWidth = ($srcWidth/$srcHeight) * $maxHeight;
          } else {
            $maxWidth = $srcWidth;
          }
           return array('width' => $srcWidth, 'height' => $maxHeight);
        }
        return array('width' => $srcWidth, 'height' => $srcHeight);
    }

    //percent 按照百分比压缩 百分比去除百分号
    public function getNewSizeInPercent($percent, $srcWidth, $srcHeight) 
    {
        $maxHeight = ($srcHeight * $percent) / 100;
        $maxWidth = ($srcWidth * $percent) / 100;

        return array('width'=>$maxWidth, 'height'=>$maxHeight, 'srcWidth'=>$srcWidth, 'srcHeight'=>$srcHeight);
    }


     /**
      * 生成缩略图
      *
      * @param  String  $srcFile  原始文件路径
      * @param  String  $dstFile  目标文件路径
      * @param  string  $number  压缩百分比
      * @return  Boolean  生成成功则返回true，否则返回false
      */
    public function makeThumb($srcFile, $dstFile, $number) {
        if ($size = getimagesize($srcFile)) {
           $srcWidth = $size[0];
           $srcHeight = $size[1];
           $mime = $size['mime'];
           switch ($mime) {
            case 'image/jpeg';
             $isJpeg = true;
             break;
            case 'image/gif';
             $isGif = true;
             break;
            case 'image/png';
             $isPng = true;
             break;
            default:
             return false;
             break;
           }
           
           // header("Content-type:$mime");
           if (!strstr($number, '%')) {
                $arr = $this->getNewSizeInFix(intval($number), $srcHeight, $srcWidth, $srcHeight);
           } else {
                $arr = $this->getNewSizeInPercent(substr($number, 0, -1), $srcWidth, $srcHeight);
           }
           // dd($arr); die;

           $thumbWidth = $arr['width'];
           $thumbHeight = $arr['height'];
           if (isset($isJpeg) && $isJpeg) {
                $dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);
                $srcPic = imagecreatefromjpeg($srcFile);
                imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                imagejpeg($dstThumbPic, $dstFile, 100);
                imagedestroy($dstThumbPic);
                imagedestroy($srcPic);
                return true;
           } elseif (isset($isGif) && $isGif) {
            $dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);
                //创建透明画布
                imagealphablending($dstThumbPic, true);
                imagesavealpha($dstThumbPic, true);
                $trans_colour = imagecolorallocatealpha($dstThumbPic, 0, 0, 0, 127);
                imagefill($dstThumbPic, 0, 0, $trans_colour);
                $srcPic = imagecreatefromgif($srcFile);
                imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                imagegif($dstThumbPic, $dstFile);
                imagedestroy($dstThumbPic);
                imagedestroy($srcPic);
                return true;
           } elseif (isset($isPng) && $isPng) {
                $dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);
                //创建透明画布
                imagealphablending($dstThumbPic, true);
                imagesavealpha($dstThumbPic, true);
                $trans_colour = imagecolorallocatealpha($dstThumbPic, 0, 0, 0, 127);
                imagefill($dstThumbPic, 0, 0, $trans_colour);
                $srcPic = imagecreatefrompng($srcFile);
                imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
                imagepng($dstThumbPic, $dstFile);
                imagedestroy($dstThumbPic);
                imagedestroy($srcPic);
                return true;
           } else {
                return false;
           }
            } else {
            return false;
        }
    }
}
