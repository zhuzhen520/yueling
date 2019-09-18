<?php

namespace app\models;

use think\Model;

class Admin extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created';
    protected $updateTime = 'updated';


}