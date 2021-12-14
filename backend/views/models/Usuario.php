<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;
use yiibr\brvalidator\CpfValidator;

/**
 * Este é o model para a tabela "usuario".
 *
 * @property integer $id
 * @property string $username
 * @property string $nome
 * @property string $cpf
 * @property string $email
 * @property integer $filial_id
 * @property integer $nivel_acesso_id
 * @property string $password
 * @property string $dt_criacao
 * @property boolean $ativo
 * @property string $dt_ultima_mudanca_senha
 * @property string $auth_key
 * @property string $password_reset_token
 *
 * @property Filial $filial
 * @property NivelAcesso $nivelAcesso
 *
 * @author Vinicius Schettino 19/03/2015
 */
class Usuario extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = false;
    const STATUS_ACTIVE = true;

    public $repeat_password;


    /**
     * @inheritdoc
     * @author Vinicius Schettino 19/03/2015
     */
    public static function tableName()
    {
        return 'usuario';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 19/03/2015
     */
    public function rules()
    {
        return [
            [['username', 'nome', 'cpf', 'email', 'nivel_acesso_id', 'dt_criacao'], 'required'],
            [['filial_id', 'nivel_acesso_id'], 'integer'],
            [['dt_criacao', 'dt_ultima_mudanca_senha'], 'safe'],
            [['ativo'], 'boolean'],
            [['cargo'], 'string', 'max' => 50],
            //[['username'], 'string', 'max' => 30],
            [['username', 'email'], 'unique'],
            [['nome', 'email', 'password_reset_token'], 'string', 'max' => 150],
            ['cpf', CpfValidator::className()],
            [['password'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 123],
            ['email', 'email'],
            [
                ['repeat_password'],
                'compare',
                'compareAttribute' => 'password',
                'message' => 'Os campos Senha e Repetir Senha devem ser iguais'
            ]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['noRepeatPassword'] = $this->getAttributes();

        return $scenarios;
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 19/03/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'nome' => 'Nome',
            'cpf' => 'CPF',
            'email' => 'Email',
            'filial_id' => 'Filial ID',
            'nivel_acesso_id' => 'Nivel Acesso ID',
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
     * @author Vinicius Schettino 19/03/2015
     */
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 19/03/2015
     */
    public function getNivelAcesso()
    {
        return $this->hasOne(NivelAcesso::className(), ['id' => 'nivel_acesso_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 19/03/2015
     */
    public static function find()
    {
        return new UsuarioQuery(get_called_class());
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
        return static::findOne(['email' => $username, 'ativo' => true,]);
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
 * Classe para contenção de escopos da Usuario, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 19/03/2015
 */
class UsuarioQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 19/03/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['usuario.nome' => $sort_type]);
    }
}
