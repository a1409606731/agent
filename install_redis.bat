@echo off
echo 正在准备安装Redis扩展...
echo.

REM 设置PHP路径和临时目录
set PHP_PATH=H:\php-8.4.5-Win32-vs17-x64
set TEMP_DIR=%TEMP%\php_redis_temp
set DOWNLOAD_URL=https://windows.php.net/downloads/pecl/releases/redis/5.3.7/php_redis-5.3.7-8.4-ts-x64.zip
set ZIP_FILE=%TEMP_DIR%\redis.zip
set REDIS_DLL=php_redis.dll

REM 创建临时目录
if not exist "%TEMP_DIR%" mkdir "%TEMP_DIR%"

echo 正在下载Redis扩展...
echo 从 %DOWNLOAD_URL% 下载中...

REM 使用PowerShell下载文件
powershell -Command "(New-Object Net.WebClient).DownloadFile('%DOWNLOAD_URL%', '%ZIP_FILE%')"

if not exist "%ZIP_FILE%" (
    echo 下载失败！请手动下载Redis扩展。
    echo 请访问: https://windows.php.net/downloads/pecl/releases/redis/
    echo 选择适合您的PHP版本(8.4.x)的线程安全(ts)版本
    pause
    exit /b 1
)

echo 正在解压Redis扩展...

REM 使用PowerShell解压文件
powershell -Command "Expand-Archive -Path '%ZIP_FILE%' -DestinationPath '%TEMP_DIR%' -Force"

REM 查找php_redis.dll文件
echo 正在查找%REDIS_DLL%文件...
set FOUND=0
for /r "%TEMP_DIR%" %%i in (*%REDIS_DLL%) do (
    set REDIS_DLL_PATH=%%i
    set FOUND=1
)

if %FOUND%==0 (
    echo 未找到%REDIS_DLL%文件！请手动解压并安装Redis扩展。
    pause
    exit /b 1
)

echo 找到Redis扩展文件: %REDIS_DLL_PATH%

REM 复制文件到PHP扩展目录
echo 正在安装Redis扩展到PHP目录...
copy "%REDIS_DLL_PATH%" "%PHP_PATH%\ext\" /y

if not exist "%PHP_PATH%\ext\%REDIS_DLL%" (
    echo 复制失败！请手动将%REDIS_DLL%文件复制到%PHP_PATH%\ext\目录。
    pause
    exit /b 1
)

echo.
echo Redis扩展安装成功！
echo.
echo 注意：如果PHP配置文件已包含Redis扩展配置，则可以直接使用。
echo 否则，请确保在php.ini文件中添加以下行：
echo     extension=php_redis.dll
echo.

REM 清理临时文件
echo 是否清理临时文件？(Y/N)
set /p CLEAN=
if /i "%CLEAN%"=="Y" (
    echo 正在清理临时文件...
    rmdir /s /q "%TEMP_DIR%"
    echo 清理完成。
)

echo.
echo 安装完成！
pause 