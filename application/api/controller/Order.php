<?php

namespace app\api\controller;

use app\helps\Msg;
use phpass;
use think\Db;
use think\facade\Env;
use think\Validate;

/**
 * Class Order
 * 接单发单   -----主要功能
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
class Order extends Base
{
    /**
     * 发单栏目
     */
//    public function orderType(){
//        $type = input('type');
//        $online = Db::name('product_category')->field('cat_id,parent_id,cat_name,describe,thumb')->select();
//        return $this->succ(['list'=>category_merge($online),'type'=>$type]);
//    }

    public function orderType(){
        $hot = Db::name('product_category')->where('parent_id','neq',0)->where(['is_show'=>1])->field('cat_id,parent_id,cat_name,describe,thumb')->select();
        $online['parent'] = Db::name('product_category')->where(['parent_id'=>0])->field('cat_id,parent_id,cat_name,describe,thumb')->select();
        $online['son'] = Db::name('product_category')->where('parent_id','neq',0)->field('cat_id,parent_id,cat_name,describe,thumb')->select();
        $arr = [];
        foreach ($online['parent'] as $key =>$parent){
            $arr[$key] = $parent;
            foreach ($online['son'] as $c => $son){
                if($parent['cat_id'] == $son['parent_id']){
                    $arr[$key]['childs'][] =  $son;
                }
            }
        }
//        $unshift = [
//            "cat_id"=>0,
//            "parent_id"=> 0,
//            "cat_name"=> "热门推荐",
//            "describe"=> "我是描述我是描述我是描述",
//            "thumb"=> "http://minyi-001.oss-cn-shenzhen.aliyuncs.com/09146456af924aedfd5560977f930b57.png",
//            'childs'=>$hot
//        ];
        //array_unshift($arr,$unshift);
        return $this->succ($arr);
    }

    #----------------------------------------------------------------------发单

    /**
     * 发单
     */
    public function invoice()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $voice = $data['voice'];
            $img = $data['img'];
            $data['address'] = !empty($data['address'])?$data['address']:'';
            $data['city'] = !empty($data['city'])?$data['city']:'';
            $data['area'] = !empty($data['area'])?$data['area']:'';
            $unline_service = isset($data['unline_service'])&&is_numeric($data['unline_service'])?$data['unline_service']:0;
            $unline_unit = isset($data['unline_unit'])&&is_numeric($data['unline_unit'])?$data['unline_unit']:0;

            $phone_service = isset($data['phone_service'])&&is_numeric($data['phone_service'])?$data['phone_service']:0;
            $phone_unit = isset($data['phone_unit'])&&is_numeric($data['phone_unit'])?$data['phone_unit']:0;

            $longrange_service = isset($data['longrange_service'])&&is_numeric($data['longrange_service'])?$data['longrange_service']:0;
            $longrange_unit = isset($data['longrange_unit'])&&is_numeric($data['longrange_unit'])?$data['longrange_unit']:0;
            $rule = [
                'receipt_type' => 'require|number',  #接单发单类型
                'service_type' => 'require|number',
//                'address' => 'require|number',-
                'introduce' => 'require',
                'start_time' => 'require',
                'end_time' => 'require',
//                'city' => 'require',
//                'area' => 'require',
            ];
            $msg = [
                'receipt_type.require' => '错误操作',
                'receipt_type.number' => '错误操作',
//                'address.require' => '请选择地址',
//                'address.number' => '错误操作',
                'service_type.require' => '请选择服务品类',
                'service_type.number' => '错误操作',
                'introduce.require' => '请填写服务介绍',
                'start_time.require' => '请选择开始时间',
                'end_time.require' => '请选择结束时间',
//                'city.require' => '缺少参数',
//                'area.require' => '缺少参数',
            ];
            $data = [
                'address' =>  $data['address'],
                'receipt_type' => $data['receipt_type'],
                'introduce' => $data['introduce'],
                'service_type' => $data['service_type'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'city' => $data['city'],
                'area' => $data['area'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $this->userIdent();
            $this->userIsLevel();
            if(!empty($voice)){
                $info['voice'] = $voice;
            }
            if(!empty($img)){
                $info['img'] = json_encode($img);
            }
            $address = Db::name('user_address')->where(['user_id'=>$this->request->user_id,'id'=>$data['address']])->find();
            if(empty($address)){
                return $this->fail('地址错误', false);
            }
            if(!empty($unline_service) || !empty($unline_unit)){
                if(empty($unline_service)){
                    return $this->fail('请输入线下服务收费标准', false);
                }
                if(empty($unline_unit)){
                    return $this->fail('请选择线下服务计价单位', false);
                }
                $info['unline_service'] = $unline_service;
                $info['unline_unit'] = $unline_unit;
            }
            if(!empty($phone_service) || !empty($phone_unit)){
                if(empty($phone_service)){
                    return $this->fail('请输入手机服务收费标准', false);
                }
                if(empty($phone_unit)){
                    return $this->fail('请选择手机服务计价单位', false);
                }
                $info['phone_service'] = $phone_service;
                $info['phone_unit'] = $phone_unit;
            }
            if(!empty($longrange_service) || !empty($longrange_unit)){
                if(empty($longrange_service)){
                    return $this->fail('请输入线上服务收费标准', false);
                }
                if(empty($longrange_service)){
                    return $this->fail('请选择线上服务计价单位', false);
                }
                $info['longrange_service'] = $longrange_service;
                $info['longrange_unit'] = $longrange_unit;
            }
            if(empty($info['longrange_service']) && empty($info['unline_service']) && empty($info['phone_service'])){
                return $this->fail('至少选择一项收费标准', false);
            }
            $str = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

            $coordinate = (new Distance())->coordinate($data['area'],$data['city']);
//            $coordinate = (new Distance())->addresstolatlag($data['area'].$data['city']);
            $info['address'] = $data['address'];
            $info['receipt_type'] = $data['receipt_type'];
            $info['introduce']    = $data['introduce'];
            $info['service_type'] = $data['service_type'];
            $info['longitude']    = $coordinate['lng'];
            $info['latitude']     = $coordinate['lat'];
            $info['start_time']   = strtotime($data['start_time']);
            $info['end_time']     = strtotime($data['end_time']);
            $info['add_time']     =  time();
            $info['user_id']      =  $this->request->user_id;
            $info['order_on']     =  $str;

            try{
                Db::startTrans();
                //2019/7/23
//                $st = Db::name('receipt')->where(['user_id'=> $info['user_id']])->where('status','in','2,3,4')->find();
//                if($st){
//                    return $this->fail('请先完成未完成的订单',false);
//                }
                Db::name('receipt')->where(['user_id'=> $info['user_id']])->where('status','eq','0')->delete();
//                $online = Db::name('receipt')->where('status','neq',0)->where(['user_id'=> $info['user_id'] ,'receipt_type'=>$data['receipt_type'],'start_time'=>$info['start_time'],'end_time'=>$info['end_time']])->find();
//                if($online){
//                    return $this->fail('已存在相同的单,请更新此单时间',false);
//                }
                $receipt_id = Db::name('receipt')->insertGetId($info);
                // 提交事务
                Db::commit();
                return $this->succ(['msg'=>'已提交','receipt_id'=>$receipt_id]);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        }
    }

    /**
     * 支付诚意金
     */
    public function paymoney(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'money'      => 'require',
                'trade'      => 'require',
                'receipt_id' => 'require|number',
            ];
            $msg = [
                'receipt_id.require' => '错误操作',
                'receipt_id.number'  => '错误操作',
                'money.require'      => '请选择诚意金',
                'trade.require'      => '请输入交易密码',
            ];
            $data = [
                'receipt_id' => $data['receipt_id'],
                'money'      => $data['money'],
                'trade'      => $data['trade'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            try{
                Db::startTrans();
                if(!is_numeric($data['money'])){
                    return $this->fail('错误操作',false);
                }
                if(empty(floatval($data['money']))){
                    return $this->fail('错误操作',false);
                }
                $row = Db::table('tb_user')->where('id', $this->request->user_id)->find();
                if ($row) {
                    if (empty($row['trade'])){
                        return $this->fail('请先设置交易密码', false);
                    }
                    $checkPassword = $this->checkPassword($data['trade'], 2, $row['trade']);
                    if(!$checkPassword){
                        return $this->fail('密码错误', false);
                    }
                }else{
                    return $this->fail('?????', false);
                }

                $order = Db::name('receipt')->where(['id'=>$data['receipt_id']])->find();
                if(!$order){
                    return $this->fail('订单不存在!',false);
                }
                if(floatval($order['earnestmoney']) > 0){
                    return $this->fail('不能重复提交!',false);
                }


                //用户余额支付
                if(floatval($this->userinfo()['money']) < floatval($data['money'])){
                    return $this->fail('余额不足,请充值',false,2);
                }
                Db::name('user')->where(['id'=>$this->request->user_id])->setDec('money',floatval($data['money']));
                $this->add_log($this->request->user_id,1,7,floatval($data['money']),0);

                $online = Db::name('receipt')->where(['id'=>$data['receipt_id']])->setInc('earnestmoney',floatval($data['money']));
                $status = Db::name('receipt')->where(['id'=>$data['receipt_id']])->update(['status'=>1]);
                if(!$online || !$status){
                    return $this->fail('网络错误,请稍后再试..',false);
                }
                 #提交事务
                Db::commit();
                return $this->succ('已提交');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        }
    }

    /**
     * 大厅列表
     * @throws \think\exception\DbException
     */
    public function home(){
        $page = input('page');
        if(!is_numeric($page)){
            return $this->fail('错误操作',false);
        }
        $longitude = input('longitude');
        $latitude = input('latitude');
        if(empty($longitude) || empty($latitude)){
            return $this->fail('缺少必传参数',false);
        }
        if(!is_numeric($longitude) || !is_numeric($latitude)){
            return $this->fail('经纬度错误',false);
        }
        $db = Db::name('receipt');

        $sort = '';
        $selz = input('selz');
        if(!empty($selz) && isset($selz)){
            if($selz==1){
                //用户评论排名
                $user_evaluate = (new Home())->getStart('all');
                $str = implode(',',$user_evaluate);
                $db->where('user_id','in',$str);
                $sort = 'star';
            }elseif($selz==2 || $selz== 3){
                //最新发布
                $db->order('id desc');
            }
        }

        $selt = input('selt');
            if(!empty($selt) && isset($selt)){
                if($selt==1){
                    $sort = 'distance';
                }elseif($selt==2){   //技能认证
                    //最新发布
                    $db->order('id desc');
                }elseif($selt==3){   //技能认证
                    //最新发布
                    $db->where('voice','neq','');
                }elseif($selt==4){   //技能认证
                    //最新发布
                    $sort ='pay_money';
                }
            }
        $self = input('self');
        if(!empty($self) && isset($self)){
            if($self==1){   //线下服务
                $db->where('unline_service','gt',0);
            }elseif($self==2){   //电话服务
                $db->where('phone_service','gt',0);
            }elseif($self==3){   //线上服务
                $db->where('longrange_service','gt',0);
            }
        }
        $skill = input('skill'); //技能id
        if(!empty($skill) && isset($skill)){
            $where['service_type']  = $skill;
        }

        $where['status']  = 1;
//        $where['receipt_type']  = 1;
        $online = $db
                    ->where($where)
//                    ->where('status','neq',0)
//                    ->whereOr('status','neq',6)
//                    ->where('status','in'.'1')
                    ->where('user_id','neq',$this->request->user_id)
                    ->field('longitude,latitude,id,user_id,img,unline_unit,longrange_unit,phone_unit,unline_service,phone_service,longrange_service,start_time,end_time,service_type,add_time,earnestmoney,status,introduce')
                    ->order('id desc')
                    ->page($page,Env::get('app.page'))
                    ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
                    ->each(function($item){
                        $item['star'] = $this->getUserAllStar($item['user_id']);
                        $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                        $item['start_time'] = date('Y-m-d H:i:s',$item['start_time']);
                        $item['end_time'] = date('Y-m-d H:i:s',$item['end_time']);
                        $item['status_status'] = $item['status'];
                        $item['status'] = receipttype($item['status']);
                        $item['avatar'] = Db::name('user')->where(['id'=>$item['user_id']])->value('avatar');
                        $item['name'] = Db::name('user')->where(['id'=>$item['user_id']])->value('name');
                        if(floatval($item['unline_service']) > 0){
                            $item['unline_service'] = floatval($item['unline_service']).'/'.unittype($item['unline_unit']);
                        }
                        if(floatval($item['longrange_service']) > 0){
                            $item['longrange_service'] = floatval($item['longrange_service']).'/'.unittype($item['longrange_unit']);
                        }
                        if(floatval($item['phone_service']) > 0){
                            $item['phone_service'] = floatval($item['phone_service']).'/'.unittype($item['phone_unit']);
                        }
                        $item['img'] = json_decode($item['img']);

                        $join = [['tb_user b','b.id=a.user_id']];
                        $item['application'] = Db::name('receipt_aceept')
                            ->where(['a.receipt_id'=>$item['id']])
                            ->where('a.status','neq',6)
                            ->join($join)
                            ->alias('a')
                            ->field('a.*,b.name,b.avatar,b.age')
                            ->paginate(get_config('base','receipt_num'), false, ['query' => request()->param()])
                            ->each(function($item){
                                $skill =Db::name('user_skill')
                                    ->where(['user_id'=>$item['user_id'],'type'=>$item['service_type']])
                                    ->field('unline_service,unline_unit,longrange_service,longrange_unit,phone_service,phone_unit')
                                    ->find();
                                if(floatval($skill['unline_service']) > 0){
                                    $item['unline_service'] = floatval($skill['unline_service']).'/'.unittype($skill['unline_unit']);
                                }
                                if(floatval($skill['longrange_service']) > 0){
                                    $item['longrange_service'] = floatval($skill['longrange_service']).'/'.unittype($skill['longrange_unit']);
                                }
                                if(floatval($skill['phone_service']) > 0){
                                    $item['phone_service'] = floatval($skill['phone_service']).'/'.unittype($skill['phone_unit']);
                                }
                                return $item;
                            });
                        return $item;
                    });
        $arr = $online->toArray();
        $list = [];
        foreach ($arr['data'] as $key => $item){
            $Distance = new Distance();
            $rice  = $Distance->getdistance($item['longitude'],$item['latitude'],$longitude,$latitude);
            $item['distance'] = intval($rice);

            if(floatval($item['distance']) >= 1000){
                $arr['data'][$key]['distance'] =  floor($item['distance']);
                $arr['data'][$key]['last_distance'] =  floor(($item['distance'] /1000)*100)/100 . '公里';
            }else{
                $arr['data'][$key]['distance'] = intval($item['distance']);
                $arr['data'][$key]['last_distance'] = intval($item['distance']) .'米';
            }

            if($sort == 'distance' || $sort == 'star'){
                $list[$item[$sort]] = $arr['data'][$key];
            }else{
                $list[$key] = $arr['data'][$key];
            }
        }
        if($sort == 'distance'){

            ksort($list);
        }else{
            arsort($list);
        }
        //完成
        $arr['data'] = array_values($list);
        return $this->succ($arr);
    }
    /**
     * 获取用户总星数
     * @param $user_id
     * @return mixed
     */
    public function getUserAllStar($user_id){
        $num = 0;
        $num += round(Db::name('evaluate')->where(['uid'=>$user_id])->avg('attitude'));
        $num += round(Db::name('evaluate')->where(['uid'=>$user_id])->avg('skill'));
        $num += round(Db::name('evaluate')->where(['uid'=>$user_id])->avg('work'));
        return $num;
    }

    public function orderDetails(){
        $id = input('id');
        if(!is_numeric($id)){
            return $this->fail('错误操作',false);
        }
        $online = Db::name('receipt')->where(['id'=>$id])->find();
        if(empty($online)){
            return $this->fail('订单不存在',false);
        }
        if(floatval($online['unline_service']) > 0){
            $online['unline_service'] = floatval($online['unline_service']).'/'.unittype($online['unline_unit']);
        }
        if(floatval($online['longrange_service']) > 0){
            $online['longrange_service'] = floatval($online['longrange_service']).'/'.unittype($online['longrange_unit']);
        }
        if(floatval($online['phone_service']) > 0){
            $online['phone_service'] = floatval($online['phone_service']).'/'.unittype($online['phone_unit']);
        }
//        $online['add_time'] = date('Y-m-d H:i:s',$online['add_time']);
//        $online['start_time'] = date('Y-m-d H:i:s',$online['start_time']);
//        $online['end_time'] = date('Y-m-d H:i:s',$online['end_time']);

        $online['add_time'] = date('Y-m-d',$online['add_time']);
        $online['start_time'] = date('Y-m-d',$online['start_time']);
        $online['end_time'] = date('Y-m-d',$online['end_time']);

        $online['avatar'] = Db::name('user')->where(['id'=>$online['user_id']])->value('avatar');
        $online['name'] = Db::name('user')->where(['id'=>$online['user_id']])->value('name');

        $online['service_type'] = Db::name('product_category')->where(['cat_id'=>$online['service_type']])->find();
        $join = [['tb_user b','b.id=a.user_id']];
        $online['img'] = json_decode($online['img']);
        $online['receipt_address'] =[
            'lat'=> $this->getUserInfo($online['user_id'],'lat'),
            'lng'=> $this->getUserInfo($online['user_id'],'lng'),
        ];
        $online['receipt_phone'] = $online['mobile'];
        $accept_lng = 0;
        $accept_lat = 0;
        if($online['service_time'] == 0){
            $online['service_time'] = 1;
        }
        $online['all_money'] = $online['pay_money'] * $online['service_time'];
        $aceept_find = Db::name('receipt_aceept')->where(['id'=>$online['accept_id']])->find();
        if(empty($aceept_find)){
           Db::name('receipt')->where(['id'=>$id])->update(['accept_id'=>'']);
        }
        if(!empty($online['accept_id']) && !empty($aceept_find)){
            $online['accept'] = $aceept_find;
            $online['accept_phone'] = $aceept_find['phone'];
            $online['accept']['name']  =  $this->getUserInfo($online['accept']['user_id'],'name');
            $online['accept']['avatar']  =  $this->getUserInfo($online['accept']['user_id'],'avatar');
            $online['accept']['user_name']  = Db::name('user_auth')->where(['user_id'=>$online['accept']['user_id']])->value('name');
            $online['accept']['b_pay_money'] =$online['pay_money'] -(floatval(get_config('user','revenue')) * $online['pay_money'] /100);

            $online['accept_address'] =[
                'lat'=> $this->getUserInfo($online['accept']['user_id'],'lat'),
                'lng'=> $this->getUserInfo($online['accept']['user_id'],'lng'),
            ];

            $accept_lng = $online['accept_address']['lng'];
            $accept_lat = $online['accept_address']['lat'];
        }


        $rice = (new Distance())->getdistance($online['receipt_address']['lng'],$online['receipt_address']['lat'],$accept_lng,$accept_lat);
        if(floatval($rice) >= 1000){
            $online['distance'] =  floor((floatval($rice)/1000)*100)/100 . '公里';
        }else{
            $online['distance'] = intval($rice) .'米';
        }

        $online['application'] = Db::name('receipt_aceept')
                                    ->where('a.status','neq',6)
                                    ->where(['a.receipt_id'=>$online['id']])
                                    ->join($join)
                                    ->alias('a')
                                    ->field('a.*,b.name,b.avatar,b.age')
                                    ->paginate(get_config('base','receipt_num'), false, ['query' => request()->param()])
                                    ->each(function($item){
                                        $skill =Db::name('user_skill')
                                            ->where(['user_id'=>$item['user_id'],'type'=>$item['service_type']])
                                            ->field('unline_service,unline_unit,longrange_service,longrange_unit,phone_service,phone_unit')
                                            ->find();
                                        if(floatval($skill['unline_service']) > 0){
                                            $item['unline_service'] = floatval($skill['unline_service']).'/'.unittype($skill['unline_unit']);
                                        }
                                        if(floatval($skill['longrange_service']) > 0){
                                            $item['longrange_service'] = floatval($skill['longrange_service']).'/'.unittype($skill['longrange_unit']);
                                        }
                                        if(floatval($skill['phone_service']) > 0){
                                            $item['phone_service'] = floatval($skill['phone_service']).'/'.unittype($skill['phone_unit']);
                                        }
                                        $item['add_time'] = date('Y-m-d',$item['add_time'] );
                                        return $item;
                                    });
        if(!isset($online['id'])){
            return $this->fail('没有数据',false);
        }
        if($online['user_id'] == $this->request->user_id){  //发单人
            $online['all_money'] = $online['pay_money'] * $online['service_time'];
        }else{
            $online['all_money'] = $online['pay_money'] -(floatval(get_config('user','revenue')) * $online['pay_money']/100) * $online['service_time'];
            $online['last_service_price'] =  $online['pay_money'] - (floatval(get_config('user','revenue')) * $online['pay_money']/100);
        }
        return $this->succ($online);

    }

    /**
     * 约他
     */
    public function userAppointment(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'accept_id' => 'require|number',
                'receipt_id' => 'require|number',
                'last_service_type' => 'require',
                'last_service_price'=> 'require|number',
                'last_service_unit' => 'require|number',
                'service_time'      => 'require|number',
                'money'             => 'require',
                'trade'             => 'require',
            ];
            $msg = [
                'accept_id.require' => '错误操作',
                'accept_id.number'  => '错误操作',
                'receipt_id.require' => '错误操作',
                'receipt_id.number'  => '错误操作',
                'last_service_type.require'  => '请选择服务类型',
                'last_service_price.require' => '请选择服务价格',
                'last_service_price.number'  => '错误操作',
                'last_service_unit.require'  => '请选择服务单位',
                'last_service_unit.number'   => '错误操作',
                'service_time.require'       => '请选择服务时长',
                'money.require'              => '错误操作',
                'trade.require'              => '请输入交易密码',
                'service_time.number'        => '错误操作',

            ];
            $data = [
                'accept_id'           => $data['accept_id'],
                'receipt_id'           => $data['receipt_id'],
                'last_service_type'   => $data['last_service_type'],
                'last_service_price'  => $data['last_service_price'],
                'last_service_unit'   => $data['last_service_unit'],
                'service_time'        => $data['service_time'],
                'money'               => floatval($data['money']),
                'trade'               => $data['trade'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $password = $this->checkPassword($data['trade'],2,$this->getUserInfo($this->request->user_id,'trade'));
            if(!$password){
                return $this->fail('密码错误', false);
            }
            if(floatval($data['money']) > floatval($this->getUserInfo($this->request->user_id,'money')))  //余额支付
            {
                return $this->fail('余额不足,请先充值', false);
            }

             if(!is_numeric($data['money'])){
                 return $this->fail('错误操作', false);
             }
             if(floatval($data['money']) <= 0){
                 return $this->fail('该用户不支持此类型', false);
             }
             $all_price = floatval($data['last_service_price']) * floatval($data['service_time']);
             if($all_price != floatval($data['money'])){
                 return $this->fail('金额错误', false);
             }
             $accept = Db::name('receipt_aceept')->where(['id'=>$data['accept_id']])->find();
             if(empty($accept)){
                 return $this->fail('该用户已退出竞争', false);
             }
            $this->userIdent($accept['user_id']);
            $this->userIsLevel($accept['user_id']);
             $receipt = Db::name('receipt')->where(['id'=>$accept['receipt_id']])->find();
            if(empty($receipt)){
                return $this->fail('订单不存在', false);
            }
             if($receipt['status'] != 1 && isset($receipt['status'])){
                 return $this->fail('该订单不需要支付', false);
             }
            try{
                Db::startTrans();
                 //改变未约的用户状态
                 Db::name('receipt_aceept')->where(['receipt_id'=>$data['receipt_id']])->where('id','neq',$data['accept_id'])->update(['status'=>6]);
                 //扣除发单用户金额
                 Db::name('user')->where(['id'=>$this->request->user_id])->setDec('money',floatval($data['money']));
                 Db::name('receipt_aceept')->where(['id'=>$data['accept_id']])->update(['status'=>7]);  //改变接单人状态

                #扣除佣金,改变状态
                Db::name('receipt')->where(['id'=>$accept['receipt_id']])->update(
                    [
                        'status'=>7,  //待接单
                        'pay_money'=>$data['money'],
                        'storage_money'=>$data['money'],
                        'accept_id'=>$data['accept_id'],
                        'last_service_type'=>$data['last_service_type'],
                        'last_service_price'=>$data['last_service_price'],
                        'last_service_unit'=>$data['last_service_unit'],
                        'service_time'=>$data['service_time'],
                    ]);

                $this->add_log($this->request->user_id,1,11,$data['money'],0);
                //向接单用户发送组成信息
                $service_type = Db::name('product_category')->where(['cat_id'=>$receipt['service_type']])->value('cat_name');
                $msg = [
                    'type'=>'order',
                    'content'=>'您抢的'.$service_type.'类的订单已发单人被选中! 用户需求：'.$receipt['introduce'].',请及时处理订单,超时未接受将受到相应的处罚',
                    'admin'=>'admin',
                    'user_id'=>$accept['user_id'],
                    'title'=>'您已成功抢到'.$service_type.'订单,点击查看详情',
                    'add_time'=>time(),
                    'status'=>0,
                    'info'=>$receipt['id'],
                ];
                Db::name('info')->insert($msg);
                $cid = $this->getUserInfo($accept['user_id'],'cid');
                $push = new Push();
                $push->getui($cid,'您抢的'.$service_type.'类的订单已发单人被选中! 用户需求：'.$receipt['introduce'].',请及时处理订单,超时未接受将受到相应的处罚');


                //向接单人发送短信
                (new Msg())->sendInfo($accept['phone'],$service_type);
                // 提交事务
                Db::commit();
                return $this->succ('预约成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }else{
            $accept_id = intval(input('accept_id'));
            if($accept_id == 0){
                return $this->fail('错误操作', false);
            }
//            $accept_status = Db::name('receipt_aceept')->where(['id'=>$accept_id])->value('status');  //接单人订单状态

            $accept = Db::name('receipt_aceept')->field('user_id,id,service_type,receipt_id')->where(['id'=>$accept_id])->find();
            if(empty($accept)){
                return $this->fail('错误操作', false);
            }
//            $accept['accept_status'] = [
//                'status'=>$accept_status,
//                'notes' => '1 竞争中,等待用户确认，  2已确认'
//            ];
            $accept['name'] = $this->getUserInfo($accept['user_id'],'name');
            $accept['avatar'] = $this->getUserInfo($accept['user_id'],'avatar');
            $skill = Db::name('user_skill')->where(['user_id'=>$accept['user_id'],'type'=>$accept['service_type']])->field('unline_service,longrange_service,phone_service,unline_unit,phone_unit,longrange_unit,start_time,end_time')->find();

            $arr = [];
            if(!empty(floatval($skill['unline_service']))){
                $arr[] = [
                    'type'=>'1',
                    'name'=>'线下服务',
                    'service'=>floatval($skill['unline_service']),
                    'unit'=>$skill['unline_unit']
                ];
                $accept['unline_service'] = floatval($skill['unline_service']);
                $accept['unline_unit'] =  $skill['unline_unit'];
            }
            if(!empty(floatval($skill['longrange_service']))){
                $arr[] = [
                    'type'=>'3',
                    'name'=>'线上服务',
                    'service'=>floatval($skill['longrange_service']),
                    'unit'=>$skill['longrange_unit']
                ];
                $accept['longrange_service'] = floatval($skill['longrange_service']);
                $accept['longrange_unit'] = $skill['longrange_unit'];
            }
            if(!empty(floatval($skill['phone_service']))){
                $arr[] = [
                    'type'=>'2',
                    'name'=>'电话服务',
                    'service'=>floatval($skill['phone_service']),
                    'unit'=>$skill['phone_unit']
                ];
                $accept['phone_service'] = floatval($skill['phone_service']);
                $accept['phone_unit'] = $skill['phone_unit'];
            }

            $accept['server'] = empty($arr)?['type'=>'','name'=>'无服务']:$arr;
            unset($accept['user_id']);
            unset($accept['service_type']);
            return $this->succ($accept);

        }
    }

    public function senddx(){
        $res = (new Msg())->sendInfo(15924222221,'测试');
        dump($res);exit;
    }

    /**
     * 退出参与竞争
     */
    public function acceptOutDetalsOrder(){
         if ($this->request->isPost()) {
             $data = $this->request->param();
             $rule = [
                 'accept_id' => 'require|number',
             ];
             $msg = [
                 'accept_id.require' => '错误操作',
                 'accept_id.number' => '错误操作',
             ];
             $data = [
                 'accept_id' => $data['accept_id'],
             ];
             $validate = new Validate($rule, $msg);
             $result = $validate->check($data);
             if (!$result) {
                 return $this->fail($validate->getError(), false);
             }

             $where['user_id'] = $this->request->user_id;
             $where['id'] = $data['accept_id'];
             try{
                 Db::startTrans();
                 $receipt_aceept = Db::name('receipt_aceept')->where($where)->find();

                 $receipt = Db::name('receipt')->where(['id'=>$receipt_aceept['receipt_id']])->find();
                if($receipt['status'] == 7){
                    Db::name('receipt')->where(['id'=>$receipt['id']])->update(['status'=>1]);
                    #返还佣金,改变状态
                    Db::name('receipt')->where(['id'=>$receipt['id']])->update(
                        [
                            'status'=>1,  //待接单
                            'pay_money'=>0,
                            'storage_money'=>0,
                            'accept_id'=>0,
                            'last_service_type'=>0,
                            'last_service_price'=>0,
                            'last_service_unit'=>0,
                            'service_time'=>0,
                        ]);
                    //返还发单用户金额
                    Db::name('user')->where(['id'=>$receipt['user_id']])->setInc('money',floatval($receipt['pay_money']));

                    $this->add_log($receipt['user_id'],1,11,floatval($receipt['pay_money']),1);
                }
                 $cid = $this->getUserInfo($receipt['user_id'],'cid');
                 $push = new Push();
                 $push->getui($cid,'您的发单已有人退出参与,详情请点击查看约邻APP');
                 $this->sendInfo('您的发单已有人退出参与,详情请点击查看约邻APP',$receipt['user_id'],'您的发单已有人退出参与,详情请点击查看约邻APP',$receipt['id']);

                 Db::name('receipt')->where(['id'=>$receipt_aceept['receipt_id']])->update(['accept_id'=>0]);

                 Db::name('receipt_aceept')->where($where)->delete();
                 // 提交事务
                 Db::commit();
                 return $this->succ('已退出竞争');
             } catch (\Exception $e) {
                 // 回滚事务
                 Db::rollback();
             }
         }
    }
    /**
     * 退出参与发单人已付款的接单
     */
    public function acceptOutOrder(){
         if ($this->request->isPost()) {
             $data = $this->request->param();
             $rule = [
                 'receipt_id' => 'require|number',
             ];
             $msg = [
                 'receipt_id.require' => '错误操作',
                 'receipt_id.number' => '错误操作',
             ];
             $data = [
                 'receipt_id' => $data['receipt_id'],
             ];
             $validate = new Validate($rule, $msg);
             $result = $validate->check($data);
             if (!$result) {
                 return $this->fail($validate->getError(), false);
             }
             try{
                 Db::startTrans();
                 $receipt = Db::name('receipt')->where(['id'=>$data['receipt_id']])->field('id,user_id,pay_money,status,accept_id')->find();  //用户支付的金额
                 $aceept_user_id = Db::name('receipt_aceept')->where(['id'=>$receipt['accept_id']])->value('user_id');
                 if($receipt['status'] != 1){
                     return $this->fail('当前订单不能取消',false);
                 }
                 Db::name('user')->where(['id'=>$receipt['user_id']])->setInc('money',floatval($receipt['pay_money']));//退还发单用户支付的金额
                 $this->add_log($receipt['user_id'],1,16,floatval($receipt['pay_money']));
                 //订单失效
                 $where['receipt_id'] = $data['receipt_id'];
                 Db::name('receipt_aceept')->where($where)->update(['status'=>5]);
                 Db::name('receipt')->where(['id'=>$data['receipt_id']])->update(['status'=>5]);
                 //改变订单状态
                 $push = new Push();
                 $cids = $this->getUserInfo($aceept_user_id,'cid');

                 $cid = $this->getUserInfo($receipt['user_id'],'cid');
                 $push->getui($cid,'订单已取消, 支付金额已退出余额');
                 $this->sendInfo('订单已取消',$aceept_user_id,'订单已取消',$data['receipt_id'],11);
                 $this->sendInfo('订单已取消, 支付金额已退出余额',$receipt['user_id'],'订单已取消, 支付金额已退出余额',$data['receipt_id'],11);

                 // 提交事务
                 Db::commit();
                 //推送消息提醒订单取消
                 $push->getui($cids,'订单已取消');
                 return $this->succ('订单已失效');
             } catch (\Exception $e) {
                 // 回滚事务
                 Db::rollback();
             }
         }
    }

    public function acceptOrder(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'accept_id' => 'require|number',
            ];
            $msg = [
                'accept_id.require' => '错误操作',
                'accept_id.number' => '错误操作',
            ];
            $data = [
                'accept_id' => $data['accept_id'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $this->userIdent();
            $this->userIsLevel();
            try{
                Db::startTrans();
                $receipt_id = Db::name('receipt_aceept')->where(['id'=>$data['accept_id']])->value('receipt_id');
                $pay_money = Db::name('receipt')->where(['id'=>$receipt_id])->value('pay_money');
                $service_type = Db::name('receipt')->where(['id'=>$receipt_id])->value('service_type');
                $cat_name = Db::name('product_category')->where(['cat_id'=>$service_type])->value('cat_name');
                if(floatval($pay_money) == 0){
                    return $this->fail('订单未付款',false);
                }
                Db::name('receipt_aceept')->where(['id'=>$data['accept_id'],'user_id'=>$this->request->user_id])->update(['status'=>2,'add_time'=>time()]);
                Db::name('receipt')->where(['id'=>$receipt_id])->update(['status'=>2]);
                //向接单用户发送组成信息
                $user = Db::name('receipt')->where(['id'=>$receipt_id])->value('user_id');
                $msg = [
                    'receipt_id'=>$user,
                    'accept_id'=>Db::name('receipt_aceept')->where(['id'=>$data['accept_id']])->value('user_id'),
                    'order_id'=>$receipt_id,
                    'add_time'=>time(),
                ];
                Db::name('user_msg')->insert($msg);
                // 提交事务
                Db::commit();
                $cid = $this->getUserInfo($user,'cid');
                $push = new Push();
                $push->getui($cid,'您的发单'.$cat_name.',接单用户已确认接单啦，详情请查看约邻APP');

                $this->sendInfo('您的发单'.$cat_name.',接单用户已确认接单啦，详情请查看约邻APP',$user,'您的发单'.$cat_name.',接单用户已确认接单啦，详情请查看约邻APP',$receipt_id,2);
                //向发单人发送短信
                (new Msg())->sendFadan($this->getUserInfo($user,'mobile'),$cat_name);
                return $this->succ('已确认接单');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }
    }



    public function orderApplication(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'receipt_id' => 'require|number',
                'type' => 'require|number',
                'phone' => 'require',
            ];
            $msg = [
                'phone.require' => '请输入手机号',
                'receipt_id.require' => '错误操作',
                'receipt_id.number' => '错误操作',
                'type.require' => '错误操作',
                'type.number' => '错误操作',
            ];
            $data = [
                'receipt_id' => $data['receipt_id'],
                'phone' => $data['phone'],
                'type' => $data['type'],  //1自助接单  2远程/ 帮他人接单
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $this->userIdent();
            $this->userIsLevel();
            //2019/7/23
//            $moneystatus = Db::name('receipt_aceept')->where(['user_id'=> $this->request->user_id])->where('money','lt',0)->where('status','eq',6)->find();
//            if($moneystatus){
//                return $this->fail('你有欠款在身，不能接单', false);
//            }

            $receipt = Db::name('receipt')->where(['id' => $data['receipt_id']])->where('user_id','neq',$this->request->user_id)->find();
            if(empty($receipt)){
                return $this->fail('不能接自己发布的订单', false);
            }
            #接单服务类型 $receipt['service_type']
            #获取当前用户所有技能
            $skill =  $this->userSkill($this->request->user_id,$receipt['service_type'],0);
            if($skill){
                return $this->fail('您的技能'.$this->getCatName($receipt['service_type']).'正在审核中,请等待管理员审核', false);
            }
            $skill =  $this->userSkill($this->request->user_id,$receipt['service_type']);
            if(empty($skill)){
                return $this->fail('需要认证技能：'.$this->getCatName($receipt['service_type']), false,5);
            }
            #限制申请人数
            $receipt_num = get_config('base','receipt_num');
            $accept_num = Db::name('receipt_aceept')->where(['receipt_id'=>$data['receipt_id']])->where('status','neq',6)->count();
            if(intval($accept_num) >= $receipt_num){
                return $this->fail('需求人数已满，快去抢其他的单吧', false);
            }
            $info = [
                'user_id' => $this->request->user_id,
                'service_type' => $receipt['service_type'],
                'add_time' => time(),
                'receipt_id' => $data['receipt_id'],
                'phone' => $data['phone'],
                'type' => $data['type'],
            ];
            try{
                Db::startTrans();
                $st = Db::name('receipt_aceept')->where(['user_id'=> $info['user_id'] ,'status'=>['in','0,1,2,7,8,5']])->find();
                if($st){
                    return $this->fail('请先完成未完成的订单',false);
                }

                $fin = Db::name('receipt_aceept')
                    ->where([
                        'user_id'=>$this->request->user_id,
                        'service_type'=>$receipt['service_type'],
                        'receipt_id'=>$receipt['id'],
                    ])->find();
                if($fin){
                    return $this->fail('您已经申请过此订单,或者您已经退出过订单',false);
                }
                Db::name('receipt_aceept')->where([
                        'user_id'=>$this->request->user_id,
                        'receipt_id'=>$receipt['id']
                    ])->delete();

                $getid = Db::name('receipt_aceept')->insertGetId($info);
                if(!$getid){
                    return $this->fail('网络错误.请稍后再试',false);
                }

                //向发单用户发送组成信息
                $service_type = Db::name('product_category')->where(['cat_id'=>$receipt['service_type']])->value('cat_name');
                $msg = [
                    'type'=>'order',
                    'content'=>'您的'.$service_type.'类发单订单有人应聘啦!,请及时处理订单,超时未接受将受到相应的处罚',
                    'admin'=>'admin',
                    'user_id'=>$receipt['user_id'],
                    'title'=>'您的发单'.$service_type.'订单有新的应聘消息,点击查看详情',
                    'add_time'=>time(),
                    'status'=>0,
                    'info'=>$receipt['id'],
                ];
                Db::name('info')->insert($msg);
                $cid = $this->getUserInfo($receipt['user_id'],'cid');
                $push = new Push();
                $push->getui($cid,'您的'.$service_type.'类发单订单有人应聘啦!,请及时处理订单,超时未接受将受到相应的处罚');

                // 提交事务
                Db::commit();
                return $this->succ('已提交');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }
    }

    /**
     * 用户技能查询
     * @param $user_id int  用户id
     * @param int $skill int 技能id
     * @return array|int|\PDOStatement|string|\think\Collection|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userSkill($user_id,$skill = 0,$status = 1){
        $where['user_id'] = $user_id;
        $where['status'] = $status;
        if($skill != 0){
            $where['type'] = $skill;
            $online = Db::name('user_skill')->where($where)->find();
            if(empty($online)){
                return 0;
            }
            $online['cat_name'] = $this->getCatName($online['type']);
            return $online;
        }else{
            $join = [['tb_product_category b','b.cat_id=a.type']];
            $online = Db::name('user_skill')
                        ->join($join)
                        ->where($where)
                        ->alias('a')
                        ->field('a.*,b.cat_name')
                        ->select();
            return $online;
        }

    }

    /**
     * 获取类型名
     * @param $cat_id
     * @return mixed
     */
    public function getCatName($cat_id){
        return Db::name('product_category')->where(['cat_id'=>$cat_id])->value('cat_name');
    }

    /**
     * 我的发单
     */
    public function myOrderInvoice(){
        $page = input('page');
        $type = input('type');
        if(!is_numeric($page)){
            return $this->fail('错误操作',false);
        }
        if(!is_numeric($type)){
            return $this->fail('错误操作',false);
        }
        if($type != 0){
            $where['status']  = $type;
        }
        $where['receipt_type']  = 1;
        $online = Db::name('receipt')
            ->where($where)
            ->where('user_id','eq',$this->request->user_id)
            ->where('status','neq',5)
            ->order('id desc')
            ->page($page,Env::get('app.page'))
            ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
            ->each(function($item){
                $item['receipt_address'] =[
                    'lat'=> $this->getUserInfo($this->request->user_id,'lat'),
                    'lng'=> $this->getUserInfo($this->request->user_id,'lng'),
                ];
                if(!empty($item['accept_id'])){
                    $accept_id_address = Db::name('receipt_aceept')->where(['id'=>$item['accept_id']])->value('user_id');
                    $item['accept_address'] =[
                        'lat'=> $this->getUserInfo($accept_id_address,'lat'),
                        'lng'=> $this->getUserInfo($accept_id_address,'lng'),
                    ];
                }


                $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                $item['cat_img'] = Db::name('product_category')->where(['cat_id'=>$item['service_type']])->value('thumb');
                $item['cat_name'] = Db::name('product_category')->where(['cat_id'=>$item['service_type']])->value('cat_name');
                $item['start_time'] = date('Y-m-d H:i:s',$item['start_time']);
                $item['end_time'] = date('Y-m-d H:i:s',$item['end_time']);
                $accept_id =  Db::name('receipt_aceept')->where(['receipt_id'=>$item['id']])->where('status','neq',1)->value('user_id');
                $item['arrive']  =  Db::name('receipt_aceept')->where(['receipt_id'=>$item['id']])->where('status','neq',1)->value('arrive');
                $item['accept_id'] = empty($accept_id)?'':$accept_id;
                $item['pinglun'] = empty(Db::name('evaluate')->where(['receipt_id'=>$item['id'],'type'=>1])->find())?0:1;

                if(floatval($item['unline_service']) > 0){
                    $item['unline_service'] = floatval($item['unline_service']).'/'.unittype($item['unline_unit']);
                }
                if(floatval($item['longrange_service']) > 0){
                    $item['longrange_service'] = floatval($item['longrange_service']).''.unittype($item['longrange_unit']);
                }
                if(floatval($item['phone_service']) > 0){
                    $item['phone_service'] = floatval($item['phone_service']).'/'.unittype($item['phone_unit']);
                }
                $item['img'] = json_decode($item['img']);
                $item['status_status'] = $item['status'];
               if($item['status'] == 1){
                   $user_join = [['tb_user b','b.id=a.user_id']];
                   $item['application'] = Db::name('receipt_aceept')
                       ->alias('a')
                       ->join($user_join)
                       ->where(['a.receipt_id'=>$item['id']])
                       ->where('a.status','neq',6)
                       ->field('a.id,b.name,b.avatar,a.status')
                       ->select();
                   $item['application_count'] = count($item['application']);
               }

                $item['status'] = receipttype($item['status']);
                return $item;
            });
        if(empty($online->toArray()['data'])){
            return $this->fail([],false);
        }
        return $this->succ($online);
    }

    /**
     * 进行中的订单详情get 获取列表    post   付款
     */
    public function orderConduct(){
        if($this->request->isPost()){
                $data = $this->request->param();
                $rule = [
                    'receipt_id' => 'require|number',
                    'trade' => 'require',
                ];
                $msg = [
                    'trade.require' => '请输入支付密码',
                    'receipt_id.require' => '错误操作',
                    'receipt_id.number' => '错误操作',
                ];
                $data = [
                    'receipt_id' => $data['receipt_id'],
                    'trade' => $data['trade'],
                ];
                $validate = new Validate($rule, $msg);
                $result = $validate->check($data);
                if (!$result) {
                    return $this->fail($validate->getError(), false);
                }

            try{
                Db::startTrans();
                $password = $this->checkPassword($data['trade'],2,$this->getUserInfo($this->request->user_id,'trade'));
                if(!$password){
                    return $this->fail('密码错误', false);
                }

                //付款
                $receipt = Db::name('receipt')->where(['id'=>$data['receipt_id'],'user_id'=>$this->request->user_id])->where(['status'=>2])->field('user_id,earnestmoney,storage_money,pay_money,accept_id')->find();
                $accept = Db::name('receipt_aceept')->where(['id'=>$receipt['accept_id']])->where(['status'=>2])->field('user_id,arrive')->find();
                if(empty($receipt) || empty($accept)){
                    return $this->fail('订单不存在', false);
                }
                if($accept['arrive'] == 0){
                    return $this->fail('接单人未确认开始服务，不能确认付款', false);
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

                Db::name('receipt')->where(['id'=>$data['receipt_id']])->update(['status'=>3]);  //发单人已付款状态   订单完成 待评价
                Db::name('receipt_aceept')->where(['id'=>$receipt['accept_id']])->update(['status'=>4]);  //发单人已付款状态   订单完成
                Db::name('user_msg')->where(['order_id'=>$data['receipt_id']])->update(['status'=>1]);  //发单人已付款状态   订单完成  改变 聊天状态

                // 提交事务
                Db::commit();
                $push = new Push();
                $cid = $this->getUserInfo($accept['user_id'],'cid');
                $push->getui($cid,'发单人已确认付款');
                $cid2 = $this->getUserInfo($receipt['user_id'],'cid');
                $push->getui($cid2,'订单已完成');

                $this->sendInfo('发单人已确认付款',$accept['user_id'],'发单人已确认付款',$data['receipt_id'],9);
                $this->sendInfo('订单已完成',$receipt['user_id'],'订单已完成',$data['receipt_id'],10);

                return $this->succ('付款成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }else{
            $receipt_id = intval(input('receipt_id'));
            if($receipt_id == 0){
                return $this->fail('错误操作', false);
            }
            $receipt = Db::name('receipt')->where(['id'=>$receipt_id,'user_id'=>$this->request->user_id])->field('pay_money,service_type,user_id,accept_id,last_service_type,last_service_price,last_service_unit,service_time,pay_money')->find();
            if(empty($receipt)){
                return $this->fail('错误操作', false);
            }
             if($receipt['last_service_type'] == 1){
                 $receipt['last_service_type']= '线下服务';
             }if($receipt['last_service_type'] == 2){
                 $receipt['last_service_type']= '电话服务';
             }if($receipt['last_service_type'] == 3){
                 $receipt['last_service_type']= '线上服务';
             }
            $receipt['service_type'] = Db::name('product_category')->where(['cat_id'=>$receipt['service_type']])->value('cat_name');
            $receipt['name'] = $this->getUserInfo($receipt['user_id'],'name');
            $receipt['avatar'] = $this->getUserInfo($receipt['user_id'],'avatar');
            $accept_user = Db::name('receipt_aceept')->where(['id'=>$receipt['accept_id']])->value('user_id');
            $accept_address = Db::name('user_address')->where(['user_id'=>$accept_user])->find();
            $receipt['accept_name'] = $accept_address['name'];
            $receipt['arrive'] = Db::name('receipt_aceept')->where(['id'=>$receipt['accept_id']])->value('arrive');
            $receipt['accept_mobile'] = $accept_address['mobile'];
            return $this->succ($receipt);
        }
    }

    public function confirmPayMoney(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'accept_id' => 'require|number',
//                'trade' => 'require',
            ];
            $msg = [
//                'trade.require' => '请输入支付密码',
                'accept_id.require' => '错误操作',
                'accept_id.number' => '错误操作',
            ];
            $data = [
                'accept_id' => $data['accept_id'],
//                'trade' => $data['trade'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            try{
                Db::startTrans();
//                $password = $this->checkPassword($data['trade'],2,$this->getUserInfo($this->request->user_id,'trade'));
//                if(!$password){
//                    return $this->fail('密码错误', false);
//                }

                //确认已付款必须是  发单用户  订单状态=4  已完成支付   当前接单用户状态=2 状态进行中
                 $accept = Db::name('receipt_aceept')->where(['id'=>$data['accept_id']])->where(['status'=>2])->field('receipt_id,user_id')->find();
                 $receipt = Db::name('receipt')->where(['id'=>$accept['receipt_id']])->where(['status'=>3])->field('user_id,earnestmoney,storage_money,pay_money,accept_id')->find();
                if(!$accept || !$receipt){
                    return $this->fail('发单用户暂未付款',false);
                }
                $res = Db::name('receipt_aceept')->where(['id'=>$data['accept_id'],'user_id'=>$this->request->user_id])->update(['status'=>3]);  //完成订单
                if(!$res){
                    return $this->fail('网络错误',false);
                }

                // 提交事务
                Db::commit();
                return $this->succ('订单完成');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }
    }

    /**
     * 我的接单
     */
    public function myOrderAccept(){
        $page = input('page');
        $type = input('type');
        if(!is_numeric($page)){
            return $this->fail('错误操作',false);
        }
        if(!is_numeric($type)){
            return $this->fail('错误操作',false);
        }
        $join = [['tb_receipt b','b.id=a.receipt_id']];
        $where = [];
        if($type != 0){
            $where['a.status']  = $type;
        }
        $online = Db::name('receipt_aceept')
            ->where($where)
            ->join($join)
            ->alias('a')
            ->where('a.user_id','eq',$this->request->user_id)
            ->where('a.status','neq',5)
            ->order('a.id desc')
            ->field('a.*,b.start_time,b.end_time,b.user_id as fadan_id,b.introduce as b_introduce,b.voice as b_voice, b.img as b_img,b.pay_money as b_pay_money,b.order_on')
            ->page($page,Env::get('app.page'))
            ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
            ->each(function($item){
                $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                $item['start_time'] = date('Y-m-d H:i:s',$item['start_time']);
                $item['end_time'] = date('Y-m-d H:i:s',$item['end_time']);
                $item['cat_img'] = Db::name('product_category')->where(['cat_id'=>$item['service_type']])->value('thumb');
                $item['cat_name'] = Db::name('product_category')->where(['cat_id'=>$item['service_type']])->value('cat_name');
                $item['pinglun'] = empty(Db::name('evaluate')->where(['receipt_id'=>$item['receipt_id'],'type'=>2])->find())?0:1;
                $item['fandan_info']['name'] = $this->getUserInfo($item['fadan_id'],'name');
                $item['fandan_info']['avatar'] = $this->getUserInfo($item['fadan_id'],'avatar');
                $item['status_status'] = $item['status'];
              if($item['status'] == 1){
                  $user_join = [['tb_user b','b.id=a.user_id']];
                  $item['application'] = Db::name('receipt_aceept')
                      ->alias('a')
                      ->join($user_join)
                      ->where(['a.receipt_id'=>$item['receipt_id']])
                      ->field('a.id,b.name,b.avatar')
                      ->select();
                  $item['application_count'] = count($item['application']);
              }
                $item['order_status'] = $item['status'];
                $item['accept_status'] = $item['status'];
                $item['status'] = receipttype($item['status']);
                $item['b_img'] = json_decode($item['b_img']);
                $item['b_pay_money'] = floatval($item['b_pay_money']) -(floatval(get_config('user','revenue')) * $item['b_pay_money'] /100);


                 return $item;
            });
        if(empty($online->toArray()['data'])){
            return $this->fail([],false);
        }
        return $this->succ($online);
    }


    /**
     * 评论
     */
    public function evaluate(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $evaluate_attitude = intval($data['attitude']);
            $evaluate_skill = intval($data['skill']);
            $evaluate_work = intval($data['work']);
            $evaluate_content = $data['content'];
            $evaluate_img = $data['img'];
            $rule = [
                'type' => 'require|number',   #1发单方  2接单方
                'uid' => 'require|number',   #1发单方  2接单方
                'receipt_id' => 'require|number',   #订单id
            ];
            $msg = [
                'type.require' => '错误操作',
                'type.number' => '错误操作',
                'uid.require' => '错误操作',
                'uid.number' => '错误操作',
                'receipt_id.require' => '错误操作',
                'receipt_id.number' => '错误操作',
            ];
            $data = [
                'type' => $data['type'],  //1接单  2发单
                'uid' => $data['uid'],  //被评论人id
                'receipt_id' => $data['receipt_id'],  //订单id
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $info = [
              'attitude'=>$evaluate_attitude,
              'skill'=>$evaluate_skill,
              'work'=>$evaluate_work,
              'content'=>$evaluate_content,
              'img'=>$evaluate_img,
              'receipt_id'=>$data['receipt_id'],
              'uid'=>$data['uid'],
              'type'=>$data['type'],
              'user_id'=>$this->request->user_id,
              'add_time'=>time()
            ];
            try{
                Db::startTrans();

                $order = Db::name('receipt')->where(['id'=>$info['receipt_id'],'user_id'=>$this->request->user_id])->find();
                if($order){
                    Db::name('receipt')->where(['id'=>$info['receipt_id'],'user_id'=>$this->request->user_id])->update(['status'=>4]);
                }else{
                    Db::name('receipt_aceept')->where(['receipt_id'=>$info['receipt_id'],'user_id'=>$this->request->user_id])->update(['status'=>4]);
                }
                Db::name('evaluate')->where([
                    'user_id'=>$this->request->user_id,
                    'receipt_id'=>$info['receipt_id'],
                    'type'=>$info['type'],
                    'uid'=>$info['uid'],
                ])->delete();

                $getid = Db::name('evaluate')->insertGetId($info);
                if(!$getid){
                    return $this->fail('网络错误.请稍后再试',false);
                }

                // 提交事务
                Db::commit();
                return $this->succ('已完成');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }


        }
    }

    /**
     * 用户被评论的平均星级    如： 我的个人发单详情内显示评论星级为：我的发单被接单人评论的平均星级    我的接单被发单人评论的平均星级   uid
     * @param $user_id   int 用户id
     * @param int $type  1 发单  2接单
     * @return mixed
     */
    public function getUserStar($user_id, $type = 0,$self_type = 0){
        $online['attitude'] = round(Db::name('evaluate')->where(['uid'=>$user_id,'type'=>$type])->avg('attitude'));
        $online['skill'] = round(Db::name('evaluate')->where(['uid'=>$user_id,'type'=>$type])->avg('skill'));
        $online['work'] = round(Db::name('evaluate')->where(['uid'=>$user_id,'type'=>$type])->avg('work'));
        return $online;
    }

    public function cancelOrder(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'receipt_id' => 'require|number',   #订单id
            ];
            $msg = [
                'receipt_id.require' => '错误操作',
                'receipt_id.number' => '错误操作',
            ];
            $data = [
                'receipt_id' => $data['receipt_id'],  //订单id
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            try{
                Db::startTrans();
                $order = Db::name('receipt')->where(['id'=>$data['receipt_id'],'user_id'=>$this->request->user_id])->find();
                $t = '';
                if(isset($order['status'])){
                    switch ($order['status']){
                        case 0:
                            Db::name('receipt')->delete($data['receipt_id']);
                            $t = '已取消';
                            break;
                        case 1:
                            #退还诚意金
                            if(floatval($order['earnestmoney']) > 0){
                                $earnestmoney = $this->fzper($order['earnestmoney']);
                                Db::name('user')->where(['id'=>$this->request->user_id])->setInc('money',$earnestmoney);
                                $this->add_log($this->request->user_id, 1,17,$earnestmoney);
                            }
                            if(floatval($order['pay_money']) > 0){
                                Db::name('user')->where(['id'=>$this->request->user_id])->setInc('money',$order['pay_money']);
                                $this->add_log($this->request->user_id, 1,16,$order['pay_money']);
                            }
                            Db::name('receipt')->delete($data['receipt_id']);
                            if(!empty($order['accept_id'])){
                                Db::name('receipt_aceept')->delete($order['accept_id']);
                            }
                            $t = '已取消';
                            break;
                        case 2:
                            $arrive = Db::name('receipt_aceept')->where(['id'=>$order['accept_id']])->value('arrive');
                            if($arrive == 0){
                                #退还诚意金
                                if(floatval($order['earnestmoney']) > 0){
                                    $earnestmoney = $this->fzper($order['earnestmoney']);
                                    Db::name('user')->where(['id'=>$this->request->user_id])->setInc('money',$earnestmoney);
                                    $this->add_log($this->request->user_id, 1,17,$earnestmoney);
                                }
                                if(floatval($order['pay_money']) > 0){
                                    Db::name('user')->where(['id'=>$this->request->user_id])->setInc('money',$order['pay_money']);
                                    $this->add_log($this->request->user_id, 1,16,$order['pay_money']);
                                }
                                Db::name('receipt')->where(['id'=>$data['receipt_id']])->update(['status'=>6]);
                                if(!empty($order['accept_id'])){
                                    Db::name('receipt_aceept')->where(['id'=>$order['accept_id']])->update(['status'=>6]);
                                }


                                $t = '已取消';
                                break;
                            }else{
                                $t = '接单人已到达,暂不允许取消,更多更新详情,请留意官方公告';
                            }

                            break;
                        case 3:
                            $t = '待评价的订单暂不允许被删除';
                            break;
                        case 4:
                            Db::name('receipt')->delete($data['receipt_id']);
                            $t = '已取消';
                            break;
                        case 5:
                            Db::name('receipt')->delete($data['receipt_id']);
                            $t = '已取消';
                            break;
                        case 6:
                            Db::name('receipt')->delete($data['receipt_id']);
                            $t = '已取消';
                            break;
                        case 7:
                                #退还诚意金
                                if(floatval($order['earnestmoney']) > 0){
                                    $earnestmoney = $this->fzper($order['earnestmoney']);
                                    Db::name('user')->where(['id'=>$this->request->user_id])->setInc('money',$earnestmoney);
                                    $this->add_log($this->request->user_id, 1,17,$earnestmoney);
                                }
                                if(floatval($order['pay_money']) > 0){
                                    Db::name('user')->where(['id'=>$this->request->user_id])->setInc('money',$order['pay_money']);
                                    $this->add_log($this->request->user_id, 1,16,$order['pay_money']);
                                }
                                Db::name('receipt')->where(['id'=>$data['receipt_id']])->update(['status'=>6]);
                                if(!empty($order['accept_id'])){
                                    Db::name('receipt_aceept')->where(['id'=>$order['accept_id']])->update(['status'=>6]);
                                }
                            $t = '已取消';
                            break;
                        case 8:
                            Db::name('receipt')->delete($data['receipt_id']);
                            $t = '已取消';
                            break;
                        case 9:
                            Db::name('receipt')->delete($data['receipt_id']);
                            $t = '已取消';
                            break;
                    }
                }else{
                    $t = '订单不存在';
                    return $this->fail($t,false);
                }

                // 提交事务
                Db::commit();
                return $this->succ(['msg'=>$t]);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }


        }
    }

    protected function fzper($money){
        $fzper = get_config('base','fzper');
        if ($fzper > 0){
            $earnestmoney = $money * $fzper/100;
        }else{
            $earnestmoney = $money;
        }
        return $earnestmoney;
    }

    /**
     * 发单人删除订单
     */
    public function orderDel(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'skill_id' => 'require|number',   #订单id
            ];
            $msg = [
                'skill_id.require' => '错误操作',
                'skill_id.number' => '错误操作',
            ];
            $data = [
                'skill_id' => $data['skill_id'],  //订单id
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            try{
                Db::startTrans();
                $order = Db::name('user_skill')->where(['id'=>$data['skill_id'],'user_id'=>$this->request->user_id])->find();
                if($order){
                    Db::name('user_skill')->delete($data['skill_id']);
                }else{
                    return $this->fail('删除错误', false);
                }
                // 提交事务
                Db::commit();
                return $this->succ('删除成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        }
    }

    public function arrive(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'longitude' => 'require',
                'latitude' => 'require',
                'accept_id' => 'require|number',
            ];
            $msg = [
                'accept_id.require' => '错误操作',
                'accept_id.number' => '错误操作',
                'longitude.require' => '错误操作',
                'latitude.require' => '错误操作',
            ];
            $data = [
                'accept_id' => $data['accept_id'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            try{
                Db::startTrans();
                $order = Db::name('receipt_aceept')->where(['id'=> $data['accept_id'],'user_id'=>$this->request->user_id,'arrive'=>1])->find();

                $info = [
                  'longitude'=>$data['longitude'],
                  'latitude'=>$data['latitude'],
                  'arrive'=>1,
                  'arrive_time'=>time(),
                ];
                if($order){
                    Db::name('receipt_aceept')->where(['id'=> $data['accept_id'],'user_id'=>$this->request->user_id])->update($info);
                    $t = '已确认开始服务';
                }else{
                    Db::name('receipt_aceept')->where(['id'=> $data['accept_id'],'user_id'=>$this->request->user_id])->update($info);
                    $t = '已确认开始服务';
                }
                $accept = Db::name('receipt_aceept')->where(['id'=>$data['accept_id']])->find();  //接单人待付
                $receipt = Db::name('receipt')->where(['id'=>$accept['receipt_id']])->find();  //接单人待付
                // 提交事务

                $push = new Push();
                $cid = $this->getUserInfo($accept['user_id'],'cid');
                $push->getui($cid,'已开始服务');
                $fduser = $receipt['user_id'];
                $cid2 = $this->getUserInfo($fduser,'cid');
                $push->getui($cid2,'接单人已确认开始服务');

                $this->sendInfo('您已经开始服务',$accept['user_id'],'开始服务',$accept['receipt_id'],3);
                $this->sendInfo('您发布的订单,接单人已确认开始服务',$fduser,'接单人已确认开始服务',$accept['receipt_id'],4);
                Db::commit();
                return $this->succ($t);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        }
    }


}