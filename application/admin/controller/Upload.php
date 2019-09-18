<?php

namespace app\admin\controller;


use app\helps\Oss;
use think\Controller;

/**
 * Class Upload
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
class Upload extends Controller
{

    public function upload()
    {
        $img = '';
        //调用阿里云上传
        if($this->request->file()){
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('file');
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move( '../public/uploads');
            if($info){
                return json(['status'=>'n','info'=>'/uploads/'.$info->getSaveName()]);
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
            exit;
        }
        return json(['status'=>'n','info'=>$img]);
    }




}