<?php

namespace backend\models;

use Yii;

/**
 * Este é o model para a tabela "administrador".
 *
 * @property integer $id
 * @property string $username
 * @property string $nome
 * @property string $cpf
 * @property string $email
 * @property string $password
 * @property string $dt_criacao
 * @property boolean $ativo
 * @property string $dt_ultima_mudanca_senha
 * @property string $auth_key
 * @property string $password_reset_token
 *
 * @author Unknown 08/03/2021
 */
class Administrador extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 08/03/2021
     */
    public static function tableName()
    {
        return 'administrador';
    }

    /**
     * @inheritdoc
     * @author Unknown 08/03/2021
     */
    public function rules()
    {
        return [
            [['username', 'nome', 'email', 'dt_criacao'], 'required'],
            [['dt_criacao', 'dt_ultima_mudanca_senha'], 'safe'],
            [['ativo'], 'boolean'],
            [['username', 'nome', 'email', 'password_reset_token'], 'string', 'max' => 150],
            [['cpf'], 'string', 'max' => 11],
            [['password'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 123]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 08/03/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'nome' => 'Nome',
            'cpf' => 'Cpf',
            'email' => 'Email',
            'password' => 'Password',
            'dt_criacao' => 'Dt Criacao',
            'ativo' => 'Ativo',
            'dt_ultima_mudanca_senha' => 'Dt Ultima Mudanca Senha',
            'auth_key' => 'Auth Key',
            'password_reset_token' => 'Password Reset Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/03/2021
    */
    public static function find()
    {
        return new AdministradorQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Administrador, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 08/03/2021
*/
class AdministradorQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/03/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['administrador.nome' => $sort_type]);
    }
}
