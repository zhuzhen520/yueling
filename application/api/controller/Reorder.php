<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/26 0026
 * Time: 9:14
 */

namespace app\api\controller;

use app\helps\Redis;
use think\Db;
use think\facade\Env;

class Reorder extends Base
{
    public function priceEdit(){
        if($this->request->isPost()){
            $type =  input('type');
            $receipt_id  = input('receipt_id');
            $money  = input('money');

            $pay_type  = input('pay_type');
            if(empty(intval($type)) || empty($receipt_id)){
                return $this->fail('信息错误',false);
            }
            $receipt = Db::name('receipt')->where(['id'=>$receipt_id])->find();
            $accept_user_id = Db::name('receipt_aceept')->where(['id'=>$receipt['accept_id']])->value('user_id');
            if(floatval($receipt['pay_money']) > 0){
                if($type == 1){  //加价
                    if(empty($money) || floatval($money) < 1){
                        return $this->fail('最底加价1',false);
                    }
                    $order_sn = $this->userOrder($money, 25, $type,$receipt_id);   //下订单
                    $pay = new Pay();
                    #支付类型
                    if($pay_type == 1){
                         $pay->payAliPay($order_sn,'http://'.Env::get('app.api_url').'/Callback/alipayreceipt');
                    }elseif($pay_type == 2){
                        $pay->wechatPay($order_sn,'http://'.Env::get('app.api_url').'/Callback/wechatreceipt');
                    }else{
                        $this->paymoney($order_sn);
                    }
                    $this->add_log($receipt['user_id'],1,25,$money,0);
                    $push = new Push();
                    $cid = $this->getUserInfo($accept_user_id,'cid');
                    $push->getui($cid,'约邻提示：发单人已加价');
//                    $cids = $this->getUserInfo($receipt['user_id'],'cid');
//                    $push->getui($cids,'约邻提示：您已加价');

                    $this->sendInfo('约邻提示：发单人已加价',$accept_user_id,'发单人已加价',$receipt_id,5);
//                    $this->sendInfo('约邻提示：您已加价',$receipt['user_id'],'您已加价',$receipt_id,6);

                    return $this->succ(floatval($receipt['pay_money']+$money));
                }else{   //减价
                    if(empty($money) || floatval($money) < 1){
                        return $this->fail('最底减价1',false);
                    }
                    $revenue = get_config('user','revenue');
                    $pingtai = floatval($receipt['storage_money']) * (floatval($revenue)/100);   //平台收益   20%
                    if((floatval($receipt['pay_money']) - $money) < $pingtai){
                        $num = $receipt['pay_money']-$pingtai;
                        return $this->fail('最多还可减价:' . $num,false);
                    }
                    if(floatval($receipt['pay_money']) < $money){
                        return $this->fail('金额错误',false);
                    }
                    Db::name('receipt')->where(['id'=>$receipt_id])->setDec('pay_money',$money);
                    Db::name('user')->where(['id'=>$receipt['user_id']])->setInc('money',$money);
                    $this->add_log($receipt['user_id'],1,26,$money,1);
                    $push = new Push();
                    $cid = $this->getUserInfo($receipt['user_id'],'cid');
                    $push->getui($cid,'约邻提示：接单人已减价');
//                    $cids = $this->getUserInfo($accept_user_id,'cid');
//                    $push->getui($cids,'约邻提示：您已减价');
                    $this->sendInfo('约邻提示：接单人已减价',$receipt['user_id'],'接单人已减价',$receipt_id,7);
//                    $this->sendInfo('约邻提示：您已减价',$accept_user_id,'您已减价',$receipt_id,8);
                    return $this->succ(floatval($receipt['pay_money'] - $money));
                }
            }else{
                return $this->succ('订单错误');
            }

        }
    }

