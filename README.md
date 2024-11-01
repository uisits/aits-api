# UIS ITS AITS API

This package is a collection of AITS api's. It provides and easy way to integrate and use AITS api endpoints in Laravel applications.

## Installation
- Install the package by running
```bash
composer require uisits/aits-api
```
- Publish the config file:
```bash
php artisan vendor:publish --tag=aits-api
```
- Lastly, configure the environment variables.
```shell
AITS_BASE_URL=https://webservices-test.admin.uillinois.edu/studentWS/data/edu.uis.its.apps/
AITS_PERSON_BASE_URL=https://webservices-test.admin.uillinois.edu/personWS/data/edu.uis.its.apps/

AITS_AZURE_PORTAL_KEY='xxxxxxxxxxx'
AITS_AZURE_BASE_URL=https://api.apps.uillinois.edu/
```

If you want to setup proxy server, add the details of proxy server to your `.env`
```shell
AITS_PROXY_HOST='xx.xx.xx.xx'
AITS_PROXY_PORT='xxxx'
AITS_PROXY_USERNAME='xxxxx'
AITS_PROXY_PASSWORD='xxxxxxxxxxxxxxx'
```

Please refer to [GitHub Wiki](https://github.com/uisits/aits-api/wiki) page for api documentation.
