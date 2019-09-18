<?php

namespace app\api\controller;

use app\helps\Auth;
use app\helps\Msg;
use think\facade\Env;
use think\Validate;
use app\helps\Mns;
use think\Db;

/**
 * Class getallarder
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
class Home extends Base
{

    public function edition(){
        $version = input('version');
        if(empty($version)){
            return $this->fail('错误操作',false);
        }

        $sever_version = get_config('base','version'); 
        if(floatval($version) != floatval($sever_version)){
            $arr = [
                't'=>0,
                'link'=>$_SERVER['HTTP_HOST'].'/apk/index.apk'
            ];
            return $this->fail($arr,false);
        }else{
            $arr = [
                't'=>1,
                'link'=>$_SERVER['HTTP_HOST'].'/apk/index.apk'
            ];
            return $this->succ($arr);
        }
        
    }

    public function userMassage(){
        $type= input('type');
        if(empty($type)){
            return $this->fail('缺少必要参数');
        }

        //type 1聊天信息 2 系统消息 3订单消息
        if($type == 2){
            $online = Db::name('info')
                         ->where(['user_id'=>0])
                         ->whereOr(['user_id'=>$this->request->user_id])
                         ->where('type','in','notice')
                         ->order('id desc')
                         ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
                         ->each(function($item){
                             $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                             return $item;
                         });
        } elseif($type == 1){
            $online = '';
//            $online = Db::name('user_msg')->where(['receipt_id'=>$this->request->user_id])
//                        ->whereOr(['accept_id'=>$this->request->user_id])
//                        ->order('id desc')
//                        ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
//                        ->each(function($item){
//                            $service_type = Db::name('receipt')->where(['id'=>$item['order_id']])->value('service_type');
//                            $cat = Db::name('product_category')->where(['cat_id'=>$service_type])->find();
//                            $item['cat_name'] = $cat['cat_name'];
//                            $item['describe'] = $cat['describe'];
//                            if($item['receipt_id'] == $this->request->user_id){
//                                $userinfo  = Db::name('user')->where(['id'=>$item['receipt_id']])->field('id,avatar,name, mobile')->find();
//                                $otherinfo = Db::name('user')->where(['id'=>$item['accept_id']])->field('id,avatar,name, mobile')->find();
//                            }else{
//                                $userinfo  = Db::name('user')->where(['id'=>$item['accept_id']])->field('id,avatar,name, mobile')->find();
//                                $otherinfo = Db::name('user')->where(['id'=>$item['receipt_id']])->field('id,avatar,name, mobile')->find();
//                            }
//
//                            $item['user'] = [
//                                'user_id'=>$userinfo['id'],
//                                'avatar'=>$userinfo['avatar'],
//                                'name'=>$userinfo['name'],
//                                'mobile'=>$userinfo['mobile'],
//                                'type' =>$item['receipt_id'] == $this->request->user_id? 1:0
//                            ];
//                            $item['other'] = [
//                                'user_id'=>$otherinfo['id'],
//                                'avatar'=>$otherinfo['avatar'],
//                                'name'=>$otherinfo['name'],
//                                'mobile'=>$otherinfo['mobile'],
//                                'type' =>$item['accept_id'] == $this->request->user_id? 1:0
//                            ];
//                            $item['add_time'] = date('Y-m-d',$item['add_time']);
//                            unset($item['receipt_id']);
//                            unset($item['accept_id']);
//                            return $item;
//                        });
        }elseif($type == 3) {
            $online = Db::name('info')
                ->where(['user_id' => 0])
                ->whereOr(['user_id' => $this->request->user_id])
                ->where('type', '=', 'order')
                ->order('id desc')
                ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
                ->each(function ($item) {
                    $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                    return $item;
                });
        }
        return $this->succ($online);
    }

    # type=1 系统公告   type=2 接单成功消息
    public function delMsg(){
        $type = input('type');
        $msgid = input('msgid');
        if(empty($type) || empty($msgid)){
            return $this->fail('缺少必要参数');
        }
        Db::startTrans();
        try{
            if($type == 1){
                Db::name('info')->delete($msgid);
            }elseif ($type == 2){
                Db::name('user_msg')->delete($msgid);
            }
            //提交事务
            Db::commit();
            return $this->succ('删除成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

    }

    // type=1 系统公告   type=2 接单成功消息
    public function updateMsgStatus(){
        $type = input('type');
        $msgid = input('msgid');
        if(empty($type) || empty($msgid)){
            return $this->fail('缺少必要参数');
        }
        if($type == 1){
            $online = Db::name('info')->where(['id'=>$msgid])->update(['status'=>1]);
        }else{
            $online = Db::name('user_msg')->where(['id'=>$msgid])->update(['msg_status'=>1]);
        }
        return $this->succ($online);
    }

    public function getInfo(){
        $type = input('type');
        if(empty($type)){
            return $this->fail('错误操作');
        }
        $info = config('info.info');
        $t = '';
        foreach ($info as $key=>$item){
            if($item['type'] == $type && $item['status'] == 1){
                $t = 'find';
                break;
            }else if($item['type'] == $type && $item['status'] == 0){
                $t = 'select';
                break;
            }
        }
        $online = Db::name('info')->order('id desc')->where(['type'=>$type])->$t();
        return $this->succ($online);
    }

    public function infoDetails(){
        $id = input('id');
        if(empty(intval($id))){
            return $this->fail('错误操作');
        }
        $online = Db::name('info')->where(['id'=>$id])->find();
        return $this->succ($online);
    }

    /**
     * 轮播图
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function banner(){
        if(empty(input('type'))){
            return $this->fail('错误操作');
        }
        if(input('type') == 'backimg'){
            $list = Db::name('config')->where('name','eq','backimg')->value('value');
            return $this->succ('http://'.$_SERVER['SERVER_NAME'].$list);
        }

        $list = Db::name('banner')->where(['type'=>input('type')])->field('image_path')->select();
        return $this->succ($list);
    }


    public function kefu(){
        $online = Db::name('kefu')->order('id desc')
            ->field('title,type,thumb')
            ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
            ->each(function($item){
                $item['name'] = $item['type'] == 4?'微信':'QQ';
                return $item;
            });
        return  $this->succ($online);
    }

    public function search(){
        $data = input('title');
        $car_id = Db::name('car')->where('name','like','%'.$data.'%')->field('id')->select();
        $ids= '';
        foreach ($car_id as $item){
            $ids .= $item['id'].',';
        }
        $online = Db::name('vehicle')
                ->where('brand','in',trim($ids,','))
                ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
                ->each(function($item){
                    $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                    return $item;
                });
        return $this->succ($online);
    }

    /**
     * 资讯列表
     */
    public function information(){
        $type = input('type');
        $page = input('page');
        if(!is_numeric($page) || !is_numeric($type) ){
            return $this->fail('错误操作',false);
        }
        if($type== 1){      //约邻之星
            //公告
            $online['notify'] = Db::name('info')->where(['type'=>'star'])->value('content');

            //按单数来论
            $online['today'] = Db::name('user')->where('id','in', implode(',',$this->getStart('day')))->field('id,name,avatar')->limit(10)->select();
            $online['month'] = Db::name('user')->where('id','in', implode(',',$this->getStart('month')))->field('id,name,avatar')->limit(10)->select();
            $online['year'] = Db::name('user')->where('id','in', implode(',',$this->getStart('year')))->field('id,name,avatar')->limit(10)->select();
        } else{
            $online= Db::name('information')->order('id desc')
                ->where(['type'=>$type])
                ->page($page,Env::get('app.page'))
                ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
                ->each(function($item){
                    $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                    return $item;
                });
        }
        return $this->succ($online);
    }

    /**
     * @param $type string today   month  year
     */
    public function getStart($type){
        //时间
        $start = 0;
        $end = 0;
        $t = '';
        switch ($type){
            case $type == 'day':
                $start = strtotime(date('Y-m-d  00:00:00',strtotime('-1 day')));
                $end   = strtotime(date('Y-m-d 23:59:59',strtotime('-1 day')));
                break;
            case $type == 'month':
                $start = strtotime(date('Y-m-01 00:00:00',strtotime('-1 month')));
                $end = strtotime(date("Y-m-29 23:59:59", strtotime('-1 month')));
                break;
            case $type == 'year':
                $start = strtotime(date('Y-01-01 00:00:00',strtotime('-1 year')));
                $end   = strtotime(date("Y-12-31 23:59:59", strtotime('-1 year')));
            case $type == 'all':
                $start = strtotime(date('2019-01-01 00:00:00',strtotime('-1 year')));
                $end   = strtotime(date("Y-m-d H:i:s", time()));
                break;
        }
        $evaluate_user = Db::name('evaluate')->group('uid')->field('uid')->where('add_time','between',[$start,$end])->select();
        $arr = [];
        $num = [];
        foreach ($evaluate_user as $u => $u_item){
            $arr[$u_item['uid']]['attitude'] = Db::name('evaluate')->field('attitude')->where(['uid'=>$u_item['uid']])->where('add_time','between',[$start,$end])->avg('attitude');
            $arr[$u_item['uid']]['skill'] = Db::name('evaluate')->field('skill')->where(['uid'=>$u_item['uid']])->where('add_time','between',[$start,$end])->avg('skill');
            $arr[$u_item['uid']]['work'] = Db::name('evaluate')->field('work')->where(['uid'=>$u_item['uid']])->where('add_time','between',[$start,$end])->avg('work');
            $num[$u_item['uid']] = $arr[$u_item['uid']]['attitude'] + $arr[$u_item['uid']]['skill'] + $arr[$u_item['uid']]['work'];
        }
        arsort($num);
        return array_keys($num);
    }

    public function applyActivity(){
        //活动报名
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'infoid' => 'require|number',
                'name' => 'require',
                'phone' => 'require',
            ];
            $msg = [
                'infoid.require' => '错误操作',
                'infoid.number' => '错误操作',
                'name.require' => '请输入姓名',
                'phone.require' => '请输入手机号码',
            ];
            $data = [
                'infoid' => $data['infoid'],
                'name' => $data['name'],
                'phone' => $data['phone'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $info = Db::name('information')->where(['id'=>$data['infoid']])->find();
            if(!$info){
                return $this->fail('活动不存在',false);
            }
            $info = [
                'info_id'=>$data['infoid'],
                'name'=>$data['name'],
                'phone'=>$data['phone'],
                'user_id'=>$this->request->user_id,
                'add_time'=>time(),
            ];
            try{
                Db::startTrans();

                $fin = Db::name('information_activity')
                    ->where([
                        'user_id'=>$this->request->user_id,
                        'info_id'=>$info['info_id'],
                    ])->find();
                if($fin){
                    return $this->fail('同一个活动每个用户只能报名一次',false);
                }
                Db::name('information_activity')->where([
                    'user_id'=>$this->request->user_id,
                    'info_id'=>$info['info_id'],
                ])->delete();

                $getid = Db::name('information_activity')->insertGetId($info);
                if(!$getid){
                    return $this->fail('网络错误.请稍后再试',false);
                }

                // 提交事务
                Db::commit();
                return $this->succ('已申请报名');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }
    }

    public function informationDetals(){
        $id= input('id');
        if(!is_numeric($id)){
            return $this->fail('错误操作',false);
        }
        $online= Db::name('information')->where(['id'=>$id])->value('content');
        if(empty($online)){
            return $this->fail('没有数据了',false);
        }
        return $this->succ($online);
    }

    public function getAllOrder(){
        $type = input('type');
        $longitude = input('longitude');
        $latitude = input('latitude');
        $skill = input('skill');
        if(!is_numeric($longitude) || !is_numeric($latitude)){
            return $this->fail('经纬度错误',false);
        }
        if(empty($type) || empty($longitude) || empty($latitude)){
            return $this->fail('没有数据',false);
        }
        //热闹推荐
        if(intval($type) == 1){
            $page = input('page');
            if(empty($page)){
                return $this->fail('没有更多数据',false);
            }
//            $db = Db::name('receipt');
            $db = Db::name('user');
            $sort = '';
            $selz = input('selz');
            if(!empty($selz) && isset($selz)){
                if($selz==1){
                    //用户评论排名
                    $user_evaluate = $this->getStart('all');
                    $str = implode(',',$user_evaluate);
                    $db->where('a.id','in',$str);
                    $sort = 'star';
                }elseif($selz==2){
                    //最新发布
                    $sort = 'add_time';
                }

            }
            $selt = input('selt');
                if(!empty($selt) && isset($selt)){
                    if($selt==1){
                         $sort = 'distance';
                    }elseif($selt==2){
                        $sort = 'skill';
                    }elseif($selt==3){
                        $sort = 'voie';
                    }
                }
            $self = input('self');
                if(!empty($self) && isset($self)){
                    if($self==1){   //线下服务
                        $sort = 'unline_service';
                    }elseif($self==2){   //电话服务
                        $sort = 'phone_service';
                    }elseif($self==3){   //线上服务
                        $sort = 'longrange_service';
                    }
                }
            if(!empty($skill)){
                $db->where(['b.type'=>$skill]);
            }
            $join = [
                ['tb_user_skill b','b.user_id=a.id'],
            ];
            $online = $db
                ->join($join)
                ->alias('a')
                ->group('b.user_id')
                ->where(['b.status'=>1])
                ->where('a.id','neq',$this->request->user_id)
//                ->where('a.avatar','neq','')
                ->field('a.id,a.avatar,a.name,a.lat,a.lng,b.status,b.user_id, b.type,b.introduce,b.add_time,b.img,b.unline_service,b.longrange_service,b.unline_unit,b.longrange_unit')
//                ->page($page,Env::get('app.page'))
                ->page($page,10)
                ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
                ->each(function ($item){
                    $item['star'] = $this->getUserStar($item['id']);
                    $item['skill'] = Db::name('user_skill')->where(['user_id'=>$item['id']])->where(['status'=>1])->count();
                    $item['voie'] = Db::name('user_skill')->where(['user_id'=>$item['id']])->where(['status'=>1])->where('voice','neq','')->count();
                   // $item['unline_service'] = Db::name('user_skill')->where(['user_id'=>$item['id']])->where(['status'=>1])->where('unline_service','neq',0)->count();
                    $item['phone_service'] = Db::name('user_skill')->where(['user_id'=>$item['id']])->where(['status'=>1])->where('phone_service','neq',0)->count();
                   // $item['longrange_service'] = Db::name('user_skill')->where(['user_id'=>$item['id']])->where(['status'=>1])->where('longrange_service','neq',0)->count();
                    $item['add_time'] = date('Y-m-d',$item['add_time']);
                    $item['cat_name'] = Db::name('product_category')->where(['cat_id'=>$item['type']])->value('cat_name');
                    $item['img'] = json_decode($item['img']);
                    return $item;

                });
            $arr = $online->toArray();
            $list = [];
            foreach ($arr['data'] as $key => $item){
                $Distance = new Distance();
                $rice   = $Distance->getdistance($item['lng'],$item['lat'],$longitude,$latitude);
                $item['distance'] = intval($rice);
                if(floatval($item['distance']) >= 1000){
                    $arr['data'][$key]['distance'] =  floor($item['distance']);
                    $arr['data'][$key]['last_distance'] =  floor(($item['distance'] /1000)*100)/100 . '公里';
                }else{
                    $arr['data'][$key]['distance'] = intval($item['distance']);
                    $arr['data'][$key]['last_distance'] = intval($item['distance']) .'米';
                }

                $list[$key] = $arr['data'][$key];
            }

            if(!empty($sort)){
                if($sort == 'distance'){
                    $last_names = array_column($list,$sort);
                    array_multisort($last_names,SORT_ASC,$list);
                }else{
                    $last_names = array_column($list,$sort);
                    array_multisort($last_names,SORT_DESC,$list);
                }
            }

            $arr['data'] = array_values ($list);
            return $this->succ($arr);
        }
        //我的关注用户
        if(intval($type) == 2){
            $page = input('page');
            if(empty($page)){
                return $this->fail('没有更多数据',false);
            }
            $str = Db::name('user_follow')->where(['user_id'=>$this->request->user_id])->value('str');
            if(empty($str)){
                return $this->fail('你还没有关注的人,快去关注吧', false);
            }

            $online = Db::name('user')
                        ->where('id','in',$str)
                        ->field('name,avatar,age,id,lng, lat')
                        ->page($page,Env::get('app.page'))
                        ->paginate(Env::get('app.page'), false, ['query' => request()->param()]);
            $arr = $online->toArray();
            $list = [];
            foreach ($arr['data'] as $key => $item){
                $Distance = new Distance();
                $rice   = $Distance->getdistance($item['lng'],$item['lat'],$longitude,$latitude);
                $item['distance'] = intval($rice);
                if(floatval($item['distance']) >= 1000){
                    $arr['data'][$key]['distance'] =  floor($item['distance']);
                    $arr['data'][$key]['last_distance'] =  floor(($item['distance'] /1000)*100)/100 . '公里';
                }else{
                    $arr['data'][$key]['distance'] = $item['distance'];
                    $arr['data'][$key]['last_distance'] = intval($item['distance']) .'米';
                }
                $list[$key] = $arr['data'][$key];
            }
            ksort($list);
            $arr['data'] = array_values ($list);
            return $this->succ($arr);
        }
    }

    /**
     * 获取用户总星数
     * @param $user_id
     * @return mixed
     */
    public function getUserStar($user_id){
        $num = 0;
        $num += round(Db::name('evaluate')->where(['uid'=>$user_id])->avg('attitude'));
        $num += round(Db::name('evaluate')->where(['uid'=>$user_id])->avg('skill'));
        $num += round(Db::name('evaluate')->where(['uid'=>$user_id])->avg('work'));
        return $num;
    }

    public function flowDel(){
        $follow_id  = input('id');
        if(empty($follow_id) || intval($follow_id) == 0){
            return $this->fail('没有数据',false);
        }
        $str = Db::name('user_follow')->where(['user_id'=>$this->request->user_id])->value('str');
        $seach = strstr($str, $follow_id);
        if($seach === false){
            return $this->fail('没有关注的用户',false);
        }else{
            $t1= ','.$follow_id;
            $t2= $follow_id. ',';
            $seach1 = strstr($str, $t1);
            $seach2 = strstr($str, $t2);
            if($seach1 !== false){
                $res=str_replace($t1,'',$str);
            }else{
                if($seach2 !== false){
                    $res=str_replace($t2,'',$str);
                }else{
                    if(!empty($str)){
                        Db::name('user_follow')->where(['user_id'=>$this->request->user_id])->delete();
                        return $this->succ('取消成功');
                    }

                }
            }
            $r = Db::name('user_follow')->where(['user_id'=>$this->request->user_id])->update(['str'=>$res]);
            if($r){
                return $this->succ('取消成功');
            }else{
                return $this->fail('服务器繁忙',false);
            }
        }

    }


    /**
     * 直接约
     */
    public function invoice()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $voice = isset($data['voice'])?$data['voice']:'';
            $img = isset($data['img'])?$data['img']:'';
            $introduce = isset($data['introduce'])?$data['introduce']:'';
            $rule = [
                'skill_id' => 'require|number',
                'address' => 'require|number',
              //  'apm' => 'require|number',
//                'introduce' => 'require',
                'start_time' => 'require',
                'end_time' => 'require',
//                'lng' => 'require',
//                'lat' => 'require',
                'accept_userid' => 'require',
                'last_service_type' => 'require',
                'last_service_price' => 'require',
                'last_service_unit' => 'require',
                'service_time' => 'require',
                'username' => 'require',
                'mobile' => 'require',
            ];
            $msg = [
                'address.require' => '请选择地址',
                'address.number' => '错误操作',
//                'apm.require' => '请选择时间区域',
//                'apm.number' => '时间区域参数错误',
                'skill_id.require' => '缺少技能参数',
                'skill_id.number' => '错误操作',
//                'introduce.require' => '请填写留言',
                'start_time.require' => '请选择开始时间',
                'end_time.require' => '请选择结束时间',
//                'lng.require' => '缺少参数',
//                'lat.require' => '缺少参数',
                'accept_userid.require' => '缺少接单用户ID',
                'last_service_type.require' => '请填写服务类型',
                'last_service_price.require' => '请填写服务价格',
                'last_service_unit.require' => '请填写服务类型',
                'service_time.require' => '请填写服务时长',
                'username.require' => '请填写姓名',
                'mobile.require' => '请填写手机号',
            ];
            $data = [
                'address' => $data['address'],
//                'introduce' => $data['introduce'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
//                'lng' => $data['lng'],
//                'lat' => $data['lat'],
                'accept_userid' => $data['accept_userid'],
                'skill_id' => $data['skill_id'],
                'last_service_type' => $data['last_service_type'],
                'last_service_price' => $data['last_service_price'],
                'last_service_unit' => $data['last_service_unit'],
                'service_time' => $data['service_time'],
                'username' => $data['username'],
                'mobile' => $data['mobile'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $data['lng'] = 0;
            $data['lat'] = 0;
//            $this->userIdent();
//            $this->userIsLevel();
//            $this->userIdent($data['accept_userid']);
//            $this->userIsLevel($data['accept_userid']);

            if(!empty($voice)){
                $info['voice'] = $voice;
            }
            if(!empty($introduce)){
                $info['introduce'] = $introduce;
            }
            if(!empty($img)){
                $info['img'] = json_encode($img);
            }
            $address = Db::name('user_address')->where(['user_id'=>$this->request->user_id,'id'=>$data['address']])->find();
            if(empty($address)){
                return $this->fail('地址错误', false);
            }

            if($data['last_service_type'] == 1){
                $info['unline_service'] = $data['last_service_price'];
                $info['unline_unit'] = $data['last_service_unit'];
            }elseif($data['last_service_type'] == 2){
                $info['phone_service'] = $data['last_service_price'];
                $info['phone_unit'] = $data['last_service_unit'];
            }elseif($data['last_service_type'] == 3){
                $info['longrange_service'] = $data['last_service_price'];
                $info['longrange_unit'] = $data['last_service_unit'];
            }

            if(empty($info['longrange_service']) && empty($info['unline_service']) && empty($info['phone_service'])){
                return $this->fail('至少选择一项收费标准', false);
            }
            $str = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

            $info['mobile'] = $data['mobile'];
            $info['username'] = $data['username'];
            $info['address'] = $data['address'];
            $info['longitude']    = $data['lng'];
            $info['latitude']     = $data['lat'];
            $info['start_time']   = strtotime($data['start_time']);
            $info['end_time']     = strtotime($data['end_time']);
            $info['add_time']     =  time();
            $info['user_id']      =  $this->request->user_id;
            $info['order_on']     =  $str;
            $info['service_time'] =  $data['service_time'];
            $info['last_service_type'] =  $data['last_service_type'];
            $info['last_service_price'] =  $data['last_service_price'];
            $info['last_service_unit'] =  $data['last_service_unit'];

            try{
                Db::startTrans();
                //2019/7/23
//                $st = Db::name('receipt')->where(['user_id'=> $info['user_id']])->where('status','in','1,2,7,8')->find();
//                if($st){
//                    return $this->fail('请先完成未完成的订单',false);
//                }
                Db::name('receipt')->where(['user_id'=> $info['user_id']])->where('status','eq','0')->delete();
                $in['status']  = ['in','1,2'];
                $online = Db::name('receipt')
                    ->where($in)
                    ->where(['user_id'=> $info['user_id']])->find();
                if($online){
                    return $this->fail('已存在同一时间段订单,请先完成未完成的订单',false);
                }

                //直接约接单用户
                $acceipt_user_kill = Db::name('user_skill')->where(['type'=>$data['skill_id'],'status'=>1])->where('user_id','eq',$data['accept_userid'])->find();
                if(empty($acceipt_user_kill)){
                    return $this->fail('没有查询到该技能!', false);
                }
                //2019/7/23
//                $acceipt_user_order = Db::name('receipt_aceept')->where(['user_id'=> $data['accept_userid']])->where('status','in','1,2,7,8,9')->find();
//                if($acceipt_user_order){
//                    return $this->fail('该用户有未完成的单，请稍后再约', false);
//                }
                //2019/7/23
//                $moneystatus = Db::name('receipt_aceept')->where(['user_id'=> $data['accept_userid']])->where('money','lt',0)->where('status','eq',6)->find();
//                if($moneystatus){
//                    return $this->fail('该用户有欠款在身，不能接单', false);
//                }
                $info['service_type']= $acceipt_user_kill['type'];
                $info['receipt_type']= 1;
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
     * 自动约他
     */
    public function authAppointment(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'accept_userid' => 'require|number',
                'receipt_id' => 'require|number',
            ];
            $msg = [
                'accept_userid.require' => '错误操作',
                'accept_userid.number'  => '错误操作',
                'receipt_id.require' => '错误操作',
                'receipt_id.number'  => '错误操作',

            ];
            $data = [
                'accept_userid'  => $data['accept_userid'],
                'receipt_id'       => $data['receipt_id'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $receipt = Db::name('receipt')->where(['id'=>$data['receipt_id']])->find();

//            //用户余额支付
//            if(floatval($this->userinfo()['money']) < floatval($receipt['last_service_price'])){
//                return $this->fail('余额不足请充值，需支付金额'.floatval($receipt['last_service_price']),false,2);
//            }
//
//            $ct = Db::name('receipt_aceept')->where(['receipt_id'=>$receipt['id']])->count();
//            $receipt_num = get_config('base','receipt_num');
//            if(intval($ct) >= intval($receipt_num)){
//                return $this->fail('当前订单接单人数已满!', false,3);
//            }

            try{
                Db::startTrans();

            $info = [
                'user_id'=>$data['accept_userid'],
                'service_type'=>$receipt['service_type'],
                'add_time'=>time(),
                'status'=>1,
                'receipt_id'=>$data['receipt_id'],
                'type'=>1,
                'longitude'=>$this->getUserInfo($data['accept_userid'],'lat'),
                'latitude'=>$this->getUserInfo($data['accept_userid'],'lng'),
                'phone'=>$this->getUserInfo($data['accept_userid'],'mobile'),
            ];
            $insertid_accept = Db::name('receipt_aceept')->insertGetId($info);
            #扣除佣金,改变状态
            Db::name('receipt')->where(['id'=>$receipt['id']])->update(
                [
                    'storage_money'=>floatval($receipt['last_service_price']),
                    'accept_id'=>$insertid_accept,
                    'service_time'=>$receipt['service_time'],
                ]);
            //扣除发单用户金额


            $service_type = Db::name('product_category')->where(['cat_id'=>$receipt['service_type']])->value('cat_name');
            //向接单用户发送组成信息
                $msg = [
                    'type'=>'order',
                    'content'=>'您已经被直接约单啦,服务类型:'.$service_type.'请及时处理订单,超时未接受将受到相应的处罚',
                    'admin'=>'admin',
                    'user_id'=>$data['accept_userid'],
                    'title'=>'您已经被直接约单啦,'.$service_type.'订单,点击查看详情',
                    'add_time'=>time(),
                    'status'=>0,
                    'info'=>$data['receipt_id'],
                    'void_type'=>1
                ];
                Db::name('info')->insert($msg);
                $cid = $this->getUserInfo($data['accept_userid'],'cid');
                $push = new Push();
                $push->getui($cid,'您已经被直接约单啦,服务类型:'.$service_type.'请及时处理订单,超时未接受将受到相应的处罚');
                (new Msg())->sendInfo($info['phone'],$service_type);
                //向接单人发送短信
                // 提交事务
                Db::commit();
                return $this->succ('操作成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        }

    }


    public function getAcceptInfo(){
        $data = $this->request->param();

        $userid = $data['userid'];
        if(empty($userid) || !is_numeric($userid)){
            return $this->fail('用户错误', false,3);
        }
       $type = $data['type'];
        if(empty($type) || !is_numeric($type)){
            return $this->fail('类型错误', false,3);
        }
        $online['avatar'] = $this->getUserInfo($userid, 'avatar');
        $online['name'] = $this->getUserInfo($userid, 'name');
        $online['type'] = Db::name('user_skill')->where(['type'=>$type,'user_id'=>$userid])->find();


        print_r($online);exit;

    }



    /**
     * 约他
     */
    public function userAppointment(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'accept_userid' => 'require|number',
                'skill_id' => 'require|number',
//                'longitude' => 'require',
//                'latitude' => 'require',
                'trade' => 'require',
            ];
            $msg = [
                'accept_userid.require' => '错误操作',
                'accept_userid.number'  => '错误操作',
                'skill_id.require' => '错误操作',
                'skill_id.number'  => '错误操作',
//                'latitude.require' => '错误操作',
//                'longitude.require' => '错误操作',
                'trade.require' => '错误操作',
            ];
            $data = [
                'accept_userid'  => $data['accept_userid'],
                'skill_id'       => $data['skill_id'],
//                'latitude'       => $data['latitude'],
//                'longitude'      => $data['longitude'],
                'trade'          => $data['trade'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
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

            $this->userIdent();
            $this->userIsLevel();
            $this->userIdent($data['accept_userid']);
            $this->userIsLevel($data['accept_userid']);

            $acceipt_user_kill = Db::name('user_skill')->where(['type'=>$data['skill_id'],'status'=>1])->where('user_id','eq',$data['accept_userid'])->find();
            if(empty($acceipt_user_kill)){
                return $this->fail('没有查询到该技能!', false);
            }
            if(floatval($acceipt_user_kill['unline_service']) <= 0){
                return $this->fail('直接约单只支持线下类型,该用户暂未设置该项收费,其他类型暂未开放!', false,3);
            }
            //2019/7/23
//            $st = Db::name('receipt')->where(['user_id'=> $this->request->user_id])->where('status','in','1,2,7,8,9')->find();
//            $acceipt_user_order = Db::name('receipt_aceept')->where(['user_id'=> $data['accept_userid']])->where('status','in','1,2,7,8,9')->find();
//            if($acceipt_user_order){
//                return $this->fail('该用户有未完成的单，请稍后再约', false);
//            }
//            $moneystatus = Db::name('receipt_aceept')->where(['user_id'=> $data['accept_userid']])->where('money','lt',0)->where('status','eq',6)->find();
//            if($moneystatus){
//                return $this->fail('该用户有欠款在身，不能接单', false);
//            }
            if($st){
                if($st['status'] == 1){
                    //如果类型相同.
                    if($acceipt_user_kill['type'] == $st['service_type']){
                        $fd = Db::name('receipt_aceept')->where(['receipt_id'=>$st['id'],'user_id'=>$data['accept_userid']])->find();
                        if($fd){
                            return $this->fail('该用户已存在订单中!', false,3);
                        }else{

                            //用户余额支付
                            if(floatval($this->userinfo()['money']) < floatval($acceipt_user_kill['unline_service'])){
                                return $this->fail('余额不足请充值，需支付金额'.floatval($acceipt_user_kill['unline_service']),false,2);
                            }

                            $ct = Db::name('receipt_aceept')->where(['receipt_id'=>$st['id']])->count();
                            $receipt_num = get_config('base','receipt_num');
                            if(intval($ct) >= intval($receipt_num)){
                                return $this->fail('当前订单接单人数已满!', false,3);
                            }



                            //如果不存在订单，直接加用户接受单
                            $info = [
                                'user_id'=>$data['accept_userid'],
                                'service_type'=>$acceipt_user_kill['type'],
                                'add_time'=>time(),
                                'status'=>7,
                                'receipt_id'=>$st['id'],
                                'type'=>1,
                                'phone'=>$this->getUserInfo($data['accept_userid'],'mobile'),
                                'longitude'=>$this->getUserInfo($data['accept_userid'],'lat'),
                                'latitude'=>$this->getUserInfo($data['accept_userid'],'lng'),
                            ];

                            $insertid_accept = Db::name('receipt_aceept')->insertGetId($info);
                            Db::name('receipt_aceept')->where(['receipt_id'=>$st['id']])->where('id','neq',$insertid_accept)->update(['status'=>6]);
                            $res = [
                                'accept_id'=>$insertid_accept,
                                'receipt_id'=>$st['id'],
                            ];
                            #扣除佣金,改变状态
                            Db::name('receipt')->where(['id'=>$st['id']])->update(
                                [
                                    'status'=>7,  //待接单
                                    'pay_money'=>floatval($acceipt_user_kill['unline_service']),
                                    'storage_money'=>floatval($acceipt_user_kill['unline_service']),
                                    'accept_id'=>$insertid_accept,
                                    'last_service_type'=>1,
                                    'last_service_price'=>$acceipt_user_kill['unline_service'],
                                    'last_service_unit'=>$acceipt_user_kill['unline_unit'],
                                    'service_time'=>1,
                                ]);
                            //扣除发单用户金额
                            Db::name('user')->where(['id'=>$this->request->user_id])->setDec('money',floatval($acceipt_user_kill['unline_service']));
                            $this->add_log($this->request->user_id,1,11,floatval($acceipt_user_kill['unline_service']),0);
                            $service_type = Db::name('product_category')->where(['cat_id'=>$acceipt_user_kill['type']])->value('cat_name');
                            //向接单用户发送组成信息
                            $msg = [
                                'type'=>'order',
                                'content'=>'您已经被直接约单啦,服务类型:'.$service_type.'请及时处理订单,超时未接受将受到相应的处罚',
                                'admin'=>'admin',
                                'user_id'=>$acceipt_user_kill['user_id'],
                                'title'=>'您已经被直接约单啦,'.$service_type.'订单,点击查看详情',
                                'add_time'=>time(),
                                'status'=>0,
                                'info'=>$st['id'],
                                'void_type'=>1
                            ];
                            Db::name('info')->insert($msg);
                            $cid = $this->getUserInfo($acceipt_user_kill['user_id'],'cid');
                            $push = new Push();
                            $push->getui($cid,'您已经被直接约单啦,服务类型:'.$service_type.'请及时处理订单,超时未接受将受到相应的处罚');

                            //向接单人发送短信
                            (new Msg())->sendInfo($info['phone'],$service_type);
                            return $this->succ($res);
                        }
                    }else{
                        //如果类型不相同
                        return $this->fail('请先完成未完成的订单',false);
                    }
                }else{
                    //如果类型不相同
                    return $this->fail('请先完成未完成的订单',false);
                }
            }

            try{
                Db::startTrans();
                $address = Db::name('user_address')->where(['user_id'=>$this->request->user_id,'is_default'=>1])->order('id desc')->find();
                if(empty($address)){
                    return $this->fail('请先设置默认地址', false);
                }

                $money = get_config('user','cyj');
                //用户余额支付
                if(floatval($this->userinfo()['money']) < floatval($money) + floatval($acceipt_user_kill['unline_service'])){
                    $last_money  =floatval($acceipt_user_kill['unline_service'])+ floatval($money);
                    return $this->fail('余额不足请充值，诚意金￥'.$money.'+支付金额'.floatval($acceipt_user_kill['unline_service']).',共￥'.$last_money,false,2);
                }

                //生成发单
                $receipt_info['voice'] = '';
                $receipt_info['img'] = '';
                $receipt_info['unline_service'] = $acceipt_user_kill['unline_service'];
                $receipt_info['unline_unit'] = $acceipt_user_kill['unline_unit'];
                $receipt_info['phone_service'] = $acceipt_user_kill['phone_service'];
                $receipt_info['phone_unit'] = $acceipt_user_kill['phone_unit'];
                $receipt_info['longrange_service'] = $acceipt_user_kill['longrange_service'];
                $receipt_info['longrange_unit'] = $acceipt_user_kill['longrange_unit'];
                $receipt_info['address'] = $address['id'];
                $receipt_info['receipt_type'] = 1;
                $service_type = Db::name('product_category')->where(['cat_id'=>$acceipt_user_kill['type']])->value('cat_name');
                $receipt_info['introduce'] = '预约类型：'.$service_type.',来自直接约';
                $receipt_info['service_type'] = $acceipt_user_kill['type'];
//                $receipt_info['longitude']  = $data['longitude'];
//                $receipt_info['latitude']   = $data['latitude'];
                $receipt_info['start_time'] = time();
                $receipt_info['end_time']   = time()+24*60*60;
                $receipt_info['add_time']   =  time();
                $receipt_info['status']   =  1;
                $receipt_info['earnestmoney']   =  $money;
                $receipt_info['user_id']    =  $this->request->user_id;
                $receipt_info['order_on']   = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);


                $insertid = Db::name('receipt')->insertGetId($receipt_info);

                $info = [
                  'user_id'=>$data['accept_userid'],
                  'service_type'=>$acceipt_user_kill['type'],
                  'add_time'=>time(),
                  'status'=>7,
                  'receipt_id'=>$insertid,
                  'type'=>1,
                  'longitude'=>$this->getUserInfo($data['accept_userid'],'lat'),
                  'latitude'=>$this->getUserInfo($data['accept_userid'],'lng'),
                  'phone'=>$this->getUserInfo($data['accept_userid'],'mobile'),
                ];

                //改变未约的用户状态
                $insertid_accept = Db::name('receipt_aceept')->insertGetId($info);
                $res = [
                  'accept_id'=>$insertid_accept,
                  'receipt_id'=>$insertid,
                ];

                #扣除佣金,改变状态
                Db::name('receipt')->where(['id'=>$insertid])->update(
                    [
                        'status'=>7,  //待接单
                        'pay_money'=>floatval($acceipt_user_kill['unline_service']),
                        'storage_money'=>floatval($acceipt_user_kill['unline_service']),
                        'accept_id'=>$insertid_accept,
                        'last_service_type'=>1,
                        'last_service_price'=>$acceipt_user_kill['unline_service'],
                        'last_service_unit'=>$acceipt_user_kill['unline_unit'],
                        'service_time'=>1,
                    ]);
                Db::name('user')->where(['id'=>$this->request->user_id])->setDec('money',floatval($money));
                $this->add_log($this->request->user_id,1,7,floatval($money),0);
                //扣除发单用户金额
                Db::name('user')->where(['id'=>$this->request->user_id])->setDec('money',floatval($acceipt_user_kill['unline_service']));
                $this->add_log($this->request->user_id,1,11,floatval($acceipt_user_kill['unline_service']),0);

                //向接单用户发送组成信息
                $service_type = Db::name('product_category')->where(['cat_id'=>$acceipt_user_kill['type']])->value('cat_name');
                $msg = [
                    'type'=>'order',
                    'content'=>'您已经被直接约单啦,服务类型:'.$service_type.'请及时处理订单,超时未接受将受到相应的处罚',
                    'admin'=>'admin',
                    'user_id'=>$acceipt_user_kill['user_id'],
                    'title'=>'您已经被直接约单啦,'.$service_type.'订单,点击查看详情',
                    'add_time'=>time(),
                    'status'=>0,
                    'info'=>$insertid,
                    'void_type'=>1
                ];
                Db::name('info')->insert($msg);
                $cid = $this->getUserInfo($acceipt_user_kill['user_id'],'cid');
                $push = new Push();
                $push->getui($cid,'您已经被直接约单啦,服务类型:'.$service_type.'请及时处理订单,超时未接受将受到相应的处罚');
                $m ='您已经被直接约单啦,服务类型:'.$service_type.'请及时处理订单,超时未接受将受到相应的处罚';
                $this->sendInfo($m,$acceipt_user_kill['user_id'],$m,$insertid);
                $service_type = Db::name('product_category')->where(['cat_id'=>$acceipt_user_kill['type']])->value('cat_name');
                //向接单人发送短信
                (new Msg())->sendInfo($info['phone'],$service_type);
                // 提交事务
                Db::commit();
                return $this->succ($res);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }
    }
    public function testgetui(){
        $push = new Push();
        $push->getui("ec730390c2df4b184196655b3859a5be",'桢哥在测试你懂不');
    }
    public function editions(){
        $config = get_config('base');
        $data['version']=$config['version'];
        $data['url']="www.yuelinapp.com";
        return $this->succ($data);
    }

}