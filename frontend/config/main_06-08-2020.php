<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'homeUrl' => '',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'assetManager' => [
            'linkAssets' => false,
        ],
        'request' => [
            'baseUrl' => '/',
        ],
        'user' => [
            'identityClass' => 'common\models\Comprador',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_compradorUser', // unique for backend
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'baseUrl' => '/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'p/<id:\d+>' 				=> 'product/view',
                'p/<id:\d+>/<slug>' 			=> 'product/view',
                'p/<id:\d+>/' 				=> 'product/view',
                'auto/<categoria>' 			=> 'categorias/auto',
                'auto/<categoria>/<subcategoria>' 	=> 'categorias/subcategoria',
                'auto' 					=> 'categorias/index',
                'site/<id:\w+>/img.jpg' 		=> 'site/img',
                'site/<id:\w+>/img.png' 		=> 'site/img',
                'veiculos/<tipo>' 			=> 'veiculos/marcas',
                'veiculos/<tipo>/<marca>' 		=> 'veiculos/modelos',
                'veiculos' 				=> 'veiculos/index',
		'pedidoml' 				=> 'callbackml/pedidoml',
		'melhorenvios'                  	=> 'callbackmelhorenvios/melhorenvios',
		'google6cbbae050be36cee.html'      	=> 'google/',
		'pedidomlduplicada'                 	=> 'callbackmlduplicada/pedidoml',
		'pedidomlteste'                         => 'callbackmlteste/pedidoml',
            ]
        ],
    ],
    'params' => $params,
];
