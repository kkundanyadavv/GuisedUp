@echo off
pushd "D:\Guised Up\backend\laravel"
"D:\Guised Up\backend\laravel\portable_php\php.exe" -r "try{ $db=new PDO('sqlite:database/database.sqlite'); echo 'users:'. $db->query('select count(*) from users')->fetchColumn() . PHP_EOL; echo 'posts:'. $db->query('select count(*) from posts')->fetchColumn() . PHP_EOL; echo 'interactions:'. $db->query('select count(*) from interactions')->fetchColumn() . PHP_EOL; echo 'follows:'. $db->query('select count(*) from follows')->fetchColumn() . PHP_EOL; }catch(Exception $e){ echo 'ERR:'.$e->getMessage(); }"
set EXITCODE=%ERRORLEVEL%
popd
exit /b %EXITCODE%
