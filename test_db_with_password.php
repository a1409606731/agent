<?php
// 数据库连接测试（带密码提示）
echo "正在测试MySQL连接...\n";
echo "请输入MySQL root密码: ";
$password = trim(fgets(STDIN));

$host = '127.0.0.1';
$port = 3306;
$user = 'root';

try {
    $dsn = "mysql:host=$host;port=$port";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "成功连接到MySQL服务器！\n";
    
    // 检查cozex数据库是否存在
    $stmt = $pdo->query("SHOW DATABASES LIKE 'cozex'");
    $dbExists = $stmt->fetchColumn();
    
    if ($dbExists) {
        echo "cozex数据库已存在\n";
    } else {
        echo "cozex数据库不存在，尝试创建...\n";
        $pdo->exec("CREATE DATABASE cozex");
        echo "成功创建cozex数据库\n";
    }
    
    // 如果成功连接，建议更新配置文件
    echo "\n建议更新config/db.php中的密码为您输入的密码\n";
    
} catch (PDOException $e) {
    echo "连接错误: " . $e->getMessage() . "\n";
}

echo "\n完成。"; 