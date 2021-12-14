<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
 * Time: 18:54
 */

namespace console\controllers\actions\mercadolivre;


class Action extends \yii\base\Action
{
//    const APP_ID = '7346648451576903';
//    const SECRET_KEY = 'ps9zV3qhUfFRZiwdBK0kPnmQFyf7PXWp';
//    const REDIRECT_URI = 'http://localhost/pecaagora/lojista/web/mercado-livre/callback';
    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    const REDIRECT_URI = 'https://www.pecaagora.com/lojista/web/mercado-livre/callback';
    const MELI_TOKEN_SESSION_KEY = 'meliAccessToken';
    const MELI_EXPIRES_SESSION_KEY = 'meliExpiresIn';

}