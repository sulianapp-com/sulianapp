<?php namespace zgldh\UploadManager;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2015/7/23
 * Time: 16:50
 */
class UploadManager
{
    /**
     * @var UploadStrategyInterface
     */
    private $strategy = null;

    /**
     * @var null The name of default disk
     */
    private $diskName = null;

    /**
     * @var array zgldh\UploadManager\Validators\Base
     */
    private $validatorGroups = null;

    private $errors = null;

    public function __construct()
    {
        $this->strategy = self::getStrategy();
        $this->withDisk();
    }

    /**
     * @return UploadManager
     */
    public static function getInstance()
    {
        return \App::make('upload-manager');
    }

    /**
     * @return UploadStrategyInterface
     */
    public static function getStrategy()
    {
        return \App::make('zgldh\UploadManager\UploadStrategyInterface');
    }

    /**
     * 得到已上传文件的URL
     * @param $disk
     * @param $path
     * @return string
     */
    public function getUploadUrl($disk, $path)
    {
        $url = '';
        $methodName = 'get' . ucfirst(Str::camel($disk)) . 'Url';
        if (method_exists($this->strategy, $methodName)) {
            $url = $this->strategy->$methodName($path);
        }
        return $url;
    }

    /**
     * 设置默认disk名字
     * @param $diskName config/filesystems.php disks数组内的key
     * @return $this
     * @throws \Exception
     */
    public function withDisk($diskName = null)
    {
        if ($diskName == null) {
            $this->diskName = \Config::get('upload.base_storage_disk');
        } elseif (\Config::has('filesystems.disks.' . $diskName)) {
            $this->diskName = $diskName;
        } else {
            throw new BadDiskException("Bad disk name: " . $diskName);
        }
        return $this;
    }

    /**
     * 设置验证机制， 要在upload、update之前调用
     * @param $validatorGroups 验证组的名字
     * @return $this
     */
    public function withValidator($validatorGroups)
    {
        if (!is_array($validatorGroups)) {
            $validatorGroups = [$validatorGroups];
        }
        $this->validatorGroups = $validatorGroups;
        return $this;
    }

    /**
     * 核心上传
     * @param $upload           Upload object
     * @param $uploadedFilePath string (path)
     * @param $file             UploadedFile / string
     * @param $preCallback      function
     * @return bool
     */
    private function coreUpload($upload, $uploadedFilePath, $file, $preCallback)
    {
        try {
            $newName = $this->strategy->makeFileName($file);
            $path = $this->strategy->makeStorePath($newName);


            $content = file_get_contents($uploadedFilePath);
            UploadValidator::validate($content, $this->validatorGroups);

            $upload->path = $path;
            $upload->disk = $this->diskName;
            $upload->size = strlen($content);

            if (is_callable($preCallback)) {
                $upload = $preCallback($upload);
            }
            if (!$upload) {
                unset($content);
                return false;
            }

            $disk = \Storage::disk($upload->disk);
            if ($disk->put($upload->path, $content) == false) {
                return false;
            }
        } catch (UploadException $e) {
            $this->storeErrors($e);
            return false;
        }
        return $upload;
    }

    private function newUploadModel()
    {
        $modelClassName = config('upload.upload_model');
        $model = app($modelClassName);
        return $model;
    }

    /**
     * 保存上传文件，生成上传对象
     * @param $file
     * @param null $preCallback
     * @return Upload|bool
     */
    public function upload($file, $preCallback = null)
    {
        if (is_string($file)) {
            return $this->uploadByUrl($file, $preCallback);
        }

        $upload = $this->newUploadModel();
        $upload->disk = $this->diskName;

        $uploadedFilePath = $file->getPathname();
        $upload = $this->coreUpload($upload, $uploadedFilePath, $file, $preCallback);

        return $upload;
    }

    /**
     * 从URL获取文件并保存，生成上传对象
     * @param $url
     * @param null $preCallback
     * @return Upload|bool
     */
    public function uploadByUrl($url, $preCallback = null)
    {
        $upload = $this->newUploadModel();
        $upload->disk = $this->diskName;

        $uploadedFilePath = $url;
        $upload = $this->coreUpload($upload, $uploadedFilePath, $url, $preCallback);

        return $upload;
    }

    /**
     * 用已上传文件更新一个上传对象
     * @param $upload
     * @param UploadedFile $file
     * @param null $preCallback
     * @return bool
     */
    public function update(&$upload, $file, $preCallback = null)
    {
        if (is_string($file)) {
            return $this->updateByUrl($upload, $file, $preCallback);
        }
        $oldDisk = $upload->disk;
        $oldPath = $upload->path;

        $uploadedFilePath = $file->getPathname();
        $result = $this->coreUpload($upload, $uploadedFilePath, $file, $preCallback);
        if ($result) {
            $this->removeOldFile($oldDisk, $oldPath);
            $upload = $result;
        } else {
            $upload->disk = $oldDisk;
            $upload->path = $oldPath;
            return false;
        }
        return true;
    }

    /**
     * 用URL更新一个上传对象
     * @param $upload
     * @param $url
     * @param null $preCallback
     * @return bool
     */
    public function updateByUrl(&$upload, $url, $preCallback = null)
    {
        $oldDisk = $upload->disk;
        $oldPath = $upload->path;

        $uploadedFilePath = $url;
        $result = $this->coreUpload($upload, $uploadedFilePath, $url, $preCallback);
        if ($result) {
            $this->removeOldFile($oldDisk, $oldPath);
            $upload = $result;
        } else {
            $upload->disk = $oldDisk;
            $upload->path = $oldPath;
            return false;
        }

        return true;
    }

    private function removeOldFile($disk, $path)
    {
        if ($disk && $path) {
            $disk = \Storage::disk($disk);
            if ($disk) {
                $disk->delete($path);
            }
        }
    }

    public function storeErrors(UploadException $e)
    {
        \Log::error($e);
        $this->errors = $e->errors;
        //TODO
    }

    /**
     * @return null
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * 得到第一个错误信息
     * @return mixed
     */
    public function getFirstErrorMessage()
    {
        if (isset($this->errors[0])) {
            reset($this->errors);
            $error = each($this->errors);
            reset($this->errors);
            return $error['value'];
        }
    }

}
