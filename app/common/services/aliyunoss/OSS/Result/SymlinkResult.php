<?php

namespace app\common\services\aliyunoss\OSS\Result;

use app\common\services\aliyunoss\OSS\Core\OssException;
use app\common\services\aliyunoss\OssClient;

/**
 *
 * @package OSS\Result
 */
class SymlinkResult extends Result
{
    /**
     * @return string
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $this->rawResponse->header[OssClient::OSS_SYMLINK_TARGET] = rawurldecode($this->rawResponse->header[OssClient::OSS_SYMLINK_TARGET]);
        return $this->rawResponse->header;
    }
}

