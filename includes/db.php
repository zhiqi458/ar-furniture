<?php
$config = require __DIR__ . '/../config/config.php';

try {
    // 包含 port 参数，确保完美兼容 MySQL Workbench 的本地或远程连接配置
    $dsn = "mysql:host={$config['db']['host']};port={$config['db']['port']};dbname={$config['db']['name']};charset={$config['db']['charset']}";
    
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    // 生产环境中隐去敏感错误，开发阶段可将下面改为 echo $e->getMessage();
    die("数据库连接失败：无法连接至 MySQL 数据库。请检查 config/config.php 中的参数与 MySQL Workbench 服务状态。");
}