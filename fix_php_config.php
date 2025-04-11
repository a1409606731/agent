<?php
// 直接指定PHP安装目录和扩展目录的完整路径
$php_dir = 'H:/php-8.4.5-Win32-vs17-x64';
$ext_dir = 'H:/php-8.4.5-Win32-vs17-x64/ext';

// 获取php.ini的内容
$php_ini_file = $php_dir . '/php.ini';
$php_ini_content = file_get_contents($php_ini_file);

// 修改扩展目录路径（使用Windows格式的路径）
$windows_ext_dir = str_replace('/', '\\', $ext_dir);
$php_ini_content = preg_replace(
    '/extension_dir\s*=\s*"[^"]*"/',
    'extension_dir = "' . $windows_ext_dir . '"',
    $php_ini_content
);

// 需要启用的扩展列表
$required_extensions = [
    'php_fileinfo.dll',
    'php_mbstring.dll',
    'php_pdo_mysql.dll',
    'php_curl.dll',
    'php_openssl.dll',
    'php_mysqli.dll'
];

// 启用所需的扩展
foreach ($required_extensions as $ext) {
    // 检查扩展是否已经启用
    if (strpos($php_ini_content, 'extension=' . $ext) === false) {
        // 如果扩展被注释掉了，取消注释
        if (strpos($php_ini_content, ';extension=' . $ext) !== false) {
            $php_ini_content = str_replace(';extension=' . $ext, 'extension=' . $ext, $php_ini_content);
        } else {
            // 如果扩展行不存在，添加它
            $php_ini_content .= "\nextension=" . $ext;
        }
    }
}

// 保存修改后的php.ini
file_put_contents($php_ini_file, $php_ini_content);

echo "PHP配置已更新！\n";
echo "扩展目录已设置为: " . $windows_ext_dir . "\n";
echo "已启用以下扩展: " . implode(', ', $required_extensions) . "\n";

// 输出重要信息供验证
echo "\n当前PHP版本: " . PHP_VERSION . "\n";
echo "配置文件路径: " . php_ini_get_path() . "\n";

/**
 * 获取PHP ini文件路径
 */
function php_ini_get_path() {
    ob_start();
    phpinfo(INFO_GENERAL);
    $info = ob_get_clean();
    
    preg_match('/Loaded Configuration File => (.*)/', $info, $matches);
    if (isset($matches[1])) {
        return trim($matches[1]);
    }
    
    return 'Not found';
} 