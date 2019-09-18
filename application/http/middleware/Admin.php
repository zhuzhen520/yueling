<?php

namespace app\http\middleware;

use think\Db;

class Admin
{
    public function handle($request, \Closure $next)
    {
        $id = session('admin_id');
        $admin = Db::table('tb_admin')->find($id);
        if(!$admin) {
            return redirect('index/login');
        }
        $url = $request->baseUrl();
        $auth = config('base.auth.admin');
        $admin['role'] = explode(',', $admin['role']);
        if (!in_array($url, $admin['role']) && !in_array($url, $auth) && $admin['is_default'] == 0) {
            if (request()->isAjax()) {
                exit(json_encode(['status' => 1, 'msg' => '没有操作权限']));
            } else {
                die("<h3 style='color:red;text-align: center; margin-top:200px;'>没有操作权限</h3>");
            }
        }
        $request->admin = $admin;
        return $next($request);
    }
}
