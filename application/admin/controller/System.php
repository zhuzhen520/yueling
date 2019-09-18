<?php
namespace app\admin\controller;
use app\helps\Oss;
use org\Baksql;
use think\Db;
use think\facade\Config;
use think\facade\Env;

class System extends Base{
    public function config(){
        $db = Db::name('config');
        if($this->request->isPost()){
            $base = input('post.base/a');
            $team = input('post.team/a');
//            $reward = input('post.reward/a');
            $user = input('post.user/a');
            $data = [];
            foreach($base as $bk=>$bv){
                $data[] = array(
                    'name' => $bk,
                    'value' => $bv,
                    'type' => 'base'
                );
            }
//            foreach($reward as $bk=>$bv){
//                $data[] = array(
//                    'name' => $bk,
//                    'value' => $bv,
//                    'type' => 'reward'
//                );
//            }
            foreach($team as $bk=>$bv){
                $data[] = array(
                    'name' => $bk,
                    'value' => $bv,
                    'type' => 'team'
                );
            }
            foreach($user as $bk=>$bv){
                $data[] = array(
                    'name' => $bk,
                    'value' => $bv,
                    'type' => 'user'
                );
            }
            foreach($data as $v){
                $row = Db::name('config')->where(array('name'=>$v['name'],'type'=>$v['type']))->find();
                if(!empty($row)){
                    $v['id'] = $row['id'];
                    Db::name('config')->update($v);
                }else{
                    Db::name('config')->insert($v);
                }
            }
            return json(['status'=>'y','info'=>'操作成功']);
        }else{
            $base = get_config('base');
            $team = get_config('team');
//            $reward = get_config('reward');
            $user = get_config('user');

            $this->assign('user',$user);
            $this->assign('base',$base);
            $this->assign('team',$team);
//            $this->assign('reward',$reward);
            return $this->fetch();
        }
    }

