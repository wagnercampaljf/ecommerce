<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * Este é o model para a tabela "administrador".
 *
 * @property integer $id
 * @property string $nome
 * @property string $cpf
 * @property string $email
 * @property string $password
 * @property string $dt_criacao
 * @property boolean $ativo
 * @property string $dt_ultima_mudanca_senha
 * @property string $auth_key
 * @property string $password_reset_token
 * @property string $codigo_omie_sp
 * @property string $codigo_omie_mg
 * @property string $codigo_omie_filial
 * @property integer $filial_id
 *
 * @author smart_i9 03/07/2015
 */
class Administrador extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = false;
    const STATUS_ACTIVE = true;

    public $repeat_password;
    public $username;

    /**
     * @inheritdoc
     * @author smart_i9 03/07/2015
     */
    public static function tableName()
    {
        return 'administrador';
    }

    /**
     * @inheritdoc
     * @author smart_i9 03/07/2015
     */
    public function rules()
    {
        return [
            [['nome', 'cpf', 'email', 'dt_criacao'], 'required'],
            [['dt_criacao', 'dt_ultima_mudanca_senha'], 'safe'],
            [['ativo'], 'boolean'],
            [['nome', 'email', 'password_reset_token'], 'string', 'max' => 150],
            [['codigo_omie_sp', 'codigo_omie_mg', 'codigo_omie_filial'], 'string', 'max' => 15],
            [['cpf'], 'string', 'max' => 11],
            [['filial_id'], 'integer'],
            [['password'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 123],
            [
                ['repeat_password'],
                'compare',
                'compareAttribute' => 'password',
                'message' => 'Os campos Senha e Repetir Senha devem ser iguais'
            ]
        ];
    }

    /**
     * @inheritdoc
     * @author smart_i9 03/07/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'cpf' => 'Cpf',
            'email' => 'Email',
            'password' => 'Password',
            'dt_criacao' => 'Dt Criacao',
            'ativo' => 'Ativo',
            'dt_ultima_mudanca_senha' => 'Dt Ultima Mudanca Senha',
            'auth_key' => 'Auth Key',
            'password_reset_token' => 'Password Reset Token',
            'codigo_omie_sp' => 'codigo_omie_sp',
            'codigo_omie_mg' => 'codigo_omie_mg',
            'codigo_omie_filial' => 'codigo_omie_filial',
            'filial_id' => 'Filial ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author smart_i9 03/07/2015
     */
    public static function find()
    {
        return new AdministradorQuery(get_called_class());
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'ativo' => true]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'ativo' => true,]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(
            [
                'password_reset_token' => $token,
                'ativo' => true,
            ]
        );
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {

        return (Yii::$app->security->validatePassword($password, $this->password));
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}

/**
 * Classe para contenção de escopos da Administrador, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author smart_i9 03/07/2015
 */
class AdministradorQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author smart_i9 03/07/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['administrador.nome' => $sort_type]);
    }
}
