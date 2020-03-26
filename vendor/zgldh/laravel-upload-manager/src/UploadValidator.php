<?php namespace zgldh\UploadManager;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2015/8/12
 * Time: 16:50
 */
class UploadValidator
{
    /**
     * 验证文件是否符合验证规则
     * @param $file
     * @param $validatorGroups array(string)
     * @return bool
     */
    public static function validate($file, $validatorGroups)
    {
        $tempFile = self::storeTempFile($file);

        $validators = self::mergeValidators($validatorGroups);
        $rules = self::makeRules($validators);

        $data = ['upload' => $tempFile];
        $rules = ['upload' => $rules];

        $messages = [
            'upload.min'     => trans('validation.min.file'),
            'upload.max'     => trans('validation.max.file'),
            'upload.size'    => trans('validation.size.file'),
            'upload.between' => trans('validation.between.file')
        ];
        $validator = \Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            self::removeTempFile();
            $messages = $validator->errors();
            $errors = $messages->get('upload');
            throw new UploadException($errors);
        }
        self::removeTempFile();
        return true;
    }

    private static function mergeValidators($validatorGroups)
    {
        $validators = [];
        if (is_array($validatorGroups)) {
            foreach ($validatorGroups as $validatorGroup) {
                $groupItems = config('upload.validator_groups.' . $validatorGroup);
                $validators = $validators + $groupItems;
            }
        }
        $validators = $validators + config('upload.validator_groups.common');

        return $validators;
    }

    private static function makeRules($validators)
    {
        $rules = [];
        foreach ($validators as $key => $value) {
            if ($key == null) {
                continue;
            }
            if ($value === null) {
                $rule = $key;
            } else {
                $rule = $key . ':' . $value;
            }
            $rules[] = $rule;
        }
        $rules = join('|', $rules);
        return $rules;
    }

    private static $tempFileName = null;

    private static function getTempFileName($fileContent = null)
    {
        if (self::$tempFileName == null) {
            self::$tempFileName = time() . md5($fileContent);
        }
        return self::$tempFileName;
    }

    private static function storeTempFile($fileContent)
    {
        $filename = self::getTempFileName($fileContent);
        $filePath = storage_path('framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $filename);
        file_put_contents($filePath, $fileContent);
        $file = new File($filePath);
        return $file;
    }

    private static function removeTempFile()
    {
        if (self::$tempFileName) {
            $filePath = storage_path('framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . self::$tempFileName);
            unlink($filePath);
        }
    }
}