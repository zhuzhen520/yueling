<?php

namespace app\admin\controller;

use app\models\Admin;
use app\validate\Admin as Validate;
use phpass;

/**
 * Class Index
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
Class Index extends Base
{
    public function login()
    {
        $admin_id = session('admin_id');
        if (Admin::get($admin_id)) {
            return redirect('/main/index');
        }
        $data = $this->request->post();
        if ($this->request->isPost()) {
            $validate = new Validate();
            if (!$validate->scene('login')->check($data)) {
                return json(['status' => 1, 'msg' => $validate->getError()]);
            }
            $admin = Admin::get(['username' => $data['username']]);
            if ($admin == false) {
                return json(['status' => 1, 'msg' => '账号或者密码错误']);
            }
            #验证密码
             $passwordcheck = $this->checkPassword($data['password'],$admin['entry'],2,$admin['password']);

            if (!$passwordcheck) {
                return json(['status' => 1, 'msg' => '账号或者密码错误']);
            }

            session('admin_id', $admin['id']);
            $admin->save([
                'login_num' => $admin['login_num'] + 1,
                'login_ip' => request()->ip(),
            ], [
                'id' => $admin['id']
            ]);
            return json(['status' => 0, 'msg' => '登陆成功', 'url' => url('main/index')]);
        }

        return view(null, [
            'data' => $data
        ]);
    }

    public function logout()
    {
        session('admin_id', null);
        $this->redirect('index/login');
    }

}