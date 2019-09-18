<?php

namespace app\api\controller;

use phpass;
use think\Db;
use think\facade\Env;
use think\Validate;

/**
 * Class Vip
 * 会员
 * @package app\admin\controller
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))
 */
class Vip extends Base
{
    /**
     * 购买会员
     */
    public function vipApply(){
        if($this->request->isPost()){
            $type =  input('type');
            if(empty(intval($type))){
                return $this->fail('信息错误',false);
            }
            $order_sn = $this->vipOrder( get_config('user','vipmoney'), 5, $type);   //下订单
            $pay = new Pay();
            #支付类型
            if($type == 1){
                $res =  $pay->payAliPay($order_sn,'http://'.Env::get('app.api_url').'/Callback/alipay');
            }else{
                $res =  $pay->wechatPay($order_sn,'http://'.Env::get('app.api_url').'/Callback/wechat');
            }
            return $this->succ($res);
        }else{
            $vip_price = get_config('user','vipmoney');
            return $this->succ(['vip_price'=>$vip_price]);
        }
    }

    /**
     * 用户充值
     */
    public function moneyRecharge(){
        if($this->request->isPost()){
            $data = $this->request->param();

            $rule = [
                'money'     => 'require',
                'type'      => 'require',
            ];
            $msg = [
                'money.require'    => '请输入金额',
                'type.require'     => '信息错误',
            ];
            $data = [
                'money'  => $data['money'],
                'type'   => $data['type'],
            ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result){
                return $this->fail($validate->getError(),false);
            }
            $order_sn = $this->vipOrder($data['money'], 21, $data['type']);   //下订单
            $pay = new Pay();
            #支付类型
            if($data['type'] == 1){
                $res =  $pay->payAliPay($order_sn,'http://'.Env::get('app.api_url').'/Callback/alipayrecharge');
            }else{
                $res =  $pay->wechatPay($order_sn,'http://'.Env::get('app.api_url').'/Callback/wechatrecharge');
            }
            return $this->succ($res);
        }
    }



    /**
     * 是否有 X个直推会员
     * @param $user_id
     * @return bool
     */
    public function vilidateVip($user_id){
        $next_user = Db::name('user')->where(['parent_id'=>$user_id,'level'=>1])->count();
        $zhiuser = get_config('user','zhiuser');
        if($next_user >= intval($zhiuser)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 就否是30W以内的会员
     * @param $user_id
     * @return bool
     */
    public function userVip($user_id){
        $next_user = Db::name('user')->where(['id'=>$user_id,'level'=>1,'in_reward'=>1])->count();
        if($next_user){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 下订单
     */
    protected function vipOrder($money, $pay_name,$type = 1){
        $str = date('Ymds').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $arr = [
            'user_id'=>$this->request->user_id,
            'money'=> $money,
            'add_time'=>time(),
            'status'=>0,
            'order_no'=>$str,
            'pay_name'=>$pay_name,  //21 充值
            'pay_type'=>$type  //支付类型
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
    /**
     * 加入会员   ---------------------没有返回值
     */
    public function joinVip($userid){
        $back_bean = get_config('user','bean');  //用户返豆数量
        $zhibean = get_config('user','zhibean'); //直推会员奖励
        $jianbean = get_config('user','jianbean');  //间推会员奖励

        Db::startTrans();
        try{
            //加入会员返当前用户推广豆
            Db::name('user')->where(['id'=>$userid])->update(['level'=>1]);
            $level_start_time = $this->userinfo($userid)['level_start_time'];
            if(empty($level_start_time)){   #如果用户没有充值 过会员.第一次可以返豆， 第二次之后没有返豆也不会给上级产生效益豆
                Db::name('user')->where(['id'=>$userid])->setInc('cz_money',$back_bean);
                $this->add_log($userid,3,1,$back_bean);
                $parent = $this->getParentId($userid);
                if(!empty($parent['zhi']) && isset($parent['zhi'])){
                    //直推收益
                    Db::name('user')->where(['id'=>$parent['zhi']])->setInc('cz_money',$zhibean);
                    $this->add_log($parent['zhi'],3,2,$zhibean);
                }
                if(!empty($parent['jian']) && isset($parent['jian'])){
                    //直推收益
                    Db::name('user')->where(['id'=>$parent['jian']])->setInc('cz_money',$jianbean);
                    $this->add_log($parent['jian'],3,3,$jianbean);
                }
            }
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

    }

    /**
     * 直接发展100人以上（含100人），包括间接发展总数达500人（含500人）以上的，将来10年期间免费使用软件
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function updateLevelTime($user_id){
        #$this->getUserInfo($user_id, 'level_start_time')
            $user = $this->userinfo($user_id);
            $son = $this->getSonCount($user_id);
            $user_zhi = get_config('user','zhinum');  //直接发展
            $user_jian = get_config('user','jiannum');  //间接发展
            //判断用户是否直推发展了直推$user_zhi上人，直推+间推$user_jian以上人  并且还是会员
            if($son['zhi'] >= $user_zhi  && $son['jian'] + $son['zhi']  >= $user_jian && $user['in_reward'] == 1 && $user['level'] == 1){
                $vip_year = get_config('user','year');   //会员免费使用时间
                Db::startTrans();
                try{

                    $start_time = date('Y-m-d H:i:s',time());    //未设计 用户开始时间
                    $end_time = date('Y-m-d H:i:s',strtotime("+".$vip_year." year"));
                    Db::name('user')->where(['id'=>$this->request->user_id])->update([
                        'level_start_time'=>strtotime($start_time),
                        'level_end_time'=>strtotime($end_time),
                    ]);
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                }
             }

    }

    /**
     * 直推 数量，  间推数量   下级直推VIP数量              ---------------------------必须是有效会员
     * @param $userid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getSonCount($userid){
        $son_count = Db::name('user')->where(['parent_id'=>$userid,'level'=>1])->field('id,parent_id')->select();
        $indirect_count = 0;
        foreach ($son_count as $key => $item){
            $indirect_count += Db::name('user')->where(['parent_id'=>$item['id'],'level'=>1])->count();
        }
        $arr = [
            'zhi'=>count($son_count),
            'jian'=>$indirect_count
        ];
        return $arr;
    }

    /**
     * 获取会员数量 并且 是30W以内的会员
     * @return float|string
     */
    public function getVipCount(){
        $count = Db::name('user')->where(['level'=>1,'in_reward'=>1])->count();
        return $count;
    }


    /**
     * 获取用户直推和间推
     * @param $userid
     * @return array
     */
    public function getParentId($userid){
        $parent_zhi = Db::name('user')->where(['id'=>$userid])->value('parent_id');
        $parent_jian = Db::name('user')->where(['id'=>$parent_zhi])->value('parent_id');
        $arr = [
            'zhi'=>$parent_zhi,
            'jian'=>$parent_jian
        ];
        return $arr;
    }


}