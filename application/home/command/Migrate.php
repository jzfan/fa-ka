<?php

namespace app\home\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Exception;

/**
 * 数据库迁移与更新工具
 * @author Richie Zhang
 * @version 1.0 2018-04-28
 */
class migrate extends Command
{
    protected function configure()
    {
        $this->setName('migrate')->setDescription('migrate your database');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("Migrating database...");

        //1. 如果数据库里没有 migrations 表，则先创建之
        $result = Db::query("show tables like 'migrations';");
        if (empty($result)) {
            $this->init_migrations();
        }

        //2. 执行 database/migrations 里面的数据库更新语句
        $this->do_migrate();
        $output->writeln("Migration finished.");
    }

    /**
     * 执行migrations文件
     */
    private function do_migrate()
    {
        $dir = ROOT_PATH . 'database' . DS . 'migrations';
        $files = scandir($dir);
        if (is_array($files)) {

            $last_migration = Db::name('migrations')->order('id desc')->find();
            $batch = empty($last_migration) ? 0 : ($last_migration['batch'] + 1);

            foreach ($files as $file) {
                if (substr($file, -4) == '.sql') {
                    $migration_exists = Db::query("select * from migrations where migration = '{$file}'");
                    if (!$migration_exists) {
                        $file_path = $dir . DS . $file;
                        echo 'migrating: ' . $file_path . PHP_EOL;
                        $this->source_sql($file_path);
                        Db::name('migrations')->insert(['migration' => $file, 'batch' => $batch]);
                    }
                }
            }
        }

    }

    /**
     * 初始化相关的表
     */
    private function init_migrations()
    {
        $dir = ROOT_PATH . 'database' . DS . 'migrations' . DS . 'init';
        $files = scandir($dir);
        if (!$files) {
            throw new Exception('初始文件不存在');
        }

        foreach ($files as $file) {
            if (substr($file, -4) == '.sql') {
                $this->source_sql($dir . DS . $file);
            }
        }
    }

    /**
     * 模拟执行 mysql source file
     * @param $file_path
     */
    private function source_sql($file_path)
    {
        $host = config('database.hostname', '127.0.0.1');
        $port = config('database.hostport', '3306');
        $user = config('database.username');
        $password = config('database.password');
        $database = config('database.database');

        if (function_exists('passthru') && `if hash mysql 2>/dev/null; then echo 1; else echo 0; fi` == 1) {
            $cmd = "mysql -h{$host} -P{$port} -u{$user} -p{$password} -e 'use {$database}; source {$file_path};'";
            echo "importing mysql source file using the 'mysql' command" . PHP_EOL;
            passthru($cmd);
        } else {
            echo "executing mysql source file using the mysqli extension" . PHP_EOL;
            $query = file_get_contents($file_path);
            $mysqli = new \mysqli($host, $user, $password, $database, $port);
            if (mysqli_connect_errno()) {
                printf("MySQL connect failed: %s\n", mysqli_connect_error());
                exit();
            }
            /* execute multi query */
            if ($mysqli->multi_query($query)) {
                do {
                    /* store first result set */
                    if ($result = $mysqli->store_result()) {
                        while ($row = $result->fetch_row()) {
                            printf("%s\n", $row[0]);
                        }
                        $result->free();
                    }
                    /* print divider */
                    if ($mysqli->more_results()) {
                        printf("-----------------\n");
                    } else {
                        break;
                    }
                } while ($mysqli->next_result());
            }
            $mysqli->close();
        }
    }
}