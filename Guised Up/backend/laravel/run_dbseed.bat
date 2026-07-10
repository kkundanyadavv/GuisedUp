@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" artisan db:seed --class="Database\\Seeders\\DatabaseSeeder" --force -v
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
