<?php
// 数据库连接测试
echo "正在测试MySQL连接...\n";

$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = ''; // 尝试空密码

try {
    $dsn = "mysql:host=$host;port=$port";
    $pdo = new PDO($dsn, $user, $pass);
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
} catch (PDOException $e) {
    echo "连接错误: " . $e->getMessage() . "\n";
    
    // 如果空密码连接失败，尝试用常见密码
    echo "尝试其他常见密码...\n";
    
    $passwords = ['root', 'mysql', 'password', '123456', 'admin'];
    $connected = false;
    
    foreach ($passwords as $password) {
        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "成功使用密码 '$password' 连接!\n";
            
            // 更新配置文件建议
            echo "\n建议更新config/db.php中的密码为: '$password'\n";
            $connected = true;
            
            // 检查cozex数据库
            $stmt = $pdo->query("SHOW DATABASES LIKE 'cozex'");
            $dbExists = $stmt->fetchColumn();
            
            if ($dbExists) {
                echo "cozex数据库已存在\n";
            } else {
                echo "cozex数据库不存在，尝试创建...\n";
                $pdo->exec("CREATE DATABASE cozex");
                echo "成功创建cozex数据库\n";
            }
            
            break;
        } catch (PDOException $ex) {
            // 继续尝试下一个密码
        }
    }
    
    if (!$connected) {
        echo "所有常见密码都尝试失败，请确认您的MySQL root密码\n";
    }
} 