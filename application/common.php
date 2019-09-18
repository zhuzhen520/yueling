<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
function get_config($type,$name=null){

    $result = array();
    $data = db('config')->where(array('type'=>$type))->select();
    if($data){
        foreach($data as $v){
            if(!empty($name)){
                if($v['name'] == $name){
                    $result = $v['value'];
                    break;
                }
            }else{
                $result[$v['name']] = $v['value'];
            }
        }
    }
    return $result;
}
// 应用公共文件
#无级分类（位置排序）
function category_merge($data,$parent_id=0,$level=0,$pk='cat_id'){
    $res = array();
    foreach($data as $v){
        #一级分类
        if($v['parent_id'] == $parent_id){
            $v['level'] = $level;
            $res[] = $v;
            #寻找自己下级
            $res = array_merge($res,category_merge($data,$v[$pk],$level+1,$pk));
        }
    }
    return $res;
}
#无级分类（位置排序）
function city_merge($data,$parent_id=0,$level=0,$pk='id'){
    $res = array();
    foreach($data as $v){
        #一级分类
        if($v['pid'] == $parent_id){
            $v['level'] = $level;
            $res[] = $v;
            #寻找自己下级
            $res = array_merge($res,city_merge($data,$v[$pk],$level+1,$pk));
        }
    }
    return $res;
}

#无级分类（位置排序）
function city_merge_other($data,$parent_id=0,$level=0,$pk='id'){
    $res = array();
    foreach($data as $key=> $v){
        #一级分类
        if($v['pid'] == $parent_id){
            $v['level'] = $level;
            $res[]= $v;
            #寻找自己下级
            $res = array_merge($res,city_merge($data,$v[$pk],$level+1,$pk));
        }
    }
    return $res;
}

#查询子分类cat_id
function get_children($data,$cat_id,$pk='cat_id'){
    $res = array();
    foreach($data as $v){
        #指定级
        if($v['parent_id'] == $cat_id){
            $res[] = $v[$pk];
            #寻找下级分类
            $res = array_merge($res,get_children($data,$v[$pk],$pk));
        }
    }
    return $res;
}

/**
 * 币种类型
 * @param $type
 * @return string
 * 红：255，0，0    #FF0000
 * 橙: 255,125,0    #FF7D00
 * 黄：255，255，0   #FFFF00
 * 绿：0，255，0    #00FF00
 * 蓝：0，0，255    #0000FF
 * 靛: 0,255,255    #00FFFF
 * 紫: 255,0,255    #FF00FF
 */
function currencytype($type){
     switch($type){
         case 1:
             $typename = '余额';
             break;
         case 2:
             $typename = '优惠卷';
             break;
         case 3:
             $typename = '豆豆';
             break;
         default:
             $typename = '其他';
     }
    return $typename;
}

/**
 * 收益类型
 * @param $type
 * @return string
 * 红：255，0，0    #FF0000
 * 橙: 255,125,0    #FF7D00
 * 绿：0，255，0    #00FF00
 * 蓝：0，0，255    #0000FF
 * 靛: 0,255,255    #00FFFF
 * 紫: 255,0,255    #FF00FF
 */
function actiontype($type){
     switch($type){
         case 1:
             $typename = '会员返豆';
             break;
         case 2:
             $typename = '直推奖励';
             break;
         case 3:
             $typename = '间推奖励';
             break;
         case 4:
             $typename = '商品购买';
             break;
         case 5:
             $typename = '购买会员';
             break;
         case 6:
             $typename = '豆转余额';
             break;
         case 7:
             $typename = '诚意金';
             break;
         case 8:
             $typename = '提现驳回';
             break;
         case 9:
             $typename = '提现申请';
             break;
         case 10:
             $typename = '邀请好友';
             break;
         case 11:
             $typename = '预约扣除';//-------------
             break;
         case 12:
             $typename = '商品兑换';
             break;
         case 13:
             $typename = '系统操作';
             break;
         case 14:
             $typename = '下级接单';
             break;
         case 15:
             $typename = '接单收益';
             break;
         case 16:
             $typename = '约单退款';
             break;
         case 17:
             $typename = '返还诚意金';
             break;
         case 18:
             $typename = '省代收益';
             break;
         case 19:
             $typename = '县代收益';
             break;
         case 20:
             $typename = '区代收益';
             break;
         case 21:
             $typename = '充值';
             break;
         case 22:
             $typename = '未接单处罚';
             break;
         case 23:
             $typename = '用户认证';
             break;
         case 24:
             $typename = '约单扣除';
             break;
         case 25:
             $typename = '订单加价';
             break;
         case 26:
             $typename = '订单减价';
             break;
         default:
             $typename = '其他';
     }
    return $typename;
}

/**
 * 省市县区类型
 * @param $type
 * @return string
 * 红：255，0，0    #FF0000
 * 橙: 255,125,0    #FF7D00
 * 绿：0，255，0    #00FF00
 * 蓝：0，0，255    #0000FF
 * 靛: 0,255,255    #00FFFF
 * 紫: 255,0,255    #FF00FF
 */

function leveltype($type){
     switch($type){
         case 1:
             $typename = '省代';
             break;
         case 2:
             $typename = '市代';
             break;
         case 3:
             $typename = '县/区代';
             break;
         default:
             $typename = '普通用户';
     }
    return $typename;
}

//<span class="label label-default">默认标签</span>
//<span class="label label-primary">主要标签</span>
//<span class="label label-success">成功标签</span>
//<span class="label label-info">信息标签</span>
//<span class="label label-warning">警告标签</span>
//<span class="label label-danger">危险标签</span>
function release($type){
    switch($type){
        case 'extens':
            $typename = '推广描述';
            break;
        case 'about':
            $typename = '关于我们';
            break;
        case 'notice':
            $typename = '公告';
            break;
        case 'star':
            $typename = '约邻之星';
            break;
        case 'strategy':
            $typename = '赚钱攻略';
            break;
        case 'vip':
            $typename = '会员协议';
            break;
        case 'manual':
            $typename = '使用手册';
            break;
        case 'recharge':
            $typename = '充值协议';
            break;
        default:
            $typename = '<span class="label label-warning radius">其他</span>';
    }
    return $typename;
}

function unittype($type){
    switch($type){
        case 1:
            $typename = '次';
            break;
        case 2:
            $typename = '小时';
            break;
        case 3:
            $typename = '天';
            break;
        default:
            $typename = '0';
    }
    return $typename;
}

//0未发布  1竞单中 2 进行中  3待评价  4 已完成  5 已失效
function receipttype($type){
    switch($type){
        case 0:
            $typename = '未发布';
            break;
        case 1:
            $typename = '待接单';
            break;
        case 2:
            $typename = '进行中';
            break;
        case 3:
            $typename = '待评价';
            break;
        case 4:
            $typename = '已完成';
            break;
        case 5:
            $typename = '已失效';
            break;
        case 6:
            $typename = '申诉中';
            break;
        default:
            $typename = '未发布';
    }
    return $typename;
}

function generate_username( $length = 6 ) {
    // 密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*-_+=|';
    $password = '';
    for ( $i = 0; $i < $length; $i++ )
    {
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $password;
}


