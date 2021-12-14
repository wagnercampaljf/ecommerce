<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 06/01/2016
 * Time: 13:56
 */

namespace lojista\models;


use common\models\Usuario;
use yii\web\IdentityInterface;

class MudarSenhaForm extends \common\models\MudarSenhaForm
{

    /**
     * Encontra o Usuário pelo [[user_id]]
     *
     * @return IdentityInterface|null
     */
    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = Usuario::findOne(['id' => $this->user_id]);
        }

        return $this->_user;
    }
}