<?php
namespace app\api\controller;


use app\helps\pay\aop\AopClient;
use app\helps\pay\aop\request\AlipayTradeAppPayRequest;
use think\Db;
use Naixiaoxin\ThinkWechat\Facade;
use think\facade\Env;
use think\Validate;

/**
 * Class Pay
 * 支付接口
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
class Pay extends Base
{
    /**
     * 支付宝支付
     * @param $order_no
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function payAliPay($order_no, $url)
    {
            $data['order_no'] = $order_no;
            $order = db('user_order')->where('order_no', $data['order_no'])->find();
            if ($order['status'] != 0) {
                return $this->fail('订单不存在',false);
            }
            try {
                $payData = [
                    'amount' => $order['money'],
                    'pay_no' => md5(time() . uniqid()),
                ];
                Db::name('user_order')->where('order_no', $data['order_no'])->update($payData);

                $aop = new AopClient();
                $aop->appId = '2019052265351002';
                $aop->rsaPrivateKey = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCcoLd2di5DkRXIwYzApNo+109tkTeMrlmbVjYny7i1S8bbjaWqtc/MwUkhuXjXLJS+5RE4m6sAywr8jrG8gesUXRQMFpI9RivXLSB0FDFOGsLzjdrlf0PpsRYAGyVpIzSqqeC8uRD8G0j0EZJEd2vAf5HZgSv8BpoG8+4Z+jaQwix9p+3KtWAn/MK+OkCdy0uO4LkN9DlXGpRcOxBTowmMGacfdnMrzJEQQLABN12YfqvBoLTTIAZZZ7e4HcdNn01lzdO4IVFBc/HCbRNleEtLQd6rTj54/lYzDkw5xBlaohu/myrdJz94+0AGWoBlbWiPEZ8zGoDK72zqBy2IYfnjAgMBAAECggEADF9hvrjdNLcsSlyHutwK5XXqr1QOWE2ZhHzN8FzWbXPb9JH+6TpNfPYzybc2BYFfCqlbr+YUHw7rAkJfZe25XTVxFPdfe/mkEu5cpu7Fak8Q4V5uUTy5Z6d2fnGmheSjD2w19ZKE9fVN0QHmVQQ9/cnWAqGCkPkQh8YTI/gxAURFvNFuQfJAUMIkUDQhgg/IVEJG4zgMZWd64sek16sYoX7bowhkA5zM0OFn6QW1nExmGbFOuIYJk6hpR3csvaaITVmz+84JtpbeAPU28jc0GRFmJe0PD0CV5Hw6Q03JDJwJ0eLo41ypaqzBJaj9QegHnPlhqnMVyx28psOqd9EgSQKBgQDV9rXSz77U69sliW7fMkb0pIseKZrNVRKsADHDgh3IxjxydbwT+KTC2aGKwDCzGGCbwtU+q/WBkay9Qj6VvbmZgF0hmS4t3uCO7ITM6+a/3WkICsv+T8LYhb7E7VS06tyv74/pMr8imsSlSIghOmn1lbcfwyVaYvJyXpCMggTH/QKBgQC7Zkz9ZBFIekRGTm/QeVkzQp8HwVecftj3OyBQERDu2vRwv+FJSyXObU1EfCMRdDMbtdYr8Aq3um3XHDKOI+LWRPnzd882V7ezvlvLRmGDgPWbeCXUiyjwsWi1895jk5F6WsNeO+jKkULtGpUO91MWKDtcyBbDf01YeVlkkt6/XwKBgQCFBtBg8zyoUMN9gQbnyfCHpc3AwQk35E6pla1a6ojuDSID3+NBX3PAmWJwS7F7iAp4jRfb0TnghtupB4i1vLGqGD7O5tfpOQrQkYi3h2t9moD0uRG/WYPA7tZ+xTfHC8aNtBn7WMuxDhx4zrGkRxcd0yl5m/CbU619IgIo+aVTWQKBgBuuq/bOXmM3dHhyQ8Am/M8+qyFJJTj/3+r9d/N74eV2wJ8bKRtbl3Ub3kscj02Xvaj9Pmr1JZAoYOtixfELSgne3JFjhF+Bq6hImWS4u9TiKuXRZL015VFQ06f1I0fQV7AcUjnMXcH2nU1LHPO+Ay8FuGcnQ32Tg4hrPJJHw9G/AoGAHpGjWLxUn9GHZUXL8StSSEdccDdDszg5F8kDJ7Wt/mzf81kORW4IRlOyZNWoMSO5+aEGMsQ77HYpTT0B4a0LSF634asT9kg8wFsKqJ/EZZnGmy6CQTmTGo3oE9+Icf4Bd1weNHtISdNkor5F9u1KqE5ErpoRBAnbMGrTlOSBT5w=';
                $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwTkk4n94SzI9wDcle82KMmI8T8U6G1NKt6JdFAi8g9FSwNmOA2LEY+KWi/HDFqQM3JMh1RAXWLs713tL0075ak2yn6fjZjfIANW1GM+sAgfCAMrLXGbr9m2zGd5ZKLiWmeKp5SA6cWfD4ZqvKxqubNAk7wwq/QEfRtYnAPloU7vfGfDxeirTVF4STyeiG0N0mnvFiguC3oeS+WodS10Q29KQetg0Dfk0LgYAi+HAEt3eQVz019gkdXCnuYoo+vhLsaYCgwa9oDP6p4iM8+0IPp7rgscETbln4JK2y2RTskJw4aCoLanfmP9Lg80DNOhalTymv2QjIICBxCr4fzwbpwIDAQAB';
                $aop->format = "json";
                $aop->charset = "UTF-8";
                $aop->signType = "RSA2";

                $request = new AlipayTradeAppPayRequest();
                $content = [
                    'body' => '约邻',
                    'subject' => '约邻',
                    'out_trade_no' => $data['order_no'],
                    'timeout_express' => '30m',
                    'total_amount' => $order['money'],
                    'product_code' => 'QUICK_MSECURITY_PAY'
                ];

                $request->setNotifyUrl($url);
                $request->setBizContent(json_encode($content, JSON_UNESCAPED_UNICODE));
                $response = $aop->sdkExecute($request);
                return $this->succ(['sdk' => $response ]);
                //  return $this->successful(htmlspecialchars($response));
            } catch (\Exception $e) {
                return $this->fail([], false);
            }
    }


    /**
     * 微信支付
     * @param $order_no
     * @return array|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wechatPay($order_no,$url)
    {
            $data['order_no'] = $order_no;
            $order = Db::name('user_order')->where('order_no', $data['order_no'])
                ->where('user_id', $this->request->user_id)
                ->find();
            if(empty($order)){
                return $this->fail('订单不存在',false);
            }
            if (isset($order['status']) && $order['status'] != 0) {
                return $this->fail('该订单不需要支付',false);
            }
            try {
                $payment =  'open';
                $app = Facade::payment($payment);
                $pay = [
                    'notify_url' => $url,
                    'trade_type' => 'APP',
                    'body' => '约邻',
                    'out_trade_no' => $order_no,
                    'total_fee' => floatval($order['money']) * 100,
                ];
                $result = $app->order->unify($pay);
                $payData = [
//                    'money' => $order['money'],
                    'type' =>  2,
//                    'status' => 1,
//                    'pay_time' => time(),
                    'transaction_id' => $result['prepay_id'],
                    'pay_no' => $order_no
                ];
                Db::name('user_order')->where(['order_no'=>$data['order_no']])->update($payData);
                $pay['trade_type'] = 'APP';
                return $this->succ($app->jssdk->appConfig($result['prepay_id']));
            } catch (\Exception $e) {
                return $this->fail( $e->getMessage(), false);
            }
    }



}