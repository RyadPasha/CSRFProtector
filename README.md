CSRF Protector
==========================
This package can be used to prevent CSRF request security attacks, it's a standalone php library for csrf mitigation in web applications. Easy to integrate in any php web app. 

Add to your project using packagist
==========
 Add a `composer.json` file to your project directory
 ```json
 {
    "require": {
        "ryadpasha/csrfprotector": "dev-master"
    }
}
```
Then open terminal (or command prompt), move to project directory and run
```shell
composer install
```
OR
```
php composer.phar install
```
This will add CSRFP (library will be downloaded at ./vendor/RyadPasha/CSRFProtector) to your project directory. View [packagist.org](https://packagist.org/) for more help with composer!

Configuration
==========
For composer installations: Copy the folder 'vendor' into your your server.
For non-composer installations: Download and unzip all the contents in a folder in your server.
See detailed information below ..

How to use
==========
Let's suppose is you installed it with composer and you have the folder 'vendor' in the root folder at in your server.
At the begin of your main script, add this code:

```php
<?php
include_once __DIR__ .'/vendor/ryadpasha/csrfprotector/CSRFProtector.php';

//Initialise CSRFProtector library
$jsPath = "CSRFProtector"; // path where is native.history.js
$csrf = new CSRFProtector($jsPath);
$csrf->run();
```
Simply include the library and call the `init()` function!

That is all! Anyway it's more powerfull than what might seem.

### Contribute

* Fork the repo
* Create your branch
* Commit your changes
* Create a pull request

### Note
This version (`master`) requires the clients to have Javascript enabled.

## Discussion
For any queries contact me at: **me@ryadpasha.com**
