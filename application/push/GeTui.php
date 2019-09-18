<?php
namespace app\push;
class GeTui
{
    private $host = 'http://sdk.open.api.igexin.com/apiex.htm';
    //测试
    private $appkey = '';
    private $appid = '';
    private $mastersecret = '';

    private function init()
    {
        $this->appid = '1CWCGFof0b7CQl062o9a5';
        $this->appkey = '8BPXRAY55yAA42MYWqpkX1';
        $this->mastersecret = 'yXbD8aX8Sa6ks2wuNGxSe3';
        $this->host = 'http://sdk.open.api.igexin.com/apiex.htm';

    }

    public function __construct()
    {
        $this->init();
        $this->__loader();
    }

    private function __loader()
    {
        require_once(dirname(__FILE__) . '/' . 'IGt.Push.php');
        require_once(dirname(__FILE__) . '/' . 'igetui/IGt.AppMessage.php');
        require_once(dirname(__FILE__) . '/' . 'igetui/IGt.TagMessage.php');
        require_once(dirname(__FILE__) . '/' . 'igetui/IGt.APNPayload.php');
        require_once(dirname(__FILE__) . '/' . 'igetui/template/IGt.BaseTemplate.php');
        require_once(dirname(__FILE__) . '/' . 'IGt.Batch.php');
        require_once(dirname(__FILE__) . '/' . 'igetui/utils/AppConditions.php');
        require_once(dirname(__FILE__) . '/' . 'igetui/template/notify/IGt.Notify.php');
        require_once(dirname(__FILE__) . '/' . 'igetui/IGt.MultiMedia.php');
        require_once(dirname(__FILE__) . '/' . 'payload/VOIPPayload.php');
    }

    //服务端推送接口，支持三个接口推送
    //1.PushMessageToSingle接口：支持对单个用户进行推送
    //2.PushMessageToList接口：支持对多个用户进行推送，建议为50个用户
    //3.pushMessageToApp接口：对单个应用下的所有用户进行推送，可根据省份，标签，机型过滤推送
    //单推接口案例
    function pushMessageToSingle($cid,$payload,$type){
        $igt = new \IGeTui($this->host,$this->appkey,$this->mastersecret);

        //消息模版：

        if($type == 1){
            //通知栏消息模板
            $template = $this->IGtNotificationTemplateDemo($payload);
        }else{
            // 穿透消息模板
             $template = $this->IGtTransmissionTemplateDemo($payload);
        }



        //定义"SingleMessage"
        $message = new \IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        //$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送
        //接收方
        $target = new \IGtTarget();
        $target->set_appId($this->appid);
        $target->set_clientId($cid);
        //    $target->set_alias(Alias);

        try {
            $rep = $igt->pushMessageToSingle($message, $target);
        }catch(RequestException $e){
            $requstId =$e.getRequestId();
            //失败时重发
            $rep = $igt->pushMessageToSingle($message, $target,$requstId);
        }
    }

    //穿透消息模板
    public function IGtTransmissionTemplateDemo($payload = []){
        $template =  new \IGtTransmissionTemplate();
        $template->set_appId($this->appid); //应用appid
        $template->set_appkey($this->appkey); //应用appkey
        //透传消息类型
        $template->set_transmissionType(2);

        //透传内容
        $template->set_transmissionContent(json_encode($payload));
        // $template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //这是老方法，新方法参见iOS模板说明(PHP)*/
        //$template->set_pushInfo("actionLocKey","badge","message",
        //"sound","payload","locKey","locArgs","launchImage");

        //  APN高级推送
        // $apn = new \IGtAPNPayload();
        // $alertmsg=new \DictionaryAlertMsg();
        // $alertmsg->body="body";
        // $alertmsg->actionLocKey="ActionLockey";
        // $alertmsg->locKey="LocKey";
        // $alertmsg->locArgs=array("locargs");
        // $alertmsg->launchImage="launchimage";
        return $template;
    }

    //通知栏消息 (通知栏显示 点击启动应用)
    function IGtNotificationTemplateDemo($payload){
        $template =  new \IGtNotificationTemplate();
        $template->set_appId($this->appid); //应用appid
        $template->set_appkey($this->appkey); //应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($payload['title']);//透传内容
        $template->set_title($payload['title']);//通知栏标题
        $template->set_text($payload['content']);//通知栏内容
        $template->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }

    //群推接口案例
    public function pushMessageToApp(){
        $igt = new \IGeTui($this->host,$this->appkey,$this->mastersecret);

        $template = $this->IGtNotificationTemplateDemo();
        //个推信息体
        //基于应用消息体
        $message = new \IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);
        //    $message->setPushTime("201808011537");
        $appIdList=array($this->appid);
        $phoneTypeList=array('ANDROID');
        $provinceList=array('上海');
        $tagList=array('中文');
        $age = array("0000", "0010");

        //推送条件
        // $cdt = new \AppConditions();
        // 手机类型
        // $cdt->addCondition(\AppConditions::PHONE_TYPE, $phoneTypeList);
        // 地区
        // $cdt->addCondition(\AppConditions::REGION, $provinceList);
        // 标签
        // $cdt->addCondition(\AppConditions::TAG, $tagList);
        // 年龄？
        // $cdt->addCondition("age", $age);
        // $message->set_conditions($cdt);

        $message->set_appIdList($appIdList);

        $rep = $igt->pushMessageToApp($message);

        var_dump($rep);
        echo ("<br><br>");
    }

    //通知栏显示 点击跳转url
    function IGtLinkTemplateDemo(){
        $template =  new \IGtLinkTemplate();
        $template ->set_appId($this->appid);//应用appid
        $template ->set_appkey($this->appkey);//应用appkey
        $template ->set_title("测试群发消息");//通知栏标题
        $template ->set_text("点击就送66个老铁666");//通知栏内容
        $template ->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo
        $template ->set_isRing(true);//是否响铃
        $template ->set_isVibrate(true);//是否震动
        $template ->set_isClearable(true);//通知栏是否可清除
        $template ->set_url("http://www.igetui.com/");//打开连接地址
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }

}