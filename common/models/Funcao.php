<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "funcao".
 *
 * @property integer $id
 * @property string $nome
 * @property string $funcao_nome
 * @property string $caminho
 *
 * @property Processamento[] $processamentos
 *
 * @author Unknown 08/07/2021
 */
class Funcao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public static function tableName()
    {
        return 'funcao';
    }

    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public function rules()
    {
        return [
            [['nome', 'funcao_nome'], 'required'],
            [['nome', 'funcao_nome', 'caminho'], 'string']
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'funcao_nome' => 'Funcao Nome',
            'caminho' => 'Caminho',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */
    public function getProcessamentos()
    {
        return $this->hasMany(Processamento::className(), ['funcao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */
    public static function find()
    {
        return new FuncaoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Funcao, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 08/07/2021
*/
class FuncaoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['funcao.nome' => $sort_type]);
    }
}
