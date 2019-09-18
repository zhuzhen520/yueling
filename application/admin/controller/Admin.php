<?php

namespace app\admin\controller;

use app\models\Admin as Model;
use app\validate\Admin as Validate;
use think\Db;
use phpass;

/**
 * Class Admin
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
Class Admin extends Base
{
    public function index()
    {
        $results = Db::table('tb_admin')->paginate();
        return view(null, [
            'list' => $results
        ]);
    }

    public function input()
    {
        $id = $this->request->get('id');
        $data = $this->request->post('data');
        if ($this->request->isPost()) {
            $data['id'] = $id;
            $validate = new Validate();
            $entry = $this->get_rand_str();
            if ($data['id']) {
                if (!$validate->scene('edit')->check($data)) {
                    return json(['status' => 1, 'msg' => $validate->getError()]);
                }
                $update = [
                    'nickname' => $data['nickname'],
                    'role' => $id == 1? '':implode(',', $data['role'])
                ];
                if (isset($data['password']) && $data['password']) {
                     $update['entry'] = $entry;
                     $update['password'] = $this->checkPassword($data['password'],$update['entry']);
                }
                $res = Db::table('tb_admin')->where('id', $data['id'])->update($update);
            } else {
                if (!$validate->scene('add')->check($data)) {
                    return json(['status' => 1, 'msg' => $validate->getError()]);
                }

                $res =  Db::table('tb_admin')->insert([
                    'username' => $data['username'],
                    'entry' => $entry,
//                    'password' => md5(md5($data['password'] ? : 123456).md5($entry)),
                    'password' => $this->checkPassword($data['password'],$entry),
                    'nickname' => $data['nickname'],
                    'created' => date('Y-m-d H:
                    i:s'),
                    'role' => implode(',', $data['role'])
                ]);
            }
            if ($res) {
                return json(['status' => 0, 'msg' => '操作成功']);
            }
            return json(['status' => 1, 'msg' => '操作失败']);
        } elseif ($id) {
            $data = Db::table('tb_admin')->where('id', $id)->find();
            $data['role'] = explode(',', $data['role']);
            if (!$data) {
                return redirect('/main/404');
            }
        }
        return view(null, [
            'data' => $data
        ]);
    }

    public function del()
    {
        $admin_id =  $this->request->get('id');
        $admin = Db::table('tb_admin')->where('id', $admin_id);
        if ($admin['is_default'] == 1) {
            return json(['status' => 1, 'msg' => '超级管理员不允许删除']);
        }
        $res = Db::table('tb_admin')->where('id', $admin_id)->delete();
        if ($res) {
            return json(['status' => 0, 'msg' => '删除成功']);
        }
        return json(['status' => 1, 'msg' => '操作失败']);
    }
}