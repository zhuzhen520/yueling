<?php

return [
    'menu' => [
        'order' => [
            'name' => '交易订单',
            'sub' => [
                'receipt_list' => ['name' => '发单列表', 'uri' => '/Order/receipt_list', 'show' => true, 'method' => 'get|post'],


                'category' => ['name' => '订单分类', 'uri' => '/product/category', 'show' => true, 'method' => 'get|post'],
                'category_add' => ['name' => '添加分类', 'uri' => '/product/category_add', 'show' => false, 'method' => 'get|post'],
                'category_del' => ['name' => '删除分类', 'uri' => '/product/category_del', 'show' => false, 'method' => 'get|post'],

            ],
            'show' => true,
        ],

        'user' => [
            'name' => '用户管理',
            'sub' => [
                'index' => ['name' => '用户列表', 'uri' => '/user/index', 'show' => true, 'method' => 'get'], // show 在菜单中是否显示
                'useradd' => ['name' => '添加/编辑用户', 'uri' => '/user/useradd', 'show' => false, 'method' => 'get|post'],
                'userrecharge' => ['name' => '充值', 'uri' => '/user/userrecharge', 'show' => false, 'method' => 'get|post'],
                'usermoney' => ['name' => '团队效益', 'uri' => '/user/usermoney', 'show' => false, 'method' => 'get|post'],
                'userwallet' => ['name' => '我的直推', 'uri' => '/user/userwallet', 'show' => false, 'method' => 'get|post'],
                'userdel' => ['name' => '删除用户', 'uri' => '/user/userdel', 'show' => false, 'method' => 'get|post'],
                'toggle_status' => ['name' => '修改状态', 'uri' => '/base/toggle_status', 'show' => false, 'method' => 'post'],

                'head_auth' => ['name' => '头像认证', 'uri' => '/user/head_auth', 'show' => true, 'method' => 'get'],
                'head_status' => ['name' => '审核头像', 'uri' => '/user/head_status', 'show' => false, 'method' => 'get|post'],

                'user_auth' => ['name' => '身份认证', 'uri' => '/user/user_auth', 'show' => true, 'method' => 'get'],
                'user_auth_sta' => ['name' => '认证状态', 'uri' => '/user/user_auth_sta', 'show' => false, 'method' => 'get'],
                'user_auth_del' => ['name' => '认证删除', 'uri' => '/user/user_auth_del', 'show' => false, 'method' => 'get'],

                'user_skill' => ['name' => '技能认证', 'uri' => '/user/user_skill', 'show' => true, 'method' => 'get'],
                'user_skill_sta' => ['name' => '技能状态', 'uri' => '/user/user_skill_sta', 'show' => false, 'method' => 'get'],
                'user_skill_del' => ['name' => '技能删除', 'uri' => '/user/user_skill_del', 'show' => false, 'method' => 'get'],

                'user_company' => ['name' => '企业认证', 'uri' => '/user/user_company', 'show' => true, 'method' => 'get'],
                 'user_company_sta' => ['name' => '认证状态', 'uri' => '/user/user_company_sta', 'show' => false, 'method' => 'get'],
                 'user_company_del' => ['name' => '认证删除', 'uri' => '/user/user_company_del', 'show' => false, 'method' => 'get'],

//                'team' => ['name' => '团队信息', 'uri' => '/user/team', 'show' => true, 'method' => 'get'],
//                'teamadd' => ['name' => '编辑团队', 'uri' => '/user/teamadd', 'show' => false, 'method' => 'get|post'],
//                'userteam' => ['name' => '上级用户', 'uri' => '/user/userteam', 'show' => false, 'method' => 'get|post'],
            ],
            'show' => true, //是否打开这个功能
        ],
        'money' => [
            'name' => '财务管理',
            'sub' => [
                'moneyprofit' => ['name' => '用户收益', 'uri' => '/user/moneyprofit', 'show' => true, 'method' => 'get|post'],
                'teamprofit' => ['name' => '代理收益', 'uri' => '/user/teamprofit', 'show' => true, 'method' => 'get'],
                'money_apply' => ['name' => '提现申请', 'uri' => '/user/money_apply', 'show' => true, 'method' => 'get|post'],
            ],
            'show' => true,
        ],
///        'coins' => [
//            'name' => '价格走势',
//            'sub' => [
//                'price' => ['name' => '价格走势', 'uri' => '/coins/price', 'show' => true, 'method' => 'get'],
//            ],
//            'show' => true,
//        ],

        'shop' => [
            'name' => '商城管理',
            'sub' => [
                'product_list' => ['name' => '商城列表', 'uri' => '/product/product_list', 'show' => true, 'method' => 'get'],
                'product_add' => ['name' => '编辑商品', 'uri' => '/product/product_add', 'show' => false, 'method' => 'get|post'],
                'product_del' => ['name' => '删除商品', 'uri' => '/product/product_del', 'show' => false, 'method' => 'get|post'],

                'order_list' => ['name' => '订单列表', 'uri' => '/product/order_list', 'show' => true, 'method' => 'get'],
                'order_status' => ['name' => '编辑发货', 'uri' => '/product/order_status', 'show' => false, 'method' => 'get|post'],


            ],
            'show' => true,
        ],
        'information' => [
            'name' => '资讯管理',
            'sub' => [
                'information' => ['name' => '资讯列表', 'uri' => '/info/information', 'show' => true, 'method' => 'get|post'],
                'information_add' => ['name' => '编辑资讯', 'uri' => '/info/information', 'show' => false, 'method' => 'get|post'],
                'information_del' => ['name' => '删除资讯', 'uri' => '/info/information_del', 'show' => false, 'method' => 'get|post'],
                'activity' => ['name' => '活动报名', 'uri' => '/info/activity', 'show' => true, 'method' => 'get|post'],
                'activity_del' => ['name' => '删除报名', 'uri' => '/info/activity_del', 'show' => false, 'method' => 'get|post'],
            ],
            'show' => true,
        ],

        'message' => [
            'name' => '帮助中心',
            'sub' => [
                'infolist' => ['name' => '信息发布', 'uri' => '/info/infolist', 'show' => true, 'method' => 'get|post'],
                'infolist_add' => ['name' => '编辑信息', 'uri' => '/info/infolist_add', 'show' => false, 'method' => 'get|post'],
                'info_del' => ['name' => '删除信息', 'uri' => '/info/info_del', 'show' => false, 'method' => 'get|post'],

                'kefu' => ['name' => '客服中心', 'uri' => '/system/kefu', 'show' => true, 'method' => 'get'],
                'kefu_add' => ['name' => '添加客服', 'uri' => '/system/kefu_add', 'show' => false, 'method' => 'get|post'],
                'kefu_del' => ['name' => '删除客服', 'uri' => '/system/kefu_del', 'show' => false, 'method' => 'get|post'],

                'message' => ['name' => '反馈管理', 'uri' => '/system/message', 'show' => true, 'method' => 'get|post'],
                'message_add' => ['name' => '编辑回复', 'uri' => '/system/message_add', 'show' => false, 'method' => 'get|post'],
                'message_del' => ['name' => '删除反馈', 'uri' => '/system/message_del', 'show' => false, 'method' => 'get|post'],

//                'usual' => ['name' => '常见问题', 'uri' => '/system/usual', 'show' => true, 'method' => 'get|post'],
//                'usual_add' => ['name' => '编辑问题', 'uri' => '/system/usual_add', 'show' => false, 'method' => 'get|post'],
//                'usual_del' => ['name' => '删除问题', 'uri' => '/system/usual_del', 'show' => false, 'method' => 'get|post'],
//                'message_edit' => ['name' => '添加问答', 'uri' => '/system/message_edit', 'show' => false, 'method' => 'get|post'],

            ],
            'show' => true,
        ],
        'admin' => [
            'name' => '系统管理',
            'sub' => [
                'operation' => ['name' => '操作日志', 'uri' => '/system/operation', 'show' => true, 'method' => 'get'],
                'index' => ['name' => '管理员列表', 'uri' => '/admin/index', 'show' => true, 'method' => 'get'],
                'input' => ['name' => '添加管理员', 'uri' => '/admin/input', 'show' => false, 'method' => 'get|post'],
                'banner' => ['name' => '轮播图', 'uri' => '/system/banner', 'show' => true, 'method' => 'get|post'],
                'banner_add' => ['name' => '添加轮播图', 'uri' => '/system/banner_add', 'show' => false, 'method' => 'get|post'],
                'banner_del' => ['name' => '删除轮播图', 'uri' => '/system/banner_del', 'show' => false, 'method' => 'get|post'],

                'recommend' => ['name' => '推荐背景', 'uri' => '/system/recommend', 'show' => true, 'method' => 'get|post'],
                'recommend_add' => ['name' => '添加背景', 'uri' => '/system/recommend_add', 'show' => false, 'method' => 'get|post'],
                'recommend_del' => ['name' => '删除背景', 'uri' => '/system/recommend_del', 'show' => false, 'method' => 'get|post'],
                'config' => ['name' => '系统设置', 'uri' => '/system/config', 'show' => true, 'method' => 'get|post'],
                'about' => ['name' => '关于我们', 'uri' => '/system/about', 'show' => true, 'method' => 'get|post'],
            ],
            'show' => true,
        ],



    ]
];