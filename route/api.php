<?php

use think\Facade\Route;
use think\Facade\Env;

Route::domain(Env::get('app.api_url'), function () {
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: POST, PUT, GET, OPTIONS, DELETE, HEAD, PATCH');
    header("Access-Control-Allow-Headers: token, language, Origin, X-Requested-With, Content-Type, Accept");

    Route::miss(function () {
        exit();
    });
    Route::group('User', function () {
        Route::post('login', '/login');  //  登陆
        Route::post('register', '/register');  //  注册
        Route::get('info', '/index');  //获取用户信息

        Route::post('sendmsg', '/sendmsg');  //发送验证码

        Route::get('teaminfo', '/teamInfo');  //团队信息
        Route::post('uploadheader', '/uploadHeader');  //头像上传

        Route::get('gethelp', '/getHelp');  //反馈列表
        Route::post('gethelp', '/getHelp');  //提交反馈

        Route::get('aboutus', '/aboutUs');  //关于我们

        Route::post('moneyapply', '/moneyApply');  //提现申请

        Route::post('bank', '/bank');  //银行卡
        Route::get('bank', '/bank');  //银行卡

        Route::post('card', '/card');  //支付宝/微信
        Route::get('card', '/card');  //支付宝/微信
        Route::get('cardid', '/cardid');  //账户设置//  支付宝、微信 、银行卡id

//        Route::post('upphone', '/upphone');  //修改手机号
        Route::post('uppassword', '/uppassword');  //修改密码

        Route::get('invitation', '/invitation');  //推广码

        Route::post('forgetpwd', '/forgetPwd');
        Route::post('oss', '/oss');

        Route::post('pdtrade', '/pdtrade');  //判断交易密码
        Route::post('logout', '/logout');  //退出登陆
        Route::post('upload', '/upload');  //上传

        Route::get('getClientIP', '/getClientIP');  //用户ip
        Route::get('getCity', '/getCity');  //获取所在地
        Route::get('getjin', '/getJin');  //获取佣金说明
        Route::get('updateusername', '/updateUsername');  //修改用户名
        Route::post('updateusername', '/updateUsername');  //获取佣金说明
        Route::get('findhelp', '/findhelp');  //获取问答详情
        Route::get('integral', '/integral');  //积分明细
        Route::post('upinfo', '/upInfo');  //修改用户信息
        Route::get('about', '/about');  //关于我们

        /**
         * 约邻功能
         */
        Route::post('selfdynamic', '/selfDynamic');  //发布动态
        Route::get('selfdynamic', '/selfDynamic');  //动态列表

        Route::post('userauth', '/userAuth');  //提交身证认证
        Route::get('userauth', '/userAuth');  //身证证号

         Route::post('enterprise', '/enterpriseAuthentication');  //提交企业认证
         Route::get('enterprise', '/enterpriseAuthentication');  //获取企业认证

         Route::post('applyskill', '/applySkill');  //提交技能认证
         Route::get('applyskill', '/applySkill');  //提交技能 认证
         Route::get('skilllist', '/skillList');  //我的技能


        Route::get('usermoney', '/userMoney');  //用户余额
        Route::get('usersuthstatus', '/userAuthStatus');  //所有认证信息

        Route::get('beantransmoney', '/beanTransMoney');  //豆豆转余额
        Route::post('beantransmoney', '/beanTransMoney');  //豆豆转余额
        Route::get('getallcity', '/getAllCity');  //所有城市
        Route::get('getson', '/getSon');  //获取下级城市

        Route::get('homepage', '/homePage');  //个人信息
        Route::get('getskillinfo', '/getSkillInfo');  //个人技能信息
        Route::get('getevaluate', '/getEvaluate');  //个人评价信息
        Route::post('userfollow', '/userFollow');  //关注
        Route::get('ossinfo', '/ossInfo');  //关注


        Route::get('serverimg', '/serverImg');  //服务图片
        Route::get('getcatinfo', '/getCatInfo');
        Route::post('getuserevaluate', '/getuserevaluate');

        Route::post('getcid', '/getCid');

    })->prefix('User');
    /**
     * 团队
     */
    Route::group('Vip', function () {
        Route::post('joinvip', '/joinVip');  //加入会员
        Route::post('updateleveltime', '/updateLevelTime');  //加入会员
        Route::post('vipapply', '/vipApply');  //会员申请
        Route::get('vipapply', '/vipApply');  //会员申请
        Route::post('viporder', '/vipOrder');  //生成会员订单

        Route::post('moneyrecharge', '/moneyRecharge');  //用户充值
    })->prefix('Vip');

    /**
     * 团队
     */
    Route::group('Team', function () {
        Route::post('jointeam', '/joinTeam');  //测试团队
        Route::post('getPython', '/getPython');  //测试团队
    })->prefix('Team');
     /**
     * 首页相关
     */
    Route::group('Home', function () {
        Route::get('banner', '/banner');  //轮播图
        Route::get('kefu', '/kefu');  //客服
        Route::get('search', '/search');  //搜索

        Route::get('edition', '/edition');  //版本
        Route::get('editions', '/editions');  //版本

        Route::get('information', '/information');  //资讯列表
        Route::get('informationdetals', '/informationDetals');  //资讯详情
        Route::post('applyactivity', '/applyActivity');  //申请活动

        Route::get('getinfo', '/getInfo');  //各种类型公告
        Route::get('infodetails', '/infoDetails');  //获取详情


        Route::get('getallarder', '/getAllOrder');  //获取推荐
        Route::post('flowdel', '/flowDel');  //删除关注


        Route::get('usermassage', '/userMassage');  //消息
        Route::post('msgstatus', '/updateMsgStatus');  //消息状态

        Route::post('appointment', '/userAppointment');  //约他
        Route::post('delmsg', '/delMsg');  //删除信息
        Route::post('invoice', '/invoice');  //直接约
        Route::post('authAppointment', '/authAppointment');  //自动 约他
        Route::get('getAcceptInfo', '/getAcceptInfo');  //自动 约他
        Route::get('system', '/system');  //获取系统设置值
        Route::get('testgetui', '/testgetui');  //获取系统设置值


    })->prefix('Home');

    Route::group('Space', function () {
        Route::get('oss', '/oss');    //  获取oss上传参数
    })->prefix('Space');


    Route::group('Distinguish', function () {
        Route::get('index', '/index');    //  人脸识别
        Route::post('gettoken', '/getToken');    //  获取Access Token
    })->prefix('Distinguish');

    Route::group('Callback', function () {
        Route::any('alipayrecharge', '/alipayRecharge');
        Route::any('alipay', '/alipay');  //支付宝会员购买回调
        Route::any('wechat', '/wechat'); //微信会员购买回调
        Route::any('wechatrecharge', '/wechatRecharge');  //微信充值回调
        Route::any('alipayreceipt', '/alipayreceipt');  //支付宝发单
        Route::any('wechatreceipt', '/wechatreceipt'); //微信发单

    })->prefix('api/Callback');
    /**
     * 积分商城
     */
    Route::group('Shop', function () {

        Route::get('shoplist', '/shoplist');  //商城列表
        Route::get('shopdetails', '/shopdetails');  //商品详情

        Route::post('joinorder', '/joinorder');  //支付并生成订单
        Route::get('joinorder', '/joinorder');  //订单显示
        Route::post('payorder', '/payOrder');  //支付
        Route::get('orderlist', '/orderlist');  //订单列表
        Route::post('orderdel', '/orderDel');  //取消订单

        Route::post('addressedit', '/addressedit');  //编辑地址
        Route::get('addressedit', '/addressedit');  //编辑地址
        Route::get('addressdetails', '/addressDetails');  //地址详情
        Route::post('addressDel', '/addressDel');  //删除地址

        Route::post('addCollection', '/addCollection');  //加入收藏
        Route::get('addCollection', '/addCollection');  //收藏状态
        Route::get('collectionlist', '/collectionList');  //收藏列表
        Route::post('collectiondel', '/collectionDel');  //删除收藏



    })->prefix('Shop');

    Route::group('register', function () {
        Route::get('index', '/index');
    })->prefix('api/register');

    /**
     * 支付接口
     */
    Route::group('Pay', function () {
        Route::post('fzb', '/payAliPay');    //支付宝支付
        Route::post('wxzf', '/wechatPay');   //微信支付
    })->prefix('api/Pay');

     /**
     * 发单/接单管理
     */
    Route::group('Order', function () {
        Route::get('ordertype', '/orderType');  #接单发单列表
        Route::post('acceptoutdetalsorder', '/acceptOutDetalsOrder');  #退出参与竞争
        Route::post('acceptoutorder', '/acceptOutOrder');  #退出参与订单
        Route::post('acceptorder', '/acceptOrder');  #确认接单
        Route::post('confirmpaymoney', '/confirmPayMoney');  #确认已付款


        Route::post('invoice', '/invoice');  #用户发单、接单
        Route::post('paymoney', '/paymoney');  //支付诚意金
        Route::get('home', '/home');  //接单大厅
        Route::get('orderdetails', '/orderDetails');  //需求详情
        Route::get('appointment', '/userAppointment');  //约他
        Route::post('appointment', '/userAppointment');  //约他
        Route::get('conduct', '/orderConduct');  //我的发单  ---进行中
        Route::post('conduct', '/orderConduct');   //我的发单  ---进行中



        Route::post('application', '/orderApplication');  //参与应聘
        Route::get('myorderinvoice', '/myOrderInvoice');  //我的发单
        Route::get('myorderaccept', '/myOrderAccept');  //我的接单
        Route::post('evaluate', '/evaluate');  //提交评论

        Route::post('cancelorder', '/cancelOrder');  //取消发单
        Route::post('orderdel', '/orderDel');  //删除发单
        Route::post('arrive', '/arrive');  //我已到达
        Route::post('senddx', '/senddx');  //我已到达


    })->prefix('api/Order');

    /**
     * 省市代收益
     */
    Route::group('Team', function () {
        Route::post('profitmoeny', '/profitMoeny');  #接单发单列表
    })->prefix('api/Team');

    /**
     * 省市代收益
     */
    Route::group('Distance', function () {
        Route::post('joinaddress', '/joinAddress');  #接单发单列表
    })->prefix('api/Distance');

    /**
     * 消息推送
     */
    Route::group('Push', function () {
        Route::post('getui', '/getui');    //支付宝支付
    })->prefix('Push');

    /**
     * 重新写接发单流程
     */
    Route::group('Reorder', function () {
        Route::post('userapply', '/userApply');  //todo::下单支付
        Route::get('userapply', '/userApply');  //todo::余额
        Route::post('priceedit', '/priceEdit');  //todo::加价减价
        Route::post('orderappeal', '/orderAppeal');  //todo::订单申诉
    })->prefix('Reorder');

})->bind('api')->prefix('api');


