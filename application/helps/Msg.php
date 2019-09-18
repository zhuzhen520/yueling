<?php

namespace app\helps;

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
use think\facade\Env;

//Config::load();

class Msg extends Rds
{
    private $config = [
        'msgname' => '约邻',
        'smsapi'=> 'http://www.smsbao.com/',
        'user'=>'qichezhousc',
        'pass'=>'qin888888',

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


    public function send($phone){
        $code = rand(100000, 999999);
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $smsapi = $this->config['smsapi'];
        $user = $this->config['user']; //短信平台帐号
        $pass = md5($this->config['pass']); //短信平台密码
        $content="【".$this->config['msgname']."】您的验证码为".$code."，在".$this->time['ttl']/60 ."分钟内有效!";//要发送的短信内容
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl) ;
        if ( $result == 0) {
          $this->redis->setex($phone, $this->time['ttl'], $code);
        }
        return $statusStr[$result];
    }
    public function sendInfo($phone,$type){
        $code = rand(100000, 999999);
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $smsapi = $this->config['smsapi'];
        $user = $this->config['user']; //短信平台帐号
        $pass = md5($this->config['pass']); //短信平台密码
        $content = "【".$this->config['msgname']."】亲爱的用户，您有新的"."【".$type."】"."类订单，记得多关注约邻APP喔~感谢您的支持！";
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl) ;
        if ( $result == 0) {
          $this->redis->setex($phone, $this->time['ttl'], $code);
        }
        return $statusStr[$result];
    }

    public function sendFadan($phone,$type){
        $code = rand(100000, 999999);
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $smsapi = $this->config['smsapi'];
        $user = $this->config['user']; //短信平台帐号
        $pass = md5($this->config['pass']); //短信平台密码
        $content = "【".$this->config['msgname']."】亲爱的用户，您的"."【".$type."】"."类发单接单人已经接单啦，记得多关注约邻APP喔~感谢您的支持！";
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl) ;
        if ( $result == 0) {
          $this->redis->setex($phone, $this->time['ttl'], $code);
        }
        return $statusStr[$result];
    }


    public function checkCode($code = null, $mobile = null)
    {
        if (!Env::get('sms.open')) {
            return true;
        }
        $check = $this->redis->get($mobile);
        if ($check && ($mobile !== null)) {
            return $check == $code ? true : false;
        }
        return false;
    }
}