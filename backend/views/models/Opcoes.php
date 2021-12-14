<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "opcoes".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property Respostas[] $respostas
 *
 * @author Ot�vio 15/12/2016
 */
class Opcoes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Ot�vio 15/12/2016
     */
    public static function tableName()
    {
        return 'opcoes';
    }

    /**
     * @inheritdoc
     * @author Ot�vio 15/12/2016
     */
    public function rules()
    {
        return [
            [['id', 'nome'], 'required'],
            [['id'], 'integer'],
            [['nome'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     * @author Ot�vio 15/12/2016
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 15/12/2016
     */
    public function getRespostas()
    {
        return $this->hasMany(Respostas::className(), ['opcao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 15/12/2016
     */
    public static function find()
    {
        return new OpcoesQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Opcoes, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Ot�vio 15/12/2016
 */
class OpcoesQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 15/12/2016
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['opcoes.nome' => $sort_type]);
    }
}
