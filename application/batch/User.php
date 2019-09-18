<?php

namespace app\batch;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class User extends Command
{

    protected function configure()
    {
        $this->setName('User')->setDescription('用户');
    }

    /**
     * 注销 {prohibit} 天未缴费的用户, 计划任务
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    protected function execute(Input $input, Output $output)
    {
        $day = get_config('user','prohibit');
        $times = $day * 24*60*60;
        $start = date('Y-m-d H:i:s',0);
        $end   = date('Y-m-d H:i:s',time()-$times);
        $user = Db::name('user')
                ->where(['benefit'=>0])
                ->where('created','between',[$start,$end])
                ->select();
        foreach ($user as $v){
            Db::name('user')->delete($v['id']);
            sleep(1);
        }
    }

}