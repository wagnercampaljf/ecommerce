<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'pecaagora@gmail.com',
    'emailToken' => sha1(date('h') . '1V*9IFaODM^CD{kZr"_/|9{.8TCxnZ|B'),
    'dominio' => 'www.pecaagora.com',
    'user.passwordResetTokenExpire' => 3600,
    'isJuridica' => function () {
        $juridica = false;
        if (!Yii::$app->user->isGuest) {
            $juridica = Yii::$app->user->getIdentity()->empresa->juridica;
        }
        return $juridica;
    },
    'getCepComprador' => function ($format = false) {
        $cep = '';
        if (!Yii::$app->user->isGuest && ($endereco = Yii::$app->user->getIdentity()->empresa->enderecosEmpresa[0])) {
            $cep = $endereco->cep;
        }
        if (isset(Yii::$app->session["cep"])) {
            $cep = Yii::$app->session["cep"];
        }
        if ($format && !is_null($cep)) {
            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5);
        }
        return $cep;
    }
];
