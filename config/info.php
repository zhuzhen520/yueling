<?php

/**
 * select 下接选择项
 * type  类型    搜索值
 * name  名称    类型名称
 * status 唯一值    1   发布唯一状态   0 不限发布数量
 * 修改完后  修改common.php 文件
 */

return [
    'info' => [   #信息发布选择
        [
            'type'=>'recharge',
            'name'=>'充值协议',
            'status'=> 1
        ],
        [
            'type'=>'about',
            'name'=>'关于我们',
             'status'=> 1
        ],
        [
            'type'=>'star',
            'name'=>'约邻之星',
            'status'=> 1
        ],
        [
            'type'=>'strategy',
            'name'=>'赚钱攻略',
            'status'=> 1
        ],
        [
            'type'=>'vip',
            'name'=>'会员协议',
            'status'=> 1
        ],
        [
            'type'=>'manual',
            'name'=>'使用手册',
            'status'=> 1
        ],
        [
            'type'=>'notice',
            'name'=>'系统公告',
            'status'=>0
        ],
    ],
    'infomation'=>[    #资讯发布选择
        [
            'type'=>2,
            'name'=>'约邻资讯'
        ], [
            'type'=>3,
            'name'=>'约邻学院'
        ], [
            'type'=>4,
            'name'=>'活动'
        ],
    ],
    'ordertype'=>[    #订单发货类型选择
        [
            'type'=>1,
            'name'=>'未发货'
        ], [
            'type'=>2,
            'name'=>'已发货'
        ], [
            'type'=>3,
            'name'=>'已收货'
        ],
    ],
    'paytype'=>[    #订单支付类型选择
        [
            'type'=>1,
            'name'=>'未付款'
        ], [
            'type'=>2,
            'name'=>'已付款'
        ],
    ],
    'authentication'=>[    #认证类型选择
        [
            'type'=>2,
            'name'=>'已驳回'
        ], [
            'type'=>1,
            'name'=>'已认证'
        ], [
            'type'=>3,
            'name'=>'未审核'
        ],
    ],
    'receipt'=>[    #认证类型选择
        [
            'type'=>0,
            'name'=>'未发布'
        ], [
            'type'=>1,
            'name'=>'竞单中'
        ], [
            'type'=>2,
            'name'=>'进行中'
        ], [
            'type'=>3,
            'name'=>'待评价'
        ],[
            'type'=>4,
            'name'=>'已完成'
        ], [
            'type'=>6,
            'name'=>'未成交'
        ],[
            'type'=>7,
            'name'=>'待接单'
        ],
    ],


];