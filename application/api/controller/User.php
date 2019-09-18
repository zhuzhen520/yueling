<?php

namespace app\api\controller;

use app\helps\Auth;
use app\helps\Msg;
use app\helps\Oss;
use app\helps\Rds;
use app\models\Info;
use app\extend\QRcode;
use GuzzleHttp\Client;
use think\Cache;
use think\db\Where;
use think\facade\Env;
use think\Loader;
use think\Validate;
use app\helps\Mns;
use think\Db;

/**
 * Class User
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
class User extends Base
{
    /**
     * 用户信息
     */
    public function index()
    {
        $info = $this->userinfo();
        $info['level_start_time'] = date('Y-m-d', $info['level_start_time']);
        $info['level_end_time'] = date('Y-m-d', $info['level_end_time']);
        return $this->succ($info);
    }

    public function login()
    {
        $data = $this->request->param();
        $validate = new Validate(
            ['mobile' => 'require|number', 'password' => 'require'],
            ['mobile.require' => '请填写手机号', 'password.require' => '请输入密码']
        );
        if (!$validate->check($data)) {
            return $this->fail($validate->getError(), false);
        }

        $row = Db::table('tb_user')->where('mobile', $data['mobile'])->find();
        if ($row) {
            $checkPassword = $this->checkPassword($data['password'], 2, $row['password']);
            if ($checkPassword) {
                return $this->succ(['token' => Auth::Auth($row['id']), 'first' => 0], '登录成功');
            } else {
                return $this->fail('手机号/密码错误', false);
            }
        } else {
            return $this->fail('请先注册', false);
        }


        return $this->fail('base.code');
    }

    public function register()
    {
        $data = $this->request->param();
        $validate = new Validate(
            [
                'mobile' => 'require|number',
                'parent' => 'require',
                'code' => 'require',
                'password' => 'require',
                'province' => 'require|number',
                'city' => 'require|number',
                'area' => 'require|number',
                'address' => 'require',
            ],
            [
                'mobile.require' => '请填写手机号',
                'mobile.number' => '手机号格式错误',
                'parent.require' => '请填写人推荐手机号',
                'code.require' => '请填写手机验证码',
                'password.require' => '请输入密码',
                'province.require' => '请选择地区',
                'province.number' => '信息错误',
                'city.require' => '请选择地区',
                'city.number' => '信息错误',
                'area.require' => '请选择地区',
                'area.number' => '信息错误',
                'address.require' => '请输入详细地址',
            ]
        );
        if (!$validate->check($data)) {
            return $this->fail($validate->getError(), false);
        }
//        if($data['password'] != $data['cfpassword']){
//            return $this->fail('两次密码不一致',false);
//        }
        if ((new Msg())->checkCode($data['code'], $data['mobile'])) {
            $row = Db::table('tb_user')->where('mobile', $data['mobile'])->find();
            if ($row) {
                return $this->fail('手机号已被注册', false);
            }
            $parent_id = Db::name('user')->where(['mobile' => $data['parent']])->value('id');
            if (empty($parent_id)) {
                return $this->fail('推荐码错误', false);
            }
            $id = Db::table('tb_user')->insertGetId([
                'mobile' => $data['mobile'],
                'parent_id' => $parent_id,
                'name' => substr_replace($data['mobile'], '****', 3, 4),
                'province' => $data['province'],
                'city' => $data['city'],
                'area' => $data['area'],
                'address' => $data['address'],
                'password' => $this->checkPassword($data['password'], 1),
                // 'trade'=>$this->checkPassword($data['trade'],1),
                'created' => date('Y-m-d H:i:s'),
            ]);

            Db::table('tb_user_address')->insert([
                'user_id' => $id,
                'province' => $this->getusercity($data['province']),
                'city' => $this->getusercity($data['city']),
                'area' => $this->getusercity($data['area']),
                'address' => $data['address'],
                'mobile' => $data['mobile'],
                'name' => substr_replace($data['mobile'], '****', 3, 4),
                'is_default' => 1,
                'add_time' => time()
            ]);

            if ($id) {
                return $this->succ('注册成功,请登录');
            }

        } else {
            return $this->fail('验证码错误', false);
        }
        return $this->fail('服务器繁忙,请稍后再试...', false);
    }

    public function getusercity($id)
    {

        $cityname = Db::table('tb_city')->where(['id' => $id])->value('cityname');
        return $cityname;
    }

    public function getAllCity()
    {
        $online['province'] = Db::name('city')->where(['type' => 1])->field('id,cityname as value,pid,type')->select();
        $online['city'] = Db::name('city')->where(['type' => 2])->field('id,cityname as value,pid,type')->select();
        $online['area'] = Db::name('city')->where(['type' => 3])->field('id,cityname as value,pid,type')->select();
        $arr = [];
        foreach ($online['province'] as $key => $province) {
            $arr[$key] = $province;
            foreach ($online['city'] as $c => $city) {
                if ($province['id'] == $city['pid']) {
                    $arr[$key]['childs'][] = $city;
                    foreach ($arr[$key]['childs'] as $m => $child) {
                        $arr[$key]['childs'][$m]['childs'] = [];
                        foreach ($online['area'] as $a => $area) {
                            if ($child['id'] == $area['pid']) {
                                $arr[$key]['childs'][$m]['childs'][] = $area;
                            }
                        }
                    }
                }
            }
        }
        return $this->succ($arr);
    }

    public function getSon()
    {
        $parent_id = input('id');
        if (empty($parent_id)) {
            return $this->fail('信息错误', false);
        }
        $online = Db::name('city')->where(['pid' => $parent_id])->select();
        if (empty($online)) {
            return $this->fail([], false);
        } else {
            return $this->succ($online);
        }

    }

    /*退出登陆*/
    public function logout($token)
    {
        $b = (new Rds())->redis->del('login_' . $token);
        return json(['status' => $b]);
    }

    public function sendMsg($type = 1)
    {
        if ($type == 1) {
            $phone = input('phone');
        } else {
            $phone = Db::name('user')->where(['id' => $this->request->user_id])->value('mobile');
        }
//        $msg = new Msg();
        $msg = new Mns();
        $res = $msg->send($phone);
//        $msg = new Mns();
//        $res = $msg->send($phone);
        if ($res) {
            return $this->succ('发送成功');
        } else {
            return $this->fail('发送失败，请稍后再试', false);
        }
    }

    /**
     * 团队信息
     * @return mixed
     */
    public function teamInfo()
    {
        $type = input('type');
        $page = input('page');
        if (empty($page)) {
            return $this->fail('信息错误', false);
        }
        if ($type == 1) {
            $type = [];
        } elseif ($type == 2) {
            $type = ['level' => 1];
        } elseif ($type == 3) {
            $type = ['level' => 0];
        } else {
            return $this->fail('信息错误', false);
        }
        $user['parent'] = Db::name('user')->where(['id' => $this->userinfo()['parent_id']])->field('avatar,name,mobile')->find();
        if (empty($user['parent'])) {
            $user['parent']['avatar'] = '';
            $user['parent']['name'] = '';
            $user['parent']['mobile'] = '';
        }
        $zhi = Db::name('user')->where(['parent_id' => $this->request->user_id])->select();
        $user['zhi_count'] = count($zhi);
        $user['jian_count'] = 0;

        foreach ($zhi as $key => $item) {
            $user['jian_count'] += Db::name('user')->where(['parent_id' => $item['id']])->count();
        }
        $user['all_count'] = $user['zhi_count'] + $user['jian_count'] + 1;
        $user['son'] = Db::name('user')
            ->where(['parent_id' => $this->request->user_id])
            ->where($type)
            ->field('id,name,level,created,mobile,avatar')
            ->page($page, Env::get('app.page'))
            ->order('id desc')->paginate(Env::get('app.page'), false, ['query' => request()->param()])
            ->each(function ($item) {
                $item['level'] = $item['level'] == 1 ? '会员' : '普通用户';
                $item['zhi_count'] = Db::name('user')->where(['parent_id' => $item['id']])->count();
                return $item;
            });
        $user['num'] = $user['son']->count();
        return $this->succ($user);
    }

    /**
     * 上传
     */
    public function uploadHeader()
    {
        if ($this->request->isPost()) {
//            $data = $this->request->param();
//            $rule = [
//                'avatar'   => 'require',
//            ];
//            $msg = [
//                'avatar.require'     => '请上传头像',
//            ];
//            $data = [
//                'avatar'      => $data['avatar'],
//            ];
//            $validate = new Validate($rule, $msg);
//            if (!$validate->check($data)) {
//                return $this->fail($validate->getError(), false);
//            }
            //调用阿里云上传
            if ($this->request->file()) {
                $file = $this->request->file('img');
                $info = $file->getInfo();
                $datas = [
                    'name' => $info['name'],
                    'tmp_name' => $info['tmp_name']
                ];
                $oss = new Oss();
                $upload_info = $oss->uploadPostFile($datas);
                $inst['img'] = $upload_info;
            } else {
                if (!empty(input('img'))) {
                    $inst['img'] = input('img');
                } else {
                    return $this->fail('请上传图片', false);
                }
            }

            $online = Db::name('user')->where(['id' => $this->request->user_id])->update(['img_apply' => $inst['img']]);
            if ($online) {
                return $this->succ('上传成功,请等待审核');
            } else {
                return $this->fail('上传失败');
            }

        } else {
            $info = $this->userinfo()['avatar'];
            return $this->succ($info);
        }
    }

    /**
     * 上传
     */
    public function upload()
    {
        if ($this->request->isPost()) {
            //调用阿里云上传
            if ($this->request->file()) {
                $file = $this->request->file('img');
                $info = $file->getInfo();
                $datas = [
                    'name' => $info['name'],
                    'tmp_name' => $info['tmp_name']
                ];
                $oss = new Oss();
                $upload_info = $oss->uploadPostFile($datas);
                $inst['img'] = $upload_info;
            } else {
                if (!empty(input('img'))) {
                    $inst['img'] = input('img');
                } else {
                    return $this->fail('请上传图片', false);
                }
            }
            if ($inst['img']) {
                return $this->succ($inst['img']);
            } else {
                return $this->fail('上传失败');
            }

        }
    }

    public function oss()
    {
        $oss = config('aliyun.oss');
        return $this->succ($oss);
    }

    /**
     * 用户反馈
     */
    public function getHelp()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $imgs = $data['img'];
            $rule = [
                'content' => 'require',
            ];
            $msg = [
                'content.require' => '请输入反馈内容',
            ];
            $data = [
                'content' => isset($data['content']) ? $data['content'] : '',
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $info = [
                'uid' => $this->request->user_id,
                'username' => Db::name('user')->where(['id' => $this->request->user_id])->value('mobile'),
                'content' => $data['content'],
                'add_time' => time(),
                'img' => !empty($imgs) ? $imgs : ''
            ];
            $res = Db::name('message')->insert($info);
            if ($res) {
                return $this->succ('已提交');
            } else {
                return $this->fail('服务器繁忙,请稍后再试', false);
            }
        } else {
            $page = input('page');
            $type = input('type');
            if (!is_numeric($page) || !is_numeric($type)) {
                return $this->fail('错误操作', false);
            }
            if ($type == 0) {
                $where['uid'] = $this->request->user_id;
                $where['type'] = $type;
            } else {
                $where['type'] = $type;
            }
            $res = Db::name('message')
                ->where($where)
                ->order('id desc')
                ->page($page, Env::get('app.page'))
                ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
                ->each(function ($item) {
                    $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                    $item['admin_time'] = $item['admin_time'] > 0 ? date('Y-m-d H:i:s', $item['admin_time']) : '待回复';
                    return $item;
                });
            if ($res) {
                return $this->succ($res);
            } else {
                return $this->fail($res, false);
            }


        }
    }

    public function findhelp($id)
    {
        if (!is_numeric($id)) {
            return $this->fail('错误操作', false);
        }
        $online = Db::name('message')->where(['id' => $id])->find();
        if ($online) {
            return $this->succ($online);
        } else {
            return $this->fail($online, false);
        }
    }


    /**
     * 关于我们
     */
    public function aboutUs()
    {
        $info = Info::where(['type' => 'about'])->order('id desc')->find();
        return $this->succ($info);
    }


    public function moneyApply()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'carid' => 'require|number',
                'money' => 'require',
                'trade' => 'require',
            ];
            $msg = [
                'carid.require' => '请选择提现账户',
                'carid.number' => '错误操作',
                'money.require' => '请填写金额',
                'trade.require' => '请输入交易密码',
            ];
            $data = [
                'carid' => $data['carid'],
                'money' => $data['money'],
                'trade' => $data['trade'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $trade = Db::table('tb_user')->where('id', $this->request->user_id)->value('trade');
            if (empty($trade)) {
                return $this->fail('请先设置交易密码', false);
            }
            $checkPassword = $this->checkPassword($data['trade'], 2, $trade);
            if (!$checkPassword) {
                return $this->fail('交易密码错误', false);
            }
            //查询用户卡号
            $car = Db::name('user_card')->where(['card_type' => $data['carid'], 'user_id' => $this->request->user_id])->find();
            if (!$car) {
                return $this->fail('请先设置该支付方式', false);
            }

            // 启动事务
            Db::startTrans();
            try {
                //手续费
                $per = get_config('base', 'usdt');
                $multiple = get_config('user', 'multiple');
                if (floatval($per) > 0) {
                    $sxf = $data['money'] * $per / 100;
                } else {
                    $sxf = $data['money'];
                }
                $user = $this->userinfo();
                if ($user['level'] == 0) {
                    return $this->fail('非会员不能够提现,请先充值购买会员', false);
                }
                $ident = Db::name('user_auth')->where(['user_id' => $this->request->user_id, 'status' => 1])->find();
                if (empty($ident)) {
                    return $this->fail('请先进行身份认证!', false);
                }
                if (intval($multiple) > 0) {
                    if (intval($data['money']) % intval($multiple) != 0) {
                        return $this->fail('提现金额必须是' . $multiple . '的倍数', false);
                    }
                }
//                if($user['money'] < floatval($data['money']) + $sxf){
//                    return $this->fail('余额不足',false);
//                }
                if ($user['money'] < floatval($data['money'])) {
                    return $this->fail('余额不足', false);
                }

                $time = Db::name('money_apply')->where(['userid' => $this->request->user_id])->order('id desc')->value('add_time');
                if (time() - $time <= 60) {
                    return $this->fail('点击过快,请一分钟后再申请', false);
                }
                $info['userid'] = $this->request->user_id;
                $info['card_type'] = $car['card_type'];
                $info['add_time'] = time();
                $info['money'] = floatval($data['money']);
                $info['name'] = $car['name'];
                $info['card'] = $car['card'];
                $info['khh'] = $car['khh'];
                $info['khzh'] = $car['khzh'];
                $info['img'] = $car['img'];
                $res = Db::name('money_apply')->insert($info);
                if ($res) {
                    //扣除用户申请金额
                    Db::name('user')->where(['id' => $this->request->user_id])->setDec('money', floatval($data['money']));
//                    Db::name('user')->where(['id'=>$this->request->user_id])->setDec('money',floatval($data['money']) + $sxf);
                    $this->add_log($this->request->user_id, 1, 9, floatval($data['money']), 0);
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return json(['status' => 'n', 'info' => '付款失败']);
            }
            if ($res) {
                return $this->succ('已提交');
            } else {
                return $this->fail('请稍后再试', false);
            }
        }
    }

    public function bank()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'name' => 'require',
                'card' => 'require',
                'khh' => 'require',
                'khzh' => 'require',
            ];
            $msg = [
                'name.require' => '请填写持卡人姓名',
                'card.require' => '请填写卡号',
                'khh.require' => '请填写开户行',
                'khzh.require' => '请填写开户支行',
            ];
            $data = [
                'name' => $data['name'],
                'card' => $data['card'],
                'khh' => $data['khh'],
                'khzh' => $data['khzh'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $info = 0;
            // 启动事务
            Db::startTrans();
            try {
                $fun = Db::name('user_card')->where(['card_type' => 3, 'user_id' => $this->request->user_id])->find();
                if ($fun) {
                    $info = Db::name('user_card')->where(['id' => $fun['id']])->update($data);
                } else {
                    $data['created'] = time();
                    $data['card_type'] = 3;
                    $data['user_id'] = $this->request->user_id;
                    $info = Db::name('user_card')->insert($data);
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($info) {
                return $this->succ('已提交');
            } else {
                return $this->succ('操作失败，请稍后再试');
            }
        } else {
            $fun = Db::name('user_card')->where(['card_type' => 3, 'user_id' => $this->request->user_id])->find();
            if ($fun) {
                return $this->succ($fun);
            } else {
                return $this->fail([], false);
            }

        }

    }


    public function card()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            $rule = [
                'card' => 'require',
                'card_type' => 'require',
            ];
            $msg = [
                'card.require' => '请填写卡号',
                'card_type.require' => '错误操作',
            ];
            $data = [
                'card' => $data['card'],
                'card_type' => $data['card_type'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            //调用阿里云上传
            if ($this->request->file()) {
                $file = $this->request->file('img');
                $info = $file->getInfo();
                $datas = [
                    'name' => $info['name'],
                    'tmp_name' => $info['tmp_name']
                ];
                $oss = new Oss();
                $upload_info = $oss->uploadPostFile($datas);
                $inst['img'] = $upload_info;
            } else {
                if (!empty(input('img'))) {
                    $inst['img'] = input('img');
                } else {
                    return $this->fail('请上传收款图片', false);
                }
            }

            $inst['card'] = $data['card'];
            $res = 0;
            Db::startTrans();
            try {
                $fun = Db::name('user_card')->where(['card_type' => $data['card_type'], 'user_id' => $this->request->user_id])->find();

                if ($fun) {
                    $res = Db::name('user_card')->where(['id' => $fun['id']])->update($inst);
                } else {
//                    $inst['img'] = $data['img'];
                    $inst['created'] = time();
                    $inst['user_id'] = $this->request->user_id;
                    $inst['card_type'] = $data['card_type'];
                    $inst['name'] = \app\models\User::where(['id' => $this->request->user_id])->value('name');
                    $res = Db::name('user_card')->insert($inst);
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($res) {
                $this->succ('已提交');
            }
        } else {
            $type = input('type');
            $fun = Db::name('user_card')->where(['card_type' => $type, 'user_id' => $this->request->user_id])->find();
            if (empty($fun)) {
                $this->fail([], false);
            } else {
                $this->succ($fun);
            }


        }
    }

    public function ossInfo()
    {
        $oss = [
            'regionId' => config('aliyun.oss.regionId'),
            'accessKeyId' => config('aliyun.oss.accessKeyId'),
            'accessSecret' => config('aliyun.oss.accessSecret'),
            'roleArn' => config('aliyun.oss.roleArn'),
            'bucket' => config('aliyun.oss.bucket'),
            'endpoint' => config('aliyun.oss.endpoint')
        ];
        return $this->succ($oss);
    }

    public function cardid()
    {
        $where['user_id'] = $this->request->user_id;
        $fun = Db::name('user_card')->where($where)->select();
        if ($fun) {
            $this->succ($fun);
        } else {
            $this->fail($fun, false);
        }

    }

    public function upphone()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            $rule = [
                'mobile' => 'require',
                'code' => 'require',
            ];
            $msg = [
                'mobile.require' => '请填写手机号',
                'code.require' => '请填写验证码',
            ];
            $data = [
                'mobile' => $data['mobile'],
                'code' => $data['code'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $res = 0;
            Db::startTrans();
            try {
                if ((new Mns())->checkCode($data['code'], $data['mobile'])) {
                    $res = Db::name('user')->where(['id' => $this->request->user_id])->update(['mobile' => $data['mobile']]);
                } else {
                    return $this->fail('验证码错误', false);
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($res) {
                return $this->succ('修改成功');
            } else {
                return $this->fail('服务器繁忙,请稍后再试', false);
            }
        }
    }

    public function uppassword()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            $rule = [
                'type' => 'require',
                'password' => 'require',
                'cfpassword' => 'require',
                'code' => 'require',
            ];
            $msg = [
                'type.require' => '错误操作',
                'password.require' => '请填写新密码',
                'cfpassword.require' => '请填写确认密码',
                'code.require' => '请填写验证码',
            ];
            $data = [
                'type' => $data['type'],
                'password' => $data['password'],
                'cfpassword' => $data['cfpassword'],
                'code' => $data['code'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $res = 0;
            $type = intval($data['type']);
            Db::startTrans();
            try {
                if ($data['password'] != $data['cfpassword']) {
                    return $this->fail('两次密码不相同', false);
                }
                if ((new Mns())->checkCode($data['code'], Db::name('user')->where(['id' => $this->request->user_id])->value('mobile'))) {

                    if ($type == 1) {
                        $pwd = 'password';
                    } else {
                        $pwd = 'trade';
                    }
                    $newpassword = $this->checkPassword($data['password'], 1);
                    $res = Db::name('user')->where(['id' => $this->request->user_id])->update([$pwd => $newpassword]);

                } else {
                    return $this->fail('验证码错误', false);
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($res) {
                return $this->succ('修改成功');
            } else {
                return $this->fail('服务器繁忙,请稍后再试', false);
            }
        }
    }

    public function forgetPwd()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            $rule = [
                'mobile' => 'require',
                'password' => 'require',
                'cfpassword' => 'require',
                'code' => 'require',
            ];
            $msg = [
                'mobile.require' => '请输入手机号',
                'password.require' => '请填写新密码',
                'cfpassword.require' => '请填写确认密码',
                'code.require' => '请填写验证码',
            ];
            $data = [
                'mobile' => $data['mobile'],
                'password' => $data['password'],
                'cfpassword' => $data['cfpassword'],
                'code' => $data['code'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $res = 0;
            Db::startTrans();
            try {
                if ($data['password'] != $data['cfpassword']) {
                    return $this->fail('两次密码不相同', false);
                }
                $info = Db::name('user')->where(['mobile' => $data['mobile']])->find();
                if (!$info) {
                    return $this->fail('手机号不存在', false);
                }
                if ((new Mns())->checkCode($data['code'], $data['mobile'])) {
                    $pwd = 'password';
                    $newpassword = $this->checkPassword($data['password'], 1);
                    $res = Db::name('user')->where(['id' => $info['id']])->update([$pwd => $newpassword]);
                } else {
                    return $this->fail('验证码错误', false);
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($res) {
                return $this->succ('修改成功');
            } else {
                return $this->fail('服务器繁忙,请稍后再试', false);
            }
        }
    }


    /*邀请*/
    public function invitation()
    {

        $tgzd = Db::name('info')->where(['type' => 'extens'])->order('id desc')->find();
        $res = [
            'tgzd' => $tgzd,
//            'link'=>'http://'.Env::get('app.api_url').'/register/index?invite='.$this->userinfo()['mobile'],
            'link' => 'http://' . Env::get('app.api_url') . '/register/index/#/Register?invite=' . $this->userinfo()['mobile'],
            'tgm' => $this->userinfo()['mobile'],
            'name' => $this->userinfo()['name'],
            'avatar' => $this->userinfo()['avatar'],
        ];
        return $this->succ($res);
    }

    public function getJin()
    {
        $online = Db::name('info')->where(['type' => 'jin'])->order('id desc')->find();
        if ($online) {
            return $this->succ($online);
        } else {
            return $this->fail($online, false);
        }

    }

    public function pdtrade()
    {
        $trade = Db::name('user')->where(['id' => $this->request->user_id])->value('trade');
        if (empty($trade)) {
            $online = 0;
        } else {
            $online = 1;
        }
        return $this->succ($online);
    }

    function getClientIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
                if (!isset($realip)) {
                    $realip = "0.0.0.0";
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        return $realip;
    }

    //获取所在城市
    public function getCity()
    {
        // 获取当前位置所在城市
        $getIp = $this->getClientIP();
        $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=2TGbi6zzFm5rjYKqPPomh9GBwcgLW5sS&ip={$getIp}&coor=bd09ll");

        $json = json_decode($content);
        $address = $json->{'content'}->{'address'};//按层级关系提取address数据
        $data['address'] = $address;
        $data['province'] = mb_substr($data['address'], 0, 3, 'utf-8');
        $data['city'] = mb_substr($data['address'], 3, 3, 'utf-8');
        return $data;
    }

    public function updateUsername()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            $rule = [
                'name' => 'require',
                'type' => 'require',
            ];
            $msg = [
                'name.require' => '内容不能为空',
                'type.require' => '类型不能为空',
            ];
            $data = [
                'name' => $data['name'],
                'type' => $data['type'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $type[1] = 'name';
            $type[2] = 'company';
            $type[3] = 'introduce';
            $type[4] = 'age';
            if ($data['type'] == 4) {
                $data['name'] = strtotime($data['name']);
            }
            Db::name('user')->where(['id' => $this->request->user_id])->update([$type[$data['type']] => $data['name']]);
            return $this->succ('修改成功', false);
        } else {
            $type = input('name');
            $name = Db::name('user')->where(['id' => $this->request->user_id])->value($type);
            return $this->succ($name);
        }

    }

    /**
     * 明细
     * @throws \think\exception\DbException
     */
    public function integral()
    {
        $type = input('type');
        if (!is_numeric($type)) {
            return $this->fail('错误操作', false);
        }
        $userid = $this->request->user_id;
        $where['currency'] = $type;
        $where['uid'] = $userid;
        $page = input('page');
        if (!is_numeric($page)) {
            return $this->fail('错误操作', false);
        }
        $profit_in = Db::name('money_profit')->where($where)->where(['type' => 1])->sum('action');
        $profit_out = Db::name('money_profit')->where($where)->where(['type' => 0])->sum('action');
        $online = Db::name('money_profit')
            ->where($where)
            ->order('id desc')
            ->page($page, Env::get('app.page'))
            ->field('id,action_type,action,type,add_time,currency')
            ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
            ->each(function ($item) {
                $item['action_type'] = actiontype($item['action_type']);
                $item['currency'] = currencytype($item['currency']);
                $item['action'] = floatval($item['action']);
                $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                $item['other'] = 'type= 1转入 +,  type= 0 转出 - ';
                return $item;
            });
        switch ($type) {
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
        $arr = [
            'list' => $online,
            'profit_in' => $profit_in,
            'profit_out' => $profit_out,
            'money' => $this->userinfo()[$currency_type]
        ];
        return $this->succ($arr);
    }

    public function userMoney()
    {
        $type = 1;  //余额

        $all_money = Db::name('money_profit')->where(['uid' => $this->request->user_id, 'currency' => $type])->sum('action');
        switch ($type) {
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
        return $this->succ(['usermoney' => $this->userinfo()[$currency_type], 'userprofit' => $all_money]);

    }


    public function upInfo()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            $rule = [
                'type' => 'require',
                'content' => 'require',
            ];
            $msg = [
                'type.require' => '类型错误',
                'content.require' => '输入不正确',
            ];
            $data = [
                'type' => $data['type'],
                'content' => $data['content'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $res = 0;
            Db::startTrans();
            try {
                if ($data['type'] == 1) {
                    $type = 'wx';
                }
                if ($data['type'] == 2) {
                    $type = 'age';
                }  #待定
                $res = Db::name('user')->where(['id' => $this->request->user_id])->update([
                    $type => $data['content']
                ]);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($res) {
                return $this->succ('修改成功');
            } else {
                return $this->fail('服务器繁忙,请稍后再试', false);
            }
        }
    }

    /**
     * 发布个人动态
     */
    public function selfDynamic()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $img = $data['img'];
            $rule = [
                'content' => 'require',
            ];
            $msg = [
                'content.require' => '请输入发布内容',
            ];
            $data = [
                'content' => $data['content'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $res = 0;
            Db::startTrans();
            try {
                $insert = [
                    'user_id' => $this->request->user_id,
                    'content' => $data['content'],
                    'img' => $img,
                ];
                $res = Db::name('user_dynamic')->insert($insert);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($res) {
                return $this->succ('发布成功');
            } else {
                return $this->fail('服务器繁忙,请稍后再试', false);
            }
        } else {
            $page = input('page');
            $join = [['tb_user a', 'a.id=b.user_id']];
            $res = Db::name('user_dynamic')
                ->order('id desc')
                ->alias('b')
                ->join($join)
                ->field('b.*,a.name')
                ->page($page, Env::get('app.page'))
                ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
                ->each(function ($item) {
                    $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                    return $item;
                });
            if (empty($res->toArray()['data'])) {
                return $this->fail([], false);
            } else {
                return $this->succ($res);
            }

        }
    }

    public function userAuth()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'name' => 'require',
                'card_no' => 'require',
//                'obverse'   => 'require',
                'reverse' => 'require',
                'userface' => 'require',
            ];
            $msg = [
                'name.require' => '请输入真实姓名',
                'card_no.require' => '请输入身份证号',
//                'obverse.require'    => '请上传身份证正面',
                'reverse.require' => '请上传身份证反面',
                'userface.require' => '请进行人脸识别',
            ];
            $data = [
                'name' => $data['name'],
                'card_no' => $data['card_no'],
//                'obverse'   => $data['obverse'],
                'reverse' => $data['reverse'],
                'userface' => $data['userface'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $res = 0;
            Db::startTrans();
            try {
                $where['card_no'] = $data['card_no'];
                $info = Db::name('user_auth')->where($where)->find();
                if ($info) {
                    if ($info['status'] == 1) {
                        return $this->fail('身份证已存在 ', false);
                    }
                    if ($info['status'] == 0) {
                        return $this->fail('身份证已存在 ', false);
                    }
                }
                $userrz['user_id'] = $this->request->user_id;
                $info = Db::name('user_auth')->where($userrz)->find();
                if ($info) {
                    if ($info['status'] == 1) {
                        return $this->fail('不能重复认证 ', false);
                    }
                    if ($info['status'] == 0) {
                        return $this->fail('申请已存在，请等待审核 ', false);
                    }
                }
                $data['user_id'] = $this->request->user_id;
                $data['created'] = date('Y-m-d H:i:s', time());

                //扣除认证费用
                $usermoney = $this->userinfo()['money'];
                $ident_money = get_config('user', 'identmoney');
                if (floatval($usermoney) >= floatval($ident_money)) {
                    Db::name('user')->where(['id' => $this->request->user_id])->setDec('money', $ident_money);
                    $this->add_log($this->request->user_id, 1, 23, $ident_money, 0);
                } else {
                    return $this->fail('余额不足，请先充值', false, 2);
                }
                $res = Db::name('user_auth')->insert($data);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($res) {
                return $this->succ('请等待审核');
            } else {
                return $this->fail('服务器繁忙,请稍后再试', false);
            }
        } else {
            $info = Db::name('user_auth')->where(['user_id' => $this->request->user_id])->find();
            if ($info) {
                return $this->succ($info);
            } else {
                return $this->fail([], false);
            }

        }
    }


    public function enterpriseAuthentication()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'license' => 'require',
                'legalperson' => 'require',
                'company' => 'require',
                'name' => 'require',
            ];
            $msg = [
                'license.require' => '请上传营业执照',
                'legalperson.require' => '请上传法人身份证',
                'company.require' => '请上传公司照片',
                'name.require' => '请输入企业名称',
            ];
            $data = [
                'license' => $data['license'],
                'legalperson' => $data['legalperson'],
                'company' => $data['company'],
                'name' => $data['name'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $res = 0;
            Db::startTrans();
            try {
                $info = Db::name('user_company')->where(['user_id' => $this->request->user_id])->find();
                if ($info) {
                    if ($info['status'] == 1) {
                        return $this->fail('认证已存在,不需要重复提交 ', false);
                    }
                    if ($info['status'] == 0) {
                        return $this->fail('请等待审核 ', false);
                    }
                }
                $data['user_id'] = $this->request->user_id;
                $data['created'] = date('Y-m-d H:i:s', time());
                $res = Db::name('user_company')->insert($data);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if ($res) {
                return $this->succ('请等待审核');
            } else {
                return $this->fail('服务器繁忙,请稍后再试', false);
            }
        } else {
            $info = Db::name('user_company')->where('status', 'neq', 2)->where(['user_id' => $this->request->user_id])->find();
            if ($info) {
                return $this->succ($info);
            } else {
                return $this->fail([], false);
            }

        }
    }

    /**
     * 所有认证信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userAuthStatus()
    {
        $where['user_id'] = $this->request->user_id;
        $online = [
            'sfz' => Db::name('user_auth')->where($where)->where(['status' => 1])->find() ? 1 : 0,
            'qiye' => Db::name('user_company')->where($where)->where(['status' => 1])->find() ? 1 : 0,
            'zfb' => Db::name('user_card')->where($where)->where(['card_type' => 1])->find() ? 1 : 0,
            'wx' => Db::name('user_card')->where($where)->where(['card_type' => 2])->find() ? 1 : 0,
            'yhk' => Db::name('user_card')->where($where)->where(['card_type' => 3])->find() ? 1 : 0,
        ];
        return $this->succ($online);
    }

    /**
     * 豆转余额
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function beanTransMoney()
    {
        $bean = $this->userinfo()['cz_money'];
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'bean' => 'require',
                'trade' => 'require',
            ];
            $msg = [
                'bean.require' => '请输入豆豆数量',
                'trade.require' => '请输入支付密码',
            ];
            $data = [
                'bean' => $data['bean'],
                'trade' => $data['trade'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            if (floatval($data['bean']) <= 0) {
                return $this->fail('请输入正确的数量', false);
            }
            $trade = Db::name('user')->where(['id' => $this->request->user_id])->value('trade');
            if (empty($trade)) {
                return $this->fail('请设置交易密码', false);
            }
            $checkPassword = $this->checkPassword($data['trade'], 2, $trade);
            if (!$checkPassword) {
                return $this->fail('密码错误', false);
            }
            //用户最少持有豆
            $minbean = get_config('user', 'minbean');
            $multiple = get_config('user', 'multiple');
            if (floatval($bean) < floatval($minbean)) {
                return $this->fail('最少拥有' . $minbean . '豆才可兑换余额', false);
            }
            //倍数
            if (intval($data['bean']) > 0) {
                if (intval($data['bean']) % intval($multiple) != 0) {
                    return $this->fail('请输入' . $multiple . '的倍数', false);
                }
            }

            try {
                Db::startTrans();
                $last_money = $this->userinfo()['money'] + floatval($data['bean']);
                $last_bean = $this->userinfo()['cz_money'] - floatval($data['bean']);
                #豆转余额  比例 1:1
                Db::name('user')
                    ->where(['id' => $this->request->user_id])
                    ->update([
                        'money' => $last_money,
                        'cz_money' => $last_bean,
                    ]);
                $this->add_log($this->request->user_id, 1, 6, floatval($data['bean']));  //余额增加
                $this->add_log($this->request->user_id, 3, 6, floatval($data['bean']), 0);  //豆豆扣除

                // 提交事务
                Db::commit();
                return $this->succ('兑换成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        } else {
            $list = [
                'all_bean' => $bean,   #总豆豆
                'bean_profit' => Db::name('money_profit')#总豆豆收益
                ->where([
                    'uid' => $this->request->user_id,
                    'currency' => 3,
                    'type' => 1,
                ])->sum('action')
            ];
            return $this->succ($list);
        }
    }

    /********************************************************************************技能认证******************/

    public function applySkill()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $voice = $data['voice'];
            $img = json_encode($data['img']);
            $unline_service = is_numeric($data['unline_service']) ? $data['unline_service'] : 0;
            $unline_unit = is_numeric($data['unline_unit']) ? $data['unline_unit'] : 0;

            $phone_service = is_numeric($data['phone_service']) ? $data['phone_service'] : 0;
            $phone_unit = is_numeric($data['phone_unit']) ? $data['phone_unit'] : 0;

            $longrange_service = is_numeric($data['longrange_service']) ? $data['longrange_service'] : 0;
            $longrange_unit = is_numeric($data['longrange_unit']) ? $data['longrange_unit'] : 0;

            $rule = [
                'type' => 'require',
                'introduce' => 'require',
                'start_time' => 'require',
                'end_time' => 'require',
            ];
            $msg = [
                'type.require' => '请选择技能类型',
                'introduce.require' => '请输入技能介绍',
                'start_time.require' => '请选择开始时间',
                'end_time.require' => '请选择结束时间',
            ];
            $data = [
                'type' => $data['type'],
                'introduce' => $data['introduce'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            if (!empty($unline_service) || !empty($unline_unit)) {
                if (empty($unline_service)) {
                    return $this->fail('请输入线下服务收费标准', false);
                }
                if (empty($unline_unit)) {
                    return $this->fail('请选择线下服务计价单位', false);
                }
                $info['unline_service'] = $unline_service;
                $info['unline_unit'] = $unline_unit;
            }
            if (!empty($phone_service) || !empty($phone_unit)) {
                if (empty($phone_service)) {
                    return $this->fail('请输入手机服务收费标准', false);
                }
                if (empty($phone_unit)) {
                    return $this->fail('请选择手机服务计价单位', false);
                }
                $info['phone_service'] = $phone_service;
                $info['phone_unit'] = $phone_unit;
            }
            if (!empty($longrange_service) || !empty($longrange_unit)) {
                if (empty($longrange_service)) {
                    return $this->fail('请输入线上服务收费标准', false);
                }
                if (empty($longrange_unit)) {
                    return $this->fail('请选择线上服务计价单位', false);
                }
                $info['longrange_service'] = $longrange_service;
                $info['longrange_unit'] = $longrange_unit;
            }
            if (empty($info['longrange_service']) && empty($info['unline_service']) && empty($info['phone_service'])) {
                return $this->fail('至少选择一项收费标准', false);
            }
            if (empty($img)) {
                return $this->fail('请上传至少一张图片介绍', false);
            }
            $info['img'] = $img;
            $info['user_id'] = $this->request->user_id;
            $info['voice'] = $voice;
            $info['type'] = $data['type'];
            $info['introduce'] = $data['introduce'];
            $info['add_time'] = time();
            $info['start_time'] = $data['start_time'];
            $info['end_time'] = $data['end_time'];
            try {
                Db::startTrans();
                $fin = Db::name('user_skill')->where(['user_id' => $this->request->user_id, 'type' => $info['type']])->where('status', 'between', '0,1')->find();
                if ($fin) {
                    return $this->fail('同技能只能申请一次', false);
                }
                $getid = Db::name('user_skill')->insertGetId($info);
                if (!$getid) {
                    return $this->fail('网络错误.请稍后再试', false);
                }
                // 提交事务
                Db::commit();
                return $this->succ('请等待审核');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        } else {
            $type = Db::name('product_category')->field('cat_id,cat_name')->select();
            return $this->succ($type);
        }
    }

    public function skillList()
    {
        $page = input('page');
        if (!is_numeric($page)) {
            return $this->fail('错误操作', false);
        }
        $where['a.user_id'] = $this->request->user_id;
//        $where['a.status'] = 1;
        $join = [['tb_product_category b', 'b.cat_id = a.type']];
        $online = Db::name('user_skill')
            ->where($where)
            ->alias('a')
            ->join($join)
            ->field('a.*,b.cat_id,b.cat_name,b.thumb,b.describe')
            ->order('a.id desc')
            ->page($page, Env::get('app.page'))
            ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
            ->each(function ($item) {
                $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                $item['img'] = json_decode($item['img']);
                return $item;
            });
        if (empty($online->toArray()['data'])) {
            return $this->fail('没有更多数据', false);
        } else {
            return $this->succ($online);
        }
    }

    /**
     * 个人主页
     */
    public function homePage()
    {
        $user_id = input('userid');
        $longitude = input('lng');
        $latitude = input('lat');

        if (empty(intval($user_id))) {
            return $this->fail('没有更多数据', false);
        }
        $info = $this->userinfo($user_id);
        $online['avatar'] = $info['avatar'];
        $online['name'] = $info['name'];
        $online['lng'] = $info['lng'];
        $online['lat'] = $info['lat'];
        $online['address'] = $info['address'];
        $skill = Db::name('user_skill')->where(['user_id' => $user_id, 'status' => 1])->field('type')->select();
        $online['skill'] = [];
        foreach ($skill as $k => $item) {
            $online['skill'][] = Db::name('product_category')->where(['cat_id' => $item['type']])->field('cat_id,cat_name,thumb')->find();
        }
        $online['distance'] = 0;
        //计算当前离自己的距离
        if (!empty($longitude) && !empty($latitude) &&!empty($info['lng']) && !empty($info['lat'])) {
            if (!is_numeric($longitude) || !is_numeric($latitude)) {
                return $this->fail('经纬度错误', false);
            }
            $Distance = new Distance();
            $rice = $Distance->getdistance($info['lng'], $info['lat'], $longitude, $latitude);
            $distance = intval($rice);
            if (floatval($distance) >= 1000) {
                // $arr['data'][$key]['distance'] =  floor($item['distance']);
                $online['distance'] = floor(($distance / 1000) * 100) / 100 . '公里';
            } else {
                //$arr['data'][$key]['distance'] = intval($item['distance']);
                $online['distance'] = intval($distance) . '米';
            }

            $client = new Client();
//            $url = 'http://127.0.0.1:3344';
//            $res = $client->request('GET', $url, [
//                'query' => [
//                    'tx' => $trade->hash,
//                ]
//            ]);

            if($info['id'] == $this->request->user_id){
                $info['lng']=$longitude;
                $info['lat']=$latitude;
            }

            $result = $client->get("https://restapi.amap.com/v3/geocode/regeo?key=94e1015603870b7634fbe6156d092834&location=" . $info['lng'] . "," . $info['lat'] . "&poitype=&radius=0&extensions=base&batch=false&roadlevel=0");
            $rescity = json_decode($result->getBody(), true);

            if ($rescity["status"] == 1) {
                $online['address'] = $rescity['regeocode']['addressComponent']['province'] . "." . $rescity['regeocode']['addressComponent']['city'] . "." . $rescity['regeocode']['addressComponent']['district'];
            }
        }
        $res = Db::name('user_follow')->where(['user_id' => $this->request->user_id])->find();
        $seach = strstr($res['str'], $user_id);
        $online['follow'] = $seach == false ? 0 : 1;
        return $this->succ($online);
    }

    public function getSkillInfo()
    {
        $type = input('cat_id');
        $user_id = input('userid');
        if (empty(intval($user_id)) || empty(intval($type))) {
            return $this->fail('没有更多数据', false);
        }
        $online = Db::name('user_skill')->where(['user_id' => $user_id, 'type' => $type, 'status' => 1])->find();
        $online['img'] = json_decode($online['img']);
        return $this->succ($online);
    }

    public function getEvaluate()
    {
        $user_id = input('userid');
        if (empty(intval($user_id))) {
            return $this->fail('没有更多数据', false);
        }
        $online['attitude'] = floor(Db::name('evaluate')->where(['uid' => $user_id])->avg('attitude') * 100) / 100;
        $online['skill'] = floor(Db::name('evaluate')->where(['uid' => $user_id])->avg('skill') * 100) / 100;
        $online['work'] = floor(Db::name('evaluate')->where(['uid' => $user_id])->avg('work') * 100) / 100;
        $online['avg'] = floor((($online['attitude'] + $online['skill'] + $online['work']) / 3) * 100) / 100;
        return $this->succ($online);
    }


    public function userFollow()
    {
        $follow_id = input('follow_id');
        if (empty(intval($follow_id))) {
            return $this->fail('没有更多数据', false);
        }
        Db::startTrans();
        try {
            if ($this->request->user_id == $follow_id) {
                return $this->fail('不能关注自己', false);
            }
            $res = Db::name('user_follow')->where(['user_id' => $this->request->user_id])->find();
            if (empty($res)) {
                $r = Db::name('user_follow')->insert(['user_id' => $this->request->user_id, 'str' => $follow_id]);
                if ($r) {
                    return $this->succ('关注成功');
                } else {
                    return $this->fail('服务器繁忙', false);
                }
            } else {
                $seach = strstr($res['str'], $follow_id);
                if ($seach !== false) {   //存在   取消关注
                    $t1 = ',' . $follow_id;
                    $t2 = $follow_id . ',';
                    $seach1 = strstr($res['str'], $t1);
                    $seach2 = strstr($res['str'], $t2);
                    if ($seach1 !== false) {
                        $str = str_replace($t1, '', $res['str']);
                    } else {
                        if ($seach2 !== false) {
                            $str = str_replace($t2, '', $res['str']);
                        }
                    }
                    $r = Db::name('user_follow')->where(['user_id' => $this->request->user_id])->update(['str' => $str]);
                    if ($r) {
                        return $this->succ('取消成功');
                    } else {
                        return $this->fail('服务器繁忙', false);
                    }
                } else {   //不存在   加入关注
                    $str = $res['str'] . ',' . $follow_id;
                    $r = Db::name('user_follow')->where(['user_id' => $this->request->user_id])->update(['str' => $str]);
                    if ($r) {
                        return $this->succ('关注成功');
                    } else {
                        return $this->fail('服务器繁忙', false);
                    }
                }
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }


    }


    public function serverImg()
    {
        $skill_id = input('skillid');
        if (empty($skill_id)) {
            return $this->fail('错误信息');
        }
        $online = Db::name('user_skill')->where(['id' => $skill_id])->value('img');
        if (empty($online)) {
            $this->fail('未上传服务图片', false);
        } else {
            return $this->succ($online);
        }

    }

    public function getCatInfo()
    {
        $cat_id = input('cat_id');
        if (empty($cat_id)) {
            return $this->fail('错误信息');
        }
        $online = Db::name('product_category')->where(['cat_id' => $cat_id])->find();
        if (empty($online)) {
            $this->fail('没有数据', false);
        } else {
            return $this->succ($online);
        }

    }

    public function getuserevaluate()
    {
        $skill_id = input('skill_id');
        $user_id = $this->request->post('user_id');
        if (empty($skill_id) || empty($user_id)) {
            return $this->fail('错误信息');
        }
        $join = [['tb_receipt b', 'b.id = a.receipt_id']];
        $online = Db::name('evaluate')
            ->alias('a')
            ->join($join)
            ->where('b.service_type', 'eq', $skill_id)
            ->where(['uid' => $user_id])
            ->field('a.*,b.service_type')
            ->order('a.id desc')
            ->paginate(5, false, ['query' => request()->param()])
            ->each(function ($item) {
                $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                $item['avatar'] = $this->getUserInfo($item['user_id'], 'avatar');
                $item['name'] = $this->getUserInfo($item['user_id'], 'name');
                return $item;
            });

        return $this->succ($online);
    }

    public function getCid()
    {
        $cid = input('cid');
        if (empty($cid)) {
            return $this->fail('错误信息', false);
        }
        $user_cid = Db::name('user')->where(['id' => $this->request->user_id])->value('cid');
        if (empty($user_cid)) {
            $res = Db::name('user')->where(['id' => $this->request->user_id])->update(['cid' => $cid]);
            if ($res) {
                return $this->succ(true);
            } else {
                return $this->fail('消息提醒功能未能开启', false);
            }
        } else {
            return $this->succ(true);
        }

    }
    public function about(){
        $row = Db::name('about')->find(1);
        return $this->succ($row);
    }


}