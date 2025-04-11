<?php
// 使用Windows身份验证连接MySQL并创建用户

echo "=== 使用Windows身份验证连接MySQL ===\n\n";

$host = '127.0.0.1';
$port = 3306;
$newUser = 'cozex';
$newPass = 'cozex';
$dbname = 'cozex';

try {
    echo "尝试使用Windows身份验证连接MySQL...\n";
    // 尝试不提供用户名和密码，依赖Windows身份验证
    $dsn = "mysql:host=$host;port=$port";
    $pdo = new PDO($dsn);
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
        $pdo->exec("CREATE USER IF NOT EXISTS '$newUser'@'localhost' IDENTIFIED BY '$newPass'");
        $pdo->exec("GRANT ALL PRIVILEGES ON $dbname.* TO '$newUser'@'localhost'");
        $pdo->exec("FLUSH PRIVILEGES");
        echo "成功创建用户并授权\n";
    } catch (PDOException $ex) {
        echo "创建用户时出错: " . $ex->getMessage() . "\n";
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
    echo "Windows身份验证连接失败: " . $e->getMessage() . "\n";
    
    echo "\n尝试使用其他方式...\n";
    // 尝试使用mysql命令行工具
    echo "尝试使用外部命令创建数据库和用户...\n";
    
    $mysqlCmd = <<<EOT
    CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
    CREATE USER IF NOT EXISTS '$newUser'@'localhost' IDENTIFIED BY '$newPass';
    GRANT ALL PRIVILEGES ON $dbname.* TO '$newUser'@'localhost';
    FLUSH PRIVILEGES;
    EOT;
    
    // 将命令保存到临时文件
    $tempFile = 'mysql_commands.sql';
    file_put_contents($tempFile, $mysqlCmd);
    echo "SQL命令已保存到 $tempFile\n";
    
    echo "\n===================================================\n";
    echo "由于无法自动连接到MySQL，请手动运行以下命令：\n";
    echo "1. 打开管理员命令提示符\n";
    echo "2. 运行: mysql -u root -p < mysql_commands.sql\n";
    echo "3. 输入您的root密码\n";
    echo "\n或者使用MySQL客户端手动执行 $tempFile 中的SQL语句\n";
    echo "===================================================\n";
    
    echo "\n完成后，请确保config/db.php中的设置如下：\n";
    echo "username: '$newUser'\n";
    echo "password: '$newPass'\n";
    echo "dbname: '$dbname'\n";
} 