    /**
     * 申诉状态         status = 6
     */
    public function orderAppeal(){
        $receipt_id =  input('receipt_id');
        $reason =  input('reason');
        if(empty($receipt_id)){
            return $this->fail('信息错误',false);
        }
        if( empty($reason)){
            return $this->fail('请填写申诉内容',false);
        }
        try{
            Db::startTrans();
            $receipt = Db::name('receipt')->where(['id'=>$receipt_id])->find();
            //冻结双方订单
            Db::name('receipt')
                ->where(['id'=>$receipt_id])
                ->update([
                    'status'=>6,
                    'reason'=>$reason,
                    'appeal'=>$this->request->user_id
                ]);
            Db::name('receipt_aceept')
                ->where(['id'=>$receipt['accept_id']])
                ->update([
                    'status'=>6,
                    'reason'=>$reason,
                    'appeal'=>$this->request->user_id
                ]);
            // 提交事务
            Db::commit();
            return $this->succ('申诉成功，订单已被冻结,请等待客服处理');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }
    /**
     * 发单支付
     */
    public function userApply(){
        if($this->request->isPost()){
            //设置redis判断是否多次点击
            $Redis = new Redis();
            $us = $Redis->get($this->request->user_id);
            if($us){
                return $this->fail('10秒内请勿重复支付',false);
            }
            $Redis->setex($this->request->user_id,10,"paytime not end");

            $type =  input('type');
            $receipt_id =  input('receipt_id');
            if(empty(intval($type)) || empty($receipt_id)){
                return $this->fail('信息错误',false);
            }
            $receipt = Db::name('receipt')->where(['id'=>$receipt_id])->find();
            $money = $receipt['last_service_price'];
//            $money = 0.01;
            $order_sn = $this->userOrder($money, 24, $type,$receipt_id);   //下订单
            $pay = new Pay();
            #支付类型
            if($type == 1){
                $res =  $pay->payAliPay($order_sn,'http://'.Env::get('app.api_url').'/Callback/alipayreceipt');
            }elseif($type == 2){
                $res =  $pay->wechatPay($order_sn,'http://'.Env::get('app.api_url').'/Callback/wechatreceipt');
            }else{
                $res = $this->paymoney($order_sn);
            }
            $this->add_log($this->request->user_id,1,11,floatval($money),0);
            return $this->succ($res);
        }else{
            return $this->succ($this->getUserInfo($this->request->user_id,'money'));
        }
    }

    public function paymoney($order_sn){
        $order = Db::name('user_order')->where(['order_no'=>$order_sn])->find();
        if(floatval($this->getUserInfo($this->request->user_id,'money')) < $order['money']){
            return $this->fail('余额不足',false);
        }
        //todo::扣除用户余额
        try{
            Db::startTrans();
            $userdec = Db::name('user')->where(['id'=>$this->request->user_id])->setDec('money',$order['money']);

            if($userdec){
                $this->SuccessPayMoney($order_sn);
            }
            // 提交事务
            Db::commit();
            return '支付成功';
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }

    /**
     * 回调
     * @param $order_sn
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function SuccessPayMoney($order_sn){
        $order = Db::name('user_order')->where(['order_no'=>$order_sn])->find();
        //todo::订单类型判断
        if(intval($order['pay_name']) == 24){         //todo::发布订单
            //改变订单状态
            Db::name('user_order')->where(['order_no'=>$order_sn])->update([
                'status'=>1,
                'pay_time'=>time(),
            ]);
            Db::name('receipt')->where(['id'=>$order['receipt']])->update([
                'is_pay'=>1,
                'status'=>1,
                'pay_money'=>$order['money'],
                'storage_money'=>$order['money'],
            ]);
        }elseif(intval($order['pay_name']) == 25){   //todo::加价处理
            //改变订单金额
            Db::name('receipt')->where(['id'=>$order['receipt']])->setInc('pay_money',$order['money']);
        }else{
            //todo::其他业务逻辑
            return false;
        }

        return true;
    }

    /**
     * 下订单
     * @param $money
     * @param $pay_name
     * @param int $type
     * @param string $receipt_id
     * @return string|void
     */
    protected function userOrder($money, $pay_name,$type = 1,$receipt_id=''){
        $str = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $arr = [
            'user_id'=>$this->request->user_id,
            'money'=> $money,
            'add_time'=>time(),
            'status'=>0,
            'order_no'=>$str,
            'pay_name'=>$pay_name,  //21 充值
            'pay_type'=>$type,  //支付类型
            'receipt'=>$receipt_id  //支付类型
        ];
        try{
            Db::startTrans();
            $del = Db::name('user_order')->where(['user_id'=>$this->request->user_id,'status'=>0,'pay_name'=>$pay_name])->field('id')->select();
            if($del){
                $id_str = '';
                foreach ($del as $ids){
                    $id_str .= $ids['id'].',';
                }
                Db::name('user_order')->delete(trim($id_str,','));
            }
            $order_id = Db::name('user_order')->insertGetId($arr);
            if(!$order_id){
                return $this->fail('订单生成失败',false);
            }
            // 提交事务
            Db::commit();
            return $str;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }



}