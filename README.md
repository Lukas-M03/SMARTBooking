**Install PHP**

You can install PHP 8.4 (thread-safe zip file version) which can be downloaded from

https://windows.php.net/download/#php-8.4-ts-vs16-x64 or
https://windows.php.net/download/#php-8.4-ts-vs17-x64

Once downloaded, right click the zip file, select 'Extract all' option and unzip the file in a
folder named c:\php , then add this folder to your user PATH variable. [NOTE: To update to
a later version of PHP, simply download the new version and extract the new zip file
contents to c:\php). Open a new terminal window and verify that its installed correctly by
typing:

c:> php -v

If you get an error it may be that the PATH variable has not been set up correctly- 

Go to 'Edit environment variables for your account' through the windows search
Go onto 'Path' and press 'edit', click 'new' and add this path %USERPROFILE%\AppData\Roaming\Composer\vendor\bin 

We now need to create a configuration file by opening the terminal and in the c:\php folder execute following command-
C:\php> copy .\php.ini-development php.ini

Next we need to enable some php extensions required by Laravel. Open the php.ini file in
notepad and update the extensions section by removing the leading ';' comment character
from start of lines shown below. This will enable a range of both required and useful
extensions for Laravel development-

Laravel Extensions enabled
extension=curl
extension=fileinfo
extension=gd
extension=gettext
extension=intl
extension=mbstring
extension=exif
extension=openssl
extension=pdo_mysql
extension=pdo_pgsql
extension=pdo_sqlite
extension=zip
zend_extension=opcache

Save the file changes and close Notepad.

**Install Composer**

Composer is the PHP Dependency Manager and is required to use Laravel. To install
composer, download and execute the setup file found at following url

https://getcomposer.org/Composer-Setup.exe.

Again to check composer is installed correctly open a terminal and type-

c:> composer about

**Install PHP/Laravel Extensions **

To simplify this create a Powershell script (this only works in Windows Powershell) using code 
below to create a PHP profile in VSCode and install the necessary extensions).

# Script for batch installing Visual Studio Code extensions
# Specify extensions to be checked & installed by modifying $extensions
$profile = "Laravel"
$extensions = @("formulahendry.auto-close-tag",
  "formulahendry.auto-rename-tag",
  "laravel.vscode-laravel",
  "bmewburn.vscode-intelephense-client",
  "mehedidracula.php-namespace-resolver",
  "liamhammett.temphpest",
  "bradlc.vscode-tailwindcss",
  "shufo.vscode-blade-formatter",
  "onecentlin.laravel-blade",
  "pkief.material-icon-theme")
$cmd = "code --list-extensions"
Invoke-Expression $cmd -OutVariable output | Out-Null
$installed = $output -split "\s"
code --profile $profile
$confirm = Read-Host "Enter y to confirm installation:"
if ($confirm -eq "Y" -or $confirm -eq "y") {
  foreach ($ext in $extensions) {
  if ($installed.Contains($ext)) {
  Write-Host $ext "already installed." -ForegroundColor Gray
  } else {
  Write-Host "Installing" $ext "..." -ForegroundColor White
  code --profile $profile --install-extension $ext --force
  }
  }
  Write-Host "Extensions installed.."
} else {
  Write-Host "Extensions not installed.."
}

Copy the code above to a file named extvscode.ps1 in your Documents folder and open a 
powershell terminal in this folder and execute the script.

c:> .\com621.ps1

The script will open vscode then wait for you to press 'y' in the terminal to confirm
installation. Once completed you can open your project in vs-code, then select the PHP
profile to have full PHP/Laravel support.

**First run on a new device (project bootstrap)**

Your local environment file (`.env`) is machine-specific and is not committed to Git.
The repository only tracks `.env.example` as a template.

From the project root, run:

```powershell
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run dev
```

Then update values in `.env` for your local setup (database credentials, Microsoft app
settings, mail settings, etc.).
