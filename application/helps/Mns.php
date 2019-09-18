<?php

namespace app\helps;

require_once APP_EXTEND . 'aliyun/sms/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
use think\facade\Env;

Config::load();

class Mns extends Rds
{
    private $config = [
        'signName' => '约邻',
        'templateCode' => 'SMS_166705430',
        'product' => 'Dysmsapi',
        'domain' => 'dysmsapi.aliyuncs.com',
        'accessKeyId' => 'LTAI1fA5T8KvQlKg',
        'accessKeySecret' => 'cAAiSPSsQfecftXE6WIgraUSE5Cxxs',
//        'accessKeyId' => 'LTAI1fA5T8KvQlKg',
//        'accessKeySecret' => 'cAAiSPSsQfecftXE6WIgraUSE5Cxxs',
        'region' => 'cn-hangzhou',
        'endPointName' => 'cn-hangzhou'
    ];

    public static $acsClient = null;

    private $time = [
        'ttl' => 600,
    ];

    private function _initialize()
    {
        parent::__construct();
    }

    public function setTemplateCode($code = '')
    {
        $this->config['templateCode'] = $code;
    }

    public function setSignName($sign = '')
    {
        $this->config['signName'] = $sign;
    }

    public function getAcsClient()
    {
        if (static::$acsClient == null) {
            $profile = DefaultProfile::getProfile($this->config['region'], $this->config['accessKeyId'], $this->config['accessKeySecret']);
            DefaultProfile::addEndpoint($this->config['endPointName'], $this->config['region'], $this->config['product'], $this->config['domain']);
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }

    public function send($mobile = null, $data = [], $prefix = '')
    {
        $code = rand(100000, 999999);
        $params = $data ?: ['code' => $code];
        $request = new SendSmsRequest();
        $request->setPhoneNumbers($mobile);
        $request->setSignName($this->config['signName']);
        $request->setTemplateCode($this->config['templateCode']);
        $request->setTemplateParam(json_encode($params));
        $acsResponse = $this->getAcsClient()->getAcsResponse($request);
        if ($acsResponse->Code == 'OK') {
            return $data == null ? $this->redis->setex($prefix . $mobile, $this->time['ttl'], $code) : true;
        }
        return false;
    }

    public function checkCode($code = null, $mobile = null, $prefix = '')
    {
        if (!Env::get('sms.open')) {
            return true;
        }
        $check = $this->redis->get($prefix . $mobile);
        if ($check && ($mobile !== null)) {
            return $check == $code ? true : false;
        }
        return false;
    }
}