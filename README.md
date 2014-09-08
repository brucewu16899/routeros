Packages are the primary way of adding functionality to Laravel.

routeros
========

Conversion of http://pear2.github.io/Net_RouterOS/ to laravel

installing
==========
Laravel 4.*:

  Open composer.json
  
  Add something like this to the repositories section:
    "repositories": [
        {
            "type":"git",
            "url":"https://github.com/Ceesco53/routeros"
        }
    ]
    
  Add this to the require section:
    "ceesco53/routeros": "dev-master"
