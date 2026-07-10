$ErrorActionPreference='Stop'
$out='D:\Guised Up\backend\laravel\portable_php'
New-Item -Path $out -ItemType Directory -Force | Out-Null
Set-Location $out
$urls=@(
  'https://windows.php.net/downloads/releases/archives/php-8.2.34-Win32-vs16-x64.zip',
  'https://windows.php.net/downloads/releases/archives/php-8.2.33-Win32-vs16-x64.zip',
  'https://windows.php.net/downloads/releases/archives/php-8.2.32-Win32-vs16-x64.zip'
)
$downloaded=$false
foreach($u in $urls){
  try{
    Write-Host "Trying $u"
    Invoke-WebRequest -Uri $u -OutFile 'php.zip' -UseBasicParsing -ErrorAction Stop
    $downloaded=$true
    break
  } catch {
    Write-Host "Failed $u: $_"
  }
}
if(-not $downloaded){
  Write-Host 'All PHP download attempts failed'
  exit 2
}
Expand-Archive -Path 'php.zip' -DestinationPath . -Force
Remove-Item 'php.zip'
Invoke-WebRequest -Uri 'https://getcomposer.org/composer.phar' -OutFile 'composer.phar' -UseBasicParsing
Write-Host 'Done' 
