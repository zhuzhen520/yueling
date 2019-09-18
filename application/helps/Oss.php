<?php

namespace app\helps;

include_once APP_EXTEND . 'aliyun/aliyun-php-sdk-core/Config.php';
include_once APP_EXTEND . 'aliyun/oss/vendor/autoload.php';

use Sts\Request\V20150401 as Sts;
use OSS\OssClient;
use OSS\Core\OssException;

class Oss
{
    protected $regionId = '';
    protected $accessKeyId = '';
    protected $accessSecret = '';
    protected $roleArn = '';
    protected $bucket = '';
    protected $endpoint = '';
    protected $storage = null;
    protected $uploadType = 'sts';

    public function __construct($regionId = '', $accessKeyId = '', $accessSecret = '', $rolaArn = '', $bucket = '', $endpoint = '')
    {
        $this->regionId = $regionId ?: config('aliyun.oss.regionId');
        $this->accessKeyId = $accessKeyId ?: config('aliyun.oss.accessKeyId');
        $this->accessSecret = $accessSecret ?: config('aliyun.oss.accessSecret');
        $this->roleArn = $rolaArn ?: config('aliyun.oss.roleArn');
        $this->bucket = $bucket ?: config('aliyun.oss.bucket');
        $this->endpoint = $endpoint ?: config('aliyun.oss.endpoint');
        $this->storage = (new Rds())->redis;
    }


    /*
     * OSS上传方式
     * ram：使用ram账号的key和secret直接上传（适用于服务器上传文件）
     * sts: 适用ram账号生成临时的sts的的key和secret直接上传（适用于APP上传文件）
     */
    public function setUploadType($type = '')
    {
        $this->uploadType = $type;
    }

    public function setStorage($redis = null)
    {
        $redis && is_object($redis) ? $this->storage = $redis : null;
    }

    public function getCredentials()
    {
        if ($this->uploadType == 'ram') {
            return [
                'AccessKeyId' => $this->accessKeyId,
                'AccessKeySecret' => $this->accessSecret,
            ];
        }
        if ($this->storage) {
            $config = json_decode($this->storage->get('oss_config'), true);
            if ($config) {
                return $config;
            }
        }

        $iClientProfile = \DefaultProfile::getProfile($this->regionId, $this->accessKeyId, $this->accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new Sts\AssumeRoleRequest();
        $request->setRoleSessionName($this->bucket);
        $request->setRoleArn($this->roleArn);
        //$request->setPolicy($policy);
        $request->setDurationSeconds(3600);
        $response = $client->doAction($request);
        $data = json_decode($response->getBody(), true);
        $config = $data['Credentials'];
        if ($this->storage) {
            $expire = strtotime($config['Expiration']);
            $this->storage->setex('oss_config', $expire - time(), json_encode($config));
        }

        return $config;
    }

    /*
     * $postfile [name => 'xxxx', tmp_name => 'xxxxx']
     */
    public function uploadPostFile($postfile, $folder = '')
    {
        $config = $this->getCredentials();
        if (!$config) {
            return false;
        }
        $newname = $this->changeName($postfile['name']);
        if ($folder) {
            $newname = str_replace('//', '/', $folder . '/' . $newname);
        }
        try {
            if ($this->uploadType == 'ram') {
                $ossClient = new OssClient($config['AccessKeyId'], $config['AccessKeySecret'], $this->endpoint);
            } else {
                $ossClient = new OssClient($config['AccessKeyId'], $config['AccessKeySecret'], $this->endpoint, false, $config['SecurityToken']);
            }
        } catch (OssException $e) {
            return false;
        }
        try {
            $res = $ossClient->uploadFile($this->bucket, $newname, $postfile['tmp_name']);
            return $res['info']['url'];
        } catch (OssException $e) {
            return false;
        }

        return false;
    }

    public function uploadLocalFile($localfile, $folder = '')
    {
        $config = $this->getCredentials();
        if (!$config) {
            return false;
        }

        $newname = $this->changeName($localfile);
        if ($folder) {
            $newname = str_replace('//', '/', $folder . '/' . $newname);
        }

        try {
            if ($this->uploadType == 'ram') {
                $ossClient = new OssClient($config['AccessKeyId'], $config['AccessKeySecret'], $this->endpoint);
            } else {
                $ossClient = new OssClient($config['AccessKeyId'], $config['AccessKeySecret'], $this->endpoint, false, $config['SecurityToken']);
            }
        } catch (OssException $e) {
            return false;
        }

        try {
            $res = $ossClient->uploadFile($this->bucket, $newname, $localfile);
            return $res['info']['url'];
        } catch (OssException $e) {
            return false;
        }

        return false;
    }

    public function uploadString($string, $name, $folder = '')
    {
        $config = $this->getCredentials();
        if (!$config) {
            return false;
        }

        $newname = $this->changeName($name);
        if ($folder) {
            $newname = str_replace('//', '/', $folder . '/' . $newname);
        }

        try {
            if ($this->uploadType == 'ram') {
                $ossClient = new OssClient($config['AccessKeyId'], $config['AccessKeySecret'], $this->endpoint);
            } else {
                $ossClient = new OssClient($config['AccessKeyId'], $config['AccessKeySecret'], $this->endpoint, false, $config['SecurityToken']);
            }
        } catch (OssException $e) {
            return false;
        }

        try {
            $res = $ossClient->putObject($this->bucket, $newname, $string);
            return $res['info']['url'];
        } catch (OssException $e) {
            return false;
        }
    }

    public function changeName($fileName)
    {
        $name = md5(uniqid(rand()));
        $ext = $this->fileExt($fileName);
        return $name . '.' . $ext;
    }

    public function fileExt($fileName)
    {
        $tmp = explode(".", $fileName);
        $fileExt = $tmp[count($tmp) - 1];
        return strtolower($fileExt);
    }
}