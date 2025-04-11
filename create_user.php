<?php
// 创建MySQL用户脚本

echo "=== MySQL用户创建工具 ===\n\n";
echo "此工具将创建cozex数据库和用户\n";
echo "请输入MySQL管理员用户名 (通常是root): ";
$adminUser = trim(fgets(STDIN));
echo "请输入{$adminUser}用户的密码: ";
$adminPass = trim(fgets(STDIN));

$host = '127.0.0.1';
$port = 3306;
$newUser = 'cozex';
$newPass = 'cozex';
$dbname = 'cozex';

try {
    echo "\n尝试连接到MySQL...\n";
    $dsn = "mysql:host=$host;port=$port";
    $pdo = new PDO($dsn, $adminUser, $adminPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "成功连接到MySQL服务器！\n";
    
    // 创建数据库（如果不存在）
    echo "检查数据库是否存在...\n";
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    $dbExists = $stmt->fetchColumn();
    
    if ($dbExists) {
        echo "数据库 '$dbname' 已存在\n";
    } else {
        echo "创建数据库 '$dbname'...\n";
        $pdo->exec("CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "成功创建数据库\n";
    }
    
    // 创建用户并授权
    echo "创建用户 '$newUser'@'localhost'...\n";
    try {
        // 对于MySQL 8.0+，需要使用不同的语法
        $pdo->exec("CREATE USER IF NOT EXISTS '$newUser'@'localhost' IDENTIFIED BY '$newPass'");
        $pdo->exec("GRANT ALL PRIVILEGES ON $dbname.* TO '$newUser'@'localhost'");
        $pdo->exec("FLUSH PRIVILEGES");
        echo "成功创建用户并授权\n";
    } catch (PDOException $ex) {
        echo "创建用户时出错，尝试其他方法...\n";
        // 对于较旧版本的MySQL，使用不同的语法
        try {
            $pdo->exec("GRANT ALL PRIVILEGES ON $dbname.* TO '$newUser'@'localhost' IDENTIFIED BY '$newPass'");
            $pdo->exec("FLUSH PRIVILEGES");
            echo "成功创建用户并授权(使用旧语法)\n";
        } catch (PDOException $ex2) {
            echo "创建用户失败: " . $ex2->getMessage() . "\n";
        }
    }
    
    // 测试新用户
    echo "\n测试新创建的用户...\n";
    try {
        $testPdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $newUser, $newPass);
        $testPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "成功使用新用户连接到数据库！\n";
    } catch (PDOException $e) {
        echo "使用新用户连接失败: " . $e->getMessage() . "\n";
    }
    
    echo "\n===================================================\n";
    echo "数据库配置完成。请确保config/db.php中的设置如下：\n";
    echo "username: '$newUser'\n";
    echo "password: '$newPass'\n";
    echo "dbname: '$dbname'\n";
    echo "===================================================\n";
    
} catch (PDOException $e) {
    echo "连接错误: " . $e->getMessage() . "\n";
    echo "请确保输入了正确的管理员用户名和密码\n";
} 