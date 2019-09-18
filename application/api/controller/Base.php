<?php

namespace app\api\controller;

use think\App;
use think\Controller;
use think\Db;
use phpass;
/**
 * Class Base
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
class Base extends Controller
{

    protected function succ($data = [], $msg = '', $status = 0)
    {
        $info = [
            'data' => $data,
            'msg' => $msg ? $msg: '成功',
            'status' => $status
        ];
        echo json_encode($info, JSON_UNESCAPED_UNICODE);
        die;
    }

    protected function fail($msg = 'base.error', $msg_replace = true, $status = 1, $data = null)
    {
        $info = [
            'data' => $data,
            'msg' => $msg_replace == false ? $msg : $this->language($msg),
            'status' => $status
        ];
        echo json_encode($info, JSON_UNESCAPED_UNICODE);
        die;
    }

    private function language($key)
    {
        $language = isset($GLOBALS['language']) ? $GLOBALS['language'] : 'cn';
        $config = config($language . '.');
        if (!strpos($key, '.')) {
            return $config[$key];
        }
        $keys = explode('.', $key);
        $arr = $config;
        array_walk($keys, function ($v) use (&$arr) {
            $arr = $arr[$v];
        });
        return $arr;
    }

    /**
     * 用户信息
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function userinfo($user_id = 0){
        if($user_id == 0){
            $user_id = $this->request->user_id;
        }
        $user = Db::name('user')
                ->field('id,parent_id,mobile,openid,address,name,avatar,money,cz_money,td_money,created,status,wx,level,level_start_time,level_end_time,in_reward,province,city,area,lng,lat')
                ->where(['id'=>$user_id])
                ->find();
        $user['money'] = floatval($user['money']);
        $user['cz_money'] = floatval($user['cz_money']);
        $user['td_money'] = floatval($user['td_money']);
        return $user;
    }

    /**
     * 获取用户值
     * @param $user_id int 用户id
     * @param $type  string  字段名
     * @return mixed
     */
    protected function getUserInfo($user_id,$type){
        $user = Db::name('user')->where(['id'=>$user_id])->value($type);
        return $user;
    }

//    protected function getCity($id){
//        $city = Db::name('city')->where(['id'=>$id])->value('cityname');
//        return $city;
//    }

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
    protected function add_log($id,$currency, $action_type,$action,$type = 1,$info = '',$order_sn = ''){
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
        $log_id = Db::name('money_profit')->getLastInsID();
        return $log_id;
    }

    /**
     * 密码
     * @param $password string  输入密码
     * @param $entry  string  编码
     * @param int $type  int 类型  1创建密码   其他验证密码
     * @param string $old_password  string 数据库密码
     * @return bool|string
     */
    public function checkPassword($password, $type = 1, $old_password = ''){
        $phpassStrength = new phpass\PasswordHash(8, false);
        if($type == 1){
            #创建密码
            $res  = $phpassStrength->HashPassword($password);
        }else{
            #验证密码
            $res = $phpassStrength->CheckPassword($password, $old_password);
        }

        return $res;

    }


    public function userIsLevel($user_id = 0){
        if(empty($user_id)){
            $user_id = $this->request->user_id;
            $msg = '请先购买会员';
        }else{
            $msg = '该用户未购买会员';
        }
        if($this->getUserInfo($user_id,'level') == 0){
            return $this->fail($msg,false,10);
        }else{
            return true;
        }
    }

    public function userIdent($user_id = 0){
        if(empty($user_id)){
            $user_id = $this->request->user_id;
            $msg = '请先进行身份认证';
        }else{
            $msg = '该用户未进行身份认证';
        }
        $user = Db::name('user_auth')->where('user_id','eq',$user_id)->where('status','eq',1)->find();
        if(empty($user)){
            return $this->fail($msg,false,10);
        }else{
            return true;
        }
    }

    public function sendInfo($content, $user_id, $title, $receipt_id,$voidtype= 0){
        $msg = [
            'type'=>'order',
            'content'=>$content,
            'admin'=>'admin',
            'user_id'=>$user_id,
            'title'=>$title,
            'add_time'=>time(),
            'status'=>0,
            'info'=>$receipt_id,
            'void_type'=>$voidtype
        ];
        Db::name('info')->insert($msg);
        return true;
    }

}