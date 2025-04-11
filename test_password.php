<?php
// 使用"password"作为密码测试MySQL连接
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = 'password';  // 使用配置文件中的密码

try {
    $dsn = "mysql:host=$host;port=$port";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "成功使用密码 '$pass' 连接到MySQL！\n";
    
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
    
    echo "\nMySQL连接和数据库检查成功，配置文件中的密码正确！\n";
    
} catch (PDOException $e) {
    echo "连接错误: " . $e->getMessage() . "\n";
    echo "\n配置文件中的密码'$pass'不正确，请更新config/db.php中的密码\n";
} 