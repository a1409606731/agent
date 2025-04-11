<?php
// 定义PHP安装目录和扩展目录
$php_dir = 'H:/php-8.4.5-Win32-vs17-x64';
$ext_dir = $php_dir . '/ext';

// 读取php.ini文件
$php_ini = file_get_contents($php_dir . '/php.ini');

// 更改扩展目录设置
$php_ini = preg_replace('/extension_dir\s*=\s*"[^"]*"/', 'extension_dir = "' . str_replace('/', '\\', $ext_dir) . '"', $php_ini);

// 启用必要的扩展
$extensions = [
    'fileinfo',
    'mbstring',
    'pdo_mysql',
    'curl',
    'redis',
    'openssl',
    'mysqli'
];

// 启用扩展
foreach ($extensions as $ext) {
    // 检查扩展是否已启用
    if (strpos($php_ini, ';extension=' . $ext) !== false) {
        $php_ini = str_replace(';extension=' . $ext, 'extension=' . $ext, $php_ini);
    } else if (strpos($php_ini, ';extension=php_' . $ext . '.dll') !== false) {
        $php_ini = str_replace(';extension=php_' . $ext . '.dll', 'extension=php_' . $ext . '.dll', $php_ini);
    } else {
        // 如果扩展行不存在，添加它
        $php_ini .= "\nextension=php_" . $ext . ".dll";
    }
}

// 保存修改后的php.ini文件
file_put_contents($php_dir . '/php.ini', $php_ini);

echo "PHP配置已更新！\n";
echo "扩展目录已设置为: " . $ext_dir . "\n";
echo "已启用以下扩展: " . implode(', ', $extensions) . "\n"; 