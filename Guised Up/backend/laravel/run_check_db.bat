@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" tools\check_db.php > tools\check_db.out 2>&1
type tools\check_db.out
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
