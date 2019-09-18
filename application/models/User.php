<?php

namespace app\models;

use think\Db;
use think\Model as Model;

class User extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created';
    protected $defaultSoftDelete = 0;
}

