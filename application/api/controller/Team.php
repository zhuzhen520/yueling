<?php

namespace app\api\controller;

use app\helps\Redis;
use phpass;
use think\App;
use think\Db;

/**
 * Class Team
 * 团队
 * @package app\api\controller
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
class Team extends Base
{
    protected $redis;
    public function initialize()
    {
        $this->redis = new Redis();
    }

    /**
     * 省市区收益   计算省市区代理的收益
     * @param $money   double  平台收益的20%
     * @param $user_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function profitMoeny($money,$user_id){
        if(floatval($money) <= 0){
            return false;
        }
        $user = $this->userinfo($user_id);
        $province = $user['province'];
        $city = $user['city'];
        $area = $user['area'];
        if(empty($province) || empty($city) || empty($area)){
            return false;
        }
        //获取当前所有省代理
        $user_profit  = Db::name('user')->where('agent_id','in',"$province,$city,$area")->field('id,agent_id')->select();
        $province_pro = get_config('team','province_pro');
        $city_pro = get_config('team','city_pro');
        $area_pro = get_config('team','area_pro');
        $online = [];
        foreach ($user_profit as $key => $item){
            if(intval($item['agent_id']) == intval($province)){
                $online[] = [
                              'level'=>1,
                              'num'=>$money * ($province_pro/100),
                              'perfor'=>$province_pro/100,
                              'action_type'=>18
                            ];
            }if(intval($item['agent_id']) == intval($city)){
                $online[] = [
                                'level'=>2,
                                'num'=>$money * ($city_pro/100),
                                'perfor'=>$city_pro/100,
                                'action_type'=>19
                            ];
            }if(intval($item['agent_id']) == intval($area)){
                $online[] = [
                                'level'=>3,
                                'num'=>$money * ($area_pro/100),
                                'perfor'=>$area_pro/100,
                                'action_type'=>20
                            ];
            }
            $online[$key]['uid'] = $item['id'];
            $online[$key]['user_id'] = $user_id;
            $online[$key]['currency'] = 1;
            $online[$key]['add_time'] = date('Y-m-d H:i:s',time());
            if(empty($online[$key]['num'])){
                unset($online[$key]);
            }
         }
        // 启动事务
        Db::startTrans();
        try{
            Db::name('team_lineup')->insertAll($online);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

    }




}