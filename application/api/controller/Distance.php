<?php
/**
 * Class Distance
 * 距离
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
namespace app\api\controller;


use think\Db;
use think\facade\Env;

class Distance extends Base
{

    /**
     * 更新用户所在位置
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function joinAddress(){
        $lng= input('lng');
        $lat= input('lat');
        if(!is_numeric($lng) || !is_numeric($lat)){
            return $this->fail('经纬度错误',false);
        }
        $user = Db::name('user')->where(['id'=>$this->request->user_id])->update(['lng'=>$lng,'lat'=>$lat]);
        return $this->succ($user);
    }

    /**
     * 求两个已知经纬度之间的距离,单位为米
     * @param $lng1
     * @param $lat1
     * @param $lng2
     * @param $lat2
     * @return float|int
     */
    function getdistance($lng1, $lat1, $lng2, $lat2) {
        if($lng1 >0 && $lng2 >0){
            // 将角度转为狐度
            $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
            $radLat2 = deg2rad($lat2);
            $radLng1 = deg2rad($lng1);
            $radLng2 = deg2rad($lng2);
            $a = $radLat1 - $radLat2;
            $b = $radLng1 - $radLng2;
//        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
            $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137*1000;
            return $s;
        }else{
            return 0;
        }

    }


    /**
     * 地址获取经纬度
     * @param $address
     * @param $city
     * @return void|null
     */
    function coordinate($address,$city)
    {
        $result = array();
        $ak = Env::get('dist.BAIDU_Key');//您的百度地图ak，可以去百度开发者中心去免费申请
        $url ="http://api.map.baidu.com/geocoder/v2/?callback=renderOption&output=json&address=".$address.$city."&ak=".$ak;
        $data = file_get_contents($url);
        $data = str_replace('renderOption&&renderOption(', '', $data);
        $data = str_replace(')', '', $data);
        $data = json_decode($data,true);
        if (!empty($data) && $data['status'] == 0) {
            $result['lat'] = $data['result']['location']['lat'];
            $result['lng'] = $data['result']['location']['lng'];
            return $result;//返回经纬度结果
        }else{
            return null;
        }

    }





}