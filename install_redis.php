<?php
// 输出PHP信息
echo "PHP版本: " . PHP_VERSION . "\n";
echo "PHP扩展目录: " . ini_get('extension_dir') . "\n\n";

// 检查是否已经安装了Redis扩展
$redis_installed = extension_loaded('redis');
echo "Redis扩展是否已安装: " . ($redis_installed ? "是" : "否") . "\n\n";

// 检查PHP架构
$is_64bit = (PHP_INT_SIZE == 8);
echo "PHP架构: " . ($is_64bit ? "64位" : "32位") . "\n\n";

// 检查PHP线程安全性
echo "检查PHP线程安全性...\n";
ob_start();
phpinfo(INFO_GENERAL);
$phpinfo = ob_get_clean();
$is_ts = (strpos($phpinfo, 'Thread Safety => enabled') !== false);
echo "PHP是否线程安全: " . ($is_ts ? "是" : "否") . "\n\n";

// 获取PHP版本号的主要和次要部分
preg_match('/^(\d+)\.(\d+)/', PHP_VERSION, $php_version_match);
$php_major = $php_version_match[1] ?? '';
$php_minor = $php_version_match[2] ?? '';

// 输出安装步骤
echo "===== Redis扩展安装步骤 =====\n";
echo "1. 访问 https://windows.php.net/downloads/pecl/releases/redis/ 下载适合您PHP版本的Redis扩展\n";
echo "   - 选择与您PHP版本兼容的最新版本，例如: redis-5.3.7\n";
echo "   - 选择正确的文件: " . ($is_ts ? "php_redis-5.3.7-" . $php_major . "." . $php_minor . "-ts-" . ($is_64bit ? "x64" : "x86") . ".zip" : "php_redis-5.3.7-" . $php_major . "." . $php_minor . "-nts-" . ($is_64bit ? "x64" : "x86") . ".zip") . "\n";
echo "2. 解压下载的文件，找到php_redis.dll\n";
echo "3. 将php_redis.dll复制到PHP扩展目录: " . ini_get('extension_dir') . "\n";
echo "4. 编辑php.ini文件，添加以下行:\n";
echo "   extension=php_redis.dll\n";
echo "5. 重启PHP服务器/Web服务器\n\n";

// 检查其他必要扩展
$required_extensions = [
    'fileinfo',
    'mbstring',
    'pdo_mysql',
    'curl',
    'openssl',
    'mysqli'
];

echo "===== 其他必要扩展检查 =====\n";
foreach ($required_extensions as $ext) {
    $installed = extension_loaded($ext);
    echo $ext . ": " . ($installed ? "已安装" : "未安装") . "\n";
}

echo "\n===== 如何安装缺失的扩展 =====\n";
echo "1. 编辑PHP配置文件 (php.ini)\n";
echo "2. 启用以下扩展 (删除前面的分号):\n";
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        echo "   extension=php_" . $ext . ".dll\n";
    }
}

echo "\n配置文件路径通常位于: " . (PHP_CONFIG_FILE_PATH ?? '未知'); 