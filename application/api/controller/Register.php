<?php
namespace app\api\controller;

use app\helps\Auth;
use app\helps\Oss;
use app\models\Info;
use think\Controller;
use think\facade\Env;
use think\Loader;
use think\Validate;
use app\helps\Mns;
use think\Db;
/**
 * Class User
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
class Register extends Controller
{


    public function index()
    {
        return view('index', [
            'invite' => $this->request->get('invite'),
            'android' => $this->request->get('android'),
            'ios' => $this->request->get('ios'),
        ]);
    }

}