<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Este Ã© o model para a tabela "respostas".
 *
 * @property integer $id
 * @property integer $produto_id
 * @property integer $opcao_id
 * @property string $data_resposta
 *
 * @property Opcoes $opcao
 *
 * @author Otávio 15/12/2016
 */
class Respostas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Otávio 15/12/2016
     */
    public static function tableName()
    {
        return 'respostas';
    }

    /**
     * @inheritdoc
     * @author Otávio 15/12/2016
     */
    public function rules()
    {
        return [
            [['opcao_id'], 'required'],
            [['id', 'produto_id', 'opcao_id'], 'integer'],
            [['data_resposta'], 'safe'],
            [['opcao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Opcoes::className(), 'targetAttribute' => ['opcao_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Otávio 15/12/2016
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produto_id' => 'Produto ID',
            'opcao_id' => 'Opcao ID',
            'data_resposta' => 'Data Resposta',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otávio 15/12/2016
     */
    public function getOpcao()
    {
        return $this->hasOne(Opcoes::className(), ['id' => 'opcao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otávio 15/12/2016
     */
    public function getOpcoes()
    {
        return ArrayHelper::map(Opcoes::find()->all(), 'id', 'nome');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otávio 15/12/2016
     */
    public static function find()
    {
        return new RespostasQuery(get_called_class());
    }
}

/**
 * Classe para contenÃ§Ã£o de escopos da Respostas, utilizada nas operaÃ§Ãµes find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Otávio 15/12/2016
 */
class RespostasQuery extends \yii\db\ActiveQuery
{
    /**
     * OrdenaÃ§Ã£o AlfabÃ©tica
     * @return \yii\db\ActiveQuery
     * @author Otávio 15/12/2016
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['respostas.nome' => $sort_type]);
    }
}
