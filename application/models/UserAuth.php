<?php
namespace app\models;

use think\Model;

class UserAuth extends Model
{
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created';

    public function getStatusTextAttr($value, $data)
    {
        return config('base.user.auth.status')[$data['status']];
    }
}