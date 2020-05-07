<?php

use api\components\JwtValidationData;
use sizeg\jwt\Jwt;
use yii\helpers\ArrayHelper;
use yii\rest\UrlRule;
use yii\web\Response;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'modules' => [],
    'components' => [
        'jwt' => [
            'class' => Jwt::class,
            'key' => 'supperpuppersecretfraza',
            // You have to configure ValidationData informing all claims you want to validate the token.
            'jwtValidationData' => JwtValidationData::class,
        ],
        'request' => [
            'csrfParam' => '_csrf-api',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    $response->data = [
                        'success' => $response->isSuccessful,
//                        'response' => get_object_vars($response),
                        'data' => $response->data,
                        'statusCode' => $response->statusCode,
                        'statusText' => ArrayHelper::getValue($response,'statusText'),
                    ];
                    //$response->statusCode = 200;
                }
            },
            'formatters' => [
                Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // используем "pretty" в режиме отладки
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
//            'enableAutoLogin' => true,
            'enableSession' => false,
           // 'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'rest-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace', 'error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'cache' //Включаем кеширование
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',  // Подключаем файловое кэширование данных
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => UrlRule::class,
                    'controller' => 'user',
                    'pluralize' => true,
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => 'login',
                    'extraPatterns' => [
                        'POST signup' => 'signup',
                        'POST login' => 'login',
                        'GET logout' => 'logout',
                    ],
                    'pluralize' => false,
                ],
            ],
        ],
    ],
    'params' => $params,
];
