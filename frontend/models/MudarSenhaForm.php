<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 11/12/2015
 * Time: 13:51
 */

namespace frontend\models;

use common\models\Comprador;
use common\models\Usuario;


class MudarSenhaForm extends \common\models\MudarSenhaForm
{

    /**
     * Encontra o Usuário pelo [[user_id]]
     *
     * @return Usuario|null
     */
    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = Comprador::findOne(['id' => $this->user_id]);
        }

        return $this->_user;
    }
}