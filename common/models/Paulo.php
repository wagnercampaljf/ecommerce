<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "paulo".
 *
 * @property integer $id
 * @property string $nome
 *
 * @author Unknown 01/11/2019
 */
class Paulo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 01/11/2019
     */
    public static function tableName()
    {
        return 'paulo';
    }

    /**
     * @inheritdoc
     * @author Unknown 01/11/2019
     */
    public function rules()
    {
        return [
            [['nome'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 01/11/2019
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
     * @author Unknown 01/11/2019
    */
    public static function find()
    {
        return new PauloQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Paulo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 01/11/2019
*/
class PauloQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 01/11/2019
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['paulo.nome' => $sort_type]);
    }
}
