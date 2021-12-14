<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-lojista',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'lojista\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'gridview' => [
            'class' => 'kartik\grid\Module'
        ],
//        'debug' => [
//            'class' => 'yii\debug\Module',
//            'allowedIPs' => ['127.0.0.1']
//        ],
    ],
    'components' => [
        'session' => [
            'name' => 'PHPBACKSESSID',
            'savePath' => sys_get_temp_dir(),
        ],
        'formatter' => [
            'class' => 'common\i18n\Formatter',
            'booleanFormat' => ['Não', 'Sim'],
            'decimalSeparator' => ',',
            'thousandSeparator' => '.'
        ],
        'user' => [
            'identityClass' => 'common\models\Usuario',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
            'identityCookie' => [
                'name' => '_lojistaUser', // unique for backend
            ]
        ],
        /*'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],*/
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'baseUrl' => '/lojista/web',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => $params,
];
