@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" "D:\Guised Up\backend\laravel\portable_php\composer.phar" dump-autoload -o
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
