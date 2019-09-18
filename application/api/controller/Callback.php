<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/31 0031
 * Time: 14:34
 */

namespace app\api\controller;
use app\helps\pay\aop\AopClient;
use app\models\CourseContent;
use think\Controller;
use Naixiaoxin\ThinkWechat\Facade;
use think\Db;
use think\facade\Log;

class Callback extends Base
{
    /**
     * 支付支付回调
     * @return bool
     */
    public function alipayRecharge()
    {
        Log::write($this->request->post(), 'alipayRecharge_pay', true);
        $message = $this->request->post();
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwTkk4n94SzI9wDcle82KMmI8T8U6G1NKt6JdFAi8g9FSwNmOA2LEY+KWi/HDFqQM3JMh1RAXWLs713tL0075ak2yn6fjZjfIANW1GM+sAgfCAMrLXGbr9m2zGd5ZKLiWmeKp5SA6cWfD4ZqvKxqubNAk7wwq/QEfRtYnAPloU7vfGfDxeirTVF4STyeiG0N0mnvFiguC3oeS+WodS10Q29KQetg0Dfk0LgYAi+HAEt3eQVz019gkdXCnuYoo+vhLsaYCgwa9oDP6p4iM8+0IPp7rgscETbln4JK2y2RTskJw4aCoLanfmP9Lg80DNOhalTymv2QjIICBxCr4fzwbpwIDAQAB';

        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        if ($flag) {
            try {
                Db::startTrans();
                // 回调
                //微信支付成功，为用户充值
                Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->update(['status'=>1]);
                $order = Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->find();
                Db::name('user')->where(['id'=>$order['user_id']])->setInc('money',$order['money']);
                $this->add_log($order['user_id'], 1,$order['pay_name'],$order['money']);
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
            Log::write($message, 'wechatRecharge_pay', true);
            if (array_key_exists("return_code", $message) && array_key_exists("result_code", $message) && $message["return_code"] == "SUCCESS" && $message["result_code"] == "SUCCESS") {
                try {
                    Db::startTrans();
                    //微信支付成功，为用户充值
                    Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->update(['status'=>1]);
                    $order = Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->find();
                    Db::name('user')->where(['id'=>$order['user_id']])->setInc('money',$order['money']);
                    $this->add_log($order['user_id'], 1,$order['pay_name'],$order['money']);
                    //处理业务
                    Db::commit();
                    return true;
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
            return true; // 返回处理完成
        });

        $response->send();
    }

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
                    //微信支付成功，为用户充值会员
                    Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->update(['status'=>1]);
                    $order = Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->find();
                    $user = Db::name('user')->where(['id'=>$order['user_id']])->find();
                    $vip = new Vip();
                    $vip->joinVip($order['user_id']);
                    #获取会员数量 并且 是30W以内的会员
                    $vip_count = $vip->getVipCount();
                    $reward_num = get_config('user','reward_num');
                    if($vip_count < $reward_num){
                        $info['in_reward']= 1;
                    }
                    if(intval($user['level_end_time']) > time()){
                        $info['level_end_time'] = intval($user['level_end_time']) + 365*24*60*60;
                    }else{
                        $info['level_start_time'] =  time();
                        $info['level_end_time'] = time() + 365*24*60*60;
                    }
                    Db::name('user')->where(['id'=>$order['user_id']])->update($info);

                    //检测上级是否达到发展奖励
                    $parent = $vip->getParentId($order['user_id']);
                    if(intval($parent['zhi']) > 0){
                        $vip->updateLevelTime($parent['zhi']);
                    }
                    if(intval($parent['jian']) > 0){
                        $vip->updateLevelTime($parent['jian']);
                    }
                    Db::commit();
                    return true;
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
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwTkk4n94SzI9wDcle82KMmI8T8U6G1NKt6JdFAi8g9FSwNmOA2LEY+KWi/HDFqQM3JMh1RAXWLs713tL0075ak2yn6fjZjfIANW1GM+sAgfCAMrLXGbr9m2zGd5ZKLiWmeKp5SA6cWfD4ZqvKxqubNAk7wwq/QEfRtYnAPloU7vfGfDxeirTVF4STyeiG0N0mnvFiguC3oeS+WodS10Q29KQetg0Dfk0LgYAi+HAEt3eQVz019gkdXCnuYoo+vhLsaYCgwa9oDP6p4iM8+0IPp7rgscETbln4JK2y2RTskJw4aCoLanfmP9Lg80DNOhalTymv2QjIICBxCr4fzwbpwIDAQAB';

        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        if ($flag) {
            try {
                Db::startTrans();
                //微信支付成功，为用户充值会员
                Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->update(['status'=>1]);
                $order = Db::name('user_order')->where(['order_no'=>$message['out_trade_no']])->find();
                $user = Db::name('user')->where(['id'=>$order['user_id']])->find();
                $vip = new Vip();
                $vip->joinVip($order['user_id']);
                #获取会员数量 并且 是30W以内的会员
                $vip_count = $vip->getVipCount();
                $reward_num = get_config('user','reward_num');
                if($vip_count < $reward_num){
                    $info['in_reward']= 1;
                }
                if(intval($user['level_end_time']) > time()){
                    $info['level_end_time'] = intval($user['level_end_time']) + 365*24*60*60;
                }else{
                    $info['level_start_time'] =  time();
                    $info['level_end_time'] = time() + 365*24*60*60;
                }
                Db::name('user')->where(['id'=>$order['user_id']])->update($info);

                //检测上级是否达到发展奖励
                $parent = $vip->getParentId($order['user_id']);
                if(intval($parent['zhi']) > 0){
                    $vip->updateLevelTime($parent['zhi']);
                }
                if(intval($parent['jian']) > 0){
                    $vip->updateLevelTime($parent['jian']);
                }
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
    public function wechatreceipt()
    {
        $app = Facade::payment();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            if (array_key_exists("return_code", $message) && array_key_exists("result_code", $message) && $message["return_code"] == "SUCCESS" && $message["result_code"] == "SUCCESS") {
                try {
                    Db::startTrans();
                    $reorder = new Reorder();
                    $reorder->SuccessPayMoney($message['out_trade_no']);
                    Db::commit();
                    return true;
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

            return true; // 返回处理完成
        });

        $response->send();
    }

    /**
     * 支付支付回调
     * @return bool
     */
    public function alipayreceipt()
    {
        Log::write($this->request->post(), 'alipay_pay', true);
        $message = $this->request->post();
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwTkk4n94SzI9wDcle82KMmI8T8U6G1NKt6JdFAi8g9FSwNmOA2LEY+KWi/HDFqQM3JMh1RAXWLs713tL0075ak2yn6fjZjfIANW1GM+sAgfCAMrLXGbr9m2zGd5ZKLiWmeKp5SA6cWfD4ZqvKxqubNAk7wwq/QEfRtYnAPloU7vfGfDxeirTVF4STyeiG0N0mnvFiguC3oeS+WodS10Q29KQetg0Dfk0LgYAi+HAEt3eQVz019gkdXCnuYoo+vhLsaYCgwa9oDP6p4iM8+0IPp7rgscETbln4JK2y2RTskJw4aCoLanfmP9Lg80DNOhalTymv2QjIICBxCr4fzwbpwIDAQAB';

        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        if ($flag) {
            try {
                Db::startTrans();
                $reorder = new Reorder();
                $reorder->SuccessPayMoney($message['out_trade_no']);
                Db::commit();
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