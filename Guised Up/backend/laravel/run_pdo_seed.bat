@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" tools\pdo_seed.php
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
