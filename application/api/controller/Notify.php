<?php
namespace app\api\controller;
use app\helps\pay\aop\AopClient;
use app\models\CourseContent;
use think\Controller;
use Naixiaoxin\ThinkWechat\Facade;
use think\Db;
use think\facade\Log;



class Notify extends Controller
{
    /**
     * 微信支付回调
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function wechat()
    {
        $app = Facade::payment();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            Log::write($message, 'wechat_pay', true);
            if (array_key_exists("return_code", $message) && array_key_exists("result_code", $message) && $message["return_code"] == "SUCCESS" && $message["result_code"] == "SUCCESS") {
                try {
                    Db::startTrans();
                    $order = Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->update(['status'=>1]);
                    //处理业务

                    Db::commit();
                    return $order;
                } catch (\Exception $e) {
                    Db::rollback();
                    $data = [
                        'line' => $e->getLine(),
                        'mgs' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ];
                    Log::write($data, 'wechat_pay', true);
                    return false;
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            return json(['status'=>1,'info'=>'回调成功']);
            return true; // 返回处理完成
        });

        $response->send();
    }

    /**
     * 支付支付回调
     * @return bool
     */
    public function alipay()
    {
        Log::write($this->request->post(), 'alipay_pay', true);
        $message = $this->request->post();
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwX7crMv5L6Ohc967x4CVm63PplQL3yIGc4tMWiCruqAXy7oOQ8WWk5T0KdpdjjUYlCJkH8o2FI/QIWqCaxaRiRLbUetK9Eq1rz5sTyyyl9bCA8QqFNY2Oh5jmAD2mCn8z1qnj2y2pP5yiUUEIJCeGlfrB8mgry4JQg+rlEuF2U2RyXjQfExPBkLtvj+mkTvDjjk+K6+H+1draOnXyGWW4vTxKKgLDQG+NP0ojjXiX33fBMSzEE9Se0mFTAYaDemDT5kBQQyzyBfFJ1YaVYP+q9+mUNW5ILGLGkagcTtbAqUDyNqYxKg7BK3tXrhIh63YuKfchWfivoFgAuZUXXNEbQIDAQAB';

        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        if ($flag) {
            try {
                Db::startTrans();
                // 回调
                $order = Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->update(['status'=>1]);
                Db::commit();
                return $order;
            } catch (\Exception $e) {
                Db::rollback();
                $data = [
                    'line' => $e->getLine(),
                    'mgs' => $e->getMessage(),
                    'code' => $e->getCode(),
                ];
                Log::write($data, 'alipay_pay', true);
                return false;
            }
            return true;
        }
    }

    /**
     * 微信支付回调
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function wechatRecharge()
    {
        $app = Facade::payment();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            Log::write($message, 'wechat_pay', true);
            if (array_key_exists("return_code", $message) && array_key_exists("result_code", $message) && $message["return_code"] == "SUCCESS" && $message["result_code"] == "SUCCESS") {
                try {
                    Db::startTrans();
                    $order = Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->update(['status'=>1]);

                    //处理业务

                    Db::commit();
                    return $order;
                } catch (\Exception $e) {
                    Db::rollback();
                    $data = [
                        'line' => $e->getLine(),
                        'mgs' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ];
                    Log::write($data, 'wechat_pay', true);
                    return false;
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            return json(['status'=>1,'info'=>'回调成功']);
            return true; // 返回处理完成
        });

        $response->send();
    }

    /**
     * 支付支付回调
     * @return bool
     */
    public function alipayRecharge()
    {
        Log::write($this->request->post(), 'alipay_pay', true);
        $message = $this->request->post();
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwX7crMv5L6Ohc967x4CVm63PplQL3yIGc4tMWiCruqAXy7oOQ8WWk5T0KdpdjjUYlCJkH8o2FI/QIWqCaxaRiRLbUetK9Eq1rz5sTyyyl9bCA8QqFNY2Oh5jmAD2mCn8z1qnj2y2pP5yiUUEIJCeGlfrB8mgry4JQg+rlEuF2U2RyXjQfExPBkLtvj+mkTvDjjk+K6+H+1draOnXyGWW4vTxKKgLDQG+NP0ojjXiX33fBMSzEE9Se0mFTAYaDemDT5kBQQyzyBfFJ1YaVYP+q9+mUNW5ILGLGkagcTtbAqUDyNqYxKg7BK3tXrhIh63YuKfchWfivoFgAuZUXXNEbQIDAQAB';

                $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");

                if ($flag) {
                    try {
                        Db::startTrans();
                        // 回调
                        $order = Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->update(['status'=>1]);

                        Db::commit();
                        return $order;
                    } catch (\Exception $e) {
                        Db::rollback();
                        $data = [
                            'line' => $e->getLine(),
                            'mgs' => $e->getMessage(),
                            'code' => $e->getCode(),
                        ];
                        Log::write($data, 'alipay_pay', true);
                        return false;
            }
            return true;
        }
    }


}