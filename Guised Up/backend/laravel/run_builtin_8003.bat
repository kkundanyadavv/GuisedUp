@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" -S 127.0.0.1:8003 -t public > logs\php_builtin.log 2>&1
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
