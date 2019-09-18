<?php

namespace app\batch;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Env;

class Distinguish extends Command
{

    protected function configure()
    {
        $this->setName('Distinguish')->setDescription('Acesstoken人脸识别');
    }

    protected function execute(Input $input, Output $output)
    {
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
        $this->request_post($url."?".$post_data);
        echo 1;
        exit;
    }

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