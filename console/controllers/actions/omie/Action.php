<?php

namespace console\controllers\actions\omie;


class Action extends \yii\base\Action
{
    //Login de produção peça agora
    const APP_KEY_OMIE              = '468080198586';
    const APP_SECRET_OMIE           = '7b3fb2b3bae35eca3b051b825b6d9f43';
    //Login de teste criado apenas para teste
    //const APP_KEY_OMIE              = '531935801397';
    //const APP_SECRET_OMIE           = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    //Login disponibilizado pelo Omie para teste internor, utilizando seus exemplos
    //const APP_KEY_OMIE              = '1560731700';
    //const APP_SECRET_OMIE           = '226dcf372489bb45ceede61bfd98f0f1';

    const APP_ID                    = '3029992417140266';
    const SECRET_KEY                = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    const REDIRECT_URI              = 'https://www.pecaagora.com/lojista/web/mercado-livre/callback';
    const MELI_TOKEN_SESSION_KEY    = 'meliAccessToken';
    const MELI_EXPIRES_SESSION_KEY  = 'meliExpiresIn';

    const APP_KEY_OMIE_SP              = '468080198586';
    const APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
    const APP_KEY_OMIE_MG              = '469728530271';
    const APP_SECRET_OMIE_MG           = '6b63421c9bb3a124e012a6bb75ef4ace';
    const APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
    const APP_SECRET_OMIE_CONTA_DUPLICADA   = '78ba33370fac6178da52d42240591291';
}
