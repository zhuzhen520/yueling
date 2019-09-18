<?php

namespace app\http\middleware;

use app\helps\Auth as AuthHelp;
use think\Db;
use think\Request;

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST, PUT, GET, OPTIONS, DELETE, HEAD, PATCH');
header("Access-Control-Allow-Headers: token, language, Origin, X-Requested-With, Content-Type, Accept");
//$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
//
//$allow_origin = array(
//    'api.appcarshop.com',
//    'admin.appcarshop.com'
//);
//
//if(in_array($origin, $allow_origin)){
//    header('Access-Control-Allow-Origin:'.$origin);
//}
class Auth
{
    public function handle($request, \Closure $next)
    {
        if ($request->isOptions()) {
            exit;
        }
        $GLOBALS['language'] = $request->header('language') ? : 'cn';
        $url = $request->baseUrl();
        $verify = config('base.auth.api');
//        dump($url);
//        dump($verify);exit;
        if( $request->module() == 'api'){   #api验证
            if (!in_array($url, $verify) && !$request->header('token')) {
                echo json_encode(['data' => null, 'msg' => '请先登录账号', 'status' => 999], JSON_UNESCAPED_UNICODE);
                exit;
            }
            if ($request->header('token') && in_array($url, $verify) == false) {
                $auth = new AuthHelp;
                $user_id = $auth->getAuth($request->header('token'));
                $user = Db::table('tb_user')->where('id', $user_id)->find();
                if (!$user) {
                    echo json_encode(['data' => null, 'msg' => '用户不存在', 'status' => 998], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                if ($user['status'] == 1) {
                    echo json_encode(['data' => null, 'msg' => '用户已被冻结', 'status' => 997], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $request->user_id = $user_id;
                $request->user = $user;
//                if(\think\facade\Request::isPost() && !in_array($url, $verify)){
//                    if($user['benefit'] == 0){
//                        echo json_encode(['data' => null, 'msg' => '请先支付预付金', 'status' => 888], JSON_UNESCAPED_UNICODE);
//                        exit;
//                    }
//                }
            }

        }else{  #后台验证
            $verify = config('base.auth.admin');
            if (!in_array($url, $verify) && !session('?admin_id')) {
                echo json_encode(['data' => null, 'msg' => '请先登录账号', 'status' => 996], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        return $next($request);
    }


}
