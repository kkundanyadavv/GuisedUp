@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" tools\inline_counts.php > tools\inline_counts.out 2>&1
type tools\inline_counts.out
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
