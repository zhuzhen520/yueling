<?php

namespace app\batch;

use app\api\controller\Push;
use app\api\controller\Vip;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Order extends Command
{

    protected function configure()
    {
        $this->setName('Order')->setDescription('订单处理');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->firstKind();

    }
    //'status','in','0,1,2,4,7'
    /**
     * 第一类，    订单接单人已开始服务,发单人超时未付款
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    protected function firstKind(){
        $join = [['tb_receipt_aceept b','b.receipt_id = a.id']];
        $receipt = Db::name('receipt')
            ->join($join)
            ->alias('a')
            ->field('b.status as b_status, a.status as a_status,a.id as a_id, b.id as b_id,a.user_id as a_user_id, b.user_id as b_user_id,b.arrive_time')
            ->where(['a.status'=>2,'b.arrive'=>1])
            ->where('b.arrive_time','elt',time()-24*60*60*3)  //时间限制
            ->select();
        if(!empty($receipt)){
            Db::startTrans();
            try{
                foreach ($receipt as $key => $item){
                    //付款
                    $receipt = Db::name('receipt')->where(['id'=>$item['a_id']])->find();
                    $accept = Db::name('receipt_aceept')->where(['id'=>$item['b_id']])->find();
                    if(empty($receipt)){
                        continue;
                    }
                    if(empty($accept)){
                        continue;
                    }
                    //revenue  平台税收   zhiuserpro 会员利润
                    $revenue = get_config('user','revenue');
                    $money = $receipt['pay_money'];
                    if(floatval($revenue) > 0){
                        $pingtai = floatval($receipt['pay_money']) * (floatval($revenue)/100);   //平台收益
                        $money = $money-$pingtai;
                        if(floatval($pingtai) > 0){
                            $vip = new Vip();
                            $parent_id = $vip->getParentId($accept['user_id'])['zhi'];
                            //验证接单用户上级是否有拿到奖励的资格
                            if($vip->vilidateVip($parent_id) && $vip->userVip($parent_id)){   //直推收益
                                $zhiuserpro = get_config('user','zhiuserpro');
                                $zhipro = floatval($pingtai) * (floatval($zhiuserpro)/100);
                                //计算上级收益
                                Db::name('user')->where(['id'=>$parent_id])->setInc('money',$zhipro);
                                $this->add_log($parent_id,1,14,$zhipro);
                            }
                        }
                    }
                    //$money; 接单人收益
                    Db::name('user')->where(['id'=>$accept['user_id']])->setInc('money',$money);
                    Db::name('receipt_aceept')->where(['id'=>$receipt['accept_id']])->setInc('money',$money);
                    $this->add_log($accept['user_id'],1,15,$money);
                    Db::name('receipt')->where(['id'=>$item['a_id']])->update(['status'=>3]);  //发单人已付款状态   订单完成 待评价
                    Db::name('receipt_aceept')->where(['id'=>$item['b_id']])->update(['status'=>4]);  //发单人已付款状态   订单完成
                    Db::name('user_msg')->where(['order_id'=>$item['a_id']])->update(['status'=>1]);  //发单人已付款状态   订单完成  改变 聊天状态
                }
                // 提交事务
                Db::commit();
                $push = new Push();
                $cid = $this->getUserInfo($accept['user_id'],'cid');
                $push->getui($cid,'发单长长时间未确认付款，系统已自动确认');
                $cid2 = $this->getUserInfo($receipt['user_id'],'cid');
                $push->getui($cid2,'订单已完成');
                return 1;
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        }
//        $this->secondKinds();
    }
//
//    /**
//     * 发单人已约定接单人，接单人未确认接单
//     * @return int
//     * @throws \think\db\exception\DataNotFoundException
//     * @throws \think\db\exception\ModelNotFoundException
//     * @throws \think\exception\DbException
//     */
//    protected function secondKinds(){
//        $join = [['tb_receipt_aceept b','b.receipt_id = a.id']];
//        $receipt = Db::name('receipt')
//            ->join($join)
//            ->alias('a')
//            ->field('b.status as b_status, a.status as a_status,b.add_time as b_add_time,a.id as a_id, b.id as b_id, a.pay_money as a_pay_money,a.user_id as a_user_id, b.user_id as b_user_id')
//            ->where(['a.status'=>1,'b.status'=>1])
////            ->where('b.add_time','elt',time()-24*60*60*3)  //时间限制
//            ->select();
//
//        if(!empty($receipt)){
//            Db::startTrans();
//            try{
//                foreach ($receipt as $key => $item){
//                    $data['receipt_id'] = $item['a_id'];
//                    $receipt = Db::name('receipt')->where(['id'=>$data['receipt_id']])->field('id,user_id,pay_money,status,accept_id')->find();  //用户支付的金额
//                    $aceept_user_id = Db::name('receipt_aceept')->where(['id'=>$receipt['accept_id']])->value('user_id');
//
//                    Db::name('user')->where(['id'=>$receipt['user_id']])->setInc('money',floatval($receipt['pay_money']));//退还发单用户支付的金额
//                    $this->add_log($receipt['user_id'],1,16,floatval($receipt['pay_money']));
//                    //订单失效
//                    $where['receipt_id'] = $data['receipt_id'];
//                    Db::name('receipt_aceept')->where($where)->update(['status'=>5]);
//                    Db::name('receipt')->where(['id'=>$data['receipt_id']])->update(['status'=>5]);
//                    //改变订单状态
//                    $push = new Push();
//                    $cids = $this->getUserInfo($aceept_user_id,'cid');
//                    $push->getui($cids,'接单人长时间未接单,您的发单订单已超时失效, 支付金额已退回余额');
//                }
//                // 提交事务
//                Db::commit();
//                return 1;
//            } catch (\Exception $e) {
//                // 回滚事务
//                Db::rollback();
//            }
//        }
//    }

    protected function getUserInfo($user_id,$type){
        $user = Db::name('user')->where(['id'=>$user_id])->value($type);
        return $user;
    }

    /**
     * 生成日志
     * @param $id
     * @param $currency
     * @param $action_type
     * @param $action
     * @param int $type
     * @param string $info
     * @param string $order_sn
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function add_log($id,$currency, $action_type,$action,$type = 0,$info = '',$order_sn = ''){
        $currency_type = '';
        switch($currency){
            case 1:
                $currency_type = 'money';   //余额
                break;
            case 2:
                $currency_type = 'td_money';   //优惠卷
                break;
            case 3:
                $currency_type = 'cz_money';   //豆豆
                break;
        }
        $user  = Db::name('user')->where(['id'=>$id])->field(['mobile',$currency_type])->find();
        //gold_log
        $data_log = [
            'uid'             => $id,
            'username'        => $user['mobile'],
            'currency'        => $currency,   //货币类型  1余额
            'action_type'     => $action_type,  //来源
            'action'          => $action,  //变动金额
            'money_end'       => $user[$currency_type], //对应余额
            'order_sn'        => $order_sn, //订单号
            'info'            => $info,  //备注
            'add_time'        => time(),
            'type'            => $type,  //转入1  转出 0
        ];
        Db::name('money_profit')->insert($data_log);
        $log_id = Db::name('trade_order')->getLastInsID();
        return $log_id;
    }



}