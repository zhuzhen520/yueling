<?php
/**
 * Created by PhpStorm.
 * Date: 2019/2/28
 * Time: 14:44
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

namespace app\admin\controller;


use app\api\controller\Distance;
use app\api\controller\Push;
use app\helps\Oss;
use think\Db;
use think\facade\Env;

class Order extends Base
{
    /**
     * 发单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function receipt_list(){
        $data = input('param.');
        $list = Db::name('receipt');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $time_max = strtotime($data['end']);
            $time_min = strtotime($data['start']);
            $data['start'] = $time_min;
            $data['end'] =  $time_max;
            $list->where('add_time', '>=', $data['start']);
            $list->where('add_time', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['user_id|order_on' => ['eq', $data['word']]]);
        }
        if (isset($data['type']) && $data['type']) {
            $list->where(['status' => ['eq', $data['type']]]);
        }
        $lists = $list
                 ->order('id desc')
                 ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
                 ->each(function($item){
//                     $item['voice'] = $this->curlinit($item['voice']);
                     $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                     $item['start_time'] = date('Y-m-d H:i:s',$item['start_time']);
                     $item['end_time'] = date('Y-m-d H:i:s',$item['end_time']);
                     $item['service_type'] = Db::name('product_category')->where(['cat_id'=>$item['service_type']])->value('cat_name');
                     $item['s_type'] = $item['last_service_type'];
                     if($item['last_service_type'] == 1){
                         $item['last_service_type'] = '线下服务';
                     }elseif($item['last_service_type'] == 2){
                         $item['last_service_type'] = '电话服务';
                     }elseif($item['last_service_type'] == 3){
                         $item['last_service_type'] = '线上服务';
                     }
                     $item['status'] = $item['status'] == 7? 8:$item['status'];
                     $accept =  Db::name('receipt_aceept')->where(['id'=>$item['accept_id']])->find();
                     $distance = new Distance();
                     $rice = $distance->getdistance($item['longitude'],$item['latitude'],$accept['longitude'],$accept['latitude']);
                     $item['distance'] = intval($rice);
                     if(floatval($item['distance']) >= 1000){
                         $item['jwd'] =  floor(($item['distance'] /1000)*100)/100 . '公里';
                     }else{
                         $item['jwd'] = intval($item['distance']) .'米';
                     }


                     $item['accept_status'] = $accept['status'];
                     $item['accept_id'] = $accept['user_id'];
                     $item['accept_money'] = $accept['money'];
                     $item['img'] = empty($item['img'])?[]:json_decode($item['img']);
                     $address = Db::name('user_address')->where(['id'=>$item['address']])->find();
                     $item['address'] = $address['province'].$address['city'].$address['area'].$address['address'];
                     return $item;
                 });
        $this->assign('info',config('info.receipt'));
        $this->assign('list',$lists);
        $this->assign('page',$lists->render());
        $this->assign('count',$lists->count());
        return $this->fetch();
    }

    public function dealOrder(){
        $receipt_id = $this->request->post('id');
        $accept_id = Db::name('receipt')->where(['id'=>$receipt_id])->value('accept_id');
        try{
            Db::startTrans();
            //冻结双方订单
            Db::name('receipt')
                ->where(['id'=>$receipt_id])
                ->update([
                    'status'=>4,
                ]);
            Db::name('receipt_aceept')
                ->where(['id'=>$accept_id])
                ->update([
                    'status'=>4,
                ]);
            // 提交事务
            Db::commit();
            return json(['status'=>'y','info'=>'已处理']);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }




//    public function curlinit($url){
//        $rand = '/void/'.rand(1000000,9999999) . '.mp3';
//        exec("ffmpeg -i ".$url." ".$rand."");
//        $file = $this->request->file($rand);
//        dump($file);exit;
//        $info = $file->getInfo();
//        $datas = [
//            'name'=>$info['name'],
//            'tmp_name'=>$info['tmp_name']
//        ];
//        $oss = new Oss();
//        $upload_info = $oss->uploadPostFile($datas);
//        $inst['img'] = $upload_info;
//        return $rand;
//    }



}