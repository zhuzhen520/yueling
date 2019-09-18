<?php
namespace app\validate;

use think\Validate;

class Admin extends Validate
{   
    protected $pk = 'id';
    protected $rule = [
        'username'  => 'require|unique:admin',
        'password'  => 'require',
        'nickname'  => 'require',
        'code'      => 'require|valiCode',
        'role'      => 'require',
    ];

    protected $scene = [
        'edit'  => ['nickname'],
        'add'   => ['username','nickname','password','role'],
    ];

    protected $message = [
        'username.require'   => '必须填写账号',
        'username.unique'    => '账号已经存在',
        'password.require'   => '必须填写密码',
        'nickname.require'   => '必须填写昵称',
        'role.require'       => '必须选择角色',
        'code.require'       => '必须填验证码',
    ];

    protected function valiCode($value){
        return captcha_check($value) ? true : '验证码错误';
    }

    public function scenelogin()
    {
        return $this->only(['username','password','code'])->remove('username', 'unique');
    }  

}