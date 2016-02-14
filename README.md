# Mf
PHP micro-framework

This toolset is designed to support my current and future PHP projects. You won't find anything here that haven't been
invented elsewhere yet. But this one is mine. Gollum!

## Installation

Just add ```"calmacil/mf" : "0.3.*"``` to your composer.json. You should also add this directive to your ```scripts```
section: ```"post-install-cmd" : "Mf\\InstallScript::postInstall"``` in order to auto-generate the minimal project tree.
Then, create an Apache virtualhost pointing on you *PROJECT_ROOT/web* directory and set up URL rewriting.

```Apache
<VirtualHost *:80>
    ServerName: myproject.dev
    DocumentRoot: /home/calmacil/projects/myproject/web
    
    <Location />
        AllowOverride none
        Require all granted
        
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule .* index.php [QSA,L]
     </Location>
</VirtualHost>
```

## Configuration

The base configuration is contained in config/settings_{env}.json file. The environment name is your choice. Following
directives are mandatory:

```javascript
{
  "project_name": "myproject",
  "debug": true,
  "log": {
    "logfile": "logs/app.log",
    "loglevel": "debug"
  },
  "paths": {
    "cache_dir": "cache",
    "twig_cache": "cache/twig",
    "routing_file": "routing"
  },
}
```

The config/db.json file is also necessary, it contains the database connection settings. You can set up as many
databases as you want. To connect to a database, you have to instantiate PdoProvider this way:

```php
$dbh = PdoProvider::getConnector($connection_name);
```

Here is an example of db.json:

```javascript
{
  "devel": {
    "driver": "mysql",
    "host": "localhost",
    "port": "3306",
    "dbname": "myproject",
    "user": "root"
    "password": "my_pass"
  }
}
```