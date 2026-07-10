@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" artisan serve --host=127.0.0.1 --port=8000
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
