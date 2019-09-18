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

class Info extends Base
{
    public function infolist(){
        $data = input('param.');
        $list = Db::name('info');
        if (isset($data['type']) && $data['type'] && !empty($data['type'])) {
            $list->where(['type' => ['eq', $data['type']]]);
        }
        $lists = $list
                 ->order('id desc')
                 ->where('type','neq','order')
                 ->where('type','neq','order')
                 ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
                 ->each(function($item){
                     $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                     return $item;
                 });
        $this->assign('info',config('info.info'));
        $this->assign('list',$lists);
        $this->assign('page',$lists->render());
        $this->assign('count',$lists->count());
        return $this->fetch();
    }

    public function infolist_add(){

        $db = Db::name('info');
        if($this->request->isPost()){
            $POST = input('param.');
            if(empty($POST['content'])){
                return json(['status'=>'n','info'=>'请输入内容!']);
            }

            $POST['admin'] = $POST['admin']['username'];
            $POST['add_time'] = time();
            #操作方式
            if($POST['id'] > 0){
                #修改
                $b = $db->update($POST);
            }else{
                $POST['add_time'] = time();
                $config_info = config('info.info');
                foreach ($config_info as $v){
                   if($POST['type'] ==  $v['type'] && $v['status'] == 1){
                       Db::name('info')->where(['type'=>$POST['type']])->delete();
                   }
                }
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
                $row['info'] = config('info.info');
            }else{
                $row = [
                    'type' => '',
                    'content' => '',
                    'title' => '',
                    'user_id' => 0,
                    'id' => $id,
                    'info'=>config('info.info')
                ];
            }
        }
        $this->assign('info',config('info.info'));
        $this->assign($row);
        #渲染模板
        return $this->fetch();
    }

    public function info_del(){
        $id = explode(',',trim(input('id'),','));
        $db = Db::name('info');
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


    public function information(){
        $data = input('param.');
        $list = Db::name('information');
        if (isset($data['type']) && $data['type'] && !empty($data['type'])) {
            $list->where(['type' => ['eq', $data['type']]]);
        }
        $lists = $list
            ->order('id desc')
            ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function($item){
                $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                return $item;
            });
        $this->assign('info',config('info.infomation'));
        $this->assign('list',$lists);
        $this->assign('page',$lists->render());
        $this->assign('count',$lists->count());
        return $this->fetch();
    }

    public function information_add(){

        $db = Db::name('information');
        if($this->request->isPost()){
            $POST = input('param.');
            unset($POST['admin']);
            if(empty($POST['content'])){
                return json(['status'=>'n','info'=>'请输入内容!']);
            }
            $POST['add_time'] = time();
            $POST['img'] = '';
            if($this->request->file()){
                $file = $this->request->file('img');
                $info = $file->getInfo();
                $datas = [
                    'name'=>$info['name'],
                    'tmp_name'=>$info['tmp_name']
                ];
                $oss = new Oss();
                $upload_info = $oss->uploadPostFile($datas);
                $POST['img'] = $upload_info;
            }
            if(empty($POST['img'])){
                unset($POST['img']);
            }

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
                    'type' => '',
                    'content' => '',
                    'title' => '',
                    'img' => '/uploads/img/uploads.jpg',
                    'id' => $id,
                ];
            }
        }
        $this->assign('info',config('info.infomation'));

        $this->assign($row);
        #渲染模板
        return $this->fetch();
    }

    public function information_del(){
        $id = explode(',',trim(input('id'),','));
        $db = Db::name('information');
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

    public function activity(){
        $data = input('param.');
        $list = Db::name('information_activity');
        if (isset($data['type']) && $data['type'] && !empty($data['type'])) {
            $list->where(['info_id' => ['eq', $data['type']]]);
        }
        $join = [['tb_information b','b.id=a.info_id']];
        $lists = $list
            ->alias('a')
            ->join($join)
            ->field('a.*,b.title as btitle, b.img as bimg')
            ->order('id desc')
            ->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function($item){
                $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                return $item;
            });
        $activity = Db::name('information')->where(['type'=>4])->field('id,title')->order('id desc')->select();

        $this->assign('activity',$activity);
        $this->assign('list',$lists);
        $this->assign('page',$lists->render());
        $this->assign('count',$lists->count());
        return $this->fetch();
    }

    public function activity_del(){
        $id = explode(',',trim(input('id'),','));
        $db = Db::name('information_activity');
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
            return json(array('status'=>'n','info'=>'删除失败!'));
        }
    }

}