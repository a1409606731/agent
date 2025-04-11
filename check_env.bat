@echo off
echo.
echo =================================
echo        Cozex环境检查工具
echo =================================
echo.

REM 设置PHP路径
set PHP_PATH=H:\php-8.4.5-Win32-vs17-x64\php.exe
set PHP_INI=E:\cozex-main\src\php.ini

REM 检查PHP是否存在
echo 检查PHP...
if not exist "%PHP_PATH%" (
    echo [失败] 找不到PHP: %PHP_PATH%
    echo 请确保PHP已正确安装，并修改脚本中的PHP_PATH变量
) else (
    echo [成功] 找到PHP: %PHP_PATH%
    
    REM 检查PHP版本
    for /f "tokens=*" %%i in ('"%PHP_PATH%" -v ^| findstr /i "PHP"') do (
        echo [信息] %%i
    )
)

echo.
echo 检查PHP配置文件...
if not exist "%PHP_INI%" (
    echo [失败] 找不到PHP配置文件: %PHP_INI%
    echo 请确保已创建PHP配置文件，并修改脚本中的PHP_INI变量
) else (
    echo [成功] 找到PHP配置文件: %PHP_INI%
)

echo.
echo 检查MySQL...
for /f "tokens=*" %%i in ('mysqladmin -V 2^>nul') do (
    echo [成功] MySQL已安装: %%i
    goto :mysql_installed
)
echo [警告] 未检测到MySQL命令行工具，请确保MySQL已安装
:mysql_installed

echo.
echo 检查PHP扩展...
echo.
echo 必要扩展状态:
"%PHP_PATH%" -c "%PHP_INI%" -r "echo 'fileinfo: ' . (extension_loaded('fileinfo') ? '[成功] 已加载' : '[失败] 未加载') . PHP_EOL;"
"%PHP_PATH%" -c "%PHP_INI%" -r "echo 'mbstring: ' . (extension_loaded('mbstring') ? '[成功] 已加载' : '[失败] 未加载') . PHP_EOL;"
"%PHP_PATH%" -c "%PHP_INI%" -r "echo 'pdo_mysql: ' . (extension_loaded('pdo_mysql') ? '[成功] 已加载' : '[失败] 未加载') . PHP_EOL;"
"%PHP_PATH%" -c "%PHP_INI%" -r "echo 'curl: ' . (extension_loaded('curl') ? '[成功] 已加载' : '[失败] 未加载') . PHP_EOL;"
"%PHP_PATH%" -c "%PHP_INI%" -r "echo 'openssl: ' . (extension_loaded('openssl') ? '[成功] 已加载' : '[失败] 未加载') . PHP_EOL;"
"%PHP_PATH%" -c "%PHP_INI%" -r "echo 'mysqli: ' . (extension_loaded('mysqli') ? '[成功] 已加载' : '[失败] 未加载') . PHP_EOL;"
"%PHP_PATH%" -c "%PHP_INI%" -r "echo 'redis: ' . (extension_loaded('redis') ? '[成功] 已加载' : '[警告] 未加载 - 使用install_redis.bat安装') . PHP_EOL;"

echo.
echo 检查Redis服务...
for /f "tokens=*" %%i in ('redis-cli -v 2^>nul') do (
    echo [成功] Redis客户端已安装: %%i
    goto :redis_installed
)
echo [警告] 未检测到Redis客户端，请安装Redis服务器
:redis_installed

echo.
echo 检查Web目录...
if not exist "web" (
    echo [失败] 未找到Web目录
) else (
    echo [成功] Web目录存在
)

echo.
echo 检查安装状态...
if exist "install.lock" (
    echo [信息] 应用已安装
) else (
    echo [信息] 应用尚未安装，首次访问时将进入安装流程
)

echo.
echo =================================
echo            检查完成
echo =================================
echo.
echo 如果出现[失败]项目，请按照提示修复相关问题
echo 如果出现[警告]项目，可能会影响应用的部分功能
echo 所有项目都是[成功]或[信息]，则环境已准备就绪
echo.
echo 运行run_cozex.bat启动应用
echo.

pause 