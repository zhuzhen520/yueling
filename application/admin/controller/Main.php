<?php

namespace app\admin\controller;

use app\models\Admin;
use think\Db;

/**
 * Class Main
 * @package app\admin\controller
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
class Main extends Base
{
    public function index()
    {
        $admin = $this->request->admin;
        $menu = config('menu.menu');
        $arr = [];
        foreach ($menu as $k => $v) {
            if ($v['show'] == true) {
                foreach ($v['sub'] as $x => $y) {
                    if (($y['show'] == true && in_array($y['uri'], $admin['role'])) || $admin['is_default'] == 1) {
                        $arr[$k]['name'] = $v['name'];
                        $arr[$k]['sub'][$x] = $y;
                    }
                }
            }
        }
        return view(null, [
            'admin' => $admin,
            'menu' => $arr,
        ]);
    }

    public function welcome()
    {
        $row['money'] = Db::name('money_profit')->where(['currency'=>1,'type'=>1])->sum('action');
        $row['td_money'] = Db::name('money_profit')->where(['currency'=>2,'type'=>1])->sum('action');
        $row['tx_money'] = Db::name('money_apply')->where(['status'=>2])->sum('money') * get_config('base','usdt')/100;
        $row['shop_num'] = Db::name('order')->count();
        $row['user'] = Db::name('user')->count();
        $this->assign('row',$row);
        return view(null, [
            'admin' => $this->request->admin
        ]);
    }

    public function miss()
    {
        return view('404');
    }

    /**
     * 信息统计接口数据
     * @return \think\response\Json
     */
    public function infoData(){
        //X轴
        $x_axis = $this->get_weeks();
//        未定义收益表
        $infodata = [];
        foreach($x_axis  as $datetime){
//            //获取今日数据
            $start = strtotime(date('Y').'-'.$datetime.' 00:00:00');
            $end   = strtotime(date('Y').'-'.$datetime.' 23:59:59');
            $infodata['money'][] = Db::name('money_profit')->where(['currency'=>1])->where('add_time','between',[$start,$end])->sum('action');
            $infodata['cz_money'][] = Db::name('money_profit')->where(['currency'=>3])->where('add_time','between',[$start,$end])->sum('action');
//            $infodata['矿钻数量'][] = Db::name('收益表')->where('created','between',[$start,$end])->sum('字段');
//            $infodata['余币宝数量'][] = Db::name('收益表')->where('created','between',[$start,$end])->sum('字段');
        }
        //折线图名称
        $line_name  = '收益统计';
        //Y轴解释
        $y_name = '数量 (个)';
        //数据
        //名称1  数组
        $info1['name'] = '余额';   //线条名称
        $info1['num'] =  $infodata['money'];   //七天数据

        //名称 2  数组
        $info2['name'] = '豆豆';   //线条名称
        $info2['num'] = $infodata['cz_money'];   //七天数据

//        //名称3  数组
//        $info3['name'] = '矿钻数量';   //线条名称
//        $info3['num'] = [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6];   //七天数据
//
//        //名称4  数组
//        $info4['name'] = '余币宝数量';   //线条名称
//        $info4['num'] = [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0];   //七天数据

        $data = [
            'x_axis'=>array_values($x_axis),
            'line_name'=>$line_name,
            'y_name'=>$y_name,
            'info1'=>$info1,
            'info2'=>$info2,
//            'info3'=>$info3,
//            'info4'=>$info4,
        ];
        return json(['info'=>$data]);
    }

    /**
     * 时间轴拆线图    两条统计数据
     * @return \think\response\Json
     */
    public function statisticInfo(){
        $line_name = '用户统计';
        //X轴
        $x_axis = $this->get_weeks();
        $infodata = [];
        foreach($x_axis  as $datetime){
            //获取今日数据
            $start = date('Y').'-'.$datetime.' 00:00:00';
            $end   = date('Y').'-'.$datetime.' 23:59:59';
            $infodata['day_num'][] = Db::name('user')->where('created','between',[$start,$end])->count();
            $infodata['all_num'][] = Db::name('user')->where('created','between',[0,$end])->count();
        }
        //Y轴解释
        $y_name = '数量 (个)';

        //名称1  数组
        $info1['name'] = '用户统计';   //线条名称
        $info1['num'] = $infodata['all_num'];   //七天数据
        //名称 2  数组
        $info2['name'] = '新增用户';   //线条名称
        $info2['num'] = $infodata['day_num'];   //七天数据

        $data = [
            'x_axis'=>array_values($x_axis),
            'line_name'=>$line_name,
            'y_name'=>$y_name,
            'info1'=>$info1,
            'info2'=>$info2,
        ];
        return json(['info'=>$data]);
    }
    /**
     * 获取最近七天所有日期
     */
    function get_weeks($time = '', $format='m-d'){
        $time = $time != '' ? $time : time();
        //组合数据
        $date = [];
        for ($i=1; $i<=7; $i++){
            $date[$i] = date($format ,strtotime( '+' . $i-7 .' days', $time));
        }
        return $date;
    }
}