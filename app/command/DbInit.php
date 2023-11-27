<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Env;

class DbInit extends Command
{
    const PREFIX = 'mksys_';

    protected function configure()
    {
        $this->setName('dbinit')
            ->addOption('prefix', null, Option::VALUE_REQUIRED, 'table prefix')
            ->setDescription('init db sql');
    }

    protected function execute(Input $input, Output $output)
    {
        $path = app()->getRootPath();
        if (file_exists($path . 'init.lock')) {
            $output->writeln("你已经初始化过数据库，如需重新初始化，请先删除根目录下的 init.lock 文件。");
            exit;
        }

        $prefix = 'mks_';
        if ($input->hasOption('prefix')) {
            $prefix = trim($input->getOption('prefix'));
        }

        // 执行导入SQL文件
        $sqlPath = $path . 'data/init.sql';
        $envPath = $path . '.env';

        if (!file_exists($envPath)) {
            $output->writeln(".env配置文件不存在，或未配置数据库");
            exit;
        }
        if (!file_exists($sqlPath)) {
            $output->writeln("数据库初始化SQL文件不存在");
            exit;
        }

        $config = Env::get();
        // 连接数据库
        $conn = @mysqli_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], null, $config['DB_PORT']);
        if (mysqli_connect_errno()) {
            $errorMsg = "连接数据库失败!" . mysqli_connect_error($conn);
            $output->writeln($errorMsg);
            exit;
        }
        mysqli_set_charset($conn, "utf8");
        $version = mysqli_get_server_info($conn);
        if ($version < 5.1) {
            $errorMsg = '数据库版本太低! 必须5.1以上';
            $output->writeln($errorMsg);
            exit;
        }

        $hasDb = mysqli_query($conn, "SHOW DATABASES LIKE '{$config['DB_NAME']}';");
        if (!$hasDb || count(mysqli_fetch_all($hasDb)) < 1) {
            //创建数据时同时设置编码
            if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `" . $config['DB_NAME'] . "` DEFAULT CHARACTER SET utf8;")) {
                $errorMsg = '数据库 ' . $config['DB_NAME'] . ' 不存在，也没权限创建新的数据库！';
                $output->writeln($errorMsg);
                exit;
            }
        }
        mysqli_select_db($conn, $config['DB_NAME']);

        // 读取数据文件
        $sqldata = file_get_contents($sqlPath);
        $sqldata = preg_replace("/(\/\*.*\*\/)|(#.*?\n)|(\/\/.*?\n)/s", '', str_replace(array("\r\n", "\r"), "\n", $sqldata));
        $sqlFormat = $this->sqlSplit($sqldata, $prefix);

        $counts = count($sqlFormat);
        for ($i = 0; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            if (strstr($sql, 'CREATE TABLE')) {
                preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                //mysqli_query($conn, "DROP TABLE IF EXISTS `$matches[1]");
                $ret = mysqli_query($conn, $sql);
                if (!$ret) {
                    $errorMsg = '创建数据表' . $matches[1] . '失败!';
                    $output->writeln($errorMsg);
                    exit;
                }
            } else {
                if (trim($sql) == '') continue;
                preg_match('/INSERT INTO `([^ ]*)`/', $sql, $matches);
                $ret = mysqli_query($conn, $sql);
                if (!$ret) {
                    $errorMsg = '写入表' . $matches[1] . '记录失败!';
                    $output->writeln($errorMsg);
                    exit;
                }
            }
        }
        @mysqli_close($conn);
        @touch($path . 'init.lock');
        $output->writeln("Db Init Done");
    }

    /**
     * 将SQL文件分割成SQL执行语句
     *
     * @param string $sql
     * @param string $tablepre
     * @return array
     */
    private function sqlSplit($sql, $tablepre = DbInit::PREFIX)
    {
        if ($tablepre != DbInit::PREFIX) $sql = str_replace(DbInit::PREFIX, $tablepre, $sql);
        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }
}
