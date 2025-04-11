<?php
// 尝试多个常见的数据库密码
$host = '127.0.0.1';
$port = 3306;
$user = 'root';

// 常见的密码列表
$passwords = ['', 'root', 'password', 'mysql', 'admin', '123456', 'Password', 'password123', 'root123'];

echo "正在尝试常见的MySQL密码...\n";
$connected = false;
$correctPassword = '';

foreach ($passwords as $pass) {
    try {
        echo "尝试密码: '" . ($pass === '' ? '空密码' : $pass) . "'... ";
        $dsn = "mysql:host=$host;port=$port";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "成功！\n";
        $connected = true;
        $correctPassword = $pass;
        
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
        
        break;
    } catch (PDOException $e) {
        echo "失败！\n";
        // 继续尝试下一个密码
    }
}

if ($connected) {
    echo "\n===================================================\n";
    echo "正确的MySQL密码是: '" . ($correctPassword === '' ? '空密码' : $correctPassword) . "'\n";
    echo "建议更新config/db.php中的密码设置\n";
    echo "===================================================\n";
} else {
    echo "\n尝试了所有常见密码都失败了。\n";
    echo "请联系系统管理员确认MySQL的root密码。\n";
    echo "或者尝试自己手动输入密码：\n";
    echo "请输入MySQL root密码: ";
    $manualPassword = trim(fgets(STDIN));
    
    try {
        $pdo = new PDO($dsn, $user, $manualPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "成功连接！您输入的密码正确\n";
        
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
        
        echo "\n===================================================\n";
        echo "正确的MySQL密码是您手动输入的密码\n";
        echo "建议更新config/db.php中的密码设置为您输入的密码\n";
        echo "===================================================\n";
    } catch (PDOException $e) {
        echo "手动输入的密码也连接失败: " . $e->getMessage() . "\n";
    }
} 