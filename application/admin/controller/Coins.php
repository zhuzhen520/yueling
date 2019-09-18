<?php

namespace app\admin\controller;

use app\models\CoinsPrice;

/**
 * Class Coins
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
class Coins extends Base
{
    public function price()
    {
        return view('');
    }

    public function input()
    {
        $model = new CoinsPrice();
        $date = $this->request->post('date');
        $price = $this->request->post('price');
        if (!$date || !$price) {
            return json(['status' => 1, 'msg' => '请填入正确信息']);
        }
        if ((new CoinsPrice)->where('date', $date)->find()) {
            $res = $model->where('date', $date)->data(['price' => $price])->update();
        } else {
            $data['price'] = $price;
            $data['date'] = $date;
            $res = $model->save($data);
        }
        if ($res) {
            return json(['status' => 0]);
        }
    }

    public function getm()
    {
        $model = new CoinsPrice();
        $start = date('Y-m-d', strtotime($this->request->post('date')));
        $end = date('Y-m-d',strtotime($this->request->post('date'). '+1month -1day'));
        $results = $model->where("date >= '{$start}' and date <= '{$end}'")->select();
        $arr = [];
        foreach ($results as $result) {
            $arr[] = ['price' => $result['price'], 'day' => $result['date']];
        }
        return json($arr);
    }
}