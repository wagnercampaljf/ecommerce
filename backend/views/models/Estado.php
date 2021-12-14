<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "estado".
 *
 * @property integer $id
 * @property string $sigla
 * @property string $nome
 *
 * @property Cidade[] $cidades
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Estado extends \yii\db\ActiveRecord implements SearchModel
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'estado';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['id', 'sigla'], 'required'],
            [['id'], 'integer'],
            [['sigla'], 'string', 'max' => 3],
            [['nome'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sigla' => 'Sigla',
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getCidades()
    {
        return $this->hasMany(Cidade::className(), ['estado_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new EstadoQuery(get_called_class());
    }

    public function getLabelSearch()
    {
        return $this->nome;
    }
}

/**
 * Classe para contenção de escopos da Estado, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class EstadoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['estado.nome' => $sort_type]);
    }
}
