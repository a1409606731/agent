@echo off
echo 正在启动Cozex应用...
echo.

REM 设置PHP路径
set PHP_PATH=H:\php-8.4.5-Win32-vs17-x64\php.exe
set PHP_INI=E:\cozex-main\src\php.ini
set WEB_ROOT=E:\cozex-main\src\web

REM 检查PHP是否存在
if not exist "%PHP_PATH%" (
    echo 错误: 找不到PHP执行文件: %PHP_PATH%
    echo 请修改PHP_PATH变量为正确的PHP路径
    pause
    exit /b 1
)

REM 检查配置文件是否存在
if not exist "%PHP_INI%" (
    echo 错误: 找不到PHP配置文件: %PHP_INI%
    echo 请修改PHP_INI变量为正确的配置文件路径
    pause
    exit /b 1
)

REM 检查web目录是否存在
if not exist "%WEB_ROOT%" (
    echo 错误: 找不到Web根目录: %WEB_ROOT%
    echo 请修改WEB_ROOT变量为正确的Web根目录路径
    pause
    exit /b 1
)

echo 使用配置文件: %PHP_INI%
echo Web根目录: %WEB_ROOT%
echo.
echo Cozex应用将运行在 http://localhost:8080
echo 按Ctrl+C结束服务器
echo.

REM 启动PHP内置Web服务器
"%PHP_PATH%" -c "%PHP_INI%" -S localhost:8080 -t "%WEB_ROOT%"

pause 