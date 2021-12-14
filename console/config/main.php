<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'language' => 'pt-BR',
    'sourceLanguage' => 'pt_BR',
    'timeZone' => 'America/Sao_Paulo',
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => [
                        'error_yii',
                        'mercado_livre_closed',
                        'mercado_livre_create',
                        'mercado_livre_deleted',
                        'mercado_livre_relist',
                        'mercado_livre_update',
                    ],
                    'logFile' => '@app/runtime/logs/mercadolivre/request.log',
                    'maxLogFiles' => 5,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => [
                        'error_yii',
                        'mercado_livre_closed',
                        'mercado_livre_create',
                        'mercado_livre_deleted',
                        'mercado_livre_relist',
                        'mercado_livre_update',
                    ],
                    'logFile' => '@app/runtime/logs/mercadolivre/request_error.log',
                    'maxLogFiles' => 5,
                ],
            ],
        ],
        'urlManager' => [
            'baseUrl' => '/lojista/web',
            'hostInfo' => 'http://pecaagora.com',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => $params,
];
