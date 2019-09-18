<?php

namespace app\admin\controller;

use app\api\controller\Distinguish;
use phpass;
use app\models\User as model;
use app\models\UserAuth;
use app\models\CandyLog;
use app\models\WithdrawCash;
use app\models\WalletInfo;
use app\models\TeamGroup as Team;
use think\Db;
use app\helps\Auth;
use think\facade\Env;
use think\Validate;

/**
 * Class User
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
class User extends Base
{
    public function index()
    {
        $data = input('param.');
        $list = model::order('id DESC');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $data['start'] = $data['start'] . ' 00:00:00';
            $data['end'] = $data['end'] . ' 23:55:55';
            $list->where('created', '>=', $data['start']);
            $list->where('created', '<=', $data['end']);
        }
        if (isset($data['mobile']) && $data['mobile']) {
            if (isset($data['type']) && $data['type']) {
                if($data['type'] == 2){
                    $parent_id = Db::name('user')->where(['mobile'=>$data['mobile']])->value('parent_id');
                    if(!$parent_id){
                        $parent_id = Db::name('user')->where(['id'=>$data['mobile']])->value('parent_id');
                    }
                    $list->where('id','eq',$parent_id);
                }else if($data['type'] == 1){
                    $user_id = Db::name('user')->where(['mobile'=>$data['mobile']])->value('id');
                    if(!$user_id){
                        $user_id = $data['mobile'];
                    }
                    $list->where('parent_id','eq',$user_id);
                }
            }else{
                $list->where('mobile|id', 'like', '%' . $data['mobile'] . '%');
            }
        }


        $lists = $list->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function ($item){
                $info =  Db::name('city')->where(['id'=> $item['agent_id'] ])->field('cityname,type,agent_num')->find();
                if($info['type'] == 1){
                    $level = '省代';
                }
                if($info['type'] == 2){
                    $level = '市代';
                }
                if($info['type'] == 3){
                    $level = '县/区代';
                }
                $item['agent_id'] = !empty( $item['agent_id'])? $info['cityname']:'—';
                $item['agent_level'] = $item['agent_id'] != '—'? $level:'';

                if($item['level_end_time'] > time()){
                    $item['level_start_time'] = date('Y-m-d H:i:s',$item['level_start_time']);
                    $item['level_end_time'] = date('Y-m-d H:i:s',$item['level_end_time']);
                }else{
                    $item['level_start_time'] =0;
                    $item['level_end_time'] =0;
                }
                $item['zhi_x'] = Db::name('user')->where(['parent_id'=>$item['id']])->count();
                $item['zhi_vip'] = Db::name('user')->where(['parent_id'=>$item['id'],'level'=>1])->count();
                $item['zhi_p'] = Db::name('user')->where(['parent_id'=>$item['id'],'level'=>0])->count();

                return $item;
            });
        return view('', [
            'list' => $lists
        ]);
    }



    public function UserAdd()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证登陆密码
            $ids = session('admin_id');
            $admin = Db::name('admin')->where(['id'=>$ids])->find();
            #验证密码
            $passwordcheck = $this->checkPassword($data['passwords'],$admin['entry'],2,$admin['password']);
            if (!$passwordcheck) {
                return json(['status' => 'n', 'info' => '操作密码错误']);
            }

            $db = new model;
            $mobile = $db->where('mobile', $data['mobile'])->find();
            if (!empty($mobile) && $mobile['id'] != $data['id']) {
                return ['status' => 'n', 'info' => '该手机号码已被使用'];
            }
            $phpassStrength = new phpass\PasswordHash(8, false);
            $user = Db::name('user')->where(['id'=>$data['id']])->value('agent_id');
            $city = Db::name('city')->where(['id'=>$data['agent_id']])->find();
            $province_num = get_config('team','province_num');
            $city_num = get_config('team','city_num');
            $area_num = get_config('team','area_num');
            if(intval($user) != intval($data['agent_id'])){
                if($city['type'] == 1){
                    if($city['agent_num'] == intval($province_num)){
                        return ['status' => 'n', 'info' => '该省代人数已满，请选择其他地区!'];
                    }
                }
                if($city['type'] == 2){
                    if($city['agent_num'] == intval($city_num)){
                        return ['status' => 'n', 'info' => '该市代人数已满，请选择其他地区!'];
                    }
                }
                if($city['type'] == 3){
                    if($city['agent_num'] == intval($area_num)){
                        return ['status' => 'n', 'info' => '该县/区代人数已满，请选择其他地区!'];
                    }
                }
            }
            if(!empty($user)){
                Db::name('city')->where(['id'=>$user])->setDec('agent_num',1);
            }
            //改变区域
            Db::name('city')->where(['id'=>$data['agent_id']])->setInc('agent_num',1);

            if ($data['id'] > 0) {
                $rs = $db->find($data['id']);
                if($data['agent_id'] == 1){
                    $data['agent_id'] = 0;
                }
                $rs->agent_id = $data['agent_id'];
                $rs->name = $data['name'];
                $rs->mobile = $data['mobile'];

                if (!empty($data['trade'])) {
                    $rs->trade = $phpassStrength->HashPassword($data['trade']);
                }
                if (!empty($data['password'])) {
                    $rs->password =  $phpassStrength->HashPassword($data['password']);
                }
                $result = $rs->save();
            } else {
                unset($data['id']);
                $result = $db->save($data);
            }
            if ($result) {
                return ['status' => 'y', 'info' => '操作成功'];
            } else {
                return ['status' => 'n', 'info' => '操作失败!'];
            }
        } else {
            $id = input('id');
            $row = (new model)->where('id', $id)->find();
            $agent = Db::name('city')->select();
            return view('', ['row' => $row,'agent'=>city_merge($agent)]);
        }
    }
    public function userRecharge()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $rule = [
                'sel1'      => 'require',
                'sel2'      => 'require',
                'sel3'      => 'require',
                'num'       => 'require|number',
                'password'  => 'require',
                'id'        => 'require|number',
            ];
            $msg = [
                'sel1.require'     => '错误操作',
                'sel2.require'     => '错误操作',
                'sel3.require'     => '错误操作',
                'num.require'      => '请输入数量',
                'num.number'       => '请输入正确数量',
                'password.require' => '请输入密码',
                'id.require'       => '错误操作',
                'id.number'        => '错误操作',
            ];
            $data = [
                'sel1'       => $data['sel1'],
                'sel2'       => $data['sel2'],
                'sel3'       => $data['sel3'],
                'num'        => $data['num'],
                'password'   => $data['password'],
                'id'         => $data['id'],
            ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result){
                 return ['status'=>'n','info'=>$validate->getError()];
            }
            if ($data['id'] > 0) {
                Db::startTrans();
                try{
                    //验证登陆密码
                    $ids = session('admin_id');
                    $admin = Db::name('admin')->where(['id'=>$ids])->find();
                    #验证密码
                    $passwordcheck = $this->checkPassword($data['password'],$admin['entry'],2,$admin['password']);
                    if (!$passwordcheck) {
                        return json(['status' => 'n', 'info' => '操作密码错误']);
                    }
                    if($data['sel1'] == 1){
                        $moneytype = 'money';
                    }elseif ($data['sel1'] == 2){
                        $moneytype = 'td_money';
                    }elseif ($data['sel1'] == 3){
                        $moneytype = 'cz_money';
                    }
                    $type = 0;
                    if($data['sel3'] == 1){  //1增加
                        $type = 1;
                        Db::name('user')->where(['id'=>$data['id']])->setInc($moneytype,floatval($data['num']));
                        $this->operation_add('/user/userRecharge','充值',$data['id'],$data['id'],floatval($data['num']),currencytype($data['sel1']));
                    }elseif($data['sel3'] == 2) {  //2扣除
                        $user = Db::name('user')->where(['id'=>$data['id']])->find();
                        if($user[$moneytype] >= floatval($data['num']) && floatval($data['num']) > 0){
                            Db::name('user')->where(['id'=>$data['id']])->setDec($moneytype,floatval($data['num']));
                            $this->operation_add('/user/userRecharge','扣除',$data['id'],$data['id'],floatval($data['num']),currencytype($data['sel1']));
                        }else{
                            return json(['status' => 'n', 'info' => '请输入正确的数量']);
                        }
                    }
                    $this->add_log($data['id'],$data['sel1'],$data['sel2'],$data['num'],$type);
                     //提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                }
            } else {
                return ['status'=>'n','info'=>'错误操作'];
            }
            if ($result) {
                return ['status' => 'y', 'info' => '操作成功'];
            } else {
                return ['status' => 'n', 'info' => '操作失败!'];
            }
        }else{
            $id = input('id');
            return $this->fetch('',['id'=>$id]);
        }
    }

    public function usermoney(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $rule = [
                'money'      => 'require|number',
                'id'        => 'require|number',
            ];
            $msg = [
                'id.require'       => '错误操作',
                'id.number'        => '错误操作',
                'money.require'    => '请输入金额',
                'money.number'     => '请输入正确的数量',
            ];
            $data = [
                'money'      => $data['money'],
                'id'         => $data['id'],
            ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result){
                return ['status'=>'n','info'=>$validate->getError()];
            }
            Db::startTrans();
            try{
                $team = new \app\api\controller\Team();
                $parentid = Db::name('user')->where(['id'=>$data['id']])->value('parent_id');
                $res =  $team->joinTeam($data['id'],$parentid,$data['money']);

                if($res){
                    if($res['status'] == 'y'){
                        Db::name('user')->where(['id'=>$data['id']])->update(['benefit'=>1]);
                    }
                    return $res;
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        }
    }
    public function userDel()
    {
        $result = model::destroy(trim(input('id')), ',');
        $where['user_id'] = ['in',trim(input('id')), ','];
        Db::name('team_group')->where($where)->delete();
        return ['status' => $result ? 'y' : 'n', 'info' => '删除' . ($result ? '成功' : '失败!')];
    }



    public function tree()
    {
        return view('user/tree', [
            'count' => Model::count()
        ]);
    }

    public function treeData()
    {
        $data = $this->request->get();
        if (!isset($data['id'])){
            $data['id'] = 0;
        }

        $results = Db::table('tb_user')->where('parent_id',  $data['id'])->select();

        $arr = [];
        foreach ($results as $k => $result) {
            $arr[$k]['id'] = $result['id'];
            //if ($result['block'] == 0) {
                $arr[$k]['name'] = $result['mobile'];
//            } else {
//                $arr[$k]['name'] = $result['mobile'] . '(' . ($result['block'] == 1 ? 'A' : 'B') . ')';
//            }
            $arr[$k]['isParent'] = 'true';
        }
        return json($arr);
    }

    public function user_auth()
    {
        $data = input('param.');
        $list = UserAuth::order('created DESC');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $data['start'] = $data['start'] . ' 00:00:00';
            $data['end'] = $data['end'] . ' 23:55:55';
            $list->where('created', '>=', $data['start']);
            $list->where('created', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['user_id|name|card_no' => ['eq', $data['word']]]);
        }
        if (!empty($data['status'])) {
            if ($data['status'] == 3) {
                $list->where(['status' => 'y']);
            } else {
                $list->where(['status' => $data['status']]);
            }
        }
        $lists = $list->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function($item){
                $item['username'] = Db::name('user')->where(['id'=>$item['user_id']])->value('name');
                $item['phone'] = Db::name('user')->where(['id'=>$item['user_id']])->value('mobile');
                $item['obverse'] = $item['obverse'].'?x-oss-process=image/resize,m_fixed,h_150,w_200/auto-orient,1';
                $item['reverse'] = $item['reverse'].'?x-oss-process=image/resize,m_fixed,h_150,w_200/auto-orient,1';
                return $item;
            });
        return view('', [
            'list' => $lists,
            'info'=>config('info.authentication')
        ]);
    }

    public function user_auth_update(){
        $dist = new Distinguish();
        $id = input('id');
        $auth =Db::name('user_auth')->where(['id'=>$id])->find();
        $userface =$auth['userface'];
        $card_no =$auth['card_no'];
        $name = $auth['name'];
        $yz = $dist::index($userface,$auth['reverse'],$card_no,$name);
       
        if($yz['error_code'] != 0){
        	return json(['status' => 'n', 'info' => $yz['error_msg']]);
        }
        Db::name('user_auth')->where(['id'=>$id])->update(['score'=>floatval($yz['result']['score'])]);
        return json(['status' => 'y', 'info' => '已刷新']);
    }

    /*实名认证修改状态*/
    public function user_auth_sta()
    {
        if ($this->request->isPost()) {
            $id = input('id');
            $data['status'] = input('status');
            if ($data['status'] == 1) {
                Db::startTrans();
                try {
                    (new UserAuth)->save($data, ['user_id' => $id]);
                    $msg = '恭喜您，您申请的身份认证信息已成功通过审核~';
                    $title = '身份认证信息～';
                    $this->sendInfo($id,$title,$msg);
                    Db::commit();
                    $this->operation_add('/user/user_auth_sta','身份认证',$id,$id,'','通过认证');
                    return json(['status' => 'y', 'info' => '操作成功']);
                } catch (\Exception $exception) {
                    Db::rollback();
                    return json(['status' => 'n', 'info' => $exception->getMessage()]);
                }

            } else {
                $msg = '很遗憾，您申请的身份认证信息暂未通过审核,申请时请多留意提交的信息是否正确~';
                $title = '身份认证信息～';
                $this->sendInfo($id,$title,$msg);
                $result = (new UserAuth)->save($data, ['user_id' => $id]);
                $this->operation_add('/user/user_auth_sta','身份认证',$id,$id,'','不通过认证');
            }
            if ($result) {
                return json(['status' => 'y', 'info' => '操作成功']);
            } else {
                return json(['status' => 'n', 'info' => '操作失败']);
            }
        }
    }

    /*实名认证删除*/
    public function user_auth_del()
    {
        $id = input('id');
        $result = Db::name('user_auth')->where('user_id', $id)->delete();
        return ['status' => $result ? 'y' : 'n', 'info' => '删除' . ($result ? '成功' : '失败!')];
    }

    public function user_company()
    {
        $data = input('param.');
        $list = Db::name('user_company')->order('id desc');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $data['start'] = $data['start'] . ' 00:00:00';
            $data['end'] = $data['end'] . ' 23:55:55';
            $list->where('created', '>=', $data['start']);
            $list->where('created', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['user_id' => ['eq', $data['word']]]);
        }
        if (!empty($data['status'])) {
            if ($data['status'] == 3) {
                $list->where(['status' => 'y']);
            } else {
                $list->where(['status' => $data['status']]);
            }
        }
        $lists = $list->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
        ->each(function($item){
            $item['username'] = Db::name('user')->where(['id'=>$item['user_id']])->value('name');
            $item['phone'] = Db::name('user')->where(['id'=>$item['user_id']])->value('mobile');
            return $item;
        });
        return view('', [
            'list' => $lists,
            'info'=>config('info.authentication')
        ]);
    }

    /*实名认证修改状态*/
    public function user_company_sta()
    {
        if ($this->request->isPost()) {
            $id = input('id');
            $data['status'] = input('status');
            if ($data['status'] == 1) {
                Db::startTrans();
                try {
                    $result =  Db::name('user_company')->where(['user_id' => $id])->update($data);
                    $msg = '恭喜您，您申请的企业认证已通过';
                    $title = '企业认证信息～';
                    $this->sendInfo($id,$title,$msg);
                    Db::commit();
                    return json(['status' => 'y', 'info' => '操作成功']);
                } catch (\Exception $exception) {
                    Db::rollback();
                    return json(['status' => 'n', 'info' => $exception->getMessage()]);
                }

            } else {
                $result =  Db::name('user_company')->where(['user_id' => $id])->update($data);
                $msg = '很遗憾，您申请的企业认证未通过审核,申请时请多留意提交的信息是否正确~';
                $title = '企业认证信息～';
                $this->sendInfo($id,$title,$msg);
            }
            if ($result) {
                return json(['status' => 'y', 'info' => '操作成功']);
            } else {
                return json(['status' => 'n', 'info' => '操作失败']);
            }
        }
    }

    /*实名认证删除*/
    public function user_company_del()
    {
        $id = input('id');
        $result = Db::name('user_company')->where('user_id', $id)->delete();
        return ['status' => $result ? 'y' : 'n', 'info' => '删除' . ($result ? '成功' : '失败!')];
    }

    /**
     * 团队列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function team(){
        $data = input('param.');
        $list = Db::name('team');
        if (isset($data['mobile']) && $data['mobile']) {
            $list->where(['user_id' => ['eq', $data['mobile']]]);
        }
        $lists = $list->paginate(Env::get('app.page'), false, ['query' => $this->request->param()]);
        return view('', [
            'list' => $lists
        ]);
    }

    /**
     * 修改等级
     * @return array|\think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function teamAdd()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $db = Db::name('team');
            $rs = $db->find($data['id']);
            if($rs){
                $update['bd_level'] = $data['level'];
                $update['bd_num'] = $data['bd_num'];
                $result = $db->update($update);
            }else{
                $result = false;
            }

            if ($result) {
                return ['status' => 'y', 'info' => '操作成功'];
            } else {
                return ['status' => 'n', 'info' => '操作失败!'];
            }
        } else {
            $id = input('id');
            $row = Db::name('team')->where('id', $id)->find();
            $level_type = Db::name('team')->distinct (true)->field('bd_level')->select();
            return view('teamadd', ['row' => $row,'level_type'=>$level_type]);
        }
    }

    /**
     * 团队收益
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function teamProfit(){
        $data = input('param.');
        $list = Db::name('team_lineup');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $data['start'] = $data['start'] . ' 00:00:00';
            $data['end'] = $data['end'] . ' 23:55:55';
            $list->where('add_time', '>=', $data['start']);
            $list->where('add_time', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['uid' => ['eq', $data['word']]]);
        }
        $lists = $list->order('id desc')
            ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function ($item){
                $item['city'] = $this->getUserCity($item['uid'],$item['level']);
                return $item;
            });
        return view('teamprofit', [
            'list' => $lists
        ]);
    }
    protected function getUserCity($userid, $level){
        if($level == 1){
            $province = Db::name('user')->where(['id'=>$userid])->value('province');
            return Db::name('city')->where(['id'=>$province])->value('cityname');
        } if($level == 2){
            $city = Db::name('user')->where(['id'=>$userid])->value('city');
            return Db::name('city')->where(['id'=>$city])->value('cityname');
        } if($level == 3){
            $area = Db::name('user')->where(['id'=>$userid])->value('area');
            return Db::name('city')->where(['id'=>$area])->value('cityname');
        }
    }

    /**
     * 余额收益
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function moneyProfit(){
        $data = input('param.');
        $list = Db::name('money_profit');
        if(isset($data['currency'])  && $data['currency']){
            $list->where(['currency' => ['eq', $data['currency']]]);
        }
        if(isset($data['action_type'])  && $data['action_type']){
            $list->where(['action_type' => ['eq', $data['action_type']]]);
        }
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $data['start'] = strtotime($data['start'] . ' 00:00:00');
            $data['end'] = strtotime($data['end'] . ' 23:55:55');
            $list->where('add_time', '>=', $data['start']);
            $list->where('add_time', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['uid|username' => ['eq', $data['word']]]);
        }

        $lists = $list
            ->order('id desc')
            ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function($item){
                if(empty($item['username'])){
                    $item['username'] = '未设置';
                }
                $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                return $item;
            });
        $currency_type = Db::name('money_profit')->distinct (true)->field('currency')->select();
        $action_type = Db::name('money_profit')->distinct (true)->field('action_type')->select();
        return view('moneyprofit', [
            'list' => $lists,
            'currency_type' => $currency_type,
            'action_type' => $action_type
        ]);
    }

    /**
     * 推荐客源
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recommend(){
        $data = input('param.');
        $list = Db::name('user_recommend');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $time_max = strtotime($data['end']);
            $time_min = strtotime($data['start']);
            $data['start'] = $time_min;
            $data['end'] =  $time_max;
            $list->where('add_time', '>=', $data['start']);
            $list->where('add_time', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['name|userid' => ['eq', $data['word']]]);
        }
        $lists = $list
            ->order('id desc')
            ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function($item){
                $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                return $item;
            });
        $this->assign('list',$lists);
        $this->assign('page',$lists->render());
        $this->assign('count',$lists->count());
        return $this->fetch();
    }



    /**
     * 提现申请
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function money_apply(){
        $data = input('param.');
        $list = Db::name('money_apply');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $time_max = strtotime($data['end']);
            $time_min = strtotime($data['start']);
            $data['start'] = $time_min;
            $data['end'] =  $time_max;
            $list->where('add_time', '>=', $data['start']);
            $list->where('add_time', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['userid' => ['eq', $data['word']]]);
        }

        $lists = $list
            ->alias('a')
            ->order('a.id desc')
            ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function($item){
                $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                $item['all_money'] = floatval($item['money']);
                $item['usdt'] = floatval($item['money'] *  get_config('base','usdt') /100);
                $item['money'] = $item['money'] - ($item['money'] *  get_config('base','usdt') /100);
                return $item;
            });
        $this->assign('list',$lists);
        $this->assign('page',$lists->render());
        $this->assign('count',$lists->count());
        return $this->fetch();
    }

    /*提现认证修改状态*/
    public function money_apply_status()
    {
        if ($this->request->isPost()) {
            $id = input('id');
            $data['status'] = input('status');


            Db::startTrans();
            try {
                $apply = Db::name('money_apply')->where(['id'=>$id])->find();
                if($data['status'] == 1){  //驳回
//                    $sxf = $apply['money'] * $per /100;
                    Db::name('user')->where(['id'=>$apply['userid']])->setInc('money',$apply['money']);
                    $this->add_log($apply['userid'],1,8,$apply['money']);
                    $msg = '您的申请提现:'.$apply['money'].'已被驳回,详情请资讯客服~~ ';
                    $title = '提现信息～';
                    $this->sendInfo($apply['userid'],$title,$msg);
                    $this->operation_add('/user/money_apply_status','提现审核',$id,$apply['userid'],$apply['money'],'驳回提现');

                }else{
                    $msg = '您的申请提现:'.$apply['money'].'已通过,1-3个工作日内到账~';
                    $title = '提现信息～';
                    $this->sendInfo($apply['userid'],$title,$msg);
                    $this->operation_add('/user/money_apply_status','提现审核',$id,$apply['userid'],$apply['money'],'通过审核');

                }
                $res =  Db::name('money_apply')->where(['id' => $id])->update($data);
                if(!$res){
                    return json(['status' => 'n', 'info' => '操作失败']);
                }
                Db::commit();
                return json(['status' => 'y', 'info' => '操作成功']);
            } catch (\Exception $exception) {
                Db::rollback();
                return json(['status' => 'n', 'info' => $exception->getMessage()]);
            }
        }
    }

    /************技能认证*****************/
    public function user_skill()
    {
        $data = input('param.');
        $list = Db::name('user_skill')->order('id desc');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $data['start'] = $data['start'] . ' 00:00:00';
            $data['end'] = $data['end'] . ' 23:55:55';
            $list->where('add_time', '>=', strtotime($data['start']));
            $list->where('add_time', '<=', strtotime($data['end']));
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['user_id' => ['eq', $data['word']]]);
        }
        if (!empty($data['status'])) {
            if ($data['status'] == 3) {
                $list->where(['status' => 0]);
            } else {
                $list->where(['status' => $data['status']]);
            }
        }
        $lists = $list->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
        ->each(function ($item){
            $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
            $item['username'] = Db::name('user')->where(['id'=>$item['user_id']])->value('name');
            $item['phone'] = Db::name('user')->where(['id'=>$item['user_id']])->value('mobile');
            $item['type'] = Db::name('product_category')->where(['cat_id'=>$item['type']])->value('cat_name');
            $item['img'] = empty($item['img']) || !is_array(json_decode($item['img']))?[]:json_decode($item['img']);
            return $item;
        });
        return view('user_skill', [
            'list' => $lists,
            'info'=>config('info.authentication')
        ]);
    }



    /*实名认证修改状态*/
    public function user_skill_sta()
    {
        if ($this->request->isPost()) {
            $id = input('id');
            $data['status'] = input('status');
            $type = Db::name('user_skill')->where(['id' => $id])->field('type,user_id')->find();
            $cat_name = Db::name('product_category')->where(['cat_id' => $type['type']])->value('cat_name');
            if ($data['status'] == 1) {
                Db::startTrans();
                try {
                    Db::name('user_skill')->where(['id' => $id])->update($data);
                    $msg = '恭喜您，您申请的技能:'.$cat_name.' 的认证已通过审核，快去接单吧~';
                    $title = '技能认证信息～';
                    $this->sendInfo($type['user_id'],$title,$msg);
                    Db::commit();
                    return json(['status' => 'y', 'info' => '操作成功']);
                } catch (\Exception $exception) {
                    Db::rollback();
                    return json(['status' => 'n', 'info' => $exception->getMessage()]);
                }

            } else {
                $msg = '很遗憾，您申请的技能:'.$cat_name.'暂示通过审核，申请时请多留意提交的信息是否正确~';
                $title = '技能认证信息～';
                $this->sendInfo($type['user_id'],$title,$msg);
                $result = Db::name('user_skill')->where(['id' => $id])->update($data);
            }
            if ($result) {
                return json(['status' => 'y', 'info' => '操作成功']);
            } else {
                return json(['status' => 'n', 'info' => '操作失败']);
            }
        }
    }

    /*认证删除*/
    public function user_skill_del()
    {
        $id = input('id');
        $result = Db::name('user_skill')->where('id', $id)->delete();
        return ['status' => $result ? 'y' : 'n', 'info' => '删除' . ($result ? '成功' : '失败!')];
    }

    /**
     * 系统认证消息
     * @param $user_id
     * @param $content
     * @return int|string
     */
    protected function sendInfo($user_id,$tile,$content){
        $row = [
            'type' => 'notice',
            'content' => $content,
            'title' =>$tile,
            'user_id' => $user_id,
            'add_time' => time(),
            'admin' => 'admin',
        ];
       return Db::name('info')->insert($row);
    }


    /************技能认证*****************/
    public function head_auth()
    {
        $data = input('param.');
        $list = Db::name('user')->order('id desc');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $data['start'] = $data['start'] . ' 00:00:00';
            $data['end'] = $data['end'] . ' 23:55:55';
            $list->where('add_time', '>=', strtotime($data['start']));
            $list->where('add_time', '<=', strtotime($data['end']));
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['id|name' => ['eq', $data['word']]]);
        }
        if (!empty($data['status'])) {
            if ($data['status'] == 3) {
                $list->where(['status' => 0]);
            } else {
                $list->where(['status' => $data['status']]);
            }
        }
        $lists = $list->where('img_apply','neq','')->field('id,name,avatar,img_apply')->paginate(Env::get('app.page'), false, ['query' => $this->request->param()]);
        return view('head_auth', [
            'list' => $lists,
            'info'=>config('info.authentication')
        ]);
    }

    public function head_status(){
        if($this->request->isPost()) {
            $id = input('id');
            $status = input('status');
            $user = Db::name('user')->where(['id' => $id])->find();

            if ($status == 1) {
                Db::startTrans();
                try {
                    Db::name('user')->where(['id' => $id])->update(['avatar'=>$user['img_apply'],'img_apply'=>'']);
                    Db::commit();
                    return json(['status' => 'y', 'info' => '操作成功']);
                } catch (\Exception $exception) {
                    Db::rollback();
                    return json(['status' => 'n', 'info' => $exception->getMessage()]);
                }
            } else {
                Db::name('user')->where(['id' => $id])->update(['img_apply'=>'']);
                return json(['status' => 'y', 'info' => '已驳回']);
            }
        }
    }
   
}