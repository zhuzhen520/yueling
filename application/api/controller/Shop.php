<?php
namespace app\api\controller;
use think\facade\Env;
use think\loader;
use think\Log;
use think\Db;
use think\Request;
use think\Validate;
use phpass;

/**
* 商城
*/
class Shop extends Base
{


    /**
     * 商城列表
     * @throws \think\exception\DbException
     */
    public function shoplist()
    {
        $page = input('page');
        if (!is_numeric($page)) {
            return $this->fail('错误操作', false);
        }
        $online['shop'] = Db::name('product')
            ->order('is_hot desc')
            ->where(['is_sale' => 1])#是否上架
            ->page($page, Env::get('app.page'))
            ->field('id,thumb,name,shop_price')
            ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
            ->each(function ($item) {
                $item['shop_price'] = floatval($item['shop_price']);
                return $item;
            });;
        return $this->succ($online);
    }

    /**
     * 商品详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function shopdetails()
    {
        $shopid = input('id');
        if (!is_numeric($shopid)) {
            return $this->fail('错误操作', false);
        }
        $online = Db::name('product')->where(['id' => $shopid])->field('thumb,name,shop_price,content,stock,freight')->find();
        if (!$online) {
            return $this->fail('错误操作', false);
        }
        $online['shop_price'] = floatval($online['shop_price']);;
        return $this->succ($online);
    }


    public function joinorder()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rule = [
                'shopid' => 'require|number',
                'num' => 'require|number',
                'address' => 'require|number',
            ];
            $msg = [
                'shopid.require' => '错误操作',
                'shopid.number' => '错误操作',
                'num.require' => '错误操作',
                'num.number' => '错误操作',
                'address.require' => '错误操作',
                'address.number' => '错误操作',
            ];
            $data = [
                'shopid' => $data['shopid'],
                'num' => $data['num'],
                'address' => $data['address'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return $this->fail($validate->getError(), false);
            }
            $shop = Db::name('product')->where(['id' => $data['shopid']])->find();
            if (empty($shop)) {
                return $this->fail('未找到该商品', false);
            }
            if ($shop['stock'] < intval($data['num'])) {
                return $this->fail('库存不足!', false);
            }
            $address = Db::name('user_address')->where(['id'=>$data['address'],'user_id'=>$this->request->user_id])->find();
            if(empty($address)){
                return $this->fail('地址不存在!', false);
            }
            Db::startTrans();
            try {

                $info = [
                    'order_sn' => time(),
                    'user_id' => $this->userinfo()['id'],
                    'username' => $this->userinfo()['mobile'],
                    'province' => $address['province'],
                    'city' => $address['city'],
                    'area' => $address['area'],
                    'address' => $address['address'],
                    'addressid' => $data['address'],
                    'name' => '',
                    'mobile' => '',
                    'pay' => $shop['pay_type'],
                    'freight' => $shop['freight'],
                    'short_name' => $shop['short_name'],
                    'add_time' => time(),
                    'pay_time' => time(),
                    'pay_state' => 0,
                    'shipping_state' => 0,
                    'price' => $shop['shop_price'],
                    'num' => $data['num'],
                    'product_id' => $shop['id'],
                    'all_price' => $data['num'] * $shop['shop_price'],
                ];
                // 加入订单
                $orderid = Db::name('order')->insertGetId($info);

                // 提交事务
                Db::commit();
                return $this->succ(['orderid'=>$orderid]);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            return $this->fail(['orderid'=>0],false);
        } else {
            $shopid = input('orderid');
            $address = Db::name('user_address')->field('id,province,city,area,address,name,mobile')->where(['user_id'=>$this->request->user_id])->find();

            if (!is_numeric($shopid)) {
                return $this->fail('错误操作', false);
            }
            $order = Db::name('order')->field('order_sn,shipping_state,pay_state,order_id,product_id,add_time,pay,price,all_price,pay,content,short_name,num')->where(['order_id'=>$shopid])->find();

            $online['thumb'] = Db::name('product')->where(['id' => $order['product_id']])->value('thumb');
            $online['time'] = date('Y-m-d H:i:s', $order['add_time']);
            //扣除用户支付类型
            switch ($order['pay']){
                case 1:
                    $moneytype = 'money';   //余额
                    break;
                case 2:
                    $moneytype = 'td_money';   //优惠卷
                    break;
                case 3:
                    $moneytype = 'cz_money';   //豆豆
                    break;
                default:
                    return $this->fail('商品支付类型错误',false);
                    break;
            }
            $online['usermoney'] = $this->userinfo()[$moneytype];
            $online['price'] = floatval($order['price']);
            $online['content'] =$order['content'];
            $online['num'] =$order['num'];
            $online['short_name'] =$order['short_name'];
            $online['all_price'] = floatval($order['price'] * $order['num']);
            $online['address'] = $address;
            $online['shopid'] = $order['order_id'];
            $online['pay_state'] = $order['pay_state'];
            $online['shipping_state'] = $order['shipping_state'];
            $online['order_sn'] = $order['order_sn'];
            return $this->succ($online);

        }
    }

    /**
     * 支付
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function payOrder(){
        if($this->request->isPost()){
            $data = $this->request->param();
            $content = $data['content'];
            $rule = [
                'addressid'   => 'require|number',
                'shopid'      => 'require|number',
                'num'         => 'require|number',
                'trade'       => 'require',
            ];
            $msg = [
                'addressid.require'  => '请填写收货地址',
                'addressid.number'   => '错误操作',
                'shopid.require'     => '错误操作',
                'shopid.number'      => '错误操作',
                'num.require'        => '错误操作',
                'num.number'         => '错误操作',
                'trade.require'      => '请输入交易密码',
            ];
            $data = [
                'shopid'      => $data['shopid'],
                'addressid'   => $data['addressid'],
                'num'         => $data['num'],
                'trade'       => $data['trade'],
            ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result){
                return $this->fail($validate->getError(),false);
            }
            $address = Db::name('user_address')->where(['user_id'=>$this->request->user_id,'id'=>$data['addressid']])->find();
            if(empty($address)){
                return $this->fail('错误操作',false);
            }
            $shop = Db::name('order')->where(['order_id'=>$data['shopid']])->find();
            if(empty($shop)){
                return $this->fail('未找到该商品',false);
            }
            if($shop['pay_state'] == 1){
                return $this->fail('该订单不需要支付',false);
            }
            $product = Db::name('product')->where(['id'=>$shop['product_id'],'is_sale'=>1])->find();
            if(empty($product)){
                return $this->fail('未找到该商品或已经下架',false);
            }
            $user = Db::name('user')->where(['id'=>$this->request->user_id])->find();
            if(empty($user['trade'])){
                return $this->fail('请先设置交易密码',false);
            }
            $checkPassword = $this->checkPassword($data['trade'],2, $user['trade']);
            if(!$checkPassword){
                return $this->fail('交易密码错误',false);
            }
            Db::startTrans();
            try{
                //扣除用户支付类型
                switch ($shop['pay']){

                    case 1:
                        $moneytype = 'money';   //余额
                        break;
                    case 2:
                        $moneytype = 'td_money';   //优惠卷
                        break;
                    case 3:
                        $moneytype = 'cz_money';   //豆豆
                        break;
                    default:
                        return $this->fail('商品支付类型错误',false);
                        break;
                }

                // 未扣除运费 + floatval($shop['freight']
                $all_money = floatval($data['num'] * $shop['price']);
                  if($all_money <= 0 || intval($data['num']) <= 0){
                      return $this->fail('商品错误',false);
                  }
                  if($user[$moneytype] < $all_money){
                      return $this->fail(currencytype($shop['pay']).'不足!',false);
                  }
                  if($product['stock'] < intval($data['num'])){
                      return $this->fail('库存不足!',false);
                  }
                  Db::name('user')->where(['id'=>$this->request->user_id])->setDec($moneytype,$all_money);
                  $this->add_log($this->request->user_id,$shop['pay'],4,$all_money,0);

                  $info = [
                      'province'=>$address['province'],
                      'city'=>$address['city'],
                      'area'=>$address['area'],
                      'address'=>$address['address'],
                      'name'=>$address['name'],
                      'mobile'=>$address['mobile'],
                      'addressid'=>$data['addressid'],
                      'pay_time'=>time(),
                      'pay_state'=>1,
                      'shipping_state'=>0,
                      'price'=>$shop['price'],
                      'num'=>$data['num'],
                      'content'=>$content,
                      'all_price'=>$data['num'] * $shop['price'],
                  ];
                  // 加入订单
                  Db::name('order')->where(['order_id'=>$data['shopid']])->update($info);

                  // 提交事务
                  Db::commit();
                  return $this->succ('购买成功');
              } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            return $this->fail('购买失败，请重新购买',false);
        }else{
            $shopid = input('shopid');
            $num = input('num');
            if(!is_numeric($shopid) || !is_numeric($num)){
                return $this->fail('错误操作',false);
            }
            $online = Db::name('product')->where(['id'=>$shopid])->field('thumb,name,shop_price,freight,pay_type')->find();
            $online['num'] = floatval($num);
            $online['time'] = date('Y-m-d H:i:s',time());
            $online['all_price'] = floatval($online['shop_price'] *  $num) + floatval($online['freight']);
            return $this->succ($online);

        }
    }

      public function orderlist(){
          $page = input('page');
          $type = input('type');
          if(!is_numeric($type)){
              return $this->fail('错误操作',false);
          }
          if(!is_numeric($page)){
              return $this->fail('错误操作',false);
          }
          $db = Db::name('order');
          $where = [];
          if($type== 1){
//              $where['pay_state'] = 0;
              $db->where('pay_state','eq',0);
          }elseif($type == 2){
//              $where[] = ['pay_state',1];
//              $where[] = ['shipping_state',['neq',3]];
              $db->where('shipping_state','neq',2)->where('pay_state',1);
          }elseif($type == 3){
//              $where['pay_state'] = 1;
//              $where['shipping_state'] = 3;
              $db->where('shipping_state','eq',2)->where('pay_state',1);
          }
          $where['user_id']=$this->request->user_id;
          $join = [['tb_product b','b.id=a.product_id']];
          $online = $db
              ->order('order_id desc')
              ->alias('a')
              ->join($join)
              ->field('a.order_sn,a.addressid,a.order_id,a.pay_state,a.shipping_state,a.add_time,a.num,a.price,a.all_price,a.hair,a.name as aname,a.province,a.city,a.area,a.address,b.name as bname,b.thumb')
              ->page($page,Env::get('app.page'))
//              ->where($where)
              ->paginate(Env::get('app.page'), false, ['query' => request()->param()])
              ->each(function($item){
                  $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
                  return $item;
              });
          return $this->succ($online);
      }


    /**
     * 取消订单
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
      public function orderDel(){
         $order_id = input('order_id');
         $type = input('type');
         $order = Db::name('order')->where(['order_id'=>$order_id])->find();
         if($order){
             Db::startTrans();
             try{
                 if($type == 1){
                     if($order['pay_state'] == 1){
                         return $this->fail('不能取消已付款的订单',false);
                     }
                     Db::name('order')->where(['order_id'=>$order_id])->delete();
                     $msg = '取消成功';
                 }elseif($type == 2){
                     if($order['shipping_state'] == 0){
                         return $this->fail('订单未发货',false);
                     }
                     if($order['shipping_state'] == 1){
                         Db::name('order')->where(['order_id'=>$order_id])->update(['shipping_state'=>2]);
                         $msg = '确认成功';
                     }

                 }else{
                     $msg = '网络错误,请稍后再试...';
                 }
                 // 提交事务
                 Db::commit();
                 return $this->succ($msg);
             } catch (\Exception $e) {
                 // 回滚事务
                 Db::rollback();
             }
         }else{
             return $this->fail('未找到订单',false);
         }
      }



    /**
     * 编辑地址
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function addressedit(){
        if($this->request->isPost()){
            $data = $this->request->param();
            $type = $data['type'];
            $id = 0;
            $id = isset($data['id'])?$data['id']:0;
            if($type != 1){

                if(empty($id)){
                    return $this->fail('请填写地址编号',false);
                }
            }else{
                if($id >0){
                    return $this->fail('添加地址不需要id',false);
                }
            }

            $rule = [
//                'province'    => 'require',
//                'city'        => 'require',
//                'area'        => 'require',
                'address'     => 'require',
                'name'        => 'require',
                'mobile'      => 'require',
                'is_default'      => 'require',
            ];
            $msg = [
//                'province.require' => '省市必须',
//                'city.require'     => '城镇必须',
//                'area.require'     => '区域必须',
                'address.require'  => '地址必须',
                'name.require'     => '请填写收货人',
                'mobile.require'   => '请填写手机号',
                'is_default.require'   => '默认地址',
            ];
            $data = [
//                'province'     => $data['province'],
//                'city'         => $data['city'],
//                'area'         => $data['area'],
                'address'      => $data['address'],
                'name'         => $data['name'],
                'mobile'       => $data['mobile'],
                'is_default'       => $data['is_default'],
            ];
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result){
                return $this->fail($validate->getError(),false);
            }
            $data['area'] = isset($data['area'])?$data['area']:'';
            $data['province'] = isset($data['province'])?$data['province']:'';
            $data['city'] = isset($data['city'])?$data['city']:'';

            if($type == 1){  //添加
                unset($data['type']);
                $data['user_id'] = $this->request->user_id;
                $fun = Db::name('user_address')->where(['user_id'=>$data['user_id'],'address'=>$data['address'],'area'=>$data['area']])->find();
                if($fun){
                    return $this->fail('已存在相同的地址',false);
                }
               $res =  Db::name('user_address')->insert($data);
               $msg = '添加成功';
            }else{  //修改
                if($data['is_default']==1){
                    Db::name('user_address')->where(['user_id'=>$this->request->user_id])->update(['is_default'=>0]);
                }
               $res =  Db::name('user_address')->where(['id'=>$id])->update($data);
                $msg = '修改成功';
            }
            if($res){
                return $this->succ($msg);
            }else{
                return $this->fail('操作失败,请稍后再试..',false);
            }
        }else{
            $online = Db::name('user_address')->order('id desc')->where(['user_id'=>$this->request->user_id])->select();
            if($online){
                return $this->succ($online);
            }else{
                return $this->fail([],false);
            }

        }
    }

    public function addressDetails(){
        $id = input('id');
        if (empty($id) || !is_numeric($id)){
            return $this->fail('信息错误',false);
        }
        $online = Db::name('user_address')->order('id desc')->where(['id'=>$id])->find();
        if($online){
            return $this->succ($online);
        }else{
            return $this->fail([],false);
        }
    }

    public function addressDel(){
        $id = input('id');
        if (empty($id) || !is_numeric($id)){
            return $this->fail('信息错误',false);
        }
        $online = Db::name('user_address')->where(['user_id'=>$this->request->user_id])->delete($id);
        if($online){
            return $this->succ($online);
        }else{
            return $this->fail([],false);
        }
    }






}