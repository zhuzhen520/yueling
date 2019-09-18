<?php

use think\Facade\Route;
use think\Facade\Env;

Route::domain(Env::get('app.admin_url'), function () {
    Route::get('/', function(){
        return redirect('main/index');
    });
    /**
     * 登陆
     */
    Route::group('index', function () {
        Route::get('login', '/login');  //登陆页面
        Route::post('login', '/login'); //post 提交数据
        Route::get('logout', '/logout'); //退出登录
    })->prefix('admin/index');

    /**
     * 欢迎页面
     */
    Route::group('main', function () {
        Route::get('index', '/index');  //基础页面,
        Route::get('welcome', '/welcome');  //首页信息页面
        Route::post('infoData', '/infoData');  //信息统计接口数据
        Route::post('statisticInfo', '/statisticInfo');  //时间轴拆线图    两条统计数据
        Route::get('404', '/miss');

    })->prefix('admin/main');

    /**
     * 用户管理
     */
    Route::group('user', function () {
        //用户
        Route::get('index', '/index');  //基础页面,
        Route::get('UserAdd', '/UserAdd');  //添加/编辑用户
        Route::post('UserAdd', '/UserAdd');  //提交添加/编辑用户
        Route::get('userrecharge', '/userRecharge');  //充值
        Route::post('userrecharge', '/userRecharge');  //充值
        Route::get('UserWallet', '/UserWallet');  //用户直推
        Route::post('userdel', '/userDel');  //删除用户
        Route::post('toggle_status', '/toggle_status');  //状态

        //认证
        Route::get('head_auth', '/head_auth');  //头像认证
        Route::post('head_status', '/head_status');  //头像认证

        Route::get('user_auth', '/user_auth');  //认证列表
        Route::post('user_auth_update', '/user_auth_update');  //刷新相似度
        Route::post('user_auth_sta', '/user_auth_sta');  //认证状态
        Route::post('user_auth_del', '/user_auth_del');  //认证删除

        Route::get('user_company', '/user_company');  //公司认证列表
        Route::post('user_company_sta', '/user_company_sta');  //公司认证状态
        Route::post('user_company_del', '/user_company_del');  //公司认证删除

        Route::get('user_skill', '/user_skill');  //技能认证列表
        Route::post('user_skill_sta', '/user_skill_sta');  //技能认证状态
        Route::post('user_skill_del', '/user_skill_del');  //技能认证删除

        //团队信息
        Route::get('team', '/team');  //团队列表
        Route::get('teamadd', '/teamadd');  //编辑团队等级
        Route::post('teamadd', '/teamadd');  //编辑团队等级
        Route::get('teamprofit', '/teamProfit');  //团队收益信息

        Route::get('moneyprofit', '/moneyProfit');  //余额收益信息

        Route::get('recommend', '/recommend');  //推荐客源

        Route::get('money_apply', '/money_apply');  //提现申请
        Route::post('money_apply_status', '/money_apply_status');

        Route::post('usermoney', '/usermoney');
        Route::post('userdl', '/userdl');
        Route::post('userteam', '/userteam');

    })->prefix('admin/user');
    /**
     * 价格走势
     */
    Route::group('coins', function () {
        Route::get('price', '/price');  //价格页面,
        Route::post('input', '/input');  //,
        Route::post('getm', '/getm');  //,
    })->prefix('admin/coins');
      /**
     * 商城管理
     */
    Route::group('product', function () {
        Route::get('product_list', '/product_list');  //商城 列表,
        Route::post('product_add', '/product_add');  //编辑,
        Route::get('product_add', '/product_add');  //编辑,
        Route::post('product_del', '/product_del');  //删除,
        Route::get('category', '/category');  //分类列表,
        Route::get('category_add', '/category_add');  //分类编辑,
        Route::post('category_add', '/category_add');  //分类编辑,
        Route::post('catlist', '/catlist');  //查询分类,
        Route::post('category_del', '/category_del');  //删除分类,
        Route::get('order_list', '/order_list');  //订单列表,
        Route::get('order_status', '/order_status');  //编辑发货,
        Route::post('order_status', '/order_status');  //编辑发货,
    })->prefix('admin/product');

    /**
     * 商城管理
     */
    Route::group('product', function () {
        Route::get('vehicle_list', '/vehicle_list');  //所有车辆,
        Route::post('vehicle_add', '/vehicle_add');  //编辑,
        Route::get('vehicle_add', '/vehicle_add');  //编辑,
        Route::post('vehicle_del', '/vehicle_del');  //删除,

        Route::get('inquiry', '/inquiry');  //询问底价,
        Route::get('car', '/car');  //车辆品牌,
        Route::post('car', '/car');  //车辆品牌,
        Route::post('brondinsert', '/brondinsert');  //提交,
    })->prefix('admin/product');

    /**
     * 交易订单
     */
    Route::group('order', function () {
        Route::get('receipt_list', '/receipt_list');  //发单管理
        Route::post('dealorder', '/dealOrder');  //发单管理

    })->prefix('admin/order');

    /**
     * base
     */
    Route::group('base', function () {
        Route::post('toggle_status', '/toggle_status');  //状态
    })->prefix('admin/base');

    /**
     * 信息发布
     */
    Route::group('info', function () {
        Route::get('infolist', '/infolist');  //信息发布
        Route::get('infolist_add', '/infolist_add');  //信息发布
        Route::post('infolist_add', '/infolist_add');  //信息发布
        Route::post('info_del', '/info_del');  //删除

        //资讯
        Route::get('information', '/information');  //发布资讯
        Route::get('information_add', '/information_add');  //资讯发布
        Route::post('information_add', '/information_add');  //资讯发布
        Route::post('information_del', '/information_del');  //删除资讯
        Route::get('activity', '/activity');  //活动报名
        Route::post('activity_del', '/activity_del');  //删除
    })->prefix('admin/info');
    /**
     * 系统管理
     */
    Route::group('system', function () {
        Route::get('operation', '/operation');  //操作日志
        Route::get('kefu', '/kefu');  //客服中心
        Route::get('kefu_add', '/kefu_add');  //添加客服
        Route::post('kefu_add', '/kefu_add');  //添加客服
        Route::post('kefu_del', '/kefu_del');  //删除

        Route::get('message', '/message');  //留言管理
        Route::get('message_add', '/message_add');  //留言编辑
        Route::post('message_add', '/message_add');  //留言编辑
        Route::post('message_del', '/message_del');  //删除留言
        Route::get('message_edit', '/message_edit');  //删除留言
        Route::post('message_edit', '/message_edit');  //删除留言

        Route::get('usual', '/usual');  //留言管理

        Route::get('banner', '/banner');  //轮播图
        Route::get('banner_add', '/banner_add');  //添加轮播图
        Route::post('banner_add', '/banner_add');  //添加轮播图
        Route::post('banner_del', '/banner_del');  //删除轮播图

        Route::get('recommend', '/recommend');  //推荐背景图
        Route::get('recommend_add', '/recommend_add');
        Route::post('recommend_add', '/recommend_add');
        Route::post('recommend_del', '/recommend_del');


        Route::get('config', '/config');  //系统设置
        Route::post('config', '/config');  //系统设置

        Route::get('about', '/about');  //系统设置
        Route::post('about', '/about');  //系统设置
    })->prefix('admin/system');


    Route::group('register', function () {
        Route::get('input', '/input');
    })->prefix('admin/register');

    Route::group('Upload', function () {
        Route::post('upload', '/upload');
    })->prefix('Upload');

    $menu = config('menu.menu');

    foreach ($menu as $k => $v) {
        if ($v['show'] == true) {
            Route::group($k, function () use ($v) {
                foreach ($v['sub'] as $x => $y) {
                    Route::rule($x, $y['uri'])->method($y['method']);
                }
            })->prefix('admin');
        }
    }
})->bind('admin');

