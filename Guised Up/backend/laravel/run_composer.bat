@pushd "D:\Guised Up\backend\laravel"
@"D:\Guised Up\backend\laravel\portable_php\php.exe" -c "D:\Guised Up\backend\laravel\portable_php\php.ini" "D:\Guised Up\backend\laravel\portable_php\composer.phar" config --no-plugins --no-scripts policy.advisories.block false || exit /b 0
@"D:\Guised Up\backend\laravel\portable_php\php.exe" -c "D:\Guised Up\backend\laravel\portable_php\php.ini" "D:\Guised Up\backend\laravel\portable_php\composer.phar" install --prefer-dist --no-dev --no-interaction --no-plugins --no-scripts --optimize-autoloader
@popd
