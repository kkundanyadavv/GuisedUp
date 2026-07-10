import os
import urllib.request
import zipfile

out = r'D:\Guised Up\backend\laravel\portable_php'
os.makedirs(out, exist_ok=True)
urls = [
    'https://windows.php.net/downloads/releases/archives/php-8.2.34-Win32-vs16-x64.zip',
    'https://windows.php.net/downloads/releases/archives/php-8.2.33-Win32-vs16-x64.zip',
    'https://windows.php.net/downloads/releases/archives/php-8.2.32-Win32-vs16-x64.zip',
]
downloaded = False
zip_path = os.path.join(out, 'php.zip')
for u in urls:
    try:
        print('Trying', u)
        urllib.request.urlretrieve(u, zip_path)
        downloaded = True
        break
    except Exception as e:
        print('Failed', u, e)
if not downloaded:
    print('All PHP download attempts failed')
    raise SystemExit(2)
with zipfile.ZipFile(zip_path, 'r') as z:
    z.extractall(out)
os.remove(zip_path)
composer_url = 'https://getcomposer.org/composer.phar'
composer_path = os.path.join(out, 'composer.phar')
urllib.request.urlretrieve(composer_url, composer_path)
print('Done, files in', out)
