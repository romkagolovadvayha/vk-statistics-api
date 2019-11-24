<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'components' => [
        'request' => [
            'cookieValidationKey' => 'Px1lmFQQJ4QlqYOsPneISnNwldQAwhDt',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
//        'cache' => [
//            'class' => 'yii\caching\FileCache',
//        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'object',
                    'prefix' => 'api',
                    'tokens' => [
                        '{id}' => '<object_id:\\w+>',
                        '{type}' => '<object_type:\\w+>'
                    ],
                    'extraPatterns' => [
                        'GET add' => 'add',
                        'DELETE {type}/{id}/delete' => 'delete',
                        'GET index' => 'index',
                        'GET {type}/{id}' => 'get',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user',
                    'prefix' => 'api',
                    'extraPatterns' => [
                        'GET auth' => 'auth',
                        'GET bay' => 'bay',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'history',
                    'prefix' => 'api',
                    'tokens' => [
                        '{id}' => '<object_id:\\w+>',
                        '{type}' => '<object_type:\\w+>'
                    ],
                    'extraPatterns' => [
                        'GET,POST {type}/{id}/add' => 'add',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];

return $config;
