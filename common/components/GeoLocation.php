<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 06/10/2015
 * Time: 17:52
 */

namespace common\components;

use Yii;
use yii\base\Component;

class GeoLocation extends Component
{
    public function getCidade($q = null)
    {
        if (Yii::$app->session->get('locale', false)) {
            return Yii::$app->session->get('locale');
        }

        if ((Yii::$app->user->isGuest || !is_null($q)) && Yii::$app->session->get('locale', true)) {
            $cidade_id = IPAPI::query($q);
            Yii::$app->session->set('locale', $cidade_id);

            return $cidade_id;
        }

        if ((!Yii::$app->user->isGuest) && ($endereco = Yii::$app->user->getIdentity()->empresa->enderecoEmpresa) && isset($empresa->cidade)) {
            Yii::$app->session->set('locale', $endereco->cidade->id);

            return $endereco->cidade->id;
        }

        return null;
    }
}