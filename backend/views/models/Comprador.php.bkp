<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yiibr\brvalidator\CpfValidator;


/**
 * Este é o model para a tabela "comprador".
 *
 * @property integer $id
 * @property string $nome
 * @property integer $empresa_id
 * @property string $cpf
 * @property string $username
 * @property string $password
 * @property string $repeat_password
 * @property string $dt_criacao
 * @property boolean $ativo
 * @property string $dt_ultima_mudanca_senha
 * @property string $email
 * @property string $cargo
 * @property integer $nivel_acesso_id
 * @property string $token_moip
 * @property string $password_reset_token
 *
 * @property Mensagem[] $mensagems
 * @property Pedido[] $pedidos
 * @property Empresa $empresa
 * @property NivelAcesso $nivelAcesso
 * @property Carrinho[] $carrinhos
 * @property Visita[] $visitas
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Comprador extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = false;
    const STATUS_ACTIVE = true;

    public $repeat_password;

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'comprador';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['nome', 'cpf', 'dt_criacao', 'password', 'email'], 'required'],
            [['repeat_password'], 'required', 'on' => 'create', 'except' => 'update'],
            [['empresa_id', 'nivel_acesso_id'], 'integer'],
            [['dt_criacao', 'dt_ultima_mudanca_senha', 'password_reset_token'], 'safe'],
            [['ativo'], 'boolean'],
            ['email', 'email'],
            ['email', 'unique'],
            [['nome'], 'string', 'max' => 150],
            [['cpf'], 'string', 'max' => 14, 'min' => 11],
            ['cpf', CpfValidator::className()],
            [['username'], 'string', 'max' => 30],
            [['password', 'email', 'token_moip'], 'string', 'max' => 255],
            [['password'], 'string', 'min' => 6],
            [['cargo'], 'string', 'max' => 50],
            [['repeat_password'], 'compare', 'compareAttribute' => 'password'],
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
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'empresa_id' => 'Empresa ID',
            'cpf' => 'Cpf',
            'username' => 'Nome de usuário',
            'password' => 'Senha',
            'repeat_password' => 'Repetir senha',
            'dt_criacao' => 'Data de Criação',
            'ativo' => 'Ativo',
            'dt_ultima_mudanca_senha' => 'Dt Ultima Mudanca Senha',
            'email' => 'Email',
            'cargo' => 'Cargo',
            'nivel_acesso_id' => 'Nivel Acesso ID',
            'token_moip' => 'Token MOIP',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getMensagemn()
    {
        return $this->hasMany(Mensagem::className(), ['comprador_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getPedidos()
    {
        return $this->hasMany(Pedido::className(), ['comprador_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getEmpresa()
    {
        return $this->hasOne(Empresa::className(), ['id' => 'empresa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getNivelAcesso()
    {
        return $this->hasOne(NivelAcesso::className(), ['id' => 'nivel_acesso_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getCarrinhos()
    {
        return $this->hasMany(Carrinho::className(), ['comprador_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getVisitas()
    {
        return $this->hasMany(Visita::className(), ['comprador_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new CompradorQuery(get_called_class());
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
 * Classe para contenção de escopos da Comprador, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class CompradorQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['comprador.nome' => $sort_type]);
    }
}
