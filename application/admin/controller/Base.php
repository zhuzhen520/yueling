<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use phpass;

/**
 * Class Base
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
class Base extends Controller
{
    protected function get_rand_str($len = 6)
    {
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'), array('$', '@', '#', '%', '&'));
        shuffle($arr);
        $sub_arr = array_slice($arr, 0, $len);
        return implode('', $sub_arr);
    }

    /*切换显示*/
    public function toggle_status()
    {
        $id = input('id');
        $status = input('status');
        $table = input('table');
        $field = input('field');
        $db = Db::name($table);
        $pk = $db->getPk(); #获取主键名称
        $where[$pk] = $id;
        $result = $db->where($where)->setField($field, $status);
        echo $result ? 1 : 0;
    }

    /**
     * 密码
     * @param $password string  输入密码
     * @param $entry  string  编码
     * @param int $type  int 类型  1创建密码   其他验证密码
     * @param string $old_password  string 数据库密码
     * @return bool|string
     */
    public function checkPassword($password,$entry, $type = 1, $old_password = ''){
        $phpassStrength = new phpass\PasswordHash(8, false);
        if($type == 1){
            #创建密码
            $res  = $phpassStrength->HashPassword($password.md5($entry));
        }else{
            #验证密码
            $res = $phpassStrength->CheckPassword($password.md5($entry), $old_password);
        }
         return $res;

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
    protected function add_log($id,$currency, $action_type,$action,$type = 1,$info = '',$order_sn = ''){
        $currency_type = '';
        switch($currency){
            case 1:
                $currency_type = 'money';   //余额
                break;
            case 2:
                $currency_type = 'td_money';   //卷
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
        return true;
    }

    protected function operation_add($Interface,$jk_name,$Interface_id,$Interface_user_id='',$money ="",$info=''){

        $name = Db::name('admin')->where(['id'=>session('admin_id')])->value('username');
        $user_id = session('admin_id');
        $data_log = [
            'user_id'            => $user_id,
            'name'               => $name,
            'add_time'           => time(),
            'Interface'          => $Interface,
            'jk_name'            => $jk_name,
            'money'              => $money,
            'info'               => $info,
            'Interface_user_id'  => $Interface_user_id,
            'Interface_id'       => $Interface_id,
            'ip'                 => $this->request->ip(),
        ];
        Db::name('interface_log')->insert($data_log);
        return true;
    }
}