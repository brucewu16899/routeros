RouterOS for Laravel
====================

Packages are the primary way of adding functionality to Laravel.

This is a wrapper of http://pear2.github.io/Net_RouterOS/

## installing

Laravel 4.*:

Open composer.json
  
Add something like this to the repositories section:
  
```json
  "repositories": [
      {
        "type":"git",
        "url":"https://github.com/Ceesco53/routeros"
      }
  ]
```
    
Add this to the require section:

```json
    "ceesco53/routeros": "dev-master"
```
