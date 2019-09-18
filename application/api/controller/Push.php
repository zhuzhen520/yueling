<?php
namespace  app\api\controller;
use app\push\GeTui;
use think\facade\Request;

class Push
{
    //个推案例接口
    public function getui($cid, $content)
    {
        if(empty($cid) || empty($content)){
            return false;
        }
        //实例化之前写的类
        $getui = new GeTui();
        //单发测试 $cid 客户端id 前端获取
        $payload = [
            'title' => '约邻',
            'content' => $content,
            'payload' => '参数'
        ];
//         $getui->pushMessageToSingle($cid,$payload,1);  //1通知
         $getui->pushMessageToSingle($cid,$payload,2); // 2穿透
//         $getui->pushMessageToSingle('d2156398bd4f421148f602f6a860a314');
        // 群发测试
//        $getui->pushMessageToApp();
//         dump($getui);die;
    }

}