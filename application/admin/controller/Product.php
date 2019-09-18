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


use app\helps\Oss;
use Slim\Http\Request;
use think\Db;
use think\facade\Env;

class Product extends Base
{
    public function product_list(){
        $data = input('param.');
        $list = Db::name('product');
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $time_max = strtotime($data['end']);
            $time_min = strtotime($data['start']);
            $data['start'] = $time_min;
            $data['end'] =  $time_max;
            $list->where('add_time', '>=', $data['start']);
            $list->where('add_time', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['name' => ['eq', $data['word']]]);
        }
        if (isset($data['type']) && $data['type']) {
            $list->where(['cat_id' => ['eq', $data['type']]]);
        }else{
            $data['type'] = 0;
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

    /* 产品添加 */
    public function product_add(){

        $db = Db::name('product');
        $cat = Db::name('Product_category');
        if($this->request->isPost()){
            $POST = input('param.');
            unset($POST['admin']);
            $POST['is_recommend'] = input('is_recommend',0);
            if(isset($POST['promote_start']) && !empty($POST['promote_start']) || isset($POST['promote_end']) && !empty($POST['promote_end'])){
                if($POST['promote_price'] <= 0){
                    return json(['status'=>'n','info'=>'必须设置促销价格']);
                }
                $POST['promote_start'] = strtotime($POST['promote_start']);
                $POST['promote_end'] = strtotime($POST['promote_end']);

            }
            if(empty($POST['img'])){
                unset($POST['img']);
            }

            //调用阿里云上传
            if($this->request->file()){
                $file = $this->request->file('img');
                $info = $file->getInfo();
                $data = [
                    'name'=>$info['name'],
                    'tmp_name'=>$info['tmp_name']
                ];
                $oss = new Oss();
                $upload_info = $oss->uploadPostFile($data);
                $POST['thumb'] = $upload_info;
            }
            $POST['pay_type'] = 3;   //积分支付  ------------------------------------------------------支付方式


            #操作方式
            if($POST['id'] > 0){
                #修改
                $b = $db->update($POST);
            }else{

                $POST['add_time'] = time();
                #添加
                $b = $db->insert($POST);
            }
            #结果
            if($b !== false){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'操作失败!']);
            }
        }else{
            $id = input('id',0);
            #修改
            if($id){
                $row = $db->find($id);
            }else{
                $row = [
                    'thumb' => '/uploads/img/uploads.jpg',
                    'name' => '',
                    'short_name' => '',
                    'cat_id' => '',
                    'shop_price' => '',
                    'stock' => 1,
                    'is_sale' => 1,
                    'promote_price' => 0,
                    'promote_start' => 0,
                    'promote_end' => 0,
                    'content' => '',
                    'pay_type' => 2,
                    'freight' => 0,
                    'id' => $id,
                ];
            }
            $cat_row = Db::name('Product_category')->where(['parent_id'=>0])->select();
        }
        $this->assign('cat',$cat_row);
        $this->assign($row);
        #渲染模板
        return $this->fetch();
    }

    public function catlist(){
        $cat = $this->request->param('cat_id');
        $cat_row = Db::name('Product_category')->where(['parent_id'=>$cat])->select();
        return json(['status'=>'y','info'=>$cat_row]);
    }

    public function product_del(){
        $id = explode(',',trim(input('id'),','));
        $db = Db::name('product');
        //查询缩略图和内容
        $b = false;
        $count = 0;
        Db::startTrans();
        try {
            foreach($id as $v){
                $res = $db->delete($v);
                if($res){
                    $count++;
                }
            }

            Db::commit();
            $b = true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        if($b){
            return json(array('status'=>'y','info'=>'删除成功^ ^','count'=>$count));
        }else{
            $this->ajaxReturn(array('status'=>'n','info'=>'删除失败!'));
        }
    }

    public function category(){
        $db = Db::name('Product_category');
        #排序
        $sort = input('sort');
        if(!empty($sort)){
            foreach($sort as $k=>$v){
                $v = intval($v);
                $db->where(array('cat_id'=>$k))->setField('sort',$v);
            }
        }
        #查询分配数据
        $list = $db->order('sort,cat_id desc')->select();
        $list = category_merge($list);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function category_add(){
        #实例化 new \Think\Model('category')#
        $db = Db::name('product_category');
        #所有分类数据
        $list = Db::name('product_category')->order('sort,cat_id desc')->select();

        if($this->request->isPost()){
            #提交过程
            $data = input('post.');
            #数据处理
            if(empty($data['cat_name'])){
                return json(['status'=>'n','info'=>'分类名称不能为空']);
            }
            if(!isset($data['name']) && empty($data['name'])){
                return json(['status'=>'n','info'=>'最少选择一个服务类型']);
            }
            if(empty($data['img'])){
                unset($data['img']);
            }
            #操作方式
            if($_POST['cat_id'] > 0){

                //调用阿里云上传
                if($this->request->file()){
                    $file = $this->request->file('img');
                    $info = $file->getInfo();
                    $datas = [
                        'name'=>$info['name'],
                        'tmp_name'=>$info['tmp_name']
                    ];
                    $oss = new Oss();
                    $upload_info = $oss->uploadPostFile($datas);
                    $data['thumb'] = $upload_info;
                }
                if($data['parent_id'] == $data['cat_id']){
                    return json(['status'=>'n','info'=>'不能使用自己为上级']);
                }
                $b = $db->update($data);
            }else{
                //调用阿里云上传
                if($this->request->file()){
                    $file = $this->request->file('img');
                    $info = $file->getInfo();
                    $datas = [
                        'name'=>$info['name'],
                        'tmp_name'=>$info['tmp_name']
                    ];
                    $oss = new Oss();
                    $upload_info = $oss->uploadPostFile($datas);
                    $data['thumb'] = $upload_info;
                }
//                $data['is_show'] = 1;
                $b = $db->insert($data);
            }
            #操作结果
            if($b !== false){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'y','info'=>'操作失败！']);
            }
        }else{

            #显示数据
            $cat_id = input('cat_id',0);
            if($cat_id > 0){
                $row = $db->find($cat_id);
            }else{
                #热门推荐
                $row['is_show'] = 0;
                $row = [
                    'thumb' => '/uploads/img/uploads.jpg',
                    'cat_name' => '',
                    'cat_id' => 0,
                    'parent_id' => 0,
                    'describe' => '',
                    'name' => '1',
                ];
            }
            $cat = Db::name('product_category')->where(['parent_id'=>0])->select();
            $this->assign('cat',category_merge($cat));
            $this->assign($row);
            $this->assign('list',$list);

            return $this->fetch();
        }
    }

    public function category_del(){
        $result = Db::name('product_category')->delete(trim(input('id')), ',');
        return ['status' => $result ? 'y' : 'n', 'info' => '删除' . ($result ? '成功' : '失败!')];
    }
    /**
     * 订单列表
     * @return mixed
     */

    public function order_list(){
        $data = input('param.');
        $list = Db::name('order');
        $type = isset($data['type'])?$data['type']:'';
        $pay_state = isset($data['pay_state'])?$data['pay_state']:'';
        if (isset($data['start']) && $data['start'] && isset($data['end']) && $data['end']) {
            $time_max = strtotime($data['end']);
            $time_min = strtotime($data['start']);
            $data['start'] = $time_min;
            $data['end'] =  $time_max;
            $list->where('add_time', '>=', $data['start']);
            $list->where('add_time', '<=', $data['end']);
        }
        if (isset($data['word']) && $data['word']) {
            $list->where(['username|name|order_sn' => ['eq', $data['word']]]);
        }
        if(!empty($type)){
            if($type == 1){
                $list->where('shipping_state', 'eq', 0);
            }
            if($type == 2){
                $list->where('shipping_state', 'eq', 1);
            }
            if($type == 3){
                $list->where('shipping_state', 'eq', 2);
            }
            if($type == 4){
                $list->where('shipping_state', 'eq', 3);
            }
        }
        if(!empty($pay_state)){
            if($pay_state == 1){
                $list->where('a.pay_state', 'eq', 0);
            }
            if($pay_state ==2){
                $list->where('a.pay_state', 'eq', 1);
            }
        }
        $join = [
            ['tb_product c','a.product_id=c.id'],
        ];
         $lists = $list
            ->order('order_id desc')
             ->alias('a')
            ->join($join)
             ->field('a.*,c.thumb, c.name as shopname')
            ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function($item){
              $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                return $item;
            });
        $this->assign('info',config('info.ordertype'));
        $this->assign('paytype',config('info.paytype'));
        $this->assign('list',$lists);
        $this->assign('page',$lists->render());
        $this->assign('count',$lists->count());
        return $this->fetch();
    }

    /**
     * 编辑发货
     */
    public function order_status(){
        $id = input('uid');
        $db = Db::name('order');
        if($this->request->isPost()){
            $POST['shipping_state'] = input('shipping_state');
            $POST['wlbh'] = input('wlbh');
            if($POST['shipping_state'] == 1 && empty($POST['wlbh'])){
                return json(['status'=>'n','info'=>'确认发货必须填写物流编码']);
            }
            $hair = Db::name('order')->where(['order_id'=>$id])->value('hair');
            if($POST['shipping_state'] == 0 && !empty($POST['wlbh']) && $hair == 0){
                return json(['status'=>'n','info'=>'填写物流编码必须确认发货']);
            }
            #操作方式
            if($_POST['uid'] > 0){

                if($hair == 1){
                    $d['wlbh'] = $POST['wlbh'];
                }else{
                    $d = $POST;
                    $d['hair'] = 1;
                }
                #修改
                $b = $db->where(['order_id'=>$_POST['uid']])->update($d);
            }else{
                return json(['status'=>'n','info'=>'错误操作']);
            }
            #结果
            if($b !== false){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'操作失败!']);
            }
        }
        $row = $db->find($id);
        $this->assign($row);
        #渲染模板
        return $this->fetch();
    }



}