<?php

namespace app\home\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * 数据库order表字段数据迁移 total_price -> total_product_price
 * @author Richie Zhang
 * @version 1.0 2018-05-10
 */
class Migrate20180510 extends Command
{
    protected function configure()
    {
        $class = new \ReflectionClass(static::class);
        $description = $class->getDocComment();
        $this->setName($class->getShortName())->setDescription($description);
    }

    protected function execute(Input $input, Output $output)
    {
        $step = 100;
        $total_affected_rows = 0;
        do {
            $query = "update `order` set `total_product_price`=`total_price` where `total_product_price`=0 and `total_product_price`!=`total_price` limit {$step}";
            $affected_rows = Db::execute($query);
            $total_affected_rows += $affected_rows;
            $output->write("\r已更新{$total_affected_rows}行");
        } while ($affected_rows > 0);
    }
}