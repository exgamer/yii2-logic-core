Пример настройик ngnx для того чтобы админка была доступна по /admin

!!! В этом случае все редиректы и создание url 
во вреимворке нужно обязательно вызывать как массив ['site/index']


```
server {
    server_name  1xbet;
    root   /var/www/1xbet;

    charset utf-8;
    index  index.php;

    location / {
        root   /var/www/1xbet/frontend/web;
        try_files  $uri /frontend/web/index.php?$args;
        access_log  /var/www/1xbet/frontend/runtime/access.log;
        error_log   /var/www/1xbet/frontend/runtime/error.log;
    }

    location ~* \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }

    location ~* \.(css|js|jpg|jpeg|png|gif|bmp|ico|mov|swf|pdf|zip|rar)$ {
        access_log  off;
        log_not_found  off;
        try_files  $uri /frontend/web$uri =404;
    }

    location /admin {
        alias /var/www/1xbet/backend/web;
        try_files  $uri /backend/web/index.php?$args;

        location = /admin/ {
           return  301 /admin;
        }

        location ~* ^/admin/(.+\.php)$ {
            try_files  $uri /backend/web/$1?$args;
        }

        # avoid processing of calls to non-existing static files by Yii (uncomment if necessary)
        location ~* ^/admin/(.+\.(css|js|jpg|jpeg|png|gif|bmp|ico|mov|swf|pdf|zip|rar))$ {
            try_files $uri /backend/web/$1?$args;
        }
    }
}


```



Добавить   

  
    'homeUrl' => '/admin/',
    
и
    
        'request' => [
            'baseUrl'=>'/admin',
            'csrfParam' => '_csrf-backend',
        ],  

```php
<?php

use \kamaelkz\yii2admin\v1\Yii2Admin;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$config =  [
    'id' => 'app-backend',
    'homeUrl' => '/admin/',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => [
        'log'
    ],
    'modules' => require(__DIR__. '/modules.php'),
    'components' => [
        'request' => [
            'baseUrl'=>'/admin',
            'csrfParam' => '_csrf-backend',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'app-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning'
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/routes.php',
        ],
    ],
    'params' => $params,
];

return yii\helpers\ArrayHelper::merge(
    Yii2Admin::getConfiguration(Yii2Admin::WEB),
    $config
);

```