    /* banner */
    public function banner(){
        $list = Db::name('banner')->order('type,id')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function banner_add(){
        $db = Db::name('banner');
        if($this->request->isPost()){
            $data =  input('param.');
            unset($data['admin']);
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
                $data['image_path'] = $upload_info;
            }

            #操作方式
            $data['add_time'] = time();
            #添加
            $b = $db->insert($data);
            if($b !== false){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'操作失败!']);
            }
        }
        return $this->fetch();
    }
    public function banner_del(){
        if($this->request->isPost()){
            $id = trim(input('post.id'));
            $c =  Db::name('banner')->delete($id);
            if($c){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'删除失败']);
            }
        }
    }

    /*客服*/
    public function kefu(){
        $list = Db::name('kefu')->order('type,id')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function kefu_add(){
        $db = Db::name('kefu');
        if($this->request->isPost()){
            #提交过程
            $data = input('post.');
            $data['add_time']=time();
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
            }else{
                unset($data['img']);
            }
            $b = $db->insert($data);
            #操作结果
            if($b !== false){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'操作失败']);
            }
        }else{
            return $this->fetch('kefu_add');
        }
    }
    public function kefu_del(){
        if($this->request->isPost()){
            $id = trim(input('id'),',');
            $b=Db::name('kefu')->delete($id);
            if($b){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'删除失败']);
            }
        }
    }
    /*静态规则*/
    public function static_config(){
        $input = input('get.');
        $type = input('type','1');
        $where = [];
        if($type){
            $where['type'] = $type;
        }

        $list = Db::name('static_config')->order('gold_lt')->where($where)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function static_config_add(){
        $db = Db::name('static_config');
        if($this->request->isPost()){
            $data = input('post.');
            if($data['id'] > 0){
                $b = $db->update($data);
            }else{
                $b = $db->insert($data);
            }
            return json(['status'=>$b?'y':'n','info'=>$b?'操作成功':'操作失败']);
        }else{

            $row = $db->where(['id'=>input('id')])->find($id);
            $this->assign($row);
            return $this->fetch();
        }
    }

    /* 留言管理 */
    public function message(){
        $input = input('param.');
        // print_r($input);

        $where = [];

        if(isset($input['state'])){
            $where['state'] = $input['state'];
        }
        $where['type']  = 0;
        $list = Db::name('message')->where($where)->order('id desc')->paginate(Env::get('app.page'))
        ->each(function($item){
            $item['admin_id'] = Db::name('admin')->where(['id'=>$item['admin_id']])->value('username');
            if(empty($item['admin_id'])){
                $item['admin_id'] = '待回复';
            }
            $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
            return $item;
        });
        $count = Db::name('message')->where($where)->count();
        $this->assign('list',$list);
        $this->assign('count',$count);
        $this->assign('page',$list->render());
        return $this->fetch();
    }
    /* 留言管理 */
    public function usual(){
        $input = input('param.');
        // print_r($input);

        $where = [];

        if(isset($input['state'])){
            $where['state'] = $input['state'];
        }
        $where['type']  = 1;
        $list = Db::name('message')->where($where)->order('id desc')->paginate(Env::get('app.page'))
        ->each(function($item){
            $item['admin_id'] = Db::name('admin')->where(['id'=>$item['admin_id']])->value('username');
            if(empty($item['admin_id'])){
                $item['admin_id'] = '待回复';
            }
            return $item;
        });
        $count = Db::name('message')->where($where)->count();
        $this->assign('list',$list);
        $this->assign('count',$count);
        $this->assign('page',$list->render());
        return $this->fetch();
    }

    /* 留言回复 */
    public function message_add(){
        $data = input('param.');
        if($this->request->isPost()){
            unset($data['admin']);
            $data['admin_id'] = session('admin_id');
            $data['admin_time'] = time();
            $b = Db::name('message')->where(['id'=>$data['id']])->update($data);
            if($b){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'操作成功']);
            }
        }else{
            $this->assign('id',$data['id']);
            return $this->fetch();
        }
        
    }
    /* 留言问答  */
    public function message_edit(){
        $data = input('param.');
        if($this->request->isPost()){
            unset($data['admin']);
            if(empty($data['id'])){
                $data['admin_id'] = session('admin_id');
                $data['admin_time'] = time();
                $data['type'] = 1;
                $data['username'] = '后台问答';
                $data['add_time'] = time();
                $data['state'] = 1;
                $b = Db::name('message')->insert($data);
            }else{
                $data['admin_id'] = session('admin_id');
                $data['state'] = 1;
                $b = Db::name('message')->where(['id'=>$data['id']])->update($data);
            }
            if($b){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'操作成功']);
            }
        }else{
            $online = Db::name('message')->where(['id'=>$data['id']])->find();
            $this->assign('online',$online);
            return $this->fetch();
        }

    }

    /* 留言提醒 */
    public function message_count(){
        $count = Db::name('message')->where(['state'=>0])->count();
        return json(['status'=>'y','count'=>$count < 99 ? $count : '99+']);
    }

    public function message_del(){
        $b = Db::name('message')->delete(input('id'));
        if($b){
            return json(['status'=>'y','info'=>'删除成功']);
        }else{
            return json(['status'=>'n','info'=>'删除失败']);
        }
    }

    /* 发现 */
    public function info(){
        $list = Db::name('system_info')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function info_add(){
        if(Request()->instance()->isPost()){}
    }

    /* 操作日志 */
    public function action_log(){
        
    }
    /*数据库备份*/
    public function databackup(){
        return $this->fetch();
    }

    /**
     * 操作日志
     */
    public function operation(){
        $data = input('param.');
        $list = Db::name('interface_log')->order('id DESC');
        if (isset($data['name']) && $data['name']) {
            $list->where('name|Interface_user_id', 'like', '%' . $data['name'] . '%');
        }
        if (isset($data['type']) && $data['type']) {
            $list->where('jk_name', 'like', '%' . $data['type'] . '%');
        }
        $lists = $list->paginate(Env::get('app.page'), false, ['query' => $this->request->param()])
            ->each(function ($item){
                $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                $item['username'] = Db::name('user')->where('id',$item['Interface_user_id'])->value('mobile');
                return $item;
            });
        return view('', [
            'list' => $lists,
            'count' => $lists->count(),
            'sel'=>Db::name('interface_log')->group('jk_name')->field('jk_name')->select()
        ]);
    }


    /* banner */
    public function recommend(){
        $list = Db::name('banner')->where('type','eq','recommend')->order('type,id')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function recommend_add(){
        $db = Db::name('banner');
        if($this->request->isPost()){
            $data =  input('param.');
            unset($data['admin']);
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
                $data['image_path'] = $upload_info;
            }

            #操作方式
            $data['add_time'] = time();
            #添加
            $b = $db->insert($data);
            if($b !== false){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'操作失败!']);
            }
        }
        return $this->fetch();
    }
    public function recommend_del(){
        if($this->request->isPost()){
            $id = trim(input('post.id'));
            $c =  Db::name('banner')->delete($id);
            if($c){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'删除失败']);
            }
        }
    }
    public function about(){

        $db = Db::name('about');
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
                $b = $db->insert($POST);
            }
            #结果
            if($b !== false){
                return json(['status'=>'y','info'=>'操作成功']);
            }else{
                return json(['status'=>'n','info'=>'操作失败!']);
            }
        }else{
            $row = $db->find();

        }
        $this->assign($row);
        #渲染模板
        return $this->fetch();
    }

}