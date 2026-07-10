@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" tools\run_seeder.php > tools\run_seeder.out 2>&1
type tools\run_seeder.out
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
