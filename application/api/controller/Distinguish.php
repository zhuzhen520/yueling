<?php
namespace app\api\controller;


use app\helps\aip\AipFace;
use app\helps\pay\aop\AopClient;
use app\helps\pay\aop\request\AlipayTradeAppPayRequest;
use think\App;
use think\Db;
use Naixiaoxin\ThinkWechat\Facade;
use think\facade\Env;
use think\Validate;

/**
 * Class Distinguish
 * 人脸识别
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
class Distinguish extends Base
{
    public static function index($image,$reverse,$idCardNumber,$name){

         $AppID = Env::get('dist.AppID');
        $API_Key = Env::get('dist.API_Key');
        $Secret_Key = Env::get('dist.Secret_Key');
        $client = new AipFace($AppID, $API_Key, $Secret_Key);
        $result = $client->match([
            [
                'image' => base64_encode(file_get_contents($image)),
                'image_type' => 'BASE64',
            ],
            [
                'image' => base64_encode(file_get_contents($reverse)),
                'image_type' => 'BASE64',
            ],
        ]);
        return $result;
    }

    public function getToken(){
        $API_Key = Env::get('dist.API_Key');
        $Secret_Key = Env::get('dist.Secret_Key');
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $post_data['grant_type']     = 'client_credentials';
        $post_data['client_id']      = $API_Key;
        $post_data['client_secret'] = $Secret_Key;
        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v )."&" ;
        }
        $post_data = substr($o,0,-1);
       $res =  $this->request_post($url."?".$post_data);
       return $this->succ($res);
    }


//    function request_post($url = '', $param = '') {

        public function request_post($url)
        {
            $info = curl_init();
            curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($info,CURLOPT_HEADER,0);
            curl_setopt($info,CURLOPT_NOBODY,0);
            curl_setopt($info,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($info,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($info,CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($info,CURLOPT_URL,$url);
            $output = curl_exec($info);
            curl_close($info);
            return $output;
        }
}